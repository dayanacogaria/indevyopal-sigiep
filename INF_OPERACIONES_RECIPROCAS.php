<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#04/04/2019 | Creado
########################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('Conexion/ConexionPDO.php');
require 'jsonPptal/funcionesPptal.php';
require_once('head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
?>
<html>
    <head>
        <title>Informe Operaciones Recíprocas</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="js/md5.pack.js"></script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Informe Operaciones Recíprocas</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/INF_OPERACIONES_RECIPROCAS.php" target=”_blank” >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Inicial:</label>
                                <select name="mesI" id="mesI" class="select2_single form-control" title="Seleccione mes Inicial" style="height: auto " required>
                                    <?php 
                                    echo '<option value="">Mes Inicial</option>';
                                    $vg = $con->Listar("SELECT numero, mes  
                                        FROM gf_mes 
                                        WHERE parametrizacionanno = $anno ORDER BY numero");
                                    for ($i = 0; $i < count($vg); $i++) {
                                       echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].'</option>'; 
                                    }                                    
                                    ?>
                                </select>
                                
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Final:</label>
                                <select name="mesF" id="mesF" class="select2_single form-control" title="Seleccione mes final" style="height: auto " required>
                                    <?php 
                                    echo '<option value="">Mes Final</option>';
                                    $vg = $con->Listar("SELECT numero, mes  
                                        FROM gf_mes 
                                        WHERE parametrizacionanno = $anno ORDER BY numero DESC");
                                    for ($i = 0; $i < count($vg); $i++) {
                                       echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].'</option>'; 
                                    }                                    
                                    ?>
                                </select>
                                
                            </div>
                            <div class="form-group">
                                <label for="sltExportar" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Exportar A</label>
                                <select required="required" name="sltExportar" id="sltExportar" class="form-control select2_single" title="Seleccione Exportar" >
                                    <option value="">Exportar</option>              
                                    <option value="1">csv</option>
                                    <option value="2">txt</option>
                                    <option value="3">xls</option>
                                </select>
                            </div> 
                            <div class="form-group" id="sep" style="display:none">
                                <label for="separador" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Separado Por</label>
                                <select name="separador" id="separador" class="form-control select2_single" title="Seleccione Separador">
                                    <option value="">Separador</option>              
                                    <option value=",">,</option>
                                    <option value=";">;</option>
                                    <option value="tab">Tab</option>
                                </select>
                            </div> 
                            <div align="center">
                                <button type="submit" class="btn btn-primary sombra" style="margin-top: 0px; margin-bottom: 10px; margin-left: -100px;" >Generar</button>
                            </div>
                        </form>
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
        </script>
        <script>
            $("#sltExportar").change(function(){
                console.log('df');
                var tipo = $("#sltExportar").val();
                if(tipo == 3){
                    $("#sep").css("display", "none");
                    $("#separador").prop("required", false);
                } else {
                    $("#sep").css("display", "block");
                    $("#separador").prop("required", true);
                }
            })
        </script>
    </body>
</html>
</html>

