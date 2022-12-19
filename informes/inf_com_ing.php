<?php 
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 360);
session_start();
ob_start();
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
############### Modificaciones ##############################
# 16/02/2017 | 5:52  | Jhon Numpaque
# Se agrego espacio para linea de firma
##################################################################################################
# 07/02/2017 | 14:52  | Jhon Numpaque
# Cambio de diseño par amedia hoja
# Cambio de impresión de tipo relación de responsable documento
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
							date_format(comP.fecha,'%d/%m/%Y')
				FROM gf_comprobante_cnt cnt 
				LEFT JOIN gf_tipo_comprobante tpcnt ON cnt.tipocomprobante = tpcnt.id_unico
				LEFT JOIN gf_detalle_comprobante dtc ON dtc.comprobante = cnt.id_unico
				LEFT JOIN gf_detalle_comprobante_pptal dtcP ON dtc.detallecomprobantepptal = dtcP.id_unico
				LEFT JOIN gf_comprobante_pptal comP ON comP.id_unico = dtcP.comprobantepptal
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
$comprobantep=$_GET['idppt'];
#Consulta de comprobante pptal
$sqlComprobantePptal="SELECT 	pptal.id_unico,
								pptal.numero,
								pptal.fecha,
								date_format(pptal.fecha,'%d/%m/%Y'),
								pptal.descripcion,
								pptal.tipocomprobante,
								tipoPP.codigo,
								tipoPP.nombre,
								pptal.tercero 
					FROM gf_comprobante_pptal pptal 
					LEFT JOIN gf_tipo_comprobante_pptal tipoPP ON pptal.tipocomprobante = tipoPP.id_unico 
					WHERE md5(pptal.id_unico)='$comprobantep'";
$pptal=$mysqli->query($sqlComprobantePptal);
#Array de asignación de valores de consulta comprobante pptal
$compptal=mysqli_fetch_row($pptal);
#Asignación de valores de consulta de comprobante presupuestal
$idCompPptal=$compptal[0];		#Id comprobante presupuestal
$numeroPptal=$compptal[1];		#Número comprobante presupuestal
$fechaPptal=$compptal[2];		#Fecha comprobante presupuestal
$fechavenPptal=$compptal[3];	#Fecha de vencimiento de comprobnate presupuestal
$descripcPptal=$compptal[4];	#Descripción comprobante presupuestal
$tipoCompPtal=$compptal[5];		#Tipo comprobante presupuestal
$codigoCompptal=$compptal[6];	#Código de comprobante presupuestal
$nombreCompptal=$compptal[7];	#Nombre de comprobnate presupuestal
$terceroCompptal=$compptal[8];	#Tercero de comprobante presupuestal
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
                global $numPag;
                $numPag = $this->PageNo();
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
		$this->ln(5);		
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
$pdf->Ln(10);
$pdf->Cell(190,12,'',1,0);
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
$pdf->Cell(90,5,$diaC.PHP_EOL.'de'.PHP_EOL.$meses[$mesC].PHP_EOL.'de'.PHP_EOL.$annoC,0,0,'L');
#Etiqueta de vencimiento
$pdf->SetFont('Courier','B',10);
$pdf->Cell(50,5,'VENCE:',0,0,'R');
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
$pdf->Cell(90,5, utf8_decode(ucwords(mb_strtolower($tercero))),0,0,'L');
#Etiqueta de Tipo de documento
$pdf->SetFont('Courier','B',10);
$pdf->Cell(50,5,'C.C / NIT:',0,0,'R');
#Impresión de número de documento
$pdf->SetFont('Courier','',10);
$pdf->Cell(25,5,$identif,0,0,'L');
#Salto de linea
$pdf->Ln(5);
#Celda para descripción
$pdf->Cell(190,10,'',1,0);
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
$pdf->Cell(190,5,'',0,0);
$pdf->Ln(0);
$pdf->SetFont('Courier','B',7);
$pdf->Cell(35,9,'CONCEPTO',1,0,'L');
$pdf->Cell(45,9,'RUBRO FUENTE',1,0,'L');
$pdf->Cell(35,9,'CUENTA',1,0,'L');
$pdf->Cell(20,9,'DEBITO',1,0,'L');
$pdf->Cell(20,9,'CREDITO',1,0,'L');
$pdf->Cell(12,9,"",1,0,'L');
$pdf->Cell(23,9,"TERCERO",1,0,'L');
$pdf->SetX(10);
$pdf->Cell(35,5,'',0,0,'L');
$pdf->Cell(45,5,'',0,0,'L');
$pdf->Cell(35,5,'',0,0,'L');
$pdf->Cell(20,5,'',0,0,'L');
$pdf->Cell(20,5,'',0,0,'L');
$pdf->Cell(12,5,"CENTRO",0,0,'C');
$pdf->Cell(23,5,"",0,0,'L');
$pdf->Ln(4);
$pdf->Cell(35,5,'',0,0,'L');
$pdf->Cell(45,5,'',0,0,'L');
$pdf->Cell(35,5,'',0,0,'L');
$pdf->Cell(20,5,'',0,0,'L');
$pdf->Cell(20,5,'',0,0,'L');
$pdf->Cell(12,5,"COSTO",0,0,'C');
$pdf->Cell(23,5,"",0,0,'L');
$pdf->Ln(0);
#Consulta de detalle presupuestal y contable
$detallP="	SELECT DISTINCT 
							dtc.id_unico, 
                            ct.id_unico, 
                            ct.nombre, 
                            rb.id_unico rubro, 
                            rb.codi_presupuesto, 
                            rb.nombre, 
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
                            dtc.tercero,
                            pptal.id_unico,
                            ft.nombre
            FROM gf_detalle_comprobante dtc 
            LEFT JOIN gf_detalle_comprobante_pptal pptal ON dtc.detallecomprobantepptal = pptal.id_unico 
            LEFT JOIN gf_concepto_rubro cnr ON pptal.conceptoRubro = cnr.id_unico 
            LEFT JOIN gf_concepto ct ON cnr.concepto = ct.id_unico 
            LEFT JOIN gf_rubro_fuente rbf ON rbf.id_unico = pptal.rubrofuente 
            LEFT JOIN gf_rubro_pptal rb ON rbf.rubro = rb.id_unico 
            LEFT JOIN gf_fuente ft ON rbf.fuente = ft.id_unico 
            LEFT JOIN gf_concepto_rubro_cuenta ctrb ON cnr.id_unico = ctrb.concepto_rubro 
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
    $pagA=$numPag;
    
	#Validación de valor por naturaleza
	if($filaDP[9]==1){
		if($filaDP[10] > 0){			
			$deb = number_format($filaDP[10],2,'.',',');
			$cre = '0.00';
			#Inicia captura suma de totales 
			$sumD+=$filaDP[10];
			$sumC+=$cre;
		}else{
			$x = (float) substr($filaDP[10],'1');
			$deb = '0.00';
			$cre = number_format($x,2,'.',',');
			$sumD+=$deb;
			$sumC+=$x;
		}
	}else if($filaDP[9]==2){
		if($filaDP[10] > 0){
			$deb = '0.00';
			$cre = number_format($filaDP[10],2,'.',',');
			#Inicia captura suma de totales 
			$sumC+=$filaDP[10];
			$sumD+=$deb;		
		}else{
			$x = (float) substr($filaDP[10],'1');
			$deb = number_format($x,2,'.',',');
			$cre = '0.00';
			#Inicia captura suma de totales 
			$sumD+=$x;
			$sumC+=$cre;
		}
	}
	#Validación de campo vacio en concepto para imprimir fuente
	if(!empty($filaDP[2])){
		$rubro = $filaDP[4].' '.$filaDP[5]." - ".$filaDP[18];
	}else{
		$rubro = "";
	}
	$cuenta = $filaDP[7].' - '.$filaDP[8];
	#Query para consultar tercero
	$sqlTercero="SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
        (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE'
        ,ter.numeroidentificacion FROM gf_tercero ter WHERE ter.id_unico = $filaDP[16]";
	$resultTer=$mysqli->query($sqlTercero);
	#Asignación de valor de tercero
	$ter=mysqli_fetch_row($resultTer);
	#Salto de linea
	$pdf->Ln(5);
	#Tipo de letra
	$pdf->SetFont('Courier','',7);
        
	#Llamado de clase de anchos y definición de anchos de columnas
	$pdf->SetWidths(array(35,45,35,20,20,12,23));
	#Definición de alinamientos y cosntrucción de array
	$pdf->SetAligns(array('L','L','L','R','R','L'));
	#Llamado de clase filla y consutrucción de array con datos a imprimir
	$pdf->Row(array(utf8_decode(ucwords(mb_strtolower($filaDP[2]))),utf8_decode(ucwords(mb_strtolower($rubro))),utf8_decode($cuenta),$deb,$cre,utf8_decode($filaDP[14]),utf8_decode(ucwords(mb_strtolower($ter[0])))));
	
}
#Salto de linea
$pdf->Ln(5);
#Fila en blanco para totales
$pdf->Cell(190,5,'',0,0);
#Salto de linea 
$pdf->Ln(0);
#Tipo de texto
$pdf->SetFont('Courier','B',9);
#Impresión de celda totales
$pdf->Cell(115,5,'Totales',1,0,'R');
#Tipo de letra
$pdf->SetFont('Courier','',7);
#Impresión de valores totales en Débito y Crédito
$pdf->CellFitScale(20,5,number_format($sumD,2,'.',','),1,0,'L');
$pdf->CellFitScale(20,5,number_format($sumC,2,'.',','),1,0,'L');
#Consulta pra obtener generar los datos de firma
$sqlR = "SELECT	CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos),car.nombre,ti.nombre,ter.numeroidentificacion,tpr.nombre
FROM 	gf_tipo_comprobante tpc 
LEFT JOIN gf_tipo_documento tpd ON tpd.id_unico = tpc.tipodocumento 
LEFT JOIN gf_responsable_documento doc ON doc.tipodocumento = tpc.tipodocumento
LEFT JOIN gf_tipo_responsable tpr ON tpr.id_unico = doc.tiporesponsable
LEFT JOIN gg_tipo_relacion tprl ON doc.tipo_relacion = tprl.id_unico
LEFT JOIN gf_tercero ter ON doc.tercero = ter.id_unico
LEFT JOIN gf_cargo_tercero cter ON cter.tercero = ter.id_unico
LEFT JOIN gf_cargo car ON cter.cargo = car.id_unico
LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
WHERE tpc.id_unico=$idtipoComp
ORDER BY doc.tipodocumento";
#Ejecutamos la consulta
$resultF= $mysqli->query($sqlR);
$resultF1= $mysqli->query($sqlR);
$altofinal = $pdf->GetY();
$altop = $pdf->GetPageHeight();
$altofirma = $altop-$altofinal;
$pdf->Ln(15);
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
			$pdf->SetFont('Courier','B',10);
			#Linea para firma
			#$pdf->Cell(60,0,'',1);
			#Varibles x,y
			$x = $pdf->GetX();
			$y = $pdf->GetY();				
			#Salto de linea
			$pdf->Ln(7);
			$pdf->setX($xm);
			#Impresión de responsable de documento
			$pdf->Cell(190,2,utf8_decode($firma[4]),0,0,'L');
			#Salto de linea
			$pdf->Ln(15);
			$pdf->setX($xm);
			#Linea para firma
			$pdf->Cell(60,0,'',1);
			$pdf->Ln(3);
			$pdf->setX($xm);
			$pdf->Cell(190,2,utf8_decode($firma[0]),0,0,'L');
			#Tipo de texto
			#$pdf->SetFont('Courier','',8);
			#Impresión de tipo de documento y numero documento
			#$pdf->Cell(190,2,$firma[2].utf8_decode(PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
			#$pdf->Cell(190,2,utf8_decode($firma[2].PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
			#Salto de linea
			$pdf->Ln(3);
			$pdf->setX($xm);
			$pdf->Cell(190,2,($firma[1]),0,0,'L');
			#Tipo de texto
			#$pdf->Cell(190,2,utf8_decode($firma[4]),0,0,'L');
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
			#Varibles x,y
			$x = $pdf->GetX();
			#alto inicial
			$y = $pdf->GetY();
			#Salto de linea
			$pdf->Ln(7);
			$pdf->setX($xn);
			#Impresión de responsable de documento
			$pdf->Cell(190,2,utf8_decode($firma[4]),0,0,'L');
			#Salto de linea
			$pdf->Ln(15);
			$pdf->setX($xn);
			#Tipo de texto
			#$pdf->SetFont('Courier','',8);
			#Linea para firma
			$pdf->Cell(60,0,'',1);
			$pdf->Ln(3);
			$pdf->setX($xn);
			#Impresión de tipo de documento y numero documento
			$pdf->Cell(190,2,utf8_decode($firma[0]),0,0,'L');
			#$pdf->Cell(190,2,utf8_decode($firma[2].PHP_EOL.':'.PHP_EOL.$firma[3]),0,0,'L');
			#Salto de linea
			$pdf->Ln(3);
			$pdf->setX($xn);
			$pdf->Cell(190,2,($firma[1]),0,0,'L');
			#Tipo de texto
			#$pdf->SetFont('Courier','B',8);
			#Impresión de cargo de responsable de documento
			#$pdf->Cell(190,2,utf8_decode($firma[4].' : '.$firma[0]),0,0,'L');
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
	

#Final del documento			
while (ob_get_length()) {
  ob_end_clean();
}			#Limpieza del buffer
#Salida del documento
$pdf->Output(0,'Informe_comprobante_ingreso ('.$numComp.').pdf',0);
