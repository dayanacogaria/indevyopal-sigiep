<?php 
	//Llamado de la clase conexión
	require_once '../Conexion/conexion.php';
	//Creación de la sesión
	session_start();
	//Definción de la variable $id con el valor enviado por get en la url
	$id = $_GET['id'];
	//Definición de la variable $sql con la consulta y el valor de variable $sql
	$sql = "DELETE FROM gf_tipo_condicion WHERE Id_Unico = $id";
	//Definición de la variable $resultado con el valor de la variable $sql 
	//la cual es cargada de forma embebida a la variable de conexión
	$resultado = $mysqli->query($sql);
	//Impresión del valor de la variable $resultado como json
	echo json_encode($resultado);
 ?>