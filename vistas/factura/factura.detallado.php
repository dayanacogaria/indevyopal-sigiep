<?php
class PDF extends FPDF{
    var $widths;
    var $aligns;
    function SetWidths($w){
        //Set the array of column widths
        $this->widths = $w;
    }
    function SetAligns($a){
        //Set the array of column alignments
        $this->aligns = $a;
    }
    function fill($f){
        //juego de arreglos de relleno
        $this->fill = $f;
    }
    function Row($data){
        //Calcula el alto de l afila
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Realiza salto de pagina si es necesario
        $this->CheckPageBreak($h);
        //Pinta las celdas de la fila
        for($i = 0; $i < count($data); $i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Guarda la posicion actual
            $x = $this->GetX();
            $y = $this->GetY();
            //Pinta el border
            $this->Rect($x, $y, $w, $h, $style);
            //Imprime el texto
            $this->MultiCell($w,5,$data[$i],'LR', $a, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Hace salto de la pagina
        $this->Ln($h - 5);
    }

    function fila($data){
        //Calcula el alto de l afila
        $nb = 0;
        for($i = 0; $i < count($data); $i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h = 5 * $nb;
        //Realiza salto de pagina si es necesario
        $this->CheckPageBreak($h);
        //Pinta las celdas de la fila
        for($i = 0; $i < count($data); $i++){
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Guarda la posicion actual
            $x = $this->GetX();
            $y = $this->GetY();
            //Pinta el border
            $this->Rect($x, $y, 0, 0, $style);
            //Imprime el texto
            $this->MultiCell($w,5, $data[$i],'', $a, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Hace salto de la pagina
        $this->Ln($h - 5);
    }

    function CheckPageBreak($h){
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }
    function NbLines($w,$txt){
        //Computes the number of lines a MultiCell of width w will take
        $cw =&$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ( $w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s  = str_replace('\r','', $txt);
        $nb = strlen($s);
        if( $nb > 0 and $s[$nb-1] == '\n' )
            $nb–;
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

    #Funcón cabeza de la página
}
$pdf = new PDF('L','mm','Letter'); 		#Creación del objeto pdf
$nb=$pdf->AliasNbPages();						#Objeto de número de pagina
$pdf->AddPage();								#Agregar página
$pdf->SetFont('Arial', 'B', 10);
if($ruta != ''){
    $pdf->Image('./'.$ruta,10,8,20);
}
$pdf->SetXY(40,15);
$pdf->MultiCell(220, 5, utf8_decode($razonsocial), 0, 'C');
$pdf->SetX(10);
$pdf->MultiCell(270,5,utf8_decode(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."\n$direccion TELEFONO : $telefono\nLISTADO DETALLADO DE FACTURAS")),0,'C');
$pdf->SetFont('Arial','B',9);
$pdf->Ln(5);
$pdf->Cell(80, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Ln(2);
$pdf->Cell(80, 5, 'CONCEPTO', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'CANTIDAD', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'VALOR', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'IVA', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'IMPOCONSUMO', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'AJUESTE PESO', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'VALOR TOTAL', 'LR', 0, 'C');
$pdf->Ln(2);
$pdf->Cell(80, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Ln(5);
$fechaI = explode("/", $_REQUEST['txtFechaI']);
$fechaI = "$fechaI[2]-$fechaI[1]-$fechaI[0]";
$fechaF = explode("/", $_REQUEST['txtFechaF']);
$fechaF = "$fechaF[2]-$fechaF[1]-$fechaF[0]";
$data   = $this->factura->listdaoFacturasDetalle($fechaI, $fechaF, $_REQUEST['clase']);
$total  = 0; $iva = 0; $impo = 0; $ajuste = 0;
foreach ($data as $row){
    $dataX = $this->factura->obtenerDetalles(md5($row[0]));
    if(count($dataX) > 0){
        $pdf->SetFont('Arial','B',8);
        $pdf->SetAligns(array('L', 'L', 'R', 'L'));
        $pdf->SetWidths(array(20, 10, 15, 100));
        $pdf->fila(array($row[1], $row[2], $row[3], $row[5]));
        $pdf->Ln(5);
        foreach ($dataX as $rowX){
            $xxx = (($rowX[2] + $rowX[4] + $rowX[5] + $rowX[6]) * $rowX[3]);
            $total += $xxx;
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetWidths(array(80, 30, 30, 30, 30, 30, 30));
            $pdf->SetAligns(array('L', 'R', 'R', 'R', 'R', 'R', 'R'));
            $pdf->fila(array($rowX[8], $rowX[3], number_format($rowX[2], 2), number_format($rowX[4], 2), number_format($rowX[5], 2), number_format($rowX[6], 2), number_format($xxx, 0)));
            $pdf->Ln(5);
        }
    }
}
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(230, 5, 'TOTAL', 1, 0, 'C');
$pdf->Cell(30, 5, number_format($total, 0), 1, 0, 'R');
while (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output(0,'Informe_detallado_factura'.'.pdf',0);		#Salida del documento