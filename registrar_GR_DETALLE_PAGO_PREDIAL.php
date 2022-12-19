<?php
################ MODIFICACIONES ####################
#06/06/2017     | Anderson Alarcon | modifique selects Detalle Factura Predial y Pago problemas al cargar  con espacios blancos  
############################################

require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>
   <title>Registrar Detalle Pago Predial</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Detalle Pago Predial</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetallePagoPredialPJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <!----------Campo para llenar Valor-->
                                <div class="form-group" style="margin-top: -10px;">
                                    <label for="valor" class="col-sm-5 control-label"><strong class="obligado">*</strong>Valor:</label>
                                    <input required="required" type="text" name="txtValor" id="txtValor" class="form-control" maxlength="20" title="Ingrese el valor" onkeypress="return txtValida(event,'num')" placeholder="Valor">
                                </div>                                    
                            <!----------Fin Campo Valor-->
                            <!------------------------- Consulta para llenar campo Detalle-->
                            <?php 
                            #
                            #NO SE HA ESTABLECIDO QUÉ ATRIBUTOS DEBE MOSTRAR A NIVEL DEL SELECT
                            #EN LA TABLA DETALLE FACTURA PREDIAL, PROVISIONALMENTE SE USAN ID_UNICO Y VALOR
                            #
                            $de = "SELECT   id_unico,
                                            CONCAT(id_unico,' - ',valor)
                                   FROM gr_detalle_factura_predial";
                            $det = $mysqli->query($de);
                            ?>  
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Detalle Factura Predial:
                            </label>
                            <select name="sltDetalle" class="form-control" id="sltDetalle" title="Seleccione Detalle Factura Predial" style="height: 30px">
                            <option value="">Detalle Factura Predial</option>
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
                            $pa = "SELECT id_unico, fechapago 
                                FROM gr_pago_predial";
                            $pag = $mysqli->query($pa);
                            ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Pago:
                            </label>
                            <select name="sltPago" class="form-control" id="sltDetalle" title="Seleccione Pago" style="height: 30px">
                            <option value="">Pago Predial</option>
                                <?php 
                                while ($filaP = mysqli_fetch_row($pag)) {
                                    $date=date_create($filaP[1]);
                                    $fech= date_format($date,"d/m/Y");?>
                                <option value="<?php echo $filaP[0]?>"><?php echo $fech?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
                        <!----------Fin Consulta Para llenar Factura Predial-->
                        
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