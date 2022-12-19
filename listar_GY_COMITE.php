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
$row = $con->Listar("SELECT a.id_unico AS Comite,
        IF(CONCAT_WS(' ',
             t.nombreuno,
             t.nombredos,
             t.apellidouno,
             t.apellidodos)
             IS NULL OR CONCAT_WS(' ',
             t.nombreuno,
             t.nombredos,
             t.apellidouno,
             t.apellidodos) = '',
             (t.razonsocial),
             CONCAT_WS(' ',
             t.nombreuno,
             t.nombredos,
             t.apellidouno,
             t.apellidodos)) AS NOMBRE,
        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
             t.numeroidentificacion,
        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))  as NUMI,
            c.nombre AS TipoComite
    FROM gy_comite a LEFT JOIN gf_tercero t
        ON a.id_tercero = t.id_unico
            LEFT JOIN gy_tipo_comite c ON a.id_tipo = c.id_unico
    WHERE t.compania = $compania
    ORDER BY NOMBRE ASC");
?>

<title>Listar Comite</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Comite</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Tercero</strong></td>
                                    <td><strong>Tipo Comite</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tercero</th>
                                    <th>Tipo Comite</th>
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
                                            <a href="GY_COMITE.php?id=<?php echo md5($row[$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                                        <td><?php echo $row[$i][1]; ?></td>
                                        <td><?php echo $row[$i][3]; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="GY_COMITE.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>
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
                    <p>¿Desea eliminar el registro seleccionado de Comite?</p>
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
                    url: "jsonProyecto/gy_comiteJson.php",
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

