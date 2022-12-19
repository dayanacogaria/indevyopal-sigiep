<?php
require_once ('Conexion/conexion.php');
require_once ('head_listar.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();

@$id = $_GET['id'];
$row = $con->Listar("SELECT DISTINCT a.id_unico, 
        a.consecutivo, a.tipo, ta.nombre, 
        a.nrocuotas, a.abonoinicial, a.valor, 
        a.porcentaje_apl, GROUP_CONCAT(DISTINCT CONCAT(\"'\",dac.soportedeuda,\"'\")), 
        GROUP_CONCAT(DISTINCT dac.soportedeuda), DATE_FORMAT(a.fecha, '%d/%m/%Y')  
    FROM ga_acuerdo a 
    LEFT JOIN ga_tipo_acuerdo ta        ON ta.id_unico = a.tipo 
    LEFT JOIN ga_detalle_acuerdo da     ON a.id_unico  = da.acuerdo 
    LEFT JOIN ga_documento_acuerdo dac  ON a.id_unico  = dac.acuerdo 
    WHERE md5(a.id_unico) = '$id' 
    GROUP BY a.id_unico "); 
#* Predial 
if ($row[0][2] == 1) {
    $rowt = $con->Listar("SELECT  p.nombres 
        FROM  gr_factura_predial fp
        LEFT JOIN gp_predio1 pd         ON pd.id_unico = fp.predio
        LEFT JOIN gp_tercero_predio t   ON pd.id_unico = t.predio
        LEFT JOIN gr_propietarios p     ON p.id_unico  = t.tercero
        WHERE fp.numero IN(".$row[0][8].")  AND t.orden = '001'");
    $rowd = $con->Listar("SELECT p.codigo_catastral,f.numero,
        DATE_FORMAT(f.fechavencimiento , '%d/%m/%Y')         
        FROM ga_acuerdo a 
        LEFT JOIN ga_documento_acuerdo doc ON a.id_unico = doc.acuerdo
        LEFT JOIN gr_factura_predial f     ON f.numero   = doc.soportedeuda
        LEFT JOIN gp_predio1 p             ON p.id_unico = f.predio
        WHERE a.id_unico = ".$row[0][0]);
    $rowc = $con->Listar("SELECT DISTINCT c.nombre,da.concepto_deuda,cp.anno  
        FROM ga_detalle_acuerdo da 
        LEFT JOIN gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda 
        LEFT JOIN gr_concepto c ON c.id_unico = cp.id_concepto 
        WHERE da.acuerdo = ".$row[0][0]." ORDER BY da.concepto_deuda ");
    $rowda = $con->Listar("SELECT DISTINCT  da.nrocuota, DATE_FORMAT(da.fecha , '%d/%m/%Y')        
        FROM ga_detalle_acuerdo da 
        LEFT JOIN gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda 
        LEFT JOIN gr_concepto c ON c.id_unico = cp.id_concepto 
        WHERE da.acuerdo = ".$row[0][0]." ORDER BY da.nrocuota, da.concepto_deuda");
#Comercio
} else {
    $rowt = $con->Listar("SELECT DISTINCT 
            IF(tr.razonsocial IS NULL 
            OR tr.razonsocial  = '',
            CONCAT_WS(' ',tr.nombreuno,tr.nombredos,
            tr.apellidouno,tr.apellidodos), 
            tr.razonsocial) 
        FROM gc_contribuyente c
        LEFT JOIN gf_tercero tr    ON tr.id_unico = c.tercero
        LEFT JOIN gc_declaracion d ON c.id_unico  = d.contribuyente
        WHERE d.cod_dec IN (".$row[0][8].")");
    $rowd = $con->Listar("SELECT c.codigo_mat,d.cod_dec,DATE_FORMAT(d.fecha, '%d/%m/%Y')
        FROM ga_acuerdo a                                                         
        LEFT JOIN ga_documento_acuerdo doc on doc.acuerdo=a.id_unico
        LEFT JOIN gc_declaracion d on d.cod_dec=doc.soportedeuda
        LEFT JOIN gc_contribuyente c on c.id_unico=d.contribuyente
        WHERE a.id_unico = ".$row[0][0]);
    $rowc = $con->Listar("SELECT DISTINCT cc.nom_inf,da.concepto_deuda,cc.id_unico  
        FROM ga_detalle_acuerdo da 
        LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda 
        WHERE da.acuerdo = ".$row[0][0]." ORDER BY da.concepto_deuda");
    $rowda = $con->Listar("SELECT DISTINCT  da.nrocuota,DATE_FORMAT(da.fecha , '%d/%m/%Y')   
        FROM ga_detalle_acuerdo da 
        LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda  
        WHERE da.acuerdo = ".$row[0][0]."  ORDER BY da.nrocuota, da.concepto_deuda");
}   
                                        
?>
<title>Detalle Acuerdo de Pago</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px;">Detalle Acuerdo Pago</h2>
                <a href="listar_GA_ACUERDO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:0px;  background-color: #0e315a; color: white; border-radius: 5px">Datos del Acuerdo</h5>
                <div class="client-form contenedorForma" style="margin-top: -7px;">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>
                        <div class="form-group form-inline" style="margin-top:0px">
                            <?= '<input type="hidden" name="txtNumeroA" id="txtNumeroA" value="'.$row[0][0].'">
                            <input type="hidden" name="sltTipo" id="sltTipo" value="'.$row[0][2].'">
                            <label class="col-sm-2 control-label"><strong class="obligado">*</strong>Tipo Acuerdo:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left">'.$row[0][3].'</label>
                            <label class="col-sm-2 control-label"><strong class="obligado">*</strong>Consecutivo:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left">'.$row[0][1].'</label>
                            <label class="col-sm-2 control-label"><strong class="obligado">*</strong>Fecha:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left">'.$row[0][10].'</label>';
                            ?> 
                        </div>
                        <div class="form-group form-inline" >
                            <?= '<label class="col-sm-2 control-label"><strong class="obligado">*</strong>N° Coutas:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left">'.$row[0][4].'</label>
                            <label class="col-sm-2 control-label"><strong class="obligado">*</strong>% Interés:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left">'.$row[0][7].'</label>
                            <label class="col-sm-2 control-label"><strong class="obligado">*</strong>Valor:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left">'.number_format($row[0][6], 2, '.', ',').'</label>';
                            ?>
                        </div>
                        <div class="form-group form-inline">
                            <?= '<label class="col-sm-2 control-label"><strong class="obligado">*</strong>Tercero:</label>
                            <label class="col-sm-2 control-label" style="font-weight:normal;text-align:left">'.$rowt[0][0].'</label>';?>
                            <div class="col-sm-6" style="margin-top:5px; text-align:-webkit-right" >
                                <button id="btnpdf" onclick="reportePdf()" class="btn sombra btn-primary" title="Generar certificado PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>
                                <?php #Si no tiene facturas puede eliminar
                                $fc = $con->Listar("SELECT * FROM ga_factura_acuerdo fa 
                                    LEFT JOIN ga_detalle_factura df ON df.factura = fa.id_unico 
                                    LEFT JOIN ga_detalle_acuerdo da ON df.detalleacuerdo = da.id_unico 
                                    WHERE da.acuerdo =".$row[0][0]);
                                if(count($fc)>0){}else{
                                    echo '<a href="javaScript:eliminar('.$row[0][0].')" class="btn sombra btn-primary" title="Eliminar"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></a>';
                                }?>
                            </div>
                        </div> 
                        <table id="" class="" cellspacing="100" width="80%" style="margin-bottom: 10px; margin-left: 80px">
                            <thead>
                                <tr>
                                    <?php if($row[0][2]==1){ 
                                        echo '<td class="cabeza"><strong>Nº Catastral </strong></td>
                                        <td class="cabeza"><strong>Nº Factura</strong></td>';
                                    } else {
                                        echo '<td class="cabeza"><strong>Cód. Matricula</strong></td>
                                        <td class="cabeza"><strong>Nº Declaración</strong></td>';
                                    }?>
                                    <td class="cabeza"><strong>Fecha Vencimiento</strong></td>
                                </tr>
                            </thead>    
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($rowd); $i++) {?>
                                    <tr>
                                        <td class="campos text-left"><?= $rowd[$i][0]; ?></td>                   
                                        <td class="campos text-left"><?= $rowd[$i][1]; ?></td>                
                                        <td class="campos text-left"><?= $rowd[$i][2];?></td>   
                                    </tr> 
                                <?php } ?>
                            </tbody>
                        </table>    
                    </form>
                </div>                
                <div class="table-responsive" >
                    <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td style="display: none;">Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Cuota</strong></td>
                                <td class="cabeza"><strong>Fecha Pago</strong></td>
                                <td class="cabeza"><strong>Total Cuota</strong></td>
                                <?php for ($c = 0;$c < count($rowc);$c++) {
                                    echo "<td class='cabeza'><strong>".$rowc[$c][0]."-".$rowc[$c][2]."</strong></td>";
                                } ?>
                            </tr>
                            <tr>
                                <th class="cabeza" style="display: none;">Identificador</th>
                                <th class="cabeza" width="7%"></th>
                                <th class="cabeza">Cuota</th>
                                <th class="cabeza">Fecha Pago</th>
                                <th class="cabeza">Total Cuota</th>
                                <?php for ($c = 0;$c < count($rowc);$c++) {
                                    echo "<th class='cabeza'>".$rowc[$c][0]."-".$rowc[$c][2]."</th>";
                                } ?>
                            </tr>
                        </thead>    
                        <tbody>
                            <?php $valort = 0;
                            for ($a = 0; $a < count($rowda);$a++) {
                            echo '<tr><td style="display: none;">'.$rowda[$a][0].'</td>
                                <td></td>
                                <td class="campos text-center">'.$rowda[$a][0].'</td>                   
                                <td class="campos text-center">'.$rowda[$a][1].'</td>';
                                if ($row[0][2] == 1) {
                                    $rowvc = $con->Listar("SELECT DISTINCT c.nombre, 
                                        da.valor, da.concepto_deuda
                                    FROM ga_detalle_acuerdo da
                                    LEFT JOIN   gr_concepto_predial cp ON cp.id_unico = da.concepto_deuda
                                    LEFT JOIN   gr_concepto c ON c.id_unico = cp.id_concepto
                                    WHERE  da.acuerdo = ".$row[0][0]." and da.nrocuota='".$rowda[$a][0]."'
                                    ORDER BY   da.nrocuota, da.concepto_deuda");
                                } else {
                                    $rowvc = $con->Listar("SELECT DISTINCT cc.nom_inf, 
                                        da.valor,da.concepto_deuda  
                                    FROM ga_detalle_acuerdo da 
                                    LEFT JOIN gc_concepto_comercial cc ON cc.id_unico = da.concepto_deuda 
                                    WHERE da.acuerdo = ".$row[0][0]." and da.nrocuota='".$rowda[$a][0]."' 
                                    ORDER BY da.nrocuota, da.concepto_deuda");
                                }
                                $vc = 0;
                                for ($v = 0; $v < count($rowvc); $v++) {
                                    $vc     += $rowvc[$v][1];
                                }
                                echo '<td class="campos text-right">'.number_format($vc, 2, '.', ',').'</td>';
                                for ($v = 0; $v < count($rowvc); $v++) {
                                    $valort += $rowvc[$v][1];
                                    $vc     += $rowvc[$v][1];
                                    echo ' <td class="campos text-right">'. number_format($rowvc[$v][1], 2, '.', ',').'</td>';
                                }
                                echo '</tr>';
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-8 col-sm-2" style="margin-top:-22px">
                <table class="tablaC table-condensed text-center" align="center">
                    <thead>
                        <tr><th>
                                <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th></tr>
                    </thead>
                    <tbody>
                        <tr> <td>
                                <a class="btn btn-primary btnInfo" href="registrar_GA_FACTURA_ACUERDO.php?nacuerdo=<?= $row[0][0] ?>&sltTiposelect=<?=$row[0][2] ?>" target="_blank">FACTURA ACUERDO</a>
                        </td> </tr>
                    </tbody>
                </table>
            </div>
        </div>                                    
    </div>
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
   <?php require_once './footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        function reportePdf() {
            $('form').attr('action', 'informes/INF_Certificado_Acuerdo_Pago.php');
        }
    </script>
    <script>
        function eliminar(id){
            let result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarAcuerdo.php?id="+id+'&action=1',
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
                            document.location = 'listar_GA_ACUERDO.php';
                        })
                    }
                });
            });
        }
    </script>
</body>
</html>