<?php 
#14/03/2017 --- Nestor B --- se agregó la funcion del datepicker  fechaInicial y fechaDisfrute y la función del cambio de formato de fecha para que la muestre dd/mm/yyyy

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT    a.id_unico,
                    a.empleado,
                    e.id_unico,
                    e.tercero,
                    t.id_unico,
                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                    a.tipo,
                    ta.id_unico,
                    ta.nombre,
                    a.tercero,
                    ter.id_unico,
                    ter.razonsocial,
                    a.estado,
                    ea.id_unico,
                    ea.nombre,
                    a.codigoadmin,
                    a.observaciones,
                    a.fechaafiliacion,
                    a.fecharetiro
                FROM gn_afiliacion a	 
                LEFT JOIN	gn_empleado e           ON a.empleado = e.id_unico
                LEFT JOIN   gf_tercero t            ON e.tercero = t.id_unico
                LEFT JOIN   gn_tipo_afiliacion ta   ON a.tipo = ta.id_unico
                LEFT JOIN   gf_tercero ter          ON a.tercero = ter.id_unico
                LEFT JOIN   gn_estado_afiliacion ea ON a.estado = ea.id_unico
                where md5(a.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
    $aid   = $row[0];
    $aemp  = $row[1];
    $eid   = $row[2];
    $eter  = $row[3];
    $tid1  = $row[4];
    $ter1  = $row[5];
    $atip  = $row[6];
    $taid  = $row[7];
    $tanom = $row[8];
    $ater  = $row[9];
    $tid2  = $row[10];
    $ter2  = $row[11];
    $aest  = $row[12];
    $eaid  = $row[13];
    $eanom = $row[14];
    $acod  = $row[15];
    $aobs  = $row[16];
    $afa   = $row[17];
    $afr   = $row[18];
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
        $("#sltFechaR").datepicker({changeMonth: true,}).val();
        
        
});
</script>
<title>Modificar Afiliación</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Modificar Afiliación</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: -10px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarAfiliacionJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $eid";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $aemp?>"><?php echo $ter1?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!------------------------- Consulta para llenar Tipo-->
                        <?php 
                        if(empty($atip))
                            $af = "SELECT id_unico, nombre FROM gn_tipo_afiliacion";
                        else
                            $af = "SELECT id_unico, nombre FROM gn_tipo_afiliacion WHERE id_unico != $atip";
                        
                        $afil = $mysqli->query($af);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Tipo:
                            </label>
                            <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo" style="height: 30px">
                            <option value="<?php echo $taid?>"><?php echo $tanom?></option>
                                <?php 
                                while ($filaTA = mysqli_fetch_row($afil)) { ?>
                                <option value="<?php echo $filaTA[0];?>"><?php echo $filaTA[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Tipo-->
<!------------------------- Consulta para llenar campo Tercero-->
                        <?php 
                        if(empty($ater))  
                            $ter = "SELECT      pt.perfil,
                                                pt.tercero,
                                                t.razonsocial,
                                                p.id_unico,
                                                p.nombre
                            FROM gf_perfil_tercero pt 
                            LEFT JOIN gf_tercero t  ON pt.tercero = t.id_unico
                            LEFT JOIN gf_perfil p ON pt.perfil = p.id_unico";
                        else
                            $ter = "SELECT      pt.perfil,
                                                pt.tercero,
                                                t.razonsocial,
                                                p.id_unico,
                                                p.nombre
                            FROM gf_perfil_tercero pt 
                            LEFT JOIN gf_tercero t  ON pt.tercero = t.id_unico
                            LEFT JOIN gf_perfil p ON pt.perfil = p.id_unico
                            WHERE pt.perfil = 11 AND t.id_unico != $ater";
                        
                            
                        $tercero = $mysqli->query($ter);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Tercero:
                            </label>
                            <select name="sltTercero" class="form-control" id="sltTercero" title="Seleccione tercero" style="height: 30px">
                            <option value="<?php echo $tid2?>"><?php echo $ter2?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($tercero)) { ?>
                                <option value="<?php echo $filaT[1];?>"><?php echo $filaT[2]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Tercero-->
<!------------------------- Consulta para llenar Estado Afiliación-->
                        <?php 
                        if(empty($aest))
                            $es   = "SELECT id_unico, nombre FROM gn_estado_afiliacion";
                        else
                            $es   = "SELECT id_unico, nombre FROM gn_estado_afiliacion WHERE id_unico != $aest";
                        $esta = $mysqli->query($es);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estado:
                            </label>
                            <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                            <option value="<?php echo $eaid?>"><?php echo $eanom?></option>
                                <?php 
                                while ($filaES = mysqli_fetch_row($esta)) { ?>
                                <option value="<?php echo $filaES[0];?>"><?php echo $filaES[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Estado Afiliación-->
                              
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha Afiliación-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fechaA" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Afiliación:</label>
                <?php
                    $afa = $row[17];
                    if(!empty($row[17])||$row[17]){
                           $afa = trim($afa, '"');
                           $fecha_div = explode("-", $afa);
                           $anioa = $fecha_div[0];
                           $mesa = $fecha_div[1];
                           $diaa = $fecha_div[2];
                           $afa = $diaa.'/'.$mesa.'/'.$anioa;
                    }else{

                           $afa = '';
                    } 
                ?>
                <input name="sltFechaA" id="sltFechaA" title="Ingrese Fecha de Afiliación" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" onchange="javascript:fechaInicial();" value="<?php echo $afa;?>">
                <!--<input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaA" id="sltFechaA" step="1" value="<?php echo $afa;?>">-->
           </div>
<!----------Fin Captura de Fecha Afiliación-->
<!------------------------- Campo para seleccionar Fecha Retiro-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fechaR" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Retiro:</label>
                <?php
                     $afr = $row[18];
                     if(!empty($row[18])||$row[18]){
                           $afr = trim($afr, '"');
                           $fecha_div = explode("-", $afr);
                           $anior = $fecha_div[0];
                           $mesr = $fecha_div[1];
                           $diar = $fecha_div[2];
                           $afr = $diar.'/'.$mesr.'/'.$anior;
                     }else{

                           $afr = '';
                    } 
                ?>  
                <input name="sltFechaR" id="sltFechaR" title="Ingrese Fecha de Retiro" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" " value="<?php echo $afr;?>">      
                <!--<input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaR" id="sltFechaR" step="1" value="<?php echo $afr;?>">-->
           </div>
<!----------Fin Captura de Fecha Fin-->                              
<!----------Campo para llenar Codigo Administrador-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="codigoadmin" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código Admin.:</label>
                     <input type="text" name="txtCodigoA" id="txtCodigoA" class="form-control" value="<?php echo $acod?>" maxlength="100" title="Ingrese el código admin." onkeypress="return txtValida(event,'num_car')" placeholder="Código Administrador">
                </div>                                    
<!----------Fin Campo Codigo Administrador-->
<!----------Campo para llenar Observaciones-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Observaciones" class="col-sm-5 control-label"><strong class="obligado">*</strong>Observaciones:</label>
                     <input type="text" name="txtObservaciones" id="txtObservaciones" value="<?php echo $aobs?>" class="form-control" maxlength="100" title="Ingrese las observaciones" onkeypress="return txtValida(event,'car')" placeholder="Observaciones">
                </div>                                    
<!----------Fin Campo Observaciones-->
                                                           
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
        var fechain= document.getElementById('sltFechaA').value;
        var fechafi= document.getElementById('sltFechaR').value;
          var fi = document.getElementById("sltFechaR");
        fi.disabled=false;
      
       
            $( "#sltFechaR" ).datepicker( "destroy" );
            $( "#sltFechaR" ).datepicker({ changeMonth: true, minDate: fechain});
                   
}
</script>
    </body>
</html>
