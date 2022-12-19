<?php
require_once '../Conexion/conexion.php';
session_start();
$orden =  $_GET['term'];
$tipo = $_GET['tipo'];
$query = "SELECT numero FROM gf_movimiento WHERE numero LIKE '%$orden%' AND tipomovimiento='$tipo'";
$result = $mysqli->query($query);
if($result->num_rows > 0){
    while ($fila = $result->fetch_assoc()){
        $movmientos[] = $fila['numero'];
    }
    echo json_encode($movmientos);
}
?>
