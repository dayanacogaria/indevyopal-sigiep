<?php
#10/03/2017 --- Nestor B --- se modificó la funcion que cambia el formato de fecha para que no genere error cuando sean vacías 

require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT        n.id_unico,
                        n.valor,
                        n.fecha,
                        n.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        n.periodo,
                        p.id_unico,
                        p.codigointerno,
                        n.concepto,
                        c.id_unico,
                        CONCAT(c.codigo,' - ',c.descripcion)
                FROM gn_novedad n	 
                LEFT JOIN	gn_empleado e ON n.empleado = e.id_unico
                LEFT JOIN   gf_tercero t ON e.tercero = t.id_unico
                LEFT JOIN   gn_periodo p ON n.periodo = p.id_unico
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico";
    $resultado = $mysqli->query($sql);


    
?>
    <title>Listar Novedad</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Novedad</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Concepto</strong></td>
                                        <td class="cabeza"><strong>Valor</strong></td>
                                        <td class="cabeza"><strong>Período</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Empleado</th>
                                        <th class="cabeza">Fecha</th>
                                        <th class="cabeza">Concepto</th>
                                        <th class="cabeza">Valor</th>
                                        <th class="cabeza">Período</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                            $nfec = $row[2];
                                            if(!empty($row[2])||$row[2]!=''){
                                            $nfec = trim($nfec, '"');
                                            $fecha_div = explode("-", $nfec);
                                            $anion = $fecha_div[0];
                                            $mesn = $fecha_div[1];
                                            $dian = $fecha_div[2];
                                            $nfec = $dian.'/'.$mesn.'/'.$anion;
                                          }else{
                                            $nfec='';
                                          }

                                        
                                            $nid    = $row[0];
                                            $nval   = $row[1];
                                            #$nfec   = $row[2];
                                            $nemp   = $row[3];
                                            $empid  = $row[4];
                                            $empter = $row[5];
                                            $terid  = $row[6];
                                            $ternom = $row[7];
                                            $nper   = $row[8];
                                            $pid    = $row[9];
                                            $pci    = $row[10];
                                            $ncon   = $row[11];
                                            $conid  = $row[12];
                                            $concn  = $row[13];

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_NOVEDAD.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <td class="campos"><?php echo $ternom?></td>                
                                        <td class="campos"><?php echo $nfec?></td>                
                                        <td class="campos"><?php echo $concn?></td>                
                                        <td class="campos"><?php echo $nval?></td>                
                                        <td class="campos"><?php echo $pci?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_NOVEDAD.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Novedad?</p>
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
                  url:"json/eliminarNovedadJson.php?id="+id,
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
        document.location = 'listar_GN_NOVEDAD.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_NOVEDAD.php';
      });    
  </script>
    </body>
</html>