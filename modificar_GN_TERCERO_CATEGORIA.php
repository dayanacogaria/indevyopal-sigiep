<?php 
#16/06/2017 --- Nestor B --- se agrego la función del datepicker y la función fecha inicial ademas se modificó la forma como se llama los calendarios
#17/03/2017 --- Nestor B --- se modifcó la consulta que trae la categoría 

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT        tc.id_unico,
                        tc.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        tc.fechamodificacion,
                        tc.fechacancelacion,
                        tc.categoria,
                        c.id_unico,
                        c.nombre,
                        tc.estado,
                        etc.id_unico,
                        etc.nombre
                FROM gn_tercero_categoria tc	 
                LEFT JOIN	gn_empleado e                   ON tc.empleado = e.id_unico
                LEFT JOIN   gf_tercero t                    ON e.tercero = t.id_unico
                LEFT JOIN   gn_categoria c                  ON tc.categoria = c.id_unico
                LEFT JOIN   gn_estado_tercero_categoria etc ON tc.estado = etc.id_unico
                where md5(tc.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
        $tcid   = $row[0];
        $tcemp  = $row[1];
        $eid    = $row[2];
        $eter   = $row[3];
        $terid  = $row[4];
        $ternom = $row[5];
        $tcfecm = $row[6];
        $tcfecc = $row[7];
        $tccat  = $row[8];
        $cid    = $row[9];
        $cnom   = $row[10];
        $tcest  = $row[11];
        $etcid  = $row[12];
        $etcnom = $row[13];
         
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
               
        $("#sltFechaM").datepicker({changeMonth: true,}).val();
        $("#sltFechaC").datepicker({changeMonth: true,}).val();
        
});
</script>
<title>Modificar Tercero Categoría</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tercero Categoría</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTerceroCategoriaJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $tcemp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select required="required" name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" >
                            <option value="<?php echo $tcemp?>"><?php echo $ternom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!------------------------- Consulta para llenar campo Categoria-->
            <?php
            if(empty($tccat) || $tccat==""){
                $cat = "SELECT id_unico, nombre FROM gn_categoria WHERE id_unico";
            } else{
                 $cat = "SELECT id_unico, nombre FROM gn_categoria WHERE id_unico != $tccat";
            }
            $categor = $mysqli->query($cat);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Categoria:
                </label>
                <select required="required" name="sltCategoria" class="form-control" id="sltCategoria" title="Seleccione categoría" style="height: 30px">
                <option value ="<?php echo $cid?>"><?php echo $cnom?></option>
                
                    <?php 
                    while ($filaC = mysqli_fetch_row($categor)) { ?>                   
                    <option value="<?php echo $filaC[0];?>"><?php echo $filaC[1];?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Categoría-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha Modificación-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Modificación:</label>
                <?php                                         
                    $tcfecm = $row[6];
                     if(!empty($row[6])||$row[6]!=''){
                            $tcfecm = trim($tcfecm, '"');
                            $fecha_div = explode("-", $tcfecm);
                            $anioi = $fecha_div[0];
                            $mesi  = $fecha_div[1];
                            $diai  = $fecha_div[2];
                            $tcfecm = $diai.'/'.$mesi.'/'.$anioi;
                      }else{
                            $tcfecm='';
                      }
                ?>
                 <input name="sltFechaM" id="sltFechaM" title="Ingrese Fecha Inicial" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"   value="<?php echo $tcfecm;?>">
               <!-- <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaM" id="sltFechaM" step="1" value="<?php echo $tcfecm;?>"> -->
           </div>
<!----------Fin Captura de Fecha Modificación-->
<!------------------------- Campo para seleccionar Fecha Cancelación-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Cancelación:</label>
                <?php                                         
                    $tcfecc = $row[7];
                     if(!empty($row[7])||$row[7]!=''){
                            $tcfecc = trim($tcfecc, '"');
                            $fecha_div = explode("-", $tcfecc);
                            $anioi = $fecha_div[0];
                            $mesi  = $fecha_div[1];
                            $diai  = $fecha_div[2];
                            $tcfecc = $diai.'/'.$mesi.'/'.$anioi;
                      }else{
                            $tcfecc ='';
                      }
                ?>
                 <input name="sltFechaC" id="sltFechaC" title="Ingrese Fecha Inicial" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"   value="<?php echo $tcfecc;?>">
              <!--nput style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaC" id="sltFechaC" step="1" value="<?php echo $tcfecc;?>"> -->
           </div>
<!----------Fin Captura de Fecha Cancelación-->
<!------------------------- Consulta para llenar campo Estado Tercero Categoría-->
            <?php
            if(empty($tcest) || $tcest==""){
                $est = "SELECT id_unico, nombre FROM gn_estado_tercero_categoria";
            }
            else  {
                $est = "SELECT id_unico, nombre FROM gn_estado_tercero_categoria WHERE id_unico != $tcest";
            }
            $estado = $mysqli->query($est);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Estado Tercero:
                </label>
                <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione estado" style="height: 30px">
                <option value ="<?php echo $etcid?>"><?php echo $etcnom?></option>
                
                    <?php 
                    while ($filaETC = mysqli_fetch_row($estado)) { ?>                   
                    <option value="<?php echo $filaETC[0];?>"><?php echo $filaETC[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Estado Tercero Categoría-->
                                                           
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
