<?php
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT  i.id_unico,tn.nombre, CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                        i.numeroinc,
                        DATE_FORMAT(i.fechainicio, '%d/%m/%Y'),
                        DATE_FORMAT(i.fechafinal, '%d/%m/%Y'),
                        i.numerodias,
                        i.numeroaprobacion,
                        DATE_FORMAT(i.fechaaprobacion, '%d/%m/%Y'),
                        a.numradicado,
                        i.diagnostico,
                        MONTH(i.fechainicio)
                FROM gn_incapacidad i
                LEFT JOIN	gn_empleado e             ON i.empleado    = e.id_unico
                LEFT JOIN   gf_tercero t              ON e.tercero     = t.id_unico
                LEFT JOIN   gn_accidente a            ON i.accidente   = a.id_unico
                LEFT JOIN   gn_tipo_novedad tn        ON i.tiponovedad = tn.id_unico 
                ORDER BY i.id_unico DESC";
    $resultado = $mysqli->query($sql);    
?>
    <title>Listar Incapacidad</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Incapacidad / Licencia</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px; ">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                     <tr>
                                        <td class="cabeza" style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Tipo Novedad</strong></td>
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>No. Incapacidad</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>Fecha Final</strong></td>
                                        <td class="cabeza"><strong>No. Días</strong></td>
                                        <td class="cabeza"><strong>No. Aprobación</strong></td>
                                        <td class="cabeza"><strong>Fecha Aprobación</strong></td>
                                        <td class="cabeza"><strong>Accidente</strong></td>                                        
                                        <td class="cabeza"><strong>Diagnóstico</strong></td>
                                    </tr>
                                     <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>                                        
                                        <th class="cabeza">Tipo Novedad</th>
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">No. Incapacidad</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">Fecha Final</th>                                        
                                        <th class="cabeza">No. Días</th>
                                        <th class="cabeza">No. Aprobación</th>
                                        <th class="cabeza">Fecha Aprobación</th>
                                        <th class="cabeza">Accidente</th>                                        
                                        <th class="cabeza">Diagnóstico</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) {  ?>
                                    <tr>
                                        <td class="campos" style="display: none;"><?php echo $row[11]?></td>
                                        <td class="campos">
                                            <a class="campos" href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                        </td>   
                                        <td class="campos"><?=utf8_decode($row[1]);?></td>                
                                        <td class="campos"><?=$row[2]?></td>                
                                        <td class="campos"><?=$row[3]?></td>                
                                        <td class="campos"><?=$row[4]?></td>                
                                        <td class="campos"><?=$row[5]?></td>                
                                        <td class="campos"><?=$row[6]?></td>                
                                        <td class="campos"><?=$row[7]?></td>                
                                        <td class="campos"><?=$row[8]?></td>                
                                        <td class="campos"><?=$row[9]?></td>                
                                        <td class="campos"><?=$row[10]?></td>                                        
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a onclick="javascript:abrirInLi()" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Incapacidad?</p>
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
                  url:"json/eliminarIncapacidadJson.php?id="+id,
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
        document.location = 'listar_GN_INCAPACIDAD.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_INCAPACIDAD.php';
      });    
  </script>
    </body>
</html>