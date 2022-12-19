<?php 
##############################################################################################################################################
# Creado por : Jhon Numpaque
# Fecha de creación : 06/03/2017
#
###############################################################################################################################################
# Modificaciones
##########################################################################################################################################
# Modificado por : 			Jhon Numpaque
# Fecha de modificación :	10/03/2017
# Descripción			:  	Se cambio metodo de filtrado de fechas
##########################################################################################################################################
# Llamado de cabezas para convertir el documento a excel
#
##############################################################################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Comprobantes_x_Tipo.xls");
#
###############################################################################################################################################
# Libreria de conexión a base de datos
#
###############################################################################################################################################
require_once("../Conexion/conexion.php");
#
###############################################################################################################################################
# declaración de sessión
#
###############################################################################################################################################
session_start();
#
###############################################################################################################################################
# Captura de variables
#
###############################################################################################################################################
$anno       = $_SESSION['anno'];
$tipoCompInicial = $_POST['sltTipoComprobanteInicial'];
$tipoCompFinal = $_POST['sltTipoComprobanteFinal'];
$fechaInicial = explode("/",$_POST['txtFechaInicial']);
$fechaInicial = $fechaInicial[2]."-".$fechaInicial[1]."-".$fechaInicial[0];
$fechaFinal = explode("/",$_POST['txtFechaFinal']);
$fechaFinal = $fechaFinal[2]."-".$fechaFinal[1]."-".$fechaFinal[0];
#
###############################################################################################################################################
# Validación de variable recibida
#
###############################################################################################################################################
switch ($_GET['tipo']) {
	case 'cnt':
		#######################################################################################################################################
		# Impresión de html
		#
		#######################################################################################################################################
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
		echo "<head>";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
		echo "<title>Listado de comprobante por tipo de comprobante</title>";
		echo "</head>";
		echo "<body>";
		echo "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
		echo "<tr>";
		echo "<td colspan=\"6\" bgcolor=\"skyblue\"><CENTER><strong>LISTADO DE COMPROBANTES POR TIPO</strong></CENTER></td>";
		echo "</tr>";		
		#######################################################################################################################################
		# Consulta por tipo cnt
		#
		#######################################################################################################################################
		$sql = "SELECT DISTINCT	tpc.id_unico,tpc.nombre,tpc.sigla
				FROM			gf_tipo_comprobante tpc
                LEFT JOIN 		gf_comprobante_cnt cnt 
                ON 				cnt.tipocomprobante = tpc.id_unico
                LEFT JOIN		gf_detalle_comprobante dtc ON dtc.comprobante = cnt.id_unico
				WHERE 			tpc.id_unico BETWEEN $tipoCompInicial AND $tipoCompFinal
                AND				cnt.fecha >= '$fechaInicial' AND cnt.fecha <= '$fechaFinal'     
                AND cnt.parametrizacionanno = $anno 
                ORDER BY 		cnt.fecha ASC";
		$result = $mysqli->query($sql);
                $total_d =0;
                $total_c =0;
		while ($row = mysqli_fetch_row($result)) {
			###################################################################################################################################
			# Impresión de tipos de comprobante
			#
			###################################################################################################################################
			echo "<tr>";
			echo "<td colspan=\"6\" border=\"1\"><strong>".strtoupper($row[1])." ".$row[2].": </strong></td>";
			echo "</tr>";
			###################################################################################################################################
			# Impresión de encabezado de la tabla
			#
			###################################################################################################################################
			echo "<tr>";
			echo "<td><strong>No comprobante</strong></td>";
			echo "<td><strong>Fecha</strong></td>";
			echo "<td><strong>Tercero</strong></td>";
			echo "<td><strong>Descripción</strong></td>";
			echo "<td><strong>Valor Débito</strong></td>";
			echo "<td><strong>Valor Crédito</strong></td>";
			echo "</tr>";
			#
			##################################################################################################################################
			# Consulta de comprobantes por tipo
			#
			###################################################################################################################################
			#
			$sqlT = "SELECT DISTINCT	cnt.id_unico,
										cnt.numero,
										date_format(cnt.fecha,'%d/%m/%Y'),
										IF(	CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
													IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
													IF(ter.apellidouno IS NULL,'',
													IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
													IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' 
										OR 	CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
													IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
													IF(ter.apellidouno IS NULL,'',
													IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
													IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
										(ter.razonsocial),                                            
											CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
													IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
													IF(ter.apellidouno IS NULL,'',
													IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
													IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
										cnt.descripcion					
					FROM 		gf_comprobante_cnt cnt
					LEFT JOIN 	gf_tercero ter 				ON cnt.tercero 		= ter.id_unico
					LEFT JOIN 	gf_detalle_comprobante dtc 	ON dtc.comprobante 	= cnt.id_unico
					WHERE 		cnt.tipocomprobante = $row[0] 
					AND			cnt.fecha >= '$fechaInicial' AND cnt.fecha <= '$fechaFinal'
					AND 		dtc.valor IS NOT NULL 
                                        AND cnt.parametrizacionanno = $anno 
					ORDER BY 	cnt.numero ASC";
			$resultT = $mysqli->query($sqlT);
                        $total_debito =0;
                        $total_credito =0;
			while ($rowD = mysqli_fetch_row($resultT)) {
				##############################################################################################################################
				# Definición de variables para debito y credito
				#
				#
				##############################################################################################################################
				#
				$debito = 0;
				$credito = 0;
				#
				##############################################################################################################################
				# Consulta para obtención de valores
				#
				#
				##############################################################################################################################
				#
				$sqlR = "SELECT 	cta.naturaleza,(dtc.valor)
						FROM	 	gf_detalle_comprobante dtc
						LEFT JOIN 	gf_cuenta cta ON cta.id_unico = dtc.cuenta
						WHERE 		dtc.comprobante = $rowD[0]";
				$resultR = $mysqli->query($sqlR);
				while ($rowV = mysqli_fetch_row($resultR)){
					######################################################################################################################
					# Validación de impresión de valores, se valido por naturaleza débito y credito, y si el valor es negativo o positivo se ubico en variables	
					#
					######################################################################################################################					
					if($rowV[0]==1){
						if($rowV[1]>0){
							$debito = $debito + $rowV[1];
						}else{
							$credito = $credito + $rowV[1]*-1;
						}
					}
					if($rowV[0]==2){
						if($rowV[1]>0){
							$credito = $credito + $rowV[1];
						}else{
							$debito = $debito + $rowV[1]*-1;
						}
					}
				}								
				#
				##############################################################################################################################
				# Impresión de valores
				#
				##############################################################################################################################
				echo "<tr>";
				echo "<td>".$rowD[1]."</td>";
				echo "<td>".$rowD[2]."</td>";
				echo "<td>".mb_convert_encoding(ucwords(strtolower($rowD[3])),'utf-8')."</td>";
				echo "<td>".mb_convert_encoding(ucwords(strtolower($rowD[4])),'utf-8')."</td>";
				echo "<td>".number_format($debito,2,'.',',')."</td>";
				echo "<td>".number_format($credito,2,'.',',')."</td>";
				echo "</tr>";
                                $total_debito   +=$debito;
                                $total_credito  +=$credito;
			}
                        echo "<tr>";
                        echo "<td colspan=\"4\" border=\"1\"><strong> TOTAL ".mb_strtoupper($row[1])." ".$row[2].": </strong></td>";
                        echo "<td><strong>".number_format($total_debito,2,'.',',')."</strong></td>";
                        echo "<td><strong>".number_format($total_credito,2,'.',',')."</strong></td>";
                        echo "</tr>";
                        $total_d    +=$total_debito;
                        $total_c    +=$total_credito;
		}
                echo "<tr>";
                echo "<td colspan=\"4\" border=\"1\"><strong> TOTALES </strong></td>";
                echo "<td><strong>".number_format($total_d,2,'.',',')."</strong></td>";
                echo "<td><strong>".number_format($total_c,2,'.',',')."</strong></td>";
                echo "</tr>";
		#######################################################################################################################################
		# Impresión Final de html
		#
		#######################################################################################################################################
		echo "</table>";
		echo "</body>";
		echo "<html>";
		break;
	
	case 'pptal':
		#######################################################################################################################################
		# Impresión de html
		#
		#######################################################################################################################################
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
		echo "<head>";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
		echo "<title>Listado de comprobante por tipo de comprobante</title>";
		echo "</head>";
		echo "<body>";
		echo "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
		echo "<tr>";
		echo "<td colspan=\"5\" bgcolor=\"skyblue\"><CENTER><strong>LISTADO DE COMPROBANTES POR TIPO</strong></CENTER></td>";
		echo "</tr>";		
		#######################################################################################################################################
		# Consulta por tipo pptal
		#
		#######################################################################################################################################
		#
		$sql = "SELECT DISTINCT		tpc.id_unico,tpc.nombre,tpc.codigo 
				FROM				gf_tipo_comprobante_pptal tpc 
				LEFT JOIN			gf_comprobante_pptal pptal ON	pptal.tipocomprobante = tpc.id_unico 
				LEFT JOIN 			gf_detalle_comprobante_pptal dtc ON dtc.comprobantepptal = pptal.id_unico
				WHERE				tpc.id_unico BETWEEN $tipoCompInicial AND $tipoCompFinal
				AND 				pptal.fecha >= '$fechaInicial' AND pptal.fecha <= '$fechaFinal' 
                                AND   pptal.parametrizacionanno = $anno     
				ORDER BY 			pptal.fecha ASC";
		$result = $mysqli->query($sql);
		while ($row = mysqli_fetch_row($result)) {
			###################################################################################################################################
			# Impresión de tipos de comprobante
			#
			###################################################################################################################################
			echo "<tr>";
			echo "<td colspan=\"5\" border=\"1\"><strong>".strtoupper($row[1])." ".$row[2].": </strong></td>";
			echo "</tr>";
			#
			##################################################################################################################################
			# Impresión de encabezado de la tabla
			#
			###################################################################################################################################
			echo "<tr>";
			echo "<td><strong>No comprobante</strong></td>";
			echo "<td><strong>Fecha</strong></td>";
			echo "<td><strong>Tercero</strong></td>";
			echo "<td><strong>Descripción</strong></td>";
			echo "<td><strong>Valor</strong></td>";
			echo "</tr>";
			#
			##################################################################################################################################
			# Consulta de comprobantes por tipo
			#
			###################################################################################################################################
			#
			$sqlT = "SELECT DISTINCT	pptal.id_unico,
										pptal.numero,
										date_format(pptal.fecha,'%d/%m/%Y'),
										IF(	CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
													IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
													IF(ter.apellidouno IS NULL,'',
													IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
													IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' 
										OR 	CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
													IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
													IF(ter.apellidouno IS NULL,'',
													IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
													IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
										(ter.razonsocial),                                            
											CONCAT(	IF(ter.nombreuno='','',ter.nombreuno),' ',
													IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
													IF(ter.apellidouno IS NULL,'',
													IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
													IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
										pptal.descripcion					
					FROM 		gf_comprobante_pptal pptal
					LEFT JOIN 	gf_tercero ter 						ON pptal.tercero 				= ter.id_unico
					LEFT JOIN 	gf_detalle_comprobante_pptal dtc 	ON dtc.comprobantepptal 		= pptal.id_unico
					WHERE 		pptal.tipocomprobante = $row[0] 
					AND			pptal.fecha >= '$fechaInicial' AND pptal.fecha <= '$fechaFinal' 
					AND 		dtc.valor IS NOT NULL 
                                        AND   pptal.parametrizacionanno = $anno     
					ORDER BY 	pptal.numero ASC";
			$resultT = $mysqli->query($sqlT);
			while ($rowD = mysqli_fetch_row($resultT)) {
				#
				##############################################################################################################################
				# Consulta para obtención de valores
				#
				#
				##############################################################################################################################
				#
				$valorT = 0;
				$sqlR = "SELECT 	SUM(dtc.valor)
						FROM		gf_detalle_comprobante_pptal dtc
						LEFT JOIN	gf_comprobante_pptal pptal ON pptal.id_unico = dtc.comprobantepptal
						WHERE		pptal.id_unico = $rowD[0]
						GROUP BY   	dtc.valor";
				$resultR = $mysqli->query($sqlR);
				while($valor = mysqli_fetch_row($resultR)){
					if($valor[0]<0){
						$valorT += $valor[0] *-1;
					}else{
						$valorT += $valor[0];
					}
				}
				echo "<tr>";
				echo "<td>".$rowD[1]."</td>";
				echo "<td>".$rowD[2]."</td>";
				echo "<td>".mb_convert_encoding(ucwords(strtolower($rowD[3])),'utf-8')."</td>";
				echo "<td>".mb_convert_encoding(ucwords(strtolower($rowD[4])),'utf-8')."</td>";
				echo "<td>".number_format($valorT<0?$valorT*-1:$valorT,2,',','.')."</td>";
				echo "</tr>";
			}
		}
		######################################################################################################################################
		# Impresión Final de html
		#
		#######################################################################################################################################
		echo "</table>";
		echo "</body>";
		echo "<html>";
		break;
}
 ?>