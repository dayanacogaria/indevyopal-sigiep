<?php
#************************************* Actualizaciones ******************************#
#10/01/2019 | ERICA G. | ARCHIVO CREADO
#************************************************************************************#
require_once('Conexion/conexion.php');
require_once 'head.php';
$compania   = $_SESSION['compania'];
$annio      = $_SESSION['anno'];
?>
<title>Conciliación Ingresos - Gastos</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #sltAnnio-error, #sltmes-error, #fuenteI-error, #fuenteF-error  {
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

    $().ready(function () {
        var validator = $("#form").validate({
            ignore: "",

            errorPlacement: function (error, element) {

                $(element)
                        .closest("form")
                        .find("label[for='" + element.attr("id") + "']")
                        .append(error);
            },
        });

        $(".cancel").click(function () {
            validator.resetForm();
        });
    });
</script>
<style>
    .form-control {font-size: 12px;} 
</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -20px"> 
                <h2 align="center" class="tituloform">Conciliación Ingresos - Gastos Por Fuente </h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                                <select name="sltAnnio" id="sltAnnio" class="select2_single form-control" title="Seleccione Año" style="height: auto " required>
                                    <option value="">Año</option>
                                    <?php
                                    $annio = "SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
                                    $rsannio = $mysqli->query($annio);
                                    while ($filaAnnio = mysqli_fetch_row($rsannio)) { 
                                        echo '<option value="'.$filaAnnio[0].'">'.$filaAnnio[1].'</option>';                                
                                    } ?>                                    
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltmes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Acumulado al Mes de:</label>
                                <select name="sltmes" id="sltmes" class="select2_single form-control" title="Mes Inicial" required>
                                    <option value>Mes Acumulado</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="fuenteI" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fuente Inicial:</label>
                                <select name="fuenteI" id="fuenteI" class="select2_single form-control" title="Seleccione Fuente Inicial" required>
                                    <option value>Fuente Inicial</option>                                  
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="fuenteF" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fuente Final:</label>
                                <select name="fuenteF" id="fuenteF" class="select2_single form-control" title="Seleccione Fuente Final" required>
                                    <option value>Fuente Final</option>                                  
                                </select>
                            </div>
                            <div class="col-sm-10" style="margin-top:0px;margin-left:700px" >
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
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });
        });
    </script>
    <script>
        $("#sltAnnio").change(function () { 
            var form_data={action: 1, annio :$("#sltAnnio").val()};
            var optionMI ="<option value=''>Mes Acumulado</option>";
            $.ajax({
               type:'POST', 
               url:'jsonPptal/consultasInformesCnt.php',
               data: form_data,
               success: function(response){
                   optionMI =optionMI+response;
                   $("#sltmes").html(optionMI).focus();              
               }
            });
            var form_data={action: 23, annio :$("#sltAnnio").val()};
            var optionF ="<option value=''>Fuente Inicial</option>";
            $.ajax({
               type:'POST', 
               url:'jsonPptal/consultasInformesCnt.php',
               data: form_data,
               success: function(response){
                   optionF =optionF+response;
                   $("#fuenteI").html(optionF).focus();              
               }
            });
            var form_data={action: 24, annio :$("#sltAnnio").val()};
            var optionFf ="<option value=''>Fuente Final</option>";
            $.ajax({
               type:'POST', 
               url:'jsonPptal/consultasInformesCnt.php',
               data: form_data,
               success: function(response){
                   optionFf =optionFf+response;
                   $("#fuenteF").html(optionFf).focus();              
               }
            });


        });
    </script>
<?php require_once 'footer.php' ?>  
    <script>
        function reporteExcel() {
            $('form').attr('action', 'informes/INFORMES_PPTAL/INF_CONCILIACION_INGRESOS_GASTOS.php?t=2');
        }
    </script>
    <script>
        function reportePdf() {
            $('form').attr('action', 'informes/INFORMES_PPTAL/INF_CONCILIACION_INGRESOS_GASTOS.php?t=1');
        }
    </script>
</body>
</html>