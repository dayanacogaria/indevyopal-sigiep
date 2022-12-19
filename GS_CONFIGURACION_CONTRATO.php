<?php 
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$anno   = $_SESSION['anno'];
$con    = new ConexionPDO();
$fc     = $con->Listar("SELECT DATE_FORMAT(fecha_contrato,'%d/%m/%Y') FROM gf_tercero WHERE id_unico = ".$_SESSION['compania']);
$fecha  = $fc[0][0];
?>
<title>Configuración Contrato</title> 
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #fecha-error  {
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

    $(document).ready(function ()
    {
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            changeYear: true,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha").datepicker({changeMonth: true}).val();

    });
</script>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Configuración Contrato</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()" >  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="fecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fecha:</label>
                            <input value="<?php echo $fecha?>" autocomplete="off" required="required" class="col-sm-2 input-sm" type="text" name="fecha" id="fecha" title="Ingrese Fecha">
                        </div> 
                        
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button style="margin-left:10px;" class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i> Guardar</button>
                        </div>
                    </div>
                </form> 
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
    function guardar(){
        var formData = new FormData($("#form")[0]);  
        jsShowWindowLoad('Guardando Información...');
        var form_data = { action:1 };
        $.ajax({
            type: 'POST',
            url: "jsonSistema/consultas.php?case=8",
            data:formData,
            contentType: false,
            processData: false,
            success: function(response)
            { 
                jsRemoveWindowLoad();
                console.log(response+'G');
                if(response ==0){
                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                        $("#modalMensajes").modal("hide");
                    }) 
                } else {
                    $("#mensaje").html('Información Guardada Correctamente');
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                        document.location.reload();
                    })
                }
                    
            }
        })
    }
</script>
<div class="modal fade" id="modalMensajes" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'?>  
</div>
</body>
</html>