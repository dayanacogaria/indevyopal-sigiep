<?php 
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
  session_start();

//consultas para llenar los campos
  $medidores = "SELECT TM.id_unico, 
                        TM.nombre, 
                        TM.tipo_medicion,
                        TMD.id_unico,
                        TMD.nombre
                        FROM gp_tipo_medicion TM
                        LEFT JOIN gp_tipo_medidor TMD
                        ON TM.tipo_medidor = TMD.id_unico
                        ORDER BY nombre ASC";

  $medidor =   $mysqli->query($medidores);

?>

<!-- Llamado a la cabecera del formulario -->
<?php require_once 'head.php'; ?>
<title>Registrar Tipo Medición</title>
</head>
<body>
 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menu del formulario -->    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Tipo Medición</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoMedicionJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

               

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
            </div>


            <div class="form-group">
              <?php 
                $sql = "SELECT nombre,id_unico FROM gp_tipo_medidor ORDER BY nombre ASC";
                $rs = $mysqli->query($sql);
             ?>
            <label for="TipoMedidor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Medidor:</label>
            <select name="sltMedidor" id="sltMedidor" class="form-control" title=
                    "Seleccione Tipo Medidor" style="height: 30px">
            <option value="">Medidor</option>
            <?php 
                while ($fila = mysqli_fetch_row($rs)) 
                { 
            ?>
            <option value="<?php echo $fila[1];?>"><?php echo ucwords(($fila[0]));?></option>                                
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
  </div>
  <!-- Fin del Contenedor principal -->
</div>
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php' ?>  

</body>
</html>

