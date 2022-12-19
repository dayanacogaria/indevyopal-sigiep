<?php 
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#17/05/2018 | Erica G. | Funciones Informes Gerenciales De Presupuesto5
#27/06/2017 | Erica G. | Archivo Creado
####/################################################################################
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
session_start(); 
$action= $_REQUEST['action'];
$anno     = $_SESSION['anno'];
$compania =$_SESSION['compania'];
$calendario = CAL_GREGORIAN;
$con = new ConexionPDO();
switch ($action){
    ############MES INICIAL EJECUCION###################
    case 1:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, numero, lower(mes) FROM gf_mes WHERE parametrizacionanno = $annio ORDER BY numero ASC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>". ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay meses </option>";
        }
    break;
    ############MES FINAL EJECUCION###################
    case 2:
        $annio = $_POST['annio'];
        $ms = "SELECT id_unico, numero, lower(mes) "
                . "FROM gf_mes WHERE parametrizacionanno = $annio ORDER BY numero DESC";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>". ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay meses </option>";
        }
    break;
    ############CÓDIGO INICIAL EJECUCION GASTOS###################
    case 3:
        $annio = $_POST['annio'];
        $ms =  "SELECT DISTINCT id_unico, codi_presupuesto, "
             . "CONCAT(codi_presupuesto,' - ',lower(nombre)) "
             . "FROM gf_rubro_pptal WHERE  parametrizacionanno = $annio AND (tipoclase = 7) AND tipovigencia=1 "
             . "ORDER BY codi_presupuesto ASC ";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos </option>";
        }
    break;
    ############CÓDIGO FINAL EJECUCION GASTOS###################
    case 4:
        $annio = $_POST['annio'];
        $ms = "SELECT DISTINCT id_unico, codi_presupuesto, 
               CONCAT(codi_presupuesto,' - ',lower(nombre)) 
               FROM gf_rubro_pptal WHERE  parametrizacionanno = $annio AND (tipoclase = 7) AND tipovigencia=1
               ORDER BY codi_presupuesto DESC ";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos </option>";
        }
    break;
    ############CÓDIGO INICIAL EJECUCION INGRESOS###################
    case 5:
        $annio = $_POST['annio'];
        $ms =  "SELECT DISTINCT id_unico, codi_presupuesto, "
             . "CONCAT(codi_presupuesto,' - ',lower(nombre)) "
             . "FROM gf_rubro_pptal WHERE tipoclase = 6  AND parametrizacionanno = $annio "
             . "ORDER BY codi_presupuesto ASC ";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos </option>";
        }
    break;
    ############CÓDIGO FINAL EJECUCION INGRESOS###################
    case 6:
        $annio = $_POST['annio'];
        $ms = "SELECT DISTINCT id_unico, codi_presupuesto, "
             . "CONCAT(codi_presupuesto,' - ',lower(nombre)) "
             . "FROM gf_rubro_pptal WHERE tipoclase = 6 AND parametrizacionanno = $annio "
             . "ORDER BY codi_presupuesto DESC ";
        $ms = $mysqli->query($ms);
        if(mysqli_num_rows($ms)>0){
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[1]'>".ucwords($row[2])."</option>";
            }
        } else {
            echo "<option value=''>No hay códigos </option>";
        }
    break;
    # *** Informes Gerenciales
    case(7):
        #**********Recepción Variables ****************#
        $anno       = $_REQUEST['sltAnnio'];
        $nanno      = anno($anno);
        $mesa       = $_REQUEST['sltmes'];
        $fechaI     = $nanno.'-'.'01-01';
        $diaF       = cal_days_in_month($calendario, $mesa, $nanno); 
        $fechaF     = $nanno.'-'.$mesa.'-'.$diaF;
        $codigoI    = $_REQUEST['sltcni'];
        $codigoF    = $_REQUEST['sltcnf'];
        $tipoG      = $_REQUEST['tipoGrafico'];
        if($tipoG==1){
            gastossector($codigoI, $codigoF, $anno,$fechaI,$fechaF);
        } elseif($tipoG==2){
            gastosfuente($codigoI, $codigoF, $anno,$fechaI,$fechaF);
        } elseif($tipoG==3){
            ingresos($codigoI, $codigoF, $anno,$fechaI,$fechaF);
        }
    break;
}

function gastossector($codigoI, $codigoF, $anno,$fechaI,$fechaF){
    global $con;
    $con->Listar("TRUNCATE TABLE temporal_consulta_pptal_gastos");
    # **** Consultar Rubros Movimiento Fecha Acumulado ***#
    $rowr = $con->Listar("SELECT DISTINCT
            rpp.id_unico, 
            rpp.nombre,
            rpp.codi_presupuesto,
            sc.nombre, 
            sc.id_unico, 
            dc.rubroFuente 
          FROM 
            gf_detalle_comprobante_pptal dc 
          LEFT JOIN 
            gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico 
          LEFT JOIN 
            gf_rubro_fuente rf ON dc.rubroFuente = rf.id_unico 
          LEFT JOIN 
            gf_rubro_pptal rpp on rf.rubro = rpp.id_unico 
          LEFT JOIN
            gf_sector sc ON sc.id_unico = rpp.sector 
         WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
             AND (rpp.tipoclase = 7  OR rpp.tipoclase = 9 OR rpp.tipoclase = 8) 
             AND rpp.parametrizacionanno = $anno  
             AND cp.fecha BETWEEN '$fechaI' AND '$fechaF'     
             AND rpp.sector IS NOT NULL 
        ORDER BY rpp.codi_presupuesto ASC");
        if(count($rowr)>0){
            for ($i= 0; $i < count($rowr); $i++) {
                $rubroFuente = $rowr[$i][5];
                #PRESUPUESTO INICIAL
                $pptoInicial= presupuestos($rubroFuente, 1, $fechaI, $fechaF);
                #ADICION
                $adicion = presupuestos($rubroFuente, 2, $fechaI, $fechaF);
                #REDUCCION
                $reduccion = presupuestos($rubroFuente, 3, $fechaI, $fechaF);
                #TRAS.CRED Y CONT.
                $tras = presupuestos($rubroFuente, 4, $fechaI, $fechaF);
                    if($tras>0){
                        $trasCredito = $tras;
                        $trasCont = 0;
                    }else {
                        $trasCredito = 0;
                        $trasCont = $tras;
                    }

                #PRESUPUESTO DEFINITIVO
                $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont;
                #REGISTROS
                $registros = disponibilidades($rubroFuente, 15, $fechaI, $fechaF);
                
                $sql_cons ="INSERT INTO `temporal_consulta_pptal_gastos` 
                ( `cod_rubro`,  `nombre_rubro`, 
                `cod_fuente`,  `rubro_fuente`,
                `ptto_inicial`,`adicion`,`reduccion`,
                `tras_credito`,`tras_cont`,
                `presupuesto_dfvo`,`registros`) 
                VALUES (:cod_rubro, :nombre_rubro, 
                  :cod_fuente, :rubro_fuente, 
                  :ptto_inicial,:adicion,:reduccion,
                  :tras_credito,:tras_cont,
                  :presupuesto_dfvo,:registros)";
                $sql_dato = array(
                        array(":cod_rubro",$rowr[$i][2]),
                        array(":nombre_rubro",$rowr[$i][1]),
                        array(":cod_fuente",$rowr[$i][3]),
                        array(":rubro_fuente",$rowr[$i][4]),
                        array(":ptto_inicial",$pptoInicial),
                        array(":adicion",$adicion),
                        array(":reduccion",$reduccion),
                        array(":tras_credito",$trasCredito),
                        array(":tras_cont",$trasCont),
                        array(":presupuesto_dfvo",$presupuestoDefinitivo),
                        array(":registros",$registros),
                    
                    
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            }
        }
    return true;
}
function gastosfuente($codigoI, $codigoF, $anno,$fechaI,$fechaF){
    global $con;
    $con->Listar("TRUNCATE TABLE temporal_consulta_pptal_gastos");
    # **** Consultar Rubros Movimiento Fecha Acumulado ***#
    $rowr = $con->Listar("SELECT DISTINCT
            rpp.id_unico, 
            rpp.nombre,
            rpp.codi_presupuesto,
            f.nombre, 
            f.id_unico, 
            dc.rubroFuente 
          FROM 
            gf_detalle_comprobante_pptal dc 
          LEFT JOIN 
            gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico 
          LEFT JOIN 
            gf_rubro_fuente rf ON dc.rubroFuente = rf.id_unico 
          LEFT JOIN 
            gf_rubro_pptal rpp on rf.rubro = rpp.id_unico 
          LEFT JOIN
            gf_fuente f ON rf.fuente = f.id_unico 
         WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
             AND (rpp.tipoclase = 7  OR rpp.tipoclase = 9 OR rpp.tipoclase = 8) 
             AND rpp.parametrizacionanno = $anno  
             AND cp.fecha BETWEEN '$fechaI' AND '$fechaF'     
        ORDER BY rpp.codi_presupuesto ASC");
        if(count($rowr)>0){
            for ($i= 0; $i < count($rowr); $i++) {
                $rubroFuente = $rowr[$i][5];
                #PRESUPUESTO INICIAL
                $pptoInicial= presupuestos($rubroFuente, 1, $fechaI, $fechaF);
                #ADICION
                $adicion = presupuestos($rubroFuente, 2, $fechaI, $fechaF);
                #REDUCCION
                $reduccion = presupuestos($rubroFuente, 3, $fechaI, $fechaF);
                #TRAS.CRED Y CONT.
                $tras = presupuestos($rubroFuente, 4, $fechaI, $fechaF);
                    if($tras>0){
                        $trasCredito = $tras;
                        $trasCont = 0;
                    }else {
                        $trasCredito = 0;
                        $trasCont = $tras;
                    }

                #PRESUPUESTO DEFINITIVO
                $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont;
                #REGISTROS
                $registros = disponibilidades($rubroFuente, 15, $fechaI, $fechaF);
                
                $sql_cons ="INSERT INTO `temporal_consulta_pptal_gastos` 
                ( `cod_rubro`,  `nombre_rubro`, 
                `cod_fuente`,  `rubro_fuente`,
                `ptto_inicial`,`adicion`,`reduccion`,
                `tras_credito`,`tras_cont`,
                `presupuesto_dfvo`,`registros`) 
                VALUES (:cod_rubro, :nombre_rubro, 
                  :cod_fuente, :rubro_fuente, 
                  :ptto_inicial,:adicion,:reduccion,
                  :tras_credito,:tras_cont,
                  :presupuesto_dfvo,:registros)";
                $sql_dato = array(
                        array(":cod_rubro",$rowr[$i][2]),
                        array(":nombre_rubro",$rowr[$i][1]),
                        array(":cod_fuente",$rowr[$i][3]),
                        array(":rubro_fuente",$rowr[$i][4]),
                        array(":ptto_inicial",$pptoInicial),
                        array(":adicion",$adicion),
                        array(":reduccion",$reduccion),
                        array(":tras_credito",$trasCredito),
                        array(":tras_cont",$trasCont),
                        array(":presupuesto_dfvo",$presupuestoDefinitivo),
                        array(":registros",$registros),
                    
                    
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            }
        }
    return true;
}
function ingresos($codigoI, $codigoF, $anno,$fechaI,$fechaF){
    global $con;
    $con->Listar("TRUNCATE TABLE temporal_consulta_pptal_gastos");
    # **** Consultar Rubros Movimiento Fecha Acumulado ***#
    $rowr = $con->Listar("SELECT DISTINCT
            rpp.id_unico, 
            rpp.nombre,
            rpp.codi_presupuesto,
            f.nombre, 
            f.id_unico, 
            rpr.codi_presupuesto, 
            rf.id_unico 
          FROM 
            gf_rubro_pptal rpp 
          LEFT JOIN
            gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
          LEFT JOIN
            gf_fuente f ON rf.fuente = f.id_unico 
          LEFT JOIN 
            gf_rubro_pptal rpr ON rpp.predecesor = rpr.id_unico 
         WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
             AND (rpp.tipoclase = 6) 
             AND rpp.parametrizacionanno = $anno     
        ORDER BY rpp.codi_presupuesto ASC");
        if(count($rowr)>0){
            for ($i= 0; $i < count($rowr); $i++) {
                $rubroFuente = $rowr[$i][6];
                if(isset($rubroFuente)){
                    #PRESUPUESTO INICIAL
                    $pptoInicial= presupuestos($rubroFuente, 1, $fechaI, $fechaF);
                    #ADICION
                    $adicion = presupuestos($rubroFuente, 2, $fechaI, $fechaF);
                    #REDUCCION
                    $reduccion = presupuestos($rubroFuente, 3, $fechaI, $fechaF);
                    #TRAS.CRED Y CONT.
                    $tras = presupuestos($rubroFuente, 4, $fechaI, $fechaF);
                        if($tras>0){
                            $trasCredito = $tras;
                            $trasCont = 0;
                        }else {
                            $trasCredito = 0;
                            $trasCont = $tras;
                        }

                    #PRESUPUESTO DEFINITIVO
                    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont;
                    #RECAUDOS
                    $recaudos = disponibilidades($rubroFuente, 18, $fechaI, $fechaF);
                } else {
                    $pptoInicial        = 0;
                    $adicion            = 0;
                    $reduccion          = 0;
                    $trasCont           = 0;
                    $trasCredito        = 0;
                    $presupuestoDefinitivo = 0;
                    $recaudos           = 0;                  
                }
                $sql_cons ="INSERT INTO `temporal_consulta_pptal_gastos` 
                ( `cod_rubro`,  `nombre_rubro`, 
                `cod_fuente`,  `rubro_fuente`,
                `ptto_inicial`,`adicion`,`reduccion`,
                `tras_credito`,`tras_cont`,
                `presupuesto_dfvo`,`recaudos`,`cod_predecesor`) 
                VALUES (:cod_rubro, :nombre_rubro, 
                  :cod_fuente, :rubro_fuente, 
                  :ptto_inicial,:adicion,:reduccion,
                  :tras_credito,:tras_cont,
                  :presupuesto_dfvo,:recaudos,:cod_predecesor)";
                $sql_dato = array(
                        array(":cod_rubro",$rowr[$i][2]),
                        array(":nombre_rubro",$rowr[$i][1]),
                        array(":cod_fuente",$rowr[$i][3]),
                        array(":rubro_fuente",$rowr[$i][4]),
                        array(":ptto_inicial",$pptoInicial),
                        array(":adicion",$adicion),
                        array(":reduccion",$reduccion),
                        array(":tras_credito",$trasCredito),
                        array(":tras_cont",$trasCont),
                        array(":presupuesto_dfvo",$presupuestoDefinitivo),
                        array(":recaudos",$recaudos),
                        array(":cod_predecesor",$rowr[$i][5]),
                    
                    
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            # *** CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO *** #
            $rowa1 =$con->Listar("SELECT id_unico, cod_rubro,
                        cod_predecesor, 
                        ptto_inicial, adicion, reduccion, 
                        presupuesto_dfvo, recaudos
                    FROM 
                        temporal_consulta_pptal_gastos 
                    ORDER BY 
                        cod_rubro DESC ");
            for ($i = 0; $i < count($rowa1); $i++) {
                $rowa = $con->Listar("SELECT id_unico, 
                    cod_rubro,
                    cod_predecesor, 
                    presupuesto_dfvo, recaudos 
                    FROM temporal_consulta_pptal_gastos WHERE id_unico =".$rowa1[$i][0]."  
                    ORDER BY cod_rubro DESC ");
                for ($j = 0;$j < count($rowa);$j++) {
                    if(!empty($rowa[$j][2])){
                        $va= $con->Listar("SELECT id_unico, 
                        cod_rubro,
                        cod_predecesor, 
                        presupuesto_dfvo, recaudos
                        FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='".$rowa[$j][2]."'");
                    $presupuestoDefinitivoM = $rowa[$j][3]+$va[0][3];
                    $recaudosM              = $rowa[$j][4]+$va[0][4];
                    $sql_cons ="UPDATE `temporal_consulta_pptal_gastos` 
                        SET
                        `presupuesto_dfvo`=:presupuesto_dfvo, 
                        `recaudos`=:recaudos 
                        WHERE `cod_rubro`=:cod_rubro";
                        $sql_dato = array(
                                array(":presupuesto_dfvo",$presupuestoDefinitivoM),
                                array(":recaudos",$recaudosM),
                                array(":cod_rubro",$rowa[$j][2]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        
                        
                    }
                }
            }
            
            
        }
    return true;
}
function presupuestos($id_rubF, $tipoO, $fechaI, $fechaF)
{
    
        require'../Conexion/conexion.php';
	$presu = 0;
	$query = "SELECT valor as value 
                    FROM
                      gf_detalle_comprobante_pptal dc
                    LEFT JOIN
                      gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                    WHERE
                      dc.rubrofuente = '$id_rubF' 
                      AND tcp.tipooperacion = '$tipoO' 
                      AND cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                      AND (tcp.clasepptal = '13')";
	$ap = $mysqli->query($query);
        if(mysqli_num_rows($ap)>0){
            $sum=0;
            while ($sum1= mysqli_fetch_array($ap)) {
                $sum = $sum1['value']+$sum;
            }
        } else {
           $sum=0; 
        }
        $presu=$sum;
        
    return $presu;
}
function disponibilidades($id_rubFue, $clase, $fechaI, $fechaF)
{
    
        require'../Conexion/conexion.php';
	
	$apropiacion_def = 0;
	 $queryApro = "SELECT   detComP.valor, 
                    tipComP.tipooperacion, 
                    tipComP.nombre, rubFue.id_unico, 
                    rubFue.rubro, rubP.id_unico,  
                    rubP.nombre  
                    from gf_detalle_comprobante_pptal detComP 
                    left join gf_comprobante_pptal comP on  comP.id_unico = detComP.comprobantepptal 
                    left join gf_tipo_comprobante_pptal tipComP on tipComP.id_unico = comP.tipocomprobante 
                    left join gf_rubro_fuente rubFue on rubFue.id_unico = detComP.rubrofuente 
                    left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro 
                    where tipComP.clasepptal = '$clase' 
                    and rubFue.id_unico =  $id_rubFue AND comP.fecha BETWEEN '$fechaI' AND '$fechaF'";
        
	$apropia = $mysqli->query($queryApro);
	while($row = mysqli_fetch_row($apropia))
	{
		if(($row[1] == 2) || ($row[1] == 4) || ($row[1] == 1))
		{
			$apropiacion_def += $row[0];
		}
		elseif($row[1] == 3)
		{
                   
			$apropiacion_def -= $row[0];
		}
	}
	return $apropiacion_def;
}
###FIN EJECUCION GASTOS###
function anno($id){
    $sql= "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $id";
    $sql = $GLOBALS['mysqli']->query($sql);
    $row = mysqli_fetch_row($sql);
    $anno = $row[0];
    return ($anno);
}