<?php require_once('Conexion/conexion.php');

//Captura de ID y consulta del resgistro correspondiente.
$id_periodicidad = " ";
if (isset($_GET["id_periodicidad"])){ 
  $id_periodicidad = (($_GET["id_periodicidad"]));

  $queryPer = "SELECT id, nombre  
    FROM gn_periodicidad
    WHERE md5(id) = '$id_periodicidad'"; 

}

$resultado = $mysqli->query($queryPer);
$row = mysqli_fetch_row($resultado);

  require_once 'head.php';

?>

  <title>Modificar Periodicidad</title>
</head>
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
  <?php require_once 'menu.php'; ?>
  
    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Periodicidad</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GN_PERIODICIDADJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">

            <div class="form-group col-sm-12" style="margin-top: 0px;">
              <label for="nombre" class="col-sm-6 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>

              <div class="col-sm-6">
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" value="<?php echo ucwords(strtolower($row[1]));?>" required>
              </div>
            </div>
            
  
            <div class="form-group col-sm-12" style="margin-top: 10px;">
              <div class="col-sm-6">
              </div>

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
<?php require_once 'footer.php'; ?>

 
</body>
</html>
