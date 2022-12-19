<?php
##############MODIFICACIONES##########################
#04/03/2017 | ERICA G. | ARREGLO BUSQUEDAS
#######################################################
require'../../fpdf/fpdf.php';
require'../../Conexion/conexion.php';
session_start();
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
#SE DEFINE LA FECHA INICIAL Y LA FECHA FINAL
if(empty($_POST['sltmesI'])){
    $fechaInicial = $anio.'-'.'01-01';
} else {
    $mes = $_POST['sltmesI'];
    $fechaInicial = $anio.'-'.$mes.'-01';
}
if(empty($_POST['sltmesF'])){
    $annioA = date('Y');
    $mesA = date('m');
    if($anio ==$annioA){
        $dia = cal_days_in_month($calendario, $mesA, $anio); 
        $fechaFinal = $anio.'-'.$mesA.'-'.$dia;
    } else {
        $fechaFinal = $anio.'-12-31';
    }
} else {
    $mes = $_POST['sltmesF'];
    $dia = cal_days_in_month($calendario, $mes, $anio);
    $fechaFinal = $anio.'-'.$mes.'-'.$dia;
}
$rubro = $mysqli->real_escape_string(''.$_POST['codigo'].'');
#SE REALIZA LA BUSQUEDA DEL RUBRO SEGUN LAS FECHAS
$con = "SELECT
      rp.codi_presupuesto   as rpcodp,
      rp.nombre             as rpnom,
      dcp.rubrofuente       as dcprf,
      dcp.tercero           as dcpter, 
      tcp.clasepptal        as tcpcla, 
      cp.fecha              as cpfecha, 
      tcp.codigo            as tcpcod, 
      cp.numero             as cpnum, 
      IF(CONCAT_WS(' ',
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
        tr.apellidodos)) AS NOMBRE, 
      
      tr.numeroidentificacion as numId, 
      dcp.descripcion       as dcpdesc,
      tcp.tipooperacion     as tcptop, 
      dcp.id_unico  as idDetalle, 
      dcp.valor as valor, 
      tcp.id_unico as tipocom, 
      dcp.comprobanteafectado as coma 
    FROM
      gf_detalle_comprobante_pptal dcp
    LEFT JOIN
      gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
    LEFT JOIN
      gf_rubro_pptal rp ON rf.rubro = rp.id_unico
    LEFT JOIN
      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
    LEFT JOIN 
      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
    LEFT JOIN 
        gf_tercero tr ON tr.id_unico = dcp.tercero    
   WHERE (tcp.clasepptal ='13' OR tcp.clasepptal ='14' OR tcp.clasepptal ='15' 
   OR tcp.clasepptal ='16' OR tcp.clasepptal ='17') AND rp.codi_presupuesto ='$rubro' "
        . "AND (cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal') "
        . "ORDER BY cp.fecha, cp.numero ASC";

    $con = $mysqli->query($con);
    
    
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
  codi_presupuesto ='$rubro'";
$ct = $mysqli->query($ct);
if(mysqli_num_rows($ct)>0){
    $ct = mysqli_fetch_array($ct);
    $codNombre= $ct['codi_presupuesto'].' - '. ucwords(mb_strtolower($ct['nombre']));
   
} else {
    $codNombre= $codigo;
    
} 

#ESTRUCTURA DE INFORMES


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
    $this->Cell(330,5,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->setX(10);
    $this->SetFont('Arial','B',8);
    $this->Cell(330,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    $this->Cell(330, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
    $this->SetFont('Arial','B',8);
    $this->SetX(10);
    $this->Cell(330,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',8);
    $this->Cell(330,5,utf8_decode('MOVIMIENTO PRESUPUESTAL-GASTOS'),0,0,'C');
    $this->SetFont('Arial','B',8);
    
    $this->SetX(10);
    $this->Cell(330,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->Cell(330,5,utf8_decode('CODIGO:  '.utf8_decode (ucwords(strtoupper($codNombre)))),0,0,'C');
    
    $this->SetFont('Arial','',8);
    $this->Ln(5);
     $this->SetX(15);
#Empty Cells
    $this->Cell(15,9,utf8_decode(''),1,0,'C');
    $this->Cell(23,9,utf8_decode(''),1,0,'C');
    $this->Cell(23,9,utf8_decode(''),1,0,'C');
    $this->Cell(33,9,utf8_decode(''),1,0,'C');
    $this->Cell(110,9,utf8_decode(''),1,0,'C');
    $this->Cell(33,9,utf8_decode(''),1,0,'C');
    $this->Cell(45,9,utf8_decode(''),1,0,'C');
    $this->Cell(23,9,utf8_decode(''),1,0,'C');
    $this->Cell(23,9,utf8_decode(''),1,0,'C');
    
    $this->SetX(15);
    #Cell Row 1
    $this->Cell(15,9,utf8_decode('Tipo'),0,0,'C');
    $this->Cell(23,9,utf8_decode('Número'),0,0,'C');
    $this->Cell(23,9,utf8_decode('Fecha'),0,0,'C');
    $this->Cell(33,9,utf8_decode('Valor'),0,0,'C');
    $this->Cell(110,9,utf8_decode('Descripción'),0,0,'C');
    $this->Cell(33,9,utf8_decode('Doc. Tercero'),0,0,'C');
    $this->Cell(45,9,utf8_decode('Tercero'),0,0,'C');
    $this->Cell(23,9,utf8_decode('Tipo Afectado'),0,0,'C');
    $this->Cell(23,9,utf8_decode('Afectado'),0,0,'C');
    
    $this->Ln(10);
    $this->Cell(326,5,'',0);
    
    
    
    
    }      
    
function Footer()
    {
    global $usuario;
    global $fechaActual;
    $this->SetY(-15);
    $this->SetFont('Arial','B',8);
    $this->Cell(15);
    $this->Cell(25,10,utf8_decode('Fecha: '.date('Y-m-d')),0,0,'L');
    $this->Cell(70);
    $this->Cell(35,10,utf8_decode('Máquina: '.  gethostname()),0);
    $this->Cell(60);
    $this->Cell(30,10,utf8_decode('Usuario: admin'),0); //.get_current_user()
    $this->Cell(70);
    $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
    }
    
    
}

$pdf = new PDF('L','mm','Legal');  
#Declaración Variable Número de Páginas
$nb=$pdf->AliasNbPages();

#Creación Objeto FPDF
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',8);
$total=0;

if(mysqli_num_rows($con)>0){
while ($row = mysqli_fetch_array($con)) {
        $pdf->SetX(15);
       $tipo =$row['tcpcod'];
       $numero =$row['cpnum'];
       $fecha =$row['cpfecha'];
       $date = date_create($fecha);
       $fecha=date_format($date, 'd/m/Y');
       $valor =$row['valor'];
       $descripcion = $row['dcpdesc'];
       $numTercero =$row['numId'];
       $tercero =$row['NOMBRE'];
       if(empty($row['coma'])){
            $tipoA=' ';
            $NumAfectado= ' ';
       } else {
           $compr= $row['coma'];
           $comA = "SELECT
                    tcp.codigo            as tcpcod, 
                    cp.numero             as cpnum 
                  FROM
                    gf_detalle_comprobante_pptal dcp
                  LEFT JOIN
                    gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
                  LEFT JOIN 
                    gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
                  WHERE dcp.id_unico = $compr";
           $comA = $mysqli->query($comA);
           if(mysqli_num_rows($comA)>0){
                $comAf = mysqli_fetch_array($comA);
                $tipoA=$comAf['tcpcod'];
                $NumAfectado= $comAf['cpnum'];
           } else {
              $tipoA=' ';
                $NumAfectado= ' '; 
           }
       }
       $pdf->CellFitScale(15,4,utf8_decode($tipo.' '),0,0,'C');
        $pdf->CellFitScale(23,4,utf8_decode($numero.' '),0,0,'C');
        $pdf->CellFitScale(23,4,utf8_decode($fecha.' '),0,0,'C');
        $pdf->CellFitScale(33,4,number_format($valor,2,'.',',').' ',0,0,'R');
        
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(110,4,utf8_decode(ucwords(mb_strtolower($descripcion))),0,'L');
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 110;
        $pdf->Ln(-$h);
        $pdf->SetX($px);
        
        $pdf->CellFitScale(33,4,utf8_decode($numTercero.' '),0,0,'L');
        
        $y1 = $pdf->GetY();
        $x1 = $pdf->GetX();        
        $pdf->MultiCell(45,4,utf8_decode(ucwords(mb_strtolower($tercero))),0,'L');
        $y21 = $pdf->GetY();
        $h1 = $y21-$y1;
        $px1 = $x1 + 45;
        $pdf->Ln(-$h1);
        $pdf->SetX($px1);
        
        
        
        $pdf->CellFitScale(23,4,utf8_decode($tipoA.' '),0,0,'C');
        $pdf->CellFitScale(23,4,utf8_decode($NumAfectado.' '),0,0,'C');
        if($h>$h1){
            $s = $h;
        } else {
            $s=$h1;
        }
        
        $pdf->Ln($s);
        $total = $valor+$total;
       
    }
     ## TOTALES ##
$pdf->Ln(5);
$pdf->SetX(15);
$pdf->SetFont('Arial','B',8.5);
$pdf->Cell(61,4,'TOTALES: ',0,0,'R');
$pdf->CellFitScale(33,4,number_format($total,2,'.',','),0,0,'R');
## FIN TOTALES ##
}

########TABLA ##########
######### ESTRUCTURA FIRMAS #########
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','',8.5);
 $pdf->Ln(20);
 $pdf->SetX(25);
 $compania = $_SESSION['compania'];
 $res = "SELECT rd.tercero, tr.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        WHERE td.nombre ='PP32' AND td.compania = $compania ";
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
    