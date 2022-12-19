<?php

    require_once './Conexion/conexion.php';
    require_once('Conexion/ConexionPDO.php');
    require_once './head_listar.php';

    $con    = new ConexionPDO();
    $anno   = $_SESSION['anno'];
    $compania = $_SESSION['compania'];
    @$tipoPro = $_GET['tipo'];
    @$Pro     = $_GET['pro'];
    @$id_pro  = $_GET['proyec'];
    $valor1 = $id_pro;
    #echo 'proyec: '.$valor1;
    if(empty($tipoPro)){
        $t = "Tipo Proyecto";
        $datos[0] = "";
    }else{
        $sql = "SELECT id_unico , nombre FROM gy_tipo_proyecto WHERE id_unico = '$tipoPro'";
        $res = $mysqli->query($sql);

        $datos = mysqli_fetch_row($res);

        $t = $datos[1];

    }

    if(empty($Pro)){
        $P = "Proyecto";
        $dat[0] = "";
    }else{
        $sql1 = "SELECT id_unico , titulo, fecha_inicio FROM gy_proyecto WHERE id_unico = '$Pro'";
        $res1 = $mysqli->query($sql1);

        $dat = mysqli_fetch_row($res1);

        $P = $dat[1];
        
        $f_d = explode("-",$dat[2]);
        $dia = $f_d[2];
        $mes = $f_d[1];
        $ani = $f_d[0];
        
        $fechaIPr = ''.$dia.'/'.$mes.'/'.$ani.'';

    }
?>


        <title>Cronograma de Actividades</title>
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
    
            label #sltActi-error, #fechaini-error, #fechafin-error, #sltTer-error, #valorP-error  {
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


                $("#fechaini").datepicker({changeMonth: true,})
                $("#fechafin").datepicker({changeMonth: true});


            });
        </script>
    </head>
    
    <body >   

        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once ('menu.php'); ?>
                <div class="col-sm-8" style="margin-top:-22px;">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-bottom: 10px;">Cronograma de Actividades</h2>
                    <!--Volver-->
                    <a href="modificar_GY_PROYECTO.php?id=<?php echo $id_pro ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo 'Proyecto: '.$P ?></h5>
                
                    <!--<a href="listar_GC_CONTRIBUYENTE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>-->
                    <div class="client-form contenedorForma" style="margin-top:-7px;margin-bottom: 20px">
                       <p align="center" style="margin-bottom: 25px; margin-top: 15px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: 5px; margin-left: 5px;">
                            <label for="sltTipoP" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Proyecto:</label>
                            <div class="classTipoP">   
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <input type="hidden" id="compania" name="compania" value="<?php echo $compania ?>">
                                    <input type="hidden" id="proyec" name="proyec" value="<?php echo  $id_pro  ?>">
                                    <input type="hidden" id="sltTipoP" name="sltTipoP" value="<?php echo $datos[0] ?>">
                                    <input type="text" id="txtTipoP" name="txtTipoP" class="form-control" value="<?php echo $t ?>"readonly>
                                    <!--<select name="sltTipoP" id="sltTipoP" required  class="form-control select2_single" title="Seleccione el Tipo de Proyecto" >
                                        <option value="<?php #echo $datos[0] ?>"><?php #echo $t ?></option>
                                    </select>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $.ajax({
                                                type: "POST",
                                                url: "buscar_GY_TIPO_PROYECTO.php",
                                                success: function(response){
                                                    $('.classTipoP select').html(response).fadeIn();
                                                    $('#sltTipoP').css('display','none');
                                                }
                                            });
                                        });
                                    </script>-->
                                </div>
                            </div>
                    
                            <label for="sltProyecto" class="col-sm-1 col-md-1 col-lg-1"><strong style="color:#03C1FB;">*</strong>Proyecto:</label>
                            <div class="classProyecto"> 
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <input type="hidden" id="sltProyecto" name="sltProyecto" value="<?php echo $dat[0] ?>">
                                    <input type="text" id="txtP" name="txtP" class="form-control" value="<?php echo $P ?>"readonly>
                                    <!--<select name="sltProyecto" id="sltProyecto" class="form-control select2_single" title="eleccione Representante Legal" >
                                        <option value="<?php #echo $dat[0] ?>"><?php #echo $P ?></option>
                                    </select>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $(".classTipoP select").change(function(){
                                                var form_data = {
                                                    is_ajax: 1,
                                                    id_TipoP: +$(".classTipoP select").val()
                                                };
                                                $.ajax({
                                                    type: "POST",
                                                    url: "buscar_GY_PROYECTO.php",
                                                    data: form_data,
                                                    success: function(response){
                                                        $('.classProyecto select').html(response).fadeIn();
                                                        $('#sltProyecto').css('display','none');
                                                    }
                                                });
                                            });
                                        });
                                    </script>-->
                                </div>
                            </div>    
                        </div>

                       <div class="form-group" style="margin-top: 10%"></div>
                    </div>    

                    <?php
                        $actividad = "SELECT id_unico, nombre FROM gy_actividad WHERE compania = '$compania'";
                        $acti = $mysqli->query($actividad);
                    ?>  
                        
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()" style="margin-bottom:-45px">
                        <input type="hidden" id="fechaIP" name="fechaIP" value="<?php echo $fechaIPr ?>">
                        <input type="hidden" name="txttipo" id="txttipo" value="<?php echo $datos[0] ?>">
                        <input type="hidden" name="txtpro" id="txtpro" value="<?php echo $dat[0] ?>">      
                        <div class="form-group" style="margin-top: 0.5%" >
                            <label for="sltActi" class="col-sm-1 col-md-1 col-lg-1 control-label" style="margin-left: 1%"><strong style="color:#03C1FB;">*</strong>Actividad:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3" style="margin-left: 3%">
                                <select name="sltActi" id="sltActi" required  class="form-control select2_single" title="Seleccione la Actividad">
                                    <option value="">Actividad</option>
                                    <?php
                                        while($AC = mysqli_fetch_row($acti)){
                                    ?>
                                            <option value="<?php echo $AC[0] ?>"><?php echo ucwords( (mb_strtolower($AC[1]))); ?></option>
                                    <?php
                                        }
                                    ?>    
                                </select>
                            </div>

                            <label for="fechaini" type = "date" class="control-label col-sm-2 col-md-2 col-lg-2" style="margin-left: -1%"><strong class="obligado">*</strong>Fecha Inicio:</label>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <input readonly="readonly " type="text" name="fechaini"  id="fechaini"  class="form-control"style="height:30px;width:110px" onchange="javaScript:fechaInicial();" title="Ingrese la Fecha de Inicio" required="" >
                            </div>
                    
                            <label for="fechafin" type = "date"  class="control-label col-sm-2 col-md-2 col-lg-2" style="margin-left: 1.5%"><strong class="obligado" >*</strong>Fecha Final:</label>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <input readonly="readonly " type="text" name="fechafin"  id="fechafin"  class="form-control" style="height:30px;width:110px" title="Ingrese la Fecha Final" required="" disabled="" > 
                            </div>
                        </div>

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
                                            WHERE md5(tp.id_proyecto) = '$valor1'";
                            $resp = $mysqli->query($responsable);
                        ?>

                        <div class="form-group" style="margin-top: 0.1%;" >
               
                            <label for="sltTer" class="col-sm-1 col-md-1 col-lg-1 control-label" style="margin-left: 1%" ><strong style="color:#03C1FB;">*</strong>Responsable:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3" style="margin-left: 3%">            
                                <select name="sltTer" id="sltTer" required  class="form-control select2_single" title="Seleccione el Responsable">
                                    <option value="">Responsable Actividad</option>
                                    <?php
                                        while($RA = mysqli_fetch_row($resp)){
                                    ?>
                                            <option value="<?php echo $RA[0] ?>"><?php echo ucwords( (mb_strtolower($RA[1]))); ?></option>
                                    <?php
                                        }
                                    ?>    
                                </select>
                            </div>

                            <label for="valorP" type = "text" class="control-label col-sm-2 col-md-2 col-lg-2" style="margin-left: -1%" ><strong class="obligado">*</strong>Valor Programado:</label>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <input type="text" name="valorP"  id="valorP"  class="form-control" style="height:30px;width:110px" placeholder="Valor" onkeypress="return txtValida(event,'num', 'valor', '2');" onkeyup="formatC('valorP');" title="Ingrese el Valor Programado" required="" >
                            </div>
                
                            <label for="valorE" type = "text"  class="control-label col-sm-2 col-md-2 col-lg-2" style="margin-left: 1.5%">Valor Ejecutado:</label>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <input  type="text" name="valorE"  id="valorE"  class="form-control" style="height:30px;width:110px" placeholder="Valor" onkeypress="return txtValida(event,'num', 'valor', '2');" onkeyup="formatC('valorE');" title="Ingrese el Valor Ejecutado"> 
                            </div>

                            <div>
                                <label for="no" class=" control-label"></label>
                                <button type="submit" class="btn btn-primary shadow" style="  margin-left: 5%"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                            </div>
                
                        </div>          
                    </form>
                </div>

                <!--informacion adicional-->
                <div class="col-sm-2 col-sm-2 col-sm-offset-8" style="margin-top: -21.7%;">
                    <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                            </tr>    
                            <tr>                                        
                                <th>
                                    <h2 class="titulo" align="center" style=" font-size:17px;">Información Adicional</h2>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php #$idContribuyente=$_GET['id']; ?>
                            <tr>                                    
                                <!--<td>
                                    <a href="registrar_GF_TERCERO.php" class="btn btn-primary btnInfo">RESPONSABLE</a>
                                </td>-->
                            </tr>
                            <tr>                                    
                                <td>
                                    <a href="GY_ACTIVIDAD.php?actividadP=1" class="btn btn-primary btnInfo">ACTIVIDAD </a>                                         
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a href="GY_PROYECTO.php?actividadP=1" class="btn btn-primary btnInfo">PROYECTO</a>
                                </td>
                            </tr>
                             <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="GY_RESPONSABLE_PROYECTO.php?id=<?php echo $id_pro ?>">RESPONSABLE</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Funcion para que recargue la pagina con las actividades del proyecto seleccionado -->
                <script type="text/javascript">
                    $("#sltProyecto").click(function(){
                        var tipoP = $("#sltTipoP").val();
                        var proye = $("#sltProyecto").val();
                        document.location = 'registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='+tipoP+'&pro='+proye;
                    });
                </script>
                <script>
                    function registrar(){
                        var tipoP = $("#txttipo").val();
                        var proye = $("#sltProyecto").val();
                        var pr    = $("#proyec").val();
                        jsShowWindowLoad('Guardando Datos ...');
                        var formData = new FormData($("#form")[0]);
                        $.ajax({
                            type: 'POST',
                            url: "jsonProyecto/gy_actividad_proyectoJson.php?action=2",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                                jsRemoveWindowLoad();
                                console.log("respuesta: "+response);
                                if(response==1){
                                    $("#mensaje").html('Información Guardada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                       document.location='registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='+tipoP+'&pro='+proye+'&proyec='+pr;
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
                    
                    function eliminaAP(id) {
                        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "jsonProyecto/gy_actividad_proyectoJson.php?action=1",
                                data: form_data,
                                success: function(response) {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Eliminada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            document.location.reload();
                                        })
                                    } else if(response == 2){
                                        $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                             $("#modalMensajes").modal("hide");
                                        })
                                    } else {
                                        $("#mensaje").html('No se puede eliminar la información, ya que la actividad posee Seguimiento(s)');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                             $("#modalMensajes").modal("hide");
                                        })
                                    }
                                }
                            });
                        });
                    }
                </script>
                <?php require_once 'modal_Modificar_Actividad_Proyecto.php';  ?>
                <script>
                    function abrirSeguimientoActividad(){
                        $("#Segui_Act").modal('show');
                    }
                    function modificarAP(id_AP){
                        var pr = $("#proyec").val();
                        var comp = $("#compania").val();
                        var form_data = {
                            id:id_AP,
                            pro:pr,
                            co:comp
                        }

                        //Envio ajax
                        $.ajax({
                            url:'modal_Modificar_Actividad_Proyecto.php#modalActividadP',
                            type:'POST',
                            data:form_data,
                            success: function(data,textStatus,jqXHR) {
                                $("#modalActividadP").html(data);
                                $(".recaDec").modal('show');
                            },error: function(data,textStatus,jqXHR) {
                                alert('Error : D'+data+', status :'+textStatus+', jqXHR : '+jqXHR);
                            } 
                        });

                    }
                    
                </script>   
                    
                <!--listado actividad contribuyente-->
                <div class="col-sm-8" style="margin-top:3%">
                    <div class="table-responsive contTabla" >
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>    
                                <tr>
                                    <td class="oculto" >Identificador</td>                            
                                    <td width="7%" class="cabeza"></td>
                                    <td class="cabeza"><strong>Actividad </strong></td>
                                    <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                    <td class="cabeza"><strong>Fecha Final</strong></td>
                                    <td class="cabeza"><strong>Valor Progrmado</strong></td>
                                    <td class="cabeza"><strong>Valor Ejecutado</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>                                    
                                    <th width="7%" class="cabeza"></th>
                                    <th class="cabeza">Actividad</th>
                                    <th class="cabeza">Fecha Inicio</th>
                                    <th class="cabeza">Fecha Final</th>
                                    <th class="cabeza">Valor Progrmado</th>
                                    <th class="cabeza">Valor Ejecutado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    if(!empty($Pro)){
                                        $consulta = "SELECT ac.id_unico,
                                                                ac.fecha_inicio_programada,
                                                                ac.fecha_final_programada,
                                                                ac.valor_programado,
                                                                ac.valor_ejecutado,
                                                                a.nombre
                                                        FROM gy_actividad_proyecto ac
                                                        LEFT JOIN gy_actividad a ON ac.id_actividad = a.id_unico
                                                        WHERE ac.id_proyecto = '$Pro' 
                                                        ORDER BY ac.fecha_inicio_programada, ac.fecha_final_programada ASC";

                                        $resultadoAC = $mysqli->query($consulta);
                                        while($row = mysqli_fetch_row($resultadoAC)){    
                                ?>
                                            <tr>
                                                <td style="display: none;"></td>
                                                <td>
                                                    <a href="#" onclick="javascript:eliminaAP(<?php echo $row[0];?>);">
                                                        <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                    </a>     
                                                    <a title="Modificar" style="text-decoration: none" class="glyphicon glyphicon-edit" onclick="javascript:modificarAP(<?php echo $row[0] ?>)" ></a>
                                                    <a href="registrar_GY_SEGUIMIENTO.php?idA=<?php echo md5($row[0])?>&proyec=<?php echo $valor1 ?>" >
                                                        <i title="Seguimiento" class="glyphicon glyphicon-eye-open"></i>
                                                    </a>   
                                                </td>

                                                <?php
                                                    $fechadiv1  = explode("-", $row[1]);
                                                    $diaI       = $fechadiv1[2];
                                                    $mesI       = $fechadiv1[1];
                                                    $anioI      = $fechadiv1[0];  
                                                    $fechaI     = ''.$diaI.'/'.$mesI.'/'.$anioI.'';
                                                    $fechadiv2  = explode("-", $row[2]);
                                                    $diaF       = $fechadiv2[2];
                                                    $mesF       = $fechadiv2[1];
                                                    $anioF      = $fechadiv2[0];  
                                                    $fechaF     = ''.$diaF.'/'.$mesF.'/'.$anioF.'';
                                                ?>
                                                <td class="campos"><?php echo $row[5]?></td> 
                                                <td class="campos" align="center"><?php echo $fechaI ?></td>
                                                <td class="campos" align="center"><?php echo $fechaF ?></td>         
                                                <td class="campos" align="right"><?php echo number_format($row[3],0,'.',',')?></td> 
                                                <td class="campos" align="right"><?php echo number_format($row[4],0,'.',',')?></td>                            
                                            </tr>
                                <?php
                                        }
                                    }    
                                ?>
                            </tbody>
                         </table>
                    </div>
                </div>
                <div class="modal fade" id="myModal" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>¿Desea eliminar el registro seleccionado?</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal1" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>Información eliminada correctamente.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="Segui_Act" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content" style="width: 450px;">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Seguimiento</h4>
                            </div>
                            <div class="modal-body" >
                                <label id="mensaje" name="mensaje" style="font-weight: normal"></label>  
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="guardar" class="btn"  style="color: #000; margin-top: 2px"  title="Guardar Recaudo"><li class="glyphicon glyphicon-floppy-disk" ></button>    
                                <button type="button" id ="soloDec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
                            </div>  
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modificar_ActividadP" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content" style="width: 450px;">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Modificar Actividad </h4>
                            </div>
                            <form method="POST" action="javascript:guardarDEC()">
                               <div class="modal-body" >
                                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>  
                                </div>
                                <div id="forma-modal" class="modal-footer">
                                    <button type="button" id="guardar" class="btn"  style="color: #000; margin-top: 2px"  title="Guardar Recaudo"><li class="glyphicon glyphicon-floppy-disk" ></button>    
                                    <button type="button" id ="soloDec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
                                </div> 
                            </form>
                        </div>
                    </div>
                </div>
                <?php require_once './footer.php'; ?>
                <script type="text/javascript" src="js/select2.js"></script>
                <link rel="stylesheet" href="css/bootstrap-theme.min.css">
                <script src="js/bootstrap.min.js"></script>
                <script>
                    $(".select2_single").select2();
                </script>
                <script>
                function fechaInicial(){
                    $("#fechafin").prop("disabled", false);
                    var fechain= document.getElementById('fechaini').value;
                    var fechafi= document.getElementById('fechafin').value;
                    var fi = document.getElementById("fechafin");
                    fi.disabled=false;
                    $( "#fechafin" ).datepicker( "destroy" );
                    $( "#fechafin" ).datepicker({ changeMonth: true, minDate: fechain}); 
                }
                </script>
                <script> 
                    
                    //$("#sltActi").click(function(){
                        var fechaIA  = document.getElementById('fechaini').value;
                        var fechaIP  = document.getElementById('fechaIP').value;
                        console.log("fecha pro: "+fechaIP);
                        var fia = document.getElementById("fechaini");
                        fia.disabled=false;

                        $("#fechaini").datepicker("destroy");
                        $("#fechaini").datepicker({changeMonth: true, minDate: fechaIP});
                    //});
                    
                    
                </script>
            </div>
        </div>    
    </body>
</html>