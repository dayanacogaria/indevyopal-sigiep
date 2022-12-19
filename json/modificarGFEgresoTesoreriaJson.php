<?php 
session_start();
require_once ('../Conexion/conexion.php');
####################################################################################################################################################################
# Creación: 14/02/2017
# Creado : Jhon Numpaque
####################################################################################################################################################################
# Modificaciones
####################################################################################################################################################################
# Modificado por : Jhon Numpaque
# Fecha 		 : 23/02/2017
# Descripción	 : Se cambio actualización para los campos de fecha y descripción
####################################################################################################################################################################
# Jhon Numpaque 15 | 02| 2017 
# Hora: 4:57
# Descripción : Se incluyo el campo cuentas por pagar el cual tiene los comprobantes tipo 4 y las clases cuentas que se traen son las 4,8,9. Y se valido si este
# tiene valor que realize el registro del detalle la cuenta por pagar seleccionada
####################################################################################################################################################################
# Captura de variables
$fecha = explode("/",$_POST['txtFecha']);
$fecha = "'"."$fecha[2]-$fecha[1]-$fecha[0]"."'";
$id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
####################################################################################################################################################################
# Validación de valores vacios o nulos
if(!empty($_POST['txtDescripcion'])){
	$descC = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
}else{
	$descC = 'NULL';
}
$tercero = $_POST['tercero'];
###################################################################################################################################################################
# Consulta de modificación
$sqlA="UPDATE gf_comprobante_cnt SET fecha=$fecha, descripcion=$descC , tercero = $tercero WHERE id_unico=$id";
$resultA = $mysqli->query($sqlA);
echo json_encode($resultA);
 ?>