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
$pdf->MultiCell(270,5,utf8_decode(mb_strtoupper($nombreTipoIden.' : '.$numeroIdent."\n$direccion TELEFONO : $telefono\nLISTADO DE FACTURAS")),0,'C');
$pdf->SetFont('Arial','B',9);
$pdf->Ln(5);
$pdf->Cell(20, 5, '', 'TLR', 0, 'C');
$pdf->Cell(15, 5, '', 'TLR', 0, 'C');
$pdf->Cell(20, 5, '', 'TLR', 0, 'C');
$pdf->Cell(55, 5, '', 'TLR', 0, 'C');
$pdf->Cell(60, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Cell(30, 5, '', 'TLR', 0, 'C');
$pdf->Ln(2);
$pdf->Cell(20, 5, 'FECHA', 'LR', 0, 'C');
$pdf->Cell(15, 5, 'TIPO', 'LR', 0, 'C');
$pdf->Cell(20, 5, 'NUMERO', 'LR', 0, 'C');
$pdf->Cell(55, 5, 'DESCRIPCION', 'LR', 0, 'C');
$pdf->Cell(60, 5, 'TERCERO', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'VALOR BASE', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'VALOR IVA', 'LR', 0, 'C');
$pdf->Cell(30, 5, 'VALOR TOTAL', 'LR', 0, 'C');
$pdf->Ln(2);
$pdf->Cell(20, 5, '', 'BLR', 0, 'C');
$pdf->Cell(15, 5, '', 'BLR', 0, 'C');
$pdf->Cell(20, 5, '', 'BLR', 0, 'C');
$pdf->Cell(55, 5, '', 'BLR', 0, 'C');
$pdf->Cell(60, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Cell(30, 5, '', 'BLR', 0, 'C');
$pdf->Ln(5);
$fechaI = explode("/", $_REQUEST['txtFechaI']);
$fechaI = "$fechaI[2]-$fechaI[1]-$fechaI[0]";
$fechaF = explode("/", $_REQUEST['txtFechaF']);
$fechaF = "$fechaF[2]-$fechaF[1]-$fechaF[0]";
$data   = $this->factura->listdaoFacturas($fechaI, $fechaF, $_REQUEST['sltTipoI'], $_REQUEST['sltTipoF'], $_REQUEST['clase']);
list($total, $xtVB, $xtVI) = array(0, 0, 0);
$pdf->SetFont('Arial', '', 8);
foreach ($data as $row){
    $datX = $this->factura->obtenerDetalles(md5($row[0]));
    list($xVB, $xVI, $xxx) = array(0, 0, 0);
    foreach ($datX as $rowX){
        $xVB += ($rowX[2] * $rowX[3]);
        $xVI += ($rowX[4] * $rowX[3]);
        $xxx += (($rowX[2] + $rowX[4] + $rowX[5] + $rowX[6]) * $rowX[3]);
    }
    $total += $xxx; $xtVB += $xVB; $xtVI += $xVI;
    $pdf->SetWidths(array(20, 15, 20, 55, 60, 30, 30, 30));
    $pdf->SetAligns(array('L', 'C', 'R', 'L', 'L', 'R', 'R', 'R'));
    $pdf->fila(array(
        $row[1], $row[2], $row[3], $row[4], utf8_decode(mb_strtoupper($row[5])),
        number_format($xVB, 2, ',', '.'),
        number_format($xVI, 2, ',', '.'),
        number_format($xxx, 2, ',', '.')
    ));
    $pdf->Ln(5);
}
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(170, 5, 'TOTAL', 1, 0, 'C');
$pdf->Cell(30, 5, number_format($xtVB, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($xtVI, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($total, 2), 1, 0, 'R');
while (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output(0,'Informe_general_factura'.'.pdf',0);		#Salida del documento