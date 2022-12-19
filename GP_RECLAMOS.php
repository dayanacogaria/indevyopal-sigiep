<?php
#############################################################################
#       ******************     Modificaciones       ******************      #
#############################################################################
#############################################################################
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];

?>
<html>
    <head>
        <title>Reclamos</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <style>
            label #tercero-error, #banco-error, #tipoComprobante-error, #numero-error, #fecha-error, #recaudo-error { 
             display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;
        }
        body{
            font-size: 12px;
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

        <style>
         .form-control {font-size: 12px;}
         .borde-sombra{
            box-shadow: 0px 2px 5px 1px grey;
        }
        .cabeza{
            text-align: center;
            background: #e9e9e9
        }
        .cabeza2{
            text-align: center;
            background: #dfdfdf;
        }
        .boton-especial{
            color: #fff;
            background-color: #337ab7;
            border-color: #2e6da4;
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .letra-boton{
            color: #fff;
        }
         tbl.dataTable thead th,tbl.dataTable thead td{background: e9e9e9;}
        </style>
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
                $("#fecha").datepicker({changeMonth: true,}).val();


        });
        </script>
    </head>
    <body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Registrar Reclamos</h2>
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra" >
                    <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12 col-md-12 col-lg-12" style="padding:10px">
                        <div class="form-group">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href=""  title="Imprimir" class="letra-boton">
                                    <span class="glyphicon glyphicon-print"></span> Imprimir <br/>&nbsp;
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a title="Calcular" class="letra-boton">
                                    <span class="glyphicon glyphicon-list"></span> Consultar <br/>&nbsp;
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href="" title="Abono" class="letra-boton">
                                   <span class="glyphicon glyphicon-usd"></span> Abono <br/>&nbsp;
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href="" title="Acueducto" class="letra-boton">
                                    <span class="glyphicon glyphicon-home"></span> Generar <br/>Factura
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href="" title="Buscar" class="letra-boton">
                                    <span class="glyphicon glyphicon-search"></span> <br/>&nbsp;
                                </a>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra" style="margin-top:15px">
                    <div class="col-sm-12 col-md-12 col-lg-12 margin-superior">
                        <div class="table-responsive">
                            <table id="tbl" class="table table-bordered clearfix" width="100%">
                                <tr>
                                    <thead>
                                        <th class="cabeza" colspan="1">Consecutivo</th>
                                        <th class="cabeza" colspan="1">Fecha</th>
                                        <th class="cabeza" colspan="1">Presentación</th>
                                        <th class="cabeza" colspan="1">Código Usuario</th>
                                        <th class="cabeza" colspan="1">Código Interno</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="3">Nombre Usuario</th>
                                        <th class="cabeza" colspan="1">Tipo Identificación</th>
                                        <th class="cabeza" colspan="1">N° Identificación</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="1">Nro Rad.</th>
                                        <th class="cabeza" colspan="2">Dirección</th>
                                        <th class="cabeza" colspan="1">Barrio</th>
                                        <th class="cabeza" colspan="1">Teléfono</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                   <thead>
                                        <th class="cabeza" colspan="3">Quien Presenta PQR</th>
                                        <th class="cabeza" colspan="1">Tipo Identificación</th>
                                        <th class="cabeza" colspan="1">N° Identificación</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza2" colspan="5">DATOS DEL RECLAMO</th>
                                    </thead>
                                    <thead>
                                        <th class="cabeza" colspan="2">Tipo Reclamo</th>
                                        <th class="cabeza" colspan="2">Descripción Reclamo</th>
                                        <th class="cabeza" colspan="1">Tipo Requerimiento</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="5">Documentos Presentados</th>
                                    </thead>
                                    <thead>
                                        <th class="cabeza" colspan="2">Tipo Documento</th>
                                        <th class="cabeza" colspan="1">Folios</th>
                                        <th class="cabeza" colspan="1">Límite de Solución</th>
                                        <th class="cabeza" colspan="1">Factura</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="5">Observaciones</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="5">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="5">Sistema de Abastecimiento</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="5">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="5">Tramitar a Dependencia</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="5">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza2" colspan="5">DATOS SOLUCIÓN</th>
                                    </thead>
                                    <thead>
                                        <th class="cabeza" colspan="2">A Favor De</th>
                                        <th class="cabeza" colspan="1">Tipo Respuesta</th>
                                        <th class="cabeza" colspan="1">Rad. Respuesta</th>
                                        <th class="cabeza" colspan="1">Tipo Notificación</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="1">Fecha Notificación</th>
                                        <th class="cabeza" colspan="1">Encargado</th>
                                        <th class="cabeza" colspan="3">Nombre del Encargado</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="">Fecha Solución</th>
                                        <th class="cabeza" colspan="2">Solución Dada</th>
                                        <th class="cabeza" colspan="2">Elaboró</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                    </tr>
                                </tr>
                            </table>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <?php require './footer.php'; ?>
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript"> 
        $("#operacion").select2();
        $("#banco").select2();
        $("#tipoComprobante").select2();
        $("#buscarR").select2();

    </script>
</body>
</html>
