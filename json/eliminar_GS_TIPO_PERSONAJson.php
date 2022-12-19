<?php
    require_once('../Conexion/conexion.php');
    session_start();

   
   $id = $_GET['id'];
   $query = "DELETE FROM gs_tipo_persona WHERE Id_Unico = '$id'";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>