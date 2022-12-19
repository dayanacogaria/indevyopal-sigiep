<?php
require '../Conexion/ConexionPDO.php';                                                     
require '../Conexion/conexion.php';                                                     
require './funcionesPptal.php';
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_POST['action'];
switch ($action) {
    #Validaciones Cierre Cuentas X Pagar
    case 1:
        $annov  = $_REQUEST['anno'];
        $nannov = anno($annov);
        $html="";
        $rta =0;
        $ci = 0;
        $cf = 9;
        $fi = $nannov.'-01-01'; 
        $ff = $nannov.'-12-31';
        #1. Se valida Si hay un año creado y hay plan presupuestal y contable
        $anno2 = $nannov+1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania ");
        if(count($an2)>0){ 
            $panno2 = $an2[0][0];
            #2. Validar Digito Configuración Para Crear Plan Contable 
            $dc = $con->Listar("SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Digito Cuentas Por Pagar'");
            if(count($dc)>0 && $dc[0][0]!=""){
                #Validar Que El Dígito Sea Válido 
                if((strlen($dc[0][0])==1)){
                    #Validar Que Existan Saldos Iniciales
                    $cp = $con->Listar("SELECT 
                                cp.* 
                            FROM 
                                gf_comprobante_pptal cp 
                            LEFT JOIN 
                                gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            WHERE 
                                tc.clasepptal = 13 and tc.tipooperacion = 1 
                                AND cp.parametrizacionanno = $panno2 ");
                    if(count($cp)>0){
                        #Validar Cuentas Por Pagar Abiertas

                        $cc = 0;
                        $rubroP ="SELECT 
                                DISTINCT dcp.id_unico as idDetalle, rp.id_unico  as codigoR, dcp.valor as valor 
                        FROM gf_detalle_comprobante_pptal dcp 
                        LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                        LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico 
                        LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
                        LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  
                        WHERE (rp.codi_presupuesto BETWEEN '$ci' AND '$cf' OR rp.codi_presupuesto BETWEEN 'a' AND 'z') 
                        AND  cp.fecha BETWEEN '$fi' AND '$ff' AND rp.parametrizacionanno = $annov 
                        AND tcp.clasepptal = 16 AND tcp.tipooperacion = 1 
                        ORDER BY codi_presupuesto ASC ";
                        $rubroP =$mysqli->query($rubroP);
                        while ($rubro = mysqli_fetch_assoc($rubroP)){
                            $condR=$rubro['codigoR'];
                            $valor = $rubro['valor'];    
                            #AFECTADO
                            $comp = $rubro['idDetalle'];    
                            $a = "SELECT valor as value, dcp.id_unico as id 
                                    FROM
                                      gf_detalle_comprobante_pptal dcp
                                    LEFT JOIN
                                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                    LEFT JOIN
                                      gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                                    WHERE
                                      dcp.comprobanteafectado = '$comp' AND top.id_unico = 1";
                            $af = $mysqli->query($a);

                            if(mysqli_num_rows($af)>0){
                                $sum=0;
                                while ($sum1= mysqli_fetch_array($af)) {
                                    $sum = $sum1['value']+$sum;
                                    #Buscar Afectaciones del afectado 
                                    $moda= "SELECT valor as value, tcp.tipooperacion as idcom 
                                            FROM
                                              gf_detalle_comprobante_pptal dcp
                                            LEFT JOIN
                                              gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                                            LEFT JOIN
                                              gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                            LEFT JOIN
                                              gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                                            WHERE
                                              dcp.comprobanteafectado = '".$sum1['id']."' AND top.id_unico != 1";
                                    $modia = $mysqli->query($moda);
                                    if(mysqli_num_rows($modia)>0){ 
                                        while ($modifa= mysqli_fetch_array($modia)){
                                            if($modifa['idcom']==2){
                                                $sum +=$modifa['value'];
                                            } else {
                                                if($modifa['idcom']==3){
                                                    $sum +=($modifa['value']*-1);
                                                } 
                                           }
                                        }
                                    }

                                }
                            } else {
                               $sum=0; 
                            }
                            $afectado = $sum;
                            #MODIFICACIONES
                            $mod= "SELECT valor as value, tcp.tipooperacion as idcom 
                                    FROM
                                      gf_detalle_comprobante_pptal dcp
                                    LEFT JOIN
                                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                    LEFT JOIN
                                      gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                                    WHERE
                                      dcp.comprobanteafectado = '$comp' AND top.id_unico != 1";
                            $modi = $mysqli->query($mod);
                            if(mysqli_num_rows($modi)>0){
                                $modifi=0;
                                while ($modif= mysqli_fetch_array($modi)){
                                    $modificacion= $modif['value'];
                                    if($modif['idcom']==2){
                                        $modifi = $modificacion+$modifi;
                                    } else {
                                        if($modif['idcom']==3){
                                            $modifi =$modifi+($modificacion*-1);
                                        } else {
                                            $modifi = 0; 
                                        }
                                   }
                                }
                            } else {
                                $modifi=0;
                            }
                            $modificacion1  = $modifi;
                            if($modificacion1<0){
                                $modificacion =$modificacion1*-1; 
                            } else {
                                $modificacion =$modificacion1;
                            }

                            #TOTAL
                            $total = $valor+$modificacion1;
                            #SALDO
                            $saldo=$total-$afectado;

                            $sl = number_format($saldo,2,'.',',');
                            if($sl !=0.00 || $sl != -0.00){
                                $cc+=1;
                            }

                        }
                        if($cc==0){
                            $html.="No Existen Cuentas Por Pagar Abiertas";
                            $rta = 1;
                        } 
                    } else {
                        $html.="No Existe Comprobante De Saldos Iniciales Para $anno2";
                        $rta = 1;
                    }
                } else {
                    $html.="Dígito Inválido";
                    $rta = 1;
                }
            } else {
                $html.="No Existe Configuración Parámetro Básico Dígito Cuentas Por Pagar";
                $rta = 1; 
            }
        } else {
            $html.="No existe Año $anno2 Creado, No Se Puede Realizar Cierre";
            $rta = 1;
        }
        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos); 
    break;
    #*Guardar Plan Presupuestal Cuentas Por Pagar
    case 2:
        $annov  = $_REQUEST['anno'];
        $nannov = anno($annov);
        $html="";
        $rta =0;
        $ci = 0;
        $cf = 9;
        $cc = 0;
        $fi = $nannov.'-01-01';
        $ff = $nannov.'-12-31';
        $anno2 = $nannov+1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
        $param = $an2[0][0];
        #Consultar Digito Cuenta X Pagar
        $dc = $con->Listar("SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Digito Cuentas Por Pagar'");
        $digito = $dc[0][0];
        #Consultar Rubros Copiar
        $arrayrubros = array();
        $arraydetalles = array();
        $arrayterceros = array();
        $rubroP ="SELECT 
                DISTINCT dcp.id_unico as idDetalle, rp.id_unico  as codigoR, 
                dcp.valor as valor , dcp.tercero as tercero 
        FROM gf_detalle_comprobante_pptal dcp 
        LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
        LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico 
        LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
        LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  
        WHERE (rp.codi_presupuesto BETWEEN '$ci' AND '$cf' OR rp.codi_presupuesto BETWEEN 'a' AND 'z') 
        AND  cp.fecha BETWEEN '$fi' AND '$ff' AND rp.parametrizacionanno = $annov AND cp.parametrizacionanno = $annov 
        AND tcp.clasepptal = 16 AND tcp.tipooperacion = 1 
        ORDER BY codi_presupuesto ASC ";
        $rubroP =$mysqli->query($rubroP);
        $tercerod = 2;
        $valorr = array();
        while ($rubro = mysqli_fetch_assoc($rubroP)){
            
            $condR  = $rubro['codigoR'];
            $valor  = $rubro['valor'];    
            $tercerod = $rubro['tercero'];
            #AFECTADO
            $comp = $rubro['idDetalle'];    
            $a = "SELECT valor as value, dcp.id_unico as id 
                    FROM
                      gf_detalle_comprobante_pptal dcp
                    LEFT JOIN
                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                    LEFT JOIN
                      gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                    WHERE
                      dcp.comprobanteafectado = '$comp' AND top.id_unico = 1";
            $af = $mysqli->query($a);

            if(mysqli_num_rows($af)>0){
                $sum=0;
                while ($sum1= mysqli_fetch_array($af)) {
                    $sum = $sum1['value']+$sum;
                    #Buscar Afectaciones del afectado 
                    $moda= "SELECT valor as value, tcp.tipooperacion as idcom 
                            FROM
                              gf_detalle_comprobante_pptal dcp
                            LEFT JOIN
                              gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                            LEFT JOIN
                              gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                            LEFT JOIN
                              gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                            WHERE
                              dcp.comprobanteafectado = '".$sum1['id']."' AND top.id_unico != 1";
                    $modia = $mysqli->query($moda);
                    if(mysqli_num_rows($modia)>0){ 
                        while ($modifa= mysqli_fetch_array($modia)){
                            if($modifa['idcom']==2){
                                $sum +=$modifa['value'];
                            } else {
                                if($modifa['idcom']==3){
                                    $sum +=($modifa['value']*-1);
                                } 
                           }
                        }
                    }

                }
            } else {
               $sum=0; 
            }
            $afectado = $sum;
            #MODIFICACIONES
            $mod= "SELECT valor as value, tcp.tipooperacion as idcom 
                    FROM
                      gf_detalle_comprobante_pptal dcp
                    LEFT JOIN
                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                    LEFT JOIN
                      gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                    WHERE
                      dcp.comprobanteafectado = '$comp' AND top.id_unico != 1";
            $modi = $mysqli->query($mod);
            if(mysqli_num_rows($modi)>0){
                $modifi=0;
                while ($modif= mysqli_fetch_array($modi)){
                    $modificacion= $modif['value'];
                    if($modif['idcom']==2){
                        $modifi = $modificacion+$modifi;
                    } else {
                        if($modif['idcom']==3){
                            $modifi =$modifi+($modificacion*-1);
                        } else {
                            $modifi = 0; 
                        }
                   }
                }
            } else {
                $modifi=0;
            }
            $modificacion1  = $modifi;
            if($modificacion1<0){
                $modificacion =$modificacion1*-1; 
            } else {
                $modificacion =$modificacion1;
            }

            #TOTAL
            $total = $valor+$modificacion1;
            #SALDO
            $saldo=$total-$afectado;

            $sl = number_format($saldo,2,'.',',');
            if($sl !=0.00 || $sl != -0.00){ 
                
                if(in_array($condR, $arrayrubros)) {
                    $valorr[$condR] +=$saldo;
                } else {
                    $valorr[$condR] = $saldo;
                    array_push ( $arrayrubros , $condR );
                }
                $cc+=1;
                if(in_array($comp, $arraydetalles)) {
                   
                } else {
                    array_push ( $arraydetalles , $comp );
                    
                }
                if(in_array($tercerod, $arrayterceros)) {

                } else {
                    array_push ( $arrayterceros , $tercerod );
                }
                
            }

        }
        
        
        $rg=0;
        #Si Encontro Rubros  Guardar Plan Contable CXP $digito
        $arrayGuardados = array();
        $arrayFuentes   = array();
        if($cc>0){
            $vigencia           = $annov;
            $parametrizacion    = $param;
            $tipoClase          = 15;
            
            $tipoVigencia       = 5;
            $sector             = 7;
            $rb = $con->Listar("SELECT * FROM gf_rubro_pptal WHERE id_unico IN (".implode(',',$arrayrubros).") ORDER BY codi_presupuesto DESC");
            for ($i = 0; $i < count($rb); $i++) { 
                $idr              = $rb[$i][0];
                $codO             = $rb[$i][2];
                $codi_presupuesto = $digito.''.$rb[$i][2];
                $nombre           = $rb[$i][1];
                $movimiento       = $rb[$i][3];
                $manpac           = $rb[$i][4];
                $equivalente      = $rb[$i][2]; 
                $destino          = $rb[$i][10]; ;
                if(in_array($codO, $arrayGuardados)) {
                } else {
                    #Guardar
                    $sql_cons ="INSERT INTO `gf_rubro_pptal`  
                        ( `nombre`, `codi_presupuesto`, 
                        `movimiento`,`manpac`,
                        `vigencia`,`parametrizacionanno`, 
                        `tipoclase`, `destino`, 
                        `tipovigencia`, `sector`, 
                        `equivalente`) 
                        VALUES (:nombre, :codi_presupuesto, 
                        :movimiento, :manpac, 
                        :vigencia,:parametrizacionanno, 
                        :tipoclase, :destino, 
                        :tipovigencia, :sector, 
                        :equivalente)";
                    $sql_dato = array(
                    array(":nombre",$nombre),
                    array(":codi_presupuesto",$codi_presupuesto),
                    array(":movimiento",$movimiento),
                    array(":manpac",$manpac),
                    array(":vigencia",$vigencia),
                    array(":parametrizacionanno",$parametrizacion),
                    array(":tipoclase",$tipoClase),
                    array(":destino",$destino),
                    array(":tipovigencia",$tipoVigencia),    
                    array(":sector",$sector),
                    array(":equivalente",$codO), );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    if (empty($obj_resp)) {
                        array_push ( $arrayGuardados , $codO );
                        $rg+=1;
                        #Buscar Si Hay Rubro Fuente Configurado
                        $f = $con->Listar("SELECT * FROM gf_rubro_fuente WHERE rubro = $idr");
                        if(count($f)>0){
                            #Buscar Si Hay Una Fuente Igual Para La Parametrizacionanno 
                            $fp = $con->Listar("SELECT * FROM gf_fuente WHERE id_unico = '".$f[0][2]."'");
                            $fn = $con->Listar("SELECT * FROM gf_fuente WHERE nombre = '".$fp[0][1]."' AND parametrizacionanno = $parametrizacion");
                            if(count($fn)>0){
                                $fuente = $fn[0][0];
                            } else {
                                #Guardar
                                $nb = $fp[0][1];
                                $sql_cons ="INSERT INTO `gf_fuente`  
                                    ( `nombre`,`parametrizacionanno`,`compania` ) 
                                    VALUES (:nombre,:parametrizacionanno, :compania)";
                                $sql_dato = array(
                                array(":nombre",$nb),
                                array(":parametrizacionanno",$parametrizacion), 
                                array(":compania",$compania));
                                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                                
                                $ft = $fn = $con->Listar("SELECT * FROM gf_fuente WHERE nombre = '".$nb."' AND parametrizacionanno = $parametrizacion");
                                $fuente = $ft[0][0];
                            }
                        } else {
                            $ft = $fn = $con->Listar("SELECT * FROM gf_fuente WHERE parametrizacionanno = $parametrizacion");
                            $fuente = $ft[0][0]; 
                        }
                        #Guardar Rubro Fuente 
                        $rbr = $con->Listar("SELECT MAX(id_unico) FROM gf_rubro_pptal WHERE codi_presupuesto = '$codi_presupuesto' AND parametrizacionanno =$parametrizacion ");
                        $rbr = $rbr[0][0];
                        $sql_cons ="INSERT INTO `gf_rubro_fuente`  
                                    ( `rubro`,`fuente` ) 
                                    VALUES (:rubro,:fuente)";
                        $sql_dato = array(
                        array(":rubro",$rbr),
                        array(":fuente",$fuente));
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        $rbf = $con->Listar("SELECT MAX(id_unico) FROM gf_rubro_fuente WHERE rubro = $rbr AND fuente =$fuente ");
                        $rbf = $rbf[0][0];
                        
                        #Buscar Comprobante Saldos Iniciales
                        $cp = $con->Listar("SELECT 
                                cp.* 
                            FROM 
                                gf_comprobante_pptal cp 
                            LEFT JOIN 
                                gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            WHERE 
                                tc.clasepptal = 13 and tc.tipooperacion = 1 
                                AND cp.parametrizacionanno = $parametrizacion ");
                        $comp = $cp[0][0];
                        $desc = $cp[0][4]; 
                        
                        $valor = $valorr[$idr];
                        $tercerod =  $vl[0][1];
                        #Insertar Detalle Comprobante Apropiación Inicial
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal`   
                                    ( `descripcion`,`valor`, `comprobantepptal`, 
                                    `rubrofuente`, `tercero`, `proyecto` ) 
                                    VALUES (:descripcion,:valor, :comprobantepptal, 
                                    :rubrofuente, :tercero, :proyecto)";
                        $sql_dato = array(
                        array(":descripcion",$desc),
                        array(":valor",$valor), 
                        array(":comprobantepptal",$comp), 
                        array(":rubrofuente",$rbf), 
                        array(":tercero",$tercerod), 
                        array(":proyecto",2147483647));
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    
                    
                }
                #Ciclo Para Guardar Predecesores;
                if(!empty($rb[$i][9])){
                    $r=true;
                    $pr = $rb[$i][9];
                    while($r == true){
                        $rbp    = $con->Listar("SELECT * FROM gf_rubro_pptal WHERE id_unico =$pr");
                        $codP   = $rbp[0][2];
                        if(in_array($codP, $arrayGuardados)) {
                        } else {
                            #Guardar
                            $codi_presupuesto = $digito.''.$rbp[0][2];
                            $nombre           = $rbp[0][1];
                            $movimiento       = $rbp[0][3];
                            $manpac           = $rbp[0][4];
                            $equivalente      = $rbp[0][2]; 
                            #Guardar
                            $sql_cons ="INSERT INTO `gf_rubro_pptal`  
                                ( `nombre`, `codi_presupuesto`, 
                                `movimiento`,`manpac`,
                                `vigencia`,`parametrizacionanno`, 
                                `tipoclase`, `destino`, 
                                `tipovigencia`, `sector`, 
                                `equivalente`) 
                                VALUES (:nombre, :codi_presupuesto, 
                                :movimiento, :manpac, 
                                :vigencia,:parametrizacionanno, 
                                :tipoclase, :destino, 
                                :tipovigencia, :sector, 
                                :equivalente)";
                            $sql_dato = array(
                            array(":nombre",$nombre),
                            array(":codi_presupuesto",$codi_presupuesto),
                            array(":movimiento",$movimiento),
                            array(":manpac",$manpac),
                            array(":vigencia",$vigencia),
                            array(":parametrizacionanno",$parametrizacion),
                            array(":tipoclase",$tipoClase),
                            array(":destino",$destino),
                            array(":tipovigencia",$tipoVigencia),  
                            array(":sector",$sector),
                            array(":equivalente",$codP), );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                            if (empty($obj_resp)) {
                                    array_push ( $arrayGuardados , $codP );
                            }
                            
                            
                        }
                        if(empty($rbp[0][9])){
                            $r = false;
                        } else {
                           $pr =$rbp[0][9];
                        }
                        
                    }
                }
                
            }
        }
        if($rg>0){
            $rta =0;
        } else {
            $rta =1;
        }
        echo $rta;
    break;
    #Validaciones Cierre Reservas
    case 3:
        $annov  = $_REQUEST['anno'];
        $nannov = anno($annov);
        $html="";
        $rta =0;
        $ci = 0;
        $cf = 9;
        $fi = $nannov.'-01-01';
        $ff = $nannov.'-12-31';
        #1. Se valida Si hay un año creado y hay plan presupuestal y contable
        $anno2 = $nannov+1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
        if(count($an2)>0){ 
            $panno2 = $an2[0][0];
            #2. Validar Digito Configuración Para Crear Plan Contable 
            $dc = $con->Listar("SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Digito Reservas'");
            if(count($dc)>0 && $dc[0][0]!=""){
                #Validar Que El Dígito Sea Válido 
                if((strlen($dc[0][0])==1)){
                    #Validar Que Existan Saldos Iniciales
                    $cp = $con->Listar("SELECT 
                                cp.* 
                            FROM 
                                gf_comprobante_pptal cp 
                            LEFT JOIN 
                                gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            WHERE 
                                tc.clasepptal = 13 and tc.tipooperacion = 1 
                                AND cp.parametrizacionanno = $panno2 ");
                    if(count($cp)>0){
                        #Validar Reservas Abiertas

                        $cc = 0;
                        $rubroP ="SELECT 
                                DISTINCT dcp.id_unico as idDetalle, rp.id_unico  as codigoR, 
                                dcp.valor as valor , dcp.tercero as tercero 
                        FROM gf_detalle_comprobante_pptal dcp 
                        LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                        LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico 
                        LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
                        LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  
                        WHERE (rp.codi_presupuesto BETWEEN '$ci' AND '$cf' OR rp.codi_presupuesto BETWEEN 'a' AND 'z') 
                        AND  cp.fecha BETWEEN '$fi' AND '$ff' AND rp.parametrizacionanno = $annov 
                        AND tcp.clasepptal = 15 AND tcp.tipooperacion = 1  
                        ORDER BY codi_presupuesto ASC ";
                        $rubroP =$mysqli->query($rubroP);
                        while ($rubro = mysqli_fetch_assoc($rubroP)){
                            $condR=$rubro['codigoR'];
                            $valor = $rubro['valor'];    
                            #AFECTADO
                            $comp = $rubro['idDetalle'];    
                            $a = "SELECT valor as value, dcp.id_unico as id 
                                    FROM
                                      gf_detalle_comprobante_pptal dcp
                                    LEFT JOIN
                                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                    LEFT JOIN
                                      gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                                    WHERE
                                      dcp.comprobanteafectado = '$comp' AND top.id_unico = 1";
                            $af = $mysqli->query($a);

                            if(mysqli_num_rows($af)>0){
                                $sum=0;
                                while ($sum1= mysqli_fetch_array($af)) {
                                    $sum = $sum1['value']+$sum;
                                    #Buscar Afectaciones del afectado 
                                    $moda= "SELECT valor as value, tcp.tipooperacion as idcom 
                                            FROM
                                              gf_detalle_comprobante_pptal dcp
                                            LEFT JOIN
                                              gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                                            LEFT JOIN
                                              gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                            LEFT JOIN
                                              gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                                            WHERE
                                              dcp.comprobanteafectado = '".$sum1['id']."' AND top.id_unico != 1";
                                    $modia = $mysqli->query($moda);
                                    if(mysqli_num_rows($modia)>0){ 
                                        while ($modifa= mysqli_fetch_array($modia)){
                                            if($modifa['idcom']==2){
                                                $sum +=$modifa['value'];
                                            } else {
                                                if($modifa['idcom']==3){
                                                    $sum +=($modifa['value']*-1);
                                                } 
                                           }
                                        }
                                    }

                                }
                            } else {
                               $sum=0; 
                            }
                            $afectado = $sum;
                            #MODIFICACIONES
                            $mod= "SELECT valor as value, tcp.tipooperacion as idcom 
                                    FROM
                                      gf_detalle_comprobante_pptal dcp
                                    LEFT JOIN
                                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                    LEFT JOIN
                                      gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                                    WHERE
                                      dcp.comprobanteafectado = '$comp' AND top.id_unico != 1";
                            $modi = $mysqli->query($mod);
                            if(mysqli_num_rows($modi)>0){
                                $modifi=0;
                                while ($modif= mysqli_fetch_array($modi)){
                                    $modificacion= $modif['value'];
                                    if($modif['idcom']==2){
                                        $modifi = $modificacion+$modifi;
                                    } else {
                                        if($modif['idcom']==3){
                                            $modifi =$modifi+($modificacion*-1);
                                        } else {
                                            $modifi = 0; 
                                        }
                                   }
                                }
                            } else {
                                $modifi=0;
                            }
                            $modificacion1  = $modifi;
                            if($modificacion1<0){
                                $modificacion =$modificacion1*-1; 
                            } else {
                                $modificacion =$modificacion1;
                            }

                            #TOTAL
                            $total = $valor+$modificacion1;
                            #SALDO
                            $saldo=$total-$afectado;

                            $sl = number_format($saldo,2,'.',',');
                            if($sl !=0.00 || $sl != -0.00){
                                $cc+=1;
                            }

                        }
                        if($cc==0){
                            $html.="No Existen Reservas Abiertas";
                            $rta = 1;
                        } 
                    } else {
                        $html.="No Existe Comprobante De Saldos Iniciales Para $anno2";
                        $rta = 1;
                    }
                } else {
                    $html.="Dígito Inválido";
                    $rta = 1;
                }
            } else {
                $html.="No Existe Configuración Parámetro Básico Dígito Reservas";
                $rta = 1; 
            }
        } else {
            $html.="No existe Año $anno2 Creado, No Se Puede Realizar Cierre";
            $rta = 1;
        }
        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos); 
    break;
    #*Guardar Plan Presupuestal Reservas
    case 4:
        $annov  = $_REQUEST['anno'];
        $nannov = anno($annov);
        $html="";
        $rta =0;
        $ci = 0;
        $cf = 9;
        $cc = 0;
        $fi = $nannov.'-01-01';
        $ff = $nannov.'-12-31';
        $anno2 = $nannov+1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
        $param = $an2[0][0];
        #Consultar Digito Reservas
        $dc = $con->Listar("SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Digito Reservas'");
        $digito = $dc[0][0];
        #Consultar Rubros Copiar
        $arrayrubros = array();
        $arraydetalles = array();
        $rubroP ="SELECT 
                DISTINCT dcp.id_unico as idDetalle, rp.id_unico  as codigoR, 
                dcp.valor as valor , dcp.tercero as tercero 
        FROM gf_detalle_comprobante_pptal dcp 
        LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
        LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico 
        LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
        LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  
        WHERE (rp.codi_presupuesto BETWEEN '$ci' AND '$cf' OR rp.codi_presupuesto BETWEEN 'a' AND 'z') 
        AND  cp.fecha BETWEEN '$fi' AND '$ff' AND rp.parametrizacionanno = $annov AND cp.parametrizacionanno = $annov 
        AND tcp.clasepptal = 15 and tcp.tipooperacion = 1 
        ORDER BY codi_presupuesto ASC ";
        $rubroP =$mysqli->query($rubroP);
        $tercerod =2;
        $valorr = array();
        while ($rubro = mysqli_fetch_assoc($rubroP)){
            
            $condR = $rubro['codigoR'];
            $valor = $rubro['valor'];   
            #AFECTADO
            $comp = $rubro['idDetalle'];    
            $a = "SELECT valor as value, dcp.id_unico as id 
                    FROM
                      gf_detalle_comprobante_pptal dcp
                    LEFT JOIN
                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                    LEFT JOIN
                      gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                    WHERE
                      dcp.comprobanteafectado = '$comp' AND top.id_unico = 1";
            $af = $mysqli->query($a);

            if(mysqli_num_rows($af)>0){
                $sum=0;
                while ($sum1= mysqli_fetch_array($af)) {
                    $sum = $sum1['value']+$sum;
                    #Buscar Afectaciones del afectado 
                    $moda= "SELECT valor as value, tcp.tipooperacion as idcom 
                            FROM
                              gf_detalle_comprobante_pptal dcp
                            LEFT JOIN
                              gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                            LEFT JOIN
                              gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                            LEFT JOIN
                              gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                            WHERE
                              dcp.comprobanteafectado = '".$sum1['id']."' AND top.id_unico != 1";
                    $modia = $mysqli->query($moda);
                    if(mysqli_num_rows($modia)>0){ 
                        while ($modifa= mysqli_fetch_array($modia)){
                            if($modifa['idcom']==2){
                                $sum +=$modifa['value'];
                            } else {
                                if($modifa['idcom']==3){
                                    $sum +=($modifa['value']*-1);
                                } 
                           }
                        }
                    }

                }
            } else {
               $sum=0; 
            }
            $afectado = $sum;
            #MODIFICACIONES
            $mod= "SELECT valor as value, tcp.tipooperacion as idcom 
                    FROM
                      gf_detalle_comprobante_pptal dcp
                    LEFT JOIN
                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                    LEFT JOIN
                      gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                    WHERE
                      dcp.comprobanteafectado = '$comp' AND top.id_unico != 1";
            $modi = $mysqli->query($mod);
            if(mysqli_num_rows($modi)>0){
                $modifi=0;
                while ($modif= mysqli_fetch_array($modi)){
                    $modificacion= $modif['value'];
                    if($modif['idcom']==2){
                        $modifi = $modificacion+$modifi;
                    } else {
                        if($modif['idcom']==3){
                            $modifi =$modifi+($modificacion*-1);
                        } else {
                            $modifi = 0; 
                        }
                   }
                }
            } else {
                $modifi=0;
            }
            $modificacion1  = $modifi;
            if($modificacion1<0){
                $modificacion =$modificacion1*-1; 
            } else {
                $modificacion =$modificacion1;
            }

            #TOTAL
            $total = $valor+$modificacion1;
            #SALDO
            $saldo=$total-$afectado;

            $sl = number_format($saldo,2,'.',',');
            if($sl !=0.00 || $sl != -0.00){
                if(in_array($condR, $arrayrubros)) {

                } else {
                    array_push ( $arrayrubros , $condR );
                    $cc+=1;
                }
                if(in_array($comp, $arraydetalles)) {
                    $valorr[$condR] +=$saldo;
                } else {
                    $valorr[$condR] = $saldo;
                    array_push ( $arraydetalles , $comp );
                }
                
            }

        }
        $rg=0;
        #Si Encontro Rubros  Guardar Plan Contable CXP $digito
        $arrayGuardados = array();
        $arrayFuentes   = array();
        $arrayConceptos = array();
        $arrayCuentas   = array();
        if($cc>0){
            $vigencia           = $annov;
            $parametrizacion    = $param;
            $tipoClase          = 16;
            $tipoVigencia       = 6;
            $sector             = 7;
            $rb = $con->Listar("SELECT * FROM gf_rubro_pptal WHERE id_unico IN (".implode(',',$arrayrubros).") ORDER BY codi_presupuesto DESC");
            for ($i = 0; $i < count($rb); $i++) {
                $idr              = $rb[$i][0];
                $codO             = $rb[$i][2];
                $codi_presupuesto = $digito.''.$rb[$i][2];
                $nombre           = $rb[$i][1];
                $movimiento       = $rb[$i][3];
                $manpac           = $rb[$i][4];
                $equivalente      = $rb[$i][2]; 
                $destino          = $rb[$i][10]; 
                if(in_array($codO, $arrayGuardados)) {
                } else {
                    #Guardar
                    $sql_cons ="INSERT INTO `gf_rubro_pptal`  
                        ( `nombre`, `codi_presupuesto`, 
                        `movimiento`,`manpac`,
                        `vigencia`,`parametrizacionanno`, 
                        `tipoclase`, `destino`, 
                        `tipovigencia`, `sector`, 
                        `equivalente`) 
                        VALUES (:nombre, :codi_presupuesto, 
                        :movimiento, :manpac, 
                        :vigencia,:parametrizacionanno, 
                        :tipoclase, :destino, 
                        :tipovigencia, :sector, 
                        :equivalente)";
                    $sql_dato = array(
                    array(":nombre",$nombre),
                    array(":codi_presupuesto",$codi_presupuesto),
                    array(":movimiento",$movimiento),
                    array(":manpac",$manpac),
                    array(":vigencia",$vigencia),
                    array(":parametrizacionanno",$parametrizacion),
                    array(":tipoclase",$tipoClase),
                    array(":destino",$destino),
                    array(":tipovigencia",$tipoVigencia),    
                    array(":sector",$sector),
                    array(":equivalente",$codO), );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    #var_dump($obj_resp;
                    if (empty($obj_resp)) {
                        array_push ( $arrayGuardados , $codO );
                        $rg+=1;
                        #Buscar Si Hay Rubro Fuente Configurado
                        $f = $con->Listar("SELECT * FROM gf_rubro_fuente WHERE rubro = $idr");
                        if(count($f)>0){
                            #Buscar Si Hay Una Fuente Igual Para La Parametrizacionanno 
                            $fp = $con->Listar("SELECT * FROM gf_fuente WHERE id_unico = '".$f[0][2]."'");
                            $fn = $con->Listar("SELECT * FROM gf_fuente WHERE nombre = '".$fp[0][1]."' AND parametrizacionanno = $parametrizacion");
                            if(count($fn)>0){
                                $fuente = $fn[0][0];
                            } else {
                                #Guardar
                                $nb = $fp[0][1];
                                $sql_cons ="INSERT INTO `gf_fuente`  
                                    ( `nombre`,`parametrizacionanno`,`compania` ) 
                                    VALUES (:nombre,:parametrizacionanno, :compania)";
                                $sql_dato = array(
                                array(":nombre",$nb),
                                array(":parametrizacionanno",$parametrizacion), 
                                array(":compania",$compania));
                                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                                
                                $ft = $fn = $con->Listar("SELECT * FROM gf_fuente WHERE nombre = '".$nb."' AND parametrizacionanno = $parametrizacion");
                                $fuente = $ft[0][0];
                            }
                        } else {
                            $ft = $fn = $con->Listar("SELECT * FROM gf_fuente WHERE parametrizacionanno = $parametrizacion");
                            $fuente = $ft[0][0]; 
                        }
                        #Guardar Rubro Fuente 
                        $rbr = $con->Listar("SELECT MAX(id_unico) FROM gf_rubro_pptal WHERE codi_presupuesto = '$codi_presupuesto' AND parametrizacionanno =$parametrizacion ");
                        $rbr = $rbr[0][0];
                        $sql_cons ="INSERT INTO `gf_rubro_fuente`  
                                    ( `rubro`,`fuente` ) 
                                    VALUES (:rubro,:fuente)";
                        $sql_dato = array(
                        array(":rubro",$rbr),
                        array(":fuente",$fuente));
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        $rbf = $con->Listar("SELECT MAX(id_unico) FROM gf_rubro_fuente WHERE rubro = $rbr AND fuente =$fuente ");
                        $rbf = $rbf[0][0];
                        
                        #Buscar Comprobante Saldos Iniciales
                        $cp = $con->Listar("SELECT 
                                cp.* 
                            FROM 
                                gf_comprobante_pptal cp 
                            LEFT JOIN 
                                gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                            WHERE 
                                tc.clasepptal = 13 and tc.tipooperacion = 1 
                                AND cp.parametrizacionanno = $parametrizacion ");
                        $comp = $cp[0][0];
                        $desc = $cp[0][4];
                        
                        
                        #*** Buscar Afectaciones y Sumar Valor De Los Detalles 
                        $rubroP ="SELECT 
                                DISTINCT dcp.id_unico as idDetalle, dcp.rubroFuente  as codigoR, 
                                dcp.valor as valor , dcp.tercero as tercero 
                        FROM gf_detalle_comprobante_pptal dcp 
                        LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                        WHERE rf.rubro = $idr  
                        AND dcp.id_unico IN (".implode(',',$arraydetalles).")";
                        
                        $rubroP =$mysqli->query($rubroP);
                        $tercero =2;
                        $saldoFinal =0;
                        while ($rubro = mysqli_fetch_assoc($rubroP)){
                            $condR = $rubro['codigoR'];
                            $valor = $rubro['valor'];   
                            $tercero = $rubro['tercero'];   
                            #AFECTADO
                            $compm = $rubro['idDetalle'];    
                            $a = "SELECT valor as value, dcp.id_unico as id 
                                    FROM
                                      gf_detalle_comprobante_pptal dcp
                                    LEFT JOIN
                                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                    LEFT JOIN
                                      gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                                    WHERE
                                      dcp.comprobanteafectado = '$compm' AND top.id_unico = 1";
                            $af = $mysqli->query($a);

                            if(mysqli_num_rows($af)>0){
                                $sum=0;
                                while ($sum1= mysqli_fetch_array($af)) {
                                    $sum = $sum1['value']+$sum;
                                    #Buscar Afectaciones del afectado 
                                    $moda= "SELECT valor as value, tcp.tipooperacion as idcom 
                                            FROM
                                              gf_detalle_comprobante_pptal dcp
                                            LEFT JOIN
                                              gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                                            LEFT JOIN
                                              gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                            LEFT JOIN
                                              gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                                            WHERE
                                              dcp.comprobanteafectado = '".$sum1['id']."' AND top.id_unico != 1";
                                    $modia = $mysqli->query($moda);
                                    if(mysqli_num_rows($modia)>0){ 
                                        while ($modifa= mysqli_fetch_array($modia)){
                                            if($modifa['idcom']==2){
                                                $sum +=$modifa['value'];
                                            } else {
                                                if($modifa['idcom']==3){
                                                    $sum +=($modifa['value']*-1);
                                                } 
                                           }
                                        }
                                    }

                                }
                            } else {
                               $sum=0; 
                            }
                            $afectado = $sum;
                            #MODIFICACIONES
                            $mod= "SELECT valor as value, tcp.tipooperacion as idcom 
                                    FROM
                                      gf_detalle_comprobante_pptal dcp
                                    LEFT JOIN
                                      gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                    LEFT JOIN
                                      gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                                    WHERE
                                      dcp.comprobanteafectado = '$compm' AND top.id_unico != 1";
                            $modi = $mysqli->query($mod);
                            if(mysqli_num_rows($modi)>0){
                                $modifi=0;
                                while ($modif= mysqli_fetch_array($modi)){
                                    $modificacion= $modif['value'];
                                    if($modif['idcom']==2){
                                        $modifi = $modificacion+$modifi;
                                    } else {
                                        if($modif['idcom']==3){
                                            $modifi =$modifi+($modificacion*-1);
                                        } else {
                                            $modifi = 0; 
                                        }
                                   }
                                }
                            } else {
                                $modifi=0;
                            }
                            $modificacion1  = $modifi;
                            if($modificacion1<0){
                                $modificacion =$modificacion1*-1; 
                            } else {
                                $modificacion =$modificacion1;
                            }

                            #TOTAL
                            $total = $valor+$modificacion1;
                            #SALDO
                            $saldo = $total-$afectado;
                            $saldoFinal +=$saldo;
                        }
                       
                       
                        
                        
                        $valor = $saldoFinal;
                        $tercerod = $tercero;
                        #Insertar Detalle Comprobante Apropiación Inicial
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal`   
                                    ( `descripcion`,`valor`, `comprobantepptal`, 
                                    `rubrofuente`, `tercero`, `proyecto` ) 
                                    VALUES (:descripcion,:valor, :comprobantepptal, 
                                    :rubrofuente, :tercero, :proyecto)";
                        $sql_dato = array(
                        array(":descripcion",$desc),
                        array(":valor",$valor), 
                        array(":comprobantepptal",$comp), 
                        array(":rubrofuente",$rbf), 
                        array(":tercero",$tercerod), 
                        array(":proyecto",2147483647));
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        //var_dump($obj_resp);
                    }
                    #Guardar Configuración Concepto
                    $nconcepto = $codi_presupuesto.' - '.$nombre;
                    if(in_array($nconcepto, $arrayConceptos)) {
                    } else {
                        #Guardar Concepto 
                        $sql_cons ="INSERT INTO `gf_concepto`   
                                    ( `nombre`,`clase_concepto`, `parametrizacionanno` ) 
                                    VALUES (:nombre,:clase_concepto, :parametrizacionanno)";
                        $sql_dato = array(
                        array(":nombre",$nconcepto),
                        array(":clase_concepto",4), 
                        array(":parametrizacionanno",$parametrizacion), );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            array_push ( $arrayConceptos , $nconcepto );
                            #Buscar Id Concepto
                            $cn = $con->Listar("SELECT MAX(id_unico) FROM gf_concepto WHERE nombre = '$nconcepto' AND clase_concepto = 4 AND parametrizacionanno = $parametrizacion");
                            $conc = $cn[0][0];
                            #Configurar Concepto Rubro 
                            $sql_cons ="INSERT INTO `gf_concepto_rubro`   
                                    ( `concepto`,`rubro`) 
                                    VALUES (:concepto,:rubro)";
                            $sql_dato = array(
                            array(":concepto",$conc),
                            array(":rubro",$rbr),  );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                            #var_dump($obj_resp);
                            if(empty($obj_resp)){
                                $cnr = $con->Listar("SELECT MAX(id_unico) FROM gf_concepto_rubro WHERE concepto = $conc AND rubro = $rbr");
                                $concr = $cnr[0][0];
                                #Buscar La Configuración de Rubro Cuenta Y Guardarla 
                                $cc = $con->Listar("SELECT crc.id_unico, 
                                                cd.codi_cuenta, 
                                                cc.codi_cuenta 
                                            FROM gf_concepto_rubro_cuenta crc 
                                            LEFT JOIN gf_concepto_rubro cr ON crc.concepto_rubro = cr.id_unico 
                                            LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico 
                                            LEFT JOIN gf_cuenta cc ON crc.cuenta_credito = cc.id_unico 
                                            WHERE cr.rubro = $idr");
                                if(count($cc)>0){
                                    $cd = $cc[0][1];
                                    $cc = $cc[0][2];
                                    
                                    #Buscar La Cuenta Debito Equivalente Para Vigencia 
                                    $cde = $con->Listar("SELECT * FROM gf_cuenta WHERE FIND_IN_SET($cd, equivalente_va )  AND parametrizacionanno = $parametrizacion");
                                    if(count($cde)>0){
                                        $cuentad = $cde[0][0];
                                    } else { 
                                        #Buscar La Cuenta Debito Igual Para Vigencia 
                                        $cde = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = $cd AND parametrizacionanno = $parametrizacion");
                                        $cuentad = $cde[0][0];
                                    }
                                    if(!empty($cuentad)>0){
                                        
                                        #Buscar Cuenta Crédito Equivalente
                                        $cce = $con->Listar("SELECT * FROM gf_cuenta WHERE FIND_IN_SET($cc, equivalente_va )  AND parametrizacionanno = $parametrizacion");
                                        if(count($cce)>0){
                                            $cuentac = $cce[0][0];
                                        } else {
                                            #Buscar Cuenta Crédito Igual
                                            $cce = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = $cc AND parametrizacionanno = $parametrizacion");
                                            $cuentac = $cce[0][0];
                                        }
                                        if(!empty($cuentac)>0){
                                            #Guardar Concepto rubro Cuenta 
                                            $sql_cons ="INSERT INTO `gf_concepto_rubro_cuenta`   
                                                    ( `concepto_rubro`,`cuenta_debito`,`cuenta_credito`) 
                                                    VALUES (:concepto_rubro,:cuenta_debito,:cuenta_credito)";
                                            $sql_dato = array(
                                            array(":concepto_rubro",$concr),
                                            array(":cuenta_debito",$cuentad),
                                            array(":cuenta_credito",$cuentac),);
                                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                                            if(empty($obj_resp)){
                                                
                                            } else {
                                                $html.="No se Pudo Guardar Configuración Del Concepto:  ".$nconcepto. "<br/>";
                                                $rta = 3;  
                                            }
                                            
                                        } else {
                                           if(in_array($cc, $arrayCuentas)) {
                                            } else {
                                                array_push ( $arrayCuentas , $cc );
                                                $html.="No se Encontró Cuenta. ".$cc." Para Año $anno2". "<br/>";
                                                $rta = 3;
                                            }  
                                        }
                                    } else{
                                        if(in_array($cd, $arrayCuentas)) {
                                        } else {
                                            array_push ( $arrayCuentas , $cd );
                                            $html.="No se Encontró Cuenta. ".$cd." Para Año $anno2". "<br/>";
                                            $rta = 3;
                                        }
                                        
                                    }
                                } else {
                                    $html.="No se Pudo Guardar Concepto Rubro".$nconcepto." <br/>";
                                    $rta = 3;
                                }
                            } else {
                                $html.="No se Pudo Guardar Concepto Rubro".$nconcepto." <br/>";
                                $rta = 3;
                            }
                            
                        } else {
                            $html.="No se Pudo Guardar Concepto ".$nconcepto." <br/>";
                            $rta = 3;
                        }
                    }
                    
                    
                    
                }
                #Ciclo Para Guardar Predecesores;
                if(!empty($rb[$i][9])){
                    $r=true;
                    $pr = $rb[$i][9];
                    while($r == true){
                        $rbp    = $con->Listar("SELECT * FROM gf_rubro_pptal WHERE id_unico =$pr");
                        $codP   = $rbp[0][2];
                        if(in_array($codP, $arrayGuardados)) {
                        } else {
                            #Guardar
                            $codi_presupuesto = $digito.''.$rbp[0][2];
                            $nombre           = $rbp[0][1];
                            $movimiento       = $rbp[0][3];
                            $manpac           = $rbp[0][4];
                            $equivalente      = $rbp[0][2]; 
                            #Guardar
                            $sql_cons ="INSERT INTO `gf_rubro_pptal`  
                                ( `nombre`, `codi_presupuesto`, 
                                `movimiento`,`manpac`,
                                `vigencia`,`parametrizacionanno`, 
                                `tipoclase`, `destino`, 
                                `tipovigencia`, `sector`, 
                                `equivalente`) 
                                VALUES (:nombre, :codi_presupuesto, 
                                :movimiento, :manpac, 
                                :vigencia,:parametrizacionanno, 
                                :tipoclase, :destino, 
                                :tipovigencia, :sector, 
                                :equivalente)";
                            $sql_dato = array(
                            array(":nombre",$nombre),
                            array(":codi_presupuesto",$codi_presupuesto),
                            array(":movimiento",$movimiento),
                            array(":manpac",$manpac),
                            array(":vigencia",$vigencia),
                            array(":parametrizacionanno",$parametrizacion),
                            array(":tipoclase",$tipoClase),
                            array(":destino",$destino),
                            array(":tipovigencia",$tipoVigencia),  
                            array(":sector",$sector),
                            array(":equivalente",$codP), );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                            if (empty($obj_resp)) {
                                    array_push ( $arrayGuardados , $codP );
                            }
                            
                            
                        }
                        if(empty($rbp[0][9])){
                            $r = false;
                        } else {
                           $pr =$rbp[0][9];
                        }
                        
                    }
                }
                
            }
        }
        if($rg>0){
            if($rta==3){
                $rta =3;
            } else {
                $rta =0;
            }
        } else {
            $rta =1;
        }
        $datos = array("msj"=>$html,"rta"=>$rta);
        
        echo json_encode($datos);
    break;
    #****************************** Validaciones Eliminar ***************#
    #Validar Si Existe Cierre Reservas
    case 5:
        $tipoClase          = 16;
        $tipoVigencia       = 6;
        $anno = $_REQUEST['anno'];
        $nannov = anno($anno);
        $anno2 = $nannov+1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
        $nanno2 = $an2[0][0];
        $rb = $con->Listar("SELECT * FROM gf_rubro_pptal 
                 WHERE tipoclase=$tipoClase AND tipovigencia = $tipoVigencia 
                 AND parametrizacionanno = $nanno2");
        if(count($rb)>0){
            echo 1;
        } else {
            echo 0;
        }
    break;
    #Validar Si Existe Cierre Cuentas Por Pagar
    case 6:
        $tipoClase          = 15;
        $tipoVigencia       = 5;
        $anno = $_REQUEST['anno'];
        $nannov = anno($anno);
        $anno2 = $nannov+1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
        $nanno2 = $an2[0][0];
        $rb = $con->Listar("SELECT * FROM gf_rubro_pptal 
                 WHERE tipoclase=$tipoClase AND tipovigencia = $tipoVigencia 
                 AND parametrizacionanno = $nanno2");
        if(count($rb)>0){
            echo 1;
        } else {
            echo 0;
        }
    break;
    #Eliminar Configuración Reservas
    case 7:
        $rta =0;
        $tipoClase          = 16;
        $tipoVigencia       = 6;
        $claseconcepto      = 4;
        $anno = $_REQUEST['anno'];
        $nannov = anno($anno);
        $anno2 = $nannov+1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
        $parametrizacion    = $an2[0][0];
        #* Validar Si Ya Hay Movimiento de Reservas *#
        $rb = $con->Listar("SELECT dcp.* FROM gf_detalle_comprobante_pptal dcp 
             LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
             LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
             LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
             LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
             WHERE rb.tipoclase = $tipoClase and rb.tipovigencia = $tipoVigencia 
             AND cp.parametrizacionanno = $parametrizacion 
             AND (tc.clasepptal != 13)"); 
        if(count($rb)>0){
            $rta =1;
        } else {
            #* Buscar Rubos Reservas *#
            $rb = $con->Listar("SELECT * FROM gf_rubro_pptal 
                 WHERE tipoclase=$tipoClase AND tipovigencia = $tipoVigencia 
                 AND parametrizacionanno = $parametrizacion");
            for ($i = 0; $i < count($rb); $i++) {
                #Buscar Concepto Rubro
                $rubro = $rb[$i][0];
                $cr = $con->Listar("SELECT * FROM gf_concepto_rubro WHERE rubro = $rubro");
                if(count($cr)>0){
                    for ($j = 0; $j < count($cr); $j++) {
                        $conceptoRubro = $cr[$j][0];
                        #* Eliminar Concepto Rubro Asociados Al Concepto Rubro Encontrado *#
                        $sql_cons ="DELETE FROM `gf_concepto_rubro_cuenta` 
                                   WHERE `concepto_rubro`=:concepto_rubro";
                        $sql_dato = array(
                                array(":concepto_rubro",$conceptoRubro),	
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    #* Eliminar Concepto Rubro *#
                    $sql_cons ="DELETE FROM `gf_concepto_rubro` 
                                   WHERE `rubro`=:rubro";
                    $sql_dato = array(
                            array(":rubro",$rubro),	
                    );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                }
                #Buscar Rubro Fuente
                $rf = $con->Listar("SELECT * FROM gf_rubro_fuente WHERE rubro = $rubro");
                if(count($rf)>0){        
                    for ($j = 0; $j < count($rf); $j++) {
                        $rubrof = $rf[$j][0];
                        #Eliminar Apropiacion Inicial
                        $sql_cons ="DELETE FROM `gf_detalle_comprobante_pptal` 
                               WHERE `rubrofuente`=:rubrofuente ";
                        $sql_dato = array(
                                array(":rubrofuente",$rubrof),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        #Eliminar Rubro Fuente
                        $sql_cons ="DELETE FROM `gf_rubro_fuente` 
                               WHERE `id_unico`=:id_unico ";
                        $sql_dato = array(
                                array(":id_unico",$rubrof),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                }
                #Eliminar Rubro
                $sql_cons ="DELETE FROM `gf_rubro_pptal` 
                               WHERE `id_unico`=:id_unico";
                $sql_dato = array(
                        array(":id_unico",$rubro),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            #Eliminar Conceptos 
            $sql_cons ="DELETE FROM `gf_concepto` 
                           WHERE `clase_concepto`=:clase_concepto 
                           AND `parametrizacionanno` =:parametrizacionanno";
            $sql_dato = array(
                    array(":clase_concepto",$claseconcepto),
                    array(":parametrizacionanno",$parametrizacion),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(!empty($obj_resp)){
                $rta =1;
            }
        }
        echo $rta;
    break;
    #Eliminar Configuración Cuentas Por Pagar
    case 8:
        $rta =0;
        $tipoClase          = 15;
        $tipoVigencia       = 5;
        $anno = $_REQUEST['anno'];
        $nannov = anno($anno);
        $anno2 = $nannov+1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
        $parametrizacion    = $an2[0][0];
        #* Validar Si Ya Hay Movimiento de Reservas *#
        $rb = $con->Listar("SELECT dcp.* FROM gf_detalle_comprobante_pptal dcp 
             LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
             LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
             LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
             LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
             WHERE rb.tipoclase = $tipoClase and rb.tipovigencia = $tipoVigencia 
             AND cp.parametrizacionanno = $parametrizacion 
             AND (tc.clasepptal != 13)"); 
        if(count($rb)>0){
            $rta =1;
        } else {
            #* Buscar Rubos Reservas *#
            $rb = $con->Listar("SELECT * FROM gf_rubro_pptal 
                 WHERE tipoclase=$tipoClase AND tipovigencia = $tipoVigencia 
                 AND parametrizacionanno = $parametrizacion");
            for ($i = 0; $i < count($rb); $i++) { 
                $rubro = $rb[$i][0];
                #Buscar Rubro Fuente
                $rf = $con->Listar("SELECT * FROM gf_rubro_fuente WHERE rubro = $rubro");
                if(count($rf)>0){        
                    for ($j = 0; $j < count($rf); $j++) {
                        $rubrof = $rf[$j][0];
                        #Eliminar Apropiacion Inicial
                        $sql_cons ="DELETE FROM `gf_detalle_comprobante_pptal` 
                               WHERE `rubrofuente`=:rubrofuente ";
                        $sql_dato = array(
                                array(":rubrofuente",$rubrof),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        #Eliminar Rubro Fuente
                        $sql_cons ="DELETE FROM `gf_rubro_fuente` 
                               WHERE `id_unico`=:id_unico ";
                        $sql_dato = array(
                                array(":id_unico",$rubrof),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                }
                #Eliminar Rubro
                $sql_cons ="DELETE FROM `gf_rubro_pptal` 
                               WHERE `id_unico`=:id_unico";
                $sql_dato = array(
                        array(":id_unico",$rubro),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            
        }
        echo $rta;
    break;
}

