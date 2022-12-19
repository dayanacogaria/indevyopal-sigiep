<?php   
########################################################################################################################################################
#                           MODIFICACIONES
########################################################################################################################################################
#17/11/2017 |ERICA G.|INFORME PAC 
#08/03/2017 |ERICA G.|REDIRECCION INFORMES
########################################################################################################################################################
  require_once('Conexion/conexion.php');
  require_once 'head.php'; 
  $annio = $_SESSION['anno'];
?>
<title>Ejecución Presupuestal Rentas e Ingresos Acumulado</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltAnnio-error, #sltmes-error, #sltcodi-error, #sltcnf-error  {
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
            <h2 align="center" class="tituloform">Ejecución Presupuestal Rentas e Ingresos Acumulado</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
<!-- inicio del formulario --> 
<form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
              
        <input type="hidden" name="tituloH" value="EJECUCIÓN PRESUPUESTAL RENTAS E INGRESOS ACUMULADOS">
        <input type="hidden" name="outputF" value="Informe_Ejecucion_Pptal_Rentas_Ingresos">
          
         <?php
          $tituloH ="EJECUCIÓN PRESUPUESTAL RENTAS E INGRESOS ACUMULADOS";
          $outputF = "Informe_Ejecucion_Pptal_Rentas_Ingresos";
          ?>

          <div class="form-group">
             <?php
             $compania = $_SESSION['compania'];
                $annio = "SELECT id_unico,anno FROM gf_parametrizacion_anno  where compania = $compania ORDER BY anno DESC";
                $rsannio = $mysqli->query($annio);
            ?> 
<!--- Consulta para Cargar Año Inicial--->              
             <div class="form-group" style="margin-top: -5px">
             <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
             <select required name="sltAnnio" id="sltAnnio" class=" select2_single form-control" title=
                     "Seleccione Año" >
                 <option value>Año</option>
             <?php 
                 while ($filaAnnio = mysqli_fetch_row($rsannio)) 
             { 
             ?>
                <option value="<?php echo $filaAnnio[0];?>"><?php echo $filaAnnio[1];?></option>                                
             <?php 
             }
              ?>                                    
             </select>
          </div>

<!--Campo para captura de Mes Inicial-->
             <div class="form-group" style="margin-top: -5px">
             <label for="sltmes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes:</label>
             <select required name="sltmes" id="sltmes"  class="select2_single form-control" title="Mes Inicial" >
                 <option value>Mes</option>
             </select>
             </div>
<!----------Fin Captura de Mes Inicial-->

             <div class="form-group" style="margin-top: -5px">
             <label for="sltcodi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Inicial:</label>
             <select required name="sltcodi" id="sltcodi"  class="select2_single form-control" title="Seleccione Código inicial" >
                 <option value>Código Inicial</option>                                   
             </select>
          </div> 
             <div class="form-group" style="margin-top: -5px">
             <label for="sltcnf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Final:</label>
             <select required name="sltcnf" id="sltcnf" class=" select2_single form-control" title=
                     "Seleccione Código final">
                 <option value>Código Final</option>                                
             </select>
          </div>
             <div class="form-group" style="margin-top: -5px">
                            <label for="fuente" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Fuente:</label>
                            <select name="fuente" id="fuente" class="select2_single form-control" title="Seleccione Fuente">
                                <option value>Fuente</option>                                   
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Tipo Informe:</label>
                            <select name="tipo" id="tipo" class="select2_single form-control" title="Seleccione Tipo Informe">
                                <option value>Tipo Informe</option>
                                <option value="1">Informe de PAC</option>           
                            </select>
                        </div>
                 <div class="col-sm-4" style="margin-top:0px;margin-left:620px">
                        <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>           
                        <button onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>   
                    </div>
          </div>

         </form>
<!--Fin de división y contenedor del formulario -->

        </div>     
    </div>
  </div>
  <!-- Fin del Contenedor principal -->
  <!--Información adicional -->
</div>
<script src="js/select/select2.full.js"></script>
<script>
$(document).ready(function() {
  $(".select2_single").select2({
    allowClear: true
  });
});
</script>
<script>    
    $("#sltAnnio").change(function(){
        
       var form_data={action: 1, annio :$("#sltAnnio").val()};
       var optionMI ="<option value=''>Mes Acumulado</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionMI =optionMI+response;
              $("#sltmes").html(optionMI).focus();              
          }
       });
       var form_data={action: 14, annio :$("#sltAnnio").val()};
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
       var form_data={action: 15, annio :$("#sltAnnio").val()};
       var optionCF ="<option value=''>Código Final</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionCF =optionCF+response;
              $("#sltcnf").html(optionCF).focus();              
          }
       });
       var form_data={action: 13, annio :$("#sltAnnio").val()};
       var optionF ="<option value=''>Fuente</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionF =optionF+response;
              $("#fuente").html(optionF).focus();              
          }
       });
       
       
    });
</script>
<?php require_once 'footer.php'?> 
    <script>
function reporteExcel(){
   $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_RENT_ING_EXCEL.php');
}

</script>
<script>
function reportePdf(){
    $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJEC_PPTAL_RENTAS_INGRESOS_C.php');
}
</script>
</body>
</html>