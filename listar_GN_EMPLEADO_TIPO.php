<?php
#02/03/2017 --- Nestor B --- Se modificó el método para cambiar el formato de fehcha para que cuando venga vacía no genere error
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT        et.id_unico,
                        et.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        et.fechainicio,
                        et.fechacancelacion,
                        et.tipo,
                        te.id_unico,
                        te.nombre,
                        et.estado,
                        ee.id_unico,
                        ee.nombre,
                        et.observaciones
                FROM gn_empleado_tipo et	 
                LEFT JOIN	gn_empleado e           ON et.empleado = e.id_unico
                LEFT JOIN   gf_tercero t            ON e.tercero = t.id_unico
                LEFT JOIN   gn_tipo_empleado te     ON et.tipo = te.id_unico
                LEFT JOIN   gn_estado_empleado ee   ON et.estado = ee.id_unico";
    $resultado = $mysqli->query($sql);


    
?>
        <title>Listar Empleado Tipo</title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Empleado Tipo</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Estado</strong></td>
                                        <td class="cabeza"><strong>Tipo</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>Fecha Cancelación</strong></td>
                                        <td class="cabeza"><strong>Observaciones</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">Estado</th>
                                        <th class="cabeza">Tipo</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">Fecha Cancelación</th>
                                        <th class="cabeza">Observaciones</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                            $etfeci = $row[6];
                                            if(!empty($row[6])||$row[6]!=''){
                                            $etfeci = trim($etfeci, '"');
                                            $fecha_div = explode("-", $etfeci);
                                            $anioi = $fecha_div[0];
                                            $mesi = $fecha_div[1];
                                            $diai = $fecha_div[2];
                                            $etfeci = $diai.'/'.$mesi.'/'.$anioi;
                                          }else{
                                            $etfeci='';
                                          }
                                            
                                            $etfecc = $row[7];
                                            if(!empty($row[7])||$row[7]!=''){
                                            $etfecc = trim($etfecc, '"');
                                            $fecha_div = explode("-", $etfecc);
                                            $anioc = $fecha_div[0];
                                            $mesc = $fecha_div[1];
                                            $diac = $fecha_div[2];
                                            $etfecc = $diac.'/'.$mesc.'/'.$anioc;

                                          }else{
                                            $etfecc='';
                                          }
                                           

                                        
                                            $etid   = $row[0];
                                            $etemp  = $row[1];
                                            $eid    = $row[2];
                                            $eter   = $row[3];
                                            $terid  = $row[4];
                                            $ternom = $row[5];
                                            #$etfeci = $row[6];
                                            #$etfecc = $row[7];
                                            $ettip  = $row[8];
                                            $teid   = $row[9];
                                            $tenom  = $row[10];
                                            $etest  = $row[11];
                                            $eeid   = $row[12];
                                            $eenom  = $row[13];
                                            $etobs  = $row[14];

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_EMPLEADO_TIPO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <td class="campos"><?php echo $ternom?></td>                
                                        <td class="campos"><?php echo $eenom?></td>                
                                        <td class="campos"><?php echo $tenom?></td>                
                                        <td class="campos"><?php echo $etfeci?></td>                
                                        <td class="campos"><?php echo $etfecc?></td>                
                                        <td class="campos"><?php echo $etobs?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_EMPLEADO_TIPO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Empleado Tipo?</p>
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
                  url:"json/eliminarEmpleadoTipoJson.php?id="+id,
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
        document.location = 'listar_GN_EMPLEADO_TIPO.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_EMPLEADO_TIPO.php';
      });    
  </script>
    </body>
</html>