<?php 
	//Llamado de la clase de conexión
	require_once '../Conexion/conexion.php';
	//Creación de la sesión
	session_start();
	//Definción de la variable $id con el valor enviado por get desde la url
	$id = $_GET['id'];
	//Definición de la variable $sql con la consulta y el valor de la variable 
	//$id
	$sql = "DELETE FROM gf_estado_mes WHERE Id_Unico = $id";
	//Definición de la variable $resultado con el valor retornado de la consulta
	//la cual es cargada de forma embedida
	$resultado = $mysqli->query($sql);
	//Impresión del valor de la variable $resultado como json
	echo json_encode($resultado);



 ?>