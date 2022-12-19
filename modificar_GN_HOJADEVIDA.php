<?php 
#28/09/2021 --- Elkin O --- Se creo el formulario modificar

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');

$id = (($_GET["id"]));

  $sql = "SELECT td.nombre,
                                       ed.fechaactualizacion,
                                       ed.numerofolio,
                                       ed.ruta,
                                       e.id_unico,
                                Concat(t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno, ' ', t.apellidodos),
                                td.id_unico,
                                ed.id_unico
                                FROM   gn_empleado_documento ed
                                       LEFT JOIN gf_tipo_documento td
                                              ON td.id_unico = ed.tipodocumento
                                       LEFT JOIN gn_empleado e
                                              ON e.id_unico = ed.empleado
                                       LEFT JOIN gf_tercero t
                                              ON e.tercero = t.id_unico
                                WHERE  ed.id_unico= '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
    

                                         $nomt   =$row[0];
                                         $fechaA =$row[1];
                                         $numF   =$row[2];
                                         $ruta   =$row[3];
                                         $idEm   =$row[4];
                                         $nomE   =$row[5];
                                         $idTip   =$row[6];
                                         $idED   =$row[7];
                       
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
               
        $("#sltFechaAc").datepicker({changeMonth: true,}).val();

        
});
</script>
<title>Modificar Hoja De Vida</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Hoja De Vida</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarHojadevidaJson.php">
                              <input type="hidden" name="Ide" value="<?php echo $idED?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
<!------------------------- Consulta para llenar campo Empleado-->
                        <?php 
                        $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico ";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $idEm?>"><?php echo $nomE?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!----------Campo para llenar Tipo Documento-->
                        <?php 
                        
                         $doc = "SELECT id_unico, nombre FROM gf_tipo_documento ORDER BY id_unico ASC";
                         $doctp = $mysqli->query($doc);
                    
                        ?>
                <div class="form-group" style="margin-top: -10px;">
                     <label for="txtDocumento" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo. Documento :</label>
                     <select name="sltDocumento" class="form-control" id="sltDocumento" title="Seleccione tipo de documento" style="height: 30px" required="">
                            <option value="<?php echo $idTip?>"><?php echo $nomt?></option>
                                <?php 
                                while ($filaTip = mysqli_fetch_row($doctp)) { ?>
                                <option value="<?php echo $filaTip[0];?>"><?php echo ucwords(($filaTip[1])); ?></option>
                                <?php
                                }
                                ?>
                            </select>  
                    </div>                                    
<!----------Fin Campo Tipo Doc-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha Actualizacion-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="FechaAc" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Terminación:</label>
                 <?php                                         
                    $fechaA =$row[1];
                     if(!empty($row[1])||$row[1]!=''){
                            $fechaA = trim($fechaA, '"');
                            $fecha_div = explode("-", $fechaA);
                            $anioi = $fecha_div[0];
                            $mesi  = $fecha_div[1];
                            $diai  = $fecha_div[2];
                            $fechaA   = $diai.'/'.$mesi.'/'.$anioi;
                      }else{
                            $fechaA='';
                      }
                ?>
                 <input name="sltFechaAc" id="sltFechaAc" title="Ingrese Fecha Actualizacion" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"   value="<?php echo $fechaA;?>">
               </div>
<!----------Fin Captura de Fecha Actualizacion-->                                                            
<!----------Campo para llenar No. Folio-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="sltFolio" class="col-sm-5 control-label"><strong class="obligado"></strong>Número Folio :</label>
                     <input type="text" name="txtFolio" id="txtFolio"  value="<?php echo $numF?>"class="form-control" maxlength="100" title="Ingrese el número de folio"  placeholder="Número de folio" required>
                </div>                                    
<!----------Fin Campo No. Folio-->

<!----------Campo para llenar archivo-->
 
                        <div class="form-group" style="margin-top: -5px">
                        
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Archivo :
                            </label>
                            <input  value="<?php echo $ruta;?>" required="required" style="width:18%; height: 40px"   class="form-control" maxlength="100" type="hidden" title="Seleccione Documento" id="archivos" name="archivos" required>
                            
                           <input  class="col-sm-2 col-md-2 col-lg-0 input-sm" id="file" name="file" type="file" style="height: 30px; " value="<?php echo $ruta;?>" >
                
                        </div>
                        <div class="form-group" style="margin-top: -60px">
                        
                            <label class="control-label col-sm-8">
                            Actual: <?php echo $ruta;?>
                            </label>
                            
                        </div>
<!----------Fin Campo para llenar archivo-->

                                                           
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
