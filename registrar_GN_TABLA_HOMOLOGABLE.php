<?php 
	require_once('Conexion/conexion.php');
  require_once 'head.php';

?>
	  <title>Registrar Tabla Homologable</title>
</head>
<body>

<div class="container-fluid text-center">
  <div class="row content">
   
   <?php
      require_once 'menu.php';
   ?>
    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Tabla Homologable</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GN_TABLA_HOMOLOGABLEJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>


            <div class="form-group col-sm-12" style="margin-top: 0px;">

              <label for="tabOrig" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Tabla Origen:
              </label>

              <div class="col-sm-6">
                <input type="text" name="tabOrig" id="tabOrig" class="form-control" maxlength="500" title="Ingrese la tabla origen" onkeypress="return txtValida(event, 'num_car')" placeholder="Tabla Origen" required>
              </div>

            </div>


            <div class="form-group col-sm-12" style="margin-top: -10px;">

              <label for="colOrg" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Columna Origen:
              </label>

              <div class="col-sm-6">
                <input type="text" name="colOrg" id="colOrg" class="form-control" maxlength="500" title="Ingrese columna origen" onkeypress="return txtValida(event, 'num_car')" placeholder="Columna Origen" required>
              </div>

            </div>


             <div class="form-group col-sm-12" style="margin-top: -10px;">

              <label for="tabDes" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Tabla Destino:
              </label>

              <div class="col-sm-6">
                <input type="text" name="tabDes" id="tabDes" class="form-control" maxlength="500" title="Ingrese tabla destino" onkeypress="return txtValida(event, 'num_car')" placeholder="Tabla Destino" required>
              </div>

            </div>


            <div class="form-group col-sm-12" style="margin-top: -10px;">

              <label for="colDes" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Columna Destino:
              </label>

              <div class="col-sm-6">
                <input type="text" name="colDes" id="colDes" class="form-control" maxlength="500" title="Ingrese columna destino" onkeypress="return txtValida(event, 'num_car')" placeholder="Columna Destino" required>
              </div>

            </div>



            <div class="form-group col-sm-12" style="margin-top: -10px;">
              <label for="tipoHom" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Tipo Homologable:
              </label>

              <div class="col-sm-6">
                <?php 
                  $sqlTipHom = "SELECT id, nombre 
                    FROM gn_tipo_homologable  
                    ORDER BY nombre ASC";
                  $tipoHom = $mysqli->query($sqlTipHom);
                ?>
                <select name="tipoHom" id="tipoHom" class="form-control" title="Tipo Homologable" required>
                  <option value="">Tipo Homologable</option>
                  <?php 
                    while($rowTH = mysqli_fetch_row($tipoHom))
                    {
                      echo '<option value="'.$rowTH[0].'">'.ucwords(strtolower($rowTH[1])).'</option>';
                    }
                  ?>
                </select> 
              </div>

            </div>


             <div class="form-group col-sm-12" style="margin-top: -10px;">
              <label for="informe" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Informe:
              </label>

              <div class="col-sm-6">
                <?php 
                  $sqlInforme = "SELECT id, nombre 
                    FROM gn_informe  
                    ORDER BY nombre ASC";
                  $informe = $mysqli->query($sqlInforme);
                ?>
                <select name="informe" id="informe" class="form-control" title="Informe" required>
                  <option value="">Informe</option>
                  <?php 
                    while($rowI = mysqli_fetch_row($informe))
                    {
                      echo '<option value="'.$rowI[0].'">'.ucwords(strtolower($rowI[1])).'</option>';
                    }
                  ?>
                </select> 
              </div>

            </div>


            <div class="form-group col-sm-12" style="margin-top: -10px;">
              <label for="periodicidad" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Periodicidad:
              </label>

              <div class="col-sm-6">
                <?php 
                  $sqlPeriodicidad = "SELECT id, nombre 
                    FROM gn_periodicidad   
                    ORDER BY nombre ASC";
                  $periodicidad = $mysqli->query($sqlPeriodicidad);
                ?>
                <select name="periodicidad" id="periodicidad" class="form-control" title="Informe" required>
                  <option value="">Periodicidad</option>
                  <?php 
                    while($rowP = mysqli_fetch_row($periodicidad))
                    {
                      echo '<option value="'.$rowP[0].'">'.ucwords(strtolower($rowP[1])).'</option>';
                    }
                  ?>
                </select> 
              </div>

            </div>
          
            
            
            <div class="form-group col-sm-12">

              <div class="col-sm-6"></div>
                
              <div class="col-sm-6">
                <button type="submit" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-bottom: 10px; margin-left: 0px;">
                  Guardar
                </button>
              </div>

            </div>

            <input type="hidden" name="MM_insert" >
          </form>
        </div>

    </div>

  </div>
</div>

<?php 
  require_once 'footer.php';
?>

</body>
</html>