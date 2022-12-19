<?php
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 360);
session_start();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
ob_start();

$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre
  FROM gf_tercero ter
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico
  WHERE ter.id_unico = '.$compania;

$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];

$grupog     = $_POST['sltGrupoG'];
$periodo    = $_POST['sltPeriodo'];
$tipof      = $_POST['sltTipoF'];


if(empty($grupog) || $grupog == ""){
    $GRUP = "Todos";
}else{
    $G  = "SELECT id_unico, nombre FROM gn_grupo_gestion WHERE id_unico = $grupog";
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

    $fecha_div  = explode("-", $PERI[2]);
    $anion      = $fecha_div[0];
    $mesn       = $fecha_div[1];
    $dian       = $fecha_div[2];
    $FI         = $dian.'/'.$mesn.'/'.$anion;
    $fecha_div2 = explode("-", $PERI[3]);
    $anion1     = $fecha_div2[0];
    $mesn1      = $fecha_div2[1];
    $dian1      = $fecha_div2[2];
    $FF         = $dian1.'/'.$mesn1.'/'.$anion1;

}

if(empty($tipof)|| $tipof ==""){
    $TF = "Todos";
}else{
    $TIF    = "SELECT id_unico , nombre FROM gn_tipo_fondo WHERE id_unico = $tipof";
    $FT     = $mysqli->query($TIF);
    $TIPF   = mysqli_fetch_row($FT);
    $TF     = $TIPF[1];
}

$consulta = "SELECT  lower(t.razonsocial) as traz,
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



$hoy        = date('d-m-Y');
$hoy        = trim($hoy, '"');
$fecha_div  = explode("-", $hoy);
$anioh      = $fecha_div[2];
$mesh       = $fecha_div[1];
$diah       = $fecha_div[0];
$hoy        = $diah.'/'.$mesh.'/'.$anioh;

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
    $this->SetFont('Arial','B',12);
    $this->SetX(20);
    $this->Cell(170,5,utf8_decode(ucwords($nomcomp)),0,0,'C');
    $this->Ln(5);
    $this->SetFont('Arial','',8);
    $this->SetX(20);
    $this->Cell(170, 5,$tipodoc.': '.$numdoc,0,0,'C');
    $this->Ln(5);
    $this->SetFont('Arial','B',12);
    $this->Cell(190,5,utf8_decode('APORTES Y PARAFISCALES'),0,0,'C');
    $this->Ln(3);
    $this->SetFont('Arial','B',11);
    $this->Cell(190,6,utf8_decode('(DETALLADO)'),0,0,'C');
    // Salto de línea
    $this->Ln(3);


    $this->SetFont('Arial','B',9);
    $this->Cell(40,18,utf8_decode('PERIODO:'),0,0,'L');
    $this->SetFont('Arial','',9);
    $this->Cell(20,18,utf8_decode($PER),0,0,'L');
    $this->Cell(20,18,utf8_decode(''),0,0,'C');
    $this->SetFont('Arial','B',9);
    $this->Cell(40,18,utf8_decode('GRUPO GESTIÓN:'),0,0,'L');
    $this->SetFont('Arial','',9);
    $this->Cell(40,18,utf8_decode($GRUP),0,0,'L');
    $this->Ln(4);

    $this->SetFont('Arial','B',9);
    $this->Cell(40,18,utf8_decode('FECHA INICIAL:'),0,0,'L');
    $this->SetFont('Arial','',9);
    $this->Cell(20,18,utf8_decode($FI),0,0,'L');
    $this->Cell(20,18,utf8_decode(''),0,0,'L');
    $this->SetFont('Arial','B',9);
    $this->Cell(40,18,utf8_decode('TIPO FONDO:'),0,0,'L');
    $this->SetFont('Arial','',9);
    $this->Cell(40,18,utf8_decode($TF),0,0,'L');
    $this->Ln(4);
    $this->SetFont('Arial','B',9);
    $this->Cell(40,18,utf8_decode('FECHA FINAL:'),0,0,'L');
    $this->SetFont('Arial','',9);
    $this->Cell(20,18,utf8_decode($FF),0,0,'L');
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
            }
        }


// Creación del objeto de la clase heredada
$pdf = new PDF('P','mm','letter');
$nb=$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',8);

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
    $pdf->Ln(13);

    while($SAL = mysqli_fetch_row($valor)){

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(20,5,utf8_decode('NIT:'),0,0,'C');
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale(20,5,utf8_decode($SAL[0]),0,0,'C');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30,5,utf8_decode('RAZON SOCIAL:'),0,0,'C');
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale(25,5,utf8_decode($SAL[1]),0,0,'C');
        $pdf->Ln(4);

        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
        $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
        $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
        $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
        $pdf->Ln(5);

        $mc0=20;$mc1=60;$mc2=20;$mc3=25;$mc4=25;$mc5=30;

        $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion

                FROM gn_empleado e
                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]'";

        $resemp = $mysqli->query($sqlemp);

        while($ConE = mysqli_fetch_row($resemp)){

            $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
            $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

            

            $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416)";
            $bas = $mysqli->query($base);
            $B = mysqli_fetch_row($bas);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

            #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
            $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'";

            $vaem = $mysqli->query($vemple);
            $valE = mysqli_fetch_row($vaem);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

            #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
            $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]'";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);

            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

            #calcula el total de los dos aportes y los agrupa por cada entidad de salud
            $VTot = "SELECT SUM(n.valor)

                FROM gn_novedad n
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND c.clase != 6";

            $valT = $mysqli->query($VTot);
            $TOT = mysqli_fetch_row($valT);
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
            $pdf->Ln(3);
        }

        $pdf->Ln(2);
        $pdf->Cell(180,0.5,'',1);
        $pdf->Ln(1);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

        $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$SAL[0]'";
        $ter = $mysqli->query($Terce);
        $T = mysqli_fetch_row($ter);

        $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                    . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                   
                    . "WHERE af.tipo = 1 AND af.tercero = '$T[0]'   AND n.concepto = 2";
        $bas1 = $mysqli->query($base1);
        $B1 = mysqli_fetch_row($bas1);

        $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

        $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]'";
        $VToEM = $mysqli->query($ToEM);
        $VAEMP = mysqli_fetch_row($VToEM);
        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[0],2,'.',',')),0,0,'R');

        $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);

        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

        $VTot1 = "SELECT SUM(n.valor)

              FROM gn_novedad n
              LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
              LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
              LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
              LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
              WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND c.clase != 6";

        $valT1 = $mysqli->query($VTot1);
        $TOT1 = mysqli_fetch_row($valT1);
        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');
        $pdf->Ln(10);
    } #FINALIZA EL CICLO DEL CARGUE DE DATOS DEL FONOD DE SALUD Y CALCULA LOS TOTALES

    ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////

    $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
    $valor1 = $mysqli->query($pension);

    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(6);

    $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
    $pdf->Ln(8);

    while($PEN = mysqli_fetch_row($valor1)){
        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale(20,18,utf8_decode($PEN[0]),0,0,'C');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale(25,18,utf8_decode($PEN[1]),0,0,'C');
        $pdf->Ln(12);

        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
        $pdf->Cell(50,5, utf8_decode('Empleado'),1,0,'C');
        $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
        $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
        $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Cell(25,5, utf8_decode('Fondo Solid'),1,0,'C');
        $pdf->Cell(25,5, utf8_decode('Total'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=50;$mc2=20;$mc3=25;$mc4=25;$mc5=25;$mc6=25;

        $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion

                FROM gn_empleado e
                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' ";

        $resemp = $mysqli->query($sqlemp);

        while($ConE = mysqli_fetch_row($resemp)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
            $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

            $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' AND concepto IN (78,416)";
            $bas = $mysqli->query($base);
            $B = mysqli_fetch_row($bas);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

            $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' ";

            $vaem = $mysqli->query($vemple);
            $valE = mysqli_fetch_row($vaem);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

            $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]'";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);

            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

            $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'";

            $foso = $mysqli->query($Fsol);
            $SolF = mysqli_fetch_row($foso);

            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[0],2,'.',',')),0,0,'R');

            $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND c.clase != 6";

            $valT = $mysqli->query($VTot);
            $TOT = mysqli_fetch_row($valT);
            $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');

            $pdf->Ln(3);
        } # FINALIZA EL CICLO DE LOS EMPLEADOS PARA LA PENSION

        $pdf->Ln(2);
        $pdf->Cell(190,0.5,'',1);
        $pdf->Ln(1);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

        $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$PEN[0]'";
            $ter = $mysqli->query($Terce);
            $T = mysqli_fetch_row($ter);

            $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                    . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                   
                    . "WHERE af.tipo = 2 AND af.tercero = '$T[0]'  AND n.concepto = 2";
            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);
            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

        $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' ";

        $vaem1 = $mysqli->query($vemple1);
        $valE1 = mysqli_fetch_row($vaem1);

        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[0],2,'.',',')),0,0,'R');

        $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);

        $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

        $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' ";

        $foso1 = $mysqli->query($Fsol1);
        $SolF1 = mysqli_fetch_row($foso1);

        $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[0],2,'.',',')),0,0,'R');

        $VTot12 = "SELECT SUM(n.valor)  FROM gn_novedad n
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND c.clase != 6";

        $VAl12  = $mysqli->query($VTot12);
        $TOVAL12 = mysqli_fetch_row($VAl12);
        $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');
        $pdf->Ln(10);

    }


    ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
    $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
    $valor2 = $mysqli->query($arl);

    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(6);

    $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
    $pdf->Ln(8);


    while($AR = mysqli_fetch_row($valor2)){

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
        $pdf->Ln(12);
        $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
        $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=60;$mc2=30;$mc3=30;

        $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion

                FROM gn_empleado e
                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2";

        $resemp = $mysqli->query($sqlemp);

        while($ConE = mysqli_fetch_row($resemp)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
            $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

            $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto = IN (78,416)";
            $bas = $mysqli->query($base);
            $B = mysqli_fetch_row($bas);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

            $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =363 AND empleado = '$ConE[0]'";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
            $pdf->Ln(3);
        }

        $pdf->Ln(1);
        $pdf->Cell(140,0.5,'',1);
        $pdf->Ln(2);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

        $base = "SELECT SUM(valor) FROM gn_novedad  WHERE  concepto = IN (78,416)";
        $bas = $mysqli->query($base);
        $B = mysqli_fetch_row($bas);

        $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

        $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n WHERE n.concepto =363 ";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);

        $pdf->cellfitscale($mc2,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
    }

    ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

    $caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
    $valor2 = $mysqli->query($caja);

    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(6);

    $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode('CAJA DE COMPENSACION'),0,0,'C');
    $pdf->Ln(8);


    while($AR = mysqli_fetch_row($valor2)){

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
        $pdf->Ln(12);
        $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
        $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
        $pdf->Ln(8);

        $mc0=20;$mc1=60;$mc2=30;$mc3=30;

        $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion

                FROM gn_empleado e
                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                WHERE c.tipofondo = 6 AND c.clase = 7  AND e.id_unico !=2";

        $resemp = $mysqli->query($sqlemp);

        while($ConE = mysqli_fetch_row($resemp)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
            $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

            $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto = IN (78,416)";
            $bas = $mysqli->query($base);
            $B = mysqli_fetch_row($bas);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

            $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =256 AND empleado = '$ConE[0]'";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
            $pdf->Ln(3);
        }

        $pdf->Ln(1);
        $pdf->Cell(140,0.5,'',1);
        $pdf->Ln(2);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

        $base = "SELECT SUM(valor) FROM gn_novedad  WHERE  concepto = IN (78,416)";
        $bas = $mysqli->query($base);
        $B = mysqli_fetch_row($bas);

        $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

        $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n WHERE n.concepto =256 ";

        $vapa1 = $mysqli->query($vpat1);
        $valP1 = mysqli_fetch_row($vapa1);

        $pdf->cellfitscale($mc2,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
    }

    ////////////// FINALIZA EL FONDO DE CAJA DE COMPENSACION /////////////////////////////////////////////

    $paraf = "SELECT t.id_unico, t.razonsocial, t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE c.tipofondo = 5 AND c.tipoentidadcredito is not NULL";
    $valor2 = $mysqli->query($paraf);

    $pdf->SetFont('Arial','B',8);
    $pdf->SetX(6);
    $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
    $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
    $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
    $pdf->Ln(12);

    while($PAR = mysqli_fetch_row($valor2)){

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale(20,18,utf8_decode($PAR[2]),0,0,'C');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
        $pdf->SetFont('Arial','B',7);
        $pdf->cellfitscale(80,18,utf8_decode($PAR[1]),0,0,'C');
        $pdf->Ln(12);

        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
        $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
        $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');

        $pdf->Ln(8);

        $mc0=20;$mc1=60;$mc2=30;$mc3=30;


        $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion

                FROM gn_empleado e
                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico != 2";

        $resemp = $mysqli->query($sqlemp);

        while($ConE = mysqli_fetch_row($resemp)){

            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
            $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

            $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto = IN (78,416)";
            $bas = $mysqli->query($base);
            $B = mysqli_fetch_row($bas);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

            $vpat = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' ";

            $vapa = $mysqli->query($vpat);
            $valP = mysqli_fetch_row($vapa);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

            $pdf->Ln(3);
        }

        $pdf->Ln(1);
        $pdf->Cell(140,0.5,'',1);
        $pdf->Ln(2);
        $pdf->SetFont('Arial','B',8);
        $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
        $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

        $base = "SELECT SUM(valor) FROM gn_novedad  WHERE  concepto IN (78,416)";
        $bas = $mysqli->query($base);
        $B = mysqli_fetch_row($bas);

        $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

        $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.id_unico = '$PAR[0]'";
        $vapa1 = $mysqli->query($vpat1);
        $valP2 = mysqli_fetch_row($vapa1);

        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP2[0],2,'.',',')),0,0,'R');
    }


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

        while($SAL = mysqli_fetch_row($valor)){

            $s = "SELECT DISTINCT  e.id_unico FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog'";

            $r = $mysqli->query($s);
            $nr = mysqli_num_rows($r);

            if($nr > 0){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,5,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,5,utf8_decode($SAL[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(30,5,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(25,5,utf8_decode($SAL[1]),0,0,'C');
                $pdf->Ln(4);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
                $pdf->Ln(5);

                $mc0=20;$mc1=60;$mc2=20;$mc3=30;$mc4=30;$mc5=30;
            }


            $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion

                FROM gn_empleado e
                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog'";

            $resemp = $mysqli->query($sqlemp);
            $nresemp = mysqli_num_rows($resemp);

            if($nresemp > 0){

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(n.valor) FROM gn_novedad n  
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            WHERE n.empleado = '$ConE[0]' AND e.grupogestion = '$grupog' AND n.concepto = IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                    $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'";

                    $vaem = $mysqli->query($vemple);
                    $valE = mysqli_fetch_row($vaem);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                    #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                    $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]'";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                    $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND c.clase != 6";

                    $valT = $mysqli->query($VTot);
                    $TOT = mysqli_fetch_row($valT);
                    $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }

                $pdf->Ln(2);
                $pdf->Cell(190,0.5,'',1);
                $pdf->Ln(1);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$SAL[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                       
                        . "WHERE af.tipo = 1 AND af.tercero = '$T[0]'  AND n.concepto = 2 AND e.grupogestion = '$grupog'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);
                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'";

                $VToEM = $mysqli->query($ToEM);
                $VAEMP = mysqli_fetch_row($VToEM);
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[0],2,'.',',')),0,0,'R');

                $vpat1 =    "SELECT  SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

                $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND c.clase != 6";

                $valT1 = $mysqli->query($VTot1);
                $TOT1 = mysqli_fetch_row($valT1);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');
                $pdf->Ln(10);
            }
        } #FINALIZA EL CICLO DEL CARGUE DE DATOS DEL FONOD DE SALUD Y CALCULA LOS TOTALES

        ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////

        $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
        $valor1 = $mysqli->query($pension);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
        $pdf->Ln(12);

        while($PEN = mysqli_fetch_row($valor1)){

            $s = "SELECT DISTINCT  e.id_unico FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog'";

            $r = $mysqli->query($s);
            $nr = mysqli_num_rows($r);

            if($nr > 0){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($PEN[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(25,18,utf8_decode($PEN[1]),0,0,'C');
                $pdf->Ln(12);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
                $pdf->Cell(50,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Fondo Solid'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Total'),1,0,'C');
                $pdf->Ln(8);

                $mc0=20;$mc1=50;$mc2=20;$mc3=25;$mc4=25;$mc5=25;$mc6=25;
            }


            $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion

                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog'";

            $resemp = $mysqli->query($sqlemp);
            $nresemp = mysqli_num_rows($resemp);

            if($nresemp > 0){

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(n.valor) FROM gn_novedad n  
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            WHERE n.empleado = '$ConE[0]' AND e.grupogestion = '$grupog' AND n.concepto IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' ";

                    $vaem = $mysqli->query($vemple);
                    $valE = mysqli_fetch_row($vaem);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]'";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n
                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'";

                    $foso = $mysqli->query($Fsol);
                    $SolF = mysqli_fetch_row($foso);

                    $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[0],2,'.',',')),0,0,'R');

                    $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND c.clase != 6";

                    $valT = $mysqli->query($VTot);
                    $TOT = mysqli_fetch_row($valT);
                    $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');

                    $pdf->Ln(3);
                } # FINALIZA EL CICLO DE LOS EMPLEADOS PARA LA PENSION

                $pdf->Ln(2);
                $pdf->Cell(190,0.5,'',1);
                $pdf->Ln(1);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$PEN[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                       
                        . "WHERE af.tipo = 2 AND af.tercero = '$T[0]'  AND n.concepto = 2 AND e.grupogestion = '$grupog'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);
                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                

                $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'";

                $vaem1 = $mysqli->query($vemple1);
                $valE1 = mysqli_fetch_row($vaem1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

                $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'";

                $foso1 = $mysqli->query($Fsol1);
                $SolF1 = mysqli_fetch_row($foso1);

                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[0],2,'.',',')),0,0,'R');

                $VTot12 = "SELECT SUM(n.valor)  FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND c.clase != 6";

                $VAl12  = $mysqli->query($VTot12);
                $TOVAL12 = mysqli_fetch_row($VAl12);
                $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');
                $pdf->Ln(10);
            }
        }

        ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
        $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
        $valor2 = $mysqli->query($arl);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
        $pdf->Ln(12);

        while($AR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
            $pdf->Ln(12);

            $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
            $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=60;$mc2=30;$mc3=30;

            $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion

                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND e.grupogestion = '$grupog'";

            $resemp = $mysqli->query($sqlemp);
            $nresemp = mysqli_num_rows($resemp);

            if($nresemp > 0){

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(n.valor) FROM gn_novedad n  
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            WHERE n.empleado = '$ConE[0]' AND e.grupogestion = '$grupog' AND n.concepto IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =363 AND empleado = '$ConE[0]'";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                       
                        . "WHERE af.tipo = 4 AND af.tercero = '$T[0]'  AND n.concepto = 2 AND e.grupogestion = '$grupog'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);
                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico WHERE n.concepto =363 AND e.grupogestion = '$grupog'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

            }

        }

        ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

        $caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
        $valor2 = $mysqli->query($caja);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('CAJA DE COMPENSACION'),0,0,'C');
        $pdf->Ln(12);

        while($AR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
            $pdf->Ln(12);

            $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
            $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=60;$mc2=30;$mc3=30;

            $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion

                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND e.grupogestion = '$grupog'";

            $resemp = $mysqli->query($sqlemp);
            $nresemp = mysqli_num_rows($resemp);

            if($nresemp > 0){

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(n.valor) FROM gn_novedad n  
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            WHERE n.empleado = '$ConE[0]' AND e.grupogestion = '$grupog' AND n.concepto IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT sum(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            WHERE  n.concepto =256 AND n.empleado = '$ConE[0]' AND e.grupogestion = '$grupog'";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                       
                        . "WHERE af.tipo = 6 AND af.tercero = '$T[0]'  AND n.concepto = 2 AND e.grupogestion = '$grupog'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);
                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico WHERE n.concepto =256 AND e.grupogestion = '$grupog'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

            }

        }

        ////////////// FINALIZA EL FONDO DE CAJA DE COMPENSACION /////////////////////////////////////////////

       
        $paraf = "SELECT t.id_unico, t.razonsocial FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE c.tipofondo = 5 AND c.tipoentidadcredito is not NULL";
        $valor2 = $mysqli->query($paraf);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
        $pdf->Ln(12);

        while($PAR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(20,18,utf8_decode($PAR[2]),0,0,'C');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(80,18,utf8_decode($PAR[1]),0,0,'C');
            $pdf->Ln(12);

            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
            $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=60;$mc2=30;$mc3=30;

            $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion

                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2 AND e.grupogestion = '$grupog' AND e.id_unico != 2";

            $resemp = $mysqli->query($sqlemp);
            $nresemp = mysqli_num_rows($resemp);

            if($nresemp > 0){

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(n.valor) FROM gn_novedad n  
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            WHERE n.empleado = '$ConE[0]' AND e.grupogestion = '$grupog' AND n.concepto IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');


                    $vpat = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' ";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE id_unico = '$PAR[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                            WHERE af.tipo = 6  AND n.concepto = 2 AND e.grupogestion= '$grupog'";

                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);
                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
                            LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
            }
        }
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

        while($SAL = mysqli_fetch_row($valor)){

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(20,5,utf8_decode('NIT:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(20,5,utf8_decode($SAL[0]),0,0,'C');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(30,5,utf8_decode('RAZON SOCIAL:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(25,5,utf8_decode($SAL[1]),0,0,'C');
            $pdf->Ln(4);

            $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
            $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(5);

            $mc0=20;$mc1=60;$mc2=20;$mc3=25;$mc4=25;$mc5=30;

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);
            
 
            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                        e.codigointerno,
                                        e.tercero,
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
                                        tr.numeroidentificacion

                        FROM gn_empleado e
                        LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                        LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro>='$retE[1]' and af.fechaafiliacion <='$retE[1]'
                        OR c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' and af.fecharetiro>='$retE[1]'    and af.fechaafiliacion <='$retE[1]' OR 

                        c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                        OR c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";

            $resemp = $mysqli->query($sqlemp);

            while($ConE = mysqli_fetch_row($resemp)){

                $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' and periodo = '$periodo' AND concepto  IN (78,416)";
                $bas = $mysqli->query($base);
                $B = mysqli_fetch_row($bas);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');
                #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' and af.fecharetiro>='$retE[1]' and af.fechaafiliacion <='$retE[1]'  OR 
                c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]' ";

                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo'  and af.fecharetiro>='$retE[1]' and af.fechaafiliacion <='$retE[1]' OR 
                c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' AND c.clase != 6  and af.fecharetiro>='$retE[1]' and af.fechaafiliacion <='$retE[1]'  OR  c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' AND c.clase != 6  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
                $pdf->Ln(3);
            }

            $pdf->Ln(2);
            $pdf->Cell(180,0.5,'',1);
            $pdf->Ln(1);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$SAL[0]'";
            $ter = $mysqli->query($Terce);
            $T = mysqli_fetch_row($ter);
            
            $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                   
                        WHERE af.tipo = 1 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND n.concepto = 2  and af.fecharetiro>='$retE[1]' and af.fechaafiliacion <='$retE[1]' OR 
                        af.tipo = 1 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND n.concepto = 2  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'    ";
            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

            $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' OR 
            c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'   ";
            $VToEM = $mysqli->query($ToEM);
            $VAEMP = mysqli_fetch_row($VToEM);
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[0],2,'.',',')),0,0,'R');

            $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' and af.fecharetiro>='$retE[1]'  OR c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";
            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);

            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

            $VTot1 = "SELECT SUM(n.valor)  FROM gn_novedad n
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND c.clase != 6 and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' OR  c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND c.clase != 6 and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";

            $valT1 = $mysqli->query($VTot1);
            $TOT1 = mysqli_fetch_row($valT1);
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');
            $pdf->Ln(10);
        }

        ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////

        $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
        $valor1 = $mysqli->query($pension);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
        $pdf->Ln(12);

        while($PEN = mysqli_fetch_row($valor1)){

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(20,18,utf8_decode($PEN[0]),0,0,'C');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(25,18,utf8_decode($PEN[1]),0,0,'C');
            $pdf->Ln(12);

            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
            $pdf->Cell(50,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Fondo Solid'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(8);

           $mc0=20;$mc1=50;$mc2=20;$mc3=25;$mc4=25;$mc5=25;$mc6=25;

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);  
            $retE = mysqli_fetch_row($retEmp);
            

            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                        e.codigointerno,  
                                        e.tercero,
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
                                        tr.numeroidentificacion
                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog' 
                    AND vr.estado=1 AND vr.vinculacionretiro IS NULL   and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'
                    OR c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog' 
                    AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' and af.fecharetiro>='$retE[1]'    OR  

                    c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog' 
                    AND vr.estado=1 AND vr.vinculacionretiro IS NULL   and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                    OR c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog' 
                    AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";

            $resemp = $mysqli->query($sqlemp);

            while($ConE = mysqli_fetch_row($resemp)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L'); 

                $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' and periodo = '$periodo' AND concepto IN (78,416)";
                $bas = $mysqli->query($base);
                $B = mysqli_fetch_row($bas);

                $pdf->cellfitscale($mc2,5,utf8_decode($B[0]),0,0,'R');

                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' OR 
                        c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' and af.fecharetiro is null and af.fechaafiliacion <='$retE[1]'  ";

                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                $foso = $mysqli->query($Fsol);
                $SolF = mysqli_fetch_row($foso);

                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[0],2,'.',',')),0,0,'R');

                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' AND c.clase != 6  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR  c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo' AND c.clase != 6  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');

                $pdf->Ln(3);
            } # FINALIZA EL CICLO DE LOS EMPLEADOS PARA LA PENSION

            $pdf->Ln(2);
            $pdf->Cell(190,0.5,'',1);
            $pdf->Ln(1);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$PEN[0]'";
            $ter = $mysqli->query($Terce);
            $T = mysqli_fetch_row($ter);

            $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico                    
                        WHERE af.tipo = 2 AND af.tercero = '$T[0]'  AND n.periodo = '$periodo' AND n.concepto = 2 and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR af.tipo = 2 AND af.tercero = '$T[0]'  AND n.periodo = '$periodo' AND n.concepto = 2 and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";
            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');
            $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

            $vaem1 = $mysqli->query($vemple1);
            $valE1 = mysqli_fetch_row($vaem1);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[0],2,'.',',')),0,0,'R');

            $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'  AND n.periodo = '$periodo'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'  AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);

            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

            $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'  AND n.periodo = '$periodo' and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR  c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'  AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

            $foso1 = $mysqli->query($Fsol1);
            $SolF1 = mysqli_fetch_row($foso1);

            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[0],2,'.',',')),0,0,'R');

            $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND c.clase != 6 and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' OR c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND c.clase != 6 and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

            $VAl12  = $mysqli->query($VTot12);
            $TOVAL12 = mysqli_fetch_row($VAl12);
            $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');
            $pdf->Ln(10);

        }

        ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
        $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
        $valor2 = $mysqli->query($arl);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);

        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
        $pdf->Ln(12);

        while($AR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
            $pdf->Ln(12);
            $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');    
            $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=60;$mc2=30;$mc3=30;

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);
            

            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                        e.codigointerno,
                                        e.tercero,
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
                                        tr.numeroidentificacion
                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado=1 
                    AND vr.vinculacionretiro IS NULL  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'
                    OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2 
                    AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR

                    c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado=1 
                    AND vr.vinculacionretiro IS NULL  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                    OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2 
                    AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

            $resemp = $mysqli->query($sqlemp);

            while($ConE = mysqli_fetch_row($resemp)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');
                



                $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' and periodo = '$periodo' AND concepto  IN (78,416)";
                $bas = $mysqli->query($base);
                $B = mysqli_fetch_row($bas);

                $pdf->cellfitscale($mc2,5,utf8_decode($B[0]),0,0,'R');

                $vpat = "SELECT sum(valor) FROM gn_novedad WHERE  concepto =363 AND empleado = '$ConE[0]' AND periodo = '$periodo'";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                $pdf->Ln(3);
            }

            $pdf->Ln(1);
            $pdf->Cell(140,0.5,'',1);
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
            $ter = $mysqli->query($Terce);
            $T = mysqli_fetch_row($ter);

            $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                   
                    WHERE af.tipo = 4 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND n.concepto = 2 and af.fecharetiro >= '$retE[1]'  and af.fechaafiliacion <='$retE[1]' OR af.tipo = 4 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND n.concepto = 2 and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

            $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_empleado e ON n.empleado = e.id_unico WHERE n.concepto =363 "
                    . "AND e.grupogestion = '$grupog' AND periodo = '$periodo'";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
        }
        ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

        $caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
      
        $valor2 = $mysqli->query($caja);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(20,18,utf8_decode('CAJA DE COMPENSACION FAMILIAR'),0,0,'C');
        $pdf->Ln(12);

        while($AR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(55,18,utf8_decode($AR[1]),0,0,'C');
            $pdf->Ln(12);
        
            $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');    
            $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=60;$mc2=30;$mc3=30;

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);
            

            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                        e.codigointerno,
                                        e.tercero,
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
                                        tr.numeroidentificacion
                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND e.grupogestion = '$grupog' AND vr.vinculacionretiro IS NULL  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' 
                    OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND e.grupogestion = '$grupog' AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' and af.fecharetiro>='$retE[1]'   OR

                    c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND e.grupogestion = '$grupog' AND vr.vinculacionretiro IS NULL  and af.fecharetiro  is null
                    OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND e.grupogestion = '$grupog' AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'    ";

            $resemp = $mysqli->query($sqlemp);

            while($ConE = mysqli_fetch_row($resemp)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' and periodo = '$periodo' AND concepto IN (78,416)";
                $bas = $mysqli->query($base);
                $B = mysqli_fetch_row($bas);

                $pdf->cellfitscale($mc2,5,utf8_decode($B[0]),0,0,'R');
                
                $vpat = "SELECT sum(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "WHERE  c.codigo =800   AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' ";
          
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                $pdf->Ln(3);
            }

            $pdf->Ln(1);
            $pdf->Cell(140,0.5,'',1);
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                      LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                      WHERE n.periodo = '$periodo' AND n.concepto = 2 AND e.grupogestion = '$grupog'";
            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');
            
            $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico WHERE c.codigo =800 AND n.periodo = '$periodo' AND e.grupogestion = '$grupog'";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
        } 

        ////////////// FINALIZA EL FONDO DE CAJA DE COMPENSACION /////////////////////////////////////////////

        $paraf = "SELECT t.id_unico, t.razonsocial,t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                 WHERE c.tipofondo = 5  AND c.tipoentidadcredito is not null ";
        $valor2 = $mysqli->query($paraf);
      
        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
        $pdf->Ln(12);

        while($PAR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(20,18,utf8_decode($PAR[2]),0,0,'C');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(80,18,utf8_decode($PAR[1]),0,0,'C');
            $pdf->Ln(12);

            $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
            $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=60;$mc2=30;$mc3=30;

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);
            

            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                        e.codigointerno,
                                        e.tercero,
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
                                        tr.numeroidentificacion
                        FROM gn_empleado e
                        LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                        LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado=1  AND vr.vinculacionretiro IS NULL  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'
                        OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2  AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR

                        c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado=1  AND vr.vinculacionretiro IS NULL  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                        OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2  AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

            $resemp = $mysqli->query($sqlemp);

            while($ConE = mysqli_fetch_row($resemp)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' and periodo = '$periodo' AND concepto IN (78,416)";
                $bas = $mysqli->query($base);
                $B = mysqli_fetch_row($bas);

                $pdf->cellfitscale($mc2,5,utf8_decode($B[0]),0,0,'R');
                $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                        . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' AND n.periodo = '$periodo'";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                $pdf->Ln(3);
            }

            $pdf->Ln(1);
            $pdf->Cell(140,0.5,'',1);
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$PAR[0]'";
            $ter = $mysqli->query($Terce);
            $T = mysqli_fetch_row($ter);

            $base1 = "SELECT SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE  n.periodo = '$periodo' AND n.concepto = '2' AND e.grupogestion = '$grupog'";
                   
                    
            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');
            $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo'";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
        }

        ////////// FINALIZA LA CONDICION CUANOD EL TIPO DE FONDO ES VACIO //////////////////////

    }elseif(empty($periodo) && !empty($tipof)){

        if($tipof == 1){

            $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                    . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
            $valor = $mysqli->query($salud);

            $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
            $Fond = $mysqli->query($fondo);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
            $pdf->Ln(12);

            while($SAL = mysqli_fetch_row($valor)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,5,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,5,utf8_decode($SAL[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(30,5,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(25,5,utf8_decode($SAL[1]),0,0,'C');
                $pdf->Ln(4);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
                $pdf->Ln(5);

                $mc0=20;$mc1=60;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            tr.numeroidentificacion,
                                            e.tercero,
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
                                            
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' AND e.grupogestion = '$grupog'";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[1]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto  IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                    $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                            . "WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'  ";

                    $vaem = $mysqli->query($vemple);
                    $valE = mysqli_fetch_row($vaem);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                    #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                    $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                            . "WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]' ";
                    
                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                    $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND c.clase != 6 ";

                    $valT = $mysqli->query($VTot);
                    $TOT = mysqli_fetch_row($valT);
                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }

                $pdf->Ln(2);
                $pdf->Cell(190,0.5,'',1);
                $pdf->Ln(1);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$SAL[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);
                
                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                       
                            WHERE af.tipo = 1 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.concepto = 2";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                        . "WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' ";
        
                $VToEM = $mysqli->query($ToEM);
                $VAEMP = mysqli_fetch_row($VToEM);
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                        . "WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' ";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

                $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico    
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND  c.clase != 6";

                $valT1 = $mysqli->query($VTot1);
                $TOT1 = mysqli_fetch_row($valT1);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');
                $pdf->Ln(10);
            }

            ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////
            
            
        }elseif($tipof == 2){

            $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial "
                    . "FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
      
            $valor1 = $mysqli->query($pension);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
            $pdf->Ln(12);

            while($PEN = mysqli_fetch_row($valor1)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($PEN[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(25,18,utf8_decode($PEN[1]),0,0,'C');
                $pdf->Ln(12);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
                $pdf->Cell(50,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Fondo Solid'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Total'),1,0,'C');
                $pdf->Ln(8);

                $mc0=20;$mc1=50;$mc2=20;$mc3=25;$mc4=25;$mc5=25;$mc6=25;

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            tr.numeroidentificacion,
                                            e.tercero,
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
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' AND e.grupogestion = '$grupog'";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[1]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');


                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                            . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]'";

                    $vaem = $mysqli->query($vemple);
                    $valE = mysqli_fetch_row($vaem);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                            . "WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]' ";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                            . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]' ";

                    $foso = $mysqli->query($Fsol);
                    $SolF = mysqli_fetch_row($foso);

                    $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[0],2,'.',',')),0,0,'R');

                    $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND c.clase != 6 ";

                    $valT = $mysqli->query($VTot);
                    $TOT = mysqli_fetch_row($valT);
                    $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                } # FINALIZA EL CICLO DE LOS EMPLEADOS PARA LA PENSION

                $pdf->Ln(2);
                $pdf->Cell(190,0.5,'',1);
                $pdf->Ln(1);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$PEN[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);
                
                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                       
                            WHERE af.tipo = 2 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.concepto = 2";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                        . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' "
                        . "AND e.grupogestion = '$grupog' ";

                $vaem1 = $mysqli->query($vemple1);
                $valE1 = mysqli_fetch_row($vaem1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                        . "WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' ";
                        

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

                $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                        . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' "
                        . "AND e.grupogestion = '$grupog' ";

                $foso1 = $mysqli->query($Fsol1);
                $SolF1 = mysqli_fetch_row($foso1);

                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[0],2,'.',',')),0,0,'R');

                $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND c.clase != 6 ";

                $VAl12  = $mysqli->query($VTot12);
                $TOVAL12 = mysqli_fetch_row($VAl12);
                $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');
                $pdf->Ln(10);
            }

            ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
            }elseif($tipof == 3){
      
                $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial "
                        . "FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
                
                $valor2 = $mysqli->query($arl);

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
                $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
                $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
                $pdf->Ln(12);

                while($AR = mysqli_fetch_row($valor2)){

                    $pdf->SetFont('Arial','B',8);
                    $pdf->SetX(6);
                    $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
                    $pdf->SetFont('Arial','B',8);
                    $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
                    $pdf->Ln(12);
                    $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
                    $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                    $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
                    $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                    $pdf->Ln(8);

                    $mc0=20;$mc1=60;$mc2=30;$mc3=30;

                    $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                tr.numeroidentificacion,
                                                e.tercero,
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
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 4 AND c.clase = 7  AND e.id_unico !=2 AND e.grupogestion = '$grupog'";

                    $resemp = $mysqli->query($sqlemp);

                    while($ConE = mysqli_fetch_row($resemp)){

                        $pdf->SetFont('Arial','B',7);
                        $pdf->cellfitscale($mc0,5,utf8_decode($ConE[1]),0,0,'R');
                        $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');
                        
                        $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416)";
                        $bas = $mysqli->query($base);
                        $B = mysqli_fetch_row($bas);

                        $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                        $vpat = "SELECT sum(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                . "WHERE  n.concepto =363   AND n.empleado = '$ConE[0]'  ";
                        
                        $vapa = $mysqli->query($vpat);
                        $valP = mysqli_fetch_row($vapa);

                        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                        $pdf->Ln(3);
                    }

                    $pdf->Ln(1);
                    $pdf->Cell(140,0.5,'',1);
                    $pdf->Ln(2);
                    $pdf->SetFont('Arial','B',8);
                    $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                    $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                    $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
                    $ter = $mysqli->query($Terce);
                    $T = mysqli_fetch_row($ter);
                    
                    $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                JOIN gn_concepto c ON c.id_unico = n.concepto 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                           
                                WHERE af.tipo = 4 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.concepto = 2";
                    $bas1 = $mysqli->query($base1);
                    $B1 = mysqli_fetch_row($bas1);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');


                    $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                            . "WHERE n.concepto =363 AND e.grupogestion = '$grupog' ";

                    $vapa1 = $mysqli->query($vpat1);
                    $valP1 = mysqli_fetch_row($vapa1);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
                }

                ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

            }elseif($tipof == 4){
                $caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial "
                        . "FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
                
                $valor2 = $mysqli->query($caja);

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
                $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
                $pdf->Cell(12,18,utf8_decode('CAJA DE COMPENSACION'),0,0,'C');
                $pdf->Ln(12);

                while($AR = mysqli_fetch_row($valor2)){

                    $pdf->SetFont('Arial','B',8);
                    $pdf->SetX(6);
                    $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
                    $pdf->SetFont('Arial','B',8);
                    $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
                    $pdf->Ln(12);
                    $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
                    $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                    $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
                    $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                    $pdf->Ln(8);

                    $mc0=20;$mc1=60;$mc2=30;$mc3=30;

                    $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                tr.numeroidentificacion,
                                                e.tercero,
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
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 6 AND c.clase = 7  AND e.id_unico !=2 AND e.grupogestion = '$grupog'";

                    $resemp = $mysqli->query($sqlemp);

                    while($ConE = mysqli_fetch_row($resemp)){

                        $pdf->SetFont('Arial','B',7);
                        $pdf->cellfitscale($mc0,5,utf8_decode($ConE[1]),0,0,'R');
                        $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');
                        
                        $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto = '2'";
                        $bas = $mysqli->query($base);
                        $B = mysqli_fetch_row($bas);

                        $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                        $vpat = "SELECT sum(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                . "WHERE  n.concepto =256   AND n.empleado = '$ConE[0]'  ";
                        
                        $vapa = $mysqli->query($vpat);
                        $valP = mysqli_fetch_row($vapa);

                        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                        $pdf->Ln(3);
                    }

                    $pdf->Ln(1);
                    $pdf->Cell(140,0.5,'',1);
                    $pdf->Ln(2);
                    $pdf->SetFont('Arial','B',8);
                    $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                    $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                    $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
                    $ter = $mysqli->query($Terce);
                    $T = mysqli_fetch_row($ter);
                    
                    $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                JOIN gn_concepto c ON c.id_unico = n.concepto 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                           
                                WHERE af.tipo = 6 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.concepto = 2";
                    $bas1 = $mysqli->query($base1);
                    $B1 = mysqli_fetch_row($bas1);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');


                    $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                            . "WHERE n.concepto =256 AND e.grupogestion = '$grupog' ";

                    $vapa1 = $mysqli->query($vpat1);
                    $valP1 = mysqli_fetch_row($vapa1);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
                }

                ////////////// FINALIZA EL FONDO DE CAJA DE COMPENSACION /////////////////////////////////////////////

            }else{

                $paraf = "SELECT t.id_unico, t.razonsocial,t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                        WHERE c.tipofondo = 5  AND c.tipoentidadcredito is not null";
      
                $valor2 = $mysqli->query($paraf);
      
                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
                $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
                $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
                $pdf->Ln(12);

                while($PAR = mysqli_fetch_row($valor2)){

                    $pdf->SetFont('Arial','B',8);
                    $pdf->SetX(6);
                    $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale(20,18,utf8_decode($PAR[2]),0,0,'C');
                    $pdf->SetFont('Arial','B',8);
                    $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale(80,18,utf8_decode($PAR[1]),0,0,'C');
                    $pdf->Ln(12);
                    
                    $pdf->SetFont('Arial','B',7);
                    $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                    $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                    $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
                    $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                    $pdf->Ln(8);

                    $mc0=20;$mc1=60;$mc2=30;$mc3=30;


                    $sqlemp = "SELECT DISTINCT  e.id_unico,
                                                e.codigointerno,
                                                e.tercero,
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
                                FROM gn_empleado e
                                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                WHERE c.tipofondo = 2 AND c.clase = 2 AND e.grupogestion = '$grupog' AND e.id_unico !=2";

                    $resemp = $mysqli->query($sqlemp);

                    while($ConE = mysqli_fetch_row($resemp)){

                        $pdf->SetFont('Arial','B',7);
                        $pdf->cellfitscale($mc0,5,utf8_decode($ConE[1]),0,0,'C');
                        $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'C');

                        $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416)";
                        $bas = $mysqli->query($base);
                        $B = mysqli_fetch_row($bas);

                        $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                        $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                                . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                                . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                                . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' ";

                        $vapa = $mysqli->query($vpat);
                        $valP = mysqli_fetch_row($vapa);

                        $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                        $pdf->Ln(3);
                    }

                    $pdf->Ln(1);
                    $pdf->Cell(140,0.5,'',1);
                    $pdf->Ln(2);
                    $pdf->SetFont('Arial','B',8);
                    $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                    $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
                    
                    $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                JOIN gn_concepto c ON c.id_unico = n.concepto 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                
                                WHERE e.grupogestion = '$grupog' AND n.concepto = 2";
                    $bas1 = $mysqli->query($base1);
                    $B1 = mysqli_fetch_row($bas1);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                    $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                            . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND c.tipofondo = '$tipof'";

                    $vapa1 = $mysqli->query($vpat1);
                    $valP1 = mysqli_fetch_row($vapa1);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
                }
            }
            ///////////// FINALIZA LA CONDCION CUANDO EL PERIODO ES VACIO Y EL TIPO DE FONDO NO  /////////////////////////////

    }else{

        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

        if($tipof == 1){

            $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial "
                    . "FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
            
            $valor = $mysqli->query($salud);

            $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
            $Fond = $mysqli->query($fondo);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
            $pdf->Ln(12);

            while($SAL = mysqli_fetch_row($valor)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,5,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,5,utf8_decode($SAL[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(30,5,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(25,5,utf8_decode($SAL[1]),0,0,'C');
                $pdf->Ln(4);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
                $pdf->Ln(5);

                $mc0=20;$mc1=60;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                
                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]'  AND e.grupogestion = '$grupog' 
                            AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                            OR c.tipofondo = 1 AND c.clase = 2 AND t.numeroidentificacion = '$SAL[0]'  AND e.grupogestion = '$grupog' 
                            AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416) AND periodo = '$periodo'";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                    $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' 
                            AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' 
                            AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                    $vaem = $mysqli->query($vemple);
                    $valE = mysqli_fetch_row($vaem);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                    #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                    $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' 
                            AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' 
                            AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                    $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' AND c.clase != 6 and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 1 AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' AND c.clase != 6 and af.fecharetiro is null and af.fechaafiliacion <='$retE[1]' ";

                    $valT = $mysqli->query($VTot);
                    $TOT = mysqli_fetch_row($valT);
                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }

                $pdf->Ln(2);
                $pdf->Cell(190,0.5,'',1);
                $pdf->Ln(1);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$SAL[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);
                
                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                       
                            WHERE af.tipo = 1 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.concepto = 2 AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'  OR af.tipo = 1 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.concepto = 2 AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');


                $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";
        
                $VToEM = $mysqli->query($ToEM);
                $VAEMP = mysqli_fetch_row($VToEM);
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

                $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND c.clase != 6 and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND e.grupogestion = '$grupog' AND n.periodo = '$periodo' AND c.clase != 6 and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                $valT1 = $mysqli->query($VTot1);
                $TOT1 = mysqli_fetch_row($valT1);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');
                $pdf->Ln(10);
            }
            ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////
        
            
        }elseif($tipof == 2){

            $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                    . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
            
            $valor1 = $mysqli->query($pension);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
            $pdf->Ln(12);

            while($PEN = mysqli_fetch_row($valor1)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($PEN[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(25,18,utf8_decode($PEN[1]),0,0,'C');
                $pdf->Ln(12);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
                $pdf->Cell(50,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Fondo Solid'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Total'),1,0,'C');
                $pdf->Ln(8);

                $mc0=20;$mc1=50;$mc2=20;$mc3=25;$mc4=25;$mc5=25;$mc6=25;

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                
                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE c.tipofondo = 2 AND c.clase = 2  AND t.numeroidentificacion = '$PEN[0]'  AND e.grupogestion = '$grupog' 
                            AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                            OR c.tipofondo = 2 AND c.clase = 2 AND t.numeroidentificacion = '$PEN[0]'  AND e.grupogestion = '$grupog' AND vr.estado = 2 AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416) AND periodo = '$periodo'";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');


                    $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' 
                            AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'   OR 
                            c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]' 
                            AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                    $vaem = $mysqli->query($vemple);
                    $valE = mysqli_fetch_row($vaem);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                        WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                    $foso = $mysqli->query($Fsol);
                    $SolF = mysqli_fetch_row($foso);

                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($SolF[0],2,'.',',')),0,0,'R');

                    $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                        WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' AND c.clase != 6  and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' AND c.clase != 6  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                    $valT = $mysqli->query($VTot);
                    $TOT = mysqli_fetch_row($valT);
                    $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                } # FINALIZA EL CICLO DE LOS EMPLEADOS PARA LA PENSION

                $pdf->Ln(2);
                $pdf->Cell(190,0.5,'',1);
                $pdf->Ln(1);    
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$PEN[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);
                
                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                       
                            WHERE af.tipo = 2 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.concepto = 2 AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'   OR af.tipo = 2 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.concepto = 2 AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'  AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog'  AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                $vaem1 = $mysqli->query($vemple1);
                $valE1 = mysqli_fetch_row($vaem1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                    WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

                $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                    WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' 
                                    AND e.grupogestion = '$grupog' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]' 
                                    AND e.grupogestion = '$grupog' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                $foso1 = $mysqli->query($Fsol1);
                $SolF1 = mysqli_fetch_row($foso1);

                $pdf->cellfitscale($mc6,5,utf8_decode(number_format($SolF1[0],2,'.',',')),0,0,'R');

                $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n
                                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                                        WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' 
                                        AND n.periodo = '$periodo' AND c.clase != 6  and af.fecharetiro >= '$retE[1]'   OR 
                                        c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND e.grupogestion = '$grupog' 
                                        AND n.periodo = '$periodo' AND c.clase != 6  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                $VAl12  = $mysqli->query($VTot12);
                $TOVAL12 = mysqli_fetch_row($VAl12);
                $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');
                $pdf->Ln(10);
            }

            ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
        }elseif($tipof == 3){
      
            $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                    . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
      
            $valor2 = $mysqli->query($arl);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
            $pdf->Ln(12);

            while($AR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
                $pdf->Ln(12);
                $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Ln(8);

                $mc0=20;$mc1=60;$mc2=30;$mc3=30;

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                $fc = explode("-", $retE[1]);
                $dias = cal_days_in_month(CAL_GREGORIAN, $fc[1], $fc[0]);
                $retE[1] = $fc[0].'-'.$fc[1].'-'.$dias;

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                            e.codigointerno,
                            e.tercero,
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
                            tr.numeroidentificacion
                        FROM gn_empleado e
                        LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                        LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND e.grupogestion = '$grupog' 
                        AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                        OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2 
                        AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416) AND periodo = '$periodo'";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            WHERE  n.concepto =363   AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' ";
                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);  

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                 $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);
                
                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                       
                            WHERE af.tipo = 4 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.concepto = 2 AND n.periodo = '$periodo'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.concepto =363 AND e.grupogestion = '$grupog'  AND n.periodo = '$periodo'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
            }

            ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////
        }elseif($tipof == 4){
            $caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                    . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
      
            $valor2 = $mysqli->query($caja);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('CAJA DE COMPENSACION'),0,0,'C');
            $pdf->Ln(12);

            while($AR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
                $pdf->Ln(12);
                $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Ln(8);

                $mc0=20;$mc1=60;$mc2=30;$mc3=30;

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                
                $sqlemp = "SELECT DISTINCT  e.id_unico,
                            e.codigointerno,
                            e.tercero,
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
                            tr.numeroidentificacion
                        FROM gn_empleado e
                        LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                        LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND e.grupogestion = '$grupog' 
                        AND vr.estado=1 AND vr.vinculacionretiro IS NULL
                        OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2 
                        AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416) AND periodo = '$periodo'";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            WHERE  n.concepto =256   AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' ";
                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);  

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);
                
                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                       
                            WHERE af.tipo = 6 AND af.tercero = '$T[0]' AND e.grupogestion = '$grupog' AND n.concepto = 2 AND n.periodo = '$periodo'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.concepto =256 AND e.grupogestion = '$grupog'  AND n.periodo = '$periodo'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
            }

            ////////////// FINALIZA EL FONDO DE CAJA DE COMPENSACION /////////////////////////////////////////////
        }else{

            $paraf = "SELECT t.id_unico, t.razonsocial FROM gn_concepto c 
                    LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE c.tipofondo = 5 AND c.tipoentidadcredito is not NULL";
        
            $valor2 = $mysqli->query($paraf);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
            $pdf->Ln(12);

            while($PAR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($PAR[2]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(80,18,utf8_decode($PAR[1]),0,0,'C');
                $pdf->Ln(12);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');        
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Ln(8);

                $mc0=20;$mc1=60;$mc2=30;$mc3=30;

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                
                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND e.grupogestion = '$grupog' AND vr.estado=1 
                            AND vr.vinculacionretiro IS NULL
                            OR c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2  AND e.grupogestion = '$grupog' AND vr.estado = 2 
                            AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]' ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416) AND periodo = '$periodo'";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                            . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                            . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            
                            WHERE  e.grupogestion = '$grupog' AND n.concepto = 2 AND n.periodo = '$periodo'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE t.id_unico = '$PAR[0]' AND e.grupogestion = '$grupog' AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
            }
        }

    }
}elseif(empty($grupog)){

    if(!empty($periodo) && empty($tipof)){

        $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 1 ORDER BY t.id_unico";
        $valor = $mysqli->query($salud);
        $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
        $Fond = $mysqli->query($fondo);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(40,18,utf8_decode('TIPO DE FONDO:'),0,0,'L');
        $pdf->Cell(20,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(20,18,utf8_decode('SALUD'),0,0,'L');
        $pdf->Ln(12);

        while($SAL = mysqli_fetch_row($valor)){
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,5,utf8_decode('NIT:'),0,0,'L');
            $pdf->cellfitscale(20,5,utf8_decode($SAL[0]),0,0,'L');
            $pdf->Cell(30,5,utf8_decode('RAZON SOCIAL:'),0,0,'L');
            $pdf->Cell(50,5,utf8_decode($SAL[1]),0,0,'L');
            $pdf->Ln(7);

            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('Cédula'),1,0,'C');
            $pdf->Cell(70,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(5);

            $mc0=20;$mc1=70;$mc2=20;$mc3=25;$mc4=25;$mc5=30;

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);
            
            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                        e.codigointerno,
                                        e.tercero,
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
                                        tr.numeroidentificacion
                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                   WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion ='$SAL[0]' AND vr.estado=1 
                   AND vr.vinculacionretiro IS NULL
                   OR c.tipofondo =1 AND c.clase = 2  AND t.numeroidentificacion ='$SAL[0]' AND vr.estado = 2 
                   AND vr.fecha BETWEEN '$retE[0]' AND '$retE[1]'";

            $resemp = $mysqli->query($sqlemp);

            while($ConE = mysqli_fetch_row($resemp)){
                if ($ConE[0]==35) {
                      $siu='ss';
                }

                $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');
                $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' and periodo = '$periodo' AND concepto IN (78,416)";
                $bas = $mysqli->query($base);
                $B = mysqli_fetch_row($bas);
                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');
                #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'";

                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'  ";
                
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' AND c.clase !=6 ";

                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
                $pdf->Ln(3);
            }

            $pdf->Ln(2);
            $pdf->Cell(190,0.5,'',1);
            $pdf->Ln(1);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$SAL[0]'";
            $ter = $mysqli->query($Terce);
            $T = mysqli_fetch_row($ter);

            $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                   
                        WHERE af.tipo = 1 AND af.tercero = '$T[0]'  AND n.periodo = '$periodo' AND n.concepto = 2 and af.fecharetiro >= '$retE[1]' OR af.tipo = 1 AND af.tercero = '$T[0]'  AND n.periodo = '$periodo' AND n.concepto = 2";
            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

            $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND c.clase != 6 AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]' AND c.clase != 6 AND n.periodo = '$periodo'  ";
        
            $VToEM = $mysqli->query($ToEM);
            $VAEMP = mysqli_fetch_row($VToEM);
            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[0],2,'.',',')),0,0,'R');

            $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                    WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'  AND n.periodo = '$periodo'  ";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);

            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

            $VTot1 = "SELECT SUM(n.valor) FROM gn_novedad n
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND c.clase != 6  AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND c.clase != 6  AND n.periodo = '$periodo' ";

            $valT1 = $mysqli->query($VTot1);
            $TOT1 = mysqli_fetch_row($valT1);
            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');
            $pdf->Ln(10);
        }

        ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////

        $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
            LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 2 ORDER BY t.id_unico";
      
        $valor1 = $mysqli->query($pension);

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(20,18,utf8_decode('TIPO DE FONDO:'),0,0,'L');
        $pdf->Cell(20,18,utf8_decode(''),0,0,'L');
        $pdf->Cell(20,18,utf8_decode('PENSION'),0,0,'L');
        $pdf->Ln(12);

        while($PEN = mysqli_fetch_row($valor1)){
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'L');
            $pdf->cellfitscale(20,18,utf8_decode($PEN[0]),0,0,'L');
            $pdf->Cell(30,18,utf8_decode('RAZON SOCIAL:'),0,0,'L');
            $pdf->Cell(50,18,utf8_decode($PEN[1]),0,0,'L');
            $pdf->Ln(12);

            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
            $pdf->Cell(50,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Fondo Solid'),1,0,'C');
            $pdf->Cell(25,5, utf8_decode('Total'),1,0,'C');
            $pdf->Ln(7);

            $mc0=20;$mc1=50;$mc2=20;$mc3=25;$mc4=25;$mc5=25;$mc6=25;

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);
            
            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                        e.codigointerno,
                                        e.tercero,
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
                                        tr.numeroidentificacion
                        FROM gn_empleado e
                        LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                        LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                        WHERE c.tipofondo = 2 AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' 
                        OR c.tipofondo =2 AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado = 2 
                        AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR c.tipofondo =2  AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'   OR   

                        c.tipofondo = 2 AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro  is null and af.fechaafiliacion <='$retE[1]' 
                        OR c.tipofondo =2 AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado = 2 
                        AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   OR c.tipofondo =2  AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro is  null and af.fechaafiliacion <='$retE[1]'  ";

            $resemp = $mysqli->query($sqlemp);

            while($ConE = mysqli_fetch_row($resemp)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' and periodo = '$periodo' AND concepto IN (78,416)";
                $bas = $mysqli->query($base);
                $B = mysqli_fetch_row($bas);

                $pdf->cellfitscale($mc2,5,utf8_decode($B[0]),0,0,'R');

                $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]'   AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]'   AND n.periodo = '$periodo' and af.fecharetiro is null and af.fechaafiliacion <='$retE[1]'  ";

                $vaem = $mysqli->query($vemple);
                $valE = mysqli_fetch_row($vaem);
                echo ' valor pension '.$valE[0];

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'   ";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'   and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'   and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'   ";

                $foso = $mysqli->query($Fsol);
                $SolF = mysqli_fetch_row($foso);

                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[0],2,'.',',')),0,0,'R');

                $VTot = "SELECT SUM(n.valor) FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND c.clase != 6 AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND c.clase != 6 AND n.periodo = '$periodo' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'   ";

                $valT = $mysqli->query($VTot);
                $TOT = mysqli_fetch_row($valT);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
                $pdf->Ln(3);
            } # FINALIZA EL CICLO DE LOS EMPLEADOS PARA LA PENSION

            $pdf->Ln(2);
            $pdf->Cell(190,0.5,'',1);
            $pdf->Ln(1);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$PEN[0]'";
            $ter = $mysqli->query($Terce);
            $T = mysqli_fetch_row($ter);

            $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                   
                        WHERE af.tipo = 2 AND af.tercero = '$T[0]'  AND n.periodo = '$periodo' AND n.concepto = 2 and af.fecharetiro >= '$retE[1]'   OR af.tipo = 2 AND af.tercero = '$T[0]'  AND n.periodo = '$periodo' AND n.concepto = 2 and af.fecharetiro is null and af.fechaafiliacion <='$retE[1]'   ";
            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

            $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo'  and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";

            $vaem1 = $mysqli->query($vemple1);
            $valE1 = mysqli_fetch_row($vaem1);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[0],2,'.',',')),0,0,'R');

            $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'  AND n.periodo = '$periodo' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'     ";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);

            $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

            $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]'   AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]'   AND n.periodo = '$periodo' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'    ";

            $foso1 = $mysqli->query($Fsol1);
            $SolF1 = mysqli_fetch_row($foso1);

            $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[0],2,'.',',')),0,0,'R');

            $VTot12 = "SELECT SUM(n.valor) FROM gn_novedad n
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND c.clase != 6 AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND c.clase != 6 AND n.periodo = '$periodo'  and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";

            $VAl12  = $mysqli->query($VTot12);
            $TOVAL12 = mysqli_fetch_row($VAl12);
            $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');
            $pdf->Ln(10);
        } 

        ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
        $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
      
        $valor2 = $mysqli->query($arl);

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(40,18,utf8_decode('TIPO DE FONDO:'),0,0,'L');
        $pdf->Cell(20,18,utf8_decode(''),0,0,'L');
        $pdf->Cell(20,18,utf8_decode('ARL'),0,0,'L');
        $pdf->Ln(8);

        while($AR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'L');
            $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'L');
            $pdf->Cell(30,18,utf8_decode('RAZON SOCIAL:'),0,0,'L');
            $pdf->Cell(50,18,utf8_decode($AR[1]),0,0,'L');
            $pdf->Ln(12);
        
            $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');    
            $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=60;$mc2=30;$mc3=30;

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);
            

            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                        e.codigointerno,
                                        e.tercero,
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
                                        tr.numeroidentificacion
                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro>='$retE[1]' 
                    OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'    and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  
                    OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'   OR  

                    c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                    OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'    and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   
                    OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro is null and af.fechaafiliacion <='$retE[1]'  ";

            $resemp = $mysqli->query($sqlemp);

            while($ConE = mysqli_fetch_row($resemp)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' and periodo = '$periodo' AND concepto IN (78,416)";
                $bas = $mysqli->query($base);
                $B = mysqli_fetch_row($bas);

                $pdf->cellfitscale($mc2,5,utf8_decode($B[0]),0,0,'R');
                
                $vpat = "SELECT sum(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE  n.concepto =363   AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' ";
          
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                $pdf->Ln(3);
            }

            $pdf->Ln(1);
            $pdf->Cell(140,0.5,'',1);
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                      WHERE n.periodo = '$periodo' AND n.concepto = 2";
            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');
            
            $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico WHERE n.concepto =363 AND n.periodo = '$periodo'";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
        } 

        ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

        $caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t 
                LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
      
        $valor2 = $mysqli->query($caja);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(20,18,utf8_decode('CAJA DE COMPENSACION FAMILIAR'),0,0,'C');
        $pdf->Ln(12);

        while($AR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(55,18,utf8_decode($AR[1]),0,0,'C');
            $pdf->Ln(12);
        
            $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');    
            $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=60;$mc2=30;$mc3=30;

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);
            

            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                        e.codigointerno,
                                        e.tercero,
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
                                        tr.numeroidentificacion
                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'
                    OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'
                    OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR

                    c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                    OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]' 
                    OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";

            $resemp = $mysqli->query($sqlemp);

            while($ConE = mysqli_fetch_row($resemp)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' and periodo = '$periodo' AND concepto IN (78,416)";
                $bas = $mysqli->query($base);
                $B = mysqli_fetch_row($bas);

                $pdf->cellfitscale($mc2,5,utf8_decode($B[0]),0,0,'R');
                
                $vpat = "SELECT sum(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "WHERE  c.codigo =800   AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo' ";
          
                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                $pdf->Ln(3);
            }

            $pdf->Ln(1);
            $pdf->Cell(140,0.5,'',1);
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

            $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n
                      WHERE n.periodo = '$periodo' AND n.concepto = 2";
            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');
            
            $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico WHERE c.codigo =800 AND n.periodo = '$periodo'";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);

            $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
        } 

        ////////////// FINALIZA EL FONDO DE CAJA DE COMPENSACION /////////////////////////////////////////////

        $paraf = "SELECT t.id_unico, t.razonsocial,t.numeroidentificacion FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                . " WHERE c.tipofondo = 5  AND c.tipoentidadcredito is not null ";
        
        $valor2 = $mysqli->query($paraf);

        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(6);
        $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
        $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
        $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
        $pdf->Ln(12);

        while($PAR = mysqli_fetch_row($valor2)){

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);
            $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(20,18,utf8_decode($PAR[2]),0,0,'C');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
            $pdf->SetFont('Arial','B',7);
            $pdf->cellfitscale(80,18,utf8_decode($PAR[1]),0,0,'C');
            $pdf->Ln(12);
            
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
            $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
            $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
            $pdf->Ln(8);

            $mc0=20;$mc1=60;$mc2=30;$mc3=30;

            #consulta la fecha inicial y la fecha final del periodo
            $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
            $retEmp = $mysqli->query($retiro);
            $retE = mysqli_fetch_row($retEmp);
            

            $sqlemp = "SELECT DISTINCT  e.id_unico,
                                        e.codigointerno,
                                        e.tercero,
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
                                        tr.numeroidentificacion
                    FROM gn_empleado e
                    LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                    LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                    LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                    LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL   and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' 
                    OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'   and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' 
                    OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' OR 

                    c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL   and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                    OR c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'   and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]' 
                    OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico !=2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'   ";

            $resemp = $mysqli->query($sqlemp);

            while($ConE = mysqli_fetch_row($resemp)){

                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]' and periodo = '$periodo' AND concepto IN (78,416)";
                $bas = $mysqli->query($base);
                $B = mysqli_fetch_row($bas);

                $pdf->cellfitscale($mc2,5,utf8_decode($B[0]),0,0,'R');

                $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                        . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]'  AND n.periodo = '$periodo'";

                $vapa = $mysqli->query($vpat);
                $valP = mysqli_fetch_row($vapa);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');
                $pdf->Ln(3);
            }

            $pdf->Ln(1);
            $pdf->Cell(140,0.5,'',1);
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',8);
            $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
            $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');
            
            $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$PAR[0]'";
            $ter = $mysqli->query($Terce);
            $T = mysqli_fetch_row($ter);

            $base1 = "SELECT SUM(n.valor) FROM gn_novedad n 
                        WHERE  n.periodo = '$periodo' AND n.concepto = '2'";
                   
                    
            $bas1 = $mysqli->query($base1);
            $B1 = mysqli_fetch_row($bas1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

            $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                    . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                    . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                    . "WHERE t.id_unico = '$PAR[0]'  AND n.periodo = '$periodo'";

            $vapa1 = $mysqli->query($vpat1);
            $valP1 = mysqli_fetch_row($vapa1);

            $pdf->cellfitscale($mc2,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
        }

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

            while($SAL = mysqli_fetch_row($valor)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,5,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,5,utf8_decode($SAL[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(30,5,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(25,5,utf8_decode($SAL[1]),0,0,'C');
                $pdf->Ln(4);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
                $pdf->Ln(5);

                $mc0=20;$mc1=60;$mc2=30;$mc3=30;$mc4=30;

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                          e.codigointerno,
                          e.tercero,
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
                          tr.numeroidentificacion
                FROM gn_empleado e
                LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                WHERE c.tipofondo = 1 AND c.clase = 2   AND t.numeroidentificacion =  '$SAL[0]' ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                    $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof'";

                    $vaem = $mysqli->query($vemple);
                    $valE = mysqli_fetch_row($vaem);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                    #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                    $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON c.id_unico = n.concepto LEFT JOIN gn_empleado e ON n.empleado = e.id_unico LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo LEFT JOIN gf_tercero t ON af.tercero = t.id_unico WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof'";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                    $VTot = "SELECT SUM(n.valor)

                            FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]'  AND c.clase != 6";

                    $valT = $mysqli->query($VTot);
                    $TOT = mysqli_fetch_row($valT);
                    $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }

                $pdf->Ln(2);
                $pdf->Cell(200,0.5,'',1);
                $pdf->Ln(1);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$SAL[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                       
                        . "WHERE af.tipo = 1 AND af.tercero = '$T[0]'   AND n.concepto = 2";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                        . "WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]'  AND c.tipofondo = '$tipof'";

                $VToEM = $mysqli->query($ToEM);
                $VAEMP = mysqli_fetch_row($VToEM);
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                        . "WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'  AND c.tipofondo = '$tipof'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

                $VTot1 = "SELECT SUM(n.valor)

                        FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]'  AND c.clase != 6";

                $valT1 = $mysqli->query($VTot1);
                $TOT1 = mysqli_fetch_row($valT1);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');
                $pdf->Ln(10);
            }
            ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////
        }elseif($tipof == 2){

            $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero "
                    . "WHERE af.tipo = 2 ORDER BY t.id_unico";
            $valor1 = $mysqli->query($pension);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
            $pdf->Ln(12);

            while($PEN = mysqli_fetch_row($valor1)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($PEN[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(25,18,utf8_decode($PEN[1]),0,0,'C');
                $pdf->Ln(12);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(50,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Fondo Solid'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Total'),1,0,'C');
                $pdf->Ln(8);

                $mc0=20;$mc1=50;$mc2=20;$mc3=25;$mc4=25;$mc5=25;$mc6=25;

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                        FROM gn_empleado e
                        LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                        LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 2 AND c.clase = 2   AND t.numeroidentificacion =  '$PEN[0]' ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                            . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof'";

                    $vaem = $mysqli->query($vemple);
                    $valE = mysqli_fetch_row($vaem);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                            . "WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof'";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                            . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                            . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                            . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                            . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof'";

                    $foso = $mysqli->query($Fsol);
                    $SolF = mysqli_fetch_row($foso);

                    $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[0],2,'.',',')),0,0,'R');


                    $VTot = "SELECT SUM(n.valor)

                        FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]'  AND c.clase != 6";

                    $valT = $mysqli->query($VTot);
                    $TOT = mysqli_fetch_row($valT);
                    $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');

                    $pdf->Ln(3);
                } # FINALIZA EL CICLO DE LOS EMPLEADOS PARA LA PENSION

                $pdf->Ln(2);
                $pdf->Cell(190,0.5,'',1);
                $pdf->Ln(1);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$PEN[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                       
                        . "WHERE af.tipo = 2 AND af.tercero = '$T[0]'   AND n.concepto = 2";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                        . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]'  AND c.tipofondo = '$tipof'";

                $vaem1 = $mysqli->query($vemple1);
                $valE1 = mysqli_fetch_row($vaem1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                        . "WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'  AND c.tipofondo = '$tipof'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

                $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo "
                        . "LEFT JOIN gf_tercero t ON af.tercero = t.id_unico "
                        . "WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]'  AND c.tipofondo = '$tipof'";

                $foso1 = $mysqli->query($Fsol1);
                $SolF1 = mysqli_fetch_row($foso1);

                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[0],2,'.',',')),0,0,'R');

                $VTot12 = "SELECT SUM(n.valor)

                        FROM gn_novedad n
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                        WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]'  AND c.clase != 6";

                $VAl12  = $mysqli->query($VTot12);
                $TOVAL12 = mysqli_fetch_row($VAl12);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');
                $pdf->Ln(10);
            }

            ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
        }elseif($tipof == 3){

            $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero "
                    . "WHERE af.tipo = 4 ORDER BY t.id_unico";
            $valor2 = $mysqli->query($arl);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
            $pdf->Ln(12);

            while($AR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
                $pdf->Ln(12);
                $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');

                $pdf->Ln(8);

                $mc0=20;$mc1=60;$mc2=30;$mc3=30;

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico !=2 ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){


                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                            . "WHERE  n.concepto =363   AND n.empleado = '$ConE[0]'  ";
                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                       
                        . "WHERE af.tipo = 4 AND af.tercero = '$T[0]'   AND n.concepto = 2";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.concepto =363 ";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
            }


            ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////

        }elseif($tipof == 4){
            $caja = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero "
                    . "WHERE af.tipo = 6 ORDER BY t.id_unico";
            $valor2 = $mysqli->query($caja);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('CAJA DE COMPENSACION'),0,0,'C');
            $pdf->Ln(12);

            while($AR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
                $pdf->Ln(12);
                $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');

                $pdf->Ln(8);

                $mc0=20;$mc1=60;$mc2=30;$mc3=30;

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 6 AND c.clase = 7  AND e.id_unico !=2 ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){


                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT sum(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                            . "WHERE  n.concepto =256   AND n.empleado = '$ConE[0]'  ";
                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                       
                        . "WHERE af.tipo = 6 AND af.tercero = '$T[0]'   AND n.concepto = 2";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.concepto =256 ";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
            }


            ////////////// FINALIZA EL FONDO DE CAJA DE COMPENSACION /////////////////////////////////////////////
        }else{

            $paraf = "SELECT t.id_unico, t.razonsocial FROM gn_concepto c LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico WHERE c.tipofondo = 5 AND c.tipoentidadcredito is not NULL";
            $valor2 = $mysqli->query($paraf);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
            $pdf->Ln(12);

            while($PAR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($PAR[2]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(80,18,utf8_decode($PAR[1]),0,0,'C');
                $pdf->Ln(12);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');

                $pdf->Ln(8);

                $mc0=20;$mc1=60;$mc2=30;$mc3=30;

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 2 AND c.clase = 2 AND e.id_unico !=2 ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416)";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                            . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                            . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof'";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        
                        WHERE  n.concepto = 2";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "WHERE t.id_unico = '$PAR[0]'  AND c.tipofondo = '$tipof'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
            }

        }

        ///////////// FINALIZA LA CONDCION CUANDO EL PERIODO ES VACIO Y EL TIPO DE FONDO NO  /////////////////////////////

    }else{


        $mc0=20;$mc1=40;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

        if($tipof == 1){

            $salud = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero "
                    . "WHERE af.tipo = 1 ORDER BY t.id_unico";
            $valor = $mysqli->query($salud);

            $fondo = "SELECT id_unico, nombre FROM gn_tipo_fondo";
            $Fond = $mysqli->query($fondo);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('SALUD'),0,0,'C');
            $pdf->Ln(12);

            while($SAL = mysqli_fetch_row($valor)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,5,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,5,utf8_decode($SAL[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(30,5,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(25,5,utf8_decode($SAL[1]),0,0,'C');
                $pdf->Ln(4);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Empleados'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Total'),1,0,'C');
                $pdf->Ln(5);

                $mc0=20;$mc1=60;$mc2=20;$mc3=30;$mc4=30;$mc5=30;

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                
                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion ='$SAL[0]' AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro>='$retE[1]' 
                            OR c.tipofondo =1 AND c.clase = 2  AND t.numeroidentificacion ='$SAL[0]' AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'
                            OR c.tipofondo =1  AND c.clase = 2  AND t.numeroidentificacion ='$SAL[0]'  AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro>='$retE[1]'    OR  

                             c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion ='$SAL[0]' AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                            OR c.tipofondo =1 AND c.clase = 2  AND t.numeroidentificacion ='$SAL[0]' AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]' 
                            OR c.tipofondo =1  AND c.clase = 2  AND t.numeroidentificacion ='$SAL[0]'  AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416) AND periodo = '$periodo'";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    #suma el total de los valores de las novedades con conccepto aporte de salud del empleado y los agrupa por la entidad de salud
                    $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                WHERE c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 1 AND c.clase = 2    AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                    $vaem = $mysqli->query($vemple);
                    $valE = mysqli_fetch_row($vaem);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                    #suma el total de los valores de las novedades con conccepto aporte de salud del patrono y los agrupa por la entidad de salud
                    $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                WHERE c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 1  AND c.clase = 7  AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'    ";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    #calcula el total de los dos aportes y los agrupa por cada entidad de salud
                    $VTot = "SELECT SUM(n.valor)

                            FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND c.clase !=6  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 1 AND n.empleado = '$ConE[0]' AND c.clase !=6  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                    $valT = $mysqli->query($VTot);
                    $TOT = mysqli_fetch_row($valT);
                    $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');
                    $pdf->Ln(3);
                }


                $pdf->Ln(2);
                $pdf->Cell(190,0.5,'',1);
                $pdf->Ln(1);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$SAL[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                       
                            WHERE af.tipo = 1 AND af.tercero = '$T[0]'   AND n.concepto = 2 AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'  OR af.tipo = 1 AND af.tercero = '$T[0]'   AND n.concepto = 2 AND n.periodo = '$periodo'  and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $ToEM = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]'  AND c.tipofondo = '$tipof' 
                        AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'  or c.tipofondo = 1 AND c.clase = 2  AND t.numeroidentificacion = '$SAL[0]'  AND c.tipofondo = '$tipof' 
                        AND n.periodo = '$periodo' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'     ";
                $VToEM = $mysqli->query($ToEM);
                $VAEMP = mysqli_fetch_row($VToEM);
                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($VAEMP[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                        LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                        WHERE c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'  AND c.tipofondo = '$tipof' 
                        AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 1  AND c.clase = 7 AND t.numeroidentificacion = '$SAL[0]'  AND c.tipofondo = '$tipof'  AND n.periodo = '$periodo' and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

                $VTot1 = "SELECT SUM(n.valor)

                            FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND c.clase !=6  AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 1 AND t.numeroidentificacion = '$SAL[0]' AND c.clase !=6  AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                $valT1 = $mysqli->query($VTot1);
                $TOT1 = mysqli_fetch_row($valT1);
                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($TOT1[0],2,'.',',')),0,0,'R');
                $pdf->Ln(10);
            }

            ///////////////////// FINALIZA EL FONDO DE SALUD //////////////////////////////////////////////////////
        }elseif($tipof == 2){

            $pension = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero "
                    . "WHERE af.tipo = 2 ORDER BY t.id_unico";
            $valor1 = $mysqli->query($pension);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PENSION'),0,0,'C');
            $pdf->Ln(12);

            while($PEN = mysqli_fetch_row($valor1)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($PEN[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(25,18,utf8_decode($PEN[1]),0,0,'C');
                $pdf->Ln(12);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(50,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(20,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Aporte Empleados'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Fondo Solid'),1,0,'C');
                $pdf->Cell(25,5, utf8_decode('Total'),1,0,'C');
                $pdf->Ln(8);

                $mc0=20;$mc1=50;$mc2=20;$mc3=25;$mc4=25;$mc5=25;$mc6=25;

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE c.tipofondo = 2 AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro>='$retE[1]' 
                            OR c.tipofondo =2 AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado = 2 
                            AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'   and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]' 
                            OR c.tipofondo =2  AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]'  AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'   OR

                            c.tipofondo = 2 AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                            OR c.tipofondo =2 AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]' AND vr.estado = 2 
                            AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'   and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                            OR c.tipofondo =2  AND c.clase = 2  AND t.numeroidentificacion ='$PEN[0]'  AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416) AND periodo = '$periodo'";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vemple = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof'  AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81  AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof'  AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'     ";

                    $vaem = $mysqli->query($vemple);
                    $valE = mysqli_fetch_row($vaem);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND c.clase = 7 AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $Fsol = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof'  AND n.periodo = '$periodo'   and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81  AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof'  AND n.periodo = '$periodo'   and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                    $foso = $mysqli->query($Fsol);
                    $SolF = mysqli_fetch_row($foso);

                    $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF[0],2,'.',',')),0,0,'R');

                    $VTot = "SELECT SUM(n.valor)

                            FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND c.clase !=6  AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND n.empleado = '$ConE[0]' AND c.clase !=6  AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                    $valT = $mysqli->query($VTot);
                    $TOT = mysqli_fetch_row($valT);
                    $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOT[0],2,'.',',')),0,0,'R');

                    $pdf->Ln(3);
                } # FINALIZA EL CICLO DE LOS EMPLEADOS PARA LA PENSION

                $pdf->Ln(2);
                $pdf->Cell(190,0.5,'',1);
                $pdf->Ln(1);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                 $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$PEN[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico 
                       
                        WHERE af.tipo = 2 AND af.tercero = '$T[0]'   AND n.concepto = 2 AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'   OR af.tipo = 2 AND af.tercero = '$T[0]'   AND n.concepto = 2 AND n.periodo = '$periodo'  and af.fecharetiro is null and af.fechaafiliacion <='$retE[1]'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vemple1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                                LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                                LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                                WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]'  
                                AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico !=81 AND t.numeroidentificacion = '$PEN[0]'  
                                AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'";

                $vaem1 = $mysqli->query($vemple1);
                $valE1 = mysqli_fetch_row($vaem1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valE1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'  AND c.tipofondo = '$tipof' 
                            AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'  OR c.tipofondo = 2  AND c.clase = 7 AND t.numeroidentificacion = '$PEN[0]'  AND c.tipofondo = '$tipof' 
                            AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc4,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');

                $Fsol1 = "SELECT  SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo 
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico 
                            WHERE c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]'  
                            AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2  AND c.clase = 2 AND c.id_unico =81 AND t.numeroidentificacion = '$PEN[0]'  
                            AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  ";

                $foso1 = $mysqli->query($Fsol1);
                $SolF1 = mysqli_fetch_row($foso1);

                $pdf->cellfitscale($mc5,5,utf8_decode(number_format($SolF1[0],2,'.',',')),0,0,'R');

                $VTot12 = "SELECT SUM(n.valor)

                            FROM gn_novedad n
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            WHERE c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND c.clase !=6  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' and af.fecharetiro >= '$retE[1]'   OR c.tipofondo = 2 AND t.numeroidentificacion = '$PEN[0]' AND c.clase !=6  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo' and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'   ";

                $VAl12  = $mysqli->query($VTot12);
                $TOVAL12 = mysqli_fetch_row($VAl12);
                $pdf->cellfitscale($mc6,5,utf8_decode(number_format($TOVAL12[0],2,'.',',')),0,0,'R');
                $pdf->Ln(10);
            }

            ////////// FINALIZA FONDO DE PENSION ////////////////////////////////////
        }elseif($tipof == 3){

            $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                    . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 4 ORDER BY t.id_unico";
            $valor2 = $mysqli->query($arl);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('ARL'),0,0,'C');
            $pdf->Ln(12);

            while($AR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
                $pdf->Ln(12);
                $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');

                $pdf->Ln(8);

                $mc0=20;$mc1=60;$mc2=30;$mc3=30;

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'
                            OR c.tipofondo =2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'
                            OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro>='$retE[1]'   OR
                            c.tipofondo = 2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]'  
                            OR c.tipofondo =2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'  and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                            OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";
                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){


                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416) AND periodo = '$periodo'";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT sum(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                            . "WHERE  n.concepto =363   AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'";
                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                       
                        . "WHERE af.tipo = 4 AND af.tercero = '$T[0]'   AND n.concepto = 2 AND n.periodo = '$periodo'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');


                 $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n "
                         . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                         . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                         . "WHERE n.concepto =363    AND n.periodo = '$periodo'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
            }

            ////////////// FINALIZA EL FONDO DE ARL /////////////////////////////////////////////
        }elseif($tipof == 4){
            $arl = "SELECT DISTINCT t.numeroidentificacion, t.razonsocial FROM gf_tercero t "
                    . "LEFT JOIN gn_afiliacion af ON t.id_unico = af.tercero WHERE af.tipo = 6 ORDER BY t.id_unico";
            $valor2 = $mysqli->query($arl);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('CAJA COMPENSACION'),0,0,'C');
            $pdf->Ln(12);

            while($AR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($AR[0]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(50,18,utf8_decode($AR[1]),0,0,'C');
                $pdf->Ln(12);
                $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');

                $pdf->Ln(8);

                $mc0=20;$mc1=60;$mc2=30;$mc3=30;

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                                                                                            

                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL   and af.fecharetiro>='$retE[1]'   and af.fechaafiliacion <='$retE[1]'
                            OR c.tipofondo =2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'    and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'
                            OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  or 

                            c.tipofondo = 2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL   and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                            OR c.tipofondo =2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'    and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                            OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'  and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";
                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){


                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416) AND periodo = '$periodo'";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT sum(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                            . "WHERE  n.concepto =256   AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'";
                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $Terce = "SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion = '$AR[0]'";
                $ter = $mysqli->query($Terce);
                $T = mysqli_fetch_row($ter);

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico "
                       
                        . "WHERE af.tipo = 6 AND af.tercero = '$T[0]'   AND n.concepto = 2 AND n.periodo = '$periodo'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');


                 $vpat1 = "SELECT sum(n.valor) FROM gn_novedad n "
                         . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                         . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                         . "WHERE n.concepto =256    AND n.periodo = '$periodo'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
            }

            ////////////// FINALIZA EL FONDO DE CAJA COMPENSACION /////////////////////////////////////////////
        }else{

            $paraf = "SELECT t.id_unico, t.razonsocial FROM gn_concepto c "
                    . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                    . "WHERE c.tipofondo = 5 AND c.tipoentidadcredito is not NULL";
            $valor2 = $mysqli->query($paraf);

            $pdf->SetFont('Arial','B',8);
            $pdf->SetX(6);

            $pdf->Cell(37,18,utf8_decode('TIPO DE FONDO:'),0,0,'C');
            $pdf->Cell(12,18,utf8_decode(''),0,0,'C');
            $pdf->Cell(12,18,utf8_decode('PARAFISCALES'),0,0,'C');
            $pdf->Ln(12);

            while($PAR = mysqli_fetch_row($valor2)){

                $pdf->SetFont('Arial','B',8);
                $pdf->SetX(6);
                $pdf->Cell(20,18,utf8_decode('NIT:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(20,18,utf8_decode($PAR[2]),0,0,'C');
                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(20,18,utf8_decode('RAZON SOCIAL:'),0,0,'C');
                $pdf->SetFont('Arial','B',7);
                $pdf->cellfitscale(80,18,utf8_decode($PAR[1]),0,0,'C');
                $pdf->Ln(12);

                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(20,5, utf8_decode('Código'),1,0,'C');
                $pdf->Cell(60,5, utf8_decode('Empleado'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Base'),1,0,'C');
                $pdf->Cell(30,5, utf8_decode('Aporte Patrono'),1,0,'C');

                $pdf->Ln(8);

                $mc0=20;$mc1=60;$mc2=30;

                #consulta la fecha inicial y la fecha final del periodo
                $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
                $retEmp = $mysqli->query($retiro);
                $retE = mysqli_fetch_row($retEmp);
                
                $sqlemp = "SELECT DISTINCT  e.id_unico,
                                            e.codigointerno,
                                            e.tercero,
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
                                            tr.numeroidentificacion
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico
                            LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                            LEFT JOIN gn_concepto c ON c.id_unico = n.concepto
                            LEFT JOIN gn_afiliacion af ON af.empleado = e.id_unico AND af.tipo = c.tipofondo
                            LEFT JOIN gf_tercero t ON af.tercero = t.id_unico
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                            WHERE c.tipofondo = 2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL   and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  or 
                            c.tipofondo = 2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL   and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  
                            OR c.tipofondo =2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'    and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'   OR c.tipofondo =2 AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) BETWEEN '$retE[0]' AND '$retE[1]'    and af.fecharetiro is null  and af.fechaafiliacion <='$retE[1]' 
                             OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'    and af.fecharetiro>='$retE[1]'  and af.fechaafiliacion <='$retE[1]'  OR c.tipofondo =2  AND c.clase = 2  AND e.id_unico != 2 AND vr.estado = 2 AND (SELECT fecha from gn_vinculacion_retiro vret where  empleado=e.id_unico and vret.estado=2 order by fecha desc LIMIT 1) > '$retE[0]'    and af.fecharetiro is null   and af.fechaafiliacion <='$retE[1]'  ";

                $resemp = $mysqli->query($sqlemp);

                while($ConE = mysqli_fetch_row($resemp)){

                    $pdf->SetFont('Arial','B',7);
                    $pdf->cellfitscale($mc0,5,utf8_decode($ConE[4]),0,0,'R');
                    $pdf->cellfitscale($mc1,5,utf8_decode($ConE[3]),0,0,'L');

                    $base = "SELECT SUM(valor) FROM gn_novedad  WHERE empleado = '$ConE[0]'  AND concepto IN (78,416) AND periodo = '$periodo'";
                    $bas = $mysqli->query($base);
                    $B = mysqli_fetch_row($bas);

                    $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B[0],2,'.',',')),0,0,'R');

                    $vpat = "SELECT SUM(n.valor) FROM gn_novedad n "
                            . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                            . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                            . "WHERE t.id_unico = '$PAR[0]' AND n.empleado = '$ConE[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'";

                    $vapa = $mysqli->query($vpat);
                    $valP = mysqli_fetch_row($vapa);

                    $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP[0],2,'.',',')),0,0,'R');

                    $pdf->Ln(3);
                }

                $pdf->Ln(1);
                $pdf->Cell(140,0.5,'',1);
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',8);
                $pdf->cellfitscale($mc0,5,utf8_decode(''),0,0,'C');
                $pdf->cellfitscale($mc1,5,utf8_decode('TOTAL'),0,0,'C');

                $base1 = "SELECT  SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON c.id_unico = n.concepto "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        
                       
                        . "WHERE  n.concepto = 2 AND n.periodo = '$periodo'";
                $bas1 = $mysqli->query($base1);
                $B1 = mysqli_fetch_row($bas1);

                $pdf->cellfitscale($mc2,5,utf8_decode(number_format($B1[0],2,'.',',')),0,0,'R');

                $vpat1 = "SELECT SUM(n.valor) FROM gn_novedad n "
                        . "LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                        . "LEFT JOIN gf_tercero t ON c.tipoentidadcredito = t.id_unico "
                        . "LEFT JOIN gn_empleado e ON n.empleado = e.id_unico "
                        . "WHERE t.id_unico = '$PAR[0]'  AND c.tipofondo = '$tipof' AND n.periodo = '$periodo'";

                $vapa1 = $mysqli->query($vpat1);
                $valP1 = mysqli_fetch_row($vapa1);

                $pdf->cellfitscale($mc3,5,utf8_decode(number_format($valP1[0],2,'.',',')),0,0,'R');
            }
        }

    }
}



ob_end_clean();#
$pdf->Output(0,'Informe_Terceros ('.date('d/m/Y').').pdf',0);
?>