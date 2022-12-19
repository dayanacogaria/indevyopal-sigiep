<html>
    <?php
    require_once ('head.php');
    require_once ('./Conexion/conexion.php');
    @session_start();
    $anno = $_SESSION['anno'];
    ?>
    <script type="text/javascript" src="js/reservadas_mysql.js"></script>
    <title>Registrar Periodo</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/md5.pack.js"></script>
    <style>
        label #txtCodigoI-error,#sltFechaI-error, #sltFechaF-error, #sltTipoPN-error, #dialiq-error{
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
            rules: {
            }
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
            $("#sltFechaI").datepicker({changeMonth: true,}).val();
            $("#sltFechaF").datepicker({changeMonth: true,}).val();                       
        });
    </script>
    </head>
    <body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-right: 4px; margin-left: 4px; margin-top: -5px">Registrar Período</h2>
                <a href="listar_GN_PERIODO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Periodo</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; font-size: 13px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarPeriodoJson.php">
                        <p align="center" style="margin-bottom: 25px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="txtCodigoI" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código Interno:</label>
                            <input type="text" name="txtCodigoI" id="txtCodigoI" class="form-control" title="Ingrese Código Interno" onkeypress="return txtValida(event,'num_car')" placeholder="Código Interno" required>
                        </div> 
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="sltFechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicio:</label>
                            <input name="sltFechaI" id="sltFechaI" title="Ingrese Fecha Inicio" type="text" style="" class="form-control col-sm-1"  onchange="javaScript:fechaInicial();" placeholder="Fecha Inicio" required="required">  
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="sltFechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Fin:</label>
                            <input name="sltFechaF" id="sltFechaF" title="Ingrese Fecha Fin" type="text" style="" class="form-control col-sm-1" placeholder="Fecha Fin" required="required">  
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="sltEstado" class="control-label col-sm-5"><strong class="obligado"></strong>Estado:</label>
                            <select name="sltEstado" class="select2_single form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px; width: 300px">
                                <option value="">Estado</option>
                                <?php 
                                $es   = "SELECT id_unico, nombre FROM gn_estado_periodo";
                                $esta = $mysqli->query($es);
                                while ($filaES = mysqli_fetch_row($esta)) {
                                    echo '<option value="'.$filaES[0].'">'.$filaES[1].'</option>';
                                } ?>
                            </select>   
                        </div>
                        <div class="form-group" style="margin-top: -0px">
                            <label for="sltTipoPN" class="control-label col-sm-5"><strong class="obligado">*</strong>Tipo Proceso Nómina: </label>
                            <select name="sltTipoPN" class="select2_single form-control" id="sltTipoPN" title="Seleccione Tipo Proceso Nómina" style="height: 30px;width: 300px" required="required">
                                <option value="">Tipo Proceso Nómina</option>
                                <?php $tpn   = "SELECT id_unico, nombre FROM gn_tipo_proceso_nomina";
                                    $tipop = $mysqli->query($tpn);
                                    while ($filaTP = mysqli_fetch_row($tipop)) {
                                        echo '<option value="'.$filaTP[0].'">'.$filaTP[1].'</option>';
                                    }
                                ?>
                            </select>   
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="es_acumulable" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;"></strong>¿Es Acumulable?:</label>
                            <input  type="radio" name="es_acumulable" id="es_acumulable"  value="1" >SI
                            <input  type="radio" name="es_acumulable" id="es_acumulable" value="2" checked>NO
                        </div> 
                        <div class="form-group" style="margin-top: -0px;">
                            <label for="dialiq" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;">*</strong>Número de Días de Nómina :</label>
                            <input  type="text" name="dialiq" id="dialiq" title="Ingresar Días Nómina" class="form-control" onkeypress="return txtValida(event,'num')" placeholder="Número de Días" required="required">
                        </div>   
                        <div class="form-group" style="margin-top: -10px">
                            <label for="sltPeriodoR" class="control-label col-sm-5"><strong class="obligado"></strong>Periodo Retroactivo:</label>
                            <select name="sltPeriodoR" class="select2_single form-control" id="sltPeriodoR" title="Seleccione Periodo Retroactivo" style="height: 30px; width: 300px">
                                <option value="">Periodo Retroactivo</option>
                                <?php 
                                $es   = "SELECT id_unico, codigointerno FROM gn_periodo WHERE parametrizacionanno = $anno ANd tipoprocesonomina = 12";
                                $esta = $mysqli->query($es);
                                while ($filaES = mysqli_fetch_row($esta)) {
                                    echo '<option value="'.$filaES[0].'">'.$filaES[1].'</option>';
                                } ?>
                            </select>   
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -5px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>                  
            <div class="col-sm-8 col-sm-2" style="margin-top:-27px">
                <table class="tablaC table-condensed text-center" align="center">
                    <thead>
                        <tr>                                        
                            <th>
                                <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>                                    
                            <td>
                                <a class="btn btn-primary btnInfo" href="registrar_GN_ESTADO_PERIODO.php">ESTADO</a>
                            </td>
                        </tr>
                        <tr>                                    
                            <td>
                                <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_PROCESO_NOMINA.php">TIPO PROCESO</a>
                            </td>
                        </tr>                            
                    </tbody>
                </table>
            </div>
        </div>
    </div>        
    <?php require_once './footer.php'; ?>
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".select2_single").select2({
                allowClear: true
            });
        });
    </script>    
    <script>
    function fechaInicial(){
        var fechain= document.getElementById('sltFechaI').value;
        var fechafi= document.getElementById('sltFechaF').value;
        var fi = document.getElementById("sltFechaF");
        fi.disabled=false;
        $( "#sltFechaF" ).datepicker( "destroy" );
        $( "#sltFechaF" ).datepicker({ changeMonth: true, minDate: fechain}); 
    }
    </script>
        </body>
    </html>
        