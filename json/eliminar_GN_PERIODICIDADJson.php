<?php
    require_once('../Conexion/conexion.php');

   //Captura de ID y eliminación del registro correspondiente.
	$id = $_GET['id'];
	$query = "DELETE FROM gn_periodicidad WHERE id = $id";
	$resultado = $mysqli->query($query);

	echo json_encode($resultado);
?>