
<?php
session_start();
require_once '../Conexion/conexion.php';
$productos[]="";
$detalle =  $_POST['detalle'];
$ficha = $_POST['ficha'];
$posicion = $_POST['posicion'];
$sqlFic = " SELECT    pln.nombre, pln.ficha
            FROM      gf_detalle_movimiento as dtm
            LEFT JOIN gf_plan_inventario as pln ON dtm.planmovimiento = pln.id_unico
            WHERE     dtm.id_unico = $detalle";
$resultFin = $mysqli->query($sqlFic);
$nom = mysqli_fetch_row($resultFin);
?>
<div class="text-left" style="margin-top:-15px">
    <div class="col-lg-9 col-sm-9">
        <ol class="breadcrumb" id="migas_de_pan">
            <li><?php echo (d($nom[1],$posicion)).'/'.$nom[0].PHP_EOL.$posicion; ?></li>
        </ol>
        <?php
        function d ($nom,$posicion){
            require ('../Conexion/conexion.php');
            $read = "";
            $sqlD = "select gf_plan_inventario_asociado.plan_padre,gf_plan_inventario.nombre from gf_plan_inventario_asociado left join gf_plan_inventario on gf_plan_inventario.id_unico= gf_plan_inventario_asociado.plan_padre where gf_plan_inventario_asociado.plan_hijo=$nom";
            $resultFD=$mysqli->query($sqlD);
            while($t=mysqli_fetch_row($resultFD)){
                $read.='/'.$t[1].' '.$posicion.'/';
                echo d($t[0], $posicion);
            }
            $r = substr($read,0, strlen($read)-1);
            return $r;
        }
        ?>
        <input type="hidden" name="txtRuta" style="display: none" id="txtRuta" class="hidden" value="<?php echo(d($nom[1],$posicion)); ?>"/>
    </div>
</div>
<?php
$sqlproficha = "SELECT    prdes.producto
                FROM      gf_producto_especificacion prdes
                LEFT JOIN gf_ficha_inventario     fin ON fin.id_unico  = prdes.fichainventario
                left join gf_elemento_ficha       elm ON elm.id_unico  = fin.elementoficha
                LEFT JOIN gf_movimiento_producto movp ON movp.producto = prdes.producto
                WHERE     fin.ficha              = $ficha
                AND       movp.detallemovimiento = $detalle
                GROUP BY  prdes.producto
                ORDER BY  prdes.producto,elm.id_unico ASC";
$resultproficha =$mysqli->query($sqlproficha);
$pos = mysqli_num_rows($resultproficha);
if($pos>0){
while($fila = $resultproficha->fetch_row()){
    unset($productos[0]);
    $productos[]=$fila[0];
}
$cantidad = count($productos);

$_SESSION['posicion']=$posicion+1;
$producto = $productos[$posicion];
$anterior = $posicion-1;
$sqlProductos = "select
                        fin.id_unico,
                        fin.elementoficha,
                        elm.id_unico,
                        elm.nombre,
                        elm.tipodato,
                        tpd.id_unico,
                        tpd.nombre,
                        fin.obligatorio,
                        fin.autogenerado,
                        CASE WHEN prdes.valor IS NULL THEN '' 
                             WHEN prdes.valor = 'NULL' THEN '' 
                        ELSE prdes.valor END,
                        prdes.id_unico
                from gf_ficha_inventario fin
                left join gf_elemento_ficha elm on elm.id_unico = fin.elementoficha
                left join gf_tipo_dato tpd on elm.tipodato = tpd.id_unico
                left join gf_producto_especificacion prdes on prdes.fichainventario = fin.id_unico
                WHERE prdes.producto = $producto ORDER BY elm.id_unico";
$resultProducto = $mysqli->query($sqlProductos);
echo '<form name="formProducto" id="formProducto" class="form-horizontal col-lg-10 col-sm-10 producto" method="POST"  enctype="multipart/form-data" action="json/modificarProductoEspecificacionMov.php" >';
echo '<div style="margin-top:-20px;" class="client-form">';
echo '<div class="form-grpup">';
echo '<div class="alert alert-info text-center" id="alerta" style="display:none;margin-top:2px;width: 300px;">
  <h5><strong>Informacion!</strong></h5><p>Posición ingresada invalida.</p>
</div>';
echo '<div class="form-group" style="margin-top:5px;">';
//echo '<label class="control-label col-lg-4 col-sm-4">Buscar:</label>';
//echo '<input type="text" name="txtConsulta" id="txtConsulta" style="width: 280px;" title="Campo de consulta" class="form-control input-sm col-lg-1 col-sm-1" "/>';
//echo '<a id="btnBuscar" class="btn col-lg-1 col-sm-1" title="Buscar" style="margin-top:-2px;padding:3px 3px 3px 3px;width: 25px;"><li class="glyphicon glyphicon-search"></li></a>';
echo '</div>';
echo    '<p align="center" class="parrafoO" style="margin-bottom:-0.00005em;margin-top:-10px">
            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
        </p>';
echo '<input value="'.$producto.'" name="producto" type="hidden" class="hidden"/>';
echo '<div class="form-group form-inline" style="margin-top:5px;">';
while($campo =$resultProducto->fetch_row()){
    echo '<div class="form-grpup">';
        switch ($campo[4]) {
            case 1:
                if($campo[7]==1){
                    echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input style="width: 280px;" type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event,'car')">
                   <?php
                }else{
                    echo '<label class="control-label col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input style="width: 280px;" type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event,'car')">
                    <?php
                }
                break;
            case 2:
                if($campo[7]==1){
                    echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input style="width: 280px;" type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event,'num_car')">
                   <?php
                }else{
                    echo '<label class="control-label col-lg-4 col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input style="width: 280px;" type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event,'num_car')">
                    <?php
                }
                break;
            case 3:
                if($campo[7]==1){
                    echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input style="width: 280px;" type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event)">
                   <?php
                }else{
                    echo '<label class="control-label col-lg-4 col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input style="width: 280px;" type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event)">
                    <?php
                }
                break;
            case 4:
                if($campo[7]==1){
                    echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input style="width: 280px;" readonly="true" type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event,'num')"/>
                   <?php
                }else{
                    echo '<label class="control-label col-lg-4 col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input readonly="true" style="width: 280px;" type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event,'num')"/>
                    <?php
                }
                break;
            case 5:
                if($campo[7]==1){
                    echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>';
                    ?>
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
                break;
            case 6:
                if($campo[7]==1){
                    $fechaS = explode("-", $campo[9]);$fecha= $fechaS[2] . '/' . $fechaS[1] . '/' . $fechaS[0];
                    echo '<label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input style="width: 280px;" onmouseover="return cargarfecha('<?php echo str_replace(' ', '', $campo[3]);?>')" type="text" value="<?php echo $fecha; ?>" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required >
                   <?php
                }else{
                    echo '<label class="control-label col-lg-4 col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input style="width: 280px;" onmouseover="return cargarfecha('<?php echo str_replace(' ', '', $campo[3]);?>')" type="text" value="<?php echo $fecha; ?>" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-lg-1 col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>">
                    <?php
                }
                break;
        }
        echo '</div>';
}

$sqlP="select descripcion,valor from gf_producto where id_unico=$producto";
$resultP = $mysqli->query($sqlP);
while($row=$resultP->fetch_row()){ ?>
    <label class="control-label col-lg-4 col-sm-4"><strong class="obligado">*</strong>Valor:</label>
    <input style="width: 280px;" onkeypress="return txtValida(event,'num')" type="text" name="txtValor" value="<?php echo $row[1];?>" id="txtValor" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese el valor" placeholder="Valor" required >
    <label class="control-label col-lg-4 col-sm-4">Descripción:</label>
    <textarea class="form-control col-lg-1 col-sm-1 area" style="margin-top:-1px;height:40px;width: 280px;" rows="2" onkeypress="return txtValida(event,'num_car');" maxlength="500" minlength="0" name="txtDescripcion" id="txtDescripcion" title="Descripción del producto"><?php echo $row[0]; ?></textarea>
<?php
if($anterior==0){
    #Validar si es 0 inhabilitar botón ?>
    <script type="text/javascript">
        $("#btnAnterior").prop('disabled',true);
    </script>
    <?php
}
}?>

<div class="col-lg-6 col-sm-6 col-lg-offset-4 col-sm-offset-4 form-inline">
    <button type="button" id="btnModificarProducto" onclick="return modificarProducto('formProducto');" title="Modificar" value="Guardar" onclick="" class="btn btn-primary" name="btnAnterior"><li class="glyphicon glyphicon-floppy-disk"></li></button>
    <button type="button" id="btnAnterior" value="Anterior" onclick="return siguiente(<?php echo $ficha; ?>,<?php echo $anterior; ?>)" class="btn btn-primary" name="btnAnterior"><li class="glyphicon glyphicon-chevron-left"></li></button>
    <input value="<?php echo $posicion ?>" class="form-control text-center" onkeypress="return envio(event,<?php echo $ficha; ?>,<?php echo $cantidad; ?>)" id="txtPosicion" style="width:40px"/><?php echo PHP_EOL.PHP_EOL.'<label class="control-label">'.PHP_EOL.'de'.PHP_EOL.count($productos).'</label>'; ?>
    <button type="button" id="btnSiguiente" value="Siguiente" onclick="return siguiente(<?php echo $ficha; ?>,<?php echo $_SESSION['posicion']; ?>)" class="btn btn-primary" name="btnSiguiente"><li class="glyphicon glyphicon-chevron-right"></li></button>
    <button type="button" id="" onclick="return imagenes(<?php echo $producto; ?>);" title="Imágenes" onclick="" class="btn btn-primary" name=""><li class="glyphicon glyphicon-picture"></li></button>
</div>

<?php
if($posicion==$cantidad){ ?>
    <script type="text/javascript">
        $("#btnSiguiente").prop('disabled',true);
    </script>
    <?php
}
?>
<script type="text/javascript">
    function envio(e,ficha,cantidad){
        if(e.keyCode === 13){
            var posicion = $("#txtPosicion").val();
            if(cantidad<posicion){
                $("#alerta").show();
                setTimeout(function() {
                    $("#alerta").fadeOut("fast");
                },1000);
            }else{
                siguiente(ficha,posicion);
            }
        }
    }

    function siguiente(ficha,posicion){ 
        var form_data = {
            ficha:ficha,
            posicion:posicion,
            detalle:<?php echo $detalle; ?>
        };

        $.ajax({
            type: 'POST',
            url: "consultasBasicas/paginacionFormularioProducto.php",
            data: form_data,
            success: function (data, textStatus, jqXHR) {
                $("#formProducto").html(data);
            }
        });
    }

    function modificarProducto(form){
        var result = "";
        var form_data = $(".producto").serialize();
        $.ajax({
            type: 'POST',
            url: "json/modificarProductoEspecificacionMov.php",
            data: form_data,
            success: function (data, textStatus, jqXHR) {
                console.log('Modificar '+data);
                $("#mensaje").html('Guardado');
                //$('#modalFichaInventario').modal('hide');
                //$('#modalGuardar').modal('show');
            }
        });
    }

    $("#btnBuscar").click(function(){
        var valor = $("#txtConsulta").val();
        if(valor.length!==0){
            var ficha = <?php echo $ficha?>;
            var form_data = {
                valor:valor,
                ficha:ficha
            };
            var posicion = "";
            $.ajax({
                type: 'POST',
                url: "consultasBasicas/consultaProductoNValor.php",
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    posicion = JSON.parse(data);
                    siguiente(ficha,posicion);
                }
            });
        }
    });
</script>
<?php
echo '</div>';
echo '</div>';
echo '</div>';
echo '</form>';
?>
<div class="col-sm-1 col-lg-1" style="border: 4px solid #020324; border-radius: 10px;width:250px;height:150px;box-shadow:inset 2px 2px 2px 2px #777;margin-left:480px;position:absolute;margin-top:40px">
    <?php require_once 'arbolProductos.php'; ?>
</div>
<?php }else{ ?>
    <div class="modal-body" style="margin-top: 8px">
        <div class="row">
            <form name="form" id="formProducto" class="form-horizontal col-lg-10 col-sm-10 formProducto" method="POST"  enctype="multipart/form-data" action="json/registrarProductoEspecificacionMov.php" >
                <div style="margin-top:-20px;" class="client-form">
                    <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                        Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                    </p>
                    <div class="form-group form-inline" style="margin-top:5px;">
                        <?php
                        $vFicha ="";
                        $posicion = 1;
                        $cantidad = 0;
                        if (!empty($_POST['ficha'])) {
                            $vFicha = $_POST['ficha'];
                            $sqlF2 = "select dtm.cantidad,dtm.valor,dtm.id_unico from gf_plan_inventario pln left join gf_detalle_movimiento dtm on dtm.planmovimiento=pln.id_unico where pln.ficha = $ficha";
                            $resultG = $mysqli->query($sqlF2);
                            $rt = mysqli_fetch_row($resultG);
                            #capturamos la variables enviadas en el post
                            $cantidad = $rt[0];
                            $ficha = $_POST['ficha'];
                            $valorE = $rt[1];
                            $valor = $rt[1];
                            $movimiento = $rt[2];
                            #consulta para verificar la cantidad que hay de productos guardados y si existe que
                            #reste la cantidad de productos a la cantidad recibida
                            $sqlProducto = "SELECT DISTINCT COUNT(prdes.fichainventario) AS cantidad FROM gf_producto_especificacion prdes
                                        LEFT JOIN gf_producto pr on pr.id_unico = prdes.producto
                                        LEFT JOIN gf_ficha_inventario fin on prdes.fichainventario  = fin.id_unico
                                        LEFT JOIN gf_ficha fch on fin.ficha = fch.id_unico
                                        WHERE fch.id_unico = $ficha
                                        GROUP BY prdes.fichainventario";
                            $resultProducto = $mysqli->query($sqlProducto);
                            $n = mysqli_fetch_row($resultProducto);
                            $cantidad = $cantidad-$n[0];
                            #imprimimos algunas variables como hidden
                            echo '<input type="hidden" class="hidden" name="ficha" value="'.$ficha.'">';
                            echo '<input type="hidden" class="hidden" name="cantidad" value="'.$rt[0].'">';
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
                        <div class="col-lg-7 col-sm-7">
                            <label class="control-label col-lg-5 col-sm-5"></label>
                            <a id="btnAplicar" title="Generar" onclick="return generarJson('formProducto')" class="btn btn-primary" name="btnAplicar">Guardar</a>
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
                                var form_data = $("."+form).serialize();
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
            <div class="col-lg-1 col-sm-1" style="border: 4px solid #020324; border-radius: 10px;width:300px;height:190px;margin-top:-215px; box-shadow:inset 2px 2px 2px 2px #777;margin-left:480px">
                <?php require_once 'arbolProductos.php'; ?>
            </div>
        </div>
    </div>
    <?php
}
?>
