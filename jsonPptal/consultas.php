<?php 
##########################################################################################
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once('./funcionesPptal.php');
require_once('./gs_auditoria_elim_cas.php');
require_once('../jsonSistema/funcionCierre.php');
@session_start();
$estruc     = $_POST['estruc'];
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$con = new ConexionPDO();
switch ($estruc){
    ############TRAER DISPONIBILIDADES PARA LA MODIFICACION#############
    case (1):
    
    $tipo = $_POST['tipo'];
    $operacion = "SELECT tipooperacion FROM gf_tipo_comprobante_pptal WHERE id_unico = $tipo ";
    $operacion = $mysqli->query($operacion);
    $operacion = mysqli_fetch_row($operacion);
    $operacion = intval($operacion[0]);
    ###ADICIONA###
    if($operacion ==2){
        $diponibilidades = "SELECT
            com.id_unico,
            com.numero,
            DATE_FORMAT(com.fecha, '%d/%m/%Y'),
            com.descripcion,
            tip.codigo,
            (SELECT
                SUM(dcp.valor)
            FROM
                gf_detalle_comprobante_pptal dcp
            WHERE
                dcp.comprobantepptal = com.id_unico
                ) AS valor
        FROM
            gf_comprobante_pptal com
        LEFT JOIN gf_tipo_comprobante_pptal tip ON
            tip.id_unico = com.tipocomprobante
        WHERE
            tip.clasepptal = 14 AND tip.tipooperacion = 1 
            AND com.parametrizacionanno = $anno 
        ORDER BY
            com.numero,
            com.fecha ASC";
        $diponibilidades =$mysqli->query($diponibilidades);
        while ($row = mysqli_fetch_row($diponibilidades)) {
            $valorDisp =0;
            $valorDis ="SELECT dcp.id_unico, dcp.rubrofuente
                        FROM gf_detalle_comprobante_pptal dcp 
                        WHERE dcp.comprobantepptal = $row[0]";
            $valorDis = $mysqli->query($valorDis);
            
            while($rowDetComp = mysqli_fetch_row($valorDis))
            {
                $IDRubroFuente = $rowDetComp[1];
                $saldoDis = apropiacion($IDRubroFuente) - disponibilidades($IDRubroFuente);
                $valorDisp +=$saldoDis;
                
            }
            if($valorDisp>0){
                $tipo = mb_strtoupper($row[4]);
                $valor = '$'.number_format($row[5], 2, '.', ',');
                echo "<option value='$row[0]'>$row[1] $tipo $row[2] $row[3] $valor</option>"; 
            }
        }
    ###REDUCE###    
    } elseif($operacion ==3){
        ########BUSCA LAS DISPONIBILIDADES#######
        $querySolAprob = "SELECT
            com.id_unico,
            com.numero,
            DATE_FORMAT(com.fecha, '%d/%m/%Y'),
            com.descripcion,
            tip.codigo, 
            (SELECT
                SUM(dcp.valor)
            FROM
                gf_detalle_comprobante_pptal dcp
            WHERE
                dcp.comprobantepptal = com.id_unico
                ) AS valor
        FROM
            gf_comprobante_pptal com
        LEFT JOIN gf_tipo_comprobante_pptal tip ON
            tip.id_unico = com.tipocomprobante
        WHERE
            tip.clasepptal = 14 AND tip.tipooperacion = 1 
            AND com.parametrizacionanno = $anno  
        ORDER BY
            com.numero,
            com.fecha ASC";
        $SolAprob = $mysqli->query($querySolAprob);
        while($row = mysqli_fetch_row($SolAprob))
        {
            
            $saldo=0;
            ##############BUSCA LOS DETALLES##########
            $queryDetCompro = "SELECT
                detComp.id_unico,
                detComp.valor
            FROM
                gf_detalle_comprobante_pptal detComp,
                gf_comprobante_pptal comP
            WHERE
                comP.id_unico = detComp.comprobantepptal AND comP.id_unico = ".$row[0];
            $saldDispo = 0;
            $totalSaldDispo = 0;
            $detCompro = $mysqli->query($queryDetCompro);
           $valorRep=0;
            while($rowDetComp = mysqli_fetch_row($detCompro))
            {
                
               $valorRep+=$rowDetComp[1];
                ########AFECTACIONES A DISPONBILIDAD#########
                $afec = "SELECT tc.tipooperacion, dc.valor, dc.id_unico FROM gf_detalle_comprobante_pptal dc "
                        . "LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico "
                        . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                        . "WHERE  dc.comprobanteafectado = $rowDetComp[0]";
                $afec = $mysqli->query($afec);
                while ($row2 = mysqli_fetch_row($afec)) {
                        if($row2[0]==2){
                            $valorRep +=$row2[1];
                        } else {
                            $valorRep -=$row2[1];
                        }

                        ########AFECTACIONES A REGISTRO#########
                        $afecR = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc "
                                . "LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico "
                                . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                                . "WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $row2[2]";
                        $afecR = $mysqli->query($afecR);
                        while ($row2R= mysqli_fetch_row($afecR)) {
                            if($row2R[0]==2){
                                $valorRep -=$row2R[1];
                            } else {
                                $valorRep +=$row2R[1];
                            }
                        }
                    
                } 
                
                $totalSaldDispo += $valorRep;
                
            }
            
            $saldo = $totalSaldDispo;

              if($saldo > 0)
              { 
                $tipo = mb_strtoupper($row[4]);
                $valor = '$'.number_format($row[5], 2, '.', ',');
                echo "<option value='$row[0]'>$row[1] $tipo $row[2] $row[3] $valor</option>"; 
              }
            }
    } else {

    }
    break;
    #########CONSULTAR NUMERO###############
    case 2:
        $id_tip_comp = $_REQUEST['id_tip_comp'];
        $parametroAnno = $_SESSION['anno'];
        $sqlAnno = 'SELECT anno 
                FROM gf_parametrizacion_anno 
                WHERE id_unico = '.$parametroAnno;
        $paramAnno = $mysqli->query($sqlAnno);
        $rowPA = mysqli_fetch_row($paramAnno);
        $numero = $rowPA[0];

        $queryNumComp = 'SELECT MAX(numero) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = '.$id_tip_comp .' 
                AND numero LIKE \''.$numero.'%\'';
        $numComp = $mysqli->query($queryNumComp);
        $row = mysqli_fetch_row($numComp);
        if($row[0] == 0)
        {
                $numero .= '000001';
        }
        else
        {
                $numero = $row[0] + 1;
        }

        echo $numero;
    break;
    ############  
    case 3:
        ############TRAER TERCEROS PARA LA MODIFICACION A REGISTRO#############
        $terceros = "SELECT DISTINCT tr.id_unico, 
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
                tr.apellidodos)) AS NOMBRE , tr.numeroidentificacion, tr.digitoverficacion 
            FROM gf_tercero tr 
            WHERE tr.compania = $compania "
            . "ORDER BY NOMBRE ASC ";
        $terceros = $mysqli->query($terceros);
        while ($row1 = mysqli_fetch_row($terceros)) {
            if(empty($row1[3])) {
            $te = ucwords(mb_strtolower($row1[1])).' '.$row1[2];
            } else {
                $te = ucwords(mb_strtolower($row1[1])).' '.$row1[2].'-'.$row1[3];
            }
            echo "<option value='$row1[0]'>$te</option>"; 
        }
        
    break;
    case (4):
    ############TRAER REGISTROS PARA LA MODIFICACION A REGISTRO#############
    $tipo      = $_POST['tipo'];
    $tercero   = $_POST['tercero'];
    $operacion = "SELECT tipooperacion FROM gf_tipo_comprobante_pptal WHERE id_unico = $tipo ";
    $operacion = $mysqli->query($operacion);
    $operacion = mysqli_fetch_row($operacion);
    $operacion = intval($operacion[0]);
    ###ADICIONA###
    if($operacion ==2){
        $diponibilidades = "SELECT
            com.id_unico,
            com.numero,
            DATE_FORMAT(com.fecha, '%d/%m/%Y'),
            com.descripcion,
            tip.codigo,
            (SELECT
                SUM(dcp.valor)
            FROM
                gf_detalle_comprobante_pptal dcp
            WHERE
                dcp.comprobantepptal = com.id_unico
                ) AS valor 
        FROM
            gf_comprobante_pptal com
        LEFT JOIN gf_tipo_comprobante_pptal tip ON
            tip.id_unico = com.tipocomprobante
        WHERE
            tip.clasepptal = 15 AND tip.tipooperacion = 1 
            AND com.parametrizacionanno = $anno 
            AND com.tercero = $tercero 
        ORDER BY
            com.numero,
            com.fecha ASC";
        $diponibilidades =$mysqli->query($diponibilidades);
        while ($row = mysqli_fetch_row($diponibilidades)) {
            $valorDisp =0;
            $valorDis ="SELECT DISTINCT dcp.id_unico, dcp.comprobanteafectado ,dcp.valor, dca.valor 
                        FROM gf_detalle_comprobante_pptal dcp 
                        LEFT JOIN gf_detalle_comprobante_pptal dca 
                        ON dcp.comprobanteafectado = dca.id_unico
                        WHERE dcp.comprobantepptal = $row[0]";
            $valorDis = $mysqli->query($valorDis);
            $afectaciones =0;
            $valorRep = 0;
            $valorD = 0;
            while($rowDetComp = mysqli_fetch_row($valorDis))
            {
                $valorRep = $rowDetComp[2];
                $valorD = $rowDetComp[3];
                ########AFECTACIONES A REGISTRO#########
                $afec = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc "
                        . "LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico "
                        . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                        . "WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $rowDetComp[0]";
                $afec = $mysqli->query($afec);
                while ($row2 = mysqli_fetch_row($afec)) {
                    if($row2[0]==2){
                        $valorRep = $valorRep+$row2[1];
                    } else {
                        $valorRep = $valorRep-$row2[1];
                    }
                } 
                ########AFECTACIONES A DISPONIBILIDAD#########
                $afec = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc "
                        . "LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico "
                        . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                        . "WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $rowDetComp[1]";
                $afec = $mysqli->query($afec);
                while ($row2 = mysqli_fetch_row($afec)) {
                    if($row2[0]==2){
                        $valorD = $valorD+$row2[1];
                    } else {
                        $valorD = $valorD-$row2[1];
                    }
                }
                $saldoDis = $valorD-$valorRep;
                $valorDisp +=$saldoDis;
                
            }
            if($valorDisp>0){
                $tipo = mb_strtoupper($row[4]);
                $valor = '$'.number_format($row[5], 2, '.', ',');
                echo "<option value='$row[0]'>$row[1] $tipo $row[2] $row[3] $valor</option>"; 
            }
        }
    ###REDUCE###    
    } elseif($operacion ==3){
        $querySolAprob = "SELECT
            com.id_unico,
            com.numero,
            DATE_FORMAT(com.fecha, '%d/%m/%Y'),
            com.descripcion,
            tip.codigo, 
            (SELECT
                SUM(dcp.valor)
            FROM
                gf_detalle_comprobante_pptal dcp
            WHERE
                dcp.comprobantepptal = com.id_unico
                ) AS valor
        FROM
            gf_comprobante_pptal com
        LEFT JOIN gf_tipo_comprobante_pptal tip ON
            tip.id_unico = com.tipocomprobante
        WHERE
            tip.clasepptal = 15 AND tip.tipooperacion = 1 
            AND com.parametrizacionanno = $anno 
             AND com.tercero = $tercero 
        ORDER BY
            com.numero,
            com.fecha ASC";
        $SolAprob = $mysqli->query($querySolAprob);
        while($row = mysqli_fetch_row($SolAprob))
        {
            $queryDetCompro = "SELECT
                detComp.id_unico,
                detComp.valor
            FROM
                gf_detalle_comprobante_pptal detComp
            WHERE
                detComp.comprobantepptal = ".$row[0];
            $saldDispo = 0;
            $totalSaldDispo = 0;
            $totalSal = 0;
            $afect=0;
            $detCompro = $mysqli->query($queryDetCompro);
            while($rowDetComp = mysqli_fetch_row($detCompro))
            {
                $valorD = $rowDetComp[1];
                $afect = "SELECT dc.valor, tc.tipooperacion, dc.id_unico  "
                        . "FROM gf_detalle_comprobante_pptal dc "
                        . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                        . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                        . "WHERE dc.comprobanteafectado = '$rowDetComp[0]'";
                $afect = $mysqli->query($afect);
                while ($row3 = mysqli_fetch_row($afect)) {
                    if(($row3[1] == 2) || ($row3[1] == 4) )
                    {
                           $valorD += $row3[0];
                    }
                    elseif($row3[1] == 3 || ($row3[1] == 1))
                    {
                           $valorD -= $row3[0];
                    } 
                    #**********************************************#
                   
                    ########AFECTACIONES A Aprobaciones#########
                    $afecR = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc "
                            . "LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico "
                            . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                            . "WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $row3[2]";
                    $afecR = $mysqli->query($afecR);
                    while ($row2R= mysqli_fetch_row($afecR)) {
                        if($row2R[0]== 2 || $row2R[0] == 4){
                            $valorD -=$row2R[1];
                        }elseif($row2R[0]== 3 || $row2R[0] == 2){
                            $valorD +=$row2R[1];
                        }
                    }
                }
                $totalSal = $valorD;
                $totalSaldDispo +=$totalSal;
            }
            $saldo = $totalSaldDispo;

              if($saldo > 0)
              { 
                $tipo = mb_strtoupper($row[4]);
                $valor = '$'.number_format($row[5], 2, '.', ',');
                echo "<option value='$row[0]'>$row[1] $tipo $row[2] $row[3] $valor</option>"; 
              }
        }
    } else {

    }
    break;
    ########CARGAR LA FUENTE QUE YA TIENE EL RUBRO(ADICION A APROPIACION)#######
    case 5:
        $rubro = $_POST['rubro'];
        echo $fr = "SELECT f.id_unico, f.nombre FROM gf_rubro_fuente rf "
                . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                . "WHERE rf.rubro = $rubro ";
        $fr= $mysqli->query($fr);
        if(mysqli_num_rows($fr)>0){
            $rf = mysqli_fetch_row($fr);
            echo "<option value='$rf[0]'>".ucwords(mb_strtolower($rf[1]))."</option>";
            $queryFue = "SELECT id_unico, nombre    
            FROM gf_fuente where id_unico !=$rf[0] AND parametrizacionanno = $anno";
            $fuente = $mysqli->query($queryFue);
            while($rowFue = mysqli_fetch_row($fuente)) { 
            echo "<option value='$rowFue[0]'>".ucwords(mb_strtolower($rowFue[1]))."</option>";
            } 
            
        } else {
           $queryFue = "SELECT id_unico, nombre    
            FROM gf_fuente WHERE parametrizacionanno = $anno";
            $fuente = $mysqli->query($queryFue);
            echo "<option value=''>Fuente</option>";
            while($rowFue = mysqli_fetch_row($fuente)) { 
            echo "<option value='$rowFue[0]'>".ucwords(mb_strtolower($rowFue[1]))."</option>";
            } 
        }
    break;
    ########VALIDAR MODIFICAR DETALLE ADICION APROPIACION#########
    case 6:
        $valor = $_POST['valor'];
        $rubrof = $_POST['rubro'];
        $idd = $_POST['id'];
        $apropiacion = apropiacionidd($rubrof,$idd)+$valor;
        $saldo = $apropiacion-disponibilidades($rubrof);
       
        if($saldo<0){
            $result = 1;
        } else {
            $result = 2;
        }
        echo json_decode($result);
    break;
    ########VALIDAR ELIMINAR DETALLE ADICION APROPIACION########
    case 7:
        $valor = $_POST['valor'];
        $rubrof = $_POST['rubro'];
        $idd = $_POST['id'];
        $apropiacion = apropiacion($rubrof)-$valor;
        $saldo = $apropiacion-disponibilidades($rubrof);
       
        if($saldo<0){
            $result = 1;
        } else {
            $result = 2;
        }
        echo json_decode($result);
    break;
    ########SALDO BANCO EGRESO########
    case 8:
        $cuentaB = $_POST['cuenta'];
        $ct = "select cuenta from gf_cuenta_bancaria WHERE id_unico =$cuentaB";
        $ct = $mysqli->query($ct);
        $ct = mysqli_fetch_row($ct);
        $cuenta = $ct[0];
         $sum = "SELECT SUM(valor) FROM gf_detalle_comprobante dc "
                . "LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico "
                . "WHERE dc.cuenta = $cuenta AND cn.parametrizacionanno = $anno";
        $sum = $mysqli->query($sum);
        if(mysqli_num_rows($sum)>0) { 
            $val= mysqli_fetch_row($sum);
            if($val[0]==NULL){
                $val=0;
            }else{
                $val = $val[0];
            }
        } else {
            $val = 0;
        }
        echo json_decode($val);
    break;
    #*****************Balanceo Presupuestal******************#
    case 9:
        $id = $_POST['id'];
        $sql = "SELECT DISTINCT id_unico, valor FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id";
        $result = $mysqli->query($sql);
        $totalcredito =0;
        $totalcontracredito =0;
        while ($row = mysqli_fetch_row($result)) {
            $credito=0;
            $contraCredito=0;
                    if($row[1] < 0)
                    {
                       $contraCredito = $row[1] * -1;
                    }
                    else
                    {
                      $credito = $row[1];
                    }
                    $totalcredito +=$credito;
                    $totalcontracredito +=$contraCredito;
        }
        if($totalcredito !=$totalcontracredito){
            $resultado  =1;
        } else {
            $resultado =0;
        }
        echo $resultado ;
    break;
     #*****************Tipo Comprobante Interfaz******************#
    case 10:
        ##Cuenta por pagar
        $cxp =$_POST['cxp'];
        $cx="SELECT tccnt.interface FROM gf_comprobante_pptal c 
            LEFT JOIN gf_tipo_comprobante_pptal tc ON c.tipocomprobante = tc.id_unico 
            LEFT JOIN gf_tipo_comprobante tccnt ON tccnt.comprobante_pptal = tc.id_unico 
            WHERE c.id_unico =  $cxp";
        $tp =$mysqli->query($cx);
        $tp = mysqli_fetch_row($tp);
        echo $tp[0]; 
    break;
    #*****************Tipo Comprobante Interfaz******************#
    case 11:
        ##Egreso Consutar CXP
        $eg = $_POST['egreso'];
        $egr = "SELECT MAX(dcx.comprobantepptal) FROM gf_detalle_comprobante_pptal dc "
                . "LEFT JOIN gf_detalle_comprobante_pptal dcx ON dc.comprobanteafectado = dcx.id_unico "
                . "WHERE dc.comprobantepptal = $eg ";
        $egr = $mysqli->query($egr);
        $egr = mysqli_fetch_row($egr);
        
        $cxp =$egr[0];
        $cx="SELECT tccnt.interface FROM gf_comprobante_pptal c 
            LEFT JOIN gf_tipo_comprobante_pptal tc ON c.tipocomprobante = tc.id_unico 
            LEFT JOIN gf_tipo_comprobante tccnt ON tccnt.comprobante_pptal = tc.id_unico 
            WHERE c.id_unico =  $cxp";
        $tp =$mysqli->query($cx);
        $tp = mysqli_fetch_row($tp);
        echo $tp[0]; 
    break;
    #****************Consultar Seguimiento Disponibilidad***************#
    case 12:
        $id_dis = $_POST['id'];
        #Consulta comprobantes afectados#
       $sql="SELECT DISTINCT 
            tc.codigo , tc.nombre, c.numero, tc.clasepptal,
            tcar.codigo, tcar.nombre, car.numero, tcar.clasepptal,
            tcop.codigo, tcop.nombre, cop.numero, tcop.clasepptal,
            tccxp.codigo, tccxp.nombre, ccxp.numero, tccxp.clasepptal, 
            tcegr.codigo, tcegr.nombre, cegr.numero, tcegr.clasepptal
        FROM 
                gf_comprobante_pptal c
        LEFT JOIN 
                gf_detalle_comprobante_pptal dc ON c.id_unico =dc.comprobantepptal 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tc ON c.tipocomprobante = tc.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dcr ON dcr.comprobanteafectado = dc.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal car ON dcr.comprobantepptal = car.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tcar ON car.tipocomprobante = tcar.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dcop ON dcop.comprobanteafectado = dcr.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal cop ON dcop.comprobantepptal = cop.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tcop ON cop.tipocomprobante = tcop.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dccxp ON dccxp.comprobanteafectado = dcop.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal ccxp ON dccxp.comprobantepptal = ccxp.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tccxp ON ccxp.tipocomprobante = tccxp.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dcegr ON dcegr.comprobanteafectado = dccxp.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal cegr ON dcegr.comprobantepptal = cegr.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tcegr ON cegr.tipocomprobante = tcegr.id_unico 
        WHERE 
        c.id_unico = $id_dis"; 
        $query = $mysqli->query($sql);
        $html ="<strong>¿Desea eliminar los siguientes comprobantes?</strong>";
        $html .="<br/><br/>";
        if(mysqli_num_rows($query)>0){
            while ($row = mysqli_fetch_row($query)) { 
                if($row[0]!=""){
                    $html .=$row[0].'  '.$row[2].'<br/>';
                }
                if($row[4]!=""){
                    $html .=$row[4].'  '.$row[6].'<br/>';
                }
                if($row[8]!=""){
                    $html .=$row[8].'  '.$row[10].'<br/>';
                }
                if($row[12]!=""){
                    $html .=$row[12]. ' '.$row[14].'<br/>';
                }
                if($row[16]!=""){
                    $html .=$row[16].'  '.$row[18].'<br/>';
                }
            }
        }
        echo $html;

    break;
    #**********Validaciones Eliminar Detalles en cascada**********#
    case 13:
        $id_dis = $_POST['id'];
        #Consulta comprobantes afectados#
        $sql="SELECT DISTINCT 
            c.id_unico,
            car.id_unico,
            cop.id_unico,
            ccxp.id_unico, 
            cegr.id_unico 
        FROM 
                gf_comprobante_pptal c
        LEFT JOIN 
                gf_detalle_comprobante_pptal dc ON c.id_unico =dc.comprobantepptal 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tc ON c.tipocomprobante = tc.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dcr ON dcr.comprobanteafectado = dc.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal car ON dcr.comprobantepptal = car.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tcar ON car.tipocomprobante = tcar.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dcop ON dcop.comprobanteafectado = dcr.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal cop ON dcop.comprobantepptal = cop.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tcop ON cop.tipocomprobante = tcop.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dccxp ON dccxp.comprobanteafectado = dcop.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal ccxp ON dccxp.comprobantepptal = ccxp.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tccxp ON ccxp.tipocomprobante = tccxp.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dcegr ON dcegr.comprobanteafectado = dccxp.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal cegr ON dcegr.comprobantepptal = cegr.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tcegr ON cegr.tipocomprobante = tcegr.id_unico 
        WHERE 
        c.id_unico = $id_dis"; 
        $query = $mysqli->query($sql);
        $c=0;
        $p=0;
        if(mysqli_num_rows($query)>0){
           while ($row = mysqli_fetch_row($query)) { 
               #Validar que no este cerrado 
                if($row[0]!=""){
                    $cierre = cierre($row[0]);
                    if($cierre==1){
                        $c+=1;
                    }
                    #Validar que no este conciliado
                    $p += conciliado($row[0]);  
                }
                if($row[1]!=""){
                    $cierre = cierre($row[1]);
                    if($cierre==1){
                        $c+=1;
                    }
                    $p += conciliado($row[1]);  
                }
                if($row[2]!=""){
                    $cierre = cierre($row[2]);
                    if($cierre==1){
                        $c+=1;
                    }
                    $p += conciliado($row[2]);  
                }
                if($row[3]!=""){
                    $cierre = cierre($row[3]);
                    if($cierre==1){
                        $c+=1;
                    }
                    $p += conciliado($row[3]);  
                }
                if($row[4]!=""){
                    $cierre = cierre($row[4]);
                    if($cierre==1){
                        $c+=1;
                    }
                    $p += conciliado($row[4]);  
                }
            }
            
            
        }
        if($c>0){
            $msj=1;
        }elseif($p>0){
            $msj=2;
        }else {
            $msj=0;
        }
        echo $msj;
    break;
    
    #**********Eliminar Detalles en cascada**********#
    case 14:
        $id_dis = $_POST['id'];
        #Consulta comprobantes afectados#
        $sql="SELECT DISTINCT 
            c.id_unico,
            car.id_unico,
            cop.id_unico,
            ccxp.id_unico, 
            cegr.id_unico 
        FROM 
                gf_comprobante_pptal c
        LEFT JOIN 
                gf_detalle_comprobante_pptal dc ON c.id_unico =dc.comprobantepptal 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tc ON c.tipocomprobante = tc.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dcr ON dcr.comprobanteafectado = dc.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal car ON dcr.comprobantepptal = car.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tcar ON car.tipocomprobante = tcar.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dcop ON dcop.comprobanteafectado = dcr.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal cop ON dcop.comprobantepptal = cop.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tcop ON cop.tipocomprobante = tcop.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dccxp ON dccxp.comprobanteafectado = dcop.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal ccxp ON dccxp.comprobantepptal = ccxp.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tccxp ON ccxp.tipocomprobante = tccxp.id_unico 
        LEFT JOIN 
                gf_detalle_comprobante_pptal dcegr ON dcegr.comprobanteafectado = dccxp.id_unico 
        LEFT JOIN 
                gf_comprobante_pptal cegr ON dcegr.comprobantepptal = cegr.id_unico 
        LEFT JOIN 
                gf_tipo_comprobante_pptal tcegr ON cegr.tipocomprobante = tcegr.id_unico 
        WHERE 
                c.id_unico = $id_dis"; 
        $query = $mysqli->query($sql);
        $c=0;
        $p=0;
        if(mysqli_num_rows($query)>0){
           while ($row = mysqli_fetch_row($query)) { 
                if($row[4]!=""){
                    #***Buscar Comprobante CNT Asociado***##
                    $ccnt = "SELECT 
                                    DISTINCT dc.comprobante 
                            FROM 
                                    gf_detalle_comprobante_pptal dcp  
                            INNER JOIN 
                                    gf_detalle_comprobante dc ON dc.detallecomprobantepptal =dcp.id_unico 
                            WHERE 
                                    dcp.comprobantepptal = $row[4] ";
                    $ccnt = $mysqli->query($ccnt);
                    if(mysqli_num_rows($ccnt)>0){
                        while ($rowcn = mysqli_fetch_row($ccnt)) {
                            #***Eliminar retenciones***#
                            $elm = eliminarRetencion($rowcn[0]);
                            $cr = "DELETE FROM gf_retencion WHERE comprobante = $rowcn[0]";
                            $cr = $mysqli->query($cr);
                            #***Actualizar Movimientos Almacen***#
                            $act = UpdatecntAlmacen($rowcn[0]);
                            $ma = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad= $rowcn[0]";
                            $ma = $mysqli->query($ma);
                            
                            #***Eliminar detalles y documentos asociados***#
                            $det = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $rowcn[0]";
                            $det = $mysqli->query($det);
                            if(mysqli_num_rows($det)>0){
                                while ($rowdet = mysqli_fetch_row($det)) {
                                    #Eliminar documentos
                                    $el = eliminardetmov('cnt', $rowdet[0]);
                                    $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $rowdet[0]";
                                    $deldoc = $mysqli->query($deldoc);
                                }
                                $el = eliminardetcnt($rowcn[0]);
                                $eldet = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $rowcn[0]";
                                $eldet = $mysqli->query($eldet);
                                $el = eliminarcnt($rowcn[0]);
                                $eldet = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $rowcn[0]";
                                $eldet = $mysqli->query($eldet);
                            }
                        }
                    } else {
                        #Buscar cnt por tipo homologado 
                        $cnt = "SELECT DISTINCT tcc.id_unico, cp.numero FROM gf_comprobante_pptal cp 
                            INNER JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            INNER JOIN gf_tipo_comprobante tcc ON tcc.comprobante_pptal = tc.id_unico 
                            WHERE cp.id_unico = $row[4]";
                        $cnt = $mysqli->query($cnt);
                        if(mysqli_num_rows($cnt)>0){
                            while ($rowcn = mysqli_fetch_row($cnt)) {
                                $com = "SELECT DISTINCT dc.comprobante FROM gf_detalle_comprobante dc 
                                        INNER JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
                                        WHERE cp.numero = '$rowcn[1]' AND tipocomprobante = $rowcn[0]";
                                $com = $mysqli->query($com);
                                if(mysqli_num_rows($com)>0){
                                    while ($rowcon = mysqli_fetch_row($com)) {
                                        #***Eliminar retenciones***#
                                        $elm = eliminarRetencion($rowcon[0]);
                                        $cr = "DELETE FROM gf_retencion WHERE comprobante = $rowcon[0]";
                                        $cr = $mysqli->query($cr);
                                         #***Actualizar Movimientos Almacen***#
                                        $act = UpdatecntAlmacen($rowcon[0]);
                                        $ma = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad= $rowcon[0]";
                                        $ma = $mysqli->query($ma);
                                        #***Eliminar detalles y documentos asociados***#
                                        $det = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $rowcon[0]";
                                        $det = $mysqli->query($det);
                                        if(mysqli_num_rows($det)>0){
                                            while ($rowdet = mysqli_fetch_row($det)) {
                                                #Eliminar documentos
                                                $el = eliminardetmov('cnt', $rowdet[0]);
                                                $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $rowdet[0]";
                                                $deldoc = $mysqli->query($deldoc);
                                            }
                                            $el = eliminardetcnt($rowcon[0]);
                                            $eldet = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $rowcon[0]";
                                            $eldet = $mysqli->query($eldet);
                                            $el = eliminarcnt($rowcon[0]);
                                            $eldet = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $rowcon[0]";
                                            $eldet = $mysqli->query($eldet);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    #***Buscar Detalles PPTL***#
                    $det = "SELECT id_unico FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $row[4]";
                    $det = $mysqli->query($det);
                    if(mysqli_num_rows($det)>0){
                        while ($rowdet = mysqli_fetch_row($det)) {
                            #Eliminar documentos
                            $el = eliminardetmov('pptal', $rowdet[0]);
                            $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantepptal = $rowdet[0]";
                            $deldoc = $mysqli->query($deldoc);
                        }
                        $el = eliminardetpptal($row[4]);
                        $eldet = "DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $row[4]";
                        $eldet = $mysqli->query($eldet);
                    }
                }
                if($row[3]!=""){
                    #***Buscar Comprobante CNT Asociado***##
                    $ccnt = "SELECT 
                                    DISTINCT dc.comprobante 
                            FROM 
                                    gf_detalle_comprobante_pptal dcp  
                            INNER JOIN 
                                    gf_detalle_comprobante dc ON dc.detallecomprobantepptal =dcp.id_unico 
                            WHERE 
                                    dcp.comprobantepptal = $row[3] ";
                    $ccnt = $mysqli->query($ccnt);
                    if(mysqli_num_rows($ccnt)>0){
                        while ($rowcn = mysqli_fetch_row($ccnt)) {
                            #***Eliminar retenciones***#
                            $elm = eliminarRetencion($rowcn[0]);
                            $cr = "DELETE FROM gf_retencion WHERE comprobante = $rowcn[0]";
                            $cr = $mysqli->query($cr);
                             #***Actualizar Movimientos Almacen***#
                            $act = UpdatecntAlmacen($rowcn[0]);
                            $ma = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad= $rowcn[0]";
                            $ma = $mysqli->query($ma);
                            #***Eliminar detalles y documentos asociados***#
                            $det = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $rowcn[0]";
                            $det = $mysqli->query($det);
                            if(mysqli_num_rows($det)>0){
                                while ($rowdet = mysqli_fetch_row($det)) {
                                    #Eliminar documentos
                                    $el = eliminardetmov('cnt', $rowdet[0]);
                                    $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $rowdet[0]";
                                    $deldoc = $mysqli->query($deldoc);
                                }
                                $el = eliminardetcnt($rowcn[0]);
                                $eldet = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $rowcn[0]";
                                $eldet = $mysqli->query($eldet);
                                $el = eliminarcnt($rowcn[0]);
                                $eldet = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $rowcn[0]";
                                $eldet = $mysqli->query($eldet);
                            }
                        }
                    } else {
                        #Buscar cnt por tipo homologado 
                        $cnt = "SELECT DISTINCT tcc.id_unico, cp.numero FROM gf_comprobante_pptal cp 
                            INNER JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            INNER JOIN gf_tipo_comprobante tcc ON tcc.comprobante_pptal = tc.id_unico 
                            WHERE cp.id_unico = $row[3]";
                        $cnt = $mysqli->query($cnt);
                        if(mysqli_num_rows($cnt)>0){
                            while ($rowcn = mysqli_fetch_row($cnt)) {
                                $com = "SELECT DISTINCT dc.comprobante FROM gf_detalle_comprobante dc 
                                        INNER JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
                                        WHERE cp.numero = '$rowcn[1]' AND tipocomprobante = $rowcn[0]";
                                $com = $mysqli->query($com);
                                if(mysqli_num_rows($com)>0){
                                    while ($rowcon = mysqli_fetch_row($com)) {
                                        #***Eliminar retenciones***#
                                        $elm = eliminarRetencion($rowcon[0]);
                                        $cr = "DELETE FROM gf_retencion WHERE comprobante = $rowcon[0]";
                                        $cr = $mysqli->query($cr);
                                         #***Actualizar Movimientos Almacen***#
                                        $act = UpdatecntAlmacen($rowcon[0]);
                                        $ma = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad= $rowcon[0]";
                                        $ma = $mysqli->query($ma);
                                        #***Eliminar detalles y documentos asociados***#
                                        $det = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $rowcon[0]";
                                        $det = $mysqli->query($det);
                                        if(mysqli_num_rows($det)>0){
                                            while ($rowdet = mysqli_fetch_row($det)) {
                                                #Eliminar documentos
                                                $el = eliminardetmov('cnt', $rowdet[0]);
                                                $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $rowdet[0]";
                                                $deldoc = $mysqli->query($deldoc);
                                            }
                                            $el = eliminardetcnt($rowcon[0]);
                                            $eldet = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $rowcon[0]";
                                            $eldet = $mysqli->query($eldet);
                                            $el = eliminarcnt($rowcon[0]);
                                            $eldet = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $rowcon[0]";
                                            $eldet = $mysqli->query($eldet);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    #***Buscar Detalles PPTL***#
                    $det = "SELECT id_unico FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $row[3]";
                    $det = $mysqli->query($det);
                    if(mysqli_num_rows($det)>0){
                        while ($rowdet = mysqli_fetch_row($det)) {
                            #Eliminar documentos
                            $el = eliminardetmov('pptal', $rowdet[0]);
                            $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantepptal = $rowdet[0]";
                            $deldoc = $mysqli->query($deldoc);
                        }
                        $el = eliminardetpptal($row[3]);
                        $eldet = "DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $row[3]";
                        $eldet = $mysqli->query($eldet);
                    }
                }
                if($row[2]!=""){
                   #***Buscar Comprobante CNT Asociado***##
                    $ccnt = "SELECT 
                                    DISTINCT dc.comprobante 
                            FROM 
                                    gf_detalle_comprobante_pptal dcp  
                            INNER JOIN 
                                    gf_detalle_comprobante dc ON dc.detallecomprobantepptal =dcp.id_unico 
                            WHERE 
                                    dcp.comprobantepptal = $row[2] ";
                    $ccnt = $mysqli->query($ccnt);
                    if(mysqli_num_rows($ccnt)>0){
                        while ($rowcn = mysqli_fetch_row($ccnt)) {
                            #***Eliminar retenciones***#
                            $elm = eliminarRetencion($rowcn[0]);
                            $cr = "DELETE FROM gf_retencion WHERE comprobante = $rowcn[0]";
                            $cr = $mysqli->query($cr);
                             #***Actualizar Movimientos Almacen***#
                            $act = UpdatecntAlmacen($rowcn[0]);
                            $ma = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad= $rowcn[0]";
                            $ma = $mysqli->query($ma);
                            #***Eliminar detalles y documentos asociados***#
                            $det = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $rowcn[0]";
                            $det = $mysqli->query($det);
                            if(mysqli_num_rows($det)>0){
                                while ($rowdet = mysqli_fetch_row($det)) {
                                    #Eliminar documentos
                                    $el = eliminardetmov('cnt', $rowdet[0]);
                                    $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $rowdet[0]";
                                    $deldoc = $mysqli->query($deldoc);
                                }
                                $el = eliminardetcnt($rowcn[0]);
                                $eldet = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $rowcn[0]";
                                $eldet = $mysqli->query($eldet);
                                $el = eliminarcnt($rowcn[0]);
                                $eldet = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $rowcn[0]";
                                $eldet = $mysqli->query($eldet);
                            }
                        }
                    } else {
                        #Buscar cnt por tipo homologado 
                        $cnt = "SELECT DISTINCT tcc.id_unico, cp.numero FROM gf_comprobante_pptal cp 
                            INNER JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            INNER JOIN gf_tipo_comprobante tcc ON tcc.comprobante_pptal = tc.id_unico 
                            WHERE cp.id_unico = $row[2]";
                        $cnt = $mysqli->query($cnt);
                        if(mysqli_num_rows($cnt)>0){
                            while ($rowcn = mysqli_fetch_row($cnt)) {
                                $com = "SELECT DISTINCT dc.comprobante FROM gf_detalle_comprobante dc 
                                        INNER JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
                                        WHERE cp.numero = '$rowcn[1]' AND tipocomprobante = $rowcn[0]";
                                $com = $mysqli->query($com);
                                if(mysqli_num_rows($com)>0){
                                    while ($rowcon = mysqli_fetch_row($com)) {
                                        #***Eliminar retenciones***#
                                        $elm = eliminarRetencion($rowcon[0]);
                                        $cr = "DELETE FROM gf_retencion WHERE comprobante = $rowcon[0]";
                                        $cr = $mysqli->query($cr);
                                         #***Actualizar Movimientos Almacen***#
                                        $act = UpdatecntAlmacen($rowcon[0]);
                                        $ma = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad= $rowcon[0]";
                                        $ma = $mysqli->query($ma);
                                        #***Eliminar detalles y documentos asociados***#
                                        $det = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $rowcon[0]";
                                        $det = $mysqli->query($det);
                                        if(mysqli_num_rows($det)>0){
                                            while ($rowdet = mysqli_fetch_row($det)) {
                                                #Eliminar documentos
                                                $el = eliminardetmov('cnt', $rowdet[0]);
                                                $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $rowdet[0]";
                                                $deldoc = $mysqli->query($deldoc);
                                            }
                                            $el = eliminardetcnt($rowcon[0]);
                                            $eldet = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $rowcon[0]";
                                            $eldet = $mysqli->query($eldet);
                                            $el = eliminarcnt($rowcon[0]);
                                            $eldet = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $rowcon[0]";
                                            $eldet = $mysqli->query($eldet);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    #***Buscar Detalles PPTL***#
                    $det = "SELECT id_unico FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $row[2]";
                    $det = $mysqli->query($det);
                    if(mysqli_num_rows($det)>0){
                        while ($rowdet = mysqli_fetch_row($det)) {
                            #Eliminar documentos
                            $el = eliminardetmov('pptal', $rowdet[0]);
                            $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantepptal = $rowdet[0]";
                            $deldoc = $mysqli->query($deldoc);
                        }
                        $el = eliminardetpptal($row[4]);
                        $eldet = "DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $row[2]";
                        $eldet = $mysqli->query($eldet);
                    }
                }
                if($row[1]!=""){
                   #***Buscar Comprobante CNT Asociado***##
                    $ccnt = "SELECT 
                                    DISTINCT dc.comprobante 
                            FROM 
                                    gf_detalle_comprobante_pptal dcp  
                            INNER JOIN 
                                    gf_detalle_comprobante dc ON dc.detallecomprobantepptal =dcp.id_unico 
                            WHERE 
                                    dcp.comprobantepptal = $row[1] ";
                    $ccnt = $mysqli->query($ccnt);
                    if(mysqli_num_rows($ccnt)>0){
                        while ($rowcn = mysqli_fetch_row($ccnt)) {
                            #***Eliminar retenciones***#
                            $elm = eliminarRetencion($rowcn[0]);
                            $cr = "DELETE FROM gf_retencion WHERE comprobante = $rowcn[0]";
                            $cr = $mysqli->query($cr);
                             #***Actualizar Movimientos Almacen***#
                            $act = UpdatecntAlmacen($rowcn[0]);
                            $ma = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad= $rowcn[0]";
                            $ma = $mysqli->query($ma);
                            #***Eliminar detalles y documentos asociados***#
                            $det = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $rowcn[0]";
                            $det = $mysqli->query($det);
                            if(mysqli_num_rows($det)>0){
                                while ($rowdet = mysqli_fetch_row($det)) {
                                    #Eliminar documentos
                                    $el = eliminardetmov('cnt', $rowdet[0]);
                                    $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $rowdet[0]";
                                    $deldoc = $mysqli->query($deldoc);
                                }
                                $el = eliminardetcnt($rowcn[0]);
                                $eldet = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $rowcn[0]";
                                $eldet = $mysqli->query($eldet);
                                $el = eliminarcnt($rowcn[0]);
                                $eldet = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $rowcn[0]";
                                $eldet = $mysqli->query($eldet);
                            }
                        }
                    } else {
                        #Buscar cnt por tipo homologado 
                        $cnt = "SELECT DISTINCT tcc.id_unico, cp.numero FROM gf_comprobante_pptal cp 
                            INNER JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            INNER JOIN gf_tipo_comprobante tcc ON tcc.comprobante_pptal = tc.id_unico 
                            WHERE cp.id_unico = $row[1]";
                        $cnt = $mysqli->query($cnt);
                        if(mysqli_num_rows($cnt)>0){
                            while ($rowcn = mysqli_fetch_row($cnt)) {
                                $com = "SELECT DISTINCT dc.comprobante FROM gf_detalle_comprobante dc 
                                        INNER JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
                                        WHERE cp.numero = '$rowcn[1]' AND tipocomprobante = $rowcn[0]";
                                $com = $mysqli->query($com);
                                if(mysqli_num_rows($com)>0){
                                    while ($rowcon = mysqli_fetch_row($com)) {
                                        #***Eliminar retenciones***#
                                        $elm = eliminarRetencion($rowcon[0]);
                                        $cr = "DELETE FROM gf_retencion WHERE comprobante = $rowcon[0]";
                                        $cr = $mysqli->query($cr);
                                         #***Actualizar Movimientos Almacen***#
                                        $act = UpdatecntAlmacen($rowcon[0]);
                                        $ma = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad= $rowcon[0]";
                                        $ma = $mysqli->query($ma);
                                        #***Eliminar detalles y documentos asociados***#
                                        $det = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $rowcon[0]";
                                        $det = $mysqli->query($det);
                                        if(mysqli_num_rows($det)>0){
                                            while ($rowdet = mysqli_fetch_row($det)) {
                                                #Eliminar documentos
                                                $el = eliminardetmov('cnt', $rowdet[0]);
                                                $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $rowdet[0]";
                                                $deldoc = $mysqli->query($deldoc);
                                            }
                                            $el = eliminardetcnt($rowcon[0]);
                                            $eldet = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $rowcon[0]";
                                            $eldet = $mysqli->query($eldet);
                                            $el = eliminarcnt($rowcon[0]);
                                            $eldet = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $rowcon[0]";
                                            $eldet = $mysqli->query($eldet);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    #***Buscar Detalles PPTL***#
                    $det = "SELECT id_unico FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $row[1]";
                    $det = $mysqli->query($det);
                    if(mysqli_num_rows($det)>0){
                        while ($rowdet = mysqli_fetch_row($det)) {
                            #Eliminar documentos
                            $el = eliminardetmov('pptal', $rowdet[0]);
                            $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantepptal = $rowdet[0]";
                            $deldoc = $mysqli->query($deldoc);
                        }
                        $el = eliminardetpptal($row[1]);
                        $eldet = "DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $row[1]";
                        $eldet = $mysqli->query($eldet);
                    }
                }
            }
        }
        #*****Eliminar Detalles Comprobante Recibido*****#
        #***Buscar Comprobante CNT Asociado***##
        $ccnt = "SELECT 
                        DISTINCT dc.comprobante 
                FROM 
                        gf_detalle_comprobante_pptal dcp  
                INNER JOIN 
                        gf_detalle_comprobante dc ON dc.detallecomprobantepptal =dcp.id_unico 
                WHERE 
                        dcp.comprobantepptal = $id_dis ";
        $ccnt = $mysqli->query($ccnt);
        if(mysqli_num_rows($ccnt)>0){
            while ($rowcn = mysqli_fetch_row($ccnt)) {
                #***Eliminar retenciones***#
                $elm = eliminarRetencion($rowcn[0]);
                $cr = "DELETE FROM gf_retencion WHERE comprobante = $rowcn[0]";
                $cr = $mysqli->query($cr);
                 #***Actualizar Movimientos Almacen***#
                $act = UpdatecntAlmacen($rowcn[0]);
                $ma = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad= $rowcn[0]";
                $ma = $mysqli->query($ma);
                #***Eliminar detalles y documentos asociados***#
                $det = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $rowcn[0]";
                $det = $mysqli->query($det);
                if(mysqli_num_rows($det)>0){
                    while ($rowdet = mysqli_fetch_row($det)) {
                        #Eliminar documentos
                        $el = eliminardetmov('cnt', $rowdet[0]);
                        $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $rowdet[0]";
                        $deldoc = $mysqli->query($deldoc);
                    }
                    $el = eliminardetcnt($rowcn[0]);
                    $eldet = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $rowcn[0]";
                    $eldet = $mysqli->query($eldet);
                    $el = eliminarcnt($rowcn[0]);
                    $eldet = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $rowcn[0]";
                    $eldet = $mysqli->query($eldet);
                }
            }
        } else {
            #Buscar cnt por tipo homologado 
            $cnt = "SELECT DISTINCT tcc.id_unico, cp.numero FROM gf_comprobante_pptal cp 
                INNER JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                INNER JOIN gf_tipo_comprobante tcc ON tcc.comprobante_pptal = tc.id_unico 
                WHERE cp.id_unico = $id_dis";
            $cnt = $mysqli->query($cnt);
            if(mysqli_num_rows($cnt)>0){
                while ($rowcn = mysqli_fetch_row($cnt)) {
                    $com = "SELECT DISTINCT dc.comprobante FROM gf_detalle_comprobante dc 
                            INNER JOIN gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
                            WHERE cp.numero = '$rowcn[1]' AND tipocomprobante = $rowcn[0]";
                    $com = $mysqli->query($com);
                    if(mysqli_num_rows($com)>0){
                        while ($rowcon = mysqli_fetch_row($com)) {
                            #***Eliminar retenciones***#
                            $elm = eliminarRetencion($rowcon[0]);
                            $cr = "DELETE FROM gf_retencion WHERE comprobante = $rowcon[0]";
                            $cr = $mysqli->query($cr);
                             #***Actualizar Movimientos Almacen***#
                            $act = UpdatecntAlmacen($rowcon[0]);
                            $ma = "UPDATE gf_movimiento SET afectado_contabilidad =NULL WHERE afectado_contabilidad= $rowcon[0]";
                            $ma = $mysqli->query($ma);
                            #***Eliminar detalles y documentos asociados***#
                            $det = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $rowcon[0]";
                            $det = $mysqli->query($det);
                            if(mysqli_num_rows($det)>0){
                                while ($rowdet = mysqli_fetch_row($det)) {
                                    #Eliminar documentos
                                    $el = eliminardetmov('cnt', $rowdet[0]);
                                    $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $rowdet[0]";
                                    $deldoc = $mysqli->query($deldoc);
                                }
                                $el = eliminardetcnt($rowcon[0]);
                                $eldet = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $rowcon[0]";
                                $eldet = $mysqli->query($eldet);
                                $el = eliminarcnt($rowcon[0]);
                                $eldet = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $rowcon[0]";
                                $eldet = $mysqli->query($eldet);
                            }
                        }
                    }
                }
            }
        }
        $res = 1;
        #***Buscar Detalles PPTL***#
        $det = "SELECT id_unico FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_dis";
        $det = $mysqli->query($det);
        if(mysqli_num_rows($det)>0){
            while ($rowdet = mysqli_fetch_row($det)) {
                #Eliminar documentos
                $el = eliminardetmov('pptal', $rowdet[0]);
                $deldoc = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantepptal = $rowdet[0]";
                $deldoc = $mysqli->query($deldoc);
            }
            $el = eliminardetpptal($id_dis);
            $eldet = "DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_dis";
            $eldet = $mysqli->query($eldet);
            if($eldet ==1){
                $res = 1;
            } else {
                $res = 2;
            }
        } 
        echo $res;
    break;
    
    #*****************Consulta Saldo Disponibilidad Nuevo, tome las apropiaciones y adiciones a disponibilidad a la fecha***#
    case 15:
        $IDRubroFuente = $_REQUEST['id_rubFue'];
        $fecha = fechaC($_POST['fecha']);
        $saldoDisponible = apropiacion($IDRubroFuente) - disponibilidades($IDRubroFuente) ;
        
        echo $saldoDisponible;
    break;

    #***********************Validar Eliminar Comprobante Cnt***************#
    case 16:
        $id = $_REQUEST['id'];
        $rta =0;
        #**Validar Cierre***#
        $cer = cierrecnt($id);
        if($cer==1){
            $rta =1;
        } else {
            #***Validar No Sea Comprobante De Interfaz Almacen***#
            $ia = "SELECT * FROM gf_interfaz_deterioro WHERE comprobante =$id";
            $ia = $mysqli->query($ia);
            if(mysqli_num_rows($ia)>0){
                #***Validar Si Alguna Cuenta Esta Conciliada***#
                $cn = "SELECT * FROM gf_detalle_comprobante WHERE comprobante = $id AND conciliado =1";
                $cn = $mysqli->query($cn);
                if(mysqli_num_rows($cn)>0){
                    $rta = 3;
                } else {
                    $rta =2;
                }
            } else {
                #***Validar Si Alguna Cuenta Esta Conciliada***#
                $cn = "SELECT * FROM gf_detalle_comprobante WHERE comprobante = $id AND conciliado =1";
                $cn = $mysqli->query($cn);
                if(mysqli_num_rows($cn)>0){
                    $rta = 3;
                }
            }
        }
        echo $rta;
    
    break;
    #**********************Eliminar Comprobante CNT******************#
    case 17:
        $id = $_REQUEST['id'];
        #***Validar No Sea Comprobante De Interfaz Almacen***#
        $ia = "SELECT * FROM gf_interfaz_deterioro WHERE comprobante =$id";
        $ia = $mysqli->query($ia);
        if(mysqli_num_rows($ia)>0){
            $iad = "DELETE FROM gf_interfaz_deterioro WHERE comprobante =$id";
            $iad = $mysqli->query($iad);
        }
        #*****Eliminar Documentos*******#
        $dc = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $id";
        $dc = $mysqli->query($dc);
        while ($row = mysqli_fetch_row($dc)) {
            $dl = "DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = ".$row[0];            
            $dl = $mysqli->query($dl);
        }
        #********Eliminar Detalles*****#
        $ddc = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $id";
        $dcc = $mysqli->query($ddc);
        if($dcc ==true){
            $rta =1;
        } else {
            $rta =2;
        }
        echo $rta;
    break;
    #Validar Si Hay Saldo
    case 18:
        $IDRubroFuente = $_REQUEST['id_rubFue'];
        $fecha = fechaC($_POST['fecha']);
        $saldoFecha  = apropiacionfecha($IDRubroFuente, $fecha) - disponibilidadesfecha($IDRubroFuente, $fecha) ;
        $saldoActual = apropiacion($IDRubroFuente) - disponibilidades($IDRubroFuente) ;
        $saldDispo = min($saldoFecha,$saldoActual);
        echo $saldDispo;
        
    break;
    #********Modificar Apobación*********#
    case 19:
        $id     = $_REQUEST['id'];
        $fecha  = fechaC($_REQUEST['fecha']);
        $terc   = $_REQUEST['tercero'];
        
        $upd = "UPDATE gf_comprobante_pptal SET tercero ='$terc', fecha = '$fecha' WHERE id_unico = $id";
        $res = $mysqli->query($upd);
        if($res ==true){
            $rta =1;
        } else {
            $rta =2;
        }
        echo $rta;
    break;
    #********Buscar Si Apobación Esta Vacía O No*********#
    case 20:
        $id = $_REQUEST['id'];
        $sl = "SELECT * FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id";
        $sl = $mysqli->query($sl);
        if(mysqli_num_rows($sl)>0){
            $rta =1;
        } else {
            $rta =2;
        }
        echo $rta;
    break;
    case 21:
        $ter = $_REQUEST['tercero'];
        $sl = "SELECT t.id_unico, 
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
                IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                t.numeroidentificacion, CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
            FROM gf_tercero t WHERE compania = $compania";
        $sl = $mysqli->query($sl);
        while ($row = mysqli_fetch_row($sl)) {
            echo '<option value="'.$row[0].'">'.ucwords(mb_strtolower($row[1])).' - '.$row[2].'</option>';
        }
    break;
    case 22:
        $sl = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_REQUEST['id'];
        $sl = $mysqli->query($sl);
        $sl = mysqli_fetch_row($sl);
        $fecha = trim($sl[0]);
        echo trim(date("d/m/Y", strtotime($fecha)));
    break;
    #**********Agregar Registro A Aprobación********#
    case 23:
        $idA = $_REQUEST['id'];
        $ter = $_REQUEST['ter'];
        $reg = $_REQUEST['reg'];
        $fec = fechaC($_REQUEST['fec']);
        #******** Busscar Fecha Registro ******#
        $fr = "SELECT fecha FROM gf_comprobante_pptal where id_unico = $reg";
        $fr = $mysqli->query($fr);
        $fr = mysqli_fetch_row($fr);
        $fr = $fr[0];
        
        #********** Comparar Fechas ******#
        if($fr>$fec) {
            $rta =3;
        } else {
            #**Buscar Y Registrar Detalles Registro***#
            $queryAntiguoDetallPttal = "SELECT 
                                        detComP.descripcion, 
                                        detComP.valor, 
                                        detComP.rubrofuente, 
                                        detComP.tercero, detComP.proyecto, 
                                        detComP.id_unico, detComP.conceptorubro  , detComP.centro_costo 
                                    FROM gf_detalle_comprobante_pptal detComP
                                    where detComP.comprobantepptal = $reg";
            $resultado = $mysqli->query($queryAntiguoDetallPttal);
            $comprobantepptal = $idA;
            while($row = mysqli_fetch_row($resultado))
            {
                $saldDisp = $row[1];
                $totalAfec = 0;
                $queryDetAfe = "SELECT DISTINCT
                  dcp.valor,
                  tc.tipooperacion, dcp.id_unico 
                FROM
                  gf_detalle_comprobante_pptal dcp
                LEFT JOIN
                  gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico
                WHERE
                  dcp.comprobanteafectado =".$row[5];
                $detAfec = $mysqli->query($queryDetAfe);
                $totalAfe = 0;
                while($rowDtAf = mysqli_fetch_row($detAfec))
                {
                    if(($rowDtAf[1] == 3) ){
                          $saldDisp = $saldDisp - $rowDtAf[0];
                    } elseif(($rowDtAf[1] == 2) || ($rowDtAf[1] == 4)){
                            $saldDisp = $saldDisp + $rowDtAf[0];
                        } else {
                           $saldDisp = $saldDisp- $rowDtAf[0]; 
                   }
                }
                $valorPpTl = $saldDisp;
                if($valorPpTl > 0)
                {
                $valor = $valorPpTl;
                $rubro = $row[2];
                $tercero = $row[3]; 
                $proyecto = $row[4];
                $idAfectado = $row[5];
                $conceptorubro = $row[6];

                $campo = "";
                $variable = "";
                if(empty($row[0])){
                    $descripcion =NULL;
                } else {
                    $descripcion = $row[0];
                }
                if(!empty($row[7])){
                    $centro_costo = $row[7];
                } else {
                    $centro_costo = 'NULL';
                }
                
                $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal "
                        . "(descripcion, valor, comprobantepptal, rubrofuente, tercero, proyecto, "
                        . "comprobanteafectado, conceptorubro, centro_costo) "
                        . "VALUES ('$descripcion', '$valor', '$comprobantepptal', "
                        . "'$rubro', '$tercero', '$proyecto', '$idAfectado', '$conceptorubro', $centro_costo)";
                $resultadoInsert = $mysqli->query($insertSQL);
              }
            }
            #Modificar Comprobante
            $cm = "UPDATE gf_comprobante_pptal SET fecha ='$fec', tercero = $tercero WHERE id_unico = $idA";
            $cm = $mysqli->query($cm);
            if($cm ==true){
                $rta = 1;
            } else {
                $rta = 2;
            }
        }
        echo $rta;
    break;
    #########CONSULTAR NUMERO CNT###############
    case 24:
        $id_tip_comp = $_REQUEST['id_tip_comp'];
        $parametroAnno = $_SESSION['anno'];
        $sqlAnno = 'SELECT anno 
                FROM gf_parametrizacion_anno 
                WHERE id_unico = '.$parametroAnno;
        $paramAnno = $mysqli->query($sqlAnno);
        $rowPA = mysqli_fetch_row($paramAnno);
        $numero = $rowPA[0];

        $queryNumComp = "SELECT MAX(cast(numero as unsigned)) 
                FROM gf_comprobante_cnt  
                WHERE tipocomprobante = '$id_tip_comp' 
                AND parametrizacionanno = $parametroAnno";
        $numComp = $mysqli->query($queryNumComp);
        $row = mysqli_fetch_row($numComp);
        if($row[0] == 0)
        {
                $numero .= '000001';
        }
        else
        {
                $numero = $row[0] + 1;
        }

        echo $numero;
    break;
    #********* Buscar Comprobantes CNT Por Tipo ************#
    case 25:
        # Consulta para datos de busqueda
        $tipo = $_REQUEST['tipo'];
        $parametroAnno = $_SESSION['anno'];
        echo "<option value>Buscar Comprobante</option>";
        ###########################################################################################################################
        $sqlCP = "SELECT    cnt.id_unico,
                            cnt.numero,
                            tpc.sigla,
                            IF(CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos) IS NULL 
                            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                            (ter.razonsocial),
                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos  )) AS NOMBRE,
                            ter.numeroidentificacion, cnt.fecha, ter.digitoverficacion   
                FROM        gf_comprobante_cnt cnt
                LEFT JOIN   gf_tipo_comprobante tpc     ON cnt.tipocomprobante      = tpc.id_unico
                LEFT JOIN   gf_tercero ter              ON cnt.tercero              = ter.id_unico
                LEFT JOIN   gf_tipo_identificacion ti   ON ter.tipoidentificacion   = ti.id_unico
                WHERE       tpc.id_unico=$tipo AND cnt.parametrizacionanno = $parametroAnno 
                ORDER BY    cast(cnt.numero as unsigned) DESC";
        $resultCP = $mysqli->query($sqlCP);
        ##########################################################################################################################
        # Consulta para datos de busqueda                                        
        ###########################################################################################################################
        while ($rowCP = mysqli_fetch_row($resultCP)) {
            $date= new DateTime($rowCP[5]);
            $f= $date->format('d/m/Y');
            ######################################################################################################################
            # Consulta de valor de comprobante
            #
            ######################################################################################################################
            $sqlVA = "SELECT SUM(IF (dtc.valor<0, dtc.valor*-1, dtc.valor) )
                    FROM      gf_detalle_comprobante dtc 
                    LEFT JOIN gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico 
                    LEFT JOIN gf_cuenta c ON dtc.cuenta = c.id_unico 
                    WHERE     cnt.id_unico = $rowCP[0] AND (c.naturaleza = 1 
                    AND dtc.valor>0 OR c.naturaleza =2 AND dtc.valor<0);";
            $resultVA = $mysqli->query($sqlVA);
            $valorVA = mysqli_fetch_row($resultVA);
            ######################################################################################################################
            # Impresión de valores
            #
            ######################################################################################################################
            if(empty($rowCP[6])) {
            echo "<option value=".$rowCP[0].">".$rowCP[1]." ".$rowCP[2]." ".$f." ".ucwords(mb_strtolower($rowCP[3])).' '.$rowCP[4]." $".number_format($valorVA[0],2,',','.')."</option>";
            } else {
                echo "<option value=".$rowCP[0].">".$rowCP[1]." ".$rowCP[2]." ".$f." ".ucwords(mb_strtolower($rowCP[3])).' '.$rowCP[4].'-'.$rowCP[6]." $".number_format($valorVA[0],2,',','.')."</option>";
            }
        }
    break;
    # **** Buscar Factura ****#
    case 26:
        $tipo = $_REQUEST['tipo'];
        echo "<option value>Buscar Factura</option>";
        $sqlB = "SELECT     fat.id_unico,
                    fat.numero_factura,
                    tpf.prefijo,
                    IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                    CONCAT(ter.numeroidentificacion) AS 'TipoD', 
                    DATE_FORMAT(fat.fecha_factura, '%d/%m/%Y')
        FROM        gp_factura fat
        LEFT JOIN   gp_tipo_factura tpf ON tpf.id_unico = fat.tipofactura
        LEFT JOIN   gf_tercero ter ON ter.id_unico = fat.tercero
        LEFT JOIN   gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion 
        WHERE fat.parametrizacionanno = $anno AND fat.tipofactura = $tipo 
        ORDER BY cast(numero_factura as unsigned)  DESC ";
        $resultB = $mysqli->query($sqlB);
        while ($rowB = mysqli_fetch_row($resultB)) {
            $sqlDF = "SELECT SUM(dtf.valor_total_ajustado) FROM gp_detalle_factura dtf WHERE factura = $rowB[0]";
            $resultDF = $mysqli->query($sqlDF);
            $valDF = mysqli_fetch_row($resultDF);
            echo "<option value=".$rowB[0].">".$rowB[1]." ".$rowB[2]." ".$rowB[5]." ".ucwords(mb_strtolower($rowB[3]))." - ".ucwords(mb_strtolower($rowB[4]))." "."$".number_format($valDF[0],2,'.',',')."</option>";
        }
    break;
    case 27:
        echo "<option value>Buscar Recuado</option>";
        $sqlB = "SELECT     pg.id_unico,
                            pg.numero_pago,
                            tpg.nombre,
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
                            tr.numeroidentificacion ,
                            DATE_FORMAT(pg.fecha_pago, '%d/%m/%Y')
                FROM        gp_pago pg
                LEFT JOIN   gp_tipo_pago tpg    ON tpg.id_unico = pg.tipo_pago
                LEFT JOIN   gf_tercero tr      ON tr.id_unico = pg.responsable
                LEFT JOIN   gf_tipo_identificacion ti   ON ti.id_unico = tr.tipoidentificacion 
                WHERE pg.parametrizacionanno = $anno 
                ORDER BY    pg.numero_pago DESC";
        $resultB = $mysqli->query($sqlB);
        while ($rowB = mysqli_fetch_row($resultB)) {
            $sqlVal = " SELECT  SUM(valor)
                        FROM    gp_detalle_pago
                        WHERE   pago = $rowB[0]";
            $resultVal = $mysqli->query($sqlVal);
            $val = mysqli_fetch_row($resultVal);
            echo "<option value=".$rowB[0].">".$rowB[1].' '.mb_strtoupper($rowB[2]).' '.$rowB[5].' '.ucwords(mb_strtolower($rowB[3])).' - '.$rowB[4].' '."$".number_format($val[0],2,',','.')."</option>";
        }
    break;
    case 28:
        $parametroAnno = $_SESSION['anno'];
        $tipo = $_REQUEST['tipo'];
        echo "<option value>Mes</option>";
        $ms ="SELECT id_unico, numero, mes FROM gf_mes WHERE parametrizacionanno = $parametroAnno"; 
        $resultB = $mysqli->query($ms);
        if(mysqli_num_rows($resultB)>0){
            while ($rowB = mysqli_fetch_row($resultB)) {
                echo '<option value="'.$rowB[1].'">'.$rowB[2].'</option>';
            }
        } else {
            echo "<option value>No Se Encontraron Meses</option>";
        }
    break;
    #********* Buscar Comprobantes CNT Por Tipo SOLO CLINICA************#
    case 29:
        # Consulta para datos de busqueda
        $tipo = $_REQUEST['tipo'];
        $mes  = $_REQUEST['mes'];
        $parametroAnno = $_SESSION['anno'];
        ###########################################################################################################################
        $sqlCP = "SELECT    cnt.id_unico,
                            cnt.numero,
                            tpc.sigla,
                            IF(CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos) IS NULL 
                            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                            (ter.razonsocial),
                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos  )) AS NOMBRE,
                            ter.numeroidentificacion, cnt.fecha, ter.digitoverficacion   
                FROM        gf_comprobante_cnt cnt
                LEFT JOIN   gf_tipo_comprobante tpc     ON cnt.tipocomprobante      = tpc.id_unico
                LEFT JOIN   gf_tercero ter              ON cnt.tercero              = ter.id_unico
                LEFT JOIN   gf_tipo_identificacion ti   ON ter.tipoidentificacion   = ti.id_unico
                WHERE       tpc.id_unico=$tipo AND cnt.parametrizacionanno = $parametroAnno 
                AND MONTH(cnt.fecha)='$mes' 
                ORDER BY    cnt.numero DESC";
        $resultCP = $mysqli->query($sqlCP);
        ##########################################################################################################################
        # Consulta para datos de busqueda                                        
        ###########################################################################################################################
        IF(mysqli_num_rows($resultCP)>0){
            echo "<option value=''></option>";
            while ($rowCP = mysqli_fetch_row($resultCP)) {
                $date= new DateTime($rowCP[5]);
                $f= $date->format('d/m/Y');
                ######################################################################################################################
                # Consulta de valor de comprobante
                #
                ######################################################################################################################
                $sqlVA = "SELECT SUM(IF (dtc.valor<0, dtc.valor*-1, dtc.valor) )
                        FROM      gf_detalle_comprobante dtc 
                        LEFT JOIN gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico 
                        LEFT JOIN gf_cuenta c ON dtc.cuenta = c.id_unico 
                        WHERE     cnt.id_unico = $rowCP[0] AND (c.naturaleza = 1 
                        AND dtc.valor>0 OR c.naturaleza =2 AND dtc.valor<0);";
                $resultVA = $mysqli->query($sqlVA);
                $valorVA = mysqli_fetch_row($resultVA);
                ######################################################################################################################
                # Impresión de valores
                #
                ######################################################################################################################
                if(empty($rowCP[6])) {
                    echo "<option value=".$rowCP[0].">".$rowCP[1]." ".$rowCP[2]." ".$f." ".ucwords(mb_strtolower($rowCP[3])).' '.$rowCP[4]." $".number_format($valorVA[0],2,',','.')."</option>";
                } else {
                    echo "<option value=".$rowCP[0].">".$rowCP[1]." ".$rowCP[2]." ".$f." ".ucwords(mb_strtolower($rowCP[3])).' '.$rowCP[4].'-'.$rowCP[6]." $".number_format($valorVA[0],2,',','.')."</option>";
                }
            }
        } else {
            echo "<option value=''>No Hay Comprobantes</option>";
        }
    break;
    #****** Gerencia Urbana********#
    case 30:
        $sucursal = $_REQUEST['sucursal'];
        $ms ="SELECT DISTINCT comparendo FROM gu_resoluciones WHERE sucursal =$sucursal"; 
        $resultB = $mysqli->query($ms);
        if(mysqli_num_rows($resultB)>0){
            while ($rowB = mysqli_fetch_row($resultB)) {
                echo '<option value="'.$rowB[0].'">'.$rowB[0].'</option>';
            }
        } else {
            echo "<option value>No Se Encontraron Comparendos</option>";
        }
    break;
    case 31:
        $sucursal = $_REQUEST['sucursal'];
        $meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $ms ="SELECT DISTINCT(DATE_FORMAT(fecha_comparendo,'%m-%Y')) 
            FROM gu_resoluciones WHERE sucursal =$sucursal 
            ORDER BY YEAR(fecha_comparendo),MONTH(fecha_res) 
            "; 
        $resultB = $mysqli->query($ms);
        if(mysqli_num_rows($resultB)>0){
            while ($rowB = mysqli_fetch_row($resultB)) {
                $fecha_div = explode("-", $rowB[0]);
                $mesS = (int) $fecha_div[0];
                $anoS = $fecha_div[1];
                echo '<option value="'.$rowB[0].'">'.$meses[$mesS].' - '.$anoS.'</option>';
            }
        } else {
            echo "<option value=''>No Se Encontraron Meses</option>";
        }
    break;
    case 32:
        $sucursal = $_REQUEST['sucursal'];
        $meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $ms ="SELECT DISTINCT(DATE_FORMAT(fecha_comparendo,'%m-%Y')) 
            FROM gu_resoluciones WHERE sucursal =$sucursal 
            ORDER BY YEAR(fecha_comparendo),MONTH(fecha_res) 
            "; 
        $resultB = $mysqli->query($ms);
        if(mysqli_num_rows($resultB)>0){
            while ($rowB = mysqli_fetch_row($resultB)) {
                $fecha_div = explode("-", $rowB[0]);
                $mesS = (int) $fecha_div[0];
                $anoS = $fecha_div[1];
                echo '<option value="'.$rowB[0].'">'.$meses[$mesS].' - '.$anoS.'</option>';
            }
        } else {
            echo "<option value=''>No Se Encontraron Meses</option>";
        }
    break;
    #***** Afectado Tipo Comprobante Pptal*****#
    case 33:
        $clase = $_REQUEST['clase'];
        $row    =$con->Listar("SELECT id_unico, codigo, nombre 
                    FROM gf_tipo_comprobante_pptal 
                    WHERE clasepptal = 0");
        switch ($clase){
            #Registo A Disponibilidad 
            case 15:
                $row = $con->Listar("SELECT id_unico, codigo, nombre 
                    FROM gf_tipo_comprobante_pptal 
                    WHERE clasepptal = 14 
                    AND compania = $compania 
                    AND tipooperacion = 1 
                    AND vigencia_actual = 1");
            break;
            #Aprobacion A registro
            case 20:
                $row = $con->Listar("SELECT id_unico, codigo, nombre 
                    FROM gf_tipo_comprobante_pptal 
                    WHERE clasepptal = 15 
                    AND compania = $compania 
                    AND tipooperacion = 1 
                    AND vigencia_actual = 1");
            break;
            # Cuenta Por Pagar A Aprobacion
            case 16:
                $row = $con->Listar("SELECT id_unico, codigo, nombre 
                    FROM gf_tipo_comprobante_pptal 
                    WHERE clasepptal = 20  
                    AND compania = $compania 
                    AND tipooperacion = 1 
                    AND vigencia_actual = 1");
            break;
            #Egreso a Cuenta Por Pagar
            case 17:
                $row = $con->Listar("SELECT id_unico, codigo, nombre 
                    FROM gf_tipo_comprobante_pptal 
                    WHERE clasepptal = 16 
                    AND compania = $compania 
                    AND tipooperacion = 1 
                    AND vigencia_actual = 1");
            break;
        }
        if(count($row)>0){
            for ($i = 0;$i < count($row);$i++) {
                echo '<option value="'.$row[$i][0].'">'.mb_strtoupper($row[$i][1]).' - '.ucwords(mb_strtolower($row[$i][2])).'</option>';
            }
        } else {
            echo "<option value=''>No Se Encontraron Tipos Comprobante</option>";
        }
    break;
    case 34:
        
    break;
    case (35):
    ############TRAER REGISTROS PARA LA MODIFICACION A REGISTRO#############
    $tipo      = $_POST['tipo'];
    $tercero   = $_POST['tercero'];
    $operacion = "SELECT tipooperacion FROM gf_tipo_comprobante_pptal WHERE id_unico = $tipo ";
    $operacion = $mysqli->query($operacion);
    $operacion = mysqli_fetch_row($operacion);
    $operacion = intval($operacion[0]);
    ###ADICIONA###
    if($operacion ==2){
        $diponibilidades = "SELECT
            com.id_unico,
            com.numero,
            DATE_FORMAT(com.fecha, '%d/%m/%Y'),
            com.descripcion,
            tip.codigo,
            (SELECT
                SUM(dcp.valor)
            FROM
                gf_detalle_comprobante_pptal dcp
            WHERE
                dcp.comprobantepptal = com.id_unico
                ) AS valor 
        FROM
            gf_comprobante_pptal com
        LEFT JOIN gf_tipo_comprobante_pptal tip ON
            tip.id_unico = com.tipocomprobante
        WHERE
            tip.clasepptal = 20 AND tip.tipooperacion = 1 
            AND com.parametrizacionanno = $anno 
            AND com.tercero = $tercero 
        ORDER BY
            com.numero,
            com.fecha ASC";
        $diponibilidades =$mysqli->query($diponibilidades);
        while ($row = mysqli_fetch_row($diponibilidades)) {
            $valorDisp =0;
            $valorDis ="SELECT DISTINCT dcp.id_unico, dcp.comprobanteafectado ,dcp.valor, dca.valor 
                        FROM gf_detalle_comprobante_pptal dcp 
                        LEFT JOIN gf_detalle_comprobante_pptal dca 
                        ON dcp.comprobanteafectado = dca.id_unico
                        WHERE dcp.comprobantepptal = $row[0]";
            $valorDis = $mysqli->query($valorDis);
            $afectaciones =0;
            $valorRep = 0;
            $valorD = 0;
            while($rowDetComp = mysqli_fetch_row($valorDis))
            {
                $valorRep = $rowDetComp[2];
                $valorD = $rowDetComp[3];
                ########AFECTACIONES A REGISTRO#########
                $afec = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc "
                        . "LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico "
                        . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                        . "WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $rowDetComp[0]";
                $afec = $mysqli->query($afec);
                while ($row2 = mysqli_fetch_row($afec)) {
                    if($row2[0]==2){
                        $valorRep = $valorRep+$row2[1];
                    } else {
                        $valorRep = $valorRep-$row2[1];
                    }
                } 
                ########AFECTACIONES A DISPONIBILIDAD#########
                $afec = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc "
                        . "LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico "
                        . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                        . "WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $rowDetComp[1]";
                $afec = $mysqli->query($afec);
                while ($row2 = mysqli_fetch_row($afec)) {
                    if($row2[0]==2){
                        $valorD = $valorD+$row2[1];
                    } else {
                        $valorD = $valorD-$row2[1];
                    }
                }
                $saldoDis = $valorD-$valorRep;
                $valorDisp +=$saldoDis;
                
            }
            if($valorDisp>0){
                $tipo = mb_strtoupper($row[4]);
                $valor = '$'.number_format($row[5], 2, '.', ',');
                echo "<option value='$row[0]'>$row[1] $tipo $row[2] $row[3] $valor</option>"; 
            }
        }
    ###REDUCE###    
    } elseif($operacion ==3){
        $querySolAprob = "SELECT
            com.id_unico,
            com.numero,
            DATE_FORMAT(com.fecha, '%d/%m/%Y'),
            com.descripcion,
            tip.codigo, 
            (SELECT
                SUM(dcp.valor)
            FROM
                gf_detalle_comprobante_pptal dcp
            WHERE
                dcp.comprobantepptal = com.id_unico
                ) AS valor
        FROM
            gf_comprobante_pptal com
        LEFT JOIN gf_tipo_comprobante_pptal tip ON
            tip.id_unico = com.tipocomprobante
        WHERE
            tip.clasepptal = 20 AND tip.tipooperacion = 1 
            AND com.parametrizacionanno = $anno 
             AND com.tercero = $tercero 
        ORDER BY
            com.numero,
            com.fecha ASC";
        $SolAprob = $mysqli->query($querySolAprob);
        while($row = mysqli_fetch_row($SolAprob))
        {
            $queryDetCompro = "SELECT
                detComp.id_unico,
                detComp.valor
            FROM
                gf_detalle_comprobante_pptal detComp
            WHERE
                detComp.comprobantepptal = ".$row[0];
            $saldDispo = 0;
            $totalSaldDispo = 0;
            $totalSal = 0;
            $afect=0;
            $detCompro = $mysqli->query($queryDetCompro);
            while($rowDetComp = mysqli_fetch_row($detCompro))
            {
                $valorD = $rowDetComp[1];
                $afect = "SELECT dc.valor, tc.tipooperacion 
                        FROM gf_detalle_comprobante_pptal dc 
                        LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico 
                        LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                        WHERE dc.comprobanteafectado = '$rowDetComp[0]'";
                $afect = $mysqli->query($afect);
                while ($row3 = mysqli_fetch_row($afect)) {
                    if(($row3[1] == 2) || ($row3[1] == 4) )
                    {
                           $valorD += $row3[0];
                    }
                    elseif($row3[1] == 3 || ($row3[1] == 1))
                    {
                           $valorD -= $row3[0];
                    } 
                    
                    
                }
                $totalSal = $valorD;
                $totalSaldDispo +=$totalSal;
            }
            $saldo = $totalSaldDispo;

              if($saldo > 0)
              { 
                $tipo = mb_strtoupper($row[4]);
                $valor = '$'.number_format($row[5], 2, '.', ',');
                echo "<option value='$row[0]'>$row[1] $tipo $row[2] $row[3] $valor</option>"; 
              }
        }
    } else {

    }
    break;
    
    case 36: 
        $id = $_REQUEST['id'];
        $en = $_REQUEST['encabezado'];
        $sql_cons ="UPDATE `gf_plantilla` 
            SET `encabezado`=:encabezado 
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
                array(":encabezado",$en),
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            echo 1;
        } else {
            echo 2;
        }
    break;
            #* Buscar Factura Cotizacion 
    case 37:
        $html = "";
        if(!empty($_REQUEST['tipo'])){
            $tipoF    = $_REQUEST['tipo'];
            $html .= '<option value="">Nro Cotización</option>';
            $sql   = "SELECT    f.id_unico, f.numero_factura, DATE_FORMAT(f.fecha_factura,'%d/%m/%Y'), tf.prefijo 
                       FROM gp_factura f
                      LEFT JOIN gp_tipo_factura tf ON tf.id_unico = f.tipofactura 
                      WHERE   (f.tipofactura = $tipoF)  ";
            $res = $mysqli->query($sql);
            $dta = $res->fetch_all(MYSQLI_NUM);
            foreach ($dta as $row) {
                list($totalX, $totalV, $xxx) = array(0, 0, 0);
                $sql_ = "SELECT id_unico, (valor + iva) * cantidad ,detalleafectado FROM gp_detalle_factura WHERE factura = $row[0]";
                $res_ = $mysqli->query($sql_);
                $dta_ = $res_->fetch_all(MYSQLI_NUM);

                foreach ($dta_ as $row_) {

                    $totalX += $row_[1];
                    $totalX =round($totalX);
                    $sq_ = "SELECT (valor + iva) * cantidad FROM gp_detalle_factura WHERE id_unico = $row_[2]";
                    $re_ = $mysqli->query($sq_);
                    if(mysqli_num_rows($re_)>0){
                        $dt_ = $re_->fetch_all();
                        foreach ($dt_ as $row_) {
                            $totalV += $row_[0];
                            $totalV =round($totalV);
                        }
                    }else{
                        $totalV+=0;
                        $totalV =round($totalV);
                    }
    
                }
                $xxx = $totalX - $totalV;
                if($xxx > 0){
                    $html .= "<option value=\"$row[0]\">$row[3] $row[1] $row[2] $".number_format($xxx, 2, ',', '.')."</option>";
                }
            }
        }
        echo $html;
    break;
    case 38:
        if(!empty($_REQUEST['id'])) {
            $id = $_REQUEST['id'];
            echo "registrar_GF_FACTURAPV.php?t=1&cotizacion=".md5($id);
        }
    break;

    case 39:
        $id = $_REQUEST['id'];
        $sq_ = "SELECT base_ingresos FROM gf_clase_retencion WHERE id_unico = $id";
        $re_ = $mysqli->query($sq_);
        if(mysqli_num_rows($re_)>0){
            $sqlI = mysqli_fetch_row($re_);
            $base_I =$sqlI[0];
        }else{
            $base_I=NULL;
        }
        if ($base_I==null || $base_I=="") {
            $base_I=2;
        }
        echo $base_I;
    break;
}
?>