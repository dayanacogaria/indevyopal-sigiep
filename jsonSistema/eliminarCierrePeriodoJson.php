<?php 
##################MODIFICACIONES#########################
#04/05/2017 | Erica G. | Creación Archivo
######################################################## 
require_once '../Conexion/conexion.php';
session_start();
$id = $_POST['id'];
$sql = "DELETE FROM gs_cierre_periodo WHERE id_unico = $id";
$resultado = $mysqli->query($sql);
echo json_encode($resultado);
 ?>
