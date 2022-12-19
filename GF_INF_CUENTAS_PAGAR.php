<?php 
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#17/09/2018 | Erica G. | Archivo Creado
#####################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #sltrubi-error, #sltrubf-error, #fechaini-error, #fechafin-error, #sltTci-error, #sltTcf-error  {
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
       
        
        $("#fechaini").datepicker({changeMonth: true,}).val(fecAct);
        $("#fechafin").datepicker({changeMonth: true}).val(fecAct);
        
        
});
</script>
<title>Cuentas Por Pagar</title>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Cuentas Por Pagar</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechaini" id="fechaini" title="Ingrese Fecha Inicial">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechafin" id="fechafin"  value="<?php echo date("Y-m-d");?>" title="Ingrese Fecha Final">
                        </div>
                        <!--TERCERO-->
                        <?php $ti= "SELECT IF(CONCAT_WS(' ',
                                    tr.nombreuno,
                                    tr.nombredos,
                                    tr.apellidouno,
                                    tr.apellidodos) 
                                    IS NULL OR CONCAT_WS(' ',
                                    tr.nombreuno,
                                    tr.nombredos,
                                    tr.apellidouno,
                                    tr.apellidodos) = '',
                                    (tr.razonsocial),
                                    CONCAT_WS(' ',
                                    tr.nombreuno,
                                    tr.nombredos,
                                    tr.apellidouno,
                                    tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico 
                                    FROM gf_tercero tr WHERE tr.compania = $compania  ORDER BY tr.numeroidentificacion ASC";
                           $rsTi = $mysqli->query($ti);?> 
                        <div class="form-group" style="margin-top: 0px">
                            <label for="sltTi" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tercero Inicial:</label>
                            <select  name="sltTi" id="sltTi" class="select2_single form-control" title="Seleccione Tercero Inicial" required>
                                <option value>Tercero Inicial</option>
                                <?php while ($filaTi = mysqli_fetch_row($rsTi)) { ?>
                                <option value="<?php echo $filaTi[1];?>"><?php echo $filaTi[1].' - '.ucwords(mb_strtolower($filaTi[0]));?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <?php $tf= "SELECT IF(CONCAT_WS(' ',
                                    tr.nombreuno,
                                    tr.nombredos,
                                    tr.apellidouno,
                                    tr.apellidodos) 
                                    IS NULL OR CONCAT_WS(' ',
                                    tr.nombreuno,
                                    tr.nombredos,
                                    tr.apellidouno,
                                    tr.apellidodos) = '',
                                    (tr.razonsocial),
                                    CONCAT_WS(' ',
                                    tr.nombreuno,
                                    tr.nombredos,
                                    tr.apellidouno,
                                    tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico 
                                    FROM gf_tercero tr  WHERE tr.compania = $compania ORDER BY tr.numeroidentificacion DESC";
                            $rsTf = $mysqli->query($tf);?>
                        <div class="form-group" style="margin-top: 0px">
                            <label for="sltTf" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tercero Final:</label>
                            <select  name="sltTf" id="sltTf" class="select2_single form-control" title="Seleccione Tercero Final" required>
                                <option value>Tercero Final</option>
                                <?php while ($filaTFI = mysqli_fetch_row($rsTf)) { ?>
                                <option value="<?php echo $filaTFI[1];?>"><?php echo $filaTFI[1].' - '.ucwords(mb_strtolower($filaTFI[0]));?></option>                                
                                <?php } ?>                                    
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
<script>
function reportePdf(){
    $('form').attr('action', 'informes/INF_CUENTAS_PAGAR.php?t=1');
    
}
</script>
<script>
function reporteExcel(){
    $('form').attr('action', 'informes/INF_CUENTAS_PAGAR.php?t=2');
    
}

</script>
</body>
</html>
<?php require_once 'footer.php'?>  