<?php
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT    es.id_unico,
                    es.empleado,
                    e.id_unico,
                    e.tercero,
                    t.id_unico,
                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                    es.titulo,
                    es.fechaterminacion,
                    es.numerosemestres,
                    es.graduado,
                    es.tarjetaprofesional,
                    es.tipo,
                    te.id_unico,
                    te.nombre,
                    es.institucioneducativa,
                    ie.id_unico,
                    ie.nombre
                FROM gn_estudio es	 
                LEFT JOIN	gn_empleado e               ON es.empleado = e.id_unico
                LEFT JOIN   gf_tercero t                ON e.tercero = t.id_unico
                LEFT JOIN   gn_tipo_estudio te          ON es.tipo = te.id_unico
                LEFT JOIN   gn_institucion_educativa ie ON es.institucioneducativa = ie.id_unico";

  $resultado = $mysqli->query($sql);
    
?>
    <title>Listar Estudio</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Estudio</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Título</strong></td>
                                        <td class="cabeza"><strong>Fecha Terminación</strong></td>
                                        <td class="cabeza"><strong>No. Semestres</strong></td>
                                        <td class="cabeza"><strong>Es Graduado</strong></td>
                                        <td class="cabeza"><strong>Tarjeta Profesional</strong></td>
                                        <td class="cabeza"><strong>Tipo</strong></td>
                                        <td class="cabeza"><strong>Institución Educativa</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">Título</th>
                                        <th class="cabeza">Fecha Terminación</th>
                                        <th class="cabeza">No. Semestres</th>
                                        <th class="cabeza">Es Graduado</th>
                                        <th class="cabeza">Tarjeta Profesional</th>
                                        <th class="cabeza">Tipo</th>
                                        <th class="cabeza">Institución Educativa</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                        $esfec = $row[7];
                                        $esfec = trim($esfec, '"');
                                        $fecha_div = explode("-", $esfec);
                                        $anioe = $fecha_div[0];
                                        $mese = $fecha_div[1];
                                        $diae = $fecha_div[2];
                                        $esfec = $diae.'/'.$mese.'/'.$anioe;
                                    
                                        $esid   = $row[0];
                                        $esemp  = $row[1];
                                        $eid    = $row[2];
                                        $eter   = $row[3];
                                        $tid    = $row[4];
                                        $tnom   = $row[5];
                                        $estit  = $row[6];
                                        #$esfec = $row[7];
                                        $esnum  = $row[8];
                                        $esgrad = $row[9];
                                        $estp   = $row[10];
                                        $estip  = $row[11];
                                        $teid   = $row[12];
                                        $tenom  = $row[13];
                                        $esie   = $row[14];
                                        $ieid   = $row[15];
                                        $ienom  = $row[16];
                                        
                                        if($esgrad==1)
                                            $grad = "SI";
                                        elseif($esgrad==2)
                                            $grad = "NO";
                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_ESTUDIO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <td class="campos"><?php echo $tnom?></td>                
                                        <td class="campos"><?php echo $estit?></td>                
                                        <td class="campos"><?php echo $esfec?></td>                
                                        <td class="campos"><?php echo $esnum?></td>                
                                        <td class="campos"><?php echo $grad?></td>                
                                        <td class="campos"><?php echo $estp?></td>                
                                        <td class="campos"><?php echo $tenom?></td>                
                                        <td class="campos"><?php echo $ienom?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_ESTUDIO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Estudio?</p>
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
                  url:"json/eliminarEstudioJson.php?id="+id,
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
        document.location = 'listar_GN_ESTUDIO.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_ESTUDIO.php';
      });    
  </script>
    </body>
</html>