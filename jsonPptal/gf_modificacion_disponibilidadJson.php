<?php
#######################################################################################
#09/06/2017 |ERICA G. |ARCHIVO CREADO
#######################################################################################
require_once '../Conexion/conexion.php';
require_once './funcionesPptal.php';
session_start();
//SIUA
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
    #######ELIMINAR DETALLE MODIFICACION#########
    case 2:
        $id = $_POST['id'];
        $delete ="DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $id";
        $resultado = $mysqli->query($delete);
        echo json_decode($resultado);
    break;
    #########MODIFICACION VALOR DETALLES MODIFICACION A DISPONIBILIDAD#######
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
                        $afect = mysqli_fetch_row($select);
                        $val =$afect[0];
                        $to = $afect[1];
                        if($to==3){
                            $saldDisp +=$val;
                        } else {
                            $saldDisp -=$val;
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
}