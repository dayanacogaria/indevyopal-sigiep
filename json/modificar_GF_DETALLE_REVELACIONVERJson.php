<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id = $mysqli->real_escape_string(''.$_POST['iddetalleV'].'');
  $revelacion = $mysqli->real_escape_string(''.$_POST['revelacionV'].'');
  
  $insert = "UPDATE gf_detalle_comprobante SET "
          . "revelacion='$revelacion' WHERE id_unico ='$id'";
  $resultado = $mysqli->query($insert);
  
  echo json_encode($resultado);
?>

