<?php 
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
     
ini_set('max_execution_time', 0);
session_start(); 
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$compania = $_SESSION['compania'];

#**********Recepción Variables ****************#
$informe    = $_POST['sltInforme'];
$exportar   = $_POST['sltExportar'];
$separador  = $_POST['separador'];
$periodo   = $_POST['periodo'];

if($separador == 'tab') {	
    $separador = "\t";		
}
if($separador == '1') {   
    $separador = "";      
}
/*fecha periodo*/
$rowP = $con->Listar("SELECT fechainicio,fechafin  FROM gn_periodo where id_unico = $periodo ");

#**********************************************#
switch ($informe){
    #**** PILA NOMINA EMPLEADOS *****# 
    case 1:
    /*#***************************    Encabezado *************************************/
        $rowE = $con->Listar("SELECT razonsocial,
               numeroidentificacion,
               digitoverficacion,
               (SELECT valor FROM gs_parametros_basicos where nombre ='Codigo ARL de la compania') cod_arl,
               (SELECT sum(n.valor) from  gn_novedad n where n.concepto =74 and n.periodo = $periodo and (SELECT empleado FROM gn_pensionado where empleado = n.empleado) is null)vlr_nom
            FROM gf_tercero where id_unico = $compania");
        
        $html;
        $campo1= "01";
        $campo2= "0";
        $campo3= "0001";
        /*contar longitud*/
        $razon = $rowE[0][0];
        $lng_rz= strlen ($razon);
        $esp_rz="";
        for($y=$lng_rz; $y < 200; $y++){
            $esp_rz = $esp_rz." ";
        }
        $campo4= $razon.$esp_rz;
        $campo5= "NI";
        /*contar longitud*/
        $nit = $rowE[0][1];
        $lng_nt= strlen ($nit);
        $esp_nt="";
        for($y=$lng_nt; $y < 16; $y++){
            $esp_nt = $esp_nt." ";
        }
        $campo6= $nit.$esp_nt;
        $campo7= $rowE[0][2];
        $campo8= "E";
        $campo9= "          ";
        $campo10= "          ";
        $campo11= "";
        $campo12= "U         ";
        $campo13= "                                         ";
        /*contar longitud*/
        $cod_arl_em = $rowE[0][3];
        $lng_cae= strlen ($cod_arl_em);
        $esp_cae="";
        for($y=$lng_cae; $y < 6; $y++){
            $esp_cae = $esp_cae." ";
        }
        $campo14= $cod_arl_em.$esp_cae;

        /*fecha periodo*/
        $fc = $rowP[0][0];
        $fip=$rowP[0][0];
        $ffip=$rowP[0][1];
        $fecha_div = explode("-", $fc);
        $anioi  = $fecha_div[0];
        $anioi2 = $fecha_div[0];
        $mesi   = $fecha_div[1];
        $mesi2  = $fecha_div[1];
        $diai   = $fecha_div[2];

        if($mesi2 == 12){
            $anioi2 = $anioi + 1;
            $mesi2  = "01";
        }else{
            $anioi2 = $anioi;
            $mesi2 = $mesi2 + 1;
            if($mesi2 < 10){
                $mesi2 = "0".$mesi2;
            }
        }

        $rowPVAC = $con->Listar("SELECT id_unico,fechainicio,fechafin  FROM gn_periodo where fechainicio >= '$fip' AND fechafin  <= '$ffip' AND tipoprocesonomina =7 ");
        $id_unico_prd_vac=  $rowPVAC[0][0];

        $campo15= $anioi.'-'.$mesi;
        $campo16= $anioi2.'-'.$mesi2;
        $campo17= "          ";
        $campo18= "          ";

        /*numero de empleados*/
        $rowNE = $con->Listar("SELECT DISTINCT n.empleado FROM gn_novedad n where n.periodo = $periodo AND (SELECT empleado FROM gn_pensionado where empleado = n.empleado) is null");
        $num_emp = count($rowNE);
        /*contar longitud*/
        $lng_ne= strlen ($num_emp);
        $esp_ne="";
        for($y=$lng_ne; $y < 5; $y++){
            $esp_ne = $esp_ne."0";
        }
        $campo19=$esp_ne.$num_emp;
        /*contar longitud*/
        $vl_nom = $rowE[0][4];
        $vl_nom = round($vl_nom/1);    
        $lng_vln= strlen ($vl_nom);
        $esp_vln="";

        for($y=$lng_vln; $y < 12; $y++){
            $esp_vln = $esp_vln."0";
        }

        $campo20= $esp_vln.$vl_nom;
        $campo21= "01";
        $campo22= "00";

        if($exportar==3){
            $html .='<tr>';
            $html .='<td>'.$campo1.'</td>';
            $html .='<td>'.$campo2.'</td>';
            $html .='<td>'.$campo3.'</td>';
            $html .='<td>'.$campo4.'</td>';
            $html .='<td>'.$campo5.'</td>';
            $html .='<td>'.$campo6.'</td>';
            $html .='<td>'.$campo7.'</td>';
            $html .='<td>'.$campo8.'</td>';
            $html .='<td>'.$campo9.'</td>';
            $html .='<td>'.$campo10.'</td>';
            $html .='<td>'.$campo11.'</td>';
            $html .='<td>'.$campo12.'</td>';
            $html .='<td>'.$campo13.'</td>';
            $html .='<td>'.$campo14.'</td>';
            $html .='<td>'.$campo15.'</td>';
            $html .='<td>'.$campo16.'</td>';
            $html .='<td>'.$campo17.'</td>';
            $html .='<td>'.$campo18.'</td>';
            $html .='<td>'.$campo19.'</td>';
            $html .='<td>'.$campo20.'</td>';
            $html .='<td>'.$campo21.'</td>';
            $html .='<td>'.$campo22.'</td>';                
            $html .='</tr>';            
        } else{ 
            $html .=str_replace(',',' ',$campo1)."$separador";
            $html .=str_replace(',',' ',$campo2)."$separador";
            $html .=str_replace(',',' ',$campo3)."$separador";
            $html .=str_replace(',',' ',$campo4)."$separador";
            $html .=str_replace(',',' ',$campo5)."$separador";
            $html .=str_replace(',',' ',$campo6)."$separador";
            $html .=str_replace(',',' ',$campo7)."$separador";
            $html .=str_replace(',',' ',$campo8)."$separador";
            $html .=str_replace(',',' ',$campo9)."$separador";
            $html .=str_replace(',',' ',$campo10)."$separador";
            $html .=str_replace(',',' ',$campo11)."$separador";
            $html .=str_replace(',',' ',$campo12)."$separador";
            $html .=str_replace(',',' ',$campo13)."$separador";
            $html .=str_replace(',',' ',$campo14)."$separador";
            $html .=str_replace(',',' ',$campo15)."$separador";
            $html .=str_replace(',',' ',$campo16)."$separador";
            $html .=str_replace(',',' ',$campo17)."$separador";
            $html .=str_replace(',',' ',$campo18)."$separador";
            $html .=str_replace(',',' ',$campo19)."$separador";
            $html .=str_replace(',',' ',$campo20)."$separador";
            $html .=str_replace(',',' ',$campo21)."$separador";
            $html .=str_replace(',',' ',$campo22);
            $html .="\r\n";
        }
    /*#*************************** FIN  Encabezado *************************************/

    /*#*************************** Cuerpo *************************************/
        //Empleados Con Vacaciones'$fip' AND fechafin  <= '$ffip'
        $emp = '0';
        $rowev = $con->Listar("SELECT GROUP_CONCAT(DISTINCT v.empleado) FROM gn_vacaciones v where v.fechainiciodisfrute BETWEEN '$fip' and '$ffip' OR v.fechafindisfrute BETWEEN '$fip' and '$ffip'");
        
        if(!empty($rowev[0][0])){
            $emp .=','.$rowev[0][0]; 
        }
        //Empleados Con Incapacidad
        $rowin = $con->Listar("SELECT GROUP_CONCAT(DISTINCT v.empleado) FROM gn_incapacidad v where v.fechainicio BETWEEN '$fip' and '$ffip' OR v.fechafinal BETWEEN '$fip' and '$ffip'");
        if(!empty($rowev[0][0])){
            $emp .=','.$rowin[0][0]; 
        }
        
        /*Detalle informe*/
        $row = $con->Listar("SELECT DISTINCT n.empleado,
                ti.sigla,
                t.numeroidentificacion,
                d.rss,
                c.rss,
                t.apellidouno,
                t.apellidodos,
                t.nombreuno,
                t.nombredos,
                (SELECT fecha FROM gn_vinculacion_retiro vr where vr.empleado = n.empleado and vr.estado = 1 and vr.vinculacionretiro is null order by vr.fecha DESC LIMIT 1) as fingreso,
                (SELECT fecha FROM gn_vinculacion_retiro vr where vr.empleado = n.empleado and vr.estado = 2 order by vr.fecha DESC LIMIT 1) as fretiro,
                ct.fecha_modificacion,
                (SELECT SUM(valor) FROM gn_novedad nv where nv.concepto IN (78) and nv.periodo = n.periodo and nv.empleado = n.empleado) as IBCSP,
                (SELECT SUM(valor) FROM gn_novedad nv where nv.concepto IN (1,65) and nv.periodo = n.periodo and nv.empleado = n.empleado) as salario,
                (SELECT valor FROM gn_novedad nv where nv.concepto = 7 and nv.periodo = n.periodo and nv.empleado = n.empleado) as dias_tb,
                e.salInt,
                (SELECT valor FROM gn_novedad nv where nv.concepto = 458 and nv.periodo = n.periodo and nv.empleado = n.empleado) as IBC_Parafiscales,
                (SELECT (asaludemple+asaludempre) salud FROM gn_parametros_liquidacion pl where vigencia = $anno AND pl.tipo_empleado = et.tipo) psalud,
                (SELECT sum(valor) FROM gn_novedad nv LEFT JOIN gn_concepto c ON nv.concepto = c.id_unico  where nv.periodo = n.periodo and nv.empleado = n.empleado AND c.codigo IN (810)) aporte_salud,
                
                (SELECT (apensionemple+apensionempre) pension FROM gn_parametros_liquidacion pl where vigencia = $anno AND pl.tipo_empleado = et.tipo) ppension,                
                (SELECT sum(valor) FROM gn_novedad nv LEFT JOIN gn_concepto c ON nv.concepto = c.id_unico where  nv.periodo = n.periodo  and nv.empleado = n.empleado AND c.codigo IN (811,813)) aporte_pension,
                (SELECT sum(valor) FROM gn_novedad nv where nv.concepto = 361 and nv.periodo = n.periodo and nv.empleado = n.empleado) aporte_fondo_sol,
                cr.valor,
                (SELECT sum(valor) FROM gn_novedad nv where nv.concepto = 363 and nv.periodo = n.periodo and nv.empleado = n.empleado) aporte_riesgos_pr,
                (SELECT acajacomp caja_comp FROM gn_parametros_liquidacion pl where vigencia = $anno AND pl.tipo_empleado = et.tipo) pcaja_comp,
                (SELECT sum(valor) FROM gn_novedad nv LEFT JOIN gn_concepto c ON nv.concepto = c.id_unico  where nv.periodo = n.periodo and nv.empleado = n.empleado AND c.codigo IN (819)) aporte_caja_com,
                (SELECT asena  FROM gn_parametros_liquidacion pl where vigencia = $anno AND pl.tipo_empleado = et.tipo) psena,
                (SELECT sum(valor) FROM gn_novedad nv where nv.concepto = 257 and nv.periodo = n.periodo and nv.empleado = n.empleado) aporte_sena,
                (SELECT aicbf  FROM gn_parametros_liquidacion pl where vigencia = $anno AND pl.tipo_empleado = et.tipo) picbf,
                (SELECT sum(valor) FROM gn_novedad nv where nv.concepto = 260 and nv.periodo = n.periodo and nv.empleado = n.empleado) aporte_icbf,
                (SELECT aesap  FROM gn_parametros_liquidacion pl where vigencia = $anno AND pl.tipo_empleado = et.tipo) pesap,
                (SELECT sum(valor) FROM gn_novedad nv where nv.concepto = 259 and nv.periodo = n.periodo and nv.empleado = n.empleado) aporte_esap,
                (SELECT aministerio  FROM gn_parametros_liquidacion pl where vigencia = $anno AND pl.tipo_empleado = et.tipo) pministerio,
                (SELECT sum(valor) FROM gn_novedad nv where nv.concepto = 258 and nv.periodo = n.periodo and nv.empleado = n.empleado) aporte_ministerio,
                (SELECT salmin FROM gn_parametros_liquidacion pl where vigencia = $anno AND pl.tipo_empleado = et.tipo) salario_min,
                cr.centro_trab
                FROM gn_novedad n 
                LEFT JOIN gn_empleado e on e.id_unico = n.empleado
                LEFT JOIN gf_tercero t on t.id_unico = e.tercero
                LEFT JOIN gf_tipo_identificacion ti on ti.id_unico = t.tipoidentificacion
                LEFT join gf_ciudad c on c.id_unico = t.ciudadresidencia
                LEFT JOIN gf_departamento d on d.id_unico = c.departamento
                LEFT join gn_tercero_categoria tc on tc.empleado = e.id_unico
                LEFT join gn_categoria ct on ct.id_unico = tc.categoria
                LEFT JOIN gn_categoria_riesgos cr on cr.id_unico = e.tipo_riesgo                
                LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                where n.periodo = $periodo AND (SELECT empleado FROM gn_pensionado where empleado = n.empleado) is null");


        //AND t.numeroidentificacion = 46666183
        $cont = 1;

        for ($i = 0; $i < count($row); $i++) {
            $emp =$row[$i][0];
            #** Campo 1 - Tipo Registro
            $campoD1 ="02";

            #** Campo 2 Secuencia 
             /*contar longitud*/
            $contador = $cont;
            $lng_cont = strlen ($contador);
            $esp_cont ="";
            for($y=$lng_cont; $y < 5; $y++){
                $esp_cont = $esp_cont."0";
            }
            $campoD2 = $esp_cont.$cont;

            #** Campo 3 - Tipo Documento 
            $td = str_replace('.','',$row[$i][1]);
            $td = substr($td, 0, 2);  
            $campoD3 = $td;      
            /*contar logitud*/
            $cc = $row[$i][2];
            $lng_cc = strlen ($cc);
            $esp_cc ="";
            for($y=$lng_cc; $y < 16; $y++){
                $esp_cc = $esp_cc." ";
            }

            #** Campo 4 - Número de Identificacion
            $campoD4 = $cc.$esp_cc;

            #** Campo 5 - Tipo Cotizante
            $campoD5 = "01";

            #** Campo 6 - Subtipo Cotizante
            $campoD6 = "00";

            #** Campo 7 - Extranjero no obligado a cotizar a pensiones
            $campoD7 = " ";

            #** Campo 8 - Colombiano en el exterior
            $campoD8 = " ";

            #** Campo 9 - Código del departamento de la ubicación laboral            
            /*contar logitud*/
            $rssd = $row[$i][3];
            $lng_rssd = strlen ($rssd);
            $esp_rssd ="";
            for($y=$lng_rssd; $y < 2; $y++){
                $esp_rssd = $esp_rssd." ";
            }
            $campoD9 = $rssd.$esp_rssd;

            #** Campo 10 - Código del municipio de ubicación laboral
            /*contar logitud*/
            $rssc = $row[$i][4];
            $lng_rssc = strlen ($rssc);
            $esp_rssc ="";
            for($y=$lng_rssc; $y < 3; $y++){
                $esp_rssc = $esp_rssc." ";
            }
            $campoD10 = $rssc.$esp_rssc;

            #** Campo 11  - Primer Apellido
            /*contar logitud*/
            $ap1 = $row[$i][5];
            $lng_ap1 = strlen ($ap1);
            $esp_ap1 ="";
            $num_carc = 20;
            if(substr_count($ap1,"Ñ")>0){
                $num_carc = 20 + substr_count($ap1,"Ñ");
            }
            for($y=$lng_ap1; $y < $num_carc; $y++){
                $esp_ap1 = $esp_ap1." ";
            }
            $campoD11 = $ap1.$esp_ap1;

            #** Campo 12  - Segundo Apellido
            /*contar logitud*/
            $ap2 = $row[$i][6];
            $lng_ap2 = strlen ($ap2);
            $esp_ap2 ="";
            $num_carc = 30;
            if(substr_count($ap2,"Ñ")>0){
                $num_carc = 30 + substr_count($ap2,"Ñ");
            }
            for($y=$lng_ap2; $y < $num_carc; $y++){
                $esp_ap2 = $esp_ap2." ";
            }
            $campoD12 = $ap2.$esp_ap2;

            #** Campo 13  - Primer Nombre 
            /*contar logitud*/
            $n1 = $row[$i][7];
            $lng_n1 = strlen ($n1);
            $esp_n1 ="";
            $num_carc = 20;
            if(substr_count($n1,"Ñ")>0){
                $num_carc = 20 + substr_count($n1,"Ñ");
            }
            for($y=$lng_n1; $y < $num_carc; $y++){
                $esp_n1 = $esp_n1." ";
            }
            $campoD13 = $n1.$esp_n1;

            #** Campo 14 - Segundo Nombre
            /*contar logitud*/
            $n2 = $row[$i][8];
            $lng_n2 = strlen ($n2);
            $esp_n2 ="";
            $num_carc = 30;
            if(substr_count($n2,"Ñ")>0){
                $num_carc = 30 + substr_count($n2,"Ñ");
            }
            for($y=$lng_n2; $y < $num_carc; $y++){
                $esp_n2 = $esp_n2." ";
            }
            $campoD14 = $n2.$esp_n2;

            #** Campo 15 - Ingreso
            $fecha_inicio =$fip;
            $fecha_fin = $ffip;
            $ing =" ";
            $fing = "          ";
            $ret =" ";
            $fret = "          ";
            $fcam_sal = "          ";
            $fmod_sal = " ";
            if(empty($row[$i][9])){
                $ing=" ";
            }else{
                $fecha = ($row[$i][9]);          

                if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                    $ing = "X";
                    $fing = $row[$i][9];
                } else {
                    $ing = " ";
                }
            }
            if(empty($row[$i][10])){
                $ret=" ";
            }else{
                $fecha = ($row[$i][10]);          

                if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                    $ret="X";
                    $fret = $row[$i][10];
                } else {
                    $ret=" ";
                }
            }
            if(empty($row[$i][11])){
                $fmod_sal=" ";
            }else{
                $fecha = ($row[$i][11]);          

                if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                    $fmod_sal="X";
                    $fcam_sal = $row[$i][11];
                } else {
                    $fmod_sal=" ";
                }
            }
            $campoD15 = $ing;
            
            #** Campo 16 -  Retiro
            $campoD16 = $ret;     

            #** Campo 17 - Traslado desde otra EPS o EOC 
            $campoD17 = " ";

            #** Campo 18 - TAE: Traslado a otra EPS o EOC
            $campoD18 = " ";

            #** Campo 19 - TDP: Traslado desde otra administradora de pensiones
            $campoD19 = " ";

            #** Campo 20 -TAP: Traslado a otra administradora de pensiones
            $campoD20 = " ";

            #** Campo 21 - VSP: Variación permanente de salario
            $campoD21 = $fmod_sal;

            #** Campo 22 - Correcciones
            $campoD22 = " ";

            #** Campo 23 - VST: Variación transitoria del salario
            $ibc_sal = $row[$i][12];
            $ibc_sal = round($ibc_sal/1);  
            $sueldo = $row[$i][13];
            $sueldo = round($sueldo/1);  
            if($ibc_sal == $sueldo){
                $vsp = " ";
            }else{
                $vsp = "X";
                $rowincapacidades = $con->Listar("SELECT fechainicio,fechafinal FROM gn_incapacidad where empleado = '$emp' and tiponovedad = 1 and fechainicio BETWEEN '$fip' and '$ffip' or empleado = '$emp' and tiponovedad = 1 and fechafinal BETWEEN '$fip' and '$ffip' or 
                	empleado = '$emp' and tiponovedad = 2 and fechainicio BETWEEN '$fip' and '$ffip' or empleado = '$emp' and tiponovedad = 2 and fechafinal BETWEEN '$fip' and '$ffip' or 
                	empleado = '$emp' and tiponovedad = 3 and fechainicio BETWEEN '$fip' and '$ffip' or empleado = '$emp' and tiponovedad = 3 and fechafinal BETWEEN '$fip' and '$ffip'  or 
                	empleado = '$emp' and tiponovedad = 4 and fechainicio BETWEEN '$fip' and '$ffip' or empleado = '$emp' and tiponovedad = 4 and fechafinal BETWEEN '$fip' and '$ffip' ");
                if(count($rowincapacidades)>0){
                	$vsp=" ";
                }else{
                	$rowvacaciones = $con->Listar("SELECT fechainiciodisfrute,fechafindisfrute FROM gn_vacaciones where  fechainiciodisfrute BETWEEN '$fip' and '$ffip' and empleado = '$emp' or fechafindisfrute BETWEEN '$fip' and '$ffip' and empleado = '$emp' ");
	                if(count($rowvacaciones)>0){
	                	$vsp="X";
	                }else{
	                	$vsp = "X";
	                }
                }
            }
            $campoD23 = $vsp;     

            #** Campo 24 - SLN: Suspensión temporal del contrato de trabajo o licencia no remunerada o comisión de servicios
            $fechaI_INR = "          ";    
            $fechaF_INR = "          ";   
            
            $rowINR = $con->Listar("SELECT fechainicio,fechafinal FROM gn_incapacidad where empleado = '$emp' and tiponovedad = 3 and fechainicio BETWEEN '$fip' and '$ffip' or empleado = '$emp' and tiponovedad = 3 and fechafinal BETWEEN '$fip' and '$ffip'  ");
            $fmod_sal=" ";
            for ($i2 = 0; $i2 < count($rowINR); $i2++) {
                if(empty($rowINR[$i2][0])){
                    $fmod_sal=" ";
                    
                }else{
                    $fecha = ($rowINR[$i2][0]);          

                    if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                        $fmod_sal="X";
                        $fechaI_INR = $rowINR[$i2][0];    
                         $fecha2 = $rowINR[$i2][1];          

                        if(($fecha2 >= $fecha_inicio) && ($fecha2 <= $fecha_fin)) {
                                $fechaF_INR = $rowINR[$i2][1];  
                        }else if(($fecha2 >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_INR = $fecha_fin;  
                        } else {
                                $fmod_sal=" ";   
                                $fechaF_INR = "          ";                           
                        }
                    } else {
                        $fmod_sal=" ";   
                        $fechaI_INR = "          ";    
                        if($fecha<$fechainicio){
                                $fechaI_INR = $fechainicio;    
                        }
                        if(empty($rowINR[$i2][1])){
                            $fmod_sal=" ";
                        }else{
                            $fecha = ($rowINR[$i2][1]);          

                            if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_INR = $rowINR[$i2][1];  
                            }else if(($fecha >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_INR = $fecha_fin;  
                            } else {
                                $fmod_sal=" ";                            
                            }
                        }                     
                    }
                }
            }
            //$campoD24 = $fmod_sal;
            $campoD24 = " ";

            #** Campo 25 - IGE: Incapacidad temporal por enfermedad general
            $fechaI_IG = "          ";    
            $fechaF_IG = "          ";    
            $rowIG = $con->Listar("SELECT fechainicio,fechafinal FROM gn_incapacidad where empleado = '$emp' and tiponovedad = 4 and fechainicio BETWEEN '$fip' and '$ffip' or empleado = '$emp' and tiponovedad = 4 and fechafinal BETWEEN '$fip' and '$ffip' ");
            $fmod_sal=" ";
            for ($i2 = 0; $i2 < count($rowIG); $i2++) {
                if(empty($rowIG[$i2][0])){
                    $fmod_sal=" ";                    
                }else{
                    $fecha = ($rowIG[$i2][0]);          

                    if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                        $fmod_sal="X";
                        $fechaI_IG = $rowIG[$i2][0];    
                        $fecha2 = $rowIG[$i2][1];          

                        if(($fecha2 >= $fecha_inicio) && ($fecha2 <= $fecha_fin)) {
                                $fechaF_IG = $rowIG[$i2][1];  
                        }else if(($fecha2 >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_IG = $fecha_fin;  
                        } else {
                                $fmod_sal=" ";   
                                $fechaF_IG = "          ";                           
                        }
                    } else {
                        $fmod_sal=" ";
                        $fechaI_IG = "          ";   
                        if($fecha<$fechainicio){
                                $fechaI_IG = $fechainicio;    
                        }
                        if(empty($rowIG[$i2][1])){
                            $fmod_sal=" ";
                        }else{
                            $fecha = ($rowIG[$i2][1]);          

                            if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_IG = $rowIG[$i2][1];    
                            }else if(($fecha >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_IG = $fecha_fin;    
                            } else {
                                $fmod_sal=" ";
                            }
                        }
                    }
                }
            }
            //$campoD25 = $fmod_sal;
            $campoD25 = " ";

            #** Campo 26 - LMA: Licencia de Maternidad o de paternidad
            $fmod_sal=" ";
            $fechaI_LM = "          ";    
            $fechaF_LM = "          ";    
            $rowLM = $con->Listar("SELECT fechainicio,fechafinal FROM gn_incapacidad where empleado = '$emp' and tiponovedad = 1 and fechainicio BETWEEN '$fip' and '$ffip' or empleado = '$emp' and tiponovedad = 1 and fechafinal BETWEEN '$fip' and '$ffip'  ");

            for ($i2 = 0; $i2 < count($rowLM); $i2++) {
                if(empty($rowLM[$i2][0])){
                    $fmod_sal=" ";
                    
                }else{
                    $fecha = ($rowLM[$i2][0]);          

                    if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                        $fmod_sal="X";
                        $fechaI_LM = $rowLM[$i2][0];    
                        $fecha2 = $rowLM[$i2][1];          

                        if(($fecha2 >= $fecha_inicio) && ($fecha2 <= $fecha_fin)) {
                                $fechaF_LM = $rowLM[$i2][1];  
                        }else if(($fecha2 >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_LM = $fecha_fin;  
                        } else {
                                $fmod_sal=" ";   
                                $fechaF_LM = "          ";                           
                        }
                    } else {
                        $fmod_sal=" ";
                        $fechaI_LM = "          ";    
                        if($fecha<$fechainicio){
                                $fechaI_LM = $fechainicio;    
                        }
                        if(empty($rowLM[$i2][1])){
                            $fmod_sal=" ";
                        }else{
                            $fecha = ($rowLM[$i2][1]);          

                            if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_LM = $rowLM[$i2][1];    
                            }else if(($fecha >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_LM = $fecha_fin; 
                            } else {
                                $fmod_sal=" ";
                            }
                        }
                    }
                }
            }
            $campoD26 = " ";            
            //$campoD26 = $fmod_sal;

            #** Campo 27 -VAC - LR: Vacaciones, Licencia remunerada
            $fmod_sal=" ";
            $fechaI_LR = "          ";    
            $fechaF_LR = "          ";  
            $rowLR = $con->Listar("SELECT fechainicio,fechafinal FROM gn_incapacidad where empleado = '$emp' and tiponovedad = 2 and fechainicio BETWEEN '$fip' and '$ffip' or empleado = '$emp' and tiponovedad = 2 and fechafinal BETWEEN '$fip' and '$ffip' ");
            for ($i2 = 0; $i2 < count($rowLR); $i2++) {
                if(empty($rowLR[$i2][0])){
                    $fmod_sal=" ";
                    
                }else{
                    $fecha = ($rowLR[$i2][0]);          

                    if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                        $fmod_sal="X";
                        $fechaI_LR = $rowLR[$i2][0];    
                        $fecha2 = $rowLR[$i2][1];          

                        if(($fecha2 >= $fecha_inicio) && ($fecha2 <= $fecha_fin)) {
                                $fechaF_LR = $rowLR[$i2][1];  
                        }else if(($fecha2 >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_LR = $fecha_fin;  
                        } else {
                                $fmod_sal=" ";   
                                $fechaF_LR = "          ";                           
                        }
                    } else {
                        $fmod_sal=" ";   
                        $fechaI_LR = "          ";    
                        if($fecha<$fechainicio){
                                $fechaI_LR = $fechainicio;    
                        }
                        if(empty($rowLR[$i2][1])){
                            $fmod_sal=" ";
                        }else{
                            $fecha = ($rowLR[$i2][1]);          

                            if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_LR = $rowLR[$i2][1];  
                            }else if(($fecha >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_LR = $fecha_fin;  
                            } else {
                                $fmod_sal=" ";                            
                            }
                        }                     
                    }
                }
            }
            
            $row_VAC = $con->Listar("SELECT fechainiciodisfrute,fechafindisfrute FROM gn_vacaciones where  fechainiciodisfrute BETWEEN '$fip' and '$ffip' and empleado = '$emp' or fechafindisfrute BETWEEN '$fip' and '$ffip' and empleado = '$emp'");
             
            for ($i2 = 0; $i2 < count($row_VAC); $i2++) {
                if(empty($row_VAC[$i2][0])){
                    $fmod_sal=" ";
                    
                }else{
                    $fecha = ($row_VAC[$i2][0]);          

                    if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                        $fmod_sal="X";
                        $fechaI_LR = $row_VAC[$i2][0];  
                        $fecha2 = $row_VAC[$i2][1];          

                        if(($fecha2 >= $fecha_inicio) && ($fecha2 <= $fecha_fin)) {
                                $fechaF_LR = $row_VAC[$i2][1];  
                        }else if(($fecha2 >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_LR = $fecha_fin;  
                        } else {
                                $fmod_sal=" ";   
                                $fechaF_LR = "          ";                           
                        }
                        
                    } else {
                        $fmod_sal=" ";   
                        if(($fecha <= $fecha_inicio)) {
                                $fechaI_LR = $fecha_inicio; 
                        }
                          
                        if(empty($row_VAC[$i2][1])){
                            $fmod_sal=" ";
                        }else{
                            $fecha = $row_VAC[$i2][1];          

                            if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                                $fmod_sal="X";                                 
                                $fechaF_LR = $row_VAC[$i2][1];  
                            }else if(($fecha >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_LR = $fecha_fin;  
                            } else {
                                $fmod_sal=" ";  
                                $fechaF_LR = "          ";                              
                            }
                        }                     
                    }
                }
            }            
            $campoD27 = " ";

            #** Campo 28- AVP: Aporte voluntario
            $campoD28 = " ";

            #** Campo 29 - VCT: Variación centros de trabajo.
            $campoD29 = " ";   

            #** Campo 30 - IRL: Días de incapacidad por accidente de trabajo o enfermedad laboral
            $fechaI_EAL = "          ";    
            $fechaF_EAL = "          ";  
            $dias_mes = 0;    
            $rowLM = $con->Listar("SELECT fechainicio,fechafinal,numerodias FROM gn_incapacidad where empleado = '$emp' and tiponovedad = 5 and tiponovedad = 6 ");

            for ($i2 = 0; $i2 < count($rowLM); $i2++) {
                if(empty($rowLM[$i2][0])){
                     $dias_mes = 0;                
                }else{
                    $fecha = strtotime($rowLM[$i2][0]);          

                    if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                        $fmod_sal="X";
                        $fechaI_EAL = $rowLM[$i2][0];    
                        $fechaF_EAL = "          ";  
                        $dias_inc = $rowLM[$i2][2];
                        $dias_desc = $rowLM[$i2][2];
                        //$dias_mes = $rowLM[$i2][2];
                        if($dias_desc > 0){
                            $fecha2 = strtotime($rowLM[$i2][1]);  
                            if(($fecha2 >= $fecha_fin)) {
                                $fi=$rowLM[$i2][0];
                                $fecha_div = explode("-", $fi);
                                $anioi = $fecha_div[0];
                                $mesi  = $fecha_div[1];
                                $diai  = $fecha_div[2];
                                $ndias_mes = cal_days_in_month(CAL_GREGORIAN, $mesi, $anioi);
                                $dias_mes = $dias_mes + ($ndias_mes - $diai);
                                $dias_desc = $dias_desc - $dias_mes;
                            }else{
                                $fi=$rowLM[$i2][0];
                                $fecha_div = explode("-", $fi);
                                $anioi = $fecha_div[0];
                                $mesi  = $fecha_div[1];
                                $diai  = $fecha_div[2];

                                $ff=$rowLM[$i2][1];
                                $fecha_divf = explode("-", $ff);
                                $aniof = $fecha_divf[0];
                                $mesf  = $fecha_divf[1];
                                $diaf  = $fecha_divf[2];

                                $dias_mes = $dias_mes + ($diaf - $diai);
                                $dias_desc = $dias_desc - $dias_mes;
                            }
                        }else{
                            $dias_mes = $dias_mes + 0;
                        }
                        $fecha2 = $rowLM[$i2][1];          

                        if(($fecha2 >= $fecha_inicio) && ($fecha2 <= $fecha_fin)) {
                                $fechaF_EAL = $rowLM[$i2][1];  
                        }else if(($fecha2 >= $fecha_fin)) {
                                $fmod_sal="X";
                                $fechaF_EAL = $fecha_fin;  
                        } else {
                                $fmod_sal=" ";   
                                $fechaF_EAL = "          ";                           
                        }
                    } else {
                        $fmod_sal=" ";
                        $fechaI_EAL = "          ";    
                        if($fecha<$fechainicio){
                                $fechaI_EAL = $fechainicio;    
                        }   

                        if(empty($rowLM[$i2][1])){
                            $fmod_sal=" ";
                        }else{
                            $fecha = strtotime($rowLM[$i2][1]);          

                            if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
                                $fechaF_EAL = $rowLM[$i2][1]; 
                                $dias_inc = $rowLM[$i2][2];
                                $dias_desc = $rowLM[$i2][2];
                               // $dias_mes = $rowLM[$i2][2];
                                if($dias_inc > 0){
                                    $ff=$rowLM[$i2][1];
                                    $fecha_divf = explode("-", $ff);
                                    $aniof = $fecha_divf[0];
                                    $mesf  = $fecha_divf[1];
                                    $diaf  = $fecha_divf[2];

                                    $dias_mes = $dias_mes + $diaf;
                                    $dias_desc = $dias_desc - $dias_mes;
                                }else{
                                    $dias_mes = 0;
                                }
                                
                            }else if(($fecha >= $fecha_fin)) {
                                $ff=$rowLM[$i2][1];
                                $fechaF_EAL = $fecha_fin; 
                                $fecha_divf = explode("-", $ff);
                                $aniof = $fecha_divf[0];
                                $mesf  = $fecha_divf[1];
                                $diaf  = $fecha_divf[2];

                                $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mesf, $aniof);
                                $dias_desc = $dias_desc - $dias_mes;
                            } else {
                                // $dias_mes = 0;
                            }
                        }
                    }
                }
            }
            /*contar logitud*/
            $di = $dias_mes;
            $lng_di = strlen ($di);
            $esp_di ="";
            for($y=$lng_di; $y < 2; $y++){
                $esp_di = $esp_di."0";
            }      
            $campoD30 = $esp_di.$dias_mes;

            #** Campo 31 - Código de la administradora de fondos de pensiones a la cual pertenece el afiliado
            $cod_pa="      ";
            $row_EPA = $con->Listar("SELECT a.id_unico, 
                                           a.fechaafiliacion, 
                                           t.codigo_afp 
                                    FROM gn_afiliacion a 
                                    LEFT JOIN gf_tercero t on t.id_unico = a.tercero 
                                    where a.empleado = '$emp' and a.tipo = 2 and a.fecharetiro is null ");
            for ($i2 = 0; $i2 < count($row_EPA); $i2++) {
                if(empty($row_EPA[$i2][2])){
                    $cod_pa="      ";
                    
                }else{
                    $cod_pa=$row_EPA[$i2][2];
                }
            }
            /*contar logitud*/
            $cdpa = $cod_pa;
            $lng_cdpa = strlen ($cdpa);
            $esp_cdpa ="";
            for($y=$lng_cdpa; $y < 6; $y++){
                $esp_cdpa = $esp_cdpa." ";
            }
            $campoD31 = $cdpa.$esp_cdpa;

            #** Campo 32 - Código de la administradora de fondos de pensiones a la cual se traslada el afiliado
            $cod_pt="      ";
            $row_EPT = $con->Listar("SELECT a.id_unico, 
                                           a.fechaafiliacion, 
                                           t.codigo_afp 
                                    FROM gn_afiliacion a 
                                    LEFT JOIN gf_tercero t on t.id_unico = a.tercero 
                                    where a.empleado = '$emp' and a.tipo = 2 and a.fechaafiliacion BETWEEN '$fecha_inicio' and '$fecha_fin' ");
            for ($i2 = 0; $i2 < count($row_EPT); $i2++) {
                if(empty($row_EPT[$i2][1])){
                    $cod_pt="      ";
                    
                }else{                     
                    $cod_pt=$row_EPT[$i2][2];
                }
            }
            /*contar logitud*/
            $cdpat = $cod_pt;
            $lng_cdpat = strlen ($cdpat);
            $esp_cdpat ="";
            for($y=$lng_cdpat; $y < 6; $y++){
                $esp_cdpat = $esp_cdpat." ";
            }
            $campoD32 = $cdpat.$esp_cdpat;

            #** Campo 33 - Código EPS o EOC a la cual pertenece el afiliado
            $cod_sa="      ";
            $row_ESA = $con->Listar("SELECT a.id_unico, 
                                           a.fechaafiliacion, 
                                           t.codigo_afp 
                                    FROM gn_afiliacion a 
                                    LEFT JOIN gf_tercero t on t.id_unico = a.tercero 
                                    where a.empleado = '$emp' and a.tipo = 1 and a.fecharetiro is null ");
            for ($i2 = 0; $i2 < count($row_ESA); $i2++) {
                if(empty($row_ESA[$i2][2])){
                    $cod_sa="      ";
                    
                }else{
                    $cod_sa=$row_ESA[$i2][2];
                }
            }
            /*contar logitud*/
            $cdsa = $cod_sa;
            $lng_cdsa = strlen ($cdsa);
            $esp_cdsa ="";
            for($y=$lng_cdsa; $y < 6; $y++){
                $esp_cdsa = $esp_cdsa." ";
            }
            $campoD33 = $cdsa.$esp_cdsa;

            #** Campo 34 - Código EPS o EOC a la cual se traslada el afiliado
            $cod_st="      ";
            $row_EST = $con->Listar("SELECT a.id_unico, 
                                           a.fechaafiliacion, 
                                           t.codigo_afp 
                                    FROM gn_afiliacion a 
                                    LEFT JOIN gf_tercero t on t.id_unico = a.tercero 
                                    where a.empleado = '$emp' and a.tipo = 1 and a.fechaafiliacion BETWEEN '$fecha_inicio' and '$fecha_fin' ");
            for ($i2 = 0; $i2 < count($row_EST); $i2++) {
                if(empty($row_EST[$i2][1])){
                    $cod_st="      ";
                    
                }else{                     
                    $cod_st=$row_EST[$i2][2];
                }
            }
            /*contar logitud*/
            $cdsat = $cod_st;
            $lng_cdpat = strlen ($cdsat);
            $esp_cdsat ="";
            if($lng_cdpat<6){
                for($y=$lng_cdsat; $y < 6; $y++){
                    $esp_cdsat = $esp_cdsat." ";
                }
            }            
            $campoD34 = $cdsat.$esp_cdsat;

            #** Campo 35 - Código CCF a la cual pertenece el afiliado
            $cod_ccfa="      ";
            $row_ECCFA = $con->Listar("SELECT a.id_unico, 
                                           a.fechaafiliacion, 
                                           t.codigo_afp 
                                    FROM gn_afiliacion a 
                                    LEFT JOIN gf_tercero t on t.id_unico = a.tercero 
                                    where a.empleado = '$emp' and a.tipo = 6 and a.fecharetiro is null ");
            for ($i2 = 0; $i2 < count($row_ECCFA); $i2++) {
                if(empty($row_ECCFA[$i2][2])){
                    $cod_ccfa="      ";
                    
                }else{
                    $cod_ccfa=$row_ECCFA[$i2][2];
                }
            }
            /*contar logitud*/
            $cdccfa = $cod_ccfa;
            $lng_cdccfa = strlen ($cdccfa);
            $esp_cdccfa ="";
            if($lng_cdccfa<6){
                for($y=$lng_cdccfa; $y < 6; $y++){
                    $esp_cdccfa = $esp_cdccfa." ";
                }
            }
            $campoD35 = $cdccfa.$esp_cdccfa;

            #** Campo 36 - Número de días cotizados a pensión
            $dtb = $row[$i][14];
            $dtb = round($dtb/1);
            if($dtb<10){
                $dtb = "0".$dtb;
            }
            $campoD36 = $dtb;

            #** Campo 37 - Número de días cotizados a salud
            $campoD37 = $dtb;

            #** Campo 38 - Número de días cotizados a riesgos
            $campoD38 = $dtb;

            #** Campo 39 - Número de días cotizados a CCF
            $campoD39 = $dtb;

            #** Campo 40 - Salario básico
            /*contar logitud*/
            $sl = $row[$i][13];
            $sl = round($sl/1);
            $lng_sl = strlen ($sl);
            $esp_sl ="";
            if($lng_sl<9){
                for($y=$lng_sl; $y < 9; $y++){
                    $esp_sl = $esp_sl."0";
                }
            }
            $campoD40 = $esp_sl.$sl;

            #** Campo 41 -Tipo de salario X. Salario integral F.Salario fijo V.Salario variable
            $sl_int = $row[$i][15];
            if($sl_int == 1){
                $sl_int ="X";
            }else{
                $sl_int ="F";
            }
            $campoD41 = $sl_int;

            #** Campo 42 - IBC pensión 
            /*contar logitud*/
            $lng_ibc_sal = strlen ($ibc_sal);
            $esp_ibcsal ="";
            if($lng_ibc_sal<9){
                for($y=$lng_ibc_sal; $y < 9; $y++){
                    $esp_ibcsal = $esp_ibcsal."0";
                }
            }
            $campoD42 = $esp_ibcsal.$ibc_sal;

            #** Campo 43 - IBC salud
            $campoD43 = $esp_ibcsal.$ibc_sal;

            #** Campo 44 - IBC Riesgos Laborales
            $campoD44 = $esp_ibcsal.$ibc_sal;

            #** Campo 45 - IBC CCF
            /*contar logitud*/
            $ibc_para = $row[$i][16];
            $ibc_para = round($ibc_para/1);  
            $lng_ibc_pr = strlen ($ibc_para);
            $esp_ibcpr ="";
            if($lng_ibc_pr<9){
                for($y=$lng_ibc_pr; $y < 9; $y++){ 
                    $esp_ibcpr = $esp_ibcpr."0";
                }
            }            
            $campoD45 =$esp_ibcsal.$ibc_sal;

            #** Campo 46 - Tarifa de aportes pensiones
            $prc_pen = $row[$i][19];
            $prc_pen = $prc_pen /100;
            $lng_prc_pen = strlen ($prc_pen);
            $esp_prpen ="";
            if($lng_prc_pen<7){
                for($y=$lng_prc_pen; $y < 7; $y++){
                    $esp_prpen = $esp_prpen."0";
                }
            }
            $campoD46 = $prc_pen.$esp_prpen;

            #** Campo 47 - Cotización obligatoria a pensiones
            $ap_pen = $row[$i][20];
            $ap_pen = round($ap_pen/1);
            $lng_ap_pen = strlen ($ap_pen);
            $esp_appen ="";
            if($lng_ap_pen<9){
                for($y=$lng_ap_pen; $y < 9; $y++){
                    $esp_appen = $esp_appen."0";
                }
            }       
            $campoD47 = $esp_appen.$ap_pen;

            #** Campo 48 - Aporte voluntario del afiliado al fondo de pensiones obligatorias
            $campoD48 = "000000000";

            #** Campo 49 - Aporte voluntario del aportante al fondo de pensiones obligatoria
            $campoD49 = "000000000";            

            #** Campo 50 - Total cotización Sistema General de Pensiones
            $campoD50 = $campoD47;

            #** Campo 51 - Aportes a fondo de solidaridad pensional - subcuenta de solidaridad
            $ap_fon_sol = $row[$i][21];
            $ap_fon_sol = round($ap_fon_sol/1);
            $lng_ap_fs = strlen ($ap_fon_sol);
            $esp_apfs ="";
            if($lng_ap_fs<9){
                for($y=$lng_ap_fs; $y < 9; $y++){
                    $esp_apfs = $esp_apfs."0";
                }
            }
            $campoD51 = $esp_apfs.$ap_fon_sol;

            #** Campo 52 -Aportes a fondo de solidaridad pensional - subcuenta de subsistencia
            $campoD52 = $campoD51;            

            #** Campo 53 - Valor no retenido por aportes voluntarios.
            $campoD53 = "000000000";

            #** Campo 54 - Tarifa de aportes salud
            $prc_sal = $row[$i][17];
            $prc_sal = $prc_sal/100;
            $lng_prc_sal = strlen ($prc_sal);
            $esp_prsal ="";
            if($lng_prc_sal<7){
                for($y=$lng_prc_sal; $y < 7; $y++){
                    $esp_prsal = $esp_prsal."0";
                } 
            } 
            $campoD54 = $prc_sal.$esp_prsal;

            #** Campo 55 -    Cotización obligatoria a salud        
            $ap_sal = $row[$i][18];
            $ap_sal = round($ap_sal/1);            
            $lng_ap_sal = strlen ($ap_sal);
            $esp_apsal ="";
            if($lng_ap_sal<9){
                for($y=$lng_ap_sal; $y < 9; $y++){
                    $esp_apsal = $esp_apsal."0";
                }
            }
            $campoD55 = $esp_apsal.$ap_sal;            

            #** Campo 56 - Valor de la UPC adicional.
            $campoD56 = "000000000";

            #** Campo 57 - N° autorización de la incapacidad por enfermedad general
            $campoD57 = "               ";

            #** Campo 58 - Valor de la incapacidad por enfermedad general
            $campoD58 = "000000000";

            #** Campo 59 - N° de autorización de la licencia de maternidad o paternidad
            $campoD59 = "               ";

            #** Campo 60 -Valor de la licencia de maternidad 
            $campoD60 = "000000000";

            #** Campo 61 - Tarifa de aportes a Riesgos Laborales
            $prc_riesgos = $row[$i][22];
            $prc_riesgos = $prc_riesgos /100;
            $lng_prc_rg = strlen ($prc_riesgos);
            $esp_prrg ="";
            if($lng_prc_rg<9){
                for($y=$lng_prc_rg; $y < 9; $y++){
                    $esp_prrg = $esp_prrg."0";
                }
            }
            $campoD61 = $prc_riesgos.$esp_prrg;

            #** Campo 62 - Centro de trabajo CT
            $campoD62 = "000000001";

            #** Campo 63 - Cotización obligatoria al Sistema General de Riesgos Laborales
            $ap_riesgos = $row[$i][23];
            $ap_riesgos = round($ap_riesgos/1);
            $lng_ap_gs = strlen ($ap_riesgos);
            $esp_apgs ="";
            if($lng_ap_gs<9){
                for($y=$lng_ap_gs; $y < 9; $y++){
                    $esp_apgs = $esp_apgs."0";
                }
            }
            $campoD63 = $esp_apgs.$ap_riesgos;

            #** Campo 64 - Tarifa de aportes CCF
            $prc_ccf = $row[$i][24];
            $prc_ccf = $prc_ccf /100;
            $lng_prc_ccf = strlen ($prc_ccf);
            $esp_prccf ="";
            if($lng_prc_ccf<7){
                for($y=$lng_prc_ccf; $y < 7; $y++){
                    $esp_prccf = $esp_prccf."0";
                }
            }
            $campoD64 = $prc_ccf.$esp_prccf;

            #** Campo 65 - Valor aporte CCF 
            $ap_ccf = $row[$i][25];
            $ap_ccf = round($ap_ccf/1);
            $lng_ap_ccf = strlen ($ap_ccf);
            $esp_apccf ="";
            if($lng_ap_ccf<9){
                for($y=$lng_ap_ccf; $y < 9; $y++){
                    $esp_apccf = $esp_apccf."0";
                }
            }
            $campoD65 = $esp_apccf.$ap_ccf;

            #** Campo 66 - Tarifa de aportes SENA
            if(!empty($row[$i][27])){
                $prc_sn = $row[$i][26];    
            } else {
                $prc_sn = 0;
            }            
            $prc_sn = $prc_sn /100;
            $lng_prc_sn = strlen ($prc_sn);
            $esp_prsn ="";
            if($lng_prc_sn<7){
                for($y=$lng_prc_sn; $y < 7; $y++){
                    $esp_prsn = $esp_prsn."0";
                }
            }
            $campoD66 = $prc_sn.$esp_prsn;

            #** Campo 67 - Valor aportes SENA
            $ap_sn = $row[$i][27];
            $ap_sn = round($ap_sn/1);
            $lng_ap_sn = strlen ($ap_sn);
            $esp_apsn ="";
            if($lng_ap_sn<9){
                for($y=$lng_ap_sn; $y < 9; $y++){
                    $esp_apsn = $esp_apsn."0";
                }
            }
            $campoD67 = $esp_apsn.$ap_sn;

            #** Campo 68 - Tarifa aportes ICBF
            if(!empty($row[$i][29])){
                $prc_icbf = $row[$i][28];   
            } else {
                $prc_icbf = 0;
            }
            
            $prc_icbf = $prc_icbf /100;
            $lng_prc_icbf = strlen ($prc_icbf);
            $esp_pricbf ="";
            if($lng_prc_icbf<7){
                for($y=$lng_prc_icbf; $y < 7; $y++){
                    $esp_pricbf = $esp_pricbf."0";
                }
            }
            $campoD68 = $prc_icbf.$esp_pricbf;

            #** Campo 69 - Valor aporte ICBF
            $ap_icbf = $row[$i][29];
            $ap_icbf = round($ap_icbf/1);
            $lng_ap_icbf = strlen ($ap_icbf);
            $esp_apicbf ="";
            if($lng_ap_icbf<9){
                for($y=$lng_ap_icbf; $y < 9; $y++){
                    $esp_apicbf = $esp_apicbf."0";
                }
            }
            $campoD69 = $esp_apicbf.$ap_icbf;

            #** Campo 70 -Tarifa aportes    ESAP
            $prc_esap = $row[$i][30];
            if($prc_esap == 0 )
            {
                $prc_esap = "0.00";
            }
            $prc_esap = $prc_esap /100;
            $lng_prc_esap = strlen ($prc_esap);
            $esp_presap ="";
            if($lng_prc_esap<7){
                for($y=$lng_prc_esap; $y < 7; $y++){
                    $esp_presap = $esp_presap."0";
                }
            }
            $campoD70 = $prc_esap.$esp_presap;

            #** Campo 71 - Valor aporte ESAP 
            $ap_esap = $row[$i][31];
            $ap_esap = round($ap_esap/1);
            $lng_ap_esap = strlen ($ap_esap);
            $esp_apesap ="";
            if($lng_ap_esap<9){
                for($y=$lng_ap_esap; $y < 9; $y++){
                    $esp_apesap = $esp_apesap."0";
                }
            }
            $campoD71 = $esp_apesap.$ap_esap;

            #** Campo 72 - Tarifa aportes MEN
            $prc_min = $row[$i][32];
            $prc_min = $prc_min /100;
            $lng_prc_min = strlen ($prc_min);
            $esp_prmin ="";
            if($lng_prc_min<7){
                for($y=$lng_prc_min; $y < 7; $y++){
                    $esp_prmin = $esp_prmin."0";
                }
            }
            $campoD72 = $prc_min.$esp_prmin;

            #** Campo 73 - Valor aporte MEN
            $ap_min = $row[$i][33];
            $ap_min = round($ap_min/1);
            $lng_ap_min = strlen ($ap_min);
            $esp_apmin ="";
            if($lng_ap_min<7){
                for($y=$lng_ap_min; $y < 9; $y++){
                    $esp_apmin = $esp_apmin."0";
                }
            }
            $campoD73 = $esp_apmin.$ap_min;

            #** Campo 74 - Tipo de documento del cotizante principal
            $campoD74 = "  ";

            #** Campo 75 - Número de identificación del cotizante principal
            $campoD75 = "                ";

            #** Campo 76 - Cotizante exonerado de pago de aporte salud, SENA e ICBF
            $sal_min = $row[$i][34];
            $sal_min = round($sal_min/1);
            if($campoD43>=($sal_min * 10)){
                $exo = "N";
            }else{
                $exo = "S";
            }
            $campoD76 = $exo;

            #** Campo 77 - Código de la administradora de Riesgos Laborales a la cual pertenece el afiliado
            $cod_arla="      ";
            $row_EARLA = $con->Listar("SELECT a.id_unico, 
                                           a.fechaafiliacion, 
                                           t.codigo_afp 
                                    FROM gn_afiliacion a 
                                    LEFT JOIN gf_tercero t on t.id_unico = a.tercero 
                                    where a.empleado = '$emp' and a.tipo = 4 and a.fecharetiro is null ");
            for ($i2 = 0; $i2 < count($row_EARLA); $i2++) {
                if(empty($row_EARLA[$i2][2])){
                    $cod_arla="      ";
                    
                }else{
                    $cod_arla=$row_EARLA[$i2][2];
                }
            }
            /*contar logitud*/
            $cdarl = $cod_arla;
            $lng_cdarl = strlen ($cdarl);
            $esp_cdarl ="";
            if($lng_cdarl<6){
                for($y=$lng_cdarl; $y < 6; $y++){
                    $esp_cdarl = $esp_cdarl." ";
                }
            }
            $campoD77 = $cdarl.$esp_cdarl;

            #** Campo 78 - Clase de riesgo en la que se encuentrael afiliado
            $campoD78 = $row[$i][35];            

            #** Campo 79 - Indicador tarifa especial pensiones
            $campoD79 = " ";

            #** Campo 80 - Fecha de ingreso Formato (AAAAMM-DD)
            $campoD80 = $fing;

            #** Campo 81 - Fecha de retiro. Formato (AAAAMM-DD).
            $campoD81 = $fret;            

            #** Campo 82 - Fecha Inicio VSP Formato (AAAAMM-DD)
            $campoD82 = $fcam_sal;

            #** Campo 83 - Fecha Inicio SLN Formato (AAAAMM-DD).
            $campoD83 = $fechaI_INR;

            #** Campo 84 - Fecha fin SLN Formato (AAAAMM-DD)
            $campoD84 = $fechaF_INR;

            #** Campo 85 - Fecha inicio IGE Formato (AAAAMM-DD).

            $campoD85 = "          ";

            #** Campo 86 - Fecha fin IGE. formato (AAAAMM-DD)
            $campoD86 = "          ";

            #** Campo 87 - Fecha inicio LMA Formato (AAAAMM-DD).
            $campoD87 = "          ";

            #** Campo 88 - Fecha fin LMA formato (AAAAMM-DD)
            $campoD88 = "          ";

            #** Campo 89 - Fecha inicio VAC -LR Formato (AAAAMM-DD).
            $campoD89 = "          ";

            #** Campo 90 -Fecha fin VAC - LR Formato (AAAAMM-DD).
            $campoD90 = "          ";

            #** Campo 91 - Fecha inicio VCT Formato (AAAAMM-DD)
            $campoD91 = "          ";

            #** Campo 92 - Fecha fin VCT Formato (AAAAMM-DD).
            $campoD92 = "          ";

            #** Campo 93 - Fecha inicio IRL Formato (AAAAMM-DD).
            $campoD93 = "          ";

            #** Campo 94 - Fecha fin IRL Formato (AAAAMM-DD).
            $campoD94 = "          ";

            #** Campo 95 - IBC otros parafiscales diferentes a CCF
            $campoD95 = $campoD45;

            #** Campo 96 -Número de horas laboradas
            $dtb = $row[$i][14];
            $dtb = round($dtb/1);
            $hlab = $dtb * 8;
            $lng_hlab = strlen ($hlab);
            $esp_hlab ="";
            if($lng_hlab<6){
                for($y=$lng_hlab; $y < 3; $y++){
                    $esp_hlab = $esp_hlab."0";
                }
            }
            $campoD96 = $esp_hlab.$hlab;
            
            if($row[$i][14]!=0){
                if($exportar==3){
                    $html .='<tr>';
                    $html .='<td>'.$campoD1.'</td>';
                    $html .='<td>'.$campoD2.'</td>';
                    $html .='<td>'.$campoD3.'</td>';
                    $html .='<td>'.$campoD4.'</td>';
                    $html .='<td>'.$campoD5.'</td>';
                    $html .='<td>'.$campoD6.'</td>';
                    $html .='<td>'.$campoD7.'</td>';
                    $html .='<td>'.$campoD8.'</td>';
                    $html .='<td>'.$campoD9.'</td>';
                    $html .='<td>'.$campoD10.'</td>';
                    $html .='<td>'.$campoD11.'</td>';
                    $html .='<td>'.$campoD12.'</td>';
                    $html .='<td>'.$campoD13.'</td>';
                    $html .='<td>'.$campoD14.'</td>';
                    $html .='<td>'.$campoD15.'</td>';
                    $html .='<td>'.$campoD16.'</td>';
                    $html .='<td>'.$campoD17.'</td>';
                    $html .='<td>'.$campoD18.'</td>';
                    $html .='<td>'.$campoD19.'</td>';
                    $html .='<td>'.$campoD20.'</td>';
                    $html .='<td>'.$campoD21.'</td>';
                    $html .='<td>'.$campoD22.'</td>';  
                    $html .='<td>'.$campoD23.'</td>';  
                    $html .='<td>'.$campoD24.'</td>';  
                    $html .='<td>'.$campoD25.'</td>';  
                    $html .='<td>'.$campoD26.'</td>';  
                    $html .='<td>'.$campoD27.'</td>';  
                    $html .='<td>'.$campoD28.'</td>';  
                    $html .='<td>'.$campoD29.'</td>';  
                    $html .='<td>'.$campoD30.'</td>';  
                    $html .='<td>'.$campoD31.'</td>';  
                    $html .='<td>'.$campoD32.'</td>';  
                    $html .='<td>'.$campoD33.'</td>';  
                    $html .='<td>'.$campoD34.'</td>';  
                    $html .='<td>'.$campoD35.'</td>';  
                    $html .='<td>'.$campoD36.'</td>';  
                    $html .='<td>'.$campoD37.'</td>';  
                    $html .='<td>'.$campoD38.'</td>';  
                    $html .='<td>'.$campoD39.'</td>';  
                    $html .='<td>'.$campoD40.'</td>';  
                    $html .='<td>'.$campoD41.'</td>';  
                    $html .='<td>'.$campoD42.'</td>';  
                    $html .='<td>'.$campoD43.'</td>';  
                    $html .='<td>'.$campoD44.'</td>';  
                    $html .='<td>'.$campoD45.'</td>';  
                    $html .='<td>'.$campoD46.'</td>';  
                    $html .='<td>'.$campoD47.'</td>';  
                    $html .='<td>'.$campoD48.'</td>';  
                    $html .='<td>'.$campoD49.'</td>';  
                    $html .='<td>'.$campoD50.'</td>';  
                    $html .='<td>'.$campoD51.'</td>';  
                    $html .='<td>'.$campoD52.'</td>';  
                    $html .='<td>'.$campoD53.'</td>';  
                    $html .='<td>'.$campoD54.'</td>';  
                    $html .='<td>'.$campoD55.'</td>';  
                    $html .='<td>'.$campoD56.'</td>';  
                    $html .='<td>'.$campoD57.'</td>';  
                    $html .='<td>'.$campoD58.'</td>';  
                    $html .='<td>'.$campoD59.'</td>';  
                    $html .='<td>'.$campoD60.'</td>';  
                    $html .='<td>'.$campoD61.'</td>';  
                    $html .='<td>'.$campoD62.'</td>';  
                    $html .='<td>'.$campoD63.'</td>';  
                    $html .='<td>'.$campoD64.'</td>';  
                    $html .='<td>'.$campoD65.'</td>';                
                    $html .='<td>'.$campoD66.'</td>';  
                    $html .='<td>'.$campoD67.'</td>';  
                    $html .='<td>'.$campoD68.'</td>';  
                    $html .='<td>'.$campoD69.'</td>';  
                    $html .='<td>'.$campoD70.'</td>';  
                    $html .='<td>'.$campoD71.'</td>';  
                    $html .='<td>'.$campoD72.'</td>';  
                    $html .='<td>'.$campoD73.'</td>';  
                    $html .='<td>'.$campoD74.'</td>';  
                    $html .='<td>'.$campoD75.'</td>';  
                    $html .='<td>'.$campoD76.'</td>';  
                    $html .='<td>'.$campoD77.'</td>';  
                    $html .='<td>'.$campoD78.'</td>';  
                    $html .='<td>'.$campoD79.'</td>';  
                    $html .='<td>'.$campoD80.'</td>';  
                    $html .='<td>'.$campoD81.'</td>';  
                    $html .='<td>'.$campoD82.'</td>';  
                    $html .='<td>'.$campoD83.'</td>';  
                    $html .='<td>'.$campoD84.'</td>';  
                    $html .='<td>'.$campoD85.'</td>';  
                    $html .='<td>'.$campoD86.'</td>';  
                    $html .='<td>'.$campoD87.'</td>';  
                    $html .='<td>'.$campoD88.'</td>';  
                    $html .='<td>'.$campoD89.'</td>';  
                    $html .='<td>'.$campoD90.'</td>';  
                    $html .='<td>'.$campoD91.'</td>';  
                    $html .='<td>'.$campoD92.'</td>';  
                    $html .='<td>'.$campoD93.'</td>';  
                    $html .='<td>'.$campoD94.'</td>';  
                    $html .='<td>'.$campoD95.'</td>';  
                    $html .='<td>'.$campoD96.'</td>'; 
                   
                    $html .='</tr>';
                } else{
                        $html .=str_replace(',',' ',$campoD1)."$separador";
                        $html .=str_replace(',',' ',$campoD2)."$separador";
                        $html .=str_replace(',',' ',$campoD3)."$separador";
                        $html .=str_replace(',',' ',$campoD4)."$separador";
                        $html .=str_replace(',',' ',$campoD5)."$separador";
                        $html .=str_replace(',',' ',$campoD6)."$separador";
                        $html .=str_replace(',',' ',$campoD7)."$separador";
                        $html .=str_replace(',',' ',$campoD8)."$separador";
                        $html .=str_replace(',',' ',$campoD9)."$separador";
                        $html .=str_replace(',',' ',$campoD10)."$separador";
                        $html .=str_replace(',',' ',$campoD11)."$separador";
                        $html .=str_replace(',',' ',$campoD12)."$separador";
                        $html .=str_replace(',',' ',$campoD13)."$separador";
                        $html .=str_replace(',',' ',$campoD14)."$separador";
                        $html .=str_replace(',',' ',$campoD15)."$separador";
                        $html .=str_replace(',',' ',$campoD16)."$separador";
                        $html .=str_replace(',',' ',$campoD17)."$separador";
                        $html .=str_replace(',',' ',$campoD18)."$separador";
                        $html .=str_replace(',',' ',$campoD19)."$separador";
                        $html .=str_replace(',',' ',$campoD20)."$separador";
                        $html .=str_replace(',',' ',$campoD21)."$separador";
                        $html .=str_replace(',',' ',$campoD22)."$separador";
                        $html .=str_replace(',',' ',$campoD23)."$separador";
                        $html .=str_replace(',',' ',$campoD24)."$separador";
                        $html .=str_replace(',',' ',$campoD25)."$separador";
                        $html .=str_replace(',',' ',$campoD26)."$separador";
                        $html .=str_replace(',',' ',$campoD27)."$separador";
                        $html .=str_replace(',',' ',$campoD28)."$separador";
                        $html .=str_replace(',',' ',$campoD29)."$separador";
                        $html .=str_replace(',',' ',$campoD30)."$separador";
                        $html .=str_replace(',',' ',$campoD31)."$separador";
                        $html .=str_replace(',',' ',$campoD32)."$separador";
                        $html .=str_replace(',',' ',$campoD33)."$separador";
                        $html .=str_replace(',',' ',$campoD34)."$separador";
                        $html .=str_replace(',',' ',$campoD35)."$separador";
                        $html .=str_replace(',',' ',$campoD36)."$separador";
                        $html .=str_replace(',',' ',$campoD37)."$separador";
                        $html .=str_replace(',',' ',$campoD38)."$separador";
                        $html .=str_replace(',',' ',$campoD39)."$separador";
                        $html .=str_replace(',',' ',$campoD40)."$separador";
                        $html .=str_replace(',',' ',$campoD41)."$separador";
                        $html .=str_replace(',',' ',$campoD42)."$separador";
                        $html .=str_replace(',',' ',$campoD43)."$separador";
                        $html .=str_replace(',',' ',$campoD44)."$separador";
                        $html .=str_replace(',',' ',$campoD45)."$separador";
                        $html .=str_replace(',',' ',$campoD46)."$separador";
                        $html .=str_replace(',',' ',$campoD47)."$separador";
                        $html .=str_replace(',',' ',$campoD48)."$separador";
                        $html .=str_replace(',',' ',$campoD49)."$separador";
                        $html .=str_replace(',',' ',$campoD50)."$separador";
                        $html .=str_replace(',',' ',$campoD51)."$separador";
                        $html .=str_replace(',',' ',$campoD52)."$separador";
                        $html .=str_replace(',',' ',$campoD53)."$separador";
                        $html .=str_replace(',',' ',$campoD54)."$separador";
                        $html .=str_replace(',',' ',$campoD55)."$separador";
                        $html .=str_replace(',',' ',$campoD56)."$separador";
                        $html .=str_replace(',',' ',$campoD57)."$separador";
                        $html .=str_replace(',',' ',$campoD58)."$separador";
                        $html .=str_replace(',',' ',$campoD59)."$separador";
                        $html .=str_replace(',',' ',$campoD60)."$separador";
                        $html .=str_replace(',',' ',$campoD61)."$separador";
                        $html .=str_replace(',',' ',$campoD62)."$separador";
                        $html .=str_replace(',',' ',$campoD63)."$separador";
                        $html .=str_replace(',',' ',$campoD64)."$separador";
                        $html .=str_replace(',',' ',$campoD65)."$separador";
                        $html .=str_replace(',',' ',$campoD66)."$separador";
                        $html .=str_replace(',',' ',$campoD67)."$separador";
                        $html .=str_replace(',',' ',$campoD68)."$separador";
                        $html .=str_replace(',',' ',$campoD69)."$separador";
                        $html .=str_replace(',',' ',$campoD70)."$separador";
                        $html .=str_replace(',',' ',$campoD71)."$separador";
                        $html .=str_replace(',',' ',$campoD72)."$separador";
                        $html .=str_replace(',',' ',$campoD73)."$separador";
                        $html .=str_replace(',',' ',$campoD74)."$separador";
                        $html .=str_replace(',',' ',$campoD75)."$separador";
                        $html .=str_replace(',',' ',$campoD76)."$separador";
                        $html .=str_replace(',',' ',$campoD77)."$separador";
                        $html .=str_replace(',',' ',$campoD78)."$separador";
                        $html .=str_replace(',',' ',$campoD79)."$separador";
                        $html .=str_replace(',',' ',$campoD80)."$separador";
                        $html .=str_replace(',',' ',$campoD81)."$separador";
                        $html .=str_replace(',',' ',$campoD82)."$separador";
                        $html .=str_replace(',',' ',$campoD83)."$separador";
                        $html .=str_replace(',',' ',$campoD84)."$separador";
                        $html .=str_replace(',',' ',$campoD85)."$separador";
                        $html .=str_replace(',',' ',$campoD86)."$separador";
                        $html .=str_replace(',',' ',$campoD87)."$separador";
                        $html .=str_replace(',',' ',$campoD88)."$separador";
                        $html .=str_replace(',',' ',$campoD89)."$separador";
                        $html .=str_replace(',',' ',$campoD90)."$separador";
                        $html .=str_replace(',',' ',$campoD91)."$separador";
                        $html .=str_replace(',',' ',$campoD92)."$separador";
                        $html .=str_replace(',',' ',$campoD93)."$separador";
                        $html .=str_replace(',',' ',$campoD94)."$separador";
                        $html .=str_replace(',',' ',$campoD95)."$separador";
                        $html .=str_replace(',',' ',$campoD96);
                        $html .="\r\n";   
                }
            }
            $cont++;
            
        ###*************************************************************************************************************####
        #***********   VACACIONES ******************#
            $rowvac = $con->Listar("SELECT id_unico,fechainiciodisfrute, fechafindisfrute, dias_hab  FROM gn_vacaciones WHERE  empleado = $emp  and tiponovedad = 7 and (fechainiciodisfrute BETWEEN '$fip'  and   '$ffip'  or fechafindisfrute BETWEEN '$fip'  and '$ffip') ");
            if(count($rowvac)>0){ 
                $pvacaciones = $periodo; 
                #Datos vacaciones 
                $rowdv =$con->Listar("SELECT DISTINCT n.empleado, 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '035') as dias , 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '144') as neto, 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '840') as salude, 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '841') as pensione, 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '842') as saludp, 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '843') as pensionp, 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '155') as vac, 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '705') as fondo, 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '800') as ccf , 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '844') as ARL, 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '861') as IBCV, 
                    (SELECT sum(valor) FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico where  n2.periodo = n.periodo  and n2.empleado = n.empleado AND c.codigo IN (841,843)) aporte_pension, 
                    (SELECT sum(valor) FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico where  n2.periodo = n.periodo  and n2.empleado = n.empleado AND c.codigo IN (849)) ccf , 
                    (SELECT n2.valor FROM gn_novedad n2 LEFT JOIN gn_concepto c ON n2.concepto = c.id_unico WHERE n.periodo = n2.periodo AND n.empleado = n2.empleado AND c.codigo = '002') as SALARIO  
                    FROM gn_novedad n 
                    where n.empleado = $emp and n.periodo in ($pvacaciones)");

                #** Campo 2 Secuencia 
                 /*contar longitud*/
                $contador = $cont;
                $lng_cont = strlen ($contador);
                $esp_cont ="";
                for($y=$lng_cont; $y < 5; $y++){
                    $esp_cont = $esp_cont."0";
                }
                $campoD2 = $esp_cont.$cont;

                #** Campo 23 - VARIACIÓN TRANSITORIA DEL SALARIO
                $campoD23 = " ";

                #** Campo 27 - VACACIONES - LICENCIA REMUNERADA
                $campoD27 = "X";

                #** Campo 36 -NÚM. DÍAS COTIZADOS A PENSIÓN
                if(!empty($rowdv[0][4])){
                    $dtb = $rowdv[0][1];
                    $dtb = round($dtb/1);
                    if($dtb<10){
                        $dtb = "0".$dtb;
                    }
                } else {
                    $dtb = "00";
                }
                $campoD36 = $dtb;

                #** Campo 37 -NÚM. DÍAS COTIZADOS A SALUD
                if(!empty($rowdv[0][3])){
                    $dtb = $rowdv[0][1];
                    $dtb = round($dtb/1);
                    if($dtb<10){
                        $dtb = "0".$dtb;
                    }
                } else {
                    $dtb = "00";
                }
                $campoD37 = $dtb;

                #** Campo 38 -NÚM. DÍAS COTIZADOS A ARL
                if(!empty($rowdv[0][10])){
                    $dtb = $rowdv[0][1];
                    $dtb = round($dtb/1);
                    if($dtb<10){
                        $dtb = "0".$dtb;
                    }
                } else {
                    $dtb = "00";
                }
                $campoD38 = $dtb;

                #** Campo 39 -NÚM. DÍAS COTIZADOS A CCF
                if(!empty($rowdv[0][9])){
                    $dtb = $rowdv[0][1];
                    $dtb = round($dtb/1);
                    if($dtb<10){
                        $dtb = "0".$dtb;
                    }
                } else {
                    $dtb = "00";
                }
                $campoD39 = $dtb;

                #** Campo 40 - Salario básico
                /*contar logitud*/
                $sl = $rowdv[0][14];
                $sl = round($sl/1);
                $lng_sl = strlen ($sl);
                $esp_sl ="";
                if($lng_sl<9){
                    for($y=$lng_sl; $y < 9; $y++){
                        $esp_sl = $esp_sl."0";
                    }
                }
                $campoD40 = $esp_sl.$sl;

                $ibc = round($rowdv[0][11]/1);   

                #** Campo 42 -IBC PENSIÓN
                if(!empty($rowdv[0][4])){
                    $lng = strlen ($ibc);
                    $esp ="";
                    if($lng<9){
                        for($y=$lng; $y < 9; $y++){
                            $esp= $esp."0";
                        }
                    }
                    $campoD42 = $esp.$ibc;
                } else {
                    $campoD42 = "000000000";
                }
                

                #** Campo 43 -IBC SALUD
                if(!empty($rowdv[0][3])){
                    $lng = strlen ($ibc);
                    $esp ="";
                    if($lng<9){
                        for($y=$lng; $y < 9; $y++){
                            $esp= $esp."0";
                        }
                    }
                    $campoD43 = $esp.$ibc;
                } else {
                    $campoD43 = "000000000";
                }
                #** Campo 44 -IBC RIESGOS
                if(!empty($rowdv[0][10])){
                    $lng = strlen ($ibc);
                    $esp ="";
                    if($lng<9){
                        for($y=$lng; $y < 9; $y++){
                            $esp= $esp."0";
                        }
                    }
                    $campoD44 = $esp.$ibc;
                } else {
                    $campoD44 = "000000000";
                }

                #** Campo 45 -IBC CCF
                if(!empty($rowdv[0][9])){
                    $lng = strlen ($ibc);
                    $esp ="";
                    if($lng<9){
                        for($y=$lng; $y < 9; $y++){
                            $esp= $esp."0";
                        }
                    }
                    $campoD45 = $esp.$ibc;
                } else {
                    $campoD45 = "000000000";
                }
                #** Campo 47 - Cotización obligatoria a pensiones
                if(!empty($rowdv[0][12])){
                    $ap_pen = $rowdv[$i][12];
                    $ap_pen = round($ap_pen/1);
                    $lng_ap_pen = strlen ($ap_pen);
                    $esp_appen ="";
                    if($lng_ap_pen<9){
                        for($y=$lng_ap_pen; $y < 9; $y++){
                            $esp_appen = $esp_appen."0";
                        }
                    }       
                    $campoD47 = $esp_appen.$ap_pen;
                } else {
                    $campoD47 = "000000000";    
                }
                            
                $campoD48 = "000000000";
                $campoD49 = "000000000";
                #** Campo 50 - Total cotización Sistema General de Pensiones
                $campoD50 = $campoD47;
                # Salud
                $campoD55 = "000000000"; 
                if(!empty($rowdv[0][3])){
                    $ap_sl = $rowdv[$i][3];
                    $ap_sl = round($ap_sl/1);
                    $lng_ap_pen = strlen ($ap_sl);
                    $esp_apsal ="";
                    if($lng_ap_pen<9){
                        for($y=$lng_ap_pen; $y < 9; $y++){
                            $esp_apsal = $esp_apsal."0";
                        }
                    }       
                    $campoD55 = $esp_apsal.$ap_sl;
                } 
                $campoD63 = "000000000"; 
                $campoD65 = "000000000"; 
                #** Campo 65 - CCF
                if(!empty($rowdv[0][13])){
                    $ap_ccf = $rowdv[$i][13];
                    $ap_ccf = round($ap_ccf/1);
                    $lng_ap_ccf = strlen ($ap_ccf);
                    $esp_apccf ="";
                    if($lng_ap_ccf<9){
                        for($y=$lng_ap_ccf; $y < 9; $y++){
                            $esp_apccf = $esp_apccf."0";
                        }
                    }       
                    $campoD65 = $esp_apccf.$ap_ccf;
                } else {
                    $campoD65 = "000000000";    
                }
                
                $campoD89 = $rowvac[0][1];
                $campoD90 = $rowvac[0][2];
                $campoD95 = $campoD45;

                $dtb = $rowdv[0][1];
                $dtb = round($dtb/1);
                $hlab = $dtb * 8;
                $lng_hlab = strlen ($hlab);
                $esp_hlab ="";
                if($lng_hlab<6){
                    for($y=$lng_hlab; $y < 3; $y++){
                        $esp_hlab = $esp_hlab."0";
                    }
                }
                $campoD96 = $esp_hlab.$hlab;

                if($exportar==3){
                    $html .='<tr>';
                    $html .='<td>'.$campoD1.'</td>';
                    $html .='<td>'.$campoD2.'</td>';
                    $html .='<td>'.$campoD3.'</td>';
                    $html .='<td>'.$campoD4.'</td>';
                    $html .='<td>'.$campoD5.'</td>';
                    $html .='<td>'.$campoD6.'</td>';
                    $html .='<td>'.$campoD7.'</td>';
                    $html .='<td>'.$campoD8.'</td>';
                    $html .='<td>'.$campoD9.'</td>';
                    $html .='<td>'.$campoD10.'</td>';
                    $html .='<td>'.$campoD11.'</td>';
                    $html .='<td>'.$campoD12.'</td>';
                    $html .='<td>'.$campoD13.'</td>';
                    $html .='<td>'.$campoD14.'</td>';
                    $html .='<td>'.$campoD15.'</td>';
                    $html .='<td>'.$campoD16.'</td>';
                    $html .='<td>'.$campoD17.'</td>';
                    $html .='<td>'.$campoD18.'</td>';
                    $html .='<td>'.$campoD19.'</td>';
                    $html .='<td>'.$campoD20.'</td>';
                    $html .='<td>'.$campoD21.'</td>';
                    $html .='<td>'.$campoD22.'</td>';  
                    $html .='<td>'.$campoD23.'</td>';  
                    $html .='<td>'.$campoD24.'</td>';  
                    $html .='<td>'.$campoD25.'</td>';  
                    $html .='<td>'.$campoD26.'</td>';  
                    $html .='<td>'.$campoD27.'</td>';  
                    $html .='<td>'.$campoD28.'</td>';  
                    $html .='<td>'.$campoD29.'</td>';  
                    $html .='<td>'.$campoD30.'</td>';  
                    $html .='<td>'.$campoD31.'</td>';  
                    $html .='<td>'.$campoD32.'</td>';  
                    $html .='<td>'.$campoD33.'</td>';  
                    $html .='<td>'.$campoD34.'</td>';  
                    $html .='<td>'.$campoD35.'</td>';  
                    $html .='<td>'.$campoD36.'</td>';  
                    $html .='<td>'.$campoD37.'</td>';  
                    $html .='<td>'.$campoD38.'</td>';  
                    $html .='<td>'.$campoD39.'</td>';  
                    $html .='<td>'.$campoD40.'</td>';  
                    $html .='<td>'.$campoD41.'</td>';  
                    $html .='<td>'.$campoD42.'</td>';  
                    $html .='<td>'.$campoD43.'</td>';  
                    $html .='<td>'.$campoD44.'</td>';  
                    $html .='<td>'.$campoD45.'</td>';  
                    $html .='<td>'.$campoD46.'</td>';  
                    $html .='<td>'.$campoD47.'</td>';  
                    $html .='<td>'.$campoD48.'</td>';  
                    $html .='<td>'.$campoD49.'</td>';  
                    $html .='<td>'.$campoD50.'</td>';  
                    $html .='<td>'.$campoD51.'</td>';  
                    $html .='<td>'.$campoD52.'</td>';  
                    $html .='<td>'.$campoD53.'</td>';  
                    $html .='<td>'.$campoD54.'</td>';  
                    $html .='<td>'.$campoD55.'</td>';  
                    $html .='<td>'.$campoD56.'</td>';  
                    $html .='<td>'.$campoD57.'</td>';  
                    $html .='<td>'.$campoD58.'</td>';  
                    $html .='<td>'.$campoD59.'</td>';  
                    $html .='<td>'.$campoD60.'</td>';  
                    $html .='<td>'.$campoD61.'</td>';  
                    $html .='<td>'.$campoD62.'</td>';  
                    $html .='<td>'.$campoD63.'</td>';  
                    $html .='<td>'.$campoD64.'</td>';  
                    $html .='<td>'.$campoD65.'</td>';                
                    $html .='<td>'.$campoD66.'</td>';  
                    $html .='<td>'.$campoD67.'</td>';  
                    $html .='<td>'.$campoD68.'</td>';  
                    $html .='<td>'.$campoD69.'</td>';  
                    $html .='<td>'.$campoD70.'</td>';  
                    $html .='<td>'.$campoD71.'</td>';  
                    $html .='<td>'.$campoD72.'</td>';  
                    $html .='<td>'.$campoD73.'</td>';  
                    $html .='<td>'.$campoD74.'</td>';  
                    $html .='<td>'.$campoD75.'</td>';  
                    $html .='<td>'.$campoD76.'</td>';  
                    $html .='<td>'.$campoD77.'</td>';  
                    $html .='<td>'.$campoD78.'</td>';  
                    $html .='<td>'.$campoD79.'</td>';  
                    $html .='<td>'.$campoD80.'</td>';  
                    $html .='<td>'.$campoD81.'</td>';  
                    $html .='<td>'.$campoD82.'</td>';  
                    $html .='<td>'.$campoD83.'</td>';  
                    $html .='<td>'.$campoD84.'</td>';  
                    $html .='<td>'.$campoD85.'</td>';  
                    $html .='<td>'.$campoD86.'</td>';  
                    $html .='<td>'.$campoD87.'</td>';  
                    $html .='<td>'.$campoD88.'</td>';  
                    $html .='<td>'.$campoD89.'</td>';  
                    $html .='<td>'.$campoD90.'</td>';  
                    $html .='<td>'.$campoD91.'</td>';  
                    $html .='<td>'.$campoD92.'</td>';  
                    $html .='<td>'.$campoD93.'</td>';  
                    $html .='<td>'.$campoD94.'</td>';  
                    $html .='<td>'.$campoD95.'</td>';  
                    $html .='<td>'.$campoD96.'</td>'; 
                   
                    $html .='</tr>';
                } else{
                        $html .=str_replace(',',' ',$campoD1)."$separador";
                        $html .=str_replace(',',' ',$campoD2)."$separador";
                        $html .=str_replace(',',' ',$campoD3)."$separador";
                        $html .=str_replace(',',' ',$campoD4)."$separador";
                        $html .=str_replace(',',' ',$campoD5)."$separador";
                        $html .=str_replace(',',' ',$campoD6)."$separador";
                        $html .=str_replace(',',' ',$campoD7)."$separador";
                        $html .=str_replace(',',' ',$campoD8)."$separador";
                        $html .=str_replace(',',' ',$campoD9)."$separador";
                        $html .=str_replace(',',' ',$campoD10)."$separador";
                        $html .=str_replace(',',' ',$campoD11)."$separador";
                        $html .=str_replace(',',' ',$campoD12)."$separador";
                        $html .=str_replace(',',' ',$campoD13)."$separador";
                        $html .=str_replace(',',' ',$campoD14)."$separador";
                        $html .=str_replace(',',' ',$campoD15)."$separador";
                        $html .=str_replace(',',' ',$campoD16)."$separador";
                        $html .=str_replace(',',' ',$campoD17)."$separador";
                        $html .=str_replace(',',' ',$campoD18)."$separador";
                        $html .=str_replace(',',' ',$campoD19)."$separador";
                        $html .=str_replace(',',' ',$campoD20)."$separador";
                        $html .=str_replace(',',' ',$campoD21)."$separador";
                        $html .=str_replace(',',' ',$campoD22)."$separador";
                        $html .=str_replace(',',' ',$campoD23)."$separador";
                        $html .=str_replace(',',' ',$campoD24)."$separador";
                        $html .=str_replace(',',' ',$campoD25)."$separador";
                        $html .=str_replace(',',' ',$campoD26)."$separador";
                        $html .=str_replace(',',' ',$campoD27)."$separador";
                        $html .=str_replace(',',' ',$campoD28)."$separador";
                        $html .=str_replace(',',' ',$campoD29)."$separador";
                        $html .=str_replace(',',' ',$campoD30)."$separador";
                        $html .=str_replace(',',' ',$campoD31)."$separador";
                        $html .=str_replace(',',' ',$campoD32)."$separador";
                        $html .=str_replace(',',' ',$campoD33)."$separador";
                        $html .=str_replace(',',' ',$campoD34)."$separador";
                        $html .=str_replace(',',' ',$campoD35)."$separador";
                        $html .=str_replace(',',' ',$campoD36)."$separador";
                        $html .=str_replace(',',' ',$campoD37)."$separador";
                        $html .=str_replace(',',' ',$campoD38)."$separador";
                        $html .=str_replace(',',' ',$campoD39)."$separador";
                        $html .=str_replace(',',' ',$campoD40)."$separador";
                        $html .=str_replace(',',' ',$campoD41)."$separador";
                        $html .=str_replace(',',' ',$campoD42)."$separador";
                        $html .=str_replace(',',' ',$campoD43)."$separador";
                        $html .=str_replace(',',' ',$campoD44)."$separador";
                        $html .=str_replace(',',' ',$campoD45)."$separador";
                        $html .=str_replace(',',' ',$campoD46)."$separador";
                        $html .=str_replace(',',' ',$campoD47)."$separador";
                        $html .=str_replace(',',' ',$campoD48)."$separador";
                        $html .=str_replace(',',' ',$campoD49)."$separador";
                        $html .=str_replace(',',' ',$campoD50)."$separador";
                        $html .=str_replace(',',' ',$campoD51)."$separador";
                        $html .=str_replace(',',' ',$campoD52)."$separador";
                        $html .=str_replace(',',' ',$campoD53)."$separador";
                        $html .=str_replace(',',' ',$campoD54)."$separador";
                        $html .=str_replace(',',' ',$campoD55)."$separador";
                        $html .=str_replace(',',' ',$campoD56)."$separador";
                        $html .=str_replace(',',' ',$campoD57)."$separador";
                        $html .=str_replace(',',' ',$campoD58)."$separador";
                        $html .=str_replace(',',' ',$campoD59)."$separador";
                        $html .=str_replace(',',' ',$campoD60)."$separador";
                        $html .=str_replace(',',' ',$campoD61)."$separador";
                        $html .=str_replace(',',' ',$campoD62)."$separador";
                        $html .=str_replace(',',' ',$campoD63)."$separador";
                        $html .=str_replace(',',' ',$campoD64)."$separador";
                        $html .=str_replace(',',' ',$campoD65)."$separador";
                        $html .=str_replace(',',' ',$campoD66)."$separador";
                        $html .=str_replace(',',' ',$campoD67)."$separador";
                        $html .=str_replace(',',' ',$campoD68)."$separador";
                        $html .=str_replace(',',' ',$campoD69)."$separador";
                        $html .=str_replace(',',' ',$campoD70)."$separador";
                        $html .=str_replace(',',' ',$campoD71)."$separador";
                        $html .=str_replace(',',' ',$campoD72)."$separador";
                        $html .=str_replace(',',' ',$campoD73)."$separador";
                        $html .=str_replace(',',' ',$campoD74)."$separador";
                        $html .=str_replace(',',' ',$campoD75)."$separador";
                        $html .=str_replace(',',' ',$campoD76)."$separador";
                        $html .=str_replace(',',' ',$campoD77)."$separador";
                        $html .=str_replace(',',' ',$campoD78)."$separador";
                        $html .=str_replace(',',' ',$campoD79)."$separador";
                        $html .=str_replace(',',' ',$campoD80)."$separador";
                        $html .=str_replace(',',' ',$campoD81)."$separador";
                        $html .=str_replace(',',' ',$campoD82)."$separador";
                        $html .=str_replace(',',' ',$campoD83)."$separador";
                        $html .=str_replace(',',' ',$campoD84)."$separador";
                        $html .=str_replace(',',' ',$campoD85)."$separador";
                        $html .=str_replace(',',' ',$campoD86)."$separador";
                        $html .=str_replace(',',' ',$campoD87)."$separador";
                        $html .=str_replace(',',' ',$campoD88)."$separador";
                        $html .=str_replace(',',' ',$campoD89)."$separador";
                        $html .=str_replace(',',' ',$campoD90)."$separador";
                        $html .=str_replace(',',' ',$campoD91)."$separador";
                        $html .=str_replace(',',' ',$campoD92)."$separador";
                        $html .=str_replace(',',' ',$campoD93)."$separador";
                        $html .=str_replace(',',' ',$campoD94)."$separador";
                        $html .=str_replace(',',' ',$campoD95)."$separador";
                        $html .=str_replace(',',' ',$campoD96);
                        $html .="\r\n";   
                }
                $cont++;  
            }

        #*********** FIN VACACIONES ******************#
            ###*************************************************************************************************************####
        #***********   INCAPACIDADES ******************#
            $rowinc = $con->Listar("SELECT id_unico, tiponovedad, fechainicio, fechafinal, numerodias FROM gn_incapacidad WHERE  empleado = $emp  and (fechainicio BETWEEN '$fip'  and   '$ffip'  or fechafinal BETWEEN '$fip'  and '$ffip') ");
            if(count($rowinc)>0){ 
                for ($in=0; $in <count($rowinc) ; $in++) { 
                    #Verifcar Tipo Incapacidad
                    $tipo_incapacidad = $rowinc[$in][1];
                    $incapacidad      = $rowinc[$in][0];

                    $rowcin = $con->Listar("SELECT id_unico, tipo_incapacidad, incapacidad, empleado, periodo, dias_incapacidad, valor, ibc, aporte_pension_patrono, aporte_pension_empleado, aporte_salud_patrono, aporte_salud_empleado, caja_compensacion, sena, icbf, esap, ministerio_educacion, institutos_tecnicos, fondo_solidaridad, arl , fecha_inicio, fecha_fin 
                        FROM gn_incapacidad_valor WHERE empleado = $emp AND incapacidad = $incapacidad AND periodo = $periodo");
                    $id_incapacidad = 0;
                    for ($ic=0; $ic <count($rowcin) ; $ic++) { 
                        #** Campo 24 - SUP. TEMP. DEL CONTRATO DE TRAB. Ó LIC. NO REMUNERADA
                        $campoD24 = " ";
                        #** Campo 25 - INC. TEMPORAL POR ENFERMEDAD GENERAL
                        $campoD25 = " ";
                        #** Campo 26 - LIC. MATERNIDAD / PATERNIDAD
                        $campoD26 = " ";
                        #** Campo 27 - VACACIONES - LICENCIA REMUNERADA
                        $campoD27 = " ";

                        switch ($tipo_incapacidad) {
                            case (1):
                                $campoD26 = "X";
                                #87 FECHA INICIO DE LIC. MATERNIDAD - PATERNIDAD
                                $campoD87 = $rowcin[$ic][20];
                                #88 FECHA FIN DE LIC. MATERNIDAD - PATERNIDAD
                                $campoD88 = $$rowcin[$ic][21];
                            break;                            
                            case (2):
                                $campoD27 = "X";
                                #89 FECHA INICIO DE VACACIONES - LICENCIA REMUNERADA
                                $campoD89 = $rowcin[$ic][20];
                                #90 FECHA FINAL DE VACACIONES - LICENCIA REMUNERADA
                                $campoD90 = $rowcin[$ic][21];
                            break;
                            case (3):
                                $campoD24 = "X";
                                #91 FECHA INICIO VCT
                                $campoD91 = $rowcin[$ic][20];
                                #92 FECHA FINAL VCT
                                $campoD92 = $rowcin[$ic][21];
                            break;
                            default:
                                $campoD25 = "X";
                                #85 FECHA INICIO DE INC. POR ENFERMEDAD GENERAL
                                $campoD85 = $rowcin[$ic][20];
                                #86 FECHA FIN DE INC. POR ENFERMEDAD GENERAL
                                $campoD86 = $rowcin[$ic][21];
                            break;
                        }
                        
                        #** Campo 2 Secuencia 
                         /*contar longitud*/
                        $contador = $cont;
                        $lng_cont = strlen ($contador);
                        $esp_cont ="";
                        for($y=$lng_cont; $y < 5; $y++){
                            $esp_cont = $esp_cont."0";
                        }
                        $campoD2 = $esp_cont.$cont;

                        #** Campo 23 - VARIACIÓN TRANSITORIA DEL SALARIO
                        $campoD23 = " ";

                        #** Campo 30 - DÍAS INC. ACCIDENTE DE TRABAJO
                        $dii = intval($rowcin[$ic][5]);
                        $lng_dii = strlen ($dii);
                        $esp_dii ="";
                        for($y=$lng_dii; $y < 2; $y++){
                            $esp_dii = $esp_dii."0";
                        }      
                        $campoD30 = $esp_dii.$dii;

                        #** Campo 36 -NÚM. DÍAS COTIZADOS A PENSION
                        $campoD36 = $campoD30;

                        #** Campo 37 -NÚM. DÍAS COTIZADOS A SALUD
                        $campoD37 = $campoD30;

                        #** Campo 38 -NÚM. DÍAS COTIZADOS A ARL
                        $campoD38 = $campoD30;;

                        #** Campo 39 -NÚM. DÍAS COTIZADOS A CCF
                        $campoD39 = $campoD30;

                        /*contar logitud*/
                        $sl = $row[$i][13];
                        $sl = round($sl/1);
                        $lng_sl = strlen ($sl);
                        $esp_sl ="";
                        if($lng_sl<9){
                            for($y=$lng_sl; $y < 9; $y++){
                                $esp_sl = $esp_sl."0";
                            }
                        }
                        $campoD40 = $esp_sl.$sl;

                        $ibc = round($rowcin[$ic][7]/1);   

                        $lng = strlen ($ibc);
                        $esp ="";
                        if($lng<9){
                            for($y=$lng; $y < 9; $y++){
                                $esp= $esp."0";
                            }
                        }
                        $ibc_t =$esp.$ibc;

                        #** Campo 42 -IBC PENSIÓN
                        $pension = round(($rowcin[$ic][8] + $rowcin[$ic][9])/1);
                        if($pension!=0){
                            
                            $campoD42 = $ibc_t;
                        } else {
                            $campoD42 = "000000000";
                        }
                        

                        #** Campo 43 -IBC SALUD
                        $salud =round(($rowcin[$ic][10] + $rowcin[$ic][11])/1);
                        if($salud!=0){
                            $campoD43 = $ibc_t;
                        } else {
                            $campoD43 = "000000000";
                        }

                        #** Campo 44 -IBC RIESGOS
                        $riesgos = round($rowcin[$ic][19]/1);
                        if($riesgos!=0){
                            $campoD44 = $ibc_t;
                        } else {
                            $campoD44 = "000000000";
                        }

                        #** Campo 45 -IBC CCF
                        $ccf = round($rowcin[$ic][12]/1);
                        if($ccf!=0){
                            $campoD45 = $ibc_t;
                        } else {
                            $campoD45 = "000000000";
                        }

                        #** Campo 47 - Cotización obligatoria a pensiones
                        $ap_pen = $pension;
                        $lng_ap_pen = strlen ($ap_pen);
                        $esp_appen ="";
                        if($lng_ap_pen<9){
                            for($y=$lng_ap_pen; $y < 9; $y++){
                                $esp_appen = $esp_appen."0";
                            }
                        }       
                        $campoD47 = $esp_appen.$ap_pen;
                                    
                        $campoD48 = "000000000";
                        $campoD49 = "000000000";
                        #** Campo 50 - Total cotización Sistema General de Pensiones
                        $campoD50 = $campoD47;
                        # Salud
                        $ap_sl = $salud;
                        $lng_ap_pen = strlen ($ap_sl);
                        $esp_apsal ="";
                        if($lng_ap_pen<9){
                            for($y=$lng_ap_pen; $y < 9; $y++){
                                $esp_apsal = $esp_apsal."0";
                            }
                        }       
                        $campoD55 = $esp_apsal.$ap_sl;

                        $campoD63 = "000000000"; 
                        $campoD65 = "000000000"; 

                        #** Campo 65 - CCF
                        $ap_ccf =$ccf;
                        $lng_ap_ccf = strlen ($ap_ccf);
                        $esp_apccf ="";
                        if($lng_ap_ccf<9){
                            for($y=$lng_ap_ccf; $y < 9; $y++){
                                $esp_apccf = $esp_apccf."0";
                            }
                        }       
                        $campoD65 = $esp_apccf.$ap_ccf;

                        $campoD95 = $campoD45;

                        
                        $campoD96 = '000';

                        if($exportar==3){
                            $html .='<tr>';
                            $html .='<td>'.$campoD1.'</td>';
                            $html .='<td>'.$campoD2.'</td>';
                            $html .='<td>'.$campoD3.'</td>';
                            $html .='<td>'.$campoD4.'</td>';
                            $html .='<td>'.$campoD5.'</td>';
                            $html .='<td>'.$campoD6.'</td>';
                            $html .='<td>'.$campoD7.'</td>';
                            $html .='<td>'.$campoD8.'</td>';
                            $html .='<td>'.$campoD9.'</td>';
                            $html .='<td>'.$campoD10.'</td>';
                            $html .='<td>'.$campoD11.'</td>';
                            $html .='<td>'.$campoD12.'</td>';
                            $html .='<td>'.$campoD13.'</td>';
                            $html .='<td>'.$campoD14.'</td>';
                            $html .='<td>'.$campoD15.'</td>';
                            $html .='<td>'.$campoD16.'</td>';
                            $html .='<td>'.$campoD17.'</td>';
                            $html .='<td>'.$campoD18.'</td>';
                            $html .='<td>'.$campoD19.'</td>';
                            $html .='<td>'.$campoD20.'</td>';
                            $html .='<td>'.$campoD21.'</td>';
                            $html .='<td>'.$campoD22.'</td>';  
                            $html .='<td>'.$campoD23.'</td>';  
                            $html .='<td>'.$campoD24.'</td>';  
                            $html .='<td>'.$campoD25.'</td>';  
                            $html .='<td>'.$campoD26.'</td>';  
                            $html .='<td>'.$campoD27.'</td>';  
                            $html .='<td>'.$campoD28.'</td>';  
                            $html .='<td>'.$campoD29.'</td>';  
                            $html .='<td>'.$campoD30.'</td>';  
                            $html .='<td>'.$campoD31.'</td>';  
                            $html .='<td>'.$campoD32.'</td>';  
                            $html .='<td>'.$campoD33.'</td>';  
                            $html .='<td>'.$campoD34.'</td>';  
                            $html .='<td>'.$campoD35.'</td>';  
                            $html .='<td>'.$campoD36.'</td>';  
                            $html .='<td>'.$campoD37.'</td>';  
                            $html .='<td>'.$campoD38.'</td>';  
                            $html .='<td>'.$campoD39.'</td>';  
                            $html .='<td>'.$campoD40.'</td>';  
                            $html .='<td>'.$campoD41.'</td>';  
                            $html .='<td>'.$campoD42.'</td>';  
                            $html .='<td>'.$campoD43.'</td>';  
                            $html .='<td>'.$campoD44.'</td>';  
                            $html .='<td>'.$campoD45.'</td>';  
                            $html .='<td>'.$campoD46.'</td>';  
                            $html .='<td>'.$campoD47.'</td>';  
                            $html .='<td>'.$campoD48.'</td>';  
                            $html .='<td>'.$campoD49.'</td>';  
                            $html .='<td>'.$campoD50.'</td>';  
                            $html .='<td>'.$campoD51.'</td>';  
                            $html .='<td>'.$campoD52.'</td>';  
                            $html .='<td>'.$campoD53.'</td>';  
                            $html .='<td>'.$campoD54.'</td>';  
                            $html .='<td>'.$campoD55.'</td>';  
                            $html .='<td>'.$campoD56.'</td>';  
                            $html .='<td>'.$campoD57.'</td>';  
                            $html .='<td>'.$campoD58.'</td>';  
                            $html .='<td>'.$campoD59.'</td>';  
                            $html .='<td>'.$campoD60.'</td>';  
                            $html .='<td>'.$campoD61.'</td>';  
                            $html .='<td>'.$campoD62.'</td>';  
                            $html .='<td>'.$campoD63.'</td>';  
                            $html .='<td>'.$campoD64.'</td>';  
                            $html .='<td>'.$campoD65.'</td>';                
                            $html .='<td>'.$campoD66.'</td>';  
                            $html .='<td>'.$campoD67.'</td>';  
                            $html .='<td>'.$campoD68.'</td>';  
                            $html .='<td>'.$campoD69.'</td>';  
                            $html .='<td>'.$campoD70.'</td>';  
                            $html .='<td>'.$campoD71.'</td>';  
                            $html .='<td>'.$campoD72.'</td>';  
                            $html .='<td>'.$campoD73.'</td>';  
                            $html .='<td>'.$campoD74.'</td>';  
                            $html .='<td>'.$campoD75.'</td>';  
                            $html .='<td>'.$campoD76.'</td>';  
                            $html .='<td>'.$campoD77.'</td>';  
                            $html .='<td>'.$campoD78.'</td>';  
                            $html .='<td>'.$campoD79.'</td>';  
                            $html .='<td>'.$campoD80.'</td>';  
                            $html .='<td>'.$campoD81.'</td>';  
                            $html .='<td>'.$campoD82.'</td>';  
                            $html .='<td>'.$campoD83.'</td>';  
                            $html .='<td>'.$campoD84.'</td>';  
                            $html .='<td>'.$campoD85.'</td>';  
                            $html .='<td>'.$campoD86.'</td>';  
                            $html .='<td>'.$campoD87.'</td>';  
                            $html .='<td>'.$campoD88.'</td>';  
                            $html .='<td>'.$campoD89.'</td>';  
                            $html .='<td>'.$campoD90.'</td>';  
                            $html .='<td>'.$campoD91.'</td>';  
                            $html .='<td>'.$campoD92.'</td>';  
                            $html .='<td>'.$campoD93.'</td>';  
                            $html .='<td>'.$campoD94.'</td>';  
                            $html .='<td>'.$campoD95.'</td>';  
                            $html .='<td>'.$campoD96.'</td>'; 
                           
                            $html .='</tr>';
                        } else{
                                $html .=str_replace(',',' ',$campoD1)."$separador";
                                $html .=str_replace(',',' ',$campoD2)."$separador";
                                $html .=str_replace(',',' ',$campoD3)."$separador";
                                $html .=str_replace(',',' ',$campoD4)."$separador";
                                $html .=str_replace(',',' ',$campoD5)."$separador";
                                $html .=str_replace(',',' ',$campoD6)."$separador";
                                $html .=str_replace(',',' ',$campoD7)."$separador";
                                $html .=str_replace(',',' ',$campoD8)."$separador";
                                $html .=str_replace(',',' ',$campoD9)."$separador";
                                $html .=str_replace(',',' ',$campoD10)."$separador";
                                $html .=str_replace(',',' ',$campoD11)."$separador";
                                $html .=str_replace(',',' ',$campoD12)."$separador";
                                $html .=str_replace(',',' ',$campoD13)."$separador";
                                $html .=str_replace(',',' ',$campoD14)."$separador";
                                $html .=str_replace(',',' ',$campoD15)."$separador";
                                $html .=str_replace(',',' ',$campoD16)."$separador";
                                $html .=str_replace(',',' ',$campoD17)."$separador";
                                $html .=str_replace(',',' ',$campoD18)."$separador";
                                $html .=str_replace(',',' ',$campoD19)."$separador";
                                $html .=str_replace(',',' ',$campoD20)."$separador";
                                $html .=str_replace(',',' ',$campoD21)."$separador";
                                $html .=str_replace(',',' ',$campoD22)."$separador";
                                $html .=str_replace(',',' ',$campoD23)."$separador";
                                $html .=str_replace(',',' ',$campoD24)."$separador";
                                $html .=str_replace(',',' ',$campoD25)."$separador";
                                $html .=str_replace(',',' ',$campoD26)."$separador";
                                $html .=str_replace(',',' ',$campoD27)."$separador";
                                $html .=str_replace(',',' ',$campoD28)."$separador";
                                $html .=str_replace(',',' ',$campoD29)."$separador";
                                $html .=str_replace(',',' ',$campoD30)."$separador";
                                $html .=str_replace(',',' ',$campoD31)."$separador";
                                $html .=str_replace(',',' ',$campoD32)."$separador";
                                $html .=str_replace(',',' ',$campoD33)."$separador";
                                $html .=str_replace(',',' ',$campoD34)."$separador";
                                $html .=str_replace(',',' ',$campoD35)."$separador";
                                $html .=str_replace(',',' ',$campoD36)."$separador";
                                $html .=str_replace(',',' ',$campoD37)."$separador";
                                $html .=str_replace(',',' ',$campoD38)."$separador";
                                $html .=str_replace(',',' ',$campoD39)."$separador";
                                $html .=str_replace(',',' ',$campoD40)."$separador";
                                $html .=str_replace(',',' ',$campoD41)."$separador";
                                $html .=str_replace(',',' ',$campoD42)."$separador";
                                $html .=str_replace(',',' ',$campoD43)."$separador";
                                $html .=str_replace(',',' ',$campoD44)."$separador";
                                $html .=str_replace(',',' ',$campoD45)."$separador";
                                $html .=str_replace(',',' ',$campoD46)."$separador";
                                $html .=str_replace(',',' ',$campoD47)."$separador";
                                $html .=str_replace(',',' ',$campoD48)."$separador";
                                $html .=str_replace(',',' ',$campoD49)."$separador";
                                $html .=str_replace(',',' ',$campoD50)."$separador";
                                $html .=str_replace(',',' ',$campoD51)."$separador";
                                $html .=str_replace(',',' ',$campoD52)."$separador";
                                $html .=str_replace(',',' ',$campoD53)."$separador";
                                $html .=str_replace(',',' ',$campoD54)."$separador";
                                $html .=str_replace(',',' ',$campoD55)."$separador";
                                $html .=str_replace(',',' ',$campoD56)."$separador";
                                $html .=str_replace(',',' ',$campoD57)."$separador";
                                $html .=str_replace(',',' ',$campoD58)."$separador";
                                $html .=str_replace(',',' ',$campoD59)."$separador";
                                $html .=str_replace(',',' ',$campoD60)."$separador";
                                $html .=str_replace(',',' ',$campoD61)."$separador";
                                $html .=str_replace(',',' ',$campoD62)."$separador";
                                $html .=str_replace(',',' ',$campoD63)."$separador";
                                $html .=str_replace(',',' ',$campoD64)."$separador";
                                $html .=str_replace(',',' ',$campoD65)."$separador";
                                $html .=str_replace(',',' ',$campoD66)."$separador";
                                $html .=str_replace(',',' ',$campoD67)."$separador";
                                $html .=str_replace(',',' ',$campoD68)."$separador";
                                $html .=str_replace(',',' ',$campoD69)."$separador";
                                $html .=str_replace(',',' ',$campoD70)."$separador";
                                $html .=str_replace(',',' ',$campoD71)."$separador";
                                $html .=str_replace(',',' ',$campoD72)."$separador";
                                $html .=str_replace(',',' ',$campoD73)."$separador";
                                $html .=str_replace(',',' ',$campoD74)."$separador";
                                $html .=str_replace(',',' ',$campoD75)."$separador";
                                $html .=str_replace(',',' ',$campoD76)."$separador";
                                $html .=str_replace(',',' ',$campoD77)."$separador";
                                $html .=str_replace(',',' ',$campoD78)."$separador";
                                $html .=str_replace(',',' ',$campoD79)."$separador";
                                $html .=str_replace(',',' ',$campoD80)."$separador";
                                $html .=str_replace(',',' ',$campoD81)."$separador";
                                $html .=str_replace(',',' ',$campoD82)."$separador";
                                $html .=str_replace(',',' ',$campoD83)."$separador";
                                $html .=str_replace(',',' ',$campoD84)."$separador";
                                $html .=str_replace(',',' ',$campoD85)."$separador";
                                $html .=str_replace(',',' ',$campoD86)."$separador";
                                $html .=str_replace(',',' ',$campoD87)."$separador";
                                $html .=str_replace(',',' ',$campoD88)."$separador";
                                $html .=str_replace(',',' ',$campoD89)."$separador";
                                $html .=str_replace(',',' ',$campoD90)."$separador";
                                $html .=str_replace(',',' ',$campoD91)."$separador";
                                $html .=str_replace(',',' ',$campoD92)."$separador";
                                $html .=str_replace(',',' ',$campoD93)."$separador";
                                $html .=str_replace(',',' ',$campoD94)."$separador";
                                $html .=str_replace(',',' ',$campoD95)."$separador";
                                $html .=str_replace(',',' ',$campoD96);
                                $html .="\r\n";   
                        }
                        $cont++;  
                    }
                }
            }
            
              
        } 

    /*#*************************** FIN Cuerpo *************************************/
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=Pila_Nomina_Empleados.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-type: application/txt"); 
                header("Content-Disposition: attachment; filename=Pila_Nomina_Empleados.txt");
                ini_set('max_execution_time', 0);
                header('Expires: 0');
    			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                echo $html;           
            break;
            #*** xls ***#
            case 3:
                header("Content-Disposition: attachment; filename=Pila_Nomina_Empleados.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>PILA NOMINA EMPLEADOS</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>TIPO DE REGISTRO</strong></center></td>
                                <td><center><strong>SECUENCIA</strong></center></td>
                                <td><center><strong>TIPO DOC. COTIZANTE</strong></center></td>
                                <td><center><strong>NUM. IDEN. COTIZANTE</strong></center></td>
                                <td><center><strong>TIPO COTIZANTE</strong></center></td>
                                <td><center><strong>SUBTIPO COTIZANTE</strong></center></td>
                                <td><center><strong>EXT. NO OBLIGADO A COTIZAR PENSIÓN</strong></center></td>
                                <td><center><strong>COLOMBIANO EN EL EXTERIOR</strong></center></td>
                                <td><center><strong>CÓD. DEPT. DE LA UBICACIÓN LABORAL</strong></center></td>
                                <td><center><strong>CÓD. MUN. DE LA UBICACIÓN LABORAL</strong></center></td>
                                <td><center><strong>PRIMER APELLIDO</strong></center></td>
                                <td><center><strong>SEGUNDO APELLIDO</strong></center></td>
                                <td><center><strong>PRIMER NOMBRE</strong></center></td>
                                <td><center><strong>SEGUNDO NOMBRE</strong></center></td>
                                <td><center><strong>INGRESO</strong></center></td>
                                <td><center><strong>RETIRO</strong></center></td>
                                <td><center><strong>TRANS. DESDE OTRA EPS</strong></center></td>
                                <td><center><strong>TRANS. A OTRA EPS</strong></center></td>
                                <td><center><strong>TRANS. DESDE OTRA ADM. DE PENSIONES</strong></center></td>
                                <td><center><strong>TRANS. A OTRA ADM. DE PENSIONES</strong></center></td>
                                <td><center><strong>VARIACION PERMANENTE DEL SALARIO</strong></center></td>
                                <td><center><strong>CORRECCIONES</strong></center></td>
                                <td><center><strong>VARIACIÓN TRANSITORIA DEL SALARIO</strong></center></td>
                                <td><center><strong>SUP. TEMP. DEL CONTRATO DE TRAB. Ó LIC. NO REMUNERADA</strong></center></td>
                                <td><center><strong>INC. TEMPORAL POR ENFERMEDAD GENERAL</strong></center></td>
                                <td><center><strong>LIC. MATERNIDAD / PATERNIDAD</strong></center></td>
                                <td><center><strong>VACACIONES - LICENCIA REMUNERADA</strong></center></td>
                                <td><center><strong>APORTE VOLUNTARIO</strong></center></td>
                                <td><center><strong>VAR. CENTROS DE TRABAJO</strong></center></td>
                                <td><center><strong>DÍAS INC. ACCIDENTE DE TRABAJO</strong></center></td>
                                <td><center><strong>CÓD. DE LA ADM. DE FONDOS DE PENSIONES A LA CUAL ESTA AFILIADO</strong></center></td>
                                <td><center><strong>CÓD. DE LA ADM. DE FONDOS DE PENSIONES A LA CUAL TRANSLADA EL AFILIADO</strong></center></td>
                                <td><center><strong>CÓD. EPS A LA CUAL ESTA AFILIADO</strong></center></td>
                                <td><center><strong>CÓD. EPS A LA CUAL TRANSLADA EL AFILIADO</strong></center></td>
                                <td><center><strong>CÓD. CCF A LA CUAL ESTA AFILIADO</strong></center></td>
                                <td><center><strong>NÚM. DÍAS COTIZADOS A PENSIÓN</strong></center></td>
                                <td><center><strong>NÚM. DÍAS COTIZADOS A SALUD</strong></center></td>
                                <td><center><strong>NÚM. DÍAS COTIZADOS A ARL</strong></center></td>
                                <td><center><strong>NÚM. DÍAS COTIZADOS A CCF</strong></center></td>
                                <td><center><strong>SALARIO BÁSICO</strong></center></td>
                                <td><center><strong>SALARIO INTEGRAL</strong></center></td>
                                <td><center><strong>IBC PENSIÓN</strong></center></td>
                                <td><center><strong>IBC SALUD</strong></center></td>
                                <td><center><strong>IBC RIESGOS PROFESIONALES</strong></center></td>
                                <td><center><strong>IBC CCF</strong></center></td>
                                <td><center><strong>TAR. APORTE DE PENSIONES</strong></center></td>
                                <td><center><strong>COTIZACIÓN OBLIGATORIA A PENSIONES</strong></center></td>
                                <td><center><strong>APORTE VOLUNTARIO AL FONDO DE PENSIONES</strong></center></td>
                                <td><center><strong>APORTE VOLUNTARIO AL FONDO DE PENSIONES</strong></center></td>
                                <td><center><strong>TOTAL COTIZACIÓN A PENSIONES</strong></center></td>
                                <td><center><strong>APORTE FONDO SOLIDARIDAD PENSIONAL</strong></center></td>
                                <td><center><strong>APORTE FONDO SOLIDARIDAD PENSIONAL</strong></center></td>
                                <td><center><strong>VALOR NO RETENIDO POR APORTES VOLUNTARIOS</strong></center></td>
                                <td><center><strong>TAR. APORTES DE SALUD</strong></center></td>
                                <td><center><strong>COTIZACIÓN OBLIGATORIA A SALUD</strong></center></td>
                                <td><center><strong>VALOR DE LA UPC ADICIONAL</strong></center></td>
                                <td><center><strong>NÚM. AUTORIZACIÓN DE LA INC. POR ENFERMEDAD GENERAL</strong></center></td>
                                <td><center><strong>VALOR DE LA INC. POR ENFERMEDAD GENERAL</strong></center></td>
                                <td><center><strong>NÚM. AUTORIZACIÓN DE LA LIC. MATERNIDAD - PATERNIDAD</strong></center></td>
                                <td><center><strong>VALOR DE LA LIC. MATERNIDAD - PATERNIDAD</strong></center></td>
                                <td><center><strong>TAR. APORTES A ARL</strong></center></td>
                                <td><center><strong>CENTRO DE TRABAJO CT</strong></center></td>
                                <td><center><strong>COTIZACIÓN OBLIGATORIA ARL</strong></center></td>
                                <td><center><strong>TAR. APORTES CCF</strong></center></td>
                                <td><center><strong>VALOR APORTES CCF</strong></center></td>
                                <td><center><strong>TAR. APORTES SENA</strong></center></td>
                                <td><center><strong>VALOR APORTES SENA</strong></center></td>
                                <td><center><strong>TAR. APORTES ICBF</strong></center></td>
                                <td><center><strong>VALOR APORTES ICBF</strong></center></td>
                                <td><center><strong>TAR. APORTES ESAP</strong></center></td>
                                <td><center><strong>VALOR APORTES ESAP</strong></center></td>
                                <td><center><strong>TAR. APORTES MEN</strong></center></td>
                                <td><center><strong>VALOR APORTES MEN</strong></center></td>
                                <td><center><strong>TIPO DOC. COTIZANTE PRINCIPAL</strong></center></td>
                                <td><center><strong>NÚM. IDEN. COTIZANTE PRINCIPAL</strong></center></td>
                                <td><center><strong>COTIZANTE EXO. DE PAGO A APORTE SALUD, SENA E ICBF</strong></center></td>
                                <td><center><strong>CÓD. ADM DE RIESGOS PROFESIONES AFILIADO</strong></center></td>
                                <td><center><strong>CLASE DE RIESGO A LA QUE SE ENCUENTRA AFILIADO</strong></center></td>
                                <td><center><strong>IND. TARIFA ESPECIAL PENSIONES</strong></center></td>
                                <td><center><strong>FECHA DE INGRESO</strong></center></td>
                                <td><center><strong>FECHA RETIRO</strong></center></td>
                                <td><center><strong>FECHA DE VARIACIÓN PERMANETE DEL SALARIO</strong></center></td>
                                <td><center><strong>FECHA INICIO DE SUSPENSIÓN TEMPORAL DEL CONTRATO</strong></center></td>
                                <td><center><strong>FECHA FIN DE SUSPENSIÓN TEMPORAL DEL CONTRATO</strong></center></td>
                                <td><center><strong>FECHA INICIO DE INC. POR ENFERMEDAD GENERAL</strong></center></td>
                                <td><center><strong>FECHA FIN DE INC. POR ENFERMEDAD GENERAL</strong></center></td>
                                <td><center><strong>FECHA INICIO DE LIC. MATERNIDAD - PATERNIDAD</strong></center></td>
                                <td><center><strong>FECHA FIN DE LIC. MATERNIDAD - PATERNIDAD</strong></center></td>
                                <td><center><strong>FECHA INICIO DE VACACIONES - LICENCIA REMUNERADA</strong></center></td>
                                <td><center><strong>FECHA FINAL DE VACACIONES - LICENCIA REMUNERADA</strong></center></td>
                                <td><center><strong>FECHA INICIO VCT</strong></center></td>
                                <td><center><strong>FECHA FINAL VCT</strong></center></td>
                                <td><center><strong>FECHA INICIO INC. POR ACCIDENTE Ó ENFERMEDAD LABORAL</strong></center></td>
                                <td><center><strong>FECHA FINAL INC. POR ACCIDENTE Ó ENFERMEDAD LABORA</strong></center></td>
                                <td><center><strong>IBC PARAFISCALES DIFERENTE A CCF</strong></center></td>
                                <td><center><strong>NÚM HORAS LABORADAS</strong></center></td>
                            </tr>
                            <?php echo $html; ?>
                        </table>
                    </body>
                </html>
                <?php             
            break;
        }
    break;
    
}
