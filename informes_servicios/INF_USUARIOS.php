<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#14/02/2019| Erica G. | Archivo Creado
#####################################################################################

require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);

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

#***********************************************************************#
$row = $con->Listar("SELECT uvms.id_unico, s.codigo, s.nombre, 
p.codigo_catastral, IF(CONCAT_WS(' ',
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
p.direccion, u.nombre, 
e.codigo, e.nombre, 
es.codigo, es.nombre, 
m.referencia, em.nombre , uv.id_unico, 
uv.codigo_ruta, 
uv.codigo_interno 
FROM 
gp_unidad_vivienda_medidor_servicio uvms 
LEFT JOIN 
	gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
LEFT JOIN 
	gp_medidor m ON uvms.medidor = m.id_unico 
LEFT JOIN 
	gp_estado_medidor em ON m.estado_medidor = em.id_unico 
LEFT JOIN 
	gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico
LEFT JOIN 
	gp_predio1 p ON uv.predio = p.id_unico 
LEFT JOIN 
	gf_tercero t ON uv.tercero = t.id_unico 
LEFT JOIN 
	gp_uso u ON uv.uso = u.id_unico 
LEFT JOIN 
	gp_estrato e ON uv.estrato = e.id_unico
LEFT JOIN 
	gr_estado_predio es ON p.estado = es.id_unico 
LEFT JOIN 
    gp_sector s ON uv.sector = s.id_unico 
ORDER BY 
    s.codigo, cast((replace(uv.codigo_ruta, '.','')) as unsigned) ASC");
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
            global $direccinTer;
            global $telefonoTer;
            global $ruta_logo;
            if($ruta_logo != '')
            {
              $this->Image('../'.$ruta_logo,20,6,20);
            }
            $this->SetFont('Arial','B',12);
            $this->Cell(280,5,utf8_decode($razonsocial),0,0,'C');
            $this->Ln(5);
            $this->Cell(280, 5,$nombreIdent.': '.$numeroIdent,0,0,'C'); 
            $this->Ln(5);
            $this->Cell(280, 5,$direccinTer.' TEL:'.$telefonoTer,0,0,'C'); 
            $this->Ln(7);
            $this->Cell(280,5,utf8_decode('LISTADO DE USUARIOS '),0,0,'C');
            $this->Ln(8);
            $this->SetFont('Arial','B',8);
            $this->Cell(25,10, utf8_decode(''),1,0,'C');
            $this->Cell(15,10,utf8_decode(''),1,0,'C');
            $this->Cell(15,10,utf8_decode(''),1,0,'C');
            $this->Cell(15,10,utf8_decode(''),1,0,'C');
            $this->Cell(30,10,utf8_decode(''),1,0,'C');
            $this->Cell(30,10,utf8_decode(''),1,0,'C');
            $this->Cell(25,10,utf8_decode(''),1,0,'C');
            $this->Cell(25,10,utf8_decode(''),1,0,'C');
            $this->Cell(25,10,utf8_decode(''),1,0,'C');
            $this->Cell(25,10,utf8_decode(''),1,0,'C');
            $this->Cell(12,10,utf8_decode(''),1,0,'C');
            $this->Cell(12,10,utf8_decode(''),1,0,'C');
            $this->Cell(12,10,utf8_decode(''),1,0,'C');
            $this->Setx(10);
            $this->Cell(25,10, utf8_decode('Sector'),0,0,'C');
            $this->Cell(15,10,utf8_decode('Código S'),0,0,'C');
            $this->Cell(15,10,utf8_decode('Código R'),0,0,'C');
            $this->Cell(15,10,utf8_decode('Código I'),0,0,'C');
            $this->Cell(30,10,utf8_decode('Nombre'),0,0,'C');
            $this->Cell(30,10,utf8_decode('Dirección'),0,0,'C');
            $this->Cell(25,10,utf8_decode('Uso'),0,0,'C');
            $this->Cell(25,10,utf8_decode('Estrato'),0,0,'C');
            $this->Cell(25,10,utf8_decode('Estado'),0,0,'C');
            $this->Cell(25,10,utf8_decode('Medidor'),0,0,'C');
            $this->Cell(12,10,utf8_decode('Acdto'),0,0,'C');
            $this->Cell(12,10,utf8_decode('Alc.'),0,0,'C');
            $this->Cell(12,10,utf8_decode('Aseo'),0,0,'C');
            $this->Ln(10);
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

    $pdf = new PDF('L','mm','Letter');
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial','',9);
    $total = 0;
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    for ($i = 0; $i < count($row); $i++) {
        $x1 = $pdf->GetX();
        $y1 = $pdf->GetY();
        $pdf->MultiCell(25,5, utf8_decode($row[$i][1].' - '.ucwords(mb_strtolower($row[$i][2]))),0,'L');
        $y2 = $pdf->GetY();
        $h1 = $y2 - $y1;        
        $pdf->SetXY($x1+25,$y1);        
        $pdf->Cell(15,5,utf8_decode($row[$i][3]),0,0,'L');
        $pdf->Cell(15,5,utf8_decode($row[$i][14]),0,0,'L');
        $pdf->Cell(15,5,utf8_decode($row[$i][15]),0,0,'L');
        $x2 = $pdf->GetX();
        $pdf->MultiCell(30,5,utf8_decode(ucwords(mb_strtolower($row[$i][4]))),0,'L');
        $y3 = $pdf->GetY();
        $h2 = $y3 - $y1;                
        $pdf->SetXY($x2+30,$y1);
        $x3 = $pdf->GetX();
        $pdf->MultiCell(30,5,utf8_decode($row[$i][5]),0,'L');
        $y4 = $pdf->GetY();
        $h3 = $y4 - $y1;                
        $pdf->SetXY($x3+30,$y1);
        $x4 = $pdf->GetX();
        $pdf->CellFitScale(25,5,utf8_decode($row[$i][6]),0,0,'L');
        $pdf->CellFitScale(25,5,utf8_decode($row[$i][7].' - '.ucwords(mb_strtolower($row[$i][8]))),0,0,'L');
        $pdf->CellFitScale(25,5,utf8_decode($row[$i][9].' - '.ucwords(mb_strtolower($row[$i][10]))),0,0,'L');
        $pdf->CellFitScale(25,5,utf8_decode($row[$i][11].' - '.ucwords(mb_strtolower($row[$i][12]))),0,0,'L');
        $a = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio WHERE tipo_servicio = 1 AND unidad_vivienda = ".$row[$i][13]);
        if(count($a)>0){
            $pdf->Cell(12,5,utf8_decode('X'),0,0,'C');
        } else {
            $pdf->Cell(12,5,utf8_decode(''),0,0,'L');
        }
        $a = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio WHERE tipo_servicio = 2 AND unidad_vivienda = ".$row[$i][13]);
        if(count($a)>0){
            $pdf->Cell(12,5,utf8_decode('X'),0,0,'C');
        } else {
            $pdf->Cell(12,5,utf8_decode(''),0,0,'L');
        }
        $a = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio WHERE tipo_servicio = 3 AND unidad_vivienda = ".$row[$i][13]);
        if(count($a)>0){
            $pdf->Cell(12,5,utf8_decode('X'),0,0,'C');
        } else {
            $pdf->Cell(12,5,utf8_decode(''),0,0,'L');
        }
        #Ymx 
        $alt = max($h1, $h2, $h3, $h4);
        $pdf->SetXY($x1, $y1);
        $pdf->Cell(25,$alt, utf8_decode(''),1,0,'C');
        $pdf->Cell(15,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(15,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(15,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(30,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(30,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(25,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(25,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(25,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(25,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(12,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(12,$alt,utf8_decode(''),1,0,'C');
        $pdf->Cell(12,$alt,utf8_decode(''),1,0,'C');
        $pdf->Ln($alt);
        
        
        
        $altp = $pdf->GetY();
        if($altp>180){
            $pdf->AddPage();
        }
    }
    while (ob_get_length()) {
        ob_end_clean();
    }
    $pdf->Output(0,utf8_decode('Informe_Usuarios('.date('d-m-Y').').pdf'),0);
} else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Usuario.xls");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Usuarios</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
           <th colspan="11" align="center"><strong>
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;
                <br/>LISTADO DE USUARIOS
                <br/>&nbsp;</strong>
            </th>
            <tr></tr>
            
            <?php 
            echo '<tr>';
            echo '<td><strong>Sector</strong></td>';
            echo '<td><strong>Código S</strong></td>';
            echo '<td><strong>Código Ruta </strong></td>';
            echo '<td><strong>Código Interno</strong></td>';
            echo '<td><strong>Nombre</strong></td>';
            echo '<td><strong>Dirección</strong></td>';
            echo '<td><strong>Uso</strong></td>';
            echo '<td><strong>Estrato</strong></td>';
            echo '<td><strong>Estado</strong></td>';
            echo '<td><strong>Medidor</strong></td>';
            echo '<td><strong>Acueducto</strong></td>';
            echo '<td><strong>Alcantarillado</strong></td>';
            echo '<td><strong>Aseo</strong></td>';
            echo '</tr>';        
            for ($i = 0; $i < count($row); $i++) {
                $id_uvms = $row[$i][0];
               
                echo '<tr>';
                echo '<td>'.$row[$i][1].' - '.ucwords(mb_strtolower($row[$i][2])).'</td>';
                echo '<td style="mso-number-format:\@">'.$row[$i][3].'</td>';
                echo '<td style="mso-number-format:\@">'.$row[$i][14].'</td>';
                echo '<td style="mso-number-format:\@">'.$row[$i][15].'</td>';
                echo '<td>'.ucwords(mb_strtolower($row[$i][4])).'</td>';
                echo '<td>'.$row[$i][5].'</td>';
                echo '<td>'.$row[$i][6].'</td>';
                echo '<td>'.$row[$i][7].' - '.ucwords(mb_strtolower($row[$i][8])).'</td>';
                echo '<td>'.$row[$i][9].' - '.ucwords(mb_strtolower($row[$i][10])).'</td>';
                echo '<td>'.$row[$i][11].' - '.ucwords(mb_strtolower($row[$i][12])).'</td>';
                #**¨Buscar servicios 
                $a = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio WHERE tipo_servicio = 1 AND unidad_vivienda = ".$row[$i][13]);
                if(count($a)>0){
                    echo '<td align="center">X</td>';
                } else {
                    echo '<td></td>';
                }                
                $a = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio WHERE tipo_servicio = 2 AND unidad_vivienda = ".$row[$i][13]);
                if(count($a)>0){
                    echo '<td align="center">X</td>';
                } else {
                    echo '<td></td>';
                }
                $a = $con->Listar("SELECT * FROM gp_unidad_vivienda_servicio WHERE tipo_servicio = 3 AND unidad_vivienda = ".$row[$i][13]);
                if(count($a)>0){
                    echo '<td align="center">X</td>';
                } else {
                    echo '<td></td>';
                }
                echo '</tr>';

             }
            
            ?>
        </table>
    </body>
</html>
<?php } ?>