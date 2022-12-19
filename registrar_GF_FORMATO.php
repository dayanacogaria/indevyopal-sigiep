<?php
require_once ('head.php');
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
?>		
<title>Registrar Formato</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script type="text/javascript">
    $(function () {
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
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#txtFechaVersion").datepicker({changeMonth: true}).val();
        $("#txtFechaVersion").val();
    });
</script>
</head>
<body>
    <div class="container-fluid text-left">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-8 text-left">
                <h2 class="tituloform" align="center">Registrar Formato</h2>
                <a href="listar_GF_FORMATO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Formato</h5>
                <div class="client-form contenedorForma" style="margin-top: -7px">
                    <form name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="json/procesos_GF_FORMATO.php?action=registrar">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <div class="form-group" style="margin-top: 5px;">
                            <label for="txtNombre" class="col-sm-5 control-label">
                                <strong class="obligado">*</strong>Nombre:
                            </label>
                            <input type="text" name="txtNombre" id="txtNombre" class="form-control" maxlength="100" title="Ingresa el nombre" placeholder="Nombre" onkeypress="return txtValida(event, 'num_car')" required="">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="txtCodigo" class="col-sm-5 control-label">
                                <strong class="obligado"></strong>Código Calidad:
                            </label>
                            <input type="text" name="txtCodigo" id="txtCodigo" class="form-control" maxlength="100" title="Ingresa el Código de Calidad" placeholder="Código de Calidad" onkeypress="return txtValida(event, 'num_car')">
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="txtVersion" class="col-sm-5 control-label">
                                Versión:
                            </label>
                            <input type="text" name="txtVersion" id="txtVersion" class="form-control" maxlength="100" title="Ingrese la versión del formato" placeholder="Versión" onkeypress="return txtValida(event, 'sin_espcio')">
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="txtFechaVersion" class="col-sm-5 control-label">
                                <strong class="obligado">*</strong>Fecha Versión:
                            </label>
                            <input type="text" name="txtFechaVersion" id="txtFechaVersion" class="form-control" title="Seleccione la fecha" placeholder="Fecha Versión" required>
                        </div>
                        <div class="form-group" style="margin-top:-14px">
                            <label for="optCheque" class="col-sm-5 control-label">
                                Es formato de cheque?
                            </label>
                            <input type="radio" name="optCheque" id="optCheque1" title="Si este formto es cheque" value="1">SI
                            <input type="radio" name="optCheque" id="optCheque2" title="No es formato de cheque" value="2" checked="">NO
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="txtDescripcion" class="control-label col-sm-5">
                                Descripción:
                            </label>
                            <textarea type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" title="Ingrese descripción del formato" placeholder="Descripción"></textarea>
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="plantilla" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Plantilla:</label>
                            <select name="plantilla" id="plantilla" class="select2_single form-control" title="Seleccione Plantilla">
                                <option value="">Plantilla</option>
                                <?php 
                                $row = $con->Listar("SELECT * FROM gf_plantilla");
                                for ($i = 0; $i < count($row); $i++) {
                                    echo '<option value="'.$row[$i][0].'">'.$row[$i][1].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-6 col-sm-2" style="margin-top:-22px" >
                <table class="tablaC table-condensed" style="margin-left: -3px; ">
                    <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px; height:35px">Adicional</h2>
                    </th>
                    </thead>
                    <tbody>                           
                        <tr>
                            <td align="center">
                                <a href="GF_PLANTILLA.php" target="_blank" class="btn btnInfo btn-primary">VER<br/> PLANTILLAS</a><br/>                              
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="js/select/select2.full.js"></script>
    <?php require_once ('footer.php');?>
    <script>
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });
        });
    </script>
</body>
</html>