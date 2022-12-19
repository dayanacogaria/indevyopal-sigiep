<?php 
	require_once('../Conexion/conexion.php');
    session_start();
   
   //Captura de ID e instrucción SQL para su eliminación de la tabla gf_perfil_tercero.
   $id = $_GET['id'];
   $query = "DELETE FROM gf_telefono WHERE id_unico = $id";
   $resultado = $mysqli->query($query);
   
      //Eliminación del registro en la tabla gf_tercero.
   $deleteSQL = "DELETE FROM gf_tercero WHERE Id_Unico = $id";
   $resultado = $mysqli->query($deleteSQL);

  echo json_encode($resultado);
?>