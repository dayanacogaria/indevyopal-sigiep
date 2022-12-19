<?php
##########################################################################################
#
# creado por Nestor B 15/12/2018 
#
##########################################################################################
require_once ('./Conexion/conexion.php');
session_start();
$id = $_GET["id"];

$unom = null;
$sql = "SELECT f.id_unico, f.numero_factura FROM gp_factura f WHERE f.unidad_vivienda_servicio = '$id' ORDER BY f.id_unico LIMIT 6";
$resultado = $mysqli->query($sql);

while ($row = mysqli_fetch_row($resultado))
{
   	echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
} 


?>
