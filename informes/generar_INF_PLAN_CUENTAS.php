<?php

session_start();
require'../Conexion/conexion.php';
ini_set('max_execution_time', 360);    
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];
$anno = $_SESSION['anno'];
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
        $nomcomp = $fila['traz'];       
        $tipodoc = $fila['tnom'];       
        $numdoc = $fila['tnum'];   
    }

$hoy = date('d-m-Y');
$hoy = trim($hoy, '"');
$fecha_div = explode("-", $hoy);
$anioh = $fecha_div[2];
$mesh = $fecha_div[1];
$diah = $fecha_div[0];
$hoy = $diah.'/'.$mesh.'/'.$anioh;

if($_GET['id']==1) {
require'../fpdf/fpdf.php';
class PDF extends FPDF
{
// Cabecera de página  
function Header()
{ 
    
    global $nomcomp;
    global $tipodoc;
    global $numdoc;
    // Logo
    //$this->Image('logo_pb.png',10,8,33);
    //Arial bold 10
    $this->SetFont('Arial','B',10);
    
        // Título
    $this->Cell(330,10,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(345,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(4);

    $this->SetFont('Arial','',10);
    $this->Cell(330,10,utf8_decode($tipodoc.': '.$numdoc),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(345,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(4);

    $this->SetFont('Arial','',8);
    $this->Cell(330,10,utf8_decode('LISTADO PLAN DE CUENTAS'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(345,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(8);
    
    $this->SetFont('Arial','B',7);
    
    $this->SetX(10);
    
    $this->Cell(25,9,'',1);
    $this->Cell(120,9,'',1);
    $this->Cell(15,9,'',1);
    $this->Cell(50,9,'',1);
    $this->Cell(16,9,'',1);
    $this->Cell(14,9,'',1);
    $this->Cell(14,9,'',1);
    $this->Cell(14,9,'',1);
    $this->Cell(11,9,'',1);
    $this->Cell(25,9,'',1);
    $this->Cell(25,9,'',1);
    
    $this->SetX(10);
    
    $this->Cell(25,9, utf8_decode('Código'),0,0,'C');
    $this->Cell(120,9, utf8_decode('Nombre'),0,0,'C');
    $this->Cell(15,9, utf8_decode('Naturaleza'),0,'C');
    $this->Cell(50,9, utf8_decode('Clase'),0,0,'C');
    $this->Cell(16,9, utf8_decode('Movimiento'),0,0,'C');
    $this->Cell(14,6, utf8_decode('Centro'),0,0,'C');
    $this->Cell(14,6, utf8_decode('Auxiliar'),0,0,'C');
    $this->Cell(14,6, utf8_decode('Auxiliar'),0,0,'C');
    $this->Cell(11,9, utf8_decode('Activa'),0,0,'C');
    $this->Cell(25,6, utf8_decode('Cuenta'),0,0,'C');
    $this->Cell(25,9, utf8_decode('Predecesor'),0,0,'C');
    $this->Ln(4);
    
    $this->SetX(10);
    
    $this->Cell(25,4,'',0);
    $this->Cell(120,4,'',0);
    $this->Cell(15,4,'',0);
    $this->Cell(50,4,'',0);
    $this->Cell(16,4,'',0);
    $this->Cell(14,4,utf8_decode('Costo'),0,0,'C');
    $this->Cell(14,4,utf8_decode('Tercero'),0,0,'C');
    $this->Cell(14,4,utf8_decode('Proyecto'),0,0,'C');
    $this->Cell(11,4,'',0,0,'C');
    $this->Cell(25,4,utf8_decode('CGN'),0,0,'C');
    $this->Cell(25,4,'',0,0,'C');
    $this->Ln(5);
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

$nb=$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->AliasNbPages();

$pdf->SetFont('Arial','',7);

//Consulta SQL
$sql = "SELECT 
                    RP.id_unico as id,
                    RP.codi_cuenta as codc,
                    RP.nombre as nom,       
                    RP.movimiento as mov,
                    RP.centrocosto as cenc,
                    RP.auxiliartercero as auxt,
                    RP.auxiliarproyecto as auxp,
                    RP.activa as acti,
                    RP.dinamica as din,
                    (SELECT H.codi_cuenta FROM gf_cuenta H WHERE RP.predecesor = H.id_unico) as hj,       
                    RP.naturaleza as nat,
                    NT.id_unico as nid,
                    NT.nombre as nnom,
                    RP.tipocuentacgn as cgn,
                    TPC.id_unico as tid,
                    TPC.nombre as tnom,
                    RP.clasecuenta as clc,
                    CC.id_unico as cid,
                    CC.nombre as cnom
        FROM gf_cuenta RP  
  LEFT JOIN gf_naturaleza NT        ON RP.naturaleza = NT.id_unico
  LEFT JOIN gf_tipo_cuenta_cgn TPC  ON RP.tipocuentacgn = TPC.id_unico
  LEFT JOIN gf_clase_cuenta CC      ON RP.clasecuenta = CC.id_unico 
  WHERE RP.parametrizacionanno = $anno 
  ORDER BY RP.codi_cuenta ASC";
$cp = $mysqli->query($sql);
$codp = 0;
$mov = "";
$cen = "";
$ater = "";
$aproy = "";


while ($fila = mysqli_fetch_array($cp)) 
        { 
         $codp = $codp + 1;
         ######### Celda 1
         if($fila['codc']!="")
             $pdf->cellfitscale(25,5,utf8_decode($fila['codc']),0);
         else
             $pdf->Cell(25,4,"",0);
         ######## Celda 2
         // Posiciones X & Y
         $y1 = $pdf->GetY();
         $x1 = $pdf->GetX();        
         // Trazado de celda multilinea.
         $pdf->MultiCell(120,4,utf8_decode(ucwords(($fila['nom']))),0);
         // Alto Final y Alto Inicial.
         $y2 = $pdf->GetY();            
         $alto_de_fila = $y2-$y1;    
         // Posición en X más ancho de multicelda.
         $posicionX = $x1 + 120;            
         // Posicionamiento 
         $pdf->SetXY($posicionX,$y1);
         ###### Celda 3
         if($fila['nnom']!="")
            $pdf->cellfitscale(15,4,utf8_decode($fila['nnom']),0,0,'C');
         else
            $pdf->Cell(15,4,'',0,0,'C');
         ##### Celda 4
         // Trazado de celda multilinea.
         $pdf->MultiCell(50,4,utf8_decode(ucwords(($fila['cnom']))),0);
         // Alto Final y Alto Inicial.
         $y3 = $pdf->GetY();            
         $alto2 = $y3-$y1;    
         // Posición en X más ancho de multicelda.
         $posicionX2 = $posicionX + 65;            
         // Posicionamiento 
         $pdf->SetXY($posicionX2,$y1);         
         
         switch($fila['mov'])
         {
          case 1:
          {
              $mov="SI";
              break;
          }
          case 2:
          {
              $mov="NO";
              break;
          }
         }    
         $pdf->Cell(16,4,utf8_decode($mov),0,0,'C');
         
         switch($fila['cenc'])
         {
          case 1:
          {
              $cos="SI";
              break;
          }
          case 2:
          {
              $cos="NO";
              break;
          }
         }    
         $pdf->Cell(14,4,utf8_decode($cos),0,0,'C');
         
         switch($fila['auxt'])
         {
          case 1:
          {
              $ater="SI";
              break;
          }
          case 2:
          {
              $ater="NO";
              break;
          }
         }    
         $pdf->Cell(14,4,utf8_decode($ater),0,0,'C');
         
         switch($fila['auxp'])
         {
          case 1:
          {
              $aproy="SI";
              break;
          }
          case 2:
          {
              $aproy="NO";
              break;
          }
         }    
         $pdf->Cell(14,4,utf8_decode($aproy),0,0,'C');
         
         switch($fila['acti'])
         {
          case 1:
          {
              $act="SI";
              break;
          }
          case 2:
          {
              $act="NO";
              break;
          }
         }    
         $pdf->Cell(11,4,utf8_decode($act),0,0,'C');
         
         if($fila['tnom']!=null)
            $pdf->cellfitscale(25,4,utf8_decode($fila['tnom']),0,0,'L');
         else
            $pdf->Cell(25,4,"",0,0,'L');
    
         if($fila['hj']!="")
            $pdf->cellfitscale(25,4,utf8_decode($fila['hj']),0,0,'L');
         else
            $pdf->Cell(25,4,"",0,0,'L');
         $alt = max($alto_de_fila,$alto2);    
         $pdf->Ln($alt);
        }
        
        $pdf->Cell(335,0.5,'',1);


ob_end_clean();
$pdf->Output(0,'Informe_Plan_Cuentas ('.date('d/m/Y').').pdf',0);

} else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Plan_Cuentas.xls");
    #************Datos Compañia************#
    $compania = $_SESSION['compania'];
    $sqlC = "SELECT 	ter.id_unico,
                    ter.razonsocial,
                    UPPER(ti.nombre),
                    ter.numeroidentificacion,
                    dir.direccion,
                    tel.valor,
                    ter.ruta_logo
    FROM gf_tercero ter
    LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
    LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
    LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
    WHERE ter.id_unico = $compania";
    $resultC = $mysqli->query($sqlC);
    $rowC = mysqli_fetch_row($resultC);
    $razonsocial = $rowC[1];
    $nombreIdent = $rowC[2];
    $numeroIdent = $rowC[3];
    $direccinTer = $rowC[4];
    $telefonoTer = $rowC[5];
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Listado Plan Cuentas</title>
        </head>
        <body>
            <?php $sql = "SELECT 
                    RP.id_unico as id,
                    RP.codi_cuenta as codc,
                    RP.nombre as nom,       
                    RP.movimiento as mov,
                    RP.centrocosto as cenc,
                    RP.auxiliartercero as auxt,
                    RP.auxiliarproyecto as auxp,
                    RP.activa as acti,
                    RP.dinamica as din,
                    (SELECT H.codi_cuenta FROM gf_cuenta H WHERE RP.predecesor = H.id_unico) as hj,       
                    RP.naturaleza as nat,
                    NT.id_unico as nid,
                    NT.nombre as nnom,
                    RP.tipocuentacgn as cgn,
                    TPC.id_unico as tid,
                    TPC.nombre as tnom,
                    RP.clasecuenta as clc,
                    CC.id_unico as cid,
                    CC.nombre as cnom
                FROM gf_cuenta RP  
                LEFT JOIN gf_naturaleza NT        ON RP.naturaleza = NT.id_unico
                LEFT JOIN gf_tipo_cuenta_cgn TPC  ON RP.tipocuentacgn = TPC.id_unico
                LEFT JOIN gf_clase_cuenta CC      ON RP.clasecuenta = CC.id_unico 
                WHERE RP.parametrizacionanno = $anno 
                ORDER BY RP.codi_cuenta ASC ";
                $cp = $mysqli->query($sql); ?>
                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                    <th colspan="12" align="center"><strong>
                        <br/>&nbsp;
                        <br/><?php echo $razonsocial ?>
                        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                        <br/>&nbsp;
                        <br/> PLAN DE CUENTAS
                        
                        <br/>&nbsp;</strong>
                    </th>
                    <tr>
                        <td><strong>Código Cuenta</strong></td>
                        <td><strong>Nombre</strong></td>
                        <td><strong>Movimiento</strong></td>
                        <td><strong>Centro Costo</strong></td>
                        <td><strong>Auxiliar Tercero</strong></td>
                        <td><strong>Auxiliar Proyecto</strong></td>
                        <td><strong>Activa</strong></td>
                        <td><strong>Dinámica</strong></td>
                        <td><strong>Naturaleza</strong></td>
                        <td><strong>Predecesor</strong></td>
                        <td><strong>Tipo Cuenta CGN</strong></td>
                        <td><strong>Clase Cuenta</strong></td>
                    </tr>
                    
                <?PHP 
                while ($fila = mysqli_fetch_array($cp)) { 
                    echo '<tr>';
                    echo '<td>'.$fila['codc'].'</td>';
                    echo '<td>'.ucwords(mb_strtolower($fila['nom'])).'</td>';
                    if($fila['mov']==1){ $mov = 'Si';}else {$mov = 'No';};
                    echo '<td>'.$mov.'</td>';
                    if($fila['cenc']==1){ $cos = 'Si';}else {$cos = 'No';};
                    echo '<td>'.$cos.'</td>';
                    if($fila['auxt']==1){ $auxt = 'Si';}else {$auxt = 'No';};
                    echo '<td>'.$auxt.'</td>';
                    if($fila['auxp']==1){ $auxp = 'Si';}else {$auxp = 'No';};
                    echo '<td>'.$auxp.'</td>';
                    if($fila['acti']==1){ $acti = 'Si';}else {$acti = 'No';};
                    echo '<td>'.$acti.'</td>';
                    echo '<td>'.$fila['din'].'</td>';
                    
                    echo '<td>'.ucwords(mb_strtolower($fila['nnom'])).'</td>';
                    echo '<td>'.$fila['hj'].'</td>';
                    echo '<td>'.ucwords(mb_strtolower($fila['tnom'])).'</td>';
                    echo '<td>'.ucwords(mb_strtolower($fila['cnom'])).'</td>';
                    echo '</tr>';
                }
                ?>    
                </table>
        </body>
    </html>
    
<?php         
}
?>

