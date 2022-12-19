<div class="modal fade" id="mdlCaracteristica" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="forma-modal">
                <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Características de Espacio <span id="nEspacio"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form method="post" class="form-horizontal" id="form">
                        <input type="hidden" name="txtEspacio" id="txtEspacio">
                        <input type="hidden" name="txtContador" id="txtContador">
                        <div class="form-group clone-data">
                            <label for="sltTipoDato" class="col-sm-2 col-md-2 col-lg-2 control-label text-right">Tipo Dato:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltTipoDato" id="sltTipoDato" class="form-control" title="Seleccione tipo dato" style="font-size: 10px;" required onchange="cambiarCampoValor(0)">
                                    <?php
                                    $html  = "";
                                    $html .= "<option value=''>Tipo Dato</option>";
                                    foreach ($tipoDato as  $row){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="txtNombre" class="col-sm-1 col-md-1 col-lg-1 control-label text-right">Nombre:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtNombre" id="txtNombre" placeholder="Nombre" style="width: 100%; font-size: 10px;" class="form-control" autocomplete="off" title="Nombre de Característica" required>
                            </div>
                            <label for="txtValor" class="col-sm-1 col-md-1 col-lg-1 control-label text-right">Valor:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtValor" id="txtValor" title="Valor de Característica" placeholder="Valor" class="form-control" style="width: 100%; font-size: 10px;" autocomplete="off" required>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <a href="#" class="btn btn-primary borde-sombra" title="Nueva Característica" id="btnAdd"><span class="glyphicon glyphicon-plus"></span></a>
                            </div>
                        </div>
                        <div id="campos"></div>
                        <div class="form-group">
                            <label for="sltEspacios" class="col-sm-2 col-md-2 col-lg-2 text-right control-label">Espacios:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltEspacios" id="sltEspacios" class="select2 form-control" multiple data-placeholder="Espacios Habitables"></select>
                            </div>
                            <label for="chkTodos" class="col-sm-4 col-md-4 col-lg-4 control-label text-right">Aplicar caracteristicas a todos los espacios:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="checkbox" name="chkTodos" id="chkTodos" value="0">
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <a href="" class="btn btn-primary borde-sombra" title="Guardar Característica" id="btnGuardarCar"><span class="glyphicon glyphicon-floppy-disk"></span></a>
                            </div>
                        </div>
                    </form>
                    <div id="html"></div>
                </div>
            </div>
            <div class="modal-footer" id="forma-modal"></div>
        </div>
    </div>
</div>