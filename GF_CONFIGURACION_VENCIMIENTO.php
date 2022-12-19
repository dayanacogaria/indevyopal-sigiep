<?php
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];
if(empty($_GET['id'])){ 
    $rowc       = $con->Listar("SELECT id_unico, LOWER(nombre) FROM gf_clase_pptal 
    WHERE id_unico NOT IN (SELECT clase FROM gf_vencimiento WHERE compania = $compania )");
} else {
    $rowc       = $con->Listar("SELECT id_unico, LOWER(nombre) FROM gf_clase_pptal 
    WHERE id_unico!=1 AND id_unico NOT IN (SELECT clase FROM gf_vencimiento WHERE compania = $compania )");
}
$row = $con->Listar("SELECT v.id_unico,cl.id_unico,  
    LOWER(cl.nombre), v.dias 
    FROM gf_vencimiento v 
    LEFT JOIN gf_clase_pptal cl ON v.clase = cl.id_unico 
    WHERE v.compania = $compania");
?>
<html>
    <head>
        <title>Configuración Vencimientos</title>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <script src="js/md5.pack.js"></script>
        <style>

            label #clase-error,#dias-error {
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
                rules: {
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });
        </script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Configuración Vencimientos</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline" style="margin-top: -5px; margin-left: 50px">
                                <div class="form-group form-inline  col-md-1 col-lg-1" >
                                    <label for="clase" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Clase:</label>
                                </div>
                                <div class="form-group form-inline  col-md-4 col-lg-4" >
                                    <select name="clase" id="clase" class="form-control col-sm-1 col-md-1 col-lg-1 select2_single" title="Seleccione Clase" style="width: 250px; text-align: left" required="required">
                                        <option value="">Clase</option>
                                        <?php for ($z = 0; $z < count($rowc); $z++) {
                                            echo '<option value="'.$rowc[$z][0].'">'.ucwords($rowc[$z][1]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" >
                                    <label for="dias" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Días:</label>
                                </div>
                                <div class="form-group form-inline  col-md-4 col-lg-4">
                                    <input type="text" name="dias" id="dias" placeholder="Días" class="form-control col-sm-1 col-md-1 col-lg-1" title="Ingrese Días Vencimiento"  autocomplete="off" style="width:250px" required="required" onkeypress="return txtValida(event, 'num')">
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1">
                                    <button id="btnGuardar" type="submit" class="btn btn-primary glyphicon glyphicon-floppy-disk shadow guardar" style="margin-bottom: 5px;" title="Guardar"></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <br/>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px" align="center"></td>
                                        <td><strong>Clase</strong></td>
                                        <td><strong>Días</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Clase</th>
                                        <th>Días</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?PHP 
                                    for ($i = 0; $i < count($row); $i++) {
                                        echo '<tr>';
                                        echo '<td style="display: none;">'.$row[$i][0].'</td>';
                                        echo '<td>'; 
                                        echo '<a href="#" onclick="javascript:eliminar('.$row[$i][0].')"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>';
                                        echo '</td>';
                                        echo '<td>'.ucwords($row[$i][2]).'</td>'; 
                                        echo '<td>'.$row[$i][3].'</td>'; 
                                        echo '</tr>';
                                    } ?>
                                </tbody>

                            </table>       
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
        <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensajeEliminar" name="mensajeEliminar"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    allowClear: true,
                });
            });
            function guardar(){
                jsShowWindowLoad('Guardando Datos ...');
                var formData = new FormData($("#form")[0]);
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_vencimientoJson.php?action=1",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    {
                        jsRemoveWindowLoad();
                        console.log(response);
                        if(response==1){
                            $("#mensaje").html('Información Guardada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location.reload();
                            })
                        } else {
                            $("#mensaje").html('No Se Ha Podido Guardar Información');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                $("#modalMensajes").modal("hide");
                            })
                        }
                    }
                });
            }
            function eliminar(id){
                var result = '';
                $("#mensajeEliminar").html('¿Desea Eliminar El Registro Seleccionado?');
                $("#modalEliminar").modal('show');
                $("#btnAceptar").click(function(){
                    $("#modalEliminar").modal('hide');
                    $.ajax({
                        type:"GET",
                        url:"jsonPptal/gf_vencimientoJson.php?id="+id+'&action=3',
                        success: function (response) {
                            console.log(response);
                            if(response==1){
                                $("#mensaje").html('Información Eliminada Correctamente');
                                $("#modalMensajes").modal("show");
                                $("#Aceptar").click(function(){
                                    document.location.reload();
                                })
                            } else {
                            $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                $("#modalMensajes").modal("hide");
                            })
                        }
                        }
                    });
                });
               }
        </script> 
    </body>
</html>

