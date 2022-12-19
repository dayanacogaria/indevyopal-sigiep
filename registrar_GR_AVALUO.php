<?php

################ MODIFICACIONES ####################
#02/06/2017 | Anderson Alarcon | modifique validaciones de registro, tipo fondo y predio
#02/06/2017 | Anderson Alarcon | modifique consulta de select tipo fondo
####################################################


require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>
   <title>Registrar Avalúo</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Avalúo</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarAvaluoPJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              <!-------Campo para ingresar Valor------------->
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="valor" class="col-sm-5 control-label"><strong class="obligado">*</strong>Valor:</label>
                                <input type="text" name="txtValor" id="txtValor" class="form-control" maxlength="20" title="Ingrese el valor" onkeypress="return txtValida(event,'num')" placeholder="Valor" required>
                            </div>
                            <!-------Fin Campo para ingresar Valor------------->
                            <!-------Campo para seleccionar Indicador------------->
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="indicador" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;">*</strong>Indicador:</label>
                                <input  type="radio" name="sltIndicador" id="sltIndicador"  value="1" checked>SI
                                <input  type="radio" name="sltIndicador" id="sltIndicador" value="2" >NO
                            </div>
                            <!-------Fin Campo para seleccionar Indicador------------->
                            <!------------------------- Consulta para llenar campo Tarifa-->
                            <?php 
                                $tf = "SELECT t.id_unico AS id_unico_tarifa,tt.nombre AS nombre_tipo_tarifa FROM `gp_tarifa` t LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa=tt.id_unico";
                                $tar = $mysqli->query($tf);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Tipo Fondo:
                                </label>
                                <select name="sltTarifa" class="form-control" id="sltTarifa" title="Seleccione Tarifa" style="height: 30px" required>
                                <option value="">Tarifa</option>                
                                <?php 
                                    while ($filaT = mysqli_fetch_array($tar)) { ?>                   
                                    <option value="<?php echo $filaT['id_unico_tarifa'];?>"><?php echo $filaT['nombre_tipo_tarifa'];?></option>
                                <?php
                                }
                                ?>
                                </select>   
                            </div>
                            <!------------------------- Fin Consulta para llenar Tarifa-->
                            <!------------------------- Consulta para llenar campo Predio-->
                            <?php 
                                $pr = "SELECT id_unico, nombre FROM gp_predio1";
                                $pre = $mysqli->query($pr);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Predio:
                                </label>
                                <select name="sltPredio" class="form-control" id="sltPredio" title="Seleccione Predio" style="height: 30px" required>
                                <option value="">Predio</option>                
                                <?php 
                                    while ($filaP = mysqli_fetch_row($pre)) { ?>                   
                                    <option value="<?php echo $filaP[0];?>"><?php echo $filaP[1];?></option>
                                <?php
                                }
                                ?>
                                </select>   
                            </div>
                            <!------------------------- Fin Consulta para llenar Predio-->
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