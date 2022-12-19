<?php 
#05/04/2017 --- Nestor B --- se agregó la funcion del datepicker par que cambie el aspecto de las fechas 

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT        a.id_unico,
                        a.lugaraccidente,
                        a.diagnostico,
                        a.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        a.tipoaccidente,
                        ta.id_unico,
                        ta.nombre,
                        a.estado,
                        ea.id_unico,
                        ea.nombre,
                        a.numradicado,
                        a.fechareporte,
                        a.descripcion,
                        a.rutareporte
                FROM gn_accidente a	 
                LEFT JOIN	gn_empleado e           ON a.empleado      = e.id_unico
                LEFT JOIN   gf_tercero t            ON e.tercero       = t.id_unico
                LEFT JOIN   gn_tipo_accidente ta    ON a.tipoaccidente = ta.id_unico
                LEFT JOIN   gn_estado_accidente ea  ON a.estado        = ea.id_unico
                where md5(a.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
        $aid    = $row[0];
        $alug   = $row[1];
        $adia   = $row[2];
        $aemp   = $row[3];
        $empid  = $row[4];
        $empter = $row[5];
        $terid  = $row[6];
        $ternom = $row[7];
        $atip   = $row[8];
        $taid   = $row[9];
        $tanom  = $row[10];
        $aest   = $row[11];
        $eatid  = $row[12];
        $eatnom = $row[13];
        $anumr  = $row[14];
        $afr    = $row[15];
        $ades   = $row[16];
        $arut   = $row[17];
         
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
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
       
        
        $("#sltFechaR").datepicker({changeMonth: true,}).val();
        
        
        
});
</script>
<title>Modificar Accidente</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Accidente</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarAccidenteJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $aemp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $aemp?>"><?php echo $ternom?></option>
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
<!------------------------- Campo para seleccionar Fecha Reporte-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Reporte:</label>
                <?php
                $afr = $row[15];
                if(!empty($row[15])||$row[15]!=''){
                    $afr = trim($afr, '"');
                    $fecha_div = explode("-", $afr);
                    $anior = $fecha_div[0];
                    $mesr = $fecha_div[1];
                    $diar = $fecha_div[2];
                    $afr = $diar.'/'.$mesr.'/'.$anior;
                }else{
                    $afr='';
                }
                ?>
                <input style="width:auto" class="col-sm-2 input-sm" type="text" name="sltFechaR" id="sltFechaR" step="1" value="<?php echo $afr;?>">
           </div>
<!----------Fin Captura de Fecha Reporte-->                              
<!------------------------- Consulta para llenar campo Tipo Accidente-->
<!------------------------- Campo para llenar Lugar Accidente-->
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="lugaraccidente" class="col-sm-5 control-label"><strong class="obligado"></strong>Lugar:</label>
                                <input type="text" name="txtLugar" id="txtLugar" value="<?php echo $alug;?>" class="form-control" maxlength="100" title="Ingrese el lugar del accidente" placeholder="Lugar Accidente">
                            </div>
<!----------Fin Campo para llenar Lugar Accidente-->                               
<!------------------------- Campo para llenar Diagnóstico-->
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="diagnostico" class="col-sm-5 control-label"><strong class="diagnostico"></strong>Diagnóstico:</label>
                                <input type="text" name="txtDiagnostico" id="txtDiagnostico" value="<?php echo $adia;?>" class="form-control" maxlength="100" title="Ingrese el diagnóstico" placeholder="Diagnóstico">
                            </div>
<!----------Fin Campo para llenar Diagnóstico-->                               
<!------------------------- Campo para llenar Número Radicado-->
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="numradicado" class="col-sm-5 control-label"><strong class="obligado"></strong>Número Radicado:</label>
                                <input type="text" name="txtNumeroR" id="txtNumeroR" value="<?php echo $anumr;?>" class="form-control" maxlength="100" title="Ingrese el número de radicado" placeholder="Número Radicado">
                            </div>
<!----------Fin Campo para llenar Número Radicado-->                               
<!------------------------- Campo para llenar Descripción-->
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="descripcion" class="col-sm-5 control-label"><strong class="obligado"></strong>Descripción:</label>
                                <input type="text" name="txtDescripcion" id="txtDescripcion"value="<?php echo $ades;?>" class="form-control" maxlength="100" title="Ingrese la descripción" placeholder="Descripción">
                            </div>
<!------------------------- Fin Campo para llenar Lugar Descripción-->
<!---------- Campo para llenar Ruta Reporte-->                               
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="rutareporte" class="col-sm-5 control-label"><strong class="obligado"></strong>Ruta Reporte:</label>
                                <input type="text" name="txtRuta" id="txtRuta" value="<?php echo $arut;?>" class="form-control" maxlength="100" title="Ingrese la ruta del reporte" placeholder="Ruta Reporte">
                            </div>
<!----------Fin Campo para llenar Ruta Reporte-->
<!------------------------- Consulta para llenar campo Tipo Accidente-->
            <?php
                              
            if(empty($atip))
                $acc = "SELECT id_unico, nombre FROM gn_tipo_accidente";
            else
                $acc = "SELECT id_unico, nombre FROM gn_tipo_accidente WHERE id_unico != $atip";
                              
            $tac = $mysqli->query($acc);
            ?>
            <div class="form-group" style="margin-top: -5px"><label class="control-label col-sm-5"><strong class="obligado"></strong>Tipo Accidente:
                </label>
                <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo" style="height: 30px">
                <option value="<?php echo $taid;?>"><?php echo $tanom;?></option>
                
                    <?php 
                    while ($filaT = mysqli_fetch_row($tac)) { ?>                   
                    <option value="<?php echo $filaT[0];?>"><?php echo $filaT[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Tipo Accidente-->
<!------------------------- Consulta para llenar campo Estado Accidente-->
            <?php 
            
            if(empty($aest))
            $eac = "SELECT id_unico, nombre FROM gn_estado_accidente";
            else
            $eac = "SELECT id_unico, nombre FROM gn_estado_accidente WHERE id_unico != $aest";
                              
            $esac = $mysqli->query($eac);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Estado:
                </label>
                <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                <option value="<?php echo $eatid;?>"><?php echo $eatnom;?></option>
                
                    <?php 
                    while ($filaE = mysqli_fetch_row($esac)) { ?>                   
                    <option value="<?php echo $filaE[0];?>"><?php echo $filaE[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Tipo Accidente-->                              
                                                           
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
