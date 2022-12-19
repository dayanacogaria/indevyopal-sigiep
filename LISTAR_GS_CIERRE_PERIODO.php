<?php
#################MODIFICACIONES#########################
#04/05/2017 | Erica G. | Archivo Creado
########################################################

require_once('Conexion/conexion.php');
require_once('head_listar.php');
$cp = "SELECT cp.id_unico, pa.anno, m.mes, e.nombre, date_format(cp.fecha_cierre,'%d/%m/%Y'),"
        . "u.usuario, IF(CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) 
            IS NULL OR CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) = '',
            (tr.razonsocial),
            CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)) AS NOMBRE, m.numero  
        FROM 
            gs_cierre_periodo cp 
        LEFT JOIN 
            gf_parametrizacion_anno pa ON pa.id_unico = cp.anno 
        LEFT JOIN 
            gf_mes m ON m.id_unico = cp.mes 
        LEFT JOIN 
            gs_estado_cierre e ON cp.estado = e.id_unico 
        LEFT JOIN 
            gs_usuario u ON cp.usuario = u.id_unico 
        LEFT JOIN 
            gf_tercero tr ON u.tercero = tr.id_unico 
        ORDER BY pa.anno DESC"; 

$resultado = $mysqli->query($cp);

?>
<title>Listar Cierre Periodo</title>
</head>
<body>
 
  
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Cierre Periodo</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td style="display: none;">Identificador</td>
                                <td width="30px" align="center"></td>
                                <td><strong>Año</strong></td>
                                <td><strong>Mes</strong></td>
                                <td><strong>Estado</strong></td>                
                                <td><strong>Fecha Cierre</strong></td>                
                                <td><strong>Usuario</strong></td>                
                                <td><strong>Nombre</strong></td>                
                            </tr>
                            <tr>
                                <th style="display: none;">Identificador</th>
                                <th width="7%"></th>
                                <th>Año</th>
                                <th>Mes</th>
                                <th>Estado</th>                
                                <th>Fecha Cierre</th>                
                                <th>Usuario</th>                
                                <th>Nombre</th>                
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_row($resultado)){?>
                            <tr>
                                <td style="display: none;"><?php echo $row[7]?></td>
                                <td>
                                    <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                        <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                    </a>
                                    <a href="Modificar_GS_CIERRE_PERIODO.php?id=<?php echo md5($row[0]);?>">
                                        <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                    </a>
                                </td>
                                <td><?php echo $row[1];?></td>
                                <td><?php echo ucwords(mb_strtolower($row[2]));?></td>
                                <td><?php echo ucwords(mb_strtolower($row[3]));?></td>
                                <td><?php echo $row[4];?></td>
                                <td><?php echo mb_strtoupper($row[5]);?></td>
                                <td><?php echo ucwords(mb_strtolower($row[6]));?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div align="right">
                        <a href="Registrar_GS_CIERRE_PERIODO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> 
                    </div>  
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
          <p>¿Desea eliminar el registro seleccionado de Cierre de Periodo?</p>
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


  <?php require_once ('footer.php'); ?>

  
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              var form_data = {id:id};
              $.ajax({
                  type:"POST",
                  url:"jsonSistema/eliminarCierrePeriodoJson.php?",
                  data: form_data,
                  success: function (data) {
                      console.log(data);
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
        document.location = 'LISTAR_GS_CIERRE_PERIODO.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'LISTAR_GS_CIERRE_PERIODO.php';
      });
    
  </script>

</body>
</html>

