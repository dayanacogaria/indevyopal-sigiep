<?php 
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$anno = $_SESSION['anno'];
$con = new ConexionPDO();
?>
<title>Auxiliar Costos</title> 
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #centro_c-error, #mesF-error, #mesI-error   {
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
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Auxiliar Costos</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Inicial:</label>
                            <select name="mesI" id="mesI" class="select2_single form-control" title="Seleccione mes Inicial" style="height: auto " required>
                                <?php 
                                $vg = $con->Listar("SELECT numero, mes  
                                    FROM gf_mes 
                                    WHERE parametrizacionanno = $anno ORDER BY numero");
                                for ($i = 0; $i < count($vg); $i++) {
                                   echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].'</option>'; 
                                }                                    
                                ?>
                            </select>
                        </div> 
                        <div class="form-group" style="margin-top: -5px">
                            <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Final:</label>
                            <select name="mesF" id="mesF" class="select2_single form-control" title="Seleccione mes final" style="height: auto " required>
                                <?php 
                                $vg = $con->Listar("SELECT numero, mes  
                                    FROM gf_mes 
                                    WHERE parametrizacionanno = $anno ORDER BY numero DESC");
                                for ($i = 0; $i < count($vg); $i++) {
                                   echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].'</option>'; 
                                }                                    
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="centro_c" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Centro Costo:</label>
                            <select name="centro_c" id="centro_c" class="select2_single form-control" title="Seleccione Centro Costo" style="height: auto " required>
                                <?php 
                                $vg = $con->Listar("SELECT id_unico, sigla, nombre   
                                    FROM gf_centro_costo
                                    WHERE parametrizacionanno = $anno ");
                                for ($i = 0; $i < count($vg); $i++) {
                                   echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].' - '.$vg[$i][2].'</option>'; 
                                }                                    
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
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
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'?>  
<script>
function reporteExcel(){
    $('form').attr('action', 'informes/INF_AUXILIAR_COSTOS.php');
}

</script>
</div>
</body>
</html>