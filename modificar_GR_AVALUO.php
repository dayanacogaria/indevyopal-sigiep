<?php

################ MODIFICACIONES ####################
#02/06/2017 | Anderson Alarcon | Liste los datos del avaluo a modificar
#02/06/2017 | Anderson Alarcon | Cambie  consulta de select Tipo Fondo para que este quedara seleccionado con el valor registrado 
####################################################

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
 $id = $_GET["id"];
 $queryCond = "SELECT a.*,
        t.id_unico AS id_unico_tarifa,
        tpt.id_unico AS id_unico_tipo_tarifa,  
        tpt.nombre AS nombreTipoTarifa,
        p.id_unico AS id_unico_predio, 
        p.nombre FROM gr_avaluo a 
        LEFT JOIN gp_tarifa t ON a.tarifa = t.id_unico
        LEFT JOIN gp_tipo_tarifa tpt ON t.tipo_tarifa=tpt.id_unico
        LEFT JOIN gp_predio1 p ON a.predio = p.id_unico
    WHERE md5(a.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_array($resul);

                                        $aid  = $row['id_unico'];
                                        $aval = $row['valor'];
                                        $aind = $row['indicador'];
                                        $atar = $row['tarifa'];
                                        $apre = $row['predio'];

                                        $tid  = $row['id_unico_tarifa'];
                                        $tptid = $row['id_unico_tipo_tarifa'];//id unico tipo tarifa  
                                        $tnom = $row['nombreTipoTarifa'];//muestro nombre del tipo de tarifa  
                                        $pid  = $row['id_unico_predio'];
                                        $pnom = $row['nombre'];

require_once './head.php';
?>
<title>Modificar Avalúo</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Avalúo</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarAvaluoPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                             <!-------Campo para ingresar Valor------------->
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="valor" class="col-sm-5 control-label"><strong class="obligado">*</strong>Valor:</label>
                                <input type="text" name="txtValor" id="txtValor" value="<?php echo $aval?>" class="form-control" maxlength="20" title="Ingrese el valor" onkeypress="return txtValida(event,'num')" placeholder="Valor" required>
                            </div>
                            <!-------Fin Campo para ingresar Valor------------->
                            <!-------Campo para seleccionar Indicador------------->
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="indicador" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;">*</strong>Indicador:</label>
                                <?php if ($aind==1) { ?>
                                <input  type="radio" name="sltIndicador" id="sltIndicador"  value="1" checked="checked">SI
                                <input  type="radio" name="sltIndicador" id="sltIndicador" value="2">NO
                                <?php } else { ?>
                                <input  type="radio" name="sltIndicador" id="sltIndicador"  value="1">SI
                                <input  type="radio" name="sltIndicador" id="sltIndicador" value="2" checked="checked">NO
                                <?php } ?>
                            </div>
                            <!-------Fin Campo para seleccionar Indicador------------->
                            <!------------------------- Consulta para llenar campo Tarifa-->
                            <?php 
                                $tf = "SELECT t.id_unico,tt.id_unico,tt.nombre FROM `gp_tarifa` t
                                        LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa=tt.id_unico WHERE tt.id_unico!=$tptid";
                                $tar = $mysqli->query($tf);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Tipo Fondo:
                                </label>
                                <select name="sltTarifa" class="form-control" id="sltTarifa" title="Seleccione Tarifa" style="height: 30px" required>
                                <option value="<?php echo $tid?>"><?php echo $tnom?></option>                

                                <?php 
                                    while ($filaT = mysqli_fetch_row($tar)) { ?>                   
                                    <option value="<?php echo $filaT[0];?>"><?php echo $filaT[2];?></option>
                                <?php
                                }
                                ?>
                                </select>   
                            </div>
                            <!------------------------- Fin Consulta para llenar Tarifa-->
                            <!------------------------- Consulta para llenar campo Predio-->
                            <?php 
                                $pr = "SELECT id_unico, nombre FROM gp_predio1 where id_unico != $apre";
                                $pre = $mysqli->query($pr);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                <strong class="obligado">*</strong>Predio:
                                </label>
                                <select name="sltPredio" class="form-control" id="sltPredio" title="Seleccione Predio" style="height: 30px" required>
                                <option value="<?php echo $pid?>"><?php echo $pnom?></option>                
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