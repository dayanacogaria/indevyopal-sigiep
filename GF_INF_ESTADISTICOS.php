<?php 
##################################################################################################
#********************************** Modificaciones ¨*********************************************#
##################################################################################################
#03/10/2018 |Creado
##################################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$con = new ConexionPDO();
$anno = $_SESSION['anno'];?>
<title>Informe Consolidado Almacén</title> 
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #tipo-error  {
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
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Informes Almacén</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes_consolidado/INF_ALMACEN.php" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Informe:</label>
                            <select name="tipo" id="tipo" class="form-control select2" title="Seleccione Tipo Informe" style="height: auto " required>
                                <option value="">Tipo Informe</option>
                                <option value="1">Instituciones Educativas Sin PPE</option>
                                <option value="2">Instituciones Educativas Con PPE En 0</option>
                                <option value="6">Instituciones Educativas Con Devolutivos En 0</option>
                                <option value="3">Instituciones Educativas Con PPE Sin Vida Útil</option>
                                <option value="4">Instituciones Con Única Dependencia</option>
                                <option value="7">Instituciones Con Otros Movimientos</option>
                                <option value="5">Resumen</option>
                            </select>
                        </div>
                        
                        <div class="form-group form-inline  col-md-12 col-lg-12" style="margin-left: 5px; margin-top: 10px">
                            <label for="sector2" class="col-sm-5 control-label"></label>
                            <button type="submit" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-print" aria-hidden="true"></i></button>    
                        </div>
                        </div>
                </form>
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
    <script type="text/javascript"> 
            $("#tipo").select2();
        </script>
    <?php require_once 'footer.php'?>  
</div>
</body>
</html>