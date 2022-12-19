<?php
require 'head.php';
require 'Conexion/conexion.php';

$compania = $_SESSION['compania'];
?>
    <title>Listado de Depreciación</title>
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

        .dependencia, .responsable, .fechaIF, .periodo, .producto, .nivel, .periodoX{
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
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px;">Listado de Deterioros</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target="_blank">
                        <p align="center" style="margin-bottom: 5px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="sltInventario" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Informe:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltInventario" id="sltInventario" class="select2 form-control text-left" required="">
                                    <option value="">Informe</option>
                                    <option value="3">Informe Detallado de Depreciación</option>
                                    <option value="4">Relación de Bienes Depreciados</option>
                                    <option value="5">Relación de Bienes Depreciados Acumulado</option>
                                    <option value="6">Relación de Bienes Depreciados por Nivel</option>
                                    <option value="7">Informe Devolutivos</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group fechaIF">
                            <label for="txtFechaInicial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" class="form-control" id="txtFechaInicial" name="txtFechaInicial" placeholder="Fecha Inicial">
                            </div>
                        </div>
                        <div class="form-group fechaIF" style="margin-top: -10px">
                            <label for="txtFechaFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" class="form-control" id="txtFechaFinal" name="txtFechaFinal" placeholder="Fecha Final">
                            </div>
                        </div>
                        <div class="form-group periodo">
                            <label for="txtPeridoF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Periodo Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" class="form-control" id="txtPeridoF" name="txtPeridoF" placeholder="Periodo Final">
                            </div>
                        </div>
                        <div class="form-group producto" style="margin-top: -10px">
                            <label for="sltProductoInicial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Producto Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 text-left">
                                <select name="sltProductoInicial" id="sltProductoInicial" class="select2 form-control">
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
                                            AND        pln.compania        = $compania
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
                        <div class="form-group producto">
                            <label for="sltProductoFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Producto Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltProductoFinal" id="sltProductoFinal" class="select2 form-control text-left">
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
                                $sql  = "SELECT id_unico, nombre, sigla FROM gf_dependencia WHERE compania = $compania ORDER BY sigla ASC";
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
                                $sql  = "SELECT id_unico, nombre, sigla FROM gf_dependencia WHERE compania = $compania ORDER BY sigla DESC";
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
                                         LEFT JOIN gf_tercero                 ter ON drs.responsable        = ter.id_unico
                                         LEFT JOIN gf_tipo_identificacion     tip ON ter.tipoidentificacion = tip.id_unico
                                         LEFT JOIN gf_dependencia             gdp ON drs.dependencia        = gdp.id_unico
                                         WHERE     ter.compania        = $compania
                                         AND       gdp.compania        = $compania
                                         ";
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
                                         ";
                                $res  = $mysqli->query($sql);
                                while ($row = mysqli_fetch_row($res)) {
                                    $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))." (".ucwords(mb_strtolower($row[2])).")</option>";
                                }
                                echo $html;
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group nivel">
                            <label for="optNivel3" class="control-label control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Nivel de Grupos</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 text-left">
                                <label for="optNivel3" class="radio-inline"><input name="optNivel" id="optNivel3" type="radio" value="3">3 Digitos</label>
                                <label for="optNivel5" class="radio-inline"><input name="optNivel" id="optNivel5" type="radio" value="5">5 Digitos</label>
                            </div>
                        </div>
                        <div class="form-group periodoX">
                            <label for="txtPeriodoX" class="control-label col-sm-5 col-md-5 col-lg-5 text-left"><strong class="obligado">*</strong>Periodo Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" class="form-control" name="txtPeriodoX" id="txtPeriodoX" value="" placeholder="Periodo" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="optTipoA" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo Archivo:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 text-left">
                                <label for="optPdf"   class="radio-inline"><input name="optTipoA" id="optPdf" type="radio" required="" disabled="">Pdf</label>
                                <label for="optExcel" class="radio-inline"><input name="optTipoA" id="optExcel" type="radio" disabled="">Excel</label>
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
            <script src="js/plugins/datepicker/bootstrap-datepicker.js"></script>
            <script>
                $(".select2").select2();

                $("#sltInventario").change(function(){
                    var inventario = $("#sltInventario").val();
                    switch(inventario){
                        case '3':
                            $(".dependencia").hide('slow');
                            $(".responsable").hide('slow');
                            $(".fechaIF").hide('slow');
                            $(".periodoX").hide("slow");
                            $(".periodo").show('slow');
                            $(".nivel").hide('slow');
                            $(".producto").show('slow');

                            $("#sltDepInicial").attr("required",false);
                            $("#sltDepFinal").attr("required",false);
                            $("#sltResIni").attr("required",false);
                            $("#sltResFinal").attr("required",false);
                            $("#txtFechaInicial").attr("required",false);
                            $("#txtFechaFinal").attr("required",false);
                            $("#txtPeridoF").attr("required",true);
                            $("#sltProductoInicial").attr("required",true);
                            $("#sltProductoFinal").attr("required",true);
                            $("#optNivel3").attr("required",false);
                            $("#txtPeridoX").attr("required", false);

                            $("input[name='optTipoA']").attr("disabled",false);
                            $("input[name='optTipoA']").removeAttr('checked');
                            $("input[name='optNivel']").attr("disabled",true);
                            $("input[name='optNivel']").removeAttr('checked');

                            $("#optPdf").click(function(){
                                $("#form").attr("action","informes_almacen/listado_productos_depreciacion_pdf.php");
                            });

                            $("#optExcel").click(function(){
                                $("#form").attr("action","informes_almacen/listado_productos_depreciacion_excel.php");
                            });
                            break;
                        case '4':
                            $(".dependencia").hide('slow');
                            $(".responsable").hide('slow');
                            $(".fechaIF").hide('slow');
                            $(".producto").hide('slow');
                            $(".nivel").hide('slow');
                            $(".periodo").show('slow');
                            $(".periodoX").hide("slow");

                            $("#sltDepInicial").attr("required",false);
                            $("#sltDepFinal").attr("required",false);
                            $("#sltResIni").attr("required",false);
                            $("#sltResFinal").attr("required",false);
                            $("#sltProductoInicial").attr("required",false);
                            $("#sltProductoFinal").attr("required",false);
                            $("#txtPeridoF").attr("required",true);
                            $("#optNivel3").attr("required",false);
                            $("#txtPeridoX").attr("required", false);

                            $("input[name='optTipoA']").attr("disabled",false);
                            $("input[name='optTipoA']").removeAttr('checked');
                            $("input[name='optNivel']").attr("disabled",true);
                            $("input[name='optNivel']").removeAttr('checked');

                            $("#optPdf").click(function(){
                                $("#form").attr("action","informes_almacen/relacion_bienes_depreciacion_periodo_pdf.php");
                            });

                            $("#optExcel").click(function(){
                                $("#form").attr("action","informes_almacen/relacion_bienes_depreciacion_periodo_excel.php");
                            });
                            break;
                        case '5':
                            $(".dependencia").hide('slow');
                            $(".responsable").hide('slow');
                            $(".fechaIF").hide('slow');
                            $(".periodo").show('slow');
                            $(".nivel").hide('slow');
                            $(".producto").hide('slow');
                            $(".periodoX").hide("slow");

                            $("#sltDepInicial").attr("required",false);
                            $("#sltDepFinal").attr("required",false);
                            $("#sltResIni").attr("required",false);
                            $("#sltResFinal").attr("required",false);
                            $("#txtPeridoF").attr("required",true);
                            $("#sltProductoInicial").attr("required",false);
                            $("#sltProductoFinal").attr("required",false);
                            $("#optNivel3").attr("required",false);
                            $("#txtPeridoX").attr("required", false);

                            $("input[name='optTipoA']").attr("disabled",false);
                            $("input[name='optTipoA']").removeAttr('checked');
                            $("input[name='optNivel']").attr("disabled",true);
                            $("input[name='optNivel']").removeAttr('checked');

                            $("#optPdf").click(function(){
                                $("#form").attr("action","informes_almacen/relacion_bienes_depreciacion_periodo_s_pdf.php");
                            });

                            $("#optExcel").click(function(){
                                $("#form").attr("action","informes_almacen/relacion_bienes_depreciacion_periodo_s_excel.php");
                            });
                            break;
                        case '6':
                            $(".dependencia").hide('slow');
                            $(".responsable").hide('slow');
                            $(".fechaIF").hide('slow');
                            $(".periodo").show('slow');
                            $(".nivel").show('slow');
                            $(".producto").hide('slow');
                            $(".periodoX").hide('slow');

                            $("#sltDepInicial").attr("required",false);
                            $("#sltDepFinal").attr("required",false);
                            $("#sltResIni").attr("required",false);
                            $("#sltResFinal").attr("required",false);
                            $("#txtPeridoF").attr("required",true);
                            $("#sltProductoInicial").attr("required",false);
                            $("#sltProductoFinal").attr("required",false);
                            $("#optNivel3").attr("required",true);
                            $("#txtPeridoX").attr("required", false);

                            $("input[name='optTipoA']").attr("disabled",false);
                            $("input[name='optTipoA']").removeAttr('checked');
                            $("input[name='optNivel']").attr("disabled",false);
                            $("input[name='optNivel']").removeAttr('checked');

                            $("#optPdf").click(function(){
                                $("#form").attr("action","informes_almacen/relacion_bienes_depurados_digitos_pdf.php");
                            });

                            $("#optExcel").click(function(){
                                $("#form").attr("action","informes_almacen/relacion_bienes_depurados_digitos_excel.php");
                            });
                            break;
                        case "7":
                            $(".dependencia").hide('slow');
                            $(".responsable").hide('slow');
                            $(".fechaIF").hide('slow');
                            $(".periodo").hide('slow');
                            $(".producto").hide('slow');
                            $(".nivel").hide('slow');
                            $(".periodoX").show("slow");

                            $("#sltDepInicial").attr("required",false);
                            $("#sltDepFinal").attr("required",false);
                            $("#sltResIni").attr("required",false);
                            $("#sltResFinal").attr("required",false);
                            $("#txtPeridoF").attr("required",false);
                            $("#sltProductoInicial").attr("required",false);
                            $("#sltProductoFinal").attr("required",false);
                            $("#optNivel3").attr("required",false);
                            $("#txtPeridoX").attr("required", true);

                            $("input[name='optTipoA']").attr("disabled",false);
                            $("input[name='optTipoA']").removeAttr('checked');
                            $("input[name='optNivel']").attr("disabled",false);
                            $("input[name='optNivel']").removeAttr('checked');

                            $("#optPdf").click(function(){
                                $("#form").attr("action","informes_almacen/relacion_bienes_contabilidad_pdf.php");
                            });

                            $("#optExcel").click(function(){
                                $("#form").attr("action","informes_almacen/relacion_bienes_contabilidad_excel.php");
                            });
                            break;
                        default:
                            $(".dependencia").hide('slow');
                            $(".responsable").hide('slow');
                            $(".fechaIF").hide('slow');
                            $(".periodo").hide('slow');
                            $(".producto").hide('slow');
                            $(".nivel").hide('slow');
                            $(".periodoX").hide('slow');

                            $("#sltDepInicial").attr("required",false);
                            $("#sltDepFinal").attr("required",false);
                            $("#sltResIni").attr("required",false);
                            $("#sltResFinal").attr("required",false);
                            $("#txtPeridoF").attr("required",false);
                            $("#sltProductoInicial").attr("required",false);
                            $("#sltProductoFinal").attr("required",false);
                            $("#optNivel3").attr("required",false);
                            $("#txtPeridoX").attr("required", false);
                            $("input[name='optTipoA']").attr("disabled",true);
                            $("input[name='optTipoA']").removeAttr('checked');

                            $("input[name='optNivel']").attr("disabled",false);
                            $("input[name='optNivel']").removeAttr('checked');
                            break;
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
                $(".fechaIF").hide('fast');
                $(".periodo").hide('fast');
                $(".producto").hide('fast');

                jQuery(function ($) {
                    $.fn.datepicker.dates['en'] = {
                        days:        ['Domingo',   'Lunes',  'Martes',
                                      'Miércoles', 'Jueves', 'Viernes',
                                      'Sábado'],
                        daysShort:   ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
                        daysMin:     ['Do',  'Lu',  'Ma',  'Mi',  'Ju',  'Vi',  'Sá'],
                        months:      ['Enero',   'Febrero',   'Marzo',
                                      'Abril',   'Mayo',      'Junio',
                                      'Julio',   'Agosto',    'Septiembre',
                                      'Octubre', 'Noviembre', 'Diciembre'],
                        monthsShort: ["Ene", "Feb", "Mar", "Apr", "May", "Jun",
                                      "Jul", "Aug", "Sep", "Oct", "Nov", "Dic"],
                        today:       "Hoy",
                        clear:       "Limpiar",
                        weekStart:   0,
                    };

                    $.datepicker.setDefaults($.datepicker.regional['es']);
                    $("#txtFechaInicial").datepicker({
                        format:      "dd/mm/yyyy"
                    });
                    $("#txtFechaFinal").datepicker({
                        format:      "dd/mm/yyyy"
                    });

                    $("#txtPeridoF, #txtPeriodoX").datepicker({
                        format:      "mm/yyyy",
                        titleFormat: "MM yyyy",
                        viewMode: "years",
                        minViewMode: "months",
                        autoclose: true
                    });
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