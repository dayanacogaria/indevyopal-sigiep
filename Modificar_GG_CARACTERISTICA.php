<?php 
require_once('Conexion/conexion.php');
require_once 'head.php';
//REGISTRO A MODIFICAR
$id=$_GET['id'];
$result = "SELECT c.id_unico, c.nombre, c.tipo_dato, td.nombre, c.unidad, uf.nombre "
        . "FROM gg_caracteristica c "
        . "LEFT JOIN gf_tipo_dato td ON c.tipo_dato = td.id_unico "
        . "LEFT JOIN gf_unidad_factor uf ON c.unidad= uf.id_unico "
        . "WHERE md5(c.id_unico)= '$id'"; 
$result = $mysqli->query($result);
$rowC = mysqli_fetch_row($result);
//Tipo Dato
$tipoD = "SELECT id_unico, nombre FROM gf_tipo_dato WHERE id_unico != '$rowC[2]' ORDER BY nombre ASC";
$tipoD = $mysqli->query($tipoD);

#unidad
$unidad = "SELECT id_unico, nombre FROM gf_unidad_factor WHERE id_unico !='$rowC[4]' ORDER BY nombre ASC";
$unidad = $mysqli->query($unidad);
?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">

<title>Modificar Característica</title>
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
<script>
$(function(){
        var combo = document.getElementById("tipo1");
      var valorA = combo.options[combo.selectedIndex].text
      var fi = document.getElementById("unidad");
      if(valorA =='Numerico' || valorA=='Numérico' || valorA=='numerico'){
          
            fi.disabled=false;
      } else {
          fi.value='';
          fi.disabled=true;
      }
});
</script>
<style>
label#nombre-error, #tipo-error{
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
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Característica</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form"  class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/modificar_GG_CARACTERISTICAJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="id" id="id" value="<?php echo $rowC[0];?>">
                    <div class="form-group" style="margin-top: -15px;">
                      <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                      <input type="text" name="nombre" id="nombre" class="form-control"  maxlength="100" onkeypress="return txtValida(event,'car')" title="Ingrese el nombre"  placeholder="Nombre" required value="<?php echo ucwords(strtolower($rowC[1]));?>">
                    </div>
                    <div class="form-group" style="margin-top: 5px;">
                        <input type="hidden" name="tipo" id="tipo" required="required" title="Seleccione tipo dato" value="<?php echo $rowC[2];?>">
                        <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Dato:</label>
                        <select name="tipo1" id="tipo1" required="required" style="margin-left: 10px; margin-right: 10px;"   class="select2_single form-control col-sm-1" title="Seleccione tipo dato" required="required" onchange="llenar();">
                            <option value="<?php echo $rowC[2];?>"><?php echo ucwords(strtolower($rowC[3]));?></option>
                            <?php while($row = mysqli_fetch_row($tipoD)){?>
                            <option value="<?php echo $row[0] ?>"><?php echo ucwords((strtolower($row[1])));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: 20px;">
                      <label for="unidad" class="col-sm-5 control-label">Unidad:</label>
                      <select name="unidad" id="unidad" style="margin-left: 10px; margin-right: 10px;"   class="select2_single form-control col-sm-1" title="Seleccione unidad" disabled="true">
                          <?php if(empty($rowC[4])){?>
                            <option value="">-</option>
                            <?php while($rowu = mysqli_fetch_row($unidad)){?>
                            <option value="<?php echo $rowu[0] ?>"><?php echo ucwords((strtolower($rowu[1])));}?></option>
                          <?php } else { ?>
                            <option value="<?php echo $rowC[4]?>"><?php echo ucwords(strtolower($rowC[5]))?></option>
                            <option value="">-</option>
                            <?php while($rowu = mysqli_fetch_row($unidad)){?>
                            <option value="<?php echo $rowu[0] ?>"><?php echo ucwords((strtolower($rowu[1])));}?></option>
                          <?php } ?>
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
      var tipo = document.getElementById('tipo1').value;
      document.getElementById('tipo').value= tipo;
      var combo = document.getElementById("tipo1");
      var valorA = combo.options[combo.selectedIndex].text
      var fi = document.getElementById("unidad");
      if(valorA =='Numerico' || valorA=='Numérico' || valorA=='numerico'){
            fi.disabled=false;
            var valor = <?php echo '"'.$rowC[4].'"';?>;
            if(valor=='' || valor ==""){
            }else{ document.getElementById('unidad').value = valor;}
      } else {
          fi.value='';
          fi.disabled=true;
      }
  }
  </script>