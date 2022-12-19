<?php
#######################################################################################################
#           *********       Modificaciones      *********       #
#######################################################################################################
#21/12/2017 |Erica G.| No tome en cuenta el comprobante cierre- Parametrizacion Año
#29/09/2017 |Erica G. | ARCHIVO CREADO
#######################################################################################################

session_start();
    
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require_once('../jsonPptal/funcionesPptal.php');
ini_set('max_execution_time', 0);
ob_start();
$compania = $_SESSION['compania'];
$parmanno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$foliador   = $_POST['foliador'];
$annionom = anno($parmanno);
$calendario = CAL_GREGORIAN;
$anno =$annionom; 
$mesI = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
$diaI = '01';
$fechaInicial = $anno.'-'.$mesI.'-'.$diaI;
$diaF = cal_days_in_month($calendario, $mesI, $anno); 
$fechaFinal = $anno.'-'.$mesI.'-'.$diaF;
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
          WHERE c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF'  
              AND c.parametrizacionanno = $parmanno 
          ORDER BY 
            c.codi_cuenta DESC";
$select1 = $mysqli->query($select);


while($row = mysqli_fetch_row($select1)){
    #GUARDA LOS DATOS EN LA TABLA TEMPORAL
          $insert= "INSERT INTO temporal_consulta_tesoreria "
                  . "(id_cuenta, numero_cuenta, nombre,cod_predecesor, naturaleza) "
                  . "VALUES ('$row[0]','$row[1]','$row[2]','$row[4]','$row[3]' )";
          $mysqli->query($insert);
} 


//CONSULTO LAS CUENTAS QUE TENGAN MOVIMIENTO

$mov = "SELECT DISTINCT c.id_unico, c.codi_cuenta, "
        . "c.nombre, c.naturaleza FROM gf_detalle_comprobante dc "
        . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
        . "WHERE c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF' AND c.parametrizacionanno = $parmanno "
        . "ORDER BY c.codi_cuenta DESC";
$mov= $mysqli->query($mov);
$totaldeb=0;
$totalcred=0;
$totalsaldoID =0;
$totalsaldoIC =0;
$totalsaldoFD =0;
$totalsaldoFC =0;

while($row = mysqli_fetch_row($mov)){
      #SI FECHA INICIAL =01 DE ENERO
      $fechaPrimera = $anno.'-01-01';
      if ($fechaInicial==$fechaPrimera){
            #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
            $fechaMax = $anno.'-12-31';
            ##############SALDO DEBITO###########
             $com= "SELECT SUM(valor)
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE valor>0 AND 
                      cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                      AND cc.id_unico = '5' 
                      AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
            $com = $mysqli->query($com);
            if(mysqli_num_rows($com)>0) {
              $saldo = mysqli_fetch_row($com);
              if(($saldo[0]=="" || $saldo[0]=='NULL')){
                  $saldodebito = 0;
              } else {
                  $saldodebito = $saldo[0];
              }
            } else {
                  $saldodebito=0;
            }
            
            ##############SALDO CREDITO###########
             $com= "SELECT SUM(valor)
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE valor<0 AND 
                      cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                      AND cc.id_unico = '5' 
                      AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
            $com = $mysqli->query($com);
            if(mysqli_num_rows($com)>0) {
              $saldo = mysqli_fetch_row($com);
              if($saldo[0]=="" || $saldo[0]=='NULL'){
                  $saldocredito = 0;
              } else {
                  $saldocredito = $saldo[0];
              }
              
            } else {
                $saldocredito=0;
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
                      cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                      AND cc.id_unico != '5' AND cc.id_unico !='20'  
                      AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno ";
            $debt = $mysqli->query($deb);
            if(mysqli_num_rows($debt)>0){
            $debito = mysqli_fetch_row($debt);
                if($debito[0]=="" || $debito[0]=='NULL'){
                    $debito =  0;
                } else {
                    $debito = $debito[0];
                }
            
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
                      cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                      AND cc.id_unico != '5' AND cc.id_unico !='20' 
                      AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
            $cred = $mysqli->query($cr);
            if(mysqli_num_rows($cred)>0){
                $credito = mysqli_fetch_row($cred);
                if($credito[0]=="" || $credito[0]=='NULL'){
                    $credito=0;
                } else {
                    $credito = $credito[0];
                }
            
            } else {
                $credito=0;
            }
            
#SI FECHA INICIAL !=01 DE ENERO
      } else { 
            #TRAE EL SALDO INICIAL DEBITO
            $sInicial = "SELECT SUM(dc.valor) 
                    from 
                        gf_detalle_comprobante dc 
                    LEFT JOIN 
                        gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                    LEFT JOIN
                        gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                    LEFT JOIN
                        gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                    WHERE dc.cuenta='$row[0]' AND dc.valor>0 
                        AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaInicial' 
                        AND cn.parametrizacionanno =$parmanno AND  cc.id_unico !='20'";
            $sald = $mysqli->query($sInicial);
            if(mysqli_num_rows($sald)>0){
                $saldo = mysqli_fetch_row($sald);
                if($saldo[0]=="" || $saldo[0]=='NULL'){
                    $saldodebito = 0;
                } else {
                    $saldodebito = $saldo[0];
                }
            } else {
                $saldodebito=0;
            }
            
            #TRAE EL SALDO INICIAL CREDITO
            $sInicial = "SELECT SUM(dc.valor) 
                    from 
                        gf_detalle_comprobante dc 
                    LEFT JOIN 
                        gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                    LEFT JOIN
                        gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                    LEFT JOIN
                        gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                    WHERE dc.cuenta='$row[0]' AND dc.valor<0 
                        AND cn.fecha >='$fechaPrimera' 
                        AND cn.fecha <'$fechaInicial' AND cn.parametrizacionanno =$parmanno  "
                    . "AND  cc.id_unico !='20'";
            $sald = $mysqli->query($sInicial);
            if(mysqli_num_rows($sald)>0){
            $saldo = mysqli_fetch_row($sald);
                if($saldo[0]=="" || $saldo[0]=='NULL'){
                  $saldocredito = 0;
              } else {
                  $saldocredito = $saldo[0];
              }
            } else {
                $saldocredito=0;
            }
            
            #DEBITOS
            $deb = "SELECT SUM(dc.valor) 
                    FROM 
                        gf_detalle_comprobante dc 
                    LEFT JOIN 
                        gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                    LEFT JOIN
                        gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                    LEFT JOIN
                        gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                    WHERE dc.valor>0 AND dc.cuenta='$row[0]' AND 
                      cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                      AND cn.parametrizacionanno =$parmanno AND  cc.id_unico !='20'";
            $debt = $mysqli->query($deb);
            if(mysqli_num_rows($debt)>0){
                $debito = mysqli_fetch_row($debt);
                if($debito[0]=="" || $debito[0]=='NULL'){
                    $debito =  0;
                } else {
                    $debito = $debito[0];
                }
            } else {
                $debito=0;
            }
            #CREDITOS
            $cr = "SELECT SUM(dc.valor) 
                    FROM 
                        gf_detalle_comprobante dc 
                    LEFT JOIN 
                        gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                    LEFT JOIN
                        gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                    LEFT JOIN
                        gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                    WHERE dc.valor<0 AND dc.cuenta='$row[0]' AND 
                        cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
                        AND cn.parametrizacionanno =$parmanno AND  cc.id_unico !='20'";
            $cred = $mysqli->query($cr);
            
            if(mysqli_num_rows($cred)>0){
                $credito = mysqli_fetch_row($cred);
                if($credito[0]=="" || $credito[0]=='NULL'){
                    $credito=0;
                } else {
                    $credito = $credito[0];
                }
            } else {
                $credito=0;
            }
      
    }
    if($debito<0){
        $debito = $debito *-1;
    }
    if($credito<0){
        $credito = $credito *-1;
    }
    if($saldodebito<0){
        $saldodebito = $saldodebito*-1;
    }
    if($saldocredito<0){
        $saldocredito = $saldocredito*-1;
    }
    
    #SI LA NATURALEZA ES DEBITO
    if($row[3]=='1'){
        
        $saldoNuevo =($saldodebito+$debito)-($saldocredito+$credito);
    
        if($saldoNuevo > 0){
            $nuevoSaldodebito = $saldoNuevo;
            $nuevoSaldoCredito = 0;
        } else {
            $nuevoSaldoCredito = $saldoNuevo*-1;
            $nuevoSaldodebito = 0;
        }
         $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial_debito ='$saldodebito', "
                . "saldo_inicial_credito = '$saldocredito', "
                . "debito = '$debito', "
                . "credito ='$credito', "
                . "nuevo_saldo_debito ='$nuevoSaldodebito',"
                . "nuevo_saldo_credito = '$nuevoSaldoCredito' "
                . "WHERE id_cuenta ='$row[0]'";
        $update = $mysqli->query($update);
        
        $d = $debito;
        $c = $credito;
        $sid =$saldodebito;
        $sic =$saldocredito;
        $nsd =$nuevoSaldodebito;
        $nsc =$nuevoSaldoCredito;
    #SI LA NATURALEZA ES CREDITO
    }else{
        $saldoNuevo =($saldodebito+$debito)-($saldocredito+$credito);
    
        if($saldoNuevo > 0){
            $nuevoSaldodebito = $saldoNuevo;
            $nuevoSaldoCredito = 0;
        } else {
            $nuevoSaldoCredito = $saldoNuevo*-1;
            $nuevoSaldodebito = 0;
        }
            $update = "UPDATE temporal_consulta_tesoreria "
               . "SET saldo_inicial_debito='$saldocredito', "
               . "saldo_inicial_credito = '$saldodebito',"
               . "debito = '$credito', "
               . "credito ='$debito', "
               . "nuevo_saldo_debito ='$nuevoSaldoCredito',"
               . "nuevo_saldo_credito = '$nuevoSaldodebito' "
               . "WHERE id_cuenta ='$row[0]'";
            $update = $mysqli->query($update);
            $d=$credito;
            $c = $debito;
            $sid =$saldocredito;
            $sic =$saldodebito;
            $nsd =$nuevoSaldoCredito;
            $nsc =$nuevoSaldodebito;
    }
    
    //var_dump($row[1]>=$codigoI || $row[1]<=$codigoF);
    if($row[1]>=$codigoI || $row[1]<=$codigoF){
        
        $totaldeb=$totaldeb+$d;
        $totalcred=$totalcred+$c;
        $totalsaldoID +=$sid;
        $totalsaldoIC +=$sic;
        $totalsaldoFD +=$nsd;
        $totalsaldoFC +=$nsc;
        
    }
   
    
      
}      

#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
$acum = "SELECT id_cuenta,numero_cuenta, cod_predecesor, "
        . "saldo_inicial_debito,saldo_inicial_credito,"
        . "debito, credito, "
        . "nuevo_saldo_debito, nuevo_saldo_credito "
        . "FROM temporal_consulta_tesoreria "
        . "ORDER BY numero_cuenta DESC ";
$acum = $mysqli->query($acum);

while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_cuenta,numero_cuenta, cod_predecesor, "
            . "saldo_inicial_debito, saldo_inicial_credito,"
            . "debito, credito, "
            . "nuevo_saldo_debito, nuevo_saldo_credito "
        . "FROM temporal_consulta_tesoreria WHERE id_cuenta ='$rowa1[0]'"
        . "ORDER BY numero_cuenta DESC ";
    $acumd = $mysqli->query($acumd);
    while ($rowa= mysqli_fetch_row($acumd)){
        if(!empty($rowa[2])){
    
        $va11= "SELECT numero_cuenta,"
                . "saldo_inicial_debito, saldo_inicial_credito,"
                . "debito, credito, "
                . "nuevo_saldo_debito, nuevo_saldo_credito "
                . "FROM temporal_consulta_tesoreria WHERE numero_cuenta ='$rowa[2]'";
       
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $saldoInD= $rowa[3]+$va[1];
        $saldoInC= $rowa[4]+$va[2];
        $debitoN= $rowa[5]+$va[3];
        $creditoN= $rowa[6]+$va[4];
        $nuevoND=$rowa[7]+$va[5];
        $nuevoNC=$rowa[8]+$va[6];
        $updateA = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial_debito ='$saldoInD',"
                . "saldo_inicial_credito ='$saldoInC', "
                . "debito = '$debitoN', "
                . "credito ='$creditoN', "
                . "nuevo_saldo_debito ='$nuevoND', "
                . "nuevo_saldo_credito ='$nuevoNC' "
                . "WHERE numero_cuenta ='$rowa[2]'";
        $updateA = $mysqli->query($updateA);
    }
    }
}

#*************CONSULTA DATOS COMPAÑIA*************#
$compa=$_SESSION['compania'];
$comp="SELECT
  t.razonsocial,
  IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
  t.numeroidentificacion, 
  CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)),
  t.ruta_logo,
  d.direccion,
  tel.valor
FROM
  gf_tercero t
LEFT JOIN
  gf_direccion d ON d.tercero = t.id_unico
LEFT JOIN
  gf_telefono tel ON tel.tercero = t.id_unico
WHERE
  t.id_unico =$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
$nitcompania = $comp[1];
$ruta = $comp[2];
$direccion = $comp[3];
$telefono = $comp[4];
$usuario = $_SESSION['usuario'];
$meses = array( "01" => 'Enero', "02" => 'Febrero', "03" => 'Marzo',"04" => 'Abril', "05" => 'Mayo', "06" => 'Junio', 
                "07" => 'Julio', "08" => 'Agosto', "09" => 'Septiembre', "10" => 'Octubre', "11" => 'Noviembre', "12" => 'Diciembre');

$mesnom = mb_strtoupper($meses[$mesI]);

class PDF extends FPDF
{
function Header()
{ 
        global $foliador;
        global $nombreCompania;
        global $nitcompania;
        global $numpaginas;
        $numpaginas = $this->PageNo();
        global $tam;
        global $ruta;
        global $mesnom;
        global $annionom;
        if($foliador=='Si'){
            
            if ($ruta != '') {
                $this->Image('../'.$ruta,10,8,20);
            }
            
            $this->SetFont('Arial', 'B', 10);
            $this->SetX(35);
            $this->MultiCell($tam, 5, utf8_decode($nombreCompania), 0, 'C');
            $this->SetX(35);
            $this->Cell($tam, 5, $nitcompania, 0, 0, 'C');
            $this->Ln(4);
            $this->SetX(35);
            $this->Cell($tam, 5, utf8_decode('LIBRO INVENTARIOS Y BALANCES'), 0, 0, 'C');
            $this->Ln(4);
            $this->SetX(35);
            $this->Cell($tam, 5, utf8_decode($mesnom.' DE '.$annionom), 0, 0, 'C');
            $this->Ln(10);
        } else {
            $this->SetFont('Arial', 'B', 10);
            $this->Ln(15);
            $this->SetX(35);
            $this->Cell($tam, 5, utf8_decode($mesnom.' DE '.$annionom), 0, 0, 'C');
            $this->Ln(10);
        }
    
    
    }      
    
function Footer(){
}
}

$pdf = new PDF('P','mm','Letter');        
$pdf->AliasNbPages();
$pdf->AddPage();

$cnt = 0;

#Consulta Cuentas
if(!empty($_POST['ndigitos'])){
$dig = $_POST['ndigitos'];
$sql3 = "SELECT DISTINCT 
                tem.numero_cuenta           as numcuen, 
                tem.nombre                  as cnom,
                tem.saldo_inicial_debito    as salinid,
                tem.saldo_inicial_credito   as salinic,
                tem.debito                  as deb,
                tem.credito                 as cred,
                tem.nuevo_saldo_debito      as nsald,
                tem.nuevo_saldo_credito     as nsalc , 
                tem.naturaleza              as naturaleza, 
                cta.cuentapuente            as cpuente 
from temporal_consulta_tesoreria tem
LEFT JOIN       gf_cuenta cta       ON cta.codi_cuenta = tem.numero_cuenta
WHERE tem.saldo_inicial_debito IS NOT NULL AND tem.debito IS NOT NULL AND tem.credito IS NOT NULL 
AND tem.nuevo_saldo_debito IS NOT NULL 
AND LENGTH(tem.numero_cuenta)<=$dig  
ORDER BY tem.numero_cuenta ASC";
} else {
  $sql3 = "SELECT DISTINCT 
                tem.numero_cuenta           as numcuen, 
                tem.nombre                  as cnom,
                tem.saldo_inicial_debito    as salinid,
                tem.saldo_inicial_credito   as salinic,
                tem.debito                  as deb,
                tem.credito                 as cred,
                tem.nuevo_saldo_debito      as nsald,
                tem.nuevo_saldo_credito     as nsalc , 
                tem.naturaleza              as naturaleza , 
                cta.cuentapuente            as cpuente 
from temporal_consulta_tesoreria tem
LEFT JOIN       gf_cuenta cta       ON cta.codi_cuenta = tem.numero_cuenta
WHERE tem.saldo_inicial_debito IS NOT NULL AND tem.debito IS NOT NULL AND tem.credito IS NOT NULL 
AND tem.nuevo_saldo_debito IS NOT NULL   
ORDER BY tem.numero_cuenta ASC"; 
}
$ccuentas = $mysqli->query($sql3);

$saldd = 0;
$saldc = 0;
$debit = 0;
$credit = 0;
$nsaldd = 0;
$nsaldc = 0;
$x = 1;
while ($filactas = mysqli_fetch_array($ccuentas)) 
{
        $a = $pdf->GetY();
        if($a>240)
        {
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(30,10, utf8_decode('Código'),1,0,'C');
            $pdf->Cell(95,10,utf8_decode('Nombre'),1,0,'C');
            $pdf->Cell(33,10,utf8_decode('Saldo Débito'),1,0,'C');
            $pdf->Cell(33,10,utf8_decode('Saldo Crédito'),1,0,'C');
            $pdf->Ln(10);
            
        }
        
        $saldd      = (float)($filactas['salinid']);
        $saldc      = (float)($filactas['salinic']);
        $debit      = (float)($filactas['deb']);
        $credit     = (float)($filactas['cred']);
        $nsaldd     = (float)($filactas['nsald']);
        $nsaldc     = (float)($filactas['nsalc']);
        $codi_cuenta =$filactas['numcuen']; 
        $naturaleza = $filactas['naturaleza']; 
        $pdf->SetFont('Arial','',8);
        if(strlen($codi_cuenta)<=1){
            $a = $pdf->GetY();
            if($a>240)
            {
                $pdf->AddPage();
            }
            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(190,4,utf8_decode('CLASE   '.$filactas['numcuen']),0,0,'L');   
            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(30,10, utf8_decode('Código'),1,0,'C');
            $pdf->Cell(95,10,utf8_decode('Nombre'),1,0,'C');
            $pdf->Cell(33,10,utf8_decode('Saldo Débito'),1,0,'C');
            $pdf->Cell(33,10,utf8_decode('Saldo Crédito'),1,0,'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(30,4,utf8_decode($filactas['numcuen']),0,0,'L');   
            $y = $pdf->GetY();
            $x = $pdf->GetX();        
            $pdf->MultiCell(95,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');   
            $y2 = $pdf->GetY();
            $h = $y2-$y;
            $px = $x + 95;
            $pdf->Ln(-$h);
            $pdf->SetX($px);
            
            if($naturaleza ==1) {
                $totalsI = $saldd -$saldc;
                if($totalsI>0){
                  $salddebito  =$totalsI;
                  $saldcredito =0;
                } else {
                    $salddebito  =0;
                    $saldcredito =$totalsI*-1;
                }
                $totalnS = $nsaldd-$nsaldc;
                if($totalnS>0){
                  $nsalddebito  =$totalnS;
                  $nsaldcredito =0;
                } else {
                    $nsalddebito  =0;
                    $nsaldcredito =$totalnS*-1;
                }
            }  else {
                $totalsI = $saldc -$saldd;
                if($totalsI>0){
                  $saldcredito  =$totalsI;
                  $salddebito =0;
                } else {
                    $saldcredito  =0;
                    $salddebito =$totalsI*-1;
                }
                $totalnS = $nsaldc-$nsaldd;
                if($totalnS>0){
                  $nsaldcredito  =$totalnS;
                  $nsalddebito =0;
                } else {
                    $nsaldcredito  =0;
                    $nsalddebito =$totalnS*-1;
                }
            
                
            }
            $pdf->Cell(33,4,number_format($nsalddebito,2,'.',','),0,0,'R');
            $pdf->Cell(33,4,number_format($nsaldcredito,2,'.',','),0,0,'R');
            $pdf->Ln($h);
         } 
         else { 
            $npa = $pdf->PageNo();
            
            if($npa==1 && $x==1)
            {
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(30,10, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(95,10,utf8_decode('Nombre'),1,0,'C');
                $pdf->Cell(33,10,utf8_decode('Saldo Débito'),1,0,'C');
                $pdf->Cell(33,10,utf8_decode('Saldo Crédito'),1,0,'C');
                $pdf->Ln(10);
                $x=2;

            }
            $pdf->SetFont('Arial','',8);
            if($filactas['cpuente']==1){
        
            } else {
            if ($saldd == 0  && $saldc == 0  && $debit == 0  && $credit == 0 )
            {   
                #########si los hijos tienen saldo####
                $sh = "SELECT id_unico, 
                        SUM(IF(saldo_inicial_debito<0, saldo_inicial_debito*-1,saldo_inicial_debito))   as salID,
                        SUM(IF(saldo_inicial_credito<0, saldo_inicial_credito*-1,saldo_inicial_credito))   as salIC,
                        SUM(IF(debito<0, debito*-1,debito))   as debI, 
                        SUM(IF(credito<0, credito*-1,credito))   as credI,
                        SUM(IF(nuevo_saldo_debito<0, nuevo_saldo_debito*-1,nuevo_saldo_debito))   as salID , 
                        SUM(IF(nuevo_saldo_credito<0, nuevo_saldo_credito*-1,nuevo_saldo_credito))   as salIC  
                        FROM temporal_consulta_tesoreria 
                        WHERE saldo_inicial_debito IS NOT NULL AND saldo_inicial_credito IS NOT NULL 
                        AND debito IS NOT NULL AND credito IS NOT NULL 
                        AND nuevo_saldo_debito IS NOT NULL AND nuevo_saldo_credito IS NOT NULL
                        AND cod_predecesor = ".$filactas['numcuen']; 
                $sh = $mysqli->query($sh);
                if(mysqli_num_rows($sh)>0){
                    $rowc = mysqli_fetch_row($sh);
                     if($naturaleza ==1) {
                             $totalsI = $saldd -$saldc;
                            if($totalsI>0){
                              $salddebito  =$totalsI;
                              $saldcredito =0;
                            } else {
                                $salddebito  =0;
                                $saldcredito =$totalsI*-1;
                            }
                            $totalnS = $nsaldd-$nsaldc;
                            if($totalnS>0){
                              $nsalddebito  =$totalnS;
                              $nsaldcredito =0;
                            } else {
                                $nsalddebito  =0;
                                $nsaldcredito =$totalnS*-1;
                            }
                        }  else {
                             $totalsI = $saldc -$saldd;
                            if($totalsI>0){
                              $saldcredito  =$totalsI;
                              $salddebito =0;
                            } else {
                                $saldcredito  =0;
                                $salddebito =$totalsI*-1;
                            }
                            $totalnS = $nsaldc-$nsaldd;
                            if($totalnS>0){
                              $nsaldcredito  =$totalnS;
                              $nsalddebito =0;
                            } else {
                                $nsaldcredito  =0;
                                $nsalddebito =$totalnS*-1;
                            }


                        }
                    if ($salddebito == 0  && $saldcredito == 0  && $debit == 0 && $credit == 0 && $nsalddebito == 0 && $nsaldcredito == 0)
                    {   
                    } else {
                        $pdf->Cell(30,4,utf8_decode($filactas['numcuen']),0,0,'L');   
                        $y = $pdf->GetY();
                        $x = $pdf->GetX();        
                        $pdf->MultiCell(95,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');   
                        $y2 = $pdf->GetY();
                        $h = $y2-$y;
                        $px = $x + 95;
                        $pdf->Ln(-$h);
                        $pdf->SetX($px);
                       
                       
                        $pdf->Cell(33,4,number_format($nsalddebito,2,'.',','),0,0,'R');
                        $pdf->Cell(33,4,number_format($nsaldcredito,2,'.',','),0,0,'R');
                        $pdf->Ln($h);
                    }
                } else {

                }

            } else {
               if($naturaleza ==1) {
                    $totalsI = $saldd -$saldc;
                    if($totalsI>0){
                      $salddebito  =$totalsI;
                      $saldcredito =0;
                    } else {
                        $salddebito  =0;
                        $saldcredito =$totalsI*-1;
                    }
                    $totalnS = $nsaldd-$nsaldc;
                    if($totalnS>0){
                      $nsalddebito  =$totalnS;
                      $nsaldcredito =0;
                    } else {
                        $nsalddebito  =0;
                        $nsaldcredito =$totalnS*-1;
                    }
                }  else {
                    $totalsI = $saldc -$saldd;
                    if($totalsI>0){
                      $saldcredito  =$totalsI;
                      $salddebito =0;
                    } else {
                        $saldcredito  =0;
                        $salddebito =$totalsI*-1;
                    }
                    $totalnS = $nsaldc-$nsaldd;
                    if($totalnS>0){
                      $nsaldcredito  =$totalnS;
                      $nsalddebito =0;
                    } else {
                        $nsaldcredito  =0;
                        $nsalddebito =$totalnS*-1;
                    }


                }
                
                
                if ($salddebito == 0  && $saldcredito == 0  && $debit == 0 && $credit == 0 && $nsalddebito == 0 && $nsaldcredito == 0)
                {   
                } else {
                
                
                $pdf->Cell(30,4,utf8_decode($filactas['numcuen']),0,0,'L');   
                $y = $pdf->GetY();
                $x = $pdf->GetX();        
                $pdf->MultiCell(95,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');   
                $y2 = $pdf->GetY();
                $h = $y2-$y;
                $px = $x + 95;
                $pdf->Ln(-$h);
                $pdf->SetX($px);
                $pdf->Cell(33,4,number_format($nsalddebito,2,'.',','),0,0,'R');
                $pdf->Cell(33,4,number_format($nsaldcredito,2,'.',','),0,0,'R');
                $pdf->Ln($h);
                }
            }  
            }
        }
    }
$pdf->Ln(3);    
############TOTALES##########################
$pdf->SetFont('Arial','B',8);   
$pdf->Cell(191,0.5,utf8_decode(''),1,0,'C'); 
$pdf->Ln(2);
$pdf->Cell(125,4,utf8_decode('TOTALES'),0,0,'R');
$pdf->Cell(33,4,number_format($totalsaldoFD,2,'.',','),0,0,'R');
$pdf->Cell(33,4,number_format($totalsaldoFC,2,'.',','),0,0,'R');
$pdf->Ln(10);

$pdf->AddPage();
$pdf->Ln(3);
 ##########################RESUMEN#################################################
 $rs = "SELECT DISTINCT id_unico as id, 
          numero_cuenta         as codigo, 
          nombre                as nombre, 
          saldo_inicial_debito  as iniciald, 
          saldo_inicial_credito as inicialc, 
          debito                as debito, 
          credito               as credito, 
          nuevo_saldo_debito    as nuevod,
          nuevo_saldo_credito   as nuevoc, 
          naturaleza            as naturalezaR 
          
        FROM temporal_consulta_tesoreria 
        WHERE LENGTH(numero_cuenta) = (1) ORDER BY numero_cuenta ASC";
 $rs = $mysqli->query($rs);
 
 $pdf->Cell(191,4,utf8_decode('RESUMEN'),1,0,'C'); 
 $pdf->Ln(4);
 $pdf->Cell(30,8,utf8_decode('CÓDIGO'),1,0,'C'); 
 $pdf->Cell(95,8,utf8_decode('NOMBRE'),1,0,'C'); 
 $pdf->Cell(33,8,utf8_decode('SALDO DÉBITO'),1,0,'C');
 $pdf->Cell(33,8,utf8_decode('SALDO CRÉDITO'),1,0,'C');
 $pdf->Ln(8);
$anteriortotald =0;
$anteriortotalc =0;
$debitototal=0;
$creditototal=0;
$nuevototald=0;
$nuevototalc=0;
$pdf->SetFont('Arial','',8);  

 while ($row1 = mysqli_fetch_array($rs)) {
        $saldd   = (float)($row1['iniciald']);
        $saldc   = (float)($row1['inicialc']);
        $debit  = (float)($row1['debito']);
        $credit = (float)($row1['credito']);
        $nsaldd  = (float)($row1['nuevod']);
        $nsaldc  = (float)($row1['nuevoc']);
        $naturalezaR = $row1['naturalezaR'];
        
        if($naturalezaR ==1) {
            $totalsalID =$saldd-$saldc;
            if($totalsalID>0){
                $salddebR = $totalsalID;
                $saldcredR =0;
            } else {
                $salddebR =0;
                $saldcredR =$totalsalID*-1;
            }

            $totalsalIN =$nsaldd-$nsaldc;
            if($totalsalIN>0){
                $nsalddR = $totalsalIN;
                $nsaldcR =0;
            } else {
                $nsalddR =0;
                $nsaldcR =$totalsalIN*-1;
            }
        } else {
            $totalsalID =$saldc-$saldd;
            if($totalsalID>0){
                $saldcredR = $totalsalID;
                $salddebR =0;
            } else {
                $saldcredR =0;
                $salddebR =$totalsalID*-1;
            }

            $totalsalIN =$nsaldc-$nsaldd;
            if($totalsalIN>0){
                $nsaldcR = $totalsalIN;
                $nsalddR =0;
            } else {
                $nsaldcR =0;
                $nsalddR =$totalsalIN*-1;
            }
        }
        $pdf->Cell(30,4,utf8_decode($row1['codigo']),0,0,'R');   
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(95,4,utf8_decode(ucwords(mb_strtolower($row1['nombre']))),0,'L');   
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 95;
        $pdf->Ln(-$h);
        $pdf->SetX($px);
        $pdf->Cell(33,4,number_format($nsalddR,2,'.',','),0,0,'R');
        $pdf->Cell(33,4,number_format($nsaldcR,2,'.',','),0,0,'R');
        $pdf->Ln($h);
        
        $debitototal +=$debit;
        $creditototal +=$credit;
        switch ($row1['codigo']){
            case 1:
                $anteriortotald +=$salddebR;
                $anteriortotalc -=$saldcredR;
                $nuevototald +=$nsalddR;
                $nuevototalc -=$nsaldcR;
            break;
            case 2:
                $anteriortotald -=$salddebR;
                $anteriortotalc +=$saldcredR;
                $nuevototald -=$nsalddR;
                $nuevototalc +=$nsaldcR;
            break;
            case 3:
                $anteriortotald -=$salddebR;
                $anteriortotalc +=$saldcredR;
                $nuevototald -=$nsalddR;
                $nuevototalc +=$nsaldcR;
            break;
            case 4:
                $anteriortotald -=$salddebR;
                $anteriortotalc +=$saldcredR;
                $nuevototald -=$nsalddR;
                $nuevototalc +=$nsaldcR;
            break;
            case 5:
                $anteriortotald +=$salddebR;
                $anteriortotalc -=$saldcredR;
                $nuevototald +=$nsalddR;
                $nuevototalc -=$nsaldcR;
            break;
            case 6:
                $anteriortotald +=$salddebR;
                $anteriortotalc -=$saldcredR;
                $nuevototald +=$nsalddR;
                $nuevototalc -=$nsaldcR;
            break;
            case 7:
                $anteriortotald +=$salddebR;
                $anteriortotalc -=$saldcredR;
                $nuevototald +=$nsalddR;
                $nuevototalc -=$nsaldcR;
            break;
            default :
                $anteriortotald +=$salddebR;
                $anteriortotalc +=$saldcredR;
                $nuevototald +=$nsalddR;
                $nuevototalc +=$nsaldcR;
            break;
                    
        }
             
}
##################################################################################
##############################TOTALES#############################################
$pdf->SetFont('Arial','B',8);   
$pdf->Cell(191,0.5,utf8_decode(''),1,0,'C'); 
$pdf->Ln(2);
$pdf->Cell(125,4,utf8_decode('TOTALES'),0,0,'C'); 
$pdf->Cell(33,4,number_format($nuevototald,2,'.',','),0,0,'R');
$pdf->Cell(33,4,number_format($nuevototalc,2,'.',','),0,0,'R');
$pdf->Ln(2);
################################ ESTRUCTURA FIRMAS ##########################################
######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','B',9);
 $pdf->Ln(30);
 
 $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        WHERE LOWER(td.nombre) ='libro inventarios' and td.compania = $compania ORDER BY rd.orden ASC";
 $res= $mysqli->query($res);
 $i=0;
 $x=130;
 #ESTRUCTURA
 if(mysqli_num_rows($res)>0){
     $h=4;
     while ($row2 = mysqli_fetch_row($res)) {
         $ter = "SELECT IF(CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos) 
                    IS NULL OR CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos) = '',
                    (tr.razonsocial),
                    CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos)) AS NOMBREC, "
                 . "tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional "
                 . "FROM gf_tercero tr "
                 . "LEFT JOIN gf_cargo_tercero ct ON tr.id_unico = ct.tercero "
                 . "LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico "
                 . "WHERE tr.id_unico ='$row2[0]'";
         
         $ter = $mysqli->query($ter);
         $ter = mysqli_fetch_row($ter);
         if(!empty($ter[3])){
                 $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n T.P:".(mb_strtoupper($ter[3]));
         } else {
             $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n";
         }
         
         $pdf->MultiCell(110,4, utf8_decode($responsable),0,'L');
        
         if($i==1){
           $pdf->Ln(15);
           $x=130;
           $i=0;
         } else {
         $pdf->Ln(-25);
         $pdf->SetX($x);
         $x=$x+110;
          $i=$i+1;
         }
        
     }
     
 } 
 ##################################################################################
      
       
 while (ob_get_length()) {
  ob_end_clean();
}
$pdf->Output(0,utf8_decode('Libro_Inventarios_balances('.date('d-m-Y').').pdf'),0);

?>