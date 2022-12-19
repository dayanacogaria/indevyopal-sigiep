<?php 
#################################################################################################
#  ********************************** Modificaciones **********************************         #
#################################################################################################
#01/03/2018 |Erica G. | ARCHIVO CREADO
#################################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$con  = new ConexionPDO();
#* Tipo Comprobante Inicial
$tci = $con->Listar("SELECT id_unico, UPPER(sigla), LOWER(nombre) 
        FROM gf_tipo_comprobante WHERE compania = $compania ORDER BY id_unico ASC");
#* Tipo Comprobante Final
$tcf = $con->Listar("SELECT id_unico, UPPER(sigla), LOWER(nombre) 
        FROM gf_tipo_comprobante WHERE compania = $compania ORDER BY id_unico DESC");
#* Cuenta
$ci  = $con->Listar("SELECT id_unico, codi_cuenta, LOWER(nombre) 
        FROM gf_cuenta 
        WHERE parametrizacionanno =$anno 
        AND (clasecuenta = 12 OR clasecuenta = 11) AND (movimiento = 1 OR auxiliartercero = 1) 
        ORDER BY codi_cuenta ASC ");

?>
<html>
    <head>
        <title>Boletín Diario De Caja</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            label #tipoI-error, #tipoF-error, #fechaI-error, #fechaF-error, #cuentaI-error, #cuentaF-error  {
                display: block;
                color: #bd081c;
                font-weight: bold;
                font-style: italic;
            }
        </style>
        <script>
            $().ready(function() {
                var validator = $("#form").validate({
                      ignore: "",
                  errorPlacement: function(error, element) {
                    $( element )
                      .closest( "form" )
                        .find( "label[for='" + element.attr( "id" ) + "']" )
                          .append( error );
                  },
                });
                $(".cancel").click(function() {
                    validator.resetForm();
                });
            });
        </script>
        <script>
            $(function(){
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
                    yearSuffix: '',
                    changeYear: true
                };
                $.datepicker.setDefaults($.datepicker.regional['es']);
                $("#fechaI").datepicker({changeMonth: true,}).val();
                $("#fechaF").datepicker({changeMonth: true}).val();
            });
        </script>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left" style="margin-left: 0px;margin-top: -20px"> 
                    <h2 align="center" class="tituloform">Boletín Diario De Caja</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                            <div class="form-group">
                                <div class="form-group" style="margin-top: -5px">
                                    <label for="tipoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Inicial:</label>
                                    <select  name="tipoI" id="tipoI" class="select2_single form-control" title="Seleccione Tipo De Comprobante Inicial" style="height: 30px" required>
                                            <option value="">Tipo De Comprobante Inicial</option>                              
                                            <?php  for ($i = 0; $i < count($tci); $i++) {
                                                echo '<option value="'.$tci[$i][0].'">'.$tci[$i][1].' - '.ucwords($tci[$i][2]).'</option>';
                                            } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: -5px">
                                    <label for="tipoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Final:</label>
                                    <select  name="tipoF" id="tipoF" class="select2_single form-control" title="Seleccione Tipo De Comprobante Final" style="height: 30px" required>
                                            <option value="">Tipo De Comprobante Final</option>                              
                                            <?php  for ($i = 0; $i < count($tcf); $i++) {
                                                echo '<option value="'.$tcf[$i][0].'">'.$tcf[$i][1].' - '.ucwords($tcf[$i][2]).'</option>';
                                            } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: -5px;">
                                     <label for="fechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                                     <input class="form-control" type="text" name="fechaI" id="fechaI"  value="" required title="Seleccione Fecha Inicial">
                                </div>
                                <div class="form-group" style="margin-top: -10px;">
                                     <label for="fechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                                     <input class="form-control" type="text" name="fechaF" id="fechaF"  value="" required title="Seleccione Fecha Final">
                                </div>
                                <div class="form-group" style="margin-top: -5px">
                                    <label for="cuentaI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta :</label>
                                    <select  name="cuentaI" id="cuentaI" class="select2_single form-control" title="Seleccione Cuenta " style="height: 30px" required>
                                            <option value="">Cuenta</option>                              
                                            <?php  for ($i = 0; $i < count($ci); $i++) {
                                                echo '<option value="'.$ci[$i][0].'">'.$ci[$i][1].' - '.ucwords($ci[$i][2]).'</option>';
                                            } ?>
                                    </select>
                                </div>
                                <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                                    <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                                    <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function() {
              $(".select2_single").select2({
                allowClear: true
              });
            });
        </script>
        <script>
        function reporteExcel(){
           $('form').attr('action', 'informes/INF_DIARIO_CAJA.php?tipo=2');
        }

        function reportePdf(){
            $('form').attr('action', 'informes/INF_DIARIO_CAJA.php?tipo=1');
        }
        </script>
        <?php require_once 'footer.php' ?>  
    </body>
</html>