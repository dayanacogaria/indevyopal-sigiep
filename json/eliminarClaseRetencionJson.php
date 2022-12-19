<?php 
	//Llamado de la clase conexión
	require_once '../Conexion/conexion.php';
	//Creación de sessión
	session_start();	
	//Definición de la variable id la cual toma el valor enviado por get
	//desde el formulario de lista
	$id = $_GET['id'];
	//Definición de la variable sql como variable de string o consulta
	$sql = "DELETE FROM gf_clase_retencion WHERE Id_Unico = $id";
	//Definición de la variable resultado como variable de proceso
	$resultado = $mysqli->query($sql);
	//Se imprime la variable resultado como json
	echo json_encode($resultado);

 ?>