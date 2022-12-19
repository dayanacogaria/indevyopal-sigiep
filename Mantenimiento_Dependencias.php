<?php 

require_once('Conexion/conexion.php');
require_once('head_listar.php');
?>
<!--Titulo de la página-->
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<title>Actualización Dependencias</title>
</head>
<body onload="mantenimiento()">
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">
    <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Actualización Dependencias</h2>
    
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
          <p><label id="msj"> </label></p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnRListo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  </div>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
 
  <script>
  function mantenimiento(){
        jsShowWindowLoad('Actualizando Dependencias. <br>Espere por favor');
        var form_data = {                     
                        id:1
                    };
        $.ajax({
            type:"POST",
            url:"consultasBasicas/mantenimiento_Dependencias.php",
            data:form_data,
            success: function (data) {
                    console.log(data);
                    var result = JSON.parse(data);
                    document.getElementById('msj').innerHTML = result;
                    jsRemoveWindowLoad();
                    $("#modalListo").modal('show');
                    $('#btnRListo').click(function(){
                        $('#modalListo').modal('hide');
                        document.location = "LISTAR_GF_DEPENDENCIA.php";
                    });
                
            }
        });
  }
</script>
<?php require_once 'footer.php';?>
</body>
</html>

