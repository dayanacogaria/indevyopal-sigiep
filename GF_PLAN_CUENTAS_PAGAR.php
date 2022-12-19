<?php
#########################################################################
#  *************************** Modificaciones ***************************
#########################################################################
#17-01-2018 |Erica G. | ARCHIVO CREADO
#########################################################################
require_once ('Conexion/conexion.php');
require_once 'head_listar.php';
$compania = $_SESSION['compania'];

#*****Consulta Años*****#
$sql = "SELECT id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC ";
$sql = $mysqli->query($sql);
?>
<html>
    <head>
        <title>Configuración Cierre Cuentas Por Pagar</title>
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
            label#sltAnnio-error {
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
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Configuración Cuentas Por Pagar</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:Generar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
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
                    <button type="button" id="btnErrorModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                  </div>
                </div>
            </div>
        </div>
        <script>
            function Generar(){
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
                                        } else {
                                            document.location = "Mantenimiento_Rubros.php";
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

