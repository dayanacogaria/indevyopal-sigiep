<?php 
#################################################################################################
#  ********************************** Modificaciones **********************************         #
#################################################################################################
#15/08/2018 |Erica G. | ARCHIVO CREADO 
#################################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$con  = new ConexionPDO();

#** Banco Inicial **#   
$rowbI = $con->Listar("SELECT  ctb.id_unico,
        CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
        c.id_unico 
    FROM 
        gf_cuenta_bancaria ctb
    LEFT JOIN 
        gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
    LEFT JOIN 
        gf_cuenta c ON ctb.cuenta = c.id_unico 
    WHERE 
        ctbt.tercero =$compania  
        AND ctb.parametrizacionanno = $anno 
        AND c.id_unico IS NOT NULL ORDER BY ctb.id_unico ASC"); 
#** Banco Final**#   
$rowbF = $con->Listar("SELECT  ctb.id_unico,
        CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
        c.id_unico 
    FROM 
        gf_cuenta_bancaria ctb
    LEFT JOIN 
        gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
    LEFT JOIN 
        gf_cuenta c ON ctb.cuenta = c.id_unico 
    WHERE 
        ctbt.tercero =$compania  
        AND ctb.parametrizacionanno = $anno 
        AND c.id_unico IS NOT NULL ORDER BY ctb.id_unico DESC"); 

$rowrI = $con->Listar("SELECT DISTINCT t.id_unico,IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     (t.razonsocial),
     CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos)) AS NOMBRE 
     FROM gp_pago p 
     LEFT JOIN gf_tercero t ON t.id_unico = p.usuario  
     WHERE p.parametrizacionanno = $anno 
     ORDER BY t.id_unico ASC ");
$rowrF = $con->Listar("SELECT DISTINCT t.id_unico, IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     (t.razonsocial),
     CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos)) AS NOMBRE 
     FROM gp_pago p 
     LEFT JOIN gf_tercero t ON t.id_unico = p.usuario  
     WHERE p.parametrizacionanno = $anno 
     ORDER BY t.id_unico DESC ");     
?>
<html>
    <head>
        <title>Planilla De Caja</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            label #bancoI-error, #bancoF-error, #fechaI-error, #fechaF-error, #responsableI-error, #responsableF-error  {
                display: block;
                color: #bd081c;
                font-weight: bold;
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
        </script>
        <script>
            $(function(){
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
                var fecha = new Date();
                var dia = fecha.getDate();
                var mes = fecha.getMonth() + 1;
                if(dia < 10){ dia = "0" + dia;}
                if(mes < 10){ mes = "0" + mes; }
                var fecha_A = dia + "/" + mes + "/" + fecha.getFullYear();
                $.datepicker.setDefaults($.datepicker.regional['es']);
                $("#fechaI").datepicker({changeMonth: true,}).val(fecha_A);
                $("#fechaF").datepicker({changeMonth: true}).val(fecha_A);
            });
        </script>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left" style="margin-left: 0px;margin-top: -20px"> 
                    <h2 align="center" class="tituloform">Planilla De Caja</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:generar()">  
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                            <div class="form-group">
                                <div class="form-group" style="margin-top: -5px;">
                                     <label for="fechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                                     <input class="form-control" type="text" name="fechaI" id="fechaI"  value="" required title="Seleccione Fecha Inicial">
                                </div>
                                <div class="form-group" style="margin-top: -10px;">
                                     <label for="fechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                                     <input class="form-control" type="text" name="fechaF" id="fechaF"  value="" required title="Seleccione Fecha Final">
                                </div>
                                <div class="form-group" style="margin-top: -5px">
                                    <label for="bancoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Banco Inicial:</label>
                                    <select  name="bancoI" id="bancoI" class="select2_single form-control" title="Seleccione Banco Inicial" style="height: 30px" required>
                                            <?php  
                                            if(count($rowbI)>0){
                                                for ($i = 0; $i < count($rowbI); $i++) {
                                                    echo '<option value="'.$rowbI[$i][0].'">'.ucwords(mb_strtolower($rowbI[$i][1])).'</option>';
                                                }
                                            } else{
                                                echo '<option value="">No Hay Bancos</option>';
                                            } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: -5px">
                                    <label for="bancoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Banco Final:</label>
                                    <select  name="bancoF" id="bancoF" class="select2_single form-control" title="Seleccione Banco Final" style="height: 30px" required>
                                            <?php  
                                            if(count($rowbF)>0){
                                                for ($i = 0; $i < count($rowbF); $i++) {
                                                    echo '<option value="'.$rowbF[$i][0].'">'.ucwords(mb_strtolower($rowbF[$i][1])).'</option>';
                                                }
                                            } else{
                                                echo '<option value="">No Hay Bancos</option>';
                                            } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: -5px">
                                    <label for="responsableI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Responsable Inicial:</label>
                                    <select  name="responsableI" id="responsableI" class="select2_single form-control" title="Seleccione Responsable Inicial" style="height: 30px" required>
                                            <?php  
                                            if(count($rowrI)>0){
                                                for ($i = 0; $i < count($rowrI); $i++) {
                                                    echo '<option value="'.$rowrI[$i][0].'">'.ucwords(mb_strtolower($rowrI[$i][1])).'</option>';
                                                }
                                            } else{
                                                echo '<option value="">No Hay Responsables</option>';
                                            } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: -5px">
                                    <label for="responsableF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Responsable Final:</label>
                                    <select  name="responsableF" id="responsableF" class="select2_single form-control" title="Seleccione Responsable Final" style="height: 30px" required>
                                            <?php  
                                            if(count($rowrF)>0){
                                                for ($i = 0; $i < count($rowrF); $i++) {
                                                    echo '<option value="'.$rowrF[$i][0].'">'.ucwords(mb_strtolower($rowrF[$i][1])).'</option>';
                                                }
                                            } else{
                                                echo '<option value="">No Hay Responsables</option>';
                                            } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: -5px">
                                    <label for="responsableF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Generar A:</label>
                                    <label for="optPdf" class="radio-inline"><input type="radio" name="optArchivo" id="optArchivo1" title="Seleccione Opción" checked>PDF</label>
                                    <label for="optExl" class="radio-inline"><input type="radio" name="optArchivo" id="optArchivo2">EXCEL</label>
                                </div>
                                <div class="col-sm-10" style="margin-top:5px;margin-left:25px" >
                                    <label for="responsableF" class="col-sm-5 control-label"></label>
                                    <button type="submit" class="btn sombra btn-primary" title="Generar reporte"><i class="glyphicon glyphicon-send" aria-hidden="true"></i></button>              
                                </div>
                            </div>
                        </form>
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
        function generar(){
            jsShowWindowLoad('Validando Datos');
            var fechaI = $("#fechaI").val();
            var fechaF = $("#fechaF").val();
            var bancoI = $("#bancoI").val();
            var bancoF = $("#bancoF").val();
            var responsableI = $("#responsableI").val();
            var responsableF = $("#responsableF").val();
            var form_data = {
                action:27,
                fechaI:$("#fechaI").val(),
                fechaF:$("#fechaF").val(),                       
            };
            $.ajax({
                type:'POST',
                url:'jsonPptal/gf_facturaJson.php',
                data:form_data,
                success: function(data){
                    jsRemoveWindowLoad();
                    if($("#optArchivo1").is(':checked')) {  
                        window.open('informes/INF_PLANILLA_CAJA.php?tipo=1&fechaI='+fechaI+'&fechaF='+fechaF+'&bancoI='+bancoI+'&bancoF='+bancoF+'&responsableI='+responsableI+'&responsableF='+responsableF );
                    } else if($("#optArchivo2").is(':checked')) {    
                        window.open('informes/INF_PLANILLA_CAJA.php?tipo=2&fechaI='+fechaI+'&fechaF='+fechaF+'&bancoI='+bancoI+'&bancoF='+bancoF+'&responsableI='+responsableI+'&responsableF='+responsableF );
                    }  
                }
            });
           
        }
        
        </script>
        <?php require_once 'footer.php' ?>  
    </body>
</html>