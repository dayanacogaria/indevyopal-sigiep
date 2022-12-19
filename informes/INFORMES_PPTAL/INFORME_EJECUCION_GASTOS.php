<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#26/07/2018 |Erica G. | Encabezados
#24/08/2017 |Erica G. | Modificacion logo, firmas
#27/06/2017 | ERICA G.| ARCHIVO CREADO
#######################################################################################################
?>

<?php
require'../../fpdf/fpdf.php';
require'../../Conexion/conexion.php';
require'consultas.php';
ini_set('max_execution_time', 0);
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');
ob_start();

$calendario = CAL_GREGORIAN;
$mesI = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
$mesF = $mysqli->real_escape_string(''.$_POST['sltmesf'].'');
$codigoI =$mysqli->real_escape_string(''.$_POST['sltcodi'].'');
$codigoF = $mysqli->real_escape_string(''.$_POST['sltcodf'].'');
$parmanno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0]; 
$dia = cal_days_in_month($calendario, $mesI, $anno); 
$fecha = $anno.'-'.$mes.'-'.$dia;
$fechaFir= date('Y-m-d');
$meses = array('no', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 
    'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');

 $mesInicial = $meses[(int)$mesI];
 $mesFinal = $meses[(int)$mesF];
 $annoInforme = anno($parmanno);
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
         AND (rpp.tipoclase = 7  OR rpp.tipoclase = 9 OR rpp.tipoclase = 10) 
         AND rpp.parametrizacionanno = $parmanno 
        ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    $mysqli->query($insert);
    
}   

##CONSULTA CUENTAS SEGUN VARIABLES QUE RECIBE
##SI SELECCIONA O NO FUENTE

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
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
        AND (rpp.tipoclase = 7  OR rpp.tipoclase = 9 OR rpp.tipoclase = 10) 
        AND rpp.parametrizacionanno = $parmanno  
        ORDER BY rpp.codi_presupuesto ASC";
$select1 = $mysqli->query($select);


while($row = mysqli_fetch_row($select1)){
##########################################################################################################################################################################    
    $fechaInicial = $anno.'-01-01';
    $diaF = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFinal = $anno.'-'.$mesF.'-'.$diaF;
    #PRESUPUESTO INICIAL
    $pptoInicial= presupuestos($row[4], 1, $fechaInicial, $fechaFinal);
    #ADICION
    $adicion = presupuestos($row[4], 2, $fechaInicial, $fechaFinal);
    #REDUCCION
    $reduccion = presupuestos($row[4], 3, $fechaInicial, $fechaFinal);
    #TRAS.CRED Y CONT.
    $tras= presupuestos($row[4], 4, $fechaInicial, $fechaFinal);
        if($tras>0){
            $trasCredito= $tras;
            $trasCont = 0;
        }else {
            $trasCredito = 0;
            $trasCont= $tras;
        }
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont;
    
    
##########################################################################################################################################################################    
#   ANTERIORES
##########################################################################################################################################################################    
    $fechaIAn = $anno.'-01-01';    
    $fechaFinAn = $anno.'-'.$mesI.'-01';
    $nuevafechaAn = strtotime ( '-1 day' , strtotime ( $fechaFinAn ) ) ;
    $fechaFAn = date ( 'Y-m-d' , $nuevafechaAn );
    #DISPONIBILIDAD
    $disponibilidadAnterior = disponibilidades($row[4], 14, $fechaIAn, $fechaFinAn);
    #REGISTROS
    $registrosAnterior = disponibilidades($row[4], 15, $fechaIAn, $fechaFinAn);
    #TOTAL OBLIGACIONES
    $totalObligacionesAnterior = disponibilidades($row[4], 16, $fechaIAn, $fechaFinAn);
    #TOTAL PAGOS
    $totalPagosAnterior= disponibilidades($row[4], 17, $fechaIAn, $fechaFinAn);

##########################################################################################################################################################################    
#   ACTUALES
##########################################################################################################################################################################    
    $fechaIAc = $anno.'-'.$mesI.'-01';
    $diaFAc = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFinAc = $anno.'-'.$mesF.'-'.$diaFAc;
    #DISPONIBILIDAD
    $disponibilidadActual= disponibilidades($row[4], 14, $fechaIAc, $fechaFinAc);
    #REGISTROS
    $registrosActual = disponibilidades($row[4], 15, $fechaIAc, $fechaFinAc);
    #TOTAL OBLIGACIONES
    $totalObligacionesActual = disponibilidades($row[4], 16, $fechaIAc, $fechaFinAc);
    #TOTAL PAGOS
    $totalPagosActual= disponibilidades($row[4], 17, $fechaIAc, $fechaFinAc);

######################################################################################################################################################
#   ACUMULADO
######################################################################################################################################################
    $fechaIAcum = $anno.'-01-01';  
    $diaFAc = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFAcum = $anno.'-'.$mesF.'-'.$diaFAc;
    #DISPONIBILIDAD
    $disponibilidadAcum= disponibilidades($row[4], 14, $fechaIAcum, $fechaFAcum);
    #REGISTROS
    $registrosAcum = disponibilidades($row[4], 15, $fechaIAcum, $fechaFAcum);
    #TOTAL OBLIGACIONES
    $totalObligacionesAcum = disponibilidades($row[4], 16, $fechaIAcum, $fechaFAcum);
    #TOTAL PAGOS
    $totalPagosAcum= disponibilidades($row[4], 17, $fechaIAcum, $fechaFAcum);
######################################################################################################################################################

    
    #ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "tras_credito = '$trasCredito', "
            . "tras_cont = '$trasCont', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "disponibilidades = '$disponibilidadAnterior', "
            . "saldo_disponible = '$disponibilidadActual', "
            . "disponibilidad_abierta = '$disponibilidadAcum', "
            . "registros = '$registrosAnterior', "
            . "registros_abiertos = '$registrosActual', "
            . "registros_otros = '$registrosAcum', "
            . "total_obligaciones = '$totalObligacionesAnterior', "
            . "reservas = '$totalObligacionesActual', "
            . "cuentas_x_pagar = '$totalObligacionesAcum', "
            . "total_pagos = '$totalPagosAnterior', "
            . "recaudos = '$totalPagosActual', "
            . "saldos_x_recaudar = '$totalPagosAcum' "
            . "WHERE rubro_fuente = '$row[4]'";
    $update = $mysqli->query($update);
          
}   

#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
 $acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, "
        . "adicion, reduccion, "
        . "tras_credito, tras_cont, "
        . "presupuesto_dfvo, "
        . "disponibilidades, "
        . "saldo_disponible, "
        . "disponibilidad_abierta, "
        . "registros, "
        . "registros_abiertos, "
        . "registros_otros, "
        . "total_obligaciones, "
        . "reservas, "
        . "cuentas_x_pagar,"
        . "total_pagos,"
        . "recaudos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
$acum = $mysqli->query($acum);

while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, "
        . "adicion, reduccion, "
        . "tras_credito, tras_cont, "
        . "presupuesto_dfvo, "
        . "disponibilidades, "
        . "saldo_disponible, "
        . "disponibilidad_abierta, "
        . "registros, "
        . "registros_abiertos, "
        . "registros_otros, "
        . "total_obligaciones, "
        . "reservas, "
        . "cuentas_x_pagar,"
        . "total_pagos,"
        . "recaudos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
        . "ORDER BY cod_rubro DESC ";
    $acumd = $mysqli->query($acumd);
    while ($rowa= mysqli_fetch_row($acumd)){
        if(!empty($rowa[2])){
            $va11= "SELECT id_unico, "
            . "cod_rubro,"
            . "cod_predecesor, "
            . "ptto_inicial, "
            . "adicion, reduccion, "
            . "tras_credito, tras_cont, "
            . "presupuesto_dfvo, "
            . "disponibilidades, "
            . "saldo_disponible, "
            . "disponibilidad_abierta, "
            . "registros, "
            . "registros_abiertos, "
            . "registros_otros, "
            . "total_obligaciones, "
            . "reservas, "
            . "cuentas_x_pagar,"
            . "total_pagos,"
            . "recaudos, "
            . "saldos_x_recaudar "
            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $pptoInicialM = $rowa[3]+$va[3];
        $adicionM = $rowa[4]+$va[4];
        $reduccionM = $rowa[5]+$va[5];
        $trasCreditoM = $rowa[6]+$va[6];
        $trasContM = $rowa[7]+$va[7];
        $presupuestoDefinitivoM = $rowa[8]+$va[8];
        $disponibilidadAnteriorM = $rowa[9]+$va[9];
        $disponibilidadActualM = $rowa[10]+$va[10];
        $disponibilidadAcumM = $rowa[11]+$va[11];
        $registrosAnteriorM = $rowa[12]+$va[12];
        $registrosActualM = $rowa[13]+$va[13];
        $registrosAcumM = $rowa[14]+$va[14];
        $totalObligacionesAnteriorM = $rowa[15]+$va[15];
        $totalObligacionesActualM = $rowa[16]+$va[16];
        $totalObligacionesAcumM = $rowa[17]+$va[17];
        $totalPagosAnteriorM = $rowa[18]+$va[18];
        $totalPagosActualM = $rowa[19]+$va[19];
        $totalPagosAcumM = $rowa[20]+$va[20];
        
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "tras_credito = '$trasCreditoM', "
                . "tras_cont = '$trasContM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "disponibilidades = '$disponibilidadAnteriorM', "
                . "saldo_disponible = '$disponibilidadActualM', "
                . "disponibilidad_abierta = '$disponibilidadAcumM', "
                . "registros = '$registrosAnteriorM', "
                . "registros_abiertos = '$registrosActualM', "
                . "registros_otros = '$registrosAcumM', "
                . "total_obligaciones = '$totalObligacionesAnteriorM', "
                . "reservas = '$totalObligacionesActualM', "
                . "cuentas_x_pagar = '$totalObligacionesAcumM', "
                . "total_pagos = '$totalPagosAnteriorM', "
                . "recaudos = '$totalPagosActualM', "
                . "saldos_x_recaudar = '$totalPagosAcumM' "
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
    
    global $mesInicial;
    global $mesFinal;
    global $annoInforme;
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
    $this->Cell(340,5,utf8_decode('EJECUCION DEL PRESUPUESTO DE GASTOS E INVERSIONES POR PERIODO'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(340,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(5);
    $this->SetFont('Arial','B',8);
    $this->Cell(340, 5,utf8_decode('DE '.$mesInicial.' A '.$mesFinal.' - '.$annoInforme),0,0,'C'); 
    $this->Ln(3);
    
    $this->SetFont('Arial','B',6);
    $this->Ln(5);
    $this->SetX(5);
    
    $this->Cell(15,8, utf8_decode(''),1,0,'C');#
    $this->Cell(55,8,utf8_decode(''),1,0,'C');#
    $this->Cell(8,8,utf8_decode(''),1,0,'C');################
    $this->Cell(15,8,utf8_decode(''),1,0,'C');#
    $this->Cell(60,8,utf8_decode(''),1,0,'C');# 
    $this->Cell(15,8,utf8_decode(''),1,0,'C');#
    $this->Cell(45,8,utf8_decode(''),1,0,'C');###################
    $this->Cell(45,8,utf8_decode(''),1,0,'C');#
    $this->Cell(45,8,utf8_decode(''),1,0,'C');#
    $this->Cell(45,8,utf8_decode(''),1,0,'C');#
    $this->SetX(5);
    
    $this->CellFitScale(15,8, utf8_decode('RUBRO'),1,0,'C');#
    $this->CellFitScale(55,8,utf8_decode('DETALLE'),1,0,'C');#
    $this->CellFitScale(8,8,utf8_decode('FUENTE'),1,0,'C');################
    $this->CellFitScale(15,4,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->CellFitScale(60,4,utf8_decode('MODIFICACIONES PRESUPUESTALES'),0,0,'C');
    $this->CellFitScale(15,4,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->CellFitScale(45,4,utf8_decode('DISPONIBILIDADES'),0,0,'C');
    $this->CellFitScale(45,4,utf8_decode('REGISTROS'),0,0,'C');
    $this->CellFitScale(45,4,utf8_decode('OBLIGACIONES'),0,0,'C');
    $this->CellFitScale(45,4,utf8_decode('PAGOS'),0,0,'C');
    $this->Ln(4);
    $this->SetX(5);
    
    $this->CellFitScale(15,4, utf8_decode(' '),0,0,'C');#
    $this->CellFitScale(55,4,utf8_decode(' '),0,0,'C');#
    $this->CellFitScale(8,4,utf8_decode(' '),0,0,'C');###############################
    $this->CellFitScale(15,4,utf8_decode('INICIAL'),0,0,'C');#
    $this->CellFitScale(15,4,utf8_decode('ADICIÓN'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('REDUCCIÓN'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('TRAS.CREDITO'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('TRAS.CONT'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('DEFINITIVO'),0,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ANTERIOR'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ACTUAL'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ACUMULADO'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ANTERIOR'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ACTUAL'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ACUMULADO'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ANTERIOR'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ACTUAL'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ACUMULADO'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ANTERIOR'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ACTUAL'),1,0,'C');
    $this->CellFitScale(15,4,utf8_decode('ACUMULADO'),1,0,'C');
    
    
    $this->Ln(4);
    
    
    }      
    
function Footer()
    {
    global $usuario;
    global $fechaActual;
    $this->SetY(-15);
    $this->SetFont('Arial','B',8);
    $this->Cell(15);
    $this->Cell(326,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}
$pdf = new PDF('L','mm','Legal');        

        
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

$pdf->setY(47);
$cnt = 0;
#Consulta Cuentas
$sql2 = "SELECT DISTINCT 
                        cod_rubro               as codrub, 
                        nombre_rubro            as nomrub,
                        cod_fuente              as codfte,
                        ptto_inicial            as ppti,
                        adicion                 as adi,
                        reduccion               as red,
                        tras_credito            as tcred,
                        tras_cont               as trcont,
                        presupuesto_dfvo        as ppdf,
                        disponibilidades        as disanterior,
                        saldo_disponible        as disactual,
                        disponibilidad_abierta  as disacum, 
                        registros               as reganterior,
                        registros_abiertos      as regactual,
                        registros_otros         as regacum, 
                        total_obligaciones      as oblianterior,
                        reservas                as obliactual,
                        cuentas_x_pagar         as obliacum, 
                        total_pagos             as paganterior,
                        recaudos                as pagactual, 
                        saldos_x_recaudar       as pagosacum 
                        
from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
$conejc  = $mysqli->query($sql2);

    $pdf->SetY(37);
    $pdf->Ln(4);
 
   

$pdf->SetFont('Arial','',6); 
while ($filactas = mysqli_fetch_array($conejc)) 
{
$a = $pdf->GetY();
if($a>175)
{
 #$pdf->Ln(10);        
     $pdf->AddPage();            
}
    $pdf->setX(5);    
    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['tcred'];
    $p5  = (float) $filactas['trcont'];
    $p6  = (float) $filactas['ppdf'];
    $disan  = (float) $filactas['disanterior'];
    $disac  = (float) $filactas['disactual'];
    $disam  = (float) $filactas['disacum'];
    $regan  = (float) $filactas['reganterior'];
    $regac  = (float) $filactas['regactual'];
    $regam  = (float) $filactas['regacum'];
    $oblan  = (float) $filactas['oblianterior'];
    $oblac  = (float) $filactas['obliactual'];
    $oblam  = (float) $filactas['obliacum'];
    $pagan  = (float) $filactas['paganterior'];
    $pagac  = (float) $filactas['pagactual'];
    $pagam  = (float) $filactas['pagosacum'];
       # $codd = $codd + 1;
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && 
            $disan==0 && $disac==0 && $disam==0 && $regan==0 && $regac==0 && $regam==0 && 
            $oblan==0 && $oblac==0 && $oblam==0 && $pagan==0 && $pagac==0 && $pagam==0)
        { } else {
        $a = $pdf->GetY();
        $pdf->Cell(15,4,'',0,0,'R');        
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(55,3.5,utf8_decode($filactas['nomrub']),1,'L');        
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 55;
        $pdf->Ln(-$h);
        $pdf->SetX(5);
        if(!empty($filactas['codrub'])) {
            $pdf->CellFitScale(15,$h,$filactas['codrub'],1,0,'R');
        } else {
            $pdf->Cell(15,$h,'',1,0,'R');
        }
        $pdf->SetX($px);
        if(empty($filactas['codfte'])) {
            $pdf->Cell(8,$h,utf8_decode($filactas['codfte']),1,0,'C');
        } else {
            $pdf->CellFitScale(8,$h,utf8_decode($filactas['codfte']),1,0,'C');
        }
        if(empty($p1)) {
            $pdf->Cell(15,$h,number_format($p1,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($p1,2,'.',','),1,0,'R');
        }
        if(empty($p2)) {
            $pdf->Cell(15,$h,number_format($p2,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($p2,2,'.',','),1,0,'R');
        }
        
         if(empty($p3)) {
            $pdf->Cell(15,$h,number_format($p3,2,'.',','),1,0,'R');
        } else {
            $pdf->Cell(15,$h,number_format($p3,2,'.',','),1,0,'R');
        }
        
         if(empty($p4)) {
            $pdf->Cell(15,$h,number_format($p4,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($p4,2,'.',','),1,0,'R');
        }
        
         if(empty($p5)) {
            $pdf->Cell(15,$h,number_format($p5,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($p5,2,'.',','),1,0,'R');
        }
        
         if(empty($p6)) {
            $pdf->Cell(15,$h,number_format($p6,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($p6,2,'.',','),1,0,'R');
        }
        
         if(empty($disan)) {
            $pdf->Cell(15,$h,number_format($disan,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($disan,2,'.',','),1,0,'R');
        }
        
        
         if(empty($disac)) {
            $pdf->Cell(15,$h,number_format($disac,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($disac,2,'.',','),1,0,'R');
        }
        
         if(empty($disam)) {
            $pdf->Cell(15,$h,number_format($disam,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($disam,2,'.',','),1,0,'R');
        }
        
         if(empty($regan)) {
            $pdf->Cell(15,$h,number_format($regan,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($regan,2,'.',','),1,0,'R');
        }
        
        
         if(empty($regac)) {
            $pdf->Cell(15,$h,number_format($regac,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($regac,2,'.',','),1,0,'R');
        }
        
        
         if(empty($regam)) {
            $pdf->Cell(15,$h,number_format($regam,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($regam,2,'.',','),1,0,'R');
        }
        
         if(empty($oblan)) {
            $pdf->Cell(15,$h,number_format($oblan,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($oblan,2,'.',','),1,0,'R');
        }
        
         if(empty($oblac)) {
            $pdf->Cell(15,$h,number_format($oblac,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($oblac,2,'.',','),1,0,'R');
        }
        
         if(empty($oblam)) {
            $pdf->Cell(15,$h,number_format($oblam,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($oblam,2,'.',','),1,0,'R');
        }
        
         if(empty($pagan)) {
            $pdf->Cell(15,$h,number_format($pagan,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($pagan,2,'.',','),1,0,'R');
        }
        
         if(empty($pagac)) {
            $pdf->Cell(15,$h,number_format($pagac,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($pagac,2,'.',','),1,0,'R');
        }
        
         if(empty($pagam)) {
            $pdf->Cell(15,$h,number_format($pagam,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(15,$h,number_format($pagam,2,'.',','),1,0,'R');
        }
           
         $pdf->Ln($h);
        #Actualización 10/02/2017
        }
}    
$pdf->setX(5);
        $pdf->Cell(337,0.5,utf8_decode(''),1,0,'C');
        $pdf->Cell(31,4,utf8_decode($filactas['cnnom']),0,0,'C');
 
 
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
 ################################################################################################################################
 ##################################################################################
 while (ob_get_length()) {
  ob_end_clean();
}
 ######### FIN FIRMAS #########
 $pdf->Output(0,utf8_decode('Informe_Ejecuciones_Pptales_Gastos_Invers('.date('d-m-Y').').pdf'),0);
?>