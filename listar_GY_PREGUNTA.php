<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#16/04/2018 | Erica G. | Arreglar Código
####/################################################################################
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes
require_once 'head_listar.php';
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
$con    = new ConexionPDO();
$compania   = $_SESSION['compania'];
$row = $con->Listar("SELECT a.id_unico, a.nombre, b.nombre,tp.nombre
        FROM gy_pregunta a 
        LEFT JOIN gy_clase_pregunta b ON a.id_clase_pregunta = b.id_unico
        LEFT JOIN gy_tipo_pregunta tp ON a.id_tipo_pregunta = tp.id_unico
            
            WHERE a.compania = $compania
        ORDER BY a.nombre ASC");
?>

<title>Listar Pregunta</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Pregunta</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Clase Pregunta</strong></td>
                                    <td><strong>Tipo Pregunta</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>
                                    <th>Clase Pregunta</th>
                                    <th>Tipo Pregunta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                    <tr>
                                        <td style="display: none;"></td>
                                        <td><a href="#"
                                               onclick="javascript:eliminar(<?php echo $row[$i][0]; ?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="GY_PREGUNTA.php?id=<?php echo md5($row[$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                                        <td><?php echo $row[$i][1]; ?></td>
                                        <td><?php echo $row[$i][2]; ?></td>
                                        <td><?php echo $row[$i][3]; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="GY_PREGUNTA.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                    <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function eliminar(id) {
            $("#myModal").modal('show');
            $("#ver").click(function(){
                jsShowWindowLoad('Eliminando Datos ...');
                $("#mymodal").modal('hide');
                var form_data = {action:1, id:id};
                $.ajax({
                    type: 'POST',
                    url: "jsonProyecto/gy_preguntaJson.php?action=1",
                    data: form_data,
                    success: function(response) {
                        jsRemoveWindowLoad();
                        console.log(response);
                        if(response==1){
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location.reload();
                            })
                        } else {
                            $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                 $("#modalMensajes").modal("hide");
                            })
                        }
                    }
                });
            });
        }
    </script>
    <?php require_once 'footer.php' ?>
</body>
</html>

