<?php 
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#01/08/2018 | Erica G. | Archivo Creado
#####################################################################################
require_once('Conexion/ConexionPDO.php');
require_once 'head.php';
$con    = new ConexionPDO();
$anno   = $_SESSION['anno']; 
$compania = $_SESSION['compania'];
?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #fechaI-error, #fechaF-error{
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
<title>Reconstruir Comprobantes Financieros</title>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Reconstruir Comprobantes Financieros</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="fechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechaI" id="fechaI" title="Ingrese Fecha Inicial" autocomplete="off">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="fechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechaF" id="fechaF" title="Ingrese Fecha Final" autocomplete="off">
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo:</label>
                            <select name="tipo" id="tipo" class="select2_single form-control" title="Seleccione Tipo">
                                <option value="">Tipo</option>
                                <option value="1">Facturación</option>
                                <option value="2">Recaudo</option>
                            </select>
                        </div>
                        <!--TIPO FACTURA--->
                        <div class="form-group" style="margin-top: 10px; display:none" id="divTipoF">
                            <label for="tipof" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Factura:</label>
                            <select name="tipof" id="tipof" class="select2_single form-control" title="Seleccione Tipo Factura">
                                <?php $rowf= $con->Listar("SELECT * FROM gp_tipo_factura WHERE compania = $compania");
                                echo '<option value="">Tipo Factura</option>';
                                for ($i = 0; $i < count($rowf); $i++) {
                                   echo '<option value="'.$rowf[$i][0].'">'.$rowf[$i][2].' - '.$rowf[$i][1].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <!--TIPO RECAUDO--->
                        <div class="form-group" style="margin-top: 10px; display:none" id="divTipoP">
                            <label for="tipoP" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Pago:</label>
                            <select name="tipoP" id="tipoP" class="select2_single form-control" title="Seleccione Tipo Pago">
                                <?php $rowf= $con->Listar("SELECT * FROM gp_tipo_pago WHERE compania = $compania");
                                echo '<option value="">Tipo Pago</option>';
                                for ($i = 0; $i < count($rowf); $i++) {
                                   echo '<option value="'.$rowf[$i][0].'">'.$rowf[$i][1].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button style="margin-left:10px;" type="submit" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>
            </div>     
        </div>
    </div>
</div>
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
<script src="js/select/select2.full.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
</script>
<script>
function guardar(){
    jsShowWindowLoad('Restaurando Información...');
   var formData = new FormData($("#form")[0]);  
    $.ajax({
        type: 'POST',
        url: "jsonPptal/gf_facturaJson.php?action=37",
        data:formData,
        contentType: false,
        processData: false,
        success: function(response)
        {
            jsRemoveWindowLoad();
            console.log(response);
            if(response==1){
                $("#mensaje").html('Información Guardada Correctamente');
                $("#modalMensajes").modal("show");
                $("#Aceptar").click(function(){
                    document.location='GF_RECONSTRUIR_COMPROBANTES_FINANCIEROS.php';
                })
            } else {
                $("#mensaje").html('No Se Ha Podido Guardar Información');
                $("#modalMensajes").modal("show");
                $("#Aceptar").click(function(){
                    $("#modalMensajes").modal("hide");
                })

            }
        }
    })
}
$("#tipo").change(function(){
    var tipo = $("#tipo").val();
    if(tipo !=""){
        if(tipo==1){
            $("#divTipoF").css("display","block");
            $("#divTipoP").css("display","none");
        } else {
            $("#divTipoP").css("display","block");
            $("#divTipoF").css("display","none");
        }
    } else {
        $("#divTipoF").css("display","none");
        $("#divTipoP").css("display","none");
    }
})
</script>
</body>
</html>
<?php require_once 'footer.php'?>  
