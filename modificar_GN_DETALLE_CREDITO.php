<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT                dc.id_unico,
                                cr.id_unico,
                                cr.numerocredito,
                                c.id_unico,
                                c.codigo,
                                c.descripcion                                
                FROM		gn_detalle_credito dc                
                LEFT JOIN	gn_credito cr ON dc.credito = cr.id_unico
                LEFT JOIN	gn_concepto c ON dc.concepto = c.id_unico                
                where md5(dc.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
    $idcred  = $row[1];
    $numcred = $row[2];
    $idconc  = $row[3];
    $ccod    = $row[4];
    $cdesc   = $row[5];

/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<title>Modificar Detalle Crédito</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; 
                        

                        ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Detalle Crédito</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarDetalleCreditoJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              
<!------------------------- Consulta para llenar campo Crédito
                        <?php 
//                        $crd = "SELECT id_unico, numerocredito FROM gn_credito where id_unico != $idcred";
//                        $credito = $mysqli->query($crd);
//                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Crédito:
                            </label>
                            <select name="sltCredito" class="form-control" id="sltCredito" title="Seleccione crédito" style="height: 30px" required="">
                            <option value="<?php echo $idcred?>"><?php echo $numcred?></option>
                                //<?php 
//                                while ($filaC = mysqli_fetch_row($credito)) { ?>
                                <option value="////////<?php echo $filaC[0];?>"><?php echo ucwords(($filaC[1])); ?></option>
                                //<?php
//                                }
//                                ?>
                            </select>   
                        </div>-->
                        <?php 
                        $sql = "SELECT id_unico, numerocredito FROM gn_credito where id_unico != $idcred";
                        $credito = $mysqli->query($sql);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Crédito:
                            </label>
                            <select name="sltCredito" class="form-control" id="sltCredito" title="Seleccione crédito" style="height: 30px" required="">
                            <option value="<?php echo $idcred?>"><?php echo $numcred?></option>
                                <?php 
                                while ($filaC = mysqli_fetch_row($credito)) { ?>
                                <option value="<?php echo $filaC[0];?>"><?php echo ucwords(($filaC[1])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>

<!----------Fin Consulta Para llenar campo Credito-->                              
<!------------------------- Consulta para llenar campo Concepto-->
            <?php 
            $sql = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto where id_unico != '$idconc'";
            $concepto = $mysqli->query($sql);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Concepto:
                </label>
                <select required="required" name="sltConcepto" class="form-control" id="sltConcepto" title="Seleccione concepto" style="height: 30px">
                <option value="<?php echo $idconc;?>"><?php echo $ccod.' - '.$cdesc?></option>
                    <?php 
                    while ($fila1 = mysqli_fetch_row($concepto)) { ?>
                    <option value="<?php echo $fila1[0];?>"><?php echo $fila1[1]; ?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar campo Concepto-->
                                                           
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
