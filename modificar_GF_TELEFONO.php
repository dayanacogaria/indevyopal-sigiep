<?php 
require_once('Conexion/conexion.php');
session_start();

//Captura de ID y consulta del resgistro correspondiente.
$id_telef = " ";
if (isset($_GET["id_telef"]))
{ 
  $id_telef = (($_GET["id_telef"]));

  $queryTelef = "SELECT t.id_unico, t.tipo_telefono, tt.nombre, t.valor
        FROM gf_telefono t
        LEFT JOIN gf_tipo_telefono tt ON t.tipo_telefono = tt.id_unico 
        WHERE md5(t.id_unico) = '$id_telef'";
}

$resultado = $mysqli->query($queryTelef);
$row = mysqli_fetch_row($resultado);



$telefono ="SELECT id_unico, nombre FROM gf_tipo_telefono  WHERE id_unico !='$row[1]' ORDER BY nombre ASC";
$tel = $mysqli->query($telefono);


require_once ('head.php');
?>
<title>Modificar Teléfono</title>
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Teléfono</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GF_TELEFONOJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0]; ?>">

            <div class="form-group" style="margin-top: -10px;" >
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Tel&eacutefono:</label>
                <select name="nombre" id="nombre" class="form-control" title="Seleccione el tipo teléfono" required style="width:230px">
                  <option value="<?php echo $row[0]?>"><?php echo $row[2]?></option>
                  <?php while($rowT = mysqli_fetch_assoc($tel)){?>
                  <option value="<?php echo $rowT['id_unico'] ?>"><?php echo ucwords(utf8_encode(strtolower($rowT['nombre'])));}?></option>
                </select> 
            </div>


             <div class="form-group" style="margin-top: -20px;">
              <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valor" id="valor" class="form-control" maxlength="20" title="Ingrese el valor" onkeypress="return txtValida(event,'num')" placeholder="valor" value="<?php echo $row[3];?>" required>
            </div>
            
            <div align="center">

            <button type="submit" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: -10px; margin-bottom: 10px; margin-left: -100px;">Guardar</button>

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
