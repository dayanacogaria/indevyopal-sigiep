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

                        //Logo
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
                        $this->Cell(315,5,utf8_decode('INFORME DE EXCENSIONES PREDIO'),0,0,'C');
                        $this->SetFont('Arial','B',8);
                        $this->SetX(25);
                        $this->Cell(315,10,utf8_decode('FECHA SGC'),0,0,'R');

                        $this->Ln(17);

                 
                        $this->SetFont('Arial','B',8);
                        $this->SetX(3);

                        $this->Cell(42,9,utf8_decode(''),1,0,'C');
                        $this->Cell(42,9,utf8_decode(''),1,0,'C');
                 
                        $this->Cell(42,9,utf8_decode('Fecha Final'),1,0,'C');
                        $this->Cell(42,9,utf8_decode('Porcentaje Ex.'),1,0,'C');
                        $this->Cell(55,9,utf8_decode('Observaciones'),1,0,'C');
                        $this->Cell(42,9,utf8_decode('Tipo'),1,0,'C');
                        $this->Cell(42,9,utf8_decode('Predio'),1,0,'C');
                        $this->Cell(42,9,utf8_decode('Concepto'),1,0,'C');



                        $this->SetX(10);
                        $this->Cell(42,9,utf8_decode('Resolución'),0,0,'C');
                        $this->Cell(42,9,utf8_decode('Fecha Inicial'),0,0,'C');
                       

                        $this->Ln(9);
                        
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
                    $sql = "SELECT          ep.id_unico,
                                            ep.resolucion,
                                            DATE_FORMAT(ep.fechainicial,'%d-%m-%Y') AS fechaFacConvertidaI,
                                            DATE_FORMAT(ep.fechafinal,'%d-%m-%Y') AS fechaFacConvertidaF,
                                            ep.porcentajeex,
                                            ep.observaciones,
                                            ep.tipo,
                                            te.id_unico,
                                            te.nombre AS nombreTE,
                                            ep.predio,
                                            pr.id_unico,
                                            pr.nombre AS nombrePredio,
                                            ep.concepto,
                                            cp.id_unico,
                                            cp.nombre AS nombreConcepto
                        FROM gr_excensiones_predio ep
                        LEFT JOIN gr_tipo_excension te      ON ep.tipo   = te.id_unico
                        LEFT JOIN gp_predio1 pr             ON ep.predio = pr.id_unico
                        LEFT JOIN gr_concepto_predial cp    ON ep.concepto = cp.id_unico";

                    $resultado=$mysqli->query($sql);

                    while($row=mysqli_fetch_array($resultado)){
                       
                        //llenar datos
                        $pdf->SetX(3);
                        $pdf->Cell(42,4,utf8_decode($row['resolucion']),0,0,'C');
                        $pdf->Cell(42,4,utf8_decode($row['fechaFacConvertidaI']),0,0,'C');
                        $pdf->Cell(42,4,utf8_decode($row['fechaFacConvertidaF']),0,0,'C');
                        $pdf->Cell(42,4,utf8_decode($row['porcentajeex']),0,0,'C');
                        //multicelda
                        $y2 = $pdf->GetY();
                        $x2 = $pdf->GetX();
                        $pdf->MultiCell(55,4,utf8_decode(ucwords(mb_strtolower($row['observaciones'] ))),0,'L');
                        $y22 = $pdf->GetY();
                        $h1 = $y22-$y2;
                        $px2 = $x2+55;

                        if($numpaginas>$paginactual){
                            $pdf->SetXY($px2,$yp);
                            $h1=$y22-$yp;
                        } else {
                            $pdf->SetXY($px2,$y2);
                        }

                        $pdf->Cell(42,4,utf8_decode($row['nombreTE']),0,0,'C');
                        $pdf->Cell(42,4,utf8_decode($row['nombrePredio']),0,0,'C');
                        $pdf->Cell(42,4,utf8_decode($row['nombreConcepto']),0,0,'C');

                   
                       
                        //salto
                        $alto = max($h,$h1);
                        $pdf->Ln($alto);
                        $paginactual=$numpaginas;

                    }


                    while (ob_get_length()) {
                        ob_end_clean();
                    }
         //nombre del archivo de  descarga           
        $pdf->Output(0,'Informe_Excensiones_Predio('.date('d/m/Y').').pdf',0);


?>
