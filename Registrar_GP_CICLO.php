<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
//Unidad vivienda Inicial
$unidad_vivi = "SELECT uv.id_unico, p.codigo_catastral FROM gp_unidad_vivienda uv LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico ORDER BY p.codigo_catastral ASC";
$unidadVI = $mysqli->query($unidad_vivi);

//Unidad vivienda final
$unidad_vivf = "SELECT uv.id_unico, p.codigo_catastral FROM gp_unidad_vivienda uv LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico ORDER BY p.codigo_catastral DESC";
$unidadVF = $mysqli->query($unidad_vivf);

//Estado facturación
$estado_f= "SELECT id_unico, nombre FROM gp_estado_facturacion ORDER BY nombre ASC";
$estadoF = $mysqli->query($estado_f);

//Formato factura
$formato= "SELECT id_unico, nombre FROM gf_formato ORDER BY nombre ASC";
$formatoF = $mysqli->query($formato);

?>


<title>Registrar Ciclo</title>
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
label#UnidadViviendaInicial-error, #nombre-error, #UnidadViviendaFinal-error,  #FormatoFactura-error, #EstadoFacturacion-error, #estado-error{
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
     }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
</head>
<body>

 
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Ciclo</h2>
            <a href="LISTAR_GP_CICLO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Ciclo</h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GP_CICLOJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group" style="margin-top: -10px;">
                      <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" onkeypress="return txtValida(event,'car')" title="Ingrese el nombre"  placeholder="Nombre" required>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="UnidadViviendaInicial" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Unidad Vivienda Inicial:</label>
                        <select name="UnidadViviendaInicial" id="UnidadViviendaInicial"  class="select2_single form-control col-sm-1" title="Seleccione Unidad Vivienda Inicial" required="required" >
                            <option value="">Unidad Vivienda Inicial</option>
                            <?php while($row1 = mysqli_fetch_row($unidadVI)){?>
                            <option value="<?php echo $row1[0] ?>"><?php echo ucwords((mb_strtolower($row1[1])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="UnidadViviendaFinal" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Unidad Vivienda Final:</label>
                        <select name="UnidadViviendaFinal" id="UnidadViviendaFinal"  class="select2_single form-control col-sm-1" title="Seleccione Unidad Vivienda Final" required="required">
                            <option value="">Unidad Vivienda Final</option>
                            <?php while($row2 = mysqli_fetch_row($unidadVF)){?>
                            <option value="<?php echo $row2[0] ?>"><?php echo ucwords((mb_strtolower($row2[1])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="EstadoFacturacion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado Facturación:</label>
                        <select name="EstadoFacturacion" id="EstadoFacturacion"  class="select2_single form-control col-sm-1" title="Seleccione Estado Facturación" required="required" >
                            <option value="">Estado Facturación</option>
                            <?php while($row3= mysqli_fetch_assoc($estadoF)){?>
                            <option value="<?php echo $row3['id_unico'] ?>"><?php echo ucwords((mb_strtolower($row3['nombre'])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="FormatoFactura" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Formato Factura:</label>
                        <select name="FormatoFactura" id="FormatoFactura"  class="select2_single form-control col-sm-1" title="Seleccione Formato Factura" required="required" >
                            <option value="">Formato Factura</option>
                            <?php while($row4 = mysqli_fetch_assoc($formatoF)){?>
                            <option value="<?php echo $row4['id_unico'] ?>"><?php echo ucwords((mb_strtolower($row4['nombre'])));}?></option>;
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

