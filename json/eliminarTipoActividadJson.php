<?php 
	//Llamado de la clase de conexión
	require_once '../Conexion/conexion.php';
	//Creación de la sesión
	session_start();
	//Definición de la variable $id con el valor enviado por get desde la url
	$id = $_GET['id'];
	//Definición de la variable $resultado con la consulta y el valor de $id
	$sql = "DELETE FROM gf_tipo_actividad WHERE id_unico = $id";
	//Definición de la variable $resultado con el resultado de la variable $sql
	//el cual es cargado de forma embebida a la variable de conexión
	$resultado = $mysqli->query($sql);	
	//Impresión del valor de la variable $resultado como json
	echo json_encode($resultado);



 ?>