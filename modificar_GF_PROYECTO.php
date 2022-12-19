<!-- Llamado a la cabecera del formulario -->
<?php require_once('Conexion/conexion.php');
require_once 'head.php';
$id_proy = $_GET["id_proy"];
$queryProy = "SELECT Id_Unico, Nombre, codigo, codigo_bpin FROM gf_proyecto  WHERE md5(Id_Unico) ='$id_proy'";
$resultado = $mysqli->query($queryProy);
$row = mysqli_fetch_row($resultado);
?>
<title>Modificar Proyecto</title>
</head> 
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Proyecto</h2>
            <a href="listar_GF_PROYECTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $row[1]; ?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarProyectoJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                     <div class="form-group" style="margin-top: -10px;">
                        <label for="codigo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código:</label>
                        <input type="text" name="codigo" id="codigo" class="form-control" maxlength="150" title="Ingrese el Código" onkeypress="return txtValida(event, 'num_car')" placeholder="Código" required value="<?= $row[2] ?>">
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required value="<?= $row[1] ?>">
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="codigobpin" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código BPIN:</label>
                        <input type="text" name="codigobpin" id="codigobpin" class="form-control" maxlength="150" title="Ingrese el Código BPIN" onkeypress="return txtValida(event, 'num_car')" placeholder="Código BPIN" required value="<?= $row[3] ?>">
                    </div>
                    <div align="center">
                        <button type="submit" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: -10px; margin-bottom: 10px; margin-left: -100px;">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>
</body>
</html>
