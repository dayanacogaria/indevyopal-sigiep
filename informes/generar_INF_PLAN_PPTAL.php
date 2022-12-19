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

#***********************Datos Rubro***********************#
$cuentas = $con->Listar("SELECT rb.id_unico, 
                            rb.codi_presupuesto,
                            rb.nombre, 
                            rb.movimiento, 
                            rb.manpac, 
                            rb.vigencia, 
                            vg.anno, 
                            tc.nombre, 
                            pr.codi_presupuesto, 
                            pr.nombre, 
                            ds.nombre,
                            tv.nombre, 
                            sc.nombre 
                        FROM 
                            gf_rubro_pptal rb 
                        LEFT JOIN 
                            gf_parametrizacion_anno vg  ON vg.id_unico = rb.vigencia 
                        LEFT JOIN 
                            gf_tipo_clase_pptal tc      ON tc.id_unico = rb.tipoclase 
                        LEFT JOIN 
                            gf_rubro_pptal pr           ON pr.id_unico = rb.predecesor 
                        LEFT JOIN 
                            gf_tipo_vigencia tv         ON tv.id_unico = rb.tipovigencia 
                        LEFT JOIN 
                            gf_sector sc                ON sc.id_unico = rb.sector 
                        LEFT JOIN 
                            gf_destino ds               ON ds.id_unico = rb.destino 
                        WHERE 
                            rb.parametrizacionanno = $anno 
                        ORDER BY 
                            rb.codi_presupuesto ASC");
 
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
            global $nanno;
            $numpaginas=$numpaginas+1;
            
            $this->SetFont('Arial','B',10);
            
            $this->SetY(10);
            if($ruta_logo != '')
	    {
	      $this->Image('../'.$ruta_logo,20,10,25);
	    }
            $this->SetFont('Arial','B',10);	
            $this->SetXY(40,15);
            $this->MultiCell(220,5,utf8_decode($razonsocial),0,'C');		
            $this->SetX(10);
            $this->Ln(1);
            $this->Cell(280,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
            $this->ln(5);
            $this->Cell(280,5,utf8_decode('Dirección: '.$direccinTer),0,0,'C');
            $this->ln(5);
            $this->Cell(280,5,utf8_decode('Tel: '.$telefonoTer),0,0,'C');
            $this->ln(5);
            $this->Cell(280,5,utf8_decode('PLAN PRESUPUESTAL '.$nanno),0,0,'C');
            $this->ln(5);
            $this->Ln(5);
            $this->SetFont('Arial','B',8);
            $this->Cell(20,7,utf8_decode(''),1,0,'C');
            $this->Cell(60,7,utf8_decode(''),1,0,'C');
            $this->Cell(10,7,utf8_decode(''),1,0,'C');
            $this->Cell(10,7,utf8_decode(''),1,0,'C');
            $this->Cell(20,7,utf8_decode(''),1,0,'C');
            $this->Cell(20,7,utf8_decode(''),1,0,'C');
            $this->Cell(60,7,utf8_decode(''),1,0,'C');
            $this->Cell(20,7,utf8_decode(''),1,0,'C');
            $this->Cell(20,7,utf8_decode(''),1,0,'C');
            $this->Cell(20,7,utf8_decode(''),1,0,'C');
            
            
            $this->Setx(10);
            $this->Cell(20,7,utf8_decode('Código'),0,0,'C');
            $this->Cell(60,7,utf8_decode('Nombre'),0,0,'C');
            $this->Cell(10,7,utf8_decode('MOV'),0,0,'C');
            $this->Cell(10,7,utf8_decode('PAC'),0,0,'C');
            $this->Cell(20,7,utf8_decode('Vigencia'),0,0,'C');
            $this->Cell(20,7,utf8_decode('Tipo Clase'),0,0,'C');
            $this->Cell(60,7,utf8_decode('Predecesor'),0,0,'C');
            $this->Cell(20,7,utf8_decode('Destino'),0,0,'C');
            $this->Cell(20,7,utf8_decode('Tipo Vigencia'),0,0,'C');
            $this->Cell(20,7,utf8_decode('Sector'),0,0,'C');
            $this->Ln(7);
            
            
        }      
    
        function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->SetX(10);
                $this->Cell(250,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
        }

    $pdf = new PDF('L','mm','Letter');   
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial','',7);
    
    for ($i = 0; $i < count($cuentas); $i++) {
        $alt = $pdf->GetY();
        if($alt>180){
            $pdf->AddPage();
        }
        $pdf->Setx(10);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->CellFitScale(20,4,utf8_decode(''),0,0,'L');
        $pdf->MultiCell(60,4,utf8_decode($cuentas[$i][2]),0,'L');
        $y1 = $pdf->GetY();
        $h1 = $y1 - $y;
        $pdf->SetXY($x+140, $y);
        $pdf->MultiCell(60,4,utf8_decode($cuentas[$i][8].' - '.$cuentas[$i][9]),0,'L');
        $y2 = $pdf->GetY();
        $h2 = $y2 - $y;
        $pdf->SetXY($x+200, $y);
        $pdf->MultiCell(20,4,utf8_decode($cuentas[$i][10]),0,'L');
        $y3 = $pdf->GetY();
        $h3 = $y3 - $y;
        $pdf->SetXY($x+220, $y);
        $pdf->MultiCell(20,4,utf8_decode($cuentas[$i][11]),0,'L');
        $y4 = $pdf->GetY();
        $h4 = $y4 - $y;
        $pdf->SetXY($x+240, $y);
        $pdf->MultiCell(20,4,utf8_decode($cuentas[$i][12]),0,'L');
        $y5 = $pdf->GetY();
        $h5 = $y5 - $y;
        
        $h = max($h1, $h2, $h3, $h4, $h5);
        $pdf->SetXY($x, $y);
        $pdf->CellFitScale(20,$h,utf8_decode($cuentas[$i][1]),1,0,'L');
        $pdf->Cell(60,$h,utf8_decode(),1,0,'L');
        if($cuentas[$i][3]==1){
            $pdf->CellFitScale(10,$h,utf8_decode('Sí'),1,0,'C');
        } else {
            $pdf->CellFitScale(10,$h,utf8_decode('No'),1,0,'C');
        }
        if($cuentas[$i][4]==1){
            $pdf->CellFitScale(10,$h,utf8_decode('Sí'),1,0,'C');
        } else {
            $pdf->CellFitScale(10,$h,utf8_decode('No'),1,0,'C');
        }
        $pdf->Cell(20,$h,utf8_decode($cuentas[$i][6]),1,0,'C');
        $pdf->Cell(20,$h,utf8_decode($cuentas[$i][7]),1,0,'L');
        $pdf->Cell(60,$h,utf8_decode(''),1,0,'L');
        $pdf->Cell(20,$h,utf8_decode(''),1,0,'L');
        $pdf->Cell(20,$h,utf8_decode(''),1,0,'L');
        $pdf->Cell(20,$h,utf8_decode(''),1,0,'L');
        $pdf->Ln($h);
    }
  	
    ob_end_clean();		
    $pdf->Output(0,'Informe_Plan_Presupuestal.pdf',0);




} 

#***************EXCEL****************#
elseif($_GET['t']==2){ 
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Plan_Presupuestal.xls");
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>PLAN PRESUPUESTAL</title>
        </head>
        <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <th colspan="10" align="center"><strong>
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                    <br/>&nbsp;
                    <br/>PLAN PRESUPUESTAL <?php echo $nanno?>
                    </strong>
                </th>
                <tr>
                    <td><center><strong>CÓDIGO</strong></center></td>
                    <td><center><strong>NOMBRE</strong></center></td>
                    <td><center><strong>MOV</strong></center></td>
                    <td><center><strong>PAC</strong></center></td>
                    <td><center><strong>VIGENCIA</strong></center></td>
                    <td><center><strong>TIPO CLASE</strong></center></td>
                    <td><center><strong>PREDECESOR</strong></center></td>
                    <td><center><strong>DESTINO</strong></center></td>
                    <td><center><strong>TIPO VIGENCIA</strong></center></td>
                    <td><center><strong>SECTOR</strong></center></td>
                    

                </tr>
                <tbody>
                    <?php 
                        for ($i = 0; $i < count($cuentas); $i++) {
                            echo '<tr>';
                            echo '<td>'.$cuentas[$i][1].'</td>';
                            echo '<td>'.$cuentas[$i][2].'</td>';
                            if($cuentas[$i][3]==1){
                                echo '<td>Sí</td>';
                            } else {
                                echo '<td>No</td>';
                            }
                            if($cuentas[$i][4]==1){
                                echo '<td>Sí</td>';
                            } else {
                                echo '<td>No</td>';
                            }
                            echo '<td>'.$cuentas[$i][6].'</td>';
                            echo '<td>'.$cuentas[$i][7].'</td>';
                            echo '<td>'.$cuentas[$i][8].' - '.$cuentas[$i][9].'</td>';
                            echo '<td>'.$cuentas[$i][10].'</td>';
                            echo '<td>'.$cuentas[$i][11].'</td>';
                            echo '<td>'.$cuentas[$i][12].'</td>';
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