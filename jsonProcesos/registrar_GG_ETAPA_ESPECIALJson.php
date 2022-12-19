<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $proceso  = $mysqli->real_escape_string(''.$_POST['proceso'].'');
  $tercero  = $mysqli->real_escape_string(''.$_POST['tercero'].'');
  $flujo  = $mysqli->real_escape_string(''.$_POST['flujo'].'');
  
  #FECHA PROGRAMADA
  $fechaP  = $mysqli->real_escape_string(''.$_POST['fechaP'].'');
  $fechaP = DateTime::createFromFormat('d/m/Y', "$fechaP");
  $fechaP= $fechaP->format('Y/m/d');
  
  #FECHA EJECUTADA
  if(!empty($_POST['fechaE'])){
  $fechaE  = $mysqli->real_escape_string(''.$_POST['fechaE'].'');
  $fechaE = DateTime::createFromFormat('d/m/Y', "$fechaE");
  $fechaE= $fechaE->format('Y/m/d');
  } else {
      $fechaE='NULL';
  }
  #FLUJO RELACIONADO
  if(!empty($_POST['flujoR'])){
    $flujoR  = $mysqli->real_escape_string(''.$_POST['flujoR'].'');
    
  } else {
      $flujoR='NULL';
  }
    $FlujoEtapaE = "UPDATE gg_flujo_procesal SET flujo_si = $flujoR WHERE id_unico ='$flujo'";
    $FlujoEtapaE = $mysqli->query($FlujoEtapaE);
  #ESTADO GUARDAR
  $estadop = "SELECT estado FROM gg_proceso WHERE id_unico ='$proceso'";
  $estadop = $mysqli->query($estadop);
  $estadop = mysqli_fetch_row($estadop);
  $estadog = $estadop[0];
  
  #ESTADO MODIFICAR PROCESO
  $estadoNuevoP = "SELECT estado FROM gg_flujo_procesal WHERE id_unico = '$flujo'";
  $estadoNuevoP = $mysqli->query($estadoNuevoP);
  $estadoNuevoP = mysqli_fetch_row($estadoNuevoP);
  if(empty($estadoNuevoP[0]) || $estadoNuevoP[0]==''){
     $estadoproceso = $estadog;
  }else {
      $estadoproceso=$estadoNuevoP[0];
  }
  $condicion =1;
  $observaciones ='Etapa Especial';
  if ($FlujoEtapaE ==true || $FlujoEtapaE==1 ){
        $cambioEstado ="UPDATE gg_proceso SET estado ='$estadoproceso' WHERE id_unico ='$proceso'";
        $cambioEstado = $mysqli->query($cambioEstado);
        if ($cambioEstado ==true || $cambioEstado==1 ){
            $insert = "INSERT INTO gg_detalle_proceso ( proceso, flujo_procesal, fecha_programada, fecha_ejecutada, tercero, condicion, estadoA, observaciones) "
                   . "VALUES('$proceso', '$flujo', '$fechaP', '$fechaE', '$tercero','$condicion', '$estadog', '$observaciones')";
            $resultado = $mysqli->query($insert);
        } else { $resultado=false; }
  } else {
      $resultado=false;
  }
        
    
    echo json_encode($resultado);
?>
