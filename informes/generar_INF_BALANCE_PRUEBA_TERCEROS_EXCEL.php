<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Balance_Prueba.xls");
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
session_start();
$con            = new ConexionPDO();
$compania       = $_SESSION['compania'];
$calendario     = CAL_GREGORIAN;
$parmanno       = $mysqli->real_escape_string('' . $_POST['sltAnnio'] . '');
$anno           = anno($parmanno);
$mesI           = $mysqli->real_escape_string('' . $_POST['sltmesi'] . '');
$diaI           = '01';
$fechaInicial   = $anno . '-' . $mesI . '-' . $diaI;
$mesF           = $mysqli->real_escape_string('' . $_POST['sltmesf'] . '');
$diaF           = cal_days_in_month($calendario, $mesF, $anno);
$fechaFinal     = $anno . '-' . $mesF . '-' . $diaF;
$fechaComparar  = $anno . '-' . '01-01';
$codigoI        = $mysqli->real_escape_string('' . $_POST['sltcodi'] . '');
$codigoF        = $mysqli->real_escape_string('' . $_POST['sltcodf'] . '');

$bl             = generarBalance($anno, $parmanno, $fechaInicial, $fechaFinal, $codigoI, $codigoF, $compania, 1);

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

$meses = array("01" => 'Enero', "02" => 'Febrero', "03" => 'Marzo', "04" => 'Abril', "05" => 'Mayo', "06" => 'Junio',
    "07" => 'Julio', "08" => 'Agosto', "09" => 'Septiembre', "10" => 'Octubre', "11" => 'Noviembre', "12" => 'Diciembre');
$month1 = $meses[$mesI];
$month2 = $meses[$mesF];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>BALANCE PRUEBA</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="6" align="center"><strong>
                    <br/>&nbsp;
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent . ' : ' . $numeroIdent . "<br/>" . $direccinTer . ' Tel:' . $telefonoTer ?>
                    <br/>&nbsp;
                    <br/>BALANCE DE PRUEBA TERCEROS
                    <br/><?php echo 'Cuentas De ' . $codigoI . ' A ' . $codigoF ?>
                    <br/><?php echo 'Mes Inicial: ' . $month1 . ' - Mes Final: ' . $month2 ?>
                    <br/>&nbsp;</strong>
            </th>
            </tr>
            <tr>
                <td rowspan="2"><center><strong>CÓDIGO</strong></center></td>
                <td rowspan="2"><center><strong>NOMBRE</strong></center></td>
                <td rowspan="2"><center><strong>SALDO INICIAL</strong></center></td>
                <td colspan="2"><center><strong>VALOR</strong></center></td>
                <td rowspan="2"><center><strong>SALDO FINAL</strong></center></td>

            </tr>
            <tr>
                <td><center><strong>DÉBITO</strong></center></td>
                <td><center><strong>CRÉDITO</strong></center></td>
            </tr>
            <?php
            #Consulta Cuentas
            $sql3 = "SELECT DISTINCT 
                                    tem.numero_cuenta   as numcuen, 
                                    tem.nombre          as cnom,
                                    tem.saldo_inicial   as salini,
                                    tem.debito          as deb,
                                    tem.credito         as cred,
                                    tem.nuevo_saldo     as nsal,
                                    cta.auxiliartercero as auxt, 
                                    cta.cuentapuente    as cpuente 
                    FROM            temporal_balance$compania tem
                    LEFT JOIN       gf_cuenta cta       ON cta.codi_cuenta = tem.numero_cuenta
                                    AND cta.parametrizacionanno = $parmanno 
                    WHERE           tem.saldo_inicial   IS NOT NULL 
                    AND             tem.debito          IS NOT NULL 
                    AND             tem.credito         IS NOT NULL 
                    AND             tem.nuevo_saldo     IS NOT NULL
                    ORDER BY        tem.numero_cuenta   ASC";
            $ccuentas = $mysqli->query($sql3);

            $sald = 0;
            $debit = 0;
            $credit = 0;
            $nsald = 0;

            while ($filactas = mysqli_fetch_array($ccuentas)) {
                $sald = (float) ($filactas['salini']);
                $debit = (float) ($filactas['deb']);
                $credit = (float) ($filactas['cred']);
                $nsald = (float) ($filactas['nsal']);
                if($filactas['cpuente']==1){
        
                } else {
                if ($sald == 0 && $debit == 0 && $credit == 0 && $filactas['auxt'] != 1) {
                    #########si los hijos tienen saldo####
        $sh = "SELECT id_unico, 
                SUM(IF(saldo_inicial<0, saldo_inicial*-1,saldo_inicial))   as salID,
                SUM(IF(debito<0, debito*-1,debito))   as debI, 
                SUM(IF(credito<0, credito*-1,credito))   as credI,
                SUM(IF(nuevo_saldo<0, nuevo_saldo*-1,nuevo_saldo))   as salID  
                FROM temporal_balance$compania  
                WHERE saldo_inicial IS NOT NULL 
                AND debito IS NOT NULL AND credito IS NOT NULL 
                AND nuevo_saldo IS NOT NULL 
                AND cod_predecesor = ".$filactas['numcuen']; 
        $sh = $mysqli->query($sh);
        if(mysqli_num_rows($sh)>0){
                $sh = mysqli_fetch_row($sh);
                if($sh[1]==0 && $sh[2]==0 && $sh[3]==0 && $sh[4]==0) {
                    
                } else { ?>
                     <tr>
                        <td><?php echo $filactas['numcuen']; ?></td>
                        <td><?php echo ucwords(mb_strtolower($filactas['cnom'])); ?></td>
                        <td><?php echo number_format($sald, 2, '.', ','); ?></td>
                        <td><?php echo number_format($debit, 2, '.', ','); ?></td>
                        <td><?php echo number_format($credit, 2, '.', ','); ?></td>
                        <td><?php echo number_format($nsald, 2, '.', ','); ?></td>
                    </tr>
               <?PHP  }
            } else {

            }
                } else {
                    #Validamos si auxiliar tercero es 1
                    if ($filactas['auxt'] == 1) {
                        $cod_cuenta = $filactas['numcuen'];
                        ##########################BUSCAR LOS TERCEROS DE ESA CUENTA 
                        $sqlTT = "SELECT  DISTINCT  dc.tercero, dc.cuenta, 
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
                                                tr.apellidodos)) AS NOMBRE, 
                               CONCAT(c.codi_cuenta, ' - ', tr.numeroidentificacion), 
                               c.naturaleza 
                            FROM  gf_detalle_comprobante dc   
                            LEFT JOIN gf_cuenta c ON dc.cuenta    = c.id_unico
                            LEFT JOIN   gf_comprobante_cnt cn       ON cn.id_unico  = dc.comprobante
                            LEFT JOIN   gf_tercero tr              ON tr.id_unico = dc.tercero                        
                            LEFT JOIN
                                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                            LEFT JOIN
                                gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                            WHERE       c.auxiliartercero   = 1 
                            AND         c.codi_cuenta       = '$cod_cuenta' 
                            AND         cn.parametrizacionanno =$parmanno AND cc.id_unico !='20' 
                            ORDER BY    tr.numeroidentificacion ASC";
                        $resultTT = $mysqli->query($sqlTT);
                        $saldoIT = 0;
                        $debitoT = 0;
                        $creditoT = 0;
                        $numtermov = 0;
                        if (mysqli_num_rows($resultTT) > 0) {
                            while ($rowTT = mysqli_fetch_row($resultTT)) {
                                $terceroD = $rowTT[0];
                                $cuentaD = $rowTT[1];
                                ##########BUSCAR MOVIMIENTOS POR TERCERO ###############
                                #SI FECHA INICIAL =01 DE ENERO
                                $fechaPrimera = $anno . '-01-01';
                                if ($fechaInicial == $fechaPrimera) {
                                    #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
                                    $fechaMax = $anno . '-12-31';
                                    $com = "SELECT SUM(valor)
                                            FROM
                                              gf_detalle_comprobante dc
                                            LEFT JOIN
                                              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                            LEFT JOIN
                                              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                            LEFT JOIN
                                              gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                            WHERE
                                              cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                                              AND cc.id_unico = '5' 
                                              AND dc.cuenta = '$cuentaD' 
                                              AND cp.parametrizacionanno =$parmanno    
                                              AND dc.tercero = $terceroD ";
                                    $com = $mysqli->query($com);
                                    if (mysqli_num_rows($com) > 0) {
                                        $saldo = mysqli_fetch_row($com);
                                        $saldoIT = $saldo[0];
                                    } else {
                                        $saldoIT = 0;
                                    }

                                    #DEBITOS
                                    $deb = "SELECT SUM(valor)
                                            FROM
                                              gf_detalle_comprobante dc
                                            LEFT JOIN
                                              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                            LEFT JOIN
                                              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                            LEFT JOIN
                                              gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                            WHERE valor>0 AND 
                                              cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                                              AND cc.id_unico != '5' AND cc.id_unico !='20' 
                                              AND dc.cuenta = '$cuentaD' 
                                              AND cp.parametrizacionanno =$parmanno    
                                              AND dc.tercero = $terceroD";
                                    $debt = $mysqli->query($deb);
                                    if (mysqli_num_rows($debt) > 0) {
                                        $debito = mysqli_fetch_row($debt);
                                        $debitoT = $debito[0];
                                    } else {
                                        $debitoT = 0;
                                    }

                                    #CREDITOS
                                     $cr = "SELECT SUM(valor)
                                        FROM
                                          gf_detalle_comprobante dc
                                        LEFT JOIN
                                          gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                        LEFT JOIN
                                          gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                        LEFT JOIN
                                          gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                        WHERE valor<0 AND 
                                          cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                                          AND cc.id_unico != '5' AND cc.id_unico != '20' 
                                          AND dc.cuenta = '$cuentaD' 
                                          AND cp.parametrizacionanno =$parmanno    
                                          AND dc.tercero = $terceroD";
                                    $cred = $mysqli->query($cr);
                                    if (mysqli_num_rows($cred) > 0) {
                                        $credito = mysqli_fetch_row($cred);
                                        $creditoT = $credito[0];
                                    } else {
                                        $creditoT = 0;
                                    }

                                    #SI FECHA INICIAL !=01 DE ENERO
                                } else {
                                    #TRAE EL SALDO INICIAL
                                    $sInicial = "SELECT SUM(dc.valor) 
                                            from 
                                                gf_detalle_comprobante dc 
                                            LEFT JOIN 
                                                gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                            LEFT JOIN
                                                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                            LEFT JOIN
                                                gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                            WHERE  dc.cuenta = '$cuentaD' 
                                            AND dc.tercero = $terceroD 
                                            AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaInicial' 
                                            AND cn.parametrizacionanno =$parmanno  AND cc.id_unico != '20' ";
                                    $saldt = $mysqli->query($sInicial);
                                    if (mysqli_num_rows($saldt) > 0) {
                                        $saldo = mysqli_fetch_row($saldt);
                                        $saldoIT = $saldo[0];
                                    } else {
                                        $saldoIT = 0;
                                    }
                                    #DEBITOS
                                    $deb = "SELECT SUM(dc.valor) 
                                            from 
                                                gf_detalle_comprobante dc 
                                            LEFT JOIN 
                                                gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                            LEFT JOIN
                                                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                            LEFT JOIN
                                                gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                            WHERE dc.valor>0 AND  dc.cuenta = '$cuentaD' 
                                              AND cn.parametrizacionanno =$parmanno  AND cc.id_unico != '20'   
                                              AND dc.tercero = $terceroD  
                                              AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
                                    $debt = $mysqli->query($deb);
                                    if (mysqli_num_rows($debt) > 0) {
                                        $debito = mysqli_fetch_row($debt);
                                        $debitoT = $debito[0];
                                    } else {
                                        $debitoT = 0;
                                    }
                                    #CREDITOS
                                    $cr = "SELECT SUM(dc.valor) 
                                            FROM 
                                                gf_detalle_comprobante dc 
                                            LEFT JOIN 
                                                gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                            LEFT JOIN
                                                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                            LEFT JOIN
                                                gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                            WHERE dc.valor<0 AND  dc.cuenta = '$cuentaD' 
                                                AND cn.parametrizacionanno =$parmanno  AND cc.id_unico != '20'     
                                                AND dc.tercero = $terceroD  
                                                AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
                                    $cred = $mysqli->query($cr);

                                    if (mysqli_num_rows($cred) > 0) {
                                        $credito = mysqli_fetch_row($cred);
                                        $creditoT = $credito[0];
                                    } else {
                                        $creditoT = 0;
                                    }
                                }
                                #SI LA NATURALEZA ES DEBITO
                                if ($rowTT[4] == '1') {
                                    if ($creditoT < 0) {
                                        $creditoT = (float) substr($creditoT, '1');
                                    }
                                    $saldoNuevoT = $saldoIT + $debitoT - $creditoT;

                                    $d = $debitoT;
                                    $c = $creditoT;
                                    #SI LA NATURALEZA ES CREDITO
                                } else {
                                    if ($creditoT < 0) {
                                        $creditoT = (float) substr($creditoT, '1');
                                    }
                                    $saldoNuevoT = $saldoIT - $creditoT + $debitoT;

                                    $d = $creditoT;
                                    $c = $debitoT;
                                }

                                $saldIT = (float) ($saldoIT);
                                $debitT = (float) ($d);
                                $creditT = (float) ($c);
                                $nsaldT = (float) ($saldoNuevoT);

                                if ($saldIT == 0 && $debitT == 0 && $creditT == 0) {

                                } else {
                                    $numtermov +=1;
                                }
                            }
                            if ($numtermov > 0) {
                                $sald = (float) ($filactas['salini']);
                                $debit = (float) ($filactas['deb']);
                                $credit = (float) ($filactas['cred']);
                                $nsald = (float) ($filactas['nsal']);
                                ?>
                                <tr>
                                    <td><?php echo $filactas['numcuen']; ?></td>
                                    <td><?php echo ucwords(mb_strtolower($filactas['cnom'])); ?></td>
                                    <td><?php echo number_format($sald, 2, '.', ','); ?></td>
                                    <td><?php echo number_format($debit, 2, '.', ','); ?></td>
                                    <td><?php echo number_format($credit, 2, '.', ','); ?></td>
                                    <td><?php echo number_format($nsald, 2, '.', ','); ?></td>
                                </tr>    
                                <?php
                                $cod_cuenta = $filactas['numcuen'];
                                ##########################BUSCAR LOS TERCEROS DE ESA CUENTA 
                                $sqlTT = "SELECT  DISTINCT  dc.tercero, dc.cuenta, 
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
                                            tr.apellidodos)) AS NOMBRE, 
                                           CONCAT(c.codi_cuenta, ' - ', tr.numeroidentificacion), 
                                           c.naturaleza 
                                        FROM  gf_detalle_comprobante dc   
                                        LEFT JOIN gf_cuenta c ON dc.cuenta    = c.id_unico
                                        LEFT JOIN   gf_comprobante_cnt cn       ON cn.id_unico  = dc.comprobante
                                        LEFT JOIN   gf_tercero tr              ON tr.id_unico = dc.tercero                        
                                        LEFT JOIN
                                            gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                        LEFT JOIN
                                            gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                        WHERE       c.auxiliartercero   = 1 
                                        AND         c.codi_cuenta       = '$cod_cuenta' 
                                        AND         cn.parametrizacionanno =$parmanno AND cc.id_unico !='20' 
                                        ORDER BY    tr.numeroidentificacion ASC";
                                $resultTT = $mysqli->query($sqlTT);
                                while ($rowTT = mysqli_fetch_row($resultTT)) {
                                    $terceroD = $rowTT[0];
                                    $cuentaD = $rowTT[1];
                                    ##########BUSCAR MOVIMIENTOS POR TERCERO ###############
                                    #SI FECHA INICIAL =01 DE ENERO
                                    $fechaPrimera = $anno . '-01-01';
                                    if ($fechaInicial == $fechaPrimera) {
                                        #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
                                        $fechaMax = $anno . '-12-31';
                                        $com = "SELECT SUM(valor)
                                                FROM
                                                  gf_detalle_comprobante dc
                                                LEFT JOIN
                                                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                                LEFT JOIN
                                                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                                LEFT JOIN
                                                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                                WHERE
                                                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                                                  AND cc.id_unico = '5' 
                                                  AND dc.cuenta = '$cuentaD' 
                                                  AND dc.tercero = $terceroD AND cp.parametrizacionanno =$parmanno";
                                        $com = $mysqli->query($com);
                                        if (mysqli_num_rows($com) > 0) {
                                            $saldo = mysqli_fetch_row($com);
                                            $saldoIT = $saldo[0];
                                        } else {
                                            $saldoIT = 0;
                                        }

                                        #DEBITOS
                                        $deb = "SELECT SUM(valor)
                                            FROM
                                              gf_detalle_comprobante dc
                                            LEFT JOIN
                                              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                            LEFT JOIN
                                              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                            LEFT JOIN
                                              gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                            WHERE valor>0 AND 
                                              cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                                              AND cc.id_unico != '5' AND cc.id_unico != '20' 
                                              AND dc.cuenta = '$cuentaD' 
                                              AND dc.tercero = $terceroD AND cp.parametrizacionanno =$parmanno";
                                        $debt = $mysqli->query($deb);
                                        if (mysqli_num_rows($debt) > 0) {
                                            $debito = mysqli_fetch_row($debt);
                                            $debitoT = $debito[0];
                                        } else {
                                            $debitoT = 0;
                                        }

                                        #CREDITOS
                                        $cr = "SELECT SUM(valor)
                                            FROM
                                              gf_detalle_comprobante dc
                                            LEFT JOIN
                                              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                            LEFT JOIN
                                              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                            LEFT JOIN
                                              gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                            WHERE valor<0 AND 
                                              cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                                              AND cc.id_unico != '5' AND cc.id_unico != '20' 
                                              AND dc.cuenta = '$cuentaD' 
                                              AND dc.tercero = $terceroD AND cp.parametrizacionanno =$parmanno";
                                        $cred = $mysqli->query($cr);
                                        if (mysqli_num_rows($cred) > 0) {
                                            $credito = mysqli_fetch_row($cred);
                                            $creditoT = $credito[0];
                                        } else {
                                            $creditoT = 0;
                                        }

                                        #SI FECHA INICIAL !=01 DE ENERO
                                    } else {
                                        #TRAE EL SALDO INICIAL
                                        $sInicial = "SELECT SUM(dc.valor) 
                                            from gf_detalle_comprobante dc 
                                            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                            LEFT JOIN
                                              gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                            LEFT JOIN
                                              gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                            WHERE  dc.cuenta = '$cuentaD' 
                                            AND dc.tercero = $terceroD 
                                            AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20' 
                                            AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaInicial' ";
                                        $sald = $mysqli->query($sInicial);
                                        if (mysqli_num_rows($sald) > 0) {
                                            $saldo = mysqli_fetch_row($sald);
                                            $saldoIT = $saldo[0];
                                        } else {
                                            $saldoIT = 0;
                                        }
                                        #DEBITOS
                                        $deb = "SELECT SUM(dc.valor) 
                                                FROM  
                                                    gf_detalle_comprobante dc 
                                                LEFT JOIN 
                                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                                LEFT JOIN
                                                  gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                                LEFT JOIN
                                                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                                WHERE dc.valor>0 AND  dc.cuenta = '$cuentaD' 
                                                    AND dc.tercero = $terceroD  
                                                    AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20' 
                                                    AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
                                        $debt = $mysqli->query($deb);
                                        if (mysqli_num_rows($debt) > 0) {
                                            $debito = mysqli_fetch_row($debt);
                                            $debitoT = $debito[0];
                                        } else {
                                            $debitoT = 0;
                                        }
                                        #CREDITOS
                                        $cr = "SELECT SUM(dc.valor) 
                                            FROM  
                                                gf_detalle_comprobante dc 
                                            LEFT JOIN 
                                                gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                            LEFT JOIN
                                              gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                            LEFT JOIN
                                              gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                            WHERE dc.valor<0 AND  dc.cuenta = '$cuentaD' 
                                                AND dc.tercero = $terceroD  
                                                AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20' 
                                                AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
                                        $cred = $mysqli->query($cr);

                                        if (mysqli_num_rows($cred) > 0) {
                                            $credito = mysqli_fetch_row($cred);
                                            $creditoT = $credito[0];
                                        } else {
                                            $creditoT = 0;
                                        }
                                    }
                                    #SI LA NATURALEZA ES DEBITO
                                    if ($rowTT[4] == '1') {
                                        if ($creditoT < 0) {
                                            $creditoT = (float) substr($creditoT, '1');
                                        }
                                        $saldoNuevoT = $saldoIT + $debitoT - $creditoT;

                                        $d = $debitoT;
                                        $c = $creditoT;
                                        #SI LA NATURALEZA ES CREDITO
                                    } else {
                                        if ($creditoT < 0) {
                                            $creditoT = (float) substr($creditoT, '1');
                                        }
                                        $saldoNuevoT = $saldoIT - $creditoT + $debitoT;

                                        $d = $creditoT;
                                        $c = $debitoT;
                                    }
                                    
                                    $saldIT = (float) ($saldoIT);
                                    $debitT = (float) ($d);
                                    $creditT = (float) ($c);
                                    $nsaldT = (float) ($saldoNuevoT);

                                    if ($saldIT == 0 && $debitT == 0 && $creditT == 0) {
                                        
                                    } else {
                                        ?>

                                        <tr>
                                            <td><?php echo $rowTT[3]; ?></td>
                                            <td><?php echo (ucwords(mb_strtolower($rowTT[2]))); ?></td>
                                            <td><?php echo number_format($saldIT, 2, '.', ','); ?></td>
                                            <td><?php echo number_format($debitT, 2, '.', ','); ?></td>
                                            <td><?php echo number_format($creditT, 2, '.', ','); ?></td>
                                            <td><?php echo number_format($nsaldT, 2, '.', ','); ?></td>
                                        </tr>   

                                        <?php
                                    }
                                }
                                ?>
                            <?PHP
                            }
                        }
                         else {
                            if ($sald == 0 && $debit == 0 && $credit == 0 && $nsald == 0) {

                            } else { ?>
                                <tr>
                                    <td><?php echo $filactas['numcuen']; ?></td>
                                    <td><?php echo ucwords(mb_strtolower($filactas['cnom'])); ?></td>
                                    <td><?php echo number_format($sald, 2, '.', ','); ?></td>
                                    <td><?php echo number_format($debit, 2, '.', ','); ?></td>
                                    <td><?php echo number_format($credit, 2, '.', ','); ?></td>
                                    <td><?php echo number_format($nsald, 2, '.', ','); ?></td>
                                </tr>  
                        <?php
                            }
                        }
                    } else {
                        ?>
                        <tr>
                            <td><?php echo $filactas['numcuen']; ?></td>
                            <td><?php echo ucwords(mb_strtolower($filactas['cnom'])); ?></td>
                            <td><?php echo number_format($sald, 2, '.', ','); ?></td>
                            <td><?php echo number_format($debit, 2, '.', ','); ?></td>
                            <td><?php echo number_format($credit, 2, '.', ','); ?></td>
                            <td><?php echo number_format($nsald, 2, '.', ','); ?></td>
                        </tr>    
                    <?php
                    }
                }
                }
            }
            ?>
            <tr>
                <td colspan="3"><strong>
                        TOTALES
                    </strong>
                </td>
                <td><?php echo number_format($bl["totaldeb"], 2, '.', ',') ?></td>
                <td><?php echo number_format($bl["totalcred"], 2, '.', ',') ?></td>
                <td></td>
            </tr>
            <?php
            ##########################RESUMEN#################################################
        $rs = "SELECT DISTINCT id_unico as id, 
                 numero_cuenta  as codigo, 
                 nombre         as nombre, 
                 saldo_inicial  as inicial, 
                 debito         as debito, 
                 credito        as credito, 
                 nuevo_saldo    as nuevo, 
                 naturaleza     as naturalezaR 
               FROM temporal_balance$compania  
               WHERE LENGTH(numero_cuenta) = (1) ORDER BY numero_cuenta ASC";
        $rs = $mysqli->query($rs);

        ?>
           <tr>
               <td colspan="6"><center><strong>RESUMEN</strong></center></td>
           </tr>
           <tr>
               <td rowspan="2"><center><strong>CÓDIGO</strong></center></td>
               <td rowspan="2"><center><strong>NOMBRE</strong></center></td>
               <td rowspan="2"><center><strong>SALDO INICIAL</strong></center></td>
               <td colspan="2"><center><strong>VALOR</strong></center></td>
               <td rowspan="2"><center><strong>SALDO FINAL</strong></center></td>

           </tr>
           <tr>
               <td><center><strong>DÉBITO</strong></center></td>
               <td><center><strong>CRÉDITO</strong></center></td>
           </tr>  
       <?php 
       $anteriortotal =0;
       $debitototal=0;
       $creditototal=0;
       $nuevototal=0;

        while ($row1 = mysqli_fetch_array($rs)) {
               $sald   = (float)($row1['inicial']);
               $debit  = (float)($row1['debito']);
               $credit = (float)($row1['credito']);
               $nsald  = (float)($row1['nuevo']);
               $naturalezaR = $row1['naturalezaR'];

               ?>
               <tr>
                   <td><?php echo $row1['codigo']; ?></td>
                   <td><?php echo ucwords(mb_strtolower($row1['nombre']));?></td>
                   <td><?php echo number_format($sald,2,'.',',');?></td>
                   <td><?php echo number_format($debit,2,'.',',');?></td>
                   <td><?php echo number_format($credit,2,'.',',');?></td>
                   <td><?php echo number_format($nsald,2,'.',',');?></td>
                </tr> 
               <?php

               $debitototal +=$debit;
               $creditototal +=$credit;
               switch ($row1['codigo']){
                   case 1:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   case 2:
                       $anteriortotal -=$sald;
                       $nuevototal -=$nsald;
                   break;
                   case 3:
                       $anteriortotal -=$sald;
                       $nuevototal -=$nsald;
                   break;
                   case 4:
                       $anteriortotal -=$sald;
                       $nuevototal -=$nsald;
                   break;
                   case 5:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   case 6:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   case 7:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   default :
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;


               }


        }
        ##################################################################################
        ##############################TOTALES#############################################
        ?>
        <tr>
               <td colspan="2" align="center"><center><strong>TOTALES</strong></center></td>
               <td><strong><?php echo number_format($anteriortotal,2,'.',',')?></strong></td>
               <td><strong><?php echo number_format($debitototal,2,'.',',')?></strong></td>
               <td><strong><?php echo number_format($creditototal,2,'.',',')?></strong></td>
               <td><strong><?php echo number_format($nuevototal,2,'.',',')?></strong></td>
           </tr>
        </table>
    </body>
</html>