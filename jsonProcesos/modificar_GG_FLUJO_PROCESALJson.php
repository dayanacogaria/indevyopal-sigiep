<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id  = $mysqli->real_escape_string(''.$_POST['id'].'');
  $proceso = $mysqli->real_escape_string(''.$_POST['tipop'].'');
  $estado  = $mysqli->real_escape_string(''.$_POST['estado'].'');
  $fase= $mysqli->real_escape_string(''.$_POST['fase'].'');
  $duracion= $mysqli->real_escape_string(''.$_POST['duracion'].'');
  $tercero= $mysqli->real_escape_string(''.$_POST['responsable'].'');
  
  if(!empty($_POST['unidad'])){
     $unidad= $mysqli->real_escape_string(''.$_POST['unidad'].'');
  }else{
       $unidad='NULL';
  }
  if ($unidad=='""'|| $unidad=='' || $unidad==NULL || $unidad=='NULL'){
     
      $unidad='NULL'; 
      $unidadB='IS NULL'; 
  } else {
      
      $unidadB = '='.$unidad;
      
  }
  if(!empty($_POST['tipod'])){
     $tipod= $mysqli->real_escape_string(''.$_POST['tipod'].''); 
  }else{
       $tipod='NULL';
  }
  if ($tipod=='""'|| $tipod=='' || $tipod==NULL || $tipod=='NULL'){
     
      $tipod='NULL'; 
      $tipoB='IS NULL'; 
  } else {
      
      $tipoB = '='.$tipod;
      
  }
  if ($duracion=='""'|| $duracion=='' || $duracion==NULL){
    $duracion=0; 
  }
  if ($tercero=='""'|| $tercero=='' || $tercero==NULL){
    $tercero='NULL'; 
    $terceroB='IS NULL'; 
  } else {
     $terceroB='='.$tercero;
  }
   if(!empty($_POST['estado'])){
     $estado= $mysqli->real_escape_string(''.$_POST['estado'].'');
  }else{
     $estado='NULL';  
  }
 if ($estado=='""'|| $estado=='' || $estado==NULL || $estado=='NULL'){
    $estado='NULL'; 
    $estadoB='IS NULL'; 
  } else {
     $estadoB='='.$estado;
  }
  
 $queryU="SELECT * FROM gg_flujo_procesal "
          . "WHERE tipo_proceso = '$proceso' "
          . "AND fase = '$fase' "
          . "AND duracion='$duracion' "
          . "AND tipo_dia $tipoB "
          . "AND unidad_tiempo $unidadB "
          . "AND tercero $terceroB "
          . "AND estado $estadoB";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);
  
  
  $queryUA="SELECT tipo_proceso, fase, duracion, tipo_dia, tercero, unidad_tiempo, estado FROM gg_flujo_procesal "
          . "WHERE id_unico = '$id'";
  $carA = $mysqli->query($queryUA);
  $numA=  mysqli_fetch_row($carA);
  
  //Busca elemento por si cambia, en caso de que sea condicion elimine el registro de condicion no, o si es etapa especial, elimine registro
  $elemento = "SELECT f.id_unico, ef.id_unico, ef.nombre "
            . "FROM gg_fase f LEFT JOIN gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico WHERE f.id_unico = $fase";
  $elemento = $mysqli->query($elemento);
  $elemento = mysqli_fetch_row($elemento);
  $comp = strtolower($elemento[2]);
  switch ($comp){
      case('etapa especial'):
          $borrar1= "UPDATE gg_flujo_procesal SET flujo_no = NULL, flujo_si = NULL WHERE id_unico = '$id'";
          $borrar1 = $mysqli->query($borrar1);
      break;
      case('condicion'):
      case('condición'):
          
      break;
      default :
          $borrar2= "UPDATE gg_flujo_procesal SET flujo_no = NULL WHERE id_unico = '$id'";
          $borrar2 = $mysqli->query($borrar2);
      break;
  }
  
  //Buscar estado si cambia eliminar flujo
  $nomEstado ="SELECT id_unico, nombre FROM gg_estado_proceso WHERE id_unico ='$estado'";
  $nomEstado=$mysqli->query($nomEstado);
  $nomEstado = mysqli_fetch_row($nomEstado);
  $nomEstado = strtolower($nomEstado[1]);
  if($nomEstado=='cerrado' || $nomEstado=='anulado'){
      $borrar1= "UPDATE gg_flujo_procesal SET flujo_no = NULL, flujo_si = NULL WHERE id_unico = '$id'";
      $borrar1 = $mysqli->query($borrar1);
  }

   //comparación para que guarde
   if($numA[0]==$proceso && 
           $numA[1]==$fase && 
           $numA[2]==$duracion && 
           $numA[3]==$tipod || $numA[3]==''&& 
           $numA[4]==$tercero || $numA[4]=='' &&
           $numA[6]==$estado || $numA[4]=='' &&
           $numA[5]==$unidad || $numA[5]==''){
       $insert = "UPDATE gg_flujo_procesal  "
            . "SET tipo_proceso=$proceso, "
            . "fase=$fase, "
            . "duracion=$duracion, "
            . "tipo_dia=$tipod, "
            . "tercero=$tercero, "
            . "unidad_tiempo=$unidad, estado = $estado WHERE id_unico = $id ";
        $resultado = $mysqli->query($insert);
   } else { 
        if($num == 0)
        {
         $insert = "UPDATE gg_flujo_procesal  "
                  . "SET tipo_proceso=$proceso, "
                  . "fase=$fase, "
                  . "duracion=$duracion, "
                  . "tipo_dia=$tipod, "
                  . "tercero=$tercero, "
                  . "unidad_tiempo=$unidad, estado = $estado WHERE id_unico = $id ";
          $resultado = $mysqli->query($insert);
         }
        else
        {
            if($num > 0){
                 $resultado ='3';
             }else {
                 $resultado= false;
             }
        }
   }
         
echo json_encode($resultado);
?>