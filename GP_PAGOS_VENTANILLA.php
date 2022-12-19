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
        <title>Pagos Por Ventanilla</title>
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
        .cabeza{background: e9e9e9;}
        
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
                <h2 align="center" class="tituloform" style="margin-top:-3px">Pagos Por Ventanilla</h2>
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra" style="margin-top:15px">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12 col-md-12 col-lg-12" style="padding:10px">
                        <div class="form-group">
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label>Cajero</label>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label>Fecha:</label>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label><?php echo date('d/m/Y')?></label>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href="" title="Imprimir" class="letra-boton">
                                   <span class="glyphicon glyphicon-print"></span> Imprimir Movimiento
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 borde-sombra" style="margin-top:15px">
                    <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:24px">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="operacion" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Operación:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <select name="operacion" id="operacion" class=" form-control select2" title="Seleccione Opere" style="height: auto;" required>
                                    <option value="">Operación</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="tipo" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <select name="tipo" id="tipo" class=" form-control select2" title="Seleccione Tipo" style="height: auto;" required>
                                    <option value="">Tipo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group form-inline  col-md-6 col-lg-6">
                            <label for="codigo" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Suscriptor:</label>
                        </div>
                        <div class="form-group form-inline  col-md-6 col-lg-6">
                            <input name="numero" id="numero" class="col-sm-4 form-control" title="Seleccione Código" required style="width: 100%"  />
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="nombre" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Nombre:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <input name="nombre" id="nombre" class="col-sm-4 form-control" title="Seleccione Nombre" required style="width: 100%"  />
                            </div>
                        </div>
                    </div>                    
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="direccion" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Dirección:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <input name="direccion" id="direccion" class="col-sm-4 form-control" title="Seleccione Dirección" required style="width: 100%"  />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="su" class="col-sm-10 control-label">Subtotal:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6" style="text-align: right">
                                <label for="su" class="col-sm-10 control-label"><u>____________________________$0.00</u></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="su" class="col-sm-10 control-label">Aseo Terceros:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6" style="text-align: right">
                                <label for="su" class="col-sm-10 control-label"><u>____________________________$0.00</u></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="su" class="col-sm-10 control-label">Valor Factura:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6" style="text-align: right">
                                <label for="su" class="col-sm-10 control-label"><u>____________________________$0.00</u></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="su" class="col-sm-10 control-label">Valor Pagar:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6" style="text-align: right">
                                <label for="su" class="col-sm-10 control-label"><u>____________________________$0.00</u></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="su" class="col-sm-10 control-label">Efectivo:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6" style="text-align: right">
                                <label for="su" class="col-sm-10 control-label"><u>____________________________$0.00</u></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="su" class="col-sm-10 control-label">Cheque:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6" style="text-align: right">
                                <label for="su" class="col-sm-10 control-label"><u>____________________________$0.00</u></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="su" class="col-sm-10 control-label">Devolución:</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6" style="text-align: right">
                                <label for="su" class="col-sm-10 control-label"><u>____________________________$0.00</u></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12 col-md-12 col-lg-12 borde-sombra" style="padding:10px">
                        <div class="form-group">
                            <div class="col-sm-6 col-md-6 col-lg-6 margen-spr borde-sombra boton-especial">
                                <a href="" title="Imprimir" class="letra-boton">
                                   <span class="glyphicon glyphicon-us"></span>Registrar<br/>Pago
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6 margen-spr borde-sombra boton-especial">
                                <a href="" title="Imprimir" class="letra-boton">
                                   <span class="glyphicon glyphicon-us"></span>Timbrar<br/>Recibo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-md-6 col-lg-6 borde-sombra" style="margin-top:15px">
                    <div class="col-sm-12 col-md-12 col-lg-12 margin-superior">
                        <div class="table-responsive">
                            <table id="tbl" class="table table-bordered clearfix" width="100%">
                                <tr>
                                    <thead>
                                        <th class="cabeza" colspan="4">DETALLES DE PAGOS</th>
                                    </thead>
                                    <thead>
                                        <th class="cabeza">Código</th>
                                        <th class="cabeza">Subtotal</th>
                                        <th class="cabeza">Aseo Terceros</th>
                                        <th class="cabeza">Total Pago</th>
                                    </thead>
                                </tr>
                            </table>
                        </div>
                    </div><br/> 
                    <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> 
                    <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> 
                    <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/> <br/>
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
        $("#tipoComprobante").select2();
        $("#buscarR").select2();
    </script>
</body>
</html>
