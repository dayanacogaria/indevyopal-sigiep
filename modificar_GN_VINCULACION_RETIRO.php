<?php 
#15/03/2017 --- Nestor B --- se agregó la función del datepicker y la forma como se llama ademas se agrgó el método que cambia el format ode fecha
#16/03/2017 --- Nestor B --- se agregó la función estado para que me deshabilite los selects dependindo de que estado sea y se modicaron las consultas de los selects vinculación y estado
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
  $id = (($_GET["id"]));

  $sql = "SELECT        vr.id_unico,
                        vr.numeroacto,
                        vr.fechaacto,
                        vr.fecha,
                        vr.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        vr.tipovinculacion,
                        tv.id_unico,
                        tv.nombre,
                        vr.estado,
                        evr.id_unico,
                        evr.nombre,
                        vr.vinculacionretiro,
                        tvr.id_unico,
                        tvr.numeroacto,
                        vr.causaretiro,
                        cr.id_unico,
                        cr.nombre
                       
                FROM gn_vinculacion_retiro vr
                LEFT JOIN	gn_empleado e                     ON vr.empleado          = e.id_unico
                LEFT JOIN   gf_tercero t                      ON e.tercero            = t.id_unico
                LEFT JOIN   gn_tipo_vinculacion tv            ON vr.tipovinculacion   = tv.id_unico
                LEFT JOIN   gn_estado_vinculacion_retiro evr  ON vr.estado            = evr.id_unico
                LEFT JOIN   gn_vinculacion_retiro tvr         ON vr.vinculacionretiro = tvr.id_unico
                LEFT JOIN   gn_causa_retiro cr                ON vr.causaretiro       = cr.id_unico
                where md5(vr.id_unico) = '$id'";

  $resultado = $mysqli->query($sql);
  $row = mysqli_fetch_row($resultado);    
    
        $vrid   = $row[0];
        $vrnact = $row[1];
        $vrfact = $row[2];
        $vrfec  = $row[3];
        $vremp  = $row[4];
        $empid  = $row[5];
        $empter = $row[6];
        $terid  = $row[7];
        $ternom = $row[8];
        $vrtip  = $row[9];
        $tvid   = $row[10];
        $tvnom  = $row[11];
        $vrest  = $row[12];
        $evrid  = $row[13];
        $evrnom = $row[14];
        $vrv    = $row[15];
        $tvrid  = $row[16];
        $tvrnum = $row[17];
        $vrcr   = $row[18];
        $crid   = $row[19];
        $crnom  = $row[20];
       
        
         
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>

<script>
var vrest= '<?php echo $vrest ; ?>';
function estado(vrest){

     if(vrest=="1" ){

            document.getElementById("sltTipo").disabled=false;
            document.getElementById("sltCausa").disabled=true;
            document.getElementById("sltVinculacion").disabled=true;

    }else{
            document.getElementById("sltCausa").disabled=false;
            document.getElementById("sltVinculacion").disabled=false;
            document.getElementById("sltTipo").disabled=true;
}
}
</script>
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
            dia = "0" + dria;
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
        $("#sltFecha").datepicker({changeMonth: true,}).val();
       
        
        
});
</script>
<title>Modificar Vinculación Retiro</title>
    </head>
    <body onload="javascript:estado(vrest);">
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Vinculación Retiro</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarVinculacionRetiroJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $vremp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $empid?>"><?php echo $ternom?></option>
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
<!------------------------- Campo Llenar Número Acto-->
                            <div class="form-group" style="margin-top: -10px;">
                                 <label for="numeroA" class="col-sm-5 control-label"><strong class="obligado"></strong>Número Acto:</label>
                                <input type="text" name="txtNumeroA" id="txtNumeroA" class="form-control" value="<?php echo $vrnact?>" maxlength="100" title="Ingrese el número de acto" onkeypress="return txtValida(event,'num_car')" placeholder="Número Acto">
                            </div>                              
<!------------------------- Fin Campo Llenar Número Acto-->
<!------------------------- Campo para seleccionar Fecha Acto-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="FechaA" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Acto:</label>
                <?php                                         
                    $vrfact = $row[2];
                     if(!empty($row[2])||$row[2]!=''){
                            $vrfact = trim($vrfact, '"');
                            $fecha_div = explode("-", $vrfact);
                            $anioi = $fecha_div[0];
                            $mesi  = $fecha_div[1];
                            $diai  = $fecha_div[2];
                            $vrfact   = $diai.'/'.$mesi.'/'.$anioi;
                      }else{
                            $vfi='';
                      }
                ?>
                            <input name="sltFechaA" id="sltFechaA" title="Ingrese Fecha Inicio" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"  value="<?php echo $vrfact;?>">
                <!--<input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaA" id="sltFechaA" step="1" value="<?php echo $vrfact?>">-->
           </div>
<!----------Fin Captura de Fecha Acto-->                              
<!------------------------- Campo para seleccionar Fecha-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="Fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Vinculació/Retiro:</label>
                <?php                                         
                    $vrfec = $row[3];
                     if(!empty($row[3])||$row[3]!=''){
                            $vrfec = trim($vrfec, '"');
                            $fecha_div = explode("-", $vrfec);
                            $anioi = $fecha_div[0];
                            $mesi  = $fecha_div[1];
                            $diai  = $fecha_div[2];
                            $vrfec   = $diai.'/'.$mesi.'/'.$anioi;
                      }else{
                            $vrfec='';
                      }
                ?>
                            <input name="sltFecha" id="sltFecha" title="Ingrese Fecha Inicio" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"  value="<?php echo $vrfec;?>">
                <!--<input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFecha" id="sltFecha" step="1" value="<?php echo $vrfec?>">-->
           </div>
<!----------Fin Captura de Fecha-->                              
<!------------------------- Consulta para llenar campo Tipo Vinculación-->
            <?php
            if(empty($tvid)){ 
            $tip = "SELECT id_unico, nombre FROM gn_tipo_vinculacion";
            }else{
             $tip = "SELECT id_unico, nombre FROM gn_tipo_vinculacion WHERE id_unico != $vrtip";   
            }
            $tipon = $mysqli->query($tip);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Tipo Vinculación:
                </label>
                <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione tipo vinculación" style="height: 30px" >
                <option value="<?php echo $tvid?>"><?php echo $tvnom?></option>
                
                    <?php 
                    while ($filaTV = mysqli_fetch_row($tipon)) { ?>                   
                    <option value="<?php echo $filaTV[0];?>"><?php echo $filaTV[1];?></option>
                    <?php
                    }
                    
                    ?>

                    <option value=null></option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Tipo Vinculación-->
<!------------------------- Consulta para llenar campo Estado-->
            <?php
              /*if(empty($evrid)){
                    $evi = "SELECT id_unico, nombre FROM gn_estado_vinculacion_retiro";
              }else{
                    $evi = "SELECT id_unico, nombre FROM gn_estado_vinculacion_retiro WHERE id_unico = $vrest";
              }  
            $evin = $mysqli->query($evi);*/
              
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Estado Vinculación:
                </label>
                <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione estado vinculación" style="height: 30px"  >
                <option value="<?php echo $evrid?>"><?php echo $evrnom?></option>
                
                    <?php 
                    while ($filaEV = mysqli_fetch_row($evin)) { ?>                   
                    <option value="<?php echo $filaEV[0];?>"><?php echo $filaEV[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=null></option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Estado-->

<!------------------------- Consulta para llenar campo Causa Retiro-->
            <?php
                if(empty($crid)){   
                    $cau = "SELECT id_unico, nombre FROM gn_causa_retiro";
                }else{
                    $cau = "SELECT id_unico, nombre FROM gn_causa_retiro WHERE id_unico != $vrcr";
                }    
                $caur = $mysqli->query($cau);
                
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Causa Retiro:
                </label>
                <select name="sltCausa" class="form-control" id="sltCausa" title="Seleccione causa retiro" style="height: 30px" >
                <option value="<?php echo $crid?>"><?php echo $crnom?></option>
                
                    <?php 
                    while ($filaCR = mysqli_fetch_row($caur)) { ?>                   
                    <option value="<?php echo $filaCR[0];?>"><?php echo $filaCR[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=null></option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Causa Retiro-->
<!------------------------- Consulta para llenar campo Vinculación Retiro-->
            <?php   
                    $vre ="SELECT vr.id_unico, e.id_unico, e.tercero, t.id_unico, 
                                                CONCAT_WS(' ',vr.id_unico, vr.numeroacto, t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos) 
                                                FROM gn_vinculacion_retiro vr 
                                                LEFT JOIN gn_empleado e ON e.id_unico = vr.empleado 
                                                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                                                WHERE vr.estado=1 AND vr.id_unico!= $vrv";
                 if(empty($vrv)){
                    $rvconemp='';
                 }else{


                     $sql2="SELECT vr.id_unico, e.id_unico, e.tercero, t.id_unico, 
                                                CONCAT_WS(' ',vr.id_unico, vr.numeroacto, t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos) 
                                                FROM gn_vinculacion_retiro vr 
                                                LEFT JOIN gn_empleado e ON e.id_unico = vr.empleado 
                                                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                                                WHERE  vr.id_unico= $vrv";   
                        $res = $mysqli->query($sql2);
                        $row1 = mysqli_fetch_row($res);    
    
                         $rvid      = $row1[0];
                         $rvemid    = $row1[1];
                         $rvter     = $row1[2];
                         $rvterid   = $row1[3];
                         $rvconemp  = $row1[4];
                }                       
                    #"SELECT id_unico, numeroacto FROM gn_vinculacion_retiro WHERE id_unico != $row[0] AND empleado = $row[4] ORDER BY id_unico ASC";
            
                    $vret = $mysqli->query($vre);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>ID Vinculación:
                </label>
                <select name="sltVinculacion" class="form-control" id="sltVinculacion" title="Seleccione vinculación retiro" style="height: 30px"  >
                    <option value="<?php echo $tvrid?>"><?php echo $rvconemp ?></option>
                    
                    <?php 
                    while ($filaVR = mysqli_fetch_row($vret)) { ?>                   
                    <option value="<?php echo $filaVR[0];?>"><?php echo $filaVR[4];?></option>
                    <?php
                    }
                    ?>
                    <option value=null></option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Vinculación Retiro-->
                                                           
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
