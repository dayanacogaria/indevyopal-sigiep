<?php
require_once ('Conexion/conexion.php');
require_once 'head_listar.php'; 
#ESTILOS Y VALIDACION ?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
label#fecha_programada-error, #fecha_ejecutada-error, #formaN-error, #responsable-error, #condicion-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
body{
    font-size: 12px;
}
</style>

<script>


$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<style>
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px; width:300px}
table.dataTable tbody td,table.dataTable tbody td{padding:1px 10px;}

    .cabeza{
        white-space:nowrap;
        padding: 20px;
    }
    
    .campos{
        padding:-20px;
    }
    td{
        width: 300px;
        
    }
    .form-control {font-size: 12px;}
    
</style>
<?php #DETALLES DEL PROCESO 
$id=$_GET['id'];
$query = "SELECT p.id_unico, "
        . "p.estado, "
        . "ep.nombre, "
        . "p.tipo_proceso, "
        . "tp.identificador, "
        . "tp.nombre, "
        . "p.tercero, "
        . "CONCAT(t.nombreuno, ' ', t.nombredos,' ', t.apellidouno,' ', t.apellidodos, '(',t.numeroidentificacion, ')') AS TERCERO, "
        . "p.proceso, "
        . "epp.nombre, "
        . "tpp.identificador, "
        . "tpp.nombre, "
        . "p.fecha, p.identificador "
        . "FROM gg_proceso p  "
        . "LEFT JOIN gg_estado_proceso ep ON p.estado = ep.id_unico "
        . "LEFT JOIN gg_tipo_proceso tp ON tp.id_unico = p.tipo_proceso "
        . "LEFT JOIN gf_tercero t ON p.tercero = t.id_unico "
        . "LEFT JOIN gg_proceso pr ON p.proceso = pr.id_unico "
        . "LEFT JOIN gg_estado_proceso epp ON pr.estado = epp.id_unico "
        . "LEFT JOIN gg_tipo_proceso tpp ON tpp.id_unico = pr.tipo_proceso "
        . "WHERE md5(p.id_unico)='$id'"; 
$procesos = $mysqli->query($query);
$ROPW=  mysqli_fetch_row($procesos);
$PROCESO= ucwords(strtolower($ROPW[13].'( '.$ROPW[4].' - '.$ROPW[5].' - '.$ROPW[2].')'));

#FORMA NOTIFICACIÓN
$notificacion = "SELECT id_unico, nombre FROM gg_forma_notificacion ORDER BY nombre ASC";
$notificacion = $mysqli->query($notificacion);

#ULTIMO DETALLE_PROCESO
$ultimoR ="SELECT MAX(id_unico) FROM gg_detalle_proceso WHERE proceso ='$ROPW[0]'";
$ultimoR = $mysqli->query($ultimoR);
$ultimoR = mysqli_fetch_row($ultimoR);
$idultimo = $ultimoR[0];

#BUSCAR DETALLE PROCESO
$detalle= "SELECT dp.id_unico, dp.fecha_programada, dp.tercero, "
        . "CONCAT(t.nombreuno, ' ', t.nombredos,' ', t.apellidouno,' ', t.apellidodos, '(',t.numeroidentificacion, ')') AS TERCERO, "
        . "dp.flujo_procesal, f.nombre, f.elemento_flujo, ef.nombre "
        . "FROM gg_detalle_proceso dp LEFT JOIN gf_tercero t ON dp.tercero = t.id_unico "
        . "LEFT JOIN gg_flujo_procesal fp ON dp.flujo_procesal = fp.id_unico "
        . "LEFT JOIN gg_fase f ON fp.fase = f.id_unico "
        . "LEFT JOIN gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico "
        . "WHERE dp.id_unico = '$idultimo'";
$detalle = $mysqli->query($detalle);
$detalle = mysqli_fetch_row($detalle);
$fase = $detalle[5].' - '.$detalle[7];
# SI EL ESTADO ES :
$estado = strtolower($ROPW[2]);
 if ($estado =='anulado' || $estado =='cancelado' || $estado =='inactivo' || $estado =='cerrado') { 
     $fase = ucwords($estado);?>

    <script>
        $(function(){
        document.getElementById('fecha_programada').disabled=true;
        document.getElementById('fecha_ejecutada').disabled=true;
        document.getElementById('responsable1').disabled=true;
        document.getElementById('formaN1').disabled=true;
        document.getElementById('observaciones').disabled=true;
        document.getElementById('guardar').disabled=true;
        document.getElementById('etapae').disabled=true;
        });
    </script>
 <?php } ?>

<script type="text/javascript">
    $(function(){
        
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
            changeYear: true,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        var fechaProceso = '<?php echo date("d/m/Y", strtotime($ROPW[12]));?>';
        var fechaD = '<?php echo date("d/m/Y", strtotime($detalle[1]));?>';
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_programada").datepicker({changeMonth: true, minDate:fechaProceso}).val(fechaD);
        $("#fecha_ejecutada").datepicker({changeMonth: true, minDate:fechaProceso}).val();
    });
</script>
<title>Detalle Proceso</title>
</head>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-9 text-left" style="margin-left: -16px; ">
            <h2 align="center" class="tituloform" style="margin-top:-3px">Detalle Proceso</h2>
                <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none; margin-top: -5px" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-15px;  background-color: #0e315a; color: white; border-radius: 5px">PROCESO:<?php echo $PROCESO; ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/GG_DETALLE_PROCESOJson.php">
                <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                <!--Flujo procesal, eestado, proceso-->
                <input name="id" id="id" type="hidden" value="<?php echo $detalle[0]?>">
                <input name="flujoprocesal" id="flujoprocesal" type="hidden" value="<?php echo $detalle[4]?>">
                <input name="estado" id="estado" type="hidden" value="<?php echo $estado;?>">
                <input name="proceso" id="proceso" type="hidden" value="<?php echo $ROPW[0];?>">
                
                <div class="form-group form-inline" style="margin-left:0px; margin-top:-25px">
                    <!--Fase-->
                    <div class="form-group form-inline"style="margin-left:5px" >
                        <label style="width:100px; margin-left: 58px;margin-bottom: 10px; display:inline" for="fase" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fase:&nbsp;</label>
                        <label style="width:186px; text-align: left; margin-top:-6px" class="control-label" style="display:inline"><i><?php echo ucwords(strtolower($fase))?></i></label>
                    </div>
                    <!--Fecha Programada-->
                    <div class="form-group form-inline"style="margin-left:10px" >
                        <label style="width:100px;" for="fecha_programada" class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha Programada:</label>
                        <input type="text" name="fecha_programada" id="fecha_programada" readonly="true" style="width:200px; display: inline; margin-top:12px; margin-left: 5px;" class="form-control" required="required" title="Ingrese Fecha Programada" placeholder="Fecha Programada">
                    </div>
                    <div class="form-group form-inline"style="margin-left:30px" >
                        <label style="width:100px;" for="fecha_ejecutada" class="control-label">Fecha Ejecutada:</label>
                        <input type="text" name="fecha_ejecutada" id="fecha_ejecutada" readonly="true" style="width:200px; display: inline;margin-top:12px; margin-left: 5px;" class="form-control" title="Ingrese Fecha Ejecutada" placeholder="Fecha Ejecutada">
                    </div>
                </div>
                <div class="form-group form-inline" style="margin-left:0px; margin-top:-30px">
                    <!--RESPONSABLE-->
                    <div class="form-group form-inline"style="margin-left:8px" >
                        <label style="width:100px; margin-bottom: 10px;" for="responsable" class="control-label" ><strong style="color:#03C1FB;">*</strong>Responsable:&nbsp;</label>
                       <input type="hidden" name="responsable" required id="responsable" value="<?php echo $detalle[2];?>" title="Seleccione responsable">
                        <select style="width:180px;" name="responsable1" id="responsable1" required="required" class="select2_single form-control" title="Seleccione responsable" required="required" onchange="llenarR();">
                            <option value="<?php echo $detalle[2]?>"><?php echo ucwords(strtolower($detalle[3]));?></option>
                                <?php  while($rowr = mysqli_fetch_row($responsable)){?>
                            <option value="<?php echo $rowr[1] ?>"><?php echo ucwords((strtolower($rowr[0].'('.$rowr[2].')')));}?></option>;
                        </select> 
                    </div>
                    <!--Forma Notificación-->
                    <div class="form-group form-inline" style="margin-left:13px">
                        <label style="width:100px;margin-right: 5px;" for="formaN" class="control-label"><strong style="color:#03C1FB;">*</strong>Forma Notificación:</label>
                        <?php while ($row3=  mysqli_fetch_row($notificacion)) { 
                                    $comp = strtolower($row3[1]); 
                                    if($comp=='na' || $comp =='n.a' || $comp=='n.a.') {
                                        $idforma=$row3[0];
                                        $nombreForma= $row3[1];
                                    } 
                                 }
                                 if($idforma==''|| $nombreForma==''){
                                     $idforma='';
                                     $nombreForma='-';
                                    }
                                $forman= "SELECT id_unico, nombre FROM gg_forma_notificacion WHERE id_unico != '$idforma'";    
                                $forman=$mysqli->query($forman);?>
                            <input type="hidden" name="formaN" required id="formaN" value="<?php echo $idforma?>" title="Seleccione forma notificación">
                        <select style="width:200px; " name="formaN1" id="formaN1" required="required" class="select2_single form-control" title="Seleccione Forma Notificación" required="required" onchange="llenarF();">
                            <option value="<?php echo $idforma?>"><?php echo ucwords(strtolower($nombreForma));?></option>
                            <?php while($row2 = mysqli_fetch_row($forman)){?>
                            <option value="<?php echo $row2[0] ?>"><?php echo ucwords((strtolower($row2[1])));}?></option>;
                        </select> 
                    </div>
                    
                    <!--OBSERVACIONES-->
                    <div class="form-group form-inline"style="margin-left:33px" >
                        <label style="width:80px;" for="observaciones" class="control-label">Observaciones:</label>
                        <textarea type="text" name="observaciones" id="observaciones"  style=" display: inline; margin-left:22px;  width: 200px; height: 65px" class="form-control"  title="Ingrese Observaciones" placeholder="Observaciones" maxlength="500"></textarea>
                    </div>
                    
                    <div class="form-group form-inline" style="margin-left:10px">
                         
                        <button id="guardar" type="submit" class="btn btn-primary sombra" title="Guardar" style="margin-left:8px; margin-top: 15px"> <i class="glyphicon glyphicon-floppy-disk" ></i></button>
                    </div>
                    
                </div>
                <div align="center" id="divCondicion" style="display: none; margin-left:0px; margin-top:-35px" class="form-group form-inline" >
                    <!--CONDICION-->
                    <div class="form-group form-inline"style="margin-left:8px" >
                        <label for="condicion" class="control-label" ><strong style="color:#03C1FB;">*</strong><?php echo '¿'.$labelFase.'?:'?></label>
                    </div>
                    <div class="form-group form-inline"style="margin-left:20px; margin-top: 5px;" >
                        <input type="radio" name="condicion" id="condicion"  title="Escoja una opción "value="1">Sí
                        <input type="radio" name="condicion" id="condicion" title="Escoja una opción " value="2">No
                    </div>
                    
                </div>
                </form>
            </div>
            <script>
            //FUNCION AL CAMBIAR LA FECHA PROGRAMADA
            $("#fecha_programada").change(function() {
                var programada= document.getElementById('fecha_programada').value;
                $("#fecha_ejecutada" ).datepicker( "destroy" );
                $("#fecha_ejecutada").datepicker({changeMonth: true, minDate: programada}).val(programada);
                
            });
            
            </script>    
             <input type="hidden" id="idPrevio" value="">
               <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed text-center" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto"></td>
                                    <td style="min-width: 20px; max-width: 20px;" align="center"></td>
                                    <td style="min-width: 200px;max-width: 200px;"><strong>Fase</strong></td>
                                    <td style="min-width: 100px;max-width: 100px;"><strong>Fecha Programada</strong></td>
                                    <td style="min-width: 100px;max-width: 100px;"><strong>Fecha Ejecutada</strong></td>
                                    <td style="min-width: 200px;max-width: 200px;"><strong>Responsable</strong></td>
                                    <td style="min-width: 150px;max-width: 150px;"><strong>Forma Notificación</strong></td>
                                    <td style="min-width: 200px;max-width: 200px;"><strong>Observaciones</strong></td>
                                    <td style="min-width: 100px;max-width: 100px;"><strong>Documento</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto"></th>
                                    <th style="min-width: 20px; max-width: 20px;"></th>
                                    <th style="min-width: 200px;max-width: 200px;">Fase</th>
                                    <th style="min-width: 100px;max-width: 100px;">Fecha Programada</th>
                                    <th style="min-width: 100px;max-width: 100px;">Fecha Ejecutada</th>
                                    <th style="min-width: 200px;max-width: 200px;">Responsable</th>
                                    <th style="min-width: 150px;max-width: 150px;">Forma Notificación</th>
                                    <th style="min-width: 200px;max-width: 200px;">Observaciones</th>
                                    <th class="oculto" style="min-width: 100px;max-width: 100px;"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
            </div>
        </div>
        <div class="col-sm-6 col-sm-1" style="margin-top:-24px; margin-left: -20px" >
            <table class="tablaC table-condensed" style="margin-left: -3px; ">
                <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px; height:35px">Adicional</h2>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="#" onclick="etapaEspecial();"><button id="etapae" class="btn btnInfo btn-primary">ETAPA ESPECIAL</button></a><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--Modales eliminar-->
<div class="modal fade" id="myModalEliminar" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de detalle proceso?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="verE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal3" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información eliminada correctamente</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal4" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver4" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal25" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver25" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal26" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver26" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<?php require_once 'footer.php';?>
</body>
<script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>
  <script>
      function llenarR(){
          var responsable = document.getElementById('responsable1').value;
          document.getElementById('responsable').value=responsable;
      }
      function llenarF(){
          var forma = document.getElementById('formaN1').value;
          document.getElementById('formaN').value=forma;
      }
  </script>
  <script>
    function eliminar(id){
        $("#myModalEliminar").modal('show');
        $("#verE").click(function(){
             $("#myModalEliminar").modal('hide');
             $.ajax({
                 type:"GET",
                 url:"jsonProcesos/eliminar_GG_DETALLE_PROCESOJson.php?id="+id,
                 success: function (data) {
                 result = JSON.parse(data);
                 if(result==true){
                     $("#myModal3").modal('show');
                     $("#ver3").click(function(){
                         document.location.reload(); 
                   });
                 }else{
                     $("#myModal4").modal('show');
                     $("#ver4").click(function(){
                       $("#myModal4").modal('hide');
                   });
                 }}
             });
         });
    }
</script>
<script>
function select(id){
            var responsable = 'selectResponsable'+id;
            $(".select2_single, #"+responsable).select2();
            var formaN = 'selectFormaN'+id;
            $(".select2_single, #"+formaN).select2();
        }
</script>
    <div class="modal fade" id="myModalRegistrarEtapaEspecial" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content client-form1">
                <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Etapa Especial</h4>
                </div>
                <form  name="form" method="POST" action="javascript:registrarEtapaEspecial()">
                    <div class="modal-body ">
                        <div class="form-group" style="margin-top: 13px;">
                            <label class="text-right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Flujo Procesal:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="etapaE" id="etapaE" class="select2_single form-control" title="Seleccione Etapa Especial" required>
                                <option value="">Etapa Especial</option>

                            </select>                                
                        </div> 
                        <div class="form-group" style="margin-top: 13px;">
                            <label class="text-right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Fecha Programada:</label>
                            <input type="text" name="fecha_programada_ee" id="fecha_programada_ee" readonly="true" title="Fecha programada" required="requiered" style="width:250px; height:35px">
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label class="text-right" style="display:inline-block; width:140px">Fecha Ejecutada:</label>
                            <input type="text" name="fecha_ejecutada_ee" id="fecha_ejecutada_ee" readonly="true" title="Fecha ejecutada" style="width:250px; height:32px">
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label  class="text-right" style="display:inline-block; width:140px">Elemento Relacional:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="etapaRelacionada" id="etapaRelacionada" class="select2_single form-control" title="Seleccione Elemento Flujo Relacionado">
                                <option value="">Elemento Relacional</option>

                            </select>                                
                        </div>
                    </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>

                    <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                </div>
                </form>
            </div>
        </div>
    </div>
  <div class="modal fade" id="myModal11" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información registrada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver11" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal12" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido registrar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver12" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
   <script>
        function etapaEspecial(){
        tipo_proceso = <?php echo $ROPW[3]?>;
        var fechaProceso = '<?php echo date("d/m/Y", strtotime($ROPW[12]));?>';
        var fechadefault = '<?php echo date("d/m/Y", strtotime($fechaEjecutada));?>';
        var ejecutadaAnterior = '<?php echo date("d/m/Y", strtotime($fechaEjecutadaA));?>';
        $( "#fecha_programada_ee" ).datepicker( "destroy" );
        $("#fecha_programada_ee").datepicker({changeMonth: true, minDate: ejecutadaAnterior}).val(fechadefault);
        $( "#fecha_ejecutada_ee" ).datepicker( "destroy" );
        $("#fecha_ejecutada_ee").datepicker({changeMonth: true, minDate: ejecutadaAnterior}).val();
         var form_data={
            existente:14,
            tipo_proceso:tipo_proceso      
        };
        $.ajax({
            type: 'POST',
            url: "consultasBasicas/consultarNumeros.php",
            data:form_data,
            success: function (data) { 
                $("#etapaE").html(data).fadeIn();
                $("#etapaE").css('display','none');
                
                var form_data={
                    existente:15,
                    tipo_proceso:tipo_proceso
                };
                $.ajax({
                    type: 'POST',
                    url: "consultasBasicas/consultarNumeros.php",
                    data:form_data,
                    success: function (data) { 
                        $("#etapaRelacionada").html(data).fadeIn();
                        $("#etapaRelacionada").css('display','none');
                        $("#myModalRegistrarEtapaEspecial").modal('show');
                    }
                });
            }
        });
       }
   </script>
   <script>
        
       function registrarEtapaEspecial(){
          
            var proceso = <?php echo $ROPW[0]?>;
            var tercero = <?php echo $id_tercero ?>;
            var flujo= document.getElementById('etapaE').value;
            var fechaP= document.getElementById('fecha_programada_ee').value;
            var fechaE= document.getElementById('fecha_ejecutada_ee').value;
            var flujoR= document.getElementById('etapaRelacionada').value;
          
            var form_data={
              proceso:proceso,
              tercero: tercero,
              flujo:flujo,
              fechaP: fechaP,
              fechaE:fechaE,
              flujoR:flujoR
          };
          $.ajax({
                  type:"POST",
                  url:"jsonProcesos/registrar_GG_ETAPA_ESPECIALJson.php",
                  data:form_data,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true) {
                      
                      $("#myModal11").modal('show');
                      $('#ver11').click(function(){
                          $("#myModalRegistrarEtapaEspecial").modal('hide');
                        document.location.reload(); 
                      });
                  } else { 
                      $("#myModal12").modal('show');
                      $('#ver12').click(function(){
                           $("#myModalRegistrarEtapaEspecial").modal('hide');
                        document.location.reload(); 
                      });
                  }
                  }
              });
      }
      
  </script>
  <script>
  $("#etapaE").change(function() {
      var id = document.getElementById('etapaE').value;
      var tipo_proceso = <?php echo $ROPW[3]?>;
      var form_data={
                    existente:16,
                    id:id, 
                    tipo:tipo_proceso
                };
                $.ajax({
                    type: 'POST',
                    url: "consultasBasicas/consultarNumeros.php",
                    data:form_data,
                    success: function (data) { 
                        $("#etapaRelacionada").html(data).fadeIn();
                        $("#etapaRelacionada").css('display','none');
                    }
                });
  });
  </script>