<?php
require './Conexion/conexion.php';
require './head.php';
$concpI = $this->factura->obtenerListadoConceptos('ASC', $_SESSION['anno']);
$concpF = $this->factura->obtenerListadoConceptos('DESC', $_SESSION['anno']);
$tersI  = $this->factura->obtenerListadoTerceros('ASC', $_SESSION['compania']);
$tersF  = $this->factura->obtenerListadoTerceros('DESC', $_SESSION['compania']);
?>
    <title>Listado de Facturación</title>
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

        .fechaX, .tipo, .concepto, .tercero{
            display: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top: 0;">Listado de Facturación</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" action="" class="form-horizontal" method="POST"  enctype="multipart/form-data" target="_blank">
                        <p align="center" class="parrafoO" style="margin-bottom:5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="sltTipo" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Tipo Informe</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltTipo" id="sltTipo" required class="select form-control" title="Seleccione Tipo Informe">
                                    <option value="">Tipo Informe</option>
                                    <option value="general">General</option>
                                    <option value="detallado">Detallado</option>
                                    <option value="concepto">Concepto</option>
                                    <option value="tercero">Tercero</option>
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
                        <div class="form-group concepto">
                            <label for="sltConceptoI" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Concepto Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltConceptoI" id="sltConceptoI" class="select form-control" title="Seleccione Concepto Inicial" required>
                                    <?php
                                    $html = "<option value=''>Concepto Inicial</option>";
                                    foreach ($concpI as $row){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group concepto">
                            <label for="sltConceptoI" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Concepto Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltConceptoF" id="sltConceptoF" class="select form-control" title="Seleccione Concepto Final" required>
                                    <?php
                                    $html = "<option value=''>Concepto Final</option>";
                                    foreach ($concpF as $row){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group tercero">
                            <label for="sltTerceroI" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tercero Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltTerceroI" class="select form-control" id="sltTerceroI" title="Seleccione Tercero Inicial"  required >
                                    <?php
                                    $html = "<option value=''>Tercero Inicial</option>";
                                    foreach ($tersI as $row){
                                        $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group tercero">
                            <label for="sltTerceroF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tercero Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltTerceroF" class="select form-control" id="sltTerceroF" title="Seleccione Tercero Final"  required>
                                    <?php
                                    $html = "<option value=''>Tercero Final</option>";
                                    foreach ($tersF as $row){
                                        $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
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
