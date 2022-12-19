<?php
#################################################################################################################
#                                           MODIFICACIONES
################################################################################################################
#06/06/2018 |Erica G. |Archivo Creado
################################################################################################################
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
$con = new ConexionPDO();
session_start();
ini_set('max_execution_time', 0);
$para = $_SESSION['anno'];

#************Datos Compañia************#
$compania = $_SESSION['compania'];
$rowC     = $con->Listar("SELECT ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
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
$rutalogoTer = $rowC[0][6];


#***********Consulta Detalles pago************#

$pg = $con->Listar("SELECT pg.id_unico, 
    pg.numero_pago, 
    tp.nombre, 
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
    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
    pg.fecha_pago, 
    DATE_FORMAT(pg.fecha_pago, '%d/%m/%Y')
    FROM gp_pago pg 
    LEFT JOIN gp_tipo_pago tp ON pg.tipo_pago = tp.id_unico 
    LEFT JOIN gf_tercero t ON pg.responsable = t.id_unico 
    WHERE pg.id_unico = ".$_GET['id']);

$numero = $pg[0][1];
$tipo_p = $pg[0][2];
$tercero= $pg[0][3].' - '.$pg[0][4];
$fecha  = $pg[0][6];
#**********PDF*********#
    header("Content-Type: text/html;charset=utf-8");
    require'../fpdf/fpdf.php';
    ob_start();
    
    class PDF extends FPDF {
        function Header() {

            global $rutalogoTer;
            global $razonsocial;
            global $numeroIdent;
            global $anno;
            global $numero;
            global $tipo_p;
            global $tercero;
            global $fecha;
            $numpaginas = $this->PageNo();
            if ($rutalogoTer != '') {
                $this->Image('../' . $rutalogoTer, 12, 6, 25);
            }

            $this->SetFont('Courier', 'B', 12);
            $this->SetY(10);
            $this->SetX(20);
            $this->Cell(180, 5, utf8_decode($razonsocial), 0, 0, 'C');
            $this->Ln(5);
            $this->SetFont('Courier', '', 12);
            $this->SetX(20);
            $this->Cell(180, 5, $numeroIdent, 0, 0, 'C');
            $this->SetFont('Courier', 'B', 10);
            $this->Ln(8);
            $this->SetX(20);
            $this->Cell(180, 5, utf8_decode(mb_strtoupper($tipo_p)), 0, 0, 'C');
            $this->Ln(4);
            $this->SetX(20);
            $this->Cell(180, 5, utf8_decode('N°: '.$numero), 0, 0, 'C');
            $this->Ln(10);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Cell(50,5,'FECHA:',0,'L');
            $this->SetFont('Courier', '', 10);
            $this->Cell(145, 5, utf8_decode($fecha),0, 0, 'L');
            $this->Ln(5);
            $this->SetFont('Courier', 'B', 10);
            $this->Cell(50,5,utf8_decode('RECIBÍ DE :'),0,'L');
            $this->SetFont('Courier', '', 10);
            $this->MultiCell(145, 5, utf8_decode(ucwords(mb_strtolower($tercero))), 0, 'L');
            $y2  = $this->GetY();
            $alt = $y-$y2;
            $this->SetXY($x, $y+10);
            $this->Cell(195,$alt,'',1,'L');
            $this->Ln(5);
            
            
        }

        function Footer() {
        $this->SetY(-15);
        $this->SetFont('Courier', 'B', 8);
        $this->SetX(10);
        $this->Cell(100, 10, utf8_decode('Fecha: ' . date('d/m/Y')), 0, 0, 'L');
        $this->Cell(95, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }

    }

    $pdf = new PDF('P', 'mm', 'Letter');
    $pdf->AddPage();
    $pdf->AliasNbPages();
    
    #****************** RECAUDO ***************************#
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(195,8,'RECAUDO',1,0,'C');
    $pdf->Ln(8);
    $pdf->Cell(50,10,'TIPO',1,0,'C');
    $pdf->Cell(40,10, utf8_decode('NÚMERO'),1,0,'C');
    $pdf->Cell(30,10,'VALOR',1,0,'C');
    $pdf->Cell(25,10,'IVA',1,0,'C');
    $pdf->Cell(25,10,'IMPUESTO DE',1,0,'C');
    $pdf->Cell(25,10,'AJUSTE AL',1,0,'C');
    $pdf->SetX(10);
    $pdf->Cell(50,5,'',0,0,'C');
    $pdf->Cell(40,5,'',0,0,'C');
    $pdf->Cell(30,5,'',0,0,'C');
    $pdf->Cell(25,5,'',0,0,'C');
    $pdf->Cell(25,5,'',0,0,'C');
    $pdf->Cell(25,5,'',0,0,'C');
    $pdf->Ln(5);
    $pdf->Cell(50,5,'FACTURA',0,0,'C');
    $pdf->Cell(40,5,'FACTURA',0,0,'C');
    $pdf->Cell(30,5,'',0,0,'C');
    $pdf->Cell(25,5,'',0,0,'C');
    $pdf->Cell(25,5,'CONSUMO',0,0,'C');
    $pdf->Cell(25,5,'PESO',0,0,'C');
    $pdf->Ln(5);
    
    $rowP   = $con->Listar("SELECT  dtp.id_unico,
            dtp.detalle_factura,
            fat.numero_factura,
            tfat.nombre,
            dtp.valor,
            dtp.pago,
            fat.id_unico,
            dtp.iva,
            dtp.impoconsumo,
            dtp.ajuste_peso,
            dtp.saldo_credito
    FROM gp_detalle_pago dtp
    LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
    LEFT JOIN gp_factura fat ON dtf.factura = fat.id_unico
    LEFT JOIN gp_tipo_factura tfat ON fat.tipofactura = tfat.id_unico
    LEFT JOIN gp_pago pg ON dtp.pago = pg.id_unico
    WHERE pg.id_unico =".$_GET['id']);
    
    $totalp =0;
    for ($i = 0; $i < count($rowP); $i++) {
        if(($pdf->GetY())>250){
            $pdf->AddPage();
        }
        $pdf->SetFont('Courier','',10);
        $pdf->CellFitScale(50,5, utf8_decode(ucwords($rowP[$i][3])),1,0,'L');
        $pdf->CellFitScale(40,5, utf8_decode($rowP[$i][2]),1,0,'L');
        $pdf->CellFitScale(30,5, number_format($rowP[$i][4],2,'.',','),1,0,'R');
        $pdf->CellFitScale(25,5, number_format($rowP[$i][7],2,'.',','),1,0,'R');
        $pdf->CellFitScale(25,5, number_format($rowP[$i][8],2,'.',','),1,0,'R');
        $pdf->CellFitScale(25,5, number_format($rowP[$i][9],2,'.',','),1,0,'R');
        $pdf->Ln(5);
        $totalp +=$rowP[$i][4]+$rowP[$i][7]+$rowP[$i][8]+$rowP[$i][9];
    }
    $pdf->SetFont('Courier','B',10);
    $pdf->CellFitScale(90,5,utf8_decode('TOTAL'),1,0,'C');
    $pdf->CellFitScale(105,5,number_format($totalp,2,'.',','),1,0,'R');
    $pdf->Ln(10);
    if(($pdf->GetY())>250){
        $pdf->AddPage();
    }
    #****************** CNT ***************************#
    if(!empty($_REQUEST['c'])){
        $rowP   = $con->Listar("SELECT  c.codi_cuenta, 
            c.nombre, 
            c.naturaleza, 
            dc.valor 
        FROM gf_detalle_comprobante dc 
        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico
        WHERE dc.comprobante =".$_GET['c']);
        if(count($rowP)>0){
            $pdf->SetFont('Courier','B',10);
            $pdf->Cell(195,8,utf8_decode('AFECTACIÓN CONTABLE'),1,0,'C');
            $pdf->Ln(8);
            $pdf->Cell(40,10,utf8_decode('CÓDIGO'),1,0,'C');
            $pdf->Cell(105,10, utf8_decode('NOMBRE'),1,0,'C');
            $pdf->Cell(25,10,'VALOR',1,0,'C');
            $pdf->Cell(25,10,'VALOR',1,0,'C');
            $pdf->SetX(10);
            $pdf->Cell(40,5,'',0,0,'C');
            $pdf->Cell(105,5,'',0,0,'C');
            $pdf->Cell(25,5,'',0,0,'C');
            $pdf->Cell(25,5,'',0,0,'C');
            $pdf->Ln(5);
            $pdf->Cell(40,5,'CUENTA',0,0,'C');
            $pdf->Cell(105,5,'CUENTA',0,0,'C');
            $pdf->Cell(25,5,utf8_decode('DÉBITO'),0,0,'C');
            $pdf->Cell(25,5,utf8_decode('CRÉDITO'),0,0,'C');
            $pdf->Ln(5);
            $totald =0;
            $totalc =0;
            for ($i = 0; $i < count($rowP); $i++) {
                if(($pdf->GetY())>250){
                    $pdf->AddPage();
                }
                $pdf->SetFont('Courier','',10);
                $pdf->CellFitScale(40,5,  utf8_decode($rowP[$i][0]),1,0,'L');
                $pdf->CellFitScale(105,5, utf8_decode(ucwords(mb_strtolower($rowP[$i][1]))),1,0,'L');
                $debito     = 0;
                $credito    = 0;
                switch ($rowP[$i][2]){
                    case 1:
                        if($rowP[$i][3]>0){
                            $debito     = $rowP[$i][3];
                        } else {
                            $credito    = $rowP[$i][3]*-1;
                        }
                    break;
                    case 2:
                        if($rowP[$i][3]>0){
                            $credito    = $rowP[$i][3];
                        } else {
                            $debito     = $rowP[$i][3]*-1;
                        }
                    break;
                }

                $pdf->CellFitScale(25,5, number_format($debito,2,'.',','),1,0,'R');
                $pdf->CellFitScale(25,5, number_format($credito,2,'.',','),1,0,'R');
                $pdf->Ln(5);
                $totald += $debito;
                $totalc += $credito;

            }
            $pdf->SetFont('Courier','B',10);
            $pdf->CellFitScale(145,5,utf8_decode('TOTAL'),1,0,'C');
            $pdf->CellFitScale(25,5,number_format($totald,2,'.',','),1,0,'R');
            $pdf->CellFitScale(25,5,number_format($totalc,2,'.',','),1,0,'R');
            $pdf->Ln(10);
            if(($pdf->GetY())>250){
                $pdf->AddPage();
            }
        }
    }
    #****************** CNT ***************************#
    if(!empty($_REQUEST['p'])){
        $rowP   = $con->Listar("SELECT  c.nombre, 
            rb.codi_presupuesto, 
            rb.nombre, 
            dc.valor 
        FROM gf_detalle_comprobante_pptal dc 
        LEFT JOIN gf_concepto_rubro cr ON dc.conceptorubro = cr.id_unico 
        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
        LEFT JOIN gf_rubro_pptal rb ON cr.rubro = rb.id_unico 
        WHERE dc.comprobantepptal =".$_GET['p']);
        if(count($rowP)>0){
            $pdf->SetFont('Courier','B',10);
            $pdf->Cell(195,8,utf8_decode('AFECTACIÓN PRESUPUESTAL'),1,0,'C');
            $pdf->Ln(8);
            $pdf->Cell(85,10,utf8_decode('CONCEPTO'),1,0,'C');
            $pdf->Cell(85,10, utf8_decode('RUBRO'),1,0,'C');
            $pdf->Cell(25,10,'VALOR',1,0,'C');
            $pdf->Ln(10);
            $total =0;
            for ($i = 0; $i < count($rowP); $i++) {
                if(($pdf->GetY())>250){
                    $pdf->AddPage();
                }
                $pdf->SetFont('Courier','',10);
                $pdf->CellFitScale(85,5, utf8_decode(ucwords(mb_strtolower($rowP[$i][0]))),1,0,'L');
                $pdf->CellFitScale(85,5, utf8_decode($rowP[$i][1].' - '.ucwords(mb_strtolower($rowP[$i][2]))),1,0,'L');
                $pdf->CellFitScale(25,5, number_format($rowP[$i][3],2,'.',','),1,0,'R');
                $pdf->Ln(5);
                $total += $rowP[$i][3];

            }
            $pdf->SetFont('Courier','B',10);
            $pdf->CellFitScale(170,5,utf8_decode('TOTAL'),1,0,'C');
            $pdf->CellFitScale(25,5,number_format($totalc,2,'.',','),1,0,'R');
            $pdf->Ln(10);
            if(($pdf->GetY())>250){
                $pdf->AddPage();
            }
        }
    }
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0, 'Informe_Recaudo(' . date('d/m/Y') . ').pdf', 0);