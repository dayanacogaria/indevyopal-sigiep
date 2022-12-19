<?php
#################################################################################################################
#                                           MODIFICACIONES
################################################################################################################
#06/09/2018 |Erica G. |Archivo Creado
################################################################################################################
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
$con = new ConexionPDO();
session_start();
ini_set('max_execution_time', 0);
$para = $_SESSION['anno'];

#************Datos Compañia************#
$compania = $_SESSION['compania'];
$rowC     = $con->Listar("SELECT ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
                dir.direccion,
                tel.valor,
                ter.ruta_logo, 
                c.nombre, 
                d.nombre 
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico 
LEFT JOIN   gf_ciudad c ON ter.ciudadidentificacion = c.id_unico 
LEFT JOIN   gf_departamento d ON c.departamento = d.id_unico 
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$rutalogoTer = $rowC[0][6];
$ciudad      = $rowC[0][7];
$departamento= $rowC[0][8];


#**********PDF*********#
    header("Content-Type: text/html;charset=utf-8");
    require'../fpdf/fpdf.php';
    ob_start();
    
    class PDF extends FPDF {
        function Header() {

            global $rutalogoTer;
            global $razonsocial;
            global $numeroIdent;
            global $ciudad;
            global $departamento;
            global $numero;
            if ($rutalogoTer != '') {
                $this->Image('../' . $rutalogoTer, 12, 6, 25);
            }

            $this->SetFont('Arial', 'B', 10);
            $this->SetY(10);
            $this->SetX(20);
            $this->MultiCell(180, 5, utf8_decode($razonsocial), 0, 'C');
            $this->SetX(20);
            $this->Cell(180, 5,'NIT:'.$numeroIdent, 0, 0, 'C');
            $this->Ln(5);
            $this->SetX(20);
            $this->MultiCell(180, 5,ucwords(mb_strtolower($ciudad.' - '.$departamento)),0, 'C');
            $this->Ln(8);
        }
        function Footer() {
        }
    }

    $pdf = new PDF('P', 'mm', 'Letter');
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetFillColor(238,238,238); 
    
    $sector = $con->Listar("SELECT id_unico, codigo, nombre FROM gp_sector");
    for ($s = 0; $s < count($sector); $s++) {
        $id_sector = $sector[$s][0];
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(200,8,utf8_decode(mb_strtoupper($sector[$s][1].' '.$sector[$s][2])),1,0,'C', true);
        $pdf->Ln(8);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(25,8,utf8_decode('CÓDIGO RUTA'),1,0,'C');
        $pdf->Cell(55,8,utf8_decode('TERCERO'),1,0,'C');
        $pdf->Cell(35,8,utf8_decode('DIRECCIÓN'),1,0,'C');
        $pdf->Cell(25,8,utf8_decode('LECT. ANTERIOR'),1,0,'C');
        $pdf->Cell(35,8,utf8_decode('LECT. ACTUAL'),1,0,'C');
        $pdf->Cell(25,8,utf8_decode('OBSERVACIONES'),1,0,'C');
        $pdf->Ln(8);
        
        $row = $con->Listar("SELECT uvms.id_unico,
            uv.codigo_ruta, 
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
             p.direccion   
        FROM gp_unidad_vivienda_medidor_servicio uvms 
        LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        WHERE uvs.estado_servicio = 1 
        AND uv.sector = $id_sector 
        AND m.estado_medidor != 3 AND p.estado = 4 
        ORDER BY cast((replace(uv.codigo_ruta, '.','')) as unsigned) ASC");
        for ($i = 0; $i < count($row); $i++) {
            $iduvms = $row[$i][0];
            $la = $con->Listar("SELECT  IF(LENGTH(valor)>3, SUBSTRING(valor, -3), valor) FROM gp_lectura 
                WHERE unidad_vivienda_medidor_servicio = $iduvms ORDER BY periodo desc 
                LIMIT 1");
            $ya = $pdf->GetY();
            if($ya>240){
                $pdf->AddPage();
            }
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(25,5,utf8_decode(''),0,0,'L');
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->MultiCell(55,5,utf8_decode(ucwords(mb_strtolower($row[$i][2]))),0,'L');
            $y2 = $pdf->GetY();
            $alt = $y2-$y;
            if($alt<7){
                $alt = 7;
            }
            $pdf->SetXY($x+55, $y);
            $x2 = $pdf->GetX();
            $y2 = $pdf->GetY();
            $pdf->MultiCell(35,5,utf8_decode($row[$i][3]),0,'L');
            $y22 = $pdf->GetY();
            $alt = $y22-$y2;
            if($alt<7){
                $alt = 7;
            }
            
            $pdf->SetXY($x-25, $y);
            $pdf->Cell(25,$alt,utf8_decode($row[$i][1]),1,0,'L');
            $pdf->Cell(55,$alt,utf8_decode(''),1,0,'L');
            $pdf->Cell(35,$alt,utf8_decode(''),1,0,'L');
            $pdf->Cell(25,$alt,utf8_decode($la[0][0]),1,0,'R');
            $pdf->Cell(35,$alt,utf8_decode(''),1,0,'L');
            $pdf->Cell(25,$alt,utf8_decode(''),1,0,'L');
            $pdf->Ln($alt);
        }
        if($s == (count($sector)-1)){}else {
            $pdf->AddPage();
        }
    }
    
    
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0, 'Informe_Toma_Lecturas(' . date('d/m/Y') . ').pdf', 0);