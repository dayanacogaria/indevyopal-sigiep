<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');

//Estado Chequera
$es= "SELECT id_unico, nombre FROM gf_estado_chequera ORDER BY nombre ASC";
$estado = $mysqli->query($es);

//Cuenta bancaria
$cue= "SELECT id_unico, numerocuenta, descripcion FROM gf_cuenta_bancaria ORDER BY numerocuenta ASC";
$cuenta = $mysqli->query($cue);
?>

<title>Registrar Chequera</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -10px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;">Registrar Chequera</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_CHEQUERAJson.php"  >
                       
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <!-- Número de chequera-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="numero" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Chequera:</label>
                            <input style="display:inline" type="text" name="numero" id="numero" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número de chequera"  placeholder="Número Chequera" required="required">
                        </div>
                        <!-- Número inicial-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="numeroI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Inicial:</label>
                            <input type="text" name="numeroI" id="numeroI" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número inicial"  placeholder="Número Inicial" required="required" >
                        </div>
                        <!-- Número final-->
                        <div class="form-group" id="numeroFinal" name="numeroFinal" style="margin-top: -10px;">
                            <label for="numeroF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Final:</label>
                            <input type="text" name="numeroF" id="numeroF" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número final" placeholder="Número Final" required="required" >
                           
                        </div>
                        <!-- Estado chequera--> 
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="estado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado:</label>
                            <select name="estado" id="estado" title="Seleccione estado"  required="required">
                                <option value="">Estado</option>
                                <?php while ($rowEstado = mysqli_fetch_row($estado)){ ?>
                                <option value="<?php echo $rowEstado[0];?>"><?php echo ucwords(strtolower($rowEstado[1]));?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <!-- Cuenta bancaria--> 
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="cuenta" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta Bancaria:</label>
                            <select name="cuenta" id="cuenta" title="Seleccione cuenta bancaria" required="required">
                                <option value="">Cuenta Bancaria</option>
                                <?php while ($rowCuenta = mysqli_fetch_row($cuenta)){ ?>
                                <option value="<?php echo $rowCuenta[0];?>"><?php echo ucwords(strtolower($rowCuenta[1].' - '.$rowCuenta[2]));?></option>
                                <?php } ?>
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

