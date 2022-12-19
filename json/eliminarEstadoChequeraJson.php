<?php 
	//Llamado de la clase conexión 
	require_once '../Conexion/conexion.php';
	//Creación de la sesión
	session_start();
	//Definición de la variable $id con la captura del valor por get enviado
	//en la url
	$id = $_GET['id'];	
	//Definición de la variable $sql con la query y el valor de la $id
	$sql = "DELETE FROM gf_estado_chequera WHERE Id_Unico = $id"; 
	//Definición de la variable $resultado con el valor devuelto de la consulta
	//el cual es cargo de manera embebida a la variable de conexión
	$resultado = $mysqli->query($sql);
	//Impresión del valor de la variable $resultado como json
	echo json_encode($resultado);
 ?>