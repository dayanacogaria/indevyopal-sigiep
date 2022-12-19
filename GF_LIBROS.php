<?php
#####################################################################################################################################################################
#                                               MODIFICACIONES
######################################################################################################################################################################
#28/09/2017 | ERICA G. | ARCHIVO CREADO 
######################################################################################################################################################################
require_once 'head.php';
require_once('Conexion/conexion.php');
$titulo ="";
$titulo2 ="";
if(empty($_GET['id'])){
    $titulo = "Registrar Libros";
    $titulo2 =".";
} else {
    $titulo = "Modificar Libros";
    $sql = "SELECT id_unico, codigo_libro, nombre_libro "
            . "FROM gf_libros "
            . "WHERE md5(id_unico)='".$_GET['id']."'";
    $sql = $mysqli->query($sql);
    $row = mysqli_fetch_row($sql);
    $titulo2 =$row[1].' - '.$row[2];
}

?>
<title><?php echo $titulo;?></title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #nombre-error, #codigo-error {
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
</head>
<body> 
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">
    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo;?></h2>
    <a href="LISTAR_GF_LIBROS.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
      <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
          <?php if(empty($_GET['id'])) { ?>
          <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <!--Ingresa la información-->
            <div class="form-group">
              <label for="codigo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código:</label>
              <input type="text" name="codigo" id="codigo" class="form-control"  title="Ingrese el Código "  placeholder="Código" required onkeypress="return txtValida(event, 'num_car')" maxlenght="500">
            </div>
            <div class="form-group">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control"  title="Ingrese el nombre "  placeholder="Nombre" required onkeypress="return txtValida(event, 'num_car')" maxlenght="500">
            </div>
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert" >
          </form>
          <?php } else { ?>
          <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <!--Ingresa la información-->
            <input type="hidden" name="id" id="id" value="<?php echo $row[0];?>">
            <div class="form-group">
              <label for="codigo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código:</label>
              <input type="text" name="codigo" id="codigo" class="form-control"  title="Ingrese el Código "  placeholder="Código" required onkeypress="return txtValida(event, 'num_car')" maxlenght="500" value="<?php echo $row[1]?>">
            </div>
            <div class="form-group">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control"  title="Ingrese el nombre "  placeholder="Nombre" required onkeypress="return txtValida(event, 'num_car')" maxlenght="500" value="<?php echo $row[2]?>">
            </div>
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert" >
          </form>
          <?php } ?>
        </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">

          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <label id="mensaje" name="mensaje"></label>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
</div>
<script>
    function registrar(){
        var form_data = { action: 1, nombre:$("#nombre").val(), codigo:$("#codigo").val()};
        $.ajax({
          type: "POST",
          url: "jsonPptal/gf_LibrosJson.php",
          data: form_data,
          success: function(response)
          { 
              if(response ==true){
                 $("#mensaje").html('Información Guardada Correctamente');  
                 $("#myModal").modal('show'); 
              } else {
                   $("#myModal").modal('show');
                  $("#mensaje").html('No se ha podido guardar la información');    

              }
          }//Fin succes.
        }); 
    }
</script> 
<script>
    function modificar(){
        var form_data = { action: 2, id:$("#id").val(),nombre:$("#nombre").val(), codigo:$("#codigo").val()};
            $.ajax({
              type: "POST",
              url: "jsonPptal/gf_LibrosJson.php",
              data: form_data,
              success: function(response)
              { 
                if(response ==true){
                    $("#mensaje").html('Información Modificada Correctamente');  
                    $("#myModal").modal('show'); 
                 } else {
                      $("#myModal").modal('show');
                     $("#mensaje").html('No se ha podido modificar la información');    

                 }
              }//Fin succes.
            }); 
    }
</script> 
<script>
    $("#aceptar").click(function(){
       window.location="LISTAR_GF_LIBROS.php";
    });
</script>
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
</html>



