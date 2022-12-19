<?php
require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
@$id = $_GET['id'];
@$idtip = $_GET['tip'];

if ($idtip == 1) {
    $sql = "SELECT DISTINCT
            pp.fechapago,
            a.id_unico as acu,
            ta.id_unico as id_tipo,
            ta.nombre as nombre_tipo,
            fa.id_unico as id_fac, 
            fa.numero, 
            t.id_unico as id_tercero, 
            t.razonsocial, 
            sum(dpp.valor) as vlr_pago,
            pp.id_unico as id_pago

            from gr_pago_predial pp 
            left join gr_detalle_pago_predial dpp on dpp.pago=pp.id_unico 
            left join ga_detalle_factura df on df.iddetallerecaudo=dpp.id_unico 
            left join ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
            left join ga_acuerdo a on a.id_unico=da.acuerdo 
            left join ga_tipo_acuerdo ta on ta.id_unico=a.tipo 
            left join ga_factura_acuerdo fa on fa.id_unico=df.factura 
            left join gf_cuenta_bancaria cb on cb.id_unico=pp.banco 
            left join gf_tercero t on t.id_unico=cb.banco
            where md5(pp.id_unico)= '$id' ";

    $sql_anul = "SELECT DISTINCT
             dpp.iddetalleanulacion,
             pp_an.fechapago

            from gr_pago_predial pp 
            left join gr_detalle_pago_predial dpp on dpp.pago=pp.id_unico 
            left join ga_detalle_factura df on df.iddetallerecaudo=dpp.id_unico 
            left join ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
            left join ga_acuerdo a on a.id_unico=da.acuerdo 
            left join ga_tipo_acuerdo ta on ta.id_unico=a.tipo 
            left join ga_factura_acuerdo fa on fa.id_unico=df.factura 
            left join gf_cuenta_bancaria cb on cb.id_unico=pp.banco 
            left join gf_tercero t on t.id_unico=cb.banco
            left join gr_detalle_pago_predial dpp_an on dpp_an.id_unico=dpp.iddetalleanulacion
            left join gr_pago_predial pp_an on pp_an.id_unico=dpp_an.pago
            where md5(pp.id_unico)= '$id' ";
} else if ($idtip == 2) {
    $sql = "SELECT DISTINCT
            r.fecha,
            a.id_unico as acu,
            ta.id_unico as id_tipo,
            ta.nombre as nombre_tipo,
            fa.id_unico as id_fac, 
            fa.numero, 
            t.id_unico as id_ter,
            t.razonsocial, 
            r.valor as vlr_pago,
            r.id_unico as id_rec

            from gc_recaudo_comercial r
            left join gc_detalle_recaudo dr on dr.recaudo=r.id_unico
            left join ga_detalle_factura df on df.iddetallerecaudo=dr.id_unico
            left join ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
            left join ga_acuerdo a on a.id_unico=da.acuerdo
            left join ga_tipo_acuerdo ta on ta.id_unico=a.tipo            
            left join ga_factura_acuerdo fa on fa.id_unico=df.factura
            left join gf_cuenta_bancaria cb on cb.id_unico=r.cuenta_ban
            left join gf_tercero t on t.id_unico=cb.banco
            where  md5(r.id_unico)= '$id' ";

    $sql_anul = "SELECT DISTINCT
            dr.iddetalleanulacion,
             r_an.fecha
            
            from gc_recaudo_comercial r
            left join gc_detalle_recaudo dr on dr.recaudo=r.id_unico
            left join ga_detalle_factura df on df.iddetallerecaudo=dr.id_unico
            left join ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
            left join ga_acuerdo a on a.id_unico=da.acuerdo
            left join ga_tipo_acuerdo ta on ta.id_unico=a.tipo            
            left join ga_factura_acuerdo fa on fa.id_unico=df.factura
            left join gf_cuenta_bancaria cb on cb.id_unico=r.cuenta_ban
            left join gf_tercero t on t.id_unico=cb.banco
            left join gc_detalle_recaudo dr_an on dr_an.id_unico=dr.iddetalleanulacion
            left join gc_recaudo_comercial r_an on r_an.id_unico=dr_an.recaudo
            where  md5(r.id_unico)= '$id' ";
}

$resultado = $mysqli->query($sql);
$res = mysqli_fetch_row($resultado);

$resultad = $mysqli->query($sql_anul);
$resl = mysqli_fetch_row($resultad);
$fecha_anulacion = $resl[1];

#@$nacuerdo = 8;
#@$tipo=$_GET['sltTiposelect'];
@$ntipo = $_GET['sltTiposelect'];
@$cont_sel = $_GET['sltContS'];
@$ncont_sel = $_GET['sltContS'];
@$fecha_ac = $_GET['fecha_acuerdo'];
@$disp = $_GET['dis'];
@$array = array();

if (empty($disp)) {
    $a = "none";
} else {
    $a = "inline-block";
}
if (empty($res[0])) {
    $a2 = "none";
} else {
    $a2 = "inline-block";
}
if (empty($resl[0])) {
    $fe = "none";
} else {
    $fe = "inline-block";
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style >
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
                                    font-family: Arial;}
    </style>
    <script type="text/javascript" src="../jquery.js"></script>
    <script src="js/jquery-ui.js"></script>

    <script>

        $(function () {
            var fecha = new Date();
            var dia = fecha.getDate();
            var mes = fecha.getMonth() + 1;
            if (dia < 10) {
                dia = "0" + dia;
            }
            if (mes < 10) {
                mes = "0" + mes;
            }
            var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
            $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: 'Anterior',
                nextText: 'Siguiente',
                currentText: 'Hoy',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
                dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: '',
                changeYear: true
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);


            $("#sltFecha").datepicker({changeMonth: true, }).val();


        });
    </script>

    <script>
        function estado(value) {

            if (value == "1") {

                document.getElementById("sltTipo").disabled = false;
                document.getElementById("sltContribuyente").disabled = true;


            } else {
                document.getElementById("sltContribuyente").disabled = false;
                document.getElementById("sltTipo").disabled = true;
            }
        }
    </script>
    <title>Detalle Recaudo Acuerdo de Pago</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
    <div class="container-fluid text-center">
    <div class="row content">
<?php require_once 'menu.php'; ?>
        <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: 0px">
            <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Detalle Recaudo Acuerdo Pago</h2>
            <a href="<?php echo 'listar_GA_RECAUDO_ACUERDO.php'; ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:8px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(("Datos del Acuerdo")); ?></h5>
            <div class="client-form contenedorForma" style="margin-top: -7px;">
                <form id="formid" name="formid" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="ver_GA_RECAUDO_ACUERDO.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                    <!-------------------------------------------------------------------------------------- -->
                    <div class="form-group form-inline" style="margin-top:-25px">
                        <!--Fecha-->
                        <input name="id_rec" id="id_rec" type="hidden" value="<?php echo $id; ?>">
                        <!----------Script para invocar Date Picker-->
                        <script type="text/javascript">
                            $(document).ready(function () {
                                $("#datepicker").datepicker();
                            });
                        </script>
                        <?php
                        $fecha = explode("-", $res[0]);
                        $anio = $fecha[0];
                        $mes = $fecha[1];
                        $dia = $fecha[2];
                        $Fec = $dia . '/' . $mes . '/' . $anio;
                        ?>
                        <label for="sltFechaA" class="col-sm-2 control-label">
                            <strong class="obligado">*</strong>Fecha:
                        </label>
                        <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left"><?= $Fec; ?></label>
                        <?php
                        if (empty($ntipo)) {
                            $tip = "SELECT id_unico, nombre FROM ga_tipo_acuerdo";
                            $t[0] = "";
                            $t[1] = "Tipo Acuerdo";
                        } else {
                            $tip = "SELECT id_unico, nombre FROM ga_tipo_acuerdo where id_unico!= '$ntipo'";
                            $tx = "SELECT id_unico, nombre FROM ga_tipo_acuerdo where id_unico= '$ntipo'";
                            $tipoa = $mysqli->query($tx);
                            $t = mysqli_fetch_row($tipoa);
                        }

                        $tipon = $mysqli->query($tip);
                        ?> 

                        <label for="sltTipo" class="col-sm-2 control-label">
                            <strong class="obligado">*</strong>Tipo Acuerdo:
                        </label>
                        <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left"><?= $res[3]; ?></label>
                        <?php
                        if (empty($ntipo)) {

                            $t[0] = "";
                            $t[1] = "Factura";
                        } else {

                            $tip = "SELECT DISTINCT f.id_unico,f.numero FROM ga_factura_acuerdo f
                                                                        left join ga_detalle_factura df on f.id_unico = df.factura
                                                                        left join ga_detalle_acuerdo da on df.detalleacuerdo = da.id_unico
                                                                        left join ga_acuerdo a on da.acuerdo = a.id_unico
                                                                        where f.id_unico='$res[2]'";

                            $tipoa = $mysqli->query($tip);
                            $t = mysqli_fetch_row($tipoa);
                            if (empty($res[2])) {
                                $t[0] = "";
                                $t[1] = "Factura";
                            }
                        }
                        $tipon = $mysqli->query($tip);
                        ?> 
                        <label for="sltFactura" class="col-sm-2 control-label">
                            <strong class="obligado">*</strong>Factura Acuerdo:
                        </label>
                        <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left"><?= $res[5]; ?></label>
                    </div>
                    <div class="form-group form-inline" >
                        <label for="sltBanco" class="col-sm-2 control-label"><strong class="obligado">*</strong>Banco:</label>
                        <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left"><?= $res[7]; ?></label>
                        <label for="txtValorP" class="col-sm-2 control-label"><strong class="obligado">*</strong>Valor Pago:</label>
                        <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left"><?= number_format($res[8], 2, '.', ','); ?></label>
                        <a class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px; " href="javaScript:eliminar(<?=$res[9].','.$idtip ?>)"><i title="Eliminar Recaudo" class="glyphicon glyphicon-trash"></i></a>
                        
                        <a id="enviar"   href="informes/INF_RECAUDO_ACUERDO.php?id=<?= $id; ?>" target="_blank" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;"><li class="glyphicon glyphicon-print"></li></a>   
                    </div> 
                    <strong><label style="color-text: red; color: red; display: <?php echo $fe ?>; margin-left: 20px;">Recaudo Anulado: <?php echo $fecha_anulacion ?></label></strong>  
                    <div class=" col-sm-12 col-md-12 col-lg-12 form-group form-inline" style="margin-top:5px; ">
                    <?php
                    $tab_fd = '';
                    if (empty($fecha_ac)) {

                    } else {
                        $fec_Ac = explode("/", $fecha_ac);                                                //Divimos la fecha usando /
                        $fec_Ac = "$fec_Ac[2]-$fec_Ac[1]-$fec_Ac[0]";
                    }
                    ?>
                        <div class="table-responsive" style="margin-left: 10px; margin-right: 20px;margin-top:10px;">
                            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                                <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td style="display: none;">Identificador</td>
                                            <td width="7%" class="cabeza"></td>                                        
                                            <?php
                                            if ($res[2] == 1) {//$nacuerdo
                                            $sql = "SELECT DISTINCT 
                                                c.nombre, da.concepto_deuda,cp.anno 
                                                FROM gr_detalle_pago_predial dpg
                                                LEFT JOIN ga_detalle_factura df on df.iddetallerecaudo=dpg.id_unico
                                                LEFT join   ga_detalle_acuerdo da on da.id_unico = df.detalleacuerdo 
                                                LEFT JOIN gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda 
                                                LEFT JOIN gr_concepto c ON c.id_unico = cp.id_concepto 
                                                WHERE da.acuerdo = '$res[1]' ORDER BY da.concepto_deuda ";
                                            } else if ($res[2] == 2) {
                                                $sql = "SELECT DISTINCT 
                                                    cc.nom_inf,da.concepto_deuda,cc.id_unico    
                                                    FROM  gc_detalle_recaudo dr
                                                    LEFT JOIN ga_detalle_factura df on df.iddetallerecaudo=dr.id_unico
                                                    LEFT join   ga_detalle_acuerdo da on da.id_unico = df.detalleacuerdo
                                                    LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda 
                                                    WHERE da.acuerdo = '$res[1]' ORDER BY da.concepto_deuda";
                                            } else {
                                                $sql = "";
                                            }
                                            $resultado = $mysqli->query($sql);
                                            while ($row = mysqli_fetch_row($resultado)) {
                                                echo "<td class='cabeza'><strong>$row[0] - $row[2]</strong></td>";
                                            }?>
                                            <td class="cabeza"><strong>Total Cuota</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="cabeza" style="display: none;">Identificador</th>
                                            <th class="cabeza" width="7%"></th>

                                            <?php
                                            if ($res[2] == 1) {//$nacuerdo
                                                $sql = "SELECT DISTINCT 
                                                    c.nombre, cp.id_unico, da.concepto_deuda,cp.anno  
                                                    FROM gr_detalle_pago_predial dpg
                                                    LEFT JOIN ga_detalle_factura df on df.iddetallerecaudo=dpg.id_unico
                                                    LEFT join   ga_detalle_acuerdo da on da.id_unico = df.detalleacuerdo
                                                    LEFT JOIN gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda 
                                                    LEFT JOIN gr_concepto c ON c.id_unico = cp.id_concepto 
                                                    WHERE da.acuerdo = '$res[1]' ORDER BY da.concepto_deuda ";
                                            } else if ($res[2] == 2) {
                                                $sql = "SELECT DISTINCT 
                                                    cc.nom_inf,cc.id_unico, da.concepto_deuda, da.concepto_deuda    
                                                    FROM  gc_detalle_recaudo dr
                                                    LEFT JOIN ga_detalle_factura df on df.iddetallerecaudo=dr.id_unico
                                                    LEFT join   ga_detalle_acuerdo da on da.id_unico = df.detalleacuerdo
                                                    LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda 
                                                    WHERE da.acuerdo = '$res[1]' ORDER BY da.concepto_deuda";
                                            } else {
                                                $sql = "";
                                            }
                                            $resultado = $mysqli->query($sql);
                                            while ($row = mysqli_fetch_row($resultado)) {
                                                echo "<th class='cabeza'>$row[0] - $row[3]</th>";
                                            } ?>
                                            <th class="cabeza">Total Cuota</th>
                                        </tr>
                                    </thead>    
                                    <tbody>
                                        <tr>
                                            <td style="display: none;"><?php echo $row[0] ?></td>
                                            <td></td>     
                                            <?php
                                            if ($res[2] == 1) {//$nacuerdo
                                                $sql = "SELECT DISTINCT
                                                    c.nombre,
                                                    (dpg.valor),
                                                    cp.anno

                                                  FROM gr_detalle_pago_predial dpg
                                                  LEFT JOIN ga_detalle_factura df on df.iddetallerecaudo=dpg.id_unico
                                                LEFT join   ga_detalle_acuerdo da on da.id_unico = df.detalleacuerdo
                                                  LEFT JOIN   gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda
                                                  LEFT JOIN   gr_concepto c ON c.id_unico = cp.id_concepto
                                                  WHERE  dpg.pago = '$res[9]' order by da.nrocuota,da.concepto_deuda asc";
                                            } else if ($res[2] == 2) {
                                                $sql = "SELECT DISTINCT 
                                                cc.nom_inf, (dr.valor), da.concepto_deuda    
                                                FROM  gc_detalle_recaudo dr
                                                LEFT JOIN ga_detalle_factura df on df.iddetallerecaudo=dr.id_unico
                                            LEFT join   ga_detalle_acuerdo da on da.id_unico = df.detalleacuerdo
                                                LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda 
                                                WHERE dr.recaudo = '$res[9]' and da.concepto_deuda is not null order by da.nrocuota,da.concepto_deuda asc";
                                            } else {
                                                $sql = "";
                                            }
                                            $result = $mysqli->query($sql);
                                            $tota = 0;
                                            while ($rowV = mysqli_fetch_row($result)) {
                                                $vcuota = $rowV[1];
                                                $tota = $tota + $vcuota; ?>
                                                <td class="campos text-right"><?php echo number_format($vcuota, 2, '.', ','); ?></td>  
                                            <?php } ?>
                                            <td class="campos text-right"><?php echo number_format($tota, 2, '.', ','); ?></td>  
                                            <script>
                                                var total = 0;
                                                function sumar(valor) {
                                                    total += valor;
                                                    document.getElementById("txtValor").innerHTML = total;
                                                }

                                                function restar(valor) {
                                                    total -= valor;
                                                    document.getElementById("txtValor").innerHTML = total;
                                                }
                                            </script> 
                                        </tr> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>                                    
</div>
<div>
<?php require_once './footer.php'; ?>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalMsj" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="msj"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript">
        $("#sltTipo").select2();
        $("#sltFactura").select2();
        $("#sltBanco").select2();
    </script>
    <script type="text/javascript">
        function eliminar(nrec, tp){
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarAcuerdo.php?id="+nrec+'&action=2&tipo='+tp,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){ 
                            $("#msj").html('Información Eliminada Correctamente');
                            $("#myModalMsj").modal('show');
                        } else{ 
                            $("#myModalMsj").modal('show');     
                            $("#msj").html('No Se Ha Podido Eliminar La Información');
                        }
                        $("#ver1").click(function(){
                            document.location='listar_GA_RECAUDO_ACUERDO.php'
                        })
                    }
                });
            });
        }
    </script>
</body>
</html>