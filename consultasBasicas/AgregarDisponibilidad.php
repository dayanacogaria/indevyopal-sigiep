<?php
############################ MODIFICACIONES ########################################
#17/07/2017| ERICA G. | Verificar el valos disponible de los detalles de la dis.
#13-02-2017| 05:30 | Erica G. //Archivo Creado 
####################################################################################
require_once '../Conexion/conexion.php';
session_start();
 $comprobante = $_POST['comprobante'];
$disponibilidad = $_POST['disponibilidad'];
$resultado=true;
 $query = "SELECT descripcion, valor, 
        rubrofuente,
        tercero, 
        proyecto, 
        id_unico, 
        conceptorubro, centro_costo 
      FROM gf_detalle_comprobante_pptal 
      where comprobantepptal ='$disponibilidad'";
    
    $resultado1 = $mysqli->query($query);
    
   while($row = mysqli_fetch_row($resultado1)){
       $valorRep=0;
      ################VALOR DISPONIBLE##########
        $totalSaldDispo = 0;
        $valorRep +=$row[1];
        $saldo =0;
        ########AFECTACIONES A DISPONBILIDAD#########
        $afec = "SELECT tc.tipooperacion, dc.valor, dc.id_unico FROM gf_detalle_comprobante_pptal dc 
                LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico 
                LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                WHERE dc.comprobanteafectado = $row[5]";
        $afec = $mysqli->query($afec);
        while ($row2 = mysqli_fetch_row($afec)) {
            if($row2[0]==2){
                $valorRep +=$row2[1];
            } elseif($row2[0]==1) {
                   $valorRep -=$row2[1];
                    ########AFECTACIONES A REGISTRO#########
                    $afecR = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc 
                            LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico 
                            LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $row2[2]";
                    $afecR = $mysqli->query($afecR);
                    if(mysqli_num_rows($afecR)>0){
                    while ($row2R = mysqli_fetch_row($afecR)) {
                        if($row2R[0]==2){
                            $valorRep -=$row2R[1];
                        } 
                        elseif($row2R[0]==3) {
                            $valorRep +=$row2R[1];
                        }
                    }
                    }
            }elseif($row2[0]==3) {
                $valorRep-=$row2[1];
            }
        }
        $totalSaldDispo += $valorRep;
        $valor = $valorRep;
     ####################################################
      if($valor>0){
      $datos = "SELECT descripcion, tercero FROM gf_comprobante_pptal WHERE id_unico ='$comprobante'";
      $datos =$mysqli->query($datos);
      
      if($row[1] > 0){
          if(mysqli_num_rows($datos)>0){
           $datos = mysqli_fetch_row($datos);
           if(empty($datos[1])){
               $tercero = $row[3];
           }else {
           $tercero = $datos[1];
           }
           if(empty($datos[0])){
             $descripcion = $row[0];  
           }else {
           $descripcion = $datos[0];
           }
           
          } else {
          $descripcion = $row[0];
          $tercero= $row[3];
          }
          
          $rubro = $row[2];
          $proyecto = $row[4];
          $idAfectado = $row[5];
          $conceptorubro = $row[6];
          $var=0;
          if(empty($row[7])) { 
            $centro_costo = 'NULL';
          } else {
            $centro_costo = $row[7];
          }
           $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal (valor,descripcion, "
                  . "comprobantepptal, rubrofuente, tercero, proyecto, comprobanteafectado, conceptorubro, centro_costo) "
                  . "VALUES ($valor, '$descripcion',$comprobante, $rubro, $tercero, $proyecto, $idAfectado, $conceptorubro,$centro_costo)";
          
          $resultado = $mysqli->query($insertSQL);
          
      } else {
        $resultado = false;  
      }
      }
     
   }

 echo json_encode($resultado);     


        
 ?>
