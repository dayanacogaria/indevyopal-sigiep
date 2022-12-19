<?php 
require_once('Conexion/conexion.php');	
require_once 'head.php'; 

  //Consulta para el listado del combo 'fichaInve' correspondiente a los datos de la tabla gf_ficha_inventario.
  
$queryFichaInve = "SELECT distinct gf_ficha_inventario.id_Unico,gf_ficha.descripcion
FROM gf_ficha_inventario LEFT JOIN gf_ficha ON gf_ficha_inventario.ficha = gf_ficha.id_unico GROUP BY gf_ficha.id_unico";
$resultado = $mysqli->query($queryFichaInve);
$queryFichaInve1 = "SELECT distinct id_unico,descripcion
FROM gf_producto";
$resultado1 = $mysqli->query($queryFichaInve1);
?>
<title>Registrar Producto Especificación</title>
</head>
<body>

</div>

<div class="container-fluid text-center">
  <div class="row content">
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:-2px">Registrar Producto Especificación</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_PRODUCTO_ESPECIFICACIONJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <div class="form-group" style="margin-top: -10px;">
              <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valor" id="valor" class="form-control" maxlength="5000" title="Ingrese el valor de la especificación" onkeypress="return txtValida(event,'car')" placeholder="Valor" required>
            </div>

            <div class="form-group">
              <label for="fichaInve" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ficha Inventario:</label>
              <select name="fichaInve" id="fichaInve" class="form-control" title="Seleccione una ficha de inventario" required>
                <option value="">Ficha Inventario</option>
                <?php while($row = mysqli_fetch_row($resultado))
                { ?>
                <option value="<?php echo $row[0]; ?>"> <?php echo ucwords(utf8_encode(strtolower($row[1]))); ?> </option>
                <?php
                 }
                 ?>
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