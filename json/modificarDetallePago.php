<?php
session_start();
require '../Conexion/conexion.php';
$id = $_POST['id'];
$valor = $_POST['valor'];

$sql = "UPDATE gp_detalle_pago SET valor=$valor WHERE id_unico=$id";
$result = $mysqli->query($sql); 
echo json_encode($result);
?>

