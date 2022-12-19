<?php 
#09/03/2017 --- Nestor B --- se modificó la función strtoupper por mb_strtolower para que al traer la información me convierta la cadena en minuscula
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
 $id = $_GET["id"];
 $queryCond = "SELECT 
                        tp.id_unico,
                        tp.nombre, 
                        tp.tiposoipensionado,
                        tsp.id_unico,
                        tsp.nombre
                        FROM gn_tipo_pensionado tp
    LEFT JOIN gn_tipo_soi_pensionado tsp ON tp.tiposoipensionado = tsp.id_unico
    WHERE md5(tp.id_unico) = '$id'"; 

 $resul = $mysqli->query($queryCond);
 $row    = mysqli_fetch_row($resul);
 $tpid   = $row[0];
 $tpnom  = $row[1];
 $tptsp  = $row[2];
 $tspid  = $row[3];
 $tspnom = $row[4];
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './head.php';
?>
<title>Modificar Tipo Pensionado</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tipo Pensionado</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoPensionadoJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
                                 <input type="text" name="txtNombre" value="<?php echo ucwords((mb_strtolower($row[1]))) ?>" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                            </div>
<!------------------------- Consulta para llenar campo Tipo SOI Pensionado-->
                        <?php 
                        $sql = "SELECT id_unico, nombre FROM gn_tipo_soi_pensionado where id_unico != $tspid";
                        $tiposoi = $mysqli->query($sql);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Tipo SOI Pensionado:
                            </label>
                            <select name="sltTipoSP" class="form-control" id="sltTipoSP" title="Seleccione tipo SOI" style="height: 30px">
                            <option value="<?php echo $tspid?>"><?php echo $tspnom?></option>
                                <?php 
                                while ($fila1 = mysqli_fetch_row($tiposoi)) { ?>
                                <option value="<?php echo $fila1[0];?>"><?php echo ucwords(($fila1[1])); ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar campo Concepto-->                              
                                      
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
