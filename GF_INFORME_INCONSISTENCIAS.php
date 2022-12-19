<?php 
  require_once('Conexion/conexion.php');
  require_once 'head_listar.php';
?>
<title>Informe Inconsistencias Terceros</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #tipoInf-error {
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
                <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 0px; margin-right: 4px; margin-left: 4px;width: 100%">Informe Inconsistencias Terceros</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form col-sm-12">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="#" target="_blank">
                        <p align="center" style="margin-bottom: 0px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: 5px">
                            <label for="tipoInf" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Informe:</label>
                            <select name="tipoInf" id="tipoInf" class="form-control select2_single" title="Tipo Informe" style="width: 250px;" required>
                                <option value="">Tipo Informe</option>
                                <option value="1">Terceros Con Doble Perfil</option>
                                <option value="2">Terceros Con Razón Social y Nombres</option>
                                <option value="3">Terceros Sin Perfil</option>
                                <option value="4">Terceros Sin Dirección</option>
                            </select>  
                            <div class="col-sm-10" style="margin-top:15px;margin-left:600px" >
                                <input id="exportar" name="exportar"  type="hidden"/>
                                <button onclick="reporte(1)" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                                <button style="margin-left:10px;" onclick="reporte(2)" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
                <script>
                    function reporte(tipo) {
                        $('form').attr('action', 'informes/INF_INCONSISTENCIAS_TERCEROS.php?tipo='+tipo);
                    }

                </script>
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