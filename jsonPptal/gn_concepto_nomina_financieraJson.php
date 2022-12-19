<?php
##############################################################################################################################
#                                                                                                           MODIFICACIONES
##############################################################################################################################                                                                                                           
#24/07/2017 |Erica G. |Archivo Creado
##############################################################################################################################
require_once('../Conexion/conexion.php');
session_start();
$action     = $_REQUEST['action'];
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
switch ($action){
    ############REGISTRAR HOMOLOGACIÓN#############
    case (1):
        $conceptoN=$_POST['conceptoN'];
        $conceptoF=$_POST['conceptoF'];
        $grupoG=$_POST['grupoG'];
        if(empty($_POST['tercero'])){
            $tercero = 'NULL';
        } else {
            $tercero = $_POST['tercero'];
        }
        $insert = "INSERT INTO gn_concepto_nomina_financiero (concepto_nomina, concepto_financiero, "
                . "grupo_gestion, tercero) VALUES ($conceptoN, $conceptoF, $grupoG, $tercero)";
        $r =$mysqli->query($insert);
        echo json_decode($r);
    break;
    ############ELIMINAR HOMOLOGACIÓN#############
    case (2):
        $id =$_POST['id'];
        $delete = "DELETE FROM  gn_concepto_nomina_financiero WHERE id_unico = $id";
        $r =$mysqli->query($delete);
        echo json_decode($r);
    break;
    ############MODIFICAR HOMOLOGACIÓN#############
    case (3):
        $id=$_POST['id'];
        $conceptoN=$_POST['conceptoN'];
        $conceptoF=$_POST['conceptoF'];
        $grupoG=$_POST['grupoG'];
        if(empty($_POST['tercero'])){
            $tercero = 'NULL';
        } else {
            $tercero = $_POST['tercero'];
        }
        $insert = "UPDATE gn_concepto_nomina_financiero SET concepto_nomina=$conceptoN, "
                . "concepto_financiero=$conceptoF, "
                . "grupo_gestion=$grupoG, "
                . "tercero=$tercero WHERE id_unico = $id";
        $r =$mysqli->query($insert);
        echo json_decode($r);
    break;
}
