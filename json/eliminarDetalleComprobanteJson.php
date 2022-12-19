<?php
session_start();
require_once ('../Conexion/conexion.php');
$id = $_GET['id'];
$sql = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $id";
$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>
