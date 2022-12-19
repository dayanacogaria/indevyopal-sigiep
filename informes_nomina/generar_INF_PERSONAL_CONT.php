<?php                                                                                     
##########################################################################################################################################################################
#13/10/2021 | Elkin O. | Se crea informe para conteo del personal 
##########################################################################################################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Personl_Conteo.xls");
require_once("../Conexion/conexion.php");
session_start();
ini_set('max_execution_time', 0);
$compania = $_SESSION['compania'];
$panno = $_SESSION['anno'];
$anno=$_POST['sltAnno'];
$mesI=$_POST['sltMesi'];
$mesF=$_POST['sltMesf'];
$calendario = CAL_GREGORIAN;
$diaF = cal_days_in_month($calendario, $mesF , $anno); 
$fechaIn='01/'.$mesI.'/'.$anno;
$fechaFin=$diaF.'/'.$mesF.'/'.$anno;
$sqlMeses="SELECT m.numero,m.mes 
           FROM gf_mes m 
           LEFT JOIN gf_parametrizacion_anno pa ON pa.id_unico=m.parametrizacionanno
           WHERE  m.compania=$compania
           AND pa.anno='$anno'
           AND m.numero BETWEEN '$mesI' AND '$mesF'";
$meses = $mysqli->query($sqlMeses);

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Personal</title>
</head>
<body>
<table width="30%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="4" align="center"><strong>
          Personal Administrativo
        </th>
  </tr>
  <tr>
        <th colspan="4" align="center"><strong>
            PERIODO COMPRENDIDO ENTRE:
        <br/><?php echo $fechaIn.' al '.$fechaFin ?>
        </th>
  </tr>
  <tr>
        <td rowspan="2" colspan="2" align="center"><strong>MES A REPORTAR</strong></td>
        <td rowspan="2" colspan="2" align="center"><strong>NÚMERO TOTAL DE PERSONAL ADMINISTRATIVO</strong></td>

        <tr>
        </tr>
        </tr>
      <?php
     

   
     while($filaM = mysqli_fetch_row($meses)){
        $diaF = cal_days_in_month($calendario, $filaM[0] , $anno); 
        $fechaI=$anno.'-'.$filaM[0].'-01';
        $fechaF=$anno.'-'.$filaM[0].'-'.$diaF;
    
    
    
       
       $empleados="SELECT DISTINCT n.id_unico, COUNT(n.empleado), t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   WHERE p.fechainicio BETWEEN '$fechaI' AND '$fechaF'
                   AND p.fechafin BETWEEN '$fechaI' AND '$fechaF'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.grupo_sui='Personal Administrativo'";
        $countEmple = $mysqli->query($empleados);
        $resultEmp = mysqli_fetch_row($countEmple);


        ?>
     <tr>
        <td  colspan="2" align="center"><?php echo $filaM[1]?></td>
        <td  colspan="2" align="center"><?php echo $resultEmp[1]?></td>
    </tr>
        <?php
      }?>
        <tr>
        <th colspan="4" align="center"><strong></th>
        </tr>
       <tr>
        <th colspan="4" align="center"><strong>
        Personal Operativo Acueducto
        </th>
      </tr>
      <tr>
        <th colspan="4" align="center"><strong>
            PERIODO COMPRENDIDO ENTRE:
        <br/><?php echo $fechaIn.' al '.$fechaFin ?>
        </th>
     </tr>
     <tr>
        <td rowspan="2" colspan="2" align="center"><strong>MES A REPORTAR</strong></td>
        <td rowspan="2" colspan="2" align="center"><strong>NÚMERO TOTAL DE PERSONAL OPERATIVO ACUEDUCTO</strong></td>

        <tr>
        </tr>
        </tr>
        <?php
     
$sqlMesesD="SELECT m.numero,m.mes 
           FROM gf_mes m 
           LEFT JOIN gf_parametrizacion_anno pa ON pa.id_unico=m.parametrizacionanno
           WHERE  m.compania=$compania
           AND pa.anno='$anno'
           AND m.numero BETWEEN '$mesI' AND '$mesF'";
$meses1 = $mysqli->query($sqlMesesD);
   
     while($filaD = mysqli_fetch_row($meses1)){
        $diaF = cal_days_in_month($calendario, $filaD[0] , $anno); 
        $fechaI=$anno.'-'.$filaD[0].'-01';
        $fechaF=$anno.'-'.$filaD[0].'-'.$diaF;
    
    
    
       
       $empleados="SELECT DISTINCT n.id_unico, COUNT(n.empleado), t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   WHERE p.fechainicio BETWEEN '$fechaI' AND '$fechaF'
                   AND p.fechafin BETWEEN '$fechaI' AND '$fechaF'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.grupo_sui='Personal Operativo Acueducto'";
        $countEmple = $mysqli->query($empleados);
        $resultEmp = mysqli_fetch_row($countEmple);


        ?>
     <tr>
        <td  colspan="2" align="center"><?php echo $filaD[1]?></td>
        <td  colspan="2" align="center"><?php echo $resultEmp[1]?></td>
    </tr>
        <?php
      }?>
         <tr>
        <th colspan="4" align="center"><strong></th>
        </tr>
       <tr>
        <th colspan="4" align="center"><strong>
        Personal Operativo Alcantarillado
        </th>
      </tr>
      <tr>
        <th colspan="4" align="center"><strong>
            PERIODO COMPRENDIDO ENTRE:
        <br/><?php echo $fechaIn.' al '.$fechaFin ?>
        </th>
     </tr>
     <tr>
        <td rowspan="2" colspan="2" align="center"><strong>MES A REPORTAR</strong></td>
        <td rowspan="2" colspan="2" align="center"><strong>NÚMERO TOTAL DE PERSONAL OPERATIVO ALCANTARILLADO</strong></td>

        <tr>
        </tr>
        </tr>
        <?php
     
$sqlMesesD="SELECT m.numero,m.mes 
           FROM gf_mes m 
           LEFT JOIN gf_parametrizacion_anno pa ON pa.id_unico=m.parametrizacionanno
           WHERE  m.compania=$compania
           AND pa.anno='$anno'
           AND m.numero BETWEEN '$mesI' AND '$mesF'";
$meses1 = $mysqli->query($sqlMesesD);
   
     while($filaD = mysqli_fetch_row($meses1)){
        $diaF = cal_days_in_month($calendario, $filaD[0] , $anno); 
        $fechaI=$anno.'-'.$filaD[0].'-01';
        $fechaF=$anno.'-'.$filaD[0].'-'.$diaF;
    
    
    
       
       $empleados="SELECT DISTINCT n.id_unico, COUNT(n.empleado), t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   WHERE p.fechainicio BETWEEN '$fechaI' AND '$fechaF'
                   AND p.fechafin BETWEEN '$fechaI' AND '$fechaF'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.grupo_sui='Personal Operativo Alcantarillado'";
        $countEmple = $mysqli->query($empleados);
        $resultEmp = mysqli_fetch_row($countEmple);


        ?>
     <tr>
        <td  colspan="2" align="center"><?php echo $filaD[1]?></td>
        <td  colspan="2" align="center"><?php echo $resultEmp[1]?></td>
    </tr>
        <?php
      }?>
        
    
</table>
</body>
</html>