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
        <title>Facturación Servicios Públicos</title>
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
                <h2 align="center" class="tituloform" style="margin-top:-3px">Facturación Servicios Públicos</h2>
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra" >
                    <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12 col-md-12 col-lg-12" style="padding:10px">
                        <div class="form-group">
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a title="Calcular" class="letra-boton">
                                    <span class="glyphicon glyphicon-list"></span> Calcular <br/>&nbsp;
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href=""  title="Imprimir" class="letra-boton">
                                    <span class="glyphicon glyphicon-print"></span> Imprimir <br/>&nbsp;
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href="" title="Abono" class="letra-boton">
                                   <span class="glyphicon glyphicon-usd"></span> Abono <br/>&nbsp;
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href="" title="Acueducto" class="letra-boton">
                                    <span class="glyphicon glyphicon-home"></span> Factura <br/>Acueducto
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href=""  title="Aseo" class="letra-boton">
                                    <span class="glyphicon glyphicon-home"></span> Factura <br/>Aseo
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href="" title="Buscar" class="letra-boton">
                                    <span class="glyphicon glyphicon-search"></span> <br/>&nbsp;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra" style="margin-top:15px">
                    <div class="col-sm-12 col-md-12 col-lg-12 margin-superior">
                        <div class="table-responsive">
                            <table id="tbl" class="table table-bordered clearfix" width="100%">
                                <tr>
                                    <thead>
                                        <th class="cabeza" colspan="7">Nombre</th>
                                        <th class="cabeza" colspan="2">Año Inicio</th>
                                        <th class="cabeza" colspan="2">Periodo Inicio</th>
                                        <th class="cabeza" colspan="2">Matrícula</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="7">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="4">Código</th>
                                        <th class="cabeza" colspan="3">Código Interno</th>
                                        <th class="cabeza" colspan="3">Dirección</th>
                                        <th class="cabeza" colspan="1">Año</th>
                                        <th class="cabeza" colspan="1">Periodo</th>
                                        <th class="cabeza" colspan="1">Ciclo</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="4">&nbsp;</td>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="4">Uso</th>
                                        <th class="cabeza" colspan="4">Estrato</th>
                                        <th class="cabeza" colspan="3">Categoría Aseo</th>
                                        <th class="cabeza" colspan="1">Peso Aseo</th>
                                        <th class="cabeza" colspan="1">Tipo Aforo</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="4">&nbsp;</td>
                                        <td class="campos" colspan="4">&nbsp;</td>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="4">Uso</th>
                                        <th class="cabeza" colspan="4">Estrato</th>
                                        <th class="cabeza" colspan="3">Categoría Aseo</th>
                                        <th class="cabeza" colspan="1">Peso Aseo</th>
                                        <th class="cabeza" colspan="1">Tipo Aforo</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="4">&nbsp;</td>
                                        <td class="campos" colspan="4">&nbsp;</td>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="4">Estado Servicio</th>
                                        <th class="cabeza" colspan="4">Estado Usuario</th>
                                        <th class="cabeza" colspan="3">Medidor</th>
                                        <th class="cabeza" colspan="1">Número Medidor</th>
                                        <th class="cabeza" colspan="1">Dígitos</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="4">&nbsp;</td>
                                        <td class="campos" colspan="4">&nbsp;</td>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="7">Servicios A Cobrar</th>
                                        <th class="cabeza" colspan="6">Datos Del Pago</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="7">&nbsp;</td>
                                        <td class="campos" colspan="6">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="2">Acueducto</th>
                                        <th class="cabeza" colspan="2">Alcantarillado</th>
                                        <th class="cabeza" colspan="1">Aseo</th>
                                        <th class="cabeza" colspan="1">Barrido</th>
                                        <th class="cabeza" colspan="1">Otros</th>
                                        <th class="cabeza" colspan="2">Banco</th>
                                        <th class="cabeza" colspan="1">Fecha</th>
                                        <th class="cabeza" colspan="1">Valor</th>
                                        <th class="cabeza" colspan="1">Paquete</th>
                                        <th class="cabeza" colspan="1">Tipo</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="7">Últimos Consumos</th>
                                        <th class="cabeza" colspan="3">Lecturas</th>
                                        <th class="cabeza" colspan="3">Consumos</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="7">&nbsp;</td>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="1">6</th>
                                        <th class="cabeza" colspan="1">5</th>
                                        <th class="cabeza" colspan="1">4</th>
                                        <th class="cabeza" colspan="1">3</th>
                                        <th class="cabeza" colspan="1">2</th>
                                        <th class="cabeza" colspan="1">1</th>
                                        <th class="cabeza" colspan="1">Promedio</th>
                                        <th class="cabeza" colspan="1">Penulti.</th>
                                        <th class="cabeza" colspan="1">Anterior</th>
                                        <th class="cabeza" colspan="1">Actual</th>
                                        <th class="cabeza" colspan="1">Consumo</th>
                                        <th class="cabeza" colspan="1">Cons Estim</th>
                                        <th class="cabeza" colspan="1">Prom Act</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="3">Lectura Tomada</th>
                                        <th class="cabeza" colspan="4">Nov Aforo</th>
                                        <th class="cabeza" colspan="2">Periodos Promedio</th>
                                        <th class="cabeza" colspan="2">Atrasos</th>
                                        <th class="cabeza" colspan="1">Exn. Int.</th>
                                        <th class="cabeza" colspan="1">Aforo Con</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="4">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="2">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="13">Datos Del Cobro</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="13">&nbsp;</td>
                                    </tr>
                                    <thead>
                                        <th class="cabeza" colspan="4">Factura</th>
                                        <th class="cabeza" colspan="3">Fecha Impresión</th>
                                        <th class="cabeza" colspan="3">Valor Deuda</th>
                                        <th class="cabeza" colspan="1">Valor Aseo</th>
                                        <th class="cabeza" colspan="1">Valor Sin Aseo</th>
                                        <th class="cabeza" colspan="1">TOTAL FACTURA</th>
                                    </thead>
                                    <tr>
                                        <td class="campos" colspan="4">&nbsp;</td>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="3">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                        <td class="campos" colspan="1">&nbsp;</td>
                                    </tr>
                                </tr>
                            </table>
                        </div>
                    </div> 
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra" style="margin-top:10px" >
                    <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12 col-md-12 col-lg-12" style="padding:10px">
                        <div class="form-group">
                            <div class="col-sm-4 col-md-4 col-lg-4">
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href="" title="Abono" class="letra-boton">
                                   <span class="glyphicon glyphicon-usd"></span> Financiables
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href="" title="Acueducto" class="letra-boton">
                                    <span class="glyphicon glyphicon-home"></span> Saldo Créditos
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href=""  title="Aseo" class="letra-boton">Detalle Factura
                                </a>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 margen-spr borde-sombra boton-especial">
                                <a href="" title="Buscar" class="letra-boton">Aseo Tercero
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!--            <div class="col-sm-8 col-md-8 col-lg-8">
                <div class="col-sm-6 col-md-6 col-lg-6 margin-superior">
                    <div class="table-responsive">
                        <input type="hidden" name="txtFactura" id="txtFactura" value="<?php echo $fat; ?>">
                        <input type="hidden" name="txtMov" id="txtMov" value="<?php echo $mov; ?>">
                        <input type="hidden" name="txtCaja" id="txtCaja" value="<?php echo $caja ?>">
                        <table id="tblFactura" class="table table-bordered clearfix" width="100%">
                            <thead>
                                <tr>
                                    <th class="cabeza" style="width: 12%;">Código</th>
                                    <th class="cabeza">Nombre</th>
                                    <th class="cabeza" style="width: 10%;">Unidad</th>
                                    <th class="cabeza" title="Cantidad">Cantidad</th>
                                    <th class="cabeza">Iva</th>
                                    <th class="cabeza">Impo</th>
                                    <th class="cabeza">Valor</th>
                                    <th class="cabeza">Total</th>
                                    <th class="cabeza" style="width: 4%;"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-sm-4 col-md-4 col-lg-4 borde-sombra margin-superior">
                    <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-lg-12 margin-superior">
                            <input type="text" name="txtNumero" id="txtNumero" class="form-control" placeholder="Codigo de Barras" style="width: 100%;" autocomplete="off" autofocus>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <table id="tblProductos" class="table-bordered clearfix" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="cabeza" style="width: 25%;">Producto</th>
                                        <th class="cabeza">Nombre</th>
                                        <th class="cabeza">Unidad</th>
                                        <th class="cabeza">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 contenedorT text-center">
                            <a href="7" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">7</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="8" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">8</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="9" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">9</a>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 contenedorT text-center">
                            <a href="4" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">4</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="5" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">5</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="6" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">6</a>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 contenedorT text-center">
                            <a href="1" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">1</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="2" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">2</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="3" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">3</a>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 contenedorT text-center">
                            <a href="0" class="btn btn-primary borde-sombra col-sm-2 col-md-2 col-lg-3 botones">0</a>
                            <span class="col-sm-2 col-md-2 col-lg-1"></span>
                            <a href="C" class="btn btn-primary borde-sombra col-sm-6 col-md-6 col-lg-7 botones"><span class="fa fa-long-arrow-left"></span></a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <table id="tblTotal" class="display" width="100%">
                        <thead>
                        <tr>
                            <th>SUBTOTAL</th>
                            <td class="text-right"><label id="lblSubTotal">$ 0.00</label></td>
                            <th>TOTAL</th>
                            <th class="text-right borde-inferior"><label id="lblTotal">$ 0.00</label></th>
                            <td class="text-center Btns">
                                <a class="col-sm-12 col-md-12 col-lg-12 btn-sm btn-primary borde-sombra" title="Imprimir" id="btnImprimir"><span class="glyphicon glyphicon-print"></span></a>
                            </td>
                        </tr>
                        <tr>
                            <th>IVA</th>
                            <td class="text-right"><label id="lblIva">$ 0.00</label></td>
                            <th>RECIBIDO</th>
                            <td class="VRecibido borde-inferior">
                                <input type="text" id="txtRecibido" name="txtRecibido" class="form-control" placeholder="Valor Recibido" value="" title="Ingrese el valor recibido">
                            </td>
                        </tr>
                        <tr>
                            <th>IMPOCONSUMO</th>
                            <td class="text-right"><label id="lblImpo">$ 0.00</label></td>
                            <th>CAMBIO</th>
                            <td class="text-right borde-inferior"><label id="lblCambio">$ 0.00</label></td>
                            <td class="text-center Btns">
                                <a href="javascript:new_()" class="col-sm-12 col-md-12 col-lg-12 btn-sm btn-primary borde-sombra" title="Nueva Factura" id="btnNuevo"><span class="glyphicon glyphicon-plus"></span></a>
                            </td>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2">
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra margin-superior">
                    <h4 class="text-center">INFORMACIÓN</h4>
                    <div class="form-group margin-superior">
                        <label for="lblNumero" class="control-label col-sm-7 col-md-7 col-lg-7">N° Factura:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5 text-right">
                            <label id="lblNumeroFactura"><?php echo $numero ?></label>
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <div class="form-group">
                        <label for="lblCaja" class="control-label col-sm-7 col-md-7 col-lg-7">Caja:</label>
                        <div class="col-sm-5 col-md-5 col-lg-5 text-right">
                            <label id="lblCaja"><?php echo $nomCaja ?></label>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label for="lblVendendor" class="control-label col-sm-7 col-md-7 col-lg-7">Vendedor:</label>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <label id="lblVendendor" ><?php echo $nomPer ?></label>
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <br/>
                    <div class="form-group">
                        <label for="txtFecha" class="control-label col-sm-7 col-md-7 col-lg-7">Fecha:</label>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <input type="text" id="txtFecha" name="txtFecha" value="<?php echo  $fecha ?>" class="form-control fecha" style='width: 100%;' readonly >
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <br/>
                    <div class="form-group">
                        <label for="sltFormaPago" class="control-label col-sm-12 col-md-12 col-lg-12">Forma de Pago</label>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <select name="sltFormaPago" id="sltFormaPago" class="select form-control" placeholder="Forma de Pago">
                                <option value=""></option>
                                <?php
                                if(count($cuenta) > 0){
                                    $html = "";
                                    foreach ($cuenta  as $row){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                </div>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3">
                <div class="col-sm-12 col-md-12 col-lg-12 borde-sombra margen-superior form-group">
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a href="" class="btn btn-primary borde-sombra" title="Registrar Tercero">
                            <span class="glyphicon glyphicon-user"></span>
                        </a>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a class="btn btn-primary borde-sombra" data-toggle="modal" data-target="#mdlInformes" title="Informes de Caja">
                            <span class="glyphicon glyphicon-list"></span>
                        </a>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a href="" class="btn btn-primary borde-sombra" title="Cierre de Caja">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 margen-spr">
                        <a href="" class="btn btn-primary borde-sombra" title="Buscar Facturas" data-toggle="modal" data-target="#mdlFacturas">
                            <span class="glyphicon glyphicon-search"></span>
                        </a>
                    </div>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                </div>
            </div>-->
        </div>
    </div>
    <?php require './footer.php'; ?>
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
</body>
</html>
