<?php
require '../Conexion/ConexionPDO.php';                                                  
require '../Conexion/conexion.php';                    
require_once('../Conexion/conexionsql.php');
require './funcionesPptal.php';
require '../ExcelR/Classes/PHPExcel/IOFactory.php';    
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
$fechaa     = date('Y-m-d');
switch ($action){
    #* Comprobar que las hojas existan
    case 1:
        $html = '';
        $rta  = 0;
        if (!empty($_FILES['flDoc']['tmp_name'])) {
            $file = $_FILES['flDoc']['tmp_name'];
            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            
            try { 
                if ($objPHPExcel->setActiveSheetIndexByName('CREDITOS')) {
                    $objWorksheet = $objPHPExcel->setActiveSheetIndexByName('CREDITOS');   
                }
            } catch (Exception $e) { $html .= 'Hoja Créditos, No Encontrada'.'<br/>';$rta  += 1; }
        }
        $datos = array("html" => $html, "rta" => $rta);
        echo json_encode($datos);
    break;
    #* Verificar que los terceros existan en la bd SQL
    case 2:
        $html = 'Terceros No Encontrados:'.'<br/>';
        $rta  = 0;
        if (!empty($_FILES['flDoc']['tmp_name'])) {
            $file = $_FILES['flDoc']['tmp_name'];
            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            
            if ($objPHPExcel->setActiveSheetIndexByName('CREDITOS')) {
                $objWorksheet3 = $objPHPExcel->setActiveSheetIndexByName('CREDITOS');   
                $total_filas3  = $objWorksheet3->getHighestRow();					
                for ($a = 2; $a <= $total_filas3; $a++) {
                    $nit       = $objWorksheet3->getCellByColumnAndRow(3, $a)->getCalculatedValue();
                    $sqlcf1 = "SELECT COUNT(*) AS ps FROM PERSONA WHERE PERSONA.Numero_Documento = '".$nit."'";
                    $stmt41 = sqlsrv_query( $conn, $sqlcf1 ); 
                    $row41 = sqlsrv_fetch_array( $stmt41, SQLSRV_FETCH_ASSOC);
                    if($row41['ps']>0){}else {
                        $rta  += 1;
                        $html .= $nit.'<br/>';
                    }
                }
            }
        }
        $datos = array("html" => $html, "rta" => $rta);
        echo json_encode($datos);
    break;
    
    #* Guardat Datos
    case 3:
        
        $htmlc  = 'Créditos Guardados: '.'<br/>';
        $rta  = 0;
        if (!empty($_FILES['flDoc']['tmp_name'])) {
            $file = $_FILES['flDoc']['tmp_name'];
            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = PHPExcel_IOFactory::load($file);
         
            #*************CREDITOS *****************#
            if ($objPHPExcel->setActiveSheetIndexByName('CREDITOS')) {
                $objWorksheet1  = $objPHPExcel->setActiveSheetIndexByName('CREDITOS'); 
                $total_filas    = $objWorksheet1->getHighestRow();					
                for ($a = 2; $a <= $total_filas; $a++){
                    $nit        = $objWorksheet1->getCellByColumnAndRow(3, $a)->getCalculatedValue();
                    $fechac     = $objWorksheet1->getCellByColumnAndRow(1, $a)->getCalculatedValue();
                    $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fechac);
                    $fechac     = date("Y-m-d",$timestamp);
                    $valor      = $objWorksheet1->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                    $abono      = $valor;
                    IF(!empty($abono)){
                        #** Buscar Créditos Saldo Del Tecero 
                        $sql = "SELECT DISTINCT c.Numero_Credito as Credito FROM CREDITO c
                            LEFT JOIN SOLICITUD_CREDITOS sc ON c.Id_Solicitud_Credito = sc.Identificador 
                            LEFT JOIN PERSONA_SOLICITUD  ps ON ps.Id_Solicitud = sc.Identificador 
                            LEFT JOIN PERSONA p ON ps.Id_persona =p.Numero_Documento 
                            WHERE p.Numero_Documento ='$nit' 
                            AND (SELECT SUM(dc.Saldo_Concepto) FROM DETALLE_CREDITO dc WHERE dc.Numero_Credito = c.Numero_Credito)>0
                            AND ps.Principal=1 ORDER BY c.Numero_Credito";
                        $sql  = sqlsrv_query( $conn, $sql ); 
                        $row  = sqlsrv_fetch_array( $sql, SQLSRV_FETCH_ASSOC);
                        $credito = $row['Credito'];

                        #*** CREAR ENCABEZADO PAGO 
                        $sqlca = "SELECT MAX(Numero_Recibo) AS Num_c FROM PAGOS";
                        $sqlca = sqlsrv_query( $conn, $sqlca ); 
                        $rownc  = sqlsrv_fetch_array( $sqlca, SQLSRV_FETCH_ASSOC);
                        if(empty($rownc['Num_c'])){
                            $numero_recibo = 1;
                        } else {
                            $numero_recibo = $rownc['Num_c']+1;
                        }

                        #** crear detalle
                        $sqlca = "INSERT INTO PAGOS 
                            (Numero_Recibo, Numero_Credito,Id_Documento,
                            Numero_Documento_Soporte,Fecha_Pago,Id_Banco,
                            Valor,Id_Estado_Pago,Observaciones,
                            Fecha_Creacion,Responsable_Creacion) 
                            VALUES(?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?)";

                        $var= array("$numero_recibo", "$credito",900, 
                            "$numero_recibo", "$fechac", 1,
                            $valor, 1,"Pago Archivo","$fechac", 1);
                        if (!sqlsrv_query($conn, $sqlca, $var))
                        {
                        } else {
                        $htmlc .=$nit.': $'.$valor.'<br/>';
                        $rta +=1;
                        while($abono>0){
                            $dc = "SELECT Id_Linea, Tipo_Linea, Id_Concepto, 
                                Numero_Cuota, Valor_Concepto, Saldo_Concepto 
                                FROM DETALLE_CREDITO 
                                WHERE Numero_Credito ='$credito'
                                AND Saldo_Concepto>0 ORDER BY Numero_Cuota;";
                            $dc = sqlsrv_query( $conn, $dc ); 
                            $rowdc  = sqlsrv_fetch_array( $dc, SQLSRV_FETCH_ASSOC);
                            $saldo = $rowdc['Saldo_Concepto'];
                            if($abono>$saldo){
                                $vg     = $saldo;
                                $abono -=$saldo;
                            } else {
                                $vg     = $abono;
                                $abono  = 0;
                            }
                            #*¨Guardar DTP
                             $sqlca = "INSERT INTO DETALLE_PAGO 
                                (Numero_Recibo, Numero_Credito, Id_Linea,
                                Tipo_Linea,Id_Concepto,Numero_Cuota,
                                Numero_Pago,Valor_pago,Pagado,
                                Observaciones,Fecha_Creacion,Responsable_Creacion) 
                                VALUES(?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?,?)";

                            $var= array("$numero_recibo", "$credito",$rowdc['Id_Linea'], 
                                $rowdc['Tipo_Linea'], $rowdc['Id_Concepto'], $rowdc['Numero_Cuota'],
                                1,$vg, 1,
                                "Pago Archivo","$fechaa", 1);
                            if (!sqlsrv_query($conn, $sqlca, $var))
                            {
                            } else {
                                $sc = $rowdc['Saldo_Concepto']-$vg;
                                #** ACTUALIZAR DETALLE CRÉDITO
                                $tsql = "UPDATE DETALLE_CREDITO 
                                        SET Saldo_Concepto = (?), 
                                        Valor_Abono = (?)
                                        WHERE Numero_Credito = (?) 
                                        AND Id_Linea = (?) 
                                        AND Tipo_Linea = (?)
                                        AND Id_Concepto = (?)
                                        AND Numero_Cuota = (?)";  
                                
                               $params = array($sc, $vg,"$credito",$rowdc['Id_Linea'], 
                                $rowdc['Tipo_Linea'], $rowdc['Id_Concepto'], $rowdc['Numero_Cuota']);  
                               if (!sqlsrv_query($conn, $tsql, $params)) {  
                               }
                            }

                        }
                        
                    }
                    }
                }
            }   
        }
        $html   =  $htmlc;
        $datos  = array("html" => $html, "rta" => $rta);
        echo json_encode($datos);
    break;
}