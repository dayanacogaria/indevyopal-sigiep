<?php
#######################################################################################
#16/06/2017 |ERICA G. |ARCHIVO CREADO
#######################################################################################
require_once '../Conexion/conexion.php';
session_start();

switch ($_POST['action']){
#############GUARDAR ADICION APROPIACION##########    
case 1:
    $numero  = $_POST['numero'];
    $fecha  = $_POST['fecha'];
    $fechaVen  = $_POST['fechaVen'];
    if(!empty($_POST['descripcion'])){
        $descripcion = "'".$_POST['descripcion']."'";
    } else {
        $descripcion ='NULL';
    }
    $estado = '"'.$mysqli->real_escape_string(''.$_POST['estado'].'').'"';
    $tipocomprobante = '"'.$mysqli->real_escape_string(''.$_POST['tipocomprobante'].'').'"';
    ######CONVERSION FECHA######
    $fecha = trim($fecha, '"');
    $fecha_div = explode("/", $fecha);
    $dia = $fecha_div[0];
    $mes = $fecha_div[1];
    $anio = $fecha_div[2];
    $fecha = $anio.'-'.$mes.'-'.$dia;
    
    #######CONVERSION FECHA VENCIMIENTO###########
    $fechaVen = trim($fechaVen, '"');
    $fecha_div = explode("/", $fechaVen);
    $dia = $fecha_div[0];
    $mes = $fecha_div[1];
    $anio = $fecha_div[2];
    $fechaVen = $anio.'-'.$mes.'-'.$dia;
    $tercero =2;
    $responsable = 2;
    $parametroAnno = $_SESSION['anno'];
    $fechaE = date('Y/m/d');
    $user = $_SESSION['usuario'];
    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, 
        descripcion, parametrizacionanno, tipocomprobante, tercero, 
        estado, responsable, fecha_elaboracion, usuario) 
    VALUES($numero, '$fecha', '$fechaVen', $descripcion, $parametroAnno, "
            . "$tipocomprobante, $tercero, $estado, $responsable,'$fechaE', '$user' )";
    $resultado = $mysqli->query($insertSQL);
    if($resultado == true) {
        $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal "
                . "WHERE numero = '$numero' and tipocomprobante =$tipocomprobante";
        $ultimComp = $mysqli->query($queryUltComp);
        $rowUC = mysqli_fetch_row($ultimComp);
        echo json_decode($rowUC[0]);
    } else {
            echo json_decode(0);
    }
    
    break;
    #######MODIFICAR COMPROBANTE ADICION APROPIACION###########                
    case 2:
        $id=  $_POST['comprobante'];
        ##FECHA
        $fecha  = $_POST['fecha'];
        $fecha = trim($fecha, '"');
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio.'-'.$mes.'-'.$dia;
        $fechaVen  = $_POST['fechaVen'];
        $descripcion =$_POST['descripcion'];
        if(empty($descripcion)){
            $descripcion ='NULL' ;
        } else {
            $descripcion ="'".$_POST['descripcion']."'";
        }
        ##FECHA VENCIMIENTO
        $fechaVen = trim($fechaVen, '"');
        $fecha_div = explode("/", $fechaVen);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fechaVen = $anio.'-'.$mes.'-'.$dia;
        $upd= "UPDATE gf_comprobante_pptal SET fecha='$fecha', fechavencimiento ='$fechaVen', "
                . "descripcion = $descripcion WHERE id_unico = $id";
        $result=$mysqli->query($upd);
        //ACTUALIZAR DETALLES
        $udpd="UPDATE gf_detalle_comprobante_pptal SET descripcion = $descripcion WHERE comprobantepptal = $id";
        $udpd =$mysqli->query($udpd);
        if($result==true || $result==1){
            echo 1;
        } else {
            echo 0;
        }
    
    break; 
    ############REGISTRAR DETALLE ADICION A APROPIACION###########
    case 3:
        $rubro  = $_POST['rubro'];
        $fuente = $_POST['fuente'];
        $valor  = $_POST['valor'];
        $valor  = str_replace(',', '', $valor);
        ######RUBRO FUENTE#######
        $queryRubFue = "SELECT id_unico 
                FROM gf_rubro_fuente 
                WHERE rubro = $rubro   
                AND fuente = $fuente";
        $rubroFuente = $mysqli->query($queryRubFue);
        $result=1;
        if(mysqli_num_rows($rubroFuente)>0){
            $row = mysqli_fetch_row($rubroFuente);
            $id_rubro_fuente = $row[0];
        } else {
            $insertSQL = "INSERT INTO gf_rubro_fuente (rubro, fuente) 
                VALUES($rubro, $fuente)";
            $resultado = $mysqli->query($insertSQL);
            if($resultado == true) {
                $queryMaxID = "SELECT MAX(id_unico) FROM gf_rubro_fuente WHERE rubro = $rubro AND fuente = $fuente";
                $maxID = $mysqli->query($queryMaxID);
                $row = mysqli_fetch_row($maxID);
                $id_rubro_fuente = $row[0];
            } else {
                $result=2;
            }
        }
        $id_comprobante_pptal = $_POST['id'];
        if(!empty($_POST['descripcion'])){
            $descripcion = "'".$_POST['descripcion']."'";
        } else {
            $descripcion ='NULL';
        }
        $tercero = 2;
        $id_proyecto = 2147483647;
        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal "
               . "(descripcion, valor, comprobantepptal, rubrofuente, tercero, proyecto) "
        . "VALUES ($descripcion, $valor, $id_comprobante_pptal, $id_rubro_fuente, $tercero, $id_proyecto)";
        $resultado = $mysqli->query($insertSQL);
        if($resultado == true){
            $result=1;
        } else {
            $result=2;
        }
        echo json_decode($result);
    break;
    #########ELIMINAR DETALLES ADICION##########
    case 4:
        $id = $_POST['id'];
        $delet = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $id";
        $delete = $mysqli->query($delet);
        if($delete ==true){
            $result = 1;
        } else {
            $result = 2;
        }
        echo json_decode($result);
    break;
    ##########MODIFICAR DETALLE ADICION APROPIACION#######
    case 5:
        $id     = $_POST['id'];
        $valor  = $_POST['valor'];

        $updateSQL = "UPDATE gf_detalle_comprobante_pptal  
          SET valor = $valor    
          WHERE id_unico = $id";
        $resultado = $mysqli->query($updateSQL);
        if($resultado == true){ 
          $result= 1;
        } else {
          $result= 2;
        } 
        echo json_decode($result);
    break;

}