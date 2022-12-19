<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
?>
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<title>Generar Placas</title>
</head>
<body onload="subir()">
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <!--Titulo del formulario-->
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Generar Placas</h2>

            </div>
        </div>
    </div>
    <div class="modal fade" id="modalListo" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p><label id="msj" align="left" style="font-weight: normal;"> </label></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnRListo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMensaje" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p><label id="msj1">Al Generar Placas, se eliminaran las placas existentes 
                            y se generan nuevas a partir del número 1</label></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAcp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCan" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>

<script>
  function subir() {
     $("#modalMensaje").modal('show');
 }
 </script>
 <script>
     $("#btnAcp").click(function(){
        jsShowWindowLoad('Generando Placas. <br>Espere por favor');
        <?php if($_GET['id']==1) { ?>
         var form_data = {action: 1}; 
        <?php }  else  { ?>
         var form_data = {action: 2}; 
        <?php } ?>    
        $.ajax({
            type: "POST",
            url: "consultasBasicas/generar_placas.php",
            data: form_data,
            success: function (data) {
                console.log(data);
                document.getElementById('msj').innerHTML = data;
                jsRemoveWindowLoad();
                $("#modalListo").modal('show');
                $('#btnRListo').click(function () {
                    $('#modalListo').modal('hide');
                    document.location = "RF_ENTRADA_ALMACEN.php";
                });

            }
        });
     })
     $("#btnCan").click(function(){
         $("#modalMensaje").modal('hide');
     })
</script>
<?php require_once 'footer.php'; ?>
</body>
</html>

