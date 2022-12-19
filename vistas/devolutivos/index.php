<?php
require './Conexion/conexion.php';
require './head_listar.php';
$terS  = $this->dev->obtenerListadoTerceros('ASC', $_SESSION['compania']);
$terD  = $this->dev->obtenerTercerosDiff($tercero, $_SESSION['compania']);
if(!empty($_GET['cnt'])){ $idCnt = $this->cnt->obtner($_GET['cnt']); }
$pago  = $this->dev->obtenerRecaudoFactura($id);
?> 
    <title>Devoluciones</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/desing.css">
    <style>
        #tableD{
            margin-top: 5px;
            height: 250px;
            overflow-y: auto;
            overflow-x: auto;
            display: block;
            color: #555;
        }

        #tableD>thead>tr>th{
            width: 100%;
            font-size: 10px;
        }

        .table-bordered{
            border-radius: 5px !important;
        }

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
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require './menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top: 0;">Devoluciones</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12 col-md-12 col-lg-12">
                    <form name="form" id="form" action="access.php?controller=Devolutivos&action=saveData" class="form-horizontal" method="POST"  enctype="multipart/form-data">
                        <p align="center" class="parrafoO" style="margin-bottom:5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="sltTipoFactura" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Tipo Factura:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select class="form-control" id="sltTipoFactura" name="sltTipoFactura" title="Tipo Factura" placeholder="Tipo Factura" required>
                                    <?php
                                    $html = "<option></option>";
                                    foreach ($tipos as $row){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltFacturaX" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Factura:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select class="form-control select" id="sltFacturaX" name="sltFacturaX" title="Factura" placeholder="Factura" required>
                                    <?php
                                    $html = "";
                                    if(!empty($aso)){
                                        $html .= "<option value='$aso'>$xaso</option>";
                                    }else{
                                        $html .= "<option value=\"\"></option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltTipoDev" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Tipo Devolutivo:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select class="form-control select" id="sltTipoDev" name="sltTipoDev" title="Tipo Devolutivo" placeholder="Tipo Devolutivo" required>
                                    <?php
                                    $html = "<option></option>";
                                    foreach ($tipoD as $row){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtNumero" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Número:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtNumero" id="txtNumero" class="form-control" autocomplete="off" style="width: 100%;" placeholder="Numero" value="<?php echo $numero; ?>" required />
                            </div>
                            <label for="txtFecha" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Fecha:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtFecha" id="txtFecha" class="form-control fecha" autocomplete="off" style="width: 100%;" placeholder="Fecha" value="<?php echo $fecha; ?>" required />
                            </div>
                            <label for="sltTercero" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Tercero:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select class="form-control select" id="sltTercero" name="sltTercero" title="Tercero" placeholder="Tercero" required>
                                    <?php
                                    $html = "";
                                    if(!empty($tercero)){
                                        $html .= "<option value='$tercero'>$nomT</option>";
                                        foreach ($terD as $row){
                                            $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                        }
                                    }else{
                                        $html .= "<option></option>";
                                        foreach ($terS as $row){
                                            $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <?php if(!empty($_REQUEST['dev'])){
                                echo '<a href="informes/inf_com_dev.php?factura='.$_REQUEST['dev'].'" class="btn btn-primary borde-sombra btn-group" title="Imprimir" target="_blank"><li class="glyphicon glyphicon glyphicon-print"></li></a>';
                            }?>
                        </div>
                        <div class="form-group">
                            <label for="txtDescripcion" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado"></strong>Descripción:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" autocomplete="off" style="width: 100%;" placeholder="Descripción" value="<?php echo $desc; ?>"  />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="client-form col-sm-10 col-md-10 col-lg-10" style="margin-top: 5px;">
                    <table id="tableD" class="table table-bordered table-hover table-striped table-condensed table-responsive">
                        <thead>
                            <tr>
                                <th style="width: 30%; text-align: center;">Concepto</th>
                                <th style="width: 10%; text-align: center;">Cantidad</th>
                                <th style="width: 20%; text-align: center;">Valor Base</th>
                                <th style="width: 20%; text-align: center;">Iva</th>
                                <th style="width: 10%; text-align: center;">Impoconsumo</th>
                                <th style="width: 20%; text-align: center;">Ajuste al peso</th>
                                <th style="width: 5%; text-align: center;">
                                    <input type="checkbox" name="chkTodos" id="chkTodos" title="Marcar / Desmarcar todos" />
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $html = "";
                           
                            if(!empty($_REQUEST['factura'])){
                                $data = $this->dev->obtenerDetalles($_REQUEST['factura']);
                                foreach ($data as $row){
                                    $xDA  = $this->dev->buscarAfectacionesDetalleDev($row[0]);
                                     $xValR  = $this->dev->buscarAfectacionesDetalleDevValor($row[0]);
                                     $xVal = str_replace(array("-", "'", ";"), '', $xValR);
                                     $xxx  = ($row[3]) - ($xDA);
                                     $xval=$row[2]-$xVal;
                                    if ($xval>0) { 
                                        $xxx=$row[3];
                                    }
                                    
                                    if($xxx > 0  || $xval>0){
                                        $html .= "<tr>";
                                        $html .= "<td>$row[8]</td>";
                                        $html .= "<td class='text-right'>";
                                        $html .= "<input type='hidden' value='$row[9]' name='iva_conf$row[0]' id='iva_conf$row[0]' />";
                                        $html .= "<input type='text' class='form-control' value='$xxx' name='txtCant$row[0]' id='txtCant$row[0]' style='width: 100%; margin: 0 0 0;' />";
                                        $html .= "</td>";
                                        $html .= "<td class='text-right'>";
                                        $html .= "<input type='text' class='form-control' onkeyup=calcule_values(".$row[0].") value='".number_format($xval, 2, ',', '.')."' name='txtValor$row[0]' id='txtValor$row[0]' style='width: 100%; margin: 0 0 0;' />";
                                        $html .= "</td>";
                                        $html .= "<td class='text-right'>";
                                        $html .= "<input type='text' class='form-control'  value='".number_format($row[4], 2, ',', '.')."' name='txtIva$row[0]' id='txtIva$row[0]' style='width: 100%; margin: 0 0 0;' />";
                                        $html .= "</td>";
                                        //$html .= "<td class='text-right'>".number_format($row[2], 2, ',', '.')."</td>";
                                        //$html .= "<td class='text-right'>".number_format($row[4], 2, ',', '.')."</td>";
                                        $html .= "<td class='text-right'>".number_format($row[5], 2, ',', '.')."</td>";
                                        $html .= "<td class='text-right'>".number_format($row[6], 2, ',', '.')."</td>";
                                        $html .= "<td class='text-center'><input type='checkbox' name='chkDet' id='chkDet$row[0]' value='$row[0]'></td>";
                                        $html .= "</tr>";
                                    }
                                }
                            }
                            if(!empty($_REQUEST['dev'])){
                                $data = $this->dev->obtenerDetalles($_REQUEST['dev']);
                                foreach ($data as $row){
                                    $html .= "<tr>";
                                    $html .= "<td>$row[8]</td>";
                                    $html .= "<td class='text-right'>$row[3]</td>";
                                    $html .= "<td class='text-right'>".number_format($row[2], 2, ',', '.')."</td>";
                                    $html .= "<td class='text-right'>".number_format($row[4], 2, ',', '.')."</td>";
                                    $html .= "<td class='text-right'>".number_format($row[5], 2, ',', '.')."</td>";
                                    $html .= "<td class='text-right'>".number_format($row[6], 2, ',', '.')."</td>";
                                    $html .= "<td class='text-center'></td>";
                                    $html .= "</tr>";
                                }
                            }
                            echo $html;
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="client-form col-sm-10 col-md-2 col-md-10 col-md-2 col-lg-8 col-lg-2 text-center">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <h2 class="titulo" align="center" style="font-size:17px; margin-top: 10px;">Información<br/>adicional</h2>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <a id="btnCnt" class="btn btn-primary btnInfo" onclick="return cargarComprobante(<?php echo !empty($idCnt)?$idCnt:'0'; ?>)">COMPROBANTE<br/>CONTABLE</a>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <?php
                        $html = "";
                        if(!empty($pago)){
                            $html .= "<a href=\"registrar_GF_RECAUDO_FACTURACION_2.php?recaudo=".md5($pago)."\" class=\"btn btn-primary btnInfo\" target=\"_blank\">VER RECAUDO</a>";
                        }
                        echo $html;
                        ?>
                    </div>
                </div>
                <div class="client-form col-sm-12 col-md-12 col-lg-12 text-right form-inline">
                    <label class="control-label col-sm-2 col-md-2 col-lg-2">Buscar Devoluciones:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2 text-left">
                        <select class="form-control select" id="sltTipoDevs" name="sltTipoDevs" title="Tipo Devolutivo" placeholder="Tipo Devolutivo" required>
                            <?php
                            $html = "<option></option>";
                            foreach ($tipoD as $row){
                                $html .= "<option value='$row[0]'>$row[1]</option>";
                            }
                            echo $html;
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 text-left">
                        <select class="form-control select" id="sltDevolutivos" name="sltDevolutivos" title="Devolutivos" placeholder="Devolutivos" required>
                            <?php
                            $html = "<option></option>";
                            echo $html;
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                        <a class="btn btn-primary borde-sombra" id="btnBuscar"><span class="glyphicon glyphicon-search"></span></a>
                    </div>
                    <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                        <a class="btn btn-primary borde-sombra" id="btnuevo"><span class="glyphicon glyphicon-plus"></span></a>
                    </div>
                    <a class="btn btn-primary borde-sombra" id="btnG"><span class="glyphicon glyphicon-floppy-disk"></span></a>
                </div>
            </div>
            <?php require './footer.php'; ?>
        </div>
    </div>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_date.js"></script>
    <script src="js/script_validation.js"></script>
    <script src="js/script.js"></script>
    <script src="js/md5.js"></script>
    <script>
        $("#sltTipoFactura").select2().change(function (){
            let tipo = $("#sltTipoFactura").val();
            $.get("access.php?controller=Devolutivos&action=buscarFacturas", { tipo: tipo },
                function (data){
                    let option = '<option value=""> - </option>'
                    option =option + data;
                    console.log(data);
                    $("#sltFacturaX").html(option);
                }
            );
        });

        $("#sltFacturaX").change(function (e) {
            let fat = e.target.value;
            if(fat.length > 0){
                window.location = 'access.php?controller=Devolutivos&action=index&factura='+md5(fat);
            }
        });

        $("#chkTodos").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

        $("#sltTipoDev").change(function (e) {
            let tipo = e.target.value;
            let form_data = { tipo:tipo,action:1 };
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_facturaJson.php",
                data: form_data,
                success: function (data) {
                    $("#txtNumero").val(data);
                }
            })
        });

        $("#btnG").click(function () {
            let asoFt = $("#sltFactura").val();
            let tipoD = $("#sltTipoDev").val();
            let numro = $("#txtNumero").val();
            let fecha = $("#txtFecha").val().split('/').reverse().join('-');
            let tercr = $("#sltTercero").val();
            let desc  = $("#txtDescripcion").val();
            let list  = { 'datos' : [] };
            //console.log(desc);
            $("input[name='chkDet']").each(function (){
                if(this.checked){
                    list.datos.push({
                        "id"   : $(this).val(),
                        "Cant" : $("#txtCant" + $(this).val()).val(),
                        "Valor": $("#txtValor" + $(this).val()).val(),
                        "Iva": $("#txtIva" + $(this).val()).val()
                    });
                }
            });

            let json = JSON.stringify(list);

            $.get("access.php?controller=Devolutivos&action=Procesar",
                {
                    asociado : asoFt,  tipoDevt : tipoD,  numeroDt : numro,
                    fechaDev : fecha,  terceroD : tercr,  Detalles : json, 
                    desc: desc
                },
                function (data){
                    console.log(data);
                    window.location = data;
                }
            );

        });

        $("#sltTipoDevs").change(function (){
            let tipo = $("#sltTipoDevs").val();
            $.get("access.php?controller=Devolutivos&action=buscarFacturas", { tipo: tipo },
                function (data){
                    $("#sltDevolutivos").append(data).trigger("change");
                }
            );
        });

        $("#btnBuscar").click(function () {
            let fat = $("#sltDevolutivos").val();
            $.get("access.php?controller=Devolutivos&action=buscarDevs", { factura : fat }, function (data) {
                window.location = data;
            });
        });

        let dev = QueryString.dev;
        if(dev){
            $("#btnG").attr("disabled", true);
        }

        let xcnt = QueryString.cnt;
        let xmov = QueryString.mov;
        if(!xcnt &&  dev){
            jsShowWindowLoad("Generando recaudo.");
            let dev = QueryString.dev;
            
            let form_data = {
                id_factura: dev,
                action:     10,
            };
            $.ajax({
                type:"POST",
                url:"jsonPptal/gf_recaudoFacJson.php",
                data:form_data,
                success: function(data){
                    jsRemoveWindowLoad();
                    console.log(data +'Recons1');
                    if(data.length > 0){ 
                        let form_data = {
                        id_factura: dev,
                        action:     11,
                        fat: dev,
                        mov:xmov,
                        };
                        $.ajax({
                            type:"POST",
                            url:"jsonPptal/gf_recaudoFacJson.php",
                            data:form_data,
                            success: function(data){
                                jsRemoveWindowLoad();
                                console.log(data +'Recons2');
                                let fat = data;
                                console.log(fat+'Buscar');
                                $.get("access.php?controller=Devolutivos&action=buscarDevs", { factura : fat }, function (data) {
                                    console.log(data+'Buscar');
                                    window.location = data;
                                });
                            }
                        });
                    } else {
                         document.location.reload();
                    }
                }
            });
        }else{
            $("#idPago").css("display", "block");
        }

        function jsRemoveWindowLoad() {
            $("#WindowLoad").remove();
        }

        function jsShowWindowLoad(mensaje) {
            jsRemoveWindowLoad();
            if (mensaje === undefined) mensaje = "Procesando la información<br>Espere por favor";
            let height = 20;
            let ancho  = 0;
            let alto   = 0;
            if (window.innerWidth == undefined) ancho = window.screen.width;
            else ancho = window.innerWidth;
            if (window.innerHeight == undefined) alto = window.screen.height;
            else alto = window.innerHeight;
            let heightdivsito = alto/2 - parseInt(height)/2;//Se utiliza en el margen superior, para centrar
            let imgCentro = "<div style='text-align:center;height:" + alto + "px;'><div  style='color:#FFFFFF;margin-top:" + heightdivsito + "px; font-size:20px;font-weight:bold;color:#1075C1'>" + mensaje + "</div><img src='img/loading.gif'/></div>";
            let div          = document.createElement("div");
            div.id           = "WindowLoad";
            div.style.width  = ancho + "px";
            div.style.height = alto  + "px";
            $("body").append(div);
            let input  = document.createElement("input");
            input.id   = "focusInput";
            input.type = "text";
            $("#WindowLoad").append(input).html(imgCentro);
            $("#focusInput").focus().hide();
        }

        function cargarComprobante(idCnt){
            $.post("modalConsultaComprobanteC.php", { idC : idCnt }, function (data) {
                $("#modalComprobanteC").html(data);
                $(".comprobantec").modal('show');
            });
        }
        function calcule_values(id) {
            var txtCantidad = 'txtCant'+id;
            var txtValor    = 'txtValor'+id;
            var txtIva      = 'txtIva'+id;
            var ivaTarifa = 'iva_conf'+id;
            
            //var txtTotal    = 'txttotal'+id;

            var cantidad = parseFloat($("#"+txtCantidad).val());
            var valor    = parseFloat($("#"+txtValor).val());
            var iva    = parseFloat($("#"+ivaTarifa).val());
            //alert(iva);
            if (cantidad === 0 || cantidad === "" || cantidad.length === 0 || cantidad === null) {
                cantidad = 0;
            }else {
                cantidad = parseFloat($("#"+txtCantidad).val());
            }
            var totalIva = (parseFloat(valor) * parseFloat(iva)) / 100;
            var total    = (parseFloat(valor) + parseFloat(totalIva)) * cantidad;
            $("#"+txtIva).val(totalIva);
        }
        $("#btnuevo").click(function(){
            document.location ='access.php?controller=Devolutivos';
        })
    </script>
    <?php require_once './modalConsultaComprobanteC.php'; ?>
</body>
</html>
