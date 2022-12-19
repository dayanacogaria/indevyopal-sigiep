<?php
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';

session_start();
ob_start();
ini_set('max_execution_time', 360);
#************Datos Compañia************#
$usuario  = $_SESSION['usuario'];
$compania = $_SESSION['compania'];
$sqlC = "SELECT 	ter.id_unico,
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
WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];
$ruta_logo   = $rowC[6];

$tipoInforme = $_POST['tipoInforme'];
$fecha1      = $_POST["fechaInicial"];
$fecha2      = $_POST["fechaFinal"];
$fechaI      = fechaC($_POST["fechaInicial"]);
$fechaF      = fechaC($_POST["fechaFinal"]);
if ($tipoInforme == "general") {
    /* ELABORACION TIPO INFORME GENERAL */
    $conceptoInicialFactura = $_POST['conceptoInicialFactura'];
    $conceptoFinalFactura   = $_POST['conceptoFinalFactura'];
    class PDF extends FPDF {
        function Header() {            
            global $fecha1;
            global $fecha2;
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $direccinTer;
            global $telefonoTer;
            global $ruta_logo;
            
            $this->SetY(10);
            if($ruta_logo != '')
            {
              $this->Image('../'.$ruta_logo,60,6,20);
            }
            $this->SetFont('Arial', 'B', 10);
            $this->SetY(10);
            $this->SetX(25);
            $this->Cell(315, 5, utf8_decode($razonsocial), 0, 0, 'C');
            $this->Ln(5);
            $this->SetX(25);
            $this->Cell(315, 5, $nombreIdent . ': ' . $numeroIdent, 0, 0, 'C');
            $this->Ln(5);
            $this->SetX(25);
            $this->Cell(315, 5, utf8_decode('Dirección: '.$direccinTer . '  - Teléfono ' . $telefonoTer), 0, 0, 'C');
            $this->Ln(5);
            $this->SetX(25);
            $this->Cell(315, 5, utf8_decode('LISTADO FACTURACIÓN GENERAL'), 0, 0, 'C');
            $this->Ln(5);
            $this->SetX(25);
            $this->Cell(315, 5, utf8_decode('DEL ' . $fecha1 . ' AL ' . $fecha2), 0, 0, 'C');
            $this->Ln(12);
            $this->SetX(20);
            $this->Cell(37, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(37, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(37, 9, utf8_decode(''), 1, 0, 'C');
            $this->Cell(90, 9, utf8_decode('Detalle'), 1, 0, 'C');
            $this->Cell(90, 9, utf8_decode('Tercero'), 1, 0, 'C');

            $this->Cell(37, 9, utf8_decode(''), 1, 0, 'C');
            $this->SetX(20);

            $this->Cell(37, 9, utf8_decode('Fecha'), 0, 0, 'C');
            $this->Cell(37, 9, utf8_decode('Tipo'), 0, 0, 'C');
            $this->Cell(37, 9, utf8_decode('Número'), 0, 0, 'C');
            $this->Cell(90, 9, utf8_decode(''), 0, 0, 'C');
            $this->Cell(90, 9, utf8_decode(''), 0, 0, 'C');
            $this->Cell(37, 9, utf8_decode('Valor'), 0, 0, 'C');
            $this->Ln(10);
        }

        function Footer() {
            global $hoy;
            global $usuario;
            $this->SetY(-15);
            $this->SetFont('Arial', 'B', 8);
            $this->SetX(10);
            $this->Cell(90, 10, utf8_decode('Fecha: ' . $hoy), 0, 0, 'L');
            $this->Cell(90, 10, utf8_decode('Máquina: ' . gethostname()), 0, 0, 'C');
            $this->Cell(90, 10, utf8_decode('Usuario: ' . strtoupper($usuario)), 0, 0, 'C');
            $this->Cell(65, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
        }
    }
    $pdf = new PDF('L', 'mm', 'Legal');
    $pdf->AddPage();
    $pdf->AliasNbPages();
    
    $pdf->SetFont('Arial', '', 10);
    
    $sqlF = "SELECT f.*, tf.prefijo,  
    DATE_FORMAT(f.fecha_factura,'%d/%m/%Y') AS fechaFacConvertida,
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
        t.apellidodos)) AS NOMBRE 
    FROM gp_factura f
    LEFT JOIN gp_tipo_factura tf ON f.tipofactura=tf.id_unico 
    LEFT JOIN gf_tercero t ON f.tercero=t.id_unico
    WHERE fecha_factura BETWEEN '$fechaI' AND '$fechaF' 
    AND f.tipofactura BETWEEN '$conceptoInicialFactura' and '$conceptoFinalFactura' 
    ORDER BY  f.numero_factura, fechaFacConvertida ASC ";

    $lf = $mysqli->query($sqlF);
    $vt  =0;
    while ($f = mysqli_fetch_array($lf)) {
        $y = $pdf->GetY();
        if($y>180){
            $pdf->AddPage();
        }
        $id_unico_factura = $f['id_unico'];
        //CONSULTAR LOS DETALLES DE LA FACTURA $f Y SUMAR EL VALOR
        $sqldf = "SELECT   SUM(df.valor_total_ajustado) AS totalValor
            FROM gp_detalle_factura df LEFT JOIN gp_factura f   ON df.factura=f.id_unico 
            WHERE f.id_unico='$id_unico_factura'";
        $ldf = $mysqli->query($sqldf);
        $df = mysqli_fetch_array($ldf);

        $pdf->SetX(20);
        $pdf->Cell(37, 4, utf8_decode($f['fechaFacConvertida']), 0, 0, 'L');
        $pdf->Cell(37, 4, utf8_decode($f['prefijo']), 0, 0, 'L');
        $pdf->Cell(37, 4, utf8_decode($f['numero_factura']), 0, 0, 'L');
        $y2 = $pdf->GetY();
        $x2 = $pdf->GetX();
        $pdf->MultiCell(90, 4, utf8_decode(ucwords(mb_strtolower($f['descripcion']))), 0, 'L');
        $y22 = $pdf->GetY();
        $h1 = $y22 - $y2;
        $pdf->SetXY($x2+90,$y2);
        $y1 = $pdf->GetY();
        $x1 = $pdf->GetX();
        $pdf->MultiCell(90, 4, utf8_decode(ucwords(mb_strtolower($f['NOMBRE']))), 0, 'L');
        $y2 = $pdf->GetY();
        $h = $y2 - $y1;
        $px = $x1 + 90;
        $pdf->SetXY($px,$y1);
        $pdf->Cell(37, 4, utf8_decode(number_format($df['totalValor'], 2, '.', ',')), 0, 0, 'R');
        $alto = max($h, $h1);
        $pdf->Ln($alto);
        $vt +=$df['totalValor'];
    }
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(291, 4, utf8_decode('TOTAL'), 0, 0, 'L');
    $pdf->Cell(37, 4, utf8_decode(number_format($vt, 2, '.', ',')), 0, 0, 'R');
        
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0, 'Informe_Listado_Facturacion_General (' . date('d/m/Y') . ').pdf', 0);
} elseif ($tipoInforme == "detallado") {
        $conceptoInicialDetalle = $_POST['conceptoInicialDetalle'];
        $conceptoFinalDetalle   = $_POST['conceptoFinalDetalle'];
        class PDF extends FPDF {
            function Header() {            
                global $fecha1;
                global $fecha2;
                global $razonsocial;
                global $nombreIdent;
                global $numeroIdent;
                global $direccinTer;
                global $telefonoTer;
                global $ruta_logo;

                $this->SetY(10);
                if($ruta_logo != '')
                {
                  $this->Image('../'.$ruta_logo,60,6,20);
                }
                $this->SetFont('Arial', 'B', 10);
                $this->SetY(10);
                $this->SetX(25);
                $this->Cell(315, 5, utf8_decode($razonsocial), 0, 0, 'C');
                $this->Ln(5);
                $this->SetX(25);
                $this->Cell(315, 5, $nombreIdent . ': ' . $numeroIdent, 0, 0, 'C');
                $this->Ln(5);
                $this->SetX(25);
                $this->Cell(315, 5, utf8_decode('Dirección: '.$direccinTer . '  - Teléfono ' . $telefonoTer), 0, 0, 'C');
                $this->Ln(5);
                $this->SetX(25);
                $this->Cell(315, 5, utf8_decode('LISTADO FACTURACIÓN DETALLADO'), 0, 0, 'C');
                $this->Ln(5);
                $this->SetX(25);
                $this->Cell(315, 5, utf8_decode('DEL ' . $fecha1 . ' AL ' . $fecha2), 0, 0, 'C');
                $this->Ln(12);
                $this->SetFont('Arial', 'B', 8);
                $this->SetX(20);
                $this->Cell(45.7, 9, utf8_decode(''), 1, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode(''), 1, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode(''), 1, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode(''), 1, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode('Impoconsumo'), 1, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode('Ajuste del peso'), 1, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode(''), 1, 0, 'C');
                $this->SetX(20);
                $this->Cell(45.7, 9, utf8_decode('Concepto'), 0, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode('Cantidad'), 0, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode('Valor'), 0, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode('Iva'), 0, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode(''), 0, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode(''), 0, 0, 'C');
                $this->Cell(45.7, 9, utf8_decode('Valor Total'), 0, 0, 'C');
                $this->Ln(10);
            }

            function Footer() {
                global $hoy;
                global $usuario;
                $this->SetY(-15);
                $this->SetFont('Arial', 'B', 8);
                $this->SetX(10);
                $this->Cell(90, 10, utf8_decode('Fecha: ' . $hoy), 0, 0, 'L');
                $this->Cell(90, 10, utf8_decode('Máquina: ' . gethostname()), 0, 0, 'C');
                $this->Cell(90, 10, utf8_decode('Usuario: ' . strtoupper($usuario)), 0, 0, 'C');
                $this->Cell(65, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
            }
        }

        $pdf = new PDF('L', 'mm', 'Legal');
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(50);
        $sqlF = "SELECT f.*, tf.prefijo,   
            DATE_FORMAT(f.fecha_factura,'%d/%m/%Y') AS fechaFacConvertida,
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
            t.apellidodos)) AS NOMBRE 
          FROM gp_factura f
          LEFT JOIN gp_tipo_factura tf ON f.tipofactura=tf.id_unico 
          LEFT JOIN gf_tercero t ON f.tercero=t.id_unico
          WHERE fecha_factura BETWEEN '$fechaI' AND '$fechaF' ORDER BY  numero_factura  ASC";
        $lf = $mysqli->query($sqlF);
        while ($f = mysqli_fetch_array($lf)) {
            //DETALLES FACTURA
            $factura_id_unico = $f['id_unico'];
            $sqldf = "SELECT  ct.*,
            df.* 
            FROM gp_detalle_factura df
            LEFT JOIN gp_concepto ct ON ct.id_unico=df.concepto_tarifa
            WHERE df.factura='$factura_id_unico' AND df.concepto_tarifa  BETWEEN '$conceptoInicialDetalle' AND '$conceptoFinalDetalle'";
            $ldf = $mysqli->query($sqldf);
            if ($ldf->num_rows > 0) {
                $y = $pdf->GetY();
                if($y>180){
                    $pdf->AddPage();
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetX(20);
                $pdf->Cell(20, 9, utf8_decode($f['numero_factura']), 0, 0, 'L');
                $pdf->Cell(11, 9, utf8_decode($f['prefijo']), 0, 0, 'L');
                $pdf->Cell(100, 9, utf8_decode(ucwords(mb_strtolower($f['NOMBRE']))), 0, 0, 'L');
                $pdf->Cell(25, 9, utf8_decode($f['fechaFacConvertida']), 0, 0, 'L');
                $pdf->Ln(6);
                $tf = 0;
                while ($fdf = mysqli_fetch_array($ldf)) {
                    $y = $pdf->GetY();
                    if($y>180){
                        $pdf->AddPage();
                    }
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetX(20);
                    $pdf->Cell(45.7, 4, utf8_decode($fdf['nombre']), 0, 0, 'L');
                    $pdf->Cell(45.7, 4, utf8_decode($fdf['cantidad']), 0, 0, 'C');
                    $pdf->Cell(45.7, 4, utf8_decode(number_format($fdf['valor'], 2, '.', ',')), 0, 0, 'R');
                    $pdf->Cell(45.7, 4, utf8_decode(number_format($fdf['iva'], 2, '.', ',')), 0, 0, 'R');
                    $pdf->Cell(45.7, 4, utf8_decode(number_format($fdf['impoconsumo'], 2, '.', ',')), 0, 0, 'R');
                    $pdf->Cell(45.7, 4, utf8_decode(number_format($fdf['ajuste_peso'], 2, '.', ',')), 0, 0, 'R');
                    $pdf->Cell(45.7, 4, utf8_decode(number_format($fdf['valor_total_ajustado'], 2, '.', ',')), 0, 0, 'R');
                    $pdf->Ln(4);
                    $tf += $fdf['valor_total_ajustado'];
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetX(20);
                $pdf->Cell(274.2, 4, utf8_decode('Total Factura: '.$f['numero_factura']), 0, 0, 'R');
                $pdf->Cell(45.7, 4, utf8_decode(number_format($tf, 2, '.', ',')), 0, 0, 'R');
                $pdf->Ln(4);
            }
        }

        while (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output(0, 'Informe_Listado_Facturacion_Detallado (' . date('d/m/Y') . ').pdf', 0);
    } elseif ($tipoInforme == "concepto") {
        /* ELABORACION TIPO INFORME CONCEPTO */
        $conceptoInicial = $_POST['conceptoInicial'];
        $conceptoFinal = $_POST['conceptoFinal'];
        class PDF extends FPDF {
            function Header() {            
                global $fecha1;
                global $fecha2;
                global $razonsocial;
                global $nombreIdent;
                global $numeroIdent;
                global $direccinTer;
                global $telefonoTer;
                global $ruta_logo;

                $this->SetY(10);
                if($ruta_logo != '')
                {
                  $this->Image('../'.$ruta_logo,60,6,20);
                }
                $this->SetFont('Arial', 'B', 10);
                $this->SetY(10);
                $this->SetX(25);
                $this->Cell(315, 5, utf8_decode($razonsocial), 0, 0, 'C');
                $this->Ln(5);
                $this->SetX(25);
                $this->Cell(315, 5, $nombreIdent . ': ' . $numeroIdent, 0, 0, 'C');
                $this->Ln(5);
                $this->SetX(25);
                $this->Cell(315, 5, utf8_decode('Dirección: '.$direccinTer . '  - Teléfono ' . $telefonoTer), 0, 0, 'C');
                $this->Ln(5);
                $this->SetX(25);
                $this->Cell(315, 5, utf8_decode('LISTADO FACTURACIÓN POR CONCEPTO'), 0, 0, 'C');
                $this->Ln(5);
                $this->SetX(25);
                $this->Cell(315, 5, utf8_decode('DEL ' . $fecha1 . ' AL ' . $fecha2), 0, 0, 'C');
                $this->Ln(12);
                $this->SetFont('Arial', 'B', 8);
                $this->SetX(20);
                $this->Cell(37, 9, utf8_decode(''), 1, 0, 'C');
                $this->Cell(37, 9, utf8_decode(''), 1, 0, 'C');
                $this->Cell(37, 9, utf8_decode(''), 1, 0, 'C');
                $this->Cell(90, 9, utf8_decode('Descripción'), 1, 0, 'C');
                $this->Cell(90, 9, utf8_decode('Tercero'), 1, 0, 'C');
                $this->Cell(37, 9, utf8_decode(''), 1, 0, 'C');
                $this->SetX(20);
                $this->Cell(37, 9, utf8_decode('Número'), 0, 0, 'C');
                $this->Cell(37, 9, utf8_decode('Tipo'), 0, 0, 'C');
                $this->Cell(37, 9, utf8_decode('Fecha'), 0, 0, 'C');
                $this->Cell(90, 9, utf8_decode(''), 0, 0, 'C');
                $this->Cell(90, 9, utf8_decode(''), 0, 0, 'C');
                $this->Cell(37, 9, utf8_decode('Valor'), 0, 0, 'C');

                $this->Ln(10);
            }

            function Footer() {
                global $hoy;
                global $usuario;
                $this->SetY(-15);
                $this->SetFont('Arial', 'B', 8);
                $this->SetX(10);
                $this->Cell(90, 10, utf8_decode('Fecha: ' . $hoy), 0, 0, 'L');
                $this->Cell(90, 10, utf8_decode('Máquina: ' . gethostname()), 0, 0, 'C');
                $this->Cell(90, 10, utf8_decode('Usuario: ' . strtoupper($usuario)), 0, 0, 'C');
                $this->Cell(65, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
            }
        }
        $pdf = new PDF('L', 'mm', 'Legal');
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX(50);
        $yp = $pdf->GetY();
        $sqlf = "SELECT f.*,df.concepto_tarifa,df.valor,tf.prefijo,ct.nombre AS nombreConceptoTarifa, 
                DATE_FORMAT(f.fecha_factura,'%d/%m/%Y') AS fechaFacConvertida,

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
                t.apellidodos)) AS NOMBRE 
        FROM gp_factura f 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura=tf.id_unico
        LEFT JOIN gf_tercero t ON f.tercero=t.id_unico
        LEFT JOIN gp_detalle_factura df ON  df.factura=f.id_unico 
        LEFT JOIN gp_concepto ct ON df.concepto_tarifa=ct.id_unico 
        WHERE ct.id_unico BETWEEN '$conceptoInicial' AND '$conceptoFinal' AND fecha_factura BETWEEN '$fechaI' AND '$fechaF'
        ORDER BY  ct.id_unico  ASC";
        $lf = $mysqli->query($sqlf);
        while ($f = mysqli_fetch_array($lf)) {
            $y = $pdf->GetY();
            if($y>180){
                $pdf->AddPage();
            }
            if ($conceptoTarifa != $f['nombreConceptoTarifa']) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetX(20);
                $pdf->Cell(18, 4, utf8_decode($f['nombreConceptoTarifa']), 0, 0, 'L');
                $pdf->Ln(6);
                $conceptoTarifa = $f['nombreConceptoTarifa'];
            }
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetX(20);
            $pdf->Cell(37, 4, utf8_decode($f['numero_factura']), 0, 0, 'L');
            $pdf->Cell(37, 4, utf8_decode($f['prefijo']), 0, 0, 'L'); //tipo
            $pdf->Cell(37, 4, utf8_decode($f['fechaFacConvertida']), 0, 0, 'L');
            $y2 = $pdf->GetY();
            $x2 = $pdf->GetX();
            $pdf->MultiCell(90, 4, utf8_decode(ucwords(mb_strtolower($f['descripcion']))), 0, 'L');
            $y22 = $pdf->GetY();
            $h1 = $y22 - $y2;
            $px2 = $x2 + 90;
            $pdf->SetXY($x2+90, $y2);
            $y1 = $pdf->GetY();
            $x1 = $pdf->GetX();
            $pdf->MultiCell(90, 4, utf8_decode(ucwords(mb_strtolower($f['NOMBRE']))), 0, 'L');
            $y2 = $pdf->GetY();
            $h = $y2 - $y1;
            $px = $x1 + 90;
            $pdf->SetXY($x1+90, $y1);
            $pdf->Cell(37, 4, utf8_decode(number_format($f['valor'], 2, '.', ',')), 0, 0, 'R'); //valor del detalle
            $alto = max($h, $h1);
            $pdf->Ln($alto);
            }


            $pdf->Output(0, 'Informe_Listado_Facturacion_Concepto (' . date('d/m/Y') . ').pdf', 0);
        } else {

            /* ELABORACION TIPO INFORME TERCERO */

            $fechaini = $mysqli->real_escape_string('' . $_POST["fechaInicial"] . '');
            $fechafin = $mysqli->real_escape_string('' . $_POST["fechaFinal"] . '');

            //Conversion fecha para consulta sql

            $fechaI = DateTime::createFromFormat('d/m/Y', "$fechaini");
            $fechaI = $fechaI->format('Y/m/d');


            $fechaF = DateTime::createFromFormat('d/m/Y', "$fechafin");
            $fechaF = $fechaF->format('Y/m/d');


            #Conversión Fecha para Cabecera pdf
            $fecha1 = $fechaini;
            $fecha1 = trim($fecha1, '"');
            $fecha_div = explode("/", $fecha1);
            $dia1 = $fecha_div[0];
            $mes1 = $fecha_div[1];
            $anio1 = $fecha_div[2];
            $fecha1 = $dia1 . '/' . $mes1 . '/' . $anio1;

            $fecha2 = $fechafin;
            $fecha2 = trim($fecha2, '"');
            $fecha_div = explode("/", $fecha2);
            $dia2 = $fecha_div[0];
            $mes2 = $fecha_div[1];
            $anio2 = $fecha_div[2];
            $fecha2 = $dia2 . '/' . $mes2 . '/' . $anio2;


            //Fin Conversión Fecha / Hora
            $hoy = date('d-m-Y');
            $hoy = trim($hoy, '"');
            $fecha_div = explode("-", $hoy);
            $anioh = $fecha_div[2];
            $mesh = $fecha_div[1];
            $diah = $fecha_div[0];
            $hoy = $diah . '/' . $mesh . '/' . $anioh;

            class PDF extends FPDF {

                // Cabecera de página
                function Header() {

                    // Logo
                    //$this->Image('logo_pb.png',10,8,33);
                    //Arial bold 15
                    global $nomcomp;
                    global $tipodoc;
                    global $numdoc;

                    global $fecha1;
                    global $fecha2;




                    global $numpaginas;
                    $numpaginas = $numpaginas + 1;

                    $this->SetFont('Arial', 'B', 10);
                    //$this->Ln(1);
                    // Título
                    $this->SetY(10);
                    //$this->image('../LOGOABC.png', 20,10,20,15,'PNG');
                    //$pdf->SetFillColor(232,232,232);

                    $this->SetX(25);
                    $this->Cell(315, 5, utf8_decode($nomcomp), 0, 0, 'C');
                    // Salto de línea
                    $this->setX(25);
                    $this->SetFont('Arial', 'B', 8);
                    $this->Cell(315, 10, utf8_decode('CÓDIGO SGC'), 0, 0, 'R');

                    $this->Ln(5);

                    $this->SetFont('Arial', '', 8);
                    $this->SetX(25);
                    $this->Cell(315, 5, $tipodoc . ': ' . $numdoc, 0, 0, 'C');
                    $this->SetFont('Arial', 'B', 8);
                    $this->SetX(25);
                    $this->Cell(315, 10, utf8_decode('VERSIÓN SGC'), 0, 0, 'R');

                    $this->Ln(5);

                    $this->SetFont('Arial', '', 8);
                    $this->SetX(25);
                    $this->Cell(315, 5, utf8_decode('LISTADO FACTURACIÓN'), 0, 0, 'C');
                    $this->SetFont('Arial', 'B', 8);
                    $this->SetX(25);
                    $this->Cell(315, 10, utf8_decode('FECHA SGC'), 0, 0, 'R');

                    $this->Ln(3);



                    $this->SetFont('Arial', '', 7);
                    $this->SetX(25);
                    $this->Cell(315, 5, utf8_decode('entre Fechas ' . $fecha1 . ' y ' . $fecha2), 0, 0, 'C');

                    $this->Ln(12);

                    $this->SetFont('Arial', 'B', 8);
                    $this->SetX(20);
                    $this->Cell(60, 9, utf8_decode(''), 1, 0, 'C');
                    $this->Cell(60, 9, utf8_decode(''), 1, 0, 'C');
                    $this->Cell(60, 9, utf8_decode(''), 1, 0, 'C');
                    $this->Cell(90, 9, utf8_decode('Descripción'), 1, 0, 'C');
                    $this->Cell(60, 9, utf8_decode('Valor'), 1, 0, 'C');



                    $this->SetX(20);

                    $this->Cell(60, 9, utf8_decode('Número'), 0, 0, 'C');
                    $this->Cell(60, 9, utf8_decode('Tipo'), 0, 0, 'C');
                    $this->Cell(60, 9, utf8_decode('Fecha'), 0, 0, 'C');

                    $this->Cell(90, 9, utf8_decode(''), 0, 0, 'C');
                    $this->Cell(90, 9, utf8_decode(''), 0, 0, 'C');


                    $this->Ln(4);

                    $this->SetX(55);



                    $this->Ln(6);
                }

                // Pie de página
                function Footer() {
                    // Posición: a 1,5 cm del final
                    global $hoy;
                    global $usuario;
                    $this->SetY(-15);
                    // Arial italic 8
                    $this->SetFont('Arial', 'B', 8);
                    $this->SetX(10);
                    $this->Cell(90, 10, utf8_decode('Fecha: ' . $hoy), 0, 0, 'L');
                    $this->Cell(90, 10, utf8_decode('Máquina: ' . gethostname()), 0, 0, 'C');
                    $this->Cell(90, 10, utf8_decode('Usuario: ' . strtoupper($usuario)), 0, 0, 'C');
                    $this->Cell(65, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
                }

            }

            // Creación del objeto de la clase heredada
            $pdf = new PDF('L', 'mm', 'Legal');



            $fechauno = $fechaini;
            $fechados = $fechafin;

            $compania = $_SESSION['compania'];
            $usuario = $_SESSION['usuario'];
            $terceroInicial = $_POST['terceroInicial'];
            $terceroFinal = $_POST['terceroFinal'];

            $consulta = "SELECT         t.razonsocial as traz,
                                            t.tipoidentificacion as tide,
                                            ti.id_unico as tid,
                                            ti.nombre as tnom,
                                            t.numeroidentificacion tnum
                            FROM gf_tercero t
                            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
                            WHERE t.id_unico = $compania";

            $cmp = $mysqli->query($consulta);

            $nomcomp = "";
            $tipodoc = "";
            $numdoc = "";

            while ($fila = mysqli_fetch_array($cmp)) {
                $nomcomp = utf8_decode($fila['traz']);
                $tipodoc = utf8_decode($fila['tnom']);
                $numdoc = utf8_decode($fila['tnum']);
            }


            $pdf->AddPage();
            $pdf->AliasNbPages();
            $pdf->SetFont('Arial', 'B', 10);

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(50);
            $yp = $pdf->GetY();


            $codd = 0;
            $totales = 0;
            $valorA = 0;


            //LOGO
            $sqlRutaLogo = 'SELECT ter.ruta_logo, ciu.nombre 
                  FROM gf_tercero ter 
                  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
                  WHERE ter.id_unico = ' . $compania;
            $rutaLogo = $mysqli->query($sqlRutaLogo);
            $rowLogo = mysqli_fetch_array($rutaLogo);
            $ruta = $rowLogo[0];
            if ($ruta != '') {
                $pdf->Image('../' . $ruta, 30, 8, 20);
            }



            $totalValorFacturas = 0;

            $sqlTerceros = "SELECT t.*, 
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
                                        t.apellidodos)) AS NOMBRE 
                                        
                                        FROM gf_tercero t WHERE t.id_unico BETWEEN '$terceroInicial' AND '$terceroFinal'";

            $lterceros = $mysqli->query($sqlTerceros);

            while ($tercero = mysqli_fetch_array($lterceros)) {

                $id_unicoTercero = $tercero['id_unico'];

                $sqlFacturas = "SELECT f.*, tf.prefijo,
                                        DATE_FORMAT(f.fecha_factura,'%d/%m/%Y') AS fechaFacConvertida

 

                                      FROM gp_factura f
                                      LEFT JOIN gp_tipo_factura tf ON f.tipofactura=tf.id_unico 
                                      LEFT JOIN gf_tercero t ON f.tercero=t.id_unico
                                      WHERE fecha_factura BETWEEN '$fechaI' AND '$fechaF' AND f.tercero='$id_unicoTercero'
                                      ORDER BY  fecha_factura ASC";

                $lfacturas = $mysqli->query($sqlFacturas);

                if ($lfacturas->num_rows > 0) {

                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->SetX(20);

                    $pdf->Cell(60, 4, "Tercero:" . $tercero['numeroidentificacion'] . " - " . ucwords(mb_strtolower($tercero['NOMBRE'])), 0, 0, 'L'); //tercero
                    $pdf->Ln(3.5);


                    while ($factura = mysqli_fetch_array($lfacturas)) {


                        $paginactual = $numpaginas;


                        $id_unico_factura = $factura['id_unico'];

                        $sqldf = "SELECT   SUM(df.valor_total_ajustado) AS totalValor
                                                   FROM gp_detalle_factura df LEFT JOIN gp_factura f   ON df.factura=f.id_unico 
                                                   WHERE f.id_unico='$id_unico_factura'";

                        $ldf = $mysqli->query($sqldf);

                        $df = mysqli_fetch_array($ldf);

                        $totalValorFacturas += $df['totalValor'];

                        $pdf->SetFont('Arial', '', 8);

                        $pdf->SetX(20);

                        $pdf->Cell(60.3, 4, utf8_decode($factura['numero_factura']), 0, 0, 'L');
                        $pdf->Cell(60.3, 4, utf8_decode($factura['prefijo']), 0, 0, 'L'); //tipo
                        $pdf->Cell(60, 4, utf8_decode($factura['fechaFacConvertida']), 0, 0, 'L');

                        $y1 = $pdf->GetY();
                        $x1 = $pdf->GetX();
                        $pdf->MultiCell(90.3, 4, utf8_decode(ucwords(mb_strtolower($factura['descripcion']))), 0, 'L');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y1;
                        $px = $x1 + 90.3;

                        if ($numpaginas > $paginactual) {
                            $pdf->SetXY($px, $yp);
                            $h = $y2 - $yp;
                        } else {
                            $pdf->SetXY($px, $y1);
                        }

                        // $pdf->Cell(90.3,4,utf8_decode(ucwords(mb_strtolower($factura['descripcion']))),0,0,'L');
                        $pdf->Cell(60.3, 4, utf8_decode(number_format($df['totalValor'], 2, '.', ',')), 0, 0, 'R'); //valor del detalle

                        $alto = max($h, $h1);
                        $pdf->Ln($alto);
                        $paginactual = $numpaginas;
                    }
                }
            }



            if ($totalValorFacturas > 0) {
                $pdf->SetFont('Arial', 'B', 8.5);
                $pdf->Ln(2);
                $pdf->SetX(41.4);
                $pdf->Cell(270, 4, 'TOTALES: ', 0, 0, 'R');
                $pdf->Cell(40, 4, number_format($totalValorFacturas, 2, '.', ','), 0, 0, 'R');
            }//FIN TERCERO




            while (ob_get_length()) {
                ob_end_clean();
            }




            $pdf->Output(0, 'Informe_Listado_Facturacion_Tercero (' . date('d/m/Y') . ').pdf', 0);
        
    
}
?>
