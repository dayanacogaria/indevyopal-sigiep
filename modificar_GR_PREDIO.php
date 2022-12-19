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
 $queryCond = "SELECT   p.id_unico,
                        p.nombre,
                        p.aniocreacion,
                        p.codigoigac,
                        p.participacion,
                        p.principal,
                        p.estado,
                        e.id_unico,
                        e.nombre,
                        p.estrato,
                        ep.id_unico,
                        ep.nombre,
                        p.predioaso,
                        pr.id_unico,
                        pr.nombre,
                        p.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
    FROM gp_predio1 p
    LEFT JOIN gr_estado_predio e       ON p.estado  = e.id_unico
    LEFT JOIN gr_estrato_predio ep     ON p.estrato = ep.id_unico
    LEFT JOIN gp_predio1 pr            ON p.predioaso = pr.id_unico
    LEFT JOIN gf_tercero t             ON p.tercero = t.id_unico
    WHERE p.nombre IS NOT NULL AND md5(p.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
 
#$date=date_create($rfec); 
#$fec = date_format($date,"d/m/Y");

require_once './head.php';
?>
<title>Modificar Predio</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Predio</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarPredioPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              <!------------------------- Campo para llenar Nombre-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Nombre:
                            </label>
                            <input type="text" name="txtNombre" id="txtNombre" value="<?php echo $row[1]?>" class="form-control" maxlength="100" title="Ingrese el Nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" >
                        </div>
                        <!----------Fin Nombre-->
                            <!------------------------- Campo para llenar Año Creación-->
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Año Creación:
                            </label>
                            <input type="text" name="txtAnio" id="txtAnio" value="<?php echo $row[2]?>" class="form-control" maxlength="4" title="Ingrese el año de creación" onkeypress="return txtValida(event,'num')" placeholder="Año Creación">
                           </div>
                        <!----------Fin Año Creación-->
                            <!------------------------- Campo para llenar Código IGAC-->
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Código IGAC:
                            </label>
                            <input type="text" name="txtCodigo" id="txtCodigo" class="form-control" value="<?php echo $row[3]?>" maxlength="50" title="Ingrese el código IGAC" onkeypress="return txtValida(event,'num_car')" placeholder="Código IGAC">
                           </div>
                        <!----------Fin Código IGAC-->
                        <!------------------------- Campo para llenar Participación-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Participación:
                            </label>
                            <input type="text" name="txtParticipacion" id="txtParticipacion" class="form-control" value="<?php echo $row[4]?>" maxlength="5" title="Ingrese la participación" onkeypress="return txtValida(event,'decimales')" placeholder="Participación">
                        </div>
                        <!----------Fin Participación-->
                        <!------------------------- Campo para llenar Principal-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Principal:
                            </label>
                            <?php if($row[5]==1)
                            {?>
                                <input  type="radio" name="sltPrincipal" id="sltPrincipal" value="1" checked>SI
                                <input  type="radio" name="sltPrincipal" id="sltPrincipal" value="2">NO
                            <?php
                            }
                            else
                            {?>
                                <input  type="radio" name="sltPrincipal" id="sltPrincipal" value="1">SI
                                <input  type="radio" name="sltPrincipal" id="sltPrincipal" value="2" checked>NO
                            <?php }?>
                        </div>
                        <!----------Fin Participación-->
                        <!------------------------- Consulta para llenar campo Estado Predio-->
                            <?php 
                            $es = "SELECT id_unico, nombre 
                                FROM gr_estado_predio where id_unico != $row[6] ORDER BY id_unico ASC";
                            $est = $mysqli->query($es);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estado:
                            </label>
                            <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                            <option value="<?php echo $row[7]?>"><?php echo $row[8]?></option>
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
                                FROM gr_estrato_predio where id_unico != $row[9] ORDER BY id_unico ASC";
                            $estr = $mysqli->query($ep);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estrato:
                            </label>
                            <select name="sltEstrato" class="form-control" id="sltEstrato" title="Seleccione Estrato" style="height: 30px">
                            <option value="<?php echo $row[10]?>"><?php echo $row[11]?></option>
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
                                        WHERE id_unico != $row[12] AND id_unico != $row[0]
                                    ORDER BY id_unico ASC";
                            $pre = $mysqli->query($pr);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Predio Asociado:
                            </label>
                            <select name="sltPredioA" class="form-control" id="sltPredioA" title="Seleccione Predio Asociado" style="height: 30px">
                            <option value="<?php echo $row[13]?>"><?php echo $row[14]?></option>
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
                            $ter = "SELECT DISTINCT     p.tercero,
                                                t.id_unico,
                                                CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                                        FROM gp_predio1 p
                                LEFT JOIN gf_tercero t ON p.tercero = t.id_unico
                                WHERE p.id_unico != $row[15]";
                            $terc = $mysqli->query($ter);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tercero:
                            </label>
                            <select required="required" name="sltTercero" class="form-control" id="sltTercero" title="Seleccione Tercero" style="height: 30px">
                            <option value="<?php echo $row[16]?>"><?php echo $row[17]?></option>
                                <?php 
                                while ($filaTER = mysqli_fetch_row($terc)) { ?>
                                <option value="<?php echo $filaTER[0]?>"><?php echo $filaTER[2]?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Predio Asociado-->
                                      
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