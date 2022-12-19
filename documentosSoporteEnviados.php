<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
  $query = "SELECT de.id_unico, CONCAT(tde.sigla,'-',de.numero), CONCAT(IF(CONCAT_WS(' ',
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
           tr.apellidodos)),'-',tr.numeroidentificacion) AS NOMBRE, de.fecha, 
           SUM(dde.valor_unitario*dde.cantidad), SUM(dde.valor_iva*dde.cantidad), SUM(dde.valor_total)
           
           FROM gf_documento_equivalente de 
           LEFT JOIN gf_tipo_documento_equivalente tde ON tde.id_unico=de.tipo  
           LEFT JOIN gf_tercero tr ON tr.id_unico = de.tercero
           LEFT JOIN gf_detalle_documento_equivalente dde ON dde.documento_equivalente = de.id_unico 
           LEFT JOIN gf_tipo_regimen trg ON tr.tiporegimen = trg.id_unico 
           LEFT JOIN gf_telefono tel ON tel.tercero=tr.id_unico
           WHERE tde.envio_doc_soporte=1
           AND de.parametrizacionanno= ".$_SESSION['anno']." 
           AND de.cuds IS NOT NULL 
           GROUP BY de.id_unico
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
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Documento Soporte</h2>
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
                            <th class="cabeza"><strong>Valor Total Ajustado</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_row($resultado)):?>
                          <tr>
                            <td class="campos" style="display: none;"></td>
                            <td>
                              
                            <!--  <a target="_blank" class="campos btn btn-primary" href="consultasFacturacion/pdf.php?id=<?=$row[0].'&t='.$_REQUEST['t'];?>">
                                <i title="Imprimir Factura" class="glyphicon glyphicon-print"></i>
                              </a>   -->                          

                            </td>
                            <td class="campos" ><?=$row[1]?></td>
                            <td class="campos" ><?=$row[2].' - '.$row[10]?></td>
                            <td class="campos" ><?=date('d/m/Y', strtotime($row[3]))?></td>
                            <td class="campos" ><?="$ ".number_format($row[4], 2)?></td>
                            <td class="campos" ><?="$ ".number_format($row[5], 2)?></td>
                          
                            <td class="campos" ><?="$ ".number_format($row[6], 2)?></td>
                          </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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


