<?php 
################MODIFICACIONES####################
#03/03/2017 |Erica G |Valor opcion combos
#11/02/2017 |Erica G //Datapicker
#10-02-2017 |Erica G // Busqueda rádipa de cuentas
##################################################

  require_once('Conexion/conexion.php');
 require_once 'head.php'; 
 $anno = $_SESSION['anno'];
 $compania = $_SESSION['compania'];
 ?>
<title>Auxiliares Contables</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltTci-error, #sltTcf-error, #fechaini-error, #fechafin-error, #sltctai-error, #sltctaf-error  {
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
       
        
        $("#fechaini").datepicker({changeMonth: true,}).val(fecAct);
        $("#fechafin").datepicker({changeMonth: true}).val(fecAct);
        
        
});
</script>
<!-- contenedor principal -->  
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Auxiliares Contables</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                <div class="form-group">
                <?php
                $tci= "SELECT id_unico,sigla, nombre FROM gf_tipo_comprobante WHERE compania = $compania ORDER BY sigla ASC";
                $rsTci = $mysqli->query($tci);
                ?> 
                <div class="form-group" style="margin-top: -5px">
                    <label for="sltTci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Inicial:</label>
                    <select  name="sltTci" id="sltTci" class="select2_single form-control" title=
                            "Seleccione Tipo comprobante inicial" style="height: 30px" required>
                        <option value="">Tipo Comprobante Inicial</option>
                    <?php 
                        while ($filaTci = mysqli_fetch_row($rsTci)) 
                    { 
                    ?>
                        <option value="<?php echo $filaTci[1];?>"><?php echo $filaTci[1].' - '.ucwords(mb_strtolower($filaTci[2]));?></option>                                
                    <?php 
                    }
                     ?>                                    
                    </select>
                </div>
                <?php 
                $tcf= "SELECT id_unico,sigla,nombre  FROM gf_tipo_comprobante WHERE compania = $compania  ORDER BY sigla DESC";
                $rsTcf = $mysqli->query($tcf);
                ?>
                <div class="form-group" style="margin-top: -5px">
                    <label for="sltTcf" class="control-label col-sm-5">
                            <strong class="obligado">*</strong>Tipo Comprobante Final:
                    </label>
                    <select name="sltTcf" class="select2_single form-control" id="sltTcf" title="Seleccione Tipo comprobante final" style="height: 30px"  required >
                        <option value="">Tipo Comprobante Final</option>
                        <?php 
                        while ($filaTcf = mysqli_fetch_row($rsTcf)) { ?>
                    <option value="<?php echo $filaTcf[1];?>"><?php echo ($filaTcf[1].' - '. ucwords(mb_strtolower($filaTcf[2]))); ?></option>
                        <?php
                        }
                        ?>
                    </select>   
                </div>
                <div class="form-group" style="margin-top: -5px;">
                     <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                     <input class="form-control" type="text" name="fechaini" id="fechaini"  value="<?php echo date("Y-m-d");?>" required>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                     <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                     <input class="form-control" type="text" name="fechafin" id="fechafin"  value="<?php echo date("Y-m-d");?>" required>
                </div>
                <?php 
                    $cuentaI = "SELECT id_unico,CONCAT(codi_cuenta,' - ',nombre) from gf_cuenta where parametrizacionanno = $anno ORDER BY codi_cuenta ASC";
                    $rsctai = $mysqli->query($cuentaI);
                ?>
                <div class="form-group" style="margin-top: -10px">
                    <label for="sltctai" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Cuenta Inicial:</label>
                    <select name="sltctai" id="sltctai" required style="height: auto" class="select2_single form-control" title="Seleccione Cuenta inicial">
                        <option value="">Tipo Cuenta Inicial</option>
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
            <?php 
                $cuentaF = "SELECT id_unico, codi_cuenta, nombre from gf_cuenta where parametrizacionanno = $anno ORDER BY codi_cuenta DESC";
                $rsctaf = $mysqli->query($cuentaF);
            ?>
             <div class="form-group" style="margin-top: 0px">
                <label for="sltctaf" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Cuenta Final:</label>
                <select name="sltctaf" id="sltctaf" style="height: auto" class=" select2_single form-control" title=
                        "Seleccione Tipo cuenta final" required>
                    <option value="">Tipo Cuenta Final</option>
                <?php 
                    while ($filactaf = mysqli_fetch_row($rsctaf)) 
                { 
                ?>
                    <option value="<?php echo $filactaf[0];?>"><?php echo ucwords(mb_strtolower($filactaf[1].' - '.$filactaf[2]));?></option>                                
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
<script>
function reporteExcel(){
   $('form').attr('action', 'informes/generar_INF_AUX_CNT_EXCEL.php');
}

function reportePdf(){
    $('form').attr('action', 'informes/generar_INF_AUX_CONTABLES.php');
}


$("#fechaini").change(function(){
    var fechain= document.getElementById('fechaini').value;
    $( "#fechafin" ).datepicker( "destroy" );
    $( "#fechafin" ).datepicker({ changeMonth: true, minDate: fechain});
})
</script>
</div>
<?php require_once 'footer.php' ?>  
</body>
</html>