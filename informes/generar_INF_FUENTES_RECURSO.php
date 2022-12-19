<?php 
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
require'../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
session_start();
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
#***********************Datos Compañia***********************#
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 
            ter.id_unico,
            ter.razonsocial,
            UPPER(ti.nombre),
            IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
            dir.direccion,
            tel.valor,
            ter.ruta_logo 
        FROM            
            gf_tercero ter
        LEFT JOIN 	
            gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
        LEFT JOIN       
            gf_direccion dir ON dir.tercero = ter.id_unico
        LEFT JOIN 	
            gf_telefono  tel ON tel.tercero = ter.id_unico
        WHERE 
            ter.id_unico = $compania");

$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];

#***********************Datos Fuente***********************#

$row =$con->Listar("SELECT DISTINCT 
                    f.id_unico, 
                    f.nombre, 
                    tf.nombre, 
                    rf.nombre 
                FROM 
                    gf_fuente f 
                LEFT JOIN 
                    gf_tipo_fuente tf         ON tf.id_unico = f.tipofuente 
                LEFT JOIN 
                    gf_recurso_financiero rf  ON rf.id_unico = f.recursofinanciero 
                WHERE 
                    f.parametrizacionanno = $anno");

if($_GET['t']==1){
    
    header("Content-Type: text/html;charset=utf-8");
    require'../fpdf/fpdf.php';
    ob_start();
    #********Encabezado pie De Página*********#
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
            global $numpaginas;
            $numpaginas=$numpaginas+1;
            
            $this->SetFont('Arial','B',10);
            
            $this->SetY(10);
            if($ruta_logo != '')
	    {
	      $this->Image('../'.$ruta_logo,10,8,20);
	    }
            $this->SetFont('Arial','B',10);	
            $this->SetXY(40,15);
            $this->MultiCell(140,5,utf8_decode($razonsocial),0,'C');		
            $this->SetX(10);
            $this->Ln(1);
            $this->Cell(200,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode('Dirección: '.$direccinTer),0,0,'C');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode('Tel: '.$telefonoTer),0,0,'C');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode('FUENTES DE RECURSO'),0,0,'C');
            $this->Ln(5);
            $this->Ln(5);
            $this->SetFont('Arial','B',10);
            $this->Cell(70,7, utf8_decode(''),1,0,'C');
            $this->Cell(70,7,utf8_decode(''),1,0,'C');
            $this->Cell(60,7,utf8_decode(''),1,0,'C');
            $this->Setx(10);
            $this->Cell(70,7, utf8_decode('Nombre'),0,0,'C');
            $this->Cell(70,7,utf8_decode('Tipo Fuente'),0,0,'C');
            $this->Cell(60,7,utf8_decode('Recurso Financiero'),0,0,'C');
            $this->Ln(7);
        }      
    
        function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->SetX(10);
                $this->Cell(190,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
        }

    $pdf = new PDF('P','mm','Letter');   
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial','',8);   
    $total=0;
    for ($i = 0; $i < count($row); $i++) {
        $alt = $pdf->GetY();
        if($alt>240){
            $pdf->AddPage();
        }
        $pdf->Setx(10);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell(70,4,utf8_decode($row[$i][1]),0,'J');
        $y1 = $pdf->GetY();
        $h1 = $y1 - $y;
        $pdf->SetXY($x+70, $y);
        $pdf->MultiCell(70,4,utf8_decode($row[$i][2]),0,'J');
        $y2 = $pdf->GetY();
        $h2 = $y2 - $y;
        $pdf->SetXY($x+140, $y);
        $pdf->MultiCell(60,4,utf8_decode($row[$i][3]),0,'J');
        $y3 = $pdf->GetY();
        $h3 = $y3 - $y;
        $h = max($h1, $h2, $h3);
        $pdf->SetXY($x, $y);
        $pdf->Cell(70,$h,utf8_decode(''),1,0,'L');
        $pdf->Cell(70,$h,utf8_decode(''),1,0,'L');
        $pdf->Cell(60,$h,utf8_decode(''),1,0,'L');
        $pdf->Ln($h);
        $total +=$rowd[$i][4];
        
    }
    
		
    ob_end_clean();		
    $pdf->Output(0,'Informe_Fuentes.pdf',0);




} 

#***************EXCEL****************#
elseif($_GET['t']==2){ 
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Fuentes.xls");
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Fuentes</title>
        </head>
        <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <th colspan="3" align="center"><strong>
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                    <br/>&nbsp;
                    <br/>FUENTES DE RECURSO
                    <br/>&nbsp;</strong>
                </th>
                <tr>
                    <td><center><strong>NOMBRE</strong></center></td>
                    <td><center><strong>TIPO FUENTE </strong></center></td>
                    <td><center><strong>RECURSO FINANCIERO</strong></center></td>

                </tr>
                <tbody>
                    <?php 
                        for ($i = 0; $i < count($row); $i++) {
                            echo '<tr>';
                            echo '<td>'.$row[$i][1].'</td>';
                            echo '<td>'.$row[$i][2].'</td>';
                            echo '<td>'.$row[$i][3].'</td>';
                            echo '</tr>';
                        }
                    ?>
                    
                </tbody>
            </table>
        </body>
    </html>
<?php      
}
?>