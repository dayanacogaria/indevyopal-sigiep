<?php
##########MODIFICACIONES############# 
#07/02/2019 |Erica G. |Arreegló cálculo de los consecutivos
#17/05/2017 |ERICA G. |VALIDACION DE RETENCION POR EGRESO O POR CUENTA POR PAGAAR
#16/05/2017 |ERICA G. |Case 24 buscar si el tipo de comprobante tiene retencion 
#22-02-2017 |Erica G. |Case 19 y 20: para combos seguimiento a disponibilidad
#22-02-2017 |Erica G. |Case 18: para los numeros de la cuenta por pagar
#20-02-2017 |Erica G. |Case 17: para los numeros del egreso
#01/02/2017 |ERICA G. //Agregado Case 10
#####################################
require_once '../Conexion/conexion.php';
session_start();
$case = $_POST['case'];
$anno = $_SESSION['anno'];
switch ($case){
    case 2:
        $documento = $_POST['documento'];
        $sql="SELECT id_unico, nombre, es_obligatorio, consecutivo_unico, formato "
        . "FROM gf_tipo_documento WHERE id_unico ='$documento'";
        $sql= $mysqli->query($sql);
        $datos = mysqli_fetch_row($sql);
        
        echo json_encode($datos);
    break;
    case 3:
        
        $sql="SELECT MAX(numero_documento) FROM gg_documento_proceso ";
        $sql= $mysqli->query($sql);
        $datos = mysqli_fetch_row($sql);
        $datos = $datos[0];
        echo json_encode($datos);
    break;
    case 4:
        
        $sql="SELECT MAX(numero_documento) FROM gg_documento_detalle_proceso ";
        $sql= $mysqli->query($sql);
        $datos = mysqli_fetch_row($sql);
        $datos = $datos[0];
        echo json_encode($datos);
    break;
    case 5:
        
        $id = $_POST['id'];
        $proceso = $_POST['proceso'];
        $sql = "SELECT DISTINCT "
        . "CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos) AS NOMBRE ,"
        . "t.id_unico, t.numeroidentificacion "
        . "FROM gg_gestion_responsable gt "
        . "LEFT JOIN gf_tercero t ON t.id_unico = gt.tercero_uno OR t.id_unico = gt.tercero_dos "
        . "WHERE NOT EXISTS(SELECT * FROM gg_persona_proceso "
                . "WHERE tercero = t.id_unico AND proceso ='$proceso' AND tercero !='$id') "
        . "ORDER BY NOMBRE ASC";
        $result = $mysqli->query($sql); 
        $fila = mysqli_num_rows($result);
        if(!empty($fila)){
            while($fila = mysqli_fetch_row($result)){
                echo '<option value="'.$fila[1].'">'.ucwords(mb_strtolower($fila[0].'('.$fila[2])).')'. '</option>';
            }
        }else{
            echo '<option value="">Tercero</option>';
        }
    break;
    case 6:
        if(!empty($_POST['id'])){
        $id = $_POST['id'];
        $periodo = $_POST['periodo'];
        #BUSCAR FECHA INICIAL PERIODO A REGISTRAR
        $per = "SELECT fecha_inicial FROM gp_periodo WHERE id_unico ='$periodo'";
        $per = $mysqli->query($per);
        $per = mysqli_fetch_row($per);
        $fecha = $per[0];
        $sql="SELECT MAX(valor) "
                . "FROM gp_lectura l "
                . "LEFT JOIN gp_periodo p ON l.periodo = p.id_unico "
                . "WHERE l.unidad_vivienda_medidor_servicio='$id' AND p.fecha_final<'$fecha' ";
        $sql= $mysqli->query($sql);
        $datos = mysqli_fetch_row($sql);
        $datos = $datos[0];
        } else {
            $datos ='';
        }
        echo json_encode($datos);
    break;
    case 7:
        
        $id = $_POST['id'];
        $sql = "SELECT DISTINCT "
        . "p.id_unico, p.nombre FROM gp_periodo p "
        . "WHERE NOT EXISTS(SELECT * FROM gp_lectura l "
                . "WHERE l.periodo = p.id_unico AND unidad_vivienda_medidor_servicio ='$id') "
        . "ORDER BY p.nombre ASC";
        $result = $mysqli->query($sql); 
        $fila = mysqli_num_rows($result);
        if(!empty($fila)){
            echo '<option value="">Periodo</option>';
            while($fila = mysqli_fetch_row($result)){
                echo '<option value="'.$fila[0].'">'.ucwords(mb_strtolower($fila[1])). '</option>';
            }
        }else{
            echo '<option value="">Periodo</option>';
        }
    break;
    case 8:
        
        if(!empty($_POST['id'])){
        $id = $_POST['id'];
        $idcom = $_POST['idcom'];
        $sql="SELECT MAX(valor) FROM gp_lectura WHERE unidad_vivienda_medidor_servicio='$id' AND id_unico != '$idcom'";
        $sql= $mysqli->query($sql);
        $datos = mysqli_fetch_row($sql);
        $datos = $datos[0];
        } else {
            $datos ='';
        }
        echo json_encode($datos);
        
    break;
    case 9:
            $periodo=$_POST['periodo'];
            $_SESSION['periodo'] = $periodo;
   break;
    case 10:
            if(!empty($_SESSION['periodo'])){
            $_SESSION['periodo']  = "";
        }  
   break;
   case 11:
        if(!empty($_POST['referencia'])){
        $id = $_POST['referencia'];
        $idcom = mb_strtolower($id);
        $sql="SELECT uvms.id_unico FROM gp_unidad_vivienda_medidor_servicio uvms "
                . "LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico "
                . "WHERE LOWER(m.referencia)= '$idcom'";
        $sql= $mysqli->query($sql);
        $datos = mysqli_fetch_row($sql);
        $datos = $datos[0];
        } else {
            $datos ='';
        }
        echo json_encode($datos);
   break;
   case 12:       
       $id = $_POST['id'];
       echo $_SESSION['idComprobanteP']  = $id; 
   break;
    case 13:
        
        $sql="SELECT MAX(numero) FROM gf_detalle_comprobante_mov ";
        $sql= $mysqli->query($sql);
        $datos = mysqli_fetch_row($sql);
        $datos = $datos[0];
        echo json_encode($datos);
    break;
    case 14:       
       $id = $_POST['id'];
        $_SESSION['tipoEquivalencia']  = $id; 
    break;
    case 15:       
        $iduvms = $_POST['iduvms'];
        $periodo= $_POST['periodo'];
        $sql="SELECT * FROM gp_lectura WHERE unidad_vivienda_medidor_servicio='$iduvms' AND periodo='$periodo'";
        $sql=$mysqli->query($sql);
        if(mysqli_num_rows($sql)>0){
            $resultado ='1';
        }else {
            $resultado ='0';
        }
        echo json_encode($resultado);
    break;
    
    case 16:
        if(!empty($_POST['referencia'])){
        $codi = $_POST['referencia'];
        $div = explode(" - ", $codi);
        $id= $div[0];
        $idcom = mb_strtolower($id);
        $sql="SELECT id_unico FROM gf_cuenta  "
                . "WHERE codi_cuenta= '$idcom'";
        $sql= $mysqli->query($sql);
        $datos = mysqli_fetch_row($sql);
        $datos = $datos[0];
        $_SESSION['cuenta']=$datos;
        } else {
            $datos ='';
            $_SESSION['cuenta']="";
        }
        echo json_encode($datos);
   break;
    case 17:
        $tipocomprobante = $_POST['tipocomprobante'];
        $id_tip_comp = $tipocomprobante;
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
                AND parametrizacionanno ='.$parametroAnno ;
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
        echo json_encode($numero);
    break;
    case 18:
        $tipocomprobante = $_POST['tipo'];
        $id_tip_comp = $tipocomprobante;
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
                AND parametrizacionanno ='.$parametroAnno;
        $numComp = $mysqli->query($queryNumComp);

        $row = mysqli_fetch_row($numComp);
        if($row[0] == 0)
        {
                $numero =$numero. '000001';
        }
        else
        {
                $numero = $row[0] + 1;
        }
        echo json_encode($numero);
    break;
    case 19:
        $tipo = $_POST['tipo'];
         $sql = "SELECT
                cp.id_unico,
                cp.numero numero,
                tc.codigo,
                cp.fecha,
                IF(CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos) 
                    IS NULL OR CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos) = '',
                  (ter.razonsocial),
                  CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos)) AS NOMBRE,
                (SELECT SUM(dc.valor)
                FROM
                  gf_detalle_comprobante_pptal dc
                WHERE
                  cp.id_unico = dc.comprobantepptal) AS valor
              FROM
                gf_comprobante_pptal cp
              LEFT JOIN
                gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
              LEFT JOIN
                gf_tercero ter ON cp.tercero = ter.id_unico
              WHERE
                tc.id_unico  = '$tipo' 
                AND cp.parametrizacionanno = $anno 
              ORDER BY
                cp.numero ASC";
        //echo $sql;
        $result = $mysqli->query($sql); 
        $fila = mysqli_num_rows($result);
        if(!empty($fila)){
            echo '<option value="">Disponibilidad Inicial</option>';
            while($row = mysqli_fetch_row($result)){
                $source = $row[3];
                $date = new DateTime($source);
                $fecha= $date->format('d/m/Y');
                echo '<option value="'. $row[1].'">'.$row[1].' '.mb_strtoupper($row[2]).' '.ucwords(mb_strtolower($row[4].' '.$fecha.' $'.number_format($row[5]))).'</option>';
                
            }
        }else{
           
        }
    break;
    case 20:
        $tipo = $_POST['tipo'];
        $sql = "SELECT
                cp.id_unico,
                cp.numero numero,
                tc.codigo,
                cp.fecha,
                IF(CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos) 
                    IS NULL OR CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos) = '',
                  (ter.razonsocial),
                  CONCAT_WS(' ',
                    ter.nombreuno,
                    ter.nombredos,
                    ter.apellidouno,
                    ter.apellidodos)) AS NOMBRE,
                (SELECT SUM(dc.valor)
                FROM
                  gf_detalle_comprobante_pptal dc
                WHERE
                  cp.id_unico = dc.comprobantepptal) AS valor
              FROM
                gf_comprobante_pptal cp
              LEFT JOIN
                gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
              LEFT JOIN
                gf_tercero ter ON cp.tercero = ter.id_unico
              WHERE
                tc.id_unico = '$tipo' 
                AND cp.parametrizacionanno = $anno 
              ORDER BY
                cp.numero DESC";
        $result = $mysqli->query($sql); 
        $fila = mysqli_num_rows($result);
        if(!empty($fila)){
            echo '<option value="">Disponibilidad Inicial</option>';
            while($row = mysqli_fetch_row($result)){
                $source = $row[3];
                $date = new DateTime($source);
                $fecha= $date->format('d/m/Y');
                echo '<option value="'. $row[1].'">'.$row[1].' '.mb_strtoupper($row[2]).' '.ucwords(mb_strtolower($row[4].' '.$fecha.' $'.number_format($row[5]))).'</option>';
                
            }
        } else {
           echo '<option value>Disponibilidad Final</option>';
        }
    break;
    ##########BUSQUEDA SI EL COMPROBANTE DE INGRESO ESTA BALANCEADO O NO###############
    case 21:
        $id = $_POST['id'];
        $comp="SELECT DISTINCT dtc.id_unico, 
                            cnt.naturaleza, 
                            dtc.valor 
                            FROM gf_detalle_comprobante dtc 
            LEFT JOIN gf_cuenta cnt ON dtc.cuenta = cnt.id_unico 
            WHERE dtc.comprobante = $id ";
        $comp = $mysqli->query($comp);
        if(mysqli_num_rows($comp)>0){
            $sumar=0;
            $sumaT=0;
            $diferencia=0;
            while($row = mysqli_fetch_row($comp)) {
                ##########DEBITOS###########
                if($row[1] == 1) {
                    if($row[2] >= 0){
                        $sumar += $row[2];
                    }
                }else if($row[1] == 2){
                    if($row[2] <= 0){
                        $x = (float) substr($row[2],'1');
                        $sumar += $x;
                    }
                }
                #########CREDITOS##############
                if ($row[1] == 2) {
                    if($row[2] >= 0){
                        $sumaT += $row[2];
                    }
                }else if($row[1] == 1){
                    if($row[2] <= 0){
                        $x = (float) substr($row[2],'1');
                        $sumaT += $x;
                    }
                }
                #########DIFERENCIA##########
                    $valorD = $sumar;
                    $valorC = $sumaT;
                    #Diferencia
                    $diferencia = $valorC - $valorD;
                    $w = 0;
                    if($diferencia<0){
                      $w=substr($diferencia,1);
                    }else{
                      $w=$diferencia;
                    }
            }
            $diferencia = ROUND($diferencia,2);
            if($diferencia != '0' || $diferencia !='-0' || $diferencia != "") {
               $result=1; 
            } else {
                $result=0;
            }
        } else {
            $result=0;
        }
    echo $result;
    break;
    ########################################################################
    case 22:
        $_SESSION['rubro']="";
        $concepto = $_POST['concepto'];
        $sql = "SELECT ft.id_unico, CONCAT(rb.codi_presupuesto,' - ',rb.nombre) 
                    FROM gf_concepto_rubro cr 
                    LEFT JOIN gf_rubro_fuente rft ON cr.rubro = rft.rubro
                    LEFT JOIN gf_rubro_pptal rb ON cr.rubro = rb.id_unico
                    LEFT JOIN  gf_fuente ft ON rft.fuente = ft.id_unico 
                    WHERE cr.concepto = $concepto AND rb.id_unico IS NOT NULL ORDER BY codi_presupuesto ASC";
            $result = $mysqli->query($sql); 
         if(mysqli_num_rows($result)>0){
             $f = mysqli_fetch_row($result);
             if(empty($f[0]) || $f[0]=="" || $f[0]=='NULL'){
                 $r= 0;
             } else {
                $r= 1;
             }
             $_SESSION['rubro']= ucwords(mb_strtolower($f[1]));
         } else {
             $r=0;
         }
         
         echo $r;
    break;
    
    //GENERAR INFORME BUSCAR PERIODICIDAD
    
    case 23:
        $informe = $_POST['informe'];
        $p = "SELECT MAX(tbh.periodicidad) FROM gn_tabla_homologable tbh WHERE tbh.informe = $informe";
        $p = $mysqli->query($p);
        $p = mysqli_fetch_row($p);
        
        $_SESSION['periodicidad']=$p[0];
        echo $p[0];
    break;
    #####MIRAR SI EL TIPO DE COMPROBANTE TIENE RETENCION###
    case 24:
        ##RECIBE TIPO PPTAL
        $tipo =$_POST['tipo'];
        $tp="SELECT
                tc.retencion
              FROM
                gf_tipo_comprobante tc
              LEFT JOIN
                gf_tipo_comprobante_pptal tcp ON tcp.id_unico = tc.comprobante_pptal
              WHERE
                tcp.id_unico= $tipo";
        $tp =$mysqli->query($tp);
        $tp = mysqli_fetch_row($tp);
        echo $tp[0]; 
    break;
    
}
?>