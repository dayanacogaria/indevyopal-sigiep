<?php 
##################MODIFICACIONES#########################
#04/05/2017 | Erica G. | Diseño, tíldes, búsquedas


require_once 'head.php'; ?>
<?php 
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
//consultas para llenar los campos
  $clase = "SELECT id_unico, nombre FROM gf_clase_pptal ORDER BY nombre ASC";
  $claseP =   $mysqli->query($clase);
?>
<title>Registrar Tipo PAC</title>
</head>
<body>
 <link href="css/select/select2.min.css" rel="stylesheet">
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menu del formulario -->    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Tipo PAC</h2>
      <a href="listar_GF_TIPO_PAC.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Tipo PAC</h5>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoPacJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

               

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
            </div>



            <div class="form-group">
              <label for="claseP" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Presupuestal:</label>
              <select name="claseP" id="claseP" class="form-control select2_single" title="Seleccione tipo clase presupuestal" required>
                <option value="">Clase Presupuestal</option>
                <?php while($rowP = mysqli_fetch_assoc( $claseP)){?>
                <option value="<?php echo $rowP['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowP['nombre'])));}?></option>;
              </select> 
            </div>
          
 
            
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
<?php require_once 'footer.php' ?>  

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

