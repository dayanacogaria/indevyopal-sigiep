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
        <title>Consolidar</title>
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
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Consolidar</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()" >  
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
                            <div class="form-group" style="margin-top: -5px">
                                <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tercero Inicial:</label>
                                <select name="terceroI" id="terceroI" class="select2_single form-control" title="Seleccione Tercero Inicial" style="height: auto " required>
                                    <?php 
                                    echo '<option value="">Tercero Inicial</option>';
                                    $vg = $con->Listar("SELECT DISTINCT c.compania, 
                                        t.razonsocial, 
                                        t.numeroidentificacion, 
                                        t.digitoverficacion 
                                        FROM gf_consolidacion c 
                                        LEFT JOIN gf_tercero t ON c.compania = t.id_unico  
                                        WHERE c.consolidado = 1 
                                        ORDER BY c.compania ASC");
                                    for ($i = 0; $i < count($vg); $i++) {
                                       echo '<option value="'.$vg[$i][0].'">'.$vg[$i][0].' - '.$vg[$i][1].' '.$vg[$i][2].' - '.$vg[$i][3].'</option>'; 
                                    }                                    
                                    ?>
                                </select>
                                
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tercero Final:</label>
                                <select name="terceroF" id="terceroF" class="select2_single form-control" title="Seleccione Tercero final" style="height: auto " required>
                                    <?php 
                                    echo '<option value="">Tercero Final</option>';
                                    $vg = $con->Listar("SELECT DISTINCT c.compania, 
                                        t.razonsocial, 
                                        t.numeroidentificacion, 
                                        t.digitoverficacion 
                                        FROM gf_consolidacion c 
                                        LEFT JOIN gf_tercero t ON c.compania = t.id_unico  
                                        WHERE c.consolidado = 1 
                                        ORDER BY c.compania DESC"); 
                                    for ($i = 0; $i < count($vg); $i++) {
                                       echo '<option value="'.$vg[$i][0].'">'.$vg[$i][0].' - '.$vg[$i][1].' '.$vg[$i][2].' - '.$vg[$i][3].'</option>'; 
                                    }                                        
                                    ?>
                                </select>
                                
                            </div>
                            <div class="form-group" style="margin-top: 20px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
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
            function guardar(){
                jsShowWindowLoad('Guardado Consolidado');
                var formData = new FormData($("#form")[0]);
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_distribucion_costosJson.php?action=18",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    {
                        jsRemoveWindowLoad();
                        console.log(response);
                        if(response>0){ 
                            $("#mensaje").html('Información guardada correctamente');
                            $("#modalMensajes").modal("show");
                        } else {
                            $("#mensaje").html('No se ha podido guardar la Información');
                            $("#modalMensajes").modal("show");
                        }
                    }
                })
            }
        </script>
    </body>
</html>
</html>

