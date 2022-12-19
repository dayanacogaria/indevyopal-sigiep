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
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
if(!empty($_GET['m'])){
$rowc = $con->Listar("SELECT 
    ct.id_unico, 
    cat.id_unico, cat.codi_cuenta, cat.nombre, 
    cca.id_unico, cca.sigla, cca.nombre, cat.naturaleza  
FROM gf_configuracion_traslado ct  
LEFT JOIN gf_cuenta cat ON ct.cuenta_traslado = cat.id_unico 
LEFT JOIN gf_centro_costo cca ON ct.centro_costo = cca.id_unico 
WHERE cat.parametrizacionanno = $anno  ");
$nm     = $_GET['m'];
}
?>
<html>
    <head>
        <title>Traslado Centros Costo</title>
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
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Traslado Centros Costo</h2>
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
                                    
                                    echo '<input type="hidden" name="id_comprobante" id="id_comprobante" value="">';
                                    echo '<input type="hidden" name="nmes" id="nmes" value="'.$nm.'">';
                                    echo '<input type="hidden" name="nanno" id="nanno" value="'.$nanno.'">';
                                    echo '<input type="hidden" name="id"  id="id" value="'.$_GET['m'].'">';
                                    if($h==0){
                                        echo '<button id="guardar" type="button" class="btn btn-primary sombra" style=" background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-left:20px;" title="Guardar">
                                            <li class="glyphicon glyphicon-floppy-disk"></li>
                                        </button>';
                                    }
                                    echo '<div id="divbutton" style="display:none" ><button type="button" class="btn btn-primary sombra" style=" background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-left:20px;" onclick="javascript:ver()"><i title="ver" class="glyphicon glyphicon-list-alt"></i></button></div>';
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
                                        <td><strong>Cuenta a trasladar</strong></td>
                                        <td><strong>Centro de costo a trasladar</strong></td>
                                        <td><strong>Valor</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Cuenta a trasladar</th>
                                        <th>Centro de costo a trasladar</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $vtc =0;
                                        if(!empty($_GET['m'])){
                                        for ($i = 0; $i < count($rowc); $i++) {
                                            #* Buscar Valor
                                            $idc    = $rowc[$i][1];
                                            $idcc   = $rowc[$i][4];
                                            $nc     = $rowc[$i][7];
                                            $valor  = totalmovimientocc($idc, $idcc, $nm,$nanno, $anno, $nc); 
                                            if($valor !=0){ 
                                                
                                                $vtc +=$valor;
                                                echo '<tr>';
                                                echo '<td style="display: none;"></td>';
                                                echo '<td>'; 
                                                
                                                echo '</td>';
                                                echo '<td>'.$rowc[$i][2].' - '.ucwords($rowc[$i][3]).'</td>'; 
                                                echo '<td>'.$rowc[$i][5].' - '.ucwords($rowc[$i][6]).'</td>'; 
                                                
                                                echo '<td>'. number_format($valor,2,'.',',').'</td>'; 
                                                echo '</tr>';
                                            }
                                        }
                                    }?>
                                </tbody>
                            </table>      
                            <div style="text-align:right;margin-right: 50px;"> 
                                <label>Total: <?php echo number_format($vtc,2,'.',',')?></label>
                            </div>
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
                        <label id="mensajeEliminar" name="mensajeEliminar"></label>
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
                document.location = 'GF_TRASLADO_CC.php?m='+$("#mes").val();
            });
        </script>
        <script>
             $("#guardar").click(function(){
                var id = $("#id").val();
                jsShowWindowLoad('Validando Traslado...');
                var form_data ={id:id,action:12}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response);
                        if(response>0){
                            let id_tc = response;
                            let nanno = $("#nanno").val();
                            let nmes  = $("#nmes").val();
                            //Validar Si existe comprobante de amortización para el mes
                            jsShowWindowLoad('Validando Traslado...');
                            var form_data ={id_tc:id_tc,nanno:nanno,nmes:nmes,action:13}
                            $.ajax({
                                type: "POST",
                                url: "jsonPptal/gf_distribucion_costosJson.php",
                                data: form_data,
                                success: function(response)
                                { 
                                    jsRemoveWindowLoad();
                                    if(response>0){
                                        $("#mensajeEliminar").html('Ya existe un comprobante de traslado para este mes.<br/>¿Desea generarlo de nuevo?');
                                        $("#modalEliminar").modal("show");
                                        $("#btnAceptar").click(function(){
                                            eliminar(response, id_tc);
                                        })
                                    } else {
                                        guardar(id_tc);
                                    }
                                }
                            })
                            //generar(id,id_tc)
                        } else {
                            $("#mensaje").html('No hay comprobante para traslados');
                            $("#modalMensajes").modal("show");
                        }
                    }
                })
            })
        </script>
        <script>
            function guardar(tipo){
                let nanno = $("#nanno").val();
                let nmes  = $("#nmes").val();
                jsShowWindowLoad('Guardando Traslado...');
                var form_data ={id_tc:tipo,nanno:nanno,nmes:nmes,action:15}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response);
                        if(response>0){
                            $("#mensaje").html(response+' Detalles Guardados');
                            $("#modalMensajes").modal("show");
                        } else {
                            $("#mensaje").html('No se ha podido guardar el traslado');
                            $("#modalMensajes").modal("show");
                        }
                        $("#Aceptar").click(function(){
                            document.location.reload();
                        })
                        
                    }
                })
            }
            function eliminar(id, tipo){
                jsShowWindowLoad('Eliminando Traslado...');
                var form_data ={id:id,action:14}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(id);
                        console.log(response);
                        if(response>0){
                            guardar(tipo);
                        } else {
                            $("#mensaje").html('No se pudo eliminar el traslado');
                            $("#modalMensajes").modal("show");
                        }
                    }
                })
            }
        </script>
        <script>
            
            function ver(){
                //Vector de envio con mi variable
                let idCnt = $("#id_comprobante").val();
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
        <?php if(!empty($_GET['m'])){?>
        <script>
            $( document ).ready(function() {
                var id = $("#id").val();
                var form_data ={id:id,action:12}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        console.log(response);
                        if(response>0){
                            let id_tc = response;
                            let nanno = $("#nanno").val();
                            let nmes  = $("#nmes").val();
                            var form_data ={id_tc:id_tc,nanno:nanno,nmes:nmes,action:13}
                            $.ajax({
                                type: "POST",
                                url: "jsonPptal/gf_distribucion_costosJson.php",
                                data: form_data,
                                success: function(response)
                                { 
                                    jsRemoveWindowLoad();
                                    if(response>0){
                                        console.log('ac'+response);
                                        $("#divbutton").css("display", "inline-block");
                                        $("#id_comprobante").val(response);
                                    } else {
                                    }
                                }
                            })
                            //generar(id,id_tc)
                        }
                    }
                })
            })
        </script>
        <?php } ?>
    </body>
</html>
</html>

