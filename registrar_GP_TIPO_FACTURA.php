<?php
require_once ('head.php');
require_once ('Conexion/conexion.php');
$compania = $_SESSION['compania'];
?>
    <title>Registrar Tipo Factura</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script> 
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/desing.css">
    <style>
        .client-form input[type='text']{
            width: 100%;
        }
    </style> 
</head> 
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin: -2px 4px 5px;">Registrar Tipo Factura</h2>
                <a href="listar_GP_TIPO_FACTURA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Tipo Factura</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top: -5px" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoFacturaJson.php">
                        <p align="center" style="margin-bottom: 15px; margin-top: 25px; margin-left: 30px; font-size: 80%;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="nombre" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Nombre:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre" required>
                            </div>
                            <label for="prefijo" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Prefijo:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <input type="text" name="prefijo" id="prefijo" class="form-control" maxlength="10" title="Ingrese el prefijo" onkeypress="return txtValida(event,'car_sin')" placeholder="Prefijo"  style="text-transform:uppercase;" required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="sltClase" class="col-sm-2 col-md-2 col-lg-2 control-label">Clase Factura:</label>
                            <div class="col-sm-4 col-md-4 col-md-4">
                                <select name="sltClase" id="sltClase" class="form-control">
                                    <option value="">Clase Factura</option>
                                    <?php
                                    $html = "";
                                    $str  = "SELECT id_unico, nombre FROM gp_clase_factura ORDER BY nombre ASC";
                                    $res  = $mysqli->query($str);
                                    $row  = $res->fetch_all(MYSQLI_NUM);
                                    foreach ($row as $fila){
                                        $html .= "<option value='$fila[0]'>$fila[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                             <label for="tipoC" class="col-sm-1 col-md-1 col-lg-1 control-label">Tipo comprobante:</label>
                            <div class="col-sm-4 col-md-4 col-md-4">
                                <select name="tipoC" id="tipoC" title="Tipo comprobante" class="col-sm-1 form-control">
                                    <?php
                                    echo "<option value=''>Tipo comprobante</option>";
                                    $sqlT = "SELECT id_unico,nombre,sigla 
                                        FROM gf_tipo_comprobante 
                                        WHERE  niif != 1 AND compania = $compania AND  clasecontable in (9,10,15,16)";
                                    $resultT = $mysqli->query($sqlT);
                                    while ($t = mysqli_fetch_row($resultT)) {
                                        echo "<option value=".$t[0].">".$t[2].' - '.ucwords(mb_strtolower($t[1]))."</option>";
                                    }
                                    ?>
                                </select>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <label for="tipoR" class="col-sm-2 col-md-2 col-lg-2 control-label">Tipo Recaudo:</label>
                            <div class="col-sm-4 col-md-4 col-md-4">
                                <select name="tipoR" id="tipoR" title="Tipo Recaudo" class="form-control">
                                    <?php
                                    echo "<option value=''>Tipo Recaudo</option>";
                                    $sqlT = "SELECT id_unico,UPPER(nombre) FROM gp_tipo_pago WHERE compania = $compania";
                                    $resultT = $mysqli->query($sqlT);
                                    while ($t = mysqli_fetch_row($resultT)) {
                                        echo "<option value=".$t[0].">". ($t[1])."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="sltMov" class="col-sm-1 col-md-1 col-lg-1 control-label">Tipo Movimiento:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="sltMov" id="sltMov" title="Tipo Movimiento" class="form-control">
                                    <?php
                                    $html = "";
                                    $html .= "<option value=''>Tipo Movimiento</option>";
                                    $str = "SELECT id_unico, CONCAT_WS(' ', UPPER(sigla), nombre) FROM gf_tipo_movimiento WHERE clase = 3 AND compania = $compania ORDER BY sigla ASC";
                                    $rst = $mysqli->query($str);
                                    while($row = mysqli_fetch_row($rst)){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="consecutivo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Sigue Consecutivo:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <label for="consecutivo1" class="radio-inline"><input type="radio" name="consecutivo" id="consecutivo1"  value="1" >Sí</label>
                                <label for="consecutivo2" class="radio-inline"><input type="radio" name="consecutivo" id="consecutivo2" value="2" checked>No</label>
                            </div>
                            <label for="servicio" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Servicio:</label>
                            <div class="col-sm-4 col-md-4 col-md-4">
                                <label for="serv1" class="radio-inline"><input type="radio" name="serv" id="serv1" value="1" >Sí</label>
                                <label for="serv2" class="radio-inline"><input type="radio" name="serv" id="serv2" value="2" checked>No</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lblDescuento" class="col-sm-2 col-md-2 col-lg-2 control-label">Aplica descuento?</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <input type="checkbox" name="optXDescuento" id="optXDescuento" value="1" disabled>
                            </div>
                            <label for="lblDescuento" class="col-sm-1 col-md-1 col-lg-1 control-label">Automático?</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <label for="optAutS" class="radio-inline"><input type="radio" name="optAutomatico" id="optAutS" value="1">Si</label>
                                <label for="optAutN" class="radio-inline"><input type="radio" name="optAutomatico" id="optAutN" value="2" checked>No</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltTipoCambio" class="col-sm-2 col-md-2 col-lg-2 control-label">Tipo Cambio:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="sltTipoCambio" id="sltTipoCambio" title="Tipo Cambio" class="form-control">
                                    <?php
                                    $html = "";
                                    $html .= "<option value=''>Tipo Cambio</option>";
                                    $str = "SELECT id_unico,  nombre FROM gf_tipo_cambio ";
                                    $rst = $mysqli->query($str);
                                    while($row = mysqli_fetch_row($rst)){
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="lblDescuento" class="col-sm-1 col-md-1 col-lg-1 control-label">Facturación Electrónica</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <label for="optfe" class="radio-inline"><input type="radio" name="optfe" id="optfe" value="1">Si</label>
                                <label for="optfe" class="radio-inline"><input type="radio" name="optfe" id="optfe" value="2" checked>No</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="no" class="col-sm-5 col-md-5 col-lg-5 control-label"></label>
                            <div class="col-sm-6 col-md-6 col-lg-6 text-right">
                                <button type="submit" class="btn btn-primary borde-sombra">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once './footer.php'; ?>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/script.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script>
        $("#tipoC, .select, #sltMov, #sltClase, #sltTipoCambio").select2({
            allowClear:true
        });
        $("#tipoR").select2({
            allowClear:true
        });

        $().ready(function() {
            var validator = $("#form").validate({
                ignore: "",
                
                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-4").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-lg-1").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-1").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
        });

        $("#sltClase").change(function (e) {
            let clase = e.target.value;
            if(clase == 3){
                $("#optXDescuento").removeAttr("disabled");
            }else{
                $("#optXDescuento").attr("disabled", true);
            }

            let xClase = "";
            if(clase == 7){
                xClase = 2;
            }else{
                xClase = 3;
            }

            $.get("access.php?controller=Devolutivos&action=tipoMovimientoClase",
                { clase: xClase },
                function (data) {
                    $("#sltMov").html(data).trigger("change");
                } 
            );
        });
    </script>
    </body>
</html>
