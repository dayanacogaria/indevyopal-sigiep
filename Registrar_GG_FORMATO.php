<?php 
require_once('Conexion/conexion.php');
require_once 'head.php';
?>
<title>Registrar Formato</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <!--Titulo del formulario-->
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Formato</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/registrar_GG_FORMATOJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <!--Ingresa la información-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="descripcion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                            <textarea type="text" name="descripcion" id="descripcion" class="form-control" onkeypress="return txtValida(event,'num_car')" maxlength="1000" title="Ingrese la descripción"  placeholder="Descripción" required></textarea>
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
</body>
</html>

