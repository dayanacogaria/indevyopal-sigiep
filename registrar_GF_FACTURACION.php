<?php
#@Autor:Alexander
#Fomulario de facturación
require_once './Conexion/conexion.php';
require_once './head_listar.php';
?>
<title>Facturación</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
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
        $("#fecha").datepicker({changeMonth: true}).val();        
        $("#fechaV").datepicker({changeMonth: true}).val();  
    });
    </script>
    <style>
    /*Estilos tabla*/
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}
    /*Campos dinamicos*/
    .campoD:focus {
        border-color: #66afe9;
        outline: 0;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
    }
    .campoD:hover{
        cursor: pointer;
    }
    /*Campos dinamicos label*/
    .valorLabel{
        font-size: 10px;
    }
    .valorLabel:hover{
        cursor: pointer;
        color:#1155CC;
    }
    /*td de la tabla*/
    .campos{
        padding: 0px;
        font-size: 10px
    }
    /*cuerpo*/
    body{
        font-size: 10px
    }
    .form-control{
        padding: 2px;
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
    <script type="text/javascript">                                    
        function justNumbers(e){   
            var keynum = window.event ? window.event.keyCode : e.which;
            if ((keynum == 8) || (keynum == 46) || (keynum == 45))
            return true;
            return /\d/.test(String.fromCharCode(keynum));
        }
    </script>
</head>
<body>
    <div class="container-fluid text-left">
        <div class="row content">
            <?php require_once './menu.php'; ?>
            <div class="col-sm-8 text-center" style="margin-top:-22px;">
                <h2 class="tituloform" align="center">Facturación</h2>
                <div class="client-form contenedorForma" style="margin-top:-7px;">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarFacturacionJson.php" style="margin-bottom:-20px">
                        <?php 
                        $idP = 0;
                        $tipoFactura = 0;
                        $fecha = '';
                        $numeroFactura = 0;
                        $tercero = 0;
                        $centroCosto = 0;
                        $fechaVencimiento = '';
                        $estado = '';
                        $descripcion = '';
                        
                        if(!empty($_SESSION['idFactura'])){
                            $factura = $_SESSION['factura'];
                            $idFactura = $_SESSION['idFactura'];
                            $sqlFactura = "SELECT
                                        fac.id_unico,
                                        fac.tipofactura,
                                        fac.fecha_factura,
                                        fac.numero_factura,
                                        fac.tercero,
                                        fac.centrocosto,
                                        fac.fecha_vencimiento,
                                        fac.estado_factura,
                                        fac.descripcion,
                                        tpF.id_unico,
                                        tpF.nombre
                                    FROM gp_factura fac
                                    LEFT JOIN gp_tipo_factura tpF ON fac.tipofactura = tpF.id_unico
                                    WHERE fac.id_unico = $idFactura";
                            $rsFactura= $mysqli->query($sqlFactura);
                            $valoresFactura = mysqli_fetch_row($rsFactura);
                            
                            $idP = $valoresFactura[0];
                            $tipoFactura = $valoresFactura[1];
                            $fecha =$valoresFactura[2];
                            $numeroFactura = $valoresFactura[3];
                            $tercero = $valoresFactura[4];
                            $centroCosto = $valoresFactura[5];
                            $fechaVencimiento = $valoresFactura[6];
                            $estado = $valoresFactura[7];
                            $descripcion = $valoresFactura[8];
                            $sqlEstado = "SELECT nombre FROM gp_estado_factura WHERE id_unico = $estado";
                            $resEstado = $mysqli->query($sqlEstado);
                            $estdo = mysqli_fetch_row($resEstado);
                        }
                        ?>
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>                        
                        <div class="form-group form-inline"  style="margin-bottom: -0px">   
                            <!-- combo de selección Tipo factura -->
                            <label class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Tipo Factura:
                            </label>
                            <select class="form-control input-sm col-sm-2" name="slttipofactura" id="slttipofactura" title="Seleccione tipo factura" style="width:100px;height:30%" required>
                                <?php 
                                if(!empty($tipoFactura)){
                                    $sqlTF = "SELECT id_unico,nombre FROM gp_tipo_factura WHERE servicio = 2 AND id_unico = $tipoFactura";
                                    $rsTF = $mysqli->query($sqlTF);                                    
                                    $filaTF = mysqli_fetch_row($rsTF);
                                    echo '<option value="'.$filaTF[0].'">'.ucwords(strtolower($filaTF[1])).'</option>';
                                    $sqltipofactura = "SELECT id_unico,nombre FROM gp_tipo_factura WHERE servicio = 2 AND id_unico != $tipoFactura";
                                    $rstipoFactura = $mysqli->query($sqltipofactura);
                                    while($filatipoFactura = mysqli_fetch_row($rstipoFactura)){
                                        echo '<option value="'.$filatipoFactura[0].'">'.ucwords(strtolower($filatipoFactura[1])).'</option>';
                                    }
                                }else{
                                ?>
                                    <option value="">Tipo Factura</option>
                                    <?php 
                                    $sqlTF1 = "SELECT id_unico,nombre FROM gp_tipo_factura WHERE servicio = 2";
                                    $rsTF1 = $mysqli->query($sqlTF1);
                                    while($filaTF1 = mysqli_fetch_row($rsTF1)){
                                        echo '<option value="'.$filaTF1[0].'">'.ucwords(strtolower($filaTF1[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <!-- Texto en el que asignamos la fecha -->
                            <label for="fecha" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Fecha:
                            </label>                                
                            <input class="col-sm-2 input-sm" value="<?php if(!empty($fecha)){$valorF = (String) $fecha;$fechaS = explode("-",$valorF); echo $fechaS[2].'/'.$fechaS[1].'/'.$fechaS[0];}else{echo date('d/m/Y');} ?>" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;height:26px" title="Ingrese la fecha" placeholder="Fecha" required>
                            <!-- Número de factura-->
                            <label class="control-label col-sm-2">
                                <strong class="obligado">*</strong>Nro Factura:
                            </label>
                            <input class="form-control input-sm col-sm-2" name="txtNumeroF" id="txtNumeroF" type="text" title="Número factura" placeholder="Nro Factura" style="width:100px;height:26px" value="<?php if(!empty($factura)){echo $factura;}else{echo '';} ?>" required/>
                            <a id="btnBuscar" class="btn " title="Buscar Comprobante" style="margin-left:-110px;margin-top:-2px;padding:3px 3px 3px 3px"><li class="glyphicon glyphicon-search"></li></a>                            
                            <script>
                                $("#btnBuscar").click(function(){
                                    var tipofactura =$("#slttipofactura").val();
                                    if(tipofactura=='""' || tipofactura==0){
                                        $("#mdltipofactura").modal('show');
                                    }else{
                                            var form_data = {
                                                existente:2,
                                                tipo:$("#slttipofactura").val(),                                                
                                                numero:$("#txtNumeroF").val()                                                
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasBasicas/consultarNumeros.php",
                                                data:form_data,
                                                success: function (data) {
                                                    window.location.reload();                                                                                                     
                                                }
                                            });            
                                    }
                                });
                                    
                                $("#txtNumeroF").keyup(function(){ 
                                    var tipof = $("#slttipofactura").val();
                                    if(tipof==0 || tipof=='""'){
                                        $("#mdltipofactura").modal('show');
                                    }else{                                        
                                        $("#txtNumeroF").autocomplete({
                                            source: "consultasFacturacion/consultaAutocompletadoFactura.php?tipo="+tipof,
                                            minLength:5
                                        });
                                    }                                                                       
                                });
                            </script>
                        </div>
                        <div class="form-group form-inline"  style="margin-bottom: -0px">
                            <!-- Tercero -->
                            <label class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Tercero:
                            </label>
                            <select class="form-control input-sm col-sm-2" name="sltTercero" id="sltTercero" title="Seleccione tercero" style="margin-top:-5px;width:100px;height:30%" required>
                                <?php if(!empty($tercero)){
                                    $sqltercero="SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                                (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                                ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                WHERE ter.id_unico = $tercero
                                                ORDER BY NOMBRE ASC";
                                    $ter = $mysqli->query($sqltercero);                                    
                                    $per = mysqli_fetch_row($ter);
                                    echo '<option value="'.$per[1].'">'.ucwords(strtolower($per[0].'    '.$per[2])).'</option>';
                                    $tersql="SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                                (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                                ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                WHERE ter.id_unico != $tercero
                                                ORDER BY NOMBRE ASC";
                                    $tercer = $mysqli->query($tersql);
                                    while($per1 = mysqli_fetch_row($tercer)){
                                        echo '<option value="'.$per1[1].'">'.ucwords(strtolower($per1[0].'    '.$per1[2])).'</option>';
                                    }                                    
                                } else{?>
                                <option value="">Tercero</option>
                                <?php
                                    $ter2="SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                                (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                                ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                ORDER BY NOMBRE ASC";
                                    $tercero2 = $mysqli->query($ter2);
                                    while($per2 = mysqli_fetch_row($tercero2)){
                                        echo '<option value="'.$per2[1].'">'.ucwords(strtolower($per2[0].'    '.$per2[2])).'</option>';
                                    }  
                                }
                                ?>                                    
                            </select>
                            <!-- Centro de costo -->
                            <label class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Centro Costo:                                    
                            </label>
                            <select class="form-control input-sm col-sm-2" name="sltCentroCosto" id="sltCentroCosto" title="Seleccione centro costo" style="margin-top:-5px;width:100px;height:30%" required="">
                                <?php 
                                if(!empty($centroCosto)){
                                    $sqlCentroCosto = "SELECT id_unico,nombre FROM gf_centro_costo WHERE id_unico = $centroCosto GROUP BY id_unico";
                                    $rsCentroCosto = $mysqli->query($sqlCentroCosto);
                                    $filaCentroCosto=mysqli_fetch_row($rsCentroCosto);
                                    echo '<option value="'.$filaCentroCosto[0].'">'.ucwords(strtolower($filaCentroCosto[1])).'</option>';                                    
                                    $sqlCentroCosto1 = "SELECT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $centroCosto GROUP BY id_unico";
                                    $rsCentroCosto1 = $mysqli->query($sqlCentroCosto1);
                                    while ($filaCentroCosto1=mysqli_fetch_row($rsCentroCosto1)){
                                        echo '<option value="'.$filaCentroCosto1[0].'">'.ucwords(strtolower($filaCentroCosto1[1])).'</option>';
                                    }
                                }else{
                                ?>
                                    <option value="">Centro Costo</option>
                                    <?php
                                    $sqlCentroCosto = "SELECT id_unico,nombre FROM gf_centro_costo GROUP BY id_unico";
                                    $rsCentroCosto = $mysqli->query($sqlCentroCosto);
                                    while ($filaCentroCosto=mysqli_fetch_row($rsCentroCosto)){
                                        echo '<option value="'.$filaCentroCosto[0].'">'.ucwords(strtolower($filaCentroCosto[1])).'</option>';
                                    }
                                }
                                ?>                                 
                            </select>
                            <script type="text/javascript" >
                                $("#sltTercero").change(function() {
                                   var form_data  = {tercero:$("#sltTercero").val()};
                                    $.ajax({
                                        type: 'POST',
                                        url: "consultasFacturacion/consultarFechav.php",
                                        data:form_data,
                                        success: function (data) {
                                            if(data==0){
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
                                                $("#fechaV").val(fecAct);
                                            }else{
                                                var fechaV = data;
                                                var fecha = sumaFecha(fechaV,$("#fechaV").val());
                                                $("#fechaV").val(fecha);
                                            }                                            
                                        }
                                    });                                    
                                });
                                sumaFecha = function(d, fecha){
                                    var Fecha = new Date();
                                    var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
                                    var sep = sFecha.indexOf('/') != -1 ? '/' : '-'; 
                                    var aFecha = sFecha.split(sep);
                                    var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
                                    fecha= new Date(fecha);
                                    fecha.setDate(fecha.getDate()+parseInt(d));
                                    var anno=fecha.getFullYear();
                                    var mes= fecha.getMonth()+1;
                                    var dia= fecha.getDate();
                                    mes = (mes < 10) ? ("0" + mes) : mes;
                                    dia = (dia < 10) ? ("0" + dia) : dia;
                                    var fechaFinal = dia+sep+mes+sep+anno;
                                    return (fechaFinal);
                                }
                            </script>
                            <!-- Fecha de vencimiento -->
                            <label for="fechaV" class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Fecha Vencimiento:
                            </label>                                
                            <input class="col-sm-2 input-sm" value="<?php if(!empty($fechaVencimiento)){$valorFV = (String) $fechaVencimiento;$fechaSV = explode("-",$valorFV); echo $fechaSV[2].'/'.$fechaSV[1].'/'.$fechaSV[0];}else{echo date('d/m/Y');} ?>" type="text" name="fechaV" id="fechaV" class="form-control" style="margin-top:-5px;width:100px;height:26px" title="Ingrese la fecha" placeholder="Fecha" required>
                        </div>
                        <!-- Estado -->
                        <div class="form-group form-inline" style="margin-bottom: -0px">
                            <label class="col-sm-2 control-label">
                                    Estado:
                            </label>
                            <?php 
                            $sql = "SELECT id_unico,nombre FROM gp_estado_factura WHERE id_unico = 4";
                            $result = $mysqli->query($sql);
                            $row = mysqli_fetch_row($result);
                            ?>
                            <input class="col-sm-2" type="text" name="txtEstado" id="txtEstado" class="form-control" style="margin-top:-5px;width:100px;height:26px" value="<?php  if(!empty($estado)){echo ucwords(strtolower($estdo[0]));}else{echo ucwords(strtolower($row[1]));} ?>" title="Estado" placeholder="Estado" readonly/>
                        </div>
                        <div class="form-group form-inline" >
                            <label class="col-sm-2 control-label" for="txtDescripcion">
                                Descripción:
                            </label>
                            <textarea class="col-sm-2" style="margin-top:-5px;height:30px;width:350px;" class="area" rows="2" name="txtDescripcion" id="txtDescripcion"  maxlength="500" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')" ><?php if(!empty($descripcion)){echo $descripcion;}else{echo '';} ?></textarea>                                                                
                            <div class="col-sm-offset-7 col-sm-8" style="margin-top:-30px;margin-left:530px">                                                                    
                                <input type="hidden" name="id" id="id" value="<?php echo $idP; ?>" />
                                <div class="col-sm-1" style="">
                                    <?php if(!empty($factura) && $estado==4){ ?>
                                    <a onclick="javascript:modificarFacturacion()" id="btnModificar" class="btn sombra btn-primary" title="Modificar comprobante"><li class="glyphicon glyphicon-floppy-disk"></li></a>
                                    <?php    
                                    }else{ ?>
                                        <button type="submit" id="btnGuardar" class="btn sombra btn-primary" title="Guardar factura"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                    <?php    
                                    } ?>                                                                               
                                </div>
                                <div class="col-sm-1">
                                    <a class="btn sombra btn-primary" id="btnImprimir" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></a>                                                                              
                                </div> 
                                <div class="col-sm-1">
                                    <?php if(!empty($factura) & $estado==4){ ?>
                                        <a id="btnCancelarM" onclick="javascript:cancelarM()" class="btn sombra btn-primary" style="width: 40px" title="Cancelar modificación"><li class="glyphicon glyphicon glyphicon-remove"></li></a>
                                    <?php    
                                    }else{ ?>
                                        <a id="btnNuevo" onclick="javascript:nuevo()" class="btn sombra btn-primary" style="width: 40px" title="Ingresar nueva factura"><li class="glyphicon glyphicon-plus"></li></a>
                                        <a id="btnCancelarP" onclick="javascript:cancelarN()" class="btn sombra btn-primary" style="display: none;width: 40px" title="Cancelar ingreso de datos"><li class="glyphicon glyphicon glyphicon-remove"></li></a>
                                    <?php    
                                    }                                        
                                    ?>                                        
                                </div>
                                <script>
                                    /*$(document).ready(function(){
                                       $("#btnImprimir").click(function(){
                                           var form_data = {
                                               is_ajax:1,
                                               numero:$("#txtNumero").val()
                                           };
                                           var result='';
                                           $.ajax({
                                               type: 'POST',
                                               url:"consultarComprobanteIngreso/impreso.php",
                                               data:form_data,
                                               success: function (data) {
                                                   result = JSON.parse(data);
                                                   if(result==true){
                                                       window.location.reload();
                                                   }
                                                   console.log(data);
                                               }
                                           });
                                       }); 
                                    });*/

                                    function cancelarM(){
                                        var form_data = {
                                            is_ajax:1,
                                            session:2
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/vaciarSessiones.php",
                                            data: form_data,
                                            success: function (data) {                                                                                                        
                                                window.location.reload();                                                    
                                            }
                                        });
                                    }
                                    function nuevo(){
                                        var form_data = {
                                            is_ajax:1,
                                            session:2
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/vaciarSessiones.php",
                                            data: form_data,
                                            success: function (data) {                                                                                                        
                                                window.location.reload();                                                    
                                            }
                                        });                                        
                                    }
                                    $("#slttipofactura").change(function(){
                                        var tipofactura = $("#slttipofactura").val();
                                        if(tipofactura=='""' || tipofactura==0){
                                            $("#mdltipofactura").modal('show');
                                        }else{
                                            var form_data = {
                                                is_ajax:1,
                                                numero:$("#slttipofactura").val(),
                                                nuevos:2
                                            };

                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasBasicas/generarNuevos.php",
                                                data: form_data,
                                                success: function (data) {                                                                            
                                                    $("#txtNumeroF").val(data);                                                    
                                                }
                                            });
                                        }
                                    });
                                    function cancelarN(){
                                        $("#btnCancelarP").css('display','none');
                                        $("#btnNuevo").css('display','block');
                                        $("#btnGuardar").attr('disabled',true);
                                        $("#txtNumeroF").val("");                                        
                                    }
                                </script>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-10 text-center " style="margin-top:5px;margin-left:-20px" align="">                    
                    <div class="client-form" style="margin-left:60px" class="col-sm-12">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetalleFactura.php" style="margin-top:-15px">
                            <div class="col-sm-1" style="margin-right:20px;">
                                <div class="form-group" style="margin-top: 5px;"  align="left">                                    
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Concepto:
                                    </label>
                                    <select name="sltConcepto" id="sltConcepto" class="form-control" style="width:100px;height:26px;padding:2px" title="Seleccione tercero" required="">
                                        <option value="0">Concepto</option>
                                        <?php       
                                        $sql = "SELECT cnp.id_unico,cnp.nombre FROM gf_concepto con
                                                LEFT JOIN gp_concepto cnp ON cnp.concepto_financiero = con.id_unico WHERE cnp.id_unico IS NOT NULL";
                                        $result = $mysqli->query($sql);
                                        while($row = mysqli_fetch_row($result)){
                                            echo '<option value="'.$row[0].'">'.$row[1].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-1" style="margin-right:20px;">                                
                                <div class="form-group" style="margin-top: 5px;" align="left">
                                    <label class="control-label">
                                        <strong class="obligado"></strong>Cantidad:
                                    </label>
                                    <input type="text" name="txtCantidad" placeholder="Cantidad" onkeypress="return justNumbers(event);" id="txtCantidad" maxlength="50" style="height:26px;padding:2px;width:100px;" required=""/>                                    
                                </div>
                            </div>
                            <div class="col-sm-1" style="margin-right:20px;">
                                <div class="form-group" style="margin-top: 5px;" align="left">                                                                   
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Valor Unitario:
                                    </label>
                                    <!--<input type="text" name="txtValor" placeholder="Valor" onkeypress="return justNumbers(event);" id="txtValor" maxlength="50" style="height:26px;padding:2px;width:100px" required=""/>-->
                                    <select class="form-control" name="sltValor" id="sltValor" title="Seleccione valor" style="width:100px;height:26px;padding:2px" required>
                                        <option value="">Valor Unitario</option>                                        
                                    </select>                                    
                                </div>                               
                            </div>    
                            <div class="col-sm-1" style="margin-right:20px;">
                                <div class="form-group" style="margin-top: 5px;" align="left">
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Iva:
                                    </label>                                    
                                    <input type="text" name="txtIva" placeholder="Iva" onkeypress="return justNumbers(event);" id="txtIva" maxlength="50" style="height:26px;padding:2px;width:100px" required="" readonly=""/>                                    
                                </div>
                            </div>
                            <div class="col-sm-1" style="margin-right:20px;">
                                <div class="form-group" style="margin-top: 5px;" align="left">
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Impoconsumo:
                                    </label>
                                    <input type="text" name="txtImpoconsumo" placeholder="Impoconsumo" onkeypress="return justNumbers(event);" id="txtImpoconsumo" maxlength="50" style="height:26px;padding:2px;width:100px" required="" readonly=""/>                                    
                                </div>
                            </div>                            
                            <div class="col-sm-1" style="margin-right:20px;">
                                <div class="form-group" style="margin-top: 5px;" align="left">
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Ajuste al Peso:
                                    </label>
                                    <input type="text" name="txtAjustePeso" placeholder="Ajuste al Peso" onkeypress="return justNumbers(event);" id="txtAjustePeso" maxlength="50" style="height:26px;padding:2px;width:100px" required="" readonly=""/>                                                                        
                                    <?php 
                                    $sqlAjuste = "SELECT valor FROM gs_parametros_basicos WHERE id_unico = 4";
                                    $rsAjuste = $mysqli->query($sqlAjuste);
                                    $ajuste = mysqli_fetch_row($rsAjuste);
                                    ?>
                                    <script type="text/javascript" >
                                        var Impo = 0.00;
                                        var iva = 0.00;
                                        var valor = 0;
                                        var totalIva = 0;
                                        var totalImpo = 0;
                                        var ajuste = <?php echo $ajuste[0]; ?>;
                                        $(document).ready(function () {
                                            $("#sltValor").attr('disabled',true);
                                            $("#txtAjuste").attr('disabled',true);
                                        });
                                        $("#sltConcepto").change(function() {
                                            var form_data = {
                                                concepto:$("#sltConcepto").val(),
                                                proceso:1
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasFacturacion/consultarValor.php",
                                                data:form_data,
                                                success: function (data) {
                                                    if(data!=""){
                                                        $("#sltValor").attr('disabled',false);
                                                        $("#txtAjuste").attr('disabled',false);
                                                        $("#sltValor").html(data).fadeIn();
                                                    }else{
                                                        $("#sltValor").attr('disabled',true);
                                                        $("#txtAjuste").attr('disabled',true);
                                                    }                                                    
                                                }
                                            });
                                        });
                                        
                                        $("#sltValor").change(function(){
                                            var form_data={
                                                concepto:$("#sltConcepto").val(),
                                                proceso:2
                                            };
                                            
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasFacturacion/consultarValor.php",
                                                data:form_data,
                                                success: function (data) {
                                                    iva = data;
                                                    var cantidad = $("#txtCantidad").val();                                            
                                                    if(cantidad==0 || cantidad==''){
                                                    cantidad = 1;
                                                    }else{
                                                        cantidad = $("#txtCantidad").val();                                            
                                                    }                                                    
                                                    valor = $("#sltValor").val();
                                                    total = cantidad*valor;
                                                    totalIva = (iva*total)/100;
                                                    $("#txtIva").val(totalIva.toFixed(2));
                                                }
                                            });
                                        });
                                        
                                        $("#sltValor").change(function(){
                                            var form_data={
                                                concepto:$("#sltConcepto").val(),
                                                proceso:3
                                            };
                                            
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasFacturacion/consultarValor.php",
                                                data:form_data,
                                                success: function (data) {
                                                    Impo = data;
                                                    var valor = $("#sltValor").val();                                                                                                        
                                                    var cantidad = $("#txtCantidad").val();                                            
                                                    if(cantidad==0 || cantidad==''){
                                                    cantidad = 1;
                                                    }else{
                                                        cantidad = $("#txtCantidad").val();                                            
                                                    }
                                                    
                                                    var oper = (valor * cantidad);
                                                    var totalImpo = (Impo*oper)/100;
                                                    $("#txtImpoconsumo").val(totalImpo.toFixed(2));
                                                    var suma = oper + totalIva + totalImpo;
                                                    var redondo = redondeaAlAlza(suma,ajuste) ;
                                                    var ajusteT = redondeoTotal(suma,ajuste);
                                                    $("#txtAjustePeso").val(redondo);
                                                    $("#txtValorA").val(ajusteT);
                                                }
                                            });
                                        });                                                                                 
                                       
                                        /*
                                         * En esta función enviamos el valor el cual es número, esta función
                                         * redondea automaticamente los valores
                                         * @param {double} numero
                                         * @param {double} decimales
                                         * @returns {redo}
                                         */
                                        function redondeo(numero, decimales){
                                            var flotante = parseFloat(numero);
                                            var resultado = Math.round(flotante*Math.pow(10,decimales))/Math.pow(10,decimales);
                                            var falta = resultado - flotante;
                                            var redo = falta.toFixed(2);
                                            return redo;
                                        }
                                        
                                        /*
                                         * x = al número o valor decimal
                                         * r = al valor de redondeo puede ser 1,10,100.. etc
                                         * t = es el valor que hace falta para el redondeo
                                         * @param {double} x 
                                         * @param {double} r
                                         * @returns {t}
                                         */
                                        function redondeaAlAlza(x,r) {
                                            xx = Math.floor(x/r)
                                            if (xx!=x/r) {xx++}
                                            var val = (xx*r);
                                            var rt = (val-x);
                                            var t = rt.toFixed(2);
                                            return t;
                                        }
                                        
                                        /*
                                         * 
                                         * @param {type} id
                                         * @returns {undefined}
                                         */
          
                                        function redondeoTotal(valor,ajuste) {
                                            xx = Math.floor(valor/ajuste);
                                            if(xx!=valor/ajuste){xx++}
                                            var val = (xx*ajuste);
                                            return val;
                                        }
                                    </script>
                                </div>
                            </div>
                            <div class="col-sm-1" style="margin-right:20px;">
                                <div class="form-group" style="margin-top: 5px;" align="left">
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Valor Total:
                                    </label>
                                    <input type="text" name="txtValorA" placeholder="Valor Total Ajustado" onkeypress="return justNumbers(event);" id="txtValorA" maxlength="50" style="height:26px;padding:2px;width:100px" required="" readonly=""/>                                    
                                </div>
                            </div>                            
                            <div class="col-sm-1" align="left" style="margin-top:26px;margin-left:-60px;margin-right:30px; ">
                                <button type="submit" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                                <input type="hidden" name="MM_insert" >
                            </div>                                                                                   
                        </form>                        
                    </div>
                </div>
            <div class="col-sm-8" style="margin-top:-20px">
                <?php 
                    if(!empty($_SESSION['idFactura'])){
                        $factura = $_SESSION['idFactura'];
                        $result = "";
                        $sql = "SELECT 
                                    dtf.id_unico,
                                    cnp.id_unico,
                                    cnp.nombre,
                                    dtf.cantidad,
                                    dtf.valor,
                                    dtf.iva,
                                    dtf.impoconsumo,
                                    dtf.ajuste_peso,
                                    fat.numero_factura,
                                    dtf.valor_total_ajustado
                            FROM gp_detalle_factura dtf
                            LEFT JOIN gp_factura fat ON fat.id_unico = dtf.factura
                            LEFT JOIN gp_concepto cnp ON dtf.concepto_tarifa = cnp.id_unico
                            WHERE dtf.factura =$factura ";
                        $result = $mysqli->query($sql);
                    }                    
                    ?>
                    <!-- Campos ocultos en los que guaramos la id anterior y la nueva id -->
                    <input type="hidden" id="idPrevio" value="">
                    <input type="hidden" id="idActual" value="">  
                    <?php 
                    $sumaCantidad = 0;
                    $sumaValor = 0;
                    $sumaIva = 0;
                    $sumaImpo = 0;
                    $sumaAjuste=0;
                    $sumaValortotal = 0;
                    ?>
                    <div class="table-responsive contTabla" >
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>    
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%" class="cabeza"></td>
                                    <td class="cabeza"><strong>Concepto</strong></td>
                                    <td class="cabeza"><strong>Cantidad</strong></td>
                                    <td class="cabeza"><strong>Valor</strong></td>
                                    <td class="cabeza"><strong>Iva</strong></td>
                                    <td class="cabeza"><strong>Impoconsumo</strong></td>
                                    <td class="cabeza"><strong>Ajuste del peso</strong></td>
                                    <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%" class="cabeza"></th>
                                    <th class="cabeza">Concepto</th>
                                    <th class="cabeza">Cantidad</th>
                                    <th class="cabeza">Valor</th>
                                    <th class="cabeza">Iva</th>
                                    <th class="cabeza">Impoconsumo</th>
                                    <th class="cabeza">Ajuste del peso</th>
                                    <th class="cabeza">Valor Total Ajustado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                #Se crear labels en los que imprimimos el valor y a us vez creamos los campos para modificar los cuales ocultamos
                                while($row=mysqli_fetch_row($result)){ ?>
                                <tr>
                                    <td class="oculto"></td>
                                    <td class="campos">
                                        <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                                            <li class="glyphicon glyphicon-trash"></li>
                                        </a>
                                        <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);javascript:cargarValor(<?php echo $row[0]; ?>);javascript:cambioValor(<?php echo $row[0]; ?>);javascript:calcularValores(<?php echo $row[0]; ?>);javascript:calcularValoresEscrito(<?php echo $row[0]; ?>)">
                                            <li class="glyphicon glyphicon-edit"></li>
                                        </a>                                            
                                    </td>
                                    <td class="campos">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="concepto'.$row[0].'">'.ucwords(strtolower($row[2])).'</label>'; ?>
                                        <select class="col-sm-12 campoD" name="sltconcepto<?php echo $row[0] ?>" id="sltconcepto<?php echo $row[0] ?>" title="Seleccione concepto" style="display:none;padding:2px">
                                            <option value="<?php echo $row[1]; ?>"><?php echo $row[2]; ?></option>
                                                <?php 
                                                $sqlCn = "SELECT cnp.id_unico,cnp.nombre FROM gf_concepto con
                                                LEFT JOIN gp_concepto cnp ON cnp.concepto_financiero = con.id_unico
                                                WHERE cnp.id_unico != $row[1]
                                                ORDER BY cnp.nombre DESC";
                                                $resc = $mysqli->query($sqlCn);
                                                while($row2 = mysqli_fetch_row($resc)){
                                                    echo '<option value="'.$row2[0].'">'.$row2[1].'</option>';
                                                }
                                                ?>
                                        </select>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblCantidad'.$row[0].'">'.$row[3].'</label>'; 
                                              echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtcantidad'.$row[0].'" id="txtcantidad'.$row[0].'" value="'.$row[3].'" />';
                                              $sumaCantidad += $row[3];
                                        ?>                                        
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblValor'.$row[0].'">'.number_format($row[4], 2, '.', ',').'</label>';            
                                        //echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtValor'.$row[0].'" id="txtValor'.$row[0].'" value="'.$row[4].'" />';
                                        $sumaValor += $row[4];
                                        ?>
                                        <select class="col-sm-12 campoD" name="txtValor<?php echo $row[0] ?>" id="txtValor<?php echo $row[0] ?>" title="Seleccione valor" style="display:none;padding:2px">
                                        </select>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblIva'.$row[0].'">'.number_format($row[5], 2, '.', ',').'</label>'; 
                                              echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtIva'.$row[0].'" id="txtIva'.$row[0].'" value="'.$row[5].'" />';
                                              $sumaIva += $row[5];
                                        ?>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblImpoconsumo'.$row[0].'">'.number_format($row[6], 2, '.', ',').'</label>'; 
                                              echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtImpoconsumo'.$row[0].'" id="txtImpoconsumo'.$row[0].'" value="'.$row[6].'" />';
                                              $sumaImpo += $row[6];
                                        ?>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblAjustepeso'.$row[0].'">'.number_format($row[7], 2, '.', ',').'</label>'; 
                                              echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtAjustepeso'.$row[0].'" id="txtAjustepeso'.$row[0].'" value="'.$row[7].'" />';
                                              $sumaAjuste += $row[7];
                                        ?>                                        
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblValorAjuste'.$row[0].'">'.number_format($row[9], 2, '.', ',').'</label>'; 
                                              echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-9 campoD text-left"  type="text" name="txtValorAjuste'.$row[0].'" id="txtValorAjuste'.$row[0].'" value="'.$row[9].'" />';
                                              $sumaValortotal += $row[9];
                                        ?>                                        
                                        <div >
                                            <table id="tab<?php echo $row[0] ?>" style="padding:0px;background-color:transparent;background:transparent;" class="col-sm-1">
                                                <tbody>
                                                    <tr style="background-color:transparent;">
                                                        <td style="background-color:transparent;">
                                                            <a  href="#<?php echo $row[0];?>" title="Guardar" id="guardar<?php echo $row[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $row[0]; ?>)">
                                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                                            </a>
                                                        </td>
                                                        <td style="background-color:transparent;">
                                                            <a href="#<?php echo $row[0];?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>" style="display: none" onclick="javascript:cancelar(<?php echo $row[0];?>)" >
                                                                <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <?php    
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
            </div>
            <div class="col-sm-8 col-sm-offset-1" style="margin-top:5px;"> 
                <div class="col-sm-1" style="margin-right:30px">
                    <div class="form-group" style="" align="left">                                    
                        <label class="control-label">
                            <strong>Totales:</strong>
                        </label>                                
                    </div>
                </div> 
                <div class="col-sm-1" style="margin-right:20px">
                    <label class="control-label valorLabel" title="Total cantidad"><?php echo number_format($sumaCantidad, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right:30px">
                    <label class="control-label valorLabel" title="Total valor"><?php echo number_format($sumaValor, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right:50px">
                    <label class="control-label valorLabel" title="Total iva"><?php echo number_format($sumaIva, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right:30px">
                    <label class="control-label valorLabel" title="Total impuesto al consumo"><?php echo number_format($sumaImpo, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right:30px">
                    <label class="control-label valorLabel" title="Total ajsute al peso"><?php echo number_format($sumaAjuste, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right:30px">
                    <label class="control-label valorLabel" title="Total valor ajustado"><?php echo number_format($sumaValortotal, 2, '.', ','); ?></label>
                </div>
            </div>
            <div class="col-sm-8 col-sm-1" style="margin-left:-100px"  >
                <table class="tablaC table-condensed text-center" align="center" style="margin-top:-375px;">
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
                                    <!-- onclick="return ventanaSecundaria('registrar_GF_DESTINO.php')" -->
                                    <a onclick="javascript:abrirMT()" class="btn btn-primary btnInfo">PERSONA</a>                                       
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_CENTRO_COSTO.php">CENTRO COSTO</a>
                                </td>
                            </tr>                                                           
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
    <script>
        //Función para eliminar 
        function eliminar(id){
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarDetalleFactura.php?id="+id,
                    success: function (data) {
                    result = JSON.parse(data);
                    if(result==true)
                      $("#mdlEliminado").modal('show');
                    else
                      $("#mdlNoeliminado").modal('show');
                    }
                });
            });
        }
        //Función para guardar datos del detalle
        function guardarCambios(id){            
            var sltConcepto = 'sltconcepto'+id;
            var txtCantidad = 'txtcantidad'+id;
            var txtValor = 'txtValor'+id;
            var txtIva = 'txtIva'+id;            
            var txtImpoconsumo = 'txtImpoconsumo'+id;
            var txtAjustepeso = 'txtAjustepeso'+id;
            var txtValorAjuste = 'txtValorAjuste'+id
            var form_data = {
                id:id,
                concepto:$("#"+sltConcepto).val(),
                cantidad:$("#"+txtCantidad).val(),
                valor:$("#"+txtValor).val(),
                iva:$("#"+txtIva).val(),
                impoconsumo:$("#"+txtImpoconsumo).val(),
                ajustepeso:$("#"+txtAjustepeso).val(),
                valorAjuste:$("#"+txtValorAjuste).val()
            };
            var result = '';
            $.ajax({
                type: 'POST',
                url: "json/modificarDetalleFactura.php",
                data:form_data,
                success: function (data) {
                    console.log(data);
                    result = JSON.parse(data);
                    if(result==true){
                        $("#mdlModificado").modal('show');
                    }else{
                        $("#mdlNomodificado").modal('show');
                    };
                }
            });
        }
    </script>
    <script type="text/javascript">        
        //función para ocultar los label y mostrar los campos para modificar
        function modificar(id){
            //En el que valida si el campos idPrevio tiene un valor 
            //en el que asignamos los nombres de los labels y campos
            //y el asignamos la idPrevio y a su vez solo mostramos los labels
            if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
                var lblConceptoC = 'concepto'+$("#idPrevio").val();
                var sltConceptoC = 'sltconcepto'+$("#idPrevio").val();                
                var lblCantidadC = 'lblCantidad'+$("#idPrevio").val();
                var txtCantidadC = 'txtcantidad'+$("#idPrevio").val();                
                var lblValorC = 'lblValor'+$("#idPrevio").val();
                var txtValorC = 'txtValor'+$("#idPrevio").val();                
                var lblIvaC = 'lblIva'+$("#idPrevio").val();
                var txtIvaC = 'txtIva'+$("#idPrevio").val();                
                var lblImpoconsumoC = 'lblImpoconsumo'+$("#idPrevio").val();
                var txtImpoconsumoC = 'txtImpoconsumo'+$("#idPrevio").val();                
                var lblAjustepesoC = 'lblAjustepeso'+$("#idPrevio").val();
                var txtAjustepesoC = 'txtAjustepeso'+$("#idPrevio").val();                                
                var guardarC = 'guardar'+$("#idPrevio").val();
                var cancelarC = 'cancelar'+$("#idPrevio").val();
                var tablaC = 'tab'+$("#idPrevio").val();
                var lblValorAjusteC = 'lblValorAjuste'+$("#idPrevio").val();
                var txtValorAjusteC = 'txtValorAjuste'+$("#idPrevio").val();
                
                $("#"+lblConceptoC).css('display','block');
                $("#"+sltConceptoC).css('display','none');
                $("#"+lblCantidadC).css('display','block');
                $("#"+txtCantidadC).css('display','none');
                $("#"+lblValorC).css('display','block');
                $("#"+txtValorC).css('display','none');
                $("#"+lblIvaC).css('display','block');
                $("#"+txtIvaC).css('display','none');
                $("#"+lblImpoconsumoC).css('display','block');
                $("#"+txtImpoconsumoC).css('display','none');
                $("#"+lblAjustepesoC).css('display','block');
                $("#"+txtAjustepesoC).css('display','none');   
                $("#"+guardarC).css('display','none');
                $("#"+cancelarC).css('display','none');
                $("#"+tablaC).css('display','none');
                $("#"+lblValorAjusteC).css('display','block');
                $("#"+txtValorAjusteC).css('display','none');
            }
            //aqui creamos las variables similares a las anteriores en la que asignamos el nombre y el id 
            var lblConcepto = 'concepto'+id;
            var sltConcepto = 'sltconcepto'+id;
            var lblCantidad = 'lblCantidad'+id;
            var txtCantidad = 'txtcantidad'+id;
            var lblValor = 'lblValor'+id;
            var txtValor = 'txtValor'+id;
            var lblIva = 'lblIva'+id;
            var txtIva = 'txtIva'+id;
            var lblImpoconsumo = 'lblImpoconsumo'+id;
            var txtImpoconsumo = 'txtImpoconsumo'+id;
            var lblAjustepeso = 'lblAjustepeso'+id;
            var txtAjustepeso = 'txtAjustepeso'+id;
            var lblValorAjuste = 'lblValorAjuste'+id;
            var txtValorAjuste = 'txtValorAjuste'+id;
            var guardar = 'guardar'+id;
            var cancelar = 'cancelar'+id;
            var tabla = 'tab'+id;
            //ocultamos los labels y mostramos los campos ocultos
            $("#"+sltConcepto).css('display','block');                               
            $("#"+lblConcepto).css('display','none');
            $("#"+txtCantidad).css('display','block');
            $("#"+lblCantidad).css('display','none');
            $("#"+txtValor).css('display','block');
            $("#"+lblValor).css('display','none');
            $("#"+txtIva).css('display','block');
            $("#"+lblIva).css('display','none');
            $("#"+txtImpoconsumo).css('display','block');
            $("#"+lblImpoconsumo).css('display','none');
            $("#"+lblAjustepeso).css('display','none');
            $("#"+txtAjustepeso).css('display','block');
            $("#"+lblValorAjuste).css('display','none');
            $("#"+txtValorAjuste).css('display','block')
            $("#"+guardar).css('display','block');
            $("#"+cancelar).css('display','block');
            $("#"+tabla).css('display','block');
            //Asignamos el valor de la id al campo id actual
            $("#idActual").val(id);
            //Y preguntamos si el valor del idPrevio es diferente a la id
            //y se la asignamos
            if($("#idPrevio").val() != id){
                $("#idPrevio").val(id);   
            }
           }
    </script>
    <script type="text/javascript">
    function cambioValor(id){
        $("#sltconcepto"+id).change(function() {
            var form_data = {
                concepto:$("#sltconcepto"+id).val(),
                proceso:1
            };
            $.ajax({
                type: 'POST',
                url: "consultasFacturacion/consultarValor.php",
                data:form_data,
                success: function (data) {
                    if(data!=""){                        
                        $("#txtValor"+id).html(data).fadeIn();
                    }                                                    
                }
            });
        });
    }
    /*
     * 
     * @param {int} id      
     */    
    
    /*
     * 
     * @param {type} id
     * @returns {undefined}$("#txtValor"+id).change(function(){
            var valor = $("#txtValor"+id).val();
            var totali = valor * iva;
            var rta = totali.toFixed(2);
            $("#txtIva"+id).val(rta);
            
            var totalImpuest = valor*impuesto;
            var rtai = totalImpuest.toFixed(2);
            $("#txtImpoconsumo"+id).val(rtai);
            
            var cantidad = $("#txtcantidad"+id).val();                                            
            if(cantidad==0 || cantidad==''){
                cantidad = 1;
            }else{
                cantidad = $("#txtcantidad"+id).val();                                            
            }
            var ivaC = parseFloat($("#txtIva"+id).val());
            var impoC = parseFloat($("#txtImpoconsumo"+id).val());                                                                                                       
            var oper = (valor * cantidad);
            var suma = oper + ivaC + impoC;
            var ajusteP = redondeaAlAlza(suma,ajuste);
            var valorTotal = redondeoTotal(suma,ajuste);
            $("#txtAjustepeso"+id).val(ajusteP);
            $("#txtValorAjuste"+id).val(valorTotal);
        });
     */
    function calcularValores(id) { 
        var ajuste = <?php echo $ajuste[0]; ?>;
        var Impo = 0.00;
        var iva = 0.00;
        var valor = 0;
        var totalIva = 0;
        var totalImpo = 0;
        $("#txtValor"+id).change(function(){
            var form_data={
                concepto:$("#sltconcepto"+id).val(),
                proceso:2
            };

            $.ajax({
                type: 'POST',
                url: "consultasFacturacion/consultarValor.php",
                data:form_data,
                success: function (data) {
                    iva = data;
                    valor = $("#txtValor"+id).val();
                    totalIva = (iva*valor)/100;
                    $("#txtIva"+id).val(totalIva.toFixed(2));
                }
            });
        });

        $("#txtValor"+id).change(function(){
            var form_data={
                concepto:$("#sltconcepto"+id).val(),
                proceso:3
            };

            $.ajax({
                type: 'POST',
                url: "consultasFacturacion/consultarValor.php",
                data:form_data,
                success: function (data) {
                    Impo = data;
                    valor = $("#txtValor"+id).val();
                    totalImpo = (Impo*valor)/100;
                    $("#txtImpoconsumo"+id).val(totalImpo.toFixed(2));

                    var cantidad = $("#txtcantidad"+id).val();                                            
                    if(cantidad==0 || cantidad==''){
                        cantidad = 1;
                    }else{
                        cantidad = $("#txtcantidad"+id).val();                                            
                    }

                    var oper = (valor * cantidad);
                    var suma = oper + totalIva + totalImpo;
                    var redondo = redondeaAlAlza(suma,ajuste) ;
                    var ajusteT = redondeoTotal(suma,ajuste);
                    $("#txtAjustepeso"+id).val(redondo);
                    $("#txtValorAjuste"+id).val(ajusteT);
                }
            });
        });                                         
    }        
    
    function calcularValoresEscrito(id) {
        var ajuste = <?php echo $ajuste[0]; ?>;
        var Impo = 0.00;
        var iva = 0.00;
        var valor = 0;
        var totalIva = 0;
        var totalImpo = 0;
        $("#txtcantidad"+id).keyup(function(){
            var form_data={
                concepto:$("#sltconcepto"+id).val(),
                proceso:2
            };

            $.ajax({
                type: 'POST',
                url: "consultasFacturacion/consultarValor.php",
                data:form_data,
                success: function (data) {
                    iva = data;
                    valor = $("#txtValor"+id).val();
                    totalIva = (iva*valor)/100;
                    $("#txtIva"+id).val(totalIva.toFixed(2));
                }
            });
            
            var form_data={
                concepto:$("#sltconcepto"+id).val(),
                proceso:3
            };

            $.ajax({
                type: 'POST',
                url: "consultasFacturacion/consultarValor.php",
                data:form_data,
                success: function (data) {
                    Impo = data;
                    valor = $("#txtValor"+id).val();
                    totalImpo = (Impo*valor)/100;
                    $("#txtImpoconsumo"+id).val(totalImpo.toFixed(2));

                    var cantidad = $("#txtcantidad"+id).val();                                            
                    if(cantidad==0 || cantidad==''){
                        cantidad = 1;
                    }else{
                        cantidad = $("#txtcantidad"+id).val();                                            
                    }

                    var oper = (valor * cantidad);
                    var suma = oper + totalIva + totalImpo;
                    var redondo = redondeaAlAlza(suma,ajuste) ;
                    var ajusteT = redondeoTotal(suma,ajuste);
                    $("#txtAjustepeso"+id).val(redondo);
                    $("#txtValorAjuste"+id).val(ajusteT);
                }
            });
        });                
    }   
    </script>
    <script type="text/javascript">
        function cancelar(id){
            //Creamos las variables en la que cargamos los nombres de los campos y label y le concatenamos la id
            var lblConcepto = 'concepto'+id;
            var sltConcepto = 'sltconcepto'+id;
            var lblCantidad = 'lblCantidad'+id;
            var txtCantidad = 'txtcantidad'+id;
            var lblValor = 'lblValor'+id;
            var txtValor = 'txtValor'+id;
            var lblIva = 'lblIva'+id;
            var txtIva = 'txtIva'+id;
            var lblImpoconsumo = 'lblImpoconsumo'+id;
            var txtImpoconsumo = 'txtImpoconsumo'+id;
            var lblAjustepeso = 'lblAjustepeso'+id;
            var txtAjustepeso = 'txtAjustepeso'+id;
            var lblValorAjuste = 'lblValorAjuste'+id;
            var txtValorAjuste = 'txtValorAjuste'+id;
            var guardar = 'guardar'+id;
            var cancelar = 'cancelar'+id;
            var tabla = 'tab'+id;
            //ocultamos los campos y mostraos los labels            
            $("#"+lblConcepto).css('display','block');
            $("#"+sltConcepto).css('display','none');
            $("#"+lblCantidad).css('display','block');
            $("#"+txtCantidad).css('display','none');                                           
            $("#"+lblValor).css('display','block');
            $("#"+txtValor).css('display','none');
            $("#"+lblIva).css('display','block');
            $("#"+txtIva).css('display','none');
            $("#"+lblImpoconsumo).css('display','block');
            $("#"+txtImpoconsumo).css('display','none');
            $("#"+lblAjustepeso).css('display','block');
            $("#"+txtAjustepeso).css('display','none'); 
            $("#"+lblValorAjuste).css('display','block');
            $("#"+txtValorAjuste).css('display','none');
            $("#"+guardar).css('display','none');
            $("#"+cancelar).css('display','none');
            $("#"+tabla).css('display','none');
        }
    </script>
    <script>     
        function modificarFacturacion(){
            var id = $("#id").val();
            var tipofactura = $("#slttipofactura").val();
            var fecha = $("#fecha").val();
            var numero = $("#txtNumeroF").val();
            var tercero = $("#sltTercero").val();
            var centroCosto = $("#sltCentroCosto").val();
            var fechavence = $("#fechaV").val();
            var descripcion = $("#txtDescripcion").val();
            
            var form_data = {
                id:id,
                tipofactura:tipofactura,
                fecha:fecha,
                numeroFactura:numero,
                tercero:tercero,
                centrocosto:centroCosto,
                fechaVencimiento:fechavence,
                descripcion:descripcion
            };
            
            var result='';
            $.ajax({
                type: 'POST',
                url: "json/modificarFacturacionJson.php",
                data:form_data,
                success: function (data) {
                    result = JSON.parse(data);
                    if (result==true) {
                        $("#mdlModificado").modal('show');
                    }else{
                        $("#mdlNomodificado").modal('show');
                    }
                }
            });
        }
        
        $(document).ready(function(){
            $("#fecha").change(function(){
                var form_data = {
                    fecha:$("#fecha").val()
                };
                var result = ' ';
                $.ajax({
                    type: 'POST',
                    url: "consultasFacturacion/validarFecha.php",
                    data:form_data,
                    success: function (data) {
                        result= JSON.parse(data);
                        if(result==false){
                            $("#mdlfecha").modal('show');
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
                            $("#fecha").val(fecAct);
                        }
                    }
                });
            });
        });
        $(document).ready(function(){
            $("#fecha").keyup(function(){
                var x=new Date();
                var date = $("#fecha").val();
                var fecha = date.split("/");
                x.setFullYear(fecha[2],fecha[1]-1,fecha[0]);
                var today = new Date();
                var fecha = new Date();
                if(x<=today){
                    $("#mdlfecha").modal('show');                    
                    var dia = fecha.getDate();
                    var mes = fecha.getMonth() + 1;
                    if(dia < 10){
                        dia = "0" + dia;
                    }
                    if(mes < 10){
                        mes = "0" + mes;
                    }
                    var fecActual = dia + "/" + mes + "/" + fecha.getFullYear();
                    $("#fecha").val(fecActual);
                }
            });
        });
        
        function cargarValor(id){            
            $("#sltconcepto"+id).append(function(){                

                var form_data = {
                    is_ajax:1,
                    data:+id
                };

                $.ajax({
                    type: 'POST',
                    url: "consultasFacturacion/consultarValorT.php",
                    data:form_data,
                    success: function (data) {
                        $("#txtValor"+id).html(data).fadeIn();
                    }
                });
            });            
        }
        
        
    </script>
    <?php require_once './footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <!-- Modales de modificado -->
    <div class="modal fade" id="mdlModificado" role="dialog" align="center" >
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
    <div class="modal fade" id="mdltipofactura" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un tipo de factura.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNomodificado" role="dialog" align="center" >
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
    <!-- Modal de fecha -->
    <div class="modal fade" id="mdlfecha" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">          
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>La fecha ingresada es menor a la fecha del ultima factura.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div> 
    <!-- Modal de eliminado -->
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
    <div class="modal fade" id="mdlEliminado" role="dialog" align="center" >
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
    <div class="modal fade" id="mdlNoeliminado" role="dialog" align="center" >
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
        $('#btnModifico').click(function(){
            document.location.reload();
        });
    </script>
    <script type="text/javascript">
        $('#btnNoModifico').click(function(){
            document.location.reload();
        });
    </script>
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
    <script type="text/javascript">
        $('#btnG').click(function(){
            document.location.reload();
        });
    </script>
    <script type="text/javascript">    
        $('#btnG2').click(function(){  
            document.location.reload();
        });
    </script>
</body>
</html>
