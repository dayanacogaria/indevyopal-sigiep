<?php 
	require_once('Conexion/conexion.php');
  require_once 'head.php';

?>
	  <title>Registrar Homologaciones</title>
</head>
<body>

<div class="container-fluid text-center">
  <div class="row content">
   
   <?php
      require_once 'menu.php';
   ?>
    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Homologaciones</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GN_HOMOLOGACIONESJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>


            <div class="form-group col-sm-12" style="margin-top: 0px;">

              <label for="idOrig" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Id Origen:
              </label>

              <div class="col-sm-6">
                <input type="text" name="idOrig" id="idOrig" class="form-control" maxlength="500" title="Ingrese id de origen" onkeypress="return txtValida(event, 'num_car')" placeholder="Id Origen" required>
              </div>

            </div>


            <div class="form-group col-sm-12" style="margin-top: -10px;">

              <label for="idDes" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Id Destino:
              </label>

              <div class="col-sm-6">
                <input type="text" name="idDes" id="idDes" class="form-control" maxlength="500" title="Ingrese id de destino" onkeypress="return txtValida(event, 'num_car')" placeholder="Id Destino" required>
              </div>

            </div>



            <div class="form-group col-sm-12" style="margin-top: -10px;">
              <label for="origen" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Origen:
              </label>

              <div class="col-sm-6">
                <?php 
                  $sqlTabHomOri = "SELECT id, tabla_origen  
                    FROM gn_tabla_homologable  
                    ORDER BY tabla_origen ASC";
                  $tablaHomOri = $mysqli->query($sqlTabHomOri);
                ?>
                <select name="origen" id="origen" class="form-control" title="Origen" required>
                  <option value="">Origen</option>
                  <?php 
                    while($rowTHO = mysqli_fetch_row($tablaHomOri))
                    {
                      echo '<option value="'.$rowTHO[0].'">'.ucwords(strtolower($rowTHO[1])).'</option>';
                    }
                  ?>
                </select> 
              </div>

            </div>


             <div class="form-group col-sm-12" style="margin-top: -10px;">
              <label for="destino" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Destino:
              </label>

              <div class="col-sm-6">
                <?php 
                  $sqlTabHomDes = "SELECT id, tabla_destino   
                    FROM gn_tabla_homologable  
                    ORDER BY tabla_destino ASC";
                  $tablaHomDes = $mysqli->query($sqlTabHomDes);
                ?>
                <select name="destino" id="destino" class="form-control" title="Destino" required>
                  <option value="">Informe</option>
                  <?php 
                    while($rowTHD = mysqli_fetch_row($tablaHomDes))
                    {
                      echo '<option value="'.$rowTHD[0].'">'.ucwords(strtolower($rowTHD[1])).'</option>';
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