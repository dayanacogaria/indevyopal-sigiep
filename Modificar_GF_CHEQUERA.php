<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
$id = $_GET['id'];

//Registro
$che= "SELECT "
        . "c.id_unico, "
        . "c.numerochequera, "
        . "c.numeroinicial, "
        . "c.numerofinal, "
        . "ec.id_unico, "
        . "ec.nombre, "
        . "cb.id_unico, "
        . "cb.numerocuenta, "
        . "cb.descripcion  "
        . "FROM gf_chequera c "
        . "LEFT JOIN gf_estado_chequera ec ON c.estadochequera= ec.id_unico "
        . "LEFT JOIN gf_cuenta_bancaria cb ON cb.id_unico=c.cuentabancaria " 
        . "WHERE md5(c.id_unico)= '$id'";
$cheq= $mysqli->query($che);
$row = mysqli_fetch_row($cheq);
//Estado Chequera
$es= "SELECT id_unico, nombre FROM gf_estado_chequera WHERE id_unico != $row[4] ORDER BY nombre ASC";
$estado = $mysqli->query($es);

//Cuenta bancaria
$cue= "SELECT id_unico, numerocuenta, descripcion FROM gf_cuenta_bancaria WHERE id_unico != $row[6] ORDER BY numerocuenta ASC";
$cuenta = $mysqli->query($cue);
?>

<title>Modificar Chequera</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -10px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;">Modificar Chequera</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GF_CHEQUERAJson.php"  >
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" id="id" name="id" value="<?php echo $row[0]?>">
                        <!-- Número de chequera-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="numero" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Chequera:</label>
                            <input style="display:inline" type="text" name="numero" id="numero" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número de chequera"  placeholder="Número Chequera" required="required" value="<?php echo $row[1];?>">
                        </div>
                        <!-- Número inicial-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="numeroI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Inicial:</label>
                            <input type="text" name="numeroI" id="numeroI" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número inicial"  placeholder="Número Inicial" required="required" value="<?php echo $row[2];?>">
                        </div>
                        <!-- Número final-->
                        <div class="form-group" id="numeroFinal" name="numeroFinal" style="margin-top: -10px;">
                            <label for="numeroF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Final:</label>
                            <input type="text" name="numeroF" id="numeroF" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="50" title="Ingrese el número final" placeholder="Número Final" required="required" value="<?php echo $row[3];?>">
                           
                        </div>
                        <!-- Estado chequera--> 
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="estado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado:</label>
                            <select name="estado" id="estado" title="Seleccione estado"  required="required">
                                <option value="<?php echo $row[4]?>"><?php echo ucwords(strtolower($row[5]))?></option>
                                <?php while ($rowEstado = mysqli_fetch_row($estado)){ ?>
                                <option value="<?php echo $rowEstado[0];?>"><?php echo ucwords(strtolower($rowEstado[1]));?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <!-- Cuenta bancaria--> 
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="cuenta" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta Bancaria:</label>
                            <select name="cuenta" id="cuenta" title="Seleccione cuenta bancaria" required="required">
                                <option value="<?php echo $row[6]?>"><?php echo ucwords(strtolower($row[7].' - '.$row[8]));?></option>
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

