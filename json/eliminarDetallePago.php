<?php
session_start();
require_once '../Conexion/conexion.php';
$id = $_GET['id'];
$sql = "DELETE FROM gp_detalle_pago WHERe id_unico =$id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>

