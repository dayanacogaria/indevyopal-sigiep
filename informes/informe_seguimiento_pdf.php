<?php 
###############MODIFICACIONES###############################
#02/03/2017 | ERICA G. | ARCHIVO CREADO
############################################################
header("Content-Type: text/html;charset=utf-8");
# Llamdo de librerias
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
# Apertura de tiempo limite de ejecución
ini_set('max_execution_time', 0);
session_start();
ob_start();
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];


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
			$this->MultiCell($w,4,$data[$i],'LTR',$a,$fill);
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
        # Array de meses
        $meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
        # Paginación
        $this->SetFont('Arial','B',8);			
        $this->Cell(190,3,'Pagina '.$this->PageNo().PHP_EOL.'de'.PHP_EOL.'{nb}',0,0,'L');
        # Fecha Actual
        $this->Ln(3);
        $mes = (int) date('m');
        $this->Cell(190,3,date('d').PHP_EOL.'de'.PHP_EOL.$meses[$mes].PHP_EOL.'de'.PHP_EOL.date('Y'),0,0,'L');
        # Titulo
        $this->Ln(3);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,2,'Informe Seguimiento Disponiblidad',0,0,'C');
        $this->SetY(20);
	}	
}
## Función de consulta recursiva para imprimir los detalles de comprobante en disponibilidad presupuestal
function documento($id,$rubroF,$pdf,$valor){
	# LLmado de conexión
	require'../Conexion/conexion.php';	
	# Llamado de librerias para saldo
	require_once('../estructura_apropiacion.php');
	# Consulta de datos de comprobantes que afectan al detalle
	if(!empty($id)){
		$sqlD = "SELECT 	tpc.codigo,
							comp.numero,
							date_format(comp.fecha,'%d/%m/%Y'),
							comp.descripcion,
							IF(CONCAT_WS(' ',
                                                          ter.nombreuno,
                                                          ter.nombredos,
                                                          ter.apellidouno,
                                                          ter.apellidodos) 
                                                          IS NULL OR CONCAT_WS(' ',
                                                          ter.nombreuno,
                                                          ter.nombredos,
                                                          ter.apellidouno,
                                                          ter.apellidodos) = '',
                                                        (ter.razonsocial),
                                                        CONCAT_WS(' ',
                                                          ter.nombreuno,
                                                          ter.nombredos,
                                                          ter.apellidouno,
                                                          ter.apellidodos)) AS NOMBRE,
							(dtp.valor),
							dtp.comprobanteafectado,
							dtp.rubrofuente,
							dtp.id_unico
				FROM		gf_comprobante_pptal comp 
				LEFT JOIN 	gf_detalle_comprobante_pptal dtp 	ON comp.id_unico 	= dtp.comprobantepptal
				LEFT JOIN	gf_tipo_comprobante_pptal tpc		ON tpc.id_unico 	= comp.tipocomprobante
				LEFT JOIN 	gf_tercero ter						ON ter.id_unico 	= comp.tercero
				WHERE		dtp.comprobanteafectado	=$id 
				AND 		dtp.rubrofuente =$rubroF";	
	}
	$resultD = $mysqli->query($sqlD); 	
	while ($r = mysqli_fetch_row($resultD)) {
		##########################################################################################################################################################
		# obtención de saldo disponible
		$saldo = $valor-$r[5]<0?'0':$valor-$r[5];
		##########################################################################################################################################################
		# Impresión de comprobantes que afectan al detalle
		$pdf->SetFont('Arial','',7);
		$pdf->SetWidths(array(20,20,20,60,60,20));
		$pdf->SetAligns(array('L','L','L','L','L','R'));
		$pdf->Row(array($r[0],$r[1],$r[2],utf8_decode($r[3]),utf8_decode($r[4]),number_format($r[5],2,',','.')));
		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(180,5,'Total Afectaciones ',0,0,'R');
		$pdf->Cell(20,5,number_format($r[5],2,',','.'),0,0,'R');
		$pdf->Ln(5);
		$pdf->Cell(180,5,'Saldo '.$r[0],0,0,'R');
		$pdf->Cell(20,5,number_format($saldo,2,',','.'),0,0,'R');
		$pdf->Ln(5);
		# Validamos si detalleafecatdo es diferente de nulo
		if(!empty($r[6])){
			documento($r[8],$r[7],$pdf, $r[5]);
		}	
	}
}

# Declaración del objeto pdf con la clase mc_table
$pdf = new PDF_MC_Table('P','mm','Letter');		#Creación del objeto pdf
$nb  = $pdf->AliasNbPages();					#Objeto de número de pagina
$pdf->AddPage();								#Agregar página
$pdf->SetFont('Arial','B',10);

# Captura de parametro de session compania
$compania = $_SESSION['compania'];
# Consulta de logo de compañia
$sqlC = "SELECT compn.ruta_logo,compn.razonsocial
		FROM 	gf_tercero compn 
		WHERE 	compn.id_unico = $compania";
$resultC = 	$mysqli->query($sqlC);
$comp =		mysqli_fetch_row($resultC);
$logo = 	$comp[0];


##############################################################################################################################################################
#CONSULTAR LOS COMPROBANTES POR VARIABLES DE ENTRADA
#SI VIENE VACIO LAS DOS
if(empty($_POST['tipo'])) { 
        if(empty($_POST['disI']) && empty($_POST['disF'])){
        $consulta = "SELECT DISTINCT 
          cp.id_unico 
        FROM
          gf_comprobante_pptal cp
        LEFT JOIN
          gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
        LEFT JOIN
          gf_tercero ter ON cp.tercero = ter.id_unico
        WHERE
          tc.clasepptal = 14 AND tc.tipooperacion = 1 
          AND cp.parametrizacionanno = $anno 
        ORDER BY
          cp.numero ASC";
        } else {
            ####SI TRAE SOLO DISI#####
            if(!empty($_POST['disI']) && empty($_POST['disF'])){
            $disI=$_POST['disI'];
            $consulta = "SELECT DISTINCT 
                cp.id_unico 
              FROM
                gf_comprobante_pptal cp
              LEFT JOIN
                gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
              LEFT JOIN
                gf_tercero ter ON cp.tercero = ter.id_unico
              WHERE
                tc.clasepptal = 14 AND tc.tipooperacion = 1 AND numero >='$disI' 
                AND cp.parametrizacionanno = $anno 
              ORDER BY
                cp.numero ASC ";
            } else {
                ##SI TRAE SOLO DISF##
                if(empty($_POST['disI']) && !empty($_POST['disF'])){

                    $disF=$_POST['disF'];
                    $consulta = "SELECT DISTINCT 
                        cp.id_unico 
                      FROM
                        gf_comprobante_pptal cp
                      LEFT JOIN
                        gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
                      LEFT JOIN
                        gf_tercero ter ON cp.tercero = ter.id_unico
                      WHERE
                        tc.clasepptal = 14 AND tc.tipooperacion = 1 AND numero <='$disF'
                        AND cp.parametrizacionanno = $anno  
                      ORDER BY
                        cp.numero ASC ";
                } else {
                    $disI=$_POST['disI'];
                    $disF=$_POST['disF'];
                    $consulta = "SELECT DISTINCT 
                        cp.id_unico 
                      FROM
                        gf_comprobante_pptal cp
                      LEFT JOIN
                        gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
                      LEFT JOIN
                        gf_tercero ter ON cp.tercero = ter.id_unico
                      WHERE
                        tc.clasepptal = 14 AND tc.tipooperacion = 1 AND numero BETWEEN '$disI' AND '$disF' 
                        AND cp.parametrizacionanno = $anno  
                      ORDER BY
                        cp.numero ASC ";
                }
            }
    }
} else {
    $tipo = $_POST['tipo'];
    if(empty($_POST['disI']) && empty($_POST['disF'])){
    $consulta = "SELECT DISTINCT 
      cp.id_unico 
    FROM
      gf_comprobante_pptal cp
    LEFT JOIN
      gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
    LEFT JOIN
      gf_tercero ter ON cp.tercero = ter.id_unico
    WHERE
      tc.id_unico  ='$tipo'    
      AND cp.parametrizacionanno = $anno 
    ORDER BY
      cp.numero ASC";
    } else {
        ####SI TRAE SOLO DISI#####
        if(!empty($_POST['disI']) && empty($_POST['disF'])){
        $disI=$_POST['disI'];
        $consulta = "SELECT DISTINCT 
            cp.id_unico 
          FROM
            gf_comprobante_pptal cp
          LEFT JOIN
            gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
          LEFT JOIN
            gf_tercero ter ON cp.tercero = ter.id_unico
          WHERE
            tc.id_unico  ='$tipo'  AND numero >='$disI' 
            AND cp.parametrizacionanno = $anno 
          ORDER BY
            cp.numero ASC ";
        } else {
            ##SI TRAE SOLO DISF##
            if(empty($_POST['disI']) && !empty($_POST['disF'])){

                $disF=$_POST['disF'];
                $consulta = "SELECT DISTINCT 
                    cp.id_unico 
                  FROM
                    gf_comprobante_pptal cp
                  LEFT JOIN
                    gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
                  LEFT JOIN
                    gf_tercero ter ON cp.tercero = ter.id_unico
                  WHERE
                    tc.id_unico ='$tipo'  AND numero <='$disF' 
                    AND cp.parametrizacionanno = $anno 
                  ORDER BY
                    cp.numero ASC ";
            } else {
                $disI=$_POST['disI'];
                $disF=$_POST['disF'];
                $consulta = "SELECT DISTINCT 
                    cp.id_unico 
                  FROM
                    gf_comprobante_pptal cp
                  LEFT JOIN
                    gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
                  LEFT JOIN
                    gf_tercero ter ON cp.tercero = ter.id_unico
                  WHERE
                    tc.id_unico ='$tipo'  AND numero BETWEEN '$disI' AND '$disF' 
                    AND cp.parametrizacionanno = $anno  
                  ORDER BY
                    cp.numero ASC ";
            }
        }
    }
}


$consulta = $mysqli->query($consulta);
$numDis= mysqli_num_rows($consulta);
while ($row1 = mysqli_fetch_row($consulta)) {
    
$numDis=$numDis-1;

# Variable capturada por get
$id_Pptal = $row1[0];

# Consulta de comprobante
 $sql = "SELECT 		comP.id_unico,
					tipCom.nombre,
					tipCom.codigo,
					comP.numero,
					date_format(comP.fecha,'%d/%m/%Y'),
					SUM(dtp.valor),
					CONCAT(ELT(WEEKDAY( comP.fecha) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA 
		FROM 		gf_comprobante_pptal comP
		LEFT JOIN	gf_tipo_comprobante_pptal tipCom
		ON 			tipCom.id_unico =	comP.tipocomprobante
		LEFT JOIN	gf_detalle_comprobante_pptal dtp	
		ON 			dtp.comprobantepptal = 	comP.id_unico
		WHERE	comP.id_unico = '$id_Pptal'
		";
$result = $mysqli->query($sql);
$row = mysqli_fetch_row($result);
# Obtención de valores
$id 	= $row[0];
$tipo 	= $row[2].' - '.$row[1];
$numero = $row[3];
$fecha 	= $row[4];
$valor  = $row[5];
$diaF 	= $row[6];



# Impresión de ruta
if($logo != '') {
	$pdf->Image('../'.$logo,10,18,20);
}
$pdf->SetFont('Arial','B',10);
# Impresión de cabeza
$pdf->Ln(5);
# Impresión de tipo
$pdf->Cell(65,5,'Tipo:',0,0,'R');
$pdf->Cell(60,5,$tipo,0,0,'L');
# Impresión de número de comprobante
$pdf->Ln(5);
$pdf->Cell(65,5,'Numero :',0,0,'R');
$pdf->Cell(60,5,$numero,0,0,'L');
# Impresión de fecha de comprobante
$pdf->Ln(5);
$pdf->Cell(65,5,'Fecha :',0,0,'R');
$pdf->Cell(60,5,$fecha,0,0,'L');
# Valor
$pdf->Ln(5);
$pdf->Cell(65,5,'Valor :',0,0,'R');
$pdf->Cell(60,5,number_format($valor,2,'.',','),0,0,'L');
$pdf->Ln(5);
# Consulta de código, nombre y valor de rubros
 $sqlRB = "SELECT	dtcp.id_unico,
					rub.codi_presupuesto,
					rub.nombre,
					fte.nombre,
					dtcp.valor,
					dtcp.comprobanteafectado,
					dtcp.rubrofuente
		FROM		gf_detalle_comprobante_pptal dtcp
		LEFT JOIN	gf_rubro_fuente rbf 	ON 	rbf.id_unico 	= dtcp.rubrofuente
		LEFT JOIN	gf_rubro_pptal rub		ON 	rub.id_unico 	= rbf.rubro
		LEFT JOIN	gf_fuente fte			ON 	fte.id_unico 	= rbf.fuente
		WHERE		dtcp.comprobantepptal 	= 	$id";		
$resultRB = $mysqli->query($sqlRB);
# Impresión de valores obtenidos en la consulta
$pdf->SetFont('Arial','B',8);
#### CONSULTA DETALLES ####
 
while ( $rw = mysqli_fetch_row($resultRB)) {
    
    $consN="SELECT *
FROM
  gf_comprobante_pptal comp
LEFT JOIN
  gf_detalle_comprobante_pptal dtp ON comp.id_unico = dtp.comprobantepptal
LEFT JOIN
  gf_tipo_comprobante_pptal tpc ON tpc.id_unico = comp.tipocomprobante
LEFT JOIN
  gf_tercero ter ON ter.id_unico = comp.tercero
WHERE
  dtp.comprobanteafectado = $rw[0] AND dtp.rubrofuente = $rw[6]";
$consN=$mysqli->query($consN);
        
if(mysqli_num_rows($consN)>0) {
	$a = $pdf->GetY();
	$dif = $pdf->GetPageHeight() - $a;
	if($dif < 25){
		$pdf->AddPage();
	}
	# Impresión de código y nombre de rubro, fuente, valor
	$pdf->Cell(13,5,'Rubro :',0,0,'R');
	$pdf->Cell(45,5,$rw[1],0,0,'L');
	$pdf->Multicell(142,5,utf8_decode(ucwords(mb_strtolower($rw[2].' / '.$rw[3]))),0,'L');	
	$pdf->Cell(13,5,'Valor :',0,0,'R');
	$pdf->Cell(45,5,number_format($rw[4],2,',','.'),0,0,'L');
	$pdf->Ln(5);
	# Cabeza de tabla	
	$pdf->Cell(20,9,"",1,0,'C');
	$pdf->Cell(20,9,utf8_decode("Número"),1,0,'L');
	$pdf->Cell(20,9,"Fecha",1,0,'L');
	$pdf->Cell(60,9,utf8_decode("Descripción"),1,0,'L');
	$pdf->Cell(60,9,"Tercero",1,0,'L');
	$pdf->Cell(20,9,"",1,0,'C');
	$pdf->SetX(10);
	$pdf->Cell(20,4,"Tipo",0,0,'C');
	$pdf->Cell(20,4,utf8_decode(""),0,0,'L');
	$pdf->Cell(20,4,"",0,0,'L');
	$pdf->Cell(60,4,utf8_decode(""),0,0,'L');
	$pdf->Cell(60,4,"",0,0,'L');
	$pdf->Cell(20,4,"Valor",0,0,'C');	
	$pdf->Ln(4);
	$pdf->Cell(20,4,"documento",0,0,'C');
	$pdf->Cell(20,4,"",0,0,'L');
	$pdf->Cell(20,4,"",0,0,'L');
	$pdf->Cell(60,4,"",0,0,'L');
	$pdf->Cell(60,4,"",0,0,'L');
	$pdf->Cell(20,4,"documento",0,0,'C');
	$pdf->Ln(5);
	documento($rw[0],$rw[6],$pdf,$rw[4]);
	
	##########################################################################################################################################################
	# Salto final del ciclo	
	
    }
}
    if($numDis>0){
    $pdf->AddPage();
    
    }

}

while (ob_get_length()) {
  ob_end_clean();
}
# Final del documento			
	

$pdf->Output(0,'Informe Seguimiento Disponibilidad.pdf',0);

?>