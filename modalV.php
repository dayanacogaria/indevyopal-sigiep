<?php require_once './Conexion/conexion.php'; ?>
<div class="modal fade plan" id="modalRegistrarD" role="dialog" align="center" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">            
                <div id="forma-modal" class="modal-header">
                    <button type="button" class="btn btn-xs close" aria-label="Close" onclick="reload()" style="color: #fff;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registro Detalle</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <div class="row">
                        <form name="form" id="hijos" class="form-horizontal form-inline" method="POST"  enctype="multipart/form-data" >
                            <div style="margin-top:-20px;" class="client-form">                        
                                <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                    Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                                </p>         
                                <?php
                                /**
                                 * Validamos que la variable valor no este vacia, y armamos el bosquejo de una alerta oculta
                                 */
                                $val = 0;
                                if(!empty($_POST['valor'])) {
                                    $val = $_POST['valor'];
                                    echo '<div class="alert alert-info" id="alerta" style="display:none;margin-top:2px;width: 550px;">
                                            <h5><strong>Información!</strong></h5><p>El valor total acumulado es mayor que'.PHP_EOL.number_format($val,2,'.',',').'.</p>
                                        </div>';
                                }
                                ?>
                                <div class="form-group text-center" style="margin-top:5px;">
                                    <?php
                                    /**
                                     * Inicializamos variables para capturar los valores
                                     */
                                    $elemento = 0;
                                    $val = 0;
                                    $mov = 0;
                                    $cantd = 0;
                                    $porcI = 0;
                                    $detalle_p = 0;
                                    if(!empty($_POST['padre'])) {   //Validamos que la variable padre no este vacia y capturamos los valores enviados por post
                                        $elemento = $_POST['padre'];
                                        $val = $_POST['valor'];
                                        $mov = $_POST['mov'];
                                        $cantd = $_POST['cant'];
                                        $porcI = $_POST['iva'];
                                        $detalle_p = $_POST['detalle'];
                                    }
                                    echo '<input type="hidden" name="txtElemento" value="'.$elemento.'" />';
                                    /**
                                     * Armamos el bosquejo de los campos a mostrar usando varios array para obtener padre e hijos
                                     */
                                    $master[][]="";
                                    $childrens[][]="";
                                    $sqlPlanPadre = "select plan_padre,planA.plan_hijo from gf_plan_inventario_asociado planA where planA.plan_padre=$elemento";
                                    $resultPadre = $mysqli->query($sqlPlanPadre);
                                    while ($fila= mysqli_fetch_row($resultPadre)){
                                        $master[]=array($fila[0]=>$fila[1]);
                                        unset($master[0]);
                                        $sqlPlanB = "select plan_padre,planA.plan_hijo from gf_plan_inventario_asociado planA where planA.plan_padre='$fila[1]'";
                                        $resultB = $mysqli->query($sqlPlanB);
                                        while ($filaB = mysqli_fetch_row($resultB)) {
                                            $childrens[]=array($filaB[0]=>$filaB[1]);
                                            unset($childrens[0]);
                                        }
                                    }               
                                    $z=0;
                                    try {
                                        foreach ($master as $master) {
                                            foreach ($master as $key => $value) {
                                                $z++;
                                                $sqlPlan="select id_unico,codi,nombre,ficha from gf_plan_inventario where id_unico=$value";
                                                $resultPlan = $mysqli->query($sqlPlan);
                                                $planIn = mysqli_fetch_row($resultPlan);
                                                echo '<label class="control-label col-lg-4 col-sm-4 text-right" title="'.$planIn[2].'" style="cursor:pointer">'.$planIn[1].PHP_EOL.'-'.PHP_EOL.$planIn[2].'</label>';
                                                echo '<input type="hidden" class="hidden col-sm-1 " value="'.$planIn[0].'" name="txtPlan'.$z.'" id="txtPlan'.$z.'" />';
                                                echo '<input type="number" name="txtCantidad'.$z.'" value="'.$cantd.'" onkeyup="return calcularD('.$z.')" class="form-control col-lg-1 col-sm-1 text-right" id="txtCantidad'.$z.'" title="Cantidad" maxlength="50" placeholder="Cantidad" style="padding:2px;width:20%;margin-right: 10px;cursor:pointer" required readonly>';
                                                echo '<input type="number" name="txtValor'.$z.'" onblur="return calcularD('.$z.');" onkeyup="return acumular('.$z.')" class="form-control col-lg-1 col-sm-1 text-right" id="txtValor'.$z.'" title="Valor aproximado" maxlength="50" style="padding:2px;width:20%;margin-right: 10px;cursor:pointer" placeholder="Valor" required>';
                                                echo '<input type="number" name="txtValorIva'.$z.'" class="form-control col-lg-1 disabled col-sm-1 text-right"  placeholder="Iva" id="txtValorIva'.$z.'" title="Iva" maxlength="50" style="padding:2px;width:20%;margin-right:10px;cursor:pointer" readonly="" required>';?>
                                                <?php
                                                echo '<br/>';
                                            }
                                        }
                                    }catch (mysqli_sql_exception $e) {
                                        echo "error :".$e;
                                    }

                                    $y = $z;
                                    foreach ($childrens as $childrens){
                                        foreach ($childrens as $key => $value) {
                                            if(!empty($value)){
                                                $y++;
                                                $sqlPlan="select id_unico,codi,nombre from gf_plan_inventario where id_unico=$value";
                                                $resultPlan = $mysqli->query($sqlPlan);
                                                $planIn = mysqli_fetch_row($resultPlan);                                                
                                                echo '<label class="control-label text-left col-lg-4 col-sm-4 text-right" title="'.$planIn[2].'" style="cursor:pointer">'.$planIn[1].PHP_EOL.'-'.PHP_EOL.$planIn[2].'</label>';
                                                echo '<input type="hidden" class="hidden" value="'.$planIn[0].'" name="txtPlan'.$y.'" id="txtPlan'.$y.'" />';
                                                echo '<input type="number" name="txtCantidad'.$y.'" value="'.$cantd.'" onkeyup="return calcularD('.$y.')" class="form-control col-lg-2 col-sm-2 text-right" id="txtCantidad'.$y.'" title="Cantidad" maxlength="50" placeholder="Cantidad" style="padding:2px;width:20%;margin-right:10px;cursor:pointer" required readonly>';
                                                echo '<input type="number" name="txtValor'.$y.'" onblur="return calcularD('.$y.');" onkeyup="return acumular('.$y.')" class="form-control col-lg-2 col-sm-1 text-right" id="txtValor'.$y.'" title="Valor aproximado" maxlength="50" style="padding:2px;width:20%;margin-right:10px;cursor:pointer" placeholder="Valor" required>';
                                                echo '<input type="number" name="txtValorIva'.$y.'" class="form-control disabled col-lg-1 col-sm-1 text-right"  placeholder="Iva" id="txtValorIva'.$y.'" title="Iva" maxlength="50" style="padding:2px;width:20%;margin-right:10px;cursor:pointer" readonly="" required>'; ?>
                                                <?php
                                                echo '<br/>';                            
                                            }                                                    
                                        }
                                    }                                    
                                    echo '<label class="control-label text-left col-sm-4">Valor total acumulado:</label>';
                                    echo "<input type=\"number\" value=\"0\" id=\"txtVTotal\" name=\"txtVTotal\" class=\"form-control col-lg-1 col-sm-1 text-right\" title=\"El valor total acumulado no puede ser mayor a".PHP_EOL.number_format($val,2,',','.')."\" style=\"width: 20%; cursor: pointer; margin-bottom: -15px;\" readonly/>";
                                    echo '</div>';
                                    echo '<input type="hidden" class="hidden" name="txtCantidadV" id="txtCantidadV" value="'.$y.'" />';
                                    echo '<input type="hidden" class="hidden" name="txtMovimiento" id="txtMovimiento" value="'.$mov.'" />';
                                    echo '<input type="hidden" class="hidden" name="txtDetalleP" id="txtDetalleP" value="'.$detalle_p.'" />';
                                    echo '<input type="hidden" class="hidden" name="txtPorceIva" id="txtPorceIva" value="'.$porcI.'" />';
                                    echo '<div style="display:none">';
                                    ?>
                                </div>
                            </div>
                        </form>                           
                    </div>
                </div>
                <div id="forma-modal" class="modal-footer">
                <button type="submit" id="btnDetalleHijos" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                <script type="text/javascript">
                    /**
                     * Función de calculo automatico
                     * @param x
                     */
                    function calcularD(x){
                        var valor = 0.00;
                        var iva = 0.00;
                        var totalP = 0.00;
                        var totalIva = 0.00;
                        var total = 0.00;

                        var cantidad = $("#txtCantidad"+x).val();
                        if(cantidad ===0 || cantidad ===""){
                            cantidad = 1;
                        }else{
                            cantidad = parseFloat($("#txtCantidad"+x).val());
                        }
                        valor = ($("#txtValor"+x).val());
                        iva = parseFloat(<?php echo $porcI; ?>);
                        total = cantidad*valor;
                        totalIva = (total*iva)/100;
                        $("#txtValorIva"+x).val(totalIva.toFixed(2));
                        $("#txtValorTotal"+x).val(total.toFixed(2));
                    }

                    function acumular(x){
                        var totales = 0;
                        for (var i = 1; i <= x; i++) {
                            var nombre = "txtValor"+i;
                            var campo = $("#"+nombre).val();
                            if(campo.length == 0) {
                                campo = 0;
                            }
                            totales = parseFloat(campo) + parseFloat(totales);
                        }
                        $("#txtVTotal").val(totales);
                        if(totales >= <?php echo $val ?>) {
                            $("#txtVTotal").val(0); $("#txtValor1").focus();
                            $("#btnDetalleHijos").prop('disabled',true);
                            $("#alerta").show();
                            setTimeout(function() { $("#alerta").fadeOut("fast"); },3000);
                        }else{
                            $("#btnDetalleHijos").prop('disabled',false);
                        }
                    }

                    $("#btnDetalleHijos").click(function(){
                        var form_data = $("#hijos").serialize();                
                        var result = "";
                        $.ajax({
                            type: 'POST',
                            url: "json/registrarDatosHijosPlanInventarioEntrada.php",
                            data: form_data,
                            success: function (data, textStatus, jqXHR) {
                                result = JSON.parse(data);
                                console.log(data);
                                if(result===true){
                                    $(".plan").modal('hide');         
                                    $("#modalGuardar").modal('show');
                                    $("#ver1").click(function(){
                                        $("#myModal1").modal('hide');
                                        window.history.go(-1);
                                    });
                                }else{
                                    $(".plan").modal('hide');         
                                    $("#ModalNoGuardar").modal('show');
                                    $("#ver2").click(function(){
                                        $("#myModal2").modal('hide');
                                        window.history.go(-1);
                                    });
                                }
                            }
                        });
                    });                                
                </script>
            </div>
        </div>
    </div>
</div>
