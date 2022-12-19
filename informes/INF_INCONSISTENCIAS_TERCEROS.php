<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#08/05/2018 | Erica G. | Archivo Creado
####/################################################################################
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();

#**********Recepción Variables ****************#
$anno       = $_SESSION['anno'];
$informe    = $_REQUEST['tipoInf'];
$exportar   = $_REQUEST['tipo'];
$arrayt     = array();
$ninforme   ="";
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
$ruta_logo    = $rowC[0][6];
#*******************************************************************************#
switch ($informe){
    #   ****    Terceros Con Doble Perfil No Asociado ****   #
    case(1):
        $ninforme   ="TERCEROS CON DOBLE PERFIL NO ASOCIADO";
        #   Buscar Terceros Perfil Jurídico    #
        $rowt = $con->Listar("SELECT DISTINCT t.id_unico 
                FROM  
                    gf_tercero t 
                LEFT JOIN 
                    gf_perfil_tercero pt ON t.id_unico = pt.tercero 
                WHERE 
                    pt.perfil IN (1,4,6,8,9,11,12) AND compania = $compania 
                ORDER BY t.id_unico");
        for ($i = 0; $i < count($rowt); $i++) {
            #   Buscar que no tenga perfil Natural 
            $rown = $con->Listar("SELECT * 
                FROM 
                    gf_perfil_tercero 
                WHERE tercero = ".$rowt[$i][0]." 
                AND perfil IN (2,3,5,7,10)");
            if(count($rown)>0){
                if(in_array($rowt[$i][0], $arrayt)) {
                } else {
                    array_push ( $arrayt , $rowt[$i][0] );
                }
            } 
        }
    break;
    #   ****    Terceros Con Razon Social y Nombres ****   #
    case(2):
        $ninforme   ="TERCEROS CON RAZON SOCIAL Y NOMBRE";
        #   Buscar Terceros   #
        $rowt = $con->Listar("SELECT DISTINCT t.id_unico 
                FROM  
                    gf_tercero t 
                WHERE 
                    compania = $compania AND 
                (razonsocial is not null OR razonsocial !='')
                ORDER BY t.id_unico");
        for ($i = 0; $i < count($rowt); $i++) {
            # *** Buscar Tengan Nombres*** #
            $rowna = $con->Listar("SELECT IF(CONCAT_WS(' ',
                tr.nombreuno,
                tr.nombredos,
                tr.apellidouno,
                tr.apellidodos) 
                IS NULL OR CONCAT_WS(' ',
                tr.nombreuno,
                tr.nombredos,
                tr.apellidouno,
                tr.apellidodos) = '',1,2) 
            FROM gf_tercero tr 
            WHERE tr.id_unico=".$rowt[$i][0]);
            if($rowna[0][0]==2){
                if(in_array($rowt[$i][0], $arrayt)) {
                } else {
                    array_push ( $arrayt , $rowt[$i][0] );
                }
            }
        }
    break;
    #   ****    Terceros Sin Perfil  ****   #
    case(3):
        $ninforme   ="TERCEROS SIN PERFIL";
        #   Buscar Terceros   #
        $rowt = $con->Listar("SELECT DISTINCT t.id_unico 
                FROM  
                    gf_tercero t 
                WHERE 
                    compania = $compania 
                ORDER BY t.id_unico");
        for ($i = 0; $i < count($rowt); $i++) {
            # *** Buscar Perfil *** #
            $rowna = $con->Listar("SELECT *  
            FROM gf_perfil_tercero tr 
            WHERE tr.tercero=".$rowt[$i][0]);
            if(count($rowna)==0){
                if(in_array($rowt[$i][0], $arrayt)) {
                } else {
                    array_push ( $arrayt , $rowt[$i][0] );
                }
            }
        }
    break;
    #   ****    Terceros Sin Dirección ****   #
    case(4):
        $ninforme   ="TERCEROS SIN DIRECCIÓN";
        #   Buscar Terceros   #
        $rowt = $con->Listar("SELECT DISTINCT t.id_unico 
                FROM  
                    gf_tercero t 
                WHERE 
                    compania = $compania 
                ORDER BY t.id_unico");
        for ($i = 0; $i < count($rowt); $i++) {
            # *** Buscar Perfil *** #
            $rowna = $con->Listar("SELECT *  
            FROM gf_direccion tr 
            WHERE tr.tercero=".$rowt[$i][0]);
            if(count($rowna)==0){
                if(in_array($rowt[$i][0], $arrayt)) {
                } else {
                    array_push ( $arrayt , $rowt[$i][0] );
                }
            }
        }
    break;
}
$terceros = implode(",", $arrayt);
$row = $con->Listar("SELECT DISTINCT id_unico, 
        numeroidentificacion,
        nombreuno, nombredos,
        apellidouno, apellidodos, 
        razonsocial, digitoverficacion 
        FROM gf_tercero WHERE id_unico IN ($terceros)");
switch ($exportar){
    #*** pdf ***#
    case 1:
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
                global $ninforme;
                global $informe;

                $this->SetFont('Arial','B',10);

                if($ruta_logo != '')
                {
                  $this->Image('../'.$ruta_logo,10,5,28);
                }
                $this->SetFont('Arial','B',10);	
                $this->MultiCell(330,5,utf8_decode($razonsocial),0,'C');		
                $this->SetX(10);
                $this->Cell(330,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(330,5,utf8_decode('Dirección: '.$direccinTer),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(330,5,utf8_decode('Tel: '.$telefonoTer),0,0,'C');
                $this->ln(5);
                $this->SetX(10);
                $this->Cell(330,5,utf8_decode($ninforme),0,0,'C');
                $this->Ln(8);
                $this->SetFont('Arial','B',9);	
                if($informe==1){
                    $this->Cell(35,10, utf8_decode(''),1,0,'C');
                    $this->Cell(45,10,utf8_decode(''),1,0,'C');
                    $this->Cell(45,10,utf8_decode(''),1,0,'C');
                    $this->Cell(45,10,utf8_decode(''),1,0,'C');
                    $this->Cell(45,10,utf8_decode(''),1,0,'C');
                    $this->Cell(55,10,utf8_decode(''),1,0,'C');
                    $this->Cell(35,10,utf8_decode(''),1,0,'C');
                    $this->Cell(35,10,utf8_decode(''),1,0,'C');
                    $this->Setx(10);
                    $this->Cell(35,5, utf8_decode('NÚMERO DE '),0,0,'C');
                    $this->Cell(45,5,utf8_decode('NOMBRE'),0,0,'C');
                    $this->Cell(45,5,utf8_decode('NOMBRE'),0,0,'C');
                    $this->Cell(45,5,utf8_decode('APELLIDO'),0,0,'C');
                    $this->Cell(45,5,utf8_decode('APELLIDO'),0,0,'C');
                    $this->Cell(55,5,utf8_decode('RAZÓN'),0,0,'C');
                    $this->Cell(35,5,utf8_decode('DÍGITO DE '),0,0,'C');
                    $this->Cell(35,5,utf8_decode(''),0,0,'C');
                    $this->Ln(5);
                    $this->Cell(35,5, utf8_decode('IDENTIFICACIÓN'),0,0,'C');
                    $this->Cell(45,5,utf8_decode('UNO'),0,0,'C');
                    $this->Cell(45,5,utf8_decode('DOS'),0,0,'C');
                    $this->Cell(45,5,utf8_decode('UNO'),0,0,'C');
                    $this->Cell(45,5,utf8_decode('DOS'),0,0,'C');
                    $this->Cell(55,5,utf8_decode('SOCIAL'),0,0,'C');
                    $this->Cell(35,5,utf8_decode('VERIFICACIÓN'),0,0,'C');
                    $this->Cell(35,5,utf8_decode('PERFILES'),0,0,'C');
                    $this->Ln(5);
                } else {
                    $this->Cell(40,10, utf8_decode(''),1,0,'C');
                    $this->Cell(50,10,utf8_decode(''),1,0,'C');
                    $this->Cell(50,10,utf8_decode(''),1,0,'C');
                    $this->Cell(50,10,utf8_decode(''),1,0,'C');
                    $this->Cell(50,10,utf8_decode(''),1,0,'C');
                    $this->Cell(60,10,utf8_decode(''),1,0,'C');
                    $this->Cell(40,10,utf8_decode(''),1,0,'C');
                    $this->Setx(10);
                    $this->Cell(40,5, utf8_decode('NÚMERO DE '),0,0,'C');
                    $this->Cell(50,5,utf8_decode('NOMBRE'),0,0,'C');
                    $this->Cell(50,5,utf8_decode('NOMBRE'),0,0,'C');
                    $this->Cell(50,5,utf8_decode('APELLIDO'),0,0,'C');
                    $this->Cell(50,5,utf8_decode('APELLIDO'),0,0,'C');
                    $this->Cell(60,5,utf8_decode('RAZÓN'),0,0,'C');
                    $this->Cell(40,5,utf8_decode('DÍGITO DE '),0,0,'C');
                    $this->Ln(5);
                    $this->Cell(40,5, utf8_decode('IDENTIFICACIÓN'),0,0,'C');
                    $this->Cell(50,5,utf8_decode('UNO'),0,0,'C');
                    $this->Cell(50,5,utf8_decode('DOS'),0,0,'C');
                    $this->Cell(50,5,utf8_decode('UNO'),0,0,'C');
                    $this->Cell(50,5,utf8_decode('DOS'),0,0,'C');
                    $this->Cell(50,5,utf8_decode('SOCIAL'),0,0,'C');
                    $this->Cell(60,5,utf8_decode('VERIFICACIÓN'),0,0,'C');
                    $this->Cell(40,5,utf8_decode('PERFILES'),0,0,'C');
                    $this->Ln(5);
                }
                
            }      

            function Footer(){
                $this->SetY(-15);
                $this->SetFont('Arial','B',8);
                $this->SetX(10);
                $this->Cell(190,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
            }
        }
        $pdf = new PDF('L','mm','Legal');   
        $nb=$pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial','',9);
        for ($i = 0; $i < count($row); $i++) {
            $alt = $pdf->GetY();
            if($alt>160){
                $pdf->AddPage();
            }
            
            if($informe==1){
                $xp = $pdf->GetX();
                $yp = $pdf->GetY();
                $pdf->Cell(35,5,utf8_decode($row[$i][1]),0,0,'L');
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(45,5,utf8_decode($row[$i][2]),0,'L');
                $y2 = $pdf->GetY();
                $h = $y2-$y;
                $pdf->SetXY($x+45, $y);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(45,5,utf8_decode($row[$i][3]),0,'L');
                $y2 = $pdf->GetY();
                $h2 = $y2-$y;
                $pdf->SetXY($x+45, $y);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(45,5,utf8_decode($row[$i][4]),0,'L');
                $y2 = $pdf->GetY();
                $h3 = $y2-$y;
                $pdf->SetXY($x+45, $y);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(45,5,utf8_decode($row[$i][5]),0,'L');
                $y2 = $pdf->GetY();
                $h4 = $y2-$y;
                $pdf->SetXY($x+45, $y);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(55,5,utf8_decode($row[$i][6]),0,'L');
                $y2 = $pdf->GetY();
                $h5 = $y2-$y;
                $pdf->SetXY($x+55, $y);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(35,5,utf8_decode($row[$i][7]),0,'L');
                $y2 = $pdf->GetY();
                $h6 = $y2-$y;
                $pdf->SetXY($x+35, $y);
                # *** Buscar Perfiles Tercero ** #
                $pf = $con->Listar("SELECT DISTINCT p.nombre 
                        FROM 
                            gf_perfil_tercero  pt 
                        LEFT JOIN 
                            gf_perfil p On pt.perfil = p.id_unico 
                        WHERE tercero =".$row[$i][0]);
                $prf ="";
                for ($j = 0; $j < count($pf); $j++) {
                    $prf .= $pf[$j][0].' - ';
                }
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(35,5,utf8_decode($prf),0,'L');
                $y2 = $pdf->GetY();
                $h7 = $y2-$y;
                $hmax = max($h1, $h2, $h3, $h4, $h5, $h6,$h7);
                $pdf->SetXY($xp, $yp);
                $pdf->CellFitScale(35,$hmax,utf8_decode(''),1,0,'R');
                $pdf->Cell(45,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(45,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(45,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(45,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(55,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(35,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(35,$hmax,utf8_decode(''),1,0,'L');

            } else {
                $xp = $pdf->GetX();
                $yp = $pdf->GetY();
                $pdf->Cell(40,5,utf8_decode($row[$i][1]),0,0,'L');
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(50,5,utf8_decode($row[$i][2]),0,'L');
                $y2 = $pdf->GetY();
                $h = $y2-$y;
                $pdf->SetXY($x+50, $y);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(50,5,utf8_decode($row[$i][3]),0,'L');
                $y2 = $pdf->GetY();
                $h2 = $y2-$y;
                $pdf->SetXY($x+50, $y);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(50,5,utf8_decode($row[$i][4]),0,'L');
                $y2 = $pdf->GetY();
                $h3 = $y2-$y;
                $pdf->SetXY($x+50, $y);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(50,5,utf8_decode($row[$i][5]),0,'L');
                $y2 = $pdf->GetY();
                $h4 = $y2-$y;
                $pdf->SetXY($x+50, $y);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(60,5,utf8_decode($row[$i][6]),0,'L');
                $y2 = $pdf->GetY();
                $h5 = $y2-$y;
                $pdf->SetXY($x+60, $y);
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(40,5,utf8_decode($row[$i][7]),0,'L');
                $y2 = $pdf->GetY();
                $h6 = $y2-$y;
                
                $hmax = max($h1, $h2, $h3, $h4, $h5, $h6);
                $pdf->SetXY($xp, $yp);
                $pdf->CellFitScale(40,$hmax,utf8_decode(''),1,0,'R');
                $pdf->Cell(50,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(50,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(50,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(50,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(60,$hmax,utf8_decode(''),1,0,'L');
                $pdf->Cell(40,$hmax,utf8_decode(''),1,0,'L');
            
            
            }
            $pdf->Ln($hmax);

        } 
        
        
        ob_end_clean();		
        $pdf->Output(0,'Informe_Inconsistencias_Terceros.pdf',0);
    break;
    #*** xls ***#
    case 2:
        header("Content-Disposition: attachment; filename=Informe_Inconsistencias_Terceros.xls");
        ini_set('max_execution_time', 0);
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Informe Inconsistencias Tercero</title>
            </head>
            <body>
                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                    <th <?php if($informe==1){ echo 'colspan="8"'; } else {echo 'colspan="7"'; } ?> align="center"><strong>
                        <br/><?php echo $razonsocial ?>
                        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                        <br/>&nbsp;
                        <br/><?PHP echo $ninforme; ?> 
                        <br/>&nbsp;</strong>
                    </th>
                    <tr>
                        <td><center><strong>NÚMERO DE IDENTIFICACIÓN</strong></center></td>
                        <td><center><strong>NOMBRE UNO</strong></center></td>
                        <td><center><strong>NOMBRE DOS</strong></center></td>
                        <td><center><strong>APELLIDO UNO</strong></center></td>
                        <td><center><strong>APELLIDO DOS</strong></center></td>
                        <td><center><strong>RAZÓN SOCIAL</strong></center></td>
                        <td><center><strong>DIGITO DE VERIFICACIÓN</strong></center></td>
                        <?php 
                        if($informe==1){
                            echo '<td><center><strong>PERFILES</strong></center></td>';
                        }
                        ?>
                        
                    </tr>
                    <?php 
                    for ($i = 0; $i < count($row); $i++) {
                        echo '<tr>';
                        echo '<td>'.$row[$i][1].'</td>';
                        echo '<td>'.$row[$i][2].'</td>';
                        echo '<td>'.$row[$i][3].'</td>';
                        echo '<td>'.$row[$i][4].'</td>';
                        echo '<td>'.$row[$i][5].'</td>';
                        echo '<td>'.$row[$i][6].'</td>';
                        echo '<td>'.$row[$i][7].'</td>';
                        if($informe==1){
                            # *** Buscar Perfiles Tercero ** #
                            $pf = $con->Listar("SELECT DISTINCT p.nombre 
                                    FROM 
                                        gf_perfil_tercero  pt 
                                    LEFT JOIN 
                                        gf_perfil p On pt.perfil = p.id_unico 
                                    WHERE tercero =".$row[$i][0]);
                            echo '<td>';
                            for ($j = 0; $j < count($pf); $j++) {
                                echo $pf[$j][0].'<br/>';
                            }
                            echo '</td>';
                            
                        }
                        echo '</tr>';
                            
                    } ?>
                </table>
            </body>
        </html>
        <?php             
    break;
}


