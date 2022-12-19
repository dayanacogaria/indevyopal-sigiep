<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$query = "SELECT g.id_unico, g.nombre, m.nombre, g.video , g.modulo
    FROM gs_guias g 
    LEFT JOIN gs_modulos m ON g.modulo = m.id_unico 
    WHERE g.video IS NOT NULL";
$resultado = $mysqli->query($query);
$query1 = "SELECT id_unico, nombre
           FROM gs_modulos  
            ORDER BY nombre ASC";
$modulos = $mysqli->query($query1);

?>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<title>Subir Videos</title>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px;">Subir Videos</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GS_VIDEOJson.php">
                        <p align="center" style=" margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="margin-left:0px; margin-top:-10px; margin-right: 0px">
                            <div class="form-group form-inline"style="margin-left:-5px; margin-top: 0px" >
                                <label style="width:100px; margin-bottom:8px; display: inline-block;" for="modulo" class="control-label" ><strong style="color:#03C1FB;">*</strong>Módulo:</label>
                                <select style="width:180px;margin-bottom: -1px;display: inline-block;" name="modulo" id="modulo" required="required" class="select2_single form-control" title="Seleccione módulo" >
                                    <option value="">Módulo</option>
                                    <?php while ($rowM = mysqli_fetch_row($modulos)) { ?>
                                        <option value="<?php echo $rowM[0] ?>"><?php echo ucwords((mb_strtolower($rowM[1])));
                                } ?></option>;
                                </select>

                            </div>
                            <div class="form-group form-inline"style="margin-top: 10px" >                         
                                <div id="numeroR" style="display:inline;margin-left:0px;" >
                                    <label for="nombre" style="width:100px; margin-bottom: 20px;" class="control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                    <input type="text"  name="nombre" id="nombre" title="Ingrese el nombre de la Guía" class="form-control"  style=" display: inline; width:200px;width:300px" required >
                                </div>                            
                            </div>
                            <div class="form-group form-inline" style="margin-left:50px; margin-top: 11px" >
                                <input type="hidden" required="required" title="Seleccione Documento" id="archivos" name="archivos" required>
                                <input id="file" name="file" type="file"  style="height: 35px;" >
                            </div>
                            <div class="form-group form-inline" style="margin-left:30px; margin-top: 0px">
                                <button id="guardar" type="submit" class="btn btn-primary sombra" title="Guardar" style="margin-left:8px; margin-top: 15px"> <i class="glyphicon glyphicon-floppy-disk" ></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">  
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px" align="center"></td>
                                        <td><strong>Módulo</strong></td>
                                        <td><strong>Nombre Video</strong></td>
                                        <td><strong>Ver</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Módulo</th>
                                        <th>Nombre Guía</th>
                                        <th>Ver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_row($resultado)) { ?>
                                        <tr>
                                            <td style="display: none;"><?php echo $row[0] ?></td>
                                            <td> 
                                                <a  href="#" onclick="javascript:eliminarguia(<?php echo $row[0] . ',' . "'" . $row[3] . "'"; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            </td>
                                            <td><?php echo $row[2]; ?></td>
                                            <td><?php echo $row[1]; ?></td>
                                            <td><a href="<?php echo $row[3] ?>" target="_blank"><i class="glyphicon glyphicon-search"></a></td>
                                        </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
    <!--Mensajes Eliminar-->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado ?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2_single").select2();
    </script>
    <script type="text/javascript">
        function eliminarguia(id, ruta) {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $("#mymodal").modal('hide');
                $.ajax({
                    type: "GET",
                    url: "json/eliminar_GS_GUIAJson.php?id=" + id + "&ruta=" + ruta,
                    success: function (data) {
                        result = JSON.parse(data);
                        console.log(result);
                        if (result == true) {
                            $("#myModal1").modal('show');
                            $('#ver1').click(function () {
                                document.location.reload();
                            });
                        } else {
                            $("#myModal2").modal('show');
                            $('#ver2').click(function () {
                                document.location.reload();
                            });
                        }
                    }
                });
            });
        }
    </script>
</body>
</html>


