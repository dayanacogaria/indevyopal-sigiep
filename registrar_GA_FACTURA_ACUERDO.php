<?php
require_once ('Conexion/conexion.php');
require_once 'Conexion/ConexionPDO.php';
require_once './jsonPptal/funcionesPptal.php';
require_once ('head_listar.php');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$nanno      = anno($anno);
@$nacuerdo  = $_REQUEST['nacuerdo'];
@$tipo      = $_REQUEST['sltTiposelect'];
$rowa = $con->Listar("SELECT id_unico, consecutivo FROM ga_acuerdo WHERE id_unico = $nacuerdo");

#Consecutivo 
$nac = $con->Listar("SELECT (MAX(numero)+1)FROM ga_factura_acuerdo WHERE YEAR(fecha_ven)='".$nanno."'");
if (!empty($nac[0][0])) {
    $numf = $nac[0][0];
} else {
    $numf = $nanno.'000001';
}

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script>
    $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
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

        $("#sltFechaA").datepicker({changeMonth: true, }).val(fecAct);
    });
</script>
<title>Registrar Factura Acuerdo de Pago</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: 0px">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Factura Acuerdo Pago</h2>
                <div class="client-form contenedorForma" style="margin-top: -7px;">
                    <form id="formid" name="formid" class="form-horizontal"  enctype="multipart/form-data" >
                        <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                        <div class="form-group form-inline" style="margin-top:-25px">
                            <input type="hidden" name="tipo_select" id="tipo_select" value="<?=$tipo?>">
                            <input type="hidden" name="txtNumeroA" id="txtNumeroA" value="<?=$nacuerdo?>">
                            <label for="txtNumero" class="col-sm-2 control-label"><strong class="obligado">*</strong>Nº Acuerdo:</label>
                            <input  name="txtNumero" id="txtNumero" title="Ingrese Número Acuerdo" type="text" style="width: 100px;height: 30px" class="form-control col-sm-1" placeholder="Número Acuerdo" value="<?= $rowa[0][1] ?>" readonly>
                            <label for="sltFechaA" class="col-sm-2 control-label"><strong class="obligado">*</strong>Fecha Vencimiento:</label>
                            <input name="sltFechaA" id="sltFechaA" title="Ingrese Fecha Acuerdo" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"  placeholder="Ingrese la fecha" >  
                            <label for="txtNumeroF" class="col-sm-2 control-label"><strong class="obligado">*</strong>Número:</label>
                            <input  name="txtNumeroF" id="txtNumeroF" title="Ingrese Número" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" placeholder="Número" value="<?= $numf ?>">
                        </div>
                        <br/>
                        <div class="form-group form-inline" style="margin-top:-25px">
                            <label for="txtObs" class="col-sm-2 control-label">Observaciones:</label>
                            <input  name="txtObs" id="txtObs" title="Ingrese Observaciones" type="text" style=" width:70%; " class="form-control col-sm-1" placeholder="Observaciones">
                        </div>
                        <div class="form-group form-inline" style="margin-top:5px; ">
                            <div class="table-responsive" style="margin-left: 25px; margin-right: 25px;">
                                <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" >
                                    <thead>
                                        <tr>
                                            <td style="display: none;">Identificador</td>
                                            <td width="7%" class="cabeza"></td>   
                                            <td class="cabeza"><strong></strong></td>
                                            <td class="cabeza"><strong>Cuota</strong></td>
                                            <td class="cabeza"><strong>Fecha Pago</strong></td>
                                            <td class="cabeza"><strong>Total Cuota</strong></td>                                                
                                            <?php
                                            if ($tipo == 1) {//$nacuerdo
                                                $sql = "SELECT DISTINCT "
                                                        . "c.nombre, da.concepto_deuda,cp.anno, cp.id_unico "
                                                        . "FROM ga_detalle_acuerdo da "
                                                        . "LEFT JOIN gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda "
                                                        . "LEFT JOIN gr_concepto c ON c.id_unico = cp.id_concepto "
                                                        . "WHERE da.acuerdo = '$nacuerdo' order by da.nrocuota,da.concepto_deuda asc ";
                                            } else if ($tipo == 2) {
                                                $sql = "SELECT DISTINCT "
                                                        . "cc.nom_inf,da.concepto_deuda,' ' AS v, cc.id_unico  "
                                                        . "FROM ga_detalle_acuerdo da "
                                                        . "LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda "
                                                        . "WHERE da.acuerdo = '$nacuerdo' order by da.nrocuota,da.concepto_deuda asc";
                                            } else {
                                                $sql = "";
                                            }
                                            $resultado = $mysqli->query($sql);
                                            while ($row = mysqli_fetch_row($resultado)) {
                                                echo "<td class='cabeza'><strong>$row[0] - $row[2]</strong></td>";
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <th class="cabeza" style="display: none;">Identificador</th>
                                            <th class="cabeza" width="7%"></th>
                                            <th class="cabeza"></th>
                                            <th class="cabeza">Total Cuota</th>                                                
                                            <th class="cabeza">Cuota</th>
                                            <th class="cabeza">Fecha Pago</th>
                                            <?php
                                            $resultado = $mysqli->query($sql);
                                            while ($row = mysqli_fetch_row($resultado)) {
                                                echo "<th class='cabeza'>$row[0] - $row[2]</th>";
                                            }?>
                                        </tr>
                                    </thead>    
                                    <tbody>
                                    <?php
                                    $rowd = $con->Listar("SELECT  da.nrocuota, da.id_unico, 
                                        DATE_FORMAT(da.fecha, '%d/%m/%Y'), 
                                        IF(SUM(da.valor)IS NULL, 0, SUM(da.valor)) as V_Cuota, 
                                        IF(SUM(df.valor) IS NULL, 0, SUM(df.valor)) AS V_Factura, 
                                        IF(a.tipo=1, IF(SUM(dpp.valor) IS NULL, 0, SUM(dpp.valor)), IF(SUM(drc.valor) IS NULL, 0, SUM(dpp.valor))) AS V_Recaudo, 
                                        (SUM(da.valor) - IF(a.tipo=1, IF(SUM(dpp.valor) IS NULL, 0, SUM(dpp.valor)), IF(SUM(drc.valor) IS NULL, 0, SUM(dpp.valor)))) as Saldo  
                                        FROM ga_acuerdo a 
                                        LEFT JOIN ga_detalle_acuerdo da ON a.id_unico = da.acuerdo 
                                        LEFT JOIN ga_detalle_factura df ON df.detalleacuerdo = da.id_unico 
                                        LEFT JOIN gr_detalle_pago_predial dpp ON df.iddetallerecaudo = dpp.id_unico 
                                        LEFT JOIN gc_detalle_recaudo drc ON df.iddetallerecaudo = drc.id_unico
                                        WHERE a.id_unico = $nacuerdo 
                                        GROUP BY da.nrocuota 
                                        HAVING Saldo>0");
                                    for ($i = 0;$i < count($rowd);$i++) { ?>
                                            <tr>
                                                <td style="display: none;"><?= $rowd[$i][0] ?></td>
                                                <td></td>     
                                                <td class="campos text-center">
                                                    <input name="cod[]" type="checkbox" value="<?= $rowd[$i][0] ?>" onClick="if (this.checked)sumar(<?= round($rowd[$i][6],2); ?>);else restar(<?= round($rowd[$i][6],2); ?>)"></td> 
                                                <td class="campos text-center"><?= $rowd[$i][0] ?></td>                   
                                                <td class="campos text-center"><?= $rowd[$i][2] ?></td>  
                                                <td class="campos text-right"><?= number_format($rowd[$i][6], 2, '.', ','); ?></td> 

                                                <?php
                                                $resultado = $mysqli->query($sql);
                                                while ($rowc = mysqli_fetch_row($resultado)) {
                                                    $c = $rowc[3]; 
                                                    $rowvc = $con->Listar("SELECT  da.nrocuota, da.id_unico, 
                                                    DATE_FORMAT(da.fecha, '%d/%m/%Y'), 
                                                    IF(SUM(da.valor)IS NULL, 0, SUM(da.valor)) as V_Cuota, 
                                                    IF(SUM(df.valor) IS NULL, 0, SUM(df.valor)) AS V_Factura, 
                                                    IF(a.tipo=1, IF(SUM(dpp.valor) IS NULL, 0, SUM(dpp.valor)), IF(SUM(drc.valor) IS NULL, 0, SUM(dpp.valor))) AS V_Recaudo, 
                                                    (SUM(da.valor) - IF(a.tipo=1, IF(SUM(dpp.valor) IS NULL, 0, SUM(dpp.valor)), IF(SUM(drc.valor) IS NULL, 0, SUM(dpp.valor)))) as Saldo  
                                                    FROM ga_acuerdo a 
                                                    LEFT JOIN ga_detalle_acuerdo da ON a.id_unico = da.acuerdo 
                                                    LEFT JOIN ga_detalle_factura df ON df.detalleacuerdo = da.id_unico 
                                                    LEFT JOIN gr_detalle_pago_predial dpp ON df.iddetallerecaudo = dpp.id_unico 
                                                    LEFT JOIN gc_detalle_recaudo drc ON df.iddetallerecaudo = drc.id_unico
                                                    WHERE a.id_unico = $nacuerdo  AND da.concepto_deuda = $c AND da.nrocuota = ".$rowd[$i][0]);
                                                    
                                                    echo '<td class="campos text-right">'.number_format($rowvc[0][6], 2, '.', ',').'</td> ';
                                                }?>
                                            </tr> 
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-sm-offset-6  col-sm-6 text-left" style= " margin-top:5px;margin-bottom:-10px;" >
                            <div class="col-sm-3">
                                <div class="form-group"  align="left">                                    
                                    <label class="control-label">
                                        <strong class="obligado">*</strong><strong>Total A Pagar:</strong>
                                    </label>                                
                                </div>
                            </div>                        
                            <div class="col-sm-2 text-right" align="left">
                                <input style="width: 220px;height: 30px; font-weight: bold;" class="form-control col-sm-1 text-right" placeholder="Total Factura"  type="text" id="txtValor" name ="txtValor" readonly required>
                            </div>  
                        </div>
                        <div class="form-group form-inline" style="margin-top:-5px">                            
                            <button id="enviar" type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: -145px ; ">
                                <li class="glyphicon glyphicon-floppy-disk"></li></button>                             
                        </div>
                    </form>
                </div>
            </div>
        </div>                                    
    </div>
    <?php require_once './footer.php'; ?>        
    <div class="modal fade" id="myModalcomp" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Asegurese que los campos obligatorios esten diligenciados.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver8"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>    
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript">
        $('#enviar').click(function () {
            var selected = '';
            var nac     = $('#txtNumeroA').val();
            var fch     = $('#sltFechaA').val();
            var nf      = $('#txtNumeroF').val();
            var obvs    = $('#txtObs').val();
            var vlr     = $('#txtValor').val();
            var tp      = $('#tipo_select').val();
            $('#formid input[type=checkbox]').each(function () {
                if (this.checked) {
                    selected += $(this).val() + ',';
                }
            });

            if (nac === '' || fch === '' || nf === '' || vlr === '') {
                $("#myModalcomp").modal('show');
            } else {
                window.location = 'json/registrarFacturaAcuerdoJSON.php?codigos=' + selected + '&sltFechaA=' + fch +
                        '&nacuerdo=' + nac + '&numero=' + nf + '&Observaciones=' + obvs + '&tipo=' + tp;

            }

            return false;
        });
    </script>
    <script>
        $("#sltFechaA").change(function () {
            var tp      = $('#tipo_select').val();
            var nacu    = $('#txtNumeroA').val();
            var fch  = $('#sltFechaA').val();
            window.location = 'registrar_GA_FACTURA_ACUERDO.php?nacuerdo=' + nacu + '&sltTiposelect=' + tp + '&fecha_fac=' + fch;
        });
    </script>
    <script>
        var total = 0;
        function sumar(valor) {
            total += valor;
            document.getElementById("txtValor").value = total;
        }

        function restar(valor) {
            total -= valor;
            document.getElementById("txtValor").value = total;
        }
    </script> 
</body>
</html>