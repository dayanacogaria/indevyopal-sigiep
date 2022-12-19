<?php
require_once('Conexion/conexion.php');
require_once 'head.php';
$compania = $_SESSION['compania'];
?>
<title>Listado de Facturación</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style>
    label #sltTci-error, #sltTcf-error, #fechaini-error, #fechafin-error, #scid-error, #scfd-error, #scidic-error,
    #scfdic-error, #sti-error, #stf-error  {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
    }

    #conceptoInicialDetalle,#conceptoFinalDetalle,#conceptoInicialFactura,#conceptoFinalFactura,
    #terceroInicial,#terceroFinal,#conceptoInicialDetalleInfConcepto,#conceptoFinalDetalleInfConcepto,
    #divFechaInicial,#divFechaFinal{
        display: none;
    }

    #form .form-control {font-size: 10px;}

    #form input[type='text']{
        width: 100% !important;
    }

    #form .form-group{
        margin-bottom: 10px !important;
    }

    .form-group>div>button{
        box-shadow: 0px 2px 5px 1px grey;
    }
</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top: 0;">Listado de Facturación</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target="_blank">
                        <br/>
                        <div class="form-group">
                            <label for="sltctai" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Informe</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="tipoInforme" onchange="hcti()" id="selectTipoInforme" required class="select2_single form-control" title="Seleccione Tipo Informe">
                                    <option value="">Tipo Informe</option>
                                    <option value="general">General</option>
                                    <option value="detallado">Detallado</option>
                                    <option value="concepto">Concepto</option>
                                    <option value="tercero">Tercero</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="divFechaInicial">
                            <label for="fechaini" type = "date" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input class="form-control" type="text" name="fechaInicial" id="fechaini"  value="<?php echo date("Y-m-d"); ?>" required autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group" id="divFechaFinal" style="margin-top: -10px;">
                            <label for="fechafin" type = "date" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input class="form-control" type="text" name="fechaFinal" id="fechafin"  value="<?php echo date("Y-m-d"); ?>" required autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group" id="conceptoInicialFactura" style="margin-top: -10px;">
                            <?php
                            $sql = "SELECT id_unico,nombre FROM  gp_tipo_factura WHERE compania =$compania ORDER BY id_unico ASC";
                            $rsTcf = $mysqli->query($sql);
                            ?>
                            <label for="sltTci" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Factura Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select  name="conceptoInicialFactura" id="sltTci" class="select2_single form-control" title="Seleccione Tipo Factura Inicial " required>
                                    <option value="" >Tipo Factura Inicial</option>
                                    <?php while ($f = mysqli_fetch_row($rsTcf)) { ?>
                                        <option value="<?php echo $f[0]; ?>"><?php echo ucwords(mb_strtolower($f[1])); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="conceptoFinalFactura">
                            <?php
                            $tcf = "SELECT id_unico,nombre FROM  gp_tipo_factura WHERE compania =$compania ORDER BY id_unico DESC";
                            $rsTcf = $mysqli->query($tcf);
                            ?>
                            <label for="sltTcf" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo Factura Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="conceptoFinalFactura" class="select2_single form-control" id="sltTcf" title="Seleccione Tipo Factura Inicial"  required>
                                    <option value="">Tipo Factura Inicial</option>
                                    <?php while ($filaTcf = mysqli_fetch_row($rsTcf)) { ?>
                                        <option value="<?php echo $filaTcf[0]; ?>"><?php echo ucwords(mb_strtolower($filaTcf[1])); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="conceptoInicialDetalle" style="margin-top: -10px;">
                            <?php
                            $sqlr = "SELECT DISTINCT c.id_unico, c.nombre 
                                FROM  gp_detalle_factura df 
                                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                                ORDER BY c.id_unico  ASC";
                            $rsTcf = $mysqli->query($sqlr);
                            ?>
                            <label for="scid" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto Inicial Detalle:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select  name="conceptoInicialDetalle" id="scid" class="select2_single form-control" title="Seleccione Concepto Inicial Detalle" required>
                                    <option value="">Concepto Inicial Detalle</option>
                                    <?php while ($f = mysqli_fetch_row($rsTcf)) { ?>
                                        <option value="<?php echo $f[0]; ?>"><?php echo ucwords(mb_strtolower($f[1])); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="conceptoFinalDetalle">
                            <?php
                            $tcf = "SELECT DISTINCT c.id_unico, c.nombre 
                                FROM  gp_detalle_factura df 
                                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                                ORDER BY c.id_unico  DESC";
                            $rsTcf = $mysqli->query($tcf);
                            ?>
                            <label for="scfd" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Concepto Final Detalle:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="conceptoFinalDetalle" class="select2_single form-control" id="scfd" title="Seleccione Concepto Final Detalle" required  >
                                    <option value="">Concepto Final Detalle</option>
                                    <?php while ($filaTcf = mysqli_fetch_row($rsTcf)) { ?>
                                        <option value="<?php echo $filaTcf[0]; ?>"><?php echo ucwords(mb_strtolower($filaTcf[1])); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="conceptoInicialDetalleInfConcepto" style="margin-top: -10px;">
                        <?php
                        $sql = "SELECT DISTINCT c.id_unico, c.nombre 
                                FROM  gp_detalle_factura df 
                                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                                ORDER BY c.id_unico  ASC";
                            $rsTcf = $mysqli->query($sql);
                            ?>
                            <label for="scidic" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto Inicial Detalle:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select  name="conceptoInicial" id="scidic" class="select2_single form-control" title="Seleccione Concepto Inicial Detalle" required>
                                    <option value="">Concepto Inicial Detalle</option>
<?php while ($f = mysqli_fetch_row($rsTcf)) { ?>
                                        <option value="<?php echo $f[0]; ?>"><?php echo ucwords(mb_strtolower($f[1])); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="conceptoFinalDetalleInfConcepto" >
                        <?php
                        $tcf = "SELECT DISTINCT c.id_unico, c.nombre 
                                FROM  gp_detalle_factura df 
                                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                                ORDER BY c.id_unico  DESC";
                        $rsTcf = $mysqli->query($tcf);
                        ?>
                            <label for="scfdic" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Concepto Final Detalle:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="conceptoFinal" class="select2_single form-control" id="scfdic" title="Seleccione Concepto Final Detalle" required  >
                                    <option value="">Concepto Final Detalle</option>
                                <?php while ($filaTcf = mysqli_fetch_row($rsTcf)) { ?>
                                        <option value="<?php echo $filaTcf[0]; ?>"><?php echo ucwords(mb_strtolower($filaTcf[1])); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="terceroInicial" style="margin-top: -10px;">
                            <label for="sti" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tercero Inicial:</label>
<?php
$sqlt = "SELECT ter.id_unico
                                      ,IF( CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                          (ter.razonsocial),
                                          CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                      ) AS NOMBRE,
                                      ter.numeroidentificacion
                                FROM  gf_tercero ter
                                ORDER BY id_unico ASC";
$listaTerceros = $mysqli->query($sqlt);
?>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="terceroInicial" class="select2_single form-control" id="sti" title="Seleccione Tercero Inicial"  required >
                                    <option value="">Tercero Inicial</option>
<?php while ($filaT = mysqli_fetch_array($listaTerceros)) { ?>
                                        <option value="<?php echo $filaT['id_unico']; ?>"><?php echo $filaT['numeroidentificacion'] . " - " . ucwords(mb_strtolower($filaT['NOMBRE'])); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div id="terceroFinal" class="form-group">
                            <label for="stf" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tercero Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
<?php
$sqlt = "SELECT  ter.id_unico,
                                            IF(   CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                  (ter.razonsocial),
                                                  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                            ) AS NOMBRE,
                                            ter.numeroidentificacion
                                    FROM    gf_tercero ter
                                    ORDER BY id_unico DESC";
$listaTerceros = $mysqli->query($sqlt);
?>
                                <select name="terceroFinal" class="select2_single form-control" id="stf" title="Seleccione Tercero Final" required  >
                                    <option value="">Tercero Final</option>
                                <?php while ($filaT = mysqli_fetch_array($listaTerceros)) { ?>
                                        <option value="<?php echo $filaT['id_unico']; ?>"><?php echo $filaT['numeroidentificacion'] . " - " . ucwords(mb_strtolower($filaT['NOMBRE'])); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                            <div class="col-sm-2 col-md-2 col-lg-2 text-left">
                                <button id="btnpdf" onclick="reportePdf()" class="btn btn-primary" title="Generar reporte PDF" disabled><i class="fa fa-file-pdf-o"></i></button>
                                <button id="btnexcel" onclick="reporteExcel()" class="btn  btn-primary" title="Generar reporte Excel" disabled><i class="fa fa-file-excel-o"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="js/select2.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <script src="js/jquery-ui.js"></script>
        <script>
                                    $().ready(function () {
                                        var validator = $("#form").validate({
                                            ignore: "",

                                            errorPlacement: function (error, element) {

                                                $(element)
                                                        .closest("form")
                                                        .find("label[for='" + element.attr("id") + "']")
                                                        .append(error);
                                            },
                                            rules: {
                                                sltmes: {
                                                    required: true
                                                },
                                                sltcni: {
                                                    required: true
                                                },
                                                sltAnnio: {
                                                    required: true
                                                }
                                            }
                                        });
                                    });

                                    $(function () {
                                        var fecha = new Date();
                                        var dia = fecha.getDate();
                                        var mes = fecha.getMonth() + 1;
                                        if (dia < 10) {
                                            dia = "0" + dia;
                                        }
                                        if (mes < 10) {
                                            mes = "0" + mes;
                                        }
                                        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
                                        $.datepicker.regional['es'] = {
                                            closeText: 'Cerrar',
                                            prevText: 'Anterior',
                                            nextText: 'Siguiente',
                                            currentText: 'Hoy',
                                            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                                            monthNamesShort: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                                            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                                            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
                                            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                                            weekHeader: 'Sm',
                                            dateFormat: 'dd/mm/yy',
                                            firstDay: 1,
                                            isRTL: false,
                                            showMonthAfterYear: false,
                                            yearSuffix: '',
                                            changeYear: true
                                        };
                                        $.datepicker.setDefaults($.datepicker.regional['es']);
                                        $("#fechaini").datepicker({changeMonth: true, }).val(fecAct);
                                        $("#fechafin").datepicker({changeMonth: true}).val(fecAct);
                                    });

                                    function hcti() {
                                        const ti = $("#selectTipoInforme").val();
                                        $('#sltTci, #sltTcf, #scid, #scfd, #sti, #stf, #scidic, #scfdic').removeAttr("required");
                                        switch (ti) {
                                            case "general":
                                                $("#conceptoInicialFactura, #conceptoFinalFactura, #divFechaInicial, #divFechaFinal").css('display', 'block');
                                                $("#conceptoInicialDetalle, #conceptoFinalDetalle, #conceptoInicialDetalleInfConcepto, #conceptoFinalDetalleInfConcepto, #terceroInicial, #terceroFinal").css('display', 'none');
                                                $("#btnpdf,#btnexcel").prop('disabled', false);
                                                $('#sltTci, #sltTcf').prop("required", true);
                                                break;
                                            case "detallado":
                                                $("#conceptoInicialDetalle, #conceptoFinalDetalle, #divFechaInicial, #divFechaFinal").css('display', 'block');
                                                $("#conceptoInicialFactura, #conceptoFinalFactura, #conceptoInicialDetalleInfConcepto, #conceptoFinalDetalleInfConcepto, #terceroInicial, #terceroFinal").css("display", 'none');
                                                $("#btnpdf,#btnexcel").prop('disabled', false);
                                                $('#scid, #scfd').prop("required", true);
                                                break;
                                            case "concepto":
                                                $("#conceptoInicialDetalleInfConcepto, #conceptoFinalDetalleInfConcepto, #divFechaInicial, #divFechaFinal").css('display', 'block');
                                                $("#conceptoInicialDetalle, #conceptoFinalDetalle, #conceptoInicialFactura, #conceptoFinalFactura, #terceroInicial, #terceroFinal").css('display', 'none');
                                                $("#btnpdf,#btnexcel").attr('disabled', false);
                                                $('#scidic, #scfdic').prop("required", true);
                                                break;
                                            case "tercero":
                                                $("#terceroInicial, #terceroFinal, #divFechaInicial, #divFechaFinal").css('display', 'block');
                                                $('#conceptoInicialFactura, #conceptoFinalFactura, #conceptoInicialDetalleInfConcepto, #conceptoFinalDetalleInfConcepto, #conceptoInicialDetalle, #conceptoFinalDetalle').css('display', 'none');
                                                $("#btnpdf,#btnexcel").attr('disabled', false);
                                                $('#sti, #stf').prop("required", true);
                                                break;
                                            default:
                                                $("#divFechaInicial, #divFechaFinal, #terceroInicial, #terceroFinal, #conceptoInicialFactura, #conceptoFinalFactura, #conceptoInicialDetalle, #conceptoFinalDetalle, #conceptoInicialDetalleInfConcepto, #conceptoFinalDetalleInfConcepto").css('display', 'none');
                                                $("#btnpdf,#btnexcel").prop('disabled', true);
                                                break;
                                        }
                                    }

                                    $(".select2_single").select2({});

                                    function reporteExcel() {
                                        $('form').attr('action', 'informes/generar_INF_LIS_FAC_EXCEL.php');
                                    }

                                    function reportePdf() {
                                        $('form').attr('action', 'informes/generar_INF_LIS_FAC.php');
                                    }
        </script>
    </div>
<?php require_once 'footer.php' ?>
</body>
</html>