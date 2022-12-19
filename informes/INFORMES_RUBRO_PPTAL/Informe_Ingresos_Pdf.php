<?php
##################MODIFICACIONES###############
#04/03/2017 |ERICA G. | MODIFICACION CONSULTAS
###############################################
require'../../fpdf/fpdf.php';
require'../../Conexion/conexion.php';
require_once("./consultas.php");
ini_set('max_execution_time', 0);
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');
ob_start();
?>

<?php
$calendario = CAL_GREGORIAN;
$anno = $mysqli->real_escape_string(''.$_SESSION['anno'].'');
$anio = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico ='$anno'";
$anio = $mysqli->query($anio);
$anio = mysqli_fetch_row($anio);
$anio = $anio[0];
$fechaInicial = $anio.'-'.'01-01';
$fecha = $anio.'-'.'12-01';
$codigo = $mysqli->real_escape_string(''.$_POST['codigo'].'');
$codigoI =$mysqli->real_escape_string(''.$_POST['codigo'].'');

$cant = strlen($codigoI);
if($cant>1){

for($i = 0; $i < $cant-1;$i++){
     $men = substr($codigoI,0,-1);
     $codigoI=$men;
}
} else {
    $men = $codigoI;
}
$codigoF=$men+1;

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
         WHERE rpp.codi_presupuesto BETWEEN '$codigo' AND '$codigoF' ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    $mysqli->query($insert);
    
}
    
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
          WHERE rpp.codi_presupuesto BETWEEN '$codigo' AND '$codigoF' ORDER BY rpp.codi_presupuesto ASC";
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
          WHERE f.id_unico ='$fuente' AND rpp.codi_presupuesto BETWEEN '$codigo' AND '$codigoF' ORDER BY rpp.codi_presupuesto ASC";
   
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
    $presupuestoDefinitivo = $pptoInicial+$adicion+$reduccion;
    #RECAUDOS
    $recaudos = disponibilidades($row[4], 18, $fechaInicial, $fecha);
    #SALDOS POR RECAUDAR
    $saldos = $pptoInicial-$recaudos;
    
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

#CONSULTAS ENCABEZADO
#DATOS COMPAÑIA
$compania = $_SESSION['compania'];
$consulta = "SELECT t.razonsocial as traz,
            t.tipoidentificacion as tide,      
            ti.id_unico as tid,
            ti.nombre as tnom,
            t.numeroidentificacion tnum
           FROM gf_tercero t
           LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
           WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

#Inicialización parámetros Header
$nomcomp = "";
$tipodoc = "";
$numdoc = "";
if(mysqli_num_rows($cmp)>0){
    $fila = mysqli_fetch_array($cmp) ;
    $nomcomp = utf8_decode($fila['traz']);       
    $tipodoc = utf8_decode($fila['tnom']);       
    $numdoc = utf8_decode($fila['tnum']);
}
#DATOS CUENTA
$ct= "SELECT
  codi_presupuesto,
  nombre,
  fuente
FROM
  gf_rubro_pptal r
LEFT JOIN
  gf_rubro_fuente rf ON rf.rubro = r.id_unico
WHERE
  codi_presupuesto ='$codigo'";
$ct = $mysqli->query($ct); 
if(mysqli_num_rows($ct)>0){
    $ct = mysqli_fetch_array($ct);
    $codNombre= $ct['codi_presupuesto'].' - '. ucwords(mb_strtolower($ct['nombre']));
    
} else {
    $codNombre= $codigo;
    
}
#FUENTE
if(empty($_POST['fuente'])){
    $fuentef='';
} else {
    $f = $_POST['fuente'];
    $fuentef = "SELECT id_unico, nombre FROM gf_fuente WHERE id_unico = '$f'";
    $fuentef = $mysqli->query($fuentef);
    if(mysqli_num_rows($fuentef)>0){
        $fuentef= mysqli_fetch_array($fuentef);
        $fuentef = $fuentef['id_unico'].' - '.$fuentef['nombre'];
    }else {
        $fuentef=''; 
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

    global $codNombre;
    global $fuentef;
    global $anno;
    
    $this->setX(0);
    
    $this->SetFont('Arial','B',10);
        // Título
    $this->SetY(10);
    //$this->image('../LOGOABC.png', 20,10,20,15,'PNG');    
    $this->Cell(200,5,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->setX(0);
    $this->SetFont('Arial','B',8);
    $this->Cell(200,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    $this->Cell(200, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(200,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',8);
    $this->Cell(200,5,utf8_decode('RESUMEN PRESUPUESTAL INGRESOS'),0,0,'C');
    $this->SetFont('Arial','B',8);
    
    $this->SetX(0);
    $this->Cell(200,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->Cell(200,5,utf8_decode('CODIGO:  '.utf8_decode (ucwords(mb_strtoupper($codNombre)))),0,0,'C');
    
    
    if(empty($fuentef)){
    $this->Ln(5);    
    } else {
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->Cell(200,5,utf8_decode('FUENTE:  '.utf8_decode (ucwords(mb_strtoupper($fuentef)))),0,0,'C');
    $this->Ln(5);    
    
    
    }
    
    }      
    
function Footer()
    {
    global $usuario;
    global $fechaActual;
    $this->SetY(-15);
    $this->SetFont('Arial','B',8);
    $this->Cell(15);
    $this->Cell(25,10,utf8_decode('Fecha: '.$fechaActual),0,0,'L');
    $this->Cell(20);
    $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
    $this->Cell(20);
    $this->Cell(30,10,utf8_decode('Usuario:'.mb_strtoupper($usuario)),0); 
    $this->Cell(20);
    $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0);
    }
}

$pdf = new PDF('P','mm','A4');  
#Declaración Variable Número de Páginas
$nb=$pdf->AliasNbPages();

#Creación Objeto FPDF
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->setY(37);

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
from temporal_consulta_pptal_gastos WHERE cod_rubro = '$codigo' ORDER BY cod_rubro ASC";

$sql2 = $mysqli->query($sql2);
    $p1  = 0;
    $p2  = 0;
    $p3  = 0;
    $p4  = 0;
    $p5  = 0;
    $p6  = 0;
 while($filactas = mysqli_fetch_array($sql2)){
    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['ppdf'];
    $p5  = (float) $filactas['reca'];
    $p6  = (float) $filactas['spag'];
    $apropiacion = $p1+$p2-$p3;
    $saldoRe = $apropiacion -$p5;
    $porRecau = (($p5*100)/$apropiacion);
 }
    
    $pdf->setX(15);
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 )
        { } else { 
        $pdf->SetFont('Arial','B',8); 
        $pdf->CellFitScale(90,6,utf8_decode('Apropiación'),0,0,'L');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(90,6,utf8_decode('Reconocimientos'),0,0,'L');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Apropiación Inicial'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p1,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('Apropiación Definitiva'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p1,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('+Adiciones'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p2,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('(-)Total Reconocimientos'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Ln(6);
        $pdf->setX(115);
        $pdf->Cell(80,0.5,utf8_decode(''),1,0,'R');
        $pdf->Ln(1);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('-Reducciones'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p3,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('Saldos Por Reconocer'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p1,2,'.',','),1,0,'R');
        $pdf->Ln(6);
        $pdf->setX(20);
        $pdf->Cell(80,0.5,utf8_decode(''),1,0,'R');
        $pdf->setX(115);
        $pdf->Cell(80,0.5,utf8_decode(''),1,0,'R');
        $pdf->Ln(1);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Apropiación'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($apropiacion,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('Total Reconocimientos'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('-Aplazamientos'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('(-)Total Recaudado'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Ln(6);
        $pdf->setX(115);
        $pdf->Cell(80,0.5,utf8_decode(''),1,0,'R');
        $pdf->Ln(1);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Apropiación Vigencia'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('Reconocimientos X Recaudar'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        
        
        $pdf->SetFont('Arial','B',8); 
        $pdf->setX(15);
        $pdf->CellFitScale(85,6,utf8_decode('PAC'),0,0,'L');
        $pdf->Cell(5,6,' ');
        $pdf->setX(110);
        $pdf->CellFitScale(85,6,utf8_decode('Ingresos Recaudados'),0,0,'L');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('PAC Programado'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,number_format($porRecau,2,'.',',').utf8_decode('%  Total Recaudos'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p5,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('00.00%   Rezago'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('Saldo por Recaudar'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($saldoRe,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Prog X Recaudar'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
 
        }

 
 ######### ESTRUCTURA FIRMAS #########
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','',8.5);
 $pdf->Ln(20);
 $pdf->SetX(25);
 $compania = $_SESSION['compania'];
 $res = "SELECT rd.tercero, tr.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        WHERE td.nombre ='PP31' AND td.compania = $compania ";
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
         $pdf->MultiCell(110,4,utf8_decode(mb_strtoupper($row2[1])."\n\nFIRMA:_______________________________ \nNOMBRE: ".ucwords(mb_strtolower($ter[0]))."\nC.C. N°:".number_format($ter[1],0,'.',',')."\nCARGO:".ucwords(mb_strtolower($ter[2]))),0,'L');
        
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
$pdf->Output(0,utf8_decode('Resumen_Presupuestal_Ingresos('.date('d-m-Y').').pdf'),0);
?>