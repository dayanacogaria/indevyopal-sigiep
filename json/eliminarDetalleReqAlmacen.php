<?php
session_start();
require '../Conexion/conexion.php';
$id = $_GET['id'];
$sql = "DELETE FROM gf_detalle_movimiento WHERE id_unico=$id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>

