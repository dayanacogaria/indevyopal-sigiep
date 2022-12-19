<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id = $mysqli->real_escape_string(''.$_POST['idm'].'');
  $cuentaE = $mysqli->real_escape_string(''.$_POST['cuenta2m'].'');
  
  $insert = "UPDATE gf_equivalencia_puc SET "
          . "cuenta_equivalente=$cuentaE WHERE id_unico ='$id'";
  $resultado = $mysqli->query($insert);
  
  echo json_encode($resultado);
?>
