<?php 
//llamado a la clase de conexion
require_once('Conexion/conexion.php');
session_start();
//declaracion que recibe la variable que recibe el ID
//validacion preguntando si la variable enviada del listar viene vacia
  $id_TipoServ = (($_GET["id"]));
//Query o sql de consulta 
  $queryTipoServ = "SELECT       TM.id_unico, 
                                 TM.nombre, 
                                 TM.tipo_medidor,
                                 TMD.id_unico,
                                 TMD.nombre
                                 FROM gp_tipo_medicion TM
                                 LEFT JOIN gp_tipo_medidor TMD ON TM.tipo_medidor = TMD.id_unico
                                 WHERE md5(TM.id_unico) = '$id_TipoServ'";

            $tmid = $row[0];
            $tmnom = $row[1];
            $tmtmd = $row[2];
            $tmdid = $row[3];
            $tmdnom = $row[4];

/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
$resultado = $mysqli->query($queryTipoServ);
$row = mysqli_fetch_row($resultado);

            $tmid = $row[0];
            $tmnom = $row[1];
            $tmtmd = $row[2];
            $tmdid = $row[3];
            $tmdnom = $row[4];

//consultas para llenar los campos



?>

<!-- Llamado a la cabecera del formulario -->
<?php   require_once 'head.php'; ?>
<title>Modificar Tipo Medición</title>
</head>

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">
<!-- Llamado al menú del formulario -->  
    <?php require_once 'menu.php'; ?>
 
    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tipo Medición</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoMedicionJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">
              
<!----------- Campo para captura Nombre ---------------->
            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo $tmnom ?>" required>
            </div>
<!----------- Fin Campo para captura Nombre ---------------->
              
<!----------- Consulta para llenar Tipo Medición ---------------->
             
                        <div class="form-group">
              <?php 
                $sql = "SELECT id_unico,nombre FROM gp_tipo_medidor WHERE id_unico != $tmtmd ORDER BY nombre ASC ";
                $rs = $mysqli->query($sql);
             ?>
            <label for="TipoMedidor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Medidor:</label>
            <select name="sltMedidor" id="sltMedidor" class="form-control" title=
                    "Seleccione Tipo Medidor" style="height: 30px">
            <option value="<?php echo $row[3] ?>"> <?php echo $row[4]?> </option>
            <?php 
                while ($fila = mysqli_fetch_row($rs)) 
                { 
            ?>
            <option value="<?php echo $fila[0];?>"><?php echo $fila[1];?></option>                                
            <?php 
            }
             ?>                                    
            </select>
              </div>
<!----------- Consulta para llenar Tipo Medición ---------------->
            
              <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
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
