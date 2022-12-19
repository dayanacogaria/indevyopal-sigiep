<?php
#####################################################################################################################################################################
#                                               MODIFICACIONES
######################################################################################################################################################################
#20/02/2018| ERICA G. | ARCHIVO CREADO
######################################################################################################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('./jsonPptal/funcionesPptal.php');
require_once('./head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nannov = anno($anno);
$anno2 = $nannov-1;
$an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2'");
$ida2  = $an2[0][0];

#*** Listar Cuentas Año ***#
$vg = $con->Listar("SELECT c.id_unico, c.codi_cuenta, 
        LOWER(c.nombre), c.equivalente_va, LOWER(ca.nombre)  
        FROM gf_cuenta c 
        LEFT JOIN gf_cuenta ca ON c.equivalente_va = ca.codi_cuenta AND ca.parametrizacionanno = $ida2
        WHERE c.parametrizacionanno = $anno AND (c.movimiento =1 OR c.auxiliartercero = 1) 
        AND c.equivalente_va IS NOT NULL 
        ORDER BY c.codi_cuenta ASC");
?>
<html>
    <head>
    <title>Homologación Plan Contable Vigencia Anterior</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
    </head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Homologación Plan Contable Vigencia Anterior</h2>    
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Cuenta Vigencia Actual</strong></td>
                                    <td><strong>Equivalente Vigencia Anterior</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Cuenta Vigencia Actual</th>
                                    <th>Equivalente Vigencia Anterior</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <?php 
                                    # Ciclo De Concepto Predial
                                    for ($i = 0; $i < count($vg); $i++) {
                                            echo '<tr>';
                                            echo '<td style="display: none;"></td>';
                                            echo '<td>'; 
                                            echo '<a  onclick="javascript:eliminar('.$vg[$i][0].')"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>';
                                            echo '<a  href="GF_HOMOLOGACION_CUENTAS_VA.php?id='.md5($vg[$i][0]).'"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>';
                                            echo '</td>';
                                            echo '<td>'.$vg[$i][1].' - '.ucwords($vg[$i][2]).'</td>';
                                            echo '<td>'.$vg[$i][3].' - '.ucwords($vg[$i][4]).'</td>';
                                            echo '</tr>';
                                    }?>
                            </tbody>
                        </table> 
                        <div align="right"><a href="GF_HOMOLOGACION_CUENTAS_VA.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
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
                    <label id="mensajeEliminar" name="mensaje"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once 'footer.php'; ?>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });
        });
        
    </script>
    <!----**Funcion Eliminar **---->
    <script>
        function eliminar(id){
            $("#mensajeEliminar").html('¿Desea Eliminar El Registro De Homologación?');  
            $("#modalEliminar").modal('show'); 
            $("#btnAceptar").click(function(){
                $("#modalEliminar").modal('hide');  
                var form_data ={id:id,action:4}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_cuentaJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        console.log(response);
                        if(response==true){
                            $("#mensaje").html('Información Eliminada Correctamente');  
                            $("#modalMensajes").modal('show'); 

                        } else {
                            $("#mensaje").html('No Se Ha Podido Eliminar La Información');  
                            $("#modalMensajes").modal('show'); 
                        }
                    }
                });
            });
            $("#btnAceptar").click(function(){
               $("#modalEliminar").modal('hide');  
            });
        }                                                                                                                                                                                                 
    </script>      
    <script>
        $("#Aceptar").click(function(){
           document.location.reload();
        });
    </script>                                                                                                                                                                                                        
</body>
</html>



