<?php
require_once 'head.php';
require_once('Conexion/conexion.php');
?>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <title>Registrar Unidad Factor</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0;">Registrar Unidad Factor</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_UNIDAD_FACTORJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required style="width: 100%;">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Valor:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="valor" id="valor" class="form-control" maxlength="100" title="Ingrese el Valor" onkeypress="return txtValida(event, 'dec')" placeholder="Valor"  style="width: 100%;">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="codigo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código DIAN:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="codigo" id="codigo" class="form-control" maxlength="100" title="Ingrese el Código DIAN" onkeypress="return txtValida(event, 'num_car')" placeholder="Código DIAN"  style="width: 100%;" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="no" class="col-sm-5 col-md-5 col-lg-5 control-label"></label>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <button type="submit" class="btn btn-primary sombra" style="margin-top: 10px;margin-bottom: 10px; margin-left: 0px;">Guardar</button>
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

