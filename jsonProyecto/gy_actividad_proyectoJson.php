<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#19/11/218 | Nestor B. | Archivo Creado
####/################################################################################
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$anno       = $_SESSION['anno'];
$action     = $_REQUEST['action'];
switch ($action) {
    #   *** Eliminar Actividad Proyecto    ***  #
    case 1:
        $id      = $_REQUEST['id'];
        $existeSeguimiento = "SELECT * FROM gy_seguimiento WHERE id_actividad_proyecto = '$id'";
        $res = $mysqli->query($existeSeguimiento);
        $nexiste = mysqli_num_rows($res);
        
        if($nexiste < 1){
            $sql_cons ="DELETE FROM `gy_actividad_proyecto`
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                    array(":id_unico",$id),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                echo 1;
            } else {
                echo 2;
               // var_dump($obj_resp);
            }
        }else{
            echo 3;
        }
        
        
    break;
    #   *** Guardar la Actividad Proyecto    ***  #
    case 2:
        $proyecto   = $_REQUEST['txtpro'];
        $tipo_pro   = $_REQUEST['txttipo'];
        $actividad   = $_REQUEST['sltActi'];
        
        $fecha_inicio   = $_REQUEST['fechaini'];
        $fecha1 = trim($fecha_inicio, '"');
        $fecha_div = explode("/", $fecha1);
        $anio1 = $fecha_div[2];
        $mes1 = $fecha_div[1];
        $dia1 = $fecha_div[0];
        $fecha = ''.$anio1.'-'.$mes1.'-'.$dia1.''; 
        
        $fecha_fin   = $_REQUEST['fechafin'];
        $fecha2 = trim($fecha_fin, '"');
        $fecha_div2 = explode("/", $fecha2);
        $anio2 = $fecha_div2[2];
        $mes2 = $fecha_div2[1];
        $dia2 = $fecha_div2[0];
        $fecha2 = ''.$anio2.'-'.$mes2.'-'.$dia2.''; 
       
        $responsable   = $_REQUEST['sltTer'];
        $valor_p   = $_REQUEST['valorP'];
        $valor_p = str_replace(',', '', $valor_p);

        $valor_e   = $_REQUEST['valorE'];
        $valor_e = str_replace(',', '', $valor_e);
        
        $sum = "SELECT SUM(valor_programado) FROM gy_actividad_proyecto WHERE id_proyecto = '$proyecto' ";
        $suma = $mysqli->query($sum);
        $su = mysqli_fetch_row($suma);
        
        $vpt = $su[0] + $valor_p;
        #echo "valor T: ".$vpt;
        $montoT = "SELECT monto_total FROM gy_proyecto WHERE id_unico = '$proyecto'";
        $monto = $mysqli->query($montoT);
        $MT = mysqli_fetch_row($monto);
        #echo "MT: ".$MT[0];
        if($vpt <= $MT[0]){
            if($valor_p >= $valor_e){
                $sql_cons ="INSERT INTO `gy_actividad_proyecto`
                            ( `id_proyecto`, `id_tipo_proyecto`, `id_actividad`, `fecha_inicio_programada`, `fecha_final_programada`, `valor_programado`, `valor_ejecutado`, `responsable_actividad` )
                            VALUES (:proyecto,:tipo, :actividad, :fecha_inicio, :fecha_fin, :valor_p, :valor_e, :responsable)";
                $sql_dato = array(
                        array(":proyecto",$proyecto),
                        array(":tipo",$tipo_pro),
                        array(":actividad",$actividad),
                        array(":fecha_inicio",$fecha),
                        array(":fecha_fin",$fecha2),
                        array(":valor_p",$valor_p),
                        array(":valor_e",$valor_e),
                        array(":responsable",$responsable),
                );



                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($obj_resp)){
                    echo 1;
                } else {
                    echo 2;
                  //  var_dump($obj_resp);
                }
            }else{
                echo 3;
            }
        }else{
            echo 4;
        }
        
        
    break;
    #   *** Modificar la Actividad Proyecto   ***  #
    case 3:
        $proyecto   = $_REQUEST['ProAC'];
        $X = $_REQUEST['x'];
        
        if($X == 1){
            $actividad   = $_REQUEST['sltActividad'];
            $fecha_inicio   = $_REQUEST['sltFechaini'];
            $fecha_fin   = $_REQUEST['sltFechafin'];
            $responsable   = $_REQUEST['sltResponsable'];
            $valor_p   = $_REQUEST['txtvalorP'];
            
        }else{
            $actividad = $_REQUEST['actividad'];
            $fecha_inicio   = $_REQUEST['fechaIP'];
            $fecha_fin   = $_REQUEST['fechaFP'];
            $responsable   = $_REQUEST['responP'];
            $valor_p   = $_REQUEST['valorPO'];
        }
        
        $valor_e   = $_REQUEST['txtvalorE'];
        $valor_p = str_replace(',', '', $valor_p);
        $valor_e = str_replace(',', '', $valor_e);
        $idAP   = $_REQUEST['AcP'];
        /*echo $con = "UPDATE gy_actividad_proyecto SET 
                id_actividad = $actividad, 
                fecha_inicio_programada = '$fecha_inicio', 
                fecha_final_programada = '$fecha_fin',
                valor_programado = $valor_p,
                valor_ejecutado = $valor_e,
                 responsable_actividad = $responsable
               WHERE id_unico =  $idAP";*/
        
        $sum = "SELECT SUM(valor_programado) FROM gy_actividad_proyecto WHERE id_proyecto = '$proyecto' AND id_unico != '$idAP'";
        $suma = $mysqli->query($sum);
        $su = mysqli_fetch_row($suma);
        
        $vpt = $su[0] + $valor_p;
        #echo "valor T: ".$vpt;
        $montoT = "SELECT monto_total FROM gy_proyecto WHERE id_unico = '$proyecto'";
        $monto = $mysqli->query($montoT);
        $MT = mysqli_fetch_row($monto);
        #echo "MT: ".$MT[0];
        if($vpt <= $MT[0]){
            if($valor_p >= $valor_e){
                $sql_cons ="UPDATE  `gy_actividad_proyecto`
                            SET
                            `id_actividad`=:actividad,
                            `fecha_inicio_programada`=:fechaI,
                            `fecha_final_programada`=:fechaF,
                            `valor_programado`=:vp,
                            `valor_ejecutado`=:ve,
                            `responsable_actividad`=:res
                            WHERE id_unico = :id_unico";
                $sql_dato = array(
                        array(":actividad",$actividad),
                        array(":id_unico",$idAP),
                        array(":fechaI",$fecha_inicio),
                        array(":fechaF",$fecha_fin),
                        array(":vp",$valor_p),
                        array(":ve",$valor_e),
                        array(":res",$responsable)
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                #$res = $mysqli->query($con);
                #echo "res: ".$res;
                if(empty($obj_resp)){
                    echo 1;
                } else {
                    echo 2;
                   // var_dump($obj_resp);
                }
            }else{
                echo 3;
            }
        }else{
            echo 4;
        }
       
    break;
    case 4:
    break;
}
