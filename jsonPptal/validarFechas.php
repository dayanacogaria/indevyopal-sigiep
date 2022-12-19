    <?php 
#######################################################################################
#24/07/2017 |Erica G. | Case 26 Translado Modificar, funcion fechaC(Convierte la fecha )
#11/07/2017 |ERICA G. | CASE 25 TRASLADO PPTAL
#16/06/2017 |ERICA G. | CASE 1 Y 2 ADICION A APROPIACION
#09/06/2017 |ERICA G. |ARCHIVO CREADO
#######################################################################################
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once './funcionesPptal.php';
session_start(); 
$estruc = $_REQUEST['estruc'];
$anno     = $_SESSION['anno'];
$compania =$_SESSION['compania'];
$con = new ConexionPDO();
#** Verificar Tipo De Compania****#
$rowc = $con->Listar("SELECT tipo_compania FROM gf_tercero WHERE id_unico = $compania");
$validar =0;
if($rowc[0][0]==2){
    $validar =1;
}
switch ($estruc){
    ############ADICION A APROPIACIÓN VACIA###################
    case 1:
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal AND parametrizacionanno = $anno";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
        ##NUMERO MAYOR###
             $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal  AND parametrizacionanno = $anno";
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
    ############MODIFICACION A APROPIACION#############
    case 2:
        $tipComPal = $_POST['tipComPal'];
       $fecha = fechaC($_POST['fecha']);
        $nuevaFecha = '';
        $num = $_POST['num'];
        $numM = $num-1;
        $numMa = $num+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numM AND tipocomprobante =$tipComPal AND parametrizacionanno = $anno";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numMa AND tipocomprobante =$tipComPal  AND parametrizacionanno = $anno";
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
        } else {
            $nuevaFecha =1;
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
    #SOLICITUD A DISPONIBILIDAD NUEVA
    case 3:
    break;
    ##########################################################################
    ##SOLICITUD A DISPONIBILIDAD MODIFICAR
    case 4:
    break;
    ##########################################################################
    #APROBAR SOLICITUD NUEVA
    case 5:
    break;
    ##########################################################################
    #APROBAR SOLICITUD MODIFICAR
    case 6:
    break;
    ##########################################################################
     ########DISPONIBILIDAD VACIA###################
    case 7:
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        
        if($validar==0){
            ####VERIFICA SI HAY COMPROBANTES####
            $numCompr = "SELECT COUNT(*) 
                    FROM gf_comprobante_pptal 
                    WHERE tipocomprobante = $tipComPal AND parametrizacionanno = $anno";
            $numCompr =$mysqli->query($numCompr);
            $numCompr= mysqli_fetch_row($numCompr);
            $numCompr = $numCompr[0];
        if($numCompr>0){
        ##NUMERO MAYOR###
             $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal AND parametrizacionanno = $anno";
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
                     $fecha = new DateTime($fecha);
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');
                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $nuevaFecha = $dia."/".$mes."/".$anio;
                } else {
                    $fecha = new DateTime($fecha);
                    $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                    $fecha_div2 = explode("-", $nuevaFecha2);
                    $anio = $fecha_div2[0];
                    $nuevaFecha = "31/12/".$anio;
                }
               

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
                $fecha = new DateTime($fecha);
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');
                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $nuevaFecha = $dia."/".$mes."/".$anio;
            } else {
                $fecha = new DateTime($fecha);
                $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                $fecha_div2 = explode("-", $nuevaFecha2);
                $anio = $fecha_div2[0];
                $nuevaFecha = "31/12/".$anio;
            }
            
        }
        } else {
            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
            $sumDias = $mysqli->query($querySum);
            if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                $fecha = new DateTime($fecha);
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');
                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $nuevaFecha = $dia."/".$mes."/".$anio;
            } else {
                $fecha = new DateTime($fecha);
                $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                $fecha_div2 = explode("-", $nuevaFecha2);
                $anio = $fecha_div2[0];
                $nuevaFecha = "31/12/".$anio;
            }
            
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
    ########DISPONIBILIDAD (SOLICIITUD)########
    case 8:
        $fecha = fechaC($_POST['fecha']);
        if($validar==0){
        $solicitud = $_POST['solicitud'];
        $tipComPal = $_POST['tipComPal'];
       
        ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal  AND parametrizacionanno = $anno";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
            ##NUMERO MAYOR###
            $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal  AND parametrizacionanno = $anno"; 
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
                #########VALIDAR FECHA SOLICITUD#####
                $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$solicitud;
                $fs =$mysqli->query($fs);
                $fs  = mysqli_fetch_row($fs);
                $fs  = $fs[0];
                ###FECHA NUMERO###
                $fechaSol=$fs;
                if($fecha >= $fechaSol)
                {
                    $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                    $sumDias = $mysqli->query($querySum);
                    if(mysqli_num_rows($sumDias)>0) {
                        $rowS = mysqli_fetch_row($sumDias);
                        $sumarDias = $rowS[0];
                        $fecha = new DateTime($fecha);
                        $fecha->modify('+'.$sumarDias.' day');
                        $nuevaFecha = (string)$fecha->format('Y-m-d');
                        $fecha_div = explode("-", $nuevaFecha);
                        $anio = $fecha_div[0];
                        $mes = $fecha_div[1];
                        $dia = $fecha_div[2];
                        $nuevaFecha = $dia."/".$mes."/".$anio;
                    } else {
                        $fecha = new DateTime($fecha);
                        $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                        $fecha_div2 = explode("-", $nuevaFecha2);
                        $anio = $fecha_div2[0];
                        $nuevaFecha = "31/12/".$anio;
                    }
                    
                } else {
                    $nuevaFecha = 1;
                }

            }
            else
            {
                    $nuevaFecha = 1;
            }
        } else {
            #########VALIDAR FECHA SOLICITUD#####
            $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$solicitud;
            $fs =$mysqli->query($fs);
            $fs  = mysqli_fetch_row($fs);
            $fs  = $fs[0];
            ###FECHA NUMERO###
            $fechaSol=$fs;
            if($fecha >= $fechaSol)
            {
                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    $fecha = new DateTime($fecha);
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');
                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $nuevaFecha = $dia."/".$mes."/".$anio;
                } else {
                    $fecha = new DateTime($fecha);
                    $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                    $fecha_div2 = explode("-", $nuevaFecha2);
                    $anio = $fecha_div2[0];
                    $nuevaFecha = "31/12/".$anio;
                }
                
            } else {
                $nuevaFecha = 1;
            }
        }
        } else {
            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
            $sumDias = $mysqli->query($querySum);
            if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                $fecha = new DateTime($fecha);
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');
                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $nuevaFecha = $dia."/".$mes."/".$anio;
            } else {
                $fecha = new DateTime($fecha);
                $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                $fecha_div2 = explode("-", $nuevaFecha2);
                $anio = $fecha_div2[0];
                $nuevaFecha = "31/12/".$anio;
            }
            
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
    ########MODIFICAR DISPONIBILIDAD ###############
    case 9:
        $tipComPal = $_POST['tipComPal'];
        $fecha = $_POST['fecha']; //Seleccionada.
        $nuevaFecha = '';
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio."-".$mes."-".$dia;
        $num = $_POST['num'];
        if(!empty($_REQUEST['tipoc'])){
            if($_REQUEST['tipoc']==2){
                $validar=1;
            } 
        }
        if($validar==0){
            $numM = $num-1;
            $numMa = $num+1;
            #####BUSCAR LA FECHA DEL NUMERO MENOR##
            $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                    . "numero = $numM AND tipocomprobante =$tipComPal AND parametrizacionanno = $anno";
            $fnm = $mysqli->query($fnm);
            #####BUSCAR LA FECHA DEL NUMERO MAYOR##
            $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                    . "numero = $numMa AND tipocomprobante =$tipComPal  AND parametrizacionanno = $anno";
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
                    if($fecha<=$fechaMa){
                       ##VALIDAR FECHA SOLICITUD
                        if(!empty($_POST['solicitud'])){
                            $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                            $fs = $mysqli->query($fs);
                            $fs = mysqli_fetch_row($fs);
                            $fs = $fs[0];
                            ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                            if($fecha >= $fs){
                               $nuevaFecha =1;  
                            } else {
                                $nuevaFecha =2; 
                            }
                        } else {
                            $nuevaFecha =1;   
                        }
                    } else {
                        $nuevaFecha =2; 
                    }
                } else{
                    ##VALIDAR FECHA SOLICITUD
                    if(!empty($_POST['sol'])){
                        $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                        $fs = $mysqli->query($fs);
                        $fs = mysqli_fetch_row($fs);
                        $fs = $fs[0];
                        ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                        if($fecha >= $fs){
                           $nuevaFecha =1;  
                        } else {
                            $nuevaFecha =2; 
                        }
                    } else {
                        $nuevaFecha =1;   
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
                    ##VALIDAR FECHA SOLICITUD
                    if(!empty($_POST['sol'])){
                        $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                        $fs = $mysqli->query($fs);
                        $fs = mysqli_fetch_row($fs);
                        $fs = $fs[0];
                        ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                        if($fecha >= $fs){
                           $nuevaFecha =1;  
                        } else {
                            $nuevaFecha =2; 
                        }
                    } else {
                        $nuevaFecha =1;   
                    }
                } else {
                    $nuevaFecha =2; 
                }
            } else{
                ##VALIDAR FECHA SOLICITUD
                if(!empty($_POST['sol'])){
                    $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                    $fs = $mysqli->query($fs);
                    $fs = mysqli_fetch_row($fs);
                    $fs = $fs[0];
                    ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                    if($fecha >= $fs){
                       $nuevaFecha =1;  
                    } else {
                        $nuevaFecha =2; 
                    }
                } else {
                    $nuevaFecha =1;   
                }
            }
        }
        } else {
            $nuevaFecha =1;
        }
        if($nuevaFecha ==1){
            $fecha = new DateTime($fecha);
            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
            $sumDias = $mysqli->query($querySum);
            if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');
                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $nuevaFecha = $dia."/".$mes."/".$anio;
            } else {
                $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                $fecha_div2 = explode("-", $nuevaFecha2);
                $anio = $fecha_div2[0];
                $nuevaFecha = "31/12/".$anio;
            }
           
        } else {
            $nuevaFecha =1;
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
    ########REGISTRO NUEVO###############
    case 10:
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        $num = $_POST['num'];
        
        $numM = $num-1;
        $numMa = $num+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numM AND tipocomprobante =$tipComPal AND parametrizacionanno = $anno";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numMa AND tipocomprobante =$tipComPal  AND parametrizacionanno = $anno";
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
                    if($fecha<=$fechaMa){
                       ##VALIDAR FECHA DISPONIBILIDAD
                        if(!empty($_POST['dis'])){
                            $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['dis'];
                            $fs = $mysqli->query($fs);
                            $fs = mysqli_fetch_row($fs);
                            $fs = $fs[0];
                            ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                            if($fecha >= $fs){
                               $nuevaFecha =1;  
                            } else {
                                $nuevaFecha =2; 
                            }
                        } else {
                            $nuevaFecha =1;   
                        }
                    } else {
                        $nuevaFecha =2; 
                    }
                } else{
                    ##VALIDAR FECHA DISPONIBILIDAD
                    #echo $_POST['dis'];
                    if(!empty($_POST['dis'])){
                        $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['dis'];
                        $fs = $mysqli->query($fs);
                        $fs = mysqli_fetch_row($fs);
                        $fs = $fs[0];
                        ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                        if($fecha >= $fs){
                           $nuevaFecha =1;  
                        } else {
                            $nuevaFecha =2; 
                        }
                    } else {
                        $nuevaFecha =1;   
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
                    ##VALIDAR FECHA SOLICITUD
                    if(!empty($_POST['dis'])){
                        $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['dis'];
                        $fs = $mysqli->query($fs);
                        $fs = mysqli_fetch_row($fs);
                        $fs = $fs[0];
                        ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA DISPONIBILIDAD
                        if($fecha >= $fs){
                           $nuevaFecha =1;  
                        } else {
                            $nuevaFecha =2; 
                        }
                    } else {
                        $nuevaFecha =1;   
                    }
                } else {
                    $nuevaFecha =2; 
                }
            } else{
                ##VALIDAR FECHA DISPONIBILIDAD
                if(!empty($_POST['dis'])){
                    $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['dis'];
                    $fs = $mysqli->query($fs);
                    $fs = mysqli_fetch_row($fs);
                    $fs = $fs[0];
                    ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                    if($fecha >= $fs){
                       $nuevaFecha =1;  
                    } else {
                        $nuevaFecha =2; 
                    }
                } else {
                    $nuevaFecha =1;   
                }
            }
        }
        if($nuevaFecha ==1){
            ###Validar si Trae Disponibilidad Seleccionada
            
            if(empty($_REQUEST['dis'])){
            
                $fecha = new DateTime($fecha);
                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');
                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $nuevaFecha = $dia."/".$mes."/".$anio;
                } else {
                    $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                    $fecha_div2 = explode("-", $nuevaFecha2);
                    $anio = $fecha_div2[0];
                    $nuevaFecha = "31/12/".$anio;
                }
                
            } else {
                #***buscar la fecha de la disponibilidad
                $ds = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico =".$_REQUEST['dis'];
                $ds = $mysqli->query($ds);
                $ds = mysqli_fetch_row($ds);
                $fd = $ds[0];
                if($fecha<$fd){
                    $nuevaFecha =1;
                } else {
                    $fecha = new DateTime($fecha);
                    $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                    $sumDias = $mysqli->query($querySum);
                    if(mysqli_num_rows($sumDias)>0) {
                        $rowS = mysqli_fetch_row($sumDias);
                        $sumarDias = $rowS[0];
                        $fecha->modify('+'.$sumarDias.' day');
                        $nuevaFecha = (string)$fecha->format('Y-m-d');
                        $fecha_div = explode("-", $nuevaFecha);
                        $anio = $fecha_div[0];
                        $mes = $fecha_div[1];
                        $dia = $fecha_div[2];
                        $nuevaFecha = $dia."/".$mes."/".$anio;
                    } else {
                        $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                        $fecha_div2 = explode("-", $nuevaFecha2);
                        $anio = $fecha_div2[0];
                        $nuevaFecha = "31/12/".$anio;
                    }
                    
                }
                
            }
        } else {
            $nuevaFecha =1;
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
    #REGISTRO MODIFICAR
    case 12:
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        $num = $_POST['num'];
        
        $numM = $num-1;
        $numMa = $num+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numM AND tipocomprobante =$tipComPal AND parametrizacionanno = $anno";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numMa AND tipocomprobante =$tipComPal  AND parametrizacionanno = $anno";
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
                    if($fecha<=$fechaMa){
                       ##VALIDAR FECHA SOLICITUD
                        if(!empty($_POST['sol'])){
                            $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                            $fs = $mysqli->query($fs);
                            $fs = mysqli_fetch_row($fs);
                            $fs = $fs[0];
                            ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                            if($fecha >= $fs){
                               $nuevaFecha =1;  
                            } else {
                                $nuevaFecha =2; 
                            }
                        } else {
                            $nuevaFecha =1;   
                        }
                    } else {
                        $nuevaFecha =2; 
                    }
                } else{
                    ##VALIDAR FECHA SOLICITUD
                    if(!empty($_POST['sol'])){
                        $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                        $fs = $mysqli->query($fs);
                        $fs = mysqli_fetch_row($fs);
                        $fs = $fs[0];
                        ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                        if($fecha >= $fs){
                           $nuevaFecha =1;  
                        } else {
                            $nuevaFecha =2; 
                        }
                    } else {
                        $nuevaFecha =1;   
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
                    ##VALIDAR FECHA SOLICITUD
                    if(!empty($_POST['sol'])){
                        $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                        $fs = $mysqli->query($fs);
                        $fs = mysqli_fetch_row($fs);
                        $fs = $fs[0];
                        ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                        if($fecha >= $fs){
                           $nuevaFecha =1;  
                        } else {
                            $nuevaFecha =2; 
                        }
                    } else {
                        $nuevaFecha =1;   
                    }
                } else {
                    $nuevaFecha =2; 
                }
            } else{
                ##VALIDAR FECHA SOLICITUD
                if(!empty($_POST['sol'])){
                    $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                    $fs = $mysqli->query($fs);
                    $fs = mysqli_fetch_row($fs);
                    $fs = $fs[0];
                    ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                    if($fecha >= $fs){
                       $nuevaFecha =1;  
                    } else {
                        $nuevaFecha =2; 
                    }
                } else {
                    $nuevaFecha =1;   
                }
            }
        }
        if($nuevaFecha ==1){
            ###Validar Si Tiene Afectado
            $id = $_REQUEST['idComPptal'];
            $af = "SELECT 
                    ca.fecha 
            FROM 
                    gf_detalle_comprobante_pptal dc 
            LEFT JOIN 
                    gf_detalle_comprobante_pptal dca ON dc.comprobanteafectado = dca.id_unico 
            LEFT JOIN 
                    gf_comprobante_pptal ca ON dca.comprobantepptal = ca.id_unico 
            WHERE 
                    dc.comprobantepptal = $id";
            $af = $mysqli->query($af);
            if(mysqli_num_rows($af)>0){
                $rta=1;
                $af = mysqli_fetch_row($af);
                $fechadis = $af[0];
                if(empty($fechadis)){
                    $rta =2;
                } else {
                    if($fecha<$fechadis){
                        $rta =1;
                    } else {
                        $rta =2;
                    }
                }
                if($rta==1){
                    $nuevaFecha=1;
                }elseif($rta==2){
                    $fecha = new DateTime($fecha);
                    $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                    $sumDias = $mysqli->query($querySum);
                    if(mysqli_num_rows($sumDias)>0) {
                        $rowS = mysqli_fetch_row($sumDias);
                        $sumarDias = $rowS[0];
                        $fecha->modify('+'.$sumarDias.' day');
                        $nuevaFecha = (string)$fecha->format('Y-m-d');
                        $fecha_div = explode("-", $nuevaFecha);
                        $anio = $fecha_div[0];
                        $mes = $fecha_div[1];
                        $dia = $fecha_div[2];
                        $nuevaFecha = $dia."/".$mes."/".$anio;
                    } else {
                        $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                        $fecha_div2 = explode("-", $nuevaFecha2);
                        $anio = $fecha_div2[0];
                        $nuevaFecha = "31/12/".$anio;
                    }
                    
                }
                
            } else {
                $fecha = new DateTime($fecha);
                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');
                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $nuevaFecha = $dia."/".$mes."/".$anio;
                } else {
                    $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                    $fecha_div2 = explode("-", $nuevaFecha2);
                    $anio = $fecha_div2[0];
                    $nuevaFecha = "31/12/".$anio;
                }
                
                
            }
            
            
        } else {
            $nuevaFecha =1;
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
    #ORDEN DE PAGO
    case 13:
    break;
    ##########################################################################
    #CUENTA POR PAGAR VACÍA
    case 14:
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal AND parametrizacionanno = $anno";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
        ##NUMERO MAYOR###
             $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal AND parametrizacionanno = $anno";
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
                $fecha = new DateTime($fecha);

                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');
                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $nuevaFecha = $dia."/".$mes."/".$anio;
                } else {
                    $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                    $fecha_div2 = explode("-", $nuevaFecha2);
                    $anio = $fecha_div2[0];
                    $nuevaFecha = "31/12/".$anio;
                }
                
                

            }
            else
            {
                    $nuevaFecha = 1;
            }
        } else {
            $fecha = new DateTime($fecha);
            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
            $sumDias = $mysqli->query($querySum);
            if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');
                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $nuevaFecha = $dia."/".$mes."/".$anio;
            } else {
                $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                $fecha_div2 = explode("-", $nuevaFecha2);
                $anio = $fecha_div2[0];
                $nuevaFecha = "31/12/".$anio;
            }            
            
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
    #CUENTA POR PAGAR NUEVA CON REGISTRO
    case 15:
    break;
    ##########################################################################
    #CUENTA POR PAGAR MODIFICAR
    case 16:
    break;
    ##########################################################################
    #EGRESO VACIO
    case 17:
    break;
    ##########################################################################
    #EGRESO CUENTA POR PAGAR
    case 18:
    break;
    ##########################################################################
    #EGRESO MODIFICAR
    case 19:
    break;
    ##########################################################################
    ########MODIFICACION A DISPONIBILIDAD VACIA###################
    case 20:
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal AND parametrizacionanno = $anno";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
        ##NUMERO MAYOR###
             $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal AND parametrizacionanno = $anno";
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
                $fecha = new DateTime($fecha);
                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');
                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $nuevaFecha = $dia."/".$mes."/".$anio;
                } else {
                    $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                    $fecha_div2 = explode("-", $nuevaFecha2);
                    $anio = $fecha_div2[0];
                    $nuevaFecha = "31/12/".$anio;
                }

            }
            else
            {
                    $nuevaFecha = 1;
            }
        } else {
            $fecha = new DateTime($fecha);
            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
            $sumDias = $mysqli->query($querySum);
            if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');
                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $nuevaFecha = $dia."/".$mes."/".$anio;
            } else {
                $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                $fecha_div2 = explode("-", $nuevaFecha2);
                $anio = $fecha_div2[0];
                $nuevaFecha = "31/12/".$anio;
            }
            
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
    ########MODIFICACION A DISPONIBILIDAD (DISPONIBILIDAD)########
    case 21:
        $solicitud = $_POST['solicitud'];
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal  AND parametrizacionanno = $anno";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
            ##NUMERO MAYOR###
            $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal  AND parametrizacionanno = $anno"; 
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
                #########VALIDAR FECHA SOLICITUD#####
                $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$solicitud;
                $fs =$mysqli->query($fs);
                $fs  = mysqli_fetch_row($fs);
                $fs  = $fs[0];
                ###FECHA NUMERO###
                $fechaSol=$fs;
                if($fecha >= $fechaSol)
                {
                    $fecha = new DateTime($fecha);
                    $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                    $sumDias = $mysqli->query($querySum);
                    if(mysqli_num_rows($sumDias)>0) {
                        $rowS = mysqli_fetch_row($sumDias);
                        $sumarDias = $rowS[0];
                        $fecha->modify('+'.$sumarDias.' day');
                        $nuevaFecha = (string)$fecha->format('Y-m-d');
                        $fecha_div = explode("-", $nuevaFecha);
                        $anio = $fecha_div[0];
                        $mes = $fecha_div[1];
                        $dia = $fecha_div[2];
                        $nuevaFecha = $dia."/".$mes."/".$anio;
                    } else {
                        $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                        $fecha_div2 = explode("-", $nuevaFecha2);
                        $anio = $fecha_div2[0];
                        $nuevaFecha = "31/12/".$anio;
                    }                    
                    
                } else {
                    $nuevaFecha = 1;
                }

            }
            else
            {
                    $nuevaFecha = 1;
            }
        } else {
            #########VALIDAR FECHA SOLICITUD#####
            $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$solicitud;
            $fs =$mysqli->query($fs);
            $fs  = mysqli_fetch_row($fs);
            $fs  = $fs[0];
            ###FECHA NUMERO###
            $fechaSol=$fs;
            if($fecha >= $fechaSol)
            {
                $fecha = new DateTime($fecha);
                $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
                $sumDias = $mysqli->query($querySum);
                if(mysqli_num_rows($sumDias)>0) {
                    $rowS = mysqli_fetch_row($sumDias);
                    $sumarDias = $rowS[0];
                    $fecha->modify('+'.$sumarDias.' day');
                    $nuevaFecha = (string)$fecha->format('Y-m-d');
                    $fecha_div = explode("-", $nuevaFecha);
                    $anio = $fecha_div[0];
                    $mes = $fecha_div[1];
                    $dia = $fecha_div[2];
                    $nuevaFecha = $dia."/".$mes."/".$anio;
                } else {
                    $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                    $fecha_div2 = explode("-", $nuevaFecha2);
                    $anio = $fecha_div2[0];
                    $nuevaFecha = "31/12/".$anio;
                }
                
            } else {
                $nuevaFecha = 1;
            }
        }
        echo $nuevaFecha; 
    break;
    ##########################################################################
    ########MODIFICACION A DISPONIBILIDAD MODIFICAR###############
    case 22:
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        $num = $_POST['num'];
        
        $numM = $num-1;
        $numMa = $num+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numM AND tipocomprobante =$tipComPal AND parametrizacionanno = $anno";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numMa AND tipocomprobante =$tipComPal  AND parametrizacionanno = $anno";
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
                    if($fecha<=$fechaMa){
                       ##VALIDAR FECHA SOLICITUD
                        if(!empty($_POST['sol'])){
                            $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                            $fs = $mysqli->query($fs);
                            $fs = mysqli_fetch_row($fs);
                            $fs = $fs[0];
                            ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                            if($fecha >= $fs){
                               $nuevaFecha =1;  
                            } else {
                                $nuevaFecha =2; 
                            }
                        } else {
                            $nuevaFecha =1;   
                        }
                    } else {
                        $nuevaFecha =2; 
                    }
                } else{
                    ##VALIDAR FECHA SOLICITUD
                    if(!empty($_POST['sol'])){
                        $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                        $fs = $mysqli->query($fs);
                        $fs = mysqli_fetch_row($fs);
                        $fs = $fs[0];
                        ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                        if($fecha >= $fs){
                           $nuevaFecha =1;  
                        } else {
                            $nuevaFecha =2; 
                        }
                    } else {
                        $nuevaFecha =1;   
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
                    ##VALIDAR FECHA SOLICITUD
                    if(!empty($_POST['sol'])){
                        $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                        $fs = $mysqli->query($fs);
                        $fs = mysqli_fetch_row($fs);
                        $fs = $fs[0];
                        ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                        if($fecha >= $fs){
                           $nuevaFecha =1;  
                        } else {
                            $nuevaFecha =2; 
                        }
                    } else {
                        $nuevaFecha =1;   
                    }
                } else {
                    $nuevaFecha =2; 
                }
            } else{
                ##VALIDAR FECHA SOLICITUD
                if(!empty($_POST['sol'])){
                    $fs = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_POST['sol'];
                    $fs = $mysqli->query($fs);
                    $fs = mysqli_fetch_row($fs);
                    $fs = $fs[0];
                    ##VERIFICA QUE LA FECHA SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
                    if($fecha >= $fs){
                       $nuevaFecha =1;  
                    } else {
                        $nuevaFecha =2; 
                    }
                } else {
                    $nuevaFecha =1;   
                }
            }
        }
        if($nuevaFecha ==1){
            $fecha = new DateTime($fecha);
            $querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
            $sumDias = $mysqli->query($querySum);
            if(mysqli_num_rows($sumDias)>0) {
                $rowS = mysqli_fetch_row($sumDias);
                $sumarDias = $rowS[0];
                $fecha->modify('+'.$sumarDias.' day');
                $nuevaFecha = (string)$fecha->format('Y-m-d');
                $fecha_div = explode("-", $nuevaFecha);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $nuevaFecha = $dia."/".$mes."/".$anio;
            } else {
                $nuevaFecha2 = (string)$fecha->format('Y-m-d');
                $fecha_div2 = explode("-", $nuevaFecha2);
                $anio = $fecha_div2[0];
                $nuevaFecha = "31/12/".$anio;
            }
            
        } else {
            $nuevaFecha =1;
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
    #COMPROBANTE CONTABLE VACIO
    case 23:
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_cnt 
                WHERE tipocomprobante = $tipComPal AND  parametrizacionanno = $anno";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
        ##NUMERO MAYOR###
             $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_cnt 
                WHERE tipocomprobante = $tipComPal  AND parametrizacionanno = $anno";
            $numCom = $mysqli->query($numCom);
            $numCom = mysqli_fetch_row($numCom);
            $numCom = $numCom[0];
            ###FECHA NUMERO###
            $fechaComp="SELECT fecha
                    FROM gf_comprobante_cnt 
                    WHERE tipocomprobante = $tipComPal AND numero = $numCom";

            $fechaComp = $mysqli->query($fechaComp);
            $fechaComp=mysqli_fetch_row($fechaComp);
            $fechaComp = $fechaComp[0];
            $fecha_prev = ($fechaComp);
            if($fecha >= $fecha_prev)
            {
                $nuevaFecha =2;

            }
            else
            {
                    $nuevaFecha = 1;
            }
        } else {
            $nuevaFecha =2;
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
    ############COMPROBANTE CONTABLE MODIFICAR
    case 24:
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        $num = $_POST['num'];
        $numM = $num-1;
        $numMa = $num+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_cnt WHERE "
                . "numero = $numM AND tipocomprobante =$tipComPal AND parametrizacionanno = $anno";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_cnt  WHERE "
                . "numero = $numMa AND tipocomprobante =$tipComPal  AND parametrizacionanno = $anno";
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
            $nuevaFecha =2;
        } else {
            $nuevaFecha =1;
        }
        echo $nuevaFecha;
    break;
    ##########################################################################
     ########TRASLADO PRESUPUESTAL VACIO###################
    case 25:
        $tipComPal = $_POST['tipComPal'];
        $fecha = fechaC($_POST['fecha']);
        ####VERIFICA SI HAY COMPROBANTES####
        $numCompr = "SELECT COUNT(*) 
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal AND parametrizacionanno = $anno";
        $numCompr =$mysqli->query($numCompr);
        $numCompr= mysqli_fetch_row($numCompr);
        $numCompr = $numCompr[0];
        if($numCompr>0){
        ##NUMERO MAYOR###
             $numCom = "SELECT MAX(numero)
                FROM gf_comprobante_pptal 
                WHERE tipocomprobante = $tipComPal AND parametrizacionanno = $anno";
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
    ##########################################################################
    ##TRASLADO PRESUPUESTAL MODIFICAR
    case 26:
        $tipComPal = $_POST['tipComPal'];
       $fecha = fechaC($_POST['fecha']);
        $num = $_POST['num'];
        
        $numM = $num-1;
        $numMa = $num+1;
        #####BUSCAR LA FECHA DEL NUMERO MENOR##
        $fnm = "SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numM AND tipocomprobante =$tipComPal AND parametrizacionanno = $anno";
        $fnm = $mysqli->query($fnm);
        #####BUSCAR LA FECHA DEL NUMERO MAYOR##
        $fnma ="SELECT fecha FROM gf_comprobante_pptal WHERE "
                . "numero = $numMa AND tipocomprobante =$tipComPal  AND parametrizacionanno = $anno";
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
        } else {
            $nuevaFecha =1;
        }
        echo $nuevaFecha;
    break;
    
}
