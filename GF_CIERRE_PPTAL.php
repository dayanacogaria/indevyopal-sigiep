<?php
#########################################################################
#  *************************** Modificaciones ***************************
#########################################################################
#22-01-2018 |Erica G. | ARCHIVO CREADO
#########################################################################
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$compania = $_SESSION['compania'];

#*****Consulta Años*****#
$sql = "SELECT id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC ";
$sql = $mysqli->query($sql);
?>
<html>
    <head>
        <title>Cierre Presupuestal</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <script>
            $().ready(function () {
                var validator = $("#form").validate({
                    ignore: "",
                    errorPlacement: function (error, element) {
                        $(element)
                                .closest("form")
                                .find("label[for='" + element.attr("id") + "']")
                                .append(error);
                    },
                });
                $(".cancel").click(function () {
                    validator.resetForm();
                });
            });
        </script>
        <style>
            body{
                font-size: 12px;
            }       
            label#sltAnnio-error, #sltTipoCierre-error {
                display: block;
                color: #bd081c;
                font-weight: bold;
                font-style: italic;

            }
        </style>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    
                       
                    <?php if(!empty($_GET['anno']) && !empty($_GET['tipo'])) { 
                        $tipo = $_GET['tipo'];
                        $anno = $_GET['anno'];
                        if($tipo == 1){
                            $ntipo = 'Constitución Cuentas Por Pagar';
                            $tipoClase          = 15;
                            $tipoVigencia       = 5;
                        }elseif($tipo==2){
                            $ntipo = 'Constitución Reservas';
                            $tipoClase          = 16;
                            $tipoVigencia       = 6;
                        }
                        $nannov = anno($anno);
                        $anno2 = $nannov+1;
                        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2'");
                        $parametrizacion = $an2[0][0];
                        
                        $row = $con->Listar("SELECT rb.codi_presupuesto,  
                            LOWER(rb.nombre), LOWER(f.nombre), dcp.valor 
                            FROM gf_detalle_comprobante_pptal dcp 
                            LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                            LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                            LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
                            LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            WHERE rb.tipoclase = $tipoClase and rb.tipovigencia = $tipoVigencia 
                            AND cp.parametrizacionanno = $parametrizacion 
                            AND (tc.clasepptal = 13)"); 
                        ?>
                    <br/>
                    <h2 align="center" class="tituloform" style="margin-top:-3px"><?php echo $ntipo.' Año:'.$anno2.' Vigencia:'.$nannov?></h2>
                    <a href="GF_CIERRE_PPTAL.php" class="btn sombra btn-primary" style="margin-left:10px; margin-top: -60px" title="Nuevo"><i class="glyphicon glyphicon-plus"></i></a>
                    <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="cabeza" style="display: none;">Identificador</td>
                                        <td class="cabeza" width="30px"></td>
                                        <td class="cabeza"><strong>Rubro</strong></td>
                                        <td class="cabeza"><strong>Fuente</strong></td>
                                        <td class="cabeza"><strong>Valor</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <th class="cabeza">Rubro</th>
                                        <th class="cabeza">Fuente</th>
                                        <th class="cabeza">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total =0;
                                    for ($i = 0; $i < count($row); $i++) {
                                        echo '<tr>';
                                        echo '<td class="campos" style="display: none;">Identificador</td>';
                                        echo '<td class="campos" ></td>';
                                        echo '<td class="campos" >'.$row[$i][0].' - '.ucwords($row[$i][1]).'</td>';
                                        echo '<td class="campos" >'.ucwords($row[$i][2]).'</td>';
                                        echo '<td class="campos" >'.number_format($row[$i][3],2,'.',',').'</td>';
                                        echo '</tr>';
                                        $total +=$row[$i][3];
                                    }?>                                    
                                </tbody>
                            </table>
                            <div class="col-sm-10" style="margin-top:5px; padding: 0px;" > 
                                <div class="valorT" style="font-size: 12px;" align="right">
                                    <label style="margin-right: 10px;">Valor Total: 
                                    <?php
                                    if (!empty($total)) {
                                        echo number_format($total, 2, '.', ',');
                                    }
                                    ?>
                                    </label>
                                </div>
                            </div> 
                        </div>
                        <div align="right">
                            <input type="hidden" name="sltTipoCierre" id="sltTipoCierre" value="<?php echo $tipo?>">
                            <input type="hidden" name="sltAnnio" id="sltAnnio" value="<?php echo $anno?>">
                            <a href="GF_CIERRE_PPTAL.php" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 10px" title="Nuevo"><i class="glyphicon glyphicon-plus"></i></a>
                            <a onclick="Generar()" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 10px" title="Generar">Generar De Nuevo</a>
                        </div> 
                    </div>
                        
                    <?php } else { ?>
                    <!-------Script Cargar Datos Si Ya Hay Datos Registrados ---------->
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Configuración Cierre Presupuestal</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:Generar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltTipoCierre" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Cierre:</label>
                                <select name="sltTipoCierre" id="sltTipoCierre" class="select2_single form-control" title="Seleccione Cierre" style="height: auto " required>
                                    <option value="">Cierre</option>
                                    <option value="1">Constitución Cuentas Por Pagar</option>
                                    <option value="2">Constitución Reservas</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                                <select name="sltAnnio" id="sltAnnio" class="select2_single form-control" title="Seleccione Año" style="height: auto " required>
                                    <option value="">Año</option>
                                    <?php
                                    while ($filaAnnio = mysqli_fetch_row($sql)) { ?>
                                        <option value="<?php echo $filaAnnio[0]; ?>"><?php echo $filaAnnio[1]; ?></option>                                
                                    <?php } ?>                                    
                                </select>
                            </div>

                            <div class="form-group" style="margin-top: 20px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn sombra btn-primary" title="Generar Configuración"> Generar</button>              
                            </div>
                        </form>
                    </div>
                    <script>
                        $("#sltAnnio").change(function(){
                            var anno = $("#sltAnnio").val();
                            var tipo = $("#sltTipoCierre").val();
                            if(anno !='' && tipo !=''){
                                if(tipo==1){
                                    var form_data = { action: 6, anno:anno };
                                    //Validar Si Ya Hubo Cierre 
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/gf_cierrepptalJson.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            if(response==0){

                                            } else {
                                                document.location = "GF_CIERRE_PPTAL.php?anno="+anno+"&tipo="+tipo;
                                            }
                                        }
                                    })
                                } else {
                                    if(tipo ==2){
                                        var form_data = { action: 5, anno:anno };
                                        //Validar Si Ya Hubo Cierre 
                                        $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/gf_cierrepptalJson.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                                if(response==0){
                                                   
                                                } else {
                                                    document.location = "GF_CIERRE_PPTAL.php?anno="+anno+"&tipo="+tipo;
                                                }
                                            }
                                        })
                                    }
                                }
                            }
                        });
                        $("#sltTipoCierre").change(function(){
                            var anno = $("#sltAnnio").val();
                            var tipo = $("#sltTipoCierre").val();
                            if(anno !="" && tipo !=""){
                                if(tipo==1){
                                    var form_data = { action: 6, anno:anno };
                                    //Validar Si Ya Hubo Cierre 
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/gf_cierrepptalJson.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            if(response==0){

                                            } else {
                                                document.location = "GF_CIERRE_PPTAL.php?anno="+anno+"&tipo="+tipo;
                                            }
                                        }
                                    })
                                } else {
                                    if(tipo ==2){
                                        var form_data = { action: 5, anno:anno };
                                        //Validar Si Ya Hubo Cierre 
                                        $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/gf_cierrepptalJson.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                                if(response==0){
                                                   
                                                } else {
                                                    document.location = "GF_CIERRE_PPTAL.php?anno="+anno+"&tipo="+tipo;
                                                }
                                            }
                                        })
                                    }
                                }
                            }
                        })
                    </script> 
                    <?php } ?>
                    
                </div>
            </div>
        </div>

        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    allowClear: true,
                });
            });
        </script>
        <div class="modal fade" id="myModalError" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                  <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                  </div>
                  <div class="modal-body" style="margin-top: 8px">
                      <labe id="mensaje" name="mensaje" style="font-weight:light"></labe>
                  </div>
                  <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrorModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                  </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModalAdv" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                  <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                  </div>
                  <div class="modal-body" style="margin-top: 8px">
                      <labe id="mensaje2" name="mensaje2" style="font-weight:light"></labe>
                  </div>
                  <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                  </div>
                </div>
            </div>
        </div>
        <script>
            function Generar(){
                var tipo =$("#sltTipoCierre").val();
                if(tipo == 1){
                    ValidarCuentas();
                } else{
                    if(tipo==2){
                        validarReservas();
                    }
                }
            }
        </script>  
        <script>
            function Generar2(tipo){
                
                if(tipo == 1){
                    ValidarCuentas();
                } else{
                    if(tipo==2){
                        validarReservas();
                    }
                }
            }
        </script>
        <script>
            function validarReservas(){
                var anno = $("#sltAnnio").val();
                if(anno !=""){
                    var form_data = { action: 5, anno:anno };
                    jsShowWindowLoad('Validando..');
                    //Validar Si Ya Hubo Cierre 
                    $.ajax({
                        type: "POST",
                        url: "jsonPptal/gf_cierrepptalJson.php",
                        data: form_data,
                        success: function(response)
                        {
                            if(response==0){
                                jsRemoveWindowLoad();
                                Reservas();
                            } else {
                                jsRemoveWindowLoad();
                                $("#mensaje2").html('Ya Existe Cierre Para Reservas.<br/> ¿Desea Generarlo De Nuevo?');
                                $("#myModalAdv").modal("show");
                                $("#btnCancelar").click(function(){
                                    $("#myModalError").modal("hide");
                                })
                                $("#btnAceptar").click(function(){
                                    //Eliminar Configuración Reservas
                                    var form_data = { action: 7, anno:anno };
                                    jsShowWindowLoad('Eliminando..');
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/gf_cierrepptalJson.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            jsRemoveWindowLoad();
                                            if(response==0){
                                                console.log(response);
                                                Reservas();
                                            } else {
                                                $("#mensaje").html('No Se Puede Generar De Nuevo Cierre <br/> Configuración Ya Tiene Movimientos');
                                                $("#myModalError").modal("show");
                                                $("#btnErrorModal").click(function(){
                                                    $("#myModalError").modal("hide");
                                                })
                                            }
                                        }
                                    })    
                                })
                            }
                        }
                    })
                }  
            } 
            function ValidarCuentas(){
                var anno = $("#sltAnnio").val();
                if(anno !=""){
                    var form_data = { action: 6, anno:anno };
                    jsShowWindowLoad('Validando..');
                    //Validar Si Ya Hubo Cierre 
                    $.ajax({
                        type: "POST",
                        url: "jsonPptal/gf_cierrepptalJson.php",
                        data: form_data,
                        success: function(response)
                        {
                            if(response==0){
                                jsRemoveWindowLoad();
                                CuentasXPagar();
                            } else {
                                jsRemoveWindowLoad();
                                $("#mensaje2").html('Ya Existe Cierre Para Reservas.<br/> ¿Desea Generarlo De Nuevo?');
                                $("#myModalAdv").modal("show");
                                $("#btnCancelar").click(function(){
                                    $("#myModalError").modal("hide");
                                })
                                $("#btnAceptar").click(function(){
                                    //Eliminar Configuración Reservas
                                    var form_data = { action: 8, anno:anno };
                                    jsShowWindowLoad('Eliminando..');
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/gf_cierrepptalJson.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            console.log(response);
                                            jsRemoveWindowLoad();
                                            if(response==0){
                                                
                                                CuentasXPagar();
                                            } else {
                                                $("#mensaje").html('No Se Puede Generar De Nuevo Cierre <br/> Configuración Ya Tiene Movimientos');
                                                $("#myModalError").modal("show");
                                                $("#btnErrorModal").click(function(){
                                                    $("#myModalError").modal("hide");
                                                })
                                            }
                                        }
                                    })    
                                })
                            }
                        }
                    })
                } 
                
            }
        </script>
        <script>
            function Reservas(){
                var anno = $("#sltAnnio").val();
                if(anno !=""){
                    var form_data = { action: 3, anno:anno };
                    jsShowWindowLoad('Validando..');
                    $.ajax({
                        type: "POST",
                        url: "jsonPptal/gf_cierrepptalJson.php",
                        data: form_data,
                        success: function(response)
                        {
                            jsRemoveWindowLoad();
                            resultado = JSON.parse(response);
                            var msj = resultado["msj"];
                            var rta = resultado["rta"];
                            if(rta==1){
                               $("#mensaje").html(msj);
                               $("#myModalError").modal("show");
                                $("#btnErrorModal").click(function(){
                                    $("#myModalError").modal("hide");
                               })
                               $("#btnErrorAceptar").click(function(){
                                    $("#myModalError").modal("hide");
                               })
                            } else {
                                jsShowWindowLoad('Guardando..');
                                var form_data = { action: 4, anno:anno };
                                $.ajax({
                                    type: "POST",
                                    url: "jsonPptal/gf_cierrepptalJson.php",
                                    data: form_data,
                                    success: function(response)
                                    {
                                        console.log(response);
                                        jsRemoveWindowLoad();
                                        resultado = JSON.parse(response);
                                        var msj = resultado["msj"];
                                        var rta = resultado["rta"];
                                        if(rta==1){
                                            $("#mensaje").html('No se Ha Podido Generar La Configuración');
                                            $("#myModalError").modal("show");
                                             $("#btnErrorModal").click(function(){
                                                 $("#myModalError").modal("hide");
                                            }) 
                                            $("#btnErrorAceptar").click(function(){
                                                    $("#myModalError").modal("hide");
                                            })
                                        } else {
                                            if(rta==3){
                                                $("#mensaje").html(msj);
                                                $("#myModalError").modal("show");
                                                 $("#btnErrorModal").click(function(){
                                                    document.location.reload();
                                                })
                                                $("#btnErrorAceptar").click(function(){
                                                    document.location.reload();
                                               })
                                            } else {
                                                var tipo =$("#sltTipoCierre").val();
                                                document.location = "GF_CIERRE_PPTAL.php?anno="+anno+"&tipo="+tipo;
                                            }
                                        }
                                        
                                    }
                                })
                            }
                        }
                    }); 
                 }
            }
        </script>
        <script>
            function CuentasXPagar(){
                var anno = $("#sltAnnio").val();
                if(anno !=""){
                    var form_data = { action: 1, anno:anno };
                    jsShowWindowLoad('Validando..');
                    $.ajax({
                        type: "POST",
                        url: "jsonPptal/gf_cierrepptalJson.php",
                        data: form_data,
                        success: function(response)
                        {
                            jsRemoveWindowLoad();
                            resultado = JSON.parse(response);
                            var msj = resultado["msj"];
                            var rta = resultado["rta"];
                            if(rta==1){
                               $("#mensaje").html(msj);
                               $("#myModalError").modal("show");
                                $("#btnErrorModal").click(function(){
                                    $("#myModalError").modal("hide");
                               })
                               $("#btnErrorAceptar").click(function(){
                                    $("#myModalError").modal("hide");
                               })
                            } else {
                                jsShowWindowLoad('Guardando..');
                                var form_data = { action: 2, anno:anno };
                                $.ajax({
                                    type: "POST",
                                    url: "jsonPptal/gf_cierrepptalJson.php",
                                    data: form_data,
                                    success: function(response)
                                    {
                                        jsRemoveWindowLoad();
                                        if(response==1){
                                            $("#mensaje").html('No se Ha Podido Generar La Configuración');
                                            $("#myModalError").modal("show");
                                             $("#btnErrorModal").click(function(){
                                                 $("#myModalError").modal("hide");
                                            }) 
                                            $("#btnErrorAceptar").click(function(){
                                                $("#myModalError").modal("hide");
                                            })
                                        } else {
                                            var tipo =$("#sltTipoCierre").val();
                                            document.location = "GF_CIERRE_PPTAL.php?anno="+anno+"&tipo="+tipo;
                                        }
                                        
                                    }
                                })
                            }
                        }
                    }); 
                 }
            }
        </script>
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
    </body>
</html>

