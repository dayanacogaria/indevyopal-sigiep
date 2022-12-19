<?php

require_once 'head_listar.php';
require_once('Conexion/conexion.php');
  $queryTipoC = "SELECT cb.id_unico,
                  cb.id_concepto,
                  cb.id_concepto_aplica,
                  cb.id_tipo_base,
                  concat(c.descripcion,' (',c.codigo,')') nom_con,
                  concat(capl.descripcion,' (',capl.codigo,')') nom_con_aplica,
                  tp.nombre

                FROM gn_concepto_base cb 
                LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                LEFT JOIN gn_concepto capl on capl.id_unico= cb.id_concepto_aplica 
                LEFT JOIN gn_tipo_base tp on tp.id_unico = cb.id_tipo_base";
  $resultado = $mysqli->query($queryTipoC);
?>
    <title>Listar Concepto Base</title>
  </head>
<body>  
<div class="container-fluid text-center">
  <div class="row content">

  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Concepto Base</h2>
      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center"></td>
                <td><strong>Concepto Aplica</strong></td>
                <td><strong>Concepto</strong></td>
                <td><strong>Tipo Base</strong></td>                
              </tr>

              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <th>Concepto Aplica</th>
                <th>Concepto</th>            
                <th>Tipo Base</th>                       
              </tr>

            </thead>
            <tbody>
              
              <?php
                while($row = mysqli_fetch_row($resultado)){
                    
                    ?>
               <tr>
                <td style="display: none;"><?php echo $row[0]?></td>
                <td>
                  <a class"" href="#" onclick="javascript:eliminarTipoc(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                  <a href="modificar_GN_CONCEPTO_BASE.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                  <a class="campos" href="ver_GN_CONCEPTO_BASE.php?id=<?php echo md5($row[0]);?>">
                    <i title="Ver Detalle" class="glyphicon glyphicon-eye-open" ></i>
                 </a>
                </td>
                <td><?php echo    ucwords(mb_strtolower($row[5]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[4]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[6]))?></td>     
                
              </tr>
              <?php } ?>

            </tbody>
          </table>

              <div align="right"><a href="registrar_GN_Concepto_Base.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

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
          <p>¿Desea eliminar el registro seleccionado de Concepto Base?</p>
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
          <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <?php require_once 'footer.php'; ?>

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
      function eliminarTipoc(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/elminar_GN_Concepto_BaseJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  console.log("res: "+result);
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
        window.location='listar_GN_CONCEPTO_BASE.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        window.location='listar_GN_CONCEPTO_BASE.php';
      });
    
  </script>

</body>
</html>
