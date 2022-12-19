
<?php 
	require_once('../Conexion/conexion.php');
    session_start();
   
   //Captura de ID.
   $id = $_GET['id'];

   //Eliminación del registro en la tabla gf_perfil_tercero.
   $deleteSQL = "DELETE FROM gf_perfil_tercero WHERE gf_perfil_tercero.perfil = 13 AND gf_perfil_tercero.tercero = $id";
   $resultado = $mysqli->query($deleteSQL);

   //Eliminación del registro en la tabla gf_tercero.
   $deleteSQL = "DELETE FROM gf_tercero WHERE Id_Unico = $id";
   $resultado = $mysqli->query($deleteSQL);
   
  echo json_encode($resultado);
?>