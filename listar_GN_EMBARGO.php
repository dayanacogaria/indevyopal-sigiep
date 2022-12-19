<?php
#06/03/2017 --- Nestor B --- se modificó la función de mostrar las fechas para que cuando sean vacías no genere error
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT        em.id_unico,
                        em.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        em.entidad,
                        ter.id_unico,
                        ter.razonsocial,
                        em.fechaembargo,
                        em.fechaliquidar,
                        em.fechainicio,
                        em.fechafin                        
                FROM gn_embargo em	 
                LEFT JOIN	gn_empleado e                   ON em.empleado = e.id_unico
                LEFT JOIN   gf_tercero t                    ON e.tercero   = t.id_unico
                LEFT JOIN   gf_tercero ter                  ON em.entidad  = ter.id_unico";
    $resultado = $mysqli->query($sql);

?>
    <title>Listar Embargo</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Embargo</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Entidad</strong></td>
                                        <td class="cabeza"><strong>Fecha Embargo</strong></td>
                                        <td class="cabeza"><strong>Fecha Liquidar</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>Fecha Fin</strong></td>
                                        
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">Entidad</th>
                                        <th class="cabeza">Fecha Embargo</th>
                                        <th class="cabeza">Fecha Liquidar</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">Fecha Fin</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                            $emfe = $row[9];
                                            if(!empty($row[9])||$row[9]!=''){
                                            $emfe = trim($emfe, '"');
                                            $fecha_div = explode("-", $emfe);
                                            $anioe = $fecha_div[0];
                                            $mese = $fecha_div[1];
                                            $diae = $fecha_div[2];
                                            $emfe = $diae.'/'.$mese.'/'.$anioe;
                                          }else{
                                            $emfe='';
                                          }
                                        
                                            $emfl = $row[10];
                                            if(!empty($row[10])||$row[10]!=''){
                                            $emfl = trim($emfl, '"');
                                            $fecha_div = explode("-", $emfl);
                                            $aniol = $fecha_div[0];
                                            $mesl = $fecha_div[1];
                                            $dial = $fecha_div[2];
                                            $emfl = $dial.'/'.$mesl.'/'.$aniol;
                                          }else{
                                            $emfl='';
                                          }
                                        
                                            $emfi = $row[11];
                                            if(!empty($row[11])||$row[11]!=''){
                                            $emfi = trim($emfi, '"');
                                            $fecha_div = explode("-", $emfi);
                                            $anioi = $fecha_div[0];
                                            $mesi = $fecha_div[1];
                                            $diai = $fecha_div[2];
                                            $emfi = $diai.'/'.$mesi.'/'.$anioi;
                                          }else{
                                            $emfi='';
                                          }

                                        
                                            $emff = $row[12];
                                            if(!empty($row[12])||$row[12]!=''){
                                            $emff = trim($emff, '"');
                                            $fecha_div = explode("-", $emff);
                                            $aniof = $fecha_div[0];
                                            $mesf = $fecha_div[1];
                                            $diaf = $fecha_div[2];
                                            $emff = $diaf.'/'.$mesf.'/'.$aniof;
                                          }else{
                                            $emff='';
                                          }
                                        
                                            
                                        
                                            $emid   = $row[0];
                                            $ememp  = $row[1];
                                            $eid    = $row[2];
                                            $eter   = $row[3];
                                            $terid  = $row[4];
                                            $ternom = $row[5];
                                            $ement  = $row[6];
                                            $entid  = $row[7];
                                            $entnom = $row[8];
                                            #$emfe   = $row[9];
                                            #$emfl   = $row[10];
                                            #$emfi   = $row[11];
                                            #$emff   = $row[12];

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_EMBARGO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <td class="campos"><?php echo $ternom?></td>                
                                        <td class="campos"><?php echo $entnom?></td>                
                                        <td class="campos"><?php echo $emfe?></td>                
                                        <td class="campos"><?php echo $emfl?></td>                
                                        <td class="campos"><?php echo $emfi?></td>                
                                        <td class="campos"><?php echo $emff?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_EMBARGO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Embargo?</p>
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
                  url:"json/eliminarEmbargoJson.php?id="+id,
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
        document.location = 'listar_GN_EMBARGO.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_EMBARGO.php';
      });    
  </script>
    </body>
</html>