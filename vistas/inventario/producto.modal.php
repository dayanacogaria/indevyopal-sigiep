<div class="modal fade" id="mdlProductos" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Asignación de hijos</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <form class="client-form" id="frmPro">
                    <input type="hidden" name="txtTarifa" id="txtTarifa">
                    <div class="row">
                        <div class="form-group">
                            <label for="" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Elementos:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="sltElementos" id="sltElementos" class="select2 form-control" multiple title="Seleccione los elementos que se son hijos" placeholder="Elementos">
                                    <option value="">Elementos</option>
                                </select>
                            </div>
                            <label for="" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Cantidad</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtCantidad" id="txtCantidad" class="form-control" style="width: 100%;" placeholder="Cantidad" value="1">
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 text-right">
                                <button type="button" id="btnRegistroElementos" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                            </div>
                        </div>
                    </div>
                </form>
                <br/>
                <div class="form-group">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered table-highlight display" id="tblLsPro">
                            <thead>
                            <tr>
                                <th>Elemento</th>
                                <th>Cantidad</th>
                                <th style="width: 5%;"></th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer"></div>
        </div>
    </div>
</div>