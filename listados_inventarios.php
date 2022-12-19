<?php
require 'head.php';
require 'Conexion/conexion.php';

$compania = $_SESSION['compania'];
?>
    <title>Listado de Inventarios</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/datapicker.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-notify.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
    <style type="text/css" media="screen">
        .client-form input[type="text"]{
            width: 100%;
        }

        .client-form select{
            width: 100%;
        }

        .btn{
            box-shadow: 0px 2px 5px 1px grey;
        }

        .client-form input[type="file"]{
            width: 100%
        }

        .dependencia, .responsable{
            display: none;
        }

        #form .form-group{
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require('menu.php'); ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px;">Listado de Inventarios</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST" target="_selft" enctype="multipart/form-data" action="">
                        <p align="center" style="margin-bottom: 5px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="sltInventario" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Inventario:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltInventario" id="sltInventario" class="select2 form-control text-left" required="">
                                    <option value="">Inventario</option>
                                    <option value="1">Inventario General de Bienes por Dependencia</option>
                                    <option value="2">Inventario General de Bienes por Responsable</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtFechaInicial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" class="form-control" id="txtFechaInicial" name="txtFechaInicial" placeholder="Fecha Inicial" required="">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="txtFechaFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" class="form-control" id="txtFechaFinal" name="txtFechaFinal" placeholder="Fecha Final" required="">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltProductoInicial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Producto Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 text-left">
                                <select name="sltProductoInicial" id="sltProductoInicial" class="select2 form-control" required="">
                                    <?php
                                    $html= "";
                                    $html.= "<option value=\"\">Producto Inicial</option>";
                                    $sqlp = "SELECT DISTINCT
                                                       pr.id_unico AS PRODCTO,
                                                       pln.nombre  AS NOM_PLAN,
                                                       pes.valor   AS SERIE
                                            FROM       gf_producto pr
                                            LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                                            LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                                            LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                                            LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                                            LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                                            WHERE      fic.elementoficha   = 6
                                            AND        pln.tipoinventario  = 2
                                            AND        pln.compania        = $compania
                                            AND        pes.valor          != ' '
                                            ORDER BY   pr.id_unico ASC";
                                    $resp = $mysqli->query($sqlp);
                                    while($rowp = mysqli_fetch_row($resp)){
                                        $html.= "<option value=\"$rowp[0]\"> SERIE :".$rowp[2]." NOMBRE :".ucwords(mb_strtolower($rowp[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltProductoFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Producto Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltProductoFinal" id="sltProductoFinal" class="select2 form-control text-left" required="">
                                    <?php
                                    $html= "";
                                    $html.= "<option value=\"\">Producto Final</option>";
                                    $sqlp = "SELECT DISTINCT
                                                       pr.id_unico AS PRODCTO,
                                                       pln.nombre  AS NOM_PLAN,
                                                       pes.valor   AS SERIE
                                            FROM       gf_producto pr
                                            LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                                            LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                                            LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                                            LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                                            LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                                            WHERE      fic.elementoficha   = 6
                                            AND        pln.tipoinventario  = 2
                                            AND        pes.valor          != ' '
                                            AND        pln.compania        = $compania
                                            ORDER BY   pr.id_unico DESC";
                                    $resp = $mysqli->query($sqlp);
                                    while($rowp = mysqli_fetch_row($resp)){
                                        $html.= "<option value=\"$rowp[0]\"> SERIE :".$rowp[2]." NOMBRE :".ucwords(mb_strtolower($rowp[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group dependencia">
                            <label for="sltDepInicial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Dependencia Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltDepInicial" id="sltDepInicial" class="select2 form-control text-left">
                                <?php
                                $html = "";
                                $html .= "<option value=\"\">Dependencia Inicial</option>";
                                $sql  = "SELECT id_unico, nombre, sigla FROM gf_dependencia WHERE compania = $compania ORDER BY id_unico ASC";
                                $res  = $mysqli->query($sql);
                                while($row = mysqli_fetch_row($res)){
                                    $html .= "<option value=\"$row[0]\">$row[2] ".ucwords(mb_strtolower($row[1]))."</option>";
                                }
                                echo $html;
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group dependencia">
                            <label for="sltDepFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Dependencia Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltDepFinal" id="sltDepFinal" class="select2 form-control text-left">
                                <?php
                                $html = "";
                                $html .= "<option value=\"\">Dependencia Final</option>";
                                $sql  = "SELECT id_unico, nombre, sigla FROM gf_dependencia WHERE compania = $compania ORDER BY id_unico DESC";
                                $res  = $mysqli->query($sql);
                                while($row = mysqli_fetch_row($res)){
                                    $html .= "<option value=\"$row[0]\">$row[2] ".ucwords(mb_strtolower($row[1]))."</option>";
                                }
                                echo $html;
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group responsable">
                            <label for="sltResIni" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Responsable Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltResIni" id="sltResIni" class="select2 form-control text-left">
                                <?php
                                $html = "";
                                $html .= "<option value=\"\">Responsable Inicial</option>";
                                $sql  = "SELECT DISTINCT
                                                   ter.id_unico,
                                                   IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                                                      CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                      (ter.razonsocial),
                                                      CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                                                   CONCAT_WS(' - ',tip.nombre, ter.numeroidentificacion, ter.digitoverficacion) AS IDENT
                                        FROM      gf_dependencia_responsable drs
                                        LEFT JOIN gf_tercero              ter ON drs.responsable        = ter.id_unico
                                        LEFT JOIN gf_tipo_identificacion  tip ON ter.tipoidentificacion = tip.id_unico
                                        LEFT JOIN gf_dependencia          gdp ON drs.dependencia        = gdp.id_unico
                                        WHERE     ter.compania        = $compania
                                        AND       gdp.compania        = $compania
                                        ORDER BY ter.id_unico ASC";
                                $res  = $mysqli->query($sql);
                                while ($row = mysqli_fetch_row($res)) {
                                    $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))." (".ucwords(mb_strtolower($row[2])).")</option>";
                                }
                                echo $html;
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group responsable">
                            <label for="sltResFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Responsable Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltResFinal" id="sltResFinal" class="select2 form-control text-left">
                                <?php
                                $html = "";
                                $html .= "<option value=\"\">Responsable Final</option>";
                                $sql  = "SELECT DISTINCT
                                                   ter.id_unico,
                                                   IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                                                      CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                      (ter.razonsocial),
                                                      CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                                                   CONCAT_WS(' - ',tip.nombre, ter.numeroidentificacion, ter.digitoverficacion) AS IDENT
                                        FROM      gf_dependencia_responsable drs
                                        LEFT JOIN gf_tercero                 ter ON drs.responsable        = ter.id_unico
                                        LEFT JOIN gf_tipo_identificacion     tip ON ter.tipoidentificacion = tip.id_unico
                                        LEFT JOIN gf_dependencia             gdp ON drs.dependencia        = gdp.id_unico
                                        WHERE     ter.compania        = $compania
                                        AND       gdp.compania        = $compania
                                        ORDER BY ter.id_unico DESC";
                                $res  = $mysqli->query($sql);
                                while ($row = mysqli_fetch_row($res)) {
                                    $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))." (".ucwords(mb_strtolower($row[2])).")</option>";
                                }
                                echo $html;
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="optTipoA" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo Archivo:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 text-left">
                                <label for="" class="radio-inline"><input name="optTipoA" id="optPdf" type="radio" required="">Pdf</label>
                                <label for="" class="radio-inline"><input name="optTipoA" id="optExcel" type="radio">Excel</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                                <label for="sltProductoFinal" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <button type="submit" id="btnPdf" class="btn btn-primary glyphicon glyphicon-print"></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require('footer.php'); ?>
            <script src="js/jquery-ui.js"></script>
            <script type="text/javascript" src="js/select2.js"></script>
            <script src="dist/jquery.validate.js"></script>
            <script src="js/bootstrap-notify.js"></script>
            <script type="text/javascript" src="js/md5.js"></script>
            <script>
                $(".select2").select2();

                $("#sltInventario").change(function(){
                    var inventario = $("#sltInventario").val();
                    if(inventario.length > 0){
                        switch(inventario){
                            case '1':
                                $(".dependencia").show('slow');
                                $(".responsable").hide('slow');

                                $("#sltDepInicial").attr("required",true);
                                $("#sltDepFinal").attr("required",true);
                                $("#sltResIni").attr("required",false);
                                $("#sltResFinal").attr("required",false);

                                $("input[name='optTipoA']").attr("disabled",false);

                                $("#optPdf").click(function(){
                                    $("#form").attr("action","informes_almacen/informe_devolutivos_dependencia_pdf.php");
                                });

                                $("#optExcel").click(function(){
                                    $("#form").attr("action","informes_almacen/informe_devolutivos_dependencia_excel.php");
                                });
                                break;
                            case '2':
                                $(".responsable").show('slow');
                                $(".dependencia").hide('slow');

                                $("#sltDepInicial").attr("required",false);
                                $("#sltDepFinal").attr("required",false);
                                $("#sltResIni").attr("required",true);
                                $("#sltResFinal").attr("required",true);

                                $("input[name='optTipoA']").attr("disabled",false);

                                $("#optPdf").click(function(){
                                    $("#form").attr("action","informes_almacen/informe_devolutivos_responsable_pdf.php");
                                });

                                $("#optExcel").click(function(){
                                    $("#form").attr("action","informes_almacen/informe_devolutivos_responsable_excel.php");
                                });
                                break;
                        }
                    }else{
                        $(".dependencia").hide('slow');
                        $(".responsable").hide('slow');

                        $("#sltDepInicial").attr("required",false);
                        $("#sltDepFinal").attr("required",false);
                        $("#sltResIni").attr("required",false);
                        $("#sltResFinal").attr("required",false);

                        $("input[name='optTipoA']").attr("disabled",true);
                    }
                });

                $("input[name='optTipoA']").attr("disabled",true);

                $().ready(function() {
                    var validator = $("#form").validate({
                        ignore: "",
                        rules:{
                            sltTipoPredio:"required",
                            txtCodigo:"required"
                        },
                        messages:{
                            sltTipoPredio: "Seleccione tipo de predio",
                        },
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
                                $(elem).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
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
                    $(".cancel").click(function() {
                        validator.resetForm();
                    });
                });

                $(".dependencia").hide('fast');
                $(".responsable").hide('fast');

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
                    $("#txtFechaInicial").datepicker({changeMonth: true}).val();
                    $("#txtFechaFinal").datepicker({changeMonth: true}).val();
                });

                $("#txtFechaFinal").change(function(){
                    var fechaInicial = $("#txtFechaInicial").val();
                    var fechaFinal   = $("#txtFechaFinal").val();

                    if(fechaFinal < fechaInicial){
                        $("#txtFechaFinal").parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                        $("#txtFechaFinal").val("");
                    }else{
                        $("#txtFechaFinal").parents(".col-sm-5").addClass("has-success").removeClass('has-error');
                    }
                });
            </script>
        </div>
    </div>
</body>
</html>