<?php

require '../database/DataBaseConexion.php';

class MembresiaAdo
{

    function __construct()
    {
    }

    public static function listarMembresias($opcion, $buscar, $tipo, $x, $y)
    {

        try {
            $array = array();

            $membresia = Database::getInstance()->getDb()->prepare("SELECT 
            m.idMembresia,
            c.dni,
            c.apellidos,
            c.nombres,
            p.nombre AS 'nombrePlan',  
            v.serie, 
            v.numeracion, 
            v.estado AS 'estadoventa',
            m.tipoMembresia,
            m.fechaInicio, 
            m.fechaFin, 
            CASE 
            /*WHEN CAST(PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM NOW()), EXTRACT(YEAR_MONTH FROM m.fechaFin)) AS INT) < 0 THEN 1*/
            WHEN CAST(DATEDIFF(m.fechaFin,CURDATE()) AS INT) > 10 THEN 1
            WHEN CAST(DATEDIFF(m.fechaFin,CURDATE()) AS INT) >=0 AND CAST(DATEDIFF(m.fechaFin,CURDATE()) AS INT) <=10 THEN 2
            ELSE 0 END AS 'membresia', 
            m.cantidad, 
            m.precio, 
            SUM(m.cantidad*m.precio) AS 'total' 
            FROM membresiatb AS m
            INNER JOIN ventatb AS v ON v.idVenta = m.idVenta
            INNER JOIN plantb AS p ON p.idPlan = m.idPlan
            INNER JOIN clientetb AS c ON c.idCliente  = m.idCliente
            WHERE 
            ? = 0
            OR
            ? = 1 AND c.dni LIKE CONCAT(?,'%')
            OR
            ? = 1 AND c.apellidos LIKE CONCAT(?,'%')
            OR
            ? = 1 AND c.nombres LIKE CONCAT(?,'%')
            OR
            ? = 1 AND CONCAT(c.apellidos,' ', c.nombres) LIKE CONCAT(?,'%')
            OR
            ? = 1 AND v.serie = ?
            OR
            ? = 1 AND v.numeracion = ?
            OR
            ? = 1 AND CONCAT(v.serie,'-',v.numeracion) = ?
            OR
           /* ? = 2 AND ? = 1 AND CAST(PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM NOW()), EXTRACT(YEAR_MONTH FROM m.fechaFin)) AS INT) < 0*/
            ? = 2 AND ? = 1 AND CAST(DATEDIFF(m.fechaFin,CURDATE()) AS INT) > 10 
            OR
            ? = 2 AND ? = 2 AND CAST(DATEDIFF(m.fechaFin,CURDATE()) AS int) >=0 AND CAST(DATEDIFF(m.fechaFin,CURDATE()) AS int) <=10
            OR
            ? = 2 AND ? = 3 AND CAST(DATEDIFF(m.fechaFin,CURDATE()) AS int) < 0
            GROUP BY m.idMembresia
            LIMIT ?,?");
            $membresia->bindParam(1, $opcion, PDO::PARAM_INT);//0

            $membresia->bindParam(2, $opcion, PDO::PARAM_INT);//dni
            $membresia->bindParam(3, $buscar, PDO::PARAM_STR);

            $membresia->bindParam(4, $opcion, PDO::PARAM_INT);//apellidos
            $membresia->bindParam(5, $buscar, PDO::PARAM_STR);

            $membresia->bindParam(6, $opcion, PDO::PARAM_INT);//nombres
            $membresia->bindParam(7, $buscar, PDO::PARAM_STR);

            $membresia->bindParam(8, $opcion, PDO::PARAM_INT);//apellidos y nombres
            $membresia->bindParam(9, $buscar, PDO::PARAM_STR);

            $membresia->bindParam(10, $opcion, PDO::PARAM_INT);//serie
            $membresia->bindParam(11, $buscar, PDO::PARAM_STR);

            $membresia->bindParam(12, $opcion, PDO::PARAM_INT);//numeracion
            $membresia->bindParam(13, $buscar, PDO::PARAM_STR);

            $membresia->bindParam(14, $opcion, PDO::PARAM_INT);//serie y numeracion
            $membresia->bindParam(15, $buscar, PDO::PARAM_STR);

            $membresia->bindParam(16, $opcion, PDO::PARAM_INT);
            $membresia->bindParam(17, $tipo, PDO::PARAM_INT);

            $membresia->bindParam(18, $opcion, PDO::PARAM_INT);
            $membresia->bindParam(19, $tipo, PDO::PARAM_INT);

            $membresia->bindParam(20, $opcion, PDO::PARAM_INT);
            $membresia->bindParam(21, $tipo, PDO::PARAM_INT);

            $membresia->bindParam(22, $x, PDO::PARAM_INT);
            $membresia->bindParam(23, $y, PDO::PARAM_INT);
            $membresia->execute();

            $count = 0;
            $array_membresias = array();
            while ($row = $membresia->fetch()) {
                $count++;
                array_push($array_membresias, array(
                    "id" => $count,
                    "dni" => $row["dni"],
                    "apellidos" => $row["apellidos"],
                    "nombres" => $row["nombres"],
                    "nombres" => $row["nombres"],
                    "membresia" => $row["membresia"],
                    "nombrePlan" => $row["nombrePlan"],
                    "serie" => $row["serie"],
                    "numeracion" => $row["numeracion"],
                    "fechaInicio" => $row["fechaInicio"],
                    "fechaFin" => $row["fechaFin"],
                    "estadoventa" => $row["estadoventa"],
                    "total" => floatval($row["total"]),
                ));
            }

            $total = Database::getInstance()->getDb()->prepare("SELECT count(m.idMembresia) FROM membresiatb as m
            INNER JOIN ventatb as v ON v.idVenta = m.idVenta
            INNER JOIN plantb as p ON p.idPlan = m.idPlan
            INNER JOIN clientetb as c ON c.idCliente  = m.idCliente
            WHERE 
            ? = 0
            OR
            ? = 1 AND c.dni LIKE CONCAT(?,'%')
            OR
            ? = 1 AND c.apellidos LIKE CONCAT(?,'%')
            OR
            ? = 1 AND c.nombres LIKE CONCAT(?,'%')
            OR
            ? = 1 AND CONCAT(c.apellidos,' ', c.nombres) LIKE CONCAT(?,'%')
            OR
            ? = 1 AND v.serie = ?
            OR
            ? = 1 AND v.numeracion = ?
            OR
            ? = 1 AND CONCAT(v.serie,'-',v.numeracion) = ?
            OR
            ? = 2 AND ? = 1 AND CAST(DATEDIFF(m.fechaFin,CURDATE()) AS INT) > 10 
            OR
            ? = 2 AND ? = 2 AND CAST(DATEDIFF(m.fechaFin,CURDATE()) AS int) >=0 AND CAST(DATEDIFF(m.fechaFin,CURDATE()) AS int) <=10
            OR
            ? = 2 AND ? = 3 AND CAST(DATEDIFF(m.fechaFin,CURDATE()) AS int) < 0");
            $total->bindParam(1, $opcion, PDO::PARAM_INT);//0

            $total->bindParam(2, $opcion, PDO::PARAM_INT);//
            $total->bindParam(3, $buscar, PDO::PARAM_STR);

            $total->bindParam(4, $opcion, PDO::PARAM_INT);//
            $total->bindParam(5, $buscar, PDO::PARAM_STR);

            $total->bindParam(6, $opcion, PDO::PARAM_INT);//
            $total->bindParam(7, $buscar, PDO::PARAM_STR);

            $total->bindParam(8, $opcion, PDO::PARAM_INT);//
            $total->bindParam(9, $buscar, PDO::PARAM_STR);

            $total->bindParam(10, $opcion, PDO::PARAM_INT);
            $total->bindParam(11, $buscar, PDO::PARAM_STR);

            $total->bindParam(12, $opcion, PDO::PARAM_INT);
            $total->bindParam(13, $buscar, PDO::PARAM_STR);

            $total->bindParam(14, $opcion, PDO::PARAM_INT);
            $total->bindParam(15, $buscar, PDO::PARAM_STR);

            $total->bindParam(16, $opcion, PDO::PARAM_INT);
            $total->bindParam(17, $tipo, PDO::PARAM_INT);

            $total->bindParam(18, $opcion, PDO::PARAM_INT);
            $total->bindParam(19, $tipo, PDO::PARAM_INT);

            $total->bindParam(20, $opcion, PDO::PARAM_INT);
            $total->bindParam(21, $tipo, PDO::PARAM_INT);

            $total->execute();
            $resultTotal = $total->fetchColumn();

            array_push($array, $array_membresias, $resultTotal);
            return $array;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getAllMembresiasPorCliente($idCliente, $x, $y)
    {

        try {
            $array = array();
            // Preparar sentencia
            $comando = Database::getInstance()->getDb()->prepare("SELECT 
            v.idVenta, 
            v.cliente, 
            m.idMembresia, 
            p.nombre, 
            v.documento, 
            v.serie, 
            v.numeracion, 
            v.estado as 'estadoventa',
            m.tipoMembresia,
            m.fechaInicio, 
            m.fechaFin, 
            CASE 
            WHEN CAST(DATEDIFF(m.fechaFin,CURDATE()) AS INT) > 10 THEN 1
            WHEN CAST(DATEDIFF(m.fechaFin,CURDATE()) AS INT) >=0 AND CAST(DATEDIFF(m.fechaFin,CURDATE()) AS INT) <=10 THEN 2
            ELSE 0 END AS 'membresia', 
            m.cantidad, 
            m.precio, 
            SUM(m.cantidad*m.precio) as 'total' 
            FROM membresiatb as m
            INNER JOIN ventatb as v ON v.idVenta = m.idVenta
            INNER JOIN plantb as p ON p.idPlan = m.idPlan
            where v.cliente=?
            GROUP BY m.idMembresia");
            $comando->bindValue(1, $idCliente, PDO::PARAM_STR);
            $comando->execute();
            $arrayDetalle = array();
            while ($row = $comando->fetch()) {
                array_push($arrayDetalle, $row);
            }

            $comando = Database::getInstance()->getDb()->prepare("SELECT count(*) FROM membresiatb as m
            INNER JOIN ventatb as v ON v.idVenta = m.idVenta
            INNER JOIN plantb as p ON p.idPlan = m.idPlan
            where v.cliente=?");
            $comando->bindValue(1, $idCliente, PDO::PARAM_STR);
            $comando->execute();
            $resulTotal = $comando->fetchColumn();

            array_push($array, $arrayDetalle, $resulTotal);
            return $array;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}
