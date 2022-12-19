<?php

session_start();
    
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 360);
ob_start();
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];

$consulta = "SELECT         lower(t.razonsocial) as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            ti.nombre as tnom,
                            t.numeroidentificacion tnum
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = '.$compania;

$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];


#$grupog = $_POST['sltGrupoG'];
$periodo  = $_POST['sltPeriodo'];
$tipof  = $_POST['sltTipoF'];

/*$grupog = 7;
$periodo = "";
$tipof = "";*/





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

  $TIF = "SELECT id_unico , nombre FROM gn_regimen_cesantias WHERE id_unico = $tipof";
  $FT = $mysqli->query($TIF);
  $TIPF = mysqli_fetch_row($FT);

  $TF = $TIPF[1];
}

$cmp = $mysqli->query($consulta);

    $nomcomp = "";
    $tipodoc = "";
    $numdoc = 0;
    
    while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = $fila['traz'];       
        $tipodoc = utf8_decode($fila['tnom']);       
        $numdoc  = utf8_decode($fila['tnum']);   
    }
$hoy = date('d-m-Y');
$hoy = trim($hoy, '"');
$fecha_div = explode("-", $hoy);
$anioh = $fecha_div[2];
$mesh = $fecha_div[1];
$diah = $fecha_div[0];
$hoy = $diah.'/'.$mesh.'/'.$anioh;

 $sql1 = "SELECT   e.codigointerno,
                  e.id_unico,
                  e.tercero,
                  t.id_unico,
                  t.numeroidentificacion, 
                  CONCAT(t.nombreuno,'  ',t.nombredos,'  ',t.apellidouno,'  ',t.apellidodos)
        
         FROM gn_empleado e
         LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
         WHERE e.id_unico !=2";

$cp      = $mysqli->query($sql1);


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


        // Logo
        if($ruta != '')
        {
        $this->Image('../'.$ruta,20,8,15);
        } 
        $this->SetFont('Arial','B',14);
    
        // Título
        $this->Cell(330,10,utf8_decode(ucwords($nomcomp)),0,0,'C');
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
        $this->Cell(330,10,utf8_decode('APORTES A CESANTIAS'),0,0,'C');
    
        $this->Ln(3);
        $this->SetFont('Arial','B',8);
        $this->SetX(0);

        $this->SetFont('Arial','B',9);
        $this->Cell(37,18,utf8_decode('PERIODO:'),0,0,'C');
        $this->SetFont('Arial','',9);
        $this->Cell(12,18,utf8_decode($PER),0,0,'C');
        $this->Cell(25.5,18,utf8_decode(''),0,0,'C');
        $this->SetFont('Arial','B',9);
        $this->Cell(45,18,utf8_decode('GRUPO GESTIÓN:'),0,0,'L');
        $this->SetFont('Arial','',9);
        $this->Cell(10,18,utf8_decode(''),0,0,'L');
        $this->Cell(15,18,utf8_decode('Todos'),0,0,'C');
        $this->Ln(4);
        $this->SetX(11);
        $this->SetFont('Arial','B',9);
        $this->Cell(18,18,utf8_decode('Fecha Inicial:'),0,0,'C');
        $this->SetFont('Arial','',9);
        $this->Cell(32,18,utf8_decode($FI),0,0,'C');
        $this->Cell(4,18,utf8_decode(''),0,0,'C');
        $this->SetFont('Arial','B',9);
        $this->Cell(44,18,utf8_decode('REGIMEN:'),0,0,'C');
        $this->Cell(6,18,utf8_decode(''),0,0,'C');
        $this->SetFont('Arial','',9);
        $this->Cell(5,18,utf8_decode(''),0,0,'C');
        $this->Cell(34,18,utf8_decode($TF),0,0,'C');
        $this->Ln(4);
        $this->SetX(13);
        $this->SetFont('Arial','B',9);
        $this->Cell(13,19,utf8_decode('Fecha Final:'),0,0,'C');
        $this->SetFont('Arial','',9);
        $this->Cell(32,18,utf8_decode($FF),0,0,'C');
        $this->Ln(10);


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
        $this->Cell(90,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
        $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
        $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
        $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}


// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');        

#valida que  todos los campos son vacios
if(empty($periodo) && empty($tipof)){

  $fondo = "SELECT id_unico, nombre FROM gn_regimen_cesantias";
  $Fond = $mysqli->query($fondo);

  $pdf->SetFont('Arial','B',10);
  

  while($F = mysqli_fetch_row($Fond)){
    
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetX(2);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(37,18,utf8_decode('TIPO :'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode($F[1]),0,0,'C');
    $pdf->Ln(13);

    $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial, t.id_unico FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 3 ORDER BY t.id_unico";
    $valor = $mysqli->query($salud);

    while($SAL = mysqli_fetch_row($valor)){

      

      $pdf->SetFont('Arial','',7);

      $concepto = "SELECT DISTINCT n.concepto, c.descripcion FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_periodo p ON n.periodo = p.id_unico WHERE p.tipoprocesonomina = 11 AND n.concepto !=1   ORDER BY n.concepto ASC ";
      $concept = $mysqli->query($concepto);
       
      $nconcepto = "SELECT COUNT(DISTINCT n.concepto) FROM gn_novedad n 
                      LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                      LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                      LEFT JOIN gn_periodo  p ON n.periodo  = p.id_unico
                      WHERE e.cesantias = '$F[0]' AND p.tipoprocesonomina = 11 AND n.concepto !=1  ";
      $nconcep = $mysqli->query($nconcepto);
      $ncon = mysqli_fetch_row($nconcep);
      $p =" SELECT *
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                            WHERE e.id_unico !=2 AND vr.estado !=2  AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]'";
      $r = $mysqli->query($p);
      $nr = mysqli_num_rows($r);

      if($nr > 0){
        $pdf->SetX(2);
          $pdf->SetFont('Arial','B',9);
          $pdf->Cell(30,5, utf8_decode('Fondo:'),0,0,'C');
          $pdf->Cell(50,5, utf8_decode($SAL[1]),0,0,'C');
          $pdf->Ln(6);
          $filas = 230 / $ncon[0] ;

          $pdf->SetFont('Arial','B',8);
          $cx = $pdf->GetX();
          $cy = $pdf->GetY();
          $pdf->Cell(10,5, utf8_decode('Cod Int'),0,0,'C');
          $pdf->Cell(48,5, utf8_decode('Nombre'),0,0,'C');
          $pdf->Cell(18,5, utf8_decode('Básico'),0,0,'C');
         
          $h2 = 0;
          $h = 0;
          $alto = 0;

          while ($Tcon = mysqli_fetch_row($concept)){
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','B',8);
            $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
        
            if($h > $h2){
              $alto = $h;
              $h2 = $h;
            }else{
              $h2 = $h;
            }
            $pdf->SetXY($x+$filas,$y);
          }

          $pdf->SetXY($cx,$cy);
          $pdf->Cell(10,$alto, utf8_decode(''),1,0,'C');
          $pdf->Cell(48,$alto, utf8_decode(''),1,0,'C');
          $pdf->Cell(18,$alto, utf8_decode(''),1,0,'C');
          $con1 = $mysqli->query($concepto);

          while ($Tcon = mysqli_fetch_row($con1)){
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','',8);
            $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
            $pdf->SetXY($x+$filas,$y);
          }

          $pdf->Ln($alto);
          $pdf->SetFont('Arial','',8);
          $sqlemp =" SELECT DISTINCT  e.id_unico, 
                                        e.codigointerno, 
                                        e.tercero, 
                                        t.id_unico,
                                        t.numeroidentificacion, 
                                        IF(CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos) 
                                           IS NULL OR CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos) = '',
                                           (t.razonsocial),
                                           CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos)) AS NOMBRE,
                                        ca.salarioactual 
                                FROM gn_empleado e 
                                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                                LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                                WHERE e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL   AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]'";

          $emp = $mysqli->query($sqlemp); 

          $con2 = $mysqli->query($concepto);

          while($emple =  mysqli_fetch_row($emp)){
            $pdf->Cellfitscale(10,8, utf8_decode($emple[1]),0,0,'L');
            $pdf->Cellfitscale(48,8, utf8_decode($emple[5]),0,0,'L');
            $pdf->Cellfitscale(18,8, utf8_decode(number_format($emple[6],0,'.',',')),0,0,'R');
            $x =$pdf->GetX();  
            $y =$pdf->GetY(); 

            while($CO = mysqli_fetch_row($con2)){
              $novco = "SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                        WHERE c.id_unico = '$CO[0]' AND e.id_unico = '$emple[0]' AND p.tipoprocesonomina = '11' ";
                
              $cnov = $mysqli->query($novco);
              $num_con = mysqli_num_rows($cnov);
                  
              if($num_con > 0){
                $novec = mysqli_fetch_row($cnov);
                $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],0,'.',',')),0,0,'R');
              }else{
                $pdf->Cellfitscale($filas,8, utf8_decode('0'),0,0,'R');  
              }
            }

            
            $pdf->Ln(8);

            $con2 = $mysqli->query($concepto);
          }

          $pdf->SetFont('Arial','B',8);
          $pdf->Cell(58,8, utf8_decode('Total:'),1,0,'C');

          $saltot = "SELECT  SUM(c.salarioactual) 
                   FROM gn_empleado e 
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico 
                   LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico 
                   LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                   LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                   WHERE vr.estado != 2  AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]'";
        
          $tosal = $mysqli->query($saltot);
          $TOTS = mysqli_fetch_row($tosal);
          $pdf->Cellfitscale(18,8, utf8_decode(number_format($TOTS[0],0,'.',',')),1,0,'R');

          $con3 = "SELECT DISTINCT      n.concepto, 
                                       c.descripcion,
                                       c.id_unico
                  FROM gn_novedad n 
                  LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                  LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                  LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico
                  LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                  LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                  WHERE af.tercero =  '$SAL[2]' AND e.cesantias = '$F[0]' AND p.tipoprocesonomina = '11' AND n.concepto != 1
                  ORDER BY c.clase,c.id_unico";

          $co2 = $mysqli->query($con3);

          while($co3 = mysqli_fetch_row($co2)){
            $sumco = "SELECT SUM(n.valor), n.concepto 
                         FROM gn_novedad n 
                         LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                        WHERE c.id_unico = '$co3[2]' AND vr.estado != 2 AND e.cesantias = '$F[0]' AND p.tipoprocesonomina = '11' AND af.tercero =  '$SAL[2]' AND af.tipo = 3";
      
            $snov = $mysqli->query($sumco);
            
            while($sumanov = mysqli_fetch_row($snov)){

              if($sumanov[1] == 7){
                $pdf->Cellfitscale($filas,8, utf8_decode('0'),1,0,'R');  
              }else{
                $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],0,'.',',')),1,0,'R');  
              }    
            }
            $snov = $mysqli->query($sumco);
          }

          $emp = $mysqli->query($sqlemp); 
          $pdf->Ln(10);

        }
        $pdf->Ln(10);

      }
      
  }
 
#valida que la unidad ejecutora no este vacia    
}elseif(!empty ($tipof) && empty($periodo)) {
    
  $fondo = "SELECT id_unico, nombre FROM gn_regimen_cesantias WHERE id_unico = '$tipof'";
  $Fond = $mysqli->query($fondo);

  $pdf->SetFont('Arial','B',10);
  

  while($F = mysqli_fetch_row($Fond)){
    
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetX(0);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(37,18,utf8_decode('TIPO :'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode($F[1]),0,0,'C');
    $pdf->Ln(13);

    $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial, t.id_unico FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 3 ORDER BY t.id_unico";
    $valor = $mysqli->query($salud);

    while($SAL = mysqli_fetch_row($valor)){

      

      $pdf->SetFont('Arial','',7);

      $concepto = "SELECT DISTINCT n.concepto, c.descripcion FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_periodo p ON n.periodo = p.id_unico WHERE p.tipoprocesonomina = 11 AND n.concepto !=1   ORDER BY n.concepto ASC ";
      $concept = $mysqli->query($concepto);
       
      $nconcepto = "SELECT COUNT(DISTINCT n.concepto) FROM gn_novedad n 
                      LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                      LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                      LEFT JOIN gn_periodo  p ON n.periodo  = p.id_unico
                      WHERE e.cesantias = '$F[0]' AND p.tipoprocesonomina = 11 AND n.concepto !=1  ";
      $nconcep = $mysqli->query($nconcepto);
      $ncon = mysqli_fetch_row($nconcep);
      $p =" SELECT *
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                            WHERE e.id_unico !=2 AND vr.estado !=2  AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]'";
      $r = $mysqli->query($p);
      $nr = mysqli_num_rows($r);

      if($nr > 0){
        $pdf->SetX(2);
          $pdf->SetFont('Arial','B',9);
          $pdf->Cell(30,5, utf8_decode('Fondo:'),0,0,'C');
          $pdf->Cell(50,5, utf8_decode($SAL[1]),0,0,'C');
          $pdf->Cell(30,5, utf8_decode('NIT:'),0,0,'C');
          $pdf->Cell(50,5, utf8_decode($SAL[0]),0,0,'C');
          $pdf->Ln(6);
          $filas = 230 / $ncon[0] ;

          $pdf->SetFont('Arial','B',8);
          $cx = $pdf->GetX();
          $cy = $pdf->GetY();
          $pdf->Cell(10,5, utf8_decode('Cod Int'),0,0,'C');
          $pdf->Cell(48,5, utf8_decode('Nombre'),0,0,'C');
          $pdf->Cell(18,5, utf8_decode('Básico'),0,0,'C');
         
          $h2 = 0;
          $h = 0;
          $alto = 0;

          while ($Tcon = mysqli_fetch_row($concept)){
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','B',8);
            $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
        
            if($h > $h2){
              $alto = $h;
              $h2 = $h;
            }else{
              $h2 = $h;
            }
            $pdf->SetXY($x+$filas,$y);
          }

          $pdf->SetXY($cx,$cy);
          $pdf->Cell(10,$alto, utf8_decode(''),1,0,'C');
          $pdf->Cell(48,$alto, utf8_decode(''),1,0,'C');
          $pdf->Cell(18,$alto, utf8_decode(''),1,0,'C');
          $con1 = $mysqli->query($concepto);

          while ($Tcon = mysqli_fetch_row($con1)){
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','',8);
            $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
            $pdf->SetXY($x+$filas,$y);
          }

          $pdf->Ln($alto);
          $pdf->SetFont('Arial','',8);
          $sqlemp =" SELECT DISTINCT  e.id_unico, 
                                        e.codigointerno, 
                                        e.tercero, 
                                        t.id_unico,
                                        t.numeroidentificacion, 
                                        IF(CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos) 
                                           IS NULL OR CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos) = '',
                                           (t.razonsocial),
                                           CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos)) AS NOMBRE,
                                        ca.salarioactual 
                                FROM gn_empleado e 
                                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                                LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                                WHERE e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL   AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]'";

          $emp = $mysqli->query($sqlemp); 

          $con2 = $mysqli->query($concepto);

          while($emple =  mysqli_fetch_row($emp)){
            $pdf->Cellfitscale(10,8, utf8_decode($emple[1]),0,0,'L');
            $pdf->Cellfitscale(48,8, utf8_decode($emple[5]),0,0,'L');
            $pdf->Cellfitscale(18,8, utf8_decode(number_format($emple[6],0,'.',',')),0,0,'R');
            $x =$pdf->GetX();  
            $y =$pdf->GetY(); 

            while($CO = mysqli_fetch_row($con2)){
              $novco = "SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                        WHERE c.id_unico = '$CO[0]' AND e.id_unico = '$emple[0]' AND p.tipoprocesonomina = '11' ";
                
              $cnov = $mysqli->query($novco);
              $num_con = mysqli_num_rows($cnov);
                  
              if($num_con > 0){
                $novec = mysqli_fetch_row($cnov);
                $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],0,'.',',')),0,0,'R');
              }else{
                $pdf->Cellfitscale($filas,8, utf8_decode('0'),0,0,'R');  
              }
            }

            
            $pdf->Ln(8);

            $con2 = $mysqli->query($concepto);
          }

          $pdf->SetFont('Arial','B',8);
          $pdf->Cell(58,8, utf8_decode('Total:'),1,0,'C');

          $saltot = "SELECT  SUM(c.salarioactual) 
                   FROM gn_empleado e 
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico 
                   LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico 
                   LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                   LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                   WHERE vr.estado != 2  AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]'";
        
          $tosal = $mysqli->query($saltot);
          $TOTS = mysqli_fetch_row($tosal);
          $pdf->Cellfitscale(18,8, utf8_decode(number_format($TOTS[0],0,'.',',')),1,0,'R');

          $con3 = "SELECT DISTINCT      n.concepto, 
                                       c.descripcion,
                                       c.id_unico
                  FROM gn_novedad n 
                  LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                  LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                  LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico
                  LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                  LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                  WHERE af.tercero =  '$SAL[2]' AND e.cesantias = '$F[0]' AND p.tipoprocesonomina = '11' AND n.concepto != 1
                  ORDER BY c.clase,c.id_unico";

          $co2 = $mysqli->query($con3);

          while($co3 = mysqli_fetch_row($co2)){
            $sumco = "SELECT SUM(n.valor), n.concepto 
                         FROM gn_novedad n 
                         LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                        WHERE c.id_unico = '$co3[2]' AND vr.estado != 2 AND e.cesantias = '$F[0]' AND p.tipoprocesonomina = '11' AND af.tercero =  '$SAL[2]' ";
      
            $snov = $mysqli->query($sumco);
            
            while($sumanov = mysqli_fetch_row($snov)){

              if($sumanov[1] == 7){
                $pdf->Cellfitscale($filas,8, utf8_decode('0'),1,0,'R');  
              }else{
                $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],0,'.',',')),1,0,'R');  
              }    
            }
            $snov = $mysqli->query($sumco);
          }

          $emp = $mysqli->query($sqlemp); 
          $pdf->Ln(10);

        }
        $pdf->Ln(10);

      }
      
  }
}elseif(!empty($periodo) && empty($tipof)){
  
  $fondo = "SELECT id_unico, nombre FROM gn_regimen_cesantias";
  $Fond = $mysqli->query($fondo);

  $pdf->SetFont('Arial','B',10);
  

  while($F = mysqli_fetch_row($Fond)){
    
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetX(0);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(37,18,utf8_decode('TIPO :'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode($F[1]),0,0,'C');
    $pdf->Ln(13);

    $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial, t.id_unico FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 3 ORDER BY t.id_unico";
    $valor = $mysqli->query($salud);

    while($SAL = mysqli_fetch_row($valor)){

      

      $pdf->SetFont('Arial','',7);

      $concepto = "SELECT DISTINCT n.concepto, c.descripcion FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_periodo p ON n.periodo = p.id_unico WHERE p.tipoprocesonomina = 11 AND n.concepto !=1 AND n.periodo = '$periodo'  ORDER BY n.concepto ASC  ";
      $concept = $mysqli->query($concepto);
       
      $nconcepto = "SELECT COUNT(DISTINCT n.concepto) FROM gn_novedad n 
                      LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                      LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                      LEFT JOIN gn_periodo  p ON n.periodo  = p.id_unico
                      WHERE e.cesantias = '$F[0]' AND p.tipoprocesonomina = 11 AND n.concepto !=1 AND n.periodo = '$periodo' ";
      $nconcep = $mysqli->query($nconcepto);
      $ncon = mysqli_fetch_row($nconcep);
      $p =" SELECT *
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            WHERE e.id_unico !=2 AND vr.estado !=2  AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]' AND n.periodo = '$periodo'";
      $r = $mysqli->query($p);
      $nr = mysqli_num_rows($r);

      if($nr > 0){
        $pdf->SetX(2);
          $pdf->SetFont('Arial','B',9);
          $pdf->Cell(30,5, utf8_decode('Fondo:'),0,0,'C');
          $pdf->Cell(50,5, utf8_decode($SAL[1]),0,0,'C');
          $pdf->Cell(30,5, utf8_decode('NIT:'),0,0,'C');
          $pdf->Cell(50,5, utf8_decode($SAL[0]),0,0,'C');
          $pdf->Ln(6);
          $filas = 230 / $ncon[0] ;

          $pdf->SetFont('Arial','B',8);
          $cx = $pdf->GetX();
          $cy = $pdf->GetY();
          $pdf->Cell(10,5, utf8_decode('Cod Int'),0,0,'C');
          $pdf->Cell(48,5, utf8_decode('Nombre'),0,0,'C');
          $pdf->Cell(18,5, utf8_decode('Básico'),0,0,'C');
         
          $h2 = 0;
          $h = 0;
          $alto = 0;

          while ($Tcon = mysqli_fetch_row($concept)){
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','B',8);
            $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
        
            if($h > $h2){
              $alto = $h;
              $h2 = $h;
            }else{
              $h2 = $h;
            }
            $pdf->SetXY($x+$filas,$y);
          }

          $pdf->SetXY($cx,$cy);
          $pdf->Cell(10,$alto, utf8_decode(''),1,0,'C');
          $pdf->Cell(48,$alto, utf8_decode(''),1,0,'C');
          $pdf->Cell(18,$alto, utf8_decode(''),1,0,'C');
          $con1 = $mysqli->query($concepto);

          while ($Tcon = mysqli_fetch_row($con1)){
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','',8);
            $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
            $pdf->SetXY($x+$filas,$y);
          }

          $pdf->Ln($alto);
          $pdf->SetFont('Arial','',8);
          $sqlemp =" SELECT DISTINCT  e.id_unico, 
                                        e.codigointerno, 
                                        e.tercero, 
                                        t.id_unico,
                                        t.numeroidentificacion, 
                                        IF(CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos) 
                                           IS NULL OR CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos) = '',
                                           (t.razonsocial),
                                           CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos)) AS NOMBRE,
                                        ca.salarioactual 
                                FROM gn_empleado e 
                                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                                LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                WHERE e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL   AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]' AND n.periodo = '$periodo'";

          $emp = $mysqli->query($sqlemp); 

          $con2 = $mysqli->query($concepto);

          while($emple =  mysqli_fetch_row($emp)){
            $pdf->Cellfitscale(10,8, utf8_decode($emple[1]),0,0,'L');
            $pdf->Cellfitscale(48,8, utf8_decode($emple[5]),0,0,'L');
            $pdf->Cellfitscale(18,8, utf8_decode(number_format($emple[6],0,'.',',')),0,0,'R');
            $x =$pdf->GetX();  
            $y =$pdf->GetY(); 

            while($CO = mysqli_fetch_row($con2)){
              $novco = "SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                        WHERE c.id_unico = '$CO[0]' AND e.id_unico = '$emple[0]' AND p.tipoprocesonomina = '11' AND n.periodo = '$periodo' ";
                
              $cnov = $mysqli->query($novco);
              $num_con = mysqli_num_rows($cnov);
                  
              if($num_con > 0){
                $novec = mysqli_fetch_row($cnov);
                $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],0,'.',',')),0,0,'R');
              }else{
                $pdf->Cellfitscale($filas,8, utf8_decode('0'),0,0,'R');  
              }
            }

            
            $pdf->Ln(8);

            $con2 = $mysqli->query($concepto);
          }

          $pdf->SetFont('Arial','B',8);
          $pdf->Cell(58,8, utf8_decode('Total:'),1,0,'C');

           $saltot = "SELECT  SUM(c.salarioactual) 
                   FROM gn_empleado e 
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico 
                   LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico 
                   LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                   LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                    
                   WHERE vr.estado != 2  AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]' ";
        
          $tosal = $mysqli->query($saltot);
          $TOTS = mysqli_fetch_row($tosal);
          $pdf->Cellfitscale(18,8, utf8_decode(number_format($TOTS[0],0,'.',',')),1,0,'R');

          $con3 = "SELECT DISTINCT      n.concepto, 
                                       c.descripcion,
                                       c.id_unico
                  FROM gn_novedad n 
                  LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                  LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                  LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico
                  LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                  LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                  WHERE af.tercero =  '$SAL[2]' AND e.cesantias = '$F[0]' AND p.tipoprocesonomina = '11' AND n.concepto != 1 AND n.periodo = '$periodo'
                  ORDER BY c.clase,c.id_unico";

          $co2 = $mysqli->query($con3);

          while($co3 = mysqli_fetch_row($co2)){
            $sumco = "SELECT SUM(n.valor), n.concepto 
                         FROM gn_novedad n 
                         LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                        WHERE c.id_unico = '$co3[2]' AND vr.estado != 2 AND e.cesantias = '$F[0]' AND p.tipoprocesonomina = '11' AND n.periodo = '$periodo' AND af.tercero =  '$SAL[2]' AND af.tipo = 3";
      
            $snov = $mysqli->query($sumco);
            
            while($sumanov = mysqli_fetch_row($snov)){

              if($sumanov[1] == 7){
                $pdf->Cellfitscale($filas,8, utf8_decode('0'),1,0,'R');  
              }else{
                $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],0,'.',',')),1,0,'R');  
              }    
            }
            $snov = $mysqli->query($sumco);
          }

          $emp = $mysqli->query($sqlemp); 
          $pdf->Ln(10);

        }
        $pdf->Ln(10);

      }
      
  }

}else{
      
  $fondo = "SELECT id_unico, nombre FROM gn_regimen_cesantias WHERE id_unico = '$tipof'";
  $Fond = $mysqli->query($fondo);

  $pdf->SetFont('Arial','B',10);
  

  while($F = mysqli_fetch_row($Fond)){
    
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetX(0);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(37,18,utf8_decode('TIPO :'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode($F[1]),0,0,'C');
    $pdf->Ln(13);

    $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial, t.id_unico FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 3 ORDER BY t.id_unico";
    $valor = $mysqli->query($salud);

    while($SAL = mysqli_fetch_row($valor)){

      

      $pdf->SetFont('Arial','',7);

      $concepto = "SELECT DISTINCT n.concepto, c.descripcion FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_periodo p ON n.periodo = p.id_unico WHERE p.tipoprocesonomina = 11 AND n.concepto !=1 AND n.periodo = '$periodo'  ORDER BY n.concepto ASC  ";
      $concept = $mysqli->query($concepto);
       
      $nconcepto = "SELECT COUNT(DISTINCT n.concepto) FROM gn_novedad n 
                      LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                      LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                      LEFT JOIN gn_periodo  p ON n.periodo  = p.id_unico
                      WHERE e.cesantias = '$F[0]' AND p.tipoprocesonomina = 11 AND n.concepto !=1 AND n.periodo = '$periodo' ";
      $nconcep = $mysqli->query($nconcepto);
      $ncon = mysqli_fetch_row($nconcep);
      $p =" SELECT *
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            WHERE e.id_unico !=2 AND vr.estado !=2  AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]' AND n.periodo = '$periodo'";
      $r = $mysqli->query($p);
      $nr = mysqli_num_rows($r);

      if($nr > 0){
        $pdf->SetX(2);
          $pdf->SetFont('Arial','B',9);
          $pdf->Cell(30,5, utf8_decode('Fondo:'),0,0,'C');
          $pdf->Cell(50,5, utf8_decode($SAL[1]),0,0,'C');
          $pdf->Cell(30,5, utf8_decode('NIT:'),0,0,'C');
          $pdf->Cell(50,5, utf8_decode($SAL[0]),0,0,'C');
          $pdf->Ln(6);
          $filas = 230 / $ncon[0] ;

          $pdf->SetFont('Arial','B',8);
          $cx = $pdf->GetX();
          $cy = $pdf->GetY();
          $pdf->Cell(10,5, utf8_decode('Cod Int'),0,0,'C');
          $pdf->Cell(48,5, utf8_decode('Nombre'),0,0,'C');
          $pdf->Cell(18,5, utf8_decode('Básico'),0,0,'C');
         
          $h2 = 0;
          $h = 0;
          $alto = 0;

          while ($Tcon = mysqli_fetch_row($concept)){
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','B',8);
            $pdf->MultiCell($filas,5, utf8_decode(ucwords(mb_strtolower($Tcon[1]))),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
        
            if($h > $h2){
              $alto = $h;
              $h2 = $h;
            }else{
              $h2 = $h;
            }
            $pdf->SetXY($x+$filas,$y);
          }

          $pdf->SetXY($cx,$cy);
          $pdf->Cell(10,$alto, utf8_decode(''),1,0,'C');
          $pdf->Cell(48,$alto, utf8_decode(''),1,0,'C');
          $pdf->Cell(18,$alto, utf8_decode(''),1,0,'C');
          $con1 = $mysqli->query($concepto);

          while ($Tcon = mysqli_fetch_row($con1)){
            $x =$pdf->GetX();
            $y =$pdf->GetY(); 
            $pdf->SetFont('Arial','',8);
            $pdf->Cell($filas,$alto, utf8_decode(),1,'C');
            $pdf->SetXY($x+$filas,$y);
          }

          $pdf->Ln($alto);
          $pdf->SetFont('Arial','',8);
          $sqlemp =" SELECT DISTINCT  e.id_unico, 
                                        e.codigointerno, 
                                        e.tercero, 
                                        t.id_unico,
                                        t.numeroidentificacion, 
                                        IF(CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos) 
                                           IS NULL OR CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos) = '',
                                           (t.razonsocial),
                                           CONCAT_WS(' ',
                                           t.nombreuno,
                                           t.nombredos,
                                           t.apellidouno,
                                           t.apellidodos)) AS NOMBRE,
                                        ca.salarioactual 
                                FROM gn_empleado e 
                                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                                LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                WHERE e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL   AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]' AND n.periodo = '$periodo'";

          $emp = $mysqli->query($sqlemp); 

          $con2 = $mysqli->query($concepto);

          while($emple =  mysqli_fetch_row($emp)){
            $pdf->Cellfitscale(10,8, utf8_decode($emple[1]),0,0,'L');
            $pdf->Cellfitscale(48,8, utf8_decode($emple[5]),0,0,'L');
            $pdf->Cellfitscale(18,8, utf8_decode(number_format($emple[6],0,'.',',')),0,0,'R');
            $x =$pdf->GetX();  
            $y =$pdf->GetY(); 

            while($CO = mysqli_fetch_row($con2)){
              $novco = "SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                        WHERE c.id_unico = '$CO[0]' AND e.id_unico = '$emple[0]' AND p.tipoprocesonomina = '11' AND n.periodo = '$periodo' ";
                
              $cnov = $mysqli->query($novco);
              $num_con = mysqli_num_rows($cnov);
                  
              if($num_con > 0){
                $novec = mysqli_fetch_row($cnov);
                $pdf->Cellfitscale($filas,8, utf8_decode(number_format($novec[1],0,'.',',')),0,0,'R');
              }else{
                $pdf->Cellfitscale($filas,8, utf8_decode('0'),0,0,'R');  
              }
            }

            
            $pdf->Ln(8);

            $con2 = $mysqli->query($concepto);
          }

          $pdf->SetFont('Arial','B',8);
          $pdf->Cell(58,8, utf8_decode('Total:'),1,0,'C');

          $saltot = "SELECT  SUM(c.salarioactual) 
                   FROM gn_empleado e 
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico 
                   LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico 
                   LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                   LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                   
                   WHERE vr.estado != 2  AND e.cesantias = '$F[0]' AND af.tercero = '$SAL[2]' ";
        
          $tosal = $mysqli->query($saltot);
          $TOTS = mysqli_fetch_row($tosal);
          $pdf->Cellfitscale(18,8, utf8_decode(number_format($TOTS[0],0,'.',',')),1,0,'R');

          $con3 = "SELECT DISTINCT      n.concepto, 
                                       c.descripcion,
                                       c.id_unico
                  FROM gn_novedad n 
                  LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                  LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                  LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico
                  LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                  LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                  WHERE af.tercero =  '$SAL[2]' AND e.cesantias = '$F[0]' AND p.tipoprocesonomina = '11' AND n.concepto != 1 AND n.periodo = '$periodo'
                  ORDER BY c.clase,c.id_unico";

          $co2 = $mysqli->query($con3);

          while($co3 = mysqli_fetch_row($co2)){
            $sumco = "SELECT SUM(n.valor), n.concepto 
                         FROM gn_novedad n 
                         LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico
                        WHERE c.id_unico = '$co3[0]' AND vr.estado != 2 AND e.cesantias = '$F[0]' AND p.tipoprocesonomina = '11' AND n.periodo = '$periodo' AND af.tercero =  '$SAL[2]' AND af.tipo = 3";
      
            $snov = $mysqli->query($sumco);
            
            while($sumanov = mysqli_fetch_row($snov)){

              if($sumanov[1] == 7){
                $pdf->Cellfitscale($filas,8, utf8_decode('0'),1,0,'R');  
              }else{
                $pdf->Cellfitscale($filas,8, utf8_decode(number_format($sumanov[0],0,'.',',')),1,0,'R');  
              }    
            }
            $snov = $mysqli->query($sumco);
          }

          $emp = $mysqli->query($sqlemp); 
          $pdf->Ln(10);

        }
        $pdf->Ln(10);

      }
      
  }
}  




ob_end_clean();
$pdf->Output(0,'Informe_Terceros ('.date('d/m/Y').').pdf',0);     
  
?>