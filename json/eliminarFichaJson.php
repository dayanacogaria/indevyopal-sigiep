<?php
session_start();
require_once '../Conexion/conexion.php';
$id = $_GET['id'];
$sql = "delete from gf_ficha where id_unico = $id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>

