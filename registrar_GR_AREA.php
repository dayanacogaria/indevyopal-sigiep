<?php

/* 
 * ************
 * ***Autor*****
 * **DANIEL.NC***
 * ***************
 */

require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>
   <title>Registrar Área</title>
    </head>
    <body>        
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Área</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarAreaPJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <!------------------------- Campo para llenar Valor-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Valor:
                            </label>
                            <input required="required" type="text" name="txtValor" id="txtValor" class="form-control" maxlength="10" title="Ingrese el Valor" onkeypress="return txtValida(event,'num')" placeholder="Valor" >
                        </div>
                        <!----------Fin Valor-->
                            <!------------------------- Campo para llenar Año-->
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Año:
                            </label>
                            <input required="required" type="text" name="txtAnio" id="txtAnio" class="form-control" maxlength="4" title="Ingrese el Año" onkeypress="return txtValida(event,'num')" placeholder="Año">
                           </div>
                        <!----------Fin Año-->
                        <!------------------------- Consulta para llenar campo Tipo Área-->
                            <?php 
                            $ti = "SELECT id_unico, nombre 
                                FROM gr_tipo_area ORDER BY id_unico ASC";
                            $tip = $mysqli->query($ti);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tipo:
                            </label>
                            <select required="required" name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo" style="height: 30px">
                            <option value="">Tipo</option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($tip)) { ?>
                                <option value="<?php echo $filaT[0]?>"><?php echo $filaT[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Tipo Área-->
                        <!------------------------- Consulta para llenar campo Unidad Área-->
                            <?php 
                            $un = "SELECT id_unico, nombre 
                                FROM gr_unidad_area ORDER BY id_unico ASC";
                            $uni = $mysqli->query($un);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Unidad:
                            </label>
                            <select name="sltUnidad" required="required" class="form-control" id="sltUnidad" title="Seleccione Unidad" style="height: 30px">
                            <option value="">Unidad</option>
                                <?php 
                                while ($filaU = mysqli_fetch_row($uni)) { ?>
                                <option value="<?php echo $filaU[0]?>"><?php echo $filaU[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Unidad Área-->
                        <!------------------------- Consulta para llenar campo Motivo Cambio Área-->
                            <?php 
                            $mo = "SELECT id_unico, nombre 
                                FROM gr_motivo_cambio_area ORDER BY id_unico ASC";
                            $mot = $mysqli->query($mo);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Motivo Cambio:
                            </label>
                            <select name="sltMotivo" class="form-control" id="sltMotivo" title="Seleccione Motivo" style="height: 30px">
                            <option value="">Motivo</option>
                                <?php 
                                while ($filaM = mysqli_fetch_row($mot)) { ?>
                                <option value="<?php echo $filaM[0]?>"><?php echo $filaM[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Unidad Área-->                        
                        <!------------------------- Consulta para llenar campo Predio-->
                            <?php 
                            $pr = "SELECT id_unico, nombre 
                                FROM gp_predio1 ORDER BY id_unico ASC";
                            $pre = $mysqli->query($pr);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Predio:
                            </label>
                            <select name="sltPredio" required="required" class="form-control" id="sltPredio" title="Seleccione Predio" style="height: 30px">
                            <option value="">Predio</option>
                                <?php 
                                while ($filaP = mysqli_fetch_row($pre)) { ?>
                                <option value="<?php echo $filaP[0]?>"><?php echo $filaP[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Predio-->                        
                        
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