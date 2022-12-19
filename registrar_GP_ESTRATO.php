<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
?>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script> 
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="js/md5.pack.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
   <title>Registrar Estrato</title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Estrato</h2>
                    <a href="listar_GP_ESTRATO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Estrato</h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="codigo" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código:</label>
                               <input type="text" name="codigo" id="codigo" class="form-control" maxlength="100" title="Ingrese el Código" onkeypress="return txtValida(event,'num_car')" placeholder="Código" required>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
                               <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                              <label for="no" class="col-sm-5 control-label"></label>
                              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                            </div>

                        </form>
                    </div>
                </div>                  
            </div>
        </div>
        <?php require_once './footer.php'; ?>
        <script src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <div class="modal fade" id="modalMensaje" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnMsj" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                            Aceptar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script>
    function guardar(){
            var formData = new FormData($("#form")[0]);  
            jsShowWindowLoad('Guardando Información...');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_estratoJson.php?action=2",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'G');
                    if(response ==true){
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                            document.location='listar_GP_ESTRATO.php';
                        })
                    } else {
                        $("#mensaje").html('No se ha podido guardar información');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                        })
                    }
                }
            })
        }
</script>