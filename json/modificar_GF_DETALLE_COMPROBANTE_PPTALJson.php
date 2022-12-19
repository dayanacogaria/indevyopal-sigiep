<?php
  require_once('../Conexion/conexion.php');
  require_once('../Conexion/ConexionPDO.php');
  session_start();
$con = new ConexionPDO();
    $id_val = $_REQUEST['id_val'];
    $valor = $_REQUEST['valor'];
    #** Buscar Detalle Afectado ***#
    $ca = $con->Listar("SELECT dc.comprobanteafectado , cp.numero 
        FROM gf_detalle_comprobante_pptal dc 
        LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico 
        WHERE dc.id_unico =$id_val");
    if(count($ca)>0){
        $cnt = $con->Listar("SELECT dc.id_unico ,  IF(dc.valor>0, dc.valor, dc.valor*-1), dc.valor  
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico     
            WHERE dc.detallecomprobantepptal = ".$ca[0][0]." AND tc.clasecontable = 14 
            AND cn.numero = ".$ca[0][1]." ");
        if(count($cnt)>0){
            $valorac    = $valor;
            $valor_com  = $cnt[0][1];
            if($valorac>$valor_com){
                $valorac = $valor_com;
            }
            if($cnt[0][2]>0){
                    $valorac = $valorac;;
            } else {
                    $valorac   = $valorac *-1;
            }
            $updateSQL = "UPDATE gf_detalle_comprobante   
            SET valor = $valorac    
            WHERE id_unico = ".$cnt[0][0];
          $resultado = $mysqli->query($updateSQL);
          if ($resultado == true){
              $updateSQL = "UPDATE gf_detalle_comprobante_pptal  
                SET valor = $valor    
                WHERE id_unico = $id_val";
              $resultado = $mysqli->query($updateSQL);
          } else {
              $resultado = false;
          }
        } else {
            $updateSQL = "UPDATE gf_detalle_comprobante_pptal  
                SET valor = $valor    
                WHERE id_unico = $id_val";
              $resultado = $mysqli->query($updateSQL);
        }
    } else { 
      $updateSQL = "UPDATE gf_detalle_comprobante_pptal  
        SET valor = $valor    
        WHERE id_unico = $id_val";
      $resultado = $mysqli->query($updateSQL);
    }
  if($resultado == true)
    echo 1;
  else
    echo 0;
?>
