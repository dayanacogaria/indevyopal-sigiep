

<!-- Llamado a la cabecera del formulario -->
<?php require_once 'head.php';?>
<?php 
//llamado a la clase de conexion
require_once('Conexion/conexion.php');
?>
  <title>Registrar Tipo Clase Presupuestal</title>
</head>
<body>

<!-- contenedor principal -->
<div class="container-fluid text-center">
  <div class="row content">
  
<!-- Llamado al menu del formulario -->   
  <?php require_once 'menu.php'; ?>
  
    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Tipo Clase Presupuestal</h2>
      <a href="listar_GF_TIPO_CLASE_PPTAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
      <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Tipo Clase Presupuestal</h5>
      
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoClasePptalJson.php">

            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
            </div>
                   
            <div align="center">

            <button type="submit" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: -10px; margin-bottom: 10px; margin-left: -100px;">Guardar</button>

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



