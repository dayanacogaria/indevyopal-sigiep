<?php
session_start();
require_once '../Conexion/conexion.php';
#Capturamos las variables enviadas por post
$id = $mysqli->real_escape_string(''.$_POST['id'].'');
$tmovimiento = '"'.$mysqli->real_escape_string(''.$_POST['tmovimiento'].'').'"';
$numeroM = '"'.$mysqli->real_escape_string(''.$_POST['numeroM'].'').'"';
$fechaT = $mysqli->real_escape_string(''.$_POST['fecha'].'');
$valorF = explode("/", $fechaT);
$fecha =   $valorF[2].'-'.$valorF[1].'-'.$valorF[0];
$centroCosto = '"'.$mysqli->real_escape_string(''.$_POST['centroCosto'].'').'"';
$proyecto = '"'.$mysqli->real_escape_string(''.$_POST['proyecto'].'').'"';
$dependecia = '"'.$mysqli->real_escape_string(''.$_POST['dependecia'].'').'"';
$responsable = '"'.$mysqli->real_escape_string(''.$_POST['responsable'].'').'"';
$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
$observacion = '"'.$mysqli->real_escape_string(''.$_POST['observacion'].'').'"';

$sql ="update gf_movimiento set tipomovimiento=$tmovimiento,numero=$numeroM,fecha='$fecha',centrocosto=$centroCosto,proyecto=$proyecto,dependencia=$dependecia,tercero2=$responsable,descripcion=$descripcion,observaciones=$observacion where id_unico = $id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>

