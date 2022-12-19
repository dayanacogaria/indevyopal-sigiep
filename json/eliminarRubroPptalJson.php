<?php
#Llamamos a la clase conexión
require_once ('../Conexion/conexion.php');
#iniciamos la sesion
session_start();
#Capturamos la variable
$id = $_GET["id"];
#Consulta de eliminado
$sql = "DELETE FROM gf_rubro_pptal WHERE id_unico = $id";
#Cargamos la consulta a la variable resultado
$resultado = $mysqli->query($sql);
#Imprimimos el valor devuelto como un json
echo json_encode($resultado);
?>