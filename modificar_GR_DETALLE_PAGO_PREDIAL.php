<?php

################ MODIFICACIONES ####################
#06/06/2017     | Anderson Alarcon | cambie select de pago
#06/06/2017     | Anderson Alarcon | modifique selects Detalle Factura Predial y Pago problemas al cargar  con espacios blancos  
############################################

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
 $id = $_GET["id"];
 $queryCond = "SELECT                       dpp.id_unico, 
                                            dpp.valor,
                                            dpp.detallefactura,
                                            dfp.id_unico,
                                            CONCAT(dfp.id_unico,' - ',dfp.valor),
                                            dpp.pago,
                                            pp.id_unico,
                                            CONCAT(pp.id_unico,' - ',pp.fechapago),
                                            pp.fechapago AS fechaPagoOpcionElegidaSelect
                                   FROM gr_detalle_pago_predial dpp
                                   LEFT JOIN gr_detalle_factura_predial dfp ON dpp.detallefactura = dfp.id_unico
                                   LEFT JOIN gr_pago_predial pp             ON dpp.pago = pp.id_unico
    WHERE md5(dpp.id_unico) = '$id'"; 
 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
 
 $dpid   = $row[0];
 $dpval  = $row[1];
 $dpdet  = $row[2];
 $dfpid  = $row[3];
 echo "$dfpid";
 $dfpdes = $row[4];
 $dppag  = $row[5];
 $pid    = $row[6];
 $pfec   = $row[7];
 $pfecSelect   = $row[8];
 

require_once './head.php';
?>
<title>Modificar Detalle Pago Predial</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Detalle Pago Predial</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarDetallePagoPredialPJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                             <!----------Campo para llenar Valor-->
                                <div class="form-group" style="margin-top: -10px;">
                                    <label for="valor" class="col-sm-5 control-label"><strong class="obligado">*</strong>Valor:</label>
                                    <input required="required" type="text" name="txtValor" value="<?php echo $dpval?>" id="txtValor" class="form-control" maxlength="20" title="Ingrese el valor" onkeypress="return txtValida(event,'num')" placeholder="Valor">
                                </div>                                    
                            <!----------Fin Campo Valor-->
                            <!------------------------- Consulta para llenar campo Detalle-->
                            <?php 
                            #
                            #NO SE HA ESTABLECIDO QUÉ ATRIBUTOS DEBE MOSTRAR A NIVEL DEL SELECT
                            #EN LA TABLA DETALLE FACTURA PREDIAL, PROVISIONALMENTE SE USAN ID_UNICO Y VALOR
                            #
                            if($dpdet=="")
                            {
                                $de = "SELECT   id_unico,
                                            CONCAT(id_unico,' - ',valor)
                                   FROM gr_detalle_factura_predial";
                            }
                            else
                            {
                            $de = "SELECT   id_unico,
                                            CONCAT(id_unico,' - ',valor)
                                   FROM gr_detalle_factura_predial where id_unico != $dpdet";
                            }
                            $det = $mysqli->query($de);
                            ?>  
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Detalle Factura Predial:
                            </label>
                            <select name="sltDetalle" class="form-control" id="sltDetalle" title="Seleccione Detalle Factura Predial" style="height: 30px">
                            <option value="<?php echo $dfpid; ?>"><?php echo $dfpdes?></option>
                                <?php 
                                while ($filaD = mysqli_fetch_row($det)) { ?>
                                <option value="<?php echo $filaD[0];?>"><?php echo ($filaD[1]); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Factura Predial-->
                        <!------------------------- Consulta para llenar campo Pago Predial-->
                            <?php
                            if($dppag=="")
                            $pa = "SELECT id_unico, fechapago 
                                FROM gr_pago_predial";
                            else
                            $pa = "SELECT id_unico, fechapago 
                                FROM gr_pago_predial where id_unico != $dppag";                            
                            
                            $pag = $mysqli->query($pa);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Pago:
                            </label>
                            <select name="sltPago" class="form-control" id="sltDetalle" title="Seleccione Pago" style="height: 30px">
                            
                                <?php
                                    $date1=date_create($pfecSelect);
                                    $fech1= date_format($date1,"d/m/Y")
                                ?>
                                <option value="<?php echo $pid?>"><?php echo $pid.' ('.$fech1.')' ?></option>
                                <?php 
                                while ($filaP = mysqli_fetch_row($pag)) {
                                    $date=date_create($filaP[1]);
                                    $fech= date_format($date,"d/m/Y");?>
                                <option value="<?php echo $filaP[0]?>"><?php echo $filaP[0].' ('.$fech.')'?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Factura Predial-->
                                      
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