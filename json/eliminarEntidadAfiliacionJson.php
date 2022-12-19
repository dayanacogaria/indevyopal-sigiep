
<?php 
	require_once('../Conexion/conexion.php');
    session_start();
   
   //Captura de ID.
   $id = $_GET['id'];
   ##Buscar si existe mas de un perfil para el tercero 
   $perfil = "SELECT * FROM gf_perfil_tercero WHERE tercero = $id";
   $perfil = $mysqli->query($perfil);
   if(mysqli_num_rows($perfil)>1){
       //Eliminación del registro en la tabla gf_perfil_tercero.
   $deleteSQL = "DELETE FROM gf_perfil_tercero WHERE tercero = $id where perfil = 11";
   $resultado = $mysqli->query($deleteSQL);
   } else {
   //Eliminación del registro en la tabla gf_perfil_tercero.
   $deleteSQL = "DELETE FROM gf_perfil_tercero WHERE tercero = $id";
   $resultado = $mysqli->query($deleteSQL);

   //Eliminación del registro en la tabla gf_tercero.
   $deleteSQL = "DELETE FROM gf_tercero WHERE Id_Unico = $id";
   $resultado = $mysqli->query($deleteSQL);
   }
  echo json_encode($resultado);
?>