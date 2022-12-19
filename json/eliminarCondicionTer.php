<?php 
	require_once('../Conexion/conexion.php');
    session_start();
   
   //Captura de ID e instrucción SQL para su eliminación de la tabla gf_perfil_tercero.
   $id = $_GET['id'];
   $tercero = $_GET['terc'];
   $query = "DELETE FROM gf_condicion_tercero WHERE perfilcondicion = '$id' AND tercero='$tercero'";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>