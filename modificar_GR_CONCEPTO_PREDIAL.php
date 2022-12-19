<?php

################ MODIFICACIONES ####################
#03/06/2017 | Anderson Alarcon | Cambie query select tipo concepto predial
############################################

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
 $id = $_GET["id"];
 $queryCond = "SELECT   cp.id_unico,
                        cp.nombre,
                        cp.formula,
                        cp.rural,
                        cp.urbano,
                        cp.tipo,
                        tcp.id_unico,
                        tcp.nombre
    FROM gr_concepto_predial cp
    LEFT JOIN gr_tipo_concepto_predial tcp ON cp.tipo = tcp.id_unico
    WHERE md5(cp.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
 
 $cpid   = $row[0];
 $cpnom  = $row[1];
 $cpfor  = $row[2];
 $cpurb  = $row[3];
 $cprur  = $row[4];
 $cptip  = $row[5];
 $tcpid  = $row[6];
 $tcpnom = $row[7];

require_once './head.php';
?>
<title>Modificar Concepto Predial</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Concepto Predial</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarConceptoPredialPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                             <!------------------------- Campo para llenar Nombre-->
                        <div class="form-group" style="margin-top: 0px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Nombre:
                            </label>
                            <input type="text" name="txtNombre" id="txtNombre" class="form-control" maxlength="150" title="Ingrese el Nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $cpnom?>" placeholder="Nombre" required>
                        </div>
                        <!----------Fin Nombre-->
                            <!------------------------- Campo para llenar Fórmula-->
                        <div class="form-group" style="margin-top: -10px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Fórmula:
                            </label>
                            <input type="text" name="txtFormula" id="txtFormula" class="form-control" maxlength="500" title="Ingrese el Valor" onkeypress="return txtValida(event,'num_car')" value="<?php echo $cpfor?>" placeholder="Fórmula">
                        </div>
                        <!----------Fin Fórmula-->
                            <!------------------------- Campo para llenar Sector-->
                        <div class="form-group" style="margin-top: -15px">
                            <label class="control-label col-sm-5" style="margin-top:-8px">
                                    <strong class="obligado"></strong>Sector:
                            </label>
                            <?php if ($cpurb==2) { ?>
                        <input  type="radio" name="sltSector" id="sltSector"  value="2"> Urbano  
                        <input  type="radio" name="sltSector" id="sltSector" value="1" checked> Rural
                        <?php } else { ?>
                        <input  type="radio" name="sltSector" id="sltSector"  value="2" checked> Urbano  
                        <input  type="radio" name="sltSector" id="sltSector" value="1"> Rural
                        <?php } ?>
                    </div>   
                        <!----------Fin Sector-->
                        <!------------------------- Consulta para llenar campo Tipo-->
                            <?php 
                            $ti = "SELECT id_unico, nombre 
                                FROM gr_tipo_concepto_predial WHERE id_unico != $tcpid ORDER BY id_unico ASC";
                            $tip = $mysqli->query($ti);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Tipo:
                            </label>
                            <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo" style="height: 30px" required>
                            <option value="<?php echo $cptip?>"><?php echo $tcpnom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($tip)) { ?>
                                <option value="<?php echo $filaT[0]?>"><?php echo $filaT[1]?></option>
                                <?php
                                } #
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Tipo-->
                                      
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