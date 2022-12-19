<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$query = "SELECT DISTINCT fac.id_unico, fac.numero_factura, 
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
        tr.apellidodos)) AS NOMBRE, fac.fecha_factura, SUM(det.valor), SUM(det.iva), SUM(det.valor_total_ajustado)
FROM gp_factura fac 
LEFT JOIN gp_tipo_factura gpt ON gpt.id_unico = fac.tipofactura
LEFT JOIN gf_tercero tr ON tr.id_unico = fac.tercero
LEFT JOIN gp_detalle_factura det ON det.factura = fac.id_unico 
LEFT JOIN gp_detalle_factura dta ON det.id_unico = dta.detalleafectado 
LEFT JOIN gp_factura fa ON dta.factura = fa.id_unico  
LEFT JOIN gp_tipo_factura gpta ON gpta.id_unico = fa.tipofactura
WHERE (gpt.facturacion_e = 1  OR gpta.facturacion_e = 1 )
AND fac.parametrizacionanno = ".$_SESSION['anno']." AND fac.zip_id IS NULL
GROUP BY fac.id_unico
ORDER BY fac.numero_factura DESC"; 

$resultado = $mysqli->query($query);
?>
  <title>Editar Facturación Electrónica</title>
</head>
<body>

<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once ('menu.php'); ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Editar Facturas</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td class="cabeza" style="display: none;">Identificador</td>
                            <td class="cabeza" width="30px" align="center"></td>
                            <td class="cabeza"><strong>Nº</strong></td>
                            <td class="cabeza"><strong>CUFE</strong></td>
                            <td class="cabeza"><strong>ZIP ID</strong></td>
                            <td class="cabeza"><strong>Fecha Envío</strong></td>
                            <td class="cabeza"><strong>Hora Envío</strong></td>
                            <td class="cabeza"><strong>Tercero</strong></td>
                            <td class="cabeza"><strong>Fecha</strong></td>
                            <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                        </tr>
                        <tr>
                            <th class="cabeza" style="display: none;">Identificador</th>
                            <th class="cabeza" width="7%"></th>
                            <th class="cabeza"><strong>Nº</strong></th>
                            <th class="cabeza"><strong>CUFE</strong></th>
                            <th class="cabeza"><strong>ZIP ID</strong></th>
                            <th class="cabeza"><strong>Fecha Envío</strong></th>
                            <th class="cabeza"><strong>Hora Envío</strong></th>
                            <th class="cabeza"><strong>Tercero</strong></th>
                            <th class="cabeza"><strong>Fecha</strong></th>
                            <th class="cabeza"><strong>Valor Total Ajustado</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_row($resultado)):?>
	                        <tr>
                                <form name="form<?=$row[0]?>" id="form<?=$row[0]?>" method="POST" action="javascript:guardar()">
                                <td class="campos" style="display: none;"></td>
	                            <td>
                                        <a class="campos btn btn-primary sendBill" href="javascript:guardar(<?=$row[0]?>)" data-id="<?=$row[0]?>">
	                              	<i title="Modificar" class="glyphicon glyphicon-floppy-disk"></i>
                                        </a>
	                            </td>
	                            <td class="campos" ><?=$row[1]?></td>
	                            <td class="campos" ><input type="text" name="cufe<?=$row[0]?>" id="cufe<?=$row[0]?>" class="form_control"  style="width:100px"  required></td>
                                    <td class="campos" ><input type="text" name="zipid<?=$row[0]?>" id="zipid<?=$row[0]?>" class="form_control"  style="width:100px"  required></td>
                                    <td class="campos" ><input type="date" name="fecha<?=$row[0]?>" id="fecha<?=$row[0]?>" class="form_control"  style="width:100px"  required></td>
                                    <td class="campos" ><input type="time" name="hora<?=$row[0]?>" id="hora<?=$row[0]?>" class="form_control"  style="width:100px"  required step="1"></td>
                                    <td class="campos" ><?=$row[2]?></td>
	                            <td class="campos" ><?=date('d/m/Y', strtotime($row[3]))?></td>
	                            <td class="campos" ><?="$ ".number_format($row[6], 2)?></td>
                                </form>
	                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
  <div class="modal fade mdl-info" id="mdlInfo" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <label id="mensaje" name="mensaje"></label>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <?php require_once ('footer.php'); ?>
    <script>
        function guardar(id){
            var nam = 'form'+id;
            var formData = new FormData($("#"+nam)[0]);  
            
           $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_facturaJson.php?action=55&id="+id,
            data:formData,
            contentType: false,
            processData: false,
            success: function(response)
            { 
                console.log(response);
                if(response==0){
                    $("#mensaje").html('Información Guardada Correctamente');  
                    $("#mdlInfo").modal('show'); 
                    $("#ver1").click(function(){
                        document.location.reload();
                    })
                    
                } else {
                    $("#mensaje").html('No Se Ha Podido Guardar La Información');  
                    $("#mdlInfo").modal('show'); 
                }
                
            }
           })
        }  
    </script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
</body>
</html>


