<?php 
######################################################################################################
# ***************************************** Modificaciones ***************************************** #
######################################################################################################
#08/02/2017 | Erica G. | Cuentas y saldos Vigencias Anteriores
#16/11/2017 |Erica G. | ARCHIVO CREADO SOLO FOMVIDU
#######################################################################################################
ini_set('max_execution_time', 0);
session_start();
ob_start();
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
$con = new ConexionPDO();
/**
* Clase pdf con herencia a fpdf
*/
##########################################################################################################################################
# Captura de variables
##########################################################################################################################################
$mes = $_GET['mes'];
$cuenta = $_GET['cuenta'];
$annov  = $_SESSION['anno'];

$a = 0;
$cuentaA = 0;
while($a==0){
    $nannov = anno($annov);
    #Año Anterior
    $anno2 = $nannov-1;
    $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
    if(count($an2)>0){ 
        $annova = $an2[0][0];
        $ca = $con->Listar("SELECT id_unico,codi_cuenta, equivalente_va FROM gf_cuenta WHERE md5(id_unico) = '$cuenta'");
        $id_cuenta = $ca[0][0];
        $codCuenta = $ca[0][1];
        $equivalente =$ca[0][2];
        if(!empty($equivalente)){
            #echo '1'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
            $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova");
            if(count($ctaa)>0){
                if(!empty($ctaa[0][0])){
                    $cuentaA .= ','.$ctaa[0][0];
                }
            } else {
                #echo '2'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
                $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
                if(!empty($ctaa[0][0])){
                    $cuentaA .= ','.$ctaa[0][0];
                }
            }
        } else {
            #echo '3'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova";
            $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
            if(!empty($ctaa[0][0])){
                    $cuentaA .= ','.$ctaa[0][0];
                }
        }
        $annov = $annova;
    } else {
    	$ca = $con->Listar("SELECT id_unico,codi_cuenta, equivalente_va FROM gf_cuenta WHERE md5(id_unico) = '$cuenta'");
        $id_cuenta = $ca[0][0];
        $a += 1;
    }
}


$cuentas =($cuentaA.','.$id_cuenta);
##########################################################################################################################################
# Captura de variable compañia
$compania = $_SESSION['compania'];
##########################################################################################################################################
# Consulta de compañia
$sqlC = "SELECT 	ter.id_unico,
					ter.razonsocial,
					UPPER(ti.nombre),
					ter.numeroidentificacion,
					dir.direccion,
					tel.valor,
					ter.ruta_logo
		FROM 		gf_tercero ter
		LEFT JOIN 	gf_tipo_identificacion ti 	ON 	ter.tipoidentificacion = ti.id_unico
		LEFT JOIN   gf_direccion dir 			ON	dir.tercero = ter.id_unico
		LEFT JOIN 	gf_telefono  tel 			ON 	tel.tercero = ter.id_unico
		WHERE 		ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
##########################################################################################################################################
# Asignación de valores consultados
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3]; 
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];
$ruta_logo 	 = $rowC[6];

##########################################################################################################################################
# Consulta de Banco
##########################################################################################################################################
$sqlBanco = "SELECT 	cta.id_unico,
						CONCAT(cta.codi_cuenta,' ',UPPER(cta.nombre))
			FROM		gf_cuenta cta
			WHERE 		md5(cta.id_unico) 	= '$cuenta'";
$resultBanco = $mysqli->query($sqlBanco);
$banco = mysqli_fetch_row($resultBanco);
$banco1 = $banco[1];
##########################################################################################################################################
# Consulta de periodo,mes
#########################################################################################################################################
 $sqlMes = "SELECT 		mes.id_unico,
						mes.mes,
						param.anno, mes.numero 
		  FROM 			gf_mes mes 
		  LEFT JOIN 	gf_parametrizacion_anno param ON mes.parametrizacionanno = param.id_unico
		  WHERE 		md5(mes.id_unico) = '$mes'";
$resultMes = $mysqli->query($sqlMes);
$rowMes = mysqli_fetch_row($resultMes);
##########################################################################################################################################
# Asignación de valores de consulta
#########################################################################################################################################
$idMes = $rowMes[0];
$nomMes = $rowMes[1];
$annoMes = $rowMes[2];
$numMes = $rowMes[3];
##########################################################################################################################################
# Consulta de nombre de mes
##########################################################################################################################################
$sqlM = "SELECT id_unico FROM gf_mes WHERE id_unico = $idMes";
$resultM = $mysqli->query($sqlM);
$noM = mysqli_fetch_row($resultM);
##########################################################################################################################################
# Array con los numeros de los meses
##########################################################################################################################################
$meses = array( "Enero" => '01', "Febrero" => '02', "Marzo" => '03',"Abril" => '04', "Mayo" => '05', "Junio" => '06', 
                "Julio" => '07', "Agosto" => '08', "Septiembre" => '09', "Octubre" => '10', "Noviembre" => '11', "Diciembre" => '12'); 
$mess = $noM[0];
$calendario = CAL_GREGORIAN;
$diaF = cal_days_in_month($calendario, $numMes, $annoMes); 
$fechaF= $annoMes.'/'.$numMes.'/'.$diaF;
$fechaI =$annoMes.'/'.$numMes.'/'.'01';
$fechaComprobante = $annoMes.'-'.$numMes.'-'.$diaF;
##fecha formato 

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
		$wmax=($w-3*$this->cMargin)*1000/$this->FontSize;
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
            global $ruta_logo;
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $direccinTer;
            global $telefonoTer;
            global $banco1;
            ##########################################################################################################################################
            # Impresión de logo
            ##########################################################################################################################################
            if($ruta_logo !== ''){
                    $this->Image('../'.$ruta_logo,10,8,15);
            }
            ##########################################################################################################################################
            # Impresión de datos tercero
            ##########################################################################################################################################
            $this->SetX(35);
            $this->SetFont('Arial','B',12);
            $this->Multicell(155,5, utf8_decode($razonsocial),0,'C');
            $this->SetX(35);
            $this->SetFont('Arial','',9);
            $this->Cell(155,5,$nombreIdent.PHP_EOL.':'.PHP_EOL.$numeroIdent,0,0,'C');
            $this->Ln(5);
            $this->SetX(35);
            $this->Cell(155,5,$direccinTer.PHP_EOL.'Tel:'.PHP_EOL.$telefonoTer,0,0,'C');
            $this->Ln(5);
            $this->SetFont('Arial','B',10);
            $this->Cell(65,5,utf8_decode('CONCILACIÓN BANCARIA CUENTA '),0,0,'C');
            $this->MultiCell(125,5,utf8_decode($banco1),0,'L');

            
	}	
}
##########################################################################################################################################
# Declaración del objeto pdf con la clase mc_table
#########################################################################################################################################
$pdf = new PDF_MC_Table('P','mm','Letter');		#Creación del objeto pdf
$nb  = $pdf->AliasNbPages();					#Objeto de número de pagina
$pdf->AddPage();								#Agregar página
$pdf->SetFont('Arial','B',10);
##ULTIMO DIA DEL MES
$calendario = CAL_GREGORIAN;
$diaF = cal_days_in_month($calendario, $numMes, $annoMes); 
$fechaC = $diaF.'/'.$numMes.'/'.$annoMes;
##########################################################################################################################################

$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,5,utf8_decode('Mes  '),0,0,'L');
$pdf->Cell(170,5,$nomMes.PHP_EOL.'de'.PHP_EOL.$annoMes,0,0,'L');
$pdf->Ln(7);
##########################################################################################################################################
# Saldo en libros
##########################################################################################################################################

##########################################################################################################################################
# Consulta saldo en libros
##########################################################################################################################################
$sqlS = "SELECT 	SUM(dtc.valor)
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico		
		WHERE 		cnt.fecha <= '$fechaF' 
		AND 	    dtc.cuenta = $banco[0]";
$resultS = $mysqli->query($sqlS);
$saldo = mysqli_fetch_row($resultS);
##########################################################################################################################################
# Captura de valor de saldo en libros
##########################################################################################################################################
$saldoLibro = $saldo[0];

##########################################################################################################################################
# Consulta saldo extracto
##########################################################################################################################################
$sqlSE = "SELECT saldo_extracto FROM gf_partida_conciliatoria WHERE md5(id_unico) = '".$_GET['partida']."'";
$resultE = $mysqli->query($sqlSE);
$saldoE = mysqli_fetch_row($resultE);
##########################################################################################################################################
# Captura de valor de saldo extracto
##########################################################################################################################################
$saldoExtracto = $saldoE[0];

# Titulo de cabeza
##########################################################################################################################################
$pdf->Ln(5);
$pdf->SetFont('Arial','',10);
$pdf->Cell(70,5,utf8_decode('SALDO SEGÚN EXTRACTO'),0,0,'L');
$pdf->Cell(100,5,number_format($saldoExtracto,2,',','.'),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(70,5,utf8_decode('SALDO SEGÚN LIBRO'),0,0,'L');
$pdf->Cell(100,5,number_format($saldo[0],2,',','.'),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(120,0.1,'',0,0,'L');
$pdf->Cell(50,0.1,'',1,0,'R');
$dif = $saldoExtracto -$saldo[0];
$pdf->Ln(2);
$pdf->Cell(120,5,'DIFERENCIA',0,0,'L');
$pdf->Cell(50,5,number_format($dif,2,',','.'),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(120,5,'',0,0,'L');
$pdf->Cell(50,0.5,'',1,0,'R');
$pdf->Ln(10);
##########################################################################################################################################
# Variable de suma
##########################################################################################################################################
$sumaGiros = "";
$sumaG = "";
##########################################################################################################################################
# Consulta de Giros sin cobrar
##########################################################################################################################################
$sqlG = "SELECT DISTINCT	dtc.id_unico,
                date_format(cnt.fecha,'%d/%m/%Y'),
                CONCAT(tpc.sigla,' - ',cnt.numero),
                mov.numero,
                dtc.descripcion,
                IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
					IF(dtc.valor>0,dtc.valor*-1,dtc.valor)
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico
		LEFT JOIN 	gf_tipo_comprobante tpc 		ON cnt.tipocomprobante	= tpc.id_unico
		LEFT JOIN 	gf_tercero tr 					ON cnt.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor<0
		AND 		dtc.conciliado IS NULL
		AND 		cnt.fecha <= '$fechaF' 
		AND 		tpc.clasecontable != 5 
		AND 	    dtc.cuenta IN ($cuentas)
		";
$resultG = $mysqli->query($sqlG);



##########################################################################################################################################
# Consulta de Giros sin cobrar conciliados en otros periodos
##########################################################################################################################################
 $sqlG2 = "SELECT DISTINCT dtc.id_unico,
                date_format(cnt.fecha,'%d/%m/%Y'),
                CONCAT(tpc.sigla,' - ',cnt.numero),
                mov.numero,
                dtc.descripcion,
                IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
					IF(dtc.valor>0,dtc.valor*-1,dtc.valor)
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico
		LEFT JOIN 	gf_tipo_comprobante tpc 		ON cnt.tipocomprobante	= tpc.id_unico
		LEFT JOIN 	gf_tercero tr 					ON cnt.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor<0
		AND 		dtc.conciliado IS NOT NULL
		AND 		cnt.fecha <= '$fechaF' 
                AND             dtc.periodo_conciliado > $mess 
		AND 		tpc.clasecontable != 5 
		AND 	    dtc.cuenta IN ($cuentas)
		";
$resultG2 = $mysqli->query($sqlG2);
##########################################################################################################################################
# Impresión de valores
##########################################################################################################################################
if(mysqli_num_rows($resultG)>0 || mysqli_num_rows($resultG2)>0){
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,'GIROS SIN COBRAR',0,0,'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(18,5,'FECHA',1,0,'L');
    $pdf->Cell(30,5,'COMPROBANTE',1,0,'L');
    $pdf->Cell(25,5,'NO DOC',1,0,'L');
    $pdf->Cell(40,5,'DETALLE',1,0,'L');
    $pdf->Cell(40,5,'TERCERO',1,0,'L');
    $pdf->Cell(37,5,'VALOR',1,0,'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',9);
    while ($rowG = mysqli_fetch_row($resultG)) {
            $pdf->SetWidths(array(18,30,25,40,40,37));
            $pdf->SetAligns(array('L','L','L','L','L','R'));
            $pdf->Row(array($rowG[1], $rowG[2], $rowG[3], utf8_decode(ucwords(mb_strtolower($rowG[4]))), utf8_decode(ucwords(mb_strtolower($rowG[5]))),number_format($rowG[6]<0?$rowG[6]*-1:$rowG[6],2,'.',',')));
            $pdf->Ln(5);
            $sumaGiros += $rowG[6];
            $sumaG += $rowG[6];
    }
    while ($rowG2 = mysqli_fetch_row($resultG2)) {
	$pdf->SetWidths(array(18,30,25,40,40,37));
	$pdf->SetAligns(array('L','L','L','L','L','R'));
	$pdf->Row(array($rowG2[1], $rowG2[2], $rowG2[3], utf8_decode(ucwords(mb_strtolower($rowG2[4]))), utf8_decode(ucwords(mb_strtolower($rowG2[5]))),number_format($rowG2[6]<0?$rowG2[6]*-1:$rowG2[6],2,'.',',')));
	$pdf->Ln(5);
	$sumaGiros += $rowG2[6];
	$sumaG += $rowG2[6];
    }
    $pdf->Ln(5);
    ##########################################################################################################################################
    # Impresión de valor total
    ##########################################################################################################################################
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(153,5,'TOTAL GIROS SIN COBRAR',0,0,'R');
    $pdf->Cell(37,5,number_format($sumaG<0?$sumaG*-1:$sumaG,2,'.',','),0,0,'R');
     $pdf->Ln(5);
    ##########################################################################################################################################
} else {
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,'GIROS SIN COBRAR',0,0,'L');
    $pdf->Ln(5);
}



##########################################################################################################################################
# Variable de suma
#
##########################################################################################################################################
$sumaIngresos = "";
$sumaI = "";
##########################################################################################################################################
# Consulta de ingresos sin cobrar
##########################################################################################################################################
 $sqlI = "SELECT DISTINCT dtc.id_unico,
                date_format(cnt.fecha,'%d/%m/%Y'),
                CONCAT(tpc.sigla,' - ',cnt.numero),
                mov.numero,
                dtc.descripcion,
                IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
					dtc.valor
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico
		LEFT JOIN 	gf_tipo_comprobante tpc 		ON cnt.tipocomprobante	= tpc.id_unico
		LEFT JOIN 	gf_tercero tr 					ON cnt.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor>0
		AND 		dtc.conciliado IS NULL
		AND 		cnt.fecha <= '$fechaF' 
		AND 	    dtc.cuenta IN ($cuentas)
		AND 		tpc.clasecontable != 5 
		";
$resultI = $mysqli->query($sqlI);
##########################################################################################################################################
# Consulta de ingresos sin cobrar de que fueron conciliados en otros periodos
##########################################################################################################################################
 $sqlI2 = "SELECT DISTINCT dtc.id_unico,
                date_format(cnt.fecha,'%d/%m/%Y'),
                CONCAT(tpc.sigla,' - ',cnt.numero),
                mov.numero,
                dtc.descripcion,
                IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
					dtc.valor
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico
		LEFT JOIN 	gf_tipo_comprobante tpc 		ON cnt.tipocomprobante	= tpc.id_unico
		LEFT JOIN 	gf_tercero tr 					ON cnt.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor>0
		AND 		dtc.conciliado IS NOT NULL
		AND 		cnt.fecha <= '$fechaF' 
                AND             dtc.periodo_conciliado > $mess 
		AND 	        dtc.cuenta IN ($cuentas)
		AND 		tpc.clasecontable != 5 
		";
$resultI2 = $mysqli->query($sqlI2);
if(mysqli_num_rows($resultI2)>0 || mysqli_num_rows($resultI)>0){
    # Titulo de cabeza
##########################################################################################################################################
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,utf8_decode('CONSIGNACIONES EN TRÁNSITO'),0,0,'L');
$pdf->Ln(5);
##########################################################################################################################################
# Cabeza de tabla
##########################################################################################################################################

$pdf->SetFont('Arial','B',10);
$pdf->Cell(18,5,'FECHA',1,0,'L');
$pdf->Cell(30,5,'COMPROBANTE',1,0,'L');
$pdf->Cell(25,5,'NO DOC',1,0,'L');
$pdf->Cell(40,5,'DETALLE',1,0,'L');
$pdf->Cell(40,5,'TERCERO',1,0,'L');
$pdf->Cell(37,5,'VALOR',1,0,'L');
$pdf->Ln(5);
$pdf->SetFont('Arial','',9);
while ($rowI = mysqli_fetch_row($resultI)) {
	$pdf->SetWidths(array(18,30,25,40,40,37));
	$pdf->SetAligns(array('L','L','L','L','L','R'));
	$pdf->Row(array($rowI[1], $rowI[2], $rowI[3], utf8_decode(ucwords(mb_strtolower($rowI[4]))), utf8_decode(ucwords(mb_strtolower($rowI[5]))),number_format($rowI[6]<0?$rowI[6]*-1:$rowI[6],2,'.',',')));
	$pdf->Ln(5);
	$sumaIngresos += $rowI[6];
	$sumaI += $rowI[6];
}

##########################################################################################################################################
# Impresión de valores
##########################################################################################################################################
while ($rowI2 = mysqli_fetch_row($resultI2)) {
	$pdf->SetWidths(array(18,30,25,40,40,37));
	$pdf->SetAligns(array('L','L','L','L','L','R'));
	$pdf->Row(array($rowI2[1], $rowI2[2], $rowI2[3], utf8_decode(ucwords(mb_strtolower($rowI2[4]))), utf8_decode(ucwords(mb_strtolower($rowI2[5]))),number_format($rowI2[6]<0?$rowI2[6]*-1:$rowI2[6],2,'.',',')));
	$pdf->Ln(5);
	$sumaIngresos += $rowI2[6];
	$sumaI += $rowI2[6];
}
##########################################################################################################################################
# Impresión de valor total
##########################################################################################################################################
$pdf->SetFont('Arial','B',10);
$pdf->Cell(153,5,utf8_decode('TOTAL CONSIGNACIONES EN TRÁNSITO'),0,0,'R');
$pdf->Cell(37,5,number_format($sumaI<0?$sumaI*-1:$sumaI,2,'.',','),0,0,'R');
$pdf->Ln(5);
} else {
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,utf8_decode('CONSIGNACIONES EN TRÁNSITO'),0,0,'L');
    $pdf->Ln(5);
}




##########################################################################################################################################
# Cabeza de tabla
##########################################################################################################################################

##########################################################################################################################################
# Variable de movimientos
##########################################################################################################################################
$sumaMovimientos = "";
$sumaM = "";
##########################################################################################################################################
# Consulta de otros periodos
##########################################################################################################################################
$sqlM = "SELECT DISTINCT dtc.id_unico,
                    date_format(cnt.fecha,'%d/%m/%Y'),
                    CONCAT(tpc.sigla,' - ',cnt.numero),
                    mov.numero,
                    dtc.descripcion,
                    IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE, 
					dtc.valor
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico
		LEFT JOIN 	gf_tipo_comprobante tpc 		ON cnt.tipocomprobante	= tpc.id_unico
		LEFT JOIN 	gf_tercero tr 				ON cnt.tercero 		= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.conciliado IS NOT NULL
		AND 		cnt.fecha > '$fechaF' 
                AND             dtc.periodo_conciliado = $mess 
		AND             dtc.cuenta IN ($cuentas)
		AND 		tpc.clasecontable != 5 ";
$resultM = $mysqli->query($sqlM);
##########################################################################################################################################
# Impresión de valores
##########################################################################################################################################
if(mysqli_num_rows($resultM)>0){
    ##########################################################################################################################################
    # Titulo de cabeza
    ##########################################################################################################################################
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,'TRANSACCIONES CONCILIADAS Y REGISTRADAS CON FECHA POSTERIOR',0,0,'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(18,5,'FECHA',1,0,'L');
    $pdf->Cell(30,5,'COMPROBANTE',1,0,'L');
    $pdf->Cell(25,5,'NO DOC',1,0,'L');
    $pdf->Cell(40,5,'DETALLE',1,0,'L');
    $pdf->Cell(40,5,'TERCERO',1,0,'L');
    $pdf->Cell(37,5,'VALOR',1,0,'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',9);

while ($rowM = mysqli_fetch_row($resultM)) {
	$pdf->SetWidths(array(18,30,25,40,40,37));
	$pdf->SetAligns(array('L','L','L','L','L','R'));
	$pdf->Row(array($rowM[1], $rowM[2], $rowM[3], utf8_decode(ucwords(mb_strtolower($rowM[4]))), utf8_decode(ucwords(mb_strtolower($rowM[5]))),number_format($rowM[6]<0?$rowM[6]*-1:$rowM[6],2,'.',',')));
	$pdf->Ln(5);
	$sumaMovimientos += $rowM[6];
	$sumaM += $rowM[6];
}
##########################################################################################################################################
# Impresión de valor total
##########################################################################################################################################
$pdf->SetFont('Arial','B',10);
$pdf->Cell(153,5,'TOTAL TRANSACCIONES CONCILIADAS Y REGISTRADAS CON FECHA POSTERIOR',0,0,'R');
$pdf->Cell(37,5,number_format($sumaM,2,'.',','),0,0,'R');
$pdf->Ln(5);
}


##########################################################################################################################################
# Variable de movimientos
#
##########################################################################################################################################
$sumaPartidasSI = "";
$sumaSI = "";
##########################################################################################################################################
# Captura de id partida
#
##########################################################################################################################################
$partida = $_GET['partida'];
##########################################################################################################################################
# Consulta de partida conciliatoria id
#
##########################################################################################################################################
$sqlP = "SELECT		id_unico
		FROM 		gf_partida_conciliatoria 
		WHERE 		md5(id_unico) = '$partida'";
$resultP = $mysqli->query($sqlP);
$rowPP = mysqli_fetch_row($resultP);
##########################################################################################################################################
# Asignación de variables
#
##########################################################################################################################################
$idPartida = $rowPP[0];
##########################################################################################################################################
# CONSULTA PARTIDAS CONCILIATORIAS SALDOS INICIALES
#
##########################################################################################################################################
#FECHA <01/01
$fechaMenor =$annoMes.'-01-01';

$part = $_GET['partida'];
 $sqlPCA = "SELECT DISTINCT
    DATE_FORMAT(dtp.fecha_partida, '%d/%m/%Y'),
    tpp.nombre,
    tpd.nombre,
    dtp.numero_documento,
    dtp.descripcion_detalle_partida,
    dtp.valor,
    dtp.tipo_partida,
    dtp.fecha_conciliacion,
    dtp.conciliado
FROM
    gf_partida_conciliatoria pc
INNER JOIN
    gf_detalle_partida dtp
ON
    dtp.id_partida = pc.id_unico
LEFT JOIN
    gf_tipo_partida tpp
ON
    dtp.tipo_partida = tpp.id_unico
LEFT JOIN
    gf_tipo_documento tpd
ON
    dtp.tipo_documento = tpd.id_unico
WHERE
    (
        dtp.conciliado IS NULL OR dtp.conciliado IN(0, 2) 
        AND dtp.fecha_partida < '$fechaI' 
        AND pc.id_cuenta IN ($cuentas) 
        AND dtp.fecha_partida < '$fechaMenor'
        
    ) OR(
        dtp.conciliado = 1 
        AND dtp.fecha_conciliacion > '$fechaF' 
        AND pc.id_cuenta IN ($cuentas)  
        AND dtp.fecha_partida < '$fechaMenor'  
    ) AND pc.id_cuenta IN ($cuentas)  AND dtp.valor IS NOT NULL";


$resultPCA = $mysqli->query($sqlPCA);
##########################################################################################################################################
# Impresión de valores
#
##########################################################################################################################################
IF(mysqli_num_rows($resultPCA)>0){
    ##########################################################################################################################################
    # Titulo de cabeza
    ##########################################################################################################################################
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,'PARTIDAS CONCILIATORIAS SALDOS INICIALES',0,0,'L');
    $pdf->Ln(5);

    ##########################################################################################################################################
    # Cabeza de tabla
    ##########################################################################################################################################
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(18,5,'FECHA',1,0,'L');
    $pdf->Cell(30,5,'TIPO PARTIDA',1,0,'L');
    $pdf->Cell(40,5,'TIPO DOC',1,0,'L');
    $pdf->Cell(25,5,'NO DOC',1,0,'L');
    $pdf->Cell(40,5,'DETALLE',1,0,'L');
    $pdf->Cell(37,5,'VALOR',1,0,'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',9);

while ($rowPCA = mysqli_fetch_row($resultPCA)) {
	$pdf->SetWidths(array(18,30,40,25,40,37));
	$pdf->SetAligns(array('L','L','L','L','L','R'));
	$pdf->Row(array(utf8_decode($rowPCA[0]), utf8_decode($rowPCA[1]), utf8_decode($rowPCA[2]), utf8_decode(ucwords(mb_strtolower($rowPCA[3]))), utf8_decode(ucwords(mb_strtolower($rowPCA[4]))),number_format($rowPCA[5],2,'.',',')));
	
        if($rowPCA[6]==1){
		$sumaPartidasSI += $rowPCA[5];
		$sumaSI += $rowPCA[5];
	}else{
		$sumaPartidasSI += $rowPCA[5] *-1;
		$sumaSI += $rowPCA[5] *-1;
	}	
	
	$pdf->Ln(5);
}
##########################################################################################################################################
# Impresión de valor total
#
##########################################################################################################################################
$pdf->SetFont('Arial','B',10);
$pdf->Cell(153,5,'TOTAL PARTIDAS CONCILIATORIAS SALDOS INICIALES',0,0,'R');
$pdf->Cell(37,5,number_format($sumaSI,2,'.',','),0,0,'R');
$pdf->Ln(5);
##########################################################################################################################################
}

##########################################################################################################################################
# Variable de movimientos
#
##########################################################################################################################################
$sumaPartidasA = "";
$sumaPA = "";
##########################################################################################################################################

$part = $_GET['partida'];
 $sqlPCA = "SELECT DISTINCT
    DATE_FORMAT(dtp.fecha_partida, '%d/%m/%Y'),
    tpp.nombre,
    tpd.nombre,
    dtp.numero_documento,
    dtp.descripcion_detalle_partida,
    dtp.valor,
    dtp.tipo_partida,
    dtp.fecha_conciliacion,
    dtp.conciliado
FROM
    gf_partida_conciliatoria pc
INNER JOIN
    gf_detalle_partida dtp
ON
    dtp.id_partida = pc.id_unico
LEFT JOIN
    gf_tipo_partida tpp
ON
    dtp.tipo_partida = tpp.id_unico
LEFT JOIN
    gf_tipo_documento tpd
ON
    dtp.tipo_documento = tpd.id_unico
WHERE
    (
        dtp.conciliado IS NULL OR dtp.conciliado IN(0, 2) 
        AND pc.id_cuenta IN ($cuentas) 
        AND (dtp.fecha_partida < '$fechaI' and dtp.fecha_partida > '$fechaMenor') 
        
    ) OR(
        dtp.conciliado = 1 
        AND dtp.fecha_conciliacion > '$fechaF' 
        AND pc.id_cuenta IN ($cuentas)  
        AND( dtp.fecha_partida > '$fechaMenor' and dtp.fecha_partida < '$fechaI' )
    ) AND pc.id_cuenta IN ($cuentas)  AND dtp.valor IS NOT NULL";


$resultPCA = $mysqli->query($sqlPCA);
##########################################################################################################################################
# Impresión de valores
#
##########################################################################################################################################
IF(mysqli_num_rows($resultPCA)>0){
    ##########################################################################################################################################
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,'PARTIDAS CONCILIATORIAS PERIODOS ANTERIORES',0,0,'L');
    $pdf->Ln(5);
while ($rowPCA = mysqli_fetch_row($resultPCA)) {
	$pdf->SetWidths(array(18,30,40,25,40,37));
	$pdf->SetAligns(array('L','L','L','L','L','R'));
	$pdf->Row(array(utf8_decode($rowPCA[0]), utf8_decode($rowPCA[1]), utf8_decode($rowPCA[2]), utf8_decode(ucwords(mb_strtolower($rowPCA[3]))), utf8_decode(ucwords(mb_strtolower($rowPCA[4]))),number_format($rowPCA[5],2,'.',',')));
	if($rowPCA[6]==1){
		$sumaPartidasA += $rowPCA[5];
		$sumaPA += $rowPCA[5];
	}else{
		$sumaPartidasA += $rowPCA[5] *-1;
		$sumaPA += $rowPCA[5] *-1;
	}
       
	
	$pdf->Ln(5);
}


##########################################################################################################################################
# Impresión de valor total
#
##########################################################################################################################################
$pdf->SetFont('Arial','B',10);
$pdf->Cell(153,5,'TOTAL PARTIDAS CONCILIATORIAS PERIODOS ANTERIORES',0,0,'R');
$pdf->Cell(37,5,number_format($sumaPA,2,'.',','),0,0,'R');
$pdf->Ln(5);
}
#########################################################################################################################################
# CONSULTA PARTIDAS CONCILIATORIAS DEL PERIODO
##########################################################################################################################################
$sumaPartidas=0;
$sqlPC = "SELECT	date_format(dtp.fecha_partida,'%d/%m/%Y'),
					tpp.nombre,
					tpd.nombre,
					dtp.numero_documento,
					dtp.descripcion_detalle_partida,
					dtp.valor,
					dtp.tipo_partida
		FROM 		gf_partida_conciliatoria pc
		LEFT JOIN 	gf_detalle_partida dtp 			ON dtp.id_partida 		= pc.id_unico
		LEFT JOIN 	gf_tipo_partida tpp 			ON dtp.tipo_partida 	= tpp.id_unico
		LEFT JOIN 	gf_tipo_documento tpd 			ON dtp.tipo_documento 	= tpd.id_unico
		WHERE 		dtp.fecha_partida BETWEEN '$fechaI' and '$fechaF' 
		AND 		pc.id_cuenta 	IN ($cuentas)
		AND 		dtp.conciliado 	!= 1";
$resultPC = $mysqli->query($sqlPC);


$sqlPC2 = "SELECT	date_format(dtp.fecha_partida,'%d/%m/%Y'),
					tpp.nombre,
					tpd.nombre,
					dtp.numero_documento,
					dtp.descripcion_detalle_partida,
					dtp.valor,
					dtp.tipo_partida
		FROM 		gf_partida_conciliatoria pc
		LEFT JOIN 	gf_detalle_partida dtp 			ON dtp.id_partida 		= pc.id_unico
		LEFT JOIN 	gf_tipo_partida tpp 			ON dtp.tipo_partida 	= tpp.id_unico
		LEFT JOIN 	gf_tipo_documento tpd 			ON dtp.tipo_documento 	= tpd.id_unico
		WHERE 		dtp.fecha_partida BETWEEN '$fechaI' and '$fechaF' 
		AND 		pc.id_cuenta 	IN ($cuentas)
		AND 		dtp.conciliado 	= 1 AND dtp.fecha_conciliacion BETWEEN '$fechaI' and '$fechaF' ";
$resultPC2 = $mysqli->query($sqlPC2);
##########################################################################################################################################
# Impresión de valores
#
##########################################################################################################################################
if(mysqli_num_rows($resultPC)>0 || mysqli_num_rows($resultPC2)>0){
    ##########################################################################################################################################
    # Titulo de cabeza
    #
    ##########################################################################################################################################
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,'PARTIDAS CONCILIATORIAS DEL PERIODO',0,0,'L');
    $pdf->Ln(5);
    ##########################################################################################################################################
# Cabeza de tabla
#
##########################################################################################################################################

$pdf->SetFont('Arial','B',10);
$pdf->Cell(18,5,'FECHA',1,0,'L');
$pdf->Cell(30,5,'TIPO PARTIDA',1,0,'L');
$pdf->Cell(40,5,'TIPO DOC',1,0,'L');
$pdf->Cell(25,5,'NO DOC',1,0,'L');
$pdf->Cell(40,5,'DETALLE',1,0,'L');
$pdf->Cell(37,5,'VALOR',1,0,'L');
$pdf->Ln(5);
$pdf->SetFont('Arial','',9);


##########################################################################################################################################
# Impresión de valores
#
##########################################################################################################################################
while ($rowPC2 = mysqli_fetch_row($resultPC2)) {
	$pdf->SetWidths(array(18,30,40,25,40,37));
	$pdf->SetAligns(array('L','L','L','L','L','R'));
	$pdf->Row(array(utf8_decode($rowPC2[0]), utf8_decode($rowPC2[1]), utf8_decode($rowPC2[2]), utf8_decode(ucwords(mb_strtolower($rowPC2[3]))), utf8_decode(ucwords(mb_strtolower($rowPC2[4]))),number_format($rowPC2[5]<0?$rowPC2[5]*-1:$rowPC2[5],2,'.',',')));
	if($rowPC2[6]==1){
		$sumaPartidas += $rowPC2[5];
		$sumaP += $rowPC2[5];
	}else{
		$sumaPartidas += $rowPC2[5] *-1;
		$sumaP += $rowPC2[5] *-1;
	}	
	$pdf->Ln(5);
}
while ($rowPC = mysqli_fetch_row($resultPC)) {
	$pdf->SetWidths(array(18,30,40,25,40,37));
	$pdf->SetAligns(array('L','L','L','L','L','R'));
	$pdf->Row(array(utf8_decode($rowPC[0]), utf8_decode($rowPC[1]), utf8_decode($rowPC[2]), utf8_decode(ucwords(mb_strtolower($rowPC[3]))), utf8_decode(ucwords(mb_strtolower($rowPC[4]))),number_format($rowPC[5]<0?$rowPC[5]*-1:$rowPC[5],2,'.',',')));
	if($rowPC[6]==1){
		$sumaPartidas += $rowPC[5];
		$sumaP += $rowPC[5];
	}else{
		$sumaPartidas += $rowPC[5] *-1;
		$sumaP += $rowPC[5] *-1;
	}	
	$pdf->Ln(5);
}

##########################################################################################################################################
# Impresión de valor total
#
##########################################################################################################################################
$pdf->SetFont('Arial','B',10);
$pdf->Cell(153,5,'TOTAL PARTIDAS CONCILIATORIAS DEL PERIODO',0,0,'R');
$pdf->Cell(37,5,number_format($sumaP,2,'.',','),0,0,'R');
}

$pdf->Ln(10);

##########################################################################################################################################
# Saldo conciliado
#
##########################################################################################################################################
$saldoConciliado = ($saldoLibro + $sumaMovimientos + $sumaPartidas + $sumaPartidasA + $sumaPartidasSI) - $saldoExtracto - ($sumaIngresos + $sumaGiros);

##########################################################################################################################################
# Saldo extracto
##########################################################################################################################################
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode('SALDO SEGÚN EXTRACTO'),0,0,'L');
$pdf->Cell(50,5,'',0,0,'R');
$pdf->Cell(50,5,number_format($saldoE[0],2,',','.'),0,0,'R');
$pdf->Ln(5);
if($sumaGiros!=0){
$pdf->Cell(50,5,utf8_decode('GIROS SIN COBRAR'),0,0,'L');
if($sumaGiros<0){$sumaGiros=$sumaGiros*-1;}else{$sumaGiros=$sumaGiros;}
$pdf->Cell(50,5,number_format($sumaGiros,2,',','.'),0,0,'R');
$pdf->Cell(50,5,'',0,0,'R');
$pdf->Ln(5);
}
//if($sumaIngresos!=0){
//$pdf->Cell(50,5,utf8_decode('INGRESOS SIN COBRAR'),0,0,'L');
//$pdf->Cell(50,5,'',0,0,'R');
//$pdf->Cell(50,5,number_format($sumaIngresos,2,',','.'),0,0,'R');
//$pdf->Ln(5);
//}
//if($sumaMovimientos!=0){
//$pdf->Cell(50,5,utf8_decode('MOVIMIENTOS OTROS PERIODOS'),0,0,'L');
//$pdf->Cell(50,5,'',0,0,'R');
//$pdf->Cell(50,5,number_format($sumaMovimientos,2,',','.'),0,0,'R');
//$pdf->Ln(5);
//}
//if($sumaPartidasSI!=0){
//$pdf->Cell(50,5,utf8_decode('PARTIDAS SALDOS INICIALES'),0,0,'L');
//$pdf->Cell(50,5,'',0,0,'R');
//$pdf->Cell(50,5,number_format($sumaPartidasSI,2,',','.'),0,0,'R');
//$pdf->Ln(5);
//}
//if($sumaPartidasA!=0){
//$pdf->Cell(50,5,utf8_decode('PARTIDAS PERIODOS ANTERIORES'),0,0,'L');
//$pdf->Cell(50,5,'',0,0,'R');
//$pdf->Cell(50,5,number_format($sumaPartidasA,2,',','.'),0,0,'R');
//$pdf->Ln(5);
//}
//if($sumaPartidas!=0){
//$pdf->Cell(50,5,utf8_decode('PARTIDAS PERIODOO'),0,0,'L');
//$pdf->Cell(50,5,'',0,0,'R');
//$pdf->Cell(50,5,number_format($sumaPartidas,2,',','.'),0,0,'R');
//$pdf->Ln(5);
//}
$pdf->Cell(50,5,utf8_decode('SALDO SEGÚN LIBRO'),0,0,'L');
$pdf->Cell(50,5,number_format($saldoLibro,2,',','.'),0,0,'R');
$pdf->Cell(50,5,'',0,0,'R');
$pdf->Ln(5);
$pdf->Cell(50,5,'',0,0,'L');
$pdf->Cell(100,0.1,'',1,0,'R');
$pdf->Ln(5);
$uno =$sumaGiros+$saldoLibro;
$dos =$saldoExtracto+$sumaPartidasSI;
$pdf->Cell(50,5,'SUMAS IGUALES',0,0,'L');
$pdf->Cell(50,5,number_format($uno,2,',','.'),0,0,'R');
$pdf->Cell(50,5,number_format($dos,2,',','.'),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(50,5,'',0,0,'L');
$pdf->Cell(100,0.5,'',1,0,'R');
$pdf->Ln(5);
##########################################################################################################################################
$compania = $_SESSION['compania'];
#Consulta pra obtener generar los datos de firma
  $sqlTipoComp = "SELECT IF(CONCAT_WS(' ',
    t.nombreuno,
    t.nombredos,
    t.apellidouno,
    t.apellidodos) 
    IS NULL OR CONCAT_WS(' ',
    t.nombreuno,
    t.nombredos,
    t.apellidouno,
    t.apellidodos) = '',
    UPPER(t.razonsocial),
    CONCAT_WS(' ',
    UPPER(t.nombreuno),
    UPPER(t.nombredos),
    UPPER(t.apellidouno),
    UPPER(t.apellidodos))) AS NOMBRE, ti.nombre, t.numeroidentificacion, UPPER(car.nombre) , 
    rd.fecha_inicio, rd.fecha_fin , t.tarjeta_profesional 
 FROM  gf_tipo_documento td 
 LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento 
 LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico
 LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
 LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
 LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
 LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
 WHERE td.nombre = 'Conciliacion' AND t.id_unico IS NOT NULL 
 AND td.compania = $compania 
 AND if(rd.fecha_inicio IS NULL, rd.fecha_inicio IS NULL, rd.fecha_inicio <= '$fechaComprobante') 
 AND if(rd.fecha_fin IS NULL,rd.fecha_fin IS NULL, rd.fecha_fin >= '$fechaComprobante')";
//$fechaComp
$tipComp = $mysqli->query($sqlTipoComp);
$resultF1 = $mysqli->query($sqlTipoComp);
$pdf->Ln(25);
$yy1 = $pdf->GetY();
$xx1 = $pdf->GetX();
$i   = 1;
while ($firma = mysqli_fetch_row($tipComp)) {

    $pdf->SetXY($xx1,$yy1);
    $pdf->Cell(80, 0, '', 'B');
    $pdf->Ln(3);
    $pdf->SetX($xx1);
    $pdf->MultiCell(80, 3, utf8_decode($firma[0]),  0, 'L');
    $pdf->Ln(1);
    $pdf->SetX($xx1);
    $pdf->Cell(80, 2, utf8_decode($firma[3]), 0, 0, 'L');
    if($i==2){
        $pdf->Ln(20);
        $i = 1;
        $yy1 =  $pdf->GetY();
        $xx1 = $pdf->GetX();
    } else {
        $i++;
        $xx1 = $xx1 + 90;
    }
}
 

while (ob_get_length()) {
  ob_end_clean();
}
##########################################################################################################################################
# Salida del documento
#
##########################################################################################################################################
$pdf->Output(0,'inf_conciliaciones_periodo.pdf',0);
 ?>