<?php
#############MODIFICACIONES###################
#08/03/2017 |ERICA G. |CONSULTAS
##############################################
require'../../fpdf/fpdf.php';
require'../../Conexion/conexion.php';
require'consultas.php';
ini_set('max_execution_time', 0);
ob_start();

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
$codigoI =$mysqli->real_escape_string(''.$_POST['sltcodi'].'');
$codigoF=$mysqli->real_escape_string(''.$_POST['sltcnf'].'');


#VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos ';
$mysqli->query($vaciarTabla);

#CONSULTA TODAS LA CUENTAS
$ctas = "SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.id_unico,
            rpp2.codi_presupuesto, 
            rf.id_unico, rpp.equivalente, f.equivalente  
          FROM
            gf_rubro_pptal rpp
          LEFT JOIN
            gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
          LEFT JOIN
            gf_fuente f ON rf.fuente = f.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
            WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
            AND rpp.parametrizacionanno = $parmanno";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente, equivalente_rubro, equivalente_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]','$row1[5]','$row1[6]' )";
    $mysqli->query($insert);
    
}
    
#CONSULTA CUENTAS DEL DETALLE SEGUN VARIABLES QUE RECIBE
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
            gf_tercero t ON dcp.tercero = t.id_unico 
          LEFT JOIN 
            gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF'  
            AND cp.parametrizacionanno =$parmanno 
            AND rpp.parametrizacionanno = $parmanno";
$select1 = $mysqli->query($select);


while($row = mysqli_fetch_row($select1)){
    
    #PRESUPUESTO INICIAL
    $pptoInicial= presupuestos($row[4], 1, $fechaInicial, $fecha);
    
    #ADICION
    $adicion = presupuestos($row[4], 2, $fechaInicial, $fecha);
    #REDUCCION
    $reduccion = presupuestos($row[4], 3, $fechaInicial, $fecha);
    
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion;
    #DISPONIBILIDAD
    $disponibilidad = disponibilidades($row[4], 14, $fechaInicial, $fecha);
    #SALDO DISPONIBLE
    $saldoDisponible= $presupuestoDefinitivo-$disponibilidad;
    #REGISTROS
    $registros = disponibilidades($row[4], 15, $fechaInicial, $fecha);
    #REGISTROS ABIERTOS
    $registrosAbiertos = $disponibilidad-$registros;
    #TOTAL OBLIGACIONES
    $totalObligaciones = disponibilidades($row[4], 16, $fechaInicial, $fecha);
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
    global $codDane;

    global $month1;
    global $month2;
    global $anno;
    
    
    $this->SetFont('Arial','B',10);
        // Título
    $this->SetY(10);
    //$this->image('../LOGOABC.png', 20,10,20,15,'PNG');    
    $this->Cell(330,5,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->setX(20);
    $this->SetFont('Arial','B',8);
    $this->Cell(320,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    $this->Cell(330, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
    $this->SetFont('Arial','B',8);
    $this->SetX(20);
    $this->Cell(320,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',8);
    $this->Cell(330,5,utf8_decode('REPORTE GASTOS PRESUPUESTALES'),0,0,'C');
    $this->SetFont('Arial','B',8);
    
    $this->SetX(20);
    $this->Cell(320,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',8);
    $this->Cell(332,5,utf8_decode(utf8_decode (ucwords(strtoupper($month1)))),0,0,'C');
    
    $this->Ln(5);
    
    $this->SetX(10);
    
    $this->Cell(51,9,utf8_decode(''),1,0,'C');#
    $this->Cell(31,9,utf8_decode(''),1,0,'C');#
    $this->Cell(31,9,utf8_decode(''),1,0,'C');#
    $this->Cell(31,9,utf8_decode(''),1,0,'C');#
    $this->Cell(31,9,utf8_decode(''),1,0,'C');#
    $this->Cell(31,9,utf8_decode(''),1,0,'C');
    $this->Cell(31,9,utf8_decode(''),1,0,'C');#
    $this->Cell(31,9,utf8_decode(''),1,0,'C');
    $this->Cell(31,9,utf8_decode(''),1,0,'C');
    $this->Cell(31,9,utf8_decode(''),1,0,'C');
    
    
        $this->SetX(10);
    
    $this->Cell(51,7,utf8_decode('CODIGO'),0,0,'C');#
    $this->Cell(31,9,utf8_decode('AÑO'),1,0,'C');#
    $this->Cell(31,7,utf8_decode('TRIMESTRE'),0,0,'C');#
    $this->Cell(31,7,utf8_decode('FUENTE DE '),0,0,'C');#
    $this->Cell(31,7,utf8_decode('ITEM '),0,0,'C');#
    $this->Cell(31,7,utf8_decode('PRESUPUESTO '),0,0,'C');#
    $this->Cell(31,7,utf8_decode('PRESUPUESTO '),0,0,'C');#
    $this->Cell(31,7,utf8_decode('COMPROMISOS '),0,0,'C');#
    $this->Cell(31,7,utf8_decode('OBLIGACIONES '),0,0,'C');#
    $this->Cell(31,7,utf8_decode('PAGOS '),0,0,'C');#
    
   
    $this->Ln(4);
    
    $this->SetX(10);
    
    $this->Cell(51,5,utf8_decode('ESTABLECIMIENTO'),0,0,'C');#
    $this->Cell(31,5,utf8_decode(''),0,0,'C');#
    $this->Cell(31,5,utf8_decode(''),0,0,'C');#
    $this->Cell(31,5,utf8_decode('INGRESOS'),0,0,'C');#
    $this->Cell(31,5,utf8_decode('DETALLE'),0,0,'C');#
    $this->Cell(31,5,utf8_decode('INICIAL'),0,0,'C');
    $this->Cell(31,5,utf8_decode('DEFINITIVO'),0,0,'C');
    $this->Cell(31,5,utf8_decode(''),0,0,'C');
    $this->Cell(31,5,utf8_decode(''),0,0,'C');
    $this->Cell(31,5,utf8_decode(''),0,0,'C');
    

    
    $this->Ln(5);
    $this->Cell(326,5,'',0);
    }      
    
    // Pie de página
        function Footer()
            {
            // Posición: a 1,5 cm del final
            global $hoy;
            global $usuario;
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','B',8);
                $this->SetX(10);
                $this->Cell(90,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
    }



// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');        

////Asingación de valor a Mes 1    
    switch($mes)
    {
        case 3:
        $month1 = "Primer Trimestre";
            break;
        case 6:
        $month1 = "Segundo Trimestre";
            break;
        case 9:
        $month1 = "Tercer Trimestre";
            break;
        case 12:
        $month1 = "Cuarto Trimestre";
            break;
        }
        
        #Igualación de Variable local a POST
        $annio = $anno;
    
    #Consulta Compañía para Encabezado
    $compania = $_SESSION['compania'];
    $usuario = $_SESSION['usuario'];

    $consulta = "SELECT         t.razonsocial as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum, 
                                t.codigo_dane as codigo 
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

#Inicialización parámetros Header
$nomcomp = "";
$tipodoc = "";
$numdoc = "";
$codDane = "";  
#Consulta para obtener parámetros Header

while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = utf8_decode($fila['traz']);       
        $tipodoc = utf8_decode($fila['tnom']);       
        $numdoc = utf8_decode($fila['tnum']);   
        $codDane = utf8_decode($fila['codigo']);   
    }

 #Declaración Variable Número de Páginas
$nb=$pdf->AliasNbPages();

#Creación Objeto FPDF

$pdf->AddPage();
//$pdf->SetMargins(20,20,20);

$pdf->AliasNbPages();
$pdf->SetFont('Arial','',6);


#Consulta Cuentas
$sql2 = "SELECT equivalente_fuente AS equivF, 
            equivalente_rubro AS equivR,
            SUM( ptto_inicial ) AS pInicial, 
            SUM( presupuesto_dfvo ) AS pDefinitivo, 
            SUM( registros) AS compromisos, 
            SUM( total_obligaciones ) AS obligaciones, 
            SUM( total_pagos ) AS pagos  
        FROM temporal_consulta_pptal_gastos
        WHERE equivalente_fuente IS NOT NULL 
            AND equivalente_fuente !=  ''
            AND equivalente_rubro IS NOT NULL 
            AND equivalente_rubro !=  ''
        GROUP BY equivalente_rubro, equivalente_fuente
    UNION ALL 
        SELECT equivalente_fuente AS equivF, 
            equivalente_rubro AS equivR, 
            ptto_inicial AS pInicial, 
            presupuesto_dfvo AS pDefinitivo, 
            registros AS compromisos, 
            total_obligaciones AS obligaciones, 
            total_pagos AS pagos
        FROM temporal_consulta_pptal_gastos
        WHERE equivalente_fuente IS NULL 
            OR equivalente_fuente =  ''
            OR equivalente_rubro IS NULL 
            OR equivalente_rubro =  ''";
$conejc  = $mysqli->query($sql2);

    $pdf->SetY(32);
    $pdf->Ln(5);
    $pdf->SetFont(Arial,'',8);
$total1=0;
$total2=0;
$total3=0;
$total4=0;
$total5=0;
    
while ($filactas = mysqli_fetch_array($conejc)) 
{
    $p1  = (float) $filactas['pInicial'];
    $p2  = (float) $filactas['pDefinitivo'];
    $p3  = (float) $filactas['compromisos'];
    $p4  = (float) $filactas['obligaciones'];
    $p5  = (float) $filactas['pagos'];
    switch ($mes){
        case 3:
            $trim= '01';
            break;
        case 6:
            $trim= '02';
            break;
        case 9:
            $trim= '03';
            break;
        case 12:
            $trim= '04';
            break;
    }
       # $codd = $codd + 1;
    if($p1==0 && $p2 ==0 && $p3==0 && $p4 ==0 && $p5 ==0 ){} else {
        $pdf->Cell(51,4,utf8_decode($codDane),0,0,'C');
        $pdf->Cell(31,4,utf8_decode($anno),0,0,'C');
        $pdf->Cell(31,4,utf8_decode($trim),0,0,'C');
        $pdf->Cell(31,4,utf8_decode($filactas['equivF']),0,0,'C');
        if(empty($filactas['equivR'])|| $filactas['equivR']=='null') {
            $pdf->Cell(31,4,utf8_decode(' '),0,0,'C');
        } else {
            $pdf->Cell(31,4,utf8_decode($filactas['equivR']),0,0,'C');
        }
        $pdf->Cell(31,4,number_format($p1,2,'.',','),0,0,'R');
        $pdf->Cell(31,4,number_format($p2,2,'.',','),0,0,'R');
        $pdf->Cell(31,4,number_format($p3,2,'.',','),0,0,'R');
        $pdf->Cell(31,4,number_format($p4,2,'.',','),0,0,'R');
        $pdf->Cell(31,4,number_format($p5,2,'.',','),0,0,'R');
        $total1=$total1+$p1;
        $total2=$total2+$p2;
        $total3=$total3+$p3;
        $total4=$total4+$p4;
        $total5=$total5+$p5;
         
        $pdf->Ln(4);
    }
}
## TOTALES ##
        $pdf->SetFont('Arial','B',8.5);
        $pdf->Ln(3);
        $pdf->SetX(25);
        $pdf->Cell(161,4,'TOTALES: ',0,0,'R');
        $pdf->CellFitScale(31,4,number_format($total1,2,'.',','),0,0,'R');
        $pdf->CellFitScale(31,4,number_format($total2,2,'.',','),0,0,'R');
        $pdf->CellFitScale(31,4,number_format($total3,2,'.',','),0,0,'R');
        $pdf->CellFitScale(31,4,number_format($total4,2,'.',','),0,0,'R');
        $pdf->CellFitScale(31,4,number_format($total5,2,'.',','),0,0,'R');
    ## FIN TOTALES ##
    
        $pdf->Ln(4);
        $pdf->Cell(330,0.5,utf8_decode(''),1,0,'C');
        $pdf->Cell(30,4,utf8_decode($filactas['cnnom']),0,0,'C');
 #$pdf->Ln(5);

 
 ######### ESTRUCTURA FIRMAS #########
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','',8.5);
 $pdf->Ln(20);
 $pdf->SetX(25);
 $compania = $_SESSION['compania'];
 $res = "SELECT rd.tercero, tr.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        WHERE td.nombre ='PP14' AND td.compania = $compania ";
 $res= $mysqli->query($res);
 $i=0;
 $x=130;
 #ESTRUCTURA
 if(mysqli_num_rows($res)>0){
     
     while ($row2 = mysqli_fetch_row($res)) {
         
         $ter = "SELECT IF(CONCAT(t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos)='', "
                 . "t.razonsocial,"
                 . "CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)) AS 'NOMBREC', "
                 . "t.numeroidentificacion, c.nombre "
                 . "FROM gf_tercero t "
                 . "LEFT JOIN gf_cargo c ON t.cargo = c.id_unico "
                 . "WHERE t.id_unico ='$row2[0]'";
         
         $ter = $mysqli->query($ter);
         $ter = mysqli_fetch_row($ter);
         $pdf->MultiCell(110,4,utf8_decode(strtoupper($row2[1])."\n\nFIRMA:_______________________________ \nNOMBRE: ".ucwords(strtolower($ter[0]))."\nC.C. N°:".number_format($ter[1],0,'.',',')."\nCARGO:".ucwords(strtolower($ter[2]))),0,'L');
        
         if($i==2 OR $i==5 OR $i==8 OR $i==11 OR $i==14 OR $i==17 OR $i==20){
           $pdf->Ln(10);
           $pdf->SetX(25);
           $x=130;
         } else {
         $pdf->Ln(-24);
         $pdf->SetX($x);
         $x=$x+110;
         }
         $i=$i+1;
     }
     
 }
while (ob_get_length()) { 
  ob_end_clean();
}  
 ######### FIN FIRMAS #########
 $pdf->Output(0,utf8_decode('Reporte Gastos Presupuestales SIFSE('.date('d-m-Y').').pdf'),0);
?>