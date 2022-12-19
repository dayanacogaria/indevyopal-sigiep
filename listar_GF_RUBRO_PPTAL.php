<?php 
###########################################################################################################
#                           MODIFICACIONES 
###########################################################################################################                          
#06/07/2017 | ERICA G. | PARAMETRIZACION                            
#10/04/2017 | Erica G. | Diseño, tíldes, búsquedas  
###########################################################################################################
#Llamamos a la clase de conexión
require_once ('Conexion/conexion.php');
#Llamamos a la cabeza del formulario
require_once ('head_listar.php');
#Consulta de carga de datos 
$param = $_SESSION['anno'];
$sql = "SELECT RP.id_unico,
       RP.nombre,
       RP.codi_presupuesto,
       RP.movimiento,
       RP.manpac,
       RP.vigencia,
       RP.dinamica,
       CPT.id_unico,
       CPT.nombre,
       rpp.codi_presupuesto,
       DT.id_unico,
       DT.nombre,
       TV.id_unico,
       TV.nombre,
       SC.id_unico,
       SC.nombre, RP.equivalente 
  FROM gf_rubro_pptal RP
  LEFT JOIN gf_tipo_clase_pptal CPT ON RP.tipoclase = CPT.id_unico
  LEFT JOIN gf_destino DT ON RP.destino = DT.id_unico
  LEFT JOIN gf_tipo_vigencia TV ON RP.tipovigencia = TV.id_unico
  LEFT JOIN gf_sector SC ON RP.sector = SC.id_unico 
  LEFT JOIN gf_rubro_pptal rpp ON RP.predecesor = rpp.id_unico 
  WHERE RP.parametrizacionanno = $param 
  ORDER BY RP.codi_presupuesto ASC";
#Cargamos la consulta en la variable de conexiòn y definimos la variable pptal
$pptal = $mysqli->query($sql);

?>
        <title>Listar Rubro Presupuestal</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row content">
                <?php require_once ('menu.php'); ?>
                <div class="col-sm-10 text-left" style="margin-top: -20px">
                    <h2 class="titulolista" align="center">Rubro Presupuestal</h2>
                    <div class="table-responsive contTabla">
                        <div class="table-responsive contTabla">
                            <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="oculto">Identificador</td>
                                        <td width="7%"></td>
                                        <td class="cabeza"><strong>Código Presupuesto</strong></td>
                                        <td class="cabeza"><strong>Nombre</strong></td>
                                        <td class="cabeza"><strong>Movimiento</strong></td>
                                        <td class="cabeza"><strong>Maneja PAC</strong></td>
                                        <td class="cabeza"><strong>Vigencia</strong></td>
                                        <td class="cabeza"><strong>Dinamica</strong></td>
                                        <td class="cabeza"><strong>Tipo Clase</strong></td>
                                        <td class="cabeza"><strong>Predecesor</strong></td>
                                        <td class="cabeza"><strong>Destino</strong></td>
                                        <td class="cabeza"><strong>Tipo Vigencia</strong></td>
                                        <td class="cabeza"><strong>Sector</strong></td>
                                        <td class="cabeza"><strong>Equivalente</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="oculto">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Código Presupuesto</th>
                                        <th>Nombre</th>
                                        <th>Movimiento</th>
                                        <th>Maneja PAC</th>
                                        <th>Vigencia</th>
                                        <th>Tipo Clase</th>
                                        <th>Dinamica</th>
                                        <th>Predecesor</th>
                                        <th>Destino</th>
                                        <th>Tipo Vigencia</th>
                                        <th>Sector</th>
                                        <th>Equivalente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($fila = mysqli_fetch_row($pptal)) { ?>
                                        <tr>
                                            <td class="oculto"></td>
                                            <td class="campos">
                                                <a href="#" onclick="javascript:eliminar(<?php echo $fila[0] ?>)">
                                                    <li title="Eliminar" class="glyphicon glyphicon-trash"></li>
                                                </a>
                                                <a href="modificar_GF_RUBRO_PPTAL.php?id=<?php echo md5($fila[0]); ?>">
                                                    <li title="Modificar" class="glyphicon glyphicon-edit"></li>
                                                </a>
                                            </td>
                                            <td class="campos"><?php echo ucwords(($fila[2])); ?></td>
                                            <td class="campos"><?php echo ucwords(ucwords(mb_strtolower($fila[1]))); ?></td>
                                            <td class="campos"><?php 
                                                if ($fila[3] == 1) {
                                                    echo 'SI';
                                                }elseif ($fila[3] == 2) {
                                                    echo 'NO';
                                                }
                                            ?></td>
                                            <td class="campos"><?php 
                                                switch ($fila[4]){
                                                    case 1:
                                                        echo 'SI';
                                                        break;
                                                    case 2:
                                                        echo 'NO';
                                                        break;
                                                }
                                            ?></td>
                                            <td class="campos"><?php 
                                                $sql1="select anno from gf_parametrizacion_anno where id_unico=$fila[5]";
                                                $result100=$mysqli->query($sql1);
                                                if(mysqli_num_rows($result100)>0){
                                                $vigencia = mysqli_fetch_row($result100);
                                                echo ucwords(($vigencia[0])); 
                                                } else {
                                                    echo '';
                                                }
                                                ?>                                                
                                            </td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[6])); ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[8])); ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[9])); ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[11])); ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[13])); ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[15])); ?></td>
                                            <td class="campos"><?php if($fila[16] =='null') { } else { echo ucwords(mb_strtolower($fila[16])); }?></td>
                                        </tr>
                                    <?php    
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GF_RUBRO_PPTAL.php" class="btn btn-primary btnNuevoLista" style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">
                                    Registrar Nuevo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once ('footer.php'); ?>
        
        <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Rubro Presupuestal?</p>
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
                    <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/menu.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function eliminar(id){
            var result = '';
            $("#myModal").modal('show');
                $("#ver").click(function () {
                    $("#mymodal").modal('hide');
                    $.ajax({
                        type: "GET",
                        url: "json/eliminarRubroPptalJson.php?id=" + id,
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
        function modal(){
            $("#myModal").modal('show');
        }
    </script> 
    <script type="text/javascript">
        $('#ver1').click(function () {
            document.location = 'listar_GF_RUBRO_PPTAL.php';
        });
    </script>
    </body>
</html>
