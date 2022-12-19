<?php
require_once '../Conexion/conexion.php';
session_start();
#@Autor:Alexander
#Consulta para generar el autocompletado de numeros o consecutivos existentes en la base de datos
$tipo =  $_GET['term'];
$tipo = $_GET['tipo'];
$query = "SELECT numero_pago FROM gp_pago WHERE numero_pago LIKE '%$tipo%' && tipo_pago=$tipo";
$result = $mysqli->query($query);
if($result->num_rows > 0){
    while ($fila = $result->fetch_assoc()){
        $pagos[] = $fila['numero_pago'];
    }
    echo json_encode($pagos);
}
?>
