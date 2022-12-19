<?php
session_start();
require_once '../Conexion/conexion.php';
$factura = ''.$mysqli->real_escape_string(''.$_POST['sltFactura2'].'').'';
$sqltercero = "SELECT tercero FROM gp_factura WHERE id_unico=$factura";
$resultTercero = $mysqli->query($sqltercero);
$rowTercero = mysqli_fetch_row($resultTercero);
$pago = $_SESSION['idpago'];
$sqldetalle = "SELECT id_unico FROM gp_detalle_factura WHERE factura = $factura";
$resultdetalle = $mysqli->query($sqldetalle);
$filadetalle = $resultdetalle->fetch_row();
$detalleFactura = $filadetalle[0];
$valor = ''.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'';
#rowValor[0] {int} valor
$sqlValor = "SELECT SUM(dtf.valor_total_ajustado) AS ULTIMO,
             SUM(dtf.iva) AS IVA,
             SUM(dtf.impoconsumo) AS IMPOCONSUMO,
             SUM(dtf.ajuste_peso) AS AJUSTE_PESO
        FROM gp_detalle_factura dtf 
        LEFT JOIN gp_detalle_pago dfp ON dfp.detalle_factura = dtf.id_unico
        WHERE dtf.factura = $factura";
$resultValor = $mysqli->query($sqlValor);    
$rowValor = mysqli_fetch_row($resultValor);
$sqlPago = "SELECT SUM(dtp.valor) valor,
                SUM(dtp.iva) iva,
                SUM(dtp.impoconsumo) impo,
                SUM(dtp.ajuste_peso) ajuste
            FROM gp_detalle_pago dtp 
            WHERE dtp.pago = $pago";
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
if($valor>=$rowPago[0]){    
    if($valor>=$rowValor[0]){
        $valor = $valorX;
        $ivaR=$valorR-$rowValor[1];
        $valorT=$valorR-$ivaR;
        $iva=$valorT;
        if($iva==$rowValor[1]){            
            $impoR=$ivaR-$rowValor[2];
            $valorT = $ivaR-$impoR;
            $impo=$valorT;
            if($impo==$rowValor[2]){                
                $ajusteR=$impoR-$rowValor[3];
                $valorT1=$impoR-$ajusteR;
                $ajuste=$valorT1;
                $ajusteR;
                if($ajuste==$rowValor[3]){
                    if($ajusteR>=0){                        
                        $saldo_credito=$ajusteR;                        
                    }
                }else{
                    $ajuste=$valorT;
                }              
            }else{
                $impo=$valorT;
                $saldo_credito = 0;
                $ajuste = 0;
                $saldo_credito = 0;
            }
        }else{
            $iva=$valorT;
            $impo = 0;
            $ajuste = 0;
            $saldo_credito = 0;
        }
    }else{
        $valor = ''.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'';
        $suma=$rowPago[0]+$valor;
        if($suma==$rowValor[0]){
            $valor = ''.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'';
            $iva = $rowValor[1];
            $impo = $rowValor[2];
            $ajuste = $rowValor[3];
        }else{
            $valor = ''.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'';
            $iva = 0;
            $impo = 0;
            $ajuste = 0;
            $saldo_credito = 0;    
        }
    }
}else{    
    $valor = ''.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'';
    $suma = $rowPago[0] + $valor;
    if($suma==$rowValor[0]){
        $valor = ''.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'';
        $iva = $rowValor[1];
        $impo = $rowValor[2];
        $ajuste = $rowValor[3];
    }else{
        $valor = ''.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'';
        $iva = 0;
        $impo = 0;
        $ajuste = 0;
        $saldo_credito = 0;    
    }    
}

$sql = "INSERT INTO gp_detalle_pago(detalle_factura,valor,iva,impoconsumo,ajuste_peso,saldo_credito,pago) VALUES($detalleFactura,$valor,$iva,$impo,$ajuste,$saldo_credito,$pago)";
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

<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../registrar_GF_RECAUDO_FACTURACION.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>

