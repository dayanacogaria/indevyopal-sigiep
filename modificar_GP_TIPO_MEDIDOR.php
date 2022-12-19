<?php 
###################################################################################
#   **********************      Modificaciones      ******************************#
###################################################################################
#21/08/2018 |Erica G. | Modificación Código
###################################################################################
require_once('Conexion/conexion.php');
require_once './head.php';
$id = $_GET["id"];
$queryCond = "SELECT id_unico, nombre FROM gp_tipo_medidor
    WHERE md5(id_unico) = '$id'"; 
$resul = $mysqli->query($queryCond);
$row = mysqli_fetch_row($resul);
?>
<title>Modificar Tipo Medidor</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tipo Medidor</h2>
                <a href="listar_GP_TIPO_MEDIDOR.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(mb_strtolower($row[1]));?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:modificar()">
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
                            <input type="text" name="nombre" value="<?php echo ucwords((mb_strtoupper($row[1]))) ?>" id="nombre" class="form-control" maxlength="500" title="Ingrese el nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre" required>
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
    <script>
        function modificar(){
            var formData = new FormData($("#form")[0]);
            jsShowWindowLoad('Modificando..');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_TipoMedidorJson.php?action=3",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    jsRemoveWindowLoad();
                    console.log(data);
                    if (data ==true) {
                        $("#mensaje").html("Información Modificada Correctamente");
                        $("#mdlMensajes").modal("show");
                        $("#btnAceptar").click(function(){
                            document.location ='listar_GP_TIPO_MEDIDOR.php';
                        })
                    } else {
                        $("#mensaje").html("No Se Ha Podido Modificar La Información");
                        $("#mdlMensajes").modal("show");
                        $("#btnAceptar").click(function(){
                            $("#mdlMensajes").modal("hide");
                        })
                    }
                }
            })
        }
    </script>
    <div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    </body>
</html>
