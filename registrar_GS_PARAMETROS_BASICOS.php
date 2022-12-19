<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
?>
  <!--Titulo de la página-->
<title>Registrar Parámetros Básicos</title>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>
label#indicador-error, #txtNombre-error,#txtValor-error
{
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
            <h2 id="forma-titulo3" align="center" style="margin-top:0px;margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Parámetros Básicos</h2>
            <a href="listar_GS_PARAMETROS_BASICOS.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Parámetros Básicos</h5>
                   <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form  contenedorForma">
                       <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/parametrosBasicosJson.php?action=1" >
                           <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 100%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                           <div class="form-group" style="font-size: 13px;">
                
                            <div class="form-group" style="margin-top: 8px;">
                                <label for="indicador" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong><strong></strong>Indicador:</label>
                                <input type="text" name="indicador" id="indicador" class="form-control" maxlength="100" title="Ingrese el Indicador" onkeypress="return txtValida(event,'num_car')" placeholder="Indicador" required="required">
                            </div>      
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="txtNombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong><strong></strong>Nombre:</label>
                                <input type="text" name="txtNombre" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre" required>
                            </div>                                    

                            <div class="form-group" style="margin-top: -10px;">
                                <label for="txtValor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong><strong></strong> Valor:</label>
                                <input type="text"  name="txtValor" id="txtValor" class="form-control" maxlength="500"  title="Ingrese el Valor" placeholder=" Valor"  required>
                            </div> 
         
            
            
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                           </div>
                            <input type="hidden" name="MM_insert" >
                           
                        </form>
                       
                    </div>
                    
                    
            </div>
   
       </div>
</div>
<script src="js/select/select2.full.js"></script>

                        <script>
                            $(document).ready(function() {
                            $(".select2_single").select2({
        
                            allowClear: true
                            });
     
                            });
                        </script>
<?php require_once 'footer.php'; ?>
</body>
</html>

