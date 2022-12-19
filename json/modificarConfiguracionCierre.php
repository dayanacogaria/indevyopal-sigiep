<?php
session_start();
require_once '../Conexion/conexion.php';
$cuentaCerrar = '"'.$mysqli->real_escape_string(''.$_POST['cuentaCerrar'].'').'"';
$contraCuenta = '"'.$mysqli->real_escape_string(''.$_POST['contraCuenta'].'').'"';
if(!empty($_POST['tipocondicion'])){
	$tipoCondicion = '"'.$mysqli->real_escape_string(''.$_POST['tipocondicion'].'').'"';
}else{
	$tipoCondicion = 'NULL';
}
$id = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';

$sql = "UPDATE gf_configuracion_cierre_contable SET cuentacerrar=$cuentaCerrar,contracuenta=$contraCuenta,tipocondicion=$tipoCondicion WHERE id_unico=$id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>