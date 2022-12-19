
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
<div class="modal fade recaDec" id="modalActividadP" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content" style="width: 450px;">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Modificar Actividad</h4>
                    </div>
                    <div class="modal-body" >
                        <div class="row text-left">
                            <form class="form-horizontal" id="formActP" name="formu" method="post" action="jsonComercio/registrarRecaudoJson.php">                                                        
                                
                                <?php
                                    require_once 'Conexion/conexion.php';
                                    
                                    $compania = $_POST['co'];
                                    $idAC = $_POST['id'];
                                    $id_pro = $_POST['pro']; 
                                    $valor = $id_pro;
                                    $proyecto = "SELECT p.id_unico, p.titulo, p.fecha_inicio FROM gy_proyecto p LEFT JOIN gy_actividad_proyecto ap ON ap.id_proyecto = p.id_unico WHERE ap.id_unico = '$idAC'";
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
                                    if($nres < 1){
                                ?>
                                        <div class="form-group" >
                                            <label for="txtProyecto" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Proyecto:</label>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <input name="txtProyecto" id="txtProyecto"  type="text"  class="form-control " value="<?php echo $pro[1] ?>" readonly>
                                            </div> 
                                        </div>
                                        <div class="form-group"  align="left">
                                            <label for="sltActividad" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Actividad:</label>
                                                <?php 
                                                    $Nact = "SELECT id_unico, nombre FROM gy_actividad WHERE id_unico != '$idActi[0]' AND compania = '$compania'";
                                                    $resAct = $mysqli->query($Nact);

                                                ?> 
                                            <div class="col-sm-6 col-md-6 col-lg-6">    
                                                <select  id="sltActividad" name="sltActividad" class="form-control select2" title="Seleccione la Actividad" >
                                                    <option value="<?php echo $idActi[0] ?>" ><?php echo $idActi[1] ?></option>
                                                    <?php 
                                                        while($rowAC = mysqli_fetch_row($resAct)){
                                                            echo "<option value=".$rowAC[0].">".$rowAC[1]."</option>";
                                                        }
                                                    ?>                     
                                                </select>
                                            </div>    
                                        </div>

                                        <div class="form-group">
                                            <label for="sltFechaini" type="date" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Fecha Inicial:</label>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <input name="sltFechaini" id="sltFechaini" title="Ingrese la Fecha Inicial " type="date" min="<?php echo $pro[2]; ?>" class="form-control " value="<?php echo $rowInfo[0] ?>"  placeholder="Ingrese la fecha Inicial">
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <script>
                                                $("#sltFechaini").change(function(){
                                                    var FI = $("#sltFechaini").val();
                                                    $("#sltFechafin").prop("min",FI);
                                               
                                                });
                                                
                                            </script>    
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
                                                   $responsable = "SELECT tr.id_unico,
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
                                                                LEFT JOIN gy_tercero_proyecto tp ON tp.id_tercero = tr.id_unico
                                                                WHERE md5(tp.id_proyecto) = '$valor' AND tr.id_unico != '$rowRes[0]'";
                                                        $resp = $mysqli->query($responsable);
                                                ?> 
                                            <div class="col-sm-6 col-md-6 col-lg-6">    
                                                <select  id="sltResponsable" name="sltResponsable" class="form-control select2" title="Seleccione el Responsable" >
                                                    <option value="<?php echo $rowRes[0] ?>"><?php echo $rowRes[1] ?></option>
                                                    <?php 
                                                        while($rowTR = mysqli_fetch_row($resp)){
                                                            echo "<option value=".$rowTR[0].">".$rowTR[1]."</option>";
                                                        }
                                                    ?>                     
                                                </select>
                                            </div>    
                                        </div>
                                        
                                        <input type="hidden" id="x" name="x" value="1">
                                <?php        
                                    }else{
                                ?>
                                
                                        <div class="form-group" >
                                            <label for="txtProyecto" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Proyecto:</label>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <input name="txtProyecto" id="txtProyecto"  type="text"  class="form-control " value="<?php echo $pro[1] ?>" readonly>
                                            </div> 
                                        </div>
                                        <div class="form-group"  align="left">
                                            <label for="sltActividad" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Actividad:</label>
                                                <?php 
                                                    $actividad = "SELECT a.id_unico, a.nombre FROM gy_actividad a LEFT JOIN gy_actividad_proyecto ap ON ap.id_actividad = a.id_unico WHERE ap.id_unico = '$idAC' ";
                                                    $acti = $mysqli->query($actividad);
                                                    $idActi = mysqli_fetch_row($acti);

                                                    $Nact = "SELECT id_unico, nombre FROM gy_actividad WHERE id_unico != '$idActi[0]'";
                                                    $resAct = $mysqli->query($Nact);

                                                ?> 
                                            <div class="col-sm-6 col-md-6 col-lg-6">    
                                                <select  id="sltActividad" name="sltActividad" class="form-control select2" title="Seleccione la Actividad" disabled>
                                                    <option value="<?php echo $idActi[0] ?>" ><?php echo $idActi[1] ?></option>
                                                    <?php 
                                                        while($rowAC = mysqli_fetch_row($resAct)){
                                                            echo "<option value=".$rowAC[0].">".$rowAC[1]."</option>";
                                                        }
                                                    ?>                     
                                                </select>
                                            </div>    
                                        </div>

                                        <div class="form-group">
                                            <label for="sltFechaini" type="date" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Fecha Inicial:</label>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <input name="sltFechaini" id="sltFechaini" title="Ingrese la Fecha Inicial " type="date"  class="form-control " value="<?php echo $rowInfo[0] ?>"  placeholder="Ingrese la fecha Inicial" disabled>
                                            </div>    
                                        </div>
                                        <div class="form-group">

                                            <label for="sltFechafin" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Fecha Final:</label>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <input name="sltFechafin" id="sltFechafin" title="Ingrese la Fecha Final " type="date"  class="form-control " value="<?php echo $rowInfo[1] ?>"  placeholder="Ingrese la fecha final" disabled>
                                            </div>    
                                        </div>
                                        <div class="form-group">

                                            <label for="txtvalorP" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" >Valor Programado:</label>
                                            <div class="col-sm-6 col-md-6 col-lg-6">
                                                <input name="txtvalorP" id="txtvalorP" title="Ingrese el valor Programado " type="text" class="form-control " onkeypress="return txtValida(event,'num', 'valor', '2');" onkeyup="formatC('txtvalorP');" value="<?php echo number_format($rowInfo[2],0,'.',',') ?>" disabled>
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
                                                <select  id="sltResponsable" name="sltResponsable" class="form-control select2" title="Seleccione el Responsable" disabled>
                                                    <option value="<?php echo $rowRes[0] ?>"><?php echo $rowRes[1] ?></option>
                                                    <?php 
                                                        while($rowTR = mysqli_fetch_row($treca)){
                                                            echo "<option value=".$rowTR[0].">".$rowTR[1]."</option>";
                                                        }
                                                    ?>                     
                                                </select>
                                            </div>    
                                        </div>
                                        <input type="hidden" id="x" name="x" value="2">
                                <?php
                                    }
                                ?>
                                
                                <input type="hidden" name="ProAC" id="ProAC" value="<?php echo $pro[0] ?>">
                                <input type="hidden" name="actividad" id="actividad" value="<?php echo $idActi[0]  ?>">
                                <input type="hidden" name="fechaIP" id="fechaIP" value="<?php echo $rowInfo[0]  ?>">
                                <input type="hidden" name="fechaFP" id="fechaFP" value="<?php echo $rowInfo[1]  ?>">
                                <input type="hidden" name="valorPO" id="valorPO" value="<?php echo $rowInfo[2]  ?>">
                                <input type="hidden" name="responP" id="responP" value="<?php echo $rowRes[0]  ?>">
                                <input type="hidden" name="AcP" id="AcP" value="<?php echo $idAC ?>">
                                <input type="hidden" name="tipoP" id="tipoP" value="<?php echo $rowInfo[5]  ?>">
                                <input type="hidden" name="id_P" id="id_P" value="<?php echo $id_pro ?>"
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
                    var id_p = $("#id_P").val(); 
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
                                       document.location='registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='+tipoP+'&pro='+proye+'&proyec='+id_p;
                                    })
                                } else if(response == 2){
                                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalMensajes").modal("hide");
                                    })
                                } else if(response == 3){
                                    $("#mensaje").html('No Se Ha Podido Guardar Información, El valor Ejecutado no puede ser mayor al valor Programado');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalMensajes").modal("hide");
                                    })
                                } else if(response == 4){
                                    $("#mensaje").html('No Se Ha Podido Guardar Información, La sumatoria total de los valores programado es mayor al monto total');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalMensajes").modal("hide");
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