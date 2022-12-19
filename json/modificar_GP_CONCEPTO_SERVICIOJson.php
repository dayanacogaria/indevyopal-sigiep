<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id = $_GET['id'];
  $concepto = $_GET['concepto'];
  $servicio  = $_GET['servicio'];
  
  
  $queryUA="SELECT concepto, tipo_servicio FROM gp_concepto_servicio "
          . "WHERE id_unico = '$id'";
  $carA = $mysqli->query($queryUA);
  $numA=  mysqli_fetch_row($carA);
  
 $queryU="SELECT * FROM gp_concepto_servicio "
          . "WHERE concepto = $concepto "
          . "AND tipo_servicio  =$servicio ";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);
  if($numA[0]==$concepto && $numA[1] == $servicio){
        
        $resultado = '1';
  }else {
        if($num == 0)
        {
         
         $update = "UPDATE gp_concepto_servicio  "
              . "SET concepto = '$concepto', "
              . "tipo_servicio ='$servicio' "
              . "WHERE id_unico = '$id'";
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