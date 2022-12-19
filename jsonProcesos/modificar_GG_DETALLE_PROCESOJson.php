<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id  = $mysqli->real_escape_string(''.$_POST['id'].'');
  $fechaP  = $mysqli->real_escape_string(''.$_POST['fechaP'].'');
  $fechaP = DateTime::createFromFormat('d/m/Y', "$fechaP");
  $fechaP= $fechaP->format('Y/m/d');
  
  if(!empty($_POST['fechaE'])){
  $fechaE  = $mysqli->real_escape_string(''.$_POST['fechaE'].'');
  $fechaE = DateTime::createFromFormat('d/m/Y', "$fechaE");
  $fechaE= $fechaE->format('Y/m/d');
  } else {
      $fechaE='NULL';
  }
  
  $tercero= $mysqli->real_escape_string(''.$_POST['responsable'].'');
  $formaN= $mysqli->real_escape_string(''.$_POST['forma'].'');
  $observaciones= $mysqli->real_escape_string(''.$_POST['observaciones'].'');

 $insert = "UPDATE gg_detalle_proceso SET "
        . "fecha_programada='$fechaP', "
        . "fecha_ejecutada ='$fechaE', "
        . "tercero='$tercero', "
        . "forma_notificacion='$formaN', "
        . "observaciones='$observaciones' "
        . "WHERE id_unico = '$id'";
$resultado = $mysqli->query($insert);
    
  echo json_encode($resultado) ;
?>
