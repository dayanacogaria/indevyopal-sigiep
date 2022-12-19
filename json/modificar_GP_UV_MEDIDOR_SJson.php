<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id  = $_GET['id'];
  $medidor  = $_GET['medidor'];
  $fecha =  $_GET['fecha'];
  
  $queryU="SELECT * FROM gp_unidad_vivienda_medidor_servicio "
          . "WHERE  medidor = $medidor ";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);
  
  $queryUA="SELECT uvms.medidor, m.fecha_instalacion FROM gp_unidad_vivienda_medidor_servicio uvms "
          . "LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico "
          . "WHERE uvms.id_unico = '$id'";
  $carA = $mysqli->query($queryUA);
  $numA=  mysqli_fetch_row($carA);
  
  if($numA[0]==$medidor && $numA[1]==$fecha ){
      $resultado = '1';
  }else {
      
    if($num == 0)
    {
     $modMedidor = "UPDATE gp_medidor SET fecha_instalacion = NULL WHERE id_unico = '$numA[0]'";
     $mysqli->query($modMedidor);
     
     $modMedidor = "UPDATE gp_medidor SET fecha_instalacion = '$fecha' WHERE id_unico = '$medidor'";
     $mysqli->query($modMedidor);
     
     $insert = "UPDATE gp_unidad_vivienda_medidor_servicio SET medidor = $medidor WHERE id_unico = $id";
     $insert= $mysqli->query($insert);
    $resultado= '1';
     } else {
         if($numA[0]==$medidor){
                $modMedidor = "UPDATE gp_medidor SET fecha_instalacion = NULL WHERE id_unico = '$numA[0]'";
                $mysqli->query($modMedidor);

                $modMedidor = "UPDATE gp_medidor SET fecha_instalacion = '$fecha' WHERE id_unico = '$medidor'";
                $mysqli->query($modMedidor);

                $insert = "UPDATE gp_unidad_vivienda_medidor_servicio SET medidor = $medidor WHERE id_unico = $id";
                $insert= $mysqli->query($insert);
               $resultado= '1';
            }
        else { 
            if($num > 0){ 
                $resultado='2';
            } else {
                $resultado = false;
            }
        }
     }
  }
  
echo json_encode($resultado);
?>