<?php
require_once 'Conexion/conexion.php';
require_once 'Conexion/ConexionPDO.php';
require_once './head_listar.php';
require_once './jsonPptal/funcionesPptal.php';
$con   = new ConexionPDO();
$nanno = anno($_SESSION['anno']);
$sql   = "SELECT DISTINCT a.id_unico, 
        a.consecutivo, a.tipo, ta.nombre, 
        a.nrocuotas, a.abonoinicial, a.valor, 
        a.porcentaje_apl, GROUP_CONCAT(DISTINCT CONCAT(\"'\",dac.soportedeuda,\"'\")), 
        GROUP_CONCAT(DISTINCT dac.soportedeuda), DATE_FORMAT(a.fecha, '%d/%m/%Y')
    FROM ga_acuerdo a 
    LEFT JOIN ga_tipo_acuerdo ta        ON ta.id_unico = a.tipo 
    LEFT JOIN ga_detalle_acuerdo da     ON a.id_unico  = da.acuerdo 
    LEFT JOIN ga_documento_acuerdo dac  ON a.id_unico  = dac.acuerdo 
    WHERE YEAR(da.fecha) = '$nanno' 
    GROUP BY a.id_unico ";
$resultado = $mysqli->query($sql);
?>
<title>Listar Acuerdo</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once './menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Acuerdos de Pago</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="7%" class="cabeza"></td>             
                                    <td class="cabeza"><strong>Tipo</strong></td>
                                    <td class="cabeza"><strong>Consecutivo</strong></td>
                                    <td class="cabeza"><strong>Fecha</strong></td>
                                    <td class="cabeza"><strong>Tercero</strong></td>
                                    <td class="cabeza"><strong>N° Documento</strong></td>
                                    <td class="cabeza"><strong>Cuotas</strong></td>
                                    <td class="cabeza"><strong>% Interés</strong></td>
                                    <td class="cabeza"><strong>Valor</strong></td>

                                </tr>
                                <tr>
                                    <th class="cabeza" style="display: none;">Identificador</th>
                                    <th width="7%"></th>           
                                    <th class="cabeza">Tipo</th>
                                    <th class="cabeza">Consecutivo</th>
                                    <th class="cabeza">Fecha</th>
                                    <th class="cabeza">Tercero</th>
                                    <th class="cabeza">N° Documento</th>
                                    <th class="cabeza">Cuotas</th>
                                    <th class="cabeza">% Interés</th>
                                    <th class="cabeza">Valor</th>

                                </tr>
                            </thead>    
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_row($resultado)) {
                                    #Tercero
                                    if ($row[2] == 1) {
                                        $rowt = $con->Listar("SELECT  p.nombres 
                                            FROM  gr_factura_predial fp
                                            LEFT JOIN gp_predio1 pd         ON pd.id_unico = fp.predio
                                            LEFT JOIN gp_tercero_predio t   ON pd.id_unico = t.predio
                                            LEFT JOIN gr_propietarios p     ON p.id_unico  = t.tercero
                                            WHERE fp.numero IN($row[8])  AND t.orden = '001'");
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
                                            WHERE d.cod_dec IN ($row[8])");
                                    } ?>
                                    <tr>
                                        <td class="campos" style="display: none;"><?=$row[0];?></td>
                                        <td class="campos">
                                            <a class="campos" href="ver_GA_ACUERDO.php?id=<?= md5($row[0]);?>"><i title="Ver Detalle" class="glyphicon glyphicon-eye-open"></i></a>
                                            <?php #Si no tiene facturas puede eliminar
                                            $fc = $con->Listar("SELECT * FROM ga_factura_acuerdo fa 
                                                LEFT JOIN ga_detalle_factura df ON df.factura = fa.id_unico 
                                                LEFT JOIN ga_detalle_acuerdo da ON df.detalleacuerdo = da.id_unico 
                                                WHERE da.acuerdo =".$row[0]);
                                            if(count($fc)>0){}else{
                                                echo '<a class="campos" href="javaScript:eliminar('.$row[0].')"><i title="Eliminar Acuerdo" class="glyphicon glyphicon-trash"></i></a>';
                                            }?>
                                        </td>                                        
                                        <td class="campos"><?= $row[3];?></td>                
                                        <td class="campos"><?= $row[1] ?></td>                
                                        <td class="campos"><?= $row[10] ?></td>                
                                        <td class="campos"><?= $rowt[0][0]; ?></td>           
                                        <td class="campos"><?= $row[9]; ?></td>                
                                        <td class="campos text-right"><?= $row[4]; ?></td>                
                                        <td class="campos text-right"><?= $row[7]; ?></td>                
                                        <td class="campos text-right"><?= number_format($row[6], 2, '.', ',') ?></td>                
                                    </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                        <div align="right">
                            <a href="registrar_GA_ACUERDO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once './footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
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
                            document.location.reload();
                        })
                    }
                });
            });
        }
    </script>
</body>
</html>