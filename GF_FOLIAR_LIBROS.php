<?php
#######################################################################################################
#                           Modificaciones
#######################################################################################################
#29/09/2017 |Erica G. | ARCHIVO CREADO
#######################################################################################################
require_once ('Conexion/conexion.php');
require_once 'head_listar.php';
#*****Consulta Libros*****#
$sql = "SELECT id_unico, CONCAT(codigo_libro,' - ', nombre_libro) FROM gf_libros ";
$sql = $mysqli->query($sql);
?>
<title>Foliar Libros</title>
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
        label#libro-error, #num_inicial-error, #num_final-error{
            display: block;
            color: #155180;
            font-weight: normal;
            font-style: italic;

        }
    </style>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Foliar Libros</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top:0px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="libro" class="col-sm-5 control-label"><strong class="obligado">*</strong>Libro:</label>
                            <select name="libro" id="libro"  class="select2_single form-control" title="Seleccione Libro"  required="required">
                               <option value="">Libro</option>
                               <?php while ($row = mysqli_fetch_row($sql)) {
                                   echo '<option value="'.$row[0].'">'.$row[1].'</option>';
                               }?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="num_inicial" class="col-sm-5 control-label"><strong class="obligado">*</strong>Número Inicial:</label>
                            <input type="text" name="num_inicial" id="num_inicial" class="form-control" required onkeypress="return txtValida(event, 'num')" title="Número Inicial" placeholder="Número Inicial" onchange="validarnum()">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="num_final" class="col-sm-5 control-label"><strong class="obligado">*</strong>Número Final:</label>
                            <input type="text" name="num_final" id="num_final" class="form-control" required onkeypress="return txtValida(event, 'num')" title="Número Final" placeholder="Número Final" onchange="validarnum()">
                        </div>
                        <div class="form-group" style="margin-top:-10px;">
                            <label for="encabezado" class="col-sm-5 control-label"><strong class="obligado">*</strong>Encabezado:</label>
                            <input type="radio" id="encabezado" name="encabezado" value="Si" checked="checked">Sí
                            <input type="radio" id="encabezado" name="encabezado" value="No" >No
                        </div>
                        <div class="form-group" style="margin-top:-10px;">
                            <label for="papel" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Papel:</label>
                            <input type="radio" id="papel" name="papel" value="Letter" checked="checked">Carta
                            <input type="radio" id="papel" name="papel" value="Legal">Oficio
                        </div>
                        <div class="form-group" style="margin-top:-10px;">
                            <label for="orientacion" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Orientación:</label>
                            <input type="radio" id="orientacion" name="orientacion" value="P" checked="checked">Vertical
                            <input type="radio" id="orientacion" name="orientacion" value="L">Horizontal
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                              <button onclick="reportePdf()"  class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Generar</button>              
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    function reportePdf(){
        $('form').attr('action', 'informes_libros/INF_FOLIAR_LIBRO.php');
    }
    </script>
    <script>
    function validarnum(){
        
        var numI =$("#num_inicial").val();
        var numF =$("#num_final").val();
        if(numI!="" && numF!=""){
            if(numF<numI){
                $("#myModal").modal("show");
                $("#num_inicial").val("");
                $("#num_final").val("");
            }
        }else {
        } 
    }
    </script>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Número Inicial tiene que ser menor al Número Final.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver11" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
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

