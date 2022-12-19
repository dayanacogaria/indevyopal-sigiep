<?php
require_once ('head.php');
require_once ('Conexion/conexion.php');
require_once('./jsonSistema/funcionCierre.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
 
<title>Listar Detalles Comprobante</title>
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
      <div class="col-sm-8" style="margin-top:10px">
                    <?php 
                    if(!empty($_GET['id'])){
                        $idComprobante = $_GET['id'];
                        $result = "";
                        $sql="  
                                SELECT
                                   DT.id_unico,
                                   CT.id_unico as cuenta,
                                   CT.nombre,
                                   CT.codi_cuenta,
                                   CT.naturaleza,
                                   N.id_unico,
                                   N.nombre,
                                   IF(CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos) IS NULL 
                                                            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                            (ter.razonsocial),
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos  )) AS 'NOMBRE', 
                                                ter.id_unico, 
                                                CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD',                                   
                                   CC.id_unico,
                                   CC.nombre,
                                   PR.id_unico,
                                   PR.nombre,
                                   DT.valor, 
                                   DT.revelacion , DT.conciliado 
                                FROM
                                  gf_detalle_comprobante DT
                                LEFT JOIN
                                  gf_cuenta CT ON DT.cuenta = CT.id_unico
                                LEFT JOIN
                                  gf_naturaleza N ON N.id_unico = DT.naturaleza
                                LEFT JOIN
                                  gf_tercero ter ON DT.tercero = ter.id_unico
                                LEFT JOIN
                                  gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
                                LEFT JOIN
                                  gf_centro_costo CC ON DT.centrocosto = CC.id_unico
                                LEFT JOIN
                                  gf_proyecto PR ON DT.proyecto = PR.id_unico
                                WHERE md5(DT.comprobante) = '$idComprobante'";
                                $rs = $mysqli->query($sql);
                                        
                    ?>
                    <input type="hidden" id="idPrevio" value="">
                    <input type="hidden" id="idActual" value="">
                    <?php 
                    $sumar = 0;
                    $sumaT = 0;
                    ?>
                    
                    <div class="table-responsive contTabla" >
                       <table id="Jtabla" cellpadding="0" cellspacing="0" border="0" class="display" >
                            <thead>
                            <tr>
                                <th width="7%" class="cabeza"></th>
                                <th>Cuenta Contable</th>
                                <th>Tercero</th>
                                <th>Centro Costo</th>
                                <th>Proyecto</th>
                                <th>Débito</th>
                                <th>Crédito</th>
                                <th>Documentos</th>
                                <th>Revelación</th>
                            </tr>
                        </thead>
                        <tbody>  
                            <?php 
                            while ($row = mysqli_fetch_row($rs)) { ?>
                            <tr>
                                <td class="campos">
                                    <?php if(!empty($_SESSION['idNumeroC'])){
                                        $cierre = cierrecnt($_SESSION['idNumeroC']);
                                        if($cierre ==0){ 
                                            if($row[16]==1){} else { ?>
                                    
                                    <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                                        <li class="glyphicon glyphicon-trash"></li>
                                    </a>
                                    <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>)">
                                        <li class="glyphicon glyphicon-edit"></li>
                                    </a> 
                                    <?php } } }?>
                                </td>
                                <!-- Código de cuenta y nombre de la cuenta -->
                                <td class="campos text-left" >
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="cuenta'.$row[0].'">'. (ucwords(mb_strtolower($row[3].' - '.$row[2]))).'</label>'; ?>
                                </td>
                                <!-- Datos de tercero -->
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" title="'.$row[9].'" style="font-weight:normal" id="tercero'.$row[0].'">'. (ucwords(mb_strtolower($row[7]))).'</label>'; ?>
                                    
                                </td>
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="centroC'.$row[0].'">'. (ucwords(mb_strtolower($row[11]))).'</label>'; ?>
                                    
                                </td>
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="proyecto'.$row[0].'">'. (ucwords(mb_strtolower($row[13]))).'</label>'; ?>
                                    
                                </td>
                                <!-- Campo de valor debito y credito. Validación para imprimir valor -->
                                <td class="campos text-right" align="center">

                                    <?php 

                                    if ($row[4] == 1) {
                                        if($row[14] >= 0){
                                            $sumar += $row[14];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($row[14], 2, '.', ',').'</label>';
                                            
                                        }else{
                                            echo '<label style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';

                                        }  
                                    }else if($row[4] == 2){
                                        if($row[14] <= 0){
                                            $x = (float) substr($row[14],'1');
                                            $sumar += $x;
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($x, 2,'.', ',').'</label>';
                                            
                                        }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                                            
                                        }
                                    }

                                   ?>                                            
                                </td>
                                <td class="campos text-right">
                                    <?php
                                    if ($row[4] == 2) {
                                        if($row[14] >= 0){
                                            $sumaT += $row[14];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($row[14], 2, '.', ',').'</label>';
                                            
                                        }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                            
                                        }
                                    }else if($row[4] == 1){
                                       if($row[14] <= 0){
                                            $x = (float) substr($row[14],'1');
                                            $sumaT += $x;
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($x, 2, '.', ',').'</label>';
                                            
                                    }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                            
                                        }
                                    }?>                                    
                                </td>
                                <td class="campos text-center">
                                    <div style="display:inline">
                                        <a id="btnDetalleMovimiento" onclick="javascript:abrirdetalleMov(<?php echo $row[0]?>,<?php echo $row[14]?>);" title="Comprobante detalle movimiento"><i class="glyphicon glyphicon-file"></i></a>                                        
                                        
                                    </div>
                                    
                                </td>
                                <td class="campos text-center">
                                    <?php if(empty($row[15])) { ?>
                                    <div style="display:inline">
                                            <a id="btnDRevelaciones" onclick="javascript:revelaciones(<?php echo $row[0]?>);" title="Revelación"><i class="glyphicon glyphicon-paste"></i></a>                                        
                                    </div> 
                                    <?php } else { ?>
                                        <div style="display:inline">
                                            <a id="btnDRevelaciones" onclick="javascript:verRevelaciones(<?php echo $row[0].','."'".$row[15]."'"?>);" title="Revelación"><i class="glyphicon glyphicon-eye-open"></i></a>                                        
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php }
                            ?>
                        </tbody>
                        </table>
                    
                    </div>
                    <script type="text/javascript" >
                      function abrirdetalleMov(id,valor){                                                                                                   
                        var form_data={                            
                          id:id,
                          valor:valor                          
                        };
                        $.ajax({
                          type: 'POST',
                            url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php#mdlDetalleMovimiento",
                            data:form_data,
                            success: function (data) { 
                              $("#mdlDetalleMovimiento").html(data);
                              $(".mov").modal('show');
                            }
                        });
                      }
                      function abrirdetalleMov1(id,valor){                                                                                                   
                        var form_data={                            
                          id:id,
                          valor:valor                          
                        };
                        $.ajax({
                          type: 'POST',
                            url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                            data:form_data,
                            success: function (data) { 
                              $("#mdlDetalleMovimiento").html(data);
                              $(".mov").modal('show');
                            }
                        });
                      }                                                                                          
                    </script>
                    <?php 
                    $valorD = $sumar;
                    $valorC = $sumaT;
                    #Diferencia
                    $diferencia = $valorC - $valorD;
                    $w = 0;
                    if($diferencia<0){
                      $w=substr($diferencia,1);
                    }else{
                      $w=$diferencia;
                    }                    
                    ?>
                    <style>
                        .valores:hover{
                            cursor: pointer;
                            color:#1155CC;
                        }
                    </style>
                    <div class="container">

                    </div>
                    <div class="col-sm-offset-6  col-sm-6 text-left">
                        <div class="col-sm-2">
                            <div class="form-group" style="margin-top:5px;margin-bottom:-10px" align="left">                                    
                                <label class="control-label">
                                    <strong>Totales:</strong>
                                </label>                                
                            </div>
                        </div>                        
                        <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
                            <?php 
                            if (($valorD) === NULL) { ?>
                                 <label class="control-label valores" title="Suma débito">0</label>                   
                            <?php
                            }else { ?>
                                 <label class="control-label valores" title="Suma débito"><?php echo number_format($valorD, 2, '.', ',') ?></label>
                            <?php }
                            ?>
                        </div>                        
                        <div class="col-sm-2 text-right col-sm-offset-1" style="margin-top:5px;" align="left">
                            <?php 
                            if ($valorC === NULL) { ?>
                                <label class="control-label valores" title="Suma crédito">0</label>
                            <?php
                            }else{ ?>
                                <label class="control-label valores" title="Suma crédito"><?php echo number_format($valorC, 2, '.', ','); ?></label>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
                            <?php 
                            if ($diferencia === 0) { ?>
                                  <label class="control-label text-right valores" title="Diferencia">0.00</label>                          
                            <?php }else{ ?>
                                  <label class="control-label text-right valores" title="Diferencia"><?php echo number_format($diferencia, 2, '.', ',') ; ?></label>
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
                            <?php if($_REQUEST['t']=='2'){
                                 echo '<a class="btn btn-primary btnInfo" href="registrar_GF_COMPROBANTE_CAUSACION.php">VOLVER</a>';
                            } else {
                                echo '<a class="btn btn-primary btnInfo" href="registrar_GF_COMPROBANTE_CONTABLE.php">VOLVER</a>';
                            }?>
                            
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
                    <div class="form-group" style="margin-top: -15px;">                                    
                       <label class="control-label" style="display:inline-block; width:140px"><strong class="obligado">*</strong>Cuenta: </label>
                        <select name="sltcuenta" id="sltcuenta" class="select2_single form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px"  title="Seleccione cuenta" required="required">
                            
                        </select>
                    </div>  
                    <div class="form-group" style="margin-top:-25px;">
                        
                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado">*</strong>Tercero
                        </label>
                        <select name="slttercero" id="slttercero" class="select2_single form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" title="Seleccione tercero" required="">
                            <option value="2">Tercero</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: -25px;" >
                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado"></strong>Centro Costo:
                        </label>
                        <select name="sltcentroc" id="sltcentroc" class="select2_single form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" title="Seleccione centro costo" required="">

                        </select>
                    </div>
                    <div class="form-group" style="margin-top: -25px;" >

                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado"></strong>Proyecto:
                        </label>
                        <select name="sltproyecto" id="sltproyecto" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px"  title="Seleccione proyecto" >
                           
                        </select>
                    </div>
                    <div class="form-group" style="margin-top:-25px;">
                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado">*</strong>Valor Débito:
                        </label>
                        <input type="text" name="txtValorDebito" onkeypress="return justNumbers(event);" id="txtValorDebito" minlength="1" maxlength="50" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" onkeyup="debito();"/>
                    </div>
                    <div class="form-group" style="margin-top:-25px;">
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
<div class="modal fade" id="modDetalleValor" role="dialog" align="center" >
    <div class="modal-dialog" >
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Agregar Cuenta Contable</h4>
            </div>
            <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="guardarModificacion">
            <div class="modal-body" style="margin-top: 8px">
                    <input type="hidden" name="idS" id="idS"/>
                    
                    <div class="form-group" style="margin-top:5px;">
                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado">*</strong>Valor Débito:
                        </label>
                        <input type="text" name="txtValorDebitoS" onkeypress="return justNumbers(event);" id="txtValorDebitoS" minlength="1" maxlength="50" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" onkeyup="debito1();"/>
                    </div>
                    <div class="form-group" style="margin-top:5px;">
                        <label class="control-label" style="display:inline-block; width:140px">
                            <strong class="obligado">*</strong>Valor Crédito:
                        </label>
                        <input type="text" name="txtValorCreditoS" onkeypress="return justNumbers(event);" id="txtValorCreditoS" minlength="1" maxlength="50" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" onkeyup="credito1();"/>
                    </div>
                    <input type="hidden" name="id" id="id" />

            </div>

            <div id="forma-modal" class="modal-footer">
                <button type="submit" id="guardarMod" onclick="guardarModificacionsd()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Guardar</button>
                <button type="button" id="cancelarMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
            </div>
        </form>
        </div>
    </div>
</div>
<script>
     function show_inputs(id){
        $("#idS").val(id);
        var form_data ={action:2, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
            data: form_data,
            success: function(response){
                console.log(response);
                if(response>0){
                   $("#txtValorDebitoS").val(response);
                   $("#txtValorCreditoS").val('0');
                   $("#txtValorCreditoS").prop('disabled',true);
                } else {
                    $("#txtValorDebitoS").val('0');
                    $("#txtValorCreditoS").val(response*-1);
                    $("#txtValorDebitoS").prop('disabled',true);
                }
            }
        });
            
        $("#modDetalleValor").modal("show");
            
       
    }
</script>

<script type="text/javascript">  
    function debito1(){
        var debito = document.getElementById("txtValorDebitoS").value;

        if(debito>0 || debito.length>0 || debito !=''){
            $("#txtValorCreditoS").prop('disabled',true);

        } else {
           $("#txtValorCreditoS").prop('disabled',false);
        }
    }
</script>
<script>
    function credito1(){
        var credito = document.getElementById('txtValorCreditoS').value;
        if(credito>0 || credito.length>0 || credito !=''){
            $("#txtValorDebitoS").prop('disabled',true);
        } else {
             $("#txtValorDebitoS").prop('disabled',false);
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
    function guardarModificacionsd(){
        
        var id          = $("#idS").val();
        var debito      = $("#txtValorDebitoS").val();
        var credito     = $("#txtValorCreditoS").val();
        
        if( debito =="" && credito ==""){
            $("#modalError").modal("show");
        } else {
            var form_data={action:3, id:id, debito:debito, credito:credito};
            $.ajax({
                type:'POST',
                url:'jsonPptal/comprobantesIngresoJson.php',
                data:form_data,
                success: function(data){
                    result = JSON.parse(data);        
                    if (result==true) {
                        $("#infoM").modal('show');
                    }else {                                
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
</script>
<script>
    function modificar(id){
        $("#id").val(id);
        var form_data ={action:4, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
            data: form_data,
            success: function(response){
                
                $("#sltcuenta").html(response).focus();
                $("#sltcuenta").select2({
                    allowClear:true
                });
            }
        });
        var form_data ={action:9, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/gf_tercerosJson.php",
            data: form_data,
            success: function(response){

                $("#slttercero").html(response).focus();
                $("#slttercero").select2({
                    allowClear:true
                });
            }
        });
        var form_data ={action:6, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
            data: form_data,
            success: function(response){

                $("#sltcentroc").html(response).focus();
                $("#sltcentroc").select2({
                    allowClear:true
                });
            }
        });
        var form_data ={action:7, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
            data: form_data,
            success: function(response){

                $("#sltproyecto").html(response).focus();
                $("#sltproyecto").select2({
                    allowClear:true
                });
            }
        });
        var form_data ={action:16, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
            data: form_data,
            success: function(response){

                $("#sltconcepto").html(response).focus();
                $("#sltconcepto").select2({
                    allowClear:true
                });
            }
        });
        var form_data ={action:17, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
            data: form_data,
            success: function(response){

                $("#sltrubro").html(response).focus();
                $("#sltrubro").select2({
                    allowClear:true
                });
            }
        });
        var form_data ={action:8, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
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
        var form_data ={action:9, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
            data: form_data,
            success: function(response){
                
                if(response==2){
                   $("#slttercero").prop('disabled',true);
                } else {
                    $("#slttercero").prop('disabled',false);
                }
            }
        });
        var form_data ={action:10, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
            data: form_data,
            success: function(response){
                if(response==2){
                   $("#sltcentroc").prop('disabled',true);
                } else {
                    $("#sltcentroc").prop('disabled',false);
                }
            }
        });
        var form_data ={action:11, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
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
   function guardarModificacion(){
    var id          = $("#id").val();
    var cuenta      = $("#sltcuenta").val();
    var tercero     = $("#slttercero").val();
    var centrocosto = $("#sltcentroc").val();
    var proyecto    = $("#sltproyecto").val();
    var debito      = $("#txtValorDebito").val();
    var credito     = $("#txtValorCredito").val();
    var concepto    = $("#sltconcepto").val();
    var rubroFuente = $("#sltrubro").val();

    var form_data = {
            is_ajax:1,
            id:+id,
            cuenta:cuenta,
            tercero:tercero,
            centroC:centrocosto,
            proyecto:proyecto,
            debito:debito,
            credito:credito,
            };
    var result='';
    $.ajax({
            type: 'POST',
            url: "json/modificarDetalleComprobante.php",
            data:form_data,
            success: function (data) {
                    result = JSON.parse(data);                        
                    if (result==true) {
                            $("#infoM").modal('show');
                    }else{
                            $("#noModifico").modal('show');
                    }                    
            }
    });
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
 <?php } ?>


 <script type="text/javascript" >
    function revelaciones(id){                                                                                                   
        $("#iddetalle").val(id);
        $("#myModalRevelacion").modal('show');
    }                                                                                        
</script>
 <!--  Modal revelaciones  -->  
<div class="modal fade" id="myModalRevelacion" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content client-form1">
      <div id="forma-modal" class="modal-header">       
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Revelación</h4>
      </div>
      <div class="modal-body ">
          <form  name="formRev" id="formRev" method="POST" action="javascript:modificarRevelacion()">
            <input type="hidden" name="iddetalle" id="iddetalle">
            <div class="form-group" style="margin-top: 13px;">
                <div>
                <label for="cuenta2m" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Revelación:</label>
                </div>
                <textarea name="revelacion" id="revelacion" required="required" style="width: 300px; height: 80px"></textarea>
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
 <script type="text/javascript" >
    function verRevelaciones(id, revelacion){                                                                                                   
        $("#iddetalleV").val(id);
        $("#revelacionV").val(revelacion);
        $("#myModalVerRevelacion").modal('show');
    }                                                                                        
</script>
<div class="modal fade" id="myModalVerRevelacion" role="dialog" align="center" >
              <div class="modal-dialog">
                <div class="modal-content client-form1">
                  <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Revelación</h4>
                  </div>
                  <div class="modal-body ">
                      <form  name="formVerRev" id="formVerRev" method="POST" action="javascript:modificarRevelacionVer()">
                        <input type="hidden" name="iddetalleV" id="iddetalleV">
                        <div class="form-group" style="margin-top: 13px;">
                            <div>
                            <label for="cuenta2m" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Revelación:</label>
                            </div>
                            <textarea name="revelacionV" id="revelacionV" required="required" style="width: 300px; height: 80px"></textarea>
                        </div>
                  </div>

                  <div id="forma-modal" class="modal-footer">
                      <button type="submit" class="btn" style="color: #000; margin-top: 2px">Modificar</button>
                    <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                  </div>
                  </form>
                </div>
              </div>
            </div>
             
             <script>
                 function modificarRevelacion(){
                     var formData = new FormData($("#formRev")[0]);  
                    $.ajax({

                      type:"POST",
                      url:"json/modificar_GF_DETALLE_REVELACIONJson.php",
                      data:formData,
                      contentType: false,
                       processData: false,
                      success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                          $("#myModalRevelacion").modal('hide');
                          $("#mdlGuardarRevelacion").modal('show');
                          $("#btnGuardadoRevelacion").click(function(){

                            $("#mdlGuardarRevelacion").modal('hide');
                            document.location.reload();

                          });
                        }else{
                           $("#myModalRevelacion").modal('hide'); 
                          $("#mdlGuardarRevelacionNo").modal('show');
                          $("#btnGuardadoRevelacionNo").click(function(){

                            $("#mdlGuardarRevelacionNo").modal('hide');
                            document.location.reload();

                          });

                        }
                      }
                    });
                 }
             </script>
             <script>
                 function modificarRevelacionVer(){
                     var formData = new FormData($("#formVerRev")[0]);  
                    $.ajax({

                      type:"POST",
                      url:"json/modificar_GF_DETALLE_REVELACIONVERJson.php",
                      data:formData,
                      contentType: false,
                       processData: false,
                      success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                          $("#myModalRevelacionVer").modal('hide');
                          $("#mdlGuardarRevelacionVer").modal('show');
                          $("#btnGuardadoRevelacionVer").click(function(){

                            $("#mdlGuardarRevelacionVer").modal('hide');
                            document.location.reload();

                          });
                        }else{
                           $("#myModalRevelacionVer").modal('hide'); 
                          $("#mdlGuardarRevelacionVerNo").modal('show');
                          $("#btnGuardadoRevelacionVerNo").click(function(){

                            $("#mdlGuardarRevelacionVerNo").modal('hide');
                            document.location.reload();

                          });

                        }
                      }
                    });
                 }
             </script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
       <div class="modal fade" id="mdlGuardarRevelacion" role="dialog" align="center" >
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>Información guardada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardadoRevelacion" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div>
             <div class="modal fade" id="mdlGuardarRevelacionNo" role="dialog" align="center" >
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>La información no se ha podido guardar.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardadoRevelacionNo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div> 
        <div class="modal fade" id="mdlGuardarRevelacionVer" role="dialog" align="center" >
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardadoRevelacionVer" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div>
             <div class="modal fade" id="mdlGuardarRevelacionVerNo" role="dialog" align="center" >
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>La información no se ha podido guardar.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardadoRevelacionVerNo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div> 
        <script>
        $('#slttercero').on('select2-open', function () {
            buscarTercero('slttercero');
        });

        function buscarTercero(campo) {
            console.log(campo);
            $('.select2-input').on("keydown", function (e) {
                let term = e.currentTarget.value;
                let form_data4 = {action: 8, term: term};

                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_tercerosJson.php",
                    data: form_data4,
                    success: function (data) {
                        let option = '<option value=""> - </option>';
                        console.log(data);
                        option = option + data;
                        $("#" + campo).html(option);

                    }
                });
            });

        }
        
    </script> 
</body>
</html>
