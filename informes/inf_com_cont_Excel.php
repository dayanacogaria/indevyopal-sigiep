<?php 
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Comprobante_Contable.xls");
require_once("../Conexion/conexion.php");
session_start();
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');

$comprobante=$_GET['idcom'];
#Consulta de comprobante cnt
$sqlComprobante="SELECT 	cnt.id_unico,
							cnt.numero,
							cnt.fecha,
							cnt.descripcion,
							cnt.tipocomprobante,
							tpcnt.nombre,
							cnt.tercero,
							tpcnt.sigla,
							date_format(cnt.fecha,'%d/%m/%Y')
				FROM gf_comprobante_cnt cnt 
				LEFT JOIN gf_tipo_comprobante tpcnt ON cnt.tipocomprobante = tpcnt.id_unico
				LEFT JOIN gf_detalle_comprobante dtC ON dtC.comprobante = cnt.id_unico								
				WHERE md5(cnt.id_unico)='$comprobante'";
$comp = $mysqli->query($sqlComprobante);
#Array de asignación de valores de consulta comprobante comtable
$filaComp=mysqli_fetch_row($comp);
#Asignación de valores a variables de comprobante contable
$idComp=$filaComp[0];
$numComp=$filaComp[1];			#Número de comprobante
$fechaComp=$filaComp[2];		#Fecha de comprobante
$desComp=$filaComp[3];			#Descripción
$idtipoComp=$filaComp[4];		#Id tipo de comprobante
$nombreTipoComp=$filaComp[5];	#Nombre de tipo de comprobante
$terceroComp=$filaComp[6];		#Tercero de comprobante
$fechavencimientoC = $filaComp[8];
#Comprobante pptal
#Consulta de compañia
$compania = $_SESSION['compania'];
#consulta de datos de compañia
$queryCompania="SELECT 	com.razonsocial,
						tpI.nombre,
						com.numeroidentificacion,
						ci.nombre,
						com.ruta_logo
				FROM gf_tercero com 
				LEFT JOIN gf_tipo_identificacion tpI ON com.tipoidentificacion = tpI.id_unico
				LEFT JOIN gf_ciudad ci ON com.ciudadidentificacion = ci.id_unico
				WHERE com.id_unico=$compania";
$resultCompania = $mysqli->query($queryCompania);
#Array de asignación de valores de consulta de compañia
$rowComp=mysqli_fetch_row($resultCompania);
#Asignación de valores de consulta de compañia
$razonsocial=$rowComp[0];		#Razón social de la compañia
$tipoIdnComp=$rowComp[1];		#Tipo de identificación de compañia
$numeroIdent=$rowComp[2];		#Número de identificación
$ciudadident=$rowComp[3];		#Ciudad de identificación
$ruta=$rowComp[4];				#Ruta de logo
#Variables para el pie de página
$fechaActual=date('d/m/Y');		#Fecha actual
$usuario=$_SESSION['usuario'];	#Usuario accesado

#Consulta para obtener el tercero
$queryTercero="	SELECT	IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',ter.numeroidentificacion
				FROM gf_tercero ter
				WHERE ter.id_unico = $terceroComp";
$resultTercero=$mysqli->query($queryTercero);
#Array de asignación de valores
$rowTercero=mysqli_fetch_row($resultTercero);
#Asignación de valores de contal de tercero
$tercero= $rowTercero[0]; 		#Nombre de tercero
$identif= $rowTercero[1];		#Número de identificación
#Creación de celda vacia para valores inciales
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Comprobante Contable</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="6" bgcolor="skyblue"><CENTER><strong>Comprobante Contable</strong></CENTER></td>
  </tr>
  <tr>
  	<td colspan="6" bgcolor="skyblue"><CENTER><strong><?php echo utf8_decode(ucwords(strtoupper($nombreTipoComp.PHP_EOL))).'Nro:'.PHP_EOL.$numComp?></strong></CENTER></td>
  </tr>	
  	<?php 
  	#Conversión de fecha
$fechaD=explode("-",$fechaComp);			
$diaC =$fechaD[2];				#Dia
$mesC = (int) $fechaD[1];		#Mes
$annoC= $fechaD[0];				#Año
#Impresión de fecha
  	?>
  <tr>
  	 <td colspan="4" ><strong>FECHA: <?php echo $diaC.PHP_EOL.'de'.PHP_EOL.$meses[$mesC].PHP_EOL.'de'.PHP_EOL.$annoC?></strong></td>
  	  <td colspan="2" ><strong>VENCE: <?php echo $fechavencimientoC?></strong></td>
  </tr> 	
    <tr>
  	 <td colspan="4" ><strong>RECIBÍ DE: <?php echo $tercero ?></strong></td>
  	  <td colspan="2"><strong>C.C / NIT: <?php echo $identif ?></strong></td>
  </tr> 
  <tr>
  	<td colspan="6" rowspan="2"><strong>DESCRIPCIÓN: <?php echo $desComp ?></strong></td>
  </tr>	
<tr></tr>
<tr>
	<td><center><strong>CUENTA
	</strong></center></td>
	<td><center><strong>DÉBITO
	</strong></center></td>
	<td><center><strong>CRÉDITO
	</strong></center></td>
	<td><center><strong>CENTRO COSTO
	</strong></center></td>
	<td><center><strong>PROYECTO 
	</strong></center></td>
	<td><center><strong>TERCERO
	</strong></center></td>
</tr>
<?php
#Consulta de detalle presupuestal y contable
$detallP="	SELECT DISTINCT 
							dtc.id_unico,                             
                            cnt.id_unico cuenta, 
                            cnt.codi_cuenta, 
                            cnt.nombre, 
                            cnt.naturaleza, 
                            dtc.valor, 
                            pr.id_unico proyecto, 
                            pr.nombre, 
                            ctr.id_unico centroc, 
                            ctr.nombre,
                            ctr.sigla, 
                            dtc.tercero
            FROM gf_detalle_comprobante dtc             
            LEFT JOIN gf_cuenta cnt ON dtc.cuenta = cnt.id_unico 
            LEFT JOIN gf_proyecto pr ON dtc.proyecto = pr.id_unico 
            LEFT JOIN gf_centro_costo ctr ON dtc.centrocosto = ctr.id_unico 
            LEFT JOIN gf_tercero ter ON dtc.tercero = ter.id_unico 
            WHERE dtc.comprobante = $idComp";
$resultDP = $mysqli->query($detallP);
#Variable de suma inicializadas en 0+
$sumD = 0;
$sumC = 0;
#Ciclo o bluce
while ($filaDP=mysqli_fetch_row($resultDP)) {	
	#Validación de valor por naturaleza
	if($filaDP[4]==1){
		if($filaDP[5] > 0){			
			$deb = number_format($filaDP[5],2,'.',',');
			$cre = '0.00';
			#Inicia captura suma de totales 
			$sumD+=$filaDP[5];
			$sumC+=$cre;
		}else{
			$x = (float) substr($filaDP[5],'1');
			$deb = '0.00';
			$cre = number_format($x,2,'.',',');
			$sumD+=$deb;
			$sumC+=$x;
		}
	}else if($filaDP[4]==2){
		if($filaDP[5] > 0){
			$deb = '0.00';
			$cre = number_format($filaDP[5],2,'.',',');
			#Inicia captura suma de totales 
			$sumC+=$filaDP[5];
			$sumD+=$deb;		
		}else{
			$x = (float) substr($filaDP[5],'1');
			$deb = number_format($x,2,'.',',');
			$cre = '0.00';
			#Inicia captura suma de totales 
			$sumD+=$x;
			$sumC+=$cre;
		}
	}
	
	$cuenta = $filaDP[2].' - '.$filaDP[3];
	#Query para consultar tercero
	$sqlTercero="SELECT	IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',ter.numeroidentificacion FROM gf_tercero ter WHERE ter.id_unico = $filaDP[11]";
	$resultTer=$mysqli->query($sqlTercero);
	#Asignación de valor de tercero
	$ter=mysqli_fetch_row($resultTer);
	?>
	<tr>
		<td><?php echo $cuenta; ?></td>
		<td><?php echo $deb; ?></td>
		<td><?php echo $cre; ?></td>
		<td><?php echo $filaDP[9]; ?></td>
		<td><?php echo ucwords(mb_strtolower($filaDP[7])); ?></td>
		<td><?php echo ucwords(mb_strtolower($ter[0])); ?></td>
	</tr>
	<?php 
}
?>
<tr>
	<td><strong>TOTALES:</strong>

</td>
<td><?php echo number_format($sumD,2,'.',',')?></td>
<td><?php echo number_format($sumC,2,'.',',')?></td>
<td colspan="3"></td>
</tr>
</table>
</body>
</html>