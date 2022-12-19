<?php
    require_once('../Conexion/conexion.php');
    session_start();

   
   $id = $_GET['id'];
   $query = "DELETE FROM gf_tipo_equivalencia_puc WHERE id_unico = $id";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>
