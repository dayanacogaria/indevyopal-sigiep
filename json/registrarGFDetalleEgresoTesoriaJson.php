<?php  
session_start();
require_once('../Conexion/conexion.php');
####################################################################################################################################################
# Fecha de creación :14/02/2017
# Creado: Jhon Numpaque
####################################################################################################################################################
# Modificaciones
####################################################################################################################################################
# Modificado por : Jhon Numpaque
# Fecha 		 : 10:43 
# Descripción	 : Se cambio validación de registro de campo valor
####################################################################################################################################################
#action == ingresar
if($_POST['action']=='registrar'){
	################################################################################################################################################################
	# Captura de variables
	#
	###############################################################################################################################################################
	#
	$cuenta = '"'.$mysqli->real_escape_string(''.$_POST['cuenta'].'').'"';
	//$valor = '"'.$mysqli->real_escape_string(''.($_POST['valor']*-1).'').'"';
	$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
	$comprobanteE = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
	$fecha = explode("/",$_POST['fecha']);
	$fecha = "'"."$fecha[2]-$fecha[1]-$fecha[0]"."'";
	################################################################################################################################################################
	#Validación de campos vacios, si están vacios deben ser nulos
	#
	###############################################################################################################################################################
	#Tercero
	if(empty($_POST['tercero'])){
		$tercero = "2";
	}else{
		$tercero = '"'.$mysqli->real_escape_string(''.$_POST['tercero'].'').'"';
	}
	#Centro costo
	if(empty($_POST['centrocosto'])){
		$centrocosto = "12";
	}else{
		$centrocosto = '"'.$mysqli->real_escape_string(''.$_POST['centrocosto'].'').'"';
	}
	#Proyecto
	if(empty($_POST['proyecto'])){
		$proyecto = "2147483647";
	}else{
		$proyecto = '"'.$mysqli->real_escape_string(''.$_POST['proyecto'].'').'"';
	}
	#
	###############################################################################################################################################################
	# Consulta naturaleza
	#
	###############################################################################################################################################################
	#
	$sqlN = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $cuenta";
	$resultN = $mysqli->query($sqlN);
	$valN = mysqli_fetch_row($resultN);
	$naturaleza = $valN[0];
	#
	###############################################################################################################################################################
	# Validación de guardado de valor
	#
	###############################################################################################################################################################
	#
	if(!empty($_POST['valorD']) && empty($_POST['valorC'])) {
		if($naturaleza == 1) {
			$valor = $_POST['valorD'];
		}else{
			$valor = $_POST['valorD']*-1;
		}
	}
	if(!empty($_POST['valorC']) && empty($_POST['valorD'])) {
		if($naturaleza == 2) {
			$valor = $_POST['valorC'];
		}else{
			$valor = $_POST['valorC']*-1;
		}
	}
	################################################################################################################################################################
	# Consulta de insertado
	#
	###############################################################################################################################################################
	#
	$sqlI = "INSERT INTO gf_detalle_comprobante (fecha,descripcion,valor,comprobante,cuenta,naturaleza,tercero,proyecto,centrocosto) VALUES($fecha,$descripcion,$valor,$comprobanteE,$cuenta,$naturaleza,$tercero,$proyecto,$centrocosto)";
	$resultI = $mysqli->query($sqlI);
	echo json_encode($resultI);
}
#action == eliminar
if($_POST['action']=='eliminar'){
	################################################################################################################################################################
	# Captura de variables
	#
	###############################################################################################################################################################
	#
	$id = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
	################################################################################################################################################################
	# Consulta de eliminado
	#
	###############################################################################################################################################################
	#
	$sqlD = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $id";
	$resultD = $mysqli->query($sqlD);
	echo json_encode($resultD);
	#
}
#action == modificar
if($_POST['action']=='modificar'){
	################################################################################################################################################################
	# Captura de variables
	#
	###############################################################################################################################################################
	#
	$id = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
	$naturaleza = $mysqli->real_escape_string(''.$_POST['naturaleza'].'');
	################################################################################################################################################################
	# Validación para guardado de valor dependiendo de su naturaleza y de que los campos recibidos haya uno nulo o vacio, o que tenga valor 0	
	#
	###############################################################################################################################################################
	#
	if(!empty($_POST['txtCredito'])){
		if(empty($_POST['txtDebito']) || $_POST['txtDebito'] == 0){
			if($naturaleza == 1){
				$val = $_POST['txtCredito']*-1;
				$valor = '"'.$mysqli->real_escape_string(''.$val.'').'"';
			}else{
				$valor = '"'.$mysqli->real_escape_string(''.$_POST['txtCredito'].'').'"';
			}
		}
	}
	if(empty($_POST['txtCredito']) || $_POST['txtCredito'] == 0){
		if(!empty($_POST['txtDebito'])){
			if($naturaleza == 2) {
				$val = $_POST['txtDebito']*-1;
				$valor = '"'.$mysqli->real_escape_string(''.$val.'').'"';
			}else{
				$valor = '"'.$mysqli->real_escape_string(''.$_POST['txtDebito'].'').'"';
			}
		}
	}
	################################################################################################################################################################
	# Consulta de actualización
	#
	###############################################################################################################################################################
	#
	$sqlA = "UPDATE gf_detalle_comprobante SET valor = $valor  WHERE id_unico = $id";
	$resultA = $mysqli->query($sqlA);
	echo json_encode($resultA);
}
 ?>
	
