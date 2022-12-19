<?php
require_once './Conexion/conexion.php';
require_once './head_listar.php';
$compania = $_SESSION['compania'];
?>
    <title>Listar Tipo Factura</title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Tipo Factura</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="cabeza" style="display: none;">Identificador</td>
                                        <td class="cabeza" width="7%" ></td>
                                        <td class="cabeza"><strong>Nombre</strong></td>
                                        <td class="cabeza"><strong>Prefijo</strong></td>
                                        <td class="cabeza"><strong>Clase Factura</strong></td>
                                        <td class="cabeza"><strong>Tipo Comprobante</strong></td>
                                        <td class="cabeza"><strong>Tipo Recaudo</strong></td>
                                        <td class="cabeza"><strong>Tipo Movimiento</strong></td>
                                        <td class="cabeza"><strong>Sigue Consecutivo</strong></td>
                                        <td class="cabeza"><strong>Servicio</strong></td>                                        
                                        <td class="cabeza"><strong>Aplica Descuento</strong></td>
                                        <td class="cabeza"><strong>Automático</strong></td>
                                        <td class="cabeza"><strong>Tipo Cambio</strong></td>
                                        <td class="cabeza"><strong>Facturación Electrónica</strong></td>
                                        
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <th class="cabeza">Nombre</th>
                                        <th class="cabeza">Prefijo</th>
                                        <th class="cabeza">Clase Factura</th>
                                        <th class="cabeza">Tipo Comprobante </th>
                                        <th class="cabeza">Tipo Recaudo</th>
                                        <th class="cabeza">Tipo Movimiento</th>
                                        <th class="cabeza">Sigue Consecutivo</th>
                                        <th class="cabeza">Servicio</th>
                                        <th class="cabeza">Aplica Descuento</th>
                                        <th class="cabeza">Automático</th>
                                        <th class="cabeza">Tipo Cambio</th>
                                        <th class="cabeza">Facturación Electrónica</th>
                                    </tr>
                                   
                                </thead>
                                <tbody>
                                    <?php 
                                    $sql = "SELECT tpf.id_unico,tpf.nombre, UPPER(tpf.prefijo), cf.nombre,	
                                            UPPER(tpc.sigla), tpc.nombre, 
                                            tr.nombre, UPPER(tm.sigla), tm.nombre,
                                        IF(tpf.sigue_consecutivo=1, 'Sí', 'No') cons,
                                        IF(tpf.servicio=1, 'Sí', 'No') serv,
                                        IF(tpf.xDescuento=1, 'Sí', 'No') des,
                                        IF(tpf.automatico=1, 'Sí', 'No') aut, 
                                        tc.nombre , 
                                        IF(tpf.facturacion_e=1, 'Sí', 'No') fe 
                                    FROM gp_tipo_factura tpf 
                                    LEFT JOIN gf_tipo_comprobante tpc ON tpc.id_unico = tpf.tipo_comprobante 
                                    LEFT JOIN gp_tipo_pago tr ON tr.id_unico = tpf.tipo_recaudo 
                                    LEFT JOIN gp_clase_factura cf ON tpf.clase_factura = cf.id_unico
                                    LEFT JOIN gf_tipo_movimiento tm ON tpf.tipo_movimiento = tm.id_unico 
                                    LEFT JOIN gf_tipo_cambio tc ON tpf.tipo_cambio = tc.id_unico 
                                    WHERE  tpf.compania = $compania";
                                    $resultado = $mysqli->query($sql);
                                    while ($row = mysqli_fetch_row($resultado)) { ?>
                                    <tr>
                                        <td class="campos" style="display: none;"><?php echo $row[0]?></td>
                                        <td class="campos">
                                            <a class="campos" href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a class="campos" href="modificar_GP_TIPO_FACTURA.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[1]));?></td>
                                        <td class="campos"><?php echo $row[2]?></td>
                                        <td class="campos"><?php echo $row[3]?></td>
                                        <td class="campos"><?php echo $row[4].' - '.$row[5];?></td>
                                        <td class="campos"><?php echo $row[6]?></td>
                                        <td class="campos"><?php echo $row[7].' - '.$row[8];?></td>
                                        <td class="campos"><?php echo $row[9]?></td>
                                        <td class="campos"><?php echo $row[10]?></td>
                                        <td class="campos"><?php echo $row[11]?></td>
                                        <td class="campos"><?php echo $row[12]?></td>
                                        <td class="campos"><?php echo $row[13]?></td>
                                        <td class="campos"><?php echo $row[14]?></td>
                                    </tr>
                                    <?php }  ?>
                                </tbody>
                            </table>
                            <div align="right">
                                 <a href="registrar_GP_TIPO_FACTURA.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Tipo Factura?</p>
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
                  url:"json/eliminarTipoFacturaJson.php?id="+id,
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
        document.location = 'listar_GP_TIPO_FACTURA.php';
      });
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GP_TIPO_FACTURA.php';
      });    
  </script>
    </body>
</html>
 

