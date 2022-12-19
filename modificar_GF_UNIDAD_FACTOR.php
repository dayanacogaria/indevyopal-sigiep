<?php
require_once 'head.php';
require_once('Conexion/conexion.php');
list($id_unid_fact, $id, $nom, $aso, $valor) = array("", 0, "", 0, 0);
if (isset($_GET["id_unid_fact"])){
    $id_unid_fact  = (($_GET["id_unid_fact"]));
    $queryUnidFact = "SELECT id_unico, nombre, valor, codigo_fe FROM gf_unidad_factor
    WHERE md5(Id_Unico) = '$id_unid_fact'";
    $resultado = $mysqli->query($queryUnidFact);
    $row = mysqli_fetch_row($resultado);
    $id = $row[0]; $nom = $row[1]; $valor = $row[2];
}

?>
    <title>Modificar Unidad Factor</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0;">Modificar Unidad Factor</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GF_UNIDAD_FACTORJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" value="<?php echo ucwords(mb_strtoupper($nom));?>" required style="width: 100%;">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Valor:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="valor" id="valor" class="form-control" maxlength="100" title="Ingrese el Valor" onkeypress="return txtValida(event, 'dec')" placeholder="Valor" value="<?php echo $valor;?>"  style="width: 100%;">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="codigo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código DIAN:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="codigo" id="codigo" class="form-control" maxlength="100" title="Ingrese el Código DIAN" onkeypress="return txtValida(event, 'num_car')" placeholder="Código DIAN"  style="width: 100%;" required="required" value="<?=$row[3];?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: 10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select").select2();
    </script>
</body>
</html>
