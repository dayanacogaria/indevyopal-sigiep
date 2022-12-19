<?php 
###############################################################################
#       ******************       Modificaciones      ******************       #
###############################################################################
#30/05/2018 |Erica G.| Informes Convergencia
#13/04/2018 |Erica G.| Archivo Creado
###############################################################################
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();
$informe    = $_REQUEST['tI'];
$tipo       = $_REQUEST['t'];
$panno      = $_REQUEST['a'];
$anno       = anno($panno);

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

#   *****   Tipos Comprobante   ****    #
$numc =0;
$tcn = $con->Listar("SELECT DISTINCT id_unico FROM gf_tipo_comprobante WHERE niif=1");
if(count($tcn)>0){
    $numc = count($tcn);
} 
$row = $con->Listar("SELECT 
                t.id_cuenta,
                t.codi_cuenta, 
                t.cod_predecesor, 
                t.saldo_inicial,
                t.uno_debito, 
                t.uno_credito,
                t.dos_debito, 
                t.dos_credito,
                t.tres_debito, 
                t.tres_credito,
                t.cuatro_debito, 
                t.cuatro_credito, 
                LOWER(t.nombre), 
                t.naturaleza, 
                c.tipocuentacgn  
            FROM 
                temporal_estado_apertura t 
            LEFT JOIN 
                gf_cuenta c ON t.id_cuenta = c.id_unico 
            ORDER BY 
                t.codi_cuenta ASC ");
$row_r = $con->Listar("SELECT 
                t.id_cuenta,
                t.codi_cuenta, 
                t.cod_predecesor, 
                t.saldo_inicial,
                t.uno_debito, 
                t.uno_credito,
                t.dos_debito, 
                t.dos_credito,
                t.tres_debito, 
                t.tres_credito,
                t.cuatro_debito, 
                t.cuatro_credito, 
                LOWER(t.nombre), 
                t.naturaleza, 
                c.tipocuentacgn  
            FROM 
                temporal_estado_apertura t 
            LEFT JOIN 
                gf_cuenta c ON t.id_cuenta = c.id_unico 
            WHERE 
                LENGTH(t.codi_cuenta) =1 
            ORDER BY 
                t.codi_cuenta ASC ");
$row_n = $con->Listar("SELECT 
                t.id_cuenta,
                t.codi_cuenta, 
                t.cod_predecesor, 
                t.saldo_inicial,
                t.uno_debito, 
                t.uno_credito,
                t.dos_debito, 
                t.dos_credito,
                t.tres_debito, 
                t.tres_credito,
                t.cuatro_debito, 
                t.cuatro_credito, 
                LOWER(t.nombre), 
                t.naturaleza, 
                c.tipocuentacgn  
            FROM 
                temporal_estado_apertura t 
            LEFT JOIN 
                gf_cuenta c ON t.id_cuenta = c.id_unico 
            WHERE 
                LENGTH(t.codi_cuenta) <=6 
            ORDER BY 
                t.codi_cuenta ASC ");
#********* Informe General **************# 
if($informe==1){
    if($tipo=='pdf'){
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
                global $numc;
                global $anno;
                global $con;
                $annoa = $anno-1;
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
                $this->Cell(330,5,utf8_decode('ESTADO DE SITUACION FINANCIERA DE APERTURA'),0,0,'C');
                $this->Ln(5);
                $this->Cell(330,5,utf8_decode( 'AÑO: '.$anno ),0,0,'C');
                $this->Ln(8);

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
        $annoa = $anno-1;
        $pdf->SetFont('Arial','B',8);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(30,5, utf8_decode(''),0,0,'C');
        $pdf->Cell(55,5,utf8_decode(''),0,0,'C');
        $pdf->Cell(25,5,utf8_decode(''),0,0,'C');
        $tam = 120/$numc;
        $tcn = $con->Listar("SELECT DISTINCT UPPER(nombre) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
        $l = 110;

        $alto =3;
        for($t =0; $t < count($tcn); $t++){
            $l +=$tam;
            $yp = $pdf->GetY();
            $pdf->MultiCell($tam,5,utf8_decode($tcn[$t][0]),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $yp;
            $alto = max($alto, $h);
            $pdf->SetXY($x+$l, $y);
        }
        $alto =$alto+5;
        $pdf->SetXY($x, $y);
        $pdf->Cell(30,$alto, utf8_decode('CUENTA'),0,0,'C');
        $pdf->Cell(55,$alto,utf8_decode('NOMBRE'),0,0,'C');
        $pdf->Cell(25,($alto-5),utf8_decode('SALDO A 31 DE '),0,0,'C');
        $tam = 120/($numc);
        for($t =0; $t < ($numc); $t++){
            $pdf->Cell($tam,($alto-5),utf8_decode(''),1,0,'C');
        }
        $pdf->Cell(25,($alto-5),utf8_decode('SALDO A 01 DE '),0,0,'C');
        $pdf->Cell(25,($alto),utf8_decode('CORRIENTE '),0,0,'C');
        $pdf->Cell(25,$alto,utf8_decode('NO CORRIENTE'),0,0,'C');
        $pdf->Cell(30,$alto,utf8_decode('OBSERVACIONES'),0,0,'C');
        $pdf->Ln($alto-5);

        $pdf->Cell(30,5,utf8_decode(''),0,0,'C');
        $pdf->Cell(55,5,utf8_decode(''),0,0,'C');
        $pdf->CellFitScale(25,(5),utf8_decode('DICIEMBRE '.$annoa),0,0,'C');
        $tam = 120/($numc*2);
        for($t =0; $t < ($numc); $t++){
            $pdf->Cell($tam,(5),utf8_decode('DÉBITO'),1,0,'C');
            $pdf->Cell($tam,(5),utf8_decode('CRÉDITO'),1,0,'C');
        }

        $pdf->CellFitScale(25,(5),utf8_decode('ENERO '.$anno),0,0,'C');
        $pdf->Cell(25,5,utf8_decode(''),0,0,'C');
        $pdf->Cell(25,5,utf8_decode(''),0,0,'C');
        $pdf->Cell(30,5,utf8_decode(''),0,0,'C');

        $pdf->SetXY($x, $y);
        $pdf->Cell(30,$alto, utf8_decode(''),1,0,'C');
        $pdf->Cell(55,$alto,utf8_decode(''),1,0,'C');
        $pdf->Cell(25,$alto,utf8_decode(''),1,0,'C');
        $tam = 120/$numc;
        for($t =0; $t < ($numc); $t++){
            $pdf->Cell($tam,$alto,utf8_decode(''),0,0,'C');
        }
        $pdf->Cell(25,$alto,utf8_decode(''),1,0,'C');
        $pdf->Cell(25,$alto,utf8_decode(''),1,0,'C');
        $pdf->Cell(25,$alto,utf8_decode(''),1,0,'C');
        $pdf->Cell(30,$alto,utf8_decode(''),1,0,'C');
        $pdf->Ln($alto);



         for ($i = 0; $i < count($row); $i++) {
                $id_cuenta      = $row[$i][0];
                $codi_cuenta    = $row[$i][1];
                $nombre         = $row[$i][12];
                $cod_predecesor = $row[$i][2];
                $naturaleza     = $row[$i][13];
                $saldo_inicial  = $row[$i][3];
                $debito0        = $row[$i][4];
                $credito0       = $row[$i][5];
                $debito1        = $row[$i][6];
                $credito1       = $row[$i][7];
                $debito2        = $row[$i][8];
                $credito2       = $row[$i][9];
                $debito3        = $row[$i][10];
                $credito3       = $row[$i][11];
                $cgn            = $row[$i][14];
                # ** Si Tamaño Del Codigo es 1 ** #
                $imprimir       = 0;
                if(strlen($codi_cuenta)<=1){
                    $imprimir   = 1;
                } else { 
                    if(($saldo_inicial==0   || $saldo_inicial==NULL) && 
                        ($debito0   == 0    || $debito0==NULL) && 
                        ($credito0  == 0    || $credito0==NULL )&& 
                        ($debito1   == 0    || $debito1==NULL ) && 
                        ($credito1  == 0    || $credito1==NULL) && 
                        ($debito2   == 0    || $debito2==NULL) && 
                        ($credito2  == 0    || $credito2==NULL) && 
                        ($debito3   == 0    || $debito3==NULL) && 
                        ($credito3  == 0    || $credito3==NULL) ){
                        #   *** Buscar Si Los Hijos Tienen Saldo *** #
                        $rowc = $con->Listar("SELECT id_unico, 
                                SUM(IF(saldo_inicial<0, saldo_inicial*-1,saldo_inicial)),
                                SUM(IF(uno_debito<0, uno_debito*-1,uno_debito)),
                                SUM(IF(uno_credito<0, uno_credito*-1,uno_credito)),
                                SUM(IF(dos_debito<0, dos_debito*-1,dos_debito)),
                                SUM(IF(dos_credito<0, dos_credito*-1,dos_credito)),
                                SUM(IF(tres_debito<0, tres_debito*-1,tres_debito)),
                                SUM(IF(tres_credito<0, tres_credito*-1,tres_credito)),
                                SUM(IF(cuatro_debito<0, cuatro_debito*-1,cuatro_debito)),
                                SUM(IF(cuatro_credito<0, cuatro_credito*-1,cuatro_credito)) 
                                FROM temporal_estado_apertura  
                                WHERE cod_predecesor = $codi_cuenta"); 

                        if(count($rowc)>0){
                            if (($rowc[0][1] == 0 || $rowc[0][1] == NULL) 
                            && ($rowc[0][2] == 0  || $rowc[0][2] == NULL)  
                            && ($rowc[0][3] == 0  || $rowc[0][3] == NULL)
                            && ($rowc[0][4] == 0  || $rowc[0][4] == NULL) 
                            && ($rowc[0][5] == 0  || $rowc[0][5] == NULL) 
                            && ($rowc[0][6] == 0  || $rowc[0][6] == NULL) 
                            && ($rowc[0][7] == 0  || $rowc[0][7] == NULL) 
                            && ($rowc[0][8] == 0  || $rowc[0][8] == NULL) 
                            && ($rowc[0][9] == 0  || $rowc[0][9] == NULL)){   
                            } else {
                                $imprimir   = 1;
                            }
                        }
                    } else {
                        $imprimir   = 1;

                    } 
                }
                if($imprimir ==1){
                    $a = $pdf->GetY();
                    if($a>185){
                        $pdf->AddPage();
                        $pdf->SetFont('Arial','B',8);
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->Cell(30,5, utf8_decode(''),0,0,'C');
                        $pdf->Cell(55,5,utf8_decode(''),0,0,'C');
                        $pdf->Cell(25,5,utf8_decode(''),0,0,'C');
                        $tam = 120/$numc;
                        $tcn = $con->Listar("SELECT DISTINCT UPPER(nombre) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
                        $l = 110;

                        $alto =3;
                        for($t =0; $t < count($tcn); $t++){
                            $l +=$tam;
                            $yp = $pdf->GetY();
                            $pdf->MultiCell($tam,3,utf8_decode($tcn[$t][0]),0,'C');
                            $y2 = $pdf->GetY();
                            $h = $y2 - $yp;
                            $alto = max($alto, $h);
                            $pdf->SetXY($x+$l, $y);
                        }
                        $alto =$alto+5;
                        $pdf->SetXY($x, $y);
                        $pdf->Cell(30,$alto, utf8_decode('CUENTA'),0,0,'C');
                        $pdf->Cell(55,$alto,utf8_decode('NOMBRE'),0,0,'C');
                        $pdf->Cell(25,($alto-5),utf8_decode('SALDO A 31 DE '),0,0,'C');
                        $tam = 120/($numc);
                        for($t =0; $t < ($numc); $t++){
                            $pdf->Cell($tam,($alto-5),utf8_decode(''),1,0,'C');
                        }
                        $pdf->Cell(25,($alto-5),utf8_decode('SALDO A 01 DE '),0,0,'C');
                        $pdf->Cell(25,($alto),utf8_decode('CORRIENTE '),0,0,'C');
                        $pdf->Cell(25,$alto,utf8_decode('NO CORRIENTE'),0,0,'C');
                        $pdf->Cell(30,$alto,utf8_decode('OBSERVACIONES'),0,0,'C');
                        $pdf->Ln($alto-5);

                        $pdf->Cell(30,5,utf8_decode(''),0,0,'C');
                        $pdf->Cell(55,5,utf8_decode(''),0,0,'C');
                        $pdf->CellFitScale(25,(5),utf8_decode('DICIEMBRE '.$annoa),0,0,'C');
                        $tam = 120/($numc*2);
                        for($t =0; $t < ($numc); $t++){
                            $pdf->Cell($tam,(5),utf8_decode('DÉBITO'),1,0,'C');
                            $pdf->Cell($tam,(5),utf8_decode('CRÉDITO'),1,0,'C');
                        }

                        $pdf->CellFitScale(25,(5),utf8_decode('ENERO '.$anno),0,0,'C');
                        $pdf->Cell(25,5,utf8_decode(''),0,0,'C');
                        $pdf->Cell(25,5,utf8_decode(''),0,0,'C');
                        $pdf->Cell(30,5,utf8_decode(''),0,0,'C');

                        $pdf->SetXY($x, $y);
                        $pdf->Cell(30,$alto, utf8_decode(''),1,0,'C');
                        $pdf->Cell(55,$alto,utf8_decode(''),1,0,'C');
                        $pdf->Cell(25,$alto,utf8_decode(''),1,0,'C');
                        $tam = 120/$numc;
                        for($t =0; $t < ($numc); $t++){
                            $pdf->Cell($tam,$alto,utf8_decode(''),0,0,'C');
                        }
                        $pdf->Cell(25,$alto,utf8_decode(''),1,0,'C');
                        $pdf->Cell(25,$alto,utf8_decode(''),1,0,'C');
                        $pdf->Cell(25,$alto,utf8_decode(''),1,0,'C');
                        $pdf->Cell(30,$alto,utf8_decode(''),1,0,'C');
                        $pdf->Ln($alto);
                    }
                    $pdf->SetFont('Arial','',8);
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();

                    $xp = $pdf->GetX();
                    $yp = $pdf->GetY();
                    $pdf->CellFitScale(30, 5, '', 0, 0, 'L');
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell(55, 5, utf8_decode(ucwords($nombre)), 0, 'J');
                    $y2 = $pdf->GetY();
                    $h = $y2 - $y;
                    $alto = $h;

                    $pdf->SetXY($xp,$yp);

                    $pdf->CellFitScale(30,$alto, utf8_decode($codi_cuenta),1,0,'L');
                    $pdf->Cell(55,$alto,utf8_decode(''),1,0,'L');
                    $pdf->CellFitScale(25,$alto,number_format($saldo_inicial,2,'.',','),1,0,'R');

                    $tam = 120/($numc*2);
                    $saldo_final = 0;
                    $saldo_final += $saldo_inicial;
                    $tcn = $con->Listar("SELECT DISTINCT UPPER(sigla) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
                    for($t =0; $t < count($tcn); $t++){
                        $debito     = 'debito'.$t;
                        $credito    = 'credito'.$t;
                        $pdf->CellFitScale($tam,$alto,number_format($$debito,2,'.',','),1,0,'R');
                        $pdf->CellFitScale($tam,$alto,number_format($$credito,2,'.',','),1,0,'R');
                        if($naturaleza==1){
                            $saldo_final +=($$debito-$$credito);
                        } else {
                            $saldo_final +=($$credito-$$debito);
                        }

                    }

                    $pdf->Cell(25,$alto,number_format($saldo_final,2,'.',','),1,0,'R');

                    $s_corriente    =   0;
                    $s_ncorriente   =   0;

                    if($cgn==2){
                        $s_corriente    =   $saldo_final;
                    } elseif($cgn==6){
                        $s_ncorriente   =   $saldo_final;
                    }
                    $pdf->Cell(25,$alto,number_format($s_corriente,2,'.',','),1,0,'R');
                    $pdf->Cell(25,$alto,number_format($s_ncorriente,2,'.',','),1,0,'R');
                    $pdf->Cell(30,$alto,utf8_decode(''),1,0,'C');
                    $pdf->Ln($alto);
                }
        }
        #   ** Resumen ** #
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(335,10, utf8_decode('RESUMEN'),1,0,'C');
        $pdf->Ln(10);

        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(30,5, utf8_decode(''),0,0,'C');
        $pdf->Cell(55,5,utf8_decode(''),0,0,'C');
        $pdf->Cell(25,5,utf8_decode(''),0,0,'C');
        $tam = 200/$numc;
        $tcn = $con->Listar("SELECT DISTINCT UPPER(nombre) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
        $l = 110;

        $alto =3;
        for($t =0; $t < count($tcn); $t++){
            $l +=$tam;
            $yp = $pdf->GetY();
            $pdf->MultiCell($tam,5,utf8_decode($tcn[$t][0]),0,'C');
            $y2 = $pdf->GetY();
            $h = $y2 - $yp;
            $alto = max($alto, $h);
            $pdf->SetXY($x+$l, $y);
        }
        $alto =$alto+5;
        $pdf->SetXY($x, $y);
        $pdf->Cell(30,$alto, utf8_decode('CUENTA'),0,0,'C');
        $pdf->Cell(55,$alto,utf8_decode('NOMBRE'),0,0,'C');
        $pdf->Cell(25,($alto-5),utf8_decode('SALDO A 31 DE '),0,0,'C');
        $tam = 200/($numc);
        for($t =0; $t < ($numc); $t++){
            $pdf->Cell($tam,($alto-5),utf8_decode(''),1,0,'C');
        }
        $pdf->Cell(25,($alto-5),utf8_decode('SALDO A 01 DE '),0,0,'C');
        $pdf->Ln($alto-5);

        $pdf->Cell(30,5,utf8_decode(''),0,0,'C');
        $pdf->Cell(55,5,utf8_decode(''),0,0,'C');
        $pdf->CellFitScale(25,(5),utf8_decode('DICIEMBRE '.$annoa),0,0,'C');
        $tam = 200/($numc*2);
        for($t =0; $t < ($numc); $t++){
            $pdf->Cell($tam,(5),utf8_decode('DÉBITO'),1,0,'C');
            $pdf->Cell($tam,(5),utf8_decode('CRÉDITO'),1,0,'C');
        }

        $pdf->CellFitScale(25,(5),utf8_decode('ENERO '.$anno),0,0,'C');

        $pdf->SetXY($x, $y);
        $pdf->Cell(30,$alto, utf8_decode(''),1,0,'C');
        $pdf->Cell(55,$alto,utf8_decode(''),1,0,'C');
        $pdf->Cell(25,$alto,utf8_decode(''),1,0,'C');
        $tam = 200/$numc;
        for($t =0; $t < ($numc); $t++){
            $pdf->Cell($tam,$alto,utf8_decode(''),0,0,'C');
        }
        $pdf->Cell(25,$alto,utf8_decode(''),1,0,'C');
        $pdf->Ln($alto);
        $total_inicial        = 0;
        $total_debito0        = 0;
        $total_credito0       = 0;
        $total_debito1        = 0;
        $total_credito1       = 0;
        $total_debito2        = 0;
        $total_credito2       = 0;
        $total_debito3        = 0;
        $total_credito3       = 0;
        $total_final          = 0;
        $pdf->SetFont('Arial','',8);  

        for ($i = 0; $i < count($row_r); $i++) {
            $id_cuenta      = $row_r[$i][0];
            $codi_cuenta    = $row_r[$i][1];
            $nombre         = $row_r[$i][12];
            $cod_predecesor = $row_r[$i][2];
            $naturaleza     = $row_r[$i][13];
            $saldo_inicial  = $row_r[$i][3];
            $debito0        = $row_r[$i][4];
            $credito0       = $row_r[$i][5];
            $debito1        = $row_r[$i][6];
            $credito1       = $row_r[$i][7];
            $debito2        = $row_r[$i][8];
            $credito2       = $row_r[$i][9];
            $debito3        = $row_r[$i][10];
            $credito3       = $row_r[$i][11];
            $cgn            = $row_r[$i][14];
            $xp = $pdf->GetX();
            $yp = $pdf->GetY();
            $pdf->CellFitScale(30, 5, '', 0, 0, 'L');
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->MultiCell(55, 5, utf8_decode(ucwords($nombre)), 0, 'J');
            $y2 = $pdf->GetY();
            $h = $y2 - $y;
            $alto = $h;

            $pdf->SetXY($xp,$yp);

            $pdf->CellFitScale(30,$alto, utf8_decode($codi_cuenta),1,0,'L');
            $pdf->Cell(55,$alto,utf8_decode(''),1,0,'L');
            $pdf->CellFitScale(25,$alto,number_format($saldo_inicial,2,'.',','),1,0,'R');

            $tam = 200/($numc*2);
            $saldo_final = 0;
            $tcn = $con->Listar("SELECT DISTINCT UPPER(sigla) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
            $saldo_final  +=$saldo_inicial;
            for($t =0; $t < count($tcn); $t++){
                $debito     = 'debito'.$t;
                $credito    = 'credito'.$t;
                $pdf->CellFitScale($tam,$alto,number_format($$debito,2,'.',','),1,0,'R');
                $pdf->CellFitScale($tam,$alto,number_format($$credito,2,'.',','),1,0,'R');
                if($naturaleza==1){
                    $saldo_final +=($$debito-$$credito);
                } else {
                    $saldo_final +=($$credito-$$debito);
                }
                $total_debito        = 'total_debito'.$t;
                $total_credito       = 'total_credito'.$t;
                $$total_debito  += $$debito;
                $$total_credito += $$credito;

            }
            $pdf->Cell(25,$alto,number_format($saldo_final,2,'.',','),1,0,'R');
            $pdf->Ln($alto);

            switch ($codi_cuenta){
                case 1:
                    $total_inicial +=$saldo_inicial;
                    $total_final   +=$saldo_final;
                break;
                case 2:
                    $total_inicial -=$saldo_inicial;
                    $total_final   -=$saldo_final;
                break;
                case 3:
                    $total_inicial -=$saldo_inicial;
                    $total_final   -=$saldo_final;
                break;
                case 4:
                    $total_inicial -=$saldo_inicial;
                    $total_final   -=$saldo_final;
                break;
                case 5:
                    $total_inicial +=$saldo_inicial;
                    $total_final   +=$saldo_final;
                break;
                case 6:
                    $total_inicial +=$saldo_inicial;
                    $total_final   +=$saldo_final;
                break;
                case 7:
                    $total_inicial +=$saldo_inicial;
                    $total_final   +=$saldo_final;
                break;
                default :
                    $total_inicial +=$saldo_inicial;
                    $total_final   +=$saldo_final;
                break;

            }

       }
        $pdf->SetFont('Arial','B',8);  
        $pdf->CellFitScale(85, 10, 'TOTALES',1, 0, 'C');
        $pdf->CellFitScale(25,10,number_format($total_inicial,2,'.',','),1,0,'R');
        $tam = 200/($numc*2);
        $saldo_final = 0;
        $tcn = $con->Listar("SELECT DISTINCT UPPER(sigla) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
        for($t =0; $t < count($tcn); $t++){
            $total_debito     = 'total_debito'.$t;
            $total_credito     = 'total_credito'.$t;
            $pdf->CellFitScale($tam,10,number_format($$total_debito,2,'.',','),1,0,'R');
            $pdf->CellFitScale($tam,10,number_format($$total_credito,2,'.',','),1,0,'R');
        }
        $pdf->CellFitScale(25,10,number_format($total_final,2,'.',','),1,0,'R');
        $pdf->Ln(10);

        #   ***************      ESTRUCTURA FIRMAS      ***************     #
        $pdf->SetFont('Arial','B',9);
        $pdf->Ln(30);
        $compania = $_SESSION['compania'];
        $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
               LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
               LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
               LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
               WHERE LOWER(td.nombre) ='estado de situacion financiera de apertura' AND td.compania = $compania  ORDER BY rd.orden ASC";
        $res= $mysqli->query($res);
        $i=0;
        $x=130;
        #ESTRUCTURA
        if(mysqli_num_rows($res)>0){
            $h=4;
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
                if(!empty($ter[3])){
                        $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n T.P:".(mb_strtoupper($ter[3]));
                } else {
                    $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n";
                }

                $pdf->MultiCell(110,4, utf8_decode($responsable),0,'L');

                if($i==1){
                  $pdf->Ln(15);
                  $x=130;
                  $i=0;
                } else {
                $pdf->Ln(-20);
                $pdf->SetX($x);
                $x=$x+110;
                 $i=$i+1;
                }

            }

        } 


        while (ob_get_length()) {
            ob_end_clean();
        }

        $pdf->Output(0,utf8_decode('Informe_Estado_Apertura('.date('d-m-Y').').pdf'),0);


    } elseif($tipo=='excel') {
        $numf =7+($numc*2);
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Informe_Estado_Apertura.xls");
      ?> 
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe_Estado_Apertura</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="<?php echo $numf ?>" align="center"><strong>
                <br/>&nbsp;
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
               <br/>&nbsp;
               <br/>ESTADO DE SITUACION FINANCIERA DE APERTURA
               <br/><?php echo 'AÑO: '.$anno ?>
               <br/>&nbsp;

                 </strong>
        </th>
        <?php
        # ********** Encabezado ********** #
        $annoa = $anno-1;
        echo '<tr>';
        echo '<td rowspan="2"><center><strong>CUENTA CONTABLE</strong></center></td>';
        echo '<td rowspan="2"><center><strong>NOMBRE</strong></center></td>';
        echo '<td rowspan="2"><center><strong>SALDO A 31 DE DICIEMBRE DE '.$annoa.'</strong></center></td>';

        $tcn = $con->Listar("SELECT DISTINCT UPPER(nombre) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
        for($t =0; $t<count($tcn); $t++){
            echo '<td colspan="2"><center><strong>'.ucwords($tcn[$t][0]).'</strong></center></td>';
        }
        echo '<td rowspan="2"><center><strong>SALDO A 1 DE ENERO DE '.$anno.'</strong></center></td>';
        echo '<td rowspan="2"><center><strong>CORRIENTE</strong></center></td>';
        echo '<td rowspan="2"><center><strong>NO CORRIENTE</strong></center></td>';
        echo '<td rowspan="2"><center><strong>OBSERVACIONES</strong></center></td>';
        echo '</tr>';
        echo '<tr>';
        for($t =0; $t<count($tcn); $t++){
            echo '<td><center><strong>DÉBITO</strong></center></td>';
            echo '<td><center><strong>CRÉDITO</strong></center></td>';
        }
        echo '</tr>';
        for ($i = 0; $i < count($row); $i++) {
                $id_cuenta      = $row[$i][0];
                $codi_cuenta    = $row[$i][1];
                $nombre         = $row[$i][12];
                $cod_predecesor = $row[$i][2];
                $naturaleza     = $row[$i][13];
                $saldo_inicial  = $row[$i][3];
                $debito0        = $row[$i][4];
                $credito0       = $row[$i][5];
                $debito1        = $row[$i][6];
                $credito1       = $row[$i][7];
                $debito2        = $row[$i][8];
                $credito2       = $row[$i][9];
                $debito3        = $row[$i][10];
                $credito3       = $row[$i][11];
                $cgn            = $row[$i][14];
                # ** Si Tamaño Del Codigo es 1 ** #
                $imprimir       = 0;
                if(strlen($codi_cuenta)<=1){
                   $imprimir   = 1;
                } else {

                    if(($saldo_inicial==0   || $saldo_inicial==NULL) && 
                        ($debito0   == 0    || $debito0==NULL) && 
                        ($credito0  == 0    || $credito0==NULL )&& 
                        ($debito1   == 0    || $debito1==NULL ) && 
                        ($credito1  == 0    || $credito1==NULL) && 
                        ($debito2   == 0    || $debito2==NULL) && 
                        ($credito2  == 0    || $credito2==NULL) && 
                        ($debito3   == 0    || $debito3==NULL) && 
                        ($credito3  == 0    || $credito3==NULL) ){

                        #   *** Buscar Si Los Hijos Tienen Saldo *** #
                        $rowc = $con->Listar("SELECT id_unico, 
                                SUM(IF(saldo_inicial<0, saldo_inicial*-1,saldo_inicial)),
                                SUM(IF(uno_debito<0, uno_debito*-1,uno_debito)),
                                SUM(IF(uno_credito<0, uno_credito*-1,uno_credito)),
                                SUM(IF(dos_debito<0, dos_debito*-1,dos_debito)),
                                SUM(IF(dos_credito<0, dos_credito*-1,dos_credito)),
                                SUM(IF(tres_debito<0, tres_debito*-1,tres_debito)),
                                SUM(IF(tres_credito<0, tres_credito*-1,tres_credito)),
                                SUM(IF(cuatro_debito<0, cuatro_debito*-1,cuatro_debito)),
                                SUM(IF(cuatro_credito<0, cuatro_credito*-1,cuatro_credito)) 
                                FROM temporal_estado_apertura  
                                WHERE cod_predecesor = $codi_cuenta"); 

                        if(count($rowc)>0){
                            if (($rowc[0][1] == 0 || $rowc[0][1] == NULL) 
                            && ($rowc[0][2] == 0  || $rowc[0][2] == NULL)  
                            && ($rowc[0][3] == 0  || $rowc[0][3] == NULL)
                            && ($rowc[0][4] == 0  || $rowc[0][4] == NULL) 
                            && ($rowc[0][5] == 0  || $rowc[0][5] == NULL) 
                            && ($rowc[0][6] == 0  || $rowc[0][6] == NULL) 
                            && ($rowc[0][7] == 0  || $rowc[0][7] == NULL) 
                            && ($rowc[0][8] == 0  || $rowc[0][8] == NULL) 
                            && ($rowc[0][9] == 0  || $rowc[0][9] == NULL)) {
                            } else {
                                $imprimir   = 1;
                            }
                        }
                    } else {
                        $imprimir   = 1;
                    } 
                }
                if($imprimir ==1){
                    echo '<tr>';
                    echo '<td>'.$codi_cuenta.'</td>';
                    echo '<td>'.ucwords($nombre).'</td>';
                    echo '<td>'.number_format($saldo_inicial,2,'.',',').'</td>';
                    $saldo_final = 0;
                    $saldo_final += $saldo_inicial;
                    $tcn = $con->Listar("SELECT DISTINCT UPPER(nombre) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
                    for($t =0; $t<count($tcn); $t++){
                        $debito     = 'debito'.$t;
                        $credito    = 'credito'.$t;
                        echo '<td>'.number_format($$debito,2,'.',',').'</td>';
                        echo '<td>'.number_format($$credito,2,'.',',').'</td>';
                        if($naturaleza==1){
                            $saldo_final +=($$debito-$$credito);
                        } else {
                            $saldo_final +=($$credito-$$debito);
                        }
                    }
                    echo '<td>'.number_format($saldo_final,2,'.',',').'</td>';
                    $s_corriente    =   0;
                    $s_ncorriente   =   0;

                    if($cgn==2){
                        $s_corriente    =   $saldo_final;
                    } elseif($cgn==6){
                        $s_ncorriente   =   $saldo_final;
                    }
                    echo '<td>'.number_format($s_corriente,2,'.',',').'</td>';
                    echo '<td>'.number_format($s_ncorriente,2,'.',',').'</td>';
                    echo '<td>&nbsp;</td>';
                    echo '</tr>';
                }

        }
        # ************** RESUMEN ************** #
        echo '<tr><td colspan="'.($numf).'"></td></tr>';
        echo '<tr><td colspan="'.($numf).'"><strong><center>&nbsp;<br/>RESUMEN&nbsp;<br/>&nbsp;</center></strong></td></tr>';
        echo '<tr>';
        echo '<td rowspan="2"><center><strong>CUENTA CONTABLE</strong></center></td>';
        echo '<td rowspan="2" colspan="2"><center><strong>NOMBRE</strong></center></td>';
        echo '<td rowspan="2" colspan="2"><center><strong>SALDO A 31 DE DICIEMBRE DE '.$annoa.'</strong></center></td>';

        $tcn = $con->Listar("SELECT DISTINCT UPPER(nombre) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
        for($t =0; $t<count($tcn); $t++){
            echo '<td colspan="2"><center><strong>'.ucwords($tcn[$t][0]).'</strong></center></td>';
        }
        echo '<td rowspan="2" colspan="2"><center><strong>SALDO A 1 DE ENERO DE '.$anno.'</strong></center></td>';
        echo '</tr>';
        echo '<tr>';
        for($t =0; $t<count($tcn); $t++){
            echo '<td><center><strong>DÉBITO</strong></center></td>';
            echo '<td><center><strong>CRÉDITO</strong></center></td>';
        }
        echo '</tr>';
        echo '<tr></tr>';
        $total_inicial        = 0;
        $total_debito0        = 0;
        $total_credito0       = 0;
        $total_debito1        = 0;
        $total_credito1       = 0;
        $total_debito2        = 0;
        $total_credito2       = 0;
        $total_debito3        = 0;
        $total_credito3       = 0;
        $total_final          = 0; 

        for ($i = 0; $i < count($row_r); $i++) {
            $id_cuenta      = $row_r[$i][0];
            $codi_cuenta    = $row_r[$i][1];
            $nombre         = $row_r[$i][12];
            $cod_predecesor = $row_r[$i][2];
            $naturaleza     = $row_r[$i][13];
            $saldo_inicial  = $row_r[$i][3];
            $debito0        = $row_r[$i][4];
            $credito0       = $row_r[$i][5];
            $debito1        = $row_r[$i][6];
            $credito1       = $row_r[$i][7];
            $debito2        = $row_r[$i][8];
            $credito2       = $row_r[$i][9];
            $debito3        = $row_r[$i][10];
            $credito3       = $row_r[$i][11];
            $cgn            = $row_r[$i][14];
            echo '<tr>';
            echo '<td>'.$codi_cuenta.'</td>';
            echo '<td colspan="2">'.ucwords($nombre).'</td>';
            echo '<td colspan="2">'.number_format($saldo_inicial,2,'.',',').'</td>';
            $saldo_final = 0;
            $saldo_final +=$saldo_inicial;
            $tcn = $con->Listar("SELECT DISTINCT UPPER(nombre) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
            for($t =0; $t<count($tcn); $t++){
                $debito     = 'debito'.$t;
                $credito    = 'credito'.$t;
                echo '<td>'.number_format($$debito,2,'.',',').'</td>';
                echo '<td>'.number_format($$credito,2,'.',',').'</td>';
                if($naturaleza==1){
                    $saldo_final +=($$debito-$$credito);
                } else {
                    $saldo_final +=($$credito-$$debito);
                }
                $total_debito        = 'total_debito'.$t;
                $total_credito       = 'total_credito'.$t;
                $$total_debito  += $$debito;
                $$total_credito += $$credito;

            }
            echo '<td colspan="2">'.number_format($saldo_final,2,'.',',').'</td>';
            switch ($codi_cuenta){
                case 1:
                    $total_inicial +=$saldo_inicial;
                    $total_final   +=$saldo_final;
                break;
                case 2:
                    $total_inicial -=$saldo_inicial;
                    $total_final   -=$saldo_final;
                break;
                case 3:
                    $total_inicial -=$saldo_inicial;
                    $total_final   -=$saldo_final;
                break;
                case 4:
                    $total_inicial -=$saldo_inicial;
                    $total_final   -=$saldo_final;
                break;
                case 5:
                    $total_inicial +=$saldo_inicial;
                    $total_final   +=$saldo_final;
                break;
                case 6:
                    $total_inicial +=$saldo_inicial;
                    $total_final   +=$saldo_final;
                break;
                case 7:
                    $total_inicial +=$saldo_inicial;
                    $total_final   +=$saldo_final;
                break;
                default :
                    $total_inicial +=$saldo_inicial;
                    $total_final   +=$saldo_final;
                break;

            }
            echo '</tr>';

       }
        echo '<tr>';
        echo '<td colspan="3"><strong><center>TOTALES</strong></center></td>';
        echo '<td colspan="2"><strong>'.number_format($total_inicial,2,'.',',').'</strong></td>';
        $tcn = $con->Listar("SELECT DISTINCT UPPER(sigla) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
        for($t =0; $t < count($tcn); $t++){
            $total_debito     = 'total_debito'.$t;
            $total_credito     = 'total_credito'.$t;
            echo '<td><strong>'.number_format($$total_debito,2,'.',',').'</strong></td>';
            echo '<td><strong>'.number_format($$total_credito,2,'.',',').'</strong></td>';
        }
        echo '<td colspan="2"><strong>'.number_format($total_final,2,'.',',').'</strong></td>';
        echo '</tr>';    


        ?>

    </table>
    </body>
    </html>
    <?php } 

} 
#********* Informes ESFA **************# 
elseif($informe==2 || $informe==3) {
    if(!empty($_REQUEST['s'])){
        $separador = $_REQUEST['s'];
        if($separador == 'tab') {	
            $separador = "\t";		
        }
    }
    $e      = $_REQUEST['t'];
    $html2  = "";# *Informe CGN2015_001_SI_CONVERGENCIA * #
    $html3  = "";# *CGN2015_001_ESFA_CONVERGENCIA * #
    for ($i = 0; $i < count($row_n); $i++) {
        $id_cuenta      = $row_n[$i][0];
        $codi_cuenta    = $row_n[$i][1];
        $nombre         = $row_n[$i][12];
        $cod_predecesor = $row_n[$i][2];
        $naturaleza     = $row_n[$i][13];
        $saldo_inicial  = $row_n[$i][3];
        $debito0        = $row_n[$i][4];
        $credito0       = $row_n[$i][5];
        $debito1        = $row_n[$i][6];
        $credito1       = $row_n[$i][7];
        $debito2        = $row_n[$i][8];
        $credito2       = $row_n[$i][9];
        $debito3        = $row_n[$i][10];
        $credito3       = $row_n[$i][11];
        $cgn            = $row_n[$i][14];
        $cod_impr       = $codi_cuenta;
        # Formato Código #
        $y1 = chunk_split($codi_cuenta,1,".");
        $y1 = substr($y1,0,strlen($y1)-1);
        $y = explode(".",$y1);

        switch (strlen($codi_cuenta)){
            case 2:
                $cod_impr = $y1;
            break;
            case 3:
                $cod_impr = $y[0].'.'.$y[1].$y[2];
            break;
            case 4:
                $cod_impr = $y[0].'.'.$y[1].'.'.$y[2].$y[3];
            break;
            case 5:
                $cod_impr = $y[0].'.'.$y[1].'.'.$y[2].'.'.$y[3].$y[4];
            break;
            case 6:
                $cod_impr = $y[0].'.'.$y[1].'.'.$y[2].$y[3].'.'.$y[4].$y[5];
            break;
        }
        # ** Si Tamaño Del Codigo es 1 ** #
        $imprimir       = 0;
        if(strlen($codi_cuenta)<=1){
           $imprimir   = 1;
        } else {
            if(($saldo_inicial==0   || $saldo_inicial==NULL) && 
                ($debito0   == 0    || $debito0==NULL) && 
                ($credito0  == 0    || $credito0==NULL )&& 
                ($debito1   == 0    || $debito1==NULL ) && 
                ($credito1  == 0    || $credito1==NULL) && 
                ($debito2   == 0    || $debito2==NULL) && 
                ($credito2  == 0    || $credito2==NULL) && 
                ($debito3   == 0    || $debito3==NULL) && 
                ($credito3  == 0    || $credito3==NULL) ){

                #   *** Buscar Si Los Hijos Tienen Saldo *** #
                $rowc = $con->Listar("SELECT id_unico, 
                        SUM(IF(saldo_inicial<0, saldo_inicial*-1,saldo_inicial)),
                        SUM(IF(uno_debito<0, uno_debito*-1,uno_debito)),
                        SUM(IF(uno_credito<0, uno_credito*-1,uno_credito)),
                        SUM(IF(dos_debito<0, dos_debito*-1,dos_debito)),
                        SUM(IF(dos_credito<0, dos_credito*-1,dos_credito)),
                        SUM(IF(tres_debito<0, tres_debito*-1,tres_debito)),
                        SUM(IF(tres_credito<0, tres_credito*-1,tres_credito)),
                        SUM(IF(cuatro_debito<0, cuatro_debito*-1,cuatro_debito)),
                        SUM(IF(cuatro_credito<0, cuatro_credito*-1,cuatro_credito)) 
                        FROM temporal_estado_apertura  
                        WHERE cod_predecesor = $codi_cuenta"); 

                if(count($rowc)>0){
                    if (($rowc[0][1] == 0 || $rowc[0][1] == NULL) 
                    && ($rowc[0][2] == 0  || $rowc[0][2] == NULL)  
                    && ($rowc[0][3] == 0  || $rowc[0][3] == NULL)
                    && ($rowc[0][4] == 0  || $rowc[0][4] == NULL) 
                    && ($rowc[0][5] == 0  || $rowc[0][5] == NULL) 
                    && ($rowc[0][6] == 0  || $rowc[0][6] == NULL) 
                    && ($rowc[0][7] == 0  || $rowc[0][7] == NULL) 
                    && ($rowc[0][8] == 0  || $rowc[0][8] == NULL) 
                    && ($rowc[0][9] == 0  || $rowc[0][9] == NULL)) {
                    } else {
                        $imprimir   = 1;
                    }
                }
            } else {
                $imprimir   = 1;
            } 
        }
        if($imprimir ==1){
            if($e== 3){
                $html2.='<tr>';
                $html2.='<td>D</td>';
                $html2.='<td>'.$cod_impr.'</td>';
                $html2.='<td>'.number_format($saldo_inicial,2,'.',',').'</td>';
                $html3.='<tr>';
                $html3.='<td>D</td>';
                $html3.='<td>'.$cod_impr.'</td>';
            } else {
                $html2.='D'."$separador";
                $html2.=str_replace(',',' ',$cod_impr)."$separador";
                $html2.=str_replace(',',' ',$saldo_inicial)."$separador";
                $html3.='D'."$separador";
                $html3.=str_replace(',',' ',$cod_impr)."$separador";
            }
            $saldo_final = 0;
            $saldo_final += $saldo_inicial;
            $tcn = $con->Listar("SELECT DISTINCT UPPER(nombre) FROM gf_tipo_comprobante WHERE niif=1 ORDER BY id_unico ASC");
            for($t =0; $t<count($tcn); $t++){
                $debito     = 'debito'.$t;
                $credito    = 'credito'.$t;
                if($e== 3){
                    $html2.='<td>'.number_format($$debito,2,'.',',').'</td>';
                    $html2.='<td>'.number_format($$credito,2,'.',',').'</td>';
                } else {
                    $html2.=str_replace(',',' ',$$debito)."$separador";
                    $html2.=str_replace(',',' ',$$credito)."$separador";
                }
                if($naturaleza==1){
                    $saldo_final +=($$debito-$$credito);
                } else {
                    $saldo_final +=($$credito-$$debito);
                }
            }
            $s_corriente    =   0;
            $s_ncorriente   =   0;
            if($cgn==2){
                $s_corriente    =   $saldo_final;
            } elseif($cgn==6){
                $s_ncorriente   =   $saldo_final;
            }
            if($e== 3){
                $html2.='<td>'.number_format($saldo_final,2,'.',',').'</td>';
                $html2.='<td>'.number_format($s_corriente,2,'.',',').'</td>';
                $html2.='<td>'.number_format($s_ncorriente,2,'.',',').'</td>';
                $html2.='</tr>';
                $html3.='<td>'.number_format($saldo_final,2,'.',',').'</td>';
                $html3.='</tr>';
            } else {
                $html2.=str_replace(',',' ',$saldo_final)."$separador";
                $html2.=str_replace(',',' ',$s_corriente)."$separador";
                $html2.=str_replace(',',' ',$s_ncorriente);
                $html2.= "\n";
                $html3.=str_replace(',',' ',$saldo_final);
                $html3.= "\n";
            }
        }
    }
    $titulo = "";
    $htmlI  = "";
    if($informe==2){
        $titulo = "CGN2015_001_SI_CONVERGENCIA";
    }elseif($informe==3){
        $titulo = "CGN2015_001_ESFA_CONVERGENCIA";
    }
    if($e== 3){
        $htmlI .='<tr>';
        $htmlI .='<td>S</td>';
        $htmlI .='<td>213715537</td>';
        $htmlI .='<td>10112</td>';
        $htmlI .='<td>'.$anno.'</td>';
        $htmlI .='<td>'.$titulo.'</td>';
        $htmlI .='</tr>';
    } else {
        $htmlI.='S'."$separador";
        $htmlI.='213715537'."$separador";
        $htmlI.='10112'."$separador";
        $htmlI.=$anno."$separador";
        $htmlI.=$titulo;
        $htmlI.= "\n";
    }
    if($informe==2){
        $htmlI .=$html2;
    }elseif($informe==3){
        $htmlI .=$html3;
    }
    if($_REQUEST['t']==3){
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$titulo.xls");
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<title>Informes_Estado_Convergencia</title>';
        echo '</head>';
        echo '<body>';
        echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
        echo $htmlI;
        echo '</table>';
        echo '</body>';
        echo '</html>';
    } else {
        header("Content-Disposition: attachment; filename=$titulo.txt");
        ini_set('max_execution_time', 0);
        echo $htmlI;
    }?>

<?php    

}
?>