<?php
require_once './Conexion/conexion.php';
require_once './head_listar.php';
session_start();
$anno = $_SESSION['anno'];
$sql = "SELECT        v.id_unico,
      CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
      DATE_FORMAT(v.fechainicio, '%d/%m/%Y'),
      DATE_FORMAT(v.fechafin, '%d/%m/%Y'),
      DATE_FORMAT(v.fechainiciodisfrute, '%d/%m/%Y'),
      DATE_FORMAT(v.fechafindisfrute, '%d/%m/%Y'),
      v.numeroacto,
      DATE_FORMAT(v.fechaacto, '%d/%m/%Y'),
      tn.nombre, 
      v.dias_hab, 
      p.codigointerno 
FROM gn_vacaciones v
LEFT JOIN	gn_empleado e           ON v.empleado       = e.id_unico
LEFT JOIN   gf_tercero t            ON e.tercero        = t.id_unico
LEFT JOIN   gn_tipo_novedad tn      ON v.tiponovedad = tn.id_unico
LEFT JOIN gn_periodo p ON v.periodo = p.id_unico 
WHERE p.parametrizacionanno = $anno";
$resultado = $mysqli->query($sql);
?>
    <title>Listar Vacaciones</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Vacaciones</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="cabeza" style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>Fecha Fin</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio Disfrute</strong></td>
                                        <td class="cabeza"><strong>Fecha Fin Disfrute</strong></td>
                                        <td class="cabeza"><strong>Número Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha Acto</strong></td>
                                        <td class="cabeza"><strong>Novedad</strong></td>
                                        <td class="cabeza"><strong>Días Disfrute</strong></td>
                                        <td class="cabeza"><strong>Periodo</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">Fecha Fin</th>
                                        <th class="cabeza">Fecha Inicio Disfrute</th>
                                        <th class="cabeza">Fecha Fin Disfrute</th>
                                        <th class="cabeza">Número Acto</th>
                                        <th class="cabeza">Fecha Acto</th>
                                        <th class="cabeza">Novedad</th>
                                        <th class="cabeza">Días Disfrute</th>
                                        <th class="cabeza">Periodo</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php  
                                    while ($row = mysqli_fetch_row($resultado)) { ?>
                                    <tr>
                                        <td style="display: none;"><?=$row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?=$row[0]?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_VACACIONES.php?id=<?= md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <td class="campos"><?=$row[1]?></td>                
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
                                <a href="registrar_GN_VACACIONES.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Vacaciones?</p>
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
                  url:"json/eliminarVacacionesJson.php?id="+id,
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
        document.location = 'listar_GN_VACACIONES.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_VACACIONES.php';
      });    
  </script>
    </body>
</html>