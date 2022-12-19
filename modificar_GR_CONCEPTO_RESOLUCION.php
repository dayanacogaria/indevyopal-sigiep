<?php

################ MODIFICACIONES ####################
#04/06/2017    | Anderson Alarcon | Cambie la consulta de los selectcs 'concepto resolucion'
####################################################

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
 $id = $_GET["id"];
 $queryCond = "
    SELECT cr.id_unico,cr.valoranterior,cr.valoractual,
             rp.id_unico AS id_unico_resolucion_predio,p.codigo_catastral,r.observaciones,tcp.id_unico AS id_unico_tipo_concepto_predial,tcp.nombre,tm.id_unico AS id_unico_tipo_modificacion,tm.nombre AS nombreTipoModificacion
    FROM gr_concepto_resolucion cr 
    LEFT JOIN gr_resolucion_predio rp ON cr.resolucionconcepto=rp.id_unico
    LEFT JOIN gr_resolucion r ON rp.resolucion=r.id_unico
    LEFT JOIN gp_predio1 p ON rp.predio=p.id_unico
    LEFT JOIN gr_tipo_concepto_predial tcp ON cr.tipoconcepto=tcp.id_unico
    LEFT JOIN gr_tipo_modificacion tm ON cr.tipomodificacion=tm.id_unico     

    WHERE md5(cr.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_array($resul);
 
 $crid     = $row['id_unico'];
 $crvan    = $row['valoranterior'];
 $crvac    = $row['valoractual'];
 $crid_unico_r_p    = $row['id_unico_resolucion_predio'];
 $cr_nombre_r=$row['codigo_catastral']." - ".$row['observaciones'];
 $crtcon=$row['id_unico_tipo_concepto_predial'];
 $tcnom=$row['nombre']; //nombre tipo concepto
 $crtmod=$row['id_unico_tipo_modificacion'];
 $tmnom=$row['nombreTipoModificacion'];
 
require_once './head.php';
?>
<title>Modificar Concepto Resolución</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Concepto Resolución</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarConceptoResolucionPJson.php">
                              <input type="hidden" name="id" value="<?php echo $crid ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              <!------------------------- Campo para llenar Valor Anterior-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Valor Anterior:
                            </label>
                            <input type="text" value="<?php echo $crvan?>" name="txtValant" id="txtValant" class="form-control" maxlength="200" title="Ingrese el Valor Anterior" onkeypress="return txtValida(event,'num')" placeholder="Valor Anterior">
                        </div>
                        <!----------Fin Valor Anterior-->
                            <!------------------------- Campo para llenar Valor Actual-->
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Valor Actual:
                            </label>
                            <input type="text" value="<?php echo $crvac?>" name="txtValact" id="txtValact" class="form-control" maxlength="200" title="Ingrese el Valor Actual" onkeypress="return txtValida(event,'num')" placeholder="Valor Actual">
                        </div>
                        <!----------Fin Valor Actual-->
                        <!------------------------- Consulta para llenar campo Resolución Concepto-->
                            <?php 
                            $re = "SELECT rp.id_unico,p.codigo_catastral,r.observaciones FROM `gr_resolucion_predio` rp
                                    LEFT JOIN gr_resolucion r ON rp.resolucion=r.id_unico 
                                    LEFT JOIN gp_predio1 p ON rp.predio=p.id_unico WHERE rp.id_unico != $crid_unico_r_p ORDER BY id_unico ASC";
                            $res = $mysqli->query($re);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Resolución:
                            </label>
                            <select required="required" name="sltResolucion" class="form-control" id="sltResolucion" title="Seleccione Resolución" style="height: 30px">
                            <option value="<?php echo $crid_unico_r_p?>"><?php echo $cr_nombre_r?></option>
                                <?php 
                                while ($filaR = mysqli_fetch_array($res)) { ?>
                                <option value="<?php echo $filaR['id_unico']?>"><?php echo $filaR['codigo_catastral']." - ".$filaR['observaciones']?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Resolución Concepto-->
                        <!------------------------- Consulta para llenar campo Tipo Concepto-->
                            <?php 
                            $ti = "SELECT id_unico, nombre 
                                FROM gr_tipo_concepto_predial where id_unico != $crtcon ORDER BY id_unico ASC";
                            $tip = $mysqli->query($ti);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tipo Concepto:
                            </label>
                            <select required="required" name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo Concepto" style="height: 30px">
                            <option value="<?php echo $crtcon?>"><?php echo $tcnom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_array($tip)) { ?>
                                <option value="<?php echo $filaT['id_unico']?>"><?php echo $filaT['nombre']?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Tipo Concepto-->                        
                        <!------------------------- Consulta para llenar campo Tipo Modificación-->
                            <?php 
                            $tm = "SELECT id_unico, nombre 
                                FROM gr_tipo_modificacion WHERE id_unico != $crtmod ORDER BY id_unico ASC";
                            $tmod = $mysqli->query($tm);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tipo Modificación:
                            </label>
                            <select required="required" name="sltModificacion" class="form-control" id="sltModificacion" title="Seleccione Tipo Modificación" style="height: 30px">
                            <option value="<?php echo $crtmod?>"><?php echo $tmnom?></option>
                                <?php 
                                while ($filaTM = mysqli_fetch_row($tmod)) { ?>
                                <option value="<?php echo $filaTM[0]?>"><?php echo $filaTM[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Tipo Modificación-->
                                      
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