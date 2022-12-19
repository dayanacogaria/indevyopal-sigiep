<?php
    require_once('../Conexion/conexion.php');
    session_start();

   $id = $_GET['id'];
   $medidor = $_GET['medidor'];
  
   $updateM= "UPDATE gp_medidor SET fecha_instalacion =NULL WHERE id_unico = '$medidor'";
   $updateM=$mysqli->query($updateM);
   $query = "DELETE FROM gp_unidad_vivienda_medidor_servicio WHERE id_unico= $id ";
   
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>
