<?php
    require_once('../Conexion/conexion.php');
    session_start();
//obtiene la informacion para la elminacion
   
   $id = $_GET['id'];
   $query = "DELETE FROM gf_clase_contrato WHERE Id_Unico = $id";
   // elimina el registro
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>