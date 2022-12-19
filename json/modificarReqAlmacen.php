<?php
session_start();
require_once '../Conexion/conexion.php';
$id = $_POST['id'];
$tipo = $_POST['tipo'];
$numero =$_POST['numero'];            
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
$cc=$_POST['centroCosto'];
$proyecto=$_POST['proyecto'];
$dependecia=$_POST['dependencia'];
$responsable=$_POST['responsable'];
$rr=$_POST['rubropp'];
$pla=$_POST['plazoE'];
$unidad=$_POST['unidad'];
$luagar=$_POST['lugarE'];
$descripcion=$_POST['descripcion'];
$observacion=$_POST['observacion'];
$iva = $_POST['iva'];
$sql = "UPDATE gf_movimiento SET numero=$numero,tipomovimiento=$tipo,fecha=$fecha,centrocosto=$cc,proyecto=$proyecto,dependencia=$dependecia,tercero=$responsable,rubropptal=$rr,plazoentrega=$pla,unidadentrega=$unidad,lugarentrega=$luagar,observaciones='$observacion',descripcion='$descripcion',porcivaglobal=$iva WHERE id_unico=$id";
$result=$mysqli->query($sql);
echo json_encode($result);

