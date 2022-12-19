<?php
##################################################################################################################################################################
#                                                                                                                           Modificaciones
##################################################################################################################################################################
#02/02/2018 |Erica G. | Parametrizacion Año
#09/09/2017 |Erica G. | Acumulado, actual, anterior
#01/09/2017 |Erica G. | Archivo Creado, Informe para Casalac y terminal
##################################################################################################################################################################
?>
<?php
require'../../fpdf/fpdf.php';
require'../../Conexion/conexion.php';
require'consultas.php';
ini_set('max_execution_time', 0);
ob_start();
?>

<?php
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
             AND rpp.tipoclase = 6  AND rpp.parametrizacionanno = $parmanno  
        ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
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
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' AND rpp.parametrizacionanno = $parmanno ";

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
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion;

    
 ##########################################################################################################################################################################    
#   ANTERIORES
##########################################################################################################################################################################    
    $fechaIAn = $anno.'-01-01';    
    $fechaFinAn = $anno.'-'.$mesI.'-01';
    $nuevafechaAn = strtotime ( '-1 day' , strtotime ( $fechaFinAn ) ) ;
    $fechaFAn = date ( 'Y-m-d' , $nuevafechaAn );
    #RECONOCIMIENTOS
    $reconocimientosAn= disponibilidades($row[4], 19, $fechaIAn, $fechaFAn);
    #RECAUDOS
    $recaudosAn = disponibilidades($row[4], 18, $fechaIAn, $fechaFAn);

##########################################################################################################################################################################    
#   ACTUALES
##########################################################################################################################################################################    
    $fechaIAc = $anno.'-'.$mesI.'-01';
    $diaFAc = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFinAc = $anno.'-'.$mesF.'-'.$diaFAc;
    #RECONOCIMIENTOS
    $reconocimientosAc= disponibilidades($row[4], 19, $fechaIAc, $fechaFinAc);
    #RECAUDOS
    $recaudosAc = disponibilidades($row[4], 18, $fechaIAc, $fechaFinAc);
    
######################################################################################################################################################
#   ACUMULADO
######################################################################################################################################################
    $fechaIAcum = $anno.'-01-01';  
    $diaFAc = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFAcum = $anno.'-'.$mesF.'-'.$diaFAc;
    #RECONOCIMIENTOS
    $reconocimientosAcum= disponibilidades($row[4], 19, $fechaIAcum, $fechaFAcum);
    #RECAUDOS
    $recaudosAcum = disponibilidades($row[4], 18, $fechaIAcum, $fechaFAcum);
######################################################################################################################################################
    
    #SALDOS POR RECAUDAR
    $saldosRecaudar = $reconocimientosAcum-$recaudosAcum;
    #SALDOS POR EJECUTAR
    $saldosEjecutar = $presupuestoDefinitivo-$reconocimientosAcum;
    #PRESUPUESTO POR RECAUDAR
    $pptoRecaudar= $presupuestoDefinitivo-$recaudosAcum;

###ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "disponibilidades ='$reconocimientosAn', "
            . "saldo_disponible = '$reconocimientosAc', "
            . "disponibilidad_abierta = '$reconocimientosAcum', "
            . "total_pagos = '$recaudosAn',"
            . "reservas = '$recaudosAc', "
            . "recaudos = '$recaudosAcum', "
            . "registros = '$saldosEjecutar', "
            . "registros_abiertos ='$pptoRecaudar',"
            . "saldos_x_recaudar = '$saldosRecaudar' "
            . "WHERE rubro_fuente = '$row[4]'";
    $update = $mysqli->query($update);
          
}   
//#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
$acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, "
        . "disponibilidades, "
        . "saldo_disponible, "
        . "disponibilidad_abierta, "
        . "total_pagos, "
        . "reservas, "
        . "recaudos, "
        . "registros, "
        . "registros_abiertos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
$acum = $mysqli->query($acum);

while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, "
        . "disponibilidades, "
        . "saldo_disponible, "
        . "disponibilidad_abierta, "
        . "total_pagos, "
        . "reservas,  "
        . "recaudos, "
        . "registros, "
        . "registros_abiertos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
        . "ORDER BY cod_rubro DESC ";
    $acumd = $mysqli->query($acumd);
    while ($rowa= mysqli_fetch_row($acumd)){
        if(!empty($rowa[2])){
            $va11= "SELECT id_unico, "
                    . "cod_rubro,"
                    . "cod_predecesor, "
                    . "ptto_inicial, adicion, reduccion, "
                    . "presupuesto_dfvo, "
                    . "disponibilidades, "
                    . "saldo_disponible, "
                    . "disponibilidad_abierta, "
                    . "total_pagos, "
                    . "reservas, "
                    . "recaudos, "
                    . "registros, "
                    . "registros_abiertos, "
                    . "saldos_x_recaudar "
            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $pptoInicialM = $rowa[3]+$va[3];
        $adicionM = $rowa[4]+$va[4];
        $reduccionM = $rowa[5]+$va[5];
        $presupuestoDefinitivoM = $rowa[6]+$va[6];
        $reconocimientosAnM =$rowa[7]+$va[7];
        $reconocimientosAcM =$rowa[8]+$va[8];
        $reconocimientosAcumM =$rowa[9]+$va[9];
        $recaudosAnM = $rowa[10]+$va[10];
        $recaudosAcM = $rowa[11]+$va[11];
        $recaudosAcumM = $rowa[12]+$va[12];
        $saldosEjecutarM = $rowa[13]+$va[13];
        $pptoRecaudarM = $rowa[14]+$va[14];
        $saldosRecaudarM = $rowa[15]+$va[15];
        
        #ACTUALIZAR TABLA CON DATOS HALLADOS
         $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "disponibilidades ='$reconocimientosAnM', "
                . "saldo_disponible ='$reconocimientosAcM', "
                . "disponibilidad_abierta ='$reconocimientosAcumM', "
                . "total_pagos ='$recaudosAnM', "
                . "reservas ='$recaudosAcM', "
                . "recaudos = '$recaudosAcumM', "
                . "registros = '$saldosEjecutarM', "
                . "registros_abiertos ='$pptoRecaudarM',"
                . "saldos_x_recaudar = '$saldosRecaudarM' "
                . "WHERE cod_rubro = '$rowa[2]'";
        $updateA = $mysqli->query($updateA);
        }
    }
}

#********************Datos Compañia **************************##
$compania = $_SESSION['compania'];
$consulta = "SELECT         t.razonsocial as traz,
                    t.tipoidentificacion as tide,
                    ti.id_unico as tid,
                    ti.nombre as tnom,
                    t.numeroidentificacion tnum, 
                    t.ruta_logo  as ruta 
FROM gf_tercero t
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE t.id_unico = $compania";
$cmp = $mysqli->query($consulta);
$nomcomp = "";
$tipodoc = "";
$numdoc = "";
 while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = ($fila['traz']);       
        $tipodoc = ($fila['tnom']);       
        $numdoc = ($fila['tnum']);   
        $ruta = ($fila['ruta']);   
    }
$month1 ="";
$month2 ="";
$meses = array( "01" => 'Enero', "02" => 'Febrero', "03" => 'Marzo',"04" => 'Abril', "05" => 'Mayo', "06" => 'Junio', 
                "07" => 'Julio', "08" => 'Agosto', "09" => 'Septiembre', "10" => 'Octubre', "11" => 'Noviembre', "12" => 'Diciembre');
$month1 = $meses[$mesI];
$month2 = $meses[$mesF];

class PDF extends FPDF
{
function Header()
{ 
    global $nomcomp;
    global $tipodoc;
    global $numdoc;
    global $ruta;
    global $month1;
    global $month2;


    if($ruta != '')
    {
        $this->Image('../../'.$ruta,60,6,20);
    }
    $this->SetFont('Arial','B',10);
    $this->Cell(340,5,utf8_decode($nomcomp),0,0,'C');
    $this->setX(20);
    $this->SetFont('Arial','B',8);
    $this->Cell(320,10,utf8_decode('CÓDIGO SGC'),0,0,'R');
    $this->Ln(5);
    $this->SetFont('Arial','',8);
    $this->Cell(340, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
    $this->SetFont('Arial','B',8);
    $this->SetX(20);
    $this->Cell(320,10,utf8_decode('VERSIÓN SGC'),0,0,'R');
    $this->Ln(5);
    $this->SetFont('Arial','',8);
    $this->Cell(340,5,utf8_decode('EJECUCION DEL PRESUPUESTO DE INGRESOS RECONOCIDOS'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(20);
    $this->Cell(320,10,utf8_decode('FECHA SGC'),0,0,'R');
    $this->Ln(3);
    
    $this->SetFont('Arial','',8);
    $this->Cell(340,5,utf8_decode('ENTRE '.utf8_decode (mb_strtoupper($month1.' Y '.$month2)).' - '.$anno),0,0,'C');
    
    $this->Ln(5);
    
    $this->SetX(10);
    $this->SetFont('Arial','B',6);
    $this->Cell(18,9,utf8_decode(''),1,0,'C');#
    $this->Cell(66,9,utf8_decode(''),1,0,'C');#
    $this->Cell(9,9,utf8_decode(''),1,0,'C');#
    $this->Cell(18,9,utf8_decode(''),1,0,'C');#
    $this->Cell(40,9,utf8_decode(''),1,0,'C');#
    $this->Cell(18,9,utf8_decode(''),1,0,'C');#
    $this->Cell(54,9,utf8_decode(''),1,0,'C');
    $this->Cell(54,9,utf8_decode(''),1,0,'C');
    $this->Cell(18,9,utf8_decode(''),1,0,'C');
    $this->Cell(18,9,utf8_decode(''),1,0,'C');
    $this->Cell(18,9,utf8_decode(''),1,0,'C');
    
    
        $this->SetX(10);
    
    $this->Cell(18,9,utf8_decode('RUBRO'),1,0,'C');#
    $this->Cell(66,9,utf8_decode('DETALLE'),1,0,'C');#
    $this->Cell(9,9,utf8_decode('FUENTE'),1,0,'C');#
    $this->Cell(18,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->Cell(40,4,utf8_decode('MODIFICACIONES PRESUPUESTALES'),0,0,'C');#
    $this->Cell(18,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->Cell(54,4,utf8_decode('RECONOCIMIENTO'),0,0,'C');#
    $this->Cell(54,4,utf8_decode('RECAUDO'),0,0,'C');#
    $this->Cell(18,7,utf8_decode('SALDOS POR'),0,0,'C');#
    $this->Cell(18,7,utf8_decode('SALDOS POR'),0,0,'C');#
    $this->Cell(18,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->Ln(4);
    $this->SetX(10);
    $this->Cell(18,5,utf8_decode(''),0,0,'C');#
    $this->Cell(66,5,utf8_decode(''),0,0,'C');#
    $this->Cell(9,5,utf8_decode(''),0,0,'C');#
    $this->Cell(18,5,utf8_decode('INICIAL'),0,0,'C');#
    $this->Cell(20,5,utf8_decode('ADICIÓN'),1,0,'C');
    $this->Cell(20,5,utf8_decode('REDUCCIÓN'),1,0,'C');
    $this->Cell(18,5,utf8_decode('DEFINITIVO'),0,0,'C');
    $this->Cell(18,5,utf8_decode('ANTERIOR'),1,0,'C');#
    $this->Cell(18,5,utf8_decode('ACTUAL'),1,0,'C');#
    $this->Cell(18,5,utf8_decode('ACUMULADO'),1,0,'C');#
    $this->Cell(18,5,utf8_decode('ANTERIOR'),1,0,'C');#
    $this->Cell(18,5,utf8_decode('ACTUAL'),1,0,'C');#
    $this->Cell(18,5,utf8_decode('ACUMULADO'),1,0,'C');#
    $this->Cell(18,5,utf8_decode('RECAUDAR'),0,0,'C');#
    $this->Cell(18,5,utf8_decode('EJECUTAR'),0,0,'C');#
    $this->Cell(18,5,utf8_decode('POR RECAUDAR'),0,0,'C');#
    $this->Ln(5);
    $this->Cell(326,5,'',0);
    }      
function Footer()
    {
    
        $this->SetY(-15);
        $this->SetFont('Arial','B',8);
        $this->Cell(15);
        $this->Cell(25,10,utf8_decode('Fecha: '. date('d/m/Y')),0,0,'L');
        $this->Cell(70);
        $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
        $this->Cell(60);
        $this->Cell(30,10,utf8_decode('Usuario:'.$_SESSION['usuario']),0); //.get_current_user()
        $this->Cell(70);
        $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
    }
}

$pdf = new PDF('L','mm','Legal');        
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',6);

#Consulta Cuentas
$sql2 = "SELECT DISTINCT 
                        cod_rubro                           as codrub, 
                        nombre_rubro                   as nomrub,
                        ptto_inicial                         as ppti,
                        adicion                                as adi,
                        reduccion                           as red,
                        presupuesto_dfvo             as ppdf,
                        cod_fuente                          as fuente , 
                        disponibilidades              as reconocimientosAn,
                        saldo_disponible              as reconocimientosAc, 
                        disponibilidad_abierta    as reconocimientosAcum, 
                        total_pagos                         as recaudosAn, 
                        reservas                              as recaudosAc, 
                        recaudos                             as recaudosAcum,
                        registros                             as saldosEjecutar, 
                        registros_abiertos            as pptoRecaudar, 
                        saldos_x_recaudar           as saldosRecaudar  
        FROM temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
$conejc  = $mysqli->query($sql2);

    $pdf->SetY(32);
    $pdf->Ln(5);
    $pdf->SetFont(Arial,'',6);
  
    
while ($filactas = mysqli_fetch_array($conejc)) 
{
    $p1    = (float) $filactas['ppti'];
    $p2    = (float) $filactas['adi'];
    $p3    = (float) $filactas['red'];
    $p4    = (float) $filactas['ppdf'];
    $p5    = (float) $filactas['reconocimientosAn'];
    $p6    = (float) $filactas['reconocimientosAc'];
    $p7    = (float) $filactas['reconocimientosAcum'];
    $p8    = (float) $filactas['recaudosAn'];
    $p9    = (float) $filactas['recaudosAc'];
    $p10    = (float) $filactas['recaudosAcum'];
    $p11  = (float) $filactas['saldosRecaudar'];
    $p12  = (float) $filactas['saldosEjecutar'];
    $p13  = (float) $filactas['pptoRecaudar'];
    $a = $pdf->GetY();
    if($a > 190)
    {
        $pdf->AddPage();
        $pdf->SetX(10);  
    }
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 
            && $p5==0 && $p6==0 && $p7==0 
            && $p8==0 && $p9==0 && $p10==0
            && $p11==0 && $p12==0 && $p13==0)
    { } else {
        if($filactas['codrub']!="")
            $pdf->cellfitscale(18,4,utf8_decode($filactas['codrub']),0,0,'R');
        else
            $pdf->Cell(18,4,'',0,0,'L');
        
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(66,4,utf8_decode($filactas['nomrub']),0,'L');
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 66;
        $pdf->Ln(-$h);
        $pdf->SetX($px);
        if(!empty($filactas['fuente']))
            $pdf->cellfitscale(9,4,utf8_decode($filactas['fuente']),0,0,'C');
        else
            $pdf->Cell(9,4,'',0,0,'L');        
        $pdf->cellfitscale(18,4,number_format($p1,2,'.',','),0,0,'R');
        $pdf->cellfitscale(20,4,number_format($p2,2,'.',','),0,0,'R');
        $pdf->cellfitscale(20,4,number_format($p3,2,'.',','),0,0,'R');
        $pdf->cellfitscale(18,4,number_format($p4,2,'.',','),0,0,'R');
        $pdf->cellfitscale(18,4,number_format($p5,2,'.',','),0,0,'R');
        $pdf->cellfitscale(18,4,number_format($p6,2,'.',','),0,0,'R');
        $pdf->cellfitscale(18,4,number_format($p7,2,'.',','),0,0,'R');
        $pdf->cellfitscale(18,4,number_format($p8,2,'.',','),0,0,'R');
        $pdf->cellfitscale(18,4,number_format($p9,2,'.',','),0,0,'R');
        $pdf->cellfitscale(18,4,number_format($p10,2,'.',','),0,0,'R');
        $pdf->cellfitscale(18,4,number_format($p11,2,'.',','),0,0,'R');
        $pdf->cellfitscale(18,4,number_format($p12,2,'.',','),0,0,'R');
        $pdf->cellfitscale(18,4,number_format($p13,2,'.',','),0,0,'R');
        $pdf->Ln($h);
    }
}
#*Línea del final
$pdf->Cell(334,0.5,utf8_decode(''),1,0,'C');

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
        WHERE LOWER(td.nombre) ='ejecucion presupuestal' 
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
 $pdf->Output(0,utf8_decode('Informe_Ejecucion_Ingresos_Reconocidos('.date('d-m-Y').').pdf'),0);
?>