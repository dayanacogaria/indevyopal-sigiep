<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');


//consulta los tipos de contratos que hay para mostrarlos en el combo
$contratos = "SELECT Id_Unico, Nombre FROM gf_tipo_contrato  ORDER BY Nombre ASC";
$contrato =   $mysqli->query($contratos);


?>
 <!--Titulo de  la paginá-->

<title>Registrar Clase Contrato</title>
</head>
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
    
  <?php require_once 'menu.php';?>

    <div class="col-sm-10 text-left">
       <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Clase Contrato</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarContratoJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>


            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="txNombres()" placeholder="Nombre" required>
            </div>

            <div class="form-group">
              <label for="contrato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Contrato:</label>
              <select name="contrato" id="contrato" class="form-control" title="Seleccione el tipo contrato" required>
                <option value="">Tipo Contrato</option>
                <?php while($row = mysqli_fetch_assoc($contrato)){?>
                <option value="<?php echo $row['Id_Unico'] ?>"><?php echo ucwords((strtolower($row['Nombre'])));}?></option>;
              </select> 
            </div>
          
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert" >
          </form>
        </div>

      
      
    </div>

  </div>
</div>
<?php require_once 'footer.php';?>

</body>
</html>

