<?php
#02/03/2017 --- Nestor B --- Se modificó el método para cambiar el formato de fecha para que no genere error cuando venga vacía
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT    a.id_unico,
                    a.empleado,
                    e.id_unico,
                    e.tercero,
                    t.id_unico,
                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                    a.tipo,
                    ta.id_unico,
                    ta.nombre,
                    a.tercero,
                    ter.id_unico,
                    ter.razonsocial,
                    a.estado,
                    ea.id_unico,
                    ea.nombre,
                    a.codigoadmin,
                    a.observaciones,
                    a.fechaafiliacion,
                    a.fecharetiro
                FROM gn_afiliacion a	 
                LEFT JOIN	gn_empleado e           ON a.empleado = e.id_unico
                LEFT JOIN   gf_tercero t            ON e.tercero = t.id_unico
                LEFT JOIN   gn_tipo_afiliacion ta   ON a.tipo = ta.id_unico
                LEFT JOIN   gf_tercero ter          ON a.tercero = ter.id_unico
                LEFT JOIN   gn_estado_afiliacion ea ON a.estado = ea.id_unico";
    $resultado = $mysqli->query($sql);
    
?>
    <title>Listar Afiliación</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Afiliación</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Código Admin.</strong></td>
                                        <td class="cabeza"><strong>Tercero</strong></td>
                                        <td class="cabeza"><strong>Tipo</strong></td>
                                        <td class="cabeza"><strong>Fecha Afiliación</strong></td>
                                        <td class="cabeza"><strong>Fecha Retiro</strong></td>
                                        <td class="cabeza"><strong>Observaciones</strong></td>
                                        <td class="cabeza"><strong>Estado</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">Código Admin.</th>
                                        <th class="cabeza">Tercero</th>
                                        <th class="cabeza">Tipo</th>
                                        <th class="cabeza">Fecha Afiliación</th>
                                        <th class="cabeza">Fecha Retiro</th>
                                        <th class="cabeza">Observaciones</th>
                                        <th class="cabeza">Estado</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                        $afa = $row[17];
                                        if(!empty($row[17])||$row[17]!=''){
                                        $afa = trim($afa, '"');
                                        $fecha_div = explode("-", $afa);
                                        $anioa = $fecha_div[0];
                                        $mesa = $fecha_div[1];
                                        $diaa = $fecha_div[2];
                                        $afa = $diaa.'/'.$mesa.'/'.$anioa;
                                      }else{

                                        $afa = '';
                                      }
                                        
                                        $afr = $row[18];
                                        if(!empty($row[18])||$row[18]){
                                        $afr = trim($afr, '"');
                                        $fecha_div = explode("-", $afr);
                                        $anior = $fecha_div[0];
                                        $mesr = $fecha_div[1];
                                        $diar = $fecha_div[2];
                                        $afr = $diar.'/'.$mesr.'/'.$anior;
                                      }else{

                                         $afr = ''; 
                                      }


                                        
                                        $aid   = $row[0];
                                        $aemp  = $row[1];
                                        $eid   = $row[2];
                                        $eter  = $row[3];
                                        $tid1  = $row[4];
                                        $ter1  = $row[5];
                                        $atip  = $row[6];
                                        $taid  = $row[7];
                                        $tanom = $row[8];
                                        $ater  = $row[9];
                                        $tid2  = $row[10];
                                        $ter2  = $row[11];
                                        $aest  = $row[12];
                                        $eaid  = $row[13];
                                        $eanom = $row[14];
                                        $acod  = $row[15];
                                        $aobs  = $row[16];
                                        #$afa   = $row[17];
                                        #$afr   = $row[18];

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_AFILIACION.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <td class="campos"><?php echo $ter1?></td>                
                                        <td class="campos"><?php echo $acod?></td>                
                                        <td class="campos"><?php echo $ter2?></td>                
                                        <td class="campos"><?php echo $tanom?></td>                
                                        <td class="campos"><?php echo $afa?></td>                
                                        <td class="campos"><?php echo $afr?></td>                
                                        <td class="campos"><?php echo $aobs?></td>                
                                        <td class="campos"><?php echo $eanom?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_AFILIACION.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Afiliación?</p>
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
                  url:"json/eliminarAfiliacionJson.php?id="+id,
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
        document.location = 'listar_GN_AFILIACION.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_AFILIACION.php';
      });    
  </script>
    </body>
</html>