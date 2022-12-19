<?php
#####################################################################################################
#       ******************************      MODIFICACIONES      ******************************      # 
#####################################################################################################
#23/07/2018 | ERICA G. | ARCHIVO CREADO 
#####################################################################################################
require_once 'head.php';
require_once('Conexion/conexion.php');
$titulo ="";
$titulo2 ="";
if(empty($_GET['id'])){
    $titulo = "Registrar Vigencias Interfaz Reteica";
    $titulo2 =".";
} else {
    $titulo = "Modificar Vigencias Interfaz Reteica";
    $sql = "SELECT id_unico, nombre, valor, vigencias_anteriores "
            . "FROM gf_vigencias_interfaz_reteica "
            . "WHERE md5(id_unico)='".$_GET['id']."'";
    $sql = $mysqli->query($sql);
    $row = mysqli_fetch_row($sql);
    $titulo2 =$row[1];
}

?>
<title><?php echo $titulo;?></title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #nombre-error, #valor-error {
    display: block;
    color: #bd081c;
    font-weight: bold;
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
    <a href="LISTAR_GF_VIGENCIAS_COMERCIO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
      <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
          <?php if(empty($_GET['id'])) { ?>
          <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <!--Ingresa la información-->
            <div class="form-group">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control"  title="Ingrese el nombre "  placeholder="Nombre" required onkeypress="return txtValida(event, 'num_car')" maxlenght="500">
            </div>
            <div class="form-group">
              <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label>
              <input type="text" name="valor" id="valor" class="form-control"  title="Ingrese el valor "  placeholder="Valor" required onkeypress="return txtValida(event, 'num_car')" maxlenght="500">
            </div>
            <div class="form-group">
              <label for="vigencias_anteriores" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Vigencias Anteriores:</label>
              <input type="radio" name="vigencias_anteriores" id="vigencias_anteriores" value="1">Sí
              <input type="radio" name="vigencias_anteriores" id="vigencias_anteriores" value="2" checked>No
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
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control"  title="Ingrese el nombre "  placeholder="Nombre" required onkeypress="return txtValida(event, 'num_car')" maxlenght="500" value="<?php echo $row[1]?>">
            </div>
            <div class="form-group">
              <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label>
              <input type="text" name="valor" id="valor" class="form-control"  title="Ingrese el valor "  placeholder="Valor" required onkeypress="return txtValida(event, 'num_car')" maxlenght="500" value="<?php echo $row[2]?>">
            </div>
            <div class="form-group">
              <label for="vigencias_anteriores" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Vigencias Anteriores:</label>
              <?php if($row[3]==1) { ?>
              <input type="radio" name="vigencias_anteriores" id="vigencias_anteriores" value="1" checked>Sí
              <input type="radio" name="vigencias_anteriores" id="vigencias_anteriores" value="2" >No
              <?php }  else  { ?>
              <input type="radio" name="vigencias_anteriores" id="vigencias_anteriores" value="1">Sí
              <input type="radio" name="vigencias_anteriores" id="vigencias_anteriores" value="2" checked>No
              <?php }?> 
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
        jsShowWindowLoad('Guardando Información...');
        var va = $('input:radio[name=vigencias_anteriores]:checked').val();
        var form_data = { action: 12, nombre:$("#nombre").val(), valor:$("#valor").val(), vigencias_anteriores : va};
        $.ajax({
          type: "POST",
          url: "jsonPptal/gf_interfaz_ComercioJson.php",
          data: form_data,
          success: function(response)
          { 
              jsRemoveWindowLoad();
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
        jsShowWindowLoad('Modificando Información...');
        var va = $('input:radio[name=vigencias_anteriores]:checked').val();
        var form_data = { action: 13, id:$("#id").val(),nombre:$("#nombre").val(), valor:$("#valor").val(), vigencias_anteriores : va};
        
            $.ajax({
              type: "POST",
              url: "jsonPptal/gf_interfaz_ComercioJson.php",
              data: form_data,
              success: function(response)
              { 
                jsRemoveWindowLoad();  
                console.log(response);
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
       window.location="LISTAR_GF_VIGENCIAS_RETEICA.php";
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




