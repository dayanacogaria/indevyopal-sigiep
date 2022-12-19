<?php 
##########################################################################################################################################
# Fecha de Creación : 01/02/2017
# Creado por : Jhon Numpaque
#
##########################################################################################################################################
# Modificaciones
##########################################################################################################################################
# Modificado por : 			Jhon Numpaque
# Fecha de modificación :	10/03/2017
# Descripción			:  	Se cambio metodo de filtrado de fechas
##########################################################################################################################################
# Modificado por : 			Jhon Numpaque
# Fecha de modificación :	06/03/2017
# Descripción			:  	Se cambio metodo de consulta por variables, autoincremento para suma de valores de comprobante cnt, para obtener quienes son de naturaleza debito y/o credito
##########################################################################################################################################
# Modificaciones
# Modificado por : 			Jhon Numpaque
# Fecha de modificación :	04/03/2017
# Descripción			:  	Se valido la consulta de impresión de registros, por naturaleza y se modifico la consulta haciendo agrupamiento por cuenta en el caso 
#							cnt
##########################################################################################################################################
header("Content-Type: text/html;charset=utf-8");
##########################################################################################################################################
# Llamdo de librerias
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
##########################################################################################################################################
# Apertura de tiempo limite de ejecución
ini_set('max_execution_time', 0);
##########################################################################################################################################
session_start();
ob_start();
$anno       = $_SESSION['anno'];
##########################################################################################################################################
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
			$this->MultiCell($w,5,$data[$i],'LTR',$a,$fill);
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
		##################################################################################################################
		# Array de meses
		#
		##################################################################################################################
		$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
		##################################################################################################################
		# Paginación
		#
		##################################################################################################################
		$this->SetFont('Arial','B',8);			
        $this->Cell(190,3,'Pagina '.$this->PageNo().PHP_EOL.'de'.PHP_EOL.'{nb}',0,0,'L');
        ##################################################################################################################
        # Fecha Actual
        #
        ##################################################################################################################
        $this->Ln(3);
        $mes = (int) date('m');
        $this->Cell(190,3,date('d').PHP_EOL.'de'.PHP_EOL.$meses[$mes].PHP_EOL.'de'.PHP_EOL.date('Y'),0,0,'L');
        ##################################################################################################################
        # Titulo
        #
        ##################################################################################################################
        $this->Ln(3);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,2,'Listados de comprobantes por tipo',0,0,'C');
        $this->SetY(20);
	}	
}
##########################################################################################################################################
# Declaración del objeto pdf con la clase mc_table
#
##########################################################################################################################################
$pdf = new PDF_MC_Table('P','mm','Letter');		#Creación del objeto pdf
$nb  = $pdf->AliasNbPages();					#Objeto de número de pagina
$pdf->AddPage();								#Agregar página
$pdf->SetFont('Arial','B',10);
##########################################################################################################################################
# Captura del variables enviadas
#
##########################################################################################################################################
$tipoCompInicial = $_POST['sltTipoComprobanteInicial'];
$tipoCompFinal = $_POST['sltTipoComprobanteFinal'];
$fechaInicial = explode("/",$_POST['txtFechaInicial']);
$fechaInicial = $fechaInicial[2]."-".$fechaInicial[1]."-".$fechaInicial[0];
$fechaFinal = explode("/",$_POST['txtFechaFinal']);
$fechaFinal = $fechaFinal[2]."-".$fechaFinal[1]."-".$fechaFinal[0];
##########################################################################################################################################
# Validación por la variable tipo recibida
#
##########################################################################################################################################
switch ($_GET['tipo']) {
	case 'cnt':	
	######################################################################################################################################
	# Consulta por tipo cnt
	#
	######################################################################################################################################
	#date_format(cnt.fecha,'%d/%m/%Y')  BETWEEN '$fechaInicial' AND '$fechaFinal' 
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
			##############################################################################################################################
			# Tipo de comprobante
			#
			##############################################################################################################################
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(55,5,(strtoupper($row[1])).PHP_EOL.$row[2],0,0,'L');			
			$pdf->Ln(8);
			##############################################################################################################################
			# Impresión de cabeza de listado
			#
			##############################################################################################################################
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(30,5,'No Comprobante',1,0,'L');
			$pdf->Cell(20,5,'Fecha',1,0,'L');
			$pdf->Cell(45,5,'Tercero',1,0,'L');
			$pdf->Cell(45,5,utf8_decode('Descripción'),1,0,'L');
			$pdf->Cell(30,5,'Valor Debito',1,0,'L');
			$pdf->Cell(30,5,'Valor Credito',1,0,'L');
			$pdf->Ln(5);
			##############################################################################################################################
			# Consulta de comprobates por tipo
			#
			##############################################################################################################################
			#date_format(cnt.fecha,'%d/%m/%Y')  BETWEEN '$fechaInicial' AND '$fechaFinal' 
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
                                        AND cnt.parametrizacionanno = $anno 
					AND 		dtc.valor IS NOT NULL
					ORDER BY 	cnt.numero ASC";
			$resultT = $mysqli->query($sqlT);
                        $total_debito   =0;
                        $total_credito  =0;
			while ($rowD = mysqli_fetch_row($resultT)) {
				$debito = 0;
				$credito = 0;
				##########################################################################################################################
				# Consulta de valores Debito por comprobante
				#
				##########################################################################################################################
				$sqlR = "SELECT 	cta.naturaleza,dtc.valor
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
							$debito += $rowV[1];
						}else{
							$credito += $rowV[1]*-1;
						}
					}
					if($rowV[0]==2){
						if($rowV[1]>0){
							$credito += $rowV[1];
						}else{
							$debito += $rowV[1]*-1;
						}
					}
				}								
				##########################################################################################################################
				# Impresión de valores en cnt
				#
				##########################################################################################################################
				$pdf->SetFont('Arial','',9);
				$pdf->SetWidths(array(30,20,45,45,30,30));
				$pdf->SetAligns(array('L','L','L','L','R','R'));
				$pdf->Row(array($rowD[1],$rowD[2],utf8_decode(ucwords(strtolower($rowD[3]))),utf8_decode(ucfirst(strtolower($rowD[4]))),number_format($debito,2,',','.'),number_format($credito,2,',','.')));
				$pdf->Ln(5);
                                $total_debito   +=$debito;
                                $total_credito  +=$credito;
			}
                        $pdf->SetFont('Arial','B',9);
                        $pdf->SetWidths(array(140,30,30));
                        $pdf->SetAligns(array('L','R','R'));
                        $pdf->Row(array('TOTAL '.(strtoupper($row[1])).' - '.$row[2],number_format($total_debito,2,',','.'),number_format($total_credito,2,',','.')));
                        $pdf->ln(5);
                        $total_d +=$total_debito;
                        $total_c +=$total_credito;
		}
                $pdf->SetFont('Arial','B',9);
                $pdf->SetWidths(array(140,30,30));
                $pdf->SetAligns(array('L','R','R'));
                $pdf->Row(array('TOTALES ',number_format($total_d,2,',','.'),number_format($total_c,2,',','.')));
                $pdf->ln(7);
		break;
	
	case 'pptal':
		##################################################################################################################################
		# Consulta por tipo comprobante pptal
		#
		##################################################################################################################################
		$sql = "SELECT DISTINCT	tpc.id_unico,tpc.nombre,tpc.codigo 
				FROM			gf_tipo_comprobante_pptal tpc 
				LEFT JOIN		gf_comprobante_pptal pptal ON	pptal.tipocomprobante = tpc.id_unico 
				LEFT JOIN 		gf_detalle_comprobante_pptal dtc ON dtc.comprobantepptal = pptal.id_unico
				WHERE			tpc.id_unico BETWEEN $tipoCompInicial AND $tipoCompFinal
				AND 			pptal.fecha >= '$fechaInicial' AND pptal.fecha <= '$fechaFinal' 
                                AND pptal.parametrizacionanno = $anno     
				AND 			dtc.valor IS NOT NULL";
		$result = $mysqli->query($sql);
		while ($row = mysqli_fetch_row($result)) {
			##############################################################################################################################
			# Tipo de comprobante
			#
			##############################################################################################################################
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(55,5,(strtoupper($row[1])).PHP_EOL.$row[2],0,0,'L');			
			$pdf->Ln(8);
			##############################################################################################################################
			# Impresión de cabeza de listado
			#
			##############################################################################################################################
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(30,5,'No Comprobante',1,0,'L');
			$pdf->Cell(20,5,'Fecha',1,0,'L');
			$pdf->Cell(60,5,'Tercero',1,0,'L');
			$pdf->Cell(60,5,utf8_decode('Descripción'),1,0,'L');
			$pdf->Cell(30,5,'Valor',1,0,'L');			
			$pdf->Ln(5);
			##############################################################################################################################
			# Consulta de comprobates por tipo
			#
			##############################################################################################################################
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
					AND		pptal.fecha >= '$fechaInicial' AND pptal.fecha <= '$fechaFinal' 
                                        AND             pptal.parametrizacionanno = $anno         
					AND 		dtc.valor IS NOT NULL
					ORDER BY 	pptal.numero ASC";
			$resultT = $mysqli->query($sqlT);
			while ($rowD = mysqli_fetch_row($resultT)) {
				##########################################################################################################################
				# Consulta de valor por comprobante
				#
				##########################################################################################################################
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
				##########################################################################################################################
				# Impresión de valores en cnt
				#
				##########################################################################################################################
				$pdf->SetFont('Arial','',9);
				$pdf->SetWidths(array(30,20,60,60,30));
				$pdf->SetAligns(array('L','L','L','L','R'));
				$pdf->Row(array($rowD[1],$rowD[2],mb_convert_encoding(ucwords(strtolower($rowD[3])),'utf-8'),mb_convert_encoding(ucwords(strtolower($rowD[4])),'utf-8'),number_format($valorT<0?$valorT*-1:$valorT,2,',','.')));
				$pdf->Ln(5);
			}
			$pdf->Ln(5);
		}
		break;
}
##########################################################################################################################################
# Final del documento			
#
##########################################################################################################################################
while (ob_get_length()) {
  ob_end_clean();
}
##########################################################################################################################################
# Salida del documento
$pdf->Output(0,'Listado_de_comprobantes_por_tipo.pdf',0);
 ?>