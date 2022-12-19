<?php
require_once 'head.php';
require_once('Conexion/conexion.php');
$id = " ";
$queryCond = "";
$row="";
if (isset($_GET["id"])) {
    $id = (($_GET["id"]));
    $queryCond = "SELECT Id_Unico, Nombre
    FROM gp_ruta 
    WHERE md5(Id_Unico) = '$id'";
$resul = $mysqli->query($queryCond);
$row = mysqli_fetch_row($resul);
}
?>
<title>Modificar Ruta</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script src="dist/jquery.validate.js"></script>
<link href="css/select/select2.min.css" rel="stylesheet">
<style>
    label#nombre-error{
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;
    }
</style>
<script>
    $().ready(function () {
        var validator = $("#form").validate({
            ignore: "",

            errorPlacement: function (error, element) {

                $(element)
                        .closest("form")
                        .find("label[for='" + element.attr("id") + "']")
                        .append(error);
            },
            rules: {
            }
        });

        $(".cancel").click(function () {
            validator.resetForm();
        });
    });
</script>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Ruta</h2>
                <a href="LISTAR_GP_RUTA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?PHP ECHO $row[1]?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GP_RUTAJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event, 'num_car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" value="<?php echo $row[1]; ?>" required>
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
<?php require_once 'footer.php'; ?>
</body>
</html>
