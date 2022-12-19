<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes
require_once 'head_listar.php';
require_once('Conexion/conexion.php');


//consulta para cargar la informacion en la tabla
$queryCondicion = "SELECT Id_Unico, Nombre FROM gf_tipo_entidad  ORDER BY Nombre ASC" ; 

$resultado = $mysqli->query($queryCondicion);

?>

<!--Titulo de la página-->
<title>Listar Tipo Entidad</title>
</head>
<body>
  
<div class="container-fluid text-center">
  <div class="row content">
    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
    <!--titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Tipo Entidad</h2>

      
      <!--tabla para mostrar la información-->
      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>


            <!--titulos de la tabla-->
              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px"></td>
                <td><strong>Nombre</strong></td>
                

                
              </tr>
              
              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <th>Nombre</th>
                

                
              </tr>
            </thead>
            <tbody>
              <!--Aqui se muestran  los registros de la base de datos junto a los iconos de eliminar y modificar-->
              <?php
                while($row = mysqli_fetch_row($resultado)){?>
               <tr>
                <td style="display: none;"><?php echo $row[0]?></td>
                <td><a class"" href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a><a href="Modificar_GF_TIPO_ENTIDAD.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                <td><?php echo ucwords(mb_strtolower($row[1]));?></td>
                
                
                
              </tr>
              <?php } ?>


            </tbody>
          </table>
          <!--Boton que abre el formulario de registro-->
          <div align="right"><a href="Registrar_GF_TIPO_ENTIDAD.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

        </div>
        
       
      </div>
      
    </div>

  </div>
</div>
<!--Modal que confirma la eliminación del registro -->
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Tipo Entidad?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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


  
<!--Links de estilos para el formulario -->
  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Script que envia los datos necesarios para la eliminación-->
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarTipo_Entidad.php?id="+id,
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
   <!--actualizar la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'listar_GF_TIPO_ENTIDAD.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GF_TIPO_ENTIDAD.php';
      });
    
  </script>
<?php require_once 'footer.php' ?>
</body>
</html>

