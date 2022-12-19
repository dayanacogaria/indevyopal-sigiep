<?php 
require_once('Conexion/conexion.php');
require_once 'head.php';
?>
<title>Registrar Tipo Proceso</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
                <h2 align="center" class="tituloform">Registrar Tipo Proceso</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/registrar_GG_TIPO_PROCESOJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <!--Ingresa la información-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="identificador" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Identificador:</label>
                            <input type="text" name="identificador" id="identificador" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="10" title="Ingrese el identificador"  placeholder="Identificador" required>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required>
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-6 col-sm-2" style="margin-top:-22px" >
                <table class="tablaC table-condensed" style="margin-left: -3px; ">
                    <thead>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px; height:36px">Adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <button class="btn btnInfo btn-primary" disabled="true" >CARACTERÍSTICA</button><br/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php';?>
</body>
</html>

