<?php
#######################################################################################################
#           *********       Modificaciones      *********       #
#######################################################################################################
#21/12/2017 |Erica G.| No tome en cuenta el comprobante cierre - Parametrización año
#28/06/2017 |ERICA G.|QUEDO SOLO PARA TESORERIA 
#06/04/2017 |Erica G |MODIFICACION RESULTADOS Y VERIFICACION CONSULTAS
#04/03/2017 |Erica G |MODIFICACION DISEÑO, VERIFICACION CONSULTAS
#03/03/2017 |Erica G |MODIFICACION NOMBRES
##################################################

session_start();
    
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 0);
ob_start();

$calendario = CAL_GREGORIAN;
$parmanno = $mysqli->real_escape_string('' . $_POST['sltAnnio'] . '');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno = $an[0];

$mesI = $mysqli->real_escape_string('' . $_POST['sltmesi'] . '');
$diaI = '01';
$fechaInicial = $anno . '-' . $mesI . '-' . $diaI;
$mesF = $mysqli->real_escape_string('' . $_POST['sltmesf'] . '');
$diaF = cal_days_in_month($calendario, $mesF, $anno);
$fechaFinal = $anno . '-' . $mesF . '-' . $diaF;
$fechaComparar = $anno . '-' . '01-01';
$codigoI = $mysqli->real_escape_string('' . $_POST['sltcodi'] . '');
$codigoF = $mysqli->real_escape_string('' . $_POST['sltcodf'] . '');


#VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_tesoreria ';
$mysqli->query($vaciarTabla);

#CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
 $select = "SELECT DISTINCT
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
            AND (c.clasecuenta = 11 OR c.clasecuenta = 12 )
            AND c.parametrizacionanno = $parmanno   
          ORDER BY 
            c.codi_cuenta DESC";
$select1 = $mysqli->query($select);


while ($row = mysqli_fetch_row($select1)) {
    #GUARDA LOS DATOS EN LA TABLA TEMPORAL
    $insert = "INSERT INTO temporal_consulta_tesoreria "
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
                      AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
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
                      AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
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
                      AND dc.cuenta = '$row[0]' AND cp.parametrizacionanno =$parmanno";
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
                AND cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaInicial' 
                AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20'";
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
                    AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20'";
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
                AND cn.parametrizacionanno =$parmanno AND cc.id_unico !='20'";
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
    #SI LA NATURALEZA ES DEBITO
    if ($row[3] == '1') {
        if ($credito < 0) {
            $credito = (float) substr($credito, '1');
        }
        $saldoNuevo = $saldo + $debito - $credito;
        $update = "UPDATE temporal_consulta_tesoreria "
                . "SET saldo_inicial ='$saldo', "
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
                . "SET saldo_inicial ='$saldo', "
                . "debito = '$credito', "
                . "credito ='$debito', "
                . "nuevo_saldo ='$saldoNuevo' "
                . "WHERE id_cuenta ='$row[0]'";
        $update = $mysqli->query($update);

        $d = $credito;
        $c = $debito;
    }

    //var_dump($row[1]>=$codigoI || $row[1]<=$codigoF);
    if ($row[1] >= $codigoI || $row[1] <= $codigoF) {

        $totaldeb = $totaldeb + $d;
        $totalcred = $totalcred + $c;
    }
}



class PDF extends FPDF
{
// Cabecera de página  
function Header()
{ 
    // Logo
    //$this->Image('logo_pb.png',10,8,33);
    //Arial bold 15
    global $nomcomp;
    global $tipodoc;
    global $numdoc;

    global $month1;
    global $month2;
    global $anno;
    global $digtv;
    global $ruta ;
    
    
    $this->SetFont('Arial','B',10);
        // Título
    $this->SetY(10);
    if($ruta != '')
{
  $this->Image('../'.$ruta,80,8,20);
}
    $this->Cell(330,5,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->setX(10);
    $this->SetFont('Arial','B',8);
    $this->Cell(330,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    if(empty($digtv)) {
    $this->Cell(330, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
    } else {
        $this->Cell(330, 5,$tipodoc.': '.$numdoc.' - '.$digtv,0,0,'C'); 
    }
    $this->SetFont('Arial','B',8);
    $this->SetX(10);
    $this->Cell(330,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',8);
    $tit1 =$_POST['tituloH'];

    $this->Cell(330,5,utf8_decode('BALANCE TESORERIA CAJA Y BANCOS'),0,0,'C');
    
    $this->SetFont('Arial','B',8);
    
    $this->SetX(10);
    $this->Cell(330,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->Cell(332,5,utf8_decode('Entre '.$month1.' y '.$month2.' de '.$anno),0,0,'C');
    
    $this->Ln(5);
    
    $this->Cell(47,10, utf8_decode(''),1,0,'C');
    $this->Cell(94,10,utf8_decode(''),1,0,'C');
    $this->Cell(47,10,utf8_decode(''),1,0,'C');
    $this->Cell(94,10,utf8_decode(''),1,0,'C');
    $this->Cell(47,10,utf8_decode(''),1,0,'C');
    $this->Setx(10);
    $this->Cell(47,10, utf8_decode('Código'),0,0,'C');
    $this->Cell(94,10,utf8_decode('Nombre'),0,0,'C');
    $this->Cell(47,10,utf8_decode('Saldo Inicial'),0,0,'C');
    $this->Cell(94,5,utf8_decode('Valor'),0,0,'C');
    $this->Cell(47,10,utf8_decode('Saldo Final'),0,0,'C');
    $this->Ln(5);
    $this->Cell(47,5, utf8_decode(''),0,0,'C');
    $this->Cell(94,5,utf8_decode(''),0,0,'C');
    $this->Cell(47,5,utf8_decode(''),0,0,'C');
    $this->Cell(47,5,utf8_decode('Débito'),1,0,'C');
    $this->Cell(47,5,utf8_decode('Crédito'),1,0,'C');
    $this->Cell(47,5,utf8_decode(''),0,0,'C');
    $this->Ln(5);
    $this->Cell(326,5,'',0);
    }      
    
function Footer()
    {
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    // Número de página
        $this->Cell(15);
        $this->Cell(25,10,utf8_decode('Fecha: '.date('d-m-Y')),0,0,'L');
        $this->Cell(70);
        $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
        $this->Cell(60);
        $this->Cell(30,10,utf8_decode('Usuario: admin'),0); //.get_current_user()
        $this->Cell(70);
        $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
    }
}

// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');        

//Asingación de valor a Mes 1    
    switch($mesI)
    {
     case 1:
         $month1 = "Enero";
        break;
        case 2:
        $month1 = "Febrero";
            break;
        case 3:
        $month1 = "Marzo";
            break;
        case 4:
        $month1 = "Abril"; 
            break;
        case 5:
        $month1 = "Mayo";
            break;
        case 6:
        $month1 = "Junio";
            break;
        case 7:
        $month1 = "Julio";
            break;
        case 8:
        $month1 = "Agosto";
            break;
        case 9:
        $month1 = "Septiembre";
            break;
        case 10:
        $month1 = "Octubre";
            break;
        case 11:
        $month1 = "Noviembre";
            break;
        case 12:
        $month1 = "Diciembre";
            break;
        }
//Asingación de valor a Mes 2        
    switch($mesF)
    {
     case 1:
         $month2 = "Enero";
        break;
        case 2:
        $month2 = "Febrero";
            break;
        case 3:
        $month2 = "Marzo";
            break;
        case 4:
        $month2 = "Abril"; 
            break;
        case 5:
        $month2 = "Mayo";
            break;
        case 6:
        $month2 = "Junio";
            break;
        case 7:
        $month2 = "Julio";
            break;
        case 8:
        $month2 = "Agosto";
            break;
        case 9:
        $month2 = "Septiembre";
            break;
        case 10:
        $month2 = "Octubre";
            break;
        case 11:
        $month2 = "Noviembre";
            break;
        case 12:
        $month2 = "Diciembre";
            break;
        }
        
        #Igualación de Variable local a POST
        $annio = $anno;
    
    #Consulta Compañía para Encabezado
    $compania = $_SESSION['compania'];

    $consulta = "SELECT         t.razonsocial as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum, 
                                t.digitoverficacion as dig 
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

#Inicialización parámetros Header
$nomcomp = "";
$tipodoc = "";
$numdoc = "";
    
#Consulta para obtener parámetros Header
while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = utf8_decode($fila['traz']);       
        $tipodoc = utf8_decode($fila['tnom']);       
        $numdoc = utf8_decode($fila['tnum']);   
        $digtv = utf8_decode($fila['dig']);   
    }
$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = '.$compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];

 #Declaración Variable Número de Páginas
$nb=$pdf->AliasNbPages();

$saldoT = 0;
#Fin Consulta Secundaria

#Creación Objeto FPDF
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',8);

$codd    = 0;
$totales = 0;
$valorA = 0;

#Variables de valor de naturaleza

//$pdf->setY(37);
$pdf->SetY(39);
$cnt = 0;

#Consulta Cuentas
$sql3 = "SELECT DISTINCT 
                        numero_cuenta   as numcuen, 
                        nombre          as cnom,
                        saldo_inicial   as salini,
                        debito          as deb,
                        credito         as cred,
                        nuevo_saldo     as nsal
from temporal_consulta_tesoreria 
WHERE saldo_inicial IS NOT NULL AND debito IS NOT NULL AND credito IS NOT NULL AND nuevo_saldo IS NOT NULL
ORDER BY numero_cuenta ASC";
$ccuentas = $mysqli->query($sql3);

$sald = 0;
$debit = 0;
$credit = 0;
$nsald = 0;

//echo $totaldeb.'<br/>';
//echo    $totalcred.'<br/>';
while ($filactas = mysqli_fetch_array($ccuentas)) 
{
       # $codd = $codd + 1;
    
        $sald   = (float)($filactas['salini']);
        $debit  = (float)($filactas['deb']);
        $credit = (float)($filactas['cred']);
        $nsald  = (float)($filactas['nsal']);
        $codi_cuenta =$filactas['numcuen']; 
        if(strlen($codi_cuenta)<=2){
            $pdf->Cell(47,4,utf8_decode($filactas['numcuen']),0,0,'R');   
            $y = $pdf->GetY();
            $x = $pdf->GetX();        
            $pdf->MultiCell(94,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');
            $y2 = $pdf->GetY();
            $h = $y2-$y;
            $px = $x + 94;
            $pdf->Ln(-$h);
            $pdf->SetX($px);


             //$pdf->Cell(47,4,utf8_decode($filactas['numcuen']),0,0,'L');

             $pdf->Cell(47,4,number_format($sald,2,'.',','),0,0,'R');
             $pdf->Cell(47,4,number_format($debit,2,'.',','),0,0,'R');
             $pdf->Cell(47,4,number_format($credit,2,'.',','),0,0,'R');
             $pdf->Cell(47,4,number_format($nsald,2,'.',','),0,0,'R');
             $pdf->Ln($h);
        } else { 
        if ($sald == 0  && $debit == 0  && $credit == 0 )
        {   
        } else {
         $pdf->Cell(47,4,utf8_decode($filactas['numcuen']),0,0,'R');   
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(94,4,utf8_decode(ucwords(mb_strtolower($filactas['cnom']))),0,'L');
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 94;
        $pdf->Ln(-$h);
        $pdf->SetX($px);
         
         
         //$pdf->Cell(47,4,utf8_decode($filactas['numcuen']),0,0,'L');
         
         $pdf->Cell(47,4,number_format($sald,2,'.',','),0,0,'R');
         $pdf->Cell(47,4,number_format($debit,2,'.',','),0,0,'R');
         $pdf->Cell(47,4,number_format($credit,2,'.',','),0,0,'R');
         $pdf->Cell(47,4,number_format($nsald,2,'.',','),0,0,'R');
         $pdf->Ln($h);
        
        }
        }

       
        

    }
 ############TOTALES##########################
 $pdf->SetFont('Arial','B',8);   
 $pdf->Cell(329,0.5,utf8_decode(''),1,0,'C'); 
 $pdf->Ln(2);
 $pdf->Cell(141,4,utf8_decode(''),0,0,'C'); 
 $pdf->Cell(47,4,utf8_decode('TOTALES'),0,0,'R');
 $pdf->Cell(47,4,number_format($totaldeb,2,'.',','),0,0,'R');
 $pdf->Cell(47,4,number_format($totalcred,2,'.',','),0,0,'R');
 $pdf->Cell(47,4,utf8_decode(''),0,0,'R');
$pdf->Ln(10);


  ################################ ESTRUCTURA FIRMAS ##########################################
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','B',9);
 $pdf->Ln(10);
 $compania = $_SESSION['compania'];
 $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        WHERE LOWER(td.nombre) ='balance de prueba' AND td.compania = $compania  ORDER BY rd.orden ASC";
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
  
        
        
 while (ob_get_length()) {
  ob_end_clean();
}
 $pdf->Output(0,utf8_decode('Informe_Tesoreria_Caja_Bancos('.date('d-m-Y').').pdf'),0);
 
?>