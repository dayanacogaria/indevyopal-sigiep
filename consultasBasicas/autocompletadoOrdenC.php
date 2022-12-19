<?php
require_once '../Conexion/conexion.php';
session_start();
$orden    =  $_GET['term'];
$param    = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$tipo     = 1;
$query    = "SELECT numero FROM gf_movimiento WHERE numero LIKE '%$orden%' AND tipomovimiento='$tipo' AND parametrizacionanno = $param AND compania = $compania";
$result = $mysqli->query($query);
if($result->num_rows > 0){
    while ($fila = $result->fetch_assoc()){
        $movmientos[] = $fila['numero'];
    }
    echo json_encode($movmientos);
}
?>
