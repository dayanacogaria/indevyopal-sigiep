<?php 
  require_once('Conexion/conexion.php');
  require_once 'head.php'; 
//Tipo Medicion
  $sql = "SELECT nombre,id_unico FROM gp_tipo_medicion ORDER BY nombre ASC";
  $rs = $mysqli->query($sql);

?>
<?php ?>
<title>Registrar Tipo Servicio</title>
</head>
<body>
 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menu del formulario -->    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
      <h2 align="center" class="tituloform">Registrar Tipo Servicio</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoServicioJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

               

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
            </div>


            <div class="form-group">
              <?php 
                
             ?>
            <label for="TipoMedicion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Medición:</label>
            <select name="sltMedicion" id="sltMedicion" class="form-control" title="Seleccione Medición" style="height: 30px" required="required">
            <option value="">Medición</option>
            <?php 
                while ($fila = mysqli_fetch_row($rs)) 
                { 
            ?>
            <option value="<?php echo $fila[1];?>"><?php echo ucwords(strtolower($fila[0]));?></option>                                
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
            <td><button class="btn btnInfo btn-primary" disabled="true">Concepto</button><br/></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <!-- Fin del Contenedor principal -->
</div>
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php' ?>  

</body>
</html>

