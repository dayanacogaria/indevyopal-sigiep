<?php 
#16/06/2017 --- Nestor B --- se agrego la función del datepicker y la función fecha inicial ademas se modificó la forma como se llama los calendarios

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();

$id = (($_GET["id"]));

  $sql = "SELECT    es.id_unico,
                    es.empleado,
                    e.id_unico,
                    e.tercero,
                    t.id_unico,
                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                    es.titulo,
                    es.fechaterminacion,
                    es.numerosemestres,
                    es.graduado,
                    es.tarjetaprofesional,
                    es.tipo,
                    te.id_unico,
                    te.nombre,
                    es.institucioneducativa,
                    ie.id_unico,
                    ie.nombre
                FROM gn_estudio es	 
                LEFT JOIN	gn_empleado e               ON es.empleado = e.id_unico
                LEFT JOIN   gf_tercero t                ON e.tercero = t.id_unico
                LEFT JOIN   gn_tipo_estudio te          ON es.tipo = te.id_unico
                LEFT JOIN   gn_institucion_educativa ie ON es.institucioneducativa = ie.id_unico
                where md5(es.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
    
                    $esid   = $row[0];
                    $esemp  = $row[1];
                    $eid    = $row[2];
                    $eter   = $row[3];
                    $tid    = $row[4];
                    $tnom   = $row[5];
                    $estit  = $row[6];
                    $esfec  = $row[7];
                    $esnum  = $row[8];
                    $esgrad = $row[9];
                    $estp   = $row[10];
                    $estip  = $row[11];
                    $teid   = $row[12];
                    $tenom  = $row[13];
                    $esie   = $row[14];
                    $ieid   = $row[15];
                    $ienom  = $row[16];    
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
               
        $("#sltFechaT").datepicker({changeMonth: true,}).val();

        
});
</script>
<title>Modificar Estudio</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Estudio</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarEstudioJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $esemp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $eid?>"><?php echo $tnom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!----------Campo para llenar Título-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Titulo" class="col-sm-5 control-label"><strong class="obligado">*</strong>Título:</label>
                     <input type="text" name="txtTitulo" id="txtTitulo" class="form-control" value="<?php echo $estit?>" maxlength="100" title="Ingrese el título académico" onkeypress="return txtValida(event,'car')" placeholder="Título Profesional" required>
                </div>                                    
<!----------Fin Campo Título-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha Terminación-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fechaA" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Terminación:</label>
                 <?php                                         
                    $esfec = $row[7];
                     if(!empty($row[7])||$row[7]!=''){
                            $esfec = trim($esfec, '"');
                            $fecha_div = explode("-", $esfec);
                            $anioi = $fecha_div[0];
                            $mesi  = $fecha_div[1];
                            $diai  = $fecha_div[2];
                            $esfec   = $diai.'/'.$mesi.'/'.$anioi;
                      }else{
                            $esfec='';
                      }
                ?>
                 <input name="sltFechaT" id="sltFechaT" title="Ingrese Fecha Inicial" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"   value="<?php echo $esfec;?>">
               <!-- <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaT" id="sltFechaT" step="1" max="2016-12-31" value="<?php echo $esfec;?>"> -->
           </div>
<!----------Fin Captura de Fecha Terminación-->                                                            
<!----------Campo para llenar No. Semestres-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Semestres" class="col-sm-5 control-label"><strong class="obligado">*</strong>Número Semestres:</label>
                     <input type="text" name="txtNumeroS" id="txtNumeroS"  value="<?php echo $esnum?>"class="form-control" maxlength="100" title="Ingrese el número de semestres" onkeypress="return txtValida(event,'num')" placeholder="Número de semestres" required>
                </div>                                    
<!----------Fin Campo No. Semestres-->
<!------------------------- Campo para seleccionar Acumulable-->
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="es_graduado" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;">*</strong>Graduado:</label>
                        <?php if ($esgrad==1) { ?>
                        <input  type="radio" name="es_graduado" id="es_graduado"  value="1" checked="checked">SI
                        <input  type="radio" name="es_graduado" id="es_graduado" value="2">NO
                        <?php } else { ?>
                        <input  type="radio" name="es_graduado" id="es_graduado"  value="1">SI
                        <input  type="radio" name="es_graduado" id="es_graduado" value="2" checked="checked">NO
                        <?php } ?>
                    </div>
<!----------Fin Campo para seleccionar Acumulable-->
<!----------Campo para llenar Tarjeta Profesional-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Tarjeta" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tarjeta Profesional:</label>
                     <input type="text" name="txtTarjetaP" id="txtTarjetaP" value="<?php echo $estp;?>" class="form-control" maxlength="100" title="Ingrese Tarjeta Profesional" onkeypress="return txtValida(event,'num_car')" >
                </div>                                    
<!----------Fin Campo Tarjeta Profesional-->
<!------------------------- Consulta para llenar Tipo Estudio-->
                        <?php 
                        $es = "SELECT id_unico, nombre FROM gn_tipo_estudio WHERE id_unico !=$estip";
                        $est = $mysqli->query($es);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tipo:
                            </label>
                            <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo Estudio" style="height: 30px" required="">
                            <option value="<?php echo $teid;?>"><?php echo $tenom?></option>
                                <?php   
                                while ($filaE = mysqli_fetch_row($est)) { ?>
                                <option value="<?php echo $filaE[0];?>"><?php echo $filaE[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Tipo Estudio-->
<!------------------------- Consulta para llenar Institución Educativa-->
                        <?php 
                        $in = "SELECT id_unico, nombre FROM gn_institucion_educativa WHERE id_unico !=$esie ORDER BY id_unico ASC";
                        $ined = $mysqli->query($in);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Institución Educativa:
                            </label>
                            <select name="sltInstE" class="form-control" id="sltInstE" title="Seleccione Institución Educativa" style="height: 30px" required="">
                            <option value="<?php echo $ieid;?>"><?php echo $ienom;?></option>
                                <?php 
                                while ($filaIE = mysqli_fetch_row($ined)) { ?>
                                <option value="<?php echo $filaIE[0];?>"><?php echo $filaIE[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Institución Educativa-->
                                                           
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
    </body>
</html>
