<?php 
#######################################################################################################
#           *********       Modificaciones      *********       #
#######################################################################################################
#21/12/2017 |Erica G.| No tome en cuenta el comprobante cierre - Parametrización año
#07/03/2017 |Erica G |OPCION EXCEL
#04/03/2017 |Erica G |Valor opcion combos DISEÑO
#######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php';
$annio = $_SESSION['anno'];
$compania = $_SESSION['compania']; 
?>
<title>Estado Actividad económica - social - contable</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltAnnio-error, #sltmesi-error, #sltmesf-error, #sltcodi-error, #sltcodf-error, #ndigitos-error {
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
 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menu del formulario -->    
  <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left" style="margin-top: -20px"> 
        <h2 align="center" class="tituloform">Estado Actividad Económica, Social y Contable</h2>
        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
            <!-- inicio del formulario --> 
            <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="#"  target=”_blank”>  
                  <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                  <input type="hidden" name="id" value="<?php echo $row[0] ?>">

                  <div class="form-group">
        <!--- Consulta para Cargar Año Inicial--->              
                     <div class="form-group" style="margin-top: -5px">
                     <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                     <select name="sltAnnio" id="sltAnnio" class="select2_single form-control" title="Seleccione Año" style="height: auto " required>
                                        <option value="">Año</option>
                                        <?php 
                                        $annio = "SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
                                        $rsannio = $mysqli->query($annio);
                                        while ($filaAnnio = mysqli_fetch_row($rsannio)) { ?>
                                             <option value="<?php echo $filaAnnio[0];?>"><?php echo $filaAnnio[1];?></option>                                
                                        <?php }?>                                    
                                    </select>
                  </div>

            <!--Campo para captura de Mes Inicial-->
                         <div class="form-group" style="margin-top: -5px">
                         <label for="sltmesi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Inicial:</label>
                         <select name="sltmesi" id="sltmesi" style=" height: auto" class="select2_single form-control" title="Mes Inicial" required>
                             <option value="">Mes Inicial</option>
                         </select>
                         </div>
            <!----------Fin Captura de Mes Inicial-->
            <!--Campo para captura de Mes Final-->
                         <div class="form-group" style="margin-top: -5px">
                         <label for="sltmesf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Final:</label>
                         <select name="sltmesf" id="sltmesf" style="height: auto" class="select2_single form-control" title="Mes Final" required>
                             <option value="">Mes Final</option>
                         </select>
                         </div>


                         <div class="form-group" style="margin-top: -5px">
                         <label for="sltcodi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Inicial:</label>
                         <select name="sltcodi" id="sltcodi" style="height: auto" class="select2_single form-control" title="Seleccione Cuenta Inicial" required>
                             <option value="">Código Inicial</option>                              
                         </select>
                      </div>
                         <div class="form-group" style="margin-top: -5px">
                         <label for="sltcodf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Final:</label>
                         <select name="sltcodf" id="sltcodf" style="height: auto" class="select2_single form-control" title=
                                 "Seleccione Cuenta Final" required>
                             <option value="">Código Final</option>                            
                         </select>
                      </div>
                            <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              

                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            </div>
                      </div>

                     </form>
            <!--Fin de división y contenedor del formulario -->

        </div>     
    </div>
  </div>
  <!-- Fin del Contenedor principal -->
<script>    
    $("#sltAnnio").change(function(){
        
       var form_data={action: 1, annio :$("#sltAnnio").val()};
       var optionMI ="<option value=''>Mes Inicial</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionMI =optionMI+response;
              $("#sltmesi").html(optionMI).focus();              
          }
       });
       var form_data={action: 2, annio :$("#sltAnnio").val()};
       var optionMF ="<option value=''>Mes Final</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionMF =optionMF+response;
              $("#sltmesf").html(optionMF).focus();              
          }
       });
       var form_data={action: 9, annio :$("#sltAnnio").val()};
       var optionCI ="<option value=''>Código Inicial</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionCI =optionCI+response;
              $("#sltcodi").html(optionCI).focus();              
          }
       });
       var form_data={action: 10, annio :$("#sltAnnio").val()};
       var optionCF ="<option value=''>Código Final</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionCF =optionCF+response;
              $("#sltcodf").html(optionCF).focus();              
          }
       });
       
       
    });
</script>
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
   $('form').attr('action', 'informes/generar_INF_EST_ACT_ECON_SOCEXCEL.php');
}

</script>
<script>
function reportePdf(){
    $('form').attr('action', 'informes/generar_INF_EST_ACT_ECON_SOC.php');
}
</script>
</body>
</html>