<?php   
#####################################MODIFICACIONES#################################################
#28/06/2017 |ERICA G.|ARCHIVO CREADOR
####################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php';
$compania = $_SESSION['compania'];
$annio = $_SESSION['anno'];
?>
<title>Ejecución Presupuestal Rentas e Ingresos</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltfechaI-error, #sltfechaF-error, #sltcni-error, #sltcnf-error  {
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
    }
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
            <h2 align="center" class="tituloform">Ejecución Presupuestal Rentas e Ingresos</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group">
                       <div class="form-group" style="margin-top: -5px">
                            <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                            <select name="sltAnnio" id="sltAnnio" class="select2_single form-control" title="Seleccione Año" style="height: auto " required>
                                <option value="">Año</option>
                                <?php 
                                $annio = "SELECT  id_unico, anno FROM gf_parametrizacion_anno "
                                        . "WHERE compania = $compania ORDER BY anno DESC";
                                $rsannio = $mysqli->query($annio);
                                while ($filaAnnio = mysqli_fetch_row($rsannio)) { ?>
                                     <option value="<?php echo $filaAnnio[0];?>"><?php echo $filaAnnio[1];?></option>                                
                                <?php }?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltmesi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo Inicial:</label>
                            <select required name="sltmesi" id="sltmesi" style="height: auto" class="select2_single form-control" title="Mes Inicial" >
                                <option value="">Mes Inicial</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltmesf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo Final:</label>
                            <select required name="sltmesf" id="sltmesf" style=" height: auto" class="select2_single form-control" title="Mes Final" >
                                   <option value="">Mes Final</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Inicial:</label>
                            <select required="required" name="sltcodi" id="sltcodi" class="select2_single form-control" title="Seleccione Cuenta Inicial" >
                                <option value> Código Inicial</option>                                  
                            </select>
                        </div>
                       <div class="form-group" style="margin-top: -0px">
                            <label for="sltcodf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Final:</label>
                            <select required="required" name="sltcodf" id="sltcodf" class="select2_single form-control" title="Seleccione Cuenta Final">
                                <option value> Código Final</option>                      
                            </select>
                        </div>
                        
                        <div class="col-sm-10" style="margin-top:0px;margin-left:700px" >
                            <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                        
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
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
    $("#sltAnnio").change(function(){
        
       var form_data={action: 1, annio :$("#sltAnnio").val()};
       var optionMI ="<option value=''>Periodo Inicial</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesPptal.php',
          data: form_data,
          success: function(response){
              optionMI =optionMI+response;
              $("#sltmesi").html(optionMI).focus();              
          }
       });
       var form_data={action: 2, annio :$("#sltAnnio").val()};
       var optionMF ="<option value=''>Periodo Final</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesPptal.php',
          data: form_data,
          success: function(response){
              optionMF =optionMF+response;
              $("#sltmesf").html(optionMF).focus();              
          }
       });
       var form_data={action: 5, annio :$("#sltAnnio").val()};
       var optionCI ="<option value=''>Código Inicial</option>";
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
       var optionCF ="<option value=''>Código Final</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesPptal.php',
          data: form_data,
          success: function(response){
              optionCF =optionCF+response;
              $("#sltcodf").html(optionCF).focus();              
          }
       });
       
       
    });
</script>
<?php require_once 'footer.php'?>  
<script>
function reporteExcel(){
    $('form').attr('action', 'informes/INFORMES_PPTAL/INFORME_EJECUCION_INGRESOS_EXCEL.php');
}

</script>
<script>
function reportePdf(){
    $('form').attr('action', 'informes/INFORMES_PPTAL/INFORME_EJECUCION_INGRESOS.php');  
}
</script>
</body>
</html>