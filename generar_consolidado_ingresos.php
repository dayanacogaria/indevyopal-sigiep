<?php
require_once('head.php');
require_once('./Conexion/conexion.php');
?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Informe Consolidado de Ingresos</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <style type="text/css" media="screen">
        .client-form input[type="text"]{
            width: 100%;
        }

        .shadow{
            box-shadow: 1px 1px 1px 1px grey;
        }
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:2px">Informe Consolidado de Ingresos</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top: -15px" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" target="_blank">
                        <p align="center" class="parrafoO" style="margin-bottom: 5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="sltTipoComprobanteInicial" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Tipo Comprobante Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltTipoComprobanteInicial" id="sltTipoComprobanteInicial" class="select2 form-control" required="">
                                    <?php
                                    $html = "";
                                    $html .= "<option value=''>Tipo Comprobante Inicial</option>";
                                    $sql  = "SELECT DISTINCT tpc.id_unico,tpc.nombre,tpc.sigla FROM  gf_tipo_comprobante tpc WHERE comprobante_pptal IS NOT NULL ORDER BY tpc.nombre ASC";
                                    $result = $mysqli->query($sql);
                                    while ($row = mysqli_fetch_row($result)) {
                                        $html.= "<option value=".$row[0].">".ucfirst(strtolower($row[1])).PHP_EOL.$row[2]."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltTipoComprobanteFinal" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Tipo Comprobante Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltTipoComprobanteFinal" id="sltTipoComprobanteFinal" class="select2 form-control" required="">
                                    <?php
                                    $html  = "";
                                    $html .= "<option value=''>Tipo Comprobante Final</option>";
                                    $sql = "SELECT DISTINCT tpc.id_unico,tpc.nombre,tpc.sigla FROM  gf_tipo_comprobante tpc WHERE comprobante_pptal IS NOT NULL ORDER BY tpc.nombre ASC";
                                    $result = $mysqli->query($sql);
                                    while ($row = mysqli_fetch_row($result)) {
                                        $html.= "<option value=".$row[0].">".ucfirst(strtolower($row[1])).PHP_EOL.$row[2]."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtFechaInicial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtFechaInicial" id="txtFechaInicial" title="Seleccione fecha inicial" placeholder="Fecha Inicial" title="Fecha Inicial" class="form-control" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtFechaFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtFechaFinal" id="txtFechaFinal" title="Seleccione fecha final" placeholder="Fecha Final" title="Fecha Final" class="form-control" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="optTipo" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo de Informe:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <label for="" class="radio-inline"><input type="radio" name="optTipo" id="optPdf" required="">Pdf</label>
                                <label for="" class="radio-inline"><input type="radio" name="optTipo" id="optExcel">Excel</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="optTipo" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <button type="submit" class="btn btn-primary shadow glyphicon glyphicon-print"></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require_once ('footer.php'); ?>
        </div>
    </div>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script>
        //Tipo comprobante inicial
        $(".select2").select2({
            placeholder:"Tipo Comprobante Inicial",
            allowClear: true
        });

        //Script para campos de fecha
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
                monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $("#txtFechaInicial").datepicker({changeMonth: true}).val(fecAct);
            $("#txtFechaFinal").datepicker({changeMonth: true}).val(fecAct);

            var validator = $("#form").validate({
                ignore: "",
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
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
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
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
        });

        $("#optPdf").click(function(){
            $("#form").attr("action","informes/informe_consolidad_ingresos.php");
        });

        $("#optExcel").click(function(){
            $("#form").attr("action","informes/informe_consolidad_ingresos_excel.php");
        });
        </script>
    </body>
</html>