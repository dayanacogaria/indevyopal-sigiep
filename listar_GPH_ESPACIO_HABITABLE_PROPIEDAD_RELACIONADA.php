<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes 
require_once 'head_listar.php';
require_once('Conexion/conexion.php');
  $queryTipoV = "SELECT 
                ehp.id_unico,
                ehp.id_tipo_relacion,
                ehp.id_vehiculo,
                ehp.id_espacio_habitable,
                v.placa,
                v.marca,
                v.color,
                concat(eh.codigo,' - ',eh.descripcion)as des_apto

                from gph_espacio_habitable_propiedad_relacionada ehp
                left join gph_vehiculo v on v.id_unico=ehp.id_vehiculo
                left join gh_espacios_habitables eh on eh.id_unico= ehp.id_espacio_habitable
                where ehp.id_tipo_relacion='1' order by ehp.id_espacio_habitable asc";
  $resultado = $mysqli->query($queryTipoV);

  $queryTipoM = "SELECT 
                ehp.id_unico,
                ehp.id_tipo_relacion,
                ehp.id_mascota,
                ehp.id_espacio_habitable,
                m.especie,
                m.raza,
                concat(eh.codigo,' - ',eh.descripcion)as des_apto

                from gph_espacio_habitable_propiedad_relacionada ehp
                left join gph_mascota m on m.id_unico=ehp.id_mascota
                left join gh_espacios_habitables eh on eh.id_unico= ehp.id_espacio_habitable
                where ehp.id_tipo_relacion='2' order by ehp.id_espacio_habitable asc";
  $resultado2 = $mysqli->query($queryTipoM);

?>
    <title>Listar Espacio Habitable Propiedad Relacionada</title>
  </head>
<body>  
<div class="container-fluid text-center">
  <div class="row content">

  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Espacio Habitable Propiedad Relacionada</h2>

      <strong style=" font-size: 12px; margin-left: 53px; margin-right: 5px;margin-top:50px; margin-bottom: 500px;">MASCOTAS</strong>

      <div class="table-responsive" style="margin-left: 52px; margin-right: 35px;margin-top: 15px;margin-bottom: 15px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">          
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center"></td>
                <td><strong>Espacio Habitable (Apartamento)</strong></td>
                <td><strong>Especie</strong></td>
                <td><strong>Raza</strong></td>
                
              </tr>

              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <th>Espacio Habitable (Apartamento)</th>
                <th>Especie</th>
                <th>Raza</th>
                
              </tr>

            </thead>
            <tbody>
              
              <?php
                while($row = mysqli_fetch_row($resultado2)){?>
               <tr>
                <td style="display: none;"><?php echo $row[0]?></td>
                <td>
                  <a class"" href="#" onclick="javascript:eliminarTipoc(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                  <a href="modificar_GPH_ESPACIO_HABITABLE_PROPIEDAD_RELACIONADA.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                  <!--<a class="campos" href="ver_GPH_VISITANTES.php?id=<?php echo md5($row[0]);?>">
                    <i title="Ver Detalle" class="glyphicon glyphicon-eye-open" ></i></a>-->
                </td>
                <td><?php echo    ucwords(mb_strtolower($row[6]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[4]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[5]))?></td>      
                
              </tr>
              <?php } ?>

            </tbody>
          </table>
        </div>      
      </div>

<strong style=" font-size: 12px; margin-left: 53px; margin-right: 5px;margin-top:500px; margin-bottom: 50px;">VEHICULOS</strong>
      <div class="table-responsive" style="margin-left: 52px; margin-right: 35px; margin-top: 15px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">          
          <table id="tabla2" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" >
            <thead>

              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center"></td>
                <td><strong>Espacio Habitable (Apartamento)</strong></td>
                <td><strong>Placa</strong></td>
                <td><strong>Marca</strong></td>
                <td><strong>Color</strong></td>
              </tr>

              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <th>Espacio Habitable (Apartamento)</th>
                <th>Placa</th>
                <th>Marca</th>
                <th>Color</th>                
              </tr>

            </thead>
            <tbody>
              
              <?php
                while($row = mysqli_fetch_row($resultado)){?>
               <tr>
                <td style="display: none;"><?php echo $row[0]?></td>
                <td>
                  <a class"" href="#" onclick="javascript:eliminarTipoc(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                  <a href="modificar_GPH_ESPACIO_HABITABLE_PROPIEDAD_RELACIONADA.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                  <!--<a class="campos" href="ver_GPH_VISITANTES.php?id=<?php echo md5($row[0]);?>">
                    <i title="Ver Detalle" class="glyphicon glyphicon-eye-open" ></i></a>-->
                </td>
                <td><?php echo    ucwords(mb_strtolower($row[7]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[4]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[5]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[6]))?></td>      
                
              </tr>
              <?php } ?>

            </tbody>
          </table>

              <div align="right"><a href="registrar_GPH_ESPACIO_HABITABLE_PROPIEDAD_RELACIONADA.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

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
          <p>¿Desea eliminar el registro seleccionado de Espacio habitable propiedad?</p>
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
                  url:"json/eliminarEspacioHabitablePropiedad_JSON_GPH.php?id="+id,
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
        document.location = 'listar_GPH_ESPACIO_HABITABLE_PROPIEDAD_RELACIONADA.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GPH_ESPACIO_HABITABLE_PROPIEDAD_RELACIONADA.php';
      });
    
  </script>

</body>
</html>
