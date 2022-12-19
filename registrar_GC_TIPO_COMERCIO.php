<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
?>

  <title>Registrar Tipo Comercio</title>
</head>
<body>

<div class="container-fluid text-center">
  <div class="row content">
                <?php require_once ('menu.php'); ?>
  
    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;">Tipo Comercio</h2>
      <!--volver-->
      <a href="listar_GC_TIPO_COMERCIO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>


      <h5 id="forma-titulo3a" align="center" style="width:96.5%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;color:#0e315a;">.</h5> 
      <!---->

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarTipoComercioJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre"  class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
            </div>
         
            
            <div class="form-group" style="margin-top: 10px;">
             <label for="no" class="col-sm-5 control-label"></label>
             <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>


          </form>
        </div>

    </div>

  </div>
</div>

  <?php require_once 'footer.php'; ?>
<style>
    label #nombre-error{
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;

    }
</style>
</body>
</html>

