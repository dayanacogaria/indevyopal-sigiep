<?php 
require_once('Conexion/conexion.php');
session_start();
$calendario = CAL_GREGORIAN;
$anno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$mesI = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
$diaI = '01';
$fechaInicial = $anno.'-'.$mesI.'-'.$diaI;
$mesF = $mysqli->real_escape_string(''.$_POST['sltmesf'].'');
$diaF = cal_days_in_month($calendario, $mesF, $anno); 
$fechaFinal = $anno.'-'.$mesF.'-'.$diaF;
$fechaComparar = $anno.'-'.'01-01';
$codigoI =$mysqli->real_escape_string(''.$_POST['sltcodi'].'');
$codigoF=$mysqli->real_escape_string(''.$_POST['sltcodf'].'');


#VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_tesoreria ';
$mysqli->query($vaciarTabla);

#CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
$select ="SELECT DISTINCT
            c.id_unico, 
            c.codi_cuenta,
            c.nombre,
            c.naturaleza,
            ch.codi_cuenta 
          FROM
            gf_cuenta c
          LEFT JOIN
            gf_cuenta ch ON c.predecesor = ch.id_unico
          LEFT JOIN 
            gf_detalle_comprobante dc ON dc.cuenta = c.id_unico 
          WHERE dc.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
              AND c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF'  
          ORDER BY 
            c.codi_cuenta DESC";
$select1 = $mysqli->query($select);


while($row = mysqli_fetch_row($select1)){
    #GUARDA LOS DATOS EN LA TABLA TEMPORAL
          $insert= "INSERT INTO temporal_consulta_tesoreria "
                  . "(id_cuenta, numero_cuenta, nombre,cod_predecesor, naturaleza) "
                  . "VALUES ('$row[0]','$row[1]','$row[2]','$row[4]','$row[3]' )";
          $mysqli->query($insert);
        
      #SI FECHA INICIAL =01 DE ENERO
      $fechaPrimera = $anno.'-01-01';
      if ($fechaInicial==$fechaPrimera){
            #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
            $fechaMax = $anno.'-12-31';
            $com= "SELECT SUM(valor)
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE
                      dc.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                      AND cc.id_unico = '5' 
                      AND dc.cuenta = '$row[0]' ";
            $com = $mysqli->query($com);
            if(mysqli_num_rows($com)>0) {
              $saldo = mysqli_fetch_row($com);
              $saldo = $saldo[0];
            } else {
                  $saldo=0;
            }
            
            #DEBITOS
            $deb="SELECT SUM(valor)
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE valor>0 AND 
                      dc.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                      AND cc.id_unico != '5' 
                      AND dc.cuenta = '$row[0]'";
            $debt = $mysqli->query($deb);
            if(mysqli_num_rows($debt)>0){
            $debito = mysqli_fetch_row($debt);
            $debito = $debito[0];
            } else {
                $debito=0;
            }
            
            #CREDITOS
            $cr = "SELECT SUM(valor)
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE valor<0 AND 
                      dc.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                      AND cc.id_unico != '5' 
                      AND dc.cuenta = '$row[0]'";
            $cred = $mysqli->query($cr);
            if(mysqli_num_rows($cred)>0){
            $credito = mysqli_fetch_row($cred);
            $credito = $credito[0];
            } else {
                $credito=0;
            }
            
#SI FECHA INICIAL !=01 DE ENERO
      } else { 
            #TRAE EL SALDO INICIAL
            $sInicial = "SELECT SUM(valor) from gf_detalle_comprobante WHERE cuenta='$row[0]' AND fecha >='$fechaPrimera' AND fecha <'$fechaInicial'";
            $sald = $mysqli->query($sInicial);
            if(mysqli_num_rows($sald)>0){
            $saldo = mysqli_fetch_row($sald);
            $saldo = $saldo[0];
            } else {
                $saldo=0;
            }
            #DEBITOS
            $deb = "SELECT SUM(valor) FROM gf_detalle_comprobante WHERE valor>0 AND cuenta='$row[0]' AND 
                      fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
            $debt = $mysqli->query($deb);
            if(mysqli_num_rows($debt)>0){
            $debito = mysqli_fetch_row($debt);
            $debito = $debito[0];
            } else {
                $debito=0;
            }
            #CREDITOS
            $cr = "SELECT SUM(valor) FROM gf_detalle_comprobante WHERE valor<0 AND cuenta='$row[0]' AND 
                      fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
            $cred = $mysqli->query($cr);
            
            if(mysqli_num_rows($cred)>0){
            $credito = mysqli_fetch_row($cred);
            $credito = $credito[0];
            } else {
                $credito=0;
            }
      
    }
    #SI LA NATURALEZA ES DEBITO
    if($row[3]=='1'){
        if($credito <0){
                $credito =(float) substr($credito, '1');
        }
        $saldoNuevo =$saldo+$debito-$credito;
        $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial ='$saldo', "
                . "debito = '$debito', "
                . "credito ='$credito', "
                . "nuevo_saldo ='$saldoNuevo' "
                . "WHERE id_cuenta ='$row[0]'";
        $update = $mysqli->query($update);
    #SI LA NATURALEZA ES CREDITO
    }else{
            if($credito <0){
                $credito =(float) substr($credito, '1');
            }
            $saldoNuevo =$saldo-$credito+$debito;
             $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial ='$saldo', "
                . "debito = '$credito', "
                . "credito ='$debito', "
                . "nuevo_saldo ='$saldoNuevo' "
                . "WHERE id_cuenta ='$row[0]'";
            $update = $mysqli->query($update);
    }
   
    
      
}     
#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
$acum = "SELECT id_cuenta,numero_cuenta, cod_predecesor, saldo_inicial, debito, credito, nuevo_saldo "
        . "FROM temporal_consulta_tesoreria "
        . "ORDER BY numero_cuenta DESC ";
$acum = $mysqli->query($acum);

while ($rowa= mysqli_fetch_row($acum)){
    if(!empty($rowa[2])){
        $va= "SELECT id_cuenta, saldo_inicial, debito, credito, nuevo_saldo "
                . "FROM temporal_consulta_tesoreria WHERE numero_cuenta ='$rowa[2]'";
        $va = $mysqli->query($va);
        $va= mysqli_fetch_row($va);
        $saldoIn= $rowa[3]+$va[1];
        $debitoN= $rowa[4]+$va[2];
        $creditoN= $rowa[5]+$va[3];
        $nuevoN=$rowa[6]+$va[4];
        $updateA = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial ='$saldoIn', "
                . "debito = '$debitoN', "
                . "credito ='$creditoN', "
                . "nuevo_saldo ='$nuevoN' "
                . "WHERE numero_cuenta ='$rowa[2]'";
        $updateA = $mysqli->query($updateA);
    }
}