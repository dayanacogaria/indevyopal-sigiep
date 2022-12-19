<?php
require_once('../Conexion/conexion.php');
  session_start();
 $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valor'].'').'"';
 $proceso= '"'.$mysqli->real_escape_string(''.$_POST['proceso'].'').'"';
 $caracteristica= '"'.$mysqli->real_escape_string(''.$_POST['caracteristica'].'').'"';
 $queryU="SELECT * FROM gg_caracteristica_x "
          . "WHERE descripcion = $valor "
          . "AND proceso=$proceso AND caracteristica = $caracteristica";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  if($num == 0)
  {
 $insert = "INSERT INTO gg_caracteristica_x(descripcion, proceso, caracteristica) "
          . "VALUES($valor,  $proceso, $caracteristica)";
  $resultado = $mysqli->query($insert);
   }
  else
  {
      if($num>0){
          $resultado = '3';
      }else {
          $resultado = false;
      }
          
    
  }
  echo json_encode($resultado);
?>

