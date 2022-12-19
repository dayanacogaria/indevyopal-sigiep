<?php
require_once '../Conexion/conexion.php';
session_start();
$id = $_GET["id"];
$sql = "DELETE FROM gn_estado_tercero_categoria WHERE id_unico = $id";
$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>