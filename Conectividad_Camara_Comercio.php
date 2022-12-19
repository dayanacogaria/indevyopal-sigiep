<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
?>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <title>Camara Comercio</title>
    <style>
        #path_file, #path_file1{
            display: none;
        }
    </style>
</head>
<body onload="CargarMetodos()">
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Conexión Cámara de Comercio</h2>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalListo" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p><label id="msj"> </label></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnRListo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalArchivo" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -25px">
                        <button type="button" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                    </div>
                </div>
                <div class="modal-body text-center" style="margin-top: 5px">
                    <label for="" class="control-label col-sm-12 text_center">El archivo a sido creado correctamente</label>
                    <br/>
                    <br/>
                    <a href="" id="path_file" name="path_file" download="" class="text-center">Descargar el archivo Contribuyentes</a>
                    <br/>
                    <a href="" id="path_file1" name="path_file1" download="" class="text-center">Descargar el archivo Estableciminientos</a>
                </div>
                <div id="forma-modal" class="modal-footer"></div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        function CargarMetodos(){
            jsShowWindowLoad('Actualizando Registros. <br>Espere por favor');
            var form_data = { id:1 };
            $.ajax({
                type:"POST",
                url:"Conexion_CamaraC/conexion.php",
                data:form_data,
                success: function (data) {
                    jsRemoveWindowLoad();
                    if(data.length > 0){
                        if(data.indexOf(";")!== 0){
                            var dato = data.split(";");
                            console.log(dato);
                            if(dato[1]){
                                var path = dato[1];
                                $("#path_file").css('display', 'block').attr('href',path.substring(2));
                                $("#modalArchivo").modal('show');
                            }
                            if(dato[2]){
                                var path1 = dato[2];
                                $("#path_file1").css('display', 'block').attr('href',path1.substring(2));
                                $("#modalArchivo").modal('show');
                            }
                        }
                    }
                }
            });
        }

        function jsRemoveWindowLoad() {
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
    <?php require_once 'footer.php';?>
</body>
</html>
