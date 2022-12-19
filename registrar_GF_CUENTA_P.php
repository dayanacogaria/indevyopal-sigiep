<?php
##########################################################################################
# *********************************** Modificaciones *********************************** # 
##########################################################################################
#05/02/2018 | Erica G. | Equivalente Vigencia Anterior
##########################################################################################
require_once ('./Conexion/conexion.php');
require_once ('./head.php');
require_once './Conexion/ConexionPDO.php';
require_once './jsonPptal/funcionesPptal.php';
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
$nanno2 = $nanno-1;
$cann2 = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = $nanno2");
if(count($cann2)>0){
    $anno2 = $cann2[0][0];
} else {
    $anno2 = 0;
}
?>
        <title>Registrar Cuenta</title>
        <style>
            .disabled {
                pointer-events: none;
                cursor: default;
                opacity: 0.6;
            }
        </style>
    </head>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <body> 
        <div class="container-fluid text-left" >
            <div class="row content">
                <?php require_once ('menu.php'); ?>                
                <div class="col-sm-7 text-left" style="margin-top: -22px;margin-left: -20px" >
                    <h2 class="tituloform" align="center">Registrar Cuenta</h2>
                    <div class="contenedorForma client-form" style="margin-top: -5px">
                        <form name="formcuenta" id="formcuenta" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:guardarCta()" >  
                            <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>					
                            <div class="form-group" style="margin-top: -20px">
                                <label for="txtCodigoC" class="control-label col-sm-5" >
                                    <strong class="obligado">*</strong>Código Cuenta:                                   
                                </label>
                                <input type="text" name="txtCodigoC" id="txtCodigoC" class="form-control" title="Ingrese código cuenta" onkeypress="return txtValida(event,'num')" maxlength="50" placeholder="Código Cuenta" required style="height: 30px" autofocus="" onblur="javascript:consultar();"/>
                            </div> 
                            
                            <script>
                                $("#txtCodigoC").change(function(){
                                    var codigo = document.getElementById('txtCodigoC').value;
                                    
                                    var form_data = {
                                                
                                                id:codigo
                                            };
                                        
                                            $.ajax({
                                                type:"POST",
                                                url:"consultasDatosCuenta/consultarExistente.php",
                                                data:form_data,
                                                success: function (data) {
                                                    var result = JSON.parse(data);
                                                    if(result==1){
                                                        $("#myModal1").modal('show');
                                                        document.getElementById('txtCodigoC').value="";
                                                    }
                                                }
                                            });
                                })
                            </script>
                            <div class="form-group" style="margin-top: -20px">
                                <label class="col-sm-5 control-label" for="txtNombre">
                                    <strong class="obligado">*</strong>Nombre:
                                </label>
                                <input type="text" name="txtNombre" id="txtNombre" class="form-control" title="Ingrese nombre" onkeypress="return txtValida(event,'num_car')" maxlength="100" placeholder="Nombre" required style="height: 30px" autofocus=""/>
                            </div>                            
                            <div class="form-group" style="margin-top: -20px">
                                <?php 
                                $sql = "SELECT DISTINCTROW id_unico,nombre FROM gf_naturaleza ORDER BY nombre ASC";
                                $nat = $mysqli->query($sql);                                
                                ?>
                                <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Naturaleza:
                                </label>
                                <select name="sltNaturaleza" id="sltNaturaleza" class="form-control" title="Seleccione naturaleza de la cuenta" style="height: 30px" required="">
                                    <option>Naturaleza</option>
                                    <?php 
                                    while ($fila = mysqli_fetch_row($nat)) {
                                        echo '<option value="'.$fila[0].'">'.ucwords((strtolower($fila[1]))).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -20px">
                                <?php 
                                $sql = "SELECT DISTINCTROW id_unico,nombre FROM gf_clase_cuenta ORDER BY nombre ASC";
                                $rs = $mysqli->query($sql);                                
                                ?>
                                <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Clase Cuenta:
                                </label>
                                <select name="sltClaseC" id="sltClaseC" class="form-control" title="Seleccione clase cuenta" style="height: 30px" required="">
                                    <option>Clase Cuenta</option>
                                    <?php 
                                    while ($fila = mysqli_fetch_row($rs)) {
                                        echo '<option value="'.$fila[0].'">'.ucwords((strtolower($fila[1]))).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top:-28px">                                    
                                <label class="control-label col-sm-5" for="optMov">
                                     <strong class="obligado">*</strong>Movimiento:
                                </label>
                                <input type="radio" name="optMov" id="optMov1"  title="Indicar si la cuenta recibe movimiento directamente, es decir son cuentas auxiliares" value="1" />SI
                                <input type="radio" name="optMov" id="optMov2"  title="Indicar si la cuenta no recibe movimiento directamente, es decir no son cuentas auxiliares" value="2" />NO                                
                            </div><br/>
                            <div class="form-group" style="margin-top:-28px">
                                <label for="optManP" class="control-label col-sm-5">
                                     <strong class="obligado">*</strong>Centro Costo:
                                </label>
                                <input type="radio" name="optCentro" id="optCentro1" title="Indicar si la cuenta maneja centro de costo" value="1"/>SI
                                <input type="radio" name="optCentro" id="optCentro2" title="Indicar si la cuenta no maneja centro de costo" value="2"/>NO                                    
                            </div><br/>
                            <div class="form-group" style="margin-top:-28px">                                    
                                <label class="control-label col-sm-5" for="optMov">
                                     <strong class="obligado">*</strong>Auxiliar Tercero:
                                </label>
                                <input type="radio" name="optAuxT" id="optAuxT1"  title="Indicar si la cuenta maneja auxiliar de tercero" value="1" />SI
                                <input type="radio" name="optAuxT" id="optAuxT2"  title="Indicar si la cuenta no maneja auxiliar de tercero" value="2" />NO                                
                            </div><br/>
                            <div class="form-group" style="margin-top: -28px">
                                <label for="optManP" class="control-label col-sm-5">
                                     <strong class="obligado">*</strong>Auxiliar Proyecto:
                                </label>
                                <input type="radio" name="optAuxP" id="optAuxP1" title="Indicar si maneja centro costo" value="1" />SI
                                <input type="radio" name="optAuxP" id="optAuxP2" title="Indicar si maneja centro costo" value="2" />NO                                    
                            </div><br/>
                            <div class="form-group" style="margin-top: -28px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Activa:                                    
                                </label>
                                <input type="radio" name="optAct" id="optAct1" title="Indicar si la cuenta esta activa" value="1"/>SI
                                <input type="radio" name="optAct" id="optAct2" title="Indicar si la cuenta esta no activa" value="2" />NO                                    
                            </div><br/>
                            <div class="form-group" style="margin-top:-28px">
                                <label for="txtDinamica" class="col-sm-5 control-label">
                                    Dinamica:
                                </label>
                                <textarea type="text" name="txtDinamica" id="txtDinamica" title="Ingrese dinamica" class="form-control" onkeypress="return txtValida(event,'num_car')" maxlength="5000" placeholder="Dinamica" style="height: 45px;resize: both;"></textarea>
                            </div>
                            <div class="form-group" style="margin-top: -20px">
                                <?php 
                                $query = "SELECT DISTINCTROW id_unico,nombre FROM gf_tipo_cuenta_cgn ORDER BY nombre DESC";
                                $result = $mysqli->query($query); 
                                ?>
                                <label class="col-sm-5 control-label">
                                    Tipo Cuenta Cgn:
                                </label>
                                <select name="sltTipoCuentaCgn" id="sltTipoCuentaCgn" class="form-control" title="Seleccione tipo clase cuenta cgn" style="height: 30px" >
                                    <option value="0">Tipo Cuenta Cgn</option>
                                    <?php 
                                    while ($fila = mysqli_fetch_row($result)) {
                                        echo '<option value="'.$fila[0].'">'.ucwords((mb_strtolower($fila[1]))).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group texto" style="margin-top: -20px">
                                <?php 
                                $query = "SELECT DISTINCTROW
                                                        PADRE.id_unico,                        
                                                        PADRE.codi_cuenta,
                                                        PADRE.nombre
                                    FROM
                                                        gf_cuenta PADRE 
                                        WHERE PADRE.parametrizacionanno = $anno ORDER BY PADRE.codi_cuenta ASC";
                                $rs = $mysqli->query($query);
                                ?>
                                <label class="col-sm-5 control-label">
                                    Predecesor:
                                </label>
                                <select name="sltPredecesor" class="select2_single form-control" id="sltPredecesor" title="Seleccione predecesor">
                                    <option value="">Predecesor</option>
                                    <?php 
                                    while($fila1 = mysqli_fetch_row($rs)){ ?>
                                        <option value="<?php echo $fila1[0];  ?>"><?php echo ucwords((mb_strtolower($fila1[1].' - '.$fila1[2]))); ?></option>
                                    <?php }
                                    ?>
                                </select>
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        var padre = 0;
                                        $("#sltPredecesor").change(function(){
                                   
                                            if ($("#sltPredecesor").val()=="" || $("#sltPredecesor").val()==0) {
                                                padre = 0;
                                            }else{
                                                padre = $("#sltPredecesor").val();
                                            }
                                        
                                            var form_data = {
                                                is_ajax:1,
                                                id:+padre
                                            };
                                        
                                            $.ajax({
                                                type:"POST",
                                                url:"consultasDatosCuenta/consultarNatural.php",
                                                data:form_data,
                                                success: function (data) {
                                                    $("#sltNaturaleza").html(data).fadeIn();                                                        
                                                }
                                            });
                                        });
                                    });
                                                                        
                                </script>
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        var padre = 0;
                                        $("#sltPredecesor").change(function(){
                                            if ($("#sltPredecesor").val()=="" || $("#sltPredecesor").val()==0) {
                                                padre = 0;
                                            }else{
                                                padre = $("#sltPredecesor").val();
                                            }
                                            
                                            var form_data = {
                                                is_ajax:1,
                                                id:+padre
                                            };
                                            $.ajax({
                                                type:"POST",
                                                url:"consultasDatosCuenta/consultarClaseCuenta.php",
                                                data:form_data,
                                                success: function (data) {
                                                    $("#sltClaseC").html(data).fadeIn();                                                        
                                                }
                                            });
                                        });
                                    });
                                </script>
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        $("#optMov1").prop('checked',false);
                                        $("#optMov2").prop('checked',true);
                                        var padre = 0;
                                        $("#sltPredecesor").change(function(){
                                            if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                padre = 0;
                                                $("#optMov1").prop('checked',false);
                                                $("#optMov2").prop('checked',true);
                                            }else{
                                                padre = $("#sltPredecesor").val();
                                            }
                                            console.log(padre+'padre');
                                            var form_data = {
                                                is_ajax:1,
                                                id:+padre
                                            };
                                            
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasDatosCuenta/consultarMovimiento.php",
                                                data:form_data,
                                                success: function (data) {
                                                    console.log(data+'Mov');
                                                    if (data==1) {
                                                        $("#optMov1").prop('checked',true);
                                                        $("#optMov2").prop('checked',false);
                                                        $("#noMov").modal('show');
                                                        desabilitar();
                                                    }else if(data==2){
                                                        $("#optMov1").prop('checked',false);
                                                        $("#optMov2").prop('checked',true);
                                                    }else if(data==0){
                                                        $("#optMov1").prop('checked',false);
                                                        $("#optMov2").prop('checked',true);
                                                    }
                                                }
                                            });
                                        });
                                    });
                                </script>
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        $("#optCentro1").prop('checked',false);
                                        $("#optCentro2").prop('checked',true);
                                        var padre = 0;
                                        $("#sltPredecesor").change(function(){
                                            if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                padre = 0;
                                                $("#optCentro1").prop('checked',false);
                                                $("#optCentro2").prop('checked',true);
                                            }else{
                                                padre = $("#sltPredecesor").val();
                                            }
                                            
                                            var form_data = {
                                                is_ajax:1,
                                                id:$("#sltPredecesor").val()
                                            };
                                            
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasDatosCuenta/ConsultarCC.php",
                                                data:form_data,
                                                success: function (data) {
                                                    console.log(data);
                                                    if (data==1) {                              
                                                        $("#optMov1").attr('disabled',true);
                                                        $("#optCentro1").prop('checked',true);                                                                                                                
                                                        $("#optCentro2").prop('checked',false);                                                         
                                                    }else if(data==2){
                                                        $("#optCentro1").prop('checked',false);
                                                        $("#optCentro2").prop('checked',true);
                                                        console.log($("#optAuxP1").is(':checked'));
                                                        if($("#optAuxP1").is(':checked')){
                                                            if($("#optAuxT1").is(':checked')){
                                                                $("#optMov1").attr('disabled',true);
                                                            }                                                            
                                                        }else{
                                                            $("#optMov1").attr('disabled',false);
                                                        }                                                        
                                                    }else if(data==0){
                                                        $("#optCentro1").prop('checked',false);
                                                        $("#optCentro2").prop('checked',true);
                                                        $("#optMov1").attr('disabled',false);
                                                    }
                                                }
                                            });
                                        });
                                    });                                                                        
                                </script>
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        $("#optAuxT1").prop('checked',false);
                                        $("#optAuxT2").prop('checked',true);
                                        var padre = 0;
                                        $("#sltPredecesor").change(function(){
                                            if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                padre = 0;
                                                $("#optAuxT1").prop('checked',false);
                                                $("#optAuxT2").prop('checked',true);
                                            }else{
                                                padre = $("#sltPredecesor").val();
                                            }
                                            
                                            var form_data = {
                                                is_ajax:1,
                                                id:+padre
                                            };
                                            
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasDatosCuenta/consultarAuxT.php",
                                                data:form_data,
                                                success: function (data) {
                                                    console.log(data);
                                                    if (data==1) {
                                                        $("#optMov1").prop('disabled',true);
                                                        $("#optAuxT1").prop('checked',true);
                                                        $("#optAuxT2").prop('checked',false);                                                        
                                                    }else if(data==2){
                                                        $("#optAuxT1").prop('checked',false);
                                                        $("#optAuxT2").prop('checked',true);
                                                    }else if(data ==0){
                                                        $("#optAuxT1").prop('checked',false);
                                                        $("#optAuxT2").prop('checked',true);
                                                    }
                                                }
                                            });
                                        });
                                    });
                                </script>
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        $("#optAuxP1").prop('checked',false);
                                        $("#optAuxP2").prop('checked',true);
                                        var padre = 0;
                                        $("#sltPredecesor").change(function(){
                                            if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                padre = 0;
                                                $("#optAuxP1").prop('checked',false);
                                                $("#optAuxP2").prop('checked',true);
                                            }else{
                                                padre = $("#sltPredecesor").val();
                                            }
                                            
                                            var form_data = {
                                                is_ajax:1,
                                                id:+padre
                                            };
                                            
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasDatosCuenta/consultarAuxP.php",
                                                data:form_data,
                                                success: function (data) {
                                                    console.log(data);
                                                    if (data==1) {
                                                        $("#optMov1").prop('disabled',true);
                                                        $("#optAuxP1").prop('checked',true);
                                                        $("#optAuxP2").prop('checked',false);                                                        
                                                    }else if(data==2){
                                                        $("#optAuxP1").prop('checked',false);
                                                        $("#optAuxP2").prop('checked',true);
                                                    }else if(data ==0){
                                                        $("#optAuxP1").prop('checked',false);
                                                        $("#optAuxP2").prop('checked',true);
                                                    }
                                                }
                                            });
                                        });
                                    });
                                </script>
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        $("#optAct1").prop('checked',false);
                                        $("#optAct2").prop('checked',true);
                                        var padre = 0;
                                        $("#sltPredecesor").change(function(){
                                            if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() === 0))) {
                                                padre = 0;
                                                $("#optAct1").prop('checked',false);
                                                $("#optAct2").prop('checked',true);
                                            }else{
                                                padre = $("#sltPredecesor").val();
                                            }
                                            
                                            var form_data = {
                                                is_ajax:1,
                                                id:+padre
                                            };
                                            
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasDatosCuenta/consultarActiva.php",
                                                data:form_data,
                                                success: function (data) {
                                                    console.log(data);
                                                    if (data==1) {
                                                        $("#optAct1").prop('checked',true);
                                                        $("#optAct2").prop('checked',false);
                                                        $("#optMov1").prop('disabled',true);
                                                    }else if(data==2){
                                                        $("#optAct1").prop('checked',false);
                                                        $("#optAct2").prop('checked',true);
                                                    }else if(data ==0){
                                                        $("#optAct1").prop('checked',false);
                                                        $("#optAct2").prop('checked',true);
                                                    }
                                                }
                                            });
                                        });
                                    });
                                </script>
                                <script type="text/javascript">
                                        $(document).ready(function(){
                                            var padre = 0;
                                            $("#sltPredecesor").change(function(){
                                                if (($("#sltPredecesor").val() == "" || ($("#sltPredecesor").val() == 0))) {
                                                    padre = 0;
                                                }else{
                                                    padre = $("#sltPredecesor").val();
                                                }
                                                
                                                var form_data = {
                                                    is_ajax:1,
                                                    id:+padre
                                                };
                                                
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultasDatosCuenta/consultarDinamica.php",
                                                    data:form_data,
                                                    success: function (data) {
                                                        $('textarea[name=txtDinamica]').val(data);
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                    <script type="text/javascript">
                                    $(document).ready(function(){
                                        var padre = 0;
                                        $("#sltPredecesor").change(function(){
                                            if ($("#sltPredecesor").val()=="" || $("#sltPredecesor").val()==0) {
                                                padre = 0;
                                            }else{
                                                padre = $("#sltPredecesor").val();
                                            }
                                            
                                            var form_data = {
                                                is_ajax:1,
                                                id:$("#sltPredecesor").val()
                                            };
                                            $.ajax({
                                                type:"POST",
                                                url:"consultasDatosCuenta/consultarTipoCGN.php",
                                                data:form_data,
                                                success: function (data) {
                                                    $("#sltTipoCuentaCgn").html(data).fadeIn();                                                        
                                                }
                                            });
                                        });
                                    });
                                </script>
                            </div>
                            <div class="form-group" style="margin-top: -10px">
                                <?php 
                                $query = "SELECT DISTINCTROW id_unico,codi_cuenta, nombre "
                                        . "FROM gf_cuenta WHERE parametrizacionanno = $anno2 ORDER BY codi_cuenta ASC";
                                $result = $mysqli->ver1
                                
                                ($query); 
                                ?>
                                <label class="col-sm-5 control-label">
                                    Equivalente Vigencia Anterior:
                                </label>
                                <select name="sltEquivalente" id="sltEquivalente" class="select2_single form-control" title="Seleccione Código Equivalente Vigencia Anterior" style="height: 30px" >
                                    <option value="">Equivalente Vigencia Anterior</option>
                                    <?php 
                                    while ($fila = mysqli_fetch_row($result)) {
                                        echo '<option value="'.$fila[1].'">'.$fila[1].' - '.ucwords((mb_strtolower($fila[2]))).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top:5px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -12px; margin-bottom: -10px; margin-left: 0px;">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div>
                <div class="col-sm-7 col-sm-3">
                    <table class="tablaC table-condensed" style="margin-top: -22px;">
                        <thead>
                            <tr>
                                <tr>
                                    <th>
                                        <h2 class="titulo" align="center">Consultas</h2>
                                    </th>
                                    <th>
                                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                    </th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="btnConsultas disabled" style="margin-bottom: 1px;">
                                        <a href="javascript:void(0)"  id="linkMovE">
                                            MOVIMIENTOS EFECTUADOS
                                        </a>                                        
                                    </div>
                                </td>
                                <td>
                                    <a href="Registrar_GF_CLASE_CUENTA.php" class="btn btn-primary btnInfo">CLASE CUENTA</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="btnConsultas" style="margin-bottom: 1px;">
                                        <a href="#">
                                            MOVIMIENTO ENTRE MESES                                            
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GF_SECTOR.php">SECTOR</a>
                                </td>
                            </tr>                            
                            <tr>
                                <td>
                                    <div class="btnConsultas" style="margin-bottom: 1px;">
                                        <a href="#">
                                            GRAFICO DE<br/>SALDOS
                                        </a>
                                    </div>
                                </td>
                                <td>
                                </td>
                            </tr>                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noC" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Ingrese un código de cuenta.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Este código presupuestal ya existe.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noMov" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Movimiento esta activo. Código no puede ser utilizado</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="submit" id="btnNM" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Volver</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once('footer.php'); ?>            
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script> 
        <script type="text/javascript">
            function consultar(){
                var cod = document.form.txtCodigoC.value;
                if (cod == null || cod == '') {
                    $("#noC").modal('show');
                }else{
                    $.ajax({
                        data:{"codigo":cod},
                        type:"POST",
                        url:"consultasDatosCuenta/consultaCuenta.php",
                        success: function (data) {  
                            $('.texto select').html(data).change();
                        }
                    });
                }
            }
            
            function desabilitar(){
                $("#txtCodigoC").prop('disabled',true);
                $("#txtNombre").prop('disabled',true);
                $("#sltNaturaleza").prop('disabled',true);
                $("#sltClaseC").prop('disabled',true);
                $("input[type=radio]").prop('disabled',true);
                $("#txtDinamica").prop('disabled',true);
                $("#sltTipoCuentaCgn").prop('disabled',true);
                $("#sltPredecesor").prop('disabled',true);
            }
            
            $("#btnNM").click(function(){
                window.location.reload();
            });
            
            function zonaC (){
                $(document).click(function(){
                    window.location.reload();
                });
            }
        </script>
        <script type="text/javascript" >
            $("#sltPredecesor").change(function(){  
               $("#optMov1").change(function(){                
                    $("#optCentro1").attr('disabled',true);
                    $("#optMov1").attr('disabled',false);
                    $("#optAuxT1").attr('disabled',true);
                    $("#optAuxP1").attr('disabled',true);
                });
                $("#optMov2").change(function(){                
                    $("#optCentro1").attr('disabled',false);
                    $("#optMov1").attr('disabled',false);
                    $("#optAuxT1").attr('disabled',false);
                    $("#optAuxP1").attr('disabled',false);
                });

                $("#optCentro1").change(function(){
                    $("#optMov1").attr('disabled',true);
                });
                $("#optCentro2").change(function(){
                    $("#optMov1").attr('disabled',false);
                });
                $("#optAuxT1").change(function(){
                    $("#optMov1").attr('disabled',true);
                });
                $("#optAuxP1").change(function(){
                    $("#optMov1").attr('disabled',true);
                });
                $("#optAuxP2").change(function(){
                    $("#optMov1").attr('disabled',false);
                }); 
            }); 
            $("#optMov1").change(function(){                
                $("#optCentro1").attr('disabled',true);
                $("#optMov1").attr('disabled',false);
                $("#optAuxT1").attr('disabled',true);
                $("#optAuxP1").attr('disabled',true);
            });
            $("#optMov2").change(function(){                
                $("#optCentro1").attr('disabled',false);
                $("#optMov1").attr('disabled',false);
                $("#optAuxT1").attr('disabled',false);
                $("#optAuxP1").attr('disabled',false);
            });
            
            $("#optCentro1").change(function(){
                $("#optMov1").attr('disabled',true);
            });
            $("#optCentro2").change(function(){
                $("#optMov1").attr('disabled',false);
            });
            $("#optAuxT1").change(function(){
                $("#optMov1").attr('disabled',true);
            });
            $("#optAuxP1").change(function(){
                $("#optMov1").attr('disabled',true);
            });
            $("#optAuxP2").change(function(){
                $("#optMov1").attr('disabled',false);
            });
        </script>
        <script src="js/select/select2.full.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
</script>

<script>
      function guardarCta(){
          var cc = $("#sltClaseC").val();
          
          if(cc ==11 || cc==12){
            $("#lblmsj").html("¿Desea Crear Cuenta Bancaria?");
            $("#mdlMensajes").modal("show");
            $("#btnMsjAceptar").click(function(){ 
                guardarCuenta();
            });
            $("#btnMsjCancelar").click(function(){
                guardarSolo();
            });
          } else {
             guardarSolo(); 
          }
      }
  </script>
    <div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label style="font-weight: normal" id="lblmsj" name="lblmsj" ></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="submit" id="btnMsjAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Si</button>
                    <button type="submit" id="btnMsjCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">No</button>
                </div>
            </div>
        </div>
    </div>
    <script>
       function guardarCuenta(){
            var formData = new FormData($("#formcuenta")[0]); 
            $("#modalCuentaB").modal("show");
            
       }
        
    </script>
    <script>
        function guardarSolo(){
            var formData = new FormData($("#formcuenta")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_cuentaJson.php?action=1",
                data:formData,
                contentType: false,
                processData: false,
                success: function (response) { 
                    resultado = JSON.parse(response);
                    var data = resultado["respuesta"];
                    if(data==1){
                        $("#lblmsj1").html("Información Guardada Correctamente");
                        $("#mdlMensajes1").modal("show");
                        $("#btnMsjAceptar1").click(function(){
                            window.location='buscarCuenta.php';
                        });
                    }else{
                        if(data==2){
                            $("#lblmsj1").html("La Información No Se Ha Podido Guardar");
                            $("#mdlMensajes1").modal("show");
                            $("#btnMsjAceptar1").click(function(){
                                $("#mdlMensajes1").modal("hide");
                            }); 
                        } else {
                            if(data==3){
                                $("#lblmsj1").html("El Código Ya Está Siendo Utilizado");
                                $("#mdlMensajes1").modal("show");
                                $("#btnMsjAceptar1").click(function(){
                                    $("#mdlMensajes1").modal("hide");
                                }); 
                            } else {
                                $("#lblmsj1").html("Error");
                                $("#mdlMensajes1").modal("show");
                                $("#btnMsjAceptar1").click(function(){
                                    $("#mdlMensajes1").modal("hide");
                                });
                            }
                        }
                    }
                }
            });
        }
    </script>
    <div class="modal fade" id="mdlMensajes1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label style="font-weight: normal" id="lblmsj1" name="lblmsj" ></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="submit" id="btnMsjAceptar1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Aceptar</button>
                    <button type="submit" id="btnMsjCancelar1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalCuentaB" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <form  name="form2" id="form2" method="POST" action="javascript:guardarCuentaB()" enctype="multipart/form-data" >

                    <input type="hidden" id="idcuenta" name="idcuenta" value="">
                    <div class="form-group" style="margin-top: 13px;">
                    <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Banco:</label>
                    <?php 
                    @session_start();
                    $compania = $_SESSION['compania'];
                     $bancos= "SELECT t.id_unico, 
                            LOWER(t.razonsocial), 
                            t.tipoidentificacion, 
                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                            t.numeroidentificacion, 
                       CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                        FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt 
                        WHERE t.tipoidentificacion = ti.id_unico 
                        AND t.id_unico = pt.tercero 
                        AND pt.perfil = 9 AND t.compania = $compania 
                        ORDER BY razonsocial ASC";
                    $banco =   $mysqli->query($bancos);
                     echo '<select id="banco" name="banco" class="form-control select2_single" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" required="required">';
                     echo '<option value="">Banco</option>';
                     while ($row = mysqli_fetch_row($banco)) {
                         echo '<option value="'.($row[0]).'">'. ucwords($row[1].' - '.$row[3]).'</option>';
                     }
                    echo '</select>'; 
                    
                    ?>
                    </div>
                    <div class="form-group" style="margin-top: 13px;">
                        <label for="obligatorio"  style="margin-left: 0px;display:inline-block; width:140px;" ><strong style="color:#03C1FB;">*</strong>Número Cuenta:</label>
                        <input type="text" name="numcuenta" id="numcuenta" class="form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" required="required">
                    </div>
                    <div class="form-group" style="margin-top: 13px;">
                    <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Tercero:</label>
                    <?php 
                     $tercer= "SELECT t.id_unico, IF(CONCAT_WS(' ',
                            t.nombreuno,
                            t.nombredos,
                            t.apellidouno,
                            t.apellidodos) 
                            IS NULL OR CONCAT_WS(' ',
                            t.nombreuno,
                            t.nombredos,
                            t.apellidouno,
                            t.apellidodos) = '',
                            (t.razonsocial),
                            CONCAT_WS(' ',
                            t.nombreuno,
                            t.nombredos,
                            t.apellidouno,
                            t.apellidodos)) AS NOMBRE,
                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                            t.numeroidentificacion, 
                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                        FROM gf_tercero t WHERE t.compania = $compania";
                    $tercer =   $mysqli->query($tercer);
                     echo '<select id="tercero" name="tercero" class="form-control select2_single" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" required="required">';
                     echo '<option value="">Tercero</option>';
                     while ($rowt = mysqli_fetch_row($tercer)) {
                         echo '<option value="'.($rowt[0]).'">'. ucwords($rowt[1].' - '.$rowt[2]).'</option>';
                     }
                    echo '</select>'; 
                    
                    ?>
                    </div>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="submit" class="btn" style="color: #000; margin-top: 2px" >Aceptar</button>
                    <button id="btnmodalCuentaBC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Cancelar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script>
     function guardarCuentaB(){
        var formData = new FormData($("#formcuenta")[0]);  
        $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_cuentaJson.php?action=1",
            data:formData,
            contentType: false,
            processData: false,
            success: function (response) { 
                resultado = JSON.parse(response);
                var data = resultado["respuesta"];
                var id   = resultado["id"];
                if(data==1){
                     var formData2 = new FormData($("#form2")[0]);  
                     $.ajax({
                        type: 'POST',
                        url: "jsonPptal/gf_cuentaJson.php?action=2&id="+id,
                        data:formData2,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            resultado = JSON.parse(response);
                            var data = resultado["respuesta"];
                            var id   = resultado["id"];
                            if(data==1){
                                $("#modalCuentaB").modal("hide");
                                $("#lblmsj1").html("Información Guardada Correctamente");
                                $("#mdlMensajes1").modal("show");
                                $("#btnMsjAceptar1").click(function(){
                                    window.location='GF_CUENTA_BANCARIA_TERCERO.php';
                                });
                            } else {
                                $("#modalCuentaB").modal("hide");
                                $("#lblmsj1").html("La Información De La Cuenta Bancaria No Se Ha Podido Guardar");
                                $("#mdlMensajes1").modal("show");
                                $("#btnMsjAceptar1").click(function(){
                                    $("#mdlMensajes1").modal("hide");
                                });
                            }
                        }
                    })
                } else {
                    $("#modalCuentaB").modal("hide");
                    $("#lblmsj1").html("La Información De La Cuenta No Se Ha Podido Guardar");
                    $("#mdlMensajes1").modal("show");
                    $("#btnMsjAceptar1").click(function(){
                        $("#mdlMensajes1").modal("hide");
                    }); 
                }
            }
        });
       
     }
    </script>
    </body>
</html>

