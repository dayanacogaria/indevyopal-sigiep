<?php                                                                                     
##########################################################################################################################################################################
#13/10/2021 | Elkin O. | Se crea informe para conteo del personal 
##########################################################################################################################################################################
require_once("../Conexion/conexion.php");
require_once ('../Conexion/ConexionPDO.php');
session_start();
ini_set('max_execution_time', 0);
$con  = new ConexionPDO();
$compania = $_SESSION['compania'];
$panno = $_SESSION['anno'];
$anno=$_POST['sltAnnio'];

             
            $borrarTabla = $con->Listar("DROP TABLE gn_personal_costos");
            $create  = $con->Listar("CREATE TABLE IF NOT EXISTS gn_personal_costos (
               `id_unico` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
               `emp` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
               `Concepto` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
               `Cedula` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
               `nombre_Empleado` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
               `Grado` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
               `Consecutivo` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
               `Denominacion` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
               `Tipo_Vinculacion` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
               `Asig_Basica` double(20,2) DEFAULT NULL,
               `Gastos_Representacion` double(20,2) DEFAULT NULL,
               `PrimaTS` double(20,2) DEFAULT NULL,
               `Prima_Gestion` double(20,2) DEFAULT NULL,  
               `Prima_Localizacion` double(20,2) DEFAULT NULL,
               `Prima_Coordinacion` double(20,2) DEFAULT NULL,
               `Prima_Riesgo` double(20,2) DEFAULT NULL,
               `Prima_Extraordinaria` double(20,2) DEFAULT NULL,
               `Prima_Altomando` double(20,2) DEFAULT NULL,
               `Prima_Sub_Alimentacion` double(20,2) DEFAULT NULL,  
               `Auxilio_Transporte` double(20,2) DEFAULT NULL,
               `Prima_Antiguedad` double(20,2) DEFAULT NULL,
               `Prima_Servicios` double(20,2) DEFAULT NULL,
               `Prima_Navidad` double(20,2) DEFAULT NULL,
               `Bon_Servicios` double(20,2) DEFAULT NULL,
               `Bon_Recreacion` double(20,2) DEFAULT NULL,
               `Prima_vacaciones` double(20,2) DEFAULT NULL,
               `Prima_Actividad` double(20,2) DEFAULT NULL,
               `Otras_Primas` double(20,2) DEFAULT NULL,
               `Cesantias` double(20,2) DEFAULT NULL,
               `Intereses_Cesantias` double(20,2) DEFAULT NULL,
               `parametrizacionanno` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
               `id_cargo` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
               $vaciarTabla = $con->Listar("TRUNCATE gn_personal_costos");
                 #CONSULTA CUENTAS 
                 $rowc ="SELECT  DISTINCT n.empleado as emp,ni.codigoPersonal as Concepto, t.numeroidentificacion AS Cedula, IF(CONCAT_WS(' ',
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
                        t.apellidodos)) AS nombre_Empleado,ca.nombre AS Grado,car.codigo AS Consecutivo,car.nombre AS Denominacion,te.tipo_vinculacion AS Tipo_Vinculacion, 
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Asig_Basica' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Asig_Basica,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Gastos_Representacion' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Gastos_Representacion,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='PrimaTS' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as PrimaTS,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Gestion' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Gestion,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Localizacion' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Localizacion,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Coordinacion' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Coordinacion,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Riesgo' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Riesgo,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Extraordinaria' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Extraordinaria,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Altomando' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Altomando,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Sub_Alimentacion' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Sub_Alimentacion,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Auxilio_Transporte' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Auxilio_Transporte,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Antiguedad' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Antiguedad,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Servicios' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Servicios,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Navidad' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Navidad,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Bon_Servicios' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Bon_Servicios,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Bon_Recreacion' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Bon_Recreacion,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_vacaciones' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_vacaciones,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Prima_Actividad' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Prima_Actividad,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Otras_Primas' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Otras_Primas,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Cesantias' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Cesantias,
                      (SELECT SUM(no.valor) FROM gn_novedad no 
                      LEFT JOIN gn_periodo pe ON pe.id_unico= no.periodo
                      LEFT JOIN gn_concepto c ON c.id_unico=no.concepto
                      WHERE c.equivalente_personal_cos='Intereses_Cesantias' 
                      AND pe.parametrizacionanno=p.parametrizacionanno 
                      AND no.empleado=n.empleado) as Intereses_Cesantias,
                      car.id_unico
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      LEFT JOIN gn_concepto con ON con.id_unico=n.concepto
                      LEFT JOIN gf_cargo_tercero ct ON ct.tercero=e.tercero
                      LEFT JOIN gf_cargo car ON car.id_unico=ct.cargo
                      WHERE p.parametrizacionanno=$anno";

                   $rowCem = $mysqli->query($rowc);

                  while($empGereG=mysqli_fetch_row($rowCem)){
                     if($empGereG[1]==null){
                        $empGereG[1]=null;
                    }else{
                        $empGereG[1]= $empGereG[1];
                    }
                    
                    if($empGereG[2]==null){
                        $empGereG[2]=null;
                    }else{
                        $empGereG[2]= $empGereG[2];
                    }
                    
                    if($empGereG[3]==null){
                        $empGereG[3]=null;
                    }else{
                        $empGereG[3]= $empGereG[3];
                    }
                    
                    if($empGereG[4]==null){
                        $empGereG[4]=null;
                    }else{
                        $empGereG[4]= $empGereG[4];
                    }
                    
                    if($empGereG[5]==null){
                        $empGereG[5]=null;
                    }else{
                        $empGereG[5]= $empGereG[5];
                    }
                    
                    if($empGereG[6]==null){
                        $empGereG[6]=null;
                    }else{
                        $empGereG[6]= $empGereG[6];
                    }
                    
                    if($empGereG[7]==null){
                        $empGereG[7]=null;
                    }else{
                        $empGereG[7]= $empGereG[7];
                    }
                    
                    if($empGereG[8]==null){
                        $empGereG[8]=0;
                    }else{
                        $empGereG[8]= $empGereG[8];
                    }
                    
                    if($empGereG[9]==null){
                        $empGereG[9]=0;
                    }else{
                        $empGereG[9]= $empGereG[9];
                    }
                    
                    if($empGereG[10]==null){
                        $empGereG[10]=0;
                    }else{
                        $empGereG[10]= $empGereG[10];
                    }
                    
                    
                    if($empGereG[11]==null){
                        $empGereG[11]=0;
                    }else{
                        $empGereG[11]= $empGereG[11];
                    }
                    
                    
                    if($empGereG[12]==null){
                        $empGereG[12]=0;
                    }else{
                        $empGereG[12]= $empGereG[12];
                    }
                    
                    
                    if($empGereG[13]==null){
                        $empGereG[13]=0;
                    }else{
                        $empGereG[13]= $empGereG[13];
                    }
                    
                    
                    
                    if($empGereG[14]==null){
                        $empGereG[14]=0;
                    }else{
                        $empGereG[14]= $empGereG[14];
                    }
                    
                    
                    if($empGereG[15]==null){
                        $empGereG[15]=0;
                    }else{
                        $empGereG[15]= $empGereG[15];
                    }
                    
                    
                    if($empGereG[16]==null){
                        $empGereG[16]=0;
                    }else{
                        $empGereG[16]= $empGereG[16];
                    }
                    
                    
                    if($empGereG[17]==null){
                        $empGereG[17]=0;
                    }else{
                        $empGereG[17]= $empGereG[17];
                    }
                    
                    if($empGereG[18]==null){
                        $empGereG[18]=0;
                    }else{
                        $empGereG[18]= $empGereG[18];
                    }
                    
                    if($empGereG[19]==null){
                        $empGereG[19]=0;
                    }else{
                        $empGereG[19]= $empGereG[19];
                    }
                    
                    if($empGereG[20]==null){
                        $empGereG[20]=0;
                    }else{
                        $empGereG[20]= $empGereG[20];
                    }
                    
                    if($empGereG[21]==null){
                        $empGereG[21]=0;
                    }else{
                        $empGereG[21]= $empGereG[21];
                    }
                    
                    if($empGereG[22]==null){
                        $empGereG[22]=0;
                    }else{
                        $empGereG[22]= $empGereG[22];
                    }
                    
                    if($empGereG[23]==null){
                        $empGereG[23]=0;
                    }else{
                        $empGereG[23]= $empGereG[23];
                    }
                    
                    if($empGereG[24]==null){
                        $empGereG[24]=0;
                    }else{
                        $empGereG[24]= $empGereG[24];
                    }
                    
                    if($empGereG[25]==null){
                        $empGereG[25]=0;
                    }else{
                        $empGereG[25]= $empGereG[25];
                    }
                    
                    if($empGereG[26]==null){
                        $empGereG[26]=0;
                    }else{
                        $empGereG[26]= $empGereG[26];
                    }
                    
                    
                    if($empGereG[27]==null){
                        $empGereG[27]=0;
                    }else{
                        $empGereG[27]= $empGereG[27];
                    }
                    
                    
                    if($empGereG[28]==null){
                        $empGereG[28]=0;
                    }else{
                        $empGereG[28]= $empGereG[28];
                    }
                    
                    if($empGereG[29]==null){
                        $empGereG[29]=0;
                    }else{
                        $empGereG[29]= $empGereG[29];
                    }
                    

                     
                     $sql_cons ="INSERT INTO `gn_personal_costos` 
                     ( `emp`, `Concepto`,`Cedula`,`nombre_Empleado`, `Grado`,`Consecutivo`,
                     `Denominacion`,`Tipo_Vinculacion`,`Asig_Basica`,`Gastos_Representacion`,
                     `PrimaTS`,`Prima_Gestion`,`Prima_Localizacion`,`Prima_Coordinacion`,
                     `Prima_Riesgo`,`Prima_Extraordinaria`,`Prima_Altomando`,`Prima_Sub_Alimentacion`,
                     `Auxilio_Transporte`,`Prima_Antiguedad`,`Prima_Servicios`,`Prima_Navidad`,
                     `Bon_Servicios`,`Bon_Recreacion`,`Prima_vacaciones`,`Prima_Actividad`,
                     `Otras_Primas`,`Cesantias`,`Intereses_Cesantias`,`parametrizacionanno`,`id_cargo`) 
                      VALUES ('$empGereG[0]','$empGereG[1]','$empGereG[2]','$empGereG[3]','$empGereG[4]',
                      '$empGereG[5]','$empGereG[6]','$empGereG[7]','$empGereG[8]',$empGereG[9],
                      $empGereG[10],$empGereG[11],$empGereG[12],$empGereG[13],$empGereG[14],
                      $empGereG[15],$empGereG[16],$empGereG[17],$empGereG[18],$empGereG[19],
                      $empGereG[20],$empGereG[21],$empGereG[22],$empGereG[23],$empGereG[24],
                      $empGereG[25],$empGereG[26],$empGereG[27],$empGereG[28],'$anno','$empGereG[29]')";   
                      $countEmpleTO = $mysqli->query($sql_cons);
                     
                  }
        
             ?>

<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/style.css">
 <script src="../js/md5.pack.js"></script>
 <script src="../js/jquery.min.js"></script>
 <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
 <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
</head>
<body>
</body>
</html>
<!--Modal para informar al usuario que se ha modificado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información cargada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido modificar la información-->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informacion</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Cargando datos Personal y Costos Nomina.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          
        </div>
      </div>
    </div>
  </div>
<!--Links para dar estilos a la página-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

  <!--Vuelve a carga la página de listar mostrando la informacion modificada-->
<!--Vuelve a carga la página de listar mostrando la informacion modificada-->
<?php if($countEmpleTO==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../generar_GN_INF_PERSONAL_COSTOS_FI.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>






