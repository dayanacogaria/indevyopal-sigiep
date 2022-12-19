<?php require_once './Conexion/conexion.php';
?>
<div class="modal fade modalI" id="modalFichaInventario" role="dialog" align="center" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog container">
        <div class="modal-content" style="width:800px">
                <div id="forma-modal" class="modal-header">
                    <?php

                    $c = "";
                    if(!empty($_POST['ficha'])){
                        $f = $_POST['ficha'];
                        $d = $_POST['movimiento'];
                        $existencias = $_POST['cantidad'];
                        $sqlProducto = "SELECT DISTINCT COUNT(prdes.fichainventario) AS cantidad FROM gf_producto_especificacion prdes
                                    LEFT JOIN gf_producto pr on pr.id_unico = prdes.producto
                                    LEFT JOIN gf_ficha_inventario fin on prdes.fichainventario  = fin.id_unico
                                    LEFT JOIN gf_ficha fch on fin.ficha = fch.id_unico
                                    LEFT JOIN gf_movimiento_producto movp ON movp.producto = pr.id_unico
                                    WHERE fch.id_unico = $f AND movp.detallemovimiento = $d
                                    GROUP BY prdes.fichainventario";
                        $resultProducto = $mysqli->query($sqlProducto);
                        $n = mysqli_fetch_row($resultProducto);
                        $x = $n[0];

                        $sqlFic = "select descripcion from gf_ficha where id_unico=$f";
                        $resultFin = $mysqli->query($sqlFic);
                        $nom = mysqli_fetch_row($resultFin);

                        if($x >= $existencias){
                            $c="";?>
                            <script type="text/javascript">
                                $("#btnGuardarProducto").prop('disabled',true);
                                $("#chkTodos").prop('disabled',true);
                                $("#lblA").hide();
                                $("#btnGuardarProducto").hide();
                                $("#chkTodos").hide();
                            </script>
                        <?php
                        }else {
                            $c = $n[0]+1;?>
                            <script type="text/javascript">
                                $("#btnGuardarProducto").prop('disabled',false);
                                $("#chkTodos").prop('disabled',false);
                                $("#btnGuardarProducto").show();
                                $("#chkTodos").show();
                            </script>
                        <?php
                        }

                        if($x==$existencias){ ?>
                            <script type="text/javascript">
                                var form_data = {
                                    ficha:<?php echo $f; ?>,
                                    posicion:1,
                                    detalle:<?php echo $d; ?>
                                };

                                $.ajax({
                                    type: 'POST',
                                    url: "consultasBasicas/paginacionFormularioProducto.php",
                                    data: form_data,
                                    success: function (data, textStatus, jqXHR) {
                                        $("#formProducto").html(data);
                                    }
                                });
                            </script>
                        <?php
                        }
                    }
                    ?>
                    <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;" id="nomProducto"><?php echo $nom[0]; ?></h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <div class="row">
                        <form name="form" id="formProducto" class="form-horizontal col-sm-10 col-lg-10" method="POST"  enctype="multipart/form-data" action="json/registrarProductoEspecificacionMov.php" >
                            <div style="margin-top:-20px;" class="client-form">
                                <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                    Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                                </p>
                                <div class="form-group form-inline" style="margin-top:5px;">
                                    <?php
                                    $vFicha ="";
                                    $valorE = "";
                                    if(!empty($_POST['posicion'])){
                                        $posicion = $_POST['posicion'];
                                    }else{
                                        $posicion = 1;
                                    }
                                    $cantidad = 0;
                                    if (!empty($_POST['ficha'])) {
                                        $vFicha = $_POST['ficha'];
                                        #capturamos la variables enviadas en el post
                                        $cantidad = $_POST['cantidad'];
                                        $ficha = $_POST['ficha'];
                                        $valorE = $_POST['valor'];
                                        $valor = $_POST['valor'];
                                        $movimiento = $_POST['movimiento'];
                                        #consulta para verificar la cantidad que hay de productos guardados y si existe que
                                        #reste la cantidad de productos a la cantidad recibida
                                        $sqlProducto = "SELECT DISTINCT COUNT(prdes.fichainventario) AS cantidad FROM gf_producto_especificacion prdes
                                                    LEFT JOIN gf_producto pr on pr.id_unico = prdes.producto
                                                    LEFT JOIN gf_ficha_inventario fin on prdes.fichainventario  = fin.id_unico
                                                    LEFT JOIN gf_ficha fch on fin.ficha = fch.id_unico
                                                    LEFT JOIN gf_movimiento_producto movp ON movp.producto = pr.id_unico
                                                    WHERE fch.id_unico = $ficha AND movp.detallemovimiento = $movimiento
                                                    GROUP BY prdes.fichainventario";
                                        $resultProducto = $mysqli->query($sqlProducto);
                                        $n = mysqli_fetch_row($resultProducto);
                                        $cantidad = $cantidad-$n[0];
                                        #imprimimos algunas variables como hidden
                                        echo '<input type="hidden" class="hidden" name="ficha" value="'.$ficha.'">';
                                        echo '<input type="hidden" class="hidden" name="cantidad" value="'.$cantidad.'">';
                                        echo '<input type="hidden" class="hidden" name="valor" value="'.$valor.'">';
                                        echo '<input type="hidden" class="hidden" name="movimiento" value="'.$movimiento.'">';
                                        #consulta de valores y conslta con la que creamos el formulario
                                        $sqlProducto = "select
                                                            fin.id_unico,
                                                            fin.elementoficha,
                                                            elm.id_unico,
                                                            elm.nombre,
                                                            elm.tipodato,
                                                            tpd.id_unico,
                                                            tpd.nombre,
                                                            fin.obligatorio,
                                                            fin.autogenerado
                                                    from gf_ficha_inventario fin
                                                    left join gf_elemento_ficha elm on elm.id_unico = fin.elementoficha
                                                    left join gf_tipo_dato tpd on elm.tipodato = tpd.id_unico
                                                    WHERE fin.ficha = $ficha ORDER BY elm.id_unico";
                                        $resultProducto = $mysqli->query($sqlProducto);
                                        while ($campo = $resultProducto->fetch_row()){
                                            echo '<div class="form-grpup">';
                                            switch ($campo[4]) {
                                                case 1:
                                                    if($campo[8] == 1){
                                                        $auto = "";
                                                        $sqlAuto="select max(prdes.valor) from gf_producto_especificacion prdes
                                                        left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                                                        left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                                                        where elm.nombre = $campo[3]";
                                                        $resultAuto=$mysqli->query($sqlAuto);
                                                        $filaAuto= mysqli_num_rows($resultAuto);
                                                        $valor= mysqli_fetch_row($resultAuto);
                                                        if($filaAuto==0){
                                                            $auto = "NA";
                                                        }else{
                                                            $auto = $valor[0];
                                                        }
                                                        ?>
                                                        <input type="hidden" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $auto; ?>"/>
                                                        <?php
                                                    }else{
                                                        if($campo[7] == 1){
                                                            echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event,'car')">
                                                           <?php
                                                        }else{
                                                            echo '<label class="control-label col-lg-4 col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event,'car')">
                                                            <?php
                                                        }
                                                    }
                                                    break;
                                                case 2:
                                                    if($campo[8]==1){
                                                        $auto = "";
                                                        $sqlAuto="select max(prdes.valor) from gf_producto_especificacion prdes
                                                        left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                                                        left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                                                        where elm.nombre = '$campo[3]'";
                                                        $resultAuto=$mysqli->query($sqlAuto);
                                                        $filaAuto= mysqli_num_rows($resultAuto);
                                                        $valor= mysqli_fetch_row($resultAuto);
                                                        if($filaAuto==0){
                                                            $auto = "NA";
                                                        }else{
                                                            $auto = $valor[0];
                                                        }
                                                        ?>
                                                        <input type="hidden" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $auto; ?>"/>
                                                        <?php
                                                    }else{
                                                        if($campo[7]==1){
                                                            echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event,'num_car')">
                                                           <?php
                                                        }else{
                                                            echo '<label class="control-label col-lg-4 col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event,'num_car')">
                                                            <?php
                                                        }
                                                    }
                                                    break;
                                                case 3:
                                                    if($campo[8]==1){
                                                        $auto = "";
                                                        $sqlAuto="select max(prdes.valor) from gf_producto_especificacion prdes
                                                        left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                                                        left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                                                        where elm.nombre = $campo[3]";
                                                        $resultAuto=$mysqli->query($sqlAuto);
                                                        $filaAuto= mysqli_num_rows($resultAuto);
                                                        $valor= mysqli_fetch_row($resultAuto);
                                                        if($filaAuto==0){
                                                            $auto = "NA";
                                                        }else{
                                                            $auto = $valor[0];
                                                        }
                                                        ?>
                                                        <input type="hidden" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $auto; ?>"/>
                                                        <?php
                                                    }else{
                                                        if($campo[7]==1){
                                                            echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event)">
                                                           <?php
                                                        }else{
                                                            echo '<label class="control-label col-lg-4 col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event)">
                                                            <?php
                                                        }
                                                    }
                                                    break;
                                                case 4:
                                                    if($campo[8] == 1){
                                                        $auto = "";
                                                        $sqlAuto="select max(prdes.valor) from gf_producto_especificacion prdes
                                                        left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                                                        left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                                                        where elm.nombre = '$campo[3]'";
                                                        $resultAuto=$mysqli->query($sqlAuto);
                                                        $filaAuto= mysqli_num_rows($resultAuto);
                                                        $valor= mysqli_fetch_row($resultAuto);
                                                        if(!empty($valor[0])){
                                                            $auto = "1";
                                                        }else{
                                                            $auto = $valor[0] + 1;
                                                        }
                                                        ?>
                                                        <input type="hidden" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $auto; ?>"/>
                                                        <?php
                                                    }else{
                                                        if($campo[7]==1){
                                                            echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event,'num')"/>
                                                           <?php
                                                        }else{
                                                            echo '<label class="control-label col-lg-4 col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event,'num')"/>
                                                            <?php
                                                        }
                                                    }
                                                    break;
                                                case 5:
                                                    if($campo[8]==1){
                                                        $auto = "";
                                                        $sqlAuto="select max(prdes.valor) from gf_producto_especificacion prdes
                                                        left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                                                        left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                                                        where elm.nombre = $campo[3]";
                                                        $resultAuto=$mysqli->query($sqlAuto);
                                                        $filaAuto= mysqli_num_rows($resultAuto);
                                                        $valor= mysqli_fetch_row($resultAuto);
                                                        if($filaAuto==0){
                                                            $auto = "2";
                                                        }else{
                                                            $auto = $valor[0];
                                                        }
                                                        ?>
                                                        <input type="hidden" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $auto; ?>"/>
                                                        <?php
                                                    }else{
                                                        if($campo[7]==1){
                                                            echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <div class="col-lg-2 col-sm-2">
                                                                <input type="radio" name="<?php echo str_replace(' ', '', $campo[3]);?>" value="1" id="<?php echo str_replace(' ', '', $campo[3]);?>+'1'" title="Indique si es obligatorio"/>SI
                                                                <input type="radio" name="<?php echo str_replace(' ', '', $campo[3]);?>" value="2" id="<?php echo str_replace(' ', '', $campo[3]);?>+'2'" title="Indique si no es obligatorio" checked/>NO
                                                            </div>
                                                            <?php
                                                        }else{
                                                            echo '<label class="control-label col-lg-4 col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <div class="col-lg-2 col-sm-2">
                                                                <input type="radio" name="<?php echo str_replace(' ', '', $campo[3]);?>" value="1" id="<?php echo str_replace(' ', '', $campo[3]);?>+'1'" title="Indique si es obligatorio"/>SI
                                                                <input type="radio" name="<?php echo str_replace(' ', '', $campo[3]);?>" value="2" id="<?php echo str_replace(' ', '', $campo[3]);?>+'2'" title="Indique si no es obligatorio" checked/>NO
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    break;
                                                case 6:
                                                    if($campo[8]==1){
                                                        $auto = "";
                                                        $sqlAuto="select max(prdes.valor) from gf_producto_especificacion prdes
                                                        left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                                                        left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                                                        where elm.nombre = $campo[3]";
                                                        $resultAuto=$mysqli->query($sqlAuto);
                                                        $filaAuto= mysqli_num_rows($resultAuto);
                                                        $valor= mysqli_fetch_row($resultAuto);
                                                        if($filaAuto==0){
                                                            $auto = date('d/m/Y');
                                                        }else{
                                                            $auto = $valor[0];
                                                        }
                                                        ?>
                                                        <input type="hidden" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $auto; ?>"/>
                                                        <?php
                                                    }else{
                                                        if($campo[7]==1){
                                                            echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <input  onmouseover="return cargarfecha('<?php echo str_replace(' ', '', $campo[3]);?>')" type="text" value="<?php echo date('d/m/Y'); ?>" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required >
                                                           <?php
                                                        }else{
                                                            echo '<label class="control-label col-lg-4 col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                                                            <input  onmouseover="return cargarfecha('<?php echo str_replace(' ', '', $campo[3]);?>')" type="text" value="<?php echo date('d/m/Y'); ?>" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>">
                                                            <?php
                                                        }
                                                    }
                                                    break;
                                            }
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                    <label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>Valor:</label>
                                    <input  onkeypress="return txtValida(event,'num')" type="text" name="txtValor" value="<?php echo $valorE;?>" id="txtValor" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese el valor" placeholder="Valor" required >
                                    <label class="control-label col-lg-4 col-sm-4">Descripción:</label>
                                    <textarea class="form-control col-lg-1 col-sm-1 area" style="width:300px;margin-top:-1px;height:40px" rows="2" onkeypress="return txtValida(event,'num_car');" maxlength="500" minlength="0" name="txtDescripcion" id="txtDescripcion" title="Descripción del producto"></textarea>
                                    <div class="col-sm-7">
                                        <label class="control-label col-lg-5 col-sm-5"></label>
                                        <a id="btnAplicar" style="margin-left:-13px;" title="Generar" onclick="return generarJson('formProducto')" class="btn btn-primary" name="btnAplicar">Guardar</a>
                                    </div>
                                    <script type="text/javascript">
                                        function siguiente(ficha,posicion){
                                            var form_data = {
                                                ficha:ficha,
                                                posicion:posicion
                                            };

                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasBasicas/paginacionFormularioProducto.php",
                                                data: form_data,
                                                success: function (data, textStatus, jqXHR) {
                                                    $(".modalI").html(data);
                                                }
                                            });
                                        }

                                        function generarJson(form){
                                            $("#modalFichaInventario").modal('hide');
                                            jsShowWindowLoad('Guardando..');
                                            var result = "";
                                            var form_data = $("#"+form).serialize();
                                            $.ajax({
                                                type: 'GET',
                                                url: "json/registrarProductoEspecificacionMov2.php",
                                                data: form_data,
                                                success: function (data, textStatus, jqXHR) {
                                                    result = JSON.parse(data);
                                                    if(result==true){
                                                        //Remover Div
                                                        jsRemoveWindowLoad();
                                                        $("#modalFichaInventario").modal('hide');
                                                        $('#modalGuardar').modal('show');
                                                    }else{
                                                        $("#modalFichaInventario").modal('hide');
                                                        $('#ModalNoGuardar').modal('show');
                                                    }
                                                }
                                            });
                                        }
                                    </script>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="forma-modal" class="modal-footer" style="margin-top:-10px"></div>
        </div>
    </div>
</div>
<script>
function jsRemoveWindowLoad() {
    // eliminamos el div que bloquea pantalla
    $("#WindowLoad").remove();
}

function jsShowWindowLoad(mensaje) {
    //eliminamos si existe un div ya bloqueando
    jsRemoveWindowLoad();
    //si no enviamos mensaje se pondra este por defecto
    if (mensaje === undefined) mensaje = "Procesando la información<br>Espere por favor";
    //centrar imagen gif
    height = 20;//El div del titulo, para que se vea mas arriba (H)
    var ancho = 0;
    var alto = 0;
    //obtenemos el ancho y alto de la ventana de nuestro navegador, compatible con todos los navegadores
    if (window.innerWidth == undefined) ancho = window.screen.width;
    else ancho = window.innerWidth;
    if (window.innerHeight == undefined) alto = window.screen.height;
    else alto = window.innerHeight;
    //operación necesaria para centrar el div que muestra el mensaje
    var heightdivsito = alto/2 - parseInt(height)/2;//Se utiliza en el margen superior, para centrar
   //imagen que aparece mientras nuestro div es mostrado y da apariencia de cargando
    imgCentro = "<div style='text-align:center;height:" + alto + "px;'><div  style='color:#FFFFFF;margin-top:" + heightdivsito + "px; font-size:20px;font-weight:bold;color:#1075C1'>" + mensaje + "</div><img src='img/loading.gif'/></div>";
        //creamos el div que bloquea grande------------------------------------------
        div = document.createElement("div");
        div.id = "WindowLoad";
        div.style.width = ancho + "px";
        div.style.height = alto + "px";
        $("body").append(div);
        //creamos un input text para que el foco se plasme en este y el usuario no pueda escribir en nada de atras
        input = document.createElement("input");
        input.id = "focusInput";
        input.type = "text";
        //asignamos el div que bloquea
        $("#WindowLoad").append(input);
        //asignamos el foco y ocultamos el input text
        $("#focusInput").focus();
        $("#focusInput").hide();
        //centramos el div del texto
        $("#WindowLoad").html(imgCentro);

}
</script>

<style>
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