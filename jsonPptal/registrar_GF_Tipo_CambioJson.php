<?php
#############File by David Padilla 31/05/2019#######################
require_once('../Conexion/ConexionPDO.php');
require_once('../Conexion/conexion.php');
session_start();
$con     = new ConexionPDO();
$anno    = $_SESSION['anno'];
$usuario = $_SESSION['id_usuario'];
date_default_timezone_set('America/Bogota');
$getdate = date("Y-m-d");
$ajax    = 0;
$msj     = '';
$action  = 0;
$table   = 0;
$view    = '';
if (!empty($_POST['action'])) {
    $action = $_POST['action'];
    $table  = $_POST['table'];
} else {
    $action = $_GET['action'];
    $table  = $_GET['table'];
}
switch ($table) {
    case 1: #table gf_tipo_cambio
        $view = 'listar_GF_TIPO_CAMBIO';
        switch ($action) {
            case 1: #Insert        
                $nombre = $_POST['txtNombre'];
                $sigla  = $_POST['txtSigla'];
                $sqlinsert = "INSERT INTO gf_tipo_cambio VALUES (NULL,'$nombre', '$sigla')";
                $resultado = $mysqli->query($sqlinsert);
                break;
            case 2: #Update
                $id = $_POST['idx'];
                $sigla  = $_POST['txtSiglax'];
                $nombre = $_POST['txtNombrex'];
                $sqlupdate = "
                UPDATE gf_tipo_cambio SET
                nombre          = '$nombre', 
                sigla          = '$sigla'   
                WHERE id_unico  = $id";
                $resultado = $mysqli->query($sqlupdate);
                break;
            case 3: #Delete
                $id = $_GET['id'];
                $sqlinsert = "DELETE FROM gf_tipo_cambio WHERE md5(id_unico) = '$id'";
                $resultado = $mysqli->query($sqlinsert);
                break;
        }
        break;
    case 2:  #table gf_trm
        $view = 'listar_GF_TRM';
        switch ($action) {        
            case 1: #Insert        
                $tipo_c = $_POST['slttipo_c'];
                $valor  = $_POST['txtValor'];
                $fecha  = $_POST['txtFecha'];
                $ff = explode("/", $fecha);
                $xx = explode(" ", $ff[2]);
                $fecha = trim($xx[0]) ."-$ff[1]-$ff[0]";
                $sqlinsert = "INSERT INTO gf_trm VALUES (NULL,$tipo_c, $valor, '$fecha')";
                $resultado = $mysqli->query($sqlinsert);
                if ($resultado){
                    $ip   = $_SERVER['REMOTE_ADDR'];
                    $host = gethostname();
                    #Columns
                    $sqlcol = $con->Listar("
                    SELECT COLUMN_NAME
                    FROM information_schema.columns 
                    WHERE table_name = 'gf_trm'");
                    $col0 = $sqlcol[0][0];  #id_unico
                    $col1 = $sqlcol[1][0];  #tipo_cambio
                    $col2 = $sqlcol[2][0];  #valor
                    $col3 = $sqlcol[3][0];  #fecha
                    
                    #Max(id)
                    $sqltrmid = $con->Listar("
                    SELECT MAX(id_unico)
                    FROM gf_trm");
                    $id = $sqltrmid[0][0];
                    #Datos
                    $sqltrm = $con->Listar("
                    SELECT *
                    FROM gf_trm
                    WHERE id_unico = $id");
                    $trm0 = $sqltrm[0][0];  #id_unico
                    $trm1 = $sqltrm[0][1];  #tipo_cambio
                    $trm2 = $sqltrm[0][2];  #valor
                    $trm3 = $sqltrm[0][3];  #fecha

                    #Insert id_unico
                    $sqlinsert = " 
                    INSERT INTO gs_auditoria VALUES (NULL,
                    'gf_trm', '$col0', '$trm0', 
                    '$host', '$getdate',
                    'Registrar', '$trm0', 'NA', '$ip', 
                    '$host', $usuario, 'Registrado por Sigiep')";
                    $into = $mysqli->query($sqlinsert);
                    #Insert tipo_cambio
                    $sqlinsert = " 
                    INSERT INTO gs_auditoria VALUES (NULL,
                    'gf_trm', '$col1', '$trm0', 
                    '$host', '$getdate',
                    'Registrar', '$trm1', 'NA', '$ip', 
                    '$host', $usuario, 'Registrado por Sigiep')";
                    $into = $mysqli->query($sqlinsert);
                    #Insert valor
                    $sqlinsert = " 
                    INSERT INTO gs_auditoria VALUES (NULL,
                    'gf_trm', '$col2', '$trm0', 
                    '$host', '$getdate',
                    'Registrar', '$trm2', 'NA', '$ip', 
                    '$host', $usuario, 'Registrado por Sigiep')";
                    $into = $mysqli->query($sqlinsert);
                    #Insert fecha
                    $sqlinsert = " 
                    INSERT INTO gs_auditoria VALUES (NULL,
                    'gf_trm', '$col3', '$trm0', 
                    '$host', '$getdate',
                    'Registrar', '$trm3', 'NA', '$ip', 
                    '$host', $usuario, 'Registrado por Sigiep')";
                    $into = $mysqli->query($sqlinsert);
                }
                break;
            case 2: #Update
                $id = $_POST['idx'];
                $tipo_c = $_POST['slttipo_cx'];
                $valor = $_POST['txtValorx'];
                $fecha = $_POST['txtFechax'];
                $ff = explode("/", $fecha);
                $xx = explode(" ", $ff[2]);
                $fecha = trim($xx[0]) ."-$ff[1]-$ff[0]";    
                
                $ip   = $_SERVER['REMOTE_ADDR'];
                $host = gethostname();
                #Columns
                $sqlcol = $con->Listar("
                SELECT COLUMN_NAME
                FROM information_schema.columns 
                WHERE table_name = 'gf_trm'");
                $col0 = $sqlcol[0][0];  #id_unico
                $col1 = $sqlcol[1][0];  #tipo_cambio
                $col2 = $sqlcol[2][0];  #valor
                $col3 = $sqlcol[3][0];  #fecha

                #Datos
                $sqltrm = $con->Listar("
                SELECT *
                FROM gf_trm
                WHERE id_unico = $id");
                $trm0 = $sqltrm[0][0];  #id_unico
                $trm1 = $sqltrm[0][1];  #tipo_cambio
                $trm2 = $sqltrm[0][2];  #valor
                $trm3 = $sqltrm[0][3];  #fecha

                #Insert id_unico
                $sqlinsert = " 
                INSERT INTO gs_auditoria VALUES (NULL,
                'gf_trm', '$col0', '$trm0', 
                '$host', '$getdate',
                'Actualizar', '$trm0', '$id', '$ip', 
                '$host', $usuario, 'Actualizado por Sigiep')";
                $into = $mysqli->query($sqlinsert);
                #Insert tipo_cambio
                $sqlinsert = " 
                INSERT INTO gs_auditoria VALUES (NULL,
                'gf_trm', '$col1', '$trm0', 
                '$host', '$getdate',
                'Actualizar', '$trm1', '$tipo_c', '$ip', 
                '$host', $usuario, 'Actualizado por Sigiep')";
                $into = $mysqli->query($sqlinsert);
                #Insert valor
                $sqlinsert = " 
                INSERT INTO gs_auditoria VALUES (NULL,
                'gf_trm', '$col2', '$trm0', 
                '$host', '$getdate',
                'Actualizar', '$trm2', '$valor', '$ip', 
                '$host', $usuario, 'Actualizado por Sigiep')";
                $into = $mysqli->query($sqlinsert);
                #Insert fecha
                $sqlinsert = " 
                INSERT INTO gs_auditoria VALUES (NULL,
                'gf_trm', '$col3', '$trm0', 
                '$host', '$getdate',
                'Actualizar', '$trm3', '$fecha', '$ip', 
                '$host', $usuario, 'Actualizado por Sigiep')";
                $into = $mysqli->query($sqlinsert);
                $sqlupdate = "
                UPDATE gf_trm SET
                tipo_cambio = $tipo_c,
                valor       = $valor,
                fecha       = '$fecha'
                WHERE id_unico  = $id";
                $resultado = $mysqli->query($sqlupdate);
                break;
            case 3: #Delete
                $id = $_GET['id'];
                $ip   = $_SERVER['REMOTE_ADDR'];
                $host = gethostname();
                #Columns
                $sqlcol = $con->Listar("
                SELECT COLUMN_NAME
                FROM information_schema.columns 
                WHERE table_name = 'gf_trm'");
                $col0 = $sqlcol[0][0];  #id_unico
                $col1 = $sqlcol[1][0];  #tipo_cambio
                $col2 = $sqlcol[2][0];  #valor
                $col3 = $sqlcol[3][0];  #fecha

                #Datos
                $sqltrm = $con->Listar("
                SELECT *
                FROM gf_trm
                WHERE md5(id_unico) = '$id'");
                $trm0 = $sqltrm[0][0];  #id_unico
                $trm1 = $sqltrm[0][1];  #tipo_cambio
                $trm2 = $sqltrm[0][2];  #valor
                $trm3 = $sqltrm[0][3];  #fecha
                
                #Insert id_unico
                $sqlinsert = " 
                INSERT INTO gs_auditoria VALUES (NULL,
                'gf_trm', '$col0', '$trm0', 
                '$host', '$getdate',
                'Eliminar', '$trm0', 'NA', '$ip', 
                '$host', $usuario, 'Eliminado por Sigiep')";
                $into = $mysqli->query($sqlinsert);
                #Insert tipo_cambio
                $sqlinsert = " 
                INSERT INTO gs_auditoria VALUES (NULL,
                'gf_trm', '$col1', '$trm0', 
                '$host', '$getdate',
                'Eliminar', '$trm1', 'NA', '$ip', 
                '$host', $usuario, 'Eliminado por Sigiep')";
                $into = $mysqli->query($sqlinsert);
                #Insert valor
                $sqlinsert = " 
                INSERT INTO gs_auditoria VALUES (NULL,
                'gf_trm', '$col2', '$trm0', 
                '$host', '$getdate',
                'Eliminar', '$trm2', 'NA', '$ip', 
                '$host', $usuario, 'Eliminado por Sigiep')";
                $into = $mysqli->query($sqlinsert);
                #Insert fecha
                $sqlinsert = " 
                INSERT INTO gs_auditoria VALUES (NULL,
                'gf_trm', '$col3', '$trm0', 
                '$host', '$getdate',
                'Eliminar', '$trm3', 'NA', '$ip', 
                '$host', $usuario, 'Eliminado por Sigiep')";
                $into = $mysqli->query($sqlinsert);
                $sqldelete = "DELETE FROM gf_trm WHERE md5(id_unico) = '$id'";
                $resultado = $mysqli->query($sqldelete);
                break;
            case 4: #Consulta para traer datos y llenar el select
                $ajax = 1;
                $id = $_POST['id'];
                $sql = $con->Listar("SELECT cb.*, trm.id_unico, trm.valor, DATE_FORMAT(trm.fecha, '%d/%m/%Y')
                        FROM gf_trm trm 
                        LEFT JOIN gf_tipo_cambio cb ON trm.tipo_cambio = cb.id_unico
                        WHERE md5(trm.id_unico) = '$id'");
                $idtc = $sql[0][0];
                $nmtc = $sql[0][1];
                $idtrm = $sql[0][2];
                $valor = $sql[0][3];
                $fecha = $sql[0][4];
                $sqltipoc = "SELECT * FROM gf_tipo_cambio WHERE id_unico !=  $idtc";
                $restipoc = $mysqli->query($sqltipoc);
                $datatipo = $restipoc->fetch_all(MYSQLI_NUM);
                $datos = array("idtc" => $idtc, "nmtc" => $nmtc, "idtrm" => $idtrm, "valor" => $valor, "fecha" => $fecha,"datatipo" => $datatipo);
                echo json_encode($datos);
                break;
        }
        break;
}
if ($ajax == 0){
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
        <script src="../js/md5.pack.js"></script>
        <script src="../js/jquery.min.js"></script>
        <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
        <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
    </head>
</html>
<div class="modal fade" id="mdlinfo" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p id="pinfo"></p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!--<script type="text/javascript" src="../js/menu.js"></script>-->
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.js"></script>
<?php
if ($action == 1) { # Insert
    if ($resultado == true) {
        $msj = "Informaci&oacuten guardada correctamente.";
    } else {
        $msj = "No se ha podido guardar la informaci&oacuten.";
    }
} else if ($action == 2) { #Update
    if ($resultado == true) {
        $msj = "Informaci&oacuten modificada correctamente.";
    } else {
        $msj = "No se ha podido modificar la informaci&oacuten.";
    }
} else if ($action == 3) { #Delete
    if ($resultado == true) {
        $msj = "Informaci&oacuten eliminada correctamente.";
    } else {
        $msj = "No se ha podido eliminar la informaci&oacuten.";
    }
}
?>
<script type="text/javascript">
    let response = "<?php echo $msj ?>";
    let view = "<?php echo $view ?>";    
    $("#pinfo").html(response);
    $("#mdlinfo").modal('show');
    $("#ver1").click(function () {
        $("#mdlinfo").modal('hide');
        window.location = '../'+view+'.php';
    });
</script>
<?php }?>