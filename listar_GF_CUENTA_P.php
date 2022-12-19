<?php 
##########################################################################################
# *********************************** Modificaciones *********************************** # 
##########################################################################################
#05/02/2018 | Erica G. | Equivalente Vigencia Anterior
##########################################################################################
#Llamamos a la clase de conexión
require_once ('Conexion/conexion.php');
require_once ('head_listar.php');
require_once './Conexion/ConexionPDO.php';
require_once './jsonPptal/funcionesPptal.php';
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
$nanno2 = $nanno-1;
$cann2 = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = $nanno2");
if(count($cann2)>0){
    $anno2 = $cann2[0][0];
} else {
    $anno2 = 0;
}

#Iniciamos la sesion
#Consulta de carga de datos 
$sql = "SELECT RP.id_unico,
       RP.codi_cuenta,
       RP.nombre,       
       RP.movimiento,
       RP.centrocosto,
       RP.auxiliartercero,
       RP.auxiliarproyecto,
       RP.activa,
       RP.dinamica,
       pre.codi_cuenta,
       NT.id_unico,
       NT.nombre,
       TPC.id_unico,
       TPC.nombre,
       CC.id_unico,
       CC.nombre, 
       cva.codi_cuenta, LOWER(cva.nombre) 
  FROM gf_cuenta RP  
  LEFT JOIN gf_naturaleza NT ON RP.naturaleza = NT.id_unico
  LEFT JOIN gf_tipo_cuenta_cgn TPC ON RP.tipocuentacgn = TPC.id_unico
  LEFT JOIN gf_clase_cuenta CC ON RP.clasecuenta = CC.id_unico 
  LEFT JOIN gf_cuenta pre ON RP.predecesor = pre.id_unico 
  LEFT JOIN gf_cuenta cva ON cva.codi_cuenta = RP.equivalente_va AND cva.parametrizacionanno = $anno2   
  WHERE RP.parametrizacionanno = $anno 
  ORDER BY RP.codi_cuenta ASC";
#Cargamos la consulta en la variable de conexiòn y definimos la variable pptal 
$cuenta = $mysqli->query($sql);
#Llamamos a la cabeza del formulario


?>
<style type="text/css">
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:10px}
    .dataTables_wrapper .ui-toolbar{padding:5px}
    .campoD:focus {
        border-color: #66afe9;
        outline: 0;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
    }
</style>
        <title>Listar Cuenta</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row content">
                <?php require_once ('menu.php'); ?>
                <div class="col-sm-10 text-left" style="margin-top: -20px">
                    <h2 class="titulolista" align="center">Plan Contable</h2>
                    <div class="table-responsive contTabla">
                        <div class="table-responsive contTabla">
                            <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="oculto">Identificador</td>
                                        <td width="7%"></td>                                        
                                        <td class="cabeza"><strong>Código Cuenta</strong></td>
                                        <td class="cabeza"><strong>Nombre</strong></td>
                                        <td class="cabeza"><strong>Movimiento</strong></td>
                                        <td class="cabeza"><strong>Centro Costo</strong></td>
                                        <td class="cabeza"><strong>Auxiliar Tercero</strong></td>
                                        <td class="cabeza"><strong>Auxiliar Proyecto</strong></td>
                                        <td class="cabeza"><strong>Activa</strong></td>
                                        <td class="cabeza"><strong>Dinamica</strong></td>
                                        <td class="cabeza"><strong>Naturaleza</strong></td>
                                        <td class="cabeza"><strong>Predecesor</strong></td>
                                        <td class="cabeza"><strong>Tipo Cuenta Cgn</strong></td>
                                        <td class="cabeza"><strong>Clase Cuenta</strong></td>                                        
                                        <td class="cabeza"><strong>Equivalente Vigencia Anterior</strong></td>                                        
                                    </tr>
                                    <tr>
                                        <th class="oculto">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Código Cuenta</th>
                                        <th>Nombre</th>                                        
                                        <th>Movimiento</th>                                        
                                        <th>Centro Costo</th>
                                        <th>Auxiliar Tercero</th>
                                        <th>Auxiliar Proyecto</th>
                                        <th>Activa</th>
                                        <th>Dinamica</th>
                                        <th>Naturaleza</th>
                                        <th>Predecesor</th>
                                        <th>Tipo Cuenta Cgn</th>
                                        <th>Clase Cuenta</th>  
                                        <th>Equivalente Vigencia Anterior</th>  
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($fila = mysqli_fetch_row($cuenta)) { ?>
                                        <tr>
                                            <td class="oculto"></td>
                                            <td class="campos">
                                                <a href="#" onclick="javascript:eliminar(<?php echo $fila[0] ?>)">
                                                    <li title="Eliminar" class="glyphicon glyphicon-trash"></li>
                                                </a>
                                                <a href="modificar_GF_CUENTA_P.php?id=<?php echo md5($fila[0]); ?>">
                                                    <li title="Modificar" class="glyphicon glyphicon-edit"></li>
                                                </a>
                                            </td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[1])); ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[2])); ?></td>
                                            <td class="campos"><?php 
                                                switch ($fila[3]){
                                                    case 1:
                                                        echo 'SI';
                                                        break;
                                                    case 2:
                                                        echo 'NO';
                                                        break;
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
                                                switch ($fila[5]){
                                                    case 1:
                                                        echo 'SI';
                                                        break;
                                                    case 2:
                                                        echo 'NO';
                                                        break;
                                                }
                                            ?></td>
                                            <td class="campos"><?php 
                                                switch ($fila[6]){
                                                    case 1:
                                                        echo 'SI';
                                                        break;
                                                    case 2:
                                                        echo 'NO';
                                                        break;
                                                }
                                            ?></td>
                                            <td class="campos"><?php 
                                                switch ($fila[7]){
                                                    case 1:
                                                        echo 'SI';
                                                        break;
                                                    case 2:
                                                        echo 'NO';
                                                        break;
                                                }
                                            ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[8])); ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[11])); ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[9])); ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[13])); ?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower($fila[15])); ?></td>                                                                                       
                                            <td class="campos"><?php echo $fila[16].' - '.ucwords(mb_strtolower($fila[17])); ?></td>                                                                                       
                                        </tr>
                                    <?php    
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GF_CUENTA_P.php" class="btn btn-primary btnNuevoLista" style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">
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
                        url: "json/eliminarClaseP.php?id=" + id,
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
            document.location = 'listar_GF_CUENTA_P.php';
        });
    </script>
    </body>
</html>
