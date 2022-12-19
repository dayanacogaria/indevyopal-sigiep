<?php
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 360);
$compania       = $_SESSION['compania'];
$usuario        = $_SESSION['usuario'];
$parmanno       = $_SESSION['anno'];
$calendario     = CAL_GREGORIAN;
$anno           = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$mes            = $mysqli->real_escape_string(''.$_POST['sltmes'].'');
$dia            = cal_days_in_month($calendario, $mes, $anno); 
$fecha          = $anno.'-'.$mes.'-'.$dia;
$fechaInicial   = $anno.'-'.'01-01';

$compini        = $mysqli->real_escape_string(''.$_POST["sltTci"].'');
$compfin        = $mysqli->real_escape_string(''.$_POST["sltTcf"].'');
$fechaini       = $mysqli->real_escape_string(''.$_POST["fechaini"].''); 
$fechafin       = $mysqli->real_escape_string(''.$_POST["fechafin"].'');
$cuentaini      = $mysqli->real_escape_string(''.$_POST["sltctai"].'');
$cuentafin      = $mysqli->real_escape_string(''.$_POST["sltctaf"].'');
$centroI      = $mysqli->real_escape_string(''.$_POST["sltCci"].'');
$centroF      = $mysqli->real_escape_string(''.$_POST["sltCcf"].'');

#********Consultas encabezado ****#########
#Consulta Mínima Cuenta
$cta1 = "SELECT codi_cuenta from gf_cuenta WHERE id_unico = $cuentaini";
$mincta = $mysqli->query($cta1);
$filac1 = mysqli_fetch_array($mincta);
$cuentaMin = $filac1['codi_cuenta'];   
#Fin Consulta Mínima Cuenta
#Inicio consulta Máxima Cuenta
$cta2 = "SELECT codi_cuenta from gf_cuenta WHERE id_unico = $cuentafin";
$maxcta = $mysqli->query($cta2);
$filac2 = mysqli_fetch_array($maxcta);
$cuentaMax = $filac2['codi_cuenta'];  

#************Datos Compañia************#
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
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];
$ruta_logo   = $rowC[6];

#************Centro Costo*************#
$centroIn= "SELECT nombre FROM gf_centro_costo WHERE sigla = '$centroI' AND parametrizacionanno =". $_SESSION['anno'];
$centroIn =$mysqli->query($centroIn);
$centroIn = mysqli_fetch_row($centroIn);
$centroIn = $centroIn[0];

$centroFi= "SELECT nombre FROM gf_centro_costo WHERE sigla = '$centroF' AND parametrizacionanno =". $_SESSION['anno'];
$centroFi =$mysqli->query($centroFi);
$centroFi = mysqli_fetch_row($centroFi);
$centroFi = $centroFi[0];

#************Comprobante*************#
$comp1 = "SELECT tc.id_unico,tc.sigla, tc.nombre
    FROM gf_tipo_comprobante tc 
    WHERE tc.sigla = '$compini' AND tc.compania = $compania";
$mincomp = $mysqli->query($comp1);
$filamin = mysqli_fetch_array($mincomp);
$compMin = $filamin['sigla'].' - '.$filamin['nombre'];

$comp2 = "SELECT  tc.id_unico,tc.sigla, tc.nombre
    FROM gf_tipo_comprobante tc 
    WHERE tc.sigla = '$compfin' AND tc.compania = $compania";
$maxcomp = $mysqli->query($comp2);
$filamax = mysqli_fetch_array($maxcomp);
$compMax = $filamax['sigla'].' - '.$filamax['nombre'];

class PDF extends FPDF {
    function Header() {
        global $razonsocial;
        global $nombreIdent;
        global $numeroIdent;
        global $direccinTer;
        global $telefonoTer;
        global $ruta_logo;
        global $centroIn;
        global $centroFi;
        global $cuentaMin;
        global $cuentaMax;
        global $compMin;
        global $compMax;
        global $fechaini;
        global $fechafin;
        $this->SetFont('Arial', 'B', 10);
        $this->SetY(10);
        if ($ruta_logo != '') {
            $this->Image('../' . $ruta_logo, 60, 6, 20);
        }
        $this->SetX(25);
        $this->Cell(315, 5, utf8_decode($razonsocial), 0, 0, 'C');
        $this->setX(25);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(315, 10, utf8_decode('CÓDIGO SGC'), 0, 0, 'R');
        $this->Ln(5);

        $this->SetFont('Arial', '', 8);
        $this->SetX(25);
        $this->Cell(315, 5, $nombreIdent . ': ' . $numeroIdent, 0, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->SetX(25);
        $this->Cell(315, 10, utf8_decode('VERSIÓN SGC'), 0, 0, 'R');
        $this->Ln(5);

        $this->SetFont('Arial', '', 8);
        $this->SetX(25);
        $this->Cell(315, 5, utf8_decode('AUXILIAR CONTABLE CENTRO DE COSTO'), 0, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->SetX(25);
        $this->Cell(315, 10, utf8_decode('FECHA SGC'), 0, 0, 'R');

        $this->Ln(3);
        $this->SetFont('Arial', '', 7);
        $this->SetX(25);
        $this->Cell(315, 5, utf8_decode('Entre Centros De Costo ' . $centroIn . ' y ' . $centroFi), 0, 0, 'C');
        $this->Ln(3);
        $this->SetFont('Arial', '', 7);
        $this->SetX(25);
        $this->Cell(315, 5, utf8_decode('Comprobantes ' . $compMin . ' y ' . $compMax), 0, 0, 'C');

        $this->Ln(3);
        $this->SetFont('Arial', '', 7);
        $this->SetX(25);
        $this->Cell(315, 5, utf8_decode('entre Fechas ' . $fechaini . ' y ' . $fechafin), 0, 0, 'C');

        $this->Ln(3);
        $this->SetFont('Arial', '', 7);
        $this->SetX(25);
        $this->Cell(315, 5, utf8_decode('y Cuentas ' . $cuentaMin . ' a ' . $cuentaMax), 0, 0, 'C');

        $this->Ln(5);

        $this->SetX(20);

        $this->Cell(15, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(17, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(65, 9, utf8_decode('Nombre del Tercero'), 1, 0, 'C');
        $this->Cell(70, 9, utf8_decode('Descripción'), 1, 0, 'C');
        $this->Cell(32, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(32, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(32, 9, utf8_decode(''), 1, 0, 'C');

        $this->SetX(20);

        $this->Cell(15, 9, utf8_decode('Fecha'), 0, 0, 'C');
        $this->Cell(17, 6, utf8_decode('Tipo'), 0, 0, 'C');
        $this->Cell(30, 6, utf8_decode('Número'), 0, 0, 'C');
        $this->Cell(30, 6, utf8_decode('Centro'), 0, 0, 'C');
        $this->Cell(60, 6, utf8_decode(''), 0, 0, 'C');
        $this->Cell(70, 9, utf8_decode(''), 0, 0, 'C');
        $this->Cell(32, 9, utf8_decode('Valor Débito'), 0, 0, 'C');
        $this->Cell(32, 9, utf8_decode('Valor Crédito'), 0, 0, 'C');
        $this->Cell(32, 9, utf8_decode('Saldo'), 0, 0, 'C');

        $this->Ln(4);

        $this->SetX(20);

        $this->Cell(15, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(17, 4, utf8_decode('Comprobante'), 0, 0, 'C');
        $this->Cell(30, 4, utf8_decode('Comprobante'), 0, 0, 'C');
        $this->Cell(30, 4, utf8_decode('de Costo'), 0, 0, 'C');
        $this->Cell(60, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(70, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(32, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(32, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(32, 4, utf8_decode(''), 0, 0, 'C');

        $this->Ln(6);
    }

    function Footer() {
        global $usuario;
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 8);
        $this->SetX(10);
        $this->Cell(90, 10, utf8_decode('Fecha: ' . date('d/m/Y')), 0, 0, 'L');
        $this->Cell(90, 10, utf8_decode('Máquina: ' . gethostname()), 0, 0, 'C');
        $this->Cell(90, 10, utf8_decode('Usuario: ' . strtoupper($usuario)), 0, 0, 'C');
        $this->Cell(65, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }

}

$pdf = new PDF('L', 'mm', 'Legal');
$nb = $pdf->AliasNbPages();
$pdf->AddPage();


#Consulta Cuentas
$sql3 = "SELECT DISTINCT COUNT(id_unico) as tctas, codi_cuenta 
    from gf_cuenta WHERE parametrizacionanno = $parmanno AND  codi_cuenta 
    BETWEEN '$cuentaMin' AND '$cuentaMax' ORDER BY codi_cuenta ASC";
$ccuentas = $mysqli->query($sql3);

while ($filactas = mysqli_fetch_array($ccuentas)) {
    $numctas = $filactas['tctas'];
}

$pdf->SetFont('Arial', '', 8);
$pdf->SetX(25);

$codd       = 0;
$totales    = 0;
$valorA     = 0;
$debito     = "";
$credito    = "";
$totaldeb   = 0.00;
$totalcred  = 0.00;
$saldoT     = 0;
$saldoTT    = 0;
$cnt        = 0;
$cuentas = "SELECT DISTINCT cuenta FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_cuenta c ON dc.cuenta= c.id_unico 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            WHERE cn.parametrizacionanno = $parmanno 
            AND dc.valor IS NOT NULL AND c.codi_cuenta BETWEEN '$cuentaMin' AND '$cuentaMax' 
            ORDER BY c.codi_cuenta ASC";
$cuenta = $mysqli->query($cuentas);
$yp         = $pdf->GetY();
$totaldebT  = 0;
$totalcredT = 0;
while ($filacuenta = mysqli_fetch_array($cuenta)) {
    $cuent = $filacuenta['cuenta'];
    $cnt = $cuent;
    #############Código cuenta actual#####################################    
    $idcuen = "SELECT codi_cuenta, nombre FROM gf_cuenta WHERE id_unico = '$cnt'";
    $codcuen = $mysqli->query($idcuen);
    while ($filacuen = mysqli_fetch_array($codcuen)) {
        $codicuenta = $filacuen['codi_cuenta'] . ' - ' . ucwords(mb_strtolower($filacuen['nombre']));
    }

    $fecha11    = trim($fechaini, '"');
    $fecha_div1 = explode("/", $fecha11);
    $dia11      = $fecha_div1[0];
    $mes11      = $fecha_div1[1];
    $anio11     = $fecha_div1[2];
    $fechaini1  = $anio11 . '/' . $mes11 . '/' . $dia11;
    $fecha12    = trim($fechafin, '"');
    $fecha_div2 = explode("/", $fecha12);
    $dia12      = $fecha_div2[0];
    $mes12      = $fecha_div2[1];
    $anio12     = $fecha_div2[2];
    $fechafin1  = $anio12 . '/' . $mes12 . '/' . $dia12;

    $sql = "SELECT DISTINCT
        cn.id_unico             as cnid,
        cn.tipocomprobante      as cntcom,
        cn.numero               as cnnum,
        cn.tercero              as cnter, 
        tr.id_unico             as trid,
        tr.nombreuno            as trnom1,
        tr.nombredos            as trnom2,
        tr.apellidouno          as trape1,
        tr.apellidodos          as trape2,
        tr.razonsocial          as trsoc,
        ti.nombre               as tinom,
        tr.numeroidentificacion as trnum,
        ct.id_unico             as ctid,
        ct.sigla               as ctnom,
        cc.id_unico             as ccid,
        cc.nombre               as ccnom,
        cn.numerocontrato       as cnnumcont,
        ec.nombre               as ecnom,
        cn.descripcion          as cndesc,
        dc.comprobante          as dccomp,
        dc.centrocosto          as dccos,
        cn.fecha                as dcfec,
        cen.id_unico            as cencid,
        cen.nombre              as cennom,
        cta.codi_cuenta         as nomcta,
        cta.naturaleza          as natcta,
        dc.valor                as dcvalor,
        dc.cuenta               as dcuenta,
        cn.fecha                as cnfec, 
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
        tr.apellidodos)) AS NOMBRE , dc.id_unico 
    FROM gf_comprobante_cnt cn
    LEFT JOIN gf_tipo_comprobante ct        ON cn.tipocomprobante = ct.id_unico
    LEFT JOIN gf_tipo_comprobante nom       ON cn.tipocomprobante = ct.id_unico
    LEFT JOIN gf_clase_contrato cc          ON cn.clasecontrato = cc.id_unico
    LEFT JOIN gf_estado_comprobante_cnt ec  ON cn.estado = ec.id_unico 
    LEFT JOIN gf_detalle_comprobante dc     ON cn.id_unico = dc.comprobante
    LEFT JOIN gf_tercero tr                 ON dc.tercero = tr.id_unico
    LEFT JOIN gf_tipo_identificacion ti     ON tr.tipoidentificacion = ti.id_unico
    LEFT JOIN gf_centro_costo cen           ON dc.centrocosto = cen.id_unico
    LEFT JOIN gf_cuenta cta                 ON cta.id_unico = dc.cuenta
    WHERE dc.valor IS NOT NULL AND dc.cuenta = '$cnt'
    AND cn.fecha BETWEEN '$fechaini1' AND '$fechafin1'
    AND ct.sigla BETWEEN '$compini' AND '$compfin' 
    AND cen.sigla BETWEEN '$centroI' AND '$centroF' 
    AND ct.sigla != 'SLI'  
    AND cn.parametrizacionanno = $parmanno 
    ORDER BY cn.fecha ASC";
    $cp = $mysqli->query($sql);
    ###########################################Fin Consulta Principal################################# 
    #Consulta Secundaria
    $a      = $_SESSION['anno'];
    $anno   = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $a";
    $anno   = $mysqli->query($anno);
    if(mysqli_num_rows($anno)>0){
        $anno = mysqli_fetch_row($anno);
        $anno = $anno[0];
    } else {
        $anno = date('Y');
    }
    if ($fechaini1 != $anno.'/01/01') {
        $sql2  = "SELECT DISTINCT
            cn.id_unico             as cnid,                                                
            cn.tipocomprobante      as cntcom,
            cn.numero               as cnnum,
            cn.tercero              as cnter, 
            tr.id_unico             as trid,
            tr.nombreuno            as trnom1,
            tr.nombredos            as trnom2,
            tr.apellidouno          as trape1,
            tr.apellidodos          as trape2,
            tr.razonsocial          as trsoc,
            ti.nombre               as tinom,
            tr.numeroidentificacion as trnum,
            ct.id_unico             as ctid,
            ct.sigla                as ctnom,
            cc.id_unico             as ccid,
            cc.nombre               as ccnom,
            cn.numerocontrato       as cnnumcont,
            ec.nombre               as ecnom,
            cn.descripcion          as cndesc,
            dc.comprobante          as dccomp,
            dc.centrocosto          as dccos,
            dc.fecha                as dcfec,
            cen.id_unico            as cencid,
            cen.nombre              as cennom,
            cta.codi_cuenta         as nomcta,
            cta.naturaleza          as natcta,
            dc.valor                as dcvalor,
            dc.cuenta               as dcuenta,
            dc.fecha                as cnfec, 
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
            tr.apellidodos)) AS NOMBRE, dc.id_unico  
        FROM gf_comprobante_cnt cn
        LEFT JOIN gf_tipo_comprobante ct        ON cn.tipocomprobante = ct.id_unico
        LEFT JOIN gf_tipo_comprobante nom       ON cn.tipocomprobante = ct.id_unico
        LEFT JOIN gf_clase_contrato cc          ON cn.clasecontrato = cc.id_unico
        LEFT JOIN gf_estado_comprobante_cnt ec  ON cn.estado = ec.id_unico 
        LEFT JOIN gf_detalle_comprobante dc     ON cn.id_unico = dc.comprobante
        LEFT JOIN gf_tercero tr                 ON dc.tercero = tr.id_unico 
        LEFT JOIN gf_tipo_identificacion ti     ON tr.tipoidentificacion = ti.id_unico 
        LEFT JOIN gf_centro_costo cen           ON dc.centrocosto = cen.id_unico
        LEFT JOIN gf_cuenta cta                 ON cta.id_unico = dc.cuenta
        WHERE dc.cuenta = '$cnt'
        AND cn.fecha BETWEEN '$anno-01-01' AND '$fechaP'
        AND ct.sigla BETWEEN '$compini' AND '$compfin' 
        AND cen.sigla BETWEEN '$centroI' AND '$centroF' 
        AND cn.parametrizacionanno = $parmanno 
        ORDER BY dc.fecha ASC";
    }  elseif ($fechaini1 == $anno . '/01/01') { //            
        $sql2 = "SELECT DISTINCT
            cn.id_unico             as cnid,                                                
            cn.tipocomprobante      as cntcom,
            cn.numero               as cnnum,
            cn.tercero              as cnter, 
            tr.id_unico             as trid,
            tr.nombreuno            as trnom1,
            tr.nombredos            as trnom2,
            tr.apellidouno          as trape1,
            tr.apellidodos          as trape2,
            tr.razonsocial          as trsoc,
            ti.nombre               as tinom,
            tr.numeroidentificacion as trnum,
            ct.id_unico             as ctid,
            ct.sigla                as ctnom,
            cc.id_unico             as ccid,
            cc.nombre               as ccnom,
            cn.numerocontrato       as cnnumcont,
            ec.nombre               as ecnom,
            cn.descripcion          as cndesc,
            dc.comprobante          as dccomp,
            dc.centrocosto          as dccos,
            dc.fecha                as dcfec,
            cen.id_unico            as cencid,
            cen.nombre              as cennom,
            cta.codi_cuenta         as nomcta,
            cta.naturaleza          as natcta,
            dc.valor                as dcvalor,
            dc.cuenta               as dcuenta,
            dc.fecha                as cnfec, 
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
            tr.apellidodos)) AS NOMBRE 
        FROM gf_comprobante_cnt cn
        LEFT JOIN gf_tipo_comprobante ct        ON cn.tipocomprobante = ct.id_unico
        LEFT JOIN gf_tipo_comprobante nom       ON cn.tipocomprobante = ct.id_unico
        LEFT JOIN gf_clase_contrato cc          ON cn.clasecontrato = cc.id_unico
        LEFT JOIN gf_estado_comprobante_cnt ec  ON cn.estado = ec.id_unico 
        LEFT JOIN gf_detalle_comprobante dc     ON cn.id_unico = dc.comprobante
        LEFT JOIN gf_tercero tr                 ON dc.tercero = tr.id_unico 
        LEFT JOIN gf_tipo_identificacion ti     ON tr.tipoidentificacion = ti.id_unico
        LEFT JOIN gf_centro_costo cen           ON dc.centrocosto = cen.id_unico
        LEFT JOIN gf_cuenta cta                 ON cta.id_unico = dc.cuenta
        WHERE dc.cuenta = '$cnt'
        AND cn.fecha = '$anno-01-01' 
        AND ct.clasecontable = 5
        AND cen.sigla BETWEEN '$centroI' AND '$centroF' 
        AND cn.parametrizacionanno = $parmanno 
        ORDER BY dc.fecha ASC";//Empty Query
    }
    $csaldo = $mysqli->query($sql2);
    $saldoTA = 0.00;
    while ($filasal = mysqli_fetch_array($csaldo)) {
        if ($filasal['natcta'] == 1) {
            if ($filasal['dcvalor'] >= 0) {
                $debA = $filasal['dcvalor'];
                $saldoTA = $saldoTA + $debA;
                $debitoA = number_format($filasal['dcvalor'], 2, '.', ',');
            } else {
                $debitoA = "0.00";
            }
            //$saldoT = $saldoT - $deb;
        } elseif ($filasal['natcta'] == 2) {
            if ($filasal['dcvalor'] <= 0) {
                $debA = $filasal['dcvalor'];
                $saldoTA = $saldoTA + $debA;
                $xA = (float) substr($filasal['dcvalor'], '1');
                $debitoA = number_format($xA, 2, '.', ',');
            } else {
                $debitoA = "0.00";
            }
        }
        #Fin Naturaleza Débito
        # 
        #Naturaleza Crédito
        if ($filasal['natcta'] == 2) {
            if ($filasal['dcvalor'] >= 0) {
                $crA = $filasal['dcvalor'];
                $saldoTA = $saldoTA + $crA;
                $creditoA = number_format($filasal['dcvalor'], 2, '.', ',');
            } else {
                $creditoA = "0.00";
            }
            //$saldoT = $saldoT - $cr;
        } elseif ($filasal['natcta'] == 1) {
            if ($filasal['dcvalor'] <= 0) {
                $crA = $filasal['dcvalor'];
                $saldoTA = $saldoTA + $crA;
                $yA = (float) substr($filasal['dcvalor'], '1');
                $creditoA = number_format($yA, 2, '.', ',');
            } else {
                $creditoA = "0.00";
            }
        }
    }

    if (mysqli_num_rows($cp) > 0) {
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetX(20);
        $pdf->Cell(105, 4, utf8_decode('Código Cuenta: ' . $codicuenta), 0);
        $pdf->Cell(20, 4, utf8_decode('Saldo Inicial: '), 0);
        $pdf->Cell(80, 4, utf8_decode('$' . number_format($saldoTA, 2, '.', ',')), 0);
        $pdf->Cell(205, 4, '', 0);
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 7);

        $tmp = 0;
        $cuenta1 = $cuentaini;
        $cuenta2 = 0;
        $saldoT = $saldoTA;
        $totaldeb=0;
        $totalcred=0;
        while ($fila = mysqli_fetch_array($cp)) {
            $deb = 0;
            if ($fila['natcta'] == 1) {
                if ($fila['dcvalor'] >= 0) {
                    $deb = $fila['dcvalor'];
                    $tmp = $deb;
                    $saldoT = $saldoT + $deb;
                    $debito = number_format($fila['dcvalor'], 2, '.', ',');
                    $totaldeb = $totaldeb + $fila['dcvalor'];
                } else {
                    $debito = "0.00";
                }
                //$saldoT = $saldoT - $deb;
            } elseif ($fila['natcta'] == 2) {
                if ($fila['dcvalor'] <= 0) {
                    $deb = $fila['dcvalor'];
                    $tmp = $deb;
                    $saldoT = $saldoT + $deb;
                    $x = (float) substr($fila['dcvalor'], '1');
                    $debito = number_format($x, 2, '.', ',');
                    $totaldeb = $totaldeb + $x;
                } else {
                    $debito = "0.00";
                }
            }
            #Fin Naturaleza Débito
            $cr = 0;
            #Naturaleza Crédito
            if ($fila['natcta'] == 2) {
                if ($fila['dcvalor'] >= 0) {
                    $cr = $fila['dcvalor'];
                    $saldoT = $saldoT + $cr;
                    $credito = number_format($fila['dcvalor'], 2, '.', ',');
                    $totalcred = $totalcred + $fila['dcvalor'];
                } else {
                    $credito = "0.00";
                }
                //$saldoT = $saldoT - $cr;
            } elseif ($fila['natcta'] == 1) {
                if ($fila['dcvalor'] <= 0) {
                    $cr = $fila['dcvalor'];
                    $saldoT = $saldoT + $cr;
                    $y = (float) substr($fila['dcvalor'], '1');
                    $credito = number_format($y, 2, '.', ',');
                    $totalcred = $totalcred + $y;
                } else {
                    $credito = "0.00";
                }
                //$saldoT = $saldoT - $cr;
            }
            #Fin Naturaleza Crédito
            $codd = $codd + 1;
            #Fecha - Comienzo
            $fechaCC = $fila['cnfec'];
            $fechaCC = trim($fila['cnfec'], '"');
            $fecha_div = explode("-", $fechaCC);
            $anio = $fecha_div[0];
            $mes = $fecha_div[1];
            $dia = $fecha_div[2];
            $fechaCC = $dia . '/' . $mes . '/' . $anio;
            #Fecha - Fin

            $pdf->SetX(20);
            $y1 = $pdf->GetY();
            $pdf->Cell(15, 4, utf8_decode($fechaCC), 0, 0, 'C');
            $pdf->Cell(17, 4, utf8_decode($fila['ctnom']), 0, 0, 'C');
            $pdf->Cell(30, 4, utf8_decode($fila['cnnum']), 0, 0, 'R');
            
            $x1 = $pdf->GetX();
            $pdf->MultiCell(30, 4, utf8_decode($fila['cennom']), 0, 'L');
            $y2 = $pdf->GetY();
            $a = $y2-$y1;
            $pdf->SetXY($x1+30, $y1);
            
            $x2 = $pdf->GetX();
            $pdf->MultiCell(65, 4, utf8_decode(ucwords(mb_strtolower($fila['NOMBRE']))), 0, 'L');
            $y3 = $pdf->GetY();
            $a2 = $y3 - $y1;
            $pdf->SetXY($x2+65, $y1);
            
            $x3 = $pdf->GetX();
            $pdf->MultiCell(70, 4, utf8_decode($fila['cndesc']), 0, 'J');
            $y4 = $pdf->GetY();
            $a3 = $y4 - $y1;
            $pdf->SetXY($x3+70, $y1);
            

            $pdf->Cell(32, 4, utf8_decode($debito), 0, 0, 'R');
            $pdf->Cell(32, 4, utf8_decode($credito), 0, 0, 'R');
            $pdf->Cell(32, 4, utf8_decode(number_format($saldoT, 2, '.', ',')), 0, 0, 'R');
            $alto = max($a,$a2,$a3);
            $pdf->Ln($alto);
            
            if($pdf->GetY()>180){
                $pdf->AddPage();
            }
        }
        $pdf->Ln(3);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(227, 4, 'TOTALES', 0, 0, 'R');
        $pdf->Cell(32, 4, utf8_decode(number_format($totaldeb, 2, '.', ',')), 0, 0, 'R');
        $pdf->Cell(32, 4, utf8_decode(number_format($totalcred, 2, '.', ',')), 0, 0, 'R');
        $pdf->Ln(3);
        $totaldebT = $totaldebT + $totaldeb;
        $totalcredT = $totalcredT + $totalcred;
    }
}

$pdf->Ln(3);
$pdf->SetX(220);
$pdf->Cell(123, 0.5, '', 1);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Ln(3);
$pdf->SetX(20);
$pdf->Cell(227, 4, 'TOTALES', 0, 0, 'R');
$pdf->Cell(32, 4, utf8_decode(number_format($totaldebT, 2, '.', ',')), 0, 0, 'R');
$pdf->Cell(32, 4, utf8_decode(number_format($totalcredT, 2, '.', ',')), 0, 0, 'R');
$pdf->Ln(5);
$pdf->SetX(20);
$pdf->Cell(323, 0.5, '', 1);


################################ ESTRUCTURA FIRMAS ##########################################
######### BUSQUEDA RESPONSABLE #########
$pdf->SetFont('Arial', 'B', 9);
$pdf->Ln(20);
$compania = $_SESSION['compania'];
$res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        WHERE LOWER(td.nombre) ='auxiliar contable' AND td.compania = $compania  ORDER BY rd.orden ASC";
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
            $pdf->Ln(-25);
            $pdf->SetX($x);
            $x = $x + 110;
            $i = $i + 1;
        }
    }
}


while (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output(0, 'Informe_Auxiliares_Contables_Centro_Costo (' . date('d/m/Y') . ').pdf', 0);
?>