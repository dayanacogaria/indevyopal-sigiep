<?php 
//Llamado a la cabecera del formulario
require_once 'head.php';
//llamado a la clase de conexion
require_once('Conexion/conexion.php');
?>


  <title>Registrar Destino</title>
</head>
<body>

<!-- contenedor principal -->
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menu del formulario -->   
  <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Destino</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDestinoJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
            </div>
         
<div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>

            <input type="hidden" name="MM_insert" >
          </form>
<!-- Fin de división y contenedor del formulario -->           
        </div>
    </div>
  </div>
   <!-- Fin del Contenedor principal -->  
</div>
<!-- Llamado al pie de pagina -->
  <?php require_once 'footer.php'; ?>

</body>
</html>



