<?php
session_start();
require_once '../Conexion/conexion.php';
#Captura de los envios
$factura = ''.$mysqli->real_escape_string(''.$_POST['sltFactura2'].'').'';
#Captura de la fecha
$fecha = '"'.$mysqli->real_escape_string(''.$_POST['txtFecha'].'').'"';
#Captura del campo tercero
$tercero = '"'.$mysqli->real_escape_string(''.$_POST['txtTercero'].'').'"';
$banco = $_POST['txtBanco'];
#consulta del tercero de la factura
$sqltercero = "SELECT tercero FROM gp_factura WHERE id_unico=$factura";
$resultTercero = $mysqli->query($sqltercero);
$rowTercero = mysqli_fetch_row($resultTercero);
#Id del recaudo o pago
$pago = $mysqli->real_escape_string($_POST['txtIdRecaudo']);
#Consulta para obtener el id de la factura
$sqldetalle = "SELECT id_unico FROM gp_detalle_factura WHERE factura = $factura";
$resultdetalle = $mysqli->query($sqldetalle);
$filadetalle = $resultdetalle->fetch_row();
$detalleFactura = $filadetalle[0];
$conceptorubro = $_POST['sltConcepto'];
#Captura del campo valor
$valor = ''.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'';
#rowValor[0] {int} valor
#Consulta del campo las varibles de valor, iva, impuesto al consumo y ajuste al peso
$sqlValor = "SELECT     SUM(dtf.valor_total_ajustado) AS ULTIMO,
                        SUM(dtf.iva) AS IVA,
                        SUM(dtf.impoconsumo) AS IMPOCONSUMO,
                        SUM(dtf.ajuste_peso) AS AJUSTE_PESO
            FROM        gp_detalle_factura dtf
            LEFT JOIN   gp_detalle_pago dfp ON dfp.detalle_factura = dtf.id_unico
            WHERE       dtf.factura = $factura";
$resultValor = $mysqli->query($sqlValor);
$rowValor = mysqli_fetch_row($resultValor);
#Consulta para obtener del pago el valor, el iva, el impuesto al consumo y el ajuste al peso
$sqlPago = "SELECT  SUM(dtp.valor) valor,
                    SUM(dtp.iva) iva,
                    SUM(dtp.impoconsumo) impo,
                    SUM(dtp.ajuste_peso) ajuste
            FROM    gp_detalle_pago dtp
            WHERE   dtp.pago = $pago";
$resultPago = $mysqli->query($sqlPago);
$rowPago = mysqli_fetch_row($resultPago);
$saldo= $rowValor[0]-$rowPago[0];
$iva = (double) $rowPago[1];
$impo = (double) $rowPago[2];
$ajuste = (double) $rowPago[3];
$saldo_credito = (double) 0;
$valorR = $valor-$rowValor[0];
$valorT = $valor-$valorR;
$valorX = $valorT;

list($vIva, $vValor, $vImpo, $vAjs) = array(0, 0, 0, 0);
$sql_afe = "SELECT      dtp.iva, dtp.valor, dtp.impoconsumo, dtp.ajuste_peso
            FROM        gp_detalle_pago    dtp
            LEFT JOIN   gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
            WHERE       dtf.factura = $factura";
$res_afe = $mysqli->query($sql_afe);
while($row_afe = mysqli_fetch_row($res_afe)){
    $vIva   += $row_afe[0];
    $vValor += $row_afe[1];
    $vImpo  += $row_afe[2];
    $vAjs   += $row_afe[3];
}

if($vIva > 0){
    if($vIva == $rowPago[1]){
        $iva = 0;
    }else{
        if($valor > $rowPago[1]){
            $iva   = $rowPago[1];
            $valor = $valor - $iva;
        }else{
            $iva = 0;
        }
    }
}else{
    if($valor > $rowValor[1]){
        $iva   = $rowValor[1];
        $valor = $valor - $rowValor[1];
    }else{
        $iva = 0;
    }
}

if($vImpo > 0){
    if($vImpo == $rowPago[2]){
        $impo = 0;
    }else{
        if($valor > $rowValor[2]){
            $impo  = $rowValor[2];
            $valor = $valor - $impo;
        }else{
            $impo = 0;
        }
    }
}else{
    if($valor > $rowValor[2]){
        $impo  = $rowValor[2];
        $valor = $valor - $impo;
    }else{
        $impo = 0;
    }
}

if($vAjs > 0){
    if($vAjs == $rowPago[3]){
        $ajuste = 0;
    }else{
        if($valor > $rowValor[3]){
            $ajuste = $rowValor[3];
            $valor  = $valor - $ajuste;
        }else{
            $ajuste = 0;
        }
    }
}else{
    if($valor > $rowValor[3]){
        $ajuste = $rowValor[3];
        $valor  = $valor - $ajuste;
    }else{
        $ajuste = 0;
    }
}

$iddetallePtal  = "";
$idDetalleCnt   = "";
$sqlBanco       = "SELECT cuenta FROM gf_cuenta_bancaria WHERE id_unico = $banco";
$resultBanco    = $mysqli->query($sqlBanco);
if(!empty($conceptorubro)) {
    $sql5 = "SELECT  dtf.id_unico
    FROM        gp_detalle_factura dtf
    LEFT JOIN   gf_detalle_comprobante dtc          ON dtc.id_unico = dtf.detallecomprobante
    LEFT JOIN   gf_detalle_comprobante aft          ON aft.id_unico = dtc.detalleafectado
    LEFT JOIN   gf_detalle_comprobante af1          ON af1.id_unico = aft.detalleafectado
    LEFT JOIN   gf_detalle_comprobante af2          ON af2.id_unico = af1.detalleafectado
    LEFT JOIN   gf_detalle_comprobante af3          ON af3.id_unico = af2.detalleafectado
    LEFT JOIN   gf_detalle_comprobante_pptal dtp    ON dtp.id_unico = dtc.detallecomprobantepptal
    LEFT JOIN   gf_concepto_rubro            crb    ON crb.id_unico = dtp.conceptorubro
    WHERE       dtf.factura              = $factura
    AND         dtf.concepto_tarifa      = $conceptorubro";
    $result5 = $mysqli->query($sql5);
    $row5 = mysqli_fetch_row($result5);
    $detalleFactura = $row5[0];
}else{
    $sql5 = "SELECT  dtf.id_unico
    FROM        gp_detalle_factura dtf
    LEFT JOIN   gf_detalle_comprobante dtc          ON dtc.id_unico = dtf.detallecomprobante
    LEFT JOIN   gf_detalle_comprobante aft          ON aft.id_unico = dtc.detalleafectado
    LEFT JOIN   gf_detalle_comprobante af1          ON af1.id_unico = aft.detalleafectado
    LEFT JOIN   gf_detalle_comprobante af2          ON af2.id_unico = af1.detalleafectado
    LEFT JOIN   gf_detalle_comprobante af3          ON af3.id_unico = af2.detalleafectado
    LEFT JOIN   gf_detalle_comprobante_pptal dtp    ON dtp.id_unico = dtc.detallecomprobantepptal
    WHERE       dtf.factura = $factura";
    $result5 = $mysqli->query($sql5);
    $row5 = mysqli_fetch_row($result5);
    $detalleFactura = $row5[0];
}

if(empty($iva)){
    $iva = 0;
}

$valor = round($valor);

$sql = "INSERT INTO gp_detalle_pago (detalle_factura, valor, iva, impoconsumo, ajuste_peso, saldo_credito, pago)  VALUES ($detalleFactura, $valor, $iva, $impo, $ajuste, $saldo_credito, $pago)";
$resultado = $mysqli->query($sql);

?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/md5.pack.js"></script>
    <script src="../js/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
    <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
</head>
<body>
</body>
</html>
<?php
if($saldo_credito>=0){ ?>
    <script type="text/javascript" >
        $("#mdlSaldoCredito").modal('show');
    </script>
<?php
} ?>
<div class="modal fade" id="mdlSaldoCredito" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Registrar recaudo adicional : <?php echo $saldo_credito; ?></p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnSaldo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información guardada correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se ha podido guardar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<?php if($resultado==true){ ?>
<script type="text/javascript">
    $("#myModal1").modal('show');
    $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.history.go(-1);
    });
</script>
<?php }else{ ?>
<script type="text/javascript">
    $("#myModal2").modal('show');
    $("#ver2").click(function(){
        $("#myModal2").modal('hide');
        window.history.go(-1);
    });
</script>
<?php } ?>

