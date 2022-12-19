<?php
    require_once 'head.php';
    require_once('Conexion/conexion.php');
    //consulta para cargar la informacion guardada con ese id
    $id = " ";
    $queryCond="";
    if (isset($_GET["id"])){
        $id = (($_GET["id"]));
        $queryCond = "SELECT id_unico, descripcion FROM gf_ficha  WHERE md5(id_unico) = '$id'";
    }
    $resul = $mysqli->query($queryCond);
    $row = mysqli_fetch_row($resul);
    $_SESSION['url'] = "modificar_GF_FICHA.php?id=".(($_GET["id"]));
?>
    <!--Titulo de la página-->
    <title>Modificar Ficha</title>
</head>
<body>
        <div class="container-fluid text-center">
            <div class="row content">   
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left">
                <!--titulo de formulario-->
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px">Modificar Ficha</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGFFicha.php?action=modify">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>           
                            <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                            <!--Carga los datos para la modificación-->
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                                <input type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" maxlength="150" title="Ingrese la descripción" onkeypress="return txtValida(event,'car')" placeholder="Descripción" value="<?php echo ucwords(strtolower($row[1]));?>" required>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div>
                <div class="col-sm-8 col-sm-1">
                    <table class="tablaC table-condensed" style="margin-top: -22px;">
                        <thead>
                            <tr>
                                <tr>                                    
                                    <th>
                                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                    </th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>                                    
                                    <div style="margin-bottom: 1px;" id="div">
                                        <a class="btn btnConsultas" href="registrar_GF_FICHA_INVENTARIO.php?ficha=<?php echo $id; ?>"  id="linkMovE">
                                            FICHA<br/>INVENTARIO
                                        </a>                                        
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php  require_once 'footer.php';?>
    </body>
</html>
