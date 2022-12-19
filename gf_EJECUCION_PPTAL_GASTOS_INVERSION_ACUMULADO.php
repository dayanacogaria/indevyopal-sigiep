<?php 
##################################################################################################################################################################
#                                      Modificaciones
##################################################################################################################################################################
#17/11/2017 | ERICA G. | INFORME PAC
##################################################################################################################################################################
require_once('Conexion/conexion.php'); ?>
<?php require_once 'head.php'; ?>
<title>Ejecución Presupuestal Gastos - Inversión Acumulado</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltAnnio-error, #sltmes-error, #sltcni-error, #sltcnf-error  {
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

   <style>
    .form-control {font-size: 12px;}
    
</style>

</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Ejecución Presupuestal Gastos e Inversión Acumulado</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <input type="hidden" name="tituloH" value="EJECUCIÓN PRESUPUESTAL GASTOS E INVERSIÓN ACUMULADO">
                    <input type="hidden" name="outputF" value="Informe_Ejecucion_Pptal_Gastos_Inversion">
                    <?php
                    $tituloH ="EJECUCIÓN PRESUPUESTAL GASTOS E INVERSIÓN ACUMULADO";
                    $outputF = "Informe_Ejecucion_Pptal_Gastos_Inversion";
                    ?>
                    <div class="form-group">
                        <?php
                        $compania = $_SESSION['compania']; 
                        $annio = "SELECT id_unico,anno 
                            FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
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
                        <div class="form-group" style="margin-top: -5px">
                            <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Informe por:</label>
                            <select name="tipo" id="tipo" class="select2_single form-control" title="Seleccione Informe">
                                <option value="0">Informe</option>
                                <option value="1">Disponibilidad</option>                                                                  
                                <option value="2">Registro</option>
                                <option value="3">Obligaciones</option>
                                <option value="4">Pagos</option>
                                <option value="5">Disponibilidad y Registros</option>
                                <option value="6">Informe PAC</option>
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
       var form_data={action: 11, annio :$("#sltAnnio").val()};
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
       var form_data={action: 12, annio :$("#sltAnnio").val()};
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
    
    var opcion = document.getElementById('tipo').value;
    switch(opcion){
        case('0'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_INV_EXCEL.php');
        break;
        case('1'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_DISPONIBILIDAD_EXCEL.php');
        break;
        case('2'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_REGISTROS_EXCEL.php');
        break;
        case('3'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_OBLIGACIONES_EXCEL.php');
        break;
        case('4'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_PAGOS_EXCEL.php');
        break;
        case('5'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_DISPO-REG_EXCEL.php');
        break;
        case('6'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_PAGOS_EXCEL.php?t=1');
        break;
    }
    
    
   
}

</script>
<script>
function reportePdf(){
    
     var opcion = document.getElementById('tipo').value;
    switch(opcion){
        case('0'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJEC_PPTAL_GASTOS_INVERSION.php');
        break;
        case('1'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_DISPONIBILIDAD.php');
        break;
        case('2'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_REGISTRO.php');
        break;
        case('3'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_OBLIGACIONES.php');
        break;
        case('4'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_PAGOS.php');
        break;
        case('5'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_DISPO-REG.php');
        break;
        case('6'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_EJP_GAST_PAGOS.php?t=1');
        break;
    }
    
}
</script>
</body>
</html>