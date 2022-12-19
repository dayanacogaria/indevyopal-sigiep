<?php

/* 
 * ************
 * ***Autor*****
 * **DANIEL.NC***
 * ***************
 */

require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>
   <title>Registrar Resolución Predio</title>
    </head>
    <body>
        <link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script>
$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>

   <style>
    .form-control {font-size: 12px;}
    
</style>

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
       
        
        $("#sltFecha").datepicker({changeMonth: true,}).val(fecAct);                
});
</script>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Resolución Predio</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarResolucionPredioPJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              <!----------Script para invocar Date Picker-->
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                    $("#datepicker").datepicker();
                                });
                                </script>
                            <!------------------------- Campo para seleccionar Fecha-->
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="Fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha:</label>
                                <input required="required" style="width:auto" class="col-sm-2 input-sm" type="text" name="sltFecha" id="sltFecha" value="<?php echo date("Y-m-d");?>" title="Ingrese Fecha">
                            </div>
                            <!----------Fin Captura de Fecha Inicio-->                            
                            <!------------------------- Consulta para llenar campo Resolución-->
                            <?php 
                                $rs = "SELECT id_unico, CONCAT(numero,' - ',nombre) FROM gr_resolucion";
                                $res = $mysqli->query($rs);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Resolución:
                                </label>
                                <select name="sltResolucion" class="form-control" id="sltResolucion" title="Seleccione Resolución" style="height: 30px" required>
                                <option>Resolución</option>                
                                <?php 
                                    while ($filaR = mysqli_fetch_row($res)) { ?>                   
                                    <option value="<?php echo $filaR[0];?>"><?php echo $filaR[1];?></option>
                                <?php
                                }
                                ?>
                                </select>   
                            </div>
                            <!------------------------- Fin Consulta para llenar Resolución-->
                            <!------------------------- Consulta para llenar campo Predio-->
                            <?php 
                                $pr = "SELECT id_unico, nombre FROM gp_predio1";
                                $pre = $mysqli->query($pr);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Predio:
                                </label>
                                <select name="sltPredio" class="form-control" id="sltPredio" title="Seleccione Predio" style="height: 30px" required>
                                <option value="">Predio</option>                
                                <?php 
                                    while ($filaP = mysqli_fetch_row($pre)) { ?>                   
                                    <option value="<?php echo $filaP[0];?>"><?php echo $filaP[1];?></option>
                                <?php
                                }
                                ?>
                                </select>   
                            </div>
                            <!------------------------- Fin Consulta para llenar Predio-->
                            <div class="form-group" style="margin-top: 10px;">
                               <label for="no" class="col-sm-5 control-label"></label>
                               <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                            </div>

                          </form>
                      </div>
                  </div>                  
              </div>
        </div>
        <?php require_once './footer.php'; ?>
    </body>
</html>