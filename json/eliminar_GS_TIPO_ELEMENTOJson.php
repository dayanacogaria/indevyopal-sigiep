<?php
    require_once('../Conexion/conexion.php');
    session_start();

   //Captura de ID e instrucción SQL para su eliminación de la tabla gs_tipo_elemento.
   $id = $_GET['id'];
   $query = "DELETE FROM gs_tipo_elemento WHERE Id_Unico = $id";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>