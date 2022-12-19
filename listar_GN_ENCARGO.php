<?php
#08/03/2017 --- Nestor B --- se modificó el método que cambia el formatod de fachas para que cuando sean vacías no genere error;
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT        en.id_unico,
                        en.numeroacto,
                        en.fechaacto,
                        en.fechainicio,
                        en.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        en.fechafin,
                        en.categoria,
                        c.id_unico,
                        c.nombre,
                        en.cargo,
                        car.id_unico,
                        car.nombre,
                        en.dependencia,
                        d.id_unico,
                        d.nombre,
                        en.tiponovedad,
                        tn.id_unico,
                        tn.nombre
                FROM gn_encargo en
                LEFT JOIN	gn_empleado e         ON en.empleado    = e.id_unico
                LEFT JOIN   gf_tercero t          ON e.tercero      = t.id_unico
                LEFT JOIN   gn_tipo_novedad tn    ON en.tiponovedad = tn.id_unico
                LEFT JOIN   gn_categoria c        ON en.categoria   = c.id_unico
                LEFT JOIN   gf_cargo car          ON en.cargo       = car.id_unico
                LEFT JOIN   gf_tipo_dependencia d ON en.dependencia = d.id_unico";
    $resultado = $mysqli->query($sql);


    
?>
    <title>Listar Encargo</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Encargo</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                     <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Cargo</strong></td>
                                        <td class="cabeza"><strong>Dependencia</strong></td>
                                        <td class="cabeza"><strong>Categoría</strong></td>
                                        <td class="cabeza"><strong>Novedad</strong></td>
                                        <td class="cabeza"><strong>Número Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>Fecha Fin</strong></td>
                                    </tr>
                                     <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">Cargo</th>
                                        <th class="cabeza">Dependencia</th>
                                        <th class="cabeza">Categoría</th>
                                        <th class="cabeza">Novedad</th>
                                        <th class="cabeza">Número Acto</th>
                                        <th class="cabeza">Fecha Acto</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">Fecha Fin</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                            $enfact    = $row[2];
                                            if(!empty($row[2])||$row[2]!=''){
                                            $enfact    = trim($enfact, '"');
                                            $fecha_div = explode("-", $enfact);
                                            $aniof     = $fecha_div[0];
                                            $mesf      = $fecha_div[1];
                                            $diaf      = $fecha_div[2];
                                            $enfact    = $diaf.'/'.$mesf.'/'.$aniof;
                                          }else{
                                            $enfact='';
                                          }
                                        
                                            $enfi      = $row[3];
                                            if(!empty($row[3])||$row[3]!=''){
                                            $enfi      = trim($enfi, '"');
                                            $fecha_div = explode("-", $enfi);
                                            $anioi     = $fecha_div[0];
                                            $mesi      = $fecha_div[1];
                                            $diai      = $fecha_div[2];
                                            $enfi      = $diai.'/'.$mesi.'/'.$anioi;
                                          }else{
                                            $enfi='';
                                          }
                                        
                                            $enff      = $row[9];
                                            if(!empty($row[9])||$row[9]!=''){
                                            $enff      = trim($enff, '"');
                                            $fecha_div = explode("-", $enff);
                                            $anioff    = $fecha_div[0];
                                            $mesff     = $fecha_div[1];
                                            $diaff     = $fecha_div[2];
                                            $enff     = $diaff.'/'.$mesff.'/'.$anioff;
                                          }else{
                                            $enff='';
                                          }
                                            
                                            
                                        
                                                                                
                                            $enid    = $row[0];
                                            $ennact  = $row[1];
                                            #$enfact = $row[2];
                                            #$enfi   = $row[3];
                                            $enemp   = $row[4];
                                            $empid   = $row[5];
                                            $empter  = $row[6];
                                            $terid   = $row[7];
                                            $ternom  = $row[8];
                                            #$enff   = $row[9];
                                            $encat   = $row[10];
                                            $cid     = $row[11];
                                            $cnom    = $row[12];
                                            $encar   = $row[13];
                                            $carid   = $row[14];
                                            $carnom  = $row[15];
                                            $endep   = $row[16];
                                            $depid   = $row[17];
                                            $depnom  = $row[18];
                                            $entn    = $row[19];
                                            $tnid    = $row[20];
                                            $tnnom   = $row[21];

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_ENCARGO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>   
                                        <td class="campos"><?php echo $ternom?></td>                
                                        <td class="campos"><?php echo $carnom?></td>                   
                                        <td class="campos"><?php echo $depnom?></td>                   
                                        <td class="campos"><?php echo $cnom?></td>                   
                                        <td class="campos"><?php echo $tnnom?></td>                
                                        <td class="campos"><?php echo $ennact?></td>                
                                        <td class="campos"><?php echo $enfact?></td>                
                                        <td class="campos"><?php echo $enfi?></td>                
                                        <td class="campos"><?php echo $enff?></td>                                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_ENCARGO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Encargo?</p>
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
                  url:"json/eliminarEncargoJson.php?id="+id,
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
        document.location = 'listar_GN_ENCARGO.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_ENCARGO.php';
      });    
  </script>
    </body>
</html>