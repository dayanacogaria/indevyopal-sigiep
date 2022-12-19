<?php
session_start();
require_once '../Conexion/conexion.php';
$id = $_POST['id'];
$obligatorio = '"'.$mysqli->real_escape_string(''.$_POST['obligatorio'].'').'"';
$autogenerado = '"'.$mysqli->real_escape_string(''.$_POST['autogenerado'].'').'"';
$sql = "update gf_ficha_inventario set obligatorio=$obligatorio,autogenerado=$autogenerado where id_unico = $id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>

