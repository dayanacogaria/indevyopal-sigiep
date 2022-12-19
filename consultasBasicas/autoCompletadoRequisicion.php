<?php
require_once '../Conexion/conexion.php';
session_start();
$requisicion =  $_GET['term'];
$tipo = 4;
$query = "SELECT numero FROM gf_movimiento WHERE numero LIKE '%$requisicion%' AND tipomovimiento='4'";
$result = $mysqli->query($query);
if($result->num_rows > 0){
    while ($fila = $result->fetch_assoc()){
        $requisiciones[] = $fila['numero'];
    }
    echo json_encode($requisiciones);
}
?>
