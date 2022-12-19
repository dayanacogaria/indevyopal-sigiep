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

$rowp        = $con->Listar("SELECT p.fechainicio, p.fechafin, p.dias_nomina, pa.anno, p.periodo_retro , pa2.tipoprocesonomina  
    FROM  gn_periodo p 
    LEFT JOIN gf_parametrizacion_anno pa oN p.parametrizacionanno = pa.id_unico 
    LEFT JOIN gn_periodo pa2 ON p.periodo_retro =pa2.id_unico 
 WHERE p.id_unico = $periodo");

$fechaInicio = $rowp[0][0];
$fechaFin    = $rowp[0][1];        
$diasPeriodo = $rowp[0][2];       
$fechaInicial =  $rowp[0][3].'-01-01';      

if($empleador ==2){
    $rowe = $con->Listar("SELECT DISTINCT    e.id_unico, 
        e.tercero, 
        CONCAT_WS(' ', t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
        tc.categoria, 
        c.id_unico, 
        c.nombre, 
        c.salarioactual,
        e.unidadejecutora,
        cr.valor,
        e.cesantias , e.grupogestion, et.tipo  
    FROM gn_empleado e 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico 
    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado 
    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
    LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
    LEFT JOIN gn_empleado_tipo et ON et.empleado =e.id_unico 
    WHERE e.id_unico != 2 AND (((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto<= '$fechaFin' ORDER BY vr.fechaacto DESC LIMIT 1)=1) or  ((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto BETWEEN '$fechaInicio' AND '$fechaFin' ORDER BY vr.fechaacto DESC LIMIT 1)=2 )) ORDER BY `e`.`id_unico` ASC");
} else {
    $rowe = $con->Listar("SELECT DISTINCT    e.id_unico, 
        e.tercero, 
        CONCAT_WS(' ', t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
        tc.categoria, 
        c.id_unico, 
        c.nombre, 
        c.salarioactual,
        e.unidadejecutora,
        cr.valor,
        e.cesantias , e.grupogestion, et.tipo  
    FROM gn_empleado e 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico 
    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado 
    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
    LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
    LEFT JOIN gn_empleado_tipo et ON et.empleado =e.id_unico 
    WHERE e.id_unico = $empleador AND (((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto<= '$fechaFin' ORDER BY vr.fechaacto DESC LIMIT 1)=1) or  ((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto BETWEEN '$fechaInicio' AND '$fechaFin' ORDER BY vr.fechaacto DESC LIMIT 1)=2 )) ORDER BY `e`.`id_unico` ASC"); 

}
$rta =0;
$liquidar = 0;
$liquidados = 0;
for ($i=0; $i < count($rowe); $i++) {
    $empleado       = $rowe[$i][0];
    $id_empleado    = $rowe[$i][0];
    #1. ELIMINAR CONCEPTOS 
    $sql4 = "DELETE FROM gn_novedad WHERE empleado = '$id_empleado' AND periodo = '$periodo' AND aplicabilidad IN(1,2,3)"; //Borra las novedades del empleado en el periodo de apli 1
    $resultado = $mysqli->query($sql4);

    #2 PARAMETROS#
    $sql2 = "SELECT  * FROM gn_parametros_liquidacion WHERE vigencia = $panno and tipo_empleado  = ".$rowe[$i][11];
    $res = $mysqli->query($sql2);
    $rowP = mysqli_fetch_row($res);

    #Ingreso 
    $rowing = $con->Listar("SELECT fechaacto, DATE_FORMAT( fechaacto, '%d'), DATE_FORMAT( fechaacto, '%m'), DATE_FORMAT( fechaacto, '%Y')  FROM gn_vinculacion_retiro where empleado = $empleado  AND fechaacto <='$fechaFin' and estado = 1 
        ORDER BY fechaacto  DESC LIMIT 1");
    $fechaIngreso = $rowing[0][0];

    $rowsal = $con->Listar("SELECT fechaacto, DATE_FORMAT( fechaacto, '%d'), DATE_FORMAT( fechaacto, '%m'), DATE_FORMAT( fechaacto, '%Y')  FROM gn_vinculacion_retiro where empleado = $empleado  AND fechaacto <='$fechaFin' and estado = 2 AND fechaacto >='$fechaIngreso' 
        ORDER BY fechaacto  DESC LIMIT 1");
    if(empty($rowsal[0][0])){
        $fechaSalida  = $fechaFin;
    } else {
        $fechaSalida  = $rowsal[0][0];    
    }

    $fechar = new DateTime($fechaSalida);
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
    $diasPN     = $rowP[39];// Dias Prima Navidad
    $diasPSN    = $rowP[41];// Dias Prima Servicio Navidad
    $tipoL      = $rowP[40];// Dias Prima Servicio Navidad

    if(!empty($rowP[39])){

        #Auxilio Transporte
        guardarActual($empleado,'953', $periodo );
        #* Auxilio Alimentación
        guardarActual($empleado,'1005', $periodo );

        $salario = sueldo($empleado);
        $id_cs   =  id_concepto('001');
        guardarNovedad($salario, $empleado, $periodo, $id_cs);  


        $auxt    = valorConceptoPeriodo($empleado,$periodo, '953');
        $palim   = valorConceptoPeriodo($empleado,$periodo, '1005');

        #Prima Navidad 
        $id_cpn     =  id_concepto('158');
        $valorpn    = doceavaAcumulado($empleado,$id_cpn,$periodo, $fechaIngreso);
        $id_cpnf    =  id_concepto('1011');
        if($valorpn!=0){
            //guardarNovedad($valorpn, $empleado, $periodo, $id_cpnf);    
        }

        #Prima Vacaciones
        $id_cpv     =  id_concepto('175');
        $valorpv    = doceavaAcumulado($empleado,$id_cpv,$periodo, $fechaIngreso);
        $id_cpvf    =  id_concepto('804');
        if($valorpv!=0){
            guardarNovedad($valorpv, $empleado, $periodo, $id_cpvf);    
        }


        

        #Prima Bonificacin por servicios prestados
        $id_cbs     =  id_concepto('161');
        $valorbs    = doceavaAcumulado($empleado,$id_cbs,$periodo, $fechaIngreso);
        $id_cbsf    =  id_concepto('956');
        if($valorbs!=0){
            guardarNovedad($valorbs, $empleado, $periodo, $id_cbsf);    
        }


        #Prima Antiguedad
        $id_cpa     =  id_concepto('150');
        $valorpa    =  ultimoValor($empleado,$id_cpa,$periodo, $fechaIngreso);
        $id_cpaf    =  id_concepto('806');
        if($valorpa!=0){
            guardarNovedad($valorpa, $empleado, $periodo, $id_cpaf);    
        }

        #Horas Extras
        $valorhe    = doceavaHE($empleado,$periodo, $fechaIngreso);
        $id_chef    =  id_concepto('1008');
        if($valorhe!=0){
            guardarNovedad($valorhe, $empleado, $periodo, $id_chef);    
        }
                


        #**PRIMA NAVIDAD
        #DIAS PENDIENTES PRIMA N
        $diasrnv = diasPendientes2(8, $empleado, $fechaIngreso, $fechar, $fechaSalida, $periodo);
        $id_cpnv = id_concepto('L005');
        if($diasrnv>0){
            guardarNovedad($diasrnv, $empleado, $periodo, $id_cpnv);
        }
        

        #DIAS A LIQUIDAR PRIMA N
        $diasPenpn = ROUND($diasrnv*$diasPN/360, 5);
        $id_cdnv = id_concepto('039');
        if($diasPenpn>0){
            guardarNovedad($diasPenpn, $empleado, $periodo, $id_cdnv);
        }
        
        $valorpn = 0;
        if($tipoL=='Oficiales'){
            #Prima Semestral
            $id_cps     =  id_concepto('160');
            $valorps    = doceavaAcumulado($empleado,$id_cps,$periodo, $fechaIngreso);
            $id_cpsf    =  id_concepto('1002');
            if($valorps!=0){
                guardarNovedad($valorps, $empleado, $periodo, $id_cpsf);    
            }
            #Prima Navidad
            $id_cpn     =  id_concepto('158');
            $valorpn    = doceavaAcumulado($empleado,$id_cpn,$periodo, $fechaIngreso);
            $id_cpnf    =  id_concepto('1011');
            if($valorpn!=0){
                guardarNovedad($valorpn, $empleado, $periodo, $id_cpnf);    
            }
            #Salario + Prima Semestral + Auxilio Transporte + Auxilio Alimentación +Horas Extras+Prima de vacaciones+ Prima Antiguedad + Bonificación Por servicios prestados + prima servicios
            $vpnavidad = ROUND((($salario +  $auxt +$palim +$valorhe+$valorpv+ $valorpa +$valorps+$valorbs +$valorpn ) * ($diasPenpn/30)), 0);
            $id_cpnvv  = id_concepto('158');
            if($vpnavidad>0){
                guardarNovedad($vpnavidad, $empleado, $periodo, $id_cpnvv);
            }
            
            #Periodo Asociado Prima Servicios 
            if(!empty($rowp[0][4])){
                if($rowp[0][5]==2){
                    if(!empty($diasPSN)){
                        if($fechaIngreso>$fechaInicial){
                            $fechaIn = $fechaIngreso;
                        } else {
                            $fechaIn = $fechaIncial;
                        }
                        #*Dias Trabajados
                        $rowdt = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                            WHERE n.empleado = $empleado AND  c.codigo IN('009','011','035','350','351','352','353','354','355','367','368','960') 
                            AND (p.fechainicio BETWEEN '$fechaIn' AND '$fechaFin' 
                            or p.fechafin BETWEEN '$fechaIn' AND '$fechaFin' )
                            AND p.tipoprocesonomina NOT IN (12)");

                        $dias_t = $rowdt[0][0] + 30;
                        $id_conceptodt = id_concepto('038');
                        guardarNovedad($dias_t, $empleado, $periodo, $id_conceptodt);
                        
                        if($dias_t <180){
                            $pp = ROUND(($diasPSN * $dias_t)/180, 5);
                            $prima = ROUND((($salario + $auxt + $palim + $valorbs) *$pp)/30);
                            $id_conceptodp = id_concepto('090');
                            guardarNovedad($pp , $empleado, $periodo, $id_conceptodp);
                        } else {
                            $prima = ROUND((($salario + $auxt + $palim + $valorbs) *$diasPSN)/30);
                            $id_conceptodp = id_concepto('090');
                            guardarNovedad($diasPSN , $empleado, $periodo, $id_conceptodp);
                        }
                        $id_conceptops = id_concepto('160');
                        guardarNovedad($prima , $empleado, $periodo, $id_conceptops);
                    }
                }
            
            }
        } elseif($tipoL=='Supernumerarios') {

            #Periodo Asociado Prima Servicios 
            if(!empty($rowp[0][4])){
                if($rowp[0][5]==2){
                    if(!empty($diasPSN)){
                        if($fechaIngreso>$fechaInicial){
                            $fechaIn = $fechaIngreso;
                        } else {
                            $fechaIn = $fechaInicial;
                        }
                        
                        $dias_t = diasPendientes2(2, $empleado, $fechaIngreso, $fechar, $fechaSalida, $periodo);
                        $id_conceptodt = id_concepto('038');
                        
                        guardarNovedad($dias_t, $empleado, $periodo, $id_conceptodt);


                        $pp = ROUND($dias_t*$diasPSN/360, 5);
                        $prima = ROUND((($salario + $auxt + $palim + $valorbs+$valorhe) *$pp)/30);
                        echo $valorbs.'--'.$prima;
                        $id_conceptodp = id_concepto('090');
                        guardarNovedad($pp , $empleado, $periodo, $id_conceptodp);
                        
                        $id_conceptops = id_concepto('160');
                        guardarNovedad($prima , $empleado, $periodo, $id_conceptops);
                    }
                }
            
            }
            #Prima Semestral
            $id_cps     =  id_concepto('160');
            $valorps    = doceavaAcumulado($empleado,$id_cps,$periodo, $fechaIngreso);
            $id_cpsf    =  id_concepto('1002');
            if($valorps!=0){
                guardarNovedad($valorps, $empleado, $periodo, $id_cpsf);    
            }
            #Salario + Prima Semestral + Auxilio Transporte + Auxilio Alimentación +Horas Extras+Prima de vacaciones+ Prima Antiguedad + Bonificación Por servicios prestados + prima servicios
            $vpnavidad = ROUND((($salario +  $auxt +$palim +$valorbs+$valorhe) * ($diasPenpn/30)), 0);
            $id_cpnvv  = id_concepto('158');
            if($vpnavidad>0){
                guardarNovedad($vpnavidad, $empleado, $periodo, $id_cpnvv);
            }
            
        }
        
        #RETENCION FTE
        retencion($empleado,$periodo);
        #Devengos
        $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
            LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
            WHERE n.periodo = $periodo
            AND n.empleado = $empleado 
            AND c.clase = 1 and c.unidadmedida = 1");


        if(empty($dv[0][0])){
            $tdv = 0;
        } else {
            $tdv = $dv[0][0];   
        }
        $id_conceptotd = id_concepto('097');
        guardarNovedad($tdv , $id_empleado, $periodo, $id_conceptotd);

        #Descuentos
        $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
            LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
            WHERE n.periodo = $periodo
            AND n.empleado = $empleado 
            AND c.clase = 2 and c.unidadmedida = 1");
        if(empty($dv[0][0])){
            $tds = 0;
        } else {
            $tds = $dv[0][0];
        }
        $id_conceptods = id_concepto('140');
        guardarNovedad($tds , $empleado, $periodo, $id_conceptods);


        #Neto
        $np = $tdv -$tds;
        $id_conceptonp = id_concepto('144');
        $ge = guardarNovedad($np , $empleado, $periodo, $id_conceptonp);

        $rta =0;
        if(empty($ge)){
            $rta +=1;
        }   
    } else {
        
    }

} 

#Buscar si tiene periodo asociado de clase prima s 


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
        if(response>=1){
            $("#mensaje").html('Información Guardada Correctamente');  
            $("#mdlInfo").modal('show'); 
            $("#ver1").click(function(){
                document.location ='../informes_nomina/generar_INF_SABANA_NOMINAT.php?t=1&sltPeriodo=<?=$periodo?>';
            })
            
        } else {
            $("#mensaje").html('No Se Ha Podido Guardar La Información');  
            $("#mdlInfo").modal('show'); 
            document.location ='../Liquidar_GN_PRIMA_NAV.php';
        }
    })
    </script>

<?php } else {
    echo $rta;
}?>