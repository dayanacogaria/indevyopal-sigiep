<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');

#PARAMETRIZACION
$parm= "SELECT "
        . "p.id_unico, "
        . "p.anno, "
        . "t.razonsocial "
        . "FROM gf_parametrizacion_anno p "
        . "LEFT JOIN gf_tercero t ON p.compania= t.id_unico "
        . "ORDER BY p.anno ASC ";
$parm = $mysqli->query($parm);
?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">

<title>Registrar Tipo Equivalencia</title>
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
label#nombre-error, #descripcion-error, #parametrizacion-error{
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
         <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Registrar Tipo Equivalencia PUC</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form"  class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_TIPO_EQUIVALENCIAJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

                    <div class="form-group" style="margin-top: -15px;">
                      <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                      <input type="text" name="nombre" id="nombre" class="form-control"  maxlength="30" title="Ingrese el nombre "  placeholder="Nombre" required onkeypress="return txtValida(event, 'num_car')" maxlenght="500">
                    </div>
                    <div class="form-group" style="margin-top: -15px;">
                      <label for="descripcion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                      <textarea type="text" name="descripcion" id="descripcion" onkeypress="return txtValida(event,'num_car')" maxlength="500" class="form-control col-sm-1"  title="Ingrese Descripción"  placeholder="Descripción" required="required" ></textArea>
                    </div>
                    <div class="form-group" style="margin-top: -15px;">
                      <label for="parametrizacion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año - Compañía:</label>
                      <select name="parametrizacion" id="parametrizacion" class="select2_single form-control col-sm-1" title="Seleccione " required  >
                        <option value="">Año - Compañía</option>
                        <?php while($par = mysqli_fetch_row($parm)){?>
                        <option value="<?php echo $par[0] ?>"><?php echo strtoupper($par[1]).' - '.ucwords(strtolower($par[2]));}?></option>;
                    </select>
                    </div>
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-bottom: 10px; margin-left:0px">Guardar</button>
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