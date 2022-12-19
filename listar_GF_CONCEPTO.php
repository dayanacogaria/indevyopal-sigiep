<?php
require_once('Conexion/conexion.php');
require_once 'head_listar.php';
$anno = $_SESSION['anno'];
?>
    <title>Listar Concepto</title>
  </head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:-2px">Concepto</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-15px">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td class="cabeza" width="30px" align="center"></td>
                                        <td class="cabeza"><strong>Nombre</strong></td>  
                                        <td class="cabeza"><strong>Tipo Concepto</strong></td>  
                                        <td class="cabeza"><strong>Amortizable</strong></td>
                                        <td class="cabeza"><strong>Servicio</strong></td>
                                    </tr>
                                    <tr>
                                      <th class="cabeza" style="display: none;">Identificador</th>
                                      <th class="cabeza" width="7%"></th>
                                      <th class="cabeza">Nombre</th>
                                      <th class="cabeza">Tipo Concepto</th>
                                      <th class="cabeza">Amortizable</th>
                                      <th class="cabeza">Servicio</th>
                                    </tr>
                                </thead>
                                <tbody>              
                                <?php
                                    $queryConcepto = "SELECT c.id_unico,c.nombre,cc.id_unico,"
                                            . "cc.nombre, c.amortizable, ts.nombre "
                                            . "FROM gf_concepto c "
                                            . "LEFT JOIN gf_clase_concepto cc ON c.clase_concepto=cc.id_unico "
                                            . "LEFT JOIN gp_tipo_servicio ts ON c.tipo_servicio = ts.id_unico "
                                            . "WHERE c.parametrizacionanno = $anno";
                                    $resultado = $mysqli->query($queryConcepto);
                                    while($row = mysqli_fetch_row($resultado)){?>
                                    <tr>
                                        <td class="campos" style="display: none;"><?php echo $row[0]?></td>
                                        <td class="campos">
                                          <a href="#" onclick="javascript:eliminarConcepto(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                          <a href="modificar_GF_CONCEPTO.php?id_concepto=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        </td>
                                        <td class="campos"><?php echo ucwords (mb_strtolower($row[1]))?></td>                               
                                        <td class="campos"><?php echo ucwords (mb_strtolower($row[3])); ?></td>
                                        <?php if ($row[4] == 1){
                                            $row[4]= "Si";
                                            }else if ($row[4] == 2 || empty($row[4])){
                                            $row[4]= "No";    
                                            }
                                            ?>
                                        <td class="campos"><?php echo ucwords (mb_strtolower($row[4])); ?></td>
                                        <td class="campos"><?php echo ucwords (mb_strtolower($row[5])); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div align="right"><a href="registrar_GF_CONCEPTO.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a></div>
                        </div             
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
                    <p>¿Desea eliminar el registro seleccionado de Concepto?</p>
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
                    <p> Información eliminada correctamente.</p>
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
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>      
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function eliminarConcepto(id){
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarConcepto.php?id="+id,
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
        function modal(){
            $("#myModal").modal('show');
        }
    </script>
    <script type="text/javascript">    
        $('#ver1').click(function(){
            document.location = 'listar_GF_CONCEPTO.php';
        });    
    </script>
    <script type="text/javascript">    
        $('#ver2').click(function(){
            document.location = 'listar_GF_CONCEPTO.php';
        });    
    </script>
    </body>
    <?php require_once 'footer.php'; ?>
</html>

