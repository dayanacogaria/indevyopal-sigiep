
<?php


require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

$sql = "SELECT ac.id_unico,
ac.vigencia,
ac.mes,
ec.nombre
FROM gc_anno_comercial ac 
LEFT JOIN gc_estado_anno_comercial ec ON ec.id_unico=ac.estado
";
$resultado = $mysqli->query($sql);
?>
        <title>Listar Año Comercial</title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Año Comercial</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class=""><strong>Vigencia</strong></td>
                                        <td class=""><strong>Mes</strong></td>
                                        <td class=""><strong>Estado</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th class="cabeza">Vigencia</th>
                                        <th class="cabeza">Mes</th>
                                        <th class="cabeza">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                      if($row[2] == 0){
                                        $m = "ANUAL";
                                      }elseif($row[2] == 1){
                                        $m = "ENERO";
                                      }elseif($row[2] == 2){
                                        $m = "FEBRERO";
                                      }elseif($row[2] == 3){
                                        $m = "MARZO";
                                      }elseif($row[2] == 4){
                                        $m = "ABRIL";
                                      }elseif($row[2] == 5){
                                        $m = "MAYO";
                                      }elseif($row[2] == 6){
                                        $m = "JUNIO";
                                      }elseif($row[2] == 7){
                                        $m = "JULIO";
                                      }elseif($row[2] == 8){
                                        $m = "AGOSTO";
                                      }elseif($row[2] == 9){
                                        $m = "SEPTIEMBRE";
                                      }elseif($row[2] == 10){
                                        $m = "OCTUBRE";
                                      }elseif($row[2] == 11){
                                        $m = "NOVIEMBRE";
                                      }elseif($row[2] == 12){
                                        $m = "DICIEMBRE";
                                      }
                                      ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GC_ANNO_COMERCIAL.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <td class="cabeza"><?php echo $row[1]?></td>   
                                        <td class="cabeza"><?php echo $m?></td>    
                                        <td class="cabeza"><?php echo $row[3]?></td>                

                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GC_ANNO_COMERCIAL.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Año Comercial?</p>
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
                  url:"jsonComercio/eliminarAnnoComercialJson.php?id="+id,
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
        document.location = 'listar_GC_ANNO_COMERCIAL.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GC_ANNO_COMERCIAL.php';
      });    
  </script>
    </body>
</html>