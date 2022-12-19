<?php
  require_once('../Conexion/conexion.php');
  session_start();

  $id = $_GET['p1'];
  $tipoActi = $_GET['p2'];

  $updateSQL = "UPDATE gf_tipo_actividad_tercero SET tipoactividad = '$tipoActi' WHERE tercero = '$id'";
  $resultado = $mysqli->query($updateSQL);

  echo json_encode($resultado);
  
?>
