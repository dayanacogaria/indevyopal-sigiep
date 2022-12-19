<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
$b="";
if(!empty($_GET['b'])){
    $b=1;
}
?>
<title>Comparar Planes Contables</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -20px"> 
                <h2 align="center" class="tituloform">Comparación Cuentas</h2>
                <div id="divtabla">
                    
                </div>
            </div>
        </div>
    </div>
    
   
    <?php require_once 'footer.php' ?>  
    <script>
        $(document).ready(function (){
            jsShowWindowLoad('Comparando Cuentas'); 
            var form_data = { action:5 };
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_cuentaJson.php",
                data:form_data,
                success: function(response)
                {
                    jsRemoveWindowLoad();
                    $("#divtabla").html(response)
                }
            });
        })
    </script>
    <script>
        function guardarCuentas(){
            var cuentas = $("#cuentas").val();
            if(cuentas!=""){
                jsShowWindowLoad('Guardando Cuentas');
                var form_data = { action:6, cuentas:cuentas };
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_cuentaJson.php",
                    data:form_data,
                    success: function(response)
                    {
                        console.log(response);
                        jsRemoveWindowLoad();
                        $("#mensaje").html(response + ' Cuentas Creadas En El Plan Presupuestal Actual');
                        $("#modalMensaje").modal("show");
                        $("#btnMsjAc").click(function(){
                            document.location="buscarCuenta.php";
                        })
                    }
                });
            }
            
        }
    </script>
     <div class="modal fade" id="modalMensaje" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight:normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnMsjAc" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
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
</body>