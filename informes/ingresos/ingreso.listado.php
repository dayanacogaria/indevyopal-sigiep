<?php

require_once("./Conexion/conexion.php");

class PDF extends FPDF {

    var $widths;
    var $aligns;

    function SetWidths($w) {
        $this->widths = $w; //Set the array of column widths
    }

    function SetAligns($a) {
        $this->aligns = $a; //Set the array of column alignments
    }

    function fill($f) {
        $this->fill = $f; //juego de arreglos de relleno
    }

    function Row($data) {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 6 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            //$this->Rect($x,$y,$w,$h,$style);
            //Print the text
            $this->MultiCell($w, 6, $data[$i], 0, $a, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h - 6);
    }

    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace('\r', '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == '\n')
            $nb–;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == '\n') {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == '')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function Footer() {
        global $usuario;
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(15);
        $this->Cell(25, 10, utf8_decode('Fecha: ' . date('d-m-Y')), 0, 0, 'L');
        $this->Cell(280);
        $this->Cell(0, 10, utf8_decode('Pagina ' . $this->PageNo() . '/{nb}'), 0, 0);
    }

}

$pdf = new PDF('L', 'mm', 'Legal');
$nb = $pdf->AliasNbPages();                    #Objeto de número de pagina
$pdf->AddPage();                                #Agregar página
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetY(10);
if ($ruta != '') {
    $pdf->Image('./' . $ruta, 15, 8, 20);
}
$pdf->SetX(35);
$pdf->Cell(308, 5, utf8_decode($nombreCompania), 0, 0, 'C');
$pdf->Ln(5);
$pdf->SetX(35);
$pdf->Cell(308, 5, "NIT :" . $nitcompania, 0, 0, 'C');
$pdf->Ln(5);
$pdf->SetX(35);
$pdf->Cell(308, 5, "REPORTE DE INGRESOS DEL " . $_REQUEST['txtFechaI'] ." AL ". $_REQUEST['txtFechaF'], 0, 0, 'C');
$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 10);
$ff = $pdf->GetY();
$xx = $pdf->GetX();
$pdf->MultiCell(25, 5, utf8_decode("NÚMERO DE INGRESO"), 1, 'C');
$pdf->SetY($ff);
$pdf->SetX($xx+25);
$pdf->MultiCell(40, 10, utf8_decode("TIPO HABITACIÓN"), 1, 'C');
$pdf->SetY($ff);
$pdf->SetX($xx+65);
$pdf->MultiCell(25, 10, utf8_decode("HABITACIÓN"), 1, 'C');
$pdf->SetY($ff);
$pdf->SetX($xx+90);
$pdf->MultiCell(80, 10, utf8_decode("NOMBRE DE HÚESPED"), 1, 'C');
$pdf->SetY($ff);
$pdf->SetX($xx+170);
$pdf->MultiCell(30, 5, utf8_decode("FECHA LLEGADA"), 1, 'C');
$pdf->SetY($ff);
$pdf->SetX($xx+200);
$pdf->MultiCell(30, 10, utf8_decode("FECHA SALIDA"), 1, 'C');
$pdf->SetY($ff);
$pdf->SetX($xx+230);
$pdf->MultiCell(25, 5, utf8_decode("CANTIDAD DE NOCHES"), 1, 'C');
$pdf->SetY($ff);
$pdf->SetX($xx+255);
$pdf->MultiCell(25, 5, utf8_decode("NÚMERO DE RESERVA"), 1, 'C');
$pdf->SetY($ff);
$pdf->SetX($xx+280);
$pdf->MultiCell(25, 5, utf8_decode("TIPO DE RESERVA"), 1, 'C');
$pdf->SetY($ff);
$pdf->SetX($xx+305);
$pdf->MultiCell(30, 5, utf8_decode("CONCEPTO CONSUMIBLE"), 1, 'C');
foreach ($data as $row) {
    $page = round($pdf->GetY());
    $movimiento = $row[6];
    $date1 = new DateTime($row[4]);
    $date2 = new DateTime($row[5]);
    $diff = $date1->diff($date2);
    $cantidad = $diff->days;
    if ($cantidad < 1) {
        $cantidad = 1;
    }
    $sqldetalles =  " SELECT gdm.id_unico, stp.nombre, sph.codigo
    FROM gh_detalle_mov gdm
        LEFT JOIN gh_espacios_habitables sph 
            ON gdm.espacio = sph.id_unico
        LEFT JOIN gh_tipo_espacio stp 
            ON sph.tipo = stp.id_unico
    WHERE movimiento = $movimiento ";


    $detalles = $mysqli->query($sqldetalles);
    $countdet = mysqli_num_rows($detalles);
    if ($countdet > 0){
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(35);
        $ygh = $pdf->GetY();
        $higthnum = 0;
        
        /*****Cantidad de Húespedes en la habitación******/
        $sqlnumdelta = 
        "
        SELECT GROUP_CONCAT(gdm.id_unico)
        FROM gh_detalle_mov gdm
        WHERE gdm.movimiento =  $movimiento
        ";
        $restnumdelta = $mysqli->query($sqlnumdelta);
        $rowCon = mysqli_fetch_row($restnumdelta);
        $detlin = $rowCon[0];
        $sqlnumpersonas = 
        "
        SELECT *
        FROM gh_detalle_persona
        WHERE detalle IN ($detlin)
        ";
        $restnumpersonas = $mysqli->query($sqlnumpersonas);
        $countpersonas = mysqli_num_rows($restnumpersonas);
        if ($countpersonas >= 2){
            $oper = (3.2 * $countpersonas);
            $oper2 =  $page + $oper;
            if ($oper2 >= 180){
                $pdf->AddPage();
                $ygh = $pdf->GetY();
            }
        }else if ($countpersonas <= 1) {
            $oper = 7 * $countpersonas;
            $oper2 = $page + $oper;
            if ($oper2 >= 180){
                $pdf->AddPage();
                $ygh = $pdf->GetY();
            }
        }
        /*************************************************/
        while ($row2 = mysqli_fetch_row($detalles)) {
        $sqldetallesp = 
        "
        SELECT 
        UPPER(IF(
        CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
        gtr.razonsocial,
        CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
        )) AS TER
        FROM gh_detalle_persona gdp
            LEFT JOIN gf_tercero gtr 
                ON gdp.tercero = gtr.id_unico
        WHERE gdp.detalle = $row2[0]
        ";
        $detallesp = $mysqli->query($sqldetallesp);
        $countper  = mysqli_num_rows($detallesp);
        $ypb = $pdf->GetY();
        if ($countper > 0){
            $hight = 3.2;
            $hightcell = 4;
            if($countper == 1){
                $hightcell = 7;
            }
            $pdf->SetY($ypb+1);
            while ($row3 = mysqli_fetch_row($detallesp)) {
                    $page = round($pdf->GetY());
                    $pdf->SetX(100);
                    $pdf->Cell(80, $hightcell, utf8_decode($row3[0]), 0,0,'L');
                    $pdf->Ln(4);
                    $hight += 3.2;
            }
            if($countper == 1){
                $hight = 7;                
            }            
            $pdf->SetXY(100,$ypb);
            $hight = $hight+1;
            $higthnum += $hight;
            $pdf->Cell(80, $hight, utf8_decode(), 1,0,'C');
            $pdf->SetY($ypb);
            $pdf->SetX(35);
            $pdf->CellFitScale(40, $hight, utf8_decode($row2[1]), 1,0,'C');
            $pdf->Cell(25, $hight, utf8_decode($row2[2]), 1,0,'C');
            $pdf->SetX(180);
            $pdf->Cell(30, $hight, utf8_decode($row[2]), 1,0,'C');
            $pdf->Cell(30, $hight, utf8_decode($row[3]), 1,0,'C');        
            $pdf->Cell(25, $hight, utf8_decode($cantidad), 1,0,'C');
            $pdf->Cell(25, $hight, utf8_decode($row[7]), 1,0,'C');
            $pdf->Cell(25, $hight, utf8_decode(), 1,0,'C');
            $pdf->Ln($hight);
        }else{
            $pdf->SetX(35);            
            $pdf->CellFitScale(40, 7, utf8_decode($row2[1]), 1,0,'C');
            $pdf->Cell(25, 7, utf8_decode($row2[2]), 1,0,'C');
            $pdf->Cell(80, 7, utf8_decode($row[1]), 1,0,'L');
            $pdf->Cell(30, 7, utf8_decode($row[2]), 1,0,'C');
            $pdf->Cell(30, 7, utf8_decode($row[3]), 1,0,'C');
            $pdf->Cell(25, 7, utf8_decode($cantidad), 1,0,'C');
            $pdf->Cell(25, 7, utf8_decode($row[7]), 1,0,'C');
            $pdf->Cell(25, 7, utf8_decode(), 1,0,'C');
            
            $pdf->Ln(7);
            $higthnum += 7;
        }
    }
    $pdf->SetY($ygh);
    $pdf->Cell(25, $higthnum, utf8_decode($row[0]), 1,0,'C');
    $pdf->SetXY(315,$ygh);
    #***
    $consumible = '';
    $sqlconcp   = 'SELECT GROUP_CONCAT(DISTINCT c.nombre) FROM gp_detalle_factura df 
    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
    LEFT JOIN gp_factura f ON df.factura = f.id_unico 
    WHERE f.mov_hotel ='.$movimiento.' AND c.alojamiento != 1';
    $detallesC  = $mysqli->query($sqlconcp);
    while ($row6 = mysqli_fetch_row($detallesC)) {
        $consumible .= $row6[0];
    }
    
    
    #*
    $pdf->CellFitScale(30, $higthnum, utf8_decode($consumible), 1,0,'L');
    $pdf->Ln($higthnum);
        
    }
}

while (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output(0, "ReporteIngresos" . $_REQUEST['txtFechaI'] . ".pdf", 0);