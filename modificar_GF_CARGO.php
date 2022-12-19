<?php 
############MODIFICACIONES################
#30/03/2017 ERICA G. TÍLDES
##########################################
require_once 'head.php';
require_once('Conexion/conexion.php');
//Captura de ID y consulta del resgistro correspondiente.
$id_cargo = " ";
if (isset($_GET["id_cargo"])){ 
  $id_cargo = (($_GET["id_cargo"]));

  $queryCargo = "SELECT Id_Unico, Nombre, Numero_Plazas,codigo 
    FROM gf_cargo
    WHERE md5(Id_Unico) = '$id_cargo'"; 

}

$resultado = $mysqli->query($queryCargo);
$row = mysqli_fetch_row($resultado);

?>

  <title>Modificar Cargo</title>
</head>
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
  <?php require_once 'menu.php'; ?>
  
    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Cargo</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GF_CARGOJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">

            <div class="form-group" style="margin-top: -10px;">
              <label for="cod" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código del Cargo:</label>
              <input type="text" name="cod" id="cod" class="form-control" maxlength="100" title="Ingrese el código" onkeypress="return txtValida(event, 'num_car')" placeholder="Código" value="<?php echo ucwords(mb_strtolower($row[3]));?>" required>
            </div>

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'num_car')" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower($row[1]));?>" required>
            </div>

             <div class="form-group" style="margin-top: -10px;">
              <label for="noPlazas" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Plazas:</label>
                <input type="text" name="noPlazas" id="noPlazas" class="form-control" maxlength="150" title="Ingrese el número de plazas" onkeypress="return txtValida(event, 'num')" placeholder="Número Plazas" value="<?php echo $row[2] ?>" required>
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
