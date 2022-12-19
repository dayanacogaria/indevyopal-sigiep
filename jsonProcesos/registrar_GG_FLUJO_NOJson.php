<?php

  require_once('../Conexion/conexion.php');
  session_start();
  $_SESSION['flujoprocesal'] = '';
  $id  = $_GET['id'];
  $flujo  = $_GET['flujo'];
  
   $modFlujo = "UPDATE gg_flujo_procesal SET flujo_no = '$flujo' WHERE id_unico = '$id'";
   $flujo= $mysqli->query($modFlujo);
 
echo json_encode($flujo);
?>