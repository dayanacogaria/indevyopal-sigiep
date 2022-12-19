<?php
session_start();
require_once '../Conexion/conexion.php';
$detalle = $_POST['detalle'];
$cantidad = $_POST['cantidad'];
$producto = $_POST['producto'];
$query = "SELECT cantidad FROM gf_detalle_movimiento where id_unico=$detalle";
$result1 = $mysqli->query($query);
$x = $result1->fetch_row();

$sqlinsert="insert into gf_movimiento_producto(detallemovimiento,producto) values ($detalle,$producto)";
$result3 = $mysqli->query($sqlinsert);
?>

