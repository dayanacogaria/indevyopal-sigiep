<div class="modal fade" id="mdlTercero" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="forma-modal">
                <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Tercero</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form action="<?php echo "access.php?controller=Punto&action=registrarTercero" ?>" method="post" class="form-horizontal" id="formTercero" enctype="multipart/form-data" style="font-size: 10px !important;">
                        <p align="center" style="margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <input type="hidden" name="txtUrl" value="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                        <div class="form-group">
                            <label for="txtRazonSocial" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Razón Social:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtRazonSocial" id="txtRazonSocial" class="form-control" maxlength="100" title="Ingrese razón social" onkeypress="return txtValida(event,'car')" placeholder="Razón Social" required="" style="width: 100%;font-size: 10px !important;" tabindex="1" autocomplete="off">
                            </div>
                            <label for="txtNombreComercial" class="control-label col-sm-3 col-md-3 col-lg-3 text-right">Nombre Comercial:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtNombreComercial" id="txtNombreComercial" class="form-control" maxlength="500" title="Ingrese el nombre comercial" onkeypress="return txtValida(event,'car')" placeholder="Nombre Comercial" style="width: 100%; font-size: 10px !important;" tabindex="2" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtPrimerNombre" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Primer Nombre:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtPrimerNombre" id="txtPrimerNombre" class="form-control" maxlength="100" title="Ingrese el primer nombre" onkeypress="return txtValida(event,'car')" placeholder="Primer Nombre" required="" style="width: 100%;font-size: 10px !important;" tabindex="3" autocomplete="off">
                            </div>
                            <label for="txtSegundoNombre" class="control-label col-sm-3 col-md-3 col-lg-3 text-right">Segundo Nombre:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtSegundoNombre" id="txtSegundoNombre" class="form-control" maxlength="100" title="Ingrese el segundo nombre" onkeypress="return txtValida(event,'car')" placeholder="Segundo Nombre" style="width: 100%; font-size: 10px !important;" tabindex="4" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtPrimerApellido" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Primer Apellido:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtPrimerApellido" id="txtPrimerApellido" class="form-control" maxlength="100" title="Ingrese el primer apellido" onkeypress="return txtValida(event,'car')" placeholder="Primer Apellido" required style="width: 100%;font-size: 10px !important;" tabindex="5" autocomplete="off">
                            </div>
                            <label for="txtSegundoApellido" class="control-label col-sm-3 col-md-3 col-lg-3 text-right">Segundo Apellido:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtSegundoApellido" id="txtSegundoApellido" class="form-control" maxlength="100" title="Ingrese el segundo apellido" onkeypress="return txtValida(event,'car')" placeholder="Segundo Apellido" style="width: 100%; font-size: 10px !important;" tabindex="6" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltTipoIdent" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo Identificación:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="sltTipoIdent" id="sltTipoIdent" class="form-control select" title="Seleccione tipo identificación" required tabindex="7">
                                    <option value="">Tipo Identificación</option>
                                    <?php
                                    $html = "";
                                    foreach($tipoIdent as $row){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="txtNumeroI" class="col-sm-3 col-md-3 col-lg-3 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtNumeroI" id="txtNumeroI" class="form-control" maxlength="100" title="Ingrese número identificación" onkeypress="return txtValida(event,'num')" placeholder="Número Identificación" required style="width: 100%; font-size: 10px !important;" tabindex="8" autocomplete="off">
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-1">
                                <input type="text" name="txtDigito" id="txtDigito" class="form-control" maxlength="100" title="Ingrese digito de verificación" onkeypress="return txtValida(event,'num')" placeholder="Digito" style="width: 100%; font-size: 10px !important;" tabindex="9" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtDireccion" class="col-sm-2 col-md-2 col-lg-2 control-label">Dirección Residencia:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtDireccion" id="txtDireccion" class="form-control" style="width: 100%; font-size: 10px;" title="Ingrese dirección de residencia" placeholder="Dirección" tabindex="10" autocomplete="off">
                            </div>
                            <label for="sltDepto" class="col-sm-3 col-md-3 col-lg-3 control-label"><strong style="color:#03C1FB;">*</strong>Departamento:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="sltDepto" id="sltDepto" class="form-control select" title="Seleccione departamento" required tabindex="11">
                                    <option value="">Departamento</option>
                                    <?php
                                    $html = "";
                                    foreach($departs as $row){
                                        $html .= "<option value='$row[0]'>".ucwords(mb_strtolower($row[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltCiudad" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong style="color:#03C1FB;">*</strong>Ciudad:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="sltCiudad" id="sltCiudad" class="form-control select" title="Seleccione ciudad" required tabindex="12">
                                    <option value="">Ciudad</option>
                                </select>
                            </div>
                            <label for="txtNumeroC" class="col-sm-3 col-md-3 col-lg-3 control-label"><strong style="color:#03C1FB;">*</strong>Número Celular:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="txtNumeroC" id="txtNumeroC" class="form-control" maxlength="100" title="Ingrese número celular" placeholder="Número Celular" required style="width: 100%; font-size: 10px" tabindex="13" autocomplete="off">
                            </div>
                        </div> 
                        <div class="form-group">
                            <label for="SltEmpresa" class="col-sm-2 col-md-2 col-lg-2 control-label">Representante Legal:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="sltRepresentante" id="sltRepresentante" class="form-control select" title="Seleccione representante legal" tabindex="14">
                                    <?php
                                    $html = "";
                                    $html .= "<option value=''>Representante Legal</option>";
                                    foreach ($terceros as $row){
                                        $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="txtEmail" class="control-label col-sm-3 col-md-3 col-lg-3 text-right">Email:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="email" name="txtEmail" id="txtEmail" class="form-control" maxlength="100" title="Ingrese email" placeholder="Email" style="width: 100%; font-size: 10px !important;" tabindex="4" autocomplete="off">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" id="forma-modal">
                <div class="row">
                    <div class="form-group">
                        <label for="no" class="col-sm-11 col-md-11 col-lg-11 control-label"></label>
                        <div class="col-sm-1 col-md-1 col-lg-1 text-right">
                            <button type="submit" class="btn btn-default" id="btnModalGuardarT"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>