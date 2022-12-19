<?php
    require_once('../Conexion/conexion.php');
    session_start();

   $id1 = $_GET['id1'];
   $id2 = $_GET['id2'];
   
   $query = "DELETE FROM gp_tercero_predio WHERE tercero = $id1 AND predio= $id2 ";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>
