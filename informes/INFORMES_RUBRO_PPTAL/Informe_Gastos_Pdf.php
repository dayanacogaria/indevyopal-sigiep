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
    $registrosAbiertos = $disponibilidad-$totalObligaciones;
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
            . "disponibilidad_abierta = '$disponibilidadesAbiertas', "
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
        $disponibilidadM = $rowa[8]+$va[8];
        $saldoDisponibleM = $rowa[9]+$va[9];
        $registrosM = $rowa[10]+$va[10];
        $registrosAbiertosM = $rowa[11]+$va[11];
        $totalObligacionesM = $rowa[12]+$va[12];
        $totalPagosM = $rowa[13]+$va[13];
        $reservasM = $rowa[14]+$va[14];
        $cuentasxpagarM = $rowa[15]+$va[15];
        $reduccionM = $rowa[16]+$va[16];
        $disponibilidadAbiertaM = $rowa[17]+$va[17];
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
                . "disponibilidad_abierta = '$disponibilidadAbiertaM', "
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
    $this->Cell(200,5,utf8_decode('RESUMEN PRESUPUESTAL GASTOS'),0,0,'C');
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
from temporal_consulta_pptal_gastos WHERE cod_rubro = '$codigo' ORDER BY cod_rubro ASC ";
$conejc  = $mysqli->query($sql2);

    $p1  = 0;
    $p2  = 0;
    $p3  = 0;
    $p4  = 0;
    $p5  = 0;
    $p6  = 0;
    $p7  = 0;
    $p8  = 0;
    $p9  = 0;
    $p10 = 0;
    $p11 = 0;
    $p12 = 0;
    $p13 = 0;
    $p14 = 0;
    $p15 = 0;
while ($filactas = mysqli_fetch_array($conejc)){

    
    $p1  = $p1+ (float) $filactas['ppti'];
    $p2  = $p2+ (float) $filactas['adi'];
    $p3  = $p3+(float) $filactas['red'];
    $p4  = $p4+(float) $filactas['tcred'];
    $p5  = $p5+(float) $filactas['trcont'];
    $p6  = $p6+(float) $filactas['ppdf'];
    $p7  = $p7+(float) $filactas['disp'];
    $p8  = $p8+(float) $filactas['sald'];
    $p9  = $p9+(float) $filactas['reg'];
    $p10 = $p10+(float) $filactas['rega'];
    $p11 = $p11+(float) $filactas['tobl'];
    $p12 = $p12+(float) $filactas['tpag'];
    $p13 = $p13+(float) $filactas['reserv'];
    $p14 = $p14+(float) $filactas['cpag'];
    $p15 = $p15+(float) $filactas['disAb'];
    
    $traslados= $p4+$p5;
    $apropiacion = $p1+$p2-$p3+$traslados;
    $aproVig = $apropiacion-$p7;
    $cdp=$p7-$p9;
    $comCum= $p9-$p11;
    $obligC= $p11-$p12;
    $porCdp = (($p7*100)/$apropiacion);
    $porCom = (($p9*100)/$apropiacion);
    $porObli = (($p11*100)/$apropiacion);
    $porPag = (($p12*100)/$apropiacion);
  }
 $pdf->setX(15);   
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p7==0 && $p8==0 && $p9==0 && $p10==0 && $p11==0 && $p12==0 && $p13==0)
        { } else {
        $pdf->SetFont('Arial','B',8); 
        $pdf->CellFitScale(90,6,utf8_decode('Apropiación'),0,0,'L');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(90,6,utf8_decode('Certificados de Disponibilidad Presupuestal'),0,0,'L');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Apropiación Inicial'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p1,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('C.D.P    %').number_format($porCdp,2,'.',','),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p7,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('+Adiciones'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p2,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('Aprop. Vigente No Afectada'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($aproVig,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Reducciones'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p3,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('C.D.P Por Comprometer'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($cdp,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('+- Traslados'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($traslados,2,'.',','),1,0,'R');
        $pdf->Cell(10,6,' ');
        $pdf->SetFont('Arial','B',8); 
        $pdf->CellFitScale(90,6,utf8_decode('Compromisos'),0,0,'L');
        $pdf->Ln(6);
        $pdf->setX(30);
        $pdf->Cell(70,0.5,utf8_decode(''),1,0,'R');
        $pdf->Ln(1);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Apropiación'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($apropiacion,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,number_format($porCom,2,'.',',').utf8_decode('%    Total Compromisos'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p9,2,'.',','),1,0,'R');
        $pdf->Ln(7);
    
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Aplazamientos'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,utf8_decode('Compromisos por Cumplir'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($comCum,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('+- Liberación aplazamientos'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Cell(10,6,' ');
        $pdf->SetFont('Arial','B',8); 
        $pdf->CellFitScale(90,6,utf8_decode('Obligaciones'),0,0,'L');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Apropiación Vigencia'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,number_format($porObli,2,'.',',').utf8_decode('%    Total Obligaciones'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p11,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','B',8); 
        $pdf->setX(15);
        $pdf->CellFitScale(85,6,utf8_decode('PAC'),0,0,'L');
        $pdf->Cell(5,6,' ');
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Obligaciones por cumplir'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($obligC,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Disponible Anual'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Cell(10,6,' ');
        $pdf->SetFont('Arial','B',8); 
        $pdf->CellFitScale(90,6,utf8_decode('Pagos Tesorería'),0,0,'L');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('00.00%   Acumulado'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format(0,2,'.',','),1,0,'R');
        $pdf->Cell(5,6,' ');
        $pdf->CellFitScale(45,6,number_format($porPag,2,'.',',').utf8_decode('%    Total Pagos'),0,0,'R');
        $pdf->CellFitScale(45,6,number_format($p12,2,'.',','),1,0,'R');
        $pdf->Ln(7);
        
        $pdf->SetFont('Arial','',8); 
        $pdf->CellFitScale(45,6,utf8_decode('Saldo de PAC Acumulado'),0,0,'R');
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
        WHERE td.nombre ='PP30' AND td.compania = $compania ";
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
$pdf->Output(0,utf8_decode('Resumen_Presupuestal_Gastos('.date('d-m-Y').').pdf'),0);
?>