<?php
session_start();
require_once '../Conexion/conexion.php';
$id = $_GET['id'];
$sql = "DELETE FROM gf_ficha_inventario WHERE id_unico= $id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>

