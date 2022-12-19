<?php 
require_once('Conexion/conexion.php');
require_once ('head.php');
//Captura de ID y consulta del resgistro correspondiente.
  $id_Prod_Espe = (($_GET["id_Prod_Espe"]));

  $queryProdEspe = "SELECT pe.Id_Unico, pe.Valor, pe.FichaInventario
  FROM gf_producto_especificacion pe
  LEFT JOIN gf_ficha_inventario fi ON pe.FichaInventario = fi.Id_Unico 
  WHERE md5(pe.Id_Unico) = '$id_Prod_Espe'";

$productoEspec = $mysqli->query($queryProdEspe);
$rowPE = mysqli_fetch_row($productoEspec);

//Consulta para el listado del combo 'fichaInve' correspondiente a los datos de la tabla gf_ficha_inventario.
$queryFichaInve1 = "SELECT gf_ficha_inventario.id_unico, gf_ficha.descripcion
FROM gf_ficha_inventario 
LEFT JOIN gf_ficha ON gf_ficha_inventario.ficha = gf_ficha.id_unico
WHERE gf_ficha_inventario.id_unico = $rowPE[2]";
$fichaInven1= $mysqli->query($queryFichaInve1);
$pro = mysqli_fetch_row($fichaInven1);

//Consulta para el listado del combo 'fichaInve' correspondiente a los datos de la tabla gf_ficha_inventario.
$queryFichaInve = "SELECT gf_ficha_inventario.id_unico, gf_ficha.descripcion
FROM gf_ficha_inventario 
LEFT JOIN gf_ficha ON gf_ficha_inventario.ficha = gf_ficha.id_unico
WHERE gf_ficha_inventario.id_unico != $rowPE[2] and gf_ficha.descripcion!='$pro[1]' group by gf_ficha.id_unico";
$fichaInven = $mysqli->query($queryFichaInve);

?>
<title>Modificar Producto Especificación</title>
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Modificar Producto Especificación</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top:-5px" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GF_PRODUCTO_ESPECIFICACIONJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $rowPE[0]; ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valor" id="valor" class="form-control" maxlength="5000" title="Ingrese el valor de la especificación" onkeypress="return txtValida(event,'car')" placeholder="Valor" value="<?php echo $rowPE[1]; ?>" required>
            </div>

            <div class="form-group">
              <label for="fichaInve" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ficha Inventario:</label>
              <select name="fichaInve" id="fichaInve" class="form-control" title="Seleccione una ficha de inventario" required>
                <option value="<?php echo $pro[0] ?>"><?php echo $pro[1] ?></option>
                <?php while($rowFI = mysqli_fetch_row($fichaInven))
                { ?>
                <option value="<?php echo $rowFI[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($rowFI[1])));?> </option>
      <?php     } ?>
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


  <?php require_once 'footer.php';  ?>

</body>
</html>
