<?php 
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
session_start();
ob_start();
$con            = new ConexionPDO();
$compania       = $_SESSION['compania'];
$calendario     = CAL_GREGORIAN;
$parmanno       = $mysqli->real_escape_string('' . $_POST['sltAnnio'] . '');
$anno           = anno($parmanno);
$mesI           = $mysqli->real_escape_string('' . $_POST['sltmesi'] . '');
$diaI           = '01';
$fechaInicial   = $anno . '-' . $mesI . '-' . $diaI;
$mesF           = $mysqli->real_escape_string('' . $_POST['sltmesf'] . '');
$diaF           = cal_days_in_month($calendario, $mesF, $anno);
$fechaFinal     = $anno . '-' . $mesF . '-' . $diaF;
$fechaComparar  = $anno . '-' . '01-01';
$codigoI        = $mysqli->real_escape_string('' . $_POST['sltcodi'] . '');
$codigoF        = $mysqli->real_escape_string('' . $_POST['sltcodf'] . '');
$bl             = generarBalance($anno, $parmanno, $fechaInicial, $fechaFinal, $codigoI, $codigoF, $compania, 1);

$window = $_GET['window'];
$compania = $_SESSION['compania'];
switch ($window) {
    case 'excel':
        header("Content-Disposition: attachment; filename=Informe_Balance_cgn.xls");    //Salida y nombre del informe en excel
        $sqlC = "SELECT ter.id_unico,
            ter.razonsocial,
            UPPER(ti.nombre),
            ter.numeroidentificacion,
            dir.direccion,
            tel.valor,
            ter.ruta_logo
        FROM 		gf_tercero ter
        LEFT JOIN 	gf_tipo_identificacion ti 	ON 	ter.tipoidentificacion = ti.id_unico
        LEFT JOIN       gf_direccion dir 			ON	dir.tercero = ter.id_unico
        LEFT JOIN 	gf_telefono  tel 			ON 	tel.tercero = ter.id_unico
        WHERE 		ter.id_unico = $compania";
        $resultC = $mysqli->query($sqlC);
        $rowC = mysqli_fetch_row($resultC);
        
        ##################################################################################################################################################
        $razonsocial = $rowC[1];
        $nombreIdent = $rowC[2];
        $numeroIdent = $rowC[3];
        $direccinTer = $rowC[4];
        $telefonoTer = $rowC[5];
        $ruta_logo = $rowC[6];
        ####################################################################################################################################################
        #Impresión de html
        #####################################################################################################################################################
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
        echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
        echo "<head>";
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
        echo "<title>BALANCE CGN</title>";
        echo "</head>";
        echo "<body>";
        echo "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
        echo "<thead>";
        echo "<tr>";
        echo "<td colspan=\"8\" align=\"center\"><strong>" . $razonsocial . "</strong><br/><strong>" . $nombreIdent . PHP_EOL . "</strong>:" . PHP_EOL . $numeroIdent . "<br/>" . $direccinTer . PHP_EOL . "<strong>Tel:</strong>" . PHP_EOL . $telefonoTer . "</td>";
        echo "</tr>";
        echo "</tr>";
        echo "<tr>";
        echo "<td colspan=\"8\"><CENTER><strong>";
        $tit1 = $_POST['tituloH'];
        if (empty($tit1) || $tit1 == "") {
            echo 'CGN2015_001_SALDOS_Y_MOVIMIENTOS_CONVERGENCIA';
        } else {
            echo 'CGN2015_001_SALDOS_Y_MOVIMIENTOS_CONVERGENCIA';
        }
        echo "</strong></CENTER>";
        echo "</td>";
        echo "<tr>";
        echo "<td rowspan=\"2\"><center><strong>CODIGO</strong></center></td>";
        echo "<td rowspan=\"2\"><center><strong>NOMBRE</strong></center></td>";
        echo "<td rowspan=\"2\"><center><strong>SALDO INICIAL</strong></center></td>";
        echo "<td colspan=\"2\"><center><strong>VALOR</strong></center></td>";
        echo "<td rowspan=\"2\"><center><strong>SALDO FINAL</strong></center></td>";
        echo "<td rowspan=\"2\"><center><strong>SALDO FINAL CORRIENTE</strong></center></td>";
        echo "<td rowspan=\"2\"><center><strong>SALDO FINAL NO CORRIENTE</strong></center></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td><center><strong>DÉBITO</strong></center></td>";
        echo "<td><center><strong>CRÉDITO</strong></center></td>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        #Consulta Cuentas
        $sald = 0;
        $debit = 0;
        $credit = 0;
        $nsald = 0;
        $corriente = 0;
        $nocorriente = 0;

        $totaldeb = 0;
        $totalcred = 0;
        $totalsaldoI = 0;
        $totalsaldoF = 0;
        $totalcorriente = 0;
        $totalNoCorriente = 0;
        $sql3 = "SELECT DISTINCT 
            numero_cuenta 			as numcuen, 
            nombre          		as cnom,
            (saldo_inicial)   	as salini,
            (debito)          	as deb,
            (credito)         	as cred,
            (nuevo_saldo)     	as nsal
        from temporal_balance$compania  
        WHERE saldo_inicial IS NOT NULL AND debito IS NOT NULL AND credito IS NOT NULL AND nuevo_saldo IS NOT NULL
        ORDER BY numero_cuenta ASC";
        $ccuentas = $mysqli->query($sql3);
        while ($filactas = mysqli_fetch_array($ccuentas)) {
            $sald = (float) ($filactas['salini']);
            $debit = (float) ($filactas['deb']);
            $credit = (float) ($filactas['cred']);
            $nsald = (float) ($filactas['nsal']);
            if ($sald == 0 && $debit == 0 && $credit == 0) {
                
            } else {
                if (strlen($filactas['numcuen']) <= 6) { #Validamos para que solo imprima 6 digitos
                    $numc = $filactas['numcuen'];
                    $nomc = $filactas['cnom'];

                    $sqlC = "SELECT 	cta.tipocuentacgn			    		
                    FROM 		gf_cuenta cta
                    LEFT JOIN	gf_detalle_comprobante dtc ON dtc.cuenta = cta.id_unico
                    WHERE 		cta.codi_cuenta = '$numc'		    			
                    GROUP BY 	cta.id_unico";
                    $resultC = $mysqli->query($sqlC);
                    $rowCC = mysqli_fetch_row($resultC);

                    if ($rowCC[0] == NULL) {
                        $corriente = 0;
                        $nocorriente = 0;
                    } else if ($rowCC[0] == 2) {
                        $corriente = $nsald;
                        $nocorriente = 0;
                    } else if ($rowCC[0] == 6) {
                        $nocorriente = $nsald;
                        $corriente = 0;
                    }
                    //Validación por tamaño de string
                    echo "<tr>";
                    if (strlen($filactas['numcuen']) == 1) {
                        echo "<td>" . $filactas['numcuen'] . "</td>";
                    } else if (strlen($filactas['numcuen']) == 2) {
                        $y = chunk_split($filactas['numcuen'], 1, ".");
                        $y = substr($y, 0, strlen($y) - 1);
                        echo "<td>" . $y . "</td>";
                    } else if (strlen($filactas['numcuen']) == 4) {
                        $y = chunk_split($filactas['numcuen'], 1, ".");
                        $y = substr($y, 0, strlen($y) - 1);
                        $y = explode(".", $y);
                        echo "<td>" . $y[0] . "." . $y[1] . "." . $y[2] . $y[3] . "</td>";
                    } else if (strlen($filactas['numcuen']) == 6) {
                        $y = chunk_split($filactas['numcuen'], 1, ".");
                        $y = substr($y, 0, strlen($y) - 1);
                        $y = explode(".", $y);
                        echo "<td>" . $y[0] . "." . $y[1] . "." . $y[2] . $y[3] . "." . $y[4] . $y[5] . "</td>";
                    }
                    echo "<td>" . ucwords(mb_strtolower($filactas['cnom'])) . "</td>";
                    echo "<td>" . number_format(round($sald), 2, '.', ',') . "</td>";
                    echo "<td>" . number_format(round($debit), 2, '.', ',') . "</td>";
                    echo "<td>" . number_format(round($credit), 2, '.', ',') . "</td>";
                    echo "<td>" . number_format(round($nsald), 2, '.', ',') . "</td>";
                    echo "<td>" . number_format(round($corriente), 2, '.', ',') . "</td>";
                    echo "<td>" . number_format(round($nocorriente), 2, '.', ',') . "</td>";
                    echo "</tr>";
                }
            }
        }
        echo "</tbody>";
        echo "<tfoot>";
        echo "</tfoot>";
        echo "</table>";
        break;
    case 'pdf':
        require'../fpdf/fpdf.php';
        ob_start();

        class PDF extends FPDF {

            // Cabecera de página  
            function Header() {
                global $nomcomp;
                global $tipodoc;
                global $numdoc;
                global $month1;
                global $month2;
                global $anno;
                global $ruta;
                $this->SetFont('Arial', 'B', 10);
                // Título
                $this->SetY(10);
                if ($ruta != '') {
                    $this->Image('../' . $ruta, 60, 6, 20);
                }
                $this->Cell(330, 5, utf8_decode($nomcomp), 0, 0, 'C');
                // Salto de línea
                $this->setX(10);
                $this->SetFont('Arial', 'B', 8);
                $this->Cell(330, 10, utf8_decode('CÓDIGO SGC'), 0, 0, 'R');
                $this->Ln(5);
                $this->SetFont('Arial', '', 8);
                $this->Cell(330, 5, $tipodoc . ': ' . $numdoc, 0, 0, 'C');
                $this->SetFont('Arial', 'B', 8);
                $this->SetX(10);
                $this->Cell(330, 10, utf8_decode('VERSIÓN SGC'), 0, 0, 'R');
                $this->Ln(5);
                $this->SetFont('Arial', '', 8);
                $tit1 = $_POST['tituloH'];
                $this->Cell(330, 5, utf8_decode('CGN2015_001_SALDOS_Y_MOVIMIENTOS_CONVERGENCIA'), 0, 0, 'C');

                $this->SetFont('Arial', 'B', 8);
                $this->SetX(10);
                $this->Cell(330, 10, utf8_decode('FECHA SGC'), 0, 0, 'R');
                $this->Ln(3);
                $this->SetFont('Arial', '', 7);
                $this->Cell(332, 5, utf8_decode('Entre ' . $month1 . ' y ' . $month2 . ' de ' . $anno), 0, 0, 'C');
                $this->Ln(5);
                $this->Cell(20, 10, utf8_decode(''), 1, 0, 'C');
                $this->Cell(94, 10, utf8_decode(''), 1, 0, 'C');
                $this->Cell(35, 10, utf8_decode(''), 1, 0, 'C');
                $this->Cell(70, 10, utf8_decode(''), 1, 0, 'C');
                $this->Cell(35, 10, utf8_decode(''), 1, 0, 'C');
                $this->Cell(35, 10, utf8_decode(''), 1, 0, 'C');
                $this->Cell(35, 10, utf8_decode(''), 1, 0, 'C');
                $this->Setx(10);
                $this->Cell(20, 10, utf8_decode('Código'), 0, 0, 'C');
                $this->Cell(94, 10, utf8_decode('Nombre'), 0, 0, 'C');
                $this->Cell(35, 10, utf8_decode('Saldo Inicial'), 0, 0, 'C');
                $this->Cell(70, 5, utf8_decode('Valor'), 0, 0, 'C');
                $this->Cell(35, 10, utf8_decode('Saldo Final'), 0, 0, 'C');
                $this->Cell(35, 10, utf8_decode('Saldo Final Corriente'), 1, 0, 'C');
                $this->Cell(35, 10, utf8_decode('Saldo Final No Corriente'), 1, 0, 'C');
                $this->Ln(5);
                $this->Cell(20, 5, utf8_decode(''), 0, 0, 'C');
                $this->Cell(94, 5, utf8_decode(''), 0, 0, 'C');
                $this->Cell(35, 5, utf8_decode(''), 0, 0, 'C');
                $this->Cell(35, 5, utf8_decode('Débito'), 1, 0, 'C');
                $this->Cell(35, 5, utf8_decode('Crédito'), 1, 0, 'C');
                $this->Cell(35, 5, utf8_decode(''), 0, 0, 'C');
                $this->Cell(35, 10, utf8_decode(''), 0, 0, 'C');
                $this->Ln(5);
                $this->Cell(326, 5, '', 0);
            }

            function Footer() {
                // Posición: a 1,5 cm del final
                $this->SetY(-15);
                // Arial italic 8
                $this->SetFont('Arial', 'B', 8);
                // Número de página
                $this->Cell(15);
                $this->Cell(25, 10, utf8_decode('Fecha: ' . date('d-m-Y')), 0, 0, 'L');
                $this->Cell(70);
                $this->Cell(35, 10, utf8_decode('Máquina: ' . gethostname()), 0);
                $this->Cell(60);
                $this->Cell(30, 10, utf8_decode('Usuario: admin'), 0); //.get_current_user()
                $this->Cell(70);
                $this->Cell(0, 10, utf8_decode('Pagina ' . $this->PageNo() . '/{nb}'), 0, 0);
            }

        }

        // Creación del objeto de la clase heredada
        $pdf = new PDF('L', 'mm', 'Legal');

        //Asingación de valor a Mes 1    
        switch ($mesI) {
            case 1:
                $month1 = "Enero";
                break;
            case 2:
                $month1 = "Febrero";
                break;
            case 3:
                $month1 = "Marzo";
                break;
            case 4:
                $month1 = "Abril";
                break;
            case 5:
                $month1 = "Mayo";
                break;
            case 6:
                $month1 = "Junio";
                break;
            case 7:
                $month1 = "Julio";
                break;
            case 8:
                $month1 = "Agosto";
                break;
            case 9:
                $month1 = "Septiembre";
                break;
            case 10:
                $month1 = "Octubre";
                break;
            case 11:
                $month1 = "Noviembre";
                break;
            case 12:
                $month1 = "Diciembre";
                break;
        }
        //Asingación de valor a Mes 2        
        switch ($mesF) {
            case 1:
                $month2 = "Enero";
                break;
            case 2:
                $month2 = "Febrero";
                break;
            case 3:
                $month2 = "Marzo";
                break;
            case 4:
                $month2 = "Abril";
                break;
            case 5:
                $month2 = "Mayo";
                break;
            case 6:
                $month2 = "Junio";
                break;
            case 7:
                $month2 = "Julio";
                break;
            case 8:
                $month2 = "Agosto";
                break;
            case 9:
                $month2 = "Septiembre";
                break;
            case 10:
                $month2 = "Octubre";
                break;
            case 11:
                $month2 = "Noviembre";
                break;
            case 12:
                $month2 = "Diciembre";
                break;
        }

        #Igualación de Variable local a POST
        $annio = $anno;

        #Consulta Compañía para Encabezado
        $compania = $_SESSION['compania'];

        $consulta = "SELECT         t.razonsocial as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum, t.ruta_logo as ruta 
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

        $cmp = $mysqli->query($consulta);

        #Consulta para obtener parámetros Header
        $fila = mysqli_fetch_array($cmp);
        $nomcomp = ($fila['traz']);
        $tipodoc = ($fila['tnom']);
        $numdoc = ($fila['tnum']);
        $ruta = ($fila['ruta']);

        $sqlRutaLogo = 'SELECT ter.ruta_logo, ciu.nombre 
	  FROM gf_tercero ter 
	  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
	  WHERE ter.id_unico = ' . $compania;
        $rutaLogo = $mysqli->query($sqlRutaLogo);
        $rowLogo = mysqli_fetch_array($rutaLogo);
        $ruta = $rowLogo[0];


        #Declaración Variable Número de Páginas
        $nb = $pdf->AliasNbPages();

        $saldoT = 0;
        #Fin Consulta Secundaria
        #Creación Objeto FPDF
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', '', 8);

        $codd = 0;
        $totales = 0;
        $valorA = 0;

        #Variables de valor de naturaleza
        $debito = "";
        $credito = "";
        $totaldeb = 0.00;
        $totalcred = 0.00;

        $saldoT = 0;
        $saldoTT = 0;

        //$pdf->setY(37);
        $pdf->SetY(39);

        $cnt = 0;
        #Consulta Cuentas
        $sql3 = "SELECT DISTINCT 
            numero_cuenta   		as numcuen, 
            nombre          		as cnom,
            (saldo_inicial)   	as salini,
            (debito)          	as deb,
            (credito)         	as cred,
            (nuevo_saldo)     	as nsal,
            id_cuenta				as id_c
        FROM 			temporal_balance$compania   
        WHERE 			saldo_inicial 	IS NOT NULL 
        AND 			debito 			IS NOT NULL 
        AND 			credito 		IS NOT NULL 
        AND				nuevo_saldo 	IS NOT NULL
        ORDER BY 		numero_cuenta 	ASC";

        $ccuentas = $mysqli->query($sql3);

        $sald = 0;
        $debit = 0;
        $credit = 0;
        $nsald = 0;
        $corriente = 0;
        $nocorriente = 0;

        $totaldeb = 0;
        $totalcred = 0;
        $totalsaldoI = 0;
        $totalsaldoF = 0;
        $totalcorriente = 0;
        $totalNoCorriente = 0;
        while ($filactas = mysqli_fetch_array($ccuentas)) {
            if (strlen($filactas['numcuen']) <= 6) { #Validamos para que solo imprima 6 digitos
                # $codd = $codd + 1;	    
                $sald = (float) ($filactas['salini']);
                $debit = (float) ($filactas['deb']);
                $credit = (float) ($filactas['cred']);
                $nsald = (float) ($filactas['nsal']);
                if ($sald == 0 && $debit == 0 && $credit == 0) {
                    
                } else {
                    $numc = $filactas['id_c'];
                    $sqlC = "SELECT 	cta.tipocuentacgn		    						
		    			FROM 		gf_cuenta cta
		    			LEFT JOIN	gf_detalle_comprobante dtc ON dtc.cuenta = cta.id_unico
		    			WHERE 		cta.id_unico=$numc";
                    $resultC = $mysqli->query($sqlC);
                    $rowCC = mysqli_fetch_row($resultC);

                    if ($rowCC[0] == NULL) {
                        $corriente = 0;
                        $nocorriente = 0;
                    } else if ($rowCC[0] == 2) {
                        $corriente = $nsald;
                        //$corriente = $sald;
                        $nocorriente = 0;
                    } else if ($rowCC[0] == 6) {
                        $nocorriente = $nsald;
                        //$nocorriente = $sald;
                        $corriente = 0;
                    }

                    $pdf->Cell(20, 4, utf8_decode($filactas['numcuen']), 0, 0, 'L');
                    $y = $pdf->GetY();
                    $x = $pdf->GetX();
                    $pdf->MultiCell(94, 4, utf8_decode(ucwords(mb_strtolower($filactas['cnom']))), 0, 'L');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    $px = $x + 94;
                    $pdf->Ln(-$h);
                    $pdf->SetX($px);
                    //$pdf->Cell(47,4,utf8_decode($filactas['numcuen']),0,0,'L');

                    $pdf->Cell(35, 4, number_format(round($sald), 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(35, 4, number_format(round($debit), 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(35, 4, number_format(round($credit), 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(35, 4, number_format(round($nsald), 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(35, 4, number_format(round($corriente), 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(35, 4, number_format(round($nocorriente), 2, '.', ','), 0, 0, 'R');
                    $pdf->Ln($h);

                    $totalsaldoI = $totalsaldoI + $sald;
                    $totaldeb = $totaldeb + $debit;
                    $totalcred = $totalcred + $credit;
                    $totalsaldoF = $totalsaldoF + $nsald;
                    $totalcorriente += $corriente;
                    $totalNoCorriente += $nocorriente;
                }
            }
        }
        ################################ ESTRUCTURA FIRMAS ##########################################
        ######### BUSQUEDA RESPONSABLE #########
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Ln(10);
        $compania = $_SESSION['compania'];
        $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        WHERE LOWER(td.nombre) ='balance de prueba' AND td.compania = $compania  ORDER BY rd.orden ASC";
        $res = $mysqli->query($res);
        $i = 0;
        $x = 130;
        #ESTRUCTURA
        if (mysqli_num_rows($res) > 0) {
            $h = 4;
            while ($row2 = mysqli_fetch_row($res)) {

                $ter = "SELECT IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) AS NOMBREC, "
                        . "tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional "
                        . "FROM gf_tercero tr "
                        . "LEFT JOIN gf_cargo_tercero ct ON tr.id_unico = ct.tercero "
                        . "LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico "
                        . "WHERE tr.id_unico ='$row2[0]'";

                $ter = $mysqli->query($ter);
                $ter = mysqli_fetch_row($ter);
                if (!empty($ter[3])) {
                    $responsable = "\n\n___________________________________ \n" . (mb_strtoupper($ter[0])) . "\n" . mb_strtoupper($ter[2]) . "\n T.P:" . (mb_strtoupper($ter[3]));
                } else {
                    $responsable = "\n\n___________________________________ \n" . (mb_strtoupper($ter[0])) . "\n" . mb_strtoupper($ter[2]) . "\n";
                }

                $pdf->MultiCell(110, 4, utf8_decode($responsable), 0, 'L');

                if ($i == 1) {
                    $pdf->Ln(15);
                    $x = 130;
                    $i = 0;
                } else {
                    $pdf->Ln(-25);
                    $pdf->SetX($x);
                    $x = $x + 110;
                    $i = $i + 1;
                }
            }
        }

        while (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output(0, utf8_decode('CGN2015_001_SALDOS_Y_MOVIMIENTOS_CONVERGENCIA(' . date('d-m-Y') . ').pdf'), 0);
        break;
    case 'txt':
        header("Content-type: text/plain; charset=UTF-8");
        header("Content-type: application/force-download");
        header("Content-Disposition: attachment; filename=Informe_Balance_cng.txt");
        $compania = $_SESSION['compania'];
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Consultamos el valor codigo_dane para obtener el codigo de la compañia
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sqlCompania = "SELECT codigo_dane FROM gf_tercero WHERE id_unico = $compania";
        $resultCompania = $mysqli->query($sqlCompania);
        $rowCompania = mysqli_fetch_row($resultCompania);
        $codigo_entidad = $rowCompania[0];
        $filas = '';
        $filas = trim($filas, ' ');
        $filas = 'S' . "\t" . $codigo_entidad . "\t" . $mesI . $mesF . "\t" . $anno . "\tCGN2015_001_SALDOS_Y_MOVIMIENTOS_CONVERGENCIA\t" . date('d-m-Y') . "\n";
        #$filas = str_replace($filas," ","");
        $sql3 = "SELECT DISTINCT
								'D' as 'D',
		                        numero_cuenta   		as numcuen, 
		                        nombre          		as cnom,
		                        (saldo_inicial)   	as salini,
		                        (debito) 	        as deb,
		                        (credito)          as cred,
		                        (nuevo_saldo)      as nsal,
		                        id_cuenta 				as id_c
		FROM temporal_balance$compania   
		WHERE saldo_inicial IS NOT NULL AND debito IS NOT NULL AND credito IS NOT NULL AND nuevo_saldo IS NOT NULL
		ORDER BY numero_cuenta ASC";
        $ccuentas = $mysqli->query($sql3);
        while ($filactas = mysqli_fetch_array($ccuentas)) {
            $sald = (float) ($filactas['salini']);
            $debit = (float) ($filactas['deb']);
            $credit = (float) ($filactas['cred']);
            $nsald = (float) ($filactas['nsal']);
            if ($sald == 0 && $debit == 0 && $credit == 0) {
                
            } else {
                if (strlen($filactas['numcuen']) <= 6) { #Validamos para que solo imprima 6 digitos
                    $numc = $filactas['id_c'];

                    $sqlC = "SELECT 	cta.tipocuentacgn
			    			FROM 		gf_cuenta cta
			    			LEFT JOIN	gf_detalle_comprobante dtc ON dtc.cuenta = cta.id_unico
			    			WHERE 		cta.id_unico = '$numc'		    			
			    			GROUP BY 	cta.id_unico";
                    $resultC = $mysqli->query($sqlC);
                    $rowCC = mysqli_fetch_row($resultC);

                    if ($rowCC[0] == NULL) {
                        $corriente = 0;
                        $nocorriente = 0;
                    } else if ($rowCC[0] == 2) {
                        $corriente = $nsald;
                        $nocorriente = 0;
                    } else if ($rowCC[0] == 6) {
                        $nocorriente = $nsald;
                        $corriente = 0;
                    }
                    //Validación por tamaño de string, e imprimimos solo cuando el string tiene solo 1 de tamaño
                    if (strlen($filactas['numcuen']) == 1) {
                        //Captura de variables en el string $filas
                        $filas .= "D\t" . $filactas['numcuen'] . "\t" . round($sald) . "\t" . round($debit) . "\t" . round($credit) . "\t" . round($nsald) . "\t" . round($corriente) . "\t" . round($nocorriente) . "\n";
                        //Si el tamaño del string es igual a 2, e imprimimos el formato 1.2
                    } else if (strlen($filactas['numcuen']) == 2) {
                        $y = chunk_split($filactas['numcuen'], 1, "."); //Agrega puntos al string
                        $y = substr($y, 0, strlen($y) - 1);     //Quitamos punto final al string
                        $filas .= "D\t" . $y . "\t" . round($sald) . "\t" . round($debit) . "\t" . round($credit) . "\t" . round($nsald) . "\t" . round($corriente) . "\t" . round($nocorriente) . "\n";
                        //Si el tamaño del string es igual a 4, e imprimimos el formato 1.2.34
                    } else if (strlen($filactas['numcuen']) == 4) {
                        $y = chunk_split($filactas['numcuen'], 1, "."); //Agregamos puntos al string
                        $y = substr($y, 0, strlen($y) - 1);     //Quitamos punto al final del string
                        $y = explode(".", $y);       //Convertimos en un array
                        $filas .= "D\t" . $y[0] . "." . $y[1] . "." . $y[2] . $y[3] . "\t" . round($sald) . "\t" . round($debit) . "\t" . round($credit) . "\t" . round($nsald) . "\t" . round($corriente) . "\t" . round($nocorriente) . "\n";
                        //Si el tamaño del string es igual a 6, e imprimimos el formato 1.2.34.56
                    } else if (strlen($filactas['numcuen']) == 6) {
                        $y = chunk_split($filactas['numcuen'], 1, "."); //Agregamos punto al string
                        $y = substr($y, 0, strlen($y) - 1);     //Quitamos punto al final del string
                        $y = explode(".", $y);       //Convertimos en un array
                        $filas .= "D\t" . $y[0] . "." . $y[1] . "." . $y[2] . $y[3] . "." . $y[4] . $y[5] . "\t" . round($sald) . "\t" . round($debit) . "\t" . round($credit) . "\t" . round($nsald) . "\t" . round($corriente) . "\t" . round($nocorriente) . "\n";
                    }
                }
            }
        }
        //Impresión de filas
        echo ltrim($filas);
        break;
}
?>