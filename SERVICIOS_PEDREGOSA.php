<?php 
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO(); 
?>
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

<title>Subir Movimiento</title>
</head>
<body>

 
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">
    <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Subir</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
          <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
           <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo plano, separado por ;</p>
           
           <div class="form-group" style="margin-top: -10px; ">
                <label for="descripcion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
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
 <script src="js/select/select2.full.js"></script>
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
        function guardar(){
            var formData = new FormData($("#form")[0]);  
            jsShowWindowLoad('Cargando Información...');
            var form_data = { action:1 };
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gs_subir_pedregosa.php?action=1",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response);
                    var resultado = JSON.parse(response);
                    var rta = resultado["rta"];
                    var html = resultado["html"];
                    var registros = resultado["registros"];
                    if (rta > 0) {
                        $("#mensaje").html(html);
                        $("#mdlMensajes").modal("show");
                        $("#btnAceptar").click(function () {
                            $("#mdlMensajes").modal("hide");
                        });
                        $("#btnCancelar").click(function () {
                            $("#mdlMensajes").modal("hide");
                        })
                    } else {
                        $("#mensaje").html(registros + ' Registros Subidos Correctamente');
                        $("#mdlMensajes").modal("show");
                        $("#btnAceptar").click(function () {
                            document.location.reload();
                        });
                        $("#btnCancelar").click(function () {
                            $("#mdlMensajes").modal("hide");
                        })
                    }

                    
                }
            })
        }
  </script>
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
  
<?php require_once 'footer.php';?>
</body>
</html>

