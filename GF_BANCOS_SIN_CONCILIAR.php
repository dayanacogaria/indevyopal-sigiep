<?php 
require_once('Conexion/conexion.php');
require_once 'head.php'; ?>
<title>Bancos Sin Conciliar</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #mes-error, #sltTcf-error, #fechaini-error, #fechafin-error, #sltctai-error, #sltctaf-error  {
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
            <h2 align="center" class="tituloform">Bancos Sin Conciliar</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                <div class="form-group">
                <?php 
                $annio = $_SESSION['anno'];
                     $cuentaI = "SELECT numero,mes FROM gf_mes "
                            . "WHERE parametrizacionanno='$annio' ORDER BY numero ASC";
                    $rsctai = $mysqli->query($cuentaI);
                ?>
                <div class="form-group" style="margin-top: -10px">
                    <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Mes Conciliación:</label>
                    <select required="required" name="mes" id="mes"  style="height: auto" class="select2_single form-control" title="Seleccione Mes">
                        <option value="">Mes Conciliación</option>
                    <?php 
                        while ($filactai= mysqli_fetch_row($rsctai)) 
                    { 
                    ?>
                        <option value="<?php echo $filactai[0];?>"><?php echo ucwords(mb_strtolower($filactai[1]));?></option>                                
                    <?php 
                    }
                     ?>                                    
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
   $('form').attr('action', 'informes/inf_bancos_sin_conciliar_Excel.php');
}

</script>
<script>
function reportePdf(){
    $('form').attr('action', 'informes/inf_bancos_sin_conciliar_pdf.php');
}
</script>
</div>
</body>
</html>