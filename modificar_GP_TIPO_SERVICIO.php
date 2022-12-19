<?php 
//llamado a la clase de conexion
require_once('Conexion/conexion.php');
require_once 'head.php';

  $id_TipoServ = (($_GET["id"]));
 $_SESSION['url'] = 'modificar_GP_TIPO_SERVICIO.php?id='.$id_TipoServ;
  $queryTipoServ = "SELECT TS.id_unico, 
                                 TS.nombre, 
                                 TS.tipo_medicion,
                                 TM.id_unico,
                                 TM.nombre
                                 FROM gp_tipo_servicio TS
                                 LEFT JOIN gp_tipo_medicion TM
                                 ON TS.tipo_medicion = TM.id_unico
                                 WHERE md5(TS.id_unico) = '$id_TipoServ'";
$resultado = $mysqli->query($queryTipoServ);
$row = mysqli_fetch_row($resultado);

//Medicion
$mediciones = "SELECT id_unico, nombre 
                FROM gp_tipo_medicion WHERE id_unico != '$row[3]' 
                ORDER BY id_unico ASC";
$medicion =   $mysqli->query($mediciones);


?>

<!-- Llamado a la cabecera del formulario -->
<?php    ?>
<title>Modificar Tipo Servicio</title>
</head>

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">
<!-- Llamado al menú del formulario -->  
    <?php require_once 'menu.php'; ?>
 
    <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
      <h2 align="center" class="tituloform">Modificar Tipo Servicio</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoServicioJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo ucwords(strtolower($row[1])); ?>" required>
            </div>

             <div class="form-group">
             <?php 
                $sql = "SELECT nombre,id_unico FROM gp_tipo_medicion ORDER BY nombre ASC";
                $rs = $mysqli->query($sql);
             ?>
             <label for="TipoMedicion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Medición:</label>
            <select name="sltMedicion" id="sltMedicion" class="form-control" title= "Seleccione Medición" style="height: 30px" required="required">    
                <option value="<?php echo $row[3] ?>"> <?php echo ucwords(strtolower($row[4])); ?> </option>
                <?php while($rowP = mysqli_fetch_assoc($medicion)){?>
                <option value="<?php echo $rowP['id_unico'] ?>"><?php echo ucwords((strtolower($rowP['nombre'])));}?></option>
                
              </select> 
            </div>
            
            
              <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
              </div>
            <input type="hidden" name="MM_insert" >
          </form>
<!-- Fin de división y contenedor del formulario -->           
        </div>     
    </div>
    <div class="col-sm-2 col-sm-2"  style="margin-top: -22px">
      <table class="tablaC table-condensed" style="margin-left: -3px; ">
        <thead>
          <th><h2 class="titulo" align="center" style=" font-size:17px; height: 35px;">Adicional</h2></th>
        </thead>
        <tbody>
          <tr>
              <td><a href="GP_CONCEPTO_SERVICIO.php?id=<?php echo md5($row[0]);?>"><button class="btn btnInfo btn-primary">Concepto</button></a><br/></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <!-- Fin del Contenedor principal -->
</div>

<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'; ?>
  </div> 

</body>
</html>
