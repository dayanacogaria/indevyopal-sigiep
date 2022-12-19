<?php
################ MODIFICACIONES ####################

############################################

require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$con = new ConexionPDO();
$meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
?>
<title>Resoluciones</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #organismo-error, #tipo-error, #mes-error, #dias-error, #comparendo-error{
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
            rules: {
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


        $("#dias").datepicker({changeMonth: true,}).val();


    });
</script>


<!-- contenedor principal -->
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once ('menu.php'); ?>
        <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px">
            <h2 align="center" class="tituloform">Resoluciones </h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/INF_Resolucion_GU.php" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -10px">
                            <label for="organismo" class="col-sm-5 control-label"><strong class="obligado">*</strong>Organismo de Transito:</label>
                            <select name="organismo" id="organismo" title="Seleccione Organismo" style="width: 300px;height: 30px" class="select2_single form-control col-sm-1"   required>
                                <?php
                                $rowt = $con->Listar("SELECT id_unico, LOWER(nombre) FROM gf_sucursal ORDER by id_unico");
                                echo '<option value="">Sucursal</option>';
                                for ($i = 0; $i < count($rowt); $i++) {
                                    echo '<option value="'.$rowt[$i][0].'">'. ucwords($rowt[$i][1]).'</option>';
                                }?> 
                            </select> 
                        </div>
                        <script>
                            $("#organismo").change(function(){
                                $("#divComparendos").css('display','none');
                                $("#divDias").css('display','none');
                                $("#divMes").css('display','none');
                                $("#comparendo").removeAttr('required');
                                $("#mes").removeAttr('required');
                                $("#dias").removeAttr('required');
                                $("#mes").val('');
                                $("#dias").val('');
                                $("#comparendo").val('');
                                $("#tipo").val('');
                                
                            })
                        </script>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="tipo" class="col-sm-5 control-label"><strong class="obligado">*</strong>Generar Por:</label>
                            <select name="tipo" id="tipo" title="Seleccione Tipo" style="width: 300px;height: 30px" class="select2_single form-control col-sm-1" required>
                                <option value="">Seleccione Tipo</option>
                                <option value="1">Mes</option>
                                <option value="2">Día</option>
                                <option value="3">Comparendo</option>
                            </select>  
                        </div>
                        
                        <div class="form-group" id="divMes" style="margin-top: -10px; display: none" >
                            <label for="mes" class="col-sm-5 control-label"><strong class="obligado">*</strong>Mes:</label>
                            <select name="mes" id="mes" title="Seleccione Mes" style="width: 300px;height: 30px" class="select2_single form-control col-sm-1" >
                                <option value="">Mes</option>
                            </select>
                        </div>
                        <div class="form-group" id="divDias" style="margin-top: -10px; display: none"  >
                            <label for="dias" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Día:</label>
                            <input class="form-control" type="text" name="dias" id="dias" title="Ingrese el día" >
                        </div>
                        
                        <div class="form-group" id="divComparendos" style="margin-top: -10px;  display: none">
                            <label for="comparendo" class="col-sm-5 control-label"><strong class="obligado">*</strong>Comparendo:</label>
                            <select name="comparendo" id="comparendo" title="Seleccione Comparendo" style="width: 300px;height: 30px" class="select2_single form-control col-sm-1" >
                                <option value="">Comparendo</option>
                            </select>
                        </div>
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button type="submit" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Imprimir</button>              
                        </div>
                        <script>
                            $("#tipo").change(function(){
                                var tipo = $("#tipo").val();
                                /**Mes**/
                                if(tipo==1){
                                    $("#divMes").css('display','block');
                                    $("#divDias").css('display','none');
                                    $("#divComparendos").css('display','none');
                                    $("#mes").prop("required","true");
                                    $("#dias").removeAttr('required');
                                    $("#comparendo").removeAttr('required');
                                    $("#dias").val('');
                                    $("#comparendo").val('');
                                    /**********Buscar Comparendos Por Sucursal*************/
                                    var form_data ={estruc:31, sucursal:$("#organismo").val()}
                                    var option ="<option value=''>Mes</option>";
                                    $.ajax({
                                        type:'POST', 
                                        url:'jsonPptal/consultas.php',
                                        data: form_data,
                                        success: function(response){
                                            console.log('res'+response);
                                            option =option+response;
                                            $("#mes").html(option).focus();              
                                        }
                                    });
                                /**Dia**/    
                                } else if(tipo==2){
                                    $("#divDias").css('display','block');
                                    $("#divMes").css('display','none');
                                    $("#divComparendos").css('display','none');
                                    $("#dias").prop("required","true");
                                    $("#mes").removeAttr('required');
                                    $("#comparendo").removeAttr('required');
                                    $("#mes").val('');
                                    $("#comparendo").val('');
                                /**Comparendo**/    
                                } else if(tipo==3){
                                    $("#divComparendos").css('display','block');
                                    $("#divDias").css('display','none');
                                    $("#divMes").css('display','none');
                                    $("#comparendo").prop("required","true");
                                    $("#mes").removeAttr('required');
                                    $("#dias").removeAttr('required');
                                    $("#mes").val('');
                                    $("#dias").val('');
                                    /**********Buscar Comparendos Por Sucursal*************/
                                    var form_data ={estruc:30, sucursal:$("#organismo").val()}
                                    var option ="<option value=''>Comparendo</option>";
                                    $.ajax({
                                        type:'POST', 
                                        url:'jsonPptal/consultas.php',
                                        data: form_data,
                                        success: function(response){
                                            console.log(response);
                                            option =option+response;
                                            $("#comparendo").html(option).focus();              
                                        }
                                     });
                                /**Nada**/    
                                } else {
                                    $("#divComparendos").css('display','none');
                                    $("#divDias").css('display','none');
                                    $("#divMes").css('display','none');
                                    $("#comparendo").removeAttr('required');
                                    $("#mes").removeAttr('required');
                                    $("#dias").removeAttr('required');
                                    $("#mes").val('');
                                    $("#dias").val('');
                                    $("#comparendo").val('');
                                }
                            })
                        </script>
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
    </script>
</div>
<?php require_once 'footer.php' ?>
</body>
</html>