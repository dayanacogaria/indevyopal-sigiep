<?php
#29/08/2017 --- 

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
if(!empty($_REQUEST['id_per_liq'])){

    $periodolq = $_REQUEST['id_per_liq'];
}
if(!empty($_POST['sltEmpleado'])){

    $empleado  = $_POST['sltEmpleado'];  
}

if(!empty($_POST['sltPeriodo'])){

    $periodo   = $_POST['sltPeriodo'];  
}
if(!empty($_POST['sltPeriodolq'])){

    $periodolq   = $_POST['sltPeriodolq'];  
}
//echo ' empleado '.$empleado.' periodo '.$periodo.' p liq '.$periodolq;
//$empleado  = 2;  
//$periodo   = 25;  
//$periodolq   = 30;  
$consulta = "SELECT         lower(t.razonsocial) as traz,
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
        $nomcomp = $fila['traz'];       
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
    global $sal;
    global $fec_ing;
    global $fec_ret;

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
    $this->Cell(170,5,utf8_decode(ucwords($nomcomp)),0,0,'C');
    // Salto de línea
   $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    $this->SetX(20);
    $this->Cell(170, 5,$tipodoc.': '.$numdoc,0,0,'C'); ;
    $this->Ln(5);
    $this->Cell(190,5,utf8_decode('LIQUIDACIÓN FINAL DE PRESTACIONES SOCIALES'),0,0,'C');
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
$pdf = new PDF('P','mm','mcarta');   



$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();

$pdf->SetFont('Arial','',8);

//consulta sql

$id = "SELECT id_unico FROM gn_empleado WHERE md5(id_unico) = '$empleado'";
$ide = $mysqli->query($id);
$idem = mysqli_fetch_row($ide);
$empleado = $idem[0];

#$cp      = $mysqli->query($sql);
#$codd    = 0;
#Asignación de anchos de columna
if(empty($empleado) || $empleado == 2){

     $retiro = "SELECT DISTINCT e.id_unico, vr.estado, vr.fecha "
            . "FROM gn_empleado e LEFT JOIN gn_novedad n ON n.empleado = e.id_unico "
            . "LEFT JOIN gn_periodo p ON n.periodo = p.id_unico LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico "
            . "WHERE vr.estado = 2 AND md5(p.id_unico) = '$periodo'";
    
    $reti = $mysqli->query($retiro);
    $nreti = mysqli_num_rows($reti);
    
    if($nreti > 0){
        
        $rt = mysqli_fetch_row($reti);
    }

   $consulta2 = "SELECT DISTINCT  e.id_unico, 
                    e.tercero, 
                    CONCAT_WS(' ', t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
                    tc.categoria, 
                    c.id_unico, 
                    c.nombre, 
                    c.salarioactual,
                    gg.nombre,
                        c.salarioactual,
                        (SELECT ingreso.fecha FROM gn_empleado e_ing 
                          LEFT JOIN gn_vinculacion_retiro ingreso on ingreso.empleado=e_ing.id_unico 
                          WHERE e_ing.id_unico = e.id_unico and ingreso.estado=1 order by ingreso.fecha desc LIMIT 1 )
                        fecha_ingreso,
                        (SELECT retiro.fecha FROM gn_empleado e_ret 
                          LEFT JOIN gn_vinculacion_retiro retiro on retiro.empleado=e_ret.id_unico 
                          WHERE e_ret.id_unico = e.id_unico and retiro.estado=2 order by retiro.fecha desc LIMIT 1 )
                        fecha_retiro,
                        (SELECT n.valor FROM gn_novedad n  
                            where  md5(n.periodo)='$periodolq' and n.concepto=7 and n.empleado= e.id_unico) as dias_trab,
                        (SELECT n.fecha FROM gn_novedad n  
                            where  md5(n.periodo)='$periodolq' and n.concepto=7 and n.empleado= e.id_unico) as fecha_nov,
                    t.numeroidentificacion
  
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t on e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico 
            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico
            WHERE e.id_unico != 2 and md5(n.periodo)='$periodo'
            ORDER BY `e`.`id_unico` ASC";
}else{

    $consulta2 = "SELECT distinct  e.id_unico, 
                        t.id_unico, 
                        CONCAT_WS(' ',t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        c.id_unico,
                        tc.categoria,
                        c.nombre, 
                        tc.categoria,
                        gp.nombre,
                        c.salarioactual,
                        (SELECT ingreso.fecha FROM gn_empleado e_ing 
                          LEFT JOIN gn_vinculacion_retiro ingreso on ingreso.empleado=e_ing.id_unico 
                          WHERE e_ing.id_unico = e.id_unico and ingreso.estado=1 order by ingreso.fecha desc LIMIT 1 )
                        fecha_ingreso,
                        (SELECT retiro.fecha FROM gn_empleado e_ret 
                          LEFT JOIN gn_vinculacion_retiro retiro on retiro.empleado=e_ret.id_unico 
                          WHERE e_ret.id_unico = e.id_unico and retiro.estado=2 order by retiro.fecha desc LIMIT 1 )
                        fecha_retiro,
                        (SELECT n.valor FROM gn_novedad n  
                            where  md5(n.periodo)='$periodolq' and n.concepto=7 and n.empleado= e.id_unico) as dias_trab,
                            (SELECT n.fecha FROM gn_novedad n  
                            where  md5(n.periodo)='$periodolq' and n.concepto=7 and n.empleado= e.id_unico) as fecha_nov,
                        t.numeroidentificacion, 
                        (SELECT date_format(ingreso.fecha, '%d/%m/%Y') FROM gn_empleado e_ing 
                          LEFT JOIN gn_vinculacion_retiro ingreso on ingreso.empleado=e_ing.id_unico 
                          WHERE e_ing.id_unico = e.id_unico and ingreso.estado=1 order by ingreso.fecha desc LIMIT 1 )
                        fecha_ingreso,
                        (SELECT date_format(retiro.fecha, '%d/%m/%Y') FROM gn_empleado e_ret 
                          LEFT JOIN gn_vinculacion_retiro retiro on retiro.empleado=e_ret.id_unico 
                          WHERE e_ret.id_unico = e.id_unico and retiro.estado=2 order by retiro.fecha desc LIMIT 1 )
                        fecha_retiro 
                        FROM gn_empleado e 
                        LEFT JOIN gn_novedad n on n.empleado=e.id_unico
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                        LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico
                        LEFT JOIN gn_grupo_gestion gp ON e.grupogestion = gp.id_unico
                        WHERE e.id_unico = '$empleado' and md5(n.periodo)='$periodo'
                        ORDER BY e.id_unico";    
}    
echo $consulta2;                   
$empl = $mysqli->query($consulta2);
$numemp = mysqli_num_rows($empl);

//echo ' peropdp liqui   '.$consulta2;

$consulta3 = "SELECT id_unico, codigointerno, fechainicio, fechafin, date_format(fechainicio, '%d/%m/%Y'), 
date_format(fechafin, '%d/%m/%Y') FROM gn_periodo WHERE md5(id_unico) = '$periodo'";
$perio = $mysqli->query($consulta3);
$perN  = mysqli_fetch_row($perio);

while($fila1 = mysqli_fetch_row($empl)){

    $idemp = $fila1[0];
    //$per  = utf8_decode($fila1[1]);
    $emp  = $fila1[2];
    $sal = $fila1[8];
    $fec_ing = $fila1[9];
    $fec_ret = $fila1[10];
    $dias_tr = $fila1[11];
    $fecha_nov_lq = $fila1[12];
    $fecha_nov_lq = explode("-", $fecha_nov_lq);
    $y_nov = $fecha_nov_lq[0];
    $m_nov = $fecha_nov_lq[1];
    $fec_ing2 = $fila1[14];
    $fec_ret2 = $fila1[15];

    if($m_nov==1){
        $mes_liq='ENERO DEL '.$y_nov;
    }
    else if($m_nov==2){
        $mes_liq='FEBRERO DEL '.$y_nov;
    }
    else if($m_nov==3){
        $mes_liq='MARZO DEL '.$y_nov;
    }
    else if($m_nov==4){
        $mes_liq='ABRIL DEL '.$y_nov;
    }
    else if($m_nov==5){
        $mes_liq='MAYO DEL '.$y_nov;
    }
    else if($m_nov==6){
        $mes_liq='JUNIO DEL '.$y_nov;
    }
    else if($m_nov==7){
        $mes_liq='JULIO DEL '.$y_nov;
    }
    else if($m_nov==8){
        $mes_liq='AGOSTO DEL '.$y_nov;
    }
    else if($m_nov==9){
        $mes_liq='SEPTIEMBRE DEL '.$y_nov;
    }
    else if($m_nov==10){
        $mes_liq='OCTUBRE DEL '.$y_nov;
    }
    else if($m_nov==11){
        $mes_liq='NOVIEMBRE DEL '.$y_nov;
    }
    else if($m_nov==12){
        $mes_liq='DICIEMBRE DEL '.$y_nov;
    }

    //$codi = utf8_decode($fila1[0]);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(210,5,utf8_decode('COMPROBANTE DE NOMINA'),0,0,'C');
    $pdf->Ln(10);
    $pdf->Cell(25,5,utf8_decode('PERIODO:'),0,0,'L');
    $pdf->Cell(45,5,utf8_decode($perN[1]),0,0,'L');
    $pdf->Cell(30,5,utf8_decode('NOMBRE:'),0,0,'L');
    $pdf->Cell(60,5,utf8_decode($emp .' - '.$fila1[13]),0,0,'L');
    $pdf->Ln(4);
    $pdf->Cell(25,5,utf8_decode('FECHA INICIAL:'),0,0,'L');
    $pdf->Cell(45,5,utf8_decode($perN[4]),0,0,'L');
    $pdf->Cell(30,5,utf8_decode('CARGO:'),0,0,'L');
    $pdf->Cell(60,5,utf8_decode($fila1[5]),0,0,'L');
    $pdf->Ln(4);
    $pdf->Cell(25,5,utf8_decode('FECHA FINAL:'),0,0,'L');
    $pdf->Cell(45,5,utf8_decode($perN[5]),0,0,'L');
    $pdf->Cell(30,5,utf8_decode('GRUPO GESTIÓN:'),0,0,'L');
    $pdf->Cell(60,5,utf8_decode($fila1[7]),0,0,'L');
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
                         CONCAT_WS(' ',t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos), 
                         c.clase,
                         ca.salarioactual
                         FROM gn_novedad n 
                         LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                         LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                         LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                         LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                         LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                         LEFT JOIN gn_categoria ca ON ca.id_unico = tc.categoria
                         WHERE n.empleado = '$idemp' AND md5(n.periodo) = '$periodo' AND c.clase= 1 AND n.concepto !=7 AND n.valor > 0  
                         ORDER BY c.id_unico"; 

    $nom = $mysqli->query($consulta1);
    while($filaN = mysqli_fetch_row($nom)){
        $pdf->SetFont('Arial','',6);
        $valor  = $filaN[1];
        $codcon = $filaN[6];
        $descon = $filaN[7];

        
        $pdf->cellfitscale($mc0,5,utf8_decode($codcon),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode($descon),0,0);
        
        $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = '$idemp' AND md5(periodo) = '$periodo' AND concepto = 7";
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
                         CONCAT_WS(' ',t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos), 
                         c.clase,
                         ca.salarioactual
                         FROM gn_novedad n 
                         LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                         LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                         LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                         LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                         LEFT JOIN gn_tercero_categoria  tc ON e.id_unico = tc.empleado
                         LEFT JOIN gn_categoria ca ON ca.id_unico = tc.categoria  
                         WHERE n.empleado = '$idemp' AND md5(n.periodo) = '$periodo' AND c.clase= 2 AND n.valor > 0
                         ORDER BY c.id_unico"; 

    $nomd = $mysqli->query($consulta4);
    while($filaD = mysqli_fetch_row($nomd)){
         $pdf->SetFont('Arial','',6);
        $valor  = $filaD[1];
        $codcon = $filaD[6];
        $descon = $filaD[7];
       
        $pdf->cellfitscale($mc0,5,utf8_decode($codcon),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode($descon),0,0);
        
        $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = '$idemp' AND md5(periodo) = '$periodo' AND concepto = 7";
        $diast = $mysqli->query($consulta5);
        $dt = mysqli_fetch_row($diast);
        
        $pdf->cellfitscale($mc2,5,utf8_decode($dt[1]),0,0,'R');
        $pdf->cellfitscale(40,5,utf8_decode(' '),0,0,'R');
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valor,2,'.',',')),0,0,'R');
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($filaD[17],2,'.',',')),0,0,'R');       

        $pdf->Ln(3);

    }
    $pdf->Ln(1);
     
    $pdf->Cell(188,0.5,'',1);
    $pdf->Ln(1);
    $pdf->SetFont('Arial','B',8);

     $EntidadAfi = "SELECT   a.id_unico,
                            a.empleado,
                            a.tercero,
                            t.id_unico,
                            t.razonsocial,
                            a.tipo
                            FROM gn_afiliacion a 
                            LEFT JOIN gf_tercero t ON a.tercero = t.id_unico
                            WHERE a.empleado = '$idemp'
                            ORDER BY a.tipo";
    $afient = $mysqli->query($EntidadAfi);
    $nafi = mysqli_num_rows($afient);
    $x =$pdf->GetX();
    $y =$pdf->GetY();
    if($nafi >0){
        while($afiliacion = mysqli_fetch_row($afient)){

            
            if($afiliacion[5] == 1){
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(15,5,utf8_decode('SALUD: '),0,0,'R');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(60,5,utf8_decode($afiliacion[4]),0,0,'L');
                $pdf->ln(3);
            }elseif($afiliacion[5] == 2){
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(15,5,utf8_decode('PENSION: '),0,0,'R');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(60,5,utf8_decode($afiliacion[4]),0,0,'L');
                $pdf->ln(3);
            }elseif($afiliacion[5] == 3){
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(15,5,utf8_decode('CESANTIAS: '),0,0,'R');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(60,5,utf8_decode($afiliacion[4]),0,0,'L');
                $pdf->ln(3);
            }elseif($afiliacion[5] == 4){
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(15,5,utf8_decode('ARL: '),0,0,'R');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(60,5,utf8_decode($afiliacion[4]),0,0,'L');
                $pdf->ln(3);
            }elseif($afiliacion[5] == 5){
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(15,5,utf8_decode('CAJA C.: '),0,0,'R');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(60,5,utf8_decode($afiliacion[4]),0,0,'l');
            }
        } 
    }

    //$pdf->SetX($x + 50);
    $pdf->SetY($y);
    $pdf->SetFont('Arial','B',8);
    $pdf->cellfitscale(135,5,utf8_decode('TOTAL DEVENGOS: '),0,0,'R');
    
    $consulta6 = "SELECT n.valor FROM gn_novedad n  WHERE n.empleado = '$idemp' AND n.concepto = 74 AND md5(n.periodo) = '$periodo'";
    $neto = $mysqli->query($consulta6);
    $nt = mysqli_fetch_row($neto);

    $pdf->cellfitscale(50,5,utf8_decode(number_format($nt[0],2,'.',',')),0,0,'R');

    $pdf->ln(3);

    $pdf->cellfitscale(135,5,utf8_decode('TOTAL DESCUENTOS: '),0,0,'R');
    
    $consulta6 = "SELECT n.valor FROM gn_novedad n  WHERE n.empleado = '$idemp' AND n.concepto = 98 AND md5(n.periodo) = '$periodo' ";
    $neto = $mysqli->query($consulta6);
    $nt = mysqli_fetch_row($neto);

    $pdf->cellfitscale(50,5,utf8_decode(number_format($nt[0],2,'.',',')),0,0,'R');

    $pdf->ln(3);

    $pdf->cellfitscale(135,5,utf8_decode('NETO A PAGAR: '),0,0,'R');
    
    $consulta6 = "SELECT n.valor FROM gn_novedad n  WHERE n.empleado = $idemp AND n.concepto = 102 AND md5(n.periodo) = '$periodo'";
    $neto = $mysqli->query($consulta6);
    $nt = mysqli_fetch_row($neto);
    
    

    $firma = "SELECT e.id_unico, IF(CONCAT_WS(' ',
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
     tr.numeroidentificacion
     FROM gn_empleado e LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico  WHERE e.id_unico = '$idemp'";
    
    $fir = $mysqli->query($firma);
    $nfir = mysqli_fetch_row($fir);

    $pdf->cellfitscale(50,5,utf8_decode(number_format($nt[0],2,'.',',')),0,0,'R');
    $pdf->Ln(20);
    
$firmas = "SELECT   c.nombre, 
                        rd.orden,
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
                        tr.apellidodos)) AS NOMBRE 

                FROM gf_responsable_documento rd 
                LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
                LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
                LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
                LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                WHERE td.nombre = 'Volante Pago'
                ORDER BY rd.orden ASC";

    $fi = $mysqli->query($firmas);
    

    $pdf->SetFont('Arial','B',8);
    $y =$pdf->GetY();
    $x = $pdf->GetX();
    while($F = mysqli_fetch_row($fi)){
            
            
            //$pdf->Cell(10,0.1,'',0);
            
            
            if($F[1] ==  1){
                 $pdf->Cell(60,0.1,'',1);  
                 $pdf->Ln(2); 
                 $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                $pdf->Ln(3);
                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L'); 
            }else{
                $pdf->SetXY($x+70, $y); 

                $pdf->Cell(60,0.1,'',1); 
                $pdf->Ln(2); 
                $y1 =$pdf->GetY();
                $pdf->SetXY($x+70, $y1); 
                $pdf->cellfitscale(50,5,utf8_decode($F[2]),0,0,'L');
                $pdf->Ln(3); 
                $y2 =$pdf->GetY();
                $pdf->SetXY($x+70, $y2); 
                $pdf->cellfitscale(50,5,utf8_decode($F[0]),0,0,'L');  
            }
            
            
            
            #$pdf->cellfitscale(30,5,utf8_decode(''),0,0,'R');

    }    
        $pdf->SetXY($x+140, $y); 
        $pdf->Cell(60,0.1,'',1);
        $pdf->Ln(2); 
        $y1 =$pdf->GetY();
        $pdf->SetXY($x+140, $y1);      
        $pdf->cellfitscale(50,5,utf8_decode($nfir[1]),0,0,'R');  
 
     $pdf->AddPage();

            //comprobante de liquidacion final
if(empty($dias_tr)){

}else{
     $pdf->SetFont('Arial','B',8);
    $pdf->Cell(210,5,utf8_decode('COMPROBANTE DE LIQUIDACIÓN FINAL'),0,0,'C');
    $pdf->Ln(8);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(25,5,utf8_decode('PERIODO:  '),0,0,'L');
    $pdf->Cell(45,5,utf8_decode($mes_liq),0,0,'L');
    $pdf->Cell(30,5,utf8_decode('NOMBRE:'),0,0,'L');
    $pdf->Cell(60,5,utf8_decode($emp .' - '.$fila1[13]),0,0,'L');
    $pdf->Ln(4);
    $pdf->Cell(25,5,utf8_decode('FECHA INGRESO:'),0,0,'L');
    $pdf->Cell(45,5,utf8_decode($fec_ing2),0,0,'L');
    $pdf->Cell(30,5,utf8_decode('SALARIO MENSUAL:'),0,0,'L');
    $pdf->Cell(60,5,utf8_decode(number_format($sal,2,'.',',')),0,0,'L');
    $pdf->Ln(4);
    $pdf->Cell(25,5,utf8_decode('FECHA RETIRO:'),0,0,'L');
    $pdf->Cell(45,5,utf8_decode($fec_ret2),0,0,'L');
    $pdf->Cell(30,5,utf8_decode('DIAS TRABAJADOS:'),0,0,'L');
    $pdf->Cell(60,5,utf8_decode($dias_tr),0,0,'L');
    $pdf->Ln(8);
    
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
    $pdf->Cell(70,5, utf8_decode('Concepto'),1,0,'C');
    $pdf->Cell(15,5, utf8_decode('Dias'),1,0,'C');
    $pdf->Cell(40,5,utf8_decode('Base'),1,0,'C');
    $pdf->Cell(40,5,utf8_decode('Valor'),1,0,'C');
    
    $pdf->Ln(5);
    $c0=20;$c1=70;$c2=15;$c3=40;$c4= 40;

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
                         CONCAT_WS(' ',t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos), 
                         c.clase,
                         ca.salarioactual
                         FROM gn_novedad n 
                         LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                         LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                         LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                         LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                         LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                         LEFT JOIN gn_categoria ca ON ca.id_unico = tc.categoria
                         WHERE n.empleado = '$idemp' AND md5(n.periodo) = '$periodolq'  AND n.concepto !=7 AND n.valor > 0 AND n.concepto !=443  AND n.concepto !=444  AND n.concepto !=445
                         ORDER BY c.id_unico"; 

    $nom = $mysqli->query($consulta1);
    while($filaN = mysqli_fetch_row($nom)){
        $pdf->SetFont('Arial','',6);
        $valor  = $filaN[1];
        $codcon = $filaN[6];
        $descon = $filaN[7];

        
        $pdf->cellfitscale($mc0,5,utf8_decode($codcon),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode($descon),0,0);
        
        //sacar los dias
        if($codcon=='C12'){
            $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = '$idemp' AND md5(periodo) = '$periodolq' AND concepto = 443";
        }
        else if($codcon=='C13'){
            $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = '$idemp' AND md5(periodo) = '$periodolq' AND concepto = 444";
        }
        else if($codcon=='C14' || $codcon=='C15'){
            $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = '$idemp' AND md5(periodo) = '$periodolq' AND concepto = 445";
        }

        
        $diast = $mysqli->query($consulta5);
        $dt = mysqli_fetch_row($diast);
        $pdf->cellfitscale($mc2,5,utf8_decode($dt[1]),0,0,'R');
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format(0,2,'.',',')),0,0,'R');
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valor,2,'.',',')),0,0,'R'); 
        $pdf->Ln(3);
        $pdf->Ln(1);
      
    }
    $pdf->Cell(188,0.5,'',1);
    $pdf->Ln(1);
    $pdf->SetFont('Arial','B',8);
    $x =$pdf->GetX();
    $y =$pdf->GetY();
    $pdf->SetX($x + 45);
    $pdf->SetY($y);
    $pdf->SetFont('Arial','B',8);
    $pdf->cellfitscale(130,5,utf8_decode('TOTAL DEVENGOS: '),0,0,'R');
    
    $consulta6 = "SELECT sum(n.valor) FROM gn_novedad n 
      left join gn_concepto c on c.id_unico = n.concepto 
     WHERE n.empleado = '$idemp' AND n.concepto != 7 AND md5(n.periodo) = '$periodolq'  and c.clase =1";
    $neto = $mysqli->query($consulta6);
    $nt = mysqli_fetch_row($neto);

    $pdf->cellfitscale(50,5,utf8_decode(number_format($nt[0],2,'.',',')),0,0,'R');
    $pdf->ln(3);
    $pdf->cellfitscale(130,5,utf8_decode('TOTAL DESCUENTOS: '),0,0,'R');    
    $pdf->cellfitscale(50,5,utf8_decode(number_format(0,2,'.',',')),0,0,'R');
    $pdf->ln(3);
    $pdf->cellfitscale(130,5,utf8_decode('NETO A PAGAR: '),0,0,'R');    
    $pdf->cellfitscale(50,5,utf8_decode(number_format($nt[0],2,'.',',')),0,0,'R');

    $pdf->cellfitscale(50,5,utf8_decode(number_format($nt[0],2,'.',',')),0,0,'R');
    $pdf->Ln(15);
    
    $firmas = "SELECT   c.nombre, 
                        rd.orden,
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
                        tr.apellidodos)) AS NOMBRE 

                FROM gf_responsable_documento rd 
                LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
                LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
                LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
                LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                WHERE td.nombre = 'Volante Pago'
                ORDER BY rd.orden ASC";

    $fi = $mysqli->query($firmas);
    

    $pdf->SetFont('Arial','B',8);
    $y =$pdf->GetY();
    $x = $pdf->GetX();
    $pdf->Cell(60,0.1,'',1);  
    $pdf->Ln(2); 
    $pdf->cellfitscale(60,5,utf8_decode('CONTABILIDAD'),0,0,'C');
    $pdf->Ln(3);
        
    $pdf->SetXY($x+70, $y); 
        $pdf->Cell(60,0.1,'',1);
        $pdf->Ln(2); 
        $y1 =$pdf->GetY();
        $pdf->SetXY($x+70, $y1);      
        $pdf->cellfitscale(50,5,utf8_decode('GERENCIA'),0,0,'C');  

    $pdf->SetXY($x+140, $y); 
        $pdf->Cell(60,0.1,'',1);
        $pdf->Ln(2); 
        $y1 =$pdf->GetY();
        $pdf->SetXY($x+145, $y1);      
        $pdf->cellfitscale(50,5,utf8_decode('NOMINA'),0,0,'C');  


        $pdf->Ln(15);
        $pdf->SetFont('Arial','B',6);        
        $pdf->Cell(60,0.1,'',1);  
        $pdf->Ln(1); 
        $pdf->cellfitscale(60,5,utf8_decode('FIMA DEL TRABAJADOR'),0,0,'L');
        $pdf->Ln(3);
        $pdf->cellfitscale(50,5,utf8_decode('NOMBRE: '.$nfir[1]),0,0,'L');  
        $pdf->Ln(3);
        $pdf->cellfitscale(50,5,utf8_decode('CC: '.number_format($nfir[2],2,'.',',')),0,0,'L'); 

        $pdf->SetXY($x+70, $y+13); 
        $pdf->SetFont('Arial','',6);   
        $pdf->MultiCell(130, 3, utf8_decode('CONSTANCIA:  Se hace constar expresamente  lo siguiente: Que '.mb_strtoupper($nomcomp).' ha incorporado en la liquidacion la totalidad de valores correspondientes a salarios, horas extras,'.
            ' recargos por trabajo nocturno, descansos remunerados, cesantias, vacaciones, auxilios de enfermedad,'.
            ' accidentes de trabajo, primas, calzado, overoles, auxilio de transporte y en general, todo concepto '.
            'relacionado  con salarios, prestaciones o indemnizaciones que tengan por causa el contrato de trabajo'.'  que se ha quedado extinguido.'), 0, 'J');
        
        //$pdf->AddPage();
    }
    
    
}  
 

        
        

ob_end_clean();
$pdf->Output(0,'Volante_Liquidacion ('.date('d/m/Y').').pdf',0);  
?>