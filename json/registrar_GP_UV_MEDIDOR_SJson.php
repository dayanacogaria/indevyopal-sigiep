<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $uvs  = $_GET['uvs'];
  $medidor  = $_GET['medidor'];
  $fecha =  $_GET['fecha'];
  
  $queryU="SELECT * FROM gp_unidad_vivienda_medidor_servicio "
          . "WHERE  medidor = $medidor ";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  if($num == 0)
  {
   $modMedidor = "UPDATE gp_medidor SET fecha_instalacion = '$fecha' WHERE id_unico = '$medidor'";
   $mysqli->query($modMedidor);
   $insert = "INSERT INTO gp_unidad_vivienda_medidor_servicio (unidad_vivienda_servicio, medidor) "
          . "VALUES($uvs, $medidor)";
   $insert= $mysqli->query($insert);
  $resultado= '1';
   }
  else
  {
      if($num > 0){ 
    $resultado='2';
    } else {
        $resultado = false;
    }
    
  }
echo json_encode($resultado);
?>