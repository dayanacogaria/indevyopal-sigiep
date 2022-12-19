<?php
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
ini_set('max_execution_time', 0); 
session_start();
$con  = new ConexionPDO();
$anno = $_SESSION['anno'];
$tipo = $_REQUEST['t'];

#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
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
$empleado   = $_REQUEST['empleado'];
$periodoI   = $_REQUEST['periodoI'];
$periodoF   = $_REQUEST['periodoF'];
$conceptoI  = $_REQUEST['conceptoI'];
$conceptoF  = $_REQUEST['conceptoF'];
$t     = ''; 
IF(!empty($_REQUEST['tipoC'])){
    $t .=' AND c.clase = '.$_REQUEST['tipoC'];
}

$rowe = $con->Listar("SELECT CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos), t.numeroidentificacion 
    FROM gn_empleado e 
    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
    WHERE e.id_unico = $empleado");
$emple = $rowe[0][0].' - '.$rowe[0][1];
$rowp  = $con->Listar("SELECT DISTINCT p.id_unico, tp.nombre, p.codigointerno, 
    DATE_FORMAT(p.fechainicio,'%d/%m/%Y'), DATE_FORMAT(p.fechafin,'%d/%m/%Y'), 
    COUNT(DISTINCT n.concepto), 
    GROUP_CONCAT(DISTINCT n.concepto)
    FROM gn_novedad n 
    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
    LEFT JOIN gn_tipo_proceso_nomina tp ON p.tipoprocesonomina = tp.id_unico
    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
    WHERE n.concepto BETWEEN '$conceptoI' AND '$conceptoF' 
    and p.id_unico BETWEEN '$periodoI' AND '$periodoF'
    AND n.empleado = $empleado
    AND p.parametrizacionanno = $anno 
        $t 
    GROUP BY p.id_unico");
if($tipo ==1){
    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF
    {
        function Header(){ 
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $direccinTer;
            global $telefonoTer;
            global $ruta_logo;
            global $emple;
            $numpaginas=$numpaginas+1;

            $this->SetFont('Arial','B',10);

            if($ruta_logo != '')
            {
              $this->Image('../'.$ruta_logo,10,5,28);
            }
            $this->SetFont('Arial','B',10);	
            $this->MultiCell(200,5,utf8_decode($razonsocial),0,'C');		
            $this->SetX(10);
            $this->Ln(1);
            $this->Cell(200,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
            $this->ln(5);
            $this->SetX(10);
            $this->Cell(200,5,utf8_decode('Dirección: '.$direccinTer.' Tel: '.$telefonoTer),0,0,'C');
            $this->ln(5);
            $this->SetX(10);
            $this->Cell(200,5,utf8_decode('AUXILIAR DE NÓMINA'),0,0,'C');
            $this->Ln(5);
            $this->Cell(200,5,utf8_decode('EMPLEADO: '.$emple),0,0,'C');
            $this->Ln(10);
        }      

        function Footer(){
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(260,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }
    $pdf = new PDF('P','mm','Letter');   
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial','B',10);
    
    $pdf->Cell(50,5,utf8_decode('PERIODO'),1,0,'C');
    $pdf->Cell(100,5,utf8_decode('CONCEPTO'),1,0,'C');
    $pdf->Cell(45,5,utf8_decode('VALOR'),1,0,'C');
    $pdf->Ln(5);
    $tt = 0;
    for ($i = 0; $i < count($rowp); $i++) {
        $pdf->SetFont('Arial','',10);
        $id_periodo = $rowp[$i][0];
        $rowsp      = $rowp[$i][5]+1;
        $cod_con    = array();
        $cod_con    = explode(",", $rowp[$i][6]);
        $x          = $pdf->GetX();
        $y          = $pdf->GetY();
        $pdf->MultiCell(50,5,utf8_decode($rowp[$i][1].' '.$rowp[$i][2]),0,'L');
        $h1 = $pdf->GetY()-$y;
        $pdf->SetXY($x+50, $y);
        $tp = 0;
        for ($c = 0; $c < count($cod_con); $c++) {
            $id_concepto = $cod_con[$c];
            $rowv = $con->Listar("SELECT DISTINCT CONCAT_WS(' ',c.codigo, c.descripcion), SUM(n.valor)
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.concepto =$id_concepto 
            and n.periodo= $id_periodo 
            AND n.empleado = $empleado  
            GROUP BY c.id_unico");
            $pdf->SetX($x+50);
            $pdf->CellFitScale(100,5,utf8_decode($rowv[0][0]),0,0,'L');
            $pdf->Cell(45,5,number_format($rowv[0][1],2, ',', '.' ),0,0,'R');
            $pdf->Ln(5);
            $tp += $rowv[0][1];
            if($pdf->GetY()>250){
                $h2  = $pdf->GetY()-$y;
                $alt = max($h1, $h2);
                $pdf->SetXY($x, $y);
                $pdf->Cell(50,$alt,'',1,0,'L');
                $pdf->Cell(100,$alt,'',1,0,'L');
                $pdf->Cell(45,$alt,'',1,0,'L');
                $pdf->Ln($alt);
                $pdf->AddPage();
                $x = $pdf->GetX();
                $y = $pdf->GetY();
            }
        }     
        $h2  = $pdf->GetY()-$y;
        $alt = max($h1, $h2);
        $pdf->SetXY($x, $y);
        $pdf->Cell(50,$alt,'',1,0,'L');
        $pdf->Cell(100,$alt,'',1,0,'L');
        $pdf->Cell(45,$alt,'',1,0,'L');
        $pdf->Ln($alt);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(150,5,utf8_decode('TOTAL '.$rowp[$i][1].' '.$rowp[$i][2]),1,0,'L');
        $pdf->Cell(45,5,number_format($tp,2, ',', '.' ),1,0,'R');
        $pdf->Ln(5);
        $tt += $tp ;
        if($pdf->GetY()>250){
            $pdf->AddPage();
        }
    }
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(150,5,utf8_decode('TOTAL '),1,0,'L');
    $pdf->Cell(45,5,number_format($tt,2, ',', '.' ),1,0,'R');
    $pdf->Ln(5);
    ob_end_clean();		
    $pdf->Output(0,'Informe_Auxiliar_Nomina.pdf',0);
} else { 
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Auxiliar_Nomina.xls");    
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Auxiliar Nómina</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="6" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial.'<br/>'.$nombreIdent.' : '.$numeroIdent.
            '<br/>&nbsp<br/>'.$direccinTer." Nª:".$telefonoTer.
            '<br/>&nbsp<br/>AUXILIAR NÓMINA'.
            '<br/>&nbsp<br/>EMPLEADO: '.$rowe[0][0].' - '.$rowe[0][1]?>
            <br/>&nbsp;                 
            </strong> 
        </th>
        <?php 
        $html = '';
        $html .='<tr>';
        $html .='<td><strong>PERIODO</strong></td>';              
        $html .='<td><strong>CONCEPTO</strong></td>';
        $html .='<td><strong>VALOR</strong></td>';
        $html .='</tr>';
        $tt = 0;
        for ($i = 0; $i < count($rowp); $i++) {
            $id_periodo = $rowp[$i][0];
            $rowsp      = $rowp[$i][5]+1;
            $cod_con    = array();
            $cod_con    = explode(",", $rowp[$i][6]);
            $html .='<tr>';
            $html .='<td rowspan="'.$rowp[$i][5].'">'.$rowp[$i][1].' '.$rowp[$i][2].'<br/>&nbsp<br/>'.$rowp[$i][3].' - '.$rowp[$i][4].'</td>';              
            $tp = 0;
            for ($c = 0; $c < count($cod_con); $c++) {
                $id_concepto = $cod_con[$c];
                $rowv = $con->Listar("SELECT DISTINCT CONCAT_WS(' ',c.codigo, c.descripcion), SUM(n.valor)
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.concepto =$id_concepto 
                and n.periodo= $id_periodo 
                AND n.empleado = $empleado  
                GROUP BY c.id_unico");
                $html .='<td>'.$rowv[0][0].'</td>';
                $html .='<td >'.number_format($rowv[0][1],2, ',', '.' ).'</td>';
                $html .='</tr>';
                $tp += $rowv[0][1];
            }            
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td colspan = "2"><strong><i>TOTAL '.$rowp[$i][1].' '.$rowp[$i][2].'</i></strong></td>';
            $html .='<td ><strong><i>TOTAL '.number_format($tp,2, ',', '.' ).'</i></strong></td>';
            $html .='</tr>';
            $tt += $tp ;
        }
       
        $html .='<td colspan = "2"><strong>  </i></strong></td>';
        $html .='<td ><strong><i>TOTAL '.number_format($tt,2, ',', '.' ).'</i></strong></td>';

        echo $html;
        ?>
    </table>
    </body>
    </html>
        
<?php }?>