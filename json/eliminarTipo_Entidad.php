<?php
    require_once('../Conexion/conexion.php');
    session_start();


//obtiene los datos que se van a eliminar   
   $id = $_GET['id'];
   //elimina en la base de datos
   $query = "DELETE FROM gf_tipo_entidad WHERE Id_Unico = $id";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>