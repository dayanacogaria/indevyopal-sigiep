<?php

    require_once './Conexion/conexion.php';
    require_once('Conexion/ConexionPDO.php');
    require_once './head_listar.php';

    $con        = new ConexionPDO();
    $anno       = $_SESSION['anno'];
    $compania   = $_SESSION['compania'];
    #session_start();
    @$id = $_GET['idA'];
    @$id_pro  = $_GET['proyec'];
    $valor = $id_pro;
    
    #echo 'proye: '.$valor;
    $Acti_Pro = "SELECT ap.id_proyecto,
                        ap.id_tipo_proyecto,
                        ap.id_actividad,
                        a.nombre,
                        p.titulo,
                        ap.id_unico,
                        ap.fecha_inicio_programada
                FROM gy_actividad_proyecto ap
                LEFT JOIN gy_actividad a ON ap.id_actividad = a.id_unico
                LEFT JOIN gy_proyecto  p ON ap.id_proyecto  = p.id_unico
                WHERE md5(ap.id_unico) = '$id'";
    
    $bus = $mysqli->query($Acti_Pro);
    $res = mysqli_fetch_row($bus);
    
       $f_d = explode("-",$res[6]);
        $dia = $f_d[2];
        $mes = $f_d[1];
        $ani = $f_d[0];
        
        $fechaIAc = ''.$dia.'/'.$mes.'/'.$ani.'';

?>


        <title>seguimiento</title>
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
    
            label #sltEstado-error, #sltFechaS-error, #txtDescripcion-error {
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


                $("#sltFechaS").datepicker({changeMonth: true,});
                


            });
        </script>
    </head>
    
    <body >   

        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once ('menu.php'); ?>
                <div class="col-sm-8" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Seguimiento</h2>
                    <a href="<?php echo 'registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='.$res[1].'&pro='.$res[0].'&proyec='.$id_pro;?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-13px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:85%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 10px; margin-left: 6%;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo "Proyecto: ".ucwords((mb_strtolower($res[4])));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         

                            <div class="col-sm-12 col-md-12 col-lg-12">  
                                <div class="form-group form-inline" style="margin-top:-20px;">
                                    <label for="txtActividad" class="col-sm-2 col-md-2 col-lg-2 control-label" style="margin-left: -3%">Actividad:</label>
                                    <input style="width:25%" class="col-sm-2 input-sm" type="text" name="txtActividad" id="txtActividad"  value="<?php echo $res[3] ?>" readonly>
                                    <input type="hidden" id="fechaIA" name="fechaIA" value="<?php echo  $fechaIAc?>">
                                    <input type="hidden" name="Acti" id="Acti" value="<?php echo $res[5] ?>">
                                    <input type="hidden" name="id_AP" id="id_AP" value="<?php echo $id ?>">
                                    <input type="hidden" id="compania" id="compania" value="<?php echo $compania ?>">
                                    <!---Script para invocar Date Picker-->
                                    <script type="text/javascript">
                          
                                        $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        });
                                    </script>
                            
                                    <label for="sltFechaS" class="col-sm-2 col-md-2 col-lg-2 control-label" style="margin-left: -3%"><strong class="obligado">*</strong>Fecha:</label>
                                    <input style="width:13%;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaS" id="sltFechaS"    title="Ingrese la fehca del seguimiento" readonly required>                            
                                        
                                    <label for="sltEstado" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Estado:</label>
                                    <select name="sltEstado" id="sltEstado" title="Seleccione el Estado" style="width: 12%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2" required>
                                        
                                        <?php 
                                            $es   = "SELECT id_unico, nombre FROM gy_estado WHERE compania = '$compania'";
                                            $esta = $mysqli->query($es);
                                            echo "<option value=''>Estado</option>";                            		
                            		    while($rowES = mysqli_fetch_row($esta)){
                            		        echo "<option value=".$rowES[0].">".$rowES[1]."</option>";
                            		    }     	                                
                                        ?>
                                    </select>
                                        
                                </div>

                                <div class="form-group form-inline">
                            
                                    <label for="txtDescripcion" class="col-sm-2 control-label" style="margin-left: -3%"><strong class="obligado">*</strong>Descripcion:</label>
                                    <input style="width:25%" class="col-sm-2 input-sm" type="text" name="txtDescripcion" id="txtDescripcion" title="Ingrese la Descripcion" placeholder="Descripción" required>
                            
                                    <label for="txtObservaciones" class="col-sm-2 control-label" style="margin-left: -3%">Observaciones:</label>
                                    <input style="width:35%" class="col-sm-2 input-sm" type="text" name="txtObservaciones" id="txtObservaciones" title="Ingrese las Observaciones" placeholder="Observaciones">
                            
                                    <label for="No" class="col-sm-2 control-label"></label>
                                    <button type="submit"  class="btn btn-primary sombra col-sm-1" style="width:40px; margin-left: 73% ; margin-top: -4%"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                </div>      
                            </div>
                            <input type="hidden" id="txtidP" name="txtidP" value="<?php echo $id_pro ?>">
                            <input type="hidden" id="txtiP" name="txtiP" value="<?php echo $valor ?>">
                        </form>
                    </div>
                </div>

                <!--informacion adicional-->
                <div class="col-sm-2 col-sm-2 " style="margin-top:-22px">
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
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="GY_ESTADO.php?seguiAct=1">ESTADO</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <script>
                    function registrar(){
                        //var tipoP = $("#txttipo").val();
                        //var proye = $("#sltProyecto").val();
                        var act = $("#id_AP").val();
                        var id_p = $("#txtidP").val();            
                        //jsShowWindowLoad('Guardando Datos ...');
                        var formData = new FormData($("#form")[0]);
                        $.ajax({
                            type: 'POST',
                            url: "jsonProyecto/gy_seguimientoJson.php?action=2",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                                //jsRemoveWindowLoad();
                                console.log(response);
                                if(response==1){
                                    $("#mensaje").html('Información Guardada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        document.location='registrar_GY_SEGUIMIENTO.php?idA='+act+'&proyec='+id_p;
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalMensajes").modal("hide");
                                    })
                                }
                            }
                        });
                    }
                    
                    function eliminarS(id) {
                        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            //jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "jsonProyecto/gy_seguimientoJson.php?action=1",
                                data: form_data,
                                success: function(response) {
                                    //jsRemoveWindowLoad();
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
                                        $("#mensaje").html('No se puede eliminar la información, ya que el seguimiento posee Seguimiento(s)');
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
                
                <?php require_once 'modal_Modificar_Seguimiento.php';  ?> 
         
                
                <script>
                    function modificarSA(id){
                        var id_proy = $("#txtiP").val(); 
                        var comp = $("#compania").val();
                        var form_data = {
                            id:id,
                            idpro:id_proy,
                            co:comp
                        }

                        //Envio ajax
                        $.ajax({
                            url:'modal_Modificar_Seguimiento.php#modalSeguimientoA',
                            type:'POST',
                            data:form_data,
                            success: function(data,textStatus,jqXHR) {
                                $("#modalSeguimientoA").html(data);
                                $(".reca").modal('show');
                            },error: function(data,textStatus,jqXHR) {
                                alert('Error : D'+data+', status :'+textStatus+', jqXHR : '+jqXHR);
                            } 
                        });
                    }    

                </script>
                
                <!--listado actividad contribuyente-->
                <div class="form-group form-inline" style="margin-top:5px;">
                    <?php require_once './menu.php'; 
                        $sql = "SELECT  s.id_unico,
                                        s.fecha_seguimiento,
                                        s.descripcion,
                                        s.observaciones,
                                        e.nombre
                                FROM gy_seguimiento s 
                                LEFT JOIN gy_estado e ON s.id_estado = e.id_unico
                                WHERE md5(s.id_actividad_proyecto) = '$id' ORDER BY s.fecha_seguimiento DESC";
                    
                        $resultado = $mysqli->query($sql);
                    ?>
                    
                    <div class="col-sm-8 col-md-8 col-lg-8" style="margin-top: 5px;">
                        <div class="table-responsive contTabla" >
                            <table id="tabla" class=" col-sm-8 table table-striped table-condensed" class="display" cellspacing="0" width="100%">
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
                                                    <a href="GY_INGRESAR_DOCUMENTO.php?id=<?php echo md5($row[0]); ?>&proyec=<?php echo $valor ?>" >
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

            <!--Script que dan estilo al formulario-->

            <!-- <script type="text/javascript" src="js/menu.js"></script>
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/bootstrap.min.js"></script>
            <!--Scrip que envia los datos para la eliminación-->
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
                <script src="js/bootstrap.min.js"></script>
            <script type="text/javascript">
                function eliminar(id)
                {
                    var result = '';
                    $("#myModal").modal('show');
                    $("#ver").click(function(){
                        $("#mymodal").modal('hide');
                        $.ajax({
                            type:"GET",
                            url:"json/eliminarAfiliacionJson.php?id="+id,
                            success: function (data) {
                                result = JSON.parse(data);
                                if(result==true)
                                    $("#myModal1").modal('show');
                                else
                                    $("#myModal2").modal('show');
                            }
                        });
                    });
                }
                
                
            </script>

            <script type="text/javascript">
                function modal()
                {
                    $("#myModal").modal('show');
                }
            </script>
       
            <script type="text/javascript">
                function recargar()
                {
                    window.location.reload();     
                }
            </script>     
       
            <!--Actualiza la página-->
            <script type="text/javascript">
    
                $('#ver1').click(function(){ 
                    reload();
                    //window.location= '../registrar_GN_ACCIDENTE.php?idE=<?php #echo md5($_POST['sltEmpleado'])?>';
                    //window.location='../listar_GN_ACCIDENTE.php';
                    window.history.go(-1);        
                });
    
            </script>

            <script type="text/javascript">    
                $('#ver2').click(function(){
                    window.history.go(-1);
                });    
            </script>
        </div>

        <script>
            function fechaInicial(){
                var fechain= document.getElementById('sltFechaA').value;
                var fechafi= document.getElementById('sltFechaR').value;
                var fi = document.getElementById("sltFechaR");
                fi.disabled=false;
       
                $( "#sltFechaR" ).datepicker( "destroy" );
                $( "#sltFechaR" ).datepicker({ changeMonth: true, minDate: fechain});
           
            }
        </script>
        <script>
            var fechaIAc  = document.getElementById('sltFechaS').value;
                        var fechaIP  = document.getElementById('fechaIA').value;
                        console.log("fecha pro: "+fechaIP);
                        var fia = document.getElementById("sltFechaS");
                        fia.disabled=false;

                        $("#sltFechaS").datepicker("destroy");
                        $("#sltFechaS").datepicker({changeMonth: true, minDate: fechaIP});
        </script>    
        <script type="text/javascript" src="js/select2.js"></script>
        <script>
           $("#sltEmpleado").select2();
           $("#sltTipo").select2();
           $("#sltTercero").select2();
           $("#sltEstado").select2();
        </script>
    </body>
</html>