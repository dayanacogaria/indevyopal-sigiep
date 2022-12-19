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
                        $idComprobanteI = $_GET['id'];
                        $result = "";
                        $sql = "SELECT DISTINCT
                            dtc.id_unico,
                            ct.id_unico,
                            ct.nombre,
                            rb.id_unico rubro,
                            rb.codi_presupuesto,
                            rb.nombre,
                            cnt.id_unico cuenta,
                            cnt.codi_cuenta,
                            cnt.nombre,
                            cnt.naturaleza,
                            dtc.valor,
                            pr.id_unico proyecto,
                            pr.nombre,
                            ctr.id_unico centroc,
                            ctr.nombre,
                            dtc.tercero,
                            pptal.id_unico,
                            ft.nombre,
                            pptal.id_unico,
                            dtc.conciliado, 
                            IF(CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos) 
                            IS NULL OR CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos) = '',
                            (tr.razonsocial),
                            CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion ,
                            dtc.comprobante 
                          FROM
                            gf_detalle_comprobante dtc
                          LEFT JOIN
                            gf_detalle_comprobante_pptal pptal ON dtc.detallecomprobantepptal = pptal.id_unico
                          LEFT JOIN
                            gf_concepto_rubro cnr ON pptal.conceptoRubro = cnr.id_unico
                          LEFT JOIN
                            gf_concepto ct ON cnr.concepto = ct.id_unico
                          LEFT JOIN
                            gf_rubro_fuente rbf ON rbf.id_unico = pptal.rubrofuente
                          LEFT JOIN
                            gf_rubro_pptal rb ON rbf.rubro = rb.id_unico
                          LEFT JOIN
                            gf_fuente ft ON rbf.fuente = ft.id_unico
                          LEFT JOIN
                            gf_concepto_rubro_cuenta ctrb ON cnr.id_unico = ctrb.concepto_rubro
                          LEFT JOIN
                            gf_cuenta cnt ON dtc.cuenta = cnt.id_unico
                          LEFT JOIN
                            gf_proyecto pr ON dtc.proyecto = pr.id_unico
                          LEFT JOIN
                            gf_centro_costo ctr ON dtc.centrocosto = ctr.id_unico
                          LEFT JOIN
                            gf_tercero tr ON dtc.tercero = tr.id_unico
                          WHERE
                            md5(dtc.comprobante)='$idComprobanteI'";
                        $result = $mysqli->query($sql);
                                        
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
                                    <th>Concepto</th>
                                    <th>Rubro Fuente</th>
                                    <th>Cuenta</th>
                                    <th>Débito</th>
                                    <th>Crédito</th>                                    
                                    <th>Centro Costo</th>
                                    <th>Proyecto</th>
                                    <th>Tercero</th>
                                    <th>Documentos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_row($result)){ ?>
                                <tr>
                                    <td class="campos">  
                                        <?php if(!empty($idComprobanteI)){
                                        $cierre = cierrecnt($row[22]);
                                        if($cierre ==0){ ?>
                                        <?php if($row[19] =='1') { 
                                        } else {
                                        if(!empty($row[16]) ){?>
                                            <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>,<?php echo $row[16]; ?>)" title="Eliminar">
                                                <li class="glyphicon glyphicon-trash"></li>
                                            </a>
                                          <?php
                                        }else{?>
                                            <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>,0)" title="Eliminar">
                                                <li class="glyphicon glyphicon-trash"></li>
                                            </a>
                                        <?php    
                                        }
                                        ?>
                                        <?php 
                                        if(empty($row[1])){ ?>
                                          <a  href="#<?php echo $row[0] ?>" onclick="javascript:show_inputs(<?php echo $row[0] ?>)"><li class="glyphicon glyphicon-edit"></li></a>
                                        <?php
                                        }else{
                                         ?>                                                                                
                                          <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);">
                                              <li class="glyphicon glyphicon-edit"></li>
                                          </a>                                            
                                        <?php } } } } ?>
                                    </td>
                                    <td class="campos text-left">
                                    <?php echo '<label class="valorLabel col-sm-12" style="font-weight:normal" id="concepto'.$row[0].'">'.ucwords(mb_strtolower($row[2])).'</label>'; ?>                                        
                                    </td>
                                    <td class="campos text-left">
                                            <?php 
                                            if(!empty($row[3])) {
                                            $sqlRB = "SELECT DISTINCT rb.id_unico,rb.codi_presupuesto,rb.nombre,ft.nombre,rft.id_unico
                                                    FROM gf_concepto_rubro cr 
                                                    LEFT JOIN gf_rubro_fuente rft ON cr.rubro = rft.rubro
                                                    LEFT JOIN gf_rubro_pptal rb ON rft.rubro = rb.id_unico
                                                    LEFT JOIN  gf_fuente ft ON rft.fuente = ft.id_unico
                                                    WHERE rb.id_unico =  $row[3] AND rb.id_unico IS NOT NULL";
	                                            $conR = $mysqli->query($sqlRB);

                                                    $tiene = mysqli_num_rows($conR);
	                                            
                                                    if($tiene>0){
                                                        $rubrofuente = mysqli_fetch_row($conR);
                                                        echo '<label class="valorLabel" style="font-weight:normal" title="'.$rubrofuente[2].' - '.$row[17].'" id="rubroFuente'.$row[0].'">'.$rubrofuente[1].' - '.$row[5].' - '.$row[17].'</label>';                                                                                     
                                                    }
                                            }
                                            ?>
                                    </td>
                                    <td class="campos text-left">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="cuenta'.$row[0].'">'.(ucwords(mb_strtolower($row[7].' - '.$row[8]))).'</label>'; ?>                                  
                                        
                                    </td>
                                    <td class="campos text-right">
                                        <?php 
                                        if ($row[9] == 1) {
                                            if($row[10] >= 0){
                                                $sumar += $row[10];
                                                echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($row[10], 2, '.', ',').'</label>';
                                            }else{
                                                echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">0.00</label>';
                                            }  
                                        }else if($row[9] == 2){
                                            if($row[10] <= 0){
                                                $x = (float) substr($row[10],'1');
                                                $sumar += $x;
                                                echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($x, 2,'.', ',').'</label>';
                                             }else{
                                                echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">0.00</label>';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="campos text-right">
                                        <?php
                                        if ($row[9] == 2) {
                                            if($row[10] >= 0){
                                                $sumaT += $row[10];
                                                echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($row[10], 2, '.', ',').'</label>';
                                            }else{
                                                echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0.00</label>';
                                            }
                                        }else if($row[9] == 1){
                                           if($row[10] <= 0){
                                                $x = (float) substr($row[10],'1');
                                                $sumaT += $x;
                                                echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($x, 2, '.', ',').'</label>';
                                          }else{
                                                echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0.00</label>';
                                           }
                                        }?>
                                    </td>
                                    <td class="campos text-left">                                        
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="centroC'.$row[0].'">'. (ucwords(mb_strtolower($row[14]))).'</label>'; ?>
                                        
                                    </td>
                                    <td class="campos text-left">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="proyecto'.$row[0].'">'. (ucwords(mb_strtolower($row[12]))).'</label>'; ?>
                                         
                                    </td>
                                    <td class="campos">
                                            <?PHP echo '<label style="font-weight:normal" class="valorLabel" id="tercero'.$row[0].'">'. (ucwords(mb_strtolower($row[20]))).' - '.$row[21].'</label>'; 
                                            ?>
                                           
                                    </td>
                                    <td class="campos text-center">
                                        <?php 
                                        if(!empty($row[16])){ ?>
                                          <a href="javascript:void(0)" onclick="abrirdetalleMov(<?php echo $row[16];?>,<?php echo $row[10];?>)" data-toggle="modal" class="col-sm-6"><li class="glyphicon glyphicon-file"></li></a>
                                        <?php }else{ ?>
                                            <a href="javascript:void(0)" onclick="abrirdetalleMov1(<?php echo $row[0];?>,<?php echo $row[10];?>)" data-toggle="modal" class="col-sm-6"><li class="glyphicon glyphicon-file"></li></a>
                                        <?php }
                                         ?>                                        
                                    </td>
                                </tr>
                                <?php } ?>
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
                    <div class="col-sm-offset-4  col-sm-5 text-left">
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
                        <div class="col-sm-2 text-right col-sm-offset-1" style="margin-top:5px;" align="left">
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
                            <a class="btn btn-primary btnInfo" href="registrar_GF_COMPROBANTE_INGRESO.php">VOLVER</a>
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
                url:"jsonPptal/comprobantesIngresoJson.php",
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
                    <div class="form-group" style="margin-top: -5px;">                                    
                       <label class="control-label" style="display:inline-block; width:140px"><strong class="obligado">*</strong>Concepto: </label>
                        <select name="sltconcepto" id="sltconcepto" class="select2_single form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px"  title="Seleccione Concepto" required="required">
                            
                        </select>
                    </div> 
                    <div class="form-group" style="margin-top: -25px;">                                    
                       <label class="control-label" style="display:inline-block; width:140px"><strong class="obligado">*</strong>Rubro: </label>
                        <select name="sltrubro" id="sltrubro" class="select2_single form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px"  title="Seleccione Rubro" required="required">
                            
                        </select>
                    </div> 
                    <div class="form-group" style="margin-top: -25px;">                                    
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
        var form_data ={action:5, id:id};
        $.ajax({
            type: "POST",
            url: "jsonPptal/comprobantesIngresoJson.php",
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
<!----CAMBIO CUENTA MODIFICAR------>
<script>
    $("#sltconcepto").change(function(){
        var concepto = $("#sltconcepto").val();
        var form_data ={action:18, concepto:concepto};
        $.ajax({
            type:"POST",
            url:"jsonPptal/comprobantesIngresoJson.php",
            data:form_data,                                                    
            success: function (data) {
                
                $("#sltrubro").html(data).focus();
                $("#sltrubro").select2({
                    allowClear:true
                });                                                     
            }
        }); 
          
        var rubro = $("#sltrubro").val();
        var concepto = $("#sltconcepto").val();
        var form_data ={action:19, rubro:rubro,concepto:concepto};
        $.ajax({
            type:"POST",
            url:"jsonPptal/comprobantesIngresoJson.php",
            data:form_data,                                                    
            success: function (data) {
                console.log(data);
                $("#sltcuenta").html(data).focus();
                $("#sltcuenta").select2({
                    allowClear:true
                });                                                     
            }
        });  
        var cuenta = $("#sltcuenta").val();
        var form_data ={action:12, id:cuenta};
        $.ajax({
            type:"POST",
            url:"jsonPptal/comprobantesIngresoJson.php",
            data:form_data,                                                    
            success: function (data) {
                console.log(data);
                if (data==1) {
                    $("#sltcentroc").prop('disabled',false);
                }else if(data==2){
                    $("#sltcentroc").val('12');
                    $("#sltcentroc").prop('disabled',true);
                }                                                       
            }
        });
        var form_data ={action:13, id:cuenta};
        $.ajax({
            type:"POST",
            url:"jsonPptal/comprobantesIngresoJson.php",
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
        var form_data ={action:14, id:cuenta};
        $.ajax({
            type:"POST",
            url:"jsonPptal/comprobantesIngresoJson.php",
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
            action:20,
            id:id,
            cuenta:cuenta,
            tercero:tercero,
            centrocosto:centrocosto,
            proyecto:proyecto,
            debito:debito,
            credito:credito,
            concepto:concepto,
            rubroFuente:rubroFuente
            };
    var result='';
    $.ajax({
            type: 'POST',
            url: "jsonPptal/comprobantesIngresoJson.php",
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
</body>
</html>