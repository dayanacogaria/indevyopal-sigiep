<?php
require_once '../Conexion/conexion.php';
session_start();
$id = $_GET["id"];
$opc = $_GET["opc"];
if($opc == 2) {
	$empl = $_GET["emp"];
	$per = $_GET["peri"];
	$sql = "DELETE FROM gn_novedad WHERE empleado = $empl and periodo = $per";	
}else {
	$sql = "DELETE FROM gn_novedad WHERE id_unico = $id";	
}

$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>