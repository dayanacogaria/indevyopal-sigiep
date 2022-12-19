<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#27/07/2018 |Erica G. | Buscar Por Fechas
#######################################################################################################
require_once 'Conexion/conexion.php';
require_once 'Conexion/ConexionPDO.php';
require_once './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$con = new ConexionPDO();
$anno  = $_SESSION['anno'];
$rowfecha = $con->Listar("SELECT DISTINCT MONTH(fecha), 
                CASE WHEN MONTH(fecha) = 1 THEN 'Enero'
                WHEN MONTH(fecha) = 2 THEN 'Febrero'
                WHEN MONTH(fecha) = 3 THEN 'Marzo'
                WHEN MONTH(fecha) = 4 THEN 'Abril'
                WHEN MONTH(fecha) = 5 THEN 'Mayo'
                WHEN MONTH(fecha) = 6 THEN 'Junio'
                WHEN MONTH(fecha) = 7 THEN 'Julio'
                WHEN MONTH(fecha) = 8 THEN 'Agosto'
                WHEN MONTH(fecha) = 9 THEN 'Septiembre'
                WHEN MONTH(fecha) = 10 THEN 'Octubre'
                WHEN MONTH(fecha) = 11 THEN 'Noviembre'
                WHEN MONTH(fecha) = 12 THEN 'Diciembre' 
                END as 'f' 
                FROM gc_recaudo_comercial pr  
                WHERE pr.parametrizacionanno= $anno 
                AND pr.clase = 2 
                ORDER BY MONTH(fecha) ASC");
$row = $con->Listar("SELECT DISTINCT id_unico, consecutivo  
        FROM gc_recaudo_comercial WHERE parametrizacionanno = $anno ORDER BY fecha")
?>
<title>Listar Recaudos Reteica</title>	
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link href="css/select/select2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom:20px; margin-right:4px; margin-left:4px;">Recaudos Reteica</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="mes" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Mes:</label>
                            <select   name="mes" id="mes" class="select2_single form-control" title="Seleccione Mes">
                                <option value="">Mes</option>
                                <?php 
                                    for ($i = 0; $i < count($rowfecha); $i++) {
                                        echo '<option value="'.$rowfecha[$i][0].'">'.$rowfecha[$i][1].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="fechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <select   name="fechaI" id="fechaI" class="select2_single form-control" title="Seleccione Fecha Inicial">
                                <option value="">Fecha Inicial</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="fechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <select   name="fechaF" id="fechaF" class="select2_single form-control" title="Seleccione Fecha Final">
                                <option value="">Fecha Final</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 20px;" >
                            <label class="col-sm-5">&nbsp;</label>
                            <button type="button" onclick="guardar()" class="btn sombra btn-primary" title="Generar">GENERAR INTERFAZ</button>              
                            
                        </div>
                    </div>
                </form>
                <script>
                    $("#mes").change(function(){
                        var mes = $("#mes").val();
                        var optionMI = "<option value=''>Fecha</option>";
                        if(mes !=""){
                            var form_data = { action: 16, mes:mes};
                            $.ajax({
                              type: "POST",
                              url: "jsonPptal/gf_interfaz_ComercioJson.php",
                              data: form_data,
                              success: function(response)
                              { 
                                  console.log(response);
                                  optionMI =optionMI+response;
                                    $("#fechaI").html(optionMI).focus();
                                    $("#fechaF").html(optionMI).focus();
                              }
                            })
                        }
                    });
                </script>
                </div>
            </div>
        </div>
    </div>
   <div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
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
    <script>
        function guardar(){
            jsShowWindowLoad('Verificando..');
            var fechaI =$("#fechaI").val();
            var fechaF =$("#fechaF").val();
            var form_data = { action: 1,fechaI:fechaI,fechaF:fechaF};
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_interfaz_IcaFuncion.php",
                data: form_data,
                success: function(response)
                {
                    jsRemoveWindowLoad();
                    console.log(response);
                    var resultado = JSON.parse(response);
                    var rta = resultado["rta"];
                    var mensaje = resultado["msj"];
                    if(rta==1){
                        jsShowWindowLoad('Guardando..');
                        var form_data = { action: 2,fechaI:fechaI,fechaF:fechaF };
                        $.ajax({
                            type: "POST",
                            url: "jsonPptal/gf_interfaz_IcaFuncion.php",
                            data: form_data,
                            success: function(response)
                            {
                                console.log(response);
                                jsRemoveWindowLoad();
                                if(response==1){
                                    $("#mensaje").html('Comprobante Guardado Correctamente');
                                    $("#mdlMensajes").modal("show");
                                    $("#btnAceptar").click(function(){
                                        document.location.reload();
                                    });
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Registrar Comprobante');
                                    $("#mdlMensajes").modal("show");
                                    $("#btnAceptar").click(function(){
                                        document.location.reload();
                                    });
                                }
                            }
                        })
                    } else {
                        $("#mensaje").html(mensaje);
                        $("#mdlMensajes").modal("show");
                        $("#btnAceptar").click(function(){
                            $("#mdlMensajes").modal("hide");
                        });
                    }
                }
            })
            
            
            
        }
    </script>
    <script>
        function ver(id, idp){
            var form_data = { action: 9, id:id, idp:idp };
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_interfaz_ComercioJson.php",
                data: form_data,
                success: function(response)
                {
                    window.open("registrar_GF_COMPROBANTE_INGRESO.php");
                }
            })
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
