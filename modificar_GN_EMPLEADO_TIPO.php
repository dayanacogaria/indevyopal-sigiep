<?php 
#14/03/2017 --- Nestor B --- se agregó la funcion del datepicker  fechaInicial y fechaDisfrute y la función del cambio de formato de fecha para que la muestre dd/mm/yyyy

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT        et.id_unico,
                        et.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        et.fechainicio,
                        et.fechacancelacion,
                        et.tipo,
                        te.id_unico,
                        te.nombre,
                        et.estado,
                        ee.id_unico,
                        ee.nombre,
                        et.observaciones
                FROM gn_empleado_tipo et	 
                LEFT JOIN	gn_empleado e           ON et.empleado = e.id_unico
                LEFT JOIN   gf_tercero t            ON e.tercero = t.id_unico
                LEFT JOIN   gn_tipo_empleado te     ON et.tipo = te.id_unico
                LEFT JOIN   gn_estado_empleado ee   ON et.estado = ee.id_unico
                where md5(et.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
        $etid   = $row[0];
        $etemp  = $row[1];
        $eid    = $row[2];
        $eter   = $row[3];
        $terid  = $row[4];
        $ternom = $row[5];
        $etfeci = $row[6];
        $etfecc = $row[7];
        $ettip  = $row[8];
        $teid   = $row[9];
        $tenom  = $row[10];
        $etest  = $row[11];
        $eeid   = $row[12];
        $eenom  = $row[13];
        $etobs  = $row[14];
         
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
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
        $("#sltFechaC").datepicker({changeMonth: true,}).val();
        
        
});
</script>
<title>Modificar Empleado Tipo</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Empleado Tipo</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarEmpleadoTipoJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
<!------------------------- Consulta para llenar campo Empleado-->
                        <?php 
                        $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $etemp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $etemp?>"><?php echo $ternom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha Inicio-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="FechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicio:</label>
                <?php
                    $etfeci = $row[6];
                    if(!empty($row[6])||$row[6]){
                           $etfeci= trim($etfeci, '"');
                           $fecha_div = explode("-", $etfeci);
                           $anioa = $fecha_div[0];
                           $mesa = $fecha_div[1];
                           $diaa = $fecha_div[2];
                           $etfeci = $diaa.'/'.$mesa.'/'.$anioa;
                    }else{

                           $etfeci = '';
                    } 
                ?>
                <input name="sltFechaI" id="sltFechaI" title="Ingrese Fecha de Afiliación" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" onchange="javascript:fechaInicial();" value="<?php echo $etfeci;?>">
                <!--<input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaI" id="sltFechaI" step="1" value="<?php echo $etfeci;?>">-->
           </div>
<!----------Fin Captura de Fecha Inicio-->
<!------------------------- Campo para seleccionar Fecha Cancelación-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Cancelación:</label>
                <?php
                    $etfecc = $row[7];
                    if(!empty($row[7])||$row[7]){
                           $etfecc= trim($etfecc, '"');
                           $fecha_div = explode("-", $etfecc);
                           $anioa = $fecha_div[0];
                           $mesa = $fecha_div[1];
                           $diaa = $fecha_div[2];
                           $etfecc = $diaa.'/'.$mesa.'/'.$anioa;
                    }else{

                           $etfecc = '';
                    } 
                ?>
                <input name="sltFechaC" id="sltFechaC" title="Ingrese Fecha de Afiliación" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" value="<?php echo $etfecc;?>">
                <!--<input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaC" id="sltFechaC" step="1" value="<?php echo $etfecc;?>">-->
           </div>
<!----------Fin Captura de Fecha Cancelación-->
<!----------Campo para llenar Observaciones-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Observaciones" class="col-sm-5 control-label"><strong class="obligado"></strong>Observaciones:</label>
                     <input type="text" name="txtObservaciones" id="txtObservaciones" value="<?php echo $etobs?>" class="form-control" maxlength="100" title="Ingrese las observaciones" onkeypress="return txtValida(event,'num_car')" placeholder="Observaciones">
                </div>                                    
<!----------Fin Campo Observaciones-->
<!------------------------- Consulta para llenar campo Tipo Empleado-->
            <?php 
            $tip = "SELECT id_unico, nombre FROM gn_tipo_empleado WHERE id_unico != $ettip";
            $tipe = $mysqli->query($tip);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Tipo Empleado:
                </label>
                <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo empleado" style="height: 30px">
                <option value ="<?php echo $teid?>"><?php echo $tenom?></option>
                
                    <?php 
                    while ($filaTE = mysqli_fetch_row($tipe)) { ?>                   
                    <option value="<?php echo $filaTE[0];?>"><?php echo $filaTE[1];?></option>
                    <?php
                    }
                    ?>
                    <option value =null> </option>
                        
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Tipo Empleado-->
<!------------------------- Consulta para llenar campo Estado Empleado-->
            <?php 
            $est = "SELECT id_unico, nombre FROM gn_estado_empleado WHERE id_unico != $etest";
            $estado = $mysqli->query($est);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Estado Empleado:
                </label>
                <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione estado" style="height: 30px">
                <option value ="<?php echo $eeid?>"><?php echo $eenom?></option>
                
                    <?php 
                    while ($filaETC = mysqli_fetch_row($estado)) { ?>                   
                    <option value="<?php echo $filaETC[0];?>"><?php echo $filaETC[1];?></option>
                    <?php
                    }
                    ?>
                    <option value =null> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Estado Empleado-->
                                                           
                            <div class="form-group" style="margin-top: 10px;">
                              <label for="no" class="col-sm-5 control-label"></label>
                              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            </div>


                          </form>
                      </div>
                  </div>                  
              </div>
        </div>
        <?php require_once './footer.php'; ?>
<script>
function fechaInicial(){
        var fechain= document.getElementById('sltFechaI').value;
        var fechafi= document.getElementById('sltFechaC').value;
          var fi = document.getElementById("sltFechaC");
        fi.disabled=false;
      
       
            $( "#sltFechaC" ).datepicker( "destroy" );
            $( "#sltFechaC" ).datepicker({ changeMonth: true, minDate: fechain});
                   
}
</script>
    </body>
</html>
