<?php
####################################################################################################################################################
# Fecha de creación :13/02/2017
# Creado: Jhon Numpaque
################################################################################################################################
# Modificaciones
# 14/02/2017 | Jhon Numpaque
# Descripción: Se incluyo campo valor 
session_start();
require_once ('../Conexion/conexion.php');
#action == ingresar
if($_POST['action'] == 'ingresar'){
	################################################################################################################################################
	# Captura de variables
	$idPartida = '"'.$mysqli->real_escape_string(''.$_POST['idPartida'].'').'"';
	$tipoPartida = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoPartida'].'').'"';
	$fecha = explode("/",$_POST['txtFechaP']);
	$fecha = '"'.$fecha[2].'-'.$fecha[1].'-'.$fecha[0].'"';
	$concilado = '"'.$mysqli->real_escape_string(''.$_POST['optConciliado'].'').'"';
	$tipodoc = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoDoc'].'').'"';
	$numeroDoc = '"'.$mysqli->real_escape_string(''.$_POST['txtNumDoc'].'').'"';
	$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
	$txtValor = '"'.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'"';
	$estado = "1";

	if(!empty($_POST['txtFechaPCon'])){
		$fechaC = explode("/",$_POST['txtFechaPCon']);
		$fechaC = '"'.$fechaC[2].'-'.$fechaC[1].'-'.$fechaC[0].'"';
	}else{		
		$fechaC = 'NULL';
	}
	################################################################################################################################################
	#Consulta de insertado
	$sqlI = "INSERT INTO gf_detalle_partida(id_partida,tipo_partida,fecha_partida,conciliado,tipo_documento,numero_documento,descripcion_detalle_partida,valor,estado,fecha_conciliacion) VALUES($idPartida,$tipoPartida,$fecha,$concilado,$tipodoc,$numeroDoc,$descripcion,$txtValor,$estado,$fechaC)";
	$resultI = $mysqli->query($sqlI);
	echo json_encode($resultI);
}
#action == eliminar
if($_POST['action'] == 'eliminar'){
	################################################################################################################################################
	# Captura de variables
	$idDetalle = $_POST['id'];
	################################################################################################################################################
	#Consulta de eliminado
	$sqlDel = "DELETE FROM gf_detalle_partida WHERE id_unico = $idDetalle";
	$resultDel = $mysqli->query($sqlDel);
	echo json_encode($resultDel);
}
#action == editar
if($_POST['action'] == 'editar'){
	################################################################################################################################################
	# Captura de variables
	$id = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
	$sltTipoPtda = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoPtda'].'').'"';
	$fecha = explode("/",$_POST['txtFechaP']);	
	$fecha = '"'.$fecha[2].'-'.$fecha[1].'-'.$fecha[0].'"';
	$sltTipoDocP = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoDocP'].'').'"';
	$txtNumDocP = '"'.$mysqli->real_escape_string(''.$_POST['txtNumDocP'].'').'"';
	$txtDescripcionP = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcionP'].'').'"';
	$txtValor = '"'.$mysqli->real_escape_string(''.$_POST['txtValorP'].'').'"';
	
	if(!empty($_POST['txtFechaPCon'])){
		$fechaC = explode("/",$_POST['txtFechaPCon']);
		$fechaC = '"'.$fechaC[2].'-'.$fechaC[1].'-'.$fechaC[0].'"';
	}else{
		$fechaC = 'NULL';
	}
	
	$concilado = '"'.$mysqli->real_escape_string(''.$_POST['optConciliado'].'').'"';
	################################################################################################################################################
	# Consulta de actualización
	$sqlA = "UPDATE gf_detalle_partida SET tipo_partida = $sltTipoPtda, fecha_partida = $fecha, tipo_documento = $sltTipoDocP, numero_documento = $txtNumDocP, descripcion_detalle_partida = $txtDescripcionP, valor=$txtValor, conciliado = $concilado, fecha_conciliacion = $fechaC WHERE id_unico=$id";
	$resultA = $mysqli->query($sqlA);
	echo json_encode($resultA);
}
 ?>
