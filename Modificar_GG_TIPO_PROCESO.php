<?php 
require_once('Conexion/conexion.php');
require_once 'head.php';
$id=$_GET['id'];
$tipo = "SELECT id_unico, identificador, nombre FROM gg_tipo_proceso WHERE md5(id_unico)= '$id'";
$tipo = $mysqli->query($tipo);
$rowTipo = mysqli_fetch_row($tipo);
$_SESSION['url']='Modificar_GG_TIPO_PROCESO.php?id='.$id;
?>
<title>Modificar Tipo Proceso</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
                <h2 align="center" class="tituloform">Modificar Tipo Proceso</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/modificar_GG_TIPO_PROCESOJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <!--Ingresa la información-->
                        <input name="id" id="id" type="hidden" value="<?php echo $rowTipo[0]?>">
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="identificador" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Identificador:</label>
                            <input type="text" name="identificador" id="identificador" class="form-control" onkeypress="return txtValida(event,'num')" maxlength="10" title="Ingrese el identificador"  placeholder="Identificador" required value="<?php echo $rowTipo[1]?>">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required value="<?php echo ucwords(strtolower($rowTipo[2]));?>">
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
                                <a href="GG_PROCESO_CARACTERISTICA.php?id=<?php echo $id?>"><button class="btn btnInfo btn-primary" >CARACTERÍSTICA</button></a><br/>
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

