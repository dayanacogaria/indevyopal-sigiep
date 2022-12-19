<?php

################ MODIFICACIONES ####################
#04/06/2017    | Anderson Alarcon | Modifique consulta para listar los 'concepto resolucion' 
####################################################

require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';
                                     

$sql="SELECT cr.id_unico,cr.valoranterior,cr.valoractual,
	 p.codigo_catastral,r.observaciones,tcp.nombre,tm.nombre AS nombreTipoModificacion
FROM gr_concepto_resolucion cr 
LEFT JOIN gr_resolucion_predio rp ON cr.resolucionconcepto=rp.id_unico
LEFT JOIN gr_resolucion r ON rp.resolucion=r.id_unico
LEFT JOIN gp_predio1 p ON rp.predio=p.id_unico
LEFT JOIN gr_tipo_concepto_predial tcp ON cr.tipoconcepto=tcp.id_unico
LEFT JOIN gr_tipo_modificacion tm ON cr.tipomodificacion=tm.id_unico";
/*$sql = "SELECT          cr.id_unico,
                        cr.valoranterior,
                        cr.valoractual,
                        cr.resolucionconcepto,
                        rp.id_unico,
                        rp.resolucion,
                        r.id_unico,
                        r.numero,
                        rp.predio,
                        p.id_unico,
                        p.codigoigac,
                        cr.tipoconcepto,
                        tc.id_unico,
                        tc.nombre,
                        cr.tipomodificacion,
                        tm.id_unico,
                        tm.nombre,
                        CONCAT (r.numero,' - ',p.codigoigac)
    FROM gr_concepto_resolucion cr
    LEFT JOIN gr_resolucion_predio rp     ON cr.resolucionconcepto  = rp.id_unico
    LEFT JOIN gr_resolucion r 		      ON rp.resolucion = r.id_unico
    LEFT JOIN gp_predio1 p                ON rp.predio = p.id_unico
    LEFT JOIN gr_tipo_concepto_predial tc ON cr.tipoconcepto = tc.id_unico
    LEFT JOIN gr_tipo_modificacion tm     ON cr.tipomodificacion = tm.id_unico";*/
    
$resultado  = $mysqli->query($sql);
?>
        <title>Listar Concepto Resolución</title>
    </head>
    <body>
        
        
        <!--Anderson-->
         <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Concepto Resolución</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Valor Anterior</strong></td>
                                        <td class="cabeza"><strong>Valor Actual</strong></td>
                                        <td class="cabeza"><strong>Resolución Concepto</strong></td>
                                        <td class="cabeza"><strong>Tipo Concepto</strong></td>
                                        <td class="cabeza"><strong>Tipo Modificación</strong></td>    
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th class="cabeza">Valor Anterior</th>
                                        <th class="cabeza">Valor Actual</th>
                                        <th class="cabeza">Resolución Concepto</th>
                                        <th class="cabeza">Tipo Concepto</th>
                                        <th class="cabeza">Tipo Modificación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_array($resultado)) { 
                                    ?>
                                     <tr>
                                        <td style="display: none;"><?php echo $row['id_unico']?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row['id_unico'];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GR_CONCEPTO_RESOLUCION.php?id=<?php echo md5($row['id_unico']);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <td class="campos"><?php echo $row['valoranterior']?></td>                
                                        <td class="campos"><?php echo $row['valoractual'] ?></td>                
                                        <td class="campos"><?php echo $row['codigo_catastral']." - ".$row['observaciones'] ?></td>                
                                        <td class="campos"><?php echo $row['nombre'] ?></td>                
                                        <td class="campos"><?php echo $row['nombreTipoModificacion'] ?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GR_CONCEPTO_RESOLUCION.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End Anderson-->
        
        
        <!--Daniel-->
       
        <!--End Daniel-->
        <?php require_once './footer.php'; ?>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Concepto Resolución?</p>
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
                  url:"json/eliminarConceptoResolucionPJson.php?id="+id,
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
        document.location = 'listar_GR_CONCEPTO_RESOLUCION.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GR_CONCEPTO_RESOLUCION.php';
      });    
  </script>
    </body>
</html>