<?php
##################################################################################################################################################################
#                                                                                                                           Modificaciones
##################################################################################################################################################################
#24/08/2017 |Erica G. | Modificacion logo, firmas
################MODIFICACIONES######################
#07/03/2017 |ERICA G.|CONSULTAS A FUNCION
#02-02-2017 | 9:30 | Erica González //Modificacion búsqueda disponibilidades
######################################
##################################################################################################################################################################
?>
<?php
require'../../fpdf/fpdf.php';
require'../../Conexion/conexion.php';
require'consultas.php';
ini_set('max_execution_time', 0);

$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');
ob_start();
?>

<?php
$calendario = CAL_GREGORIAN;
$parmanno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0]; 
$mes = $mysqli->real_escape_string(''.$_POST['sltmes'].'');
$dia = cal_days_in_month($calendario, $mes, $anno); 
$fecha = $anno.'-'.$mes.'-'.$dia;
$fechaInicial = $anno.'-'.'01-01';
$codigoI =$mysqli->real_escape_string(''.$_POST['sltcni'].'');
$codigoF=$mysqli->real_escape_string(''.$_POST['sltcnf'].'');
$fechaFir= date('Y-m-d');

#VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos ';
$mysqli->query($vaciarTabla);

#CONSULTA TODAS LA CUENTAS
$ctas = "SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.id_unico,
            rpp2.codi_presupuesto, 
            rf.id_unico 
          FROM
            gf_rubro_pptal rpp
          LEFT JOIN
            gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
          LEFT JOIN
            gf_fuente f ON rf.fuente = f.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
         WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
          AND rpp.tipoclase=7 AND rpp.tipovigencia=1
         AND rpp.parametrizacionanno = $parmanno ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    $mysqli->query($insert);
    
}
    
#CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
#CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
#SI SELECCIONA O NO FUENTE
if(empty($_POST['fuente'])){
$select ="SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.id_unico, 
            rpp2.codi_presupuesto, 
            dcp.rubrofuente 
          FROM
            gf_detalle_comprobante_pptal dcp
          LEFT JOIN
            gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
          LEFT JOIN
            gf_fuente f ON rf.fuente = f.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
          LEFT JOIN 
            gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
           AND rpp.tipoclase=7 AND rpp.tipovigencia=1
          AND cp.parametrizacionanno = $parmanno ORDER BY rpp.codi_presupuesto ASC";
}else {
    $fuente = $_POST['fuente'];
 $select ="SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.id_unico, 
            rpp2.codi_presupuesto, 
            dcp.rubrofuente 
          FROM
            gf_detalle_comprobante_pptal dcp
          LEFT JOIN
            gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
          LEFT JOIN
            gf_fuente f ON rf.fuente = f.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
          LEFT JOIN 
            gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
          WHERE f.id_unico ='$fuente' AND cp.parametrizacionanno = $parmanno 
           AND rpp.tipoclase=7 AND rpp.tipovigencia=1
          AND rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' ORDER BY rpp.codi_presupuesto ASC";
   
}
$select1 = $mysqli->query($select);


while($row = mysqli_fetch_row($select1)){
    
    #PRESUPUESTO INICIAL
    $pptoInicial= presupuestos($row[4], 1, $fechaInicial, $fecha);
    #ADICION
    $adicion = presupuestos($row[4], 2, $fechaInicial, $fecha);
    #REDUCCION
    $reduccion = presupuestos($row[4], 3, $fechaInicial, $fecha);
    #TRAS.CRED Y CONT.
    $tras = presupuestos($row[4], 4, $fechaInicial, $fecha);
        if($tras>0){
            $trasCredito = $tras;
            $trasCont = 0;
        }else {
            $trasCredito = 0;
            $trasCont = $tras;
        }
    
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont;
    #DISPONIBILIDAD
    $disponibilidad = disponibilidades($row[4], 14, $fechaInicial, $fecha);
    #SALDO DISPONIBLE
    $saldoDisponible= $presupuestoDefinitivo-$disponibilidad;
    #REGISTROS
    $registros = disponibilidades($row[4], 15, $fechaInicial, $fecha);
    #REGISTROS ABIERTOS
    $disponibilidadesAbiertas = $disponibilidad-$registros;
    #TOTAL OBLIGACIONES
    $totalObligaciones = disponibilidades($row[4], 16, $fechaInicial, $fecha);
    #REGISTROS ABIERTOS
    $registrosAbiertos = $registros-$totalObligaciones;
    #TOTAL PAGOS
    $totalPagos= disponibilidades($row[4], 17, $fechaInicial, $fecha);
    #RESERVAS
    $reservas= $registros-$totalObligaciones;
    #CUENTAS POR PAGAR
    $cuentasxpagar = $totalObligaciones-$totalPagos;
    
    #ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "tras_credito = '$trasCredito', "
            . "tras_cont = '$trasCont', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "disponibilidades = '$disponibilidad', "
            . "saldo_disponible = '$saldoDisponible', "
            . "registros = '$registros', "
            . "registros_abiertos = '$registrosAbiertos', "
            . "total_obligaciones = '$totalObligaciones', "
            . "total_pagos = '$totalPagos', "
            . "reservas = '$reservas', "
            . "cuentas_x_pagar = '$cuentasxpagar' "
            . "WHERE rubro_fuente = '$row[4]'";
    $update = $mysqli->query($update);
          
}   
#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
$acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, tras_credito, tras_cont, "
        . "presupuesto_dfvo, disponibilidades, "
        . "saldo_disponible,registros, "
        . "registros_abiertos,total_obligaciones, "
        . "total_pagos,reservas,cuentas_x_pagar, reduccion "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
$acum = $mysqli->query($acum);

while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, tras_credito, tras_cont, "
        . "presupuesto_dfvo, disponibilidades, "
        . "saldo_disponible,registros, "
        . "registros_abiertos,total_obligaciones, "
        . "total_pagos,reservas,cuentas_x_pagar, reduccion "
        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
        . "ORDER BY cod_rubro DESC ";
    $acumd = $mysqli->query($acumd);
    while ($rowa= mysqli_fetch_row($acumd)){
        if(!empty($rowa[2])){
            $va11= "SELECT id_unico, "
            . "cod_rubro,"
            . "cod_predecesor, "
            . "ptto_inicial, adicion, tras_credito, tras_cont, "
            . "presupuesto_dfvo, disponibilidades, "
            . "saldo_disponible,registros, "
            . "registros_abiertos,total_obligaciones, "
            . "total_pagos,reservas,cuentas_x_pagar, reduccion "
            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $pptoInicialM = $rowa[3]+$va[3];
        $adicionM = $rowa[4]+$va[4];
        $trasCreditoM = $rowa[5]+$va[5];
        $trasContM = $rowa[6]+$va[6];
        $presupuestoDefinitivoM = $rowa[7]+$va[7];
        $disponibilidadM = $rowa[8]+$va[8];
        $saldoDisponibleM = $rowa[9]+$va[9];
        $registrosM = $rowa[10]+$va[10];
        $registrosAbiertosM = $rowa[11]+$va[11];
        $totalObligacionesM = $rowa[12]+$va[12];
        $totalPagosM = $rowa[13]+$va[13];
        $reservasM = $rowa[14]+$va[14];
        $cuentasxpagarM = $rowa[15]+$va[15];
        $reduccionM = $rowa[16]+$va[16];
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "tras_credito = '$trasCreditoM', "
                . "tras_cont = '$trasContM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "disponibilidades = '$disponibilidadM', "
                . "saldo_disponible = '$saldoDisponibleM', "
                . "registros = '$registrosM', "
                . "registros_abiertos = '$registrosAbiertosM', "
                . "total_obligaciones = '$totalObligacionesM', "
                . "total_pagos = '$totalPagosM', "
                . "reservas = '$reservasM', "
                . "cuentas_x_pagar = '$cuentasxpagarM' "
                . "WHERE cod_rubro = '$rowa[2]'";
        $updateA = $mysqli->query($updateA);
    }
    }
}


$hoy = date('d-m-Y');
$hoy = trim($hoy, '"');
$fecha_div = explode("-", $hoy);
$anioh = $fecha_div[2];
$mesh = $fecha_div[1];
$diah = $fecha_div[0];
$hoy = $diah.'/'.$mesh.'/'.$anioh;

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
    global $ruta;
    
    $this->setX(0);
    
    $this->SetFont('Arial','B',10);
        // Título
    $this->SetY(10);
    if($ruta != '')
    {
        $this->Image('../../'.$ruta,60,6,20);
    } 
    $this->Cell(340,5,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->setX(0);
    $this->SetFont('Arial','B',8);
    $this->Cell(340,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    $this->Cell(340, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(340,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',8);
    $this->Cell(340,5,utf8_decode('EJECUCION DEL PRESUPUESTO DE GASTOS - DISPONIBILIDAD Y REGISTROS'),0,0,'C');
    $this->SetFont('Arial','B',8);
    
    $this->SetX(0);
    $this->Cell(340,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',6);
    $this->Cell(340,5,utf8_decode('MES DE '.utf8_decode (ucwords(strtoupper($month1.' - '.$anno)))),0,0,'C');
    
    $this->Ln(6);
    $this->SetFont('Arial','',7);
    $this->SetX(15);
    
    $this->Cell(15,9, utf8_decode(''),1,0,'C');#
    $this->Cell(68,9,utf8_decode(''),1,0,'C');#
    $this->Cell(15,9,utf8_decode(''),1,0,'C');################
    $this->Cell(23,9,utf8_decode(''),1,0,'C');#
    $this->Cell(92,9,utf8_decode(''),1,0,'C');# 
    $this->Cell(23,9,utf8_decode(''),1,0,'C');#
    $this->Cell(23,9,utf8_decode(''),1,0,'C');###################
    $this->Cell(23,9,utf8_decode(''),1,0,'C');#
    $this->Cell(23,9,utf8_decode(''),1,0,'C');#
    $this->Cell(23,9,utf8_decode(''),1,0,'C');#
    
        $this->SetX(15);
    
    $this->Cell(15,9, utf8_decode('RUBRO'),1,0,'C');#
    $this->Cell(68,9,utf8_decode('DETALLE'),1,0,'C');#
    $this->Cell(15,9,utf8_decode('FUENTE'),1,0,'C');################
    $this->Cell(23,8,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->Cell(92,4,utf8_decode('MODIFICACIONES PRESUPUESTALES'),0,0,'C');
    $this->Cell(23,8,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->Cell(23,9,utf8_decode('DISPONIBILIDAD'),1,0,'C');################
    $this->Cell(23,9,utf8_decode('REGISTROS'),1,0,'C');################
    $this->Cell(23,8,utf8_decode('SALDO'),0,0,'C');#
    $this->CellFitScale(23,8,utf8_decode('DISPONIBILIDADES'),0,0,'C');#
   
    $this->Ln(4);
    
    $this->SetX(15);
    
    $this->Cell(15,5, utf8_decode(''),0,0,'C');#
    $this->Cell(68,5,utf8_decode(''),0,0,'C');#
    $this->Cell(15,5,utf8_decode(''),0,0,'C');###############################
    $this->Cell(23,5,utf8_decode('INICIAL'),0,0,'C');#
    $this->Cell(23,5,utf8_decode('ADICIÓN'),1,0,'C');
    $this->Cell(23,5,utf8_decode('REDUCCIÓN'),1,0,'C');
    $this->Cell(23,5,utf8_decode('TRAS.CREDITO'),1,0,'C');#######################
    $this->Cell(23,5,utf8_decode('TRAS.CONT'),1,0,'C');
    $this->Cell(23,5,utf8_decode('DEFINITIVO'),0,0,'C');
    $this->Cell(23,5,utf8_decode(''),0,0,'C');#
    $this->Cell(23,5,utf8_decode(''),0,0,'C');#
    $this->Cell(23,5,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->Cell(23,5,utf8_decode('ABIERTAS'),0,0,'C');#

    
    $this->Ln(5);
    $this->Cell(326,5,'',0);
    }      
    
function Footer()
    {
    global $usuario;
    global $fechaActual;
    $this->SetY(-15);
    $this->SetFont('Arial','B',8);
    $this->SetX(10);
    $this->Cell(90,10,utf8_decode('Fecha: '.$fechaActual),0,0,'L');
    $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
    $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
    $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

class PDF_MC_Table extends FPDF
{
    var $widths;
    var $aligns;
function SetWidths($w)
{
//Set the array of column widths
    $this->widths=$w;
}
function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}
function fill($f)
{
    //juego de arreglos de relleno
    $this->fill=$f;
}



}

// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');        

//Asingación de valor a Mes 1    
    switch($mes)
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
        
        #Igualación de Variable local a POST
        $annio = $anno;
    
    #Consulta Compañía para Encabezado
    $compania = $_SESSION['compania'];

    $consulta = "SELECT         t.razonsocial as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum, 
                                t.ruta_logo as ruta 
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
        $nomcomp = ($fila['traz']);       
        $tipodoc = ($fila['tnom']);       
        $numdoc = ($fila['tnum']);   
        $ruta = ($fila['ruta']);   
    }

 #Declaración Variable Número de Páginas
$nb=$pdf->AliasNbPages();

$saldoT = 0;
#Fin Consulta Secundaria

#Creación Objeto FPDF

$pdf->AddPage();
//$pdf->SetMargins(20,20,20);

$pdf->AliasNbPages();
$pdf->SetFont('Arial','',6);

$codd    = 0;
$totales = 0;
$valorA = 0;

#Variables de valor de naturaleza
$debito = "";
$credito = "";
$totaldeb = 0.00;
$totalcred = 0.00;

$saldoT = 0;
$saldoTT = 0;

//$pdf->setY(37);
$cnt = 0;
#Consulta Cuentas
$sql2 = "SELECT DISTINCT 
                        cod_rubro           as codrub, 
                        nombre_rubro        as nomrub,
                        cod_fuente          as codfte,
                        ptto_inicial        as ppti,
                        adicion             as adi,
                        reduccion           as red,
                        tras_credito        as tcred,
                        tras_cont           as trcont,
                        presupuesto_dfvo    as ppdf,
                        disponibilidades    as disp,
                        saldo_disponible    as sald,
                        registros           as reg,
                        registros_abiertos  as rega,
                        total_obligaciones  as tobl,
                        total_pagos         as tpag,
                        reservas            as reserv,
                        cuentas_x_pagar     as cpag                        
from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
$conejc  = $mysqli->query($sql2);

    $pdf->SetY(32);
    $pdf->Ln(6);
    $pdf->SetFont(Arial,'',7);
  
 $total1=0;
 $total2=0;
 $total3=0;
 $total4=0;
 $total5=0;
while ($filactas = mysqli_fetch_array($conejc)) 
{
    $a = $pdf->GetY();
    if($a>175) {
      #$pdf->Ln(10);        
          $pdf->AddPage();            
     }    
    $pdf->setX(15);
    
    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['tcred'];
    $p5  = (float) $filactas['trcont'];
    $p6  = (float) $filactas['ppdf'];
    $p7  = (float) $filactas['disp'];
    $p8  = (float) $filactas['sald'];
    $p9  = (float) $filactas['reg'];
    $p10  = (float) $filactas['rega'];
    $p11 = (float) $filactas['tobl'];
    $p12 = (float) $filactas['tpag'];
    $p13 = (float) $filactas['reserv'];
    $p14 = (float) $filactas['cpag'];
    $t   = ($p6-$p7);
    $t1   = ($p7-$p9);
       # $codd = $codd + 1;
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p7==0 && $p8==0 && $p9==0 && $p10==0 && $p11==0 && $p12==0 && $p13==0)
        { } else {
            
            
        $pdf->CellFitScale(15,4,utf8_decode($filactas['codrub']),1,0,'R');
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(68,4,utf8_decode($filactas['nomrub']),1,'L');        
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 68;
        $pdf->SetXY($px, $y);
        
        $pdf->CellFitScale(15,4,utf8_decode($filactas['codfte']),1,0,'L');
        $pdf->CellFitScale(23,4,number_format($p1,2,'.',','),1,0,'R');
        $pdf->CellFitScale(23,4,number_format($p2,2,'.',','),1,0,'R');
        $pdf->CellFitScale(23,4,number_format($p3,2,'.',','),1,0,'R');
        $pdf->CellFitScale(23,4,number_format($p4,2,'.',','),1,0,'R');
        $pdf->CellFitScale(23,4,number_format($p5,2,'.',','),1,0,'R');
        $pdf->CellFitScale(23,4,number_format($p6,2,'.',','),1,0,'R');
        $pdf->CellFitScale(23,4,number_format($p7,2,'.',','),1,0,'R');
        $pdf->CellFitScale(23,4,number_format($p9,2,'.',','),1,0,'R');
        $pdf->CellFitScale(23,4,number_format($t,2,'.',','),1,0,'R');
        $pdf->CellFitScale(23,4,number_format($t1,2,'.',','),1,0,'R');
        $total1=$p6+$total1;
        $total2=$p7+$total2;
        $total3=$p9+$total3;
        $total4=$t+$total4;
        $total5=$t1+$total5;
         $pdf->Ln($h);
        }
}

## TOTALES ##
$pdf->Ln(3);
$pdf->SetX(15);
$pdf->SetFont('Arial','B',8);
  $pdf->Cell(214,4,'TOTALES: ',0,0,'R');
  $pdf->CellFitScale(23,4,number_format($total1,2,'.',','),0,0,'R');
  $pdf->CellFitScale(23,4,number_format($total2,2,'.',','),0,0,'R');
  $pdf->CellFitScale(23,4,number_format($total3,2,'.',','),0,0,'R');
  $pdf->CellFitScale(23,4,number_format($total4,2,'.',','),0,0,'R');
  $pdf->CellFitScale(23,4,number_format($total5,2,'.',','),0,0,'R');
## FIN TOTALES ##
$pdf->Ln(5);
$pdf->setX(15);

        $pdf->Cell(326,0.5,utf8_decode(''),1,0,'R');
        $pdf->Cell(30,4,utf8_decode($filactas['cnnom']),0,0,'C');

 
################################ ESTRUCTURA FIRMAS ##########################################
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','B',9);
 $pdf->Ln(20);
 $compania = $_SESSION['compania'];
 $res = "SELECT   c.nombre,rd.orden,IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) AS NOMBREC, 
                 tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional 
        FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion trel ON rd.tipo_relacion = trel.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico 
        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
        WHERE LOWER(td.nombre) ='Ejecución Presupuestal Gastos' 
        AND if(rd.fecha_inicio IS NULL, rd.fecha_inicio IS NULL, rd.fecha_inicio <= '$fechaFir') 
        AND if(rd.fecha_fin IS NULL,rd.fecha_fin IS NULL, rd.fecha_fin >= '$fechaFir') 
        AND td.compania = $compania  ORDER BY rd.orden ASC";
 $res= $mysqli->query($res);
 $i=0;
 $x=130;
 #ESTRUCTURA
$y =$pdf->GetY();
$x = $pdf->GetX();
 if(mysqli_num_rows($res)>0){  $h=4;
           $cant=mysqli_num_rows($res);
    while($F = mysqli_fetch_row($res)){

             if ($cant==3) {
               $xc=40;
            }elseif ($cant==4) {
                $xc=26;
            }elseif ($cant==2){
               $xc=80;
            }
            $pdf->SetXY($x+$xc, $y); 
            $pdf->Cell(55,0.1,'',1); 
            $pdf->Ln(2); 
            $y1 =$pdf->GetY();
            $pdf->SetXY($x+$xc, $y1); 
            $pdf->cellfitscale(45,5,utf8_decode($F[2]),0,0,'L');
            $pdf->Ln(3); 
            $y2 =$pdf->GetY();
            $pdf->SetXY($x+$xc, $y2); 
            $pdf->cellfitscale(45,5,utf8_decode($F[0]),0,0,'L');  
        
        $x = $pdf->GetX();  
     }
     
 } 
while (ob_get_length()) {
  ob_end_clean();
}  
 ######### FIN FIRMAS #########
 $pdf->Output(0,utf8_decode('Informe_Ejecuciones_Pptales_Gastos_Disponibilidad-Registros('.date('d-m-Y').').pdf'),0);
?>