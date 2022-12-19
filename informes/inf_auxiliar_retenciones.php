<?php
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 360);
$anno = $_SESSION['anno'];
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

#CUENTA RETENCION INICIAL
if (empty($_POST['ctari'])) {
    $cuentaRI = '1';
} else {
    $cuentaRI = $_POST['ctari'];
}
#CUENTA RETENCION FINAL
if (empty($_POST['ctarf'])) {
    $cuentaRF = '9';
} else {
    $cuentaRF = $_POST['ctarf'];
}

#FECHA INICIAL
if (empty($_POST['fechaini'])) {
    $fechaY = date('Y');
    $fechaI = $fechaY . '/01/01';
    $fecha1 = '01/01/' . $fechaY;
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

        $this->SetFont('Arial', 'B', 10);
        $this->SetY(10);
        //$this->image('../LOGOABC.png', 20,10,20,15,'PNG');
        //$pdf->SetFillColor(232,232,232);

        $this->SetX(25);
        $this->Cell(315, 5, utf8_decode($nombreCompania), 0, 0, 'C');
        // Salto de línea
        $this->setX(25);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(315, 10, utf8_decode('CÓDIGO SGC'), 0, 0, 'R');

        $this->Ln(5);

        $this->SetFont('Arial', '', 8);
        $this->SetX(25);
        $this->Cell(315, 5, $nitcompania, 0, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->SetX(25);
        $this->Cell(315, 10, utf8_decode('VERSIÓN SGC'), 0, 0, 'R');

        $this->Ln(5);

        $this->SetFont('Arial', '', 8);
        $this->SetX(25);
        $this->Cell(315, 5, utf8_decode('AUXILIAR RETENCIONES'), 0, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->SetX(25);
        $this->Cell(315, 10, utf8_decode('FECHA SGC'), 0, 0, 'R');

        $this->Ln(3);

        $this->SetFont('Arial', '', 7);
        $this->SetX(25);
        $this->Cell(315, 5, utf8_decode('Cuentas ' . $cuentaI . ' y ' . $cuentaF), 0, 0, 'C');

        $this->Ln(3);

        $this->SetFont('Arial', '', 7);
        $this->SetX(25);
        $this->Cell(315, 5, utf8_decode('entre Fechas ' . $fecha1 . ' y ' . $fecha2), 0, 0, 'C');

        $this->Ln(5);

        $this->SetX(20);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(18, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(30, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(65, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(65, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(32, 9, utf8_decode(''), 1, 0, 'C');
        $this->Cell(32, 9, utf8_decode(''), 1, 0, 'C');

        $this->SetX(20);

        $this->Cell(18, 6, utf8_decode('Tipo'), 0, 0, 'C');
        $this->Cell(30, 6, utf8_decode('Número'), 0, 0, 'C');
        $this->Cell(18, 6, utf8_decode('Tipo'), 0, 0, 'C');
        $this->Cell(30, 6, utf8_decode('Número'), 0, 0, 'C');
        $this->Cell(30, 6, utf8_decode('Fecha Comprobante'), 0, 0, 'C');
        $this->Cell(65, 9, utf8_decode('Nombre Tercero'), 0, 0, 'C');
        $this->Cell(65, 9, utf8_decode('Descipción'), 0, 0, 'C');
        $this->Cell(32, 9, utf8_decode('Valor'), 0, 0, 'C');
        $this->Cell(32, 6, utf8_decode('Base'), 0, 0, 'C');

        $this->Ln(4);

        $this->SetX(20);

        $this->Cell(18, 4, utf8_decode('Egreso'), 0, 0, 'C');
        $this->Cell(30, 4, utf8_decode('Egreso'), 0, 0, 'C');
        $this->Cell(18, 4, utf8_decode('CXP'), 0, 0, 'C');
        $this->Cell(30, 4, utf8_decode('CXP'), 0, 0, 'C');
        $this->Cell(30, 4, utf8_decode('Retención'), 0, 0, 'C');
        $this->Cell(65, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(65, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(32, 4, utf8_decode(''), 0, 0, 'C');
        $this->Cell(32, 4, utf8_decode('Gravable'), 0, 0, 'C');

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
        $this->Cell(90, 10, utf8_decode('Fecha: ' . date('d/m/Y')), 0, 0, 'L');
        $this->Cell(90, 10, utf8_decode('Máquina: ' . gethostname()), 0, 0, 'C');
        $this->Cell(90, 10, utf8_decode('Usuario: ' . strtoupper($usuario)), 0, 0, 'C');
        $this->Cell(65, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }

}

$pdf = new PDF('L', 'mm', 'Legal');
$pdf->AddPage();

$pdf->AliasNbPages();


if ($ruta != '') {
    $pdf->Image('../' . $ruta, 80, 8, 20);
}
$yp = $pdf->GetY();

#BANCOS CUENTAS BANCARIAS

$banco = "SELECT DISTINCT  dc.cuenta, c.codi_cuenta, c.nombre from gf_detalle_comprobante dc 
LEFT JOIN gf_comprobante_cnt cnt ON dc.comprobante = cnt.id_unico 
LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
LEFT JOIN 
gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico 
WHERE tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL and c.clasecuenta = 11 AND c.parametrizacionanno = $anno 
AND c.codi_cuenta BETWEEN '$cuentaI' AND '$cuentaF' AND cnt.fecha BETWEEN '$fechaI' AND '$fechaF' 
ORDER BY c.codi_cuenta ASC";

$banco = $mysqli->query($banco);
$total = 0;
$total2 = 0;

#****************Verificacar en donde se aplican las retenciones***************#
$cc = "SELECT DISTINCT clasecontable FROM gf_tipo_comprobante WHERE retencion =1";
$cc = $mysqli->query($cc);
$cc = mysqli_fetch_row($cc);
$clase = $cc[0];
$ids = "";
#****Si la clase contable es cuenta por pagar****#
if ($clase == 13) {

    while ($row = mysqli_fetch_row($banco)) {
        $cuenta = $row[0];

        ##############REALIZO LA CONSULTA PARA VERIFICAR SI ESE BANCO TIENE MOVIMIENTO CON RETENCIONES###############
        $idc = "SELECT DISTINCT
                dc.comprobante, cn.numero 
              FROM
                gf_detalle_comprobante dc
              LEFT JOIN
                gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico
              LEFT JOIN 
                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
              WHERE tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL AND 
                dc.cuenta = $cuenta AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'"
                . "ORDER BY cn.numero ASC";
        $idc = $mysqli->query($idc);
        $numCom = 0;
        while ($row1 = mysqli_fetch_row($idc)) {

            $comprobante = $row1[0];
            #BUSCAR EL AFECTADO
            $comp = "SELECT DISTINCT
                    dca.comprobante
                  FROM
                    gf_detalle_comprobante dc
                  LEFT JOIN
                    gf_detalle_comprobante dca ON dc.detalleafectado = dca.id_unico
                  WHERE
                    dc.comprobante = '$comprobante' AND dc.detalleafectado IS NOT NULL";
            $comp = $mysqli->query($comp);
            $comp = mysqli_fetch_row($comp);
            $comp = $comp[0];

            $compptal = "SELECT DISTINCT
                    dca.comprobantepptal, cp.numero, cp.tipocomprobante 
                  FROM
                    gf_detalle_comprobante dc
                  LEFT JOIN
                    gf_detalle_comprobante_pptal dca ON dc.detallecomprobantepptal = dca.id_unico 
                  LEFT JOIN 
                    gf_comprobante_pptal cp ON dca.comprobantepptal = cp.id_unico 
                  WHERE
                    dc.comprobante = '$comprobante' AND dc.detallecomprobantepptal IS NOT NULL";
            $compptal = $mysqli->query($compptal);
            if (mysqli_num_rows($compptal) > 0) {
                $compptal = mysqli_fetch_row($compptal);
                $compptal2 = $compptal[0];
                $num = $compptal[1];
                $tip = $compptal[2];
                //BUSCAR TIPO CNT
                $tipA = "SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tip";
                $tipA = $mysqli->query($tipA);
                $tipA = mysqli_fetch_row($tipA);
                $tipA = $tipA[0];

                //BUSCAR CNT 
                $cntA = "SELECT cnt.id_unico, cnt.numero, cnt.fecha, IF(CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) IS NULL 
                       OR CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) = '',
                      (tr.razonsocial),
                      CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos)) AS NOMBRE,
                    tr.numeroidentificacion, tc.sigla "
                        . "FROM gf_comprobante_cnt cnt "
                        . "LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico "
                        . "LEFT JOIN gf_tercero tr ON cnt.tercero = tr.id_unico "
                        . "WHERE numero = $num AND tipocomprobante = $tipA AND cnt.fecha BETWEEN '$fechaI' AND '$fechaF'"
                        . "ORDER BY cnt.numero ASC";
                $cntA = $mysqli->query($cntA);
                if (mysqli_num_rows($cntA) > 0) {
                    $cntA = mysqli_fetch_row($cntA);
                    $cntA2 = $cntA[0];
                    $cntN = $cntA[1];
                    $cntFecha = $cntA[2];
                    $cntTercero = $cntA[3];
                    $cntTipo = $cntA[5];


                    //BUSCO LOS DETALLES LA CUENTA CLASE 16
                    $dt = "SELECT DISTINCT 
                            dc.cuenta, valor, c.codi_cuenta   
                            FROM gf_detalle_comprobante dc 
                            LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                            WHERE comprobante =$cntA2  
                            AND c.clasecuenta = 16  AND c.parametrizacionanno = $anno 
                            AND c.codi_cuenta BETWEEN '$cuentaRI' and '$cuentaRF'";
                    $dt = $mysqli->query($dt);

                    if (mysqli_num_rows($dt) > 0) {
                        $numCom = $numCom + 1;
                    }
                }
            }
            #* Buscar Si Egreso tiene retenciones 
            $egr = "SELECT DISTINCT 
                dc.cuenta, valor, c.codi_cuenta   
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE comprobante =$comprobante  
                AND c.clasecuenta = 16  AND c.parametrizacionanno = $anno 
                AND c.codi_cuenta BETWEEN '$cuentaRI' and '$cuentaRF'";
            $egr = $mysqli->query($egr);
            if (mysqli_num_rows($egr) > 0) {
                $numCom = $numCom + 1;
            }
        }
        if ($numCom > 0) {

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(20);
            $pdf->Cell(320, 4, utf8_decode('Cuenta Banco: ' . $row[1] . ' - ' . $row[2]), 1, 0);
            $pdf->Ln(4);
            #####BUSCAR LAS CUENTAS DE RETENCION 16 DE TODOS DETALLES CNT#####
            $dtR = "SELECT DISTINCT 
                dc.cuenta , c.codi_cuenta, c.nombre
              FROM
                gf_detalle_comprobante dc
              LEFT JOIN
                gf_cuenta c ON dc.cuenta = c.id_unico 
              WHERE c.clasecuenta = 16  AND c.parametrizacionanno = $anno 
              AND c.codi_cuenta BETWEEN '$cuentaRI' and '$cuentaRF'
              ORDER BY c.codi_cuenta ASC";
            $dtR = $mysqli->query($dtR);

            if (mysqli_num_rows($dtR) > 0) {
                $totalV = 0;
                $totalB = 0;
                while ($rowr = mysqli_fetch_row($dtR)) {
                    $cuentaRet = $rowr[0];
                    #BUSCAR SI HAY DATOS
                    $dcr = 0;

                    #####BUSCAR SI EXISTE DATOS CUENTA RETENCION####
                    #BUSCA LOS COMPROBANTES RELACIONADOS A ESA CUENTA
                    $idc = "SELECT DISTINCT
                        dc.comprobante, cn.numero, tcpp.codigo  
                      FROM
                        gf_detalle_comprobante dc
                      LEFT JOIN
                        gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                      LEFT JOIN 
                        gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                      LEFT JOIN 
                        gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tc.comprobante_pptal 
                      WHERE tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL AND 
                        dc.cuenta = $cuenta  AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'"
                            . "ORDER BY cn.numero ASC";
                    $idc = $mysqli->query($idc);

                    while ($row1 = mysqli_fetch_row($idc)) {

                        $comprobante = $row1[0];
                        $numEgreso = $row1[1];
                        $tipoEgreso = $row1[2];
                        #BUSCAR EL AFECTADO
                        $comp = "SELECT DISTINCT
                            dca.comprobante
                          FROM
                            gf_detalle_comprobante dc
                          LEFT JOIN
                            gf_detalle_comprobante dca ON dc.detalleafectado = dca.id_unico
                          WHERE
                            dc.comprobante = '$comprobante' AND dc.detalleafectado IS NOT NULL";
                        $comp = $mysqli->query($comp);
                        $comp = mysqli_fetch_row($comp);
                        $comp = $comp[0];

                        $compptal = "SELECT DISTINCT
                            dca.comprobantepptal, cp.numero, cp.tipocomprobante 
                          FROM
                            gf_detalle_comprobante dc
                          LEFT JOIN
                            gf_detalle_comprobante_pptal dca ON dc.detallecomprobantepptal = dca.id_unico 
                          LEFT JOIN 
                            gf_comprobante_pptal cp ON dca.comprobantepptal = cp.id_unico 
                          WHERE
                            dc.comprobante = '$comprobante' AND dc.detallecomprobantepptal IS NOT NULL";
                        $compptal = $mysqli->query($compptal);
                        if (mysqli_num_rows($compptal) > 0) {
                            $compptal = mysqli_fetch_row($compptal);
                            $compptal2 = $compptal[0];
                            $num = $compptal[1];
                            $tip = $compptal[2];
                            //BUSCAR TIPO CNT
                            $tipA = "SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tip";
                            $tipA = $mysqli->query($tipA);
                            $tipA = mysqli_fetch_row($tipA);
                            $tipA = $tipA[0];

                            //BUSCAR CNT 
                            $cntA = "SELECT cnt.id_unico "
                                    . "FROM gf_comprobante_cnt cnt "
                                    . "LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico "
                                    . "LEFT JOIN gf_tercero tr ON cnt.tercero = tr.id_unico "
                                    . "WHERE numero = $num AND tipocomprobante = $tipA "
                                    . "ORDER BY cnt.numero ASC";
                            $cntA = $mysqli->query($cntA);
                            $cntA = mysqli_fetch_row($cntA);
                            $cntA2 = $cntA[0];

                            //BUSCO LOS DETALLES DE LA CUENTA DE RETENCION
                            $dt = "SELECT DISTINCT 
                                dc.cuenta, valor, c.codi_cuenta   
                                FROM gf_detalle_comprobante dc 
                                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                WHERE comprobante =$cntA2  
                                AND c.id_unico = $cuentaRet ";
                            $dt = $mysqli->query($dt);

                            if (mysqli_num_rows($dt) > 0) {
                                $dcr = $dcr + 1;
                            }
                        }
                    
                        $dt = "SELECT DISTINCT 
                            dc.cuenta, valor, c.codi_cuenta   
                            FROM gf_detalle_comprobante dc 
                            LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                            WHERE comprobante =$comprobante  
                            AND c.id_unico = $cuentaRet ";
                        $dt = $mysqli->query($dt);
                        if (mysqli_num_rows($dt) > 0) {
                            $dcr = $dcr + 1;
                        }
                    }
                    if ($dcr > 0) {
                        ##CUENTA RETENCION ##
                        $pdf->SetFont('Arial', 'I', 8);
                        $pdf->SetX(20);
                        $pdf->Cell(320, 4, utf8_decode('Cuenta Retención: ' . $rowr[1] . ' - ' . $rowr[2]), 1, 0);
                        $pdf->Ln(4);
                        //echo "Cuenta Retencion: ".$rowr[1]." - ".$rowr[2].'<br/>';
                        #BUSCA LOS COMPROBANTES RELACIONADOS A ESA CUENTA
                        $idc = "SELECT DISTINCT
                            dc.comprobante, cn.numero, tcpp.codigo, DATE_FORMAT(cn.fecha, '%d/%m/%Y'), 
                            cn.descripcion, IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) IS NULL 
                               OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                              (tr.razonsocial),
                              CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE,tr.numeroidentificacion  
                          FROM
                            gf_detalle_comprobante dc
                          LEFT JOIN
                            gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                          LEFT JOIN 
                            gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                          LEFT JOIN 
                            gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tc.comprobante_pptal 
                          LEFT JOIN 
                            gf_tercero tr ON cn.tercero = tr.id_unico 
                          WHERE tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL AND 
                            dc.cuenta = $cuenta AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'"
                                . "ORDER BY cn.numero ASC";
                        $idc = $mysqli->query($idc);
                        $subValor = 0;
                        $subBase = 0;
                        while ($row1 = mysqli_fetch_row($idc)) {
                            $paginactual = $numpaginas;

                            $comprobante    = $row1[0];
                            $numEgreso      = $row1[1];
                            $tipoEgreso     = $row1[2];
                            $fechaEgreso    = $row1[3];
                            $desEgreso      = $row1[4];
                            $TerceroEgreso  = $row1[5] . ' - ' . $row1[6];
                            #BUSCAR EL AFECTADO
                                    $comp = "SELECT DISTINCT
                            dca.comprobante
                          FROM
                            gf_detalle_comprobante dc
                          LEFT JOIN
                            gf_detalle_comprobante dca ON dc.detalleafectado = dca.id_unico
                          WHERE
                            dc.comprobante = '$comprobante' AND dc.detalleafectado IS NOT NULL";
                            $comp = $mysqli->query($comp);
                            $comp = mysqli_fetch_row($comp);
                            $comp = $comp[0];

                            $compptal = "SELECT DISTINCT
                                dca.comprobantepptal, cp.numero, cp.tipocomprobante 
                              FROM
                                gf_detalle_comprobante dc
                              LEFT JOIN
                                gf_detalle_comprobante_pptal dca ON dc.detallecomprobantepptal = dca.id_unico 
                              LEFT JOIN 
                                gf_comprobante_pptal cp ON dca.comprobantepptal = cp.id_unico 
                              WHERE
                                dc.comprobante = '$comprobante' AND dc.detallecomprobantepptal IS NOT NULL";
                            $compptal = $mysqli->query($compptal);
                            if (mysqli_num_rows($compptal) > 0) {
                                $compptal = mysqli_fetch_row($compptal);
                                $compptal2 = $compptal[0];
                                $num = $compptal[1];
                                $tip = $compptal[2];
                                //BUSCAR TIPO CNT
                                $tipA = "SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tip";
                                $tipA = $mysqli->query($tipA);
                                $tipA = mysqli_fetch_row($tipA);
                                $tipA = $tipA[0];

                                //BUSCAR CNT 
                                $cntA = "SELECT cnt.id_unico, cnt.numero, cnt.fecha, IF(CONCAT_WS(' ',
                                        tr.nombreuno,
                                        tr.nombredos,
                                        tr.apellidouno,
                                        tr.apellidodos) IS NULL 
                                       OR CONCAT_WS(' ',
                                        tr.nombreuno,
                                        tr.nombredos,
                                        tr.apellidouno,
                                        tr.apellidodos) = '',
                                      (tr.razonsocial),
                                      CONCAT_WS(' ',
                                        tr.nombreuno,
                                        tr.nombredos,
                                        tr.apellidouno,
                                        tr.apellidodos)) AS NOMBRE,
                                    tr.numeroidentificacion, tc.sigla, cnt.descripcion "
                                        . "FROM gf_comprobante_cnt cnt "
                                        . "LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico "
                                        . "LEFT JOIN gf_tercero tr ON cnt.tercero = tr.id_unico "
                                        . "WHERE numero = $num AND tipocomprobante = $tipA "
                                        . "ORDER BY cnt.numero ASC";
                                $cntA = $mysqli->query($cntA);
                                if (mysqli_num_rows($cntA) > 0) {
                                    $cntA = mysqli_fetch_row($cntA);
                                    $cntA2 = $cntA[0];
                                    $cntN = $cntA[1];
                                    $cntF = $cntA[2];
                                    $cntTercero = ucwords(mb_strtolower($cntA[3]));
                                    $cntTipo = $cntA[5];
                                    $descripcion = ucwords(mb_strtolower($cntA[6]));

                                    $fecha22 = $cntA[2];
                                    $fecha_div22 = explode("-", $fecha22);
                                    $anio22 = $fecha_div22[0];
                                    $mes22 = $fecha_div22[1];
                                    $dia22 = $fecha_div22[2];
                                    $cntFecha = $dia22 . '/' . $mes22 . '/' . $anio22;

                                    //BUSCO LOS DETALLES DE LA CUENTA DE RETENCION
                                    $dt = "SELECT DISTINCT 
                                    dc.cuenta, valor, c.codi_cuenta   
                                    FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                    WHERE comprobante =$cntA2  
                                    AND c.id_unico = $cuentaRet ";
                                    $dt = $mysqli->query($dt);

                                    if (mysqli_num_rows($dt) > 0) {


                                    $row1 = mysqli_fetch_row($dt);
                                    $valor = $row1[1];
                                    //BUSCO EL VALOR BASE EN TIPO RETENCION
                                    $rb = "SELECT rt.retencionbase FROM gf_retencion rt "
                                            . "LEFT JOIN gf_tipo_retencion tr ON rt.tiporetencion = tr.id_unico "
                                            . "WHERE  rt.comprobante = $cntA2 AND tr.cuenta = $row1[0] "
                                            . "GROUP BY rt.retencionbase, rt.comprobante, rt.cuentadescuentoretencion";
                                    $rb = $mysqli->query($rb);
                                    if (mysqli_num_rows($rb) > 0) {
                                        $bas = mysqli_fetch_row($rb);
                                        $base = $bas[0];
                                    } else {
                                        $base = 0;
                                    }

                                    $pdf->SetFont('Arial', '', 8);
                                    $pdf->SetX(20);
                                    $ypr = $pdf->GetY();

                                    $pdf->Cell(126, 4, utf8_decode(' '), 0, 0, 'C');


                                    $x = $pdf->GetX();
                                    $y = $pdf->GetY();
                                    $pdf->MultiCell(65, 4, utf8_decode($cntTercero), 0, 'J');
                                    $y2 = $pdf->GetY();
                                    $h = $y2 - $y;
                                    $px = $x + 65;
                                    if ($numpaginas > $paginactual) {
                                        $pdf->SetXY($px, $yp);
                                        $h = $y2 - $yp;
                                    } else {
                                        $pdf->SetXY($px, $y);
                                    }

                                    $x2 = $pdf->GetX();
                                    $y2 = $pdf->GetY();
                                    $pdf->MultiCell(65, 4, utf8_decode($descripcion), 0, 'J');
                                    $y22 = $pdf->GetY();
                                    $h2 = $y22 - $y2;
                                    $px2 = $x2 + 65;
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

                                    $pdf->SetX(20);
                                    $pdf->Cell(18, $alto, utf8_decode($tipoEgreso), 1, 0, 'C');
                                    $pdf->Cell(30, $alto, utf8_decode($numEgreso), 1, 0, 'C');
                                    $pdf->Cell(18, $alto, utf8_decode($cntTipo), 1, 0, 'C');
                                    $pdf->Cell(30, $alto, utf8_decode($cntN), 1, 0, 'C');
                                    $pdf->Cell(30, $alto, utf8_decode($cntFecha), 1, 0, 'C');
                                    $pdf->Cell(65, $alto, utf8_decode(' '), 1, 0, 'C');
                                    $pdf->Cell(65, $alto, utf8_decode(' '), 1, 0, 'C');
                                    $pdf->CellFitScale(32, $alto, utf8_decode('$' . number_format($valor, 2, '.', ',')), 1, 0, 'R');
                                    $pdf->CellFitScale(32, $alto, utf8_decode('$' . number_format($base, 2, '.', ',')), 1, 0, 'R');
                                    $pdf->Ln($alto);
                                    $subValor = $subValor + $valor;
                                    $subBase = $subBase + $base;
                                }
                            
                                }
                            }
                            #Buscar datos Egreso Si tiene retenciones
                            $dt = "SELECT DISTINCT 
                                r.valorretencion, r.retencionbase, r.id_unico , if(dc.valor>0, dc.valor, dc.valor*-1)
                                FROM gf_detalle_comprobante dc 
                                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                LEFT JOIN gf_retencion r ON dc.comprobante = r.comprobante AND r.cuentadescuentoretencion = dc.cuenta 
                                WHERE dc.comprobante =$comprobante  
                                AND c.id_unico = $cuentaRet ";
                            $dt = $mysqli->query($dt);
                            if (mysqli_num_rows($dt) > 0) {
                                while ($roweg = mysqli_fetch_row($dt)) {
                                    $dtsc = "SELECT DISTINCT cn.id_unico, cn.numero, tc.sigla, tc.nombre
                                        FROM gf_comprobante_cnt cn 
                                        LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                                        LEFT JOIN gf_detalle_comprobante dce ON dce.detalleafectado = dc.id_unico 
                                        LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                        WHERE dce.comprobante= $comprobante";
                                    $dtsc = $mysqli->query($dtsc);
                                    $dtsc = mysqli_fetch_row($dtsc);
                                    
                                    $pdf->SetFont('Arial', '', 8);
                                    $pdf->SetX(20);
                                    $ypr = $pdf->GetY();

                                    $pdf->Cell(126, 4, utf8_decode(' '), 0, 0, 'C');


                                    $x = $pdf->GetX();
                                    $y = $pdf->GetY();
                                    $pdf->MultiCell(65, 4, utf8_decode($TerceroEgreso), 0, 'J');
                                    $y2 = $pdf->GetY();
                                    $h = $y2 - $y;
                                    $px = $x + 65;
                                    if ($numpaginas > $paginactual) {
                                        $pdf->SetXY($px, $yp);
                                        $h = $y2 - $yp;
                                    } else {
                                        $pdf->SetXY($px, $y);
                                    }

                                    $x2 = $pdf->GetX();
                                    $y2 = $pdf->GetY();
                                    $pdf->MultiCell(65, 4, utf8_decode($desEgreso), 0, 'J');
                                    $y22 = $pdf->GetY();
                                    $h2 = $y22 - $y2;
                                    $px2 = $x2 + 65;
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

                                    $pdf->SetX(20);
                                    $pdf->Cell(18, $alto, utf8_decode($tipoEgreso), 1, 0, 'C');
                                    $pdf->Cell(30, $alto, utf8_decode($numEgreso), 1, 0, 'C');
                                    $pdf->Cell(18, $alto, utf8_decode($dtsc[2]), 1, 0, 'C');
                                    $pdf->Cell(30, $alto, utf8_decode($dtsc[1]), 1, 0, 'C');
                                    $pdf->Cell(30, $alto, utf8_decode($fechaEgreso ), 1, 0, 'C');
                                    $pdf->Cell(65, $alto, utf8_decode(' '), 1, 0, 'C');
                                    $pdf->Cell(65, $alto, utf8_decode(' '), 1, 0, 'C');
                                    $pdf->CellFitScale(32, $alto, utf8_decode('$' . number_format($roweg[3], 2, '.', ',')), 1, 0, 'R');
                                    $pdf->CellFitScale(32, $alto, utf8_decode('$' . number_format($roweg[1], 2, '.', ',')), 1, 0, 'R');
                                    $pdf->Ln($alto);
                                    
                                    $subValor = $subValor + $roweg[3];
                                    $subBase = $subBase + $roweg[1];
                                }
                            }
                        }
                        #SUBTOTALES
                        $pdf->SetX(20);
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(256, 4, utf8_decode('SUBTOTAL: '), 1, 0, 'R');
                        $pdf->CellFitScale(32, 4, utf8_decode('$' . number_format($subValor, 2, '.', ',')), 1, 0, 'R');
                        $pdf->CellFitScale(32, 4, utf8_decode('$' . number_format($subBase, 2, '.', ',')), 1, 0, 'R');
                        $totalV = $totalV + $subValor;
                        $totalB = $totalB + $subBase;
                        $pdf->Ln(4);
                    }
                }
                #TOTAL CUENTA
                $pdf->SetX(20);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(256, 4, utf8_decode('TOTALES: '), 1, 0, 'R');
                $pdf->CellFitScale(32, 4, utf8_decode('$' . number_format($totalV, 2, '.', ',')), 1, 0, 'R');
                $pdf->CellFitScale(32, 4, utf8_decode('$' . number_format($totalB, 2, '.', ',')), 1, 0, 'R');
                $total = $total + $totalV;
                $total2 = $total2 + $totalB;
                $pdf->Ln(4);
            }
        }
    }
}
#Si La Clase Contable Es Egreso
elseif ($clase == 14) {
    while ($row = mysqli_fetch_row($banco)) {
        $cuenta = $row[0];

        ##############REALIZO LA CONSULTA PARA VERIFICAR SI ESE BANCO TIENE MOVIMIENTO CON RETENCIONES###############
        $idc = "SELECT DISTINCT
                dc.comprobante, cn.numero 
              FROM
                gf_detalle_comprobante dc
              LEFT JOIN
                gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
              LEFT JOIN 
                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
              WHERE tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL AND 
                dc.cuenta = $cuenta  AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'"
                . "ORDER BY cn.numero ASC";
        $idc = $mysqli->query($idc);
        $numCom = 0;
        while ($row1 = mysqli_fetch_row($idc)) {

            $comprobante = $row1[0];
            #BUSCAR EL AFECTADO
            $comp = "SELECT DISTINCT
                    dca.comprobante
                  FROM
                    gf_detalle_comprobante dc
                  LEFT JOIN
                    gf_detalle_comprobante dca ON dc.detalleafectado = dca.id_unico
                  WHERE
                    dc.comprobante = '$comprobante' AND dc.detalleafectado IS NOT NULL";
            $comp = $mysqli->query($comp);
            $comp = mysqli_fetch_row($comp);
            $comp = $comp[0];

            $compptal = "SELECT DISTINCT
                    dca.comprobantepptal, cp.numero, cp.tipocomprobante 
                  FROM
                    gf_detalle_comprobante dc
                  LEFT JOIN
                    gf_detalle_comprobante_pptal dca ON dc.detallecomprobantepptal = dca.id_unico 
                  LEFT JOIN 
                    gf_comprobante_pptal cp ON dca.comprobantepptal = cp.id_unico 
                  WHERE
                    dc.comprobante = '$comprobante' AND dc.detallecomprobantepptal IS NOT NULL";
            $compptal = $mysqli->query($compptal);
            if (mysqli_num_rows($compptal) > 0) {
                $compptal = mysqli_fetch_row($compptal);
                $compptal2 = $compptal[0];
                $num = $compptal[1];
                $tip = $compptal[2];
                //BUSCAR TIPO CNT
                $tipA = "SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tip";
                $tipA = $mysqli->query($tipA);
                $tipA = mysqli_fetch_row($tipA);
                $tipA = $tipA[0];

                //BUSCAR CNT 
                $cntA = "SELECT cnt.id_unico, cnt.numero, cnt.fecha, IF(CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) IS NULL 
                       OR CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) = '',
                      (tr.razonsocial),
                      CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos)) AS NOMBRE,
                    tr.numeroidentificacion, tc.sigla "
                        . "FROM gf_comprobante_cnt cnt "
                        . "LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico "
                        . "LEFT JOIN gf_tercero tr ON cnt.tercero = tr.id_unico "
                        . "WHERE numero = $num AND tipocomprobante = $tipA AND cnt.fecha BETWEEN '$fechaI' AND '$fechaF'"
                        . "ORDER BY cnt.numero ASC";
                $cntA = $mysqli->query($cntA);
                $cntA = mysqli_fetch_row($cntA);
                $cntA2 = $cntA[0];
                $cntN = $cntA[1];
                $cntFecha = $cntA[2];
                $cntTercero = $cntA[3];
                $cntTipo = $cntA[5];


                //BUSCO LOS DETALLES LA CUENTA CLASE 16
                $dt = "SELECT DISTINCT 
                        dc.cuenta, valor, c.codi_cuenta   
                        FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                        WHERE comprobante =$comprobante   
                        AND c.clasecuenta = 16  AND c.parametrizacionanno = $anno AND c.codi_cuenta BETWEEN '$cuentaRI' and '$cuentaRF'";
                $dt = $mysqli->query($dt);

                if (mysqli_num_rows($dt) > 0) {
                    $numCom = $numCom + 1;
                }
            }
        }
        if ($numCom > 0) {

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(20);
            $pdf->Cell(320, 4, utf8_decode('Cuenta Banco: ' . $row[1] . ' - ' . $row[2]), 1, 0);
            $pdf->Ln(4);

            #####BUSCAR LAS CUENTAS DE RETENCION 16 DE TODOS DETALLES CNT#####
            $dtR = "SELECT DISTINCT 
                dc.cuenta , c.codi_cuenta, c.nombre
              FROM
                gf_detalle_comprobante dc
              LEFT JOIN
                gf_cuenta c ON dc.cuenta = c.id_unico 
              WHERE c.clasecuenta = 16  AND c.parametrizacionanno = $anno 
               AND c.codi_cuenta BETWEEN '$cuentaRI' and '$cuentaRF'
               ORDER BY c.codi_cuenta ASC";
            $dtR = $mysqli->query($dtR);

            if (mysqli_num_rows($dtR) > 0) {
                $totalV = 0;
                $totalB = 0;
                while ($rowr = mysqli_fetch_row($dtR)) {
                    $cuentaRet = $rowr[0];
                    #BUSCAR SI HAY DATOS
                    $dcr = 0;

                    #####BUSCAR SI EXISTE DATOS CUENTA RETENCION####
                    #BUSCA LOS COMPROBANTES RELACIONADOS A ESA CUENTA
                    $idc = "SELECT DISTINCT
                dc.comprobante, cn.numero, tcpp.codigo  
              FROM
                gf_detalle_comprobante dc
              LEFT JOIN
                gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
              LEFT JOIN 
                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
              LEFT JOIN 
                gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tc.comprobante_pptal 
              WHERE
                dc.cuenta = $cuenta 
                AND tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL  
                AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                ORDER BY cn.numero ASC";
                    $idc = $mysqli->query($idc);

                    while ($row1 = mysqli_fetch_row($idc)) {

                        $comprobante = $row1[0];
                        $numEgreso = $row1[1];
                        $tipoEgreso = $row1[2];
                        #BUSCAR EL AFECTADO
                        $comp = "SELECT DISTINCT
                    dca.comprobante
                  FROM
                    gf_detalle_comprobante dc
                  LEFT JOIN
                    gf_detalle_comprobante dca ON dc.detalleafectado = dca.id_unico
                  WHERE
                    dc.comprobante = '$comprobante' AND dc.detalleafectado IS NOT NULL";
                        $comp = $mysqli->query($comp);
                        $comp = mysqli_fetch_row($comp);
                        $comp = $comp[0];

                        $compptal = "SELECT DISTINCT
                    dca.comprobantepptal, cp.numero, cp.tipocomprobante 
                  FROM
                    gf_detalle_comprobante dc
                  LEFT JOIN
                    gf_detalle_comprobante_pptal dca ON dc.detallecomprobantepptal = dca.id_unico 
                  LEFT JOIN 
                    gf_comprobante_pptal cp ON dca.comprobantepptal = cp.id_unico 
                  WHERE
                    dc.comprobante = '$comprobante' AND dc.detallecomprobantepptal IS NOT NULL";
                        $compptal = $mysqli->query($compptal);
                        if (mysqli_num_rows($compptal) > 0) {
                            $compptal = mysqli_fetch_row($compptal);
                            $compptal2 = $compptal[0];
                            $num = $compptal[1];
                            $tip = $compptal[2];
                            //BUSCAR TIPO CNT
                            $tipA = "SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tip";
                            $tipA = $mysqli->query($tipA);
                            $tipA = mysqli_fetch_row($tipA);
                            $tipA = $tipA[0];

                            //BUSCAR CNT 
                            $cntA = "SELECT cnt.id_unico "
                                    . "FROM gf_comprobante_cnt cnt "
                                    . "LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico "
                                    . "LEFT JOIN gf_tercero tr ON cnt.tercero = tr.id_unico "
                                    . "WHERE numero = $num AND tipocomprobante = $tipA "
                                    . "ORDER BY cnt.numero ASC";
                            $cntA = $mysqli->query($cntA);
                            $cntA = mysqli_fetch_row($cntA);
                            $cntA2 = $cntA[0];

                            //BUSCO LOS DETALLES DE LA CUENTA DE RETENCION
                            $dt = "SELECT DISTINCT 
                        dc.cuenta, valor, c.codi_cuenta   
                        FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                        WHERE comprobante =$comprobante   
                        AND c.id_unico = $cuentaRet ";
                            $dt = $mysqli->query($dt);

                            if (mysqli_num_rows($dt) > 0) {
                                $dcr = $dcr + 1;
                            }
                        }
                    }
                    if ($dcr > 0) {
                        ##CUENTA RETENCION ##
                        $pdf->SetFont('Arial', 'I', 8);
                        $pdf->SetX(20);
                        $pdf->Cell(320, 4, utf8_decode('Cuenta Retención: ' . $rowr[1] . ' - ' . $rowr[2]), 1, 0);
                        $pdf->Ln(4);
                        //echo "Cuenta Retencion: ".$rowr[1]." - ".$rowr[2].'<br/>';
                        #BUSCA LOS COMPROBANTES RELACIONADOS A ESA CUENTA
                        $idc = "SELECT DISTINCT
                dc.comprobante, cn.numero, tcpp.codigo  
              FROM
                gf_detalle_comprobante dc
              LEFT JOIN
                gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
              LEFT JOIN 
                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
              LEFT JOIN 
                gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tc.comprobante_pptal 
              LEFT JOIN 
                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
              WHERE tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL AND 
                dc.cuenta = $cuenta  AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' "
                                . "ORDER BY cn.numero ASC";
                        $idc = $mysqli->query($idc);
                        $subValor = 0;
                        $subBase = 0;
                        while ($row1 = mysqli_fetch_row($idc)) {
                            $paginactual = $numpaginas;

                            $comprobante = $row1[0];
                            $numEgreso = $row1[1];
                            $tipoEgreso = $row1[2];
                            #BUSCAR EL AFECTADO
                            $comp = "SELECT DISTINCT
                    dca.comprobante
                  FROM
                    gf_detalle_comprobante dc
                  LEFT JOIN
                    gf_detalle_comprobante dca ON dc.detalleafectado = dca.id_unico
                  WHERE
                    dc.comprobante = '$comprobante' AND dc.detalleafectado IS NOT NULL";
                            $comp = $mysqli->query($comp);
                            $comp = mysqli_fetch_row($comp);
                            $comp = $comp[0];

                            $compptal = "SELECT DISTINCT
                    dca.comprobantepptal, cp.numero, cp.tipocomprobante 
                  FROM
                    gf_detalle_comprobante dc
                  LEFT JOIN
                    gf_detalle_comprobante_pptal dca ON dc.detallecomprobantepptal = dca.id_unico 
                  LEFT JOIN 
                    gf_comprobante_pptal cp ON dca.comprobantepptal = cp.id_unico 
                  WHERE
                    dc.comprobante = '$comprobante' AND dc.detallecomprobantepptal IS NOT NULL";
                            $compptal = $mysqli->query($compptal);
                            if (mysqli_num_rows($compptal) > 0) {
                                $compptal = mysqli_fetch_row($compptal);
                                $compptal2 = $compptal[0];
                                $num = $compptal[1];
                                $tip = $compptal[2];
                                //BUSCAR TIPO CNT
                                $tipA = "SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tip";
                                $tipA = $mysqli->query($tipA);
                                $tipA = mysqli_fetch_row($tipA);
                                $tipA = $tipA[0];

                                //BUSCAR CNT 
                                $cntA = "SELECT cnt.id_unico, cnt.numero, cnt.fecha, IF(CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) IS NULL 
                       OR CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) = '',
                      (tr.razonsocial),
                      CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos)) AS NOMBRE,
                    tr.numeroidentificacion, tc.sigla, cnt.descripcion "
                                        . "FROM gf_comprobante_cnt cnt "
                                        . "LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico "
                                        . "LEFT JOIN gf_tercero tr ON cnt.tercero = tr.id_unico "
                                        . "WHERE numero = $num AND tipocomprobante = $tipA "
                                        . "ORDER BY cnt.numero ASC";
                                $cntA = $mysqli->query($cntA);
                                $cntA = mysqli_fetch_row($cntA);
                                $cntA2 = $cntA[0];
                                $cntN = $cntA[1];
                                $cntF = $cntA[2];
                                $cntTercero = ucwords(mb_strtolower($cntA[3]));
                                $cntTipo = $cntA[5];
                                $descripcion = ucwords(mb_strtolower($cntA[6]));

                                $fecha22 = $cntA[2];
                                $fecha_div22 = explode("-", $fecha22);
                                $anio22 = $fecha_div22[0];
                                $mes22 = $fecha_div22[1];
                                $dia22 = $fecha_div22[2];
                                $cntFecha = $dia22 . '/' . $mes22 . '/' . $anio22;

                                //BUSCO LOS DETALLES DE LA CUENTA DE RETENCION
                                $dt = "SELECT DISTINCT 
                        dc.cuenta, valor, c.codi_cuenta   
                        FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                        WHERE comprobante =$comprobante   
                        AND c.id_unico = $cuentaRet ";
                                $dt = $mysqli->query($dt);

                                if (mysqli_num_rows($dt) > 0) {


                                    $row1 = mysqli_fetch_row($dt);
                                    $valor = $row1[1];
                                    //BUSCO EL VALOR BASE EN TIPO RETENCION
                                    $rb = "SELECT rt.retencionbase FROM gf_retencion rt "
                                            . "LEFT JOIN gf_tipo_retencion tr ON rt.tiporetencion = tr.id_unico "
                                            . "WHERE  rt.comprobante = $comprobante AND tr.cuenta = $row1[0] "
                                            . "GROUP BY rt.retencionbase, rt.comprobante, rt.cuentadescuentoretencion";
                                    $rb = $mysqli->query($rb);
                                    if (mysqli_num_rows($rb) > 0) {
                                        $bas = mysqli_fetch_row($rb);
                                        $base = $bas[0];
                                    } else {
                                        $base = 0;
                                    }

                                    $pdf->SetFont('Arial', '', 8);
                                    $pdf->SetX(20);
                                    $ypr = $pdf->GetY();

                                    $pdf->Cell(126, 4, utf8_decode(' '), 0, 0, 'C');


                                    $x = $pdf->GetX();
                                    $y = $pdf->GetY();
                                    $pdf->MultiCell(65, 4, utf8_decode($cntTercero), 0, 'J');
                                    $y2 = $pdf->GetY();
                                    $h = $y2 - $y;
                                    $px = $x + 65;
                                    if ($numpaginas > $paginactual) {
                                        $pdf->SetXY($px, $yp);
                                        $h = $y2 - $yp;
                                    } else {
                                        $pdf->SetXY($px, $y);
                                    }

                                    $x2 = $pdf->GetX();
                                    $y2 = $pdf->GetY();
                                    $pdf->MultiCell(65, 4, utf8_decode($descripcion), 0, 'J');
                                    $y22 = $pdf->GetY();
                                    $h2 = $y22 - $y2;
                                    $px2 = $x2 + 65;
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

                                    $pdf->SetX(20);
                                    $pdf->Cell(18, $alto, utf8_decode($tipoEgreso), 1, 0, 'C');
                                    $pdf->Cell(30, $alto, utf8_decode($numEgreso), 1, 0, 'C');
                                    $pdf->Cell(18, $alto, utf8_decode($cntTipo), 1, 0, 'C');
                                    $pdf->Cell(30, $alto, utf8_decode($cntN), 1, 0, 'C');
                                    $pdf->Cell(30, $alto, utf8_decode($cntFecha), 1, 0, 'C');
                                    $pdf->Cell(65, $alto, utf8_decode(' '), 1, 0, 'C');
                                    $pdf->Cell(65, $alto, utf8_decode(' '), 1, 0, 'C');
                                    $pdf->CellFitScale(32, $alto, utf8_decode('$' . number_format($valor, 2, '.', ',')), 1, 0, 'R');
                                    $pdf->CellFitScale(32, $alto, utf8_decode('$' . number_format($base, 2, '.', ',')), 1, 0, 'R');
                                    $pdf->Ln($alto);
                                    $subValor = $subValor + $valor;
                                    $subBase = $subBase + $base;
                                }
                            }
                        }
                        #SUBTOTALES
                        $pdf->SetX(20);
                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->Cell(256, 4, utf8_decode('SUBTOTAL: '), 1, 0, 'R');
                        $pdf->CellFitScale(32, 4, utf8_decode('$' . number_format($subValor, 2, '.', ',')), 1, 0, 'R');
                        $pdf->CellFitScale(32, 4, utf8_decode('$' . number_format($subBase, 2, '.', ',')), 1, 0, 'R');
                        $totalV = $totalV + $subValor;
                        $totalB = $totalB + $subBase;
                        $pdf->Ln(4);
                    }
                }
                #TOTAL CUENTA
                $pdf->SetX(20);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(256, 4, utf8_decode('TOTALES: '), 1, 0, 'R');
                $pdf->CellFitScale(32, 4, utf8_decode('$' . number_format($totalV, 2, '.', ',')), 1, 0, 'R');
                $pdf->CellFitScale(32, 4, utf8_decode('$' . number_format($totalB, 2, '.', ',')), 1, 0, 'R');
                $total = $total + $totalV;
                $total2 = $total2 + $totalB;
                $pdf->Ln(4);
            }
        }
    }
}
while (ob_get_length()) {
    ob_end_clean();
}
//ob_end_clean();
$pdf->Output(0, 'Informe_Auxiliar_Retenciones (' . date('d/m/Y') . ').pdf', 0);
