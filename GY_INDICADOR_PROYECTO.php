<?php 
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$annio = $_SESSION['anno'];
$compania = $_SESSION['compania'];
?>
<title>Indicador Por Proyecto</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #sltpryi-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
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
                <h2 align="center" class="tituloform">Indicadores Por Proyecto</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltpryi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Proyecto:</label>
                            <select required name="sltpryi" id="sltpryi" style="height: auto" class="select2_single form-control" title="Seleccione Proyecto">
                                <option value="">Proyecto</option>
                            </select>
                        </div>
                        <div class="col-sm-10" style="margin-top:10px;margin-left:650px" >
                            <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                        </div>
                    </div>
                </form>
            </div>    
        </div>
    </div>
</div>
<script>    
    $().ready(function () {
       var form_data={action: 26, annio :1, orden:'ASC'};
       var optionPI ="<option value=''>Proyecto</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionPI =optionPI+response;
              $("#sltpryi").html(optionPI).focus();              
          }
       });
       
    });
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
function reportePdf(){
    $('form').attr('action', 'informesProyecto/INF_GY_INDICADORES_PROYECTO.php');
}
</script>
</body>
</html>