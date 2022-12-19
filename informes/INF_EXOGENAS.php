<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#18/05/2018 | Erica G. | Formato 1001 Cuando Se Aplica Retención En El Egreso
#17/05/2018 | Erica G. | Formato 1008,1009,1011 Incluir SaldoInicial
#07/05/2018 | Erica G. | Formato 1009, 1005, 1006, 1011
#03/05/2018 | Erica G. | Formato 1001 Acumulado o Detallado
#26/04/2018 | Erica G. | Formato 1001
#18/04/2018 | Erica G. | Formato 1008
#16/04/2018 | Erica G. | Formato 1007
####/################################################################################
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();

#**********Recepción Variables ****************#
$anno       = $_REQUEST['anno'];
$formato    = $_REQUEST['formato'];
$exportar   = $_REQUEST['sltExportar'];
$separador  = $_REQUEST['separador'];
if($separador == 'tab') {	
    $separador = "\t";		
}
#   **** Buscar Código Formato ****    #
$cf = $con->Listar("SELECT formato, cuantia FROM gf_formatos_exogenas WHERE id_unico =$formato");
$codigo_formato = $cf[0][0];
$cuantia        = $cf[0][1];
$html   ="";
$htmle  ="";
switch ($codigo_formato){
    case(1007):
        #   *****   Buscar Conceptos  ****   #
        $rowc = $con->Listar("SELECT DISTINCT 
            ce.concepto_exogenas, cn.codigo , GROUP_CONCAT(ce.cuenta) 
            FROM gf_configuracion_exogenas ce 
            LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
            WHERE cn.formato =$formato 
            GROUP BY ce.concepto_exogenas" );
        
        for ($i = 0; $i < count($rowc); $i++) {
            $concepto = $rowc[$i][0];
            $codigo   = $rowc[$i][1];
            $cuentas  = $rowc[$i][2];
            $valor_cuantiai =0;
            $valor_cuantiad =0;
            #   ****    Buscar Terceros     ****    #
            $rowt = $con->Listar("SELECT DISTINCT 
                dc.tercero 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico  
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                WHERE dc.cuenta IN ($cuentas) AND cn.parametrizacionanno = $anno 
                AND (tc.clasecontable !=5 AND tc.clasecontable !=20)");
            if(count($rowt)>0){
                for ($z = 0; $z < count($rowt); $z++) {
                    if(!empty($rowt[$z][0])){
                        $imprimir   = 0;
                        $ingresos   = valor($cuentas, $rowt[$z][0], '+');
                        $descuentos = valor($cuentas, $rowt[$z][0], '-');
                        if($descuentos<0){
                            $descuentos = $descuentos*-1;
                        }
                        if(!empty($cuantia)){ 
                            if($ingresos<=$cuantia || $rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantiai +=$ingresos;
                                $valor_cuantiad +=$descuentos;
                            }
                        } else {
                            if($rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantiai +=$ingresos;
                                $valor_cuantiad +=$descuentos;
                            }
                        }
                        if($imprimir==0) {
                            if($ingresos != 0 || $descuentos !=0) { 
                                # *** Buscar Datos Tercero *** #
                                $dter = $con->Listar("SELECT DISTINCT 
                                    t.id_unico, t.nombreuno,
                                    t.nombredos,  t.apellidouno,
                                    t.apellidodos, t.razonsocial, 
                                    t.digitoverficacion, ti.codigo, 
                                    t.numeroidentificacion, 
                                    GROUP_CONCAT(d.direccion),
                                    ci.rss, dp.rss 
                                    FROM gf_tercero t 
                                    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                                    LEFT JOIN gf_direccion d ON d.tercero = t.id_unico 
                                    LEFT JOIN gf_ciudad ci ON d.ciudad_direccion = ci.id_unico 
                                    LEFT JOIN gf_departamento dp ON ci.departamento = dp.id_unico 
                                    WHERE t.id_unico = ".$rowt[$z][0]); 
                                if($exportar==3){
                                    $html .='<tr>';
                                    $html .='<td>'.$codigo.'</td>';
                                    $html .='<td>'.$dter[0][7].'</td>';
                                    $html .='<td>'.$dter[0][8].'</td>';
                                    $html .='<td>'.$dter[0][3].'</td>';
                                    $html .='<td>'.$dter[0][4].'</td>';
                                    $html .='<td>'.$dter[0][1].'</td>';
                                    $html .='<td>'.$dter[0][2].'</td>';
                                    $html .='<td>'.$dter[0][5].'</td>';
                                    $html .='<td>169</td>';
                                    $html .='<td>'.round($ingresos,0).'</td>';
                                    $html .='<td>'.round($descuentos, 0).'</td>';
                                    $html .='</tr>';
                                } else {
                                    $html .=str_replace(',',' ',$codigo)."$separador";
                                    $html .=str_replace(',',' ',$dter[0][7])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][8])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][3])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][4])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][1])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][2])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][5])."$separador";
                                    $html .='169'."$separador";
                                    $html .=round($ingresos,0)."$separador";
                                    $html .=round($descuentos, 0);
                                    $html .= "\n";
                                }
                            }    
                        }
                    }
                }
            }
            if($valor_cuantiad!=0 || $valor_cuantiai !=0){
                if($exportar==3){
                    $html .='<tr>';
                    $html .='<td>'.$codigo.'</td>';
                    $html .='<td>43</td>';
                    $html .='<td>222222222</td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td>Cuantías menores</td>';
                    $html .='<td>169</td>';
                    $html .='<td>'.round($valor_cuantiai, 0).'</td>';
                    $html .='<td>'.round($valor_cuantiad, 0).'</td>';
                    $html .='</tr>';
                } else {
                    $html .=str_replace(',',' ',$codigo)."$separador";
                    $html .='43'."$separador";
                    $html .='222222222'."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .='Cuantías menores'."$separador";
                    $html .='169'."$separador";
                    $html .=round($valor_cuantiai, 0)."$separador";
                    $html .=round($valor_cuantiad, 0);
                    $html .= "\n";
                }
            }
        }
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>CONCEPTO</strong></center></td>';
            $h  .='<td><center><strong>TIPO DE DOCUMENTO</strong></center></td>';
            $h  .='<td><center><strong>NÚMERO DE IDENTIFICACIÓN DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>RAZÓN SOCIAL DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>PAIS DE RESIDENCIA O DOMICILIO</strong></center></td>';
            $h  .='<td><center><strong>INGRESOS BRUTOS RECIBIDOS POR OPERACIONES PROPIAS</strong></center></td>';
            $h  .='<td><center><strong>DEVOLUCIONES, REBAJAS Y DESCUENTOS</strong></center></td>';
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
        
    break;
    case(1008):
        #   *****   Buscar Conceptos  ****   #
        $rowc = $con->Listar("SELECT DISTINCT 
            ce.concepto_exogenas, cn.codigo , GROUP_CONCAT(ce.cuenta) 
            FROM gf_configuracion_exogenas ce 
            LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
            WHERE cn.formato =$formato 
            GROUP BY ce.concepto_exogenas" );
        $imprimir = 0;
        for ($i = 0; $i < count($rowc); $i++) {
            $concepto = $rowc[$i][0];
            $codigo   = $rowc[$i][1];
            $cuentas  = $rowc[$i][2];
            $valor_cuantia =0;
            #   ****    Buscar Terceros     ****    #
            $rowt = $con->Listar("SELECT DISTINCT 
                dc.tercero 
                FROM gf_detalle_comprobante dc 
                WHERE dc.cuenta IN ($cuentas) 
                ORDER BY dc.tercero ASC ");
            if(count($rowt)>0){
                for ($z = 0; $z < count($rowt); $z++) { 
                    if(!empty($rowt[$z][0])){
                        $imprimir   = 0;
                        $suman      = valorPI($cuentas, $rowt[$z][0], '+');
                        $restan     = valorPI($cuentas, $rowt[$z][0], '-');
                        $total      = $suman - ($restan*-1);
                        if($codigo=='1318'){
                            if($total <0){
                                $total = $total *-1;
                            }
                        }
                        if(empty($cuantia)){ 
                            $cuantia = 0;                            
                        }
                        if($total <= $cuantia || $rowt[$z][0]=='2'){
                            $imprimir = 1;
                            $valor_cuantia +=$total;
                        } 
                        
                        if($imprimir==0) {
                            if($total >= $cuantia) { 
                                $dter = $con->Listar("SELECT DISTINCT 
                                    t.id_unico, t.nombreuno,
                                    t.nombredos,  t.apellidouno,
                                    t.apellidodos, t.razonsocial, 
                                    t.digitoverficacion, ti.codigo, 
                                    t.numeroidentificacion, 
                                    GROUP_CONCAT(d.direccion),
                                    ci.rss, dp.rss 
                                    FROM gf_tercero t 
                                    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                                    LEFT JOIN gf_direccion d ON d.tercero = t.id_unico 
                                    LEFT JOIN gf_ciudad ci ON d.ciudad_direccion = ci.id_unico 
                                    LEFT JOIN gf_departamento dp ON ci.departamento = dp.id_unico 
                                    WHERE t.id_unico = ".$rowt[$z][0]); 
                                if($exportar==3){
                                    $html .='<tr>';
                                    $html .='<td>'.$codigo.'</td>';
                                    $html .='<td>'.$dter[0][7].'</td>';
                                    $html .='<td>'.$dter[0][8].'</td>';
                                    $html .='<td>'.$dter[0][6].'</td>';
                                    $html .='<td>'.$dter[0][3].'</td>';
                                    $html .='<td>'.$dter[0][4].'</td>';
                                    $html .='<td>'.$dter[0][1].'</td>';
                                    $html .='<td>'.$dter[0][2].'</td>';
                                    $html .='<td>'.$dter[0][5].'</td>';
                                    $html .='<td>'.$dter[0][9].'</td>';
                                    $html .='<td>'.$dter[0][11].'</td>';
                                    $html .="<td style='mso-number-format:\@'>".$dter[0][10]."</td>";                                    
                                    $html .='<td>169</td>';
                                    $html .='<td>'.round($total, 0).'</td>';
                                    $html .='</tr>';
                                } else {
                                    $html .=str_replace(',',' ',$codigo)."$separador";
                                    $html .=str_replace(',',' ',$dter[0][7])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][8])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][6])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][3])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][4])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][1])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][2])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][5])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][9])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][11])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][10])."$separador";                                    
                                    $html .='169'."$separador";
                                    $html .=round($total, 0);
                                    $html .= "\n";
                                }
                            }
                        }
                    }
                }
            }
            if($valor_cuantia!=0){
                if($exportar==3){
                    $html .='<tr>';
                    $html .='<td>'.$codigo.'</td>';
                    $html .='<td>43</td>';
                    $html .='<td>222222222</td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td>Cuantías menores</td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td>169</td>';
                    $html .='<td>'.round($valor_cuantia, 0).'</td>';
                    $html .='</tr>';
                } else {
                    $html .=str_replace(',',' ',$codigo)."$separador";
                    $html .='43'."$separador";
                    $html .='222222222'."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .='Cuantías menores'."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .='169'."$separador";
                    $html .=round($valor_cuantia, 0);
                    $html .= "\n";
                }
            }
        }
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>CONCEPTO</strong></center></td>';
            $h  .='<td><center><strong>TIPO DE DOCUMENTO</strong></center></td>';
            $h  .='<td><center><strong>NÚMERO DE IDENTIFICACIÓN DEL DEUDOR</strong></center></td>';
            $h  .='<td><center><strong>DV</strong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO DEL DEUDOR</strong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO DEL DEUDOR</strong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE DEL DEUDOR</strong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES DEL DEUDOR</strong></center></td>';
            $h  .='<td><center><strong>RAZÓN SOCIAL DEL DEUDOR</strong></center></td>';
            $h  .='<td><center><strong>DIRECCION</strong></center></td>';
            $h  .='<td><center><strong>CÓDIGO DPTO</strong></center></td>';
            $h  .='<td><center><strong>CÓDIGO MCP</strong></center></td>';
            $h  .='<td><center><strong>PAIS DE RESIDENCIA O DOMICILIO</strong></center></td>';
            $h  .='<td><center><strong>SALDO CUENTAS POR COBRAR AL 31-12</strong></center></td>';
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
        
    break;
    case(1001):
        #   *****   Buscar Clase Comprobantes Donde Aplica Retención  ****   #
        $rt = $con->Listar("SELECT DISTINCT clasecontable FROM gf_tipo_comprobante WHERE retencion = 1");
        $rt = $rt[0][0];
        #   *****   Buscar Conceptos  ****   #
        $rowc = $con->Listar("SELECT DISTINCT 
            ce.concepto_exogenas, cn.codigo , GROUP_CONCAT(ce.cuenta) 
            FROM gf_configuracion_exogenas ce 
            LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
            WHERE cn.formato =$formato 
            GROUP BY ce.concepto_exogenas" );
        $h   =   "";
        if($_REQUEST['informe']==2){
            #***********************************************************************#
            #          ***************     Detallado           ***************      #
            #***********************************************************************#
            #   *****   Titulos     ****    #
            $h  .='<tr>';
            $h  .='<td><center><strong>COMPROBANTE</strong></center></td>';
            $h  .='<td><center><strong>CONCEPTO</strong></center></td>';
            $h  .='<td><center><strong>TIPO DE DOCUMENTO</strong></center></td>';
            $h  .='<td><center><strong>NÚMERO DE IDENTIFICACIÓN</strong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>RAZÓN SOCIAL DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>DIRECCION</strong></center></td>';
            $h  .='<td><center><strong>CÓDIGO DPTO</strong></center></td>';
            $h  .='<td><center><strong>CÓDIGO MCP</strong></center></td>';
            $h  .='<td><center><strong>PAIS DE RESIDENCIA O DOMICILIO</strong></center></td>';
            $h  .='<td><center><strong>PAGO O ABONO EN CUENTA DEDUCIBLE</strong></center></td>';
            $h  .='<td><center><strong>PAGO O ABONO EN CUENTA NO DEDUCIBLE</strong></center></td>';
            $h  .='<td><center><strong>IVA MAYOR VALOR DEL COSTO O GASTO DEDUCIBLE</strong></center></td>';
            $h  .='<td><center><strong>IVA MAYOR VALOR DEL COSTO O GASTO NO DEDUCIBLE</strong></center></td>';
            $h  .='<td><center><strong>RETENCIÓN EN LA FUENTE PRACTICADA RENTA</strong></center></td>';
            $h  .='<td><center><strong>RENTENCIÓN EN LA FUENTE ASUMIDA RENTA</strong></center></td>';
            $h  .='<td><center><strong>RETENCIÓN EN LA FUENTE PRACTICADA IVA RÉGIMEN COMÚN</strong></center></td>';
            $h  .='<td><center><strong>RETENCIÓN EN LA FUENTE PRACTICADA IVA NO DOMICILIARIOS</strong></center></td>';
            $h  .='</tr>';
            for ($i = 0; $i < count($rowc); $i++) {
                $concepto = $rowc[$i][0];
                $codigo   = $rowc[$i][1];
                $cuentas  = $rowc[$i][2];
                #   ****    Buscar Terceros     ****    #
                $rowt = $con->Listar("SELECT DISTINCT 
                    dc.tercero 
                    FROM gf_detalle_comprobante dc 
                    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = dc.comprobante 
                    LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante 
                    WHERE dc.cuenta IN ($cuentas) AND cn.parametrizacionanno = $anno 
                    AND (tc.clasecontable !=5 AND tc.clasecontable !=20) ");
                if(count($rowt)>0){
                    $arrayt = array();
                    for ($z = 0; $z < count($rowt); $z++) { 
                        $totalt =0;
                        $imprimir = 0;
                        if(!empty($rowt[$z][0])){
                            #Acumular Valor
                            $tercero = $rowt[$z][0];
                            $com = $con->Listar("SELECT DISTINCT 
                                cn.id_unico, cn.numero, tc.sigla, 
                                tc.nombre, tc.clasecontable, dc.valor, dc.id_unico   
                                FROM gf_detalle_comprobante dc 
                                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante
                                WHERE cn.parametrizacionanno = $anno 
                                AND dc.tercero = $tercero 
                                AND dc.cuenta IN ($cuentas) 
                                AND (tc.clasecontable !=5 AND tc.clasecontable !=20) 
                                ORDER BY cn.numero, cn.tipocomprobante");
                            
                            for($j = 0; $j <count($com); $j++){
                                $id_comprobante = $com[$j][0];
                                $totalt +=$com[$j][5];
                                  
                            }
                            if(!empty($cuantia)){ 
                                if($totalt<=$cuantia || $tercero=='2'){
                                    if(in_array($tercero, $arrayt)) {
                                    } else {
                                        array_push ( $arrayt , $tercero );
                                    }
                                } else {
                                    $imprimir = 1;
                                }

                            } else {
                                if($tercero=='2'){
                                    if(in_array($tercero, $arrayt)) {
                                    } else {
                                        array_push ( $arrayt , $tercero );
                                    }
                                } else {
                                    $imprimir = 1;
                                }
                            }
                            if($imprimir ==1){
                                #***** Buscar Comprobantes *****#
                                $com = $con->Listar("SELECT DISTINCT 
                                    cn.id_unico, cn.numero, tc.sigla, 
                                    tc.nombre, tc.clasecontable, dc.valor, dc.id_unico   
                                    FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                    LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante
                                    WHERE cn.parametrizacionanno = $anno 
                                    AND dc.tercero = $tercero 
                                    AND dc.cuenta IN ($cuentas) 
                                    AND (tc.clasecontable !=5 AND tc.clasecontable !=20) 
                                    ORDER BY cn.numero, cn.tipocomprobante");
                                for($j = 0; $j <count($com); $j++){
                                $id_comprobante = $com[$j][0];
                                $html.= '<tr>';
                                $html.= '<td>'.$com[$j][2].' - '.$com[$j][1].'</td>';
                                $html.= '<td>'.$codigo.'</td>';
                                #   *******     Buscar Datos Tercero    *******     #
                                $dt = $con->Listar("SELECT DISTINCT 
                                    t.id_unico, t.nombreuno,
                                    t.nombredos,  t.apellidouno,
                                    t.apellidodos, t.razonsocial, 
                                    t.digitoverficacion, ti.codigo, 
                                    t.numeroidentificacion, 
                                    GROUP_CONCAT(d.direccion),
                                    ci.rss, dp.rss 
                                    FROM gf_tercero t 
                                    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                                    LEFT JOIN gf_direccion d ON d.tercero = t.id_unico 
                                    LEFT JOIN gf_ciudad ci ON d.ciudad_direccion = ci.id_unico 
                                    LEFT JOIN gf_departamento dp ON ci.departamento = dp.id_unico 
                                    WHERE t.id_unico = $tercero");
                               
                                $html.= '<td>'.$dt[0][7].'</td>';
                                $html.= '<td>'.$dt[0][8].'</td>';
                                $html.= '<td>'.$dt[0][3].'</td>';
                                $html.= '<td>'.$dt[0][4].'</td>';
                                $html.= '<td>'.$dt[0][1].'</td>';
                                $html.= '<td>'.$dt[0][2].'</td>';
                                $html.= '<td>'.$dt[0][5].'</td>';
                                $html.= '<td>'.$dt[0][9].'</td>';
                                $html.= '<td>'.$dt[0][11].'</td>';
                                $html.= '<td>'.$dt[0][10].'</td>';                                
                                $html.= '<td>169</td>';
                                $html.= '<td>'. round($com[$j][5], 0).'</td>';
                                $html.= '<td>0.00</td>';
                                $rte  =0;
                                $im   =0;
                                if(($com[$j][4]==$rt) || ($com[$j][4]==13 && $rt==14) ){
                                    #   Validar Si El Valor Es El Mayor
                                    $vm = $con ->Listar("SELECT MAX(if(valor<0, valor*-1, valor))  
                                            FROM gf_detalle_comprobante WHERE cuenta IN($cuentas) 
                                            AND comprobante = $id_comprobante AND tercero = $tercero");
                                    $vm = $vm[0][0];
                                    if($com[$j][5]==$vm){
                                        # Validar Cuantos Detalles hay con ese valor 
                                        $idm = $con ->Listar("SELECT * 
                                                FROM gf_detalle_comprobante WHERE cuenta IN($cuentas) 
                                                AND comprobante = $id_comprobante AND tercero = $tercero 
                                                AND valor = $vm");
                                        if(count($idm)>0){  
                                            $idm = $idm[0][0];
                                            if($com[$j][6]==$idm){
                                                $im = 1;
                                            } else {
                                                $rte = 1;
                                            }
                                        } else {
                                            $im = 1;
                                        }
                                    } else {
                                       $rte  = 1; 
                                    }
                                } else {
                                    $im = 1;
                                }
                                if($im==1){
                                    if($com[$j][4]==13 && $rt==14){
                                        #**** Buscar Comprobantes Que Afectan La Cuenta Por Pagar ****#
                                        $afe = $con->Listar("SELECT DISTINCT cne.id_unico FROM gf_detalle_comprobante dc 
                                            LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico 
                                            LEFT JOIN gf_detalle_comprobante_pptal dce ON dce.comprobanteafectado = dcp.id_unico 
                                            LEFT JOIN gf_comprobante_pptal cpe ON dce.comprobantepptal = cpe.id_unico 
                                            LEFT JOIN gf_tipo_comprobante_pptal tce ON cpe.tipocomprobante = tce.id_unico 
                                            LEFT JOIN gf_tipo_comprobante tcne ON tce.id_unico = tcne.comprobante_pptal
                                            LEFT JOIN gf_comprobante_cnt cne ON tcne.id_unico = cne.tipocomprobante AND cpe.numero = cne.numero 
                                            WHERE dc.comprobante = $id_comprobante");
                                        if(count($afe)>0){
                                            $comprobantes ="";
                                            $cont = count($afe);
                                            for ($e = 0; $e < count($afe); $e++) {
                                                #Id Egreso
                                                $id_egr = $afe[$e][0];
                                                #Buscar Cuentas Por Pagar Que Lo Afectan Que Sean Diferentes A CXP
                                                $cxaf = $con->Listar("SELECT DISTINCT cxcnt.id_unico 
                                                        FROM gf_detalle_comprobante dc 
                                                        LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                                        LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                                        LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = cn.numero 
                                                        LEFT JOIN gf_detalle_comprobante_pptal dp ON cp.id_unico = dp.comprobantepptal 
                                                        LEFT JOIN gf_detalle_comprobante_pptal dpc ON dp.comprobanteafectado = dpc.id_unico 
                                                        LEFT JOIN gf_comprobante_pptal cxp ON dpc.comprobantepptal = cxp.id_unico 
                                                        LEFT JOIN gf_tipo_comprobante_pptal tcxp ON cxp.tipocomprobante = tcxp.id_unico 
                                                        LEFT JOIN gf_tipo_comprobante tccxp ON tccxp.comprobante_pptal = tcxp.id_unico 
                                                        LEFT JOIN gf_comprobante_cnt cxcnt ON cxcnt.tipocomprobante = tccxp.id_unico AND cxp.numero = cxcnt.numero
                                                        WHERE cn.id_unico = $id_egr ORDER BY cxcnt.fecha desc");
                                                if(count($cxaf)>0){
                                                    
                                                    $id_com = $cxaf[0][0];
                                                    if($id_com==$id_comprobante){
                                                        $comprobantes .=$id_egr.',';
                                                    }
                                                }
                                            }
                                            $comprobantes = substr($comprobantes, 0, -1);
                                            #   *******     Buscar Si Tiene Retenciones Aplicadas   *******    #
                                            #   Iva
                                            $brI = $con->Listar("SELECT SUM(r.valorretencion),
                                                SUM(r.retencionbase), tr.claseretencion 
                                                FROM gf_retencion r 
                                                LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                                WHERE r.comprobante IN($comprobantes) AND (tr.claseretencion = 4) 
                                                GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                            #Base Iva
                                            if(count($brI)>0){
                                                $html.= '<td>'. round($brI[0][1], 0).'</td>';
                                            } else {
                                                $html.= '<td>0.00</td>';
                                            }
                                            $html.= '<td>0.00</td>';
                                            #   RETEFTE
                                            $br = $con->Listar("SELECT SUM(r.valorretencion),
                                                SUM(r.retencionbase), tr.claseretencion 
                                                FROM gf_retencion r 
                                                LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                                WHERE r.comprobante IN($comprobantes) AND (tr.claseretencion = 3) 
                                                GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                            if(count($br)>0){
                                                $html.= '<td>'. round($br[0][0],0).'</td>';
                                            } else {
                                                $html.= '<td>0.00</td>';
                                            }
                                            $html.= '<td>0.00</td>';
                                            #Valor Iva
                                            if(count($brI)>0){
                                                $html.= '<td>'. round($brI[0][0], 0) .'</td>';
                                            } else {
                                                $html.= '<td>0.00</td>';
                                            }
                                        } else {
                                            $html.= '<td>0.00</td>';
                                            $html.= '<td>0.00</td>';
                                            $html.= '<td>0.00</td>';
                                            $html.= '<td>0.00</td>';
                                            $html.= '<td>0.00</td>';
                                        }
                                    } else {
                                        #   *******     Buscar Si Tiene Retenciones Aplicadas   *******    #
                                        #   Iva
                                        $brI = $con->Listar("SELECT SUM(r.valorretencion),
                                            SUM(r.retencionbase), tr.claseretencion 
                                            FROM gf_retencion r 
                                            LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                            WHERE r.comprobante = $id_comprobante AND (tr.claseretencion = 4) 
                                            GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                        #Base Iva
                                        if(count($brI)>0){
                                            $html.= '<td>'. round($brI[0][1], 0).'</td>';
                                        } else {
                                            $html.= '<td>0.00</td>';
                                        }
                                        $html.= '<td>0.00</td>';
                                        #   RETEFTE
                                        $br = $con->Listar("SELECT SUM(r.valorretencion),
                                            SUM(r.retencionbase), tr.claseretencion 
                                            FROM gf_retencion r 
                                            LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                            WHERE r.comprobante = $id_comprobante AND (tr.claseretencion = 3) 
                                            GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                        if(count($br)>0){
                                            $html.= '<td>'. round($br[0][0], 0).'</td>';
                                        } else {
                                            $html.= '<td>0.00</td>';
                                        }
                                        $html.= '<td>0.00</td>';
                                        #Valor Iva
                                        if(count($brI)>0){
                                            $html.= '<td>'. round($brI[0][0], 0).'</td>';
                                        } else {
                                            $html.= '<td>0.00</td>';
                                        }
                                    }
                                } else {
                                    $rte=1;
                                }
                                if($rte==1){
                                    $html.= '<td>0.00</td>';
                                    $html.= '<td>0.00</td>';
                                }
                                $html.= '<td>0.00</td>';
                                $html.= '</tr>';     
                            }
                            }

                        }
                        
                    }
                    $terceros_c = implode(",", $arrayt);
                    #***** Buscar Comprobantes Array Cuantias Menores*****#
                    $com = $con->Listar("SELECT DISTINCT 
                        cn.id_unico, cn.numero, tc.sigla, 
                        tc.nombre, tc.clasecontable, dc.valor, dc.id_unico   
                        FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                        LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante
                        WHERE cn.parametrizacionanno = $anno 
                        AND dc.tercero IN ($terceros_c) 
                        AND dc.cuenta IN ($cuentas) 
                        AND (tc.clasecontable !=5 AND tc.clasecontable !=20) 
                        ORDER BY cn.numero, cn.tipocomprobante");
                    for($j = 0; $j <count($com); $j++){
                        $id_comprobante = $com[$j][0];
                        $html.= '<tr>';
                        $html.= '<td>'.$com[$j][2].' - '.$com[$j][1].'</td>';
                        $html.= '<td>'.$codigo.'</td>';
                        #   *******     Datos Tercero    *******     #
                        $html.= '<td>43</td>';
                        $html.= '<td>222222222</td>';
                        $html.= '<td></td>';
                        $html.= '<td></td>';
                        $html.= '<td></td>';
                        $html.= '<td></td>';
                        $html.= '<td>Cuantías Menores</td>';
                        $html.= '<td></td>';
                        $html.= '<td></td>';
                        $html.= '<td></td>';                    
                        $html.= '<td>169</td>';
                        $html.= '<td>'. round($com[$j][5], 0).'</td>';
                        $html.= '<td>0.00</td>';
                        $rte  =0;
                        $im   =0;
                        if(($com[$j][4]==$rt) || ($com[$j][4]==13 && $rt==14)){
                            #   Validar Si El Valor Es El Mayor
                            $vm = $con ->Listar("SELECT MAX(if(valor<0, valor*-1, valor))  
                                    FROM gf_detalle_comprobante WHERE cuenta IN($cuentas) 
                                    AND comprobante = $id_comprobante AND tercero = $tercero");
                            $vm = $vm[0][0];
                            if($com[$j][5]==$vm){
                                # Validar Cuantos Detalles hay con ese valor 
                                $idm = $con ->Listar("SELECT * 
                                        FROM gf_detalle_comprobante WHERE cuenta IN($cuentas) 
                                        AND comprobante = $id_comprobante AND tercero = $tercero 
                                        AND valor = $vm");
                                if(count($idm)>0){  
                                    $idm = $idm[0][0];
                                    if($com[$j][6]==$idm){
                                        $im = 1;
                                    } else {
                                        $rte = 1;
                                    }
                                } else {
                                    $im = 1;
                                }
                            } else {
                               $rte  = 1; 
                            }
                        } else {
                            $rte  = 1;
                        }
                        if($im==1){
                            if($com[$j][4]==13 && $rt==14){
                                #**** Buscar Comprobantes Que Afectan La Cuenta Por Pagar ****#
                                $afe = $con->Listar("SELECT DISTINCT cne.id_unico FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico 
                                    LEFT JOIN gf_detalle_comprobante_pptal dce ON dce.comprobanteafectado = dcp.id_unico 
                                    LEFT JOIN gf_comprobante_pptal cpe ON dce.comprobantepptal = cpe.id_unico 
                                    LEFT JOIN gf_tipo_comprobante_pptal tce ON cpe.tipocomprobante = tce.id_unico 
                                    LEFT JOIN gf_tipo_comprobante tcne ON tce.id_unico = tcne.comprobante_pptal
                                    LEFT JOIN gf_comprobante_cnt cne ON tcne.id_unico = cne.tipocomprobante AND cpe.numero = cne.numero 
                                    WHERE dc.comprobante = $id_comprobante");
                                if(count($afe)>0){
                                    $comprobantes ="";
                                    $cont = count($afe);
                                    for ($e = 0; $e < count($afe); $e++) {
                                        #Id Egreso
                                        $id_egr = $afe[$e][0];
                                        #Buscar Cuentas Por Pagar Que Lo Afectan Que Sean Diferentes A CXP
                                        $cxaf = $con->Listar("SELECT DISTINCT cxcnt.id_unico 
                                                FROM gf_detalle_comprobante dc 
                                                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                                LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = cn.numero 
                                                LEFT JOIN gf_detalle_comprobante_pptal dp ON cp.id_unico = dp.comprobantepptal 
                                                LEFT JOIN gf_detalle_comprobante_pptal dpc ON dp.comprobanteafectado = dpc.id_unico 
                                                LEFT JOIN gf_comprobante_pptal cxp ON dpc.comprobantepptal = cxp.id_unico 
                                                LEFT JOIN gf_tipo_comprobante_pptal tcxp ON cxp.tipocomprobante = tcxp.id_unico 
                                                LEFT JOIN gf_tipo_comprobante tccxp ON tccxp.comprobante_pptal = tcxp.id_unico 
                                                LEFT JOIN gf_comprobante_cnt cxcnt ON cxcnt.tipocomprobante = tccxp.id_unico AND cxp.numero = cxcnt.numero
                                                WHERE cn.id_unico = $id_egr ORDER BY cxcnt.fecha desc");
                                        if(count($cxaf)>0){

                                            $id_com = $cxaf[0][0];
                                            if($id_com==$id_comprobante){
                                                $comprobantes .=$id_egr.',';
                                            }
                                        }
                                    }
                                    $comprobantes = substr($comprobantes, 0, -1);
                                    #   *******     Buscar Si Tiene Retenciones Aplicadas   *******    #
                                    #   Iva
                                    $brI = $con->Listar("SELECT SUM(r.valorretencion),
                                        SUM(r.retencionbase), tr.claseretencion 
                                        FROM gf_retencion r 
                                        LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                        WHERE r.comprobante IN($comprobantes) AND (tr.claseretencion = 4) 
                                        GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                    #Base Iva
                                    if(count($brI)>0){
                                        $html.= '<td>'. round($brI[0][1], 0).'</td>';
                                    } else {
                                        $html.= '<td>0.00</td>';
                                    }
                                    $html.= '<td>0.00</td>';
                                    #   RETEFTE
                                    $br = $con->Listar("SELECT SUM(r.valorretencion),
                                        SUM(r.retencionbase), tr.claseretencion 
                                        FROM gf_retencion r 
                                        LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                        WHERE r.comprobante IN($comprobantes) AND (tr.claseretencion = 3) 
                                        GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                    if(count($br)>0){
                                        $html.= '<td>'. round($br[0][0],0).'</td>';
                                    } else {
                                        $html.= '<td>0.00</td>';
                                    }
                                    $html.= '<td>0.00</td>';
                                    #Valor Iva
                                    if(count($brI)>0){
                                        $html.= '<td>'. round($brI[0][0],0).'</td>';
                                    } else {
                                        $html.= '<td>0.00</td>';
                                    }
                                } else {
                                    $html.= '<td>0.00</td>';
                                    $html.= '<td>0.00</td>';
                                    $html.= '<td>0.00</td>';
                                    $html.= '<td>0.00</td>';
                                    $html.= '<td>0.00</td>';
                                }
                            } else {
                                #   *******     Buscar Si Tiene Retenciones Aplicadas   *******    #
                                #   Iva
                                $brI = $con->Listar("SELECT SUM(r.valorretencion),
                                    SUM(r.retencionbase), tr.claseretencion 
                                    FROM gf_retencion r 
                                    LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                    WHERE r.comprobante = $id_comprobante AND (tr.claseretencion = 4) 
                                    GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                #Base Iva
                                if(count($brI)>0){
                                    $html.= '<td>'. round($brI[0][1],0).'</td>';
                                } else {
                                    $html.= '<td>0.00</td>';
                                }
                                $html.= '<td>0.00</td>';
                                #   RETEFTE
                                $br = $con->Listar("SELECT SUM(r.valorretencion),
                                    SUM(r.retencionbase), tr.claseretencion 
                                    FROM gf_retencion r 
                                    LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                    WHERE r.comprobante = $id_comprobante AND (tr.claseretencion = 3) 
                                    GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                if(count($br)>0){
                                    $html.= '<td>'. round($br[0][0],0).'</td>';
                                } else {
                                    $html.= '<td>0.00</td>';
                                }
                                $html.= '<td>0.00</td>';
                                #Valor Iva
                                if(count($brI)>0){
                                    $html.= '<td>'. round($brI[0][0],0).'</td>';
                                } else {
                                    $html.= '<td>0.00</td>';
                                }
                            }
                        } else {
                            $rte=1;
                        }
                        if($rte==1){
                            $html.= '<td>0.00</td>';
                            $html.= '<td>0.00</td>';
                            $html.= '<td>0.00</td>';
                            $html.= '<td>0.00</td>';
                            $html.= '<td>0.00</td>';
                        }
                        $html.= '<td>0.00</td>';
                        $html.= '</tr>';     
                    }  
                }
            }  
        } else {
            #***********************************************************************#
            #          ***************     Acumulado           ***************      #
            #***********************************************************************#
            #   *****   Titulos     ****    #
            $h  .='<tr>';
            $h  .='<td><center><strong>CONCEPTO</strong></center></td>';
            $h  .='<td><center><strong>TIPO DE DOCUMENTO</strong></center></td>';
            $h  .='<td><center><strong>NÚMERO DE IDENTIFICACIÓN</strong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>RAZÓN SOCIAL DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>DIRECCION</strong></center></td>';
            $h  .='<td><center><strong>CÓDIGO DPTO</strong></center></td>';
            $h  .='<td><center><strong>CÓDIGO MCP</strong></center></td>';
            $h  .='<td><center><strong>PAIS DE RESIDENCIA O DOMICILIO</strong></center></td>';
            $h  .='<td><center><strong>PAGO O ABONO EN CUENTA DEDUCIBLE</strong></center></td>';
            $h  .='<td><center><strong>PAGO O ABONO EN CUENTA NO DEDUCIBLE</strong></center></td>';
            $h  .='<td><center><strong>IVA MAYOR VALOR DEL COSTO O GASTO DEDUCIBLE</strong></center></td>';
            $h  .='<td><center><strong>IVA MAYOR VALOR DEL COSTO O GASTO NO DEDUCIBLE</strong></center></td>';
            $h  .='<td><center><strong>RETENCIÓN EN LA FUENTE PRACTICADA RENTA</strong></center></td>';
            $h  .='<td><center><strong>RENTENCIÓN EN LA FUENTE ASUMIDA RENTA</strong></center></td>';
            $h  .='<td><center><strong>RETENCIÓN EN LA FUENTE PRACTICADA IVA RÉGIMEN COMÚN</strong></center></td>';
            $h  .='<td><center><strong>RETENCIÓN EN LA FUENTE PRACTICADA IVA NO DOMICILIARIOS</strong></center></td>';
            $h  .='</tr>';
            for ($i = 0; $i < count($rowc); $i++) {
                $concepto = $rowc[$i][0];
                $codigo   = $rowc[$i][1];
                $cuentas  = $rowc[$i][2];
                #   ****    Buscar Terceros     ****    #
                $rowt = $con->Listar("SELECT DISTINCT 
                    dc.tercero  
                    FROM gf_detalle_comprobante dc 
                    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = dc.comprobante 
                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                    WHERE dc.cuenta IN ($cuentas) AND cn.parametrizacionanno = $anno 
                    AND (tc.clasecontable !=5 AND tc.clasecontable !=20) ");
                if(count($rowt)>0){
                    $arrayt = array();
                    for ($z = 0; $z < count($rowt); $z++) { 
                        $totalt =0;
                        $imprimir = 0;
                        if(!empty($rowt[$z][0])){
                            #Acumular Valor
                            $tercero = $rowt[$z][0];
                            $com = $con->Listar("SELECT DISTINCT 
                                cn.id_unico, cn.numero, tc.sigla, 
                                tc.nombre, tc.clasecontable, dc.valor, dc.id_unico   
                                FROM gf_detalle_comprobante dc 
                                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante
                                WHERE cn.parametrizacionanno = $anno 
                                AND dc.tercero = $tercero 
                                AND dc.cuenta IN ($cuentas) 
                                AND (tc.clasecontable !=5 AND tc.clasecontable !=20) 
                                ORDER BY cn.numero, cn.tipocomprobante");
                            
                            for($j = 0; $j <count($com); $j++){
                                $totalt +=$com[$j][5];
                                  
                            }
                            if(!empty($cuantia)){ 
                                if($totalt<=$cuantia || $tercero=='2'){
                                    if(in_array($tercero, $arrayt)) {
                                    } else {
                                        array_push ( $arrayt , $tercero );
                                    }
                                } else {
                                    $imprimir = 1;
                                }

                            } else {
                                if($tercero=='2'){
                                    if(in_array($tercero, $arrayt)) {
                                    } else {
                                        array_push ( $arrayt , $tercero );
                                    }
                                } else {
                                    $imprimir = 1;
                                }
                            }
                            if($imprimir ==1){
                                
                                #   *******     Buscar Datos Tercero    *******     #
                                $dt = $con->Listar("SELECT DISTINCT 
                                    t.id_unico, t.nombreuno,
                                    t.nombredos,  t.apellidouno,
                                    t.apellidodos, t.razonsocial, 
                                    t.digitoverficacion, ti.codigo, 
                                    t.numeroidentificacion, 
                                    GROUP_CONCAT(d.direccion),
                                    ci.rss, dp.rss 
                                    FROM gf_tercero t 
                                    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                                    LEFT JOIN gf_direccion d ON d.tercero = t.id_unico 
                                    LEFT JOIN gf_ciudad ci ON d.ciudad_direccion = ci.id_unico 
                                    LEFT JOIN gf_departamento dp ON ci.departamento = dp.id_unico 
                                    WHERE t.id_unico = $tercero");
                                if($exportar==3){
                                    $html.= '<tr>';
                                    $html.= '<td>'.$codigo.'</td>';
                                    $html.= '<td>'.$dt[0][7].'</td>';
                                    $html.= '<td>'.$dt[0][8].'</td>';
                                    $html.= '<td>'.$dt[0][3].'</td>';
                                    $html.= '<td>'.$dt[0][4].'</td>';
                                    $html.= '<td>'.$dt[0][1].'</td>';
                                    $html.= '<td>'.$dt[0][2].'</td>';
                                    $html.= '<td>'.$dt[0][5].'</td>';
                                    $html.= '<td>'.$dt[0][9].'</td>';
                                    $html.= '<td>'.$dt[0][11].'</td>';
                                    $html.= "<td style='mso-number-format:\@'>".$dt[0][10]."</td>";
                                    $html.= '<td>169</td>';
                                } else {
                                    $html .=str_replace(',',' ',$codigo)."$separador";
                                    $html .=str_replace(',',' ',$dt[0][7])."$separador";
                                    $html .=str_replace(',',' ',$dt[0][8])."$separador";
                                    $html .=str_replace(',',' ',utf8_decode($dt[0][3]))."$separador";
                                    $html .=str_replace(',',' ',utf8_decode($dt[0][4]))."$separador";
                                    $html .=str_replace(',',' ',utf8_decode($dt[0][1]))."$separador";
                                    $html .=str_replace(',',' ',utf8_decode($dt[0][2]))."$separador";
                                    $html .=str_replace(',',' ',utf8_decode($dt[0][5]))."$separador";
                                    $html .=str_replace(',',' ',$dt[0][9])."$separador";
                                    $html .=str_replace(',',' ',$dt[0][11])."$separador";
                                    $html .=str_replace(',',' ',$dt[0][10])."$separador";
                                    $html .=str_replace(',',' ',169)."$separador";
                                }
                                #***** Buscar Comprobantes *****#
                                $com = $con->Listar("SELECT DISTINCT 
                                    cn.id_unico, cn.numero, tc.sigla, 
                                    tc.nombre, tc.clasecontable, dc.valor, dc.id_unico   
                                    FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                    LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante
                                    WHERE cn.parametrizacionanno = $anno 
                                    AND dc.tercero = $tercero 
                                    AND dc.cuenta IN ($cuentas) 
                                    AND (tc.clasecontable !=5 AND tc.clasecontable !=20) 
                                    ORDER BY cn.numero, cn.tipocomprobante");
                                $total1 = 0;
                                $total2 = 0;
                                $total3 = 0;
                                $total4 = 0;
                                for($j = 0; $j <count($com); $j++){
                                    $id_comprobante = $com[$j][0];
                                    $total1 += $com[$j][5];
                                    $rte  =0;
                                    $im   =0;
                                    if($com[$j][4]==$rt || ($com[$j][4]==13 && $rt==14)){
                                        #   Validar Si El Valor Es El Mayor
                                        $vm = $con ->Listar("SELECT MAX(if(valor<0, valor*-1, valor))  
                                                FROM gf_detalle_comprobante WHERE cuenta IN($cuentas) 
                                                AND comprobante = $id_comprobante AND tercero = $tercero");
                                        $vm = $vm[0][0];
                                        if($com[$j][5]==$vm){
                                            # Validar Cuantos Detalles hay con ese valor 
                                            $idm = $con ->Listar("SELECT * 
                                                    FROM gf_detalle_comprobante WHERE cuenta IN($cuentas) 
                                                    AND comprobante = $id_comprobante AND tercero = $tercero 
                                                    AND valor = $vm");
                                            if(count($idm)>0){  
                                                $idm = $idm[0][0];
                                                if($com[$j][6]==$idm){
                                                    $im = 1;
                                                }
                                            } else {
                                                $im = 1;
                                            }
                                        }
                                    }
                                    if($im==1){
                                        if($com[$j][4]==13 && $rt==14){
                                            #**** Buscar Comprobantes Que Afectan La Cuenta Por Pagar ****#
                                            $afe = $con->Listar("SELECT DISTINCT cne.id_unico FROM gf_detalle_comprobante dc 
                                                LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico 
                                                LEFT JOIN gf_detalle_comprobante_pptal dce ON dce.comprobanteafectado = dcp.id_unico 
                                                LEFT JOIN gf_comprobante_pptal cpe ON dce.comprobantepptal = cpe.id_unico 
                                                LEFT JOIN gf_tipo_comprobante_pptal tce ON cpe.tipocomprobante = tce.id_unico 
                                                LEFT JOIN gf_tipo_comprobante tcne ON tce.id_unico = tcne.comprobante_pptal
                                                LEFT JOIN gf_comprobante_cnt cne ON tcne.id_unico = cne.tipocomprobante AND cpe.numero = cne.numero 
                                                WHERE dc.comprobante = $id_comprobante");
                                            if(count($afe)>0){
                                                $comprobantes ="";
                                                $cont = count($afe);
                                                for ($e = 0; $e < count($afe); $e++) {
                                                    #Id Egreso
                                                    $id_egr = $afe[$e][0];
                                                    #Buscar Cuentas Por Pagar Que Lo Afectan Que Sean Diferentes A CXP
                                                    $cxaf = $con->Listar("SELECT DISTINCT cxcnt.id_unico 
                                                            FROM gf_detalle_comprobante dc 
                                                            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                                            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                                            LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = cn.numero 
                                                            LEFT JOIN gf_detalle_comprobante_pptal dp ON cp.id_unico = dp.comprobantepptal 
                                                            LEFT JOIN gf_detalle_comprobante_pptal dpc ON dp.comprobanteafectado = dpc.id_unico 
                                                            LEFT JOIN gf_comprobante_pptal cxp ON dpc.comprobantepptal = cxp.id_unico 
                                                            LEFT JOIN gf_tipo_comprobante_pptal tcxp ON cxp.tipocomprobante = tcxp.id_unico 
                                                            LEFT JOIN gf_tipo_comprobante tccxp ON tccxp.comprobante_pptal = tcxp.id_unico 
                                                            LEFT JOIN gf_comprobante_cnt cxcnt ON cxcnt.tipocomprobante = tccxp.id_unico AND cxp.numero = cxcnt.numero
                                                            WHERE cn.id_unico = $id_egr ORDER BY cxcnt.fecha desc");
                                                    if(count($cxaf)>0){

                                                        $id_com = $cxaf[0][0];
                                                        if($id_com==$id_comprobante){
                                                            $comprobantes .=$id_egr.',';
                                                        }
                                                    }
                                                }
                                                $comprobantes = substr($comprobantes, 0, -1);
                                                #   *******     Buscar Si Tiene Retenciones Aplicadas   *******    #
                                                #   Iva
                                                $brI = $con->Listar("SELECT SUM(r.valorretencion),
                                                    SUM(r.retencionbase), tr.claseretencion 
                                                    FROM gf_retencion r 
                                                    LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                                    WHERE r.comprobante IN($comprobantes) AND (tr.claseretencion = 4) 
                                                    GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                                #Base Iva
                                                if(count($brI)>0){
                                                    $total2 +=$brI[0][1];
                                                }
                                                #   RETEFTE
                                                $br = $con->Listar("SELECT SUM(r.valorretencion),
                                                    SUM(r.retencionbase), tr.claseretencion 
                                                    FROM gf_retencion r 
                                                    LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                                    WHERE r.comprobante IN($comprobantes) AND (tr.claseretencion = 3) 
                                                    GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                                if(count($br)>0){
                                                    $total3 += $br[0][0];
                                                }
                                                #Valor Iva
                                                if(count($brI)>0){
                                                    $total4 += $brI[0][0];
                                                }
                                            }
                                        } else {
                                            #   *******     Buscar Si Tiene Retenciones Aplicadas   *******    #
                                            #   Iva
                                            $brI = $con->Listar("SELECT SUM(r.valorretencion),
                                                SUM(r.retencionbase), tr.claseretencion 
                                                FROM gf_retencion r 
                                                LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                                WHERE r.comprobante = $id_comprobante AND (tr.claseretencion = 4) 
                                                GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                            #Base Iva
                                            if(count($brI)>0){
                                                $total2 +=$brI[0][1];
                                            }
                                            #   RETEFTE
                                            $br = $con->Listar("SELECT SUM(r.valorretencion),
                                                SUM(r.retencionbase), tr.claseretencion 
                                                FROM gf_retencion r 
                                                LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                                WHERE r.comprobante = $id_comprobante AND (tr.claseretencion = 3) 
                                                GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                            if(count($br)>0){
                                                $total3 += $br[0][0];
                                            }
                                            #Valor Iva
                                            if(count($brI)>0){
                                                $total4 += $brI[0][0];
                                            }
                                        }
                                    }        
                                }
                                if($exportar==3){
                                    $html.= '<td>'. round($total1,0).'</td>';
                                    $html.= '<td>0.00</td>';
                                    $html.= '<td>'. round($total2,0).'</td>';
                                    $html.= '<td>0.00</td>';
                                    $html.= '<td>'. round($total3,0).'</td>';
                                    $html.= '<td>0.00</td>';
                                    $html.= '<td>'. round($total4,0).'</td>';
                                    $html.= '<td>0.00</td>';
                                    $html.= '</tr>';
                                } else {
                                    $html .=str_replace(',',' ',round($total1,0))."$separador";
                                    $html .=str_replace(',',' ',0)."$separador";
                                    $html .=str_replace(',',' ',round($total2,0))."$separador";
                                    $html .=str_replace(',',' ',0)."$separador";
                                    $html .=str_replace(',',' ',round($total3,0))."$separador";
                                    $html .=str_replace(',',' ',0)."$separador";
                                    $html .=str_replace(',',' ',round($total4,0))."$separador";
                                    $html .=str_replace(',',' ',0)."$separador";
                                    $html .= "\n";
                                }   
                            }
                        } 
                    }
                    $terceros_c = implode(",", $arrayt);
                    #***** Buscar Comprobantes Array Cuantias Menores*****#
                    $com = $con->Listar("SELECT DISTINCT 
                        cn.id_unico, cn.numero, tc.sigla, 
                        tc.nombre, tc.clasecontable, dc.valor, dc.id_unico   
                        FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                        LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante
                        WHERE cn.parametrizacionanno = $anno 
                        AND dc.tercero IN ($terceros_c) 
                        AND dc.cuenta IN ($cuentas) 
                        AND (tc.clasecontable !=5 AND tc.clasecontable !=20) 
                        ORDER BY cn.numero, cn.tipocomprobante");
                    if($exportar==3){
                        $html.= '<tr>';
                        $html.= '<td>'.$codigo.'</td>';
                        #   *******     Datos Tercero    *******     #
                        $html.= '<td>43</td>';
                        $html.= '<td>222222222</td>';
                        $html.= '<td></td>';
                        $html.= '<td></td>';
                        $html.= '<td></td>';
                        $html.= '<td></td>';
                        $html.= '<td>Cuantías Menores</td>';
                        $html.= '<td></td>';
                        $html.= '<td></td>';
                        $html.= '<td></td>';                    
                        $html.= '<td>169</td>';
                    } else {
                        $html .=str_replace(',',' ',$codigo)."$separador";
                        $html .=str_replace(',',' ',43)."$separador";
                        $html .=str_replace(',',' ',222222222)."$separador";
                        $html .=''."$separador";
                        $html .=''."$separador";
                        $html .=''."$separador";
                        $html .=''."$separador";
                        $html .=utf8_decode('Cuantías Menores')."$separador";
                        $html .=''."$separador";
                        $html .=''."$separador";
                        $html .=''."$separador";
                        $html .='169'."$separador"; 
                    }
                    
                    $total1 = 0;
                    $total2 = 0;
                    $total3 = 0;
                    $total4 = 0;
                    for($j = 0; $j <count($com); $j++){
                        $id_comprobante = $com[$j][0];
                        $total1 +=$com[$j][5];
                        $rte  =0;
                        $im   =0;
                        if($com[$j][4]==$rt || ($com[$j][4]==13 && $rt==14) ){
                            #   Validar Si El Valor Es El Mayor
                            $vm = $con ->Listar("SELECT MAX(if(valor<0, valor*-1, valor))  
                                    FROM gf_detalle_comprobante WHERE cuenta IN($cuentas) 
                                    AND comprobante = $id_comprobante AND tercero = $tercero");
                            $vm = $vm[0][0];
                            if($com[$j][5]==$vm){
                                # Validar Cuantos Detalles hay con ese valor 
                                $idm = $con ->Listar("SELECT * 
                                        FROM gf_detalle_comprobante WHERE cuenta IN($cuentas) 
                                        AND comprobante = $id_comprobante AND tercero = $tercero 
                                        AND valor = $vm");
                                if(count($idm)>0){  
                                    $idm = $idm[0][0];
                                    if($com[$j][6]==$idm){
                                        $im = 1;
                                    }
                                } else {
                                    $im = 1;
                                }
                            }
                        } 
                        if($im==1){
                            if($com[$j][4]==13 && $rt==14){
                                #**** Buscar Comprobantes Que Afectan La Cuenta Por Pagar ****#
                                $afe = $con->Listar("SELECT DISTINCT cne.id_unico FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico 
                                    LEFT JOIN gf_detalle_comprobante_pptal dce ON dce.comprobanteafectado = dcp.id_unico 
                                    LEFT JOIN gf_comprobante_pptal cpe ON dce.comprobantepptal = cpe.id_unico 
                                    LEFT JOIN gf_tipo_comprobante_pptal tce ON cpe.tipocomprobante = tce.id_unico 
                                    LEFT JOIN gf_tipo_comprobante tcne ON tce.id_unico = tcne.comprobante_pptal
                                    LEFT JOIN gf_comprobante_cnt cne ON tcne.id_unico = cne.tipocomprobante AND cpe.numero = cne.numero 
                                    WHERE dc.comprobante = $id_comprobante");
                                if(count($afe)>0){
                                    $comprobantes ="";
                                    $cont = count($afe);
                                    for ($e = 0; $e < count($afe); $e++) {
                                        #Id Egreso
                                        $id_egr = $afe[$e][0];
                                        #Buscar Cuentas Por Pagar Que Lo Afectan Que Sean Diferentes A CXP
                                        $cxaf = $con->Listar("SELECT DISTINCT cxcnt.id_unico 
                                                FROM gf_detalle_comprobante dc 
                                                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                                LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = cn.numero 
                                                LEFT JOIN gf_detalle_comprobante_pptal dp ON cp.id_unico = dp.comprobantepptal 
                                                LEFT JOIN gf_detalle_comprobante_pptal dpc ON dp.comprobanteafectado = dpc.id_unico 
                                                LEFT JOIN gf_comprobante_pptal cxp ON dpc.comprobantepptal = cxp.id_unico 
                                                LEFT JOIN gf_tipo_comprobante_pptal tcxp ON cxp.tipocomprobante = tcxp.id_unico 
                                                LEFT JOIN gf_tipo_comprobante tccxp ON tccxp.comprobante_pptal = tcxp.id_unico 
                                                LEFT JOIN gf_comprobante_cnt cxcnt ON cxcnt.tipocomprobante = tccxp.id_unico AND cxp.numero = cxcnt.numero
                                                WHERE cn.id_unico = $id_egr ORDER BY cxcnt.fecha desc");
                                        if(count($cxaf)>0){

                                            $id_com = $cxaf[0][0];
                                            if($id_com==$id_comprobante){
                                                $comprobantes .=$id_egr.',';
                                            }
                                        }
                                    }
                                    $comprobantes = substr($comprobantes, 0, -1);
                                    #   *******     Buscar Si Tiene Retenciones Aplicadas   *******    #
                                    #   Iva
                                    $brI = $con->Listar("SELECT SUM(r.valorretencion),
                                        SUM(r.retencionbase), tr.claseretencion 
                                        FROM gf_retencion r 
                                        LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                        WHERE r.comprobante IN($comprobantes) AND (tr.claseretencion = 4) 
                                        GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                    #Base Iva
                                    if(count($brI)>0){
                                        $total2 +=$brI[0][1];
                                    }
                                    #   RETEFTE
                                    $br = $con->Listar("SELECT SUM(r.valorretencion),
                                        SUM(r.retencionbase), tr.claseretencion 
                                        FROM gf_retencion r 
                                        LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                        WHERE r.comprobante IN($comprobantes) AND (tr.claseretencion = 3) 
                                        GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                    if(count($br)>0){
                                        $total3 += $br[0][0];
                                    }
                                    #Valor Iva
                                    if(count($brI)>0){
                                        $total4 += $brI[0][0];
                                    }
                                }
                            } else {
                                #   *******     Buscar Si Tiene Retenciones Aplicadas   *******    #
                                #   Iva
                                $brI = $con->Listar("SELECT SUM(r.valorretencion),
                                    SUM(r.retencionbase), tr.claseretencion 
                                    FROM gf_retencion r 
                                    LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                    WHERE r.comprobante = $id_comprobante AND (tr.claseretencion = 4) 
                                    GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                #Base Iva
                                if(count($brI)>0){
                                    $total2 +=$brI[0][1];
                                }
                                #   RETEFTE
                                $br = $con->Listar("SELECT SUM(r.valorretencion),
                                    SUM(r.retencionbase), tr.claseretencion 
                                    FROM gf_retencion r 
                                    LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                                    WHERE r.comprobante = $id_comprobante AND (tr.claseretencion = 3) 
                                    GROUP BY tr.claseretencion ORDER BY tr.claseretencion DESC");
                                if(count($br)>0){
                                    $total3 += $br[0][0];
                                }
                                #Valor Iva
                                if(count($brI)>0){
                                    $total4 += $brI[0][0];
                                }
                            }
                        }     
                    }
                    if($exportar==3){
                        $html.= '<td>'. round($total1,0).'</td>';
                        $html.= '<td>0.00</td>';
                        $html.= '<td>'. round($total2,0).'</td>';
                        $html.= '<td>0.00</td>';
                        $html.= '<td>'. round($total3,0).'</td>';
                        $html.= '<td>0.00</td>';
                        $html.= '<td>'. round($total4,0).'</td>';
                        $html.= '<td>0.00</td>';
                        $html.= '</tr>'; 
                    } else {
                        $html .=str_replace(',',' ',round($total1,0))."$separador";
                        $html .=str_replace(',',' ',0)."$separador";
                        $html .=str_replace(',',' ',round($total2,0))."$separador";
                        $html .=str_replace(',',' ',0)."$separador";
                        $html .=str_replace(',',' ',round($total3,0))."$separador";
                        $html .=str_replace(',',' ',0)."$separador";
                        $html .=str_replace(',',' ',round($total4,0))."$separador";
                        $html .=str_replace(',',' ',0)."$separador";
                        $html .= "\n";
                    }  
                    
                    
                }
            }  
        }
        if($exportar==3){
            $htmle .=$h.$html;
        }
    break;
    case(1009):
        #   *****   Buscar Conceptos  ****   #
        $rowc = $con->Listar("SELECT DISTINCT 
            ce.concepto_exogenas, cn.codigo , GROUP_CONCAT(ce.cuenta) 
            FROM gf_configuracion_exogenas ce 
            LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
            WHERE cn.formato =$formato 
            GROUP BY ce.concepto_exogenas" );
        
        for ($i = 0; $i < count($rowc); $i++) {
            $concepto = $rowc[$i][0];
            $codigo   = $rowc[$i][1];
            $cuentas  = $rowc[$i][2];
            $valor_cuantia =0;
            #   ****    Buscar Terceros     ****    #
            $rowt = $con->Listar("SELECT DISTINCT 
                dc.tercero 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante 
                WHERE dc.cuenta IN ($cuentas) AND cn.parametrizacionanno = $anno 
                AND (tc.clasecontable !=20) ");
            if(count($rowt)>0){
                for ($z = 0; $z < count($rowt); $z++) {
                    if(!empty($rowt[$z][0])){
                        $imprimir   = 0;
                        $suman      = valorPI($cuentas, $rowt[$z][0], '+');
                        $restan     = valorPI($cuentas, $rowt[$z][0], '-');
                        $total      = $suman - ($restan*-1);
                        
                        if(!empty($cuantia)){ 
                            if($total<=$cuantia || $rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantia +=$total;
                            }
                            
                        } else {
                            if($rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantia +=$total;
                            }
                        }
                        if($imprimir==0) {
                            if($total!=0) { 
                                # *** Buscar Datos Tercero *** #
                                $dter = $con->Listar("SELECT DISTINCT 
                                    t.id_unico, t.nombreuno,
                                    t.nombredos,  t.apellidouno,
                                    t.apellidodos, t.razonsocial, 
                                    t.digitoverficacion, ti.codigo, 
                                    t.numeroidentificacion, 
                                    GROUP_CONCAT(d.direccion),
                                    ci.rss, dp.rss 
                                    FROM gf_tercero t 
                                    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                                    LEFT JOIN gf_direccion d ON d.tercero = t.id_unico 
                                    LEFT JOIN gf_ciudad ci ON d.ciudad_direccion = ci.id_unico 
                                    LEFT JOIN gf_departamento dp ON ci.departamento = dp.id_unico 
                                    WHERE t.id_unico = ".$rowt[$z][0]);
                                if($exportar==3){
                                    $html .='<tr>';
                                    $html .='<td>'.$codigo.'</td>';
                                    $html .='<td>'.$dter[0][7].'</td>';
                                    $html .='<td>'.$dter[0][8].'</td>';
                                    $html .='<td>'.$dter[0][6].'</td>';
                                    $html .='<td>'.$dter[0][3].'</td>';
                                    $html .='<td>'.$dter[0][4].'</td>';
                                    $html .='<td>'.$dter[0][1].'</td>';
                                    $html .='<td>'.$dter[0][2].'</td>';
                                    $html .='<td>'.$dter[0][5].'</td>';
                                    $html .='<td>'.$dter[0][9].'</td>';
                                    $html .='<td>'.$dter[0][11].'</td>';
                                    $html .="<td style='mso-number-format:\@'>".$dter[0][10]."</td>";                                    
                                    $html .='<td>169</td>';
                                    $html .='<td>'.round($total,0).'</td>';
                                    $html .='</tr>';
                                } else {
                                    $html .=str_replace(',',' ',$codigo)."$separador";
                                    $html .=str_replace(',',' ',$dter[0][7])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][8])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][6])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][3])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][4])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][1])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][2])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][5])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][9])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][11])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][10])."$separador";                                    
                                    $html .='169'."$separador";
                                    $html .=round($total,0);
                                    $html .= "\n";
                                }
                            }
                        }
                    }
                }
            }
            if($valor_cuantia!=0){
                if($exportar==3){
                    $html .='<tr>';
                    $html .='<td>'.$codigo.'</td>';
                    $html .='<td>43</td>';
                    $html .='<td>222222222</td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td>Cuantías menores</td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td>169</td>';
                    $html .='<td>'.round($valor_cuantia,0).'</td>';
                    $html .='</tr>';
                } else {
                    $html .=str_replace(',',' ',$codigo)."$separador";
                    $html .='43'."$separador";
                    $html .='222222222'."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .='Cuantías menores'."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .='169'."$separador";
                    $html .=round($valor_cuantia,0);
                    $html .= "\n";
                }
            }
        }
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>CONCEPTO</strong></center></td>';
            $h  .='<td><center><strong>TIPO DE DOCUMENTO</strong></center></td>';
            $h  .='<td><center><strong>NÚMERO DE IDENTIFICACIÓN ACREEDOR</strong></center></td>';
            $h  .='<td><center><strong>DV</strong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO ACREEDOR</strong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO ACREEDOR</strong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE ACREEDOR</strong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES ACREEDOR</strong></center></td>';
            $h  .='<td><center><strong>RAZÓN SOCIAL ACREEDOR</strong></center></td>';
            $h  .='<td><center><strong>DIRECCION</strong></center></td>';
            $h  .='<td><center><strong>CÓDIGO DPTO</strong></center></td>';
            $h  .='<td><center><strong>CÓDIGO MCP</strong></center></td>';
            $h  .='<td><center><strong>PAIS DE RESIDENCIA O DOMICILIO</strong></center></td>';
            $h  .='<td><center><strong>SALDO CUENTAS POR PAGAR AL 31-12</strong></center></td>';
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
        
    break;
    case(1005):
        #   *****   Buscar Conceptos  ****   #
        $rowc = $con->Listar("SELECT DISTINCT 
            ce.concepto_exogenas, cn.codigo , GROUP_CONCAT(ce.cuenta) 
            FROM gf_configuracion_exogenas ce 
            LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
            WHERE cn.formato =$formato 
            GROUP BY ce.concepto_exogenas" );
        
        for ($i = 0; $i < count($rowc); $i++) {
            $concepto = $rowc[$i][0];
            $codigo   = $rowc[$i][1];
            $cuentas  = $rowc[$i][2];
            $valor_cuantiai =0;
            $valor_cuantiad =0;
            #   ****    Buscar Terceros     ****    #
            $rowt = $con->Listar("SELECT DISTINCT 
                dc.tercero 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico  
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                WHERE dc.cuenta IN ($cuentas) AND cn.parametrizacionanno = $anno 
                AND (tc.clasecontable !=5 AND tc.clasecontable !=20)");
            if(count($rowt)>0){
                for ($z = 0; $z < count($rowt); $z++) {
                    if(!empty($rowt[$z][0])){
                        $imprimir   = 0;
                        $ingresos   = valor($cuentas, $rowt[$z][0], '+');
                        $descuentos = valor($cuentas, $rowt[$z][0], '-');
                        if($descuentos<0){
                            $descuentos = $descuentos*-1;
                        }
                        if(!empty($cuantia)){ 
                            if($ingresos<=$cuantia || $rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantiai +=$ingresos;
                                $valor_cuantiad +=$descuentos;
                            }
                        } else {
                            if($rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantiai +=$ingresos;
                                $valor_cuantiad +=$descuentos;
                            }
                        }
                        if($imprimir==0) {
                            if($ingresos != 0 || $descuentos !=0) { 
                                # *** Buscar Datos Tercero *** #
                                $dter = $con->Listar("SELECT DISTINCT 
                                    t.id_unico, t.nombreuno,
                                    t.nombredos,  t.apellidouno,
                                    t.apellidodos, t.razonsocial, 
                                    t.digitoverficacion, ti.codigo, 
                                    t.numeroidentificacion, 
                                    GROUP_CONCAT(d.direccion),
                                    ci.rss, dp.rss 
                                    FROM gf_tercero t 
                                    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                                    LEFT JOIN gf_direccion d ON d.tercero = t.id_unico 
                                    LEFT JOIN gf_ciudad ci ON d.ciudad_direccion = ci.id_unico 
                                    LEFT JOIN gf_departamento dp ON ci.departamento = dp.id_unico 
                                    WHERE t.id_unico = ".$rowt[$z][0]); 
                                if($exportar==3){
                                    $html .='<tr>';
                                    $html .='<td>'.$dter[0][7].'</td>';
                                    $html .='<td>'.$dter[0][8].'</td>';
                                    $html .='<td>'.$dter[0][6].'</td>';
                                    $html .='<td>'.$dter[0][3].'</td>';
                                    $html .='<td>'.$dter[0][4].'</td>';
                                    $html .='<td>'.$dter[0][1].'</td>';
                                    $html .='<td>'.$dter[0][2].'</td>';
                                    $html .='<td>'.$dter[0][5].'</td>';
                                    $html .='<td>'.round($ingresos,0).'</td>';
                                    $html .='<td>'.round($descuentos,0).'</td>';
                                    $html .='</tr>';
                                } else {
                                    $html .=str_replace(',',' ',$dter[0][7])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][8])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][6])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][3])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][4])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][1])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][2])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][5])."$separador";
                                    $html .=round($ingresos,0)."$separador";
                                    $html .=round($descuentos,0);
                                    $html .= "\n";
                                }
                            }    
                        }
                    }
                }
            }
            if($valor_cuantiad!=0 || $valor_cuantiai !=0){
                if($exportar==3){
                    $html .='<tr>';
                    $html .='<td>43</td>';
                    $html .='<td>222222222</td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td>Cuantías menores</td>';
                    $html .='<td>'.round($valor_cuantiai,0).'</td>';
                    $html .='<td>'.round($valor_cuantiad,0).'</td>';
                    $html .='</tr>';
                } else {
                    $html .='43'."$separador";
                    $html .='222222222'."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .='Cuantías menores'."$separador";
                    $html .=round($valor_cuantiai,0)."$separador";
                    $html .=round($valor_cuantiad,0);
                    $html .= "\n";
                }
            }
        }
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>TIPO DE DOCUMENTO</strong></center></td>';
            $h  .='<td><center><strong>NÚMERO DE IDENTIFICACIÓN</strong></center></td>';
            $h  .='<td><center><strong>DV</strong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>RAZÓN SOCIAL DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>IMPUESTO DESCONTABLE</strong></center></td>';
            $h  .='<td><center><strong>IVA RESULTANTE POR DEVOLUCIONES EN VENTAS ANULADAS, RESCINDIDAS O RESUELTAS</strong></center></td>';
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
        
    break;
    case(1006):
        #   *****   Buscar Conceptos  ****   #
        $rowc = $con->Listar("SELECT DISTINCT 
            ce.concepto_exogenas, cn.codigo , GROUP_CONCAT(ce.cuenta) 
            FROM gf_configuracion_exogenas ce 
            LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
            WHERE cn.formato =$formato 
            GROUP BY ce.concepto_exogenas" );
        
        for ($i = 0; $i < count($rowc); $i++) {
            $concepto = $rowc[$i][0];
            $codigo   = $rowc[$i][1];
            $cuentas  = $rowc[$i][2];
            $valor_cuantiai =0;
            $valor_cuantiad =0;
            #   ****    Buscar Terceros     ****    #
            $rowt = $con->Listar("SELECT DISTINCT 
                dc.tercero 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico  
                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                WHERE dc.cuenta IN ($cuentas) AND cn.parametrizacionanno = $anno 
                AND (tc.clasecontable !=5 AND tc.clasecontable !=20)");
            if(count($rowt)>0){
                for ($z = 0; $z < count($rowt); $z++) {
                    if(!empty($rowt[$z][0])){
                        $imprimir   = 0;
                        $ingresos   = valor($cuentas, $rowt[$z][0], '+');
                        $descuentos = valor($cuentas, $rowt[$z][0], '-');
                        if($descuentos<0){
                            $descuentos = $descuentos*-1;
                        }
                        if(!empty($cuantia)){ 
                            if($ingresos<=$cuantia || $rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantiai +=$ingresos;
                                $valor_cuantiad +=$descuentos;
                            }
                        } else {
                            if($rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantiai +=$ingresos;
                                $valor_cuantiad +=$descuentos;
                            }
                        }
                        if($imprimir==0) {
                            if($ingresos != 0 || $descuentos !=0) { 
                                # *** Buscar Datos Tercero *** #
                                $dter = $con->Listar("SELECT DISTINCT 
                                    t.id_unico, t.nombreuno,
                                    t.nombredos,  t.apellidouno,
                                    t.apellidodos, t.razonsocial, 
                                    t.digitoverficacion, ti.codigo, 
                                    t.numeroidentificacion, 
                                    GROUP_CONCAT(d.direccion),
                                    ci.rss, dp.rss 
                                    FROM gf_tercero t 
                                    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                                    LEFT JOIN gf_direccion d ON d.tercero = t.id_unico 
                                    LEFT JOIN gf_ciudad ci ON d.ciudad_direccion = ci.id_unico 
                                    LEFT JOIN gf_departamento dp ON ci.departamento = dp.id_unico 
                                    WHERE t.id_unico = ".$rowt[$z][0]); 
                                if($exportar==3){
                                    $html .='<tr>';
                                    $html .='<td>'.$dter[0][7].'</td>';
                                    $html .='<td>'.$dter[0][8].'</td>';
                                    $html .='<td>'.$dter[0][6].'</td>';
                                    $html .='<td>'.$dter[0][3].'</td>';
                                    $html .='<td>'.$dter[0][4].'</td>';
                                    $html .='<td>'.$dter[0][1].'</td>';
                                    $html .='<td>'.$dter[0][2].'</td>';
                                    $html .='<td>'.$dter[0][5].'</td>';
                                    $html .='<td>'.round($ingresos,0).'</td>';
                                    $html .='<td>'.round($descuentos,0).'</td>';
                                    $html .='<td>0.00</td>';
                                    $html .='</tr>';
                                } else {
                                    $html .=str_replace(',',' ',$dter[0][7])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][8])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][6])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][3])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][4])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][1])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][2])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][5])."$separador";
                                    $html .=round($ingresos,0)."$separador";
                                    $html .=round($descuentos,0)."$separador";
                                    $html .='0';
                                    
                                    $html .= "\n";
                                }
                            }    
                        }
                    }
                }
            }
            if($valor_cuantiad!=0 || $valor_cuantiai !=0){
                if($exportar==3){
                    $html .='<tr>';
                    $html .='<td>43</td>';
                    $html .='<td>222222222</td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td>Cuantías menores</td>';
                    $html .='<td>'.round($valor_cuantiai,0).'</td>';
                    $html .='<td>'.round($valor_cuantiad,0).'</td>';
                    $html .='<td>0</td>';
                    $html .='</tr>';
                } else {
                    $html .='43'."$separador";
                    $html .='222222222'."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .='Cuantías menores'."$separador";
                    $html .=round($valor_cuantiai,0)."$separador";
                    $html .=round($valor_cuantiad,0)."$separador";
                    $html .='0';
                    $html .= "\n";
                }
            }
        }
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>TIPO DE DOCUMENTO</strong></center></td>';
            $h  .='<td><center><strong>NÚMERO DE IDENTIFICACIÓN</strong></center></td>';
            $h  .='<td><center><strong>DV</strong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>RAZÓN SOCIAL DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>IMPUESTO GENERADO</strong></center></td>';
            $h  .='<td><center><strong>IVA RECUPERADO EN DEVOLUCIONES EN COMPRAS ANULADAS, RESCINDIDAS O RESUELTAS</strong></center></td>';
            $h  .='<td><center><strong>IMPUESTO AL CONSUMO</strong></center></td>';
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
        
    break;
    case(1011):
        #   *****   Buscar Conceptos  ****   #
        $rowc = $con->Listar("SELECT DISTINCT 
            ce.concepto_exogenas, cn.codigo , GROUP_CONCAT(ce.cuenta) 
            FROM gf_configuracion_exogenas ce 
            LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
            WHERE cn.formato =$formato 
            GROUP BY ce.concepto_exogenas" );
        
        for ($i = 0; $i < count($rowc); $i++) {
            $concepto = $rowc[$i][0];
            $codigo   = $rowc[$i][1];
            $cuentas  = $rowc[$i][2];
            $valor    = 0;
            $imprimir   = 0;
            $positivos  = valor_total($cuentas, '+');
            $negativos  = valor_total($cuentas, '-');
            $valor      = $positivos -($negativos *-1);
            if($valor<0){
                $valor = $valor*-1;
            }
            if($valor>0) {
                if($exportar==3){
                    $html .='<tr>';
                    $html .='<td>'.$codigo.'</td>';
                    $html .='<td>'.round($valor,0).'</td>';
                    $html .='</tr>';
                } else {
                    $html .=str_replace(',',' ',$codigo)."$separador";
                    $html .=round($valor,0);
                    $html .= "\n";
                }  
            }
        }
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>CONCEPTOS</strong></center></td>';
            $h  .='<td><center><strong>SALDOS AL 31-12</strong></center></td>';
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
        
    break;
    #Nómina 
    case(2276):
        # *** Buscar Datos Tercero *** #
        $dter = $con->Listar("SELECT DISTINCT 
            t.id_unico, t.nombreuno,
            t.nombredos,  t.apellidouno,
            t.apellidodos, t.razonsocial, 
            t.digitoverficacion, ti.codigo, 
            t.numeroidentificacion, 
            CONCAT_WS(' - ',d.direccion),
            ci.rss, dp.rss 
            FROM gn_novedad n 
            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
            LEFT JOIN gf_direccion d ON d.tercero = t.id_unico 
            LEFT JOIN gf_ciudad ci ON d.ciudad_direccion = ci.id_unico 
            LEFT JOIN gf_departamento dp ON ci.departamento = dp.id_unico 
            WHERE t.numeroidentificacion != 9999999999 GROUP BY t.id_unico "); 
        for ($t = 0; $t < count($dter); $t++) {
            #   *****   Buscar Conceptos  ****   #
            if($exportar==3){
                $html .='<tr>';
                $html .='<td>'.$dter[$t][7].'</td>';
                $html .='<td>'.$dter[$t][8].'</td>';
                $html .='<td>'.$dter[$t][3].'</td>';
                $html .='<td>'.$dter[$t][4].'</td>';
                $html .='<td>'.$dter[$t][1].'</td>';
                $html .='<td>'.$dter[$t][2].'</td>';
                $html .='<td>'.$dter[$t][9].'</td>';
                $html .='<td>'.$dter[$t][11].'</td>';
                $html .='<td>'.$dter[$t][10].'</td>';
                $html .='<td>169</td>';
                $rowc = $con->Listar("SELECT DISTINCT cn.id_unico 
                FROM  gf_concepto_exogenas cn 
                WHERE cn.formato =$formato 
                ORDER BY cn.codigo" );
                for ($c = 0; $c < count($rowc); $c++) {
                    $id_c = $rowc[$c][0];
                    $bv = $con->Listar("SELECT SUM(n.valor) 
                    FROM gf_configuracion_exogenas ce 
                    LEFT JOIN gn_concepto c ON ce.cuenta = c.id_unico 
                    LEFT JOIN gn_novedad n ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE e.tercero = '".$dter[$t][0]."' 
                        AND ce.concepto_exogenas = $id_c 
                        AND p.parametrizacionanno = $anno  
                        GROUP BY e.id_unico");
                    if(empty($bv[0][0])){
                        $html .='<td>'.round(0,0).'</td>';
                    } else {
                        $html .='<td>'.round($bv[0][0],0).'</td>';
                    }
                }
                
                
                $html .='</tr>';
            } else {
                $html .=str_replace(',',' ',$dter[$t][7])."$separador";
                $html .=str_replace(',',' ',$dter[$t][8])."$separador";
                $html .=str_replace(',',' ',$dter[$t][3])."$separador";
                $html .=str_replace(',',' ',$dter[$t][4])."$separador";
                $html .=str_replace(',',' ',$dter[$t][1])."$separador";
                $html .=str_replace(',',' ',$dter[$t][2])."$separador";
                $html .=str_replace(',',' ',$dter[$t][9])."$separador";
                $html .=str_replace(',',' ',$dter[$t][11])."$separador";
                $html .=str_replace(',',' ',$dter[$t][10])."$separador";
                $html .=str_replace(',',' ',169)."$separador";
                $rowc = $con->Listar("SELECT DISTINCT cn.id_unico 
                FROM  gf_concepto_exogenas cn 
                WHERE cn.formato =$formato 
                ORDER BY cn.codigo" );
                for ($c = 0; $c < count($rowc); $c++) {
                    $id_c = $rowc[$c][0];
                    $bv = $con->Listar("SELECT SUM(n.valor) 
                    FROM gf_configuracion_exogenas ce 
                    LEFT JOIN gn_concepto c ON ce.cuenta = c.id_unico 
                    LEFT JOIN gn_novedad n ON c.id_unico = n.concepto 
                    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE e.tercero = '".$dter[$t][0]."' 
                        AND ce.concepto_exogenas = $id_c 
                        AND p.parametrizacionanno = $anno  
                        GROUP BY e.id_unico");
                    if(empty($bv[0][0])){
                        $valor =0;
                    } else {
                        $valor =$bv[0][0];
                    }
                    if($c==count($rowc)-1){
                        $html .=str_replace(',',' ',$valor);
                    } else {
                        $html .=str_replace(',',' ',$valor)."$separador";
                    }
                }
                $html .= "\n";
            }      
        }
        
        
        
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>TIPO DOCUMENTO DEL BENEFICIARIO</strong></center></td>';
            $h  .='<td><center><strong>NÚMERO DE IDENTIFICACIÓN DEL BENEFICARIO</strong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO DEL BENEFICIARIO</strong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO DEL BENEFICIARIO</strong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE DEL BENEFICIARIO</strong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES DEL BENEFICIARIO</strong></center></td>';
            $h  .='<td><center><strong>DIRECCIÓN BENEFICARIO</strong></center></td>';
            $h  .='<td><center><strong>DEPARTAMENTO BENEFICARIO</strong></center></td>';
            $h  .='<td><center><strong>MUNICIPIO BENEFICARIO</strong></center></td>';
            $h  .='<td><center><strong>PAÍS BENEFICARIO</strong></center></td>';
            $rowc = $con->Listar("SELECT DISTINCT  cn.codigo , cn.nombre 
                FROM  gf_concepto_exogenas cn 
                WHERE cn.formato =$formato 
                ORDER BY cn.codigo" );
            for ($t = 0; $t < count($rowc); $t++) {
                $h  .='<td><center><strong>'.$rowc[$t][1].'</strong></center></td>';
            }
            
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
    break;
    #* Predial
    case (1476):
        # *** Buscar Datos  *** #
        $dter = $con->Listar("SELECT 'PROPIETARIO',
            (SELECT td.codigo FROM 
             gp_tercero_predio tp
             LEFT JOIN gr_propietarios pp1  ON tp.tercero = pp1.id_unico 
             LEFT JOIN gf_tipo_identificacion td ON pp1.tipoidentificacion = td.id_unico 
             WHERE tp.predio = pr.id_unico AND tp.orden = '001' LIMIT 1) TI ,
            (SELECT pp1.numero FROM 
             gp_tercero_predio tp
             LEFT JOIN gr_propietarios pp1  ON tp.tercero = pp1.id_unico 
             WHERE tp.predio = pr.id_unico AND tp.orden = '001' LIMIT 1) IDENTIFICACION ,
            '','','',
            (SELECT pp1.nombres FROM 
             gp_tercero_predio tp
             LEFT JOIN gr_propietarios pp1  ON tp.tercero = pp1.id_unico 
             WHERE tp.predio = pr.id_unico AND tp.orden = '001' LIMIT 1) AS NOMBRES,'',
            pr.direccion,d.rss, ci.rss, 
            a.valor,a.valor,SUM(dp.valor), 
            pr.codigo_catastral,pr.codigo_catastral,pr.matricula_inmobiliaria,pr.codigoigac,
            pr.direccion,CONCAT_WS(' ', des.codigo, des.nombre),
            COUNT(DISTINCT tp.tercero) 
            FROM gr_pago_predial p 
            LEFT JOIN gr_detalle_pago_predial dp ON p.id_unico = dp.pago 
            LEFT JOIN gr_detalle_factura_predial df ON dp.detallefactura = df.id_unico 
            LEFT JOIN gr_concepto_predial cn ON df.concepto = cn.id_unico 
            LEFT JOIN gr_factura_predial f ON df.factura = f.id_unico 
            LEFT JOIN gp_predio1 pr ON f.predio = pr.id_unico 
            LEFT JOIN gf_parametrizacion_anno pa ON p.parametrizacionanno = pa.id_unico 
            LEFT JOIN gr_concepto c ON cn.id_concepto = c.id_unico 
            LEFT JOIN gr_avaluo a ON pr.id_unico = a.predio AND a.anno = pa.anno 
            LEFT JOIN gp_tercero_predio tp ON tp.predio = pr.id_unico             
            LEFT JOIN gf_tercero tc ON pa.compania = tc.id_unico 
            LEFT JOIN gf_ciudad ci ON tc.ciudadresidencia = ci.id_unico 
            LEFT JOIN gf_departamento d ON ci.departamento = d.id_unico 
            LEFT JOIN gr_destino_economico des ON pr.destino_economico = des.id_unico 
        WHERE p.parametrizacionanno = $anno AND cn.id_concepto = 1  GROUP BY pr.id_unico"); 
        for ($t = 0; $t < count($dter); $t++) {
            #   *****   Buscar Conceptos  ****   #
            if($exportar==3){
                $html .='<tr>';
                $html .='<td>'.$dter[$t][0].'</td>';
                $html .='<td>'.$dter[$t][1].'</td>';
                $html .='<td>'.$dter[$t][2].'</td>';
                $html .='<td>'.$dter[$t][3].'</td>';
                $html .='<td>'.$dter[$t][4].'</td>';
                $html .='<td>'.$dter[$t][5].'</td>';
                $html .='<td>'.$dter[$t][6].'</td>';
                $html .='<td>'.$dter[$t][7].'</td>';
                $html .='<td>'.$dter[$t][8].'</td>';
                $html .='<td>'.$dter[$t][9].'</td>';
                $html .='<td>'.$dter[$t][10].'</td>';
                $html .='<td>'.$dter[$t][11].'</td>';
                $html .='<td>'.$dter[$t][12].'</td>';
                $html .='<td>'.$dter[$t][13].'</td>';
                $html .='<td>'.$dter[$t][14].'</td>';
                $html .='<td>'.$dter[$t][15].'</td>';
                $html .='<td>'.$dter[$t][16].'</td>';
                $html .='<td>'.$dter[$t][17].'</td>';
                $html .='<td>'.$dter[$t][18].'</td>';
                $html .='<td>'.$dter[$t][19].'</td>';
                $html .='<td>'.$dter[$t][20].'</td>';
                
                
                $html .='</tr>';
            } else {
                $html .=str_replace(',',' ',$dter[$t][0])."$separador";
                $html .=str_replace(',',' ',$dter[$t][1])."$separador";
                $html .=str_replace(',',' ',$dter[$t][2])."$separador";
                $html .=str_replace(',',' ',$dter[$t][3])."$separador";
                $html .=str_replace(',',' ',$dter[$t][4])."$separador";
                $html .=str_replace(',',' ',$dter[$t][5])."$separador";
                $html .=str_replace(',',' ',$dter[$t][6])."$separador";
                $html .=str_replace(',',' ',$dter[$t][7])."$separador";
                $html .=str_replace(',',' ',$dter[$t][8])."$separador";
                $html .=str_replace(',',' ',$dter[$t][9])."$separador";
                $html .=str_replace(',',' ',$dter[$t][10])."$separador";
                $html .=str_replace(',',' ',$dter[$t][11])."$separador";
                $html .=str_replace(',',' ',$dter[$t][12])."$separador";
                $html .=str_replace(',',' ',$dter[$t][13])."$separador";
                $html .=str_replace(',',' ',$dter[$t][14])."$separador";
                $html .=str_replace(',',' ',$dter[$t][15])."$separador";
                $html .=str_replace(',',' ',$dter[$t][16])."$separador";
                $html .=str_replace(',',' ',$dter[$t][17])."$separador";
                $html .=str_replace(',',' ',$dter[$t][18])."$separador";
                $html .=str_replace(',',' ',$dter[$t][19])."$separador";
                $html .=str_replace(',',' ',$dter[$t][20]);
                $html .= "\n";
            }      
        }
        
        
        
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>TIPO DE RESPONSABLE </srong></center></td>';
            $h  .='<td><center><strong>TIPO DOCUMENTO RESPONSABLE </srong></center></td>';
            $h  .='<td><center><strong>NÚMERO IDENTIFICACIÓN </srong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO </srong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO </srong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE </srong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES </srong></center></td>';
            $h  .='<td><center><strong>RAZON SOCIAL </srong></center></td>';
            $h  .='<td><center><strong>DIRECCION </srong></center></td>';
            $h  .='<td><center><strong>DEPARTAMENTO </srong></center></td>';
            $h  .='<td><center><strong>MUNICIPIO </srong></center></td>';
            $h  .='<td><center><strong>VALOR AVALÚO CATASTRAL </srong></center></td>';
            $h  .='<td><center><strong>VALOR AVALÚO PREDIO </srong></center></td>';
            $h  .='<td><center><strong>VALOR IMPUESTO A CARGO </srong></center></td>';
            $h  .='<td><center><strong>NÚMERO PREDIAL NACIONAL </srong></center></td>';
            $h  .='<td><center><strong>CEDULA CATASTRAL </srong></center></td>';
            $h  .='<td><center><strong>MATRÍCULA INMOBILIARIA </srong></center></td>';
            $h  .='<td><center><strong>IDENTIFICACIÓN ASIGNADA POR ENTIDAD CATASTRAL </srong></center></td>';
            $h  .='<td><center><strong>UBICACIÓN DEL PREDIO </srong></center></td>';
            $h  .='<td><center><strong>DESTINO ECONÓMICO </srong></center></td>';
            $h  .='<td><center><strong>TOTAL PROPIETARIOS  </srong></center></td>';
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
    break;
    #*Comercio
    case (1481):
        $row = $con->Listar("SELECT DISTINCT  ti.codigo, 
            t.numeroidentificacion,t.apellidouno, t.apellidodos,
            t.nombreuno, t.nombredos, t.razonsocial,
            drc.direccion,dpr.rss, cd.rss, 
            (SELECT CONCAT_WS(' ',a.codigo, a.descripcion) 
             FROM gc_actividad_comercial a 
             LEFT JOIN gc_actividad_contribuyente ac ON a.id_unico =ac.actividad 
            WHERE ac.contribuyente = c.id_unico LIMIT 1) AS actividad ,
            COUNT(DISTINCT e.id_unico) as establecimiento,
            GROUP_CONCAT(DISTINCT(d.id_unico)),
            GROUP_CONCAT(DISTINCT(rc.id_unico)),GROUP_CONCAT(DISTINCT(c.id_unico ))
            FROM gc_recaudo_comercial rc 
            LEFT JOIN gc_detalle_recaudo dr ON rc.id_unico = dr.recaudo 
            LEFT JOIN gc_detalle_declaracion dc ON dr.det_dec = dc.id_unico 
            LEFT JOIN gc_declaracion d ON dc.declaracion = d.id_unico 
            LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico 
            LEFT JOIN gf_tercero t ON c.tercero = t.id_unico 
            LEFT JOIN gf_direccion drc ON t.id_unico = drc.id_unico 
            LEFT JOIN gf_ciudad cd ON drc.ciudad_direccion = cd.id_unico 
            LEFT JOIN gf_departamento dpr ON cd.departamento = dpr.id_unico 
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
            LEFT JOIN gc_establecimiento e ON e.contribuyente = c.id_unico 
            WHERE rc.clase = 1 AND rc.parametrizacionanno = $anno group by t.id_unico");
        for ($i = 0; $i < count($row); $i++) {
            #** 
            $decl   = $row[$i][12];
            $rec    = $row[$i][13];
            $cont   = $row[$i][14];
            #** Ingresos brutos jurisdiccion
            $ibj = $con->Listar("SELECT SUM(valor) 
                FROM gc_detalle_declaracion 
                WHERE declaracion in ($decl) AND concepto = 1 ");
            if(empty($ibj[0][0])){
                $ibjv = 0;
            } else {
                $ibjv = $ibj[0][0];
            }            
            #** Ingresos brutos otras jurisdicciones
            $ibjo = $con->Listar("SELECT SUM(valor) 
                FROM gc_detalle_declaracion 
                WHERE declaracion in ($decl) AND concepto = 2 ");
            if(empty($ibjo[0][0])){
                $ibjov = 0;
            } else {
                $ibjov = $ibjo[0][0];
            }
            #** Ingresos brutos  jurisdicciones
            $ibjt = $con->Listar("SELECT SUM(valor) 
                FROM gc_detalle_declaracion 
                WHERE declaracion in ($decl) AND concepto = 3 ");
            if(empty($ibjt[0][0])){
                $ibjtv = 0;
            } else {
                $ibjtv = $ibjt[0][0];
            }
            #** Devoluciones Deducciones 
            $ibjd = $con->Listar("SELECT SUM(valor) 
                FROM gc_detalle_declaracion 
                WHERE declaracion in ($decl) AND concepto = 4 ");
            if(empty($ibjd[0][0])){
                $ibjdv = 0;
            } else {
                $ibjdv = $ibjd[0][0];
            }
            #** Impuesto industria y comercio a cargo27
            $ibjc = $con->Listar("SELECT GROUP_CONCAT(id_unico),
                    SUM(valor) 
                FROM gc_detalle_declaracion 
                WHERE declaracion in ($decl) AND concepto = 27 ");
            $dt ="0";
            if(empty($ibjc[0][1])){
                $ibjcv = 0;
            } else {
                $ibjcv = $ibjc[0][1];
                $dt    = $ibjc[0][0];
            }
            #** Impuesto pagado
            $ibjp = $con->Listar("SELECT SUM(valor) 
                FROM gc_detalle_recaudo  
                WHERE det_dec in ($dt) ");
            if(empty($ibjp[0][1])){
                $ibjpv = 0;
            } else {
                $ibjpv = $ibjp[0][1];
            }
            if($exportar==3){
                $html .='<tr>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][1].'</td>';
                $html .='<td>'.$row[$i][2].'</td>';
                $html .='<td>'.$row[$i][3].'</td>';
                $html .='<td>'.$row[$i][4].'</td>';
                $html .='<td>'.$row[$i][5].'</td>';
                $html .='<td>'.$row[$i][6].'</td>';
                $html .='<td>'.$row[$i][7].'</td>';
                $html .='<td>'.$row[$i][8].'</td>';
                $html .='<td>'.$row[$i][9].'</td>';
                $html .='<td>'.$row[$i][10].'</td>';
                $html .='<td>'.$row[$i][11].'</td>';
                
                $html .='<td>'.$ibjv.'</td>';
                $html .='<td>'.$ibjov.'</td>';
                $html .='<td>'.$ibjdv.'</td>';
                $html .='<td>'.$ibjtv.'</td>';
                $html .='<td>'.$ibjcv.'</td>';
                $html .='<td>'.$ibjcv.'</td>';
                $html .='</tr>';
            } else {
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=str_replace(',',' ',$row[$i][2])."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=str_replace(',',' ',$row[$i][4])."$separador";
                $html .=str_replace(',',' ',$row[$i][5])."$separador";
                $html .=str_replace(',',' ',$row[$i][6])."$separador";
                $html .=str_replace(',',' ',$row[$i][7])."$separador";
                $html .=str_replace(',',' ',$row[$i][8])."$separador";
                $html .=str_replace(',',' ',$row[$i][9])."$separador";
                $html .=str_replace(',',' ',$row[$i][10])."$separador";
                $html .=str_replace(',',' ',$row[$i][11])."$separador";
                $html .=str_replace(',',' ',$ibjv)."$separador";
                $html .=str_replace(',',' ',$ibjov)."$separador";
                $html .=str_replace(',',' ',$ibjdv)."$separador";
                $html .=str_replace(',',' ',$ibjtv)."$separador";
                $html .=str_replace(',',' ',$ibjcv)."$separador";
                $html .=str_replace(',',' ',$ibjcv);
                $html .= "\n";
            }
        }
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>TIPO DOCUMENTO  </srong></center></td>';
            $h  .='<td><center><strong>NÚMERO IDENTIFICACIÓN </srong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO </srong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO </srong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE </srong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES </srong></center></td>';
            $h  .='<td><center><strong>RAZON SOCIAL </srong></center></td>';
            $h  .='<td><center><strong>DIRECCION </srong></center></td>';
            $h  .='<td><center><strong>DEPARTAMENTO </srong></center></td>';
            $h  .='<td><center><strong>MUNICIPIO </srong></center></td>';
            $h  .='<td><center><strong>ACTIVIDAD ECONÓMICA </srong></center></td>';
            $h  .='<td><center><strong>NÚMERO ESTABLECIMIENTOS</srong></center></td>';
            $h  .='<td><center><strong>INGRESOS BRUTOS JURIDISDICCIÓN </srong></center></td>';
            $h  .='<td><center><strong>INGRESOS BRUTOS OTRAS JURIDISDICCIONES </srong></center></td>';
            $h  .='<td><center><strong>DEVOLUCIONES DEDUCCIONES JURISDICCIÓN </srong></center></td>';
            $h  .='<td><center><strong>INGRESOS NETOS JURIDSDICCIÓN </srong></center></td>';
            $h  .='<td><center><strong>IMPUESTO INDUSTRIA Y COMERCIO A CARGO </srong></center></td>';
            $h  .='<td><center><strong>IMPUESTO DE INDUSTRIA Y COMERCIO PAGADO </srong></center></td>';
            
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
    break;

    case(1012):
        #   *****   Buscar Conceptos  ****   #
        $rowc = $con->Listar("SELECT DISTINCT 
            ce.concepto_exogenas, cn.codigo , GROUP_CONCAT(ce.cuenta) 
            FROM gf_configuracion_exogenas ce 
            LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
            WHERE cn.formato =$formato 
            GROUP BY ce.concepto_exogenas" );
        
        for ($i = 0; $i < count($rowc); $i++) {
            $concepto = $rowc[$i][0];
            $codigo   = $rowc[$i][1];
            $cuentas  = $rowc[$i][2];
            $valor_cuantia =0;
            #   ****    Buscar Terceros     ****    #
            $rowt = $con->Listar("SELECT DISTINCT 
                dc.tercero 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante 
                WHERE dc.cuenta IN ($cuentas) AND cn.parametrizacionanno = $anno 
                AND (tc.clasecontable !=20) ");
            if(count($rowt)>0){
                for ($z = 0; $z < count($rowt); $z++) {
                    if(!empty($rowt[$z][0])){
                        $imprimir   = 0;
                        $suman      = valorPI($cuentas, $rowt[$z][0], '+');
                        $restan     = valorPI($cuentas, $rowt[$z][0], '-');
                        $total      = $suman - ($restan*-1);
                        
                        if(!empty($cuantia)){ 
                            if($total<=$cuantia || $rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantia +=$total;
                            }
                            
                        } else {
                            if($rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantia +=$total;
                            }
                        }
                        if($imprimir==0) {
                            if($total!=0) { 
                                # *** Buscar Datos Tercero *** #
                                $dter = $con->Listar("SELECT DISTINCT 
                                    t.id_unico, t.nombreuno,
                                    t.nombredos,  t.apellidouno,
                                    t.apellidodos, t.razonsocial, 
                                    t.digitoverficacion, ti.codigo, 
                                    t.numeroidentificacion, 
                                    GROUP_CONCAT(d.direccion),
                                    ci.rss, dp.rss 
                                    FROM gf_tercero t 
                                    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                                    LEFT JOIN gf_direccion d ON d.tercero = t.id_unico 
                                    LEFT JOIN gf_ciudad ci ON d.ciudad_direccion = ci.id_unico 
                                    LEFT JOIN gf_departamento dp ON ci.departamento = dp.id_unico 
                                    WHERE t.id_unico = ".$rowt[$z][0]);
                                if($exportar==3){
                                    $html .='<tr>';
                                    $html .='<td>'.$codigo.'</td>';
                                    $html .='<td>'.$dter[0][7].'</td>';
                                    $html .='<td>'.$dter[0][8].'</td>';
                                    $html .='<td>'.$dter[0][6].'</td>';
                                    $html .='<td>'.$dter[0][3].'</td>';
                                    $html .='<td>'.$dter[0][4].'</td>';
                                    $html .='<td>'.$dter[0][1].'</td>';
                                    $html .='<td>'.$dter[0][2].'</td>';
                                    $html .='<td>'.$dter[0][5].'</td>';                                 
                                    $html .='<td>169</td>';
                                    $html .='<td>'.round($total,0).'</td>';
                                    $html .='</tr>';
                                } else {
                                    $html .=str_replace(',',' ',$codigo)."$separador";
                                    $html .=str_replace(',',' ',$dter[0][7])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][8])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][6])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][3])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][4])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][1])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][2])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][5])."$separador";                           
                                    $html .='169'."$separador";
                                    $html .=round($total,0);
                                    $html .= "\n";
                                }
                            }
                        }
                    }
                }
            }
            if($valor_cuantia!=0){
                if($exportar==3){
                    $html .='<tr>';
                    $html .='<td>'.$codigo.'</td>';
                    $html .='<td>43</td>';
                    $html .='<td>222222222</td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td>Cuantías menores</td>';
                    $html .='<td>169</td>';
                    $html .='<td>'.round($valor_cuantia,0).'</td>';
                    $html .='</tr>';
                } else {
                    $html .=str_replace(',',' ',$codigo)."$separador";
                    $html .='43'."$separador";
                    $html .='222222222'."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .='Cuantías menores'."$separador";
                    $html .='169'."$separador";
                    $html .=round($valor_cuantia,0);
                    $html .= "\n";
                }
            }
        }
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>CONCEPTO</strong></center></td>';
            $h  .='<td><center><strong>TIPO DE DOCUMENTO</strong></center></td>';
            $h  .='<td><center><strong>NIT INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>DV</strong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>RAZÓN SOCIAL DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>PAIS DE RESIDENCIA O DOMICILIO</strong></center></td>';
            $h  .='<td><center><strong>VALOR AL 31-12</strong></center></td>';
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
    break;
    case(1003):
        #   *****   Buscar Conceptos  ****   #
        $rowc = $con->Listar("SELECT DISTINCT 
            ce.concepto_exogenas, cn.codigo , GROUP_CONCAT(ce.cuenta) 
            FROM gf_configuracion_exogenas ce 
            LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
            WHERE cn.formato =$formato 
            GROUP BY ce.concepto_exogenas" );
        
        for ($i = 0; $i < count($rowc); $i++) {
            $concepto = $rowc[$i][0];
            $codigo   = $rowc[$i][1];
            $cuentas  = $rowc[$i][2];
            $valor_cuantia =0;
            #   ****    Buscar Terceros     ****    #
            $rowt = $con->Listar("SELECT DISTINCT 
                dc.tercero 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico = cn.tipocomprobante 
                WHERE dc.cuenta IN ($cuentas) AND cn.parametrizacionanno = $anno 
                AND (tc.clasecontable !=20) AND tc.clasecontable=9");
            if(count($rowt)>0){
                for ($z = 0; $z < count($rowt); $z++) {
                    if(!empty($rowt[$z][0])){
                        $imprimir   = 0;

                        $rowv = $con->Listar("SELECT DISTINCT r.id_unico, r.valorretencion, r.retencionbase FROM gf_comprobante_cnt cn 
                        LEFT JOIN gf_detalle_comprobante dc oN cn.id_unico = dc.comprobante 
                        LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                        LEFT JOIN gf_retencion r ON r.comprobante = cn.id_unico
                        LEFT JOIN gf_tipo_retencion tr ON tr.id_unico = r.tiporetencion 
                        LEFT JOIN gf_cuenta c ON c.id_unico = tr.cuenta
                        WHERE tc.clasecontable = 9 and cn.parametrizacionanno = $anno 
                        AND tr.cuenta in ($cuentas) AND cn.tercero = ".$rowt[$z][0]);
                        $vr = 0;
                        $rb = 0;
                        for ($v=0; $v < count($rowv); $v++) { 
                            $vr += $rowv[$v][1];
                            $rb += $rowv[$v][2];
                        }

                        $total      = $vr;
                        
                        if(!empty($cuantia)){ 
                            if($total<=$cuantia || $rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantia +=$total;
                            }
                            
                        } else {
                            if($rowt[$z][0]=='2'){
                                $imprimir = 1;
                                $valor_cuantia +=$total;
                            }
                        }
                        if($imprimir==0) {
                            if($total!=0) { 
                                # *** Buscar Datos Tercero *** #
                                $dter = $con->Listar("SELECT DISTINCT 
                                    t.id_unico, t.nombreuno,
                                    t.nombredos,  t.apellidouno,
                                    t.apellidodos, t.razonsocial, 
                                    t.digitoverficacion, ti.codigo, 
                                    t.numeroidentificacion, 
                                    GROUP_CONCAT(d.direccion),
                                    ci.rss, dp.rss 
                                    FROM gf_tercero t 
                                    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                                    LEFT JOIN gf_direccion d ON d.tercero = t.id_unico 
                                    LEFT JOIN gf_ciudad ci ON d.ciudad_direccion = ci.id_unico 
                                    LEFT JOIN gf_departamento dp ON ci.departamento = dp.id_unico 
                                    WHERE t.id_unico = ".$rowt[$z][0]);
                                if($exportar==3){
                                    $html .='<tr>';
                                    $html .='<td>'.$codigo.'</td>';
                                    $html .='<td>'.$dter[0][7].'</td>';
                                    $html .='<td>'.$dter[0][8].'</td>';
                                    $html .='<td>'.$dter[0][6].'</td>';
                                    $html .='<td>'.$dter[0][3].'</td>';
                                    $html .='<td>'.$dter[0][4].'</td>';
                                    $html .='<td>'.$dter[0][1].'</td>';
                                    $html .='<td>'.$dter[0][2].'</td>';
                                    $html .='<td>'.$dter[0][5].'</td>';
                                    $html .='<td>'.$dter[0][9].'</td>';
                                    $html .='<td>'.$dter[0][11].'</td>';
                                    $html .="<td style='mso-number-format:\@'>".$dter[0][10]."</td>";                                    
                                    $html .='<td>'.round($rb,0).'</td>';
                                    $html .='<td>'.round($total,0).'</td>';
                                    $html .='</tr>';
                                } else {
                                    $html .=str_replace(',',' ',$codigo)."$separador";
                                    $html .=str_replace(',',' ',$dter[0][7])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][8])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][6])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][3])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][4])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][1])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][2])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][5])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][9])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][11])."$separador";
                                    $html .=str_replace(',',' ',$dter[0][10])."$separador";                                    
                                    $html .=round($rb,0)."$separador";
                                    $html .=round($total,0);
                                    $html .= "\n";
                                }
                            }
                        }
                    }
                }
            }
            if($valor_cuantia!=0){
                if($exportar==3){
                    $html .='<tr>';
                    $html .='<td>'.$codigo.'</td>';
                    $html .='<td>43</td>';
                    $html .='<td>222222222</td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td>Cuantías menores</td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td></td>';
                    $html .='<td>169</td>';
                    $html .='<td>'.round($valor_cuantia,0).'</td>';
                    $html .='</tr>';
                } else {
                    $html .=str_replace(',',' ',$codigo)."$separador";
                    $html .='43'."$separador";
                    $html .='222222222'."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .='Cuantías menores'."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .=''."$separador";
                    $html .='169'."$separador";
                    $html .=round($valor_cuantia,0);
                    $html .= "\n";
                }
            }
        }
        if($exportar==3){
            $h  =   "";
            $h  .='<tr>';
            $h  .='<td><center><strong>CONCEPTO</strong></center></td>';
            $h  .='<td><center><strong>TIPO DE DOCUMENTO</strong></center></td>';
            $h  .='<td><center><strong>NÚMERO DE IDENTIFICACIÓN DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>DV</strong></center></td>';
            $h  .='<td><center><strong>PRIMER APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>SEGUNDO APELLIDO DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>PRIMER NOMBRE DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>OTROS NOMBRES DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>RAZÓN SOCIAL DEL INFORMADO</strong></center></td>';
            $h  .='<td><center><strong>DIRECCION</strong></center></td>';
            $h  .='<td><center><strong>CÓDIGO DPTO</strong></center></td>';
            $h  .='<td><center><strong>CÓDIGO MCP</strong></center></td>';
            $h  .='<td><center><strong>VALOR DEL PAGO O ABONO SUJETO A RETENCIÓN EN LA FUENTE</strong></center></td>';
            $h  .='<td><center><strong>RETENCIÓN QUE LE PRACTICARON</strong></center></td>';
            $h  .='</tr>';
            $htmle ="";
            $htmle .=$h;
            $htmle .=$html;
        }
        
    break;
}
switch ($exportar){
    #*** csv ***#
    case 1:
        header("Content-Disposition: attachment; filename=Informe_Exogenas_$codigo_formato.csv");
        ini_set('max_execution_time', 0);
        echo $html;
    break;
    #*** txt ***#   
    case 2:
        header("Content-Disposition: attachment; filename=Informe_Exogenas_$codigo_formato.txt");
        ini_set('max_execution_time', 0);
        echo $html;
    break;
    #*** xls ***#
    case 3:
        header("Content-Disposition: attachment; filename=Informe_Exogenas_$codigo_formato.xls");
        ini_set('max_execution_time', 0);
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Informe Exógenas</title>
            </head>
            <body>
                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <?php echo $htmle; ?>
                </table>
            </body>
        </html>
        <?php             
    break;
}
function valor($cuentas, $tercero, $s){
    @session_start();
    global    $con;
    global    $anno;
    $valor    = 0;
    if($s=='+'){ 
        $vl     = $con->Listar("SELECT SUM(valor)
            FROM
              gf_detalle_comprobante dc
            LEFT JOIN
              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
            LEFT JOIN 
              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico 
            WHERE dc.valor>0 
                AND dc.tercero = $tercero 
                AND dc.cuenta IN ($cuentas) 
                AND cp.parametrizacionanno =$anno 
                AND (tc.clasecontable !=5 AND tc.clasecontable !=20)");
    } else {
        $vl     = $con->Listar("SELECT SUM(valor)
            FROM
              gf_detalle_comprobante dc
            LEFT JOIN
              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
            LEFT JOIN 
              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico 
            WHERE dc.valor<0 
                AND dc.tercero = $tercero 
                AND dc.cuenta IN ($cuentas) 
                AND cp.parametrizacionanno =$anno 
                AND (tc.clasecontable !=5 AND tc.clasecontable !=20)");
    }
    if(count($vl)>0){
        if($vl[0][0]!=""){
            $valor = $vl[0][0];
        }
    }
    return $valor;
    
}
function valorPI($cuentas, $tercero, $s){
    @session_start();
    global    $con;
    global    $anno;
    $valor    = 0;
    if($s=='+'){ 
        $vl     = $con->Listar("SELECT SUM(valor)
            FROM
              gf_detalle_comprobante dc
            LEFT JOIN
              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
            LEFT JOIN 
              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico 
            WHERE dc.valor>0 
                AND dc.tercero = $tercero 
                AND dc.cuenta IN ($cuentas) 
                AND cp.parametrizacionanno =$anno 
                AND (tc.clasecontable !=20)");
    } else {
        $vl     = $con->Listar("SELECT SUM(valor)
            FROM
              gf_detalle_comprobante dc
            LEFT JOIN
              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
            LEFT JOIN 
              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico 
            WHERE dc.valor<0 
                AND dc.tercero = $tercero 
                AND dc.cuenta IN ($cuentas) 
                AND cp.parametrizacionanno =$anno 
                AND (tc.clasecontable !=20 )");
    }
    if(count($vl)>0){
        if($vl[0][0]!=""){
            $valor = $vl[0][0];
        }
    }
    return $valor;
    
}

function valor_total($cuentas, $s){
    @session_start();
    global    $con;
    global    $anno;
    $valor    = 0;
    if($s=='+'){ 
        $vl     = $con->Listar("SELECT SUM(valor)
            FROM
              gf_detalle_comprobante dc
            LEFT JOIN
              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
            LEFT JOIN 
              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico 
            WHERE dc.valor>0 
                AND dc.cuenta IN ($cuentas) 
                AND cp.parametrizacionanno =$anno 
                AND (tc.clasecontable !=5)");
    } else {
        $vl     = $con->Listar("SELECT SUM(valor)
            FROM
              gf_detalle_comprobante dc
            LEFT JOIN
              gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico 
            LEFT JOIN 
              gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico 
            WHERE dc.valor<0 
                AND dc.cuenta IN ($cuentas) 
                AND cp.parametrizacionanno =$anno 
                AND (tc.clasecontable !=5)");
    }
    if(count($vl)>0){
        if($vl[0][0]!=""){
            $valor = $vl[0][0];
        }
    }
    return $valor;
}


