<?php 
######################################################################################################
# ***************************************** Modificaciones ***************************************** #
######################################################################################################
#14/02/2018 | ERICA G. |ARCHIVO CREADO
######################################################################################################
require_once('Conexion/conexion.php'); ?>
<?php require_once 'head.php'; 
@session_start();
$compania  = $_SESSION['compania'];
?>
<title>Ejecución Presupuestal Cuentas Por Pagar</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltAnnio-error, #sltmes-error, #sltcni-error, #sltcnf-error  {
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
            <h2 align="center" class="tituloform">Ejecución Presupuestal Cuentas Por Pagar Vigencia Anterior</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group">
                        <?php
                        $annio = "SELECT id_unico,anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
                        $rsannio = $mysqli->query($annio);
                        ?> 
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                            <select  name="sltAnnio" id="sltAnnio" class="select2_single form-control" title="Seleccione Año">
                                <option value>Año</option>
                                <?php while ($filaAnnio = mysqli_fetch_row($rsannio)) { ?>
                                <option value="<?php echo $filaAnnio[0];?>"><?php echo $filaAnnio[1];?></option>                                
                                <?php } ?>                                    
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
                        <div class="form-group" style="margin-top: -5px">
                            <label for="fuente" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Fuente:</label>
                            <select name="fuente" id="fuente" class="select2_single form-control" title="Seleccione Fuente">
                                <option value>Fuente</option>                                  
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
       var form_data={action: 18, annio :$("#sltAnnio").val()};
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
       var form_data={action: 19, annio :$("#sltAnnio").val()};
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
    $('form').attr('action', 'informes/INFORMES_PPTAL/GF_INF_EJECUCION_CUENTAS_PAGAR.php?tipo=2');
    
    
   
}

</script>
<script>
function reportePdf(){
    $('form').attr('action', 'informes/INFORMES_PPTAL/GF_INF_EJECUCION_CUENTAS_PAGAR.php?tipo=1');
    
    
}
</script>
</body>
</html>