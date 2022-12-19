<?php
/**
 * funciones_cargue_facturacion.php
 *
 * Archivo en donde se encuentran varias funciones usadas para cargue de archivo
 *
 * @author Alexander Numpaque
 * @package Subir Facturación
 * @version $Id: funciones_cargue_facturacion.php 001 2017-05-24 Alexander Numpaque$
 **/

/**
 * get_id_type_cnt
 *
 * @author Alexander Numpaque
 * @package Subir Facturación
 * @param int $id_tfactura Id de tipo factura
 * @return int $id_type Id de tipo comprobante cnt
 **/
function get_id_type_cnt($id_tfactura) {
	require ('../Conexion/conexion.php');
	$id_type = 0;
	$sql = "SELECT tipo_comprobante FROM gp_tipo_factura WHERE id_unico = $id_tfactura";
	$result = $mysqli->query($sql);
	if($result == true) {
		$row = mysqli_fetch_row($result);
		$id_type = $row[0];
	}
	return $id_type;
}

/**
 * get_max_acount
 *
 * Función para obtener el numeor maximo por el tipo de comprobante
 *
 * @author Alexander Numpaque
 * @package Subir Facturación
 * @param int $id_tfactura Id de tipo factura
 * @return String Formateo con el ultimo numero registrado, si no envia el primer valor con el formato YYYY000001
 **/
function get_max_acount($id_tfactura, $param) {
	require ('../Conexion/conexion.php');
	$number = 0;
	$sql = "SELECT MAX(numero_factura) 
	FROM gp_factura WHERE tipofactura = $id_tfactura AND parametrizacionanno = $param";
	$result = $mysqli->query($sql);
	$filas = mysqli_num_rows($result);
	if($filas > 0){
		$row = mysqli_fetch_row($result);
		$number = $row[0];
	}else{
		$sql = "SELECT anno  
		FROM gf_parametrizacion_anno 
		WHERE id_unico = $param";
		$result = $mysqli->query($sql);
		$filas = mysqli_num_rows($result);
		if($filas > 0){
			$row = mysqli_fetch_row($result);
			$numbera = $row[0];
		} else {
			$numbera = date('Y');
		}
		$number = $numbera.'000001';

	}
	return $number;
}

/**
 * get_tercero_usuario
 *
 * Obtenemos el id del tercero por medio del nombre del usuario
 *
 * @author Alexander Numpaque
 * @package Subir Facturación
 * @param String $usuario nombre del usuario registrado en la base de datos
 * @return int $tercero Id de tercero
 **/
function get_tercero_usuario($usuario) {
	require ('../Conexion/conexion.php');
	$tercero = 0;
	$sqlT = "SELECT us.tercero FROM gs_usuario us WHERE us.usuario = \"$usuario\"";
	$resultT = $mysqli->query($sqlT);
	$filas = mysqli_num_rows($resultT);
	if($filas > 0){
		$row = mysqli_fetch_row($resultT);
		$tercero = $row[0];
	}
	return $tercero;
}

/**
 * save_head_fac
 *
 * Función para guardar la cabeza de la factura y retorna el id del registro
 *
 * @author Alexander Numpaque
 * @package Guardar Facturación
 * @param date $fecha Fecha de factura
 * @param int $tipo_factura Tipo de factura
 * @param int $numero_factura Número de factura
 * @param int $centrocosto Id de centro de costo
 * @param String $descripcion Descripción
 * @param int $estado Id de estado
 * @param int $tercero Id de tercero
 * @param data $fecha_v Fecha de vencimiento
 * @return int $factura Id de la factura registrada
 **/
function save_head_fac($fecha, $tipo_factura, $numero_factura, $centrocosto, $descripcion, $estado, $tercero, $fecha_v, $param){
	require ('../Conexion/conexion.php');
	$factura = 0;
	$sql = "INSERT INTO gp_factura (fecha_factura, tipofactura, numero_factura, centrocosto, descripcion, estado_factura, tercero, fecha_vencimiento, parametrizacionanno) VALUES (\"$fecha\", $tipo_factura, $numero_factura, $centrocosto, $descripcion, $estado, $tercero, \"$fecha_v\", $param)";
	$result = $mysqli->query($sql);
	if($result == true) {
		$sqlTD = "SELECT MAX(id_unico) FROM gp_factura WHERE numero_factura = $numero_factura AND tipofactura = $tipo_factura";
		$resultTD = $mysqli->query($sqlTD);
		$row = mysqli_fetch_row($resultTD);
		$factura = $row[0];
	}
	return $factura;
}

/**
 * save_detail_fac
 *
 * Función para registrar la factura
 *
 * @author Alexander Numpaque
 * @package Guardar Facturación
 * @param int $factura Id de la factura
 * @param int $concepto Id concepto
 * @param int $valor Id de
 * @param int $cantidad
 * @param int $iva
 * @param int $ajuste_peso
 * @param int $impo
 * @param int $valor_A
 * @param int $detalle_c
 * @return int
 **/
function  save_detail_fac($factura, $concepto, $valor, $cantidad, $iva, $ajuste_peso, $impo, $valor_A, $detalle_c) {
	require ('../Conexion/conexion.php');
	$detail  = 0;
	$sql = "INSERT INTO gp_detalle_factura (factura, concepto_tarifa, valor, cantidad, iva, ajuste_peso, impoconsumo, valor_total_ajustado, detallecomprobante) VALUES ($factura, $concepto, $valor, $cantidad, $iva, $ajuste_peso, $impo, $valor_A, $detalle_c)";
	$result = $mysqli->query($sql);
	if($result == true) {
		$sqlDF = "SELECT MAX(id_unico) FROM gp_detalle_factura WHERE factura = $factura";
		$resultDF = $mysqli->query($sqlDF);
		$row = mysqli_fetch_row($resultDF);
		$detail = $row[0];
	}
	return $detail;
}

/**
 * get_concept_fin
 *
 * Función para obtener el concepto relacionado a concepto rubro cuenta
 *
 * @author Alexander Numpaque
 * @package Subir Facturación
 * @param int $concep_rb Id de concepto rubro cuenta
 * @return int Id de concepto
 **/
function get_concept_fin($concep_rb) {
	require ('../Conexion/conexion.php');
	$concept = 0;
	$sql = "SELECT cr.concepto FROM gf_concepto_rubro_cuenta crb
			LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = crb.concepto_rubro
			WHERE crb.id_unico = $concep_rb";
	$result = $mysqli->query($sql);
	$filas = mysqli_num_rows($result);
	if($filas > 0) {
		$row = mysqli_fetch_row($result);
		$concept = $row[0];
	}
	return $concept;
}

/**
 * get_id_concept_fat
 *
 * Función para obtener el id del concepto relacionado en gp_concepto
 *
 * @author Alexander Numpaque
 * @package Subir Facturación
 * @param int $concept Id del concepto en concepto rubro
 * @return int $concept Id del concepto ne gp_concept
 */
function get_id_concept_fat($conceptoRubro,$rubroFuente, $param) {
	require ('../Conexion/conexion.php');
	$concep = 0;
	$sql = "SELECT concepto FROM gp_configuracion_concepto WHERE concepto_rubro = $conceptoRubro AND rubro_fuente = $rubroFuente AND parametrizacionanno = $param";
	$result = $mysqli->query($sql);
	$filas = mysqli_num_rows($result);
	if($filas > 0){
		$row = mysqli_fetch_row($result);
		$concep = $row[0];
	}
	return $concep;
}


