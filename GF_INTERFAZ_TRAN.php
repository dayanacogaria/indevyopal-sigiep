<?php 
##############################################################################################################################
#                                                                                                                                 MODIFICACIONES
##############################################################################################################################                                                                                                           
#17/11/2017 |Erica G. |Archivo Creado
##############################################################################################################################
require_once('Conexion/ConexionPDO.php');
require_once 'head_listar.php'; 
$anno     = $_SESSION['anno'];
$compania =$_SESSION['compania'];
$conex = new ConexionPDO();
#********************Consulta el parametro de dígitos Código********************
$con = $conex->Listar("SELECT id_unico, nombre, valor   
                FROM gs_parametros_basicos 
                WHERE nombre='Dígitos Interfaz Inventario'");
$dig = $con[0][2];
#********************Consulta plan Inventario********************

$planid='';

if(isset($_GET['grupo'])){
    $cplan = $conex->Listar("SELECT *  
                FROM gf_plan_inventario  
                WHERE LENGTH(codi)='".$dig."' AND md5(id_unico) !='".$_GET['grupo']."' AND compania = $compania ORDER BY codi ASC");
    $planid = $conex->Listar("SELECT *  
                FROM gf_plan_inventario  
                WHERE md5(id_unico) ='".$_GET['grupo']."' AND compania = $compania  ");
    #********************Tipo Movimient********************#
    $ttmov  = $conex->Listar("SELECT *  
                FROM gf_tipo_movimiento   
                WHERE compania = $compania AND id_unico NOT IN (SELECT tipo_movimiento FROM gf_configuracion_almacen WHERE md5(plan_inventario) ='".$_GET['grupo']."' AND parametrizacion_anno = $anno) ORDER BY sigla");
    
} else {
    $cplan = $conex->Listar("SELECT *  
                FROM gf_plan_inventario  
                WHERE LENGTH(codi)='".$dig."' AND compania = $compania ORDER BY codi ASC");
}
$ttmov2 = $conex->Listar("SELECT *  
                FROM gf_tipo_movimiento AND compania = $compania 
                ORDER BY sigla ASC");
#********************Cuenta********************#
$ccuenta = $conex->Listar("SELECT *  
                FROM gf_cuenta   
                WHERE parametrizacionanno = $anno AND activa = 1 
                AND (movimiento = 1 OR centrocosto = 1 OR auxiliarproyecto = 1 OR auxiliartercero  = 1) 
                ORDER BY codi_cuenta ASC");

#********************#********************#
$cons="";
if(isset($_GET['interfaz'])){ 
    if(isset($_GET['grupo'])){
        if($_GET['interfaz']==1){
            $cons = $conex->Listar("SELECT ca.id_unico, 
                CONCAT_WS(' - ',UPPER(tm.sigla),LOWER(tm.nombre)),
                CONCAT_WS(' - ',cd.codi_cuenta,LOWER(cd.nombre)),
                CONCAT_WS(' - ',cc.codi_cuenta,LOWER(cc.nombre)), 
                tm.id_unico, cd.id_unico, cc.id_unico 
            FROM gf_configuracion_almacen ca 
            LEFT JOIN 
                gf_tipo_movimiento tm ON ca.tipo_movimiento=tm.id_unico
            LEFT JOIN 
                gf_cuenta cd ON ca.cuenta_debito = cd.id_unico 
            LEFT JOIN 
                gf_cuenta cc ON ca.cuenta_credito = cc.id_unico 
            WHERE 
                md5(ca.plan_inventario) = '".$_GET['grupo']."' 
                AND cuenta_baja IS NULL 
                AND ca.parametrizacion_anno =$anno");
        }elseif($_GET['interfaz']==2){
            $cons = $conex->Listar("SELECT ca.id_unico, 
                CONCAT_WS(' - ',cd.codi_cuenta,LOWER(cd.nombre)),
                CONCAT_WS(' - ',cc.codi_cuenta,LOWER(cc.nombre)),
                CONCAT_WS(' - ',cb.codi_cuenta,LOWER(cb.nombre)), 
                cb.id_unico, cd.id_unico, cc.id_unico  
            FROM gf_configuracion_almacen ca 
            LEFT JOIN 
                gf_cuenta cd ON ca.cuenta_debito = cd.id_unico 
            LEFT JOIN 
                gf_cuenta cc ON ca.cuenta_credito = cc.id_unico 
            LEFT JOIN 
                gf_cuenta cb ON ca.cuenta_baja = cb.id_unico 
            WHERE 
                md5(ca.plan_inventario) = '".$_GET['grupo']."' 
                AND tipo_movimiento IS NULL 
                AND ca.parametrizacion_anno =$anno");
        }
        
    }
}

?>
<title>Configuración</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="js/md5.pack.js"></script>

</head>
<body>
    <div class="container-fluid text-center"  >
        <div class="row content">
            <?php require_once 'menu.php'; ?> 
            <!-- Localización de los botones de información a la derecha. -->
            <div class="col-sm-10" style="margin-left: -10px;margin-top: 5px" >
                <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Configuración</h2>
                <div class="col-sm-12">
                    <div class="client-form contenedorForma"  style=""> 
                       
                        <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                        <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                                    <div class="col-sm-4" align="left">  
                                        <label for="tipoI" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Interfaz:</label><br>
                                        <select name="tipoI" id="tipoI" class="select2_single form-control input-sm" title="Seleccione Tipo Interfaz" style="width:250px; " required>
                                        <?php if(isset($_GET['interfaz'])) { 
                                            if($_GET['interfaz']==1){
                                               echo '<option value="1">Transacción</option>';
                                               echo '<option value="2">Depreciación</option>';         
                                            } elseif($_GET['interfaz']==2){
                                                echo '<option value="2">Depreciación</option>';
                                                echo '<option value="1">Transacción</option>';
                                            }
                                         } else { ?>
                                            <option >Tipo Interfaz</option>
                                            <option value="1">Transacción</option>
                                            <option value="2">Depreciación</option>
                                         <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <script>
                                    $("#tipoI").change(function(){
                                        document.location = ('GF_INTERFAZ_TRAN.php?interfaz='+$("#tipoI").val());  
                                    })
                                </script>
                                <?php if(isset($_GET['interfaz'])){
                                        if(isset($_GET['grupo'])){ ?>
                                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                                                <div class="col-sm-4" align="left">  
                                                    <label for="grupo" class="control-label" ><strong style="color:#03C1FB;">*</strong>Grupo:</label><br>
                                                    <select name="grupo" id="grupo" class="select2_single form-control input-sm" title="Seleccione Grupo" style="width:250px; " required>
                                                        <?php if(isset($_GET['grupo'])){ 
                                                            for($i=0; $i<count($planid); $i++){
                                                                echo '<option value="'.$planid[$i][0].'">'.$planid[$i][2].' - '.ucwords(mb_strtolower($planid[$i][1])). '</option>';
                                                            }
                                                        } else { ?>
                                                        <option value="">Grupo</option>
                                                        <?php }
                                                        for($i=0;$i<count($cplan); $i++){
                                                            echo '<option value="'.$cplan[$i][0].'">'.$cplan[$i][2].' - '.ucwords(mb_strtolower($cplan[$i][1])). '</option>';
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <script>
                                                $("#grupo").change(function(){
                                                    document.location = ('GF_INTERFAZ_TRAN.php?interfaz='+$("#tipoI").val()+'&grupo='+md5($("#grupo").val()));  
                                                })
                                            </script>
                                            <?php if($_GET['interfaz']==1) { ?>
                                                <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                                    <div class="col-sm-4" align="left" >  
                                                        <label for="tipomov" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Movimiento:</label><br>
                                                        <select name="tipomov" id="tipomov" class="select2_single form-control input-sm" title="Seleccione Tipo Movimiento" style="width:250px; " required>
                                                            <option value="">Tipo Movimiento</option>
                                                            <?php 
                                                            for($i=0;$i<count($ttmov); $i++){
                                                                echo '<option value="'.$ttmov[$i][0].'">'.mb_strtoupper($ttmov[$i][1]).' - '.ucwords(mb_strtolower($ttmov[$i][2])). '</option>';
                                                            }?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                                        <label for="ctadeb" class="control-label" ><strong style="color:#03C1FB;">*</strong>Cuenta Débito:</label><br>
                                                        <select name="ctadeb" id="ctadeb" class="select2_single form-control input-sm" title="Seleccione Cuenta Débito" style="width:250px; " required>
                                                            <option value="">Cuenta Débito</option>
                                                            <?php 
                                                            for($i=0;$i<count($ccuenta); $i++){
                                                                echo '<option value="'.$ccuenta[$i][0].'">'.$ccuenta[$i][1].' - '.ucwords(mb_strtolower($ccuenta[$i][2])). '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-4" align="left">  
                                                        <label for="ctacred" class="control-label" ><strong style="color:#03C1FB;">*</strong>Cuenta Crédito:</label><br>
                                                        <select name="ctacred" id="ctacred" class="select2_single form-control input-sm" title="Seleccione Cuenta Crédito" style="width:250px; " required>
                                                            <option value="">Cuenta Crédito</option>
                                                            <?php 
                                                            for($i=0;$i<count($ccuenta); $i++){
                                                                echo '<option value="'.$ccuenta[$i][0].'">'.$ccuenta[$i][1].' - '.ucwords(mb_strtolower($ccuenta[$i][2])). '</option>';
                                                            }?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                                    <div class="col-sm-12 " align="right"  style="margin-top: -20px; margin-left: -20px;">
                                                        <button type="button" onclick="guardar1()"  id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px; margin-bottom: 0px; margin-left: -10px;" title="Guardar"><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->
                                                        <input type="hidden" name="MM_insert" >
                                                    </div>
                                                </div>
                                            <?php }elseif($_GET['interfaz']==2) { 
                                                if(count($cons)<1) { ?>
                                                <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                                    <div class="col-sm-4" align="left">  
                                                        <label for="ctadeb" class="control-label" ><strong style="color:#03C1FB;">*</strong>Cuenta Débito:</label><br>
                                                        <select name="ctadeb" id="ctadeb" class="select2_single form-control input-sm" title="Seleccione Cuenta Débito" style="width:250px; " required>
                                                            <option value="">Cuenta Débito</option>
                                                            <?php
                                                            for($i=0;$i<count($ccuenta); $i++){
                                                                echo '<option value="'.$ccuenta[$i][0].'">'.$ccuenta[$i][1].' - '.ucwords(mb_strtolower($ccuenta[$i][2])). '</option>';
                                                            }?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-4" align="left">  
                                                        <label for="ctacred" class="control-label" ><strong style="color:#03C1FB;">*</strong>Cuenta Crédito:</label><br>
                                                        <select name="ctacred" id="ctacred" class="select2_single form-control input-sm" title="Seleccione Cuenta Crédito" style="width:250px; " required>
                                                            <option value="">Cuenta Crédito</option>
                                                            <?php 
                                                            for($i=0;$i<count($ccuenta); $i++){
                                                                echo '<option value="'.$ccuenta[$i][0].'">'.$ccuenta[$i][1].' - '.ucwords(mb_strtolower($ccuenta[$i][2])). '</option>';
                                                            }?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-4" align="left">  
                                                        <label for="ctabaja" class="control-label" ><strong style="color:#03C1FB;"></strong>Cuenta Baja:</label><br>
                                                        <select name="ctabaja" id="ctabaja" class="select2_single form-control input-sm" title="Seleccione Cuenta Baja" style="width:250px; " >
                                                            <option value="">Cuenta Baja</option>
                                                            <?php 
                                                            for($i=0;$i<count($ccuenta); $i++){
                                                                echo '<option value="'.$ccuenta[$i][0].'">'.$ccuenta[$i][1].' - '.ucwords(mb_strtolower($ccuenta[$i][2])). '</option>';
                                                            }?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                                    <div class="col-sm-12 " align="right"  style="margin-top: -20px; margin-left: -20px;">
                                                        <button type="button" onclick="guardar2()"  id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px; margin-bottom: 0px; margin-left: -10px;" title="Guardar"><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->
                                                        <input type="hidden" name="MM_insert" >
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                                    <div class="col-sm-12 " align="right"  style="margin-top: -20px; margin-left: -20px;">
                                                        <input type="hidden" name="MM_insert" >
                                                    </div>
                                                </div>
                                            <?php } } ?>
                                        <?PHP } else { ?>
                                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                                                <div class="col-sm-4" align="left">  
                                                    <label for="grupo" class="control-label" ><strong style="color:#03C1FB;">*</strong>Grupo:</label><br>
                                                    <select name="grupo" id="grupo" class="select2_single form-control input-sm" title="Seleccione Grupo" style="width:250px; " required>
                                                        <option value="">Grupo</option>
                                                        <?php  
                                                        
                                                        for($i=0;$i<count($cplan); $i++){
                                                            echo '<option value="'.$cplan[$i][0].'">'.$cplan[$i][2].' - '.ucwords(mb_strtolower($cplan[$i][1])). '</option>';
                                                        } ?>
                                                       
                                                    </select>
                                                </div>
                                            </div>
                                            <script>
                                                $("#grupo").change(function(){
                                                    document.location = ('GF_INTERFAZ_TRAN.php?interfaz='+$("#tipoI").val()+'&grupo='+md5($("#grupo").val()));  
                                                })
                                            </script>
                                            <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                                <div class="col-sm-12 " align="right"  style="margin-top: -20px; margin-left: -20px;">
                                                    <input type="hidden" name="MM_insert" >
                                                </div>
                                            </div>
                                        <?php }     
                                } else { ?>
                                <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                    <div class="col-sm-12 " align="right"  style="margin-top: -20px; margin-left: -20px;">
                                        <input type="hidden" name="MM_insert" >
                                    </div>
                                </div>    
                                <?php }  ?> 
                            <input type="hidden" name="MM_insert" >
                            <!--Funciones Guardar-->
                            <script>
                                function guardar1(){
                                    var grupo   = $("#grupo").val();
                                    var tipom   = $("#tipomov").val();
                                    var ctade  = $("#ctadeb").val();
                                    var ctacr = $("#ctacred").val();
                                    if(grupo =='' || tipom=='' || ctade=='' || ctacr==''){
                                        $("#mensaje").html('Datos Incompletos');
                                        $("#myModalError").modal("show");
                                    } else {
                                        jsShowWindowLoad('Guardando Configuración...');
                                        var form_data = { action: 1, grupo :grupo,
                                        tipom :tipom,
                                        ctade :ctade,
                                        ctacr :ctacr };
                                        $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/gf_interfaz_almacen.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                                jsRemoveWindowLoad();
                                                console.log(response+'Action1');
                                                $("#mensaje").html(response);
                                                $("#myModalError").modal("show");
                                                $("#btnErrorModal").click(function(){
                                                    document.location.reload();
                                                })
                                            }
                                        })

                                    }
                                }
                            </script>
                            <script>
                                function guardar2(){
                                    var grupo   = $("#grupo").val();
                                    var ctade  = $("#ctadeb").val();
                                    var ctacr = $("#ctacred").val();
                                    var ctaba = $("#ctabaja").val();
                                    
                                    if(grupo =='' || ctade=='' || ctacr==''){
                                        $("#mensaje").html('Datos Incompletos');
                                        $("#myModalError").modal("show");
                                    } else {
                                        jsShowWindowLoad('Guardando Configuración...');
                                        var form_data = { action: 2, grupo :grupo,
                                        ctade :ctade,
                                        ctacr :ctacr, 
                                        ctaba :ctaba };
                                        $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/gf_interfaz_almacen.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                                jsRemoveWindowLoad();
                                                console.log(response+'Action2')
                                                $("#mensaje").html(response);
                                                $("#myModalError").modal("show");
                                                $("#btnErrorModal").click(function(){
                                                    document.location.reload();
                                                })
                                            }
                                        })
                                    }
                                }
                            </script>
                    </form>  
                    </div>
                    <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <input type="hidden" id="idPrevio" value="">
                        <table id="tabla" class="table table-striped table-condensed text-center" class="display" cellspacing="0" width="100%">
                            <?php if(isset($_GET['interfaz'])){ 
                                if(isset($_GET['grupo'])){
                                    if($_GET['interfaz']==1){ ?>
                                        <thead>
                                            <tr>
                                                <td style="display: none;">Identificador</td>
                                                <td width="30px" align="center"></td>
                                                <td><strong>Tipo Movimiento</strong></td>
                                                <td><strong>Cuenta Débito</strong></td>
                                                <td><strong>Cuenta Crédito</strong></td>
                                            </tr>
                                            <tr>
                                                <th style="display: none;">Identificador</th>
                                                <th width="7%"></th>
                                                <th width="30%">Tipo Movimiento</th>
                                                <th width="30%">Cuenta Débito</th>
                                                <th width="30%">Cuenta Crédito</th>
                                            </tr>
                                        </thead> 
                                       
                                    <?php }elseif($_GET['interfaz']==2){ ?>
                                        <thead>
                                            <tr>
                                                <td style="display: none;">Identificador</td>
                                                <td width="30px" align="center"></td>
                                                <td><strong>Cuenta Débito</strong></td>
                                                <td><strong>Cuenta Crédito</strong></td>
                                                <td><strong>Cuenta Baja</strong></td>
                                                
                                            </tr>
                                            <tr>
                                                <th style="display: none;">Identificador</th>
                                                <th width="7%"></th>
                                                <th width="30%">Cuenta Débito</th>
                                                <th width="30%">Cuenta Crédito</th>
                                                <th width="30%">Cuenta Baja</th>
                                                
                                            </tr>
                                        </thead>
                                    <?php } ?>
                                    <tbody>
                                        <?php for($i=0; $i<count($cons);$i++){?>
                                            <tr>
                                                <td style="display: none;"><?php echo $cons[$i][0]?></td>
                                                <td>
                                                    <a href="#" onclick="javascript:eliminar(<?php echo $cons[$i][0];?>);">
                                                        <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                    </a>
                                                    <?php if(empty($cons[$i][4])) { $cb = 0;}else{$cb = $cons[$i][4];} ?>
                                                    <a href="javascript:modificar(<?php echo $cons[$i][0].','.$cb.','.$cons[$i][5].','.$cons[$i][6]?>);">
                                                        
                                                        <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                    </a>
                                                </td>
                                                <?php 
                                                echo "<td>".ucwords($cons[$i][1])."</td>";
                                                echo "<td>".ucwords($cons[$i][2])."</td>";
                                                echo "<td>".ucwords($cons[$i][3])."</td>";
                                            ?>         
                                            </tr>
                                        <?php } ?>        
                                    </tbody>
                                <?php } 
                                }?>
                            
                           
                        </table>
                    </div>
            </div>
            </div>
             <script>
                function eliminar(id){
                    $("#mdlEliminar").modal("show");
                    $("#btnmdlEliminarS").click(function(){
                        jsShowWindowLoad('Eliminando Información...');
                        var form_data = { action: 3, id:id };
                        $.ajax({
                            type: "POST",
                            url: "jsonPptal/gf_interfaz_almacen.php",
                            data: form_data,
                            success: function(response)
                            {
                                jsRemoveWindowLoad();
                                $("#mensaje").html(response);
                                $("#myModalError").modal("show");
                                $("#btnErrorModal").click(function(){
                                    document.location.reload();
                                })
                            }
                        })
                    })
                    
                }
             </script>
             <script>
                function modificar(id, bt, cd, cc){
                    $("#idmod").val(id);
                    $("#tipcmod").val(bt);
                    $("#ctadmod").val(cd);
                    $("#ctacmod").val(cc);
                    $("#myModalUpdate").modal("show");
                    
                }
             </script>
             <!--  Modal Modificar  -->  
            <div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
                
                <div class="modal-dialog">
                    <div class="modal-content client-form1">
                        <div id="forma-modal" class="modal-header">       
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
                        </div>
                        <div class="modal-body ">
                            <form  name="form" method="POST" action="javascript:modificarItem()">
                                <div class="form-group" style="margin-top: 13px;">
                                    <?php if($_GET['interfaz']==1){ ?>
                                        <input type="hidden" id="tipo" name = "tipo" value ="1">
                                        <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Tipo Movimiento:</label>
                                        <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tipcmod" id="tipcmod" class="select2_single form-control" title="Seleccione Tipo Movimiento" required>
                                                <option>Tipo Movimiento</option>
                                                <?php  
                                                for($i=0;$i<count($ttmov2); $i++){
                                                    echo '<option value="'.$ttmov2[$i][0].'">'.mb_strtoupper( $ttmov2[$i][2]).' - '.ucwords(mb_strtolower($ttmov2[$i][1])). '</option>';
                                                    
                                                }?>
                                        </select>                       
                                    <?php } ?>         
                                </div>
                                <div class="form-group" style="margin-top: 13px;">
                                        <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Cuenta Débito:</label>
                                        <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="ctadmod" id="ctadmod" class="select2_single form-control" title="Seleccione Cuenta Débito" required>
                                                <option>Cuenta Débito </option>
                                                <?php 
                                                for($i=0;$i<count($ccuenta); $i++){
                                                    echo '<option value="'.$ccuenta[$i][0].'">'.$ccuenta[$i][2].' - '.ucwords(mb_strtolower($ccuenta[$i][1])). '</option>';
                                                }?>
                                        </select>                       
                                </div>
                                <div class="form-group" style="margin-top: 13px;">
                                        <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Cuenta Crédito:</label>
                                        <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="ctacmod" id="ctacmod" class="select2_single form-control" title="Seleccione Cuenta Crédito" required>
                                                <option>Cuenta Crédito </option>
                                                <?php 
                                                for($i=0;$i<count($ccuenta); $i++){
                                                    echo '<option value="'.$ccuenta[$i][0].'">'.$ccuenta[$i][2].' - '.ucwords(mb_strtolower($ccuenta[$i][1])). '</option>';
                                                }
                                                ?>
                                        </select>                       
                                </div>
                                <div class="form-group" style="margin-top: 13px;">
                                    <?php if($_GET['interfaz']==2){ ?>
                                        <input type="hidden" id="tipo" name = "tipo" value ="2">
                                        <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Cuenta Baja:</label>
                                        <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tipcmod" id="tipcmod" class="select2_single form-control" title="Seleccione Cuenta Baja">
                                                <option value="">Cuenta Baja </option>
                                                <?php 
                                                for($i=0;$i<count($ccuenta); $i++){
                                                    echo '<option value="'.$ccuenta[$i][0].'">'.$ccuenta[$i][2].' - '.ucwords(mb_strtolower($ccuenta[$i][1])). '</option>';
                                                }?>
                                        </select>                       
                                    <?php } ?>         
                                </div>
                                <input type="hidden" id="idmod" name="idmod">  
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                            <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                function modificarItem(){
                    $("#myModalUpdate").modal("hide");
                    var id =$("#idmod").val();
                    var tipo = $("#tipcmod").val();
                    var ctad = $("#ctadmod").val();
                    var ctac = $("#ctacmod").val();
                    if(ctad=='' || ctac==''){
                        $("#mensaje").html('Datos Incompletos');
                        $("#myModalError").modal("show");
                    } else {
                        jsShowWindowLoad('Modificando Información...');
                        if($("#tipo").val()==1) { 
                            var form_data = { action: 4, id :id,
                                ctad :ctad,
                                ctac :ctac, 
                                tipo :tipo };
                            $.ajax({
                                type: "POST",
                                url: "jsonPptal/gf_interfaz_almacen.php",
                                data: form_data,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    $("#mensaje").html(response);
                                    $("#myModalError").modal("show");
                                    $("#btnErrorModal").click(function(){
                                        document.location.reload();
                                    })
                                }
                            })
                        } else {
                            if($("#tipo").val()==2){ 
                                var form_data = { action: 5, id :id,
                                    ctad :ctad,
                                    ctac :ctac, 
                                    tipo :tipo };
                                $.ajax({
                                    type: "POST",
                                    url: "jsonPptal/gf_interfaz_almacen.php",
                                    data: form_data,
                                    success: function(response)
                                    {
                                        jsRemoveWindowLoad();
                                        console.log(response);
                                        $("#mensaje").html(response);
                                        $("#myModalError").modal("show");
                                        $("#btnErrorModal").click(function(){
                                            document.location.reload();
                                        })
                                    }
                                })
                            }
                        }
                    } 
                    
                }
             </script>
            <div class="modal fade" id="myModalError" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                      <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                      </div>
                      <div class="modal-body" style="margin-top: 8px">
                          <labe id="mensaje" name="mensaje" style="font-weight:light"></labe>
                      </div>
                      <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnErrorModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                        </button>
                      </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="mdlEliminar" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                      <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                      </div>
                      <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado?</p>
                      </div>
                      <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnmdlEliminarS" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                        </button>
                        <button type="button" id="btnmdlEliminarN" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Cancelar
                        </button>
                      </div>
                    </div>
                </div>
            </div>
            </div>
        </div> <!-- Cierra clase col-sm-10 text-left -->
    </div> <!-- Cierra clase row content -->

<script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() 
    {
        $(".select2_single").select2(
        {
            allowClear: true
        });
    });
</script>
<script>
function jsRemoveWindowLoad() {
    // eliminamos el div que bloquea pantalla
    $("#WindowLoad").remove(); 
}
 
function jsShowWindowLoad(mensaje) {
    //eliminamos si existe un div ya bloqueando
    jsRemoveWindowLoad(); 
    //si no enviamos mensaje se pondra este por defecto
    if (mensaje === undefined) mensaje = "Procesando la información<br>Espere por favor"; 
    //centrar imagen gif
    height = 20;//El div del titulo, para que se vea mas arriba (H)
    var ancho = 0;
    var alto = 0; 
    //obtenemos el ancho y alto de la ventana de nuestro navegador, compatible con todos los navegadores
    if (window.innerWidth == undefined) ancho = window.screen.width;
    else ancho = window.innerWidth;
    if (window.innerHeight == undefined) alto = window.screen.height;
    else alto = window.innerHeight; 
    //operación necesaria para centrar el div que muestra el mensaje
    var heightdivsito = alto/2 - parseInt(height)/2;//Se utiliza en el margen superior, para centrar 
   //imagen que aparece mientras nuestro div es mostrado y da apariencia de cargando
    imgCentro = "<div style='text-align:center;height:" + alto + "px;'><div  style='color:#FFFFFF;margin-top:" + heightdivsito + "px; font-size:20px;font-weight:bold;color:#1075C1'>" + mensaje + "</div><img src='img/loading.gif'/></div>"; 
        //creamos el div que bloquea grande------------------------------------------
        div = document.createElement("div");
        div.id = "WindowLoad";
        div.style.width = ancho + "px";
        div.style.height = alto + "px";        
        $("body").append(div); 
        //creamos un input text para que el foco se plasme en este y el usuario no pueda escribir en nada de atras
        input = document.createElement("input");
        input.id = "focusInput";
        input.type = "text"; 
        //asignamos el div que bloquea
        $("#WindowLoad").append(input); 
        //asignamos el foco y ocultamos el input text
        $("#focusInput").focus();
        $("#focusInput").hide(); 
        //centramos el div del texto
        $("#WindowLoad").html(imgCentro);
 
}
</script>

<style>
#WindowLoad{
    position:fixed;
    top:0px;
    left:0px;
    z-index:3200;
    filter:alpha(opacity=80);
   -moz-opacity:80;
    opacity:0.80;
    background:#FFF;
}
</style>
<?php require_once 'footer.php'; ?>

</body>
</html>

