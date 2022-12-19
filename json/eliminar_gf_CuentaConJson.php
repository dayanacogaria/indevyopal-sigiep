<?php
require_once('../Conexion/conexion.php');
  session_start();
   
  //Captura de ID.
    $id = $_GET['id'];
    #Buscar si el comprobante tiene retención
      $comprobante = $_SESSION['idCompCntV'];
      $queryretencion = "SELECT * FROM gf_retencion WHERE comprobante = $comprobante";
      $queryretencion = $mysqli->query($queryretencion);
      if(mysqli_num_rows($queryretencion)>0){
          #Buscar el valor y la cuenta del detalle
          $detalle = "SELECT cuenta, valor FROM gf_detalle_comprobante WHERE id_unico = $id";
          $detalle = $mysqli->query($detalle);
          $detalle = mysqli_fetch_row($detalle);
          $cuentadetalle = $detalle[0];
          $valordetalle = $detalle[1];
          
          #Buscar la retencion que tiene esa cuenta y ese comprobante  valor
          $retencion = "SELECT r.id_unico "
                  . "FROM gf_retencion r "
                  . "LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico  "
                  . "WHERE r.comprobante = $comprobante AND tr.cuenta=$cuentadetalle  "
                  . "AND r.valorretencion = $valordetalle";
          $retencion = $mysqli->query($retencion);
          if(mysqli_num_rows($retencion)>0){
              $retencion = mysqli_fetch_row($retencion);
              $idretencion = $retencion[0];
              #*Actualiza la retencion 
              $deleteRetencion = "DELETE FROM gf_retencion WHERE id_unico = $idretencion";
              $deleteRetencion = $mysqli->query($deleteRetencion);
          }
          
          
      }

    $deleteSQL = "DELETE FROM gf_detalle_comprobante WHERE Id_Unico = $id";
    $resultado = $mysqli->query($deleteSQL);

  echo json_encode($resultado);
