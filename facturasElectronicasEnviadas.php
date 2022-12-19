<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
if($_REQUEST['t']==2){
  $query = "SELECT fac.id_unico, CONCAT(gpt.prefijo, ' - ', fac.numero_factura), 
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
          tr.apellidodos)) AS NOMBRE, fac.fecha_factura, 
          SUM(det.valor*det.cantidad), SUM(det.iva*det.cantidad), 
          SUM(det.impoconsumo*det.cantidad), SUM(det.valor_total_ajustado), fac.zip_id, 
          tr.email, tr.numeroidentificacion 
  FROM gp_factura fac 
  LEFT JOIN gp_tipo_factura gpt ON gpt.id_unico = fac.tipofactura
  LEFT JOIN gf_tercero tr ON tr.id_unico = fac.tercero
  LEFT JOIN gp_detalle_factura det ON det.factura = fac.id_unico
  WHERE  fac.cufe IS NOT NULL AND fac.parametrizacionanno = ".$_SESSION['anno']." 
  AND gpt.clase_factura = 7 
  GROUP BY fac.id_unico
  ORDER BY cast(fac.numero_factura  as unsigned) DESC"; 

} elseif($_REQUEST['t']==3){
  $query = "SELECT fac.id_unico, CONCAT(gpt.prefijo, ' - ', fac.numero_factura), 
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
          tr.apellidodos)) AS NOMBRE, fac.fecha_factura, 
          SUM(det.valoru_conversion*det.cantidad), SUM((det.iva/det.valor_trm)*det.cantidad), 
          SUM((det.impoconsumo/det.valor_trm)*det.cantidad), SUM(det.valor_conversion), fac.zip_id, 
          tr.email, tr.numeroidentificacion 
  FROM gp_factura fac 
  LEFT JOIN gp_tipo_factura gpt ON gpt.id_unico = fac.tipofactura
  LEFT JOIN gf_tercero tr ON tr.id_unico = fac.tercero
  LEFT JOIN gp_detalle_factura det ON det.factura = fac.id_unico
  WHERE  fac.cufe IS NOT NULL AND fac.parametrizacionanno = ".$_SESSION['anno']." 
  AND fac.tipo_cambio IS NOT NULL 
  GROUP BY fac.id_unico
  ORDER BY cast(fac.numero_factura  as unsigned)  DESC";
} else {
  $query = "SELECT fac.id_unico, CONCAT(gpt.prefijo, ' - ', fac.numero_factura), 
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
          tr.apellidodos)) AS NOMBRE, fac.fecha_factura, 
          SUM(det.valor*det.cantidad), SUM(det.iva*det.cantidad), 
          SUM(det.impoconsumo*det.cantidad), SUM(det.valor_total_ajustado), fac.zip_id, 
          tr.email, tr.numeroidentificacion 
  FROM gp_factura fac 
  LEFT JOIN gp_tipo_factura gpt ON gpt.id_unico = fac.tipofactura
  LEFT JOIN gf_tercero tr ON tr.id_unico = fac.tercero
  LEFT JOIN gp_detalle_factura det ON det.factura = fac.id_unico
  WHERE  fac.cufe IS NOT NULL AND fac.parametrizacionanno = ".$_SESSION['anno']."
  AND gpt.facturacion_e = 1 AND fac.tipo_cambio IS NULL 
  GROUP BY fac.id_unico
  ORDER BY cast(fac.numero_factura  as unsigned) DESC"; 
}

$resultado = $mysqli->query($query);
?>
  <title>Facturación Electrónica</title>
</head>
<body>

<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once ('menu.php'); ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Facturas Enviadas</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td class="cabeza" style="display: none;">Identificador</td>
                            <td class="cabeza" width="30px" align="center"></td>
                            <td class="cabeza"><strong>Nº</strong></td>
                            <td class="cabeza"><strong>Tercero</strong></td>
                            <td class="cabeza"><strong>Fecha</strong></td>
                            <td class="cabeza"><strong>Valor</strong></td>
                            <td class="cabeza"><strong>IVA</strong></td>
                            <td class="cabeza"><strong>Impconsumo</strong></td>
                            <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                        </tr>
                        <tr>
                            <th class="cabeza" style="display: none;">Identificador</th>
                            <th class="cabeza" width="7%"></th>
                            <th class="cabeza"><strong>Nº</strong></th>
                            <th class="cabeza"><strong>Tercero</strong></th>
                            <th class="cabeza"><strong>Fecha</strong></th>
                            <th class="cabeza"><strong>Valor</strong></th>
                            <th class="cabeza"><strong>IVA</strong></th>
                            <th class="cabeza"><strong>Impoconsumo</strong></th>
                            <th class="cabeza"><strong>Valor Total Ajustado</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_row($resultado)):?>
                          <tr>
                            <td class="campos" style="display: none;"></td>
                            <td>
                              
                             <a target="_blank" class="campos btn btn-primary" href="consultasFacturacion/pdf.php?id=<?=$row[0].'&t='.$_REQUEST['t'];?>">
                                <i title="Imprimir Factura" class="glyphicon glyphicon-print"></i>
                              </a>                            

                            </td>
                            <td class="campos" ><?=$row[1]?></td>
                            <td class="campos" ><?=$row[2].' - '.$row[10]?></td>
                            <td class="campos" ><?=date('d/m/Y', strtotime($row[3]))?></td>
                            <td class="campos" ><?="$ ".number_format($row[4], 2)?></td>
                            <td class="campos" ><?="$ ".number_format($row[5], 2)?></td>
                            <td class="campos" ><?="$ ".number_format($row[6], 2)?></td>
                            <td class="campos" ><?="$ ".number_format($row[7], 2)?></td>
                          </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
  <div class="modal fade" id="mdlInfo" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
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
  <script type="text/javascript">
      facturacion.events();
  </script>
</body>
</html>


