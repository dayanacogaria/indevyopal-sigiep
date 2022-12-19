<?php

require_once 'head_listar.php';
require_once('Conexion/conexion.php');
  $queryTipoC = "SELECT eht.id_unico,
                eht.id_espacio_habitable,  
                eht.id_tercero,
                eht.id_perfil,
                eh.descripcion,
                 IF(CONCAT_WS(' ',
                   t.nombreuno,
                   t.nombredos,
                   t.apellidouno,
                   t.apellidodos) 
                   IS NULL OR CONCAT_WS(' ',
                   t.nombreuno,
                   t.nombredos,
                   t.apellidouno,
                   t.apellidodos) = '',
                   (t.razonsocial),
                   CONCAT_WS(' ',
                   t.nombreuno,
                   t.nombredos,
                   t.apellidouno,
                   t.apellidodos)) AS NOMBRE, 
                p.nombre,
                eht.id_parentesco,
                tp.nombre,
                eht.principal,
                IF(CONCAT_WS(' ',
                   trel.nombreuno,
                   trel.nombredos,
                   trel.apellidouno,
                   trel.apellidodos) 
                   IS NULL OR CONCAT_WS(' ',
                   trel.nombreuno,
                   trel.nombredos,
                   trel.apellidouno,
                   trel.apellidodos) = '',
                   (trel.razonsocial),
                   CONCAT_WS(' ',
                   trel.nombreuno,
                   trel.nombredos,
                   trel.apellidouno,
                   trel.apellidodos)) AS NOMBRE,
                eht.tercero_asociado
                FROM gph_espacio_habitable_tercero  eht
                LEFT JOIN gh_espacios_habitables eh on eh.id_unico=eht.id_espacio_habitable
                LEFT join gf_tercero t on t.id_unico=eht.id_tercero 
                LEFT JOIN gf_perfil p on p.id_unico=eht.id_perfil
                LEFT JOIN gph_tipo_parentesco tp on tp.id_unico=eht.id_parentesco
                left join gf_tercero trel on trel.id_unico=eht.tercero_asociado
                order by eht.id_espacio_habitable asc";
  $resultado = $mysqli->query($queryTipoC);
?>
    <title>Listar Espacio Habitable Tercero</title>
  </head>
<body>  
<div class="container-fluid text-center">
  <div class="row content">

  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Espacio Habitable Tercero</h2>
      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center"></td>
                <td><strong>Espacio Habitable</strong></td>
                <td><strong>Tercero</strong></td>
                <td><strong>Perfil</strong></td>
                <td><strong>Parentesco</strong></td>
                <td><strong>Principal</strong></td>
                <td><strong>Tercero Relacionado</strong></td>
              </tr>

              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <th>Espacio Habitable</th>
                <th>Tercero</th>            
                <th>Perfil</th>  
                <th>Parentesco</th>     
                <th>Principal</th>   
                <th>Tercero Relacionado</th>     
              </tr>

            </thead>
            <tbody>
              
              <?php
                while($row = mysqli_fetch_row($resultado)){
                    $n_ter=$row[5];
                    $princp='No';
                    if(empty($n_ter)){
                        $n_ter=$row[6];
                    }
                    if($row[9]==='2'){
                        $princp='Si';
                    }
                    ?>
               <tr>
                <td style="display: none;"><?php echo $row[0]?></td>
                <td>
                  <a class"" href="#" onclick="javascript:eliminarTipoc(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                  <a href="modificar_GPH_ESPACIO_HABITABLE_TERCERO.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                  <a class="campos" href="ver_GPH_ESPACIO_HABITABLE_TERCERO.php?id=<?php echo md5($row[0]);?>">
                    <i title="Ver Detalle" class="glyphicon glyphicon-eye-open" ></i>
                 </a>
                </td>
                <td><?php echo    ucwords(mb_strtolower($row[4]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[5]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[6]))?></td>     
                <td><?php echo    ucwords(mb_strtolower($row[8]))?></td>    
                <td><?php echo    ucwords(mb_strtolower($princp))?></td>  
                <td><?php echo    ucwords(mb_strtolower($row[10]))?></td>    
              </tr>
              <?php } ?>

            </tbody>
          </table>

              <div align="right"><a href="registrar_GPH_ESPACIO_HABITABLE_TERCERO.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

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
          <p>¿Desea eliminar el registro seleccionado de Espacio habitable tercero?</p>
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
                  url:"json/eliminarEspacioHabitableTerceroJson.php?id="+id,
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
        document.location = 'listar_GPH_ESPACIO_HABITABLE_TERCERO.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GPH_ESPACIO_HABITABLE_TERCERO.php';
      });
    
  </script>

</body>
</html>
