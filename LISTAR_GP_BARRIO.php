<?php
###################################################################################
#   **********************      Modificaciones      ******************************#
###################################################################################
#11/02/2019 |Erica G. | Agregar Ciudad
#05/07/2018 |Erica G. | Modificación Código
###################################################################################
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$query = "SELECT b.id_unico, b.nombre, c.id_unico, c.nombre, d.nombre
    FROM gp_barrio b 
    LEFT JOIN gf_ciudad c ON b.ciudad = c.id_unico 
    LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
    "; 
$resultado = $mysqli->query($query);
?>
<title>Listar barrio</title>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Barrio</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Ciudad</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>
                                    <th>Ciudad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_row($resultado)) { ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0] ?></td>
                                        <td>
                                            <a  href="#" onclick="javascript:eliminarBarrio(<?php echo $row[0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="Modificar_GP_BARRIO.php?id=<?php echo md5($row[0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        </td>
                                        <td><?php echo ucwords(mb_strtolower(($row[1]))); ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($row[3].' - '.$row[4]))); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="Registrar_GP_BARRIO.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
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
                    <p>¿Desea eliminar el registro seleccionado de Barrio?</p>
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
    <!-- Función para la eliminación del registro. -->
    <script type="text/javascript">
        function eliminarBarrio(id){
             var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $("#mymodal").modal('hide');
                var form_data ={action:1, id:id};
                $.ajax({
                    type: "POST",
                    url: "jsonServicios/gp_BarrioJson.php",
                    data: form_data,
                    success: function (data) {
                        console.log(data);
                        result = JSON.parse(data);
                        if (result == true){
                            $("#myModal1").modal('show');
                            $('#ver1').click(function () {
                                document.location.reload();
                            });
                        }else{  
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


