<?php



require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';


if(!empty($_GET['v'])){

$idVigencia=$_GET['v'];
$sql="SELECT vc.id_unico,
             DATE_FORMAT(vc.fecha_inicial,'%d-%m-%Y') AS fechaInicio,
             DATE_FORMAT(vc.fecha_final,'%d-%m-%Y') AS fechaFinal,
             DATE_FORMAT(vc.fecha_inicio_inter,'%d-%m-%Y') AS fechaII,
             DATE_FORMAT(vc.fecha_limite_decl,'%d-%m-%Y') AS fechaLimiDec,
             vc.vigencia,
             ac.vigencia
      FROM  gc_vencimiento_comercial vc
      LEFT JOIN gc_anno_comercial ac ON ac.id_unico=vc.vigencia
      WHERE md5(vc.vigencia)='$idVigencia'"; 
  $resultado=$mysqli->query($sql);

}/*else{

$sql="SELECT vc.id_unico,
             DATE_FORMAT(vc.fecha_inicial,'%d-%m-%Y') AS fechaInicio,
             DATE_FORMAT(vc.fecha_final,'%d-%m-%Y') AS fechaFinal,
             DATE_FORMAT(vc.fecha_inicio_inter,'%d-%m-%Y') AS fechaII,
             DATE_FORMAT(vc.fecha_limite_decl,'%d-%m-%Y') AS fechaLimiDec,
             vc.vigencia,
             ac.vigencia
      FROM  gc_vencimiento_comercial vc
      LEFT JOIN gc_anno_comercial ac ON ac.id_unico=vc.vigencia
      "; 
  $resultado=$mysqli->query($sql);

}          */               

?>

<title>Listar Vencimiento Comercial</title>
</head>

<script>

    $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);


        $("#fechaInicial").datepicker({changeMonth: true,}).val(fecAct);
        $("#fechaFinal").datepicker({changeMonth: true}).val(fecAct);
        $("#fechaInicioInteres").datepicker({changeMonth: true}).val(fecAct);
        $("#fechaLimiteDeclaracion").datepicker({changeMonth: true}).val(fecAct);


    });
</script>
<body>
<!-- Librerias de carga para el datepicker -->
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>



<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once './menu.php'; ?>
    <!--inicio-->
  <div class="col-sm-10 text-left">

          <h2 id="forma-titulo3" align="center" style="margin-top: 0px;margin-right: 4px; margin-left: 4px;">Vencimiento Comercial</h2>

          <!--buscar vigencia-->
          <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-bottom: 0.2%;" class="client-form">
                <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_PaisJson.php" style="margin-top: -1%;margin-bottom: 3%;">

                              <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%; color: white">Los campos marcados con <strong style="color:white;">*</strong> son obligatorios.</p>

                     
                              <?php
                              $cuentaI = "SELECT id_unico,vigencia from gc_anno_comercial ORDER BY vigencia ASC";
                              $rsctai = $mysqli->query($cuentaI);
                              ?>
                              <div class="form-group" >
                                  <label for="sv" class="col-sm-5 control-label">Vigencia</label>
                                  <div class="col-sm-3 col-md-3 col-lg-3">
                                  <select name="vigencia" id="sv" required="true"  class="select2 form-control" title="Seleccione Vigencia" onchange="search()">
                                      <option value="">Vigencia</option>
                                      <?php while($rowv=mysqli_fetch_row($rsctai)){ ?> 
                                          <option value="<?php echo $rowv[0]?>"><?php echo $rowv[1] ?></option>
                                      <?php } ?>
                                  </select>
                                  </div>
                              </div>

                </form>

          </div>

          <?php  if(!empty($_GET['v'])){  ?>

          <!--form-->
          <div class="client-form" style="" class="col-sm-10">
            <form name="form" class="form-inline" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarVencimientoComercialJson.php" style="    margin-left: 9.5%;">
                       
                        <div class="form-group" >
                        <label for="fechaInicial"  class="col-sm-1 col-md-1 col-lg-1" style="padding-right: 29%;"><strong class="obligado">*</strong>Fecha Inicial:</label>

                        <input readonly="readonly " type="text" name="fechaInicial" onkeypress="return justNumbers(event);" id="fechaInicial"  class="form-control" style="height:30px;width:100px;" required="" />

                        </div>

                        <div class="form-group" >
                        <label for="fechaFinal"  class="col-sm-1 col-md-1 col-lg-1" style="padding-right: 29%;"><strong class="obligado">*</strong>Fecha Final:</label>

                        <input readonly="readonly " type="text" name="fechaFinal" onkeypress="return justNumbers(event);" id="fechaFinal"  class="form-control" style="height:30px;width:100px;" required="" />

                        </div>

                         <div class="form-group" >
                        <label for="fechaInicioInteres"  class="col-sm-1 col-md-1 col-lg-1" style="padding-right: 24%;"><strong class="obligado">*</strong>Fecha Inicio Interes:</label>

                          <input readonly="readonly " type="text" name="fechaInicioInteres" onkeypress="return justNumbers(event);" id="fechaInicioInteres"  class="form-control" style="height:30px;width:100px;margin-top: 2%;" required="" />
                        </div>

                        <div class="form-group" style="margin-left: -4%;">
                        <label for="fechaLimiteDeclaracion" type = "date" class="col-sm-1 col-md-1 col-lg-1" style="padding-right: 32%;"><strong class="obligado">*</strong>Fecha Limite Declaracion:</label>

                        <input readonly="readonly " type="text" name="fechaLimiteDeclaracion" onkeypress="return justNumbers(event);" id="fechaLimiteDeclaracion"  class="form-control" style="height:30px;width:100px;margin-top: 2%;" required="" />

                        </div>
                        <?php

                            $vigenc=$_GET['v'];
                            
                          ?>
                        <input type="hidden" name="vigenciaa" value="<?php echo $vigenc ?>">


                        
                        <div class="form-group" style="margin-top: 10px;margin-left: -60px;">
                            <button type="submit" id="btnGuardarDetalle" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                            <input type="hidden" name="MM_insert" >
                        </div>  
            
            </form>                        
          </div>
          <!--table vc-->
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -0.2%;">
              <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                  <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                      <thead>
                          <tr>
                              <td style="display: none;">Identificador</td>
                              <td width="7%" class="cabeza"></td>
                              <td class="cabeza"><strong>Fecha Inicial</strong></td>
                              <td class="cabeza"><strong>Fecha Final</strong></td>
                              <td class="cabeza"><strong>Fecha Inicio</strong></td>
                              <td class="cabeza"><strong>Fecha Limite</strong></td>
                              <td class="cabeza"><strong>Vigencia</strong></td>
                          </tr>
                          <tr>
                              <th class="cabeza" style="display: none;">Identificador</th>
                              <th width="7%"></th>
                              <th class="cabeza">Fecha Inicial</th>
                              <th class="cabeza">Fecha Final</th>
                              <th class="cabeza">Fecha Inicio</th>
                              <th class="cabeza">Fecha Limite</th>
                              <th class="cabeza">Vigencia</th>
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
                                  <a title="Modificar" style="text-decoration: none" class="glyphicon glyphicon-edit" onclick="javascript:open_modal_modificar(<?php echo $row[0] ?>)"></a>         
                              </td>
                              <td class="campos"><?php echo $row[1] ?></td>                
                              <td class="campos"><?php echo $row[2] ?></td>    <!--tipo-valor-periodo de tarifa-->            
                              <td class="campos"><?php echo $row[3]?></td>      
                              <td class="campos"><?php echo $row[4]?></td>  
                              <td class="campos"><?php echo $row[6]?></td>    
                          </tr>
                          <?php }
                          ?>
                      </tbody>
                  </table>
              </div>
          </div>

          <?php } ?>

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
          <p>¿Desea eliminar el registro seleccionado de Vencimiento Comercial?</p>
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
  <div class="modal fade" id="myModal20" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Ya existe un vencimiento comercial con esa vigencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver20" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


 <?php require 'GC_VENCIMIENTO_COMERCIAL_MODAL.php' ; ?>

  <!--Script que dan estilo al formulario-->
  <!--<script type="text/javascript" src="js/menu.js"></script>-->
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
                  url:"jsonComercio/eliminarVencimientoComercialJson.php?id="+id,
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

      function open_modal_modificar(id){  

            var form_data={                            
              id:id 
            };
            $.ajax({
                type:"POST",
                url: "GC_VENCIMIENTO_COMERCIAL_MODAL.php#mdlModificar",
                data:form_data,
                success: function (data) { 
                  $("#mdlModificar").html(data);
                  $(".modalvc").modal('show');
               }
           })  
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
          window.history.go(0);

      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
      window.history.go(0);
        
      });    
  </script>
    </body>
</html>




<link href="css/select2.css" rel="stylesheet">
<link href="css/select2-bootstrap.min.css" rel="stylesheet">


<script src="js/select2.js"></script>
<script src="js/md5.js"></script>
</head>
<script>
    
      $("#sv").select2({
        allowClear: true
      });
    
</script>

<script>
  function search(){
    var id = document.getElementById("sv").value;
    document.location = 'listar_GC_VENCIMIENTO_COMERCIAL.php?v='+md5(id);
    
  }
</script>
<style>
    label #nombre-error{
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;

    }
</style>

  <?php require_once 'footer.php'; ?>

</html>

