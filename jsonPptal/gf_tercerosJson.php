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
$action     = $_REQUEST['action'];
switch ($action) {
    #   ********    Actualización Digitos de Verificación   ********    #
    case 1:
        $arrayt = array();
        #   Buscar Terceros Perfil Jurídico    #
        $rowt = $con->Listar("SELECT DISTINCT t.id_unico 
                FROM  
                    gf_tercero t 
                LEFT JOIN 
                    gf_perfil_tercero pt ON t.id_unico = pt.tercero 
                WHERE 
                    pt.perfil IN (1,4,6,8,9,11,12) AND compania = $compania 
                ORDER BY t.id_unico");
        for ($i = 0; $i < count($rowt); $i++) {
            #   Buscar que no tenga perfil Natural 
            $rown = $con->Listar("SELECT * 
                FROM 
                    gf_perfil_tercero 
                WHERE tercero = ".$rowt[$i][0]." 
                AND perfil IN (2,3,5,7,10)");
            if(count($rown)==0){
                # *** Buscar Que No Tenga Nombres Ni Apellidos  *** #
                $rowna = $con->Listar("SELECT IF(CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos) 
                    IS NULL OR CONCAT_WS(' ',
                    tr.nombreuno,
                    tr.nombredos,
                    tr.apellidouno,
                    tr.apellidodos) = '',1,2) 
                FROM gf_tercero tr 
                WHERE tr.id_unico=".$rowt[$i][0]);
                if($rowna[0][0]==1){
                    if(in_array($rowt[$i][0], $arrayt)) {
                    } else {
                        array_push ( $arrayt , $rowt[$i][0] );
                    }
                }
            } 
        }
        #   ** Actualizar Digitos Terceros Válidos  **  #
        $terceros_c = implode(",", $arrayt);
        $rowt = $con->Listar("SELECT 
                    DISTINCT t.id_unico, t.numeroidentificacion 
                FROM 
                    gf_tercero t 
                WHERE 
                    t.id_unico IN ($terceros_c)
                ORDER BY t.id_unico");
        #** Actualizados **#
        $a =0;
        for ($i = 0; $i < count($rowt); $i++) {
            $dv         ="";
            $id_t       = $rowt[$i][0];
            $num_i      = $rowt[$i][1];
            $arreglo    = array(16); 
            $x          = 0; 
            $y          = 0; 
            $z          = strlen($num_i);
            
            $arreglo[1]=3;   $arreglo[2]=7;   $arreglo[3]=13; 
            $arreglo[4]=17;  $arreglo[5]=19;  $arreglo[6]=23;
            $arreglo[7]=29;  $arreglo[8]=37;  $arreglo[9]=41;
            $arreglo[10]=43; $arreglo[11]=47; $arreglo[12]=53;  
            $arreglo[13]=59; $arreglo[14]=67; $arreglo[15]=71;
            for($j=0 ; $j<$z ; $j++) { 
                $y  =(substr($num_i,$j,1)) ;
                $x +=($y*$arreglo[$z-$j]);
            } 
            $y=$x % 11;
            if($y>1){
                $dv = 11-$y;
            } else {
                $dv = $y;
            }
            #   ****    Actualizar  ****    #
            $sql_cons ="UPDATE  `gf_tercero` 
            SET `digitoverficacion`=:digitoverficacion 
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                    array(":digitoverficacion",$dv),
                    array(":id_unico",$id_t),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                $a +=1;
            } else {
            }
        }
        $msj = "Terceros Modificados: $a";
        echo $msj;   
    break;
    #   *******     Verificar Si El Tercero Ya Existe Y Que Perfil Tiene ******* # 
    case 2:
        $numI   = $_REQUEST['numI'];
        $perfil = $_REQUEST['perfil'];
        $msj    = "";
        $rta    = 0;
        $id     = "";
        #*** Buscar Si Tercero Existe ***#
        $et = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion = $numI AND compania = $compania");
        #var_dump(count($et));
        if(count($et)>0){
            $id = $et[0][0];
            # *** Verificar Perfil *** #
            if($perfil==1 || $perfil==4 || $perfil==6 || $perfil==8 || $perfil==9 || $perfil==11 || $perfil==12){
                #   Buscar Tercero En Perfil Natural    #
                $rowt = $con->Listar("SELECT DISTINCT t.* 
                    FROM  
                        gf_tercero t 
                    LEFT JOIN 
                        gf_perfil_tercero pt ON t.id_unico = pt.tercero 
                    WHERE 
                        pt.perfil IN (2,3,5,7,10) AND compania = $compania 
                        AND t.id_unico = ".$et[0][0]." 
                    ORDER BY t.id_unico");
                if(count($rowt)>0){
                    $rta =1;
                }
            } elseif($perfil==2 || $perfil==3 || $perfil==5 || $perfil==7 || $perfil==10){
                #   Buscar Tercero En Perfil Jurídico    #
                $rowt = $con->Listar("SELECT DISTINCT t.* 
                    FROM  
                        gf_tercero t 
                    LEFT JOIN 
                        gf_perfil_tercero pt ON t.id_unico = pt.tercero 
                    WHERE 
                        pt.perfil IN (1,4,6,8,9,11,12) AND compania = $compania 
                        AND t.id_unico = ".$et[0][0]." 
                    ORDER BY t.id_unico");
                if(count($rowt)>0){
                    $rta =1;
                }
            }
        }
        $datos = array("id"=>$id,"rta"=>$rta);
        echo json_encode($datos); 
    break;
    #*** Eliminar Terceros ***#
    case 3:
        $id     = $_REQUEST['id'];
        $perfil = $_REQUEST['perfil'];
        $rta    = 0;
        #*** Eliminar Perfil Tercero ****# 
        $sql_cons ="DELETE FROM `gf_perfil_tercero` 
        WHERE `perfil` =:perfil AND `tercero` =:tercero";
        $sql_dato = array(
                array(":perfil",$perfil),
                array(":tercero",$id),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            #*** Eliminar Tercero ****#
            $sql_cons ="DELETE FROM `gf_tercero` 
            WHERE `id_unico` =:id_unico";
            $sql_dato = array(
                    array(":id_unico",$id),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                $rta    = 1;
            } else {
                $rta    = 2;
            }
        }
        echo $rta;            
    break;
    #*** Autocomplementado Terceros ***#
    case 4:
        $referencia =  $_GET['term'];
        $query = "SELECT IF(CONCAT_WS(' ',
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
                t.numeroidentificacion, 
           CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
        FROM gf_tercero t 
        WHERE t.numeroidentificacion LIKE '%$referencia%'";
        $result = $mysqli->query($query);
        $data = array();
        if($result->num_rows > 0){
            while ($row = $result->fetch_row()){
                $data[] = $row[1].' - '.$row[2];
            }
            echo json_encode($data);
        }
    break;
    
    #** Guardar Tercero Cliente Natural Modal ***#
    case 5:
        $tipoI      = $_POST['sltTipoIdent'];
        $numId      = $_POST['txtNumeroI'];
        $primerN    = $_POST['txtPrimerNombre'];
        $primerA    = $_POST['txtPrimerApellido'];
        $compania   = $_SESSION['compania'];

        if(empty($_POST['txtSegundoNombre'])){
            $segundoN = 'NULL';
        } else {
            $segundoN   ="'".$_POST['txtSegundoNombre']."'";
        }
        if(empty($_POST['txtSegundoApellido'])){
            $segundoA = 'NULL';
        } else {
            $segundoA   = "'".$_POST['txtSegundoApellido']."'";
        }
        $insertSQL = "INSERT INTO gf_tercero (TipoIdentificacion, 
            NumeroIdentificacion, NombreUno, NombreDos, ApellidoUno, 
            ApellidoDos, compania) 
            VALUES( $tipoI, '$numId',  '$primerN', $segundoN, 
            '$primerA', $segundoA, $compania)";
        $rs = $mysqli->query($insertSQL);
        if($rs == true)
        {
            $max = "SELECT MAX(Id_unico) Id_unico FROM gf_tercero WHERE numeroidentificacion = $numId";
            $id_M = $mysqli->query($max);
            $row = mysqli_fetch_row($id_M);
            $sqlP = "INSERT INTO gf_perfil_tercero(Perfil,Tercero) VALUES (3,$row[0])";
            $rs = $mysqli->query($sqlP);
            echo 1;
        } else {
            echo 0;
        }
    break;
    
    #* TERCERO EMPRESA HOTEL
    case 6:
        $nt = $_REQUEST['num'];
        $dt = $con->Listar("SELECT t.id_unico, t.razonsocial, ti.id_unico, ti.nombre, 
            t.numeroidentificacion, t.digitoverficacion, 
            d.direccion, dp.id_unico, dp.nombre, c.id_unico, c.nombre, 
            tl.valor, t.email 
            FROM gf_tercero t 
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
            LEFT JOIN gf_direccion d ON t.id_unico = d.tercero AND d.direccion !=''
            LEFT JOIN gf_ciudad c ON d.ciudad_direccion = c.id_unico 
            LEFT JOIN gf_departamento dp ON c.departamento = dp.id_unico 
            LEFT JOIN gf_telefono tl ON t.id_unico = tl.tercero AND tl.valor !=''
            WHERE numeroidentificacion ='$nt' and compania = $compania 
            ORDER BY d.id_unico , tl.id_unico LIMIT 1    ");
        $datos = array("id"=>$dt[0][0],
            "razonsocial"=>$dt[0][1],
            "idt"=>$dt[0][2],
            "tid"=>$dt[0][3],
            "nmi"=>$dt[0][4],
            "div"=>$dt[0][5],
            "dir"=>$dt[0][6],
            "did"=>$dt[0][7],
            "dnb"=>$dt[0][8],
            "cid"=>$dt[0][9],
            "cnm"=>$dt[0][10],
            "tel"=>$dt[0][11],
            "emi"=>$dt[0][12]);
        echo json_encode($datos); 
    break;
    #* TERCERO HUESPED HOTEL
    case 7:
        $nt = $_REQUEST['num'];
        $dt = $con->Listar("SELECT t.id_unico, t.razonsocial, ti.id_unico, ti.nombre, 
            t.numeroidentificacion, t.digitoverficacion, 
            d.direccion, dp.id_unico, dp.nombre, c.id_unico, c.nombre, 
            tl.valor, t.email, t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos,
            DATE_FORMAT(t.fecha_nacimiento,'%d/%m/%Y'), ctr.id_unico, dptr.id_unico , tr.id_unico , 
            ctr.nombre, dptr.nombre
            FROM gf_tercero t 
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
            LEFT JOIN gf_direccion d ON t.id_unico = d.tercero AND d.direccion !=''
            LEFT JOIN gf_ciudad c ON d.ciudad_direccion = c.id_unico 
            LEFT JOIN gf_departamento dp ON c.departamento = dp.id_unico 
            LEFT JOIN gf_telefono tl ON t.id_unico = tl.tercero AND tl.valor !=''
            LEFT JOIN gf_ciudad ctr ON ctr.id_unico = t.ciudadidentificacion
            LEFT JOIN gf_departamento dptr ON ctr.departamento = dptr.id_unico 
            LEFT JOIN gf_tercero  tr ON t.representantelegal = tr.id_unico
            WHERE t.numeroidentificacion  ='$nt' and t.compania = $compania
            ORDER BY d.id_unico , tl.id_unico LIMIT 1   ");
        $datos = array("id"=>$dt[0][0],
            "razonsocial"=>$dt[0][1],
            "idt"=>$dt[0][2],
            "tid"=>$dt[0][3],
            "nmi"=>$dt[0][4],
            "div"=>$dt[0][5],
            "dir"=>$dt[0][6],
            "did"=>$dt[0][7],
            "dnb"=>$dt[0][8],
            "cid"=>$dt[0][9],
            "cnm"=>$dt[0][10],
            "tel"=>$dt[0][11],
            "emi"=>$dt[0][12],
            
            "nun"=>$dt[0][13],
            "nds"=>$dt[0][14],
            "apu"=>$dt[0][15],
            "apd"=>$dt[0][16],
            "fnc"=>$dt[0][17],
            "cdr"=>$dt[0][18],
            "dpr"=>$dt[0][19],
            "rpl"=>$dt[0][20],
            "ncr"=>$dt[0][21],
            "dcr"=>$dt[0][22]);
                
        echo json_encode($datos); 
    break;

    #**AUTOCOMPLETADO TERCEROS 
    case 8:
        $referencia =  $_REQUEST['term'];
        $query = "SELECT DISTINCT t.id_unico, IF(t.razonsocial IS NULL 
        OR t.razonsocial ='', 
            CONCAT_WS(' ',
            t.nombreuno,
            t.nombredos,
            t.apellidouno,
            t.apellidodos),
            (t.razonsocial)) AS NOMBRE,
           IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                t.numeroidentificacion, 
           CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
        FROM gf_tercero t 
        WHERE t.compania = $compania AND (t.razonsocial LIKE '%$referencia%' 
        OR CONCAT_WS(' ',t.nombreuno,
            t.nombredos,
            t.apellidouno,
            t.apellidodos) LIKE '%$referencia%' 
        OR t.numeroidentificacion LIKE '%$referencia%')
            LIMIT 20";
        $result = $mysqli->query($query);
        $option = '';
        if($result->num_rows > 0){
            while ($row = $result->fetch_row()){
                $option .= '<option value="'.$row[0].'">'.$row[1].' - '.$row[2].'</option>';
            }
        }
        echo $option;
    break;
    #* Cargar Combos Inciales de Terceros
    case 9:
        $option = ''; 
        if(!empty($_POST['id'])){
            $id = $_POST['id'];
            $del ="SELECT t.id_unico, IF(t.razonsocial IS NULL 
                OR t.razonsocial ='', 
                CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos),
                (t.razonsocial)) AS NOMBRE,
                IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                    t.numeroidentificacion, 
                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_tercero t ON dc.tercero = t.id_unico 
            WHERE dc.id_unico = $id";
            $del = $mysqli->query($del);
            $cta = mysqli_fetch_row($del);
            $option .= '<option value="'.$cta[0].'">'.$cta[1].' - '.$cta[2].'</option>';
        }
        $query = "SELECT t.id_unico, IF(t.razonsocial IS NULL 
        OR t.razonsocial ='', 
            CONCAT_WS(' ',
            t.nombreuno,
            t.nombredos,
            t.apellidouno,
            t.apellidodos),
            (t.razonsocial)) AS NOMBRE,
           IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                t.numeroidentificacion, 
           CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
        FROM gf_tercero t 
        WHERE t.compania = $compania 
            LIMIT 20";
        $result = $mysqli->query($query);
        
        if($result->num_rows > 0){
            while ($row = $result->fetch_row()){
                $option .= '<option value="'.$row[0].'">'.$row[1].' - '.$row[2].'</option>';
            }
        }
        echo $option;
    break;
    #Guardar Responsabilidad Tercero
    case 10:
     
        $queryT = "SELECT * FROM gf_tercero_responsabilidad
                      WHERE tercero=".$_POST['tercero'];
             $resultT = $mysqli->query($queryT);
             if($resultT->num_rows > 0){
                $sql_cons ="UPDATE  `gf_tercero_responsabilidad` 
                SET `responsabilidad`=:responsabilidad 
                WHERE tercero = :tercero";
                $sql_dato = array(
                        array(":responsabilidad",$_POST['responsabilidad']),
                        array(":tercero",$_POST['tercero']),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
             }else{
                $sql_cons ="INSERT INTO `gf_tercero_responsabilidad` 
                ( `tercero`, `responsabilidad`) 
                VALUES (:tercero, :responsabilidad)";
                $sql_dato = array(
                    array(":tercero",$_POST['tercero']),
                    array(":responsabilidad",$_POST['responsabilidad']),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
             }
            if(empty($resp)){
                echo 1;
            } else {
                echo 0;
            }
    break;

    #Eliminar Responsabilidad Tercero
    case 11:

        if ($_REQUEST['iden']==1) {
            $sql_cons ="UPDATE  `gf_tercero_responsabilidad` 
            SET `responsabilidad`=:responsabilidad 
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                    array(":responsabilidad",NULL),
                    array(":id_unico",$_REQUEST['id']),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);

        }elseif($_REQUEST['iden']==2){
            $sql_cons ="UPDATE  `gf_tercero_responsabilidad` 
            SET `responsabilidad_tributaria`=:responsabilidad_tributaria 
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                    array(":responsabilidad_tributaria",NULL),
                    array(":id_unico",$_REQUEST['id']),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        
        }
        $queryR = "SELECT responsabilidad,responsabilidad_tributaria,id_unico FROM gf_tercero_responsabilidad
        WHERE id_unico=".$_REQUEST['id'];
        $resultR = $mysqli->query($queryR);
        if($resultR->num_rows > 0){
            while ($rowRe = $resultR->fetch_row()){
                if ($rowRe[0]==null && $rowRe[1]==null){
                    $sql_cons ="DELETE FROM `gf_tercero_responsabilidad` 
                    WHERE `id_unico` =:id_unico";
                    $sql_dato = array(
                            array(":id_unico",$rowRe[2]),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                }
            }
        }
        
        if(empty($resp)){
            echo 1;
        } else {
            echo 0;
        }
    break;

     #**AUTOCOMPLETADO TERCEROS 
    case 12:
        $referencia =  $_REQUEST['term'];
        $query = "SELECT tr.id_unico, IF(CONCAT_WS(' ',
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
                                    tr.numeroidentificacion, tr.id_unico 
        FROM gf_tercero tr 
        WHERE tr.compania = $compania AND (tr.razonsocial LIKE '%$referencia%' 
        OR CONCAT_WS(' ',tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) LIKE '%$referencia%' 
        OR tr.numeroidentificacion LIKE '%$referencia%')
            LIMIT 20";
        $result = $mysqli->query($query);
        $option = '';
        if($result->num_rows > 0){
            while ($row = $result->fetch_row()){
                $option .= '<option value="'.$row[2].'">'.$row[1].' - '.$row[2].'</option>';
            }
        }
        echo $option;
    break;
     case 13:
        $referencia =  $_REQUEST['term'];
        $query = "SELECT tr.id_unico, IF(CONCAT_WS(' ',
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
                                    tr.numeroidentificacion, tr.id_unico 
        FROM gf_tercero tr 
        WHERE tr.compania = $compania AND (tr.razonsocial LIKE '%$referencia%' 
        OR CONCAT_WS(' ',tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) LIKE '%$referencia%' 
        OR tr.numeroidentificacion LIKE '%$referencia%')
            LIMIT 20";
        $result = $mysqli->query($query);
        $option = '';
        if($result->num_rows > 0){
            while ($row = $result->fetch_row()){
                $option .= '<option value="'.$row[2].'">'.$row[1].' - '.$row[2].'</option>';
            }
        }
        echo $option;
    break;
        #Guardar Responsabilidad Tributaria
        case 14:
            $queryT = "SELECT * FROM gf_tercero_responsabilidad
                      WHERE tercero=".$_POST['tercero'];
             $resultT = $mysqli->query($queryT);
             if($resultT->num_rows > 0){
                $sql_cons ="UPDATE  `gf_tercero_responsabilidad` 
                SET `responsabilidad_tributaria`=:responsabilidad_tributaria 
                WHERE tercero = :tercero";
                $sql_dato = array(
                        array(":responsabilidad_tributaria",$_POST['responsabilidad_tribu']),
                        array(":tercero",$_POST['tercero']),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
             }else{
                $sql_cons ="INSERT INTO `gf_tercero_responsabilidad` 
                ( `tercero`, `responsabilidad_tributaria`) 
                VALUES (:tercero, :responsabilidad_tributaria)";
                $sql_dato = array(
                    array(":tercero",$_POST['tercero']),
                    array(":responsabilidad_tributaria",$_POST['responsabilidad_tribu']),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
             }
            if(empty($resp)){
                echo 1;
            } else {
                echo 0;
            }
        break;
}

