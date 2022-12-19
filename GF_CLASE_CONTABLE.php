<?php 
	require_once('Conexion/conexion.php');

	//session_start();

	$claseC = "SELECT Id_Unico, Nombre FROM gf_clase_contable ORDER BY Nombre ASC";
	$clase = $mysqli->query($claseC);

?>
<?php require_once 'head.php'; ?>
<title>Registrar Clase Contable</title>
</head>
<body>

</div>

<div class="container-fluid text-center">
  <div class="row content">
    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Clase Contable</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarClaseContableJson.php">

            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
            </div>

            <div class="form-group">
              <label for="clase" class="col-sm-5 control-label">Clase Asociada:</label>
              <select name="clase" id="clase" class="form-control" title="Seleccione clase asociada" >
                <option value=""></option>
                <?php while($row = mysqli_fetch_assoc($clase)){?>
                <option value="<?php echo $row['Id_Unico'] ?>"><?php echo ucwords(mb_strtolower($row['Nombre']));}?></option>;
              </select> 
            </div>
          
            
<div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
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