<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');

?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">

<title>Registrar Políticas Contables</title>
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
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
label#nombre_politica-error, #descripcion-error, #nombre_aplicable-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;
}
</style>
</head>
<body>

 
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
         <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Registrar Políticas Contables</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form"  class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_POLITICAS_CONTABLESJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

                    <div class="form-group" style="margin-top: -15px;">
                      <label for="nombre_politica" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre Política:</label>
                      <input type="text" name="nombre_politica" id="nombre_politica" class="form-control"  maxlength="200" title="Ingrese el nombre política"  placeholder="Nombre Política" required onkeypress="return txtValida(event, 'num_car')" maxlenght="500">
                    </div>
                    <div class="form-group" style="margin-top: -15px;">
                      <label for="descripcion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                      <textarea type="text" name="descripcion" id="descripcion" onkeypress="return txtValida(event,'num_car')" maxlength="500" class="form-control col-sm-1"  title="Ingrese Descripción"  placeholder="Descripción" required="required" ></textArea>
                    </div>
                    <div class="form-group" style="margin-top: -15px;">
                      <label for="nombre_aplicable" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Norma Aplicable:</label>
                      <input type="text" name="nombre_aplicable" required="required" id="nombre_aplicable"  class="form-control col-sm-1" maxlength="500" title="Ingrese nombre aplicable"  placeholder="Nombre Aplicable" onkeypress="return txtValida(event, 'num_car')">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
        </div>
        <!--Información adicional -->
        <div class="col-sm-6 col-sm-2" style="margin-top:-22px" >
            <table class="tablaC table-condensed" style="margin-left: -3px; ">
                <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px; height:36px">Adicional</h2>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <button class="btn btnInfo btn-primary" disabled="true" >Establecimiento <br/>política NIIF<br/></button><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once 'footer.php';?>
</body>
<!-- select2 -->
  <script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>