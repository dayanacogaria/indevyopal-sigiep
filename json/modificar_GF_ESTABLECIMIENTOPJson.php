<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id = $mysqli->real_escape_string(''.$_POST['idm'].'');
  $fechaI  =$mysqli->real_escape_string(''.$_POST['fechaIm'].'');
  $fechaI = DateTime::createFromFormat('d/m/Y', "$fechaI");
  $fechaI= $fechaI->format('Y/m/d');
  
  $fechaF  =$mysqli->real_escape_string(''.$_POST['fechaFm'].'');
  $fechaF = DateTime::createFromFormat('d/m/Y', "$fechaF");
  $fechaF= $fechaF->format('Y/m/d');
  $descripcion  = '"'.$mysqli->real_escape_string(''.$_POST['descripcionm'].'').'"';
  $dependencia= $mysqli->real_escape_string(''.$_POST['dependenciam'].'');
  $cuenta1= $mysqli->real_escape_string(''.$_POST['cuenta1m'].'');
  $cuenta2= $mysqli->real_escape_string(''.$_POST['cuenta2m'].'');
  
  
 $insert = "UPDATE gf_establecimiento_politicas_niif SET "
         . "fecha_inicial='$fechaI', "
         . "fecha_final='$fechaF', "
         . "descripcion=$descripcion, "
         . "dependencia=$dependencia, "
         . "cuenta_uno=$cuenta1, "
         . "cuenta_dos= $cuenta2 "
         . "WHERE id_unico = '$id'";
  $resultado = $mysqli->query($insert);
  
  echo json_encode($resultado);
?>
