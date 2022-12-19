<?php

##########################################################################################
# ******************************  Sábana Transito  ************************************** # 
##########################################################################################
# 23/10/2018 | Modificación Código y Consultas
##########################################################################################    
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
ini_set('max_execution_time', 0);
session_start();
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];

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
$ruta_logo = $rowC[0][6];


#************** Datos Recibe *********************#
$periodo = $_POST['sltPeriodo'];
$formato = $_POST['sltFormato'];
$firma = $_POST['sltFirma'];
$np = $con->Listar("SELECT p.id_unico,p.codigointerno, tpn.nombre , fechafin , fechainicio 
    FROM gn_periodo p 
    LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico 
    WHERE p.id_unico = $periodo");

$nperiodo = ucwords(mb_strtolower($np[0][1] . ' - ' . $np[0][2]));
$fechafin = $np[0][3];
$fechaInicio = $np[0][4];

#********** Tipo PDF ***********#
if ($_GET['t'] == 1) {
    require'../fpdf/fpdf.php';
    ob_start();

    class PDF extends FPDF {

        function Header() {
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $ruta_logo;
            global $nperiodo;
            if ($ruta_logo != '') {
                $this->Image('../' . $ruta_logo, 20, 8, 20);
            }

            $this->SetFont('Arial', 'B', 10);
            $this->SetY(10);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode($razonsocial), 0, 0, 'C');
            $this->Ln(4);
            $this->SetFont('Arial', '', 8);
            $this->SetX(25);
            $this->Cell(320, 5, $nombreIdent . ': ' . $numeroIdent, 0, 0, 'C');
            $this->SetFont('Arial', 'B', 8);
            $this->Ln(4);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode('SÁBANA DE NÓMINA'), 0, 0, 'C');
            $this->Ln(4);
            $this->SetFont('Arial', 'B', 8);
            $this->SetX(25);
            $this->Cell(320, 5, utf8_decode('NÓMINA:' . $nperiodo), 0, 0, 'C');
            $this->Ln(5);
        }

        function Footer() {
            global $usuario;
            $this->SetY(-15);
            $this->SetFont('Arial', 'B', 8);
            $this->SetX(10);
            $this->Cell(90, 10, utf8_decode('Fecha: ' . date('d/m/Y')), 0, 0, 'L');
            $this->Cell(90, 10, utf8_decode('Máquina: ' . gethostname()), 0, 0, 'C');
            $this->Cell(90, 10, utf8_decode('Usuario: ' . strtoupper($usuario)), 0, 0, 'C');
            $this->Cell(65, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
        }

    }

    $pdf = new PDF('L', 'mm', 'Legal');
    $pdf->AddPage();
    $pdf->AliasNbPages();

    $grupog = $_POST['sltGrupoG'];
    $unidad = $_POST['sltUnidadE'];
    if (!empty($unidad) && !empty($grupog)) {
        if ($formato == 1) {
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(10);
            $pdf->Cell(50, 5, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
            $pdf->Cell(100, 5, utf8_decode($rowu[0][1]), 0, 0, 'L');
            $pdf->Ln(4);
            #**** Buscar Grupos de Gestión Del Seleccionado y De La unidad ejecutora id****#
            $rowg = $con->Listar("SELECT * FROM gn_grupo_gestion WHERE id_unico = $grupog");
            $pdf->Cell(50, 5, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
            $pdf->Cell(100, 5, utf8_decode($rowg[0][1]), 0, 0, 'L');
            $pdf->Ln(4);
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

            $filas = 210 / $ncon[0][0];
            $pdf->SetFont('Arial', 'B', 7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
            $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
            $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
            $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
            $h2 = 0;
            $h = 0;
            $alto = 0;
            #**** Nombre de conceptos ****#
            $rowcn = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) 
            ORDER BY c.clase,c.id_unico");
            #*** Titulos ***#
            $pdf->SetFont('Arial', 'B', 7);
            for ($c = 0; $c < count($rowcn); $c++) {
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if ($h > $h2) {
                    $alto = $h;
                    $h2 = $h;
                } else {
                    //$alto = $h2;
                }
                $pdf->SetXY($x + $filas, $y);
            }
            $pdf->SetXY($cx, $cy);
            $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
            $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
            $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
            for ($c = 0; $c < count($rowcn); $c++) {
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                $pdf->SetXY($x + $filas, $y);
            }
            $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
            $pdf->Ln($alto);
            #***************************************************************#
            #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora  Seleccionado***#
            $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
            t.numeroidentificacion, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
            ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
            $pdf->SetFont('Arial', '', 8);
            $salarioa = 0;
            for ($e = 0; $e < count($rowe); $e++) {
                $x = $pdf->GetY();
                if ($x >= 176){
                    $pdf->AddPage();
                    $pdf->Ln(10);
                }
                $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                #*** Salario ****#
                $basico = $con->Listar("SELECT 
                valor FROM gn_novedad 
                WHERE empleado = " . $rowe[$e][0] . " 
                AND concepto = '1' AND periodo = '$periodo'");

                $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                $salarioa += $basico[0][0];
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                $pdf->Ln(8);
            }
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
            $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');

            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
            }
            $pdf->Ln(17);
        } else if ($formato == 2) {
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(10);
            $pdf->Cell(50, 5, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
            $pdf->Cell(100, 5, utf8_decode($rowu[0][1]), 0, 0, 'L');
            $pdf->Ln(4);
            #**** Buscar Grupos de Gestión Del Seleccionado y De La unidad ejecutora id****#
            $rowg = $con->Listar("SELECT * FROM gn_grupo_gestion WHERE id_unico = $grupog");
            $pdf->Cell(50, 5, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
            $pdf->Cell(100, 5, utf8_decode($rowg[0][1]), 0, 0, 'L');
            $pdf->Ln(4);

            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

            $filas = 210 / $ncon[0][0];
            $pdf->SetFont('Arial', 'B', 7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
            $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
            $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
            $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
            $h2 = 0;
            $h = 0;
            $alto = 0;
            $totalcon = 0;
            #**** Nombre de conceptos clase 1****#
            $rowcn = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 1) 
            ORDER BY c.clase,c.id_unico");
            #*** Titulos ***#
            for ($c = 0; $c < count($rowcn); $c++) {
                $totalcon ++;
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if ($h > $h2) {
                    $alto = $h;
                    $h2 = $h;
                } else {
                    //$alto = $h2;
                }
                $pdf->SetXY($x + $filas, $y);
            }
            #**** Nombre de conceptos clase 3****#
            $rowcn2 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 3) 
            ORDER BY c.clase,c.id_unico");
            #*** Titulos ***#
            for ($c = 0; $c < count($rowcn2); $c++) {
                $totalcon ++;
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn2[$c][1]))), 0, 'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if ($h > $h2) {
                    $alto = $h;
                    $h2 = $h;
                } else {
                    //$alto = $h2;
                }
                $pdf->SetXY($x + $filas, $y);
            }
            #**** Nombre de conceptos clase 2****#
            $rowcn3 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 2) 
            ORDER BY c.clase,c.id_unico");
            #*** Titulos ***#
            for ($c = 0; $c < count($rowcn3); $c++) {
                $totalcon ++;
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn3[$c][1]))), 0, 'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if ($h > $h2) {
                    $alto = $h;
                    $h2 = $h;
                } else {
                    //$alto = $h2;
                }
                $pdf->SetXY($x + $filas, $y);
            }
            #**** Nombre de conceptos clase 4****#
            $rowcn4 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 4) 
            ORDER BY c.clase,c.id_unico");
            #*** Titulos ***#
            for ($c = 0; $c < count($rowcn4); $c++) {
                $totalcon ++;
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn4[$c][1]))), 0, 'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if ($h > $h2) {
                    $alto = $h;
                    $h2 = $h;
                } else {
                    //$alto = $h2;
                }
                $pdf->SetXY($x + $filas, $y);
            }
            #**** Nombre de conceptos clase 5****#
            $rowcn5 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 5) 
            ORDER BY c.clase,c.id_unico");
            #*** Titulos ***#
            for ($c = 0; $c < count($rowcn5); $c++) {
                $totalcon ++;
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn5[$c][1]))), 0, 'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if ($h > $h2) {
                    $alto = $h;
                    $h2 = $h;
                } else {
                    //$alto = $h2;
                }
                $pdf->SetXY($x + $filas, $y);
            }
            $pdf->SetXY($cx, $cy);
            $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
            $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
            $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
            for ($c = 0; $c < $totalcon; $c++) {
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                $pdf->SetXY($x + $filas, $y);
            }
            $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
            $pdf->Ln($alto);
            #***************************************************************#
            #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora  Seleccionado***#
            $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
            t.numeroidentificacion, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
            ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
            $pdf->SetFont('Arial', '', 8);
            $salarioa = 0;
            for ($e = 0; $e < count($rowe); $e++) {
                $x = $pdf->GetY();
                if ($x >= 176){
                    $pdf->AddPage();
                    $pdf->Ln(10);
                }
                $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                #*** Salario ****#
                $basico = $con->Listar("SELECT 
                valor FROM gn_novedad 
                WHERE empleado = " . $rowe[$e][0] . " 
                AND concepto = '1' AND periodo = '$periodo'");

                $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                $salarioa += $basico[0][0];
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                // valor clase 1
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // valor clase 3
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // valor clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // valor clase 4
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // valor clase 5
                for ($c = 0; $c < count($rowcn5); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn5[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                $pdf->Ln(8);
            }
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
            $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');
            //total clase 1
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
            }
            //total clase 3
            for ($c = 0; $c < count($rowcn2); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
            }
            //total clase 2
            for ($c = 0; $c < count($rowcn3); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
            }
            //total clase 4
            for ($c = 0; $c < count($rowcn4); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
            }
            //total clase 5
            for ($c = 0; $c < count($rowcn5); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn5[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
            }
            $pdf->Ln(17);
        } else {
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(10);
            $pdf->Cell(50, 5, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
            $pdf->Cell(100, 5, utf8_decode($rowu[0][1]), 0, 0, 'L');
            $pdf->Ln(4);
            #**** Buscar Grupos de Gestión Del Seleccionado y De La unidad ejecutora id****#
            $rowg = $con->Listar("SELECT * FROM gn_grupo_gestion WHERE id_unico = $grupog");
            $pdf->Cell(50, 5, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
            $pdf->Cell(100, 5, utf8_decode($rowg[0][1]), 0, 0, 'L');
            $pdf->Ln(4);

            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5)");

            $filas = 210 / $ncon[0][0];
            $pdf->SetFont('Arial', 'B', 7);
            $cx = $pdf->GetX();
            $cy = $pdf->GetY();
            $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
            $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
            $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
            $h2 = 0;
            $h = 0;
            $alto = 0;
            $totalcon = 0;
            #**** Nombre de conceptos clase 6****#
            $rowcn = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 6) 
            ORDER BY c.clase,c.id_unico");
            #*** Titulos ***#
            for ($c = 0; $c < count($rowcn); $c++) {
                $totalcon ++;
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if ($h > $h2) {
                    $alto = $h;
                    $h2 = $h;
                } else {
                    //$alto = $h2;
                }
                $pdf->SetXY($x + $filas, $y);
            }
            #**** Nombre de conceptos clase 1****#
            $rowcn2 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 1) 
            ORDER BY c.clase,c.id_unico");
            #*** Titulos ***#
            for ($c = 0; $c < count($rowcn2); $c++) {
                $totalcon ++;
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn2[$c][1]))), 0, 'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if ($h > $h2) {
                    $alto = $h;
                    $h2 = $h;
                } else {
                    //$alto = $h2;
                }
                $pdf->SetXY($x + $filas, $y);
            }
            #**** Nombre de conceptos clase 2****#
            $rowcn3 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 2) 
            ORDER BY c.clase,c.id_unico");
            #*** Titulos ***#
            for ($c = 0; $c < count($rowcn3); $c++) {
                $totalcon ++;
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn3[$c][1]))), 0, 'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if ($h > $h2) {
                    $alto = $h;
                    $h2 = $h;
                } else {
                    //$alto = $h2;
                }
                $pdf->SetXY($x + $filas, $y);
            }
            #**** Nombre de conceptos clase 5****#
            $rowcn4 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 5) 
            ORDER BY c.clase,c.id_unico");
            #*** Titulos ***#
            for ($c = 0; $c < count($rowcn4); $c++) {
                $totalcon ++;
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn4[$c][1]))), 0, 'C');
                $y2 = $pdf->GetY();
                $h = $y2 - $y;
                if ($h > $h2) {
                    $alto = $h;
                    $h2 = $h;
                } else {
                    //$alto = $h2;
                }
                $pdf->SetXY($x + $filas, $y);
            }
            $pdf->SetXY($cx, $cy);
            $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
            $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
            $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
            for ($c = 0; $c < $totalcon; $c++) {
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                $pdf->SetXY($x + $filas, $y);
            }
            $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
            $pdf->Ln($alto);
            #***************************************************************#
            #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora  Seleccionado***#
            $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
            t.numeroidentificacion, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
            ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5) AND n.valor>0)");
            $pdf->SetFont('Arial', '', 8);
            $salarioa = 0;
            for ($e = 0; $e < count($rowe); $e++) {
                $x = $pdf->GetY();
                if ($x >= 176){
                    $pdf->AddPage();
                    $pdf->Ln(10);
                }
                $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                #*** Salario ****#
                $basico = $con->Listar("SELECT 
                valor FROM gn_novedad 
                WHERE empleado = " . $rowe[$e][0] . " 
                AND concepto = '1' AND periodo = '$periodo'");

                $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                $salarioa += $basico[0][0];
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                // valor clase 6
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // valor clase 1
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // valor clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // valor clase 5
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                $pdf->Ln(8);
            }
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
            $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');
            //total clase 6
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
            }
            //total clase 1
            for ($c = 0; $c < count($rowcn2); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
            }
            //total clase 2
            for ($c = 0; $c < count($rowcn3); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
            }
            //total clase 5
            for ($c = 0; $c < count($rowcn4); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
            }
            $pdf->Ln(17);
        }
    } elseif (!empty($unidad) && empty($grupog)) {
        if ($formato == 1) {
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(10);
            $pdf->Cell(50, 5, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
            $pdf->Cell(100, 5, utf8_decode($rowu[0][1]), 0, 0, 'L');
            $pdf->Ln(4);
            #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE e.unidadejecutora = $unidad");
            for ($g = 0; $g < count($rowg); $g++) {
                $xhg = $pdf->GetY();
                if ($xhg >= 176){
                    $pdf->AddPage();
                    $pdf->Ln(10);
                }
                $pdf->Cell(50, 5, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
                $pdf->Cell(100, 5, utf8_decode($rowg[$g][1]), 0, 0, 'L');
                $pdf->Ln(4);

                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                $filas = 210 / $ncon[0][0];
                $pdf->SetFont('Arial', 'B', 7);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();                
                $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
                $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
                $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
                $h2 = 0;
                $h = 0;
                $alto = 0;
                #**** Nombre de conceptos ****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                $pdf->SetFont('Arial', 'B', 7);
                for ($c = 0; $c < count($rowcn); $c++) {
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }
                $pdf->SetXY($cx, $cy);
                $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
                for ($c = 0; $c < count($rowcn); $c++) {
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                    $pdf->SetXY($x + $filas, $y);
                }
                $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
                $pdf->Ln($alto);
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[$g][0] . " AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                $pdf->SetFont('Arial', '', 8);
                $salarioa = 0;
                for ($e = 0; $e < count($rowe); $e++) {
                    $x = $pdf->GetY();
                    if ($x >= 176){
                        $pdf->AddPage();
                        $pdf->Ln(10);
                    }
                    $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                    $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");

                    $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                    $salarioa += $basico[0][0];
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                    $pdf->Ln(8);
                }
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
                $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');

                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                $pdf->Ln(10);
            }
        } else if ($formato == 2) {
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(10);
            $pdf->Cell(50, 5, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
            $pdf->Cell(100, 5, utf8_decode($rowu[0][1]), 0, 0, 'L');
            $pdf->Ln(4);
            #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE e.unidadejecutora = $unidad");
            for ($g = 0; $g < count($rowg); $g++) {
                $xhg = $pdf->GetY();
                if ($xhg >= 176){
                    $pdf->AddPage();
                    $pdf->Ln(10);
                }
                $pdf->Cell(50, 5, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
                $pdf->Cell(100, 5, utf8_decode($rowg[$g][1]), 0, 0, 'L');
                $pdf->Ln(4);

                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                $filas = 210 / $ncon[0][0];
                $pdf->SetFont('Arial', 'B', 7);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();
                $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
                $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
                $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
                $h2 = 0;
                $h = 0;
                $alto = 0;
                $totalcon = 0;
                #**** Nombre de conceptos clase 1****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    $totalcon++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 3****#
                $rowcn2 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 3) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $totalcon++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn2[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 2****#
                $rowcn3 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 2) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $totalcon++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn3[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 4****#
                $rowcn4 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 4) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $totalcon++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn4[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 5****#
                $rowcn5 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn5); $c++) {
                    $totalcon++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn5[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                $pdf->SetXY($cx, $cy);
                $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
                for ($c = 0; $c < $totalcon; $c++) {
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                    $pdf->SetXY($x + $filas, $y);
                }
                $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
                $pdf->Ln($alto);
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[$g][0] . " AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                $pdf->SetFont('Arial', '', 8);
                $salarioa = 0;
                for ($e = 0; $e < count($rowe); $e++) {
                    $x = $pdf->GetY();
                    if ($x >= 176){
                        $pdf->AddPage();
                        $pdf->Ln(10);
                    }
                    $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                    $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");

                    $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                    $salarioa += $basico[0][0];
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    // valor clase 1
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // valor clase 3
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // valor clase 2
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // valor clase 4
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // valor clase 5
                    for ($c = 0; $c < count($rowcn5); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn5[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                    $pdf->Ln(8);
                }
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
                $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');
                // total clase 1
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // total clase 3
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // total clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // total clase 4
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // total clase 5
                for ($c = 0; $c < count($rowcn5); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn5[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                $pdf->Ln(17);
            }
        } else {
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(10);
            $pdf->Cell(50, 5, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
            $pdf->Cell(100, 5, utf8_decode($rowu[0][1]), 0, 0, 'L');
            $pdf->Ln(4);
            #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE e.unidadejecutora = $unidad");
            for ($g = 0; $g < count($rowg); $g++) {
                $xhg = $pdf->GetY();
                if ($xhg >= 176){
                    $pdf->AddPage();
                    $pdf->Ln(10);
                }
                $pdf->Cell(50, 5, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
                $pdf->Cell(100, 5, utf8_decode($rowg[$g][1]), 0, 0, 'L');
                $pdf->Ln(4);

                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5)");

                $filas = 210 / $ncon[0][0];
                $pdf->SetFont('Arial', 'B', 7);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();
                $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
                $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
                $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
                $h2 = 0;
                $h = 0;
                $alto = 0;
                $totalcon = 0;
                #**** Nombre de conceptos clase 6****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 6) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    $totalcon++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 1****#
                $rowcn2 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $totalcon++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn2[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 2****#
                $rowcn3 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 2) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $totalcon++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn3[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 5****#
                $rowcn4 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $totalcon++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn4[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                $pdf->SetXY($cx, $cy);
                $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
                for ($c = 0; $c < $totalcon; $c++) {
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                    $pdf->SetXY($x + $filas, $y);
                }
                $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
                $pdf->Ln($alto);
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[$g][0] . " AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                $pdf->SetFont('Arial', '', 8);
                $salarioa = 0;
                for ($e = 0; $e < count($rowe); $e++) {
                    $x = $pdf->GetY();
                    if ($x >= 176){
                        $pdf->AddPage();
                        $pdf->Ln(10);
                    }
                    $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                    $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");

                    $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                    $salarioa += $basico[0][0];
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    // valor clase 6
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // valor clase 1
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // valor clase 2
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // valor clase 5
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                    $pdf->Ln(8);
                }
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
                $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');
                // total clase 6
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // total clase 1
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // total clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                // total clase 5
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                $pdf->Ln(17);
            }
        }
    } elseif (empty($unidad) && !empty($grupog)) {
        if ($formato == 1) {
            #**** Buscar Grupos de Gestión ****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE gg.id_unico=$grupog");
            for ($g = 0; $g < count($rowg); $g++) {
                $pdf->Ln(10);
                $pdf->Cell(50, 5, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
                $pdf->Cell(100, 5, utf8_decode($rowg[$g][1]), 0, 0, 'L');
                $pdf->Ln(4);

                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                $filas = 210 / $ncon[0][0];
                $pdf->SetFont('Arial', 'B', 7);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();
                $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
                $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
                $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
                $h2 = 0;
                $h = 0;
                $alto = 0;
                #**** Nombre de conceptos ****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                $pdf->SetFont('Arial', 'B', 7);
                for ($c = 0; $c < count($rowcn); $c++) {
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }
                $pdf->SetXY($cx, $cy);
                $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
                for ($c = 0; $c < count($rowcn); $c++) {
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                    $pdf->SetXY($x + $filas, $y);
                }
                $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
                $pdf->Ln($alto);
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania 
            AND e.grupogestion = " . $rowg[$g][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                $pdf->SetFont('Arial', '', 8);
                $salarioa = 0;
                for ($e = 0; $e < count($rowe); $e++) {
                    $x = $pdf->GetY();
                    if ($x >= 176){
                        $pdf->AddPage();
                        $pdf->Ln(10);
                    }
                    $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                    $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");

                    $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                    $salarioa += $basico[0][0];
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                    $pdf->Ln(8);
                }
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
                $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');

                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                $pdf->Ln(17);
            }
        } else if ($formato == 2) {
            #**** Buscar Grupos de Gestión ****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE gg.id_unico=$grupog");
            for ($g = 0; $g < count($rowg); $g++) {
                $pdf->Ln(10);
                $pdf->Cell(50, 5, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
                $pdf->Cell(100, 5, utf8_decode($rowg[$g][1]), 0, 0, 'L');
                $pdf->Ln(4);

                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                $filas = 210 / $ncon[0][0];
                $pdf->SetFont('Arial', 'B', 7);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();
                $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
                $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
                $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
                $h2 = 0;
                $h = 0;
                $alto = 0;
                $totalcon = 0;
                #**** Nombre de conceptos clase 1****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    $totalcon ++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 3****#
                $rowcn2 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 3) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $totalcon ++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn2[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 2****#
                $rowcn3 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 2) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $totalcon ++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn3[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 4****#
                $rowcn4 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 4) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $totalcon ++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn4[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 5****#
                $rowcn5 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn5); $c++) {
                    $totalcon ++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn5[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }
                $pdf->SetXY($cx, $cy);
                $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
                for ($c = 0; $c < $totalcon; $c++) {
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                    $pdf->SetXY($x + $filas, $y);
                }
                $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
                $pdf->Ln($alto);
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania 
            AND e.grupogestion = " . $rowg[$g][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                $pdf->SetFont('Arial', '', 8);
                $salarioa = 0;
                for ($e = 0; $e < count($rowe); $e++) {
                    $x = $pdf->GetY();
                    if ($x >= 176){
                        $pdf->AddPage();
                        $pdf->Ln(10);
                    }
                    $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                    $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");

                    $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                    $salarioa += $basico[0][0];
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    // valor devengados
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // subtotal devengados
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // valor descuentos
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // subtotal descuentos
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // subtotal neto a pagar
                    for ($c = 0; $c < count($rowcn5); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn5[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                    $pdf->Ln(8);
                }
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
                $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');
                //total devengos
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                //total final devengos
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                //total descuentos
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                //total final descuentos
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                //total neto a pagar
                for ($c = 0; $c < count($rowcn5); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn5[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                $pdf->Ln(17);
            }
        } else {
            // formato 3
            #**** Buscar Grupos de Gestión ****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE gg.id_unico=$grupog");
            for ($g = 0; $g < count($rowg); $g++) {
                $pdf->Ln(10);
                $pdf->Cell(50, 5, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
                $pdf->Cell(100, 5, utf8_decode($rowg[$g][1]), 0, 0, 'L');
                $pdf->Ln(4);

                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5)");

                $filas = 210 / $ncon[0][0];
                $pdf->SetFont('Arial', 'B', 7);
                $cx = $pdf->GetX();
                $cy = $pdf->GetY();
                $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
                $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
                $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
                $h2 = 0;
                $h = 0;
                $alto = 0;
                $totalcon = 0;
                #**** Nombre de conceptos clase 6****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 6) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    $totalcon ++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 1****#
                $rowcn2 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $totalcon ++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn2[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 2****#
                $rowcn3 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 2) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $totalcon ++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn3[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                #**** Nombre de conceptos clase 5****#
                $rowcn4 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $totalcon ++;
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn4[$c][1]))), 0, 'C');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    if ($h > $h2) {
                        $alto = $h;
                        $h2 = $h;
                    } else {
                        //$alto = $h2;
                    }
                    $pdf->SetXY($x + $filas, $y);
                }

                $pdf->SetXY($cx, $cy);
                $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
                $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
                for ($c = 0; $c < $totalcon; $c++) {
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                    $pdf->SetXY($x + $filas, $y);
                }
                $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
                $pdf->Ln($alto);
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania 
            AND e.grupogestion = " . $rowg[$g][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5) AND n.valor>0)");
                $pdf->SetFont('Arial', '', 8);
                $salarioa = 0;
                for ($e = 0; $e < count($rowe); $e++) {
                    $x = $pdf->GetY();
                    if ($x >= 176){
                        $pdf->AddPage();
                        $pdf->Ln(10);
                    }
                    $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                    $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");

                    $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                    $salarioa += $basico[0][0];
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    // valor clase 6
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // valor clase 1
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // valor clase 2
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // subtotal clase 5
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                    $pdf->Ln(8);
                }
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
                $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');
                //total clase 6
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                //total clase 1
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                //total clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                //total clase 5
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                }
                $pdf->Ln(17);
            }
        }
    } else {
        if ($formato == 1) {
            //normal
            #**** Buscar Todas Unidades Ejecutoras del periodo***# 
            $rowu = $con->Listar("SELECT DISTINCT e.unidadejecutora, ue.nombre 
            FROM gn_novedad n 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gn_unidad_ejecutora ue ON ue.id_unico = e.unidadejecutora 
            WHERE n.periodo = $periodo");
            for ($u = 0; $u < count($rowu); $u++) {
                $unidad = $rowu[$u][0];
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Ln(10);
                $pdf->Cell(50, 4, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
                $pdf->Cell(100, 4, utf8_decode($rowu[$u][1]), 0, 0, 'L');
                $pdf->Ln(4);

                #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
                $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
                FROM gn_empleado e 
                LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
                WHERE e.unidadejecutora = $unidad");
                for ($g = 0; $g < count($rowg); $g++) {
                    $pdf->Cell(50, 4, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
                    $pdf->Cell(100, 4, utf8_decode($rowg[$g][1]), 0, 0, 'L');
                    $pdf->Ln(4);

                    #**** Numero de conceptos ****#
                    $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo      
                    AND n.valor > 0 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                    $filas = 210 / $ncon[0][0];
                    $pdf->SetFont('Arial', 'B', 7);
                    $cx = $pdf->GetX();
                    $cy = $pdf->GetY();

                    $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
                    $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
                    $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
                    $h2 = 0;
                    $h = 0;
                    $alto = 0;
                    #**** Nombre de conceptos ****#
                    $rowcn = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) 
                    ORDER BY c.clase,c.id_unico");
                    //echo $compania.','.$unidad.','.$rowg[$g][0].','.$periodo     .'--';
                    #*** Titulos ***#
                    $pdf->SetFont('Arial', 'B', 7);
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if ($h > $h2) {
                            $alto = $h;
                            $h2 = $h;
                        } else {
                            //$alto = $h2;
                        }
                        $pdf->SetXY($x + $filas, $y);
                    }
                    $pdf->SetXY($cx, $cy);
                    $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
                    $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
                    $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                        $pdf->SetXY($x + $filas, $y);
                    }
                    $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
                    $pdf->Ln($alto);
                    #***************************************************************#
                    #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                    $rowe = $con->Listar(" SELECT DISTINCT  e.id_unico, 
                    t.numeroidentificacion, 
                    e.tercero, 
                    t.id_unico,
                    t.numeroidentificacion, 
                    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                    ca.salarioactual 
                FROM gn_empleado e 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    $pdf->SetFont('Arial', '', 8);
                    $salarioa = 0;
                    for ($e = 0; $e < count($rowe); $e++) {
                        $x = $pdf->GetY();
                        if ($x >= 176){
                            $pdf->AddPage();
                            $pdf->Ln(10);
                        }
                        $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                        $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                        #*** Salario ****#
                        $basico = $con->Listar("SELECT 
                        valor FROM gn_novedad 
                        WHERE empleado = " . $rowe[$e][0] . " 
                        AND concepto = '1' AND periodo = '$periodo'");

                        $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                        $salarioa += $basico[0][0];
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        for ($c = 0; $c < count($rowcn); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                        }
                        $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                        $pdf->Ln(8);
                    }
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
                    $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');

                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    $pdf->Ln(17);
                }

                if ($u != (count($rowu) - 1)) {
                    $pdf->AddPage();
                }
            }
        } else if ($formato == 2) {
            //devengos
            #**** Buscar Todas Unidades Ejecutoras del periodo***# 
            $rowu = $con->Listar("SELECT DISTINCT e.unidadejecutora, ue.nombre 
            FROM gn_novedad n 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gn_unidad_ejecutora ue ON ue.id_unico = e.unidadejecutora 
            WHERE n.periodo = $periodo");
            for ($u = 0; $u < count($rowu); $u++) {
                $unidad = $rowu[$u][0];
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Ln(10);
                $pdf->Cell(50, 4, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
                $pdf->Cell(100, 4, utf8_decode($rowu[$u][1]), 0, 0, 'L');
                $pdf->Ln(4);

                #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
                $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
                FROM gn_empleado e 
                LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
                WHERE e.unidadejecutora = $unidad");
                for ($g = 0; $g < count($rowg); $g++) {
                    $pdf->Cell(50, 4, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
                    $pdf->Cell(100, 4, utf8_decode($rowg[$g][1]), 0, 0, 'L');
                    $pdf->Ln(4);

                    #**** Numero de conceptos ****#
                    $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo      
                    AND n.valor > 0 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                    $filas = 210 / $ncon[0][0];
                    $pdf->SetFont('Arial', 'B', 7);
                    $cx = $pdf->GetX();
                    $cy = $pdf->GetY();
                    $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
                    $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
                    $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
                    $h2 = 0;
                    $h = 0;
                    $alto = 0;
                    $totalcon = 0;
                    #**** Nombre de conceptos clase 1****#
                    $rowcn = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND (c.clase = 1 ) 
                    ORDER BY c.clase,c.id_unico");
                    //echo $compania.','.$unidad.','.$rowg[$g][0].','.$periodo     .'--';
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $totalcon ++;
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if ($h > $h2) {
                            $alto = $h;
                            $h2 = $h;
                        } else {
                            //$alto = $h2;
                        }
                        $pdf->SetXY($x + $filas, $y);
                    }

                    #**** Nombre de conceptos clase 3****#
                    $rowcn2 = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND (c.clase = 3) 
                    ORDER BY c.clase,c.id_unico");
                    //echo $compania.','.$unidad.','.$rowg[$g][0].','.$periodo     .'--';
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $totalcon ++;
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn2[$c][1]))), 0, 'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if ($h > $h2) {
                            $alto = $h;
                            $h2 = $h;
                        } else {
                            //$alto = $h2;
                        }
                        $pdf->SetXY($x + $filas, $y);
                    }

                    #**** Nombre de conceptos clase 2****#
                    $rowcn3 = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND (c.clase = 2) 
                    ORDER BY c.clase,c.id_unico");
                    //echo $compania.','.$unidad.','.$rowg[$g][0].','.$periodo     .'--';
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $totalcon ++;
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn3[$c][1]))), 0, 'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if ($h > $h2) {
                            $alto = $h;
                            $h2 = $h;
                        } else {
                            //$alto = $h2;
                        }
                        $pdf->SetXY($x + $filas, $y);
                    }

                    #**** Nombre de conceptos clase 4****#
                    $rowcn4 = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND (c.clase = 4) 
                    ORDER BY c.clase,c.id_unico");
                    //echo $compania.','.$unidad.','.$rowg[$g][0].','.$periodo     .'--';
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $totalcon ++;
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn4[$c][1]))), 0, 'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if ($h > $h2) {
                            $alto = $h;
                            $h2 = $h;
                        } else {
                            //$alto = $h2;
                        }
                        $pdf->SetXY($x + $filas, $y);
                    }

                    #**** Nombre de conceptos clase 5****#
                    $rowcn5 = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND (c.clase = 5) 
                    ORDER BY c.clase,c.id_unico");
                    //echo $compania.','.$unidad.','.$rowg[$g][0].','.$periodo     .'--';
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn5); $c++) {
                        $totalcon ++;
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell($filas, 5, utf8_decode(ucwords(mb_strtolower($rowcn5[$c][1]))), 0, 'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if ($h > $h2) {
                            $alto = $h;
                            $h2 = $h;
                        } else {
                            //$alto = $h2;
                        }
                        $pdf->SetXY($x + $filas, $y);
                    }

                    $pdf->SetXY($cx, $cy);
                    $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
                    $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
                    $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
                    for ($c = 0; $c < $totalcon; $c++) {
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                        $pdf->SetXY($x + $filas, $y);
                    }
                    $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
                    $pdf->Ln($alto);
                    #***************************************************************#
                    #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                    $rowe = $con->Listar(" SELECT DISTINCT  e.id_unico, 
                    t.numeroidentificacion, 
                    e.tercero, 
                    t.id_unico,
                    t.numeroidentificacion, 
                    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                    ca.salarioactual 
                FROM gn_empleado e 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    $pdf->SetFont('Arial', '', 8);
                    $salarioa = 0;
                    for ($e = 0; $e < count($rowe); $e++) {
                        $x = $pdf->GetY();
                        if ($x >= 176){
                            $pdf->AddPage();
                            $pdf->Ln(10);
                        }
                        $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                        $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                        #*** Salario ****#
                        $basico = $con->Listar("SELECT 
                        valor FROM gn_novedad 
                        WHERE empleado = " . $rowe[$e][0] . " 
                        AND concepto = '1' AND periodo = '$periodo'");

                        $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                        $salarioa += $basico[0][0];
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        // valor devengados
                        for ($c = 0; $c < count($rowcn); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                        }
                        //subtotal devengados
                        for ($c = 0; $c < count($rowcn2); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                        }
                        //valor descuento
                        for ($c = 0; $c < count($rowcn3); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                        }
                        //subtotal descuento
                        for ($c = 0; $c < count($rowcn4); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                        }
                        //subtotal neto a pagar
                        for ($c = 0; $c < count($rowcn5); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn5[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                        }
                        $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                        $pdf->Ln(8);
                    }
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
                    $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');

                    //total devengados
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor), c.clase
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    //total final devengados
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor), c.clase
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    //total descuentos
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor), c.clase
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    //total final descuentos
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor), c.clase
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    //total final neto a pagar
                    for ($c = 0; $c < count($rowcn5); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor), c.clase
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn5[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    $pdf->Ln(17);
                }

                if ($u != (count($rowu) - 1)) {
                    $pdf->AddPage();
                }
            }
        } else {
            //informativos
            #**** Buscar Todas Unidades Ejecutoras del periodo***# 
            $rowu = $con->Listar("SELECT DISTINCT e.unidadejecutora, ue.nombre 
            FROM gn_novedad n 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gn_unidad_ejecutora ue ON ue.id_unico = e.unidadejecutora 
            WHERE n.periodo = $periodo");
            for ($u = 0; $u < count($rowu); $u++) {
                $unidad = $rowu[$u][0];
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Ln(10);
                $pdf->Cell(50, 4, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
                $pdf->Cell(100, 4, utf8_decode($rowu[$u][1]), 0, 0, 'L');
                $pdf->Ln(4);

                #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
                $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
                FROM gn_empleado e 
                LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
                WHERE e.unidadejecutora = $unidad");
                for ($g = 0; $g < count($rowg); $g++) {
                    $pdf->Cell(50, 4, utf8_decode('GRUPO DE GESTIÓN:'), 0, 0, 'L');
                    $pdf->Cell(100, 4, utf8_decode($rowg[$g][1]), 0, 0, 'L');
                    $pdf->Ln(4);

                    #**** Numero de conceptos ****#
                    $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo      
                    AND n.valor > 0 
                    AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5)");

                    $filas = 210 / $ncon[0][0];
                    $pdf->SetFont('Arial', 'B', 7);
                    $cx = $pdf->GetX();
                    $cy = $pdf->GetY();
                    $pdf->Cell(25, 5, utf8_decode('Cédula'), 0, 0, 'C');
                    $pdf->Cell(48, 5, utf8_decode('Nombre'), 0, 0, 'C');
                    $pdf->Cell(18, 5, utf8_decode('Básico'), 0, 0, 'C');
                    $h2 = 0;
                    $h = 0;
                    $alto = 0;
                    $sumconceptos = 0;
                    #**** Nombre de conceptos clase 6****#
                    $rowcn = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND c.clase = 6");
                    //echo $compania.','.$unidad.','.$rowg[$g][0].','.$periodo.'--';
                    #*** Titulos ***#
                    $pdf->SetFont('Arial', 'B', 7);
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $sumconceptos ++;
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))), 0, 'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if ($h > $h2) {
                            $alto = $h;
                            $h2 = $h;
                        } else {
                            //$alto = $h2;
                        }
                        $pdf->SetXY($x + $filas, $y);
                    }

                    #**** Nombre de conceptos clase 1****#
                    $rowcn2 = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND c.clase = 1");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $sumconceptos ++;
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn2[$c][1]))), 0, 'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if ($h > $h2) {
                            $alto = $h;
                            $h2 = $h;
                        } else {
                            //$alto = $h2;
                        }
                        $pdf->SetXY($x + $filas, $y);
                    }
                    #**** Nombre de conceptos clase 2****#
                    $rowcn3 = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND c.clase = 2");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $sumconceptos ++;
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn3[$c][1]))), 0, 'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if ($h > $h2) {
                            $alto = $h;
                            $h2 = $h;
                        } else {
                            //$alto = $h2;
                        }
                        $pdf->SetXY($x + $filas, $y);
                    }
                    #**** Nombre de conceptos clase 5****#
                    $rowcn4 = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND c.clase = 5");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $sumconceptos ++;
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell($filas, 3, utf8_decode(ucwords(mb_strtolower($rowcn4[$c][1]))), 0, 'C');
                        $y2 = $pdf->GetY();
                        $h = $y2 - $y;
                        if ($h > $h2) {
                            $alto = $h;
                            $h2 = $h;
                        } else {
                            //$alto = $h2;
                        }
                        $pdf->SetXY($x + $filas, $y);
                    }

                    $pdf->SetXY($cx, $cy);
                    $pdf->Cell(25, $alto, utf8_decode(''), 1, 0, 'C');
                    $pdf->Cell(48, $alto, utf8_decode(''), 1, 0, 'C');
                    $pdf->Cell(18, $alto, utf8_decode(''), 1, 0, 'C');
                    for ($c = 0; $c < $sumconceptos; $c++) {
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->Cell($filas, $alto, utf8_decode(), 1, 'C');
                        $pdf->SetXY($x + $filas, $y);
                    }
                    $pdf->Cell(30, $alto, utf8_decode('Firma'), 1, 0, 'C');
                    $pdf->Ln($alto);
                    #***************************************************************#
                    #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                    $rowe = $con->Listar(" SELECT DISTINCT  e.id_unico, 
                    t.numeroidentificacion, 
                    e.tercero, 
                    t.id_unico,
                    t.numeroidentificacion, 
                    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                    ca.salarioactual 
                FROM gn_empleado e 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5) AND n.valor>0)");
                    $pdf->SetFont('Arial', '', 8);
                    $salarioa = 0;
                    for ($e = 0; $e < count($rowe); $e++) {
                        $x = $pdf->GetY();
                        if ($x >= 176){
                            $pdf->AddPage();
                            $pdf->Ln(10);
                        }
                        $pdf->Cellfitscale(25, 8, utf8_decode($rowe[$e][1]), 1, 0, 'L');
                        $pdf->Cellfitscale(48, 8, utf8_decode($rowe[$e][5]), 1, 0, 'L');

                        #*** Salario ****#
                        $basico = $con->Listar("SELECT 
                        valor FROM gn_novedad 
                        WHERE empleado = " . $rowe[$e][0] . " 
                        AND concepto = '1' AND periodo = '$periodo'");

                        $pdf->Cellfitscale(18, 8, utf8_decode(number_format($basico[0][0], 0, '.', ',')), 1, 0, 'R');
                        $salarioa += $basico[0][0];
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        //valor de clase concepto 6
                        for ($c = 0; $c < count($rowcn); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                        }
                        //valor de clase concepto 1
                        for ($c = 0; $c < count($rowcn2); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                        }
                        //valor de clase concepto 2
                        for ($c = 0; $c < count($rowcn3); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                        }
                        //valor de clase concepto 5
                        for ($c = 0; $c < count($rowcn4); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                        }
                        $pdf->Cellfitscale(30, 8, utf8_decode(''), 1, 0, 'R');
                        $pdf->Ln(8);
                    }
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(73, 8, utf8_decode('Total:'), 1, 0, 'C');
                    $pdf->Cellfitscale(18, 8, utf8_decode(number_format($salarioa, 0, '.', ',')), 1, 0, 'R');
                    // total clase concepto 6
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 6) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // total clase concepto 1
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 1) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // total clase concepto 2
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 2) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    // total clase concepto 5
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 5) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        $pdf->Cellfitscale($filas, 8, number_format($valor, 0, '.', ','), 1, 0, 'R');
                    }
                    $pdf->Ln(17);
                }

                if ($u != (count($rowu) - 1)) {
                    $pdf->AddPage();
                }
            }
        }
    }

    #**************** FIRMAS *****************#

    $pdf->Ln(20);
    $firmas = "SELECT   c.nombre, 
            rd.orden,
            IF(CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) 
            IS NULL OR CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) = '',
            (tr.razonsocial),
            CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)) AS NOMBRE
    FROM gf_responsable_documento rd 
    LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
    LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
    LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
    LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
    WHERE td.nombre = 'Sabana Nomina'
    ORDER BY rd.orden ASC";

    $fi = $mysqli->query($firmas);
    $altura = $pdf->GetY();
    $altura += 10;
    if ($altura > 200) {
        $pdf->AddPage();
        $pdf->Ln(15);
    }
    $pdf->SetFont('Arial', 'B', 8);
    $y = $pdf->GetY();
    $x = $pdf->GetX();
    while ($F = mysqli_fetch_row($fi)) {

        if ($F[1] == 1) {
            $pdf->Cell(60, 0.1, '', 1);
            $pdf->Ln(1);
            $pdf->cellfitscale(50, 2, utf8_decode($F[2]), 0, 0, 'L');
            $pdf->Ln(3);
            $pdf->cellfitscale(50, 3, utf8_decode($F[0]), 0, 0, 'L');
        } else {
            $pdf->SetXY($x + 25, $y);
            $pdf->Cell(60, 0.1, '', 1);
            $pdf->Ln(1);
            $y1 = $pdf->GetY();
            $pdf->SetXY($x + 25, $y1);
            $pdf->cellfitscale(50, 2, utf8_decode($F[2]), 0, 0, 'L');
            $pdf->Ln(3);
            $y2 = $pdf->GetY();
            $pdf->SetXY($x + 25, $y2);
            $pdf->cellfitscale(50, 3, utf8_decode($F[0]), 0, 0, 'L');
        }
        $x = $pdf->GetX();
    }
    
    if ($firma == 2){
        //elaboró
        $firmas1 = "SELECT   c.nombre, 
        rd.orden,
        IF(CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos) 
        IS NULL OR CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos) = '',
        (tr.razonsocial),
        CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos)) AS NOMBRE
        FROM gf_responsable_documento rd 
        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        WHERE td.nombre = 'Sabana Nomina' AND rd.orden = 0
        ORDER BY rd.orden ASC";
        $fi1 = $mysqli->query($firmas1);

        $pdf->Ln(20);
        while($Elab = mysqli_fetch_row($fi1)){
            $pdf->SetX(35);
            $pdf->cellfitscale(50,5,utf8_decode('Elaboró'),0,0,'L');
            $pdf->Ln(5);
            $pdf->SetX(35);
            $pdf->cellfitscale(50,5,utf8_decode($Elab[2]),0,0,'L');
            $pdf->Ln(3);
            $pdf->SetX(35);
            $pdf->cellfitscale(50,5,utf8_decode($Elab[0]),0,0,'L'); 
        }
    }      

    ob_end_clean();
    $pdf->Output(0, 'Sabana_Nomina(' . date('d/m/Y') . ').pdf', 0);
}
#******** Tipo Excel *************#
else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Sabana_Nomina.xls");
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<title>Sabana Nomina</title>';
    echo '</head>';
    echo '<body>';
    echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';

    $grupog = $_POST['sltGrupoG'];
    $unidad = $_POST['sltUnidadE'];
    if (!empty($unidad) && !empty($grupog)) {
        if ($formato == 1) {
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            $rowg = $con->Listar("SELECT * FROM gn_grupo_gestion WHERE id_unico = $grupog");

            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';
            echo '<tr><td colspan="' . $nc . '" ><strong><i>&nbsp;<br/>UNIDAD EJECUTORA: ' . $rowu[0][1] . '<br/>&nbsp;</i></strong></td></tr>';
            echo '<tr><td colspan="' . $nc . '" ><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN: ' . $rowg[0][1] . '<br/>&nbsp;</i></strong></td></tr>';

            echo '<tr>';
            echo '<td><strong>Cédula</strong></td>';
            echo '<td><strong>Nombre</strong></td>';
            echo '<td><strong>Básico</strong></td>';
            #**** Nombre de conceptos ****#
            $rowcn = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) 
            ORDER BY c.clase,c.id_unico");
            for ($c = 0; $c < count($rowcn); $c++) {
                echo '<td><strong>' . ucwords(mb_strtolower($rowcn[$c][1])) . '</strong></td>';
            }
            echo '<td><strong>Firma</strong></td>';
            echo '</tr>';

            #***************************************************************#
            #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora  Seleccionado***#
            $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
            t.numeroidentificacion, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
            ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");

            $salarioa = 0;
            for ($e = 0; $e < count($rowe); $e++) {
                echo '<tr>';
                echo '<td align="left">' . utf8_decode($rowe[$e][1]) . '</td>';
                echo '<td align="ritght">' . utf8_decode($rowe[$e][5]) . '</td>';
                #*** Salario ****#
                $basico = $con->Listar("SELECT 
                valor FROM gn_novedad 
                WHERE empleado = " . $rowe[$e][0] . " 
                AND concepto = '1' AND periodo = '$periodo'");
                echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                $salarioa += $basico[0][0];
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                }
                echo '<td></td>';
                echo '</tr>';
            }
            echo '<tr>';
            echo '<td colspan="2"><strong>Total</strong></td>';
            echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
            }
        } else if ($formato == 2) {
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            $rowg = $con->Listar("SELECT * FROM gn_grupo_gestion WHERE id_unico = $grupog");

            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 3 OR c.clase = 2 OR c.clase = 4 OR c.clase = 5)");

            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';
            echo '<tr><td colspan="' . $nc . '" ><strong><i>&nbsp;<br/>UNIDAD EJECUTORA: ' . $rowu[0][1] . '<br/>&nbsp;</i></strong></td></tr>';
            echo '<tr><td colspan="' . $nc . '" ><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN: ' . $rowg[0][1] . '<br/>&nbsp;</i></strong></td></tr>';

            echo '<tr>';
            echo '<td><strong>Cédula</strong></td>';
            echo '<td><strong>Nombre</strong></td>';
            echo '<td><strong>Básico</strong></td>';
            #**** Nombre de conceptos clase 1****#
            $rowcn = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 1) 
            ORDER BY c.clase,c.id_unico");
            for ($c = 0; $c < count($rowcn); $c++) {
                echo '<td><strong>' . ucwords(mb_strtolower($rowcn[$c][1])) . '</strong></td>';
            }
            #**** Nombre de conceptos clase 3****#
            $rowcn2 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 3) 
            ORDER BY c.clase,c.id_unico");
            for ($c = 0; $c < count($rowcn2); $c++) {
                echo '<td><strong>' . ucwords(mb_strtolower($rowcn2[$c][1])) . '</strong></td>';
            }
            #**** Nombre de conceptos clase 2****#
            $rowcn3 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 2) 
            ORDER BY c.clase,c.id_unico");
            for ($c = 0; $c < count($rowcn3); $c++) {
                echo '<td><strong>' . ucwords(mb_strtolower($rowcn3[$c][1])) . '</strong></td>';
            }
            #**** Nombre de conceptos clase 4****#
            $rowcn4 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 4) 
            ORDER BY c.clase,c.id_unico");
            for ($c = 0; $c < count($rowcn4); $c++) {
                echo '<td><strong>' . ucwords(mb_strtolower($rowcn4[$c][1])) . '</strong></td>';
            }
            #**** Nombre de conceptos clase 5****#
            $rowcn5 = $con->Listar("SELECT DISTINCT 
                n.concepto, 
                c.descripcion,
                c.id_unico
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo     
            AND n.valor > 0 
            AND (c.clase = 5) 
            ORDER BY c.clase,c.id_unico");
            for ($c = 0; $c < count($rowcn5); $c++) {
                echo '<td><strong>' . ucwords(mb_strtolower($rowcn5[$c][1])) . '</strong></td>';
            }
            echo '<td><strong>Firma</strong></td>';
            echo '</tr>';

            #***************************************************************#
            #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora  Seleccionado***#
            $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
            t.numeroidentificacion, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
            ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");

            $salarioa = 0;
            for ($e = 0; $e < count($rowe); $e++) {
                echo '<tr>';
                echo '<td align="left">' . utf8_decode($rowe[$e][1]) . '</td>';
                echo '<td align="ritght">' . utf8_decode($rowe[$e][5]) . '</td>';
                #*** Salario ****#
                $basico = $con->Listar("SELECT 
                valor FROM gn_novedad 
                WHERE empleado = " . $rowe[$e][0] . " 
                AND concepto = '1' AND periodo = '$periodo'");
                echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                $salarioa += $basico[0][0];
                // valor clase 1
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                }
                // valor clase 3
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                }
                // valor clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                }
                // valor clase 4
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                }
                // valor clase 5
                for ($c = 0; $c < count($rowcn5); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = " . $rowcn5[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                    AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                }
                echo '<td></td>';
                echo '</tr>';
            }
            echo '<tr>';
            echo '<td colspan="2"><strong>Total</strong></td>';
            echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
            //total clase 1
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
            }
            //total clase 3
            for ($c = 0; $c < count($rowcn2); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
            }
            //total clase 2
            for ($c = 0; $c < count($rowcn3); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
            }
            //total clase 4
            for ($c = 0; $c < count($rowcn4); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
            }
            //total clase 5
            for ($c = 0; $c < count($rowcn5); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = " . $rowcn5[$c][2] . " 
                AND n.periodo = $periodo 
                AND e.id_unico !=2 
                AND t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
            }
        } else {
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            $rowg = $con->Listar("SELECT * FROM gn_grupo_gestion WHERE id_unico = $grupog");

            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5)");

            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';
            echo '<tr><td colspan="' . $nc . '" ><strong><i>&nbsp;<br/>UNIDAD EJECUTORA: ' . $rowu[0][1] . '<br/>&nbsp;</i></strong></td></tr>';
            echo '<tr><td colspan="' . $nc . '" ><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN: ' . $rowg[0][1] . '<br/>&nbsp;</i></strong></td></tr>';

            echo '<tr>';
            echo '<td><strong>Cédula</strong></td>';
            echo '<td><strong>Nombre</strong></td>';
            echo '<td><strong>Básico</strong></td>';
            #**** Nombre de conceptos clase 6****#
            $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 6) 
                ORDER BY c.clase,c.id_unico");
            for ($c = 0; $c < count($rowcn); $c++) {
                echo '<td><strong>' . ucwords(mb_strtolower($rowcn[$c][1])) . '</strong></td>';
            }
            #**** Nombre de conceptos clase 1****#
            $rowcn2 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1) 
                ORDER BY c.clase,c.id_unico");
            for ($c = 0; $c < count($rowcn2); $c++) {
                echo '<td><strong>' . ucwords(mb_strtolower($rowcn2[$c][1])) . '</strong></td>';
            }
            #**** Nombre de conceptos clase 2****#
            $rowcn3 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 2) 
                ORDER BY c.clase,c.id_unico");
            for ($c = 0; $c < count($rowcn3); $c++) {
                echo '<td><strong>' . ucwords(mb_strtolower($rowcn3[$c][1])) . '</strong></td>';
            }
            #**** Nombre de conceptos clase 5****#
            $rowcn4 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[0][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
            for ($c = 0; $c < count($rowcn4); $c++) {
                echo '<td><strong>' . ucwords(mb_strtolower($rowcn4[$c][1])) . '</strong></td>';
            }
            echo '<td><strong>Firma</strong></td>';
            echo '</tr>';

            #***************************************************************#
            #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora  Seleccionado***#
            $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[0][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5) AND n.valor>0)");

            $salarioa = 0;
            for ($e = 0; $e < count($rowe); $e++) {
                echo '<tr>';
                echo '<td align="left">' . utf8_decode($rowe[$e][1]) . '</td>';
                echo '<td align="ritght">' . utf8_decode($rowe[$e][5]) . '</td>';
                #*** Salario ****#
                $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");
                echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                $salarioa += $basico[0][0];
                // valor clase 6
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                }
                // valor clase 1
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                }
                // valor clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                }
                // valor clase 5
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                }
                echo '<td></td>';
                echo '</tr>';
            }
            echo '<tr>';
            echo '<td colspan="2"><strong>Total</strong></td>';
            echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
            //total clase 6
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[0][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
            }
            //total clase 1
            for ($c = 0; $c < count($rowcn2); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[0][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
            }
            //total clase 2
            for ($c = 0; $c < count($rowcn3); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[0][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
            }
            //total clase 5
            for ($c = 0; $c < count($rowcn4); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[0][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                if ($num_con[0][1] > 0) {
                    $valor = $num_con[0][1];
                } else {
                    $valor = 0;
                }
                echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
            }
        }
    } elseif (!empty($unidad) && empty($grupog)) {
        if ($formato == 1) {
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");
            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            echo '<tr>';
            echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>UNIDAD EJECUTORA: ' . $rowu[0][1] . '<br/>&nbsp;</i></strong></td>';
            echo '</tr>';
            #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE e.unidadejecutora = $unidad");

            for ($g = 0; $g < count($rowg); $g++) {
                echo '<tr>';
                echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN:: ' . $rowg[$g][1] . '<br/>&nbsp;</i></strong></td>';
                echo '</tr>';
                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                echo '<tr>';
                echo '<td><strong>Cédula</strong></td>';
                echo '<td><strong>Nombre</strong></td>';
                echo '<td><strong>Básico</strong></td>';
                #**** Nombre de conceptos ****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    echo '<td><strong>' . utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))) . '</strong></td>';
                }
                echo '<td><strong>Firma</strong></td>';
                echo '</tr>';
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[$g][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                $salarioa = 0;
                echo '<tr>';
                for ($e = 0; $e < count($rowe); $e++) {
                    echo '<td align="left">' . $rowe[$e][1] . '</td>';
                    echo '<td align="ritght">' . $rowe[$e][5] . '</td>';
                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");
                    echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                    $salarioa += $basico[0][0];
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    echo '<td></td>';
                    echo '</tr>';
                }
                echo '<tr>';
                echo '<td colspan="2"><strong>Total</strong></td>';
                echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                echo '</tr>';
            }
        } else if ($formato == 2) {
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");
            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            echo '<tr>';
            echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>UNIDAD EJECUTORA: ' . $rowu[0][1] . '<br/>&nbsp;</i></strong></td>';
            echo '</tr>';
            #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE e.unidadejecutora = $unidad");

            for ($g = 0; $g < count($rowg); $g++) {
                echo '<tr>';
                echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN:: ' . $rowg[$g][1] . '<br/>&nbsp;</i></strong></td>';
                echo '</tr>';
                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                echo '<tr>';
                echo '<td><strong>Cédula</strong></td>';
                echo '<td><strong>Nombre</strong></td>';
                echo '<td><strong>Básico</strong></td>';
                #**** Nombre de conceptos clase 1****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    echo '<td><strong>' . utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))) . '</strong></td>';
                }
                #**** Nombre de conceptos clase 3****#
                $rowcn2 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 3) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn2); $c++) {
                    echo '<td><strong>' . utf8_decode(ucwords(mb_strtolower($rowcn2[$c][1]))) . '</strong></td>';
                }
                #**** Nombre de conceptos clase 2****#
                $rowcn3 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 2) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn3); $c++) {
                    echo '<td><strong>' . utf8_decode(ucwords(mb_strtolower($rowcn3[$c][1]))) . '</strong></td>';
                }
                #**** Nombre de conceptos clase 4****#
                $rowcn4 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 4) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn4); $c++) {
                    echo '<td><strong>' . utf8_decode(ucwords(mb_strtolower($rowcn4[$c][1]))) . '</strong></td>';
                }
                #**** Nombre de conceptos clase 5****#
                $rowcn5 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn5); $c++) {
                    echo '<td><strong>' . utf8_decode(ucwords(mb_strtolower($rowcn5[$c][1]))) . '</strong></td>';
                }
                echo '<td><strong>Firma</strong></td>';
                echo '</tr>';
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[$g][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                $salarioa = 0;
                echo '<tr>';
                for ($e = 0; $e < count($rowe); $e++) {
                    echo '<td align="left">' . $rowe[$e][1] . '</td>';
                    echo '<td align="ritght">' . $rowe[$e][5] . '</td>';
                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");
                    echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                    $salarioa += $basico[0][0];
                    //valor clase 1
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    //valor clase 3
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    //valor clase 2
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    //valor clase 4
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    //valor clase 5
                    for ($c = 0; $c < count($rowcn5); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn5[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    echo '<td></td>';
                    echo '</tr>';
                }
                echo '<tr>';
                echo '<td colspan="2"><strong>Total</strong></td>';
                echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
                //total clase 1
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 3
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 4
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 5
                for ($c = 0; $c < count($rowcn5); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn5[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                echo '</tr>';
            }
        } else {
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.unidadejecutora = $unidad 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5)");
            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';
            #**** Buscar Unidades Ejecutoras id ***#
            $rowu = $con->Listar("SELECT * FROM gn_unidad_ejecutora WHERE id_unico = $unidad");
            echo '<tr>';
            echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>UNIDAD EJECUTORA: ' . $rowu[0][1] . '<br/>&nbsp;</i></strong></td>';
            echo '</tr>';
            #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE e.unidadejecutora = $unidad");

            for ($g = 0; $g < count($rowg); $g++) {
                echo '<tr>';
                echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN:: ' . $rowg[$g][1] . '<br/>&nbsp;</i></strong></td>';
                echo '</tr>';
                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5)");

                echo '<tr>';
                echo '<td><strong>Cédula</strong></td>';
                echo '<td><strong>Nombre</strong></td>';
                echo '<td><strong>Básico</strong></td>';
                #**** Nombre de conceptos clase 6****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 6) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    echo '<td><strong>' . utf8_decode(ucwords(mb_strtolower($rowcn[$c][1]))) . '</strong></td>';
                }
                #**** Nombre de conceptos clase 1****#
                $rowcn2 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn2); $c++) {
                    echo '<td><strong>' . utf8_decode(ucwords(mb_strtolower($rowcn2[$c][1]))) . '</strong></td>';
                }
                #**** Nombre de conceptos clase 2****#
                $rowcn3 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 2) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn3); $c++) {
                    echo '<td><strong>' . utf8_decode(ucwords(mb_strtolower($rowcn3[$c][1]))) . '</strong></td>';
                }
                #**** Nombre de conceptos clase 5****#
                $rowcn4 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn4); $c++) {
                    echo '<td><strong>' . utf8_decode(ucwords(mb_strtolower($rowcn4[$c][1]))) . '</strong></td>';
                }
                echo '<td><strong>Firma</strong></td>';
                echo '</tr>';
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
            AND e.grupogestion = " . $rowg[$g][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5) AND n.valor>0)");
                $salarioa = 0;
                echo '<tr>';
                for ($e = 0; $e < count($rowe); $e++) {
                    echo '<td align="left">' . $rowe[$e][1] . '</td>';
                    echo '<td align="ritght">' . $rowe[$e][5] . '</td>';
                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");
                    echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                    $salarioa += $basico[0][0];
                    //valor clase 6
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    //valor clase 1
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    //valor clase 2
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    //valor clase 5
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    echo '<td></td>';
                    echo '</tr>';
                }
                echo '<tr>';
                echo '<td colspan="2"><strong>Total</strong></td>';
                echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
                //total clase 6
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 1
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 5
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                echo '</tr>';
            }
        }
    } elseif (empty($unidad) && !empty($grupog)) {
        if ($formato == 1) {
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.grupogestion = $grupog 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");
            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';

            #**** Buscar Grupos de Gestión ****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE gg.id_unico=$grupog");
            for ($g = 0; $g < count($rowg); $g++) {
                echo '<tr>';
                echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN: ' . $rowg[$g][1] . '<br/>&nbsp;</i></strong></td>';
                echo '</tr>';
                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                echo '<tr>';
                echo '<td><strong>Cédula</strong></td>';
                echo '<td><strong>Nombre</strong></td>';
                echo '<td><strong>Básico</strong></td>';
                #**** Nombre de conceptos ****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    echo '<td><strong>' . (ucwords(mb_strtolower($rowcn[$c][1]))) . '</strong></td>';
                }
                echo '<td><strong>Firma</strong></td>';
                echo '</tr>';
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania 
            AND e.grupogestion = " . $rowg[$g][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                echo '<tr>';
                for ($e = 0; $e < count($rowe); $e++) {
                    echo '<td align="left">' . $rowe[$e][1] . '</td>';
                    echo '<td align="ritght">' . $rowe[$e][5] . '</td>';
                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");
                    echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                    $salarioa += $basico[0][0];
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    echo '<td></td>';
                    echo '</tr>';
                }
                echo '<tr>';
                echo '<td colspan="2"><strong>Total</strong></td>';
                echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                echo '</tr>';
            }
        } else if ($formato == 2) {
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.grupogestion = $grupog 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");
            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';

            #**** Buscar Grupos de Gestión ****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE gg.id_unico=$grupog");
            for ($g = 0; $g < count($rowg); $g++) {
                echo '<tr>';
                echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN: ' . $rowg[$g][1] . '<br/>&nbsp;</i></strong></td>';
                echo '</tr>';
                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                echo '<tr>';
                echo '<td><strong>Cédula</strong></td>';
                echo '<td><strong>Nombre</strong></td>';
                echo '<td><strong>Básico</strong></td>';
                #**** Nombre de conceptos clase 1****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    echo '<td><strong>' . (ucwords(mb_strtolower($rowcn[$c][1]))) . '</strong></td>';
                }

                #**** Nombre de conceptos clase 3****#
                $rowcn2 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 3) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn2); $c++) {
                    echo '<td><strong>' . (ucwords(mb_strtolower($rowcn2[$c][1]))) . '</strong></td>';
                }

                #**** Nombre de conceptos clase 2****#
                $rowcn3 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 2) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn3); $c++) {
                    echo '<td><strong>' . (ucwords(mb_strtolower($rowcn3[$c][1]))) . '</strong></td>';
                }

                #**** Nombre de conceptos clase 4****#
                $rowcn4 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 4) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn4); $c++) {
                    echo '<td><strong>' . (ucwords(mb_strtolower($rowcn4[$c][1]))) . '</strong></td>';
                }

                #**** Nombre de conceptos clase 5****#
                $rowcn5 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn5); $c++) {
                    echo '<td><strong>' . (ucwords(mb_strtolower($rowcn5[$c][1]))) . '</strong></td>';
                }
                echo '<td><strong>Firma</strong></td>';
                echo '</tr>';
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania 
            AND e.grupogestion = " . $rowg[$g][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                echo '<tr>';
                for ($e = 0; $e < count($rowe); $e++) {
                    echo '<td align="left">' . $rowe[$e][1] . '</td>';
                    echo '<td align="ritght">' . $rowe[$e][5] . '</td>';
                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");
                    echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                    $salarioa += $basico[0][0];
                    // valor clase 1
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    // valor clase 3
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    // valor clase 2
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    // valor clase 4
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    // valor clase 5
                    for ($c = 0; $c < count($rowcn5); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn5[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    echo '<td></td>';
                    echo '</tr>';
                }
                echo '<tr>';
                echo '<td colspan="2"><strong>Total</strong></td>';
                echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
                //total clase 1
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 3
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 4
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 5
                for ($c = 0; $c < count($rowcn5); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn5[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                echo '</tr>';
            }
        } else {
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
            FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
            WHERE t.compania = $compania 
            AND e.grupogestion = $grupog 
            AND n.periodo = $periodo      
            AND n.valor > 0 
            AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5)");
            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';

            #**** Buscar Grupos de Gestión ****#
            $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
            FROM gn_empleado e 
            LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
            WHERE gg.id_unico=$grupog");
            for ($g = 0; $g < count($rowg); $g++) {
                echo '<tr>';
                echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN: ' . $rowg[$g][1] . '<br/>&nbsp;</i></strong></td>';
                echo '</tr>';
                #**** Numero de conceptos ****#
                $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");

                echo '<tr>';
                echo '<td><strong>Cédula</strong></td>';
                echo '<td><strong>Nombre</strong></td>';
                echo '<td><strong>Básico</strong></td>';
                #**** Nombre de conceptos clase 6****#
                $rowcn = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 6) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn); $c++) {
                    echo '<td><strong>' . (ucwords(mb_strtolower($rowcn[$c][1]))) . '</strong></td>';
                }

                #**** Nombre de conceptos clase 1****#
                $rowcn2 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 1) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn2); $c++) {
                    echo '<td><strong>' . (ucwords(mb_strtolower($rowcn2[$c][1]))) . '</strong></td>';
                }

                #**** Nombre de conceptos clase 2****#
                $rowcn3 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 2) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn3); $c++) {
                    echo '<td><strong>' . (ucwords(mb_strtolower($rowcn3[$c][1]))) . '</strong></td>';
                }

                #**** Nombre de conceptos clase 5****#
                $rowcn4 = $con->Listar("SELECT DISTINCT 
                    n.concepto, 
                    c.descripcion,
                    c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND n.periodo = $periodo     
                AND n.valor > 0 
                AND (c.clase = 5) 
                ORDER BY c.clase,c.id_unico");
                #*** Titulos ***#
                for ($c = 0; $c < count($rowcn4); $c++) {
                    echo '<td><strong>' . (ucwords(mb_strtolower($rowcn4[$c][1]))) . '</strong></td>';
                }
                echo '<td><strong>Firma</strong></td>';
                echo '</tr>';
                #***************************************************************#
                #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
                t.numeroidentificacion, 
                e.tercero, 
                t.id_unico,
                t.numeroidentificacion, 
                CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            WHERE e.id_unico !=2 AND compania = $compania 
            AND e.grupogestion = " . $rowg[$g][0] . " 
            AND e.id_unico 
            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
            AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5) AND n.valor>0)");
                echo '<tr>';
                for ($e = 0; $e < count($rowe); $e++) {
                    echo '<td align="left">' . $rowe[$e][1] . '</td>';
                    echo '<td align="ritght">' . $rowe[$e][5] . '</td>';
                    #*** Salario ****#
                    $basico = $con->Listar("SELECT 
                    valor FROM gn_novedad 
                    WHERE empleado = " . $rowe[$e][0] . " 
                    AND concepto = '1' AND periodo = '$periodo'");
                    echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                    $salarioa += $basico[0][0];
                    // valor clase 6
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    // valor clase 1
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    // valor clase 2
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    // valor clase 5
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                        AND n.periodo = $periodo ");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                    }
                    echo '<td></td>';
                    echo '</tr>';
                }
                echo '<tr>';
                echo '<td colspan="2"><strong>Total</strong></td>';
                echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
                //total clase 6
                for ($c = 0; $c < count($rowcn); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 1
                for ($c = 0; $c < count($rowcn2); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 2
                for ($c = 0; $c < count($rowcn3); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                //total clase 5
                for ($c = 0; $c < count($rowcn4); $c++) {
                    $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                    WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                    AND n.periodo = $periodo 
                    AND e.id_unico !=2 
                    AND t.compania = $compania 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    if ($num_con[0][1] > 0) {
                        $valor = $num_con[0][1];
                    } else {
                        $valor = 0;
                    }
                    echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                }
                echo '</tr>';
            }
        }
    } else {
        if ($formato == 1) {
            //formato 1
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");
            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';

            #**** Buscar Todas Unidades Ejecutoras del periodo***# 
            $rowu = $con->Listar("SELECT DISTINCT e.unidadejecutora, ue.nombre 
                FROM gn_novedad n 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_unidad_ejecutora ue ON ue.id_unico = e.unidadejecutora 
                WHERE n.periodo = $periodo");
            for ($u = 0; $u < count($rowu); $u++) {
                $unidad = $rowu[$u][0];
                echo '<tr>';
                echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>UNIDAD EJECUTORA: ' . $rowu[$u][1] . '<br/>&nbsp;</i></strong></td>';
                echo '</tr>';
                #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
                $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
                    FROM gn_empleado e 
                    LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
                    WHERE e.unidadejecutora = $unidad");
                for ($g = 0; $g < count($rowg); $g++) {
                    echo '<tr>';
                    echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN:: ' . $rowg[$g][1] . '<br/>&nbsp;</i></strong></td>';
                    echo '</tr>';
                    #**** Numero de conceptos ****#
                    $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo      
                        AND n.valor > 0 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");
                    echo '<tr>';
                    echo '<td><strong>Cédula</strong></td>';
                    echo '<td><strong>Nombre</strong></td>';
                    echo '<td><strong>Básico</strong></td>';
                    #**** Nombre de conceptos ****#
                    $rowcn = $con->Listar("SELECT DISTINCT 
                            n.concepto, 
                            c.descripcion,
                            c.id_unico
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo     
                        AND n.valor > 0 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) 
                        ORDER BY c.clase,c.id_unico");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn); $c++) {
                        echo '<td><strong>' . ucwords(mb_strtolower($rowcn[$c][1])) . '</strong></td>';
                    }
                    echo '<td><strong>Firma</strong></td>';
                    echo '</tr>';
                    #***************************************************************#
                    #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                    $rowe = $con->Listar(" SELECT DISTINCT  e.id_unico, 
                        t.numeroidentificacion, 
                        e.tercero, 
                        t.id_unico,
                        t.numeroidentificacion, 
                        CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                        ca.salarioactual 
                    FROM gn_empleado e 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                    LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . "
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    $salarioa = 0;
                    for ($e = 0; $e < count($rowe); $e++) {
                        echo '<tr>';
                        echo '<td align="left">' . utf8_decode($rowe[$e][1]) . '</td>';
                        echo '<td align="ritght">' . utf8_decode($rowe[$e][5]) . '</td>';
                        #*** Salario ****#
                        $basico = $con->Listar("SELECT 
                            valor FROM gn_novedad 
                            WHERE empleado = " . $rowe[$e][0] . " 
                            AND concepto = '1' AND periodo = '$periodo'");
                        echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                        $salarioa += $basico[0][0];
                        for ($c = 0; $c < count($rowcn); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                                AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                        }
                        echo '<td></td>';
                        echo '</tr>';
                    }
                    echo '<tr>';
                    echo '<td colspan="2"><strong>Total</strong></td>';
                    echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';

                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                            WHERE c.id_unico = " . $rowcn[$c][2] . " 
                            AND n.periodo = $periodo 
                            AND e.id_unico !=2 
                            AND t.compania = $compania 
                            AND e.unidadejecutora = $unidad 
                            AND e.grupogestion = " . $rowg[$g][0] . " 
                            AND e.id_unico 
                            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor >0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                    }
                    echo '<tr>';
                }

                if ($u != (count($rowu) - 1)) {
                    echo '<tr><td colspan="' . $nc . '">&nbsp;<br/>&nbsp;<br/></td></tr>';
                }
            }
        } else if ($formato == 2) {
            //formato 2
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");
            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';

            #**** Buscar Todas Unidades Ejecutoras del periodo***# 
            $rowu = $con->Listar("SELECT DISTINCT e.unidadejecutora, ue.nombre 
                FROM gn_novedad n 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_unidad_ejecutora ue ON ue.id_unico = e.unidadejecutora 
                WHERE n.periodo = $periodo");
            for ($u = 0; $u < count($rowu); $u++) {
                $unidad = $rowu[$u][0];
                echo '<tr>';
                echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>UNIDAD EJECUTORA: ' . $rowu[$u][1] . '<br/>&nbsp;</i></strong></td>';
                echo '</tr>';
                #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
                $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
                    FROM gn_empleado e 
                    LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
                    WHERE e.unidadejecutora = $unidad");
                for ($g = 0; $g < count($rowg); $g++) {
                    echo '<tr>';
                    echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN:: ' . $rowg[$g][1] . '<br/>&nbsp;</i></strong></td>';
                    echo '</tr>';
                    #**** Numero de conceptos ****#
                    $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo      
                        AND n.valor > 0 
                        AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5)");
                    echo '<tr>';
                    echo '<td><strong>Cédula</strong></td>';
                    echo '<td><strong>Nombre</strong></td>';
                    echo '<td><strong>Básico</strong></td>';
                    #**** Nombre de conceptos clase 1****#
                    $rowcn = $con->Listar("SELECT DISTINCT 
                            n.concepto, 
                            c.descripcion,
                            c.id_unico
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo     
                        AND n.valor > 0 
                        AND (c.clase = 1) 
                        ORDER BY c.clase,c.id_unico");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn); $c++) {
                        echo '<td><strong>' . ucwords(mb_strtolower($rowcn[$c][1])) . '</strong></td>';
                    }

                    #**** Nombre de conceptos clase 3****#
                    $rowcn2 = $con->Listar("SELECT DISTINCT 
                            n.concepto, 
                            c.descripcion,
                            c.id_unico
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo     
                        AND n.valor > 0 
                        AND (c.clase = 3) 
                        ORDER BY c.clase,c.id_unico");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        echo '<td><strong>' . ucwords(mb_strtolower($rowcn2[$c][1])) . '</strong></td>';
                    }

                    #**** Nombre de conceptos clase 2****#
                    $rowcn3 = $con->Listar("SELECT DISTINCT 
                            n.concepto, 
                            c.descripcion,
                            c.id_unico
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo     
                        AND n.valor > 0 
                        AND (c.clase = 2) 
                        ORDER BY c.clase,c.id_unico");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        echo '<td><strong>' . ucwords(mb_strtolower($rowcn3[$c][1])) . '</strong></td>';
                    }

                    #**** Nombre de conceptos clase 4****#
                    $rowcn4 = $con->Listar("SELECT DISTINCT 
                            n.concepto, 
                            c.descripcion,
                            c.id_unico
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo     
                        AND n.valor > 0 
                        AND (c.clase = 4) 
                        ORDER BY c.clase,c.id_unico");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        echo '<td><strong>' . ucwords(mb_strtolower($rowcn4[$c][1])) . '</strong></td>';
                    }

                    #**** Nombre de conceptos clase 5****#
                    $rowcn5 = $con->Listar("SELECT DISTINCT 
                            n.concepto, 
                            c.descripcion,
                            c.id_unico
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo     
                        AND n.valor > 0 
                        AND (c.clase = 5) 
                        ORDER BY c.clase,c.id_unico");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn5); $c++) {
                        echo '<td><strong>' . ucwords(mb_strtolower($rowcn5[$c][1])) . '</strong></td>';
                    }

                    echo '<td><strong>Firma</strong></td>';
                    echo '</tr>';
                    #***************************************************************#
                    #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                    $rowe = $con->Listar(" SELECT DISTINCT  e.id_unico, 
                        t.numeroidentificacion, 
                        e.tercero, 
                        t.id_unico,
                        t.numeroidentificacion, 
                        CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                        ca.salarioactual 
                    FROM gn_empleado e 
                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                    LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                    WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . "
                    AND e.id_unico 
                    IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                    AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
                    $salarioa = 0;
                    for ($e = 0; $e < count($rowe); $e++) {
                        echo '<tr>';
                        echo '<td align="left">' . utf8_decode($rowe[$e][1]) . '</td>';
                        echo '<td align="ritght">' . utf8_decode($rowe[$e][5]) . '</td>';
                        #*** Salario ****#
                        $basico = $con->Listar("SELECT 
                            valor FROM gn_novedad 
                            WHERE empleado = " . $rowe[$e][0] . " 
                            AND concepto = '1' AND periodo = '$periodo'");
                        echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                        $salarioa += $basico[0][0];
                        // valor devengados
                        for ($c = 0; $c < count($rowcn); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                                AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                        }
                        // subtotal devengados
                        for ($c = 0; $c < count($rowcn2); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                                AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                        }
                        // valor descuentos
                        for ($c = 0; $c < count($rowcn3); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                                AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                        }
                        // subtotal descuentos
                        for ($c = 0; $c < count($rowcn4); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                                AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                        }
                        // subtotal neto a pagar
                        for ($c = 0; $c < count($rowcn5); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                                FROM gn_novedad n 
                                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                WHERE c.id_unico = " . $rowcn5[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                                AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                        }
                        echo '<td></td>';
                        echo '</tr>';
                    }
                    echo '<tr>';
                    echo '<td colspan="2"><strong>Total</strong></td>';
                    echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
                    // total devengados
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                            WHERE c.id_unico = " . $rowcn[$c][2] . " 
                            AND n.periodo = $periodo 
                            AND e.id_unico !=2 
                            AND t.compania = $compania 
                            AND e.unidadejecutora = $unidad 
                            AND e.grupogestion = " . $rowg[$g][0] . " 
                            AND e.id_unico 
                            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor >0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                    }
                    //total final devengados
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                            WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                            AND n.periodo = $periodo 
                            AND e.id_unico !=2 
                            AND t.compania = $compania 
                            AND e.unidadejecutora = $unidad 
                            AND e.grupogestion = " . $rowg[$g][0] . " 
                            AND e.id_unico 
                            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor >0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                    }
                    //total descuentos
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                            WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                            AND n.periodo = $periodo 
                            AND e.id_unico !=2 
                            AND t.compania = $compania 
                            AND e.unidadejecutora = $unidad 
                            AND e.grupogestion = " . $rowg[$g][0] . " 
                            AND e.id_unico 
                            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor >0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                    }
                    //total final descuentos
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                            WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                            AND n.periodo = $periodo 
                            AND e.id_unico !=2 
                            AND t.compania = $compania 
                            AND e.unidadejecutora = $unidad 
                            AND e.grupogestion = " . $rowg[$g][0] . " 
                            AND e.id_unico 
                            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor >0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                    }
                    //total final neto a pargar
                    for ($c = 0; $c < count($rowcn5); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                            WHERE c.id_unico = " . $rowcn5[$c][2] . " 
                            AND n.periodo = $periodo 
                            AND e.id_unico !=2 
                            AND t.compania = $compania 
                            AND e.unidadejecutora = $unidad 
                            AND e.grupogestion = " . $rowg[$g][0] . " 
                            AND e.id_unico 
                            IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                            AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor >0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                    }
                    echo '</tr>';
                }

                if ($u != (count($rowu) - 1)) {
                    echo '<tr><td colspan="' . $nc . '">&nbsp;<br/>&nbsp;<br/></td></tr>';
                }
            }
        } else {
            //formato 3
            #**** Numero de conceptos ****#
            $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                WHERE t.compania = $compania 
                AND n.periodo = $periodo      
                AND n.valor > 0 
                AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5)");
            $nc = $ncon[0][0] + 4;
            echo '<th colspan="' . $nc . '" align="center"><strong>';
            echo '<br/>&nbsp;';
            echo '<br/>' . $razonsocial;
            echo '<br/>' . $nombreIdent . ': ' . $numeroIdent;
            echo '<br/>&nbsp;';
            echo '<br/>SÁBANA DE NÓMINA';
            echo '<br/>&nbsp;';
            echo 'NÓMINA:' . $nperiodo;
            echo '<br/>&nbsp;';
            echo '</strong>';
            echo '</th>';
            echo '<tr></tr>  ';

            #**** Buscar Todas Unidades Ejecutoras del periodo***# 
            $rowu = $con->Listar("SELECT DISTINCT e.unidadejecutora, ue.nombre 
                FROM gn_novedad n 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gn_unidad_ejecutora ue ON ue.id_unico = e.unidadejecutora 
                WHERE n.periodo = $periodo");
            for ($u = 0; $u < count($rowu); $u++) {
                $unidad = $rowu[$u][0];
                echo '<tr>';
                echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>UNIDAD EJECUTORA: ' . $rowu[$u][1] . '<br/>&nbsp;</i></strong></td>';
                echo '</tr>';
                #**** Buscar Grupos de Gestión De La unidad ejecutora id****#
                $rowg = $con->Listar("SELECT DISTINCT e.grupogestion, gg.nombre 
                    FROM gn_empleado e 
                    LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico 
                    WHERE e.unidadejecutora = $unidad");
                for ($g = 0; $g < count($rowg); $g++) {
                    echo '<tr>';
                    echo '<td colspan="' . $nc . '"><strong><i>&nbsp;<br/>GRUPO DE GESTIÓN:: ' . $rowg[$g][1] . '<br/>&nbsp;</i></strong></td>';
                    echo '</tr>';
                    #**** Numero de conceptos ****#
                    $ncon = $con->Listar("SELECT COUNT(DISTINCT n.concepto) 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo      
                        AND n.valor > 0 
                        AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5)");
                    echo '<tr>';
                    echo '<td><strong>Cédula</strong></td>';
                    echo '<td><strong>Nombre</strong></td>';
                    echo '<td><strong>Básico</strong></td>';
                    #**** Nombre de conceptos clase 6****#
                    $rowcn = $con->Listar("SELECT DISTINCT 
                        n.concepto, 
                        c.descripcion,
                        c.id_unico
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                    WHERE t.compania = $compania 
                    AND e.unidadejecutora = $unidad 
                    AND e.grupogestion = " . $rowg[$g][0] . " 
                    AND n.periodo = $periodo     
                    AND n.valor > 0 
                    AND c.clase = 6");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn); $c++) {
                        echo '<td><strong>' . ucwords(mb_strtolower($rowcn[$c][1])) . '</strong></td>';
                    }

                    #**** Nombre de conceptos clase 1****#
                    $rowcn2 = $con->Listar("SELECT DISTINCT 
                            n.concepto, 
                            c.descripcion,
                            c.id_unico
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo     
                        AND n.valor > 0 
                        AND (c.clase = 1) 
                        ORDER BY c.clase,c.id_unico");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        echo '<td><strong>' . ucwords(mb_strtolower($rowcn2[$c][1])) . '</strong></td>';
                    }

                    #**** Nombre de conceptos clase 2****#
                    $rowcn3 = $con->Listar("SELECT DISTINCT 
                            n.concepto, 
                            c.descripcion,
                            c.id_unico
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo     
                        AND n.valor > 0 
                        AND (c.clase = 2) 
                        ORDER BY c.clase,c.id_unico");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        echo '<td><strong>' . ucwords(mb_strtolower($rowcn3[$c][1])) . '</strong></td>';
                    }

                    #**** Nombre de conceptos clase 5****#
                    $rowcn4 = $con->Listar("SELECT DISTINCT 
                            n.concepto, 
                            c.descripcion,
                            c.id_unico
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                        WHERE t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND n.periodo = $periodo     
                        AND n.valor > 0 
                        AND (c.clase = 5) 
                        ORDER BY c.clase,c.id_unico");
                    #*** Titulos ***#
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        echo '<td><strong>' . ucwords(mb_strtolower($rowcn4[$c][1])) . '</strong></td>';
                    }

                    echo '<td><strong>Firma</strong></td>';
                    echo '</tr>';
                    #***************************************************************#
                    #**** Buscar Terceros de Grupo de Gestion y unidad Ejecutora ***#
                    $rowe = $con->Listar(" SELECT DISTINCT  e.id_unico, 
                    t.numeroidentificacion, 
                    e.tercero, 
                    t.id_unico,
                    t.numeroidentificacion, 
                    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
                    ca.salarioactual 
                FROM gn_empleado e 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                WHERE e.id_unico !=2 AND compania = $compania AND e.unidadejecutora = $unidad 
                AND e.grupogestion = " . $rowg[$g][0] . " 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 6 OR c.clase = 1 OR c.clase = 2 OR c.clase = 5) AND n.valor>0)");
                    $salarioa = 0;
                    for ($e = 0; $e < count($rowe); $e++) {
                        echo '<tr>';
                        echo '<td align="left">' . utf8_decode($rowe[$e][1]) . '</td>';
                        echo '<td align="ritght">' . utf8_decode($rowe[$e][5]) . '</td>';
                        #*** Salario ****#
                        $basico = $con->Listar("SELECT 
                            valor FROM gn_novedad 
                            WHERE empleado = " . $rowe[$e][0] . " 
                            AND concepto = '1' AND periodo = '$periodo'");
                        echo '<td>' . number_format($basico[0][0], 0, '.', ',') . '</td>';
                        $salarioa += $basico[0][0];
                        //valor de clase concepto 6
                        for ($c = 0; $c < count($rowcn); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                        }
                        //valor de clase concepto 1
                        for ($c = 0; $c < count($rowcn2); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn2[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                        }
                        //valor de clase concepto 2
                        for ($c = 0; $c < count($rowcn3); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn3[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                        }
                        //valor de clase concepto 5
                        for ($c = 0; $c < count($rowcn4); $c++) {
                            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                            FROM gn_novedad n 
                            LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            WHERE c.id_unico = " . $rowcn4[$c][2] . " AND e.id_unico = " . $rowe[$e][0] . " 
                            AND n.periodo = $periodo ");
                            if ($num_con[0][1] > 0) {
                                $valor = $num_con[0][1];
                            } else {
                                $valor = 0;
                            }
                            echo '<td align="ritght">' . number_format($valor, 0, '.', ',') . '</td>';
                        }
                        echo '<td></td>';
                        echo '</tr>';
                    }
                    echo '<tr>';
                    echo '<td colspan="2"><strong>Total</strong></td>';
                    echo '<td><strong>' . number_format($salarioa, 0, '.', ',') . '</strong></td>';
                    // total clase concepto 6
                    for ($c = 0; $c < count($rowcn); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 6) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                    }
                    // total clase concepto 1
                    for ($c = 0; $c < count($rowcn2); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn2[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 1) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                    }
                    // total clase concepto 2
                    for ($c = 0; $c < count($rowcn3); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn3[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 2) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                    }
                    // total clase concepto 5
                    for ($c = 0; $c < count($rowcn4); $c++) {
                        $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                        FROM gn_novedad n 
                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                        WHERE c.id_unico = " . $rowcn4[$c][2] . " 
                        AND n.periodo = $periodo 
                        AND e.id_unico !=2 
                        AND t.compania = $compania 
                        AND e.unidadejecutora = $unidad 
                        AND e.grupogestion = " . $rowg[$g][0] . " 
                        AND e.id_unico 
                        IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                        AND (c.clase = 5) AND n.valor>0)");
                        if ($num_con[0][1] > 0) {
                            $valor = $num_con[0][1];
                        } else {
                            $valor = 0;
                        }
                        echo '<td><strong>' . number_format($valor, 0, '.', ',') . '</strong></td>';
                    }
                    echo '</tr>';
                }

                if ($u != (count($rowu) - 1)) {
                    echo '<tr><td colspan="' . $nc . '">&nbsp;<br/>&nbsp;<br/></td></tr>';
                }
            }
        }
    }
}
?>