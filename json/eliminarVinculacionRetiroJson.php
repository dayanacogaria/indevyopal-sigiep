<?php
require_once '../Conexion/conexion.php';
session_start();
$id = $_GET["id"];

$sql_vin = "SELECT vinculacionretiro,empleado FROM gn_vinculacion_retiro WHERE id_unico = $id";
$res_vin = $mysqli->query($sql_vin);
$row_vin = mysqli_fetch_row($res_vin);

$sql = "DELETE FROM gn_vinculacion_retiro WHERE id_unico = $id";
$resultado = $mysqli->query($sql);

$sql_up = "UPDATE gn_vinculacion_retiro set vinculacionretiro = NULL  WHERE id_unico = $row_vin[0]";
$resultado = $mysqli->query($sql_up);

$sql_up = "UPDATE gn_empleado set estado = 1  where id_unico = $row_vin[1]";
$resultado = $mysqli->query($sql_up);

echo json_encode($resultado);
?>