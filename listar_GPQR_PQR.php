<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#13/12/2018 | Nestor B. | Arreglar Código
####/################################################################################

require_once 'head_listar.php';
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
$con    = new ConexionPDO();
$compania   = $_SESSION['compania'];
$sql = "SELECT DISTINCT(pqr.id_unico), 
                            pqr.fecha_hora,
                            e.nombre,
                            pqr.observaciones, 
                            af.nombre,
                            pqr.id_factura
                    FROM gpqr_pqr pqr 
                    LEFT JOIN gpqr_afavor af ON pqr.id_afavor = af.id_unico
                    LEFT JOIN gpqr_estado e ON pqr.id_estado_pqr = e.id_unico
                    WHERE pqr.compania = '$compania'
                    ORDER BY pqr.fecha_hora DESC";

$res = $mysqli->query($sql);
?>

<title>PQR</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">PQR</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Tercero</strong></td>
                                    <td><strong>Unidad de Vivienda</strong></td>
                                    <td><strong>Fecha y Hora</strong></td>
                                    <td><strong>Estado</strong></td>
                                    <td><strong>Ultima Factura</strong></td>
                                    <td><strong>A Favor</strong></td>
                                    <td><strong>Observaciones</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tercero</th>
                                    <th>Unidad de Vivienda</th>
                                    <th>Fecha y Hora</th>
                                    <th>Estado</th>
                                    <th>Ultima Factura</th>
                                    <th>A Favor</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php   while($rowPQR = mysqli_fetch_row($res)) {    ?>
                                            <tr>
                                                <td style="display: none;"></td>
                                                <td><a href="#"
                                                       onclick="javascript:eliminar(<?php echo $rowPQR[0]; ?>);">
                                                        <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                    </a>
                                                    <a href="GPQR_PQR.php?id=<?php echo $rowPQR[0]; ?>&mod=1"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                                                <?php
                                                    $sqlT = "SELECT tr.id_unico,
                                                                    IF(CONCAT_WS(' ',
                                                                        tr.nombreuno,
                                                                        tr.nombredos,
                                                                        tr.apellidouno,
                                                                        tr.apellidodos) 
                                                                    IS NULL OR CONCAT_WS(' ',
                                                                        tr.nombreuno,
                                                                        tr.nombredos,
                                                                        tr.apellidouno,
                                                                        tr.apellidodos) = '',
                                                                        (tr.razonsocial),
                                                                    CONCAT_WS(' ',
                                                                        tr.nombreuno,
                                                                        tr.nombredos,
                                                                        tr.apellidouno,
                                                                        tr.apellidodos)) AS NOMBRE
                                                            FROM gf_tercero tr
                                                            LEFT JOIN gpqr_pqr p ON p.id_tercero = tr.id_unico
                                                            WHERE p.id_unico = '$rowPQR[0]'";

                                                    $resT = $mysqli->query($sqlT);
                                                    $rowT = mysqli_fetch_row($resT);
                                                ?>
                                                <td><?php echo $rowT[1]; ?></td>

                                                <?php
                                                    $Cod_Cat  = "SELECT uvms.id_unico,
                                                                    p.codigo_catastral,
                                                                    IF(CONCAT_WS(' ',
                                                                         tr.nombreuno,
                                                                         tr.nombredos,
                                                                         tr.apellidouno,
                                                                         tr.apellidodos) 
                                                                         IS NULL OR CONCAT_WS(' ',
                                                                         tr.nombreuno,
                                                                         tr.nombredos,
                                                                         tr.apellidouno,
                                                                         tr.apellidodos) = '',
                                                                         (tr.razonsocial),
                                                                         CONCAT_WS(' ',
                                                                         tr.nombreuno,
                                                                         tr.nombredos,
                                                                         tr.apellidouno,
                                                                         tr.apellidodos)) AS NOMBRE,
                                                                         s.nombre

                                                            FROM gp_unidad_vivienda_medidor_servicio uvms
                                                            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico
                                                            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico
                                                            LEFT JOIN  gp_predio1 p ON uv.predio = p.id_unico
                                                            LEFT JOIN  gp_sector s ON uv.sector = s.id_unico
                                                            LEFT JOIN  gf_tercero tr ON uv.tercero = tr.id_unico
                                                            LEFT JOIN gpqr_pqr pqr ON pqr.id_unidad_vivienda = uvms.id_unico
                                                            WHERE pqr.id_unico = '$rowPQR[0]'";
                                                  
                                                    $CodC = $mysqli->query($Cod_Cat);
                                                    $rowUV = mysqli_fetch_row($CodC);
                                                ?>
                                                <td><?php echo $rowUV[1].' - '.$rowUV[2].' - '.$rowUV[3]; ?></td>
                                                <?php

                                                    $newDate = date("d/m/Y h:m:s", strtotime($rowPQR[1]));
                                                ?>
                                                <td align="center"><?php echo $newDate; ?></td>
                                                <td><?php echo $rowPQR[2]; ?></td>

                                                <?php
                                                    $sqlUF = "SELECT   uf.id_unico,
                                                                        uf.numero_factura
                                                            FROM gp_factura uf 
                                                            LEFT JOIN gpqr_pqr p ON p.id_factura = uf.id_unico
                                                            WHERE p.id_factura = '$rowPQR[5]'";

                                                    $resUF = $mysqli->query($sqlUF);
                                                    $rowUF = mysqli_fetch_row($resUF);
                                                ?>

                                                <td align="right"><?php echo $rowUF[1]; ?></td>
                                                <td><?php echo $rowPQR[4]; ?></td>
                                                <td><?php echo $rowPQR[3]; ?></td>
                                            </tr>
                                <?php   } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="GPQR_PQR.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>
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
                    <p>¿Desea eliminar el registro seleccionado?</p>
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
                    url: "jsonPQR/gpqr_pqrJson.php?action=1",
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

