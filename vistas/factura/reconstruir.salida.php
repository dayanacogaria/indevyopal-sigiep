<?php
require './Conexion/conexion.php';
require './head.php';
?>
    <title>Reconstruir data salida</title>
    <link rel="stylesheet" href="./css/select2.css">
    <link rel="stylesheet" href="./css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="./css/jquery-ui.css">
    <link rel="stylesheet" href="./css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="./css/desing.css">
    <style type="text/css" media="screen">
        .client-form input[type="text"]{
            width: 100%;
        }

        .client-form select{
            width: 100%;
        }

        .btn{
            box-shadow: 0 2px 5px 1px grey;
        }

        .client-form input[type="file"]{
            width: 100%
        }

        #Carga{
            background-color: #FFF !important;
            position:fixed;
            top:0px;
            left:0px;
            z-index:3200;
            filter:alpha(opacity=80);
            -moz-opacity:80;
            opacity:0.80;
        }

        .client-form>#form>.form-group{
            margin-bottom: 5px !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require('menu.php'); ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px;">Reconstruir data salida</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="access.php?controller=Almacen&action=procesarConstruccionSalida">
                        <p align="center" style="margin-bottom: 10px; margin-top: 10px; margin-left: 30px; font-size: 80%;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="txtFechaI" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input class="form-control fecha" type="text" name="txtFechaI" id="txtFechaI"  value="<?php echo date("d/m/Y");?>" required autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtFechaF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input class="form-control fecha" type="text" name="txtFechaF" id="txtFechaF"  value="<?php echo date("d/m/Y");?>" required autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltTipoFactura" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo Tarifa:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select class="form-control select2" id="sltTipoFactura" name="sltTipoFactura" placeholder="Tipo Factura">
                                    <?php
                                    $html = "<option value=''></option>";
                                    if(count($tipoFat) > 0){
                                        foreach ($tipoFat as $row){
                                            $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltTipoFactura" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <button class="btn btn-primary"><span class="glyphicon glyphicon-send"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require('footer.php'); ?>
        </div>
    </div>
    <script src="./js/jquery-ui.js"></script>
    <script src="./js/php-date-formatter.min.js"></script>
    <script src="./js/jquery.datetimepicker.js"></script>
    <script type="text/javascript" src="./js/select2.js"></script>
    <script src="./dist/jquery.validate.js"></script>
    <script src="./js/script_date.js"></script>
    <script src="./js/script_validation.js"></script>
    <script src="./js/script.js"></script>
    <script>
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
                        $(elem).parents(".col-lg-6").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-6").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-6").addClass("has-error").removeClass('has-success');
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    $(element).parents(".col-lg-6").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-md-6").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-sm-6").addClass('has-success').removeClass('has-error');
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });
    </script>
</body>
</html>
