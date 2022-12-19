<?php
require_once './Conexion/conexion.php';
require_once './head_listar.php';
?>
<!-- select2 -->
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<title>Recaudo facturación</title>
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
</head>
<body >        
    <div class="container-fluid text-left">
        <div class="row content">
            <?php require_once './menu.php'; ?>
            <div class="col-sm-9 text-center" style="margin-top:-22px;">
                <h2 class="tituloform" align="center">Recaudo Facturación</h2>
                <div class="client-form contenedorForma" style="margin-top:-7px;">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarPagoJson.php" style="margin-bottom:-20px">
                        <?php 
                        $idP = 0;
                        $tipoPago = 0;
                        $fecha = '';
                        $numeroPago = 0;
                        $banco = 0;                        
                        $estado = '';                        
                        $pago = 0;
                        $responsable = 0;
                        if(!empty($_SESSION['pago'])){
                            $pago = $_SESSION['pago'];
                            $idA = $_SESSION['idpago'];
                            $sqlPago= "SELECT
                                                id_unico,
                                                tipo_pago,
                                                fecha_pago,
                                                numero_pago,
                                                banco,
                                                estado,
                                                responsable
                                        FROM
                                          gp_pago 
                                        WHERE id_unico = $idA";
                            $rsPago= $mysqli->query($sqlPago);
                            $valoresPago = mysqli_fetch_row($rsPago);
                            
                            $idP = $valoresPago[0];
                            $tipoPago = $valoresPago[1];
                            $fecha = $valoresPago[2];
                            $numeroPago = $valoresPago[3];
                            $banco = $valoresPago[4];                        
                            $estado = $valoresPago[5];  
                            $responsable = $valoresPago[6];  
                            $sqlEstado = "SELECT nombre FROM gp_estado_pago_factura WHERE id_unico = $estado";
                            $resEstado = $mysqli->query($sqlEstado);
                            $estdo = mysqli_fetch_row($resEstado);
                        }
                        $idpago = 0;
                        if(!empty($_SESSION['idpago'])){
                            $idpago = $_SESSION['idpago'];
                        }
                        ?>
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>                        
                        <div class="form-group form-inline" style="margin-bottom: -0px">  
                            <!-- combo de selección Tipo factura -->
                            <label class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Tipo Pago:
                            </label>
                            <select class="form-control input-sm col-sm-2" name="slttipopago" id="slttipopago" title="Seleccione tipo pago" style="width:100px;height:30%" required>
                                <?php 
                                if(!empty($tipoPago)){
                                    $sqlTP = "SELECT id_unico,nombre FROM gp_tipo_pago WHERE id_unico = $tipoPago";
                                    $rsTP = $mysqli->query($sqlTP);                                    
                                    $filaTP = mysqli_fetch_row($rsTP);
                                    echo '<option value="'.$filaTP[0].'">'.ucwords(strtolower($filaTP[1])).'</option>';
                                    $sqltipopago = "SELECT id_unico,nombre FROM gp_tipo_pago WHERE id_unico != $tipoPago";
                                    $rstipoPago = $mysqli->query($sqltipopago);
                                    while($filatipoPago = mysqli_fetch_row($rstipoPago)){
                                        echo '<option value="'.$filatipoPago[0].'">'.ucwords(strtolower($filatipoPago[1])).'</option>';
                                    }
                                }else{
                                ?>
                                    <option value="">Tipo Pago</option>
                                    <?php 
                                    $sqlTP1 = "SELECT id_unico,nombre FROM gp_tipo_pago";
                                    $rsTP1 = $mysqli->query($sqlTP1);
                                    while($filaTP1 = mysqli_fetch_row($rsTP1)){
                                        echo '<option value="'.$filaTP1[0].'">'.ucwords(strtolower($filaTP1[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <!-- Texto en el que asignamos la fecha -->
                            <label for="fecha" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Fecha:
                            </label>                                
                            <input class="col-sm-2 input-sm" value="<?php if(!empty($fecha)){$valorF = (String) $fecha;$fechaS = explode("-",$valorF); echo $fechaS[2].'/'.$fechaS[1].'/'.$fechaS[0];}else{echo date('d/m/Y');} ?>" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;height:26px" title="Ingrese la fecha" placeholder="Fecha" required>
                            <script type="text/javascript" >
                                /*
                                 * Validación para el campo fecha la cual no puede ser mayor al utimo pago
                                 */
                                $("#fecha").change(function(){
                                    var form_data = {
                                        is_ajax:1,
                                        fecha:$("#fecha").val()
                                    };
                                    var result = ' ';
                                    $.ajax({
                                        type: 'POST',
                                        url: "consultasRecuadoFacturacion/validarFecha.php",
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
                            </script>
                            <!-- Número de factura-->
                            <label class="control-label col-sm-2">
                                <strong class="obligado">*</strong>Nro Pago:
                            </label>
                            <input class="form-control input-sm col-sm-2" name="txtNumeroP" id="txtNumeroP" type="text" title="Número factura" placeholder="Nro Pago" style="width:100px;height:26px" value="<?php if(!empty($numeroPago)){echo $numeroPago;}else{echo '';} ?>" required/>
                            <a id="btnBuscar" class="btn " title="Buscar Comprobante" style="margin-left:-180px;margin-top:-2px;padding:3px 3px 3px 3px"><li class="glyphicon glyphicon-search"></li></a>                            
                            <script>
                                $("#txtNumeroP").keyup(function(){
                                    var tipoP = $("#slttipopago").val();
                                    if(tipoP=='""' || tipoP==0){
                                        $("#mdlTPago").modal('show');
                                    }else {
                                        $("#txtNumeroP").autocomplete({
                                            source: "consultasRecuadoFacturacion/autompletadoNumero.php?tipo="+tipoP,
                                            minLength:5
                                        });
                                    }                                    
                                });
                                
                                $("#btnBuscar").click(function(){    
                                    var tipoF = $("#slttipopago").val();
                                    if(tipoF=='""' || tipoF==0 ){
                                        $("#mdlTPago").modal('show');
                                    }else {
                                        var form_data = {
                                            is_ajax:1,
                                            numero:+$("#txtNumeroP").val(),
                                            tipo:+$("#slttipopago").val(),
                                            existente:3
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/consultarNumeros.php",
                                            data:form_data,
                                            success: function (data) {
                                                window.location.reload();                                                  
                                                //console.log(form_data);
                                            }
                                        });
                                    }            
                                });                                
                            </script>
                        </div>
                        <div class="form-group form-inline" style="margin-bottom: -0px">
                            <!-- Tercero -->
                            <label class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Banco:                                    
                                </label>
                            <select class="form-control input-sm col-sm-2" name="sltBanco" id="sltBanco" title="Seleccione banco" style="margin-top:-5px;width:100px;height:30%" required="">
                                    <?php
                                    if(!(empty($banco))){
                                        $sql1 = "SELECT  ctb.id_unico,CONCAT(ctb.numerocuenta,' ',ctb.descripcion)
                                            FROM gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                            WHERE ctbt.tercero ='". $_SESSION['compania']."' AND ctb.id_unico=$banco ORDER BY ctb.numerocuenta";
                                        $rs1 = $mysqli->query($sql1);
                                        $fila1 = $rs1->fetch_row();
                                        echo '<option value="'.$fila1[0].'">'.$fila1[1].'</option>';
                                        $sql2 = "SELECT  ctb.id_unico,CONCAT(ctb.numerocuenta,' ',ctb.descripcion)
                                            FROM gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                            WHERE ctbt.tercero ='". $_SESSION['compania']."' AND ctb.id_unico!=$banco ORDER BY ctb.numerocuenta";
                                        $rs2 = $mysqli->query($sql2);
                                        while($fila2 = $rs2->fetch_row()){
                                            echo '<option value="'.$fila2[0].'">'.$fila2[1].'</option>';
                                        }
                                    }  else {
                                        echo '<option value="">Banco</option>';
                                        $sql = "SELECT  ctb.id_unico,CONCAT(ctb.numerocuenta,' ',ctb.descripcion)
                                            FROM gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                            WHERE ctbt.tercero ='". $_SESSION['compania']."' ORDER BY ctb.numerocuenta";
                                        $rs = $mysqli->query($sql);
                                        while($row = mysqli_fetch_row($rs)){
                                            echo '<option value="'.$row[0].'">'.$row[1].'</option>';
                                        }
                                    }                                    
                                    ?>                                 
                                </select>
                            <!-- Centro de costo -->
                            <label class="col-sm-2 control-label">
                                Nro Cupones:                                    
                            </label>                            
                            <input class="form-control input-sm col-sm-2" name="txtCupones" id="txtNumeroC" type="text" title="Ingrese número cupones" placeholder="Nro cupones" style="margin-top:-5px;width:100px;height:26px" value="<?php if(!empty($_SESSION['cupones'])){echo $_SESSION['cupones'];}else{echo '';} ?>"/>                            
                            <!-- Fecha de vencimiento -->
                            <label for="fechaV" class="col-sm-2 control-label">
                               Valor:
                            </label>                                
                            <input class="col-sm-2 input-sm" type="text" name="txtValor" id="txtValor" class="form-control" style="margin-top:-5px;width:100px;height:26px" value="<?php if(!empty($_SESSION['valor'])){$va=(double) $_SESSION['valor']; $oper = $va; echo number_format($oper, 2, '.', ',');}else{echo '0.00';} ?>" title="Ingrese el valor" value="" placeholder="Valor">
                            <script type="text/javascript" >
                            $("#txtValor").keyup(function(){
                                var valor = $("#txtValor").val();                                
                            });
                            </script>
                        </div>
                        <!-- Estado -->
                        <div class="form-group form-inline" style="margin-bottom: -0px">
                            <label class="col-sm-2 control-label">
                                    Estado:
                            </label>
                            <?php 
                            $sql = "SELECT id_unico,nombre FROM gp_estado_pago_factura WHERE id_unico = 1";
                            $result = $mysqli->query($sql);
                            $row = mysqli_fetch_row($result);
                            ?>
                            <input class="col-sm-2" type="text" name="txtEstado" id="txtEstado" class="form-control" style="margin-top:-5px;width:100px;height:26px" value="<?php  if(!empty($estado)){echo ucwords(strtolower($estdo[0]));}else{echo ucwords(strtolower($row[1]));} ?>" title="Estado" placeholder="Estado" readonly/>
                            <div>
                                <label class="col-sm-2 control-label">
                                Tercero:
                            </label>
                                <style>                                
                                .select2-results__option,.select2-chosen{
                                    padding: 2px;
                                    line-height: 26px
                                }
                                
                                </style>
                            <select class="form-control col-sm-1 input-sm select2 text-left" name="sltTercero" id="sltTercero" title="Seleccione un tercero para consultar" style="margin-top:-5px;width:368px;height:30%;" >
                                <?php
                                $tercero = '';
                                if(!empty($_SESSION['terceroConsulta'])){
                                    $tercero = $_SESSION['terceroConsulta']; 
                                }
                                
                                
                                if(!empty($tercero)){                                   
                                    $sql18 = "SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                            (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                            ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                            WHERE ter.id_unico=$tercero";
                                    $rs18 = $mysqli->query($sql18);
                                    $row18 = mysqli_fetch_row($rs18);
                                    echo '<option value="'.$row18[1].'">'.ucwords(strtolower($row18[0].PHP_EOL.$row18[2])).'</option>';                                    
                                    $sql19 = "SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                            (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                            ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion";
                                    $rs19 = $mysqli->query($sql19);
                                    while($row19 = mysqli_fetch_row($rs19)){
                                        echo '<option value="'.$row19[1].'">'.ucwords(strtolower($row19[0].PHP_EOL.$row19[2])).'</option>';
                                    }
                                }else{
                                    echo '<option value="">Tercero</option>';
                                    $sql1 = "SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                            (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                            ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion";
                                    $rs1 = $mysqli->query($sql1);
                                    while($row1 = mysqli_fetch_row($rs1)){
                                        echo '<option value="'.$row1[1].'">'.ucwords(strtolower($row1[0].PHP_EOL.$row1[2])).'</option>';
                                    }
                                }                                
                                ?>                                     
                            </select> 
                            </div>                                                       
                        </div>
                        <div class="form-group form-inline" style="margin-top:10px">                            
                            <div class="col-sm-offset-7 col-sm-7" style="">                                                                    
                                <input type="hidden" name="id" id="id" value="<?php echo $idP; ?>" />
                                <div class="col-sm-1" style="">
                                    <?php if(!empty($pago) && $estado==1){ ?>
                                    <a onclick="javascript:modificarPago()" id="btnModificar" class="btn sombra btn-primary" title="Modificar comprobante"><li class="glyphicon glyphicon-floppy-disk"></li></a>
                                    <?php    
                                    }else{ ?>
                                        <button type="submit" id="btnGuardar" class="btn sombra btn-primary" title="Guardar recaudo"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                    <?php    
                                    } ?>                                                                               
                                </div>
                                <div class="col-sm-1">
                                    <a class="btn sombra btn-primary" id="btnImprimir" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></a>                                                                              
                                </div> 
                                <div class="col-sm-1">
                                    <?php if(!empty($pago) && $estado==1){ ?>
                                        <a id="btnCancelarM" onclick="javascript:cancelarM()" class="btn sombra btn-primary" style="width: 40px" title="Cancelar modificación"><li class="glyphicon glyphicon glyphicon-remove"></li></a>
                                    <?php    
                                    }else{ ?>
                                        <a id="btnNuevo" onclick="javascript:nuevo()" class="btn sombra btn-primary" style="width: 40px" title="Ingresar nuevo recaudo"><li class="glyphicon glyphicon-plus"></li></a>
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
                                            session:3
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
                                            session:3
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
                                    
                                    $("#slttipopago").change(function(){
                                        var tipoPago = $("#slttipopago").val();
                                        if(tipoPago=='""' || tipoPago==0){
                                            $("#mdlTPago").modal('show');
                                        }else{
                                            var form_data = {
                                                is_ajax:1,
                                                numero:$("#slttipopago").val(),
                                                nuevos:3
                                            };
                                            
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasBasicas/generarNuevos.php",
                                                data: form_data,
                                                success: function (data) {                                                                            
                                                    $("#txtNumeroP").val(data);
                                                    $("#btnGuardar").attr('disabled',false);
                                                    $("#btnNuevo").css('display','none');
                                                    $("#btnCancelarP").css('display','block');
                                                }
                                            });
                                        }
                                    });
                                                                        
                                    function cancelarN(){
                                        window.location='registrar_GF_RECAUDO_FACTURACION.php';
                                    }
                                </script>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-10 text-center " style="margin-top:5px;" align="">                    
                <div class="client-form col-sm-12 col-sm-offset-2" style="margin-left:200px">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetallePago.php" style="margin-top:-15px">                        
                        <div class="col-sm-1" style="margin-right:250px;">
                            <script type="text/javascript">                                    
                                function justNumbers(e){   
                                    var keynum = window.event ? window.event.keyCode : e.which;
                                    if ((keynum == 8) || (keynum == 46) || (keynum == 45))
                                    return true;
                                    return /\d/.test(String.fromCharCode(keynum));
                                }
                            </script>
                            <div class="form-group" style="margin-top: 5px;" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Factura:
                                </label>
                                <style>
                                    .select2-container{
                                            margin-right: 34px;
                                    }
                                </style>                                
                                <select name="sltFactura2" id="sltFactura2" class="form-control select2 input-sm" style="width:350px;height:30%;" title="Seleccione un tipo de factura" required="">
                                    <?php 
                                    echo '<option value="">Factura</option>';
                                    $sqlFacturas= "SELECT DISTINCT fat.id_unico,fat.numero_factura,tpf.nombre,fat.fecha_factura FROM gp_factura fat
                                    LEFT JOIN  gp_tipo_factura tpf ON fat.tipofactura = tpf.id_unico
                                    LEFT JOIN gp_detalle_factura dtf ON dtf.factura = fat.id_unico 
                                    WHERE (SELECT SUM(dtf.valor_total_ajustado) FROM gp_detalle_factura dft WHERE dtf.factura = fat.id_unico)>0
                                    ORDER BY fat.fecha_factura";
                                    $resultFacturas = $mysqli->query($sqlFacturas);                                   
                                    $cantidad = $resultFacturas->num_rows;
                                    if($cantidad!=0){
                                        while($row = $resultFacturas->fetch_row()){
                                            $sql4 = "SELECT DISTINCT SUM(dtf.valor_total_ajustado) AS ULTIMO 
                                                        FROM gp_detalle_factura dtf
                                                        WHERE dtf.factura = $row[0]";
                                            $result4 = $mysqli->query($sql4);
                                            $row4 = mysqli_fetch_row($result4);
                                            $sql100 = "SELECT DISTINCT IFNULL($row4[0]-SUM(dtp.valor),$row4[0]) AS ULTIMO 
                                                    FROM gp_detalle_pago dtp
                                                    LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                                                    WHERE dtf.factura = $row[0]";
                                            $result100 = $mysqli->query($sql100);
                                            $row5= mysqli_fetch_row($result100);
                                            if($row5[0]==0){
                                            }else{
                                                echo '<option value="'.$row[0].'">'.ucwords(strtolower('Tipo Factura: '.$row[2].'    '.$row[1].'    -   Saldo:'.$row5[0].'     -   Fecha:'.$row[3])).'</option>';
                                            }        
                                        }
                                    }                                   
                                    ?>                                     
                                </select>
                            </div>                            
                        </div>                                                         
                        <div class="col-sm-1" style="margin-right:80px;">
                            <div class="facturas" class="form-group" style="margin-top: 5px;" align="left">                                                           
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Valor:
                                </label>                                
                                <input type="text" name="txtValor" placeholder="Valor" onkeypress="return justNumbers(event);" id="txtValor2" maxlength="50" style="height:32px;padding:2px;width:150px" required=""/>
                                <script type="text/javascript" >                                
                                $("#sltFactura2").change(function(){
                                    var factura = $("#sltFactura2").val();
                                    if(factura=='""' || factura==0){
                                        $("#txtValor2").val('');
                                    }else {
                                        var form_data = {
                                            is_ajax:1,
                                            factura:factura
                                        };
                                        
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasDetallePago/consultarValor.php",
                                            data:form_data,
                                            success: function (data) {
                                                $("#txtValor2").val(data);
                                            }
                                        });
                                    }
                                });
                                </script>
                                <script type="text/javascript" >
                                    $("#sltTercero").change(function(){                                
                                        var form_data = {                                            
                                            tercero:$("#sltTercero").val(),
                                            funcion:1
                                        };

                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasDetallePago/consultarFacturaTercero.php",
                                            data:form_data,
                                            success: function (data) {                                                
                                                $("#sltFactura2").html(data).fadeIn();
                                                $("#sltFactura2").css('display','none');                                       
                                            }
                                        });
                                    });                                                                                                                                    
                            </script>
                            </div>                               
                        </div>                                                                                                      
                        <div class="col-sm-1" align="left" style="margin-top:32px;margin-left:-55px;margin-right:30px;">                            
                            <div class="col-sm-1">
                                <button type="submit" id="btnDetalle" class="btn btn-primary sombra "><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                                <input type="hidden" name="MM_insert" >
                            </div>                            
                            <script type="text/javascript" >                                                                
                                $(document).ready(function () {
                                    var idpago = <?php echo $idpago; ?>;
                                    if(idpago==0 || idpago=='""' ||  idpago.length === 0){
                                        $("#btnDetalle").attr('disabled',true);
                                    }else{
                                        $("#btnDetalle").attr('disabled',false);
                                    }
                                });
                            </script>
                        </div>                                                                                   
                    </form>                        
                </div>
            </div>
            <div class="col-sm-9" style="margin-top:-10px">
                <?php 
                $tercero = 0;
                if (!(empty($_SESSION['idpago']))) {
                    $pago = $_SESSION['idpago'];                                                                                   
                        $sql = "SELECT  dtp.id_unico,
                                    dtp.detalle_factura,
                                    fat.numero_factura,
                                    tfat.nombre,
                                    dtp.valor,
                                    dtp.pago,
                                    ter.id_unico tercero,
                                    fat.id_unico,
                                    dtp.iva,
                                    dtp.impoconsumo,
                                    dtp.ajuste_peso,
                                    dtp.saldo_credito                                    
                            FROM gp_detalle_pago dtp
                            LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                            LEFT JOIN gp_factura fat ON dtf.factura = fat.id_unico
                            LEFT JOIN gp_tipo_factura tfat ON fat.tipofactura = tfat.id_unico
                            LEFT JOIN gp_pago pg ON dtp.pago = pg.id_unico
                            LEFT JOIN gf_tercero ter ON fat.tercero = ter.id_unico
                            WHERE pg.id_unico = $pago";
                $result = $mysqli->query($sql);
                } ?>
                <?php 
                $cupones = 0;
                $sumV = 0;
                ?>
                <?php 
                $tercero = 0;
                if (!(empty($_SESSION['idpago']))) {
                    $pago = $_SESSION['idpago'];                                                                                   
                        $sql = "SELECT  dtp.id_unico,
                                    dtp.detalle_factura,
                                    fat.numero_factura,
                                    tfat.nombre,
                                    dtp.valor,
                                    dtp.pago,
                                    ter.id_unico tercero,
                                    fat.id_unico,
                                    dtp.iva,
                                    dtp.impoconsumo,
                                    dtp.ajuste_peso,
                                    dtp.saldo_credito                                    
                            FROM gp_detalle_pago dtp
                            LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                            LEFT JOIN gp_factura fat ON dtf.factura = fat.id_unico
                            LEFT JOIN gp_tipo_factura tfat ON fat.tipofactura = tfat.id_unico
                            LEFT JOIN gp_pago pg ON dtp.pago = pg.id_unico
                            LEFT JOIN gf_tercero ter ON fat.tercero = ter.id_unico
                            WHERE pg.id_unico = $pago";
                $result = $mysqli->query($sql);
                } ?>
                <?php 
                $cupones = 0;
                $sumV = 0;
                $sumIVa = 0;
                $sumaImpo = 0;
                $sumaAjuste = 0;
                $sumaSaldo = 0;
                ?>
                <!-- Campos ocultos en los que guaramos la id anterior y la nueva id -->
                <input type="hidden" id="idPrevio" value="">
                <input type="hidden" id="idActual" value="">  
                <div class="table-responsive contTabla" >
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>    
                            <tr>
                                <td class="oculto" >Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Tipo Factura</strong></td>
                                <td class="cabeza"><strong>Factura</strong></td>
                                <td class="cabeza"><strong>Tercero</strong></td>
                                <td class="cabeza"><strong>Valor</strong></td>
                                <td class="cabeza"><strong>Iva</strong></td>
                                <td class="cabeza"><strong>Impoconsumo</strong></td>
                                <td class="cabeza"><strong>Ajuste peso</strong></td>
                                <td class="cabeza"><strong>Saldo Crédito</strong></td>
                                <td class="cabeza"><strong>Saldo Factura</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%" class="cabeza"></th>
                                <th class="cabeza">Tipo Factura</th>
                                <th class="cabeza">Tercero</th>
                                <th class="cabeza">Factura</th>
                                <th class="cabeza">Valor</th>
                                <th class="cabeza">Iva</th>
                                <th class="cabeza">Impoconsumo</th>
                                <th class="cabeza">Ajuste peso</th>
                                <th class="cabeza">Saldo Crédito</th>
                                <th class="cabeza">Saldo Factura</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while($row=$result->fetch_row()){ ?>
                            <tr>
                                <?php $cupones+=1; ?>
                                <td class="oculto"></td>
                                <td class="campos" width="7%">
                                    <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                                        <li class="glyphicon glyphicon-trash"></li>
                                    </a>
                                    <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);">
                                        <li class="glyphicon glyphicon-edit"></li>
                                    </a>                                            
                                </td>
                                <td class="campos text-right">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblTipofactura'.$row[0].'">'.ucwords(strtolower($row[3])).'</label>'; ?>
                                </td>
                                <td class="campos text-right">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblFactura'.$row[0].'">'.ucwords(strtolower($row[2])).'</label>'; ?>
                                </td>
                                <td class="campos text-right">                                    
                                    <?php 
                                    $sqltercero="SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
                                                (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                                                ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                WHERE ter.id_unico = $row[6]
                                                ORDER BY NOMBRE ASC";
                                    $ter = $mysqli->query($sqltercero);                                    
                                    $per = mysqli_fetch_row($ter);
                                    echo '<label class="valorLabel" style="font-weight:normal" title="'.$per[2].'" id="lblTercero'.$row[0].'">'.ucwords(strtolower($per[0])).'</label>'; 
                                    ?>
                                </td>                                
                                <td class="campos text-right">
                                    <?php 
                                          $sumV += $row[4];
                                          echo '<label class="valorLabel" style="font-weight:normal" id="lblValor'.$row[0].'">'.number_format($row[4], 2, '.', ',').'</label>'; 
                                          echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-8 campoD text-left"  type="text" name="txtValor'.$row[0].'" id="txtValor'.$row[0].'" value="'.$row[4].'" />';
                                    ?>
                                    <div >
                                        <table id="tab<?php echo $row[0] ?>" style="padding:0px;background-color:transparent;background:transparent;margin-left: -5px" class="col-sm-1">
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
                                <td class="campos text-right">
                                    <?php 
                                    $sumIVa+=$row[8];
                                    echo '<label class="valorLabel" style="font-weight:normal" id="lblIva'.$row[0].'">'.number_format($row[8], 2, '.', ',').'</label>'; 
                                    ?>
                                </td>
                                <td class="campos text-right">
                                    <?php 
                                    $sumaImpo += $row[9];
                                    echo '<label class="valorLabel" style="font-weight:normal" id="lblImpoconsumo'.$row[0].'">'.number_format($row[9], 2, '.', ',').'</label>'; 
                                    ?>
                                </td>
                                <td class="campos text-right">
                                    <?php 
                                    $sumaAjuste +=$row[10];
                                    echo '<label class="valorLabel" style="font-weight:normal" id="lblAjustePeso'.$row[0].'">'.number_format($row[10], 2, '.', ',').'</label>'; 
                                    ?>
                                </td>
                                <td class="campos text-right">
                                    <?php 
                                    $sumaSaldo +=$row[11];
                                    echo '<label class="valorLabel" style="font-weight:normal" id="lblSaldoCredito'.$row[0].'">'.number_format($row[11], 2, '.', ',').'</label>'; 
                                    ?>
                                </td>
                                <td class="campos text-right">
                                    <?php  
                                        $sql4 = "SELECT DISTINCT SUM(dtf.valor_total_ajustado) AS ULTIMO 
                                                FROM gp_detalle_factura dtf
                                                WHERE dtf.factura = $row[7]";
                                        $result4 = $mysqli->query($sql4);
                                        $row4 = mysqli_fetch_row($result4);
                                        $sql100 = "SELECT DISTINCT $row4[0]-SUM(dtp.valor) AS ULTIMO 
                                                FROM gp_detalle_pago dtp
                                                LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                                                WHERE dtf.factura = $row[7]";
                                        $result100 = $mysqli->query($sql100);                                        
                                        $row100 = mysqli_fetch_row($result100);                                                                               
                                    ?>
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="saldoFactura'.$row[0].'">'.number_format($row100[0], 2, '.', ',').'</label>'; ?>
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
                <div class="col-sm-1 col-sm-offset-4" style="margin-right:30px">
                    <div class="form-group" style="" align="left">                                    
                        <label class="control-label">
                            <strong>Totales:</strong>
                        </label>                                
                    </div>
                </div> 
                <div class="col-sm-1" style="margin-right:20px">
                    <label class="control-label valorLabel" title="Total cupones"><?php echo $cupones; ?></label>
                </div>                
                <div class="col-sm-1" style="margin-right:20px">
                    <label class="control-label valorLabel" title="Total Valor"><?php echo number_format($sumV, 2, '.', ','); ?></label>
                </div>                
            </div>
            <div class="col-sm-8 col-sm-offset-1" style="margin-top:-15px;margin-bottom:-30px"> 
                <div class="col-sm-1 col-sm-offset-4" style="margin-right:30px">
                    <div class="form-group" style="" align="left">                                    
                        <label class="control-label">
                            <strong>Diferencias:</strong>
                        </label>                                
                    </div>
                </div> 
                <div class="col-sm-1" style="margin-right:20px">
                    <label class="control-label valorLabel" title="Diferencia de cupones"><?php if(!empty($_SESSION['cupones'])){ $c = (int) $_SESSION['cupones']; echo $c- $cupones ;}else{echo 0;}?></label>
                </div>                
                <div class="col-sm-1" style="margin-right:20px">
                    <label class="control-label valorLabel" title="Diferencia de valor"><?php if(!empty($_SESSION['valor'])){$va=(double) $_SESSION['valor']; $oper = $va-$sumV; echo number_format($oper, 2, '.', ',');}else{echo '0.00';} ?></label>
                </div>                
            </div>
        </div>        
    </div>
    <script type="text/javascript" >
    var valorNuevo = 0;
    var valorAnt = 0;         
    function eliminar(id){
        var result = '';
        $("#myModal").modal('show');
        $("#ver").click(function(){
            $("#mymodal").modal('hide');
            $.ajax({
                type:"GET",
                url:"json/eliminarDetallePago.php?id="+id,
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
    
    function modificarPago(){
        var id = $("#id").val();
        var tipo=$("#slttipopago").val();
        var fecha=$("#fecha").val();
        var numero=$("#txtNumeroP").val();
        var banco=$("#sltBanco").val();
        
        var form_data = {
            id:id,
            tipopago:tipo,
            fecha:fecha,
            numero:numero,
            banco:banco
        };
        
        var result='';
        $.ajax({
            type: 'POST',
            url: "json/modificarPagoJson.php",
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
    
    function modificar(id){
        valorAnt = $("#txtValor"+id).val();
        $("#txtValor"+id).keyup(function(){
            valorNuevo = $("#txtValor"+id).val();                        
        });
                                
        if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
            var lblValorC = 'lblValor'+$("#idPrevio").val();
            var txtValorC = 'txtValor'+$("#idPrevio").val();
            var guardarC = 'guardar'+$("#idPrevio").val();
            var cancelarC = 'cancelar'+$("#idPrevio").val();
            var tablaC = 'tab'+$("#idPrevio").val();
            
            $("#"+lblValorC).css('display','block');
            $("#"+txtValorC).css('display','none');
            $("#"+guardarC).css('display','none');
            $("#"+cancelarC).css('display','none');
            $("#"+tablaC).css('display','none');                                    
        }
        
        var lblValor = 'lblValor'+id;
        var txtValor = 'txtValor'+id;
        var guardar = 'guardar'+id;
        var cancelar = 'cancelar'+id;
        var tabla = 'tab'+id;              
        
        $("#"+txtValor).css('display','block');
        $("#"+lblValor).css('display','none');
        $("#"+guardar).css('display','block');
        $("#"+cancelar).css('display','block');
        $("#"+tabla).css('display','block');
        
        $("#idActual").val(id);
        
        if($("#idPrevio").val() != id){
            $("#idPrevio").val(id);   
        }
    }    
    function cancelar(id){
        var lblValor = 'lblValor'+id;
        var txtValor = 'txtValor'+id;
        var guardar = 'guardar'+id;
        var cancelar = 'cancelar'+id;
        var tabla = 'tab'+id;
        
        $("#"+lblValor).css('display','block');
        $("#"+txtValor).css('display','none');
        $("#"+guardar).css('display','none');
        $("#"+cancelar).css('display','none');
        $("#"+tabla).css('display','none');
        $("#"+txtValor).val(valorAnt);
    }
    function guardarCambios(id){                               
        
       if(valorNuevo>valorAnt){
            $("#mdlValor").modal('show');
        }else{
            var form_data = {
                is_ajax:1,
                id:id,
                valor:valorNuevo = $("#txtValor"+id).val()
            };
            var result = '';
            $.ajax({
                type: 'POST',
                url: "json/modificarDetallePago.php",
                data:form_data,
                success: function (data) {
                    result = JSON.parse(data);
                    if(result==true){
                        $("#mdlModificado").modal('show');
                    }else{
                        $("#mdlNomodificado").modal('show');
                    }
                }
            });
        }
                
    }       
    </script>    
    <?php require_once './footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <!-- Modal de validación de tercero -->
    <div class="modal fade" id="mdlValTercero" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un tercero para consultar los detalle de recaudo.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnTercero" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de validación de valor -->
    <div class="modal fade" id="mdlValor" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Ingrese un valor menor al actual</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnValor" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>                    
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
                    <p>¿Desea eliminar el registro seleccionado de Detalle Pago?</p>
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
    <!-- modal de tipo factura -->
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
    <!-- Modal de tipo pago -->
    <div class="modal fade" id="mdlTPago" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un tipo pago.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>   
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
    <!-- Modal de validación de fecha -->
    <div class="modal fade" id="mdlfecha" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>La fecha es anterior al ultimo pago por favor ingrese una fecha diferente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2();                    
    </script>
    </script>    
</body>
</html>
