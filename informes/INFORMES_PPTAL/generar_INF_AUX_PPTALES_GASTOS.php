<?php
##################################################################################################################################################################
#                                                                                                                           Modificaciones
##################################################################################################################################################################
#24/08/2017 |Erica G. | Modificacion logo, firmas
##############MODIFICACIONES##########################
#03/03/2017 | ERICA G. | ARREGLO BUSQUEDA MODIFICACION Y AFECTADO
#11/02/17 [8:40] Daniel N. - Agregado salto de línea para columna descripción.
#######################################################
require'../../fpdf/fpdf.php';
require'../../Conexion/conexion.php';
session_start();
ini_set('max_execution_time', 0);
$panno = $_SESSION['anno'];
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');
ob_start();
$rubini         = $mysqli->real_escape_string(''.$_POST["sltrubi"].'');
$rubfin         = $mysqli->real_escape_string(''.$_POST["sltrubf"].'');
$fechaini       = $mysqli->real_escape_string(''.$_POST["fechaini"].''); 
$fechafin       = $mysqli->real_escape_string(''.$_POST["fechafin"].'');
$compini        = $mysqli->real_escape_string(''.$_POST["sltTci"].'');
$compfin        = $mysqli->real_escape_string(''.$_POST["sltTcf"].'');

$head        = $mysqli->real_escape_string(''.$_POST["headH"].'');
$foot        = $mysqli->real_escape_string(''.$_POST["footH"].'');

#Conversión Fecha 
$fechaI = DateTime::createFromFormat('d/m/Y', "$fechaini");
$fechaI= $fechaI->format('Y/m/d');

$fechaF = DateTime::createFromFormat('d/m/Y', "$fechafin");
$fechaF= $fechaF->format('Y/m/d');


class PDF extends FPDF
{
// Cabecera de página  
function Header()
{ 
    global $nomcomp;
    global $tipodoc;
    global $numdoc;
    
    global $compini;
    global $compfin;        
    
    global $fechaini;       
    global $fechafin;       
    
    global $rub1;      
    global $rub2;
    global $rubMin;      
    global $rubMax;
    
    global $head;
    global $foot;
    global $ruta;
    
    $this->SetFont('Arial','B',10);
        // Título
    $this->SetY(10);
     if($ruta != '')
    {
        $this->Image('../../'.$ruta,60,6,20);
    }
    
    $this->Cell(330,5,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->setX(10);
    $this->SetFont('Arial','B',8);
    $this->Cell(330,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    //$this->Cell(121,8,'',0);
    $this->Cell(330, 5,$tipodoc.': '.$numdoc,0,0,'C');
    //$this->Cell(93,10,'',0);
    $this->SetFont('Arial','B',8);
    $this->SetX(10);
    $this->Cell(330,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',8);
    //$this->Cell(105,8,'',0);
    $this->Cell(330,5,utf8_decode('AUXILIAR COMPROBANTE PRESUPUESTAL - GASTOS'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(10);
    $this->Cell(330,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->Cell(335,5,utf8_decode('Entre Rubros '.$rub1.' y '.$rub2),0,0,'C');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->Cell(332,5,utf8_decode('Fechas '.$fechaini.' y '.$fechafin),0,0,'C');
    
    $this->Ln(3);
    
    $this->SetFont('Arial','',7);
    $this->Cell(330,5,utf8_decode('y Comprobantes '.$compini.' a '.$compfin),0,0,'C');
    
    $this->Ln(5);
    
    $this->SetX(25);
    #Empty Cells
    $this->Cell(16,9,utf8_decode(''),1,0,'C');
    $this->Cell(45,9,utf8_decode(''),1,0,'C');
    $this->Cell(70,9,utf8_decode(''),1,0,'C');
    $this->Cell(60,9,utf8_decode(''),1,0,'C');
    $this->Cell(26,9,utf8_decode(''),1,0,'C');
    $this->Cell(26,9,utf8_decode(''),1,0,'C');
    $this->Cell(26,9,utf8_decode(''),1,0,'C');
    $this->Cell(26,9,utf8_decode(''),1,0,'C');
    $this->Cell(26,9,utf8_decode(''),1,0,'C');
        
    
    $this->SetX(25);
    #Cell Row 1
    $this->Cell(16,9,utf8_decode('Fecha'),0,0,'C');
    $this->Cell(45,5,utf8_decode('Comprobante'),1,0,'C');
    $this->Cell(70,9,utf8_decode('Nombre del Tercero'),0,0,'C');
    $this->Cell(60,9,utf8_decode('Descripción'),0,0,'C');
    $this->Cell(26,9,utf8_decode('Valor'),0,0,'C');
    $this->Cell(26,9,utf8_decode('Modificaciones'),0,0,'C');
    $this->Cell(26,9,utf8_decode('Total'),0,0,'C');
    $this->Cell(26,9,utf8_decode('Afectado'),0,0,'C');
    $this->Cell(26,9,utf8_decode('Saldo'),0,0,'C');
    
    $this->Ln(5);
    
    $this->SetX(25);
    #Cell Row 2
    $this->Cell(16,4,utf8_decode(''),0,0,'C');
    $this->Cell(15,4,utf8_decode('Tipo'),1,0,'C');
    $this->Cell(30,4,utf8_decode('Numero'),1,0,'C');
    $this->Cell(70,4,utf8_decode(''),0,0,'C');
    $this->Cell(60,4,utf8_decode(''),0,0,'C');
    $this->Cell(26,4,utf8_decode(''),0,0,'C');
    $this->Cell(26,4,utf8_decode(''),0,0,'C');
    $this->Cell(26,4,utf8_decode(''),0,0,'C');
    $this->Cell(26,4,utf8_decode(''),0,0,'C');
    $this->Cell(26,4,utf8_decode(''),0,0,'C');
    
    $this->Ln(5);
    $this->Cell(326,5,'',0);
    }      
    
function Footer()
    {
    global $usuario;
    global $fechaActual;
    $this->SetY(-20);
    $this->SetFont('Arial','B',8);
    $this->SetX(10);
    $this->Cell(90,10,utf8_decode('Fecha: '.$fechaActual),0,0,'L');
    $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
    $this->Cell(90,10,utf8_decode('Usuario: '.mb_strtoupper($usuario)),0,0,'C');
    $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');        

    $rubuno = $rubini;      
    $rubdos = $rubfin;
    $compuno = $compini;
    $compdos = $compfin;        
    $fechauno = $fechaini;       
    $fechados = $fechafin;       

    
    $compania = $_SESSION['compania'];

$consulta = "SELECT         t.razonsocial as traz,
                            t.tipoidentificacion as tide,
                            ti.id_unico as tid,
                            ti.nombre as tnom,
                            t.numeroidentificacion tnum, 
                            t.ruta_logo as ruta 
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

$minrub = "";
$maxrub = "";
#Consulta Mínima Cuenta
$rb1 = "SELECT codi_presupuesto from gf_rubro_pptal WHERE codi_presupuesto = $rubini";
$minrub = $mysqli->query($rb1);
while ($filarub1 = mysqli_fetch_array($minrub)) 
{
 $rubMin = $filarub1['codi_presupuesto'];   
}
#Fin Consulta Mínima Cuenta
$rub1 = $rubMin;

#Inicio consulta Máxima Cuenta
$rb2 = "SELECT codi_presupuesto from gf_rubro_pptal WHERE codi_presupuesto = $rubfin";
$maxrub = $mysqli->query($rb2);
while ($filarub2 = mysqli_fetch_array($maxrub)) 
{
 $rubMax = $filarub2['codi_presupuesto'];   
}
#Fin Consulta Maxima Cuenta
$rub2 = $rubMax;

$nomcomp = "";
$tipodoc = "";
$numdoc = "";

while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = ($fila['traz']);       
        $tipodoc = ($fila['tnom']);       
        $numdoc = ($fila['tnum']);   
        $ruta = ($fila['ruta']);   
    }

#Variable Conteo de Páginas
$nb=$pdf->AliasNbPages();

#Creación del objeto
$pdf->AddPage();

#Definición Alias para número de Páginas
$pdf->AliasNbPages();

$pdf->SetX(25);
$pdf->Ln(-1);

//$pdf->setY(37);
$total1=0;
$compania = $_SESSION['compania'];
$sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = '.$compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];

$rubroP ="SELECT DISTINCT rp.codi_presupuesto AS codigoR, rp.nombre as nombreR "
        . "FROM gf_detalle_comprobante_pptal dcp "
        . "LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico "
        . "LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
        . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
        . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  "
        . "WHERE rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' "
        . "AND  cp.fecha BETWEEN '$fechaI' AND '$fechaF'  "
        . "AND (rp.tipoclase = 7 OR rp.tipoclase = 9 OR rp.tipoclase=10 OR rp.tipoclase=15 OR rp.tipoclase=16) "
        . "AND cp.parametrizacionanno = $panno "
        . "ORDER BY codi_presupuesto ASC ";
$rubroP =$mysqli->query($rubroP);
while ($rubro = mysqli_fetch_assoc($rubroP)){
         $numd=0;
         $cons = "SELECT rp.codi_presupuesto   as rpcodp,
                tcp.id_unico as tipocom 
                FROM
                  gf_detalle_comprobante_pptal dcp
                LEFT JOIN
                  gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
                LEFT JOIN
                  gf_rubro_pptal rp ON rf.rubro = rp.id_unico
                LEFT JOIN
                  gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
                LEFT JOIN 
                  gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
                LEFT JOIN 
                    gf_tercero t ON t.id_unico = dcp.tercero 
                WHERE 
                    rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' 
                    AND (rp.tipoclase = 7 OR rp.tipoclase = 9 OR rp.tipoclase=10 OR rp.tipoclase=15 OR rp.tipoclase=16) 
                    AND cp.parametrizacionanno = $panno 
                    AND  cp.fecha BETWEEN '$fechaI' AND '$fechaF' ORDER BY cp.fecha, tcp.codigo, cp.numero ASC";
         $cons =$mysqli->query($cons);
        while ($row1 = mysqli_fetch_array($cons)) {
            if($row1['rpcodp'] ==$rubro['codigoR']){
                $tipo= $row1['tipocom'];
                $comprobanteI1= "SELECT codigo as cod FROM gf_tipo_comprobante_pptal WHERE id_unico ='$tipo' "
                        . "AND (clasepptal ='13' OR clasepptal ='14' OR clasepptal ='15' OR clasepptal ='16' OR clasepptal ='17' OR clasepptal ='20')";
                $comprobanteI1 = $mysqli->query($comprobanteI1);
                if(mysqli_num_rows($comprobanteI1)>0){
                $row11 = mysqli_fetch_array($comprobanteI1);
                    if($row11['cod']>=$compini && $row11['cod']<=$compfin){
                        $numd=$numd+1;
                    }
                }
            }
        }
        if($numd>0){ 
    
            $pdf->SetX(25);
            $pdf->SetX(25);
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(50,5,utf8_decode('Rubro: '.$rubro['codigoR'].' - '.$rubro['nombreR']),0,0);
            $pdf->Ln(5);
            $pdf->SetFont('Arial','',8);
        }   
      $con = "SELECT
      rp.codi_presupuesto   as rpcodp,
      rp.nombre             as rpnom,
      dcp.rubrofuente       as dcprf,
      dcp.tercero           as dcpter, 
      tcp.clasepptal        as tcpcla, 
      cp.fecha              as cpfecha, 
      tcp.codigo            as tcpcod, 
      cp.numero             as cpnum, 
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
      dcp.descripcion       as dcpdesc,
      tcp.tipooperacion     as tcptop, 
      dcp.id_unico  as idDetalle, 
      dcp.valor as valor, 
      tcp.id_unico as tipocom 
    FROM
      gf_detalle_comprobante_pptal dcp
    LEFT JOIN
      gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
    LEFT JOIN
      gf_rubro_pptal rp ON rf.rubro = rp.id_unico
    LEFT JOIN
      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
    LEFT JOIN 
      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
    LEFT JOIN 
        gf_tercero tr ON tr.id_unico = dcp.tercero 
    WHERE 
        rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' 
        AND (rp.tipoclase = 7 OR rp.tipoclase = 9 OR rp.tipoclase=10 OR rp.tipoclase=15 OR rp.tipoclase=16) 
        AND cp.parametrizacionanno = $panno 
        AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' ORDER BY cp.fecha, tcp.codigo, cp.numero ASC";
    $con = $mysqli->query($con);
 
    while ($row = mysqli_fetch_array($con)) {

    if($row['rpcodp'] ==$rubro['codigoR']){
        $tipo= $row['tipocom'];
        $comprobanteI= "SELECT codigo as cod FROM gf_tipo_comprobante_pptal WHERE id_unico ='$tipo' "
                . "AND (clasepptal ='13' OR clasepptal ='14' OR clasepptal ='15' OR clasepptal ='16' OR clasepptal ='17' OR clasepptal ='20')";
        $comprobanteI = $mysqli->query($comprobanteI);
        if(mysqli_num_rows($comprobanteI)>0){
        $row1 = mysqli_fetch_array($comprobanteI);
        if($row1['cod']>=$compini && $row1['cod']<=$compfin){
        $valor = $row['valor'];    
        #AFECTADO
	$comp = $row['idDetalle'];    
        $a = "SELECT valor as value
                FROM
                  gf_detalle_comprobante_pptal dcp
                LEFT JOIN
                  gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                LEFT JOIN
                  gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                WHERE
                  dcp.comprobanteafectado = '$comp' AND top.id_unico = 1";
	$af = $mysqli->query($a);
        
        if(mysqli_num_rows($af)>0){
            $sum=0;
            while ($sum1= mysqli_fetch_array($af)) {
                $sum = $sum1['value']+$sum;
            }
        } else {
           $sum=0; 
        }
        $afectado = $sum;
        #MODIFICACIONES
        $mod= "SELECT valor as value, tcp.tipooperacion as idcom 
                FROM
                  gf_detalle_comprobante_pptal dcp
                LEFT JOIN
                  gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                LEFT JOIN
                  gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                WHERE
                  dcp.comprobanteafectado = '$comp' AND top.id_unico != 1";
        $modi = $mysqli->query($mod);
        if(mysqli_num_rows($modi)>0){
            $modifi=0;
            while ($modif= mysqli_fetch_array($modi)){
                $modificacion= $modif['value'];
                if($modif['idcom']==2){
                    $modifi = $modificacion+$modifi;
                } else {
                    if($modif['idcom']==3){
                        $modifi =$modifi+($modificacion*-1);
                    } else {
                        $modifi = 0; 
                    }
               }
            }
        } else {
            $modifi=0;
        }
        $modificacion1  = $modifi;
        if($modificacion1<0){
            $modificacion =$modificacion1*-1; 
        } else {
            $modificacion =$modificacion1;
        }
        
        #TOTAL
        $total = $valor+$modificacion1;
        #SALDO
        $saldo=$total-$afectado;
        
        //$p1 = (float) ;
        
        $pdf->SetX(25);
        
        $p1   = (float)($valor);
        $p2   = (float)($modificacion);
        $p3   = (float)($total);
        $p4   = (float)($afectado);
        $p5   = (float)($saldo);
        $hi = $pdf->GetY();
        if($hi>185)
        {
         $pdf->Ln(15);
         $pdf->AddPage();    
         $pdf->SetX(25);
        }
        #$fechaD = DateTime::createFromFormat('d/m/Y', "$row['cpfecha']");
        #$fechaD = $fechaD->format('Y/m/d');
         $dat = $row['cpfecha'];//date('Y-m-d');
         $dat = trim($dat, '"');
         $fecha_div = explode("-", $dat);
         $aniodat = $fecha_div[0];
         $mesdat = $fecha_div[1];
         $diadat = $fecha_div[2];
         $dat = $diadat.'/'.$mesdat.'/'.$aniodat;
        if(!empty($dat))
            $pdf->CellFitScale(16,4,$dat,0,0,'C');
        else
            $pdf->Cell(16,4,'',0,0,'C');
        if(!empty($row['tcpcod']))
            $pdf->CellFitScale(15,4,utf8_decode($row['tcpcod']),0,0,'C');
        else
            $pdf->Cell(15,4,'',0,0,'C');
        if(!empty($row['cpnum']))
            $pdf->CellFitScale(30,4,utf8_decode($row['cpnum']),0,0,'C');
        else
            $pdf->Cell(30,4,'',0,0,'C');
        if(!empty($row['NOMBRE']))
            $pdf->CellFitScale(70,4,utf8_decode(ucwords(mb_strtolower($row['NOMBRE']))),0,0,'L');
        else
            $pdf->Cell(70,4,'',0,0,'L');
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(60,4,utf8_decode(ucwords(mb_strtolower($row['dcpdesc']))),0,'L');
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 60;
        $pdf->Ln(-$h);
        $pdf->SetX($px);
        $pdf->CellFitScale(26,4,number_format($p1,2,'.',','),0,0,'R');
        $pdf->CellFitScale(26,4,number_format($p2,2,'.',','),0,0,'R');
        $pdf->CellFitScale(26,4,number_format($p3,2,'.',','),0,0,'R');
        $pdf->CellFitScale(26,4,number_format($p4,2,'.',','),0,0,'R');
        $pdf->CellFitScale(26,4,number_format($p5,2,'.',','),0,0,'R');
        $total1= $total1+$p5;
        $pdf->Ln($h);
       }
        }
        //echo 'valor ='.$valor.' MODIFICACION='.$modificacion.' TOTAL'.$total.' AFECTADO='.$afectado.' SALDO='.$saldo.'<BR/>';   
    }
  }
    
}
## TOTALES ##
$pdf->SetFont('Arial','B',8.5);
$pdf->Ln(5);
$pdf->SetX(36);


$pdf->Cell(244,4,'TOTALES: ',0,0,'R');
$pdf->Cell(66,4,number_format($total1,2,'.',','),0,0,'R');
## FIN TOTALES ##
#########  Fin Estructura Funcionamiento   ############

 $pdf->Ln(5);
 $pdf->SetX(25);
 $pdf->Cell(320,0.5,'',1,0,'R');
 
   ######### ESTRUCTURA FIRMAS #########
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','',8.5);
 $pdf->Ln(20);
 $pdf->SetX(25);
  ################################ ESTRUCTURA FIRMAS ##########################################
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','B',9);
 $pdf->Ln(10);
 $compania = $_SESSION['compania'];
 $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        WHERE LOWER(td.nombre) ='auxiliar presupuestal' AND td.compania = $compania  ORDER BY rd.orden ASC";
 $res= $mysqli->query($res);
 $i=0;
 $x=130;
 #ESTRUCTURA
 if(mysqli_num_rows($res)>0){
     $h=4;
     while ($row2 = mysqli_fetch_row($res)) {
         
         $ter = "SELECT IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) AS NOMBREC, "
                 . "tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional "
                 . "FROM gf_tercero tr "
                 . "LEFT JOIN gf_cargo_tercero ct ON tr.id_unico = ct.tercero "
                 . "LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico "
                 . "WHERE tr.id_unico ='$row2[0]'";
         
         $ter = $mysqli->query($ter);
         $ter = mysqli_fetch_row($ter);
         if(!empty($ter[3])){
                 $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n T.P:".(mb_strtoupper($ter[3]));
         } else {
             $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n";
         }
         
         $pdf->MultiCell(110,4, utf8_decode($responsable),0,'L');
        
         if($i==1){
           $pdf->Ln(15);
           $x=130;
           $i=0;
         } else {
         $pdf->Ln(-25);
         $pdf->SetX($x);
         $x=$x+110;
          $i=$i+1;
         }
        
     }
     
 } 
 ################################################################################################################################
while (ob_get_length()) {
  ob_end_clean();
}
 ######## FIN FIRMAS #########
$pdf->Output(0,'Informe_Auxiliares_Presupuestales_Gastos ('.date('d/m/Y').').pdf',0);
?>