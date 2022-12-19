<?php
require_once '../Conexion/conexion.php';
session_start();
$id= '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
$mov= $mysqli->real_escape_string(''.$_POST['id'].'');
$elemento= '"'.$mysqli->real_escape_string(''.$_POST['sltPlanInv'].'').'"';
$cantidad= '"'.$mysqli->real_escape_string(''.$_POST['txtCantidad'].'').'"';
$valor= '"'.$mysqli->real_escape_string(''.$_POST['txtValor'].'').'"';
$val = $mysqli->real_escape_string(''.$_POST['txtValor'].'');
$iva= '"'.$mysqli->real_escape_string(''.$_POST['txtValorIva'].'').'"';
$sql = "INSERT INTO gf_detalle_movimiento (cantidad, valor, iva, movimiento, planmovimiento) VALUES ( $cantidad, $valor, $iva, $id, $elemento)";
$resultado = $mysqli->query($sql);
$sqlV = "select max(id_unico) from gf_detalle_movimiento";
$resultV = $mysqli->query($sqlV);
$v = $resultV->fetch_row();
$detalle = $v[0];
?>  
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/md5.pack.js"></script>
    <script src="../js/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
    <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
</head>
<body>
</body>
</html>
<!--Modal para informar al usuario que se ha registrado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información guardada correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!--Modal para informar al usuario que no se ha podido registrar -->
<div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se ha podido guardar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal de registro de detalle de planInventario -->
<?php
$sqlPlanPadre5 = "select planA.plan_hijo from gf_plan_inventario_asociado planA where planA.plan_padre=$elemento";
$resultPadre5 = $mysqli->query($sqlPlanPadre5);
$existencias5 = mysqli_num_rows($resultPadre5);
if($existencias5>0){ ?>
    <div class="modal fade" id="modalDetalleHijos" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                <?php
                    $sqlIVa = "select valor from gs_parametros_basicos where id_unico = 2";
                    $resultIva = $mysqli->query($sqlIVa);
                    $iva = $resultIva->fetch_row();
                    $porcIva = $iva[0];
                ?>
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Registro de Detalle</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <form  id="hijos" method="POST">
                    <?php
                echo '<div class="alert alert-info" id="alerta" style="display:none;margin-top:2px;width: 550px;">
                        <h5><strong>Informacion!</strong></h5><p>El valor total acumulado es mayor que'.PHP_EOL.number_format($val,2,'.',',').'.</p>
                      </div>';
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
                foreach ($master as $master) {                    
                    foreach ($master as $key => $value) {
                        $z++;
                        $sqlPlan="select id_unico,CONCAT(codi,' - ',nombre),ficha from gf_plan_inventario where id_unico=$value";
                        $resultPlan = $mysqli->query($sqlPlan);
                        $planIn = mysqli_fetch_row($resultPlan);
                        echo '<div class="form-group form-inline">';
                        echo '<label class="control-label text-left col-sm-4">'.$planIn[1].'</label>';
                        echo '<input type="hidden" class="hidden" value="'.$planIn[0].'" name="txtPlan'.$z.'" id="txtPlan'.$z.'" />';
                        echo '<input type="number" name="txtCantidad'.$z.'" onkeyup="return calcular('.$z.')" class="form-control" value="'.$cantidad.'" id="txtCantidad'.$z.'" title="Cantidad" maxlength="50" placeholder="Cantidad" style="height:26px;padding:2px;width:100px;margin-left: -20px;margin-right: 10px;cursor:pointer" required>';
                        echo '<input type="number" name="txtValor'.$z.'" onkeyup="return calcular('.$z.');" onfocusout="return acumular('.$z.')" class="form-control" id="txtValor'.$z.'" title="Valor aproximado" maxlength="50" style="height:26px;padding:2px;width:100px;margin-right: 10px;cursor:pointer" placeholder="Valor" required>';
                        echo '<input type="number" name="txtValorIva'.$z.'" class="form-control disabled"  placeholder="Iva" id="txtValorIva'.$z.'" title="Iva" maxlength="50" style="height:26px;padding:2px;width:100px;margin-right:10px;cursor:pointer" readonly="" required>';?>
                        <!--<a id="plan<?php echo $z?>" href="javascript:void(0)" title="Ficha Inventario" onclick="return abrirFichaI($('#txtCantidad<?php echo $z?>').val(),'<?php echo $planIn[1]?>',$('txtValor<?php echo $z?>').val(),'<?php echo $detalle ?>')" data-backdrop="static" data-keyboard="false" data-toggle="modal"><i class="glyphicon glyphicon-blackboard"></i></a>-->
                        <?php
                        echo '</div><br/>';
                    }
                }
                $y = $z;
                foreach ($childrens as $childrens){
                    foreach ($childrens as $key => $value) {
                        if(!empty($value)){
                            $y++;
                            $sqlPlan="select id_unico,CONCAT(codi,' - ',nombre) from gf_plan_inventario where id_unico=$value";
                            $resultPlan = $mysqli->query($sqlPlan);
                            $planIn = mysqli_fetch_row($resultPlan);
                            echo '<div class="form-group form-inline">';
                            echo '<label class="control-label text-left col-sm-4">'.$planIn[1].'</label>';
                            echo '<input type="hidden" class="hidden" value="'.$planIn[0].'" name="txtPlan'.$y.'" id="txtPlan'.$y.'" />';
                            echo '<input type="number" name="txtCantidad'.$y.'" onkeyup="return calcular('.$y.')" value="'.$cantidad.'" class="form-control" id="txtCantidad'.$y.'" title="Cantidad" maxlength="50" placeholder="Cantidad" style="height:26px;padding:2px;width:100px;margin-left: -20px;margin-right: 10px;cursor:pointer" required>';
                            echo '<input type="number" name="txtValor'.$y.'" onkeyup="return calcular('.$y.');" onfocusout="return acumular('.$y.')" class="form-control" id="txtValor'.$y.'" title="Valor aproximado" maxlength="50" style="height:26px;padding:2px;width:100px;margin-right: 10px;cursor:pointer" placeholder="Valor" required>';
                            echo '<input type="number" name="txtValorIva'.$y.'" class="form-control disabled"  placeholder="Iva" id="txtValorIva'.$y.'" title="Iva" maxlength="50" style="height:26px;padding:2px;width:100px;margin-right:10px;cursor:pointer" readonly="" required>'; ?>
                            <!--<a id="plan<?php echo $z?>" href="javascript:void(0)" title="Ficha Inventario" onclick="return abrirFichaI($('#txtCantidad<?php echo $z?>').val(),'<?php echo $planIn[1]?>',$('txtValor<?php echo $z?>').val(),'<?php echo $detalle ?>')" data-backdrop="static" data-keyboard="false" data-toggle="modal"><i class="glyphicon glyphicon-blackboard"></i></a>-->
                            <?php
                            echo '</div><br/>';                            
                        }                                                    
                    }
                }
                echo '<div class="form-group">';
                echo '<label class="control-label text-left col-sm-4">Valor total acumulado:</label>';
                echo '<input type="number" value="0" id="txtVTotal" name="txtVTotal" class="form-control col-sm-2" title="El valor total acumulado no puede ser mayor a'.PHP_EOL.number_format($val,2,'.',',').'" style="width:150px;cursor:pointer;margin-left:15px" readonly/>';
                echo '</div><br/>';
                echo '<input type="hidden" class="hidden" name="txtCantidadV" id="txtCantidadV" value="'.$y.'" />';
                echo '<input type="hidden" class="hidden" name="txtMovimiento" id="txtMovimiento" value="'.$mov.'" />';
                echo '<input type="hidden" class="" name="txtTotal" id="txtTotal" />';
            ?>
        </div>    
        <script type="text/javascript" >
            function calcular(x){
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
                iva = parseFloat(<?php echo $porcIva; ?>);
                total = cantidad*valor;               
                totalIva = (total*iva)/100;
                $("#txtValorIva"+x).val(totalIva.toFixed(2));
                total = total+totalIva;
                $("#txtValorTotal"+x).val(total.toFixed(2));
            }
            
            function acumular(x){
                var valor = "#txtValor"+x;
                if($(valor).val().length===0){
                    $("#txtTotal").val(0);
                }else{
                    var campo = parseFloat($("#txtVTotal").val());
                    valor = parseFloat($(valor).val());
                    var total = campo+valor;
                    $("#txtVTotal").val(total);                     
                    if(total><?php echo $val; ?>){
                        var valorX = <?php echo $val ?>;
                        var y = valorX-total;
                        $("#txtTotal").val(y);
                        $("#txtTotal").val(0);
                        $("#txtValor1").focus();
                        $("#btnDetalleHijos").prop('disabled',true);
                        $("#alerta").show();
                        setTimeout(function() {
                            $("#alerta").fadeOut("fast");
                        },3000);
                    }else{
                        $("#btnDetalleHijos").prop('disabled',false);
                    }
                }                                
            }
                        
        </script>
        </form>        
        <div id="forma-modal" class="modal-footer">
            <button type="submit" id="btnDetalleHijos" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
            <script type="text/javascript">
                $("#btnDetalleHijos").click(function(){
                    var form_data = $("#hijos").serialize();                
                    var result = "";
                    $.ajax({
                        type: 'POST',
                        url: "registrarDatosHijosPlanInventarioEntrada.php",
                        data: form_data,
                        success: function (data, textStatus, jqXHR) {
                            result = JSON.parse(data);                            
                            if(result===true){
                                $("#modalDetalleHijos").modal('hide');         
                                $("#myModal1").modal('show');
                                $("#ver1").click(function(){
                                    $("#myModal1").modal('hide');
                                    window.history.go(-1);
                                });
                            }else{
                                $("#modalDetalleHijos").modal('hide');         
                                $("#myModal2").modal('show');
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
        <?php } ?>
<!--lnks para el estilo de la pagina-->
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php 
    if($resultado==true){ 
        $sqlPlanPadre = "select planA.plan_hijo from gf_plan_inventario_asociado planA where planA.plan_padre=$elemento";
        $resultPadre = $mysqli->query($sqlPlanPadre);
        $existencias = mysqli_num_rows($resultPadre);   
        if($existencias>0){ ?>
            <script type="text/javascript">
                $("#modalDetalleHijos").modal('show');
            </script>
        <?php    
        }
    ?>
    <script type="text/javascript">
        $("#myModal1").modal('show');
        $("#ver1").click(function(){
            $("#myModal1").modal('hide');
            window.history.go(-1);
        });
    </script>
    <?php }else{ ?>
        <script type="text/javascript">
            $("#myModal2").modal('show');
            $("#ver2").click(function(){
                $("#myModal2").modal('hide');
                window.history.go(-1);
            });
        </script>
    <?php } ?>