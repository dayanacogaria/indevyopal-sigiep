<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$anno = $_SESSION['anno'];
$query = "SELECT p.id_unico, "
        . "c.nombre, "
        . "pa.anno, "
        . "p.fecha_inicial, "
        . "p.fecha_final, "
        . "p.primera_fecha, "
        . "p.segunda_fecha, "
        . "p.fecha_cierre, "
        . "p.descripcion, "
        . "p.nombre "
        . "FROM gp_periodo p "
        . "LEFT JOIN gp_ciclo c ON p.ciclo= c.id_unico "
        . "LEFT JOIN gf_parametrizacion_anno pa ON p.anno = pa.id_unico "
        . "WHERE pa.id_unico = $anno"; 
$resultado = $mysqli->query($query);

?>
<title>Listar Periodo</title>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Periodo</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Ciclo</strong></td>
                                    <td><strong>Año</strong></td>
                                    <td><strong>Fecha inicial</strong></td>
                                    <td><strong>Fecha final</strong></td>
                                    <td><strong>Primera fecha</strong></td>
                                    <td><strong>Segunda fecha</strong></td>
                                    <td><strong>Fecha cierre</strong></td>
                                    <td><strong>Descripción</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th><strong>Nombre</strong></th>
                                    <th><strong>Ciclo</strong></th>
                                    <th><strong>Año</strong></th>
                                    <th><strong>Fecha inicial</strong></th>
                                    <th><strong>Fecha final</strong></th>
                                    <th><strong>Primera fecha</strong></th>
                                    <th><strong>Segunda fecha</strong></th>
                                    <th><strong>Fecha cierre</strong></th>
                                    <th><strong>Descripción</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = mysqli_fetch_row($resultado)){?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td>
                                      <a  href="#" onclick="javascript:eliminarPeriodo(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                      <a href="Modificar_GP_PERIODO.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo ucwords(mb_strtolower(($row[9])));?></td>
                                    <td><?php echo ucwords(mb_strtolower(($row[1])));?></td>
                                    <td><?php echo ucwords(mb_strtolower(($row[2])));?></td>
                                    <td><?php echo date("d/m/Y", strtotime($row[3]));?></td>
                                    <td><?php echo date("d/m/Y", strtotime($row[4]));?></td>
                                    <td><?php echo date("d/m/Y", strtotime($row[5]));?></td>
                                    <td><?php echo date("d/m/Y", strtotime($row[6]));?></td>
                                    <td><?php echo date("d/m/Y", strtotime($row[7]));?></td>
                                    <td><?php echo ucwords(mb_strtolower(($row[8])));;?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="Registrar_GP_PERIODO.php" class="btn btn-primary" 
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
                    <p>¿Desea eliminar el registro seleccionado de Periodo?</p>
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
    <script type="text/javascript" src="js/menu.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function eliminarPeriodo(id)
        {
           var result = '';
           $("#myModal").modal('show');
           $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminar_GP_PERIODOJson.php?id="+id,
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
          document.location = 'LISTAR_GP_PERIODO.php';
        });

    </script>
    <script type="text/javascript">

        $('#ver2').click(function(){
          document.location = 'LISTAR_GP_PERIODO.php';
        });

    </script>
</body>


