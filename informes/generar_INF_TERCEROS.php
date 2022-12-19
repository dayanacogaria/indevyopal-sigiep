<?php
@session_start();
ini_set('max_execution_time', 360);
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];    
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
$con = new ConexionPDO();
#   ************   Datos Compañia   ************    #
$rowC = $con->Listar("SELECT 	ter.id_unico,
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
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6]; 

$row = $con->Listar("SELECT t.id_unico, ti.sigla,  IF(CONCAT_WS(' ',
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
             IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                t.numeroidentificacion, 
                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) AS NI, 
               CONCAT( c.nombre,' - '),  
              t.email,  tn.nombre, te.nombre , 
              GROUP_CONCAT(' ',d.direccion), GROUP_CONCAT(' ',tl.valor)
FROM gf_tercero t 
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
LEFT JOIN gf_tipo_entidad  tn ON t.tipoentidad = tn.id_unico 
LEFT JOIN gf_tipo_empresa te ON t.tipoempresa = te.id_unico 
LEFT JOIN gf_direccion d ON t.id_unico = d.tercero 
LEFT JOIN gf_ciudad c ON d.ciudad_direccion = c.id_unico 
LEFT JOIN gf_telefono tl ON t.id_unico = tl.tercero 
WHERE t.compania =$compania 
    GROUP BY t.id_unico ");
if($_REQUEST['t']==1){
require'../fpdf/fpdf.php';
class PDF extends FPDF
{ 
    function Header()
    { 
        global $razonsocial;
        global $nombreIdent;
        global $numeroIdent;
        global $direccinTer;
        global $telefonoTer;
        global $ruta_logo;
    
        $this->SetFont('Arial','B',10);
        if($ruta_logo != '')
        {
          $this->Image('../'.$ruta_logo,60,6,25);
        }
        $this->Cell(330,5,utf8_decode($razonsocial),0,0,'C');
        $this->Ln(5);
        $this->Cell(330,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
        $this->Ln(5);
        $this->Cell(330,5,utf8_decode('Dirección: '.$direccinTer),0,0,'C');
        $this->Ln(5);
        $this->Cell(330,5,utf8_decode('Teléfono: '.$telefonoTer),0,0,'C');
        $this->Ln(5);
        $this->Cell(330,5,utf8_decode('LISTADO TERCEROS'),0,0,'C');
        $this->Ln(8);

        $this->SetFont('Arial','B',7);
        $this->Cell(25,5, utf8_decode('TIPO DOCUMENTO'),1,0,'C');
        $this->Cell(25,5, utf8_decode('NÚMERO'),1,0,'C');
        $this->Cell(70,5, utf8_decode('RAZÓN SOCIAL / NOMBRE'),1,0,'C');
        $this->Cell(35,5, utf8_decode('CIUDAD'),1,0,'C');
        $this->Cell(40,5, utf8_decode('CORREO'),1,0,'C');
        $this->Cell(35,5,utf8_decode('TIPO EMPRESA'),1,0,'C');
        $this->Cell(35,5,utf8_decode('TIPO ENTIDAD'),1,0,'C');
        $this->Cell(35,5,utf8_decode('DIRECCIÓN'),1,0,'C');
        $this->Cell(35,5,utf8_decode('TELÉFONO'),1,0,'C');
        $this->Ln(5);

    
    }
    function Footer() {

    }
}
$pdf = new PDF('L','mm','Legal');        
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',7);

for ($i = 0; $i < count($row); $i++) {
    $y = $pdf->GetY();
    if($y>180){
        $pdf->AddPage();
    }
    $y = $pdf->GetY();
    $x = $pdf->GetX();
    $pdf->Cell(25,5,utf8_decode($row[$i][1]),0,0,'L');
    $pdf->Cell(25,5,utf8_decode($row[$i][3]),0,0,'L');
    $pdf->MultiCell(70,5,utf8_decode($row[$i][2]),0,'L');
    $yf1 = $pdf->GetY();
    $pdf->SetXY($x+120, $y);
    $pdf->MultiCell(35,5,utf8_decode($row[$i][4]),0,'L');
    $yf2 = $pdf->GetY();
    $pdf->SetXY($x+155, $y);
    $pdf->MultiCell(40,5,utf8_decode($row[$i][5]),0,'L');
    $yf3 = $pdf->GetY();
    $pdf->SetXY($x+195, $y);
    $pdf->MultiCell(35,5,utf8_decode($row[$i][6]),0,'L');
    $yf4 = $pdf->GetY();
    $pdf->SetXY($x+230, $y);
    $pdf->MultiCell(35,5,utf8_decode($row[$i][7]),0,'L');
    $yf5 = $pdf->GetY();
    $pdf->SetXY($x+265, $y);
    $pdf->MultiCell(35,5,utf8_decode($row[$i][8]),0,'L');
    $yf6 = $pdf->GetY();
    $pdf->SetXY($x+300, $y);
    $pdf->MultiCell(35,5,utf8_decode($row[$i][9]), 0,'L');
    $yf7 = $pdf->GetY();
    $h = max($yf1,$yf2,$yf3,$yf4,$yf5,$yf6,$yf7)-$y;
    $pdf->SetXY($x, $y);
    $pdf->Cell(25,$h,utf8_decode(''),1,0,'L');
    $pdf->Cell(25,$h,utf8_decode(''),1,0,'L');
    $pdf->Cell(70,$h,utf8_decode(''),1,0,'L');
    $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
    $pdf->Cell(40,$h,utf8_decode(''),1,0,'L');
    $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
    $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
    $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
    $pdf->Cell(35,$h,utf8_decode(''),1,0,'L');
    $pdf->Ln($h);
    
}


        
ob_end_clean();
$pdf->Output(0,'Informe_Terceros ('.date('d/m/Y').').pdf',0);
} else { 
 header("Content-type: application/vnd.ms-excel");
 header("Content-Disposition: attachment; filename=Listado_Terceros.xls");   
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe Terceros</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="9" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/>LISTADO TERCEROS
        <br/>&nbsp;</strong>
    </th>
    <tr>
        <td><center><strong>TIPO DOCUMENTO</strong></center></td>
        <td><center><strong>NÚMERO</strong></center></td>
        <td><center><strong>RAZON SOCIAL/NOMBRE</strong></center></td>
        <td><center><strong>CIUDAD</strong></center></td>
        <td><center><strong>CORREO</strong></center></td>
        <td><center><strong>TIPO EMPRESA</strong></center></td>
        <td><center><strong>TIPO ENTIDAD</strong></center></td>
        <td><center><strong>DIRECCIÓN</strong></center></td>
        <td><center><strong>TELÉFONO</strong></center></td>
    </tr>
    <?php 
     for ($i = 0; $i < count($row); $i++) {
         echo '<tr>';
         echo '<td>'.$row[$i][1].'</td>';
         echo '<td>'.$row[$i][3].'</td>';
         echo '<td>'.$row[$i][2].'</td>';
         echo '<td>'.$row[$i][4].'</td>';
         echo '<td>'.$row[$i][5].'</td>';
         echo '<td>'.$row[$i][6].'</td>';
         echo '<td>'.$row[$i][7].'</td>';
         echo '<td>'.$row[$i][8].'</td>';
         echo '<td>'.$row[$i][9].'</td>';
         echo '</tr>';
     }
    ?>
    
                
</table>
</body>
</html>
<?php } ?>



