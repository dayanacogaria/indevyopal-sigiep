<?php 
##########################################################################################
# *********************************** Modificaciones *********************************** # 
##########################################################################################
#30/05/2018 |Erica G.| Informes Convergencia
#13/04/2018 |Erica G.| Archivo Creado
##########################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$con= new ConexionPDO();
?>
<title>Estado De Apertura</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #sltAnnio-error, #dexportar-error, #tipoInforme-error, #sltcodi-error, #sltcodf-error, #separador-error {
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
                <h2 align="center" class="tituloform">Estado De Situación Financiera De Apertura</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                            <select name="sltAnnio" id="sltAnnio" class="select2_single form-control" title="Seleccione Año" style="height: auto " required>
                                <option value="">Año</option>
                                <?php 
                                $annio = "SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
                                $rsannio = $mysqli->query($annio);
                                while ($filaAnnio = mysqli_fetch_row($rsannio)) { ?>
                                     <option value="<?php echo $filaAnnio[0];?>"><?php echo $filaAnnio[1];?></option>                                
                                <?php }?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Inicial:</label>
                            <select required name="sltcodi" id="sltcodi" style="height: auto" class="select2_single form-control" title="Seleccione Cuenta Inicial">
                                <option value="">Código Inicial</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Final:</label>
                            <select required name="sltcodf" id="sltcodf" style="height: auto" class="select2_single form-control" title="Seleccione Cuenta Final">
                                <option value="">Código Final</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="tipoInforme" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Informe:</label>
                            <select required name="tipoInforme" id="tipoInforme" style="height: auto" class="select2_single form-control" title="Tipo Informe">
                                <option value="1">Informe General</option>
                                <option value="2">CGN2015_001_SI_CONVERGENCIA</option>
                                <option value="3">CGN2015_001_ESFA_CONVERGENCIA</option>
                            </select>
                        </div>
                        <div class="form-group" id="divexportar"  style="display:none; margin-top: -5px">
                            <label for="dexportar" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Exportar:</label>
                            <select name="dexportar" id="dexportar" style="height: auto" class="select2_single form-control" title="Seleccione Exportar">
                                <option value="">Exportar</option>
                                <option value="1">csv</option>
                                <option value="2">txt</option>
                                <option value="3">xls</option>
                            </select>
                        </div>
                        <div class="form-group" id="divseparador"  style="display:none; margin-top: -5px">
                            <label for="separador" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Separador:</label>
                            <select name="separador" id="separador" style="height: auto" class="select2_single form-control" title="Seleccione Separador">
                                <option value="">Separador</option>
                                <option value=",">,</option>
                                <option value=";">;</option>
                                <option value="tab">tab</option>
                            </select>
                        </div>
                        <input type="hidden" id="tipo" name="tipo">
                        <div class="col-sm-10" id="exportarGeneral" style="display:block; margin-top:0px;margin-left:600px" >
                            <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>
                        <div class="col-sm-10" id="exportarGeneral2" style="display:none; margin-top:0px;margin-left:600px" >
                            <button onclick="reporteGeneral()" class="btn sombra btn-primary" title="Generar reporte">Generar</button>              
                        </div>
                    </div>
                </form>
            </div>    
        </div>
    </div>
</div>
<script>    
    $("#sltAnnio").change(function(){
       var form_data={action: 3, annio :$("#sltAnnio").val()};
       var optionCI ="<option value=''>Código Inicial</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionCI =optionCI+response;
              $("#sltcodi").html(optionCI).focus();              
          }
       });
       var form_data={action: 4, annio :$("#sltAnnio").val()};
       var optionCF ="<option value=''>Código Final</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionCF =optionCF+response;
              $("#sltcodf").html(optionCF).focus();              
          }
       });
       
       
    });
</script>
<script>
    $("#tipoInforme").change(function(){
        var tipoI = $("#tipoInforme").val();
        if(tipoI==1){
            $("#exportarGeneral").css("display","block");
            $("#divexportar").css("display","none");
            $("#divseparador").css("display","none");
            $("#exportarGeneral2").css("display","none");
            $("#dexportar").attr("required",false);
            $("#separador").attr("required",false);
            
        } else {
            $("#exportarGeneral").css("display","none");
            $("#divexportar").css("display","block");
            $("#divseparador").css("display","none");
            $("#exportarGeneral2").css("display","block");
            $("#dexportar").attr("required",true);
            $("#separador").attr("required",false);
        }
    })
</script>
<script>
    $("#dexportar").change(function(){
        var exportar = $("#dexportar").val();
        if(exportar==1 || exportar==2){
            $("#divseparador").css("display","block"); 
            $("#separador").attr("required",true);
        } else {
            $("#divseparador").css("display","none");
            $("#separador").attr("required",false);
        }
    })
</script>
<script src="js/select/select2.full.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
</script>
<?php require_once 'footer.php' ?>  
<script>
    function generar(){
        var formData = new FormData($("#form")[0]);  
        jsShowWindowLoad('Generando Informe...');
        $.ajax({
            type: 'POST',
            url: "jsonPptal/consultasInformesCnt.php?action=21",
            data:formData,
            contentType: false,
            processData: false,
            success: function(response)
            {
                jsRemoveWindowLoad();
                console.log(response);
                var tipoI   = $("#tipoInforme").val();
                var tipo    = $("#tipo").val();
                var annio   = $("#sltAnnio").val();
                var sep     = $("#separador").val();
                window.open('informes/informe_estado_apertura.php?tI='+tipoI+'&a='+annio+'&t='+tipo+'&s='+sep);
            }
        });
    }
</script>
<script>
function reporteExcel(){
    $("#tipo").val('excel');
    $('form').attr('action', 'javaScript:generar()'); 
}

function reportePdf(){
    $("#tipo").val('pdf');
    $('form').attr('action', 'javaScript:generar()'); 
}
function reporteGeneral(){
    
    $("#tipo").val($("#dexportar").val());
    $('form').attr('action', 'javaScript:generar()'); 
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