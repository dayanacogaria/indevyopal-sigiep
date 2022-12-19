<?php
#29/06/2017 --- Nestor B --- se modificó la consulta para que no muestre el empleado varios
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

   $sql = "SELECT    e.id_unico,
                    e.tercero,
                    ter.id_unico,
                    CONCAT_WS(' ',
                     ter.nombreuno,
                     ter.nombredos,
                     ter.apellidouno,
                     ter.apellidodos),
                    e.estado,
                    ee.id_unico,
                    ee.nombre,
                    e.cesantias,
                    rc.id_unico,
                    rc.nombre,
                    e.mediopago,
                    mp.id_unico,
                    mp.nombre,
                    e.unidadejecutora,
                    ue.id_unico,
                    ue.nombre,
                    e.grupogestion,
                    gg.id_unico,
                    gg.nombre,
                    e.codigointerno,
                    ter.id_unico
                FROM gn_empleado e
                LEFT JOIN   gf_tercero ter          ON e.tercero = ter.id_unico
                LEFT JOIN   gn_estado_empleado ee   ON e.estado = ee.id_unico
                LEFT JOIN   gn_regimen_cesantias rc ON e.cesantias = rc.id_unico
                LEFT JOIN   gn_medio_pago mp        ON e.mediopago = mp.id_unico
                LEFT JOIN   gn_unidad_ejecutora ue  ON e.unidadejecutora = ue.id_unico
                LEFT JOIN   gn_grupo_gestion gg     ON e.grupogestion = gg.id_unico
                WHERE e.id_unico !=2";
    $resultado = $mysqli->query($sql);
    
?>
    <title>Listar Empleado</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Empleado</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Tercero</strong></td>
                                        <td class="cabeza"><strong>Código Interno</strong></td>
                                        <td class="cabeza"><strong>Estado</strong></td>
                                        <td class="cabeza"><strong>Cesantías</strong></td>
                                        <td class="cabeza"><strong>Medio Pago</strong></td>
                                        <td class="cabeza"><strong>Unidad Ejecutora</strong></td>
                                        <td class="cabeza"><strong>Grupo Gestión</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th ></th>                                        
                                        <th class="cabeza">Tercero</th>
                                        <th class="cabeza">Código Interno</th>
                                        <th class="cabeza">Estado</th>
                                        <th class="cabeza">Cesantías</th>
                                        <th class="cabeza">Medio Pago</th>
                                        <th class="cabeza">Unidad Ejecutora</th>
                                        <th class="cabeza">Grupo Gestión</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                            $emid   = $row[0];
                                            $emter  = $row[1];
                                            $terid  = $row[2];
                                            $ternom = $row[3];
                                            $emest  = $row[4];
                                            $estid  = $row[5];
                                            $estnom = $row[6];
                                            $emcrc  = $row[7];
                                            $rcid   = $row[8];
                                            $rcnom  = $row[9];
                                            $emmp   = $row[10];
                                            $mpid   = $row[11];
                                            $mpnom  = $row[12];
                                            $emue   = $row[13];
                                            $ueid   = $row[14];
                                            $uenom  = $row[15];
                                            $emgg   = $row[16];
                                            $ggid   = $row[17];
                                            $ggnom  = $row[18];
                                            $emci   = $row[19];
                                            

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_EMPLEADO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                            <a href="GN_EMPLEADO_CC.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Centro Costo" class="glyphicon glyphicon-subtitles" ></i>
                                            </a>
                                             <!--Se agrego icono para generar hoja de vida-->
                                            <a href="informes_nomina/generar_INF_HOJA_DE_VIDA.php?id=<?php echo $row[20];?>" target="_blank">
                                                <i title="Hoja de vida" class="glyphicon glyphicon-file" ></i>
                                            </a>
                                           </td>                                        
                                        <td class="campos"><?php echo $ternom?></td>                
                                        <td class="campos"><?php echo $emci?></td>                
                                        <td class="campos"><?php echo $estnom?></td>                
                                        <td class="campos"><?php echo $rcnom?></td>                
                                        <td class="campos"><?php echo $mpnom?></td>                
                                        <td class="campos"><?php echo $uenom?></td>                
                                        <td class="campos"><?php echo $ggnom?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_EMPLEADO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Empleado?</p>
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
                  url:"json/eliminarEmpleadoJson.php?id="+id,
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
        document.location = 'listar_GN_EMPLEADO.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_EMPLEADO.php';
      });   

      function reportePdf(){
    $('form').attr('action', 'informes/generar_INF_HOJA_DE_VIDA.php?t=1');
} 
  </script>
    </body>
</html>