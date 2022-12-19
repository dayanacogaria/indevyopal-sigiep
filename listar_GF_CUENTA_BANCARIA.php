<?php 
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
#03/03/2017 |ERICA G. |MODIFICACION CONSULTA
######################################################################################################
require_once 'head_listar.php';
require_once ('Conexion/conexion.php');
$anno = $_SESSION['anno'];
$sql = "SELECT   cb.id_unico, cb.banco, t.id_unico, t.razonsocial, 
    t.numeroidentificacion, t.tipoidentificacion,  ti.nombre, cb.numerocuenta, 
    cb.descripcion, cb.tipocuenta, tc.nombre, cb.cuenta, c.nombre, c.codi_cuenta, 
    cb.recursofinanciero, rf.nombre, rf.codi, cb.formato, fc.nombre, 
    d.id_unico, LOWER(d.nombre) 
  FROM gf_cuenta_bancaria cb
  LEFT JOIN gf_tercero t ON cb.banco=t.id_unico
  LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
  LEFT JOIN gf_tipo_cuenta tc ON cb.tipocuenta = tc.id_unico
  LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico
  LEFT JOIN gf_recurso_financiero rf ON cb.recursofinanciero = rf.id_unico
  LEFT JOIN gf_formato fc ON cb.formato = fc.id_unico 
  LEFT JOIN gf_tipo_destinacion d ON cb.destinacion = d.id_unico 
  WHERE cb.parametrizacionanno = $anno 
  ";

$resultado = $mysqli->query($sql);
?>
<title>Listar Cuenta Bancaria</title>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 class="titulolista" align="center">Cuenta Bancaria</h2>
                <div class="table-responsive contTabla">
                    <div class="table-responsive contTabla">
                        <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                            <thead>
 				<tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Banco</strong></td>
                                    <td><strong>Número Cuenta</strong></td>
                                    <td><strong>Descripción</strong></td>
                                    <td><strong>Tipo Cuenta</strong></td>
                                    <td><strong>Formato</strong></td>
                                    <td><strong>Recurso Financiero</strong></td>
                                    <td><strong>Destinación</strong></td>
 				</tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Banco</th>
                                    <th>Número Cuenta</th>
                                    <th>Descripción</th>
                                    <th>Tipo Cuenta</th>
                                    <th>Formato</th>
                                    <th>Recurso Financiero</th>
                                    <th>Destinación</th>
 				</tr>
                            </thead>
                            <tbody>
                                <?php while ( $row = mysqli_fetch_row($resultado)) { ?>
 				<tr>
                                    <td style="display: none;" ><?php echo $row[0]?></td>
                                    <td>
                                        <a href="#" onclick="javascript:eliminarCuentaB(<?php echo $row[0];?>);">
                                            <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                        </a>
                                        <a href="modificar_GF_CUENTA_BANCARIA.php?id_cuentaB=<?php echo md5($row[0]);?>">
                                            <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                        </a>
                                    </td>
                                    <td><?php echo ucwords(mb_strtolower($row[3])." (".($row[4]).")");?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[7]));?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[8]));?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[10]));?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[18]));?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[15]));?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[20]));?></td>
 				</tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right">
                            <a href="registrar_GF_CUENTA_BANCARIA.php" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px;">Registrar Nuevo</a>
                        </div>  
                    </div>
 		</div>
            </div>
        </div>
    </div>

  <!-- Formularios Modales -->  
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            <div id="forma-modal" class="modal-header">
              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
              <p>¿Desea eliminar el registro seleccionado de Cuenta Bancaria?</p>
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
              <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
              <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
            </div>
          </div>
        </div>
    </div>
    <?php require_once ('footer.php');  ?>
    <script type="text/javascript" src="js/menu.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
  	  function eliminarCuentaB(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarCuentaBancaria.php?id="+id,
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
  
  	<script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'listar_GF_CUENTA_BANCARIA.php';
      });
    
  	</script>

  	<script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GF_CUENTA_BANCARIA.php';
      });
    
  	</script>

    <?php require_once 'footer.php'; ?>
</body>
</html>