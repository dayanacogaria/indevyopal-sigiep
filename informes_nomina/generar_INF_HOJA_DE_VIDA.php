<?php
#************ 28/09/2021- Elkin O- Se crea hoja de vida en PDF***********#
setlocale(LC_TIME, 'es_ES');
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
ini_set('max_execution_time', 0); 
session_start();
$con  = new ConexionPDO();
$anno = $_SESSION['anno'];
$tipo = $_REQUEST['t'];
$compania = $_SESSION['compania'];
$empleado = (($_GET["id"]));


#   ************   Datos Compañia   ************    #
$rowC = $con->Listar("SELECT
ter.razonsocial,
ter.nombre_comercial,         
UPPER(ti.nombre),
ter.numeroidentificacion,
ciudad.nombre,
dir.direccion,
tel.valor,
ter.ruta_logo,
ter.email
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
LEFT JOIN gf_ciudad ciudad ON ciudad.id_unico = ter.ciudadidentificacion
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][0];
$nombreEm = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$ciudad = $rowC[0][4];
$direccinTer = $rowC[0][5];
$telefonoTer = $rowC[0][6];
$ruta_logo   = $rowC[0][7];
$email   = $rowC[0][8];
$t     = ''; 
#   ************   Datos Empleado   ************    #
    $rowE=$con->Listar("SELECT 
                    CONCAT_WS(' ',
                     ter.nombreuno,
                     ter.nombredos,
                     ter.apellidouno,
                     ter.apellidodos) AS nombree,
                     (CASE WHEN ca.nombre=ca.nombre THEN ca.nombre ELSE 'CARGO SIN REGISTRAR' END) AS cargo ,
                     (CASE WHEN ue.nombre=ue.nombre THEN ue.nombre ELSE 'UNIDAD SIN REGISTRAR' END ) AS unidadeje,
                     (CASE WHEN gg.nombre=gg.nombre THEN gg.nombre ELSE 'GRUPO SIN REGISTRAR' END ) AS grupog,                         
                     (CASE WHEN  cc.nombre= cc.nombre THEN  cc.nombre ELSE 'CENTRO COSTO SIN REGISTRAR' END ) AS centroco,             
                     DATE_FORMAT(ter.fecha_nacimiento, '%d/%m/%Y') AS fechana,
                     TIMESTAMPDIFF(YEAR,ter.fecha_nacimiento,CURDATE()) AS edad,
                     (CASE WHEN  MAX(d.direccion)= MAX(d.direccion) THEN   MAX(d.direccion) ELSE 'DIRECCION SIN REGISTRAR' END ) AS direccion, 
                     (CASE WHEN  c.nombre= c.nombre THEN  c.nombre ELSE 'MUNICIPIO SIN REGISTRAR' END ) AS ciudad,
                     (CASE WHEN  dep.nombre= dep.nombre THEN  dep.nombre ELSE 'DEPARTAMENTO SIN REGISTRAR' END ) AS departamento,  
                     (CASE WHEN   MAX(t.valor)=  MAX(t.valor) THEN   MAX(t.valor) ELSE 'TELEFONO SIN REGISTRAR' END ) AS telefono, 
                      DATE_FORMAT(vr.fechaacto, '%d/%m/%Y') AS fechaing,
                     (CASE WHEN rc.nombre=rc.nombre THEN rc.nombre ELSE 'REGIMEN SIN REGISTRAR' END) AS regimen,
                     (CASE WHEN cb1.descripcion=cb1.descripcion THEN cb1.descripcion ELSE 'CUENTA SIN REGISTRAR' END) AS cuentab,
                      e.tipo_riesgo,
                      vr.fechaacto,
                      cat.salarioactual
                      FROM gn_empleado e
                      LEFT JOIN   gf_tercero ter          ON e.tercero = ter.id_unico
                      LEFT JOIN   gn_estado_empleado ee   ON e.estado = ee.id_unico
                      LEFT JOIN   gn_regimen_cesantias rc ON e.cesantias = rc.id_unico
                      LEFT JOIN   gn_medio_pago mp        ON e.mediopago = mp.id_unico
                      LEFT JOIN   gn_unidad_ejecutora ue  ON e.unidadejecutora = ue.id_unico
                      LEFT JOIN   gn_grupo_gestion gg     ON e.grupogestion = gg.id_unico
                      LEFT JOIN gf_cargo_tercero ct       ON ct.tercero = e.tercero
                      LEFT JOIN gf_cargo ca               ON ca.id_unico = ct.cargo
                      LEFT JOIN gn_empleado_centro_costo ecc ON ecc.empleado=e.id_unico
                      LEFT JOIN gf_centro_costo cc        ON cc.id_unico=ecc.centro_costo
                      LEFT JOIN gf_direccion d            ON d.tercero=ter.id_unico
                      LEFT JOIN gf_ciudad c               ON c.id_unico=ter.ciudadresidencia
                      LEFT JOIN gf_departamento dep       ON dep.id_unico=c.departamento
                      LEFT JOIN gf_telefono t             ON t.tercero=ter.id_unico
                      LEFT JOIN gn_vinculacion_retiro vr  ON vr.empleado=e.id_unico
                      LEFT JOIN gf_cuenta_bancaria_tercero cb ON cb.tercero=ter.id_unico
                      LEFT JOIN gf_cuenta_bancaria cb1    ON cb1.id_unico=cb.cuentabancaria
                      LEFT JOIN gn_tercero_categoria tc   ON tc.empleado = e.id_unico
                      LEFT JOIN gn_categoria cat          ON cat.id_unico = tc.categoria
                      WHERE vr.estado=1
                      AND   ter.id_unico=$empleado");

$fechare=$con->Listar("SELECT ter.id_unico,
                              DATE_FORMAT(vr.fechaacto,'%d/%m/%Y') AS retiro,
                              vr.fechaacto
                              FROM   gf_tercero ter
                                     LEFT JOIN gn_vinculacion_retiro vr
                                     ON vr.empleado = ter.id_unico
                              WHERE  vr.estado = 2
                              AND ter.id_unico =$empleado");

$fechai=$rowE[0][15];
$fechareee=$fechare[0][2];
if($fechareee==null){
    $fecha_ingreso = new DateTime($fechai);
    $fecha_retiro = new DateTime();
    $edad = $fecha_retiro->diff($fecha_ingreso);
    $añoss=$edad->y.' años';
    

}else{
$fecha_ingreso = new DateTime($fechai);
$fecha_retiro = new DateTime($fechareee);
$edad = $fecha_retiro->diff($fecha_ingreso);
$añoss=$edad->y.' años';
}

                            
#**************** Consultas en general****************#


 $afilia="SELECT     ta.nombre,
                    ter.razonsocial
                    FROM gn_afiliacion a	 
                    LEFT JOIN	gn_empleado e           ON a.empleado = e.id_unico
                    LEFT JOIN   gf_tercero t            ON e.tercero = t.id_unico
                    LEFT JOIN   gn_tipo_afiliacion ta   ON a.tipo = ta.id_unico
                    LEFT JOIN   gf_tercero ter          ON a.tercero = ter.id_unico
                    LEFT JOIN   gn_estado_afiliacion ea ON a.estado = ea.id_unico
                    WHERE t.id_unico=$empleado";
$afiliacion = $mysqli->query($afilia);
$riesgo=$rowE[0][14];

$tipor="SELECT 'TIPO RIESGO' AS titulo, nombre, ROUND(valor,2) 
             FROM gn_categoria_riesgos 
            WHERE id_unico =$riesgo";
$tiporiesgo=$mysqli->query($tipor);

$estud="SELECT es.titulo,
               ie.nombre,
               te.nombre,
               Date_format(es.fechaterminacion, '%d/%m/%Y') AS fecha,
               es.numerosemestres,
               es.tarjetaprofesional
                FROM   gn_estudio es
                LEFT JOIN gn_empleado e
                       ON es.empleado = e.id_unico
                LEFT JOIN gf_tercero t
                       ON e.tercero = t.id_unico
                LEFT JOIN gn_tipo_estudio te
                       ON es.tipo = te.id_unico
                LEFT JOIN gn_institucion_educativa ie
                       ON es.institucioneducativa = ie.id_unico
                    WHERE t.id_unico=$empleado"; 

$estudio=$mysqli->query($estud);      

$estudios=$con->Listar("SELECT es.titulo,
               ie.nombre,
               te.nombre,
               Date_format(es.fechaterminacion, '%d/%m/%Y') AS fecha,
               es.numerosemestres,
               es.tarjetaprofesional
                FROM   gn_estudio es
                LEFT JOIN gn_empleado e
                       ON es.empleado = e.id_unico
                LEFT JOIN gf_tercero t
                       ON e.tercero = t.id_unico
                LEFT JOIN gn_tipo_estudio te
                       ON es.tipo = te.id_unico
                LEFT JOIN gn_institucion_educativa ie
                       ON es.institucioneducativa = ie.id_unico
                    WHERE t.id_unico=$empleado");
$familiar=$con->Listar("SELECT tr.nombre,
                        Concat(ter.nombreuno, ' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)
                        FROM   gn_familiar f
                           LEFT JOIN gn_empleado e
                                  ON f.empleado = e.id_unico
                           LEFT JOIN gf_tercero t
                                  ON e.tercero = t.id_unico
                           LEFT JOIN gn_tipo_relacion tr
                                  ON f.tiporelacion = tr.id_unico
                           LEFT JOIN gf_tercero ter
                                  ON f.tercero = ter.id_unico 
                                  WHERE t.id_unico=$empleado");    

 $idio=$con->Listar("SELECT 
                            id.nombre,
                            ie.lee,
                            ie.escribe,
                            ie.habla
                     FROM   gn_idioma_empleado ie
                            LEFT JOIN gn_empleado e
                                   ON ie.empleado = e.id_unico
                            LEFT JOIN gf_tercero t
                                   ON e.tercero = t.id_unico
                            LEFT JOIN gn_idioma id
                                   ON ie.idioma = id.id_unico
                     WHERE  t.id_unico = $empleado ");

                                  
                                  


 $laboral=$con->Listar("SELECT l.entidad_laboral,
                    td.nombre,
                    de.nombre,
                    ca.nombre,
                    cr.nombre,
                    DATE_FORMAT(l.fechaingreso,'%d/%m/%Y'),
                    DATE_FORMAT(l.fecharetiro,'%d/%m/%Y')
                    FROM   gn_laboral l
                          LEFT JOIN gn_empleado e
                                 ON l.empleado = e.id_unico
                          LEFT JOIN gf_tercero t
                                 ON e.tercero = t.id_unico
                          LEFT JOIN gf_tercero ter
                                   ON ter.id_unico=l.entidad 
                          LEFT JOIN gn_dependencia_empleado de
                                 ON l.dependencia = de.id_unico
                          LEFT JOIN gf_cargo ca
                                 ON l.cargo = ca.id_unico
                          LEFT JOIN gn_causa_retiro cr
                                 ON l.causaretiro = cr.id_unico
                          LEFT JOIN gn_tipo_dedicacion td
                                 ON l.tipodedicacion = td.id_unico
                         WHERE  t.id_unico=$empleado");   
                         
 $hojad=$con->Listar("SELECT td.nombre,
                             ed.ruta,
                             Date_format(ed.fechaactualizacion, '%d/%m/%Y'),
                             ed.numerofolio
                      FROM   gn_empleado_documento ed
                             LEFT JOIN gf_tipo_documento td
                                    ON td.id_unico = ed.tipodocumento
                             LEFT JOIN gn_empleado e
                                    ON e.id_unico = ed.empleado
                             LEFT JOIN gf_tercero t
                                    ON e.tercero = t.id_unico
                      WHERE  t.id_unico =$empleado 
                      ORDER  BY ed.id_unico ASC ");    
                         
                         $months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                         $fechactM = $months[(int) date("m")];
                         $fechaActual =date('d').' de '.$fechactM.' de '.date(Y);         

 #   ************   Armado de documento   ************    #

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
            global $emple;
            global $ciudad;
            global $email;
            global $afiliacion;
            global $afilia;
            $numpaginas=$numpaginas+1;
            $this->SetFont('Arial','B',10);
            if($ruta_logo != '')
            {
              $this->Image('../'.$ruta_logo,10,5,28);
            }
            $this->SetY(10);
            $this->SetFont('Arial','',10);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower($razonsocial))),0,0,'R');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower($direccinTer))),0,0,'R');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower($email))),0,0,'R');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower('NIT '.$numeroIdent))),0,0,'R');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower('TEL.'.$telefonoTer))),0,0,'R');
            $this->ln(5);
            $this->Cell(200,5,utf8_decode(ucwords(mb_strtolower($ciudad))),0,0,'R');
            $this->ln(10);
        }      
        function Footer(){
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(260,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
             
    }
    $pdf = new PDF('P','mm','Letter');   
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetY(40);
    $pdf->SetFont('Arial','B',10);
    $pdf->Ln();
    $pdf->Cell(200,5,("INFORMACION GENERAL"),1,0,"C");
    $pdf->Ln();
    $pdf->Cell(70,5,("NOMBRE"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[0][0]),1,0);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,("CARGO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,utf8_decode(ucwords(mb_strtolower($rowE[0][1]))),1,0);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,("SUELDO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,'$'.utf8_decode(ucwords(mb_strtolower(number_format($rowE[0][16])))),1,0);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->SetXY(10, 65);
    $pdf->Rect(10, 65, 70, 10);
    $pdf->Cell(70,5,"UNIDAD EJECUTORA",0);
    $pdf->SetFont('Arial','',10);
    $pdf->MultiCell(130,5,utf8_decode($rowE[0][2]),1,"L"); 
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,utf8_decode("GRUPO GESTIÓN"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,utf8_decode($rowE[0][3]),1,0);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,("CENTRO DE COSTO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,utf8_decode($rowE[0][4]),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,("FECHA NACIMIENTO "),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,utf8_decode($rowE[0][5]),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,("EDAD"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,utf8_decode($rowE[0][6].' años'),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,utf8_decode("DIRECCIÓN"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,utf8_decode(ucwords(mb_strtolower($rowE[0][7]))),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,("DEPARTAMENTO/MUNICIPIO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[0][8].'/'.$rowE[0][9]),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,("NUMERO TELEFONO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,utf8_decode($rowE[0][10]),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,("FECHA INGRESO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,utf8_decode($rowE[0][11]),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,("FECHA RETIRO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($fechare[0][1]),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,utf8_decode("AÑOS DE SERVICIO"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,utf8_decode($añoss),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,utf8_decode("REGIMEN DE CESANTÍAS"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,($rowE[0][12]),1);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,("CUENTA BANCARIA"),1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5,utf8_decode($rowE[0][13]),1);
    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("AFILIACIONES"),1,0,"C");
    $pdf->Ln();
        while($rowA = mysqli_fetch_row($afiliacion)){
            $pdf->SetFont('Arial','B',10);
            $pdf->Cellfitscale(70,5,utf8_decode($rowA[0]),1,0,'L');
            $pdf->SetFont('Arial','',10);
            $pdf->Cellfitscale(130,5,utf8_decode($rowA[1]),1,0,'L');
            $pdf->Ln();
                        
               }
         while($rowR = mysqli_fetch_row($tiporiesgo)){
                $pdf->SetFont('Arial','B',10);
                $pdf->Cellfitscale(70,5,utf8_decode($rowR[0]),1,0,'L');
                $pdf->SetFont('Arial','',10);
                $pdf->Cellfitscale(130,5,utf8_decode($rowR[1].' - '.$rowR[2].'%'),1,0,'L');         
         }
    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,("ESTUDIOS"),1,0,"C");
    $pdf->Ln(); 
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(33,5,("TITULO OBTENIDO"),1,0,"C");
    $pdf->Cell(33,5,("INSTITUCION EDUCATIVA"),1,0,"C");
    $pdf->Cell(33,5,("TIPO"),1,0,"C");
    $pdf->Cell(33,5,("FECHA TERMINACION"),1,0,"C");
    $pdf->Cell(33,5,("NUMERO SEMESTRES"),1,0,"C");
    $pdf->Cell(35,5,("TARJETA PROFESIONAL"),1,0,"C");
    $pdf->Ln();

    for ($c=0; $c < count($estudios) ; $c++) {
        $pdf->SetFont('Arial','',10);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell(33,5,utf8_decode($estudios[$c][0]) ,0,'L');
        $h1 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+33, $y);
        $pdf->MultiCell(33,5,utf8_decode($estudios[$c][1]) ,0,'L');
        $h2 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+66, $y);
        $pdf->MultiCell(33,5,($estudios[$c][2]) ,0,'L');
        $h3 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+99, $y);
        $pdf->MultiCell(33,5,utf8_decode($estudios[$c][3]) ,0,'L');
        $h4 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+132, $y);
        $pdf->MultiCell(33,5,utf8_decode($estudios[$c][4]) ,0,'L');
        $h5 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+165, $y);
        $pdf->MultiCell(35,5,utf8_decode($estudios[$c][5]) ,0,'L');

        $alt = max($h1, $h2,$h3,$h4,$h5);
        $pdf->SetXY($x, $y);
        $pdf->Cell(33, $alt,'' ,1,0,'L');
        $pdf->Cell(33,$alt,'' ,1,0,'L');
        $pdf->Cell(33,$alt,'' ,1,0,'L');
        $pdf->Cell(33,$alt,'' ,1,0,'L');
        $pdf->Cell(33,$alt,'' ,1,0,'L');
        $pdf->Cell(35,$alt,'' ,1,0,'L');
        $pdf->Ln($alt);
        $totalie +=$rowc[$c][8];
        if($pdf->GetY()>170){
            $pdf->AddPage();
        }
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,utf8_decode("INFORMACIÓN FAMILIAR"),1,0,"C");
    $pdf->Ln();
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(100,5,utf8_decode("PARENTESCO(RELACIÓN)"),1,0,"C");
    $pdf->Cell(100,5,("NOMBRES"),1,0,"C");
    $pdf->Ln();

    for ($c=0; $c < count($familiar) ; $c++) {
        $pdf->SetFont('Arial','',10);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell(100,5,utf8_decode($familiar[$c][0]) ,0,'L');
        $h1 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+100, $y);
        $pdf->MultiCell(100,5,utf8_decode($familiar[$c][1]) ,0,'L');
        $h2 = ($pdf->GetY()-$y);

        $alt = max($h1,$h2);
        $pdf->SetXY($x, $y);
        $pdf->Cell(100, $alt,'' ,1,0,'L');
        $pdf->Cell(100,$alt,'' ,1,0,'L');
        $pdf->Ln($alt);
        $totalie +=$rowc[$c][8];
        if($pdf->GetY()>170){
            $pdf->AddPage();
        }
    }




    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,utf8_decode("IDIOMAS"),1,0,"C");
    $pdf->Ln();
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(50,5,utf8_decode("IDIOMA"),1,0,"C");
    $pdf->Cell(50,5,("LEE"),1,0,"C");
    $pdf->Cell(50,5,("ESCRIBE"),1,0,"C");
    $pdf->Cell(50,5,("HABLA"),1,0,"C");
    $pdf->Ln();
    for ($c=0; $c < count($idio) ; $c++) {
        $pdf->SetFont('Arial','',10);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell(50,5,($idio[$c][0]) ,0,'L');
        $h1 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+50, $y);
        $pdf->MultiCell(50,5,utf8_decode($idio[$c][1]) ,0,'L');
        $h2 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+100, $y);
        $pdf->MultiCell(50,5,utf8_decode($idio[$c][2]) ,0,'L');
        $h3 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+150, $y);
        $pdf->MultiCell(50,5,utf8_decode($idio[$c][3]) ,0,'L');

        $alt = max($h1,$h2,$h3);
        $pdf->SetXY($x, $y);
        $pdf->Cell(50, $alt,'' ,1,0,'L');
        $pdf->Cell(50,$alt,'' ,1,0,'L');
        $pdf->Cell(50,$alt,'' ,1,0,'L');
        $pdf->Cell(50,$alt,'' ,1,0,'L');
        $pdf->Ln($alt);
        $totalie +=$rowc[$c][8];
        if($pdf->GetY()>170){
            $pdf->AddPage();
        }
    }




    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,utf8_decode("INFORMACIÓN LABORAL"),1,0,"C");
    $pdf->Ln();
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(29,5,utf8_decode("EMPRESA"),1,0,"C");
    $pdf->Cell(28,5,utf8_decode("TIPO VINCULACIÓN"),1,0,"C");
    $pdf->Cell(28,5,("DEPENDENCIA"),1,0,"C");
    $pdf->Cell(28,5,("CARGO"),1,0,"C");
    $pdf->Cell(29,5,("CAUSA RETIRO"),1,0,"C");
    $pdf->Cell(29,5,("FECHA INGRESO"),1,0,"C");
    $pdf->Cell(29,5,("FECHA"."\n"."RETIRO"),1,0,"C");
    $pdf->Ln();
       for ($c=0; $c < count($laboral) ; $c++) {
        $pdf->SetFont('Arial','',10);
        $x = $pdf->GetX();
        $y = $pdf->GetY();

       
        $pdf->MultiCell(29,6,utf8_decode($laboral[$c][0]) ,0,'L');
        $h1 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+29, $y);
        $pdf->MultiCell(28,6,utf8_decode($laboral[$c][1]) ,0,'L');
        $h2 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+57, $y);
        $pdf->MultiCell(28,6,utf8_decode($laboral[$c][2]) ,0,'L');
        $h3 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+85, $y);
        $pdf->MultiCell(28,5,utf8_decode($laboral[$c][3]) ,0,'L');
        $h4 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+113, $y);
        $pdf->MultiCell(29,5,utf8_decode($laboral[$c][4]) ,0,'L');
        $h5 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+142, $y);
        $pdf->MultiCell(29,5,utf8_decode($laboral[$c][5]) ,0,'L');
        $h6 = ($pdf->GetY()-$y);
        $pdf->SetXY($x+171, $y);
        $pdf->MultiCell(29,5,utf8_decode($laboral[$c][6]) ,0,'L');


        $alt = max($h1, $h2,$h3,$h4,$h5,$h6);
        $pdf->SetXY($x, $y);
        $pdf->Cell(29, $alt,'' ,1,0,'L');
        $pdf->Cell(28,$alt,'' ,1,0,'L');
        $pdf->Cell(28,$alt,'' ,1,0,'L');
        $pdf->Cell(28,$alt,'' ,1,0,'L');
        $pdf->Cell(29,$alt,'' ,1,0,'L');
        $pdf->Cell(29,$alt,'' ,1,0,'L');
        $pdf->Cell(29,$alt,'' ,1,0,'L');
        $pdf->Ln($alt);
        $totalie +=$rowc[$c][8];
        if($pdf->GetY()>170){
            $pdf->AddPage();
        }
    }
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(200,5,utf8_decode("DOCUMENTOS HOJA DE VIDA"),1,0,"C");
    $pdf->Ln();
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(50,5,utf8_decode("TIPO DOCUMENTO"),1,0,"C");
    $pdf->Cell(50,5,utf8_decode("NOMBRE DOCUMENTO"),1,0,"C");
    $pdf->Cell(50,5,utf8_decode("FECHA ACTUALIZACIÓN"),1,0,"C");
    $pdf->Cell(50,5,("NUMERO DE FOLIO"),1,0,"C");
    $pdf->Ln();

    for ($c=0; $c < count($hojad) ; $c++) {
       $pdf->SetFont('Arial','',10);
       $x = $pdf->GetX();
       $y = $pdf->GetY();
       $pdf->MultiCell(50,5,utf8_decode($hojad[$c][0]) ,0,'L');
       $h1 = ($pdf->GetY()-$y);
       $pdf->SetXY($x+50, $y);
       $pdf->MultiCell(50,5,utf8_decode($hojad[$c][1]) ,0,'L');
       $h2 = ($pdf->GetY()-$y);
       $pdf->SetXY($x+100, $y);
       $pdf->MultiCell(50,5,utf8_decode($hojad[$c][2]) ,0,'L');
       $h3 = ($pdf->GetY()-$y);
       $pdf->SetXY($x+150, $y);
       $pdf->MultiCell(50,5,utf8_decode($hojad[$c][3]) ,0,'L');
       $h4 = ($pdf->GetY()-$y);

       $alt = max($h1,$h2,$h3,$h4);
       $pdf->SetXY($x, $y);
       $pdf->Cell(50, $alt,'' ,1,0,'L');
       $pdf->Cell(50,$alt,'' ,1,0,'L');
       $pdf->Cell(50, $alt,'' ,1,0,'L');
       $pdf->Cell(50,$alt,'' ,1,0,'L');
       $pdf->Ln($alt);
       $totalie +=$rowc[$c][8];
       if($pdf->GetY()>170){
           
       }
   }
   $pdf->Ln(2);
   $pdf->SetFont('Arial','B',10);
   $pdf->Cell(50,5,utf8_decode('Impreso el:      '.$fechaActual) ,0,'L');



    ob_end_clean();		
    $pdf->Output(0,'eje.pdf',0);


 

    ?>
   
        
<?php ?>