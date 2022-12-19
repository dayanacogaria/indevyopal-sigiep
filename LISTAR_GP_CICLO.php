<?php
require_once('head_listar.php');
require_once('Conexion/conexion.php');

$query = "SELECT c.id_unico, c.nombre, pi.codigo_catastral, pf.codigo_catastral, ef.nombre, f.nombre FROM gp_ciclo c 
    LEFT JOIN gp_unidad_vivienda uvi ON c.unidad_vivienda_inicial = uvi.id_unico
    LEFT JOIN gp_unidad_vivienda uvf ON c.unidad_vivienda_final = uvf.id_unico
    LEFT JOIN gp_predio1 pi ON uvi.predio = pi.id_unico
    LEFT JOIN gp_predio1 pf ON uvf.predio = pf.id_unico
    LEFT JOIN gp_estado_facturacion ef ON c.estado_facturacion = ef.id_unico
    LEFT JOIN gf_formato f ON f.id_unico = c.formato_factura"; 
$resultado = $mysqli->query($query);

?>
<title>Listar Ciclo</title>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Ciclo</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Unidad vivienda inicial</strong></td>
                                    <td><strong>Unidad vivienda final</strong></td>
                                    <td><strong>Estado facturación</strong></td>
                                    <td><strong>Formato factura</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th><strong>Nombre</strong></th>
                                    <th><strong>Unidad vivienda inicial</strong></th>
                                    <th><strong>Unidad vivienda final</strong></th>
                                    <th><strong>Estado facturación</strong></th>
                                    <th><strong>Formato factura</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = mysqli_fetch_row($resultado)){?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td>
                                      <a  href="#" onclick="javascript:eliminarCiclo(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                      <a href="Modificar_GP_CICLO.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo ucwords(mb_strtolower(($row[1])));?></td>
                                    <td><?php echo ucwords(mb_strtolower(($row[2])));?></td>
                                    <td><?php echo ucwords(mb_strtolower(($row[3])));?></td>
                                    <td><?php echo ucwords(mb_strtolower(($row[4])));?></td>
                                    <td><?php echo ucwords(mb_strtolower(($row[5])));?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="Registrar_GP_CICLO.php" class="btn btn-primary" 
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
                    <p>¿Desea eliminar el registro seleccionado de Ciclo?</p>
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
    <script type="text/javascript">
        function eliminarCiclo(id)
        {
           var result = '';
           $("#myModal").modal('show');
           $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminar_GP_CICLOJson.php?id="+id,
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
          document.location = 'LISTAR_GP_CICLO.php';
        });

    </script>
    <script type="text/javascript">

        $('#ver2').click(function(){
          document.location = 'LISTAR_GP_CICLO.php';
        });

    </script>
</body>


