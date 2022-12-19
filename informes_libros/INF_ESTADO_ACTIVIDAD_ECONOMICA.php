<?php
#######################################################################################################
#           *********       Modificaciones      *********       #
#######################################################################################################
#21/12/2017 |Erica G.| No tome en cuenta el comprobante cierre
#05/10/2017 |Erica G. | ARCHIVO CREADO
#######################################################################################################

session_start();
    
require'../Conexion/conexion.php';
require_once('../jsonPptal/funcionesPptal.php');
ini_set('max_execution_time', 0);
ob_start();

$parmanno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$foliador   = $_POST['foliador'];
$annionom = anno($parmanno);
$calendario = CAL_GREGORIAN;
$anno =$annionom; 
$mesI = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
$diaI = '01';
$fechaInicial = $anno . '-01-01';
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
        . "WHERE c.codi_cuenta BETWEEN '$codigoI' AND '$codigoF' "
        . "ORDER BY c.codi_cuenta DESC";
$mov = $mysqli->query($mov);
$totaldeb = 0;
$totalcred = 0;
$totalsaldoI = 0;
$totalsaldoF = 0;

while ($row = mysqli_fetch_row($mov)) {
    #SI FECHA INICIAL =01 DE ENERO
    $fechaPrimera = $anno . '-01-01';
    if ($fechaInicial == $fechaPrimera) {
        #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
        $fechaMax = $anno . '-12-31';
        $com = "SELECT SUM(valor)
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE
                      cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                      AND cc.id_unico = '5' 
                      AND cp.parametrizacionanno = $parmanno 
                      AND dc.cuenta = '$row[0]' ";
        $com = $mysqli->query($com);
        if (mysqli_num_rows($com) > 0) {
            $saldo = mysqli_fetch_row($com);
            if(($saldo[0]=="" || $saldo[0]=='NULL')){
                $saldo = 0;
            } else {
                $saldo = $saldo[0];
            }
        } else {
            $saldo = 0;
        }

        #DEBITOS
        $deb = "SELECT SUM(valor)
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
                      AND cc.id_unico != '5' AND cc.id_unico != '20' 
                      AND cp.parametrizacionanno = $parmanno 
                      AND dc.cuenta = '$row[0]'";
        $debt = $mysqli->query($deb);
        if (mysqli_num_rows($debt) > 0) {
            $debito = mysqli_fetch_row($debt);
            if(($debito[0]=="" || $debito[0]=='NULL')){
                $debito = 0;
            } else {
                $debito = $debito[0];
            }
        } else {
            $debito = 0;
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
                      AND cc.id_unico != '5' AND cc.id_unico != '20' 
                      AND cp.parametrizacionanno = $parmanno 
                      AND dc.cuenta = '$row[0]'";
        $cred = $mysqli->query($cr);
        if (mysqli_num_rows($cred) > 0) {
            $credito = mysqli_fetch_row($cred);
            if(($credito[0]=="" || $credito[0]=='NULL')){
                $credito = 0;
            } else {
                $credito = $credito[0];
            }
        } else {
            $credito = 0;
        }

#SI FECHA INICIAL !=01 DE ENERO
    } else {
        #TRAE EL SALDO INICIAL
        $sInicial = "SELECT SUM(dc.valor) 
                FROM 
                    gf_detalle_comprobante dc 
                LEFT JOIN 
                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN
                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                LEFT JOIN
                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico  
                WHERE dc.cuenta='$row[0]' 
                AND cc.id_unico != '20' 
                AND cn.parametrizacionanno = $parmanno 
                AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaInicial'";
        $sald = $mysqli->query($sInicial);
        if (mysqli_num_rows($sald) > 0) {
            $saldo = mysqli_fetch_row($sald);
            if(($saldo[0]=="" || $saldo[0]=='NULL')){
                $saldo = 0;
            } else {
                $saldo = $saldo[0];
            }
        } else {
            $saldo = 0;
        }
        #DEBITOS
        $deb = "SELECT 
                    SUM(dc.valor) 
                FROM 
                    gf_detalle_comprobante dc 
                LEFT JOIN 
                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN
                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                LEFT JOIN
                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico  
                WHERE dc.valor>0 AND dc.cuenta='$row[0]' 
                    AND cc.id_unico != '20' 
                    AND cn.parametrizacionanno = $parmanno 
                    AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
        $debt = $mysqli->query($deb);
        if (mysqli_num_rows($debt) > 0) {
            $debito = mysqli_fetch_row($debt);
            if(($debito[0]=="" || $debito[0]=='NULL')){
                $debito = 0;
            } else {
                $debito = $debito[0];
            }
        } else {
            $debito = 0;
        }
        #CREDITOS
        $cr = "SELECT 
                    SUM(dc.valor) 
                FROM 
                    gf_detalle_comprobante dc 
                LEFT JOIN 
                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN
                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                LEFT JOIN
                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                WHERE dc.valor<0 AND dc.cuenta='$row[0]' 
                    AND cc.id_unico != '20' 
                    AND cn.parametrizacionanno = $parmanno 
                    AND cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' ";
        $cred = $mysqli->query($cr);

        if (mysqli_num_rows($cred) > 0) {
            $credito = mysqli_fetch_row($cred);
             if(($credito[0]=="" || $credito[0]=='NULL')){
                $credito = 0;
            } else {
                $credito = $credito[0];
            }
        } else {
            $credito = 0;
        }
    }
    
    #*******************SALDO INICIAL***************#
   $sql = "SELECT SUM(valor)
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN
                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                WHERE
                  cp.fecha BETWEEN '$fechaInicial' AND '$fechaMax' 
                  AND cc.id_unico = '5' 
                  AND cp.parametrizacionanno = $parmanno 
                  AND dc.cuenta = '$row[0]' ";
    $sql = $mysqli->query($sql);
    if (mysqli_num_rows($sql) > 0) {
        $saldoIm = mysqli_fetch_row($sql);
        if(($saldoIm[0]=="" || $saldoIm[0]=='NULL')){
            $saldoIni = 0;
        } else {
            $saldoIni = $saldoIm[0];
        }
    } else {
        $saldoIni = 0;
    }
        
    #SI LA NATURALEZA ES DEBITO
    if ($row[3] == '1') {
        if ($credito < 0) {
            $credito = (float) substr($credito, '1');
        }
        $saldoNuevo = $saldo + $debito - $credito;
        $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial ='$saldoIni', "
                . "debito = '$debito', "
                . "credito ='$credito', "
                . "nuevo_saldo ='$saldoNuevo' "
                . "WHERE id_cuenta ='$row[0]'";
        $update = $mysqli->query($update);

        $d = $debito;
        $c = $credito;
        #SI LA NATURALEZA ES CREDITO
    } else {
        if ($credito < 0) {
            $credito = (float) substr($credito, '1');
        }
        $saldoNuevo = $saldo - $credito + $debito;
        $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial ='$saldoIni', "
                . "debito = '$credito', "
                . "credito ='$debito', "
                . "nuevo_saldo ='$saldoNuevo' "
                . "WHERE id_cuenta ='$row[0]'";
        $update = $mysqli->query($update);

        $d = $credito;
        $c = $debito;
    }

    
    if ($row[1] >= $codigoI || $row[1] <= $codigoF) {

        $totaldeb = $totaldeb + $d;
        $totalcred = $totalcred + $c;
    }
}
#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
$acum = "SELECT id_cuenta,numero_cuenta, cod_predecesor, saldo_inicial, debito, credito, nuevo_saldo "
        . "FROM temporal_consulta_tesoreria "
        . "ORDER BY numero_cuenta DESC ";
$acum = $mysqli->query($acum);

while ($rowa1 = mysqli_fetch_row($acum)) {
    $acumd = "SELECT id_cuenta,numero_cuenta, cod_predecesor, saldo_inicial, debito, credito, nuevo_saldo "
            . "FROM temporal_consulta_tesoreria WHERE id_cuenta ='$rowa1[0]'"
            . "ORDER BY numero_cuenta DESC ";
    $acumd = $mysqli->query($acumd);
    while ($rowa = mysqli_fetch_row($acumd)) {
        if (!empty($rowa[2])) {


            $va11 = "SELECT numero_cuenta,saldo_inicial, debito, credito, nuevo_saldo "
                    . "FROM temporal_consulta_tesoreria WHERE numero_cuenta ='$rowa[2]'";

            $va1 = $mysqli->query($va11);
            $va = mysqli_fetch_row($va1);
            $saldoIn = $rowa[3] + $va[1];
            $debitoN = $rowa[4] + $va[2];
            $creditoN = $rowa[5] + $va[3];
            $nuevoN = $rowa[6] + $va[4];
            $updateA = "UPDATE temporal_consulta_tesoreria "
                    . "SET saldo_inicial ='$saldoIn', "
                    . "debito = '$debitoN', "
                    . "credito ='$creditoN', "
                    . "nuevo_saldo ='$nuevoN' "
                    . "WHERE numero_cuenta ='$rowa[2]'";
            $updateA = $mysqli->query($updateA);
        }
    }
}

#*************CONSULTA DATOS COMPAÑIA*************#
$compania =$_SESSION['compania'];
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
#****** Generar¨Pdf *********#
$t = $_GET['t'];
if($t==1){
    require'../fpdf/fpdf.php';
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
            $this->Cell($tam, 5, utf8_decode('ESTADO ACTIVIDAD ECONOMICA Y FINANCIERA'), 0, 0, 'C');
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
                        tem.numero_cuenta   as numcuen, 
                        tem.nombre          as cnom,
                        tem.saldo_inicial   as salini,
                        tem.debito          as deb,
                        tem.credito         as cred,
                        tem.nuevo_saldo     as nsal,
                        cta.auxiliartercero as auxt, 
                        cta.cuentapuente    as cpuente 
        FROM            temporal_consulta_tesoreria tem
        LEFT JOIN       gf_cuenta cta       ON cta.codi_cuenta = tem.numero_cuenta 
        WHERE           LENGTH(tem.numero_cuenta)<=$dig  
        ORDER BY        tem.numero_cuenta   ASC";
} else {
  $sql3 = "SELECT DISTINCT 
                        tem.numero_cuenta   as numcuen, 
                        tem.nombre          as cnom,
                        tem.saldo_inicial   as salini,
                        tem.debito          as deb,
                        tem.credito         as cred,
                        tem.nuevo_saldo     as nsal,
                        cta.auxiliartercero as auxt, 
                        cta.cuentapuente    as cpuente, 
                        cta.naturaleza      as naturaleza 
        FROM            temporal_consulta_tesoreria tem
        LEFT JOIN       gf_cuenta cta       ON cta.codi_cuenta = tem.numero_cuenta 
        ORDER BY        tem.numero_cuenta   ASC";
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
            $pdf->Cell(25,10, utf8_decode('Código'),1,0,'C');
            $pdf->Cell(46,10,utf8_decode('Nombre'),1,0,'C');
            $pdf->Cell(30,10,utf8_decode('Año '. $anno),1,0,'C');
            $pdf->Cell(30,10,utf8_decode('Año '. ($anno-1)),1,0,'C');
            $pdf->Cell(30,10,utf8_decode('Variación ($)'),1,0,'C');
            $pdf->Cell(30,10,utf8_decode('Variación (%)'),1,0,'C');
            $pdf->Ln(10);
            
        }
        
        $sald        = (float) ($filactas['salini']);
        $nsald       = (float) ($filactas['nsal']);
        $varpe       =  ($nsald - $sald);
        if($sald ==0 || $sald =='0.00'){
            $varpo       =  0;
        } else {
        $varpo       =  ($nsald/$sald);
        }
        $codi_cuenta = $filactas['numcuen']; 
        $naturaleza  = $filactas['naturaleza']; 
        var_dump (number_format($nsald, 2, '.', ',') == '0.00' 
                && number_format($sald, 2, '.', ',') == '0.00' 
                && number_format($varpe, 2, '.', ',') == '0.00' 
                && number_format($varpo, 2, '.', ',') == '0.00'     
                 );
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
            $pdf->Cell(25,10, utf8_decode('Código'),1,0,'C');
            $pdf->Cell(46,10,utf8_decode('Nombre'),1,0,'C');
            $pdf->Cell(30,10,utf8_decode('Año '. $anno),1,0,'C');
            $pdf->Cell(30,10,utf8_decode('Año '. ($anno-1)),1,0,'C');
            $pdf->Cell(30,10,utf8_decode('Variación ($)'),1,0,'C');
            $pdf->Cell(30,10,utf8_decode('Variación (%)'),1,0,'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(25,4,utf8_decode($filactas['numcuen']),0,0,'L');   
            $y = $pdf->GetY();
            $x = $pdf->GetX();        
            $pdf->MultiCell(46,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');   
            $y2 = $pdf->GetY();
            $h = $y2-$y;
            $px = $x + 46;
            $pdf->Ln(-$h);
            $pdf->SetX($px);
            $pdf->Cell(30, 4, number_format($nsald, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(30, 4, number_format($sald, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(30, 4, number_format($varpe, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(30, 4, number_format($varpo, 2, '.', ','), 0, 0, 'R');
            $pdf->Ln($h);
         } 
         else { 
            $npa = $pdf->PageNo();
            
            if($npa==1 && $x==1)
            {
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(25,10, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(46,10,utf8_decode('Nombre'),1,0,'C');
                $pdf->Cell(30,10,utf8_decode('Año '. $anno),1,0,'C');
                $pdf->Cell(30,10,utf8_decode('Año '. ($anno-1)),1,0,'C');
                $pdf->Cell(30,10,utf8_decode('Variación ($)'),1,0,'C');
                $pdf->Cell(30,10,utf8_decode('Variación (%)'),1,0,'C');
                $pdf->Ln(10);
                $x=2;

            }
            $pdf->SetFont('Arial','',8);
            if($filactas['cpuente']==1){
        
            } else {
            if (number_format($nsald, 2, '.', ',') == '0.00' 
                && number_format($sald, 2, '.', ',') == '0.00' 
                && number_format($varpe, 2, '.', ',') == '0.00' 
                && number_format($varpo, 2, '.', ',') == '0.00'     
                 ) {   
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
                    #echo 'ac';
                    if($rowc[0] == NULL && $rowc[1] == NULL && $rowc[2] == NULL && $rowc[3] == NULL 
                    && $rowc[4] == NULL && $rowc[5] == NULL && $rowc[6] == NULL ){  } else { 
                        $pdf->Cell(25,4,utf8_decode($filactas['numcuen']),0,0,'L');   
                        $y = $pdf->GetY();
                        $x = $pdf->GetX();        
                        $pdf->MultiCell(46,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');   
                        $y2 = $pdf->GetY();
                        $h = $y2-$y;
                        $px = $x + 46;
                        $pdf->Ln(-$h);
                        $pdf->SetX($px);
                        $pdf->Cell(30, 4, number_format($nsald, 2, '.', ','), 0, 0, 'R');
                        $pdf->Cell(30, 4, number_format($sald, 2, '.', ','), 0, 0, 'R');
                        $pdf->Cell(30, 4, number_format($varpe, 2, '.', ','), 0, 0, 'R');
                        $pdf->Cell(30, 4, number_format($varpo, 2, '.', ','), 0, 0, 'R');
                        $pdf->Ln($h);
                    }
                    
                } else {

                }

            } else {
               
                if (number_format($nsald, 2, '.', ',') == '0.00' 
                && number_format($sald, 2, '.', ',') == '0.00' 
                && number_format($varpe, 2, '.', ',') == '0.00' 
                && number_format($varpo, 2, '.', ',') == '0.00'     
                 )  {
                } else {
                
                
                $pdf->Cell(25,4,utf8_decode($filactas['numcuen']),0,0,'L');   
                $y = $pdf->GetY();
                $x = $pdf->GetX();        
                $pdf->MultiCell(46,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');   
                $y2 = $pdf->GetY();
                $h = $y2-$y;
                $px = $x + 46;
                $pdf->Ln(-$h);
                $pdf->SetX($px);
                $pdf->Cell(30, 4, number_format($nsald, 2, '.', ','), 0, 0, 'R');
                $pdf->Cell(30, 4, number_format($sald, 2, '.', ','), 0, 0, 'R');
                $pdf->Cell(30, 4, number_format($varpe, 2, '.', ','), 0, 0, 'R');
                $pdf->Cell(30, 4, number_format($varpo, 2, '.', ','), 0, 0, 'R');
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

$pdf->AddPage();
$pdf->Ln(3);
 ##########################RESUMEN#################################################
 $rs = "SELECT DISTINCT id_unico as id, 
                 numero_cuenta  as codigo, 
                 nombre         as nombre, 
                 saldo_inicial  as inicial, 
                 debito         as debito, 
                 credito        as credito, 
                 nuevo_saldo    as nuevo, 
                 naturaleza     as naturalezaR 
               FROM temporal_consulta_tesoreria 
               WHERE LENGTH(numero_cuenta) = (1) ORDER BY numero_cuenta ASC";
 $rs = $mysqli->query($rs);
 $pdf->SetFont('Arial','B',8);
 if($_POST['tipoInforme']==1){
    $pdf->Cell(191,4,utf8_decode('CUADRO BALANCE'),1,0,'C'); 
 } else {
        $pdf->Cell(191,4,utf8_decode('CUADRO UTILIDAD'),1,0,'C'); 
 }
 $pdf->Ln(4);
 $pdf->Cell(25,8,utf8_decode('CÓDIGO'),1,0,'C'); 
 $pdf->Cell(45,8,utf8_decode('NOMBRE'),1,0,'C'); 
 $pdf->Cell(30,8,utf8_decode('AÑO '. $anno),1,0,'C');
 $pdf->Cell(30,8,utf8_decode('AÑO '. ($anno-1)),1,0,'C');
 $pdf->Cell(30,8,utf8_decode('VARIACIÓN ($)'),1,0,'C');
 $pdf->Cell(30,8,utf8_decode('VARIACIÓN (%)'),1,0,'C');
 $pdf->ln(8);

$pdf->SetFont('Arial','',8);  
$anteriortotal =0;
$debitototal=0;
$creditototal=0;
$nuevototal=0;

    while ($row1 = mysqli_fetch_array($rs)) {
        $sald   = (float)($row1['inicial']);
        $nsald  = (float)($row1['nuevo']);
        $varpe  = $nsald -$sald;
        if(number_format($sald,2,'.',',')=='0.00'){
            $varpo  = 0;
        } else {
            $varpo  = ($nsald/$sald);
        }
        $naturalezaR = $row1['naturalezaR'];
        
        $pdf->Cell(25,4,utf8_decode($row1['codigo']),0,0,'R');   
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(46,4,utf8_decode(ucwords(mb_strtolower($row1['nombre']))),0,'L');   
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 46;
        $pdf->Ln(-$h);
        $pdf->SetX($px);
        $pdf->Cell(30,4,number_format($nsald,2,'.',','),0,0,'R');
        $pdf->Cell(30,4,number_format($sald,2,'.',','),0,0,'R');
        $pdf->Cell(30,4,number_format($varpe,2,'.',','),0,0,'R');
        $pdf->Cell(30,4,number_format($varpo,2,'.',','),0,0,'R');
        $pdf->Ln($h);
       switch ($row1['codigo']){
                   case 1:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   case 2:
                       $anteriortotal -=$sald;
                       $nuevototal -=$nsald;
                   break;
                   case 3:
                       $anteriortotal -=$sald;
                       $nuevototal -=$nsald;
                   break;
                   case 4:
                       $anteriortotal -=$sald;
                       $nuevototal -=$nsald;
                   break;
                   case 5:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   case 6:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   case 7:
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
                   default :
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;


               }
             
}
##################################################################################
##############################TOTALES#############################################
$pdf->SetFont('Arial','B',8);   
$pdf->Cell(191,0.5,utf8_decode(''),1,0,'C'); 
$pdf->Ln(2);
$nvpe = ((float)$nuevototal-(float)$anteriortotal);
if(number_format($anteriortotal,2,'.',',') ==0 || number_format($anteriortotal,2,'.',',') =='0.00') {
    $nvpo = 0;
} else {
    $nvpo = ((float)$nuevototal/(float)$anteriortotal);
}
$pdf->Cell(71,4,utf8_decode('TOTALES'),0,0,'C'); 
$pdf->Cell(30,4,number_format($nuevototal,2,'.',','),0,0,'R');
$pdf->Cell(30,4,number_format($anteriortotal,2,'.',','),0,0,'R');
$pdf->Cell(30,4,number_format($nvpe,2,'.',','),0,0,'R');
$pdf->Cell(30,4,number_format($nvpo,2,'.',','),0,0,'R');
$pdf->Ln(2);
################################ ESTRUCTURA FIRMAS ##########################################
######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','B',9);
 $pdf->Ln(30);
 
 $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        WHERE LOWER(td.nombre) ='estado actividad economica' AND td.compania = $compania ORDER BY rd.orden ASC";
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
         $pdf->Ln(-20);
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
$pdf->Output(0,utf8_decode('Estado_Actividad_Economica('.date('d-m-Y').').pdf'),0);
}
#****** Generar¨Excel********#
else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=InformeEstadoActividadEconomica.xls");
    ?>
    <html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe Auxiliar Contable Retenciones</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <th colspan="6" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $nombreCompania ?>
        <br/><?php echo $nitcompania;?>
        <br/>&nbsp;
        <br/>ESTADO ACTIVIDAD ECONOMICA Y FINANCIERA
        <br/><?php echo $mesnom.' DE '.$annionom ?>
        <br/>&nbsp;                 
        </strong>
    </th>
        <tr></tr> 
        <?php
        
    $cnt = 0;

    #Consulta Cuentas
    if(!empty($_POST['ndigitos'])){
        $dig = $_POST['ndigitos'];
        $sql3 = "SELECT DISTINCT 
                                tem.numero_cuenta   as numcuen, 
                                tem.nombre          as cnom,
                                tem.saldo_inicial   as salini,
                                tem.debito          as deb,
                                tem.credito         as cred,
                                tem.nuevo_saldo     as nsal,
                                cta.auxiliartercero as auxt, 
                                cta.cuentapuente    as cpuente ,
                                cta.naturaleza      as naturaleza 
                FROM            temporal_consulta_tesoreria tem
                LEFT JOIN       gf_cuenta cta       ON cta.codi_cuenta = tem.numero_cuenta 
                WHERE           LENGTH(tem.numero_cuenta)<=$dig  
                ORDER BY        tem.numero_cuenta   ASC";
    } else {
      $sql3 = "SELECT DISTINCT 
                            tem.numero_cuenta   as numcuen, 
                            tem.nombre          as cnom,
                            tem.saldo_inicial   as salini,
                            tem.debito          as deb,
                            tem.credito         as cred,
                            tem.nuevo_saldo     as nsal,
                            cta.auxiliartercero as auxt, 
                            cta.cuentapuente    as cpuente, 
                            cta.naturaleza      as naturaleza 
            FROM            temporal_consulta_tesoreria tem
            LEFT JOIN       gf_cuenta cta       ON cta.codi_cuenta = tem.numero_cuenta 
            ORDER BY        tem.numero_cuenta   ASC";
    }

    $ccuentas = $mysqli->query($sql3);

    $saldd  = 0;
    $saldc  = 0;
    $debit  = 0;
    $credit = 0;
    $nsaldd = 0;
    $nsaldc = 0;
    $x = 1;
    while ($filactas = mysqli_fetch_array($ccuentas)) 
    {
        $sald        = (float) ($filactas['salini']);
        $nsald       = (float) ($filactas['nsal']);
        $varpe       =  ($nsald - $sald);
        if($sald ==0 || $sald =='0.00'){
            $varpo       =  0;
        } else {
        $varpo       =  ($nsald/$sald);
        }
        $codi_cuenta = $filactas['numcuen']; 
        $naturaleza  = $filactas['naturaleza']; 
        
        if(strlen($codi_cuenta)<=1){
            echo '<tr>';
            echo '<td colspan="6"><strong><i>&nbsp;<br/>CLASE '.$filactas['numcuen'].'<br/>&nbsp;<i></strong></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td><strong>Código</strong></td>';
            echo '<td><strong>Nombre</strong></td>';
            echo '<td><strong>Año '.$anno.'</strong></td>';
            echo '<td><strong>Año '.($anno-1).'</strong></td>';
            echo '<td><strong>Variación ($)</strong></td>';
            echo '<td><strong>Variación (%)</strong></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>'.$filactas['numcuen'].'</td>';
            echo '<td>'.ucwords(mb_strtolower($filactas['cnom'])).'</td>';
            echo '<td>'.number_format($nsald, 2, '.', ',').'</td>';
            echo '<td>'.number_format($sald, 2, '.', ',').'</td>';
            echo '<td>'.number_format($varpe, 2, '.', ',').'</td>';
            echo '<td>'.number_format($varpo, 2, '.', ',').'</td>';
            echo '</tr>';
            
         } 
        else {
            if($filactas['cpuente']==1){

            } else {
                if (number_format($nsald, 2, '.', ',') == '0.00' 
                && number_format($sald, 2, '.', ',') == '0.00' 
                && number_format($varpe, 2, '.', ',') == '0.00' 
                && number_format($varpo, 2, '.', ',') == '0.00'     
                 ) {   
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
                    #echo 'ac';
                    if($rowc[0] == NULL && $rowc[1] == NULL && $rowc[2] == NULL && $rowc[3] == NULL 
                    && $rowc[4] == NULL && $rowc[5] == NULL && $rowc[6] == NULL ){  } else { 
                        echo '<tr>';
                        echo '<td>'.$filactas['numcuen'].'</td>';
                        echo '<td>'.ucwords(mb_strtolower($filactas['cnom'])).'</td>';
                        echo '<td>'.number_format($nsald, 2, '.', ',').'</td>';
                        echo '<td>'.number_format($sald, 2, '.', ',').'</td>';
                        echo '<td>'.number_format($varpe, 2, '.', ',').'</td>';
                        echo '<td>'.number_format($varpo, 2, '.', ',').'</td>';
                        echo '</tr>';
                    }

                } else {

                }

            } else {

                if (number_format($nsald, 2, '.', ',') == '0.00' 
                && number_format($sald, 2, '.', ',') == '0.00' 
                && number_format($varpe, 2, '.', ',') == '0.00' 
                && number_format($varpo, 2, '.', ',') == '0.00'     
                 )  {
                } else {
                    echo '<tr>';
                    echo '<td>'.$filactas['numcuen'].'</td>';
                    echo '<td>'.ucwords(mb_strtolower($filactas['cnom'])).'</td>';
                    echo '<td>'.number_format($nsald, 2, '.', ',').'</td>';
                    echo '<td>'.number_format($sald, 2, '.', ',').'</td>';
                    echo '<td>'.number_format($varpe, 2, '.', ',').'</td>';
                    echo '<td>'.number_format($varpo, 2, '.', ',').'</td>';
                    echo '</tr>';
                }
            }  
            }
        }
    }
    
     ##########################RESUMEN#################################################
     $rs = "SELECT DISTINCT id_unico as id, 
                     numero_cuenta  as codigo, 
                     nombre         as nombre, 
                     saldo_inicial  as inicial, 
                     debito         as debito, 
                     credito        as credito, 
                     nuevo_saldo    as nuevo, 
                     naturaleza     as naturalezaR 
                   FROM temporal_consulta_tesoreria 
                   WHERE LENGTH(numero_cuenta) = (1) ORDER BY numero_cuenta ASC";
     $rs = $mysqli->query($rs);
     if($_POST['tipoInforme']==1){
        echo '<tr>';
        echo '<td colspan="6"><center><strong><i>&nbsp;<br/>CUADRO BALANCE<br/>&nbsp;<i></strong></center></td>';
        echo '</tr>';
     } else {
        echo '<tr>';
        echo '<td colspan="6"><center><strong><i>&nbsp;<br/>CUADRO UTILIDAD<br/>&nbsp;<i></strong></center></td>';
        echo '</tr>';
    }
    echo '<tr>';
    echo '<td><strong>CÓDIGO</strong></td>';
    echo '<td><strong>NOMBRE</strong></td>';
    echo '<td><strong>AÑO '.$anno.'</strong></td>';
    echo '<td><strong>AÑO '.($anno-1).'</strong></td>';
    echo '<td><strong>VARIACIÓN ($)</strong></td>';
    echo '<td><strong>VARIACIÓN (%)</strong></td>';
    echo '</tr>';
    $anteriortotal =0;
    $debitototal=0;
    $creditototal=0;
    $nuevototal=0;

    while ($row1 = mysqli_fetch_array($rs)) {
        $sald   = (float)($row1['inicial']);
        $nsald  = (float)($row1['nuevo']);
        $varpe  = $nsald -$sald;
        if(number_format($sald,2,'.',',')=='0.00'){
            $varpo  = 0;
        } else {
            $varpo  = ($nsald/$sald);
        }
        $naturalezaR = $row1['naturalezaR'];
        echo '<tr>';
        echo '<td>'.$row1['codigo'].'</td>';
        echo '<td>'.ucwords(mb_strtolower($row1['nombre'])).'</td>';
        echo '<td>'.number_format($nsald, 2, '.', ',').'</td>';
        echo '<td>'.number_format($sald, 2, '.', ',').'</td>';
        echo '<td>'.number_format($varpe, 2, '.', ',').'</td>';
        echo '<td>'.number_format($varpo, 2, '.', ',').'</td>';
        echo '</tr>';
        switch ($row1['codigo']){
            case 1:
                $anteriortotal +=$sald;
                $nuevototal +=$nsald;
            break;
            case 2:
                $anteriortotal -=$sald;
                $nuevototal -=$nsald;
            break;
            case 3:
                $anteriortotal -=$sald;
                $nuevototal -=$nsald;
            break;
            case 4:
                $anteriortotal -=$sald;
                $nuevototal -=$nsald;
            break;
            case 5:
                $anteriortotal +=$sald;
                $nuevototal +=$nsald;
            break;
            case 6:
                $anteriortotal +=$sald;
                $nuevototal +=$nsald;
            break;
            case 7:
                $anteriortotal +=$sald;
                $nuevototal +=$nsald;
            break;
            default :
                       $anteriortotal +=$sald;
                       $nuevototal +=$nsald;
                   break;
        }
    }
    ##################################################################################
    ##############################TOTALES#############################################
    
    $nvpe = ((float)$nuevototal-(float)$anteriortotal);
    if(number_format($anteriortotal,2,'.',',') ==0 || number_format($anteriortotal,2,'.',',') =='0.00') {
        $nvpo = 0;
    } else {
        $nvpo = ((float)$nuevototal/(float)$anteriortotal);
    }
    echo '<tr>';
    echo '<td colspan="2"><strong>TOTALES</strong></td>';
    echo '<td><strong>'.number_format($nuevototal,2,'.',',').'</strong></td>';
    echo '<td><strong>'.number_format($anteriortotal,2,'.',',').'</strong></td>';
    echo '<td><strong>'.number_format($nvpe,2,'.',',').'</strong></td>';
    echo '<td><strong>'.number_format($nvpo,2,'.',',').'</strong></td>';
    echo '</tr>';

}
?>