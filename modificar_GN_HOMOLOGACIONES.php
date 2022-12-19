<?php require_once('Conexion/conexion.php');

$id_homologaciones = " ";
if (isset($_GET["id_homologaciones"]))
{ 
  $id_homologaciones = (($_GET["id_homologaciones"]));

  $queryTabHom = "SELECT hom.id, hom.id_origen, hom.id_destino, hom.origen, tabHom.tabla_origen, hom.destino, tabHom.tabla_destino 
  FROM gn_homologaciones hom 
  LEFT JOIN gn_tabla_homologable tabHom ON tabHom.id = hom.origen
  LEFT JOIN gn_tabla_homologable tablHom ON tablHom.id = hom.destino
  WHERE md5(hom.id) = '$id_homologaciones'";
}

$resultado = $mysqli->query($queryTabHom);
$row = mysqli_fetch_row($resultado);

require_once 'head.php';

?>

<title>Modificar Tabla Homologable</title>  
</head>
<body>

<div class="container-fluid text-center">
  <div class="row content">
    
  <?php
    require_once 'menu.php';
  ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tabla Homologable</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GN_HOMOLOGACIONESJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <input type="hidden" name="id" value="<?php echo $row[0]; ?>">

             <div class="form-group col-sm-12" style="margin-top: 0px;">

              <label for="idOrig" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Id Origen:
              </label>

              <div class="col-sm-6">
                <input type="text" name="idOrig" id="idOrig" class="form-control" maxlength="500" title="Ingrese id de origen" onkeypress="return txtValida(event, 'num_car')" placeholder="Id Origen" value="<?php echo ucwords(strtolower($row[1]));?>" required>
              </div>

            </div>


            <div class="form-group col-sm-12" style="margin-top: -10px;">

              <label for="idDes" class="col-sm-6 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Id Destino:
              </label>

              <div class="col-sm-6">
                <input type="text" name="idDes" id="idDes" class="form-control" maxlength="500" title="Ingrese id de destino" onkeypress="return txtValida(event, 'num_car')" placeholder="Id Destino" value="<?php echo ucwords(strtolower($row[2]));?>" required>
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
                    WHERE id != '$row[3]'  
                    ORDER BY tabla_origen ASC";
                  $tablaHomOri = $mysqli->query($sqlTabHomOri);
                ?>
                <select name="origen" id="origen" class="form-control" title="Origen" required>
                  <option value="<?php echo $row[3];?>"><?php echo ucwords(strtolower($row[4])); ?></option>
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
                Informe:
              </label>

              <div class="col-sm-6">
                <?php 
                  $sqlTabHomDes = "SELECT id, tabla_destino 
                    FROM gn_tabla_homologable 
                    WHERE id != '$row[5]'   
                    ORDER BY tabla_destino ASC";
                  $tablaHomDes = $mysqli->query($sqlTabHomDes);
                ?>
                <select name="destino" id="destino" class="form-control" title="Destino" required>
                  <option value="<?php echo $row[5];?>"><?php echo ucwords(strtolower($row[6]));?></option>
                  <?php 
                    while($rowTHD = mysqli_fetch_row($tablaHomDes))
                    {
                      echo '<option value="'.$rowTHD[0].'">'.ucwords(strtolower($rowTHD[1])).'</option>';
                    }
                  ?>
                </select> 
              </div>

            </div>


             <div class="form-group col-sm-12" style="margin-top: 10px;">

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
