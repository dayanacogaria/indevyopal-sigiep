<?php
/**
 * funciones_cargue_predial.php
 *
 * Archivo en donde se encuentran varias funciones usadas para cargue de archivo
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @version $Id: funciones_cargue_predial.php 001 2017-05-19 Alexander Numpaque$
 **/


/**
 * start_rate
 *
 * Función de consulta para obtener el numero de la linea inicial para indiciar la lectura del archivo
 *
 * @author Alexander Numpaque
 * @package Subir  Prediral
 * @param int $clase Id de la clase enviada por post
 * @return int $line Linea para indicar en que linea se debe iniciar la lectura del archivo
 **/
function start_rate($clase) {
	require ('../Conexion/conexion.php');
	$line = 0;
	$sql = "SELECT linea_inicial FROM gs_clase_archivo WHERE id_unico = $clase";
	$result = $mysqli->query($sql);
	if($result == true) {
		$row = mysqli_fetch_row($result);
		$line = $row[0];
	}
	return $line;
}

/**
 * get_columns
 *
 * Función para obtener los numeros de las columnas y los id concepto_rubro_cuenta
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @param int $clase Id de la clase enviada por post
 * @return array $columns Array con el las columnas
 **/
function get_columns($clase, $param) {
	require ('../Conexion/conexion.php');
	$columns = array();
	$sql = "SELECT columna FROM gs_detalle_archivo WHERE clase = $clase AND parametrizacionanno = $param";
	$result = $mysqli->query($sql);
	if($result == true) {
		while ($row = mysqli_fetch_row($result)) {
			$columns[] = $row[0];
		}
	}
	return $columns;
}

/**
 * get_concepts
 *
 * Función para obtener los id de concepto rubro cuentas
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @param int $clase Id de la clase enviada por post
 * @return array $concepts Array con el id de concepto rubro cuenta
 **/
function get_concepts($clase) {
	require ('../Conexion/conexion.php');
	@session_start();
	$concepts = array();
    $_param   = $_SESSION['anno'];
	$sql      = "SELECT concepto_rubro_cuenta FROM gs_detalle_archivo WHERE clase = $clase AND parametrizacionanno = $_param";
	$result   = $mysqli->query($sql);
	if($result == true) {
		while ($row = mysqli_fetch_row($result)) {
			$concepts[] = $row[0];
		}
	}
	return $concepts;
}

/**
 * get_id_int_pptal
 *
 * Función para consultar en la base de datos para obtener el id de tipo comprobante pptal relacionado al cnt
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @param int $tipoCnt Id de tipo comprobante cnt envidado por post
 * @return int $int Id de tipo de comprobante pptal
 **/
function get_id_int_pptal($tipoCnt) {
	require ('../Conexion/conexion.php');
	$int    = 0;
	$sqlC   = "SELECT comprobante_pptal FROM gf_tipo_comprobante WHERE id_unico = $tipoCnt"; //Consulta para obtener el tipo de comprobante pptal
	$resultC = $mysqli->query($sqlC);
	if($resultC == true) {
		$rowC = mysqli_fetch_row($resultC);
		$int = $rowC[0];																	//Id de tipo comprobante pptal
	}
	return $int;
}

/**
 * get_id_bank
 *
 * Función para obtener el id de la cuenta relacionada a la cuenta bancaria
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @param int $bank Id de la cuenta bancaria enviada por post
 * @return int $id_bank Id de la cuenta relacionada de la cuenta bancaria
 **/
function get_id_bank($bank) {
	require ('../Conexion/conexion.php');
	$id_bank = 0;
	$sqlCB = "SELECT cuenta FROM gf_cuenta_bancaria WHERE id_unico = $bank";			//Consulta para obtener la cuenta que se relaciona al banco
	$resultCB = $mysqli->query($sqlCB);
	if($resultCB == true) {
		$rowCB = mysqli_fetch_row($resultCB);
		$id_bank = $rowCB[0];
	}
	return $id_bank;
}

/**
 * get_id_int_hom
 *
 * Función para obtener el tipo de comprobante cnt homologado
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @param int $id_int Id del tipo de comprobante cnt
 * @return int $int_hom Id de comprobante homologado
 **/
function get_id_int_hom($id_int) {
	require ('../Conexion/conexion.php');
	$int_hom = 0;
	$sql = "SELECT tipo_comp_hom FROM gf_tipo_comprobante WHERE id_unico = $id_int";
	$result = $mysqli->query($sql);
	if($result == true){
		$row = mysqli_fetch_row($result);
		$int_hom = $row[0];
	}
	return $int_hom;
}

/**
 * evalute_num
 *
 * Función para obtener el número formateado
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @param int|float $numero Número del archivo
 * @return int|float $numero Número formateado 2017000001
 **/
function evalute_num($numero) {
	if($numero <= 9) {															 //Validación para indicar el tamaño y darle formato al numero
		$numero = '201800000'.$numero;
	}elseif ($numero <= 99) {
		$numero = '20180000'.$numero;
	}elseif ($numero <= 999) {
		$numero = '2018000'.$numero;
	}elseif($numero <= 9999) {
		$numero = '201800'.$numero;
	}elseif($numero <= 99999) {
		$numero = '20180'.$numero;
	}elseif($numero <= 999999) {
		$numero = '2018'.$numero;
	}
	return $numero;
}

/**
 * get_concept_rbc
 *
 * Función para obtener el id de concepto rubro, y los id cuentas débito y crédito
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @param int $id_unico Id de concepto rubro cuenta
 * @return String|int $values Id de concepto rubro y los id de cuenta débito y crédito
 **/
function get_concept_rbc($id_unico) {
	require ('../Conexion/conexion.php');
	@session_start();
    $_param = $_SESSION['anno'];
	$values = "";
	$sql = "SELECT    crbc.concepto_rubro, crbc.cuenta_debito, crbc.cuenta_credito
			FROM      gf_concepto_rubro_cuenta as crbc
			LEFT JOIN gf_concepto_rubro        as crb  ON crbc.concepto_rubro = crb.id_unico
			LEFT JOIN gf_cuenta                as deb  ON crbc.cuenta_debito  = deb.id_unico
			LEFT JOIN gf_cuenta                as cre  ON crbc.cuenta_credito = cre.id_unico
			LEFT JOIN gf_concepto              as con  ON crb.concepto        = con.id_unico
			LEFT JOIN gf_rubro_pptal           as rbro ON crb.rubro           = rbro.id_unico
			WHERE     crbc.id_unico            = $id_unico
			AND       deb.parametrizacionanno  = $_param
			AND       cre.parametrizacionanno  = $_param
			AND       con.parametrizacionanno  = $_param
			AND       rbro.parametrizacionanno = $_param";
	$result = $mysqli->query($sql);
	if($result == true) {
		$row = mysqli_fetch_row($result);
		$values = "$row[0], $row[1], $row[2]";
	}
	return $values;
}

/**
 * get_rubro_fuente
 *
 * Función para obtener rubro fuente relacionada al rubro de concepto rubro
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @param int $id_unico Id de concepto rubro
 * @return int $rb_F Id de rubro fuente relacioando al rubro de concepto rubro
 */
function get_rubro_fuente($id_unico) {
	require ('../Conexion/conexion.php');
	@session_start();
	$rb_F   = "NULL";
    $_param = $_SESSION['anno'];
	$sql    = " SELECT    r_pp.id_unico
	            FROM      gf_concepto_rubro as c_rb
				LEFT JOIN gf_rubro_fuente   as r_pp ON c_rb.rubro    = r_pp.rubro
				LEFT JOIN gf_concepto       as con  ON c_rb.concepto = con.id_unico
				LEFT JOIN gf_rubro_pptal    as rbr  ON c_rb.rubro    = rbr.id_unico
				WHERE     c_rb.id_unico           = $id_unico
				AND       con.parametrizacionanno = $_param
				AND       rbr.parametrizacionanno = $_param";
	$result = $mysqli->query($sql);
	if($result == true) {
		$row = mysqli_fetch_row($result);
		$rb_F = $row[0];
	}
	return $rb_F;
}

/**
 * save_head_pptal
 *
 * Función para registrar comprobante pptal y retornar el id del registro
 *
 * @author Alexander Numpaque
 * @package Registrar Comprobante Cnt
 * @param int $numero Nùmero o consecutivo del comprobante
 * @param date $fecha Fecha del comprobante
 * @param date $fecha_v Fecha de vencimiento
 * @param String $descripcion Descripción del comprobante
 * @param int $param Parametrización año
 * @param int $clasecontrato Id de clase contrato
 * @param int $tipo_p Id del tipo de comprobante
 * @param int $tercero Id del tercero comprobante
 * @param int $estado Id del estado del comprobante
 * @param int $responsable Id del tercero o responsable
 * @return int $pptal Id del comprobante que se acaba de registrar
 **/
function save_head_pptal($numero, $fecha, $fecha_v, $descripcion, $param, $clasecontrato, $tipo_p, $tercero, $estado, $responsable) {
	require ('../Conexion/conexion.php');
	$pptal = 0;
	$sqlP = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, descripcion, parametrizacionanno, clasecontrato, tipocomprobante, tercero, estado, responsable) VALUES($numero, \"$fecha\", \"$fecha_v\", $descripcion, $param, $clasecontrato, $tipo_p, $tercero, $estado, $responsable);";
	$result = $mysqli->query($sqlP);
	if($result == true) {
		$sql = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante = $tipo_p";
		$result_U = $mysqli->query($sql);
		$row = mysqli_fetch_row($result_U);
		$pptal = $row[0];
	}
	return $pptal;
}

/**
 * save_head_cnt
 *
 * Registro de comprobante cnt y retornado del id del registro
 *
 * @author Alexander Numpaque
 * @package Registrar Comprobante Pptal
 * @param int|String $numero Número del comprobante
 * @param date $fecha Fecha del comprobante
 * @param int $tercero Id del tercero que se relaciona con el comprobante
 * @param String $descripcion Descripción del comprobante
 * @param int $estado Id de estado del comprobante
 * @param int $clasecontrato Id de clase contrato
 * @param int|String $numerocontrato Número de contrato puede ser int o String
 * @param int $compania Id de la compañia
 * @param int $param Id de la parametrización o vigencia
 * @param int $tipo_cnt Id del tipo de comprobante
 * @return int $cnt Id del ultimo registro es decir del comprobante que se acaba de registrar
 **/
function save_head_cnt($numero, $fecha, $tercero, $descripcion, $estado, $clasecontrato, $numerocontrato, $compania, $param, $tipo_cnt) {
	require ('../Conexion/conexion.php');
	$cnt = 0;
	$sqlC = "INSERT INTO gf_comprobante_cnt (numero, fecha, tercero, descripcion, estado, clasecontrato, numerocontrato, compania,  parametrizacionanno, tipocomprobante) VALUES($numero, \"$fecha\", $tercero, $descripcion, $estado, $clasecontrato, $numerocontrato, $compania, $param, $tipo_cnt);";
	$result = $mysqli->query($sqlC);
	if($result == true) {
		$sql = "SELECT MAX(id_unico) FROM gf_comprobante_cnt WHERE numero = $numero AND tipocomprobante = $tipo_cnt";
		$result_U = $mysqli->query($sql);
		$row = mysqli_fetch_row($result_U);
		$cnt = $row[0];
	}
	return $cnt;
}

/**
 * save_detail_pptal
 *
 * Función para registrar detalle pptal y retornar el id del registro
 * @author Alexander Numpaque
 * @package Registrar Comprobante Pptal
 * @param String $descripcion Descripción del comprobante
 * @param int|float $valor Valor del detalle
 * @param int $comprobante_pptal Id del comprobante al que se realciona el detalle
 * @param int $rubro_fuente Id de rubro fuente
 * @param int $con_rub Id de concepto rubro
 * @param int $tercero Id de tercero
 * @param int $proyecto Id de proyecto
 * @return int $detail; Id del detalle registrado
 **/
function save_detail_pptal($descripcion, $valor, $comprobante_pptal, $rubro_fuente, $con_rub, $tercero, $proyecto) {
	require ('../Conexion/conexion.php');
	$detail = 0;
	$sql_DP = "INSERT INTO gf_detalle_comprobante_pptal(descripcion, valor, comprobantepptal, rubrofuente, conceptorubro, tercero, proyecto) VALUES($descripcion, $valor, $comprobante_pptal, $rubro_fuente, $con_rub, $tercero, $proyecto);";
	$resultP = $mysqli->query($sql_DP);
	if($resultP == true) {
		$sql = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $comprobante_pptal";
		$result_U = $mysqli->query($sql);
		$row = mysqli_fetch_row($result_U);
		$detail = $row[0];
	}
	return $detail;
}

/**
 * save_detail_cnt
 *
 * Función para guardar los detalles de comprobante cnt
 *
 * @author Alexander Numpaque
 * @package Registrar Comprobante Pptal
 * @param date $fecha Fecha del comprobante
 * @param String $descripcion Descripción del comprobante
 * @param int|float $valor Valor del detalle
 * @param int $cuenta Id de cuenta
 * @param int $naturaleza Id de naturaleza
 * @param int $tercero Id de tercero
 * @param int $proyecto Id de proyecto
 * @param int $centrocosto Id de centro de consto
 * @param int $comprobante Id del comprobante al que se relacionara el detalle
 * @param int $detalle_pptal Id de detalle pptal
 * @return int $detail Id del detalle registrado
 **/
function save_detail_cnt($fecha, $descripcion, $valor, $cuenta, $naturaleza, $tercero, $proyecto, $centrocosto, $comprobante, $detalle_pptal, $detalleafectado) {
	try {
		require ('../Conexion/conexion.php');
		$detail = 0;
		$sql = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, valor, cuenta, naturaleza, tercero, proyecto, centrocosto, comprobante, detallecomprobantepptal,detalleafectado) VALUES(\"$fecha\", $descripcion, $valor, $cuenta, $naturaleza, $tercero, $proyecto, $centrocosto, $comprobante, $detalle_pptal, $detalleafectado);";
		$result = $mysqli->query($sql);
		if($result == true){
			$sql = "SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $comprobante";
			$result_U = $mysqli->query($sql);
			$row = mysqli_fetch_row($result_U);
			$detail = $row[0];
		}
		return $detail;
	} catch (Exception $e) {
		die($e->getMessage());
	}
}

/**
 * get_max_number
 *
 * Función para obtener el numero maximo de comprobante cnt
 *
 * @author Alexander Numpaque
 * @package Registrar Comprobante Pptal
 * @param int $type Id tipo de comprobante
 * @return int|String $number Número maximo con el tipo de comprobante
 **/
function get_max_number($type){
	require ('../Conexion/conexion.php');
	@session_start();
	$number = 0;
    $_param = $_SESSION['anno'];
	$sql = "SELECT MAX(numero)+1 FROM gf_comprobante_cnt WHERE tipocomprobante = $type AND parametrizacionanno = $_param";
	$result = $mysqli->query($sql);
	$filas = mysqli_num_rows($result);$row = mysqli_fetch_row($result);
	if($filas > 0 && !empty($row[0])){		
		$number = $row[0];
	} else {
		$sql = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $_param";
		$result = $mysqli->query($sql);
		$row = mysqli_fetch_row($result);
		$number = $row[0].'000001';
	}
	return $number;
}

/**
 * get_tercero
 *
 * Buscamos el tercero asignado en la tabla concepto rubno cuenta
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @param int $id_unico Id de concepto rubro cuenta
 * @return int $tercero Id de tercero asignado al valor enviado, y no hay valor retorna el tercero = 2 (Tercero : Varios)
 **/
function get_tercero($id_unico){
	require ('../Conexion/conexion.php');
	$tercero = 2;
	$sql = "SELECT tercero FROM gf_concepto_rubro_cuenta WHERE id_unico = $id_unico";
	$result = $mysqli->query($sql);
	$row = mysqli_fetch_row($result);
	if(!empty($row[0])) {
		$tercero = $row[0];
	}
	return $tercero;
}

/**
 * update_set_tercero_cnt
 *
 * Actualizamos el tercero en el comprobante
 *
 * @author Alexander Numpaque
 * @package Modificar Comprobante
 * @param int $tercero
 * @param int $id_unico
 * @return bool $update Si se actualizo el valor se retornara verdadero
 **/
function update_set_tercero_cnt($tercero,$id_unico) {
	require ('../Conexion/conexion.php');
	$updated = false;
	$sql = "UPDATE gf_comprobante_cnt SET tercero = $tercero WHERE id_unico = $id_unico";
	$result = $mysqli->query($sql);
	if($result == true){
		$updated = true;
	}
	return $updated;
}

/**
 * update_set_tercero_pptal
 *
 * Actualizamos el tercero en el comprobante pptal
 *
 * @author Alexander Numpaque
 * @package Modificar Comprobante Pptal
 * @param int $tercero
 * @param int $id_unico
 * @return bool $update Si se actualizo el valor se retornara verdadero
 **/
function update_set_tercero_pptal($tercero,$id_unico) {
	require ('../Conexion/conexion.php');
	$updated = false;
	$sql = "UPDATE gf_comprobante_pptal SET tercero = $tercero, responsable = $tercero WHERE id_unico = $id_unico";
	$result = $mysqli->query($sql);
	if($result == true){
		$updated = true;
	}
	return $updated;
}

/**
 * exist_opertion
 *
 * Función para encontrar si en la columna enviada hay algun operador matematico
 *
 * @author Alexander Numpaque
 * @package Subir Predial
 * @param int|String $expression Columna obtenidad por consulta
 * @return int $regex Valor en el regex
 */
function exist_opertion($expression) {
	$regex = preg_match_all("/[(\+\-\*\/)*][(\+\-\*\/)+]*[(\+\-\*\/)+]*[(\+\-\*\/)*]*/i", $expression, $matches);
	return $regex;
}

/**
 * get_operator
 *
 * Función para obtener el operador en la expresión
 *
 * @param String $expression columna que contiene operador 10-11
 * @return Array $oper array con el operador
 */
function get_operator($expression) {
	$operator = "+ - * /";
	$operator = explode(' ',$operator);
	$nc = count($operator);
	$i = 0;
	$pos = array();
	$oper = array();
	while ($i < $nc) {
		$ex = strpos("$expression",$operator[$i]);
		if($ex !== false) {
			$oper[] = $operator[$i];
		}
		$i++;
	}
	return $oper;
}

/**
 * get_naturaleza
 *
 * Función para obtener la naturaleza de la cuenta
 *
 * @param   int $cuenta id_unico de la cuenta
 * @return  int $row[0] naturaleza de la cuenta
 */
function get_naturaleza($cuenta){
	try {
		require ('../Conexion/conexion.php');
		$sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico =  $cuenta";
		$res = $mysqli->query($sql);
		$row = mysqli_fetch_row($res);
		return $row[0];
	} catch (Exception $e) {
		die($e->getMessage());
	}
}

/**
 * contar_detalles_cnt
 *
 * Función para contar los detalles existentes que se relacionan al comprobante
 *
 * @param   int $comprobante
 * @return  int $row[0] cantidad de detalles que se relacionan al comprobante
 */
function contar_detalles_cnt($comprobante){
	require('../Conexion/conexion.php');
	$sql = "SELECT COUNT(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $comprobante";
	$res = $mysqli->query($sql);
	$row = $res->fetch_row();
	return $row[0];
}

/**
 * contar_detalles_pptal
 *
 * Función para contar los detalles existentes que se relacionan al comprobante
 *
 * @param   int $comprobante
 * @return  int $row[0] cantidad de detalles que se relacionan al comprobante
 */
function contar_detalles_pptal($comprobante){
	require('../Conexion/conexion.php');
	$sql = "SELECT COUNT(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $comprobante";
	$res = $mysqli->query($sql);
	$row = $res->fetch_row();
	return $row[0];
}

/**
 * eliminar_comprobante_cnt
 *
 * Función para eliminar el comprobante
 *
 * @param   int $comprobante Id del comprobante
 */
function eliminar_comprobante_cnt($comprobante){
	require('../Conexion/conexion.php');
	$sql = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $comprobante";
	$res = $mysqli->query($sql);
}

/**
 * eliminar_comprobante_pptal
 *
 * Función para eliminar el comprobante
 *
 * @param   int $comprobante Id del comprobante
 */
function eliminar_comprobante_pptal($comprobante){
	require('../Conexion/conexion.php');
	$sql = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $comprobante";
	$res = $mysqli->query($sql);
}

/**
 * get_comprobante_cnt
 *
 * Función obtener el id del comprobante cnt
 *
 * @param  int    $tipo   Id del tipo de comprobante
 * @param  date   $fecha  fecha del comprobante
 * @return int    $id     Id del comprobante
 */
function get_comprobante_cnt($tipo, $fecha){
	require ('../Conexion/conexion.php');
	$id = 0;
	$sql = "SELECT id_unico FROM gf_comprobante_cnt
	        WHERE  tipocomprobante = $tipo
	        AND    fecha           = '$fecha'";
	$res = $mysqli->query($sql);
	if($res->num_rows > 0){
		$row = mysqli_fetch_row($res);
		$id  = $row[0];
	}
	return $id;
}

/**
 * get_comprobante_pptal
 *
 * Función obtener el id del comprobante pptal
 *
 * @param  int    $tipo   Id del tipo de comprobante
 * @param  date   $fecha  fecha del comprobante
 * @return int    $id     Id del comprobante
 */
function get_comprobante_pptal($tipo, $fecha){
	require ('../Conexion/conexion.php');
	$id = 0;
	$sql = "SELECT id_unico FROM gf_comprobante_pptal
	        WHERE  tipocomprobante = $tipo
	        AND    fecha           = '$fecha'";
	$res = $mysqli->query($sql);
	if($res->num_rows > 0){
		$row = mysqli_fetch_row($res);
		$id  = $row[0];
	}
	return $id;
}

/**
 * get_detallle_cnt
 *
 * Función obtener el id del detalle de comprobante CNT
 *
 * @param  int $cuenta       Id de la cuenta
 * @param  int $comprobante  Id de comprobante
 * @return int $id           Id de del detalle
 */
function get_detalle_cnt($cuenta, $comprobante){
	require '../Conexion/conexion.php';
	$id  = 0;
	$sql = "SELECT id_unico
	        FROM   gf_detalle_comprobante
	        WHERE  comprobante = $comprobante
	        AND    cuenta      = $cuenta";
	$res = $mysqli->query($sql);
	if($res->num_rows > 0){
		$row = mysqli_fetch_row($res);
		$id  = $row[0];
	}
	return $id;
}

/**
 * get_comprobante_pptal
 *
 * Función obtener el id del detalle de comprobante pptal
 *
 * @param  int $rubro_f      Id de rubro fuente
 * @param  int $con_rub      Id de concepto rubro
 * @param  int $comprobante  Id de comprobante al que pertence el detalle
 * @return int $id           Id de del detalle
 */
function get_detalle_pptal($rubro_f, $con_rub, $comprobante){
	require '../Conexion/conexion.php';
	$id  = 0;
	$sql = "SELECT id_unico
	        FROM   gf_detalle_comprobante_pptal
	        WHERE  rubrofuente      = $rubro_f
	        AND    conceptorubro    = $con_rub
	        AND    comprobantepptal = $comprobante";
	$res = $mysqli->query($sql);
	$row = mysqli_fetch_row($res);
	$id  = $row[0];
	return $id;
}

/**
 * agregar_valor_detalle_cnt
 *
 * Función para sumar y modificar el valor del detalle
 *
 * @param  int  $id_unico Id de rubro fuente
 * @param  int  $valor    Id de concepto rubro
 * @return bool $res_t    Valor booleano
 */
function agregar_valor_detalle_cnt($id_unico, $valor){
	require '../Conexion/conexion.php';
	$sql = "UPDATE gf_detalle_comprobante
	        SET    valor    = $valor
	        WHERE  id_unico = $id_unico";
	$res = $mysqli->query($sql);

	if($res == true){
		$res_t = true;
	}else{
		$res_t = false;
	}
	return $res_t;
}

/**
 * agregar_valor_detalle_pptal
 *
 * Función para sumar y modificar el valor del detalle
 *
 * @param  int  $id_unico Id de rubro fuente
 * @param  int  $valor    Id de concepto rubro
 * @return bool $res_t    Valor booleano
 */
 function agregar_valor_detalle_pptal($id_unico, $valor){
	require '../Conexion/conexion.php';
	$sql = "UPDATE gf_detalle_comprobante_pptal
	        SET    valor    = $valor
	        WHERE  id_unico = $id_unico";
	$res = $mysqli->query($sql);

	if($res == true){
		$res_t = true;
	}else{
		$res_t = false;
	}
	return $res_t;
}

/**
 * obtner_valor_detalle_cnt
 *
 * Función para obtner el valor de  detalle cnt
 *
 * @param  int       $id_unico Id de detalle cnt
 * @return int|float $valor    Valor del detalle
 */
function obtner_valor_detalle_cnt($id_unico){
	require '../Conexion/conexion.php';
	$valor = 0;
	$sql   = "SELECT valor FROM gf_detalle_comprobante WHERE id_unico = $id_unico";
	$res   = $mysqli->query($sql);
	if($res->num_rows > 0){
		$row   = mysqli_fetch_row($res);
		$valor = $row[0];
	}
	return $valor;
}

/**
 * obtner_valor_detalle_pptal
 *
 * Función para obtner el valor de  detalle pptal
 *
 * @param  int       $id_unico Id de detalle pptal
 * @return int|float $valor    Valor del detalle
 */
function obtner_valor_detalle_pptal($id_unico){
	require '../Conexion/conexion.php';
	$valor = 0;
	$sql   = "SELECT valor FROM gf_detalle_comprobante_pptal WHERE id_unico = $id_unico";
	$res   = $mysqli->query($sql);
	$row   = mysqli_fetch_row($res);
	$valor = $row[0];
	return $valor;
}

/**
 * obtner_detalle_cnt
 *
 * Función para obtner el valor de  detalle pptal
 *
 * @param  int $id_pptal    Id del detalle pptal
 * @param  int $comprobante Id del comprobante cnt
 * @return int $id          Id del detalle cnt
 */
function obtner_detalle_cnt($id_pptal, $comprobante){
	require '../Conexion/conexion.php';
	$id  = 0;
	$sql = "SELECT id_unico FROM gf_detalle_comprobante
	        WHERE  detallecomprobantepptal = $id_pptal
	        AND    comprobante             = $comprobante";
	$res = $mysqli->query($sql);
	if($res->num_rows > 0){
		$row = mysqli_fetch_row($res);
		$id  = $row[0];
	}
	return $id;
}
