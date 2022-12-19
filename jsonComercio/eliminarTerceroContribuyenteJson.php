
<?php 
	require_once('../Conexion/conexion.php');
    session_start();
   
   //Captura de ID.
   $id = $_GET['id'];

   //Eliminación del registro en la tabla gf_perfil_tercero.
   $deleteSQL = "DELETE FROM gf_perfil_tercero WHERE tercero=$id";
   $resultado = $mysqli->query($deleteSQL);


  echo json_encode($resultado);
?>