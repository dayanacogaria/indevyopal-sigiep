<?php
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../jsonPptal/funcionesPptal.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);
require_once('../numeros_a_letras.php'); 
$anno = $_SESSION['anno'];

##########RECEPCION VARIABLES###############
#CUENTA INICIAL
if(empty($_POST['sltTi'])){
    $terceroI='1';
} else {
    $terceroI=$_POST['sltTi'];
}
#CUENTA FINAL
if(empty($_POST['sltTf'])){
    $terceroF='9';
} else {
    $terceroF=$_POST['sltTf'];
}

#FECHA INICIAL
if(empty($_POST['fechaini'])){
    $fechaY= anno($anno);
    $fechaI=$fechaY.'/01/01';
    $fecha1 = '01/01/'.$fechaY;
} else {
    $fecha1=$_POST['fechaini'];
    $fecha_div = explode("/", $fecha1);
    $dia1 = $fecha_div[0];
    $mes1 = $fecha_div[1];
    $anio1 = $fecha_div[2];
    $fechaI = $anio1.'/'.$mes1.'/'.$dia1;
}
#FECHA FINAL
if(empty($_POST['fechafin'])){
    $fechaY= anno($anno);
    $fechaF= $fechaY.'/12/31';
    $fecha2 = '31/12/'.$fechaY;
} else {
    $fecha2=$_POST['fechafin'];
    $fecha_div2 = explode("/", $fecha2);
    $dia2 = $fecha_div2[0];
    $mes2 = $fecha_div2[1];
    $anio2 = $fecha_div2[2];
    $fechaF = $anio2.'/'.$mes2.'/'.$dia2;
}
#OPCION#
$opcion =$_POST['opcion'];

##CONSULTA DATOS COMPAÑIA##
$compa=$_SESSION['compania'];
$comp="SELECT
  t.razonsocial,
  t.numeroidentificacion,
  t.digitoverficacion,
  t.ruta_logo,
  d.direccion,
  tel.valor
FROM
  gf_tercero t
LEFT JOIN
  gf_direccion d ON d.tercero = t.id_unico
LEFT JOIN
  gf_telefono tel ON tel.tercero = t.id_unico
WHERE
  t.id_unico =$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
if(empty($comp[2])) {
    $nitcompania = $comp[1];
} else {
    $nitcompania = $comp[1].' - '.$comp[2];
}
$ruta = $comp[3];
$direccion = $comp[4];
$telefono = $comp[5];
$usuario = $_SESSION['usuario'];
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
#CREACION PDF, HEAD AND FOOTER

class PDF extends FPDF
{
function Header()
{ 
    
    global $fecha1;
    global $fecha2;
    global $cuentaI;
    global $cuentaF;
    global $nombreCompania;
    global $nitcompania;
    global $numpaginas;
    global $ruta;
    global $direccion;
    global $telefono;
    global $fecha1;
    global $fecha2;
    
    
    $date1=$fecha1;
    
    $date2=$fecha2;
    
    $numpaginas=$this->PageNo();
    
    
    $this->SetY(10);
   
    if($ruta != '')
    {
      $this->Image('../'.$ruta,20,8,20);
    }
    $this->SetY(10);
    $this->SetFont('Arial','B',12);
    $this->Cell(190,10,utf8_decode('CERTIFICADO DE RETENCIÓN '),0,0,'C');
    $this->ln(5);
    $this->Cell(190,10,utf8_decode('EN LA FUENTE'),0,0,'C');
    $this->ln(15);
    $this->SetFont('Arial','',10);
    $this->Cell(80,10,utf8_decode('Razón Social del Retenedor:'),0,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(110,10,utf8_decode(ucwords(mb_strtolower($nombreCompania))),0,0,'L');
    $this->ln(5);
    $this->SetFont('Arial','',10);
    $this->Cell(80,10,utf8_decode('Nit:'),0,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(110,10,utf8_decode($nitcompania),0,0,'L');
    $this->ln(5);
    $this->SetFont('Arial','',10);
    $this->Cell(80,10,utf8_decode('Dirección:'),0,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(110,10,utf8_decode($direccion),0,0,'L');
    $this->ln(5);
    $this->SetFont('Arial','',10);
    $this->Cell(80,10,utf8_decode('Teléfono:'),0,0,'L');
    $this->SetFont('Arial','B',10);
    $this->Cell(110,10,utf8_decode($telefono),0,0,'L');
    $this->ln(10);
    $this->SetFont('Arial','',10);
    $this->MultiCell(190,5,utf8_decode("Con el fin de dar cumplimiento a las disposiciones vigentes "
            . "sobre retención en la fuente, certificamos que entre el $date1 y el $date2"),0,'J');
    
    $this->SetFont('Arial','B',10);
    $this->Cell(190,10,utf8_decode('HEMOS RETENIDO A:'),0,0,'C');
    $this->ln(10);
    }      
    
    function Footer()
    {
    // Posición: a 1,5 cm del final
    global $hoy;
    global $usuario;
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    $this->SetX(10);
    $this->Cell(40,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
    $this->Cell(55,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
    $this->Cell(55,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
    $this->Cell(40,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

$pdf = new PDF('P','mm','Letter');      


$pdf->AliasNbPages();

##############BUSQUEDA DE TERCEROS COMPROBANTES DE RETENCION ENTRE FECHAS#########
 $selecT ="SELECT DISTINCT
  t.id_unico 
FROM
  gf_retencion r
LEFT JOIN
    gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico
LEFT JOIN
    gf_cuenta c ON tr.cuenta = c.id_unico
LEFT JOIN
    gf_comprobante_cnt cn ON r.comprobante = cn.id_unico 
LEFT JOIN 
    gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante AND dc.cuenta = c.id_unico 
LEFT JOIN 
    gf_tercero t ON dc.tercero = t.id_unico 
LEFT JOIN 
	gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
WHERE 
  cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
  AND t.numeroidentificacion BETWEEN '$terceroI' AND '$terceroF' AND cn.parametrizacionanno = $anno";
$selecT =$mysqli->query($selecT);

while ($row = mysqli_fetch_row($selecT)) {
    $total =0;
        $pdf->AddPage();
    $yp = $pdf->GetY();
        
    $idT=$row[0];
    $dt="SELECT 
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
         tr.numeroidentificacion, 
         tr.digitoverficacion,  
         tr.id_unico, 
         dir.direccion, 
         tel.valor, 
         c.nombre, 
         d.nombre 
     FROM  gf_tercero tr 
     LEFT JOIN 
            gf_direccion dir ON dir.tercero = tr.id_unico 
     LEFT JOIN 
            gf_telefono tel ON tel.tercero = tr.id_unico LEFT JOIN gf_ciudad c ON c.id_unico = tr.ciudadresidencia 
     LEFT JOIN 
            gf_departamento d ON c.departamento = d.id_unico 
     WHERE tr.id_unico =$idT";
    $dt =$mysqli->query($dt);
    $dt = mysqli_fetch_row($dt);
    
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(30,10,utf8_decode('Nombre:'),0,0,'L');
    $pdf->SetFont('Arial','B',10);
    $pdf->CellFitScale(90,10,utf8_decode(ucwords(mb_strtolower($dt[0]))),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(30,10,utf8_decode('Nit o C.C N°:'),0,0,'L');
    $pdf->SetFont('Arial','B',10);
    if(!empty($dt[2])){
    $pdf->Cell(50,10,utf8_decode(ucwords(mb_strtolower($dt[1].' - '.$dt[2]))),0,0,'L');
    } else {
    $pdf->Cell(50,10,utf8_decode(ucwords(mb_strtolower($dt[1]))),0,0,'L');    
    }
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(30,10,utf8_decode('Dirección:'),0,0,'L');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(90,10,utf8_decode(ucwords(mb_strtolower($dt[4]))),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(30,10,utf8_decode('Teléfono:'),0,0,'L');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,10,utf8_decode(ucwords(mb_strtolower($dt[5]))),0,0,'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(30,10,utf8_decode('Ciudad:'),0,0,'L');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(90,10,utf8_decode(ucwords(mb_strtolower($dt[6]))),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(30,10,utf8_decode('Departamento:'),0,0,'L');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,10,utf8_decode(ucwords(mb_strtolower($dt[7]))),0,0,'L');
    $pdf->Ln(10);
    $pdf->Cell(30,10,utf8_decode('CÓDIGO'),1,0,'C');
    $pdf->Cell(100,10,utf8_decode('CONCEPTO'),1,0,'C');
    $pdf->Cell(30,10,utf8_decode('BASE'),1,0,'C');
    $pdf->Cell(30,10,utf8_decode('RETENCIÓN'),1,0,'C');
    $pdf->Ln(10);
    
    #####BUSCAR LAS RETENCIONES DEL TERCERO######
    if($opcion==1){
        $rt ="SELECT DISTINCT 
        r.id_unico,
        c.codi_cuenta,
        CONCAT(tr.nombre,' - ',tr.porcentajeaplicar,'%'), 
        SUM(r.retencionbase),
        SUM(r.valorretencion) 
      FROM
        gf_retencion r
      LEFT JOIN
        gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico
      LEFT JOIN
        gf_cuenta c ON tr.cuenta = c.id_unico
      LEFT JOIN
        gf_comprobante_cnt cn ON r.comprobante = cn.id_unico 
      LEFT JOIN 
        gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante AND dc.cuenta = c.id_unico 
      LEFT JOIN 
	gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
      WHERE 
        dc.tercero = $dt[3] AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' AND cn.parametrizacionanno = $anno 
      GROUP by c.codi_cuenta, tr.id_unico, dc.tercero ";
    
    } else {
        $rt ="SELECT DISTINCT 
        r.id_unico,
        c.codi_cuenta,
        CONCAT(tr.nombre,' - ',tr.porcentajeaplicar,'%'), 
        r.retencionbase,
        r.valorretencion
      FROM
        gf_retencion r
      LEFT JOIN
        gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico
      LEFT JOIN
        gf_cuenta c ON tr.cuenta = c.id_unico
      LEFT JOIN
        gf_comprobante_cnt cn ON r.comprobante = cn.id_unico 
      LEFT JOIN 
        gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante AND dc.cuenta = c.id_unico 
      LEFT JOIN 
	gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
      WHERE 
        dc.tercero = $dt[3] AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' AND cn.parametrizacionanno = $anno  ";
    }
    echo $rt;
    $rt =$mysqli->query($rt);
    $pdf->SetFont('Arial','',10);
    while ($row1 = mysqli_fetch_row($rt)) {
        $paginactual = $numpaginas;
        $alt = $pdf->GetY();
        if($alt>240){
            $pdf->AddPage();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(30,10,utf8_decode('Nombre:'),0,0,'L');
            $pdf->SetFont('Arial','B',10);
            $pdf->CellFitScale(90,10,utf8_decode(ucwords(mb_strtolower($dt[0]))),0,0,'L');
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(30,10,utf8_decode('Nit o C.C N°:'),0,0,'L');
            $pdf->SetFont('Arial','B',10);
            if(!empty($dt[2])){
            $pdf->Cell(50,10,utf8_decode(ucwords(mb_strtolower($dt[1].' - '.$dt[2]))),0,0,'L');
            } else {
            $pdf->Cell(50,10,utf8_decode(ucwords(mb_strtolower($dt[1]))),0,0,'L');    
            }
            $pdf->Ln(5);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(30,10,utf8_decode('Dirección:'),0,0,'L');
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(90,10,utf8_decode(ucwords(mb_strtolower($dt[4]))),0,0,'L');
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(30,10,utf8_decode('Teléfono:'),0,0,'L');
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(50,10,utf8_decode(ucwords(mb_strtolower($dt[5]))),0,0,'L');
            $pdf->Ln(5);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(30,10,utf8_decode('Ciudad:'),0,0,'L');
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(90,10,utf8_decode(ucwords(mb_strtolower($dt[6]))),0,0,'L');
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(30,10,utf8_decode('Departamento:'),0,0,'L');
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(50,10,utf8_decode(ucwords(mb_strtolower($dt[7]))),0,0,'L');
            $pdf->Ln(10);
            $pdf->Cell(30,10,utf8_decode('CÓDIGO'),1,0,'C');
            $pdf->Cell(100,10,utf8_decode('CONCEPTO'),1,0,'C');
            $pdf->Cell(30,10,utf8_decode('BASE'),1,0,'C');
            $pdf->Cell(30,10,utf8_decode('RETENCIÓN'),1,0,'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial','',10);
        }
        
        $x=$pdf->GetX();
        $y=$pdf->GetY();
        $pdf->Cell(30,5,'',0,0,'L');
        $pdf->MultiCell(100,5,utf8_decode(ucwords(mb_strtolower($row1[2]))),0,'J');
        $y2=$pdf->GetY();
        $h = $y2-$y;
        $px = $x+100;
        $pdf->SetXY($x,$y);
        $pdf->Cell(30,$h,utf8_decode($row1[1]),0,0,'L');
        $pdf->Cell(100,$h,'',0,0,'L');
        $pdf->Cell(30,$h, number_format($row1[3],2,'.',','),0,0,'R');
        $pdf->Cell(30,$h,number_format($row1[4],2,'.',','),0,0,'R');
        $pdf->Ln($h);
        $total +=$row1[4];
    }
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(160,10,utf8_decode('TOTAL RETENIDO'),1,0,'L');
    $pdf->Cell(30,10,number_format($total,2,'.',','),1,0,'R');
    $pdf->Ln(10);
    $valorLetras = numtoletras($total);
    $pdf->MultiCell(190,10,utf8_decode('SON:'.$valorLetras),0,'J');
    $pdf->SetFont('Arial','',10);
    $pdf->MultiCell(190,5,utf8_decode('Retención Consignada oportunamente en la Administración de Impuestos'
            . ' Nacionales Tesorería Municipal Correspondiente'),0,'J');
    $pdf->Cell(190,0,'',1,'R');
    $pdf->Ln(10);
    $fechaComp =date('d/m/Y');
    $fecha_div = explode("/", $fechaComp);
    $diaS = $fecha_div[0];
    $mesS = $fecha_div[1];
    $mesS = (int)$mesS;
    $anioS = $fecha_div[2];
    $pdf->MultiCell(190,10,utf8_decode('Expedida a los '.$diaS.' días del mes de '.$meses[$mesS].' de '.$anioS),0,'J');
    $pdf->Ln(10);
    $compania = $_SESSION['compania'];
     $sqlTipoComp = "SELECT IF(CONCAT_WS(' ',
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
            tr.numeroidentificacion, car.nombre 
            FROM
          gf_tipo_documento tpd
        LEFT JOIN
          gf_responsable_documento doc ON doc.tipodocumento = tpd.id_unico 
        LEFT JOIN
          gf_tipo_responsable tpr ON tpr.id_unico = doc.tiporesponsable
        LEFT JOIN
          gg_tipo_relacion tprl ON doc.tipo_relacion = tprl.id_unico
        LEFT JOIN
          gf_tercero tr ON doc.tercero = tr.id_unico
        LEFT JOIN
          gf_tipo_identificacion ti ON ti.id_unico = tr.tipoidentificacion
        LEFT JOIN
          gf_cargo_tercero carTer ON carTer.tercero = tr.id_unico
        LEFT JOIN
          gf_cargo car ON car.id_unico = carTer.cargo
        WHERE
          (tpd.nombre = LOWER('retencion') OR tpd.nombre = LOWER('retención')) AND tprl.nombre = 'Firma' AND tpd.compania = $compania ";

    $tipComp = $mysqli->query($sqlTipoComp); 
    $i = 0;
 $pdf->SetFont('Arial','B',10);
    while ($rowTipComp = mysqli_fetch_array($tipComp))
    {
      $firmaNom[$i] = $rowTipComp[0];
      $firmaNum[$i] = $rowTipComp[1];
      $firmaCarg[$i] = $rowTipComp[2];
      $i++;
    
    }
    $numFirmas = $i;

if($numFirmas > 3)
  $numFirmas = 3;

for($i = 0; $i < $numFirmas; $i++)
{
  $pdf->Cell(60,30,'',0,0,'C');
  
}


$pdf->SetX(-200);
$pdf->Ln(14);
for($i = 0; $i < $numFirmas; $i++)
{
  $pdf->Cell(1,0,'',0,0,'L');
  $pdf->Cell(55,0,'',0,0,'L');
}


$pdf->Ln(2);
for($i = 0; $i < $numFirmas; $i++)
{
    $pdf->Cell(60,0,'',1,0,'L');
    $pdf->Ln(1);
    if($firmaNom[$i]=='' || $firmaNom[$i]==""){
        $pdf->Cell(60,5,utf8_decode($firmaNom[$i]),0,0,'L');
    } else {
  $pdf->CellFitScale(60,5,utf8_decode($firmaNom[$i]),0,0,'L');
    }
    
}
  

$pdf->Ln(4);
for($i = 0; $i < $numFirmas; $i++)
{
    if($firmaCarg[$i]=='' || $firmaCarg[$i]==""){
        $pdf->Cell(70,5,utf8_decode($firmaCarg[$i]),0,0,'L');
    } else {
        $pdf->CellFitScale(70,5,utf8_decode($firmaCarg[$i]),0,0,'L');
    }
 
}

    
   
}




while (ob_get_length()) {
  ob_end_clean();
}

$pdf->Output(0,'Informe_Auxiliar_Retenciones ('.date('d/m/Y').').pdf',0);