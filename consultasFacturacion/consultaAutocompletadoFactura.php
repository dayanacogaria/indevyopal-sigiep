<?php
require_once '../Conexion/conexion.php';
session_start();
#@Autor:Alexander
#Consulta para generar el autocompletado de numeros o consecutivos existentes en la base de datos
$factura =  $_GET['term'];
$tipo = $_GET['tipo'];
$query = "SELECT numero_factura FROM gp_factura WHERE numero_factura LIKE '%$factura%' && tipofactura=$tipo";
$result = $mysqli->query($query);
if($result->num_rows > 0){
    while ($fila = $result->fetch_assoc()){
        $facturas[] = $fila['numero_factura'];
    }
    echo json_encode($facturas);
}
?>
