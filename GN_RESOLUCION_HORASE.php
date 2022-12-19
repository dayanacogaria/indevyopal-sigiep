<?php 
  require_once('Conexion/conexion.php');
  require_once 'head.php'; 
  $anno = $_SESSION['anno'];
  $compania = $_SESSION['compania'];?>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #fechaini-error, #codigoRes-error, #sltPeriodo-error {
    display: block;
    color: #155180;
    font-weight: normal;
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
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>

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
        
        
});
</script>
<title>Resolución Horas Extras</title>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Resolución Horas Extras</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                            <div class="form-group" style="margin-top: 0px;">
                            <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Resolución:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechaini" id="fechaini" title="Ingrese Fecha ">
                        </div>
                        <div class="form-group" style="margin-top: 0px">
                            <label for="codigoRes" class="col-sm-5 control-label"><strong style="color:#03C1FB;" class="obligado">*</strong>Código Resolución:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="codigoRes" id="codigoRes" title="Ingrese Código ">
                        </div>                       

                        <?php
                        $per = "SELECT  id_unico, codigointerno FROM gn_periodo
                                WHERE   id_unico            != 1 
                                and tipoprocesonomina = 1 
                                AND     parametrizacionanno = '$anno'";
                        $periodo = $mysqli->query($per);
                        ?>

                        <div class="form-group" style="margin-top: 0px">
                            <label for="sltPeriodo" class="control-label col-sm-5"><strong class="obligado">*</strong>Periodo</label>
                            <select  name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="height: 30px;" class="select2_single form-control" required="required" >
                                    <option value="">Periodo</option>
                                    <?php
                                    while($rowE = mysqli_fetch_row($periodo)){
                                        echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                    }
                                    ?>
                                </select>
                        </div>

                        <div class="form-group" style="margin-top: -5px">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button id="enviar" type="submit"  class="btn sombra btn-primary" title="Generar reporte PDF" style="margin-top: 15px;"><i>Generar</i></button>
                        </div>
                    </div>
                </form>
            </div>     
        </div>
    </div>
</div>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script src="js/select/select2.full.js"></script>
<script>
    $("#sltPeriodo").select2();
       
    $("#enviar").click(function(){
        $("#form").attr("action", "informes_nomina/INF_RESOLUCION_HORAS_EXTRAS.php");
    });           
</script>
</body>
</html>
<?php require_once 'footer.php'?>   