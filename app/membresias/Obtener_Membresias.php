<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require './MembresiaAdo.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Manejar petición GET
    $body = $_GET['page'];
    
    $membresias = MembresiaAdo::getAllMembresias(($body-1)*10,10);
    $total = MembresiaAdo::getAllCountMembresias();
    if ($membresias) {
        $datos["estado"] = 1;
        $datos["page"] = $body;
        $datos["page_rows"] = 10;
        $datos["total"] = $total;
        $datos["total_page"] = ceil($total / 10);
        $datos["membresias"] = $membresias;
        print json_encode($datos);
    } else {
        print json_encode(array(
            "estado" => 2,
            "mensaje" => "Ha ocurrido un error"
        ));
    }
    exit();
}
