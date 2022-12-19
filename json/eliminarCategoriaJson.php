<?php
require_once '../Conexion/conexion.php';
require_once('../jsonPptal/gs_auditoria_acciones_nomina.php');
session_start();
$id = $_GET["id"];
$validaCaTer="SELECT * FROM gn_tercero_categoria WHERE categoria=$id";
$resultadoCaTer = $mysqli->query($validaCaTer);
if (mysqli_num_rows($resultadoCaTer)>0) {
}else{
   $elm = eliminarCategoria($id);
}
$sql = "DELETE FROM gn_categoria WHERE id_unico = $id";
$resultado = $mysqli->query($sql);
echo json_encode($resultado);
?>