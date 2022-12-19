<?php require_once './../Conexion/conexion.php'; ?>
<?php
$productos[]="";
$ficha = $_POST['ficha'];
$posicion = $_POST['posicion'];
$detalle = $_POST['detalle'];
$asociado = $_POST['asociado'];
$sqlproM = "SELECT prdes.producto FROM gf_producto_especificacion prdes  
LEFT JOIN gf_ficha_inventario fin     ON fin.id_unico   = prdes.fichainventario 
LEFT JOIN gf_elemento_ficha elm       ON elm.id_unico   = fin.elementoficha 
LEFT JOIN gf_movimiento_producto movp ON movp.producto  = prdes.producto
WHERE     fin.ficha = $ficha         AND movp.detallemovimiento = $detalle
GROUP BY  prdes.producto
ORDER BY  prdes.producto,elm.id_unico ASC";
$resultProD = $mysqli->query($sqlproM);
$guardado = mysqli_num_rows($resultProD);
$guardado = $guardado+1;
if($guardado==$posicion){    
    ?>
    <script type="text/javascript">
        $("#btnAnterior").prop('disabled',true);
    </script>
    <?php
}
$sqlproficha = "SELECT prdes.producto FROM gf_producto_especificacion prdes 
LEFT JOIN gf_ficha_inventario fin     ON fin.id_unico   = prdes.fichainventario 
LEFT JOIN gf_elemento_ficha elm       ON elm.id_unico   = fin.elementoficha
LEFT JOIN gf_movimiento_producto movp ON movp.producto  = prdes.producto
WHERE     fin.ficha = $ficha AND movp.detallemovimiento = $asociado
GROUP BY  prdes.producto
ORDER BY  prdes.producto,elm.id_unico ASC";
$resultproficha =$mysqli->query($sqlproficha);
$pos = mysqli_num_rows($resultproficha);
while($fila = $resultproficha->fetch_row()){
    unset($productos[0]);
    $productos[]=$fila[0];
}
$cantidad = count($productos); $_SESSION['posicion']=$posicion+1; $producto = $productos[$posicion]; $anterior = $posicion-1;
$sqlProductos = "SELECT   fin.id_unico, fin.elementoficha, elm.id_unico, elm.nombre, elm.tipodato, 
                          tpd.id_unico, tpd.nombre, fin.obligatorio, fin.autogenerado, prdes.valor, prdes.id_unico
                FROM      gf_ficha_inventario fin 
                LEFT JOIN gf_elemento_ficha elm             ON elm.id_unico = fin.elementoficha 
                LEFT JOIN gf_tipo_dato tpd                  ON elm.tipodato = tpd.id_unico
                LEFT JOIN gf_producto_especificacion prdes  ON prdes.fichainventario = fin.id_unico
                WHERE     prdes.producto = $producto ORDER BY elm.id_unico";
$resultProducto = $mysqli->query($sqlProductos);
echo '<form name="formSalida" id="formSalida" class="form-horizontal producto" method="POST"  enctype="multipart/form-data" action="json/modificarProductoEspecificacionMov.php" >';
echo '<input type="hidden" name="detalle" id="detalle" value="'.$detalle.'"/>';
echo '<div style="margin-top:-20px;" class="client-form">';
echo '<div class="form-grpup">';
echo '<div class="alert alert-info" id="alerta" style="display:none;margin-top:2px;width: 550px;"><h5><strong>Informacion!</strong></h5><p>Posición ingresada invalida.</p></div>';
echo '<p align="center" class="parrafoO" style="margin-bottom:-0.00005em;margin-top:-10px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>';
echo '<input value="'.$producto.'" name="producto" type="hidden" class="hidden"/>';
echo '<div class="form-group form-inline" style="margin-top:5px;">';
while($campo =$resultProducto->fetch_row()){  
    echo '<div class="form-grpup">';
        switch ($campo[4]) {
            case 1:                     
                if($campo[7]==1){                                            
                    echo '<label class="control-label col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event,'car')">
                   <?php
                }else{
                    echo '<label class="control-label col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event,'car')">
                    <?php 
                }
                break;
            case 2:
                if($campo[7]==1){                                            
                    echo '<label class="control-label col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event,'num_car')">
                   <?php
                }else{
                    echo '<label class="control-label col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event,'num_car')">
                    <?php 
                }
                break;
            case 3:                
                if($campo[7]==1){                                            
                    echo '<label class="control-label col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event)">
                   <?php
                }else{
                    echo '<label class="control-label col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event)">
                    <?php 
                }                                                                  
                break;
            case 4:                
                if($campo[7]==1){                                            
                    echo '<label class="control-label col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required onkeypress="return txtValida(event,'num')"/>
                   <?php
                }else{
                    echo '<label class="control-label col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input type="text"  name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" value="<?php echo $campo[9]; ?>" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" onkeypress="return txtValida(event,'num')"/>
                    <?php 
                }                                                                  
                break;
            case 5:
                if($campo[7]==1){
                    echo '<label class="control-label col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; 
                    ?>
                    <div class="col-sm-2">
                        <input type="radio" name="<?php echo str_replace(' ', '', $campo[3]);?>" value="1" id="<?php echo str_replace(' ', '', $campo[3]);?>+'1'" title="Indique si es obligatorio"/>SI
                        <input type="radio" name="<?php echo str_replace(' ', '', $campo[3]);?>" value="2" id="<?php echo str_replace(' ', '', $campo[3]);?>+'2'" title="Indique si no es obligatorio" checked/>NO
                    </div>
                    <?php
                }else{
                    echo '<label class="control-label col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <div class="col-sm-2">
                        <input type="radio" name="<?php echo str_replace(' ', '', $campo[3]);?>" value="1" id="<?php echo str_replace(' ', '', $campo[3]);?>+'1'" title="Indique si es obligatorio"/>SI
                        <input type="radio" name="<?php echo str_replace(' ', '', $campo[3]);?>" value="2" id="<?php echo str_replace(' ', '', $campo[3]);?>+'2'" title="Indique si no es obligatorio" checked/>NO
                    </div>
                    <?php
                }                                                  
                break;
            case 6:                
                if($campo[7]==1){   
                    $fechaS = explode("-", $campo[9]);$fecha= $fechaS[2] . '/' . $fechaS[1] . '/' . $fechaS[0];
                    echo '<label class="control-label col-sm-4"><strong class="obligado">*</strong>'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input  onmouseover="return cargarfecha('<?php echo str_replace(' ', '', $campo[3]);?>')" type="text" value="<?php echo $fecha; ?>" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>" required >
                   <?php
                }else{
                    echo '<label class="control-label col-sm-4">'.ucwords(strtolower($campo[3])).':</label>'; ?>
                    <input  onmouseover="return cargarfecha('<?php echo str_replace(' ', '', $campo[3]);?>')" type="text" value="<?php echo $fecha; ?>" name="<?php echo str_replace(' ', '', $campo[3]);?>" id="<?php echo str_replace(' ', '', $campo[3]);?>" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese <?php echo strtolower($campo[3])?>" placeholder="<?php echo ucwords(strtolower($campo[3]))?>">
                    <?php 
                }
                break;
        }
        echo '</div>';
}

$sqlP="select descripcion,valor from gf_producto where id_unico=$producto";
$resultP = $mysqli->query($sqlP);
while($row=$resultP->fetch_row()){ ?>
    <label class="control-label col-sm-4"><strong class="obligado">*</strong>Valor:</label>
    <input  onkeypress="return txtValida(event,'num')" type="text" name="txtValor" value="<?php echo $row[1];?>" id="txtValor" class="form-control col-sm-1 input-sm" maxlength="150" title="Ingrese el valor" placeholder="Valor" required >
    <label class="control-label col-sm-4">Descripción:</label>                                    
    <textarea class="form-control col-sm-1 area" style="width:300px;margin-top:-1px;height:40px" rows="2" onkeypress="return txtValida(event,'num_car');" maxlength="500" minlength="0" name="txtDescripcion" id="txtDescripcion" title="Descripción del producto"><?php echo $row[0]; ?></textarea>                                    
<?php 
if($anterior==0){
    #Validar si es 0 inhabilitar botón ?>
    <script type="text/javascript">
        $("#btnAnterior").prop('disabled',true);
    </script>
    <?php
}
}?>          
    <label class="control-label col-sm-4">Seleccione el producto para salida:</label>
    <div class="col-sm-1">
        <input type="checkbox" name="chkSalida" class="checkbox" id="chkSalida" value="1" />               
    </div>    
<div class="col-sm-4 col-sm-offset-4 form-inline">      
    <button type="button" id="btnModificarProducto" onclick="return modificarProducto($('#chkSalida').val(),<?php echo $detalle; ?>,<?php echo $producto; ?>)" title="Guardar Salida" value="Guardar" onclick="" class="btn btn-primary" name="btnGuardar"><li class="glyphicon glyphicon-floppy-disk"></li></button>
    <button type="button" id="btnAnterior" value="Anterior" onclick="return siguiente(<?php echo $ficha; ?>,<?php echo $anterior; ?>,<?php echo $detalle; ?>,<?php echo $asociado; ?>)" class="btn btn-primary" name="btnAnterior"><li class="glyphicon glyphicon-chevron-left"></li></button>
    <input value="<?php echo $posicion ?>" class="form-control text-center" onkeypress="return envio(event,<?php echo $ficha; ?>,<?php echo $cantidad; ?>,,<?php echo $detalle; ?>,<?php echo $asociado; ?>)" id="txtPosicion" style="width:40px"/><?php echo PHP_EOL.PHP_EOL.'<label class="control-label">'.PHP_EOL.'de'.PHP_EOL.count($productos).'</label>'; ?>
    <button type="button" id="btnSiguiente" value="Siguiente" onclick="return siguiente(<?php echo $ficha; ?>,<?php echo $_SESSION['posicion']; ?>,<?php echo $detalle; ?>,<?php echo $asociado; ?>)" class="btn btn-primary" name="btnSiguiente"><li class="glyphicon glyphicon-chevron-right"></li></button>                    
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
    function envio(e,ficha,cantidad,detalle,asociado){        
        if(e.keyCode === 13){        
            var posicion = $("#txtPosicion").val();
            if(cantidad<posicion){
                $("#alerta").show();
                setTimeout(function() {
                    $("#alerta").fadeOut("fast");
                },1000);
            }else{
                siguiente(ficha,posicion,detalle,asociado);
            }            
        }
    }                   

    function siguiente(ficha,posicion,detalle,asociado){
        var form_data = {
            ficha:ficha,
            posicion:posicion,
            detalle:detalle,
            asociado:asociado
        };

        $.ajax({
            type: 'POST',
            url: "consultasBasicas/paginacionSalida.php",
            data: form_data,
            success: function (data, textStatus, jqXHR) {
                $("#formSalida").html(data);
                
            }
        });
    }

    function modificarProducto(cantidad,detalle,producto){
        var result = "";
        var form_data = {
            cantidad:cantidad,
            detalle:detalle,
            producto:producto
        };        
        $.ajax({
            type: 'POST',
            url: "json/modificarCantidadDetalleSalida.php",
            data: form_data,
            success: function (data, textStatus, jqXHR) {                
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
    
    $("#btnCerrarModalMov").click(function(){
        document.location.reload();
    });
</script>
<?php 
echo '</div>';
echo '</div>';
echo '</div>';
echo '</form>';
?>