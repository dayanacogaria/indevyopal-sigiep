<?php
require_once './Conexion/conexion.php';
//session_start();
require_once './head_listar.php';

$sql = "SELECT IF(CONCAT(t.nombreuno,' ', t.nombredos, ' ', t.apellidouno, ' ', t.apellidodos)='',(t.razonsocial),CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)) AS 'NOMBRE', t.id_unico, t.numeroidentificacion,uv.id_unico, p.id_unico, p.codigo_catastral,tuv.id_unico, tuv.nombre, uv.uso, u.id_unico, u.nombre, uv.estrato,  e.id_unico, e.nombre, uv.numero_familias, uv.numero_personas, uv.codigo_ruta, uv.codigo_interno, uv.tipo_productor, tp.id_unico, tp.nombre, ts.id_unico, ts.nombre, tsc.id_unico, tsc.nombre,tm.id_unico, tm.nombre, tlm.id_unico, tlm.nombre, tsh.id_unico, tsh.nombre, tmm.id_unico,  tmm.nombre, uv.deshabilitado
FROM gp_unidad_vivienda uv 
LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico
LEFT JOIN gp_tipo_unidad_vivienda tuv on uv.tipo_unidad = tuv.id_unico 
LEFT JOIN gp_uso u on uv.uso = u.id_unico   
LEFT JOIN gp_estrato e on uv.estrato = e.id_unico
LEFT JOIN gp_tipo_productor tp ON uv.tipo_productor = tp.id_unico
LEFT JOIN gp_sector ts ON uv.sector = ts.id_unico
LEFT JOIN gp_seccion tsc ON uv.seccion = tsc.id_unico
LEFT JOIN gp_tipo_manzana tm ON uv.manzana = tm.id_unico
LEFT JOIN gp_tipo_lado_manzana tlm ON uv.lado_manzana = tlm.id_unico
LEFT JOIN gp_tipo_sector_hidraulico tsh ON uv.sector_hidraulico = tsh.id_unico
LEFT JOIN gp_tipo_microsector tmm ON uv.microsector = tmm.id_unico";

$resultado = $mysqli->query($sql);
?>
<style>
    body{
        font-size: 12px;
    }
</style>
<title>Listar Unidad Vivienda</title>
</head>
<body>
      <div class="container-fluid text-center">
      <div class="row content">
      <?php require_once './menu.php'; ?>
           <div class="col-sm-10 text-left">
           <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Unidad Vivienda</h2>
               <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                   <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                       <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                             <thead>
                                 <!--Cabecera de las columnas -->
                             <tr>
                                 <td style="display: none;">Identificador</td>
                                 <td width="7%" class="cabeza"></td>
                                 <td class=""><strong>Predio</strong></td>
                                 <td class=""><strong>Tipo Unidad</strong></td>
                                 <td class=""><strong>Tercero</strong></td>
                                 <td class=""><strong>Uso</strong></td>
                                 <td class=""><strong>Estrato</strong></td>
                                 <td class=""><strong>Número Familias</strong></td>
                                 <td class=""><strong>Número Personas</strong></td>
                                 <td class=""><strong>Código Ruta</strong></td>
                                 <td class=""><strong>Código Interno</strong></td>
                                 <td class=""><strong>Tipo Productor</strong></td>
                                 <td class=""><strong>Sector</strong></td>
                                 <td class=""><strong>Sección</strong></td>
                                 <td class=""><strong>Manzana</strong></td>
                                 <td class=""><strong>Lado Manzana</strong></td>
                                 <td class=""><strong>Sector Hidráulico</strong></td>
                                 <td class=""><strong>Microsector</strong></td>
                                 <td class=""><strong>Deshabilitado</strong></td>

                             </tr>
                                <!--Nombres de los campos -->
                             <tr>
                              <th style="display: none;">Identificador</th>
                              <th width="7%"></th>
                              <th>Predio</th>
                              <th>Tipo Unidad</th>
                              <th>Tercero</th>
                              <th>Uso</th>
                              <th>Estrato</th>
                              <th>Numero Familias</th>
                              <th>Numero Personas</th>
                              <th>Código Ruta</th>
                              <th>Código Interno</th>
                              <th>Tipo Productor</th>
                              <th>Sector</th>
                              <th>Sección</th>
                              <th>Manzana</th>
                              <th>Lado Manzana</th>
                              <th>Sector hidráulico</th>
                              <th>Microsector</th>
                              <th>Deshabilitado</th>

                             </tr>
                             </thead> 
                             <?php 
                              while ($row = mysqli_fetch_row($resultado)) { ?>
                              <tr>
                                  <td style="display: none;"><?php echo $row[3]?></td>
                                     <td>
                                         <a href="#" onclick="javascript:eliminar(<?php echo $row[3];?>);">
                                          <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                         </a>
                                         <a href="modificar_GP_UNIDAD_VIVIENDA.php?id=<?php echo md5($row[3]);?>">
                                          <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                         </a>
                                     </td>
                                  <td><?php echo(strtoupper($row[5]))?></td>
                                  <td><?php echo ucwords(strtolower($row[7]))?></td>
                                  <td><?php echo(ucwords(strtolower($row[0].'('.$row[2].')')))?></td>
                                  <td><?php echo(ucwords(strtolower($row[10])));?></td>
                                  <td><?php echo(ucwords(strtolower($row[13])));?></td>
                                  <td><?php echo($row[14])?></td>
                                  <td><?php echo($row[15])?></td>
                                  <td><?php echo strtoupper($row[16])?></td>
                                  <td><?php echo($row[17])?></td>
                                  <td><?php echo(ucwords(strtolower($row[20])));?></td>
                                  <td><?php echo(ucwords(strtolower($row[22])));?></td>
                                  <td><?php echo(ucwords(strtolower($row[24])));?></td>
                                  <td><?php echo(ucwords(strtolower($row[26])));?></td>
                                  <td><?php echo(ucwords(strtolower($row[28])));?></td>
                                  <td><?php echo(ucwords(strtolower($row[30])));?></td>
                                  <td><?php echo(ucwords(strtolower($row[32])));?></td>
                                  <td><?php switch($row[33]) {
                                             case ('1'):
                                                echo 'Sí';
                                              break;
                                              case ('2'):
                                                echo 'No';
                                              break;
                                              default: 
                                                echo '';
                                              break;
                                              }?>
                                  </td>
                                  

                                </tr>
                               <?php }
                               ?>
                                </tbody>
                            </table>
 
          <div align="right"><a href="registrar_GP_UNIDAD_VIVIENDA.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

        </div>
        
       
      </div>
      
    </div>

  </div>
</div>

<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea Eliminar el registro de Unidad Vivienda?</p>
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


  <?php require_once 'footer.php'; ?>

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

  <script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarUnidadViviendaJson.php?id="+id,
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
        document.location = 'listar_GP_UNIDAD_VIVIENDA.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GP_UNIDAD_VIVIENDA.php';
      });
    
  </script>

</body>
</html>