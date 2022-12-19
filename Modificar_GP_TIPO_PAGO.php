<?php 
require_once('Conexion/conexion.php');
require_once 'head.php';
$compania = $_SESSION['compania'];
$id=$_GET['id'];
$tipo = "SELECT tpg.id_unico, tpg.nombre,tpg.tipo_comprobante,
    tpc.nombre,tpc.sigla, cb.id_unico, cb.numerocuenta, cb.descripcion, tpg.retencion  
    FROM gp_tipo_pago tpg 
    LEFT JOIN gf_tipo_comprobante tpc ON tpc.id_unico = tpg.tipo_comprobante 
    LEFT JOIN gf_cuenta_bancaria cb ON tpg.cuenta_bancaria = cb.id_unico 
    WHERE md5(tpg.id_unico)= '$id'";
$tipo = $mysqli->query($tipo);
$rowTipo = mysqli_fetch_row($tipo);
$tipo = $rowTipo[2];
$anno = $_SESSION['anno'];
?>
<title>Modificar Tipo Pago</title>
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
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 2px">Modificar Tipo Pago</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top: -12px" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GP_TIPO_PAGOJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <!--Ingresa la información-->
                        <input name="id" id="id" type="hidden" value="<?php echo $rowTipo[0]?>">
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required value="<?php echo $rowTipo[1]?>">
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                              <label for="tipoC" class="col-sm-5 control-label">Tipo comprobante:</label>
                              <select name="tipoC" id="tipoC" title="Tipo comprobante" class="col-sm-1 form-control" style="width: 300px">
                                <?php 
                                if(empty($tipo)){
                                  echo "<option value=''>Tipo comprobante</option>";
                                  $sqlT = "SELECT id_unico,nombre,sigla FROM gf_tipo_comprobante WHERE compania = $compania";
                                  $resultT = $mysqli->query($sqlT);
                                  while ($t = mysqli_fetch_row($resultT)) {
                                    echo "<option value=".$t[0].">".ucwords(strtolower($t[1])).PHP_EOL.$t[2]."</option>";
                                  }
                                }else{
                                  echo "<option value=".$tipo.">".ucwords(strtolower($rowTipo[3])).PHP_EOL.$rowTipo[4]."</option>";
                                  $sqlT = "SELECT id_unico,nombre,sigla FROM gf_tipo_comprobante WHERE id_unico != $tipo AND compania = $compania";
                                  $resultT = $mysqli->query($sqlT);
                                  while ($t = mysqli_fetch_row($resultT)) {
                                    echo "<option value=".$t[0].">".ucwords(mb_strtolower($t[1])).PHP_EOL.$t[2]."</option>";
                                  }
                                  echo "<option></option>";
                                }
                                 ?>
                              </select>
                            </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="banco" class="col-sm-5 control-label">Banco:</label>
                            <select name="banco" id="banco" title="Banco" class="col-sm-1 form-control" style="width: 300px">
                              <?php    
                              if(empty($rowTipo[5])){
                                echo "<option value=''> - </option>";
                                $idc = 0;
                              } else {
                                  echo "<option value=".$rowTipo[5].">".$rowTipo[6].' - '.ucwords(mb_strtolower($rowTipo[7]))."</option>";
                                  echo "<option value=''> - </option>";
                                  $idc = $rowTipo[5];
                              }
                                $sqlT = "SELECT id_unico,numerocuenta,descripcion 
                                    FROM gf_cuenta_bancaria WHERE id_unico != $idc AND parametrizacionanno = $anno";
                                $resultT = $mysqli->query($sqlT);
                                while ($t = mysqli_fetch_row($resultT)) {
                                  echo "<option value=".$t[0].">".$t[1].' - '.ucwords(mb_strtolower($t[2]))."</option>";
                                }                                
                               ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="retencion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Retención:</label>
                            <?php    
                              if($rowTipo[8]==1){
                                  echo '<input type="radio" name="retencion" id="retencion" value="1" checked="checked">Si'
                                  . '<input type="radio" name="retencion" id="retencion" value="2">No';
                              } else {
                                  echo '<input type="radio" name="retencion" id="retencion" value="1" >Si'
                                  . '<input type="radio" name="retencion" id="retencion" value="2" checked="checked">No';
                              }?>
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

