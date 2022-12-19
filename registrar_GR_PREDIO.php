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
   <title>Registrar Predio</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Predio</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarPredioPJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <!------------------------- Campo para llenar Nombre-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Nombre:
                            </label>
                            <input type="text" name="txtNombre" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el Nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" >
                        </div>
                        <!----------Fin Nombre-->
                            <!------------------------- Campo para llenar Año Creación-->
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Año Creación:
                            </label>
                            <input type="text" name="txtAnio" id="txtAnio" class="form-control" maxlength="4" title="Ingrese el año de creación" onkeypress="return txtValida(event,'num')" placeholder="Año Creación">
                           </div>
                        <!----------Fin Año Creación-->
                            <!------------------------- Campo para llenar Código IGAC-->
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Código IGAC:
                            </label>
                            <input type="text" name="txtCodigo" id="txtCodigo" class="form-control" maxlength="50" title="Ingrese el código IGAC" onkeypress="return txtValida(event,'num_car')" placeholder="Código IGAC">
                           </div>
                        <!----------Fin Código IGAC-->
                        <!------------------------- Campo para llenar Participación-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Participación:
                            </label>
                            <input required="required" type="text" name="txtParticipacion" id="txtParticipacion" class="form-control" maxlength="5" title="Ingrese el porcentaje de Participación" onkeypress="return txtValida(event,'decimales')" placeholder="Participación">
                        </div>
                        <!----------Fin Participación-->
                        <!------------------------- Campo para llenar Principal-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Principal:
                            </label>
                                <input  type="radio" name="sltPrincipal" id="sltPrincipal" value="1" checked>SI
                                <input  type="radio" name="sltPrincipal" id="sltPrincipal" value="2" >NO
                        </div>
                        <!----------Fin Principal-->
                        <!------------------------- Consulta para llenar campo Estado Predio-->
                            <?php 
                            $es = "SELECT id_unico, nombre 
                                FROM gr_estado_predio ORDER BY id_unico ASC";
                            $est = $mysqli->query($es);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estado:
                            </label>
                            <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                            <option value="">Tipo</option>
                                <?php 
                                while ($filaE = mysqli_fetch_row($est)) { ?>
                                <option value="<?php echo $filaE[0]?>"><?php echo $filaE[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Estado Predio-->
                        <!------------------------- Consulta para llenar campo Estrato Predio-->
                            <?php 
                            $ep = "SELECT id_unico, nombre 
                                FROM gr_estrato_predio ORDER BY id_unico ASC";
                            $estr = $mysqli->query($ep);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estrato:
                            </label>
                            <select name="sltEstrato" class="form-control" id="sltEstrato" title="Seleccione Estrato" style="height: 30px">
                            <option value="">Estrato</option>
                                <?php 
                                while ($filaES = mysqli_fetch_row($estr)) { ?>
                                <option value="<?php echo $filaES[0]?>"><?php echo $filaES[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Estado Predio-->
                        <!------------------------- Consulta para llenar campo Predio Asociado-->
                            <?php 
                            $pr = "SELECT 
                                            id_unico,
                                            nombre
                                        FROM gp_predio1 p
                                    ORDER BY id_unico ASC";
                            $pre = $mysqli->query($pr);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Predio Asociado:
                            </label>
                            <select name="sltPredioA" class="form-control" id="sltPredioA" title="Seleccione Predio Asociado" style="height: 30px">
                            <option value="">Predio Asociado</option>
                                <?php 
                                while ($filaPA = mysqli_fetch_row($pre)) { ?>
                                <option value="<?php echo $filaPA[0]?>"><?php echo $filaPA[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Predio Asociado-->
                        <!------------------------- Consulta para llenar campo Predio Asociado-->
                            <?php 
                            $ter = "SELECT DISTINCT     id_unico,
                                                        CONCAT(nombreuno,' ',nombredos,' ',apellidouno,' ',apellidodos)
                                    FROM gf_tercero WHERE nombreuno IS NOT NULL ORDER BY id_unico";
                            $terc = $mysqli->query($ter);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tercero:
                            </label>
                            <select required="required" name="sltTercero" class="form-control" id="sltTercero" title="Seleccione Tercero" style="height: 30px">
                            <option value="">Tercero</option>
                                <?php 
                                while ($filaTER = mysqli_fetch_row($terc)) { ?>
                                <option value="<?php echo $filaTER[0]?>"><?php echo $filaTER[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Predio Asociado-->
                        
                        
                        
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