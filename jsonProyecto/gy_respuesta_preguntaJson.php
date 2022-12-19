<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#17/04/2018 | Erica G. | Archivo Creado
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
    #   *** Eliminar Tipo Identificacion    ***  #
    case 1:
        $id      = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gy_categoria`
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
    #   *** Guardar Respuestas  ***  #
    case 2:
        
        $pro = $_REQUEST['txtidproyecto'];
        $cantidad = $_REQUEST['txtnump'];
        
        for($a = 1; $a <= $cantidad; $a++){
            
            $id_pregunta = 'idpregunta'.$a;
            $id_pre = $_POST["$id_pregunta"];
            $resp        = 'txtresp'.$a;
            $re = $_POST["$resp"];
            
            if(!empty($re)){
                $sql = "INSERT INTO gy_respuesta_pregunta(respuesta,id_pregunta,id_proyecto,compania)
                        VALUES('$re',$id_pre,$pro,$compania)";
                
                $res = $mysqli->query($sql);
                
                
            }
        }
        
        if($res == 1){
                    echo 1;
                }else{
                    echo 2;
                }
        
    break;
    #   *** Modificar Tipo Identificacion    ***  #
    case 3:
        $pro = $_REQUEST['txtidproyecto'];
        $cantidad = $_REQUEST['txtnump'];
        
        for($a = 1; $a <= $cantidad; $a++){
            
            $id_pregunta = 'idpregunta'.$a;
            $id_pre = $_POST["$id_pregunta"];
            $resp        = 'txtresp'.$a;
            $re = $_POST["$resp"];
            
            $existe = "SELECT * FROM gy_respuesta_pregunta WHERE id_pregunta = '$id_pre' AND id_proyecto = '$pro'";
            
            $exist = $mysqli->query($existe);
            $resexi = mysqli_num_rows($exist);
            
            
            if($resexi < 1){
                if(!empty($re)){
                    $sql = "INSERT INTO gy_respuesta_pregunta(respuesta,id_pregunta,id_proyecto,compania)
                            VALUES('$re',$id_pre,$pro,$compania)";

                    $res = $mysqli->query($sql);
                }
                
            }else{
                if(!empty($re)){
                    $I_PR = mysqli_fetch_row($exist);
                    $sql = "UPDATE gy_respuesta_pregunta SET respuesta = '$re' WHERE id_unico = '$I_PR[0]' AND id_proyecto = '$pro' AND compania = '$compania'";
                    $res = $mysqli->query($sql);
                }else{
                    $I_PR = mysqli_fetch_row($exist);
                    $sql = "DELETE FROM gy_respuesta_pregunta WHERE id_unico = '$I_PR[0]' AND id_proyecto = '$pro' AND compania = '$compania'";
                    $res = $mysqli->query($sql);
                }    
            }
            
             
        }
        
        if($res == 1){
                    echo 1;
                }else{
                    echo 2;
                }
    break;
    case 4:
    break;
}
