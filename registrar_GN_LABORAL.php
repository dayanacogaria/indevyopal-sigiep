<?php
#02/03/2017 --- Nestor B --- Se modificó la ruta del botón "atrás" y la función para que renozca las tildes
#03/03/2017 --- Nestor B --- se agregó la función fecha Inicial para que no permita que la fecha de modificación sea mayor que la fehca de cancelación, se modificó el método para cambiar el fomrato de fecha y se ajusto el ancho del formulario listar con el de registrar 
#10/03/2017 --- Nestor B --- se modificó el alto del botón guardar para que cuadre con el sutittulo 

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
@$id = $_GET['idE'];
$emp = "SELECT e.id_unico, e.tercero, CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
FROM gn_empleado e
LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE md5(e.id_unico) = '$id'";
$bus = $mysqli->query($emp);
$busq = mysqli_fetch_row($bus);
$idT = $busq[0];
$datosTercero= $busq[2].' ('.$busq[5].')';
$a = "none";
if(empty($idT))
{
    $tercero = "Empleado";    
}
else
{
    $tercero = $datosTercero;
    $a="inline-block";
}
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
        $("#sltFechaR").datepicker({changeMonth: true,}).val();
        
        
});
</script>
   <title>Registrar Laboral</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-8 text-left" style="margin-top: 0px">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Laboral</h2>
                      <a href="<?php echo 'modificar_GN_EMPLEADO.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                      <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                      <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 10px">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarLaboralJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
<!----------------------------------------------------------------------------------------------------------------------->
                      <div class="form-group form-inline" style="margin-top:-25px">
                          <?php 
                       if(empty($idT))
                        {
                         $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
							                            t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico";
                            $idTer = "";
                        }
                        else
                        {
                        $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
							                            t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico = 0";
                        $idTer = $idT;
                        }
                        $empleado = $mysqli->query($emp);
                        ?>
                            <label for="sltEmpleado" class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 140px;height: 30px" class="form-control col-sm-1">
                            	<option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                            	<?php 
                            		while($rowE = mysqli_fetch_row($empleado))
                                    {
                            			echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                            		}
                            	?>                            	                           	
                            </select>
                          <!----------------------------------------------------------------------->
                            <?php 
                                $en = "SELECT 	t.id_unico,
                                                t.razonsocial
                                        FROM 	gf_perfil_tercero pt
                                        LEFT JOIN gf_tercero t ON pt.tercero = t.id_unico
                                        WHERE pt.perfil = '11'";
                                $ent = $mysqli->query($en);
                            ?>
                            <label for="sltEntidad" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Entidad:
                            </label>
                            <select name="sltEntidad" id="sltEntidad" title="Seleccione Entidad" style="width: 140px;height: 30px" class="form-control col-sm-1">
                            	<option value="">Entidad</option>
                            	<?php 
                            		while($rowE = mysqli_fetch_row($ent))
                                    {
                            			echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                            		}
                            	?>                            	                           	
                            </select>                          
                            <?php 
                                $tip = "SELECT id_unico, nombre FROM gn_tipo_dedicacion";
                                $tipod = $mysqli->query($tip);
                            ?>
                            <label for="sltTipo" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Tipo Dedicación:
                            </label>
                            <select name="sltTipo" id="sltTipo" title="Seleccione Tipo Dedicación" style="width: 140px;height: 30px" class="form-control col-sm-1">
                            	<option value="">Tipo</option>
                            	<?php 
                            		while($rowT = mysqli_fetch_row($tipod))
                                    {
                            			echo "<option value=".$rowT[0].">".$rowT[1]."</option>";
                            		}
                            	?>                            	                           	
                            </select>
                        </div>
<!----------------------------------------------------------------------------------------------------->                              
                        <div class="form-group form-inline" style="margin-top:-15px">
                            <?php 
                                $dep = "SELECT id_unico, nombre FROM gn_dependencia_empleado";
                                $depen = $mysqli->query($dep);
                            ?>
                            <label for="sltDependencia" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Dependencia Empleado:
                            </label>
                            <select name="sltDependencia" id="sltDependencia" title="Seleccione Dependencia Empleado" style="width: 140px;height: 30px" class="form-control col-sm-1">
                            	<option value="">Dependencia</option>
                            	<?php 
                            		while($rowD = mysqli_fetch_row($depen))
                                    {
                            			echo "<option value=".$rowD[0].">".$rowD[1]."</option>";
                            		}
                            	?>                            	                           	
                            </select>
                            <?php 
                                $car = "SELECT id_unico, nombre FROM gf_cargo";
                                $carg = $mysqli->query($car);
                            ?>
                            <label for="sltCargo" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Cargo:
                            </label>
                            <select name="sltCargo" id="sltCargo" title="Seleccione Cargo" style="width: 140px;height: 30px" class="form-control col-sm-1">
                            	<option value="">Cargo</option>
                            	<?php 
                            		while($rowC = mysqli_fetch_row($carg))
                                    {
                            			echo "<option value=".$rowC[0].">".$rowC[1]."</option>";
                            		}
                            	?>                            	                           	
                            </select>
                            <?php 
                                $cau = "SELECT id_unico, nombre FROM gn_causa_retiro";
                                $caur = $mysqli->query($cau);
                            ?>
                            <label for="sltCausaR" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Causa Retiro:
                            </label>
                            <select name="sltCausaR" id="sltCausaR" title="Seleccione Causa Retiro" style="width: 140px;height: 30px" class="form-control col-sm-1">
                            	<option value="">Causa Retiro</option>
                            	<?php 
                            		while($rowCR = mysqli_fetch_row($caur))
                                    {
                            			echo "<option value=".$rowCR[0].">".$rowCR[1]."</option>";
                            		}
                            	?>                            	                           	
                            </select>
                        </div>      
<!----------------------------------------------------------------------------------------------------->                              
                        <div class="form-group form-inline" style="margin-top:-15px">                            
                            <!----------Script para invocar Date Picker-->
                            <script type="text/javascript">
                            $(document).ready(function() {
                               $("#datepicker").datepicker();
                            });
                            </script>
                            <label for="sltFechaI" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Fecha Inicial:
                            </label>
                            <input name="sltFechaI" id="sltFechaI" title="Ingrese Fecha Inicial" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" placeholder="Ingrese la fecha" onchange="javascript:fechaInicial();">  
                            <label for="sltFechaR" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Fecha Retiro:
                            </label>
                            <input name="sltFechaR" id="sltFechaR" title="Ingrese Fecha Retiro" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" placeholder="Ingrese la fecha" disabled="true">  
                            <label for="No" class="col-sm-2 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 0px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                        </div>      
                  </div>
                
<!---------------------------------------------------------------------------------------------------->                        
    <div class="form-group form-inline" style="margin-top:5px; display:<?php echo $a?>">
                <?php require_once './menu.php'; 
                $sql = "SELECT          l.id_unico,
                                        l.fechaingreso,
                                        l.fecharetiro,
                                        l.empleado,
                                        e.id_unico,
                                        e.tercero,
                                        t.id_unico,
                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                        l.entidad,
                                        en.id_unico,
                                        en.nombre,
                                        l.dependencia,
                                        de.id_unico,
                                        de.nombre,
                                        l.cargo,
                                        ca.id_unico,
                                        ca.nombre,
                                        l.causaretiro,
                                        cr.id_unico,
                                        cr.nombre,
                                        l.tipodedicacion,
                                        td.id_unico,
                                        td.nombre
                                FROM gn_laboral l	 
                                LEFT JOIN	gn_empleado e           ON l.empleado       = e.id_unico
                                LEFT JOIN   gf_tercero t            ON e.tercero        = t.id_unico
                                LEFT JOIN   gn_entidad en           ON l.entidad        = en.id_unico
                                LEFT JOIN   gn_dependencia_empleado de ON l.dependencia = de.id_unico
                                LEFT JOIN   gf_cargo ca             ON l.cargo          = ca.id_unico
                                LEFT JOIN   gn_causa_retiro cr      ON l.causaretiro    = cr.id_unico
                                LEFT JOIN   gn_tipo_dedicacion td   ON l.tipodedicacion = td.id_unico
                                WHERE l.empleado = $idTer";
                    $resultado = $mysqli->query($sql);
                ?>
             <!--   <div class="col-sm-12 text-left" style="display:<?php echo $a?>"> -->
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:0px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <!-- Actualización 24 / 02 10:43 No es necesario mostrar el nombre del empleado
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        -->
                                        <td class="cabeza"><strong>Entidad</strong></td>
                                        <td class="cabeza"><strong>Fecha Ingreso</strong></td>
                                        <td class="cabeza"><strong>Fecha Retiro</strong></td>
                                        <td class="cabeza"><strong>Dependencia Empleado</strong></td>
                                        <td class="cabeza"><strong>Tipo Dedicación</strong></td>
                                        <td class="cabeza"><strong>Cargo</strong></td>
                                        <td class="cabeza"><strong>Causa Retiro</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <!-- Actualización 24 / 02 10:43 No es necesario mostrar el nombre del empleado
                                        <th class="cabeza">Empleado</th>
                                        -->
                                        <th class="cabeza">Entidad</th>
                                        <th class="cabeza">Fecha Ingreso</th>
                                        <th class="cabeza">Fecha Retiro</th>
                                        <th class="cabeza">Dependencia Empleado</th>
                                        <th class="cabeza">Tipo Dedicación</th>
                                        <th class="cabeza">Cargo</th>
                                        <th class="cabeza">Causa Retiro</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) {                                         
                                            $lfi = $row[1];
                                            if(!empty($row[1])||$row[1]!=''){
                                            $lfi = trim($lfi, '"');
                                            $fecha_div = explode("-", $lfi);
                                            $anioi = $fecha_div[0];
                                            $mesi = $fecha_div[1];
                                            $diai = $fecha_div[2];
                                            $lfi = $diai.'/'.$mesi.'/'.$anioi;
                                        }else{
                                            $lfi='';
                                        }
                                        
                                            $lfr = $row[2];
                                            if(!empty($row[2])||$row[2]!=''){
                                            $lfr = trim($lfr, '"');
                                            $fecha_div = explode("-", $lfr);
                                            $anior = $fecha_div[0];
                                            $mesr = $fecha_div[1];
                                            $diar = $fecha_div[2];
                                            $lfr = $diar.'/'.$mesr.'/'.$anior;
                                        }else{
                                            $lfr='';
                                        }
                                        
                                            $lid    = $row[0];
                                            #$lfi    = $row[1];
                                            #$lfr    = $row[2];
                                            $lemp   = $row[3];
                                            $empid  = $row[4];
                                            $empter = $row[5];
                                            $terid  = $row[6];
                                            $ternom = $row[7];
                                            $len    = $row[8];
                                            $enid   = $row[9];
                                            $enom   = $row[10];
                                            $ldep   = $row[11];
                                            $deid   = $row[12];
                                            $denom  = $row[13];
                                            $lca    = $row[14];
                                            $caid   = $row[15];
                                            $canom  = $row[16];
                                            $lcr    = $row[17];
                                            $crid   = $row[18];
                                            $crnom  = $row[19];       
                                            $ltd    = $row[20];
                                            $tdid   = $row[21];
                                            $tdnom  = $row[22];
                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_LABORAL.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <!-- Actualización 24 / 02 10:47 No es necesario mostrar el nombre del empleado
                                        <td class="campos"><?php #echo $ternom?></td>                
                                        -->
                                        <td class="campos"><?php echo $enom?></td>                
                                        <td class="campos"><?php echo $lfi?></td>                
                                        <td class="campos"><?php echo $lfr?></td>                
                                        <td class="campos"><?php echo $denom?></td>                
                                        <td class="campos"><?php echo $tdnom?></td>                
                                        <td class="campos"><?php echo $canom?></td>                
                                        <td class="campos"><?php echo $crnom?></td> 
                                    <?php }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                <!-- </div> -->
            </div>
            <div class="col-sm-8 col-sm-2" style="margin-top:-22px">
                <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                                <tr>                                        
                                    <th>
                                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                    </th>
                                </tr>
                        </thead>
                        <tbody>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_EMPLEADO.php">EMPLEADO</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GF_CARGO.php">CARGO</a>
                                </td>
                            </tr>                            
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_CAUSA_RETIRO.php">CAUSA RETIRO</a>
                                </td>
                            </tr>                            
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_DEPENDENCIA_EMPLEADO.php">DEPENDENCIA</a>
                                </td>
                            </tr>                            
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GF_TERCERO_ENTIDAD_AFILIACION.php">ENTIDAD</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_DEDICACION.php">TIPO DEDICACIÓN</a>
                                </td>
                            </tr>
                </table>
          </div>
      </div>                                    
    </div>
   <div>
<?php require_once './footer.php'; ?>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Laboral?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal1" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" onclick="recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


  <!--Script que dan estilo al formulario-->

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarLaboralJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true)
                      $("#myModal1").modal('show');
                 else
                      $("#myModal2").modal('show');
                  }
              });
          });
      }
  </script>

  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
 <script type="text/javascript">
      function recargar()
      {
        window.location.reload();     
      }
  </script>     
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){ 
         reload();
        //window.location= '../registrar_GN_ACCIDENTE.php?idE=<?php #echo md5($_POST['sltEmpleado'])?>';
        //window.location='../listar_GN_ACCIDENTE.php';
        window.history.go(-1);        
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        window.history.go(-1);
      });    
  </script>
</div>
<script>
function fechaInicial(){
        var fechain= document.getElementById('sltFechaI').value;
        var fechafi= document.getElementById('sltFechaR').value;
          var fi = document.getElementById("sltFechaR");
        fi.disabled=false;
      
       
            $( "#sltFechaR" ).datepicker( "destroy" );
            $( "#sltFechaR" ).datepicker({ changeMonth: true, minDate: fechain});
        

           
           
}
</script>
</body>
</html>
