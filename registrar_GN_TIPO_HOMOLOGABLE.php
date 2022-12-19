<?php require_once('Conexion/conexion.php');

require_once 'head.php';
?>

  <title>Registrar Tipo Homologable</title>
</head>
<body>

<div class="container-fluid text-center">
  <div class="row content">
  <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Tipo Homologable</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GN_TIPO_HOMOLOGABLEJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

         

            <div class="form-group col-sm-12" style="margin-top: 0px;">
                <label for="nombre" class="col-sm-6 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>

              <div class="col-sm-6">
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car');" placeholder="Nombre" required>
              </div>
                
            </div>
           
            
            <div class="form-group col-sm-12" style="margin-top: 10px;">
              <div class="col-sm-6"></div>
              <div class="col-sm-6" align="left">
                <button type="submit" class="btn btn-primary sombra" style=" margin-bottom: 10px; margin-left: 0px;">Guardar</button>
              </div>
                
            </div>



            <input type="hidden" name="MM_insert" >
          </form>
        </div>

    </div>

  </div>
</div>

  <?php require_once 'footer.php'; ?>

</body>
</html>

