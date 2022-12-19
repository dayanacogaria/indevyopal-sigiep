<?php
##########################################################################################
#       ******************      MODIFICACIONES      ******************      #
##########################################################################################
#06/04/2018 |Erica G. |Parametrización Modificar
#29/06/2017 |ERICA G. | ARCHIVO CREADO                          
##########################################################################################
require_once ('head.php');
require_once ('Conexion/conexion.php');
$parmanno = $_SESSION['anno']; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
 
<title>Listar Saldos Iniciales</title>
<script type="text/javascript" language="javascript" src="js/jquery-1.10.2.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/> 
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css" media="screen" />
 
</head>
 
<body>
    <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once ('menu.php'); ?>
      <div class="col-sm-8 text-center" style="margin-top: -22px; margin-left:30px">
           <h2 class="tituloform" align="center" >Saldos Iniciales</h2>
     <?php 
     
            $sql = " SELECT
                        DT.id_unico,
                        CT.id_unico,
                        CT.nombre,
                        CT.codi_cuenta,
                        CT.naturaleza,
                        N.id_unico,
                        N.nombre,
                        T.id_unico,
                        IF(CONCAT_WS(' ',
                        T.nombreuno,
                        T.nombredos,
                        T.apellidouno,
                        T.apellidodos) 
                        IS NULL OR CONCAT_WS(' ',
                        T.nombreuno,
                        T.nombredos,
                        T.apellidouno,
                        T.apellidodos) = '',
                        (T.razonsocial),
                        CONCAT_WS(' ',
                        T.nombreuno,
                        T.nombredos,
                        T.apellidouno,
                        T.apellidodos)) AS NOMBRE,
                        T.numeroidentificacion,
                        TI.id_unico,
                        TI.nombre,
                        CC.id_unico,
                        CC.nombre,
                        PR.id_unico,
                        PR.nombre,
                        DT.valor
                    FROM
                        gf_detalle_comprobante DT
                    LEFT JOIN
                        gf_cuenta CT ON DT.cuenta = CT.id_unico
                    LEFT JOIN
                        gf_naturaleza N ON N.id_unico = DT.naturaleza
                    LEFT JOIN
                        gf_tercero T ON DT.tercero = T.id_unico
                    LEFT JOIN
                        gf_tipo_identificacion TI ON T.tipoidentificacion = TI.id_unico
                    LEFT JOIN
                        gf_centro_costo CC ON DT.centrocosto = CC.id_unico
                    LEFT JOIN
                        gf_proyecto PR ON DT.proyecto = PR.id_unico
                    LEFT JOIN gf_comprobante_cnt cnt ON DT.comprobante = cnt.id_unico 
                    LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico 
                    WHERE tc.clasecontable = 5 and cnt.parametrizacionanno = $parmanno ORDER BY CT.codi_cuenta ASC";
           $rs = $mysqli->query($sql);
            ?>
           
            <?php 
            $sumar = 0;
            $sumaT = 0;
            ?>
<table id="Jtabla" cellpadding="0" cellspacing="0" border="0" class="display" >
 
<thead>
<tr>
    <th></th>
    <th><center>Cuenta</center></th>
    <th><center>Tercero</center></th>
    <th><center>Centro Costo</center></th>
    <th><center>Proyecto</center></th>
    <th><center>Valor Débito</center></th>
    <th><center>Valor Crédito</center></th>
</tr>
 
</thead>
<tbody>  
     <?php while ($row = mysqli_fetch_row($rs)) { ?>
    <tr>
        <td>
            <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                <li class="glyphicon glyphicon-trash"></li>
            </a>
            <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>)">
                <li class="glyphicon glyphicon-edit"></li>
            </a>                                            
        </td>
        <td align="left">
            <?php echo (ucwords(mb_strtolower($row[3].' - '.$row[2])))?>
        </td>
        <td align="left">
            <?php echo (ucwords(mb_strtolower($row[8].' - '.$row[9])))?>
        </td>
        <td align="left">
            <?php echo (ucwords(mb_strtolower($row[13])))?>
        </td>
        <td align="left">
            <?php echo (ucwords(mb_strtolower($row[15])))?>
        </td>
        <td align="right">
            <?php
            if ($row[4] == 1) {
                    if($row[16] >= 0){
                        $sumar += $row[16];
                        echo number_format($row[16], 2, '.', ',');
                    }else{
                        echo '0,00';
                    }  
                }else if($row[4] == 2){
                    if($row[16] <= 0){
                        $x = (float) substr($row[16],'1');
                        $sumar += $x;
                        echo number_format($x, 2,'.', ',');
                    }else{
                        echo '0,00';
                    }
                }

               ?>                                            
            </td>
            <td align="right">
                <?php
                if ($row[4] == 2) {
                    if($row[16] >= 0){
                        $sumaT += $row[16];
                        echo number_format($row[16], 2, '.', ',');
                     }else{
                        echo '0,00';
                     }
                }else if($row[4] == 1){
                   if($row[16] <= 0){
                        $x = (float) substr($row[16],'1');
                        $sumaT += $x;
                        echo number_format($x, 2, '.', ',');
                   }else{
                        echo '0,00';
                    }
                }?>
            </td>
                
        
    </tr>
     <?php } ?>
                </tbody>
</table>
    <?php 
                    $valorD = $sumar;
                    $valorC = $sumaT;
                    #Diferencia
                    $diferencia = $valorD - $valorC;
                    ?>
                    <div class="col-sm-offset-8  col-sm-6 text-left">
                        <div class="col-sm-2">
                            <div class="form-group" style="margin-top:5px" align="left">                                    
                                <label class="control-label">
                                    <strong>Totales</strong>
                                </label>                                
                            </div>
                        </div>                        
                        <div class="col-sm-2 text-right" style="margin-top:10px;" align="left">
                            <?php 
                            if (($valorD) === NULL) { ?>
                                 <label class="control-label" title="Suma débito">0</label>                   
                            <?php
                            }else { ?>
                                 <label class="control-label" title="Suma débito"><?php echo number_format($valorD, 2, '.', ',') ?></label>
                            <?php }
                            ?>
                        </div>                        
                        <div class="col-sm-2  col-sm-offset-1" style="margin-top:10px;" align="left">
                            <?php 
                            if ($valorC === NULL) { ?>
                                <label class="control-label" title="Suma crédito">0</label>
                            <?php
                            }else{ ?>
                                <label class="control-label" title="Suma crédito"><?php echo number_format($valorC, 2, '.', ','); ?></label>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
      </div>
        <div class="col-sm-8 col-sm-1" style="margin-top:-25px"  >
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
                            <a class="btn btn-primary btnInfo" href="registrar_GF_SALDOS_INICIALES.php">REGISTRAR NUEVO</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> 
    </div>
    </div>
    
<script>
 
$(document).ready(function(){
    $('#Jtabla').DataTable();
 
});
</script>
<!-----------------ELIMINAR DETALLE--------------------------->
<script>
    function eliminar(id){
        console.log(id);
        var result = '';
        $("#myModal").modal('show');
        $("#ver").click(function(){
        $("#myModal").modal('hide');
        var form_data ={action:1, id:id};
            $.ajax({
                type:"POST",
                url:"json/eliminarDetalleComprobanteJson.php?id="+id,
                data:form_data,
                success: function (data) {
                    result = JSON.parse(data);
                    if(result==1) {
                        $("#myModal1").modal('show');
                    } else{ 
                        $("#myModal2").modal('show');
                    }
                }
            });
        });
    }
</script>
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de Detalle Comprobante?</p>
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
                <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#ver1').click(function(){
        document.location.reload();
    });
</script>
<script type="text/javascript">    
    $('#ver2').click(function(){  
        document.location.reload();
    });
</script>
<!-----------------FIN ELIMINAR DETALLE--------------------------->
<!-----------------MODIFICAR DETALLE--------------------------->

<div class="modal fade" id="modDetalle" role="dialog" align="center" >
    <div class="modal-dialog" >
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Agregar Cuenta Contable</h4>
            </div>
            <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="guardarModificacion">
            <div class="modal-body" style="margin-top: 8px">
                    <input type="hidden" name="id" id="id"/>
                    <div class="form-group" style="margin-top: 5px;">                                    
                       <label class="control-label" style="display:inline-block; width:140px"><strong class="obligado">*</strong>Cuenta: </label>
                        <select name="sltcuenta" id="sltcuenta" class="select2_single form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px"  title="Seleccione cuenta" required="required">
                            
                        </select>
                    </div>  
                    <div class="form-group" style="margin-top:5px;">
                        
                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado">*</strong>Tercero
                        </label>
                        <select name="slttercero" id="slttercero" class="select2_single form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" title="Seleccione tercero" required="">
                            <option value="2">Tercero</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: 5px;" >
                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado"></strong>Centro Costo:
                        </label>
                        <select name="sltcentroc" id="sltcentroc" class="select2_single form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" title="Seleccione centro costo" required="">

                        </select>
                    </div>
                    <div class="form-group" style="margin-top: 5px;" >

                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado"></strong>Proyecto:
                        </label>
                        <select name="sltproyecto" id="sltproyecto" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px"  title="Seleccione proyecto" >
                           
                        </select>
                    </div>
                    <script type="text/javascript">                                                                                                                                          
                        function justNumbers(e){   
                            var keynum = window.event ? window.event.keyCode : e.which;
                            if ((keynum == 8) || (keynum == 46) || (keynum == 45))
                            return true;
                            return /\d/.test(String.fromCharCode(keynum));
                        }
                    </script>
                    <div class="form-group" style="margin-top:5px;">
                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado">*</strong>Valor Débito:
                        </label>
                        <input type="text" name="txtValorDebito" onkeypress="return justNumbers(event);" id="txtValorDebito" minlength="1" maxlength="50" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" onkeyup="debito();"/>
                    </div>
                    <div class="form-group" style="margin-top:5px;">
                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado">*</strong>Valor Crédito:
                        </label>
                        <input type="text" name="txtValorCredito" onkeypress="return justNumbers(event);" id="txtValorCredito" minlength="1" maxlength="50" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" onkeyup="credito();"/>
                    </div>
                    <input type="hidden" name="id" id="id" />

            </div>

            <div id="forma-modal" class="modal-footer">
                <button type="submit" id="guardarMod" onclick="guardarModificacion()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Guardar</button>
                <button type="button" id="cancelarMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
            </div>
        </form>
        </div>
    </div>
</div>
<script>
    function modificar(id){
        $("#id").val(id);
        var form_data ={action:2, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/saldosInicialesJson.php",
            data: form_data,
            success: function(response){
                
                $("#sltcuenta").html(response).focus();
                $("#sltcuenta").select2({
                    allowClear:true
                });
            }
        });
        var form_data ={action:3, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/saldosInicialesJson.php",
            data: form_data,
            success: function(response){

                $("#slttercero").html(response).focus();
                $("#slttercero").select2({
                    allowClear:true
                });
            }
        });
        var form_data ={action:4, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/saldosInicialesJson.php",
            data: form_data,
            success: function(response){

                $("#sltcentroc").html(response).focus();
                $("#sltcentroc").select2({
                    allowClear:true
                });
            }
        });
        var form_data ={action:5, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/saldosInicialesJson.php",
            data: form_data,
            success: function(response){

                $("#sltproyecto").html(response).focus();
                $("#sltproyecto").select2({
                    allowClear:true
                });
            }
        });
        var form_data ={action:6, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/saldosInicialesJson.php",
            data: form_data,
            success: function(response){
                if(response>0){
                   $("#txtValorDebito").val(response);
                   $("#txtValorCredito").val('0');
                   $("#txtValorCredito").prop('disabled',true);
                } else {
                    $("#txtValorDebito").val('0');
                    $("#txtValorCredito").val(response*-1);
                    $("#txtValorDebito").prop('disabled',true);
                }
            }
        });
        /*CONSULTAR HABILITAR O DESHABILITAR CAMPOS*/
        var form_data ={action:7, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/saldosInicialesJson.php",
            data: form_data,
            success: function(response){
                
                if(response==2){
                   $("#slttercero").prop('disabled',true);
                } else {
                    $("#slttercero").prop('disabled',false);
                }
            }
        });
        var form_data ={action:8, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/saldosInicialesJson.php",
            data: form_data,
            success: function(response){
                if(response==2){
                   $("#sltcentroc").prop('disabled',true);
                } else {
                    $("#sltcentroc").prop('disabled',false);
                }
            }
        });
        var form_data ={action:9, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/saldosInicialesJson.php",
            data: form_data,
            success: function(response){
                console.log(response);
                if(response==2){
                   $("#sltproyecto").prop('disabled',true);
                } else {
                    $("#sltproyecto").prop('disabled',false);
                }
            }
        });
            
        $("#modDetalle").modal("show");
            
       
    }
</script>
<!----CAMBIO CUENTA MODIFICAR------>
<script>
    $("#sltcuenta").change(function(){
        var cuenta = $("#sltcuenta").val();
        var form_data ={action:10, id:cuenta};
        $.ajax({
            type:"POST",
            url:"jsonPptal/saldosInicialesJson.php",
            data:form_data,                                                    
            success: function (data) {
                console.log(data);
                if (data==1) {
                    $("#sltcentroc").prop('disabled',false);
                }else if(data==2){
                    $("#sltcentroc").prop('disabled',true);
                }                                                       
            }
        });
        var form_data ={action:11, id:cuenta};
        $.ajax({
            type:"POST",
            url:"jsonPptal/saldosInicialesJson.php",
            data:form_data,                                                    
            success: function (data) {
                console.log(data);
                if (data==1) {
                    $("#slttercero").prop('disabled',false);
                }else if(data==2){
                    $("#slttercero").val('2');
                    $("#slttercero").prop('disabled',true);
                }                                                       
            }
        });
        var form_data ={action:12, id:cuenta};
        $.ajax({
            type:"POST",
            url:"jsonPptal/saldosInicialesJson.php",
            data:form_data,                                                    
            success: function (data) {
                console.log(data);
                if (data==1) {
                    $("#slttercero").prop('disabled',false);
                }else if(data==2){
                    $("#sltproyecto").val('2147483647');
                    $("#sltproyecto").prop('disabled',true);
                }                                                       
            }
        });
    })
</script>
<script type="text/javascript">  
    function debito(){
        var debito = document.getElementById("txtValorDebito").value;

        if(debito>0 || debito.length>0 || debito !=''){
            $("#txtValorCredito").prop('disabled',true);

        } else {
           $("#txtValorCredito").prop('disabled',false);
        }
    }
</script>
<script>
    function credito(){
        var credito = document.getElementById('txtValorCredito').value;
        if(credito>0 || credito.length>0 || credito !=''){
            $("#txtValorDebito").prop('disabled',true);
        } else {
             $("#txtValorDebito").prop('disabled',false);
        }
    }
</script>
<script>
    function justNumbers(e){   
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
        return true;
        return /\d/.test(String.fromCharCode(keynum));
    }
</script>
<!----GUARDAR------>
<script>
    function guardarModificacion(){
        var id          = $("#id").val();
        var cuenta      = $("#sltcuenta").val();
        var tercero     = $("#slttercero").val();
        var proyecto    = $("#sltproyecto").val();
        var centrocosto = $("#sltcentroc").val();
        var debito      = $("#txtValorDebito").val();
        var credito     = $("#txtValorCredito").val();
        console.log('centro'+centrocosto);
        if(cuenta=="" || tercero =="" || id=="" || proyecto =="" || centrocosto ==""){
            $("#modalError").modal("show");
        } else {
            if( debito =="" && credito ==""){
                $("#modalError").modal("show");
            } else {
                var form_data={action:13, id:id, cuenta:cuenta, tercero:tercero, proyecto:proyecto, 
                centrocosto:centrocosto, debito:debito, credito:credito};
                $.ajax({
                    type:'POST',
                    url:'jsonPptal/saldosInicialesJson.php',
                    data:form_data,
                    success: function(data){
                        console.log(data);
                        result = JSON.parse(data);        
                        if (result==true) {
                            $("#infoM").modal('show');
                        } else {                                
                            if(result=='0'){
                                $("#myModal3").modal('show');                                                                                            
                            }else{
                                $("#noModifico").modal('show');                                                                                            
                            }
                            
                        } 
                    }
                });
            }
        }
        
    }
</script>
<div class="modal fade" id="modalError" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Los datos están incompletos.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                <p>La información ya existe.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>  
<div class="modal fade" id="infoM" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información modificada correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="noModifico" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">          
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se ha podido modificar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#btnModifico').click(function(){
        document.location.reload();
    });
</script>
<script type="text/javascript">
    $('#btnNoModifico').click(function(){
        document.location.reload();
    });
</script>
<script type="text/javascript" src="js/select2.js"></script>
</body>
</html>