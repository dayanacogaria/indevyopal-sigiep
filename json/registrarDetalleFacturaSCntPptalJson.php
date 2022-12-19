<?php
##################################################################################################################################################################
# Modificaciones
##################################################################################################################################################################
# Fecha de Modificación : 24/08/2017
# Modificado por        : Alexander Numpaque
# Descripción           : Se incluyo registro y validación para cuando los campos de concepto rubro y rubro fuente esten vacios, se obtenga el valor de los dos
# nuevos campos enviados desde el formulario, de esta forma hacer el registro a gf_rubro_fuente
##################################################################################################################################################################
# Fecha de Modificación : 23/06/2017
# Modificado por        : Jhon Numpaque
# Descripción           : Se cambio enlaze en el detalle de factura con presupuesto, se conectara directamente con la cuenta debito
##################################################################################################################################################################
# Fecha de Modificación : 15/05/2017
# Modificado por        : Jhon Numpaque
# Descripción           : Se valido enlaze de factura y detalle contable usando la cuenta debito como enlace
##################################################################################################################################################################
# Fecha de Modificación : 03/05/2017
# Modificado por        : Jhon Numpaque
# Descripción           : Se valido que al hacer las consultas de ultimo registro de detalle se hagan respecto al comprobante
##################################################################################################################################################################
# Fecha de Modificación : 01/03/2017
# Modificado por        : Jhon Numpaque
# Descripción           : Se verifico consultas para guardado de la naturaleza y verifico registro en cascada a cnt, pptal y detalle factura
##################################################################################################################################################################
# 02/03/2017 |ERICA G. | ARREGLO GUARDAR DETALLES CNT
##################################################################################################################################################################
# Fecha de Modificación : 01/03/2017
# Modificado por        : Jhon Numpaque
# Hora de Modificación  : 10 : 30
# Descripción           : Se agrego registro para dos detalles que cuya cuenta es debtio y tienen valores de impoconsumo e via
#
###################################################################################################################################################################
# Fecha : 23/02/2017
# Hora  : 02:50 p.m
# Modificó : Jhon Numpaque
# Descripción : Se agrego Validación recepción del valor ya que puede venir del select con el nombre sltValor o del input txtValor.
#
###################################################################################################################################################################
session_start();
require_once '../Conexion/conexion.php';

# Captura de variables
$factura = '"'.$mysqli->real_escape_string(''.$_POST['txtIdFactura'].'').'"';
$concepto = '"'.$mysqli->real_escape_string(''.$_POST['sltConcepto'].'').'"';
$iva = $mysqli->real_escape_string(''.$_POST['txtIva'].'');
$impoconsumo = $mysqli->real_escape_string(''.$_POST['txtImpoconsumo'].'');
$ajustePeso = $mysqli->real_escape_string(''.$_POST['txtAjustePeso'].'');
$valorTotalAjuste = $mysqli->real_escape_string(''.$_POST['txtValorA'].'');;
###################################################################################################################################################################
# Validación de campo valor enviado
if(!empty($_POST['sltValor'])){
    $valor = '"'.$mysqli->real_escape_string(''.$_POST['sltValor'].'').'"';
}else{
    $valor = '"'.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'"';
}
if(empty($_POST['txtCantidad'])){
    $cantidad = "'1'";
}else{
    $cantidad = '"'.$mysqli->real_escape_string(''.$_POST['txtCantidad'].'').'"';
}
#
###################################################################################################################################################################
# Registro del Detalle de factura
$sql = "INSERT INTO gp_detalle_factura(factura, concepto_tarifa, valor, cantidad, iva, impoconsumo, ajuste_peso,valor_total_ajustado) VALUES ($factura,$concepto,$valor,$cantidad,$iva,$impoconsumo,$ajustePeso,$valorTotalAjuste)";
$resultado = $mysqli->query($sql);
###################################################################################################################################################################
# Consultamos el ultimo id registrado de detalle de factura
$sqlDetalleFactura="select max(id_unico) from gp_detalle_factura where factura=$factura";
$resultDetalleFactura=$mysqli->query($sqlDetalleFactura);
$detalleFactura= mysqli_fetch_row($resultDetalleFactura);
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
    <script src="../js/md5.pack.js"></script>
    <!--<script src="../js/jquery.min.js"></script>-->
    <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
</head>
<body>
</body>
</html>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
<?php if($resultado==true){
    ###############################################################################################################################################################
    # Consulta de concepto financiero y verificamos si existe el concepto
    $sqlConcepto="select distinct con.concepto_financiero from gp_concepto con where con.id_unico=$concepto";
    $resultConcepto=$mysqli->query($sqlConcepto);
    $conceptoFinanciero= mysqli_fetch_row($resultConcepto);
    ###############################################################################################################################################################
    # Registro para detalle comprobante pptal si existe el envió de la id pptal se realizara el registro
    if(!empty($_POST['txtIdPptal'])){
        ###########################################################################################################################################################
        # Captura de valores
        $idPptal=$_POST['txtIdPptal'];
        if(empty($_POST['sltRubroFuente'])){
            $fuente = '"'.$mysqli->real_escape_string(''.$_POST['sltFuentes'].'').'"';
            $rubro_s = '"'.$mysqli->real_escape_string(''.$_POST['sltRubros'].'').'"';

            $sql_cr = "SELECT id_unico FROM gf_concepto_rubro WHERE rubro = $rubro_s AND concepto = $conceptoFinanciero[0]";
            $res_cr = $mysqli->query($sql_cr);
            $row_cr = mysqli_fetch_row($res_cr);
            $concepto1='"'.$mysqli->real_escape_string(''.$row_cr[0].'').'"';

            $sql_i = "INSERT INTO gf_rubro_fuente(rubro, fuente) VALUES($rubro_s, $fuente)";
            $res_i = $mysqli->query($sql_i);

            $sql_rf = "SELECT id_unico FROM gf_rubro_fuente WHERE rubro = $rubro_s AND fuente = $fuente";
            $res_rf = $mysqli->query($sql_rf);
            $row_rf = mysqli_fetch_array($res_rf);
            $rubroFuente1 = $row_rf[0];
        }else{
            $rubroFuente1='"'.$mysqli->real_escape_string(''.$_POST['sltRubroFuente'].'').'"';
            $concepto1='"'.$mysqli->real_escape_string(''.$_POST['txtConceptoRubro'].'').'"';
        }
        $tercero1='"'.$mysqli->real_escape_string(''.$_POST['txtTercero'].'').'"';
        $centroCosto1='"'.$mysqli->real_escape_string(''.$_POST['txtCentroCosto'].'').'"';
        $descripcion1 ='"'.$mysqli->real_escape_string(''.$_POST['txtDescr'].'').'"';
        #
        ###########################################################################################################################################################
        # Insertamos dos veces en comprobante pptal para guardar las cuentas
        $insertPptal1="insert into gf_detalle_comprobante_pptal(descripcion,valor,comprobantepptal,rubrofuente,conceptoRubro,tercero,proyecto) values($descripcion1,($valor*$cantidad)+$ajustePeso,$idPptal,$rubroFuente1,$concepto1,$tercero1,'2147483647');";
        $resultPptal1=$mysqli->query($insertPptal1);
        #
        ###########################################################################################################################################################
    }
    $detAF = ""; $cuenta_debito = "";
    ###############################################################################################################################################################
    # Validamos que tenga valor la consulta
    if(!empty($conceptoFinanciero[0])){
        ###########################################################################################################################################################
        # Registro para detalle comprobante si existe el envió de la id de comprobante se realizara el registro
        if(!empty($_POST['txtIdCnt'])){
            #######################################################################################################################################################
            # Id de comprobante contable
            $idCnt = $_POST['txtIdCnt'];
            #
            #######################################################################################################################################################
            # Si existe el campo rubro fuente traera los siguientes campos como el id del concepto
            if(!empty($_POST['sltRubroFuente'])){
                # Captura de variables
                $rubroFuente='"'.$mysqli->real_escape_string(''.$_POST['sltRubroFuente'].'').'"';
                $conceptoF='"'.$mysqli->real_escape_string(''.$_POST['txtConceptoRubro'].'').'"';
            }else{
                $sql_rf = "SELECT id_unico FROM gf_rubro_fuente WHERE rubro = $rubro_s AND fuente = $fuente";
                $res_rf = $mysqli->query($sql_rf);
                $row_rf = mysqli_fetch_array($res_rf);
                $rubroFuente='"'.$mysqli->real_escape_string(''.$row_rf[0].'').'"';

                $sql_cr = "SELECT id_unico FROM gf_concepto_rubro WHERE rubro = $rubro_s AND concepto = $conceptoFinanciero[0]";
                $res_cr = $mysqli->query($sql_cr);
                $row_cr = mysqli_fetch_row($res_cr);
                $conceptoF = '"'.$mysqli->real_escape_string(''.$row_cr[0].'').'"';
            }
            ###################################################################################################################################################
            # Consultamos el ultimo detalle de comprobante pptal registrado referente a la id del comprobante
            $sqlDetallePPtal="select max(id_unico) from gf_detalle_comprobante_pptal where comprobantepptal=$idPptal";
            $resultDetallePptal=$mysqli->query($sqlDetallePPtal);
            $idDetallePptal= mysqli_fetch_row($resultDetallePptal);
            ###################################################################################################################################################
            # Captura de variables
            $rubroFuente='"'.$mysqli->real_escape_string(''.$_POST['sltRubroFuente'].'').'"';
            $conceptoF='"'.$mysqli->real_escape_string(''.$_POST['txtConceptoRubro'].'').'"';
            $fecha=$_POST['txtFecha'];
            $tercero='"'.$mysqli->real_escape_string(''.$_POST['txtTercero'].'').'"';
            $centroCosto='"'.$mysqli->real_escape_string(''.$_POST['txtCentroCosto'].'').'"';
            $descripcion ='"'.$mysqli->real_escape_string(''.$_POST['txtDescr'].'').'"';
            ###################################################################################################################################################
            # Consultamos la tabla concepto rubro cuenta y treamos a cuenta_credito_causación y a cuenta debito causación
            $sqlConceptoRubroCuenta="SELECT cnrbcta.cuenta_debito,
                                            cnrbcta.cuenta_credito,
                                            cnrbcta.cuenta_iva,
                                            cnrbcta.cuenta_impoconsumo
                                    FROM    gf_concepto_rubro_cuenta cnrbcta
                                    WHERE   cnrbcta.concepto_rubro =    $conceptoF
                                    AND     cnrbcta.cuenta_debito is not null
                                    AND     cnrbcta.cuenta_credito is not null";
            $resultConceptoRubroCuenta=$mysqli->query($sqlConceptoRubroCuenta);
            $cuentas = mysqli_fetch_row($resultConceptoRubroCuenta);
            ###################################################################################################################################################
            # Validamos que la consulta retorne valores
            if(!empty($cuentas[0])){
                $cuenta_debito = $cuentas[0];
                ###############################################################################################################################################
                # Consultamos la naturaleza de la cuenta
                # (OJO: POR SI LAS CUENTAS NO ESTAN UBICADAS CORRECTAMENTE Si la naturaleza de las cuentas están mal configurada, la variable $naturaleza1[0] cambiela por 1, ya que es el registro de la cuenta débito y la variable naturaleza2[0] por 2 ya que es el registro de la cuenta crédtio) O.O
                $sqlNaturalezaCuenta1="select naturaleza from gf_cuenta where id_unico=$cuentas[0]";
                $resultNaturaleza1=$mysqli->query($sqlNaturalezaCuenta1);
                $naturaleza1= mysqli_fetch_row($resultNaturaleza1);
                ###############################################################################################################################################
                # Realizamos el insertado de datos para la tabla detalle comprobante cnt para cuenta debito
                $insertComprobante1="insert into gf_detalle_comprobante(fecha,descripcion,valor,valorejecucion,comprobante,cuenta,naturaleza,tercero,proyecto,centrocosto,detallecomprobantepptal)
                values('$fecha',$descripcion,($valor*$cantidad)+$ajustePeso,($valor*$cantidad)+$ajustePeso,$idCnt,$cuentas[0],1,$tercero,'2147483647',$centroCosto,$idDetallePptal[0])";
                $resultComprobante1=$mysqli->query($insertComprobante1);
                ###############################################################################################################################################
                # Consulta para obtener el ultimo penultimo registro
                $sqlD = "SELECT max(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idCnt";
                $resultD = $mysqli->query($sqlD);
                $detA = mysqli_fetch_row($resultD);
                $detAF = $detA[0];
                ###############################################################################################################################################
                # Consulta de naturaleza de la cuenta numero 2
                $sqlNaturalezaCuenta2="select naturaleza from gf_cuenta where id_unico=$cuentas[1]";
                $resultNaturaleza2=$mysqli->query($sqlNaturalezaCuenta2);
                $naturaleza2= mysqli_fetch_row($resultNaturaleza2);
                ###############################################################################################################################################
                # insertamos en la tabla comprobante cnt para cuenta credito
                $insertComprobante2="insert into gf_detalle_comprobante(fecha,descripcion,valor,valorejecucion,comprobante,cuenta,naturaleza,tercero,proyecto,centrocosto,detallecomprobantepptal,detalleafectado) values('$fecha',$descripcion,($valor*$cantidad)+$ajustePeso,($valor*$cantidad)+$ajustePeso,$idCnt,$cuentas[1],2,$tercero,'2147483647',$centroCosto,$idDetallePptal[0],$detA[0])";
                $resultComprobante2=$mysqli->query($insertComprobante2);
                ###############################################################################################################################################
                if(!empty($cuentas[2])){
                    ###########################################################################################################################################
                    # Consulta para obtener el ultimo penultimo registro
                    $sqlD1 = "SELECT max(id_unico) FROM gf_detalle_comprobante";
                    $resultD1 = $mysqli->query($sqlD1);
                    $detA1 = mysqli_fetch_row($resultD1);
                    #
                    ###########################################################################################################################################
                    # Consulta de naturaleza de la cuenta de iva
                    $sqlNaturalezaCuenta3="select naturaleza from gf_cuenta where id_unico=$cuentas[2]";
                    $resultNaturaleza3=$mysqli->query($sqlNaturalezaCuenta3);
                    $naturaleza3= mysqli_fetch_row($resultNaturaleza3);
                    #
                    ############################################################################################################################################
                    # Validamos que el campo iva no venga vacio, e insertamos el detalle con la cuenta iva
                   // echo $iva;
                   // var_dump (!empty($iva) || $iva!='0' || $iva!='0.00');
                    if(!empty($iva) || $iva!='0' || $iva!='0.00'){
                        $insertCuentaIva = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, valor, valorejecucion, comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detalleafectado, detallecomprobantepptal)
                        VALUES('$fecha', $descripcion, $iva, $iva ,$idCnt, $cuentas[2], 2, $tercero, '2147483647', $centroCosto, $detA1[0],$idDetallePptal[0])";
                        $resultCuentaIva = $mysqli->query($insertCuentaIva);
                        #######################################################################################################################################
                        # Consulta para obtener el ultimo penultimo registro
                        $sqlD2 = "SELECT max(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idCnt";
                        $resultD2 = $mysqli->query($sqlD2);
                        $detA2 = mysqli_fetch_row($resultD2);
                        #
                        #######################################################################################################################################
                        # Registro de cuenta debito con valor de iva
                        $insertCuentaIvaD = "INSERT INTO gf_detalle_comprobante (fecha, descripcion, valor, valorejecucion, comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detalleafectado, detallecomprobantepptal) VALUES('$fecha', $descripcion, $iva, $iva ,$idCnt, $cuentas[0], 1, $tercero, '2147483647', $centroCosto, $detA2[0], $idDetallePptal[0])";
                        $resultCuentaIvaD = $mysqli->query($insertCuentaIvaD);
                        #
                        #######################################################################################################################################
                    }
                    #
                    ###########################################################################################################################################
                    # Consulta para obtener el ultimo penultimo registro
                    $sqlD3 = "SELECT max(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idCnt";
                    $resultD3 = $mysqli->query($sqlD3);
                    $detA3 = mysqli_fetch_row($resultD3);
                    #
                    ###########################################################################################################################################
                    # Consulta para la naturaleza de impuesto al consumo
                    $sqlNaturalezaCuenta4="select naturaleza from gf_cuenta where id_unico=$cuentas[3]";
                    $resultNaturaleza4=$mysqli->query($sqlNaturalezaCuenta4);
                    $naturaleza4= mysqli_fetch_row($resultNaturaleza4);
                    #
                    ###########################################################################################################################################
                    # Validamos que el campo de impoconsumo no venga vacio, e insertamos el detalle de la cuenta de impuesto al consumo

                    if(!empty($impoconsumo) || $impoconsumo!='0' || $impoconsumo!='0.00'){
                        $insertCuentaImpo = "INSERT INTO gf_detalle_comprobante(fecha, descripcion, valor, valorejecucion, comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detalleafectado, detallecomprobantepptal) VALUES('$fecha', $descripcion, $impoconsumo, $impoconsumo, $idCnt, $cuentas[3], 2, $tercero, '2147483647', $centroCosto, $detA3[0], $idDetallePptal[0])";
                        $resultCuentaImpo = $mysqli->query($insertCuentaImpo);
                        #
                        #######################################################################################################################################
                        # Consulta para obtener el ultimo penultimo registro
                        $sqlD4 = "SELECT max(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $idCnt";
                        $resultD4 = $mysqli->query($sqlD4);
                        $detA4 = mysqli_fetch_row($resultD4);
                        #
                        #######################################################################################################################################
                        # Registro de detalle con cuenta debito con valor de impo consumo
                        $insertCuentaImpoD = "INSERT INTO gf_detalle_comprobante(fecha, descripcion, valor, valorejecucion, comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detalleafectado,detallecomprobantepptal) VALUES('$fecha', $descripcion, $impoconsumo, $impoconsumo, $idCnt, $cuentas[0], 1, $tercero, '2147483647', $centroCosto, $detA4[0],$idDetallePptal[0])";
                        $resultCuentaImpoD = $mysqli->query($insertCuentaImpoD);
                        #
                        #######################################################################################################################################
                    }
                }
                $idCnt = $_POST['txtIdCnt'];
                # Consultamos el ultimo id registrado de detalle de comprobante cnt
                $sqlDetalleCnt="select MAX(id_unico) from gf_detalle_comprobante where comprobante = $idCnt AND cuenta = $cuenta_debito";
                $resultDetalleCnt=$mysqli->query($sqlDetalleCnt);
                $idDetalleCnt= mysqli_fetch_row($resultDetalleCnt);
                ################################################################################################################################################
                # Actualizamos la tabla de detalle factura en el campo detalle_contable
                $sqlUpdate="update gp_detalle_factura set detallecomprobante=$detAF where id_unico=$detalleFactura[0]";
                $resultUpdate=$mysqli->query($sqlUpdate);
            }

        }
    }
    $html = "";
    $html .= "<script type=\"text/javascript\">";
    $html .= "\n\t$(\"#myModal1\").modal('show');";
    $html .= "\n\t$(\"#ver1\").click(function(){";
    $html .= "\n\t\t$(\"#myModal1\").modal('hide');";
    $html .= "\n\t\twindow.history.go(-1);";
    $html .= "\n\t});";
    $html .= "\n</script>";
    echo $html;
}else{
    $html = "";
    $html .= "<script type=\"text/javascript\">";
    $html .= "\n\t$(\"#myModal2\").modal('show');";
    $html .= "\n\t$(\"#ver2\").click(function(){";
    $html .= "\n\t\t$(\"#myModal2\").modal('hide');";
    $html .= "\n\t\twindow.history.go(-1);";
    $html .= "\n\t})";
    $html .= "</script>";
    echo $html;
}