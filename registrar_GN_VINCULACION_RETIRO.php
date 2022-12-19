<?php
#01/03/2017 --- Nestor B --- se modificó el botón "atrás" y la funcion strtolower por mb_strtolower par que tome las tildes
#03/03/2017 --- Nestor B --- se modificó el ancho de listar para que concuerde con el de registrar y se agregó la función fecha Inicial para que no permita que la fecha de modificación sea mayo que la fehca de cancelación
#10/03/2017 --- Nestor B --- se modificó el alto del botón atrás par que cuadre con el subtitulo
#15/03/2017 --- Nestor B --- se modificaron el orden de tipo vinculación por estado vinculación/retiro y la consulta que trae la vinculación y se agregó la función estado que me habilita los selects dependiendo de que seleccione
#16/03/2017 --- Nestor B --- se agregó el atributo required  al select de estado

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
@$id = $_GET['idE'];
$emp = "SELECT e.id_unico, e.tercero, CONCAT_WS(' ',t.nombreuno,t.nombredos, t.apellidouno,t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
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
               
        $("#sltFechaA").datepicker({changeMonth: true,}).val();
        $("#sltFecha").datepicker({changeMonth: true,}).val();
        
        
});
</script>

<script>
function estado(value){

     if(value=="1" ){

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
   <title>Registrar Vinculación Retiro</title>
   <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-8 text-left" style="margin-top: 0px">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Vinculación Retiro</h2>
                      <a href="<?php echo 'modificar_GN_EMPLEADO.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                      <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                      <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 10px">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarVinculacionRetiroJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
<!-------------------------------------------------------------------------------------------------------------------- -->
                      <div class="form-group form-inline" style="margin-top:-25px">
                          <?php 
                       if(empty($idT))
                        {
                         $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
							                            t.id_unico,
                                                        CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos)
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
                                                        CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos)
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
                          <!-- ----------------------------------------------------------------------  -->
                            <?php  
                                $tip = "SELECT id_unico, nombre FROM gn_estado_vinculacion_retiro";
                                $tipon = $mysqli->query($tip);
                            ?> 
                            <label for="sltEstado" class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Estado Vinculación/Retiro:
                            </label>
                            <select   name="sltEstado" id="sltEstado" title="Seleccione Estado Vinculación/Retiro" style="width: 140px;height: 30px" class="form-control col-sm-1" onchange="javascript:estado(this.value);"  required="required">
                                <option value="">Estado Vinculación</option>
                                <?php 
                                    while($rowEV = mysqli_fetch_row($tipon))
                                    {
                                        echo "<option value=".$rowEV[0].">".$rowEV[1]."</option>";
                                    }

                                ?>
                            </select> 
                            <?php  
                                $tip = "SELECT id_unico, nombre FROM gn_tipo_vinculacion";
                                $tipon = $mysqli->query($tip);
                            ?> 
                            <label for="sltTipo" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Tipo Vinculación:
                            </label>
                            <select name="sltTipo" id="sltTipo" title="Seleccione Tipo" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"  disabled="true">
                            	<option value="">Tipo Vinculación</option>
                            	<?php 
                            		while($rowT = mysqli_fetch_row($tipon))
                                    {
                            			echo "<option value=".$rowT[0].">".$rowT[1]."</option>";
                            		}
                            	?>                            	                           	
                            </select>                          
                                                                             
                        </div>
<!--------------------------------------------------------------------------------------------------- -->                              
                        <div class="form-group form-inline" style="margin-top:-15px">                                                        
                            <!----------Script para invocar Date Picker-->
                            <label for="txtNumeroA" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Número Acto:
                            </label>
                            <input  name="txtNumeroA" id="txtNumeroA" title="Ingrese Número Acto" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" placeholder="Número Acto">
                            <script type="text/javascript">
                            $(document).ready(function() {
                               $("#datepicker").datepicker();
                            });
                            </script>
                            <label for="sltFechaA" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Fecha Acto:
                            </label>
                            <input name="sltFechaA" id="sltFechaA" title="Ingrese Fecha Acto" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"  onchange="javaScript:fechaInicial();" placeholder="Ingrese la fecha">  
                            <label for="sltFecha" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Fecha Vinculacion/Retiro:
                            </label>
                            <input name="sltFecha" id="sltFecha" title="Ingrese Fecha" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" placeholder="Ingrese la fecha" disabled="true">  
                        </div>
                        <div class="form-group form-inline" style="margin-top:-15px">                            
                            <?php  
                                $cr = "SELECT id_unico, nombre FROM gn_causa_retiro";
                                $cre = $mysqli->query($cr);
                            ?> 
                            <label for="sltCausa" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Causa Retiro:
                            </label>
                            <select name="sltCausa" id="sltCausa" title="Seleccione Causa Retiro" style="width: 140px;height: 30px" class="form-control col-sm-1" disabled="true">
                            	<option value="">Causa Retiro</option>
                            	<?php 
                            		while($rowCR = mysqli_fetch_row($cre))
                                    {
                            			echo "<option value=".$rowCR[0].">".$rowCR[1]."</option>";
                            		}
                            	?>                            	                           	
                            </select>
                            <?php  
                                $vre = "SELECT vr.id_unico, e.id_unico, e.tercero, t.id_unico, 
                                                CONCAT_WS(' ',vr.id_unico, vr.numeroacto, t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos) 
                                                FROM gn_vinculacion_retiro vr 
                                                LEFT JOIN gn_empleado e ON e.id_unico = vr.empleado 
                                                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                                                WHERE vr.estado=1 ";
                                $vret = $mysqli->query($vre);
                            ?> 
                            <label for="sltVinculacion" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Vinculación:
                            </label>
                            <select name="sltVinculacion" id="sltVinculacion" title="Seleccione Vinculación Retiro" style="width: 140px;height: 30px" class="form-control col-sm-1" disabled="true">
                            	<option value="">Vinculación</option>
                            	<?php 
                            		while($rowVR = mysqli_fetch_row($vret))
                                    {
                            			echo "<option value=".$rowVR[0].">".$rowVR[4]."</option>";
                            		}
                            	?>                            	                           	
                            </select>
                            <label for="No" class="col-sm-2 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 0px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                        </div>
                  </div>
                
<!---------------------------------------------------------------------------------------------------->                        
    <div class="form-group form-inline" style="margin-top:5px; display:<?php echo $a?>">
                <?php  
                $sql = "SELECT          vr.id_unico,
                                        vr.numeroacto,
                                        vr.fechaacto,
                                        vr.fecha,
                                        vr.empleado,
                                        e.id_unico,
                                        e.tercero,
                                        t.id_unico,
                                        CONCAT_WS('',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
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
                                WHERE vr.empleado = $idTer";
                    $resultado = $mysqli->query($sql);
                ?>
        <!-- <div class="col-sm-12 text-left" style="display:<?php echo $a?>">-->
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:0px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <!-- Actualización 24 / 02 16:43 No es necesario mostrar el nombre del empleado
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        -->
                                        <td class="cabeza"><strong>Número Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Tipo Vinculación</strong></td>
                                        <td class="cabeza"><strong>Estado Vinculación</strong></td>
                                        <td class="cabeza"><strong>ID Vinculación</strong></td>
                                        <td class="cabeza"><strong>Causa Retiro</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <!-- Actualización 24 / 02 16:43 No es necesario mostrar el nombre del empleado
                                        <th class="cabeza">Empleado</th>
                                        -->
                                        <th class="cabeza">Número Acto</th>
                                        <th class="cabeza">Fecha Acto</th>
                                        <th class="cabeza">Fecha</th>
                                        <th class="cabeza">Tipo Vinculación</th>
                                        <th class="cabeza">Estado Vinculación</th>
                                        <th class="cabeza">ID Vinculación</th>
                                        <th class="cabeza">Causa Retiro</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) {                                         
                                            $vrfec = $row[3];
                                            if(!empty($row[3])||$row[3]!=''){
                                            $vrfec = trim($vrfec, '"');
                                            $fecha_div = explode("-", $vrfec);
                                            $aniof = $fecha_div[0];
                                            $mesf  = $fecha_div[1];
                                            $diaf  = $fecha_div[2];
                                            $vrfec  = $diaf.'/'.$mesf.'/'.$aniof;
                                        }else{
                                            $vrfec='';
                                        }

                                        
                                            $vfa = $row[2];
                                            if(!empty($row[2])||$row[2]!=''){
                                            $vfa = trim($vfa, '"');
                                            $fecha_div = explode("-", $vfa);
                                            $aniofa = $fecha_div[0];
                                            $mesfa = $fecha_div[1];
                                            $diafa = $fecha_div[2];
                                            $vfa = $diafa.'/'.$mesfa.'/'.$aniofa;
                                        }else{
                                            $vfa='';
                                        }
                                        
                                            $vrid   = $row[0];
                                            $vrnact = $row[1];
                                            #$vrfact = $row[2];
                                            #$vrfec  = $row[3];
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
                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_VINCULACION_RETIRO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <!-- Actualización 24 / 02 16:47 No es necesario mostrar el nombre del empleado
                                        <td class="campos"><?php #echo $ternom?></td>                
                                        -->                             
                                        <td class="campos"><?php echo $vrnact?></td>                
                                        <td class="campos"><?php echo $vfa?></td>                   
                                        <td class="campos"><?php echo $vrfec?></td>                
                                        <td class="campos"><?php echo $tvnom?></td>                
                                        <td class="campos"><?php echo $evrnom?></td>                
                                        <td class="campos"><?php echo $vrv?></td>                
                                        <td class="campos"><?php echo $crnom?></td>                
                                    </tr> 
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
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_CAUSA_RETIRO.php">CAUSA RETIRO</a>
                                </td>
                            </tr>                                                        
                            <!--<tr>   
                            no es necesario mostrar el estado porque solo pueden ser dos vinculacion retiro                                 
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_ESTADO_VINCULACION_RETIRO.php">ESTADO</a>
                                </td>
                            </tr>-->                                                        
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_VINCULACION.php">TIPO VINCULACION</a>
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
          <p>¿Desea eliminar el registro seleccionado de Vinculación Retiro?</p>
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
                  url:"json/eliminarVinculacionRetiroJson.php?id="+id,
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
        var fechain= document.getElementById('sltFechaA').value;
        var fechafi= document.getElementById('sltFecha').value;
          var fi = document.getElementById("sltFecha");
        fi.disabled=false;
      
       
            $( "#sltFecha" ).datepicker( "destroy" );
            $( "#sltFecha" ).datepicker({ changeMonth: true, minDate: fechain});
     
}
</script>
<script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
         $("#sltVinculacion").select2();
</script>
</body>
<script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
         $("#sltCausa").select2();
</script>
<script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
         $("#sltTipo").select2();
</script>
</body>
</html>