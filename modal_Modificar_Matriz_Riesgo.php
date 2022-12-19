
<!-- Librerias de carga para el datapicker -->

<link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
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
               
        $("#sltFechaini").datepicker({changeMonth: true,}).val()
        $("#sltFechafin").datepicker({changeMonth: true,}).val();
       
        
        
});
</script>

<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<div class="modal fade recaMat" id="modalMatrizR" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content" style="width: 450px;">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Modificar Matriz de Riesgo</h4>
                    </div>
                    <div class="modal-body" >
                        <div class="row text-left">
                            <form class="form-horizontal" id="formActP" name="formu" method="post" action="jsonComercio/registrarRecaudoJson.php">                                                        
                                
                                <?php
                                    require_once 'Conexion/conexion.php';
                                    $idAC = $_POST['id'];
                                       
                                    $proyecto = "SELECT p.id_unico, p.titulo FROM gy_proyecto p LEFT JOIN gy_actividad_proyecto ap ON ap.id_proyecto = p.id_unico WHERE ap.id_unico = '$idAC'";
                                    $proyec = $mysqli->query($proyecto);
                                    $pro = mysqli_fetch_row($proyec);
                                    
                                    $actividad = "SELECT a.id_unico, a.nombre FROM gy_actividad a LEFT JOIN gy_actividad_proyecto ap ON ap.id_actividad = a.id_unico WHERE ap.id_unico = '$idAC' ";
                                    $acti = $mysqli->query($actividad);
                                    $idActi = mysqli_fetch_row($acti);

                                    $sqlinfo = "SELECT  fecha_inicio_programada, 
                                                        fecha_final_programada, 
                                                        valor_programado, 
                                                        valor_ejecutado, 
                                                        responsable_actividad,
                                                        id_tipo_proyecto
                                                FROM gy_actividad_proyecto WHERE id_unico = '$idAC'";
                                    $resinfo = $mysqli->query($sqlinfo);
                                    $rowInfo = mysqli_fetch_row($resinfo);
                                    
                                   
                                    
                                    $responsa = "SELECT tr.id_unico,
                                                IF(CONCAT_WS(' ',
                                                 tr.nombreuno,
                                                 tr.nombredos,
                                                 tr.apellidouno,
                                                 tr.apellidodos) 
                                                 IS NULL OR CONCAT_WS(' ',
                                                 tr.nombreuno,
                                                 tr.nombredos,
                                                 tr.apellidouno,
                                                 tr.apellidodos) = '',
                                                 (tr.razonsocial),
                                                 CONCAT_WS(' ',
                                                 tr.nombreuno,
                                                 tr.nombredos,
                                                 tr.apellidouno,
                                                 tr.apellidodos)) AS NOMBRE
                                            FROM gf_tercero tr
                                            WHERE tr.id_unico = '$rowInfo[4]'";
                                    $respon = $mysqli->query($responsa);
                                    $rowRes = mysqli_fetch_row($respon);
                                    
                                    $Segui = "SELECT * FROM  gy_seguimiento WHERE id_actividad_proyecto ='$idAC'";
                                    $resSegui = $mysqli->query($Segui);
                                    $nres = mysqli_num_rows($resSegui);
                                    
                                    #Validad si la actividad no posee seguimientos para que permita modificar todos los campos
                                    #Si posee seguiminetos, solo permite modificar el valor ejecutado
                                   
                                ?>
                                        <div class="form-group" >
                                            <input type="hidden" name="txtTipoR" id="txtTipoR" value=""> 
                                            <label for="sltTRiesgo" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Tipo Riesgo:</label>
                                            <div class="classTipoR">
                                                <div class="col-sm-6 col-md-6 col-lg-6">
                                                    <select id="sltTRiesgo" name="sltTRiesgo" class="form-control" title="Ingrese el Riesgo" required >
                                                        <option value="">Tipo Riesgo</option>
                                                    </select>
                                                    <script type="text/javascript">

                                                        $(document).ready(function(){


                                                            $.ajax({
                                                                type: "POST",
                                                                url: "buscar_GY_TIPO_RIESGO.php",
                                                                success: function(response){
                                                                    $('.classTipoR select').html(response).fadeIn();
                                                                    $('#sltTRiesgo').css('display','none');
                                                                }
                                                            }); 


                                                        });

                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group"  align="left">
                                            <label for="sltActividad" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Riesgo:</label>
                                            <div class="classRiesgo">   
                                                <div class="col-sm-6 col-md-6 col-lg-6">    
                                                    <select id="sltRiesgo" name="sltRiesgo" class="form-control" title="Ingrese el Riesgo" required >
                                                        <option value="">Riesgo</option>
                                                    </select>
                                                    <script type="text/javascript">

                                                        $(document).ready(function(){
                                                            $(".classTipoR select").change(function(){
                                                                var form_data = {
                                                                    is_ajax: 1,
                                                                    id_TipoR: +$(".classTipoR select").val()
                                                                };
                                                                $.ajax({
                                                                    type: "POST",
                                                                    url: "buscar_GY_RIESGO.php",
                                                                    data: form_data,
                                                                    success: function(response){
                                                                        $('.classRiesgo select').html(response).fadeIn();
                                                                        $('#sltRiesgo').css('display','none');
                                                                    }
                                                                });
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="sltProba" type="date" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Probabilidad:</label>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <select id="sltProba" name="sltProba" class="form-control" title="Ingrese la Probabilidad" required>
                                                    <option value="">Probabilidad</option>
                                                    <?php
                                                        $probabi = "SELECT id_unico, nombre FROM gy_probabilidad WHERE compania = '$compania'";
                                                        $proba = $mysqli->query($probabi);

                                                        while($rowP = mysqli_fetch_row($proba)){
                                                    ?>
                                                            <option value="<?php echo $rowP[0] ?>"><?php echo $rowP[1] ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>    
                                        </div>
                                        <div class="form-group">

                                            <label for="sltFechafin" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Fecha Final:</label>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <input name="sltFechafin" id="sltFechafin" title="Ingrese la Fecha Final " type="date"  class="form-control " value="<?php echo $rowInfo[1] ?>"  placeholder="Ingrese la fecha final">
                                            </div>    
                                        </div>
                                        <div class="form-group">

                                            <label for="txtvalorP" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" >Valor Programado:</label>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <input name="txtvalorP" id="txtvalorP" title="Ingrese el valor Programado " type="text" class="form-control " onkeypress="return txtValida(event,'num', 'valor', '2');" onkeyup="formatC('txtvalorP');" value="<?php echo number_format($rowInfo[2],0,'.',',') ?>">
                                            </div>    
                                        </div>
                                        <div class="form-group">

                                            <label for="txtvalorE" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Valor Ejecutado:</label>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <input name="txtvalorE" id="txtvalorE"  type="text" class="form-control " onkeypress="return txtValida(event,'num', 'valor', '2');" onkeyup="formatC('txtvalorE');" value="<?php echo number_format($rowInfo[3],0,'.',',') ?>"  >
                                            </div>    
                                        </div>
                                        <div class="form-group"  align="left">
                                            <label for="sltResponsable" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Responsable:</label>
                                                <?php 
                                                    $tiporec = "SELECT id_unico, nombre FROM gc_tipo_recaudo WHERE id_unico = 1 ";
                                                    $treca = $mysqli->query($tiporec);
                                                ?> 
                                            <div class="col-sm-6 col-md-6 col-lg-6">    
                                                <select  id="sltResponsable" name="sltResponsable" class="form-control select2" title="Seleccione el Responsable" >
                                                    <option value="<?php echo $rowRes[0] ?>"><?php echo $rowRes[1] ?></option>
                                                    <?php 
                                                        while($rowTR = mysqli_fetch_row($treca)){
                                                            echo "<option value=".$rowTR[0].">".$rowTR[1]."</option>";
                                                        }
                                                    ?>                     
                                                </select>
                                            </div>    
                                        </div>
                                        
                                 
                            </form>
                        </div>    
                           
                        
                        
                                                 
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="guardar" class="btn"  style="color: #000; margin-top: 2px"  title="Modificar Actividad"><li class="glyphicon glyphicon-floppy-disk" onclick="javascript:modificar(<?php echo $idAC ?>)"></button>    
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
                    </div>  
                </div>
            </div>

            <script>
                function modificar(id){
                    var tipoP = $("#tipoP").val();
                     var proye = $("#ProAC").val();
                        jsShowWindowLoad('Modificando Datos ...');
                        var formData = new FormData($("#formActP")[0]);
                        $.ajax({
                            type: 'POST',
                            url: "jsonProyecto/gy_actividad_proyectoJson.php?action=3",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                                jsRemoveWindowLoad();
                                console.log(response);
                                if(response==1){
                                    $("#modalActividadP").modal("hide");
                                    $("#mensaje").html('Información Modificada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                       document.location='registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='+tipoP+'&pro='+proye;
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Modificar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalActividadP").modal("hide");
                                        document.location='registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='+tipoP+'&pro='+proye;
                                    })
                                }
                            }
                        });
                }

            </script>

            <script src="js/bootstrap.min.js"></script>
        
            <script type="text/javascript" src="js/select2.js"></script>
            <script type="text/javascript"> 
                $("#sltResponsable").select2();
                $("#sltActividad").select2();
            </script>
            
           
        </div>