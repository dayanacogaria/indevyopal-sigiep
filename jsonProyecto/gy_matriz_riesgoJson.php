<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#26/11/2018 | Nestor B. | Archivo Creado
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
    #   *** Eliminar Matriz riesgo   ***  #
    case 1:
        $id      = $_REQUEST['id'];
        
        $sql_cons ="DELETE FROM `gy_matriz_riesgo`
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
       
        
        
    break;
    #   *** Guardar Matriz de Riesgo    ***  #
    case 2:
        $tipoR      = $_REQUEST['sltTRiesgo'];
        $Riesgo     = $_REQUEST['sltRiesgo'];
        $probab     = $_REQUEST['sltProba'];
        $impacto    = $_REQUEST['sltImpacto'];
        $mitiga     = $_REQUEST['sltMiti'];
        $respo      = $_REQUEST['sltResp'];
        $control    = $_REQUEST['txtControles'];
        $proyecto   = $_REQUEST['txtProyecto'];
        #$proyecto   = 4;
        $sql = "INSERT INTO gy_matriz_riesgo(id_tipo_riesgo,id_riesgo,id_probabilidad,id_tipo_impacto,controles_existentes,id_mitigacion,id_tercero_responsable,id_proyecto)"
                . "VALUES($tipoR, $Riesgo, $probab, $impacto,'$control',$mitiga,$respo,$proyecto)";
       
        $res = $mysqli->query($sql);
        if($res == 1){
            echo 1;
        } else {
            echo 2;
            //var_dump($obj_resp);
        }
    break;
    #   *** Modificar la matriz de riesgos   ***  #
    case 3:
        $riesgo   = $_REQUEST['sltRiesgo'];
        $tipo     = $_REQUEST['sltTRiesgo'];
        $proba    = $_REQUEST['sltProba'];
        $impac     = $_REQUEST['sltTim'];
        $control      = $_REQUEST['txtControles'];
        $mitig      = $_REQUEST['sltMit'];
        $resp      = $_REQUEST['sltResponsable'];
        $id      = $_REQUEST['txtidM'];
        
        $con = "UPDATE gy_matriz_riesgo SET 
                id_tipo_riesgo= $tipo, 
                id_riesgo = $riesgo, 
                id_probabilidad = $proba,
                id_tipo_impacto = $impac,
                controles_existentes = '$control',
                id_mitigacion = $mitig,
                id_tercero_responsable = $resp
               WHERE id_unico =  $id";
        
        $res = $mysqli->query($con);
        #echo "res: ".$res;
        if($res == 1){
            echo 1;
        } else {
            echo 2;
           // var_dump($obj_resp);
        }
    break;
    case 4:
    break;
}
