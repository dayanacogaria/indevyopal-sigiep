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

#***********************Datos Comprobante***********************#

#Consulta de comprobante cnt
$row =$con->Listar("SELECT 	
                    cp.id_unico,
                    cp.numero,
                    cp.fecha,
                    date_format(cp.fecha,'%d/%m/%Y'),
                    cp.descripcion,
                    UPPER(tc.codigo),
                    UPPER(tc.nombre)
                FROM 
                    gf_comprobante_pptal cp 
                LEFT JOIN 
                    gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                WHERE 
                    tc.clasepptal = 13 AND tc.tipooperacion = 1 AND cp.parametrizacionanno = $anno");

$idComp         =$row[0][0];
$numero         =$row[0][1];			
$fecha          =$row[0][3];		
$descripcion    =$row[0][4];			
$tipo           =$row[0][5].' - '.$row[0][6];			

#*********** Consulta Detalles ¨****************#

    $rowd   = $con->Listar("SELECT DISTINCT 
        dt.id_unico,       
        rb.codi_presupuesto,
        rb.nombre, 
        f.nombre, 
        dt.valor, 
        rb.tipoclase 
    FROM 
        gf_detalle_comprobante_pptal dt 
    LEFT JOIN 
        gf_rubro_fuente rf  ON dt.rubrofuente = rf.id_unico
    LEFT JOIN 
        gf_rubro_pptal rb   ON rf.rubro = rb.id_unico 
    LEFT JOIN 
        gf_fuente f         ON f.id_unico = rf.fuente 
    WHERE dt.comprobantepptal = $idComp");
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
            global $numero;
            global $tipo;
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
            $this->Cell(200,5,utf8_decode($tipo ).' Nro: '.$numero,0,0,'C');
            $this->Ln(5);
            $this->Ln(5);
            $this->SetFont('Arial','B',8);
            $this->Cell(25,7, utf8_decode(''),1,0,'C');
            $this->Cell(75,7,utf8_decode(''),1,0,'C');
            $this->Cell(50,7,utf8_decode(''),1,0,'C');
            $this->Cell(25,7,utf8_decode(''),1,0,'C');
            $this->Cell(25,7,utf8_decode(''),1,0,'C');
            $this->Setx(10);
            $this->Cell(25,7, utf8_decode('Código Rubro'),0,0,'C');
            $this->Cell(75,7,utf8_decode('Nombre Rubro'),0,0,'C');
            $this->Cell(50,7,utf8_decode('Fuente'),0,0,'C');
            $this->Cell(25,7,utf8_decode('Crédito'),0,0,'C');
            $this->Cell(25,7,utf8_decode('Contracrédito'),0,0,'C');
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
    $totalc  = 0;
    $totalcc = 0;
    for ($i = 0; $i < count($rowd); $i++) {
        $vcredito  = 0;
        $vccredito = 0;
        $alt = $pdf->GetY();
        if($alt>240){
            $pdf->AddPage();
        }
        $pdf->Setx(10);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(25,4,utf8_decode(''),0,0,'L');
        $pdf->MultiCell(75,4,utf8_decode($rowd[$i][2]),0,'J');
        $y1 = $pdf->GetY();
        $h1 = $y1 - $y;
        $pdf->SetXY($x+100, $y);
        $pdf->MultiCell(50,4,utf8_decode($rowd[$i][3]),0,'J');
        $y2 = $pdf->GetY();
        $h2 = $y2 - $y;
        $h = max($h1, $h2);
        $pdf->SetXY($x, $y);
        $pdf->Cell(25,$h,utf8_decode($rowd[$i][1]),1,0,'L');
        $pdf->Cell(75,$h,utf8_decode(''),1,0,'L');
        $pdf->Cell(50,$h,utf8_decode(''),1,0,'L');
        IF($rowd[$i][5]==6){
            $vccredito = $rowd[$i][4];
        } else {
            $vcredito  = $rowd[$i][4];
        }
        $pdf->Cell(25,$h, number_format($vcredito, 2, '.', ','),1,0,'R');
        $pdf->Cell(25,$h, number_format($vccredito, 2, '.', ','),1,0,'R');
        $pdf->Ln($h);
        $totalc  += $vcredito;
        $totalcc += $vccredito;
        
    }
    $pdf->SetFont('Arial','B',8);   
    $pdf->Cell(200,0.5,utf8_decode(''),1,0,'C'); 
    $pdf->Ln(2);
    $pdf->Cell(150,4,utf8_decode('TOTALES'),0,0,'R');
    $pdf->Cell(25,4,number_format($totalc,2,'.',','),0,0,'R');
    $pdf->Cell(25,4,number_format($totalcc,2,'.',','),0,0,'R');
		
    ob_end_clean();		
    $pdf->Output(0,'Informe_Apropiaciones_Iniciales.pdf',0);




} 

#***************EXCEL****************#
elseif($_GET['t']==2){ 
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Apropiacion_Inicial.xls");
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>APROPIACIONES INICIALES</title>
        </head>
        <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <th colspan="5" align="center"><strong>
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                    <br/>&nbsp;
                    <br/><?php echo $tipo.' Nro: '.$numero?>
                    <br/>&nbsp;</strong>
                </th>
                <tr>
                    <td><center><strong>CÓDIGO RUBRO</strong></center></td>
                    <td><center><strong>NOMBRE RUBRO</strong></center></td>
                    <td><center><strong>FUENTE</strong></center></td>
                    <td><center><strong>CRÉDITO</strong></center></td>
                    <td><center><strong>CONTRACRÉDITO</strong></center></td>

                </tr>
                <tbody>
                    <?php 
                        $totalc  = 0;
                        $totalcc = 0;
                        for ($i = 0; $i < count($rowd); $i++) {
                            $vcredito  = 0;
                            $vccredito = 0;
                            echo '<tr>';
                            echo '<td>'.$rowd[$i][1].'</td>';
                            echo '<td>'.$rowd[$i][2].'</td>';
                            echo '<td>'.$rowd[$i][3].'</td>';
                            IF($rowd[$i][5]==6){
                                $vccredito = $rowd[$i][4];
                            } else {
                                $vcredito  = $rowd[$i][4];
                            }
                            echo '<td>'.number_format($vcredito,2, '.',',').'</td>';
                            echo '<td>'.number_format($vccredito,2, '.',',').'</td>';
                            echo '</tr>';
                            $totalc  += $vcredito;
                            $totalcc += $vccredito;
                        }
                        echo '<tr>';
                        echo '<td colspan="3"><strong>TOTALES</strong></td>';
                        echo '<td><strong>'.number_format($totalc,2,'.',',').'</strong></td>';
                        echo '<td><strong>'.number_format($totalcc,2,'.',',').'</strong></td>';
                        echo '</tr>';
                    ?>
                    
                </tbody>
            </table>
        </body>
    </html>
<?php      
}
?>