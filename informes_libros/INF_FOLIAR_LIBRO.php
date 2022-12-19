<?php
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require_once('../Conexion/conexion.php');

session_start();
ob_start();
ini_set('max_execution_time', 0);    
#*************RECEPCION VARIABLES*****************#
$num_inicial    = $_POST['num_inicial'];
$num_final      = $_POST['num_final'];
$encabezado     = $_POST['encabezado'];
$papel          = $_POST['papel'];
$orientacion    = $_POST['orientacion'];
#*************CONSULTA DATOS LIBRO****************#
$libro          = $_POST['libro'];
$sql_libro ="SELECT id_unico, codigo_libro, nombre_libro FROM gf_libros WHERE id_unico = $libro";
$sql_libro = $mysqli->query($sql_libro);
$row = mysqli_fetch_row($sql_libro);
$codigo = $row[1];
$nombre = $row[2];

#*************CONSULTA DATOS COMPAÑIA*************#
$compania =$_SESSION['compania'];
$compa=$_SESSION['compania'];
$comp="SELECT
  t.razonsocial,
  IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
  t.numeroidentificacion, 
  CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)),
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
$nitcompania = $comp[1];
$ruta = $comp[2];
$direccion = $comp[3];
$telefono = $comp[4];
$usuario = $_SESSION['usuario'];

#CREACION PDF, HEAD AND FOOTER

class PDF extends FPDF
{
    function Header()
    { 
        
           
    }
   
}

$pdf = new PDF($orientacion,'mm',$papel);   
$pdf->AliasNbPages();


$pdf->SetFont('Arial','B',10);
if($orientacion =='L'){
    if($papel=='Letter'){
        $tam  = 190;
        $tam2 = 45;
        $tamc = 260;
    }elseif($papel ='Legal'){
        $tam  = 250;
        $tam2 = 60;
        $tamc = 330;
    }
    if($encabezado=='Si'){
        for ($i = $num_inicial; $i <= $num_final; $i++) { 
            $pdf->AddPage();
            if($ruta != '')
            {
              $pdf->Image('../'.$ruta,10,8,20);
            } 

            $pdf->SetX(35);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->MultiCell($tam,5,utf8_decode($nombreCompania),0,'C');
            $yn = $pdf->GetY();
            $pdf->SetXY($x+$tam, $y);
            $pdf->MultiCell($tam2,5,utf8_decode($nombre),0,'R');

            $y1 = $pdf->GetY();
            $pdf->SetXY($x, $yn);
            $x1 = $pdf->GetX();
            $pdf->MultiCell($tam,5,utf8_decode('Nit:'.$nitcompania),0,'C');
            $pdf->SetXY($x1+$tam, $y1);
            $pdf->MultiCell($tam2,5,utf8_decode('CODIGO: '.$codigo.'   PAG '.$i),0,'R');
            $pdf->ln(5);
        
        }
        
    }elseif($encabezado=='No'){
       for ($i = $num_inicial; $i <= $num_final; $i++) { 
           $pdf->AddPage();
           $pdf->MultiCell($tamc,5,utf8_decode($nombre),0,'R');
           $pdf->MultiCell($tamc,5,utf8_decode('CODIGO: '.$codigo.'   PAG '.$i),0,'R');
           $pdf->ln(5);
       }
    }
} elseif($orientacion=='P') {
   if($encabezado=='Si'){
        for ($i = $num_inicial; $i <= $num_final; $i++) { 
            $pdf->AddPage();
            if($ruta != '')
            {
               $pdf->Image('../'.$ruta,10,8,20);
            }

            $pdf->SetX(35);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->MultiCell(130,5,utf8_decode($nombreCompania),0,'C');
            $yn = $pdf->GetY();
            $pdf->SetXY($x+130, $y);
            $pdf->MultiCell(45,5,utf8_decode($nombre),0,'R');

            $y1 = $pdf->GetY();
            $pdf->SetXY($x, $yn);
            $x1 = $pdf->GetX();
            $pdf->MultiCell(130,5,utf8_decode('Nit:'.$nitcompania),0,'C');
            $pdf->SetXY($x1+130, $y1);
            $pdf->MultiCell(45,5,utf8_decode('CODIGO: '.$codigo.'   PAG '.$i),0,'R');
            $pdf->ln(5);
        }
   }elseif($encabezado=='No'){
       for ($i = $num_inicial; $i <= $num_final; $i++) { 
           $pdf->AddPage();
           $pdf->MultiCell(200,5,utf8_decode($nombre),0,'R');
           $pdf->MultiCell(200,5,utf8_decode('CODIGO: '.$codigo.'   PAG '.$i),0,'R');
           $pdf->ln(5);
       }
   } 
}


while (ob_get_length()) {
  ob_end_clean();
}

$pdf->Output(0,'Folios_'.$nombre.'.pdf',0);