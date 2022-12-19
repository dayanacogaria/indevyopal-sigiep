<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#04/07/2018 | Erica G. | Afectado
#04/05/2017 | Erica G. | Diseño, tíldes, búsquedas 
#27/02/2017 | Erica G. | Agregar campo vigencia actual
#######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head_listar.php';
$compania = $_SESSION['compania'];
$querytipoC = "SELECT tcp.id_unico, tcp.codigo, tcp.nombre, 
  tcp.obligacionafectacion, tcp.terceroigual, tcp.clasepptal, 
  cp.nombre, tcp.tipodocumento, td.nombre, tcp.tipooperacion, 
  t.nombre, tcp.vigencia_actual , tcp.automatico, 
  tcp.afectado, 
  tcpa.codigo, tcpa.nombre 
FROM gf_tipo_comprobante_pptal tcp 
LEFT JOIN gf_clase_pptal cp ON tcp.clasepptal=cp.id_unico 
LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico 
LEFT JOIN gf_tipo_operacion t ON tcp.tipooperacion=t.id_unico 
LEFT JOIN gf_tipo_comprobante_pptal tcpa ON tcp.afectado = tcpa.id_unico 
WHERE tcp.compania = $compania"; 

$resultado = $mysqli->query($querytipoC);
$_SESSION['url']='listar_GF_TIPO_COMPROBANTE_PPTAL.php';
?>
<title>Listar Tipo Comprobante Presupuestal</title>
</head>
<body>
  
<div class="container-fluid text-center">
  <div class="row content">
    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px">Tipo Comprobante Presupuestal</h2>
      

      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td class="cabeza" style="display: none;">Identificador</td>
                <td class="cabeza" width="30px" align="center"></td>
                <td class="cabeza"><strong>Código</strong></td>
                <td class="cabeza"><strong>Nombre</strong></td>
                <td class="cabeza"><strong>Obligación Afectación</strong></td>
                <td class="cabeza"><strong>Tercero Igual</strong></td>
                <td class="cabeza"><strong>Clase Presupuestal</strong></td>
                <td class="cabeza"><strong>Tipo Documento</strong></td>
                <td class="cabeza"><strong>Tipo Operación</strong></td>
                <td class="cabeza"><strong>Vigencia Actual</strong></td>
                <td class="cabeza"><strong>Automático</strong></td>
                <td class="cabeza"><strong>Afectado</strong></td>
              </tr>


              <tr>
                <th class="cabeza" style="display: none;">Identificador</th>
                <th class="cabeza" width="7%"></th>
                <th class="cabeza">Código</th>
                <th class="cabeza">Nombre</th>
                <th class="cabeza">Obligación Afectación</th>
                <th class="cabeza">Tercero Igual</th>
                <th class="cabeza">Clase Presupuestal</th>
                <th class="cabeza">Tipo Documento</th>
                <th class="cabeza">Tipo Operación</th>
                <th class="cabeza">Vigencia Actual</th>
                <th class="cabeza">Automático</th>
                <th class="cabeza">Afectado</th>
              </tr>

            </thead>
            <tbody>
              
              <?php
                while($row = mysqli_fetch_row($resultado)){?>
               <tr>
                  <td class="campos" style="display: none;"><?php echo $row[0]?></td>
                  <td class="campos"><a class="campos" href="#" onclick="javascript:eliminarTipoC(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      <a class="campos" href="modificar_GF_TIPO_COMPROBANTE_PPTAL.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                  <td class="campos"><?php echo mb_strtoupper($row[1])?></td>
                  <td class="campos"><?php echo ucwords(mb_strtolower($row[2]));?></td>
                  <td class="campos">
                    <?php if(utf8_encode($row[3]) == 2){echo "No";}else{echo "Sí";} ?>
                  </td>
                  <td class="campos">
                    <?php if(utf8_encode($row[4]) == 2){echo "No";}else{echo "Sí";} ?>
                  </td>
                  <td class="campos"><?php echo ucwords(mb_strtolower($row[6]))?></td>
                  <td class="campos"><?php echo ucwords(mb_strtolower($row[8]))?></td>
                  <td class="campos"><?php echo ucwords(mb_strtolower($row[10]))?></td>   
                  <td class="campos">
                    <?php if(utf8_encode($row[11]) == 2){echo "No";}else{echo "Sí";} ?>
                  </td>
                  <td class="campos">
                    <?php if(($row[12]) == 1){echo "Sí";}else{echo "No";} ?>
                  </td>
                  <td class="campos"><?php echo $row[14].' - '.ucwords(mb_strtolower($row[15]))?></td>   
              </tr>
              <?php } ?>


            </tbody>
          </table>

          <div align="right"><a href="registrar_GF_TIPO_COMPROBANTE_PPTAL.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

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
          <p>¿Desea eliminar el registro seleccionado de Tipo Comprobante Presupuestal?</p>
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
      function eliminarTipoC(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarTipoComprobantePptal.php?id="+id,
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
        document.location = 'listar_GF_TIPO_COMPROBANTE_PPTAL.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GF_TIPO_COMPROBANTE_PPTAL.php';
      });
    
  </script>

</body>
</html>

