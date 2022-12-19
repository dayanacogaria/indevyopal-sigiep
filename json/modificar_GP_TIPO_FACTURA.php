<?php require_once('Conexion/conexion.php');
 session_start();
 $id = $_GET["id"];
 $queryCond = "SELECT id_unico, nombre,servicio FROM gp_tipo_factura
    WHERE md5(id_unico) = '$id'"; 

 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './head.php';
?>
<title>Modificar Tipo Factura</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tipo Factura</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoFacturaJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
                                 <input type="text" name="nombre" value="<?php echo ucwords((strtoupper($row[1]))) ?>" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="serv" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Servicio:</label>
                                <?php switch ($row[2]) {
                                case 1: ?>
                                    <input type="radio" name="serv" id="serv"  value="1" checked>SI
                                    <input type="radio" name="serv" id="serv" value="2" >NO                  
                                <?php
                                    break;
                                case 2: ?>
                                    <input type="radio" name="serv" id="serv"  value="1" >SI
                                    <input type="radio" name="serv" id="serv" value="2" checked>NO
                                <?php
                                    break;
                                } ?> 

                                <div class="form-group" style="margin-top: 10px;">
                                    <label for="no" class="col-sm-5 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 30px; margin-left: 0px;">Guardar</button>
                                </div>
                            </div>
                          </form>
                      </div>
                  </div>                  
              </div>
        </div>
        <?php require_once './footer.php'; ?>
    </body>
</html>
