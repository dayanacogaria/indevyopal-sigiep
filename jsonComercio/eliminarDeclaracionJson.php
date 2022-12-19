<?php

require_once '../Conexion/conexion.php';
session_start();
$id = $_GET["id"];
$sql = "DELETE FROM gc_detalle_declaracion WHERE declaracion=$id";
$resultado = $mysqli->query($sql);

if ($resultado == true){

 $sql = "DELETE FROM gc_declaracion_ingreso WHERE declaracion=$id";
 $resultado1 = $mysqli->query($sql);

  if ($resultado1 == true){

	 $sql = "DELETE FROM gc_declaracion WHERE id_unico=$id";
	 $resultado2 = $mysqli->query($sql); 
  }	 

}
	


echo json_encode($resultado);
?>
