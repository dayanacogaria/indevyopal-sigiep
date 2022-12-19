<?php
  require_once('../Conexion/conexion.php');
  $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
  $sltTable  = '"'.$mysqli->real_escape_string(''.$_POST['sltTable'].'').'"';
  $updateRol = "UPDATE gn_informe SET select_table = $sltTable WHERE id = $id";
  $resultado = $mysqli->query($updateRol);
  echo json_encode($resultado);
?>
