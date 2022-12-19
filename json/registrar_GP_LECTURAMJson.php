<?php
  require_once('../Conexion/conexion.php');
  session_start();
	
  setlocale(LC_ALL,"es_ES");
  date_default_timezone_set('America/Bogota');
 $fecha= date('Y-m-d H:m:s'); 
 $tercero = $_SESSION['compania'];
 $uvms= $mysqli->real_escape_string(''.$_POST['iduvms'].'');
 $periodo= $mysqli->real_escape_string(''.$_POST['periodo'].'');
 $valor= $mysqli->real_escape_string(''.$_POST['valor'].'');
 
 $nr= "SELECT * FROM gp_lectura WHERE unidad_vivienda_medidor_servicio='$uvms' AND periodo = '$periodo'";
 $nr = $mysqli->query($nr);
 if(mysqli_num_rows($nr)>0){
     $resultado = '3';
 } else { 
 $insert ="INSERT INTO gp_lectura (unidad_vivienda_medidor_servicio, periodo, valor, aforador, fecha)VALUES ($uvms, $periodo, $valor, $tercero, '$fecha')";
 $resultado = $mysqli->query($insert);
 }
 echo json_encode($resultado);
 ?>