<?php
require './Conexion/conexion.php';
require './head_listar.php';
?>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/jquery.datetimepicker.css">
    <link rel="stylesheet" href="css/desing.css">
    <title>Listar tarifa</title>
    <style>
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:12px;}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px;}
        .dataTables_wrapper .ui-toolbar{padding:2px;}
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
        <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin: -2px 4px 20px;">Tarifa</h2>                
                <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="cabeza oculto"></td>
                                        <td class="cabeza" width="30px" align="center"></td>
                                        <td class="cabeza"><strong>Nombre</strong></td>
                                        <th class="cabeza">Valor</th>
                                        <th class="cabeza">Unidad Medida</th>
                                        <td class="cabeza"><strong>Desviación</strong></td>
                                        <td class="cabeza"><strong>Intervalo</strong></td>
                                        <td class="cabeza"><strong>Tarifa Asociada</strong></td>
                                        <td class="cabeza"><strong>Tipo Vehiculo</strong></td>

                                    </tr>
                                    <tr>
                                        <th class="cabeza oculto"></th>
                                        <th class="cabeza" width="7%"></th>
                                        <th class="cabeza"></th>
                                        <th class="cabeza"></th>
                                        <th class="cabeza"></th>
                                        <th class="cabeza"></th>
                                        <th class="cabeza"></th>
                                        <th class="cabeza"></th>
                                        <th class="cabeza"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $html = "";
                                    $sqlingresos = "SELECT 
                                        fc.*, vh.nombre, aso.nombre
                                        FROM gq_fraccion fc
                                        LEFT JOIN gp_tipo_vehiculo vh ON fc.tipo_vehiculo = vh.id_unico
                                        LEFT JOIN gq_fraccion aso ON fc.tarifa_asociada = aso.id_unico";
                                        $resingresos = $mysqli->query($sqlingresos);
                                        while ($row = mysqli_fetch_row($resingresos)) {
                                        $html .= "<tr>";
                                        $html .= "<td class='oculto'></td>";
                                        $html .= "<td>";
                                        $html .= "<a class='campos' href='javascript:eliminar(\"".md5($row[0])."\",3)' title='Eliminar'><span class='glyphicon glyphicon-trash'></span></a>";
                                        $html .= "<a class='campos' href='Modificar_GQ_TARIFA.php?id=".md5($row[0])."' title='Modificar'><span class='glyphicon glyphicon-edit'></span></a>";
                                        $html .= "</td>";
                                        $html .= "<td>$row[1]</td>";
                                        $html .= "<td>".number_format($row[2], 2, ',', '.')."</td>";
                                        $html .= "<td>$row[3]</td>";
                                        $html .= "<td>$row[4]</td>";
                                        $html .= "<td>$row[5]</td>";
                                        $html .= "<td>$row[9]</td>";
                                        $html .= "<td>$row[8]</td>";
                                        $html .= "</tr>";
                                    }
                                    echo $html;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 text-right">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <a href="Registrar_GQ_TARIFA.php" class="btn btn-primary borde-sombra"  style="margin-top: 5px;">Registrar Nuevo</a>
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
    <?php require_once ('footer.php'); ?>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/script.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript">
        function eliminar(id, action){
            let tarifa = id;
            let act = action;
            $("#mdleliminar").modal('show');
            $("#btneliminar").click(function(){
                window.location='json/registrar_GQ_TARIFAJson.php?id=' + tarifa + '&action=' + act;
            });
        }

        $('#ver1').click(function(){
            document.location.reload();
        });

        $('#ver2').click(function(){
            document.location.reload();
        });
    </script>
</body>
</html>


