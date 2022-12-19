<?php
##################MODIFICACIONES#########################
#10/04/2017 | Erica G. | Diseño, tíldes, búsquedas 
######################################################## 
?>
<?php require_once 'head.php'; ?>
<?php 
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
//consultas para llenar los campos
  $tipo = "SELECT Id_Unico, Nombre FROM gf_tipo_clase_pptal ORDER BY Nombre ASC";
  $tipoC =   $mysqli->query($tipo);

  $clase = "SELECT Id_Unico, Nombre FROM gf_clase_pptal ORDER BY Nombre ASC";
  $claseA = $mysqli->query( $clase);

?>
<title>Registrar Clase Presupuestal</title>
<link href="css/select/select2.min.css" rel="stylesheet">
</head>
<body>
 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menu del formulario -->    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-right: 4px; margin-left: 4px;">Registrar Clase Presupuestal</h2>
      <a href="listar_GF_CLASE_PPTAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
      <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">  Tipo</h5>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarClasePptalJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

               

            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
            </div>



            <div class="form-group">
              <label for="tipoC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Clase:</label>
              <select name="tipoC" id="tipoC" class="select2_single form-control" title="Seleccione tipo clase presupuestal" required>
                <option value="">Tipo Clase</option>
                <?php while($rowT = mysqli_fetch_assoc( $tipoC)){?>
                <option value="<?php echo $rowT['Id_Unico'] ?>"><?php echo ucwords((mb_strtolower($rowT['Nombre'])));}?></option>;
              </select> 
            </div>
          
            <div class="form-group">
              <label for="claseA" class="col-sm-5 control-label">Clase Afectar:</label>
              <select name="claseA" id="claseA" class="select2_single form-control" title="Seleccione la clase afectar">
                <option value="">Clase Afectar</option>
                <?php while($rowA = mysqli_fetch_assoc($claseA)){?>
                <option value="<?php echo $rowA['Id_Unico'] ?>"><?php echo ucwords((mb_strtolower($rowA['Nombre'])));}?></option>;
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

