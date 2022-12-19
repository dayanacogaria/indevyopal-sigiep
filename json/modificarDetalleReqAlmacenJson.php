<?php
require_once('../Conexion/conexion.php');
session_start();
//Captura de parámetros
$id           = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
$elemento     = '"'.$mysqli->real_escape_string(''.$_POST['planI'].'').'"';
$cantidad     = '"'.$mysqli->real_escape_string(''.$_POST['cantidad'].'').'"';
$valor      = '"'.$mysqli->real_escape_string(''.$_POST['valor'].'').'"';
$iva      = '"'.$mysqli->real_escape_string(''.$_POST['iva'].'').'"';
$insertSQL = "UPDATE gf_detalle_movimiento SET planmovimiento=$elemento,cantidad=$cantidad,valor=$valor,iva=$iva WHERE id_unico = $id";
$resultado = $mysqli->query($insertSQL);
echo json_encode($resultado);
?>
