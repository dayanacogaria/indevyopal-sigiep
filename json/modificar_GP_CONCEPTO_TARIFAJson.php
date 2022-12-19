<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id = $_GET['id'];
  $concepto = $_GET['concepto'];
  $nombre  = $_GET['nombre'];
  $tarifa  = $_GET['tarifa'];
  $cadena1 = strtolower($nombre);
 
  $exp_regular = array();
  $exp_regular[0] = '/á/';
  $exp_regular[1] = '/é/';
  $exp_regular[2] = '/í/';
  $exp_regular[3] = '/ó/';
  $exp_regular[4] = '/ú/';
  $exp_regular[4] = '/ñ/';
 
  $cadena_nueva = array();
  $cadena_nueva[0] = 'a';
  $cadena_nueva[1] = 'e';
  $cadena_nueva[2] = 'i';
  $cadena_nueva[3] = 'o';
  $cadena_nueva[4] = 'u';
  $cadena_nueva[4] = 'n';
  $nombreC= preg_replace($exp_regular, $cadena_nueva, $cadena1);
  
  $queryUA="SELECT nombre, concepto, tarifa FROM gp_concepto_tarifa "
          . "WHERE id_unico = '$id'";
  $carA = $mysqli->query($queryUA);
  $numA=  mysqli_fetch_row($carA);
  
 $queryU="SELECT * FROM gp_concepto_tarifa "
          . "WHERE concepto = $concepto "
          . "AND LOWER(nombre) ='$nombreC' "
          . "AND tarifa  =$tarifa ";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);
  if(strtolower($numA[0])==$nombreC && $numA[1]==$concepto
     && $numA[2] == $tarifa){
        
        $resultado = '1';
  }else {
        if($num == 0)
        {
         
         $update = "UPDATE gp_concepto_tarifa  "
              . "SET nombre ='$nombre', "
              . "concepto = '$concepto', "
              . "tarifa ='$tarifa' "
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