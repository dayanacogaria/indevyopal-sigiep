<?php
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

$grupog = $_POST['sltGrupoG'];
$periodo  = $_POST['sltPeriodo'];
$tipof  = $_POST['sltTipoF'];  

/*$grupog = 7;
$periodo = "";
$tipof = "";
*/


if(empty($grupog) || $grupog == ""){

  $GRUP = "Todos";

}else{

  $G = "SELECT id_unico, nombre FROM gn_grupo_gestion WHERE id_unico = $grupog";
  $GG = $mysqli->query($G);
  $GR = mysqli_fetch_row($GG);

  $GRUP = $GR[1];
}

if(empty($periodo) || $periodo == ""){

  $PER = "Todos";
  $FI = "";
  $FF = "";

}else{

 $P = "SELECT id_unico, codigointerno , fechainicio, fechafin FROM gn_periodo WHERE id_unico = $periodo";
  $PP = $mysqli->query($P);
  $PERI = mysqli_fetch_row($PP);

  $PER = $PERI[1];
  

  $fecha_div = explode("-", $PERI[2]);
  $anion = $fecha_div[0];
  $mesn = $fecha_div[1];
  $dian = $fecha_div[2];
  $FI = $dian.'/'.$mesn.'/'.$anion;

  $fecha_div2 = explode("-", $PERI[3]);
  $anion1 = $fecha_div2[0];
  $mesn1 = $fecha_div2[1];
  $dian1 = $fecha_div2[2];
  $FF = $dian1.'/'.$mesn1.'/'.$anion1;

}

if(empty($tipof)|| $tipof ==""){

  $TF = "Todos";

}else{

  $TIF = "SELECT id_unico , nombre FROM gn_tipo_fondo WHERE id_unico = $tipof";
  $FT = $mysqli->query($TIF);
  $TIPF = mysqli_fetch_row($FT);

  $TF = $TIPF[1];
} 

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
    global $CO;
    global $TF;
    global $PER;
    global $FI;
    global $FF;
    global $GRUP;
  
    

    $numeroP = $this->PageNo();

    if($ruta != '')
    {
      $this->Image('../'.$ruta,20,8,15);
    } 
    // Logo
    //$this->Image('logo_pb.png',10,8,33);
    //Arial bold 10
    $this->SetFont('Arial','B',12);
    
        // Título
    
    $this->SetX(20);
    $this->Cell(170,5,utf8_decode(ucwords($nomcomp)),0,0,'C');
    // Salto de línea
   $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    $this->SetX(20);
    $this->Cell(170, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
    $this->Ln(5);
     $this->SetFont('Arial','B',12);
    $this->Cell(190,5,utf8_decode('APORTES Y PARAFISCALES'),0,0,'C');
    // Salto de línea
    $this->Ln(3);
    $this->SetFont('Arial','B',8);
    $this->SetX(0);

    $this->SetFont('Arial','B',10);
    $this->Cell(37,18,utf8_decode('PERIODO:'),0,0,'C');
    $this->SetFont('Arial', '', 10);
    $this->Cell(12,18,utf8_decode($PER),0,0,'C');
    $this->Cell(25.5,18,utf8_decode(''),0,0,'C');
    $this->SetFont('Arial','B',10);
    $this->Cell(40,18,utf8_decode('GRUPO GESTIÓN:'),0,0,'L');
    $this->SetFont('Arial','',8);
    $this->Cell(15,18,utf8_decode(''),0,0,'L');
    $this->Cell(15,18,utf8_decode($GRUP),0,0,'C');
    $this->Ln(4);
    $this->SetX(11);
    $this->SetFont('Arial', 'B',10);
    $this->Cell(18,18,utf8_decode('Fecha Inicial:'),0,0,'C');
    $this->SetFont('Arial','',10);
    $this->Cell(32,18,utf8_decode($FI),0,0,'C');
    $this->Cell(6,18,utf8_decode(''),0,0,'C');
    $this->SetFont('Arial', 'B',10);
    $this->Cell(43,18,utf8_decode('TIPO FONDO:'),0,0,'C');
    $this->SetFont('Arial','',9);
    $this->Cell(5,18,utf8_decode(''),0,0,'C');
    $this->Cell(34,18,utf8_decode($TF),0,0,'C');
    $this->Ln(4);
    $this->SetX(13);
    $this->SetFont('Arial', 'B',10);
    $this->Cell(13,19,utf8_decode('Fecha Final:'),0,0,'C');
    $this->SetFont('Arial', '', 10); 
    $this->Cell(3,18,utf8_decode(''),0,0,'L');
    $this->Cell(32,18,utf8_decode($FF),0,0,'C');
    $this->Ln(20);
    
    
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
                /*$this->SetX(10);
                $this->Cell(30,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                $this->Cell(10,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');*/
            }
        }


// Creación del objeto de la clase heredada
$pdf = new PDF('P','mm','letter');   



$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();

$pdf->SetFont('Arial','',8);


//consulta sql

#$cp      = $mysqli->query($sql);
#$codd    = 0;
#Asignación de anchos de columna

# si todos los campos del formulario vienen vacios

if(empty($grupog) && empty($periodo) && empty($tipof)){

    $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
    $valor = $mysqli->query($salud);

    $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
    $Fond = $mysqli->query($fondo); 

    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(6);

    $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
    $pdf->Ln(12);
     
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
    $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
    $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
    $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
    $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
    $pdf->Ln(8);

    $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

    # ciclo de todas las entidades de salud
    while($SAL = mysqli_fetch_row($valor)){

        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale($mc0,5,utf8_decode($SAL[0]),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode($SAL[1]),0,0,'C');

        #calcula cuantos empleados esta afiliadso a cada entidad de salud
        $canTemp = "SELECT  COUNT(DISTINCT e.id_unico), 
                        t.razonsocial 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' ";
        
        $Fsal = $mysqli->query($canTemp);
        $CanE = mysqli_fetch_row($Fsal);
        $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');
      
        #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
        $vemple = "SELECT   c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion = '$SAL[0]' GROUP BY t.numeroidentificacion";
      
        $vaem = $mysqli->query($vemple);
        $valE = mysqli_fetch_row($vaem);
            
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

        #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
        $vpat = "SELECT     c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_un|ico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' GROUP BY t.numeroidentificacion";

        $vapa = $mysqli->query($vpat);
        $valP = mysqli_fetch_row($vapa);
        
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

        #calcula el total de los dos aportes y los agrupa por cada entidad de salud 
        $VTot = "SELECT SUM(n.valor),t.razonsocial  FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 1  AND t.numeroidentificacion = '$SAL[0]'";
      
        $valT = $mysqli->query($VTot);
        $TOT = mysqli_fetch_row($valT);
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

        $pdf->Ln(5);
    } #FINALIZA EL CICLO DEL CARGUE DE DATOS DEL FONOD DE SALUD Y CALCULA LOS TOTALES

    $pdf->Ln(3);
    $pdf->Cell(170,0.5,'',1);
    $pdf->Ln(4);
    $pdf->SetFont('Arial','B',8);
    $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
    $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

    $canTemp1 = "SELECT  COUNT(DISTINCT n.empleado) 
                        
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 2 AND n.empleado !=2 ";
    $Fsal1 = $mysqli->query($canTemp1);
    $CanE1 = mysqli_fetch_row($Fsal1);
    $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');


    $ToEM = "SELECT     c.id_unico, 
                        c.descripcion, 
                        t.numeroidentificacion, 
                        t.razonsocial, 
                        SUM(n.valor), 
                        c.clase 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
            WHERE c.tipofondo = 1 AND c.clase = 2  ";
    
    $VToEM = $mysqli->query($ToEM);
    $VAEMP = mysqli_fetch_row($VToEM);
    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[4],2,'.',',')),0,0,'R'); 

    $vpat1 = "SELECT    c.id_unico, 
                        c.descripcion, 
                        t.numeroidentificacion, 
                        t.razonsocial, 
                        SUM(n.valor), 
                        c.clase 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
            WHERE c.tipofondo = 1  AND c.clase = 7 ";

    $vapa1 = $mysqli->query($vpat1);
    $valP1 = mysqli_fetch_row($vapa1);
        
    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

    $VTot1 = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
            WHERE c.tipofondo = 1 ";
      
    $valT1 = $mysqli->query($VTot1);
    $TOT1 = mysqli_fetch_row($valT1);
    $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');    

    ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////

    $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
    $valor1 = $mysqli->query($pension);

    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(6);

    $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
    $pdf->Ln(12);
     
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
    $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
    $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
    $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
    $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    $pdf->Cell(30,5, utf8_decode('Fondo Solid'),1,0,'C');
    $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
    $pdf->Ln(8);

    $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;$mc6=30;

    while($PEN = mysqli_fetch_row($valor1)){

        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale($mc0,5,utf8_decode($PEN[0]),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode($PEN[1]),0,0,'C');

        $canTemp = "SELECT  COUNT(e.id_unico) 
                              FROM gn_empleado e 
                              LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                              LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                              WHERE t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2";
        
        $Fsal = $mysqli->query($canTemp);
        $CanE = mysqli_fetch_row($Fsal);
        $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

        $vemple = "SELECT   c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND t.numeroidentificacion = '$PEN[0]' GROUP BY t.numeroidentificacion";
     
        $vaem = $mysqli->query($vemple);
        $valE = mysqli_fetch_row($vaem);
            
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

        $vpat = "SELECT     c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' GROUP BY t.numeroidentificacion";

        $vapa = $mysqli->query($vpat);
        $valP = mysqli_fetch_row($vapa);
        
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

        $Fsol = "SELECT     c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND t.numeroidentificacion = '$PEN[0]' GROUP BY t.numeroidentificacion";
     
        $foso = $mysqli->query($Fsol);
        $SolF = mysqli_fetch_row($foso);
            
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[4],2,'.',',')),0,0,'R');

        $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND t.numeroidentificacion = '$PEN[0]'";
      
        $valT = $mysqli->query($VTot);
        $TOT = mysqli_fetch_row($valT);
        $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

        $pdf->Ln(5);
    }

    $pdf->Ln(3);
    $pdf->Cell(200,0.5,'',1);
    $pdf->Ln(4);
    $pdf->SetFont('Arial','B',8);
    $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
    $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C'); 

    $canTemp1 = "SELECT  COUNT(DISTINCT e.id_unico) 
                              FROM gn_empleado e 
                              LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                              LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                              WHERE  af.tipo = 2";
    
    $Fsal1 = $mysqli->query($canTemp1);
    $CanE1 = mysqli_fetch_row($Fsal1);
    $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

    $vemple1 = "SELECT  c.id_unico, 
                        c.descripcion, 
                        t.numeroidentificacion, 
                        t.razonsocial, 
                        SUM(n.valor), 
                        c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  ";
     
    $vaem1 = $mysqli->query($vemple1);
    $valE1 = mysqli_fetch_row($vaem1);
            
    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[4],2,'.',',')),0,0,'R');

    $vpat1 = "SELECT    c.id_unico, 
                        c.descripcion, 
                        t.numeroidentificacion, 
                        t.razonsocial, 
                        SUM(n.valor), 
                        c.clase 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
            WHERE c.tipofondo = 2  AND c.clase = 7 ";

    $vapa1 = $mysqli->query($vpat1);
    $valP1 = mysqli_fetch_row($vapa1);
        
    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

    $Fsol1 = "SELECT    c.id_unico, 
                        c.descripcion, 
                        t.numeroidentificacion, 
                        t.razonsocial, 
                        SUM(n.valor), 
                        c.clase 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  ";
     
    $foso1 = $mysqli->query($Fsol1);
    $SolF1 = mysqli_fetch_row($foso1);
            
    $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[4],2,'.',',')),0,0,'R');
  
    $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
              LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
              LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
              WHERE c.tipofondo = 2  ";

    $VAl12  = $mysqli->query($VTot12);
    $TOVAL12 = mysqli_fetch_row($VAl12);
    $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');


    ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
    $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
    $valor2 = $mysqli->query($arl);

    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(6);

    $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
    $pdf->Ln(12);
     
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
    $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
    $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
    $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
    $pdf->Ln(8);

    $mc0=20;$mc1=40;$mc2=20;$mc3=30;

    while($AR = mysqli_fetch_row($valor2)){

        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale($mc0,5,utf8_decode($AR[0]),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode($AR[1]),0,0,'C');

        $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_empleado e 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE t.numeroidentificacion = '$AR[0]' AND af.tipo = 4";
        
        $Fsal = $mysqli->query($canTemp);
        $CanE = mysqli_fetch_row($Fsal);
        $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

        $vpat = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363";
        $vapa = $mysqli->query($vpat);
        $valP = mysqli_fetch_row($vapa);
        
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

        $pdf->Ln(5);
    }

    $pdf->Ln(3);
    $pdf->Cell(110,0.5,'',1);
    $pdf->Ln(4);
    $pdf->SetFont('Arial','B',8);
    $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
    $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
    $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

    $vpat1 = "SELECT sum(valor) FROM `gn_novedad` WHERE concepto =363";

    $vapa1 = $mysqli->query($vpat1);
    $valP1 = mysqli_fetch_row($vapa1);
        
    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R'); 

    ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

    $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.tipoentidad = 4";
    $valor2 = $mysqli->query($paraf);
    
    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(6);

    $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
    $pdf->Ln(12);
     
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
    $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
    $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
    $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
    $pdf->Ln(8);

    $mc0=20;$mc1=40;$mc2=20;$mc3=30;

    while($PAR = mysqli_fetch_row($valor2)){

        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale($mc0,5,utf8_decode($PAR[2]),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode($PAR[1]),0,0,'C');

        $canTemp = "SELECT COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                    WHERE t.id_unico = '$PAR[0]'";
      
        $Fsal = $mysqli->query($canTemp);
        $CanE = mysqli_fetch_row($Fsal);
        $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

        $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.id_unico = '$PAR[0]' AND c.clase = 7";
        $vapa = $mysqli->query($vpat);
        $valP = mysqli_fetch_row($vapa);
        
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

        $pdf->Ln(5);
    }

    $pdf->Ln(3);
    $pdf->Cell(110,0.5,'',1);
    $pdf->Ln(4);
    $pdf->SetFont('Arial','B',8);
    $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
    $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
    $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

    $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
            . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
            . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
            . "WHERE c.tipoentidadcredito IS NOT NULL AND c.clase = 7";
    $vapa1 = $mysqli->query($vpat1);
    $valP2 = mysqli_fetch_row($vapa1);
        
    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP2[0],2,'.',',')),0,0,'R');

    $TOTAL = $valP2[0] + $valP1[0] + $TOVAL12[0] + $TOT1[0];
    $pdf->Ln(15);
    $pdf->SetFont('Arial','B',10);
    $pdf->cellfitscale($mc0,5,utf8_decode('TOTAL:'),0,0,'C');
    $pdf->cellfitscale($mc1,5,utf8_decode(number_format($TOTAL,2,'.',',')),0,0,'R');

/*
    $TOTAL = $valP2[0] + $valP1[0] + $TOVAL12[0] + $TOT1[0];
    $pdf->Ln(15);
    $pdf->SetFont('Arial','B',10);
    $pdf->cellfitscale($mc0,5,utf8_decode('TOTAL:'),0,0,'C');
    $pdf->cellfitscale($mc1,5,utf8_decode(number_format($TOTAL,2,'.',',')),0,0,'R');
*/
    /////// FINALIZA LA CONDICION CUANDO TODOS SON VACIOS ///////////////////

}elseif(!empty($grupog)){

    if(empty($periodo) && empty($tipof)){

        $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
        $valor = $mysqli->query($salud);

        $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
        $Fond = $mysqli->query($fondo); 

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

        # ciclo de todas las entidades de salud
        while($SAL = mysqli_fetch_row($valor)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($SAL[0]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($SAL[1]),0,0,'C');

            #calcula cuantos empleados esta afiliadso a cada entidad de salud
            $canTemp = "SELECT  COUNT(DISTINCT e.id_unico), t.razonsocial 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'";
            
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');
        
            #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
            $vemple = "SELECT   c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' GROUP BY t.numeroidentificacion";
        
            $vaem = $mysqli->query($vemple);
            $valE = mysqli_fetch_row($vaem);
            
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

            #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
            $vpat = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' GROUP BY t.numeroidentificacion";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

            #calcula el total de los dos aportes y los agrupa por cada entidad de salud 
            $VTot = "SELECT SUM(n.valor), t.razonsocial 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'";
      
            $valT = $mysqli->query($VTot);
            $TOT = mysqli_fetch_row($valT);
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

            $pdf->Ln(5);
        }   #FINALIZA EL CICLO DEL CARGUE DE DATOS DEL FONOD DE SALUD Y CALCULA LOS TOTALES

        $pdf->Ln(3);
        $pdf->Cell(170,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

        $canTemp1 = "SELECT  COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  AND c.clase = 2 AND n.empleado !=2 AND e.grupogestion = '$grupog'";
      
        $Fsal1 = $mysqli->query($canTemp1);
        $CanE1 = mysqli_fetch_row($Fsal1);
        $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

        $ToEM = "SELECT     c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1 AND c.clase = 2 AND e.grupogestion = '$grupog' ";
      
        $VToEM = $mysqli->query($ToEM);
        $VAEMP = mysqli_fetch_row($VToEM);
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[4],2,'.',',')),0,0,'R'); 

        $vpat1 = "SELECT    c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 1  AND c.clase = 7 AND e.grupogestion = '$grupog'";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

        $VTot1 = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 1 AND e.grupogestion = '$grupog'";
      
        $valT1 = $mysqli->query($VTot1);
        $TOT1 = mysqli_fetch_row($valT1);
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');    

        ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////

        $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
        $valor1 = $mysqli->query($pension);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Fondo Solid'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;$mc6=30;

        while($PEN = mysqli_fetch_row($valor1)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($PEN[0]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($PEN[1]),0,0,'C');

            $canTemp = "SELECT  COUNT(e.id_unico) FROM gn_empleado e 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2 AND e.grupogestion = '$grupog'";
        
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

            $vemple = "SELECT   c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' GROUP BY t.numeroidentificacion";
     
            $vaem = $mysqli->query($vemple);
            $valE = mysqli_fetch_row($vaem);
            
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

            $vpat = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' GROUP BY t.numeroidentificacion";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

            $Fsol = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' GROUP BY t.numeroidentificacion";
     
            $foso = $mysqli->query($Fsol);
            $SolF = mysqli_fetch_row($foso);
            
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[4],2,'.',',')),0,0,'R');

            $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'";
      
            $valT = $mysqli->query($VTot);
            $TOT = mysqli_fetch_row($valT);
            $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

            $pdf->Ln(5);
        }

        $pdf->Ln(3);
        $pdf->Cell(200,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C'); 

        $canTemp1 = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_empleado e 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE  af.tipo = 2 AND e.grupogestion = '$grupog'";
      
        $Fsal1 = $mysqli->query($canTemp1);
        $CanE1 = mysqli_fetch_row($Fsal1);
        $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

        $vemple1 = "SELECT  c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND e.grupogestion = '$grupog' ";
     
        $vaem1 = $mysqli->query($vemple1);
        $valE1 = mysqli_fetch_row($vaem1);
            
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[4],2,'.',',')),0,0,'R');

        $vpat1 = "SELECT    c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND c.clase = 7 AND e.grupogestion = '$grupog'";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

        $Fsol1 = "SELECT    c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND e.grupogestion = '$grupog' ";
     
        $foso1 = $mysqli->query($Fsol1);
        $SolF1 = mysqli_fetch_row($foso1);
            
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[4],2,'.',',')),0,0,'R');

        $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2 AND e.grupogestion = '$grupog' ";

        $VAl12  = $mysqli->query($VTot12);
        $TOVAL12 = mysqli_fetch_row($VAl12);
        $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');

        ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
        $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
        $valor2 = $mysqli->query($arl);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;

        while($AR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($AR[0]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($AR[1]),0,0,'C');

            $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_empleado e 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE t.numeroidentificacion = '$AR[0]' AND af.tipo = 4 AND e.grupogestion = '$grupog'";
        
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

            $vpat = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

            $pdf->Ln(5);
        }

        $pdf->Ln(3);
        $pdf->Cell(110,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
        $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

        $vpat1 = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R'); 

        ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

        $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.tipoentidad = 4";
        $valor2 = $mysqli->query($paraf);
      
        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;

        while($PAR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($PAR[2]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($PAR[1]),0,0,'C');

            $canTemp = "SELECT  COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog'";
            
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

            $vpat = "SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND c.clase = 7";
        
            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

            $pdf->Ln(5);
        }

        $pdf->Ln(3);
        $pdf->Cell(110,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
        $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

        $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                WHERE e.grupogestion = '$grupog' AND c.tipoentidadcredito IS NOT NULL AND c.clase = 7";
      
        $vapa1 = $mysqli->query($vpat1);
        $valP2 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP2[0],2,'.',',')),0,0,'R');

        $TOTAL = $valP2[0] + $valP1[0] + $TOVAL12[0] + $TOT1[0];
        $pdf->Ln(15);
        $pdf->SetFont('Arial','B',10);
        $pdf->cellfitscale($mc0,5,utf8_decode('TOTAL:'),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode(number_format($TOTAL,2,'.',',')),0,0,'R');
        /*
        $TOTAL = $valP2[0] + $valP1[0] + $TOVAL12[0] + $TOT1[0];
        $pdf->Ln(15);
        $pdf->SetFont('Arial','B',10);
        $pdf->cellfitscale($mc0,5,utf8_decode('TOTAL:'),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode(number_format($TOTAL,2,'.',',')),0,0,'R');
        */
        ///////// FINALIZA LA CONDICION  CUANDO EL PERIODO Y EL TIPO DE FONDO SON VACIOS ///////////////////////////     
  
    }elseif(!empty($periodo) && empty($tipof)){

        $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
        $valor = $mysqli->query($salud);

        $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
        $Fond = $mysqli->query($fondo); 

 

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

        #consulta la fecha inicial y la fecha final del periodo
        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
        $retEmp = $mysqli->query($retiro);
        $retE = mysqli_fetch_row($retEmp);

        # ciclo de todas las entidades de salud
        while($SAL = mysqli_fetch_row($valor)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($SAL[0]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($SAL[1]),0,0,'C');

            #calcula cuantos empleados esta afiliadso a cada entidad de salud
            $canTemp = "SELECT  COUNT(DISTINCT e.id_unico), 
                                t.razonsocial 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE af.tipo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 
                        AND vr.vinculacionretiro IS NULL OR af.tipo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' 
                        AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
                       
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');
        
            #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
            $vemple = "SELECT   c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' GROUP BY t.numeroidentificacion";
       
            $vaem = $mysqli->query($vemple);
            $valE = mysqli_fetch_row($vaem);
            
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

            #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
            $vpat = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' GROUP BY t.numeroidentificacion";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

            #calcula el total de los dos aportes y los agrupa por cada entidad de salud 
            $VTot = "SELECT SUM(n.valor), t.razonsocial 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  AND c.clase != 6 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";
      
            $valT = $mysqli->query($VTot);
            $TOT = mysqli_fetch_row($valT);
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

            $pdf->Ln(5);
        }   #FINALIZA EL CICLO DEL CARGUE DE DATOS DEL FONOD DE SALUD Y CALCULA LOS TOTALES

        $pdf->Ln(3);
        $pdf->Cell(170,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

        $canTemp1 = "SELECT  COUNT(DISTINCT n.empleado) 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE af.tipo = 1  AND c.clase = 2   AND e.grupogestion = '$grupog' AND vr.estado=1 
                    AND vr.vinculacionretiro IS NULL OR af.tipo = 1  AND c.clase = 2  AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' 
                    AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
    
        $Fsal1 = $mysqli->query($canTemp1);
        $CanE1 = mysqli_fetch_row($Fsal1);
        $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

        $ToEM = "SELECT     c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 1 AND c.clase = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";
      
        $VToEM = $mysqli->query($ToEM);
        $VAEMP = mysqli_fetch_row($VToEM);
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[4],2,'.',',')),0,0,'R'); 

        $vpat1 = "SELECT    c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 1  AND c.clase = 7 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

        $VTot1 = "SELECT SUM(n.valor), t.razonsocial 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 1 AND c.clase != 6 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";
      
        $valT1 = $mysqli->query($VTot1);
        $TOT1 = mysqli_fetch_row($valT1);
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');    

        ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////

        $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
        $valor1 = $mysqli->query($pension);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Fondo Solid'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;$mc6=30;

        while($PEN = mysqli_fetch_row($valor1)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($PEN[0]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($PEN[1]),0,0,'C');

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);

            #calcula cuantos empleados estan afiliados al fondo de pension
            $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_empleado e 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                        WHERE t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR  t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
            
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

            $vemple = "SELECT   c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' 
                    AND n.periodo = '$periodo' GROUP BY t.numeroidentificacion";
     
            $vaem = $mysqli->query($vemple);
            $valE = mysqli_fetch_row($vaem);
            
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

            $vpat = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' GROUP BY t.numeroidentificacion";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

            $Fsol = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' 
                    AND n.periodo = '$periodo' GROUP BY t.numeroidentificacion";
     
            $foso = $mysqli->query($Fsol);
            $SolF = mysqli_fetch_row($foso);
            
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[4],2,'.',',')),0,0,'R');


      
            $VTot = "SELECT SUM(n.valor), t.razonsocial 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase != 6 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";
      
            $valT = $mysqli->query($VTot);
            $TOT = mysqli_fetch_row($valT);
            $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

            $pdf->Ln(5);
        }

        $pdf->Ln(3);
        $pdf->Cell(200,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C'); 

        $canTemp1 = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE  af.tipo = 2 AND e.grupogestion = '$grupog' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                    OR  af.tipo = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
      
        $Fsal1 = $mysqli->query($canTemp1);
        $CanE1 = mysqli_fetch_row($Fsal1);
        $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

        $vemple1 = "SELECT  c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' ";
     
        $vaem1 = $mysqli->query($vemple1);
        $valE1 = mysqli_fetch_row($vaem1);
            
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[4],2,'.',',')),0,0,'R');

        $vpat1 = "SELECT    c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND c.clase = 7 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

        $Fsol1 = "SELECT    c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' ";
     
        $foso1 = $mysqli->query($Fsol1);
        $SolF1 = mysqli_fetch_row($foso1);
            
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[4],2,'.',',')),0,0,'R');

        $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2 AND c.clase != 6 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' ";

        $VAl12  = $mysqli->query($VTot12);
        $TOVAL12 = mysqli_fetch_row($VAl12);
        $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');

        ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
      
        $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
        $valor2 = $mysqli->query($arl);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;

        #consulta la fecha inicial y la fecha final del periodo
        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
        $retEmp = $mysqli->query($retiro);
        $retE = mysqli_fetch_row($retEmp);

        while($AR = mysqli_fetch_row($valor2)){

            $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE t.numeroidentificacion = '$AR[0]' AND af.tipo = 4 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR  t.numeroidentificacion = '$AR[0]' AND af.tipo = 4 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
       
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);

            if($CanE[0] > 0){
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($AR[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($AR[1]),0,0,'C');
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vpat = "SELECT sum(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE periodo = '$periodo' AND concepto =363 AND e.grupogestion = '$grupog'";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
            
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                $pdf->Ln(5);
            }

            
        }

        $pdf->Ln(3);
        $pdf->Cell(110,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
        $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

        $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE periodo = '$periodo' AND concepto =363 AND e.grupogestion = '$grupog'";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R'); 

        ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////
        $caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial, t.id_unico FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
        $valor2 = $mysqli->query($caja);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(40,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(20,18,utf8_decode('CAJAS DE COMPENSACIÓN FAMILIAR'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;

        while($AR = mysqli_fetch_row($valor2)){

           

             $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE t.numeroidentificacion = '$AR[0]' AND e.grupogestion = '$grupog' AND af.tipo = 6 AND n.periodo = '$periodo'  AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR t.numeroidentificacion = '$AR[0]' AND e.grupogestion = '$grupog' AND af.tipo = 6 AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
        
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);

            if($CanE[0] > 0){
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($AR[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($AR[1]),0,0,'C');
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vpat = "SELECT sum(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico

                        WHERE periodo = '$periodo' AND e.grupogestion = '$grupog' AND concepto =256 AND t.numeroidentificacion = '$AR[0]'";
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
                
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                $pdf->Ln(5);
            }
            
        }

        $pdf->Ln(3);
        $pdf->Cell(110,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
        $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

        $vpat1 = "SELECT sum(n.valor) FROM `gn_novedad` n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                WHERE n.periodo = '$periodo' AND n.concepto =256 AND e.grupogestion = '$grupog'";
        $vapa1 = $mysqli->query($vpat1);
        $valP3 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP3[0],2,'.',',')),0,0,'R'); 

        ////////////// FINALIZA EL FONDO DE CCF /////////////////////////////////////////////

        $paraf = "SELECT DISTINCT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.tipoentidad = 4";
        $valor2 = $mysqli->query($paraf);
      
        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;

        while($PAR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($PAR[2]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($PAR[1]),0,0,'C');

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);

            $canTemp = "SELECT COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND  vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

            $vpat = "SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND c.clase = 7 ";
        
            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

            $pdf->Ln(5);
        }

        $pdf->Ln(3);
        $pdf->Cell(110,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
        $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

        $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                . "WHERE e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND c.tipoentidadcredito IS NOT NULL AND c.clase = 7 AND n.concepto != 363 AND n.concepto != 256";
      
        $vapa1 = $mysqli->query($vpat1);
        $valP2 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP2[0],2,'.',',')),0,0,'R');

        $TOTAL = $valP2[0] + $valP1[0] + $TOVAL12[0] + $TOT1[0] + $valP3[0];
        $pdf->Ln(15);
        $pdf->SetFont('Arial','B',10);
        $pdf->cellfitscale($mc0,5,utf8_decode('TOTAL:'),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode(number_format($TOTAL,2,'.',',')),0,0,'R');

        ////////// FINALIZA LA CONDICION CUANOD EL TIPO DE FONDO ES VACIO //////////////////////
        
    }elseif(empty($periodo) && !empty($tipof)){

        $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
        $valor = $mysqli->query($salud);

        $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
        $Fond = $mysqli->query($fondo); 

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

        if($tipof = 1){
      
            # ciclo de todas las entidades de salud
            while($SAL = mysqli_fetch_row($valor)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($SAL[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($SAL[1]),0,0,'C');

                #calcula cuantos empleados esta afiliadso a cada entidad de salud
                $canTemp = "SELECT  COUNT(DISTINCT e.id_unico), t.razonsocial 
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'";
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');
          
                #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                $vemple = "SELECT   c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'  GROUP BY t.numeroidentificacion";
          
                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);
            
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

                #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                $vpat = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'  GROUP BY t.numeroidentificacion";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

                #calcula el total de los dos aportes y los agrupa por cada entidad de salud 
                $VTot = "SELECT SUM(n.valor), t.razonsocial 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' ";
      
                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

                $pdf->Ln(5);
            }   #FINALIZA EL CICLO DEL CARGUE DE DATOS DEL FONOD DE SALUD Y CALCULA LOS TOTALES

            $pdf->Ln(3);
            $pdf->Cell(170,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $canTemp1 = "SELECT  COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 2 AND n.empleado !=2 AND e.grupogestion = '$grupog'";
      
            $Fsal1 = $mysqli->query($canTemp1);
            $CanE1 = mysqli_fetch_row($Fsal1);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

            $ToEM = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1 AND c.clase = 2 AND e.grupogestion = '$grupog' ";
      
            $VToEM = $mysqli->query($ToEM);
            $VAEMP = mysqli_fetch_row($VToEM);
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[4],2,'.',',')),0,0,'R'); 

            $vpat1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  AND c.clase = 7 AND e.grupogestion = '$grupog' ";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

            $VTot1 = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1 AND e.grupogestion = '$grupog' ";
      
            $valT1 = $mysqli->query($VTot1);
            $TOT1 = mysqli_fetch_row($valT1);
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');    

            ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////
        }elseif($tipof = 2){    

            $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
            $valor1 = $mysqli->query($pension);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Fondo Solid'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;$mc6=30;

            while($PEN = mysqli_fetch_row($valor1)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($PEN[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($PEN[1]),0,0,'C');

                $canTemp = "SELECT  COUNT(e.id_unico) FROM gn_empleado e 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2 AND e.grupogestion = '$grupog'";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vemple = "SELECT   c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'  GROUP BY t.numeroidentificacion";
     
                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);
            
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

                $vpat = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'  GROUP BY t.numeroidentificacion";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

                $Fsol = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'  GROUP BY t.numeroidentificacion";
     
                $foso = $mysqli->query($Fsol);
                $SolF = mysqli_fetch_row($foso);
            
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[4],2,'.',',')),0,0,'R');

                $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' ";
      
                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(200,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C'); 

            $canTemp1 = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_empleado e 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE  af.tipo = 2 AND e.grupogestion = '$grupog'";
      
            $Fsal1 = $mysqli->query($canTemp1);
            $CanE1 = mysqli_fetch_row($Fsal1);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

            $vemple1 = "SELECT  c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND e.grupogestion = '$grupog'  ";
     
            $vaem1 = $mysqli->query($vemple1);
            $valE1 = mysqli_fetch_row($vaem1);
            
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[4],2,'.',',')),0,0,'R');

            $vpat1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 7 AND e.grupogestion = '$grupog' ";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

            $Fsol1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND e.grupogestion = '$grupog'  ";
     
            $foso1 = $mysqli->query($Fsol1);
            $SolF1 = mysqli_fetch_row($foso1);
            
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[4],2,'.',',')),0,0,'R');

            $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2 AND e.grupogestion = '$grupog' ";

            $VAl12  = $mysqli->query($VTot12);
            $TOVAL12 = mysqli_fetch_row($VAl12);
            $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');

            ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
    
        }elseif($tipof = 3){   
      
            $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
            $valor2 = $mysqli->query($arl);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');    
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;

            while($AR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($AR[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($AR[1]),0,0,'C');

                $canTemp = "SELECT  COUNT(e.id_unico) FROM gn_empleado e 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE t.numeroidentificacion = '$AR[0]' AND af.tipo = 4 AND e.grupogestion = '$grupog'";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vpat = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363";
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(110,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
            $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

            $vpat1 = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363";
            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R'); 

            ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

        }else{  

            $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.tipoentidad = 4";
            $valor2 = $mysqli->query($paraf);
      
            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;

            while($PAR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($PAR[2]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($PAR[1]),0,0,'C');

                $canTemp = "SELECT COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' ";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vpat = "SELECT SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND c.clase = 7";
        
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(110,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
            $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

            $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE e.grupogestion = '$grupog'  AND c.tipoentidadcredito IS NOT NULL AND c.clase = 7";
    
            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

        } 


        ///////////// FINALIZA LA CONDCION CUANDO EL PERIODO ES VACIO Y EL TIPO DE FONDO NO  /////////////////////////////  

    }else{

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

        if($tipof == 1){

            $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
            $valor = $mysqli->query($salud);

            $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
            $Fond = $mysqli->query($fondo); 

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(8);
            
            # ciclo de todas las entidades de salud
            while($SAL = mysqli_fetch_row($valor)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($SAL[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($SAL[1]),0,0,'C');

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);

                #calcula cuantos empleados esta afiliadso a cada entidad de salud
                $canTemp = "SELECT  COUNT(DISTINCT e.id_unico), t.razonsocial 
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE c.tipofondo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                            OR c.tipofondo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
          
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');
          
                #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                $vemple = "SELECT   c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                            FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'  GROUP BY t.numeroidentificacion";
          
                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);
                    
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

                #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                $vpat = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'   GROUP BY t.numeroidentificacion";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

                #calcula el total de los dos aportes y los agrupa por cada entidad de salud 
                $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";
      
                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

                $pdf->Ln(5);
            }   #FINALIZA EL CICLO DEL CARGUE DE DATOS DEL FONOD DE SALUD Y CALCULA LOS TOTALES

            $pdf->Ln(3);
            $pdf->Cell(170,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $canTemp1 = "SELECT  COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE c.tipofondo = 1  AND c.clase = 2  AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR c.tipofondo = 1  AND c.clase = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
      
            $Fsal1 = $mysqli->query($canTemp1);
            $CanE1 = mysqli_fetch_row($Fsal1);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

            $ToEM = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion,
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1 AND c.clase = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";
      
            $VToEM = $mysqli->query($ToEM);
            $VAEMP = mysqli_fetch_row($VToEM);
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[4],2,'.',',')),0,0,'R'); 

            $vpat1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  AND c.clase = 7 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

            $VTot1 = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";
      
            $valT1 = $mysqli->query($VTot1);
            $TOT1 = mysqli_fetch_row($valT1);
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');    

            ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////
        }elseif($tipof == 2){    

            $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                        LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
      
            $valor1 = $mysqli->query($pension);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Fondo Solid'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;$mc6=30;

            while($PEN = mysqli_fetch_row($valor1)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($PEN[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($PEN[1]),0,0,'C');

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                
                $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro 
                            IS NULL OR  t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha 
                            BETWEEN '$retE[0]' AND '$retE[1]' ";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vemple = "SELECT   c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' 
                        AND n.periodo = '$periodo'   GROUP BY t.numeroidentificacion";
     
                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);
            
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

                $vpat = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'  GROUP BY t.numeroidentificacion";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

                $Fsol = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' 
                        AND n.periodo = '$periodo'   GROUP BY t.numeroidentificacion";
     
                $foso = $mysqli->query($Fsol);
                $SolF = mysqli_fetch_row($foso);
            
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[4],2,'.',',')),0,0,'R');

                $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'  ";
      
                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(200,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C'); 

            $canTemp1 = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE  af.tipo = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro 
                        IS NULL OR  af.tipo = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha 
                        BETWEEN '$retE[0]' AND '$retE[1]'";
      
            $Fsal1 = $mysqli->query($canTemp1);
            $CanE1 = mysqli_fetch_row($Fsal1);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

            $vemple1 = "SELECT  c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'  ";
     
            $vaem1 = $mysqli->query($vemple1);
            $valE1 = mysqli_fetch_row($vaem1);
            
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[4],2,'.',',')),0,0,'R');

            $vpat1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 7 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'  ";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

            $Fsol1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'  ";
     
            $foso1 = $mysqli->query($Fsol1);
            $SolF1 = mysqli_fetch_row($foso1);
            
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[4],2,'.',',')),0,0,'R');

            $VTot12 = "SELECT SUM(n.valor)   FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'  ";

            $VAl12  = $mysqli->query($VTot12);
            $TOVAL12 = mysqli_fetch_row($VAl12);
            $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');

            ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
    
        }elseif($tipof == 3){   
      
            $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
            $valor2 = $mysqli->query($arl);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;

            while($AR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($AR[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($AR[1]),0,0,'C');

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);

                $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE t.numeroidentificacion = '$AR[0]' AND af.tipo = 4 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro 
                            IS NULL OR t.numeroidentificacion = '$AR[0]' AND af.tipo = 4 AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha 
                            BETWEEN '$retE[0]' AND '$retE[1]'";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vpat = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363 AND periodo = '$periodo'";
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(110,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
            $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

            $vpat1 = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363 AND periodo = '$periodo' ";
            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R'); 

            ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////
        
        }else{  

            $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.tipoentidad = 4";
            $valor2 = $mysqli->query($paraf);
      
            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;

            while($PAR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($PAR[2]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($PAR[1]),0,0,'C');

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);

                $canTemp = "SELECT COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
                            WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                            OR  t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vpat = "SELECT SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND c.clase = 7 ";
        
                $vapa = $mysqli->query($vpat);      
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(110,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
            $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

            $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE e.grupogestion = '$grupog' AND n.periodo = '$periodo'  AND c.tipoentidadcredito IS NOT NULL AND c.clase = 7";
    
            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
        }

    }

    //////////////////// FINALIZA CUANDO TODOS LOS VALORES ESTAN LLENOS ////////////////////////////

}elseif(empty($grupog)){

    if(!empty($periodo) && empty($tipof)){

        $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial, t.id_unico FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
        $valor = $mysqli->query($salud);

        $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
        $Fond = $mysqli->query($fondo); 

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

        # ciclo de todas las entidades de salud
        while($SAL = mysqli_fetch_row($valor)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($SAL[0]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($SAL[1]),0,0,'C');

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);

            #calcula cuantos empleados esta afiliadso a cada entidad de salud
            $canTemp = "SELECT  COUNT(DISTINCT e.id_unico), t.razonsocial 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE c.tipofondo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR c.tipofondo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
            
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');
        
            #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
            $vemple = "SELECT   c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo' GROUP BY t.numeroidentificacion";
        
            $vaem = $mysqli->query($vemple);
            $valE = mysqli_fetch_row($vaem);
            
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

            #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
            $vpat = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo' GROUP BY t.numeroidentificacion";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

            #calcula el total de los dos aportes y los agrupa por cada entidad de salud 
            $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 1  AND t.numeroidentificacion = '$SAL[0]' AND c.clase != 6 AND n.periodo = '$periodo'";
      
            $valT = $mysqli->query($VTot);
            $TOT = mysqli_fetch_row($valT);
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

            $pdf->Ln(5);
        }   #FINALIZA EL CICLO DEL CARGUE DE DATOS DEL FONOD DE SALUD Y CALCULA LOS TOTALES

        $pdf->Ln(3);
        $pdf->Cell(170,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

        $canTemp1 = "SELECT  COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
                    WHERE af.tipo = 1  AND n.periodo = '$periodo' AND n.empleado !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                    OR af.tipo = 1  AND n.periodo = '$periodo' AND n.empleado !=2 AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
      
        $Fsal1 = $mysqli->query($canTemp1);
        $CanE1 = mysqli_fetch_row($Fsal1);
        $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

        $ToEM = "SELECT     c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 1 AND c.clase = 2  AND n.periodo = '$periodo'";
      
        $VToEM = $mysqli->query($ToEM);
        $VAEMP = mysqli_fetch_row($VToEM);
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[4],2,'.',',')),0,0,'R'); 

        $vpat1 = "SELECT    c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 1  AND c.clase = 7  AND n.periodo = '$periodo'";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

        $VTot1 = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
              LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
              LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
              LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
              WHERE c.tipofondo = 1  AND n.periodo = '$periodo' AND c.clase != 6 ";
      
        $valT1 = $mysqli->query($VTot1);
        $TOT1 = mysqli_fetch_row($valT1);
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');    

        ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////

        $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial, t.id_unico FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
        $valor1 = $mysqli->query($pension);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Fondo Solid'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;$mc6=30;

        #consulta la fecha inicial y la fecha final del periodo
        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
        $retEmp = $mysqli->query($retiro);
        $retE = mysqli_fetch_row($retEmp);

        while($PEN = mysqli_fetch_row($valor1)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($PEN[0]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($PEN[1]),0,0,'C');

            $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_empleado e 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2  AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2  AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
        
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

            $vemple = "SELECT   c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo' GROUP BY t.numeroidentificacion";
     
            $vaem = $mysqli->query($vemple);
            $valE = mysqli_fetch_row($vaem);
            
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

            $vpat = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo' GROUP BY t.numeroidentificacion";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

            $Fsol = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo' GROUP BY t.numeroidentificacion";
     
            $foso = $mysqli->query($Fsol);
            $SolF = mysqli_fetch_row($foso);
            
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[4],2,'.',',')),0,0,'R');

            $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND t.numeroidentificacion = '$PEN[0]' AND c.clase != 6 AND n.periodo = '$periodo'";
      
            $valT = $mysqli->query($VTot);
            $TOT = mysqli_fetch_row($valT);
            $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     

            $pdf->Ln(5);
        }

        $pdf->Ln(3);
        $pdf->Cell(200,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C'); 

         $canTemp1 = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_empleado e 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE  af.tipo = 2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                    OR  af.tipo = 2  AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
      
        $Fsal1 = $mysqli->query($canTemp1);
        $CanE1 = mysqli_fetch_row($Fsal1);
        $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

        $vemple1 = "SELECT  c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.periodo = '$periodo' ";
     
        $vaem1 = $mysqli->query($vemple1);
        $valE1 = mysqli_fetch_row($vaem1);
            
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[4],2,'.',',')),0,0,'R');

        $vpat1 = "SELECT    c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND c.clase = 7  AND n.periodo = '$periodo'";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

        $Fsol1 = "SELECT    c.id_unico, 
                            c.descripcion, 
                            t.numeroidentificacion, 
                            t.razonsocial, 
                            SUM(n.valor), 
                            c.clase 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.periodo = '$periodo' ";
     
        $foso1 = $mysqli->query($Fsol1);
        $SolF1 = mysqli_fetch_row($foso1);
            
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[4],2,'.',',')),0,0,'R');

        $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                WHERE c.tipofondo = 2  AND n.periodo = '$periodo' AND c.clase != 6 ";

        $VAl12  = $mysqli->query($VTot12);
        $TOVAL12 = mysqli_fetch_row($VAl12);
        $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');

        ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
      
        $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial, t.id_unico FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
        $valor2 = $mysqli->query($arl);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;

        while($AR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($AR[0]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($AR[1]),0,0,'C');

            $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE t.numeroidentificacion = '$AR[0]' AND af.tipo = 4 AND n.periodo = '$periodo'  AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR t.numeroidentificacion = '$AR[0]' AND af.tipo = 4 AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
        
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

            $vpat = "SELECT sum(n.valor) FROM gn_novedad n
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico

                    WHERE periodo = '$periodo' AND concepto =363 AND t.numeroidentificacion = '$AR[0]'";
            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

            $pdf->Ln(5);
        }

        $pdf->Ln(3);
        $pdf->Cell(110,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
        $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

        $vpat1 = "SELECT sum(valor) FROM `gn_novedad` WHERE periodo = '$periodo' AND concepto =363";
        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R'); 

        ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

        echo $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial, t.id_unico FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
        $valor2 = $mysqli->query($arl);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(40,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(20,18,utf8_decode('CAJAS DE COMPENSACIÓN FAMILIAR'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;

        while($AR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($AR[0]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($AR[1]),0,0,'C');

            $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE t.numeroidentificacion = '$AR[0]' AND af.tipo = 6 AND n.periodo = '$periodo'  AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR t.numeroidentificacion = '$AR[0]' AND af.tipo = 6 AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
        
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

            $vpat = "SELECT sum(n.valor) FROM gn_novedad n
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico

                    WHERE periodo = '$periodo' AND concepto =256 AND t.numeroidentificacion = '$AR[0]'";
            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        	
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

            $pdf->Ln(5);
        }

        $pdf->Ln(3);
        $pdf->Cell(110,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
        $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

        $vpat1 = "SELECT sum(valor) FROM `gn_novedad` WHERE periodo = '$periodo' AND concepto =256";
        $vapa1 = $mysqli->query($vpat1);
        $valP36 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP36[0],2,'.',',')),0,0,'R'); 

        ////////////// FINALIZA EL FONDO DE CCF /////////////////////////////////////////////

        $paraf = "SELECT DISTINCT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.tipoentidad = 4";
        $valor2 = $mysqli->query($paraf);
      
        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
        $pdf->Ln(12);
     
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
        $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;

        while($PAR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($PAR[2]),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode($PAR[1]),0,0,'C');

            $canTemp = "SELECT COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE t.id_unico = '$PAR[0]'  AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR t.id_unico = '$PAR[0]'  AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";
        
            $Fsal = $mysqli->query($canTemp);
            $CanE = mysqli_fetch_row($Fsal);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

            $vpat = "SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE t.id_unico = '$PAR[0]'  AND n.periodo = '$periodo' AND c.clase = 7";
            
            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
            $pdf->Ln(5);
        }

        $pdf->Ln(3);
        $pdf->Cell(110,0.5,'',1);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
        $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

        $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                WHERE  n.periodo = '$periodo' AND c.tipoentidadcredito IS NOT NULL AND c.clase = 7 AND n.concepto !=363 AND n.concepto !=256 ";
      
        $vapa1 = $mysqli->query($vpat1);
        $valP2 = mysqli_fetch_row($vapa1);
        
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP2[0],2,'.',',')),0,0,'R');

        
        $TOTAL = $valP2[0] + $valP1[0] + $TOVAL12[0] + $TOT1[0] + $valP36[0];
        $pdf->Ln(15);
        $pdf->SetFont('Arial','B',10);
        $pdf->cellfitscale($mc0,5,utf8_decode('TOTAL:'),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode(number_format($TOTAL,2,'.',',')),0,0,'R');
        
        /*$TOTAL = $valP2[0] + $valP1[0] + $TOVAL12[0] + $TOT1[0];
        $pdf->Ln(15);
        $pdf->SetFont('Arial','B',10);
        $pdf->cellfitscale($mc0,5,utf8_decode('TOTAL:'),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode(number_format($TOTAL,2,'.',',')),0,0,'R');*/

        ////////// FINALIZA LA CONDICION CUANOD EL TIPO DE FONDO ES VACIO //////////////////////
    
    }elseif(empty($periodo) && !empty($tipof)){
    
        if($tipof == 1){
            
            $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
            $valor = $mysqli->query($salud);

            $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
            $Fond = $mysqli->query($fondo); 

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;
            
            # ciclo de todas las entidades de salud
            while($SAL = mysqli_fetch_row($valor)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($SAL[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($SAL[1]),0,0,'C');

                #calcula cuantos empleados esta afiliadso a cada entidad de salud
                $canTemp = "SELECT  COUNT(DISTINCT e.id_unico), t.razonsocial FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' ";
          
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');
          
                #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                $vemple = "SELECT   c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion = '$SAL[0]'   GROUP BY t.numeroidentificacion";
          
                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);
            
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

                #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                $vpat = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'   GROUP BY t.numeroidentificacion";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

                #calcula el total de los dos aportes y los agrupa por cada entidad de salud 
                $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND t.numeroidentificacion = '$SAL[0]'  ";
      
                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     
                $pdf->Ln(5);
            }    #FINALIZA EL CICLO DEL CARGUE DE DATOS DEL FONOD DE SALUD Y CALCULA LOS TOTALES

            $pdf->Ln(3);
            $pdf->Cell(170,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $canTemp1 = "SELECT  COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 2 AND n.empleado !=2 ";
      
            $Fsal1 = $mysqli->query($canTemp1);
            $CanE1 = mysqli_fetch_row($Fsal1);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');


            $ToEM = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1 AND c.clase = 2 ";
      
            $VToEM = $mysqli->query($ToEM);
            $VAEMP = mysqli_fetch_row($VToEM);
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[4],2,'.',',')),0,0,'R'); 

            $vpat1 = "SELECT    c.id_unico, 
                                c.descripcion,  
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  AND c.clase = 7  ";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

            $VTot1 = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  ";
      
            $valT1 = $mysqli->query($VTot1);
            $TOT1 = mysqli_fetch_row($valT1);
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');    

            ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////
        
        }elseif($tipof == 2){    

            $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
            $valor1 = $mysqli->query($pension);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Fondo Solid'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;$mc6=30;

            while($PEN = mysqli_fetch_row($valor1)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($PEN[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($PEN[1]),0,0,'C');

                $canTemp = "SELECT  COUNT(e.id_unico) FROM gn_empleado e 
                              LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                              LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                              WHERE t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2 ";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vemple = "SELECT   c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND t.numeroidentificacion = '$PEN[0]'   GROUP BY t.numeroidentificacion";
     
                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);
            
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

                $vpat = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'   GROUP BY t.numeroidentificacion";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

                $Fsol = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND t.numeroidentificacion = '$PEN[0]'   GROUP BY t.numeroidentificacion";
     
                $foso = $mysqli->query($Fsol);
                $SolF = mysqli_fetch_row($foso);
            
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[4],2,'.',',')),0,0,'R');

                $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND t.numeroidentificacion = '$PEN[0]'  ";
      
                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     
                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(200,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C'); 

            $canTemp1 = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_empleado e 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE  af.tipo = 2 ";
      
            $Fsal1 = $mysqli->query($canTemp1);
            $CanE1 = mysqli_fetch_row($Fsal1);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

            $vemple1 = "SELECT  c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  ";
     
            $vaem1 = $mysqli->query($vemple1);
            $valE1 = mysqli_fetch_row($vaem1);
            
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[4],2,'.',',')),0,0,'R');

            $vpat1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 7  ";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

            $Fsol1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81   ";
     
            $foso1 = $mysqli->query($Fsol1);
            $SolF1 = mysqli_fetch_row($foso1);
            
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[4],2,'.',',')),0,0,'R');

            $VTot12 = "SELECT SUM(n.valor)  FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  ";

            $VAl12  = $mysqli->query($VTot12);
            $TOVAL12 = mysqli_fetch_row($VAl12);
            $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');

            ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
        
        }elseif($tipof == 3){   
      
            $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
            $valor2 = $mysqli->query($arl);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;

            while($AR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($AR[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($AR[1]),0,0,'C');

                $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_empleado e 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE t.numeroidentificacion = '$AR[0]' AND af.tipo = 4 ";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vpat = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363";
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(110,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
            $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

            $vpat1 = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363";
            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R'); 

            ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

        }else{  

            $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE c.tipoentidadcredito is not NULL";
            $valor2 = $mysqli->query($paraf);
            
            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;

            while($PAR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($PAR[2]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($PAR[1]),0,0,'C');

                $canTemp = "SELECT COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico WHERE t.id_unico = '$PAR[0]'  ";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vpat = "SELECT SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico WHERE t.id_unico = '$PAR[0]'  ";
        
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(110,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
            $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

            $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico WHERE  c.tipoentidadcredito IS NOT NULL";
    
            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

        } 

        ///////////// FINALIZA LA CONDCION CUANDO EL PERIODO ES VACIO Y EL TIPO DE FONDO NO  /////////////////////////////  

    }else{

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

        #consulta la fecha inicial y la fecha final del periodo
        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
        $retEmp = $mysqli->query($retiro);
        $retE = mysqli_fetch_row($retEmp);

        if($tipof == 1){
      
            $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
            $valor = $mysqli->query($salud);

            $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
            $Fond = $mysqli->query($fondo); 

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(8);
            
            # ciclo de todas las entidades de salud
            while($SAL = mysqli_fetch_row($valor)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($SAL[0]),0,0,'C');      
                $pdf->cellfitscale($mc1,5,utf8_decode($SAL[1]),0,0,'C');

                #calcula cuantos empleados esta afiliadso a cada entidad de salud
                $canTemp = "SELECT  COUNT(DISTINCT e.id_unico), t.razonsocial FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE af.tipo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR af.tipo = 1  AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
            
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');
          
                #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                $vemple = "SELECT   c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo'  GROUP BY t.numeroidentificacion";
          
                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);
            
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

                #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                $vpat = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo'   GROUP BY t.numeroidentificacion";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

                #calcula el total de los dos aportes y los agrupa por cada entidad de salud 
                $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo'";
      
                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     
                $pdf->Ln(5);
            
            }   #FINALIZA EL CICLO DEL CARGUE DE DATOS DEL FONOD DE SALUD Y CALCULA LOS TOTALES

            $pdf->Ln(3);
            $pdf->Cell(170,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $canTemp1 = "SELECT  COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE c.tipofondo = 1  AND c.clase = 2 AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR c.tipofondo = 1  AND c.clase = 2  AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha 
                        BETWEEN '$retE[0]' AND '$retE[1]' ";
      
            $Fsal1 = $mysqli->query($canTemp1);
            $CanE1 = mysqli_fetch_row($Fsal1);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

            $ToEM = "SELECT     c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1 AND c.clase = 2 AND n.periodo = '$periodo'";
      
            $VToEM = $mysqli->query($ToEM);
            $VAEMP = mysqli_fetch_row($VToEM);
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[4],2,'.',',')),0,0,'R'); 

            $vpat1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 1  AND c.clase = 7  AND n.periodo = '$periodo'";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

            $VTot1 = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1 AND n.periodo = '$periodo' ";
      
            $valT1 = $mysqli->query($VTot1);
            $TOT1 = mysqli_fetch_row($valT1);
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');    

            ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////
        
        }elseif($tipof == 2){    

            $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
            $valor1 = $mysqli->query($pension);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Fondo Solid'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;$mc6=30;

            while($PEN = mysqli_fetch_row($valor1)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($PEN[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($PEN[1]),0,0,'C');

                $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2 AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                            OR t.numeroidentificacion = '$PEN[0]' AND af.tipo = 2  AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vemple = "SELECT   c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo'   GROUP BY t.numeroidentificacion";
     
                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);
            
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[4],2,'.',',')),0,0,'R');

                $vpat = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo'  GROUP BY t.numeroidentificacion";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[4],2,'.',',')),0,0,'R');

                $Fsol = "SELECT     c.id_unico, 
                                    c.descripcion, 
                                    t.numeroidentificacion, 
                                    t.razonsocial, 
                                    SUM(n.valor), 
                                    c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo'   GROUP BY t.numeroidentificacion";
     
                $foso = $mysqli->query($Fsol);
                $SolF = mysqli_fetch_row($foso);
            
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[4],2,'.',',')),0,0,'R');

                $VTot = "SELECT SUM(n.valor), t.razonsocial FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo'  ";
      
                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');     
                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(200,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8); 
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C'); 

            $canTemp1 = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE  af.tipo = 2 AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                        OR  af.tipo = 2  AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
      
            $Fsal1 = $mysqli->query($canTemp1);
            $CanE1 = mysqli_fetch_row($Fsal1);
            $pdf->cellfitscale($mc2,5,utf8_decode($CanE1[0]),0,0,'R');

            $vemple1 = "SELECT  c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.periodo = '$periodo'  ";
     
            $vaem1 = $mysqli->query($vemple1);
            $valE1 = mysqli_fetch_row($vaem1);
            
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[4],2,'.',',')),0,0,'R');

            $vpat1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 7  AND n.periodo = '$periodo'  ";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[4],2,'.',',')),0,0,'R');

            $Fsol1 = "SELECT    c.id_unico, 
                                c.descripcion, 
                                t.numeroidentificacion, 
                                t.razonsocial, 
                                SUM(n.valor), 
                                c.clase 
                                FROM gn_novedad n 
                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.periodo = '$periodo'  ";
     
            $foso1 = $mysqli->query($Fsol1);
            $SolF1 = mysqli_fetch_row($foso1);
            
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[4],2,'.',',')),0,0,'R');
      
            $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2 AND n.periodo = '$periodo'  ";

            $VAl12  = $mysqli->query($VTot12);
            $TOVAL12 = mysqli_fetch_row($VAl12);
            $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');

            ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
    
        }elseif($tipof == 3){   
      
            $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
            $valor2 = $mysqli->query($arl);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;

            while($AR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($AR[0]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($AR[1]),0,0,'C');

                $canTemp = "SELECT  COUNT(DISTINCT e.id_unico) FROM gn_novedad n
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE t.numeroidentificacion = '$AR[0]' AND af.tipo = 4 AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                            OR t.numeroidentificacion = '$AR[0]' AND af.tipo = 4  AND n.periodo = '$periodo' AND vr.estado=1 AND vr.estado = 2 AND vr.fecha 
                            BETWEEN '$retE[0]' AND '$retE[1]'";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vpat = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363 AND periodo = '$periodo'";
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                $pdf->Ln(5);
            } 

            $pdf->Ln(3);
            $pdf->Cell(110,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
            $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

            $vpat1 = "SELECT sum(valor) FROM `gn_novedad` WHERE  concepto =363 AND periodo = '$periodo' ";
            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R'); 

            ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////
        }else{  

            $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE c.tipoentidadcredito is not NULL";
            $valor2 = $mysqli->query($paraf);
      
            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
            $pdf->Ln(12);
     
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('NIT'),1,0,'C');
            $pdf->Cell(40,5, utf8_decode('Razón Social'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Empleados'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
    
            $pdf->Ln(8);

            $mc0=20;$mc1=40;$mc2=20;$mc3=30;

            while($PAR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($PAR[2]),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode($PAR[1]),0,0,'C');

                $canTemp = "SELECT COUNT(DISTINCT n.empleado) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE t.id_unico = '$PAR[0]'  AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                            OR  t.id_unico = '$PAR[0]'  AND n.periodo = '$periodo' AND vr.estado=1 AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";
        
                $Fsal = $mysqli->query($canTemp);
                $CanE = mysqli_fetch_row($Fsal);
                $pdf->cellfitscale($mc2,5,utf8_decode($CanE[0]),0,0,'R');

                $vpat = "SELECT SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico WHERE t.id_unico = '$PAR[0]'  AND n.periodo = '$periodo'  ";
        
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);
        
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                $pdf->Ln(5);
            }

            $pdf->Ln(3);
            $pdf->Cell(110,0.5,'',1);
            $pdf->Ln(4);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
            $pdf->cellfitscale($mc2,5,utf8_decode(''),0,0,'C');

            $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico WHERE  n.periodo = '$periodo'  AND c.tipoentidadcredito IS NOT NULL";
    
            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
        
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
        }

    }
}

ob_end_clean();
$pdf->Output(0,'Informe_Terceros ('.date('d/m/Y').').pdf',0);  
?>