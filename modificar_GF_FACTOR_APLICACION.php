<?php 
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes
require_once ('head.php');
require_once('Conexion/conexion.php');
//Captura de ID y consulta del resgistro correspondiente.
$id_fact_aplic = " ";
if (isset($_GET["id_fact_aplic"])){ 
  $id_fact_aplic = (($_GET["id_fact_aplic"]));

  $queryFacApli = "SELECT Id_Unico, Nombre 
    FROM gf_factor_aplicacion
    WHERE md5(Id_Unico) = '$id_fact_aplic'";
}

$resultado = $mysqli->query($queryFacApli);
$row = mysqli_fetch_row($resultado);
?>
<title>Modificar Factor Aplicación</title>
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Factor Aplicación</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GF_FACTOR_APLICACIONJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower($row[1]));?>" required>
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


  <?php require_once 'footer.php';  ?>

</body>
</html>
