<?php
session_start();
require_once '../Conexion/conexion.php';
$id = $_POST['id'];
$concepto = $_POST['concepto'];
$cantidad = $_POST['cantidad'];
$valor = $_POST['valor'];
$iva = $_POST['iva']; 
$impoconsumo = $_POST['impoconsumo'];
$ajustepeso = $_POST['ajustepeso'];

$sql = "UPDATE gf_detalle_factura SET concepto_tarifa=$concepto,cantidad=$cantidad,valor=$valor,iva=$iva,impoconsumo=$impoconsumo,ajuste_peso=$ajustepeso WHERE id_unico = $id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>