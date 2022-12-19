<?php 
require_once('Conexion/conexion.php');
require_once 'head.php';
$compania = $_SESSION['compania'];
$anno = $_SESSION['anno'];
?>
<title>Registrar Tipo Pago</title>
<!-- Librerias de carga para el datapicker -->
  <link rel="stylesheet" href="css/jquery-ui.css">
  <script src="js/jquery-ui.js"></script>
  <!-- select2 -->
  <link rel="stylesheet" href="css/select2.css">
  <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <!--Titulo del formulario-->
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 2px">Registrar Tipo Pago</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top: 15px" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GP_TIPO_PAGOJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <!--Ingresa la información-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required>
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                              <label for="tipoC" class="col-sm-5 control-label">Tipo comprobante:</label>
                              <select name="tipoC" id="tipoC" title="Tipo comprobante" class="col-sm-1 form-control" style="width: 300px">
                                <?php                                 
                                  echo "<option value=''>Tipo comprobante</option>";
                                  $sqlT = "SELECT id_unico,nombre,sigla FROM gf_tipo_comprobante WHERE compania = $compania";
                                  $resultT = $mysqli->query($sqlT);
                                  while ($t = mysqli_fetch_row($resultT)) {
                                    echo "<option value=".$t[0].">".ucwords(strtolower($t[1])).PHP_EOL.$t[2]."</option>";
                                  }                                
                                 ?>
                              </select>
                            </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="banco" class="col-sm-5 control-label">Banco:</label>
                            <select name="banco" id="banco" title="Banco" class="col-sm-1 form-control" style="width: 300px">
                              <?php                                 
                                echo "<option value=''>Banco</option>";
                                $sqlT = "SELECT id_unico,numerocuenta,descripcion 
                                    FROM gf_cuenta_bancaria WHERE parametrizacionanno = $anno";
                                $resultT = $mysqli->query($sqlT);
                                while ($t = mysqli_fetch_row($resultT)) {
                                  echo "<option value=".$t[0].">".$t[1].' - '.ucwords(strtolower($t[2]))."</option>";
                                }                                
                               ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="retencion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Retención:</label>
                            <input type="radio" name="retencion" id="retencion" value="1" >Si
                            <input type="radio" name="retencion" id="retencion" value="2" checked="checked">No
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php';?>
    <script type="text/javascript" src="js/select2.js"></script>
        <script>
          $("#tipoC").select2({
            allowClear:true
          });
          $("#banco").select2({
            allowClear:true
          });
        </script>
</body>
</html>

