<?php

/* 
 * ************
 * ***Autor*****
 * **DANIEL.NC***
 * ***************
 */

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();

 $id = $_GET["id"];
 $queryCond = "SELECT   a.id_unico,
                        a.valor,
                        a.anio,
                        a.tipoarea,
                        ta.id_unico,
                        ta.nombre,
                        a.unidadarea,
                        ua.id_unico,
                        ua.nombre,
                        a.motivocambioarea,
                        mca.id_unico,
                        mca.nombre,
                        a.predio,
                        p.id_unico,
                        p.nombre
    FROM gr_area a
    LEFT JOIN gr_tipo_area ta           ON a.tipoarea  = ta.id_unico
    LEFT JOIN gr_unidad_area ua         ON a.unidadarea = ua.id_unico
    LEFT JOIN gr_motivo_cambio_area mca ON a.motivocambioarea = mca.id_unico
    LEFT JOIN gp_predio1 p              ON a.predio = p.id_unico
    WHERE md5(a.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
 
 
 
$dat1=date_create($row[2]); 
$fec1 = date_format($dat1,"d/m/Y");
$dat2=date_create($row[3]); 
$fec2 = date_format($dat1,"d/m/Y");

require_once './head.php';
?>
<title>Modificar Área</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Excensiones Predio</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarAreaPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              <!------------------------- Campo para llenar Valor-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Valor:
                            </label>
                            <input required="required" type="text" value="<?php echo $row[1]?>" name="txtValor" id="txtValor" class="form-control" maxlength="10" title="Ingrese el Valor" onkeypress="return txtValida(event,'num')" placeholder="Valor" >
                        </div>
                        <!----------Fin Valor-->
                            <!------------------------- Campo para llenar Año-->
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Año:
                            </label>
                            <input required="required" type="text" name="txtAnio" value="<?php echo $row[2]?>" id="txtAnio" class="form-control" maxlength="4" title="Ingrese el Año" onkeypress="return txtValida(event,'num')" placeholder="Año">
                           </div>
                        <!----------Fin Año-->
                        <!------------------------- Consulta para llenar campo Tipo Área-->
                            <?php 
                            $ti = "SELECT id_unico, nombre 
                                FROM gr_tipo_area WHERE id_unico != $row[4] ORDER BY id_unico ASC";
                            $tip = $mysqli->query($ti);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tipo:
                            </label>
                            <select required="required" name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo" style="height: 30px">
                            <option value="<?php echo $row[4]?>"><?php echo $row[5]?></option>
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
                                FROM gr_unidad_area 
                                WHERE id_unico != $row[6] ORDER BY id_unico ASC";
                            $uni = $mysqli->query($un);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Unidad:
                            </label>
                            <select name="sltUnidad" required="required" class="form-control" id="sltUnidad" title="Seleccione Unidad" style="height: 30px">
                            <option value="<?php echo $row[7]?>"><?php echo $row[8]?></option>
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
                            if($row[9]=="")
                            $mo = "SELECT id_unico, nombre 
                                FROM gr_motivo_cambio_area ORDER BY id_unico ASC";
                            else
                            $mo = "SELECT id_unico, nombre 
                                FROM gr_motivo_cambio_area 
                                WHERE id_unico != $row[9] ORDER BY id_unico ASC";
                            
                            $mot = $mysqli->query($mo);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Motivo Cambio:
                            </label>
                            <select name="sltMotivo" class="form-control" id="sltMotivo" title="Seleccione Motivo" style="height: 30px">
                            <option value="<?php echo $row[10]?>"><?php echo $row[11]?></option>
                                <?php 
                                while ($filaM = mysqli_fetch_row($mot)) { ?>
                                <option value="<?php echo $filaM[0]?>"><?php echo $filaM[1]?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Unidad Área-->                        
                        <!------------------------- Consulta para llenar campo Predio-->
                            <?php 
                            $pr = "SELECT id_unico, nombre 
                                FROM gp_predio1 
                                WHERE id_unico != $row[12] ORDER BY id_unico ASC";
                            $pre = $mysqli->query($pr);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Predio:
                            </label>
                            <select name="sltPredio" required="required" class="form-control" id="sltPredio" title="Seleccione Predio" style="height: 30px">
                            <option value="<?php echo $row[13]?>"><?php echo $row[14]?></option>
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