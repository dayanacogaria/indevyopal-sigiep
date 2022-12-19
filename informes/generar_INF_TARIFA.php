<?php

require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 360);
?>



<?php
                //Fin Conversión Fecha / Hora
                $hoy = date('d-m-Y');
                $hoy = trim($hoy, '"');
                $fecha_div = explode("-", $hoy);
                $anioh = $fecha_div[2];
                $mesh = $fecha_div[1];
                $diah = $fecha_div[0];
                $hoy = $diah.'/'.$mesh.'/'.$anioh;

                class PDF extends FPDF
                {  
                // Cabecera de página
                    function Header()
                    {

                        // Logo
                        //$this->Image('logo_pb.png',10,8,33);
                        //Arial bold 15
                        global $nomcomp;
                        global $tipodoc;
                        global $numdoc;
    
                        global $fecha1;
                        global $fecha2;
                        global $numpaginas;
                        $numpaginas=$numpaginas+1;

                        $this->SetFont('Arial','B',10);
                        //$this->Ln(1);
                        // Título
                        $this->SetY(10);
                        //$this->image('../LOGOABC.png', 20,10,20,15,'PNG');
                        //$pdf->SetFillColor(232,232,232);

                        $this->SetX(25);
                        $this->Cell(315,5,utf8_decode($nomcomp),0,0,'C');
                        // Salto de línea
                        $this->setX(25);
                        $this->SetFont('Arial','B',8);
                        $this->Cell(315,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

                        $this->Ln(5);

                        $this->SetFont('Arial','',8);
                        $this->SetX(25);
                        $this->Cell(315, 5,$tipodoc.': '.$numdoc,0,0,'C');
                        $this->SetFont('Arial','B',8);
                        $this->SetX(25);
                        $this->Cell(315,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

                        $this->Ln(5);

                        $this->SetFont('Arial','',8);
                        $this->SetX(25);
                        $this->Cell(315,5,utf8_decode('INFORME DE TARIFA'),0,0,'C');
                        $this->SetFont('Arial','B',8);
                        $this->SetX(25);
                        $this->Cell(315,10,utf8_decode('FECHA SGC'),0,0,'R');

                        $this->Ln(12);

                 
                        $this->SetFont('Arial','B',8);
                        $this->SetX(1);


                        $this->Ln(5);
                        $this->SetX(1.8);
                        $this->Cell(26,10,utf8_decode(''),1,0,'C');
                        $this->Cell(17,10,utf8_decode(''),1,0,'C');
                        $this->Cell(22,10,utf8_decode(''),1,0,'C');
                        $this->Cell(22,10,utf8_decode(''),1,0,'C');
                        $this->Cell(22,10,utf8_decode(''),1,0,'C');
                        $this->Cell(17,10,utf8_decode(''),1,0,'C');
                        $this->Cell(22,10,utf8_decode(''),1,0,'C');
                        $this->Cell(17,10,utf8_decode(''),1,0,'C');
                        $this->Cell(17,10,utf8_decode(''),1,0,'C');
                        $this->Cell(22,10,utf8_decode(''),1,0,'C');
                        $this->Cell(22,10,utf8_decode(''),1,0,'C');
                        $this->Cell(22,10,utf8_decode(''),1,0,'C');
                        $this->Cell(26,10,utf8_decode(''),1,0,'C');
                        $this->Cell(26,10,utf8_decode(''),1,0,'C');
                        $this->Cell(26,10,utf8_decode(''),1,0,'C');
                        $this->Cell(26,10,utf8_decode(''),1,0,'C');


                        $this->SetX(1.8);
                        $this->SetFont('Arial','B',8);
                        $this->Cell(26,9,utf8_decode('Nombre'),0,0,'C');
                        $this->Cell(17,9,utf8_decode('Año'),0,0,'C');
                        $this->Cell(22,9,utf8_decode('Código'),0,0,'C');
                        $this->Cell(22,9,utf8_decode('Límite'),0,0,'C');
                        $this->Cell(22,9,utf8_decode('Límite'),0,0,'C');
                        $this->Cell(17,9,utf8_decode('%'),0,0,'C');
                        $this->Cell(22,9,utf8_decode('Valor'),0,0,'C');
                        $this->Cell(17,9,utf8_decode('%'),0,0,'C');
                        $this->Cell(17,9,utf8_decode('%'),0,0,'C');
                        $this->Cell(22,9,utf8_decode('Base'),0,0,'C');
                        $this->Cell(22,9,utf8_decode('Base'),0,0,'C');
                        $this->Cell(22,9,utf8_decode('Estrato'),0,0,'C');
                        $this->Cell(26,9,utf8_decode('Ley'),0,0,'C');
                        $this->Cell(26,9,utf8_decode('Tipo Base'),0,0,'C');
                        $this->Cell(26,9,utf8_decode('Tipo Base'),0,0,'C');
                        $this->Cell(26,9,utf8_decode('Tipo Base'),0,0,'C');

                        $this->Ln(5);

                        $this->SetX(1.8);
                        $this->Cell(26,5,utf8_decode(''),0,0,'C');
                        $this->Cell(17,5,utf8_decode(''),0,0,'C');
                        $this->Cell(22,5,utf8_decode('Tarifa'),0,0,'C');    
                        $this->Cell(22,5,utf8_decode('Inferior'),0,0,'C');
                        $this->Cell(22,5,utf8_decode('Superior'),0,0,'C');
                        $this->Cell(17,5,utf8_decode('Incremento'),0,0,'C');
                        $this->Cell(22,5,utf8_decode(''),0,0,'C');
                        $this->Cell(17,5,utf8_decode('Sobretasa'),0,0,'C');
                        $this->Cell(17,5,utf8_decode('Ambiental'),0,0,'C');
                        $this->Cell(22,5,utf8_decode('Impuesto'),0,0,'C');
                        $this->Cell(22,5,utf8_decode('Ambiental'),0,0,'C');
                        $this->Cell(22,5,utf8_decode(''),0,0,'C');
                        $this->Cell(26,5,utf8_decode('44'),0,0,'C');
                        $this->Cell(26,5,utf8_decode('Rango'),0,0,'C');
                        $this->Cell(26,5,utf8_decode('Ambiental'),0,0,'C');
                        $this->Cell(26,5,utf8_decode('Calculo'),0,0,'C');

                        $this->Ln(5);

        /*                $this->Cell(22,9,utf8_decode(''),1,0,'C');
                        $this->Cell(22,9,utf8_decode(''),1,0,'C');
                 
                        $this->Cell(22,9,utf8_decode('Código Tarifa'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('Limite Inferior'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('Limite Superior'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('% Incremento'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('Valor'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('% Sobretasa'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('% Imp. Ambiental'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('Base Impuesto'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('Base Ambiental'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('Estrato'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('Ley 44'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('Tipo Base Rango'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('Tipo Base Ambiental'),1,0,'C');
                        $this->Cell(22,9,utf8_decode('Tipo Base Cálculo'),1,0,'C');





                        $this->SetX(1);
                        $this->Cell(22,9,utf8_decode('Nombre'),0,0,'C');
                        $this->Cell(22,9,utf8_decode('Año'),0,0,'C');
                       

                        $this->Ln(9);*/
                        
                    }

                    // Pie de página
                    function Footer()
                    {
                        // Posición: a 1,5 cm del final
                        global $hoy;
                        global $usuario;
                        $this->SetY(-15);
                        // Arial italic 8
                        $this->SetFont('Arial','B',8);
                        $this->SetX(10);
                        $this->Cell(90,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                        $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                        $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                        $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
                    }
                }

                $pdf = new PDF('L','mm','Legal');

                    $fechauno = $fechaini;       
                    $fechados = $fechafin;       
                    $compania = $_SESSION['compania'];
                    $usuario = $_SESSION['usuario'];

                    $consulta = "SELECT     t.razonsocial as traz,
                                            t.tipoidentificacion as tide,
                                            ti.id_unico as tid,
                                            ti.nombre as tnom,
                                            t.numeroidentificacion tnum
                            FROM gf_tercero t
                            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
                            WHERE t.id_unico = $compania";

                     $cmp = $mysqli->query($consulta);

                    $nomcomp = "";
                    $tipodoc = "";
                    $numdoc = "";
                    
                    while ($fila = mysqli_fetch_array($cmp))
                        {
                            $nomcomp = utf8_decode($fila['traz']);       
                            $tipodoc = utf8_decode($fila['tnom']);       
                            $numdoc = utf8_decode($fila['tnum']);   
                        }


                    $pdf->AddPage();
                    $pdf->AliasNbPages();
                    $pdf->SetFont('Arial','B',10);

                    $pdf->SetFont('Arial','',8);
                    $pdf->SetX(50);
                    $yp = $pdf->GetY();


                    $codd    = 0;
                    $totales = 0;
                    $valorA = 0;

                    //LOGO
                    $sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
                      FROM gf_tercero ter 
                      LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
                      WHERE ter.id_unico = '.$compania;
                    $rutaLogo = $mysqli->query($sqlRutaLogo);
                    $rowLogo = mysqli_fetch_array($rutaLogo);
                    $ruta = $rowLogo[0];
                    if($ruta != '')
                        {
                          $pdf->Image('../'.$ruta,30,8,20);
                        }

                    //CONSULTA
                    $sql = "SELECT          t.id_unico,
                                            t.nombre,
                                            t.anio,
                                            t.codigotarifa,
                                            t.limiteinferior,
                                            t.limitesuperior,
                                            t.porcentajeincremento,
                                            t.valor,
                                            t.porcentajesobretasa,
                                            t.porcentajeimpuestoambiental,
                                            t.baseimpuesto,
                                            t.baseambiental,
                                            t.estrato,
                                            e.id_unico,
                                            e.nombre,
                                            t.ley44,
                                            l.id_unico,
                                            l.nombre,
                                            t.tipobaserango,
                                            tb1.id_unico,
                                            tb1.nombre,
                                            t.tipobaseambiental,
                                            tb2.id_unico,
                                            tb2.nombre,
                                            t.tipobasecalculo,
                                            tb3.id_unico,
                                            tb3.nombre
                        FROM gr_tarifa t
                        LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
                        LEFT JOIN gr_ley_44 l ON t.ley44 = l.id_unico
                        LEFT JOIN gr_tipo_base tb1 ON t.tipobaserango = tb1.id_unico
                        LEFT JOIN gr_tipo_base tb2 ON t.tipobaseambiental = tb2.id_unico
                        LEFT JOIN gr_tipo_base tb3 ON t.tipobasecalculo = tb3.id_unico";

                    $resultado=$mysqli->query($sql);

                    while($row=mysqli_fetch_row($resultado)){

                        $tid    = $row[0];
                        $tnom   = $row[1];
                        $tanio  = $row[2];
                        $tctar  = $row[3];
                        $tlimi  = $row[4];
                        $tlims  = $row[5];
                        $tpori  = $row[6];
                        $tval   = $row[7];
                        $tpors  = $row[8];
                        $tporia = $row[9];
                        $tbasei = $row[10];
                        $tbasea = $row[11];
                        $testr  = $row[12];
                        $eid    = $row[13];
                        $enom   = $row[14];
                        $tley   = $row[15];
                        $lid    = $row[16];
                        $lnom   = $row[17];
                        $ttbasr = $row[18];
                        $tb1id  = $row[19];
                        $tb1nom = $row[20];
                        $ttbasa = $row[21];
                        $tb2id  = $row[22];
                        $tb2nom = $row[23];
                        $ttbasc = $row[24];
                        $tb3id  = $row[25];
                        $tb3nom = $row[26];                      
                        //llenar datos
                        $pdf->SetX(1.8);
                        $pdf->SetFont('Arial','',8);

                        //multicelda
                        $y2 = $pdf->GetY();
                        $x2 = $pdf->GetX();
                        $pdf->MultiCell(26,4,utf8_decode(ucwords(mb_strtolower($tnom))),0,'L');
                        $y22 = $pdf->GetY();
                        $h1 = $y22-$y2;
                        $px2 = $x2+26;

                        if($numpaginas>$paginactual){
                            $pdf->SetXY($px2,$yp);
                            $h1=$y22-$yp;
                        } else {
                            $pdf->SetXY($px2,$y2);
                        }

                        $pdf->Cell(17,4,utf8_decode($tanio),0,0,'C');
                        $pdf->Cell(22,4,utf8_decode($tctar),0,0,'C');
                        $pdf->Cell(22,4,utf8_decode($tlimi),0,0,'C');
                        $pdf->Cell(22,4,utf8_decode($tlims),0,0,'C');
                        $pdf->Cell(17,4,utf8_decode($tpori),0,0,'C');
                        $pdf->Cell(22,4,utf8_decode($tval),0,0,'C');
                        $pdf->Cell(17,4,utf8_decode($tpors),0,0,'C');
                        $pdf->Cell(17,4,utf8_decode($tporia),0,0,'C');
                        $pdf->Cell(22,4,utf8_decode($tbasei),0,0,'C');
                        $pdf->Cell(22,4,utf8_decode($tbasea),0,0,'C');
                        $pdf->Cell(22,4,utf8_decode($enom),0,0,'C');

                        //multicelda
                        $y2 = $pdf->GetY();
                        $x2 = $pdf->GetX();
                        $pdf->MultiCell(26,4,utf8_decode(ucwords(mb_strtolower($lnom))),0,'L');
                        $y22 = $pdf->GetY();
                        $h1 = $y22-$y2;
                        $px2 = $x2+26;

                        if($numpaginas>$paginactual){
                            $pdf->SetXY($px2,$yp);
                            $h1=$y22-$yp;
                        } else {
                            $pdf->SetXY($px2,$y2);
                        }


                        //multicelda
                        $y2 = $pdf->GetY();
                        $x2 = $pdf->GetX();
                        $pdf->MultiCell(26,4,utf8_decode(ucwords(mb_strtolower($tb1nom))),0,'L');
                        $y22 = $pdf->GetY();
                        $h1 = $y22-$y2;
                        $px2 = $x2+26;

                        if($numpaginas>$paginactual){
                            $pdf->SetXY($px2,$yp);
                            $h1=$y22-$yp;
                        } else {
                            $pdf->SetXY($px2,$y2);
                        }

                        //multicelda
                        $y2 = $pdf->GetY();
                        $x2 = $pdf->GetX();
                        $pdf->MultiCell(26,4,utf8_decode(ucwords(mb_strtolower($tb2nom))),0,'L');
                        $y22 = $pdf->GetY();
                        $h1 = $y22-$y2;
                        $px2 = $x2+26;

                        if($numpaginas>$paginactual){
                            $pdf->SetXY($px2,$yp);
                            $h1=$y22-$yp;
                        } else {
                            $pdf->SetXY($px2,$y2);
                        }

                        //multicelda
                        $y2 = $pdf->GetY();
                        $x2 = $pdf->GetX();
                        $pdf->MultiCell(26,4,utf8_decode(ucwords(mb_strtolower($tb3nom))),0,'L');
                        $y22 = $pdf->GetY();
                        $h1 = $y22-$y2;
                        $px2 = $x2+26;

                        if($numpaginas>$paginactual){
                            $pdf->SetXY($px2,$yp);
                            $h1=$y22-$yp;
                        } else {
                            $pdf->SetXY($px2,$y2);
                        }

                       

                       
                        //salto
                        $alto = max($h,$h1);
                        $pdf->Ln($alto);
                        $paginactual=$numpaginas;
                    }


                    while (ob_get_length()) {
                        ob_end_clean();
                    }
         //nombre del archivo de  descarga           
        $pdf->Output(0,'Informe_Tarifa('.date('d/m/Y').').pdf',0);


?>
