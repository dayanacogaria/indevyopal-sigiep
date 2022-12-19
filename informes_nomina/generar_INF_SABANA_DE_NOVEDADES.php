<?php
##########################################################################################
# ******************************  Sábana Asuaped  ************************************** # 
##########################################################################################
# 23/10/2018 | Modificación Código y Consultas
##########################################################################################    
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
ini_set('max_execution_time',0);
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];

#************** Datos Compañia *********************#
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo, 
                c.rss, 
                c2.rss, d1.rss, d2.rss
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN       gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
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
$ruta_logo   = $rowC[0][6]; 


#************** Datos Recibe *********************#
$periodo  = $_POST['sltPeriodo'];

$np = $con->Listar("SELECT p.id_unico,p.codigointerno, tpn.nombre , fechafin , fechainicio 
    FROM gn_periodo p 
    LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico 
    WHERE p.id_unico = $periodo");

$nperiodo = ucwords(mb_strtolower($np[0][1]));
$fechafin = $np[0][3];
$fechaInicio = $np[0][4];

#******** Tipo Excel *************#
if($_GET['t']=1){

    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Sabana_De_Novedades.xls");  
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<title>Sabana Nomina</title>';
    echo '</head>';
    echo '<body>';
    echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
    
    $claseC = $_POST['sltClaseCon'];
    if($claseC != null){

         #**** Buscar Unidades Ejecutoras id ***#
         $rowCo = $con->Listar("SELECT * FROM gn_clase_concepto WHERE id_unico = $claseC");
        #**** Numero de conceptos ****#
         $ncon = $con->Listar("SELECT  COUNT(DISTINCT n.concepto) 
        FROM gn_novedad n
        LEFT JOIN gn_concepto c ON n.concepto=c.id_unico
        LEFT JOIN gn_clase_concepto cc  ON c.clase=cc.id_unico
        LEFT JOIN gn_periodo p ON n.periodo= p.id_unico
        LEFT JOIN gn_empleado e ON n.empleado= e.id_unico
        LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
        WHERE c.clase=$claseC
        AND n.periodo= $periodo
        AND t.compania=$compania");  

        $nc =$ncon[0][0]+4;
        echo '<th colspan="'.$nc.'" align="center"><strong>';
        echo '<br/>&nbsp;';
        echo '<br/>'.$razonsocial;
        echo '<br/>'.$nombreIdent.': '.$numeroIdent;
        echo '<br/>&nbsp;';
        echo '<br/>SÁBANA DE NOVEDADES';
        echo '<br/>&nbsp;';
        echo 'NOVEDADES:&nbsp;'.$nperiodo;
        echo '<br/>&nbsp;';
        echo '</strong>';
        echo '</th>';
        echo '<tr></tr>  ';
        echo '<tr><td colspan="'.$nc.'" ><strong><i>&nbsp;<br/>CLASE CONCEPTO: '.$rowCo[0][1].'<br/>&nbsp;</i></strong></td></tr>';
        echo '<tr>';
        echo '<td><strong>Cod Int</strong></td>';
        echo '<td><strong>Nombre</strong></td>';
        echo '<td><strong>Básico</strong></td>';
        #**** Nombre de conceptos ****#
            $rowcn = $con->Listar("SELECT distinct 
            n.concepto, 
            c.descripcion,
            c.id_unico
                FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero  t ON e.tercero  = t.id_unico 
                LEFT JOIN gn_clase_concepto cc ON cc.id_unico=c.clase
                LEFT JOIN gn_periodo p ON n.periodo= p.id_unico
                WHERE t.compania = $compania   
                AND n.periodo = $periodo
                AND n.valor > 0 
                AND c.clase= $claseC
                ORDER BY c.clase,c.id_unico");  
        for ($c = 0; $c < count($rowcn); $c++) {
            echo '<td><strong>'.ucwords(mb_strtolower($rowcn[$c][1])).'</strong></td>';
        }
        echo '<td><strong>&nbsp;&nbsp;Firma &nbsp; &nbsp;</strong></td>';
        echo '</tr>';
       
        #***************************************************************#
        #**** Buscar empleados con datos solicitados y salario
            $rowe = $con->Listar(" SELECT  DISTINCT  e.id_unico, 
            e.codigointerno, 
            e.tercero, 
            t.id_unico,
            t.numeroidentificacion, 
            concat_ws(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos),
            ca.salarioactual 
            FROM gn_empleado e 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
            LEFT JOIN gn_novedad n ON n.empleado= e.id_unico
            LEFT JOIN gn_concepto c ON c.id_unico= n.concepto
            LEFT JOIN gn_clase_concepto cc ON cc.id_unico= c.clase
            WHERE t.compania = $compania
            AND cc.id_unico= $claseC
            AND n.periodo=$periodo");
        $salarioa = 0;
        for ($e = 0; $e < count($rowe); $e++) {
            echo '<tr>';
            echo '<td align= "left">'.utf8_decode($rowe[$e][4]).'</td>';
            echo '<td>'.utf8_decode($rowe[$e][5]).'</td>';
            #*** Salario ****#
            $basico = $con->Listar("SELECT 
                valor FROM gn_novedad 
                WHERE empleado = ".$rowe[$e][0]." 
                AND concepto = '78' AND periodo = '$periodo'");
            echo '<td>'.number_format($rowe[$e][6],0,'.',',').'</td>';
            $salarioa += $rowe[$e][6];
            for ($c = 0; $c < count($rowcn); $c++) {
                $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                    FROM gn_novedad n 
                    LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    WHERE c.id_unico = ".$rowcn[$c][2]." AND e.id_unico = ".$rowe[$e][0]." 
                    AND n.periodo = $periodo ");
                if($num_con[0][1] > 0){
                    $valor = $num_con[0][1];
                }else{
                    $valor =0;
                }
                echo '<td>'.number_format($valor,0,'.',',').'</td>';
            }   
            echo '<td></td>';
            echo '</tr>';
        }
         echo '<tr>';
        echo '<td colspan="2"><strong>Total</strong></td>';
        echo '<td><strong>'.number_format($salarioa,0,'.',',').'</strong></td>';
        for ($c = 0; $c < count($rowcn); $c++) {
            $num_con = $con->Listar("SELECT n.id_unico, sum(n.valor) 
                FROM gn_novedad n 
                LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                WHERE c.id_unico = ".$rowcn[$c][2]." 
                AND n.periodo = $periodo 
                AND t.compania = $compania 
                AND e.id_unico 
                IN (SELECT n.empleado FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado !=2 and n.empleado = e.id_unico and n.periodo =$periodo 
                AND (c.clase = 1 OR c.clase = 2 OR c.clase = 3 OR c.clase = 4 OR c.clase = 5) AND n.valor>0)");
            if($num_con[0][1] > 0){
                $valor = $num_con[0][1];
            }else{
                $valor =0;
            }
            echo '<td><strong>'.number_format($valor, 0, '.',',').'</strong></td>';
        } 

    }
    
}
?>