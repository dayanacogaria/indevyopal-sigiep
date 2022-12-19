<?php

################ MODIFICACIONES ####################
#09/06/2017 | Anderson Alarcon | modifique consulta de los selects tipo DI y mes   
############################################

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();

 $id = $_GET["id"];
 $queryCond = "SELECT   id.id_unico,
                        id.valor,
                        id.anio,
                        id.interes,
                        id.financiacion,
                        id.tipo,
                        td.id_unico,
                        td.nombre,
                        id.mes,
                        m.id_unico,
                        m.mes
    FROM gr_interes_descuento id
    LEFT JOIN gr_tipo_di td    ON id.tipo  = td.id_unico
    LEFT JOIN gf_mes m         ON id.mes = m.id_unico
    WHERE md5(id.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
require_once './head.php';
?>
<title>Modificar Interés Descuento</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Interés Descuento</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarInteresDescuentoPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              <!------------------------- Campo para llenar Año-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Año:
                            </label>
                            <input required="required" value="<?php echo $row[2]?>" type="text" name="txtAnio" id="txtAnio" class="form-control" maxlength="4" title="Ingrese el Año" onkeypress="return txtValida(event,'num')" placeholder="Año" >
                        </div>
                        <!----------Fin Año-->
                            <!------------------------- Campo para llenar Valor-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Valor:
                            </label>
                            <input required="required" type="text" value="<?php echo $row[1]?>" name="txtValor" id="txtValor" class="form-control" maxlength="18" title="Ingrese el Valor" onkeypress="return txtValida(event,'dec')" placeholder="Valor">
                        </div>
                        <!----------Fin Valor-->
                        <!------------------------- Campo para llenar Interés-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Interés:
                            </label>
                            <?php if($row[3]==1)
                            {?>
                            <input  type="radio" name="sltInteres" id="sltInteres"  value="1" checked>SI
                            <input  type="radio" name="sltInteres" id="sltInteres" value="2">NO
                            
                            <?php }
                            else
                            {?>
                            <input  type="radio" name="sltInteres" id="sltInteres"  value="1" >SI
                            <input  type="radio" name="sltInteres" id="sltInteres" value="2" checked>NO
                            <?php }?>
                        </div>
                        <!----------Fin Interés-->
                        <!------------------------- Campo para llenar Financiación-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Financiación:
                            </label>
                            <?php if($row[4]==1)
                            {?>
                            <input  type="radio" name="sltFinanciacion" id="sltFinanciacion"  value="1" checked>SI
                            <input  type="radio" name="sltFinanciacion" id="sltFinanciacion" value="2">NO
                            <?php }
                            else
                            {?>
                            <input  type="radio" name="sltFinanciacion" id="sltFinanciacion"  value="1" >SI
                            <input  type="radio" name="sltFinanciacion" id="sltFinanciacion" value="2" checked>NO
                            <?php }?>
                        </div>
                        <!----------Fin Financiación-->
                        <!------------------------- Consulta para llenar campo Tipo DI-->
                            <?php 
                            $td = "SELECT id_unico, nombre 
                                FROM gr_tipo_di 
                                WHERE id_unico != $row[5] ORDER BY id_unico ASC";
                            $tdi = $mysqli->query($td);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Tipo DI:
                            </label>
                            <select required="required" name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo DI" style="height: 30px">
                            <option value="<?php echo $row[6]?>"><?php echo $row[7]?></option>
                                <?php 
                                while ($filaTD = mysqli_fetch_row($tdi)) { ?>
                                <option value="<?php echo $filaTD[0]?>"><?php echo $filaTD[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Tipo DI-->
                        <!------------------------- Consulta para llenar campo Mes-->
                            <?php 
                            $me = "SELECT id_unico, mes 
                                FROM gf_mes 
                                WHERE id_unico != $row[8] ORDER BY id_unico ASC";
                            $mes = $mysqli->query($me);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Mes:
                            </label>
                            <select required="required" name="sltMes" class="form-control" id="sltMes" title="Seleccione Mes" style="height: 30px">
                            <option value="<?php echo $row[9]?>"><?php echo $row[10]?></option>
                                <?php 
                                while ($filaM = mysqli_fetch_row($mes)) { ?>
                                <option value="<?php echo $filaM[0]?>"><?php echo $filaM[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Mes-->
                                      
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