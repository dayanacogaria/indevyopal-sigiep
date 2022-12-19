<?php
require_once '../Conexion/conexion.php';
session_start();
$id= $_GET['id'];
$tipo=$_GET['tipo'];
$estados=$_GET['estados'];
$uvv=$_GET['uvv'];

  $queryUA="SELECT unidad_vivienda, tipo_servicio, estado_servicio FROM gp_unidad_vivienda_servicio "
          . "WHERE id_unico = '$id'";
  $carA = $mysqli->query($queryUA);
  $numA=  mysqli_fetch_row($carA);
  
 $queryU="SELECT * FROM gp_unidad_vivienda_servicio WHERE unidad_vivienda='$uvv' AND tipo_servicio='$tipo' AND estado_servicio='$estados'";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  
  if($numA[1]==$tipo && $numA[2]==$estados && $numA[0]==$uvv ){
      $resultado = '1';
  }else {
        if($num == 0)
        {
         
        $update = "UPDATE gp_unidad_vivienda_servicio SET tipo_servicio=$tipo,estado_servicio=$estados,unidad_vivienda=$uvv "
        . "WHERE id_unico='$id'";
         $resul = $mysqli->query($update);
         
         $resultado ='1';
         } else {
             if($num > 0){
                 $resultado ='3';
             }else {
                 $resultado= false;
             }
         } 
  }
  echo json_encode($resultado);
?>
  
