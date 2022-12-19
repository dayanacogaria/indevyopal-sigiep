<?php
require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
@$id = $_GET['id'];
$sql = "SELECT fa.id_unico, fa.numero, DATE_FORMAT(fa.fecha_ven,'%d/%m/%Y'), a.consecutivo,
    fa.observaciones,SUM(df.valor)as valor,a.tipo
            FROM ga_factura_acuerdo fa 
            left join ga_detalle_factura df on df.factura=fa.id_unico
            left join ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
            left join ga_acuerdo a on a.id_unico=da.acuerdo 
            WHERE md5(fa.id_unico) = '$id' ";
$resultado = $mysqli->query($sql);
$res = mysqli_fetch_row($resultado);
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<title>Detalle Factura Acuerdo de Pago</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: 0px">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Detalle Factura Acuerdo Pago</h2>
                <a href="listar_GA_FACTURA_ACUERDO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:8px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(("Datos de la Factura Acuerdo")); ?></h5>
                <div class="client-form contenedorForma" style="margin-top: -7px;">
                    <form id="formid" name="formid" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarFacturaAcuerdoJSON.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                        <div class="form-group form-inline" style="margin-top:-25px">
                            <label for="txtNumeroA" class="col-sm-2 control-label"><strong class="obligado">*</strong>Nº Acuerdo:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left"><?=$res[3];?></label>
                            <label for="sltFechaA" class="col-sm-2 control-label"><strong class="obligado">*</strong>Fecha Vencimiento:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left"><?=$res[2];?></label>
                            <label for="txtNumeroF" class="col-sm-2 control-label"><strong class="obligado">*</strong>Número:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left"><?=$res[1];?></label>
                        </div>
                        <br/>
                        <div class="form-group form-inline" style="margin-top:-25px">
                            <label for="txtObservaciones" class="col-sm-2 control-label">Observaciones:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left"><?=$res[4];?></label>
                            <label for="txtTotalF" class="col-sm-1 control-label">Total Factura:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left"><?=number_format($res[5], 2, '.', ',');?></label>
                            <a id="enviar"   href="informes/INF_FACTURA_ACUERDO.php?id=<?=$id;?>" target="_blank" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;">
                                <li class="glyphicon glyphicon-print"></li></a>     
                        </div>
                    </form>
                </div>
                <div class=" col-sm-12 col-md-12 col-lg-12 form-group form-inline" style="margin-top:5px; display:<?php echo $a2 ?>">
                    <?php
                    $tab_fd = '';
                    if (empty($fecha_ac)) {                        
                    } else {
                        $fec_Ac = explode("/", $fecha_ac);                                                //Divimos la fecha usando /
                        $fec_Ac = "$fec_Ac[2]-$fec_Ac[1]-$fec_Ac[0]";
                    }
                    ?>
                    <div class="table-responsive" style="margin-left: 10px; margin-right: 20px;margin-top:0px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" >
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Cuota</strong></td>
                                        <?php
                                        if ($res[6] == 1) {//$nacuerdo
                                            $sql = "SELECT DISTINCT 
                                                c.nombre, c.id_unico 
                                                FROM ga_detalle_factura df
                                                left JOIN ga_detalle_acuerdo da on da.id_unico= df.detalleacuerdo
                                                LEFT JOIN gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda 
                                                LEFT JOIN gr_concepto c ON c.id_unico = cp.id_concepto 
                                                WHERE df.factura = '$res[0]' ORDER BY da.concepto_deuda ";
                                        } else if ($res[6] == 2) {
                                            $sql = "SELECT DISTINCT 
                                                cc.nom_inf , cc.id_unico 
                                                FROM ga_detalle_factura df
                                                LEFT JOIN ga_detalle_acuerdo da on da.id_unico= df.detalleacuerdo
                                                LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda 
                                                WHERE df.factura = '$res[0]' ORDER BY da.concepto_deuda";
                                        } else {
                                            $sql = "";
                                        }
                                        $resultado = $mysqli->query($sql);
                                        while ($row = mysqli_fetch_row($resultado)) {
                                            echo "<td class='cabeza'><strong>$row[0]</strong></td>";
                                        } ?>
                                        <td class="cabeza"><strong>Total Cuota</strong></td>

                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <th class="cabeza">Cuota</th>
                                        <?php
                                        $resultado = $mysqli->query($sql);
                                        while ($row = mysqli_fetch_row($resultado)) {
                                            echo "<th class='cabeza'>$row[0]</th>";
                                        }?>
                                        <th class="cabeza">Total Cuota</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                <?php
                                $rowct = $con->Listar("SELECT DISTINCT da.nrocuota 
                                    FROM ga_detalle_factura df 
                                    LEFT JOIN ga_detalle_acuerdo da ON df.detalleacuerdo = da.id_unico 
                                    WHERE md5(df.factura) = '$id' ");
                                for ($i= 0;$i < count($rowct );$i++) {
                                    $ncta = $rowct[$i][0]; ?>
                                    <tr>
                                    <td style="display: none;"></td>
                                    <td></td>                  
                                    <td class="campos text-center"><?= $ncta; ?></td> 
                                    <?php $tc = 0;
                                    $resultado = $mysqli->query($sql);
                                    while ($row = mysqli_fetch_row($resultado)) {
                                        $cn = $row[1];
                                        if ($res[6] == 1) {
                                            $rowv = $con->Listar("SELECT DISTINCT SUM(df.valor) FROM ga_detalle_factura df 
                                                LEFT JOIN ga_detalle_acuerdo da ON df.detalleacuerdo = da.id_unico 
                                                LEFT JOIN gr_concepto_predial cp ON da.concepto_deuda = cp.id_unico 
                                                LEFT JOIN gr_concepto c ON cp.id_concepto = c.id_unico 
                                                WHERE df.factura = $res[0] AND c.id_unico = $cn AND da.nrocuota  = $ncta 
                                                GROUP BY da.nrocuota, c.id_unico");
                                        } else { 
                                            $rowv = $con->Listar("SELECT DISTINCT SUM(df.valor) FROM ga_detalle_factura df 
                                                LEFT JOIN ga_detalle_acuerdo da ON df.detalleacuerdo = da.id_unico 
                                                LEFT JOIN gc_concepto_comercial cp ON da.concepto_deuda = cp.id_unico 
                                                WHERE df.factura = $res[0] AND cp.id_unico = $cn AND da.nrocuota  = $ncta 
                                                GROUP BY da.nrocuota, cp.id_unico");
                                        }?>
                                        <td class="campos text-right"><?= number_format($rowv[0][0], 2, '.', ','); ?></td>
                                    <?php $tc += $rowv[0][0]; } ?>
                                    <td class="campos text-right"><?php echo number_format($tc, 2, '.', ','); ?></td>  
                                    </tr> 
                                <?php } ?>    
                                </tbody>
                            </table>
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
                    <p>¿Desea eliminar el registro seleccionado de Vinculación Retiro?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" onclick="recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
</body>
</html>