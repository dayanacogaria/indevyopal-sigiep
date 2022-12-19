<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#15/08/2018 | Erica G. | Archivo Creado
#####################################################################################
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../numeros_a_letras.php');
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);

#   ************    Datos Recibe    ************    #
$fechaI         = $_REQUEST['fechaI'];
$fechaF         = $_REQUEST['fechaF'];
$fechaI         = fechaC($fechaI);
$fechaF         = fechaC($fechaF);
$bancoI         = $_REQUEST['bancoI'];
$bancoF         = $_REQUEST['bancoF'];
$responsableI   = $_REQUEST['responsableI'];
$responsableF   = $_REQUEST['responsableF'];
$exportar       = $_REQUEST['tipo'];        
#*** Division Fechas ***#
#Fecha Inicial
$fecha_divI = explode("-", $fechaI);
$annoI = trim($fecha_divI[0]);
$mesI  = trim($fecha_divI[1]);
$diaI  = trim($fecha_divI[2]);
#Fecha Final
$fecha_divF = explode("-", $fechaF);
$annoF = trim($fecha_divF[0]);
$mesI  = trim($fecha_divF[1]);
$diaF  = trim($fecha_divF[2]);
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
$ruta_logo   = $rowC[0][6];    
#*******************************************************************************#
#Consultar Tercero Inicial- Tercero Final 
$ri = $con->Listar("SELECT IF(CONCAT_WS(' ',
        t.nombreuno,
        t.nombredos,
        t.apellidouno,
        t.apellidodos) 
        IS NULL OR CONCAT_WS(' ',
        t.nombreuno,
        t.nombredos,
        t.apellidouno,
        t.apellidodos) = '',
        (t.razonsocial),
        CONCAT_WS(' ',
        t.nombreuno,
        t.nombredos,
        t.apellidouno,
        t.apellidodos)) AS NOMBRE
    FROM gf_tercero t WHERE id_unico=".$responsableI );
$nombreRI = ucwords(mb_strtolower($ri[0][0]));
$rf = $con->Listar("SELECT IF(CONCAT_WS(' ',
        t.nombreuno,
        t.nombredos,
        t.apellidouno,
        t.apellidodos) 
        IS NULL OR CONCAT_WS(' ',
        t.nombreuno,
        t.nombredos,
        t.apellidouno,
        t.apellidodos) = '',
        (t.razonsocial),
        CONCAT_WS(' ',
        t.nombreuno,
        t.nombredos,
        t.apellidouno,
        t.apellidodos)) AS NOMBRE
    FROM gf_tercero t WHERE id_unico=".$responsableF );
$nombreRF = ucwords(mb_strtolower($rf[0][0]));
#*******************************************************************************#    
#** Consulta Cuentas De Bancos
$rowB = $con->Listar("SELECT  ctb.id_unico,
        CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
        c.id_unico 
    FROM 
        gf_cuenta_bancaria ctb
    LEFT JOIN 
        gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
    LEFT JOIN 
        gf_cuenta c ON ctb.cuenta = c.id_unico 
    WHERE 
        ctb.id_unico BETWEEN '$bancoI' AND '$bancoF' AND ctb.parametrizacionanno = $anno");
#*******************************************************************************#    
switch ($exportar){
    # *** Generar Pdf **#
    case 1:
        require'../fpdf/fpdf.php';
        ob_start();
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
                global $fechaI;
                global $fechaF;
                global $nombreRI;
                global $nombreRF;
                $numpaginas=$numpaginas+1;

                $this->SetFont('Arial','B',10);

                if($ruta_logo != '')
                {
                  $this->Image('../'.$ruta_logo,10,5,28);
                }
                $this->SetFont('Arial','B',10);	
                $this->MultiCell(260,5,utf8_decode($razonsocial),0,'C');		
                $this->SetX(10);
                $this->Ln(1);
                $this->Cell(260,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(260,5,utf8_decode('Dirección: '.$direccinTer.' Tel: '.$telefonoTer),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(260,5,utf8_decode('PLANILLA DE CAJA '),0,0,'C');
                $this->Ln(5);
                $this->Cell(260,5,utf8_decode('DEL '.$_REQUEST['fechaI'].' AL '.$_REQUEST['fechaF']),0,0,'C');
                $this->Ln(5);
                $this->Cell(260,5,utf8_decode('RESPONSABLE INICIAL:'.$nombreRI.' - RESPONSABLE FINAL:'.$nombreRF),0,0,'C');
                $this->Ln(5);
            }      

            function Footer(){
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->SetX(10);
                $this->Cell(260,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
        }
        $pdf = new PDF('L','mm','Letter');   
        $nb=$pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->Ln(5);
        for ($b = 0; $b < count($rowB); $b++) {
            $banco  = $rowB[$b][0];
            #** Buscar Saldo Inicial **#
            $saldoInicial =0;
            if($fechaI==$annoI.'-01-01'){
                $fechaIN = $annoI.'-01-01';
                #*** Saldo Ingresos ****#
                $si = $con->Listar("SELECT DISTINCT p.id_unico, 
                (dpp.valor+dpp.iva+dpp.ajuste_peso+dpp.impoconsumo) , dpp.id_unico  
                FROM gp_detalle_pago dpp
                LEFT JOIN gp_pago p ON p.id_unico = dpp.pago
                WHERE p.banco = $banco  
                AND p.usuario BETWEEN '$responsableI' AND '$responsableF' 
                AND p.fecha_pago = '$fechaIN' 
                AND p.parametrizacionanno = $anno 
                GROUP BY p.id_unico ");
                $saldoI = 0;
                for ($s = 0; $s < count($si); $s++) {
                    $saldoI += $si[$s][1];
                }
                #*** Saldo Gastos ****#
                $sg =$con->Listar("SELECT
                        SUM(rg.valor)
                        GROUP_CONCAT(DISTINCT rg.retenciones) 
                    FROM
                            gf_registro_gastos rg 
                    WHERE banco = $banco 
                    AND usuario_tercero BETWEEN '$responsableI' AND '$responsableF' 
                    AND parametrizacionanno = $anno 
                    AND fecha = '$fechaIN'");
                $rtn    = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE id_unico IN (".$sg[0][1].")");                                
                $rt     = $rtn[0][0];
                $nsg = $sg[0][0]-$rt;
                $saldoInicial = $saldoI-$nsg;

            } else {
                $fechaFN = strtotime ( '-1 day' , strtotime ( $fechaI ) ) ;
                $fechaFN = date ( 'Y-m-d' , $fechaFN );
                #*** Saldo Ingresos ****#
                $si = $con->Listar("SELECT DISTINCT p.id_unico, 
                (dpp.valor+dpp.iva+dpp.ajuste_peso+dpp.impoconsumo) , dpp.id_unico 
                FROM gp_detalle_pago dpp
                LEFT JOIN gp_pago p ON p.id_unico = dpp.pago
                WHERE p.banco = $banco  AND p.parametrizacionanno = $anno 
                AND p.usuario BETWEEN '$responsableI' AND '$responsableF' 
                AND p.fecha_pago <= '$fechaFN' "); 
                $saldoI = 0;
                for ($s = 0; $s < count($si); $s++) {
                    $saldoI += $si[$s][1];
                }
                #*** Saldo Gastos ****#
                $nsg =0;
                $sg =$con->Listar("SELECT
                        SUM(rg.valor),
                        GROUP_CONCAT(DISTINCT rg.retenciones) 
                    FROM
                            gf_registro_gastos rg 
                    WHERE banco = $banco 
                    AND usuario_tercero BETWEEN '$responsableI' AND '$responsableF' 
                    AND parametrizacionanno = $anno 
                    AND fecha <= '$fechaFN' ");
                if(count($sg)>0){
                    $slg =$sg[0][0];
                } else {
                    $slg =0;
                }
                if(empty($sg[0][1])){
                    $rt =0;
                } else {
                    $rtn    = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE id_unico IN (".$sg[0][1].")"); 
                    if(count($rtn)>0){
                        $rt     = $rtn[0][0];
                    } else {
                        $rt =0;
                    }
                }
                $nsg = $slg-$rt;
                $saldoInicial = $saldoI-$nsg;
            }
            #*******************************************************"#    

            #*** Buscar Ingresos Con Banco ****#
            $rowI =$con->Listar("SELECT DISTINCT 
            DATE_FORMAT(p.fecha_pago,'%d/%m/%Y'), f.numero_factura , 
            tf.prefijo,tf.nombre, 
            IF(CONCAT_WS(' ',
                 t.nombreuno,
                 t.nombredos,
                 t.apellidouno,
                 t.apellidodos) 
                 IS NULL OR CONCAT_WS(' ',
                 t.nombreuno,
                 t.nombredos,
                 t.apellidouno,
                 t.apellidodos) = '',
                 (t.razonsocial),
                 CONCAT_WS(' ',
                 t.nombreuno,
                 t.nombredos,
                 t.apellidouno,
                 t.apellidodos)) AS NOMBRE,
            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                 t.numeroidentificacion, 
            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) AS dv, 
            p.id_unico, GROUP_CONCAT(df.id_unico), GROUP_CONCAT(dp.id_unico) 
            FROM gp_detalle_pago dp 
            LEFT JOIN gp_pago p ON p.id_unico = dp.pago
            LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
            LEFT JOIN gp_factura f ON df.factura = f.id_unico
            LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
            LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
            WHERE p.banco = $banco  
            AND p.usuario BETWEEN '$responsableI' AND '$responsableF' 
            AND p.fecha_pago BETWEEN '$fechaI' AND '$fechaF'  
            AND p.parametrizacionanno = $anno 
            GROUP BY f.id_unico, p.id_unico  
            ORDER BY p.fecha_pago ");

            #*** Buscar Gastos Con Banco ****#
            $rowG =$con->Listar("SELECT
                    DATE_FORMAT(rg.fecha, '%d/%m/%Y'), 
                    c.nombre,
                    IF(CONCAT_WS(' ',
                        t.nombreuno,
                        t.nombredos,
                        t.apellidouno,
                        t.apellidodos) 
                        IS NULL OR CONCAT_WS(' ',
                        t.nombreuno,
                        t.nombredos,
                        t.apellidouno,
                        t.apellidodos) = '',
                        (t.razonsocial),
                        CONCAT_WS(' ',
                        t.nombreuno,
                        t.nombredos,
                        t.apellidouno,
                        t.apellidodos)) AS NOMBRE,
                    IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                        t.numeroidentificacion, 
                    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) AS dv, 
                    rg.numero_documento, 
                    rg.descripcion, 
                    rg.valor, 
                    rg.retenciones 

                FROM
                        gf_registro_gastos rg 
                LEFT JOIN 
                        gf_concepto_rubro cr ON rg.concepto_rubro = cr.id_unico  
                LEFT JOIN 
                        gf_concepto c ON cr.concepto = c.id_unico 
                LEFT JOIN 
                        gf_tercero t ON rg.tercero = t.id_unico 
                WHERE banco =$banco 
                AND usuario_tercero BETWEEN '$responsableI' AND '$responsableF' 
                AND rg.parametrizacionanno = $anno 
                AND fecha BETWEEN '$fechaI' AND '$fechaF' 
                ORDER BY fecha");
            $totalI =0;
            $totalG =0;
            if(count($rowI)>0 || count($rowG)>0){
                $pdf->SetFont('Arial','I',12);
                $pdf->MultiCell(260,10,utf8_decode(ucwords(mb_strtolower($rowB[$b][1]))),1,'L');
                $pdf->MultiCell(260,10,utf8_decode('Saldo Inicial:'.number_format($saldoInicial,2,'.',',')),1,'L');
                if(count($rowI)>0){
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(260,10,utf8_decode('INGRESOS'),1,0,'C');
                    $pdf->Ln(10);
                    $pdf->Cell(30,5,utf8_decode('Fecha'),1,0,'C');
                    $pdf->Cell(40,5,utf8_decode('Tipo Factura'),1,0,'C');
                    $pdf->Cell(35,5,utf8_decode('Número'),1,0,'C');
                    $pdf->Cell(85,5,utf8_decode('Tercero'),1,0,'C');
                    $pdf->Cell(35,5,utf8_decode('Valor Factura'),1,0,'C');
                    $pdf->Cell(35,5,utf8_decode('Valor Recaudo'),1,0,'C');
                    $pdf->Ln(5);
                    $pdf->SetFont('Arial','',10);
                    for ($i= 0; $i < count($rowI); $i++) {
                        $yp = $pdf->GetY();
                        $xp = $pdf->GetX();
                        $pdf->Cell(30,5,utf8_decode($rowI[$i][0]),0,0,'L');
                        $pdf->CellFitScale(40,5,utf8_decode(mb_strtoupper($rowI[$i][2]).' - '.ucwords(mb_strtolower($rowI[$i][3]))),0,0,'L');
                        $pdf->Cell(35,5,utf8_decode($rowI[$i][1]),0,0,'L');
                        $pdf->MultiCell(85,5,utf8_decode(ucwords(mb_strtolower($rowI[$i][4])).' '.$rowI[$i][5]),0,'L');
                        $y = $pdf->GetY();
                        $pdf->SetXY($xp+190, $yp);
                        #******** Buscar Valor Factura ***********#
                        $vf = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura 
                                WHERE id_unico IN (".$rowI[$i][7].")");
                        $vr = $con->Listar("SELECT SUM(valor+iva+impoconsumo+ajuste_peso) FROM gp_detalle_pago "
                                . "WHERE id_unico IN (".$rowI[$i][8].")"); 
                        $pdf->Cell(35,5,(number_format($vf[0][0],2,'.',',')),0,0,'R');
                        $pdf->Cell(35,5,(number_format($vr[0][0],2,'.',',')),0,0,'R');
                        $pdf->Ln(5);
                        $yf = $pdf->GetY();
                        $ym = max($yf, $y);
                        $h  = $ym-$yp;
                        $pdf->SetXY($xp, $yp);
                        $pdf->Cell(30,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(40,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(85,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
                        $pdf->Ln($h);
                        $totalI+=$vr[0][0];
                        $yalt = $pdf->GetY();
                        if($yalt>180){
                            $pdf->AddPage();
                            $pdf->SetFont('Arial','B',10);
                            $pdf->Cell(260,10,utf8_decode('INGRESOS'),1,0,'C');
                            $pdf->Ln(10);
                            $pdf->Cell(30,5,utf8_decode('Fecha'),1,0,'C');
                            $pdf->Cell(40,5,utf8_decode('Tipo Factura'),1,0,'C');
                            $pdf->Cell(35,5,utf8_decode('Número'),1,0,'C');
                            $pdf->Cell(85,5,utf8_decode('Tercero'),1,0,'C');
                            $pdf->Cell(35,5,utf8_decode('Valor Factura'),1,0,'C');
                            $pdf->Cell(35,5,utf8_decode('Valor Recaudo'),1,0,'C');
                            $pdf->Ln(5);
                            $pdf->SetFont('Arial','',10);
                            $yp = $pdf->GetY();
                            $xp = $pdf->GetX();
                        }
                    }
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(225,5,utf8_decode('TOTAL INGRESOS'),1,0,'L');
                    $pdf->Cell(35,5,(number_format($totalI,2,'.',',')),1,0,'R');
                    $pdf->Ln(5);
                }
                if(count($rowG)>0){
                    if($yalt>160){
                        $pdf->AddPage();
                        $pdf->Ln(5);
                    }
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(260,10,utf8_decode('GASTOS'),1,0,'C');
                    $pdf->Ln(10);
                    $pdf->Cell(30,5,utf8_decode('Fecha'),1,0,'C');
                    $pdf->Cell(45,5,utf8_decode('Concepto'),1,0,'C');
                    $pdf->Cell(65,5,utf8_decode('Tercero'),1,0,'C');
                    $pdf->Cell(35,5,utf8_decode('N° Documento'),1,0,'C');
                    $pdf->Cell(50,5,utf8_decode('Descripción'),1,0,'C');
                    $pdf->Cell(35,5,utf8_decode('Valor Neto'),1,0,'C');
                    $pdf->Ln(5);
                    $pdf->SetFont('Arial','',10);
                    for ($i= 0; $i < count($rowG); $i++) {
                        $pdf->SetFont('Arial','',10);
                        $yp = $pdf->GetY();
                        $xp = $pdf->GetX();
                        $pdf->Cell(30,5,utf8_decode($rowG[$i][0]),0,0,'L');
                        $pdf->MultiCell(45,5,utf8_decode(ucwords(mb_strtolower($rowG[$i][1]))),0,'L');
                        $y3 = $pdf->GetY();
                        $pdf->SetXY($xp+75, $yp);
                        $pdf->MultiCell(65,5,utf8_decode(ucwords(mb_strtolower($rowG[$i][2])).' '.$rowG[$i][3]),0,'L');
                        $y = $pdf->GetY();
                        $pdf->SetXY($xp+140, $yp);
                        $pdf->Cell(35,5,utf8_decode($rowG[$i][4]),0,0,'L');
                        $pdf->MultiCell(50,5,utf8_decode($rowG[$i][5]),0,'L');
                        $y1 = $pdf->GetY();
                        $pdf->SetXY($xp+225, $yp);
                        $rt=0;
                        if(!empty($rowG[$i][7])){ 
                            $rtn    = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE id_unico IN (".$rowG[$i][7].")");
                            $rt     = $rtn[0][0];
                        }
                        $neto = $rowG[$i][6] - $rt;
                        $pdf->Cell(35,5,(number_format($neto,2,'.',',')),0,0,'R');
                        $pdf->Ln(5);
                        $yf = $pdf->GetY();
                        $ym = max($yf, $y, $y1,$y3);
                        $h  = $ym-$yp;
                        $pdf->SetXY($xp, $yp);
                        $pdf->Cell(30,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(45,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(65,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(50,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
                        $pdf->Ln($h);
                        $yalt = $pdf->GetY();
                        if($yalt>180){
                            $pdf->AddPage();
                            $pdf->Ln(5);
                            $pdf->SetFont('Arial','B',10);
                            $pdf->Cell(260,10,utf8_decode('GASTOS'),1,0,'C');
                            $pdf->Ln(10);
                            $pdf->Cell(30,5,utf8_decode('Fecha'),1,0,'C');
                            $pdf->Cell(45,5,utf8_decode('Concepto'),1,0,'C');
                            $pdf->Cell(65,5,utf8_decode('Tercero'),1,0,'C');
                            $pdf->Cell(35,5,utf8_decode('N° Documento'),1,0,'C');
                            $pdf->Cell(50,5,utf8_decode('Descripción'),1,0,'C');
                            $pdf->Cell(35,5,utf8_decode('Valor Neto'),1,0,'C');
                            $pdf->Ln(5);
                        }
                        $totalG +=$neto;
                    }
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(225,5,utf8_decode('TOTAL GASTOS'),1,0,'L');
                    $pdf->Cell(35,5,(number_format($totalG,2,'.',',')),1,0,'R');
                    $pdf->Ln(5);
                }
                $saldoFinal = $saldoInicial+($totalI-$totalG);
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(225,10,utf8_decode('SALDO FINAL '.mb_strtoupper($rowB[$b][1])),1,0,'L');
                $pdf->Cell(35,10,(number_format($saldoFinal,2,'.',',')),1,0,'R');
                $pdf->Ln(10);
                $pdf->Cell(260,0.5,(''),0,0,'R');
                $pdf->Ln(1);

            }
        }
        #************** FACTURAS SIN RECAUDAR *******************#
        $rowfsr = $con->Listar("SELECT DISTINCT f.id_unico,
            f.fecha_factura, DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), 
            f.numero_factura,  tf.nombre,tf.prefijo, 
            (SELECT SUM(dff.valor_total_ajustado) FROM gp_detalle_factura dff WHERE dff.factura IN(f.id_unico)) as valor_factura, f.id_unico, 
            IF(CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos) 
                IS NULL OR CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos) = '',
                (t.razonsocial),
                CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos)) AS NOMBRE,
            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                t.numeroidentificacion, 
            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) AS dv 
            FROM gp_factura f 
            LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
            LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura 
            LEFT JOIN gf_tercero t ON t.id_unico = f.tercero 
            WHERE f.fecha_factura  BETWEEN '$fechaI' AND '$fechaF'  
            AND f.parametrizacionanno = $anno 
            GROUP BY f.id_unico
            ORDER BY tf.id_unico, f.numero_factura");
        if(count($rowfsr)>0){
            $pdf->AddPage();
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(260,10,utf8_decode('FACTURAS SIN RECAUDAR'),1,0,'C');
            $pdf->Ln(10);
            $pdf->Cell(30,5,utf8_decode('Fecha'),1,0,'C');
            $pdf->Cell(45,5,utf8_decode('Tipo Factura'),1,0,'C');
            $pdf->Cell(35,5,utf8_decode('Número'),1,0,'C');
            $pdf->Cell(80,5,utf8_decode('Tercero'),1,0,'C');
            $pdf->Cell(30,5,utf8_decode('Valor Factura'),1,0,'C');
            $pdf->Cell(40,5,utf8_decode('Saldo Factura'),1,0,'C');
            $pdf->Ln(5);
            $pdf->SetFont('Arial','',10);
            $totalfs =0;
            for ($i = 0; $i < count($rowfsr); $i++) {
                $saldofactura = saldoFacturaFecha($rowfsr[$i][0],$fechaF );
                if(round($saldofactura)!='-0' || round($saldofactura)>0){
                    $yp = $pdf->GetY();
                    $xp = $pdf->GetX();
                    $pdf->Cell(30,5,utf8_decode($rowfsr[$i][2]),0,0,'L');
                    $pdf->CellFitScale(45,5,utf8_decode(mb_strtoupper($rowfsr[$i][5]).' - '.ucwords(mb_strtolower($rowfsr[$i][4]))),0,0,'L');
                    $pdf->Cell(35,5,utf8_decode($rowfsr[$i][3]),0,0,'L');
                    $pdf->MultiCell(80,5,utf8_decode(ucwords(mb_strtolower($rowfsr[$i][8])).' '.$rowfsr[$i][9]),0,'L');
                    $y = $pdf->GetY();
                    $pdf->SetXY($xp+190, $yp);
                    $pdf->Cell(30,5,(number_format($rowfsr[$i][6],2,'.',',')),0,0,'R');
                    $pdf->Cell(40,5,(number_format($saldofactura,2,'.',',')),0,0,'R');
                    $pdf->Ln(5);
                    $yf = $pdf->GetY();
                    $ym = max($yf, $y);
                    $h  = $ym-$yp;
                    $pdf->SetXY($xp, $yp);
                    $pdf->Cell(30,$h,utf8_decode(''),1,0,'L');
                    $pdf->Cell(45,$h,utf8_decode(''),1,0,'L');
                    $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
                    $pdf->Cell(80,$h,utf8_decode(''),1,0,'L');
                    $pdf->Cell(30,$h,utf8_decode(''),1,0,'L');
                    $pdf->Cell(40,$h,utf8_decode(''),1,0,'L');
                    $pdf->Ln($h);
                    $totalfs+=$saldofactura;
                    $yalt = $pdf->GetY();
                    if($yalt>180){
                        $pdf->AddPage();
                        $pdf->SetFont('Arial','B',10);
                        $pdf->Cell(260,10,utf8_decode('FACTURAS SIN RECAUDAR'),1,0,'C');
                        $pdf->Ln(10);
                        $pdf->Cell(30,5,utf8_decode('Fecha'),1,0,'C');
                        $pdf->Cell(45,5,utf8_decode('Tipo Factura'),1,0,'C');
                        $pdf->Cell(35,5,utf8_decode('Número'),1,0,'C');
                        $pdf->Cell(80,5,utf8_decode('Tercero'),1,0,'C');
                        $pdf->Cell(30,5,utf8_decode('Valor Factura'),1,0,'C');
                        $pdf->Cell(40,5,utf8_decode('Saldo Factura'),1,0,'C');
                        $pdf->Ln(5);
                        $pdf->SetFont('Arial','',10);
                        $yp = $pdf->GetY();
                        $xp = $pdf->GetX();
                    }
                }
            }
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(220,10,utf8_decode('TOTAL FACTURAS SIN RECAUDAR'),1,0,'L');
            $pdf->Cell(40,10,(number_format($totalfs,2,'.',',')),1,0,'R');
            $pdf->Ln(10);
            $pdf->Cell(260,0.5,(''),0,0,'R');
            $pdf->Ln(1);
        }
        ob_end_clean();		
        $pdf->Output(0,'Informe_Planilla_Caja.pdf',0);
    break;
    # *** Generar xls **#
    case 2:
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Informe_Planilla_Caja.xls"); ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Boletín Diario De Caja</title>
            </head>
            <body>
                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                    <th colspan="6" align="center"><strong>
                        <br/><?php echo $razonsocial ?>
                        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                        <br/>&nbsp;
                        <br/>PLANILLA DE CAJA
                        <br/>DEL <?php echo $_REQUEST['fechaI'].' AL '.$_REQUEST['fechaF']; ?>
                        <br/>RESPONSABLE INICIAL <?php echo $nombreRI.' - RESPONSABLE FINAL '.$nombreRF; ?>
                        <br/>&nbsp;</strong>
                    </th>
                    <tbody>
                        <?php 
                        for ($b = 0; $b < count($rowB); $b++) {
                            $banco  = $rowB[$b][0];
                            #** Buscar Saldo Inicial **#
                            $saldoInicial =0;
                            if($fechaI==$annoI.'-01-01'){
                                $fechaIN = $annoI.'-01-01';
                                #*** Saldo Ingresos ****#
                                $si = $con->Listar("SELECT DISTINCT p.id_unico, 
                                (dpp.valor+dpp.iva+dpp.ajuste_peso+dpp.impoconsumo) , dpp.id_unico  
                                FROM gp_detalle_pago dpp
                                LEFT JOIN gp_pago p ON p.id_unico = dpp.pago
                                WHERE p.banco = $banco  
                                AND p.usuario BETWEEN '$responsableI' AND '$responsableF' 
                                AND p.fecha_pago = '$fechaIN' 
                                AND p.parametrizacionanno = $anno 
                                GROUP BY p.id_unico ");
                                $saldoI = 0;
                                for ($s = 0; $s < count($si); $s++) {
                                    $saldoI += $si[$s][1];
                                }
                                #*** Saldo Gastos ****#
                                $sg =$con->Listar("SELECT
                                        SUM(rg.valor)
                                        GROUP_CONCAT(DISTINCT rg.retenciones) 
                                    FROM
                                            gf_registro_gastos rg 
                                    WHERE banco = $banco 
                                    AND usuario_tercero BETWEEN '$responsableI' AND '$responsableF' 
                                    AND parametrizacionanno = $anno 
                                    AND fecha = '$fechaIN'");
                                $rtn    = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE id_unico IN (".$sg[0][1].")");                                
                                $rt     = $rtn[0][0];
                                $nsg = $sg[0][0]-$rt;
                                $saldoInicial = $saldoI-$nsg;

                            } else {
                                $fechaFN = strtotime ( '-1 day' , strtotime ( $fechaI ) ) ;
                                $fechaFN = date ( 'Y-m-d' , $fechaFN );
                                #*** Saldo Ingresos ****#
                                $si = $con->Listar("SELECT DISTINCT p.id_unico, 
                                (dpp.valor+dpp.iva+dpp.ajuste_peso+dpp.impoconsumo) , dpp.id_unico 
                                FROM gp_detalle_pago dpp
                                LEFT JOIN gp_pago p ON p.id_unico = dpp.pago
                                WHERE p.banco = $banco  AND p.parametrizacionanno = $anno 
                                AND p.usuario BETWEEN '$responsableI' AND '$responsableF' 
                                AND p.fecha_pago <= '$fechaFN' "); 
                                $saldoI = 0;
                                for ($s = 0; $s < count($si); $s++) {
                                    $saldoI += $si[$s][1];
                                }
                                #*** Saldo Gastos ****#
                                $nsg =0;
                                $sg =$con->Listar("SELECT
                                        SUM(rg.valor),
                                        GROUP_CONCAT(DISTINCT rg.retenciones) 
                                    FROM
                                            gf_registro_gastos rg 
                                    WHERE banco = $banco 
                                    AND usuario_tercero BETWEEN '$responsableI' AND '$responsableF' 
                                    AND parametrizacionanno = $anno 
                                    AND fecha <= '$fechaFN' ");
                                if(count($sg)>0){
                                    $slg =$sg[0][0];
                                } else {
                                    $slg =0;
                                }
                                if(empty($sg[0][1])){
                                    $rt =0;
                                } else {
                                    $rtn    = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE id_unico IN (".$sg[0][1].")"); 
                                    if(count($rtn)>0){
                                        $rt     = $rtn[0][0];
                                    } else {
                                        $rt =0;
                                    }
                                }
                                $nsg = $slg-$rt;
                                $saldoInicial = $saldoI-$nsg;
                            }
                            #*******************************************************"#    

                            #*** Buscar Ingresos Con Banco ****#
                            $rowI =$con->Listar("SELECT DISTINCT 
                            DATE_FORMAT(p.fecha_pago,'%d/%m/%Y'), f.numero_factura , 
                            tf.prefijo,tf.nombre, 
                            IF(CONCAT_WS(' ',
                                 t.nombreuno,
                                 t.nombredos,
                                 t.apellidouno,
                                 t.apellidodos) 
                                 IS NULL OR CONCAT_WS(' ',
                                 t.nombreuno,
                                 t.nombredos,
                                 t.apellidouno,
                                 t.apellidodos) = '',
                                 (t.razonsocial),
                                 CONCAT_WS(' ',
                                 t.nombreuno,
                                 t.nombredos,
                                 t.apellidouno,
                                 t.apellidodos)) AS NOMBRE,
                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                 t.numeroidentificacion, 
                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) AS dv, 
                            p.id_unico, GROUP_CONCAT(df.id_unico), GROUP_CONCAT(dp.id_unico) 
                            FROM gp_detalle_pago dp 
                            LEFT JOIN gp_pago p ON p.id_unico = dp.pago
                            LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                            LEFT JOIN gp_factura f ON df.factura = f.id_unico
                            LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
                            LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
                            WHERE p.banco = $banco  
                            AND p.usuario BETWEEN '$responsableI' AND '$responsableF' 
                            AND p.fecha_pago BETWEEN '$fechaI' AND '$fechaF'  
                            AND p.parametrizacionanno = $anno 
                            GROUP BY f.id_unico, p.id_unico  
                            ORDER BY p.fecha_pago ");
                            
                            #*** Buscar Gastos Con Banco ****#
                            $rowG =$con->Listar("SELECT
                                    DATE_FORMAT(rg.fecha, '%d/%m/%Y'), 
                                    c.nombre,
                                    IF(CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos) 
                                        IS NULL OR CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos) = '',
                                        (t.razonsocial),
                                        CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos)) AS NOMBRE,
                                    IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                        t.numeroidentificacion, 
                                    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) AS dv, 
                                    rg.numero_documento, 
                                    rg.descripcion, 
                                    rg.valor, 
                                    rg.retenciones 

                                FROM
                                        gf_registro_gastos rg 
                                LEFT JOIN 
                                        gf_concepto_rubro cr ON rg.concepto_rubro = cr.id_unico  
                                LEFT JOIN 
                                        gf_concepto c ON cr.concepto = c.id_unico 
                                LEFT JOIN 
                                        gf_tercero t ON rg.tercero = t.id_unico 
                                WHERE banco =$banco 
                                AND usuario_tercero BETWEEN '$responsableI' AND '$responsableF' 
                                AND rg.parametrizacionanno = $anno 
                                AND fecha BETWEEN '$fechaI' AND '$fechaF' 
                                ORDER BY fecha");
                            $totalI =0;
                            $totalG =0;
                            if(count($rowI)>0 || count($rowG)>0){
                                echo '<tr><td colspan="6"><br/><i><strong>'. ucwords(mb_strtolower($rowB[$b][1])).'<br/>Saldo Inicial:'.number_format($saldoInicial,2,'.',',').'</strong></i><br/>&nbsp;</td></tr>';
                                if(count($rowI)>0){
                                    echo '<tr><td colspan="6"><strong><center>INGRESOS</center></strong></td></tr>';
                                    echo '<tr>';
                                    echo '<td><strong><center>Fecha</center></strong></td>';
                                    echo '<td><strong><center>Tipo Factura</center></strong></td>';
                                    echo '<td><strong><center>Número</center></strong></td>';
                                    echo '<td><strong><center>Tercero</center></strong></td>';
                                    echo '<td><strong><center>Valor Factura</center></strong></td>';
                                    echo '<td><strong><center>Valor Recaudo</center></strong></td>';
                                    echo '</tr>';
                                    for ($i= 0; $i < count($rowI); $i++) {
                                        echo '<tr>';
                                        echo '<td>'.$rowI[$i][0].'</td>';
                                        echo '<td>'.mb_strtoupper($rowI[$i][2]).' - '.ucwords(mb_strtolower($rowI[$i][3])).'</td>';
                                        echo '<td>'.$rowI[$i][1].'</td>';
                                        echo '<td>'.ucwords(mb_strtolower($rowI[$i][4])).' '.$rowI[$i][5].'</td>';
                                        #******** Buscar Valor Factura ***********#
                                        $vf = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura 
                                                WHERE id_unico IN (".$rowI[$i][7].")");
                                        echo '<td>'.number_format($vf[0][0],2,'.',',').'</td>';
                                        $vr = $con->Listar("SELECT SUM(valor+iva+impoconsumo+ajuste_peso) FROM gp_detalle_pago "
                                                . "WHERE id_unico IN (".$rowI[$i][8].")"); 
                                        echo '<td>'.number_format($vr[0][0],2,'.',',').'</td>';
                                        echo '</tr>';
                                        $totalI+=$vr[0][0];
                                    }
                                    echo '<tr><td colspan="5"><strong>TOTAL INGRESOS</strong></td>';
                                    echo '<td colspan="1"><strong>'.number_format($totalI,2,'.',',').'</strong></td></tr>';
                                }
                                if(count($rowG)>0){
                                    echo '<tr><td colspan="6"><strong><center>GASTOS</center></strong></td></tr>';
                                    echo '<tr>';
                                    echo '<td><strong><center>Fecha</center></strong></td>';
                                    echo '<td><strong><center>Concepto</center></strong></td>';
                                    echo '<td><strong><center>Tercero</center></strong></td>';
                                    echo '<td><strong><center>N° Documento</center></strong></td>';
                                    echo '<td><strong><center>Descripción</center></strong></td>';
                                    echo '<td><strong><center>Valor Neto</center></strong></td>';
                                    echo '</tr>';
                                    for ($i= 0; $i < count($rowG); $i++) {
                                        echo '<tr>';
                                        echo '<td>'.$rowG[$i][0].'</td>';
                                        echo '<td>'.ucwords(mb_strtolower($rowG[$i][1])).'</td>';
                                        echo '<td>'.ucwords(mb_strtolower($rowG[$i][2])).' '.$rowG[$i][3].'</td>';
                                        echo '<td>'.$rowG[$i][4].'</td>';
                                        echo '<td>'.$rowG[$i][5].'</td>';
                                        $rt=0;
                                        if(!empty($rowG[$i][7])){ 
                                            $rtn    = $con->Listar("SELECT SUM(valorretencion) FROM gf_retencion WHERE id_unico IN (".$rowG[$i][7].")");
                                            $rt     = $rtn[0][0];
                                        }
                                        $neto = $rowG[$i][6] - $rt;
                                        echo '<td>'.number_format($neto,2,'.',',').'</td>';
                                        echo '</tr>';  
                                        $totalG +=$neto;
                                    }
                                    echo '<tr><td colspan="5"><strong>TOTAL GASTOS</strong></td>';
                                    echo '<td colspan="1"><strong>'.number_format($totalG,2,'.',',').'</strong></td></tr>';
                                    
                                }
                                $saldoFinal = $saldoInicial+($totalI-$totalG);
                                echo '<tr><td colspan="4"><strong>SALDO FINAL '.(mb_strtoupper($rowB[$b][1])).'</strong></td>';
                                echo '<td colspan="2"><strong>'.number_format($saldoFinal,2,'.',',').'</strong></td></tr>';
                                
                            }
                        }
                        #************** FACTURAS SIN RECAUDAR *******************#
                        $rowfsr = $con->Listar("SELECT f.id_unico,
                            f.fecha_factura, DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), 
                            f.numero_factura,  tf.nombre,tf.prefijo, 
                            (SELECT SUM(dff.valor_total_ajustado) FROM gp_detalle_factura dff WHERE dff.factura IN(f.id_unico)) as valor_factura, p.numero_pago, 
                            IF(CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos) = '',
                                (t.razonsocial),
                                CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos)) AS NOMBRE,
                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                t.numeroidentificacion, 
                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) AS dv 
                            FROM gp_factura f 
                            LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
                            LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura 
                            LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura 
                            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                            LEFT JOIN gf_tercero t ON t.id_unico = f.tercero 
                            WHERE f.fecha_factura  BETWEEN '$fechaI' AND '$fechaF'  
                            AND f.parametrizacionanno = $anno 
                            GROUP BY f.id_unico, p.id_unico 
                            ORDER BY tf.id_unico, f.numero_factura");
                        if(count($rowfsr)>0){
                            $totalfs =0; 
                            echo '<tr><td colspan="6"><br/>&nbsp;<br/>&nbsp;</td></tr>';
                            echo '<tr><td colspan="6"><strong><center>FACTURAS SIN RECAUDAR</center></strong></td></tr>';
                            echo '<tr>';
                            echo '<td><strong><center>Fecha</center></strong></td>';
                            echo '<td><strong><center>Tipo Factura</center></strong></td>';
                            echo '<td><strong><center>Número</center></strong></td>';
                            echo '<td><strong><center>Tercero</center></strong></td>';
                            echo '<td><strong><center>Valor Factura</center></strong></td>';
                            echo '<td><strong><center>Saldo Factura</center></strong></td>';
                            echo '</tr>';
                            for ($i = 0; $i < count($rowfsr); $i++) {
                                $saldofactura = saldoFacturaFecha($rowfsr[$i][0],$fechaF);
                                if(round($saldofactura)!='-0' || round($saldofactura)>0){
                                    echo '<tr>';
                                    echo '<td>'.$rowfsr[$i][2].'</td>';
                                    echo '<td>'.$rowfsr[$i][5].' - '.ucwords(mb_strtolower($rowfsr[$i][4])).'</td>';
                                    echo '<td>'.$rowfsr[$i][3].'</td>';
                                    echo '<td>'.ucwords(mb_strtolower($rowfsr[$i][8])).' - '.$rowfsr[$i][9].'</td>';
                                    echo '<td>'.number_format($rowfsr[$i][6],2,'.',',').'</td>';
                                    echo '<td>'.number_format($saldofactura,2,'.',',').'</td>';
                                    echo '</tr>';
                                    $totalfs+=$saldofactura;
                                }
                            }
                            echo '<tr><td colspan="5"><strong>TOTAL FACTURAS SIN RECAUDO </strong></td>';
                            echo '<td colspan="1"><strong>'.number_format($totalfs,2,'.',',').'</strong></td></tr>';
                            
                        }
                        
                        ?>
                    </tbody>
                </table>
            </body>
        </html>
        <?php
    break;
}

