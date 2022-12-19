
<!-- Librerias de carga para el datapicker -->


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
<div class="modal fade DocSop" id="modalDocumentoS" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 900px; margin-left: -20%">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Ingresar Documento Soporte</h4>
            </div>
            <div class="modal-body" >
                <div class="row text-left">
                    <form class="form-horizontal" id="formDocS" name="formDocS" method="post" action="jsonComercio/registrarRecaudoJson.php">                                                        
                                
                        <?php
                            require_once 'Conexion/conexion.php';
                            $idSe = $_POST['id'];
                                       
                            $seguimiento = "SELECT  s.id_unico,
                                                            s.fecha_seguimiento,
                                                            a.nombre
                                                    FROM gy_seguimiento s
                                                    LEFT JOIN gy_actividad_proyecto ap ON s.id_actividad_proyecto = ap.id_unico
                                                    LEFT JOIN gy_actividad a ON ap.id_actividad = a.id_unico
                                                    WHERE s.id_unico = '$idSe' ";
                            $seguim = $mysqli->query($seguimiento);
                            $SE = mysqli_fetch_row($seguim);
                            
                            $fecha_d = explode("-",$SE[1]);
                            $dia = $fecha_d[2];
                            $mes = $fecha_d[1];
                            $ann = $fecha_d[0];
                            
                            $fechaS = ''.$dia.'/'.$mes.'/'.$ann.'';
                        ?>
                        <div class="form-group" style="margin-top: 1%">
                            <input type="hidden" id="txtSeguimiento" name="txtSeguimiento" value="<?php echo $idSe ?>">
                            <label for="txtActividad" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" style="margin-left: -3%" ><strong class="obligado">*</strong>Actividad:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input name="txtActividad" id="txtActividad" style="width: 150%" type="text"  class="form-control " value="<?php echo $SE[2] ?>" readonly>
                            </div> 
                            
                            <label for="txtFecha" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" ><strong class="obligado">*</strong>Fecha:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input name="txtFecha" id="txtFecha"  type="text"  class="form-control " value="<?php echo $fechaS ?>" readonly>
                            </div>
                            
                            <label for="txtNombre" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" style="margin-left: -5%" ><strong class="obligado">*</strong>Nombre:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input name="txtNombre" id="txtNombre" style="width:120%" type="text"  class="form-control " >
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-top: 1%">
                            <label for="txtOb" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" style="margin-left: -3%" ><strong class="obligado">*</strong>Observaciones:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input name="txtOb" id="txtOb" style="width: 150%" type="text"  class="form-control ">
                            </div> 
                            
                            <label for="txtruta" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" ><strong class="obligado">*</strong>Archivo:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="file" class="form-control col-sm-1" name="txtruta" name="txtruta" title="Archivo" style="width: 200px;height: 40px">							
                            </div>
                            
                            
                        </div>
                    </form>
                    <div class="form-group form-inline" style="margin-top:5px;">
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top: 5px;">
                            <div class="table-responsive" >
                                <table id="tablaDoc" class=" col-sm-8 table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td style="display: none;">Identificador</td>
                                            <td width="7%" class="cabeza"></td>                                        
                                            <td class="cabeza"><strong>Fecha</strong></td>
                                            <td class="cabeza"><strong>Estado</strong></td>
                                            <td class="cabeza"><strong>Descripcion</strong></td>
                                            <td class="cabeza"><strong>Observaciones</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="cabeza" style="display: none;">Identificador</th>
                                            <th width="7%"></th>                                        
                                            <th class="cabeza">Fecha</th>
                                            <th class="cabeza">Estado</th>
                                            <th class="cabeza">Descripcion</th>
                                            <th class="cabeza">Observaciones</th>
                                        </tr>
                                    </thead>    
                                    <tbody>
                                        <?php 
                                            while ($row = mysqli_fetch_row($resultado)) { 

                                                $FI = $row[1];

                                                $FI = trim($FI, '"');
                                                $fecha_div = explode("-", $FI);
                                                $anioa = $fecha_div[0];
                                                $mesa = $fecha_div[1];
                                                $diaa = $fecha_div[2];
                                                $fecha = $diaa.'/'.$mesa.'/'.$anioa;
                                        ?>
                                                <tr>
                                                    <td style="display: none;"></td>
                                                    <td>
                                                        <a href="#" onclick="javascript:eliminarS(<?php echo $row[0];?>);">
                                                           <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                        </a>
                                                        <a href="#" onclick="javascript:modificarSA(<?php echo $row[0];?>);" >
                                                            <i title="Modificar" class="glyphicon glyphicon-edit"></i>
                                                        </a>
                                                        <a href="#" onclick="javascript:ingresarDoc(<?php echo $row[0];?>);">
                                                            <i title="Ingresar Documento" class="glyphicon glyphicon-cloud-upload"></i>
                                                        </a>  
                                                    </td>
                                                    <td class="campos" align="center" ><?php echo $fecha?></td>                
                                                    <td class="campos" align="left" ><?php echo $row[4]?></td>                
                                                    <td class="campos" align="left"><?php echo $row[2]?></td>                
                                                    <td class="campos" align="left"><?php echo $row[3]?></td>                
                                                </tr>
                                        <?php 
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>    
                </div>    
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="guardar" class="btn"  style="color: #000; margin-top: 2px" onclick="javascript:registrar();" title="Modificar Actividad"><li class="glyphicon glyphicon-floppy-disk" ></button>    
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
            </div>  
        </div>
    </div>

            <script>
                function registrar(){
                    //var tipoP = $("#tipoP").val();
                    // var proye = $("#ProAC").val();
                        //jsShowWindowLoad('Modificando Datos ...');
                        var formData = new FormData($("#formDocS")[0]);
                        $.ajax({
                            type: 'POST',
                            url: "jsonProyecto/gy_documento_proyectoJson.php?action=2",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                                //jsRemoveWindowLoad();
                                console.log(response);
                                if(response==1){
                                    $("#modalActividadP").modal("hide");
                                    $("#mensaje").html('Información Guardada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                       //document.location='registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='+tipoP+'&pro='+proye;
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Guardar Información');
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

