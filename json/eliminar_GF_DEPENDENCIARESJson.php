<?php
    require_once('../Conexion/conexion.php');
    session_start();

   $dep = $_GET['dep'];
   $resp = $_GET['res'];
   $mov = $_GET['mov'];
   $est = $_GET['est'];
   
   $query = "DELETE FROM gf_dependencia_responsable WHERE dependencia = $dep AND responsable= $resp AND movimiento = $mov AND estado =$est ";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>
