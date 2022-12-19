<?php
##########################################################################################
#
# creado por Nestor B 17/12/2018 
#
##########################################################################################
require_once ('./Conexion/conexion.php');
session_start();
$id = $_GET["id"];

$unom = null;
$sql = "SELECT id_unico, nombre, indicador_cierre FROM gpqr_clase  WHERE id_unico= '$id'";
$resultado = $mysqli->query($sql);

while($res = mysqli_fetch_row($resultado)){
		
		$unom = $res[2];
}

echo  json_encode($unom);
?>
