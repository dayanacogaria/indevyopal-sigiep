<?php
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
ini_set('max_execution_time',0);
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];

#************** Datos Compañia *********************#
$rowC = $con->Listar("SELECT    ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo, 
                c.rss, 
                c2.rss, d1.rss, d2.rss
FROM gf_tercero ter
LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN       gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
LEFT JOIN       gf_ciudad c ON ter.ciudadresidencia = c.id_unico 
LEFT JOIN       gf_ciudad c2 ON ter.ciudadidentificacion = c2.id_unico 
LEFT JOIN       gf_departamento d1 ON c.departamento = d1.id_unico 
LEFT JOIN       gf_departamento d2 ON c2.departamento = d2.id_unico 
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6]; 

#** DATOS EMPLEDOS **#
$ni = '';
if($_REQUEST['sltTipo']==1){
    $ni ='INFORME EMPLEADOS ACTIVOS';
    $row = $con->Listar("SELECT DISTINCT e.id_unico,  @rownum:=@rownum+1, e.codigointerno, 
        t.numeroidentificacion, 
        CONCAT_WS(' ', t.apellidouno, t.apellidodos, t.nombreuno, t.nombredos) AS NOMBRE, 
        (SELECT dr.direccion FROM gf_direccion dr WHERE dr.tercero = t.id_unico LIMIT 1) AS direccion, 
        (SELECT tl.valor FROM gf_telefono tl WHERE tl.tercero = t.id_unico LIMIT 1) AS telefono, 
        (SELECT cg.nombre FROM gf_cargo_tercero ct LEFT JOIN gf_cargo cg ON ct.cargo = cg.id_unico 
            WHERE ct.tercero = t.id_unico LIMIT 1) AS cargo, 
        (SELECT c.nombre FROM  gn_tercero_categoria tc LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
            WHERE e.id_unico = tc.empleado ORDER BY tc.id_unico DESC LIMIT 1) as categoria, 
        (SELECT c.salarioactual FROM  gn_tercero_categoria tc LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
            WHERE e.id_unico = tc.empleado ORDER BY tc.id_unico DESC LIMIT 1) as salario_actual, 
        (SELECT tp.nombre FROM  gn_empleado_tipo et LEFT JOIN gn_tipo_empleado tp ON et.tipo = tp.id_unico 
            WHERE  et.empleado =e.id_unico ORDER BY et.id_unico DESC LIMIT 1 ) as tipoE, 
        ue.nombre as uej, gg.nombre as ggt,
        DATE_FORMAT(t.fecha_nacimiento, '%d/%m/%Y'), 
        IF(t.fecha_nacimiento IS NOT NULL, TIMESTAMPDIFF(YEAR, t.fecha_nacimiento, CURRENT_DATE()), '') edad, 
        (SELECT DATE_FORMAT(fechaacto, '%d/%m/%Y') FROM gn_vinculacion_retiro WHERE empleado = e.id_unico AND estado = 1 order by fechaacto DESC LIMIT 1) 
    FROM gn_empleado e 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico 
    LEFT JOIN gn_unidad_ejecutora ue ON e.unidadejecutora = ue.id_unico 
    LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico   
    WHERE e.id_unico != 2 AND (((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto<=CURDATE()  ORDER BY vr.fechaacto DESC LIMIT 1)=1)) 
    ORDER BY NOMBRE ASC");
} else {
    $ni ='INFORME EMPLEADOS';
    $row = $con->Listar("SELECT DISTINCT e.id_unico,  @rownum:=@rownum+1, e.codigointerno, 
        t.numeroidentificacion, 
        CONCAT_WS(' ', t.apellidouno, t.apellidodos, t.nombreuno, t.nombredos) AS NOMBRE, 
        (SELECT dr.direccion FROM gf_direccion dr WHERE dr.tercero = t.id_unico LIMIT 1) AS direccion, 
        (SELECT tl.valor FROM gf_telefono tl WHERE tl.tercero = t.id_unico LIMIT 1) AS telefono, 
        (SELECT cg.nombre FROM gf_cargo_tercero ct LEFT JOIN gf_cargo cg ON ct.cargo = cg.id_unico 
            WHERE ct.tercero = t.id_unico LIMIT 1) AS cargo, 
        (SELECT c.nombre FROM  gn_tercero_categoria tc LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
            WHERE e.id_unico = tc.empleado ORDER BY tc.id_unico DESC LIMIT 1) as categoria, 
        (SELECT c.salarioactual FROM  gn_tercero_categoria tc LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
            WHERE e.id_unico = tc.empleado ORDER BY tc.id_unico DESC LIMIT 1) as salario_actual, 
        (SELECT tp.nombre FROM  gn_empleado_tipo et LEFT JOIN gn_tipo_empleado tp ON et.tipo = tp.id_unico 
            WHERE  et.empleado =e.id_unico ORDER BY et.id_unico DESC LIMIT 1 ) as tipoE, 
        ue.nombre as uej, gg.nombre as ggt,
        DATE_FORMAT(t.fecha_nacimiento, '%d/%m/%Y'), 
        IF(t.fecha_nacimiento IS NOT NULL, TIMESTAMPDIFF(YEAR, t.fecha_nacimiento, CURRENT_DATE()), '') edad , 
        (SELECT DATE_FORMAT(fechaacto, '%d/%m/%Y') FROM gn_vinculacion_retiro WHERE empleado = e.id_unico AND estado = 1 order by fechaacto DESC LIMIT 1) 
    FROM gn_empleado e 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico 
    LEFT JOIN gn_unidad_ejecutora ue ON e.unidadejecutora = ue.id_unico 
    LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico 
    WHERE e.id_unico != 2 
    ORDER BY NOMBRE ASC");
}
#********** Tipo PDF ***********#
if($_GET['t']==1){
    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF
    { 
        function Header()
        { 
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $ruta_logo;
            global $ni;
            if ($ruta_logo != '') {
                $this->Image('../' . $ruta_logo, 40,5,30);
            }

            $this->SetFont('Arial', 'B', 15);
            $this->SetY(10);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode($razonsocial), 0, 0, 'C');
            $this->Ln(5);
            $this->SetFont('Arial', '', 12);
            $this->SetX(25);
            $this->Cell(320, 5, $nombreIdent.': '.$numeroIdent, 0, 0, 'C');
            $this->Ln(5);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode($ni), 0, 0, 'C');
            $this->Ln(8); 

            $this->SetFont('Arial', 'B', 10);

            $this->Cell(10,9, utf8_decode(''),1,0,'C');#
            $this->Cell(15,9,utf8_decode(''),1,0,'C');#
            $this->Cell(20,9,utf8_decode(''),1,0,'C');#
            $this->Cell(40,9,utf8_decode(''),1,0,'C');#
            $this->Cell(20,9,utf8_decode(''),1,0,'C');#
            $this->Cell(20,9,utf8_decode(''),1,0,'C');# 
            $this->Cell(20,9,utf8_decode(''),1,0,'C');#
            $this->Cell(30,9,utf8_decode(''),1,0,'C');#
            $this->Cell(20,9,utf8_decode(''),1,0,'C');#
            $this->Cell(20,9,utf8_decode(''),1,0,'C');#
            $this->Cell(30,9,utf8_decode(''),1,0,'C');#
            $this->Cell(30,9,utf8_decode(''),1,0,'C');#
            $this->Cell(30,9,utf8_decode(''),1,0,'C');#
            $this->Cell(20,9,utf8_decode(''),1,0,'C');#
            $this->Cell(10,9,utf8_decode(''),1,0,'C');


            
            $this->SetX(10);
            $this->CellFitScale(10,9, utf8_decode('ITEM'),0,0,'C');#
            $this->CellFitScale(15,5,utf8_decode('CÓDIGO'),0,0,'C');#
            $this->CellFitScale(20,5,utf8_decode('NÚMERO'),0,0,'C');#
            $this->CellFitScale(40,9,utf8_decode('NOMBRES'),0,0,'C');#
            $this->CellFitScale(20,5,utf8_decode('FECHA '),0,0,'C');#
            $this->CellFitScale(20,9,utf8_decode('DIRECCIÓN'),0,0,'C');# 
            $this->CellFitScale(20,9,utf8_decode('TELÉFONO'),0,0,'C');#
            $this->CellFitScale(30,9,utf8_decode('CARGO'),0,0,'C');#
            $this->CellFitScale(20,9,utf8_decode('CATEGORÍA'),0,0,'C');#
            $this->CellFitScale(20,5,utf8_decode('SALARIO'),0,0,'C');#
            $this->CellFitScale(30,5,utf8_decode('TIPO'),0,0,'C');#
            $this->CellFitScale(30,5,utf8_decode('UNIDAD'),0,0,'C');#
            $this->CellFitScale(30,5,utf8_decode('GRUPO'),0,0,'C');
            $this->CellFitScale(20,5,utf8_decode('FECHA'),0,0,'C');#
            $this->CellFitScale(10,5,utf8_decode('EDAD'),0,0,'C');#
            $this->Ln(4);
            
            $this->SetX(10);
            $this->Cell(10,9, utf8_decode(''),0,0,'C');#
            $this->CellFitScale(15,4,utf8_decode('INTERNO'),0,0,'C');#
            $this->CellFitScale(20,4,utf8_decode('IDENTIFICACIÓN'),0,0,'C');#
            $this->Cell(40,9,utf8_decode(''),0,0,'C');#
            $this->CellFitScale(20,4,utf8_decode('VINCULACIÓN'),0,0,'C');#
            $this->Cell(20,9,utf8_decode(''),0,0,'C');# 
            $this->Cell(20,9,utf8_decode(''),0,0,'C');#
            $this->Cell(30,9,utf8_decode(''),0,0,'C');#
            $this->Cell(20,9,utf8_decode(''),0,0,'C');#
            $this->CellFitScale(20,4,utf8_decode('ACTUAL'),0,0,'C');#
            $this->CellFitScale(30,4,utf8_decode('EMPLEADO'),0,0,'C');#
            $this->CellFitScale(30,4,utf8_decode('EJECUTORA'),0,0,'C');#
            $this->CellFitScale(30,4,utf8_decode('GESTIÓN'),0,0,'C');
            $this->CellFitScale(20,4,utf8_decode('NACIMIENTO'),0,0,'C');#
            $this->Cell(10,5,utf8_decode(''),0,0,'C');#
           
            $this->Ln(5);
        }
        function Footer()
        {
            global $usuario;
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(90,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
            $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
            $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
            $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }

    $pdf = new PDF('L','mm','Legal');        
    $pdf->AddPage();
    $pdf->AliasNbPages();
   
    $pdf->SetFont('Arial','',8);
    for ($e = 0; $e < count($row); $e++) { 
        $item = $e+1;
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        $pdf->CellFitScale(10,5,'',0,0,'L');#ITEM
        $pdf->CellFitScale(15,5,'',0,0,'L');#CODIGO 
        $pdf->CellFitScale(20,5,'',0,0,'L');#NUMERO

        $pdf->MultiCell(40,5,utf8_decode(ucwords(mb_strtolower($row[$e][4]))),0,'L');#NOMBRE 
        $a1 = $pdf->GetY()-$y;
        $pdf->SetXY($x+85,$y);

        $pdf->CellFitScale(20,5,'',0,0,'L');# FEHCA

        $pdf->MultiCell(20,5,utf8_decode(ucwords(mb_strtolower($row[$e][5]))),0,'L');# DIRECCION
        $a2 = $pdf->GetY()-$y;
        $pdf->SetXY($x+125,$y);

        $pdf->CellFitScale(20,5,'',0,0,'L');#TELEFONO

        $pdf->MultiCell(30,5,utf8_decode(ucwords(mb_strtolower($row[$e][7]))),0,'L');# CARGO
        $a3 = $pdf->GetY()-$y;
        $pdf->SetXY($x+175,$y);

        $pdf->MultiCell(20,5,utf8_decode(ucwords(mb_strtolower($row[$e][8]))),0,'L');#CATEGORÍA
        $a4 = $pdf->GetY()-$y;
        $pdf->SetXY($x+195,$y);

        $pdf->CellFitScale(20,5,'',0,0,'L');#SALARIO

        $pdf->MultiCell(30,5,utf8_decode(ucwords(mb_strtolower($row[$e][10]))),0,'L');#TIPO
        $a5 = $pdf->GetY()-$y;
        $pdf->SetXY($x+245,$y);


        $pdf->MultiCell(30,5,utf8_decode(ucwords(mb_strtolower($row[$e][11]))),0,'L');#UNIDAD
        $a6 = $pdf->GetY()-$y;
        $pdf->SetXY($x+275,$y);

        $pdf->MultiCell(30,5,utf8_decode(ucwords(mb_strtolower($row[$e][12]))),0,'L');#GRUPO
        $a7 = $pdf->GetY()-$y;
        $pdf->SetXY($x+305,$y);


        $pdf->CellFitScale(20,5,'',0,0,'L');#
        $pdf->CellFitScale(10,5,'',0,0,'L');

        $h= max($a1, $a2,$a3, $a4, $a5, $a6, $a7);
        $pdf->SetXY($x,$y);
        $pdf->CellFitScale(10,$h,$item,1,0,'L');# ITEM
        $pdf->CellFitScale(15,$h,$row[$e][2],1,0,'L');#CODIGO
        $pdf->CellFitScale(20,$h,$row[$e][3],1,0,'L');#NUMERO
        $pdf->Cell(40,$h,'',1,0,'L');#NOMBRE
        $pdf->CellFitScale(20,$h,$row[$e][15],1,0,'L');#FECHA
        $pdf->Cell(20,$h,'',1,0,'L');# DIRECCION
        $pdf->CellFitScale(20,$h,$row[$e][6],1,0,'L');#TELEFONO
        $pdf->Cell(30,$h,'',1,0,'L');#CARGO
        $pdf->Cell(20,$h,'',1,0,'L');#CATEGORÍA
        $pdf->CellFitScale(20,$h,number_format($row[$e][9],0),1,0,'R');#SALARIO
        $pdf->Cell(30,$h,'',1,0,'L');#TIPO
        $pdf->Cell(30,$h,'',1,0,'L');#UNIDAD
        $pdf->Cell(30,$h,'',1,0,'L');#GRUPO
        $pdf->CellFitScale(20,$h,$row[$e][13],1,0,'L');#FECHA
        $pdf->CellFitScale(10,$h,$row[$e][14],1,0,'L');#EDAD

        $pdf->Ln($h);
        if($pdf->GetY()>160){
            $pdf->AddPage();
        }
    }
  

    ob_end_clean();
    $pdf->Output(0,'InformeEmpleados('.date('d/m/Y').').pdf',0);     
}
#******** Tipo Excel *************#
 else{
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=InformeEmpleados.xls");  
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<title>Sabana Nomina</title>';
    echo '</head>';
    echo '<body>';
    echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';


    echo '<th colspan="15" align="center"><strong>';
    echo '<br/>&nbsp;';
    echo '<br/>'.$razonsocial;
    echo '<br/>'.$nombreIdent.': '.$numeroIdent;
    echo '<br/>&nbsp;';
    echo '<br/>'.$ni;
    echo '<br/>&nbsp;';
    echo '</strong>';
    echo '</th>';
    echo '<tr></tr>  ';
   
    echo '<tr>'; 
    echo '<td><strong>ITEM</strong></td>';
    echo '<td><strong>CÓDIGO INTERNO</strong></td>';
    echo '<td><strong>NÚMERO IDENTIFICACIÓN</strong></td>';
    echo '<td><strong>NOMBRES</strong></td>';
    echo '<td><strong>FECHA VINCULACIÓN</strong></td>';
    echo '<td><strong>DIRECCIÓN</strong></td>';
    echo '<td><strong>TELÉFONO</strong></td>';
    echo '<td><strong>CARGO</strong></td>';
    echo '<td><strong>CATEGORÍA</strong></td>';
    echo '<td><strong>SALARIO ACTUAL</strong></td>';
    echo '<td><strong>TIPO EMPLEADO</strong></td>';
    echo '<td><strong>UNIDAD EJECUTORA</strong></td>';
    echo '<td><strong>GRUPO DE GESTIÓN</strong></td>';
    echo '<td><strong>FECHA DE NACIMIENTO</strong></td>';
    echo '<td><strong>EDAD</strong></td>';
    echo '</tr>';

    for ($e = 0; $e < count($row); $e++) { 
        $item = $e+1;
            echo '<tr>';
            echo '<td align= "left">'.$item.'</td>';
            echo '<td align= "left">'.$row[$e][2].'</td>';
            echo '<td align= "left">'.$row[$e][3].'</td>';
            echo '<td align= "left">'.ucwords(mb_strtolower($row[$e][4])).'</td>';
            echo '<td align= "left">'.($row[$e][15]).'</td>';
            echo '<td align= "left">'.ucwords(mb_strtolower($row[$e][5])).'</td>';
            echo '<td align= "left">'.$row[$e][6].'</td>';
            echo '<td align= "left">'.ucwords(mb_strtolower($row[$e][7])).'</td>';
            echo '<td align= "left">'.ucwords(mb_strtolower($row[$e][8])).'</td>';
            echo '<td align= "right">'.number_format($row[$e][9], 0).'</td>';
            echo '<td align= "left">'.ucwords(mb_strtolower($row[$e][10])).'</td>';
            echo '<td align= "left">'.ucwords(mb_strtolower($row[$e][11])).'</td>';
            echo '<td align= "left">'.ucwords(mb_strtolower($row[$e][12])).'</td>';
            echo '<td align= "left">'.($row[$e][13]).'</td>';
            echo '<td align= "left">'.($row[$e][14]).'</td>';

            echo '</tr>';
    }
    echo '</table>';
    echo '</body>';
}
?> 