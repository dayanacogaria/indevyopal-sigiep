<?php

/* 
 * ************
 * ***Autor*****
 * **DANIEL.NC***
 * ***************
 */

require_once '../Conexion/conexion.php';
session_start();
$id = $_GET["id"];
$sql = "DELETE FROM gr_estrato_predio WHERE id_unico = $id";
$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>