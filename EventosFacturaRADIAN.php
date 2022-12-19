<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
  $query = "SELECT fac.id_unico, CONCAT(fac.prefijo_factura, ' - ', fac.numero_factura), 
    IF(CONCAT_WS(' ',
          tr.nombreuno,
          tr.nombredos,
          tr.apellidouno,
          tr.apellidodos) 
          IS NULL OR CONCAT_WS(' ',
          tr.nombreuno,
          tr.nombredos,
          tr.apellidouno,
          tr.apellidodos) = '',
          (tr.razonsocial),
          CONCAT_WS(' ',
          tr.nombreuno,
          tr.nombredos,
          tr.apellidouno,
          tr.apellidodos)) AS NOMBRE, fac.fecha_generacion,fac.valor_factura,
          fac.acuse_factura,fac.cude_acuse_factura,
          fac.recibo_factura,fac.cude_recibo_factura,fac.aceptacion_factura,fac.cude_aceptacion_factura
  FROM gf_factura_compra  fac 
  LEFT JOIN gf_tercero tr ON tr.id_unico = fac.emisor_factura
  WHERE  fac.cufe_factura IS NOT NULL AND fac.parametrizacionanno = ".$_SESSION['anno']."
  GROUP BY fac.id_unico
  ORDER BY fac.numero_factura"; 


$resultado = $mysqli->query($query);
?>
  <title>Eventos Generados Factura</title>
  <link href="css/select/select2.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once ('menu.php'); ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Eventos Generados Factura Electrónica</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                <thead>
                        <tr>
                            <td class="cabeza" style="display: none;">Identificador</td>
                            <td class="cabeza" width="800%" align="center"><strong>Eventos Factura</strong></td>
                            <td class="cabeza"><strong>Nº Factura</strong></td>
                            <td class="cabeza"><strong>Proveedor</strong></td>
                            <td class="cabeza"><strong>Fecha</strong></td>
                            <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                        </tr>
                        <tr>
                            <th class="cabeza" style="display: none;">Identificador</th>
                            <th class="cabeza" width="800%"></th>
                            <th class="cabeza"><strong>Nº Factura</strong></th>
                            <th class="cabeza"><strong>Proveedor</strong></th>
                            <th class="cabeza"><strong>Fecha</strong></th>
                            <th class="cabeza"><strong>Valor Total Ajustado</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_row($resultado)):?>
                          <tr>
                            <td class="campos" style="display: none;width:40%" ></td>
                          
                            <td>
                            <input type="hidden" name="id_ck" id="id_ck" value="<?php echo $row[0]; ?>"/>
                         
                            <?php if ($row[5]==NULL && $row[6]==NULL ) {?>
                             <div class="container" style="width:200px">
                              <input class="form-check-input" type="checkbox" value="" title="Evento 1" id="ckAcuse<?php echo $row[0];?>" onclick="return selected(<?php echo $row[0];?>)"> <strong>Acuse de recibo</strong>
                                   <a target="_blank" class="campos btn btn-primary" style="display: none"  id="envio<?php echo $row[0];?>" onclick="return envioAcuse(<?php echo $row[0];?>)">
                                   <i title="Enviar Evento 1" class="glyphicon glyphicon-send"></i>
                                   </a> 
                              </div> 
                            <?php  }?>
                            <?php if ($row[5]!=NULL && $row[6]!=NULL ) {
                              if ($row[7]==NULL && $row[8]==NULL) {
                              ?>
                              <div class="container" style="width:200px;" >
                              <input class="form-check-input" type="checkbox" value="" title="Evento 2" id="ckRecibo<?php echo $row[0];?>" onclick="return selected(<?php echo $row[0];?>)"> <strong>Recibo del bien</strong>
                                   <a target="_blank" class="campos btn btn-primary" style="display: none"  id="envioRec<?php echo $row[0];?>" onclick="return envioRecibo(<?php echo $row[0];?>)">
                                   <i title="Enviar Evento 2" class="glyphicon glyphicon-send"></i>
                                   </a> 
                              </div> 
                            <?php  }
                              }?>

                            <?php if ($row[7]!=NULL && $row[8]!=NULL ) {
                              if ($row[9]==NULL && $row[10]==NULL) {
                                ?>
                              <div class="container" style="width:200px;" >
                              <input class="form-check-input" type="checkbox" value="" title="Evento 3" id="ckAcepta<?php echo $row[0];?>" onclick="return selected(<?php echo $row[0];?>)"><strong> Aceptación/Reclamo </strong>
                                   <a target="_blank" class="campos btn btn-primary" style="display: none"  id="envioAc<?php echo $row[0];?>"  onclick="return validarEvento3(<?php echo $row[0];?>)">
                                   <i title="Enviar Evento 3" class="glyphicon glyphicon-send"></i>
                                   </a> 
                              </div> 
                            <?php  }
                             }?>

                            <?php if ($row[9]!=NULL && $row[10]!=NULL ) {
                                ?>
                              <div class="container" style="width:200px;" >
                              <a class="campos  btn-success"  id="com" onclick="return eventosFin(<?php echo $row[0];?>)">
                                   <i class="glyphicon glyphicon-ok"></i>
                              </a> 
                              <strong>Eventos Completados</strong>
                                  
                              </div> 
                            <?php  }?>


                              <div class="form-check" class="col-sm-2" style="display: none" >
                               <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">Recibo del bien
                              
                              </div>  
                              <div class="form-check" class="col-sm-2 " style="display: none">
                               <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">Aceptación/Reclamo 
                              
                              </div> 
                            </td>
                            <td class="campos" ><?=$row[1]?></td>
                            <td class="campos" ><?=$row[2]?></td>
                            <td class="campos" ><?=date('d/m/Y', strtotime($row[3]))?></td>
                            <td class="campos" ><?="$ ".number_format($row[4], 2)?></td>
                          </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal Acuse -->
<div class="modal fade" id="modalAcuse" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Acuse de recibo de la Factura Electrónica de venta</h4>
        </div>
      
        <div class="modal-body" style="margin-top: 8px">
        <b><p style="text-align:left;">Datos de quien recibe:</p></b>
        <br>
        <form id="form" name="form"  method="POST" enctype="multipart/form-data"  action="consultasFacturacion/AcuseFactura.php">
        <div class="form-group">
        <input type="hidden" name="id_fac" id="id_fac"/>
        <input type="hidden" name="tipoEvento" id="tipoEvento" value="1"/>
          <label for="sltTipoId" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Tipo Identificación:</label>
          <div class="col-sm-3 col-md-3 col-lg-3">
               <select name="sltTipoId" id="sltTipoId" title="Seleccione Tipo Identificación" style="width:110%;" class=" form-control select2_single" >
                <?php
                    $sql3 = "SELECT id_unico, nombre,sigla FROM gf_tipo_identificacion 
                             ORDER BY Nombre ASC";
                    $result3 = $mysqli->query($sql3);
                    while ($fila3 = mysqli_fetch_row($result3)) {
                        echo '<option value="'.$fila3[0].'">'.ucwords(mb_strtolower($fila3[1])).'-'.$fila3[2].'</option>';
                    }
                  
                ?>
               </select>
               </div>
               <label for="sltNumeroI" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Nro Identificación:</label>
               <div class="col-sm-4 col-md-4 col-lg-2">
                 <input type="number" name="txtNumeroI" id="txtNumeroI" class="form-control" onchange="return buscarTercero()"style="cursor:pointer; padding:2px;width:220%;" title="Número de Identificación" placeholder="Nro de Identificación" required/>
               </div>
               <br>
               <br>
               <br>
               <br>
              <label for="txtPrimerN" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Primer Nombre:</label>
              <div class="col-sm-3 col-md-3 col-lg-3">
                  <input type="text" name="txtPrimerN" id="txtPrimerN" class="form-control" style="cursor:pointer; padding:2px;width:110%;" title="Primer Nombre" placeholder="Primer Nombre" required/>
              </div>
             <label for="txtSegundoN" class="control-label col-sm-2 col-md-2 col-lg-2 text-right">Segundo Nombre:</label>
              <div class="col-sm-4 col-md-4 col-lg-2">
                <input type="text" name="txtSegundoN" id="txtSegundoN" class="form-control" style="cursor:pointer; padding:2px;width:220%;" title="Segundo Nombre" placeholder="Segundo Nombre"/>
              </div>
              <br>
              <br>
              <br>
              <br>
              <label for="txtPrimerA" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Primer Apellido:</label>
              <div class="col-sm-3 col-md-3 col-lg-3">
                <input type="text" name="txtPrimerA" id="txtPrimerA" class="form-control" style="cursor:pointer; padding:2px;width:110%;" title="Primer Apellido" placeholder="Primer Apellido" required/>
              </div>
              <label for="txtSegundoAR" class="control-label col-sm-2 col-md-2 col-lg-2 text-right">Segundo Apellido:</label>
              <div class="col-sm-4 col-md-4 col-lg-2">
                 <input type="text" name="txtSegundoA" id="txtSegundoA" class="form-control" style="cursor:pointer; padding:2px;width:220%;" title="Segundo Apellido" placeholder="Segundo Apellido"/>
              </div>
        
              <br>
              <br>
              <br>
              <br>
              <label for="txtCargo" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Cargo/Rol:</label>
              <div class="col-sm-3 col-md-3 col-lg-3">
                <input type="text" name="txtCargo" id="txtCargo" class="form-control" style="cursor:pointer; padding:2px;width:110%;" title="Cargo" placeholder="Cargo" required/>
              </div>
              <label for="txtArea" class="control-label col-sm-2 col-md-2 col-lg-2 text-right">Área Dependencia:</label>
              <div class="col-sm-4 col-md-4 col-lg-2">
                 <input type="text" name="txtArea" id="txtArea" class="form-control" style="cursor:pointer; padding:2px;width:220%;" title="Área Dependencia" placeholder="Área Dependencia"/>
              </div>
              <br>
              <br>
              <br>
              <br>
              <label for="txtRazonSocial" class="control-label col-sm-2 col-md-2 col-lg-2 text-right">(Si es Nit)<br>Razón Social:</label>
              <div class="col-sm-3 col-md-3 col-lg-3">
                <input type="text" name="txtRazonSocial" id="txtRazonSocial" class="form-control" style="cursor:pointer; padding:2px;width:330%;" title="Razón social" placeholder="Razón social"/>
              </div>
              
      </div>
     
      <br>
        </div>
        <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px; text-align:rigth;" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="butt" class="btn" style="color: #000; margin-top: 2px" >Enviar</button>
        </div>
      </form>
      </div>
    </div>
  </div>

<!-- Modal Recibo -->
  <div class="modal fade" id="modalRecibo" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Recibo del bien o prestación del servicio</h4>
        </div>
      
        <div class="modal-body" style="margin-top: 8px">
        <b><p style="text-align:left;">Datos de quien recibe:</p></b>
        <br>
        <form id="form" name="form"  method="POST" enctype="multipart/form-data" action="consultasFacturacion/AcuseFactura.php">
        <div class="form-group">
        <input type="hidden" name="id_facR" id="id_facR"/>
        <input type="hidden" name="tipoEvento" id="tipoEvento" value="2"/>
          <label for="sltTipoIdR" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Tipo Identificación:</label>
          <div class="col-sm-3 col-md-3 col-lg-3">
               <select name="sltTipoIdR" id="sltTipoIdR" title="Seleccione Tipo Identificación" style="width:110%;" class=" form-control select2_single" >
                <?php
                    $sql3 = "SELECT id_unico, nombre,sigla FROM gf_tipo_identificacion 
                             ORDER BY Nombre ASC";
                    $result3 = $mysqli->query($sql3);
                    while ($fila3 = mysqli_fetch_row($result3)) {
                        echo '<option value="'.$fila3[0].'">'.ucwords(mb_strtolower($fila3[1])).'-'.$fila3[2].'</option>';
                    }
                  
                ?>
               </select>
               </div>
               <label for="sltNumeroIR" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Nro Identificación:</label>
               <div class="col-sm-4 col-md-4 col-lg-2">
                 <input type="number" name="txtNumeroIR" id="txtNumeroIR" class="form-control" onchange="return buscarTerceroR()"style="cursor:pointer; padding:2px;width:220%;" title="Número de Identificación" placeholder="Nro de Identificación" required/>
               </div>
               <br>
               <br>
               <br>
               <br>
              <label for="txtPrimerNR" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Primer Nombre:</label>
              <div class="col-sm-3 col-md-3 col-lg-3">
                  <input type="text" name="txtPrimerNR" id="txtPrimerNR" class="form-control" style="cursor:pointer; padding:2px;width:110%;" title="Primer Nombre" placeholder="Primer Nombre" required/>
              </div>
             <label for="txtSegundoNR" class="control-label col-sm-2 col-md-2 col-lg-2 text-right">Segundo Nombre:</label>
              <div class="col-sm-4 col-md-4 col-lg-2">
                <input type="text" name="txtSegundoNR" id="txtSegundoNR" class="form-control" style="cursor:pointer; padding:2px;width:220%;" title="Segundo Nombre" placeholder="Segundo Nombre"/>
              </div>
              <br>
              <br>
              <br>
              <br>
              <label for="txtPrimerAR" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Primer Apellido:</label>
              <div class="col-sm-3 col-md-3 col-lg-3">
                <input type="text" name="txtPrimerAR" id="txtPrimerAR" class="form-control" style="cursor:pointer; padding:2px;width:110%;" title="Primer Apellido" placeholder="Primer Apellido" required/>
              </div>
              <label for="txtSegundoAR" class="control-label col-sm-2 col-md-2 col-lg-2 text-right">Segundo Apellido:</label>
              <div class="col-sm-4 col-md-4 col-lg-2">
                 <input type="text" name="txtSegundoAR" id="txtSegundoAR" class="form-control" style="cursor:pointer; padding:2px;width:220%;" title="Segundo Apellido" placeholder="Segundo Apellido"/>
              </div>
              <br>
              <br>
              <br>
              <br>
              <label for="txtCargoR" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><strong class="obligado">*</strong>Cargo/Rol:</label>
              <div class="col-sm-3 col-md-3 col-lg-3">
                <input type="text" name="txtCargoR" id="txtCargoR" class="form-control" style="cursor:pointer; padding:2px;width:110%;" title="Cargo" placeholder="Cargo" required/>
              </div>
              <label for="txtAreaR" class="control-label col-sm-2 col-md-2 col-lg-2 text-right">Área Dependencia:</label>
              <div class="col-sm-4 col-md-4 col-lg-2">
                 <input type="text" name="txtAreaR" id="txtAreaR" class="form-control" style="cursor:pointer; padding:2px;width:220%;" title="Área Dependencia" placeholder="Área Dependencia"/>
              </div>
              <br>
              <br>
              <br>
              <br>
              <label for="txtRazonSocialR" class="control-label col-sm-2 col-md-2 col-lg-2 text-right">(Si es Nit)<br>Razón Social:</label>
              <div class="col-sm-3 col-md-3 col-lg-3">
                <input type="text" name="txtRazonSocialR" id="txtRazonSocialR" class="form-control" style="cursor:pointer; padding:2px;width:330%;" title="Razón social" placeholder="Razón social"/>
              </div>
      </div>
     
      <br>
        </div>
        <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px; text-align:rigth;" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="butt" class="btn" style="color: #000; margin-top: 2px" >Enviar</button>
        </div>
      </form>
      </div>
    </div>
  </div>

<!-- Modal Aceptacion o rechazo seleccion -->
<div class="modal fade" id="modalValidacionEvento3" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 22; text-align: left; padding: 3px;">&nbsp;&nbsp;Aceptación expresa / Reclamo de la Factura Electrónica de venta <button type="button" id="s" class="btn" style="color: #000; margin-top: -50px; margin-left: 540px;" data-dismiss="modal"> <i title="Cerrar" class="glyphicon glyphicon-remove"></i></button></h4>
        </div>
      
        <div class="modal-body" style="margin-top: 8px">
        <b><p style="text-align:center;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;¿Acepta o rechaza Factura Electrónica?</p></b>
        <br>
        <form id="form" name="form"  method="POST" enctype="multipart/form-data" >
        <div class="form-group">
        <input type="hidden" name="id_facA" id="id_facA"/>
          <label for="sltTipoId" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"></label>
          <div class="col-sm-3 col-md-3 col-lg-4">
               <button type="button" id="aceptaF" class="btn btn-success" style="margin-top: 2px; " data-dismiss="modal">&nbsp;&nbsp;Acepto&nbsp;&nbsp;</button>
          </div>
          <label for="sltNumeroI" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"></label>
          <div class="col-sm-4 col-md-4 col-lg-2">
              <button type="button" id="rechazaF" class="btn btn-danger" style="margin-top: 2px;" data-dismiss="modal">Rechazo</button>
          </div>
          <br>
      </div>
      <br>
        </div>
        <div id="forma-modal" class="modal-footer">

        </div>
      </form>
      </div>
    </div>
  </div>


<!-- Modal Rechazo -->
<div class="modal fade" id="modalMotivoRechazo" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Motivo Rechazo</h4>
        </div>
      
        <div class="modal-body" style="margin-top: 8px">
        <form id="form" name="form"  method="POST" enctype="multipart/form-data" action="consultasFacturacion/AceptacionRechazoRADIAN.php">
        <div class="form-group">
        <input type="hidden" name="id" id="id"/>
        <input type="hidden" name="event" id="event" value="1"/>
          <label for="sltTipoIdR" class="control-label col-sm-4 col-md-4 col-lg-4 text-right"><strong class="obligado">*</strong>Concepto Rechazo:</label>
          <div class="col-sm-6 col-md-6 col-lg-6">
               <select name="sltRechazo" id="sltRechazo" title="Seleccione Concepto Rechazo" style="width:110%;" class=" form-control select2_single" >
                <option value="INCONSISTENCIAS">Documento con inconsistencias</option>
                <option value="MERCANCIA_NO_TOTAL">Mercancía no entregada totalmente</option>
                <option value="MERCANCIA_NO_PARCIAL">Mercancía no entregada parcialmente</option>
                <option value="SERVICIO_NO_PRESTADO">Servicio no prestado</option>
               </select>
               </div>
               <br>
      </div>
     
      <br>
        </div>
        <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px; text-align:rigth;" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="butt" class="btn" style="color: #000; margin-top: 2px" >Enviar</button>
        </div>
      </form>
      </div>
    </div>
  </div>

           <!-- Modal de listar Eventos.  -->
    <div class="modal fade" id="modalEventosF" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <?php
                    if ($_REQUEST['id']==NULL) {
                      $factM="N";
                    }else{
                      $factM=$_REQUEST['id'];
                    }
                    $factM = str_replace("xw3Tr", "", $factM);
                    $fact = $con->Listar("SELECT CONCAT(f.prefijo_factura,f.numero_factura) 
                                          FROM gf_factura_compra  f
                                          WHERE f.id_unico=$factM")
                    ?>
                    <h4 class="modal-title" style="font-size: 24; padding: 0px;">Eventos Factura Electrónica - <?php echo $fact[0][0]?></h4>
                </div>
                <div class="modal-body" style="margin-top: 10px">
                <div class="table-responsive" > 
              
                    <!-- Inicio de la tabla -->
                    <table id="tablaDetalleC" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                        <!-- Inicio de la cabeza de la tabla -->
                        <thead>
                            <!-- Campos para titulos de los campos -->
                            <tr>                                        
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Evento</strong></td>
                                <td class="cabeza"><strong>Estado</strong></td>
                                <td class="cabeza"><strong>Codigo Cude</strong></td>                                                  
                            </tr>
                            <!-- Campos para filtros -->
                            <tr>                                        
                                <th width="7%"></th>
                                <th class="cabeza">Evento</th>
                                <th class="cabeza">Estado</th>
                                <th class="cabeza">Codigo Cude</th>                         
                            </tr>
                            <!-- Cierre de la cabeza de la tabla -->
                        </thead>
                        <!-- Inicio del cuerpo de la tabla -->
                        <tbody>
                          <?php
                            $sql = "SELECT 'Acuse' as evento,acuse_factura,cude_acuse_factura, '1' as item
                                           FROM gf_factura_compra 
                                           WHERE id_unico=$factM";
                                $resultTemp = $mysqli->query($sql);
                                while ($rowTemp = mysqli_fetch_row($resultTemp)) { 
                                        echo "<tr>";
                                        echo "<td>".$rowTemp[3]."</td>";
                                        echo "<td>".ucwords(mb_strtolower($rowTemp[0]))."</td>";
                                        echo "<td>".ucwords(mb_strtolower($rowTemp[1]))."</td>";
                                        if($rowTemp[2]!=null){$cude="Si";}else{$cude="No";}
                                        echo "<td style='width:10%;'>".$cude."</td>";
                                        echo "</tr>";
                                }
                                $sql = "SELECT 'Recibo' as evento,recibo_factura,cude_recibo_factura,'2' as item
                                FROM gf_factura_compra 
                                WHERE id_unico=$factM";
                                $resultTemp = $mysqli->query($sql);
                                while ($rowTemp = mysqli_fetch_row($resultTemp)) { 
                                            echo "<tr>";
                                            echo "<td>".$rowTemp[3]."</td>";
                                            echo "<td>".ucwords(mb_strtolower($rowTemp[0]))."</td>";
                                            echo "<td>".ucwords(mb_strtolower($rowTemp[1]))."</td>";
                                            if($rowTemp[2]!=null){$cude="Si";}else{$cude="No";}
                                            echo "<td style='width:10%;'>".$cude."</td>";
                                            echo "</tr>";
                                    }
                                $sql = "SELECT 'Aceptación/Rechazo' as evento,aceptacion_factura,cude_aceptacion_factura,'3' as item
                                           FROM gf_factura_compra 
                                           WHERE id_unico=$factM";
                                $resultTemp = $mysqli->query($sql);
                                while ($rowTemp = mysqli_fetch_row($resultTemp)) { 
                                        echo "<tr>";
                                        echo "<td>".$rowTemp[3]."</td>";
                                        echo "<td>".ucwords(mb_strtolower($rowTemp[0]))."</td>";
                                        echo "<td>".ucwords(mb_strtolower($rowTemp[1]))."</td>";
                                        if($rowTemp[2]!=null){$cude="Si";}else{$cude="No";}
                                        echo "<td style='width:10%;'>".$cude."</td>";
                                        echo "</tr>";
                                }
                            ?>
                            <!-- Cierre del cuerpo de la tabla -->
                        </tbody>
                        <!-- Cierre de la tabla -->
                    </table>
                </div>  
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptVal" onclick="devolver()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

  <?php require_once ('footer.php'); ?>

  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script src="js/facturacion_electronica/facturacion.js"></script>
  <script src="js/select/select2.full.js"></script>
  <script type="text/javascript">
        $(document).ready(function() {
          $(".select2_single").select2({
                  allowClear: true
              });

            var i= 1;
            $('#tablaDetalleC thead th').each( function () {
                if(i != 1){ 
                    var title = $(this).text();
                    switch (i){
                    case 2:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;
                    case 3:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;
                    case 4:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;
                    case 5:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;
                    case 6:
                      $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;
                    case 7:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;            
                    case 8:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;
                    case 9:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;
                }
                i = i+1;
            }else{
                i = i+1;
            }
        });
        // DataTable
        var table = $('#tablaDetalleC').DataTable({
            "autoFill": false,
            "scrollX": false,
            "pageLength": 5,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No Existen Registros...",
                "info": "Página _PAGE_ de _PAGES_ ",
                "infoEmpty": "No existen datos",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
            },
            'columnDefs': [{
                'targets': 0,
                'searchable':false,
                'orderable':false,
                'className': 'dt-body-center'         
            }]
        });
        var i = 0;
        table.columns().every( function () {
            var that = this;
            if(i!=0){
            $( 'input', this.header() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
                i = i+1;
            }else{
                i = i+1;
            }
        } );
    } );
 </script>

  <script>
          $(document).ready(function () {
              $("#txtNumeroI").val("");
               $("#txtPrimerN").val("");
               $("#txtSegundoN").val("");
               $("#txtPrimerA").val("");
               $("#txtSegundoA").val("");
               $("#txtCargo").val("");
               $("#txtArea").val("");
               $("#txtRazonSocial").val("");
               cargartipoIde()
               $("#txtNumeroIR").val("");
               $("#txtPrimerNR").val("");
               $("#txtSegundoNR").val("");
               $("#txtPrimerAR").val("");
               $("#txtSegundoAR").val("");
               $("#txtCargoR").val("");
               $("#txtAreaR").val("");
               $("#txtRazonSocialR").val("");
               cargartipoIdeR()
               var fact="<?=$factM?>";
               if (fact==="N") {
                   fact=null;
               }else{
                 fact=fact;
               }
             
               if (fact==null || fact=="") {}else{
                $("#modalEventosF").modal('show');
               }

          });
    </script>
  <script type="text/javascript">
      facturacion.events();
     
  </script>
    <script>
          function selected(idck) {
           
           if($("#ckAcuse"+idck).is(':checked')) {     
           
             //Acuse
             $("#envio"+idck).css('display', 'block');
             $("#envio"+idck).css('width', '40px');
             $("#envio"+idck).css('margin-left', '116px');
             $("#envio"+idck).css('margin-top', '-25px');
           }else{
             $("#envio"+idck).css('display', 'none');
           }

           if($("#ckRecibo"+idck).is(':checked')) {     
           
              //Recibo
             $("#envioRec"+idck).css('display', 'block');
             $("#envioRec"+idck).css('width', '40px');
             $("#envioRec"+idck).css('margin-left', '116px');
             $("#envioRec"+idck).css('margin-top', '-25px');
           }else{
             $("#envioRec"+idck).css('display', 'none');
           }

           if($("#ckAcepta"+idck).is(':checked')) {     
          
              //Aceptacion o Reclamo
             $("#envioAc"+idck).css('display', 'block');
             $("#envioAc"+idck).css('width', '40px');
             $("#envioAc"+idck).css('margin-left', '146px');
             $("#envioAc"+idck).css('margin-top', '-25px');
           }else{
             $("#envioAc"+idck).css('display', 'none');
           }
          }

          function envioAcuse(factura) {
            $("#modalAcuse").modal('show');
            $("#id_fac").val(factura);
            $("#ver1").click(function(){
               $("#txtNumeroI").val("");
               $("#txtPrimerN").val("");
               $("#txtSegundoN").val("");
               $("#txtPrimerA").val("");
               $("#txtSegundoA").val("");
               $("#txtCargo").val("");
               $("#txtArea").val("");
               $("#txtRazonSocial").val("");
               cargartipoIde()
            });
          }

          function envioRecibo(factura) {
            $("#modalRecibo").modal('show');
            $("#id_facR").val(factura);
            $("#ver1").click(function(){
               $("#txtNumeroIR").val("");
               $("#txtPrimerNR").val("");
               $("#txtSegundoNR").val("");
               $("#txtPrimerAR").val("");
               $("#txtSegundoAR").val("");
               $("#txtCargoR").val("");
               $("#txtAreaR").val("");
               $("#txtRazonSocialR").val("");
               cargartipoIdeR()
            });
          }
          function buscarTercero() {

              var numeroidentificacion=$("#txtNumeroI").val();
             
              var form_data = {
                 case:11,
                 identificacion:numeroidentificacion
              };
               $.ajax({
                   type:'POST',
                   url: "jsonSistema/consultas.php",
                   data:form_data,
                   success: function (data, textStatus, jqXHR) {
                       resultado = JSON.parse(data);

                            var respuesta   = resultado["respuesta"];
                            if (respuesta==1) {
                                 var numeroidentificacion = resultado["numeroidentificacion"];
                                 var nombreuno   = resultado["nombreuno"];
                                 if (nombreuno==="") {}else{
                                 nombreuno=nombreuno.toLowerCase();
                                 nombreuno=nombreuno.toLowerCase();
                                 nombreuno = nombreuno[0].toUpperCase() + nombreuno.substring(1);
                                 }
                                 var nombredos   = resultado["nombredos"];
                                 if (nombredos==="") {}else{
                                    nombredos=nombredos.toLowerCase();
                                    nombredos=nombredos.toLowerCase();
                                    nombredos = nombredos[0].toUpperCase() + nombredos.substring(1);
                                 }
                                 var apellidouno = resultado["apellidouno"];
                                 if (apellidouno===""){}else{
                                  apellidouno=apellidouno.toLowerCase();
                                  apellidouno=apellidouno.toLowerCase();
                                  apellidouno = apellidouno[0].toUpperCase() + apellidouno.substring(1);
                                 }
                                 var apellidodos = resultado["apellidodos"];
                                 if (apellidodos==="") {}else{
                                  apellidodos=apellidodos.toLowerCase();
                                  apellidodos=apellidodos.toLowerCase();
                                  apellidodos = apellidodos[0].toUpperCase() + apellidodos.substring(1);
                                 }
                                 var razonsocial = resultado["razonsocial"];
                                 if (razonsocial==="") {}else{
                                  razonsocial=razonsocial.toLowerCase();
                                  razonsocial=razonsocial.toLowerCase();
                                  razonsocial = razonsocial[0].toUpperCase() + razonsocial.substring(1);
                                  nombreuno="-";
                                  nombredos ="-";
                                  apellidouno="-";
                                  apellidodos ="-";
                                 }
                                 var cargo       = resultado["cargo"];
                                 var optionId     = resultado["option"];
                              $("#txtNumeroI").val(numeroidentificacion);
                              $("#txtPrimerN").val(nombreuno);
                              $("#txtSegundoN").val(nombredos);
                              $("#txtPrimerA").val(apellidouno);
                              $("#txtSegundoA").val(apellidodos);
                              $("#txtCargo").val(cargo);
                              $("#sltTipoId").html(optionId);
                              $("#txtRazonSocial").val(razonsocial);
                            }else{
                              $("#txtPrimerN").val("");
                              $("#txtSegundoN").val("");
                              $("#txtPrimerA").val("");
                              $("#txtSegundoA").val("");
                              $("#txtCargo").val("");
                              $("#txtArea").val("");
                              $("#txtRazonSocial").val("");
                              cargartipoIde()
                            }   
                   }
               }).error(function(data, textStatus, jqXHR) {
                   console.log('data :'+data+', textStatus :'+textStatus+', jqXHR :'+jqXHR);
               });

          }
          function buscarTerceroR() {
              //var numeroidentificacion=$(numeroidentificacion).val();
              var numeroidentificacion=$("#txtNumeroIR").val();
              //console.log(numeroidentificacion+'ss');
              var form_data = {
                 case:11,
                 identificacion:numeroidentificacion
              };
               $.ajax({
                   type:'POST',
                   url: "jsonSistema/consultas.php",
                   data:form_data,
                   success: function (data, textStatus, jqXHR) {
                       resultado = JSON.parse(data);

                            var respuesta   = resultado["respuesta"];
                            if (respuesta==1) {
                           
                                 var numeroidentificacion = resultado["numeroidentificacion"];
                                 var nombreuno   = resultado["nombreuno"];
                                 if (nombreuno==="") {}else{
                                  nombreuno=nombreuno.toLowerCase();
                                  nombreuno=nombreuno.toLowerCase();
                                  nombreuno = nombreuno[0].toUpperCase() + nombreuno.substring(1);
                                 }
                                 var nombredos   = resultado["nombredos"];
                                 if (nombredos==="") {}else{
                                  nombredos=nombredos.toLowerCase();
                                  nombredos=nombredos.toLowerCase();
                                  nombredos = nombredos[0].toUpperCase() + nombredos.substring(1);
                                 }
                                 var apellidouno = resultado["apellidouno"];
                                 if (apellidouno==="") {}else{
                                  apellidouno=apellidouno.toLowerCase();
                                  apellidouno=apellidouno.toLowerCase();
                                  apellidouno = apellidouno[0].toUpperCase() + apellidouno.substring(1);
                                 }
                                 var apellidodos = resultado["apellidodos"];
                                 if (apellidodos==="") {}else{
                                  apellidodos=apellidodos.toLowerCase();
                                  apellidodos=apellidodos.toLowerCase();
                                  apellidodos = apellidodos[0].toUpperCase() + apellidodos.substring(1);
                                 }
                                 var razonsocial = resultado["razonsocial"];
                                 if (razonsocial==="") {}else{
                                  razonsocial=razonsocial.toLowerCase();
                                  razonsocial=razonsocial.toLowerCase();
                                  razonsocial = razonsocial[0].toUpperCase() + razonsocial.substring(1);
                                  nombreuno="-";
                                  nombredos ="-";
                                  apellidouno="-";
                                  apellidodos ="-";
                                 }
                                 var cargo       = resultado["cargo"];
                                 var optionId     = resultado["option"];
                                 console.log(numeroidentificacion);
                              $("#txtNumeroIR").val(numeroidentificacion);
                              $("#txtPrimerNR").val(nombreuno);
                              $("#txtSegundoNR").val(nombredos);
                              $("#txtPrimerAR").val(apellidouno);
                              $("#txtSegundoAR").val(apellidodos);
                              $("#txtCargoR").val(cargo);
                              $("#sltTipoIdR").html(optionId);
                              $("#txtRazonSocialR").val(razonsocial);
                            }else{
                              $("#txtPrimerNR").val("");
                              $("#txtSegundoNR").val("");
                              $("#txtPrimerAR").val("");
                              $("#txtSegundoAR").val("");
                              $("#txtCargoR").val("");
                              $("#txtAreaR").val("");
                              $("#txtRazonSocialR").val("");
                              cargartipoIdeR()
                            }   
                   }
               }).error(function(data, textStatus, jqXHR) {
                   console.log('data :'+data+', textStatus :'+textStatus+', jqXHR :'+jqXHR);
               });

          }
          function cargartipoIde() {
            var form_data = {
                 case:12
              };
               $.ajax({
                   type:'POST',
                   url: "jsonSistema/consultas.php",
                   data:form_data,
                   success: function (data, textStatus, jqXHR) {
                        $("#sltTipoId").html(data);
                   }
               }).error(function(data, textStatus, jqXHR) {
                   console.log('data :'+data+', textStatus :'+textStatus+', jqXHR :'+jqXHR);
               });
          }

          function cargartipoIdeR() {
            var form_data = {
                 case:12
              };
               $.ajax({
                   type:'POST',
                   url: "jsonSistema/consultas.php",
                   data:form_data,
                   success: function (data, textStatus, jqXHR) {
                        $("#sltTipoIdR").html(data);
                   }
               }).error(function(data, textStatus, jqXHR) {
                   console.log('data :'+data+', textStatus :'+textStatus+', jqXHR :'+jqXHR);
               });
          }

          function validarEvento3(factura) {
            $("#modalValidacionEvento3").modal('show');
            //$("#id_facA").val(factura);
            $("#aceptaF").click(function(){
              jsShowWindowLoad('Enviando Evento...');
              window.location = 'consultasFacturacion/AceptacionRechazoRADIAN.php?id='+factura+'xw3Tr&event=QKxuCFmUlPLn2';
            });
            $("#rechazaF").click(function(){
              $("#modalMotivoRechazo").modal('show');
              $("#id").val(factura);
            });

            
          }
          function eventosFin(factura) {
            window.location = 'EventosFacturaRADIAN.php?id='+factura+'xw3Tr';
          }
    </script>
    <script>
      function jsRemoveWindowLoad() {
          // eliminamos el div que bloquea pantalla
          $("#WindowLoad").remove(); 
      }
       
      function jsShowWindowLoad(mensaje) {
          //eliminamos si existe un div ya bloqueando
          jsRemoveWindowLoad(); 
          //si no enviamos mensaje se pondra este por defecto
          if (mensaje === undefined) mensaje = "Procesando la información<br>Espere por favor"; 
          //centrar imagen gif
          height = 20;//El div del titulo, para que se vea mas arriba (H)
          var ancho = 0;
          var alto = 0; 
          //obtenemos el ancho y alto de la ventana de nuestro navegador, compatible con todos los navegadores
          if (window.innerWidth == undefined) ancho = window.screen.width;
          else ancho = window.innerWidth;
          if (window.innerHeight == undefined) alto = window.screen.height;
          else alto = window.innerHeight; 
          //operación necesaria para centrar el div que muestra el mensaje
          var heightdivsito = alto/2 - parseInt(height)/2;//Se utiliza en el margen superior, para centrar 
         //imagen que aparece mientras nuestro div es mostrado y da apariencia de cargando
          imgCentro = "<div style='text-align:center;height:" + alto + "px;'><div  style='color:#FFFFFF;margin-top:" + heightdivsito + "px; font-size:20px;font-weight:bold;color:#1075C1'>" + mensaje + "</div><img src='img/loading.gif'/></div>"; 
              //creamos el div que bloquea grande------------------------------------------
              div = document.createElement("div");
              div.id = "WindowLoad";
              div.style.width = ancho + "px";
              div.style.height = alto + "px";        
              $("body").append(div); 
              //creamos un input text para que el foco se plasme en este y el usuario no pueda escribir en nada de atras
              input = document.createElement("input");
              input.id = "focusInput";
              input.type = "text"; 
              //asignamos el div que bloquea
              $("#WindowLoad").append(input); 
              //asignamos el foco y ocultamos el input text
              $("#focusInput").focus();
              $("#focusInput").hide(); 
              //centramos el div del texto
              $("#WindowLoad").html(imgCentro);
       
      }
  </script>
</body>
</html>


