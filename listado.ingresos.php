<?php
require './Conexion/conexion.php';
require './head.php';
?>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/jquery.datetimepicker.css">
<link rel="stylesheet" href="css/desing.css">
<title>Informe</title>
<style>
    .FECHA{
        display: none;
    }

    #form>.form-group{
        margin-bottom: 10px !important;
    }
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 class="tituloform" align="center" style="margin-top: 0px;">Listado de Ingresos</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12 col-md-12 col-lg-12">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:generar()" target="_blank">
                    <br/>
                    <div class="form-group">
                        <div class="form-group">
                            <label for="fechaI" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha I:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtFechaI" id="txtFechaI" class="form-control entre" placeholder="Fecha Inicial" required="" title="Fecha Inicial" style="width: 100%;" value="<?php echo date("d/m/Y H:i") ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fechaF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha F:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtFechaF" id="txtFechaF" class="form-control entre" placeholder="Fecha Final" required="" title="Fecha Final" style="width: 100%;" value="<?php echo date("d/m/Y H:i") ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="optTipoPDF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Salida:</label>
                            <div class="col-sm-1 col-md-1 col-lg-1" style="width: 150px;">
                                <label class="radio-inline"><input type="radio" name="optArchivo" id="optArchivo1" title="Seleccione Opción" checked>Si</label>
                                <label class="radio-inline"><input type="radio" name="optArchivo" id="optArchivo2">No</label>                                
                                <label class="radio-inline" style="padding-left: 1px;"><a href="javascript:void(0)" title="Deseleccionar" id="btnunchecked" ><li class="glyphicon glyphicon-record"></li></a></label>
                            </div>                                         
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <button type="submit" class="btn btn-primary borde-sombra" ><span class="glyphicon glyphicon-download"></span></button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
            <?php require_once 'footer.php'; ?>
        </div>
    </div>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="js/script_date.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/script.js"></script>
    <script type="text/javascript">
        function generar() {
            var fechaI = $("#txtFechaI").val();
            var fechaF = $("#txtFechaF").val();
            var salida = 2;
            if ($("#optArchivo1").is(':checked')) {
                salida = 1;
            } else if ($("#optArchivo2").is(':checked')) {
                salida = 0;
            }            
            window.open('informes/informe_ingresos_parqueadero.php?tipo=1&fechaI='+fechaI+'&fechaF='+fechaF+'&salida='+salida);
        }
        $("#btnunchecked").click(function (evt){
            evt.preventDefault();
            $("#optArchivo1").prop("checked", false);
            $("#optArchivo2").prop("checked", false);
        });
        
    </script>
</body>
</html>