<?php 
require_once('../Conexion/conexion.php');
session_start();
   
//Captura de ID.
$id = $_GET['id'];

//Eliminación del registro en la tabla gf_perfil_tercero.
$deleteSQL = "DELETE FROM gf_perfil_tercero WHERE Tercero = $id and perfil=3";
$resultado = $mysqli->query($deleteSQL);

$sql="select perfil from gf_perfil_tercero where tercero=$id";
$result=$mysqli->query($sql);
$cantidad = mysqli_num_rows($result);
if($cantidad==0 || empty($cantidad)){
  //Eliminación del registro en la tabla gf_tercero.
  $deleteSQL = "DELETE FROM gf_tercero WHERE Id_Unico = $id";
  $resultado = $mysqli->query($deleteSQL);
}

  echo json_encode($resultado);
?>