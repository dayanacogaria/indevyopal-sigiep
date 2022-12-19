<?php

################ MODIFICACIONES ####################
#11/06/2017       | Anderson Alarcon | cambie consulta de selects Responsable  
############################################

require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>
   <title>Registrar Pago Predial</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Pago Predial</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  action="json/registrarPagoPredialPJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                              <!----------Script para invocar Date Picker-->
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                    $("#datepicker").datepicker();
                                });
                                </script>
                            <!------------------------- Campo para seleccionar Fecha Pago-->
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="FechaP" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha:</label>
                                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaP" id="sltFechaP" step="1" max="2016-12-31" value="<?php echo date("Y-m-d");?>">
                            </div>
                            <!----------Fin Captura de Fecha Pago-->                            
                            <!------------------------- Consulta para llenar campo Responsable-->
                            <?php 
                            $rs = "SELECT   t.id_unico,						
                                            CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                                FROM gf_perfil_tercero pt
                                LEFT JOIN gf_tercero t ON pt.tercero = t.id_unico
                                WHERE pt.perfil = '2'";
                            $res = $mysqli->query($rs);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Responsable:
                            </label>
                            <select name="sltResponsable" class="form-control" id="sltResponsable" title="Seleccione Responsable" style="height: 30px" >
                            <option value="">Responsable</option>
                                <?php 
                                while ($filaR = mysqli_fetch_row($res)) { ?>
                                <option value="<?php echo $filaR[0];?>"><?php echo ucwords(mb_strtolower($filaR[1] )); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Empleado-->
                        <!------------------------- Consulta para llenar campo Banco-->
                        <?php 
                        $ban = "SELECT          pt.perfil,
                                                pt.tercero,
                                                t.razonsocial,
                                                p.id_unico,
                                                p.nombre
                            FROM gf_perfil_tercero pt 
                            LEFT JOIN gf_tercero t  ON pt.tercero = t.id_unico
                            LEFT JOIN gf_perfil p ON pt.perfil = p.id_unico
                            WHERE pt.perfil = 12";
                        $banco = $mysqli->query($ban);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Banco:
                            </label>
                            <select name="sltBanco" class="form-control" id="sltBanco" title="Seleccione Banco" style="height: 30px">
                            <option value="">Banco</option>
                                <?php 
                                while ($filaB = mysqli_fetch_row($banco)) { ?>
                                <option value="<?php echo $filaB[1];?>"><?php echo $filaB[2]; ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Banco-->
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