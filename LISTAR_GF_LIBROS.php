<?php
#####################################################################################################################################################################
#                                               MODIFICACIONES
######################################################################################################################################################################
#28/09/2017 | ERICA G. | ARCHIVO CREADO
######################################################################################################################################################################
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$query = "SELECT id_unico, codigo_libro, LOWER(nombre_libro)
      FROM gf_libros"; 
$resultado = $mysqli->query($query);
?>
    <title>Listar Libros</title>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Libros</h2>    
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Código</strong></td>
                                    <td><strong>Nombre</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Código</th>                          
                                    <th>Nombre</th>                          
                                </tr>
                            </thead>
                            <tbody>              
                            <?php
                                while($row = mysqli_fetch_row($resultado)){
                                    echo '<tr>';
                                    echo '<td style="display: none;">'.$row[0].'</td>';
                                    echo '<td>';
                                    echo '<a  href="#" onclick="javascript:eliminarItem('.$row[0].')"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>';
                                    echo '<a href="GF_LIBROS.php?id='.md5($row[0]).'"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>';
                                    echo '</td>';
                                    echo '<td>'.$row[1].'</td>';
                                    echo '<td>'.ucwords($row[2]).'</td>';
                                    echo '</tr>';
                                } ?>
                        </tbody>
                    </table>
                        <div align="right"><a href="GF_LIBROS.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px;margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> 
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
                action:'3'
            };
            $("#myModal1").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_LibrosJson.php",
                    data:form_data,
                    success: function (data) {
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


