<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
$id = " ";
$queryCond="";
if (isset($_GET["id"])){ 
  $id = (($_GET["id"]));
$queryCond = "SELECT c.id_unico,c.unidad_vivienda_inicial, c.unidad_vivienda_final, ef.id_unico, f.id_unico, 
            c.nombre, pi.codigo_catastral, pf.codigo_catastral, ef.nombre, f.nombre, c.unidad_vivienda_inicial, c.unidad_vivienda_final FROM gp_ciclo c 
            LEFT JOIN gp_unidad_vivienda uvi ON c.unidad_vivienda_inicial = uvi.id_unico
            LEFT JOIN gp_unidad_vivienda uvf ON c.unidad_vivienda_final = uvf.id_unico
            LEFT JOIN gp_predio1 pi ON uvi.predio = pi.id_unico
            LEFT JOIN gp_predio1 pf ON uvf.predio = pf.id_unico
            LEFT JOIN gp_estado_facturacion ef ON c.estado_facturacion = ef.id_unico
            LEFT JOIN gf_formato f ON f.id_unico = c.formato_factura 
            WHERE md5(c.id_unico)='$id'"; 
$resul = $mysqli->query($queryCond);
$row = mysqli_fetch_row($resul);

//Unidad vivienda Inicial

$unidad_vivi = "SELECT uv.id_unico, p.codigo_catastral FROM gp_unidad_vivienda uv "
        . "LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico WHERE uv.id_unico != $row[10] ORDER BY p.codigo_catastral ASC";
$unidadVI = $mysqli->query($unidad_vivi);

//Unidad vivienda final
$unidad_vivf = "SELECT uv.id_unico, p.codigo_catastral FROM gp_unidad_vivienda uv "
        . "LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico WHERE uv.id_unico != $row[11] ORDER BY p.codigo_catastral DESC";
$unidadVF = $mysqli->query($unidad_vivf);

//Estado facturación
$estado_f= "SELECT id_unico, nombre FROM gp_estado_facturacion WHERE id_unico != $row[3] ORDER BY nombre ASC";
$estadoF = $mysqli->query($estado_f);

//Formato factura
$formato= "SELECT id_unico, nombre FROM gf_formato WHERE id_unico != $row[4] ORDER BY nombre ASC";
$formatoF = $mysqli->query($formato);


}

?>

<title>Modificar Ciclo</title>
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
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Ciclo</h2>
            <a href="LISTAR_GP_CICLO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px"><?PHP ECHO $row[5]?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GP_CICLOJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" value="<?php echo ucwords(mb_strtolower($row[5])); ?>" required>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="UnidadViviendaInicial" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Unidad Vivienda Inicial:</label>
                        <select name="UnidadViviendaInicial" id="UnidadViviendaInicial"  class="select2_single form-control col-sm-1" title="Seleccione Unidad Vivienda Inicial" required="required">
                            <?php   
                            if (empty($row[1])) {
                                echo '<option value=""> - </option>';
                                $unidad_vivi = "SELECT uv.id_unico, p.codigo_catastral FROM gp_unidad_vivienda uv "
                                            . "LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico ORDER BY p.codigo_catastral ASC";
                                $unidadVI = $mysqli->query($unidad_vivi);
                                 while($row1 = mysqli_fetch_row($unidadVI)){?>
                                <option value="<?php echo $row1[0] ?>"><?php echo ucwords((mb_strtolower($row1[1])));}?></option>;
                            <?php } else { ?>
                                <option value="<?php echo $row[1] ?>"><?php echo ucwords(mb_strtolower($row[6]));?></option>
                                 <?php while($row1 = mysqli_fetch_row($unidadVI)) { ?>
                                <option value="<?php echo $row1[0] ?>"> <?php echo ucwords((mb_strtolower($row1[1])));}?></option>;
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="UnidadViviendaFinal" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Unidad Vivienda Final:</label>
                        <select name="UnidadViviendaFinal" id="UnidadViviendaFinal"  class="select2_single form-control col-sm-1" title="Seleccione Unidad Vivienda Final" required="required">
                            <?php   
                            if (empty($row[2])) {
                                echo '<option value=""> - </option>';
                                echo $unidad_vivf = "SELECT uv.id_unico, p.codigo_catastral FROM gp_unidad_vivienda uv "
                                            . "LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico ORDER BY p.codigo_catastral DESC";
                                $unidadVF = $mysqli->query($unidad_vivf);
                                 while($row2 = mysqli_fetch_row($unidadVF)){?>
                                <option value="<?php echo $row2[0] ?>"><?php echo ucwords((mb_strtolower($row2[1]))); } ?></option>;
                            <?php } else { ?>
                                <option value="<?php echo $row[2] ?>"><?php echo ucwords(mb_strtolower($row[7]));?></option>
                                 <?php while($row2 = mysqli_fetch_row($unidadVF)) { ?>
                                <option value="<?php echo $row2[0] ?>"> <?php echo ucwords((mb_strtolower($row2[1])));}?></option>;
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="EstadoFacturacion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado Facturación:</label>
                        <select name="EstadoFacturacion" id="EstadoFacturacion"  class="select2_single form-control col-sm-1" title="Seleccione Estado Facturación" required="required" >
                            <?php   
                            if (empty($row[3])) {
                                echo '<option value=""> - </option>';
                                $estado_f= "SELECT id_unico, nombre FROM gp_estado_facturacion ORDER BY nombre ASC";
                                $estadoF = $mysqli->query($estado_f);
                                 while($row3 = mysqli_fetch_row($estadoF)){?>
                                <option value="<?php echo $row3[0] ?>"><?php echo ucwords((mb_strtolower($row3[1]))); } ?></option>;
                            <?php } else { ?>
                                <option value="<?php echo $row[3] ?>"><?php echo ucwords(mb_strtolower($row[8]));?></option>
                                 <?php while($row3 = mysqli_fetch_row($estadoF)) { ?>
                                <option value="<?php echo $row3[0] ?>"> <?php echo ucwords((mb_strtolower($row3[1])));}?></option>;
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="FormatoFactura" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Formato Factura:</label>
                        <select name="FormatoFactura" id="FormatoFactura"  class="select2_single form-control col-sm-1" title="Seleccione Formato Factura" required="required" >
                            <?php   
                            if (empty($row[4])) {
                                echo '<option value=""> - </option>';
                                $formato= "SELECT id_unico, nombre FROM gf_formato ORDER BY nombre ASC";
                                $formatoF = $mysqli->query($formato);
                                 while($row4 = mysqli_fetch_row($formatoF)){?>
                                <option value="<?php echo $row4[0] ?>"><?php echo ucwords((mb_strtolower($row4[1]))); } ?></option>;
                            <?php } else { ?>
                                <option value="<?php echo $row[4] ?>"><?php echo ucwords(mb_strtolower($row[9]));?></option>
                                 <?php while($row4 = mysqli_fetch_row($formatoF)) { ?>
                                <option value="<?php echo $row4[0] ?>"> <?php echo ucwords((mb_strtolower($row4[1])));}?></option>;
                            <?php } ?>
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
<?php  require_once 'footer.php';?>
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
