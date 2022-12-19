<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Creación de archivo  : 26/04/2017
// Creado por           : Alexander Numpaque
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Archivos abjuntos
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
require ('head_listar.php');
require ('Conexion/conexion.php');
?>
    <title>Listar Clase Informe</title><!-- ./Titulo  de la pagina -->
    <script type="text/javascript">
      //Función para eliminar valores de la base de datos
      function delete_class(id){
        var result = '';                              //Variable para capturar el valor retornado
        $("#myModal").modal('show');                  //Mostramos el modal
        $("#ver").click(function(){                   //Función cuando el botón se haga el evento click
          $.ajax({                                    //Envio ajax
              type:"GET",                             //Tipo de envio
              url:"json/registrarGNClaseInformeJson.php?id="+id+"&action=delete",    //url o ruta  de envio
              success: function (data) {              //Función cuando se da un buen resultado
                result = JSON.parse(data);            //Capturamos el valor de la url retornado
                if(result==true){                     //Si el valor retornado es verdadero
                  $("#myModal1").modal('show');       //Mostramos el modal con mensaje afirmativo
                } else{                               //De lo contrario
                  $("#myModal2").modal('show');       //Mostramos el modal con el mensaje de respuesta negativa
                }
              }
            });
          });
        }
      //Función para recargar la pagina
      function reload_page() {
        window.location.reload();   //Recargamos la pagina
      }
    </script>
  </head><!-- ./head -->
  <body><!-- body -->
    <div class="container-fluid"><!-- container-fluid -->
      <div class="row content"><!-- row content -->
        <?php require ('menu.php'); ?><!-- ./menu -->
        <div class="col-sm-10"><!-- col-sm-10 -->
          <h2 id="forma-titulo3" align="center" style="margin-top:0px">Clase Informe</h2><!-- ./h2 -->
          <div class="table-responsive"><!-- table-responsive -->
            <div class="table responsive"><!-- table-responsive -->
              <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%"><!-- table -->
                <thead><!-- thead -->
                  <tr><!-- tr-->
                    <td class="oculto"></td><!-- ./td -->
                    <td width="30px" align="center"></td><!-- ./td -->
                    <td><strong>Nombre</strong></td><!-- ./td -->
                  </tr><!-- ./tr -->
                  <tr><!-- tr-->
                    <th class="oculto"></th><!-- ./th -->
                    <th width="30px" align="center"></th><!-- ./th -->
                    <th>Nombre</th><!-- ./th -->
                  </tr><!-- ./tr -->
                </thead><!-- ./thead -->
                <tbody><!-- tbody -->
                  <?php
                  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                  //Consulta para obtener los valores de la tabla
                  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                  $sql = "SELECT id_unico,nombre FROM gn_clase_informe ORDER BY nombre ASC";
                  $result = $mysqli->query($sql);
                  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                  //Impresión de los valores retornado por la consulta
                  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                  while($row = mysqli_fetch_row($result)){
                    echo "<tr>\n";
                    echo "<td class=\"oculto\"></td>\n";
                    echo "<td>\n";
                    echo "<a href=\"#$row[0]\" title=\"Eliminar\" onclick=\"delete_class($row[0])\" class=\"campos\">\n";
                    echo "<li class=\"glyphicon glyphicon-trash\"></li>\n";
                    echo "</a>\n";
                    echo "<a href=\"MODIFICAR_GN_CLASE_INFORME.php?id=".md5($row[0])."\" title=\"Modificar\" class=\"campos\">\n";
                    echo "<li class=\"glyphicon glyphicon-edit\"></li>\n";
                    echo "</a>\n";
                    echo "</td>\n";
                    echo "<td class=\"campos\">".ucwords(mb_strtolower($row[1]))."</td>\n";
                    echo "</tr>\n";
                  }
                   ?>
                </tbody><!-- ./tbody -->
              </table><!-- ./table -->
              <div class="text-right"><!-- text-right -->
                <a href="registrar_GN_CLASE_INFORME.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;border-color: #1075C1; margin-top: 10px;margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
              </div><!-- /ext-right -->
            </div><!-- ./table-responsive -->
          </div><!-- ./table-responsive -->
        </div><!-- ./col-sm-10 -->
        <?php require ('footer.php'); ?><!-- ./footer -->
      </div><!-- ./row content -->
    </div><!-- ./container-fluid -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
      <div class="modal-dialog">
        <div class="modal-content">
          <div id="forma-modal" class="modal-header">
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
          </div>
          <div class="modal-body" style="margin-top: 8px">
            <p>¿Desea eliminar el registro seleccionado de Clase?</p>
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
            <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" onclick="reload_page()">Aceptar</button>
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
            <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
          </div>
        </div>
      </div>
    </div>
  </body><!-- ./body-->
</html><!-- ./html -->
