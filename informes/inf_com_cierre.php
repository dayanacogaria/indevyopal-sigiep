<?php 
require'../Conexion/ConexionPDO.php';
ini_set('max_execution_time', 0);
session_start();
$con = new ConexionPDO();

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
$comprobante=$_GET['id'];
#Consulta de comprobante cnt
$row =$con->Listar("SELECT 	
                    cnt.id_unico,
                    cnt.numero,
                    cnt.fecha,
                    date_format(cnt.fecha,'%d/%m/%Y'),
                    cnt.descripcion,
                    UPPER(tpcnt.sigla),
                    UPPER(tpcnt.nombre),
                    cnt.tercero,
                    IF(CONCAT_WS(' ',
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
                        t.numeroidentificacion, CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))                     
                FROM 
                    gf_comprobante_cnt cnt 
                LEFT JOIN 
                    gf_tipo_comprobante tpcnt ON cnt.tipocomprobante = tpcnt.id_unico 
                LEFT JOIN 
                    gf_tercero t ON cnt.tercero = t.id_unico 
                WHERE 
                    md5(cnt.id_unico)='$comprobante'");

$idComp         =$row[0][0];
$numero         =$row[0][1];			
$fecha          =$row[0][3];		
$descripcion    =$row[0][4];			
$tipo           =$row[0][5].' - '.$row[0][6];		
$tercero        = ucwords(mb_strtolower($row[0][8])).' - '.$row[0][9];		

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
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(35,7, utf8_decode(''),1,0,'C');
    $pdf->Cell(85,7,utf8_decode(''),1,0,'C');
    $pdf->Cell(40,7,utf8_decode(''),1,0,'C');
    $pdf->Cell(40,7,utf8_decode(''),1,0,'C');
    $pdf->Setx(10);
    $pdf->Cell(35,7, utf8_decode('Código'),0,0,'C');
    $pdf->Cell(85,7,utf8_decode('Nombre'),0,0,'C');
    $pdf->Cell(40,7,utf8_decode('Débito'),0,0,'C');
    $pdf->Cell(40,7,utf8_decode('Crédito'),0,0,'C');
    $pdf->Ln(7);
  
    $rowd   = $con->Listar("SELECT DISTINCT 
                dtc.id_unico,       
                cnt.codi_cuenta, 
                cnt.nombre, 
                cnt.naturaleza, 
                dtc.valor 
            FROM gf_detalle_comprobante dtc             
            LEFT JOIN gf_cuenta cnt ON dtc.cuenta = cnt.id_unico 
            WHERE dtc.comprobante = $idComp");
    $tdebito    = 0;
    $tcredito   = 0;
    for ($i = 0; $i < count($rowd); $i++) {
        #*******valores***********#
        $debito  =0;
        $credito =0;
        switch (($rowd[$i][3])){
            case 1:
                if($rowd[$i][4]>0){
                   $debito  = $rowd[$i][4];
                } else {
                   $credito = ($rowd[$i][4])*-1; 
                }
            break;
            case 2:
                if($rowd[$i][4]>0){
                   $credito = $rowd[$i][4]; 
                } else {
                   $debito  = ($rowd[$i][4])*-1; 
                }
            break;                                    
        }
        $tdebito    += $debito;
        $tcredito   += $credito;
        
        $a = $pdf->GetY();
        $paginactual = $numpaginas;
        if($a>220)
        {
            $pdf->AddPage();            
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(35,7, utf8_decode(''),1,0,'C');
            $pdf->Cell(85,7,utf8_decode(''),1,0,'C');
            $pdf->Cell(40,7,utf8_decode(''),1,0,'C');
            $pdf->Cell(40,7,utf8_decode(''),1,0,'C');
            $pdf->Setx(10);
            $pdf->Cell(35,7, utf8_decode('Código'),0,0,'C');
            $pdf->Cell(85,7,utf8_decode('Nombre'),0,0,'C');
            $pdf->Cell(40,7,utf8_decode('Débito'),0,0,'C');
            $pdf->Cell(40,7,utf8_decode('Crédito'),0,0,'C');
            $pdf->Ln(7);            
        }
        $pdf->SetFont('Arial','',8);
        
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(35, 7, utf8_decode(), 0, 0, 'C');
        $pdf->MultiCell(85, 7, utf8_decode(ucwords(mb_strtolower($rowd[$i][2]))), 0, 'J');
        $y2 = $pdf->GetY();
        $h = $y2 - $y;
        $pdf->SetXY($x,$y);
        $pdf->Cell(35,$h, utf8_decode($rowd[$i][1]),1,0,'L');
        $pdf->Cell(85,$h,utf8_decode(''),1,0,'C');
        $pdf->Cell(40,$h, number_format($debito,2, '.',','),1,0,'R');
        $pdf->Cell(40,$h, number_format($credito,2, '.',','),1,0,'R');
        $pdf->Ln($h); 
        
    }
    $pdf->SetFont('Arial','B',10);   
    $pdf->Cell(200,0.5,utf8_decode(''),1,0,'C'); 
    $pdf->Ln(2);
    $pdf->Cell(120,4,utf8_decode('TOTALES'),0,0,'R');
    $pdf->Cell(40,4,number_format($tdebito,2,'.',','),0,0,'R');
    $pdf->Cell(40,4,number_format($tcredito,2,'.',','),0,0,'R');
		
    ob_end_clean();		
    $pdf->Output(0,'Informe_Comprobante_Cierre.pdf',0);




} 

#***************EXCEL****************#
elseif($_GET['t']==2){ 
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Cierre_Contable.xls");
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>CIERRE CONTABLE</title>
        </head>
        <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <th colspan="4" align="center"><strong>
                    <br/><?php echo $razonsocial ?>
                    <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                    <br/>&nbsp;
                    <br/><?php echo $tipo.' Nro: '.$numero?>
                    <br/>&nbsp;</strong>
                </th>
                <tr>
                    <td><center><strong>CÓDIGO</strong></center></td>
                    <td><center><strong>NOMBRE</strong></center></td>
                    <td><center><strong>DÉBITO</strong></center></td>
                    <td><center><strong>CRÉDITO</strong></center></td>

                </tr>
                <tbody>
                    <?php 
                        $rowd   = $con->Listar("SELECT DISTINCT 
                                    dtc.id_unico,       
                                    cnt.codi_cuenta, 
                                    cnt.nombre, 
                                    cnt.naturaleza, 
                                    dtc.valor 
                                FROM gf_detalle_comprobante dtc             
                                LEFT JOIN gf_cuenta cnt ON dtc.cuenta = cnt.id_unico 
                                WHERE dtc.comprobante = $idComp");
                        $tdebito    = 0;
                        $tcredito   = 0;
                        for ($i = 0; $i < count($rowd); $i++) {
                            #*******valores***********#
                            $debito  =0;
                            $credito =0;
                            switch (($rowd[$i][3])){
                                case 1:
                                    if($rowd[$i][4]>0){
                                       $debito  = $rowd[$i][4];
                                    } else {
                                       $credito = ($rowd[$i][4])*-1; 
                                    }
                                break;
                                case 2:
                                    if($rowd[$i][4]>0){
                                       $credito = $rowd[$i][4]; 
                                    } else {
                                       $debito  = ($rowd[$i][4])*-1; 
                                    }
                                break;                                    
                            }
                            $tdebito    += $debito;
                            $tcredito   += $credito;
                            echo '<tr>';
                            echo '<td>'.$rowd[$i][1].'</td>';
                            echo '<td>'.ucwords(mb_strtolower($rowd[$i][2])).'</td>';
                            echo '<td>'.number_format($debito,2, '.',',').'</td>';
                            echo '<td>'.number_format($credito,2, '.',',').'</td>';
                            echo '</tr>';

                        }
                        echo '<tr>';
                        echo '<td colspan="2"><strong>TOTALES</strong></td>';
                        echo '<td><strong>'.number_format($tdebito,2,'.',',').'</strong></td>';
                        echo '<td><strong>'.number_format($tcredito,2,'.',',').'</strong></td>';
                        echo '</tr>';
                    ?>
                    
                </tbody>
            </table>
        </body>
    </html>
<?php      
}
?>