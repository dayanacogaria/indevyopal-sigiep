<?php 
    require_once('../Conexion/conexion.php');
    session_start();

    $id = $_GET['id'];
    $flujoRelacionado="SELECT
              ef.nombre, fp.id_unico 
            FROM
              gg_detalle_proceso dp
            LEFT JOIN
              gg_flujo_procesal fp ON dp.flujo_procesal = fp.id_unico
            LEFT JOIN
              gg_fase f ON fp.fase = f.id_unico
            LEFT JOIN
              gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico
            WHERE
              dp.id_unico = '$id'";
    $flujoRelacionado = $mysqli->query($flujoRelacionado);
    $flujoRelacionado = mysqli_fetch_row($flujoRelacionado);
    $comp = strtolower($flujoRelacionado[0]);
    if($comp=='etapa especial'){
        $modFlujo = "UPDATE gg_flujo_procesal SET flujo_si=NULL WHERE id_unico = '$flujoRelacionado[1]'";
        $modFlujo =$mysqli->query($modFlujo);
    } else {
        $modFlujo=true;
    }
    $estado = "SELECT estadoA, proceso FROM gg_detalle_proceso WHERE id_unico ='$id'";
    $estado =$mysqli->query($estado);
    $estado =  mysqli_fetch_row($estado);
    if ($modFlujo ==true || $modFlujo==1 ){
        $cambEstado = "UPDATE gg_proceso SET estado ='$estado[0]' WHERE id_unico='$estado[1]'";
        $cambEstado=$mysqli->query($cambEstado);
        if ($cambEstado ==true || $cambEstado==1 ){
                $query = "DELETE FROM gg_detalle_proceso WHERE id_unico = $id";
                $resultado = $mysqli->query($query);
        } else { 
            $resultado = false;
        }
    } else {
        $resultado = false;
    }

    echo json_encode($resultado);
?>