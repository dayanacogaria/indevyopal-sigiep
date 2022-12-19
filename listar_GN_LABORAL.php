<?php
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT        l.id_unico,
                        l.fechaingreso,
                        l.fecharetiro,
                        l.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        l.entidad,
                        en.id_unico,
                        en.nombre,
                        l.dependencia,
                        de.id_unico,
                        de.nombre,
                        l.cargo,
                        ca.id_unico,
                        ca.nombre,
                        l.causaretiro,
                        cr.id_unico,
                        cr.nombre,
                        l.tipodedicacion,
                        td.id_unico,
                        td.nombre
                FROM gn_laboral l	 
                LEFT JOIN	gn_empleado e           ON l.empleado       = e.id_unico
                LEFT JOIN   gf_tercero t            ON e.tercero        = t.id_unico
                LEFT JOIN   gn_entidad en           ON l.entidad        = en.id_unico
                LEFT JOIN   gn_dependencia_empleado de ON l.dependencia = de.id_unico
                LEFT JOIN   gf_cargo ca             ON l.cargo          = ca.id_unico
                LEFT JOIN   gn_causa_retiro cr      ON l.causaretiro    = cr.id_unico
                LEFT JOIN   gn_tipo_dedicacion td   ON l.tipodedicacion = td.id_unico";
    $resultado = $mysqli->query($sql);


    
?>
    <title>Listar Laboral</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Laboral</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Entidad</strong></td>
                                        <td class="cabeza"><strong>Fecha Ingreso</strong></td>
                                        <td class="cabeza"><strong>Fecha Retiro</strong></td>
                                        <td class="cabeza"><strong>Dependencia Empleado</strong></td>
                                        <td class="cabeza"><strong>Tipo Dedicación</strong></td>
                                        <td class="cabeza"><strong>Cargo</strong></td>
                                        <td class="cabeza"><strong>Causa Retiro</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">Entidad</th>
                                        <th class="cabeza">Fecha Ingreso</th>
                                        <th class="cabeza">Fecha Retiro</th>
                                        <th class="cabeza">Dependencia Empleado</th>
                                        <th class="cabeza">Tipo Dedicación</th>
                                        <th class="cabeza">Cargo</th>
                                        <th class="cabeza">Causa Retiro</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                            $lfi = $row[1];
                                            $lfi = trim($lfi, '"');
                                            $fecha_div = explode("-", $lfi);
                                            $anioi = $fecha_div[0];
                                            $mesi = $fecha_div[1];
                                            $diai = $fecha_div[2];
                                            $lfi = $diai.'/'.$mesi.'/'.$anioi;
                                        
                                            $lfr = $row[1];
                                            $lfr = trim($lfr, '"');
                                            $fecha_div = explode("-", $lfr);
                                            $anior = $fecha_div[0];
                                            $mesr = $fecha_div[1];
                                            $diar = $fecha_div[2];
                                            $lfr = $diar.'/'.$mesr.'/'.$anior;
                                        
                                            $lid    = $row[0];
                                            #$lfi    = $row[1];
                                            #$lfr    = $row[2];
                                            $lemp   = $row[3];
                                            $empid  = $row[4];
                                            $empter = $row[5];
                                            $terid  = $row[6];
                                            $ternom = $row[7];
                                            $len    = $row[8];
                                            $enid   = $row[9];
                                            $enom   = $row[10];
                                            $ldep   = $row[11];
                                            $deid   = $row[12];
                                            $denom  = $row[13];
                                            $lca    = $row[14];
                                            $caid   = $row[15];
                                            $canom  = $row[16];
                                            $lcr    = $row[17];
                                            $crid   = $row[18];
                                            $crnom  = $row[19];       
                                            $ltd    = $row[20];
                                            $tdid   = $row[21];
                                            $tdnom  = $row[22];

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_LABORAL.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <td class="campos"><?php echo $ternom?></td>                
                                        <td class="campos"><?php echo $enom?></td>                
                                        <td class="campos"><?php echo $lfi?></td>                
                                        <td class="campos"><?php echo $lfr?></td>                
                                        <td class="campos"><?php echo $denom?></td>                
                                        <td class="campos"><?php echo $tdnom?></td>                
                                        <td class="campos"><?php echo $canom?></td>                
                                        <td class="campos"><?php echo $crnom?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_LABORAL.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Laboral?</p>
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
                  url:"json/eliminarLaboralJson.php?id="+id,
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
        document.location = 'listar_GN_LABORAL.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_LABORAL.php';
      });    
  </script>
    </body>
</html>