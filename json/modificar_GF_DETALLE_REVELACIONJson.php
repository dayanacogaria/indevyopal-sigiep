<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id = $mysqli->real_escape_string(''.$_POST['iddetalle'].'');
  $revelacion = $mysqli->real_escape_string(''.$_POST['revelacion'].'');
  
  $insert = "UPDATE gf_detalle_comprobante SET "
          . "revelacion='$revelacion' WHERE id_unico ='$id'";
  $resultado = $mysqli->query($insert);
  
  echo json_encode($resultado);
?>

