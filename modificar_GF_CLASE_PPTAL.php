<?php
##################MODIFICACIONES#########################
#10/04/2017 | Erica G. | Diseño, tíldes, búsquedas 
######################################################## 
?>
<!-- Llamado a la cabecera del formulario -->
<?php require_once 'head.php'; ?>
<?php
//llamado a la clase de conexion 
require_once('Conexion/conexion.php');
//declaracion que recibe la variable que recibe el ID
$id = " ";
//validacion preguntando si la variable enviada del listar viene vacia
if (isset($_GET["id"]))
{ 
  $id_claseP = (($_GET["id"]));
//Query o sql de consulta
  $queryClaseP = "SELECT cp.id_unico, cp.nombre, cp.tipoclase, tcp.nombre, cp.claseafectar, c.id_unico, c.nombre
        FROM gf_clase_pptal cp 
        LEFT JOIN gf_tipo_clase_pptal tcp ON cp.tipoclase = tcp.id_unico
        LEFT JOIN gf_clase_pptal c ON cp.claseafectar = c.Id_Unico
        WHERE md5(cp.Id_Unico) = '$id_claseP'";
}

/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
$resultado = $mysqli->query($queryClaseP);
$row = mysqli_fetch_row($resultado);

//consulta para llenar los campos
$tipo = "SELECT id_unico, nombre FROM gf_tipo_clase_pptal WHERE id_unico != '$row[2]' ORDER BY nombre ASC";
$tipoC =   $mysqli->query($tipo);

$claseP = "SELECT Id_Unico, Nombre FROM gf_clase_pptal WHERE Id_Unico != '$row[0]' AND id_unico !='$row[5]' ORDER BY Nombre ASC";
$clase =   $mysqli->query($claseP);



?>
<title>Modificar Clase Presupuestal</title>
<link href="css/select/select2.min.css" rel="stylesheet">
</head>

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menú del formulario -->  
    <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-right: 4px; margin-left: 4px;">Modificar Clase Presupuestal</h2>
      <a href="listar_GF_CLASE_PPTAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
      <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Clase: <?php echo ucwords(mb_strtolower($row[1]))?></h5>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario -->
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarClasePptalJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
              <input type="text" name="nombre" id="nombre" class=" form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower($row[1])) ?>" required>
            </div>

            <div class="form-group" style="margin-top: -10px;">
              <label for="tipoC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Clase:</label>
              <select name="tipoC" id="tipoC" class="select2_single form-control" title="Seleccione tipo clase presupuestal">
                <option value="<?php echo $row[2]?>"><?php echo $row[3]?></option>
                <?php while($rowC = mysqli_fetch_assoc($tipoC)){?>
                <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowC['nombre'])));}?></option>
              </select> 
            </div>

            <div class="form-group">
              <label for="claseA" class="col-sm-5 control-label">Clase Afectada:</label>
              <select name="claseA" id="claseA" class="select2_single form-control" title="Seleccione clase afectada" >
                <?php if (empty($row[6])) { ?> 
                  <option value="">-</option>
                <?php } else { ?>  
                  <option value="<?php echo $row[5]?>"><?php echo ucwords(mb_strtolower($row[6]));?></option>
                <?php } ?>  
                
                <?php while($rowA = mysqli_fetch_assoc( $clase)){?>
                
                    <option value="<?php echo $rowA['Id_Unico'] ?>"><?php echo ucwords((mb_strtolower($rowA['Nombre'])));?></option>
                <?php } ?>
                
              </select> 
            </div>
                      
            <div class="form-group" style="margin-top: 15px;">
                <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>

            <input type="hidden" name="MM_insert" >
          </form>
<!-- Fin de división y contenedor del formulario -->          
        </div>      
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
