<?php

#01/03/2017 --- Nestor B --- se modifico el botón atrás y se enlazó con la página "modificar_GN_EMPLEADO.php" y se modificó la función strtolower por mb_strtolower
#01/03/2017 --- Nestor B --- se modificó el método que cambia el formato de fechas
#02/03/2017 --- Nestor B --- se insertó la funcion fechaInicial y fechaFinal para que al momento de selecionar una fecha en el calendario de la primera fecha no me deje selecionar una fecha menor  
#03/03/2017 --- Nestor B --- se modificó el ancho del formulario de listar par aque concuerde con el de registrar 
#04/03/2017 --- Nestor B --- Se agregaron librerías para la busqueda rápida en los selects tipo de empleado y estado del empleado 
#10/03/2017 --- Nestor B --- se modificaron el alto del botón atras, del titulo de informacipón adicional para que cuadre con el subtitulo y el titulo respectivamente y del botón guardar

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
        $("#sltFechaC").datepicker({changeMonth: true}).val();
        
        
});
</script>


   <title>Registrar Empleado Tipo</title>
   <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-8 text-left" style="margin-top: 0px">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Empleado Tipo</h2>
                      <a href="<?php echo 'modificar_GN_EMPLEADO.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                      <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                      <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 10px">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarEmpleadoTipoJson.php">
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
                            <label for="sltTipo" class="col-sm-2 control-label">
                                <strong class="obligado"></strong>Tipo Empleado:
                            </label>                                
                            <select name="sltTipo" id="sltTipo" title="Seleccione Tipo Empleado" style="width: 140px;height: 30px" class="form-control col-sm-1">
                            	                             	
                                    <?php 
                                        $tip = "SELECT id_unico, nombre FROM gn_tipo_empleado";
                                        $tipe = $mysqli->query($tip);                                
                            		echo "<option value=''>Tipo</option>";                            		
                            		while($rowTE = mysqli_fetch_row($tipe)){
                            			echo "<option value=".$rowTE[0].">".ucwords(strtolower($rowTE[1]))."</option>";
                            		}                            	
                            	?>                            	                           	
                            </select>
                            <label for="sltTipo" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Estado Empleado:
                            </label>
                            <?php 
                                $est = "SELECT id_unico, nombre FROM gn_estado_empleado";
                                $estado = $mysqli->query($est);
                            ?>
                            <select name="sltEstado" id="sltEstado" title="Seleccione Estado" style="width: 140px;height: 30px" class="form-control col-sm-1">
                            <option value="">Estado</option>
                            <?php 
                                while($rowE = mysqli_fetch_row($estado))
                                {
                                    echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
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
                            	<strong class="obligado"></strong>Fecha Inicio:
                            </label>
                            <input style="width:140px" class="col-sm-2 input-sm" type="text" name="sltFechaI" id="sltFechaI" step="1"   onchange="javaScript:fechaInicial();" title="Ingrese la fecha de Inicio" placeholder="Ingrese la fecha">                            
                            <label for="sltFechaM" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Fecha Cancelación:
                            </label>
                            <input style="width:140px" class="col-sm-2 input-sm" type="text" name="sltFechaC" id="sltFechaC" step="1"  onchange="javaScript:fechaFinal();"   disabled ="true" placeholder="Ingrese la fecha" title="Ingrese la fecha de Cancelación">                            
                            <label for="txtObservaciones" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Observaciones:
                            </label>
                            <input style="width:140px" class="col-sm-2 input-sm" type="text" name="txtObservaciones" id="txtObservaciones" placeholder="Observaciones" onkeypress="return txtValida(event,'car')">
                            <div class="form-group form-inline" style="margin-top:-10px; margin-left: 20px">
                            <label for="No" class="col-sm-10 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 0px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                        </div>     
                        </div>                     
<!---------------------------------------------------------------------------------------------------->                              
                        
                  </div>
                
<!---------------------------------------------------------------------------------------------------->                        
    <div class="form-group form-inline" style="margin-top:-10px; "> -
                <?php require_once './menu.php'; 
                $sql = "SELECT          et.id_unico,
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
                                WHERE et.empleado = $idTer";
                    $resultado = $mysqli->query($sql);
                ?>
               <div class="col-sm-12 text-left" style="margin-top:5px"> 
                    <div class="table-responsive" style="">
                        <div class="table-responsive" style="">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>     
                                        <!-- Actualización 24 / 02 09:47: No es necesario mostrar el nombre del empleado
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        -->
                                        <td class="cabeza"><strong>Estado</strong></td>
                                        <td class="cabeza"><strong>Tipo</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>Fecha Cancelación</strong></td>
                                        <td class="cabeza"><strong>Observaciones</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <!-- Actualización 24 / 02 09:47: No es necesario mostrar el nombre del empleado
                                        <th class="cabeza">Empleado</th>
                                        -->
                                        <th class="cabeza">Estado</th>
                                        <th class="cabeza">Tipo</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">Fecha Cancelación</th>
                                        <th class="cabeza">Observaciones</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                          $etfeci = $row[6];
                                          if(!empty($row[6])||$row[6]!=''){
                                            $etfeci = trim($etfeci, '"');
                                            $fecha_div = explode("-", $etfeci);
                                            $anioi = $fecha_div[0];
                                            $mesi = $fecha_div[1];
                                            $diai = $fecha_div[2];
                                            $etfeci = $diai.'/'.$mesi.'/'.$anioi;
                                        }else{

                                            $etfeci='';
                                        }
                                        
                                            $etfecc = $row[7];
                                            if(!empty($row[7])||$row[7]!=''){
                                            $etfecc = trim($etfecc, '"');
                                            $fecha_div = explode("-", $etfecc);
                                            $anioc = $fecha_div[0];
                                            $mesc = $fecha_div[1];
                                            $diac = $fecha_div[2];
                                            $etfecc = $diac.'/'.$mesc.'/'.$anioc;
                                        }else{

                                            $etfecc='';
                                        }
                                        
                                        
                                            $etid   = $row[0];
                                            $etemp  = $row[1];
                                            $eid    = $row[2];
                                            $eter   = $row[3];
                                            $terid  = $row[4];
                                            $ternom = $row[5];
                                            #$etfeci = $row[6];
                                            #$etfecc = $row[7];
                                            $ettip  = $row[8];
                                            $teid   = $row[9];
                                            $tenom  = $row[10];
                                            $etest  = $row[11];
                                            $eeid   = $row[12];
                                            $eenom  = $row[13];
                                            $etobs  = $row[14];
                                        

                                        
                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_EMPLEADO_TIPO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <!-- Actualización 24 / 02 09:48: No es necesario mostrar el nombre del empleado
                                        <td class="campos"><?php #echo $ternom?></td>                
                                        -->
                                        <td class="campos"><?php echo $eenom?></td>                
                                        <td class="campos"><?php echo $tenom?></td>                
                                        <td class="campos"><?php echo $etfeci?></td>                
                                        <td class="campos"><?php echo $etfecc?></td>                
                                        <td class="campos"><?php echo $etobs?></td>            
                                    </tr>
                                    <?php }

                                    
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>    
                
            </div>
            <div class="col-sm-8 col-sm-2" style="margin-top:-23px">
                <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                                <tr>                                        
                                    <th>
                                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                    </th>
                                </tr>
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
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_ESTADO_EMPLEADO.php">ESTADO</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_EMPLEADO.php">TIPO</a>
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
          <p>¿Desea eliminar el registro seleccionado de Empleado Tipo?</p>
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
                  url:"json/eliminarEmpleadoTipoJson.php?id="+id,
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

<script src="js/select/select2.full.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
  <?php require_once 'footer.php';?>
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
<script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
         $("#sltTipo").select2();
        </script>
         </script>

          <script type="text/javascript"> 
         $("#sltEstado").select2();
        </script>
         </script>

</body>

</html>
