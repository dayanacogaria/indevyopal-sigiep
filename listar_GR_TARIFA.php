<?php


################ MODIFICACIONES ####################
#14/06/2017 | Anderson Alarcon | cambie consulta en  tarifa 
############################################

require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';
                                     
$sql = "SELECT          t.id_unico,
                        t.nombre,
                        t.anio,
                        t.codigotarifa,
                        t.limiteinferior,
                        t.limitesuperior,
                        t.porcentajeincremento,
                        t.valor,
                        t.porcentajesobretasa,
                        t.porcentajeimpuestoambiental,
                        t.baseimpuesto,
                        t.baseambiental,
                        t.estrato,
                        e.id_unico,
                        e.nombre,
                        t.ley44,
                        l.id_unico,
                        l.nombre,
                        t.tipobaserango,
                        tb1.id_unico,
                        tb1.nombre,
                        t.tipobaseambiental,
                        tb2.id_unico,
                        tb2.nombre,
                        t.tipobasecalculo,
                        tb3.id_unico,
                        tb3.nombre
    FROM gr_tarifa t
    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
    LEFT JOIN gr_ley_44 l ON t.ley44 = l.id_unico
    LEFT JOIN gr_tipo_base tb1 ON t.tipobaserango = tb1.id_unico
    LEFT JOIN gr_tipo_base tb2 ON t.tipobaseambiental = tb2.id_unico
    LEFT JOIN gr_tipo_base tb3 ON t.tipobasecalculo = tb3.id_unico";
$resultado = $mysqli->query($sql);
?>
        <title>Listar Tarifa</title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Tarifa</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Nombre</strong></td>
                                        <td class="cabeza"><strong>Año</strong></td>
                                        <td class="cabeza"><strong>Código Tarifa</strong></td>
                                        <td class="cabeza"><strong>Límite Inferior</strong></td>
                                        <td class="cabeza"><strong>Límite Superior</strong></td>
                                        <td class="cabeza"><strong>% Incremento</strong></td>
                                        <td class="cabeza"><strong>Valor</strong></td>
                                        <td class="cabeza"><strong>% Sobretasa</strong></td>
                                        <td class="cabeza"><strong>% Imp. Ambiental</strong></td>
                                        <td class="cabeza"><strong>Base Impuesto</strong></td>
                                        <td class="cabeza"><strong>Base Ambiental</strong></td>
                                        <td class="cabeza"><strong>Estrato</strong></td>
                                        <td class="cabeza"><strong>Ley 44</strong></td>
                                        <td class="cabeza"><strong>Tipo Base Rango</strong></td>
                                        <td class="cabeza"><strong>Tipo Base Ambiental</strong></td>
                                        <td class="cabeza"><strong>Tipo Base Cálculo</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th class="cabeza">Nombre</th>
                                        <th class="cabeza">Año</th>
                                        <th class="cabeza">Código Tarifa</th>
                                        <th class="cabeza">Límite Inferior</th>
                                        <th class="cabeza">Límite Superior</th>
                                        <th class="cabeza">% Incremento</th>
                                        <th class="cabeza">Valor</th>
                                        <th class="cabeza">% Sobretasa</th>
                                        <th class="cabeza">% Imp. Ambiental</th>
                                        <th class="cabeza">Base Impuesto</th>
                                        <th class="cabeza">Base Ambiental</th>
                                        <th class="cabeza">Estrato</th>
                                        <th class="cabeza">Ley 44</th>
                                        <th class="cabeza">Tipo Base Rango</th>
                                        <th class="cabeza">Tipo Base Ambiental</th>
                                        <th class="cabeza">Tipo Base Cálculo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                    $tid    = $row[0];
                                    $tnom   = $row[1];
                                    $tanio  = $row[2];
                                    $tctar  = $row[3];
                                    $tlimi  = $row[4];
                                    $tlims  = $row[5];
                                    $tpori  = $row[6];
                                    $tval   = $row[7];
                                    $tpors  = $row[8];
                                    $tporia = $row[9];
                                    $tbasei = $row[10];
                                    $tbasea = $row[11];
                                    $testr  = $row[12];
                                    $eid    = $row[13];
                                    $enom   = $row[14];
                                    $tley   = $row[15];
                                    $lid    = $row[16];
                                    $lnom   = $row[17];
                                    $ttbasr = $row[18];
                                    $tb1id  = $row[19];
                                    $tb1nom = $row[20];
                                    $ttbasa = $row[21];
                                    $tb2id  = $row[22];
                                    $tb2nom = $row[23];
                                    $ttbasc = $row[24];
                                    $tb3id  = $row[25];
                                    $tb3nom = $row[26];
                                    ?>
                                     <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GR_TARIFA.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <td class="campos"><?php echo $tnom  ?></td>                
                                        <td class="campos"><?php echo $tanio ?></td>                
                                        <td class="campos"><?php echo $tctar ?></td>                
                                        <td class="campos"><?php echo $tlimi ?></td>                
                                        <td class="campos"><?php echo $tlims ?></td>                
                                        <td class="campos"><?php echo $tpori ?></td>                
                                        <td class="campos"><?php echo $tval  ?></td>                
                                        <td class="campos"><?php echo $tpors ?></td>                
                                        <td class="campos"><?php echo $tporia?></td>                
                                        <td class="campos"><?php echo $tbasei?></td>                
                                        <td class="campos"><?php echo $tbasea?></td>                
                                        <td class="campos"><?php echo $enom  ?></td>                
                                        <td class="campos"><?php echo $lnom  ?></td>                
                                        <td class="campos"><?php echo $tb1nom?></td>                
                                        <td class="campos"><?php echo $tb2nom?></td>                
                                        <td class="campos"><?php echo $tb3nom?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GR_TARIFA.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Tarifa?</p>
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
                  url:"json/eliminarTarifaPJson.php?id="+id,
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
        document.location = 'listar_GR_TARIFA.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GR_TARIFA.php';
      });    
  </script>
    </body>
</html>