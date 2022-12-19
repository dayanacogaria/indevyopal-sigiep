<?php

require_once("./Conexion/ConexionPDO.php");
require_once("./Conexion/conexion.php");
$con = new ConexionPDO();

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

}

$pdf = new PDF('P', 'mm', 'Letter');
$nb = $pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetXY(10, 10);
$pdf->Cell(100, 20, "", "LRTB", 0, "C");
if ($ruta != '') {
    $pdf->Image('./' . $ruta, 10, 11, 40);
}
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetXY(40, 15);
$pdf->MultiCell(50, 5,  utf8_decode("REGISTRO DE\nHUÉSPEDES"), 0, "R");
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(110, 10);
$pdf->Cell(50, 10, utf8_decode("REGISTRO Nª"), "T", 0, "R");
$pdf->SetXY(160, 10);
$pdf->Cell(50, 10, $numero, "TR", 0, "L");
$pdf->SetXY(99, 20);
$pdf->SetFont('Arial', 'B', 9.5);
$pdf->Cell(40, 10, "RESPONSABLE", "B", 1, "R");
$pdf->SetXY(136, 20);
$pdf->SetFont('Arial', '', 9.5);
$pdf->Cell(74, 10, utf8_decode($responsable), "BR", 1, "C");
$pdf->Ln(0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 5, "FECHA \nLLEGADA", 1, "L");
$pdf->SetXY(40, 30);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, $fechaE, 1, 1, "L");
$pdf->SetXY(110, 30);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 10, "FECHA SALIDA", 1, "L");
$pdf->SetXY(140, 30);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, $fechaS, 1, 1, "L");
$pdf->Ln(0);
$xh = 38;
$conx = 42;
$pt = 1;
$tarifan = 0;
$footh = 120;
$fir = 0;
$numdt = mysqli_num_rows($tarifa);
if ($numdt > 0) {
    while ($row = mysqli_fetch_row($tarifa)) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY($conx, $xh);
        if ($pt < $numdt) {
            $pdf->MultiCell(70, 10, $row[1] . ",", 0);
            $pdf->Ln(0);
        } else {
            $pdf->MultiCell(70, 10, $row[1], 0);
            $pdf->Ln(0);
        }

        $conx += 7;
        if ($conx > 100) {
            $conx = 42;
            $xh = 42;
        }
        $pt ++;
        $tarifan += $row[3];

        // Listar Huesped por habitación
        $dtper = $con->Listar("SELECT -- t.*,
                                UPPER(CONCAT_WS(' ', t.nombreuno, t.nombredos)),
                            UPPER(CONCAT_WS(' ', t.apellidouno, t.apellidodos))
                            , t.numeroidentificacion, idf.nombre, DATE_FORMAT(t.fecha_nacimiento, '%d/%m/%Y') as fehcaNaimiento,
                            CASE WHEN dir.direccion = '' OR dir.direccion IS NULL OR dir.direccion = 'NULL' THEN ''
                            ELSE dir.direccion END AS direccion,
                            tlf.valor,
                            CASE WHEN t.email = '' OR t.email IS NULL OR t.email = 'NULL' THEN ''
                            ELSE t.email END AS email,
                            CASE WHEN UPPER(gps.nombre) = '' OR UPPER(gps.nombre) IS NULL OR UPPER(gps.nombre) = 'NULL' THEN ''
                            ELSE UPPER(gps.nombre) END AS pais
                            FROM gh_detalle_persona per
                            LEFT JOIN gf_tercero t ON per.tercero = t.id_unico
                            LEFT JOIN gf_tipo_identificacion idf ON t.tipoidentificacion = idf.id_unico
                            LEFT JOIN gf_telefono tlf ON t.id_unico = tlf.tercero
                            LEFT JOIN gf_direccion dir ON t.id_unico = dir.tercero
                            LEFT JOIN gf_ciudad       AS gci ON dir.ciudad_direccion = gci.id_unico
                            LEFT JOIN gf_departamento AS gdp ON gci.departamento = gdp.id_unico
                            LEFT JOIN gf_pais         AS gps ON gdp.pais = gps.id_unico
                        WHERE detalle = $row[0] ");
        if (count($dtper)) {
            $fir ++;
            $xper = $footh - 2;
            $hg = 0;
            for ($i = 0; $i < count($dtper); $i++) {
                $pdf->SetXY(40, $xper);
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(170, 10, "* " . utf8_decode($dtper[$i][0]) . " " . utf8_decode($dtper[$i][1]) . " " . utf8_decode($dtper[$i][3]) . " " . utf8_decode($dtper[$i][2]), 0, "L");
                $xper += 4;
                $hg += 5;
            }
            $pdf->SetXY(40, $footh);
            $pdf->SetFont('Arial', '', 6);
            $pdf->MultiCell(170, $hg, "", 1, "L");
            $pdf->SetXY(10, $footh);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(30, $hg, $row[1], 1, "C");
            $footh += $hg;
        }
    }
}
$pdf->SetXY(10, 40);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 10, utf8_decode("HABITACIÓN"), 1, "L");
$pdf->SetXY(40, 40);
$pdf->Cell(70, 10, "", 1, 1, "L");
$pdf->SetXY(10, 40);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(110, 40);
$pdf->MultiCell(30, 10, "TARIFA NOCHE", 1, "L");
if ($descuento > 0) {
    $des = ($tarifan * $descuento) / 100;
    $tarifan = $tarifan - $des;
}
$pdf->SetXY(10, 40);
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(140, 40);
$pdf->MultiCell(30, 10, number_format($tarifan,2,'.',','), 1, "L");
$pdf->Ln(0);
$pdf->SetXY(10, 40);
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(170, 40);
$pdf->Cell(40, 10, "$difer NOCHE(S)", 1, 1, "L");
$xh = 50;
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 10, "APELLIDOS", 1, "L");
$pdf->SetXY(40, $xh);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, utf8_decode($apellidos), 1, 1, "L");
$pdf->Ln(0);
$pdf->SetY($xh + 10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 10, "NOMBRES", 1, "L");
$pdf->SetXY(40, $xh + 10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, utf8_decode($nombres), 1, 1, "L");
$pdf->Ln(0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 5, utf8_decode("NÚMERO DE DOCUMENTO"), 1, "L");
$pdf->SetXY(40, $xh + 20);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, $doc, 1, 1, "L");
$pdf->Ln(0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 5, "TIPO DE DOCUMENTO", 1, "L");
$pdf->SetXY(40, $xh + 30);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, utf8_decode($tdoc), 1, 1, "L");
$pdf->Ln(0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 5, "FECHA DE\nNACIMIENTO", 1, "L");
$pdf->SetXY(40, $xh + 40);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, $fechaN, 1, 1, "L");
$pdf->SetXY(110, $xh + 40);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 10, utf8_decode("DIRECCIÓN"), 1, "L");
$pdf->SetXY(140, $xh + 40);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, $direccion, 1, 1, "L");
$pdf->Ln(0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 10, utf8_decode("TELÉFONO"), 1, "L");
$pdf->SetXY(40, $xh + 50);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, $telefono, 1, 1, "L");
$pdf->Ln(0);
$pdf->SetXY(10, $xh + 60);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 10, utf8_decode("EMAIL"), 1, "L");
$pdf->SetXY(40, $xh + 60);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, $email, 1, 1, "L");
$pdf->SetXY(110, $xh + 7);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 5, utf8_decode("INFORMACIÓN EMPRESA"), 0, "L");
$pdf->SetXY(140, $xh);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 20, "", 1, 1, "L");
$pdf->SetXY(140, $xh + 2);
$pdf->MultiCell(70, 4,utf8_decode($empresa), 0, "J");
$pdf->Ln(0);
$pdf->SetXY(110, $xh + 20);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 5, utf8_decode("MODO RESERVA"), 1, "Ln");
$pdf->SetXY(140, $xh + 20);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(70, 10, utf8_decode($modoreserva), 1, "L");
$pdf->SetXY(110, $xh + 30);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 10, utf8_decode("# HÚESPEDES"), 1, "L");
$pdf->SetXY(140, $xh + 30);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(70, 10, "", 1, "L");
$pdf->SetXY(140, $xh + 30);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(70, 10, utf8_decode("Adultos: " . $adultos . "           Niños: " . $peque), 0);
$pdf->SetXY(140, $xh + 30);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(70, 10, "", 1, "L");
$pdf->SetXY(110, $xh + 50);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 5, utf8_decode("PAIS DE PROCEDENCIA"), 1, "L");
$pdf->SetXY(140, $xh + 50);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, $pais, 1, 1, "L");
$pdf->Ln(0);
$pdf->SetXY(110, $xh + 60);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(30, 5, utf8_decode("PRÓXIMO DESTINO"), 1, "L");
$pdf->SetXY(140, $xh + 60);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 10, utf8_decode($destino), 1, 1, "L");
// Footer
$pdf->SetXY(10, $footh);
$pdf->Cell(200, 5, "", "LR", 0, "C");
$pdf->SetXY(10, $footh + 5);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(200, 5, "ESTOY DE ACUERDO CON:", "LR", 0, "C");
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(200, 5, utf8_decode("El precio de los servicios de hospedaje indicados en este contrato. El reglamento interior del hotel.
Para su conveniencia y protección favor informar de sus objetos de valor dejados en su habitacion o dejarlos en la recepción para su custodia. El hotel no se hace responsable por dinero en efectivo, joyas u otros valores no informados."), "LR", "L");
$pdf->Ln(0);
$pdf->Ln(0);
$pdf->Cell(200, 5, "", "LR", 0, "C");
$pdf->Ln(5);
$pdf->MultiCell(200, 5, utf8_decode('HORA DE SALIDA 1:00 pm
Al momento de su registro el hotel Requiere conocer su forma de pago . ADVERTENCIA: En desarrollo a lo dispuesto en el articulo 17 de la Ley 679 de 2001 el HOTEL advierte al HUESPED que la explotacion y el abuso sexual de menores de edad en el país son castigados penal y civilmente conforme a las disposiciones legales vigentes.'), "LR", "L");
$pdf->Ln(0);
if ($fir > 0) {
    $pdf->Cell(200, 5, "", "LR", 0, "C");
    $pdf->Ln(5);
    $pdf->MultiCell(200, 5, utf8_decode('El ' . $nombreCompania . ' es un 100% libre de humo de tabaco, debido a esta norma no es permitido fumar dentro de las habitaciones ni en las areas públicas. El incumplimiento de esta norma conllevará un cargo diario de $200.000 que se verá reflejado en su factura.'), "LRB", "L");
    $pdf->Ln(0);
    $pdf->AddPage();
    $pdf->Cell(200, 5, "", "LRT", 0, "C");
    $pdf->Ln(5);
    $pdf->MultiCell(200, 5, utf8_decode("La politica de sostenibilidad del " . $nombreCompania . " parte del absoluto respeto por el MEDIO AMBIENTE, en todas las actividades que desarrolla, asi mismo se acoge al el código penal, ley 1453, en el artículo 328, modificado por la ley 1453, de 2011, establece condenas de 48 a 108 meses de prisión a la explotación ilegal de fauna y flora silvestre y una multa de 35 mil salarios mínimos mensuales legales vigentes. El " . $nombreCompania . " dicen NO a la discriminacion ni exclusion de las poblaciones vulnerables."), "LRB", "L");
    $pdf->Ln(0);
    $pdf->Cell(200, 45, "", "LRBT", "", "C");
    $pdf->Ln(5);
    $pdf->SetX(45);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 5, "FIRMA HUESPED", "", "", "L");
    $pdf->Ln(10);
    $pdf->SetX(45);
    $pdf->Cell(100, 5, "", "B", "", "L");
    $pdf->Ln(10);
    $pdf->SetX(45);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 5, "PLACA VEHICULO", "", "", "L");
    $pdf->Ln(10);
    $pdf->SetX(45);
    $pdf->Cell(100, 5, utf8_decode($placa), "B", "", "L");
} else {
    $pdf->Cell(200, 5, "", "LR", 0, "C");
    $pdf->Ln(5);
    $pdf->MultiCell(200, 5, utf8_decode('El ' . $nombreCompania . ' es un 100% libre de humo de tabaco, debido a esta norma no es permitido fumar dentro de las habitaciones ni en las areas públicas. El incumplimiento de esta norma conllevará un cargo diario de $200.000 que se verá reflejado en su factura.'), "LR", "L");
    $pdf->Ln(0);
    $pdf->Cell(200, 5, "", "LR", 0, "C");
    $pdf->Ln(5);
    $pdf->MultiCell(200, 5, utf8_decode("La politica de sostenibilidad del " . $nombreCompania . " parte del absoluto respeto por el MEDIO AMBIENTE, en todas las actividades que desarrolla, asi mismo se acoge al el código penal, ley 1453, en el artículo 328, modificado por la ley 1453, de 2011, establece condenas de 48 a 108 meses de prisión a la explotación ilegal de fauna y flora silvestre y una multa de 35 mil salarios mínimos mensuales legales vigentes. El " . $nombreCompania . " dicen NO a la discriminacion ni exclusion de las poblaciones vulnerables."), "LRB", "L");
    $pdf->Ln(0);
    $pdf->Cell(200, 45, "", "LRBT", "", "C");
    $pdf->Ln(5);
    $pdf->SetX(45);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 5, "FIRMA HUESPED", "", "", "L");
    $pdf->Ln(10);
    $pdf->SetX(45);
    $pdf->Cell(100, 5, "", "B", "", "L");
    $pdf->Ln(10);
    $pdf->SetX(45);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 5, "PLACA VEHÍCULO", "", "", "L");
    $pdf->Ln(10);
    $pdf->SetX(45);
    $pdf->Cell(100, 5, utf8_decode($placa), "B", "", "L");
}
while (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output(0, "InformeSalida.pdf", 0);
