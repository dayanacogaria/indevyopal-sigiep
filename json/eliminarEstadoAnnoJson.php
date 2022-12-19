<?php 
	//Llamado de la clase de conexión
	require_once '../Conexion/conexion.php';
	//Creación de la sesión
	session_start();
	//Definición de la variable $id con la captura del valor enviado por la
	//url
	$id = $_GET['id'];
	//Definición de la variable $sql con la consulta y el valor de la 
	//variable $id
	$sql = "DELETE FROM gf_estado_anno WHERE Id_Unico = $id";
	//Definición de la variable $resultaldo con el valor devuelto de la 
	//consulta cargada de forma embebida a la variable de conexión
	$resultado = $mysqli->query($sql);
	//Impresión del valor de la variable $resultado como json
	echo json_encode($resultado);



 ?>