<?php 
######################################################################################################
# ***************************************** Modificaciones ***************************************** #
######################################################################################################
#08/02/2017 | Erica G. | Cuentas y saldos Vigencias Anteriores
#06/10/2017 | Erica González | Saldo débito y ccrédito
#26/07/2017 | Erica González | Cierre y verificar que los botones de habilitar y deshabilitar todos sirvieran, ya que cuando se incluyo el campo débito y crédito, no se valido
#05/07/2017 | ERICA G. | EL VALOR QUE SE MUESTRE AL DEBITO O CREDITO SEGUN CORRESPONDA
#17/04/2017 | ERICA G. | .QUE MUESTRE VARIOS REGISTROS
################################################################################################################################
# Fecha de creación	:	21/02/2017
# Creado por 		:	Jhon Numpaque
################################################################################################################################
# Modificaciones
require_once('./jsonSistema/funcionCierre.php');
require_once('./jsonPptal/funcionesPptal.php');
 ?>
<div class="modal fade mdlConciliado" id="modalConciliado" role="dialog" align="center" >
    <div class="modal-dialog" style="width: 1000px;">
        <form action="" id="formM" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Listado de Conciliaciones</h4>
                    <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -50px">
                        <button type="button" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                    </div>
                </div>
                <div class="modal-body row">
                    
                    <?php 
                    #Libreria de conexion
                    require_once ('Conexion/conexion.php');
                    require_once ('Conexion/ConexionPDO.php');
                    $con = new ConexionPDO();
                    @session_start();
                    error_reporting(0);
                    $annov  = $_SESSION['anno'];
                    $compania  = $_SESSION['compania'];
                    $nannov = anno($annov);
                    #Año Anterior
                    $anno2 = $nannov-1;
                    $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
                    if(count($an2)>0){
                        $annova = $an2[0][0];
                    } else {
                        $annova = 0;
                    }
                    #Variables de cuenta y mes
                    $cuenta = "0";
                    $mes = "";
                    $mess = "";
                    $dato_mes = "0";
                    #Validación de POST
                    if(!empty($_POST['cuenta']) && !empty($_POST['mes'])){
                        #Carga de variables cuando los envios no son vacios
                        $cuenta = '"'.$mysqli->real_escape_string(''.$_POST['cuenta'].'').'"';
                        $mes = '"'.$mysqli->real_escape_string(''.$_POST['mes'].'').'"';
                        $dato_mes = $_POST['mes'];
                        
                        #*** Buscar Código Cuenta ***#
                        $cuentaA    = 0;
                        $annov      = $_SESSION['anno'];
                        $a          = 0;
                        while($a==0){
                            $nannov = anno($annov);
                            #Año Anterior
                            $anno2 = $nannov-1;
                            $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
                            #echo "SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2'";
                            if(count($an2)>0){ 
                                $annova = $an2[0][0];
                                $ca = $con->Listar("SELECT id_unico,codi_cuenta, equivalente_va FROM gf_cuenta WHERE id_unico = $cuenta");
                                $id_cuenta = $ca[0][0];
                                $codCuenta = $ca[0][1];
                                $equivalente =$ca[0][2];
                                if(!empty($equivalente)){
                                    #echo '1'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
                                    $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova");
                                    if(count($ctaa)>0){
                                        if(!empty($ctaa[0][0])){
                                            $cuentaA .= ','.$ctaa[0][0];
                                        }
                                    } else {
                                        #echo '2'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
                                        $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
                                        if(!empty($ctaa[0][0])){
                                            $cuentaA .= ','.$ctaa[0][0];
                                        }
                                    }
                                } else {
                                    #echo '3'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova";
                                    $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
                                    if(!empty($ctaa[0][0])){
                                            $cuentaA .= ','.$ctaa[0][0];
                                        }
                                }
                                $annov = $annova;
                            } else {
                                $a += 1;
                            }
                        }
                        #var_dump($cuentaA);
                        $cuentas =($cuentaA.','.$cuenta);
                        ######################################################################################################################
                        # Consulta de nombre de mes
                        #
                        ######################################################################################################################
                        #
                        $sqlM = "SELECT mes FROM gf_mes WHERE id_unico = $mes";
                        $resultM = $mysqli->query($sqlM);
                        $noM = mysqli_fetch_row($resultM);
                        ######################################################################################################################
                        # Array con los numeros de los meses
                        #
                        ######################################################################################################################
                        #
                        $meses = array( "Enero" => '01', "Febrero" => '02', "Marzo" => '03',"Abril" => '04', "Mayo" => '05', "Junio" => '06', "Julio" => '07', "Agosto" => '08', "Septiembre" => '09', "Octubre" => '10', "Noviembre" => '11', "Diciembre" => '12'); 
                        $mess = $meses[$noM[0]];
                    }
                     ?>
                    <div class="form-group form-inline">
                        <?php $cierre = cierrepartidames($mes);

                                                 if($cierre == 1){ }  else { ?>
                        <label class="control-label col-sm-2">Marcar todos :</label>
                        <div class="col-sm-1" style="width: 45px;margin-top: -5px">
                            <a class="btn btn-primary" id="btnMarcar" onclick="marcarCampos()" title="Desmarcar todas las conciliaciones" name="btn"><span class="glyphicon glyphicon-ok"></span></a>
                        </div>
                        <label class="col-sm-1"></label>
                        <label class="control-label col-sm-2">Desmarcar todos :</label>
                        <div class="col-sm-1" style="width: 45px;margin-top: -5px">
                            <a class="btn btn-primary" id="btnDesmarcar" onclick="desmarcarCampos()" title="Desmarcar todas las conciliaciones" name="btn"><span class="glyphicon glyphicon-remove"></span></a>
                        </div>
                                                 <?php } ?>
                    </div>
        			<!-- Tabla de listado  -->
        			<div class="table-responsive col-sm-12" style="margin-top: 5px">
    					<div class="table-responsive">
    						<table id="tablaConciliaciones" class="table table-striped table-condensed display detalle" cellpadding="0" width="100%">				
    							<!-- Cabeza de tabla -->
    							<thead>
    								<!-- Etiquetas de titulo -->
    								<tr>
    									<td class="cabeza"><strong>Tipo Movimiento</strong></td>
    									<td class="cabeza"><strong>Nro Movimiento</strong></td>
                                                                        <td class="cabeza"><strong>Fecha</strong></td>
    									<td class="cabeza"><strong>Tercero</strong></td>
    									<td class="cabeza"><strong>Descripción</strong></td>
    									<td class="cabeza"><strong>Nro Doc</strong></td>
    									<td class="cabeza"><strong>Débito</strong></td>
                                                                        <td class="cabeza"><strong>Crédito</strong></td>
    									<td class="cabeza" width="3%"></td>
    								</tr>
    								<!-- Campos de filtrado -->
    								<tr>
    									<th class="cabeza">Tipo Movimiento</th>
    									<th class="cabeza">Nro Movimiento</th>
                                                                        <th class="cabeza">Fecha</th>
    									<th class="cabeza">Tercero</th>
    									<th class="cabeza">Descripción</th>
    									<th class="cabeza">Nro Doc</th>
    									<th class="cabeza">Débito</th>
                                                                        <th class="cabeza">Crédito</th>
    									<th class="cabeza" width="3%"></th>
    								</tr>
    							</thead>
    							<!-- Cuerpo de tabla -->
    							<tbody>
    								<?php
                                    $descripcion ="";
                                    $calendario = CAL_GREGORIAN;
                                    $anno = $_SESSION['anno'];
                                    $sqlA = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $anno";
                                    $resultA = $mysqli->query($sqlA);
                                    $rowA = mysqli_fetch_row($resultA);
                                    $anno = $rowA[0];
                                    #Dia final del mes
                                    $diaF = cal_days_in_month($calendario, $mess , $anno); 
                                    $d = "'$anno-$mess-$diaF'";
                                    $month = $mess;
                                    $year = $anno;
                                    $e = date('Y-m-d', mktime(0,0,0, $month, 1, $year));  
                                    $sqlD="SELECT DISTINCT
                                        dtc.id_unico,
                                        CONCAT(tpc.sigla),
                                        cnt.numero,
                                        IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                                          OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                                        (tr.razonsocial),
                                        CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
                                        cnt.descripcion,
                                        dtc.valor,
                                        (dtc.valor),
                                        dtc.conciliado,
                                        DATE_FORMAT(cnt.fecha,
                                        '%d/%m/%Y'), 
                                        dtc.periodo_conciliado , 
                                        c.naturaleza , 
                                        cnt.id_unico 
                                      FROM
                                        gf_detalle_comprobante dtc
                                      LEFT JOIN
                                        gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico
                                      LEFT JOIN
                                        gf_tipo_comprobante tpc ON tpc.id_unico = cnt.tipocomprobante
                                      LEFT JOIN
                                        gf_tercero tr ON dtc.tercero = tr.id_unico 
                                      LEFT JOIN 
                                        gf_cuenta c On dtc.cuenta = c.id_unico 
                                      WHERE
                                      dtc.cuenta IN($cuentas)
                                      AND ( cnt.fecha BETWEEN '$e' AND $d)
                                      AND ( (dtc.conciliado IN(1,2) AND dtc.periodo_conciliado =$mes) OR dtc.conciliado IS NULL ) 
                                      AND tpc.clasecontable != 5 AND dtc.valor != 0
                                      ORDER BY
                                        cnt.fecha ASC";
                                    
                                        $resultD = $mysqli->query($sqlD);
                                        while ($r = mysqli_fetch_row($resultD)) {
                                                echo "<tr>";
                                                echo "<td class=\"text-left\" style=\"font-size:10px\">".ucwords(mb_strtoupper($r[1]))."</td>";
                                                echo "<td class=\"campos text-left\">".$r[2]."</td>";
                                                echo "<td class=\"text-left\" style=\"font-size:10px\">".ucwords(mb_strtolower($r[8]))."</td>";
                                                echo "<td class=\"text-left\" style=\"font-size:10px\">".ucwords(mb_strtolower($r[3]))."</td>";
                                                echo "<td class=\"text-left\" style=\"font-size:10px\">".ucwords(mb_strtolower($r[4]==''?'':$r[4]))."</td>";
                                                ##########BUSCAR CHEQUES###########
                                                $ch = "SELECT DISTINCT id_unico, numero "
                                                        . "FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $r[0]";
                                                $ch = $mysqli->query($ch);
                                                if(mysqli_num_rows($ch)>0){
                                                    echo "<td class=\"campos text-left\">";
                                                    while ($row = mysqli_fetch_row($ch)) {
                                                        echo $row[1].'&nbsp;&nbsp;&nbsp;';
                                                    }
                                                    echo "</td>";
                                                } else {
                                                echo "<td class=\"campos text-left\">".''."</td>";
                                                }
                                                switch ($r[10]){
                                                    case 1:
                                                        if($r[6]>0){
                                                            $debito = $r[6];
                                                            $credito =0;
                                                        } else {
                                                            $debito = 0;
                                                            $credito =$r[6]*-1;
                                                        }
                                                            
                                                    break;
                                                    case 2:
                                                        if($r[6]>0){
                                                            $debito =0;
                                                            $credito =$r[6];
                                                        } else {
                                                            $debito = $r[6]*-1;
                                                            $credito =0;
                                                        }
                                                    break;
                                                }
                                                echo "<td class=\"campos text-left\">".number_format($debito,2,',','.')."</td>";
                                                echo "<td class=\"campos text-left\">".number_format($credito,2,',','.')."</td>";
                                                echo "<td class=\"campos text-left\">";	
                                                
                                                if($r[9]!=""){ 
                                                //BUSCAR SI YA ESTA CERRADO 
                                                $cierre = cierrepartidames($mes);

                                                 if($cierre == 1){
                                                    switch ($r[7]) {
                                                         case 1: 
                                                                 echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\"  checked/>";
                                                                 break;

                                                         case 2:
                                                                 echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\" />";
                                                                 break;
                                                         default:
                                                                 echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\" />";
                                                                 break;
                                                     }  
                                                } else {
                                                   switch ($r[7]) {
                                                           case 1:
                                                                   echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" checked />";
                                                                   break;

                                                           case 2:
                                                                   echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" />";
                                                                   break;
                                                           default:
                                                                   echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" />";
                                                                   break;
                                                    }	

                                                }	
                                                } else {

                                                       $cierre = cierrepartidames($mes);

                                                 if($cierre == 1){
                                                    switch ($r[7]) {
                                                        case 1:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\" checked />";
                                                                break;

                                                        case 2:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\" />";
                                                                break;
                                                        default:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\" />";
                                                                break;
                                                    }  
                                                } else {
                                                   switch ($r[7]) {
                                                        case 1:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" checked />";
                                                                break;

                                                        case 2:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" />";
                                                                break;
                                                        default:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" />";
                                                                break;
                                                    }	

                                                }
                                                }
                                                echo "</td>";
                                                echo "</tr>";
                                        }
                                    $sqlD2="SELECT DISTINCT
                                        dtc.id_unico,
                                        CONCAT(tpc.sigla),
                                        cnt.numero,
                                        IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                                          OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                                        (tr.razonsocial),
                                        CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
                                        cnt.descripcion,
                                        dtc.valor,
                                        (dtc.valor),
                                        dtc.conciliado,
                                        DATE_FORMAT(cnt.fecha,
                                        '%d/%m/%Y'), 
                                        dtc.periodo_conciliado, 
                                        c.naturaleza 
                                      FROM
                                        gf_detalle_comprobante dtc
                                      LEFT JOIN
                                        gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico
                                      LEFT JOIN
                                        gf_tipo_comprobante tpc ON tpc.id_unico = cnt.tipocomprobante
                                      LEFT JOIN
                                        gf_tercero tr ON dtc.tercero = tr.id_unico
                                     LEFT JOIN 
                                        gf_cuenta c On dtc.cuenta = c.id_unico 
                                      WHERE
                                      dtc.cuenta IN($cuentas)
                                      AND ( cnt.fecha < '$e')
                                      AND ( dtc.conciliado IS NULL OR (dtc.conciliado IN(1,2) AND dtc.periodo_conciliado =$mes)) 
                                      AND tpc.clasecontable != 5 AND dtc.valor != 0
                                      ORDER BY
                                        cnt.fecha ASC";
                                        
                                     $resultD2 = $mysqli->query($sqlD2);
                                    while ($r = mysqli_fetch_row($resultD2)) {
                                            echo "<tr>";
                                            echo "<td class=\"text-left\" style=\"font-size:10px\">".ucwords(mb_strtoupper($r[1]))."</td>";
                                            echo "<td class=\"campos text-left\">".$r[2]."</td>";
                                            echo "<td class=\"text-left\" style=\"font-size:10px\">".ucwords(mb_strtolower($r[8]))."</td>";
                                            echo "<td class=\"text-left\" style=\"font-size:10px\">".ucwords(mb_strtolower($r[3]))."</td>";
                                            echo "<td class=\"text-left\" style=\"font-size:10px\">".ucwords(mb_strtolower($r[4]==''?'':$r[4]))."</td>";
                                            ##########BUSCAR CHEQUES###########
                                                $ch = "SELECT DISTINCT id_unico, numero "
                                                        . "FROM gf_detalle_comprobante_mov WHERE comprobantecnt = $r[0]";
                                                $ch = $mysqli->query($ch);
                                                if(mysqli_num_rows($ch)>0){
                                                    echo "<td class=\"campos text-left\">";
                                                    while ($row = mysqli_fetch_row($ch)) {
                                                        echo $row[1].'&nbsp;&nbsp;&nbsp;';
                                                    }
                                                    echo "</td>";
                                                } else {
                                                echo "<td class=\"campos text-left\">".''."</td>";
                                                }
                                            switch ($r[10]){
                                                    case 1:
                                                        if($r[6]>0){
                                                            $debito = $r[6];
                                                            $credito =0;
                                                        } else {
                                                            $debito = 0;
                                                            $credito =$r[6]*-1;
                                                        }
                                                            
                                                    break;
                                                    case 2:
                                                        if($r[6]>0){
                                                            $debito =0;
                                                            $credito =$r[6];
                                                        } else {
                                                            $debito = $r[6]*-1;
                                                            $credito =0;
                                                        }
                                                    break;
                                                }
                                                echo "<td class=\"campos text-left\">".number_format($debito,2,',','.')."</td>";
                                                echo "<td class=\"campos text-left\">".number_format($credito,2,',','.')."</td>";
                                            
                                            echo "<td class=\"campos text-left\">";									
                                            
                                            if($r[9]!=""){ 
                                                //BUSCAR SI YA ESTA CERRADO 
                                                 $cierre = cierrepartidames($mes);

                                                 if($cierre == 1){
                                                    switch ($r[7]) {
                                                         case 1: 
                                                                 echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\"  checked/>";
                                                                 break;

                                                         case 2:
                                                                 echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\" />";
                                                                 break;
                                                         default:
                                                                 echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\" />";
                                                                 break;
                                                     }  
                                                } else {
                                                   switch ($r[7]) {
                                                           case 1:
                                                                   echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" checked />";
                                                                   break;

                                                           case 2:
                                                                   echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" />";
                                                                   break;
                                                           default:
                                                                   echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" />";
                                                                   break;
                                                    }	

                                                }	
                                                } else {
                                                    $cierre = cierrepartidames($mes);

                                                 if($cierre == 1){
                                                    switch ($r[7]) {
                                                        case 1:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\" checked />";
                                                                break;

                                                        case 2:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\" />";
                                                                break;
                                                        default:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" disabled=\"true\" value=\"1\" />";
                                                                break;
                                                    }  
                                                } else {
                                                   switch ($r[7]) {
                                                        case 1:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" checked />";
                                                                break;

                                                        case 2:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" />";
                                                                break;
                                                        default:
                                                                echo "<input type=\"checkbox\" name=\"chkP".$r[0]."\" onclick=\"return conciliado(".$r[0].")\" id=\"chkP".$r[0]."\" class=\"campos\" title=\"Indicador de pago\" value=\"1\" />";
                                                                break;
                                                    }	

                                                }
                                                    
                                                }									
                                            echo "</td>";
                                            echo "</tr>";
                                    }
    								?>
    							</tbody>
    						</table>
    					</div>
        			</div>
        		</div>
                
        	</div>
        </form>
    </div>
</div>

<style>
	/*Estilos de tabla*/
	table.dataTable thead th,table.dataTable thead td{
		padding:1px 18px;
		font-size:10px
	}
	table.dataTable tbody td,table.dataTable tbody td{
		padding:1px
	}
	.dataTables_wrapper .ui-toolbar{
		padding:2px;
		font-size: 10px;
    	font-family: Arial;
    }
</style>
<script type="text/javascript">
	//Función para adaptar tabla a modal
    $("#modalConciliado").on('shown.bs.modal',function(){
        var dataTable = $("#tablaConciliaciones").DataTable();
        dataTable.columns.adjust().responsive.recalc();
    });
    //Función de cargado y estilo de la tabla
    $(document).ready(function() {
        var i= 0;
        $('#tablaConciliaciones thead th').each( function () {
            if(i >= 0){ 
        	    var title = $(this).text();
            	switch (i){
                	case 0:
                    	$(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                	break;
                	case 1:
                    	$(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                	break;
                	case 2:
                    	$(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                	break;
	                case 3:
	                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
	                break;
	                case 4:
	                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
	                break;           
	                case 5:
	                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
	                break;
                    case 6:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;
                    case 7:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;
                    case 8:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                    break;
              	}
              	i = i+1;
            }else{
              i = i+1;
            }
        });    
        // DataTable
        var table = $('#tablaConciliaciones').DataTable({
            "autoFill": true,
            "scrollX": true,
            "pageLength": 50,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No Existen Registros...",
                "info": "Página _PAGE_ de _PAGES_ ",
                "infoEmpty": "No existen datos",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
             },
            "order": [],
            "columnDefs": [ {
              "targets"  : 'no-sort',
              "orderable": false,
            }]
        });
        //Consulta en inputs cabeza
        var i = 0;
        table.columns().every( function () {
            var that = this;
            if(i !==0 ){
                $( 'input' , this.header() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search(this.value)
                            .draw();
                            
                    }
                });
              	i = i+1;
            }else{
                i = i+1;
            }
        });
    });                 
    //Función para marcar y consultar
    function marcarCampos(){
        // DataTable
        $("#tablaConciliaciones").dataTable().fnDestroy();
        var table = $('#tablaConciliaciones').DataTable({
            "autoFill": true,
            "scrollX": true,
            "pageLength": 50,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No Existen Registros...",
                "info": "Página _PAGE_ de _PAGES_ ",
                "infoEmpty": "No existen datos",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
             },
            "order": [],
            "columnDefs": [ {
              "targets"  : 'no-sort',
              "orderable": false,
            }],
            'ajax':{
                'url':'consultasBasicas/consultasListados.php?listado=1&mes=<?php echo $dato_mes ?>&cuenta=<?php echo $cuenta ?>',
                'dataSrc':'data'
            },
            'columns':[
                {data:'Tipo Movimiento'},
                {data:'Nro Movimiento'},
                {data:'Fecha'},
                {data:'Tercero'},                
                {data:'Descripción'},
                {data:'Nro Doc'},
                {data:'Débito'},
                {data:'Crédito'},
                {data:' '},
            ]
        });
        //Consulta en inputs cabeza
        var i = 0;
        table.columns().every( function () {
            var that = this;
            if(i !==0 ){
                $( 'input' , this.header() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search(this.value)
                            .draw();
                    
                     
                    }
                });
                i = i+1;
            }else{
                i = i+1;
            }
        });
    }
    //Función para desmarcar y consultar
    function desmarcarCampos(){
        // DataTable
        $("#tablaConciliaciones").dataTable().fnDestroy();
        var table = $('#tablaConciliaciones').DataTable({
            "autoFill": true,
            "scrollX": true,
            "pageLength": 50,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No Existen Registros...",
                "info": "Página _PAGE_ de _PAGES_ ",
                "infoEmpty": "No existen datos",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
             },
            "order": [],
            "columnDefs": [ {
              "targets"  : 'no-sort',
              "orderable": false,
            }],
            'ajax':{
                'url':'consultasBasicas/consultasListados.php?listado=2&mes=<?php echo $dato_mes ?>&cuenta=<?php echo $cuenta ?>',
                'dataSrc':'data'
            },
            'columns':[
                {data:'Tipo Movimiento'},
                {data:'Nro Movimiento'},
                {data:'Fecha'},
                {data:'Tercero'},                
                {data:'Descripción'},
                {data:'Nro Doc'},
                {data:'Débito'},
                {data:'Crédito'},
                {data:' '},
            ]
        });
        //Consulta en inputs cabeza
        var i = 0;
        table.columns().every( function () {
            var that = this;
            if(i !==0 ){
                $( 'input' , this.header() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search(this.value)
                            .draw();
                            
                    }
                });
                i = i+1;
            }else{
                i = i+1;
            }
        });
    }
    //Función para guardar los datos
    function actualizarModal(){
        //Serialización del formulario
        var form = $("#formM").serialize();        
        //Validación de la variable
        if(form.length > 0){
            //Captura de valores
            var form_data = {
                existente:39,
                form:form,
                mes:<?php echo $dato_mes ?>,
                cuenta:<?php echo $cuenta ?>
            };
            //Envio ajax
            var result = '';
            $.ajax({
                type:'POST',
                url:'consultasBasicas/consultarNumeros.php',
                data:form_data,
                success: function(data){
                    
                    //Captura del valor retornado
                    result = JSON.parse(data);
                    //Validación del resultado deveulto
                    if(result==true){
                        //Llamdao de modales
                        $("#modalConciliado").modal('hide');
                        $("#modalGuardado").modal('show');
                    }else{
                        //Llamdao de modales
                        $("#modalConciliado").modal('hide');
                        $("#modalNoGuardo").modal('show');
                    }
                }
            });
        }
    }
    //Función de conciliación
    function conciliado(id){
        //Captura de valor de campo
        var optCon = "#chk"+id;
        //Captura de variables
        var form_data = {
            existente:39,
            id:id,
            mes:<?php echo $dato_mes ?>
        };
        //Envio ajax
        $.ajax({
            type:'POST',
            url:'consultasBasicas/consultarNumeros.php',
            data:form_data,
            success: function(data){
                
            }
        });        
    }  
</script>
<style>
	/*Estilos tabla*/
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
        font-family: Arial;}
</style>