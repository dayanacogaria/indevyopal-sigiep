<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Facturacion_Subsidios.xls");
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);

#   ************    Datos Recibe    ************    #
$id_sector       = $_REQUEST['s1'];
$id_sectorf      = $_REQUEST['s2'];
$id_periodo      = $_REQUEST['p'];
$p = $con->Listar("SELECT DISTINCT id_unico, 
    nombre, 
    DATE_FORMAT(fecha_inicial, '%d/%m/%Y'),
    DATE_FORMAT(fecha_final, '%d/%m/%Y')                                       
    FROM gp_periodo p 
    WHERE id_unico=".$_REQUEST['p']);
$periodo =ucwords(mb_strtolower($p[0][1])).'  '.$p[0][2].' - '.$p[0][3];
$s = $con->Listar("SELECT DISTINCT id_unico, 
    nombre, codigo
    FROM gp_sector 
    WHERE id_unico =".$_REQUEST['s1']);
$sector = $s[0][2].' - '.ucwords(mb_strtolower($s[0][1]));
$s2 = $con->Listar("SELECT DISTINCT id_unico, 
    nombre, codigo
    FROM gp_sector 
    WHERE id_unico =".$_REQUEST['s2']);
$sector2 = $s2[0][2].' - '.ucwords(mb_strtolower($s2[0][1]));
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
$html        =  "";;
$tf          = 0;
$titulo      = '';
switch ($_REQUEST['tipo']){
    #* Informe subsidios
    case 1:
        $tf    = 19;
        $titulo= 'INFORME DE SUBSIDIOS';
        $rows = $con->Listar("SELECT * FROM gp_sector 
            WHERE id_unico BETWEEN ".$_REQUEST['s1']." AND ".$_REQUEST['s2']);
        for ($s = 0; $s < count($rows); $s++) {
            $row = $con->Listar("SELECT f.numero_factura,
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
                l.cantidad_facturada as La,
                IF(l.cantidad_facturada>16, 16, l.cantidad_facturada) as L, 
                e.codigo, 
                if((SELECT COUNT(uvs1.id_unico) FROM gp_unidad_vivienda_servicio uvs1 WHERE uvs1.unidad_vivienda = uv.id_unico AND uvs1.tipo_servicio = 1)>0, 'X', '')  AC,
                if((SELECT COUNT(uvs1.id_unico) FROM gp_unidad_vivienda_servicio uvs1 WHERE uvs1.unidad_vivienda = uv.id_unico AND uvs1.tipo_servicio = 2)>0, 'X', '')  Al,
                if((SELECT COUNT(uvs1.id_unico) FROM gp_unidad_vivienda_servicio uvs1 WHERE uvs1.unidad_vivienda = uv.id_unico AND uvs1.tipo_servicio = 3)>0, 'X', '')  Ase,
                IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                ((SELECT 
                    (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))+ (IF(l.cantidad_facturada>30, 30-16, 
                    if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                    (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                    FROM gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    WHERE  dff.factura = dfs.factura  
                    and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico))) as Porcentaje,
                    
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Acueducto%' GROUP BY f.id_unico) as cargo_f_a,
                    

                    (SELECT  t.valor FROM gp_concepto_tarifa ct LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico WHERE ct.nombre like '%metro acueducto%' 
                    and ct.parametrizacionanno = f.parametrizacionanno ) as valor_mtrs_a, 
                    (((SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = f.id_unico
                    and  cf.nombre like '%Cargo Fijo Acueducto%' GROUP BY f.id_unico)* 
                    IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                    ((SELECT 
                    (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))+ (IF(l.cantidad_facturada>30, 30-16, 
                      if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                      (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                      FROM gp_detalle_factura dff 
                      LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                      WHERE  dff.factura = dfs.factura  
                      and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico))))/100) as subsidioacf, 
                    IF(l.cantidad_facturada>0,
                       ROUND(((	IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)*
                         (SELECT  t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = 4
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato es ON t.estrato = es.id_unico
                    WHERE c.nombre like '%Metro acueducto%' AND (es.codigo = e.codigo OR t.estrato IS NULL))
                       	)*
                        ( IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                	((SELECT 
                    (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))
                     +(IF(l.cantidad_facturada>30, 30-16, 
                    if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                    (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                    FROM gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    WHERE  dff.factura = dfs.factura  
                    and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico)))/100)),2)	, 0)AS subsidio_ac_cc,

                     (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                        LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                        where dff.factura = f.id_unico
                        and  cf.nombre like '%Cargo Fijo Alcantarillado%' GROUP BY f.id_unico) as cargo_f_al, 

                    (SELECT  t.valor FROM gp_concepto_tarifa ct LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico WHERE ct.nombre like '%metro Alcantarillado%' 
                    and ct.parametrizacionanno = f.parametrizacionanno ) as valor_mt_al, 
                     (((SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                        LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                        where dff.factura = f.id_unico
                        and  cf.nombre like '%Cargo Fijo Alcantarillado%' GROUP BY f.id_unico)* 
                        IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                          ((SELECT 
                          (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))+ (IF(l.cantidad_facturada>30, 30-16, 
                                if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                                (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                                FROM gp_detalle_factura dff 
                                LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                                WHERE  dff.factura = dfs.factura  
                                and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
                                (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                                LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                                where dff.factura = dfs.factura  
                                and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
                                FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                                WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
                                ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                                where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                                (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                                where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico))))/100)  as  subsidio_al_cf, 

                            IF(l.cantidad_facturada>0,
                       ROUND(((	IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)*
                         (SELECT  t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = 4
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato es ON t.estrato = es.id_unico
                    WHERE c.nombre like '%Metro alcantarillado%' AND (es.codigo = e.codigo OR t.estrato IS NULL))
                       	)*
                        ( IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                	((SELECT 
                    (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))
                     +(IF(l.cantidad_facturada>30, 30-16, 
                    if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                    (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                    FROM gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    WHERE  dff.factura = dfs.factura  
                    and cf.nombre LIKE '%Consumo alcantarillado%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo alcantarillado%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio alcantarillado%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico)))/100)),2)	, 0) AS SUBSIDIO_ALCANTARILLADO,
                            (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                        LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                        where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico) as Cargo_aseo , 
                        (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                        LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                        where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico) as subsidio_aseo

            FROM gp_factura f 
            LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
            LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
            LEFT JOIN gp_lectura l ON l.unidad_vivienda_medidor_servicio = f.unidad_vivienda_servicio AND l.periodo = p.id_unico 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
            LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
            LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
            WHERE uv.uso = 1 AND s.id_unico =".$rows[$s][0]." 
                AND f.periodo =".$_REQUEST['p']." 
            ORDER BY cast(f.numero_factura as unsigned) asc");
            
            if(count($row)>0){
                $html .= '<tr>';
                $html .= '<td><strong>ITEM</strong></td>';
                $html .= '<td><strong>SUSCRIPTORES '.$rows[$s][1].'</strong></td>';
                $html .= '<td><strong>LECTURA</strong></td>';
                $html .= '<td><strong>M3</strong></td>';
                $html .= '<td><strong>ESTRATO SOCIOECONOMICO</strong></td>';
                $html .= '<td><strong>AC</strong></td>';
                $html .= '<td><strong>AL</strong></td>';
                $html .= '<td><strong>AS</strong></td>';
                $html .= '<td><strong>PORCENTAJE SUBSIDIO </strong></td>';
                $html .= '<td><strong>CARGO FIJO AC</strong></td>';
                $html .= '<td><strong>CARGO POR CONSUMO AC</strong></td>';
                $html .= '<td><strong>SUBSIDIO AC CF</strong></td>';
                $html .= '<td><strong>SUBSIDIO AC CC</strong></td>';
                $html .= '<td><strong>CARGO FIJO AL</strong></td>';
                $html .= '<td><strong>CARGO POR CONSUMO AL</strong></td>';
                $html .= '<td><strong>SUBSIDIO AL CF</strong></td>';
                $html .= '<td><strong>SUBSIDIO AL CC</strong></td>';
                $html .= '<td><strong>COSTO FIJO VARIABLE ASEO</strong></td>';
                $html .= '<td><strong>SUBSIDIO ASEO</strong></td>';
                $html .= '</tr>';  
                for ($i = 0; $i < count($row); $i++) {
                    $n += 1;
                    $html .= '<tr>';
                    $html .= '<td>'.$n.'</td>';
                    $html .= '<td>'.$row[$i][1].'</td>';
                    $html .= '<td>'.$row[$i][2].'</td>';
                    $html .= '<td>'.$row[$i][3].'</td>';
                    $html .= '<td>'.$row[$i][4].'</td>';
                    $html .= '<td>'.$row[$i][5].'</td>';
                    $html .= '<td>'.$row[$i][6].'</td>';
                    $html .= '<td>'.$row[$i][7].'</td>';
                    $html .= '<td>'.$row[$i][8].'</td>';
                    $html .= '<td>'.$row[$i][9].'</td>';
                    $html .= '<td>'.$row[$i][10].'</td>';
                    $html .= '<td>'.$row[$i][11].'</td>';
                    $html .= '<td>'.$row[$i][12].'</td>';
                    $html .= '<td>'.$row[$i][13].'</td>';
                    $html .= '<td>'.$row[$i][14].'</td>';
                    $html .= '<td>'.$row[$i][15].'</td>';
                    $html .= '<td>'.$row[$i][16].'</td>';
                    $html .= '<td>'.$row[$i][17].'</td>';
                    $html .= '<td>'.$row[$i][18].'</td>';
                    $html .= '</tr>';     
                }
            }
        }
        
    break;
    #* Informe Alcaldía
    case 2:
        $tf    = 22;
        $titulo= 'INFORME DE SUBSIDIOS ALCALDÍA';
        $html .='<tr>';
        $html .='<td><strong>N° MATRÍCULA</strong></td>';
        $html .='<td><strong>USUARIO APELLIDOS </strong></td>';
        $html .='<td><strong>USUARIO NOMBRES </strong></td>';
        $html .='<td><strong>CEDULA</strong></td>';
        $html .='<td><strong>DIRECCIÓN</strong></td>';
        $html .='<td><strong>BARRIO</strong></td>';
        $html .='<td><strong>COD. PREDIAL</strong></td>';
        $html .='<td><strong>ESTRATO</strong></td>';
        $html .='<td><strong>N° DE MICRO-MEDIDOR</strong></td>';
        $html .='<td><strong>ACUEDUCTO</strong></td>';
        $html .='<td><strong>ALCANTARILLADO</strong></td>';
        $html .='<td><strong>ASEO</strong></td>';
        $html .='<td><strong>N° FACTURA </strong></td>';
        $html .='<td><strong>CONSUMO</strong></td>';
        $html .='<td><strong></strong></td>';
        $html .='<td><strong>VALOR FACTURA MES</strong></td>';
        $html .='<td><strong>VALOR A SUBSIDIAR</strong></td>';
        $html .='<td><strong>SUBSIDIO CARGO FIJO</strong></td>';
        $html .='<td><strong>SUBSIDIO ACUEDUCTO</strong></td>';
        $html .='<td><strong>SUBSIDIO ALCANTARILLADO</strong></td>';
        $html .='<td><strong>SUBSIDIO ASEO</strong></td>';
        $html .='<td><strong>TOTAL SUBSIDIO</strong></td>';
        $html .='</tr>';  
        $n=0;
        $rows = $con->Listar("SELECT * FROM gp_sector 
            WHERE id_unico BETWEEN ".$_REQUEST['s1']." AND ".$_REQUEST['s2']);
        for ($s = 0; $s < count($rows); $s++) {
            $row = $con->Listar("SELECT 
                IF(pr.matricula_inmobiliaria ='' || pr.matricula_inmobiliaria IS NULL, 'NA', pr.matricula_inmobiliaria) AS N_MATRICULA, 
                IF(CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos) IS NULL OR CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos) = '',
                        (t.razonsocial),CONCAT_WS(' ',t.apellidouno,t.apellidodos)) AS USUARIOA, 
                IF(CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos) IS NULL OR CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos) = '',
                        '',CONCAT_WS(' ',t.nombreuno,t.nombredos)) AS USUARION, 
                IF(length(t.numeroidentificacion)<=6, 'NA', t.numeroidentificacion )as CEDULA,      
                pr.direccion as DIRECCION, 
                s.nombre as BARRIO, 
                'NA' AS CODIGO_PREDIAL, 
                e.codigo as ESTRATO,
                IF(length(m.referencia)<=6, '', m.referencia )as N_MEDIDOR,  
                IF((SELECT COUNT(uvs1.id_unico) FROM gp_unidad_vivienda_servicio uvs1 WHERE uvs1.estado_servicio = 1 AND uvs1.unidad_vivienda = uv.id_unico AND uvs1.tipo_servicio = 1)>0, 'X', '')  ACUEDUCTO,
                IF((SELECT COUNT(uvs1.id_unico) FROM gp_unidad_vivienda_servicio uvs1 WHERE uvs1.estado_servicio = 1 AND uvs1.unidad_vivienda = uv.id_unico AND uvs1.tipo_servicio = 2)>0, 'X', '')  ALCANTARILLADO,
                IF((SELECT COUNT(uvs1.id_unico) FROM gp_unidad_vivienda_servicio uvs1 WHERE uvs1.estado_servicio = 1 AND uvs1.unidad_vivienda = uv.id_unico AND uvs1.tipo_servicio = 3)>0, 'X', '')  ASEO,    
                f.numero_factura AS NUMERO_FACTURA,
                l.cantidad_facturada as CONSUMO, 
                IF(l.cantidad_facturada>16, 16, l.cantidad_facturada) as CONSUMO, 
                (SELECT SUM(df.valor_total_ajustado) 
                    FROM gp_detalle_factura df WHERE df.factura = f.id_unico) as VALOR_FACTURA, 
                0 as valor_subsidiar,
                0 as SUBSIDIO_CARGO_FIJO,
                IF(l.cantidad_facturada>0,
                       ROUND(((	IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)*
                         (SELECT  t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = 4
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato es ON t.estrato = es.id_unico
                    WHERE c.nombre like '%Metro acueducto%' AND (es.codigo = e.codigo OR t.estrato IS NULL))
                       	)*
                        ( IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                	((SELECT 
                    (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))
                     +(IF(l.cantidad_facturada>30, 30-16, 
                    if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                    (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                    FROM gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    WHERE  dff.factura = dfs.factura  
                    and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico)))/100)),2)	
                       , 0) AS SUBSIDIO_ACUEDUCTO,

                IF(l.cantidad_facturada>0,
                       ROUND(((	IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)*
                         (SELECT  t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = 4
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato es ON t.estrato = es.id_unico
                    WHERE c.nombre like '%Metro alcantarillado%' AND (es.codigo = e.codigo OR t.estrato IS NULL))
                       	)*
                        ( IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                	((SELECT 
                    (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))
                     +(IF(l.cantidad_facturada>30, 30-16, 
                    if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                    (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                    FROM gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    WHERE  dff.factura = dfs.factura  
                    and cf.nombre LIKE '%Consumo alcantarillado%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo alcantarillado%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio alcantarillado%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico)))/100)),2)	, 0) AS SUBSIDIO_ALCANTARILLADO,

                (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                where df.factura = f.id_unico AND c.nombre LIKE '%Subsidio aseo%' GROUP BY f.id_unico) as SUBSIDIO_ASEO, 
                f.id_unico , 

                IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                ((SELECT 
                (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))+ (IF(l.cantidad_facturada>30, 30-16, 
                if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                FROM gp_detalle_factura dff 
                LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                WHERE  dff.factura = dfs.factura  
                and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
                (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                where dff.factura = dfs.factura  
                and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
                FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
                   
                ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico))) AS porcentaje, 

                (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Acueducto%' GROUP BY f.id_unico) as Cargo_fijo_a, 
                (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Alcantarillado%' GROUP BY f.id_unico) as Cargo_fijo_al  , 
                (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico) as Cargo_aseo , 
                (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico) as subsidio_aseo 
            FROM gp_factura f 
            LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
            LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
            LEFT JOIN gp_lectura l ON l.unidad_vivienda_medidor_servicio = f.unidad_vivienda_servicio AND l.periodo = p.id_unico
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
            LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
            LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
            LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
            WHERE uv.uso = 1 AND  s.id_unico =".$rows[$s][0]." 
                AND f.periodo =".$_REQUEST['p']." 
            ORDER BY cast(f.numero_factura as unsigned) asc");

            if(count($row)>0){
                for ($i = 0; $i < count($row); $i++) {
                    $n += 1;
                    $html .='<tr>';
                    $html .='<td>'.$row[$i][0].'</td>';
                    $html .='<td>'.$row[$i][1].'</td>';
                    $html .='<td>'.$row[$i][2].'</td>';
                    $html .='<td>'.$row[$i][3].'</td>';
                    $html .='<td>'.$row[$i][4].'</td>';
                    $html .='<td>'.$row[$i][5].'</td>';
                    $html .='<td>'.$row[$i][6].'</td>';
                    $html .='<td>'.$row[$i][7].'</td>';
                    $html .='<td>'.$row[$i][8].'</td>';
                    $html .='<td>'.$row[$i][9].'</td>';
                    $html .='<td>'.$row[$i][10].'</td>';
                    $html .='<td>'.$row[$i][11].'</td>';
                    $html .='<td>'.$row[$i][12].'</td>';
                    $html .='<td>'.$row[$i][13].'</td>';
                    $html .='<td>'.$row[$i][14].'</td>';
                    $html .='<td>'.$row[$i][15].'</td>';
                    $porc   = $row[$i][22];
                    // Subsidio Cargo Fijo 
                    $vls    = ($row[$i][23]+$row[$i][24])*($porc/100);
                    // Valor a subsidiar 
                    $totals = ROUND(($vls+$row[$i][18]+$row[$i][19]+$row[$i][20]),0);
                    if($totals==0){
                        $vs     = 0;
                    } else {
                        if($porc!=''){
                            $p = $porc;
                            $vs     = ROUND((100/$porc)*$totals,0);
                        } elseif(!empty ($row[$i][25])){

                            $ps     = ROUND(($row[$i][26]/$row[$i][25])*100,0);
                            $vs     = ROUND((100/$ps)*$totals,0);
                            $p = $ps;
                        } else {
                            $vs     = 0;
                        }
                    }
                    $html .='<td>'.$vs.'</td>';                        
                    $html .='<td>'.ROUND($vls,2).'</td>';
                    $html .='<td>'.ROUND($row[$i][18],2).'</td>';
                    $html .='<td>'.ROUND($row[$i][19],2).'</td>';
                    $html .='<td>'.ROUND($row[$i][20],2).'</td>';
                    $html .='<td>'.$totals.'</td>';
                    $html .='</tr>';     
                }
            }
        }
    break;
    #* Informe Acumulado
    case 3:
        $tf    = 2;
        $titulo= 'INFORME DE SUBSIDIOS ACUMULADO';
        $titulo= 'INFORME DE SUBSIDIOS';
        $rows = $con->Listar("SELECT * FROM gp_sector 
            WHERE id_unico BETWEEN ".$_REQUEST['s1']." AND ".$_REQUEST['s2']);
        $totalacueducto      = 0; 
        $totalalcantarillado = 0;
        $totalaseo           = 0; 
        for ($s = 0; $s < count($rows); $s++) {
            $row = $con->Listar("SELECT f.numero_factura,
                (((SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                where dff.factura = f.id_unico
                and  cf.nombre like '%Cargo Fijo Acueducto%' GROUP BY f.id_unico)* 
                IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                  ((SELECT 
                  (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))+ (IF(l.cantidad_facturada>30, 30-16, 
                    if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                    (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                    FROM gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    WHERE  dff.factura = dfs.factura  
                    and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico))))/100) as subsidioacf, 
                    

                     (((SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                        LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                        where dff.factura = f.id_unico
                        and  cf.nombre like '%Cargo Fijo Alcantarillado%' GROUP BY f.id_unico)* 
                        IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                          ((SELECT 
                          (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))+ (IF(l.cantidad_facturada>30, 30-16, 
                                if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                                (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                                FROM gp_detalle_factura dff 
                                LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                                WHERE  dff.factura = dfs.factura  
                                and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
                                (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                                LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                                where dff.factura = dfs.factura  
                                and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
                                FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                                WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
                                ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                                where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                                (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                                LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                                where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico))))/100)  as  subsidio_al_cf, 
                                
                            IF(l.cantidad_facturada>0,
                       ROUND(((	IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)*
                         (SELECT  t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = 4
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato es ON t.estrato = es.id_unico
                    WHERE c.nombre like '%Metro acueducto%' AND (es.codigo = e.codigo OR t.estrato IS NULL))
                       	)*
                        ( IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                	((SELECT 
                    (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))
                     +(IF(l.cantidad_facturada>30, 30-16, 
                    if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                    (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                    FROM gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    WHERE  dff.factura = dfs.factura  
                    and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico)))/100)),2)	
                       , 0) AS SUBSIDIO_ACUEDUCTO,
                            
                    IF(l.cantidad_facturada>0,
                       ROUND(((	IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)*
                         (SELECT  t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = 4
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato es ON t.estrato = es.id_unico
                    WHERE c.nombre like '%Metro alcantarillado%' AND (es.codigo = e.codigo OR t.estrato IS NULL))
                       	)*
                        ( IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                	((SELECT 
                    (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))
                     +(IF(l.cantidad_facturada>30, 30-16, 
                    if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                    (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                    FROM gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    WHERE  dff.factura = dfs.factura  
                    and cf.nombre LIKE '%Consumo alcantarillado%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo alcantarillado%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio alcantarillado%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico)))/100)),2)	, 0) AS SUBSIDIO_ALCANTARILLADO,
                            
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico) as subsidio_aseo

            FROM gp_factura f 
            LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
            LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
            LEFT JOIN gp_lectura l ON l.unidad_vivienda_medidor_servicio = f.unidad_vivienda_servicio AND l.periodo = p.id_unico 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
            LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
            LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
            WHERE uv.uso = 1 AND s.id_unico =".$rows[$s][0]." 
                AND f.periodo =".$_REQUEST['p']." 
            ORDER BY cast(f.numero_factura as unsigned) asc");
            
            if(count($row)>0){ 
                for ($i = 0; $i < count($row); $i++) {
                    $totalacueducto      += $row[$i][1] + $row[$i][3]; 
                    $totalalcantarillado += $row[$i][2] + $row[$i][4];
                    $totalaseo           += $row[$i][5] ; 
                }
            }
        }
        $html .='<tr><td colspan="'.$tf.'"><center><br/>&nbsp;'.'LIQUIDACIÓN DE LOS SUBSIDIOS DE LOS SERVICIOS PÚBLICOS '
                . 'DOMICILIARIOS DE ACUEDUCTO,  ALCANTARILLADO Y '
                . 'ASEO PRESTADOS EN EL CORREGIMIENTO DE LA PEDREGOSA '.'<br/>&nbsp;</center></td></tr>';
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"><center>'
                . '<strong>TOTAL SUBSIDIADO DEL MES DE '.mb_strtoupper($p[0][1]).' DE '.$nanno.'</strong></center></td></tr>'; 
        $html .= '<tr>'
                . '<td>TOTAL SUBSIDIADO DEL MES '
                . 'DE '.mb_strtoupper($p[0][1]).' DE '.$nanno.' EN SERVICIO DE ACUEDUCTO</td>'
                . '<td>'. number_format($totalacueducto, 2, '.', ',').'</td>'
                . '</tr>'; 
        $html .= '<tr>'
                . '<td>TOTAL SUBSIDIADO DEL MES '
                . 'DE '.mb_strtoupper($p[0][1]).' DE '.$nanno.' EN SERVICIO DE ALCANTARILLADO</td>'
                . '<td>'. number_format($totalalcantarillado, 2, '.', ',').'</td>'
                . '</tr>'; 
        $html .= '<tr>'
                . '<td>TOTAL SUBSIDIADO DEL MES '
                . 'DE '.mb_strtoupper($p[0][1]).' DE '.$nanno.' EN SERVICIO DE ASEO</td>'
                . '<td>'. number_format($totalaseo, 2, '.', ',').'</td>'
                . '</tr>'; 
        $html .= '<tr>'
                . '<td><strong>TOTAL</strong></td>'
                . '<td><strong>'. number_format($totalacueducto+$totalalcantarillado+$totalaseo, 2, '.', ',').'</strong></td>'
                . '</tr>'; 
        
        
    break;
    #* Informe Acumulado Acueducto
    case 4:
        $tf    = 7;
        $titulo= 'INFORME DE SUBSIDIOS FINAL ACUEDUCTO';
        $titulo= 'INFORME DE SUBSIDIOS';
        $row = $con->Listar("SELECT (((SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
            LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
            where dff.factura = f.id_unico
            and  cf.nombre like '%Cargo Fijo Acueducto%' GROUP BY f.id_unico)* 
            IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
            ((SELECT 
            (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))+ (IF(l.cantidad_facturada>30, 30-16, 
              if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
            (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
            FROM gp_detalle_factura dff 
            LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
            WHERE  dff.factura = dfs.factura  
            and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
            (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
            LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
            where dff.factura = dfs.factura  
            and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
            FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
            WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
            ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
            LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
            where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
            (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
            LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
            where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico))))/100) as subsidioacf, 

            IF(l.cantidad_facturada>0,
                       ROUND(((	IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)*
                         (SELECT  t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = 4
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato es ON t.estrato = es.id_unico
                    WHERE c.nombre like '%Metro acueducto%' AND (es.codigo = e.codigo OR t.estrato IS NULL))
                       	)*
                        ( IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                	((SELECT 
                    (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))
                     +(IF(l.cantidad_facturada>30, 30-16, 
                    if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                    (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                    FROM gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    WHERE  dff.factura = dfs.factura  
                    and cf.nombre LIKE '%Consumo acueducto%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo acueducto%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio acueducto%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico)))/100)),2)	, 0)AS SUBSIDIO_ACUEDUCTO, 
           e.codigo, IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)

        FROM gp_factura f 
        LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
        LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
        LEFT JOIN gp_lectura l ON l.unidad_vivienda_medidor_servicio = f.unidad_vivienda_servicio AND l.periodo = p.id_unico 
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
        LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        WHERE uv.uso = 1 AND f.periodo =".$_REQUEST['p']." 
        ORDER BY cast(f.numero_factura as unsigned) asc");
        $tsub_cargof1   = 0;
        $tsub_cargof2   = 0;
        $total_sub1     = 0;
        $total_sub2     = 0;
        $cantidad1      = 0;
        $cantidad2      = 0;
        $total_c1       = 0;
        $total_c2       = 0;
        if(count($row)>0){ 
            
            for ($i = 0; $i < count($row); $i++) {
                if($row[$i][2]==1){
                    $tsub_cargof1   += $row[$i][0];
                    $total_sub1     += $row[$i][1];
                    $cantidad1      += $row[$i][3];
                    $total_c1       += 1;
                    $cf = $con->Listar("SELECT c.nombre, t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = $anno  
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
                    WHERE c.nombre like '%cargo fijo acueducto%' AND (e.codigo = 1 OR t.estrato IS NULL)");
                    $mt = $con->Listar("SELECT c.nombre, t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = $anno  
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
                    WHERE c.nombre like '%Metro acueducto%' AND (e.codigo = 1 OR t.estrato IS NULL)");
                } else {
                    $tsub_cargof2   += $row[$i][0];
                    $total_sub2     += $row[$i][1];
                    $cantidad2      += $row[$i][3];
                    $total_c2       += 1;
                    $cf = $con->Listar("SELECT c.nombre, t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = $anno  
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
                    WHERE c.nombre like '%cargo fijo acueducto%' AND (e.codigo = 2 OR t.estrato IS NULL)");
                    $mt = $con->Listar("SELECT c.nombre, t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = $anno  
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
                    WHERE c.nombre like '%Metro acueducto%' AND (e.codigo = 2 OR t.estrato IS NULL)");
                }
            }
        }
        $total_subsidiados = $total_c1 + $total_c2;
        $total_subsidio    = $tsub_cargof1+$total_sub1+$tsub_cargof2+$total_sub2;
        $contribuciones    = $con->Listar("SELECT SUM(df.valor_total_ajustado)
            FROM gp_factura f 
            LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura 
            LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
            WHERE uv.uso = 1 
            AND f.periodo =".$_REQUEST['p']." 
            AND c.nombre like '%Contribución Acueducto%'");
        $html .='<tr><td colspan="'.$tf.'"><center><br/>&nbsp;'.'LIQUIDACIÓN DE LOS SUBSIDIOS DE LOS SERVICIOS PÚBLICOS '
                . 'DOMICILIARIOS DE ACUEDUCTO,  ALCANTARILLADO Y '
                . 'ASEO PRESTADOS EN EL CORREGIMIENTO DE LA PEDREGOSA '.'<br/>&nbsp;</center></td></tr>';
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.($tf-1).'">'
                . '<strong>MES SUBSIDIADO: </strong></td>'
                . '<td>'.mb_strtoupper($p[0][1]).' DE '.$nanno.'</td></tr>'; 
        $html .= '<tr><td colspan="'.($tf-1).'">'
                . '<strong>TOTAL USUARIOS SUBSIDIADOS EN EL SERVICIO DE ACUEDUCTO : </strong></td>'
                . '<td>'.$total_subsidiados.'</td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        
        $html .= '<tr>'
                . '<td><strong>NÚMERO DE USUARIOS</strong></td>'
                . '<td><strong>ESTRATO</strong></td>'
                . '<td><strong>TARIFA PLENA</strong></td>'
                . '<td><strong>PORCENTAJE SUBSIDIO</strong></td>'
                . '<td><strong>VALOR SUBSIDIO</strong></td>'
                . '<td><strong>METROS CÚBICOS CONSUMIDOS</strong></td>'
                . '<td><strong>VALOR TOTAL SUBSIDIO</strong></td>'
                . '</tr>'; 
        $html  .= '<tr><td></td><td>Residencial</td>'
                . '<td></td><td></td><td></td>'
                . '<td></td><td></td></tr>';
        $html  .= '<tr><td></td><td>Estrato I</td>'
                . '<td></td><td></td><td></td>'
                . '<td></td><td></td></tr>';
        $html  .= '<tr><td>'.$total_c1.'</td>'
                . '<td>Cargo Fijo</td>'
                . '<td>'.number_format($cf[0][1], 2, '.', ',').'</td>'
                . '<td>0.6</td>'
                . '<td>'.number_format(ROUND($cf[0][1]*0.6), 2, '.', ',').'</td>'
                . '<td></td>'
                . '<td>'.number_format($tsub_cargof1, 2, '.', ',').'</td></tr>';
        $html  .= '<tr><td></td>'
                . '<td>Cargo Por Consumo por metro cúbico</td>'
                . '<td>'.number_format($mt[0][1], 2, '.', ',').'</td>'
                . '<td>0.6</td>'
                . '<td>'.number_format(ROUND($mt[0][1]*0.6), 2, '.', ',').'</td>'
                . '<td>'.$cantidad1.' </td>'
                . '<td>'.number_format($total_sub1, 2, '.', ',').'</td></tr>';
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html  .= '<tr><td></td><td>Estrato II</td>'
                . '<td></td><td></td><td></td>'
                . '<td></td><td></td></tr>';
        $html  .= '<tr><td>'.$total_c2.'</td>'
                . '<td>Cargo Fijo</td>'
                . '<td>'.number_format($cf[0][1], 2, '.', ',').'</td>'
                . '<td>0.4</td>'
                . '<td>'.number_format(ROUND($cf[0][1]*0.4), 2, '.', ',').'</td>'
                . '<td></td>'
                . '<td>'.number_format($tsub_cargof2, 2, '.', ',').'</td></tr>';
        $html  .= '<tr><td></td>'
                . '<td>Cargo Por Consumo por metro cúbico</td>'
                . '<td>'.number_format($mt[0][1], 2, '.', ',').'</td>'
                . '<td>0.4</td>'
                . '<td>'.number_format(ROUND($mt[0][1]*0.4), 2, '.', ',').'</td>'
                . '<td>'.$cantidad2.' </td>'
                . '<td>'.number_format($total_sub2, 2, '.', ',').'</td></tr>';
        $html .= '<tr>'
                . '<td colspan="'.($tf-1).'"><strong>SUB TOTAL</strong></td>'
                . '<td><strong>'. number_format($total_subsidio, 2, '.', ',').'</strong></td>'
                . '</tr>'; 
        $html .= '<tr></tr>';
        $html .= '<tr>'
                . '<td colspan="'.($tf-1).'"><strong>CONTRIBUCIONES</strong></td>'
                . '<td><strong>'. number_format($contribuciones[0][0], 2, '.', ',').'</strong></td>'
                . '</tr>'; 
        $html .= '<tr></tr>';
        $html .= '<tr>'
                . '<td colspan="'.($tf-1).'"><strong>TOTAL SUBSIDIADO '.mb_strtoupper($p[0][1]).' DE '.$nanno.'</strong></td>'
                . '<td><strong>'. number_format(($total_subsidio-$contribuciones[0][0]), 2, '.', ',').'</strong></td>'
                . '</tr>'; 
        
        
    break;
    #* Informe Acumulado Alcantarillado
    case 5:
        $tf    = 7;
        $titulo= 'INFORME DE SUBSIDIOS FINAL ALCANTARILLADO';
        $titulo= 'INFORME DE SUBSIDIOS';
        $row = $con->Listar("SELECT (((SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
            LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
            where dff.factura = f.id_unico
            and  cf.nombre like '%Cargo Fijo Alcantarillado%' GROUP BY f.id_unico)* 
            IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
            ((SELECT 
            (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))+ (IF(l.cantidad_facturada>30, 30-16, 
              if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
            (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
            FROM gp_detalle_factura dff 
            LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
            WHERE  dff.factura = dfs.factura  
            and cf.nombre LIKE '%Consumo Alcantarillado%' GROUP BY f.id_unico)+
            (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
            LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
            where dff.factura = dfs.factura  
            and  cf.nombre like '%Cargo Fijo Alcantarillado%' GROUP BY f.id_unico)),0) 
            FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
            WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio Alcantarillado%' GROUP BY f.id_unico),
            ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
            LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
            where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
            (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
            LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
            where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico))))/100) as subsidioacf, 

            IF(l.cantidad_facturada>0,
                       ROUND(((	IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)*
                         (SELECT  t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = 4
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato es ON t.estrato = es.id_unico
                    WHERE c.nombre like '%Metro Alcantarillado%' AND (es.codigo = e.codigo OR t.estrato IS NULL))
                       	)*
                        ( IF(l.cantidad_facturada != 0, (SELECT ROUND((IF(dfs.valor IS NULL, 0, (dfs.valor*-1))*100)/
                	((SELECT 
                    (((IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada)*if(l.cantidad_facturada<=16, l.cantidad_facturada, 16))
                     +(IF(l.cantidad_facturada>30, 30-16, 
                    if(l.cantidad_facturada<16, 0,l.cantidad_facturada-16))*
                    (IF(dff.valor IS NULL, 0,dff.valor)/l.cantidad_facturada))) as totals 
                    FROM gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    WHERE  dff.factura = dfs.factura  
                    and cf.nombre LIKE '%Consumo Alcantarillado%' GROUP BY f.id_unico)+
                    (SELECT  IF(dff.valor IS NULL, 0,dff.valor) from gp_detalle_factura dff 
                    LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
                    where dff.factura = dfs.factura  
                    and  cf.nombre like '%Cargo Fijo Alcantarillado%' GROUP BY f.id_unico)),0) 
                    FROM gp_detalle_factura dfs LEFT JOIN gp_concepto cfs ON dfs.concepto_tarifa = cfs.id_unico 
                    WHERE dfs.factura = f.id_unico and cfs.nombre LIKE '%subsidio Alcantarillado%' GROUP BY f.id_unico),
                    ROUND(((SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%subsidio Aseo%' GROUP BY f.id_unico)*100)/
                    (SELECT IF(df.valor<0,df.valor*-1, df.valor) FROM  gp_detalle_factura df 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    where df.factura = f.id_unico AND c.nombre LIKE '%Cargo Fijo Aseo%' GROUP BY f.id_unico)))/100)),2)	, 0)AS SUBSIDIO_ACUEDUCTO, 
           e.codigo, IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)

        FROM gp_factura f 
        LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
        LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
        LEFT JOIN gp_lectura l ON l.unidad_vivienda_medidor_servicio = f.unidad_vivienda_servicio AND l.periodo = p.id_unico 
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
        LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        WHERE uv.uso = 1 AND f.periodo =".$_REQUEST['p']." 
        ORDER BY cast(f.numero_factura as unsigned) asc");
        $tsub_cargof1   = 0;
        $tsub_cargof2   = 0;
        $total_sub1     = 0;
        $total_sub2     = 0;
        $cantidad1      = 0;
        $cantidad2      = 0;
        $total_c1       = 0;
        $total_c2       = 0;
        if(count($row)>0){ 
            
            for ($i = 0; $i < count($row); $i++) {
                if($row[$i][2]==1){
                    $tsub_cargof1   += $row[$i][0];
                    $total_sub1     += $row[$i][1];
                    $cantidad1      += $row[$i][3];
                    $total_c1       += 1;
                    $cf = $con->Listar("SELECT c.nombre, t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = $anno  
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
                    WHERE c.nombre like '%cargo fijo Alcantarillado%' AND (e.codigo = 1 OR t.estrato IS NULL)");
                    $mt = $con->Listar("SELECT c.nombre, t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = $anno  
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
                    WHERE c.nombre like '%Metro Alcantarillado%' AND (e.codigo = 1 OR t.estrato IS NULL)");
                } else {
                    $tsub_cargof2   += $row[$i][0];
                    $total_sub2     += $row[$i][1];
                    $cantidad2      += $row[$i][3];
                    $total_c2       += 1;
                    $cf = $con->Listar("SELECT c.nombre, t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = $anno  
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
                    WHERE c.nombre like '%cargo fijo Alcantarillado%' AND (e.codigo = 2 OR t.estrato IS NULL)");
                    $mt = $con->Listar("SELECT c.nombre, t.valor
                    FROM gp_concepto c 
                    LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto AND ct.parametrizacionanno = $anno  
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    LEFT JOIN gp_estrato e ON t.estrato = e.id_unico
                    WHERE c.nombre like '%Metro Alcantarillado%' AND (e.codigo = 2 OR t.estrato IS NULL)");
                }
            }
        }
        $total_subsidiados = $total_c1 + $total_c2;
        $total_subsidio    = $tsub_cargof1+$total_sub1+$tsub_cargof2+$total_sub2;
        $contribuciones    = $con->Listar("SELECT SUM(df.valor_total_ajustado)
            FROM gp_factura f 
            LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura 
            LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
            WHERE uv.uso = 1 
            AND f.periodo =".$_REQUEST['p']." 
            AND c.nombre like '%Contribución Alcantarillado%'");
        $html .='<tr><td colspan="'.$tf.'"><center><br/>&nbsp;'.'LIQUIDACIÓN DE LOS SUBSIDIOS DE LOS SERVICIOS PÚBLICOS '
                . 'DOMICILIARIOS DE ACUEDUCTO,  ALCANTARILLADO Y '
                . 'ASEO PRESTADOS EN EL CORREGIMIENTO DE LA PEDREGOSA '.'<br/>&nbsp;</center></td></tr>';
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.($tf-1).'">'
                . '<strong>MES SUBSIDIADO: </strong></td>'
                . '<td>'.mb_strtoupper($p[0][1]).' DE '.$nanno.'</td></tr>'; 
        $html .= '<tr><td colspan="'.($tf-1).'">'
                . '<strong>TOTAL USUARIOS SUBSIDIADOS EN EL SERVICIO DE ALCANTARILLADO : </strong></td>'
                . '<td>'.$total_subsidiados.'</td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        
        $html .= '<tr>'
                . '<td><strong>NÚMERO DE USUARIOS</strong></td>'
                . '<td><strong>ESTRATO</strong></td>'
                . '<td><strong>TARIFA PLENA</strong></td>'
                . '<td><strong>PORCENTAJE SUBSIDIO</strong></td>'
                . '<td><strong>VALOR SUBSIDIO</strong></td>'
                . '<td><strong>METROS CÚBICOS CONSUMIDOS</strong></td>'
                . '<td><strong>VALOR TOTAL SUBSIDIO</strong></td>'
                . '</tr>'; 
        $html  .= '<tr><td></td><td>Residencial</td>'
                . '<td></td><td></td><td></td>'
                . '<td></td><td></td></tr>';
        $html  .= '<tr><td></td><td>Estrato I</td>'
                . '<td></td><td></td><td></td>'
                . '<td></td><td></td></tr>';
        $html  .= '<tr><td>'.$total_c1.'</td>'
                . '<td>Cargo Fijo</td>'
                . '<td>'.number_format($cf[0][1], 2, '.', ',').'</td>'
                . '<td>0.6</td>'
                . '<td>'.number_format(ROUND($cf[0][1]*0.6), 2, '.', ',').'</td>'
                . '<td></td>'
                . '<td>'.number_format($tsub_cargof1, 2, '.', ',').'</td></tr>';
        $html  .= '<tr><td></td>'
                . '<td>Cargo Por Consumo por metro cúbico</td>'
                . '<td>'.number_format($mt[0][1], 2, '.', ',').'</td>'
                . '<td>0.6</td>'
                . '<td>'.number_format(ROUND($mt[0][1]*0.6), 2, '.', ',').'</td>'
                . '<td>'.$cantidad1.' </td>'
                . '<td>'.number_format($total_sub1, 2, '.', ',').'</td></tr>';
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html  .= '<tr><td></td><td>Estrato II</td>'
                . '<td></td><td></td><td></td>'
                . '<td></td><td></td></tr>';
        $html  .= '<tr><td>'.$total_c2.'</td>'
                . '<td>Cargo Fijo</td>'
                . '<td>'.number_format($cf[0][1], 2, '.', ',').'</td>'
                . '<td>0.4</td>'
                . '<td>'.number_format(ROUND($cf[0][1]*0.4), 2, '.', ',').'</td>'
                . '<td></td>'
                . '<td>'.number_format($tsub_cargof2, 2, '.', ',').'</td></tr>';
        $html  .= '<tr><td></td>'
                . '<td>Cargo Por Consumo por metro cúbico</td>'
                . '<td>'.number_format($mt[0][1], 2, '.', ',').'</td>'
                . '<td>0.4</td>'
                . '<td>'.number_format(ROUND($mt[0][1]*0.4), 2, '.', ',').'</td>'
                . '<td>'.$cantidad2.' </td>'
                . '<td>'.number_format($total_sub2, 2, '.', ',').'</td></tr>';
        $html .= '<tr>'
                . '<td colspan="'.($tf-1).'"><strong>SUB TOTAL</strong></td>'
                . '<td><strong>'. number_format($total_subsidio, 2, '.', ',').'</strong></td>'
                . '</tr>'; 
        $html .= '<tr></tr>';
        $html .= '<tr>'
                . '<td colspan="'.($tf-1).'"><strong>CONTRIBUCIONES</strong></td>'
                . '<td><strong>'. number_format($contribuciones[0][0], 2, '.', ',').'</strong></td>'
                . '</tr>'; 
        $html .= '<tr></tr>';
        $html .= '<tr>'
                . '<td colspan="'.($tf-1).'"><strong>TOTAL SUBSIDIADO '.mb_strtoupper($p[0][1]).' DE '.$nanno.'</strong></td>'
                . '<td><strong>'. number_format(($total_subsidio-$contribuciones[0][0]), 2, '.', ',').'</strong></td>'
                . '</tr>'; 
        
        
    break;
    #* Informe Acumulado Aseo
    case 6:
        $tf    = 6;
        $titulo= 'INFORME DE SUBSIDIOS FINAL ASEO';
        $titulo= 'INFORME DE SUBSIDIOS';
        $row = $con->Listar("SELECT (SELECT  IF(dff.valor IS NULL, 0,IF(dff.valor<0, dff.valor*-1, dff.valor )) from gp_detalle_factura dff 
            LEFT JOIN gp_concepto cf ON dff.concepto_tarifa = cf.id_unico 
            where dff.factura = f.id_unico
            and  cf.nombre like '%Subsidio Aseo%' GROUP BY f.id_unico) SUBA, 
           (SELECT SUM(t.valor)  
            FROM gp_concepto c 
            LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto and ct.parametrizacionanno = 4 
            LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
            WHERE c.nombre like '%Barrido y limpieza aseo%' ) AS TBL, 
          IF(IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)=0, 0, (SELECT SUM(t.valor)  
            FROM gp_concepto c 
            LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto and ct.parametrizacionanno = 4 
            LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
            WHERE c.nombre like '%Tramo excedente aseo%')) AS TE, 
            IF(IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)=0, 0, (SELECT SUM(t.valor)  
            FROM gp_concepto c 
            LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto and ct.parametrizacionanno = 4 
            LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
            WHERE c.nombre like '%Disposicion final aseo%' )) AS DIF, 
            (SELECT SUM(t.valor)  
            FROM gp_concepto c 
            LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto and ct.parametrizacionanno = 4 
            LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
            WHERE c.nombre like '%Comercialización aseo%' ) AS TFRA, 
            IF(IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)=0, 0, (SELECT SUM(t.valor)  
            FROM gp_concepto c 
            LEFT JOIN gp_concepto_tarifa ct ON c.id_unico = ct.concepto and ct.parametrizacionanno = 4 
            LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
            WHERE c.nombre like '%Recoleccion aseo%' )) AS TRT, 
           e.codigo, IF(l.cantidad_facturada >16, 16, l.cantidad_facturada)as CANTIDAD            
        FROM gp_factura f 
        LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
        LEFT JOIN gp_periodo p ON f.periodo = p.id_unico 
        LEFT JOIN gp_lectura l ON l.unidad_vivienda_medidor_servicio = f.unidad_vivienda_servicio AND l.periodo = p.id_unico 
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
        LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        WHERE uv.uso = 1 AND f.periodo =".$_REQUEST['p']." 
        ORDER BY cast(f.numero_factura as unsigned) asc");
        $total_sub1     = 0;
        $total_sub2     = 0;
        $total_c1       = 0;
        $total_c2       = 0;
        $html1          = '';
        $html2          = '';
        $tbl1           = 0;
        $te1            = 0;
        $dif1           = 0;
        $tfra1          = 0;
        $trt1           = 0;
        $tbl2           = 0;
        $te2            = 0;
        $dif2           = 0;
        $tfra2          = 0;
        $trt2           = 0;
        if(count($row)>0){ 
            for ($i = 0; $i < count($row); $i++) {
                if(!empty($row[$i][0])){
                    if($row[$i][6]==1){
                        $total_sub1     += $row[$i][0];
                        $total_c1       += 1;
                        $pr              = 0.6;
                        $tbl1           += $row[$i][1] * $pr;
                        $te1            += $row[$i][2] * $pr;
                        $dif1           += $row[$i][3] * $pr;
                        $tfra1          += $row[$i][4] * $pr;
                        $trt1           += $row[$i][5] * $pr;
                    } else {
                        $total_sub2     += $row[$i][0];
                        $total_c2       += 1;
                        $pr              = 0.4;
                        $tbl2           += $row[$i][1] * $pr;
                        $te2            += $row[$i][2] * $pr;
                        $dif2           += $row[$i][3] * $pr;
                        $tfra2          += $row[$i][4] * $pr;
                        $trt2           += $row[$i][5] * $pr;
                    }
                    
                }
            }
        }
        $total_subsidiados = $total_c1 + $total_c2;
        $total_subsidio    = $total_sub1+$total_sub2;
        $contribuciones    = $con->Listar("SELECT SUM(df.valor_total_ajustado)
            FROM gp_factura f 
            LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura 
            LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
            WHERE uv.uso = 1 
            AND f.periodo =".$_REQUEST['p']." 
            AND c.nombre like '%Contribución Aseo%'");
        
        $html .='<tr><td colspan="'.$tf.'"><center><br/>&nbsp;'.'LIQUIDACIÓN DE LOS SUBSIDIOS DE LOS SERVICIOS PÚBLICOS '
                . 'DOMICILIARIOS DE ACUEDUCTO,  ALCANTARILLADO Y '
                . 'ASEO PRESTADOS EN EL CORREGIMIENTO DE LA PEDREGOSA '.'<br/>&nbsp;</center></td></tr>';
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.($tf-1).'">'
                . '<strong>MES SUBSIDIADO: </strong></td>'
                . '<td>'.mb_strtoupper($p[0][1]).' DE '.$nanno.'</td></tr>'; 
        $html .= '<tr><td colspan="'.($tf-1).'">'
                . '<strong>TOTAL USUARIOS SUBSIDIADOS EN EL SERVICIO DE ASEO : </strong></td>'
                . '<td>'.$total_subsidiados.'</td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        $html .= '<tr><td colspan="'.$tf.'"></td></tr>'; 
        
        $html .= '<tr>'
                . '<td><strong>NÚMERO DE USUARIOS</strong></td>'
                . '<td><strong>ESTRATO</strong></td>'
                . '<td><strong>TARIFA PLENA</strong></td>'
                . '<td><strong>PORCENTAJE SUBSIDIO</strong></td>'
                . '<td><strong>VALOR SUBSIDIO</strong></td>'
                . '<td><strong>VALOR TOTAL SUBSIDIO</strong></td>'
                . '</tr>'; 
        
        $html  .= '<tr><td>'.$total_c1.'</td>'
                . '<td>Residencial</td>'
                . '<td>Estrato I  </td><td></td>'
                . '<td></td><td></td></tr>';
        
        $html  .= '<tr><td></td><td></td>'
                . '<td>TBL</td>'
                . '<td>0.6</td>'
                . '<td>'.number_format($row[0][1], 2, '.', ',').'</td>'
                . '<td>'.number_format(ROUND($tbl1), 2, '.', ',').'</td>'
                . '</tr>';
        $html  .= '<tr><td></td><td></td>'
                . '<td>TE</td>'
                . '<td>0.6</td>'
                . '<td>'.number_format($row[0][2], 2, '.', ',').'</td>'
                . '<td>'.number_format(ROUND($te1), 2, '.', ',').'</td>'
                . '</tr>';
        $html  .= '<tr><td></td><td></td>'
                . '<td>DIF</td>'
                . '<td>0.6</td>'
                . '<td>'.number_format($row[0][3], 2, '.', ',').'</td>'
                . '<td>'.number_format(ROUND($dif1), 2, '.', ',').'</td>'
                . '</tr>';
        $html  .= '<tr><td></td><td></td>'
                . '<td>TFRA</td>'
                . '<td>0.6</td>'
                . '<td>'.number_format($row[0][4], 2, '.', ',').'</td>'
                . '<td>'.number_format(ROUND($tfra1), 2, '.', ',').'</td>'
                . '</tr>';
        $html  .= '<tr><td></td><td></td>'
                . '<td>TRT</td>'
                . '<td>0.6</td>'
                . '<td>'.number_format($row[0][5], 2, '.', ',').'</td>'
                . '<td>'.number_format(ROUND($trt1), 2, '.', ',').'</td>'
                . '</tr>';
        
        
        $html  .= '<tr><td>'.$total_c2.'</td>'
                . '<td>Residencial</td>'
                . '<td>Estrato II    </td><td></td>'
                . '<td></td><td></td></tr>';
        
        $html  .= '<tr><td></td><td></td>'
                . '<td>TBL</td>'
                . '<td>0.4</td>'
                . '<td>'.number_format($row[0][1], 2, '.', ',').'</td>'
                . '<td>'.number_format(ROUND($tbl2), 2, '.', ',').'</td>'
                . '</tr>';
        $html  .= '<tr><td></td><td></td>'
                . '<td>TE</td>'
                . '<td>0.4</td>'
                . '<td>'.number_format($row[0][2], 2, '.', ',').'</td>'
                . '<td>'.number_format(ROUND($te2), 2, '.', ',').'</td>'
                . '</tr>';
        $html  .= '<tr><td></td><td></td>'
                . '<td>DIF</td>'
                . '<td>0.4</td>'
                . '<td>'.number_format($row[0][3], 2, '.', ',').'</td>'
                . '<td>'.number_format(ROUND($dif2), 2, '.', ',').'</td>'
                . '</tr>';
        $html  .= '<tr><td></td><td></td>'
                . '<td>TFRA</td>'
                . '<td>0.4</td>'
                . '<td>'.number_format($row[0][4], 2, '.', ',').'</td>'
                . '<td>'.number_format(ROUND($tfra2), 2, '.', ',').'</td>'
                . '</tr>';
        $html  .= '<tr><td></td><td></td>'
                . '<td>TRT</td>'
                . '<td>0.4</td>'
                . '<td>'.number_format($row[0][5], 2, '.', ',').'</td>'
                . '<td>'.number_format(ROUND($trt2), 2, '.', ',').'</td>'
                . '</tr>';
        
        
        
        
        $html .= '<tr>'
                . '<td colspan="'.($tf-1).'"><strong>SUB TOTAL</strong></td>'
                . '<td><strong>'. number_format($total_subsidio, 2, '.', ',').'</strong></td>'
                . '</tr>'; 
        $html .= '<tr></tr>';
        $html .= '<tr>'
                . '<td colspan="'.($tf-1).'"><strong>CONTRIBUCIONES</strong></td>'
                . '<td><strong>'. number_format($contribuciones[0][0], 2, '.', ',').'</strong></td>'
                . '</tr>'; 
        $html .= '<tr></tr>';
        $html .= '<tr>'
                . '<td colspan="'.($tf-1).'"><strong>TOTAL SUBSIDIADO '.mb_strtoupper($p[0][1]).' DE '.$nanno.'</strong></td>'
                . '<td><strong>'. number_format(($total_subsidio-$contribuciones[0][0]), 2, '.', ',').'</strong></td>'
                . '</tr>'; 
        
        
    break;
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Facturación</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="<?php echo $tf;?>" align="center"><strong>
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;
                <br/><?php echo $titulo;?>
                <br/>PERIODO: <?php echo $periodo; ?>
                <br/>ENTRE SECTOR:<?php echo $sector.' Y ',$sector2; ?>
                <br/>&nbsp;</strong>
            </th>
            <tr></tr>
            <?php echo $html;?>
        </table>
    </body>
</html>