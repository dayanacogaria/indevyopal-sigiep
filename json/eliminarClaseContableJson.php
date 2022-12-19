<?php 
	//Llamado de  la clase de conexión
	require_once('../Conexion/conexion.php');
	//Creación de sesion
    session_start();

   	//Captura de la variable id enviada por la url 
   	//usando el metodo get
   	$id = $_GET['id'];
   	//Consulta de eliminado
   	$query = "DELETE FROM gf_clase_contable WHERE Id_Unico = $id";
   	/*Creciación de la variable resultado la cual toma el valor o 
   	valores devueltos por la consulta la cual es cargada de manera 
   	embebida a la variable de conexión
   	 */
   	$resultado = $mysqli->query($query);
   	//Envio de la variable resultado el cuál retorna los valores en 
   	//forma de json
  	echo json_encode($resultado);
?>