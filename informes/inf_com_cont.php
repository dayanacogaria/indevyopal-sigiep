<?php 
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 360);
session_start();
ob_start();
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
############### Creación ####################################
# 14/02/2017 | Jhon Numpaque
############### Modificaciones ###################################################################################################################################
# Fecha : 01/02/2017
# Hora  : 3:49 p.m
# Modificado por : Jhon Numpaque
# Descripción : Se aumento el tamaño del ancho a 200px y se aumento tamaño de celdas de credito y debtio
#
##################################################################################################################################################################
# 14/02/2017 
# 11:45
# Descripción: Se quito consulta de firma y se ubico una sola linea debido a que este puede tener multiples firmantes
#Comprobante cnt
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
/**
* Clase pdf con herencia a fpdf
*/
class PDF_MC_Table extends FPDF{
	var $widths;
	var $aligns;
	function SetWidths($w){
		//Set the array of column widths
		$this->widths=$w;
	}
	function SetAligns($a){
		//Set the array of column alignments
		$this->aligns=$a;
	}
	function fill($f){
		//juego de arreglos de relleno
		$this->fill=$f;
	}
	function Row($data){
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
		$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=5*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++){
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			$this->Rect($x,$y,$w,$h,$style);
			//Print the text
			$this->MultiCell($w,3,$data[$i],'LTR',$a,$fill);
			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h-5);
	}
	function CheckPageBreak($h){
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
	}
	function NbLines($w,$txt){
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace('\r','',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=='\n')
			$nb–;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb){
			$c=$s[$i];
			if($c=='\n'){
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c=='')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax){
				if($sep==-1){
					if($i==$j)
						$i++;
				}else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}else
				$i++;
			}
		return $nl;
	}
	
	#Funcón cabeza de la página
	function header(){
		#Redeclaración de varibles
		global $razonsocial;	#Nombre de compañia	
		global $tipoIdnComp;	#Tipo de identificación
		global $nombreTipoComp;	#Nombre de comprobante
		global $numComp;		#Número de comprobante
		global $ruta;			#Ruta de logo
		global $numeroIdent;	#Numero identificacion
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
		$this->Cell(200,5,utf8_decode($tipoIdnComp.':'.PHP_EOL.$numeroIdent),0,0,'C');					
		#Tipo de comprobante y número de comprobante
		$this->Ln(5);		
		$this->SetFont('Courier','B',10);
		$this->Cell(200,5,utf8_decode(ucwords(strtoupper($nombreTipoComp.PHP_EOL))).'Nro:'.PHP_EOL.$numComp,0,0,'C');
		$this->Ln(5);
	}	
}

$pdf = new PDF_Mc_Table('P','mm','Letter');		#Creación del objeto pdf
$nb=$pdf->AliasNbPages();		#Objeto de número de pagina
$pdf->AddPage();				#Agregar página
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
$pdf->Ln(5);
$pdf->Cell(200,12,'',1,0);
#Fecha de comprobante
$pdf->Ln(0);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(25,5,'FECHA:',0,'L');
#Conversión de fecha
$fechaD=explode("-",$fechaComp);			
$diaC =$fechaD[2];				#Dia
$mesC = (int) $fechaD[1];		#Mes
$annoC= $fechaD[0];				#Año
#Impresión de fecha
$pdf->SetFont('Courier','',10);
$pdf->Cell(110,5,$diaC.PHP_EOL.'de'.PHP_EOL.$meses[$mesC].PHP_EOL.'de'.PHP_EOL.$annoC,0,0,'L');
#Etiqueta de vencimiento
$pdf->SetFont('Courier','B',10);
$pdf->Cell(40,5,'VENCE:',0,0,'R');
#Valor de fecha de vencimiento
$pdf->SetFont('Courier','',10);
$pdf->Cell(25,5,$fechavencimientoC,0,0,'L');
#Salto de linea
$pdf->Ln(7);
#Etiqueta recibi
$pdf->SetFont('Courier','B',10);
$pdf->Cell(25,5,'RECIBI DE:',0,'L');
#Impresion de tercero
$pdf->SetFont('Courier','',10);
$pdf->CellFitScale(110,5,utf8_decode($tercero),0,0,'L');
#Etiqueta de Tipo de documento
$pdf->SetFont('Courier','B',10);
$pdf->Cell(40,5,'C.C / NIT:',0,0,'R');
#Impresión de número de documento
$pdf->SetFont('Courier','',10);
$pdf->Cell(25,5,$identif,0,0,'L');
#Salto de linea
$pdf->Ln(5);
#Celda para descripción
$pdf->Cell(200,10,'',1,0);
#Etiqueta de descripción
$pdf->Ln(0);
$pdf->SetFont('Courier','B',9);
$pdf->Cell(25,5,utf8_decode('DESCRIPCIÓN:'),0,0,'L');
#Impresión de la descripción
$pdf->SetFont('Courier','',9);
$pdf->Multicell(165,5,utf8_decode($desComp),0,'L');
#Salto de linea
$pdf->Ln(5);
#Celda de movimiento presupuestal en vácio
$pdf->Cell(200,5,'',0,0);
$pdf->Ln(0);
$pdf->SetFont('Courier','B',7);
$pdf->Cell(47,9,'CUENTA',1,0,'C');
$pdf->Cell(25,9,'DEBITO',1,0,'C');
$pdf->Cell(25,9,'CREDITO',1,0,'C');
$pdf->Cell(20,9,"",1,0,'C');
$pdf->Cell(30,9,"PROYECTO",1,0,'C');
$pdf->Cell(53,9,"TERCERO",1,0,'C');
$pdf->SetX(10);
$pdf->Cell(47,5,'',0,0,'C');
$pdf->Cell(25,5,'',0,0,'C');
$pdf->Cell(25,5,'',0,0,'C');
$pdf->Cell(20,5,"CENTRO",0,0,'C');
$pdf->Cell(30,9,"",0,0,'C');
$pdf->Cell(53,5,"",0,0,'C');
$pdf->Ln(4);
$pdf->Cell(47,5,'',0,0,'C');
$pdf->Cell(25,5,'',0,0,'C');
$pdf->Cell(25,5,'',0,0,'C');
$pdf->Cell(20,5,"COSTO",0,0,'C');
$pdf->Cell(30,9,"",0,0,'C');
$pdf->Cell(53,5,"",0,0,'C');
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
$pdf->Ln(5);
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
	#Salto de linea
	

	#Tipo de letra
	$pdf->SetFont('Courier','',7);
	#Llamado de clase de anchos y definición de anchos de columnas
	$pdf->SetWidths(array(47,25,25,20,30,53));
	#Definición de alinamientos y cosntrucción de array
	$pdf->SetAligns(array('L','R','R','L','L','L'));
	#Llamado de clase filla y consutrucción de array con datos a imprimir
	$nombr = ucwords(strtolower($ter[0]));
	$nombr = utf8_decode($nombr);
	$pdf->Row(array(utf8_decode($cuenta),$deb,$cre,$filaDP[9],ucwords(strtolower($filaDP[7])), $nombr));
	$pdf->Ln(5);
}
#Salto de linea
$pdf->Ln(5);
#Fila en blanco para totales
$pdf->Cell(200,5,'',0,0);
#Salto de linea 
$pdf->Ln(0);
#Tipo de texto
$pdf->SetFont('Courier','B',9);
#Impresión de celda totales
$pdf->Cell(47,5,'Totales',1,0,'R');
#Tipo de letra
$pdf->SetFont('Courier','',7);
#Impresión de valores totales en Débito y Crédito
$pdf->Cell(25,5,number_format($sumD,2,'.',','),1,0,'L');
$pdf->Cell(25,5,number_format($sumC,2,'.',','),1,0,'L');
$pdf->Ln(15);
$pdf->Cell(70,0,'',1);
$pdf->Ln(2);
$pdf->Cell(200,2,'FIRMA',0,0,'L');
/*#Consulta pra obtener generar los datos de firma
$sqlR = "SELECT	CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos),car.nombre,ti.nombre,ter.numeroidentificacion,tprl.nombre
FROM 	gf_tipo_comprobante tpc 
LEFT JOIN gf_tipo_documento tpd ON tpd.id_unico = tpc.tipodocumento 
LEFT JOIN gf_responsable_documento doc ON doc.tipodocumento = tpc.tipodocumento
LEFT JOIN gf_tipo_responsable tpr ON tpr.id_unico = doc.tiporesponsable
LEFT JOIN gg_tipo_relacion tprl ON doc.tipo_relacion = tprl.id_unico
LEFT JOIN gf_tercero ter ON doc.tercero = ter.id_unico
LEFT JOIN gf_cargo_tercero cter ON cter.tercero = ter.id_unico
LEFT JOIN gf_cargo car ON cter.cargo = car.id_unico
LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
WHERE tpc.id_unico=$idtipoComp";
#Ejecutamos la consulta
$resultF= $mysqli->query($sqlR);
$resultF1= $mysqli->query($sqlR);
$altofinal = $pdf->GetY();
$altop = $pdf->GetPageHeight();
$altofirma = $altop-$altofinal;

#Carga de array $firma con los valores de consulta
#Salto de linea
$c=0;
while($cons = mysqli_fetch_row($resultF1)){
	$c++;
	}

	$tfirmas = ($c/2) * 33;
	
	if($tfirmas>$altofirma)
			$pdf->AddPage();
		
		$xt=10;	
		while($firma = mysqli_fetch_row($resultF)){
		if($xt<50){
			#Construcción de linea firma
			$xm = 10; 
			$pdf->setX($xm);
			$pdf->SetFont('Courier','',10);
			#Linea para firma
			#$pdf->Cell(60,0,'',1);
			#Varibles x,y
			$x = $pdf->GetX();
			$y = $pdf->GetY();				
			#Salto de linea
			$pdf->Ln(3);
			$pdf->setX($xm);
			#Impresión de responsable de documento
			$pdf->Cell(190,2,utf8_decode($firma[4].' : '.$firma[0]),0,0,'L');
			#Salto de linea
			$pdf->Ln(3);
			$pdf->setX($xm);
			#Tipo de texto
			#$pdf->SetFont('Courier','',8);
			#Impresión de tipo de documento y numero documento
			#$pdf->Cell(190,2,$firma[2].utf8_decode(PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
			#$pdf->Cell(190,2,utf8_decode($firma[2].PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
			#Salto de linea
			$pdf->Ln(3);
			$pdf->setX($xm);
			#Tipo de texto
			#$pdf->SetFont('Courier','B',8);
			#Impresión de cargo de responsable de documento
			#$pdf->Cell(190,2,utf8_decode($firma[1]),0,0,'L');
			#$pdf->setX($xm);
			#Obtención de alto final				
			$x2 = $pdf->GetX();				
			#Posición final de firma 2		
			$pdf->Ln(0);
			$xt = 120;
		}else{
			$xn = 120;
			$pdf->SetY($y);
			#Construcción de linea firma
			$pdf->SetFont('Courier','B',10);
			$pdf->setX($xn);
			#Linea para firma
			$pdf->Cell(60,0,'',1);
			#Varibles x,y
			$x = $pdf->GetX();
			#alto inicial
			$y = $pdf->GetY();
			#Salto de linea
			$pdf->Ln(3);
			$pdf->setX($xn);
			#Impresión de responsable de documento
			$pdf->Cell(190,2,utf8_decode($firma[0]),0,0,'L');
			#Salto de linea
			$pdf->Ln(3);
			$pdf->setX($xn);
			#Tipo de texto
			#$pdf->SetFont('Courier','',8);
			#Impresión de tipo de documento y numero documento
			#$pdf->Cell(190,2,$firma[2].utf8_decode(PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
			#$pdf->Cell(190,2,utf8_decode($firma[2].PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
			#Salto de linea
			$pdf->Ln(3);
			$pdf->setX($xn);
			#Tipo de texto
			$pdf->SetFont('Courier','B',8);
			#Impresión de cargo de responsable de documento
			$pdf->Cell(190,2,utf8_decode($firma[1]),0,0,'L');
			#Obtención de alto final			
			$x2 = $pdf->GetX();
			#Posición del ancho 		
			$posicionY = $y-20;
			#Ubicación firma 2
			$pdf->SetXY($x2,$posicionY);
			#Posición final de firma
			$xt = 0;
		}
	}
	*/

#Final del documento			
ob_end_clean();				#Limpieza del buffer
#Salida del documento
$pdf->Output(0,'Informe_comprobante_ingreso ('.$numComp.').pdf',0);
