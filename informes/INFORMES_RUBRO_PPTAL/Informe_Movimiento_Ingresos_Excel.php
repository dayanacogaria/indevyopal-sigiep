<?php
##############MODIFICACIONES##########################
#04/03/2017 | ERICA G. | ARREGLO BUSQUEDAS
#######################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Movimiento Presupuestal Ingresos.xls");
require_once("../../Conexion/conexion.php");
session_start();
ini_set('max_execution_time', 0);
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');

?>
<?php 
$calendario = CAL_GREGORIAN;
$anno = $mysqli->real_escape_string(''.$_SESSION['anno'].'');
$anio = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico ='$anno'";
$anio = $mysqli->query($anio);
$anio = mysqli_fetch_row($anio);
$anio = $anio[0];
#SE DEFINE LA FECHA INICIAL Y LA FECHA FINAL
if(empty($_POST['sltmesI'])){
    $fechaInicial = $anio.'-'.'01-01';
} else {
    $mes = $_POST['sltmesI'];
    $fechaInicial = $anio.'-'.$mes.'-01';
}
if(empty($_POST['sltmesF'])){
    $annioA = date('Y');
    $mesA = date('m');
    if($anio ==$annioA){
        $dia = cal_days_in_month($calendario, $mesA, $anio); 
        $fechaFinal = $anio.'-'.$mesA.'-'.$dia;
    } else {
        $fechaFinal = $anio.'-12-31';
    }
} else {
    $mes = $_POST['sltmesF'];
    $dia = cal_days_in_month($calendario, $mes, $anio);
    $fechaFinal = $anio.'-'.$mes.'-'.$dia;
}
$rubro = $mysqli->real_escape_string(''.$_POST['codigo'].'');
#SE REALIZA LA BUSQUEDA DEL RUBRO SEGUN LAS FECHAS
$con = "SELECT
      rp.codi_presupuesto   as rpcodp,
      rp.nombre             as rpnom,
      dcp.rubrofuente       as dcprf,
      dcp.tercero           as dcpter, 
      tcp.clasepptal        as tcpcla, 
      cp.fecha              as cpfecha, 
      tcp.codigo            as tcpcod, 
      cp.numero             as cpnum, 
      IF(CONCAT_WS(' ',
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
        tr.apellidodos)) AS NOMBRE, 
      
      tr.numeroidentificacion as numId,
      dcp.descripcion       as dcpdesc,
      tcp.tipooperacion     as tcptop, 
      dcp.id_unico  as idDetalle, 
      dcp.valor as valor, 
      tcp.id_unico as tipocom, 
      dcp.comprobanteafectado as coma 
    FROM
      gf_detalle_comprobante_pptal dcp
    LEFT JOIN
      gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
    LEFT JOIN
      gf_rubro_pptal rp ON rf.rubro = rp.id_unico
    LEFT JOIN
      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
    LEFT JOIN 
      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
    LEFT JOIN 
        gf_tercero tr ON tr.id_unico = dcp.tercero
    WHERE (tcp.clasepptal ='13' OR tcp.clasepptal ='18' OR tcp.clasepptal ='19') 
    AND rp.codi_presupuesto ='$rubro' AND (cp.fecha BETWEEN '$fechaInicial' AND '$fechaFinal') "
        . "ORDER BY cp.fecha, cp.numero ASC";

    $con = $mysqli->query($con);
    
    
    #CONSULTAS ENCABEZADO
#DATOS COMPAÑIA
$compania = $_SESSION['compania'];
$consulta = "SELECT t.razonsocial as traz,
            t.tipoidentificacion as tide,      
            ti.id_unico as tid,
            ti.nombre as tnom,
            t.numeroidentificacion tnum
           FROM gf_tercero t
           LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
           WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

#Inicialización parámetros Header
$nomcomp = "";
$tipodoc = "";
$numdoc = "";
if(mysqli_num_rows($cmp)>0){
    $fila = mysqli_fetch_array($cmp) ;
    $nomcomp = utf8_decode($fila['traz']);       
    $tipodoc = utf8_decode($fila['tnom']);       
    $numdoc = utf8_decode($fila['tnum']);
}
#DATOS CUENTA
$ct= "SELECT
  codi_presupuesto,
  nombre,
  fuente
FROM
  gf_rubro_pptal r
LEFT JOIN
  gf_rubro_fuente rf ON rf.rubro = r.id_unico
WHERE
  codi_presupuesto ='$rubro'";
$ct = $mysqli->query($ct);
if(mysqli_num_rows($ct)>0){
    $ct = mysqli_fetch_array($ct);
    $codNombre= $ct['codi_presupuesto'].' - '. ucwords(mb_strtolower($ct['nombre']));
   
} else {
    $codNombre= $codigo;
    
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Movimiento Presupuestal Ingresos</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <thead>
  <tr>
    <td colspan="9" bgcolor="skyblue"><CENTER><strong>Movimiento Presupuestal Ingresos</strong></CENTER></td>
  </tr>
  <tr> 
      <td colspan="9" align="center"><strong>CODIGO: <?php echo ucwords(mb_strtoupper($codNombre))?></strong></td>
  </tr>
    <tr>
        <td rowspan="2" align="center"><strong>TIPO</strong></td>
        <td rowspan="2" align="center"><strong>NÚMERO</strong></td>
        <td rowspan="2" align="center"><strong>FECHA</strong></td> 
        <td rowspan="2" align="center"><strong>VALOR</strong></td>
        <td rowspan="2" align="center"><strong>DESCRIPCIÓN</strong></td>
        <td rowspan="2" align="center"><strong>DOC. TERCERO</strong></td>
        <td rowspan="2" align="center"><strong>TERCERO</strong></td>
        <td rowspan="2" align="center"><strong>TIPO AFECTADO</strong></td>
        <td rowspan="2" align="center"><strong>AFECTADO</strong></td>
    </tr> 
        <tr></tr>
    </thead>
    <tbody>

<?php
#ESTRUCTURA DE INFORMES

if(mysqli_num_rows($con)>0){
while ($row = mysqli_fetch_array($con)) {
       
       $tipo =$row['tcpcod'];
       $numero =$row['cpnum'];
       $fecha =$row['cpfecha'];
       $date = date_create($fecha);
       $fecha=date_format($date, 'd/m/Y');
       $valor =$row['valor'];
       $descripcion = $row['dcpdesc'];
       $numTercero =$row['numId'];
       $tercero =$row['NOMBRE'];
       if(empty($row['coma'])){
            $tipoA=' ';
            $NumAfectado= ' ';
       } else {
           $compr= $row['coma'];
           $comA = "SELECT
                    tcp.codigo            as tcpcod, 
                    cp.numero             as cpnum 
                  FROM
                    gf_detalle_comprobante_pptal dcp
                  LEFT JOIN
                    gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
                  LEFT JOIN 
                    gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
                  WHERE dcp.id_unico = $compr";
           $comA = $mysqli->query($comA);
           if(mysqli_num_rows($comA)>0){
                $comAf = mysqli_fetch_array($comA);
                $tipoA=$comAf['tcpcod'];
                $NumAfectado= $comAf['cpnum'];
           } else {
              $tipoA=' ';
                $NumAfectado= ' '; 
           }
       }?>
        <tr>
        <td align="center"><?php echo $tipo;?></td>
        <td align="center"><?php echo $numero;?></td>
        <td align="center"><?php echo $fecha;?></td>
        <td align="right"><?php echo number_format($valor ,2,'.',',');?></td>
        <td align="left"><?php echo ucwords(mb_strtolower($descripcion));?></td>
        <td align="left"><?php echo $numTercero;?></td>
        <td align="left"><?php echo ucwords(mb_strtolower($tercero));?></td>
        <td align="center"><?php echo $tipoA;?></td>
        <td align="center"><?php echo $NumAfectado;?></td>
        </tr>
       <?php
       
    }
}
?>
    </tbody>
</table>
</body>
</html>
    