<?php

require_once '../Conexion/conexion.php';
session_start();
$id = $_GET["id"];
$sql = "DELETE FROM gc_clase_documento WHERE id_unico=$id";
$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>
