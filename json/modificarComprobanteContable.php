<?php
session_start();
require_once '../Conexion/conexion.php';
$id = $_POST['id'];
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$tipoComprobante = '"'.$_POST['tipoCmbnt'].'"';
$numeroComprobante = '"'.$_POST['numCmbnt'].'"';
$tercero = '"'.$_POST['tercero'].'"';
$centroCosto = '"'.$_POST['centroC'].'"';
$proyecto = '"'.$_POST['proycto'].'"';
if(!empty($_POST['claseCC'])){
	$claseContrato ='"'.$_POST['claseCC'].'"';
}else{
	$claseContrato ='NULL';
}
$numeroContrato = '"'.$_POST['numCont'].'"';
$estado = '"'.$_POST['estado'].'"';
$descripcion = '"'.$_POST['descpt'].'"';

$sql = "UPDATE gf_comprobante_cnt SET fecha=$fecha,descripcion=$descripcion,numerocontrato=$numeroContrato,tercero=$tercero,clasecontrato=$claseContrato WHERE id_unico = $id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>

