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
$nb  = $pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);
if($ruta != ''){
    $pdf->Image('./'.$ruta,10,8,20);
}
$pdf->SetXY(40,15);
$pdf->MultiCell(210, 5, utf8_decode($razonsocial), 0, 'C');
$pdf->SetX(10);
$pdf->MultiCell(270,5, utf8_decode(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."\n$direccion TELEFONO : $telefono\nLISTADO DE CUENTAS POR COBRAR")), 0, 'C');
$pdf->SetFont('Arial','B',9);
$pdf->Ln(5);
$pdf->Cell(40, 5, 'FECHA', 'LTRB', '', 'C');
$pdf->Cell(40, 5, 'NRO', 'LTRB', '', 'C');
$pdf->Cell(60, 5, 'VALOR FACTURA', 'LTRB', '', 'C');
$pdf->Cell(60, 5, 'ABONOS', 'LTRB', '', 'C');
$pdf->Cell(60, 5, 'SALDO', 'LTRB', '', 'C');
$pdf->Ln(5);
$xDataT = $this->factura->listarTercerosCuentas($_REQUEST['sltClienteI'], $_REQUEST['sltClienteF']);
if(count($xDataT) > 0){
    list($xtvf, $xtva, $xtvs) = array(0, 0, 0);
    foreach ($xDataT as $rowX){
        $data = $this->factura->obtenerFacturasCliente($rowX[0], $_REQUEST['sltTipoFI'], $_REQUEST['sltTipoFF']);
        if(count($data) > 0){
            list($xrt) = array(0);
            foreach ($data as $row){/* Verificar data ya que se hizo para validar que los terceros tengan adeudos  */
                $xpa = $this->factura->VerificarRecaudoFactura($row[0]);
                $xxx = $this->factura->obtenerValorTotalFactura($row[0]);
                $xab = $this->factura->buscarAbonosPago($row[0]);
                $xsl = round($xxx - $xab, 0);
                if( ($xsl > 0) OR ($xsl < 0) OR ($xsl != 0)){
                    $xrt++;
                }
            }
            if($xrt > 0){
                $pdf->SetFont('Arial','B',9);
                $pdf->SetAligns(array('L', 'L', 'L'));
                $pdf->SetWidths(array(40, 100, 30));
                $pdf->fila(array('CLIENTE:', $rowX[1], $rowX[2]));
                $pdf->Ln(5);
                $pdf->SetFont('Arial','',9);
                list($xvf, $xva, $xas) = array(0, 0, 0);
                foreach ($data as $row){
                    $xpa = $this->factura->VerificarRecaudoFactura($row[0]);
                    $xxx = $this->factura->obtenerValorTotalFactura($row[0]);
                    $xab = $this->factura->buscarAbonosPago($row[0]);
                    $xsl = round($xxx - $xab, 0);
                    if( ($xsl > 0) OR ($xsl < 0) OR ($xsl != 0)){
                        $xvf += $xxx; $xva += $xab; $xas += $xsl;
                        $pdf->SetAligns(array('L', 'L', 'R', 'R', 'R'));
                        $pdf->SetWidths(array(40, 40, 60, 60, 60));
                        $pdf->fila(array(
                            $row[1], $row[2], number_format($xxx, 0, ',', '.'),
                            number_format($xab, 0, ',', '.'),
                            number_format($xsl, 0, ',', '.')
                        ));
                        $pdf->Ln(5);
                    }
                }
                $pdf->SetFont('Arial','B',9);
                $pdf->Ln(1);
                $pdf->Cell(260, 1, '', 'T', 0, 'C');
                $pdf->Ln(1);
                $pdf->Cell(260, 1, '', 'T', 0, 'C');
                $pdf->Ln(1);
                $pdf->SetWidths(array(80, 60, 60, 60));
                $pdf->SetAligns(array('C', 'R', 'R', 'R'));
                $pdf->fila(
                    array(
                        'SUBTOTALES', number_format($xvf, 0, ',', '.'),
                        number_format($xva, 0, ',', '.'),
                        number_format($xas, 0,' ,', '.')
                    )
                );
                $xtvf += $xvf; $xtva += $xva; $xtvs += $xas;
                $pdf->Ln(5);
            }
        }
    }
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',9);
    $pdf->Ln(1);
    $pdf->Cell(260, 1, '', 'T', 0, 'C');
    $pdf->Ln(1);
    $pdf->Cell(260, 1, '', 'T', 0, 'C');
    $pdf->Ln(1);
    $pdf->SetWidths(array(80, 60, 60, 60));
    $pdf->SetAligns(array('C', 'R', 'R', 'R'));
    $pdf->fila(
        array(
            'TOTALES', number_format($xtvf, 0, ',', '.'),
            number_format($xtva, 0, ',', '.'),
            number_format($xtvs, 0,' ,', '.')
        )
    );
}
while (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output(0,'Informe_general_factura'.'.pdf',0);