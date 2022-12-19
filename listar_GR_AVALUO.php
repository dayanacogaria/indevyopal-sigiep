<?php

################ MODIFICACIONES ####################
#02/06/2017 | Anderson Alarcon | modifique consulta de table avaluo
############################################

require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

$sql = "SELECT a.*,
        t.id_unico AS id_unico_tarifa, 
        tpt.nombre AS nombreTipoTarifa,
        p.id_unico AS id_unico_predio, 
        p.nombre FROM gr_avaluo a 
        LEFT JOIN gp_tarifa t ON a.tarifa = t.id_unico
        LEFT JOIN gp_tipo_tarifa tpt ON t.tipo_tarifa=tpt.id_unico
        LEFT JOIN gp_predio1 p ON a.predio = p.id_unico";
$resultado = $mysqli->query($sql);
?>
        <title>Listar Avalúo</title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Avalúo</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Valor</strong></td>
                                        <td class="cabeza"><strong>Indicador</strong></td>
                                        <td class="cabeza"><strong>Tarifa</strong></td>
                                        <td class="cabeza"><strong>Predio</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th class="cabeza">Valor</th>
                                        <th class="cabeza">Indicador</th>
                                        <th class="cabeza">Tarifa</th>
                                        <th class="cabeza">Predio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_array($resultado)) {
                                            
                                        $aid  = $row['id_unico'];
                                        $aval = $row['valor'];
                                        $aind = $row['indicador'];
                                        $atar = $row['tarifa'];
                                        $apre = $row['predio'];

                                        $tid  = $row['id_unico_tarifa'];
                                        $tnom = $row['nombreTipoTarifa'];//muestro nombre del tipo de tarifa  
                                        $pid  = $row['id_unico_predio'];
                                        $pnom = $row['nombre'];
                                            
                                    ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GR_AVALUO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <td class="campos"><?php echo $aval?></td>                
                                        <td class="campos"><?php if($aind==1)
                                                                    echo "SI";
                                                                 else
                                                                    echo "NO";?></td>                
                                        <td class="campos"><?php echo $tnom?></td>                
                                        <td class="campos"><?php echo $pnom?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GR_AVALUO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once './footer.php'; ?>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Avalúo?</p>
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


  <!--Script que dan estilo al formulario-->

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarAvaluoPJson.php?id="+id,
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
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'listar_GR_AVALUO.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GR_AVALUO.php';
      });    
  </script>
    </body>
</html>