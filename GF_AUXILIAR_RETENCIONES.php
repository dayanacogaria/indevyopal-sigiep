<?php 
##################################################################################################
#********************************** Modificaciones ¨*********************************************#
##################################################################################################
#23/01/2018 | Parametrizacion año, 
##################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$anno = $_SESSION['anno'];?>
<title>Auxiliares Retenciones</title> 
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltTci-error, #sltTcf-error, #fechaini-error, #fechafin-error, #sltctai-error, #tipoI-error  {
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

<script>

        $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
       
        
        $("#fechaini").datepicker({changeMonth: true,}).val();
        $("#fechafin").datepicker({changeMonth: true}).val();
        
        
});
</script>
<!-- contenedor principal -->  
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Auxiliares De Retenciones</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                <div class="form-group">
                <?php 
                    $cuentaI = "SELECT id_unico,CONCAT(codi_cuenta,' - ',nombre), codi_cuenta from gf_cuenta "
                            . "WHERE parametrizacionanno = $anno AND clasecuenta=11 "
                            . "ORDER BY codi_cuenta ASC";
                    $rsctai = $mysqli->query($cuentaI);
                ?>
                <div class="form-group" style="margin-top: -10px">
                    <label for="sltctai" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Cuenta Bancos Inicial:</label>
                    <select name="sltctai" id="sltctai"  style="height: auto" class="select2_single form-control" title="Seleccione Cuenta inicial">
                        <option value="">Cuenta Inicial</option>
                    <?php 
                        while ($filactai= mysqli_fetch_row($rsctai)) 
                    { 
                    ?>
                        <option value="<?php echo $filactai[2];?>"><?php echo ucwords(mb_strtolower($filactai[1]));?></option>                                
                    <?php 
                    }
                     ?>                                    
                    </select>
                </div>            
            <?php 
                $cuentaF = "SELECT id_unico, codi_cuenta, nombre from gf_cuenta "
                        . "WHERE parametrizacionanno = $anno AND clasecuenta=11 "
                        . "ORDER BY codi_cuenta DESC";
                $rsctaf = $mysqli->query($cuentaF);
            ?>
             <div class="form-group" style="margin-top: 0px">
                <label for="sltctaf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Cuenta Bancos Final:</label>
                <select name="sltctaf" id="sltctaf" style="height: auto" class=" select2_single form-control" title=
                        "Seleccione Tipo cuenta final" >
                    <option value=""> Cuenta Final</option>
                <?php 
                    while ($filactaf = mysqli_fetch_row($rsctaf)) 
                { 
                ?>
                    <option value="<?php echo $filactaf[1];?>"><?php echo ucwords(mb_strtolower($filactaf[1].' - '.$filactaf[2]));?></option>                                
                <?php 
                }
                 ?>                                    
                </select>
            </div>
                    <?php 
                    $cuentaIR = "SELECT DISTINCT id_unico, CONCAT(codi_cuenta,' - ', nombre), codi_cuenta  from gf_cuenta "
                        . "WHERE parametrizacionanno = $anno AND clasecuenta=16 "
                            . "ORDER BY codi_cuenta ASC";
                    $cuentaIR = $mysqli->query($cuentaIR);
                    ?>
                    <div class="form-group" style="margin-top: 0px">
                    <label for="ctari" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Cuenta Retención Inicial:</label>
                    <select name="ctari" id="ctari"  style="height: auto" class="select2_single form-control" title="Seleccione Cuenta Retención inicial">
                        <option value="">Cuenta Retención Inicial</option>
                    <?php 
                        while ($filactri= mysqli_fetch_row($cuentaIR)) {  ?>
                        <option value="<?php echo $filactri[2];?>"><?php echo ucwords(mb_strtolower($filactri[1]));?></option>                                
                    <?php } ?>                                    
                    </select>
                </div>            
            <?php 
                $cuentaFR = "SELECT DISTINCT id_unico, CONCAT(codi_cuenta,' - ', nombre), codi_cuenta from gf_cuenta "
                        . "WHERE parametrizacionanno = $anno AND clasecuenta=16 "
                        . "ORDER BY codi_cuenta DESC";
                $cuentaFR = $mysqli->query($cuentaFR);
            ?>
             <div class="form-group" style="margin-top: 0px">
                <label for="ctarf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Cuenta Retención Final:</label>
                <select name="ctarf" id="ctarf" style="height: auto" class=" select2_single form-control" title=
                        "Seleccione Tipo cuenta final" >
                    <option value=""> Cuenta Retención Final</option>
                <?php 
                    while ($filactrf = mysqli_fetch_row($cuentaFR)) 
                { 
                ?>
                    <option value="<?php echo $filactrf[2];?>"><?php echo ucwords(mb_strtolower($filactrf[1]));?></option>                                
                <?php 
                }
                 ?>                                    
                </select>
            </div>
                <div class="form-group" style="margin-top: -5px;">
                     <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicial:</label>
                     <input class="form-control" type="text" name="fechaini" id="fechaini" placeholder="Fecha Inicial" autocomplete="off">
                </div>
                <div class="form-group" style="margin-top: -10px;">
                     <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Final:</label>
                     <input class="form-control" type="text" name="fechafin" id="fechafin" placeholder="Fecha Final" autocomplete="off">
                </div>
                    <div class="form-group" style="margin-top: -10px;">
                     <label for="tipoI" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Informe:</label>
                     <select name="tipoI" id="tipoI" style="height: auto" class="  form-control" title="Seleccione Tipo Informe"  required="required">
                        <option value=""> Tipo Informe</option>
                        <option value="1"> Detallado</option>
                        <option value="2">Consolidado</option>
                     </select>
                </div>
                
            <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              

                <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
            </div>
                </div>
            </form>

             
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
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'?>  
<script>
function reporteExcel(){
    console.log($("#tipoI").val());
    if($("#tipoI").val()==1) {
        $('form').attr('action', 'informes/inf_auxiliar_retencionesExcel.php');
    } else {
        if($("#tipoI").val()==2) { 
          $('form').attr('action', 'informes/inf_auxiliar_retencionesExcelC.php');
        }
    }
}

</script>
<script>
function reportePdf(){
    console.log($("#tipoI").val());
      if($("#tipoI").val()==1) {
            $('form').attr('action', 'informes/inf_auxiliar_retenciones.php');
        } else{
            if($("#tipoI").val()==2) { 
            $('form').attr('action', 'informes/inf_auxiliar_retencionesC.php');
        }
    }
}
</script>
</div>
</body>
</html>