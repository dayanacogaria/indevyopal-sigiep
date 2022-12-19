<?php
require_once ('Conexion/conexion.php');
require_once 'head_listar.php'; 
#DETALLE PROCESO 
$id=$_GET['id'];
$detalle="SELECT
            dp.id_unico,
            pr.identificador, 
            f.nombre,
            dp.fecha_programada,
            dp.fecha_ejecutada,
            CONCAT(t.nombreuno,
              ' ',t.nombredos,
              ' ',t.apellidouno,
              ' ',t.apellidodos,
              '(',t.numeroidentificacion,')'
            ),
            fn.nombre,
            dp.observaciones, 
            ef.nombre, dp.tercero, dp.forma_notificacion, 
            pr.estado, epr.nombre 
          FROM
            gg_detalle_proceso dp
          LEFT JOIN
            gg_flujo_procesal fp ON dp.flujo_procesal = fp.id_unico
          LEFT JOIN
            gg_fase f ON fp.fase = f.id_unico
          LEFT JOIN
            gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico
          LEFT JOIN
            gf_tercero t ON dp.tercero = t.id_unico
          LEFT JOIN
            gg_forma_notificacion fn ON dp.forma_notificacion = fn.id_unico 
          LEFT JOIN
            gg_proceso pr ON dp.proceso = pr.id_unico 
          LEFT JOIN 
            gg_estado_proceso epr ON pr.estado = epr.id_unico 
          WHERE md5(dp.id_unico)='$id'";

$detalle = $mysqli->query($detalle);
$detalle = mysqli_fetch_row($detalle);
$detalleP= 'Proceso: '.$detalle[1].'&nbsp;&nbsp;   FASE: '.$detalle[2].' - '.$detalle[8];
#tipodocumento 
$documento = "SELECT id_unico, nombre, es_obligatorio, consecutivo_unico, formato "
        . "FROM gf_tipo_documento ORDER BY nombre ASC";
$documento = $mysqli->query($documento);

#LISTAR
$listar = "SELECT
            dp.id_unico, 
            dp.numero_documento,
            dp.ruta,
            dp.documento,
            d.nombre,
            dp.detalle_proceso
          FROM
            gg_documento_detalle_proceso dp
          LEFT JOIN
            gf_tipo_documento d ON dp.documento = d.id_unico
          LEFT JOIN
            gg_detalle_proceso p ON p.id_unico = dp.detalle_proceso
          WHERE MD5
            (dp.detalle_proceso) = '$id'";
$resultado= $mysqli->query($listar);

$estado = strtolower($detalle[12]);
 if ($estado =='anulado' || $estado =='cancelado' || $estado =='inactivo' || $estado =='cerrado') { ?>
    <script>
        $(function(){
        document.getElementById('documento1').disabled=true;
        document.getElementById('numero1').disabled=true;
        document.getElementById('numero2').disabled=true;
        document.getElementById('file').disabled=true;
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
    label #documento-error, #numero-error, #archivos-error  {
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
<title>Documento Detalle Proceso</title>
</head>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" >
            <h2 align="center" class="tituloform" style="margin-top:-3px">Documento Detalle Proceso</h2>
            <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none; margin-top: -5px" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-15px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(strtolower($detalleP)); ?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/registrar_GG_DOCUMENTO_DETALLE_PROCESOJson.php">
                    <p align="center" style=" margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" id="proceso" name="proceso" value="<?php echo $detalle[0];?>">
                    <div class="form-group form-inline" style="margin-left:0px; margin-top:-10px; margin-right: 0px">
                        <div class="form-group form-inline"style="margin-left:-5px; margin-top: 0px" >
                            <label style="width:100px; margin-bottom: 10px; margin-top: 10px" for="documento" class="control-label" ><strong style="color:#03C1FB;">*</strong>Documento:</label>
                           <input type="hidden" name="documento" required id="documento"  title="Seleccione documento">
                            <select style="width:180px;" name="documento1" id="documento1" required="required" class="select2_single form-control" title="Seleccione documento" >
                                <option value="">Documento</option>
                                <?php  while($rowr = mysqli_fetch_row($documento)){?>
                                <option value="<?php echo $rowr[0] ?>"><?php echo ucwords((strtolower($rowr[1])));}?></option>;
                            </select>
                        </div>
                        <div class="form-group form-inline"style="margin-left:20px; margin-top: 10px" >
                         <!-- Obligatorio-->
                         <input type="hidden" name="obligatorio" id="obligatorio">
                            <div id="numeroR" style="display:inline; display: none; margin-left:0px;" >
                                <label for="numero" style="width:100px; margin-bottom: 10px;" class="control-label"><strong style="color:#03C1FB;">*</strong>Número:</label>
                                <input type="text" name="numero1" id="numero1" title="Ingrese el número" class="form-control"  style=" display: inline; width:200px;" onkeypress="return txtValida(event,'num')" maxlength="15" >
                            </div>
                            <!--No Obligatorio-->
                            <div id="numeroNr" style="display:inline; margin-left:0px;" >
                                <label for="numero" style="width:100px; margin-bottom: 10px;" class="control-label"><strong style="color:#03C1FB;"></strong>Número:</label>
                                <input type="text" name="numero2" id="numero2" title="Ingrese el número" class="form-control"  style="display: inline; width:200px;" onkeypress="return txtValida(event,'num')" maxlength="15">
                            </div>
                        </div>
                        <!--Archivo-->
                        <div class="form-group form-inline" style="margin-left:50px; margin-top: 11px" >
                                <input type="hidden" required="required" title="Seleccione Documento" id="archivos" name="archivos" required>
                                <input id="file" name="file" type="file" onchange="archivo()" style="height: 35px;" >
                             
                        </div>

                            <div class="form-group form-inline" style="margin-left:30px; margin-top: 0px">
                                <button id="guardar" type="submit" class="btn btn-primary sombra" title="Guardar" style="margin-left:8px; margin-top: 15px"> <i class="glyphicon glyphicon-floppy-disk" ></i></button>
                            </div>
                    </div>
                    <div class="form-group form-inline" style="margin-left:0px; margin-right: 0px; margin-top: -25px">
                        
                        <div class="form-group form-inline form-inline" style="margin-left:100px; margin-top:-5px; width: 200px;" >
                            <a href="#"><button id="generar" name="generar" style="display: none; height: 25px; margin-bottom:5px; margin-top: 8px"class="btn btnInfo btn-primary">GENERAR FORMATO</button></a><br/>
                        </div>
                        <div class="form-group form-inline text-right" style="margin-left:450px; margin-top:-5px; margin-right: 0px" >
                             <label for ="archivos" class="labelError" id="labelError" style="margin-left: 10px; color: #155180; font-weight: normal; font-style: italic;"></label>
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
                                    <td><strong>Documento</strong></td>
                                    <td><strong>Número</strong></td>
                                    <td><strong>Ver</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th width="30%">Documento</th>
                                    <th width="30%">Número</th>
                                    <th width="30%">Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_row($resultado)) { ?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td> 
                                        <a  href="#" onclick="javascript:eliminar(<?php echo $row[0].','."'".$row[2]."'";?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="#" onclick="javascript:modificar(<?php echo $row[0].','.$row[3].','."'".$row[1]."'".','."'".$row[2]."'".','.$row[5];?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td class="text-left">
                                        <?php echo '<label style="text-align:left; font-weight:normal" id="labelDocumento'.$row[0].'">'. ucwords(strtolower($row[4])).'</label>'; ?>
                                     </td>
                                    <td class="text-left">
                                        <?php echo '<label style="text-align:left; font-weight:normal" id="labelNum'.$row[0].'">'. ucwords(strtolower($row[1])).'</label>'; ?>
                                    </td>
                                    <td class="text-left">
                                        <div id="botonVer<?php echo $row[0]?>" style="display: inline">
                                            <a href="<?php echo $row[2];?>">
                                                <i title="Ver" class="glyphicon glyphicon-search"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
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
<!--Mensajes Eliminar-->
<div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de documento detalle proceso?</p>
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
    <div class="modal fade" id="myModalSubirDoc" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content client-form1">
                <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Documento</h4>
                </div>
                <form  name="formModificar" id="formModificar" method="POST" action="javascript:guardarCambios()">
                    <div class="modal-body ">
                        <input type="hidden" id="idMod" name="idMod">
                        <input type="hidden" id="procesoMod" name="procesoMod">
                        <input type="hidden" id="rutaMod" name="rutaMod">
                        <?php $docM="SELECT id_unico, nombre FROM gf_tipo_documento ORDER BY nombre";
                               $docM = $mysqli->query($docM);?>
                        <div class="form-group" style="margin-top: 13px;">
                            <label class="text-right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Documento:</label>
                            <select class="select2_single form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="documentoMod" id="documentoMod"  title="Seleccione documento" required>
                                <?php while ($rowDoc = mysqli_fetch_row($docM)) { ?>
                                <option value="<?php echo $rowDoc[0]?>"><?php echo ucwords(strtolower($rowDoc[1])); }?></option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label id="labelRequerido" name="labelRequerido" class="text-right" style="display:none; width:140px"><strong style="color:#03C1FB;">*</strong>Número:</label>
                            <label id="labelNoRequerido" name="labelNoRequerido" class="text-right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;"></strong>Número:</label>
                            <input type="text" id="numeroMod" name="numeroMod" style="width: 250px" onkeypress="return txtValida(event, 'num');">
                        </div>  
                        <div class="form-group" style="margin-top: 13px;">
                            <label class="text-right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Subir Documento:</label>
                            <input type="hidden" required="required" title="Seleccione Documento" id="archivosMod" name="archivosMod" required>
                            <input id="file" name="file" type="file" onchange="archivoModificar()" style="display: inline; height: 35px;  width: 250px" >
                            <label id="labelErrorModificar" name="labelErrorModificar" style="display: block; color: #155180;font-weight: normal; font-style: italic;"></label>
                        </div> 
                        <div class=" form-inline text-right" style=" margin-left: 10px ;margin-top: -20px" >
                            <a href="#"><button id="generarMod" name="generarMod" style="display:none" class="btn btnInfo btn-primary" >GENERAR FORMATO</button></a><br/>
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
$("#documentoMod").change(function() {
    var documento = document.getElementById('documentoMod').value;
    var numero = document.getElementById('numeroMod').value;
    
    if(documento != ''){
     var form_data={
            case:2,
            documento:documento,
        }
        $.ajax({
            type: 'POST',
            url: "consultasBasicas/busquedas.php",
            data:form_data,
            success: function (data) { 
                resultado = JSON.parse(data);
                var id = resultado[0];
                var nombre = resultado[1];
                var obligatorio = resultado[2];
                var consecutivo = resultado[3];
                var formato = resultado[4];
                if(formato>0){
                     document.getElementById('generarMod').style.display ='inline';
                }else {
                    document.getElementById('generarMod').style.display ='none';
                }
                if(obligatorio ==1 || obligatorio =='1'){
                    document.getElementById('numeroMod').setAttribute("required", "");
                    document.getElementById('labelRequerido').style.display ='inline-block';
                    document.getElementById('labelNoRequerido').style.display ='none';
                } else {
                   
                    document.getElementById('numeroMod').removeAttribute("required", "");
                    document.getElementById('labelNoRequerido').style.display ='inline-block';
                    document.getElementById('labelRequerido').style.display ='none'
                }
                if(numero ==''|| numero =="" ){
                    if(consecutivo ==1 || consecutivo =='1'){
                       var form_data={
                            case:4
                        } 
                        $.ajax({
                            type: 'POST',
                            url: "consultasBasicas/busquedas.php",
                            data:form_data,
                            success: function (data) { 
                                resultado = JSON.parse(data);
                                if(resultado>0){
                                    var data1= parseInt(resultado);
                                    var numero = (data1+1);
                                } else {
                                    var numero = '';
                                }
                                if(obligatorio ==1 || obligatorio =='1'){
                                    document.getElementById('numeroMod').value=numero; 
                                } else {
                                    document.getElementById('numeroMod').value=numero; 
                                }
                            }
                        });
                    }
                }
            }
        });
    }
    document.getElementById('numeroMod').removeAttribute("required", "");
});
</script>
<script> 
$("#documento1").change(function() {
    var documento = document.getElementById('documento1').value;
    document.getElementById('documento').value= documento;
    document.getElementById('numero1').value= '';
    document.getElementById('numero2').value= '';
    if(documento != ''){
     var form_data={
            case:2,
            documento:documento,
        }
        $.ajax({
            type: 'POST',
            url: "consultasBasicas/busquedas.php",
            data:form_data,
            success: function (data) { 
                resultado = JSON.parse(data);
                var id = resultado[0];
                var nombre = resultado[1];
                var obligatorio = resultado[2];
                var consecutivo = resultado[3];
                var formato = resultado[4];
                if(formato>0){
                     document.getElementById('generar').style.display ='block';
                }else {
                    document.getElementById('generar').style.display ='none';
                }
                if(obligatorio ==1 || obligatorio =='1'){
                    document.getElementById('obligatorio').value = '1';
                    document.getElementById('numeroR').style.display = 'inline';
                    document.getElementById('numero1').setAttribute("required", "");
                    document.getElementById('numeroNr').style.display = 'none';
                } else {
                    document.getElementById('obligatorio').value = '2';
                    document.getElementById('numeroR').style.display = 'none';
                    document.getElementById('numero1').removeAttribute("required", "");
                    document.getElementById('numeroNr').style.display = 'inline';
                }
                if(consecutivo ==1 || consecutivo =='1'){
                   var form_data={
                        case:4
                    } 
                    $.ajax({
                        type: 'POST',
                        url: "consultasBasicas/busquedas.php",
                        data:form_data,
                        success: function (data) { 
                            resultado = JSON.parse(data);
                            if(resultado>0){
                                var data1= parseInt(resultado);
                                var numero = (data1+1);
                            } else {
                                var numero = '';
                            }
                            if(obligatorio ==1 || obligatorio =='1'){
                                document.getElementById('numero1').value=numero; 
                            } else {
                                document.getElementById('numero2').value=numero; 
                            }
                        }
                    });
                }
            }
        });
    }
    document.getElementById('numeroR').style.display = 'none';
    document.getElementById('numero1').removeAttribute("required", "");
    document.getElementById('numeroNr').style.display = 'inline';
});
            </script>
<!--FUNCION PARA VERIFICAR QUE EL ARCHIVO QUE SE VA A SUBIR CUMPLA CON LAS ESPECIFICACIONES-->
<script>
  function archivo(){
       var formData = new FormData($("#form")[0]);  
     
     
        $.ajax({
            type: 'POST',
            url: "consultasBasicas/validacionDocumentos.php",
            data:formData,
            contentType: false,
             processData: false,
            success: function (data) {  
                resultado = JSON.parse(data);
                var mensaje = resultado["mensaje"];
                var valor = resultado["valor"];
                document.getElementById('labelError').innerHTML = mensaje;
                
                if(valor==1){
                    document.getElementById('archivos').value='1';
                } else {
                    document.getElementById('archivos').value='';
                }
            }
        });
}          
</script>
<script>
  function archivoModificar(){
       var formData = new FormData($("#formModificar")[0]);  
        $.ajax({
            type: 'POST',
            url: "consultasBasicas/validacionDocumentos.php",
            data:formData,
            contentType: false,
             processData: false,
            success: function (data) { 
                resultado = JSON.parse(data);
                var mensaje = resultado["mensaje"];
                var valor = resultado["valor"];
                document.getElementById('labelErrorModificar').innerHTML = mensaje;
                
                if(valor==1){
                    document.getElementById('archivosMod').value='1';
                } else {
                    document.getElementById('archivosMod').value='2';
                }
            }
        });
}          
</script>

<!-- Función para la eliminación del registro. -->
<script type="text/javascript">
function eliminar(id, ruta) {
    var result = '';
    $("#myModal").modal('show');
    $("#ver").click(function(){
        $("#mymodal").modal('hide');
        $.ajax({
            type:"GET",
            url:"jsonProcesos/eliminar_GG_DOCUMENTO_DETALLEJson.php?id="+id+"&ruta="+ruta,
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
     function modificar(id, documento, numero, ruta, proceso){
         
         $('#idMod').val(id);
         $('#documentoMod').val(documento);
         $('#numeroMod').val(numero);
         $('#rutaMod').val(ruta);
         $('#procesoMod').val(proceso);
          var form_data={
            case:2,
            documento:documento,
        }
        $.ajax({
            type: 'POST',
            url: "consultasBasicas/busquedas.php",
            data:form_data,
            success: function (data) { 
                resultado = JSON.parse(data);
                var obligatorio = resultado[2];
                var formato = resultado[4];
                if(formato>0){
                     document.getElementById('generarMod').style.display ='inline';
                }else {
                    document.getElementById('generarMod').style.display ='none';
                }
                if(obligatorio ==1 || obligatorio =='1'){
                    document.getElementById('numeroMod').setAttribute("required", "");
                    document.getElementById('labelRequerido').style.display ='inline-block';
                    document.getElementById('labelNoRequerido').style.display ='none';
                } else {
                   
                    document.getElementById('numeroMod').removeAttribute("required", "");
                    document.getElementById('labelNoRequerido').style.display ='inline-block';
                    document.getElementById('labelRequerido').style.display ='none';
                }
            }
        });
         $("#myModalSubirDoc").modal('show');
     }
</script>
<script>
function guardarCambios(){
     archivo = document.getElementById('archivosMod').value;
    if(archivo==2){
       
    } else {
    var formData = new FormData($("#formModificar")[0]);  
        $.ajax({
            type: 'POST',
            url: "jsonProcesos/modificar_GG_DOCUMENTO_DETALLEJson.php",
            data:formData,
            contentType: false,
             processData: false,
            success: function (data) { 
                result = JSON.parse(data);
                if(result==true){
                $("#myModalSubirDoc").modal('hide');
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
    }
</script>
