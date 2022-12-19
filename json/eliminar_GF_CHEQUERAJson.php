<?php
    require_once('../Conexion/conexion.php');
    session_start();

   //Captura de ID y eliminación del registro correspondiente.
   $id = $_GET['id'];
   $query = "DELETE FROM gf_chequera WHERE id_unico = $id";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>