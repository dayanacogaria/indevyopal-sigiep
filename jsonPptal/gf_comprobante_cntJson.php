<?php
#######################################################################################
#21/07/2017 |ERICA G. |ARCHIVO CREADO
#######################################################################################
require_once '../Conexion/conexion.php';
session_start();

switch ($_POST['action']){
    #############COPIAR COMPROBANTE##########    
    case 1:
        $idcnt = $_POST['idcnt'];
        $idcopiar = $_POST['idcopiar'];
        $fecha = $_POST['fecha'];
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fechacnt = $anio."-".$mes."-".$dia;
        
        $rs = 1;
        #DATOS DEL COMPROBANTE A COPIAR
        $dcopiar = "SELECT descripcion, numerocontrato, clasecontrato FROM gf_comprobante_cnt 
                                WHERE id_unico = $idcopiar";
        $dcopiar = $mysqli->query($dcopiar);
        $dc = mysqli_fetch_row($dcopiar);
        if(empty($dc[0])){
            $descripcion ='NULL';
        } else {
            $descripcion = "'".$dc[0]."'";
        }
        if(empty($dc[1])){
            $numc ='NULL';
        } else {
            $numc = "'".$dc[1]."'";
        }
        if(empty($dc[2])){
            $tipoc ='NULL';
        } else {
            $tipoc = "'".$dc[2]."'";
        }
        ##ACTUALIZAR COMPROBANTE
        $upd = "UPDATE gf_comprobante_cnt SET descripcion = $descripcion, numerocontrato = $numc, clasecontrato = $tipoc "
                . "WHERE id_unico = $idcnt";
        $upd =$mysqli->query($upd);
        if($upd ==true){
            $rs = 1;
        } else {
            $rs = 2;
        }
        ##COPIAR DETALLES
        $detc = "SELECT descripcion, valor, valorejecucion, "
                    . "cuenta, naturaleza, tercero, proyecto, centrocosto "
                    . "FROM gf_detalle_comprobante "
                    . "WHERE comprobante = $idcopiar";
        $detc = $mysqli->query($detc);
        if(mysqli_num_rows($detc)>0){
            while ($row = mysqli_fetch_row($detc)) {
                $ins = "INSERT INTO gf_detalle_comprobante  "
                        . "(fecha, descripcion, valor, valorejecucion, comprobante, "
                        . "cuenta, naturaleza, tercero, proyecto, centrocosto) "
                        . "VALUES( '$fechacnt', '$row[0]', $row[1], $row[2], $idcnt, "
                        . "$row[3], $row[4], $row[5], $row[6], $row[7])";
                $ins = $mysqli->query($ins);
            }
        }
            
        echo json_decode($rs);
    break;
}
