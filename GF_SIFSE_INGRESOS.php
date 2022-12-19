<?php   
################MODIFICACIONES######################
#08/03/2017 |ERICA G.|REDIRECCION INFORMES
######################################
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$compania = $_SESSION['compania'];
$anno     = $_SESSION['anno'];
?>
<title>Reporte De Ingresos Presupuestales</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltAnnio-error, #sltmes-error, #sltcodi-error, #sltcnf-error {
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
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Reporte De Ingresos Presupuestales</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/generar_INF_PPTAL_INGRESOS_SIFSE.php" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <input type="hidden" name="tituloH" value="REPORTE DE INGRESOS PRESUPUESTALES">
                    <input type="hidden" name="outputF" value="Informe_Ingresos_Presupuestales">
                    <?php
                        $tituloH ="REPORTE DE INGRESOS PRESUPUESTALES";
                        $outputF = "Informe_Ingresos_Presupuestales";
                        ?>
                    <div class="form-group">
                    <?php
                       $annio = "SELECT id_unico,anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
                       $rsannio = $mysqli->query($annio);
                    ?> 
                    <div class="form-group" style="margin-top: -5px">
                        <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                        <select required name="sltAnnio" id="sltAnnio" class="select2_single form-control" title="Seleccione Año">
                            <option value>Año</option>
                            <?php while ($filaAnnio = mysqli_fetch_row($rsannio)) { ?>
                            <option value="<?php echo $filaAnnio[0];?>"><?php echo $filaAnnio[1];?></option>                                
                            <?php }?>                                    
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: -5px">
                        <label for="sltmes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Trimestre Acumulado:</label>
                        <select required name="sltmes" id="sltmes"  class="select2_single form-control" title="Seleccione Trimestre Acumulado" >
                            <option value>Trimestre Acumulado</option>
                            <option value = "03">Primer Trimestre</option>
                            <option value = "06">Segundo Trimestre</option>
                            <option value = "09">Tercer Trimestre</option>
                            <option value = "12">Cuarto Trimestre</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-top: -5px">
                        <label for="sltcodi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Rubro Inicial:</label>
                        <select required name="sltcodi" id="sltcodi" class="select2_single form-control" title="Seleccione Rubro Inicial" >
                            <option value="">Rubro Inicial</option>
                        </select>
                    </div>            
                    <div class="form-group" style="margin-top: -5px">
                        <label for="sltcnf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Rubro Final:</label>
                        <select required name="sltcnf" id="sltcnf" class="select2_single form-control" title="Seleccione Rubro Final">
                            <option value>Rubro Final</option>                                   
                        </select>
                    </div>
                    <div class="form-group text-center" style="margin-top:20px;">
                        <div class="col-sm-1" style="margin-top:0px;margin-left:620px">
                            <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                        </div>
                        <div class="col-sm-1" style="margin-top:-34px;margin-left:670px">
                            <button onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    </div>
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
<?php require_once 'footer.php'?>  
<script>
function reporteExcel(){
   $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_INGRESOS_SIFSE_EXCEL.php');
}

</script>
<script>
function reportePdf(){
    $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_PPTAL_INGRESOS_SIFSE.php');
}

</script>
<script>    
    $("#sltAnnio").change(function(){
        
       var form_data={action: 5, annio :$("#sltAnnio").val()};
       var optionCI ="<option value=''>Rubro Inicial</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesPptal.php',
          data: form_data,
          success: function(response){
              optionCI =optionCI+response;
              $("#sltcodi").html(optionCI).focus();              
          }
       });
       var form_data={action: 6, annio :$("#sltAnnio").val()};
       var optionCF ="<option value=''>Rubro Final</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesPptal.php',
          data: form_data,
          success: function(response){
              optionCF =optionCF+response;
              $("#sltcnf").html(optionCF).focus();              
          }
       });
       
       
    });
</script>
</body>
</html>