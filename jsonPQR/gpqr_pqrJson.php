<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#13/12/2018 | Nestor b. | Archivo Creado
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
    #   *** Eliminar Clase Descripción    ***  #
    case 1:
        $id      = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gpqr_pqr`
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
    #   *** Guardar PQR    ***  #
    case 2:
        $tercero   = $_REQUEST['sltTer'];
        
        if(empty($tercero)){
            $tercero = 'NULL';
        }
        $unidad   = $_REQUEST['sltUV'];

        if(empty($unidad)){
            $unidad = 'NULL';
        }

        $estado   = $_REQUEST['sltEstado'];
        $fecha   = $_REQUEST['sltFechaS'];

        $f_d = explode("/",$fecha);
        $dia = $f_d[0];
        $mes = $f_d[1];
        $ani = $f_d[2];
        
        $fech = ''.$ani.'-'.$mes.'-'.$dia.'';
        #$afavor  = $_REQUEST['sltAfavor'];
        $fac = $_REQUEST['sltFac'];

        if(empty($fac)){
            $fac = 'NULL';
        }

        $Obs = $_REQUEST['txtObservaci'];

        ini_set('date.timezone', 'America/Bogota');
        $hora = date('H:i:s', time());

        $fecha_h = $fech.':'.$hora;
        if(empty($Obs)){
            $Obs = NULL;
        }

        

        $sql = "INSERT INTO gpqr_pqr(id_unidad_vivienda, id_tercero,fecha_hora,id_estado_pqr,id_factura,observaciones,compania)
            VALUES($unidad, $tercero,'$fecha_h',$estado,$fac,'$Obs',  $compania)";

        $res = $mysqli->query($sql);
       
        if($res == 1){

            $maximo = "SELECT MAX(id_unico) FROM gpqr_pqr ";
            $resmax = $mysqli->query($maximo);
            $id     = mysqli_fetch_row($resmax);
            echo $id[0];
        } else {
            echo 2;
          //  var_dump($obj_resp);
        }
    break;
    #   *** Modificar Descripcion   ***  #
    case 3:
        $id       = $_REQUEST['txtIdPQR'];

        $sqlMod = "SELECT * FROM gpqr_detalle_pqr WHERE id_pqr = '$id'";
        $resMod = $mysqli->query($sqlMod);
        $nres   = mysqli_num_rows($resMod);

        if($nres > 0){
            $obs = $_REQUEST['txtObservaci'];
            $est = $_REQUEST['sltEstado'];

            $sql = "UPDATE gpqr_pqr SET observaciones = '$obs', id_estado_pqr = '$est' WHERE id_unico = '$id'";
            $res = $mysqli->query($sql);
        }else{
            $obs = $_REQUEST['txtObservaci'];
            $tercero   = $_REQUEST['sltTer'];
        
            if(empty($tercero)){
                $tercero = 'NULL';
            }
            $unidad   = $_REQUEST['sltUV'];

            if(empty($unidad)){
                $unidad = 'NULL';
            }

            $estado   = $_REQUEST['sltEstado'];
            $fecha   = $_REQUEST['sltFechaS'];

            $f_d = explode("/",$fecha);
            $dia = $f_d[0];
            $mes = $f_d[1];
            $ani = $f_d[2];
            
            $fech = ''.$ani.'-'.$mes.'-'.$dia.'';
            #$afavor  = $_REQUEST['sltAfavor'];
            $fac = $_REQUEST['sltFac'];

            

            ini_set('date.timezone', 'America/Bogota');
            $hora = date('H:i:s', time());

            $fecha_h = $fech.':'.$hora;

            $sql = "UPDATE gpqr_pqr SET id_unidad_vivienda = $unidad,id_tercero = $tercero, fecha_hora = '$fecha_h', id_estado_pqr = $estado, id_factura = $fac, observaciones = '$obs'WHERE id_unico = '$id'  ";
            $res  = $mysqli->query($sql);
        } 
        
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
