<?php 
####################################################################################################################################################################
# Creado por 		: 	Jhon Numpaque
# Fecha de creación : 	27/02/2017
# Hora 				: 	03:37 p.m
header("Content-Type: text/html;charset=utf-8");
####################################################################################################################################################################
# Librerias
require_once('../numeros_a_letras.php');    
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
#
####################################################################################################################################################################
# 
session_start(); 	# Session
#
###################################################################################################################################################################
# Captura de variable de session compañia
$compania = $_SESSION['compania'];
#
###################################################################################################################################################################
# Array de meses del año
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
#
###################################################################################################################################################################
# Consulta para obtener los datos de compañia
# @$sqlC
$sqlC = "SELECT 	ter.razonsocial,
					ti.nombre,
					ter.numeroidentificacion,
					ter.ruta_logo
		FROM		gf_tercero ter
		LEFT JOIN	gf_tipo_identificacion ti 
		ON 			ti.id_unico = ter.tipoidentificacion
		WHERE		ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowCompania = mysqli_fetch_row($resultC);
# 
###################################################################################################################################################################
# Cargue de variables de compañia
$razonsocial = $rowCompania[0];
$nombreTipoIden = $rowCompania[1];
$numeroIdent = $rowCompania[2];
$ruta = $rowCompania[3];
#
###################################################################################################################################################################
# Captura de id de factura
$factura = $_GET['factura'];
#
###################################################################################################################################################################
# Consulta para obtener los datos de factura
# @sqlF {String}
$sqlF = "SELECT 	fat.id_unico,
					tpf.nombre,
					fat.numero_factura,
					CONCAT(ELT(WEEKDAY(fat.fecha_factura) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA,
					fat.fecha_factura,
					date_format(fat.fecha_vencimiento,'%d/%m/%Y'),
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
					ter.numeroidentificacion,
					fat.descripcion
		FROM		gp_factura fat
		LEFT JOIN	gp_tipo_factura tpf 	ON tpf.id_unico = fat.tipofactura
		LEFT JOIN	gf_tercero ter 			ON ter.id_unico = fat.tercero
		WHERE		md5(fat.id_unico) = '$factura'";
$resultF = $mysqli->query($sqlF);
$rowF = mysqli_fetch_row($resultF);
###################################################################################################################################################################
# Cargue de variables de factura
$fat_id = $rowF[0];
$tip_fat = $rowF[1];
$num_fat = $rowF[2];
$dia_fat = $rowF[3];
$fecha_fat = $rowF[4];
$fechaV_fat = $rowF[5];
$tercero_fat = $rowF[6];
$idt_fat = $rowF[7];
$desc_fat = $rowF[8];
#
###################################################################################################################################################################
# Clase de diseño de formato
class PDF extends FPDF
{
	#Funcón cabeza de la página
	function header(){
		#Redeclaración de varibles
		global $razonsocial;	#Nombre de compañia	
		global $nombreTipoIden;	#Tipo de identificación tercero
		global $tip_fat;		#Nombre de factura
		global $num_fat;		#Número de facutra
		global $ruta;			#Ruta de logo
		global $numeroIdent;	#Numero identificacion tercero
		// Logo acá
	    if($ruta != '')
	    {
	      $this->Image('../'.$ruta,10,8,20);
	    }
		#Razón social
		$this->SetFont('Courier','B',10);	
		$this->SetXY(40,15);
		$this->MultiCell(140,5,utf8_decode($razonsocial),0,'C');
		#Tipo documento y número de documento		
		$this->SetX(10);
		$this->Ln(1);
		$this->SetFont('Courier','',10);
		$this->Cell(200,5,utf8_decode($nombreTipoIden.':'.PHP_EOL.$numeroIdent),0,0,'C');					
		#Tipo de comprobante y número de comprobante
		$this->ln(5);		
		$this->SetFont('Courier','B',10);
		$this->Cell(200,5,utf8_decode(ucwords(strtoupper($tip_fat.PHP_EOL))).'Nro:'.PHP_EOL.$num_fat,0,0,'C');
	}	
}
#
#
###################################################################################################################################################################
# Inicializacipon de la clase
#
###################################################################################################################################################################
$pdf = new PDF('P','mm','Letter'); 		#Creación del objeto pdf
$nb=$pdf->AliasNbPages();						#Objeto de número de pagina
$pdf->AddPage();								#Agregar página
#
###################################################################################################################################################################
# Impresion de cuerpo de formato
#
###################################################################################################################################################################
# Celda vacia
#
###################################################################################################################################################################
$pdf->Ln(10);
$pdf->Cell(190,12,'',1,0);
###################################################################################################################################################################
# Fecha de factura
#
###################################################################################################################################################################
$pdf->Ln(0);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(25,5,'FECHA:',0,'L');
###################################################################################################################################################################
# Conversión de fecha
#
###################################################################################################################################################################
$fechaD=explode("-",$fecha_fat);
$diaC =$fechaD[2];				#Dia
$mesC = (int) $fechaD[1];		#Mes
$annoC= $fechaD[0];				#Año
$pdf->SetFont('Courier','',10);
$pdf->Cell(90,5,$dia_fat.', '.$diaC.' de '.$meses[$mesC].' de '.$annoC,0,0,'L');
###################################################################################################################################################################
# Fecha de vencimiento
#
###################################################################################################################################################################
$pdf->SetFont('Courier','B',10);
$pdf->Cell(50,5,'VENCE:',0,0,'R');
$pdf->SetFont('Courier','',10);
$pdf->Cell(25,5,$fechaV_fat,0,0,'L');
$pdf->Ln(7);
###################################################################################################################################################################
# Tercero
#
###################################################################################################################################################################
$pdf->SetFont('Courier','B',10);
$pdf->Cell(25,5,'RECIBI DE:',0,'L');
$pdf->SetFont('Courier','',10);
$pdf->Cell(90,5,$tercero_fat,0,0,'L');
###################################################################################################################################################################
# Numero de identificación
#
###################################################################################################################################################################
$pdf->SetFont('Courier','B',10);
$pdf->Cell(50,5,'C.C / NIT:',0,0,'R');
$pdf->SetFont('Courier','',10);
$pdf->Cell(25,5,$idt_fat,0,0,'L');
$pdf->Ln(5);
###################################################################################################################################################################
# Celda vacia
#
###################################################################################################################################################################
$pdf->Cell(190,10,'',1,0);
$pdf->Ln(0);
###################################################################################################################################################################
# Descripción
#
###################################################################################################################################################################
$pdf->SetFont('Courier','B',9);
$pdf->Cell(25,5,utf8_decode('DESCRIPCIÓN:'),0,0,'L');
$pdf->SetFont('Courier','',9);
$pdf->Multicell(165,5,utf8_decode($desc_fat),0,'L');
$pdf->Ln(5);
###################################################################################################################################################################
# Cabeza de tabla
#
###################################################################################################################################################################
$pdf->SetFont('Courier','B',7);
$pdf->Cell(50,10,'CONCEPTO',1,0,'L');
$pdf->Cell(15,10,'CANTIDAD',1,0,'L');
$pdf->Cell(25,10,'VALOR',1,0,'L');
$pdf->Cell(25,10,'IVA',1,0,'L');
$pdf->Cell(25,10,'IMPUESTO DE',1,0,'C');
$pdf->Cell(25,10,'AJUSTE AL',1,0,'C');
$pdf->Cell(25,10,'VALOR',1,0,'C');
$pdf->SetX(10);
$pdf->Cell(50,5,'',0,0,'L');
$pdf->Cell(15,5,'',0,0,'L');
$pdf->Cell(25,5,'',0,0,'L');
$pdf->Cell(25,5,'',0,0,'L');
$pdf->Cell(25,5,'',0,0,'C');
$pdf->Cell(25,5,'',0,0,'C');
$pdf->Cell(25,5,'',0,0,'L');
$pdf->Ln(5);
$pdf->Cell(50,5,'',0,0,'L');
$pdf->Cell(15,5,'',0,0,'L');
$pdf->Cell(25,5,'',0,0,'L');
$pdf->Cell(25,5,'',0,0,'L');
$pdf->Cell(25,5,'CONSUMO',0,0,'C');
$pdf->Cell(25,5,'PESO',0,0,'C');
$pdf->Cell(25,5,'TOTAL',0,0,'C');
$pdf->Ln(5);
###################################################################################################################################################################
# Definición de variables para suma
#
###################################################################################################################################################################
$sumCantidad = 0;
$sumValor = 0;
$sumIva = 0;
$sumImpo = 0;
$sumAjuste = 0;
$sumValorT = 0;
###################################################################################################################################################################
# Consulta para obtener valores de tabla
#
###################################################################################################################################################################
$sqlDetalleFactura = "SELECT DISTINCT conp.nombre,
								dtf.cantidad,
								dtf.valor,
								dtf.iva,
								dtf.impoconsumo,
								dtf.ajuste_peso,
								dtf.valor_total_ajustado
					FROM		gp_detalle_factura dtf					
					LEFT JOIN	gp_concepto conp ON conp.id_unico = dtf.concepto_tarifa
					WHERE		dtf.factura = $fat_id";
$resultDetalleFactura = $mysqli->query($sqlDetalleFactura);
while($rowDF = mysqli_fetch_row($resultDetalleFactura)){
	###############################################################################################################################################################
	# Impresión de valores
	#
	###############################################################################################################################################################
	$pdf->SetFont('Courier','',7);
        
	$cantidad = $rowDF[1];
	$valor = $rowDF[2];
	$iva = $rowDF[3];
	$impoconsumo = $rowDF[4];
	$ajuste = $rowDF[5];
	$valorTotal = $rowDF[6];
	###############################################################################################################################################################
	# Impresión
	#
	###############################################################################################################################################################
        $pdf->CellFitScale(50,4,utf8_decode($rowDF[0]),0,0,'L');
        $pdf->CellFitScale(15,4, utf8_decode($cantidad),0,0,'R');
        $pdf->CellFitScale(25,4,number_format($valor,2,',','.'),0,0,'R');
        $pdf->CellFitScale(25,4,number_format($iva,2,',','.'),0,0,'R');
        $pdf->CellFitScale(25,4,number_format($impoconsumo,2,',','.'),0,0,'R');
        $pdf->CellFitScale(25,4,number_format($ajuste,2,',','.'),0,0,'R');
        $pdf->CellFitScale(25,4,number_format($valorTotal,2,',','.'),0,0,'R');
	$pdf->Ln(4);
	###############################################################################################################################################################
	# Captura de valores totales
	#
	###############################################################################################################################################################
	$sumCantidad += $cantidad;
	$sumValor += $valor;
	$sumIva += $iva;
	$sumImpo += $impoconsumo;
	$sumAjuste += $ajuste;
	$sumValorT += $valorTotal;
}
###################################################################################################################################################################
# Impresión de valores totales
#
###################################################################################################################################################################
$pdf->SetFont('Courier','B',9);
$pdf->Cell(50,5,'Total a pagar:',1,0,'R');
$pdf->Cell(140,5,number_format($sumValorT,2,',','.'),1,0,'R');
###################################################################################################################################################################
# Total en numeros
#
###################################################################################################################################################################
$pdf->Ln(5);
$pdf->Cell(50,5,'Valor a pagar en letras:',1,0,'R');
$pdf->SetFont('Courier','',9);
$pdf->Cell(140,5,numtoletras($sumValorT),1,0,'L');
###################################################################################################################################################################
# Lineas para firma
#
###################################################################################################################################################################
$pdf->Ln(15);
$pdf->Cell(50,0,'',1);
$pdf->Ln(1);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(50,5,'FIRMA LIQUIDADOR',0,0,'C');
###################################################################################################################################################################
# Salida de pagina
####################################################################################################################################################################
ob_end_clean();															#Limpieza del buffer
$pdf->Output(0,'Informe_factura_'.$num_fat.'.pdf',0);		#Salida del documento
 ?>