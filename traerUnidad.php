<?php
##########################################################################################
#
# creado por Nestor B 04/04/2017 
#
##########################################################################################
require_once ('./Conexion/conexion.php');
session_start();
$id = $_GET["id"];

$unom = null;
$sql = "SELECT c.id_unico, c.descripcion, u.id_unico, u.nombre 
                                     FROM gn_concepto c
                                     LEFT JOIN gn_unidad_medida_con u ON c.unidadmedida = u.id_unico
                                     WHERE c.id_unico = $id";
$resultado = $mysqli->query($sql);

while($res = mysqli_fetch_row($resultado)){
		$unom = $res[3];
}

echo  json_encode($unom);
?>
