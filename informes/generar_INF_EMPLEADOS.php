<?php
session_start();
    
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';

ini_set('max_execution_time', 360);
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];

$consulta = "SELECT         t.razonsocial as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            ti.nombre as tnom,
                            t.numeroidentificacion tnum
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);
$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = '.$compania;

$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];
 $nomcomp = "";
    $tipodoc = "";
    $numdoc = 0;
    
    while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = utf8_decode($fila['traz']);       
        $tipodoc = utf8_decode($fila['tnom']);       
        $numdoc = utf8_decode($fila['tnum']);   
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
    global $nomcomp;
    global $tipodoc;
    global $numdoc;
    global $ruta;
     if($ruta != '')
    { 
      $this->Image('../'.$ruta,20,8,15);
    } 
    // Logo
    //$this->Image('logo_pb.png',10,8,33);
    //Arial bold 10
    $this->SetFont('Arial','B',14);
    
        // Título
    $this->Cell(330,10,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    

    $this->Ln(4);

    $this->SetFont('Arial','',10);
    $this->Cell(330,10,utf8_decode($tipodoc.': '.$numdoc),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
   

    $this->Ln(4);

    $this->SetFont('Arial','',8);
    $this->Cell(330,10,utf8_decode('LISTADO EMPLEADOS'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    
    
    $this->Ln(15);
    
    $this->SetFont('Arial','B',10);
    $this->Cell(30,5, utf8_decode('Código Interno'),1,0,'C');
    $this->Cell(40,5, utf8_decode(' Identificación'),1,0,'C');
    $this->Cell(170,5, utf8_decode('Nombre'),1,0,'C');
    $this->Cell(50,5, utf8_decode('Grupo Gestion'),1,0,'C');
    $this->Cell(40,5,utf8_decode('Medio de Pago'),1,0,'C');
    #$this->Cell(29,5,utf8_decode('Tipo Empresa'),1,0,'C');
    #$this->Cell(25,5,utf8_decode('Tipo Entidad'),1,0,'C');
    #$this->Cell(15,5,utf8_decode('Zona'),1,0,'C');
    $this->Ln(5);

    
    }
    // Pie de página
        function Footer()
            {
            // Posición: a 1,5 cm del final
            global $hoy;
            global $usuario;
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','B',9);
                $this->SetX(10);
                $this->Cell(90,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
        }


// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');        

$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();

$pdf->SetFont('Arial','',7);

//consulta sql
$sql = "SELECT e.id_unico, 
     e.codigointerno, 
     e.tercero, 
     e.grupogestion,
     tr.numeroidentificacion,
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
     gg.nombre,
     e.mediopago,
     mp.nombre
     FROM gn_empleado e 
     LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico 
     LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico
     LEFT JOIN gn_medio_pago mp ON  e.mediopago = mp.id_unico
     WHERE e.id_unico !=2";

$cp      = $mysqli->query($sql);
$codd    = 0;
#Asignación de anchos de columna
$mc0=30;$mc1=40;$mc2=150;$mc3=50;$mc4=40;

while ($fila = mysqli_fetch_array($cp)){
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell($mc0,10,utf8_decode($fila[1]),0,0,'C');
    $pdf->Cell($mc1,10,utf8_decode($fila[4]),0,0,'C');
    $pdf->Cell($mc2,10,utf8_decode($fila[5]),0,0,'L');
    $pdf->Cell(20,10,utf8_decode(''),0,0,'L');
    $pdf->Cell($mc3,10,utf8_decode($fila[6]),0,0,'L');
    $pdf->Cell($mc4,10,utf8_decode($fila[8]),0,0,'L');
    $pdf->Ln(3);
} 
        

ob_end_clean();
$pdf->Output(0,'Informe_Terceros ('.date('d/m/Y').').pdf',0);     
  
?>