<?php

header("Content-Type: text/html;charset=utf-8");
require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php'); 
require_once('../numeros_a_letras.php');    
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require_once('../numeros_a_letras.php');

session_start();
ob_start();
########################ARRAY DE MESES###########################################################
$meses = array('no','Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre');
#################################################################################################
#############################DATOS COMPAÑIA#######################################################
$comp =$_SESSION['compania'];
$dc ="SELECT id_unico, razonsocial, numeroidentificacion, digitoverficacion, "
        . "ruta_logo FROM gf_tercero WHERE id_unico =$comp";
$dc =$mysqli->query($dc);
$dc = mysqli_fetch_row($dc);
$razonsocial=$dc[1];
if(empty($dc[3])){
   $numeroI = $dc[2];
} else {
   $numeroI = $dc[2].' - '.$dc[3]; 
}
$ruta=$dc[4];
$usuario =$_SESSION['usuario'];
##########SLOGAN####################
$slog="SELECT valor FROM gs_parametros_basicos WHERE LOWER(nombre)='slogan'";
$slog = $mysqli->query($slog);
if(mysqli_num_rows($slog)>0) { 
    $slog = mysqli_fetch_row($slog);
    $slog = ucwords(mb_strtolower($slog[0]));
} else {
    $slog="";
}
###################################################################################################
##############################DATOS COMPROBANTE####################################################
$comprobante = $_GET['idcom'];
$dbc ="SELECT cn.id_unico, cn.numero, tc.nombre, DATE_FORMAT(cn.fecha,'%d/%m/%Y'),"
        . "CONCAT(ELT(WEEKDAY( cn.fecha) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA, "
        . "IF(CONCAT_WS(' ',
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
            tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.digitoverficacion, 
            d.direccion, tel.valor, cn.descripcion, cn.numerocontrato, tipc.nombre , 
            cn.tipocomprobante, tc.comprobante_pptal, cn.fecha , LOWER(tc.nombre) "
        . "FROM gf_comprobante_cnt cn "
        . "LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico "
        . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON tc.comprobante_pptal = tcp.id_unico "
        . "LEFT JOIN gf_tercero tr ON cn.tercero =tr.id_unico "
        . "LEFT JOIN gf_direccion d ON d.tercero = tr.id_unico "
        . "LEFT JOIN gf_telefono tel ON tel.tercero =tr.id_unico "
        . "LEFT JOIN gf_clase_contrato tipc ON tipc.id_unico = cn.clasecontrato "
        . "WHERE MD5(cn.id_unico) ='$comprobante'";
$dbc=$mysqli->query($dbc);
$dbc = mysqli_fetch_row($dbc);
$idCnt = $dbc[0];
$numero =$dbc[1];
$tipoNombre = ucwords(mb_strtoupper($dbc[2]));
$fechaC =$dbc[3];
$diaF =$dbc[4];
$terceroComprobante = $dbc[5];
if(empty($dbc[7])|| $dbc==""){
    $numeroidentificacion=$dbc[6];
}else {
    $numeroidentificacion =$dbc[6].' - '.$dbc[7];
}
$direccion =$dbc[8];
$telefono =$dbc[9];
$descripcion =$dbc[10];
$numerocon=$dbc[11];
$clasec =$dbc[12];
$tipocomprobante =$dbc[13];
$tipocomprobantepptal =$dbc[14];
$fechaComprobante =$dbc[15];
$tipocomprobantenombre =$dbc[16];
###################################################################################################
#####################CREACION PDF, HEAD AND FOOTER####################
class PDF extends FPDF
{
    function Header()
    { 
        
    global $numpaginas1;
    $numpaginas1=$this->PageNo();
    global $ruta;
    global $numero;
    global $razonsocial;
    global $numeroI;
    global $tipocomprobantenombre;
    global $fechaC;
    
      if ($ruta != '') {
            $this->Image('../' . $ruta, 10, 5, 25);
        }
        $this->Image('../logo/logoYopal.png', 175, 5, 40);
        $this->SetFont('Arial', 'B', 12);
        $this->SetX(35);
        $this->MultiCell(160, 5, utf8_decode(mb_strtoupper($razonsocial)),0, 'C');
        $this->Ln(2);
        $this->SetX(35);
        $this->Cell(160, 5, utf8_decode(('NIT:' . $numeroI)), 0, 0, 'C');
        $this->Ln(7);
        $this->SetX(35);
        $this->Cell(160, 5, utf8_decode(mb_strtoupper($tipocomprobantenombre) . ' ' . 'No: ' . $numero), 0, 0, 'C');
        $this->Ln(7);



    
    }      
    
    function Footer()
    {
    
        global $usuario;
        global $slog;
        if(empty($slog)){
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->Cell(63,10,'Elaborado por: '.strtoupper($usuario),0,0,'L');
            $this->Cell(63,10,'Fecha: '.date('d/m/Y'),0,0,'C');
            $this->Cell(64,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'R');
        } else {
          $this->SetY(-15);
          $this->SetFont('Arial','B',8);
          $this->SetX(10);
          $y1 = $this->GetY();
          $x1 = $this->GetX();
          $this->Cell(60,10,utf8_decode(''),0,0,'L');
          if(!empty($slog)|| $slog!='') { 
              $this->MultiCell(70,5,utf8_decode('"'.mb_strtoupper($slog,'utf-8').'"'),0,'C'); //Slogan
          } else {
              $this->MultiCell(70,5,utf8_decode(''),0,'C'); //Slogan
          }
          $y2 = $this->GetY();            
          $alto = $y2-$y1;
          $this->SetXY($x1,$y1);
          $this->Cell(60,$alto,utf8_decode('Elaborado por: '.strtoupper($usuario)),0,0,'C');
          $this->Cell(70,10,utf8_decode(''),0,0,'L');
          $this->Cell(30,$alto,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
          $this->Cell(30,$alto,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }
}

$pdf = new PDF('P','mm','Letter');      
$pdf->AliasNbPages();
$pdf->AddPage();
$yp=$pdf->GetY();


#####################################################################################
#* Datos Comprobante
#####################################################################################
$pdf->SetFont('Arial','B',10);
$pdf->Ln(5);
$pdf->Cell(35, 6, utf8_decode('Fecha: '), 0, 0, 'L');
$pdf->Cell(155, 6, utf8_decode($fechaC), 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(35, 6,utf8_decode('Nombre'),0, 0, 'L');
$pdf->Cell(155, 6,utf8_decode($terceroComprobante),0, 0, 'L');

$pdf->Ln(5);
$pdf->Cell(35, 6,utf8_decode('CC o Nit: '),0, 0, 'L');
$pdf->Cell(60, 6,utf8_decode($numeroidentificacion),0, 0, 'L');

$pdf->Cell(30, 6,utf8_decode('Teléfonos: '),0, 0, 'L');
$pdf->Cell(65, 6,utf8_decode($telefono),0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(35, 6,utf8_decode('Dirección: '),0, 0, 'L');
$pdf->Cell(60, 6,utf8_decode($direccion),0, 0, 'L');


$Ordenes = "";
#Ordenes de pago
$sqlOrdenesP = "SELECT
  comPtal.numero,
  tcp.codigo,
  comPtal.descripcion, 
  comPtal.id_unico 
FROM
  gf_comprobante_pptal comPtal
LEFT JOIN
  gf_detalle_comprobante_pptal detComPtal ON detComPtal.comprobantepptal = comPtal.id_unico
LEFT JOIN
  gf_detalle_comprobante detComp ON detComp.detallecomprobantepptal = detComPtal.id_unico
LEFT JOIN
  gf_tipo_comprobante_pptal tcp ON comPtal.tipocomprobante = tcp.id_unico
WHERE
  detComp.comprobante=".$idCnt;
        
$resultO = $mysqli->query($sqlOrdenesP);
$E = mysqli_num_rows($resultO);
while ( $O = mysqli_fetch_array($resultO)) {
  if($E<1){
    $Ordenes .= $O[0];
    $Ordenes .= ','.$O[0];
  }else{
    $Ordenes = $O[0];
  } 
  $idComp = $O[3];
}
$pdf->Ln(5); 

$pdf->Cell(35,6,'Orden Pago: ',0,'L');
$pdf->Cell(155, 6,utf8_decode($Ordenes),0, 0, 'L');
$pdf->Ln(5); 
$pdf->Cell(35,6,'Concepto: ',0,'L');
$pdf->Multicell(155, 5,utf8_decode($descripcion), 0, 'L');  

$pdf->Cell(35, 6,utf8_decode('Tipo de contrato: '),0, 0, 'L');
$pdf->Cell(60, 6,utf8_decode($clasec),0, 0, 'L');
$pdf->Cell(30, 6,utf8_decode('No de contrato: '),0, 0, 'L');
$pdf->Cell(65, 6,utf8_decode($numerocon),0, 0, 'L');
$pdf->Ln(5);
#Banco
$sqlMovFina = "SELECT detComp.id_unico idDetalleComp, detComp.valor valorDetalle, 
te.razonsocial banco, doc.numero, cuen.naturaleza naturalezaCuenta, cc.nombre, 
cuen.clasecuenta  
FROM gf_detalle_comprobante detComp 
LEFT JOIN gf_cuenta cuen ON cuen.id_unico = detComp.cuenta 
LEFT JOIN gf_centro_costo cc ON detComp.centrocosto = cc.id_unico
LEFT JOIN gf_cuenta_bancaria ctaB ON ctaB.cuenta = cuen.id_unico
LEFT JOIN gf_tercero te ON ctaB.banco = te.id_unico
LEFT JOIN gf_detalle_comprobante_mov doc ON detComp.id_unico = doc.comprobantecnt
WHERE md5(detComp.comprobante) = '".$comprobante."' AND cuen.clasecuenta = 11";
$banc = $mysqli->query($sqlMovFina);

while ($bc = mysqli_fetch_row($banc)) {
    $banco = $bc[2];
    $numCheque = $bc[3];
    $pdf->Cell(35, 6,utf8_decode('No Cheque :'),0, 0, 'L');
    $pdf->Cell(60, 6,utf8_decode($numCheque),0, 0, 'L');
    $pdf->Cell(30,5,'Banco: ',0,'L'); 
    $pdf->Cell(60,5,$banco,0,'L'); 
    $pdf->Ln(5);
}


###################################FIN DATOS COMPROBANTE##################################################
#####################################MOVIMIENTO CONTABLE ####################################################

$pdf->SetFont('Arial','B',10);
$pdf->Cell(190,5,'MOVIMIENTO CONTABLE',1,0,'C');

$pdf->Ln(5);
$pdf->Cell(25,5,utf8_decode('Cuenta'),1,0,'C');
$pdf->Cell(60,5,utf8_decode('Nombre de la Cuenta'),1,0,'C');
$pdf->Cell(55,5,utf8_decode('Tercero'),1,0,'C');
$pdf->Cell(25,5,utf8_decode('Débito'),1,0,'C');
$pdf->Cell(25,5,utf8_decode('Crédito'),1,0,'C');
$pdf->Ln(5);
$pdf->SetFont('Arial','',9);

$sqlMovFina ="SELECT detComp.id_unico idDetalleComp, 
    detComp.valor valorDetalle, cuen.nombre nombreCuenta, cuen.codi_cuenta codigoCuenta, 
    cuen.naturaleza naturalezaCuenta, 
    IF( CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos
      ) IS NULL OR CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos) = '',
      (tr.razonsocial),
      CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos )) AS NOMBRE, cuen.clasecuenta  
  FROM gf_detalle_comprobante detComp 
  LEFT JOIN gf_cuenta cuen ON cuen.id_unico = detComp.cuenta 
  LEFT JOIN gf_tercero tr ON detComp.tercero = tr.id_unico
  WHERE detComp.comprobante = ".$idCnt;
$movimientoFinanciero = $mysqli->query($sqlMovFina);
    //$pdf->Multicell(200,5,$sqlMovFina,0,'L'); 
$totalDebito = 0;
$totalCredito = 0;
$totalCheque = 0;
while ($rowMF = mysqli_fetch_array($movimientoFinanciero)) 
{   $nombCuen = mb_strtolower($rowMF[2],'utf-8');
    $nombCuen = ucwords($nombCuen);
     $altY = $pdf->GetY();
     if(strlen($nombCuen)>35){
        $altY = $pdf->GetY();
        if($altY>230){
            $pdf->AddPage();
        }
     }
    $debito = 0;
    $credito = 0;
    
    $centroCost = mb_strtolower($rowMF[5],'utf-8');
    $centroCost = ucwords($centroCost);
    $cod=$rowMF[3];
    
    if($rowMF[4] == 1)
    {    
      if($rowMF[1] < 0)
      {
        $credito = substr($rowMF[1], 1);
      }else{
        $debito = $rowMF[1];
      }
    }
    elseif($rowMF[4] == 2)
    {    
      if($rowMF[1] < 0)
      {
        $debito = substr($rowMF[1], 1);
      }else{
        $credito = $rowMF[1];
      }
    }
    if($rowMF[6] == 11)
    {
      if ($rowMF[1] > 0) {
       $totalCheque=0;
      }else{
       $totalCheque +=$rowMF[1];
      }
     
    }
  $xinicial = $pdf->GetX();
  $yinicial = $pdf->GetY();
  $pdf->Cell(25,5,utf8_decode($cod),0,0,'L');
  $y1 = $pdf->GetY();
  $x1 = $pdf->GetX();
  $pdf->Multicell(60,5,utf8_decode($nombCuen),0,'L');
  $y2 = $pdf->GetY();            
  $alto_de_fila = $y2-$y1;
  $posicionX = $x1 + 60;
  $pdf->SetXY($posicionX,$y1);
  $y3 = $pdf->GetY();
  $x3 = $pdf->GetX();
  $pdf->Multicell(55,5,utf8_decode($centroCost),0,'L');
  $y4 = $pdf->GetY();            
  $alto_de_fila1 = $y4-$y3;
  $posicionX = $x3 + 55;
  $pdf->SetXY($posicionX,$y1);
  $pdf->Cell(25,5,number_format($debito, 2, '.', ','),0,0,'R');
  $pdf->Cell(25,5,number_format($credito, 2, '.', ','),0,0,'R');
  #Determinar Valor máximo de altura
  $max = max($alto_de_fila,$alto_de_fila1);
  $pdf->SetXY($xinicial, $yinicial);
   $pdf->Cell(25,$max,(''),1,0,'L');
   $pdf->Cell(60,$max,(''),1,0,'L');
   $pdf->Cell(55,$max,(''),1,0,'L');
   $pdf->Cell(25,$max,(''),1,0,'L');
   $pdf->Cell(25,$max,(''),1,0,'L');
  #Salto de línea
  $pdf->Ln($max);  
    $totalDebito += $debito;
    $totalCredito += $credito;
      $altY = $pdf->GetY();
        if($altY>230){
            $pdf->AddPage();
        }
    
}

$pdf->Ln(5);

$pdf->Cell(110,15,'',1,0,'R');
$pdf->Cell(40,15,'',1,0,'R');
$pdf->Cell(40,15,'',1,0,'R');
$pdf->SetX(-200);
$pdf->Ln(3);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(110,5,utf8_decode('Débitos'),0,0,'R'); 
$pdf->Cell(40,5,utf8_decode('Créditos'),0,0,'R'); 
$pdf->Cell(40,5,utf8_decode('Valor A Girar'),0,0,'R'); 
$pdf->Ln(5);
$pdf->SetFont('Arial','',10);

if($totalDebito < 0)
{
  $totalDebito = substr($totalDebito, 1);
}
if($totalCredito < 0)
{
  $totalCredito = substr($totalCredito, 1);
}
if($totalCheque < 0)
{
  $totalCheque = substr($totalCheque, 1);
}


$totalChequeLetras = numtoletras($totalCheque);

$pdf->cellfitscale(110,5,number_format($totalDebito , 2, '.', ','),0,0,'R'); //Total débito
$pdf->cellfitscale(40,5,number_format($totalCredito , 2, '.', ','),0,0,'R'); //Total crédito
$pdf->cellfitscale(40,5,number_format($totalCheque , 2, '.', ','),0,0,'R'); //Total valor cheque
$pdf->Ln(10);

#####################################FIN MOVIMIENTO CONTABLE ####################################################

$pdf->SetFont('Arial','B',10);
$pdf->cellfitscale(190,5,utf8_decode('Valor a girar'),0,0,'L');
$pdf->Ln(5);
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(190,5,utf8_decode($totalChequeLetras),0,'L');


$pdf->SetFont('Arial','B',7);
$pdf->Ln(10);
$compania = $_SESSION['compania'];
$sqlTipoComp = "SELECT IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     UPPER(t.razonsocial),
     CONCAT_WS(' ',
     UPPER(t.nombreuno),
     UPPER(t.nombredos),
     UPPER(t.apellidouno),
     UPPER(t.apellidodos))) AS NOMBRE, ti.nombre, t.numeroidentificacion, UPPER(car.nombre) , 
     rd.fecha_inicio, rd.fecha_fin , t.tarjeta_profesional 
  FROM gf_tipo_comprobante tcp
  LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico 
  LEFT JOIN gf_responsable_documento rd ON td.id_unico = rd.tipodocumento 
  LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico
  LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = t.tipoidentificacion
  LEFT JOIN gf_cargo_tercero carTer ON carTer.tercero = t.id_unico
  LEFT JOIN gf_cargo car ON car.id_unico = carTer.cargo
  LEFT JOIN gg_tipo_relacion tipRel ON tipRel.id_unico = rd.tipo_relacion
  WHERE tcp.id_unico = $tipocomprobante 
  AND tipRel.nombre = 'Firma' ORDER BY rd.ORDEN ASC";

$tipComp = $mysqli->query($sqlTipoComp); 
$i = 0;

while ($rowTipComp = mysqli_fetch_array($tipComp))
{
     if(!empty($rowTipComp[5])){
            if($fechaComprobante <=$rowTipComp[5]){
                $firmaNom[$i] = $rowTipComp[0];
                $firmaCarg[$i] = $rowTipComp[3];
                $firmaTP[$i] = $rowTipComp[6];
                $i++;
            } 
     } elseif(!empty($rowTipComp[4]) ) {
                if($fechaComprobante >= $rowTipComp[4]){
                    $firmaNom[$i] = $rowTipComp[0];
                    $firmaCarg[$i] = $rowTipComp[3];
                    $firmaTP[$i] = $rowTipComp[6];
                    $i++;
                }
         
     } else {
            $firmaNom[$i] = $rowTipComp[0];
            $firmaCarg[$i] = $rowTipComp[3];
            $firmaTP[$i] = $rowTipComp[6];
            $i++;
     }
}

$firmaNom[$i] = 'FIRMA BENEFICIARIO'; //$rowTipComp[0];
$firmaNum[$i] = 'C.C. ó NIT';//$rowTipComp[1];
$firmaCarg[$i] = 'C.C. ó NIT';//$rowTipComp[2];

$numFirmas = $i;

if($numFirmas > 3)
  $numFirmas = 3;

for($i = 0; $i <= $numFirmas; $i++)
{
  $pdf->Cell(50,40,'',1,0,'C');
  
}

$pdf->Ln(24);
for($i = 0; $i <= $numFirmas; $i++)
{
  $pdf->Cell(1,0,'',0,0,'L');
  $pdf->Cell(45,0,'',1,0,'L');
  $pdf->Cell(4,0,'',0,0,'L');
}


$pdf->Ln(2);
for($i = 0; $i <=$numFirmas; $i++)
{
    if($firmaNom[$i]=='' || $firmaNom[$i]==""){
        $pdf->Cell(50,5,utf8_decode($firmaNom[$i]),0,0,'L');
    } else {
  $pdf->CellFitScale(50,5,utf8_decode($firmaNom[$i]),0,0,'L');
    }
    
}
  

$pdf->Ln(4);
for($i = 0; $i <= $numFirmas; $i++)
{
    if($firmaCarg[$i]=='' || $firmaCarg[$i]==""){
        $pdf->Cell(50,5,utf8_decode($firmaCarg[$i]),0,0,'L');
    } else {
        $pdf->CellFitScale(50,5,utf8_decode($firmaCarg[$i]),0,0,'L');
    }
 
}
$pdf->Ln(4);
for($i = 0; $i <= $numFirmas; $i++)
{
    if($firmaTP[$i]=='' || $firmaTP[$i]==""){
        $pdf->Cell(50,5,utf8_decode(''),0,0,'L');
    } else {
        $pdf->CellFitScale(50,5,utf8_decode('T.P. :'.$firmaTP[$i]),0,0,'L');
    }
 
}


##################################################################################


while (ob_get_length()) {
ob_end_clean();
}


$pdf->Output(0,'Informe_Certificado_Egreso_T.pdf',0);

?>

