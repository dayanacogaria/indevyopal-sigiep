<?php
    require 'head.php';
    require 'Conexion/conexion.php';
    $compania = $_SESSION['compania'];
?>
    <title>Depreciación Productos de Almacén</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/datapicker.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-notify.css">
    <style type="text/css" media="screen">
        .client-form input[type="text"]{
            width: 100%;
        }

        .client-form select{
            width: 100%;
        }

        .btn{
            box-shadow: 0px 2px 5px 1px grey;
        }

        .client-form input[type="file"]{
            width: 100%
        }

        #tbListadoD>thead, #tbListadoD>tbody { display: block; }

        #tbListadoD>tbody {
            height: 220px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        #tbListadoD>thead>tr>th,#tbListadoD>tbody>tr>td{
            width: 191px;
        }
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require('menu.php'); ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px;">Depreciación de Productos</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonAlmacen/depreciacionAlmacenProductoJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="optDepIni1" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Depreciación Inicial?</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 text-left">
                                <label for="optDepIni1" class="radio-inline"><input type="radio" name="optDepIni" id="optDepIni1" value="0" required=""> SI</label>
                                <label for="optDepIni2" class="radio-inline"><input type="radio" name="optDepIni" id="optDepIni2" value="1">NO</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtFechaFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Mes y Año Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtFechaFinal" id="txtFechaFinal" placeholder="Fecha Final" title="Ingrese Fecha Final" class="form-control" required="" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltProductoInicial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Producto Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 text-left">
                                <select name="sltProductoInicial" id="sltProductoInicial" class="select2 form-control" required="">
                                    <?php
                                    $html= "";
                                    $html.= "<option value=\"\">Producto Inicial</option>";
                                    $sqlp = "SELECT DISTINCT
                                                       pr.id_unico AS PRODCTO,
                                                       pln.nombre  AS NOM_PLAN,
                                                       pes.valor   AS SERIE
                                            FROM       gf_producto pr
                                            LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                                            LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                                            LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                                            LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                                            LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                                            WHERE      fic.elementoficha   = 6
                                            AND        (pln.tipoinventario   = 2)
                                            AND        pln.compania  = $compania
                                            ORDER BY   pr.id_unico ASC";
                                    $resp = $mysqli->query($sqlp);
                                    while($rowp = mysqli_fetch_row($resp)){
                                        $html.= "<option value=\"$rowp[0]\"> SERIE :".$rowp[2]." NOMBRE :".ucwords(mb_strtolower($rowp[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltProductoFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Producto Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltProductoFinal" id="sltProductoFinal" class="select2 form-control text-left" required="">
                                    <?php
                                    $html= "";
                                    $html.= "<option value=\"\">Producto Final</option>";
                                    $sqlp = "SELECT DISTINCT
                                                       pr.id_unico AS PRODCTO,
                                                       pln.nombre  AS NOM_PLAN,
                                                       pes.valor   AS SERIE
                                            FROM       gf_producto pr
                                            LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                                            LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                                            LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                                            LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                                            LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                                            WHERE      fic.elementoficha   = 6
                                            AND        (pln.tipoinventario  = 2)
                                            AND        pln.compania  = $compania
                                            ORDER BY   pr.id_unico DESC";
                                    $resp = $mysqli->query($sqlp);
                                    while($rowp = mysqli_fetch_row($resp)){
                                        $html.= "<option value=\"$rowp[0]\"> SERIE :".$rowp[2]." NOMBRE :".ucwords(mb_strtolower($rowp[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <label for="sltProductoFinal" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                                <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                    <button type="submit" class="btn btn-primary glyphicon glyphicon-play"></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" id="modalListadoE" role="dialog" align="center" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <p>Ya existe depreciación para varios de los elementos seleccionados.</p>
                            <!--< div class="row">
                                <div class="table-responsive">
                                    <table id="tbListadoD" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Elemento</th>
                                                <th>Fecha</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div> -->
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btn-procesar" class="btn" style="color: #000; margin-top: 2px" >Aceptar</button>
                            <button type="button" id="btn-cerrar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalMes" role="dialog" align="center" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <p>No hay depreciación del(los) producto(s) en el mes anterior.</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btn-cerrar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalParamI" role="dialog" align="center" >
                <div class="modal-dialog" style="width: 300px">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                        </div>
                        <div class="modal-body row">
                            <form action="#" id="form-guardar" class="form-horizontal" method="POST">
                                <p align="center" style="margin-bottom: 5px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                <div class="form-group">
                                    <label for="txtDepInici" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Periodo Depreciación:</label>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <input type="text" name="txtDepInici" id="txtDepInici" class="form-control" value="" required="required" title="Fecha de Depreciación Inicial" readonly="">
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btn-guardar" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                            <button type="button" id="btn-cerrar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php require('footer.php'); ?>
            <script src="js/jquery-ui.js"></script>
            <script type="text/javascript" src="js/select2.js"></script>
            <script src="dist/jquery.validate.js"></script>
            <script src="js/bootstrap-notify.js"></script>
            <script type="text/javascript" src="js/md5.js"></script>
            <script src="js/plugins/datepicker/bootstrap-datepicker.js"></script>
            <script>
                jQuery(function ($) {

                    $.fn.datepicker.dates['en'] = {
                        days:        ['Domingo',   'Lunes',  'Martes',
                                      'Miércoles', 'Jueves', 'Viernes',
                                      'Sábado'],
                        daysShort:   ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
                        daysMin:     ['Do',  'Lu',  'Ma',  'Mi',  'Ju',  'Vi',  'Sá'],
                        months:      ['Enero',   'Febrero',   'Marzo',
                                      'Abril',   'Mayo',      'Junio',
                                      'Julio',   'Agosto',    'Septiembre',
                                      'Octubre', 'Noviembre', 'Diciembre'],
                        monthsShort: ["Ene", "Feb", "Mar", "Apr", "May", "Jun",
                                      "Jul", "Aug", "Sep", "Oct", "Nov", "Dic"],
                        today:       "Hoy",
                        clear:       "Limpiar",
                        format:      "mm/yyyy",
                        titleFormat: "MM yyyy",
                        weekStart:   0,
                    };
                    $("#txtFechaInicial").datepicker({
                        viewMode: "years",
                        minViewMode: "months",
                        autoclose: true
                    });
                    $("#txtFechaFinal, #txtDepInici").datepicker({
                        viewMode: "years",
                        minViewMode: "months",
                        autoclose: true
                    });

                    $("#txtFechaFinal").on("change blur click",function(event) {
                        var fechaI = $("#txtFechaInicial").val();
                        var fechaF = $("#txtFechaFinal").val();
                        if(fechaF.length > 0){
                            if(fechaF < fechaI){
                                $("#txtFechaFinal").parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                                $("#txtFechaFinal").val("");
                                $("#txtFechaFinal").parents(".col-sm-5").append("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");
                                $("#txtFechaFinal").parents(".col-md-5").append("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");
                                $("#txtFechaFinal").parents(".col-lg-5").append("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");
                            }else{
                                $("#txtFechaFinal").parents(".col-sm-5").addClass("has-success").removeClass('has-error');
                                $("#txtFechaFinal").remove("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");
                                $("#txtFechaFinal").remove("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");
                                $("#txtFechaFinal").remove("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");
                            }
                        }
                    });

                });

                $(".select2").select2();

                $().ready(function() {
                    var validator = $("#form").validate({
                        ignore: "",
                        rules:{
                            sltTipoPredio:"required",
                            txtCodigo:"required"
                        },
                        messages:{
                            sltTipoPredio: "Seleccione tipo de predio",
                        },
                        errorElement:"em",
                        errorPlacement: function(error, element){
                            error.addClass('help-block');
                        },
                        highlight: function(element, errorClass, validClass){
                            var elem = $(element);
                            if(elem.hasClass('select2-offscreen')){
                                $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                            }else{
                                $(elem).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                                $(elem).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                                $(elem).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                            }
                        },
                        unhighlight:function(element, errorClass, validClass){
                            var elem = $(element);
                            if(elem.hasClass('select2-offscreen')){
                                $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                            }else{
                                $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                                $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                                $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                            }
                        }
                    });
                    $(".cancel").click(function() {
                        validator.resetForm();
                    });

                    var validator = $("#form-guardar").validate({
                        ignore: "",
                        rules:{
                            sltTipoPredio:"required",
                            txtCodigo:"required"
                        },
                        messages:{
                            sltTipoPredio: "Seleccione tipo de predio",
                        },
                        errorElement:"em",
                        errorPlacement: function(error, element){
                            error.addClass('help-block');
                        },
                        highlight: function(element, errorClass, validClass){
                            var elem = $(element);
                            if(elem.hasClass('select2-offscreen')){
                                $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                            }else{
                                $(elem).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                                $(elem).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                                $(elem).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                            }
                        },
                        unhighlight:function(element, errorClass, validClass){
                            var elem = $(element);
                            if(elem.hasClass('select2-offscreen')){
                                $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                            }else{
                                $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                                $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                                $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                            }
                        }
                    });
                    $(".cancel").click(function() {
                        validator.resetForm();
                    });
                });

                $("#sltProductoFinal").change(function(e) {
                    var fecF = $("#txtFechaFinal").val();
                    var proI = $("#sltProductoInicial").val();
                    var proF = $(this).val();
                    $.ajax({
                        url: 'access.php?controller=dep&action=buscarDep',
                        type: 'POST',
                        dataType: "json",
                        data: {
                            txtFechaFinal:      fecF,
                            sltProductoInicial: proI,
                            sltProductoFinal:   proF
                        },
                        success:function(data, textStatus, jqXHR){
                            if(data.length > 0){
                                $("#modalListadoE").modal("show");
                                $("#modalMes").modal("hide");
                            }
                        }
                    });
                });

                $("#btn-procesar").click(function(e){
                    $("#form").attr('action', 'jsonAlmacen/depreciacionAlmacenProductoJson.php?action=rehacer');
                    $("#form").submit();
                });

                $("#sltProductoFinal").change(function(e) {
                    if($("#optDepIni2").is(':checked')){
                        var fecF = $("#txtFechaFinal").val();
                        var proI = $("#sltProductoInicial").val();
                        var proF = $(this).val();
                        $.ajax({
                            url: 'access.php?controller=dep&action=verificarMesAnterior',
                            type: 'POST',
                            dataType: "json",
                            data: {
                                txtFechaFinal:      fecF,
                                sltProductoInicial: proI,
                                sltProductoFinal:   proF
                            },
                            success:function(data, textStatus, jqXHR){
                                if(data.length < 0 || data == 0){
                                    $("#modalListadoE").modal("hide");
                                    $("#modalMes").modal("show");
                                }
                            }
                        });
                    }
                });

                $("#optDepIni1").click(function(e) {
                    if($("#optDepIni1").is(':checked')){
                        $.getJSON("access.php?controller=dep&action=buscarParametroInicio", function(data, textStatus, jqXHR){
                            if(data == 0){
                                $("#modalParamI").modal("show");
                            }
                        });
                    }
                });

                $("#btn-guardar").click(function(e){
                    var periodo = $("#txtDepInici").val();
                    if(periodo.length > 0){
                        $.getJSON('access.php?controller=dep&action=registrarParametroInicial', {txtDepInici: periodo}, function(data, textStatus, jqXHR) {
                            $("#modalParamI").modal("hide");
                        });
                    }
                });
            </script>
        </div>
    </div>
</body>
</html>