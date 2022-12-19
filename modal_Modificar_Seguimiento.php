
<!-- Librerias de carga para el datapicker -->

<script src="dist/jquery.validate.js"></script>
        <!-- Librerias de carga para el datapicker -->
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!-- select2 -->
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            /*Estilos tabla*/
            table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
            table.dataTable tbody td,table.dataTable tbody td{padding:1px}
            .dataTables_wrapper .ui-toolbar{padding:2px}
            /*Campos dinamicos*/
            .campoD:focus {
                border-color: #66afe9;
                outline: 0;            
                box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
            }
            .campoD:hover{
                cursor: pointer;
            }
            /*Campos dinamicos label*/
            .valorLabel{
                font-size: 10px;
            }
            .valorLabel:hover{
                cursor: pointer;
                color:#1155CC;
            }
            /*td de la tabla*/
            .campos{
                padding: 0px;
                font-size: 10px
            }
            /*cuerpo*/
            body{
                font-size: 10px;
                font-family: Arial;
            }

            .client-form input[type="text"]{
                width: 100%;
            }
            .client-form select{
                width: 100%;
            }

            .client-form input[type="file"]{
                width: 100%;
            }

        </style>  
        <style >

           label #sltEst-error, #sltFechaini-error, #txtDescripcion-error, #txtObser-error {
                display: block;
                color: #155180;
                font-weight: normal;
                font-style: italic;
                font-size: 10px
            }

            body{
                font-size: 11px;
            } 
           table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
           table.dataTable tbody td,table.dataTable tbody td{padding:1px}
           .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
               font-family: Arial;}
        </style>
        <script>
            $().ready(function() {
                var validator = $("#formSAct").validate({
                    ignore: "",
                    errorPlacement: function(error, element) {

                        $( element )
                        .closest( "formSAct" )
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
<div class="modal fade reca" id="modalSeguimientoA" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 450px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Modificar Seguimiento</h4>
            </div>
            <div class="modal-body" >
                <div class="row text-left">
                    <form class="form-horizontal" id="formSAct" name="formSAct" method="post" action="jsonComercio/registrarRecaudoJson.php">                                                        
                                
                        <?php
                        
                            require_once './Conexion/conexion.php';
                          
                            $compania = $_POST['co'];
                            $idS = $_POST['id'];
                            $id_pro  = $_POST['idpro'];
                            $seguimiento = "SELECT  s.id_actividad_proyecto,
                                                    s.fecha_seguimiento,
                                                    s.descripcion,
                                                    s.id_estado,
                                                    s.observaciones,
                                                    a.nombre,
                                                    e.nombre,
                                                    ap.id_proyecto,
                                                    ap.id_unico,
                                                    ap.fecha_inicio_programada
                                            FROM gy_seguimiento s
                                            LEFT JOIN gy_actividad_proyecto ap ON s.id_actividad_proyecto = ap.id_unico
                                            LEFT JOIN gy_actividad a ON ap.id_actividad = a.id_unico
                                            LEFT JOIN gy_estado e ON s.id_estado = e.id_unico
                                            WHERE s.id_unico = '$idS' ";
                            
                            $seguimi = $mysqli->query($seguimiento);
                            $segui = mysqli_fetch_row($seguimi);
                                      
                        ?>    
                        <div class="form-group"  align="left">
                            <label for="txtActividad" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" >Actividad:</label>
                             <div class="col-sm-6 col-md-6 col-lg-6">
                                 <input name="txtActividad" id="txtActividad"  type="text"  class="form-control " value="<?php echo $segui[5] ?>" readonly>
                            </div>   
                        </div>

                        <div class="form-group">
                            <label for="sltFechaini" type="date" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Fecha:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <input name="sltFechaini" id="sltFechaini" title="Ingrese la Fecha" type="date"  class="form-control " min="<?php echo $segui[9] ?>" value="<?php echo $segui[1] ?>"  placeholder="Ingrese la fecha Inicial" required>
                            </div>    
                        </div>
                                 
                        <div class="form-group">
                            <label for="txtDescripcion" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Descripción:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <input name="txtDescripcion" id="txtDescripcion" title="Ingrese la Descripcion " type="text" class="form-control " value="<?php echo $segui[2] ?>" required>
                            </div>    
                        </div>
                        <div class="form-group"  align="left">
                            <label for="sltEst" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Estado:</label>
                                <?php 
                                    $tiporec = "SELECT id_unico, nombre FROM gy_estado WHERE id_unico != '$segui[3]' AND compania = '$compania'";
                                    $treca = $mysqli->query($tiporec);
                                ?> 
                            <div class="col-sm-6 col-md-6 col-lg-6">    
                                <select  id="sltEst" name="sltEst" class="form-control select2" title="Seleccione el Estado" required>
                                    <option value="<?php echo $segui[3] ?>"><?php echo $segui[6] ?></option>
                                    <?php 
                                        while($rowTR = mysqli_fetch_row($treca)){
                                            echo "<option value=".$rowTR[0].">".$rowTR[1]."</option>";
                                        }
                                    ?>                     
                                </select>
                            </div>    
                        </div>
                        
                        <div class="form-group">
                            <label for="txtObser" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Observaciones:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <input name="txtObser" id="txtObser"  type="text" class="form-control " title="Ingrese las Observaciones " value="<?php echo $segui[4] ?>" required >
                            </div>    
                        </div>
                                
                        <input type="hidden" name="Segui_Act" id="Segui_Act" value="<?php echo $idS ?>">
                        <input type="hidden" id="id_P" name="id_P" value="<?php echo $id_pro ?>">        
                    </form>
                </div>    
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="guardar" class="btn"  style="color: #000; margin-top: 2px"  title="Modificar Actividad"><li class="glyphicon glyphicon-floppy-disk" onclick="javascript:modificar(<?php echo $idS ?>)"></button>    
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
            </div>  
        </div>
    </div>

            <script>
                function modificar(id){
                    //var tipoP = $("#tipoP").val();
                    // var proye = $("#ProAC").val();
                    var pr = $("#id_P").val();
                        //jsShowWindowLoad('Modificando Datos ...');
                        var formData = new FormData($("#formSAct")[0]);
                        $.ajax({
                            type: 'POST',
                            url: "jsonProyecto/gy_seguimientoJson.php?action=3",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                                //jsRemoveWindowLoad();
                                console.log(response);
                                if(response==1){
                                    $("#modalSeguimientoA").modal("hide");
                                    $("#mensaje").html('Información Modificada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                       document.location = 'registrar_GY_SEGUIMIENTO.php?idA=<?php echo md5($segui[8]) ?>&proyec='+pr;
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Modificar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalSeguimientoA").modal("hide");
                                        window.history.go(-1);
                                    })
                                }
                            }
                        });
                }

            </script>

            <script src="js/bootstrap.min.js"></script>
        
            <script type="text/javascript" src="js/select2.js"></script>
            <script type="text/javascript"> 
                $("#sltEst").select2();
                
            </script>
            
           
    </div>