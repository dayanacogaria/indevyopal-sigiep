<?php
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
session_start();
ob_start();
$con            = new ConexionPDO();
$compania       = $_SESSION['compania'];
$calendario     = CAL_GREGORIAN;
$parmanno       = $mysqli->real_escape_string('' . $_POST['sltAnnio'] . '');
$anno           = anno($parmanno);
$mesI           = $mysqli->real_escape_string('' . $_POST['sltmesi'] . '');
$diaI           = '01';
$fechaInicial   = $anno . '-' . $mesI . '-' . $diaI;
$mesF           = $mysqli->real_escape_string('' . $_POST['sltmesf'] . '');
$diaF           = cal_days_in_month($calendario, $mesF, $anno);
$fechaFinal     = $anno . '-' . $mesF . '-' . $diaF;
$fechaComparar  = $anno . '-' . '01-01';
$codigoI        = $mysqli->real_escape_string('' . $_POST['sltcodi'] . '');
$codigoF        = $mysqli->real_escape_string('' . $_POST['sltcodf'] . '');

$bl             = generarBalance($anno, $parmanno, $fechaInicial, $fechaFinal, $codigoI, $codigoF, $compania, 1);


class PDF extends FPDF {

// Cabecera de página  
    function Header() {
        global $nomcomp;
        global $tipodoc;
        global $numdoc;

        global $month1;
        global $month2;
        global $anno;
        global $digtv;
        global $ruta;

        if ($ruta != '') {
            $this->Image('../' . $ruta, 60, 6, 20);
        }
        $this->SetFont('Arial', 'B', 10);
        // Título
        $this->SetY(10);

        $this->Cell(330, 5, utf8_decode($nomcomp), 0, 0, 'C');
        // Salto de línea
        $this->setX(10);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(330, 10, utf8_decode('CÓDIGO SGC'), 0, 0, 'R');

        $this->Ln(5);

        $this->SetFont('Arial', '', 8);
        if (empty($digtv)) {
            $this->Cell(330, 5, $tipodoc . ': ' . $numdoc, 0, 0, 'C');
        } else {
            $this->Cell(330, 5, $tipodoc . ': ' . $numdoc . ' - ' . $digtv, 0, 0, 'C');
        }
        $this->SetFont('Arial', 'B', 8);
        $this->SetX(10);
        $this->Cell(330, 10, utf8_decode('VERSIÓN SGC'), 0, 0, 'R');

        $this->Ln(5);

        $this->SetFont('Arial', '', 8);
        $tit1 = $_POST['tituloH'];

        if (empty($tit1) || $tit1 == "") {
            $this->Cell(330, 5, utf8_decode('BALANCE PRUEBA'), 0, 0, 'C');
        } else {
            if ($tit1 == 'tesoreria') {
                $this->Cell(330, 5, utf8_decode('BALANCE TESORERIA CAJA Y BANCOS'), 0, 0, 'C');
            } else {
                $this->Cell(330, 5, utf8_decode('BALANCE PRUEBA'), 0, 0, 'C');
            }
        }
        $this->SetFont('Arial', 'B', 8);

        $this->SetX(10);
        $this->Cell(330, 10, utf8_decode('FECHA SGC'), 0, 0, 'R');

        $this->Ln(3);

        $this->SetFont('Arial', '', 7);
        $this->Cell(332, 5, utf8_decode('Entre ' . $month1 . ' y ' . $month2 . ' de ' . $anno), 0, 0, 'C');

        $this->Ln(5);

        
    }

    function Footer() {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 8);
        // Número de página
        $this->Cell(15);
        $this->Cell(25, 10, utf8_decode('Fecha: ' . date('d-m-Y')), 0, 0, 'L');
        $this->Cell(70);
        $this->Cell(35, 10, utf8_decode(''), 0);
        $this->Cell(60);
        $this->Cell(30, 10, utf8_decode(''), 0); //.get_current_user()
        $this->Cell(70);
        $this->Cell(0, 10, utf8_decode('Pagina ' . $this->PageNo() . '/{nb}'), 0, 0);
    }

}

// Creación del objeto de la clase heredada
$pdf = new PDF('L', 'mm', 'Legal');

//Asingación de valor a Mes 1    
switch ($mesI) {
    case 1:
        $month1 = "Enero";
        break;
    case 2:
        $month1 = "Febrero";
        break;
    case 3:
        $month1 = "Marzo";
        break;
    case 4:
        $month1 = "Abril";
        break;
    case 5:
        $month1 = "Mayo";
        break;
    case 6:
        $month1 = "Junio";
        break;
    case 7:
        $month1 = "Julio";
        break;
    case 8:
        $month1 = "Agosto";
        break;
    case 9:
        $month1 = "Septiembre";
        break;
    case 10:
        $month1 = "Octubre";
        break;
    case 11:
        $month1 = "Noviembre";
        break;
    case 12:
        $month1 = "Diciembre";
        break;
}
//Asingación de valor a Mes 2        
switch ($mesF) {
    case 1:
        $month2 = "Enero";
        break;
    case 2:
        $month2 = "Febrero";
        break;
    case 3:
        $month2 = "Marzo";
        break;
    case 4:
        $month2 = "Abril";
        break;
    case 5:
        $month2 = "Mayo";
        break;
    case 6:
        $month2 = "Junio";
        break;
    case 7:
        $month2 = "Julio";
        break;
    case 8:
        $month2 = "Agosto";
        break;
    case 9:
        $month2 = "Septiembre";
        break;
    case 10:
        $month2 = "Octubre";
        break;
    case 11:
        $month2 = "Noviembre";
        break;
    case 12:
        $month2 = "Diciembre";
        break;
}

#Igualación de Variable local a POST
$annio = $anno;

#Consulta Compañía para Encabezado
$compania = $_SESSION['compania'];

$consulta = "SELECT         t.razonsocial as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum, 
                                t.digitoverficacion as dig 
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

#Inicialización parámetros Header
$nomcomp = "";
$tipodoc = "";
$numdoc = "";

#Consulta para obtener parámetros Header
while ($fila = mysqli_fetch_array($cmp)) {
    $nomcomp = utf8_decode($fila['traz']);
    $tipodoc = utf8_decode($fila['tnom']);
    $numdoc = utf8_decode($fila['tnum']);
    $digtv = utf8_decode($fila['dig']);
}
$sqlRutaLogo = 'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = ' . $compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];

#Declaración Variable Número de Páginas
$nb = $pdf->AliasNbPages();

$saldoT = 0;
#Fin Consulta Secundaria
#Creación Objeto FPDF
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 8);

$codd = 0;
$totales = 0;
$valorA = 0;
$pdf->SetFont('Arial','B',8);
$pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
$pdf->Cell(94, 10, utf8_decode(''), 1, 0, 'C');
$pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
$pdf->Cell(94, 10, utf8_decode(''), 1, 0, 'C');
$pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
$pdf->Setx(10);
$pdf->Cell(47, 10, utf8_decode('Código'), 0, 0, 'C');
$pdf->Cell(94, 10, utf8_decode('Nombre'), 0, 0, 'C');
$pdf->Cell(47, 10, utf8_decode('Saldo Inicial'), 0, 0, 'C');
$pdf->Cell(94, 5, utf8_decode('Valor'), 0, 0, 'C');
$pdf->Cell(47, 10, utf8_decode('Saldo Final'), 0, 0, 'C');
$pdf->Ln(5);
$pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
$pdf->Cell(94, 5, utf8_decode(''), 0, 0, 'C');
$pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
$pdf->Cell(47, 5, utf8_decode('Débito'), 1, 0, 'C');
$pdf->Cell(47, 5, utf8_decode('Crédito'), 1, 0, 'C');
$pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
$pdf->Ln(5);
$pdf->Cell(326, 5, '', 0);

#Variables de valor de naturaleza
$pdf->SetY(39);
$cnt = 0;

#Consulta Cuentas
$sql3 = "SELECT DISTINCT 
                        tem.numero_cuenta   as numcuen, 
                        tem.nombre          as cnom,
                        tem.saldo_inicial   as salini,
                        tem.debito          as deb,
                        tem.credito         as cred,
                        tem.nuevo_saldo     as nsal,
                        cta.auxiliartercero as auxt, 
                        cta.cuentapuente    as cpuente 
        FROM            temporal_balance$compania tem
        LEFT JOIN       gf_cuenta cta       ON cta.codi_cuenta = tem.numero_cuenta 
                        AND cta.parametrizacionanno = $parmanno 
        ORDER BY        tem.numero_cuenta   ASC";
$ccuentas = $mysqli->query($sql3);

$sald = 0;
$debit = 0;
$credit = 0;
$nsald = 0;
while ($filactas = mysqli_fetch_array($ccuentas)) { 
    $sald = (float) ($filactas['salini']);
    $debit = (float) ($filactas['deb']);
    $credit = (float) ($filactas['cred']);
    $nsald = (float) ($filactas['nsal']);
    $a = $pdf->GetY();
        if($a>180)
        {
            $pdf->SetFont('Arial','B',8);
            $pdf->AddPage();            
            $pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
            $pdf->Cell(94, 10, utf8_decode(''), 1, 0, 'C');
            $pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
            $pdf->Cell(94, 10, utf8_decode(''), 1, 0, 'C');
            $pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
            $pdf->Setx(10);
            $pdf->Cell(47, 10, utf8_decode('Código'), 0, 0, 'C');
            $pdf->Cell(94, 10, utf8_decode('Nombre'), 0, 0, 'C');
            $pdf->Cell(47, 10, utf8_decode('Saldo Inicial'), 0, 0, 'C');
            $pdf->Cell(94, 5, utf8_decode('Valor'), 0, 0, 'C');
            $pdf->Cell(47, 10, utf8_decode('Saldo Final'), 0, 0, 'C');
            $pdf->Ln(5);
            $pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
            $pdf->Cell(94, 5, utf8_decode(''), 0, 0, 'C');
            $pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
            $pdf->Cell(47, 5, utf8_decode('Débito'), 1, 0, 'C');
            $pdf->Cell(47, 5, utf8_decode('Crédito'), 1, 0, 'C');
            $pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
            $pdf->Ln(5);
            $pdf->Cell(326, 5, '', 0);
            $pdf->Ln(2);
            
        }
        $pdf->SetFont('Arial','',8);
    if($filactas['cpuente']==1){
        
    } else {
    if ($sald == 0 && $debit == 0 && $credit == 0 && $nsald ==0 && $filactas['auxt'] != 1) {
        #########si los hijos tienen saldo####
        $sh = "SELECT id_unico, 
                SUM(IF(saldo_inicial<0, saldo_inicial*-1,saldo_inicial))   as salID,
                SUM(IF(debito<0, debito*-1,debito))   as debI, 
                SUM(IF(credito<0, credito*-1,credito))   as credI,
                SUM(IF(nuevo_saldo<0, nuevo_saldo*-1,nuevo_saldo))   as salID  
                FROM temporal_balance$compania 
                WHERE saldo_inicial IS NOT NULL 
                AND debito IS NOT NULL AND credito IS NOT NULL 
                AND nuevo_saldo IS NOT NULL 
                AND cod_predecesor = ".$filactas['numcuen']; 
        $sh = $mysqli->query($sh);
        if(mysqli_num_rows($sh)>0){
                $sh = mysqli_fetch_row($sh);
                if($sh[1]==0 && $sh[2]==0 && $sh[3]==0 && $sh[4]==0) {
                    
                } else {
                    $pdf->Cell(47, 4, utf8_decode($filactas['numcuen']), 0, 0, 'L');
                    $y = $pdf->GetY();
                    $x = $pdf->GetX();
                    $pdf->MultiCell(94, 4, utf8_decode(ucwords(mb_strtolower($filactas['cnom']))), 0, 'L');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    $px = $x + 94;
                    $pdf->Ln(-$h);
                    $pdf->SetX($px);
                    $pdf->Cell(47, 4, number_format($sald, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(47, 4, number_format($debit, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(47, 4, number_format($credit, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(47, 4, number_format($nsald, 2, '.', ','), 0, 0, 'R');
                    $pdf->Ln($h);
                }
        } else {

        }
    } else {
        #######################################################################################################################################################
        #Validamos si auxiliar tercero es 1
        #######################################################################################################################################################
        if ($filactas['auxt'] == 1) {
            echo $cod_cuenta = $filactas['numcuen'];
            ##########################BUSCAR LOS TERCEROS DE ESA CUENTA 
            $sqlTT = "SELECT  DISTINCT  dc.tercero, dc.cuenta, 
                            IF(CONCAT_WS(' ',
                                                tr.nombreuno,
                                                tr.nombredos,
                                                tr.apellidouno,
                                                tr.apellidodos) 
                                                IS NULL OR CONCAT_WS(' ',
                                                tr.nombreuno,
                                                tr.nombredos,
                                                tr.apellidouno,
                                                tr.apellidodos) = '',
                                              (tr.razonsocial),
                                              CONCAT_WS(' ',
                                                tr.nombreuno,
                                                tr.nombredos,
                                                tr.apellidouno,
                                                tr.apellidodos)) AS NOMBRE, 
                               CONCAT(c.codi_cuenta, ' - ', tr.numeroidentificacion), 
                               c.naturaleza 
                            FROM  gf_detalle_comprobante dc   
                            LEFT JOIN gf_cuenta c ON dc.cuenta    = c.id_unico
                            LEFT JOIN   gf_comprobante_cnt cn       ON cn.id_unico  = dc.comprobante
                            LEFT JOIN   gf_tercero tr              ON tr.id_unico = dc.tercero                        
                            LEFT JOIN
                                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                            LEFT JOIN
                                gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                            WHERE       c.auxiliartercero   = 1 
                            AND         c.codi_cuenta       = '$cod_cuenta' 
                            AND         cn.parametrizacionanno =$parmanno AND cc.id_unico !='20' 
                            ORDER BY    tr.numeroidentificacion ASC";
            $resultTT = $mysqli->query($sqlTT);
            $saldoIT = 0;
            $debitoT = 0;
            $creditoT = 0;
            $numtermov = 0;
            if (mysqli_num_rows($resultTT) > 0) {
            while ($rowTT = mysqli_fetch_row($resultTT)) {
                $terceroD = $rowTT[0];
                $cuentaD = $rowTT[1];
                ##########BUSCAR MOVIMIENTOS POR TERCERO ###############
                #SI FECHA INICIAL =01 DE ENERO
                $fechaPrimera = $anno . '-01-01';
                if ($fechaInicial == $fechaPrimera) {
                    #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
                    $fechaMax = $anno . '-12-31';
                    $com = "SELECT SUM(valor)
                                  FROM
                                    gf_detalle_comprobante dc
                                  LEFT JOIN
                                    gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                  LEFT JOIN
                                    gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                  LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                  WHERE
                                    cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                                    AND cc.id_unico = '5' 
                                    AND dc.cuenta = '$cuentaD' 
                                    AND cp.parametrizacionanno =$parmanno    
                                    AND dc.tercero = $terceroD ";
                    $com = $mysqli->query($com);
                    if (mysqli_num_rows($com) > 0) {
                        $saldo = mysqli_fetch_row($com);
                        $saldoIT = $saldo[0];
                    } else {
                        $saldoIT = 0;
                    }

                    #DEBITOS
                    $deb = "SELECT SUM(valor)
                                FROM
                                  gf_detalle_comprobante dc
                                LEFT JOIN
                                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                LEFT JOIN
                                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                WHERE valor>0 AND 
                                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                                  AND cc.id_unico != '5' AND cc.id_unico !='20' 
                                  AND dc.cuenta = '$cuentaD' 
                                  AND cp.parametrizacionanno =$parmanno    
                                  AND dc.tercero = $terceroD";
                    $debt = $mysqli->query($deb);
                    if (mysqli_num_rows($debt) > 0) {
                        $debito = mysqli_fetch_row($debt);
                        $debitoT = $debito[0];
                    } else {
                        $debitoT = 0;
                    }

                    #CREDITOS
                    $cr = "SELECT SUM(valor)
                                FROM
                                  gf_detalle_comprobante dc
                                LEFT JOIN
                                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                LEFT JOIN
                                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                WHERE valor<0 AND 
                                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                                  AND cc.id_unico != '5' AND cc.id_unico != '20' 
                                  AND dc.cuenta = '$cuentaD' 
                                  AND cp.parametrizacionanno =$parmanno    
                                  AND dc.tercero = $terceroD";
                    $cred = $mysqli->query($cr);
                    if (mysqli_num_rows($cred) > 0) {
                        $credito = mysqli_fetch_row($cred);
                        $creditoT = $credito[0];
                    } else {
                        $creditoT = 0;
                    }

                    #SI FECHA INICIAL !=01 DE ENERO
                } else {
                    #TRAE EL SALDO INICIAL
                    $sInicial = "SELECT SUM(dc.valor) 
                                from 
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE  dc.cuenta = '$cuentaD' 
                                AND dc.tercero = $terceroD 
                                AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaInicial' 
                                AND cn.parametrizacionanno =$parmanno  AND cc.id_unico != '20' ";
                    $saldt = $mysqli->query($sInicial);
                    if (mysqli_num_rows($saldt) > 0) {
                        $saldo = mysqli_fetch_row($saldt);
                        $saldoIT = $saldo[0];
                    } else {
                        $saldoIT = 0;
                    }
                    #DEBITOS
                    $deb = "SELECT SUM(dc.valor) 
                                from 
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE dc.valor>0 AND  dc.cuenta = '$cuentaD' 
                                  AND cn.parametrizacionanno =$parmanno  AND cc.id_unico != '20'   
                                  AND dc.tercero = $terceroD  
                                  AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
                    $debt = $mysqli->query($deb);
                    if (mysqli_num_rows($debt) > 0) {
                        $debito = mysqli_fetch_row($debt);
                        $debitoT = $debito[0];
                    } else {
                        $debitoT = 0;
                    }
                    #CREDITOS
                    $cr = "SELECT SUM(dc.valor) 
                                FROM 
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE dc.valor<0 AND  dc.cuenta = '$cuentaD' 
                                    AND cn.parametrizacionanno =$parmanno  AND cc.id_unico != '20'     
                                    AND dc.tercero = $terceroD  
                                    AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
                    $cred = $mysqli->query($cr);

                    if (mysqli_num_rows($cred) > 0) {
                        $credito = mysqli_fetch_row($cred);
                        $creditoT = $credito[0];
                    } else {
                        $creditoT = 0;
                    }
                }
                #SI LA NATURALEZA ES DEBITO
                if ($rowTT[4] == '1') {
                    if ($creditoT < 0) {
                        $creditoT = (float) substr($creditoT, '1');
                    }
                    $saldoNuevoT = $saldoIT + $debitoT - $creditoT;

                    $d = $debitoT;
                    $c = $creditoT;
                    #SI LA NATURALEZA ES CREDITO
                } else {
                    if ($creditoT < 0) {
                        $creditoT = (float) substr($creditoT, '1');
                    }
                    $saldoNuevoT = $saldoIT - $creditoT + $debitoT;

                    $d = $creditoT;
                    $c = $debitoT;
                }

                $saldIT = (float) ($saldoIT);
                $debitT = (float) ($d);
                $creditT = (float) ($c);
                $nsaldT = (float) ($saldoNuevoT);

                if ($saldIT == 0 && $debitT == 0 && $creditT == 0) {
                    
                } else {
                    $numtermov +=1;
                }
            }
            if($numtermov>0){
                echo $a2 = $pdf->GetY();
                if($a2>180)
                {
                    $pdf->SetFont('Arial','B',8);
                    $pdf->AddPage();            
                    $pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
                    $pdf->Cell(94, 10, utf8_decode(''), 1, 0, 'C');
                    $pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
                    $pdf->Cell(94, 10, utf8_decode(''), 1, 0, 'C');
                    $pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
                    $pdf->Setx(10);
                    $pdf->Cell(47, 10, utf8_decode('Código'), 0, 0, 'C');
                    $pdf->Cell(94, 10, utf8_decode('Nombre'), 0, 0, 'C');
                    $pdf->Cell(47, 10, utf8_decode('Saldo Inicial'), 0, 0, 'C');
                    $pdf->Cell(94, 5, utf8_decode('Valor'), 0, 0, 'C');
                    $pdf->Cell(47, 10, utf8_decode('Saldo Final'), 0, 0, 'C');
                    $pdf->Ln(5);
                    $pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
                    $pdf->Cell(94, 5, utf8_decode(''), 0, 0, 'C');
                    $pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
                    $pdf->Cell(47, 5, utf8_decode('Débito'), 1, 0, 'C');
                    $pdf->Cell(47, 5, utf8_decode('Crédito'), 1, 0, 'C');
                    $pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
                    $pdf->Ln(5);
                    $pdf->Cell(326, 5, '', 0);
                    $pdf->Ln(2);
                    $pdf->SetFont('Arial','',8);

                }
                $pdf->Cell(47, 4, utf8_decode($filactas['numcuen']), 0, 0, 'L');
                $y = $pdf->GetY();
                $x = $pdf->GetX();
                $pdf->MultiCell(94, 4, utf8_decode(ucwords(mb_strtolower($filactas['cnom']))), 0, 'L');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                $px = $x + 94;
                $pdf->Ln(-$h);
                $pdf->SetX($px);
                $pdf->Cell(47, 4, number_format($sald, 2, '.', ','), 0, 0, 'R');
                $pdf->Cell(47, 4, number_format($debit, 2, '.', ','), 0, 0, 'R');
                $pdf->Cell(47, 4, number_format($credit, 2, '.', ','), 0, 0, 'R');
                $pdf->Cell(47, 4, number_format($nsald, 2, '.', ','), 0, 0, 'R');
                echo $h;
                $pdf->Ln($h);
                $sqlTT = "SELECT  DISTINCT  dc.tercero, dc.cuenta, 
                            IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                              (tr.razonsocial),
                              CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE, 
                               CONCAT(c.codi_cuenta, ' - ', tr.numeroidentificacion), 
                               c.naturaleza 
                            FROM  gf_detalle_comprobante dc   
                            LEFT JOIN gf_cuenta c ON dc.cuenta    = c.id_unico
                            LEFT JOIN   gf_comprobante_cnt cn       ON cn.id_unico  = dc.comprobante
                            LEFT JOIN   gf_tercero tr              ON tr.id_unico = dc.tercero                        
                            LEFT JOIN
                                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                            LEFT JOIN
                                gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                            WHERE       c.auxiliartercero   = 1 
                            AND         c.codi_cuenta       = '$cod_cuenta' 
                            AND         cn.parametrizacionanno =$parmanno AND cc.id_unico !='20' 
                            ORDER BY    tr.numeroidentificacion ASC";
                $resultTT = $mysqli->query($sqlTT);
                while ($rowTT = mysqli_fetch_row($resultTT)) {
                $terceroD = $rowTT[0];
                $cuentaD = $rowTT[1];
                ##########BUSCAR MOVIMIENTOS POR TERCERO ###############
                #SI FECHA INICIAL =01 DE ENERO
                $fechaPrimera = $anno . '-01-01';
                if ($fechaInicial == $fechaPrimera) {
                    #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
                    $fechaMax = $anno . '-12-31';
                    $com = "SELECT SUM(valor)
                                  FROM
                                    gf_detalle_comprobante dc
                                  LEFT JOIN
                                    gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                  LEFT JOIN
                                    gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                  LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                  WHERE
                                    cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                                    AND cc.id_unico = '5' 
                                    AND dc.cuenta = '$cuentaD' 
                                    AND dc.tercero = $terceroD AND cp.parametrizacionanno =$parmanno";
                    $com = $mysqli->query($com);
                    if (mysqli_num_rows($com) > 0) {
                        $saldo = mysqli_fetch_row($com);
                        $saldoIT = $saldo[0];
                    } else {
                        $saldoIT = 0;
                    }

                    #DEBITOS
                    $deb = "SELECT SUM(valor)
                                FROM
                                  gf_detalle_comprobante dc
                                LEFT JOIN
                                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                LEFT JOIN
                                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                WHERE valor>0 AND 
                                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                                  AND cc.id_unico != '5' AND cc.id_unico != '20' 
                                  AND dc.cuenta = '$cuentaD' 
                                  AND dc.tercero = $terceroD AND cp.parametrizacionanno =$parmanno";
                    $debt = $mysqli->query($deb);
                    if (mysqli_num_rows($debt) > 0) {
                        $debito = mysqli_fetch_row($debt);
                        $debitoT = $debito[0];
                    } else {
                        $debitoT = 0;
                    }

                    #CREDITOS
                    $cr = "SELECT SUM(valor)
                                FROM
                                  gf_detalle_comprobante dc
                                LEFT JOIN
                                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                LEFT JOIN
                                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                WHERE valor<0 AND 
                                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                                  AND cc.id_unico != '5' AND cc.id_unico != '20' 
                                  AND dc.cuenta = '$cuentaD' 
                                  AND dc.tercero = $terceroD AND cp.parametrizacionanno =$parmanno";
                    $cred = $mysqli->query($cr);
                    if (mysqli_num_rows($cred) > 0) {
                        $credito = mysqli_fetch_row($cred);
                        $creditoT = $credito[0];
                    } else {
                        $creditoT = 0;
                    }

                    #SI FECHA INICIAL !=01 DE ENERO
                } else {
                    #TRAE EL SALDO INICIAL
                    $sInicial = "SELECT SUM(dc.valor) 
                                from gf_detalle_comprobante dc 
                                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                  gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE  dc.cuenta = '$cuentaD' 
                                AND dc.tercero = $terceroD 
                                AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20' 
                                AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaInicial' ";
                    $sald = $mysqli->query($sInicial);
                    if (mysqli_num_rows($sald) > 0) {
                        $saldo = mysqli_fetch_row($sald);
                        $saldoIT = $saldo[0];
                    } else {
                        $saldoIT = 0;
                    }
                    #DEBITOS
                    $deb = "SELECT SUM(dc.valor) 
                                FROM  
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                  gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE dc.valor>0 AND  dc.cuenta = '$cuentaD' 
                                    AND dc.tercero = $terceroD  
                                    AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20' 
                                    AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
                    $debt = $mysqli->query($deb);
                    if (mysqli_num_rows($debt) > 0) {
                        $debito = mysqli_fetch_row($debt);
                        $debitoT = $debito[0];
                    } else {
                        $debitoT = 0;
                    }
                    #CREDITOS
                    $cr = "SELECT SUM(dc.valor) 
                                FROM  
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                  gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE dc.valor<0 AND  dc.cuenta = '$cuentaD' 
                                    AND dc.tercero = $terceroD  
                                    AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20' 
                                    AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
                    $cred = $mysqli->query($cr);

                    if (mysqli_num_rows($cred) > 0) {
                        $credito = mysqli_fetch_row($cred);
                        $creditoT = $credito[0];
                    } else {
                        $creditoT = 0;
                    }
                }
                #SI LA NATURALEZA ES DEBITO
                if ($rowTT[4] == '1') {
                    if ($creditoT < 0) {
                        $creditoT = (float) substr($creditoT, '1');
                    }
                    $saldoNuevoT = $saldoIT + $debitoT - $creditoT;

                    $d = $debitoT;
                    $c = $creditoT;
                    #SI LA NATURALEZA ES CREDITO
                } else {
                    if ($creditoT < 0) {
                        $creditoT = (float) substr($creditoT, '1');
                    }
                    $saldoNuevoT = $saldoIT - $creditoT + $debitoT;

                    $d = $creditoT;
                    $c = $debitoT;
                }

                $saldIT = (float) ($saldoIT);
                $debitT = (float) ($d);
                $creditT = (float) ($c);
                $nsaldT = (float) ($saldoNuevoT);

                if ($saldIT == 0 && $debitT == 0 && $creditT == 0) {
                    
                } else {
                    $at = $pdf->GetY();
                    if($at>180)
                    {
                        $pdf->SetFont('Arial','B',8);
                        $pdf->AddPage();            
                        $pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
                        $pdf->Cell(94, 10, utf8_decode(''), 1, 0, 'C');
                        $pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
                        $pdf->Cell(94, 10, utf8_decode(''), 1, 0, 'C');
                        $pdf->Cell(47, 10, utf8_decode(''), 1, 0, 'C');
                        $pdf->Setx(10);
                        $pdf->Cell(47, 10, utf8_decode('Código'), 0, 0, 'C');
                        $pdf->Cell(94, 10, utf8_decode('Nombre'), 0, 0, 'C');
                        $pdf->Cell(47, 10, utf8_decode('Saldo Inicial'), 0, 0, 'C');
                        $pdf->Cell(94, 5, utf8_decode('Valor'), 0, 0, 'C');
                        $pdf->Cell(47, 10, utf8_decode('Saldo Final'), 0, 0, 'C');
                        $pdf->Ln(5);
                        $pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
                        $pdf->Cell(94, 5, utf8_decode(''), 0, 0, 'C');
                        $pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
                        $pdf->Cell(47, 5, utf8_decode('Débito'), 1, 0, 'C');
                        $pdf->Cell(47, 5, utf8_decode('Crédito'), 1, 0, 'C');
                        $pdf->Cell(47, 5, utf8_decode(''), 0, 0, 'C');
                        $pdf->Ln(5);
                        $pdf->Cell(326, 5, '', 0);
                        $pdf->Ln(2);
                        $pdf->SetFont('Arial','',8);
                    }
                    echo $numtermov +=1;
                    $pdf->Cell(47, 4, utf8_decode($rowTT[3]), 0, 0, 'L');
                    $y = $pdf->GetY();
                    $x = $pdf->GetX();
                    $pdf->MultiCell(94, 4, utf8_decode(ucwords(mb_strtolower($rowTT[2]))), 0, 'L');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    $px = $x + 94;
                    $pdf->Ln(-$h);
                    $pdf->SetX($px);
                    ###############################################################################################################################################
                    # Consulta para obtener los debitos
                    ###############################################################################################################################################

                    $pdf->Cell(47, 4, number_format($saldIT, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(47, 4, number_format($debitT, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(47, 4, number_format($creditT, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(47, 4, number_format($nsaldT, 2, '.', ','), 0, 0, 'R');
                    $pdf->Ln($h);
                }
            }
            }
            } else {
                if ($sald == 0 && $debit == 0 && $credit == 0 && $nsald == 0) {
                    
                } else {
                    $pdf->Cell(47, 4, utf8_decode($filactas['numcuen']), 0, 0, 'L');
                    $y = $pdf->GetY();
                    $x = $pdf->GetX();
                    $pdf->MultiCell(94, 4, utf8_decode(ucwords(mb_strtolower($filactas['cnom']))), 0, 'L');
                    $y2 = $pdf->GetY();
                    $ht = $y2 - $y;
                    $px = $x + 94;
                    $pdf->Ln(-$ht);
                    $pdf->SetX($px);
                    $pdf->Cell(47, 4, number_format($sald, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(47, 4, number_format($debit, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(47, 4, number_format($credit, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(47, 4, number_format($nsald, 2, '.', ','), 0, 0, 'R');
                    $pdf->Ln($ht);
                }
            }

            
        }
        else {
            $pdf->Cell(47, 4, utf8_decode($filactas['numcuen']), 0, 0, 'L');
            $y = $pdf->GetY();
            $x = $pdf->GetX();
            $pdf->MultiCell(94, 4, utf8_decode(ucwords(mb_strtolower($filactas['cnom']))), 0, 'L');
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
            $px = $x + 94;
            $pdf->Ln(-$h);
            $pdf->SetX($px);
            $pdf->Cell(47, 4, number_format($sald, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(47, 4, number_format($debit, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(47, 4, number_format($credit, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(47, 4, number_format($nsald, 2, '.', ','), 0, 0, 'R');
            $pdf->Ln($h);
        }
    }
    }
}
$pdf->SetFont('Arial','B',8);
$pdf->Cell(329, 0.5, utf8_decode(''), 1, 0, 'C');
$pdf->Ln(2);
$pdf->Cell(141, 4, utf8_decode(''), 0, 0, 'C');
$pdf->Cell(47, 4, utf8_decode(''), 0, 0, 'R');
$pdf->Cell(47, 4, number_format($bl["totaldeb"], 2, '.', ','), 0, 0, 'R');
$pdf->Cell(47, 4, number_format($bl["totalcred"], 2, '.', ','), 0, 0, 'R');
$pdf->Cell(47, 4, utf8_decode(''), 0, 0, 'R');

$pdf->AddPage();
$pdf->Ln(3);
 ##########################RESUMEN#################################################
 $rs = "SELECT DISTINCT id_unico as id, 
                 numero_cuenta  as codigo, 
                 nombre         as nombre, 
                 saldo_inicial  as inicial, 
                 debito         as debito, 
                 credito        as credito, 
                 nuevo_saldo    as nuevo, 
                 naturaleza     as naturalezaR 
               FROM temporal_balance$compania  
               WHERE LENGTH(numero_cuenta) = (1) ORDER BY numero_cuenta ASC";
 $rs = $mysqli->query($rs);
 $pdf->SetFont('Arial','B',8);
 $pdf->Cell(329,4,utf8_decode('RESUMEN'),1,0,'C'); 
 $pdf->Ln(4);
 $pdf->Cell(49,8,utf8_decode('CÓDIGO'),1,0,'C'); 
 $pdf->Cell(100,8,utf8_decode('NOMBRE'),1,0,'C'); 
 $pdf->Cell(45,8,utf8_decode('SALDO INICIAL'),1,0,'C');
 $pdf->Cell(90,4,utf8_decode('MOVIMIENTOS'),1,0,'C');
 $pdf->Cell(45,8,utf8_decode('SALDO FINAL'),1,0,'C');
 $pdf->ln(4);
 $pdf->Cell(49,4, utf8_decode(''),0,0,'C');
 $pdf->Cell(100,4,utf8_decode(''),0,0,'C');
 $pdf->Cell(45,4,utf8_decode(''),0,0,'C');
 $pdf->Cell(45,4,utf8_decode('Débito'),1,0,'C');
 $pdf->Cell(45,4,utf8_decode('Crédito'),1,0,'C');
 $pdf->Cell(45,4,utf8_decode(''),0,0,'C');
 $pdf->Ln(4);

$pdf->SetFont('Arial','',8);  
$anteriortotal =0;
$debitototal=0;
$creditototal=0;
$nuevototal=0;

    while ($row1 = mysqli_fetch_array($rs)) {
        $sald   = (float)($row1['inicial']);
        $debit  = (float)($row1['debito']);
        $credit = (float)($row1['credito']);
        $nsald  = (float)($row1['nuevo']);
        $naturalezaR = $row1['naturalezaR'];
        
        $pdf->Cell(49,4,utf8_decode($row1['codigo']),0,0,'R');   
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(100,4,utf8_decode(ucwords(mb_strtolower($row1['nombre']))),0,'L');   
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 100;
        $pdf->Ln(-$h);
        $pdf->SetX($px);
        $pdf->Cell(45,4,number_format($sald,2,'.',','),0,0,'R');
        $pdf->Cell(45,4,number_format($debit,2,'.',','),0,0,'R');
        $pdf->Cell(45,4,number_format($credit,2,'.',','),0,0,'R');
        $pdf->Cell(45,4,number_format($nsald,2,'.',','),0,0,'R');
        $pdf->Ln($h);
        
        $debitototal +=$debit;
       $creditototal +=$credit;
       switch ($row1['codigo']){
                   case 1:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   case 2:
                       $anteriortotal -=$sald;
                       $nuevototal -=$nsald;
                   break;
                   case 3:
                       $anteriortotal -=$sald;
                       $nuevototal -=$nsald;
                   break;
                   case 4:
                       $anteriortotal -=$sald;
                       $nuevototal -=$nsald;
                   break;
                   case 5:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   case 6:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   case 7:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   default :
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;


               }
             
}
##################################################################################
##############################TOTALES#############################################
$pdf->SetFont('Arial','B',8);   
$pdf->Cell(329,0.5,utf8_decode(''),1,0,'C'); 
$pdf->Ln(2);
$pdf->Cell(149,4,utf8_decode('TOTALES'),0,0,'C'); 
$pdf->Cell(45,4,number_format($anteriortotal,2,'.',','),0,0,'R');
$pdf->Cell(45,4,number_format($debitototal,2,'.',','),0,0,'R');
$pdf->Cell(45,4,number_format($creditototal,2,'.',','),0,0,'R');
$pdf->Cell(45,4,number_format($nuevototal,2,'.',','),0,0,'R');
$pdf->Ln(2);


################################ ESTRUCTURA FIRMAS ##########################################
######### BUSQUEDA RESPONSABLE #########
$pdf->SetFont('Arial', 'B', 9);
$pdf->Ln(30);
$compania = $_SESSION['compania'];
$res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        WHERE LOWER(td.nombre) ='balance de prueba' AND td.compania = $compania  ORDER BY rd.orden ASC";
$res = $mysqli->query($res);
$i = 0;
$x = 130;
#ESTRUCTURA
if (mysqli_num_rows($res) > 0) {
    $h = 4;
    while ($row2 = mysqli_fetch_row($res)) {

        $ter = "SELECT IF(CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos) 
                    IS NULL OR CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos) = '',
                    (tr.razonsocial),
                    CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos)) AS NOMBREC, "
                . "tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional "
                . "FROM gf_tercero tr "
                . "LEFT JOIN gf_cargo_tercero ct ON tr.id_unico = ct.tercero "
                . "LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico "
                . "WHERE tr.id_unico ='$row2[0]'";

        $ter = $mysqli->query($ter);
        $ter = mysqli_fetch_row($ter);
        if (!empty($ter[3])) {
            $responsable = "\n\n___________________________________ \n" . (mb_strtoupper($ter[0])) . "\n" . mb_strtoupper($ter[2]) . "\n T.P:" . (mb_strtoupper($ter[3]));
        } else {
            $responsable = "\n\n___________________________________ \n" . (mb_strtoupper($ter[0])) . "\n" . mb_strtoupper($ter[2]) . "\n";
        }

        $pdf->MultiCell(110, 4, utf8_decode($responsable), 0, 'L');

        if ($i == 1) {
            $pdf->Ln(15);
            $x = 130;
            $i = 0;
        } else {
            $pdf->Ln(-20);
            $pdf->SetX($x);
            $x = $x + 110;
            $i = $i + 1;
        }
    }
}
##################################################################################

while (ob_get_length()) {
    ob_end_clean();
}


$pdf->Output(0, utf8_decode('Informe_Balance_Prueba_terceros(' . date('d-m-Y') . ').pdf'), 0);
 
?>