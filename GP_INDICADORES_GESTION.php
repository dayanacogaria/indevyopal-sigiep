<?php
#############################################################################
#       ******************     Modificaciones       ******************      #
#############################################################################
#27/06/2018 |Erica G. | ARCHIVO CREADO
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
        <title>Indicadores De Gestión</title>
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
            background: #e9e9e9;
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
                <h2 align="center" class="tituloform" style="margin-top:-3px">Indicadores de Gestión</h2>
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra" style="margin-top:15px">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12 col-md-12 col-lg-12" style="padding:10px">
                        <div class="form-group">
                            <div class="form-group form-inline col-sm-1 col-md-1 col-lg-1">
                                <label for="operacion" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo:</label>
                            </div>
                            <div class="form-group form-inline  col-sm-2 col-md-2 col-lg-2">
                                <select name="tipo" id="tipo" class=" form-control select2" title="Seleccione Opere" style="height: auto;" required>
                                    <option value="">Tipo</option>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-1 col-md-1 col-lg-1">
                                <label for="operacion" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                            </div>
                            <div class="form-group form-inline  col-sm-2 col-md-2 col-lg-2">
                                <select name="operacion" id="operacion" class=" form-control select2" title="Seleccione Opere" style="height: auto;" required>
                                    <option value="">Año</option>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-1 col-md-1 col-lg-1">
                                <label for="operacion" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo:</label>
                            </div>
                            <div class="form-group form-inline  col-sm-2 col-md-2 col-lg-2">
                                <select name="periodo" id="periodo" class=" form-control select2" title="Seleccione Periodo" style="height: auto;" required>
                                    <option value="">Periodo</option>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-1 col-md-1 col-lg-1 margen-spr borde-sombra boton-especial">
                                <a href="" title="Imprimir" class="letra-boton">
                                    <span class="glyphicon glyphicon-print"></span> 
                                </a>
                            </div>
                            <div class="form-group form-inline col-sm-1 col-md-1 col-lg-1 margen-spr borde-sombra boton-especial">
                                <a href="" title="Imprimir" class="letra-boton">
                                   <span class="glyphicon glyphicon-th-list"></span> Calcular  &nbsp;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 borde-sombra" style="margin-top:15px">
                    <div class="form-group form-inline  col-md-3 col-lg-3"></div>
                    <div class="form-group form-inline col-sm-8 col-md-8 col-lg-8">
                            <br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Inconsistencias Facturación</label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Eficiencia del Recaudo </label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Variación de Cartera </label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Índice de Reclamos </label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">% Reclamos a Favor de la Empresa </label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Índice de Quejas </label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Quejas a Favor de la Empresa </label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Reconexiones </label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Reinstalaciones </label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Ordenes Trabajo Alcantarillado </label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Índice de Quejas Alcantarillado </label><br/><br/>
                            <label for="codigo" class="col-sm-10 control-label">Cobertura de Medición </label><br/><br/>
                    </div>
                </div>
                
                <div class="col-sm-6 col-md-6 col-lg-6 borde-sombra" style="margin-top:15px">
                    <div class="col-sm-12 col-md-12 col-lg-12 margin-superior">
                        <div class="table-responsive">
                            <table id="tbl" class="table table-bordered clearfix" width="100%">
                                <tr>
                                    <thead>
                                        <th class="cabeza" colspan="4">CÁLCULO</th>
                                    </thead>
                                </tr>
                            </table>
                        </div>
                    </div><br/> <br/> <br/> <br/> <br/> <br/> <br/>
                    
                    <div class="form-group form-inline col-sm-6 col-md-6 col-lg-6">
                        <label for="codigo" class="col-sm-10 control-label">Deuda Final - Deuda Inicial</label><br/>
                        <label for="codigo" class="col-sm-10 control-label">___________________________</label><br/><br/>
                        <label for="codigo" class="col-sm-10 control-label">         Deuda Inicial     </label><br/>
                    </div>
                    <div class="form-group form-inline  col-md-3 col-lg-3">
                        <label for="codigo" class="col-sm-10 control-label">=</label><br/><br/>
                    </div>
                    <div class="form-group form-inline  col-md-3 col-lg-3">
                        <label for="codigo" class="col-sm-10 control-label">Total</label><br/><br/>
                    </div>
                    <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/>
                    <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/>
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
        $("#tipo").select2();
        $("#periodo").select2();
        $("#buscarR").select2();
    </script>
</body>
</html>
