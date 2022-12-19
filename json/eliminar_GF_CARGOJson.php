<?php
    require_once('../Conexion/conexion.php');
    session_start();

   //Captura de ID y eliminación del resgistro correspondiente.
   $id = $_GET['id'];
   $query = "DELETE FROM gf_cargo WHERE Id_Unico = $id";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>