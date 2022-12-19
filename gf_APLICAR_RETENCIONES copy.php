<?php
require_once('Conexion/conexion.php');
require_once 'head_listar.php';
$valorTotal     = 0;
if (!empty($_SESSION['idCompPtalCP'])) {
    $id     = $_SESSION['idCompPtalCP'];
} else {
    $id     = 0;
}
$_SESSION['id_comp_pptal_CP'] = $id;
if (!empty($_SESSION['id_comp_pptal_CP'])) {
    $valorTotal     = $_SESSION['valorTotCP'];
    $id_comp_Ptal   = $_SESSION['idCompPtalCP'];
}
$arr_sesiones_presupuesto = array('id_compr_pptal', 'id_comprobante_pptal', 'id_comp_pptal_ED', 'id_comp_pptal_ER', 'id_comp_pptal_CP', 'idCompPtalCP', 'idCompCntV', 'id_comp_pptal_GE', 'idCompCnt');
?>
<title>Aplicar Retenciones</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<script type="text/javascript"></script>
<style type="text/css">
    .area { height: auto !important;  }  
    .acotado{ white-space: normal;}
    table.dataTable thead th,table.dataTable thead td
    { padding: 1px 18px; font-size: 10px;}
    table.dataTable tbody td,table.dataTable tbody td
    { padding: 1px; }
    .dataTables_wrapper .ui-toolbar
    { padding: 2px; font-size: 10px; }
    .control-label { font-size: 12px; }
    .itemListado { margin-left:5px;margin-top:5px;width:150px;cursor:pointer;}
    #listado { width:150px; height:80px; overflow: auto; background-color: white; }
</style>
<link href="css/select/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script type="text/javascript" src="js/select2.js"></script>
</head>
<body>
    <input type="hidden" id="id_comp_Ptal" value="<?php echo $id_comp_Ptal; ?>">
    <input type="hidden" id="valorTotalOcul" value="<?php echo $valorTotal; ?>">
    <input type="hidden" id="consecutivo"> 
    <input type="hidden" id="numeralError"> 
    <input type="hidden" id="visibleActual" value="">
    <input type="hidden" id="idform" value="<?php echo $_GET['id'] ?>">
    <?php if (empty($_GET['mova'])) { ?>
        <input type="hidden" name="moviEscogidos" id="moviEscogidos" >
    <?php } else { ?>
        <input type="hidden" name="moviEscogidos" id="moviEscogidos" value="<?php echo $_GET['mova']; ?>">
    <?php } ?>
    <?php if (empty($_GET['tercero'])) { ?>
        <input type="hidden" name="tercero" id="tercero" >
    <?php } else { ?>
        <input type="hidden" name="tercero" id="tercero" value="<?php echo $_GET['tercero']; ?>">
    <?php } ?>
    <script type="text/javascript">
        $(document).ready(function ()
        { $("#consecutivo").val(0); });
    </script>
    <div class="container-fluid text-center"  >
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" >
                <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Aplicar Retenciones</h2>
                <div class="col-sm-10">
                    <div class="client-form contenedorForma2 col-sm-12"  style=""> 
                    </div>
                </div>
                <div class="table-responsive contTabla col-sm-12" style="margin-top: 5px;">
                    <div class="table-responsive contTabla" >
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"></td>
                                    <td class="cabeza"><strong>Clase Retención</strong></td>
                                    <td class="cabeza"><strong>Tipo Retención</strong></td>
                                    <td class="cabeza"><strong>% IVA</strong></td>
                                    <td class="cabeza"><strong>Aplicar Sobre</strong></td>
                                    <td class="cabeza"><strong>Valor Total</strong></td>
                                    <td class="cabeza"><strong>Valor Base</strong></td>
                                    <td class="cabeza"><strong>Retención a Aplicar</strong></td>
                                    <td class="cabeza"><strong>Cuenta Crédito</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Clase Retención</th>
                                    <th>Tipo Retención</th>
                                    <th>% IVA</th>
                                    <th>Aplicar Sobre</th>
                                    <th>Valor Total</th>
                                    <th>Valor Base</th>
                                    <th>Retención a Aplicar</th>
                                    <th>Cuenta Crédito</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="oculto">
                                    </td>
                                    <td class="campos" > 
                                        <a class="campos" id="modificarValoresRet0" style="cursor: pointer; display: none;" onclick="javascript: modificarValores(0);">
                                            <i title="Modificar" class="glyphicon glyphicon-edit">
                                            </i>
                                        </a>
                                    </td>
                                    <td class="campos" align="center">
                                        <?php
                                        $claseRet = "SELECT nombre, id_unico
                                            FROM gf_clase_retencion  ORDER BY nombre ASC";
                                        $rscR = $mysqli->query($claseRet);
                                        ?> 
                                        <select name="sltClaseRet0" id="sltClaseRet0" onchange="javascript: claseRet(0);" class="select2_single form-control input-sm" title="Seleccione Clase Retención" style="width: 94%;">
                                            <option value="" >Clase Retención</option>
                                            <?php
                                            while ($filacR = mysqli_fetch_row($rscR)) {
                                                echo '<option value="' . $filacR[1] . '">' . ucwords(($filacR[0])) . '</option>';
                                            } ?>   
                                        </select>
                                    </td>
                                    <td class="campos" align="center" >
                                        <select name="sltTipoRet0" id="sltTipoRet0" onclick="javascript: tipoRetencion(0);"  onchange="javascript: valTipoRetRep(0);" class="form-control input-sm" title="Seleccione Tipo Retención" style="width: 94%;">
                                            <option value="">Tipo Retención</option>
                                        </select>
                                    </td>
                                    <td class="campos" align="center" >
                                        <?php
                                        $porIVA = "SELECT valor 
                                            FROM gs_parametros_basicos WHERE nombre ='porcentaje iva' ";
                                        $rsPI = $mysqli->query($porIVA);
                                        $filaPI = mysqli_fetch_row($rsPI); ?> 
                                        <input type="number" step="1" min="0" max="100" value="<?php echo $filaPI[0]; ?>" name="porIVA0" id="porIVA0" onkeyup="javascript: porcIVA(0);" class="form-control input-sm" maxlength="100" title="" onkeypress="return txtValida(event, 'dec', 'porIVA', '2')" onclick="javascript: porcIVA(0);" placeholder="% IVA" style="width: 94%;" >
                                        <input type="hidden" id="paramIVA0" value="<?php echo $filaPI[0]; ?>">
                                    </td>
                                    <td class="campos" align="center" style="padding: 0px">
                                        <?php
                                        $aplicarS = "SELECT nombre, id_unico
                                            FROM gf_tipo_base WHERE nombre != '' ORDER BY nombre ASC";
                                        $rsaS = $mysqli->query($aplicarS);?>
                                        <select name="sltAplicarS0" id="sltAplicarS0" onclick="javascript: validarTres(0);" onchange="javascript: aplicarSob(0);" class="select2_single form-control input-sm" title="Aplicar Sobre" style="width: 94%;">
                                            <option value="" selected="selected">Aplicar sobre</option>
                                            <?php while ($filaaS = mysqli_fetch_row($rsaS)) {
                                                echo '<option value="' . $filaaS[1] . '">' . ucwords(($filaaS[0])) . '</option>';
                                            } ?>                                   
                                        </select>
                                    </td>
                                    <td class="campos" align="right" >
                                        <span class="valorTotal" id="valorTotal0"><?php echo number_format($valorTotal, 2, '.', ','); ?></span>
                                    </td>
                                    <td class="campos" align="right" >
                                        <span id="valorBase0"></span>
                                        <input type="text" id="valorBaseNuevo0" name="valorBaseNuevo0" style="display: none; width: 90%;" maxlength="50" placeholder="Valor" onkeypress="return txtValida(event, 'dec', 'valorBaseNuevo0', '2');" onkeyup="formatC('valorBaseNuevo0')">
                                        <input type="hidden" id="valorBaseM0" value="">
                                        <input type="hidden" id="valorBaseOcul0">
                                    </td>
                                    <td class="campos" align="right" >
                                        <span id="retencionApl0"></span>
                                        <a class="campos" id="ok0" style="cursor: pointer; display: none; position: absolute;" onclick="javascript: aceptarMod(0);">
                                            <i title="Ok" class="glyphicon glyphicon-ok"> </i>
                                        </a>
                                        <a class="campos" id="cancelar0" style="cursor: pointer; display: none; position: absolute; margin-left: 16px;" onclick="javascript: cancelarMod(0);">
                                            <i title="Cancelar" class="glyphicon glyphicon-remove"></i>
                                        </a>
                                        <input type="text" id="valorRetencionNuevo0" name="valorRetencionNuevo0" style="display: none; width: 75%;" maxlength="50" placeholder="Valor" onkeypress="return txtValida(event, 'dec', 'valorRetencionNuevo0', '2');" onkeyup="formatC('valorRetencionNuevo0')">
                                        <input type="hidden" id="retencionAplicaM0" value="">
                                        <input type="hidden" id="retencionAplOcul0">
                                    </td>
                                    <td class="campos" align="center" >
                                        <span id=""></span>
                                        <input type="hidden" id="">
                                        <input type="hidden" id="cuenCreOc0">
                                        <select name="cuenCre0" id="cuenCre0" class="form-control input-sm" title="Seleccione una cuenta" style="width: 94%;" >
                                        <?php
                                        $sqlDetCompP = "SELECT detComP.id_unico, detComP.rubrofuente   
                                            FROM gf_detalle_comprobante_pptal detComP 
                                            LEFT JOIN gf_comprobante_pptal comP ON comP.id_unico = detComP.comprobantepptal     
                                            WHERE comP.id_unico = '$id_comp_Ptal'";
                                        $comprobPtal = $mysqli->query($sqlDetCompP);
                                        while ($rowCP = mysqli_fetch_row($comprobPtal)) {
                                            $queryCuenCre = "SELECT cuen.id_unico, cuen.codi_cuenta, cuen.nombre 
                                            FROM gf_cuenta cuen 
                                            LEFT JOIN gf_concepto_rubro_cuenta conRubCun ON conRubCun.cuenta_credito = cuen.id_unico 
                                            LEFT JOIN gf_concepto_rubro conRub ON conRub.id_unico = conRubCun.concepto_rubro 
                                            LEFT JOIN gf_rubro_pptal rub ON rub.id_unico = conRub.rubro 
                                            LEFT JOIN gf_rubro_fuente rubFue ON rubFue.rubro = rub.id_unico 
                                            WHERE rubFue.id_unico = $rowCP[1]";
                                            $cuentaCre = $mysqli->query($queryCuenCre);
                                            while ($rowCC = mysqli_fetch_row($cuentaCre)) {
                                                echo '<option value="' . $rowCC[0] . '">' . $rowCC[1] . ' ' . $rowCC[2] . '</option>';
                                            }
                                        } ?>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="col-sm-12" align="right" style="margin-top: 10px;">
                            <button type="button" id="btnGenerarCom" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px;" title="Generar Comprobante CNT" >
                                <li class="glyphicon glyphicon-ok"></li>
                            </button>
                            <button type="button" id="btnNuevo" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px;" title="Generar Comprobante CNT" >
                                <li class="glyphicon glyphicon-plus"></li>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
if (!empty($_SESSION['id_comp_pptal_CP'])) { ?>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript">
        $(document).ready(function (){   
            id_comp = $("#id_comp_Ptal").val();
            var form_data = {estruc: 11, idCompPtal: id_comp};
            $.ajax({
                type: "POST",
                url: "estructura_aplicar_retenciones.php",
                data: form_data,
                success: function (response) {
                    response = parseInt(response);
                    if (response == 0) {
                        $("#btnGenerarCom ").prop("disabled", false);
                        $("#btnNuevo").prop("disabled", false);
                        validarTipoRetencion(0);
                    } else {
                        $("#btnGenerarCom ").prop("disabled", true);
                        $("#btnNuevo").prop("disabled", true);
                    }
                }
            });
          $(".select2_single").select2({
              allowClear: true
          });
            
        });
    </script>  
<?php }
if (empty($_SESSION['id_comp_pptal_CP'])) {    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#btnGenerarCom ").prop("disabled", true);
            $("#btnNuevo").prop("disabled", true);
        });
    </script>  
<?php } ?>
    <script type="text/javascript">
        function validarTipoRetencion(id) {
            limpiarValores(id);
            if ($("#sltTipoRet" + id).val() == ""){
                $("#btnNuevo").prop("disabled", true);
            } else {
                $("#btnNuevo").prop("disabled", false);
            }
        }
    </script>
    <script type="text/javascript">
        function porcIVA(num) {
            var numeral = num;
            $("#modificarValoresRet" + num).css("display", "none");
            validarOculto(num);
            var porIva = $("#porIVA" + numeral).val();
            if (porIva > 100) {
                $("#modErrorIva").modal('show');
                $("#porIVA" + numeral).attr('readonly', 'readonly');
            } else {
                $("#sltAplicarS" + numeral).val("").attr('selected', 'selected');
                $("#valorBase" + numeral).text("");
                $("#valorBaseOcul" + numeral).val("");
                $("#retencionApl" + numeral).text("");
                $("#retencionAplOcul" + numeral).val("");
            }
        }
    </script>
    <script type="text/javascript">
        function limpiarValores(numeral) {
            if ($("#sltAplicarS" + numeral).val() != "") {
                $("#sltAplicarS" + numeral).val("").attr('selected', 'selected').focus();
                $("#valorBase" + numeral).text("");
                $("#valorBaseOcul" + numeral).val("");
                $("#retencionApl" + numeral).text("");
                $("#retencionAplOcul" + numeral).val("");
            }
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#btnNuevo").click(function () {
                var consecutivo = $("#consecutivo").val();
                consecutivo = parseInt(consecutivo);
                consecutivo += 1;
                $("#consecutivo").val(consecutivo);
                var id_comp_Ptal = $("#id_comp_Ptal").val();
                var valorTotal = $("#valorTotalOcul").val();
                var form_data = {estruc: 14, id_comp_Ptal: id_comp_Ptal, valorTotal: valorTotal, consecutivo: consecutivo};
                $.ajax({
                    type: "POST",
                    url: "estructura_aplicar_retenciones.php",
                    data: form_data,
                    success: function (data){
                        $("#tabla tbody").append(data);
                    }
                });
            });
        });
    </script>
    <script type="text/javascript"> 
        function claseRet(num) {
            var numeral = num;
            $("#modificarValoresRet" + num).css("display", "none");
            validarOculto(num);
            limpiarValores(numeral);
            var opcion = '<option value="">Tipo Retención</option>';
            if (($("#sltClaseRet" + numeral).val() != "") && ($("#sltClaseRet" + numeral).val() != 0)){
                var id_clas_rt = $("#sltClaseRet" + numeral).val();
                var ter =$("#tercero").val();
                var form_data = {estruc: 1, id_clase_ret: id_clas_rt,tercero:ter};
                $.ajax({
                    type: "POST",
                    url: "estructura_aplicar_retenciones.php",
                    data: form_data,
                    success: function (response){
                        resultado = JSON.parse(response);
                        console.log(resultado);
                        var options = resultado["option"];
                        var base    = resultado["base_i"];
                      
                        if (options != 0 && options != "") {
                            opcion += options;
                            $("#sltTipoRet" + numeral).html(opcion).focus();
                            validarTipoRetencion(numeral);
                            if (base==1) {
                                console.log('F');
                                valTipoRetRep(numeral)
                                aplicarSob(numeral);
                            }
                        } else {
                            opcion = '<option value="">No hay tipo retención</option>';
                            $("#sltTipoRet" + numeral).html(opcion);
                            validarTipoRetencion(numeral);
                        }
                    }
                }); 
            } else {
                $("#sltTipoRet" + numeral).html(opcion);
                validarTipoRetencion(numeral);
            }
        }
    </script> 
    <script type="text/javascript"> //
        function tipoRetencion(num) {
            var numeral = num;
            if (($("#sltClaseRet" + numeral).val() == "") || ($("#sltClaseRet" + numeral).val() == 0)){
                $("#modErrorTipRet").modal('show');
            }
        }
    </script> 
    <script type="text/javascript"> 
        function valTipoRetRep(num) {
            var numeral = num;
            $("#modificarValoresRet" + num).css("display", "none");
            validarOculto(num);
            var consecutivo = $("#consecutivo").val();
            var valorTipRet = $("#sltTipoRet" + num).val();
            validarTipoRetencion(numeral);
            
            if (consecutivo != 0) {
                for (var i = 0; i <= consecutivo; i++) {
                    if (i != num && valorTipRet == $("#sltTipoRet" + i).val()) {
                        $("#modTipRetRepetido").modal('show'); //Ya existe un tipo de retención igual a esta.
                        $("#numeralError").val(num);
                        validarTipoRetencion(numeral);
                        break;
                    }
                }
            }
            var form_data = {estruc: 25, tiporetencion: valorTipRet};
            $.ajax({
                type: "POST",
                url: "estructura_aplicar_retenciones.php",
                data: form_data,
                success: function (response) {
                    $("#sltAplicarS" + numeral).html(response);   
                    aplicarSob(numeral);
                }
            });
        }
    </script> 
    <script type="text/javascript"> //
        function aplicarSob(num) {
            
            var numeral = num;
            $("#modificarValoresRet" + num).css("display", "none");
            validarOculto(num);
            if (($("#sltTipoRet" + numeral).val() != "") && ($("#sltTipoRet" + numeral).val() != 0)){
                console.log($("#sltAplicarS" + numeral).val());
                if ($("#porIVA" + numeral).val() == 0 || $("#porIVA" + numeral).val() == ""){
                    $("#modErrIva").modal('show');
                    
                } else if (($("#sltAplicarS" + numeral).val() != "") && ($("#sltAplicarS" + numeral).val() != 0)){
               
                    var tipoRete = $("#sltTipoRet" + numeral).val();
                    var aplicar = $("#sltAplicarS" + numeral).val();
                    var valor = $("#valorTotal" + numeral).text();
                    valor = parseFloat(valor.replace(/\,/g, ''));
                    var iva = $("#porIVA" + numeral).val();
                    var form_data = {estruc: 2, aplicar: aplicar, valor: valor, iva: iva, tipoRete: tipoRete};
                    $.ajax({
                        type: "POST",
                        url: "estructura_aplicar_retenciones.php",
                        data: form_data,
                        success: function (response){
                            var valorBase = parseFloat(response).toFixed(2);
                            $("#valorBase" + numeral).html(valorBase);
                            $("#valorBaseOcul" + numeral).val(valorBase);
                            formatC('valorBaseOcul' + numeral);
                            $("#valorBase" + numeral).html($("#valorBaseOcul" + numeral).val());
                            retencionAplicar(numeral);
                        }
                    }); 
                } else {
                    $("#valorBase" + numeral).html("");
                    $("#valorBaseOcul" + numeral).val("");
                    $("#retencionApl" + numeral).html("");
                    $("#retencionAplOcul" + numeral).val("");
                }
            } else {
                $("#modErrorRet").modal('show');
            }
        }
        function retencionAplicar(numeral) {
            var idTipRet = $("#sltTipoRet" + numeral).val();
            var valorBas = $("#valorBase" + numeral).text();
            valorBas = parseFloat(valorBas.replace(/\,/g, ''));
            var form_data = {estruc: 3, valorBas: valorBas, idTipRet: idTipRet};
            $.ajax({
                type: "POST",
                url: "estructura_aplicar_retenciones.php",
                data: form_data,
                success: function (response) {
                    console.log('retencion');
                    console.log(response);
                    var retApl = parseFloat(response).toFixed(2);
                    $("#retencionApl" + numeral).html(retApl);
                    $("#retencionAplOcul" + numeral).val(retApl);
                    formatC('retencionAplOcul' + numeral);
                    $("#retencionApl" + numeral).html($("#retencionAplOcul" + numeral).val());
                }
            }); 
        }
        function modificarValores(num) {
            validarOculto(num);
            var retencionAplicaM = $("#retencionAplicaM" + num).val();
            var valorBaseM = $("#valorBaseM" + num).val();
            if (valorBaseM == 1) {
                $("#valorBase" + num).css("display", "none");
                var valorBse = $("#valorBaseOcul" + num).val();
                $("#valorBaseNuevo" + num).val(valorBse);
                $("#valorBaseNuevo" + num).css("display", "block");
            }
            if (retencionAplicaM == 1) {
                $("#retencionApl" + num).css("display", "none");
                var valorRtencion = $("#retencionAplOcul" + num).val();
                $("#valorRetencionNuevo" + num).val(valorRtencion);
                $("#valorRetencionNuevo" + num).css("display", "block");
            }
            $("#ok" + num).css("display", "block");
            $("#cancelar" + num).css("display", "block");
            $("#visibleActual").val(num);
        }
        function cancelarMod(num) {
            $("#visibleActual").val("");
            $("#valorBaseNuevo" + num).css("display", "none");
            $("#valorRetencionNuevo" + num).css("display", "none");
            $("#ok" + num).css("display", "none");
            $("#cancelar" + num).css("display", "none");
            $("#valorBase" + num).css("display", "block");
            $("#retencionApl" + num).css("display", "block");
        }
        function aceptarMod(num){
            if ($('#valorBaseNuevo' + num).is(":visible")){
                var valorBse = $("#valorBaseNuevo" + num).val();
                $("#valorBaseOcul" + num).val(valorBse);
                $("#valorBase" + num).text(valorBse);
                $("#valorBaseNuevo" + num).css("display", "none");
                $("#valorBase" + num).css("display", "block");
                var idTipRet = $("#sltTipoRet" + num).val();
                valorBse = parseFloat(valorBse.replace(/\,/g, ''));
                var form_data = {estruc: 3, valorBas: valorBse, idTipRet: idTipRet};
                $.ajax({
                    type: "POST",
                    url: "estructura_aplicar_retenciones.php",
                    data: form_data,
                    success: function (response){
                        console.log(response);
                        var retApl = parseFloat(response).toFixed(2);
                        $("#retencionApl" + num).html(retApl);
                        $("#retencionAplOcul" + num).val(retApl);
                        formatC('retencionAplOcul' + num);
                        $("#retencionApl" + num).html($("#retencionAplOcul" + num).val());
                    }//Fin succes.
                });
            }
            if ($('#valorRetencionNuevo' + num).is(":visible")){
                var valorRtencion = $("#valorRetencionNuevo" + num).val();
                $("#retencionAplOcul" + num).val(valorRtencion);
                $("#retencionApl" + num).text(valorRtencion);
                $("#valorRetencionNuevo" + num).css("display", "none");
                $("#retencionApl" + num).css("display", "block");
            }
            $("#visibleActual").val("");
            $("#ok" + num).css("display", "none");
            $("#cancelar" + num).css("display", "none");
        }
        function validarOculto(num){
            var actual = $("#visibleActual").val()
            if (actual != ""){
                if ($('#ok' + actual).is(":visible")){
                    if (num != actual){
                        $("#valorBaseNuevo" + actual).css("display", "none");
                        $("#valorRetencionNuevo" + actual).css("display", "none");
                        $("#ok" + actual).css("display", "none");
                        $("#cancelar" + actual).css("display", "none");
                        $("#valorBase" + actual).css("display", "block");
                        $("#retencionApl" + actual).css("display", "block");
                    } else {
                        console.log('aca1');
                        $("#visibleActual").val("");
                        $("#valorBaseNuevo" + num).css("display", "none");
                        $("#valorRetencionNuevo" + num).css("display", "none");
                        $("#ok" + num).css("display", "none");
                        $("#cancelar" + num).css("display", "none");
                        $("#valorBase" + num).css("display", "block");
                        $("#retencionApl" + num).css("display", "block");
                        $("#modificarValoresRet" + num).css("display", "none");
                    }
                }
            }
        }
    </script>
    <script type="text/javascript">
        function validarTres(num){
            $("#sltAplicarS" + num).change(function (){
                var tipoRete = $("#sltTipoRet" + num).val();
                var aplicar = $("#sltAplicarS" + num).val();
                var form_data = {estruc: 22, aplicar: aplicar, tipoRete: tipoRete};
                $.ajax({
                    type: "POST",
                    url: "estructura_aplicar_retenciones.php",
                    data: form_data,
                    success: function (response){
                        response = response.trim();
                        if (response != 0){ 
                            var valida = response.split("|");
                            if (valida[0] == 1 || valida[1] == 1){
                                $("#modificarValoresRet" + num).css("display", "block");
                            } else {
                                console.log('aca2');
                                $("#modificarValoresRet" + num).css("display", "none");
                            }
                            $("#retencionAplicaM" + num).val(valida[0]);
                            $("#valorBaseM" + num).val(valida[1]);
                        } else {
                            $("#retencionAplicaM").val("");
                            $("#valorBaseM").val("");
                        }
                    }
                }); 
            });
        }
    </script>
    <div class="modal fade" id="modErrorIva" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El valor del porcentaje del IVA no puede ser superior a 100%</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrorIva" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modErrorRet" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Debe seleccionar un Tipo Retención</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrorRet" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modErrorTipRet" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Debe seleccionar una Clase Retención</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrorTipRet" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modGuarExito" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información guardada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnGuarEx" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modGuarError" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido guardar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnGuarErr" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modVacioError" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha calculado la retención.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnVacErr" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modErrIva" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El valor del IVA no es el adecuado o está vacío.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrIva" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modReptError" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Ya existe una retención para este comprobante con el tipo de retención seleccionado.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrRep" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modCNTExito" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Se ha guardado el Comprobante CNT.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCntExi" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modCNTError" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha guardado el Comprobante CNT.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCntErr" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modNoTipComCNT" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No hay un tipo de comprobante CNT configurado para este tipo de comprobante PPTAL.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCntErr_" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modTipRetRepetido" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Ya existe un tipo de retención igual a esta.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnTipRetRepetido" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modNoCuentTipRet" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El tipo de retención seleccionado no tiene cuenta. Se ingresaron los datos pero no la retención.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoCuentTipRet" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modTipoReteNoPermit" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Este tipo de retención no permite cambiar el valor base y la retención a aplicar.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnTipoReteNoPermit" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <?php require_once 'footer.php'; ?>
    <script type="text/javascript">
        $('#btnErrorIva').click(function () {
            var numeral = parseInt($("#numeralError").val());
            $("#porIVA" + numeral).val($("#paramIVA" + numeral).val());
            $("#porIVA" + numeral).removeAttr('readonly');
        });
        $('#btnErrIva').click(function ()    {
            var numeral = parseInt($("#numeralError").val());

            $("#porIVA" + numeral).val($("#paramIVA" + numeral).val()).focus();
            $("#sltAplicarS" + numeral).val("").attr('selected', 'selected');
        });
    </script>
    <script type="text/javascript">
        $('#btnErrorRet').click(function ()
        {
            var numeral = parseInt($("#numeralError").val());

            $("#sltTipoRet" + numeral).focus();
            $("#sltAplicarS" + numeral).val("").attr('selected', 'selected');
        });
    </script>
    <script type="text/javascript">
        $('#btnErrorTipRet').click(function ()
        {
            var numeral = parseInt($("#numeralError").val());

            $("#sltClaseRet" + numeral).focus();
            $("#sltTipoRet" + numeral).val("").attr('selected', 'selected');
        });
    </script>
    <script type="text/javascript">
        $('#btnVacErr').click(function ()
        {

            var numeral = parseInt($("#numeralError").val());

            $("#sltAplicarS" + numeral).val("").attr('selected', 'selected').focus();
            $('#btnGenerarCom').prop("disabled", false);
        });
    </script>

    <script type="text/javascript">
        $('#btnGenerarCom').click(function ()
        {

            $('#btnGenerarCom').prop("disabled", true);

            var numeral = parseInt($("#consecutivo").val());
            var num = numeral;
            var pasa = 0;
            var error = 0;
            if ($("#sltClaseRet0").val() != "") {
                if (numeral == 0)
                {
                    if ($("#valorBaseOcul0").val() != 0 && $("#valorBaseOcul0").val() != "")
                    {
                        var valorRet = $("#retencionAplOcul0").val();
                        valorRet = parseFloat(valorRet.replace(/\,/g, ''));

                        var retencionBas = $("#valorBaseOcul0").val();
                        retencionBas = parseFloat(retencionBas.replace(/\,/g, ''));

                        var porcenRet = $("#porIVA0").val();
                        var tipoRet = $("#sltTipoRet0").val();
                        var cuentaDesRet = $("#cuenCre0").val();

                        pasa = 1;
                    } else
                    {
                        $("#modVacioError").modal('show'); // Mensaje: No se ha calculado la retención.
                    }
                } else
                {
                    var valorRet = new Array();
                    var retencionBas = new Array();
                    var porcenRet = new Array();
                    var tipoRet = new Array();
                    var cuentaDesRet = new Array();

                    for (var i = 0; i <= num; i++)
                    {
                        numeral = i;
                        if ($("#valorBaseOcul" + numeral).val() != 0 && $("#valorBaseOcul" + numeral).val() != "")
                        {
                            var valorRetV = $("#retencionAplOcul" + numeral).val();
                            valorRetV = parseFloat(valorRetV.replace(/\,/g, ''));
                            valorRetV = valorRetV.toString();
                            valorRet[i] = valorRetV;

                            var retencionBasV = $("#valorBaseOcul" + numeral).val();
                            retencionBasV = parseFloat(retencionBasV.replace(/\,/g, ''));
                            retencionBasV = retencionBasV.toString();
                            retencionBas[i] = retencionBasV;

                            var porcenRetV = $("#porIVA" + numeral).val();
                            porcenRet[i] = porcenRetV;

                            var tipoRetV = $("#sltTipoRet" + numeral).val();
                            tipoRet[i] = tipoRetV;

                            var cuentaDesRetV = $("#cuenCre" + numeral).val();
                            cuentaDesRet[i] = cuentaDesRetV;
                        } else
                        {
                            $("#numeralError").val(numeral);
                            error = 1;
                            break;
                        }
                    }

                    if (error != 1)
                    {
                        pasa = 1;

                        valorRet = serializeArr(valorRet);

                        retencionBas = serializeArr(retencionBas);

                        porcenRet = serializeArr(porcenRet);

                        tipoRet = serializeArr(tipoRet);

                        cuentaDesRet = serializeArr(cuentaDesRet);

                    } else
                    {
                        $("#modVacioError").modal('show'); // Mensaje: No se ha calculado la retención.
                    }

                }
                console.log(pasa);
                if (pasa != 0)
                {
                    var compRet = $("#id_comp_Ptal").val();

                    var form_data = {estruc: 4, valorRet: valorRet, retencionBas: retencionBas, porcenRet: porcenRet, compRet: compRet, tipoRet: tipoRet, cuentaDesRet: cuentaDesRet, numReng: num};

                    $.ajax({
                        type: "POST",
                        url: "estructura_aplicar_retenciones.php",
                        data: form_data,
                        success: function (response)
                        {
                            console.log('es4' + response);
                            response = parseInt(response);

                            if (response == 1)
                            {
                                var numeral = parseInt($("#consecutivo").val());
                                var num = numeral;

                                if (numeral == 0 && $("#sltTipoRet0").val() == "")
                                {

                                    var compRet = $("#id_comp_Ptal").val();

                                    var valorRet = $("#retencionAplOcul0").val();
                                    valorRet = parseFloat(valorRet.replace(/\,/g, ''));

                                    var retencionBas = $("#valorBaseOcul0").val();
                                    retencionBas = parseFloat(retencionBas.replace(/\,/g, ''));

                                    var form_data = {estruc: 21, compRet: compRet, valorRet: valorRet, retencionBas: retencionBas,
                                        idform: $("#idform").val()};

                                    $.ajax({
                                        type: "POST",
                                        url: "estructura_aplicar_retenciones.php",
                                        data: form_data,
                                        success: function (response)
                                        {
                                            console.log(response + '21');
                                            /* (Dejar comentado para le cliente) Comentar las siguientes tres líneas */
                                            var numeroLetras = response.length;
                                            response = response.substr(numeroLetras - 1, 1);

                                            response = parseInt(response);
                                            console.log(response);
                                            if (response == 1)
                                            {
                                                $("#modCNTExito").modal('show');
                                                $(".ocultarSiGuarda").hide();
                                            } else if (response == 2)
                                            {
                                                $("#modNoTipComCNT").modal('show');
                                            } else if (response == 0)
                                            {
                                                $("#modCNTError").modal('show');
                                            }

                                        }//Fin succes.
                                    }); //Fin ajax.

                                } else
                                {

                                    if (numeral == 0)
                                    {

                                        var valorRet = $("#retencionAplOcul0").val();
                                        valorRet = parseFloat(valorRet.replace(/\,/g, ''));

                                        var retencionBas = $("#valorBaseOcul0").val();
                                        retencionBas = parseFloat(retencionBas.replace(/\,/g, ''));

                                        var porcenRet = $("#porIVA0").val();
                                        var tipoRet = $("#sltTipoRet0").val();
                                        var cuentaDesRet = $("#cuenCre0").val();

                                    } else
                                    {
                                        var valorRet = new Array();
                                        var retencionBas = new Array();
                                        var porcenRet = new Array();
                                        var tipoRet = new Array();
                                        var cuentaDesRet = new Array();

                                        for (var i = 0; i <= num; i++)
                                        {
                                            numeral = i;

                                            var valorRetV = $("#retencionAplOcul" + numeral).val();
                                            valorRetV = parseFloat(valorRetV.replace(/\,/g, ''));
                                            valorRetV = valorRetV.toString();
                                            valorRet[i] = valorRetV;

                                            var retencionBasV = $("#valorBaseOcul" + numeral).val();
                                            retencionBasV = parseFloat(retencionBasV.replace(/\,/g, ''));
                                            retencionBasV = retencionBasV.toString();
                                            retencionBas[i] = retencionBasV;

                                            var porcenRetV = $("#porIVA" + numeral).val();
                                            porcenRet[i] = porcenRetV;

                                            var tipoRetV = $("#sltTipoRet" + numeral).val();
                                            tipoRet[i] = tipoRetV;

                                            var cuentaDesRetV = $("#cuenCre" + numeral).val();
                                            cuentaDesRet[i] = cuentaDesRetV;
                                        }

                                        valorRet = serializeArr(valorRet);

                                        retencionBas = serializeArr(retencionBas);

                                        porcenRet = serializeArr(porcenRet);

                                        tipoRet = serializeArr(tipoRet);

                                        cuentaDesRet = serializeArr(cuentaDesRet);

                                    }

                                    var compRet = $("#id_comp_Ptal").val();

                                    var form_data = {estruc: 5, valorRet: valorRet, retencionBas: retencionBas, porcenRet: porcenRet,
                                        compRet: compRet, tipoRet: tipoRet, cuentaCred: cuentaDesRet,
                                        numReng: num, idform: $("#idform").val(), mova: $("#moviEscogidos").val()};

                                    $.ajax({
                                        type: "POST",
                                        url: "estructura_aplicar_retenciones.php",
                                        data: form_data,
                                        success: function (response)
                                        {
                                            console.log(response + 'case 5');

                                            /* (Dejar comentado para le cliente) Comentar las siguientes tres líneas */
                                            var numeroLetras = response.length;
                                            response = response.substr(numeroLetras - 1, 1);

                                            response = parseInt(response);
                                            console.log(response);
                                            if (response == 1)
                                            {
                                                $("#modCNTExito").modal('show');
                                                $(".ocultarSiGuarda").hide();
                                            } else if (response == 2)
                                            {
                                                $("#modNoTipComCNT").modal('show');
                                            } else if (response == 0)
                                            {
                                                $("#modCNTError").modal('show');
                                            } else if (response == 3)
                                            {
                                                $("#modNoCuentTipRet").modal('show');
                                            }

                                        }//Fin succes.
                                    }); //Fin ajax.
                                }
                            } else if (response == 2)
                            {
                                $("#modReptError").modal('show');
                            } else if (response == 0)
                            {
                                $("#modGuarError").modal('show');
                            }

                        }//Fin succes.
                    }); //Fin ajax.
                }
            } else {

                var numeral = parseInt($("#consecutivo").val());
                var num = numeral;

                if (numeral == 0 && $("#sltTipoRet0").val() == "")
                {

                    var compRet = $("#id_comp_Ptal").val();

                    var valorRet = $("#retencionAplOcul0").val();
                    valorRet = parseFloat(valorRet.replace(/\,/g, ''));

                    var retencionBas = $("#valorBaseOcul0").val();
                    retencionBas = parseFloat(retencionBas.replace(/\,/g, ''));

                    var form_data = {estruc: 21, compRet: compRet, valorRet: valorRet, retencionBas: retencionBas,
                        idform: $("#idform").val(), mova: $("#moviEscogidos").val()};

                    $.ajax({
                        type: "POST",
                        url: "estructura_aplicar_retenciones.php",
                        data: form_data,
                        success: function (response)
                        {
                            console.log(response + '21');
                            /* (Dejar comentado para le cliente) Comentar las siguientes tres líneas */
                            var numeroLetras = response.length;
                            response = response.substr(numeroLetras - 1, 1);

                            response = parseInt(response);
                            console.log(response);
                            if (response == 1)
                            {
                                $("#modCNTExito").modal('show');
                                $(".ocultarSiGuarda").hide();
                            } else if (response == 2)
                            {
                                $("#modNoTipComCNT").modal('show');
                            } else if (response == 0)
                            {
                                $("#modCNTError").modal('show');
                            }

                        }//Fin succes.
                    }); //Fin ajax.

                } else
                {

                    if (numeral == 0)
                    {

                        var valorRet = $("#retencionAplOcul0").val();
                        valorRet = parseFloat(valorRet.replace(/\,/g, ''));

                        var retencionBas = $("#valorBaseOcul0").val();
                        retencionBas = parseFloat(retencionBas.replace(/\,/g, ''));

                        var porcenRet = $("#porIVA0").val();
                        var tipoRet = $("#sltTipoRet0").val();
                        var cuentaDesRet = $("#cuenCre0").val();

                    } else
                    {
                        var valorRet = new Array();
                        var retencionBas = new Array();
                        var porcenRet = new Array();
                        var tipoRet = new Array();
                        var cuentaDesRet = new Array();

                        for (var i = 0; i <= num; i++)
                        {
                            numeral = i;

                            var valorRetV = $("#retencionAplOcul" + numeral).val();
                            valorRetV = parseFloat(valorRetV.replace(/\,/g, ''));
                            valorRetV = valorRetV.toString();
                            valorRet[i] = valorRetV;

                            var retencionBasV = $("#valorBaseOcul" + numeral).val();
                            retencionBasV = parseFloat(retencionBasV.replace(/\,/g, ''));
                            retencionBasV = retencionBasV.toString();
                            retencionBas[i] = retencionBasV;

                            var porcenRetV = $("#porIVA" + numeral).val();
                            porcenRet[i] = porcenRetV;

                            var tipoRetV = $("#sltTipoRet" + numeral).val();
                            tipoRet[i] = tipoRetV;

                            var cuentaDesRetV = $("#cuenCre" + numeral).val();
                            cuentaDesRet[i] = cuentaDesRetV;
                        }

                        valorRet = serializeArr(valorRet);

                        retencionBas = serializeArr(retencionBas);

                        porcenRet = serializeArr(porcenRet);

                        tipoRet = serializeArr(tipoRet);

                        cuentaDesRet = serializeArr(cuentaDesRet);

                    }

                    var compRet = $("#id_comp_Ptal").val();

                    var form_data = {estruc: 5, valorRet: valorRet, retencionBas: retencionBas, porcenRet: porcenRet,
                        compRet: compRet, tipoRet: tipoRet, cuentaCred: cuentaDesRet,
                        numReng: num, idform: $("#idform").val()};

                    $.ajax({
                        type: "POST",
                        url: "estructura_aplicar_retenciones.php",
                        data: form_data,
                        success: function (response)
                        {
                            console.log(response + '5');

                            /* (Dejar comentado para le cliente) Comentar las siguientes tres líneas */
                            var numeroLetras = response.length;
                            response = response.substr(numeroLetras - 1, 1);

                            response = parseInt(response);
                            console.log(response);
                            if (response == 1)
                            {
                                $("#modCNTExito").modal('show');
                                $(".ocultarSiGuarda").hide();
                            } else if (response == 2)
                            {
                                $("#modNoTipComCNT").modal('show');
                            } else if (response == 0)
                            {
                                $("#modCNTError").modal('show');
                            } else if (response == 3)
                            {
                                $("#modNoCuentTipRet").modal('show');
                            }

                        }//Fin succes.
                    }); //Fin ajax.
                }





            }

        });

    </script>
    <script type="text/javascript"></script>
    <script type="text/javascript"> //Aquí
        $('#btnCntExi').click(function ()
        {
            var form = $("#idform").val();

            if (form == 2) {
                document.location = 'registro_COMPROBANTE_EGRESO.php'; //Dejar esta siempre.
            } else {
                document.location = 'registro_COMPROBANTE_CNT.php'; //Dejar esta siempre.
            }
            //window.open('registro_COMPROBANTE_CNT.php'); // Usar para probar.
        });
    </script>
    <script type="text/javascript">
        function quitarRenglon(id)
        {
            $("#renglon" + id).remove();
            var consecutivo = $("#consecutivo").val();
            consecutivo -= 1;
            $("#consecutivo").val(consecutivo);
        }
    </script>
    <script type="text/javascript">
        function serializeArr(arr)
        {
            var res = 'a:' + arr.length + ':{';
            for (ni = 0; ni < arr.length; ni++)
            {
                res += 'i:' + ni + ';s:' + arr[ni].length + ':"' + arr[ni] + '";';
            }
            res += '}';

            return res;
        }

    </script>
    <script type="text/javascript">
        $('#btnTipRetRepetido').click(function ()
        {
            var num = $("#numeralError").val();
            $("#sltTipoRet" + num).val("");
            $("#numeralError").val("");

        });
    </script>
    <script type="text/javascript">
        $('#btnCntErr_').click(function ()
        {
            activaBtnGenCom();

        });
    </script>
    <script type="text/javascript">
        $('#btnCntErr').click(function ()
        {
            activaBtnGenCom();

        });
    </script>
    <script type="text/javascript">
        function activaBtnGenCom()
        {
            $("#btnGenerarCom").prop("disabled", false);
        }

    </script>

    <script type="text/javascript"> //Aquí
        $('#btnNoCuentTipRet').click(function ()
        {

            var form = $("#idform").val();


            if (form == 2) {
                document.location = 'registro_COMPROBANTE_EGRESO.php'; //Dejar esta siempre.
            } else {
                document.location = 'registro_COMPROBANTE_CNT.php'; //Dejar esta siempre.
            }
            //window.open('registro_COMPROBANTE_CNT.php'); // Usar para probar.
        });
    </script>

</body>
</html>