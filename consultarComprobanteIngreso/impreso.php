<?php
session_start();
require_once '../Conexion/conexion.php';
$numeroComprobante = $_POST['numero'];
$sql = "UPDATE gf_comprobante_cnt SET estado=2 WHERE numero= $numeroComprobante";
$result = $mysqli->query($sql);
echo json_encode($result);
?>