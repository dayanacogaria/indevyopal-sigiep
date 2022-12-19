<?php  

require_once('Conexion/conexion.php');
require_once('head_listar.php');
$anno = $_SESSION['anno'];?>
<!--Titulo de la página-->
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #file-error {
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;
}
label #sltPeriodo-error{
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
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>

   <style>
    .form-control {font-size: 12px;}
    
</style>

<title>RESUMEN ASISTENCIA</title>
</head>
<body>

 
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">
    <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Cargar Archivo Excel Resumen Asistencia</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_ARCHIVO_EXCEL_ASISTENCIAJson.php">
          <?php
             $per = "SELECT  id_unico, codigointerno FROM gn_periodo
                     WHERE   id_unico != 1 
                     AND tipoprocesonomina = 1 
                     AND   parametrizacionanno = '$anno'";
             $periodo = $mysqli->query($per);
          ?>
          <div class="form-group" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px;">
                            <label for="sltPeriodo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo:</label>
                            <select  name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="height: 30px;" class="select2_single form-control" required="required" >
                                    <option value="">Periodo</option>
                                    <?php
                                    while($rowE = mysqli_fetch_row($periodo)){
                                        echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                    }
                                    ?>
                            </select>
            </div>
           <div class="form-group"  style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; ">
                <label for="descripcion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
            </div>
            <div class="form-group" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px;">
              <label for="no" class="col-sm-5 control-label"></label>
               <button type="submit" onclick="alertaArchivo();" id="btnRequerido" class="btn btn-primary sombra" style=" margin-top: -10px; margin-left: 120px;" data-dismiss="modal" ><li class="glyphicon glyphicon-cloud-upload"></li></button>
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
 <script src="js/select/select2.full.js"></script>
 <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>  
  <script>
  function alertaArchivo(){
       var imgVal = $('#file').val(); 
       if(imgVal=='') 
        { 
            $('#modalRequerido').modal('show');
            $('#btnRequerido').click(function(){
                $('#modalRequerido').modal('hide');
            });

           
        } else { 
          jsShowWindowLoad();
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
$("#sltPeriodo").select2();
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

