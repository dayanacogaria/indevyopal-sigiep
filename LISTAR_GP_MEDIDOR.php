<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$query = "SELECT m.id_unico, "
              . "m.referencia, "
              . "mr.nombre, "
              . "m.nro_digitos, "
              . "m.fecha_instalacion, "
              . "m.macromedidor, "
              . "m.es_macromedidor, "
              . "tm.nombre, "
              . "pm.nombre, "
              . "m.certificado_calibracion, em.nombre "
              . "FROM gp_medidor m LEFT JOIN gp_marca mr ON m.marca = mr.id_unico "
              . "LEFT JOIN gp_tipo_medidor tm ON tm.id_unico=m.tipo_medidor "
              . "LEFT JOIN gp_posicion_medidor pm ON m.posicion_medidor = pm.id_unico "
              . "LEFT JOIN gp_estado_medidor em ON m.estado_medidor = em.id_unico"; 
$resultado = $mysqli->query($query);

?>
<title>Listar Medidor</title>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Medidor</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Referencia</strong></td>
                                    <td><strong>Marca</strong></td>
                                    <td><strong>N° dígitos</strong></td>
                                    <td><strong>Fecha instalación</strong></td>
                                    <td><strong>Macromedidor</strong></td>
                                    <td><strong>¿Es macromedidor?</strong></td>
                                    <td><strong>Posición medidor</strong></td>
                                    <td><strong>Certificado calibración</strong></td>
                                    <td><strong>Tipo medidor</strong></td>
                                    <td><strong>Estado Medidor</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th><strong>Referencia</strong></th>
                                    <th><strong>Marca</strong></th>
                                    <th><strong>N° dígitos</strong></th>
                                    <th><strong>Fecha instalación</strong></th>
                                    <th><strong>Macromedidor</strong></th>
                                    <th><strong>¿Es macromedidor?</strong></th>
                                    <th><strong>Posición medidor</strong></th>
                                    <th><strong>Certificado calibración</strong></th>
                                    <th><strong>Tipo medidor</strong></th>
                                    <th><strong>Estado Medidor</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = mysqli_fetch_row($resultado)){?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td>
                                      <a  href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                      <a href="Modificar_GP_MEDIDOR.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo mb_strtoupper(($row[1]));?></td>
                                    <td><?php echo ucwords(mb_strtolower(($row[2])));?></td>
                                    <td><?php echo $row[3];?></td>
                                    <td><?php if($row[4]=='""'||$row[4]==NULL || $row[4]=='0000-00-00'){ ?>
                                        <?php echo ''; } else { ?><?php echo date("d-m-Y", strtotime($row[4]));}?></td>
                                    <td>
                                        <?php
                                            if(empty($row[5])){
                                                echo '';
                                                
                                            }else {
                                                $mac = "Select referencia FROM gp_medidor WHERE id_unico = $row[5]";
                                                $macr = $mysqli->query($mac);
                                                $macrom= mysqli_fetch_row($macr);
                                                echo ucwords(mb_strtolower($macrom[0]));
                                            }?>
                                    </td>
                                    <td>
                                        <?php if ($row[6]==1){ 
                                          echo 'Si';  
                                        } else {
                                            echo 'No';
                                        }?>
                                    </td>
                                    <td><?php echo ucwords(mb_strtolower(($row[8])));;?></td>
                                    <td><?php echo (mb_strtoupper(($row[9])));;?></td>
                                    <td><?php echo ucwords(mb_strtolower(($row[7])));;?></td>
                                    <td><?php echo ucwords(mb_strtolower(($row[10])));;?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="Registrar_GP_MEDIDOR.php" class="btn btn-primary" 
                            style="box-shadow: 0px 2px 5px 1px gray;color: #fff; 
                            border-color: #1075C1; margin-top: 20px; 
                            margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> 
                        </div>       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Eliminar. -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de medidor?</p>
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
                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
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
    <script type="text/javascript">
        function eliminar(id)
        {
           var result = '';
           $("#myModal").modal('show');
           $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminar_GP_MEDIDORJson.php?id="+id,
                    success: function (data) {
                    result = JSON.parse(data);
                    if(result==true)
                        $("#myModal1").modal('show');
                   else
                        $("#myModal2").modal('show');
                    }
                });
            });
        }
    </script>
    <script type="text/javascript">
        function modal()
        {
           $("#myModal").modal('show');
        }
    </script>
    <script type="text/javascript">

        $('#ver1').click(function(){
          document.location = 'LISTAR_GP_MEDIDOR.php';
        });

    </script>
    <script type="text/javascript">

        $('#ver2').click(function(){
          document.location = 'LISTAR_GP_MEDIDOR.php';
        });

    </script>
</body>


