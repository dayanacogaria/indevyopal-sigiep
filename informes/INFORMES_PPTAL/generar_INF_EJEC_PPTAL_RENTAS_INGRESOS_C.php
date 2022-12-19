<?php
##################################################################################################################################################################
#                                                                                                                           Modificaciones
##################################################################################################################################################################
#17/11/2017 |ERICA G.|INFORME PAC
#24/08/2017 |Erica G. | Modificacion logo, firmas
#08/03/2017 |ERICA G. |CONSULTAS
#11/02/2017 | 9:15 | Daniel N: Control de cambio de página para evitar pérdida de registros con el salto de página.
#10/02/2017 | 5:25 | Daniel N: Salto de línea dinámico en el detalle rubro.
#02-02-2017 | 9:30 | Erica González //Modificacion búsqueda disponibilidades
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
$parmanno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0]; 
$mes = $mysqli->real_escape_string(''.$_POST['sltmes'].'');
$dia = cal_days_in_month($calendario, $mes, $anno); 
$fecha = $anno.'-'.$mes.'-'.$dia;
$fechaFir = $anno.'-'.$mes.'-01';
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
             AND rpp.tipoclase = 6 AND rpp.parametrizacionanno = '$parmanno' 
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
#SI RECIBE O NO FUENTE
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
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' AND rpp.parametrizacionanno = $parmanno";
} else {
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
          WHERE f.id_unico ='$fuente' AND rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' AND rpp.parametrizacionanno = $parmanno"; 
}
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
    #RECAUDOS
    $recaudos = disponibilidades($row[4], 18, $fechaInicial, $fecha);
    #SALDOS POR RECAUDAR
    $saldos = $presupuestoDefinitivo-$recaudos;
    
    #ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "recaudos = '$recaudos', "
            . "saldos_x_recaudar = '$saldos' "
            . "WHERE rubro_fuente = '$row[4]'";
    $update = $mysqli->query($update);
          
}   
//#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
$acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, recaudos, "
        . "saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
$acum = $mysqli->query($acum);

while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, recaudos, "
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
            . "presupuesto_dfvo, recaudos, "
            . "saldos_x_recaudar "
            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $pptoInicialM = $rowa[3]+$va[3];
        $adicionM = $rowa[4]+$va[4];
        $reduccionM = $rowa[5]+$va[5];
        $presupuestoDefinitivoM = $rowa[6]+$va[6];
        $recaudosM = $rowa[7]+$va[7];
        $saldosM = $rowa[8]+$va[8];
        
        #ACTUALIZAR TABLA CON DATOS HALLADOS
        $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "recaudos = '$recaudosM', "
                . "saldos_x_recaudar = '$saldosM' "
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


////Asingación de valor a Mes 1    
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
                                t.ruta_logo  as ruta 
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
$tipo = $_POST['tipo'];    
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
    global $tipo;
    
    $this->SetFont('Arial','B',10);
        // Título
    $this->SetY(10);
     if($ruta != '')
    {
        $this->Image('../../'.$ruta,60,6,20);
    }
    $this->Cell(340,5,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->setX(20);
    $this->SetFont('Arial','B',8);
    $this->Cell(324,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    $this->Cell(340, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
    $this->SetFont('Arial','B',8);
    $this->SetX(20);
    $this->Cell(324,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',8);
    if($tipo==1) {
    $this->Cell(340,5,utf8_decode('INFORME PAC INGRESOS'),0,0,'C');
    } else {
    $this->Cell(340,5,utf8_decode('EJECUCION DEL PRESUPUESTO DE RENTAS E INGRESOS'),0,0,'C');
    }
    $this->SetFont('Arial','B',8);
    
    $this->SetX(20);
    $this->Cell(324,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',8);
    $this->Cell(340,5,utf8_decode('MES DE '.utf8_decode (ucwords(strtoupper($month1))).' - '.$anno),0,0,'C');
    
    $this->Ln(5);
    
    $this->SetX(20);
    
    
    if($tipo==1) {
    $this->Cell(28,9,utf8_decode(''),1,0,'C');#
    $this->Cell(74,9,utf8_decode(''),1,0,'C');#
    $this->Cell(24,9,utf8_decode(''),1,0,'C');#
    $this->Cell(35,9,utf8_decode(''),1,0,'C');#
    $this->Cell(60,9,utf8_decode(''),1,0,'C');#
    $this->Cell(40,9,utf8_decode(''),1,0,'C');#
    $this->Cell(40,9,utf8_decode(''),1,0,'C');
    $this->Cell(20,9,utf8_decode(''),1,0,'C');
    
        $this->SetX(20);
    $this->Cell(28,9,utf8_decode('RUBRO'),1,0,'C');#
    $this->Cell(74,9,utf8_decode('DETALLE'),1,0,'C');#
    $this->Cell(24,9,utf8_decode('FUENTE'),1,0,'C');#
    $this->Cell(35,4,utf8_decode('PAC'),0,0,'C');#
    $this->Cell(60,4,utf8_decode('MODIFICACIONES PAC'),0,0,'C');#
    $this->Cell(40,4,utf8_decode('PAC'),0,0,'C');#
    $this->Cell(40,4,utf8_decode('PAC'),0,0,'C');#
    $this->Cell(20,4,utf8_decode('%'),0,0,'C');#
    
   
    $this->Ln(4);
    
    $this->SetX(20);
    
    $this->Cell(28,5,utf8_decode(''),0,0,'C');#
    $this->Cell(74,5,utf8_decode(''),0,0,'C');#
    $this->Cell(24,5,utf8_decode(''),0,0,'C');#
    $this->Cell(35,5,utf8_decode('INICIAL'),0,0,'C');#
    $this->Cell(30,5,utf8_decode('ADICIÓN'),1,0,'C');
    $this->Cell(30,5,utf8_decode('REDUCCIÓN'),1,0,'C');
    $this->Cell(40,5,utf8_decode('DEFINITIVO'),0,0,'C');
    $this->Cell(40,5,utf8_decode('RECAUDADO'),0,0,'C');#
    $this->Cell(20,5,utf8_decode('EJECUCIÓN'),0,0,'C');#
    
    } else {
    $this->Cell(23,9,utf8_decode(''),1,0,'C');#
    $this->Cell(60,9,utf8_decode(''),1,0,'C');#
    $this->Cell(24,9,utf8_decode(''),1,0,'C');#
    $this->Cell(35,9,utf8_decode(''),1,0,'C');#
    $this->Cell(60,9,utf8_decode(''),1,0,'C');#
    $this->Cell(35,9,utf8_decode(''),1,0,'C');#
    $this->Cell(33,9,utf8_decode(''),1,0,'C');
    $this->Cell(33,9,utf8_decode(''),1,0,'C');
    $this->Cell(20,9,utf8_decode(''),1,0,'C');
    
    
    $this->SetX(20);
    $this->Cell(23,9,utf8_decode('RUBRO'),1,0,'C');#
    $this->Cell(60,9,utf8_decode('DETALLE'),1,0,'C');#
    $this->Cell(24,9,utf8_decode('FUENTE'),1,0,'C');#
    $this->Cell(35,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->Cell(60,4,utf8_decode('MODIFICACIONES PRESUPUESTALES'),0,0,'C');#
    $this->Cell(35,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->Cell(33,9,utf8_decode('RECAUDO'),0,0,'C');#
    $this->Cell(33,7,utf8_decode('SALDOS POR'),0,0,'C');#
    $this->Cell(20,7,utf8_decode('%'),0,0,'C');#
    
   
    $this->Ln(4);
    
    $this->SetX(20);
    
    $this->Cell(23,5,utf8_decode(''),0,0,'C');#
    $this->Cell(60,5,utf8_decode(''),0,0,'C');#
    $this->Cell(24,5,utf8_decode(''),0,0,'C');#
    $this->Cell(35,5,utf8_decode('INICIAL'),0,0,'C');#
    $this->Cell(30,5,utf8_decode('ADICIÓN'),1,0,'C');
    $this->Cell(30,5,utf8_decode('REDUCCIÓN'),1,0,'C');
    $this->Cell(35,5,utf8_decode('DEFINITIVO'),0,0,'C');
    $this->Cell(33,5,utf8_decode(''),0,0,'C');#
    $this->Cell(33,5,utf8_decode('RECAUDAR'),0,0,'C');#
    $this->Cell(20,5,utf8_decode('EJECUCIÓN'),0,0,'C');#
    }
    
    $this->Ln(5);
    $this->Cell(326,5,'',0);
    }      
    
function Footer()
    {
    // Posición: a 1,5 cm del final
    global $hoy;
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    // Número de página
        $this->Cell(15);
        $this->Cell(25,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
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


 #Declaración Variable Número de Páginas
$nb=$pdf->AliasNbPages();

#Creación Objeto FPDF

$pdf->AddPage();
//$pdf->SetMargins(20,20,20);

$pdf->AliasNbPages();
$pdf->SetFont('Arial','',6);


#Consulta Cuentas
$sql2 = "SELECT DISTINCT 
                        cod_rubro           as codrub, 
                        nombre_rubro        as nomrub,
                        ptto_inicial        as ppti,
                        adicion             as adi,
                        reduccion           as red,
                        presupuesto_dfvo    as ppdf,
                        recaudos            as reca,
                        reservas            as reserv,
                        saldos_x_recaudar   as spag, 
                        cod_fuente          as fuente 
from temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
$conejc  = $mysqli->query($sql2);

    $pdf->SetY(32);
    $pdf->Ln(5);
    $pdf->SetFont(Arial,'',7);
if($tipo==1){     
while ($filactas = mysqli_fetch_array($conejc)) 
{
    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['ppdf'];
    $p5  = (float) $filactas['reca'];
    $p6  = (float) $filactas['spag'];
    $a = $pdf->GetY();
    if($a > 170)
    {
        #$pdf->Ln(10);
        $pdf->AddPage();
        $pdf->SetX(10);
        
    }
       # $codd = $codd + 1;
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0)
        { } else {
         #Actualización 10/02/2017  
         $pdf->SetX(20);
         #Actualización 10/02/2017
        $pdf->Cell(28,4,'',0,0,'R');        
        #Actualización 10/02/2017   
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(74,4,utf8_decode($filactas['nomrub']),0,'L');        
        $y2 = $pdf->GetY();
        $h1 = $y2-$y;
        $pdf->Ln(-$h1);
        $pdf->SetX($x+74);
        $pdf->MultiCell(24,4,utf8_decode($filactas['fuente']),0,'L');        
        $y3 = $pdf->GetY();
        $h2 = $y3-$y;
        $h = max($h1, $h2);
        $px = $x + 24;
        $pdf->SetY($y);
        $pdf->SetX(20);
        if(!empty($filactas['codrub'])) {
            $pdf->CellFitScale(28,$h,$filactas['codrub'],1,0,'L');
        } else {
            $pdf->Cell(28,$h,'',1,0,'L');
        }
        $pdf->Cell(74,$h,'',1,0,'R'); 
        $pdf->Cell(24,$h,'',1,0,'R'); 

        if(empty($p1)) {
            $pdf->Cell(35,$h,number_format($p1,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(35,$h,number_format($p1,2,'.',','),1,0,'R');
        }
        if(empty($p2)) {
            $pdf->Cell(30,$h,number_format($p2,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(30,$h,number_format($p2,2,'.',','),1,0,'R');
        }
        
         if(empty($p3)) {
            $pdf->Cell(30,$h,number_format($p3,2,'.',','),1,0,'R');
        } else {
            $pdf->Cell(30,$h,number_format($p3,2,'.',','),1,0,'R');
        }
        
         if(empty($p4)) {
            $pdf->Cell(40,$h,number_format($p4,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(40,$h,number_format($p4,2,'.',','),1,0,'R');
        }
        
         if(empty($p5)) {
            $pdf->Cell(40,$h,number_format($p5,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(40,$h,number_format($p5,2,'.',','),1,0,'R');
        }
        if(empty($p5)) {
            $pdf->Cell(20,$h,round(($p5*100)/$p4,2).'%',1,0,'R');
        } else {
            $pdf->CellFitScale(20,$h,round(($p5*100)/$p4,2).'%',1,0,'R');
        }
         $pdf->Ln($h);
         #----- 
        // if($filactas['codrub']!="")
        //     $pdf->cellfitscale(28,4,utf8_decode($filactas['codrub']),1,0,'L');
        // else
        //     $pdf->Cell(28,4,'',0,0,'L');
        // $y = $pdf->GetY();
        // $x = $pdf->GetX();        
        // $pdf->MultiCell(80,4,utf8_decode($filactas['nomrub']),1,'L');
        // $y2 = $pdf->GetY();
        // $h = $y2-$y;
        // $px = $x + 80;
        // $pdf->Ln(-$h);
        // $pdf->SetX($px);
       
        //     $pdf->cellfitscale(24,4,utf8_decode($filactas['fuente']),1,0,'L');
      
        // $pdf->cellfitscale(35,4,number_format($p1,2,'.',','),1,0,'R');
        // $pdf->cellfitscale(36,4,number_format($p2,2,'.',','),1,0,'R');
        // $pdf->cellfitscale(36,4,number_format($p3,2,'.',','),1,0,'R');
        // $pdf->cellfitscale(45,4,number_format($p4,2,'.',','),1,0,'R');
        // $pdf->cellfitscale(45,4,number_format($p5,2,'.',','),1,0,'R');
        // $pdf->Ln($h);
        #Actualización 10/02/2017
}
}

} else {
    while ($filactas = mysqli_fetch_array($conejc)) 
{
    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['ppdf'];
    $p5  = (float) $filactas['reca'];
    $p6  = (float) $filactas['spag'];
$a = $pdf->GetY();
        if($a>160)
        {
            $pdf->AddPage();     
            $pdf->setX(20); 
        }
       # $codd = $codd + 1;
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0)
        { } else {
        $pdf->SetX(20);
         #Actualización 10/02/2017
        $pdf->Cell(23,4,'',0,0,'R');        
        #Actualización 10/02/2017   
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(60,4,utf8_decode($filactas['nomrub']),0,'L');        
        $y2 = $pdf->GetY();
        $h1 = $y2-$y;
        $pdf->Ln(-$h1);
        $pdf->SetX($x+60);
        $pdf->MultiCell(24,4,utf8_decode($filactas['fuente']),0,'L');        
        $y3 = $pdf->GetY();
        $h2 = $y3-$y;
        $h = max($h1, $h2);
        $px = $x + 60;
        $pdf->SetY($y);
        $pdf->SetX(20);
        if(!empty($filactas['codrub'])) {
            $pdf->CellFitScale(23,$h,$filactas['codrub'],1,0,'L');
        } else {
            $pdf->Cell(23,$h,'',1,0,'L');
        }
        $pdf->Cell(60,$h,'',1,0,'R'); 
        $pdf->Cell(24,$h,'',1,0,'R'); 
        if(empty($p1)) {
            $pdf->Cell(35,$h,number_format($p1,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(35,$h,number_format($p1,2,'.',','),1,0,'R');
        }
        if(empty($p2)) {
            $pdf->Cell(30,$h,number_format($p2,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(30,$h,number_format($p2,2,'.',','),1,0,'R');
        }
        
         if(empty($p3)) {
            $pdf->Cell(30,$h,number_format($p3,2,'.',','),1,0,'R');
        } else {
            $pdf->Cell(30,$h,number_format($p3,2,'.',','),1,0,'R');
        }
        
         if(empty($p4)) {
            $pdf->Cell(35,$h,number_format($p4,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(35,$h,number_format($p4,2,'.',','),1,0,'R');
        }
        
         if(empty($p5)) {
            $pdf->Cell(33,$h,number_format($p5,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(33,$h,number_format($p5,2,'.',','),1,0,'R');
        }
        
         if(empty($p6)) {
            $pdf->Cell(33,$h,number_format($p6,2,'.',','),1,0,'R');
        } else {
            $pdf->CellFitScale(33,$h,number_format($p6,2,'.',','),1,0,'R');
        }
         

        if(empty($p6)) {
            $pdf->Cell(20,$h,round(($p5*100)/$p4,2).'%',1,0,'R');
        } else {
            $pdf->CellFitScale(20,$h,round(($p5*100)/$p4,2).'%',1,0,'R');
        }



         $pdf->Ln($h);    
             #-----------------

        // if($filactas['codrub']!="")
        //     $pdf->cellfitscale(23,5,utf8_decode($filactas['codrub'].'           '),1,0,'L');
        // else
        //     $pdf->Cell(23,4,'',0,0,'L');
        //  $y = $pdf->GetY();
        // $x = $pdf->GetX();       
        // $pdf->MultiCell(70,4,utf8_decode($filactas['nomrub']),1,'L');
        // $y2 = $pdf->GetY();
        // $h = $y2-$y;
        // $px = $x + 70;
        // $pdf->SetXY($px, $y);
    
        // $pdf->cellfitscale(24,$h,utf8_decode($filactas['fuente']),1,0,'L');


        // $pdf->cellfitscale(35,$h,number_format($p1,2,'.',','),1,0,'R');
        // $pdf->cellfitscale(36,$h,number_format($p2,2,'.',','),1,0,'R');
        // $pdf->cellfitscale(36,$h,number_format($p3,2,'.',','),1,0,'R');
        // $pdf->cellfitscale(35,$h,number_format($p4,2,'.',','),1,0,'R');
        // $pdf->cellfitscale(35,$h,number_format($p5,2,'.',','),1,0,'R');
        // $pdf->cellfitscale(35,$h,number_format($p6,2,'.',','),1,0,'R');
        // $pdf->Ln($h);
        #Actualización 10/02/2017
}
}
}



        //$pdf->SetX(30);
        $pdf->Cell(330,0.5,utf8_decode(''),1,0,'C');
        $pdf->Cell(30,4,utf8_decode($filactas['cnnom']),0,0,'C');
        
        
################################ ESTRUCTURA FIRMAS ##########################################
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','B',9);
$pdf->Ln(15);
 $compania = $_SESSION['compania'];
  $res = "SELECT  c.nombre,rd.orden,IF(CONCAT_WS(' ',
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
        WHERE LOWER(td.nombre) ='Ejecución Presupuestal Ingresos' 
        AND if(rd.fecha_inicio IS NULL, rd.fecha_inicio IS NULL, rd.fecha_inicio <= '$fechaFir') 
        AND if(rd.fecha_fin IS NULL,rd.fecha_fin IS NULL, rd.fecha_fin >= '$fecha') 
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
 ##################################################################################
        
while (ob_get_length()) {
  ob_end_clean();
}
 $pdf->Output(0,utf8_decode('Informe_Ejecuciones_Pptales_Rentas_Ingresos('.date('d-m-Y').').pdf'),0);
?>