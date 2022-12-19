<?php
class PDF extends FPDF{

    var $widths;
    var $aligns;

    function SetWidths($w){
        $this->widths = $w;
    }

    function SetAligns($a){
        $this->aligns = $a;
    }

    function fill($f){
        $this->fill = $f;
    }

    function Row($data){
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        $this->CheckPageBreak($h);
        for($i = 0; $i < count($data); $i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h, '');
            $this->MultiCell($w,5,$data[$i],'LR', $a, '');
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h - 5);
    }

    function fila($data){
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h = 5 * $nb;
        $this->CheckPageBreak($h);
        for($i = 0; $i < count($data); $i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, 0, 0, '');
            $this->MultiCell($w,5, $data[$i],'', $a, '');
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h - 5);
    }

    function CheckPageBreak($h){
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt){
        $cw =&$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ( $w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s  = str_replace('\r','', $txt);
        $nb = strlen($s);
        if( $nb > 0 and $s[$nb-1] == '\n' )
            $nb--;
        $sep = -1;
        $i   = 0;
        $j   = 0;
        $l   = 0;
        $nl  = 1;
        while( $i < $nb ){
            $c = $s[$i];
            if( $c == '\n' ){
                $i++;
                $sep =-1;
                $j   =$i;
                $l   =0;
                $nl++;
                continue;
            }
            if( $c == '' )
                $sep = $i;
            $l += $cw[$c];
            if( $l > $wmax ){
                if( $sep ==-1 ){
                    if($i == $j)
                        $i++;
                }else
                    $i = $sep+1;
                $sep =-1;
                $j   =$i;
                $l   =0;
                $nl++;
            }else
                $i++;
        }
        return $nl;
    }
}
$pdf = new PDF('L','mm','Letter');
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);
if($ruta != ''){
    $pdf->Image('./'.$ruta,10,8,20);
}
$pdf->SetXY(40,15);
$pdf->MultiCell(220, 5, utf8_decode($razonsocial), 0, 'C');
$pdf->SetX(10);
$pdf->MultiCell(270,5,utf8_decode(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."\n$direccion TELEFONO : $telefono\nLISTADO DE RECAUDOS ENTRE FECHAS")),0,'C');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Ln(5);
$pdf->Cell(65, 5, 'RECAUDO', 'TLR', '', 'C');
$pdf->Cell(50, 5, 'FACTURA', 'TLR', '', 'C');
$pdf->Cell(50, 5, '', 'TLR', 0, 'C');
$pdf->Cell(35, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, 'VALOR', 'TLR', 0, 'C');
$pdf->Cell(30, 5, 'VALOR', 'TLR', 0, 'C');
$pdf->Ln(5);
$pdf->Cell(20, 5, 'FECHA', 'TLRB', '', 'C');
$pdf->Cell(25, 5, 'TIPO', 'TLRB', '', 'C');
$pdf->Cell(20, 5, 'NRO', 'TLRB', '', 'C');
$pdf->Cell(20, 5, 'FECHA', 'TLRB', '', 'C');
$pdf->Cell(15, 5, 'TIPO', 'TLRB', '', 'C');
$pdf->Cell(15, 5, 'NRO', 'TLRB', '', 'C');
$pdf->Cell(50, 5, 'TERCERO', 'LRB', 0, 'C');
$pdf->Cell(35, 5, utf8_decode('DESCRIPCIÓN'), 'LRB', 0, 'C');
$pdf->Cell(30, 5, 'FACTURA', 'LRB', 0, 'C');
$pdf->Cell(30, 5, 'RECAUDO', 'LRB', 0, 'C');
$pdf->Ln(5);
$fechaI = explode("/", $_REQUEST['txtFechaI']);
$fechaI = "$fechaI[2]-$fechaI[1]-$fechaI[0]";
$fechaF = explode("/", $_REQUEST['txtFechaF']);
$fechaF = "$fechaF[2]-$fechaF[1]-$fechaF[0]";
$data   = $this->pag->listadoRecuados($fechaI, $fechaF, $_REQUEST['sltTipoI'], $_REQUEST['sltTipoF'], $_REQUEST['clase']);
list($xvtf, $xvtp) = array(0, 0);
$pdf->SetFont('Arial', '', 8);
foreach ($data as $row){
    $datX = $this->fat->obtenerDetallesFactura($row[4]);
    $xxx  = 0;
    foreach ($datX as $rowX){
        $xxx += (($rowX[7] + $rowX[5] + $rowX[6]) * $rowX[4]);
    }
    $xrec   = $this->pag->obtenerTotalPago($row[0]);
    $xtr    = $this->pag->obtenerDetallesPago($row[0]);
    if($xtr != 0){
        $pdf->SetWidths(array(20, 25, 20, 20, 15, 15, 50, 35, 30, 30));
        $pdf->SetAligns(array('L', 'L', 'R', 'L', 'L', 'R', 'L', 'L', 'R', 'R'));
        $pdf->fila(array(
            $row[1], utf8_decode($row[2]), $row[3], $row[5], $row[6], $row[7], $row[8], $row[10],
            number_format($xxx, 0, ',', '.'),
            number_format($xrec, 0, ',', '.')
        ));
        $pdf->Ln(5);
        $xvtf += $xxx; $xvtp += $xrec;
    }
}
$pdf->Ln(1);
$pdf->Cell(260, 1, '', 'B', '', 'C');
$pdf->Ln(1);
$pdf->Cell(260, 1, '', 'B', '', 'C');
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(200, 5, 'TOTALES', 0, '', 'C');
$pdf->Cell(30, 5, number_format($xvtf, 0, ',', ','), 0, '', 'R');
$pdf->Cell(30, 5, number_format($xvtp, 0, ',', ','), 0, '', 'R');
while (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output(0,'Listado_Recuados_Entre_Fechas'.'.pdf',0);