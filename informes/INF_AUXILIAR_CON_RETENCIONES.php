<?php
################################################################################################################
#                                                                                                   MODIFICACIONES
#19/07/2017 | ERICA G. | ARCHIVO CREADO * INFORME AUXILIAR CONTABLE RETENCIONES   PDF                                                                                       
################################################################################################################
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);
$para = $_SESSION['anno'];
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $para";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno = $an[0];
##########RECEPCION VARIABLES###############
#CUENTA INICIAL
if (empty($_POST['sltctai'])) {
    $cuentaI = '1';
} else {
    $cuentaI = $_POST['sltctai'];
}
#CUENTA FINAL
if (empty($_POST['sltctaf'])) {
    $cuentaF = '9';
} else {
    $cuentaF = $_POST['sltctaf'];
}

#FECHA INICIAL
if (empty($_POST['fechaini'])) {
    $fechaY = $anno;
    $fechaI = $fechaY . '/01/01';
    $fecha1 = '01/01/' . $anno;
} else {
    $fecha1 = $_POST['fechaini'];
    $fecha_div = explode("/", $fecha1);
    $dia1 = $fecha_div[0];
    $mes1 = $fecha_div[1];
    $anio1 = $fecha_div[2];
    $fechaI = $anio1 . '/' . $mes1 . '/' . $dia1;
}
#FECHA FINAL
if (empty($_POST['fechafin'])) {
    $fechaF = date('Y/m/d');
    $fecha2 = date('d/m/Y');
} else {
    $fecha2 = $_POST['fechafin'];
    $fecha_div2 = explode("/", $fecha2);
    $dia2 = $fecha_div2[0];
    $mes2 = $fecha_div2[1];
    $anio2 = $fecha_div2[2];
    $fechaF = $anio2 . '/' . $mes2 . '/' . $dia2;
}

##CONSULTA DATOS COMPAÑIA##
$compa = $_SESSION['compania'];
$comp = "SELECT t.razonsocial, t.numeroidentificacion, t.digitoverficacion, t.ruta_logo "
        . "FROM gf_tercero t WHERE id_unico=$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
if (empty($comp[2])) {
    $nitcompania = $comp[1];
} else {
    $nitcompania = $comp[1] . ' - ' . $comp[2];
}
$ruta = $comp[3];
$usuario = $_SESSION['usuario'];
#CREACION PDF, HEAD AND FOOTER

class PDF extends FPDF {

    function Header() {

        global $fecha1;
        global $fecha2;
        global $cuentaI;
        global $cuentaF;
        global $nombreCompania;
        global $nitcompania;
        global $numpaginas;
        $numpaginas = $this->PageNo();
        global $ruta;
        if ($ruta != '') {
            $this->Image('../' . $ruta, 60,6,25);
        }

        $this->SetFont('Arial', 'B', 10);
        $this->SetY(10);

        $this->SetX(25);
        $this->Cell(320, 5, utf8_decode($nombreCompania), 0, 0, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 8);
        $this->SetX(25);
        $this->Cell(320, 5, $nitcompania, 0, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Ln(4);
        $this->SetX(25);
        $this->Cell(320, 5, utf8_decode('AUXILIAR CONTABLE DE  RETENCIONES'), 0, 0, 'C');
        $this->Ln(4);

        $this->SetFont('Arial', '', 7);
        $this->SetX(25);
        $this->Cell(320, 5, utf8_decode('Cuentas ' . $cuentaI . ' y ' . $cuentaF), 0, 0, 'C');

        $this->Ln(3);

        $this->SetFont('Arial', '', 7);
        $this->SetX(25);
        $this->Cell(320, 5, utf8_decode('entre Fechas ' . $fecha1 . ' y ' . $fecha2), 0, 0, 'C');

        $this->Ln(8);

        $this->SetX(10);
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(80, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(100, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');

        $this->SetX(10);

        $this->Cell(30, 6, utf8_decode('Tipo'), 0, 0, 'C');
        $this->Cell(30, 6, utf8_decode('Número'), 0, 0, 'C');
        $this->Cell(30, 9, utf8_decode('Fecha'), 0, 0, 'C');
        $this->Cell(80, 9, utf8_decode('Nombre Tercero'), 0, 0, 'C');
        $this->Cell(100, 9, utf8_decode('Descipción'), 0, 0, 'C');
        $this->Cell(30, 6, utf8_decode('Valor'), 0, 0, 'C');
        $this->Cell(30, 6, utf8_decode('Base'), 0, 0, 'C');

        $this->Ln(4);

        $this->SetX(10);

        $this->Cell(30, 4, utf8_decode('CXP'), 0, 0, 'C');
        $this->Cell(30, 4, utf8_decode('CXP'), 0, 0, 'C');
        $this->Cell(30, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(80, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(100, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(30, 4, utf8_decode('Retención'), 0, 0, 'C');
        $this->Cell(30, 4, utf8_decode('Gravable'), 0, 0, 'C');

        $this->Ln(5);
    }

    function Footer() {
        // Posición: a 1,5 cm del final
        global $hoy;
        global $usuario;
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 8);
        $this->SetX(10);
        $this->Cell(160, 10, utf8_decode('Fecha: ' . date('d/m/Y')), 0, 0, 'L');
        $this->Cell(170, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }

}

$pdf = new PDF('L', 'mm', 'Legal');
$pdf->AddPage();
$pdf->AliasNbPages();
$yp = $pdf->GetY();

###################CONSULTA CUENTAS###########
$ctas = "SELECT DISTINCT 
            c.id_unico, c.codi_cuenta, LOWER(c.nombre)
        FROM 
            gf_detalle_comprobante dc 
        LEFT JOIN 
            gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
        LEFT JOIN 
            gf_retencion r ON r.comprobante = cn.id_unico 
        LEFT JOIN 
            gf_tipo_retencion tc ON r.tiporetencion = tc.id_unico 
        LEFT JOIN 
            gf_cuenta c ON tc.cuenta = c.id_unico 
        WHERE 
            cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND c.codi_cuenta BETWEEN '$cuentaI' AND '$cuentaF' 
            AND c.parametrizacionanno = $para 
            AND cn.parametrizacionanno = $para 
        ORDER BY c.codi_cuenta ASC";
$ctas = $mysqli->query($ctas);
if (mysqli_num_rows($ctas) > 0) {
    $totaltt =0;
    while ($row = mysqli_fetch_row($ctas)) {
        
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->SetX(10);
        $pdf->Cell(330, 5, utf8_decode('Cuenta Retención: ' . $row[1] . ' - ' . ucwords($row[2])), 1, 0);
        $pdf->Ln(5);
        #############BUSCAR ORDENES DE PAGO QUE TIENEN RETENCION Y ESA CUENTA##########
        $cp = "SELECT DISTINCT cn.id_unico, 
           tc.sigla, cn.numero, 
           DATE_FORMAT(cn.fecha, '%d/%m/%Y'), 
            r.valorretencion, r.retencionbase , 
            IF(CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) 
            IS NULL OR CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) = '',
            (tr.razonsocial),
            CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)) AS NOMBRE , cn.descripcion, r.id_unico 
            FROM gf_comprobante_cnt cn 
            LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante 
            LEFT JOIN gf_retencion r ON r.comprobante = cn.id_unico 
            LEFT JOIN gf_tipo_retencion tret ON r.tiporetencion = tret.id_unico 
            LEFT JOIN gf_detalle_comprobante dc ON dc.comprobante = cn.id_unico 
            LEFT JOIN gf_tercero tr ON tr.id_unico = cn.tercero 
            WHERE tret.cuenta = $row[0] AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND cn.parametrizacionanno = $para 
            ORDER BY tc.sigla, cn.numero, cn.fecha  ";
        $cp = $mysqli->query($cp);
        if (mysqli_num_rows($cp) > 0) {
            $subValor =0;
            $subBase =0;
            while ($row1 = mysqli_fetch_row($cp)) {
                $alt = $pdf->GetY();
                if($alt>180){
                    $pdf->AddPage();
                }
                $cntTercero = ucwords(mb_strtolower($row1[6]));
                $descripcion = ucwords(mb_strtolower($row1[7]));
                $cntTipo = mb_strtoupper($row1[1]);
                $cntN = $row1[2];
                $cntFecha = $row1[3];
                $valor = $row1[4];
                $base = $row1[5];
                $paginactual = $numpaginas;
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetX(10);
                $ypr = $pdf->GetY();
                $pdf->Cell(90, 4, utf8_decode(' '), 0, 0, 'C');
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(80, 4, utf8_decode($cntTercero), 0, 'J');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                $px = $x + 80;
                if ($numpaginas > $paginactual) {
                    $pdf->SetXY($px, $yp);
                    $h = $y2 - $yp;
                } else {
                    $pdf->SetXY($px, $y);
                }

                $x2 = $pdf->GetX();
                $y2 = $pdf->GetY();
                $pdf->MultiCell(100, 4, utf8_decode($descripcion), 0, 'J');
                $y22 = $pdf->GetY();
                $h2 = $y22 - $y2;
                $px2 = $x2 + 100;
                if ($numpaginas > $paginactual) {
                    $pdf->SetXY($px2, $yp);
                    $h2 = $y22 - $yp;
                } else {
                    $pdf->SetXY($px2, $y2);
                }
                $alto = max($h, $h2);
                if ($numpaginas > $paginactual) {
                    $pdf->SetY($yp);
                } else {
                    $pdf->SetY($ypr);
                }
                $pdf->SetX(10);
                $pdf->Cell(30, $alto, utf8_decode($cntTipo), 1, 0, 'C');
                $pdf->Cell(30, $alto, utf8_decode($cntN), 1, 0, 'C');
                $pdf->Cell(30, $alto, utf8_decode($cntFecha), 1, 0, 'C');
                $pdf->Cell(80, $alto, utf8_decode(' '), 1, 0, 'C');
                $pdf->Cell(100, $alto, utf8_decode(' '), 1, 0, 'C');
                $pdf->CellFitScale(30, $alto, utf8_decode('$' . number_format($valor, 2, '.', ',')), 1, 0, 'R');
                $pdf->CellFitScale(30, $alto, utf8_decode('$' . number_format($base, 2, '.', ',')), 1, 0, 'R');
                $pdf->Ln($alto);
                $subValor = $subValor + $valor;
                $subBase = $subBase + $base;
            }
        }
        #SUBTOTALES
        $pdf->SetX(10);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(270, 5, utf8_decode('TOTAL : '), 1, 0, 'R');
        $pdf->CellFitScale(30, 5, utf8_decode('$' . number_format($subValor, 2, '.', ',')), 1, 0, 'R');
        $pdf->CellFitScale(30, 5, utf8_decode('$' . number_format($subBase, 2, '.', ',')), 1, 0, 'R');
        $totalV = $totalV + $subValor;
        $totalB = $totalB + $subBase;
        $pdf->Ln(5);
    }
    #TOTALES
    $pdf->SetX(10);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(270,10, utf8_decode('TOTAL : '), 1, 0, 'R');
    $pdf->CellFitScale(30, 10, utf8_decode('$' . number_format($totalV, 2, '.', ',')), 1, 0, 'R');
    $pdf->CellFitScale(30, 10, utf8_decode('$' . number_format($totalB, 2, '.', ',')), 1, 0, 'R');
    $pdf->Ln(5);
}


while (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output(0, 'Informe_Auxiliar_Contable_Retenciones (' . date('d/m/Y') . ').pdf', 0);
