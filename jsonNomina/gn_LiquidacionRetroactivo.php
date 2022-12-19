<?php
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';
require '../jsonNomina/funcionesNomina.php';
@session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$usuario_t  = $_SESSION['usuario_tercero'];

$empleador  = $_REQUEST['sltEmpleado'];  
$periodo    = $_REQUEST['sltPeriodo'];  

$rowp        = $con->Listar("SELECT p.fechainicio, p.fechafin, p.dias_nomina, pa.anno  FROM  gn_periodo p 
    LEFT JOIN gf_parametrizacion_anno pa oN p.parametrizacionanno = pa.id_unico 
 WHERE p.id_unico = $periodo");

$fechaInicio = $rowp[0][0];
$fechaFin    = $rowp[0][1];        
$diasPeriodo = $rowp[0][2];       
$fechaInicial =  $rowp[0][3].'-01-01';      

if($empleador ==2){
    $rowe = $con->Listar("SELECT DISTINCT e.id_unico, 
           tc.categoria, 
           c.salarioactual,
           (SELECT MAX(vr.fecha) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico and vr.estado = 1 limit 1 ) ulmv, 
           (SELECT vr2.estado FROM gn_vinculacion_retiro vr2 WHERE vr2.empleado = e.id_unico AND vr2.fechaacto = (SELECT MAX(vr.fechaacto) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico) LIMIT 1) ulmve,
           CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos ), 
           (SELECT et.tipo FROM  gn_empleado_tipo et 
            LEFT JOIN  gn_tipo_empleado te ON  et.tipo = te.id_unico 
           WHERE et.empleado =e.id_unico  ORDER BY et.fechainicio DESC LIMIT 1 ) AS tipo, 
            (SELECT  te.porcentaje_retroactivo FROM  gn_empleado_tipo et 
            LEFT JOIN  gn_tipo_empleado te ON  et.tipo = te.id_unico 
           WHERE et.empleado =e.id_unico ORDER BY et.fechainicio DESC LIMIT 1 ) AS porcentaje_retroactivo,            
           c.salarioactual , cr.valor 
        FROM gn_empleado e 
        LEFT JOIN gf_tercero t on e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
        LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico         
        LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico 
        WHERE e.id_unico != 2 
        HAVING ulmv <'$fechaFin' AND porcentaje_retroactivo IS NOT NULL 
        ORDER BY e.id_unico"); 
} else {
    $rowe = $con->Listar("SELECT DISTINCT e.id_unico, 
           tc.categoria, 
           c.salarioactual,
           (SELECT MAX(vr.fecha) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico and vr.estado = 1 limit 1 ) ulmv, 
           (SELECT vr2.estado FROM gn_vinculacion_retiro vr2 WHERE vr2.empleado = e.id_unico AND vr2.fechaacto = (SELECT MAX(vr.fechaacto) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico) LIMIT 1) ulmve,
           CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos ), 
           (SELECT et.tipo FROM  gn_empleado_tipo et 
            LEFT JOIN  gn_tipo_empleado te ON  et.tipo = te.id_unico 
           WHERE et.empleado =e.id_unico  ORDER BY et.fechainicio DESC LIMIT 1 ) AS tipo, 
            (SELECT  te.porcentaje_retroactivo FROM  gn_empleado_tipo et 
            LEFT JOIN  gn_tipo_empleado te ON  et.tipo = te.id_unico 
           WHERE et.empleado =e.id_unico ORDER BY et.fechainicio DESC LIMIT 1 ) AS porcentaje_retroactivo,            
           c.salarioactual , cr.valor  
        FROM gn_empleado e 
        LEFT JOIN gf_tercero t on e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
        LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico         
        LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
        WHERE e.id_unico = $empleador 
        HAVING ulmv <'$fechaFin' AND porcentaje_retroactivo IS NOT NULL 
        ORDER BY e.id_unico"); 
}

$rta =0;
$liquidar = 0;
for ($i=0; $i < count($rowe); $i++) {
    if($rowe[$i][3]>$fechaInicio){
        $fechaInicial1 = $rowe[$i][3];
    } else {
        $fechaInicial1 = $fechaInicio;
    }
    $empleado = $rowe[$i][0];
    $id_empleado = $rowe[$i][0];
    #Valida que el retiro del empleado sea después del 01
    if($rowe[$i][4]==2){
        $fr = $con->Listar("SELECT * FROM `gn_vinculacion_retiro` WHERE empleado = $id_empleado AND estado = 2 AND fechaacto >'$fechaInicial'");
        if(count($fr)>0){
            $liquidar = 1;
        }
    } else {
        $liquidar = 1;
    }
    
    if($liquidar == 1){
        #1. ELIMINAR CONCEPTOS 
        $sql4 = "DELETE FROM gn_novedad WHERE empleado = '$id_empleado' AND periodo = '$periodo' AND aplicabilidad IN(1,2,3)"; //Borra las novedades del empleado en el periodo de apli 1
        $resultado = $mysqli->query($sql4);

        #2 PARAMETROS#
        $sql2 = "SELECT  * FROM gn_parametros_liquidacion WHERE vigencia = $panno and tipo_empleado  = ".$rowe[$i][6];
        $res = $mysqli->query($sql2);
        $rowP = mysqli_fetch_row($res);

        $pid        = $rowP[0]; // id de los parametros
        $pvi        = $rowP[1]; // vigencia 
        $psm        = $rowP[2]; // salario minimo de la vigencia
        $pat        = $rowP[3]; // auxilio de transporte de la vigencia 
        $ppa        = $rowP[4]; // prima de alimentacion
        $ppm        = $rowP[5]; // prima de movilidad
        $pse        = $rowP[6]; // aporte salud empleado
        $psp        = $rowP[7]; // aporte salud empresa
        $ppe        = $rowP[8]; // aporte pension empleado
        $ppp        = $rowP[9]; // aporte pension empresa
        $pfs        = $rowP[10]; // aporte fondo de solidaridad
        $per        = $rowP[11]; // Encento de retencion
        $pcc        = $rowP[12];// aporte caja de compensacion
        $psen       = $rowP[13];// aporte SENA
        $pic        = $rowP[14];// aporte ICBF
        $pes        = $rowP[15];// aporte ESAP
        $pmi        = $rowP[16];// aporte ministrerio
        $puv        = $rowP[17];// valor UVT
        $pta        = $rowP[18];// tope alimentacion
        $pad        = $rowP[19];// tope alimentacion docente
        $pie        = $rowP[20];// porcentaje de incapacidad
        $exc        = $rowP[21];// excento de parafiscales
        $diaspv     = $rowP[35];// Dias PV

        #Buscar SUELDO ANTERIOR
        $sa = $con->Listar("SELECT DISTINCT c.salarioanterior
            FROM gn_tercero_categoria tc 
            LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico 
            WHERE tc.empleado = $id_empleado ORDER BY tc.id_unico desc");        
        $salarioa = $sa[0][0];

        #BUSCAR CONCEPTOS QUE LIQUIDAN RETROACTIVO
        $rowc = $con->Listar("SELECT DISTINCT c.id_unico, c.codigo, SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        WHERE c.liquida_retroactivo = 1 
        AND n.empleado = $id_empleado
        AND (p.fechainicio BETWEEN '$fechaInicial1' AND '$fechaFin'
          OR p.fechafin BETWEEN '$fechaInicial1' AND '$fechaFin') 
        GROUP BY n.empleado, n.concepto
        ORDER BY `c`.`codigo` ASC");
        for ($c=0; $c <count($rowc) ; $c++) { 
            
            if($rowc[$c][1]=='160'){
                $cdp = $con->Listar("SELECT DISTINCT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado
                    AND c.codigo = '090'
                    AND (p.fechainicio BETWEEN '$fechaInicial1' AND '$fechaFin'
                      OR p.fechafin BETWEEN '$fechaInicial1' AND '$fechaFin')
                    GROUP BY n.empleado, n.concepto
                    ORDER BY `c`.`codigo` ASC");


                $valor = ROUND($salarioa *4/100*$cdp[0][0]/30);

                //echo $valor.':salarioa '.$salarioa.'$dias'.$cdp[0][0].'Concepto_160'.'<br/>';
            } elseif($rowc[$c][1]=='155'){
                $cdv = $con->Listar("SELECT DISTINCT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado
                    AND c.codigo = '035'
                    AND (p.fechainicio BETWEEN '$fechaInicial1' AND '$fechaFin'
                      OR p.fechafin BETWEEN '$fechaInicial1' AND '$fechaFin') 
                    AND p.tipoprocesonomina != 1 
                    GROUP BY n.empleado, n.concepto
                    ORDER BY `c`.`codigo` ASC");
                $valor = ROUND($salarioa *4/100*$cdv[0][0]/30);
                //echo $valor.':salarioa '.$salarioa.'$dias'.$cdv[0][0].'Concepto_155'.'<br/>';
            }elseif($rowc[$c][1]=='175'){
                $valor = ROUND($salarioa *4/100*$diaspv/30);
            }elseif($rowc[$c][1]=='79'){
                 $cdtra = $con->Listar("SELECT DISTINCT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado
                    AND c.codigo = '009'
                    AND (p.fechainicio BETWEEN '$fechaInicial1' AND '$fechaFin'
                      OR p.fechafin BETWEEN '$fechaInicial1' AND '$fechaFin')
                    GROUP BY n.empleado, n.concepto
                    ORDER BY `c`.`codigo` ASC");
                $dias=$cdtra[0][0]; 
                $valPA=$ppa*$dias/30;
                $valor=ROUND($valPA-$rowc[$c][2]);
            } else {
                $porcentaje = ($rowe[$i][7]/100);
                $valor = ROUND($rowc[$c][2]*$porcentaje);
            }
            if($valor !=0){
                #Guardar
                guardarNovedad($valor , $id_empleado, $periodo, $rowc[$c][0]);
            }
        }

        #LIQUIDAR OTROS CONCEPTOS 
        $conDv ="SELECT DISTINCT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado
                    AND c.codigo = '035'
                    AND (p.fechainicio BETWEEN '$fechaInicial1' AND '$fechaFin'
                      OR p.fechafin BETWEEN '$fechaInicial1' AND '$fechaFin')
                    GROUP BY n.empleado, n.concepto
                    ORDER BY `c`.`codigo` ASC";
        $cDv = $mysqli->query($conDv);
        $conDv = mysqli_fetch_row($cDv);
        if(!empty($conDv[0]) || $conDv[0] != ""  ){
         $cdtrab = $con->Listar("SELECT DISTINCT  SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado
                    AND c.codigo = '009'
                    AND (p.fechainicio BETWEEN '$fechaInicial1' AND '$fechaFin'
                      OR p.fechafin BETWEEN '$fechaInicial1' AND '$fechaFin')
                    GROUP BY n.empleado, n.concepto
                    ORDER BY `c`.`codigo` ASC");
            $diasV=$cdtrab[0][0]; 
            $salE = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado = '$id_empleado' AND n.periodo = '$periodo' AND (c.acum_ibc = '1' OR c.codigo = '002')";
            $salEm = $mysqli->query($salE);
            $salEmp = mysqli_fetch_row($salEm);
              if(empty($salEmp[0]) || $salEmp[0] == ""  ){
                  $salEmp[0] = 0;
              }
             $sal =  $salEmp[0];
             $IBC=ROUND($sal/$diasV*30);
        }else{
            $aumIBC = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        WHERE n.empleado = '$id_empleado' AND n.periodo = '$periodo' AND (c.acum_ibc = '1' OR c.codigo = '002')";
            $AIBC = $mysqli->query($aumIBC);
            $nacum = mysqli_fetch_row($AIBC);
              if(empty($nacum[0]) || $nacum[0] == ""  ){
                  $nacum[0] = 0;
              }
            $IBC =  $nacum[0];
            $IBC  = ROUND($IBC);
        } 

        if ($IBC>=($psm * 10)) {
            $exc = 1;
        }      
    

        guardarNovedad($IBC, $id_empleado, $periodo, 78);

        #SALUD
        $saludNME = ($IBC  * $pse) / 100;
        if($exc == 1){            
            $saludNME = ceil($saludNME /100) * 100;    
        }else{
            $saludNME = round($saludNME);    
        }
        guardarNovedad($saludNME, $id_empleado, $periodo, 366);     

        #PENSION
        $pensionNME = ($IBC  * $ppe) / 100;
        $pensionNME = round($pensionNME);
        guardarNovedad($pensionNME, $id_empleado, $periodo, 367);    
        if($exc == 2 ){            
            $salT = $pse + $psp;
            $saludNMP = ($IBC  * $salT) / 100;
            $saludNMP = ceil($saludNMP /100) * 100;
            $saludNMP = $saludNMP - $saludNME;
            
            guardarNovedad($saludNMP, $id_empleado, $periodo, 368);              
        }else{
            $saludNMP = 0;
        }

       
        $penT = $ppp + $ppe; 
        $pensionNMP = ($IBC  * $penT) / 100;
        $pensionNMP = ceil($pensionNMP /100) * 100;        
        $pensionNMP = $pensionNMP - $pensionNME;
        guardarNovedad($pensionNMP, $id_empleado, $periodo, 369);                    
       
        
        #consulta la ecuacion del aporte al fondo de arl por parte del patrono de los días trabajados
        $arlNMP = ($IBC * $rowe[$i][9]) / 100;
        $arlNMP = ceil($arlNMP / 10) * 10;
        $AR = intval($arlNMP);
        $dec6 = substr($AR,-2); 
        $dec6 = intval($dec6);
       
        if($dec6 != 0){
            $arlNMP = ceil($arlNMP / 100)*100;
        }
        guardarNovedad($arlNMP, $id_empleado, $periodo, 363);              

        if($exc == 2){
            
            #consulta la ecuacion del aporte al fondo de icbf por parte del patrono de los días trabajados
            $icbfNMP = ($IBC * $pic) / 100;
            $icbfNMP = ceil($icbfNMP/10)*10;
            $IC = intval($icbfNMP);
            $dec5 = substr($IC,-2);
            $dec5 = intval($dec5);
            if($dec5 != 0){
                $icbfNMP  = ceil($icbfNMP / 100)*100;
            }
            guardarNovedad($icbfNMP, $id_empleado, $periodo, 371);     

            if($pes > 0){
                #consulta la ecuacion del aporte al fondo de esap por parte del patrono de los días trabajados
                $esapNMP = ($IBC * $pes) /100;
                $esapNMP = ceil($esapNMP/10)*10;
                $ESAP = intval($esapNMP);
                $dec4 = substr($ESAP,-2);
                $dec4 = intval($dec4);
                if($dec4 != 0){
                    $esapNMP  = ceil($esapNMP / 100)*100;    
                }
                guardarNovedad($esapNMP, $id_empleado, $periodo, 372);                  
            }
            #consulta la ecuacion del aporte al fondo de sena por parte del patrono de los días trabajados
            $senaNMP = ($IBC * $psen) / 100;
            $senaNMP = ceil($senaNMP/10)*10;
            $SE = intval($senaNMP);
            $dec2 = substr($SE,-2);
            $dec2 = intval($dec2);
            if($dec2 != 0){
                $senaNMP  = ceil($senaNMP / 100)*100;
            }
            guardarNovedad($senaNMP, $id_empleado, $periodo, 373);                  
            if($pmi > 0){
                #consulta la ecuacion del aporte al fondo de ministerio de educ por parte del patrono de los días trabajados
                $minedNMP = ($IBC * $pmi) / 100;
                $minedNMP = ceil($minedNMP/10)*10;
                $MIN = intval($minedNMP);
                $dec3= substr($MIN,-2);
                $dec3 = intval($dec3);
                if($dec3 != 0){
                    $minedNMP = ceil($minedNMP / 100)*100;    
                } 
                guardarNovedad($minedNMP, $id_empleado, $periodo, 374);                                
            }                   
        }else{
            $minedNMP   = 0;
            $senaNMP    = 0;
            $esapNMP    = 0;
            $icbfNMP    = 0;       
        }

        
        #consulta la ecuacion del aporte al fondo de ccf por parte del patrono de los días trabajados
        $ccfNMP = ($IBC * $pcc) / 100;
        $ccfNMP = ceil($ccfNMP/100)*100;
        guardarNovedad($ccfNMP, $id_empleado, $periodo, 375);      


        guardarNovedad($saludNME, $id_empleado, $periodo, 80);      
        guardarNovedad($pensionNME, $id_empleado, $periodo, 84);      
        guardarNovedad($saludNMP, $id_empleado, $periodo, 82);    
        guardarNovedad($pensionNMP, $id_empleado, $periodo, 83);    
        guardarNovedad($ccfNMP, $id_empleado, $periodo, 256);    
        
        #valida si la empresa está excenta de parafiscales 
        if($exc != 1){
            #consulta la ecuacion de la suma total del aporte al fondo escuela de adminstración pública por parte del patrono
            if($pes > 0){
                $esaP = $esapNMP ; 
                guardarNovedad($esapNMP, $id_empleado, $periodo, 259);    
            }
            guardarNovedad($senaNMP, $id_empleado, $periodo, 257);                
            
            #consulta la ecuacion de la suma total del aporte al fondo de ministerio de educación por parte del patrono
            if($pmi > 0){
                guardarNovedad($minedNMP, $id_empleado, $periodo, 258);                
            }        
            #consulta la ecuacion de la suma total del aporte al fondo de instituto colombiano de bienestar familiar  por parte del patrono
            guardarNovedad($icbfNMP, $id_empleado, $periodo, 260);                            
        } 
        $hoy = date('Y-m-d');
        $tdev = "SELECT n.id_unico,"
                . "    sum( n.valor) as total, "
                . "     n.empleado, "
                . "     n.periodo, "
                . "     n.concepto, "
                . "     c.id_unico, "
                . "     c.clase "
                . "     FROM gn_novedad n "
                . "     LEFT JOIN gn_concepto c ON n.concepto = c.id_unico " 
                . "     WHERE c.clase = 1 AND n.concepto != 7 AND n.empleado = $id_empleado AND n.periodo = $periodo";
        
        $c = $mysqli->query($tdev);
        $m = mysqli_fetch_row($c);
       
       
        $tde = "SELECT n.id_unico,"
            . "     sum( n.valor) as total, "
            . "     n.empleado, "
            . "     n.periodo, "
            . "     n.concepto, "
            . "     c.id_unico, "
            . "     c.clase "
            . "     FROM gn_novedad n "
            . "     LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
            . "     WHERE c.clase = 2 AND n.concepto != 7 AND n.empleado = $id_empleado AND n.periodo = $periodo";

        $s = $mysqli->query($tde);
        $p = mysqli_fetch_row($s);
        
        if(empty($p[1]) || $p[1] == ""){
            
            $p[1] = 0;            
        }
        
        $Np = $m[1] - $p[1];
       
        $tt = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES "
            . "($m[1],'$hoy',$id_empleado,$periodo,74,1),($p[1],'$hoy',$id_empleado,$periodo,98,1),($Np,'$hoy',$id_empleado,$periodo,102,1)";
        $resultado=$mysqli->query($tt);  
        $rta =1;

    }

}

if($_REQUEST['t']==1) { 
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

    <div class="modal fade mdl-info" id="mdlInfo" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            <div id="forma-modal" class="modal-header">
              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label id="mensaje" name="mensaje"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
              <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
          </div>
        </div>
      </div>
      <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
      <script src="../js/bootstrap.min.js"></script>
    <script>  
    $(document).ready(function() {
        let response = <?php echo $rta; ?>;
        if(response==1){
            $("#mensaje").html('Información Guardada Correctamente.<?php $tt; ?>');  
            $("#mdlInfo").modal('show'); 
            $("#ver1").click(function(){
                document.location ='../informes_nomina/generar_INF_SABANA_NOMINAT.php?t=1&sltPeriodo=<?=$periodo?>';
            })
            
        } else {
            $("#mensaje").html('No Se Ha Podido Guardar La Información.<?php $tt ?>');  
            $("#mdlInfo").modal('show'); 
        }
    })
    </script>

<?php } else {
    echo $rta;
}?>