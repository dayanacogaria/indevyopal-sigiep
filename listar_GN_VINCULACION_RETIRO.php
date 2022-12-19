<?php
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT        vr.id_unico,
                        vr.numeroacto,
                        vr.fechaacto,
                        vr.fecha,
                        vr.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        vr.tipovinculacion,
                        tv.id_unico,
                        tv.nombre,
                        vr.estado,
                        evr.id_unico,
                        evr.nombre,
                        vr.vinculacionretiro,
                        tvr.id_unico,
                        tvr.numeroacto,
                        vr.causaretiro,
                        cr.id_unico,
                        cr.nombre
                FROM gn_vinculacion_retiro vr
                LEFT JOIN	gn_empleado e                     ON vr.empleado          = e.id_unico
                LEFT JOIN   gf_tercero t                      ON e.tercero            = t.id_unico
                LEFT JOIN   gn_tipo_vinculacion tv            ON vr.tipovinculacion   = tv.id_unico
                LEFT JOIN   gn_estado_vinculacion_retiro evr  ON vr.estado            = evr.id_unico
                LEFT JOIN   gn_vinculacion_retiro tvr         ON vr.vinculacionretiro = tvr.id_unico
                LEFT JOIN   gn_causa_retiro cr                ON vr.causaretiro       = cr.id_unico";
    $resultado = $mysqli->query($sql);


    
?>
    <title>Listar Vinculación Retiro</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Vinculación Retiro</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="cabeza" style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Número Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Tipo Vinculación</strong></td>
                                        <td class="cabeza"><strong>Estado Vinculación</strong></td>
                                        <td class="cabeza"><strong>Vinculación Retiro</strong></td>
                                        <td class="cabeza"><strong>Causa Retiro</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>                                        
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">Número Acto</th>
                                        <th class="cabeza">Fecha Acto</th>
                                        <th class="cabeza">Fecha</th>
                                        <th class="cabeza">Tipo Vinculación</th>
                                        <th class="cabeza">Estado Vinculación</th>
                                        <th class="cabeza">Vinculación Retiro</th>
                                        <th class="cabeza">Causa Retiro</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                            $vrfec = $row[3];
                                            $vrfec = trim($vrfec, '"');
                                            $fecha_div = explode("-", $vrfec);
                                            $aniof = $fecha_div[0];
                                            $mesf  = $fecha_div[1];
                                            $diaf  = $fecha_div[2];
                                            $vrfec  = $diaf.'/'.$mesf.'/'.$aniof;
                                        
                                            $vfa = $row[2];
                                            $vfa = trim($vfa, '"');
                                            $fecha_div = explode("-", $vfa);
                                            $aniofa = $fecha_div[0];
                                            $mesfa = $fecha_div[1];
                                            $diafa = $fecha_div[2];
                                            $vfa = $diafa.'/'.$mesfa.'/'.$aniofa;
                                        
                                            $vrid   = $row[0];
                                            $vrnact = $row[1];
                                            #$vrfact = $row[2];
                                            #$vrfec  = $row[3];
                                            $vremp  = $row[4];
                                            $empid  = $row[5];
                                            $empter = $row[6];
                                            $terid  = $row[7];
                                            $ternom = $row[8];
                                            $vrtip  = $row[9];
                                            $tvid   = $row[10];
                                            $tvnom  = $row[11];
                                            $vrest  = $row[12];
                                            $evrid  = $row[13];
                                            $evrnom = $row[14];
                                            $vrv    = $row[15];
                                            $tvrid  = $row[16];
                                            $tvrnum = $row[17];
                                            $vrcr   = $row[18];
                                            $crid   = $row[19];
                                            $crnom  = $row[20];

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_VINCULACION_RETIRO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>   
                                        <td class="campos"><?php echo $ternom?></td>                
                                        <td class="campos"><?php echo $vrnact?></td>                
                                        <td class="campos"><?php echo $vfa?></td>                   
                                        <td class="campos"><?php echo $vrfec?></td>                
                                        <td class="campos"><?php echo $tvnom?></td>                
                                        <td class="campos"><?php echo $evrnom?></td>                
                                        <td class="campos"><?php echo $tvrnum?></td>                
                                        <td class="campos"><?php echo $crnom?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_VINCULACION_RETIRO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Vinculación Retiro?</p>
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
                  url:"json/eliminarVinculacionRetiroJson.php?id="+id,
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
        document.location = 'listar_GN_VINCULACION_RETIRO.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_VINCULACION_RETIRO.php';
      });    
  </script>
    </body>
</html>