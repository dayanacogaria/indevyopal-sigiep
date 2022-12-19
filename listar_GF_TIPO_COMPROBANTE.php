<?php
require_once('Conexion/conexion.php');
require_once 'head_listar.php';
$compania = $_SESSION['compania'];
$querytipoC = "SELECT   tc.id_unico, 
        tc.nombre, 
        tc.retencion, 
        tc.interface, 
        tc.niif, 
        tc.clasecontable, 
        cc.nombre, 
        tc.tipodocumento, 
        f.nombre,
        tc.sigla,
        tpcp.nombre,
        tpcp.codigo,
        tc.comprobante_pptal,
        tpc.nombre, tpc.sigla, 
        IF(tc.interfaz_predial=1, 'SI', 'NO'), 
        IF(tc.interfaz_comercio=1,'SI', 'NO'), 
        IF(tc.interfaz_reteica=1,'SI', 'NO'), 
        IF(tc.amortizacion=1,'SI', 'NO'), 
        IF(tc.traslado=1,'SI', 'NO'),
        IF(tc.interfaz_aportes=1,'SI', 'NO') 
    FROM          
        gf_tipo_comprobante tc 
    LEFT JOIN     
        gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
    LEFT JOIN     
        gf_tipo_documento f  ON tc.tipodocumento = f.id_unico
    LEFT JOIN     
        gf_tipo_comprobante_pptal tpcp ON tc.comprobante_pptal = tpcp.id_unico
    LEFT JOIN     
        gf_tipo_comprobante tpc ON tc.tipo_comp_hom = tpc.id_unico 
    WHERE 
        tc.compania = $compania";
$resultado = $mysqli->query($querytipoC);
?>
<title>Listar Tipo Comprobante Contable</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Tipo Comprobante Contable</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-15px">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="cabeza" style="display: none;">Identificador</td>
                                    <td class="cabeza" width="30px" align="center"></td>
                                    <td class="cabeza"><strong>Sigla</strong></td>
                                    <td class="cabeza"><strong>Nombre</strong></td>
                                    <td class="cabeza"><strong>Retención</strong></td>
                                    <td class="cabeza"><strong>Interfaz</strong></td>
                                    <td class="cabeza"><strong>NIIF</strong></td>
                                    <td class="cabeza"><strong>Clase Contable</strong></td>
                                    <td class="cabeza"><strong>Tipo Documento</strong></td>
                                    <td class="cabeza"><strong>Tipo Comprobante Presupuestal</strong></td>
                                    <td class="cabeza"><strong>Tipo Homologado</strong></td>
                                    <td class="cabeza"><strong>Interfaz Predial</strong></td>
                                    <td class="cabeza"><strong>Interfaz Comercio</strong></td>
                                    <td class="cabeza"><strong>Interfaz Reteica</strong></td>
                                    <td class="cabeza"><strong>Amortización</strong></td>
                                    <td class="cabeza"><strong>Traslado</strong></td>
                                    <td class="cabeza"><strong>Interfaz Aportes</strong></td>
                                </tr>
                                <tr>
                                    <th class="cabeza" style="display: none;">Identificador</th>
                                    <th class="cabeza" width="7%"></th>
                                    <th class="cabeza">Sigla</th>
                                    <th class="cabeza">Nombre</th>
                                    <th class="cabeza">Retención</th>
                                    <th class="cabeza">Interfaz</th>
                                    <th class="cabeza">NIIF</th>
                                    <th class="cabeza">Clase Contable</th>
                                    <th class="cabeza">Tipo Documento</th>
                                    <th class="cabeza">Tipo Comprobante Presupuestal</th>
                                    <th class="cabeza">Tipo Homologado</th>
                                    <th class="cabeza">Interfaz Predial</th>
                                    <th class="cabeza">Interfaz Comercio</th>
                                    <th class="cabeza">Interfaz Reteica</th>
                                    <th class="cabeza">Amortización</th>
                                    <th class="cabeza">Traslado</th>
                                    <th class="cabeza">Interfaz Aportes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_row($resultado)) { ?>
                                    <tr>
                                        <td class="campos" style="display: none;"><?php echo $row[0] ?></td>
                                        <td class="campos">
                                            <a class="campos" href="#" onclick="javascript:eliminarTipoC(<?php echo $row[0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a class="campos" href="modificar_GF_TIPO_COMPROBANTE.php?id=<?php echo md5($row[0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        </td>
                                        <td class="campos"><?php echo mb_strtoupper($row[9]) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[1])) ?></td>
                                        <td class="campos">
                                            <?php if (($row[2]) == 2) {
                                                echo "NO";
                                            } else {
                                                echo "SÍ";
                                            } ?> 
                                        </td>
                                        <td class="campos">
                                            <?php if (($row[3]) == 2) {
                                                echo "NO";
                                            } else {
                                                echo "SÍ";
                                            } ?>
                                        </td>
                                        <td class="campos">
                                            <?php if (($row[4]) == 2) {
                                                echo "NO";
                                            } else {
                                                echo "SÍ";
                                            } ?>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[6])) ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[8])) ?></td> 
                                        <td class="campos"><?php echo mb_strtoupper($row[11]) . '  ' . ucwords(mb_strtolower($row[10])) ?></td>                           
                                        <td class="campos"><?php echo mb_strtoupper($row[14]) . '  ' . ucwords(mb_strtolower($row[13])) ?></td>                           
                                        <td class="campos"><?php echo $row[15];?></td>
                                        <td class="campos"><?php echo $row[16];?></td>
                                        <td class="campos"><?php echo $row[17];?></td>
                                        <td class="campos"><?php echo $row[18];?></td>
                                        <td class="campos"><?php echo $row[19];?></td>
                                        <td class="campos"><?php echo $row[20];?></td>
                                    </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="registrar_GF_TIPO_COMPROBANTE.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px;  margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
                    </div>
                </div>
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
                    <p>¿Desea eliminar el registro seleccionado de Tipo Comprobante?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
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
    <div class="modal fade" id="myModal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>


<?php require_once 'footer.php'; ?>

    <script type="text/javascript" src="js/menu.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>

    <script type="text/javascript">
        function eliminarTipoC(id)
        {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $("#mymodal").modal('hide');
                $.ajax({
                    type: "GET",
                    url: "json/eliminarTipoComprobante.php?id=" + id,
                    success: function (data) {
                        result = JSON.parse(data);
                        if (result == true)
                            $("#myModal1").modal('show');
                        else
                            $("#myModal2").modal('show');
                    }
                });
            });
        }
    </script>
    <script type="text/javascript">
        function modal()
        {
            $("#myModal").modal('show');
        }
    </script>

    <script type="text/javascript">

        $('#ver1').click(function () {
            document.location = 'listar_GF_TIPO_COMPROBANTE.php';
        });

    </script>

    <script type="text/javascript">

        $('#ver2').click(function () {
            document.location = 'listar_GF_TIPO_COMPROBANTE.php';
        });

    </script>

</body>
</html>