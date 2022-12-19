<?php
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);
$anno = $_SESSION['anno'];
##########RECEPCION VARIABLES###############
#CUENTA BANCO INICIAL
if (empty($_POST['sltctai'])) {
    $cuentaI = '1';
} else {
    $cuentaI = $_POST['sltctai'];
}
#CUENTA BANCO FINAL
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
        global $ruta;
        $numpaginas = $this->PageNo();

        $this->SetFont('Arial', 'B', 12);
        $this->SetY(10);
        if ($ruta != '') {
            $this->Image('../' . $ruta, 10, 5, 25);
        }

        $this->SetX(20);
        $this->MultiCell(190, 5, utf8_decode($nombreCompania), 0, 'C');
        $this->SetX(20);
        $this->Cell(190, 5, $nitcompania, 0, 0, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Ln(5);
        $this->SetX(20);
        $this->Cell(190, 5, utf8_decode('RELACIÓN DE RETENCIONES POR BANCO'), 0, 0, 'C');
        $this->Ln(4);
        $this->SetX(20);
        $this->Cell(190, 5, utf8_decode('PERIODO DEL  ' . $fecha1 . ' AL ' . $fecha2), 0, 0, 'C');
        $this->Ln(8);
    }

    function Footer() {
        // Posición: a 1,5 cm del final
        global $hoy;
        global $usuario;
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 8);
        $this->SetX(10);
        $this->Cell(100, 10, utf8_decode('Usuario: ' . strtoupper($usuario)), 0, 0, 'L');
        $this->Cell(100, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }

}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->AliasNbPages();
$yp = $pdf->GetY();

$pdf->SetX(10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(160, 9, utf8_decode(''), 1, 0, 'C');
$pdf->Cell(40, 9, utf8_decode(''), 1, 0, 'C');
$pdf->SetX(10);
$pdf->Cell(160, 9, utf8_decode('Cuenta Retención'), 0, 0, 'C');
$pdf->Cell(40, 9, utf8_decode('Valor'), 0, 0, 'C');
$pdf->Ln(9);
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
                            AND c.clasecuenta = 16  AND c.codi_cuenta BETWEEN '$cuentaRI' and '$cuentaRF' ";
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
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(10);
            $pdf->Cell(200, 5, utf8_decode('Cuenta Banco: ' . $row[1] . ' - ' . $row[2]), 1, 0);
            $pdf->Ln(5);

            #####BUSCAR LAS CUENTAS DE RETENCION 16 DE TODOS DETALLES CNT#####
            $dtR = "SELECT DISTINCT 
                dc.cuenta , c.codi_cuenta, c.nombre
              FROM
                gf_detalle_comprobante dc
              LEFT JOIN
                gf_cuenta c ON dc.cuenta = c.id_unico 
              WHERE c.clasecuenta = 16 AND c.parametrizacionanno = $anno "
                    . "AND c.codi_cuenta BETWEEN '$cuentaRI' and '$cuentaRF' ORDER BY c.codi_cuenta ASC ";
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
                        dc.cuenta = $cuenta  AND tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL  AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'"
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
                            if (mysqli_num_rows($cntA) > 0) {
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
                            dc.cuenta = $cuenta AND  tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL  AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'"
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

                                    $subValor = $subValor + $valor;
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

                                    $subValor = $subValor + $roweg[3];
                                }
                            }
                        }
                        ##CUENTA RETENCION ##
                        $pdf->SetFont('Arial', '', 10);
                        $pdf->SetX(10);
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell(160, 5, utf8_decode($rowr[1] . ' - ' . $rowr[2]), 0, 'J');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        $px = $x + 160;
                        if ($numpaginas > $paginactual) {
                            $pdf->SetXY($x, $yp);
                            $h = $y2 - $yp;
                        } else {
                            $pdf->SetXY($x, $y);
                        }
                        $pdf->Cell(160, $h, ' ', 1, 0, 'J');
                        $pdf->CellFitScale(40, $h, utf8_decode('$' . number_format($subValor, 2, '.', ',')), 1, 0, 'R');
                        $totalV = $totalV + $subValor;
                        $pdf->Ln($h);
                    }
                }
                #TOTAL CUENTA
                $pdf->SetX(10);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(160, 5, utf8_decode('TOTALES: '), 1, 0, 'R');
                $pdf->CellFitScale(40, 5, utf8_decode('$' . number_format($totalV, 2, '.', ',')), 1, 0, 'R');
                $total = $total + $totalV;
                $pdf->Ln(5);
            }
        }
    }
} elseif ($clase == 14) {
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
              WHERE
                dc.cuenta = $cuenta AND  tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL "
                . " AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' "
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
                        AND c.clasecuenta = 16  AND c.codi_cuenta BETWEEN '$cuentaRI' and '$cuentaRF' ";
                $dt = $mysqli->query($dt);

                if (mysqli_num_rows($dt) > 0) {
                    $numCom = $numCom + 1;
                }
            }
        }
        if ($numCom > 0) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(10);
            $pdf->Cell(200, 5, utf8_decode('Cuenta Banco: ' . $row[1] . ' - ' . $row[2]), 1, 0);
            $pdf->Ln(5);

            #####BUSCAR LAS CUENTAS DE RETENCION 16 DE TODOS DETALLES CNT#####
            $dtR = "SELECT DISTINCT 
                dc.cuenta , c.codi_cuenta, c.nombre
              FROM
                gf_detalle_comprobante dc
              LEFT JOIN
                gf_cuenta c ON dc.cuenta = c.id_unico 
              WHERE c.clasecuenta = 16 AND c.parametrizacionanno = $anno "
                    . "AND c.codi_cuenta BETWEEN '$cuentaRI' and '$cuentaRF' ORDER BY c.codi_cuenta ASC ";
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
                dc.cuenta = $cuenta AND  tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'"
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
                        WHERE comprobante =$comprobante   
                        AND c.id_unico = $cuentaRet ";
                            $dt = $mysqli->query($dt);

                            if (mysqli_num_rows($dt) > 0) {
                                $dcr = $dcr + 1;
                            }
                        }
                    }
                    if ($dcr > 0) {


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
                dc.cuenta = $cuenta AND  tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'"
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
                                            . "WHERE  rt.comprobante = $cntA2 AND tr.cuenta = $row1[0] "
                                            . "GROUP BY rt.retencionbase, rt.comprobante, rt.cuentadescuentoretencion";
                                    $rb = $mysqli->query($rb);
                                    if (mysqli_num_rows($rb) > 0) {
                                        $bas = mysqli_fetch_row($rb);
                                        $base = $bas[0];
                                    } else {
                                        $base = 0;
                                    }

                                    $subValor = $subValor + $valor;
                                }
                            }
                        }
                        ##CUENTA RETENCION ##
                        $pdf->SetFont('Arial', '', 10);
                        $pdf->SetX(10);
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell(160, 5, utf8_decode($rowr[1] . ' - ' . $rowr[2]), 0, 'J');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        $px = $x + 160;
                        if ($numpaginas > $paginactual) {
                            $pdf->SetXY($x, $yp);
                            $h = $y2 - $yp;
                        } else {
                            $pdf->SetXY($x, $y);
                        }
                        $pdf->Cell(160, $h, ' ', 1, 0, 'J');
                        $pdf->CellFitScale(40, $h, utf8_decode('$' . number_format($subValor, 2, '.', ',')), 1, 0, 'R');
                        $totalV = $totalV + $subValor;
                        $pdf->Ln($h);
                    }
                }
                #TOTAL CUENTA
                $pdf->SetX(10);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(160, 5, utf8_decode('TOTALES: '), 1, 0, 'R');
                $pdf->CellFitScale(40, 5, utf8_decode('$' . number_format($totalV, 2, '.', ',')), 1, 0, 'R');
                $total = $total + $totalV;
                $pdf->Ln(5);
            }
        }
    }
}

while (ob_get_length()) {
    ob_end_clean();
}
//ob_end_clean();
$pdf->Output(0, 'Informe_Auxiliar_Retenciones_Consolidado (' . date('d/m/Y') . ').pdf', 0);
