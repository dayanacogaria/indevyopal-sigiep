<?php
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require'../fpdf/fpdf.php';
ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];

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

class PDF extends FPDF {
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
        $this->MultiCell(190,5,utf8_decode($razonsocial),0,'C');		
        $this->SetX(10);
        $this->Ln(1);
        $this->Cell(190,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
        $this->ln(5);
        $this->SetX(10);
        $this->Cell(190,5,utf8_decode('Dirección: '.$direccinTer.' Tel: '.$telefonoTer),0,0,'C');
        $this->ln(5);
    }      

    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->SetX(10);
        $this->Cell(190,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}
$pdf = new PDF('P','mm','Letter');   
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->Ln(10);

$rowf = $con->Listar("SELECT DISTINCT  
    fa.id_unico, DATE_FORMAT(fa.fecha_ven, '%d/%m/%Y'), 
    fa.numero, fa.observaciones, a.consecutivo,DATE_FORMAT(a.fecha, '%d/%m/%Y'), 
    ta.nombre, a.nrocuotas, a.porcentaje_apl, a.valor, ta.id_unico , 
    DATE_FORMAT(pp.fechapago, '%d/%m/%Y'), cb.numerocuenta
FROM gr_pago_predial pp 
LEFT JOIN gr_detalle_pago_predial dpp ON pp.id_unico = dpp.pago 
LEFT JOIN ga_detalle_factura df ON dpp.id_unico = df.iddetallerecaudo 
LEFT JOIN ga_factura_acuerdo fa ON fa.id_unico = df.factura 
LEFT JOIN ga_detalle_acuerdo da ON da.id_unico = df.detalleacuerdo 
LEFT JOIN ga_acuerdo a ON da.acuerdo = a.id_unico 
LEFT JOIN ga_tipo_acuerdo ta ON a.tipo = ta.id_unico 
LEFT JOIN gf_cuenta_bancaria cb ON pp.banco = cb.id_unico 
WHERE md5(pp.id_unico)='".$_GET['id']."'");

$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('FECHA RECAUDO:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode($rowf[0][11]),1,0,'L');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('NÚMERO CUENTA:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode($rowf[0][12]),1,0,'L');
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('FECHA FACTURA:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode($rowf[0][1]),1,0,'L');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('NÚMERO FACTURA:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode($rowf[0][2]),1,0,'L');
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('OBSERVACIONES:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(145,5,utf8_decode($rowf[0][3]),1,0,'L');
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('TIPO ACUERDO:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode($rowf[0][6]),1,0,'L');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('FECHA ACUERDO:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode($rowf[0][5]),1,0,'L');
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('NÚMERO ACUERDO:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode($rowf[0][4]),1,0,'L');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('NÚMERO CUOTAS:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode($rowf[0][7]),1,0,'L');
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('% INTERÉS:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode($rowf[0][8]),1,0,'L');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(45,5,utf8_decode('VALOR:'),1,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,utf8_decode(number_format($rowf[0][9],2)),1,0,'L');
$pdf->Ln(15);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,utf8_decode('DATOS RECAUDO'),1,0,'C');
$pdf->Ln(5);
$cx = $pdf->GetX();
$cy = $pdf->GetY();
$pdf->SetFont('Arial','B',8);
$pdf->Cell(20,5,utf8_decode('CUOTA'),0,0,'C');

if ($rowf[0][10] == 1) {
    $rowcn = $con->Listar("SELECT DISTINCT 
        c.nombre, c.id_unico 
        FROM ga_detalle_factura df
        left JOIN ga_detalle_acuerdo da on da.id_unico= df.detalleacuerdo
        LEFT JOIN gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda 
        LEFT JOIN gr_concepto c ON c.id_unico = cp.id_concepto 
        WHERE df.factura = '".$rowf[0][0]."' ORDER BY da.concepto_deuda ");
} else if ($rowf[0][10] == 2) {
    $rowcn = $con->Listar("SELECT DISTINCT 
        cc.nom_inf , cc.id_unico 
        FROM ga_detalle_factura df
        LEFT JOIN ga_detalle_acuerdo da on da.id_unico= df.detalleacuerdo
        LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda 
        WHERE df.factura = '".$rowf[0][0]."'  ORDER BY da.concepto_deuda");
}
$filas = 150/count($rowcn);
for ($c = 0; $c < count($rowcn); $c++) {
    $x  = $pdf->GetX();
    $y  = $pdf->GetY(); 
    $pdf->MultiCell($filas,5, utf8_decode($rowcn[$c][0]),0,'C');
    $y2 = $pdf->GetY();
    $h  = $y2 - $y;
    if($h > $h2){$alto = $h;$h2 = $h;}else{$alto = $h2;}
    $pdf->SetXY($x+$filas,$y);
}
$pdf->Cell(20,5,utf8_decode('TOTAL'),0,0,'C');
$pdf->SetXY($cx,$cy);
$pdf->Cell(20,$alto, utf8_decode(''),1,0,'C');
for ($c = 0; $c < count($rowcn); $c++) {
    $x =$pdf->GetX();
    $y =$pdf->GetY(); 
    $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
    $pdf->SetXY($x+$filas,$y);
}
$pdf->Cell(20,$alto,'',1,0,'C');
$pdf->Ln($alto);


$pdf->SetFont('Arial','',8);
$rowct = $con->Listar("SELECT DISTINCT da.nrocuota 
FROM ga_detalle_factura df 
LEFT JOIN ga_detalle_acuerdo da ON df.detalleacuerdo = da.id_unico 
WHERE df.factura = '".$rowf[0][0]."'");
$tf = 0;
for ($i= 0;$i < count($rowct );$i++) {
    $ncta = $rowct[$i][0]; 
    $pdf->Cell(20,5,utf8_decode($ncta),1,0,'L');
    $tc = 0;
    for ($c = 0; $c < count($rowcn); $c++) {
        $cn = $rowcn[$c][1];
        if ($rowf[0][10]== 1) {
            $rowv = $con->Listar("SELECT DISTINCT SUM(df.valor) FROM ga_detalle_factura df 
                LEFT JOIN ga_detalle_acuerdo da ON df.detalleacuerdo = da.id_unico 
                LEFT JOIN gr_concepto_predial cp ON da.concepto_deuda = cp.id_unico 
                LEFT JOIN gr_concepto c ON cp.id_concepto = c.id_unico 
                WHERE df.factura = '".$rowf[0][0]."' AND c.id_unico = $cn AND da.nrocuota  = $ncta 
                GROUP BY da.nrocuota, c.id_unico");
        } else { 
            $rowv = $con->Listar("SELECT DISTINCT SUM(df.valor) FROM ga_detalle_factura df 
                LEFT JOIN ga_detalle_acuerdo da ON df.detalleacuerdo = da.id_unico 
                LEFT JOIN gc_concepto_comercial cp ON da.concepto_deuda = cp.id_unico 
                WHERE df.factura = '".$rowf[0][0]."' AND cp.id_unico = $cn AND da.nrocuota  = $ncta 
                GROUP BY da.nrocuota, cp.id_unico");
        }
        $pdf->Cell($filas,5,utf8_decode(number_format($rowv[0][0], 2, '.', ',')),1,0,'R');
        $tc += $rowv[0][0]; 
        
    } 
    $pdf->Cell(20,5,utf8_decode(number_format($tc, 2, '.', ',')),1,0,'R');
    $pdf->Ln(5);
    $tf += $tc;
} 
$pdf->SetFont('Arial','B',10);
$pdf->Cell(170,5,utf8_decode('TOTAL RECAUDO'),1,0,'R');
$pdf->Cell(20,5,utf8_decode(number_format($tf, 2, '.', ',')),1,0,'R');
$pdf->Ln(5);


ob_end_clean();		
$pdf->Output(0,'Recaudo_Acuerdo.pdf',0);