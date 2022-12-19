<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');

//Consulta Listar
$query = "SELECT p.id_unico, "
        . "p.estado, "
        . "ep.nombre, "
        . "p.tipo_proceso, "
        . "tp.identificador, "
        . "tp.nombre, "
        . "p.tercero, "
        . "CONCAT(t.nombreuno, ' ', t.nombredos,' ', t.apellidouno,' ', t.apellidodos, '(',t.numeroidentificacion, ')') AS TERCERO, "
        . "p.proceso, "
        . "epp.nombre, "
        . "tpp.identificador, "
        . "tpp.nombre, p.fecha, p.identificador "
        . "FROM gg_proceso p  "
        . "LEFT JOIN gg_estado_proceso ep ON p.estado = ep.id_unico "
        . "LEFT JOIN gg_tipo_proceso tp ON tp.id_unico = p.tipo_proceso "
        . "LEFT JOIN gf_tercero t ON p.tercero = t.id_unico "
        . "LEFT JOIN gg_proceso pr ON p.proceso = pr.id_unico "
        . "LEFT JOIN gg_estado_proceso epp ON pr.estado = epp.id_unico "
        . "LEFT JOIN gg_tipo_proceso tpp ON tpp.id_unico = pr.tipo_proceso"; 
$resultado = $mysqli->query($query);

?>
<style>
    body{
        font-size: 12px;
    }
    
</style>
<title>Listar Proceso</title>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Proceso</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Identificador</strong></td>
                                    <td><strong>Tipo Proceso</strong></td>
                                    <td><strong>Estado</strong></td>
                                    <td><strong>Proceso Asociado</strong></td>
                                    <td><strong>Responsable</strong></td>
                                    <td><strong>Fecha</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Identificador</th>
                                    <th>Tipo Proceso</th>
                                    <th>Estado</th>
                                    <th>Proceso Asociado</th>
                                    <th>Responsable</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_row($resultado)) { ?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td>
                                        <a  href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="GG_PROCESO.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo ucwords(strtolower(($row[13])));?></td>
                                    <td><?php echo ucwords(strtolower(($row[4].' - '.$row[5])));?></td>
                                    <td><?php echo ucwords(strtolower(($row[2])));?></td>
                                    <td><?php if (empty($row[8])){ echo ''; } else { echo ucwords(strtolower($row[10].' - '.$row[11].' ( '.$row[9].')')); }?></td>
                                    <td><?php echo ucwords(strtolower(($row[7])));?></td>
                                    <td><?php if(empty($row[12]) || $row[12]=='0000-00-00'){echo'';} else { echo date("d/m/Y", strtotime($row[12]));} ?></td>
                                    
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="GG_PROCESO.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Divs de clase Modal para las ventanillas de eliminar. -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Proceso?</p>
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
    <script type="text/javascript" src="js/menu.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <!-- Función para la eliminación del registro. -->
    <script type="text/javascript">
        function eliminar(id) {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"jsonProcesos/eliminar_GG_PROCESOJson.php?id="+id,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                            $("#myModal1").modal('show');
                            $('#ver1').click(function(){
                                document.location = 'LISTAR_GG_PROCESO.php';
                            });
                        } else { 
                            $("#myModal2").modal('show');
                            $('#ver2').click(function(){
                                document.location = 'LISTAR_GG_PROCESO.php';
                            });
                        }
                    }
                });
            });
       }
    </script>
</body>
</html>


x