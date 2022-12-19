<?php 
    require_once('../Conexion/conexion.php');
    session_start();
   
   $id = $_GET['id'];

  $deleteSQL = "DELETE FROM gf_equivalencia_puc WHERE id_unico = $id";
   $resultado = $mysqli->query($deleteSQL);

  echo json_encode($resultado);
?>
