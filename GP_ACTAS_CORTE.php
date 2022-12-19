<?php 
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$annio = $_SESSION['anno'];
$compania = $_SESSION['compania'];
?>
<title>Actas de Corte</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #sltAnnio-error, #sltmesi-error, #sltmesf-error, #sltcodi-error, #sltcodf-error {
    display: block;
    color: #bd081c;
    font-weight: bold;
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
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -20px"> 
                <h2 align="center" class="tituloform">Actas De Corte</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo:</label>
                            <select required name="sltcodi" id="sltcodi" style="height: auto" class="select2_single form-control" title="Seleccione Tipo">
                                <option value="">Tipo</option>
                            </select>
                        </div>
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
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo:</label>
                            <select required name="sltcodf" id="sltcodf" style="height: auto" class="select2_single form-control" title="Seleccione Periodo">
                                <option value="">Periodo</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicial:</label>
                            <input class="form-control" type="text" name="fechaini" id="fechaini" placeholder="Fecha Inicial">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                             <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Final:</label>
                             <input class="form-control" type="text" name="fechafin" id="fechafin" placeholder="Fecha Final">
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Acta Inicial:</label>
                            <select required name="sltcodi" id="sltcodi" style="height: auto" class="select2_single form-control" title="Acta Inicial">
                                <option value="">Acta Inicial</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Acta Final:</label>
                            <select required name="sltcodf" id="sltcodf" style="height: auto" class="select2_single form-control" title="Seleccione Acta Final">
                                <option value="">Acta Final</option>
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
<?php require_once 'footer.php' ?>  

</body>
</html>