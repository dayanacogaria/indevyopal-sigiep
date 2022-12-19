<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#25/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
require_once './head_listar.php';
$sql = "SELECT 
    t.id_unico, 
    t.razonsocial,
    t.tipoidentificacion,
    ti.id_unico,
    ti.nombre,
    t.numeroidentificacion,
    t.sucursal,
    s.id_unico,
    s.nombre,
    t.tiporegimen,
    tr.id_unico,
    tr.nombre,
    t.tipoempresa,
    tem.id_unico,
    tem.nombre,
    t.tipoentidad,
    ten.id_unico,
    ten.nombre,
    t.representantelegal,
    r.id_unico,
    CONCAT(r.nombreuno,' ',r.nombredos,' ',r.apellidouno,' ',r.apellidodos),
    r.ciudadidentificacion,
    t.contacto,
    c.id_unico,
    CONCAT(c.nombreuno,' ',c.nombredos,' ',c.apellidouno,' ',c.apellidodos),
    t.zona,
    z.id_unico,
    z.nombre
FROM gf_perfil_tercero pt
LEFT JOIN gf_tercero t  		 	ON pt.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico        
LEFT JOIN gf_sucursal s             ON t.sucursal = s.id_unico
LEFT JOIN gf_tipo_regimen tr        ON t.tiporegimen = tr.id_unico
LEFT JOIN gf_tipo_empresa tem       ON t.tipoempresa = tem.id_unico
LEFT JOIN gf_tipo_entidad ten       ON t.tipoentidad = ten.id_unico
LEFT JOIN gf_tercero r  			ON t.representantelegal = r.id_unico
LEFT JOIN gf_tercero c  			ON t.contacto = c.id_unico
LEFT JOIN gf_zona z     			ON t.zona = z.id_unico
WHERE pt.perfil = 12";
$resultado = $mysqli->query($sql);
?>
        <title>Listar Entidad Financiera</title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Entidad Financiera</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Identificación</strong></td>
                                        <td class="cabeza"><strong>Razón Social</strong></td>
                                        <td class="cabeza"><strong>Sucursal</strong></td>
                                        <td class="cabeza"><strong>Tipo Régimen</strong></td>
                                        <td class="cabeza"><strong>Tipo Empresa</strong></td>
                                        <td class="cabeza"><strong>Tipo Entidad</strong></td>
                                        <td class="cabeza"><strong>Representante Legal</strong></td>
                                        <td class="cabeza"><strong>Ciudad Ident.</strong></td>
                                        <td class="cabeza"><strong>Contacto</strong></td>
                                        <td class="cabeza"><strong>Zona</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th class="cabeza">Identificación</th>
                                        <th class="cabeza">Razón Social</th>
                                        <th class="cabeza">Sucursal</th>
                                        <th class="cabeza">Tipo Régimen</th>
                                        <th class="cabeza">Tipo Empresa</th>
                                        <th class="cabeza">Tipo Entidad</th>
                                        <th class="cabeza">Representante Legal</th>
                                        <th class="cabeza">Ciudad Ident.</th>
                                        <th class="cabeza">Contacto</th>
                                        <th class="cabeza">Zona</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) {
                                            
                                        $aid  = $row[0];
                                        $aval = $row[1];
                                        $aind = $row[2];
                                        $atar = $row[3];
                                        $tid  = $row[4];
                                        $tnom = $row[5];
                                        $apre = $row[6];
                                        $pid  = $row[7];
                                        $pnom = $row[8];
                                            
                                    ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GF_TERCERO_ENTIDAD_FINANCIERA.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <td class="campos"><?php echo $row[5]?></td>                
                                        <td class="campos"><?php echo $row[1]?></td>                
                                        <td class="campos"><?php echo $row[8]?></td>                
                                        <td class="campos"><?php echo $row[11]?></td>                
                                        <td class="campos"><?php echo $row[14]?></td>                
                                        <td class="campos"><?php echo $row[17]?></td>                
                                        <td class="campos"><?php echo $row[20]?></td>                
                                        <td class="campos"><?php echo $row[21]?></td>                
                                        <td class="campos"><?php echo $row[24]?></td>                
                                        <td class="campos"><?php echo $row[27]?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GF_TERCERO_ENTIDAD_FINANCIERA.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Entidad Financiera?</p>
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
                  url:"json/eliminarEntidadFinancieraJson.php?id="+id,
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
        document.location = 'listar_GF_TERCERO_ENTIDAD_FINANCIERA.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GF_TERCERO_ENTIDAD_FINANCIERA.php';
      });    
  </script>
    </body>
</html>