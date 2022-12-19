<?php
    require_once('../Conexion/conexion.php');

   //Captura de ID y eliminación del registro correspondiente.
	$id = $_GET['id'];
	$query = "DELETE FROM gn_categoria_formula WHERE id_unico = $id";
	$resultado = $mysqli->query($query);

	echo json_encode($resultado);
?>