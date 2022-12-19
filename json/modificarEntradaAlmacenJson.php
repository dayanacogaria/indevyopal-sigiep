<?php
session_start();
require_once '../Conexion/conexion.php';
$id=$_POST['id'];
$movmiento=$_POST['movimiento'];
$numero = $_POST['numero'];
$tercero = $_POST['tercero'];
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =   '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$depencia = $_POST['dependecia'];
$responsable = $_POST['responsable'];
$centroCosto = $_POST['centroCosto'];
$rubbro = $_POST['rubroPP'];
$plazoE = $_POST['plazoE'];
$unidadP = $_POST['unidadP'];
$proyecto = $_POST['proyecto'];
$lugarE = $_POST['lugarE'];
$iva = $_POST['iva'];
if(!empty($_POST['descripcion'])){
    $descripcion = $_POST['descripcion'];
}else{
    $descripcion = 'NULL';
}
if(!empty($_POST['observacion'])){
    $observacion = $_POST['observacion'];
}else{
    $observacion = 'NULL';
}

$sql = "UPDATE gf_movimiento SET tipomovimiento=$movmiento,numero=$numero,tercero2=$tercero,fecha=$fecha,dependencia=$depencia,tercero=$responsable,centrocosto=$centroCosto,rubropptal=$rubbro,plazoentrega=$plazoE,unidadentrega=$unidadP,proyecto=$proyecto,lugarentrega=$lugarE,porcivaglobal=$iva WHERE id_unico=$id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>

