<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#31/05/2018 | Erica G. | Exogenas
####/################################################################################
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$anno = $_SESSION['anno'];
$query = 'SELECT 
        tr.id_unico, 
        tr.nombre, 
        tr.porcentajebase, 
        tr.limiteinferior, 
        tr.porcentajeaplicar, 
        tr.valoraplicar, 
        tr.factorredondeo, 
        tr.descripcion, 
        tr.modificarretencion, 
        tr.modificarbase, 
        tr.ley1450, 
        cr.id_unico, 
        cr.nombre, 
        fa.id_unico, 
        fa.nombre, 
        tb.id_unico, 
        tb.nombre, 
        c.id_unico, 
        c.codi_cuenta, 
        c.nombre,
        cn.nombre, 
        tr.cod_exogena  , 
        cc.codi_cuenta, cc.nombre ,tr.concepto_pago
        FROM gf_tipo_retencion tr 
        LEFT JOIN gf_clase_retencion cr ON tr.claseretencion=cr.id_unico 
        LEFT JOIN gf_factor_aplicacion fa ON tr.factoraplicacion=fa.id_unico 
        LEFT JOIN gf_tipo_base tb ON tr.tipobase = tb.id_unico 
        LEFT JOIN gf_cuenta c ON tr.cuenta= c.id_unico
        LEFT JOIN gf_concepto cn ON tr.concepto_ingreso_hom = cn.id_unico 
        LEFT JOIN gf_cuenta cc ON tr.cuenta_credito = cc.id_unico 
		WHERE tr.parametrizacionanno = '.$anno; 
$resultado = $mysqli->query($query);

?>
<style>
    body{
        font-size: 12px;
    }
</style>
<title>Listar Tipo Retención</title>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:-1px">Tipo Retención</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Porcentaje base</strong></td>
                                    <td><strong>Límite inferior</strong></td>
                                    <td><strong>Porcentaje aplicar</strong></td>
                                    <td><strong>Valor aplicar</strong></td>
                                    <td><strong>Factor redondeo</strong></td>
                                    <td><strong>Descripción</strong></td>
                                    <td><strong>Modificar Retención</strong></td>
                                    <td><strong>Modificar Base</strong></td>
                                    <td><strong>Aplica Ley 1450</strong></td>
                                    <td><strong>Clase retención</strong></td>
                                    <td><strong>Factor aplicación</strong></td>
                                    <td><strong>Tipo base</strong></td>
                                    <td><strong>Cuenta</strong></td>
                                    <td><strong>Concepto Ingreso Homologado</strong></td>
                                    <td><strong>Código Exógenas</strong></td>
                                    <td><strong>Cuenta Crédito</strong></td>
                                    <td><strong>Concepto Pago</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>
                                    <th>Porcentaje base</th>
                                    <th>Límite inferior</th>
                                    <th>Porcentaje aplicar</th>
                                    <th>Valor aplicar</th>
                                    <th>Factor redondeo</th>
                                    <th>Descripción</th>
                                    <th>Modificar Retención</th>
                                    <th>Modificar Base</th>
                                    <th>Aplica Ley 1450</th>
                                    <th>Clase retención</th>
                                    <th>Factor aplicación</th>
                                    <th>Tipo base</th>
                                    <th>Cuenta</th>
                                    <th>Concepto Ingreso Homologado</th>
                                    <th>Código Exógenas</th>
                                    <th>Cuenta Crédito</th>
                                    <th>Concepto Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_row($resultado)){?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td class="campos">
                                        <a  href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="Modificar_GF_TIPO_RETENCION.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower(($row[1])));?></td>
                                    <td class="campos" align="right" ><?php echo $row[2] .'%';?></td>
                                    <td class="campos" align="right"><?php echo $row[3];?></td>
                                    <td class="campos" align="right"><?php echo $row[4].'%';?></td>
                                    <td class="campos" align="right"><?php echo $row[5];?></td>
                                    <td class="campos" align="right"><?php echo $row[6];?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower(($row[7])));?></td>
                                    <td class="campos"><?php if($row[8]=='1') { echo 'Sí'; } else { echo 'No';}?></td>
                                    <td class="campos"><?php if($row[9]=='1') { echo 'Sí'; } else { echo 'No';}?></td>
                                    <td class="campos"><?php if($row[10]=='1') { echo 'Sí'; } else { echo 'No';}?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower(($row[12])));?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower(($row[14])));?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower(($row[16])));?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower(($row[18].' - '.$row[19])));?></td>
                                    <?php if(empty($row[20])){
                                            $nombre_con = '';
                                        }else{
                                            $nombre_con = $row[20];
                                        } ?>
                                    <td class="campos"><?php echo ucwords(mb_strtolower(($nombre_con)));?></td>
                                    <td class="campos"><?php echo $row[21];?></td>
                                    <td class="campos"><?php echo $row[22].' - '.$row[23];?></td>
                                    <td class="campos"><?php echo $row[24];?></td>
                                    
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="Registrar_GF_TIPO_RETENCION.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 5px; margin-bottom:10px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once ('footer.php'); ?>
    <!-- Divs de clase Modal para las ventanillas de eliminar. -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Tipo Retención?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
    <!-- Función para la eliminación del registro. -->
    <script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GF_TIPO_RETENCIONJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true){
                      $("#myModal1").modal('show');
                      $('#ver1').click(function(){
                        document.location = 'LISTAR_GF_TIPO_RETENCION.php';
                      });
                  } else{ 
                      $("#myModal2").modal('show');
                      $('#ver2').click(function(){
                        document.location ='LISTAR_GF_TIPO_RETENCION.php';
                      });
                  }
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
</body>
</html>


