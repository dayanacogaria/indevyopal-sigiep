<?php
#######################################################################################################
#           *********       Modificaciones      *********       #
#######################################################################################################
#21/12/2017 |Erica G.| No tome en cuenta el comprobante cierre - Parametrización año
#28/06/2017 |ERICA G.|QUEDO SOLO PARA TESORERIA 
#06/04/2017 |Erica G |MODIFICACION RESULTADOS Y VERIFICACION CONSULTAS
#07/03/2017 |Erica G |ARCHIVO CREADO
#######################################################################################################
header("Content-type: application/vnd.ms-excel");

require_once("../Conexion/conexion.php");
session_start();
header("Content-Disposition: attachment; filename=Informe_Tesoreria_Caja_Bancos.xls");

 
?>
<?php
$calendario = CAL_GREGORIAN;
$parmanno = $mysqli->real_escape_string('' . $_POST['sltAnnio'] . '');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno = $an[0];

$mesI = $mysqli->real_escape_string('' . $_POST['sltmesi'] . '');
$diaI = '01';
$fechaInicial = $anno . '-' . $mesI . '-' . $diaI;
$mesF = $mysqli->real_escape_string('' . $_POST['sltmesf'] . '');
$diaF = cal_days_in_month($calendario, $mesF, $anno);
$fechaFinal = $anno . '-' . $mesF . '-' . $diaF;
$fechaComparar = $anno . '-' . '01-01';
$codigoI = $mysqli->real_escape_string('' . $_POST['sltcodi'] . '');
$codigoF = $mysqli->real_escape_string('' . $_POST['sltcodf'] . '');


#VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_tesoreria ';
$mysqli->query($vaciarTabla);

#CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
 $select = "SELECT DISTINCT
            c.id_unico, 
            c.codi_cuenta,
            c.nombre,
            c.naturaleza,
            ch.codi_cuenta 
          FROM
            gf_cuenta c
          LEFT JOIN
            gf_cuenta ch ON c.predecesor = ch.id_unico
          WHERE c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF' 
            AND (c.clasecuenta = 11 OR c.clasecuenta = 12 )
            AND c.parametrizacionanno = $parmanno   
          ORDER BY 
            c.codi_cuenta DESC";
$select1 = $mysqli->query($select);


while ($row = mysqli_fetch_row($select1)) {
    #GUARDA LOS DATOS EN LA TABLA TEMPORAL
    $insert = "INSERT INTO temporal_consulta_tesoreria "
            . "(id_cuenta, numero_cuenta, nombre,cod_predecesor, naturaleza) "
            . "VALUES ('$row[0]','$row[1]','$row[2]','$row[4]','$row[3]' )";
    $mysqli->query($insert);
}


//CONSULTO LAS CUENTAS QUE TENGAN MOVIMIENTO

$mov = "SELECT DISTINCT c.id_unico, c.codi_cuenta, "
        . "c.nombre, c.naturaleza FROM gf_detalle_comprobante dc "
        . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
        . "WHERE c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF' AND c.parametrizacionanno = $parmanno "
        . "ORDER BY c.codi_cuenta DESC";
$mov = $mysqli->query($mov);
$totaldeb = 0;
$totalcred = 0;
$totalsaldoI = 0;
$totalsaldoF = 0;

while ($row = mysqli_fetch_row($mov)) {
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
                      AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
        $com = $mysqli->query($com);
        if (mysqli_num_rows($com) > 0) {
            $saldo = mysqli_fetch_row($com);
            if(($saldo[0]=="" || $saldo[0]=='NULL')){
                $saldo = 0;
            } else {
                $saldo = $saldo[0];
            }
        } else {
            $saldo = 0;
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
                      AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
        $debt = $mysqli->query($deb);
        if (mysqli_num_rows($debt) > 0) {
            $debito = mysqli_fetch_row($debt);
            if(($debito[0]=="" || $debito[0]=='NULL')){
                $debito = 0;
            } else {
                $debito = $debito[0];
            }
        } else {
            $debito = 0;
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
                      AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
        $cred = $mysqli->query($cr);
        if (mysqli_num_rows($cred) > 0) {
            $credito = mysqli_fetch_row($cred);
            if(($credito[0]=="" || $credito[0]=='NULL')){
                $credito = 0;
            } else {
                $credito = $credito[0];
            }
        } else {
            $credito = 0;
        }

#SI FECHA INICIAL !=01 DE ENERO
    } else {
        #TRAE EL SALDO INICIAL
        $sInicial = "SELECT SUM(dc.valor) 
                FROM 
                    gf_detalle_comprobante dc 
                LEFT JOIN 
                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN
                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                LEFT JOIN
                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                WHERE dc.cuenta='$row[0]' 
                AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaInicial' 
                AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20'";
        $sald = $mysqli->query($sInicial);
        if (mysqli_num_rows($sald) > 0) {
            $saldo = mysqli_fetch_row($sald);
            if(($saldo[0]=="" || $saldo[0]=='NULL')){
                $saldo = 0;
            } else {
                $saldo = $saldo[0];
            }
        } else {
            $saldo = 0;
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
                WHERE dc.valor>0 AND dc.cuenta='$row[0]' AND 
                    cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                    AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20'";
        $debt = $mysqli->query($deb);
        if (mysqli_num_rows($debt) > 0) {
            $debito = mysqli_fetch_row($debt);
            if(($debito[0]=="" || $debito[0]=='NULL')){
                $debito = 0;
            } else {
                $debito = $debito[0];
            }
        } else {
            $debito = 0;
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
                WHERE dc.valor<0 AND dc.cuenta='$row[0]' AND 
                cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20'";
        $cred = $mysqli->query($cr);

        if (mysqli_num_rows($cred) > 0) {
            $credito = mysqli_fetch_row($cred);
             if(($credito[0]=="" || $credito[0]=='NULL')){
                $credito = 0;
            } else {
                $credito = $credito[0];
            }
        } else {
            $credito = 0;
        }
    }
    #SI LA NATURALEZA ES DEBITO
    if ($row[3] == '1') {
        if ($credito < 0) {
            $credito = (float) substr($credito, '1');
        }
        $saldoNuevo = $saldo + $debito - $credito;
        $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial ='$saldo', "
                . "debito = '$debito', "
                . "credito ='$credito', "
                . "nuevo_saldo ='$saldoNuevo' "
                . "WHERE id_cuenta ='$row[0]'";
        $update = $mysqli->query($update);

        $d = $debito;
        $c = $credito;
        #SI LA NATURALEZA ES CREDITO
    } else {
        if ($credito < 0) {
            $credito = (float) substr($credito, '1');
        }
        $saldoNuevo = $saldo - $credito + $debito;
        $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial ='$saldo', "
                . "debito = '$credito', "
                . "credito ='$debito', "
                . "nuevo_saldo ='$saldoNuevo' "
                . "WHERE id_cuenta ='$row[0]'";
        $update = $mysqli->query($update);

        $d = $credito;
        $c = $debito;
    }

    //var_dump($row[1]>=$codigoI || $row[1]<=$codigoF);
    if ($row[1] >= $codigoI || $row[1] <= $codigoF) {

        $totaldeb = $totaldeb + $d;
        $totalcred = $totalcred + $c;
    }
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
            <br/>BALANCE TESORERIA CAJA Y BANCOS
            <br/><?php echo 'Cuentas De ' . $codigoI . ' A ' . $codigoF ?>
            <br/><?php echo 'Mes Inicial: ' . $month1 . ' - Mes Final: ' . $month2 ?>
            <br/>&nbsp;</strong>
    </th>
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
                        numero_cuenta   as numcuen, 
                        nombre          as cnom,
                        saldo_inicial   as salini,
                        debito          as deb,
                        credito         as cred,
                        nuevo_saldo     as nsal
from temporal_consulta_tesoreria 
WHERE saldo_inicial IS NOT NULL AND debito IS NOT NULL AND credito IS NOT NULL AND nuevo_saldo IS NOT NULL
ORDER BY numero_cuenta ASC";
$ccuentas = $mysqli->query($sql3);

$sald = 0;
$debit = 0;
$credit = 0;
$nsald = 0;

while ($filactas = mysqli_fetch_array($ccuentas)) 
{
    $sald   = (float)($filactas['salini']);
    $debit  = (float)($filactas['deb']);
    $credit = (float)($filactas['cred']);
    $nsald  = (float)($filactas['nsal']);

    if ($sald == 0  && $debit == 0  && $credit == 0 )
    { } else { ?>
    <tr>
        <td><?php echo $filactas['numcuen']; ?></td>
        <td><?php echo ucwords(mb_strtolower($filactas['cnom']));?></td>
        <td><?php echo number_format($sald,2,'.',',');?></td>
        <td><?php echo number_format($debit,2,'.',',');?></td>
        <td><?php echo number_format($credit,2,'.',',');?></td>
        <td><?php echo number_format($nsald,2,'.',',');?></td>
    </tr>    
<?php } }?>
    
    <tr>
        <td colspan="3"><strong>
            TOTALES
            </strong>
        </td>
        <td><?php echo number_format($totaldeb,2,'.',',')?></td>
        <td><?php echo number_format($totalcred,2,'.',',')?></td>
        <td></td>
    </tr>
 </table>
</body>
</html>