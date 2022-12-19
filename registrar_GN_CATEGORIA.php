
<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>
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
               
        $("#sltFechaM").datepicker({changeMonth: true,}).val();
        
        
});
</script>
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
   <title>Registrar Categoría</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-8 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Categoría</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarCategoriaJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                                            
<!----------Campo para llenar Codigo Interno-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="codigoadmin" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código Interno:</label>
                     <input type="text" name="txtCodigoI" id="txtCodigoI" class="form-control" maxlength="100" title="Ingrese el código interno" onkeypress="return txtValida(event,'num_car')" placeholder="Código Interno" required>
                </div>                                    
<!----------Fin Campo Codigo Interno-->
<!----------Campo para llenar Nombre-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Nombre" class="col-sm-5 control-label"><strong class="obligado"></strong>Nombre:</label>
                     <input type="text" name="txtNombre" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
                </div>                                    
<!----------Fin Campo Nombre-->
<!----------Campo para llenar Salario Actual-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="SalarioAC" class="col-sm-5 control-label"><strong class="obligado"></strong>Salario Actual:</label>
                     <input type="text" name="txtSalarioAC" id="txtSalarioAC" class="form-control" maxlength="100" title="Ingrese el salario actual" onkeypress="return txtValida(event,'num')" placeholder="Salario Actual">
                </div>                                    
<!----------Fin Campo Salario Actual-->
<!----------Campo para llenar Salario Anterior-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="SalarioAN" class="col-sm-5 control-label"><strong class="obligado"></strong>Salario Anterior:</label>
                     <input type="text" name="txtSalarioAN" id="txtSalarioAN" class="form-control" maxlength="100" title="Ingrese el salario anterior" onkeypress="return txtValida(event,'num')" placeholder="Salario Anterior">
                </div>                                    
<!----------Fin Campo Salario Anterior-->
<!----------Fecha Modificacion Salario-->
<div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Modificación Salario:</label>
                 <input name="sltFechaM" id="sltFechaM" title="Ingrese Fecha Modificación" type="text" style="height: 30px" class="form-control col-sm-1" value="" >
               <!-- <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaM" id="sltFechaM" step="1" value="<?php echo $tcfecm;?>"> -->
           </div>
<!----------Fin Fecha Modificacion Salarior-->
<!----------Campo para llenar Gasto Representación-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="GastoR" class="col-sm-5 control-label"><strong class="obligado"></strong>Gasto Representación:</label>
                     <input type="text" name="txtGastoR" id="txtGastoR" class="form-control" maxlength="100" title="Ingrese el valor de gastos representación" onkeypress="return txtValida(event,'num')" placeholder="Gastos Representación">
                </div>                                    
<!-------------------------Fin campo Gasto Representación-->
<!----------Consulta para llenar campo Nivel-->                                                          
                        <?php 
                        $ni = "SELECT id_unico, nombre from gn_nivel ORDER BY id_unico ASC";
                        $niv = $mysqli->query($ni);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Nivel:
                            </label>
                            <select name="sltNivel" class="form-control" id="sltNivel" title="Seleccione nivel" style="height: 30px">
                            <option value="">Nivel</option>
                                <?php 
                                while ($filaN = mysqli_fetch_row($niv)) { ?>
                                <option value="<?php echo $filaN[0];?>"><?php echo $filaN[1]; ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Nivel-->
<!------------------------- Consulta para llenar Estado Categoría-->
                        <?php 
                        $es   = "SELECT id_unico, nombre FROM gn_estado_categoria ORDER BY id_unico ASC";
                        $esta = $mysqli->query($es);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estado Categoría:
                            </label>
                            <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                            <option value="">Estado</option>
                                <?php 
                                while ($filaES = mysqli_fetch_row($esta)) { ?>
                                <option value="<?php echo $filaES[0];?>"><?php echo $filaES[1]; ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Estado Categoría-->
<!------------------------- Consulta para llenar tipo persona SUI-->
<div class="form-group" style="margin-top: -10px;">
                                <label for="tipoSui" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Persona SUI:</label>
                                <select name="tipoSui" class="select2_single form-control" id="tipoSui" title="Seleccione Tipo Persona SUI" style="height: 30px">
                                    <option value="">Seleccione Tipo Persona SUI</option>    
                                    <option value="Temporal">Temporal</option>
                                    <option value="Nomina">Nomina</option>
                                    <option value="Aprendiz">Aprendiz</option>
                                </select>
                            </div> 
<!----------Fin Consulta Para llenar tipo persona SUI-->  
                     
                            <div class="form-group" style="margin-top: 10px;">
                               <label for="no" class="col-sm-5 control-label"></label>
                               <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                            </div>
                          </form>
                      </div>
                  </div>                  
                  <div class="col-sm-8 col-sm-1" styl>        
                                </div>
              </div>
        </div>        
        <?php require_once './footer.php'; ?>
    </body>
</html>
    