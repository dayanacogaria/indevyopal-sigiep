<?php

  require_once('../Conexion/conexion.php');
  session_start();
  $id  = $_GET['id'];
  
  $modFlujo = "UPDATE gg_flujo_procesal SET flujo_no = NULL WHERE id_unico = '$id'";
  $flujo= $mysqli->query($modFlujo);
 
echo json_encode($flujo);
?>