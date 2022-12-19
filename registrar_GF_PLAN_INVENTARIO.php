<?php
    require_once ('head.php');
    require_once('Conexion/conexion.php');
    require_once ('./modelAlmacen/inventario.php');

    $plan = new inventario();
?>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/desing.css">
    <title>Registrar Plan  Inventario</title>
    <style type="text/css" media="screen">
        #form>.form-group{
            margin-bottom: 10px !important;
        }

        .client-form input[type="text"]{
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
        <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 col-sm-8 col-lg-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Registrar Plan Inventario</h2>
                <a href="GF_PLAN_INVENTARIO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Plan inventario
                    </h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-lg-12">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGFPlanInventario.php?action=insert">
                        <p align="center" style="margin-bottom: 10px; margin-top: 4px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group predc">
                            <label for="predecesor" class="col-sm-2 col-md-2 col-lg-2 control-label">Predecesor:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="predecesor" id="predecesor" class="form-control select2" title="Ingrese el predecesor" >
                                    <?php
                                    $html = "";
                                    $html .= "<option value=\"\">Predecesor</option>";
                                    $data = $plan->obtnerPredecesor();
                                    foreach ($data as $row) {
                                        $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))."</option>";
                                    }
                                    echo $html;
                                     ?>
                                </select>
                            </div>
                            <label for="codigo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong style="color:#03C1FB;">*</strong>Código:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="codigo" id="codigo" class="form-control" maxlength="20" title="Ingrese el código" placeholder="Código" onblur="existente();" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nombre" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <!-- onkeypress="return txtValida(event,'car');" -->
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required>
                            </div>
                            <label for="movimiento" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong style="color:#03C1FB;">*</strong>Movimiento:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <label class="radio-inline"><input type="radio" name="movimiento" value="2" id="si" required/> Sí&nbsp &nbsp</label>
                                <label class="radio-inline"><input type="radio" name="movimiento" value="1" id="no" /> No</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="movimiento" class="col-sm-2 col-md-2 col-lg-2 control-label">Indicador Capacidad:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <label class="checkbox-inline"><input type="checkbox" name="chkCapacidad" id="chkCapacidad" value="1" style="margin-top: -5px;"></label>
                            </div>
                            <label for="tipoInv" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Inventario:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="tipoInv" id="tipoInv" class="form-control select2" title="Ingrese el tipo de inventario" required>
                                    <?php
                                    $html = "";
                                    $html .= "<option value=\"\">Tipo Inventario</option>";
                                    $data = $plan->obtnerTipoInventario();
                                    foreach($data as $row){
                                        $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="undFact" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong style="color:#03C1FB;">*</strong>Unidad Factor:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="undFact" id="undFact" class="form-control select2" title="Ingrese la unidad factor" required>
                                    <?php
                                    $html = "";
                                    $html .= "<option value=\"\">Unidad Factor</option>";
                                    $data = $plan->obtnerUnidadFactor();
                                    foreach($data as $row){
                                        $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))."</option>";
                                    }
                                    echo $html;
                                     ?>
                                </select>
                            </div>
                            <label for="tipoAct" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Activo:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="tipoAct" id="tipoAct" class="form-control select2" title="Ingrese la unidad factor" required>
                                    <?php
                                    $html = "";
                                    $html .= "<option value=\"\">Tipo Activo</option>";
                                    $data = $plan->obtnerTipoActivo();
                                    foreach ($data as $row) {
                                        $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltPlanPadre" class="col-sm-2 col-md-2 col-lg-2 control-label">Plan Inventario Padre:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="sltPlanPadre" id="sltPlanPadre" class="form-control select2" title="Selccione plan inventario padre">
                                    <?php
                                    $html = "";
                                    $html .= "<option value=\"\">Plan Inventario Padre</option>";
                                    $data = $plan->obtnerPadre();
                                    foreach ($data as $row) {
                                        $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltFicha" class="col-sm-2 col-md-2 col-lg-2 control-label">Ficha:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="sltFicha" id="sltFicha" class="form-control select2" title="Ingrese ficha">
                                    <?php
                                    $html = "";
                                    $html .= "<option value=\"\">Ficha</option>";
                                    $data = $plan->obnterFicha();
                                    foreach ($data as $row) {
                                        $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="chkConcepto" class="control-label col-sm-2 col-lg-2">Concepto facturable?</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <label class="checkbox-inline"><input type="checkbox" name="chkConcepto" id="chkConcepto" value="1" style="margin-top: -5px;"></label>
                            </div>
                            <label for="codigoBarras" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Código Barras:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="codigoBarras" id="codigoBarras" class="form-control"  placeholder="Código Barras" value="<?php echo $codigo_barras?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="no" class="col-sm-9 col-md-9 col-lg-9 control-label"></label>
                            <div class="col-sm-2 col-md-2 col-lg-2 text-right">
                                <button type="submit" class="btn btn-primary borde-sombra">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-8 col-md-8 col-lg-8 col-sm-2 col-md-2 col-lg-2">
                <table class="tablaC table-condensed" style="margin-top:-20px">
                    <thead>
                        <th><h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="registrar_GS_TIPO_ELEMENTO.php" class="btn btn-primary btnInfo">TIPO DE ELEMENTOS</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="registrar_GF_UNIDAD_FACTOR.php" class="btn btn-primary btnInfo">UNIDAD FACTOR</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="registrar_GF_TIPO_ACTIVO.php" class="btn btn-primary btnInfo">TIPOS DE ACTIVO</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button class="btn btn-primary btnInfo" disabled>ELEMENTO FICHA</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="js/bootstrap.min.js"></script>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Este predecesor ya no puede tener más hijos.</p>
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
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Este código ya existe.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" name="Acept" id="Acept" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="dist/jquery.validate.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript">
        $(".select2").select2();
        $('#no').prop('checked', true);
        $('#si').prop('checked', false);
        $('.radios input').attr({'title': 'Seleccione si tiene movimiento o no.'});
        $("#predecesor").change(function(e){
            var padre = e.target.value;
            if(padre != "" || padre != 0){
                $.get("access.php?controller=Inventario&action=obnterCodigoHijo&padre="+padre, function(data){
                    $("#codigo").val(data);
                });
            }else{
                $('#codigo').val("").removeAttr("readonly");
                $('#si').prop('disabled', false);
                $('#no').prop('disabled', false);
                $('.radios input').attr({'title': 'Seleccione si tiene movimiento o no.'});
                $('#si').prop('checked', false);
                $('#no').prop('checked', true);
            }

            $.get("access.php?controller=Inventario&action=validarTipoInventario&padre="+padre, function(data){
                $("#tipoInv").html(data);
            });
        });

        function existente() {
            var codi = document.form.codigo.value;
            var form_data = {
                is_ajax: 1,
                codigo: +codi
            };
            $.ajax({
                type: "POST",
                url: "consulCodig.php",
                data: form_data,
                success:  function (response) {
                    if(response != 0) {
                        $("#myModal2").modal('show');
                    }
                }
            });
        }

        $('#ver1').click(function(){
            document.location = 'registrar_GF_PLAN_INVENTARIO.php';
        });

        $('#Acept').click(function(){
            $("#codigo").val('');
        });

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
                    $(element).parents(".col-lg-3").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-md-3").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-sm-3").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-lg-4").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-md-4").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-sm-4").addClass("has-error").removeClass('has-success');
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
                    $(element).parents(".col-lg-3").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-md-3").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-sm-3").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-lg-4").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-md-4").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-sm-4").addClass('has-success').removeClass('has-error');
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
    </script>
</body>
</html>