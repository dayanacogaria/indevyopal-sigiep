<?php
require_once '../Conexion/conexion.php';
session_start();
$id = $_GET["id"];
$validaCaTer="SELECT * FROM gn_novedad WHERE empleado=$id";
$resultadoCaTer = $mysqli->query($validaCaTer);
$sql = "DELETE FROM gn_empleado WHERE id_unico = $id";
$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>