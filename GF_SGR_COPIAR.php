<?php 
  require_once('Conexion/conexion.php');
  require_once('Conexion/ConexionPDO.php');
    require_once('./jsonPptal/funcionesPptal.php');
  require_once './head.php';
  $con = new ConexionPDO();
  $compania = $_SESSION['compania'];
  $panno    = $_SESSION['anno'];
  $anno     = anno($panno);
?>
<html>
    <head>
        <title>Sistema General de Regalias</title>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <script src="js/md5.pack.js"></script>
        <style>

            label #fuente-error,#compania-error , #tabla1-error,#compania1-error, #anno-error {
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
         $().ready(function() {
            var validator = $("#form2").validate({
                ignore: "",
                errorPlacement: function(error, element) {
                    $( element )
                        .closest( "form2" )
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
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Sistema General De Regalías</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:guardar()" >  
                            <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <br/>
                            <div class="form-group form-inline" style="margin-top: -5px; margin-left: 0px">
                                <div class="form-group form-inline  col-md-2 col-lg-2" text-aling="left">
                                    <label for="fuente" class="col-sm-12 control-label"><strong class="obligado">*</strong>Fuente:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="fuente" id="fuente" class="form-control select2" title="Seleccione Fuente" style="height: auto;" required="required" >
                                        <?php 
                                        echo '<option value="">Fuente</option>';
                                        $rowf = $con->Listar("SELECT * FROM gf_fuente WHERE parametrizacionanno = $panno");
                                        for ($i = 0; $i < count($rowf); $i++) {
                                            echo '<option value="'.$rowf[$i][0].'">'.ucwords(mb_strtolower($rowf[$i][1])).'</option>';                                            
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" text-aling="left">
                                    <label for="anno" class="col-sm-12 control-label"><strong class="obligado">*</strong>Año:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="anno" id="anno" class="form-control select2" title="Seleccione Año" style="height: auto;" required="required">
                                        <option value="">Año</option>
                                        <?php 
                                        $row = $con->Listar("SELECT  DISTINCT id_unico, anno 
                                            FROM gf_parametrizacion_anno 
                                            WHERE id_unico != $panno AND compania = $compania AND anno>($anno)");

                                        for ($i = 0; $i < count($row); $i++) {
                                            echo '<option value="'.$row[$i][0].'">'.$row[$i][1].'</option>';
                                        }    
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:20px">
                                    <button type="submit"  class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Copiar">
                                        <li class="glyphicon glyphicon-duplicate"></li>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>    
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
                //alert('sss');
                jsShowWindowLoad('Verificando Información...');
                var formData = new FormData($("#form")[0]);  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_copiarTablasJson.php?action=6",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response+' - G');
                        var resultado = JSON.parse(response);
                        var rta = resultado["rta"];
                        var html = resultado["html"];
                        var f = resultado["f"];
                        if(rta>0){
                            $("#mensaje1").html(html+'<br/> ¿Desea Copiar La Información?');
                            $("#mdlMensajes1").modal("show");
                            if(f==1){
                                $("#btnAceptar").click(function(){
                                    document.location.reload();
                                })
                            } else {
                                $("#btnAceptar").click(function(){
                                    registrar();
                                })
                            }
                        } else {
                            registrar();
                        }
                    }
                });
            }
            $("#btnCancelar").click(function(){
                document.location.reload();
            })
            function registrar(){
                jsShowWindowLoad('Copiando Información...');
                var formData = new FormData($("#form")[0]);  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_copiarTablasJson.php?action=7",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response+' - C');
                        
                        if(response > 0){
                            $("#mensaje").html(response+' Registros Guardados Correctamente');
                            $("#mdlMensaje").modal("show");
                            $("#btnOk").click(function(){
                                document.location.reload();
                            })
                        } else {
                            $("#mensaje").html('No se pudo guardar información');
                            $("#mdlMensajes").modal("show");
                            $("#btnOk").click(function(){
                                document.location.reload();
                            })
                            
                        }
                    }
                });
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
        <div class="modal fade" id="mdlMensajes1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje1" name="mensaje1" style="font-weight: normal"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                    </div>
                </div>
            </div>
        </div>   
    </body>
</html>

