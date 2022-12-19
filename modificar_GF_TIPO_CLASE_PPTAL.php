<?php 
#################MODIFICACIONES###########################
#04/05/2017 | Erica G. | Diseño, tíldes, búsquedas
##########################################################
require_once 'head.php';  
  require_once('Conexion/conexion.php');
  $id = " ";
 if (isset($_GET["id"])){ 
    $id_tipo= (($_GET["id"]));
 $query = "SELECT id_unico, nombre FROM gf_tipo_clase_pptal  WHERE md5(Id_Unico) ='$id_tipo'";

}

/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
$resultado = $mysqli->query($query);
$row = mysqli_fetch_row($resultado);

?>
  <title>Modificar Tipo Clase Presupuestal</title>
</head>

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">
<!-- Llamado al menú del formulario -->   
    <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tipo Clase Presupuestal</h2>
      <a href="listar_GF_TIPO_CLASE_PPTAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
      <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Tipo Clase Presupuestal: <?php echo ucwords(mb_strtolower($row[1]))?></h5>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario -->
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoClasePptalJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo $row[1] ?>" required>
            </div>
          
            <div align="center">

            <div align="center">

            <button type="submit" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: -10px; margin-bottom: 10px; margin-left: -100px;">Guardar</button>

            </div>
            <input type="hidden" name="MM_insert" >
          </form>
<!-- Fin de división y contenedor del formulario -->          
        </div>     
    </div>
  </div>
<!-- Fin del Contenedor principal -->   
</div>

<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'; ?>
  </div>

</body>
</html>
