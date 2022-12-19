<?php
###############################################################################################################################################################
# Modificaciones
#
###############################################################################################################################################################
# Fecha de Modificación : 24/08/2017
# Modificado por 		: Alexander Numpaque
# Descripción			: Se agrego validación para actualizar los valores debido a que envia NaN en algunos y no permitia validar, pues se valido que cuando
# lo envie lo convierta a 0, y tambien se cambio la consulta para obtener los detalles cuando la consulta solo tiene detalle debito y credito
###############################################################################################################################################################
# Fecha de Modificación : 23/06/2017
# Modificado por 		: Jhon Numpaque
# Descripción			: Se cambio la consulta modificar en cadena y se agrego validación para evitar errores de modificación
###############################################################################################################################################################
# Fecha de Modificación : 01/03/2017
# Modificado por 		: Jhon Numpaque
# Descripción			: Se agrego validación para modificado en cascada
###############################################################################################################################################################
# Fecha de Modificación : 01/03/2017
# Modificado por 		: Jhon Numpaque
# Hora de Modificación  : 10 : 30
# Descripción			: Se agrego modificacion para dos detalles que cuya cuenta es debtio y tienen valores de impoconsumo e via
###############################################################################################################################################################
session_start();
require_once '../Conexion/conexion.php';
###############################################################################################################################################################
# Captura de variables
#
###############################################################################################################################################################
$id              = $mysqli->real_escape_string(''.$_POST['id'].'');
$concepto        = $mysqli->real_escape_string(''.$_POST['concepto'].'');
$cantidad        = $mysqli->real_escape_string(''.$_POST['cantidad'].'');
$valor           = $mysqli->real_escape_string(''.$_POST['valor'].'');
if($_POST['iva'] == "NaN"){
	$iva         = 0;
}else{
	$iva         = $mysqli->real_escape_string(''.$_POST['iva'].'');
}

if($_POST['impoconsumo'] == "NaN"){
	$impoconsumo = 0;
}else{
	$impoconsumo = $mysqli->real_escape_string(''.$_POST['impoconsumo'].'');
}

if($_POST['ajustepeso'] == "NaN"){
	$ajustepeso  = 0;
}else{
	$ajustepeso  = $mysqli->real_escape_string(''.$_POST['ajustepeso'].'');
}
$valorAjuste     = $mysqli->real_escape_string(''.$_POST['valorAjuste'].'');
###############################################################################################################################################################
# Consulta para validar si el detalle factura tiene detalle comprobante
#
###############################################################################################################################################################
$sqlA = "SELECT detallecomprobante FROM gp_detalle_factura WHERE id_unico = $id";
$resultA = $mysqli->query($sqlA);
$rowA = mysqli_fetch_row($resultA);
###############################################################################################################################################################
# Validación si exsite detalle comprobante
#
###############################################################################################################################################################
if(!empty($rowA[0])){
	###########################################################################################################################################################
	# Validamos el campos iva
	###########################################################################################################################################################
	if(!empty($iva) || $iva!='0' || $iva!='0.00'){
		###########################################################################################################################################################
		# Consulta de busqueda de detalles
		#
		###########################################################################################################################################################
		$sqlB = "SELECT dtc.detallecomprobantepptal 'detalle_pp', dtf.detallecomprobante 'cuenta_debito', dta.id_unico 'cuenta_credito', dti.id_unico 'cuenta_iva_1', dtv.id_unico 'cuenta_iva_2', dtm.id_unico 'cuenta_impo_1', dto.id_unico 'cuenta_impo_2'
                 FROM gp_detalle_factura dtf
                 LEFT JOIN gf_detalle_comprobante dtc ON dtf.detallecomprobante = dtc.id_unico
                 LEFT JOIN gf_detalle_comprobante dta ON dta.detalleafectado = dtc.id_unico
                 LEFT JOIN gf_detalle_comprobante dti ON dti.detalleafectado = dta.id_unico
                 LEFT JOIN gf_detalle_comprobante dtv ON dtv.detalleafectado = dti.id_unico
                 LEFT JOIN gf_detalle_comprobante dtm ON dtm.detalleafectado = dtv.id_unico
                 LEFT JOIN gf_detalle_comprobante dto ON dto.detalleafectado = dtm.id_unico
                 WHERE dtf.id_unico = $id";
		$resultB = $mysqli->query($sqlB);
		$row = mysqli_fetch_row($resultB);
		###########################################################################################################################################################
		# Consulta de actualización de valores de presupuestal
		#
		###########################################################################################################################################################
		$valorOPe = ($valor*$cantidad)+$ajustepeso;
		$sqlP = "UPDATE gf_detalle_comprobante_pptal SET valor = $valorOPe WHERE id_unico = $row[0]";
		$resultP = $mysqli->query($sqlP);
		###########################################################################################################################################################
		# Consulta de actualización de valores de detalle debito
		#
		###########################################################################################################################################################
		$sqlDetalleDebito = "UPDATE gf_detalle_comprobante SET valor = $valorOPe WHERE id_unico = $row[1]";
		$resultDetalleDebito = $mysqli->query($sqlDetalleDebito);
		###########################################################################################################################################################
		# Consulta de actualización de valores de detalle crédito
		#
		###########################################################################################################################################################
		$sqlDetalleCredito = "UPDATE gf_detalle_comprobante SET valor = $valorOPe WHERE id_unico = $row[2]";
		$resultDetalleCredito = $mysqli->query($sqlDetalleCredito);
		###########################################################################################################################################################
		# Consulta de actualización de valores de detalle iva
		#
		###########################################################################################################################################################
		if(!empty($row[3]) && !empty($row[4])){
			$sqlDetalleIva = "UPDATE gf_detalle_comprobante SET valor = $iva WHERE id_unico = $row[3]";
			$resultDetalleIva = $mysqli->query($sqlDetalleIva);
			$sqlDetalleIvaD = "UPDATE gf_detalle_comprobante SET valor = $iva WHERE id_unico = $row[4]";
			$resultDetalleIvaD = $mysqli->query($sqlDetalleIvaD);
		}
		###########################################################################################################################################################
		# Consulta de actualización de valores de detalle impuesto al consumo
		#
		###########################################################################################################################################################
		if(!empty($row[5]) && !empty($row[6])) {
			$sqlDetalleImpoconsumo = "UPDATE gf_detalle_comprobante SET valor = $impoconsumo WHERE id_unico = $row[5]";
			$resultImpoconsumo = $mysqli->query($sqlDetalleImpoconsumo);
			$sqlDetalleImpoconsumoD = "UPDATE gf_detalle_comprobante SET valor = $impoconsumo WHERE id_unico = $row[6]";
			$resultImpoconsumoD = $mysqli->query($sqlDetalleImpoconsumoD);
		}
	}else{
		###########################################################################################################################################################
		# Consultamos el detalle afectado para modificar el credio y debito
		#
		###########################################################################################################################################################
		$sqlB = "SELECT dtc.detallecomprobantepptal 'detalle_pp', dtf.detallecomprobante 'cuenta_debito', dta.id_unico 'cuenta_credito'
                 FROM gp_detalle_factura dtf
                 LEFT JOIN gf_detalle_comprobante dtc ON dtf.detallecomprobante = dtc.id_unico
                 LEFT JOIN gf_detalle_comprobante dta ON dta.detalleafectado = dtc.id_unico
                 WHERE dtf.id_unico = $id";
		$resultB = $mysqli->query($sqlB);
		$rowB = mysqli_fetch_row($resultB);
		###########################################################################################################################################################
		# Modificamos los detalles del comprobante
		#
		###########################################################################################################################################################
		$sqlD = "UPDATE gf_detalle_comprobante SET valor = $valorAjuste WHERE id_unico = $rowB[1]";#debito
		$resultD = $mysqli->query($sqlD);
		$sqlC = "UPDATE gf_detalle_comprobante SET valor = $valorAjuste WHERE id_unico = $rowB[2]";#credito
		$resultC = $mysqli->query($sqlC);
		if($valorAjuste<0){
			$valorAjuste = $valorAjuste*-1;
		}else{
			$valorAjuste = $valorAjuste;
		}
		$sqlP = "UPDATE gf_detalle_comprobante_pptal SET valor = $valorAjuste WHERE id_unico = $rowB[0]";#presupuestal
		$resultP = $mysqli->query($sqlP);
	}
	###############################################################################################################################################################
	# Consulta de actualización de valores de factura
	#
	###############################################################################################################################################################
	$sql = "UPDATE gp_detalle_factura SET concepto_tarifa=$concepto,cantidad=$cantidad,valor=$valor,iva=$iva,impoconsumo=$impoconsumo,ajuste_peso=$ajustepeso,valor_total_ajustado=$valorAjuste WHERE id_unico = $id";
	$result = $mysqli->query($sql);
	###############################################################################################################################################################
	# Impresión de resultado de actualización
	#
	###############################################################################################################################################################
	echo json_encode($result);
}else{
	###############################################################################################################################################################
	# Consulta de actualización de valores
	#
	###############################################################################################################################################################
	$sql = "UPDATE gp_detalle_factura SET concepto_tarifa=$concepto,cantidad=$cantidad,valor=$valor,iva=$iva,impoconsumo=$impoconsumo,ajuste_peso=$ajustepeso,valor_total_ajustado=$valorAjuste WHERE id_unico = $id";
	$result = $mysqli->query($sql);
	###############################################################################################################################################################
	# Impresión de resultado de actualización
	#
	###############################################################################################################################################################
	echo json_encode($result);
}
?>