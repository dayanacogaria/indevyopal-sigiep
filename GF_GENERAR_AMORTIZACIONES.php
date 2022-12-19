<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#04/04/2019 | Creado
########################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('Conexion/ConexionPDO.php');
require 'jsonPptal/funcionesPptal.php';
require_once('head_listar.php');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$nanno = anno($anno);
if(!empty($_GET['m'])){
$rowc = $con->Listar("SELECT da.id_unico, tc.codigo,  tc.nombre, 
    cn.numero, c.nombre,  da.valor, da.comprobante, da.numero_cuota 
FROM gf_detalle_amortizacion da 
LEFT JOIN gf_amortizacion a ON da.amortizacion = a.id_unico 
LEFT JOIN gf_detalle_comprobante_pptal dc ON a.detallecomprobantepptal = dc.id_unico 
LEFT JOIN gf_comprobante_pptal cn ON dc.comprobantepptal = cn.id_unico 
LEFT JOIN gf_tipo_comprobante_pptal tc ON cn.tipocomprobante = tc.id_unico 
LEFT JOIN gf_concepto c ON a.concepto = c.id_unico 
LEFT JOIN gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico
WHERE cn.parametrizacionanno <= $anno AND pa.compania = $compania  
    AND  month(da.fecha_programada) = '".$_GET['m']."'
    AND  YEAR(da.fecha_programada) = '".$nanno."' "); 
}
?>
<html>
    <head>
        <title>Generar Amortizaciones</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="js/md5.pack.js"></script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Generar Amortizaciones</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="#" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes:</label>
                                <select name="mes" id="mes" class="select2_single form-control" title="Seleccione mes" style="height: auto " required>
                                    <?php 
                                    $h = 0;
                                    if(!empty($_GET['m'])){
                                        $vgm = $con->Listar("SELECT numero, mes  
                                            FROM gf_mes 
                                            WHERE numero ='".$_GET['m']."' AND parametrizacionanno = $anno ");
                                        echo '<option value="'.$vgm[0][0].'">'.$vgm[0][1].'</option>'; 
                                        $vg = $con->Listar("SELECT numero, mes  
                                            FROM gf_mes 
                                            WHERE parametrizacionanno = $anno AND numero !='".$_GET['m']."'");
                                        for ($i = 0; $i < count($vg); $i++) {
                                           echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].'</option>'; 
                                        }
                                        $bc = $con->Listar("SELECT cp.estado 
                                            FROM gf_mes m 
                                            LEFT JOIN gs_cierre_periodo cp ON m.id_unico = cp.mes 
                                            WHERE m.numero ='".$_GET['m']."' AND m.parametrizacionanno = $anno ");
                                        if(!empty($bc[0][0])){
                                            if($bc[0][0]==2){
                                                $h= 1;
                                            }
                                        }
                                    } else {
                                        echo '<option value="">Mes</option>';
                                        $vg = $con->Listar("SELECT numero, mes  
                                            FROM gf_mes 
                                            WHERE parametrizacionanno = $anno ");

                                        for ($i = 0; $i < count($vg); $i++) {
                                           echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].'</option>'; 
                                        }                                    
                                    }
                                    ?>
                                </select>
                                
                                <?php if(!empty($_GET['m'])){
                                    echo '<input type="hidden" name="id"  id="id" value="'.$_GET['m'].'">';
                                    if($h==0){
                                        echo '<button id="guardar" type="button" class="btn btn-primary sombra" style=" background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-left:20px;" title="Guardar">
                                            <li class="glyphicon glyphicon-floppy-disk"></li>
                                        </button>';
                                    }
                                } ?>
                            </div>
                        </form>
                    </div>
                    <br/>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed"  class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px" align="center"></td>
                                        <td><strong>Comprobante</strong></td>
                                        <td><strong>Concepto</strong></td>
                                        <td><strong>Valor</strong></td>
                                        <td><strong>Cuota</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Comprobante</th>
                                        <th>Concepto</th>
                                        <th>Valor</th>
                                        <th>Cuota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($_GET['m'])){
                                        for ($i = 0; $i < count($rowc); $i++) {
                                            echo '<tr>';
                                            echo '<td style="display: none;">'.$rowc[$i][0].'</td>';
                                            echo '<td>'; 
                                            if(!empty($rowc[$i][6])){
                                                echo '<a  onclick="javascript:ver('.$rowc[$i][6].')"><i title="ver" class="glyphicon glyphicon-list-alt"></i></a>';
                                            }
                                            echo '</td>';
                                            echo '<td>'.$rowc[$i][1].' - '.ucwords($rowc[$i][3]).'</td>'; 
                                            echo '<td>'.$rowc[$i][4].'</td>'; 
                                            echo '<td>'.number_format($rowc[$i][5],2,',','.').'</td>'; 
                                            echo '<td>'.$rowc[$i][7].'</td>'; 
                                            echo '</tr>';
                                        }
                                    }?>
                                </tbody>
                            </table>       
                        </div>            
                    </div> 
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensajeEliminar" name="mensaje"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    allowClear: true,
                });
            });
            $("#mes").change(function(){
                document.location = 'GF_GENERAR_AMORTIZACIONES.php?m='+$("#mes").val();
            });
        </script>
        <script>
             $("#guardar").click(function(){
                var id = $("#id").val();
                jsShowWindowLoad('Validando Amortización...');
                var form_data ={id:id,action:9}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                       //#alert(response);
                        console.log(response);
                        if(response>0){
                            var id_tc = response;
                            generar(id,id_tc)
                        } else {
                            $("#mensaje").html('No hay comprobante para amortizaciones');
                            $("#modalMensajes").modal("show");
                        }
                    }
                })
            })
        </script>
        <script>
            function generar(id,id_tc){
                jsShowWindowLoad('Generando Amortización...');
                var form_data ={id:id,action:10,id_tc:id_tc}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(data)
                    { 
                       jsRemoveWindowLoad();
                        
                        console.log(data);
                        if(data>0){
                            $("#mensaje").html(data+' Amortizaciones generadas');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location ='GF_GENERAR_AMORTIZACIONES.php';
                            })
                        } else {
                            $("#mensaje").html('No se ha podido guardar amortización');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location ='GF_GENERAR_AMORTIZACIONES.php';
                            })
                        }
                    }
                })
            }
            function ver(idCnt){
                //Vector de envio con mi variable
                var form_data ={id:idCnt,action:11}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(data)
                    { 
                        $("#mensaje").html(data);
                        $("#modalMensajes").modal("show");
                        
                    }
                });
            }
        </script>
    </body>
</html>
</html>

