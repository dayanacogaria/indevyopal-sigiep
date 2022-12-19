<?php
#08/03/2017 --- Nestor B --- se modificó el método que cambia el formatod de fachas para que cuando sean vacías no genere error;
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';


   
  $sql = "SELECT        i.id_unico,
                        i.numeroinc,
                        i.fechainicio,
                        i.numerodias,
                        i.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        i.fechaaprobacion,
                        i.numeroaprobacion,
                        i.estado,
                        ei.id_unico,
                        ei.nombre,
                        i.diagnostico,
                        i.tiponovedad,
                        tn.id_unico,
                        tn.nombre
                FROM gn_incapacidad i
                LEFT JOIN	gn_empleado e             ON i.empleado    = e.id_unico
                LEFT JOIN   gf_tercero t              ON e.tercero     = t.id_unico
                LEFT JOIN   gn_estado_incapacidad ei  ON i.estado      = ei.id_unico
                LEFT JOIN   gn_tipo_novedad tn        ON i.tiponovedad = tn.id_unico";
    $resultado = $mysqli->query($sql);


    
?>
    <title>Listar Licencia</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Licencia</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px; ">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                     <tr>
                                        <td class="cabeza" style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Tipo Novedad</strong></td>
                                        <td class="cabeza"><strong>Estado</strong></td>
                                        <td class="cabeza"><strong>No. Incapacidad</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>No. Días</strong></td>
                                        <td class="cabeza"><strong>No. Aprobación</strong></td>
                                        <td class="cabeza"><strong>Fecha Aprobación</strong></td>
                                        <td class="cabeza"><strong>Motivo</strong></td>
                                    </tr>
                                     <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>                                        
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">Tipo Novedad</th>
                                        <th class="cabeza">Estado</th>
                                        <th class="cabeza">No. Solicitud</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">No. Días</th>
                                        <th class="cabeza">No. Aprobación</th>
                                        <th class="cabeza">Fecha Aprobación</th>
                                        <th class="cabeza">Diagnóstico</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                            $infi      = $row[2];
                                            if(!empty($row[2])||$row[2]){
                                            $infi      = trim($infi, '"');
                                            $fecha_div = explode("-", $infi);
                                            $anioi     = $fecha_div[0];
                                            $mesi      = $fecha_div[1];
                                            $diai      = $fecha_div[2];
                                            $infi      = $diai.'/'.$mesi.'/'.$anioi;
                                          }else{
                                            $infi='';
                                          }

                                        
                                            $infa      = $row[9];
                                            if(!empty($row[9])||$row[9]!=''){
                                            $infa      = trim($infa, '"');
                                            $fecha_div = explode("-", $infa);
                                            $aniofa    = $fecha_div[0];
                                            $mesfa     = $fecha_div[1];
                                            $diafa     = $fecha_div[2];
                                            $infa      = $diafa.'/'.$mesfa.'/'.$aniofa;
                                          }else{
                                            $infa='';
                                          }
                                                          
                                            $inid   = $row[0];
                                            $inni   = $row[1];
                                            #$infi   = $row[2];
                                            $innd   = $row[3];
                                            $inemp  = $row[4];
                                            $empid  = $row[5];
                                            $empter = $row[6];
                                            $terid  = $row[7];
                                            $ternom = $row[8];
                                            #$infa   = $row[9];
                                            $inna   = $row[10];
                                            $inest  = $row[11];
                                            $eiid   = $row[12];
                                            $einom  = $row[13];
                                            $indiag = $row[14];
                                            $intn   = $row[15];
                                            $tnid   = $row[16];
                                            $tnnom  = $row[17];

                                        ?>
                                    <tr>
                                        <td class="campos" style="display: none;"><?php echo $row[0]?></td>
                                        <td class="campos">
                                            <a class="campos" href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a class="campos" href="modificar_GN_INCAPACIDAD.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>   
                                        <td class="campos"><?php echo $ternom?></td>                
                                        <td class="campos"><?php echo $tnnom?></td>                   
                                        <td class="campos"><?php echo $einom?></td>                   
                                        <td class="campos"><?php echo $inni?></td>                   
                                        <td class="campos"><?php echo $infi?></td>                
                                        <td class="campos"><?php echo $innd?></td>                
                                        <td class="campos"><?php echo $inna?></td>                
                                        <td class="campos"><?php echo $infa?></td>                
                                        <td class="campos"><?php echo $indiag?></td>                                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_LICENCIA.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Incapacidad?</p>
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
                  url:"json/eliminarIncapacidadJson.php?id="+id,
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
        document.location = 'listar_GN_INCAPACIDAD.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_INCAPACIDAD.php';
      });    
  </script>
    </body>
</html>