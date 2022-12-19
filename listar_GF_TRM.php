<?php
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
require_once('jsonPptal/funcionesPptal.php');
require_once 'head_listar.php';
$parmanno   = $_SESSION['anno'];
$anno       = anno($parmanno);
$con        = new ConexionPDO();
$resul      = "SELECT tc.id_unico, tc.nombre, trm.id_unico, trm.valor, DATE_FORMAT(trm.fecha,'%d/%m/%Y'), 
        trm.fecha
         FROM gf_trm trm
         LEFT JOIN gf_tipo_cambio tc ON trm.tipo_cambio = tc.id_unico
         WHERE YEAR(trm.fecha) = '$anno' 
         ORDER BY trm.fecha DESC";
$resultado = $mysqli->query($resul);


?>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/jquery.datetimepicker.css">
<link rel="stylesheet" href="css/desing.css">
<title>Listar TRM</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">TRM</h2>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Tipo Cambio</strong></td>
                                    <td><strong>Valor</strong></td>
                                    <td><strong>Fecha</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tipo Cambio</th>
                                    <th>Valor</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_row($resultado)) {
                                    echo "<tr>";
                                    echo "<td class='oculto'></td>";

                                    $nf = $con->Listar("SELECT * FROM gp_factura f 
                                        WHERE  f.fecha_factura = '".$row[5]."' AND f.id_unico IN (SELECT factura FROM gp_detalle_factura) AND f.tipo_cambio = '".$row[0]."'");
                                    if(count($nf)>0){
                                        echo '<td></td>';
                                    }else{
                                        echo "<td style='background-color:transparent;'>
                                            <a  href='javascript:eliminar(\"".md5($row[2])."\")' title='Eliminar' >
                                                <li class='glyphicon glyphicon-trash'></li>
                                            </a>
                                            <a  href='Modificar_TRM.php?id=".md5($row[2])."' title='Actualizar'>
                                                <li class='glyphicon glyphicon-edit'></li>
                                            </a>
                                        </td>"; 
                                    }
                                    echo "<td class='campos'>$row[1]</td>";
                                    echo "<td class='campos'>".number_format($row[3], 2, '.', ',')."</td>";
                                    echo "<td class='campos'>$row[4]</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 text-right">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <a href="Registrar_TRM.php" class="btn btn-primary borde-sombra"  style="margin-top: 5px;">Registrar Nuevo</a>
                        </div>
                    </div>
                </div>
            </div>            
        </div>        
    </div>    
    <div class="modal fade" id="mdleliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <p>¿Desea eliminar el registro seleccionado de tarifa?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btneliminar" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div> 
    <?php require_once './footer.php'; ?>
    <script src="js/script_modal.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="js/script_date.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script src="js/script.js"></script>
    <script>
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });
        });
        
        function eliminar(id){
            $("#mdleliminar").modal('show');
            $("#btneliminar").click(function(){
                window.location='jsonPptal/registrar_GF_Tipo_CambioJson.php?id=' + id + '&action=3&table=2';
            });
        }
    </script>
</body>
</html>
