<?php
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#05/10/2017 |Erica G. | ARCHIVO CREADO
#######################################################################################################
require_once ('Conexion/conexion.php');
require_once 'head_listar.php';
$compania = $_SESSION['compania'];
#*****Consulta Años*****#
$sql = "SELECT id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC ";
$sql = $mysqli->query($sql);
?>
<title>Estado De La Actividad Economica, Social Y Financiera</title>
</head>
<body> 
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
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
        body{
            font-size: 12px;
        }       
        label#sltAnnio-error, #sltmesi-error, #tipoInforme-error,#sltcodi-error,#sltcodf-error {
            display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;

        }
    </style>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Estado De La Actividad Economica, Social Y Financiera</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                            <select name="sltAnnio" id="sltAnnio" class="select2_single form-control" title="Seleccione Año" style="height: auto " required>
                                <option value="">Año</option>
                                <?php 
                                $annio = "SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
                                $rsannio = $mysqli->query($annio);
                                while ($filaAnnio = mysqli_fetch_row($rsannio)) { ?>
                                     <option value="<?php echo $filaAnnio[0];?>"><?php echo $filaAnnio[1];?></option>                                
                                <?php }?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltmesi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Inicial:</label>
                            <select required name="sltmesi" id="sltmesi" style="height: auto" class="select2_single form-control" title="Mes Inicial" >
                                <option value="">Mes</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="tipoInforme" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Informe:</label>
                            <select required name="tipoInforme" id="tipoInforme" style="height: auto" class="select2_single form-control" title="Tipo Informe" >
                                <option value="">Tipo Informes</option>
                                <option value="1">Estado Situación Financiera</option>
                                <option value="2">Estado De Resultados</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Inicial:</label>
                            <select required name="sltcodi" id="sltcodi" style="height: auto" class="select2_single form-control" title="Seleccione Cuenta Inicial">
                                <option value="">Código Inicial</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Final:</label>
                            <select required name="sltcodf" id="sltcodf" style="height: auto" class="select2_single form-control" title="Seleccione Cuenta Final">
                                <option value="">Código Final</option>
                            </select>
                        </div>
                        <div class="form-group" id="digitos" >
                            <label for="sltInforme" class="control-label col-sm-5">Número de dígitos:</label>
                            <input type="text" name="ndigitos" id="ndigitos" class="form-control">
                        </div>
                        <div class="form-group" style="margin-top:-5px;">
                            <label for="foliador" class="col-sm-5 control-label"><strong class="obligado">*</strong>Foliador:</label>
                            <input type="radio" id="foliador" name="foliador" value="Si" checked="checked">Sin Foliador<br/>
                            <input type="radio" id="foliador" name="foliador" value="No">Con Foliador
                        </div>
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>
                        <br/><br/><br/>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    function reportePdf(){
        $('form').attr('action', 'informes_libros/INF_ESTADO_ACTIVIDAD_ECONOMICA.php?t=1');
    }
    function reporteExcel(){
        $('form').attr('action', 'informes_libros/INF_ESTADO_ACTIVIDAD_ECONOMICA.php?t=2');
    }
    </script>
    <script>    
        $("#sltAnnio").change(function(){

           var form_data={action: 1, annio :$("#sltAnnio").val()};
           var optionMI ="<option value=''>Mes</option>";
           $.ajax({
              type:'POST', 
              url:'jsonPptal/consultasInformesCnt.php',
              data: form_data,
              success: function(response){
                  optionMI =optionMI+response;
                  $("#sltmesi").html(optionMI).focus();              
              }
           });
        });
    </script>
    <script>
        $("#tipoInforme").change(function(){
           
           var form_data={action: 5, annio :$("#sltAnnio").val(),tipo: $("#tipoInforme").val(), };
           var optionCI ="<option value=''>Código Inicial</option>";
           $.ajax({
              type:'POST', 
              url:'jsonPptal/consultasInformesCnt.php',
              data: form_data,
              success: function(response){
                  optionCI =optionCI+response;
                  $("#sltcodi").html(optionCI).focus();              
              }
           });
           var form_data={action: 6, annio :$("#sltAnnio").val(),tipo: $("#tipoInforme").val(),};
           var optionCF ="<option value=''>Código Final</option>";
           $.ajax({
              type:'POST', 
              url:'jsonPptal/consultasInformesCnt.php',
              data: form_data,
              success: function(response){
                  optionCF =optionCF+response;
                  $("#sltcodf").html(optionCF).focus();              
              }
           });
        })
    </script>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true,
            });
        });
    </script>
</body>
</html>

