<?php 
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#26/01/2018 | Erica G. | Archivo Creado
#####################################################################################
require_once('Conexion/ConexionPDO.php');
require_once 'head.php';
$con    = new ConexionPDO();
$anno   = $_SESSION['anno']; 
# *** Consultas Combos ***#
#Cuenta Banco
$cbi = $con->Listar("SELECT id_unico, codi_cuenta, LOWER(nombre) FROM gf_cuenta WHERE clasecuenta =11 AND parametrizacionanno = $anno AND (movimiento=1 OR auxiliartercero = 1 OR auxiliarproyecto =1) ORDER BY codi_cuenta ASC");
$cbf = $con->Listar("SELECT id_unico, codi_cuenta, LOWER(nombre) FROM gf_cuenta WHERE clasecuenta =11 AND parametrizacionanno = $anno AND (movimiento=1 OR auxiliartercero = 1 OR auxiliarproyecto =1) ORDER BY codi_cuenta DESC");
#Rubros
$cri = $con->Listar("SELECT id_unico, codi_presupuesto, LOWER(nombre) FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND (tipoclase = 7 OR tipoclase=8) ORDER BY codi_presupuesto ASC");
$crf = $con->Listar("SELECT id_unico, codi_presupuesto, LOWER(nombre) FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND (tipoclase = 7 OR tipoclase=8) ORDER BY codi_presupuesto DESC")
        
?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #fechaI-error, #fechaF-error, #bancoI-error, #bancoF-error, #rubroI-error, #rubroF-error  {
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
       
        
        $("#fechaI").datepicker({changeMonth: true,}).val();
        $("#fechaF").datepicker({changeMonth: true}).val();
        
        
});
</script>
<title>Relación de Egresos Con Rubro</title>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Relación de Egresos Con Rubro</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="fechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechaI" id="fechaI" title="Ingrese Fecha Inicial">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="fechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechaF" id="fechaF" title="Ingrese Fecha Final">
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="bancoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta De Banco Inicial:</label>
                            <select required="required"  name="bancoI" id="bancoI" class="select2_single form-control" title="Seleccione Banco Inicial">
                                <option value>Código Banco Inicial</option>
                                <?php for ($i = 0; $i < count($cbi); $i++) {
                                    echo '<option value ="'.$cbi[$i][1].'">'.$cbi[$i][1].' - '.ucwords($cbi[$i][2]).'</option>';
                                }?>                                    
                            </select>
                        </div> 
                        <div class="form-group" style="margin-top: 0px">
                            <label for="bancoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta De Banco Final:</label>
                            <select required="required"  name="bancoF" id="bancoF" class="select2_single form-control" title="Seleccione Banco Final">
                                <option value>Código Banco Final</option>
                                <?php for ($i = 0; $i < count($cbf); $i++) {
                                    echo '<option value ="'.$cbf[$i][1].'">'.$cbf[$i][1].' - '.ucwords($cbf[$i][2]).'</option>';
                                }?>                                   
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 0px">
                            <label for="rubroI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código Rubro Inicial:</label>
                            <select  required="required" name="rubroI" id="rubroI" class="select2_single form-control" title="Seleccione Código Inicial">
                                <option value>Código Rubro Inicial</option>
                                <?php for ($i = 0; $i < count($cri); $i++) {
                                    echo '<option value ="'.$cri[$i][1].'">'.$cri[$i][1].' - '.ucwords($cri[$i][2]).'</option>';
                                }?>                                     
                            </select>
                        </div> 
                        <div class="form-group" style="margin-top: 0px">
                            <label for="rubroF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código Rubro Final:</label>
                            <select required="required"  name="rubroF" id="rubroF" class="select2_single form-control" title="Seleccione Código Final">
                                <option value>Código Rubro Final</option>
                                <?php for ($i = 0; $i < count($crf); $i++) {
                                    echo '<option value ="'.$crf[$i][1].'">'.$crf[$i][1].' - '.ucwords($crf[$i][2]).'</option>';
                                }?>                                    
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
<script>
function reportePdf(){
    $('form').attr('action', 'informes/INF_Relacion_Egresos_Rubro.php?exp=1');
   
}
</script>
<script>
function reporteExcel(){
    $('form').attr('action', 'informes/INF_Relacion_Egresos_Rubro.php?exp=2');
}

</script>
</body>
</html>
<?php require_once 'footer.php'?>  