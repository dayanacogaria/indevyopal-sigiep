<?php 
  //llamado a la clase de conexion
  require_once('Conexion/conexion.php');
  session_start();
  //consultas para llenar los campos
  $clase = "SELECT id_unico, nombre FROM gf_clase_contable ORDER BY nombre ASC";
  $claseC =   $mysqli->query($clase);

  $forma = "SELECT id_unico, nombre FROM gf_tipo_documento ORDER BY nombre ASC";
  $formato = $mysqli->query($forma);

?>
<!-- Llamado a la cabecera del formulario -->
<?php require_once 'head.php'; ?>
<title>Registrar Tipo Comprobante</title>
</head>
<body> 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">
  <!-- Llamado al menu del formulario -->    
  <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Registrar Tipo Comprobante Contable</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
        <!-- inicio del formulario --> 
        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoComprobanteJson.php">
          <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>         
            <div class="form-group" style="margin-top: -20px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
            </div>
            <!---  para los radio Button 1 es verdadero, 2 es falso      -->
            <div class="form-group" style="margin-top:-20px">
              <label for="reten" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Retención:</label>
              <input type="radio" name="reten" id="reten"  value="1" checked>SI
              <input type="radio" name="reten" id="reten" value="2" checked>NO
            </div>
            <div class="form-group form-horizontal"  style="margin-top: -10px">  
              <label for="inter" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Interfaz:</label>
              <input type="radio" name="inter" id="inter"  value="1" checked>SI
              <input type="radio" name="inter" id="inter" value="2" checked>NO
            </div>
            <div class="form-group form-horizontal"  style="margin-top: -10px">  
              <label for="nif" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>NIIF:</label>
              <input type="radio" name="nif" id="nif"  value="1" checked>SI
              <input type="radio" name="nif" id="nif" value="2" checked>NO
            </div>
            <div class="form-group " style="margin-top: -10px">
              <label for="claseC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Contable:</label>
              <select name="claseC" id="claseC" class="form-control" title="Seleccione la clase contable" required>
                <option value="">Clase Contable</option>
                <?php while($rowC = mysqli_fetch_assoc($claseC)){?>
                <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords((strtolower($rowC['nombre'])));}?></option>;
              </select> 
            </div>
            <div class="form-group" style="margin-top: -20px;">
              <label for="formato" class="col-sm-5 control-label">Tipo Documento:</label>
              <select name="formato" id="formato" class="form-control" title="Seleccione el formato">
                <option value="">Tipo Documento</option>
                <?php while($rowF = mysqli_fetch_assoc($formato)){?>
                <option value="<?php echo $rowF['id_unico'] ?>"><?php echo ucwords((strtolower($rowF['nombre'])));}?></option>;
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
            </div>
          </div>
      </div>
    </div>
</body>
</html>

