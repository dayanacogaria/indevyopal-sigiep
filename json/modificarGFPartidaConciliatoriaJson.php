<?php 
	###########################################################################################
	#Creación 14-02-2017| 09:00| Jhon Numpaque
	#Archivo de modificación
	session_start();
	require_once('../Conexion/conexion.php');
	###########################################################################################
	################################################################################################################
	#Modificaciones
	# Fecha : 14/02/2017 | Jhon Numpaque
	# Descripción: Cambio en la ruta de guardado de documento
	#Captura de variables
	$id = '"'.$mysqli->real_escape_string(''.$_POST['txtIdPartida'].'').'"';
	$saldoE = '"'.$mysqli->real_escape_string(''.$_POST['txtSaldoE'].'').'"';
	if(!empty($_POST['txtDescripcion'])){
		$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
	}else{
		$descripcion = 'NULL';
	}
	if(!empty($_FILES['flArchivoC']['name'])){
		if(!empty($_POST['txtArchivoC'])){
			@unlink($_POST['txtArchivoC']);
		}
		$dir_subida = '../documentos/partidasConciliatorias/';
		$doc = $_FILES['flArchivoC']['tmp_name'];	
		$archivo = $dir_subida.basename($_FILES['flArchivoC']['name']);
		@move_uploaded_file($doc,$archivo);
		###########################################################################################
		#Consulta de actualización
		$sqlA = "UPDATE gf_partida_conciliatoria SET saldo_extracto=$saldoE,archivo_extracto='$archivo',descripcion=$descripcion WHERE id_unico = $id";
	}else{
		###########################################################################################
		#Consulta de actualización
		$sqlA = "UPDATE gf_partida_conciliatoria SET saldo_extracto=$saldoE,descripcion=$descripcion WHERE id_unico = $id";
	}
	$resultA = $mysqli->query($sqlA);
	echo json_encode($resultA);
 ?>