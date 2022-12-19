<?php
#######################################################################################
#09/06/2017 |ERICA G. |ARCHIVO CREADO  
#######################################################################################
require_once '../Conexion/conexion.php';
require_once './funcionesPptal.php';
session_start();

switch ($_POST['action']){
    #########MODIFICACION DATOS MODIFICACION A DISPONIBILIDAD#######
    case 1:
        if(!empty($_POST['descripcion'])){
            $descripcion = '"'.$_POST['descripcion'].'"';
        } else{
            $descripcion = 'NULL';
        }
        $idMod  = $_POST['id'];
        $fecha  = $_POST['fecha'];
        //CONVERTIR FECHA
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        //CONVERTIR FECHA VEN
        $fechaV = $_POST['fechaVen'];
        $fecha_div = explode("/", $fechaV);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fechaV = $anio."-".$mes."-".$dia;
        $update = "UPDATE gf_comprobante_pptal "
                . "SET descripcion = $descripcion, "
                . "fecha = '$fecha', "
                . "fechavencimiento = '$fechaV' "
                . "WHERE id_unico = '$idMod'";
        $resultado = $mysqli->query($update);
        echo json_decode($resultado);
    break;
    #######ELIMINAR DETALLE MODIFICACION A DISPONIBILIDAD Y REGISTRO#########
    case 2:
        $id = $_POST['id'];
        $delete ="DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $id";
        $resultado = $mysqli->query($delete);
        echo json_decode($resultado);
    break;
    #########MODIFICACION VALOR DETALLES MODIFICACION A DISPONIBILIDAD Y A REGISTRO#######
    case 3:
        $id = $_POST['id'];
        $valor = $_POST['valor'];
        $update ="UPDATE gf_detalle_comprobante_pptal SET valor = '$valor' WHERE id_unico = $id";
        $resultado = $mysqli->query($update);
        echo json_decode($resultado);
    break;
    #########AGREGAR DISPONIBILIDAD MODIFICACION A DISPONIBILIDAD#######
    case 4:
        #############VALIDAR FECHAS##################
        $id = $_POST['id'];
        $dis = $_POST['dis'];
        $resultado =1;
        #CONSULTAR FECHAS#
        #MODIFICACION
        $fechamod = "SELECT cp.fecha, tc.tipooperacion "
                . "FROM gf_comprobante_pptal cp "
                . "LEFT JOIN gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante "
                . "WHERE cp.id_unico = $id";
        $fechamod = $mysqli->query($fechamod);
        $fechamod = mysqli_fetch_row($fechamod);
        $fechamodificacion = $fechamod[0];
        $tipoO = $fechamod[1];
        #DISPONIBILIDAD
        $fechadis = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = $dis";
        $fechadis = $mysqli->query($fechadis);
        $fechadis = mysqli_fetch_row($fechadis);
        $fechadis = $fechadis[0];
        if($fechadis > $fechamodificacion){
            $resultado =3;
        } else {
             ########BUSCAR DATOS DE DISPONIBILIDAD#########
             $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, 
                    detComP.rubrofuente, detComP.tercero, detComP.proyecto, 
                    detComP.id_unico , detComP.conceptoRubro 
                  FROM gf_detalle_comprobante_pptal detComP 
                  where detComP.comprobantepptal = $dis";
            $resultado1 = $mysqli->query($queryAntiguoDetallPttal);
         
            while($row = mysqli_fetch_row($resultado1))
            {
                #########SI ES REDUCCION #########
                if($tipoO==3){
                 $saldDisp = $row[1];
                 $totalAfec = 0;
                 $queryDetAfe = "SELECT
                   dcp.valor,
                   tc.tipooperacion, dcp.id_unico 
                 FROM
                   gf_detalle_comprobante_pptal dcp
                 LEFT JOIN
                   gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                 LEFT JOIN
                   gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico
                 WHERE
                   dcp.comprobanteafectado =".$row[5];
                 $detAfec = $mysqli->query($queryDetAfe);
                 $totalAfe = 0;
                while($rowDtAf = mysqli_fetch_row($detAfec))
                {
                    $saldDisp.'Principio Ciclo';
                    if($rowDtAf[1]==3){
                          $saldDisp = $saldDisp - $rowDtAf[0];
                    } else {
                        if($rowDtAf[1]==2){
                            $saldDisp = $saldDisp + $rowDtAf[0];
                        } else {
                            $saldDisp = $saldDisp- $rowDtAf[0];
                        }
                    }
                    
                    $ida=$rowDtAf[2];
                     $selec="  SELECT
                      dcp.valor,
                      tc.tipooperacion
                    FROM
                      gf_detalle_comprobante_pptal dcp
                    LEFT JOIN
                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
                    WHERE
                      dcp.comprobanteafectado = $ida AND tc.tipooperacion != 1  ";  
                    $select =$mysqli->query($selec);
                    if(mysqli_num_rows($select)>0){ 
                        while($afect = mysqli_fetch_row($select)) {
                            $val =$afect[0];
                            $to = $afect[1];
                            if($to==3){
                               $saldDisp +=$val;
                            } else {
                                $saldDisp -=$val;
                            }
                        }
                        
                    }
                    
                }
                $valorPpTl = $saldDisp;
                if($valorPpTl > 0){
                    $valor = $valorPpTl;
                    $rubro = $row[2];
                    $tercero = $row[3]; 
                    $proyecto = $row[4];
                    $idAfectado = $row[5];
                    $conceptoRubro = $row[6];
                    if(!empty($row[0]))
                    {
                      $descripcionD = "'".$row[0]."'";
                    } else {
                      $descripcionD='NULL';
                    }
                   $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal "
                            . "(valor, comprobantepptal, rubrofuente, tercero, "
                            . "proyecto, comprobanteafectado, conceptoRubro, descripcion)"
                            . " VALUES ($valor, $id, $rubro, $tercero, $proyecto, "
                            . "$idAfectado,$conceptoRubro, $descripcionD )";
                    $resultadoInsert = $mysqli->query($insertSQL);   
                    if($resultadoInsert==true){
                        $resultado =1;
                    } else {
                        $resultado =2;
                    }
                }
            } else {
                if($tipoO==2){
                    $rubro = $row[2];
                    $saldoDis = apropiacion($rubro) - disponibilidades($rubro);
                    $saldoDisponible = $saldoDis;
                    if($saldoDisponible>0){
                        $tercero = $row[3]; 
                        $proyecto = $row[4];
                        $idAfectado = $row[5];
                        $conceptoRubro = $row[6]; 
                        $valor = 0;
                        if(!empty($row[0]))
                        {
                          $descripcionD = "'".$row[0]."'";
                        } else {
                          $descripcionD='NULL';
                        }
                        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal (valor, "
                                . "comprobantepptal, rubrofuente, tercero, proyecto,"
                                . " comprobanteafectado, conceptoRubro, descripcion ) "
                                . "VALUES ($valor, $id, "
                                . "$rubro, $tercero, $proyecto, $idAfectado,$conceptoRubro, $descripcionD)";
                        $resultadoInsert = $mysqli->query($insertSQL);
                        if($resultadoInsert==true){
                            $resultado =1;
                        } else {
                            $resultado =2;
                        }
                    }   
                }    
            }
        }
        }
        echo json_decode($resultado);
    break;
    case 5:
        if(!empty($_POST['descripcion'])){
            $descripcion = '"'.$_POST['descripcion'].'"';
        } else{
            $descripcion = 'NULL';
        }
        if(!empty($_POST['clasec'])){
            $clasec = '"'.$_POST['clasec'].'"';
        } else{
            $clasec = 'NULL';
        }
        if(!empty($_POST['numeroC'])){
            $numeroC = '"'.$_POST['numeroC'].'"';
        } else{
            $numeroC = 'NULL';
        }
        
        $idMod  = $_POST['id'];
        $fecha  = $_POST['fecha'];
        //CONVERTIR FECHA
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        //CONVERTIR FECHA VEN
        $fechaV = $_POST['fechaVen'];
        $fecha_div = explode("/", $fechaV);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fechaV = $anio."-".$mes."-".$dia;
        $update = "UPDATE gf_comprobante_pptal "
                . "SET descripcion = $descripcion, "
                . "fecha = '$fecha', "
                . "fechavencimiento = '$fechaV', "
                . "clasecontrato = $clasec, "
                . "numerocontrato = $numeroC "
                . "WHERE id_unico = '$idMod'";
        $resultado = $mysqli->query($update);
        echo json_decode($resultado);
    break;
    #########AGREGAR REGISTRO MODIFICACION A REGISTRO#######
    case 6:
        #############VALIDAR FECHAS##################
        $id = $_POST['id'];
        $dis = $_POST['dis'];
        $resultado =1;
        #CONSULTAR FECHAS#
        #MODIFICACION
        $fechamod = "SELECT cp.fecha, tc.tipooperacion "
                . "FROM gf_comprobante_pptal cp "
                . "LEFT JOIN gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante "
                . "WHERE cp.id_unico = $id";
        $fechamod = $mysqli->query($fechamod);
        $fechamod = mysqli_fetch_row($fechamod);
        $fechamodificacion = $fechamod[0];
        $tipoO = $fechamod[1];
        #REGISTRO
        $fechadis = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = $dis";
        $fechadis = $mysqli->query($fechadis);
        $fechadis = mysqli_fetch_row($fechadis);
        $fechadis = $fechadis[0];
        if($fechadis > $fechamodificacion){
            $resultado =3;
        } else {
             ########BUSCAR DATOS DE REGISTRO#########
             $queryAntiguoDetallPttal = "SELECT detComP.id_unico,
                detComP.valor, detComP.rubrofuente, 
                detComP.proyecto, detComP.conceptorubro 
              FROM gf_detalle_comprobante_pptal detComP
              where detComP.comprobantepptal = $dis";
            $resultado1 = $mysqli->query($queryAntiguoDetallPttal);
         
            while($row = mysqli_fetch_row($resultado1))
            {
                #########SI ES REDUCCION #########
                if($tipoO==3){
                   $valorD = $row[1];
                   $afect = "SELECT dc.valor, tc.tipooperacion, dc.id_unico  "
                           . "FROM gf_detalle_comprobante_pptal dc "
                           . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                           . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                           . "WHERE dc.comprobanteafectado = '$row[0]'";
                   $afect = $mysqli->query($afect);
                   while ($af = mysqli_fetch_row($afect)) {
                       $to = $af[1];
                       if(($to == 2) || ($to == 4))
                       {
                               $valorD += $af[0];
                       }
                       elseif($to == 3)
                       {
                               $valorD -= $af[0];
                       } 
                       elseif($to == 1){
                              $valorD -= $af[0];
                       }
                        ########AFECTACIONES A Aprobaciones#########
                        $afecR = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc "
                                . "LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico "
                                . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                                . "WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $af[2]";
                        $afecR = $mysqli->query($afecR);
                        while ($row2R= mysqli_fetch_row($afecR)) {
                            if($row2R[0]== 2 || $row2R[0] == 4){
                                $valorD -=$row2R[1];
                            }elseif($row2R[0]== 3 || $row2R[0] == 2){
                                $valorD +=$row2R[1];
                            }
                        }
                   }
                    $saldoDisponible = $valorD; 

                   $valorPpTl = $saldoDisponible;


                  if($valorPpTl > 0)
                  {
                    $valor = $valorPpTl;
                    $rubro = $row[2]; 
                    $proyecto = $row[3];
                    $idAfectado = $row[0];
                    $conceptoRubro = $row[4];

                    $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal ("
                            . " valor, comprobantepptal, rubrofuente, tercero, proyecto, "
                            . "comprobanteafectado, conceptoRubro, descripcion) VALUES "
                            . "( $valor, $id, $rubro, $tercero, $proyecto, "
                          . "$idAfectado,$conceptoRubro, $descripcion)";
                    $resultadoInsert = $mysqli->query($insertSQL);
                  }
                } else {
                if($tipoO==2){
                    $valorDis ="SELECT DISTINCT dcp.id_unico, dcp.comprobanteafectado ,dcp.valor, dca.valor 
                            FROM gf_detalle_comprobante_pptal dcp 
                            LEFT JOIN gf_detalle_comprobante_pptal dca 
                            ON dcp.comprobanteafectado = dca.id_unico
                            WHERE dcp.id_unico = $row[0]";
                $valorDis = $mysqli->query($valorDis);
                $valorDisp=0;
                $afectado =0;
                $valorD=0;
                $valorRep=0;
                while($rowDetComp = mysqli_fetch_row($valorDis))
                {
                    ####AFECTACIONES DISPONIBILIDAD######
                    $valorD = $rowDetComp[3];
                    $afr = "SELECT dc.valor, tc.tipooperacion FROM gf_detalle_comprobante_pptal dc "
                            . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                            . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                            . "WHERE dc.comprobanteafectado = $rowDetComp[1] "
                            . "and tc.tipooperacion !=1";
                    $afr = $mysqli->query($afr);
                    while($row4 = mysqli_fetch_row($afr)){
                        if($row4[1]==3){
                            $valorD =$valorD-$row4[0];
                        } elseif($row4[1]==2){
                            $valorD =$valorD+$row4[0];
                        }
                    }
                    ####AFECTACIONES REGISTRO######
                    $valorRep = $rowDetComp[2];
                    $afr = "SELECT dc.valor, tc.tipooperacion FROM gf_detalle_comprobante_pptal dc "
                            . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                            . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                            . "WHERE dc.comprobanteafectado = $rowDetComp[0] "
                            . "and tc.tipooperacion !=1";
                    $afr = $mysqli->query($afr);
                    while($row4 = mysqli_fetch_row($afr)){
                        if($row4[1]==3){
                            $valorRep =$valorRep-$row4[0];
                        } elseif($row4[1]==2){
                            $valorRep =$valorRep+$row4[0];
                        }
                    }

                    $saldoDis  = $valorD-$valorRep;
                    $valorDisp +=$saldoDis;

                }
                $saldoDisponible = $valorDisp; 
                $rubro = $row[3];
                if($saldoDisponible>0){
                    $rubro = $row[2]; 
                    $proyecto = $row[3];
                    $idAfectado = $row[0];
                    $conceptoRubro = $row[4];
                    $valor = 0;
                    $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal (valor, "
                            . "comprobantepptal, rubrofuente, tercero, proyecto,"
                            . " comprobanteafectado, conceptoRubro, descripcion ) "
                            . "VALUES ($valor, $id, "
                            . "$rubro, $tercero, $proyecto, $idAfectado,$conceptoRubro, $descripcion)";
                    $resultadoInsert = $mysqli->query($insertSQL);
                    }
                }
            }
            
        }
        }
        echo json_decode($resultado);
    break;
}