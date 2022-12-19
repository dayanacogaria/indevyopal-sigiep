<?php
#

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

$fechaini = $mysqli->real_escape_string(''.$_POST["fechaInicial"].'');
$fechafin   = $mysqli->real_escape_string(''.$_POST["fechaFinal"].'');
$aptoI =$_POST['sltEspacioI'];
$aptoF =$_POST['sltEspacioF'];
$tp_fac =$_POST['sltTipo'];

$fechaI = DateTime::createFromFormat('d/m/Y', "$fechaini");
$fechaI= $fechaI->format('Y/m/d');

$fechaF = DateTime::createFromFormat('d/m/Y', "$fechafin");
$fechaF= $fechaF->format('Y/m/d');

$consulta = "SELECT         lower(t.razonsocial) as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            ti.nombre as tnom,
                            if(t.digitoverficacion='NULL',t.numeroidentificacion,CONCAT_WS('-',t.numeroidentificacion,t.digitoverficacion))  tnum,
                            d.direccion,
                            te.valor,
                            (@rownum:=@rownum+1) AS rownum
            FROM  (SELECT @rownum:=0) r,gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            left join gf_direccion d on d.tercero=t.id_unico
            left join gf_telefono te on te.tercero=t.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

 $nomcomp = "";
    $tipodoc = "";
    $numdoc = 0;
    $direc = "";
    $cels = "";

    while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = strtoupper ($fila['traz']);
        $tipodoc = utf8_decode($fila['tnom']);
        $numdoc = utf8_decode($fila['tnum']);
        $direc = utf8_decode($fila['direccion']);
        $rwn=$fila['rownum'];
        if($rwn=='1'){
            $cels= $fila['valor'];
        }else{
            $cels= $cels.' - '.$fila['valor'];
        }
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
    global $direc;
    global $fechaI;
    global $fechaF;
    global $aptoI;
    global $aptoF;
    global $tp_fac;
    global $per;
    global $emp;
    global $codi;
    global $ruta;
    global $valor;
    global $codcon;
    global $descon;
    global $numeroP;
    global $cels;

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
    $this->Cell(190,5,utf8_decode('COMPROBANTE DE FACTURACIÓN'),0,0,'C');

    // Salto de línea
    $this->Ln(3);
    $this->Cell(190,5,utf8_decode(ucwords($direc.'  CEL.: '.$cels)),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);



    }
    // Pie de página
   function Footer()
            {
         $this->SetFont('Arial','I',8);
          $this->cellfitscale(190,4,utf8_decode('Grupo AAA Asesores S.A.S.'),0,0,'C');
          $this->Ln(4);
          $this->cellfitscale(190,4,utf8_decode('www.sigiep.com'),0,0,'C');
     }
        }


// Creación del objeto de la clase heredada
$pdf = new PDF('P','mm','mcarta');



$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();

$pdf->SetFont('Arial','',8);
$consulta3 = "
            SELECT DISTINCT f.numero_factura,

            (SELECT  IF(CONCAT_WS(' ',
                   ta.nombreuno,
                   ta.nombredos,
                   ta.apellidouno,
                   ta.apellidodos)
                   IS NULL OR CONCAT_WS(' ',
                   ta.nombreuno,
                   ta.nombredos,
                   ta.apellidouno,
                   ta.apellidodos) = '',
                   (ta.razonsocial),
                   CONCAT_WS(' ',
                   ta.nombreuno,
                   ta.nombredos,
                   ta.apellidouno,
                   ta.apellidodos)) AS NOMBRE   FROM
            gph_espacio_habitable_tercero eht
            left join gf_tercero ta ON ta.id_unico=eht.id_tercero
            WHERE ta.id_unico = eht.id_tercero AND eht.id_perfil = '13'
            and eht.id_espacio_habitable = f.id_espacio_habitable and eht.principal='2'
            ORDER BY ta.id_unico ASC LIMIT 0,1) AS nom_propietario,

        (SELECT  if(ta.digitoverficacion='NULL',ta.numeroidentificacion,CONCAT_WS('-',ta.numeroidentificacion,ta.digitoverficacion)) from  gph_espacio_habitable_tercero eht
            left join gf_tercero ta ON ta.id_unico=eht.id_tercero
            WHERE ta.id_unico = eht.id_tercero AND eht.id_perfil = '13'
            and eht.id_espacio_habitable = f.id_espacio_habitable and eht.principal='2'
            ORDER BY ta.id_unico ASC LIMIT 0,1) AS nit_propietario,

            (SELECT IF(CONCAT_WS(' ',
                   ta.nombreuno,
                   ta.nombredos,
                   ta.apellidouno,
                   ta.apellidodos)
                   IS NULL OR CONCAT_WS(' ',
                   ta.nombreuno,
                   ta.nombredos,
                   ta.apellidouno,
                   ta.apellidodos) = '',
                   (ta.razonsocial),
                   CONCAT_WS(' ',
                   ta.nombreuno,
                   ta.nombredos,
                   ta.apellidouno,
                   ta.apellidodos)) AS NOMBRE  FROM
            gph_espacio_habitable_tercero eht
            left join gf_tercero ta ON ta.id_unico=eht.id_tercero
            WHERE ta.id_unico = eht.id_tercero AND eht.id_perfil = '14'
            and eht.id_espacio_habitable = f.id_espacio_habitable and eht.principal='2'
            ORDER BY ta.id_unico ASC LIMIT 0,1) AS nom_arrendatario,

        (SELECT  if(ta.digitoverficacion='NULL',ta.numeroidentificacion,CONCAT_WS('-',ta.numeroidentificacion,ta.digitoverficacion)) from  gph_espacio_habitable_tercero eht
            left join gf_tercero ta ON ta.id_unico=eht.id_tercero
            WHERE ta.id_unico = eht.id_tercero AND eht.id_perfil = '14'
            and eht.id_espacio_habitable = f.id_espacio_habitable and eht.principal='2'
            ORDER BY ta.id_unico ASC LIMIT 0,1) AS nit_arren,

            eh.descripcion,
            f.fecha_factura,
            (SELECT sum(esph.coeficiente) as coef
            FROM gh_espacios_habitables esph
            where esph.id_unico=f.id_espacio_habitable
            or esph.asociado=f.id_espacio_habitable) as coeficiente,

            (MONTHNAME(f.fecha_factura)) as mes,
            f.tipofactura,
            f.id_espacio_habitable
            FROM gp_factura f
            left join gf_tercero tp on tp.id_unico=f.tercero

            LEFT JOIN gh_espacios_habitables eh on eh.id_unico=f.id_espacio_habitable
            where f.fecha_factura>='$fechaI' and f.fecha_factura<='$fechaF'
            and f.id_espacio_habitable>='$aptoI' and f.id_espacio_habitable<='$aptoF'
            and f.tipofactura='$tp_fac'";
$nfactura = $mysqli->query($consulta3);
$row_volantes = mysqli_num_rows($nfactura);
$cont=0;
while($fila1 = mysqli_fetch_row($nfactura)){
    $cont++;
    $n_fac=$fila1[0];
    $tipo_fac=$fila1[9];
    $id_esp=$fila1[10];
    $f_fac=$fila1[6];
    $nom_prop=$fila1[1];
    $nom_arren=$fila1[3];
    $mes_ing=$fila1[8];
    if($mes_ing=='January'){
        $mes_ing='Enero';
    }else if($mes_ing=='February'){
        $mes_ing='Febrero';
    }else if($mes_ing=='March'){
        $mes_ing='Marzo';
    }else if($mes_ing=='April'){
        $mes_ing='Abril';
    }else if($mes_ing=='May'){
        $mes_ing='Mayo';
    }else if($mes_ing=='June'){
        $mes_ing='Junio';
    }else if($mes_ing=='July'){
        $mes_ing='Julio';
    }else if($mes_ing=='August'){
        $mes_ing='Agosto';
    }else if($mes_ing=='September'){
        $mes_ing='Septiembre';
    }else if($mes_ing=='October'){
        $mes_ing='Octubre';
    }else if($mes_ing=='November'){
        $mes_ing='Noviembre';
    }else if($mes_ing=='December'){
        $mes_ing='Diciembre';
    }

    //$codi = utf8_decode($fila1[0]);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(160,18,utf8_decode(''),0,0,'L');
    $pdf->Cell(23,18,utf8_decode('Nº FACTURA:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(25,18,utf8_decode($fila1[0]),0,0,'L');
    $pdf->Ln(4);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,18,utf8_decode('PROPIETARIO:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    //consulta que me repita en tercero propietario
    $pdf->Cell(80,18,utf8_decode($nom_prop),0,0,'L');
    //$pdf->Cell(25.5,18,utf8_decode(''),0,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,18,utf8_decode('NIT Ó CC:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    //igual para el nit
    $pdf->Cell(60,18,utf8_decode($fila1[2]),0,0,'L');

    $pdf->Ln(4);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,18,utf8_decode('ARRENDATARIO:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    //consulta qeu me repita los arrendatarios
    $pdf->Cell(80,18,utf8_decode($nom_arren),0,0,'L');
    //$pdf->Cell(9.5,18,utf8_decode(''),0,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,18,utf8_decode('NIT Ó CC:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    //igual para el nit
    $pdf->Cell(60,18,utf8_decode($fila1[4]),0,0,'L');
    $pdf->Ln(4);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,19,utf8_decode('INMUEBLE:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(80,18,utf8_decode($fila1[5]),0,0,'L');
    //$pdf->Cell(25.5,18,utf8_decode(''),0,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,18,utf8_decode('FECHA EMISIÓN:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(60,18,utf8_decode($fila1[6]),0,0,'L');

    $pdf->Ln(4);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,19,utf8_decode('COEFICIENTE:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(80,18,utf8_decode($fila1[7]),0,0,'L');
    //$pdf->Cell(25.5,18,utf8_decode(''),0,0,'C');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(30,18,utf8_decode('MES:'),0,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(60,18,utf8_decode(strtoupper($mes_ing)),0,0,'L');

    $pdf->SetFont('Arial','',10);
    $pdf->SetFont('Arial','B',8);

    $pdf->Ln(4);

    $pdf->SetFont('Arial','B',8);
    $pdf->SetFont('Arial','B',8);
    $pdf->Ln(8);

    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(150,5, utf8_decode('Concepto'),1,0,'C');
    $pdf->Cell(40,5, utf8_decode('Valor'),1,0,'C');
    $pdf->Ln(5);

    $mc1=150;$mc2=40;
    $xTmes = 0;
    $xDa   = 0;
    $consulta1 = "SELECT
                    c.id_unico,
                    c.nombre,
                    df.valor,
                    c.id_unico
                    from gp_factura f
                    left join gp_detalle_factura df on df.factura=f.id_unico
                    left join gp_concepto c on c.id_unico=df.concepto_tarifa
                    where f.numero_factura=$n_fac and f.tipofactura=$tipo_fac";

    $nom = $mysqli->query($consulta1);
    while($filaN = mysqli_fetch_row($nom)){
        $valor   = $filaN[2];
        $con_nom = $filaN[1];
        if($filaN[3] == 4){
            $xDa += $valor;
        }else{
            $xTmes += $valor;
        }

        $pdf->SetFont('Arial','',8);

        $pdf->cellfitscale($mc1,5,utf8_decode($con_nom),0,0,'L');
        $pdf->cellfitscale($mc2,5,utf8_decode(number_format($valor,2,'.',',')),0,0,'R');

        $pdf->Ln(3);
    }

    $pdf->Ln(1);

    $pdf->Cell(188,0.5,'',1);
    $pdf->Ln(1);
    $pdf->SetFont('Arial','B',8);


    $x =$pdf->GetX();
    $y =$pdf->GetY();

    $pdf->SetX($x + 45);
    $pdf->SetY($y);
    $pdf->SetFont('Arial','B',8);
    $pdf->cellfitscale(130,5,utf8_decode('TOTAL MES: '),0,0,'R');
    $total_pag=0;
    $consulta6 = "SELECT
                        sum(df.valor) as vlr
                        from gp_factura f
                        left join gp_detalle_factura df on df.factura=f.id_unico
                        left join gp_concepto c on c.id_unico=df.concepto_tarifa
                        where f.numero_factura=$n_fac and f.tipofactura=$tipo_fac";
    $neto = $mysqli->query($consulta6);
    $nt1 = mysqli_fetch_row($neto);

    $pdf->cellfitscale(50,5,utf8_decode(number_format($xTmes,2,'.',',')),0,0,'R');

    $pdf->ln(3);

    $pdf->cellfitscale(130,5,utf8_decode('TOTAL DEUDA ANTERIOR: '),0,0,'R');

    $consulta6 = "SELECT sum(df.valor_total_ajustado) as vlr_fac,
                        sum(dp.valor) as vlr_pago_fac
                        from gp_factura f
                        left join gp_detalle_factura df on df.factura=f.id_unico
                        left join gp_detalle_pago dp on dp.detalle_factura=df.id_unico
                        left join gp_concepto c on c.id_unico=df.concepto_tarifa
                        where  f.tipofactura=$tipo_fac and f.id_espacio_habitable=$id_esp
                        and f.fecha_factura<'$f_fac' ";
    $neto = $mysqli->query($consulta6);
    $nt2t = mysqli_fetch_row($neto);
    $vlr_fac=0;
    $vlr_pag_fac=0;
    $total_deuda_ant=0;
    if(empty($nt2t[0])){
        $vlr_fac=0;
    }else{
        $vlr_fac=$nt2t[0];
    }
    if(empty($nt2t[1])){
        $vlr_pag_fac=0;
    }else{
        $vlr_pag_fac=$nt2t[1];
    }
    $total_deuda_ant=$vlr_fac-$vlr_pag_fac;
    if($total_deuda_ant<=0){
        $total_deuda_ant=0;
    }else{
        $total_deuda_ant=$vlr_fac-$vlr_pag_fac;
    }
    $pdf->cellfitscale(50,5,utf8_decode(number_format($total_deuda_ant,2,'.',',')),0,0,'R');

    $pdf->ln(3);

    $pdf->cellfitscale(130,5,utf8_decode('NETO A PAGAR: '),0,0,'R');

    $nt = $total_deuda_ant+$nt1[0];

    $pdf->cellfitscale(50,5,utf8_decode(number_format($total_deuda_ant + $xTmes,2,'.',',')),0,0,'R');
    $pdf->Ln(10);




        $pdf->SetFont('Arial','B',8);

        $pdf->cellfitscale(110,5,utf8_decode('*NOTA: DESPUES DEL DIA 10 DE CADA MES TENDRA UN PAGO ADICIONAL DE $1.000 DIARIOS'),0,0,'R');
        $pdf->Ln(20);

            $pdf->Cell(80,0.1,'',1);
            $pdf->Cell(10,0.1,'',0);

            $pdf->Cell(10,0.1,'',0);
            $pdf->Cell(80,0.1,'',1);
            $pdf->Ln(2);
            $pdf->SetX(0);
            $pdf->cellfitscale(50,5,utf8_decode('ACEPTADA'),0,0,'R');
            $pdf->cellfitscale(20,5,utf8_decode(''),0,0,'R');
            $pdf->cellfitscale(90,5,utf8_decode('ELABORADA'),0,0,'R');

          $pdf->Ln(5);
          $pdf->cellfitscale(190,5,utf8_decode('Esta factura es un título valor Art 3 de la ley '
                  . '1231 de 2008, con la presente el copropietario y/o acepte declara haber '
                  . 'recibido real y materialmente los servicios descritos en este título valor.'),0,0,'R');
          $pdf->Ln(5);


    if($cont<$row_volantes){
     $pdf->AddPage();
    }


}

ob_end_clean();
$pdf->Output(0,'Comprobante Factura ('.date('d/m/Y').').pdf',0);
?>