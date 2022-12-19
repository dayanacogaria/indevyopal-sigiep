<?php 
########## MODIFICACIONES ##############
#17/02/2017 | Erica G. *Modificación Búsqueda
########################################
require_once 'head.php';
require_once('Conexion/conexion.php');
$anno = $_SESSION['anno'];
$rubro = "SELECT Id_Unico, codi_presupuesto, Nombre "
        . "FROM gf_rubro_pptal  "
        . "WHERE parametrizacionanno = $anno "
        . "ORDER BY codi_presupuesto ASC";
$rubro_pptal =   $mysqli->query($rubro);


$concepto="SELECT Id_Unico,Nombre "
        . "FROM gf_concepto "
        . "WHERE parametrizacionanno = $anno "
        . "ORDER BY Nombre ASC";
$concep=$mysqli->query($concepto);

?>
  <!--Titulo de la página-->

<title>Registrar Concepto Rubro</title>

<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>
label#rubroB-error, #concepto-error
{
    display: block;
    color: #155180;
    font-weight: normal;
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
    <div class="col-sm-8 text-left">
        <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Concepto Rubro</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_Concepto_Rubro.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

             <!--Ingresa la información-->
            <div class="form-group" style="margin-top: -10px;">
				 <input type="hidden" name="rubroB" id="rubroB" required="required" title="Seleccione un rubro">
              <label for="rubroB" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Rubro:</label>
              <select name="rubro" id="rubro" class="select2_single form-control" title="Seleccione el rubro" onchange="llenar()" required>
                <option value="">Rubro</option>
                <?php while($row = mysqli_fetch_assoc($rubro_pptal)){?>
                <option value="<?php echo $row['Id_Unico'] ?>"><?php echo $row['codi_presupuesto'].' '.ucwords((mb_strtolower($row['Nombre'])));}?></option>;
              </select>
            </div>

            <div class="form-group">
              <label for="concepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
              <select name="concepto" id="concepto" class="select2_single form-control" title="Seleccione el concepto" required>
                <option value="">Concepto</option>
                <?php while($row = mysqli_fetch_assoc($concep)){?>
                <option value="<?php echo $row['Id_Unico'] ?>"><?php echo ucwords((mb_strtolower($row['Nombre'])));}?></option>;
              </select> 
            </div>
          
            
            
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
            </div>
            
            <input type="hidden" name="MM_insert" >
          </form>
        </div>

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
      var tercero = document.getElementById('rubro').value;
      document.getElementById('rubroB').value= tercero;
  }
  </script>
      
    </div>
      <!--Información adicional -->
        <div class="col-sm-6 col-sm-2" style="margin-top:-2px;" >
            <table class="tablaC table-condensed" style="margin-left: -3px; ">
                <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <button class="btn btnInfo btn-primary" disabled="true">Cuenta</button><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
</body>
</html>

