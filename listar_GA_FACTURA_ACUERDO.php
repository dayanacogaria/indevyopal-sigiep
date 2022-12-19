<?php
require_once 'Conexion/conexion.php';
require_once 'Conexion/ConexionPDO.php';
require_once './jsonPptal/funcionesPptal.php';
require_once './head_listar.php';
$con    = new ConexionPDO();
$nanno  = anno($_SESSION['anno']);
$sql    =  "SELECT fa.id_unico, fa.numero, 
        DATE_FORMAT(fa.fecha_ven, '%d/%m/%Y'), a.consecutivo, fa.observaciones,
        SUM(df.valor)as valor, COUNT(df.iddetallerecaudo) 
    FROM ga_factura_acuerdo fa 
    LEFT JOIN ga_detalle_factura df on df.factura=fa.id_unico
    LEFT JOIN ga_detalle_acuerdo da on da.id_unico=df.detalleacuerdo
    LEFT JOIN ga_acuerdo a on a.id_unico=da.acuerdo 
    WHERE YEAR(fa.fecha_ven) = '$nanno' group by fa.id_unico ";
$resultado = $mysqli->query($sql);?>
    <title>Listar Facturas Acuerdo</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Facturas Acuerdos de Pago</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Número</strong></td>
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Acuerdo</strong></td>
                                        <td class="cabeza"><strong>Observaciones</strong></td>
                                        <td class="cabeza"><strong>Valor</strong></td>                                        
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Número</th>
                                        <th class="cabeza">Fecha</th>
                                        <th class="cabeza">Acuerdo</th>
                                        <th class="cabeza">Observaciones</th>
                                        <th class="cabeza">Valor</th>                                        
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        $idf    = $row[0];
                                        $numf   = $row[1];
                                        $fec    = $row[2];
                                        $acu    = $row[3];
                                        $obs    = $row[4];
                                        $valor  = $row[5];
                                        ?>
                                    <tr>
                                        <td class="campos" style="display: none;"><?php echo $row[0]?></td>
                                        <td class="campos">
                                            
                                            <a class="campos" href="ver_GA_FACTURA_ACUERDO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Ver Detalle" class="glyphicon glyphicon-eye-open" ></i>
                                            </a>
                                            <a class="campos" href="informes/INF_FACTURA_ACUERDO.php?id=<?php echo md5($row[0]);?>" target="_blank">
                                                <i title="Imprimir" class="glyphicon glyphicon-print" ></i>
                                            </a>
                                            <?php if($row[6]>0){}else{
                                                echo '<a class="campos" href="javaScript:eliminar('.$row[0].')"><i title="Eliminar Factura" class="glyphicon glyphicon-trash"></i></a>';
                                            }
                                            ?>
                                        </td>                                        
                                        <td class="campos text-right"><?= $numf?></td>                
                                        <td class="campos"><?= $fec?></td>                
                                        <td class="campos"><?= $acu?></td>                
                                        <td class="campos text-right"><?= $obs?></td>                
                                        <td class="campos text-right"><?= number_format($valor, 2, '.', ',')?></td>                                                                   
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
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
        function eliminar(id){
            let result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarAcuerdo.php?id="+id+'&action=3',
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