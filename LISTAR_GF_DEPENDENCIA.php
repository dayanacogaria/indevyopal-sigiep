<?php
    require_once('Conexion/conexion.php');
    require_once('head_listar.php');
    $compania = $_SESSION['compania'];
    $query = "SELECT d.id_unico, d.nombre, d.sigla, d.movimiento, d.activa , dep.nombre, cc.nombre, td.nombre , dep.sigla 
          FROM gf_dependencia d 
          LEFT JOIN gf_dependencia dep ON d.predecesor = dep.id_unico 
          LEFT JOIN gf_centro_costo cc ON d.centrocosto=cc.id_unico 
          LEFT JOIN gf_tipo_dependencia td ON d.tipodependencia=td.id_unico
          WHERE d.compania = $compania"; 
    $resultado = $mysqli->query($query);
?>
    <title>Listar Dependencia</title>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Dependencia</h2>    
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Sigla</strong></td>
                                    <td><strong>Movimiento</strong></td>
                                    <td><strong>Activa</strong></td>
                                    <td><strong>Predecesor</strong></td>
                                    <td><strong>Centro Costo</strong></td>
                                    <td><strong>Tipo Dependencia</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>
                                    <th>Sigla</th>
                                    <th>Movimiento</th>
                                    <th>Activa</th>
                                    <th>Predecesor</th>
                                    <th>Centro Costo</th>
                                    <th>Tipo Dependencia</th>                            
                                </tr>
                            </thead>
                            <tbody>              
                            <?php
                                while($row = mysqli_fetch_row($resultado)){?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td>
                                        <a  href="#" onclick="javascript:eliminarItem(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="Modificar_GF_DEPENDENCIA.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo ucwords(strtolower(($row[1])));?></td>
                                    <td><?php echo strtoupper($row[2]);?></td>
                                    <td><?php if($row[3]=='0'){ echo 'Sí'; } elseif($row[3]=='1'){ echo 'No';}?></td>
                                    <td><?php if($row[4]=='0'){ echo 'Sí'; } elseif($row[4]=='1'){ echo 'No';}?></td>
                                    <td><?php echo ucwords(strtolower(($row[8].' - '.$row[5])));?></td>
                                    <td><?php echo ucwords(strtolower(($row[6])));?></td>
                                    <td><?php echo ucwords(strtolower(($row[7])));?></td>                    
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div align="right"><a href="Registrar_GF_DEPENDENCIA.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px;margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> 
                    </div>           
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
                    <p>¿Desea eliminar el registro seleccionado de Dependencia?</p>
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
        function eliminarItem(id) {
            var result = '';
            var form_data = {
                id:id,
                action:'delete'
            };
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"POST",
                    url:"controller/controllerGFDependencia.php",
                    data:form_data,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                          $("#myModal1").modal('show');
                        }else{
                          $("#myModal2").modal('show');
                        }
                    }
                });
            });
        }
  
        function modal() {
            $("#myModal").modal('show');
        }  
    
        $('#ver1').click(function(){
            document.location = 'LISTAR_GF_DEPENDENCIA.php';
        });
    </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'LISTAR_GF_DEPENDENCIA.php';
      });
    
  </script>

</body>
</html>


