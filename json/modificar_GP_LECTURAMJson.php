<?php
  require_once('../Conexion/conexion.php');
  session_start();
	
  setlocale(LC_ALL,"es_ES");
  date_default_timezone_set('America/Bogota');
 $fecha= date('Y-m-d H:m:s'); 
 $tercero = $_SESSION['compania'];
 $id= $mysqli->real_escape_string(''.$_POST['idm'].'');
 $valor= $mysqli->real_escape_string(''.$_POST['valorm'].'');
 
 $insert ="UPDATE gp_lectura "
         . "SET valor=$valor, fecha='$fecha' WHERE id_unico ='$id'";
 $resultado = $mysqli->query($insert);
 
 echo json_encode($resultado);
 ?>
