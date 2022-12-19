<?php 
#################MODIFICACIONES###########################
#04/05/2017 | Erica G. | Diseño, tíldes, búsquedas
##########################################################
require_once('Conexion/conexion.php');
require_once 'head_listar.php';
$query= "SELECT id_unico, nombre FROM gf_tipo_clase_pptal ";
$resultado = $mysqli->query($query);
?>
    <title>Listar Tipo Clase Presupuestal</title>
  </head>
<body>

<!-- Contnedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Lllamado al menu -->
  <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Tipo Clase Presupuestal</h2>
      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">

<!-- Inicio de tabla de listar -->          
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>
<!-- Inicio de campos de filtrado-->
              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center"></td>
                <td><strong>Nombre</strong></td>
                             
              </tr>
<!-- Fin de campos de filtrado -->
<!-- Inicio de titulos de la tabla-->
              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <th>Nombre</th>
                            
              </tr>
<!-- Fin de titulos de la tabla -->
<!-- Fin de cabeza de tabla-->
            </thead>
            <tbody>
             
              <?php
              //Ciclo de impresión de registros existentes 
                while($row = mysqli_fetch_row($resultado)){?>
               <tr>
                <td style="display: none;"><?php echo $row[0]?></td>
                <td>
                  <a  href="#" onclick="javascript:eliminarTipo(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                  <a href="modificar_GF_TIPO_CLASE_PPTAL.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                </td>
                <td><?php echo $row[1];?></td>
              </tr>
              <?php } 
                 //Fin ciclo de impresión de registros existentes             
              ?>

            </tbody>
          </table>
 <!-- Inicio de bóton de nuevo registro -->
          <div align="right"><a href="registrar_GF_TIPO_CLASE_PPTAL.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
        </div>
<!-- Fin de bóton de nuevo registro -->        
       
      </div>     
    </div>
  </div>
</div>
<!-- Inicio de Modal-->
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Tipo Clase Presupuestal?</p>
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
          <p>Información eliminada correctamente</p>
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

  <!-- Llamado al pie de pagina -->
  <?php require_once 'footer.php'; ?>


  <!-- Llamado de librerias --> 
  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
    //Función de eliminado y llamado de modal
      function eliminarTipo(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarTipoClasePptal.php?id="+id,
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
      //Función para mostrar modal  
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
  
  <script type="text/javascript">
      //Función para redirigir modal 1 al formulario listar    
      $('#ver1').click(function(){
        document.location = 'listar_GF_TIPO_CLASE_PPTAL.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GF_TIPO_CLASE_PPTAL.php';
      });
    
  </script>
</body>
</html>

