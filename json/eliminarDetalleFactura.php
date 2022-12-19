<?php
session_start();
require_once '../Conexion/conexion.php';
###############################################################################################################################################################
# Modificaciones
#
###############################################################################################################################################################
# Fecha de Modificación : 23/06/2017
# Modificado por 		: Jhon Numpaque
# Descripción			: Se cambio consulta para encontrar valores en cascada cuando el detalle tiene iva y factura y se valido para eliminar los detalles exactos
###############################################################################################################################################################
# Fecha de Modificación : 25/04/2017
# Modificado por 		: Jhon Numpaque
# Descripción			: Se agrego validación para eliminado en cascada
###############################################################################################################################################################
# Fecha de Modificación : 01/03/2017
# Modificado por 		: Jhon Numpaque
# Hora de Modificación  : 10 : 30
# Descripción			: Se agrego elimininado para dos detalles que cuya cuenta es debtio y tienen valores de impoconsumo e via
###############################################################################################################################################################
# Captura de variables
#
###############################################################################################################################################################
$id = $_GET['id'];
$iva = $_GET['iva'];
$impo = $_GET['impo'];
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
	if(!empty($iva) || $iva!='0' || $iva!='0.00'){		
		###########################################################################################################################################################
		# Consulta de busqueda de detalles		
		############################################################################################################################################################
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
		# Eliminado de detalle factura
		###########################################################################################################################################################
		$sql = "DELETE FROM gp_detalle_factura WHERE id_unico = $id";
		$result = $mysqli->query($sql);
		$sqlIva = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $row[4]";
		$resultIva = $mysqli->query($sqlIva);
		$sqlDIva = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $row[3]";
		$resultDIva = $mysqli->query($sqlDIva);
		$sqlC = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $row[2]";
		$resultC = $mysqli->query($sqlC);
		$sqlD = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $row[1]";
		$resultD = $mysqli->query($sqlD);
		echo json_encode($result);
		###########################################################################################################################################################
		# Eliminado de detalles contables		
		############################################################################################################################################################
		if(!empty($row[6]) && !empty($row[5])) {
			$sqlImpo = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $row[6]";
			$resulImpo = $mysqli->query($sqlImpo);	
			$sqlDImpo = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $row[5]";
			$resultDImpo = $mysqli->query($sqlDImpo);		
		}
				
		###########################################################################################################################################################
		# Eliminado de detalles presupuestales
		#
		###########################################################################################################################################################
		$sqlDP = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $row[0]";
		$resultDP = $mysqli->query($sqlDP);		
	}else if(empty($iva) || $iva=='0' || $iva=='0.00'){		
		###########################################################################################################################################################
		# Eliminado de detalle factura
		###########################################################################################################################################################
		$sql = "DELETE FROM gp_detalle_factura WHERE id_unico = $id";
		$result = $mysqli->query($sql);				
		echo json_encode($result);
		###########################################################################################################################################################
		# Consultamos el detalle afectado
		#
		###########################################################################################################################################################
		$sqlB = "SELECT detalleafectado,detallecomprobantepptal FROM gf_detalle_comprobante WHERE id_unico = $rowA[0]";
		$resultB = $mysqli->query($sqlB);
		$rowB = $mysqli->query($resultB);		
		###########################################################################################################################################################
		# Eliminamos los detalles
		#
		###########################################################################################################################################################
		$sqlC = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $rowB[0]";
		$resultC = $mysqli->query($sqlC);		
		$sqlD = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $rowA[0]";
		$resultD = $mysqli->query($sqlD);
		$sqlDP = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $rowB[1]";
		$resultDP = $mysqli->query($sqlDP);											
	}		
}else{
	###############################################################################################################################################################
	# Eliminado de detalle factura
	#
	###############################################################################################################################################################
	/*$sql = "DELETE FROM gp_detalle_factura WHERE id_unico = $id";
	$result = $mysqli->query($sql);
	echo json_encode($result);	*/
}
?>

