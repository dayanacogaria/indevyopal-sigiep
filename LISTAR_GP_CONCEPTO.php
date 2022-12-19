<?php

require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('jsonPptal/funcionesPptal.php');
require_once('head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$tr = tipo_cambio($compania);
$query = "SELECT c.id_unico, c.nombre, 
        tc.nombre, 
        top.nombre, 
        pi.codi, pi.nombre, fb.nombre,  
        if(c.alojamiento = 1, 'SI', 'NO'), 
        ca.nombre,
        c.ajuste, 
        c.traduccion 
        FROM gp_concepto c 
        LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto=tc.id_unico 
        LEFT JOIN gp_tipo_operacion top ON c.tipo_operacion = top.id_unico 
        LEFT JOIN gf_plan_inventario pi ON c.plan_inventario = pi.id_unico 
        LEFT JOIN gp_factor_base fb ON c.factor_base = fb.id_unico 
        LEFT JOIN gp_concepto ca ON c.concepto_asociado = ca.id_unico 
        WHERE c.compania = $compania"; 
$resultado = $mysqli->query($query);
?>
<title>Listar Concepto</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Concepto</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td class="cabeza" style="display: none;">Identificador</td>
                                <td class="cabeza" width="30px" align="center"></td>
                                <td class="cabeza"><strong>Tipo concepto</strong></td>
                                <td class="cabeza"><strong>Nombre</strong></td>
                                <td class="cabeza"><strong>Tipo Operación</strong></td>
                                <td class="cabeza"><strong>Plan inventario</strong></td>
                                <td class="cabeza"><strong>Factor base</strong></td>
                                <td class="cabeza"><strong>Alojamiento</strong></td>
                                <td class="cabeza"><strong>Concepto Asociado</strong></td>
                                <?php if ($tr!=0){
                                    echo '<td class="cabeza"><strong>Ajuste</strong></td>';
                                    echo '<td class="cabeza"><strong>Traducción</strong></td>';
                                }?>
                            </tr>
                            <tr>
                                <th class="cabeza" style="display: none;">Identificador</th>
                                <th class="cabeza" width="7%"></th>
                                <th class="cabeza"><strong>Tipo concepto</strong></th>
                                <th class="cabeza"><strong>Nombre</strong></th>
                                <th class="cabeza"><strong>Tipo Operación</strong></th>
                                <th class="cabeza"><strong>Plan inventario</strong></th>
                                <th class="cabeza"><strong>Factor base</strong></th>
                                <th class="cabeza"><strong>Alojamiento</strong></th>
                                <th class="cabeza"><strong>Concepto Asociado</strong></th>
                                <?php if ($tr!=0){
                                    echo '<th class="cabeza"><strong>Ajuste</strong></th>';
                                    echo '<th class="cabeza"><strong>Traducción</strong></th>';
                                }?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while($row = mysqli_fetch_row($resultado)){?>
                            <tr>
                                <td class="campos" style="display: none;"><?php echo $row[0]?></td>
                                <td class="campos">
                                  <a class="campos"  href="#" onclick="javascript:eliminarConcepto(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                  <a class="campos" href="Modificar_GP_CONCEPTO.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                  <a class="campos" href="GP_CONCEPTO_TARIFA.php?id=<?php echo md5($row[0]);?>"><i title="Tarifa" class="glyphicon glyphicon-usd" ></i></a>
                                </td>
                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[2])));?></td>
                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[1])));?></td>
                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[3])));?></td>
                                <td class="campos"><?php echo $row[4].' - '.ucwords(mb_strtolower(($row[5])));?></td>
                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[6])));?></td>
                                <td class="campos"><?php echo $row[7];?></td>
                                <td class="campos"><?php echo ucwords(mb_strtolower(($row[8])));?></td>
                                <?php if ($tr!=0){
                                    echo '<td class="campos">'.$row[9].'</td>';
                                    echo '<td class="campos">'.$row[10].'</td>';
                                }?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div align="right">
                        <a href="Registrar_GP_CONCEPTO.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px;margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo
                        </a> 
                    </div>       
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Divs de clase Modal para las ventanillas de eliminar. -->
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Concepto?</p>
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

  <?php require_once ('footer.php'); ?>

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

<!-- Función para la eliminación del registro. -->
<script type="text/javascript">
      function eliminarConcepto(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GP_CONCEPTOJson.php?id="+id,
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
        document.location = 'LISTAR_GP_CONCEPTO.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'LISTAR_GP_CONCEPTO.php';
      });
    
  </script>

</body>
</html>


