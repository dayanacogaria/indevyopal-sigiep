    <?php 
#################MODIFICACIONES#############################
#18/05/2017 | ERICA G. | MODIFICACION DE CASE DE EGRESO
#17/05/2017 | ERICA G. | FECHA MODIFICACION EGRESO CASE 17
#11/05/2017 | ERICA G. | CASE 14, MODIFICACION SOLICITUD DISP
#08/05/2017 | ERICA G. | VALIDAR REGISTRO VACIO
#21/03/2017 | ERICA G. | VALIDACIONES EGRESO
#11/03/2017 | ERICA G. | ARCHIVO CREADO VALIDACION FECHAS COMPROBANTES
############################################################
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
session_start();
$estruc = $_REQUEST['estruc']; 
$compania =$_SESSION['compania'];
$con = new ConexionPDO();
$rowc = $con->Listar("SELECT tipo_compania FROM gf_tercero WHERE id_unico = $compania");
$validar =0;
if($rowc[0][0]==2){
    $validar =1; 
}
switch ($estruc){
    #####VALIDACION FECHA DISPONIBILIDAD VACIA#####
    case(1);
        $tipComPal = $_POST['tipComPal'];
        $fecha = $_POST['fecha']; //Seleccionada.
        $nuevaFecha = '';
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        
       ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
        ##NUMERO MAYOR###
             $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal";
            $numCom = $mysqli->query($numCom);
            $numCom = mysqli_fetch_row($numCom);
            $numCom = $numCom[0];
            ###FECHA NUMERO###
            $fechaComp="SELECT fecha
                    FROM gf_comprobante_pptal 
                    WHERE tipocomprobante = $tipComPal AND numero = $numCom";

            $fechaComp = $mysqli->query($fechaComp);
            $fechaComp=mysqli_fetch_row($fechaComp);
            $fechaComp = $fechaComp[0];
            $fecha_prev = ($fechaComp);
            if($fecha >= $fecha_prev)
            {
                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                } else {
                    $sumarDias=30;
                }
                $fecha = new DateTime($fecha);
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');
                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $nuevaFecha = $dia."/".$mes."/".$anio;

            }
            else
            {
                    $nuevaFecha = 1;
            }
        } else {
            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
            $sumDias = $mysqli->query($querySum);
            if(mysqli_num_rows($sumDias)>0) {
            $rowS = mysqli_fetch_row($sumDias);
            $sumarDias = $rowS[0];
            } else {
                $sumarDias=30;
            }
            $fecha = new DateTime($fecha);
            $fecha->modify('+'.$sumarDias.' day');
            $nuevaFecha = (string)$fecha->format('Y-m-d');
            $fecha_div = explode("-", $nuevaFecha);
            $anio = $fecha_div[0];
            $mes = $fecha_div[1];
            $dia = $fecha_div[2];
            $nuevaFecha = $dia."/".$mes."/".$anio;
        }
        echo $nuevaFecha;
    break;
    #############ASIGNAR FECHA DISPONIBILIDAD#########################
    case(2);
        $tipComPal = $_POST['tipComPal'];
        
                $queryFechComp = "SELECT fecha 
                FROM gf_comprobante_pptal 
                WHERE id_unico = (
                        SELECT MAX(id_unico) 
                    FROM gf_comprobante_pptal 
                    WHERE tipocomprobante = $tipComPal)";
        
       
        $fechComp = $mysqli->query($queryFechComp);
        $row = mysqli_fetch_row($fechComp);
        $fechaPrev = $row[0];


        $fecha_prev = new DateTime($fechaPrev);
        $nuevaFecha = (string)$fecha_prev->format('Y-m-d');
        $fecha_div = explode("-", $nuevaFecha);
        $anio = $fecha_div[0];
        $mes = $fecha_div[1];
        $dia = $fecha_div[2];

        $nuevaFecha = $dia."/".$mes."/".$anio;

        
        echo $nuevaFecha;
    break;
     #############ASIGNAR FECHA REGISTRO#########################
    case(3);
        $tipComPal = $_POST['tipComPal'];
        
        if(empty($_POST['comp'])){
        $queryFechComp = "SELECT fecha 
        FROM gf_comprobante_pptal 
        WHERE id_unico = (
                SELECT MAX(id_unico) 
            FROM gf_comprobante_pptal 
            WHERE tipocomprobante = $tipComPal)";
        
       
        $fechComp = $mysqli->query($queryFechComp);
        $row = mysqli_fetch_row($fechComp);
        $fechaPrev = $row[0];


        $fecha_prev = new DateTime($fechaPrev);
        $nuevaFecha = (string)$fecha_prev->format('Y-m-d');
        $fecha_div = explode("-", $nuevaFecha);
        $anio = $fecha_div[0];
        $mes = $fecha_div[1];
        $dia = $fecha_div[2];

        $nuevaFecha = $dia."/".$mes."/".$anio;
        } else {
            $comp = $_POST['comp'];
            $fechaDis = "SELECT fecha 
            FROM gf_comprobante_pptal 
            WHERE id_unico = $comp";
            $fechaDis = $mysqli->query($fechaDis);
            $row = mysqli_fetch_row($fechaDis);
            $fechaDis = $row[0];
            
            $FechaComp = "SELECT fecha 
            FROM gf_comprobante_pptal 
            WHERE id_unico = (
                    SELECT MAX(id_unico) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal)";
            $FechaComp = $mysqli->query($FechaComp);
            $row1 = mysqli_fetch_row($FechaComp);
            $FechaComp = $row1[0];
            if($fechaDis>$FechaComp){
                $fechaPrev=$fechaDis;
            } else {
                $fechaPrev=$FechaComp;
            }

            $fecha_prev = new DateTime($fechaPrev);
            $nuevaFecha = (string)$fecha_prev->format('Y-m-d');
            $fecha_div = explode("-", $nuevaFecha);
            $anio = $fecha_div[0];
            $mes = $fecha_div[1];
            $dia = $fecha_div[2];

            $nuevaFecha = $dia."/".$mes."/".$anio;
        }
        
        echo $nuevaFecha;
    break;
    #########VALIDAR FECHAS REGISTRO (REGISTRO NUEVO)###############
    case(4);
        $tipComPal = $_POST['tipComPal'];
        $dis= $_POST['idComPptal'];
        $fecha =$_POST['fecha'];
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
            if(!empty($dis)) {
            ##FECHA DISPONIBILIDAD###
            $fechaDis = "SELECT fecha 
            FROM gf_comprobante_pptal 
            WHERE id_unico = '$dis'";
            $fechaDis = $mysqli->query($fechaDis);
            if(mysqli_num_rows($fechaDis)>0) {

                $row = mysqli_fetch_row($fechaDis);
                $fechadis = $row[0];
                
            } else {
                $fechadis=$fecha;
            }

            ##NUMERO MAYOR###
            $numCom = "SELECT MAX(numero)
                    FROM gf_comprobante_pptal 
                    WHERE tipocomprobante = $tipComPal";

            $numCom = $mysqli->query($numCom);
            $numCom = mysqli_fetch_row($numCom);
            $numCom = $numCom[0];
            ###FECHA NUMERO###
            $fechaComp="SELECT fecha
                    FROM gf_comprobante_pptal 
                    WHERE tipocomprobante = $tipComPal AND numero = $numCom";
            $FechaComp = $mysqli->query($fechaComp);
            $row1 = mysqli_fetch_row($FechaComp);
            $FechaComp = $row1[0];
            
            if($fecha<$fechadis){
               $nuevaFecha=1;
            } else {
                if($fecha<$FechaComp){
                   $nuevaFecha=1;
                } else {
                    $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                    $sumDias = $mysqli->query($querySum);
                    if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    } else {
                        $sumarDias=30;
                    }
                    $fecha = new DateTime($fecha);
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');


                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];

                    $nuevaFecha = $dia."/".$mes."/".$anio;
                }
            }
            } else {
                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                    $sumDias = $mysqli->query($querySum);
                    if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    } else {
                        $sumarDias=30;
                    }
                    $fecha = new DateTime($fecha);
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');


                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];

                    $nuevaFecha = $dia."/".$mes."/".$anio;
            }
        } else {
            if(!empty($dis)){
            ##FECHA DISPONIBILIDAD###
            $fechaDis = "SELECT fecha 
            FROM gf_comprobante_pptal 
            WHERE id_unico = $dis";
            $fechaDis = $mysqli->query($fechaDis);
            $row = mysqli_fetch_row($fechaDis);
            $fechadis = $row[0];
            if($fecha<$fechadis){
               $nuevaFecha=1;
            } else {
                 $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                } else {
                    $sumarDias=30;
                }
                $fecha = new DateTime($fecha);
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');


                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];

                $nuevaFecha = $dia."/".$mes."/".$anio;
            }
            } else {
               
                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                } else {
                    $sumarDias=30;
                }
                $fecha = new DateTime($fecha);
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');


                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];

                $nuevaFecha = $dia."/".$mes."/".$anio;
                
            }
            
        }
        
        echo $nuevaFecha;
        
    break;
    #########VALIDAR FECHAS REGISTRO (MODIFICAR REGISTRO)###############
    case(5);
        $tipo = $_POST['tipComPal'];
        $numero=$_POST['num'];
        $reg = $_POST['idComPptal'];
        $fechaI=$_POST['fecha'];
        $fecha_div = explode("/", $fechaI);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
         #####BUSCO LA FECHA DE LA DISPONIBILIDAD######
        $fechaD="SELECT DISTINCT MAX(cpa.fecha)
              FROM
                gf_detalle_comprobante_pptal dcp
              LEFT JOIN
                gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
              LEFT JOIN
                gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico
              LEFT JOIN
                gf_comprobante_pptal cpa ON dcpa.comprobantepptal = cpa.id_unico
              WHERE
                dcp.comprobantepptal = '$reg'";
        $fechaD=$mysqli->query($fechaD);
        $fechaD= mysqli_fetch_row($fechaD);
        $fechaD=$fechaD[0];
        #######VERIFICAR SI EL NUMERO A MODIFICAR ES EL 0001############
        $aanio="SELECT anno FROM gf_parametrizacion_anno WHERE id_unico=".$_SESSION['anno'];
        $aanio=$mysqli->query($aanio);
        $aanio= mysqli_fetch_row($aanio);
        $aanio = $aanio[0];
        $numu=$aanio.'000001';  
        
        ###DEFINIR FECHA MAXIMA DE COMPARACIÓN###
        ####BUSCAR EL ULTIMO REGISTRO####
        $ur="SELECT MAX(numero) 
            FROM gf_comprobante_pptal 
            WHERE tipocomprobante = $tipo";
        $ur=$mysqli->query($ur);
        $ur= mysqli_fetch_row($ur);
        $ur=$ur[0];
        ###SI EL NUMERO ES EL MAYOR###
        if($ur==$numero){
            if($numero==$numu){ 
                if($fecha<$fechaD){
                    $nuevaFecha=1;
                } else {
                    
                    $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                    $sumDias = $mysqli->query($querySum);
                    if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    } else {
                        $sumarDias=30;
                    }
                    $fecha = new DateTime($fecha);
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');
                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $nuevaFecha = $dia."/".$mes."/".$anio;
                }
            } else { 
                #####BUSCO FECHA DEL NUMERO ANTERIOR#####
                $numA=$numero-1;
                $fechaA="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numA'";
                $fechaA=$mysqli->query($fechaA);
                $fechaA= mysqli_fetch_row($fechaA);
                $fechaA=$fechaA[0];
               
                if($fecha<$fechaD){
                    $nuevaFecha=1;
                } else {
                    if($fecha<$fechaA){
                        $nuevaFecha=1;
                    } else {
                        $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                        $sumDias = $mysqli->query($querySum);
                        if(mysqli_num_rows($sumDias)>0) {
                        $rowS = mysqli_fetch_row($sumDias);
                        $sumarDias = $rowS[0];
                        } else {
                            $sumarDias=30;
                        }
                        $fecha = new DateTime($fecha);
                        $fecha->modify('+'.$sumarDias.' day');
                        $nuevaFecha = (string)$fecha->format('Y-m-d');
                        $fecha_div = explode("-", $nuevaFecha);
                        $anio = $fecha_div[0];
                        $mes = $fecha_div[1];
                        $dia = $fecha_div[2];
                        $nuevaFecha = $dia."/".$mes."/".$anio;
                    }
                }
            }
            
        ###SI NO ES EL NUMERO MAYOR###    
        } else {
            if($numero==$numu){ 
                #########BUSCAR LA FECHA DEL COMPROBANTE SIGUIENTE#################
                $numS=$numero+1;
                $fechaS="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numS'";
                $fechaS=$mysqli->query($fechaS);
                $fechaS= mysqli_fetch_row($fechaS);
                $fechaS=$fechaS[0];

                if($fecha<$fechaD){
                    $nuevaFecha=1;
                } else {
                    if($fecha>$fechaS){
                        $nuevaFecha=1;
                    } else {
                        $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                        $sumDias = $mysqli->query($querySum);
                        if(mysqli_num_rows($sumDias)>0) {
                        $rowS = mysqli_fetch_row($sumDias);
                        $sumarDias = $rowS[0];
                        } else {
                            $sumarDias=30;
                        }
                        $fecha = new DateTime($fecha);
                        $fecha->modify('+'.$sumarDias.' day');
                        $nuevaFecha = (string)$fecha->format('Y-m-d');
                        $fecha_div = explode("-", $nuevaFecha);
                        $anio = $fecha_div[0];
                        $mes = $fecha_div[1];
                        $dia = $fecha_div[2];
                        $nuevaFecha = $dia."/".$mes."/".$anio;
                    }
                }
            } else { 
                #####BUSCO FECHA DEL NUMERO ANTERIOR#####
                $numA=$numero-1;
                $fechaA="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numA'";
                $fechaA=$mysqli->query($fechaA);
                $fechaA= mysqli_fetch_row($fechaA);
                $fechaA=$fechaA[0];
                
                #########BUSCAR LA FECHA DEL COMPROBANTE SIGUIENTE#################
                $numS=$numero+1;
                $fechaS="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numS'";
                $fechaS=$mysqli->query($fechaS);
                $fechaS= mysqli_fetch_row($fechaS);
                $fechaS=$fechaS[0];
                
                if($fecha<$fechaD){
                    $nuevaFecha=1;
                } else {
                    if($fecha<$fechaA){
                        $nuevaFecha=1;
                    } else {
                        if($fecha>$fechaS){
                            $nuevaFecha=1;
                        } else {
                            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                            $sumDias = $mysqli->query($querySum);
                            if(mysqli_num_rows($sumDias)>0) {
                            $rowS = mysqli_fetch_row($sumDias);
                            $sumarDias = $rowS[0];
                            } else {
                                $sumarDias=30;
                            }
                            $fecha = new DateTime($fecha);
                            $fecha->modify('+'.$sumarDias.' day');
                            $nuevaFecha = (string)$fecha->format('Y-m-d');
                            $fecha_div = explode("-", $nuevaFecha);
                            $anio = $fecha_div[0];
                            $mes = $fecha_div[1];
                            $dia = $fecha_div[2];
                            $nuevaFecha = $dia."/".$mes."/".$anio;
                        }
                    }
                }
            }
            
        }
        echo $nuevaFecha;
    break;
    #########VALIDAR FECHA CUENTA POR PAGAR NUEVA###############
    case(6);
        $tipo = $_POST['tipComPal'];
        $fecha =$_POST['fecha'];
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        if($validar==0){
        $com =$_POST['comp'];
        $num =$_POST['num'];
        $numero =$num-1;
        $numM ="SELECT fecha FROM gf_comprobante_pptal WHERE numero =$numero AND tipocomprobante =$tipo";
        $numM =$mysqli->query($numM);
        $numM = mysqli_fetch_row($numM);
        $fechaNumM =$numM[0];
        if($fecha<$fechaNumM){
            $res=1;
        }else {
            
            if( $com !="N"){
                ###FECHA DEL REGISTRO ###
                $fr ="SELECT fecha FROM gf_comprobante_pptal WHERE id_unico =$com";
                $fr =$mysqli->query($fr);
                $fr = mysqli_fetch_row($fr);
                $fr =$fr[0];
                if($fecha <$fr){
                    $res=1;
                } else {
                    $res=2;
                }
            } else {
                $res=2;
            }
        }
        } else {
            $res =2;
        }
        
        echo $res;
        
    break;
    #########VALIDAR FECHA CUENTA POR PAGAR (MODIFICAR)###############
    case(7);
        
        $numero=$_POST['num'];
        $reg = $_POST['comp']; 
        $fecha=$_POST['fecha'];
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        if($validar==0){
        ##########BUSCAR TIPO COMPROBANTE PPTAL############
        $tipoC="SELECT tipocomprobante FROM gf_comprobante_pptal WHERE id_unico ='$reg'";
        $tipoC =$mysqli->query($tipoC);
        $tipoC= mysqli_fetch_row($tipoC);
        $tipo = $tipoC[0];
        ##########BUSCAR FECHA REGISTRO############
        $fechaReg ="SELECT MAX(ca.fecha)
              FROM
                gf_detalle_comprobante_pptal dcp
              LEFT JOIN
                gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico
              LEFT JOIN
                gf_comprobante_pptal ca ON dcpa.comprobantepptal = ca.id_unico
              WHERE
                dcp.comprobantepptal = $reg";
        $fechaReg =$mysqli->query($fechaReg);
        $fechaReg = mysqli_fetch_row($fechaReg);
        $fSol =$fechaReg[0];
        
        $numM  = $numero-1;
        $numMa = $numero+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numM AND tipocomprobante =$tipo";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numMa AND tipocomprobante =$tipo";
        $fnma=$mysqli->query($fnma);
        ##SE VALIDA SI HAY NUMEROS MENORES
        if(mysqli_num_rows($fnm)>0){
            ###SI HAY FECHA MENOR###
            #VALIDAR FECHA ESCOGIDA SEA MAYOR A LA FECHA MENOR#
            $fm = mysqli_fetch_row($fnm);
            $fechaM= $fm[0];
            if($fecha>=$fechaM){
                ##VALIDAR SI HAY NUMEROS MAYORES##
                if(mysqli_num_rows($fnma)>0){
                ####SI SI ES MAYOR VALIDAR QUE LA FECHA ESCOGIDA SEA MENOR A FECHA MAYOR###
                    $fechaMa = mysqli_fetch_row($fnma);
                    $fechaMa = $fechaMa[0];
                    //var_dump($fecha<$fechaMa);
                    if($fecha<=$fechaMa){
                        ###VALIDAR FECHA REGISTRO
                        if($fecha>=$fSol){
                        
                            $nuevaFecha =1;   
                        } else {
                            $nuevaFecha =2;   
                        }
                    } else {
                        $nuevaFecha =2; 
                    }
                } else{
                   if($fecha>=$fSol){
                        
                   $nuevaFecha =1;   
                    } else {
                        $nuevaFecha =2;   
                    }
                }
            } else {
                $nuevaFecha =2; 
            }
        } else {
            ###SI NO HAY FECHA MENOR### 
            ##VALIDAR SI HAY NUMEROS MAYORES##
            if(mysqli_num_rows($fnma)>0){
            ####SI SI ES MAYOR VALIDAR QUE LA FECHA ESCOGIDA SEA MENOR A FECHA MAYOR###
                $fechaMa = mysqli_fetch_row($fnma);
                $fechaMa = $fechaMa[0];
                if($fecha<=$fechaMa){
                    if($fecha>=$fSol){
                        $nuevaFecha =1;   
                    } else {
                        $nuevaFecha =2;   
                    }  
                } else {
                    $nuevaFecha =2; 
                }
            } else{
               if($fecha>=$fSol){   
                    $nuevaFecha =1;   
                } else {
                    $nuevaFecha =2;   
                }
            }
        }
        } else {
            $nuevaFecha =1;   
        }
        echo $nuevaFecha;
        
    break;
    ##########VALIDAR FECHA DISPONIBILIDAD (MODIFICAR)###############
    case(8);
        $tipo = $_POST['tipComPal'];
        $numero=$_POST['num'];
        $reg = $_POST['idComPptal'];
        $fechaI=$_POST['fecha'];
        
        ###DEFINIR FECHA MAXIMA DE COMPARACIÓN###
        ####BUSCAR EL ULTIMO REGISTRO####
       $ur="SELECT MAX(numero) 
            FROM gf_comprobante_pptal 
            WHERE tipocomprobante = $tipo";
        $ur=$mysqli->query($ur);
        $ur= mysqli_fetch_row($ur);
        $ur=$ur[0];
        ###SI EL NUMERO ES EL MAYOR###
        if($ur==$numero){
            ########VERIFICAR SI EL NUMERO A MODIFICAR ES EL 0001############
            $aanio="SELECT anno FROM gf_parametrizacion_anno WHERE id_unico=".$_SESSION['anno'];
            $aanio=$mysqli->query($aanio);
            $aanio= mysqli_fetch_row($aanio);
            $aanio = $aanio[0];
            $numu=$aanio.'000001';
            //var_dump ($numero==$numu);
            if($numero==$numu){ 
                $fecha_div = explode("/", $fechaI);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fecha = $anio."-".$mes."-".$dia;
                $fecha = new DateTime($fecha);
                
                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                } else {
                    $sumarDias=30;
                }
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');
                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $nuevaFecha = $dia."/".$mes."/".$anio;
            } else { 
                #####BUSCO FECHA DEL NUMERO ANTERIOR#####
                $numA=$numero-1;
                $fechaA="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' "
                        . "AND numero = '$numA'";
                $fechaA=$mysqli->query($fechaA);
                $fechaA= mysqli_fetch_row($fechaA);
                $fechaA=$fechaA[0];
                $fecha_div = explode("/", $fechaI);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fecha = $anio."-".$mes."-".$dia;
                

                if($fecha<$fechaA){
                    $nuevaFecha=1;
                } else {
                    $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                    $sumDias = $mysqli->query($querySum);
                    if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    } else {
                        $sumarDias=30;
                    }
                    $fecha = new DateTime($fecha);
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');
                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $nuevaFecha = $dia."/".$mes."/".$anio;
                }
            }
            
            
        ###SI NO ES EL NUMERO MAYOR###    
        } else {
            #####BUSCAR SI EL EL 001#######
            $aanio="SELECT anno FROM gf_parametrizacion_anno WHERE id_unico=".$_SESSION['anno'];
            $aanio=$mysqli->query($aanio);
            $aanio= mysqli_fetch_row($aanio);
            $aanio = $aanio[0];
            $numu=$aanio.'000001';
            //var_dump ($numero==$numu);
            if($numero==$numu){ 
                #########BUSCAR LA FECHA DEL COMPROBANTE SIGUIENTE#################
                $numS=$numero+1;
                $fechaS="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numS'";
                $fechaS=$mysqli->query($fechaS);
                $fechaS= mysqli_fetch_row($fechaS);
                $fechaS=$fechaS[0];
                $fecha_div = explode("/", $fechaI);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fecha = $anio."-".$mes."-".$dia;
                
                if($fecha>$fechaS){
                    $nuevaFecha=1;
                } else {
                    $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                    $sumDias = $mysqli->query($querySum);
                    if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    } else {
                        $sumarDias=30;
                    }
                    $fecha = new DateTime($fecha);
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');
                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $nuevaFecha = $dia."/".$mes."/".$anio;
                }
                
            } else { 
                #####BUSCO FECHA DEL NUMERO ANTERIOR#####
                $numA=$numero-1;
                $fechaA="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numA'";
                $fechaA=$mysqli->query($fechaA);
                $fechaA= mysqli_fetch_row($fechaA);
                $fechaA=$fechaA[0];

                #########BUSCAR LA FECHA DEL COMPROBANTE SIGUIENTE#################
                $numS=$numero+1;
                $fechaS="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numS'";
                $fechaS=$mysqli->query($fechaS);
                $fechaS= mysqli_fetch_row($fechaS);
                $fechaS=$fechaS[0];
                $fecha_div = explode("/", $fechaI);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fecha = $anio."-".$mes."-".$dia;
                

                if($fecha<$fechaA){
                    $nuevaFecha=1;
                } else {
                    if($fecha>$fechaS){
                        $nuevaFecha=1;
                    } else {
                        $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                        $sumDias = $mysqli->query($querySum);
                        if(mysqli_num_rows($sumDias)>0) {
                        $rowS = mysqli_fetch_row($sumDias);
                        $sumarDias = $rowS[0];
                        } else {
                            $sumarDias=30;
                        }
                        $fecha = new DateTime($fecha);
                        $fecha->modify('+'.$sumarDias.' day');
                        $nuevaFecha = (string)$fecha->format('Y-m-d');
                        $fecha_div = explode("-", $nuevaFecha);
                        $anio = $fecha_div[0];
                        $mes = $fecha_div[1];
                        $dia = $fecha_div[2];
                        $nuevaFecha = $dia."/".$mes."/".$anio;
                    }
                }
            }
            
            
        }
        echo $nuevaFecha;
    break;
    ######VALIDAR FECHA EGRESO NUEVO#######
    case(9);
        $tipo = $_POST['tipComPal'];
        $fecha =$_POST['fecha'];
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        if($validar==0){
        $com =$_POST['comp'];
        $num =$_POST['num'];
        $numero =$num-1;
        $numM ="SELECT fecha FROM gf_comprobante_pptal WHERE numero =$numero AND tipocomprobante =$tipo";
        $numM =$mysqli->query($numM);
        $numM = mysqli_fetch_row($numM);
        $fechaNumM =$numM[0];
        if($fecha<$fechaNumM){
            $res=1;
        }else {
            
            if( $com !="N" || $com !=""){
                ###FECHA DEL REGISTRO ###
                $fr ="SELECT fecha FROM gf_comprobante_pptal WHERE id_unico =$com";
                $fr =$mysqli->query($fr);
                $fr = mysqli_fetch_row($fr);
                $fr =$fr[0];
                if($fecha <$fr){
                    $res=1;
                } else {
                    $res=2;
                }
            } else {
                $res=2;
            }
        }
        } else {
            $res =2;
        }
        
        echo $res;
    break;
    #######FECHA MODIFICAR EGRESO#########
      case(10);
        
        $numero=$_POST['num'];
        $reg = $_POST['comp'];
        $fechaI=$_POST['fecha'];
        ##########BUSCAR TIPO COMPROBANTE PPTAL############
        $tipoC="SELECT tipocomprobante FROM gf_comprobante_pptal WHERE id_unico ='$reg'";
        $tipoC =$mysqli->query($tipoC);
        $tipoC= mysqli_fetch_row($tipoC);
        $tipo = $tipoC[0];
        ###DEFINIR FECHA MAXIMA DE COMPARACIÓN###
        ####BUSCAR LA ULTIMA CXP####
        $ur="SELECT MAX(numero) 
            FROM gf_comprobante_pptal 
            WHERE tipocomprobante = $tipo";
        $ur=$mysqli->query($ur);
        $ur= mysqli_fetch_row($ur);
        $ur=$ur[0];
        ###SI EL NUMERO ES EL MAYOR###
        if($ur==$numero){
            #######VERIFICAR SI EL NUMERO A MODIFICAR ES EL 0001############
            $aanio="SELECT anno FROM gf_parametrizacion_anno WHERE id_unico=".$_SESSION['anno'];
            $aanio=$mysqli->query($aanio);
            $aanio= mysqli_fetch_row($aanio);
            $aanio = $aanio[0];
            $numu=$aanio.'000001';
            if($numero==$numu){ 
                #####BUSCO LA FECHA DEL REGISTRO######
                $fechaD="SELECT DISTINCT MAX(cpa.fecha)
                      FROM
                        gf_detalle_comprobante_pptal dcp
                      LEFT JOIN
                        gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                      LEFT JOIN
                        gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico
                      LEFT JOIN
                        gf_comprobante_pptal cpa ON dcpa.comprobantepptal = cpa.id_unico
                      WHERE
                        dcp.comprobantepptal = '$reg'";
                $fechaD=$mysqli->query($fechaD);
                $fechaD= mysqli_fetch_row($fechaD);
                $fechaD=$fechaD[0];
                $fecha_div = explode("/", $fechaI);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fecha = $anio."-".$mes."-".$dia;
                if($fecha<$fechaD){
                    $nuevaFecha=1;
                } else {
                    $nuevaFecha=0;  
                }
            } else {
                #####BUSCO FECHA DEL NUMERO ANTERIOR#####
                $numA=$numero-1;
                $fechaA="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numA'";
                $fechaA=$mysqli->query($fechaA);
                $fechaA= mysqli_fetch_row($fechaA);
                $fechaA=$fechaA[0];
                #####BUSCO LA FECHA DEL REGISTRO######
                $fechaD="SELECT DISTINCT MAX(cpa.fecha)
                      FROM
                        gf_detalle_comprobante_pptal dcp
                      LEFT JOIN
                        gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                      LEFT JOIN
                        gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico
                      LEFT JOIN
                        gf_comprobante_pptal cpa ON dcpa.comprobantepptal = cpa.id_unico
                      WHERE
                        dcp.comprobantepptal = '$reg'";
                $fechaD=$mysqli->query($fechaD);
                $fechaD= mysqli_fetch_row($fechaD);
                $fechaD=$fechaD[0];
                $fecha_div = explode("/", $fechaI);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fecha = $anio."-".$mes."-".$dia;
                if($fecha<$fechaD){
                    $nuevaFecha=1;
                } else {
                    if($fecha<$fechaA){
                        $nuevaFecha=1;
                    } else {
                        $nuevaFecha=0;
                    }
                }
            }
            
        ###SI NO ES EL NUMERO MAYOR###    
        } else {
            ########VERIFICAR SI EL NUMERO A MODIFICAR ES EL 0001############
            $aanio="SELECT anno FROM gf_parametrizacion_anno WHERE id_unico=".$_SESSION['anno'];
            $aanio=$mysqli->query($aanio);
            $aanio= mysqli_fetch_row($aanio);
            $aanio = $aanio[0];
            $numu=$aanio.'000001';
            //var_dump ($numero==$numu);
            if($numero==$numu){
                #####BUSCO LA FECHA DE CXP######
                 $fechaD="SELECT DISTINCT MAX(cpa.fecha)
                      FROM
                        gf_detalle_comprobante_pptal dcp
                      LEFT JOIN
                        gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                      LEFT JOIN
                        gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico
                      LEFT JOIN
                        gf_comprobante_pptal cpa ON dcpa.comprobantepptal = cpa.id_unico
                      WHERE
                        dcp.comprobantepptal = '$reg'";
                $fechaD=$mysqli->query($fechaD);
                $fechaD= mysqli_fetch_row($fechaD);
                $fechaD=$fechaD[0];
                
                #########BUSCAR LA FECHA DEL COMPROBANTE SIGUIENTE#################
                $numS=$numero+1;
                $fechaS="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numS'";
                $fechaS=$mysqli->query($fechaS);
                $fechaS= mysqli_fetch_row($fechaS);
                $fechaS=$fechaS[0];


                
                $fecha_div = explode("/", $fechaI);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fecha = $anio."-".$mes."-".$dia;
                
                
                if($fecha<$fechaD){
                    $nuevaFecha=1;
                } else {
                    //var_dump($fecha.' - - '.$fechaS);   
                    if($fecha>$fechaS){
                        $nuevaFecha=1;
                    } else {
                        
                        $nuevaFecha=0;
                    }
                    
                }
                
            } else {
                #####BUSCO FECHA DEL NUMERO ANTERIOR#####
                $numA=$numero-1;
                $fechaA="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numA'";
                $fechaA=$mysqli->query($fechaA);
                $fechaA= mysqli_fetch_row($fechaA);
                $fechaA=$fechaA[0];
                #####BUSCO LA FECHA DE LA DISPONIBILIDAD######
                $fechaD="SELECT DISTINCT MAX(cpa.fecha)
                      FROM
                        gf_detalle_comprobante_pptal dcp
                      LEFT JOIN
                        gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                      LEFT JOIN
                        gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico
                      LEFT JOIN
                        gf_comprobante_pptal cpa ON dcpa.comprobantepptal = cpa.id_unico
                      WHERE
                        dcp.comprobantepptal = '$reg'";
                $fechaD=$mysqli->query($fechaD);
                $fechaD= mysqli_fetch_row($fechaD);
                $fechaD=$fechaD[0];

                #########BUSCAR LA FECHA DEL COMPROBANTE SIGUIENTE#################
                $numS=$numero+1;
                $fechaS="SELECT fecha FROM gf_comprobante_pptal WHERE tipocomprobante = '$tipo' AND numero = '$numS'";
                $fechaS=$mysqli->query($fechaS);
                $fechaS= mysqli_fetch_row($fechaS);
                $fechaS=$fechaS[0];
                $fecha_div = explode("/", $fechaI);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fecha = $anio."-".$mes."-".$dia;
                if($fecha<$fechaD){
                    $nuevaFecha=1;
                } else {
                    if($fecha<$fechaA){
                        $nuevaFecha=1;
                    } else {
                        if($fecha>$fechaS){
                            $nuevaFecha=1;
                        } else {
                            $nuevaFecha=0;
                        }
                    }
                }
            }
            
        }
        echo $nuevaFecha;
        
    break;
    
    
    ######VALIDAR FECHA ADICIÓN APROPIACION#######
    case(11);
        $tipComPal = $_POST['tipComPal'];
        $fecha = $_POST['fecha']; //Seleccionada.
        $nuevaFecha = '';
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        
       ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
        ##NUMERO MAYOR###
             $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal";
            $numCom = $mysqli->query($numCom);
            $numCom = mysqli_fetch_row($numCom);
            $numCom = $numCom[0];
            ###FECHA NUMERO###
            $fechaComp="SELECT fecha
                    FROM gf_comprobante_pptal 
                    WHERE tipocomprobante = $tipComPal AND numero = $numCom";

            $fechaComp = $mysqli->query($fechaComp);
            $fechaComp=mysqli_fetch_row($fechaComp);
            $fechaComp = $fechaComp[0];
            $fecha_prev = ($fechaComp);
            if($fecha >= $fecha_prev)
            {
                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                } else {
                    $sumarDias=30;
                }
                $fecha = new DateTime($fecha);
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');
                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $nuevaFecha = $dia."/".$mes."/".$anio;

            }
            else
            {
                    $nuevaFecha = 1;
            }
        } else {
            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
            $sumDias = $mysqli->query($querySum);
            if(mysqli_num_rows($sumDias)>0) {
            $rowS = mysqli_fetch_row($sumDias);
            $sumarDias = $rowS[0];
            } else {
                $sumarDias=30;
            }
            $fecha = new DateTime($fecha);
            $fecha->modify('+'.$sumarDias.' day');
            $nuevaFecha = (string)$fecha->format('Y-m-d');
            $fecha_div = explode("-", $nuevaFecha);
            $anio = $fecha_div[0];
            $mes = $fecha_div[1];
            $dia = $fecha_div[2];
            $nuevaFecha = $dia."/".$mes."/".$anio;
        }
        echo $nuevaFecha;
        
    break; 
    ##########VALIDAR FECHA ADICION APROPIACION (MODIFICAR)###############
    case(12);
        $tipComPal = $_POST['tipComPal'];
        $fecha = $_POST['fecha']; //Seleccionada.
        $nuevaFecha = '';
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        $num = $_POST['num'];
        
        $numM = $num-1;
        $numMa = $num+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numM AND tipocomprobante =$tipComPal";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numMa AND tipocomprobante =$tipComPal";
        $fnma=$mysqli->query($fnma);
        ##SE VALIDA SI HAY NUMEROS MENORES
        if(mysqli_num_rows($fnm)>0){
            ###SI HAY FECHA MENOR###
            #VALIDAR FECHA ESCOGIDA SEA MAYOR A LA FECHA MENOR#
            $fm = mysqli_fetch_row($fnm);
            $fechaM= $fm[0];
            if($fecha>=$fechaM){
                ##VALIDAR SI HAY NUMEROS MAYORES##
                if(mysqli_num_rows($fnma)>0){
                ####SI SI ES MAYOR VALIDAR QUE LA FECHA ESCOGIDA SEA MENOR A FECHA MAYOR###
                    $fechaMa = mysqli_fetch_row($fnma);
                    $fechaMa = $fechaMa[0];
                    //var_dump($fecha<$fechaMa);
                    if($fecha<=$fechaMa){
                       $nuevaFecha =1;   
                    } else {
                        $nuevaFecha =2; 
                    }
                } else{
                   $nuevaFecha =1; 
                }
            } else {
                $nuevaFecha =2; 
            }
        } else {
            ###SI NO HAY FECHA MENOR### 
            ##VALIDAR SI HAY NUMEROS MAYORES##
            if(mysqli_num_rows($fnma)>0){
            ####SI SI ES MAYOR VALIDAR QUE LA FECHA ESCOGIDA SEA MENOR A FECHA MAYOR###
                $fechaMa = mysqli_fetch_row($fnma);
                $fechaMa = $fechaMa[0];
                if($fecha<=$fechaMa){
                  $nuevaFecha =1;   
                } else {
                    $nuevaFecha =2; 
                }
            } else{
               $nuevaFecha =1; 
            }
        }
        if($nuevaFecha ==1){
            $fecha = new DateTime($fecha);
            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
            $sumDias = $mysqli->query($querySum);
            if(mysqli_num_rows($sumDias)>0) {
            $rowS = mysqli_fetch_row($sumDias);
            $sumarDias = $rowS[0];
            } else {
                $sumarDias=30;
            }
            $fecha->modify('+'.$sumarDias.' day');
            $nuevaFecha = (string)$fecha->format('Y-m-d');
            $fecha_div = explode("-", $nuevaFecha);
            $anio = $fecha_div[0];
            $mes = $fecha_div[1];
            $dia = $fecha_div[2];
            $nuevaFecha = $dia."/".$mes."/".$anio;
        }
        echo $nuevaFecha;
        
        
    break;
    case 13:
        #####FECHA SOLICITUD DE DIS
        $tipComPal = $_POST['tipComPal'];
        $fecha = $_POST['fecha']; //Seleccionada.
        $nuevaFecha = '';
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        $panno = $_SESSION['anno'];
       ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal and parametrizacionanno = $panno ";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
        ##NUMERO MAYOR###
             $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal and parametrizacionanno = $panno ";
            $numCom = $mysqli->query($numCom);
            $numCom = mysqli_fetch_row($numCom);
            $numCom = $numCom[0];
            ###FECHA NUMERO###
            $fechaComp="SELECT fecha
                    FROM gf_comprobante_pptal 
                    WHERE tipocomprobante = $tipComPal AND numero = $numCom";

            $fechaComp = $mysqli->query($fechaComp);
            $fechaComp=mysqli_fetch_row($fechaComp);
            $fechaComp = $fechaComp[0];
            $fecha_prev = ($fechaComp);
            if($fecha >= $fecha_prev)
            {
                $nuevaFecha= 1;

            }
            else
            {
                $nuevaFecha= 2;
            }
        } else {
            $nuevaFecha= 1;
        }
        echo $nuevaFecha;
    break;
    case 14:
        #####FECHA SOLICITUD DE DIS MODIFICAR
        $id = $_POST['id'];
        $tipComPal = $_POST['tipComPal'];
        $fecha = $_POST['fecha']; //Seleccionada.
        $nuevaFecha = '';
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        $num = $_POST['num'];
        
        $numM = $num-1;
        $numMa = $num+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numM AND tipocomprobante =$tipComPal";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numMa AND tipocomprobante =$tipComPal";
        $fnma=$mysqli->query($fnma);
        ##SE VALIDA SI HAY NUMEROS MENORES
        if(mysqli_num_rows($fnm)>0){
            ###SI HAY FECHA MENOR###
            #VALIDAR FECHA ESCOGIDA SEA MAYOR A LA FECHA MENOR#
            $fm = mysqli_fetch_row($fnm);
            $fechaM= $fm[0];
            if($fecha>=$fechaM){
                ##VALIDAR SI HAY NUMEROS MAYORES##
                if(mysqli_num_rows($fnma)>0){
                ####SI SI ES MAYOR VALIDAR QUE LA FECHA ESCOGIDA SEA MENOR A FECHA MAYOR###
                    $fechaMa = mysqli_fetch_row($fnma);
                    $fechaMa = $fechaMa[0];
                    //var_dump($fecha<$fechaMa);
                    if($fecha<=$fechaMa){
                       $nuevaFecha =1;   
                    } else {
                        $nuevaFecha =2; 
                    }
                } else{
                   $nuevaFecha =1; 
                }
            } else {
                $nuevaFecha =2; 
            }
        } else {
            ###SI NO HAY FECHA MENOR### 
            ##VALIDAR SI HAY NUMEROS MAYORES##
            if(mysqli_num_rows($fnma)>0){
            ####SI SI ES MAYOR VALIDAR QUE LA FECHA ESCOGIDA SEA MENOR A FECHA MAYOR###
                $fechaMa = mysqli_fetch_row($fnma);
                $fechaMa = $fechaMa[0];
                if($fecha<=$fechaMa){
                  $nuevaFecha =1;   
                } else {
                    $nuevaFecha =2; 
                }
            } else{
               $nuevaFecha =1; 
            }
        }
        
        echo $nuevaFecha;
    break;
    ####VALIDACION FECHA REGISTRO NUEVO APROBACION SOLICITUD####
    case(15);
        $tipComPal = 7;
        $fecha = $_POST['fecha']; //Seleccionada.
        $solicitud =$_POST['solicitud'];
        $nuevaFecha = '';
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        
       ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
        ##NUMERO MAYOR###
             $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal";
            $numCom = $mysqli->query($numCom);
            $numCom = mysqli_fetch_row($numCom);
            $numCom = $numCom[0];
            ###FECHA NUMERO###
            $fechaComp="SELECT fecha
                    FROM gf_comprobante_pptal 
                    WHERE tipocomprobante = $tipComPal AND numero = $numCom";

            $fechaComp = $mysqli->query($fechaComp);
            $fechaComp=mysqli_fetch_row($fechaComp);
            $fechaComp = $fechaComp[0];
            $fecha_prev = ($fechaComp);
            if($fecha >= $fecha_prev)
            {
                
                $nuevaFecha = 1;

            }
            else
            {
                ##########VERIFICAR FECHA SOLICITUD#######
                $fs ="SELECT fecha FROM gf_comprobante_pptal WHERE id_unico =$solicitud";
                $fs =$mysqli->query($fs);
                $fs= mysqli_fetch_row($fs);
                $fs =$fs[0];
                if($fecha >= $fs)
                {
                    $nuevaFecha=1;
                } else {
                    
                    $nuevaFecha = 2;
                }
                
            }
        } else {
            $nuevaFecha=1;
        }
        echo $nuevaFecha;
    break;
    ####VALIDACION FECHA REGISTRO MODIFICAR APROBACION SOLICITUD####
    case(16);
         #####FECHA APROBACION DE SOL MODIFICAR
        $id = $_POST['id'];
        $tipComPal = 7;
        $fecha = $_POST['fecha']; //Seleccionada.
        $nuevaFecha = '';
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        $num = $_POST['num'];
        $fSol = $_POST['fSol'];
        
        $numM = $num-1;
        $numMa = $num+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numM AND tipocomprobante =$tipComPal";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numMa AND tipocomprobante =$tipComPal";
        $fnma=$mysqli->query($fnma);
        ##SE VALIDA SI HAY NUMEROS MENORES
        if(mysqli_num_rows($fnm)>0){
            ###SI HAY FECHA MENOR###
            #VALIDAR FECHA ESCOGIDA SEA MAYOR A LA FECHA MENOR#
            $fm = mysqli_fetch_row($fnm);
            $fechaM= $fm[0];
            if($fecha>=$fechaM){
                ##VALIDAR SI HAY NUMEROS MAYORES##
                if(mysqli_num_rows($fnma)>0){
                ####SI SI ES MAYOR VALIDAR QUE LA FECHA ESCOGIDA SEA MENOR A FECHA MAYOR###
                    $fechaMa = mysqli_fetch_row($fnma);
                    $fechaMa = $fechaMa[0];
                    //var_dump($fecha<$fechaMa);
                    if($fecha<=$fechaMa){
                        ###VALIDAR FECHA SOLICITUD
                        if($fecha>=$fSol){
                        
                       $nuevaFecha =1;   
                        } else {
                            $nuevaFecha =2;   
                        }
                    } else {
                        $nuevaFecha =2; 
                    }
                } else{
                   if($fecha>=$fSol){
                        
                   $nuevaFecha =1;   
                    } else {
                        $nuevaFecha =2;   
                    }
                }
            } else {
                $nuevaFecha =2; 
            }
        } else {
            ###SI NO HAY FECHA MENOR### 
            ##VALIDAR SI HAY NUMEROS MAYORES##
            if(mysqli_num_rows($fnma)>0){
            ####SI SI ES MAYOR VALIDAR QUE LA FECHA ESCOGIDA SEA MENOR A FECHA MAYOR###
                $fechaMa = mysqli_fetch_row($fnma);
                $fechaMa = $fechaMa[0];
                if($fecha<=$fechaMa){
                    if($fecha>=$fSol){
                        $nuevaFecha =1;   
                    } else {
                        $nuevaFecha =2;   
                    }  
                } else {
                    $nuevaFecha =2; 
                }
            } else{
               if($fecha>=$fSol){   
                    $nuevaFecha =1;   
                } else {
                    $nuevaFecha =2;   
                }
            }
        }
        
        echo $nuevaFecha;
    break;
    case 17:
        ###########VALIDAR FECHA MODIFICACION EGRESO############
        $numero=$_POST['num'];
        $reg = $_POST['comp'];
        $fechaI=$_POST['fecha'];
        ##########BUSCAR TIPO COMPROBANTE PPTAL############
        $tipoC="SELECT
                tcp.id_unico
              FROM
                gf_tipo_comprobante_pptal tcp
              LEFT JOIN
                gf_tipo_comprobante tc ON tc.comprobante_pptal = tcp.id_unico
              LEFT JOIN
                gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico
              WHERE
                cn.id_unico ='$reg'";
        $tipoC =$mysqli->query($tipoC);
        $tipoC= mysqli_fetch_row($tipoC);
        $tipComPal = $tipoC[0];
        
        $fecha_div = explode("/", $fechaI);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        $num = $_POST['num'];
        //FECHA CUENTA POR PAGAR
        $fcx = "SELECT DISTINCT MAX(cpa.fecha)
            FROM
              gf_comprobante_cnt cn
            LEFT JOIN
              gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante
            LEFT JOIN
              gf_detalle_comprobante_pptal dcp ON dc.detallecomprobantepptal = dcp.id_unico
            LEFT JOIN
              gf_comprobante_pptal cpa ON dcp.comprobantepptal = cpa.id_unico
            WHERE
              cn.id_unico =$reg";
        $fcx = $mysqli->query($fcx);
        $fcx = mysqli_fetch_row($fcx);
        if(empty($fcx[0])|| $fcx[0]==""){
            $fSol=$fecha;
        } else {
            $fSol = $fcx[0];
        }
        $numM = $num-1;
        $numMa = $num+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numM AND tipocomprobante =$tipComPal";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numMa AND tipocomprobante =$tipComPal";
        $fnma=$mysqli->query($fnma);
        ##SE VALIDA SI HAY NUMEROS MENORES
        if(mysqli_num_rows($fnm)>0){
            ###SI HAY FECHA MENOR###
            #VALIDAR FECHA ESCOGIDA SEA MAYOR A LA FECHA MENOR#
            $fm = mysqli_fetch_row($fnm);
            $fechaM= $fm[0];
            if($fecha>=$fechaM){
                ##VALIDAR SI HAY NUMEROS MAYORES##
                if(mysqli_num_rows($fnma)>0){
                ####SI SI ES MAYOR VALIDAR QUE LA FECHA ESCOGIDA SEA MENOR A FECHA MAYOR###
                    $fechaMa = mysqli_fetch_row($fnma);
                    $fechaMa = $fechaMa[0];
                    //var_dump($fecha<$fechaMa);
                    if($fecha<=$fechaMa){
                        ###VALIDAR FECHA SOLICITUD
                        if($fecha>=$fSol){
                        
                       $nuevaFecha =1;   
                        } else {
                            $nuevaFecha =2;   
                        }
                    } else {
                        $nuevaFecha =2; 
                    }
                } else{
                   if($fecha>=$fSol){
                        
                   $nuevaFecha =1;   
                    } else {
                        $nuevaFecha =2;   
                    }
                }
            } else {
                $nuevaFecha =2; 
            }
        } else {
            ###SI NO HAY FECHA MENOR### 
            ##VALIDAR SI HAY NUMEROS MAYORES##
            if(mysqli_num_rows($fnma)>0){
            ####SI SI ES MAYOR VALIDAR QUE LA FECHA ESCOGIDA SEA MENOR A FECHA MAYOR###
                $fechaMa = mysqli_fetch_row($fnma);
                $fechaMa = $fechaMa[0];
                if($fecha<=$fechaMa){
                    if($fecha>=$fSol){
                        $nuevaFecha =1;   
                    } else {
                        $nuevaFecha =2;   
                    }  
                } else {
                    $nuevaFecha =2; 
                }
            } else{
               if($fecha>=$fSol){   
                    $nuevaFecha =1;   
                } else {
                    $nuevaFecha =2;   
                }
            }
        }
        
        echo $nuevaFecha;
        
        
    break;
    
    
}