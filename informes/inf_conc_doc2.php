<?php 
######################################################################################################
# ***************************************** Modificaciones ***************************************** #
######################################################################################################
#08/02/2017 | Erica G. | Cuentas y saldos Vigencias Anteriores
#05/10/2017 |Erica G. | ARCHIVO CREADO
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
##########################################################################################################################################
# Captura de variable compañia
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
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
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo    = $rowC[0][6];
##########################################################################################################################################

/**
* Clase pdf con herencia a fpdf
*/
class PDF extends FPDF
{
    function Header(){ 
        global $razonsocial;
        global $nombreIdent;
        global $numeroIdent;
        global $direccinTer;
        global $telefonoTer;
        global $ruta_logo;
        global $numpaginas;
        $numpaginas=$numpaginas+1;

        $this->SetFont('Arial','B',10);

        if($ruta_logo != '')
        {
          $this->Image('../'.$ruta_logo,10,5,28);
        }
        $this->SetFont('Arial','B',10);	
        $this->MultiCell(195,5,utf8_decode($razonsocial),0,'C');		
        $this->SetX(10);
        $this->Ln(1);
        $this->Cell(195,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
        $this->ln(5);
        $this->SetX(10);
        $this->Cell(195,5,utf8_decode('Dirección: '.$direccinTer),0,0,'C');
        $this->ln(5);
        $this->SetX(10);
        $this->Cell(195,5,utf8_decode('Tel: '.$telefonoTer),0,0,'C');
        $this->ln(5);
    }  
}
$pdf = new PDF('P','mm','Letter');   
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
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

$cuentas =($cuentaA.','.$id_cuenta);								#Agregar página
$pdf->SetFont('Arial','B',10);

##########################################################################################################################################
# Consulta de Banco
##########################################################################################################################################
$sqlBanco = "SELECT 	cta.id_unico,
						CONCAT(cta.codi_cuenta,' ',UPPER(cta.nombre))
			FROM		gf_cuenta cta
			WHERE 		md5(cta.id_unico) 	= '$cuenta'";
$resultBanco = $mysqli->query($sqlBanco);
$banco = mysqli_fetch_row($resultBanco);

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
##ULTIMO DIA DEL MES
$calendario = CAL_GREGORIAN;
$diaF = cal_days_in_month($calendario, $numMes, $annoMes); 
$fechaC = $diaF.'/'.$numMes.'/'.$annoMes;
##########################################################################################################################################
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25,5,'Fecha',0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,$fechaC,0,0,'L');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('Conciliación del mes de '),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(70,5,$nomMes.PHP_EOL.'de'.PHP_EOL.$annoMes,0,0,'L');
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(65,5,utf8_decode('CONCILACIÓN BANCARIA CUENTA '),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(125,5,utf8_decode($banco[1]),0,'L');
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
# Impresión saldo en libros
##########################################################################################################################################

$pdf->SetFont('Arial','B',10);
$pdf->Cell(65,5,'Saldo En Contabilidad',0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(125,5,number_format($saldo[0],2,',','.'),0,0,'L');

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
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,'MAS(+) GIROS SIN COBRAR',0,0,'L');
$pdf->Ln(5);


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
    $pdf->Cell(18,5,'FECHA',1,0,'L');
    $pdf->Cell(30,5,'COMPROBANTE',1,0,'L');
    $pdf->Cell(25,5,'NO DOC',1,0,'L');
    $pdf->Cell(40,5,'DETALLE',1,0,'L');
    $pdf->Cell(40,5,'TERCERO',1,0,'L');
    $pdf->Cell(37,5,'VALOR',1,0,'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',9);
    
    while ($rowG = mysqli_fetch_row($resultG)) {
        $x  = $pdf->GetX();
        $y  = $pdf->GetY();
        $pdf->CellFitScale(18,5,$rowG[1],0,0,'L');
        $pdf->CellFitScale(30,5,$rowG[2],0,0,'L');
        $pdf->CellFitScale(25,5,$rowG[3],0,0,'L');
        $x1 = $pdf->GetX();
        $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowG[4]))),0,'L');
        $y1 = $pdf->GetY();
        $h1 = $y1-$y;
        $pdf->SetXY($x1+40, $y);
        $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowG[5]))),0,'L');
        $y2 = $pdf->GetY();
        $h2 = $y2-$y;
        $pdf->SetXY($x1+80, $y);
        $pdf->Cell(37,5,number_format($rowG[6]<0?$rowG[6]*-1:$rowG[6],2,'.',','),0,0,'R');
        $pdf->Ln(5);
        $max = max($h1, $h2);
        $pdf->SetXY($x, $y);
        $pdf->Cell(18,$max,'',1,0,'L');
        $pdf->Cell(30,$max,'',1,0,'L');
        $pdf->Cell(25,$max,'',1,0,'L');
        $pdf->Cell(40,$max,'',1,0,'L');
        $pdf->Cell(40,$max,'',1,0,'L');
        $pdf->Cell(37,$max,'',1,0,'L');
        $pdf->Ln($max);
        $sumaGiros += $rowG[6];
        $sumaG += $rowG[6];
    }
    while ($rowG2 = mysqli_fetch_row($resultG2)) {
        $x  = $pdf->GetX();
        $y  = $pdf->GetY();
        $pdf->CellFitScale(18,5,$rowG2[1],0,0,'L');
        $pdf->CellFitScale(30,5,$rowG2[2],0,0,'L');
        $pdf->CellFitScale(25,5,$rowG2[3],0,0,'L');
        $x1 = $pdf->GetX();
        $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowG2[4]))),0,'L');
        $y1 = $pdf->GetY();
        $h1 = $y1-$y;
        $pdf->SetXY($x1+40, $y);
        $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowG2[5]))),0,'L');
        $y2 = $pdf->GetY();
        $h2 = $y2-$y;
        $pdf->SetXY($x1+80, $y);
        $pdf->Cell(37,5,number_format($rowG2[6]<0?$rowG2[6]*-1:$rowG2[6],2,'.',','),0,0,'R');
        $pdf->Ln(5);
        $max = max($h1, $h2);
        $pdf->SetXY($x, $y);
        $pdf->Cell(18,$max,'',1,0,'L');
        $pdf->Cell(30,$max,'',1,0,'L');
        $pdf->Cell(25,$max,'',1,0,'L');
        $pdf->Cell(40,$max,'',1,0,'L');
        $pdf->Cell(40,$max,'',1,0,'L');
        $pdf->Cell(37,$max,'',1,0,'L');
        $pdf->Ln($max);
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
}


# Titulo de cabeza
##########################################################################################################################################
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,utf8_decode('MENOS(-) CONSIGNACIONES EN TRÁNSITO'),0,0,'L');
$pdf->Ln(5);
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
##########################################################################################################################################
# Cabeza de tabla
##########################################################################################################################################
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
while ($rowI = mysqli_fetch_row($resultI)) {
    $x  = $pdf->GetX();
    $y  = $pdf->GetY();
    $pdf->CellFitScale(18,5,$rowI[1],0,0,'L');
    $pdf->CellFitScale(30,5,$rowI[2],0,0,'L');
    $pdf->CellFitScale(25,5,$rowI[3],0,0,'L');
    $x1 = $pdf->GetX();
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowI[4]))),0,'L');
    $y1 = $pdf->GetY();
    $h1 = $y1-$y;
    $pdf->SetXY($x1+40, $y);
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowI[5]))),0,'L');
    $y2 = $pdf->GetY();
    $h2 = $y2-$y;
    $pdf->SetXY($x1+80, $y);
    $pdf->Cell(37,5,number_format($rowI[6]<0?$rowI[6]*-1:$rowI[6],2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $max = max($h1, $h2);
    $pdf->SetXY($x, $y);
    $pdf->Cell(18,$max,'',1,0,'L');
    $pdf->Cell(30,$max,'',1,0,'L');
    $pdf->Cell(25,$max,'',1,0,'L');
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(37,$max,'',1,0,'L');
    $pdf->Ln($max);
    
    $sumaIngresos += $rowI[6];
    $sumaI += $rowI[6];
}

##########################################################################################################################################
# Impresión de valores
##########################################################################################################################################
while ($rowI2 = mysqli_fetch_row($resultI2)) {
    $x  = $pdf->GetX();
    $y  = $pdf->GetY();
    $pdf->CellFitScale(18,5,$rowI2[1],0,0,'L');
    $pdf->CellFitScale(30,5,$rowI2[2],0,0,'L');
    $pdf->CellFitScale(25,5,$rowI2[3],0,0,'L');
    $x1 = $pdf->GetX();
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowI2[4]))),0,'L');
    $y1 = $pdf->GetY();
    $h1 = $y1-$y;
    $pdf->SetXY($x1+40, $y);
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowI2[5]))),0,'L');
    $y2 = $pdf->GetY();
    $h2 = $y2-$y;
    $pdf->SetXY($x1+80, $y);
    $pdf->Cell(37,5,number_format($rowI2[6]<0?$rowI2[6]*-1:$rowI2[6],2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $max = max($h1, $h2);
    $pdf->SetXY($x, $y);
    $pdf->Cell(18,$max,'',1,0,'L');
    $pdf->Cell(30,$max,'',1,0,'L');
    $pdf->Cell(25,$max,'',1,0,'L');
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(37,$max,'',1,0,'L');
    $pdf->Ln($max);
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
}



##########################################################################################################################################
# Titulo de cabeza
##########################################################################################################################################
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,'MAS(+) TRANSACCIONES CONCILIADAS Y REGISTRADAS CON FECHA POSTERIOR',0,0,'L');
$pdf->Ln(5);
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
    $x  = $pdf->GetX();
    $y  = $pdf->GetY();
    $pdf->CellFitScale(18,5,$rowM[1],0,0,'L');
    $pdf->CellFitScale(30,5,$rowM[2],0,0,'L');
    $pdf->CellFitScale(25,5,$rowM[3],0,0,'L');
    $x1 = $pdf->GetX();
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowM[4]))),0,'L');
    $y1 = $pdf->GetY();
    $h1 = $y1-$y;
    $pdf->SetXY($x1+40, $y);
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowM[5]))),0,'L');
    $y2 = $pdf->GetY();
    $h2 = $y2-$y;
    $pdf->SetXY($x1+80, $y);
    $pdf->Cell(37,5,number_format($rowM[6]<0?$rowM[6]*-1:$rowM[6],2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $max = max($h1, $h2);
    $pdf->SetXY($x, $y);
    $pdf->Cell(18,$max,'',1,0,'L');
    $pdf->Cell(30,$max,'',1,0,'L');
    $pdf->Cell(25,$max,'',1,0,'L');
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(37,$max,'',1,0,'L');
    $pdf->Ln($max);
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
# Titulo de cabeza
##########################################################################################################################################
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,'MAS O MENOS (+/- )PARTIDAS CONCILIATORIAS SALDOS INICIALES',0,0,'L');
$pdf->Ln(5);

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
    # Cabeza de tabla
    ##########################################################################################################################################
    $pdf->Ln(5);
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
    $x  = $pdf->GetX();
    $y  = $pdf->GetY();
    $pdf->Cell(18,5,$rowPCA[0],0,0,'L');
    $pdf->Cell(30,5,$rowPCA[1],0,0,'L');
    $x1 = $pdf->GetX();
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowPCA[3]))),0,'L');
    $y1 = $pdf->GetY();
    $h1 = $y1-$y;
    $pdf->SetXY($x1+40, $y);
    $pdf->Cell(25,5,$rowPCA[2],0,0,'L');
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowPCA[4]))),0,'L');
    $y2 = $pdf->GetY();
    $h2 = $y2-$y;
    $pdf->SetXY($x1+105, $y);
    $pdf->Cell(37,5,number_format($rowPCA[5],2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $max = max($h1, $h2);
    $pdf->SetXY($x, $y);
    $pdf->Cell(18,$max,'',1,0,'L');
    $pdf->Cell(30,$max,'',1,0,'L');
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(25,$max,'',1,0,'L');    
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(37,$max,'',1,0,'L');
    $pdf->Ln($max); 
   
    if($rowPCA[6]==1){
            $sumaPartidasSI += $rowPCA[5];
            $sumaSI += $rowPCA[5];
    }else{
            $sumaPartidasSI += $rowPCA[5] *-1;
            $sumaSI += $rowPCA[5] *-1;
    }	
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
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,'MAS O MENOS(+/-) PARTIDAS CONCILIATORIAS PERIODOS ANTERIORES',0,0,'L');
$pdf->Ln(5);
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
while ($rowPCA = mysqli_fetch_row($resultPCA)) {
    $x  = $pdf->GetX();
    $y  = $pdf->GetY();
    $pdf->Cell(18,5,$rowPCA[0],0,0,'L');
    $pdf->Cell(30,5,$rowPCA[1],0,0,'L');
    $x1 = $pdf->GetX();
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowPCA[3]))),0,'L');
    $y1 = $pdf->GetY();
    $h1 = $y1-$y;
    $pdf->SetXY($x1+40, $y);
    $pdf->Cell(25,5,$rowPCA[2],0,0,'L');
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowPCA[4]))),0,'L');
    $y2 = $pdf->GetY();
    $h2 = $y2-$y;
    $pdf->SetXY($x1+105, $y);
    $pdf->Cell(37,5,number_format($rowPCA[5],2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $max = max($h1, $h2);
    $pdf->SetXY($x, $y);
    $pdf->Cell(18,$max,'',1,0,'L');
    $pdf->Cell(30,$max,'',1,0,'L');
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(25,$max,'',1,0,'L');    
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(37,$max,'',1,0,'L');
    $pdf->Ln($max); 
    
    if($rowPCA[6]==1){
            $sumaPartidasA += $rowPCA[5];
            $sumaPA += $rowPCA[5];
    }else{
            $sumaPartidasA += $rowPCA[5] *-1;
            $sumaPA += $rowPCA[5] *-1;
    }
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
#
##########################################################################################################################################
# Titulo de cabeza
#
##########################################################################################################################################
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,'MAS O MENOS(+/-) PARTIDAS CONCILIATORIAS DEL PERIODO',0,0,'L');
$pdf->Ln(5);

##########################################################################################################################################
# Consulta de partidas conciliatorias
#
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
# Cabeza de tabla
#
##########################################################################################################################################
$pdf->Ln(5);
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
    $x  = $pdf->GetX();
    $y  = $pdf->GetY();
    $pdf->Cell(18,5,$rowPC2[0],0,0,'L');
    $pdf->Cell(30,5,$rowPC2[1],0,0,'L');
    $x1 = $pdf->GetX();
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowPC2[3]))),0,'L');
    $y1 = $pdf->GetY();
    $h1 = $y1-$y;
    $pdf->SetXY($x1+40, $y);
    $pdf->Cell(25,5,$rowPC2[2],0,0,'L');
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowPC2[4]))),0,'L');
    $y2 = $pdf->GetY();
    $h2 = $y2-$y;
    $pdf->SetXY($x1+105, $y);
    $pdf->Cell(37,5,number_format($rowPC2[5]<0?$rowPC2[5]*-1:$rowPC2[5],2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $max = max($h1, $h2);
    $pdf->SetXY($x, $y);
    $pdf->Cell(18,$max,'',1,0,'L');
    $pdf->Cell(30,$max,'',1,0,'L');
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(25,$max,'',1,0,'L');    
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(37,$max,'',1,0,'L');
    $pdf->Ln($max);
    if($rowPC2[6]==1){
            $sumaPartidas += $rowPC2[5];
            $sumaP += $rowPC2[5];
    }else{
            $sumaPartidas += $rowPC2[5] *-1;
            $sumaP += $rowPC2[5] *-1;
    }	
}
while ($rowPC = mysqli_fetch_row($resultPC)) {
    $x  = $pdf->GetX();
    $y  = $pdf->GetY();
    $pdf->Cell(18,5,$rowPC[0],0,0,'L');
    $pdf->Cell(30,5,$rowPC[1],0,0,'L');
    $x1 = $pdf->GetX();
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowPC[3]))),0,'L');
    $y1 = $pdf->GetY();
    $h1 = $y1-$y;
    $pdf->SetXY($x1+40, $y);
    $pdf->Cell(25,5,$rowPC[2],0,0,'L');
    $pdf->MultiCell(40,4,utf8_decode(ucwords(mb_strtolower($rowPC[4]))),0,'L');
    $y2 = $pdf->GetY();
    $h2 = $y2-$y;
    $pdf->SetXY($x1+105, $y);
    $pdf->Cell(37,5,number_format($rowPC[5]<0?$rowPC[5]*-1:$rowPC[5],2,'.',','),0,0,'R');
    $pdf->Ln(5);
    $max = max($h1, $h2);
    $pdf->SetXY($x, $y);
    $pdf->Cell(18,$max,'',1,0,'L');
    $pdf->Cell(30,$max,'',1,0,'L');
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(25,$max,'',1,0,'L');    
    $pdf->Cell(40,$max,'',1,0,'L');
    $pdf->Cell(37,$max,'',1,0,'L');
    $pdf->Ln($max);
    if($rowPC[6]==1){
            $sumaPartidas += $rowPC[5];
            $sumaP += $rowPC[5];
    }else{
            $sumaPartidas += $rowPC[5] *-1;
            $sumaP += $rowPC[5] *-1;
    }	
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
$pdf->SetFont('Arial','B',10);
$pdf->SetX(50);
$pdf->Cell(50,5,'Saldo extracto',1,0,'L');
$pdf->Cell(50,5,number_format($saldoE[0],2,',','.'),1,0,'R');
$pdf->Ln(5);
$pdf->SetX(50);
$pdf->Cell(50,5,'Saldo Conciliado :',1,0,'L');
$pdf->Cell(50,5,number_format($saldoConciliado,2,',','.'),1,0,'R');
$pdf->Ln(5);
$pdf->SetX(50);
$rta ="";
$saldoC =  number_format($saldoConciliado,2,',','.');
if($saldoC =='0,00' || $saldoC=='-0,00' || $saldoC==-0.00){$rta ='CONCILIACIÓN CORRECTA';} else {$rta ='CONCILIACIÓN INCORRECTA';}
$pdf->Cell(100,5,utf8_decode($rta),1,0,'C');
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
if($pdf->GetY()>250){$pdf->AddPage();}
$pdf->Ln(25);
$yy1 = $pdf->GetY();
$xx1 = $pdf->GetX();
$i   = 1;
while ($firma = mysqli_fetch_row($tipComp)) {
    
    $pdf->SetXY($xx1,$yy1);
    $pdf->Cell(80, 0, '', 'B');
    $pdf->Ln(3);
    $pdf->SetX($xx1);
    if($numeroIdent=='891855951'){}else {
        $pdf->MultiCell(80, 3, utf8_decode($firma[0]),  0, 'L');
        $pdf->Ln(1);
        $pdf->SetX($xx1);
    }
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
    if($pdf->GetY()>250){$pdf->AddPage();
        $pdf->Ln(10);
        $yy1 = $pdf->GetY();
        $xx1 = $pdf->GetX();
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