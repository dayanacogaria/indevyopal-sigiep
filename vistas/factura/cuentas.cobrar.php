<?php
require './Conexion/conexion.php';
require './head.php';
$terI = $this->factura->obtenerTerceroOrdenCompania($_SESSION['compania'], 'ASC');
$terF = $this->factura->obtenerTerceroOrdenCompania($_SESSION['compania'], 'DESC');
$tipI  = $this->factura->obtenerTiposClase($_REQUEST['clase'], 'ASC');
$tipF  = $this->factura->obtenerTiposClase($_REQUEST['clase'], 'DESC');
?>
    <title>Listado Cuentas por Cobrar</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/desing.css">
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top: 0;">Listado de Cuentas Por Cobrar</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" action="" class="form-horizontal" method="POST"  enctype="multipart/form-data" target="_blank">
                        <p align="center" class="parrafoO" style="margin-bottom:5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Cliente Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltClienteI" id="sltClienteI" class="select form-control" title="Seleccione Cliente Inicial" placeholder="Cliente Inicial">
                                    <?php
                                    $html = "<option value=''></option>";
                                    if(count($terI) > 0){
                                        foreach ($terI as $row){
                                            $html .= "<option value='$row[0]'>$row[1]</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Cliente Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltClienteF" id="sltClienteF" class="select form-control"  title="Seleccione Cliente Final" placeholder="Cliente Final">
                                    <?php
                                    $html = "<option value=''></option>";
                                    if(count($terF) > 0){
                                        foreach ($terF as $row){
                                            $html .= "<option value='$row[0]'>$row[1]</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Tipo Factura Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltTipoFI" id="sltTipoFI" class="select form-control" title="Seleccione Tipo Factura Inicial" placeholder=" Tipo Factura Inicial">
                                    <?php
                                    $html = "<option value=''></option>";
                                    if(count($tipI) > 0){
                                        foreach ($tipI as $row){
                                            $html .= "<option value='$row[0]'>$row[1]</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Tipo Factura Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltTipoFF" id="sltTipoFF" class="select form-control"  title="Seleccione Tipo Factura Final" placeholder="Tipo Factura Final">
                                    <?php
                                    $html = "<option value=''></option>";
                                    if(count($tipF) > 0){
                                        foreach ($tipF as $row){
                                            $html .= "<option value='$row[0]'>$row[1]</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="optTipoArchivo" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Tipo Archivo:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <label for="optPdf"   class="radio-inline"><input type="radio" name="optArchivo" id="optPdf">PDF</label>
                                <label for="optExcel" class="radio-inline"><input type="radio" name="optArchivo" id="optExcel">EXCEL</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-5 col-md-5 col-lg-5 control-label"></label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <button class="btn btn-primary borde-sombra"><span class="glyphicon glyphicon-send"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require 'footer.php'; ?>
        </div>
    </div>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_date.js"></script>
    <script src="js/script_validation.js"></script>
    <script src="js/script.js"></script>
    <script>
        $("#optPdf").click(function () {
            if($("#optPdf").is(':checked')){
                $("#form").attr("action", "access.php?controller=Factura&action=ListadoCuentaXCobrar");
            }
        });

        $("#optExcel").click(function () {
            if($("#optExcel").is(':checked')){
                $("#form").attr("action", "access.php?controller=Factura&action=ListadoCuentaXCobrarExcel");
            }
        });
    </script>
</body>
</html>