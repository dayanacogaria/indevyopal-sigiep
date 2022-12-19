<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#04/07/2018 |Erica G. | Formato
#11/04/2018 |Erica G. | Archivo Creado 
#######################################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
?>
<html>
    <head>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            label #file-error,#sucursal-error {
                display: block;
                color: #bd081c;
                font-weight: bold;
                font-style: italic;

            }
        </style>
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
        <title>Subir Archivo Comparendos </title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <!--Titulo del formulario-->
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Subir Archivo Comparendos</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Comparendos.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                            <div class="form-group" style="margin-top: -10px; ">
                                <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Sucursal:</label>
                                <select name="sucursal" id="sucursal" class="select2_single form-control" title="Seleccione Sucursal" style="height: auto " required>
                                    <?php 
                                    #Consulta Sucursal
                                    $row = $con->Listar("SELECT id_unico, LOWER(nombre) FROM gf_sucursal");
                                    echo '<option value="">Sucursal</option>';
                                    for ($i = 0; $i < count($row); $i++) {
                                        echo '<option value = "'.$row[$i][0].'">'. ucwords($row[$i][1]).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -10px; ">
                                <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                                <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>      
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalRequerido" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">

                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Seleccione un archivo.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnRequerido" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
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
                        <button type="button" id="btnAceptal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="js/select/select2.full.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script>
          $(document).ready(function () {
              $(".select2_single").select2({
                  allowClear: true
              });
          });
        </script>
        <script>
            function jsRemoveWindowLoad() {
                $("#WindowLoad").remove();
            }
            function jsShowWindowLoad(mensaje) {
                jsRemoveWindowLoad();
                if (mensaje === undefined)
                    mensaje = "Procesando la información<br>Espere por favor";
                height = 20;//El div del titulo, para que se vea mas arriba (H)
                var ancho = 0;
                var alto = 0;
                if (window.innerWidth == undefined)
                    ancho = window.screen.width;
                else
                    ancho = window.innerWidth;
                if (window.innerHeight == undefined)
                    alto = window.screen.height;
                else
                    alto = window.innerHeight;
                //operación necesaria para centrar el div que muestra el mensaje
                var heightdivsito = alto / 2 - parseInt(height) / 2;//Se utiliza en el margen superior, para centrar 
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
        <script>
            function guardar() {
                var formData = new FormData($("#form")[0]);
                jsShowWindowLoad('Guardando Datos ...');
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gu_ComparendosJson.php?action=1",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        console.log(data);
                        jsRemoveWindowLoad();
                        if(data>0){
                            $("#mensaje").html(data+" Registros Guardados");
                            $("#modalMensaje").modal("show");
                            $("#btnAceptal").click(function(){
                                document.location.reload();
                            })
                        } else {
                            $("#mensaje").html("No Se Ha Podido Guardar La Información");
                            $("#modalMensaje").modal("show");
                            $("#btnAceptal").click(function(){
                                $("#modalMensaje").modal("hide");
                            })
                        }
                    }
                });
            }
        </script>
        <?php require_once 'footer.php'; ?>
    </body>
</html>

