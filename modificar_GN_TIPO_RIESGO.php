<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();

$id = (($_GET["id"]));
$sql1 = "SELECT id_unico, nombre, valor, centro_trab FROM gn_categoria_riesgos WHERE md5(id_unico) = '$id'";
$sq = $mysqli->query($sql1);
$row = mysqli_fetch_row($sq);
?>

   <title>Modificar Tipo Riesgo</title>
   
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar  Tipos de Riesgos Profesionles</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoRiesgoJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 100%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                            <div class="form-group" style="margin-top: -10px; font-size: 13px;">
                                <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" value="<?php echo $row[1]; ?>" title="Ingrese el nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre" required>
                            </div>
                            <div class="form-group" style="margin-top: -10px;font-size: 13px;">
                                <label for="tarifa" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tarifa:</label>
                                <input type="text" name="tarifa" id="tarifa" class="form-control" maxlength="100" title="Ingrese la tarifa" value="<?php echo $row[2]; ?>" onkeypress="return txtValida(event,'decimales')" placeholder="Tarifa" required>
                            </div>
                            <div class="form-group" style="margin-top: -10px;font-size: 13px;">
                                <label for="centro" class="col-sm-5 control-label"><strong class="obligado">*</strong>Centro de Trabajo:</label>
                                <input type="text" name="centro" id="centro" class="form-control" maxlength="100" title="Ingrese el centro de trabajo" value="<?php echo $row[3]; ?>" onkeypress="return txtValida(event,'num')" placeholder="Centro de Trabajo" required>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                               <label for="no" class="col-sm-5 control-label"></label>
                               <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                            </div>
                          </form>
                      </div>
                  </div>                  
              </div>
        </div>
        <?php require_once './footer.php'; ?>
    </body>
</html>