<?php
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#29/09/2017 |Erica G. | ARCHIVO CREADO
#######################################################################################################
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
require_once 'head_listar.php';
$compania = $_SESSION['compania'];
$con = new ConexionPDO();
#*************      Tipos de Comprobante    *************#
$tci =$con->Listar("SELECT id_unico, UPPER(sigla), LOWER(nombre) FROM gf_tipo_comprobante ORDER BY id_unico ASC");
$tcf =$con->Listar("SELECT id_unico, UPPER(sigla), LOWER(nombre) FROM gf_tipo_comprobante ORDER BY id_unico DESC");
#*****Consulta Años*****#
$sql = "SELECT id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC ";
$sql = $mysqli->query($sql);
?>
<title>Libro Diario Oficial</title>
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
        label#anno-error, #mes-error, #informe-error, #TipoComprobanteI-error, #TipoComprobanteF-error{
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
                <h2 align="center" class="tituloform" style="margin-top:-3px">Libro Diario Oficial</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="anno" class="col-sm-5 control-label"><strong class="obligado">*</strong>Año:</label>
                            <select name="anno" id="anno"  class="select2_single form-control" title="Seleccione Año"  required="required">
                               <option value="">Año</option>
                               <?php while ($row = mysqli_fetch_row($sql)) {
                                   echo '<option value="'.$row[0].'">'.$row[1].'</option>';
                               }?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="mes" class="col-sm-5 control-label"><strong class="obligado">*</strong>Mes:</label>
                            <select name="mes" id="mes"  class="select2_single form-control" title="Seleccione Mes"  required="required">
                               <option value="">Mes</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="informe" class="col-sm-5 control-label"><strong class="obligado">*</strong>Informe:</label>
                            <select name="informe" id="informe"  class="select2_single form-control" title="Seleccione Informe"  required="required">
                               <option value="">Informe</option>
                               <option value="1">Detallado</option>
                               <option value="2">Consolidado</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="TipoComprobanteI" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Comprobante Inicial:</label>
                            <select name="TipoComprobanteI" id="TipoComprobanteI"  class="select2_single form-control" title="Seleccione Tipo Comprobante Inicial" required="required">
                               <option value="">Tipo Comprobante Inicial</option>
                               <?php 
                               for ($i = 0; $i < count($tci); $i++) {
                                   echo '<option value="'.$tci[$i][0].'">'.$tci[$i][1].' - '.ucwords($tci[$i][2]).'</option>';
                               }
                               ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="TipoComprobanteF" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Comprobante Final:</label>
                            <select name="TipoComprobanteF" id="TipoComprobanteF"  class="select2_single form-control" title="Seleccione Tipo Comprobante Final" required="required">
                               <option value="">Tipo Comprobante Final</option>
                               <?php 
                               for ($i = 0; $i < count($tcf); $i++) {
                                   echo '<option value="'.$tcf[$i][0].'">'.$tcf[$i][1].' - '.ucwords($tcf[$i][2]).'</option>';
                               } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top:-5px;">
                            <label for="foliador" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Foliador:</label>
                            <input type="radio" id="foliador" name="foliador" value="Si" checked="checked">Sin Foliador<br/>
                            <input type="radio" id="foliador" name="foliador" value="No">Con Foliador
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-7 control-label"></label>
                            <button onclick="reportePdf()"  class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            <button onclick="reporteExcel()"  class="btn sombra btn-primary" style="margin-left:-1px" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>              
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    function reportePdf(){
        $('form').attr('action', 'informes_libros/INF_LIBRO_DIARIO.php?tipo=pdf');
    }
    function reporteExcel(){
        $('form').attr('action', 'informes_libros/INF_LIBRO_DIARIO.php?tipo=excel');
    }
    </script>
    <script>
        $("#anno").change(function(){
            var form_data={action: 1, anno :$("#anno").val()};
            var optionMI ="<option value=''>Mes</option>";
            $.ajax({
               type:'POST', 
               url:'jsonPptal/consultasGenerales.php',
               data: form_data,
               success: function(response){
                   optionMI =optionMI+response;
                   $("#mes").html(optionMI).focus();              
               }
            });
       });
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

