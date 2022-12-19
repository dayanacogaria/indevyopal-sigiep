<?php
require_once ('Conexion/conexion.php');

$resul = "SELECT id_unico, sigla, nombre 
         FROM gf_tipo_cambio";
$resultado = $mysqli->query($resul);

require_once 'head_listar.php';
?>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<title>Tipo Cambio</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Tipo Cambio</h2>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Sigla</strong></td>
                                    <td><strong>Nombre</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Sigla</th>
                                    <th>Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_row($resultado)) {
                                    echo "<tr>";
                                    echo "<td class='oculto'></td>";
                                    echo "<td style='background-color:transparent;'>
                                        <a  href='javascript:eliminar(\"".md5($row[0])."\")' title='Eliminar' >
                                            <li class='glyphicon glyphicon-trash'></li>
                                        </a>
                                        <a  href='Modificar_GF_TIPO_CAMBIO.php?id=".md5($row[0])."' title='Actualizar'>
                                            <li class='glyphicon glyphicon-edit'></li>
                                        </a>
                                    </td>";
                                    echo "<td class='campos'>$row[1]</td>";
                                    echo "<td class='campos'>$row[2]</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 text-right">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <a href="Registrar_GF_TIPO_CAMBIO.php" class="btn btn-primary borde-sombra"  style="margin-top: 5px;">Registrar Nuevo</a>
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
    <script type="text/javascript" src="./js/select2.js"></script>
    <script src="./js/script.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script src="./js/jquery-ui.js"></script>
    <script>
        function eliminar(id){
            $("#mdleliminar").modal('show');
            $("#btneliminar").click(function(){
                window.location='jsonPptal/registrar_GF_Tipo_CambioJson.php?id=' + id + '&action=3&table=1';
            });
        }
    </script>
</body>
</html>
