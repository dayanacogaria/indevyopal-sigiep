<?php 
###############################MODIFICACIONES##########################
#16/05/2017 | ERICA G. | TERCEROS
//llamado a la clase de conexion
#######################################################################
#29/09/2021 | ELKIN O. | INFORMES POR: 
//Se agrego la opcion de auxiliar presupuestal con contrato, se crearon los dos formatos, excel y pdf donde se agregan los dos campos de Tipo contrato y numero contrato.
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
<title>Consolidado Comprobantes De Apropiacion De Presupuesto </title>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Consolidado Comprobantes De Apropiacion De Presupuesto</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <input type="hidden" name="headH" value="GASTOS" />
                        <input type="hidden" name="footH" value="Gastos" />
                        <?php 
                        $tituloH = "GASTOS";
                        $tituloF = "Gastos";     
                        $rubroI = "SELECT id_unico,codi_presupuesto, CONCAT(codi_presupuesto,' - ',nombre) AS rubro "
                                . "from gf_rubro_pptal WHERE parametrizacionanno = $anno AND (tipoclase = 7 OR tipoclase = 9 OR tipoclase=10 OR tipoclase=15 OR tipoclase=16) ORDER BY codi_presupuesto ASC";
                        $rsrubi = $mysqli->query($rubroI);
                        ?> 
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechaini" id="fechaini" title="Ingrese Fecha Inicial">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechafin" id="fechafin"  value="<?php echo date("Y-m-d");?>" title="Ingrese Fecha Final">
                        </div>
                       
                        <div class="form-group text-center" style="margin-top:20px;">
                            
                            <div class="col-sm-2" style="margin-top:10px;margin-left:540px">
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
    var opcion = document.getElementById('tipo').value;
    $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_AUXILIAR_CONTRATOS.php');
    
}
</script>
<script>
function reporteExcel(){
    $('form').attr('action', 'informes/INFORMES_PPTAL/INF_CONSOLIDADO_APRO_PRESUPUESTAL.php');

}

</script>
</body>
</html>
<?php require_once 'footer.php'?>   