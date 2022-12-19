
<?php
require ('Conexion/conexion.php');
require ('head.php');
require_once ('./modelAlmacen/inventario.php');

$plan = new inventario();
list($id_unico, $codi, $nombre, $tienemovimiento, $tipoinventario, $tnombre, $unidad, $unidf, $pred, $tipa, $tanombre, $fid_unico, $fnombre, $numHij, $xCantidad, $xConcepto)
    = array(0, 0, "", "", 0, "", 0, 0, 0, 0, "", 0, 0, 0, 0, 0);
if (!empty($_GET["id_plan_inv"])) {
    $id_plan_inv = $_GET["id_plan_inv"];
    $queryPlanInv ="SELECT    pi.id_unico, pi.codi, pi.Nombre, pi.tienemovimiento, 
                    pi.tipoinventario, ti.nombre, pi.unidad, uf.nombre,
                    pi.predecesor, pi.tipoactivo, ta.nombre, f.id_unico, 
                    f.descripcion, pi.xCantidad, pi.xFactura, 
                    pi.codigo_barras 
          FROM      gf_plan_inventario AS pi
          LEFT JOIN gf_tipo_inventario AS ti ON pi.tipoinventario = ti.id_unico
          LEFT JOIN gf_unidad_factor   AS uf ON pi.unidad         = uf.id_unico
          LEFT JOIN gf_tipo_activo     AS ta ON pi.tipoactivo     = ta.id_unico
          LEFT JOIN gf_ficha           AS f  ON pi.ficha          = f.id_unico
          WHERE     md5(pi.id_unico) = '$id_plan_inv'";
    $resultado = $mysqli->query($queryPlanInv);
    $row = mysqli_fetch_row($resultado);
    list($id_unico, $codi, $nombre, $tienemovimiento, $tipoinventario, $tnombre,  $unidad, 
        $unidf, $pred, $tipa, $tanom, $fid_unico, $fnombre, $xCantidad, $xConcepto, $codigo_barras) =
    array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], 
        $row[9], $row[10], $row[11], $row[12], $row[13], $row[14],$row[15]);
}
 ?>
    <title>Modificar Plan Inventario</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/desing.css">
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
            <?php require ('menu.php'); ?>
            <div class="col-sm-8 col-md-8 col-lg-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin: 0px 4px 5px;">Modificar Plan  Inventario</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12 col-md-12 col-lg-12">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGFPlanInventario.php?action=modify">
                        <p align="center" style="margin-bottom: 10px; margin-top: 4px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" id="id" value="<?php echo $id_unico;?>">
                        <input type="hidden" name="id_predec" id="id_predec" value="<?php  echo md5($pred);?>">
                        <div class="form-group predc">
                            <label for="predecesor" class="col-sm-2 col-md-2 col-lg-2 control-label">Predecesor:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="predecesor" id="predecesor" class="form-control col-sm-1 col-md-1 col-lg-1 select2" title="Ingrese el predecesor" >
                                    <?php
                                    if(empty($pred)){
                                        echo "<option value=''>Predecesor</option>";
                                        $sql_pr = "SELECT    gfpi.id_unico, CONCAT(gfpi.codi,' ', gfpi.nombre) plan
                                                   FROM      gf_plan_inventario gfpi
                                                   LEFT JOIN gf_plan_inventario pi
                                                   ON        gfpi.predecesor = pi.id_unico
                                                   WHERE     gfpi.tienemovimiento = 1
                                                   ORDER BY  gfpi.codi ASC";
                                        $res_pr = $mysqli->query($sql_pr);
                                        while ($row_pr = mysqli_fetch_row($res_pr)) {
                                            echo "<option value=\"$row_pr[0]\">".ucwords(mb_strtolower($row_pr[1]))."</option>";
                                        }
                                    }else{
                                        $sql_pr = "SELECT    gfpi.id_unico, CONCAT(gfpi.codi,' ', gfpi.nombre) plan
                                                   FROM      gf_plan_inventario gfpi
                                                   WHERE     (gfpi.id_unico = $pred)
                                                   ORDER BY  gfpi.codi ASC";
                                        $res_pr = $mysqli->query($sql_pr);
                                        $row_pr = mysqli_fetch_row($res_pr);
                                        echo "<option value=\"$row_pr[0]\">".ucwords(mb_strtolower($row_pr[1]))."</option>";
                                        $sql_pr = "SELECT    gfpi.id_unico, CONCAT(gfpi.codi,' ', gfpi.nombre) plan
                                                   FROM      gf_plan_inventario gfpi
                                                   LEFT JOIN gf_plan_inventario pi ON gfpi.predecesor = pi.id_unico
                                                   WHERE     (gfpi.tienemovimiento = 1) AND (gfpi.id_unico != $pred)
                                                   ORDER BY  gfpi.codi ASC";
                                        $res_pr = $mysqli->query($sql_pr);
                                        while ($row_pr = mysqli_fetch_row($res_pr)) {
                                            echo "<option value=\"$row_pr[0]\">".ucwords(mb_strtolower($row_pr[1]))."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="codigo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Código:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="codigo" id="codigo" class="form-control" maxlength="15" placeholder="Código" value="<?php echo $codi?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nombre" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Nombre:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower($nombre));?>"  required>
                            </div>
                            <label for="movimiento" class="col-sm-3 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Movimiento:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3" >
                                <?php
                                switch ($tienemovimiento) {
                                    case '1':
                                        echo "<label class=\"radio-inline\"><input type=\"radio\" name=\"movimiento\" id=\"si\" value=\"2\" /> Sí&nbsp &nbsp </label>";
                                        echo "<label class=\"radio-inline\"><input type=\"radio\" name=\"movimiento\" id=\"no\" value=\"1\" checked /> No </label>";
                                        break;

                                    case '2':
                                        echo "<label class=\"radio-inline\"><input type=\"radio\" name=\"movimiento\" id=\"si\" value=\"2\" checked/> Sí&nbsp &nbsp </label>";
                                        echo "<label class=\"radio-inline\"><input type=\"radio\" name=\"movimiento\" id=\"no\" value=\"1\" /> No </label>";
                                        break;

                                    default:
                                        echo "<label class=\"radio-inline\"><input type=\"radio\" name=\"movimiento\" id=\"si\" value=\"2\" /> Sí&nbsp &nbsp</label>";
                                        echo "<label class=\"radio-inline\"><input type=\"radio\" name=\"movimiento\" id=\"no\" value=\"1\" /> No </label>";
                                        break;
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipoInv" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo Inventario:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="tipoInv" id="tipoInv" class="form-control select2" <?php if($numHij != 0){ echo 'title="El tipo inventario no puede ser modificado, el registro seleccionado está siendo utilizado por otra dependencia."';}else{ echo 'title="Ingrese el tipo de inventario"';}?> required>
                                    <?php
                                    if(empty($tipoinventario)){
                                        echo "<option value=\"\">Tipo Inventario</option>";
                                        $sql_t = "SELECT id_unico, nombre FROM gf_tipo_inventario";
                                        $res_t = $mysqli->query($sql_t);
                                        while($row_t = mysqli_fetch_row($res_t)){
                                            echo "<option value=\"$row_t[0]\">".ucwords(mb_strtolower($row_t[1]))."</option>";
                                        }
                                    }else{
                                        $sql_t = "SELECT id_unico, nombre FROM gf_tipo_inventario WHERE id_unico = $tipoinventario";
                                        $res_t = $mysqli->query($sql_t);
                                        $row_t = mysqli_fetch_row($res_t);
                                        echo "<option value=\"$row_t[0]\">".ucwords(mb_strtolower($row_t[1]))."</option>";
                                        $sql_t = "SELECT id_unico, nombre FROM gf_tipo_inventario WHERE id_unico != $tipoinventario";
                                        $res_t = $mysqli->query($sql_t);
                                        while($row_t = mysqli_fetch_row($res_t)){
                                            echo "<option value=\"$row_t[0]\">".ucwords(mb_strtolower($row_t[1]))."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="tipoAct" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo Activo:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="tipoAct" id="tipoAct" class="form-control col-sm-1 col-md-1 col-lg-1 select2" title="Ingrese la unidad factor" required>
                                    <?php
                                    if(empty($tipa)){
                                        echo "<option value=\"\">Tipo Activo</option>";
                                        $sql_ta = "SELECT id_unico, nombre FROM gf_tipo_activo ORDER BY nombre ASC";
                                        $res_ta = $mysqli->query($sql_ta);
                                        while($row_ta = mysqli_fetch_row($res_ta)){
                                            echo "<option value=\"$row_ta[0]\">".ucwords(mb_strtolower($row_ta[1]))."</option>";
                                        }
                                    }else{
                                        $sql_ta = "SELECT id_unico, nombre FROM gf_tipo_activo WHERE id_unico = $tipa ORDER BY nombre ASC";
                                        $res_ta = $mysqli->query($sql_ta);
                                        $row_ta = mysqli_fetch_row($res_ta);
                                        echo "<option value=\"$row_ta[0]\">".ucwords(mb_strtolower($row_ta[1]))."</option>";
                                        $sql_ta = "SELECT id_unico, nombre FROM gf_tipo_activo WHERE id_unico != $tipa ORDER BY nombre ASC";
                                        $res_ta = $mysqli->query($sql_ta);
                                        while($row_ta = mysqli_fetch_row($res_ta)){
                                            echo "<option value=\"$row_ta[0]\">".ucwords(mb_strtolower($row_ta[1]))."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <button type="button" id="btn-asignar" class="btn btn-primary borde-sombra" title="Asignar a hijos"><span class="glyphicon glyphicon-sort-by-alphabet"></span></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="undFact" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Unidad Factor:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="undFact" id="undFact" class="form-control select2"  title="Ingrese la unidad factor" required>
                                    <?php
                                    if(empty($unidad)){
                                        echo "<option value=\"\">Unidad Factor</option>";
                                        $sql_u = "SELECT id_unico, nombre FROM gf_unidad_factor ORDER BY nombre ASC";
                                        $res_u = $mysqli->query($sql_u);
                                        while ($row_u = mysqli_fetch_row($res_u)) {
                                            echo "<option value=\"$row_u[0]\">".ucwords(mb_strtolower($row_u[1]))."</option>";
                                        }
                                    }else{
                                        $sql_u = "SELECT id_unico, nombre FROM gf_unidad_factor WHERE id_unico = $unidad ORDER BY nombre ASC";
                                        $res_u = $mysqli->query($sql_u);
                                        $row_u = mysqli_fetch_row($res_u);
                                        echo "<option value=\"$row_u[0]\">".ucwords(mb_strtolower($row_u[1]))."</option>";
                                        $sql_u = "SELECT id_unico, nombre FROM gf_unidad_factor WHERE id_unico != $unidad ORDER BY nombre ASC";
                                        $res_u = $mysqli->query($sql_u);
                                        while($row_u = mysqli_fetch_row($res_u)){
                                            echo "<option value=\"$row_u[0]\">".ucwords(mb_strtolower($row_u[1]))."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="sltFicha" class="col-sm-2 col-md-2 col-lg-2 control-label">Ficha:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="sltFicha" id="sltFicha" class="form-control col-sm-1 col-md-1 col-lg-1 select2" title="Ingrese ficha">
                                    <?php
                                    if(empty($fid_unico)){
                                        echo "<option value=\"\">Ficha</option>";
                                        $sql_Fi = "SELECT id_unico,descripcion FROM gf_ficha ORDER BY id_unico";
                                        $res_Fi = $mysqli->query($sql_Fi);
                                        while($row_f = mysqli_fetch_row($res_Fi)){
                                            echo '<option value="'.$row_f[0].'">'.ucwords(strtolower($row_f[1])).'</option>';
                                        }
                                    }else{
                                        echo "<option value=\"$fid_unico\">".ucwords(mb_strtolower($fnombre))."</option>";
                                        $sql_Fi = "SELECT id_unico,descripcion FROM gf_ficha WHERE id_unico != $fid_unico ORDER BY id_unico";
                                        $res_Fi = $mysqli->query($sql_Fi);
                                        while($row_f = mysqli_fetch_row($res_Fi)){
                                            echo '<option value="'.$row_f[0].'">'.ucwords(strtolower($row_f[1])).'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php
                            $sqlAsoc = "SELECT plan_padre,id_unico FROM gf_plan_inventario_asociado WHERE plan_hijo =  $id_unico";
                            $resultAsoc = $mysqli->query($sqlAsoc);
                            $padre = mysqli_fetch_row($resultAsoc);
                            echo '<input value="'.$padre[1].'" name="planAso" type="hidden" class="hidden">';
                            ?>
                            <label for="sltPlanPadre" class="col-sm-2 col-md-2 col-lg-2 control-label">Plan Inventario Padre:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="sltPlanPadre" id="sltPlanPadre" class="form-control select2" title="Selccione plan inventario padre">
                                    <?php
                                    $cantidad = mysqli_num_rows($resultAsoc);
                                    if($cantidad!==0){
                                        $sqlPadre = "select id_unico,CONCAT(codi,' - ',nombre) from gf_plan_inventario where id_unico=$padre[0] order by id_unico";
                                        $resultPadre = $mysqli->query($sqlPadre);
                                        $planPadre = $resultPadre->fetch_row();
                                        echo '<option value="'.$planPadre[0].'">'.$planPadre[1].'</option>';
                                        $sqlPlan = "select id_unico,CONCAT(codi,' - ',nombre) from gf_plan_inventario where id_unico!=$row[0] and id_unico!=$planPadre[0] order by id_unico";
                                        $resultPlan = $mysqli->query($sqlPlan);
                                        while($campo=$resultPlan->fetch_row()){
                                            echo '<option value="'.$campo[0].'">'.ucwords(strtolower($campo[1])).'</option>';
                                        }
                                        echo "<option value=\"\"></option>";
                                    }else{
                                        echo '<option value="">Plan Inventario</option>';
                                        $sqlPlan = "select id_unico,CONCAT(codi,' - ',nombre) from gf_plan_inventario where id_unico!=$row[0] order by id_unico";
                                        $resultPlan = $mysqli->query($sqlPlan);
                                        while($campo=$resultPlan->fetch_row()){
                                            echo '<option value="'.$campo[0].'">'.ucwords(strtolower($campo[1])).'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="movimiento" class="col-sm-2 col-md-2 col-lg-2 control-label">Indicador Capacidad:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <?php
                                if($xCantidad == 1){
                                    echo "<label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"chkCapacidad\" id=\"chkCapacidad\" value=\"1\" style=\"margin-top: -5px;\" checked></label>";
                                }else{
                                    echo "<label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"chkCapacidad\" id=\"chkCapacidad\" value=\"1\" style=\"margin-top: -5px;\"></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="chkConcepto" class="control-label col-sm-2 col-lg-2">Concepto facturable?</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <?php
                                if($xConcepto == 1){
                                    echo "<label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"chkConcepto\" id=\"chkConcepto\" value=\"1\" style=\"margin-top: -5px;\" checked></label>";
                                }else{
                                    echo "<label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"chkConcepto\" id=\"chkConcepto\" value=\"1\" style=\"margin-top: -5px;\"></label>";
                                }
                                ?>
                            </div>
                            <label for="codigoBarras" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Código Barras:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="codigoBarras" id="codigoBarras" class="form-control"  placeholder="Código Barras" value="<?php echo $codigo_barras?>" required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-9 col-md-9 col-lg-9 control-label"></label>
                            <div class="col-sm-2 col-md-2 col-lg-2 text-right">
                                <button type="submit" class="btn btn-primary borde-sombra">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-8 col-md-8 col-lg-8 col-sm-2 col-md-2 col-lg-2">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <h2 class="titulo" align="center" style="font-size:17px; margin-top: 0px;">Información adicional</h2>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <a href="registrar_GS_TIPO_ELEMENTO.php" class="btn btn-primary btnInfo">TIPO DE ELEMENTO</a>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <a href="registrar_GF_UNIDAD_FACTOR.php" class="btn btn-primary btnInfo">UNIDAD FACTOR</a>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <a href="registrar_GF_TIPO_ACTIVO.php" class="btn btn-primary btnInfo">TIPOS DE ACTIVO</a>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <a class="btn btn-primary btnInfo" href="registrar_GF_ELEMENTO_FICHA.php">ELEMENTO FICHA</a>
                </div>
                <?php
                if($xConcepto == 1){
                    $html = "";
                    $html .= "\n\t<div class=\"col-sm-12 col-md-12 col-lg-12\">";
                    $html .= "\n\t\t<a class=\"btn btn-primary btnInfo\" href=\"access.php?controller=Inventario&action=vistaConceptos&plan=$id_plan_inv\">ESTABLECER PRECIO</a>";
                    $html .= "\n\t</div>";
                    echo $html;
                }
                ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>Este predecesor ya no puede tener más hijos.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
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
                <div class="modal-body" style="margin-top: 8px;">
                    <p>Este código ya existe.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Acept" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_asignado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>Información asignada correctamente a hijos.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btn-asig" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script type="text/javascript">
        $(".select2").select2();

        $("#predecesor").change(function(e){
            const padre = e.target.value;
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

            $.post("access.php?controller=Inventario&action=obtenerDatosPadre", { plan:padre }, function (data) {
                var res = JSON.parse(data);
                if(jQuery.isEmptyObject(res) == false){
                    $("#tipoInv").val(res[5]).trigger("change");
                    $("#undFact").val(res[6]).change();
                    $("#tipoAct").val(res[8]).change();
                }
            });
        });

        $("#btn-asignar").click(function(e){
            var tipo   = $("#tipoAct").val();
            var codigo = $("#codigo").val();
            var result = false;
            if(tipo != "" && codigo != ""){
                $.post('access.php?controller=Inventario&action=asignar_tipo&codigo='+codigo+'&tipo='+tipo,
                    function(data, textStatus, xhr) {
                       result = JSON.parse(data);
                       if(result == true){
                            $("#modal_asignado").modal("show");
                       }
                    }
                );
            }
        });

        $('#ver1').click(function(){
            window.location.reload();
        });

        $('#Acept').click(function(){
            window.location.reload();
        });

        var codigo = $("#codigo").val();
        if(codigo.length == 5){
            $("#btn-asignar").css('display', 'block');
        }else{
            $("#btn-asignar").css('display', 'none');
        }

        var validator = $("#form").validate({
            ignore: "",
            errorElement:"em",
            errorPlacement: function(error){
                error.addClass('help-block');
            },
            highlight: function(element){
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
                if($(element).attr('type') === 'radio'){
                    $(element.form).find("input[type=radio]").each(function(which){
                        $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                        $(this).addClass("has-error");
                    });
                } else {
                    $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                    $(element).addClass("has-error");
                }
            },
            unhighlight:function(element){
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
                if($(element).attr('type') === 'radio'){
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

        $("#chkConcepto").click(function (e) {
            var btn = $(this);
            if(btn.is(':checked')){
                $("#sltConceptoF").attr("readonly", false);
            }else{
                $("#sltConceptoF").attr("readonly", true);
            }
        });

        <?php
        if($xConcepto == 1){
            $html = "";
            $html .= "$('#sltConceptoF').removeAttr('readonly');";
            echo $html;
        }
        ?>
    </script>
    <?php require ('footer.php'); ?>
</body>
</html>