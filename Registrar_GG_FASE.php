<?php 
require_once('Conexion/conexion.php');
require_once 'head.php';
//Elemento Flujo
$elemento = "SELECT id_unico, nombre FROM gg_elemento_flujo ORDER BY nombre ASC";
$elemento = $mysqli->query($elemento);
?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">

<title>Registrar Fase</title>
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
label#nombre-error, #elemento-error{
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
          <div class="col-sm-10 text-left">
                <!--Titulo del formulario-->
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Fase</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form"  class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/registrar_GG_FASEJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

                    <div class="form-group" style="margin-top: -15px;">
                      <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control"  maxlength="100" onkeypress="return txtValida(event,'car')" title="Ingrese el nombre"  placeholder="Nombre" required>
                    </div>
                    <div class="form-group" style="margin-top: -15px;">
                        <label for="observaciones" class="col-sm-5 control-label" style="margin-top:5px">Observaciones:</label>
                      <textarea type="text" name="observaciones" id="observaciones" class="form-control"  maxlength="500" onkeypress="return txtValida(event)" title="Ingrese observaciones"  placeholder="Observaciones"></textarea>
                    </div>
                    <div class="form-group" style="margin-top: 5px;">
                        <input type="hidden" name="elemento" id="elemento" required="required" title="Seleccione elemento flujo">
                        <label for="elemento" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Elemento Flujo:</label>
                        <select name="elemento1" id="elemento1" required="required" style="margin-left: 10px; margin-right: 10px;"   class="select2_single form-control col-sm-1" title="Seleccione elemento flujo" required="required" onchange="llenar();">
                            <option value="">Elemento Flujo</option>
                            <?php while($row = mysqli_fetch_row($elemento)){?>
                            <option value="<?php echo $row[0] ?>"><?php echo ucwords((strtolower($row[1])));}?></option>;
                        </select> 
                    </div>

                    <div class="form-group" style="margin-top: 30px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
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
<script>
  function llenar(){
      var elemento = document.getElementById('elemento1').value;
      document.getElementById('elemento').value= elemento;
  }
  </script>