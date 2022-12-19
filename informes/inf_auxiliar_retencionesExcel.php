<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Auxiliar_Retenciones.xls");
require_once("../Conexion/conexion.php");
session_start();
ini_set('max_execution_time', 0);
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
#************Datos Compañia************#
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Auxiliar de Retenciones</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="9" align="center"><strong>
                    <br/>&nbsp;
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent . ' : ' . $numeroIdent . "<br/>" . $direccinTer . ' Tel:' . $telefonoTer ?>
                    <br/>&nbsp;
                    <br/> AUXILIAR DE RETENCIONES 
                    <br/>PERIODO DEL <?php echo $fecha1 . ' AL ' . $fecha2 ?>
                    <br/>&nbsp;</strong>
            </th>
            <tr>
                <td align="center"><strong>Tipo Egreso</strong></td>
                <td align="center"><strong>Número Egreso</strong></td>
                <td align="center"><strong>Tipo Cuenta Por Pagar</strong></td>   
                <td align="center"><strong>Número Cuenta Por Pagar</strong></td>
                <td align="center"><strong>Fecha Comprobante Retención</strong></td>
                <td align="center"><strong>Nombre Tercero</strong></td>
                <td align="center"><strong>Descripción</strong></td>
                <td align="center"><strong>Valor</strong></td>
                <td align="center"><strong>Base Gravable</strong></td>
            </tr>  
            <?php
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
                    #Buscar Egresos con esa cuenta Bancaria
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
                        ?>
                        <tr><td colspan="9"><strong><?= 'Cuenta Banco ' . $row[1] . '-' . $row[2]; ?></strong></td></tr>     
                        <?php
                        #####BUSCAR LAS CUENTAS DE RETENCION 16 DE TODOS DETALLES CNT#####
                        $dtR = "SELECT DISTINCT 
                            dc.cuenta , c.codi_cuenta, c.nombre
                          FROM
                            gf_detalle_comprobante dc
                          LEFT JOIN
                            gf_cuenta c ON dc.cuenta = c.id_unico 
                          WHERE c.clasecuenta = 16  AND c.parametrizacionanno = $anno 
                          AND c.codi_cuenta BETWEEN '$cuentaRI' and '$cuentaRF'
                          ORDER BY c.codi_cuenta ASC ";
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
                                    dc.comprobante, cn.numero, tcpp.codigo , DATE_FORMAT(cn.fecha, '%d/%m/%Y')
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
                                    $fechaEgreso = $row1[3];
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

                                if ($dcr > 0) {    ##CUENTA RETENCION ##
                                    ?>
                                    <tr><td colspan="9"><i><?= "Cuenta Retencion: " . $rowr[1] . " - " . $rowr[2]; ?></i></td></tr>
                                    <?php
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
                                        $comprobante = $row1[0];
                                        $numEgreso = $row1[1];
                                        $tipoEgreso = $row1[2];
                                        $fechaEgreso = $row1[3];
                                        $desEgreso = $row1[4];
                                        $TerceroEgreso = $row1[5] . ' - ' . $row1[6];
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
                                                    ?>
                                                    <tr>
                                                        <td align="center"><?php echo mb_strtoupper($tipoEgreso) ?></td>
                                                        <td align="center"><?php echo $numEgreso; ?></td>
                                                        <td align="center"><?php echo mb_strtoupper($cntTipo); ?></td>   
                                                        <td align="center"><?php echo $cntN; ?></td>
                                                        <td align="center"><?php echo $cntFecha ?></td>
                                                        <td align="left"><?php echo $cntTercero ?></td>
                                                        <td align="left"><?php echo ($descripcion); ?></td>
                                                        <td align="right"><?php echo '$' . number_format($valor, 2, '.', ',') ?></td>
                                                        <td align="right"><?php echo '$' . number_format($base, 2, '.', ',') ?></td>
                                                    </tr>
                                                    <?php
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
                                                echo '<tr>
                                                    <td align="center">' . mb_strtoupper($tipoEgreso) . '</td>
                                                    <td align="center">' . $numEgreso . '</td>
                                                    <td align="center">' . mb_strtoupper($dtsc[2]) . '</td>   
                                                    <td align="center">' . $dtsc[1]. '</td>
                                                    <td align="center">' . $fechaEgreso . '</td>
                                                    <td align="left">' . $TerceroEgreso . '</td>
                                                    <td align="left">' . $desEgreso . '</td>
                                                    <td align="right">$' . number_format($roweg[3], 2, '.', ',') . '</td>
                                                    <td align="right">$' . number_format($roweg[1], 2, '.', ',') . '</td>
                                                </tr>';
                                                $subValor = $subValor + $roweg[3];
                                                $subBase = $subBase + $roweg[1];
                                            }
                                        }
                                    }

                                    #SUBTOTALES
                                    ?>
                                    <tr>
                                        <td colspan="7" align="right"><strong>SUBTOTAL:</strong></td>
                                        <td align="right"><strong><?php echo '$' . number_format($subValor, 2, '.', ',') ?></strong></td>
                                        <td align="right"><strong><?php echo '$' . number_format($subBase, 2, '.', ',') ?></strong></td>
                                    </tr>
                                    <?php
                                    $totalV = $totalV + $subValor;
                                    $totalB = $totalB + $subBase;
                                }
                            }
                            #TOTAL CUENTA
                            ?>
                            <tr>
                                <td colspan="7" align="right"><strong>TOTALES :</strong></td>
                                <td align="right"><strong><?php echo '$' . number_format($totalV, 2, '.', ',') ?></strong></td>
                                <td align="right"><strong><?php echo '$' . number_format($totalB, 2, '.', ',') ?></strong></td>
                            </tr>
                            <?php
                            $total = $total + $totalV;
                            $total2 = $total2 + $totalB;
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
                dc.cuenta = $cuenta AND tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'"
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
                        ?>
                        <tr>
                            <td colspan="9"><strong>
                                    <?php echo 'Cuenta Banco ' . $row[1] . '-' . $row[2]; ?>
                                </strong></td>
                        </tr>     
                        <?php
                        ####BUSCAR LAS CUENTAS DE RETENCION 16 DE TODOS DETALLES CNT#####
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
                    dc.cuenta = $cuenta AND tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'"
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
                                    ##CUENTA RETENCION ##
                                    ?>
                                    <tr>
                                        <td colspan="9"><i>
                        <?php echo "Cuenta Retencion: " . $rowr[1] . " - " . $rowr[2]; ?>
                                            </i>
                                        </td>
                                    </tr>
                        <?php
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
                    dc.cuenta = $cuenta AND tc.clasecontable = 14 and tc.comprobante_pptal IS NOT NULL AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' "
                                . "ORDER BY cn.numero ASC";
                        $idc = $mysqli->query($idc);
                        $subValor = 0;
                        $subBase = 0;
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
                                    ?>
                                                <tr>
                                                    <td align="center"><?php echo mb_strtoupper($tipoEgreso) ?></td>
                                                    <td align="center"><?php echo $numEgreso; ?></td>
                                                    <td align="center"><?php echo mb_strtoupper($cntTipo); ?></td>   
                                                    <td align="center"><?php echo $cntN; ?></td>
                                                    <td align="center"><?php echo $cntFecha ?></td>
                                                    <td align="left"><?php echo $cntTercero ?></td>
                                                    <td align="left"><?php echo ($descripcion); ?></td>
                                                    <td align="right"><?php echo '$' . number_format($valor, 2, '.', ',') ?></td>
                                                    <td align="right"><?php echo '$' . number_format($base, 2, '.', ',') ?></td>
                                                </tr>
                                    <?php
                                    $subValor = $subValor + $valor;
                                    $subBase = $subBase + $base;
                                }
                            }
                        }

                        #SUBTOTALES
                        ?>
                                    <tr>
                                        <td colspan="7" align="right"><strong>SUBTOTAL:</strong></td>
                                        <td align="right"><strong><?php echo '$' . number_format($subValor, 2, '.', ',') ?></strong></td>
                                        <td align="right"><strong><?php echo '$' . number_format($subBase, 2, '.', ',') ?></strong></td>
                                    </tr>
                        <?php
                        $totalV = $totalV + $subValor;
                        $totalB = $totalB + $subBase;
                    }
                }
                #TOTAL CUENTA
                ?>
                            <tr>
                                <td colspan="7" align="right"><strong>TOTALES :</strong></td>
                                <td align="right"><strong><?php echo '$' . number_format($totalV, 2, '.', ',') ?></strong></td>
                                <td align="right"><strong><?php echo '$' . number_format($totalB, 2, '.', ',') ?></strong></td>
                            </tr>
                <?php
                $total = $total + $totalV;
                $total2 = $total2 + $totalB;
            }
        }
    }
}
?>

        </table>
    </body>
</html>