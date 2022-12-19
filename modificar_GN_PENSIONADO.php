<?php 
#14/03/2017 --- Nestor B --- se agregó la funcion del datepicker  fechaInicial y fechaDisfrute y la función del cambio de formato de fecha para que la muestre dd/mm/yyyy

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT        p.id_unico,
                        p.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        p.fechamodificacion,
                        p.observaciones,
                        p.tipopensionado,
                        tp.id_unico,
                        tp.nombre,
                        p.estado,
                        ep.id_unico,
                        ep.nombre
                FROM gn_pensionado p	 
                LEFT JOIN	gn_empleado e ON p.empleado = e.id_unico
                LEFT JOIN   gf_tercero t ON e.tercero = t.id_unico
                LEFT JOIN   gn_tipo_pensionado tp ON p.tipopensionado = tp.id_unico
                LEFT JOIN   gn_estado_pensionado ep ON p.estado = ep.id_unico
                where md5(p.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);        
        $pid    = $row[0];
        $pemp   = $row[1];
        $eid    = $row[2];
        $eter   = $row[3];
        $terid  = $row[4];
        $ternom = $row[5];
        $pfec   = $row[6];
        $pobs   = $row[7];
        $ptipo  = $row[8];
        $tpid   = $row[9];
        $tpnom  = $row[10];
        $pest   = $row[11];
        $epid   = $row[12];
        $epnom  = $row[13];         
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
               
        $("#sltFecha").datepicker({changeMonth: true,}).val();
       
        
        
});
</script>
<title>Modificar Pensionado</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Pensionado</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarPensionadoJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $pemp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $eid?>"><?php echo $ternom?></option>
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
<!------------------------- Campo para seleccionar Fecha-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Modificación:</label>
                <?php
                    $pfec = $row[6];
                    if(!empty($row[6])||$row[6]){
                           $pfec= trim($pfec, '"');
                           $fecha_div = explode("-", $pfec);
                           $anioa = $fecha_div[0];
                           $mesa = $fecha_div[1];
                           $diaa = $fecha_div[2];
                           $pfec = $diaa.'/'.$mesa.'/'.$anioa;
                    }else{

                           $pfec = '';
                    } 
                ?>
                <input name="sltFecha" id="sltFecha" title="Ingrese Fecha de Afiliación" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"  value="<?php echo $pfec;?>">
                <!--<input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFecha" id="sltFecha" step="1" value="<?php echo $pfec;?>">-->
           </div>
<!----------Fin Captura de Fecha-->                              
<!------------------------- Campo para llenar Observaciones-->
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="observaciones" class="col-sm-5 control-label"><strong class="obligado"></strong>Observaciones:</label>
                                <input type="text" name="txtObservaciones" id="txtObservaciones" class="form-control" maxlength="100" title="Ingrese las observaciones" value="<?php echo $pobs?>" placeholder="Observaciones">
                            </div>
<!----------Fin Campo para llenar Observaciones-->
<!------------------------- Consulta para llenar campo Tipo Pensionado-->
            <?php 
            if($ptipo=="")
                $pen = "SELECT id_unico, nombre FROM gn_tipo_pensionado";
            else
                $pen = "SELECT id_unico, nombre FROM gn_tipo_pensionado WHERE id_unico != $ptipo";
            $pension = $mysqli->query($pen);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Tipo Pensionado:
                </label>
                <select name="sltPensionado" class="form-control" id="sltPensionado" title="Seleccione pensionado" style="height: 30px">
                <option value ="<?php echo $tpid?>"><?php echo $tpnom?></option>
                    <?php 
                    while ($filaP = mysqli_fetch_row($pension)) { ?>                   
                    <option value="<?php echo $filaP[0];?>"><?php echo $filaP[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Tipo Pensionado-->
<!------------------------- Consulta para llenar campo Estado Pensionado-->
            <?php 
            if($pest=="")
                $est = "SELECT id_unico, nombre FROM gn_estado_pensionado";
            else
                $est = "SELECT id_unico, nombre FROM gn_estado_pensionado WHERE id_unico != $pest";
            $estado = $mysqli->query($est);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Estado Pensionado:
                </label>
                <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione estado" style="height: 30px">
                <option value ="<?php echo $epid?>"><?php echo $epnom?></option>
                    <?php 
                    while ($filaEP = mysqli_fetch_row($estado)) { ?>                   
                    <option value="<?php echo $filaEP[0];?>"><?php echo $filaEP[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Estado pensionado-->                                                           
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
