<?php   
##################################################################################################################################################################
#                                      Modificaciones
#################################################################################################################################################################
#17/05/2018 | ERICA G. | Se Envio Primero Al Archivo de Las Funcionesy Luego A Generar El Informe
#16/05/2018 | ERICA G. | Informe Creado
##################################################################################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php';
@session_start();
$annio = $_SESSION['anno'];
?>
<!-- Llamado a la cabecera del formulario -->

<title>Ejecución Gerencial De Ingresos</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltAnnio-error, #sltmes-error, #sltcni-error, #sltcnf-error, #digitos-error  {
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;
    }
    body{
        font-size: 12px;
    }
</style>
<script>
$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",
    errorPlacement: function(error, element) {
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    }
  });
  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<style>
    .form-control {font-size: 12px;}
</style>
<body> 
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -20px"> 
                <h2 align="center" class="tituloform">Ejecución Gerencial De Ingresos</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">  
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <?php
                            $annio = "SELECT id_unico,anno FROM gf_parametrizacion_anno ORDER BY anno DESC";
                            $rsannio = $mysqli->query($annio);
                            ?> 
                            <!--- Consulta para Cargar Año Inicial--->              
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                                <select required name="sltAnnio" id="sltAnnio" class=" select2_single form-control" title=    "Seleccione Año" >
                                    <option value>Año</option>
                                    <?php
                                    while ($filaAnnio = mysqli_fetch_row($rsannio)) {
                                        ?>
                                        <option value="<?php echo $filaAnnio[0]; ?>"><?php echo $filaAnnio[1]; ?></option>                                
                                        <?php
                                    }
                                    ?>                                    
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltmes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes:</label>
                                <select required name="sltmes" id="sltmes"  class="select2_single form-control" title="Mes Inicial" >
                                    <option value>Mes</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltcni" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Inicial:</label>
                                <select required name="sltcni" id="sltcni"  class="select2_single form-control" title="Seleccione Código inicial" >
                                    <option value>Código Inicial</option>                                   
                                </select>
                            </div> 
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltcnf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Final:</label>
                                <select required name="sltcnf" id="sltcnf" class=" select2_single form-control" title=
                                        "Seleccione Código final">
                                    <option value>Código Final</option>                                
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="digitos" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Dígitos:</label>
                                <select name="digitos" id="digitos" class="select2_single form-control" title="Seleccione Número Dígitos" required="required">
                                    <option value>Dígitos</option>
                                    <option value="4">4</option>           
                                    <option value="6">6</option>           
                                </select>
                                <input type="hidden" name="tipoGrafico" id="tipoGrafico" value="3">
                            </div>
                            <div class="col-sm-10" style="margin-top:0px;margin-left:700px" >
                                <button style="margin-left:10px;" type="submit" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </form>
                </div>     
            </div>
        </div>
    </div>
    <script src="js/select/select2.full.js"></script>
    <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
    </script>
    <script>    
        $("#sltAnnio").change(function(){
        
           var form_data={action: 1, annio :$("#sltAnnio").val()};
           var optionMI ="<option value=''>Mes Acumulado</option>";
           $.ajax({
              type:'POST', 
              url:'jsonPptal/consultasInformesCnt.php',
              data: form_data,
              success: function(response){
                  optionMI =optionMI+response;
                  $("#sltmes").html(optionMI).focus();              
              }
           });
           var form_data={action: 14, annio :$("#sltAnnio").val()};
           var optionCI ="<option value=''>Código Inicial</option>";
           $.ajax({
              type:'POST', 
              url:'jsonPptal/consultasInformesCnt.php',
              data: form_data,
              success: function(response){
                  optionCI =optionCI+response;
                  $("#sltcni").html(optionCI).focus();              
              }
           });
           var form_data={action: 15, annio :$("#sltAnnio").val()};
           var optionCF ="<option value=''>Código Final</option>";
           $.ajax({
              type:'POST', 
                url: 'jsonPptal/consultasInformesCnt.php',
                data: form_data,
                success: function (response) {
                    optionCF = optionCF + response;
                    $("#sltcnf").html(optionCF).focus();
                }
            });
            var form_data = {action: 13, annio: $("#sltAnnio").val()};
            var optionF = "<option value=''>Fuente</option>";
            $.ajax({
                type: 'POST',
                url: 'jsonPptal/consultasInformesCnt.php',
                data: form_data,
                success: function (response) {
                    optionF = optionF + response;
                    $("#fuente").html(optionF).focus();
                }
            });


        });
    </script>
    <?php require_once 'footer.php' ?> 
    <script>
        function guardar() {
            if($("#tipoGrafico").val()!=""){
                var formData = new FormData($("#form")[0]);  
                jsShowWindowLoad('Generando Informe...');
                var form_data = { action:1 };
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/consultasInformesPptal.php?action=7",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    {
                        jsRemoveWindowLoad();
                        console.log(response);
                        var tipoGrafico = $("#tipoGrafico").val();
                        window.open('informes/INFORMES_PPTAL/INF_PPTAL_GERENCIAL.php?tipoGrafico='+tipoGrafico+"&digitos="+$("#digitos").val());
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
</body>
</html>