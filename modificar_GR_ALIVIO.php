<?php

/* 
 * ************
 * ***Autor*****
 * **DANIEL.NC***
 * ***************
 */

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
 $id = $_GET["id"];
 $queryCond = "SELECT id_unico, anio, numacuerdo, descripcion FROM gr_alivio
    WHERE md5(id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);

require_once './head.php';
?>
<title>Modificar Alivio</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Alivio</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarAlivioPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                             <!-------Campo para ingresar Año------------->
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Año:</label>
                                <input type="text" name="txtAnio" id="txtAnio" value="<?php echo $row[1]?>" class="form-control" maxlength="4" title="Ingrese el año" onkeypress="return txtValida(event,'num')" placeholder="Año" required>
                            </div>
                            <!-------Fin Campo para ingresar Año------------->
                            <!-------Campo para ingresar Numero Acuerdo------------->
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Número Acuerdo:</label>
                                <input type="text" name="txtNumAcuerdo" id="txtNumAcuerdo" value="<?php echo $row[2]?>" class="form-control" maxlength="50" title="Ingrese número acuerdo" onkeypress="return txtValida(event,'num_car')" placeholder="Número Acuerdo" required>
                            </div>
                            <!-------Fin Campo para ingresar Numero Acuerdo------------->
                            <!-------Campo para ingresar Descripción------------->
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="Descripcion" class="col-sm-5 control-label"><strong class="obligado">*</strong>Descripción:</label>
                                <input type="text" name="txtDescripcion" id="txtDescripcion" value="<?php echo $row[3]?>" class="form-control" maxlength="500" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                            </div>
                            <!-------Fin Campo para ingresar Descripción------------->
                                      
                            <div class="form-group" style="margin-top: 10px;">
                              <label for="no" class="col-sm-5 control-label"></label>
                              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            </div>


                          </form>
                      </div>
                  </div>                  
              </div>
        </div>
        <?php require_once './footer.php'; ?>
    </body>
</html>