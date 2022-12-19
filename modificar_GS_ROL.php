<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
//Captura de ID y consulta del resgistro correspondiente.
$id_rol = " ";
if (isset($_GET["id_rol"])){ 
    $id_rol = (($_GET["id_rol"]));
    $queryRol = "SELECT r.Id_Unico, r.Nombre
    FROM gs_rol r
    WHERE  md5(r.Id_Unico) = '$id_rol'";
}
$resultado = $mysqli->query($queryRol);
$row = mysqli_fetch_row($resultado);

?>  
        <!-- Link de css de la libreria css -->
        <link rel="stylesheet" href="css/select/select2.min.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <!-- Titulo de la pagina -->
        <title>Modificar Rol</title>
        <!-- Estilos -->
        <style type="text/css">
            .select2-container--default .select2-selection--single .select2-selection__rendered{
                max-height:30px
            }         
            .cabeza{
                white-space:nowrap;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">   
            <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Modificar Rol</h2>
                    <a href="GS_ROL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Rol: <?php echo ucwords(mb_strtolower($row[1]))?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top:-5px" class="client-form">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GS_ROLJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo ucwords((mb_strtolower($row[1])));?>" required>
                            </div>         
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Libreria de select2 -->
        <script type="text/javascript" src="js/select/select2.full.js"></script>
        <!-- Script para incluir la libreria select2 -->
        <script type="text/javascript">
            $("#sistema").select2({
                allowClear:true
            });
        </script>
        <?php require_once 'footer.php'; ?> 
    </body>
</html>
