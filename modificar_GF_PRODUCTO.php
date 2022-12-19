<?php
require ('Conexion/conexion.php');
require ('head.php');

$producto  = "";
$meses     = "";
$vidaUtilR = "";
$prod      = "";
$serie     = "";
$nombreP   = "";
if(!empty($_GET['producto'])){
    $prod = $_GET['producto'];
    $sql = "SELECT     pro.id_unico              AS ID,
                       pro.meses                 AS MESES,
                       pro.vida_util_remanente   AS VIDAU,
                       pln.nombre                AS NOM_PLAN,
                       pes.valor                 AS SERIE
            FROM       gf_producto pro
            LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pro.id_unico
            LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
            LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
            LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pro.id_unico
            LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
            WHERE      fic.elementoficha   = 6
            AND        md5(pro.id_unico)   = '$prod'";
    $res = $mysqli->query($sql);
    $row = mysqli_fetch_row($res);

    $producto   = $row[0];
    $meses      = (int) $row[1];
    $vidaUtilR  = (int) $row[2];
    $nombreP    = $row[3];
    $serie      = $row[4];
}
?>
    <title>Ingreso Datos Depreciación Producto</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/datapicker.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-notify.css">
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
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px;">Ingreso Datos Depreciación Producto</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="access.php?controller=Producto&action=actualirDatosD">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="sltProductos" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Producto:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltProductos" id="sltProductos" class="select2 form-control" required="">
                                    <?php
                                    $html = "";
                                    if(empty($producto)){
                                        $html .= "\t<option value=\"\">Productos</option>";
                                        $sqlp = "SELECT    pr.id_unico AS PRODCTO,
                                                           pln.nombre  AS NOM_PLAN,
                                                           pes.valor   AS SERIE
                                                FROM       gf_producto pr
                                                LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                                                LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                                                LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                                                LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                                                LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                                                WHERE      fic.elementoficha   = 6
                                                ORDER BY   SERIE ASC";
                                        $resp = $mysqli->query($sqlp);
                                        while($rowp = mysqli_fetch_row($resp)){
                                            $html.= "\n\t\t\t\t\t\t\t\t\t\t<option value=\"$rowp[0]\"> SERIE :".$rowp[2]." NOMBRE :".ucwords(mb_strtolower($rowp[1]))."</option>";
                                        }
                                    }else{
                                        $html .= "\t<option value=\"$producto\"> SERIE:".$serie." NOMBRE :".ucwords(mb_strtolower($nombreP))."</option>";
                                        $sqlp = "SELECT    pr.id_unico AS PRODCTO,
                                                           pln.nombre  AS NOM_PLAN,
                                                           pes.valor   AS SERIE
                                                FROM       gf_producto pr
                                                LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                                                LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                                                LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                                                LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                                                LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                                                WHERE      fic.elementoficha   = 6
                                                AND        pr.id_unico        != $producto
                                                ORDER BY   SERIE ASC";
                                        $resp = $mysqli->query($sqlp);
                                        while($rowp = mysqli_fetch_row($resp)){
                                            $html.= "\n\t\t\t\t\t\t\t\t\t\t<option value=\"$rowp[0]\"> SERIE :".$rowp[2]." NOMBRE :".ucwords(mb_strtolower($rowp[1]))."</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtMeses" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Meses:</label>
                            <div class="col-sm-5 col-sm-5 col-lg-5">
                                <input type="text" name="txtMeses" placeholder="Meses" class="form-control" onkeypress="return txtValida(event, 'num')" value="<?php echo $meses ?>" required="">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="txtVidaUtil" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Vida Util Remanente:</label>
                            <div class="col-sm-5 col-sm-5 col-lg-5">
                                <input type="text" name="txtVidaUtil" placeholder="Vida Util Remanente" class="form-control" onkeypress="return txtValida(event, 'num')" value="<?php echo $vidaUtilR ?>" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <label for="sltProductoFinal" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                                <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                    <button type="submit" class="btn btn-primary glyphicon glyphicon-floppy-disk"></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require 'footer.php'; ?>
            <script src="js/jquery-ui.js"></script>
            <script type="text/javascript" src="js/select2.js"></script>
            <script src="dist/jquery.validate.js"></script>
            <script src="js/bootstrap-notify.js"></script>
            <script type="text/javascript" src="js/md5.js"></script>
            <script src="js/plugins/datepicker/bootstrap-datepicker.js"></script>
            <script src="js/md5.js"></script>
            <script>
                $(".select2").select2();

                $("#sltProductos").change(function(){
                    var producto = $("#sltProductos").val();
                    window.location = 'modificar_GF_PRODUCTO.php?producto='+md5(producto);
                });

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
                        }
                    });
                    $(".cancel").click(function() {
                        validator.resetForm();
                    });
                });
            </script>
        </div>
    </div>
</body>
</html>