<?php 
require_once('Conexion/conexion.php'); 
require_once('Conexion/ConexionPDO.php'); 
require_once 'head.php'; 
$con = new ConexionPDO();
$tf = $con->Listar("SELECT id_unico, nombre FROM gf_tipo_fuente");
$htmlf = '';
for ($i = 0; $i < count($tf); $i++) {
    $htmlf .= '<option value="'.$tf[$i][0].'">'.$tf[$i][1].'</option>';
} ?>
<title>Ejecución Presupuestal Regalías - Ingresos</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltAnnio-error, #sltmes-error, #sltcni-error, #sltcnf-error , #fuente-error {
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
    rules: {
        sltmes: {
          required: true
        },
        sltcni: {
          required: true
        },
        sltAnnio: {
          required: true
        }
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
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Ejecución Presupuestal Regalías - Ingresos</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group">
                        <input type="hidden" name="sltAnnio" id="sltAnnio" value="<?php echo $_SESSION['anno']?>">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="fuente" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong><strong class="obligado"></strong>Tipo Fuente:</label>
                            <select name="fuente" id="fuente" class="select2_single form-control" title="Seleccione Tipo Fuente" required="required">
                                <option value>Tipo Fuente</option>     
                                <?=$htmlf;?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltmes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Acumulado al Mes de:</label>
                            <select name="sltmes" id="sltmes" class="select2_single form-control" title="Mes Inicial" >
                                <option value>Mes Acumulado</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcni" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Inicial:</label>
                            <select required="required" name="sltcni" id="sltcni" class="select2_single form-control" title="Seleccione Código inicial" >
                                <option value>Código Inicial</option>                                  
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcnf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Final:</label>
                            <select required="required" name="sltcnf" id="sltcnf" class="select2_single form-control" title="Seleccione Código Final">
                                <option value>Código Final</option>                                
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


<script>    
    $(document).ready(function() {        
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
              $("#sltcni").html(optionCI).focus();              
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
       
       
    });
</script>
<?php require_once 'footer.php'?>  
<script>
function reportePdf(){
    $('form').attr('action', 'informes/INFORMES_PPTAL/INF_EJECUCION_REGALIAS.php?t=3');    
}
function reporteExcel(){
    $('form').attr('action', 'informes/INFORMES_PPTAL/INF_EJECUCION_REGALIAS.php?t=4');    
}
</script>
</body>
</html>