<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
//MARCA
$mar= "SELECT id_unico, nombre FROM gp_marca ORDER BY nombre ASC";
$marca = $mysqli->query($mar);

// TIPO MEDIDOR
$tipM = "SELECT id_unico, nombre FROM gp_tipo_medidor ORDER BY nombre ASC";
$tipoMedidor = $mysqli->query($tipM);

// MACROMEDIDOR
$macr = "SELECT id_unico, referencia FROM gp_medidor WHERE es_macromedidor ='1' ORDER BY referencia ASC";
$macromedidor = $mysqli->query($macr);

// posición medidor
$posm="SELECT id_unico, nombre FROM gp_posicion_medidor ORDER BY nombre ASC";
$posicionm= $mysqli->query($posm);
// ESTADO
$est="SELECT id_unico, nombre FROM gp_estado_medidor ORDER BY nombre ASC";
$estado= $mysqli->query($est);
?>

<title>Registrar Medidor</title>
</head>
<body>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script src="dist/jquery.validate.js"></script>
<link href="css/select/select2.min.css" rel="stylesheet">
<style>
.cmxform fieldset p label span.error { color: red;display: block; }
form.cmxform { width: 30em; display: block;}
form.cmxform label {
  width: auto;
  display: block;
  float: none;
}
label#referencia-error, #marca-error, #digitos-error,  #tipoMedidor-error, #posicionm-error, #estado-error{
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
    },
    rules: {
        param: {
          required: true
        },
        mes: {
          required: true
        },
        sltAnnio: {
          required: true
        }
     }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -22px; ">
            <h2 class="tituloform" align="center" >Registrar Medidor</h2>
            <a href="LISTAR_GP_MEDIDOR.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Medidor</h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GP_MEDIDORJson.php">
                <p align="center" style="margin-bottom: 20px; margin-top: 10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group" style="margin-top: -15px;">
                        <label for="referencia" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Referencia:</label>
                        <input class="form-control" maxlength="100" onkeypress="return txtValida(event,'num_car')" type="text" name="referencia" id="referencia"  title="Ingrese referencia" placeholder="Referencia" required="required" >
                    </div>
                    <div class="form-group" style="margin-top: -15px;">
                        <label for="marca" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Marca:</label>
                        <select name="marca" id="marca"  class="select2_single form-control col-sm-1" title="Seleccione marca" >
                            <option value="">Marca</option>
                            <?php while($rowmarca = mysqli_fetch_row($marca)){?>
                            <option value="<?php echo $rowmarca[0] ?>"><?php echo ucwords((mb_strtolower($rowmarca[1])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="digitos" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Número Dígitos:</label>
                        <input class="form-control" maxlength="2" onkeypress="return txtValida(event,'num')" type="text" name="digitos" id="digitos" title="Ingrese número de dígitos" placeholder="Número Dígitos"  >
                    </div>
                    <div class="form-group" style="margin-top: -15px;">
                        <label for="macromedidor" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Macromedidor:</label>
                        <select name="macromedidor" id="macromedidor"  class="select2_single form-control col-sm-1" title="Seleccione macromedidor">
                            <option value="">Macromedidor</option>
                            <?php while($rowmacro = mysqli_fetch_row($macromedidor)){?>
                            <option value="<?php echo $rowmacro[0] ?>"><?php echo ucwords((mb_strtolower($rowmacro[1])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="es_macromedidor" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;">*</strong>¿Es macromedidor?:</label>
                        <input  type="radio" name="es_macromedidor" id="es_macromedidor"  value="1" >SI
                        <input  type="radio" name="es_macromedidor" id="es_macromedidor" value="2" checked>NO
                    </div>
                    <div class="form-group" >
                        <label for="posicionm" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Posición Medidor:</label>
                        <select name="posicionm" id="posicionm"  class="select2_single form-control col-sm-1" title="Seleccione posición medidor" >
                            <option value="">Posición Medidor</option>
                            <?php while($rowPmedidor = mysqli_fetch_row($posicionm)){?>
                            <option value="<?php echo $rowPmedidor[0] ?>"><?php echo ucwords((mb_strtolower($rowPmedidor[1])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="certCal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Certificado Calibración:</label>
                        <input class="form-control col-sm-1" type="text" name="certCal" id="certCal" style="width:300px;" title="Ingrese certificado de calibración"  placeholder="Certificado Calibración" onkeypress="return txtValida(event,'sin_espcio')" maxlength="100"> 
                    </div>
                    <div class="form-group" style="margin-top: -15px;">
                        <label for="tipoMedidor" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Medidor:</label>
                        <select name="tipoMedidor" id="tipoMedidor"  class="select2_single form-control col-sm-1" title="Seleccione tipo medidor" >
                            <option value="">Tipo Medidor</option>
                            <?php while($rowmedidor = mysqli_fetch_row($tipoMedidor)){?>
                            <option value="<?php echo $rowmedidor[0] ?>"><?php echo ucwords((mb_strtolower($rowmedidor[1])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="estado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado Medidor:</label>
                        <select required name="estado" id="estado"  class="select2_single form-control col-sm-1" title="Seleccione Estado Medidor" required="required">
                            <option  value="">Estado Medidor</option>
                            <?php while($rowestado = mysqli_fetch_row($estado)){?>
                            <option value="<?php echo $rowestado[0] ?>"><?php echo ucwords((mb_strtolower($rowestado[1])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
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
</body>

