<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes 
require_once 'head.php';
require_once('Conexion/conexion.php');
$id= $_GET['id'];
$resul = "SELECT id_unico, fecha, descripcion FROM gf_festivos WHERE md5(id_unico)='$id'";
$resul=$mysqli->query($resul);
$row= mysqli_fetch_row($resul);
?>
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>
label#fecha-error, #descripcion-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
</style>

<script>


$().ready(function() {
  var validator = $("#form").validate({
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

<title>Modificar Festivos</title>
</head>
<body>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
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
            changeYear: true,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
       
         var fechaI = '<?php echo date("d/m/Y", strtotime($row[1]));?>';
        $("#fecha").datepicker({changeMonth: true}).val(fechaI);
        
        
});
</script>


 
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -22px; ">
        <h2 class="tituloform" align="center" >Modificar Festivos</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GF_FESTIVOSJson.php">
                <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                <input type="hidden" name="id" id="id" value="<?php echo $row[0];?>">
                    <div class="form-group" style="margin-top: 0px;">
                        <label for="fecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                        <input class="form-control col-sm-1" type="text" name="fecha" id="fecha" style="width:300px;" title="Ingrese fecha" placeholder="Fecha" readonly="readonly" required="required" >
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label for="descripcion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                        <textarea type="text" name="descripcion" id="descripcion" class="form-control col-sm-1" onkeypress="return txtValida(event)" maxlength="500" title="Ingrese descripción"  placeholder="Descripción" style="margin-top:0.1em; height: 65px;" required="required" ><?php echo ucwords(mb_strtolower($row[2]))?></textarea>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php';?>
</body>