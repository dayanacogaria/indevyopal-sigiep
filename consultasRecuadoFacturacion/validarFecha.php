<?php
session_start();
require_once '../Conexion/conexion.php';
#@param {date} fechaUno {"campo de fecha a guardar"}
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fechaUno = ''.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'';
$sql = "SELECT MAX(fecha_pago) FROM gp_pago";
$result = $mysqli->query($sql);
$row = $result->fetch_row();
$fechaBase = ($row[0]);
if($fechaUno>=$fechaBase){
    echo json_encode(true);
}else{
    echo json_encode(false);
}
?>