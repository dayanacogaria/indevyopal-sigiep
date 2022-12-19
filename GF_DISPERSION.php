<?php
#############################################################################
#       ******************     Modificaciones       ******************      #
#############################################################################
#26/03/2018 |Erica G. | ARCHIVO CREADO
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
        <title>Dispersión</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <style>
            label #fechaI-error, #fechaF-error{ 
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
                $("#fechaF").datepicker({changeMonth: true,}).val();


        });
        </script>
        <script>
            $(document).ready(function() {
                var i= 0;
                $('#tableO thead th').each( function () {
                    if(i => 0) {
                        var title = $(this).text();
                        switch (i){
                            case 0:
                                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                                break;
                            case 1:
                                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                                break;
                            case 2:
                                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                                break;
                            case 3:
                                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                                break;
                            case 4:
                                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                                break;
                            case 5:
                                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                                break;
                            case 6:
                                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                                break;
                             case 7:
                                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                                break;  
                            case 8:
                                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                                break;  
                            case 9:
                                $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                                break;  


                        }
                        i = i+1;
                    } else {
                        i = i+1;
                    }
                });
                var table = $('#tableO').DataTable({
                    "autoFill": true,
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ registros",
                        "zeroRecords": "No Existen Registros...",
                        "info": "Página _PAGE_ de _PAGES_ ",
                        "infoEmpty": "No existen datos",
                        "infoFiltered": "(Filtrado de _MAX_ registros)",
                        "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                    },
                    scrollY: 220,
                    "scrollX": true,
                    scrollCollapse: true,
                    paging: false,
                    fixedColumns:   {
                        leftColumns: 1
                    },
                    'columnDefs': [{
                        'targets': 0,
                        'searchable':false,
                        'orderable':false,
                        'className': 'dt-body-center'
                    }]
                });
                var i = 0;
                table.columns().every( function () {
                    var that = this;
                    if(i!=0) {
                        $( 'input', this.header() ).on( 'keyup change', function () {
                            if ( that.search() !== this.value ) {
                                that
                                    .search( this.value )
                                    .draw();
                            }
                        });
                        i = i+1;
                    } else {
                        i = i+1;
                    }
                });
            });
        </script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Pagos Por Dispersión</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:buscar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline" style="margin-top: -5px; margin-left: 50px">
                                <div class="form-group form-inline  col-md-1 col-lg-1" >
                                    <label for="fechaI" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Inicial:</label>
                                </div>
                                <div class="form-group form-inline  col-md-4 col-lg-4" >
                                    <input name="fechaI" id="fechaI" class="form-control input-sm" title="Seleccione Fecha Inicial" style="width:250px; " required autocomplete="off" />
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" >
                                    <label for="fechaF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Final:</label>
                                </div>
                                <div class="form-group form-inline  col-md-4 col-lg-4">
                                    <input name="fechaF" id="fechaF" class="form-control input-sm" title="Seleccione Fecha Final" style="width:250px; " required autocomplete="off" />
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <button id="btnGuardar" type="submit" class="btn btn-primary glyphicon glyphicon-search" style="margin-bottom: 5px;" title="Buscar"></button>
                                    <button id="btnImprimir" type="button" class="btn btn-primary glyphicon glyphicon-print" style="margin-bottom: 5px;" title="Imprimir"></button>
                                    <button id="btnNuevo" type="button" class="btn btn-primary glyphicon glyphicon-plus" style="margin-bottom: 5px;" title="Nuevo"></button>
                                </div>
                                <input type="hidden" name="comprobantes_s" id="comprobantes_s" value="0">
                            </div>
                        </form>
                    </div>
                    <br/>
                    <div class="form-group" style="margin-top: -15px">
                        <div  style="text-align:right">
                            <input style="margin-top:10px; margin-bottom: 10px;" type="checkbox" onclick="if (this.checked) marcar(true); else marcar(false);" /><strong style=" font-size: 12px;">&nbsp;&nbsp;Marcar/Desmarcar Todos</strong>
                        </div>
                        <script type="text/javascript">
                                function marcar(status) 
                                {
                                    var tabla1          = document.getElementById("tableO");
                                    var eleNodelist1    = tabla1.getElementsByTagName("input");
                                    var comprobantes    = $("#comprobantes_s").val();
                                    for (i = 0; i < eleNodelist1.length; i++) {
                                        var comp        = 'comprobante'+i;
                                        var comp_sel    = $("#"+comp).val(); 
                                        console.log(comp_sel);
                                        console.log(comp_sel !=="undefined");
                                        console.log(comp_sel !=='undefined');
                                        if(typeof(comp_sel) !== "undefined"){
                                            if(status==true){       
                                                comprobantes +=','+comp_sel;
                                            } else {
                                                comprobantes = comprobantes.replace(','+comp_sel, '');
                                            }
                                        }
                                        $("#comprobantes_s").val(comprobantes);
                                        if (eleNodelist1[i].type == 'checkbox'){
                                          
                                            if (status == null) {
                                                eleNodelist1[i].checked = !eleNodelist1[i].checked;
                                            }else {
                                                eleNodelist1[i].checked = status;
                                            }
                                        }
                                            
                                    }
                                }
                            </script>
                        <div class="table-responsive" style="margin-left: 0px; margin-right: 0px;margin-top:0px;">
                            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                                <table id="tableO" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="max-height: 200px">
                                    <thead>
                                        <tr>
                                            <td class="oculto">Identificador</td>
                                            <td width="7%"></td>
                                            <td class="cabeza"><strong>Cuenta Bancaria Tercero</strong></td>
                                            <td class="cabeza"><strong>Fecha</strong></td>
                                            <td class="cabeza"><strong>Número Comprobante</strong></td>
                                            <td class="cabeza"><strong>Nit</strong></td>
                                            <td class="cabeza"><strong>Banco</strong></td>
                                            <td class="cabeza"><strong>Concepto</strong></td>
                                            <td class="cabeza"><strong>Valor</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="oculto">Identificador</th>
                                            <th width="7%"></th>
                                            <th>Cuenta Bancaria Tercero</th>
                                            <th>Fecha</th>
                                            <th>Número Comprobante</th>
                                            <th>Nit</th>
                                            <th>Banco</th>
                                            <th>Concepto</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php     
                                        if(!empty($_REQUEST['fi'])){  
                                            $fechaI = fechaC($_REQUEST['fi']);
                                            $fechaF = fechaC($_REQUEST['ff']);
                                            $rowd = $con->Listar("SELECT DISTINCT cn.id_unico, 
                                            cb.numerocuenta, DATE_FORMAT(cn.fecha,'%d/%m/%Y'), cn.numero, 
                                            t.numeroidentificacion, c.codi_cuenta, 
                                            cn.descripcion, SUM(IF(dc.valor>0,dc.valor, dc.valor *-1))
                                            FROM gf_comprobante_cnt cn 
                                            LEFT JOIN gf_forma_pago fp ON cn.formapago = fp.id_unico 
                                            LEFT JOIN gf_tercero t ON t.id_unico  = cn.tercero 
                                            LEFT JOIN gf_cuenta_bancaria_tercero cbt ON t.id_unico = cbt.tercero 
                                            LEFT JOIN gf_cuenta_bancaria cb ON cbt.cuentabancaria = cb.id_unico 
                                            LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                                            LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                            WHERE fp.dispersion = 1  AND c.clasecuenta IN (11, 12)
                                            AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'
                                            GROUP BY cn.id_unico");
                                            for ($i = 0; $i < count($rowd); $i++) {    
                                                
                                                echo '<tr><td style="display: none;"></td>';
                                                echo '<td class="campos text-center">';
                                                echo '<input name="seleccion'.$i.'" id="seleccion'.$i.'" type="checkbox" onchange="cambiarV('.$i.')" style="width: 50px;height: 20px;">';
                                                echo '<input name="comprobante'.$i.'" id="comprobante'.$i.'" type="hidden" value="'.$rowd[$i][0].'">';
                                                echo '</td>';
                                                echo '<td class="campos text-left">'.$rowd[$i][1].'</td>';  
                                                echo '<td class="campos text-left">'.$rowd[$i][2].'</td>';                  
                                                echo '<td class="campos text-left">'.$rowd[$i][3].'</td>';                   
                                                echo '<td class="campos text-left">'.$rowd[$i][4].'</td>';                   
                                                echo '<td class="campos text-left">'.$rowd[$i][5].'</td>';  
                                                echo '<td class="campos text-left">'.$rowd[$i][6].'</td>';  
                                                echo '<td class="campos text-right">'.number_format($rowd[$i][7],2,'.',',').'</td>';
                                                echo '</td>';
                                                echo '</tr>';
                                            }                                       
                                        }
                                        ?>
                                    </tbody>
                                </table>                                
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
            function buscar(){
                let fechaI = $("#fechaI").val();
                let fechaF = $("#fechaF").val();
                document.location ='GF_DISPERSION.php?fi='+fechaI+'&ff='+fechaF;
            }
            function cambiarV(i){
                var id           = $("#comprobante"+i).val();
                var comprobantes = $("#comprobantes_s").val();
                if($("#seleccion"+i).prop('checked')){
                    comprobantes +=','+id;
                } else {
                    comprobantes = comprobantes.replace(','+id, '');
                }
                $("#comprobantes_s").val(comprobantes);
            }
            $("#btnImprimir").click(function(){
                let comprobantes = $("#comprobantes_s").val();
                console.log(comprobantes);
                if(comprobantes == '0'){
                    $("#mensaje").html('Seleccione Algún Registro');
                    $("#modalMensajes").modal('show');
                } else {
                    window.open('informes/INF_DISPERSION.php?ids='+comprobantes);
                }
            });
            $("#btnNuevo").click(function(){
                document.location = 'GF_DISPERSION.php';
            })
        </script>
        
    </body>
</html>
