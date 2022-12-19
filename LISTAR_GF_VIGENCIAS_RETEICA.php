<?php
##############################################################################################
#       *****************************       MODIFICACIONES      *****************************#
##############################################################################################
#23/07/2018| ERICA G. | ARCHIVO CREADO 
##############################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$row = $con->Listar("SELECT id_unico, nombre, valor, vigencias_anteriores
      FROM gf_vigencias_interfaz_reteica WHERE parametrizacionanno = $anno"); 
?>
<html>
    <head>
    <title>Vigencias Interfaz Reteica</title>
    </head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Vigencias Interfaz Reteica</h2>    
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Valor</strong></td>
                                    <td><strong>Vigencias Anteriores</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>                          
                                    <th>Valor</th>                          
                                    <th>Vigencias Anteriores</th>                          
                                </tr>
                            </thead>
                            <tbody>              
                            <?php
                                for ($i = 0; $i < count($row); $i++) {
                                    echo '<tr>';
                                    echo '<td style="display: none;">'.$row[$i][0].'</td>';
                                    echo '<td>';
                                    echo '<a  href="#" onclick="javascript:eliminarItem('.$row[$i][0].')"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>';
                                    echo '<a href="GF_VIGENCIAS_RETEICA.php?id='.md5($row[$i][0]).'"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>';
                                    echo '</td>';
                                    echo '<td>'.$row[$i][1].'</td>';
                                    echo '<td>'.$row[$i][2].'</td>';
                                    if($row[$i][3]==1){
                                    echo '<td>SÍ</td>';
                                    } else {
                                    echo '<td>NO</td>';    
                                    }
                                    echo '</tr>';
                                } ?>
                        </tbody>
                    </table>
                        <div align="right"><a href="GF_VIGENCIAS_RETEICA.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px;margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> 
                    </div>           
                </div>            
            </div>      
        </div>
        </div>
    </div>  
    <!-- Divs de clase Modal para las ventanillas de eliminar. -->
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                action:'14'
            };
            $("#myModal1").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                jsShowWindowLoad('Eliminando Información...');
                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_interfaz_ComercioJson.php",
                    data:form_data,
                    success: function (data) {
                        jsRemoveWindowLoad();
                        result = JSON.parse(data);
                        if(result==true){
                          $("#mensaje").html('Información eliminada correctamente');  
                          $("#myModal").modal('show');
                        }else{
                          $("#mensaje").html('No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.');  
                          $("#myModal").modal('show');
                        }
                    }
                });
            });
        }
       
    </script>

  <script type="text/javascript">
    
       $('#aceptar').click(function(){
            document.location.reload();
        });
    
  </script>

</body>
</html>



