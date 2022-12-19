<?php 
  require_once('Conexion/conexion.php');
  require_once('Conexion/ConexionPDO.php');
  require_once './head.php';
  $con = new ConexionPDO();
  $compania = $_SESSION['compania'];
  $panno    = $_SESSION['anno'];
?>
<html>
    <head>
        <title>Cargar Tablas</title>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <script src="js/md5.pack.js"></script>
        <style>

            label #archivo-error {
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
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <?php #* Tabla Terceros *#
                    if($_GET['t']==1) { ?>
                        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Subir Terceros</h2>
                        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">    
                            <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="JavaScript:guardar()">
                                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Terceros.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                                <div class="form-group" style="margin-top: -10px; ">
                                    <input type="hidden" id="action" name="action" value="3">
                                    <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                                    <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                   <label for="no" class="col-sm-5 control-label"></label>
                                   <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                                </div>
                                <input type="hidden" name="MM_insert" >
                            </form>
                        </div>
                    <?php }  
                    #* Plan Contable
                    elseif($_GET['t']==2) { ?>
                        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Subir Plan Contable</h2>
                        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">
                            <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="JavaScript:guardar()">
                                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Plan_Contable.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                                <div class="form-group" style="margin-top: -10px; ">
                                    <input type="hidden" id="action" name="action" value="4">
                                    <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                                    <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                   <label for="no" class="col-sm-5 control-label"></label>
                                   <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                                </div>
                                <input type="hidden" name="MM_insert" >
                            </form>
                        </div>
                    <?php } 
                    #* Plan Presupuestal 
                    elseif($_GET['t']==3) { ?>   
                        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Subir Plan Presupuestal</h2>
                        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">
                            <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="JavaScript:guardar()">
                                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Plan_Presupuestal.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                                <div class="form-group" style="margin-top: -10px; ">
                                    <input type="hidden" id="action" name="action" value="5">
                                    <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                                    <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                   <label for="no" class="col-sm-5 control-label"></label>
                                   <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                                </div>
                                <input type="hidden" name="MM_insert" >
                            </form>
                        </div>
                    <?php } 
                    #* Configuración presupuestal por fuentes 
                    elseif($_GET['t']==4){ ?> 
                        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Subir Configuración Presupuestal Instituciones Educativas</h2>
                        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">
                            <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="JavaScript:guardar2()">
                                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Configuracion_Pptal_Instituciones.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                                <div class="form-group" style="margin-top: -10px; ">
                                    <input type="hidden" id="action" name="action" value="8">
                                    <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                                    <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                   <label for="no" class="col-sm-5 control-label"></label>
                                   <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                                </div>
                                <input type="hidden" name="MM_insert" >
                            </form>
                        </div>   
                    <?php } 
                    #* Saldos Iniciales Contabilidad
                    elseif($_GET['t']==5){ ?>
                        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Subir Saldos Iniciales Contabilidad</h2>
                        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">
                            <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="JavaScript:validarsi()">
                                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Saldos_Iniciales.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                                <div class="form-group" style="margin-top: -10px; ">
                                    <input type="hidden" id="action" name="action" value="10">
                                    <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                                    <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                   <label for="no" class="col-sm-5 control-label"></label>
                                   <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                                </div>
                                <input type="hidden" name="MM_insert" >
                            </form>
                        </div>   
                    <?php } 
                    #* Plan Inventario
                    elseif($_GET['t']==6){ ?>
                        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Plan Inventario</h2>
                        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">
                            <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="JavaScript:planInventario()">
                                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Plan_Inventario.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                                <div class="form-group" style="margin-top: -10px; ">
                                    <input type="hidden" id="action" name="action" value="13">
                                    <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                                    <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                   <label for="no" class="col-sm-5 control-label"></label>
                                   <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                                </div>
                                <input type="hidden" name="MM_insert" >
                            </form>
                        </div>   
                    <?php } 
                    #* Código Barras Plan Inventario
                    elseif($_GET['t']==7){ ?>
                        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Código Barras Plan Inventario</h2>
                        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">
                            <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="JavaScript:guardar()">
                                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Plan_Inventario_Codigo.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                                <div class="form-group" style="margin-top: -10px; ">
                                    <input type="hidden" id="action" name="action" value="14">
                                    <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                                    <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                   <label for="no" class="col-sm-5 control-label"></label>
                                   <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                                </div>
                                <input type="hidden" name="MM_insert" >
                            </form>
                        </div>   
                    <?php } 
                    #* Archivo Presupuestal Aguazul
                    elseif($_GET['t']==8){ ?>
                        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Información Presupuestal</h2>
                        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">
                            <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="JavaScript:guardar3()">
                                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Informacion_Presupuestal.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                                <div class="form-group" style="margin-top: -10px; ">
                                    <input type="hidden" id="action" name="action" value="15">
                                    <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                                    <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                   <label for="no" class="col-sm-5 control-label"></label>
                                   <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                                </div>
                                <input type="hidden" name="MM_insert" >
                            </form>
                        </div>   
                    <?php } ?>    
                </div>   
            </div>
        </div>
      <?php require_once 'footer.php'; ?>
      <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/md5.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script>
            $(".select2").select2({
                allowClear:true
            });
        </script>
        <script>
            function guardar(){
                jsShowWindowLoad('Cargando Información...');
                var formData = new FormData($("#form")[0]);  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_copiarTablasJson.php",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response+'G');
                        if(response==0){
                            $("#mensaje").html('No Se Ha Podido Cargar Información');
                            $("#mdlMensaje").modal("show");
                        } else {
                            $("#mensaje").html(response+' Registros Subidos Correctamente');
                            $("#mdlMensaje").modal("show");
                        }
                        $("#btnOk").click(function(){
                            document.location.reload();
                        })

                    }
                });
            }
        </script>
        <script>
            function guardar2(){
                jsShowWindowLoad('Comprobando Información...');
                var formData = new FormData($("#form")[0]);  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_copiarTablasJson.php",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response+'G2');
                        var resultado = JSON.parse(response);
                        var rta = resultado["rta"];
                        var html = resultado["msj"];
                        if(rta==0){
                            $("#action").val('9');
                            guardar();
                        } else {
                            $("#mensaje").html(html);
                            $("#mdlMensaje").modal("show");
                        }
                    }
                })
            }
        </script>
        <script>
            function guardar3(){
                jsShowWindowLoad('Comprobando Información...');
                var formData = new FormData($("#form")[0]);  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_copiarTablasJson.php",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response+'G3');
                        var resultado = JSON.parse(response);
                        var rta = resultado["rta"];
                        var html = resultado["msj"];
                        if(rta==0){
                            $("#action").val('16');
                            guardar();
                        } else {
                            $("#mensaje").html(html);
                            $("#mdlMensaje").modal("show");
                        }
                    }
                })
            }
        </script>
        <script>
            function validarsi(){
                jsShowWindowLoad('Comprobando Información...');
                var formData = new FormData($("#form")[0]);  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_copiarTablasJson.php",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response+'G10');
                        if(response==0){
                            validarcuentas();
                        } else {
                            $("#mensajeV").html('Existe comprobante de saldos iniciales.<br/>\n\
                                ¿Desea Eliminarlo y registrar saldos iniciales nuevamente?');
                            $("#mdlMensajeV").modal("show");
                            $("#btnOkV").click(function(){
                                $("#mdlMensajeV").modal("hide");
                                validarcuentas();
                            })
                            $("#btnOkC").click(function(){
                                $("#mdlMensajeV").modal("hide");
                            })
                        }
                    }
                })
            }
            function validarcuentas(){ 
                $("#action").val('11');
                jsShowWindowLoad('Comprobando Información...');
                var formData = new FormData($("#form")[0]);  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_copiarTablasJson.php",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response+'G11');
                        var resultado = JSON.parse(response);
                        var rta = resultado["rta"];
                        var html = resultado["msj"];
                        if(rta==0){
                            $("#action").val('12');
                            guardar();
                        } else {
                            $("#mensaje").html(html);
                            $("#mdlMensaje").modal("show");
                        }
                    }
                })
            }

            function planInventario(){
                jsShowWindowLoad('Comprobando Información...');
                var formData = new FormData($("#form")[0]);  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_copiarTablasJson.php",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        let data = response;                        
                        $("#mensaje").html("Registros Insertados: "+data);
                        $("#mdlMensaje").modal("show");
                        $("#btnOk").click(function(){
                            document.location.reload();
                        })
                    }
                })
            }
        </script>
        <div class="modal fade" id="mdlMensaje" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnOk" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>    
        <div class="modal fade" id="mdlMensajeV" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensajeV" name="mensajeV" style="font-weight: normal"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnOkV" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="btnOkC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                    </div>
                </div>
            </div>
        </div>    
    </body>
</html>

