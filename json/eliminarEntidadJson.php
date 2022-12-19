<?php
require_once '../Conexion/conexion.php';
session_start();
$id = $_GET["id"];
$sql = "DELETE FROM gn_entidad WHERE id_unico = $id";
$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>