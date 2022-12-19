<?php require '../Conexion/ConexionPDO.php';                                                  
require '../Conexion/conexion.php';                    
require '../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
$fechaa     = date('Y-m-d');
$calendario = CAL_GREGORIAN;
##******** Buscar Centro De Costo ********#
$cc         = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $panno");
$c_costo    = $cc[0][0];
$pro        = $con->Listar("SELECT * FROM gf_proyecto WHERE nombre='Varios' AND compania = $compania");
$proyecto   = $pro[0][0]; 
switch ($action){
    #* Crear Concepto
    case 1:
        $tipo       = $_REQUEST['tipo'];
        $numero     = $_REQUEST['numero'];
        $nombre     = $_REQUEST['nombre'];
        $sql_cons ="INSERT INTO `gn_concepto_certificado` 
            ( `tipo`,`numero`,
            `nombre`,`compania` ) 
        VALUES (:tipo, :numero, 
            :nombre, :compania)";
        $sql_dato = array(
            array(":tipo",$tipo),
            array(":numero",$numero),
            array(":nombre",$nombre),
            array(":compania",$compania),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #* Modificar Concepto
    case 2: 
        $tipo       = $_REQUEST['tipo'];
        $numero     = $_REQUEST['numero'];
        $nombre     = $_REQUEST['nombre'];
        $id         = $_REQUEST['id'];
        $sql_cons ="UPDATE `gn_concepto_certificado`  
            SET `tipo`=:tipo, 
            `numero`=:numero, 
            `nombre`=:nombre 
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":tipo",$tipo),
            array(":numero",$numero),
            array(":nombre",$nombre),
            array(":id_unico",$id),   
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #* Eliminar Concepto
    case 3:
        $id         = $_REQUEST['id'];    
        $sql_cons  = "DELETE FROM `gn_concepto_certificado` 
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),	
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    
    #* Registrar Configuración Concepto
    case 4:
        $concepto = $_REQUEST['concepto'];    
        $idc      = $_REQUEST['id'];    
        $idsc     = '0';
        for ($i= 0; $i < count($concepto); $i++) {
            $sql_cons ="INSERT INTO `gn_configuracion_certificado` 
                ( `concepto_certificado`,`concepto_nomina`,
                `parametrizacionanno`) 
            VALUES (:concepto_certificado, :concepto_nomina, 
                :parametrizacionanno)";
            $sql_dato = array(
                array(":concepto_certificado",$idc),
                array(":concepto_nomina",$concepto[$i]),
                array(":parametrizacionanno",$panno),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #* Eliminar Configuracion
    case 5:
        $id         = $_REQUEST['id'];    
        $sql_cons  = "DELETE FROM `gn_configuracion_certificado` 
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),	
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
}