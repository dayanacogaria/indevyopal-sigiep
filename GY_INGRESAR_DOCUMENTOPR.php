<?php

    require_once './Conexion/conexion.php';
    require_once('Conexion/ConexionPDO.php');
    require_once './head_listar.php';

    $con    = new ConexionPDO();
    $anno   = $_SESSION['anno'];
    $compania = $_SESSION['compania'];
     @$idP = $_REQUEST['id'];
    
    $valor = $idP;
    $proyecto = "SELECT  p.id_unico,
                        p.titulo,
                        p.fecha_inicio
                    FROM gy_proyecto p
                    WHERE md5(p.id_unico) = '$idP' ";
    $proyec = $mysqli->query($proyecto);
    $PR = mysqli_fetch_row($proyec);
?>


        <title>Ingresar Documento</title>
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
    
            label #txtOb-error, #txtNombre-error, #txtruta-error  {
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
              var validator = $("#formD").validate({
                    ignore: "",
                errorPlacement: function(error, element) {

                  $( element )
                    .closest( "formD" )
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
                <div class="col-sm-10" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Ingresar Documento</h2>
                    <a href="<?php echo 'modificar_GY_PROYECTO.php?id='.md5($PR[0])?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-13px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:85%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 10px; margin-left: 6%;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo "Proyecto: ".ucwords((mb_strtolower($PR[1])));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="formD" id="formD" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         

                            <div class="col-sm-12 col-md-12 col-lg-12">  
                                <?php
                                    require_once 'Conexion/conexion.php';


                                    $fecha_d = explode("-",$PR[2]);
                                    $dia = $fecha_d[2];
                                    $mes = $fecha_d[1];
                                    $ann = $fecha_d[0];

                                    $fechaS = ''.$dia.'/'.$mes.'/'.$ann.'';
                                ?>
                                <div class="form-group" style="margin-top: 1%">
                                    <input type="hidden" id="txtidP" name="txtidP" value="<?php echo $PR[0] ?>">
                                    <label for="txtpro" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" style="margin-left: -1%" >Proyecto:</label>
                                    <div class="col-sm-2 col-md-2 col-lg-2">
                                        <input name="txtpro" id="txtpro" style="width: 150%" type="text"  class="form-control " value="<?php echo $PR[1] ?>" readonly>
                                    </div> 

                                    <label for="txtFecha" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" >Fecha Inicial:</label>
                                    <div class="col-sm-2 col-md-2 col-lg-2">
                                        <input name="txtFecha" id="txtFecha"  type="text"  class="form-control " value="<?php echo $fechaS ?>" readonly>
                                    </div>

                                    <label for="txtNombre" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" style="margin-left: -5%" >Nombre:</label>
                                    <div class="col-sm-2 col-md-2 col-lg-2">
                                        <input name="txtNombre" id="txtNombre" style="width:120%" type="text" title="Ingrese el Nombre"  class="form-control " required="">
                                    </div>
                                </div>
                        
                                <div class="form-group" style="margin-top: 1%">
                                    <label for="txtOb" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" style="margin-left: -1%" ><strong class="obligado">*</strong>Observaciones:</label>
                                    <div class="col-sm-2 col-md-2 col-lg-2">
                                        <input name="txtOb" id="txtOb" style="width: 150%" type="text" title="Ingrese las Observaciones" class="form-control " required="">
                                    </div> 

                                    <label for="txtruta" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" ><strong class="obligado">*</strong>Archivo:</label>
                                    <div class="col-sm-2 col-md-2 col-lg-2">
                                        <input type="file" class="form-control col-sm-1" id="txtruta" name="txtruta" title="Seleccione Archivo" style="width: 200px;height: 40px" required="">							
                                    </div>

                                    <label for="No" class="col-sm-2 control-label"></label>
                                    <div class="col-sm-2 col-md-2 col-lg-2">
                                        <button type="submit"  class="btn btn-primary sombra col-sm-1" style="width:40px;"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                    </div>

                                </div>      
                            </div>
                            
                        </form>
                    </div>
                </div>

                <!--informacion adicional-->
                <!--<div class="col-sm-2 col-sm-2 " style="margin-top:-22px">
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
                                    <a class="btn btn-primary btnInfo" href="GY_ESTADO.php">ESTADO</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>-->

                <script>
                    function registrar(){
                        //var tipoP = $("#tipoP").val();
                        // var proye = $("#ProAC").val();
                        jsShowWindowLoad('Guardando Datos ...');
                        var id_p = $("#txtidP").val();
                        var formData = new FormData($("#formD")[0]);
                        $.ajax({
                            type: 'POST',
                            url: "jsonProyecto/gy_documento_proyectoJson.php?action=4",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                                jsRemoveWindowLoad();
                                console.log(response);
                                if(response==1){
                                    //$("#modalActividadP").modal("hide");
                                    $("#mensaje").html('Información Guardada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                       //document.location='registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='+tipoP+'&pro='+proye;
                                       document.location.reload();
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalActividadP").modal("hide");
                                        //document.location='registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='+tipoP+'&pro='+proye;
                                    })
                                }
                            }
                        });
                    }
                    
                    function mostrarDoc(ruta){
                        var myApp = new ActiveXObject("Word.Application");
                        if (myApp != null)
                        {
                          myApp.Visible = true;
                          myApp.Documents.Open(ruta);
                        }
                    }
                    
                    function eliminarD(id) {
                        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "jsonProyecto/gy_documento_proyectoJson.php?action=1",
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
                
            
                <!--listado actividad contribuyente-->
                <div class="form-group form-inline" style="margin-top:5px;">
                    <?php require_once './menu.php'; 
                        $documento = "SELECT dp.nombre,"
                                . "          dp.observaciones,"
                                . "          p.titulo,"
                                . "          dp.ruta,"
                                . "          dp.id_unico"
                                . " FROM gy_documento_proyecto dp"
                                . " LEFT JOIN gy_proyecto p ON dp.id_proyecto = p.id_unico"
                                . " WHERE  dp.id_proyecto = '$PR[0]'";
                        $resultado = $mysqli->query($documento);
                    ?>
                    
                    <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 1%;">
                        <div class="table-responsive contTabla" >
                            <table id="tabla" class=" col-sm-8 table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Proyecto</strong></td>
                                        <td class="cabeza"><strong>Nombre</strong></td>
                                        <td class="cabeza"><strong>Observaciones</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Proyecto</th>
                                        <th class="cabeza">Nombre</th>
                                        <th class="cabeza">Observaciones</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                        while ($row = mysqli_fetch_row($resultado)) { 
                                       
                                            
                                    ?>
                                            <tr>
                                                <td style="display: none;"></td>
                                                <td>
                                                    <a href="#" onclick="javascript:eliminarD(<?php echo $row[4];?>);">
                                                       <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                    </a>
                                                    <!--<a href="#" onclick="javascript:modificarSA(<?php echo $row[0];?>);" >
                                                        <i title="Modificar" class="glyphicon glyphicon-edit"></i>
                                                    </a>-->
                                                    <a href="#" onclick="javascript:window.open(<?php echo "'".$row[3]."'";?>);">
                                                        <i title="Visualizar Documento" class="glyphicon glyphicon-send"></i>
                                                    </a>  
                                                </td>
                                                <td class="campos" align="left" ><?php echo $row[2]?></td>                
                                                <td class="campos" align="left"><?php echo $row[0]?></td>                
                                                <td class="campos" align="left"><?php echo $row[1]?></td>                
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

                <?php require_once './footer.php'; ?>
                    <script type="text/javascript" src="js/select2.js"></script>
                    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
                    <script src="js/bootstrap.min.js"></script>

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
    </body>
</html>