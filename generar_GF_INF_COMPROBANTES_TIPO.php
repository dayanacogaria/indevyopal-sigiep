<?php
##########################################################################################################################################
# Fecha de Creación : 01/02/2017
# Creado por : Jhon Numpaque
#
##########################################################################################################################################
# Modificaciones
##########################################################################################################################################
# Modificado por : Jhon Numpaque
# Fecha de modificación : 06/03/2017
# Descripción : Se incluyo la impresión a excel
#
##########################################################################################################################################
# Librerias requeridad
require_once ('head.php');
require_once ('Conexion/conexion.php');
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
#
?>		<!-- Titulo -->
<title>Listado de comprobantes por tipo</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
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
        });

        $(".cancel").click(function () {
            validator.resetForm();
        });
    });
</script>
<style>
    label #sltTipoComprobanteInicial-error, #sltTipoComprobanteFinal-error, #txtFechaInicial-error, #txtFechaFinal-error  {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;

    }
</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:2px">Listado de Comprobantes por Tipo</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top: -15px" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" target="_blank">
                        <p align="center" style="margin-bottom: 15px; margin-top:5px; margin-left: 30px; font-size: 80%"></p>
                        <div class="form-group">
                            <label for="sltTipoComprobanteInicial" class="col-sm-5 control-label">
                                <strong class="obligado">*</strong>Tipo Comprobante Inicial:
                            </label>
                            <select name="sltTipoComprobanteInicial" id="sltTipoComprobanteInicial" title="Seleccione Tipo Comprobante Inicial" style="width: 300px;" class="col-sm-1 form-control" required="">
                                <?php
                                echo "<option value=''>Tipo Comprobante Inicial</option>";
                                switch ($_GET['tipo']) {
                                    case 'pptal':
                                        $sql = "SELECT DISTINCT tpc.id_unico,tpc.nombre,tpc.codigo
                                            FROM    gf_tipo_comprobante_pptal tpc 
                                            WHERE tpc.compania = $compania ";
                                        $result = $mysqli->query($sql);
                                        while ($row = mysqli_fetch_row($result)) {
                                            echo "<option value=" . $row[0] . ">" . ucfirst(mb_strtolower($row[1])) . PHP_EOL . $row[2] . "</option>";
                                        }
                                        break;

                                    case 'cnt':
                                        $sql = "SELECT DISTINCT tpc.id_unico,tpc.nombre,tpc.sigla
                                            FROM gf_tipo_comprobante tpc 
                                            WHERE tpc.compania = $compania ";
                                        $result = $mysqli->query($sql);
                                        while ($row = mysqli_fetch_row($result)) {
                                            echo "<option value=" . $row[0] . ">" . ucfirst(mb_strtolower($row[1])) . PHP_EOL . $row[2] . "</option>";
                                        }
                                        break;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sltTipoComprobanteFinal" class="col-sm-5 control-label">
                                <strong class="obligado">*</strong>Tipo Comprobante Final:
                            </label>
                            <select name="sltTipoComprobanteFinal" id="sltTipoComprobanteFinal" title="Seleccione Tipo Comprobante Final" style="width: 300px;" class="col-sm-1 form-control" required="">
                                <?php
                                echo "<option value=''>Tipo Comprobante Final</option>";
                                switch ($_GET['tipo']) {
                                    case 'pptal':
                                         $sql = "SELECT DISTINCT tpc.id_unico,tpc.nombre,tpc.codigo
                                            FROM    gf_tipo_comprobante_pptal tpc 
                                            WHERE tpc.compania = $compania ";
                                        $result = $mysqli->query($sql);
                                        while ($row = mysqli_fetch_row($result)) {
                                            echo "<option value=" . $row[0] . ">" . ucfirst(mb_strtolower($row[1])) . PHP_EOL . $row[2] . "</option>";
                                        }
                                        break;

                                    case 'cnt':
                                        $sql = "SELECT DISTINCT tpc.id_unico,tpc.nombre,tpc.sigla
                                            FROM gf_tipo_comprobante tpc 
                                            WHERE tpc.compania = $compania ";
                                        $result = $mysqli->query($sql);
                                        while ($row = mysqli_fetch_row($result)) {
                                            echo "<option value=" . $row[0] . ">" . ucfirst(mb_strtolower($row[1])) . PHP_EOL . $row[2] . "</option>";
                                        }
                                        break;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="txtFechaInicial" class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Fecha Inicial:
                            </label>
                            <input type="text" name="txtFechaInicial" id="txtFechaInicial" title="Seleccione fecha inicial" placeholder="Fecha Inicial" title="Fecha Inicial" class="form-control" required="">
                        </div>
                        <div class="form-group">
                            <label for="txtFechaFinal" class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Fecha Final:
                            </label>
                            <input type="text" name="txtFechaFinal" id="txtFechaFinal" title="Seleccione fecha final" placeholder="Fecha Final" title="Fecha Final" class="form-control" required="">
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 col-sm-offset-3"></label>
                            <button onclick="return abrirPdf()" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>
                    </form>
                </div>
                <!-- Scripts -->
                <script type="text/javascript" src="js/select2.js"></script>
                <script>
                                //Tipo comprobante inicial
                                $("#sltTipoComprobanteInicial").select2({
                                    placeholder: "Tipo Comprobante Inicial",
                                    allowClear: true
                                });
                                //Tipo comprobante final
                                $("#sltTipoComprobanteFinal").select2({
                                    placeholder: "Tipo Comprobante Inicial",
                                    allowClear: true
                                });
                                //Script para campos de fecha
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
                                        yearSuffix: ''
                                    };
                                    $.datepicker.setDefaults($.datepicker.regional['es']);
                                    $("#txtFechaInicial").datepicker({changeMonth: true}).val(fecAct);
                                    $("#txtFechaFinal").datepicker({changeMonth: true}).val(fecAct);
                                });
                                //Función para abrir informe
                                function abrirPdf() {
                                    $('#form').attr('action', 'informes/inf_com_tipo.php?tipo=<?php echo $_GET['tipo'] ?>');
                                }
                                //Función para abrir excel
                                function reporteExcel() {
                                    $('form').attr('action', 'informes/inf_com_tipo_excel.php?tipo=<?php echo $_GET['tipo'] ?>');
                                }
                </script>
                <!-- Estilos -->		
                <style>
                    body{
                        font-size: 12px
                    }
                </style>
            </div>
        </div>
        <div>
            <?php require_once ('footer.php'); ?>
        </div>
    </div>
</body>
</html>