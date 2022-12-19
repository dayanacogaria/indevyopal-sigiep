<?php


include ('head_listar.php');
include ('Conexion/conexion.php');
$compania = $_SESSION['compania'];
$nameOpt   ="";
$id_prede   = "";
$predece    = "";
$option     = "";
$id_option  = "";
$ruta       = "";
$estado     = "";
$orden      = "";
if(!empty($_GET['option'])) {
    $option = $_GET['option'];
    $sqlM = "SELECT    hijo.id_unico, hijo.nombre, padre.id_unico, 
        CONCAT_WS(' ',mpp.nombre,  padre.nombre), hijo.ruta, hijo.estado, hijo.orden  
             FROM gs_menu hijo
             LEFT JOIN gs_menu padre   ON hijo.predecesor = padre.id_unico 
             LEFT JOIN gs_menu mpp ON padre.predecesor = mpp.id_unico
             WHERE     (hijo.id_unico) = '$option'";
    $resultM = $mysqli->query($sqlM);
    $rowM = mysqli_fetch_row($resultM);
    $nameOpt    = $rowM[1];
    $id_prede   = $rowM[2];
    $predece    = $rowM[3];
    $id_option  = $rowM[0];
    $ruta       = $rowM[4];
    $estado     = $rowM[5];
    $orden      = $rowM[6];
}
?>
<title>Registrar Opciones de Menú</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>

</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php include ('menu.php');?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" style="margin-top: 0px; text-align: center">Registrar Opciones de Menú</h2>
                <div class="client-form contenedorForma">
                    <form action="controller/controllerGS_Menu.php?action=insert" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="margin-top: -5px; margin-left: 0px">
                            <div class="form-group form-inline  col-md-2 col-lg-2" text-aling="left">
                                <label for="txtNombreM" class="col-sm-12 control-label"><strong class="obligado">*</strong>Nombre Opción Menú:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:10px">
                                <input type="text" name="txtNombreM" id="txtNombreM" class="form-control col-md-1" style="width:100%" title="Ingrese Nombre Opción Menú" placeholder="Ingrese Nombre Opción Menú" required="required" value="<?php echo $nameOpt ?>">
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:0px">
                                <label for="txtRutaM" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Ruta de archivo de menú:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:10px">
                                <input type="text" name="txtRutaM" id="txtRutaM" class="form-control col-md-1" style="width:100%" title="Ingrese Ruta de archivo de menú" placeholder="Ingrese Ruta de archivo de menú"  value="<?php echo $ruta ?>">
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:5px">
                                <label for="sltMenuPadre" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Opción de menu padre:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:5px">
                                <select name="sltMenuPadre" id="sltMenuPadre" class="form-control select2" title="Seleccione opción de menu padre" style="height: auto; width: 100%" >
                                     <?php
                                    if(empty($id_prede)) {
                                        echo "<option value=\"\">Opción de menú padre</option>";
                                        $sql = "SELECT m.id_unico, CONCAT_WS(' * ',LOWER(mp2.nombre), 
                                            LOWER(mp1.nombre), 
                                            LOWER(mp.nombre), 
                                            LOWER(m.nombre)) 
                                            FROM gs_menu m 
                                            LEFT JOIN gs_menu mp ON m.predecesor = mp.id_unico 
                                            LEFT JOIN gs_menu mp1 ON mp.predecesor = mp1.id_unico 
                                            LEFT JOIN gs_menu mp2 ON mp1.predecesor = mp2.id_unico 
                                           ";
                                    }else{
                                        echo "<option value=\"".$id_prede."\">".($predece)."</option>";
                                        $sql = "SELECT m.id_unico, 
                                            CONCAT_WS(' * ',LOWER(mp2.nombre), 
                                            LOWER(mp1.nombre), 
                                            LOWER(mp.nombre), 
                                            LOWER(m.nombre)) 
                                            FROM gs_menu m 
                                            LEFT JOIN gs_menu mp ON m.predecesor = mp.id_unico 
                                            LEFT JOIN gs_menu mp1 ON mp.predecesor = mp1.id_unico 
                                            LEFT JOIN gs_menu mp2 ON mp1.predecesor = mp2.id_unico 
                                            WHERE m.id_unico != $id_prede ";
                                    }
                                    $result = $mysqli->query($sql);
                                    while ($row = mysqli_fetch_row($result)) {
                                        echo "<option value=\"".$row[0]."\">".($row[1])."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                        </div>
                        <div class="form-group form-inline" style="margin-top: -5px; margin-left: 0px">
                            <div class="form-group form-inline  col-md-2 col-lg-2" text-aling="left">
                                <label for="txtOrden" class="col-sm-12 control-label"><strong class="obligado">*</strong>Orden:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:10px">
                                <input type="text" name="txtOrden" id="txtOrden" class="form-control col-md-1" style="width:100%" title="Ingrese Orden" placeholder="Ingrese Orden" required="required" value="<?php echo $orden ?>" autocomplete="off">
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:5px">
                                <label for="sltEstado" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Estado:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:5px">
                                <select name="sltEstado" id="sltEstado" class="form-control select2" title="Seleccione Estado" style="height: auto; width: 100%" required="required">
                                    <?php
                                    if(empty($estado)) {
                                        echo "<option value=\"1\">Activo</option>";
                                        echo "<option value=\"2\">Inactivo</option>";
                                    }else{
                                        if($estado == 1){
                                            echo "<option value=\"1\">Activo</option>";
                                            echo "<option value=\"2\">Inactivo</option>";
                                        } else {
                                            echo "<option value=\"2\">Inactivo</option>";
                                            echo "<option value=\"1\">Activo</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:5px">
                                <a id="btnNuevo" href="javascript:void(0)" class="btn btn-primary glyphicon glyphicon-plus shadow nuevo" style="margin-bottom: 5px;" onclick="clean_url()" title="Registrar nueva opción de menú"></a>
                                <button id="btnGuardar" type="submit" class="btn btn-primary glyphicon glyphicon-floppy-disk shadow guardar" style="margin-bottom: 5px;" title="Guardar opción de menú"></button>
                                <a href="javascript:void(0)" id="btnModificar" title="Modificar opción de menú" class="btn btn-primary shadow glyphicon glyphicon-pencil modificar" onclick="save_changes('<?php echo $id_option; ?>')" style="margin-bottom: 5px"></a>                            
                                <?php
                                if(empty($_GET['option'])) {
                                    echo "\n\t<script>";
                                    echo "\n\t\t$(\"#btnNuevo, #btnModificar\").attr('disabled',true);";
                                    echo "\n\t\t$(\"#btnNuevo, #btnModificar\").removeAttr('onclick');";
                                    echo "\n\t</script>";
                                }else{
                                    echo "\n\t<script>";
                                    echo "\n\t\t$(\"#btnGuardar\").attr('disabled',true);";
                                    echo "\n\t\t$(\"#btnGuardar\").removeAttr('onclick');";
                                    echo "\n\t</script>";
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="table-responsive">
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <td class="oculto"></td>
                            <td width="7%"></td>
                            <td><strong>Opción de Menú</strong></td>
                            <td><strong>Ruta</strong></td>
                            <td><strong>Predecesor</strong></td>
                            <td><strong>Estado</strong></td>
                            <td><strong>Orden</strong></td>
                        </tr>
                        <tr>
                            <th class="oculto"></th>
                            <th width="7%"></th>
                            <th>Opción de Menú</th>
                            <th>Ruta</th>
                            <th>Predecesor</th>
                            <th>Estado</th>
                            <th>Orden</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql__T = "SELECT m.id_unico, m.nombre,m.ruta, ma.nombre,IF(m.estado=1, 'Activo', 'Inactivo'),  
                            m.orden
                            FROM gs_menu m 
                            LEFT JOIN gs_menu ma ON m.predecesor = ma.id_unico 
                            ORDER BY CAST(m.orden as unsigned), ma.id_unico";
                        $result__T = $mysqli->query($sql__T);
                        while ($row__T = mysqli_fetch_row($result__T)) {
                            echo '<tr>
                            <td class="oculto"></td>
                            <td>
                            <a href="javascript:void(0)" class="eliminar glyphicon glyphicon-trash" onclick="delete_option('.$row__T[0].')"></a>
                            <a href="javascript:void(0)" class="modificar glyphicon glyphicon-edit" onclick="modify_data('.($row__T[0]).')"></a>
                            </td>
                            <td>'.(($row__T[1])).'</td>
                            <td>'.$row__T[2].'</td>
                            <td>'.(($row__T[3])).'</td>
                            <td>'.$row__T[4].'</td>
                            <td>'.$row__T[5].'</td>
                            </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
            
            <?php include ('footer.php');?>
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlEliminado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" onclick="reload_page()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNoeliminado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlModificado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModifico" class="btn" onclick="reload_page()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNomodificado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido modificar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoModifico" class="btn" onclick="reload_page()" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2();

        function modify_data(row) {
            window.location = 'registrar_GS_MENU.php?option='+row;
        }

        function clean_url() {
            window.location = 'registrar_GS_MENU.php';
        }

        function delete_option(son) {
            var form_data = {
                son:son,
                action:'delete'
            };

            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $.ajax({
                    type:"POST",
                    url:"controller/controllerGS_Menu.php",
                    data:form_data,
                    success: function (data, textStatus, jqXHR) {
                        result = JSON.parse(data);
                        if(result == true) {
                            $("#mdlEliminado").modal("show");
                        }else{
                            $("#mdlNoeliminado").modal("show");
                        }
                    }
                }).error(function (data,textError, jqXHR) {

                });
            });
        }

        function reload_page() {
            window.location.reload();
        }

        function save_changes(id_option) {
            var nombre  = $("#txtNombreM").val();
            var ruta    = $("#txtRutaM").val();
            var npadre  = $("#sltMenuPadre").val();
            var orden   = $("#txtOrden").val();
            var estado  = $("#sltEstado").val();
            
            var form_data = {
                id_unico:id_option,
                nombre:nombre,
                ruta:ruta,
                npadre:npadre,
                orden:orden,
                estado:estado,
                action:'modify'
            };
            var result = '';
            $.ajax({
                type:"POST",
                url:"controller/controllerGS_Menu.php",
                data:form_data,
                success: function (data, textStatus, jqXHR) {
                    console.log(data);
                    result = JSON.parse(data);
                    if(result == true) {
                        $("#mdlModificado").modal('show');
                    } else {
                        $("#mdlNomodificado").modal('show');
                    }
                }
            }).error(function (jqXHR, textStatus, errorThrown) {
                alert('XHR :'+jqXHR+' - textStatus :'+textStatus+' - errorThrown :'+errorThrown);
            });
        }

        function open_modal_asociado(id) {
            var form_data = {
                opcion:id
            };

            $.ajax({
                type:"POST",
                url:"modalMenuAsociado.php#modalMenuAsociado",
                data:form_data,
                success:function (data, textStatus, jqXHR) {
                    $("#modalMenuAsociado").html(data);
                    $(".aso").modal("show");
                }
            });
        }
    </script>
    <?php require ('modalMenuAsociado.php');?>
</body>
</html>