<?php
require_once 'Conexion/conexion.php';
require_once './head_listar.php';
$panno = $_SESSION['anno'];

  $sql =    "SELECT DISTINCT
                (SELECT CASE WHEN(a1.tipo=1)THEN(SELECT p.id_unico from gr_detalle_pago_predial dpp
                left join gr_pago_predial  p on p.id_unico=dpp.pago 
                where dpp.id_unico=df.iddetallerecaudo AND p.parametrizacionanno = $panno) ELSE (SELECT r.id_unico from gc_detalle_recaudo dr
                left join gc_recaudo_comercial  r on r.id_unico=dr.recaudo 
                where dr.id_unico=df.iddetallerecaudo AND r.parametrizacionanno = $panno) END 
                FROM ga_detalle_acuerdo da1 
                left join ga_acuerdo a1 on a1.id_unico=da1.acuerdo  
                where da1.id_unico=da.id_unico) as idrec,

                (SELECT CASE WHEN(a1.tipo=1)THEN(SELECT DATE_FORMAT(p.fechapago,'%d/%m/%Y') from gr_detalle_pago_predial dpp
                left join gr_pago_predial  p on p.id_unico=dpp.pago 
                where dpp.id_unico=df.iddetallerecaudo AND p.parametrizacionanno = $panno) ELSE (SELECT DATE_FORMAT(r.fecha,'%d/%m/%Y') from gc_detalle_recaudo dr
                left join gc_recaudo_comercial  r on r.id_unico=dr.recaudo 
                where dr.id_unico=df.iddetallerecaudo AND r.parametrizacionanno = $panno) END 
                FROM ga_detalle_acuerdo da1 
                left join ga_acuerdo a1 on a1.id_unico=da1.acuerdo  
                where da1.id_unico=da.id_unico) as fecha,

                (a.consecutivo) as acuerdo,
                (ta.nombre) as tipo,
                (f.numero) as nfac,

                (SELECT CASE WHEN(a1.tipo=1)THEN(SELECT t.razonsocial from gr_detalle_pago_predial dpp
                left join gr_pago_predial  pg on pg.id_unico=dpp.pago 
                left JOIN gf_cuenta_bancaria cb on cb.id_unico=pg.banco
                left join gf_tercero t on t.id_unico = cb.banco
                where dpp.id_unico=df.iddetallerecaudo) ELSE (SELECT t.razonsocial from gc_detalle_recaudo dr
                left join gc_recaudo_comercial  r on r.id_unico=dr.recaudo 
                left join gf_cuenta_bancaria cb on cb.id_unico=r.cuenta_ban
                left join gf_tercero t on t.id_unico=cb.banco
                where dr.id_unico=df.iddetallerecaudo) END 
                FROM ga_detalle_acuerdo da1 
                left join ga_acuerdo a1 on a1.id_unico=da1.acuerdo  
                where da1.id_unico=da.id_unico)as banco,

                ((SELECT CASE WHEN(a1.tipo=1)THEN(SELECT sum(dpp.valor) from gr_detalle_pago_predial dpp
                where dpp.pago=(SELECT CASE WHEN(a1.tipo=1)THEN(SELECT p.id_unico from gr_detalle_pago_predial dpp
                left join gr_pago_predial  p on p.id_unico=dpp.pago 
                where dpp.id_unico=df.iddetallerecaudo) ELSE (SELECT r.id_unico from gc_detalle_recaudo dr
                left join gc_recaudo_comercial  r on r.id_unico=dr.recaudo 
                where dr.id_unico=df.iddetallerecaudo) END 
                FROM ga_detalle_acuerdo da1 
                left join ga_acuerdo a1 on a1.id_unico=da1.acuerdo  
                where da1.id_unico=da.id_unico) ) ELSE (SELECT DISTINCT r.valor from gc_detalle_recaudo dr
                left join gc_recaudo_comercial r on r.id_unico=dr.recaudo  
                where dr.recaudo=(SELECT CASE WHEN(a1.tipo=1)THEN(SELECT p.id_unico from gr_detalle_pago_predial dpp
                left join gr_pago_predial  p on p.id_unico=dpp.pago 
                where dpp.id_unico=df.iddetallerecaudo) ELSE (SELECT r.id_unico from gc_detalle_recaudo dr
                left join gc_recaudo_comercial  r on r.id_unico=dr.recaudo 
                where dr.id_unico=df.iddetallerecaudo) END 
                FROM ga_detalle_acuerdo da1 
                left join ga_acuerdo a1 on a1.id_unico=da1.acuerdo  
                where da1.id_unico=da.id_unico)) END 
                FROM ga_detalle_acuerdo da1 
                left join ga_acuerdo a1 on a1.id_unico=da1.acuerdo  
                where da1.id_unico=da.id_unico) ) as vlr,
                (ta.id_unico) as idtipo


                FROM ga_detalle_acuerdo da 
                left join ga_acuerdo a on a.id_unico=da.acuerdo
                left join ga_tipo_acuerdo ta on ta.id_unico=a.tipo
                LEFT join ga_detalle_factura df on df.detalleacuerdo=da.id_unico
                LEFT join ga_factura_acuerdo f on f.id_unico=df.factura

                where df.iddetallerecaudo is NOT null  
                AND (SELECT CASE WHEN(a1.tipo=1)THEN(SELECT p.id_unico from gr_detalle_pago_predial dpp
                left join gr_pago_predial  p on p.id_unico=dpp.pago 
                where dpp.id_unico=df.iddetallerecaudo AND p.parametrizacionanno = $panno) ELSE (SELECT r.id_unico from gc_detalle_recaudo dr
                left join gc_recaudo_comercial  r on r.id_unico=dr.recaudo 
                where dr.id_unico=df.iddetallerecaudo AND r.parametrizacionanno = $panno) END 
                FROM ga_detalle_acuerdo da1 
                left join ga_acuerdo a1 on a1.id_unico=da1.acuerdo  
                where da1.id_unico=da.id_unico) IS NOT NULL 
                order by fecha DESC ";
  
    $resultado = $mysqli->query($sql);
?>
    <title>Listar Recaudos Acuerdo</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Recaudos Acuerdos de Pago</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Acuerdo</strong></td>
                                        <td class="cabeza"><strong>Tipo</strong></td>
                                        <td class="cabeza"><strong>Factura</strong></td>
                                        <td class="cabeza"><strong>Banco</strong></td>
                                        <td class="cabeza"><strong>Valor Pago</strong></td>
                                        
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Fecha</th>
                                        <th class="cabeza">Acuerdo</th>
                                        <th class="cabeza">Tipo</th>
                                        <th class="cabeza">Factura</th>
                                        <th class="cabeza">Banco</th>
                                        <th class="cabeza">Valor Pago</th>
                                        
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        $id_reca      = $row[0];
                                        $fec      = $row[1];
                                        $acu     = $row[2];
                                        $tip    = $row[3];
                                        $fac     = $row[4];
                                        $ban     = $row[5];
                                        $valor    = $row[6];
                                        ?>
                                    <tr>
                                        <td class="campos" style="display: none;"></td>
                                        <td class="campos">
                                            
                                            <a class="campos" href="ver_GA_RECAUDO_ACUERDO.php?id=<?php echo md5($row[0]);?>&tip=<?php echo $row[7];?>">
                                                <i title="Ver Detalle" class="glyphicon glyphicon-eye-open" ></i>
                                            </a>
                                            <a class="campos" href="informes/INF_RECAUDO_ACUERDO.php?id=<?php echo md5($row[0]);?>" target="_blank">
                                                <i title="Imprimir" class="glyphicon glyphicon-print" ></i>
                                            </a>
                                            <a class="campos" href="javaScript:eliminar(<?=$row[0].','.$row[7] ?>)"><i title="Eliminar Recaudo" class="glyphicon glyphicon-trash"></i></a>
                                        </td>                                        
                                        <td class="campos text-right"><?php echo $fec?></td>                
                                        <td class="campos"><?php echo $acu?></td>                
                                        <td class="campos"><?php echo $tip?></td>                
                                        <td class="campos text-right"><?php echo $fac?></td>                
                                        <td class="campos text-right"><?php echo $ban?></td>                
                                        <td class="campos text-right"><?php echo number_format($valor, 2, '.', ',')?></td>                
                                                    
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GA_RECAUDO_ACUERDO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php require_once './footer.php'; ?>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalMsj" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="msj"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script>
        function eliminar(id, tipo){
            let result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarAcuerdo.php?id="+id+'&action=2&tipo='+tipo,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){ 
                            $("#msj").html('Información Eliminada Correctamente');
                            $("#myModalMsj").modal('show');
                        } else{ 
                            $("#myModalMsj").modal('show');     
                            $("#msj").html('No Se Ha Podido Eliminar La Información');
                        }
                        $("#ver1").click(function(){
                            document.location.reload();
                        })
                    }
                });
            });
        }
    </script>
    </body>
</html>