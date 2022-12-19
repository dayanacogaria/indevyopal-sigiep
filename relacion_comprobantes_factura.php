<?php
require ('head.php');
?>
    <title>Relación entre Comprobantes Factura </title>
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
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require('menu.php'); ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px;">Relación Comprobantes Facturación</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target="_blank">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="txtFechaInicial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtFechaInicial" id="txtFechaInicial" placeholder="Fecha Inicial" title="Ingrese Fecha Inicial" class="form-control" required="" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtFechaFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtFechaFinal" id="txtFechaFinal" placeholder="Fecha Final" title="Ingrese Fecha Final" class="form-control" required="" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="optTipoA" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo Archivo:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 text-left">
                                <label for="optPdf"   class="radio-inline"><input name="optTipoA" id="optPdf" type="radio" required="">Pdf</label>
                                <label for="optExcel" class="radio-inline"><input name="optTipoA" id="optExcel" type="radio" >Excel</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                                <label for="sltProductoFinal" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <button type="submit" id="btnPdf" class="btn btn-primary glyphicon glyphicon-print"></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require ('footer.php'); ?>
            <script src="js/jquery-ui.js"></script>
            <script type="text/javascript" src="js/select2.js"></script>
            <script src="dist/jquery.validate.js"></script>
            <script src="js/bootstrap-notify.js"></script>
            <script type="text/javascript" src="js/md5.js"></script>
            <script src="js/plugins/datepicker/bootstrap-datepicker.js"></script>
            <script>
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
                    $(".cancel").click(function() {
                        validator.resetForm();
                    });
                });

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
                        weekStart:   0,
                    };

                    $.datepicker.setDefaults($.datepicker.regional['es']);
                    $("#txtFechaInicial").datepicker({
                        format:      "dd/mm/yyyy"
                    });
                    $("#txtFechaFinal").datepicker({
                        format:      "dd/mm/yyyy"
                    });
                });

                $("#optPdf").click(function(){
                    $("#form").attr("action","informes/informe_relacion_entre_comprobantes.php");
                });

                $("#optExcel").click(function(){
                    $("#form").attr("action","informes/informe_relacion_entre_comprobantes_excel.php");
                });
            </script>
        </div>
    </div>
</body>
</html>