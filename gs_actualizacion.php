<div class="modal fade" id="modalActualizar" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width:500px">
            <div id="forma-modal" class="modal-header">          
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Actualización</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;">
                    <button type="button" id="btnCerrarModalAct" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top:-10px">
                <div class="row">                    
                    <form class="form-horizontal" id="frmActualizar" name="frmActualizar" method="POST" enctype="multipart/form-data" action="json/registrarActualizacionJson.php">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>                        
                        <div>
                            <label for="txtObservaciones" class="control-label col-sm-2">
                                <strong class="obligado">*</strong>Observaciones:
                            </label>                                                        
                        </div>
                        <div>
                            <textarea name="txtObservaciones" class="form-control" id="txtObservaciones" rows="4" cols="20" style="width:470px"></textarea>                            
                        </div>
                        <div class="col-sm-2" style="margin-top:10px">
                            <input type="submit" class="btn btn-primary" value="Enviar">
                        </div>
                    </form>                                     
                </div>
            </div>
            <div id="forma-modal" class="modal-footer"></div>
        </div>
    </div>
</div>