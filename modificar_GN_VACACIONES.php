<?php 
#13/03/2017 --- Nestor B ---- se modificó el input del número de acto para que muestre el número que trae
#14/03/2017 --- Nestor B --- se agregó la funcion del datepicker  fechaInicial y fechaDisfrute y la función del cambio de formato de fecha para que la muestre dd/mm/yyyy
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
  $id = (($_GET["id"]));

  $sql = "SELECT        v.id_unico,
                        v.fechainicio,
                        v.fechafin,
                        v.fechainiciodisfrute,
                        v.fechafindisfrute,
                        v.numeroacto,
                        v.fechaacto,
                        v.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                        v.tiponovedad,
                        tn.id_unico,
                        tn.nombre
                FROM gn_vacaciones v
                LEFT JOIN	gn_empleado e           ON v.empleado       = e.id_unico
                LEFT JOIN   gf_tercero t            ON e.tercero        = t.id_unico
                LEFT JOIN   gn_tipo_novedad tn      ON v.tiponovedad = tn.id_unico
                where md5(v.id_unico) = '$id'";
  $resultado = $mysqli->query($sql);
  $row = mysqli_fetch_row($resultado);    
    
        $vid    = $row[0];
        $vfi    = $row[1];
        $vff    = $row[2];
        $vfid   = $row[3];
        $vffd   = $row[4];
        $vnact  = $row[5];
        $vfact  = $row[6];
        $vemp   = $row[7];
        $empid  = $row[8];
        $empter = $row[9];
        $terid  = $row[10];
        $ternom = $row[11];
        $vtip   = $row[12];
        $tnid   = $row[13];
        $tnnom  = $row[14];
        
  
                                    
                                        
                                            
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
               
        $("#sltFechaA").datepicker({changeMonth: true,}).val();
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();
        $("#sltFechaID").datepicker({changeMonth: true,}).val();
        $("#sltFechaFD").datepicker({changeMonth: true,}).val();
        
        
});
</script>
<title>Modificar Vacaciones</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Vacaciones</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarVacacionesJson.php">
                              <input type="hidden" name="id" value="<?php echo $vid ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
<!------------------------- Consulta para llenar campo Empleado-->
                        <?php 
                        $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
							                            t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $vemp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $vemp?>"><?php echo $ternom?></option>
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
                    $vfi = $row[1];
                     if(!empty($row[1])||$row[1]!=''){
                            $vfi = trim($vfi, '"');
                            $fecha_div = explode("-", $vfi);
                            $anioi = $fecha_div[0];
                            $mesi  = $fecha_div[1];
                            $diai  = $fecha_div[2];
                            $vfi   = $diai.'/'.$mesi.'/'.$anioi;
                      }else{
                            $vfi='';
                      }
                ?>
                            <input name="sltFechaI" id="sltFechaI" title="Ingrese Fecha Inicio" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" onchange="javascript:fechaInicial();" value="<?php echo $vfi;?>">
           <!--
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaI" id="sltFechaI" step="1" value="<?php echo $vfi;?>">-->
           </div>
<!----------Fin Captura de Fecha Inicio-->                              
<!------------------------- Campo para seleccionar Fecha Fin-->
            <div class="form-group" style="margin-top: -10px;">
                <label for="FechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Fin:</label>
                <?php
                  $vff = $row[2];
                  if(!empty($row[2])||$row[2]!=''){
                          $vff = trim($vff, '"');
                          $fecha_div = explode("-", $vff);
                          $aniof = $fecha_div[0];
                          $mesf = $fecha_div[1];
                          $diaf = $fecha_div[2];
                          $vff = $diaf.'/'.$mesf.'/'.$aniof;
                  }else{
                        $vff='';
                  } 
                ?>
                            <input name="sltFechaF" id="sltFechaF" title="Ingrese Fecha Fin" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" value="<?php echo $vff;?>" > 
           <!--
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaF" id="sltFechaF" step="1" value="<?php echo $vff;?>">-->
           </div>
<!----------Fin Captura de Fecha Fin-->
<!------------------------- Campo para seleccionar Fecha Inicio Disrute-->
            <div class="form-group" style="margin-top: -10px;">
                <label for="FechaID" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicio Disfrute:</label>
                <?php
                  $vfid = $row[3];
                  if(!empty($row[3])||$row[3]){
                         $vfid = trim($vfid, '"');
                         $fecha_div = explode("-", $vfid);
                         $anioid = $fecha_div[0];
                         $mesid  = $fecha_div[1];
                         $diaid  = $fecha_div[2];
                         $vfid   = $diaid.'/'.$mesid.'/'.$anioid;
                  }else{
                        $vfid='';
                  } 
                ?>
                <input name="sltFechaID" id="sltFechaID" title="Ingrese Fecha Inicio Disfrute" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" onchange="javascript:fechaDisfrute();" value="<?php echo $vfid; ?>">  
           <!--
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaID" id="sltFechaID" step="1" value="<?php echo $vfid;?>">-->
           </div>
<!----------Fin Captura de Fecha Inicio Disfrute-->                              
<!------------------------- Campo para seleccionar Fecha Fin Disfrute-->
           <div class="form-group" style="margin-top: -10px;">
           
                <label for="FechaFD" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Fin Disfrute:</label>
                <?php
                  $vffd = $row[4];
                  if(!empty($row[4])||$row[4]!=''){
                         $vffd = trim($vffd, '"');
                         $fecha_div = explode("-", $vffd);
                         $aniofd = $fecha_div[0];
                         $mesfd = $fecha_div[1];
                         $diafd = $fecha_div[2];
                         $vffd = $diafd.'/'.$mesfd.'/'.$aniofd;
                  }else{
                         $vffd='';
                  } 
                ?>
                 <input name="sltFechaFD" id="sltFechaFD" title="Ingrese Fecha Fin Disfrute" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" value="<?php echo $vffd;?>">           
                <!--
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaFD" id="sltFechaFD" step="1" value="<?php echo $vffd;?>">-->

           </div>
<!----------Fin Captura de Fecha Fin Disfrute-->                 
<!------------------------- Campo Llenar Número Acto-->
                            <div class="form-group" style="margin-top: -10px;">
                                 <label for="numeroA" class="col-sm-5 control-label"><strong class="obligado"></strong>Número Acto:</label>
                                <input type="text" name="txtNumeroA" id="txtNumeroA" class="form-control" maxlength="100" title="Ingrese el número de acto" onkeypress="return txtValida(event,'num_car')" value="<?php echo $vnact;?>">
                            </div>                              
<!------------------------- Fin Campo Llenar Número Acto-->
<!------------------------- Campo para seleccionar Fecha Acto-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="FechaA" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Acto:</label>
                <?php
                  $vfact = $row[6];
                  if(!empty($row[6])||$row[6]!=''){
                         $vfact = trim($vfact, '"');
                         $fecha_div = explode("-", $vfact);
                         $aniofa = $fecha_div[0];
                         $mesfa = $fecha_div[1];
                         $diafa = $fecha_div[2];
                         $vfact = $diafa.'/'.$mesfa.'/'.$aniofa;
                  }else{
                         $vfact='';
                  } 
                ?>
                <input name="sltFechaA" id="sltFechaA" title="Ingrese Fecha Acto" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" value="<?php echo $vfact;?>">
              <!-- <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaA" id="sltFechaA" step="1" value="<?php echo $vfac;?>">-->
           </div>
<!----------Fin Captura de Fecha Acto-->                              
<!------------------------- Consulta para llenar campo Tipo Novedad-->
            <?php 
            if(empty($vtip))
                $tip = "SELECT id_unico, nombre FROM gn_tipo_novedad";
            else
                $tip = "SELECT id_unico, nombre FROM gn_tipo_novedad WHERE id_unico != $vtip";
            
            $tipon = $mysqli->query($tip);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Tipo Novedad:
                </label>
                <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione tipo dedicación" style="height: 30px">
                <option value="<?php echo $tnid?>"><?php echo $tnnom?></option>
                
                    <?php 
                    while ($filaTN = mysqli_fetch_row($tipon)) { ?>                   
                    <option value="<?php echo $filaTN[0];?>"><?php echo $filaTN[1];?></option>
                    <?php
                    }
                    ?>
                </select>   
                <option value=""> </option>
            </div>
<!------------------------- Fin Consulta para llenar Tipo Novedad-->
                                                           
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
        var fechafi= document.getElementById('sltFechaF').value;
          var fi = document.getElementById("sltFechaF");
        fi.disabled=false;
      
       
            $( "#sltFechaF" ).datepicker( "destroy" );
            $( "#sltFechaF" ).datepicker({ changeMonth: true, minDate: fechain});
                   
}

function fechaDisfrute(){
        var fechain= document.getElementById('sltFechaID').value;
        var fechafi= document.getElementById('sltFechaFD').value;
          var fi = document.getElementById("sltFechaFD");
        fi.disabled=false;
      
       
            $( "#sltFechaFD" ).datepicker( "destroy" );
            $( "#sltFechaFD" ).datepicker({ changeMonth: true, minDate: fechain});
      
}
</script>
    </body>
</html>
