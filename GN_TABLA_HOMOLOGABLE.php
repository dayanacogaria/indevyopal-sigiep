<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');

$queryTabHom = "SELECT tabHom.id, tabHom.tabla_origen, tabHom.columna_origen, tabHom.tabla_destino, tabHom.columna_destino, tipHom.nombre tipoHomologacion, inf.nombre informe, per.nombre periodicidad 
  FROM gn_tabla_homologable tabHom 
  LEFT JOIN gn_tipo_homologable tipHom ON tipHom.id = tabHom.tipo 
  LEFT JOIN gn_informe inf ON inf.id = tabHom.informe 
  LEFT JOIN gn_periodicidad per ON per.id = tabHom.periodicidad";

$resultado = $mysqli->query($queryTabHom);

?>
  <title>Tabla Homologable</title>
</head>
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
    
    <?php require_once ('menu.php'); ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Tabla Homologable</h2>

      

      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center"></td>
                <td><strong>Tabla Origen</strong></td>
                <td><strong>Columna Origen</strong></td>
                <td><strong>Tabla Destino</strong></td>
                <td><strong>Columna Destino</strong></td>
                <td><strong>Tipo Homologable</strong></td>
                <td><strong>Informe</strong></td>
                <td><strong>Periodicidad</strong></td>

                
              </tr>

              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <th>Tabla Origen</th>
                <th>Columna Origen</th>
                <th>Tabla Destino</th>
                <th>Columna Destino</th>
                <th>Tipo Homologable</th>
                <th>Informe</th>
                <th>Periodicidad</th>

                
              </tr>

            </thead>
            <tbody>
              
              <?php 
                while($row = mysqli_fetch_row($resultado)){?>
               <tr>
                <td style="display: none;"><?php echo $row[0]?></td>
                <td>
                  <a class"" href="#" onclick="javascript:eliminarTablaHomologable(<?php echo $row[0];?>);">
                    <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                  </a>

                  <a href="modificar_GN_TABLA_HOMOLOGABLE.php?id_tabla_homologable=<?php echo md5($row[0]);?>">
                    <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                  </a>

                </td>
                <td><?php echo ucwords(mb_strtolower($row[1]))?></td>
                <td><?php echo ucwords(mb_strtolower($row[2]))?></td>
                <td><?php echo ucwords(mb_strtolower($row[3]))?></td>
                <td><?php echo ucwords(mb_strtolower($row[4]))?></td>
                <td><?php echo ucwords(mb_strtolower($row[5]))?></td>
                <td><?php echo ucwords(mb_strtolower($row[6]))?></td>
                <td><?php echo ucwords(mb_strtolower($row[7]))?></td>
                
              </tr>
              <?php } ?>


            </tbody>
          </table>

          <div align="right"><a href="registrar_GN_TABLA_HOMOLOGABLE.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; 
  border-color: #1075C1; margin-top: 20px; 
  margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

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
          <p>¿Desea eliminar el registro seleccionado de Tabla Homologable?</p>
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
      function eliminarTablaHomologable(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GN_TABLA_HOMOLOGABLEJson.php?id="+id,
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
        document.location = 'GN_TABLA_HOMOLOGABLE.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'GN_TABLA_HOMOLOGABLE.php';
      });
    
  </script>

</body>
</html>

