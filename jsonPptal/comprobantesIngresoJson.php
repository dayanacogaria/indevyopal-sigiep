<?php 
##########################################################################################
# *********************************** Modificaciones *********************************** # 
##########################################################################################
#10/04/2018 |Erica G. | Acción Para Eliminar Comprobante Ingreso
#30/06/2017 |ERICA G. |ARCHIVO CREADO
#######################################################################################
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
session_start();
$action     = $_POST['action'];
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$con        = new ConexionPDO();
switch ($action){
    ############ELIMINAR DETALLE #############
    case (1):
        $id         =$_POST['id'];
        $del ="DELETE FROM gf_detalle_comprobante WHERE id_unico = $id";
        $del = $mysqli->query($del);
        if($del==true){
            $result=1;
        } else {
            $result=2;
        }
        echo json_decode($result);
    break;
    ############CARGAR VALOR MODIFICAR DETALLE#############
    case (2):
        $id         =$_POST['id'];
         $del ="SELECT dc.valor, c.naturaleza FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        $valor = $cta[0];
        if($cta[1]==1){
            if($valor>0){
               $res = $valor;
            } else {
                $res = $valor;
            }
        }else {
            if($valor>0){
               $res = $valor*-1;
            } else {
                $res = $valor*-1;
            }
        }
        echo $res;
    break;
    ############MODIFICAR VALOR DETALLE#############
    case (3):
        $id=$_POST['id'];
        $debito=$_POST['debito'];
        $credito=$_POST['credito'];
        $valor = 0;
        $sql = "SELECT c.naturaleza 
                 FROM gf_detalle_comprobante dc LEFT JOIN gf_cuenta c 
                 ON dc.cuenta = c.id_unico WHERE dc.id_unico = $id";
        $rs = $mysqli->query($sql);
        $nat = mysqli_fetch_row($rs);
        $natural = $nat[0];
        #naturaleza 1 Debito, 2 credito
        if (empty($_POST['debito']) || $_POST['debito']=='0') {
            if ($_POST['credito'] != '""' || $_POST['credito'] != '0') {
                if ($natural == 1) {
                    $valor =$mysqli->real_escape_string($_POST['credito']*-1); 

                }else{

                    $valor =$mysqli->real_escape_string($_POST['credito']); 

                }

            }
        }
        if (empty($_POST['credito']) || $_POST['credito']=='0') {
            if($_POST['debito'] != '""' || $_POST['credito'] != '0'){
                if ($natural==2) {
                    $valor =  $mysqli->real_escape_string($_POST['debito']*-1);           
                }else{
                   $valor = $mysqli->real_escape_string($_POST['debito']);
                }        
            }
        }
        $sql = "UPDATE gf_detalle_comprobante SET valor=$valor 
                 WHERE id_unico=$id ";
        $rs = $mysqli->query($sql);
        echo json_encode($rs); 
    break;
    ############CARGAR CUENTA MODIFICAR DETALLE#############
    case (4):
        $id         =$_POST['id'];
         $del ="SELECT dc.cuenta, c.codi_cuenta, c.nombre  "
                . "FROM gf_detalle_comprobante dc "
                . "LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico "
                . "WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        echo '<option value ='.$cta[0].'>'.$cta[1].' - '.ucwords(mb_strtolower($cta[2])).'</option>' ;
        $c ="SELECT id_unico, codi_cuenta, nombre FROM gf_cuenta WHERE id_unico !=$cta[0] AND parametrizacionanno = $anno ORDER BY codi_cuenta ASC";
        $c = $mysqli->query($c);
        while ($row = mysqli_fetch_row($c)) {
            echo '<option value ='.$row[0].'>'.$row[1].' - '.ucwords(mb_strtolower($row[2])).'</option>' ;
        }
    break;
    ############CARGAR TERCERO MODIFICAR DETALLE#############
    case (5):
        $id         =$_POST['id'];
         $del ="SELECT dc.tercero, IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.digitoverficacion 
                    FROM gf_detalle_comprobante dc 
                    LEFT JOIN gf_tercero tr ON dc.tercero = tr.id_unico 
                  WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        if(empty($cta[3])){ 
            $ni = $cta[2];
        } else {
            $ni = $cta[2].'-'.$cta[3];
        }
        echo '<option value ='.$cta[0].'>'.ucwords(mb_strtolower($cta[1])).' - '.$ni.'</option>' ;
        $c ="SELECT tr.id_unico, IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.digitoverficacion 
                FROM gf_tercero tr WHERE tr.id_unico !=$cta[0] AND tr.compania = $compania 
                ORDER BY NOMBRE ASC";
        $c = $mysqli->query($c);
        while ($row = mysqli_fetch_row($c)) {
            if(empty($row[3])){ 
                $ni = $row[2];
            } else {
                $ni = $row[2].'-'.$row[3];
            }
            echo '<option value ='.$row[0].'>'.ucwords(mb_strtolower($row[1])).' - '.$ni.'</option>' ;
        }
    break;
    ############CARGAR CENTRO COSTO MODIFICAR DETALLE#############
    case (6):
        $id         =$_POST['id'];
         $del ="SELECT dc.centrocosto, c.nombre  "
                . "FROM gf_detalle_comprobante dc "
                . "LEFT JOIN gf_centro_costo c ON dc.centrocosto = c.id_unico "
                . "WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        echo '<option value ='.$cta[0].'>'.ucwords(mb_strtolower($cta[1])).'</option>' ;
        $c ="SELECT id_unico, nombre FROM gf_centro_costo WHERE id_unico !=$cta[0] AND parametrizacionanno = $anno ORDER BY nombre ASC";
        $c = $mysqli->query($c);
        while ($row = mysqli_fetch_row($c)) {
            echo '<option value ='.$row[0].'>'.ucwords(mb_strtolower($row[1])).'</option>' ;
        }
    break;
    ############CARGAR PROYECTO MODIFICAR DETALLE#############
    case (7):
        $id         =$_POST['id'];
         $del ="SELECT dc.proyecto, c.nombre  "
                . "FROM gf_detalle_comprobante dc "
                . "LEFT JOIN gf_proyecto c ON dc.proyecto = c.id_unico "
                . "WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        echo '<option value ='.$cta[0].'>'.ucwords(mb_strtolower($cta[1])).'</option>' ;
        $c ="SELECT id_unico, nombre FROM gf_proyecto WHERE id_unico !=$cta[0] ORDER BY nombre ASC";
        $c = $mysqli->query($c);
        while ($row = mysqli_fetch_row($c)) {
            echo '<option value ='.$row[0].'>'.ucwords(mb_strtolower($row[1])).'</option>' ;
        }
    break;
    ############CARGAR VALOR MODIFICAR DETALLE#############
    case (8):
        $id         =$_POST['id'];
         $del ="SELECT dc.valor, c.naturaleza FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.id_unico = $id";
        $del = $mysqli->query($del);
        $cta = mysqli_fetch_row($del);
        $valor = $cta[0];
        if($cta[1]==1){
            if($valor>0){
               $res = $valor;
            } else {
                $res = $valor;
            }
        }else {
            if($valor>0){
               $res = $valor*-1;
            } else {
                $res = $valor*-1;
            }
        }
        echo $res;
    break;
    ############AUXILIAR DE TERCERO MODIFICAR DETALLE#############
    case (9):
        $id         =$_POST['id'];
        $sqli = "select distinct c.auxiliartercero 
                from gf_detalle_comprobante dc LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ############CENTRO COSTO MODIFICAR DETALLE#############
    case (10):
        $id         =$_POST['id'];
        $sqli = "select distinct c.centrocosto 
                from gf_detalle_comprobante dc LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ############PROYECTO MODIFICAR DETALLE#############
    case (11):
        $id         =$_POST['id'];
        $sqli = "select distinct c.auxiliarproyecto 
                from gf_detalle_comprobante dc LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                WHERE dc.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ############AUXILIAR DE TERCERO MODIFICAR DETALLE#############
    case (12):
        $id         =$_POST['id'];
        $sqli = "select distinct c.auxiliartercero 
                from  gf_cuenta c 
                WHERE c.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ############CENTRO COSTO MODIFICAR DETALLE#############
    case (13):
        $id         =$_POST['id'];
        $sqli = "select distinct c.centrocosto 
                from  gf_cuenta c 
                WHERE c.id_unico = $id AND parametrizacionanno = $anno";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ############PROYECTO MODIFICAR DETALLE#############
    case (14):
        $id         =$_POST['id'];
        $sqli = "select distinct c.auxiliarproyecto 
                from gf_cuenta c 
                WHERE c.id_unico = $id";
        $rs = $mysqli->query($sqli);
        $row = mysqli_fetch_row($rs);
        echo $row[0];
    break;
    ###########################MODIFICAR DETALLE############
    case (15):
        $id=$_POST['id'];
        $cuenta=$_POST['cuenta'];
        $tercero=$_POST['tercero'];
        $centroC=$_POST['centrocosto'];
        $protec=$_POST['proyecto'];
        $debito=$_POST['debito'];
        $credito=$_POST['credito'];
        $valor = 0;
        $sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $cuenta";
        $rs = $mysqli->query($sql);
        $nat = mysqli_fetch_row($rs);
        $natural = $nat[0];
        #naturaleza 1 Debito, 2 credito
        if (empty($_POST['debito']) || $_POST['debito']=='0') {
            if ($_POST['credito'] != '""' || $_POST['credito'] != '0') {
                if ($natural == 1) {
                    $valor =$mysqli->real_escape_string($_POST['credito']*-1); 

                }else{

                    $valor =$mysqli->real_escape_string($_POST['credito']); 

                }

            }
        }
        if (empty($_POST['credito']) || $_POST['credito']=='0') {
            if($_POST['debito'] != '""' || $_POST['credito'] != '0'){
                if ($natural==2) {
                    $valor =  $mysqli->real_escape_string($_POST['debito']*-1);           
                }else{
                   $valor = $mysqli->real_escape_string($_POST['debito']);
                }        
            }
        }
        $sql = "UPDATE gf_detalle_comprobante SET valor=$valor,cuenta=$cuenta,tercero=$tercero,proyecto=$protec,"
                 . "centrocosto=$centroC WHERE id_unico=$id";
        $rs = $mysqli->query($sql);
        echo json_encode($rs); 
    break;
    ############CARGAR CONCEPTO MODIFICAR##########################
    case(16):
        $id=$_POST['id'];
        $dp = "SELECT detallecomprobantepptal FROM gf_detalle_comprobante WHERE id_unico = $id";
        $dp = $mysqli->query($dp);
        if(mysqli_num_rows($dp)>0){
            $idp = mysqli_fetch_row($dp);
            $idp = $idp[0];
            $rf= "SELECT c.id_unico, c.nombre 
                FROM gf_detalle_comprobante_pptal dc 
                LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = dc.conceptoRubro 
                LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                WHERE dc.id_unico =$idp ";
            $rf = $mysqli->query($rf);
            if(mysqli_num_rows($rf)>0){
                $row = mysqli_fetch_row($rf);
                echo '<option value ='.$row[0].'>'.ucwords(mb_strtolower($row[1])).'</option>' ;
            } else {
                echo "<option value =''>Concepto</option>" ;
            }
        } else {
            echo "<option value =''>Concepto</option>" ; 
        }
        ##CONCEPTOS##
        $cn = "SELECT id_unico, LOWER(nombre) FROM gf_concepto WHERE  parametrizacionanno = $anno ORDER BY nombre ASC";
        $cn = $mysqli->query($cn);
        if(mysqli_num_rows($cn)>0){
            while ($row1 = mysqli_fetch_row($cn)) {
               echo '<option value ='.$row1[0].'>'.ucwords(($row1[1])).'</option>' ; 
                
            }
        } else {
            echo "<option value =''>No hay conceptos</option>" ; 
        }
    break;
    ############CARGAR RUBRO MODIFICAR##########################
    case(17):
        $id=$_POST['id'];
        $dp = "SELECT detallecomprobantepptal FROM gf_detalle_comprobante WHERE id_unico = $id";
        $dp = $mysqli->query($dp);
        if(mysqli_num_rows($dp)>0){
            $idp = mysqli_fetch_row($dp);
            $idp = $idp[0];
            $rf= "SELECT rf.id_unico, r.codi_presupuesto, LOWER(r.nombre), LOWER(f.nombre) 
                FROM gf_detalle_comprobante_pptal dc 
                LEFT JOIN gf_rubro_fuente rf ON rf.id_unico  = dc.rubrofuente  
                LEFT JOIN gf_rubro_pptal r ON rf.rubro = r.id_unico 
                LEFT JOIN gf_fuente f ON f.id_unico = rf.fuente 
                WHERE dc.id_unico =$idp ";
            $rf = $mysqli->query($rf);
            if(mysqli_num_rows($rf)>0){
                $row = mysqli_fetch_row($rf);
                echo '<option value ='.$row[0].'>'.$row[1].' '.ucwords($row[2]).'-'. ucwords($row[3]).'</option>' ;
            } else {
                echo "<option value =''>Rubro Fuente</option>" ;
            }
        } else {
            echo "<option value =''>Rubro Fuente</option>" ; 
        }
        ##RUBRO FUENTE##
        $cn = "SELECT rf.id_unico, r.codi_presupuesto, LOWER(r.nombre), LOWER(f.nombre)  
                FROM gf_rubro_fuente rf 
                LEFT JOIN gf_rubro_pptal r ON r.id_unico = rf.rubro 
                LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico ORDER BY r.codi_presupuesto ASC";
        $cn = $mysqli->query($cn);
        if(mysqli_num_rows($cn)>0){
            while ($row1 = mysqli_fetch_row($cn)) {
               echo '<option value ='.$row1[0].'>'.$row1[1].' '.ucwords($row1[2]).'-'. ucwords($row1[3]).'</option>' ;
                
            }
        } else {
            echo "<option value =''>No hay Rubros</option>" ; 
        }
    break;
    ############CARGAR RUBRO CAMBIO CONCEPTO###########
    case(18):
        $concepto = $_POST['concepto'];
        if($concepto == 0 || $concepto=='""'){
            echo '<option value="">Rubro Fuente</option>';
        }else{
            $sql = "SELECT DISTINCT rb.id_unico,rb.codi_presupuesto,rb.nombre,ft.nombre,rft.id_unico
                    FROM gf_concepto_rubro cr 
                    LEFT JOIN gf_rubro_fuente rft ON cr.rubro = rft.rubro
                    LEFT JOIN gf_rubro_pptal rb ON cr.rubro = rb.id_unico
                    LEFT JOIN  gf_fuente ft ON rft.fuente = ft.id_unico
                    WHERE cr.concepto = $concepto AND rb.id_unico IS NOT NULL";
            $result = $mysqli->query($sql);    
            if(mysqli_num_rows($result)>0){
                while ($row = mysqli_fetch_row($result)){
                    echo '<option value="'.$row[4].'">'.(ucwords(mb_strtolower($row[1].' '.$row[2].'-'.$row[3]))).'</option>';
                }
            } else {
                echo '<option value="">Rubro Fuente</option>';
            }
        }
    break;
    ############CARGAR RUBRO CAMBIO CONCEPTO###########
    case(19):
        $rubro = $_POST['rubro'];
        $concepto = $_POST['concepto'];
        $es = "SELECT rubro FROM gf_rubro_fuente WHERE id_unico = $rubro";
        $vall = $mysqli->query($es);
        $valor=  mysqli_fetch_row($vall);
        $sql = "SELECT DISTINCT
                            cnt.id_unico cuenta,
                            cnt.codi_cuenta,
                            cnt.nombre
                FROM gf_concepto ct
                LEFT JOIN gf_concepto_rubro cnr ON cnr.concepto = ct.id_unico
                LEFT JOIN gf_rubro_fuente rbf ON rbf.rubro = cnr.rubro
                LEFT JOIN gf_rubro_pptal rb ON rbf.rubro = rb.id_unico
                LEFT JOIN gf_concepto_rubro_cuenta ctrb ON cnr.id_unico = ctrb.concepto_rubro
                LEFT JOIN gf_cuenta cnt ON ctrb.cuenta_debito = cnt.id_unico
                WHERE rb.id_unico = $valor[0]";
        $res = $mysqli->query($sql);
        while($row = mysqli_fetch_row($res)){
            echo '<option value="'.$row[0].'">'.ucwords(mb_strtolower($row[1].' '.$row[2])).'</option>';
        }
    break;
    ############MODIFICAR DETALLES###########
    case(20):
        $id=$_POST['id'];
        $cuenta=$_POST['cuenta'];
        $tercero=$_POST['tercero'];
        $centroC=$_POST['centrocosto'];
        $protec=$_POST['proyecto'];
        $debito=$_POST['debito'];
        $credito=$_POST['credito'];
        $valor = 0;
        $sql = "SELECT c.naturaleza FROM gf_detalle_comprobante dc LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico WHERE dc.id_unico = $id";
        $rs = $mysqli->query($sql);
        $nat = mysqli_fetch_row($rs);
        $naturaleza = $nat[0];
        #naturaleza 1 Debito, 2 credito
        if (empty($_POST['debito']) || $_POST['debito']=='0') {
            if(!empty($_POST['credito'])) {
                if($naturaleza == 1) {
                    $valor = $_POST['credito']*-1;
                }else{
                    $valor = $_POST['credito'];
                }
            }
        }

        if (empty($_POST['credito']) || $_POST['credito']=='0') {
            if(!empty($_POST['debito'])) {
                if($naturaleza == 2) {
                    $valor = $_POST['debito']*-1;
                }else{
                    $valor = $_POST['debito'];
                }
            }
        }
        //Valor para comprobante presupuestal
        $valorP = $valor;           //Asiganmos el valor obtenido a la variable valorP
        if($valorP < 0){            //Validamos que el valor si es menor que 0
            $valorP = $valorP *-1;  //Lo convierte a positivo
        }
        $sqlCP = "SELECT detComP.comprobantepptal FROM gf_comprobante_cnt comcnt 
        LEFT JOIN gf_detalle_comprobante detCom ON comcnt.id_unico = detCom.comprobante
        LEFT JOIN gf_detalle_comprobante_pptal detComP ON detComP.id_unico = detCom.detallecomprobantepptal
        WHERE detCom.comprobante = $id";
        $resultCP = $mysqli->query($sqlCP);
        $comPP = mysqli_fetch_row($resultCP);
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Actualizar detalle comprobante cnt
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sql1 = "UPDATE gf_detalle_comprobante SET valor=$valor,cuenta=$cuenta,tercero=$tercero,proyecto=$protec,centrocosto=$centroC WHERE id_unico=$id";
        $result1 = $mysqli->query($sql1);
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ///Actualizar detalle comprobante pptal
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sqlCP = "SELECT detallecomprobantepptal FROM gf_detalle_comprobante WHERE id_unico = $id";
        $res = $mysqli->query($sqlCP);
        $rw = mysqli_fetch_row($res);
        $compp = $rw[0];
        $rubrofuente = $_POST['rubroFuente'];
        ##CONCEPTO RUBRO 
        $concepto = $_POST['concepto'];
        ##BUSCAR RUBRO##
        $r = "SELECT rubro FROM gf_rubro_fuente WHERE id_unico = $rubrofuente";
        $r = $mysqli->query($r);
        $r = mysqli_fetch_row($r);
        $r = $r[0];
        ##BUSCAR CONCEPTO RUBRO 
        $cr = "SELECT id_unico FROM gf_concepto_rubro WHERE rubro = $r AND concepto = $concepto";
        $cr = $mysqli->query($cr);
        if(mysqli_num_rows($cr)>0){
            $cru = mysqli_fetch_row($cr);
            $conr = $cru[0];
            $sql = "UPDATE gf_detalle_comprobante_pptal SET "
                    . "valor=$valorP, "
                    . "rubrofuente = $rubrofuente , "
                    . "conceptoRubro = $conr "
                . "WHERE id_unico = $compp";
        } else { 
        $sql = "UPDATE gf_detalle_comprobante_pptal SET valor=$valorP, rubrofuente = $rubrofuente "
                . "WHERE id_unico = $compp";
        }
        $result = $mysqli->query($sql);
        echo json_encode($result);
    break;
    
    #*********** Eliminar Comprobantes **********#
    case (21):
        $e      =0;
        $msj    = "Comprobante No Se Puede Eliminar".'<br/>';
        $rta    = 0;
        $id_cnt = $_REQUEST['id'];
        #Detalles
        $d = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gf_detalle_comprobante WHERE comprobante =$id_cnt");
        $det = $d[0][0];
        #Comprobar Si Está Relacionado A Factura
        $fc = $con->Listar("SELECT DISTINCT f.numero_factura 
                FROM gp_detalle_factura df 
                LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                WHERE df.detallecomprobante IN($det)");
        if(count($fc)>0){
            $rta = 1;
            $msj .="Está Relacionado A Factura N° ".$fc[0][0];
        } else {
            #Comprobar Si Está Relacionado A Recaudo Factura
            $rc = $con->Listar("SELECT DISTINCT p.numero_pago 
                FROM gp_detalle_pago dp 
                LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                WHERE dp.detallecomprobante IN($det)");
            if(count($rc)>0){
                $rta = 1;
                $msj .="Está Relacionado A Recaudo De Facturación N° ".$rc[0][0];
            } else {
                #Comprobar Si Está Relacionado A Pago Predial
                $pp = $con->Listar("SELECT DISTINCT DATE_FORMAT(p.fechapago, '%d/%m/%Y') 
                    FROM gr_detalle_pago_predial dp 
                    LEFT JOIN gr_pago_predial p ON dp.pago = p.id_unico 
                    WHERE detallecomprobante IN($det)");
                if(count($pp)>0){
                    $rta = 1;
                    $msj .="Está Relacionado A Recaudo Predial Fecha:".$pp[0][0];
                } else {
                    #Comprobar Si Está Relacionado A pago Comercio
                    $pc = $con->Listar("SELECT DISTINCT rc.consecutivo 
                        FROM gc_detalle_recaudo dc 
                        LEFT JOIN gc_recaudo_comercial rc ON dc.recaudo = rc.id_unico 
                        WHERE dc.detalle_cnt IN($det)");
                    if(count($pc)>0){
                        $rta = 1;
                        $msj .="Está Relacionado A Recaudo Comercial N°:".$pc[0][0];
                    }
                }
            }
        }
        if($rta==0){    
            #** Buscar Comprobantes Homologados
            #*Causacion 
            $cs =$con->Listar("SELECT DISTINCT 
                    dca.comprobante FROM gf_detalle_comprobante dc 
                LEFT JOIN 
                    gf_detalle_comprobante dca ON dc.id_unico = dca.detalleafectado 
                WHERE 
                    dc.comprobante = $id_cnt AND dca.detalleafectado IS NOT NULL");
            if(count($cs)>0){
                $id_c = $cs[0][0];
                $dl = $con->Listar("DELETE FROM gf_detalle_comprobante WHERE comprobante = $id_c");
                if(empty($dl)){
                    $e +=1;
                }
            }
            #*pptal
            $cp = $con->Listar("SELECT DISTINCT 
                        dp.comprobantepptal FROM gf_detalle_comprobante dc 
                LEFT JOIN 
                        gf_detalle_comprobante_pptal dp ON dp.id_unico = dc.detallecomprobantepptal
                WHERE 
                        dc.comprobante = $id_cnt AND dc.detallecomprobantepptal IS NOT NULL;");
            if($cp>0){
                $id_p = $cp[0][0];
                $dl = $con->Listar("DELETE FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                if(empty($dl)){
                    $e +=1;
                }
                $dl = $con->Listar("DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_p");
                if(empty($dl)){
                    $e +=1;
                }
            } else {
                $dl = $con->Listar("DELETE FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                if(empty($dl)){
                    $e +=1;
                }
            }
            if($e>0){
                $rta = 0;
            } else {
                $rta = 1;
            }
        }
        $datos = array("msj"=>$msj, "rta"=>$rta);
        echo json_encode($datos); 
    break;
    #* Causación 
    case 22:
        if(!empty($_REQUEST['id_cnt']) && !empty($_REQUEST['id_pptal'])){
            $id_cnt     = $_REQUEST['id_cnt'];
            $row = $con->Listar("SELECT * FROM gf_comprobante_cnt 
            WHERE id_unico = $id_cnt");
        }elseif(!empty($_REQUEST['tipo'])){
            $fechaI = fechaC($_REQUEST['fechai']);
            $fechaF = fechaC($_REQUEST['fechaf']);
            $row = $con->Listar("SELECT * FROM gf_comprobante_cnt 
            WHERE tipocomprobante = ".$_REQUEST['tipo']." AND 
            fecha BETWEEN '$fechaI' AND '$fechaF'");
            
        } else {
            $row = '';
            echo false;
        }
        for ($c = 0; $c < count($row);$c++) {
            $id_cnt = $row[$c][0];
            $rowh = $con->Listar("SELECT    tp.tipo_comp_hom, 
                    cnt.numero, cnt.fecha, cnt.tercero, cnt.descripcion, cnt.estado 
                FROM      gf_comprobante_cnt cnt
                LEFT JOIN gf_tipo_comprobante tp ON cnt.tipocomprobante = tp.id_unico
                WHERE     cnt.id_unico = $id_cnt");
            if(!empty($rowh[0][0])){
                $rowE = $con->Listar("SELECT  id_unico FROM gf_comprobante_cnt
                        WHERE   tipocomprobante = ".$rowh[0][0]."
                        AND     numero = ".$rowh[0][1]);
                if(empty($rowE[0][0])){
                    $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                            ( `numero`, `fecha`, 
                            `descripcion`, 
                            `parametrizacionanno`,`tipocomprobante`,
                            `tercero`,
                            `compania`,`estado`) 
                    VALUES (:numero, :fecha, 
                            :descripcion,
                            :parametrizacionanno,:tipocomprobante,
                            :tercero, 
                            :compania, :estado )";
                    $sql_dato = array(
                            array(":numero",$rowh[0][1]),
                            array(":fecha",$rowh[0][2]),
                            array(":descripcion",$rowh[0][4]),
                            array(":parametrizacionanno",$anno),
                            array(":tipocomprobante",$rowh[0][0]),
                            array(":tercero",$rowh[0][3]),
                            array(":compania",$compania),
                            array(":estado",2),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato); 
                    $rowE = $con->Listar("SELECT  id_unico FROM gf_comprobante_cnt
                        WHERE   tipocomprobante = ".$rowh[0][0]."
                        AND     numero = ".$rowh[0][1]);
                    $id_causacion   =    $rowE[0][0];
                } else {
                    $id_causacion   =    $rowE[0][0];
                    eliminardetallescnt($id_causacion);
                    $sql_cons ="UPDATE `gf_comprobante_cnt` 
                            SET `fecha`=:fecha , 
                            `tercero`=:tercero , 
                            `descripcion`=:descripcion 
                            WHERE `id_unico`=:id_unico";
                    $sql_dato = array(
                            array(":fecha",$rowh[0][2]),
                            array(":tercero",$rowh[0][3]),
                            array(":descripcion",$rowh[0][4]),
                            array(":id_unico",$id_causacion),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                }
                #*** GUARDAR DETALLES
                #* Buscar Concepto Rubro Cuenta 
                $rowd  = $con->Listar("SELECT DISTINCT 
                    dtc.id_unico, dc.valor, 
                    cd.id_unico, cd.naturaleza, 
                    cc.id_unico, cc.naturaleza, 
                    dtc.tercero , dtc.centrocosto 
                    FROM gf_detalle_comprobante dtc
                    LEFT JOIN gf_detalle_comprobante_pptal dc ON dtc.detallecomprobantepptal = dc.id_unico 
                    LEFT JOIN gf_concepto_rubro_cuenta crc ON crc.concepto_rubro = dc.conceptoRubro AND crc.cuenta_debito = dtc.cuenta
                    LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico 
                    LEFT JOIN gf_cuenta cc ON crc.cuenta_credito = cc.id_unico 
                    WHERE dc.conceptoRubro IS NOT NULL 
                    AND crc.cuenta_debito != crc.cuenta_credito 
                    AND dtc.comprobante = $id_cnt");
                for ($d = 0; $d < count($rowd); $d++) {
                    $valor      = $rowd[$d][1];
                    $cuenta_d   = $rowd[$d][2];
                    $naturl_d   = $rowd[$d][3];
                    $cuenta_c   = $rowd[$d][4];
                    $naturl_c   = $rowd[$d][5];
                    
                    #** Cuenta Débito 
                    if($naturl_d == 2){
                        $valord = $valor*-1;
                    } else {
                        $valord = $valor;
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, 
                            `centrocosto`,`detalleafectado`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, 
                            :centrocosto,:detalleafectado)";
                    $sql_dato = array(
                            array(":fecha",$rowh[0][2]),
                            array(":comprobante",$id_causacion),
                            array(":valor",($valord)),
                            array(":cuenta",$cuenta_d),   
                            array(":naturaleza",$naturl_d),
                            array(":tercero",$rowd[$d][6]),
                            array(":centrocosto",$rowd[$d][7]),
                            array(":detalleafectado",$rowd[$d][0]),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    #** Cuenta Crédito 
                    if($naturl_c == 1){
                        $valorc = $valor*-1;
                    } else {
                        $valorc = $valor;
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, 
                            `centrocosto`,`detalleafectado`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, 
                            :centrocosto,:detalleafectado)";
                    $sql_dato = array(
                            array(":fecha",$rowh[0][2]),
                            array(":comprobante",$id_causacion),
                            array(":valor",($valorc)),
                            array(":cuenta",$cuenta_c),   
                            array(":naturaleza",$naturl_c),
                            array(":tercero",$rowd[$d][6]),
                            array(":centrocosto",$rowd[$d][7]),
                            array(":detalleafectado",$rowd[$d][0]),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                }                
                
            }
        }
        echo true;
    break;
}

