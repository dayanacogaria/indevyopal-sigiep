<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
//CIUDAD
$ciud= "SELECT c.id_unico, c.nombre, d.nombre FROM gf_ciudad c LEFT JOIN gf_departamento d ON c.departamento=d.id_unico ORDER BY c.nombre ASC";
$ciudad = $mysqli->query($ciud);


//RUTA
$rut= "SELECT id_unico, nombre FROM gp_ruta ORDER BY nombre ASC";
$ruta = $mysqli->query($rut);

//TIPO PREDIO
$tipo= "SELECT id_unico, nombre FROM gp_tipo_predio ORDER BY nombre ASC";
$tipoPredio = $mysqli->query($tipo);

#ESTRATO
$estrato = "SELECT id_unico, codigo, nombre FROM gp_estrato WHERE tipo_estrato = 2 ORDER BY codigo ASC";
$estrato = $mysqli->query($estrato);

#ESTADO
$estado = "SELECT id_unico, nombre FROM gr_estado_predio ORDER BY nombre ASC";
$estado = $mysqli->query($estado);
        
#PREDIO ASOCIADO
$predio = "SELECT id_unico, codigo_catastral, nombre FROM gp_predio1";
$predio = $mysqli->query($predio);
?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<title>Registrar Predio</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/md5.pack.js"></script>
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
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
label#codigo_catastral-error, #annio-error,#direccion-error, #codigo-error, #Ciudad-error, #participacion-error,  #barrio-error,  #ruta-error, #tipoPredio-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
}
.client-form input[type="text"] {
    width: 250px;
}
body{
    font-size: 12px;
}
</style>
</head>
<body>
 
 
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
         <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Registrar Predio</h2>
            <a href="LISTAR_GP_PREDIO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Predio</h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form"  class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                    <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

                    <div class="form-group form-inline" style="margin-top: -15px;">
                        <div class="col-md-6">
                        <label for="codigo_catastral" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;">*</strong>Código Catastral:</label>
                        <input type="text" name="codigo_catastral" id="codigo_catastral" class=" col-md-2 col-sm-2 form-control"  maxlength="30" title="Ingrese el código catastral"  placeholder="Código Catastral" required>
                        </div>
                        <div class="col-md-6">
                        <label for="nombre" class="col-md-4  col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control col-md-2 col-sm-2" maxlength="100"  title="Ingrese Nombre"  placeholder="Nombre" onkeypress="return txtValida(event,'num_car')">
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: -15px;">
                        <div class="col-md-6">
                        <label for="matricula_inmobiliaria" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Matrícula Inmobiliaria:</label>
                        <input type="text" name="matricula_inmobiliaria" id="matricula_inmobiliaria" class="form-control col-md-2 col-sm-2" maxlength="30"  title="Ingrese Matrícula Inmobiliaria"  placeholder="Matrícula Inmobiliaria">
                        </div>
                        <div class="col-md-6">
                        <label for="annio" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Año Creación:</label>
                        <input type="text" name="annio" id="annio" class="form-control col-md-2 col-sm-2" maxlength="4" minlength="4"  title="Ingrese Año de Creación"  placeholder="Año Creación" onkeypress="return txtValida(event,'num')">
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: -15px;">
                        <div class="col-md-6">
                        <label for="codigo" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Código SIG:</label>
                        <input type="text" name="codigo" id="codigo"  class="form-control col-md-2 col-sm-2" maxlength="100" title="Ingrese Código SIG"  placeholder="Código SIG">
                        </div>
                        <div class="col-md-6">
                        <label for="codigoIG" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Código IGAC:</label>
                        <input type="text" name="codigoIG" id="codigoIG" onkeypress="return txtValida(event,'num_car')" maxlength="50" class="form-control col-md-2 col-sm-2"  title="Ingrese Codigo IGAC"  placeholder="Código IGAC">
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: -15px;">   
                        <div class="col-md-6">
                        <label for="direccion" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;">*</strong>Dirección:</label>
                        <input type="text" name="direccion" id="direccion" onkeypress="return txtValida(event,'direccion')" maxlength="100" class="form-control col-md-2 col-sm-2"  title="Ingrese Dirección"  placeholder="Dirección" required="required" >
                        </div>
                        <div class="col-md-6">
                        <label for="Ciudad" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;">*</strong>Ciudad:</label>
                        <select name="Ciudad" id="Ciudad" required="required" style="width: 250px" class="form-control select2_single " title="Seleccione Ciudad" required="required">
                            <option value="">Ciudad</option>
                            <?php while($row = mysqli_fetch_row($ciudad)){?>
                            <option value="<?php echo $row[0] ?>"><?php echo ucwords((mb_strtolower($row[1].' - '.$row[2])));}?></option>;
                        </select> 
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: -15px;">
                        <div class="col-md-6">
                        <label for="estrato" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Estrato:</label>
                        <select name="estrato" id="estrato"  style="width: 250px" class="form-control select2_single" title="Seleccione Estrato">
                            <option value="">Estrato</option>
                            <?php while($rowE = mysqli_fetch_assoc($estrato)){?>
                            <option value="<?php echo $rowE['id_unico'] ?>"><?php echo $rowE['codigo'].' - '.ucwords((mb_strtolower($rowE['nombre'])));}?></option>;
                        </select>
                        </div>
                        <div class="col-md-6">
                        <label for="barrio" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Barrio:</label>
                        <select name="barrio" id="barrio"  style="width: 250px" class="form-control select2_single  col-md-2 col-sm-2" title="Seleccione Barrio">
                            <option value="">Barrio</option>
                        </select> 
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: 0px;">
                        <div class="col-md-6">
                        <label for="estado" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Estado:</label>
                        <select name="estado" id="estado"  style="width: 250px" class="form-control select2_single" title="Seleccione Estado" >
                            <option value="">Estado</option>
                            <?php while($row = mysqli_fetch_assoc($estado)){?>
                            <option value="<?php echo $row['id_unico'] ?>"><?php echo ucwords((strtolower($row['nombre'])));}?></option>;
                        </select> 
                        </div>
                        <div class="col-md-6">
                        <label for="ruta" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Ruta:</label>
                        <select name="ruta" id="ruta"  style="width: 250px" class="form-control select2_single" title="Seleccione Ruta">
                            <option value="">Ruta</option>
                            <?php while($row = mysqli_fetch_assoc($ruta)){?>
                            <option value="<?php echo $row['id_unico'] ?>"><?php echo ucwords((mb_strtolower($row['nombre'])));}?></option>;
                        </select> 
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: 0px;">
                        <div class="col-md-6">
                        <label for="tipoPredio" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Tipo Predio:</label>
                        <select name="tipoPredio" id="tipoPredio"  style="width: 250px" class="form-control select2_single" title="Seleccione Tipo Predio" >
                            <option value="">Tipo Predio</option>
                            <?php while($row = mysqli_fetch_assoc($tipoPredio)){?>
                            <option value="<?php echo $row['id_unico'] ?>"><?php echo ucwords((strtolower($row['nombre'])));}?></option>;
                        </select> 
                        </div>
                        <div class="col-md-6">
                        <label for="predioa" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Predio Asociado:</label>
                        <select name="predioa" id="predioa"  style="width: 250px" class="form-control select2_single col-md-2 col-sm-2" title="Seleccione Predio Asociado">
                            <option value="">Predio Asociado</option>
                            <?php while($row = mysqli_fetch_row($predio)){?>
                            <option value="<?php echo $row[0] ?>"><?php echo ucwords((mb_strtolower($row[1].' - '.$row[2])));}?></option>;
                        </select> 
                        </div>
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
                            <button class="btn btnInfo btn-primary" disabled="true" >Tercero</button><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once 'footer.php';?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/select/select2.full.js"></script>
<div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#Ciudad").change(function(){
            var form_data = { action:4, ciudad:$("#Ciudad").val() };
            $.ajax({
                type: "POST",
                url: "jsonServicios/gp_BarrioJson.php",
                data: form_data,
                success: function(response)
                { 
                    $("#barrio").html(response);
                }   
            }); 

        })
        function guardar(){
            var formData = new FormData($("#form")[0]);
            jsShowWindowLoad('Guardando..');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_PredioJson.php?action=2",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    jsRemoveWindowLoad();
                    console.log(data);
                    if (data ==true) {
                        $("#mensaje").html("Información Guardada Correctamente");
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location ='LISTAR_GP_PREDIO.php';
                        })
                    } else {
                        $("#mensaje").html("No Se Ha Podido Guardar La Información");
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            $("#mdlMensajes").modal("hide");
                        })
                    }
                }
            })
        }
    </script>
</body>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
  