<?php 
##################################################################################################################################################################
#                                                                                                                           Modificaciones
##################################################################################################################################################################
#24/08/2017 |Erica G. | Modificacion logo, firmas
##############MODIFICACIONES##########################
#16/05/2017 | ERICA G. | FILTRO TERCEROS
#03/03/2017 | ERICA G. | ARREGLO BUSQUEDA MODIFICACION Y AFECTADO
#######################################################
require'../../fpdf/fpdf.php';
require'../../Conexion/conexion.php';
session_start();
ini_set('max_execution_time', 0);
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');
ob_start();

$rubini         = $mysqli->real_escape_string(''.$_POST["sltrubi"].'');
$rubfin         = $mysqli->real_escape_string(''.$_POST["sltrubf"].'');
$fechaini       = $mysqli->real_escape_string(''.$_POST["fechaini"].''); 
$fechafin       = $mysqli->real_escape_string(''.$_POST["fechafin"].'');
$compini        = $mysqli->real_escape_string(''.$_POST["sltTci"].'');
$compfin        = $mysqli->real_escape_string(''.$_POST["sltTcf"].'');
$terInicial     = $mysqli->real_escape_string(''.$_POST["sltTi"].'');
$terFinal       = $mysqli->real_escape_string(''.$_POST["sltTf"].'');

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
    
    global $numpaginas;
    
    $numpaginas=$this->PageNo();
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
    $this->Cell(330,5,utf8_decode('AUXILIAR POR TERCEROS'),0,0,'C');
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
     $this->SetFont('Arial','',8);
    $this->SetX(25);
    #Empty Cells
    $this->Cell(25,9,utf8_decode(''),1,0,'C');
    $this->Cell(15,9,utf8_decode(''),1,0,'C');
    $this->Cell(25,9,utf8_decode(''),1,0,'C');
    $this->Cell(25,9,utf8_decode(''),1,0,'C');
    $this->Cell(75,9,utf8_decode(''),1,0,'C');
    $this->Cell(70,9,utf8_decode(''),1,0,'C');
    $this->Cell(40,9,utf8_decode(''),1,0,'C');
    $this->Cell(40,9,utf8_decode(''),1,0,'C');
     
    
    $this->SetX(25);
    #Cell Row 2
    $this->Cell(25,10,utf8_decode('Número'),0,0,'C');
    $this->Cell(15,10,utf8_decode('Tipo'),0,0,'C');
    $this->Cell(25,10,utf8_decode('Fecha'),0,0,'C');
    $this->Cell(25,10,utf8_decode('Rubro'),0,0,'C');
    $this->Cell(75,10,utf8_decode('Nombre'),0,0,'C');
    $this->Cell(70,10,utf8_decode('Tipo Afectado'),0,0,'C');
    $this->Cell(40,10,utf8_decode('Crédito'),0,0,'C');
    $this->Cell(40,10,utf8_decode('ContraCrédito'),0,0,'C');
    
    $this->Ln(10);
    $this->Cell(326,5,'',0);
    }      
    
function Footer()
    {
    global $usuario;
    global $fechaActual;
    $this->SetY(-15);
    $this->SetFont('Arial','B',8);
    $this->SetX(10);
    $this->Cell(90,10,utf8_decode('Fecha: '.$fechaActual),0,0,'L');
    $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
    $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
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
$yp=$pdf->GetY();
$pdf->SetX(25);
$pdf->Ln(-1);


//$pdf->setY(37);
if(!empty($terInicial) &&  empty($terFinal)) {
$rubroP ="SELECT DISTINCT "
        . "tr.numeroidentificacion as Numter, 
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
                tr.apellidodos)) AS NOMBRE, "
        . "tr.id_unico as idter FROM gf_detalle_comprobante_pptal dcp "
        . "LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico "
        . "LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
        . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
        . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  "
        . "LEFT JOIN gf_tercero tr ON tr.id_unico = dcp.tercero "
        . "WHERE rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' "
        . "AND  cp.fecha BETWEEN '$fechaI' AND '$fechaF' AND "
        . "tr.numeroidentificacion BETWEEN '$terInicial' AND '$terFinal' "
        . "AND tcp.codigo BETWEEN '$compini' AND '$compfin' "
        . "ORDER BY Numter ASC ";
} else {
    $rubroP ="SELECT DISTINCT "
        . "tr.numeroidentificacion as Numter, 
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
            tr.apellidodos)) AS NOMBRE, "
        . "tr.id_unico as idter FROM gf_detalle_comprobante_pptal dcp "
        . "LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico "
        . "LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
        . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
        . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  "
        . "LEFT JOIN gf_tercero tr ON tr.id_unico = dcp.tercero "
        . "WHERE rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' "
        . "AND  cp.fecha BETWEEN '$fechaI' AND '$fechaF' AND "
        . "tr.numeroidentificacion BETWEEN '$terInicial' AND '$terFinal' "
        . "AND tcp.codigo BETWEEN '$compini' AND '$compfin'"
        . "ORDER BY Numter ASC ";
}

$rubroP1 =$mysqli->query($rubroP);
while ($rubro = mysqli_fetch_assoc($rubroP1)){
    $ter = $rubro['idter'];
    $comp = "SELECT
      tcp.codigo            as tcpcod 
    FROM
      gf_detalle_comprobante_pptal dcp
    LEFT JOIN
      gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
    LEFT JOIN 
      gf_fuente ff ON rf.fuente = ff.id_unico
    LEFT JOIN
      gf_rubro_pptal rp ON rf.rubro = rp.id_unico
    LEFT JOIN
      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
    LEFT JOIN 
      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
    LEFT JOIN 
        gf_tercero t ON t.id_unico = dcp.tercero 
    WHERE t.id_unico = '$ter' AND 
        rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' AND  
        cp.fecha BETWEEN '$fechaI' AND '$fechaF' "
            . "AND tcp.codigo BETWEEN '$compini' AND '$compfin'";
    //$pdf->CellFitScale(100,5,utf8_decode($comp));
     $com = $mysqli->query($comp);
     $cnum=0;
     if(mysqli_num_rows($com)>0){
         while ($row1 = mysqli_fetch_array($com)) {
            $tipo = $row1['tcpcod'];
            $comprobanteI= "SELECT codigo as cod FROM gf_tipo_comprobante_pptal WHERE codigo ='$tipo' "
                . "AND (clasepptal ='13' OR clasepptal ='14' OR clasepptal ='15' OR clasepptal ='16' OR clasepptal ='17')";
            $comprobanteI = $mysqli->query($comprobanteI);
            if(mysqli_num_rows($comprobanteI)>0){ 
                $cnum=$cnum+1;
            }
          }
          if($cnum>0){
              $pdf->SetX(25);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(50,5,utf8_decode('Tercero: '.strtoupper($rubro['Numter']).' - '. ucwords(mb_strtolower($rubro['NOMBRE']))));
        $pdf->Ln(5);
        $pdf->SetFont('Arial','',8); 
          
    
    
      
    
    
      $con = "SELECT
      rp.codi_presupuesto   as rpcodp,
      rp.nombre             as rpnom,
      rf.fuente             as fuente,
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
      tcp.id_unico as tipocom, 
      tr.numeroidentificacion as numTer , 
      dcp.comprobanteafectado as comAfec , 
      tcp.codigo as codTipo 
    FROM
      gf_detalle_comprobante_pptal dcp
    LEFT JOIN
      gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
    LEFT JOIN 
      gf_fuente ff ON rf.fuente = ff.id_unico
    LEFT JOIN
      gf_rubro_pptal rp ON rf.rubro = rp.id_unico
    LEFT JOIN
      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
    LEFT JOIN 
      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
    LEFT JOIN 
        gf_tercero tr ON tr.id_unico = dcp.tercero 
    WHERE 
        rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' AND  
        cp.fecha BETWEEN '$fechaI' AND '$fechaF' "
              . "AND tcp.codigo BETWEEN '$compini' AND '$compfin' "
              . "ORDER BY cp.fecha asc";
     $total1=0;
      $total2=0;
    $con1 = $mysqli->query($con);
    while ($row = mysqli_fetch_array($con1)) {
        $tipo = $row['tcpcod'];
        $comprobanteI= "SELECT codigo as cod FROM gf_tipo_comprobante_pptal WHERE codigo ='$tipo' "
                . "AND (clasepptal ='13' OR clasepptal ='14' OR clasepptal ='15' OR clasepptal ='16' OR clasepptal ='17')";
        $comprobanteI = $mysqli->query($comprobanteI);
   if(mysqli_num_rows($comprobanteI)>0){

    if($row['numTer'] ==$rubro['Numter']){
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
        
        ##AFECTADO ##
        $commA = $row['comAfec'];
        $comm= "SELECT DISTINCT
            tcp.codigo as codigoA,
            cp.numero as numeroA,
            cp.descripcion as descripA 
          FROM
            gf_detalle_comprobante_pptal dcp
          LEFT JOIN
            gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
          LEFT JOIN
            gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
          WHERE
            tcp.tipooperacion = '1' AND dcp.id_unico = '$commA'";
        $comm = $mysqli->query($comm);
        if(mysqli_num_rows($comm)>0){
            $comAfec = mysqli_fetch_array($comm);
            $comprobanteAfectado = mb_strtoupper($comAfec['codigoA']).' - '.$comAfec['numeroA'].' - '.ucwords(mb_strtolower($comAfec['descripA']));
        } else {
            $comprobanteAfectado =' ';
        }
        $paginactual=$numpaginas;
        $pdf->SetX(25);
        
        $p1   = (float)($valor);
        $p2   = (float)($modificacion);
        $p3   = (float)($total);
        $p4   = (float)($afectado);
        $p5   = (float)($saldo);
        
         $dat = $row['cpfecha'];//date('Y-m-d');
         $dat = trim($dat, '"');
         $fecha_div = explode("-", $dat);
         $aniodat = $fecha_div[0];
         $mesdat = $fecha_div[1];
         $diadat = $fecha_div[2];
         $dat = $diadat.'/'.$mesdat.'/'.$aniodat;
        
         
        $pdf->CellFitScale(25,4,utf8_decode($row['cpnum']),0,0,'L');
        $pdf->CellFitScale(15,4, utf8_decode(mb_strtoupper($row['codTipo'])),0,0,'L');
        $pdf->CellFitScale(25,4,$dat,0,0,'L');
        $pdf->CellFitScale(25,4,utf8_decode($row['rpcodp']),0,0,'L');
        
        
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(75,4,utf8_decode(ucwords(mb_strtolower($row['rpnom']))),0,'L');
        $y2=$pdf->GetY();
        $h = $y2-$y;
        $px = $x+75;
        if($numpaginas>$paginactual){
           $pdf->SetXY($px,$yp);
           $h=$y2-$yp;
        } else {
            $pdf->SetXY($px,$y);
        }
        
        $y1 = $pdf->GetY();
        $x1 = $pdf->GetX();        
        $pdf->MultiCell(70,4,utf8_decode($comprobanteAfectado),0,'J');
        $y21 = $pdf->GetY();
        $h1 = $y21-$y1;
        $px1 = $x1 + 70;
        if($numpaginas>$paginactual){
           $pdf->SetXY($px1,$yp);
           $h1=$y21-$yp;
        } else {
            $pdf->SetXY($px1,$y1);
        }
         $alto = max($h,$h1);
        
        $pdf->CellFitScale(40,4,number_format($p1,2,'.',','),0,0,'R');
        $pdf->CellFitScale(40,4,number_format(0,2,'.',','),0,0,'R');
        $total1= $total1+$p1;
        $total2= $total2+0;
       
        $pdf->Ln($alto);
       }
     }
    }
    ## TOTALES ##
        $pdf->SetFont('Arial','B',8.5);
        $pdf->Ln(3);
        $pdf->SetX(36);
        $pdf->Cell(224,4,'TOTALES: ',0,0,'R');
        $pdf->Cell(40,4,number_format($total1,2,'.',','),0,0,'R');
        $pdf->Cell(40,4,number_format($total2,2,'.',','),0,0,'R');
    ## FIN TOTALES ##
    }
    }
   }
   


#########  Fin Estructura Funcionamiento   ############

 $pdf->Ln(5);
 $pdf->SetX(25);
 $pdf->Cell(320,0.5,'',1,0,'R');
 ob_end_clean();

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
 $pdf->Output(0,'Informe_Auxiliares_Por_Terceros ('.date('d/m/Y').').pdf',0);
?>