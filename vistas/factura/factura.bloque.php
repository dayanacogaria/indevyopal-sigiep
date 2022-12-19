<?php
require './Conexion/conexion.php';
require './head.php';
?>
<title>Generar Contabilidad por bloque</title>
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
</style>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require './menu.php'; ?>
        <div class="col-sm-10 col-md-10 col-lg-10 text-left">
            <h2 align="center" class="tituloform" style="margin-top: 0;">Generar Contabilidad Por Bloque</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" action="access.php?controller=Pago&action=GenerarContabilidadFactura" class="form-horizontal" method="POST"  enctype="multipart/form-data">
                    <p align="center" class="parrafoO" style="margin-bottom:5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                    <div class="form-group">
                        <label for="" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Inicial:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <input type="text" id="txtFechaI" name="txtFechaI" placeholder="Fecha Inicial" title="Ingrese fecha inicial" class="form-control fecha" required autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Final:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <input type="text" id="txtFechaF" name="txtFechaF" placeholder="Fecha Inicial" title="Ingrese fecha inicial" class="form-control fecha" required  autocomplete="off"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sltTipoXF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo Factura:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <select class="form-control select" id="sltTipoXF" name="sltTipoXF" title="Seleccione tipo factura" placeholder="Tipo Factura">
                                <?php
                                $xht = "<option value=''>Tipo Factura</option>";
                                $str = "SELECT gpt.id_unico, CONCAT_WS(' ', gpt.prefijo, UPPER(gpt.nombre)) FROM gp_tipo_factura AS gpt";
                                $res = $mysqli->query($str);
                                $dta = $res->fetch_all(MYSQLI_NUM);
                                foreach ($dta as $row){
                                    $xht .= "<option value='$row[0]'>$row[1]</option>";
                                }
                                echo $xht;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <button type="submit" class="btn btn-primary borde-sombra"><span class="glyphicon glyphicon-send"></span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php require './footer.php'; ?>
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
