    <?php 
    require_once('Conexion/conexion.php');
    require_once ('./Conexion/conexion.php');
    @session_start();
    $anno = $_SESSION['anno']; 
    $id = (($_GET["id"]));
    $sql = "SELECT    p.id_unico,
                      p.codigointerno,
                      p.fechainicio,
                      p.fechafin,
                      p.acumulable,
                      p.estado,
                      ep.id_unico,
                      ep.nombre,
                      p.parametrizacionanno,
                      p.tipoprocesonomina,
                      tpn.id_unico,
                      tpn.nombre,
                      p.dias_nomina, 
                      pr.id_unico, pr.codigointerno 
                  FROM gn_periodo p
                  LEFT JOIN   gn_estado_periodo ep        ON p.estado = ep.id_unico
                  LEFT JOIN   gn_tipo_proceso_nomina tpn  ON p.tipoprocesonomina = tpn.id_unico
                  LEFT JOIN   gn_periodo pr ON p.periodo_retro = pr.id_unico 
                  where md5(p.id_unico) = '$id'";
      $resultado = $mysqli->query($sql);
      $row = mysqli_fetch_row($resultado);    

    $pid   = $row[0];
    $pci   = $row[1];
    $pfeci = $row[2];
    $pfecf = $row[3];
    $pacu  = $row[4];
    $pest  = $row[5];
    $epid  = $row[6];
    $epnom = $row[7];
    $ppa   = $row[8];
    $ptip  = $row[9];
    $tpid  = $row[10];
    $tpnom = $row[11];
    $dias = $row[12];
    require_once './head.php';
    ?>
    <script type="text/javascript" src="js/reservadas_mysql.js"></script>
    <title>Modificar Periodo</title>
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
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-right: 4px; margin-left: 4px; margin-top: -5px">Modificar Período</h2>
                    <a href="listar_GN_PERIODO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?= $pci ?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarPeriodoJson.php">
                            <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                            <p align="center" style="margin-bottom: 25px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="txtCodigoI" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código Interno:</label>
                                <input type="text" name="txtCodigoI" value="<?php echo $pci ?>" id="txtCodigoI" class="form-control"  title="Ingrese Código Interno" onkeypress="return txtValida(event,'num_car')" placeholder="Código Interno" required="required">
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="sltFechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicio:</label>
                                <?php                                         
                                    $pfeci = $row[2];
                                    if(!empty($row[2])||$row[2]!=''){
                                        $pfeci = trim($pfeci, '"');
                                        $fecha_div = explode("-", $pfeci);
                                        $anioi = $fecha_div[0];
                                        $mesi  = $fecha_div[1];
                                        $diai  = $fecha_div[2];
                                        $pfeci   = $diai.'/'.$mesi.'/'.$anioi;
                                    }else{
                                        $pfeci='';
                                    }
                                ?>
                                <input name="sltFechaI" id="sltFechaI" title="Ingrese Fecha Inicio" type="text"  class="form-control col-sm-1"  onchange="javaScript:fechaInicial();" value="<?php echo $pfeci;?>" required="required">  
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="sltFechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Fin:</label>
                                <?php                                         
                                    $pfecf = $row[3];
                                    if(!empty($row[3])||$row[3]!=''){
                                        $pfecf = trim($pfecf, '"');
                                        $fecha_div = explode("-", $pfecf);
                                        $anioi = $fecha_div[0];
                                        $mesi  = $fecha_div[1];
                                        $diai  = $fecha_div[2];
                                        $pfecf   = $diai.'/'.$mesi.'/'.$anioi;
                                    }else{
                                        $pfecf='';
                                    }
                                ?>     
                                <input name="sltFechaF" id="sltFechaF" title="Ingrese Fecha Fin" type="text"  class="form-control col-sm-1" value="<?php echo $pfecf;?>" required="required">  
                            </div>
                            <div class="form-group" style="margin-top: -10px">
                                <label for="sltEstado" class="control-label col-sm-5"><strong class="obligado"></strong>Estado:</label>
                                <select name="sltEstado" class="select2_single form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                                    <?php 
                                    if(empty($pest)) { 
                                        echo '<option value=""> - </option>';
                                        $es   = "SELECT id_unico, nombre FROM gn_estado_periodo";
                                    } else { 
                                        echo '<option value="'.$epid.'">'.$epnom.'</option>';
                                        echo '<option value=""> - </option>';
                                        $es   = "SELECT id_unico, nombre FROM gn_estado_periodo WHERE id_unico != $pest";
                                    }
                                    $esta = $mysqli->query($es);
                                    while ($filaES = mysqli_fetch_row($esta)) { 
                                        echo '<option value="'.$filaES[0].'">'.$filaES[1].'</option>';
                                    }?>
                                </select>   
                            </div>
                            <div class="form-group" style="margin-top: 0px">
                                <label for="sltTipoPN" class="control-label col-sm-5"><strong class="obligado">*</strong>Tipo Proceso Nómina:</label>
                                <select name="sltTipoPN" class="select2_single form-control" id="sltTipoPN" title="Seleccione Tipo Proceso Nómina" style="height: 30px" required="required">
                                    <?php 
                                    if(empty($ptip)) { 
                                        echo '<option value=""> - </option>';
                                        $tpn   = "SELECT id_unico, nombre FROM gn_tipo_proceso_nomina";
                                    } else { 
                                        echo '<option value="'.$tpid.'">'.$tpnom.'</option>';
                                        $tpn   = "SELECT id_unico, nombre FROM gn_tipo_proceso_nomina WHERE id_unico != $ptip";
                                    }
                                    $tipop = $mysqli->query($tpn);
                                    while ($filaTP = mysqli_fetch_row($tipop)) {
                                        echo '<option value="'.$filaTP[0].'">'.$filaTP[1].'</option>';
                                    } ?>
                                </select>   
                            </div>
                            <div class="form-group" style="margin-top: -5px;">
                                <label for="es_acumulable" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;"></strong>¿Es acumulable?:</label>
                                <?php   if ($pacu==1) { ?>
                                <input  type="radio" name="es_acumulable" id="es_acumulable"  value="1" checked="checked">SI
                                <input  type="radio" name="es_acumulable" id="es_acumulable" value="2">NO
                                <?php   } else { ?>
                                <input  type="radio" name="es_acumulable" id="es_acumulable"  value="1">SI
                                <input  type="radio" name="es_acumulable" id="es_acumulable" value="2" checked="checked">NO
                                <?php       } ?>
                            </div>
                            <div class="form-group" style="margin-top: 0px;">
                                <label for="dialiq" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;">*</strong>Número de Días de Nómina :</label>
                                <input  type="text" name="dialiq" title="Ingresar Días Nómina " value="<?php echo $dias ?>" id="dialiq" class="form-control" onkeypress="return txtValida(event,'num')" placeholder="Número de Días" required>
                            </div>
                            <div class="form-group" style="margin-top: -10px">
                                <label for="sltPeriodoR" class="control-label col-sm-5"><strong class="obligado"></strong>Periodo Retroactivo:</label>
                                <select name="sltPeriodoR" class="select2_single form-control" id="sltPeriodoR" title="Seleccione Periodo Retroactivo" style="height: 30px; width: 300px">
                                    
                                    <?php 
                                    if(empty($row[13])){
                                        echo '<option value=""> - </option>';    
                                        $es   = "SELECT id_unico, codigointerno FROM gn_periodo WHERE parametrizacionanno = $anno ANd tipoprocesonomina = 12";
                                    } else {
                                        echo '<option value="'.$row[13].'">'.$row[14].'</option>';    
                                        echo '<option value=""> - </option>';    
                                        $es   = "SELECT id_unico, codigointerno FROM gn_periodo WHERE parametrizacionanno = $anno ANd tipoprocesonomina = 12 AND id_unico !=".$row[13];
                                    }                                    
                                    $esta = $mysqli->query($es);
                                    while ($filaES = mysqli_fetch_row($esta)) {
                                        echo '<option value="'.$filaES[0].'">'.$filaES[1].'</option>';
                                    } ?>
                                </select>   
                            </div>                                 
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style="margin-top: -5px; margin-bottom: 10px;  margin-left: 0px;">Guardar</button>
                            </div>


                        </form>
                    </div>
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
