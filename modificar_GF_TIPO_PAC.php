<?php 
##################MODIFICACIONES#########################
#04/05/2017 | Erica G. | Diseño, tíldes, búsquedas

require_once 'head.php'; ?>
<?php
//llamado a la clase de conexion 
require_once('Conexion/conexion.php');
//declaracion que recibe la variable que recibe el ID
$id = " ";
//validacion preguntando si la variable enviada del listar viene vacia
if (isset($_GET["id"]))
{ 
  $id_tipoP = (($_GET["id"]));
//Query o sql de consulta
  $queryTipo = "SELECT tp.id_unico, tp.nombre, cp.id_unico, cp.nombre
        FROM gf_tipo_pac tp 
        LEFT JOIN gf_clase_pptal cp ON tp.clasepptal = cp.Id_Unico
        WHERE md5(tp.Id_Unico) = '$id_tipoP'";
}

/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
$resultado = $mysqli->query($queryTipo);
$row = mysqli_fetch_row($resultado);

//consulta para llenar los campos
$clase = "SELECT id_unico, nombre FROM gf_clase_pptal WHERE id_unico != '$row[2]'  ORDER BY nombre ASC";
$claseP =   $mysqli->query($clase);
?>
<title>Modificar Tipo PAC</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menú del formulario -->  
    <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tipo PAC</h2>
      <a href="listar_GF_TIPO_PAC.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
      <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Tipo PAC: <?php echo ucwords(mb_strtolower($row[1]))?></h5>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario -->
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoPacJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo $row[1] ?>" required>
            </div>

            <div class="form-group">
              <label for="claseP" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Presupuestal:</label>
              <select name="claseP" id="claseP" class="form-control select2_single" title="Seleccione la clase presupuestal" required>
                  <option value="<?php echo $row[2]?>"><?php echo $row[3]?></option>
                <?php while($rowC = mysqli_fetch_assoc($claseP)){?>
                <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords( (mb_strtolower($rowC['nombre'])));}?></option>;
              </select> 
            </div>
                     
            
            <div align="center">
            <button type="submit" class="btn btn-primary sombra" style="margin-left: -100px">Guardar</button>
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

<script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<!-- select2 -->
 

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
</body>
</html>
