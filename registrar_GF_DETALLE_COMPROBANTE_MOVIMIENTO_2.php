<?php
######## MODIFICACIONES ########
#31/01/2017 | 12:30 | ERICA GONZALEZ
#03/02/2017 | 05:04 | ALEXANDER NUMPAQUE
#Ínclusión de envio de valor a campo valor é inclusión de datapicker en el campo de fecha de vencimiento
?>
<style>
    #tabla1 table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    #tabla1 table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}
    #btnCerrarModalMov:hover {
        border: 1px solid #020324;         
    }
    
    #btnCerrarModalMov{
        box-shadow: 1px 1px 1px 1px #424852;
    }
</style>
<style>
.cabeza{
    white-space:nowrap;
    padding: 20px;
}
.campos{
    padding:-20px;
}
</style> 
<div class="modal fade mov" id="mdlDetalleMovimiento" role="dialog" align="center" aria-labelledby="mdlDetalleMovimiento" aria-hidden="true">
    <div class="modal-dialog" style="height:600px;width:900px">
        <div class="modal-content">
             <?php
            require_once './Conexion/conexion.php';
            session_start();
            $compania = $_SESSION['compania'];
            $idcomprobantedetalle = $_POST['id'];
            if(!empty($_POST['valor'])){
                if($_POST['valor']<0){
                    $valorD = substr($_POST['valor'],'1');
                }else{
                    $valorD = $_POST['valor'];    
                }
            }
            ?>
            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Movimiento Documento</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="client-form contenedorForma" style="margin-top:-10px;margin-right:-3px">
                            <form id="formDoc1" name="formDoc1" action="#" class="form-horizontal" style="margin-left:25px">
                                <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                    Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                                </p>
                                <input type="hidden" id="txtIdc" name="txtIdc" value="<?php echo $idcomprobantedetalle?>"/>
                                <div class="form-group form-inline" >                                    
                                    <label for="sltTipoDocumento" class="col-sm-2 control-label">
                                        <strong class="obligado">*</strong>Tipo Documento:
                                    </label>
                                    <select required="required" class="col-sm-2 input-sm" name="sltTipoDocumento" id="sltTipoDocumento" class="form-control" style="width:100px;height:30%" title="Seleccione tipo documento" required>
                                        <option value="">Tipo Documento</option>
                                        <?php 
                                        $sql10 = "SELECT id_unico,nombre FROM gf_tipo_documento WHERE compania = $compania";
                                        $result10 = $mysqli->query($sql10);
                                        while ($fila10 = mysqli_fetch_row($result10)){
                                            echo '<option value="'.$fila10[0].'">'.$fila10[1].'</option>';
                                        }
                                        ?>

                                    </select>
                                    <script> 
                                        $("#sltTipoDocumento").change(function() {
                                            var documento = document.getElementById('sltTipoDocumento').value;
                                            
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
                                                        
                                                        if(consecutivo ==1 || consecutivo =='1'){
                                                           var form_data={
                                                                case:13
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
                                                                    document.getElementById('txtNumeroDoc').value=numero;
                                                                }
                                                            });
                                                        } else {
                                                           document.getElementById('txtNumeroDoc').value=''; 
                                                        }
                                                    }
                                                });
                                            }
                                        });
                                                    </script>
                                        <script>
                                            $("#sltTipoDocumento").change(function(){
                                                var tipoD = document.getElementById('sltTipoDocumento').value;
                                                var form_data = {
                                                    existente:31,
                                                    tipoDocumento:tipoD
                                                };
                                                $.ajax({
                                                    type:'POST',
                                                    url:'consultasBasicas/consultarNumeros.php',
                                                    data:form_data,
                                                    success: function(data){
                                                        if(data==1){
                                                            $("#btnCheque").attr('disabled',false);
                                                            $("#btnCheque").click(function(){
                                                                window.open('formatoCheque.php?idDP=<?php echo md5($idcomprobantedetalle)?>&tipo='+tipoD);
                                                            });
                                                        }else{
                                                            $("#btnCheque").attr('disabled',true);
                                                        }
                                                    }
                                                });
                                            });                                            
                                        </script>
                                    <label for="txtNumeroDoc" class="col-sm-2 control-label">
                                        <strong class="obligado">*</strong>Número Documento:                                        
                                    </label>
                                    <input class="col-sm-2 input-sm" type="text" name="txtNumeroDoc" id="txtNumeroDoc" class="form-control"  style="width:100px;height:26px" title="Número de documento" placeholder="Nro. Documento" required>
                                    <label for="fecha" class="col-sm-2 control-label">
                                        <strong class="obligado">*</strong>Fecha Vencimiento:
                                    </label>                                
                                    <input class="col-sm-2 input-sm" value="" type="text" name="fechaM" id="fechaM" class="form-control" style="width:100px;height:26px" title="Ingrese la fecha" placeholder="Fecha" required>                                                                                                
                                </div>
                                <div class="form-group form-inline" style="margin-top:-15px">
                                    <label class="col-sm-2 control-label">
                                        <strong class="obligado">*</strong>Valor:                                        
                                    </label>
                                    <input class="col-sm-2 input-sm" type="text" name="txtValorMov" id="txtValorMov" class="form-control" onkeypress="return txtValida(event, 'num');" value="<?php echo $valorD; ?>" style="width:100px;height:26px;margin-bottom: -10px;" title="Ingrese valor de documento" placeholder="Valor" required>                                    
                                        <label for="txtlabel" class="col-sm-2 control-label">
                                        <strong class="obligado"></strong>Subir Documento:                                        
                                    </label>
                                        <input type="hidden" required="required" title="Seleccione Documento" id="archivos" name="archivos" required>
                                        <input type="file" id="file" name="file"   class="col-sm-2 input-sm" onchange="archivo()" style="display: inline; height: 35px;  width: 250px" >
                                        
                                        <a onclick="abrirDoc(<?php echo $idcomprobantedetalle;?>)" class="btn btn-primary sombra" style="margin-top:1px; margin-left:-42px"><li class="glyphicon glyphicon-folder-open"></li></a>
                                        <a onclick="return guardarDetalleMovimiento()" class="btn btn-primary sombra" style="margin-top:1px; margin-left:-90px"><li class="glyphicon glyphicon-floppy-disk"></li></a>
                                       <script type="text/javascript" >
                                            function abrirDoc(id){                                                                                                   
                                                var form_data={                            
                                                    id:id
                                                    };
                                                $.ajax({
                                                    type: 'POST',
                                                    url: "verDetalleDoc2.php#mdlDetalleMovimiento1",
                                                    data:form_data,
                                                    success: function (data) { 
                                                        
                                                        $(".mov1").modal('show');
                                                        
                                                    }
                                                });

                                            }                                                                                        
                                        </script>
                                        <a class="btn btn-primary sombra" id="btnCheque" style="margin-top:1px;" disabled>$</a>                                     
                                </div>
                                <div>
                                    <label id="labelError" name="labelError" style="margin-top: -12px; display: block; color: #155180;font-weight: normal; font-style: italic;"></label>
                                        
                                </div>                                   
                            </form>                            
                        </div>
                    </div>
                    <input type="hidden" id="idPrevio1" value="">
                    <input type="hidden" id="idActual1" value="">
                    <div class="col-sm-12" style="margin-top: 10px;margin-left: 4px;margin-right: 4px">                                                
                        <?php 
                        $totalValor = 0;
                        ?>
                        <div class="table-responsive contTabla" >
                            <table id="tabla1" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                                <thead style="position: relative;overflow: auto;width: 100%;">
                                    <tr>
                                        <td class="oculto">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Tipo Documento</strong></td>
                                        <td class="cabeza"><strong>Numero</strong></td>
                                        <td class="cabeza"><strong>Fecha Vencimiento</strong></td>
                                        <td class="cabeza"><strong>Valor</strong></td>
                                        <td class="cabeza"><strong>Documento</strong></td>
                                        <td class="cabeza" width="3%"><strong>Impresión cheque</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="oculto">Identificador</th>
                                        <th width="7%" class="cabeza"></th>
                                        <th class="cabeza">Tipo Documento</th>
                                        <th class="cabeza">Numero</th>
                                        <th class="cabeza">Fecha Vencimiento</th>
                                        <th class="cabeza">Valor</th>
                                        <th class="cabeza">Documento</th>
                                        <th class="cabeza">Impresión cheque</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php                                                                           
                                        $sql11 = "SELECT dtm.id_unico,
                                                dtm.tipodocumento,
                                                tpd.id_unico,
                                                tpd.nombre,
                                                dtm.numero,
                                                dtm.fechavencimiento,
                                                dtm.valor, dtm.ruta 
                                         FROM gf_detalle_comprobante_mov dtm
                                         LEFT JOIN gf_tipo_documento tpd ON dtm.tipodocumento = tpd.id_unico
                                         WHERE dtm.comprobantepptal=$idcomprobantedetalle";

                                        $result11 = $mysqli->query($sql11);
                                    while($row11 = $result11->fetch_row()){ ?>
                                    <tr>
                                
                                        <td class="campos oculto">
                                            <?php echo $row11[0]; ?>
                                        </td>
                                
                                        <td class="campos">
                                            <a href="#<?php echo $row11[0];?>" onclick="javascript:eliminarDetalleComprobanteMov(<?php echo $row11[0].','."'".$row11[7]."'"; ?>)" title="Eliminar">
                                                <li class="glyphicon glyphicon-trash"></li>
                                            </a>
                                            <a href="#<?php echo $row11[0];?>" title="Modificar" id="mod" onclick="javascript:modificarDetalleMov(<?php echo $row11[0]; ?>);javascript:cargarFecha(<?php echo $row11[0]; ?>);javascript:crearNuevoMov(<?php echo $row11[0]; ?>)">
                                                <li class="glyphicon glyphicon-edit"></li>
                                            </a>                                            
                                        </td>
                                        <td class="campos">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lbltipodocumento'.$row11[0].'">'.(ucwords(strtolower($row11[3]))).'</label>'; ?>
                                            <select style="display: none;padding:2px;height:19px" class="col-sm-12 campoD" id="slttipodocumento<?php echo $row11[0]; ?>">
                                                <?php
                                                echo '<option value="'.$row11[2].'">'.(ucwords(strtolower($row11[3]))).'</option>';
                                                $sql12 = "SELECT id_unico,nombre FROM gf_tipo_documento WHERE id_unico!=$row11[2] AND compania = $compania";                                            
                                                $result12 = $mysqli->query($sql12);
                                                while ($row12=mysqli_fetch_row($result12)){
                                                    echo '<option value="'.$row12[0].'">'.ucwords(strtolower($row12[1])).'</option>';
                                                }

                                                ?>                                            
                                            </select>
                                            
                                    <script> 
                                        $("#slttipodocumento<?php echo $row11[0];?>").change(function() {
                                            var documento = document.getElementById('slttipodocumento<?php echo $row11[0]; ?>').value;
                                            
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
                                                        var consecutivo = resultado[3];
                                                        
                                                        if(consecutivo ==1 || consecutivo =='1'){
                                                           var form_data={
                                                                case:13
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
                                                                    document.getElementById("txtnumerodocumento<?php echo $row11[0]?>").value=numero;
                                                                }
                                                            });
                                                        } else {
                                                           document.getElementById("txtnumerodocumento<?php echo $row11[0]?>").value=''; 
                                                        }
                                                    }
                                                });
                                            }
                                        });
                                                    </script>
                                        </td>
                                        <td class="campos">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblnumerodocumento'.$row11[0].'">'.(ucwords(strtolower($row11[4]))).'</label>'; 
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD" type="text" name="txtnumerodocumento'.$row11[0].'" id="txtnumerodocumento'.$row11[0].'" value="'.$row11[4].'" />';
                                            ?>

                                        </td>
                                        <td class="campos">
                                            <?php 
                                                $valorF = (String) $row11[5];$fechaS = explode("-",$valorF); 
                                                  echo '<label class="valorLabel" style="font-weight:normal" id="lblfechamovimiento'.$row11[0].'">'.(ucwords(strtolower($fechaS[2].'/'.$fechaS[1].'/'.$fechaS[0]))).'</label>'; 
                                                  echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD" type="text" name="txtfechamovimiento'.$row11[0].'" id="txtfechamovimiento'.$row11[0].'" value="'.$fechaS[2].'/'.$fechaS[1].'/'.$fechaS[0].'" />';
                                            ?>
                                        </td>
                                        <td class="campos text-right">
                                            <?php 
                                            $totalValor += $row11[6];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="lblvalormovimiento'.$row11[0].'">'.(ucwords(strtolower($row11[6]))).'</label>';
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD" type="text" name="txtvalormovimiento'.$row11[0].'" id="txtvalormovimiento'.$row11[0].'" value="'.$row11[6].'" />';
                                            ?> 
                                            
                                        </td>
                                        <td class="campos text-center">
                                            <div id="docD<?php echo $row11[0]?>" name="docD<?php echo $row11[0]?>" style="display:inline">
                                                <?php if(empty($row11[7])) { echo '<i>No hay documento</i>'; } else { ?>
                                                <a href="<?php echo $row11[7];?>">
                                                    <i title="Ver" class="glyphicon glyphicon-search"></i>
                                                </a>
                                                <?php } ?>
                                            </div>
                                            <div id="docS<?php echo $row11[0]?>" name="docS<?php echo $row11[0]?>" style="display:none">
                                                <form id="formDoc<?php echo $row11[0]?>" name="formDoc<?php echo $row11[0]?>" method="POST">
                                                    <input type="hidden" name="archivoMod<?php echo $row11[0]?>"  id="archivoMod<?php echo $row11[0]?>">  
                                                <?php echo '<input maxlength="50" align="center" style="padding:2px;height:20px;" class="col-sm-9 text-left campoD" type="file" name="file" id="file" onchange="archivoMod('.$row11[0].')"/>';?>
                                                </form>
                                            </div>
                                            <div class="col-sm-1">
                                                <table id="tabModalDetalleMov<?php echo $row11[0] ?>" style="padding:0px;background-color:transparent;background:transparent;">
                                                    <tbody>
                                                        <tr style="background-color:transparent;">
                                                            <td style="background-color:transparent;">
                                                                <a  href="#<?php echo $row11[0];?>" title="Guardar" id="guardarModalDetalleMov<?php echo $row11[0]; ?>" style="display: none;" onclick="javascript:guardarCambiosDetalleMovimiento(<?php echo $row11[0]; ?>)">
                                                                    <li class="glyphicon glyphicon-floppy-disk"></li>
                                                                </a>
                                                            </td>
                                                            <td style="background-color:transparent;">
                                                                <a href="#<?php echo $row11[0];?>" title="Cancelar" id="cancelarModalDetalleMov<?php echo $row11[0] ?>" style="display: none" onclick="javascript:cancelarDetalleMovimiento(<?php echo $row11[0];?>)" >
                                                                    <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                        <td class="campos text-center">
                                            <?php 
                                            $sqlTP = "SELECT fto.esCheque
                                                    FROM gf_tipo_documento tdoc 
                                                    LEFT JOIN gf_formato fto ON tdoc.formato = fto.id_unico
                                                    WHERE tdoc.id_unico = $row11[2]";
                                            $resultTP = $mysqli->query($sqlTP);
                                            $esC = mysqli_fetch_row($resultTP);
                                            switch ($esC[0]) {
                                                case 1:
                                                    echo "<a  onclick=\"esCheque(".$row11[2].")\" style=\"cursor:pointer\" class=\"glyphicon glyphicon-print\"></a>";
                                                    break;

                                                case 2:
                                                    echo "<a class=\"glyphicon glyphicon-print disabled\" disabled></a>";
                                                    break;

                                                default:
                                                    echo "<a class=\"glyphicon glyphicon-print disabled\" disabled></a>";
                                                    break;
                                            }
                                             ?>                                            
                                            <script type="text/javascript">
                                                function esCheque(tipo){
                                                    window.open('formatoCheque.php?idDP=<?php echo md5($idcomprobantedetalle)?>&tipo='+tipo);
                                                }
                                            </script>
                                        </td>
                                    </tr>
                                    <?php    
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>                                                                        
                    </div>                    
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">                
            </div>
        </div>
    </div>
</div>
<script>
function justNumbers(e){   
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
        return true;
        return /\d/.test(String.fromCharCode(keynum));
    }
</script>
<div class="modal fade" id="mdlPEliminiado" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnEliminar1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" id="btnCancelar1"style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalTipo" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Seleccione un tipo de documento</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ok" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalNumero" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Escriba un número de documento</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ok" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalValor" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Escriba un valor de documento</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ok" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDocumento" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Documento inválido</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ok" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
 <div class="modal fade" id="mdlDocumentoInvalido" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Documento inválido.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnDocInvalido" class="btn" style="" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script>
  function archivo(){
       var formData = new FormData($("#formDoc1")[0]);  
     
     
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
                var doc = document.getElementById('archivos');
                if(valor ==1){
                    doc.value='1';
                } else {
                    if(valor==2){
                        doc.value='2';
                    } else {
                       doc.value='3';
                    }
                }
            }
        });
}          
</script>
<script>
  function archivoMod(id){
       var formData = new FormData($("#formDoc"+id)[0]);
       var archivo = 'archivoMod'+id;
            
           
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
                
                if(valor ==1){
                     $("#"+archivo).val('1');
                } else {
                    if(valor==2){
                      $("#"+archivo).val('2');
                    } else {
                      $("#"+archivo).val('3');
                    }
                }
            }
        });
}          
</script>
<script type="text/javascript" >
    function guardarDetalleMovimiento(){
        
        var id = document.getElementById('txtIdc').value;
        var td = document.getElementById('sltTipoDocumento').value;
        var num = document.getElementById('txtNumeroDoc').value;
        var fecha = document.getElementById('fechaM').value;
        var valor = document.getElementById('txtValorMov').value;
        var doc = document.getElementById('archivos').value;
        if(doc=='' || doc.length<0 ) { 
            if(td.length>0 && num.length>0 && fecha.length>0 && valor.length>0){
            
                var form_data = new FormData($("#formDoc1")[0]);
                var result = '';
                $.ajax({
                    type: 'POST',
                    url: "json/registrarDetalleComprobanteMovimientoJson_2.php",
                    data:form_data,
                    contentType: false,
                     processData: false,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                            $("#mdlDetalleMovimiento").modal('hide');
                            $("#guardado").modal('show');
                        }else{
                            $("#noguardado").modal('show');
                            $("#mdlDetalleMovimiento").modal('hide');
                        }
                    }
                }); 
            } else {
                if(td==''||td.length<0){
                    $("#modalTipo").modal('show');

                }else {
                    if(num=='' || num.length<0){
                        $("#modalNumero").modal('show');
                    }else {
                        if(valor=='' || valor.length<0){
                            $("#modalValor").modal('show');
                        }else {
                            if(doc=='' || doc.length<0){
                                $("#modalDocumento").modal('show');
                            }
                        }
                    }
                }
            }
        } else { 
            if(td.length>0 && num.length>0 && fecha.length>0 && valor.length>0 && doc=='1'){

                var form_data = new FormData($("#formDoc1")[0]);
                var result = '';
                $.ajax({
                    type: 'POST',
                    url: "json/registrarDetalleComprobanteMovimientoJson_2.php",
                    data:form_data,
                    contentType: false,
                     processData: false,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                            $("#mdlDetalleMovimiento").modal('hide');
                            $("#guardado").modal('show');
                        }else{
                            $("#noguardado").modal('show');
                            $("#mdlDetalleMovimiento").modal('hide');
                        }
                    }
                }); 
            } else {
                if(td==''||td.length<0){
                    $("#modalTipo").modal('show');

                }else {
                    if(num=='' || num.length<0){
                        $("#modalNumero").modal('show');
                    }else {
                        if(valor=='' || valor.length<0){
                            $("#modalValor").modal('show');
                        }else {
                            if(doc=='' || doc.length<0){
                                $("#modalDocumento").modal('show');
                            }
                        }
                    }
                }
            }
        }
    }
    function modificarDetalleMov(id){
        if(($("#idPrevio1").val() != 0)||($("#idPrevio1").val() != "")){
            var lblTipoDocumentoC = 'lbltipodocumento'+$("#idPrevio1").val();
            var sltTipoDocumentoC = 'slttipodocumento'+$("#idPrevio1").val();
            var lblNumeroDocumentoC = 'lblnumerodocumento'+$("#idPrevio1").val();
            var txtNumeroDocumentoC = 'txtnumerodocumento'+$("#idPrevio1").val();
            var lblFechaMovimientoC = 'lblfechamovimiento'+$("#idPrevio1").val();
            var txtFechaMovimientoC = 'txtfechamovimiento'+$("#idPrevio1").val();
            var lblValorMovimientoC = 'lblvalormovimiento'+$("#idPrevio1").val();
            var txtValorMovimientoC = 'txtvalormovimiento'+$("#idPrevio1").val();
            var guardarCD = 'guardarModalDetalleMov'+$("#idPrevio1").val();
            var cancelarCD = 'cancelarModalDetalleMov'+$("#idPrevio1").val();
            var tablaCD = 'tabModalDetalleMov'+$("#idPrevio1").val();
            var docD = 'docD'+$("#idPrevio1").val();
            var docS = 'docS'+$("#idPrevio1").val();
            
            $("#"+lblTipoDocumentoC).css('display','block');
            $("#"+sltTipoDocumentoC).css('display','none');                               
            $("#"+lblNumeroDocumentoC).css('display','block');
            $("#"+txtNumeroDocumentoC).css('display','none');                               
            $("#"+lblFechaMovimientoC).css('display','block');
            $("#"+txtFechaMovimientoC).css('display','none');                               
            $("#"+lblValorMovimientoC).css('display','block');
            $("#"+txtValorMovimientoC).css('display','none');                               
            $("#"+guardarCD).css('display','none');
            $("#"+cancelarCD).css('display','none');
            $("#"+tablaCD).css('display','none');
            $("#"+docD).css('display','block');
            $("#"+docS).css('display','none');
        }
        
        var lblTipoDocumento = 'lbltipodocumento'+id;
        var sltTipoDocumento = 'slttipodocumento'+id;
        var lblNumeroDocumento = 'lblnumerodocumento'+id;
        var txtNumeroDocumento = 'txtnumerodocumento'+id;
        var lblFechaMovimiento = 'lblfechamovimiento'+id;
        var txtFechaMovimiento = 'txtfechamovimiento'+id;
        var lblValorMovimiento = 'lblvalormovimiento'+id;
        var txtValorMovimiento = 'txtvalormovimiento'+id;
        var guardar = 'guardarModalDetalleMov'+id;
        var cancelar = 'cancelarModalDetalleMov'+id;
        var tabla = 'tabModalDetalleMov'+id;
        var docD = 'docD'+id;
        var docS = 'docS'+id;
        
        $("#"+lblTipoDocumento).css('display','none');
        $("#"+sltTipoDocumento).css('display','block');                               
        $("#"+lblNumeroDocumento).css('display','none');
        $("#"+txtNumeroDocumento).css('display','block');                               
        $("#"+lblFechaMovimiento).css('display','none');
        $("#"+txtFechaMovimiento).css('display','block');                               
        $("#"+lblValorMovimiento).css('display','none');
        $("#"+txtValorMovimiento).css('display','block');                               
        $("#"+guardar).css('display','block');
        $("#"+cancelar).css('display','block');
        $("#"+tabla).css('display','block');
        $("#"+docD).css('display','none');
        $("#"+docS).css('display','block');
        
        $("#idActual1").val(id);
        if($("#idPrevio1").val() != id){
            $("#idPrevio1").val(id);   
        }
    }
    
    function cancelarDetalleMovimiento(id){
        var lblTipoDocumento = 'lbltipodocumento'+id;
        var sltTipoDocumento = 'slttipodocumento'+id;
        var lblNumeroDocumento = 'lblnumerodocumento'+id;
        var txtNumeroDocumento = 'txtnumerodocumento'+id;
        var lblFechaMovimiento = 'lblfechamovimiento'+id;
        var txtFechaMovimiento = 'txtfechamovimiento'+id;
        var lblValorMovimiento = 'lblvalormovimiento'+id;
        var txtValorMovimiento = 'txtvalormovimiento'+id;
        var guardar = 'guardarModalDetalleMov'+id;
        var cancelar = 'cancelarModalDetalleMov'+id;
        var tabla = 'tabModalDetalleMov'+id;
        var docD = 'docD'+id;
        var docS = 'docS'+id;
        
        $("#"+lblTipoDocumento).css('display','block');
        $("#"+sltTipoDocumento).css('display','none');                               
        $("#"+lblNumeroDocumento).css('display','block');
        $("#"+txtNumeroDocumento).css('display','none');                               
        $("#"+lblFechaMovimiento).css('display','block');
        $("#"+txtFechaMovimiento).css('display','none');                               
        $("#"+lblValorMovimiento).css('display','block');
        $("#"+txtValorMovimiento).css('display','none');                               
        $("#"+guardar).css('display','none');
        $("#"+cancelar).css('display','none');
        $("#"+tabla).css('display','none');
        $("#"+docD).css('display','block');
        $("#"+docS).css('display','none');
        
    }
    
    function guardarCambiosDetalleMovimiento(id){   
           
            var sltTipoDocumento = 'slttipodocumento'+id;        
            var txtNumeroDocumento = 'txtnumerodocumento'+id;        
            var txtFechaMovimiento = 'txtfechamovimiento'+id;        
            var txtValorMovimiento = 'txtvalormovimiento'+id;
            var archivo = 'archivoMod'+id;
            
            var td = $("#"+sltTipoDocumento).val();
            var num = $("#"+txtNumeroDocumento).val();
            var fecha = $("#"+txtFechaMovimiento).val();
            var valor = $("#"+txtValorMovimiento).val();
            var archivos = $("#"+archivo).val();
        
        if(td.length>0 && num.length>0 && fecha.length>0 && valor.length>0 && archivos=='1'){
                    
            var form_data = new FormData($("#formDoc"+id)[0]);
            form_data.append("tipoDocumento",$("#"+sltTipoDocumento).val());
            form_data.append("id",id);
            form_data.append("numeroDocumento",$("#"+txtNumeroDocumento).val());
            form_data.append("fechaVencimiento",$("#"+txtFechaMovimiento).val());
            form_data.append("valorMovimiento",$("#"+txtValorMovimiento).val());



            var result = '';
            $.ajax({
                type: 'POST',
                url: "json/modificarDetalleComprobanteMovimientoJson.php",
                data:form_data,
                contentType: false,
                 processData: false,
                success: function (data) {
                    result = JSON.parse(data);
                    if(result==true){
                        $("#mdlDetalleMovimiento").modal('hide');
                        $("#infoM").modal('show');
                    }else{
                        $("#mdlDetalleMovimiento").modal('hide');
                        $("#noModifico").modal('show');
                    }
                }
            });
        } else {
            if(archivos =='2'){
                $("#mdlDocumentoInvalido").modal('show');
            } else { 
            if(td.length>0 && num.length>0 && fecha.length>0 && valor.length>0){
            var form_data = new FormData();
            form_data.append("tipoDocumento",$("#"+sltTipoDocumento).val());
            form_data.append("id",id);
            form_data.append("numeroDocumento",$("#"+txtNumeroDocumento).val());
            form_data.append("fechaVencimiento",$("#"+txtFechaMovimiento).val());
            form_data.append("valorMovimiento",$("#"+txtValorMovimiento).val());



            var result = '';
            $.ajax({
                type: 'POST',
                url: "json/modificarDetalleComprobanteMovimientoJson.php",
                data:form_data,
                contentType: false,
                 processData: false,
                success: function (data) {
                    result = JSON.parse(data);
                    if(result==true){
                        $("#mdlDetalleMovimiento").modal('hide');
                        $("#infoM").modal('show');
                    }else{
                        $("#mdlDetalleMovimiento").modal('hide');
                        $("#noModifico").modal('show');
                    }
                }
            });
        }
            }
    }
    }
   </script>
   <script>
   function eliminarDetalleComprobanteMov(id, ruta){
        var result='';
        $("#mdlPEliminiado").modal('show');
        $("#btnEliminar1").click(function(){
            $("#mdlPEliminiado").modal('hide');
            $.ajax({
                type: 'GET',
                url: "json/eliminarDetalleComprobanteMovimientoJson.php?id="+id+"&ruta="+ruta,                
                success: function (data) {
                    result = JSON.parse(data);
                    
                    if(result==true){
                         $("#mdlDetalleMovimiento").modal('hide');
                       $("#myModal1").modal('show');
                    }else{
                        $("#myModal2").modal('show');
                    }
                }
            });
        });
        
    }
   </script>
   <script>
    function cargarFecha(id){
        var txtFechaMovimiento = 'txtfechamovimiento'+id;
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        //var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#"+txtFechaMovimiento).datepicker({changeMonth: true}).val(); 
    }
</script>
<script type="text/javascript" >
    function crearNuevoMov(id){
        $("#slttipodocumento"+id).change(function(){
            if($("#slttipodocumento"+id).val()==0 || $("#slttipodocumento"+id).val()=='""'){
                $("#txtnumerodocumento"+id).val('');
            }else{
                 var form_data = {
                     nuevos:4,
                    tipoDocumento:$("#slttipodocumento"+id).val()
                };

                $.ajax({
                    type: 'POST',
                    url: "consultasBasicas/generarNuevos.php",
                    data: form_data,
                    success: function (data) {
                        $("#txtnumerodocumento"+id).val(data);
                    }
                });
            }                                               
        }); 
    }
 </script>

 <script type="text/javascript" >
  $("#mdlDetalleMovimiento").draggable({
      handle: ".modal-header"
  });
</script>
<script type="text/javascript" >
    $("#btnCerrarModalMov").click(function(){
        document.location.reload();
    });
    $("#btnCancelar1").click(function(){
        document.location.reload();
    });
    $("#btnDocInvalido").click(function(){
        $("#mdlDocumentoInvalido").modal('hide');
        document.location.reload();
    });
    
    $("#mdlDetalleMovimiento").on('shown.bs.modal',function(){
        var dataTable = $("#tabla1").DataTable();
        dataTable.columns.adjust().responsive.recalc();
    });
</script>
<script type="text/javascript">
  $(document).ready(function() {
     var i= 1;
    $('#tabla1 thead th').each( function () {
        if(i != 1){ 
        var title = $(this).text();
        switch (i){
          case 3:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 4:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 5:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 6:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 6:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 7:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 8:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 9:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 10:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 11:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 12:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 13:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 14:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 15:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 16:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 17:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 18:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 19:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
  
        }
        i = i+1;
      }else{
        i = i+1;
      }
    } );
 
    // DataTable
   var table = $('#tabla1').DataTable({
      "autoFill": true,
      "scrollX": true,
      "pageLength": 5,
        "language": {
          "lengthMenu": "Mostrar _MENU_ registros",
          "zeroRecords": "No Existen Registros...",
          "info": "Página _PAGE_ de _PAGES_ ",
          "infoEmpty": "No existen datos",
          "infoFiltered": "(Filtrado de _MAX_ registros)",
          "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
        },
        'columnDefs': [{
         'targets': 0,
         'searchable':false,
         'orderable':false,
         'className': 'dt-body-center'         
      }]
   });

    var i = 0;
    table.columns().every( function () {
        var that = this;
        if(i!=0){
        $( 'input', this.header() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
        i = i+1;
      }else{
        i = i+1;
      }
    } );
} );
</script>
<script type="text/javascript">
    /*Función para ejecutar el datapicker en en el campo fecha*/
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
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fechaM").datepicker({changeMonth: true}).val(fecAct);            
    });
    </script>
<?php require_once './verDetalleDoc2.php'; ?>