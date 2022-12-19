<?php 
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Inf_Volante_Pago.xls");
require_once("../Conexion/conexion.php");
require_once("../Conexion/ConexionPDO.php");
$con = new ConexionPDO();
session_start();

ini_set('max_execution_time', 360);
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];

$empleado  = $_POST['sltEmpleado'];  
$periodo   = $_POST['sltPeriodo'];  

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

#** Consulta Tercero 
if(empty($empleado) || $empleado == 2){
    
    $consulta2 = "SELECT DISTINCT  e.id_unico, 
            e.tercero, 
            CONCAT_WS(' ', t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
            tc.categoria, 
            c.id_unico, 
            c.nombre, 
            c.salarioactual,
            gg.nombre,
            t.numeroidentificacion
    FROM gn_novedad n 
    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico
    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
    LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico
    WHERE e.id_unico != 2 
    AND (n.periodo) = '$periodo'";
}else{
    $consulta2 = "SELECT distinct  e.id_unico, 
            e.tercero, 
            CONCAT_WS(' ', t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
            tc.categoria, 
            c.id_unico, 
            c.nombre, 
            c.salarioactual,
            gg.nombre,
            t.numeroidentificacion                            
     FROM gn_novedad n 
    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico
    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico
    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
    LEFT JOIN gn_grupo_gestion gg ON e.grupogestion = gg.id_unico
      WHERE (n.periodo) = '$periodo' AND (e.id_unico) = '$empleado' 
      ORDER BY e.id_unico";    
}     
//echo $consulta2;                   
$empl   = $mysqli->query($consulta2);
$numemp = mysqli_num_rows($empl);


$consulta3 = "SELECT id_unico, codigointerno, DATE_FORMAT(fechainicio,'%d/%m/%Y'), DATE_FORMAT(fechafin,'%d/%m/%Y') FROM gn_periodo WHERE (id_unico) = '$periodo'";
$perio = $mysqli->query($consulta3);
$perN  = mysqli_fetch_row($perio);
$codigo = $perN[1];
$fechaI = $perN[2];
$fechaF = $perN[3];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Volante De Pago</title>
</head>
<body>

<?php 
#Consulta Tercero 
while($fila1 = mysqli_fetch_row($empl)){
  $idemp = $fila1[0];
?>  
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="6" align="center"><strong><center>
          <br/>&nbsp;
          <br/><?php echo $razonsocial ?>
          <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
          <br/>&nbsp;
          <br/>VOLANTE DE PAGO     
          <br/>&nbsp;        </center>         
          </strong>
      </td>
    </tr>
    <tr align="center">
      <td><strong>NOMINA</strong></td>
      <td colspan="2"><strong><?=$codigo;?></strong></td>
      <td><strong>NOMBRE</strong></td>
      <td colspan="2"><strong><?=$fila1[2].' - '.$fila1[8];?></strong></td>
    </tr>
    <tr align="center">
      <td><strong>FECHA INICIAL</strong></td>
      <td colspan="2"><strong><?=$fechaI;?></strong></td>
      <td><strong>CARGO</strong></td>
      <td colspan="2"><strong><?=$fila1[5];?></strong></td>
    </tr>
    <tr align="center">
      <td><strong>FECHA FINAL</strong></td>
      <td colspan="2"><strong><?=$fechaF;?></strong></td>
      <td><strong>GRUPO GESTIÓN</strong></td>
      <td colspan="2"><strong><?=$fila1[7];?></strong></td>
    </tr>

    <tr>
        <td><center><strong>CÓDIGO</strong></center></td>
        <td><center><strong>CONCEPTO</strong></center></td>
        <td><center><strong>DIAS</strong></center></td>
        <td><center><strong>DEVENGOS</strong></center></td>
        <td><center><strong>DESCUENTOS</strong></center></td>
        <td><center><strong>SALARIO BASE</strong></center></td>
    </tr>
<?php 
    #Devengos
    $consulta1 = "SELECT n.id_unico, 
       n.valor, 
       c.codigo, 
       c.descripcion,
       ca.salarioactual, 
       c.conceptorel 
       FROM gn_novedad n 
       LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
       LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
       LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
       LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
       LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
       LEFT JOIN gn_categoria ca ON ca.id_unico = tc.categoria
       WHERE n.empleado = '$idemp' AND (n.periodo) = '$periodo' AND c.clase= 1 AND n.concepto !=7 AND n.valor > 0
       ORDER BY c.id_unico"; 

    $nom = $mysqli->query($consulta1);
    $total_dv = 0;
    while($filaN = mysqli_fetch_row($nom)){
        $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = '$idemp' AND (periodo) = '$periodo' AND concepto = ".$filaN[5];
        $diast = $mysqli->query($consulta5);
        $dt = mysqli_fetch_row($diast);

        echo '<tr>
          <td>'.$filaN[2].'</td>
          <td>'.$filaN[3].'</td>
          <td>'.$dt[1].'</td>
          <td>'.number_format($filaN[1],2,'.',',').'</td>
          <td></td>
          <td>'.number_format($filaN[4],2,'.',',').'</td>
        </tr>';
        $total_dv += $filaN[1];
        
    }

    #Descuentos
    $total_ds = 0;
    $consulta4 = "SELECT n.id_unico, 
       n.valor, 
       c.codigo, 
       c.descripcion,
       ca.salarioactual
     FROM gn_novedad n 
     LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
     LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
     LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
     LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
     LEFT JOIN gn_tercero_categoria  tc ON e.id_unico = tc.empleado
     LEFT JOIN gn_categoria ca ON ca.id_unico = tc.categoria  
     WHERE n.empleado = '$idemp' AND (n.periodo) = '$periodo' AND c.clase= 2 AND n.valor > 0
     ORDER BY c.id_unico"; 

    $nomd = $mysqli->query($consulta4);
    while($filaD = mysqli_fetch_row($nomd)){
      $consulta5 = "SELECT  id_unico, valor, concepto, empleado FROM gn_novedad WHERE empleado = '$idemp' AND (periodo) = '$periodo' AND concepto = 7";
        $diast = $mysqli->query($consulta5);
        $dt = mysqli_fetch_row($diast);

        echo '<tr>
          <td>'.$filaD[2].'</td>
          <td>'.$filaD[3].'</td>
          <td>'.$dt[1].'</td>
          <td></td>
          <td>'.number_format($filaD[1],2,'.',',').'</td>          
          <td>'.number_format($filaD[4],2,'.',',').'</td>
      </tr>';
      $total_ds += $filaD[1];

    }

    $EntidadAfi = "SELECT   a.id_unico,a.empleado, a.tercero, t.id_unico,t.razonsocial,a.tipo
        FROM gn_afiliacion a 
        LEFT JOIN gf_tercero t ON a.tercero = t.id_unico
        WHERE a.empleado = '$idemp'
        ORDER BY a.tipo";
    $afient = $mysqli->query($EntidadAfi);
    $nafi = mysqli_num_rows($afient);
    if($nafi >0){
        $a = 0;
        while($afiliacion = mysqli_fetch_row($afient)){
            echo '<tr>';
            if($afiliacion[5] == 1){
              echo '<td><strong>SALUD: </strong></td>';
            }elseif($afiliacion[5] == 2){
              echo '<td><strong>PENSION: </strong></td>';
            }elseif($afiliacion[5] == 3){
              echo '<td><strong>CESANTIAS: </strong></td>';
            }elseif($afiliacion[5] == 4){
              echo '<td><strong>ARL: </strong></td>';
            }elseif($afiliacion[5] == 6){
              echo '<td><strong>CAJA C.: </strong></td>';
            }
             echo '<td colspan="2"><strong>'.$afiliacion[4].'</strong></td>';
            if($a==0){             
              echo '<td><strong>TOTAL DEVENGOS: </strong></td>
              <td colspan="2"><strong>'.number_format($total_dv,2,'.',',').'</strong></td>';
            }elseif($a==1){
              echo '<td><strong>TOTAL DESCUENTOS: </strong></td>
              <td colspan="2"><strong>'.number_format($total_ds,2,'.',',').'</strong></td>';
            }elseif($a==2){
              echo '<td><strong>NETO A PAGAR: </strong></td>
              <td colspan="2"><strong>'.number_format(($total_dv-$total_ds),2,'.',',').'</strong></td>';
            } else {
              echo '<td colspan="3"></td>';
            }
            $a++;

            echo '</tr>';

        } 
    }
   
?>
  </table>
<?php }  ?>
