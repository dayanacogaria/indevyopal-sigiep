<?php
  require_once('../Conexion/conexion.php');
  session_start();


  $id_val = $_REQUEST['id_val'];
  $valor = $_REQUEST['valor'];
  $tercero = $_REQUEST['tercero'];
  $proyecto = $_REQUEST['proyecto'];

  $updateSQL = "UPDATE gf_detalle_comprobante_pptal  
    SET valor = $valor, tercero = $tercero, proyecto = $proyecto     
    WHERE id_unico = $id_val";
  $resultado = $mysqli->query($updateSQL);
  if($resultado == true)
    echo 1;
  else
    echo 0;
?>
