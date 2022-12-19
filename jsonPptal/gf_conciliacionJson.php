<?php
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
require '../ExcelR/Classes/PHPExcel/IOFactory.php';  
session_start(); 
$action     = $_REQUEST['action'];
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$calendario = CAL_GREGORIAN;
$con        = new ConexionPDO(); 
switch ($action){
    case 1:
        $mes    = $_REQUEST['mes'];
        $cuenta = $_REQUEST['cuenta'];
        $row = $con->Listar("SELECT * FROM gf_partida_conciliatoria 
            WHERE id_cuenta =$cuenta AND mes =$mes ");
        if(count($row)>0){
            $rta=1;
        } else {
            $rta=0;
        }
        echo $rta;
    break;
    #Guardar Datos
    case 2:
        $rta = 0;
        $mes    = $_REQUEST['mes'];
        $cuenta = $_REQUEST['cuenta'];
        $saldo  = str_replace(',', '',$_REQUEST['saldo']);
        
        $documento  = $_FILES['file'];
        $name       = $_FILES['file']['name'];
        $ext        = pathinfo($name, PATHINFO_EXTENSION);
        $directorio ='../documentos/partidasConciliatorias/';
        if(empty($_POST['nombre'])){
            $nombre         = $name;
            $nombreArchivo  = pathinfo($name, PATHINFO_FILENAME);
        } else {
            $nombreArchivo  = 'archivoconciliacion'.$mes.$cuenta;
            $nombre         = $nombreArchivo.'.'.$ext;
        }
        
        $subir  =   move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre); 
        if($subir ==true){
            $ruta = $directorio.$nombre;
            $sql_cons ="INSERT INTO `gf_partida_conciliatoria`  
                ( `id_cuenta`,  `saldo_extracto`, 
                `mes`,`archivo_extracto`) 
            VALUES (:id_cuenta, :saldo_extracto, 
                :mes, :archivo_extracto)";
            $sql_dato = array(
                array(":id_cuenta",$cuenta),
                array(":saldo_extracto",$saldo),
                array(":mes",$mes),
                array(":archivo_extracto",$ruta),   
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                
                $rta =  1;
            }
        }
        echo $rta;
    break;
    
    case 3:
        $mes    = $_REQUEST['mes'];
        $cuenta = $_REQUEST['cuenta'];
        $bscid          = $con->Listar("SELECT * FROM gf_partida_conciliatoria WHERE id_cuenta = $cuenta AND mes = $mes");
        $id_partida     = $bscid[0][0];
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas    = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        for ($a = 2; $a <= $total_filas; $a++) {
            $fecha      = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha);
            $fecha      = date("Y-m-d",$timestamp);
            $valor      = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            if(!empty($valor)){
                #Buscar Si Es Egreso o Ingreso 
                $valorb = $valor *-1;
                $rowb = $con->Listar("SELECT id_unico FROM gf_detalle_comprobante 
                    WHERE  cuenta = $cuenta AND valor = $valorb 
                    AND (conciliado IS NULL OR conciliado != 1) ");
                if(count($rowb)){
                    $sql_cons ="UPDATE `gf_detalle_comprobante`  
                        SET `conciliado`=:conciliado, 
                        `periodo_conciliado` =:periodo_conciliado 
                        WHERE `id_unico`=:id_unico";
                    $sql_dato = array(
                        array(":conciliado",1),
                        array(":periodo_conciliado",$mes),
                        array(":id_unico",$rowb[0][0]),   
                    );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);          
                }
            }
        }
        echo $id_partida;
    break;
   
}