
<?php
#16/03/2017 --- Nestor B --- se agrego la función del datepicker y la función fecha inicial ademas se modificó la forma como se llama los calendarios
#02/06/2017 --- Nestor B --- se agregó el campo de dias de nómina
require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style >
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
</style>
<script src="js/jquery-ui.js"></script>
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
               
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();
        
        
});
</script>
   <title>Registrar Int. Desc</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-8 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Interés y Descuentos</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; font-size: 13px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarIntDescJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                                            
<!----------Campo para llenar Año-->
                              <div class="form-group" style="margin-top: -10px;">
                                <label for="Anno" class="col-sm-5 control-label"><strong class="obligado">*</strong>Año:</label>
                                <input type="text" name="Anno" id="Anno" class="form-control" maxlength="100" title="Ingrese el Año" onkeypress="return txtValida(event,'num')" placeholder="Año" required>
                              </div>                                    
<!----------Fin Campo Año-->
<!----------Campo para llenar Valor-->
                              <div class="form-group" style="margin-top: -10px;">
                                <label for="Valor" class="col-sm-5 control-label"><strong class="obligado">*</strong>Valor:</label>
                                <input type="text" name="txtvalor" id="txtvalor" class="form-control" maxlength="100" title="Ingrese el valor" onkeypress="return txtValida(event,'dec')" placeholder="Valor" required>
                              </div>                                    
<!----------Fin Campo Valor-->
<!----------Script para invocar Date Picker-->
                              <script type="text/javascript">
                                  $(document).ready(function() {
                                    $("#datepicker").datepicker();
                                  });
                              </script>
<!------------------------- Campo para seleccionar Fecha Inicio-->
                              <div class="form-group" style="margin-top: -10px;">
                                  <label for="fechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicio:</label>
                                  <input name="sltFechaI" id="sltFechaI" title="Ingrese Fecha Inicial" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"  onchange="javaScript:fechaInicial();" placeholder="Ingrese la fecha">  
                                  <!--  <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaI" id="sltFechaI" step="1" value="<?php echo date("Y-m-d");?>"> -->
                              </div>
<!----------Fin Captura de Fecha Inicio-->
<!------------------------- Campo para seleccionar Fecha Fin-->
                              <div class="form-group" style="margin-top: -10px;">
                                  <label for="fechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Fin:</label>
                                  <input name="sltFechaF" id="sltFechaF" title="Ingrese Fecha Final" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" placeholder="Ingrese la fecha" disabled="true">  
                                  <!-- <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaF" id="sltFechaF" step="1" value="<?php echo date("Y-m-d");?>"> -->
                              </div>
<!----------Fin Captura de Fecha Fin-->                             
<!------------------------- Consulta para llenar Estado Empleado-->
                              <?php 
                                  $es   = "SELECT id_unico, nombre FROM gr_tipo_di";
                                  $esta = $mysqli->query($es);
                              ?>
                                <div class="form-group" style="margin-top: -5px">
                                    <label  for="sltTipo" class="control-label col-sm-5">
                                        <strong class="obligado"></strong>Tipo:
                                    </label>
                                    <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo" style="height: 30px">
                                     <option value="">Tipo</option>
                                      <?php 
                                          while ($filaES = mysqli_fetch_row($esta)) { ?>

                                              <option value="<?php echo $filaES[0];?>"><?php echo $filaES[1]; ?></option>
                                      <?php
                                          }
                                      ?>
                                    </select>   
                                </div>
<!----------Fin Consulta Para llenar Estado Empleado-->


                                <div class="form-group" style="margin-top: 10px;">
                                    <label for="no" class="col-sm-5 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                                </div>

                          </form>
                      </div>
                  </div>                  
                   <div class="col-sm-8 col-sm-2" style="margin-top:-2px">
                <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                                <tr>                                        
                                    <th>
                                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                    </th>
                                </tr>
                        </thead>
                        <tbody>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_ESTADO_PERIODO.php">ESTADO</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_PROCESO_NOMINA.php">TIPO PROCESO</a>
                                </td>
                            </tr>                            
                                                                                    
                </table>
          </div>
              </div>
        </div>        
        <?php require_once './footer.php'; ?>
<script>
function fechaInicial(){
        var fechain= document.getElementById('sltFechaI').value;
        var fechafi= document.getElementById('sltFechaF').value;
          var fi = document.getElementById("sltFechaF");
        fi.disabled=false;
      
       
            $( "#sltFechaF" ).datepicker( "destroy" );
            $( "#sltFechaF" ).datepicker({ changeMonth: true, minDate: fechain});
     
}
</script>
    </body>
</html>
    