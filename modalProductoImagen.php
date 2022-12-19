<?php require_once './Conexion/conexion.php';
require_once './Conexion/ConexionPDO.php';
$con = new ConexionPDO();
?>
<div class="modal fade modalIm" id="modalImagen" role="dialog" align="center" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="height:600px;width:900px">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Imágenes</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="client-form contenedorForma" style="margin-top:-10px;margin-right:-3px">
                            <form id="formImagen" name="formImagen" action="" class="form-horizontal" style="margin-left:25px">
                            <input type="hidden" id="txtProducto" name="txtProducto" value="<?php echo $_REQUEST['producto']?>"/>                 
                            <div class="form-group form-inline" style="">
                                <label for="txtlabel" class="col-sm-2 control-label"><strong class="obligado">*</strong>Subir Imágen:</label>
                                <input type="file" id="file" name="file"  accept="image/png, image/jpeg, image/jpg" class="col-sm-2 input-sm"  style="display: inline; height: 35px;  width: 250px" required="required">
                                <a onclick="return guardarImagen()" class="btn btn-primary sombra" style="margin-top:1px; margin-left:-90px"><li class="glyphicon glyphicon-floppy-disk"></li></a>
                            </div>
                            </form>         
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?php #* Buscar Productos
                        $row = $con->Listar("SELECT ruta, id_unico  FROM gf_imagen_producto 
                            WHERE producto =".$_REQUEST['producto']);
                        for ($i = 0; $i < count($row); $i++) {
                            echo '<div class="form-group form-inline col-sm-2" >
                                <div class="col-sm-offset-11" >
                                    <button type="button" onclick= "return eliminarImagen('.$row[$i][1].','.$_REQUEST['producto'].')"class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                                </div>
                                <image src="documentos/imagenes_producto/'.$row[$i][0].'" style="width: 100%;"/>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
