<?php
require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
require_once ('./jsonPptal/funcionesPptal.php');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$tipo       = $_REQUEST['tipo'];
$tercero    = $_REQUEST['tr'];
$fecha      = fechaC($_REQUEST['fecha']);
$nanno      = anno($anno);
$whr        = '';
if($tipo==1){
    if(!empty($tercero)){
        $whr = ' ORDER BY pd.id_unico = '.$tercero.' DESC';
    }
    
    $rowt = $con->Listar("SELECT DISTINCT pd.id_unico, pd.codigo_catastral,
            p.numero,p.nombres 
        FROM gr_factura_predial fp
        LEFT JOIN gp_predio1 pd on pd.id_unico=fp.predio
        LEFT JOIN gp_tercero_predio t on t.predio=pd.id_unico
        LEFT JOIN gr_propietarios p on p.id_unico=t.tercero 
        WHERE pd.estado= 2 ".$whr);
} else { 
    if(!empty($tercero)){
        $whr = ' ORDER BY c.id_unico = '.$tercero.' DESC';
    }
    $rowt = $con->Listar("SELECT DISTINCT c.id_unico, c.codigo_mat,tr.numeroidentificacion,
        IF(tr.razonsocial IS NULL OR tr.razonsocial = '',
             CONCAT_WS(' ',
             tr.nombreuno,
             tr.nombredos,
             tr.apellidouno,
             tr.apellidodos),(tr.razonsocial)) 
        FROM gc_declaracion d 
        LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
        LEFT JOIN gf_tercero tr on tr.id_unico=c.tercero 
        WHERE c.estado =1 ".$whr);
}
#Consecutivo 
$nac = $con->Listar("SELECT (MAX(consecutivo)+1)FROM ga_acuerdo WHERE parametrizacion= $anno");
if (!empty($nac[0][0])) {
    $numa = $nac[0][0];
} else {
    $numa = $nanno.'000001';
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
<link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
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
        $("#sltFechaA").datepicker({changeMonth: true, }).val(fecAct);
    });
</script>
<title>Registrar Acuerdo de Pago</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Acuerdo Pago</h2>
                <a href="listar_GA_ACUERDO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:0px;  background-color: #0e315a; color: white; border-radius: 5px">Registrar Acuerdo</h5>
                <div class="client-form contenedorForma" style="margin-top: -7px;">
                    <form id="formid" name="formid" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarAcuerdoJSON.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                        <div class="form-group form-inline" style="margin-top:-25px">
                            <label for="sltFechaA" class="col-sm-2 control-label"><strong class="obligado">*</strong>Fecha:</label>
                            <input name="sltFechaA" id="sltFechaA" title="Ingrese Fecha Acuerdo" type="text" style="width: 90px" class="form-control col-sm-1"  placeholder="Ingrese la fecha"  required>  
                            
                            <label for="sltTipo" class="col-sm-2 control-label"><strong class="obligado">*</strong>Tipo Acuerdo:</label>
                            <select name="sltTipo" id="sltTipo" title="Seleccione Tipo Acuerdo" style="width: 110px" class="form-control col-sm-1" required>
                                <?php if (empty($tipo)) {
                                $rowtp = $con->Listar("SELECT id_unico, nombre FROM ga_tipo_acuerdo");
                                echo '<option value="">Tipo Acuerdo</option>';
                            } else {
                                $rowtp = $con->Listar("SELECT id_unico, nombre FROM ga_tipo_acuerdo where id_unico != '$tipo'");
                                $rowts = $con->Listar("SELECT id_unico, nombre FROM ga_tipo_acuerdo where id_unico = '$tipo'");
                                echo '<option value="'.$rowts[0][0].'">'.$rowts[0][1].'</option>';
                            } 
                            for ($i = 0; $i < count($rowtp); $i++) {
                                echo '<option value="'.$rowtp[$i][0].'">'.$rowtp[$i][1].'</option>';
                            }?>                                                     
                            </select>
                            <label for="sltTercero" class="col-sm-1 control-label"><strong class="obligado">*</strong>Tercero:</label>
                            <select name="sltTercero" id="sltTercero" title="Seleccione Tercero" style="width: 220px" class="form-control col-sm-1" required>
                                <?php if(!empty($tipo)){
                                    if(empty($tercero)){
                                        echo '<option value="">Tercero</option>';
                                    }
                                    for ($i = 0; $i < count($rowt); $i++) {
                                        echo '<option value="'.$rowt[$i][0].'">'.$rowt[$i][2].' - '.$rowt[$i][3].'('.$rowt[$i][1].')'.'</option>';
                                    }
                                }else {
                                    echo '<option value="">Tercero</option>';
                                }?>
                            </select>
                        </div>
                        <div class="form-group form-inline" >
                            <label for="txtNumeroA" class="col-sm-2 control-label"><strong class="obligado">*</strong>Nº Acuerdo:</label>
                            <input  name="txtNumeroA" id="txtNumeroA" title="Ingrese Número Acuerdo" type="text" style="width: 90px;height: 30px" class="form-control col-sm-1" placeholder="Número Acuerdo" value="<?= $numa; ?>" readonly required>

                            <label for="txtNumeroC" class="col-sm-2 control-label"><strong class="obligado">*</strong>Número Cuotas:</label>
                            <input  name="txtNumeroC" id="txtNumeroC" title="Ingrese Número Cuotas" type="text" style="width: 110px;height: 30px" class="form-control col-sm-1" placeholder="Número Cuotas" required onkeypress="return txtValida(event, 'num')" >

                            <label for="txtPorcentaje" class="col-sm-1 control-label"><strong class="obligado">*</strong>% Interes:</label>
                            <input  name="txtPorcentaje" id="txtPorcentaje" title="Ingrese % de Interés" type="text" style="width: 110px;height: 30px" class="form-control col-sm-1" placeholder="% Interés" required onkeypress="return txtValida(event, 'dec', 'valor', '2');">
                        </div>
                        <!-- ----------------------------------------------------------------------  -->

                        <div class="form-group form-inline" style="margin-top:5px; display:<?php echo $a ?>">
                            <?php if ($tipo == 1) {
                                $rowd = $con->Listar("SELECT DISTINCT fp.id_unico, p.codigo_catastral, 
                                    fp.numero, DATE_FORMAT(fp.fechafactura, '%d/%m/%Y'), 
                                    fp.fechavencimiento, p.id_unico, 
                                    (SELECT SUM(dfp.valor) FROM gr_detalle_factura_predial dfp 
                                     WHERE dfp.factura = fp.id_unico) as VF, 
                                     (SELECT IF(SUM(dpp.valor) IS NULL,0, SUM(dpp.valor)) FROM gr_detalle_pago_predial dpp 
                                      LEFT JOIN gr_detalle_factura_predial dfp ON dpp.detallefactura = dfp.id_unico 
                                     WHERE dfp.factura = fp.id_unico) as VR, 
                                     ((SELECT SUM(dfp.valor) FROM gr_detalle_factura_predial dfp 
                                     WHERE dfp.factura = fp.id_unico) - (SELECT IF(SUM(dpp.valor) IS NULL,0, SUM(dpp.valor)) FROM gr_detalle_pago_predial dpp 
                                      LEFT JOIN gr_detalle_factura_predial dfp ON dpp.detallefactura = dfp.id_unico 
                                     WHERE dfp.factura = fp.id_unico)) as VD 
                                FROM  gr_factura_predial fp
                                LEFT JOIN  gp_predio1 p on p.id_unico=fp.predio
                                WHERE  p.id_unico = $tercero AND
                                MONTH(fp.fechavencimiento)= MONTH('$fecha')
                                AND YEAR(fp.fechavencimiento) = YEAR('$fecha') 
                                HAVING ((SELECT SUM(dfp.valor) FROM gr_detalle_factura_predial dfp 
                                     WHERE dfp.factura = fp.id_unico) - (SELECT IF(SUM(dpp.valor) IS NULL,0, SUM(dpp.valor)) FROM gr_detalle_pago_predial dpp 
                                      LEFT JOIN gr_detalle_factura_predial dfp ON dpp.detallefactura = dfp.id_unico 
                                     WHERE dfp.factura = fp.id_unico)) != 0 
                                ORDER BY fp.id_unico DESC  
                                LIMIT 1");                                
                            } else if ($tipo == 2) {
                                $rowd = $con->Listar("SELECT DISTINCT d.id_unico, c.codigo_mat,d.cod_dec,
                                    DATE_FORMAT(d.fecha, '%d/%m/%Y'), d.fecha, c.id_unico, 
                                     (SELECT SUM(IF(cc.tipo_ope=3, dd.valor*-1, dd.valor)) FROM gc_detalle_declaracion dd
                                     LEFT JOIN gc_concepto_comercial cc on cc.id_unico = dd.concepto
                                     WHERE dd.declaracion = d.id_unico AND 
                                     dd.tipo_det=1 AND cc.tipo_ope IN(2,3)) AS VDC , 
                                     (SELECT IF(SUM(IF(cc.tipo_ope=3, dpd.valor*-1, dpd.valor)) IS NULL, 0,SUM(IF(cc.tipo_ope=3, dpd.valor*-1, dpd.valor)))  FROM gc_detalle_recaudo dpd 
                                     LEFT JOIN gc_detalle_declaracion dd ON dpd.det_dec = dd.id_unico 
                                     LEFT JOIN gc_concepto_comercial cc on cc.id_unico = dd.concepto
                                     WHERE dd.declaracion = d.id_unico AND 
                                     dd.tipo_det=1 AND cc.tipo_ope IN(2,3)) AS VR, 
                                     ((SELECT SUM(IF(cc.tipo_ope=3, dd.valor*-1, dd.valor)) FROM gc_detalle_declaracion dd
                                     LEFT JOIN gc_concepto_comercial cc on cc.id_unico = dd.concepto
                                     WHERE dd.declaracion = d.id_unico AND 
                                     dd.tipo_det=1 AND cc.tipo_ope IN(2,3))- 
                                     (SELECT IF(SUM(IF(cc.tipo_ope=3, dpd.valor*-1, dpd.valor)) IS NULL, 0,SUM(IF(cc.tipo_ope=3, dpd.valor*-1, dpd.valor)))  FROM gc_detalle_recaudo dpd 
                                     LEFT JOIN gc_detalle_declaracion dd ON dpd.det_dec = dd.id_unico 
                                     LEFT JOIN gc_concepto_comercial cc on cc.id_unico = dd.concepto
                                     WHERE dd.declaracion = d.id_unico AND 
                                     dd.tipo_det=1 AND cc.tipo_ope IN(2,3))) as VD 
                                FROM gc_declaracion d
                                LEFT JOIN  gc_contribuyente c ON c.id_unico = d.contribuyente
                                WHERE c.id_unico = $tercero 
                                    AND d.fecha <'$fecha' AND YEAR(d.fecha) = YEAR('$fecha')
                                HAVING ((SELECT SUM(IF(cc.tipo_ope=3, dd.valor*-1, dd.valor)) FROM gc_detalle_declaracion dd
                                    LEFT JOIN gc_concepto_comercial cc on cc.id_unico = dd.concepto
                                    WHERE dd.declaracion = d.id_unico AND 
                                    dd.tipo_det=1 AND cc.tipo_ope IN(2,3))- 
                                    (SELECT IF(SUM(IF(cc.tipo_ope=3, dpd.valor*-1, dpd.valor)) IS NULL, 0,SUM(IF(cc.tipo_ope=3, dpd.valor*-1, dpd.valor)))  FROM gc_detalle_recaudo dpd 
                                    LEFT JOIN gc_detalle_declaracion dd ON dpd.det_dec = dd.id_unico 
                                    LEFT JOIN gc_concepto_comercial cc on cc.id_unico = dd.concepto
                                    WHERE dd.declaracion = d.id_unico AND 
                                    dd.tipo_det=1 AND cc.tipo_ope IN(2,3)))!=0"); 
                            } ?>
                            <div class="table-responsive" style="margin-left: 25px; margin-right: 25px;">
                                <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td style="display: none;">Identificador</td>
                                            <td width="7%" class="cabeza"></td>     
                                            <td class="cabeza"><strong>Seleccionar</strong></td>
                                            <td class="cabeza"><strong>Valor Deuda</strong></td>
                                            <td class="cabeza"><strong>Nº Catastral ó Cód. Matricula</strong></td>
                                            <td class="cabeza"><strong>Nº Factura ó Nº Declaración</strong></td>
                                            <td class="cabeza"><strong>Fecha Factura ó Declaración</strong></td>
                                            <td class="cabeza"><strong>Valor Factura ó Declaración</strong></td>
                                            <td class="cabeza"><strong>Valor Recaudo</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="cabeza" style="display: none;">Identificador</th>
                                            <th class="cabeza" width="7%"></th>
                                            <th class="cabeza">Seleccionar</th>
                                            <th class="cabeza">Valor Deuda</th>
                                            <th class="cabeza">Nº Catastral ó Cód. Matricula</th>
                                            <th class="cabeza">Nº Factura ó Declaración</th>
                                            <th class="cabeza">Fecha Factura ó Declaración</th>
                                            <th class="cabeza">Valor Factura ó Declaración</th>
                                            <th class="cabeza">Valor Recaudo</th>
                                        </tr>
                                    </thead>    
                                    <tbody>
                                    <?php for ($i = 0;$i < count($rowd);$i++) {?>
                                        <tr>
                                            <td style="display: none;"><?= $rowd[$i][0] ?></td>
                                            <td></td>                                        
                                            <td class="campos text-center"><input name="cod[]" type="radio" 
                                                accept="" value="<?= $rowd[$i][0] ?>" 
                                                onClick="if (this.checked) valor(<?= $rowd[$i][8]; ?>);else valor(<?php echo $rowd[$i][8]; ?>)"></td> 

                                            <td class="campos text-right"><?= number_format($rowd[$i][8],2,'.',',');?></td>
                                            <td class="campos text-left"><?= $rowd[$i][1];?></td>
                                            <td class="campos text-left"><?= $rowd[$i][2];?></td>
                                            <td class="campos text-left"><?= $rowd[$i][3];?></td>
                                            <td class="campos text-right"><?= number_format($rowd[$i][6],2,'.',',');?></td>
                                            <td class="campos text-right"><?= number_format($rowd[$i][7],2,'.',',');?></td>
                                        </tr> 
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-offset-6  col-sm-6 text-left" style= " margin-top:5px;margin-bottom:-10px;display:<?php echo $a ?> " >
                            <div class="col-sm-3">
                                <div class="form-group"  align="left">                                    
                                    <label class="control-label">
                                        <strong class="obligado">*</strong><strong>Total Acuerdo:</strong>
                                    </label>                                
                                </div>
                            </div>                        
                            <div class="col-sm-2 text-right" align="left">
                                <input style="width: 220px;height: 30px; font-weight: bold;" class="form-control col-sm-1 text-right" placeholder="Total Acuerdo"  type="text" id="txtValor" name ="txtValor" readonly required>
                            </div>    
                        </div>
                        <div class="form-group form-inline" style="margin-top:-5px">         
                            <button id="enviar" type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 800px ; "><li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                        </div>
                    </form>
                </div>
            </div>
        </div>                                    
    </div>
    <div>
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
        $("#sltTipo").select2();
        $("#sltTercero").select2();
    </script>
    <script>
        $("#sltTipo").change(function () {
            let tp  = $('#sltTipo').val();
            let fch = $('#sltFechaA').val();
            if(tp!='' && fch !=''){
                window.location = 'registrar_GA_ACUERDO.php?tipo=' + tp + '&fecha=' + fch;
            }
        });
    </script>
    <script>
        $("#sltTercero").change(function () {
            let tp  = $('#sltTipo').val();
            let fch = $('#sltFechaA').val();
            let tr = $('#sltTercero').val();
            if(tp!='' && fch !='' && tr != ''){
                window.location = 'registrar_GA_ACUERDO.php?tipo=' + tp + '&fecha=' + fch+ '&tr=' + tr;
            }
        });
    </script>
    <script>
        function valor(valor) {
            $("#txtValor").val(valor);
            $("#txtValor1").val(valor);
        }
    </script> 
    <script type="text/javascript">
        $('#enviar').click(function () {
            var selected = '';
            var fch = $('#sltFechaA').val();
            var tp  = $('#sltTipo').val();
            var nct = $('#txtNumeroC').val();
            var pc  = $('#txtPorcentaje').val();
            var vlr = $('#txtValor').val();

            $('#formid input[type=radio]').each(function () {
                if (this.checked) {
                    selected += $(this).val() + ',';

                }
            });
            if (fch === '' || tp === '' || nct === ''  || pc === '' || vlr === '') {
                $("#myModalcomp").modal('show');
            } else {
                window.location = 'json/registrarAcuerdoJSON.php?codigos=' + selected + '&sltFechaA=' + fch +
                '&sltTipo=' + tp + '&txtNumeroC=' + nct  + '&txtPorcentaje=' + pc + '&txtValor=' + vlr + '&txtNumeroA='+$("#txtNumeroA").val();
            }
            return false;
        });
    </script>
</body>
</html>