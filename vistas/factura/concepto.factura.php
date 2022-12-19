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
$pdf->MultiCell(270,5,utf8_decode(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."\n$direccion TELEFONO : $telefono\nLISTADO DE FACTURACIÓN POR CONCEPTO")),0,'C');
$pdf->SetFont('Arial','B',9);
$pdf->Ln(5);
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Cell(80, 5, '', 'TLR', 0, 'C');
$pdf->Cell(60, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Ln(2);
$pdf->Cell(30, 5, 'FECHA', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'TIPO', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'NUMERO', 'LR', 0, 'C');
$pdf->Cell(80, 5, 'DESCRIPCION', 'LR', 0, 'C');
$pdf->Cell(60, 5, 'TERCERO', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'VALOR', 'LR', 0, 'C');
$pdf->Ln(2);
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Cell(80, 5, '', 'BLR', 0, 'C');
$pdf->Cell(60, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Ln(5);
$fechaI = explode("/", $_REQUEST['txtFechaI']);
$fechaI = "$fechaI[2]-$fechaI[1]-$fechaI[0]";
$fechaF = explode("/", $_REQUEST['txtFechaF']);
$fechaF = "$fechaF[2]-$fechaF[1]-$fechaF[0]";
$data   = $this->factura->obtenerListadoConceptosFactura($_REQUEST['sltConceptoI'], $_REQUEST['sltConceptoF']);
$total  = 0;
foreach ($data as $row){
    $dataX = $this->factura->listdaoFacturasConcepto($fechaI, $fechaF, $row[0], $_REQUEST['clase']);
    if(count($dataX) > 0){
        $pdf->SetFont('Arial','B',8);
        $pdf->SetAligns(array('L', 'L', 'R', 'L'));
        $pdf->SetWidths(array(100));
        $pdf->fila(array($row[1]));
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        foreach ($dataX as $rowX){
            $datX = $this->factura->obtenerDetallesConcepto(md5($rowX[0]), $row[0]);
            $xxx  = 0;
            foreach ($datX as $rowt){
                $xxx += (($rowt[2] + $rowt[4] + $rowt[5] + $rowt[6]) * $rowt[3]);
            }
            $total += $xxx;
            $pdf->SetWidths(array(30, 30, 30, 80, 60, 30));
            $pdf->SetAligns(array('L', 'C', 'L', 'L', 'L', 'R'));
            $pdf->fila(array($rowX[1], $rowX[2], $rowX[3], $rowX[4], $rowX[5], number_format($xxx, 0)));
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
$pdf->Output(0,'Informe_concepto_factura'.'.pdf',0);		#Salida del documento