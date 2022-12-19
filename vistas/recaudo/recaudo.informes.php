<?php
require './Conexion/conexion.php';
require './head.php';
$factI = $this->factura->ListadoFacturasClaseOrden($_REQUEST['clase'], 'ASC', $_SESSION['anno']);
$factF = $this->factura->ListadoFacturasClaseOrden($_REQUEST['clase'], 'DESC', $_SESSION['anno']);
?>
    <title>Listado de Recaudo</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/desing.css">
    <style>
        #form>.form-group{
            margin-bottom: 5px !important;
        }

        .client-form input[type="text"]{
            width: 100%;
        }

        .fechaX, .tipo, .factura{
            display: none;
        }
    </style>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require 'menu.php'; ?>
        <div class="col-sm-10 col-md-10 col-lg-10 text-left">
            <h2 align="center" class="tituloform" style="margin-top: 0;">Listado de Recaudos</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" action="" class="form-horizontal" method="POST"  enctype="multipart/form-data" target="_blank">
                    <p align="center" class="parrafoO" style="margin-bottom:5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                    <div class="form-group">
                        <label for="sltTipoX" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Tipo Informe</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <select name="sltTipoX" id="sltTipoX" required class="select form-control" title="Seleccione Tipo Informe">
                                <option value="">Tipo Informe</option>
                                <option value="general">General</option>
                                <option value="detallado">Detallado</option>
                                <option value="fechas">Entre Fechas</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group fechaX">
                        <label for="txtFechaI" type="date" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <input class="form-control fecha" type="text" name="txtFechaI" id="txtFechaI"  value="<?php echo date("d/m/Y");?>" required autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group fechaX">
                        <label for="txtFechaF" type="date" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <input class="form-control fecha" type="text" name="txtFechaF" id="txtFechaF"  value="<?php echo date("d/m/Y");?>" required autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group factura">
                        <label for="sltFacturaI" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Factura Inicial:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <select name="sltFacturaI" id="sltFacturaI" class="select form-control" title="Seleccione Factura Inicial" required>
                                <?php
                                $html = "<option value=''>Factura Inicial</option>";
                                foreach ($factI as $row){
                                    $html .= "<option value='$row[0]'>$row[1] $row[2] $row[3] $row[4]</option>";
                                }
                                echo $html;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group factura">
                        <label for="sltFacturaF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Factura Final:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <select name="sltFacturaF" class="select form-control" id="sltFacturaF" title="Seleccione Factura Final"  required>
                                <?php
                                $html = "<option value=''>Factura Final</option>";
                                foreach ($factF as $row){
                                    $html .= "<option value='$row[0]'>$row[1] $row[2] $row[3] $row[4]</option>";
                                }
                                echo $html;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group tipo">
                        <label for="sltTipoI" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Tipo Factura Inicial:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <select name="sltTipoI" id="sltTipoI" class="select form-control" title="Seleccione Tipo Factura Inicial" required>
                                <?php
                                $html = "<option value=''>Tipo Factura Inicial</option>";
                                foreach ($tipoI as $row){
                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                }
                                echo $html;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group tipo">
                        <label for="sltTcf" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo Factura Final:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <select name="sltTipoF" class="select form-control" id="sltTipoF" title="Seleccione Tipo Factura Final"  required>
                                <?php
                                $html = "<option value=''>Tipo Factura Final</option>";
                                foreach ($tipoF as $row){
                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                }
                                echo $html;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="optTipo" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo de Archivo:</label>
                        <div class="col-sm-5 col-md-5-col-lg-5">
                            <label for="optPdf" class="radio-inline"><input type="radio" name="optArchivo" id="optPdf" required>PDF</label>
                            <label for="optExl" class="radio-inline"><input type="radio" name="optArchivo" id="optExl">EXCEL</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="btnGenerar" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                        <div class="col-sm-5 col-md-5-col-lg-5">
                            <button type="submit" class="btn btn-primary borde-sombra" id="btnGenerar"><span class="glyphicon glyphicon-play"></span></button>
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
</body>
</html>