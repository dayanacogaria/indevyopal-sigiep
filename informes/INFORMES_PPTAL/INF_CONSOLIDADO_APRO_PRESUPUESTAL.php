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
header("Content-Disposition: attachment; filename=Informe_Consolidado_Apropiaciones_Presupuesto.xls");
#	
###############################################################################################################################################
# Libreria de conexión a base de datos
#
###############################################################################################################################################
require'../../Conexion/conexion.php';
require'../../Conexion/ConexionPDO.php';
require'../../jsonPptal/funcionesPptal.php';
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
$compania   =   $_SESSION['compania'];
$fechaInicial = explode("/",$_POST['fechaini']);
$fechaInicial = $fechaInicial[2]."-".$fechaInicial[1]."-".$fechaInicial[0];
$fechaFinal = explode("/",$_POST['fechafin']);
$fechaFinal = $fechaFinal[2]."-".$fechaFinal[1]."-".$fechaFinal[0];
$annio=anno($anno);
$sqlC = "SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo 
         FROM gf_tercero ter
         LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
         LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
         LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
         WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$feI = $_POST['fechaini'];
$feF = $_POST['fechafin'];

#
###############################################################################################################################################
# Validación de variable recibida

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
		echo "<td colspan=\"7\" ><CENTER><strong>$razonsocial</strong></CENTER></td>";
		echo "</tr>";	
		echo "<tr>";	
		echo "<td colspan=\"7\" ><CENTER><strong>$nombreIdent - $numeroIdent</strong></CENTER></td>";
		echo "</tr>";
		echo "<tr>";	
		echo "<td colspan=\"7\" ><CENTER><strong>CONSOLIDADO DE COMPROBANTES DE APROPIACION DE PRESUPUESTO </strong></CENTER></td>";
		echo "</tr>";
		echo "<tr>";	
		echo "<td colspan=\"7\" ><CENTER><strong>Entre: $feI  	A  $feF</strong></CENTER></td>";
		echo "</tr>";
		#######################################################################################################################################
		# Consulta por tipo pptal
		#
		#######################################################################################################################################
		#
		$sql1 = "SELECT DISTINCT t.id_unico,
		         IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) IS NULL 
		         OR CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = '', 
		         (t.razonsocial), 
		         CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)) AS NOMBRE,t.numeroidentificacion,pr.id_unico
	           FROM  gf_tercero tr
	           LEFT JOIN gf_tercero t ON tr.compania = t.id_unico
	           LEFT JOIN gf_parametrizacion_anno pr ON pr.compania=t.id_unico
	           WHERE t.id_unico=$compania
	           AND pr.anno=$annio";

		$sql = "SELECT DISTINCT t.id_unico,
		               IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) IS NULL 
		               OR CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = '', 
		               (t.razonsocial), 
		               CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)) AS NOMBRE,t.numeroidentificacion,pr.id_unico
	            FROM  gf_tercero tr
	            LEFT JOIN gf_tercero t ON tr.compania = t.id_unico
	            LEFT JOIN gf_parametrizacion_anno pr ON pr.compania=t.id_unico
	            WHERE IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) IS NULL 
	            OR CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = '', 
	            (t.razonsocial), 
	            CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos))!='VARIOS'
	            AND t.id_unico!=1 AND pr.anno=$annio";
		 		
		$result = $mysqli->query($sql);
		if(mysqli_num_rows($result)>0){
			if ($compania==1) {
				$result = $mysqli->query($sql);
			}else{
				$result = $mysqli->query($sql1);
			}
		}else{
			$result = $mysqli->query($sql1);
		}
		while ($row = mysqli_fetch_row($result)) {

			$paranoComp=$row[3];
			###################################################################################################################################
			# Impresión de Entidadades 
			#
			###################################################################################################################################
			echo "<tr>";
			echo "<td colspan=\"7\" border=\"1\"><strong>".strtoupper($row[1])."-".$row[2].": </strong></td>";
			echo "</tr>";
			#
			##################################################################################################################################
			# Impresión de encabezado de la tabla
			#
			###################################################################################################################################
			echo "<tr>";
			echo "<td><strong>Tipo comprobante</strong></td>";
			echo "<td><strong>N° comprobante</strong></td>";
			echo "<td><strong>Fecha</strong></td>";
			echo "<td><strong>Descripción</strong></td>";
			echo "<td><strong>Valor Ingresos</strong></td>";
			echo "<td><strong>Valor Gastos</strong></td>";
			echo "<td><strong>Diferencia</strong></td>";
			echo "</tr>";
			#
			##################################################################################################################################
			# Consulta de comprobantes por tipo
			#
			###################################################################################################################################
			#
			$valorToIngr = 0;
				$valorToGast = 0;
				$valorToDif  = 0;

			$sqlT = "SELECT DISTINCT	pptal.id_unico,CONCAT(tcp.codigo,'-',tcp.nombre),
			                            pptal.numero,date_format(pptal.fecha,'%d/%m/%Y'),pptal.descripcion
                    FROM 		gf_comprobante_pptal pptal
					LEFT JOIN 	gf_tercero ter 						ON pptal.tercero 				= ter.id_unico
					LEFT JOIN 	gf_detalle_comprobante_pptal dtc 	ON dtc.comprobantepptal 		= pptal.id_unico
					LEFT JOIN   gf_tipo_comprobante_pptal tcp       ON tcp.id_unico=pptal.tipocomprobante
					WHERE 		tcp.clasepptal = 13
					AND			pptal.fecha >= '$fechaInicial' AND pptal.fecha <= '$fechaFinal' 
					AND 		dtc.valor IS NOT NULL 
                    AND          pptal.parametrizacionanno = $paranoComp     
					ORDER BY 	tcp.codigo ASC";
			$resultT = $mysqli->query($sqlT);
			while ($rowD = mysqli_fetch_row($resultT)) {
				#
				##############################################################################################################################
				# Consulta para obtención de valores Gastos e Ingresos
				##############################################################################################################################
				#
				
				
				# Consulta para obtención de valores Ingresos
				$id_pptal=$rowD[0];
					$sqlIng = "SELECT 	SUM(dtc.valor)
					           FROM		gf_detalle_comprobante_pptal dtc
					           LEFT JOIN   gf_comprobante_pptal pptal  ON pptal.id_unico = dtc.comprobantepptal
					           LEFT JOIN   gf_rubro_fuente  rf         ON rf.id_unico=dtc.rubrofuente
					           LEFT JOIN   gf_rubro_pptal   rpptal     ON rpptal.id_unico=rf.rubro
					           WHERE		pptal.id_unico =$id_pptal
					           AND         rpptal.tipoclase=6";

				$resultIng      = $mysqli->query($sqlIng);
				$valorIng       = mysqli_fetch_row($resultIng);
				$valorIngresos  = $valorIng[0];

				# Consulta para obtención de valores Gastos
				$sqlGast = "SELECT 	SUM(dtc.valor)
				           FROM		   gf_detalle_comprobante_pptal dtc
				           LEFT JOIN   gf_comprobante_pptal pptal  ON pptal.id_unico = dtc.comprobantepptal
				           LEFT JOIN   gf_rubro_fuente  rf         ON rf.id_unico=dtc.rubrofuente
				           LEFT JOIN   gf_rubro_pptal   rpptal     ON rpptal.id_unico=rf.rubro
				           WHERE		pptal.id_unico =$id_pptal
				           AND         rpptal.tipoclase=7";

                $resultGast     = $mysqli->query($sqlGast);
                $valorGast       = mysqli_fetch_row($resultGast);
                $valorGastos  = $valorGast[0];
                 # Obetener diferencia Ingresos Gastos.
				 $diferencia=$valorIngresos-$valorGastos;

				echo "<tr>";
				echo "<td>".$rowD[1]."</td>";
				echo "<td>".$rowD[2]."</td>";
				echo "<td>".$rowD[3]."</td>";
				echo "<td>".$rowD[4]."</td>";
				echo "<td>".number_format($valorIngresos,2,',','.')."</td>";
				echo "<td>".number_format($valorGastos,2,',','.')."</td>";
				echo "<td>".number_format($diferencia,2,',','.')."</td>";
				echo "</tr>";
				$valorToIngr +=$valorIngresos;
				$valorToGast +=$valorGastos;
				$valorToDif  +=$diferencia;
			}
			echo "<tr>";
			    echo "<td colspan=\"4\" ><center><strong>TOTAL</strong></center></td>";
				echo "<td colspan=\"1\">".number_format($valorToIngr,2,',','.')."</td>";
				echo "<td colspan=\"1\">".number_format($valorToGast,2,',','.')."</td>";
				echo "<td colspan=\"1\">".number_format($valorToDif,2,',','.')."</td>";
				echo "</tr>";
		}
		######################################################################################################################################
		# Impresión Final de html
		#
		#######################################################################################################################################
		echo "</table>";
		echo "</body>";
		echo "<html>";
 ?>