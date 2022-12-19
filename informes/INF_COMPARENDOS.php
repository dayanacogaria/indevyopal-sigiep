<?php
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
require'../jsonPptal/funcionesPptal.php';
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

#***********************    Recibir Variables    ***********************#

$tipo       = $_REQUEST['tipo'];
$sucursal   = $_REQUEST['sucursal'];
$fechaI     = $_REQUEST['fechaini'];
$fechaF     = $_REQUEST['fechafin'];

#   **  Buscar Nombre Sucursal  **  #
$sc         = $con->Listar("SELECT UPPER(nombre) FROM gf_sucursal WHERE id_unico = $sucursal");
$nsucursal  = $sc[0][0];
#   **  Convertir Fechas    **  #
$fechaIn    = fechaC($fechaI);
$fechaFi    = fechaC($fechaF);
#   **  Nombre Tipo         **  #
$nomb_tipo  = "";
if($tipo==1){
    $nomb_tipo  = "COMPARENDOS RECAUDADOS";
    $t2         = "RECAUDO";
    
    $row        = $con->Listar("SELECT 
                    c.comparendo, 
                DATE_FORMAT(c.fecha_comparendo, '%d/%m/%Y'), 
                c.cedula, 
                CONCAT_WS(' ', c.nombres, c.apellidos), 
                c.placa, 
                c.infraccion, 
                DATE_FORMAT(r.fecha_recaudo, '%d/%m/%Y'), 
                r.valor_recaudo, 
                r.valor_tercero 
            FROM 
                    gu_comparendo c 
            LEFT JOIN 
                    gu_recaudos r ON c.comparendo = r.comparendo 
            WHERE 
                    r.comparendo IS NOT NULL 
                    AND r.fecha_recaudo BETWEEN '$fechaIn' AND '$fechaFi' 
                    AND c.sucursal = $sucursal  AND r.sucursal = $sucursal 
            ORDER BY 
                    c.comparendo,r.fecha_recaudo,c.fecha_comparendo  ASC ");
} elseif($tipo==2){
    $nomb_tipo  = "COMPARENDOS SIN RECAUDAR";
    $t2         = "COMPARENDO";
    $row        = $con->Listar("SELECT 
                c.comparendo, 
                DATE_FORMAT(c.fecha_comparendo, '%d/%m/%Y'), 
                c.cedula, 
                CONCAT_WS(' ', c.nombres, c.apellidos), 
                c.placa, 
                c.infraccion, 
                date_format(c.fecha_comparendo, '%Y') 
            FROM 
                    gu_comparendo c 
            LEFT JOIN 
                    gu_recaudos r ON c.comparendo = r.comparendo 
            WHERE 
                    r.comparendo IS NULL 
                    AND c.fecha_comparendo BETWEEN '$fechaIn' AND '$fechaFi' 
                    AND c.sucursal = $sucursal 
            ORDER BY 
                    c.fecha_comparendo, c.comparendo ASC ");
}elseif($tipo==3){
    $nomb_tipo  = "COMPARENDOS REPETIDOS";
    $t2         = "COMPARENDO";
    $row        = $con->Listar("SELECT comparendo
                FROM 
                    gu_comparendo 
                WHERE 
                    fecha_comparendo BETWEEN '$fechaIn' AND '$fechaFi' 
                    AND sucursal = $sucursal 
                    GROUP BY comparendo 
                    HAVING COUNT(*) > 1 
                    ORDER BY comparendo ASC"); 
}
#***********************Archivo PDF ***********************#
if($_GET['ex']=='pdf'){
    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF
    {
        function Header()
        { 
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $nsucursal;
            global $ruta_logo;
            global $fechaI;
            global $fechaF;
            global $nomb_tipo;
            global $tipo;
            
            if($ruta_logo != '')
            {
              $this->Image('../'.$ruta_logo,60,6,25);
            }
            $this->SetFont('Arial','B',12);
            $this->Cell(330,5,utf8_decode($razonsocial),0,0,'C');
            $this->Ln(5);
            $this->Cell(330, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); 
            $this->Ln(7);
            $this->SetFont('Arial','B',10);
            $this->Cell(330,5,utf8_decode('INFORME '.$nomb_tipo),0,0,'C');
            $this->Ln(5);
            $this->Cell(330,5,utf8_decode('SUCURSAL '.$nsucursal),0,0,'C');
            $this->Ln(5);
            $this->SetFont('Arial','B',9);
            $this->Cell(330,5,utf8_decode('Entre Fechas ' .$fechaI.' A '.$fechaF),0,0,'C');
            $this->Ln(8);
            $this->SetFont('Arial','B',8);   
            IF($tipo==1){
                $this->Cell(40,10,utf8_decode(''),1,0,'C');
                $this->Cell(25,10, utf8_decode(''),1,0,'C');
                $this->Cell(30,10,utf8_decode(''),1,0,'C');
                $this->Cell(100,10,utf8_decode(''),1,0,'C');
                $this->Cell(30,10,utf8_decode(''),1,0,'C');
                $this->Cell(25,10,utf8_decode(''),1,0,'C');
                $this->Cell(25,10,utf8_decode(''),1,0,'C');
                $this->Cell(30,10,utf8_decode(''),1,0,'C');
                $this->Cell(30,10,utf8_decode(''),1,0,'C');
                $this->Setx(10);
                
                $this->Cell(40,5,utf8_decode('NÚMERO'),0,0,'C');
                $this->Cell(25,5, utf8_decode('FECHA'),0,0,'C');
                $this->Cell(30,5,utf8_decode('NÚMERO'),0,0,'C');
                $this->Cell(100,5,utf8_decode('NOMBRES Y '),0,0,'C');
                $this->Cell(30,5,utf8_decode('NÚMERO'),0,0,'C');
                $this->Cell(25,10,utf8_decode('INFRACCIÓN'),0,0,'C');
                $this->Cell(25,5, utf8_decode('FECHA'),0,0,'C');
                $this->Cell(30,5, utf8_decode('VALOR'),0,0,'C');
                $this->Cell(30,5, utf8_decode('VALOR'),0,0,'C');
                $this->Ln(5);
                
                $this->Cell(40,5,utf8_decode('COMPARENDO'),0,0,'C');
                $this->Cell(25,5, utf8_decode('COMPARENDO'),0,0,'C');
                $this->Cell(30,5,utf8_decode('IDENTIFICACIÓN'),0,0,'C');
                $this->Cell(100,5,utf8_decode('APELLIDOS'),0,0,'C');
                $this->Cell(30,5,utf8_decode('DE PLACA'),0,0,'C');
                $this->Cell(25,5,utf8_decode(''),0,0,'C');
                $this->Cell(25,5,utf8_decode('RECAUDO'),0,0,'C');
                $this->Cell(30,5,utf8_decode('RECAUDO'),0,0,'C');
                $this->Cell(30,5,utf8_decode('TERCERO'),0,0,'C');
                $this->Ln(5);
            } elseif($tipo==2){ 
                $this->SetX(25);
                $this->Cell(30,10, utf8_decode(''),1,0,'C');
                $this->Cell(45,10,utf8_decode(''),1,0,'C');
                $this->Cell(35,10,utf8_decode(''),1,0,'C');
                $this->Cell(90,10,utf8_decode(''),1,0,'C');
                $this->Cell(35,10,utf8_decode(''),1,0,'C');
                $this->Cell(35,10,utf8_decode(''),1,0,'C');
                $this->Cell(40,10,utf8_decode(''),1,0,'C');
                $this->Setx(25);
                $this->Cell(30,5, utf8_decode('FECHA'),0,0,'C');
                $this->Cell(45,5,utf8_decode('NÚMERO'),0,0,'C');
                $this->Cell(35,5,utf8_decode('NÚMERO'),0,0,'C');
                $this->Cell(90,5,utf8_decode('NOMBRES Y '),0,0,'C');
                $this->Cell(35,5,utf8_decode('NÚMERO'),0,0,'C');
                $this->Cell(35,10,utf8_decode('INFRACCIÓN'),0,0,'C');
                $this->Cell(40,5,utf8_decode('VALOR'),0,0,'C');
                $this->Ln(5);
                $this->SetX(25);
                $this->Cell(30,5, utf8_decode('COMPARENDO'),0,0,'C');
                $this->Cell(45,5,utf8_decode('COMPARENDO'),0,0,'C');
                $this->Cell(35,5,utf8_decode('IDENTIFICACIÓN'),0,0,'C');
                $this->Cell(90,5,utf8_decode('APELLIDOS'),0,0,'C');
                $this->Cell(35,5,utf8_decode('DE PLACA'),0,0,'C');
                $this->Cell(35,5,utf8_decode(''),0,0,'C');
                $this->Cell(40,5,utf8_decode('COMPARENDO'),0,0,'C');
                $this->Ln(5);
            } else {
                $this->SetX(25);
                $this->Cell(40,10, utf8_decode(''),1,0,'C');
                $this->Cell(50,10,utf8_decode(''),1,0,'C');
                $this->Cell(40,10,utf8_decode(''),1,0,'C');
                $this->Cell(100,10,utf8_decode(''),1,0,'C');
                $this->Cell(40,10,utf8_decode(''),1,0,'C');
                $this->Cell(40,10,utf8_decode(''),1,0,'C');
                $this->Setx(25);
                $this->Cell(40,5, utf8_decode('FECHA'),0,0,'C');
                $this->Cell(50,5,utf8_decode('NÚMERO'),0,0,'C');
                $this->Cell(40,5,utf8_decode('NÚMERO'),0,0,'C');
                $this->Cell(100,5,utf8_decode('NOMBRES Y '),0,0,'C');
                $this->Cell(40,5,utf8_decode('NÚMERO'),0,0,'C');
                $this->Cell(40,10,utf8_decode('INFRACCIÓN'),0,0,'C');
                $this->Ln(5);
                $this->SetX(25);
                $this->Cell(40,5, utf8_decode('COMPARENDO'),0,0,'C');
                $this->Cell(50,5,utf8_decode('COMPARENDO'),0,0,'C');
                $this->Cell(40,5,utf8_decode('IDENTIFICACIÓN'),0,0,'C');
                $this->Cell(100,5,utf8_decode('APELLIDOS'),0,0,'C');
                $this->Cell(40,5,utf8_decode('DE PLACA'),0,0,'C');
                $this->Cell(40,5,utf8_decode(''),0,0,'C');
                $this->Ln(5);
            }
        }      

        function Footer(){
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->Cell(15);
            $this->Cell(25,10,utf8_decode('Fecha: '.date('d-m-Y')),0,0,'L');
            $this->Cell(270);
            $this->Cell(0,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0);
        }
    }

    $pdf = new PDF('L','mm','Legal');
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    
    if($tipo==1){
        $total_r =0;
        $total_t =0;
        $nc      =0;
        for ($i = 0; $i < count($row); $i++) {
            $pdf->SetFont('Arial','',9);
            $pdf->CellFitScale(40,5,utf8_decode($row[$i][0]),1,0,'L');
            $pdf->CellFitScale(25,5, utf8_decode($row[$i][1]),1,0,'L');
            $pdf->CellFitScale(30,5,utf8_decode($row[$i][2]),1,0,'L');
            $pdf->CellFitScale(100,5,utf8_decode(ucwords(mb_strtolower($row[$i][3]))),1,0,'L');
            $pdf->CellFitScale(30,5,utf8_decode($row[$i][4]),1,0,'L');
            $pdf->CellFitScale(25,5,utf8_decode($row[$i][5]),1,0,'L');
            $pdf->CellFitScale(25,5,utf8_decode($row[$i][6]),1,0,'L');
            $pdf->CellFitScale(30,5,number_format($row[$i][7],2,',','.'),1,0,'R');
            $pdf->CellFitScale(30,5,number_format($row[$i][8],2,',','.'),1,0,'R');
            $pdf->Ln(5);
            $total_r += $row[$i][7];
            $total_t += $row[$i][8];
            $nc      +=1;
        }
        $pdf->SetFont('Arial','B',10);
        $pdf->CellFitScale(275,5,utf8_decode($nc.' COMPARENDOS '),1,0,'C');
        $pdf->CellFitScale(30,5,number_format($total_r,2,',','.'),1,0,'R');
        $pdf->CellFitScale(30,5,number_format($total_t,2,',','.'),1,0,'R');
        
    } elseif($tipo==2){
        $nc      =0;
        $total_sr = 0;
        for ($i = 0; $i < count($row); $i++) {
            $pdf->SetFont('Arial','',9);
            $pdf->SetX(25);
            $pdf->CellFitScale(30,5, utf8_decode($row[$i][1]),1,0,'L');
            $pdf->CellFitScale(45,5,utf8_decode($row[$i][0]),1,0,'L');
            $pdf->CellFitScale(35,5,utf8_decode($row[$i][2]),1,0,'L');
            $pdf->CellFitScale(90,5,utf8_decode(ucwords(mb_strtolower($row[$i][3]))),1,0,'L');
            $pdf->CellFitScale(35,5,utf8_decode($row[$i][4]),1,0,'L');
            $pdf->CellFitScale(35,5,utf8_decode($row[$i][5]),1,0,'L');
            #******** Calcular Valor a Recaudar *************#
            $valor_c = 0;
            $anno_i     = $row[$i][6];
            $infraccion = $row[$i][5];
            $rowv = $con->Listar("SELECT valor_sancion FROM gu_tipo_comparendo WHERE codigo = '$infraccion' AND anno = '$anno_i'");
            if(count($rowv)>0){
                $valor_c = $rowv[0][0];
            }
            
            $pdf->CellFitScale(40,5,number_format($valor_c,2,',','.'),1,0,'R');
            $total_sr +=$valor_c;
            #*************************************************#
            $pdf->Ln(5);
            $nc      +=1;
        }
        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(25);
        $pdf->CellFitScale(270,5,utf8_decode($nc.' COMPARENDOS '),1,0,'C');
        $pdf->CellFitScale(40,5,number_format($total_sr,2,',','.'),1,0,'R');
    }elseif($tipo==3){
        $nc      =0;
        for ($i = 0; $i < count($row); $i++) {
            $bs = $con->Listar("SELECT 
                        c.comparendo, 
                        DATE_FORMAT(c.fecha_comparendo, '%d/%m/%Y'), 
                        c.cedula, 
                        CONCAT_WS(' ', c.nombres, c.apellidos), 
                        c.placa, 
                        c.infraccion 
                    FROM 
                            gu_comparendo c  
                    WHERE c.comparendo = '".$row[$i][0]."'");
            
            $pdf->SetFont('Arial','',9);
            for ($z = 0; $z < count($bs); $z++) {
                $pdf->SetX(25);
                $pdf->CellFitScale(40,5, utf8_decode($bs[$z][1]),1,0,'L');
                $pdf->CellFitScale(50,5,utf8_decode($bs[$z][0]),1,0,'L');
                $pdf->CellFitScale(40,5,utf8_decode($bs[$z][2]),1,0,'L');
                $pdf->CellFitScale(100,5,utf8_decode(ucwords(mb_strtolower($bs[$z][3]))),1,0,'L');
                $pdf->CellFitScale(40,5,utf8_decode($bs[$z][4]),1,0,'L');
                $pdf->CellFitScale(40,5,utf8_decode($bs[$z][5]),1,0,'L');
                $pdf->Ln(5);
            }
            $nc      +=1;
            
        }
        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(25);
        $pdf->CellFitScale(310,5,utf8_decode($nc.' COMPARENDOS REPETIDOS'),1,0,'C');
    }


 
       
 while (ob_get_length()) {
  ob_end_clean();
}
 
 $pdf->Output(0,utf8_decode('Informe_Comparendos_'.$nomb_tipo.'('.date('d-m-Y').').pdf'),0);
}
elseif($_GET['ex']=='excel'){
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Comparendos_".$nomb_tipo.".xls");
  ?> 
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $nomb_tipo?></title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <?php IF($tipo==1){ ?>
    <th colspan="9" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>INFORME <?php echo $nomb_tipo?>
           <br/>SUCURSAL <?php echo $nsucursal.'<br/>'.'Entre Fechas ' .$fechaI.' A '.$fechaF ?>
           
           <br/>&nbsp;
                 
             </strong>
    </th>
    <tr>
        <td rowspan="2"><center><strong>NÚMERO COMPARENDO</strong></center></td>
        <td rowspan="2"><center><strong>FECHA COMPARENDO</strong></center></td>
        <td rowspan="2"><center><strong>NÚMERO IDENTIFICACIÓN</strong></center></td>
        <td rowspan="2"><center><strong>NOMBRES Y APELLIDOS</strong></center></td>
        <td rowspan="2"><center><strong>NÚMERO PLACA</strong></center></td>
        <td rowspan="2"><center><strong>INFRACCIÓN</strong></center></td>
        <td rowspan="2"><center><strong>FECHA RECAUDO</strong></center></td>
        <td rowspan="2"><center><strong>VALOR RECAUDO</strong></center></td>
        <td rowspan="2"><center><strong>VALOR TERCERO</strong></center></td>
    
    </tr>
    <tr></tr>
    <?php }elseif($tipo==2){ ?>
    <th colspan="7" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>INFORME <?php echo $nomb_tipo?>
           <br/>SUCURSAL <?php echo $nsucursal.'<br/>'.'Entre Fechas ' .$fechaI.' A '.$fechaF ?>
           
           <br/>&nbsp;
                 
             </strong>
    </th>
    <tr>
        <td rowspan="2"><center><strong>FECHA COMPARENDO</strong></center></td>
        <td rowspan="2"><center><strong>NÚMERO COMPARENDO</strong></center></td>
        <td rowspan="2"><center><strong>NÚMERO IDENTIFICACIÓN</strong></center></td>
        <td rowspan="2"><center><strong>NOMBRES Y APELLIDOS</strong></center></td>
        <td rowspan="2"><center><strong>NÚMERO PLACA</strong></center></td>
        <td rowspan="2"><center><strong>INFRACCIÓN</strong></center></td>
        <td rowspan="2"><center><strong>VALOR COMPARENDO</strong></center></td>
    
    </tr>
    <tr></tr>
    <?php } else { ?> 
    <th colspan="6" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>INFORME <?php echo $nomb_tipo?>
           <br/>SUCURSAL <?php echo $nsucursal.'<br/>'.'Entre Fechas ' .$fechaI.' A '.$fechaF ?>
           
           <br/>&nbsp;
                 
             </strong>
    </th>
    <tr>
        <td rowspan="2"><center><strong>FECHA COMPARENDO</strong></center></td>
        <td rowspan="2"><center><strong>NÚMERO COMPARENDO</strong></center></td>
        <td rowspan="2"><center><strong>NÚMERO IDENTIFICACIÓN</strong></center></td>
        <td rowspan="2"><center><strong>NOMBRES Y APELLIDOS</strong></center></td>
        <td rowspan="2"><center><strong>NÚMERO PLACA</strong></center></td>
        <td rowspan="2"><center><strong>INFRACCIÓN</strong></center></td>
    
    </tr>
    <tr></tr>
    <?php } 
    if($tipo==1){
        $total_r =0;
        $total_t =0;
        $nc      =0;
        for ($i = 0; $i < count($row); $i++) {
            echo '<tr>';
            echo '<td>&nbsp;'.$row[$i][0].'</td>';
            echo '<td>'.$row[$i][1].'</td>';
            echo '<td>'.$row[$i][2].'</td>';
            echo '<td>'.ucwords(mb_strtolower($row[$i][3])).'</td>';
            echo '<td>'.$row[$i][4].'</td>';
            echo '<td>'.$row[$i][5].'</td>';
            echo '<td>'.$row[$i][6].'</td>';
            echo '<td>'.number_format($row[$i][7],2,'.',',').'</td>';
            echo '<td>'.number_format($row[$i][8],2,'.',',').'</td>';
            echo '</tr>';
            $total_r += $row[$i][7];
            $total_t += $row[$i][8];
            $nc      +=1;
        }
        echo '<td colspan="7"><center><strong>'.$nc.' COMPARENDOS </center></strong></td>';
        echo '<td><strong>'.number_format($total_r,2,'.',',').'</strong></td>';
        echo '<td><strong>'.number_format($total_t,2,'.',',').'</strong></td>';
    } elseif($tipo==2){
        $nc      =0;
        $total_sr =0;
        for ($i = 0; $i < count($row); $i++) {
            echo '<tr>';
            echo '<td>'.$row[$i][1].'</td>';
            echo '<td>&nbsp;'.$row[$i][0].'</td>';
            echo '<td>'.$row[$i][2].'</td>';
            echo '<td>'.ucwords(mb_strtolower($row[$i][3])).'</td>';
            echo '<td>'.$row[$i][4].'</td>';
            echo '<td>'.$row[$i][5].'</td>';
            $valor_c = 0;
            $anno_i     = $row[$i][6];
            $infraccion = $row[$i][5];
            $rowv = $con->Listar("SELECT valor_sancion FROM gu_tipo_comparendo WHERE codigo = '$infraccion' AND anno = '$anno_i'");
            if(count($rowv)>0){
                $valor_c = $rowv[0][0];
            }
            echo '<td>'.number_format($valor_c,2,'.',',').'</td>';
            $total_sr +=$valor_c;
            echo '</tr>'; 
            $nc      +=1;
        }
         echo '<td colspan="6"><center><strong>'.$nc.' COMPARENDOS </center></strong></td>';
         echo '<td colspan="1"><center><strong>'.number_format($total_sr,2,'.',',').' </center></strong></td>';
    }elseif($tipo==3){
        $nc =0;
        for ($i = 0; $i < count($row); $i++) {
            $bs = $con->Listar("SELECT 
                        c.comparendo, 
                        DATE_FORMAT(c.fecha_comparendo, '%d/%m/%Y'), 
                        c.cedula, 
                        CONCAT_WS(' ', c.nombres, c.apellidos), 
                        c.placa, 
                        c.infraccion 
                    FROM 
                            gu_comparendo c  
                    WHERE c.comparendo = '".$row[$i][0]."'");

            for ($z = 0; $z < count($bs); $z++) {
                echo '<tr>';
                echo '<td>'.$bs[$z][1].'</td>';
                echo '<td>&nbsp;'.$bs[$z][0].'</td>';
                echo '<td>'.$bs[$z][2].'</td>';
                echo '<td>'.ucwords(mb_strtolower($bs[$z][3])).'</td>';
                echo '<td>'.$bs[$z][4].'</td>';
                echo '<td>'.$bs[$z][5].'</td>';
                echo '</tr>'; 
            }
             $nc      +=1;
        }
        echo '<td colspan="6"><center><strong>'.$nc.' COMPARENDOS REPETIDOS</center></strong></td>';
    } ?>
 </table>
</body>
</html>
<?php } ?>

