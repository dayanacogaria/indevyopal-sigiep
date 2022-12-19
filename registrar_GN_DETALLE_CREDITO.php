
<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>
   <title>Registrar Detalle Crédito</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Detalle Crédito</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetalleCreditoJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              
<!------------------------- Consulta para llenar campo Crédito-->
                        <?php 
                        $sql = "SELECT id_unico, numerocredito FROM gn_credito";
                        $credito = $mysqli->query($sql);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Crédito:
                            </label>
                            <select name="sltCredito" class="form-control" id="sltCredito" title="Seleccione crédito" style="height: 30px" required="">
                            <option value="">Crédito</option>
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
                        $sql = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto";
                        $concepto = $mysqli->query($sql);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Concepto:
                            </label>
                            <select name="sltConcepto" class="form-control" id="sltConcepto" title="Seleccione concepto" style="height: 30px" required="">
                            <option value="">Concepto</option>
                                <?php 
                                while ($fila1 = mysqli_fetch_row($concepto)) { ?>
                                <option value="<?php echo $fila1[0];?>"><?php echo ucwords(($fila1[1])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar campo Concepto-->
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
    