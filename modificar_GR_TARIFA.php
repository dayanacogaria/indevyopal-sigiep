<?php

################ MODIFICACIONES ####################
#15/06/2017 | Anderson Alarcon | modifique el query de selects  ley 44,Tipo Base Rango,Tipo Base Ambiental,Tipo Base Cálculo 
############################################

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
 $id = $_GET["id"];
 $queryCond = "SELECT          t.id_unico,
                        t.nombre,
                        t.anio,
                        t.codigotarifa,
                        t.limiteinferior,
                        t.limitesuperior,
                        t.porcentajeincremento,
                        t.valor,
                        t.porcentajesobretasa,
                        t.porcentajeimpuestoambiental,
                        t.baseimpuesto,
                        t.baseambiental,
                        t.estrato,
                        e.id_unico,
                        e.nombre,
                        t.ley44,
                        l.id_unico,
                        l.nombre,
                        t.tipobaserango,
                        tb1.id_unico,
                        tb1.nombre,
                        t.tipobaseambiental,
                        tb2.id_unico,
                        tb2.nombre,
                        t.tipobasecalculo,
                        tb3.id_unico,
                        tb3.nombre
    FROM gr_tarifa t
    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
    LEFT JOIN gr_ley_44 l ON t.ley44 = l.id_unico
    LEFT JOIN gr_tipo_base tb1 ON t.tipobaserango = tb1.id_unico
    LEFT JOIN gr_tipo_base tb2 ON t.tipobaseambiental = tb2.id_unico
    LEFT JOIN gr_tipo_base tb3 ON t.tipobasecalculo = tb3.id_unico
    WHERE md5(t.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
 
require_once './head.php';
?>
<title>Modificar Tarifa</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tarifa</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTarifaPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              <!--##--------------------- Campo para llenar Nombre-->
                        <div class="form-group" style="margin-top: 0px;">
                            <label class="control-label col-sm-2" style="margin-left:-10px">
                                    <strong class="obligado">*</strong>Nombre:
                            </label>
                            <input required="required" type="text" value="<?php echo $row[1]?>" name="txtNombre" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el Nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                        <!----------Fin Nombre-->
                        <!--###--------------------- Campo para llenar Porcentaje Impuesto Ambiental-->
                        <div class="form-group" style="margin-top: -1   0px">
                            <label class="control-label col-sm-8"style="margin-left:10px">
                                    <strong class="obligado">*</strong>% Imp. Ambiental:
                            </label>
                            <input required="required" type="text" value="<?php echo $row[9]?>" name="txtPoramb" id="txtPoramb" class="form-control" maxlength="5" title="Ingrese el Porcentaje Impuesto Ambiental" onkeypress="return txtValida(event,'decimales')" placeholder="Porcentaje Impuesto Ambiental">
                        </div>
                        <!----------Fin Porcentaje Impuesto Ambiental-->                        
                        
                            <!--##--------------------- Campo para llenar Año-->
                        <div class="form-group" style="margin-top: -30px">
                            <label class="control-label col-sm-2">
                                    <strong class="obligado">*</strong>Año:
                            </label>
                            <input required="required" value="<?php echo $row[2]?>" type="text" name="txtAnio" id="txtAnio" class="form-control" maxlength="4" title="Ingrese el Anio" onkeypress="return txtValida(event,'num')" placeholder="Año">
                        
                        <!----------Fin Año-->
                        <!--###--------------------- Campo para llenar Base Impuesto-->
                        <div class="form-group">
                            <label class="control-label col-sm-8" style="margin-top:-30px; margin-left:5px">
                                    <strong class="obligado">*</strong>Base Impuesto:
                            </label>
                            <input style="margin-top:-20px" required="required" type="text" value="<?php echo $row[10]?>" name="txtBasimp" id="txtBasimp" class="form-control" maxlength="5" title="Ingrese Base Impuesto" onkeypress="return txtValida(event,'decimales')" placeholder="Base Impuesto">
                        </div>
                        <!----------Fin Porcentaje Base Impuesto-->
                        
                            <!--##--------------------- Campo para llenar Código Tarifa-->
                        <div class="form-group" style="margin-top: -30px">
                            <label class="control-label col-sm-2" style="margin-left:10px">
                                    <strong class="obligado">*</strong>Código:
                            </label>
                            <input required="required" value="<?php echo $row[3]?>" type="text" name="txtCodigo" id="txtCodigo" class="form-control" maxlength="50" title="Ingrese el Código" onkeypress="return txtValida(event,'num_car')" placeholder="Código">
                        <!----------Fin Código Tarifa-->
                        <!--##--------------------- Campo para llenar Base Ambiental-->
                        <div class="form-group" style="margin-top: -20px">
                            <label class="control-label col-sm-8">
                                    <strong class="obligado">*</strong>Base Ambiental:
                            </label>
                            <input required="required" type="text" name="txtBasamb" value="<?php echo $row[11]?>"id="txtBasamb" class="form-control" maxlength="5" title="Ingrese Base Ambiental" onkeypress="return txtValida(event,'decimales')" placeholder="Base Ambiental">
                        </div>
                        <!----------Fin Porcentaje Base Ambiental-->
                            <!--##--------------------- Campo para llenar Límite Inferior-->
                        <div class="form-group" style="margin-top: -30px">
                            <label class="control-label col-sm-2" style="margin-left:20px">
                                    <strong class="obligado">*</strong>Límite Inferior:
                            </label>
                            <input required="required" value="<?php echo $row[4]?>" type="text" name="txtLiminf" id="txtLiminf" class="form-control" maxlength="20" title="Ingrese el Límite Inferior" onkeypress="return txtValida(event,'num')" placeholder="Límite Inferior">
                        
                        <!----------Fin Código ´Límite Inferior-->
                        <!--##--------------------- Consulta para llenar campo Estrato-->
                            <?php 
                            $er = "SELECT id_unico, nombre 
                                FROM gp_estrato e WHERE e.id_unico!=".$row[12]." ORDER BY id_unico ASC";
                            $estr = $mysqli->query($er);
                            ?>
                        <div class="form-group" style="margin-top: -15px">
                            <label class="control-label col-sm-8" style="margin-top: -10px;margin-left:-5px" >
                                    <strong class="obligado">*</strong>Estrato:
                            </label>
                            <select style="margin-top: -10px" name="sltEstrato" class="form-control" id="sltEstrato" title="Seleccione Estrato" style="height: 30px">
                            <option value="<?php echo $row[13]?>"><?php echo $row[14]?></option>
                                <?php 
                                while ($filaEr = mysqli_fetch_row($estr)) { ?>
                                <option value="<?php echo $filaEr[0]?>"><?php echo $filaEr[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!------------------------- Campo para llenar Estrato-->
                        <!--##--------------------- Campo para llenar Límite Superior-->
                        <div class="form-group" style="margin-top: -20px">
                            <label class="control-label col-sm-2" style="margin-left:30px">
                                    <strong class="obligado">*</strong>Límite Superior:
                            </label>
                            <input required="required" value="<?php echo $row[5]?>" type="text" name="txtLimsup" id="txtLimsup" class="form-control" maxlength="20" title="Ingrese el Límite Superior" onkeypress="return txtValida(event,'num')" placeholder="Límite Superior">
                        
                        <!----------Fin Código ´Límite Superior-->
                        <!--###--------------------- Consulta para llenar campo Ley 44-->
                            <?php 
                            $le = "SELECT id_unico, nombre 
                                FROM gr_ley_44 l WHERE l.id_unico!=".$row[15]." ORDER BY id_unico ASC";
                            $ley = $mysqli->query($le);
                            ?>
                        <div class="form-group" style="margin-top: -20px;margin-left:-40px">
                            <label class="control-label col-sm-8" style="margin-top: -20px" >
                                    <strong class="obligado">*</strong>Ley 44:
                            </label>
                            <select style="margin-top: -20px" name="sltLey" class="form-control" id="sltLey" title="Seleccione Ley 44" style="height: 30px">
                                <option value="<?php echo $row[16]?>"><?php echo $row[17]?></option>
                                <?php 
                                while ($filaL = mysqli_fetch_row($ley)) { ?>
                                <option value="<?php echo $filaL[0]?>"><?php echo $filaL[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Ley 44-->
                            <!--##--------------------- Campo para llenar Porcentaje Incremento-->
                        <div class="form-group" style="margin-top: -20px">
                            <label class="control-label col-sm-2" style="margin-left:40px">
                                    <strong class="obligado">*</strong>% Incremento:
                            </label>
                            <input required="required" type="text" value="<?php echo $row[6]?>" name="txtPorinc" id="txtPorinc" class="form-control" maxlength="5" title="Ingrese el Porcentaje Incremento" onkeypress="return txtValida(event,'decimales')" placeholder="Porcentaje Incremento">
                        <!----------Fin Código Porcentaje Incremento-->
                        <!--##--------------------- Consulta para llenar campo Tipo Base Rango-->
                            <?php 
                            $tbr = "SELECT id_unico, nombre 
                                FROM gr_tipo_base tb WHERE tb.id_unico!=".$row[18]."  ORDER BY id_unico ASC";
                            $tbran = $mysqli->query($tbr);
                            ?>
                        <div class="form-group" style="margin-top: -20px">
                            <label class="control-label col-sm-8" style="margin-top: -20px;margin-left:-15px" >
                                    <strong class="obligado">*</strong>Tipo Base Rango:
                            </label>
                            <select style="margin-top: -20px" name="sltTBRan" class="form-control" id="sltTBRan" title="Seleccione Tipo Base Rango" style="height: 30px">
                            <option value="<?php echo $row[19]?>"><?php echo $row[20]?></option>
                                <?php 
                                while ($filaTBR = mysqli_fetch_row($tbran)) { ?>
                                <option value="<?php echo $filaTBR[0]?>"><?php echo $filaTBR[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Tipo Base Rango-->
                            <!--##--------------------- Campo para llenar valor-->
                        <div class="form-group" style="margin-top: -20px">
                            <label class="control-label col-sm-2" style="margin-left:50px">
                                    <strong class="obligado">*</strong>Valor:
                            </label>
                            <input required="required" type="text" value="<?php echo $row[7]?>" name="txtValor" id="txtValor" class="form-control" maxlength="20" title="Ingrese el Valor" onkeypress="return txtValida(event,'num')" placeholder="Valor">
                        
                        <!----------Fin Valor-->
                        <!------------------------- Consulta para llenar campo Tipo Base Ambiental-->
                            <?php 
                            $tba = "SELECT id_unico, nombre 
                                FROM gr_tipo_base tba WHERE tba.id_unico!=".$row[21]." ORDER BY id_unico ASC";
                            $tbamb = $mysqli->query($tba);
                            ?>
                        <div class="form-group" style="margin-top: -20px">
                            <label class="control-label col-sm-8" style="margin-top: -20px;margin-left:-20px" >
                                    <strong class="obligado">*</strong>Tipo Base Ambiental:
                            </label>
                            <select style="margin-top: -20px" name="sltTBAmb" class="form-control" id="sltTBAmb" title="Seleccione Tipo Base Ambiental" style="height: 30px">
                            <option value="<?php echo $row[22]?>"><?php echo $row[23]?></option>
                                <?php 
                                while ($filaTBA = mysqli_fetch_row($tbamb)) { ?>
                                <option value="<?php echo $filaTBA[0]?>"><?php echo $filaTBA[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!------------------------- Campo para llenar Porcentaje Impuesto Ambiental-->
                            <!--##--------------------- Campo para llenar Porcentaje Sobretasa-->
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-2"style="margin-left:60px">
                                    <strong class="obligado">*</strong>% Sobretasa:
                            </label>
                            <input required="required" type="text" value="<?php echo $row[8]?>" name="txtPorsob" id="txtPorsob" class="form-control" maxlength="5" title="Ingrese el Porcentaje Sobretasa" onkeypress="return txtValida(event,'decimales')" placeholder="Porcentaje Sobretasa">
                        <!----------Fin Porcentaje Incremento-->                        
                        <!------------------------- Consulta para llenar campo Tipo Base Cálculo-->
                            <?php 
                            $tbc = "SELECT id_unico, nombre 
                                FROM gr_tipo_base tbc WHERE tbc.id_unico!=".$row[24]." ORDER BY id_unico ASC";
                            $tbcal = $mysqli->query($tbc);
                            ?>
                        <div class="form-group" style="margin-top: -20px">
                            <label class="control-label col-sm-8" style="margin-top: -20px; margin-left:-25px" >
                                    <strong class="obligado">*</strong>Tipo Base Cálculo:
                            </label>
                            <select style="margin-top: -20px" name="sltTBCal" class="form-control" id="sltTBCal" title="Seleccione Tipo Base Cálculo" style="height: 30px">
                            <option value="<?php echo $row[25]?>"><?php echo $row[26]?></option>
                                <?php 
                                while ($filaTBC = mysqli_fetch_row($tbcal)) { ?>
                                <option value="<?php echo $filaTBC[0]?>"><?php echo $filaTBC[1]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>                                                                      
                        </div>                                                                      
                        </div>                                                                      
                        </div>                                                                      
                        </div>                                                                      
                        </div>                                                                      
                        </div>                                                                      
                        </div>
                                      
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