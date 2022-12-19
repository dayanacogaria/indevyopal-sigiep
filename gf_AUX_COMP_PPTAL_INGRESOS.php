<?php 
#llamado a la clase de conexion
  require_once('Conexion/conexion.php');
  require_once 'head.php';
  @session_start();
  $anno = $_SESSION['anno'];
  $compania =$_SESSION['compania'];
?>
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
       
        
        $("#fechaini").datepicker({changeMonth: true}).val(fecAct);
        $("#fechafin").datepicker({changeMonth: true}).val(fecAct);
        
        
});
</script>
<title>Auxiliares Comprobantes Presupuestales Ingresos</title>
</head>
<body>
 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
    <div class="row content">
    <!-- Llamado al menu del formulario -->    
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Auxiliares Comprobantes Presupuestales Ingresos</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
            <!-- inicio del formulario --> 
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <input type="hidden" name="headH" value="INGRESOS" />
                        <input type="hidden" name="footH" value="Ingresos" />
                        <!--- Consulta para Rubro Inicial--->
                        <?php
                        $tituloH = "INGRESOS";
                        $tituloF = "Ingresos";
                        $rubroI = "SELECT DISTINCT id_unico,codi_presupuesto, CONCAT(codi_presupuesto,' - ',nombre) AS rubro "
                        . "from gf_rubro_pptal WHERE tipoclase = 6 AND parametrizacionanno = $anno ORDER BY codi_presupuesto ASC";
                        $rsrubi = $mysqli->query($rubroI);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltrubi" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Rubro Inicial:</label>
                            <select required="required" name="sltrubi" id="sltrubi" class="select2_single form-control" title="Seleccione Rubro Inicial">
                                <option value>Rubro Inicial</option>
                                <?php while ($filarubi= mysqli_fetch_row($rsrubi)) { ?>
                                <option value="<?php echo $filarubi[1];?>"><?php echo ucwords($filarubi[2]);?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <!--- Fin Consulta para Rubro Inicial--->              
                        <!--- Consulta para Rubro Final--->              
                        <?php 
                           $rubroF = "SELECT DISTINCT  id_unico,codi_presupuesto, CONCAT(codi_presupuesto,' - ',nombre) AS rubro "
                                   . "from gf_rubro_pptal WHERE tipoclase = 6 AND parametrizacionanno = $anno  ORDER BY codi_presupuesto DESC";
                           $rsrubf = $mysqli->query($rubroF);
                        ?>
                        <div class="form-group" style="margin-top: 0px">
                            <label for="sltrubf" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Rubro Final:</label>
                            <select required="required" name="sltrubf" id="sltrubf" class="select2_single form-control" title="Seleccione Rubro final">
                                <option value>Rubro Final</option>
                                <?php while ($filarubf = mysqli_fetch_row($rsrubf)) { ?>
                                <option value="<?php echo $filarubf[1];?>"><?php echo ucwords($filarubf[2]);?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <!-- Fin Consulta para cargar Cuenta Final
                        <!--Campo para captura de Fecha Inicial-->
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechaini" id="fechaini" title="Ingrese Fecha Inicial">
                        </div>
                        <!----------Fin Captura de Fecha Inicial-->           
                        <!--Campo para captura de Fecha Final-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechafin" id="fechafin"  value="<?php echo date("Y-m-d");?>" title="Ingrese Fecha Final">
                        </div>
                        <!----------Fin Captura de Fecha Final-->
                        <!--- Consulta para Cargar Tipo Comprobante Inicial--->
                        <?php
                            $tci= "SELECT id_unico,CONCAT (codigo,' - ',nombre) AS compp, codigo 
                                FROM gf_tipo_comprobante_pptal WHERE compania = $compania  
                                AND (clasepptal ='13' OR clasepptal ='18' OR clasepptal ='19' )
                                ORDER BY codigo ASC";
                            $rsTci = $mysqli->query($tci);
                        ?> 
                        <div class="form-group" style="margin-top: -10px">
                            <label for="sltTci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Inicial:</label>
                            <select required="required" name="sltTci" id="sltTci" class="select2_single form-control" title="Seleccione Tipo Comprobante Inicial">
                                <option value>Tipo Comprobante Inicial</option>
                                <?php while ($filaTci = mysqli_fetch_row($rsTci)) { ?>
                                <option value="<?php echo $filaTci[2];?>"><?php echo ucwords($filaTci[1]);?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <!---- Consulta para llenar campo Tipo Comprobante final-->
                        <?php 
                        $tcf= "SELECT id_unico,CONCAT (codigo,' - ',nombre) AS compp, codigo 
                                FROM gf_tipo_comprobante_pptal WHERE compania = $compania 
                                AND (clasepptal ='13' OR clasepptal ='18' OR clasepptal ='19' )
                                ORDER BY codigo DESC";
                        $rsTcf = $mysqli->query($tcf);
                        ?>
                        <div class="form-group" style="margin-top: 0px">
                            <label for="sltTcf" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Final:</label>
                            <select required="required" name="sltTcf" id="sltTcf" class="select2_single form-control" title="Seleccione Tipo Comprobante Final">
                                <option value>Tipo Comprobante Final</option>
                                <?php while ($filaTci = mysqli_fetch_row($rsTcf)) { ?>
                                <option value="<?php echo $filaTci[2];?>"><?php echo ucwords($filaTci[1]);?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 0px">
                            <label for=sltOpcion" class="control-label col-sm-5"><strong class="obligado"></strong>Informe por:</label>
                            <select name="sltOpcion" id="sltOpcion" class="form-control"  title="Seleccione Tipo Comprobante Final" >
                                <option value="0">Informe</option>
                                <option value="1">Relación Ingresos</option>
                            </select> 
                        </div>
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            <button onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>
            </div>     
        </div>
    </div>
</div>
<!-- Llamado al pie de pagina -->
<script src="js/select/select2.full.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>
  
  <script>
function reporteExcel(){
    var opcion = document.getElementById('sltOpcion').value;
    if(opcion =='0'){
        $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_AUX_PPTALES_INGRESOS_E.php');
    } else {
        $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_RELACION_INGRESOS_E.php');
    }
}

</script>
<script>
function reportePdf(){
    
    var opcion = document.getElementById('sltOpcion').value;
    if(opcion =='0'){
        $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_AUX_PPTALES_INGRESOS.php');
    } else {
        if(opcion =='1'){
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_RELACION_INGRESOS.php');
        }
    }
}
</script>
</body>
</html>
<?php require_once 'footer.php'?>   