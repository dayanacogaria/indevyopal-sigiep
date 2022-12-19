<?php
require_once 'Conexion/conexion.php';
require_once 'head_listar.php';

$sql = "SELECT DISTINCT p.id_unico,  
            p.codigo_catastral, 
            p.nombre,
            p.matricula_inmobiliaria, 
            p.aniocreacion,
            p.codigo_sig,
            p.codigoigac,
            p.codigoigac,
            p.codigoigac,
            p.direccion, 
            p.ciudad, 
            c.nombre,
            c.departamento,
            d.nombre,
            p.barrio, 
            b.nombre,
            p.estrato,
            e.nombre,
            p.estado, 
            ep.nombre,
            p.ruta, 
            r.nombre, 
            p.tipo_predio,		
            tp.nombre,
            p.predioaso,
            pa.codigo_catastral, 
            pa.nombre 
        FROM gp_predio1 p  
        LEFT JOIN gf_ciudad c       	ON p.ciudad = c.id_unico 
        LEFT JOIN gp_barrio b       	ON p.barrio = b.id_unico 
        LEFT JOIN gp_ruta   r       	ON p.ruta = r.id_unico 
        LEFT JOIN gp_tipo_predio tp 	ON p.tipo_predio = tp.id_unico
        LEFT JOIN gf_departamento d 	ON c.departamento = d.id_unico 
        LEFT JOIN gp_estrato e      	ON p.estrato = e.id_unico 
        LEFT JOIN gr_estado_predio ep 	ON p.estado = ep.id_unico
        LEFT JOIN gp_predio1 pa         ON p.predioaso = pa.id_unico";

$resultado = $mysqli->query($sql);
?>
<style>
    body{
        font-size: 12px;
    }
</style>
<title>Listar Predio</title>
</head>
<body>
      <div class="container-fluid text-center">
      <div class="row content">
      <?php require_once './menu.php'; ?>
           <div class="col-sm-10 text-left">
           <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Predio</h2>
               <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                   <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                       <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                             <thead>
                                 <!--Cabecera de las columnas -->
                             <tr>
                                 <td style="display: none;">Identificador</td>
                                 <td width="7%" class="cabeza"></td>
                                 <td class=""><strong>Código Catastral</strong></td>
                                 <td class=""><strong>Nombre</strong></td>
                                 <td class=""><strong>Matrícula Inmobiliaria</strong></td>
                                 <td class=""><strong>Año Creación</strong></td>
                                 <td class=""><strong>Código SIG</strong></td>
                                 <td class=""><strong>Código IGAC</strong></td>
                                 <td class=""><strong>Dirección</strong></td>
                                 <td class=""><strong>Ciudad</strong></td>
                                 <td class=""><strong>Barrio</strong></td>
                                 <td class=""><strong>Estrato</strong></td>
                                 <td class=""><strong>Estado</strong></td>
                                 <td class=""><strong>Ruta</strong></td>
                                 <td class=""><strong>Tipo Predio</strong></td>
                                 <td class=""><strong>Predio Asociado</strong></td>
                             </tr>
                                <!--Nombres de los campos -->
                             <tr>
                              <th style="display: none;">Identificador</th>
                              <th width="7%"></th>
                              <th>Código Catastral</th>
                              <th>Nombre</th>
                              <th>Matrícula Inombiliaria</th>
                              <th>Año Creación</th>
                              <th>Código SIG</th>
                              <th>Código IGAC</th>
                              <th>Dirección</th>
                              <th>Ciudad</th>
                              <th>Barrio</th>
                              <th>Estrato</th>
                              <th>Estado</th>
                              <th>Ruta</th>
                              <th>Tipo Predio</th>
                              <th>Predio Asociado</th>
                             </tr>
                             </thead> 
                             <tbody>
                             <?php 
                              while ($row = mysqli_fetch_row($resultado)) { ?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td>
                                        <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                            <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                        </a>
                                        <a href="Modificar_GP_PREDIO.php?id=<?php echo md5($row[0]);?>">
                                            <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                        </a>
                                    </td>
                                    <td><?php echo mb_strtoupper($row[1]);?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[2]));?></td>
                                    <td><?php echo mb_strtoupper($row[3]);?></td>
                                    <td><?php echo $row[4];?></td>
                                    <td><?php echo mb_strtoupper($row[5]);?></td>
                                    <td><?php echo mb_strtoupper($row[6]);?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[9]));?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[11].' - '.$row[13]));?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[15]));?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[17]));?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[19]));?></td>
                                    <td><?php echo ucwords(strtolower($row[21]));?></td>
                                    <td><?php echo ucwords(strtolower($row[23]));?></td>
                                    <td><?php echo ucwords(strtolower($row[25]. ' - '.$row[26]));?></td>
                                </tr>
                               <?php } ?>
                                </tbody>
                            </table>
 
          <div align="right"><a href="Registrar_GP_PREDIO.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

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
          <p>¿Desea eliminar el registro seleccionado de predio?</p>
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
                  url:"json/eliminar_GP_PREDIOJson.php?id="+id,
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
        document.location = 'LISTAR_GP_PREDIO.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'LISTAR_GP_PREDIO.php';
      });
    
  </script>

</body>
</html>