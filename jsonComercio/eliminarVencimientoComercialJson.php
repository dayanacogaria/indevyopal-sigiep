<?php
session_start();
require_once '../Conexion/conexion.php';
$id = $_GET['id'];
$sql = "DELETE FROM gc_vencimiento_comercial WHERE id_unico = $id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>

