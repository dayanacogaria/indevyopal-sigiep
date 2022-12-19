<?php
    require_once('../Conexion/conexion.php');
    session_start();

   
   $id = $_GET['id'];
   $query = "DELETE FROM gf_cuenta_bancaria WHERE Id_Unico = $id";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>