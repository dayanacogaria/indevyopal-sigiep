<?php
require_once ('Conexion/conexion.php');
require_once 'head_listar.php'; 
#PROCESO 
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

#TERCERO
$responsable = "SELECT DISTINCT "
        . "CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos) AS NOMBRE ,"
        . "t.id_unico, t.numeroidentificacion "
        . "FROM gg_gestion_responsable gt "
        . "LEFT JOIN gf_tercero t ON t.id_unico = gt.tercero_uno OR t.id_unico = gt.tercero_dos "
        . "WHERE NOT EXISTS(SELECT * FROM gg_persona_proceso "
                . "WHERE tercero = t.id_unico AND proceso ='$ROPW[0]') "
        . "ORDER BY NOMBRE ASC";
 $responsable = $mysqli->query($responsable);

#LISTAR
 $listar = "SELECT
            dp.id_unico, 
            dp.porcentaje_participacion,
            dp.proceso,
            dp.tercero, CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos,' (',t.numeroidentificacion,')')           
          FROM
            gg_persona_proceso dp
          LEFT JOIN
            gf_tercero t ON dp.tercero = t.id_unico
          LEFT JOIN
            gg_proceso p ON p.id_unico = dp.proceso
          WHERE MD5
            (dp.proceso) = '$id'";
$resultado= $mysqli->query($listar);

$estado = strtolower($ROPW[2]);
 if ($estado =='anulado' || $estado =='cancelado' || $estado =='inactivo' || $estado =='cerrado') { ?>
    <script>
        $(function(){
        document.getElementById('tercero1').disabled=true;
        document.getElementById('porcentaje').disabled=true;
        document.getElementById('guardar').disabled=true;
        });
    </script>
 <?php } ?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #tercero-error, #numero-error  {
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
    .form-control {font-size: 12px;}
    
</style>
<?php #PORCENTAJE
$porcentaje= "SELECT SUM(porcentaje_participacion) FROM gg_persona_proceso WHERE proceso = '$ROPW[0]'";
$porcentaje = $mysqli->query($porcentaje);
$porcentaje = mysqli_fetch_row($porcentaje);
$porcentaje = $porcentaje[0];
$valorP = (100-$porcentaje);
$valorPM =sprintf("%01.2f", $valorP);
if($valorPM==0) {
?>
<script>
    $(function() {
        document.getElementById('numero').value = '';
       document.getElementById('numero').disabled=true;
    })
</script>
<?php } ?>
<script>
    $(function() {
        document.getElementById('numero').value = <?php echo $valorPM;?>
    })
</script>
<title>Tercero Asociado</title>
</head>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" >
            <h2 align="center" class="tituloform" style="margin-top:-3px">Tercero Asociado</h2>
            <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none; margin-top: -5px" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-15px;  background-color: #0e315a; color: white; border-radius: 5px">PROCESO:<?php echo $PROCESO; ?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/registrar_GG_TERCERO_ASOCIADOJson.php">
                    <p align="center" style=" margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" id="proceso" name="proceso" value="<?php echo $ROPW[0];?>">
                    <div class="form-group form-inline text-center" style="margin-left:0px; margin-top:-10px; margin-right: 0px">
                        <div class="form-group form-inline"style="margin-left:-5px; margin-top: 0px" >
                            <label style="width:150px; margin-bottom: 10px; margin-top: 10px" for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label>
                           <input type="hidden" name="tercero" required id="tercero"  title="Seleccione tercero asociado">
                            <select style="width:250px;" name="tercero1" id="tercero1" required="required" class="select2_single form-control" title="Seleccione tercero asociado" >
                                <option value="">Tercero</option>
                                <?php  while($rowr = mysqli_fetch_row($responsable)){?>
                                <option value="<?php echo $rowr[1] ?>"><?php echo ucwords((strtolower($rowr[0].' ('.$rowr[2].')')));}?></option>;
                            </select>
                           
                        </div>
                        <div class="form-group form-inline"style="margin-left:20px; margin-top: 10px" >
                                <label for="numero" style="width:150px; margin-bottom: 10px;" class="control-label"><strong style="color:#03C1FB;">*</strong>Porcentaje Participación:</label>
                                <input type="text" name="numero" id="numero" required="required" title="Ingrese el porcentaje participación" placeholder="Porcentaje Participación" class="form-control"  style=" display: inline; width:250px;" onkeypress="return validarNum1(event, true)" maxlength="5" >
                        </div>
                       
                        <div class="form-group form-inline" style="margin-left:30px; margin-top: 0px">
                            <button id="guardar" type="submit" class="btn btn-primary sombra" title="Guardar" style="margin-left:8px; margin-top: 15px"> <i class="glyphicon glyphicon-floppy-disk" ></i></button>
                        </div>
                    </div>        
                </form>
            </div>
               <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <input type="hidden" id="idPrevio" value="">
                        <table id="tabla" class="table table-striped table-condensed text-center" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                     <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Tercero</strong></td>
                                    <td><strong>Porcentaje participación</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th width="30%">Tercero</th>
                                    <th width="30%">Porcentaje Participación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_row($resultado)) { ?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td> 
                                        <a  href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="#" onclick="javascript:modificar(<?php echo $row[0].','.$row[1].','.$row[3].','.$row[2];?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td class="text-left">
                                        <?php echo '<label style="text-align:left; font-weight:normal" id="labelDocumento'.$row[0].'">'. ucwords(strtolower($row[4])).'</label>'; ?>
                                     </td>
                                    <td class="text-left">
                                        <?php echo '<label style="text-align:left; font-weight:normal" id="labelNum'.$row[0].'">'. ucwords(strtolower($row[1].'%')).'</label>'; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <br/>
                        <div class="text-left">
                            <label>Porcentaje asignado: <i><?php $porc =sprintf("%01.2f", $porcentaje); echo $porc;?>%</i></label><br/>
                            <label>Porcentaje por asignar: <i><?php echo $valorPM;?>%</i></label>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
<script src="js/select/select2.full.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>
  <script>
      $("#tercero1").change(function() {
            var responsable = document.getElementById('tercero1').value;
            document.getElementById('tercero').value= responsable;
        });
  </script>
<!--Mensajes Eliminar-->
<div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de documento proceso?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
    <div class="modal fade" id="myModalModificar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content client-form1">
                <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Documento</h4>
                </div>
                <form  name="formModificar" id="formModificar" method="POST" action="javascript:guardarCambios()">
                    <div class="modal-body ">
                        <input type="hidden" id="idMod" name="idMod">
                        <input type="hidden" id="porcentajeAnterior" name="porcentajeAnterior">
                        <input type="hidden" id="procesoMod" name="procesoMod">
                        <div class="form-group form-inline" style="margin-left:0px; margin-top:-10px; margin-right: 0px">
                            <label class="text-right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Tercero:</label>
                            <select class="select2_single form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="terceroMod" id="terceroMod"  title="Seleccione tercero asociado" required>
                                <option value="">Tercero</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label class="text-right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Porcentaje Participación:</label>
                            <input type="text" id="porcentajeMod" name="porcentajeMod" style="width: 250px" onkeypress="return validarNum2(event, true)" maxlength="5">
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
<div class="modal fade" id="myModal5" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal6" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<?php require_once ('footer.php'); ?>
<script type="text/javascript" src="js/menu.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script>
    var validarNum1 = function (event){
    event = event || window.event;
    var charCode = event.keyCode || event.which;
    var first = (charCode <= 57 && charCode >= 48);
    var numero = document.getElementById('numero').value;
    var char = parseFloat(String.fromCharCode(charCode));
    var num = parseFloat(numero+char);
    var com = parseFloat(<?php echo sprintf("%01.2f", $valorP);?>);
    
        var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        var dec = match[0].length;
        if(dec<=3){
            if(num < com){
                if (charCode ==46){
                    var element = event.srcElement || event.target;
                    if(element.value.indexOf('.') == -1){
                    return (charCode =46);
                    }else{
                       return first; 
                    }
                    } else {
                    return first;
                }
            } else {
                if(num <=com){
                    return first;
                }else{
                    return false;
                }
            }
        } else { 
            return false;
        }
    
       
    
}
</script>
<script>
    
    var validarNum2 = function (event){
    event = event || window.event;
    var charCode = event.keyCode || event.which;
    var first = (charCode <= 57 && charCode >= 48);
    var numero = document.getElementById('porcentajeMod').value;
    var char = parseFloat(String.fromCharCode(charCode));
    var num = parseFloat(numero+char);
    var valor1 = document.getElementById('porcentajeAnterior').value;
    var valor1 = parseFloat(valor1);
    var valor2 = <?php echo sprintf("%01.2f", $valorP);?>;
    var valor2 = parseFloat(valor2);
    var com = parseFloat(valor1+valor2);
        var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        var dec = match[0].length;
        if(dec<=3){
            if(num < com){
                if (charCode ==46){
                    var element = event.srcElement || event.target;
                    if(element.value.indexOf('.') == -1){
                    return (charCode =46);
                    }else{
                       return first; 
                    }
                    } else {
                    return first;
                }
            } else {
                if(num <=com){
                    return first;
                }else{
                    return false;
                }
            }
        } else { 
            return false;
        }
    
       
    
}
</script>

<!-- Función para la eliminación del registro. -->
<script type="text/javascript">
function eliminar(id) {
    var result = '';
    $("#myModal").modal('show');
    $("#ver").click(function(){
        $("#mymodal").modal('hide');
        $.ajax({
            type:"GET",
            url:"jsonProcesos/eliminar_GG_TERCERO_ASOCIADOJson.php?id="+id,
            success: function (data) {
                result = JSON.parse(data);
                if(result==true){
                    $("#myModal1").modal('show');
                    $('#ver1').click(function(){
                        document.location.reload(); 
                    });
                } else { 
                    $("#myModal2").modal('show');
                    $('#ver2').click(function(){
                        document.location.reload(); 
                    });
                }
            }
        });
    });
}
</script>
<script>
     function modificar(id, porcentaje, tercero, proceso){
         
         $('#idMod').val(id);
         $('#procesoMod').val(proceso);
         $('#porcentajeMod').val(porcentaje);
         $('#porcentajeAnterior').val(porcentaje);
        
          var form_data={
            case:5,
            id:tercero,
            proceso: proceso,
        }
        $.ajax({
            type: 'POST',
            url: "consultasBasicas/busquedas.php",
            data:form_data,
            success: function (data) { 
                    $("#terceroMod").html(data).fadeIn();
                     $("#terceroMod").val(tercero);
                    $("#terceroMod").css('display','none');
                    $("#myModalModificar").modal('show');
            }
        });
         
     }
</script>
<script>
function guardarCambios(){
   
    var formData = new FormData($("#formModificar")[0]);  
        $.ajax({
            type: 'POST',
            url: "jsonProcesos/modificar_GG_TERCERO_ASOCIADOJson.php",
            data:formData,
            contentType: false,
             processData: false,
            success: function (data) { 
                result = JSON.parse(data);
                if(result==true){
                $("#myModalModificar").modal('hide');
                $("#myModal5").modal('show');
                $("#ver5").click(function(){
                    $("#myModal5").modal('hide');
                   document.location.reload(); 

                });
              }else{
                 $("#myModalSubirDoc").modal('hide'); 
                $("#myModal6").modal('show');
                $("#ver6").click(function(){

                  $("#myModal6").modal('hide');
                  document.location.reload(); 

                });

              }
            }
        });
    }
    
</script>
