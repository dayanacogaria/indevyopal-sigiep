<?php
require_once '../Conexion/conexion.php';
session_start();
$comprobante =  $_GET['term'];
$tipo = $_GET['tipo'];
$query = "SELECT numero FROM gf_comprobante_cnt WHERE numero LIKE '%$comprobante%' AND tipocomprobante='$tipo' AND fecha != '2016-01-01'";
$result = $mysqli->query($query);
if($result->num_rows > 0){
    while ($fila = $result->fetch_assoc()){
        $comprobantes[] = $fila['numero'];
    }
    echo json_encode($comprobantes);
}
?>
