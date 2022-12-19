<?php
######################################################################################################
# ***************************************** Modificaciones ***************************************** #
######################################################################################################
#14/02/2018 | Erica G. | Archivo Creado
######################################################################################################

require'../../Conexion/conexion.php';
require'consultas.php';
ini_set('max_execution_time', 0);
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');
$compania = $_SESSION['compania'];

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
$codigoF = $mysqli->real_escape_string(''.$_POST['sltcnf'].'');

#* VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos ';
$mysqli->query($vaciarTabla);

#* CONSULTA TODOS LOS RUBROS
$ctas = "SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.nombre,
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
             AND (rpp.tipoclase = 15) 
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
#CONSULTA RUBROS SEGUN VARIABLES QUE RECIBE
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
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  AND cp.parametrizacionanno = $parmanno "
        . "ORDER BY rpp.codi_presupuesto ASC";
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
          WHERE f.id_unico ='$fuente' AND cp.parametrizacionanno = $parmanno  "
         . "AND rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  ORDER BY rpp.codi_presupuesto ASC";
   
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
    #SALDO DISPONIBLE
    $saldoDisponible= $presupuestoDefinitivo;
    #TOTAL OBLIGACIONES
    $totalObligaciones = disponibilidades($row[4], 16, $fechaInicial, $fecha);
    #TOTAL PAGOS
    $totalPagos= disponibilidades($row[4], 17, $fechaInicial, $fecha);
    
   
    #ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "tras_credito = '$trasCredito', "
            . "tras_cont = '$trasCont', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "total_obligaciones = '$totalObligaciones', "
            . "total_pagos = '$totalPagos' "
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
        . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
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
        . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
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
            . "total_pagos,reservas,cuentas_x_pagar, reduccion, disponibilidad_abierta "
            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
            $va1 = $mysqli->query($va11);
            $va= mysqli_fetch_row($va1);
            $pptoInicialM = $rowa[3]+$va[3];
            $adicionM = $rowa[4]+$va[4];
            $trasCreditoM = $rowa[5]+$va[5];
            $trasContM = $rowa[6]+$va[6];
            $presupuestoDefinitivoM = $rowa[7]+$va[7];
            $saldoDisponibleM = $rowa[9]+$va[9];
            $totalObligacionesM = $rowa[12]+$va[12];
            $totalPagosM = $rowa[13]+$va[13];
            $reduccionM = $rowa[16]+$va[16];
            #ACTUALIZAR TABLA CON DATOS HALLADOS
            $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                    . "ptto_inicial ='$pptoInicialM', "
                    . "adicion = '$adicionM', "
                    . "reduccion = '$reduccionM', "
                    . "tras_credito = '$trasCreditoM', "
                    . "tras_cont = '$trasContM', "
                    . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                    . "saldo_disponible = '$saldoDisponibleM', "
                    . "total_obligaciones = '$totalObligacionesM', "
                    . "total_pagos = '$totalPagosM' " 
                    . "WHERE cod_rubro = '$rowa[2]'";
            $updateA = $mysqli->query($updateA);
        }
    }
}
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
    

    $consulta = "SELECT         t.razonsocial as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum, 
                                t.digitoverficacion as dig,
                                t.ruta_logo as ruta , 
                                dir.direccion as dir,
                                tel.valor as tel 
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
            LEFT JOIN   gf_direccion dir ON dir.tercero = t.id_unico
            LEFT JOIN 	gf_telefono  tel ON tel.tercero = t.id_unico
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
        $digtv = utf8_decode($fila['dig']);  
        $direccinTer = $fila['dir'];
        $telefonoTer =$fila['tel'];
    }
    
    
if($_GET['tipo']==1){   
require'../../fpdf/fpdf.php';
ob_start();    
class PDF extends FPDF
{
function Header()
{ 
    global $nomcomp;
    global $tipodoc;
    global $numdoc;
    global $month1;
    global $month2;
    global $anno;
    global $ruta ;
    $this->setX(0);
    $this->SetFont('Arial','B',10);
    $this->SetY(10);
    if($ruta != '')
    {
        $this->Image('../../'.$ruta,60,6,20);
    }
    $this->Cell(340,5,utf8_decode($nomcomp),0,0,'C');
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
    $this->Cell(340,5,utf8_decode('EJECUCION PRESUPUESTAL DE CUENTAS POR PAGAR VIGENCIA ANTERIOR'),0,0,'C');
    $this->SetFont('Arial','B',8);
    
    $this->SetX(0);
    $this->Cell(340,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',6);
    $this->Cell(340,5,utf8_decode('MES DE '.utf8_decode (ucwords(strtoupper($month1))).' '.$anno),0,0,'C');
    
    $this->Ln(5);
    
    $this->SetX(20);
    $this->SetFont('Arial','B',7);
    $this->Cell(40,9, utf8_decode(''),1,0,'C');#
    $this->Cell(90,9,utf8_decode(''),1,0,'C');#
    $this->Cell(55,9,utf8_decode(''),1,0,'C');################
    $this->Cell(20,9,utf8_decode(''),1,0,'C');#
    $this->Cell(80,9,utf8_decode(''),1,0,'C');# 
    $this->Cell(20,9,utf8_decode(''),1,0,'C');#
    $this->Cell(20,9,utf8_decode(''),1,0,'C');
    
        $this->SetX(20);
    
    $this->CellFitScale(40,9, utf8_decode('RUBRO'),1,0,'C');#
    $this->CellFitScale(90,9,utf8_decode('DETALLE'),1,0,'C');#
    $this->CellFitScale(55,9,utf8_decode('FUENTE'),1,0,'C');################
    $this->CellFitScale(20,8,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->CellFitScale(80,4,utf8_decode('MODIFICACIONES PRESUPUESTALES'),0,0,'C');
    $this->CellFitScale(20,8,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->CellFitScale(20,8,utf8_decode('TOTAL'),0,0,'C');#
    
   
    $this->Ln(4);
    
    $this->SetX(20);
    
    $this->CellFitScale(40,5,utf8_decode(' '),0,0,'C');#
    $this->CellFitScale(90,5,utf8_decode(' '),0,0,'C');#
    $this->CellFitScale(55,5,utf8_decode(' '),0,0,'C');###############################
    $this->CellFitScale(20,5,utf8_decode('INICIAL'),0,0,'C');#
    $this->CellFitScale(20,5,utf8_decode('ADICIÓN'),1,0,'C');
    $this->CellFitScale(20,5,utf8_decode('REDUCCIÓN'),1,0,'C');
    $this->CellFitScale(20,5,utf8_decode('TRAS.CREDITO'),1,0,'C');
    $this->CellFitScale(20,5,utf8_decode('TRAS.CONT'),1,0,'C');
    $this->CellFitScale(20,5,utf8_decode('DEFINITIVO'),0,0,'C');
    $this->CellFitScale(20,5,utf8_decode('PAGOS'),0,0,'C');
    
    
    $this->Ln(5);
    $this->Cell(326,5,'',0);
    }      
    
function Footer()
    {
    global $usuario;
    global $fechaActual;
    $this->SetY(-15);
    $this->SetFont('Arial','B',8);
    $this->Cell(15);
    $this->Cell(25,10,utf8_decode('Fecha: '.$fechaActual),0,0,'L');
    $this->Cell(70);
    $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
    $this->Cell(60);
    $this->Cell(30,10,utf8_decode('Usuario:'.strtoupper($usuario)),0); 
    $this->Cell(70);
    $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0);
    }
}
$pdf = new PDF('L','mm','Legal');        


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

$pdf->setY(37);
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
                        disponibilidades        as disp,
                        saldo_disponible        as sald,
                        registros               as reg,
                        registros_abiertos      as rega,
                        total_obligaciones      as tobl,
                        total_pagos             as tpag,
                        reservas                as reserv,
                        cuentas_x_pagar         as cpag,
                        disponibilidad_abierta  as disAb 
from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
$conejc  = $mysqli->query($sql2);

    $pdf->SetY(32);
    $pdf->Ln(5);
   
   

$pdf->SetFont('Arial','',7); 
while ($filactas = mysqli_fetch_array($conejc)) 
{

$pdf->setX(20);    
    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['tcred'];
    $p5  = (float) $filactas['trcont'];
    $p6  = (float) $filactas['ppdf'];
    $p11 = (float) $filactas['tobl'];
    $p12 = (float) $filactas['tpag'];
       # $codd = $codd + 1;
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p11==0 && $p12==0)
        { } else {
        $a = $pdf->GetY();
        if($a>160)
        {
            $pdf->AddPage();     
            $pdf->setX(20); 
        }
        $pdf->Cell(40,5,'',0,0,'R');        
        #Actualización 10/02/2017   
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(90,5,utf8_decode($filactas['nomrub']),0,'L');        
        $y2 = $pdf->GetY();
        $h1 = $y2-$y;
        $pdf->Ln(-$h1);
        $pdf->SetX($x+90);
        $pdf->MultiCell(55,5,utf8_decode($filactas['codfte']),0,'L');        
        $y3 = $pdf->GetY();
        $h2 = $y3-$y;
        $h = max($h1, $h2);
        $px = $x + 145;
        $pdf->SetY($y);
        $pdf->SetX(20);
        if(!empty($filactas['codrub'])) {
            $pdf->CellFitScale(40,$h,$filactas['codrub'],1,0,'L');
        } else {
            $pdf->Cell(40,$h,'',1,0,'L');
        }
        $pdf->Cell(90,$h,'',1,0,'R'); 
        $pdf->Cell(55,$h,'',1,0,'R'); 
        if(empty($p1)) {
            $pdf->Cell(20,$h,number_format($p1,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(20,$h,number_format($p1,2,'.',','),1,0,'R');
        }
        if(empty($p2)) {
            $pdf->Cell(20,$h,number_format($p2,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(20,$h,number_format($p2,2,'.',','),1,0,'R');
        }
        
         if(empty($p3)) {
            $pdf->Cell(20,$h,number_format($p3,2,'.',','),1,0,'R');
        } else {
            $pdf->Cell(20,$h,number_format($p3,2,'.',','),1,0,'R');
        }
        
         if(empty($p4)) {
            $pdf->Cell(20,$h,number_format($p4,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(20,$h,number_format($p4,2,'.',','),1,0,'R');
        }
        
         if(empty($p5)) {
            $pdf->Cell(20,$h,number_format($p5,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(20,$h,number_format($p5,2,'.',','),1,0,'R');
        }
        
         if(empty($p6)) {
            $pdf->Cell(20,$h,number_format($p6,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(20,$h,number_format($p6,2,'.',','),1,0,'R');
        }
        
        
         if(empty($p12)) {
            $pdf->Cell(20,$h,number_format($p12,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(20,$h,number_format($p12,2,'.',','),1,0,'R');
        }
        
        
         $pdf->Ln($h);
        #Actualización 10/02/2017
        }
}    
$pdf->setX(20);
        $pdf->Cell(325,0.5,utf8_decode(''),1,0,'C');
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
        AND if(rd.fecha_inicio IS NULL, rd.fecha_inicio IS NULL, rd.fecha_inicio <= '$fecha') 
        AND if(rd.fecha_fin IS NULL,rd.fecha_fin IS NULL, rd.fecha_fin >= '$fecha') 
        AND td.compania = $compania  ORDER BY rd.orden ASC";
 $res= $mysqli->query($res);
 $i=0;
 $x=130;
 #ESTRUCTURA
$y =$pdf->GetY();
$x = $pdf->GetX();
 if(mysqli_num_rows($res)>0){  $h=4;
    while($F = mysqli_fetch_row($res)){
        if($F[1] ==  1){
            $pdf->Cell(55,0.1,'',1);  
            $pdf->Ln(2); 
            $pdf->cellfitscale(45,5,utf8_decode($F[2]),0,0,'L');
            $pdf->Ln(3);
            $pdf->cellfitscale(45,5,utf8_decode($F[0]),0,0,'L'); 
        } else{
            $pdf->SetXY($x+20, $y); 
            $pdf->Cell(55,0.1,'',1); 
            $pdf->Ln(2); 
            $y1 =$pdf->GetY();
            $pdf->SetXY($x+20, $y1); 
            $pdf->cellfitscale(45,5,utf8_decode($F[2]),0,0,'L');
            $pdf->Ln(3); 
            $y2 =$pdf->GetY();
            $pdf->SetXY($x+20, $y2); 
            $pdf->cellfitscale(45,5,utf8_decode($F[0]),0,0,'L');  
        }
        $x = $pdf->GetX();  
     }
     
 } 
 ##################################################################################
 while (ob_get_length()) {
  ob_end_clean();
}
 ######### FIN FIRMAS #########
 $pdf->Output(0,utf8_decode('Informe_Ejecucion_Cuentas_Pagar_VA('.date('d-m-Y').').pdf'),0);
} elseif($_GET['tipo']==2){
 header("Content-type: application/vnd.ms-excel");
 header("Content-Disposition: attachment; filename=Informe_Ejecucion_Cuentas_Pagar_VA.xls");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ejecución Cuentas Por Pagar Vigencia Anterior</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="10" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $nomcomp ?>
            <br/><?php if(empty($digtv)) {
                        $nombreIdent =$tipodoc.': '.$numdoc;
                    } else {
                        $nombreIdent =$tipodoc.': '.$numdoc.' - '.$digtv; 
                    }
              echo $nombreIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>EJECUCIÓN PRESUPUESTAL DE CUENTAS POR PAGAR VIGENCIA ANTERIOR
           <br/> MES DE <?php echo strtoupper($month1).' '.$anno?>
           <br/>&nbsp;

        </th>
  </tr>
    <tr>
        <td align="center" rowspan="2"><strong>RUBRO</strong></td>
        <td align="center" rowspan="2"><strong>DETALLE</strong></td>
        <td align="center" rowspan="2"><strong>FUENTE</strong></td>
        <td align="center" rowspan="2"><strong>PRESUPUESTO <br/>INICIAL</strong></td>
        <td align="center" colspan="4"><strong>MODIFICACIONES <br/>PRESUPUESTALES</strong></td>
        <td align="center" rowspan="2"><strong>PRESUPUESTO <br/>DEFINITIVO</strong></td>
        <td align="center" rowspan="2"><strong>TOTAL <br/>PAGOS</strong></td>
    </tr>
    <tr>
        <td align="center"><strong>ADICIÓN</strong></td>
        <td align="center"><strong>REDUCCIÓN</strong></td>
        <td align="center"><strong>TRAS. CREDITO</strong></td>
        <td align="center"><strong>TRAS. CONT</strong></td>
    </tr>
    <?php
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
                            disponibilidades        as disp,
                            saldo_disponible        as sald,
                            registros               as reg,
                            registros_abiertos      as rega,
                            total_obligaciones      as tobl,
                            total_pagos             as tpag,
                            reservas                as reserv,
                            cuentas_x_pagar         as cpag,
                            disponibilidad_abierta  as disAb 
    from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
    $conejc  = $mysqli->query($sql2);
    while ($filactas = mysqli_fetch_array($conejc)) 
    {    
        $p1  = (float) $filactas['ppti'];
        $p2  = (float) $filactas['adi'];
        $p3  = (float) $filactas['red'];
        $p4  = (float) $filactas['tcred'];
        $p5  = (float) $filactas['trcont'];
        $p6  = (float) $filactas['ppdf'];
        $p11 = (float) $filactas['tobl'];
        $p12 = (float) $filactas['tpag'];
        # $codd = $codd + 1;
        if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p11==0 && $p12==0)
        { } else {
        echo '<tr>';
        echo '<td>'.$filactas['codrub'].'</td>';
        echo '<td>'.$filactas['nomrub'].'</td>';
        echo '<td>'.$filactas['codfte'].'</td>';
        echo '<td>'.number_format($p1,2,'.',',').'</td>';
        echo '<td>'.number_format($p2,2,'.',',').'</td>';
        echo '<td>'.number_format($p3,2,'.',',').'</td>';
        echo '<td>'.number_format($p4,2,'.',',').'</td>';
        echo '<td>'.number_format($p5,2,'.',',').'</td>';
        echo '<td>'.number_format($p6,2,'.',',').'</td>';
        echo '<td>'.number_format($p12,2,'.',',').'</td>';
        echo '</tr>';
        }
}    
}
?>