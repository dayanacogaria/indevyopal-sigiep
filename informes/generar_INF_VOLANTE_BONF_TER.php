<?php
################################################################################
#
#12/07/2017 |Nesstor B| se agrego la validacion del empleado 
#
#
################################################################################
session_start();
    
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';

ini_set('max_execution_time', 360);
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];
ob_start();
$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = '.$compania;

$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];

if(!empty($_REQUEST['id_emp'])){

    echo $empleado = $_REQUEST['id_emp'];
}

if(!empty($_REQUEST['id_per'])){

    $periodo = $_REQUEST['id_per'];
}

if(!empty($_POST['sltEmpleado'])){

    $empleado  = $_POST['sltEmpleado'];  
}

if(!empty($_POST['sltPeriodo'])){

    $periodo   = $_POST['sltPeriodo'];  
}

$consulta = "SELECT         t.razonsocial as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            ti.nombre as tnom,
                            t.numeroidentificacion tnum
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

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


$per = "";
$emp = "";
$codi = "";


class PDF extends FPDF
{
// Cabecera de página  
function Header()
{ 
    global $nomcomp;
    global $tipodoc;
    global $numdoc;
    global $per;
    global $emp;
    global $codi;
    global $ruta;
    global $valor;
    global $codcon;
    global $descon;
    global $numeroP; 

    $numeroP = $this->PageNo();

    if($ruta != '')
    {
      $this->Image('../'.$ruta,20,8,15);
    } 
    // Logo
    //$this->Image('logo_pb.png',10,8,33);
    //Arial bold 10
    $this->SetFont('Arial','B',10);
    
        // Título
    
    $this->SetX(20);
    $this->Cell(170,5,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
   $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    $this->SetX(20);
    $this->Cell(170, 5,$tipodoc.': '.$numdoc,0,0,'C'); ;
    $this->Ln(5);
    $this->Cell(190,5,utf8_decode('COMPROBANTE DE BONIFICACIÓN DE GESTIÓN TERRITORIAL'),0,0,'C');
    // Salto de línea
    $this->Ln(3);
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    
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
                $this->Cell(30,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                $this->Cell(10,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
        }


// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm',array(215,139));   



$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();

$pdf->SetFont('Arial','',18);

//consulta sql

if(empty($empleado) || $empleado == 2){

$consulta2 = "SELECT    e.id_unico, 
                        t.id_unico, 
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        c.id_unico,
                        c.nombre, 
                        tc.id_unico,
                        tc.categoria,
                        e.grupogestion,
                        gp.id_unico,
                        gp.nombre 
                        FROM gn_empleado e 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                        LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico
                        LEFT JOIN gn_grupo_gestion gp ON e.grupogestion = gp.id_unico
                        LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                        WHERE e.id_unico !=2  AND n.concepto = 103
                        ORDER BY e.id_unico";
}else{

    $consulta2 = "SELECT   e.id_unico, 
                        t.id_unico, 
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        c.id_unico,
                        c.nombre, 
                        tc.id_unico,
                        tc.categoria,
                        e.grupogestion,
                        gp.id_unico,
                        gp.nombre   
                        FROM gn_empleado e 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                        LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico
                        LEFT JOIN gn_grupo_gestion gp ON e.grupogestion = gp.id_unico
                        WHERE md5(e.id_unico) = '$empleado' 
                        ORDER BY e.id_unico";    
}                         
                        
$empl = $mysqli->query($consulta2);
$numemp = mysqli_num_rows($empl);


$consulta3 = "SELECT id_unico, codigointerno, fechainicio, fechafin FROM gn_periodo WHERE md5(id_unico) = '$periodo'";
$perio = $mysqli->query($consulta3);
$perN  = mysqli_fetch_row($perio);

while($fila1 = mysqli_fetch_row($empl)){

    $idemp = $fila1[0];
    //$per  = utf8_decode($fila1[1]);
    $emp  = $fila1[2];


    //$codi = utf8_decode($fila1[0]);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(37,18,utf8_decode('NÓMINA:'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode($perN[1]),0,0,'C');
    $pdf->Cell(25.5,18,utf8_decode(''),0,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(35,18,utf8_decode('NOMBRE:'),0,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(15,18,utf8_decode($emp),0,0,'C');
    $pdf->Ln(4);
    $pdf->SetX(11);
    $pdf->Cell(20,18,utf8_decode('Fecha Inicial:'),0,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(32,18,utf8_decode($perN[2]),0,0,'C');
    $pdf->Cell(37,18,utf8_decode('CARGO:'),0,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(27,18,utf8_decode($fila1[4]),0,0,'C');
    $pdf->Ln(4);
    $pdf->SetX(13);
    $pdf->Cell(15,19,utf8_decode('Fecha Final:'),0,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(37.5,18,utf8_decode($perN[3]),0,0,'C');
    $pdf->Cell(45,18,utf8_decode('GRUPO GESTIÓN:'),0,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,18,utf8_decode($fila1[9]),0,0,'C');
   
    $pdf->SetFont('Arial','',10);
    $pdf->SetFont('Arial','B',8);
    
    $pdf->SetX(0);
    $pdf->Ln(4);
    
    $pdf->SetFont('Arial','B',8);
    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(0);
    $pdf->Ln(8);
    
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
    $pdf->Cell(60,5, utf8_decode('Concepto'),1,0,'C');
    $pdf->Cell(15,5, utf8_decode('Dias T'),1,0,'C');
    $pdf->Cell(40,5,utf8_decode('Devengos'),1,0,'C');
    $pdf->Cell(29,5,utf8_decode('Descuentos'),1,0,'C');
    $pdf->Cell(25,5,utf8_decode('Salario Base'),1,0,'C');
    #$this->Cell(15,5,utf8_decode('Zona'),1,0,'C');
    $pdf->Ln(5);
    
    $mc0=20;$mc1=60;$mc2=15;$mc3=40;$mc4= 29;$mc5=25;
    
        
    $consulta1 = "SELECT n.id_unico, 
                         n.valor, 
                         n.empleado, 
                         n.periodo,
                         n.concepto,
                         n.aplicabilidad, 
                         c.codigo, 
                         c.descripcion,
                         p.codigointerno, 
                         p.fechainicio, 
                         p.fechafin, 
                         e.id_unico, 
                         e.codigointerno, 
                         e.tercero, 
                         t.id_unico, 
                         CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos), 
                         c.clase,
                         ca.salarioactual
                         FROM gn_novedad n 
                         LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                         LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                         LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                         LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                         LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                         LEFT JOIN gn_categoria ca ON ca.id_unico = tc.categoria
                         WHERE n.empleado = $idemp AND md5(n.periodo) = '$periodo' AND c.clase =1
                         ORDER BY c.id_unico"; 

    $nom = $mysqli->query($consulta1);
    while($filaN = mysqli_fetch_row($nom)){

        $valor  = $filaN[1];
        $codcon = $filaN[6];
        $descon = $filaN[7];
                 
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale($mc0,5,utf8_decode($codcon),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode($descon),0,0);
        
        $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = $idemp AND md5(periodo) = '$periodo' AND concepto = 7";
        $diast = $mysqli->query($consulta5);
        $dt = mysqli_fetch_row($diast);
        $pdf->cellfitscale($mc2,5,utf8_decode($dt[1]),0,0,'R');
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valor,2,'.',',')),0,0,'R');
        $pdf->cellfitscale($mc4,5,utf8_decode(''),0,0,'R');
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($filaN[17],2,'.',',')),0,0,'R'); 
        $pdf->Ln(3);
  
    }

    $consulta4 = "SELECT n.id_unico, 
                         n.valor, 
                         n.empleado, 
                         n.periodo,
                         n.concepto,
                         n.aplicabilidad, 
                         c.codigo, 
                         c.descripcion,
                         p.codigointerno, 
                         p.fechainicio, 
                         p.fechafin, 
                         e.id_unico, 
                         e.codigointerno, 
                         e.tercero, 
                         t.id_unico, 
                         CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos), 
                         c.clase,
                         ca.salarioactual
                         FROM gn_novedad n 
                         LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                         LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                         LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                         LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                         LEFT JOIN gn_tercero_categoria  tc ON e.id_unico = tc.empleado
                         LEFT JOIN gn_categoria ca ON ca.id_unico = tc.categoria  
                         WHERE n.empleado = $idemp AND md5(n.periodo) = '$periodo' AND c.clase= 2 AND n.concepto !=111
                         ORDER BY c.id_unico"; 

    $nomd = $mysqli->query($consulta4);
    while($filaD = mysqli_fetch_row($nomd)){

        $valor  = $filaD[1];
        $codcon = $filaD[6];
        $descon = $filaD[7];

        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale($mc0,5,utf8_decode($codcon),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode($descon),0,0);
        
        $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = $idemp AND md5(periodo) = '$periodo' AND concepto = 7";
        $diast = $mysqli->query($consulta5);
        $dt = mysqli_fetch_row($diast);
        
        $pdf->cellfitscale($mc2,5,utf8_decode($dt[1]),0,0,'R');
        $pdf->cellfitscale(40,5,utf8_decode(' '),0,0,'R');
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valor,2,'.',',')),0,0,'R');
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($filaD[17],2,'.',',')),0,0,'R');       

        $pdf->Ln(3);

    }
    $pdf->Ln(3);
     
    $pdf->Cell(188,0.5,'',1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial','B',10);

     
    $pdf->SetFont('Arial','B',10);
    $pdf->cellfitscale(130,5,utf8_decode('TOTAL DEVENGOS: '),0,0,'R');
    
    $consulta6 = "SELECT n.valor FROM gn_novedad n  WHERE n.empleado = $idemp AND n.concepto = 74 AND md5(n.periodo) = '$periodo'";
    $neto = $mysqli->query($consulta6);
    $nt = mysqli_fetch_row($neto);

    $pdf->cellfitscale(50,5,utf8_decode(number_format($nt[0],2,'.',',')),0,0,'R');

    $pdf->ln(5);

    $pdf->cellfitscale(130,5,utf8_decode('TOTAL DESCUENTOS: '),0,0,'R');
    
    $consulta6 = "SELECT n.valor FROM gn_novedad n  WHERE n.empleado = $idemp AND n.concepto = 98 AND md5(n.periodo) = '$periodo' ";
    $neto = $mysqli->query($consulta6);
    $nt = mysqli_fetch_row($neto);

    $pdf->cellfitscale(50,5,utf8_decode(number_format($nt[0],2,'.',',')),0,0,'R');

    $pdf->ln(5);

    $pdf->cellfitscale(130,5,utf8_decode('NETO A PAGAR: '),0,0,'R');
    
    $consulta6 = "SELECT n.valor FROM gn_novedad n  WHERE n.empleado = $idemp AND n.concepto = 102 AND md5(n.periodo) = '$periodo'";
    $neto = $mysqli->query($consulta6);
    $nt = mysqli_fetch_row($neto);
    
    $firma = "SELECT  e.id_unico, e.tercero, tr.id_unico, IF(CONCAT_WS(' ',
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
     c.nombre 
     FROM gn_empleado e LEFT JOIN  gf_tercero tr ON e.tercero = tr.id_unico LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico WHERE e.id_unico = '$idemp'";
    
    $fir = $mysqli->query($firma);
    $nfir = mysqli_num_rows($fir);
    $firm = mysqli_fetch_row($fir);
    $pdf->cellfitscale(50,5,utf8_decode(number_format($nt[0],2,'.',',')),0,0,'R');
    $pdf->Ln(35);
    
   
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(50,0.1,'',1);
    $pdf->cellfitscale(10,5,utf8_decode(''),0,0,'R');
    $pdf->Cell(50,0.1,'',1);
    $pdf->cellfitscale(10,5,utf8_decode(''),0,0,'R');
    $pdf->Cell(50,0.1,'',1);
    $pdf->Ln(2);
    $pdf->SetX(10);
    $pdf->cellfitscale(50,5,utf8_decode('ORDENADOR'),0,0,'C');
    $pdf->cellfitscale(70,5,utf8_decode('SECRETARIA DE HACIENDA'),0,0,'C');
    $pdf->cellfitscale(50,5,utf8_decode($firm[3]),0,0,'C');
    $pdf->Ln(3);

   
    
   if($numeroP < $numemp){

     $pdf->AddPage();


   }
    
}  
         

ob_end_clean();
$pdf->Output(0,'Informe_Terceros ('.date('d/m/Y').').pdf',0);  
?>

