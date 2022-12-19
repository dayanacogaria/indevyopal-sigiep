<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
 $query = "SELECT de.id_unico, de.numero,  IF(CONCAT_WS(' ',
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
           tr.apellidodos)) AS NOMBRE, de.fecha, 
           SUM(dde.valor_unitario*dde.cantidad), SUM(dde.valor_iva*dde.cantidad), SUM(dde.valor_total),tr.email,
           (SELECT CONCAT_WS(' - ',d.direccion, cd.nombre) FROM gf_direccion d LEFT JOIN gf_ciudad cd ON d.ciudad_direccion = cd.id_unico WHERE d.tercero = tr.id_unico LIMIT 1),
           (IF(tr.razonsocial IS NULL OR tr.razonsocial ='', 'modificar_TERCERO_CLIENTE_NATURAL.php?id_ter_clie_nat', 'modificar_TERCERO_CLIENTE_JURIDICA.php?id_ter_clie_jur')),
            tr.id_unico, trg.nombre,  (SELECT GROUP_CONCAT(DISTINCT rf.nombre) FROM gf_tercero_responsabilidad trs LEFT JOIN gf_responsabilidad_fiscal rf ON trs.responsabilidad = rf.id_unico WHERE trs.tercero = tr.id_unico) as responsabilidesd,
            tr.numeroidentificacion,(SELECT GROUP_CONCAT(DISTINCT rt.nombre) FROM gf_tercero_responsabilidad trs LEFT JOIN gf_responsabilidad_tributaria rt ON trs.responsabilidad_tributaria = rt.id_unico WHERE trs.tercero = tr.id_unico) as responsabilidedT,
            tr.procedencia,(SELECT tl.valor FROM gf_telefono tl LEFT JOIN gf_tercero ter ON ter.id_unico=tl.tercero WHERE ter.id_unico=tr.id_unico LIMIT 1),tii.sigla
           FROM gf_documento_equivalente de 
           LEFT JOIN gf_tipo_documento_equivalente tde ON tde.id_unico=de.tipo  
           LEFT JOIN gf_tercero tr ON tr.id_unico = de.tercero
           LEFT JOIN gf_detalle_documento_equivalente dde ON dde.documento_equivalente = de.id_unico 
           LEFT JOIN gf_tipo_regimen trg ON tr.tiporegimen = trg.id_unico 
           LEFT JOIN gf_tipo_identificacion tii ON tii.id_unico=tr.tipoidentificacion
           WHERE tde.envio_doc_soporte=1
           AND de.parametrizacionanno =".$_SESSION['anno']."
           AND de.cuds IS NULL 
           GROUP BY de.id_unico
           HAVING  SUM(dde.valor_total)>0
           ORDER BY cast(de.numero as unsigned) DESC";
$resultado = $mysqli->query($query);
?>
  <title>Documento Soporte</title>
</head>
<body>

<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once ('menu.php'); ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Documentos Soporte</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td class="cabeza" style="display: none;">Identificador</td>
                            <td class="cabeza" width="30px" align="center"></td>
                            <td class="cabeza"><strong>Nº</strong></td>
                            <td class="cabeza"><strong>Doc Identificación</strong></td>
                            <td class="cabeza"><strong>Tercero</strong></td>
                            <td class="cabeza"><strong>Fecha</strong></td>
                            <td class="cabeza"><strong>Valor</strong></td>
                            <td class="cabeza"><strong>IVA</strong></td>
                            <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                            <td class="cabeza"><strong>Email</strong></td>
                            <td class="cabeza"><strong>Dirección</strong></td>
                            <td class="cabeza"><strong>Régimen</strong></td>
                            <td class="cabeza"><strong>Responsabilidades Fiscales</strong></td>
                            <td class="cabeza"><strong>Responsabilidades Tributarias</strong></td>
                            <td class="cabeza"><strong>Procedencia</strong></td>
                            <td class="cabeza"><strong>Telefono</strong></td>
                        </tr>
                        <tr>
                            <th class="cabeza" style="display: none;">Identificador</th>
                            <th class="cabeza" width="7%"></th>
                            <th class="cabeza"><strong>Nº</strong></th>
                            <th class="cabeza"><strong>Doc Identificación</strong></th>
                            <th class="cabeza"><strong>Tercero</strong></th>
                            <th class="cabeza"><strong>Fecha</strong></th>
                            <th class="cabeza"><strong>Valor</strong></th>
                            <th class="cabeza"><strong>IVA</strong></th>
                            <th class="cabeza"><strong>Valor Total Ajustado</strong></th>
                            <th class="cabeza"><strong>Email</strong></th>
                            <th class="cabeza"><strong>Dirección</strong></th>
                            <th class="cabeza"><strong>Régimen</strong></th>
                            <th class="cabeza"><strong>Responsabilidades Fiscales</strong></th>
                            <th class="cabeza"><strong>Responsabilidades Tributarias</strong></th>
                            <th class="cabeza"><strong>Procedencia</strong></th>
                            <th class="cabeza"><strong>Telefono</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_row($resultado)):?>
                          <tr>
                               <td class="campos" style="display: none;"></td>
                              <td>
                                <?php 
                                if(!empty($row[8]) && !empty($row[9]) && !empty($row[12]) && !empty($row[13]) && !empty($row[14]) && !empty($row[15]) && !empty($row[16]) && $row[17]=='NIT' ){ ?>
                                <a class="campos btn btn-primary sendBill" href="consultasFacturacion/EnvioDocumentoSoporte.php?id=<?=$row[0]?>">
                                  <i title="Enviar Documento" class="glyphicon glyphicon-send"></i>
                                </a>
                              <?php } else {
                                echo '<a href="'.$row[9].'='.md5($row[10]).'" target="_blank" style="color:#f12020"><i class="glyphicon glyphicon-edit"></i>Modificar Datos Cliente</a>';
                              } ?>
                              </td>
                              <td class="campos" ><?=$row[1]?></td>
                              <td class="campos" >
                              <?php if ($row[17]!='NIT') {
                              echo '<label style="color:#f12020">PROVEEDOR TIENE TIPO DE DOCUMENTO DIFERENTE DE NIT </label>';
                              }else{
                                 echo $row[17];
                              }?>
                              </td>
                              <td class="campos" ><?=$row[2].' - '.$row[14]?></td>
                              <td class="campos" ><?=date('d/m/Y', strtotime($row[3]))?></td>
                              <td class="campos" ><?="$ ".number_format($row[4], 2)?></td>
                              <td class="campos" ><?="$ ".number_format($row[5], 2)?></td>
                              <td class="campos" ><?="$ ".number_format($row[6], 2)?></td>
                              <td class="campos" >
                                <?php if(empty($row[7])){
                                  echo '<label style="color:#f12020">PROVEEDOR NO TIENE CORREO </label>';
                                }  else {echo $row[7];}?></td>
                              <td class="campos" >
                                <?php if(empty($row[8])){
                                  echo '<label style="color:#f12020">PROVEEDOR NO TIENE DIRECCIÓN</label>';
                                }  else {echo $row[8];}?></td>
                              <td class="campos" >
                                <?php if(empty($row[11])){
                                  echo '<label style="color:#f12020">PROVEEDOR NO TIENE RÉGIMEN</label>';
                                }  else {echo $row[11];}?></td>
                              <td class="campos" >
                                <?php if(empty($row[12])){
                                  echo '<label style="color:#f12020">PROVEEDOR NO TIENE RESPONSABILIDADES FISCALES</label>';
                                }  else {echo $row[12];}?></td>
                                <td class="campos" >
                                <?php if(empty($row[14])){
                                  echo '<label style="color:#f12020">PROVEEDOR NO TIENE RESPONSABILIDADES TRIBUTARIAS</label>';
                                }  else {echo $row[14];}?></td>
                                <td class="campos" >
                                  <?php if(empty($row[15])){
                                  echo '<label style="color:#f12020">PROVEEDOR NO TIENE PROCEDENCIA</label>';
                                }  else {echo $row[15];}?></td>
                                  <td class="campos" >
                                  <?php if(empty($row[16])){
                                  echo '<label style="color:#f12020">PROVEEDOR NO TIENE TELEFONO</label>';
                                }  else {echo $row[16];}?></td>

                          </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div align="right"><a href="documentosSoporteEnviados.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px;margin-bottom: 20px; margin-left:-20px; margin-right:4px" target="_blank"><i class="glyphicon glyphicon-check"></i> Documentos Enviados</a> </div>       
                </div>
            </div>
        </div>
    </div>
</div>
  <div class="modal fade mdl-info" id="mdlInfo" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;"></h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <?php require_once ('footer.php'); ?>

  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script src="js/facturacion_electronica/facturacion.js"></script>
</body>
</html>


