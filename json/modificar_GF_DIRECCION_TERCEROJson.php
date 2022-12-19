<?php
require_once '../Conexion/conexion.php';
session_start();
$id = $_GET['id'];
$direccion=$_GET['direccion1'];
$tipo = $_GET['tipo'];
$ciu = $_GET['ciudad'];
$terc = $_GET['tercero'];

$sql = "UPDATE gf_direccion SET direccion=$direccion,tipo_direccion=$tipo, "
        . "ciudad_direccion=$ciu, tercero=$terc "
        . "WHERE id_unico='$id'";

$result = $mysqli->query($sql);
echo json_encode($result);
?>