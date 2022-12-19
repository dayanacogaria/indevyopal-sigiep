<?php
session_start();
require_once '../Conexion/conexion.php';
$banco = $_POST['banco'];
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$descripcion = $_POST['descripcion'];
$valor = $_POST['valor'];
$valorEjecucion = $_POST['valorEjecucion'];
$comprobante = $_POST['comprobante'];
#Consulta de cuenta bancaria para tomar la cuenta, la naturaleza
$sqlcuentaBancaria = "SELECT ctb.cuenta,ct.naturaleza FROM gf_cuenta_bancaria ctb 
LEFT JOIN gf_cuenta ct ON ct.id_unico = ctb.cuenta
WHERE ctb.id_unico = $banco";
$rsCuentaB = $mysqli->query($sqlcuentaBancaria);
$row = mysqli_fetch_row($rsCuentaB);
if(empty($_POST['tercero'])){
    $tercero = '"2"';
}else{
    $tercero = '"'.$mysqli->real_escape_string(''.$_POST['tercero'].'').'"';
}
if(empty($_POST['proyecto'])){
    $proyecto = '"2147483647"';
}else{
    $proyecto = '"'.$mysqli->real_escape_string(''.$_POST['proyecto'].'').'"';
}
if(empty($_POST['centro'])){
    $centro = "12";
}else{
    $centro = '"'.$mysqli->real_escape_string(''.$_POST['centro'].'').'"';
}
$sql = "INSERT INTO gf_detalle_comprobante(fecha,descripcion,valor,valorejecucion,comprobante,cuenta,naturaleza,tercero,proyecto,centrocosto,detalleafectado) VALUES ($fecha,'$descripcion',$valor,$valorEjecucion,$comprobante,$row[0],$row[1],$tercero,$proyecto,$centro,NULL)";
$result = $mysqli->query($sql);
echo json_encode($result);
?>
