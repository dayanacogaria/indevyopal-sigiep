<?php
require_once './Conexion/conexion.php';
require_once './head_listar.php';

$vig = $_SESSION['anno'];
$sql = "SELECT    p.id_unico,
                    p.codigointerno,
                    DATE_FORMAT(p.fechainicio, '%d/%m/%Y'),
                    DATE_FORMAT(p.fechafin,'%d/%m/%Y'),
                    IF(p.acumulable=1, 'Si', 'No'),
                    p.estado,
                    ep.id_unico,
                    ep.nombre,
                    p.parametrizacionanno,
                    p.tipoprocesonomina,
                    tpn.id_unico,
                    tpn.nombre, 
                    IF(p.liquidado=1, 'Si', 'No'), 
                    pr.codigointerno, 
                    p.dias_nomina
                FROM gn_periodo p
                LEFT JOIN   gn_estado_periodo ep        ON p.estado = ep.id_unico
                LEFT JOIN   gn_tipo_proceso_nomina tpn  ON p.tipoprocesonomina = tpn.id_unico
                LEFT JOIN   gn_periodo pr ON p.periodo_retro = pr.id_unico 
                WHERE p.id_unico != 1 AND p.parametrizacionanno = '$vig'";
    $resultado = $mysqli->query($sql);
    
?>
    <title>Listar Período</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Período</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Código Interno</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>Fecha Fin</strong></td>
                                        <td class="cabeza"><strong>Estado</strong></td>
                                        <td class="cabeza"><strong>Tipo</strong></td>
                                        <td class="cabeza"><strong>Acumulable</strong></td>
                                        <td class="cabeza"><strong>Días Nómina</strong></td>
                                        <td class="cabeza"><strong>Periodo Retroactivo</strong></td>
                                        <td class="cabeza"><strong>Liquidado</strong></td>
                                        
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Código Interno</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">Fecha Fin</th>
                                        <th class="cabeza">Estado</th>
                                        <th class="cabeza">Tipo</th>
                                        <th class="cabeza">Acumulable</th>
                                        <th class="cabeza">Días Nómina</th>
                                        <th class="cabeza">Periodo Retroactivo</th>
                                        <th class="cabeza">Liquidado</th>
                                        
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { ?>
                                    <tr>
                                        <td style="display: none;"><?= $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?= $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_PERIODO.php?id=<?= md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <td class="campos"><?= $row[1] ?></td>                
                                        <td class="campos"><?= $row[2]?></td>                
                                        <td class="campos"><?= $row[3]?></td>                
                                        <td class="campos"><?= $row[7]?></td>                
                                        <td class="campos"><?= $row[11]?></td>                
                                        <td class="campos"><?= $row[4]?></td>                
                                        <td class="campos"><?= $row[14]?></td>                
                                        <td class="campos"><?= $row[13]?></td>                
                                        <td class="campos"><?= $row[12]?></td>                
                                        
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_PERIODO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Período?</p>
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
                  url:"json/eliminarPeriodoJson.php?id="+id,
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
        document.location = 'listar_GN_PERIODO.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_PERIODO.php';
      });    
  </script>
    </body>
</html>