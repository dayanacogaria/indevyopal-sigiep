<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#03/05/2018 | Erica G. | Formato 1001 Acumulado o Detallado
#16/04/2018 | Erica G. | Archivo Creado
####/################################################################################
require '../Conexion/ConexionPDO.php';                                                     
require '../Conexion/conexion.php';                                                     
require './funcionesPptal.php';
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
switch ($action) {
    #   ********    Guardar Formatos Exógenas   ********    #   
    case 1:
        $formato = $_REQUEST['formato'];
        $nombre  = $_REQUEST['nombre'];
        $cuantia = $_REQUEST['cuantia'];
        
        $sql_cons ="INSERT INTO `gf_formatos_exogenas` 
        ( `formato`,  `nombre`, `cuantia`,`parametrizacionanno` ) 
        VALUES (:formato, :nombre, :cuantia, :parametrizacionanno)";
        $sql_dato = array(
                array(":formato",$formato),
                array(":nombre",$nombre),
                array(":cuantia",$cuantia),
                array(":parametrizacionanno",$panno),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
            var_dump($obj_resp);
        }
    break;
    #   ********    Modificar Formatos Exógenas   ********    #   
    case 2:
        $id      = $_REQUEST['id'];
        $formato = $_REQUEST['formato'];
        $nombre  = $_REQUEST['nombre'];
        $cuantia = $_REQUEST['cuantia'];
        
        $sql_cons ="UPDATE  `gf_formatos_exogenas` 
        SET `formato`=:formato,  
        `nombre`=:nombre, 
        `cuantia`=:cuantia,
        `parametrizacionanno` = :parametrizacionanno 
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":formato",$formato),
                array(":nombre",$nombre),
                array(":cuantia",$cuantia),
                array(":parametrizacionanno",$panno),
                array(":id_unico",$id),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
            var_dump($obj_resp);
        }
    break;
    #   ********    Eliminar Formatos Exógenas   ********    #   
    case 3:
        $id      = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gf_formatos_exogenas` 
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
            var_dump($obj_resp);
        }
    break;
    #   ********    Guardar Conceptos Exógenas Cod,N  ********    #   
    case 4:
        $formato = $_REQUEST['formato'];
        $codigo  = $_REQUEST['codigo'];
        $nombre  = $_REQUEST['nombre']; 
        
        $sql_cons ="INSERT INTO `gf_concepto_exogenas` 
        ( `formato`,  `nombre`, `codigo`) 
        VALUES (:formato, :nombre, :codigo)";
        $sql_dato = array(
                array(":formato",$formato),
                array(":nombre",$nombre),
                array(":codigo",$codigo),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
            var_dump($obj_resp);
        }
    break;
    #   ********    Guardar Conceptos Exógenas Archivo ********    #   
    case 5:
        require '../ExcelR/Classes/PHPExcel/IOFactory.php';                                     
        $formato        = $_REQUEST['formato'];
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas    = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        $guardados      = 0;
        for ($a = 0; $a <= $total_filas; $a++) {
            $codigo    = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $nombre    = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $sql_cons ="INSERT INTO `gf_concepto_exogenas` 
            ( `formato`,  `nombre`, `codigo`) 
            VALUES (:formato, :nombre, :codigo)";
            $sql_dato = array(
                    array(":formato",$formato),
                    array(":nombre",$nombre),
                    array(":codigo",$codigo),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
               $guardados +=1;
            } else {
                
            }
        }
        echo $guardados;
    break;
    #   ********    Eliminar Conceptos Exógenas   ********    #   
    case 6:
        $id      = $_REQUEST['id'];
        $sql_cons ="DELETE FROM `gf_concepto_exogenas` 
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
            var_dump($obj_resp);
        }
    break;
    #   ********    Modificar Conceptos Exógenas   ********    #   
    case 7:
        $id      = $_REQUEST['idm'];
        $nombre  = $_REQUEST['nombrem'];
        $codigo  = $_REQUEST['codigom'];
        
        $sql_cons ="UPDATE  `gf_concepto_exogenas` 
        SET  
        `nombre`=:nombre, 
        `codigo`=:codigo  
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":nombre",$nombre),
                array(":codigo",$codigo), 
                array(":id_unico",$id),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
            var_dump($obj_resp);
        }
    break;
    #   ********    Modificar Configuración Exógenas   ********    #   
    case 8:
        $id         = $_REQUEST['id'];
        $conc       = $_REQUEST['select'];
        $div        = explode(",", $conc);
        $cuenta     = trim($div[0]);
        $concepto   = trim($div[1]);
        
        $sql_cons ="UPDATE  `gf_configuracion_exogenas` 
        SET  
        `cuenta`=:cuenta, 
        `concepto_exogenas`=:concepto_exogenas  
        WHERE id_unico = :id_unico";
        $sql_dato = array(
                array(":cuenta",$cuenta),
                array(":concepto_exogenas",$concepto), 
                array(":id_unico",$id),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            echo 1;
        } else {
            echo 2;
            var_dump($obj_resp);
        }
    break;
    #   ********    Guardar Modificar Eliminar Configuración Exógenas   ********    #   
    case 9:
        $formato    = $_REQUEST['formato'];
        $concepto   = $_REQUEST['valor'];
        $cuenta     = $_REQUEST['cuenta'];
        
        #Buscar Si Existe Cuenta Y Concepto De Ese Formato 
        $conf    = $con->Listar("SELECT cf.id_unico, cn.codigo, cn.nombre, cf.concepto_exogenas 
            FROM gf_configuracion_exogenas cf 
            LEFT JOIN gf_concepto_exogenas cn ON cf.concepto_exogenas = cn.id_unico 
            WHERE cf.cuenta = $cuenta AND cn.formato= $formato");
        if($conf>0){
            $id = $conf[0][0];
            if(empty($concepto)){
                $sql_cons ="DELETE FROM `gf_configuracion_exogenas` 
                WHERE id_unico = :id_unico";
                $sql_dato = array(
                        array(":id_unico",$id),
                );
            } else {
                $sql_cons ="UPDATE  `gf_configuracion_exogenas` 
                SET  
                `cuenta`=:cuenta, 
                `concepto_exogenas`=:concepto_exogenas  
                WHERE id_unico = :id_unico";
                $sql_dato = array(
                        array(":cuenta",$cuenta),
                        array(":concepto_exogenas",$concepto), 
                        array(":id_unico",$id),
                );
            }
        } else {
            $sql_cons ="INSERT INTO `gf_configuracion_exogenas` 
                ( `cuenta`,  `concepto_exogenas`) 
                VALUES (:cuenta, :concepto_exogenas)";
            $sql_dato = array(
                    array(":cuenta",$cuenta),
                    array(":concepto_exogenas",$concepto),
            );
            
        }
        
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($obj_resp)){
            #Buscar Si Existe Cuenta Y Concepto De Ese Formato 
            $conf    = $con->Listar("SELECT cf.concepto_exogenas 
                FROM gf_configuracion_exogenas cf 
                LEFT JOIN gf_concepto_exogenas cn ON cf.concepto_exogenas = cn.id_unico 
                WHERE cf.cuenta = $cuenta AND cn.formato= $formato");
            echo $conf[0][0];
        } else {
            echo 0;
            var_dump($obj_resp);
        }
    break;
    #       **********  Select Formatos Año     **********   #
    case 10:
        $anno = $_REQUEST['anno'];
        $fm   = $con->Listar("SELECT * FROM gf_formatos_exogenas WHERE parametrizacionanno =$anno ORDER BY formato ASC");
        if(count($fm)>0){
            for ($i = 0; $i < count($fm); $i++) {
                echo '<option value="'.$fm[$i][0].'">'.$fm[$i][1].' - '.$fm[$i][2].'</option>';
            } 
        } else {
            echo '<option value="">No Existen Formatos</option>';
        }
    break; 
    #*** Codigo Formato ***#
    case 11:
        $id = $_POST['id'];
        $id_f = "";
        $fm   = $con->Listar("SELECT formato FROM gf_formatos_exogenas WHERE id_unico =$id");
        if(count($fm)>0){
        $id_f = $fm[0][0];
        }
        echo $id_f;
        
    break;

}
