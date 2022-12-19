<?php 
	//Llamado de la clase conexión
	require_once '../Conexion/conexion.php';
	//Creación de la sesion
	session_start();
	//Definición de la variable $id con el valor enviado por get desde la url
	$id = $_GET['id'];
	//Definición de la variable $sql con la consulta y el valor de la variable
	//$id
	$sql = "DELETE FROM gf_estado_comprobante_cnt WHERE Id_Unico = $id";
	//Definición de la variable $sql con el valor retornado de la consulta 
	//el cual es cargado de forma embebida a la variable de conexión
	$resultado = $mysqli->query($sql);
	//Impresión del valor de la variable $resultado como json
	echo json_encode($resultado);



 ?>