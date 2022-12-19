<?php 
	require_once('../Conexion/conexion.php');
    session_start();

   
   $id = $_GET['id'];
   $query = "DELETE FROM gn_tabla_homologable WHERE id = $id";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>