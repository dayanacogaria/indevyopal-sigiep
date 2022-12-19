<?php

require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
ini_set('max_execution_time', 0);
session_start();
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
//Datos de compañia
$rowC = $con->Listar
        ("
SELECT ter.id_unico,
    ter.razonsocial,
    UPPER(ti.nombre),
    ter.numeroidentificacion,
    dir.direccion,
    tel.valor,
    ter.ruta_logo,
    IF(CONCAT_WS(' ',
    ter.nombreuno,
    ter.nombredos,
    ter.apellidouno,
    ter.apellidodos)
    IS NULL OR CONCAT_WS(' ',
    ter.nombreuno,
    ter.nombredos,
    ter.apellidouno,
    ter.apellidodos) = '',
    (ter.razonsocial),
    CONCAT_WS(' ',
    ter.nombreuno,
    ter.nombredos,
    ter.apellidouno,
    ter.apellidodos)) AS NOMBRE
FROM gf_tercero ter
    LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
    LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
    LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania
");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][7];
$telefonoTer = $rowC[0][5];
$ruta_logo = $rowC[0][6];

// Datos de usuario1
$usuarioI = $_REQUEST['stlUsuarioI'];
$dataUsuarioI = $con->Listar("
SELECT
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
t.apellidodos)) AS NOMBRE,
IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
t.numeroidentificacion,
CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))  as NUMI,
u.id_unico as id_usuario,
t.id_unico as id_tercero
FROM gs_usuario u
INNER JOIN gf_tercero t ON u.tercero = t.id_unico
WHERE u.id_unico = $usuarioI"
);
$nombreusuarioI = $dataUsuarioI[0][0];
$idterceroI = $dataUsuarioI[0][3];


//Datos Usuario2
$usuarioF = $_REQUEST['stlUsuarioF'];
$dataUsuarioF = $con->Listar("
SELECT
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
t.apellidodos)) AS NOMBRE,
IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
t.numeroidentificacion,
CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))  as NUMI,
u.id_unico as id_usuario,
t.id_unico as id_tercero
FROM gs_usuario u
INNER JOIN gf_tercero t ON u.tercero = t.id_unico
WHERE u.id_unico = $usuarioF"
);
$nombreusuarioF = $dataUsuarioF[0][0];
$idterceroF = $dataUsuarioF[0][3];
$ff = explode("/", $_REQUEST['fechaI']);
$xx = explode(" ", $ff[2]);
$fechaI = substr(trim($xx[0]) . "-$ff[1]-$ff[0] $xx[1]:00", 0, 10);
$fechaIh = $fechaI . " " . substr($_REQUEST['fechaI'], 11, 5);
$ff = explode("/", $_REQUEST['fechaF']);
$xx = explode(" ", $ff[2]);
$fechaF = substr(trim($xx[0]) . "-$ff[1]-$ff[0] $xx[1]:00", 0, 10);
$fechaFh = $fechaF . " " . substr($_REQUEST['fechaF'], 11, 5) . ":00";
$salida = $_REQUEST['salida'];
$tipo = $_REQUEST['tipo'];
$orderby = $_REQUEST['stlorder'];
if ($orderby == 1){
    $orderby = "ORDER BY 1";
}else if ($orderby == 2){
    $orderby = "ORDER BY 4";
}else {
    $orderby = "ORDER BY 1";
}

    switch ($tipo){
        case 1:
        # *** Generar Pdf **#
            require'../fpdf/fpdf.php';
            ob_start();

            class PDF extends FPDF {

                function Header() {
                    global $razonsocial;
                    global $nombreIdent;
                    global $numeroIdent;
                    global $direccinTer;
                    global $telefonoTer;
                    global $ruta_logo;
                    global $numpaginas;
                    global $fechaI;
                    global $fechaF;
                    global $nanno;
                    global $nombreusuarioI;
                    global $nombreusuarioF;
                    $numpaginas = $numpaginas + 1;

                    $this->SetFont('Arial', 'B', 10);

                    if ($ruta_logo != '') {
                        $this->Image('../' . $ruta_logo, 10, 5, 28);
                    }
                    $this->SetFont('Arial', 'B', 10);
                    $this->MultiCell(260, 5, utf8_decode($razonsocial), 0, 'C');
                    $this->SetX(10);
                    $this->Ln(1);
                    $this->Cell(260, 5, utf8_decode($nombreIdent . ': ' . $numeroIdent), 0, 0, 'C');
                    $this->ln(5);
                    $this->SetX(10);
                    $this->Cell(260, 5, utf8_decode('INFORME DE INGRESOS PARQUEADERO'), 0, 0, 'C');
                    $this->Ln(5);
                    $this->Cell(260, 5, utf8_decode('DEL ' . $_REQUEST['fechaI'] . ' AL ' . $_REQUEST['fechaF']), 0, 0, 'C');
                    $this->Ln(5);
                    $this->Cell(260, 5, utf8_decode($nombreusuarioI).' - '. utf8_decode($nombreusuarioF), 0, 0, 'C');
                    $this->Ln(5);
                    $this->SetX(10);
                    $this->Ln(5);
                }

                function Footer() {
                    $this->SetY(-30);
                    $this->SetFont('Arial', 'B', 8);
                    $this->SetX(1);
                    $this->Cell(260, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
                }

            }
            
            $pdf = new PDF('L', 'mm', array(220, 260));
            $nb = $pdf->AliasNbPages();            
            if ($salida == 0){ // Trae ingresos SIN factura   
                $orderby = "ORDER BY 1";
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetXY(10, 41);
                $pdf->Cell(244, 10, utf8_decode('INGRESOS SIN SALIDA'), 1, 0, 'C');
                $pdf->Ln(10);
                $pdf->SetX(10);
                $pdf->Cell(60, 5, utf8_decode('Fecha'), 1, 0, 'C');
                $pdf->Cell(30, 5, utf8_decode('Ingreso'), 1, 0, 'C');
                $pdf->Cell(38, 5, utf8_decode('Placa'), 1, 0, 'C');
                $pdf->Cell(75, 5, utf8_decode('Tarifa'), 1, 0, 'C');
                $pdf->Cell(41, 5, utf8_decode('Vehiculo'), 1, 0, 'C');
                $pdf->Ln(5);
                $pdf->SetFont('Arial', '', 10);                
                // 1. Cargar los ingresos SIN factura donde la fecha del INGRESO sea menor a la Fecha Final del formulario
                $sqlCn = "SELECT
                            CONCAT(DATE_FORMAT(mov.fecha,'%d/%m/%Y'),' ',CASE
                            WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                            ELSE mov.hora
                            END) as fecha,  
                            mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i %p'), fra.nombre, fra.valor, tv.nombre
                            FROM gq_ingreso_parqueadero mov
                            LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico
                            LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico
                            LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico
                            WHERE
                            CONCAT(mov.fecha,' ',
                                CASE
                                    WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',SUBSTRING(mov.hora,1,4))
                                    ELSE SUBSTRING(mov.hora,1,5)
                                END) <= '$fechaFh'
                            AND mov.factura IS NULL $orderby";
                $resc = $mysqli->query($sqlCn);
                while ($row2 = mysqli_fetch_row($resc)) {
                    $pdf->Cell(60, 5, utf8_decode($row2[0]), 1, 0, 'L');
                    $pdf->Cell(30, 5, utf8_decode($row2[1]), 1, 0, 'R');
                    $pdf->Cell(38, 5, utf8_decode(strtoupper($row2[2])), 1, 0, 'L');
                    $pdf->Cell(75, 5, utf8_decode($row2[4] . ' (' . number_format($row2[5], 2, '.', ',') . ')'), 1, 0, 'L');
                    $pdf->Cell(41, 5, utf8_decode($row2[6]), 1, 0, 'L');
                    $pdf->Ln();
                    $f = $pdf->GetY();
                    if ($f >= 170){
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(10, 41);
                        $pdf->Cell(244, 10, utf8_decode('INGRESOS SIN SALIDA'), 1, 0, 'C');
                        $pdf->Ln(10);
                        $pdf->SetX(10);
                        $pdf->Cell(60, 5, utf8_decode('Fecha'), 1, 0, 'C');
                        $pdf->Cell(30, 5, utf8_decode('Ingreso'), 1, 0, 'C');
                        $pdf->Cell(38, 5, utf8_decode('Placa'), 1, 0, 'C');
                        $pdf->Cell(75, 5, utf8_decode('Tarifa'), 1, 0, 'C');
                        $pdf->Cell(41, 5, utf8_decode('Vehiculo'), 1, 0, 'C');
                        $pdf->Ln(5);
                        $pdf->SetFont('Arial', '', 10);
                    }
                }

                // 2. Cargar los ingresos donde la fecha del SALIDA sea mayor a la Fecha Final del formulario
                $sqlCn = "SELECT
                            WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                            ELSE mov.hora
                            END) as fecha,  
                            mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i %p'), fra.nombre, fra.valor, tv.nombre
                            FROM gq_ingreso_parqueadero mov
                            LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico
                            LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico
                            LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico
                            WHERE
                            CONCAT(mov.fecha,' ',
                                    CASE
                                    WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',SUBSTRING(mov.hora,1,4))
                                    ELSE SUBSTRING(mov.hora,1,5)
                                END) <= '$fechaFh'
                            AND SUBSTRING(mov.salida,1,16) > '$fechaFh'
                            $orderby1";
                $resc = $mysqli->query($sqlCn);
                while ($row2 = mysqli_fetch_row($resc)) {
                    $pdf->Cell(60, 5, utf8_decode($row2[0]), 1, 0, 'L');
                    $pdf->Cell(30, 5, utf8_decode($row2[1]), 1, 0, 'R');
                    $pdf->Cell(38, 5, utf8_decode(strtoupper($row2[2])), 1, 0, 'L');
                    $pdf->Cell(75, 5, utf8_decode($row2[4] . ' (' . number_format($row2[5], 2, '.', ',') . ')'), 1, 0, 'L');
                    $pdf->Cell(41, 5, utf8_decode($row2[6]), 1, 0, 'L');
                    $pdf->Ln();
                    $f = $pdf->GetY();
                    if ($f >= 170){
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(10, 41);
                        $pdf->Cell(244, 10, utf8_decode('INGRESOS SIN SALIDA'), 1, 0, 'C');
                        $pdf->Ln(10);
                        $pdf->SetX(10);
                        $pdf->Cell(60, 5, utf8_decode('Fecha'), 1, 0, 'C');
                        $pdf->Cell(30, 5, utf8_decode('Ingreso'), 1, 0, 'C');
                        $pdf->Cell(38, 5, utf8_decode('Placa'), 1, 0, 'C');
                        $pdf->Cell(75, 5, utf8_decode('Tarifa'), 1, 0, 'C');
                        $pdf->Cell(41, 5, utf8_decode('Vehiculo'), 1, 0, 'C');
                        $pdf->Ln(5);
                        $pdf->SetFont('Arial', '', 10);
                    }
                }
                
            } else if ($salida == 1){ // Trae ingresos CON factura                
                $nb = $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetXY(10, 41);
                $pdf->Cell(244, 10, utf8_decode('INGRESOS CON SALIDA'), 1, 0, 'C');
                $pdf->Ln(10);
                $pdf->SetX(10);
                $pdf->Cell(41, 5, utf8_decode('Fecha'), 1, 0, 'C');
                $pdf->Cell(14, 5, utf8_decode('Ingreso'), 1, 0, 'C');
                $pdf->Cell(18, 5, utf8_decode('Placa'), 1, 0, 'C');
                $pdf->Cell(41, 5, utf8_decode('Salida'), 1, 0, 'C');
                $pdf->Cell(50, 5, utf8_decode('Tarifa'), 1, 0, 'C');
                $pdf->Cell(20, 5, utf8_decode('Vehiculo'), 1, 0, 'C');
                $pdf->Cell(22, 5, utf8_decode('Factura'), 1, 0, 'C');
                $pdf->Cell(15, 5, utf8_decode('Tiempo'), 1, 0, 'C');
                $pdf->Cell(23, 5, utf8_decode('Valor'), 1, 0, 'C');
                $pdf->Ln(5);
                $total = 0;
                $pdf->SetFont('Arial', '', 10);
                /* $sqlCn1 Trae los ingresos con factura donde la fecha del ingreso este entre las fechas del informe */
                $sqlCn1 = "SELECT CONCAT(DATE_FORMAT(mov.fecha,'%d/%m/%Y'),' ',CASE
                    WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                    ELSE mov.hora
                    END) as fecha,   
                    mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i:%s %p'), fra.nombre, fra.valor, fac.numero_factura, df.valor_total_ajustado, dp.valor, dp.iva, dp.impoconsumo, dp.ajuste_peso, tv.nombre,
                    CONCAT(mov.fecha,' ',SUBSTRING(ltrim(mov.hora),1,5))as fecha, SUBSTRING(salida,1,16), mov.tiempo
                    FROM gq_ingreso_parqueadero mov
                    LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico                
                    LEFT JOIN gp_detalle_factura df ON fac.id_unico = df.factura
                    LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura
                    LEFT JOIN gp_pago pg ON dp.pago = pg.id_unico
                    LEFT JOIn gph_espacio_habitable_propiedad_relacionada sphp ON fac.id_espacio_habitable = sphp.id_unico
                    LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico     
                    LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico           
                    WHERE 
                    SUBSTRING(mov.salida,1,16) BETWEEN '$fechaIh' AND '$fechaFh' 
                    AND mov.factura IS NOT NULL 
                    AND dp.detalle_factura IS NOT NULL
                    AND pg.usuario BETWEEN $idterceroI AND $idterceroF
                    $orderby";
                $resc1 = $mysqli->query($sqlCn1);            
                while ($row1 = mysqli_fetch_row($resc1)) {
                    $subtotal = $row1[8] + $row1[9] + $row1[10] + $row1[11];
                    $total += round($subtotal);
                    $pdf->Cell(41, 5, utf8_decode($row1[0]), 1, 0, 'L');
                    $pdf->Cell(14, 5, utf8_decode($row1[1]), 1, 0, 'R');
                    $pdf->Cell(18, 5, utf8_decode(strtoupper($row1[2])), 1, 0, 'L');
                    $pdf->Cell(41, 5, utf8_decode($row1[3]), 1, 0, 'L');                
                    $pdf->Cell(50, 5, utf8_decode($row1[4] . ' (' . number_format($row1[5], 2, '.', ',') . ')'), 1, 0, 'L');
                    $pdf->Cell(20, 5, utf8_decode($row1[12]), 1, 0, 'L');                
                    $pdf->Cell(22, 5, utf8_decode($row1[6]), 1, 0, 'R');                
                    $pdf->Cell(15, 5, utf8_decode($row1[15]), 1, 0, 'R');
                    $pdf->Cell(23, 5, number_format(round($subtotal), 2, '.', ','), 1, 0, 'R');                
                    $pdf->Ln();
                    $f = $pdf->GetY();
                    if ($f >= 170){
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(10, 41);
                        $pdf->Cell(244, 10, utf8_decode('INGRESOS CON SALIDA'), 1, 0, 'C');
                        $pdf->Ln(10);
                        $pdf->SetX(10);
                        $pdf->Cell(41, 5, utf8_decode('Fecha'), 1, 0, 'C');
                        $pdf->Cell(14, 5, utf8_decode('Ingreso'), 1, 0, 'C');
                        $pdf->Cell(18, 5, utf8_decode('Placa'), 1, 0, 'C');
                        $pdf->Cell(41, 5, utf8_decode('Salida'), 1, 0, 'C');
                        $pdf->Cell(50, 5, utf8_decode('Tarifa'), 1, 0, 'C');
                        $pdf->Cell(20, 5, utf8_decode('Vehiculo'), 1, 0, 'C');
                        $pdf->Cell(22, 5, utf8_decode('Factura'), 1, 0, 'C');
                        $pdf->Cell(15, 5, utf8_decode('Tiempo'), 1, 0, 'C');
                        $pdf->Cell(23, 5, utf8_decode('Valor'), 1, 0, 'C');
                        $pdf->Ln(5);
                        $pdf->SetFont('Arial', '', 10);
                    }
                    $subtotal = 0;
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(219, 5, 'TOTAL', 1, 0, 'L');
                $pdf->Cell(25, 5, number_format($total, 2, '.', ','), 1, 0, 'R');
            } else if ($salida ==2){ // Trae todos los ingresos CON factura y SIN factura
                if ($_REQUEST['stlorder'] ==2){
                    $orderby1 = "ORDER BY 1";
                }
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetXY(10, 41);
                $pdf->Cell(244, 10, utf8_decode('INGRESOS SIN SALIDA'), 1, 0, 'C');
                $pdf->Ln(10);
                $pdf->SetX(10);
                $pdf->Cell(60, 5, utf8_decode('Fecha'), 1, 0, 'C');
                $pdf->Cell(30, 5, utf8_decode('Ingreso'), 1, 0, 'C');
                $pdf->Cell(38, 5, utf8_decode('Placa'), 1, 0, 'C');
                $pdf->Cell(75, 5, utf8_decode('Tarifa'), 1, 0, 'C');
                $pdf->Cell(41, 5, utf8_decode('Vehiculo'), 1, 0, 'C');
                $pdf->Ln(5);
                $pdf->SetFont('Arial', '', 10);                
                // 1. Cargar los ingresos SIN factura donde la fecha del INGRESO sea menor a la Fecha Final del formulario
                $sqlCn = "SELECT
                            CONCAT(DATE_FORMAT(mov.fecha,'%d/%m/%Y'),' ',CASE
                            WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                            ELSE mov.hora
                            END) as fecha,  
                            mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i %p'), fra.nombre, fra.valor, tv.nombre
                            FROM gq_ingreso_parqueadero mov
                            LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico
                            LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico
                            LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico
                            WHERE
                            CONCAT(mov.fecha,' ',
                                CASE
                                    WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',SUBSTRING(mov.hora,1,4))
                                    ELSE SUBSTRING(mov.hora,1,5)
                                END) <= '$fechaFh'
                            AND mov.factura IS NULL $orderby";
                $resc = $mysqli->query($sqlCn);
                while ($row2 = mysqli_fetch_row($resc)) {
                    $pdf->Cell(60, 5, utf8_decode($row2[0]), 1, 0, 'L');
                    $pdf->Cell(30, 5, utf8_decode($row2[1]), 1, 0, 'R');
                    $pdf->Cell(38, 5, utf8_decode(strtoupper($row2[2])), 1, 0, 'L');
                    $pdf->Cell(75, 5, utf8_decode($row2[4] . ' (' . number_format($row2[5], 2, '.', ',') . ')'), 1, 0, 'L');
                    $pdf->Cell(41, 5, utf8_decode($row2[6]), 1, 0, 'L');
                    $pdf->Ln();
                    $f = $pdf->GetY();
                    if ($f >= 170){
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(10, 41);
                        $pdf->Cell(244, 10, utf8_decode('INGRESOS SIN SALIDA'), 1, 0, 'C');
                        $pdf->Ln(10);
                        $pdf->SetX(10);
                        $pdf->Cell(60, 5, utf8_decode('Fecha'), 1, 0, 'C');
                        $pdf->Cell(30, 5, utf8_decode('Ingreso'), 1, 0, 'C');
                        $pdf->Cell(38, 5, utf8_decode('Placa'), 1, 0, 'C');
                        $pdf->Cell(75, 5, utf8_decode('Tarifa'), 1, 0, 'C');
                        $pdf->Cell(41, 5, utf8_decode('Vehiculo'), 1, 0, 'C');
                        $pdf->Ln(5);
                        $pdf->SetFont('Arial', '', 10);
                    }
                }

                // 2. Cargar los ingresos donde la fecha del SALIDA sea mayor a la Fecha Final del formulario
                $sqlCn = "SELECT
                            WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                            ELSE mov.hora
                            END) as fecha,  
                            mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i %p'), fra.nombre, fra.valor, tv.nombre
                            FROM gq_ingreso_parqueadero mov
                            LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico
                            LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico
                            LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico
                            WHERE
                            CONCAT(mov.fecha,' ',
                                    CASE
                                    WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',SUBSTRING(mov.hora,1,4))
                                    ELSE SUBSTRING(mov.hora,1,5)
                                END) <= '$fechaFh'
                            AND SUBSTRING(mov.salida,1,16) > '$fechaFh'
                            $orderby1";
                $resc = $mysqli->query($sqlCn);
                while ($row2 = mysqli_fetch_row($resc)) {
                    $pdf->Cell(60, 5, utf8_decode($row2[0]), 1, 0, 'L');
                    $pdf->Cell(30, 5, utf8_decode($row2[1]), 1, 0, 'R');
                    $pdf->Cell(38, 5, utf8_decode(strtoupper($row2[2])), 1, 0, 'L');
                    $pdf->Cell(75, 5, utf8_decode($row2[4] . ' (' . number_format($row2[5], 2, '.', ',') . ')'), 1, 0, 'L');
                    $pdf->Cell(41, 5, utf8_decode($row2[6]), 1, 0, 'L');
                    $pdf->Ln();
                    $f = $pdf->GetY();
                    if ($f >= 170){
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(10, 41);
                        $pdf->Cell(244, 10, utf8_decode('INGRESOS SIN SALIDA'), 1, 0, 'C');
                        $pdf->Ln(10);
                        $pdf->SetX(10);
                        $pdf->Cell(60, 5, utf8_decode('Fecha'), 1, 0, 'C');
                        $pdf->Cell(30, 5, utf8_decode('Ingreso'), 1, 0, 'C');
                        $pdf->Cell(38, 5, utf8_decode('Placa'), 1, 0, 'C');
                        $pdf->Cell(75, 5, utf8_decode('Tarifa'), 1, 0, 'C');
                        $pdf->Cell(41, 5, utf8_decode('Vehiculo'), 1, 0, 'C');
                        $pdf->Ln(5);
                        $pdf->SetFont('Arial', '', 10);
                    }
                }

                $nb = $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetXY(10, 41);
                $pdf->Cell(244, 10, utf8_decode('INGRESOS CON SALIDA'), 1, 0, 'C');
                $pdf->Ln(10);
                $pdf->SetX(10);
                $pdf->Cell(41, 5, utf8_decode('Fecha'), 1, 0, 'C');
                $pdf->Cell(14, 5, utf8_decode('Ingreso'), 1, 0, 'C');
                $pdf->Cell(18, 5, utf8_decode('Placa'), 1, 0, 'C');
                $pdf->Cell(41, 5, utf8_decode('Salida'), 1, 0, 'C');
                $pdf->Cell(50, 5, utf8_decode('Tarifa'), 1, 0, 'C');
                $pdf->Cell(20, 5, utf8_decode('Vehiculo'), 1, 0, 'C');
                $pdf->Cell(22, 5, utf8_decode('Factura'), 1, 0, 'C');
                $pdf->Cell(15, 5, utf8_decode('Tiempo'), 1, 0, 'C');
                $pdf->Cell(23, 5, utf8_decode('Valor'), 1, 0, 'C');
                $pdf->Ln(5);
                $total = 0;
                $pdf->SetFont('Arial', '', 10);
                /* $sqlCn1 Trae los ingresos con factura donde la fecha del ingreso este entre las fechas del informe */
                $sqlCn1 = "SELECT CONCAT(DATE_FORMAT(mov.fecha,'%d/%m/%Y'),' ',CASE
                    WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                    ELSE mov.hora
                    END) as fecha,   
                    mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i:%s %p'), fra.nombre, fra.valor, fac.numero_factura, df.valor_total_ajustado, dp.valor, dp.iva, dp.impoconsumo, dp.ajuste_peso, tv.nombre,
                    CONCAT(mov.fecha,' ',SUBSTRING(ltrim(mov.hora),1,5))as fecha, SUBSTRING(salida,1,16), mov.tiempo
                    FROM gq_ingreso_parqueadero mov
                    LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico                
                    LEFT JOIN gp_detalle_factura df ON fac.id_unico = df.factura
                    LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura
                    LEFT JOIN gp_pago pg ON dp.pago = pg.id_unico
                    LEFT JOIn gph_espacio_habitable_propiedad_relacionada sphp ON fac.id_espacio_habitable = sphp.id_unico
                    LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico     
                    LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico           
                    WHERE 
                    SUBSTRING(mov.salida,1,16) BETWEEN '$fechaIh' AND '$fechaFh' 
                    AND mov.factura IS NOT NULL 
                    AND dp.detalle_factura IS NOT NULL
                    AND pg.usuario BETWEEN $idterceroI AND $idterceroF
                    $orderby";
                $resc1 = $mysqli->query($sqlCn1);            
                while ($row1 = mysqli_fetch_row($resc1)) {
                    $subtotal = $row1[8] + $row1[9] + $row1[10] + $row1[11];
                    $total += round($subtotal);
                    $pdf->Cell(41, 5, utf8_decode($row1[0]), 1, 0, 'L');
                    $pdf->Cell(14, 5, utf8_decode($row1[1]), 1, 0, 'R');
                    $pdf->Cell(18, 5, utf8_decode(strtoupper($row1[2])), 1, 0, 'L');
                    $pdf->Cell(41, 5, utf8_decode($row1[3]), 1, 0, 'L');                
                    $pdf->Cell(50, 5, utf8_decode($row1[4] . ' (' . number_format($row1[5], 2, '.', ',') . ')'), 1, 0, 'L');
                    $pdf->Cell(20, 5, utf8_decode($row1[12]), 1, 0, 'L');                
                    $pdf->Cell(22, 5, utf8_decode($row1[6]), 1, 0, 'R');                
                    $pdf->Cell(15, 5, utf8_decode($row1[15]), 1, 0, 'R');
                    $pdf->Cell(23, 5, number_format(round($subtotal), 2, '.', ','), 1, 0, 'R');                
                    $pdf->Ln();
                    $f = $pdf->GetY();
                    if ($f >= 170){
                        $pdf->AddPage();
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(10, 41);
                        $pdf->Cell(244, 10, utf8_decode('INGRESOS CON SALIDA'), 1, 0, 'C');
                        $pdf->Ln(10);
                        $pdf->SetX(10);
                        $pdf->Cell(41, 5, utf8_decode('Fecha'), 1, 0, 'C');
                        $pdf->Cell(14, 5, utf8_decode('Ingreso'), 1, 0, 'C');
                        $pdf->Cell(18, 5, utf8_decode('Placa'), 1, 0, 'C');
                        $pdf->Cell(41, 5, utf8_decode('Salida'), 1, 0, 'C');
                        $pdf->Cell(50, 5, utf8_decode('Tarifa'), 1, 0, 'C');
                        $pdf->Cell(20, 5, utf8_decode('Vehiculo'), 1, 0, 'C');
                        $pdf->Cell(22, 5, utf8_decode('Factura'), 1, 0, 'C');
                        $pdf->Cell(15, 5, utf8_decode('Tiempo'), 1, 0, 'C');
                        $pdf->Cell(23, 5, utf8_decode('Valor'), 1, 0, 'C');
                        $pdf->Ln(5);
                        $pdf->SetFont('Arial', '', 10);
                    }
                    $subtotal = 0;
                }
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(219, 5, 'TOTAL', 1, 0, 'L');
                $pdf->Cell(25, 5, number_format($total, 2, '.', ','), 1, 0, 'R');
            }            
            ob_end_clean();
            $pdf->Output(0, 'Informe_ingreso_parqueadero.pdf', 0);
        break;
        case 2:            
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Informe_Ingresos_Parqueadero.xls");
            ?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <title>Boletín Diario De Caja</title>
                </head>
                <body>
                    <table width="100%" border="1" cellspacing="0" cellpadding="0">
                        <th colspan="9" align="center"><strong>
                                <br/><?php echo utf8_encode($razonsocial) ?>
                                <br/><?php echo $nombreIdent . ' : ' . $numeroIdent ?>
                                <br/>INFORME DE INGRESOS DE PARQUEADERO
                                <br/>DEL <?php echo $_REQUEST['fechaI'] . ' AL ' . $_REQUEST['fechaF']; ?>
                                <br/><?php echo utf8_decode($nombreusuarioI).' - '. utf8_decode($nombreusuarioF) ?>
                                <br/>&nbsp;</strong>
                        </th>                
                        <tbody>
                        <?php
                        #************** HABITACIONES*******************#                                               
                        if ($salida == 0){ // Trae ingresos SIN factura
                            $orderby = "ORDER BY 1";
                            echo '<tr><td style="background: #b4dbec; height: 50px; vertical-align: middle;" colspan="9"><strong><center>INGRESOS SIN SALIDA</center></strong></td></tr>';
                            echo '<tr>';
                            echo '<td colspan="2"><strong><center>FECHA</center></strong></td>';
                            echo '<td colspan="2"><strong><center>INGRESO</center></strong></td>';
                            echo '<td colspan="1"><strong><center>PLACA</center></strong></td>';
                            echo '<td colspan="2"><strong><center>TARIFA</center></strong></td>';
                            echo '<td colspan="2"><strong><center>VEHICULO </center></strong></td>';
                            echo '</tr>'; 
                            // 1. Cargar los ingresos SIN factura donde la fecha del INGRESO sea menor a la Fecha Final del formulario
                            $sqlCn = "SELECT
                            CONCAT(DATE_FORMAT(mov.fecha,'%d/%m/%Y'),' ',CASE
                            WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                            ELSE mov.hora
                            END) as fecha,  
                            mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i %p'), fra.nombre, fra.valor, tv.nombre
                            FROM gq_ingreso_parqueadero mov
                            LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico
                            LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico
                            LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico
                            WHERE
                            CONCAT(mov.fecha,' ',
                                CASE
                                    WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',SUBSTRING(mov.hora,1,4))
                                    ELSE SUBSTRING(mov.hora,1,5)
                                END) <= '$fechaFh'
                            AND mov.factura IS NULL $orderby";
                            $resc = $mysqli->query($sqlCn);
                            while ($row2 = mysqli_fetch_row($resc)) {
                                echo '<tr>';
                                echo '<td align="left" colspan="2" style="mso-number-format:\@">' . utf8_decode($row2[0]) . '</td>';
                                echo '<td align="right" colspan="2">' . utf8_decode($row2[1]) . '</td>';
                                echo '<td align="left">' . utf8_decode(strtoupper($row2[2])) . '</td>';
                                echo '<td align="left" colspan="2">' . utf8_decode($row2[4] . ' (' . number_format($row2[5], 2, '.', ',') . ')') . '</td>';
                                echo '<td align="left" colspan="2">' . $row2[6] . '</td>';
                                echo '</tr>';
                            }
                            
                            // 2. Cargar los ingresos donde la fecha del SALIDA sea mayor a la Fecha Final del formulario
                            $sqlCn = "SELECT
                            CONCAT(DATE_FORMAT(mov.fecha,'%d/%m/%Y'),' ',CASE
                            WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                            ELSE mov.hora
                            END) as fecha,  
                            mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i %p'), fra.nombre, fra.valor, tv.nombre
                            FROM gq_ingreso_parqueadero mov
                            LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico
                            LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico
                            LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico
                            WHERE
                            CONCAT(mov.fecha,' ',
                                    CASE
                                    WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',SUBSTRING(mov.hora,1,4))
                                    ELSE SUBSTRING(mov.hora,1,5)
                                END) <= '$fechaFh'
                            AND SUBSTRING(mov.salida,1,16) > '$fechaFh'
                            $orderby1";
                            $resc = $mysqli->query($sqlCn);
                            while ($row2 = mysqli_fetch_row($resc)) {
                                echo '<tr>';
                                echo '<td align="left" colspan="2" style="mso-number-format:\@">' . utf8_decode($row2[0]) . '</td>';
                                echo '<td align="right" colspan="2">' . utf8_decode($row2[1]) . '</td>';
                                echo '<td align="left">' . utf8_decode(strtoupper($row2[2])) . '</td>';
                                echo '<td align="left" colspan="2">' . utf8_decode($row2[4] . ' (' . number_format($row2[5], 2, '.', ',') . ')') . '</td>';
                                echo '<td align="left" colspan="2">' . $row2[6] . '</td>';
                                echo '</tr>';
                            }
                        }else if ($salida == 1){ // Trae ingresos CON factura
                            echo '<tr><td style="background: #b4dbec; height: 50px; vertical-align: middle;" colspan="9"><strong><center>INGRESOS CON SALIDA</center></strong></td></tr>';
                            echo '<tr>';
                            echo '<td><strong><center>FECHA</center></strong></td>';
                            echo '<td><strong><center>INGRESO</center></strong></td>';
                            echo '<td colspan="1"><strong><center>PLACA</center></strong></td>';
                            echo '<td colspan="1"><strong><center>SALIDA</center></strong></td>';
                            echo '<td colspan="1"><strong><center>TARIFA</center></strong></td>';
                            echo '<td colspan="1"><strong><center>VEHICULO </center></strong></td>';
                            echo '<td colspan="1"><strong><center>FACTURA</center></strong></td>';
                            echo '<td colspan="1"><strong><center>TIEMPO</center></strong></td>';
                            echo '<td colspan="1"><strong><center>VALOR</center></strong></td>';
                            echo '</tr>'; 
                            $total =0;
                            /* $sqlCn1 Trae los ingresos con factura donde la fecha del ingreso este entre las fechas del informe */
                            $sqlCn1 = "SELECT CONCAT(DATE_FORMAT(mov.fecha,'%d/%m/%Y'),' ',CASE
                            WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                            ELSE mov.hora
                            END) as fecha,   
                            mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i:%s %p'), fra.nombre, fra.valor, fac.numero_factura, df.valor_total_ajustado, dp.valor, dp.iva, dp.impoconsumo, dp.ajuste_peso, tv.nombre,
                            CONCAT(mov.fecha,' ',SUBSTRING(ltrim(mov.hora),1,5))as fecha, SUBSTRING(salida,1,16), mov.tiempo
                            FROM gq_ingreso_parqueadero mov
                            LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico                
                            LEFT JOIN gp_detalle_factura df ON fac.id_unico = df.factura
                            LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura
                            LEFT JOIN gp_pago pg ON dp.pago = pg.id_unico
                            LEFT JOIn gph_espacio_habitable_propiedad_relacionada sphp ON fac.id_espacio_habitable = sphp.id_unico
                            LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico     
                            LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico           
                            WHERE 
                            SUBSTRING(mov.salida,1,16) BETWEEN '$fechaIh' AND '$fechaFh' 
                            AND mov.factura IS NOT NULL 
                            AND dp.detalle_factura IS NOT NULL
                            AND pg.usuario BETWEEN $idterceroI AND $idterceroF
                            $orderby";
                            $resc1 = $mysqli->query($sqlCn1);            
                            while ($row1 = mysqli_fetch_row($resc1)) {
                                $subtotal = $row1[8] + $row1[9] + $row1[10] + $row1[11];
                                $total += round($subtotal);
                                echo '<tr>';
                                echo '<td align="left" style="mso-number-format:\@">' . utf8_decode($row1[0]) . '</td>';
                                echo '<td align="right">' . utf8_decode($row1[1]) . '</td>';
                                echo '<td align="left">' . utf8_decode(strtoupper($row1[2])) . '</td>';
                                echo '<td align="left" style="mso-number-format:\@">' . utf8_decode($row1[3]) . '</td>';
                                echo '<td align="left">' . utf8_decode($row1[4] . ' (' . number_format($row1[5], 2, '.', ',') . ')') . '</td>';
                                echo '<td align="left">' . $row1[12] . '</td>';
                                echo '<td align="right">' . utf8_decode($row1[6]) . '</td>';
                                echo '<td align="right">' . utf8_decode($row1[15]) . '</td>';
                                echo '<td align="right">' . number_format(round($subtotal), 2, '.', ',') . '</td>';
                                echo '</tr>';
                            }
                            echo '<tr>';
                            echo '<td colspan="8"><strong><left>TOTAL</center></strong></td>';
                            echo '<td colspan="1" align="right"><strong><right>' . number_format($total, 2, '.', ',') . '</center></strong></td>';
                            echo '</tr>';
                        }else if ($salida == 2){ /*Trae ingresos SIN factura y CON factura*/
                            if ($_REQUEST['stlorder'] ==2){
                                $orderby1 = "ORDER BY 1";
                            }
                            echo '<tr><td style="background: #b4dbec; height: 50px; vertical-align: middle;" colspan="9"><strong><center>INGRESOS SIN SALIDA</center></strong></td></tr>';
                            echo '<tr>';
                            echo '<td colspan="2"><strong><center>FECHA</center></strong></td>';
                            echo '<td colspan="2"><strong><center>INGRESO</center></strong></td>';
                            echo '<td colspan="1"><strong><center>PLACA</center></strong></td>';
                            echo '<td colspan="2"><strong><center>TARIFA</center></strong></td>';
                            echo '<td colspan="2"><strong><center>VEHICULO </center></strong></td>';
                            echo '</tr>'; 
                            // 1. Cargar los ingresos SIN factura donde la fecha del INGRESO sea menor a la Fecha Final del formulario
                            $sqlCn = "SELECT
                            CONCAT(DATE_FORMAT(mov.fecha,'%d/%m/%Y'),' ',CASE
                            WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                            ELSE mov.hora
                            END) as fecha,  
                            mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i %p'), fra.nombre, fra.valor, tv.nombre
                            FROM gq_ingreso_parqueadero mov
                            LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico
                            LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico
                            LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico
                            WHERE
                            CONCAT(mov.fecha,' ',
                                CASE
                                    WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',SUBSTRING(mov.hora,1,4))
                                    ELSE SUBSTRING(mov.hora,1,5)
                                END) <= '$fechaFh'
                            AND mov.factura IS NULL
                            $orderby1";
                            $resc = $mysqli->query($sqlCn);
                            while ($row2 = mysqli_fetch_row($resc)) {
                                echo '<tr>';
                                echo '<td align="left" colspan="2" style="mso-number-format:\@">' . utf8_decode($row2[0]) . '</td>';
                                echo '<td align="right" colspan="2">' . utf8_decode($row2[1]) . '</td>';
                                echo '<td align="left">' . utf8_decode(strtoupper($row2[2])) . '</td>';
                                echo '<td align="left" colspan="2">' . utf8_decode($row2[4] . ' (' . number_format($row2[5], 2, '.', ',') . ')') . '</td>';
                                echo '<td align="left" colspan="2">' . $row2[6] . '</td>';
                                echo '</tr>';
                            }
                            
                            // 2. Cargar los ingresos donde la fecha del SALIDA sea mayor a la Fecha Final del formulario
                            $sqlCn = "SELECT
                            CONCAT(DATE_FORMAT(mov.fecha,'%d/%m/%Y'),' ',CASE
                            WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                            ELSE mov.hora
                            END) as fecha,  
                            mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i %p'), fra.nombre, fra.valor, tv.nombre
                            FROM gq_ingreso_parqueadero mov
                            LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico
                            LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico
                            LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico
                            WHERE
                            CONCAT(mov.fecha,' ',
                                    CASE
                                    WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',SUBSTRING(mov.hora,1,4))
                                    ELSE SUBSTRING(mov.hora,1,5)
                                END) <= '$fechaFh'
                            AND SUBSTRING(mov.salida,1,16) > '$fechaFh'
                            $orderby1";
                            $resc = $mysqli->query($sqlCn);
                            while ($row2 = mysqli_fetch_row($resc)) {
                                echo '<tr>';
                                echo '<td align="left" colspan="2" style="mso-number-format:\@">' . utf8_decode($row2[0]) . '</td>';
                                echo '<td align="right" colspan="2">' . utf8_decode($row2[1]) . '</td>';
                                echo '<td align="left">' . utf8_decode(strtoupper($row2[2])) . '</td>';
                                echo '<td align="left" colspan="2">' . utf8_decode($row2[4] . ' (' . number_format($row2[5], 2, '.', ',') . ')') . '</td>';
                                echo '<td align="left" colspan="2">' . $row2[6] . '</td>';
                                echo '</tr>';
                            }
                            echo '<tr></tr>';
                            
                            echo '<tr><td style="background: #b4dbec; height: 50px; vertical-align: middle;" colspan="9"><strong><center>INGRESOS CON SALIDA</center></strong></td></tr>';
                            echo '<tr>';
                            echo '<td><strong><center>FECHA</center></strong></td>';
                            echo '<td><strong><center>INGRESO</center></strong></td>';
                            echo '<td colspan="1"><strong><center>PLACA</center></strong></td>';
                            echo '<td colspan="1"><strong><center>SALIDA</center></strong></td>';
                            echo '<td colspan="1"><strong><center>TARIFA</center></strong></td>';
                            echo '<td colspan="1"><strong><center>VEHICULO </center></strong></td>';
                            echo '<td colspan="1"><strong><center>FACTURA</center></strong></td>';
                            echo '<td colspan="1"><strong><center>TIEMPO</center></strong></td>';
                            echo '<td colspan="1"><strong><center>VALOR</center></strong></td>';
                            echo '</tr>'; 
                            $total =0;
                            /* $sqlCn1 Trae los ingresos con factura donde la fecha del ingreso este entre las fechas del informe */
                            $sqlCn1 = "SELECT CONCAT(DATE_FORMAT(mov.fecha,'%d/%m/%Y'),' ',CASE
                                WHEN SUBSTRING(mov.hora,1,2) < 10 AND  SUBSTRING(mov.hora,1,1) <> 0 THEN CONCAT('0',mov.hora)
                                ELSE mov.hora
                                END) as fecha,  
                                mov.numero, mov.placa, DATE_FORMAT(mov.salida, '%d/%m/%Y %h:%i:%s %p'), fra.nombre, fra.valor, fac.numero_factura, df.valor_total_ajustado, dp.valor, dp.iva, dp.impoconsumo, dp.ajuste_peso, tv.nombre,
                                CONCAT(mov.fecha,' ',SUBSTRING(ltrim(mov.hora),1,5))as fecha, SUBSTRING(salida,1,16), mov.tiempo
                                FROM gq_ingreso_parqueadero mov
                                LEFT JOIN gp_factura fac ON mov.factura = fac.id_unico                
                                LEFT JOIN gp_detalle_factura df ON fac.id_unico = df.factura
                                LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura
                                LEFT JOIN gp_pago pg ON dp.pago = pg.id_unico
                                LEFT JOIn gph_espacio_habitable_propiedad_relacionada sphp ON fac.id_espacio_habitable = sphp.id_unico
                                LEFT JOIN gq_fraccion fra ON mov.tarifaFraccion = fra.id_unico     
                                LEFT JOIN gp_tipo_vehiculo tv ON fra.tipo_vehiculo = tv.id_unico           
                                WHERE 
                                SUBSTRING(mov.salida,1,16) BETWEEN '$fechaIh' AND '$fechaFh' 
                                AND mov.factura IS NOT NULL 
                                AND dp.detalle_factura IS NOT NULL
                                AND pg.usuario BETWEEN $idterceroI AND $idterceroF
                                $orderby";
                            $resc1 = $mysqli->query($sqlCn1);            
                            while ($row1 = mysqli_fetch_row($resc1)) {
                                $subtotal = $row1[8] + $row1[9] + $row1[10] + $row1[11];
                                $total += round($subtotal);
                                echo '<tr>';
                                echo '<td align="left" style="mso-number-format:\@">' . utf8_decode($row1[0]) . '</td>';
                                echo '<td align="right">' . utf8_decode($row1[1]) . '</td>';
                                echo '<td align="left">' . utf8_decode(strtoupper($row1[2])) . '</td>';
                                echo '<td align="left" style="mso-number-format:\@">' . utf8_decode($row1[3]) . '</td>';
                                echo '<td align="left">' . utf8_decode($row1[4] . ' (' . number_format($row1[5], 2, '.', ',') . ')') . '</td>';
                                echo '<td align="left">' . $row1[12] . '</td>';
                                echo '<td align="right">' . utf8_decode($row1[6]) . '</td>';
                                echo '<td align="right">' . utf8_decode($row1[15]) . '</td>';
                                echo '<td align="right">' . number_format(round($subtotal), 2, '.', ',') . '</td>';
                                echo '</tr>';
                            }
                            echo '<tr>';
                            echo '<td colspan="8"><strong><left>TOTAL</center></strong></td>';
                            echo '<td colspan="1" align="right"><strong><right>' . number_format($total, 2, '.', ',') . '</center></strong></td>';
                            echo '</tr>';
                        }                        
                        ?>                            
                        </tbody>
                    </table>
                </body>
            </html>
            <?php
        break;
    }