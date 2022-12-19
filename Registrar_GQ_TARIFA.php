<?php
require './Conexion/conexion.php';
require './head.php';
?>
<title>Registrar tarifa</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/jquery.datetimepicker.css">
<link rel="stylesheet" href="css/desing.css">
<style>
    #form>.form-group{
        margin-bottom: 5px !important;
    }
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;font-family: Arial;}
    .campos{padding: 0px;font-size: 10px}
</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px;">
                <h2 id="forma-titulo3" align="center" style="margin-right: 4px; margin-left: 4px;">Registrar tarifa</h2>
                <a href="LISTAR_GQ_TARIFA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:96%; display:inline-block; margin-bottom: 10px; margin-right: -1px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px">Registrar</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form id="form" name="form" class="form-horizontal" method="POST" enctype="" action="json/registrar_GQ_TARIFAJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%;">Los campos marcados con <strong class='obligado'>*</strong> son obligatorios.</p>
                        <input type="hidden" id="action" name="action" value="1">
                        <div class="form-group" style="margin-top: -4px;">
                            <label for="txtNombre" class="control-label col-sm-4 col-md-4 col-lg-4"><strong class="obligado">*</strong>Nombre:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input  required="required" type="text" name="txtNombre" id="txtNombre" class="form-control" placeholder="Nombre" title="Registre nombre de la tarifa" style="width: 100%;" required autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtvalor" class="control-label col-sm-4 col-md-4 col-lg-4"><strong class='obligado'>*</strong>Valor:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtvalor" id="txtvalor" class="form-control" title="Ingrese el valor"  placeholder="Valor" onkeypress="return txtValida(event, 'decimales')" required style='width:100%;' autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltunidadm" class="control-label col-sm-4 col-md-4 col-lg-4"><strong class='obligado'>*</strong>Unidad Medida:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltunidadm" id="sltunidadm" class="select2_single form-control" title="Seleccione Unidad de Medida" required>
                                    <option value="">Unidad Medida</option>
                                    <option value="Hora">Hora</option>
                                    <option value="Minuto">Minuto</option>
                                    <option value="Segundo">Segundo</option>
                                    <option value="Mes">Mes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 11px;">
                            <label for="txtintervalo" class="control-label col-sm-4 col-md-4 col-lg-4"><strong class='obligado'>*</strong>Intervalo (Horas):</label>
                            <div class="col-sm-5 col-md-5 col-lg-5" style="padding-top: 5px;">
                                <input type="text" name="txtintervalo" id="txtintervalo" class="form-control" title="Ingrese Intervalo"  placeholder="Intervalo" onkeypress="return txtValida(event, 'decimales')" style='width:100%;' autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtintervalo" class="col-sm-4 col-md-4 col-lg-4 control-label"><strong class='obligado'>*</strong>Desviación (Horas):</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtdesviacion" id="txtdesviacion" class="form-control" title="Ingrese Desviación"  placeholder="Desviación" onkeypress="return txtValida(event, 'decimales')" style='width:100%;' autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 6px;">
                            <label for="slttarifaaso" class="control-label col-sm-4 col-md-4 col-lg-4"><strong class='obligado'></strong>Tipo Asociada:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="slttarifaaso" id="slttarifaaso" class="select2_single form-control" title="Seleccione Tarifa Asociada">
                                    <option value="">Tarifa Asociada</option>
                                    <?php
                                        $html = "";                                            
                                        $sqper = "SELECT id_unico, nombre, valor FROM gq_fraccion";
                                        $resper = $mysqli->query($sqper);
                                        while ($row = mysqli_fetch_row($resper)) {
                                            $valor = number_format($row[2], 2, ',', '.');
                                            $html .= "<option value='$row[0]'>$row[1] - $valor</option>";
                                        }
                                        echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="form-group" style="margin-top: -2px;">
                            <label for="slttipov" class="control-label col-sm-4 col-md-4 col-lg-4"><strong class='obligado'>*</strong>Tipo Vehiculo:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="slttipov" id="slttipov" class="select2_single form-control" title="Seleccione Tipo Vehiculo" required>
                                    <option value="">Tipo Vehiculo</option>
                                    <?php
                                        $html = "";                                            
                                        $sqper = "SELECT * FROM gp_tipo_vehiculo";
                                        $resper = $mysqli->query($sqper);
                                        while ($row = mysqli_fetch_row($resper)) {
                                            $html .= "<option value='$row[0]'>$row[1]</option>";
                                        }
                                        echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="no" class="control-label col-sm-4 col-md-4 col-lg-4"></label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <button type="" id="btnsavetar" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
    <div class="modal fade" id="mdlinfo" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p id="pinfo"></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="js/script_modal.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="js/script_date.js"></script>
    <script src="js/script_table.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script src="js/script.js"></script> 
    <script>       
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });
        });
                
        let validarNum1 = function (event) {
            event = event || window.event;
            let charCode = event.keyCode || event.which;
            let first = (charCode <= 57 && charCode >= 48);
            let numero = document.getElementById('txtIva').value;
            let char = parseFloat(String.fromCharCode(charCode));
            let num = parseFloat(numero + char);
            let com = parseFloat(100);
            let match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
            let dec = match[0].length;
            if (dec <= 3) {
                if (num < com) {
                    if (charCode === 46) {
                        let element = event.srcElement || event.target;
                        if (element.value.indexOf('.') === -1) {
                            return (charCode = 46);
                        } else {
                            return first;
                        }
                    } else {
                        return first;
                    }
                } else {
                    if (num <= com) {
                        return first;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        };

        let validarNum2 = function (event) {
            event = event || window.event;
            let charCode = event.keyCode || event.which;
            let first = (charCode <= 57 && charCode >= 48);
            let numero = document.getElementById('txtImpo').value;
            let char = parseFloat(String.fromCharCode(charCode));
            let num = parseFloat(numero + char);
            let com = parseFloat(100);
            let match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
            let dec = match[0].length;
            if (dec <= 3) {
                if (num < com) {
                    if (charCode === 46) {
                        let element = event.srcElement || event.target;
                        if (element.value.indexOf('.') === -1) {
                            return (charCode = 46);
                        } else {
                            return first;
                        }
                    } else {
                        return first;
                    }
                } else {
                    if (num <= com) {
                        return first;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        };

    </script>
</body>
</html>
