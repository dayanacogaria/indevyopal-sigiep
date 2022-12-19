<?php
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';
require './funcionesPptal.php';
require '../funciones/funcionEmail.php';
require '../jsonAlmacen/funcionesAlmacen.php';

@session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$usuario_t  = $_SESSION['usuario_tercero'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
switch ($action) {
    #Guardar Tipo D
    case 1:
        $sigla    = $_REQUEST['sigla'];
        $nombre   = $_REQUEST['nombre'];
        $numeroR   = $_REQUEST['txtnumeror'];
        $optD      = $_REQUEST['optD'];
        if(empty($_REQUEST['resolucion'])){
            $resolucion     = NULL;
        } else {
            $resolucion     = $_REQUEST['resolucion'];
        }
        
        $sql_cons ="INSERT INTO `gf_tipo_documento_equivalente` 
            ( `sigla`, `nombre`, `resolucion`, `compania`,`numero_resolucion`,`envio_doc_soporte`) 
        VALUES (:sigla, :nombre ,:resolucion,:compania,:numero_resolucion,:envio_doc_soporte)";
        $sql_dato = array(
            array(":sigla",$sigla),
            array(":nombre",$nombre),
            array(":resolucion",$resolucion),
            array(":compania",$compania),
            array(":numero_resolucion",$numeroR),
            array(":envio_doc_soporte",$optD),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #Modificar  Tipo D
    case 2:
        $id       = $_REQUEST['id'];
        $sigla    = $_REQUEST['sigla'];
        $nombre   = $_REQUEST['nombre'];
        $numeroR  = $_REQUEST['txtnumeror'];
        $numeroR  = $_REQUEST['txtnumeror'];
        $optD     = $_REQUEST['optD'];
        if(empty($_REQUEST['resolucion'])){
            $resolucion     = NULL;
        } else {
            $resolucion     = $_REQUEST['resolucion'];
        }
        
        $sql_cons ="UPDATE `gf_tipo_documento_equivalente` 
                SET  `sigla`=:sigla, `nombre`=:nombre ,
                `resolucion`=:resolucion,`numero_resolucion`=:numero_resolucion ,`envio_doc_soporte`=:envio_doc_soporte
                WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":sigla",$sigla),
            array(":nombre",$nombre),
            array(":resolucion",$resolucion),
            array(":numero_resolucion",$numeroR),
            array(":envio_doc_soporte",$optD),
            array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    
    #* Eliminar  Tipo D
    case 3:
        $id     = $_POST['id'];
        $sql_cons ="DELETE FROM `gf_tipo_documento_equivalente` 
                WHERE `id_unico` =:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    
    #* Calcular Número
    case 4:
        $tipo = $_REQUEST['tipo'];
        $fac = $con->Listar("SELECT * FROM gf_documento_equivalente WHERE tipo = $tipo");
        if(count($fac)>0){
            $sql = $con->Listar("SELECT MAX(cast(numero as unsigned))+1 FROM gf_documento_equivalente where tipo = $tipo ");
            $numero = $sql[0][0];
        } else {
            $numero ='1';
        }
        echo $numero;
    break;
    #* Guardar Document
    case 5:
        $rta = 0;
        $tipo     = $_REQUEST['sltTipo'];
        $numero   = $_REQUEST['txtNumeroF'];
        $fecha    = fechaC($_REQUEST['fechaF']);
        $fechaV   = fechaC($_REQUEST['fechaV']);
        $tercero  = $_REQUEST['sltTercero'];
        $metodo_pago  = $_REQUEST['sltMetodo'];
        $forma_pago  = $_REQUEST['txtFormaP'];
        if(empty($_REQUEST['txtDescripcion'])){
            $descrip     = NULL;
        } else {
            $descrip     = $_REQUEST['txtDescripcion'];
        }
        
        $sql_cons ="INSERT INTO `gf_documento_equivalente` 
            ( `tipo`, `numero`, `fecha`, `fecha_vencimiento`,`tercero`,`descripcion`,`forma_pago`,`metodo_pago`,`parametrizacionanno`) 
        VALUES (:tipo, :numero ,:fecha,:fecha_vencimiento, :tercero,:descripcion,:forma_pago,:metodo_pago,:parametrizacionanno)";
        $sql_dato = array(
            array(":tipo",$tipo),
            array(":numero",$numero),
            array(":fecha",$fecha),
            array(":fecha_vencimiento",$fechaV),
            array(":tercero",$tercero),
            array(":descripcion",$descrip),
            array(":forma_pago",$forma_pago),
            array(":metodo_pago",$metodo_pago),
            array(":parametrizacionanno",$panno ),
        );

        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $idd = $con->Listar("SELECT id_unico FROM gf_documento_equivalente 
                WHERE numero = $numero and tipo = $tipo ");
            $id = trim($idd[0][0]);
            $rta = 'GF_DOCUMENTO_EQUIVALENTE.php?id='.md5($id);
        } 
        echo $rta;
    break;
    
    #* BUSCAR 
    case 6:
        $tipo = $_REQUEST['tipo'];
        $sqlB = "SELECT d.id_unico, td.sigla, 
            d.numero, DATE_FORMAT(d.fecha, '%d/%m/%Y')
     FROM gf_documento_equivalente d 
     LEFT JOIN gf_tipo_documento_equivalente td ON d.tipo = td.id_unico 
     WHERE td.id_unico=$tipo
        ORDER BY cast(numero as unsigned)  DESC ";

        $resultB = $mysqli->query($sqlB);
        $hmtl = '<option value=""> Buscar Documento Equivalente </option>';
        while ($rowB = mysqli_fetch_row($resultB)) {
            $hmtl .="<option value=".$rowB[0].">".$rowB[1]." ".$rowB[2]." ".$rowB[3]." </option>";
        }
        echo $hmtl;
    break;
    
    #* Cargar B
    case 7:
        $id = trim($_REQUEST['id']);
        echo 'GF_DOCUMENTO_EQUIVALENTE.php?id='.md5($id);
    break;
    #* Guardar Detalles Document
    case 8:
        $rta = 0;
        
        $documento      = $_REQUEST['id'];
        $descripcion    = $_REQUEST['concepto'];
        $codigo         = $_REQUEST['codigo'];
        $codigoImpuesto = $_REQUEST['sltCodigo'];
        $unidad         = $_REQUEST['sltUnidad'];
        $unidad         = (int)$unidad;
        $cantidad       = $_REQUEST['txtCantidad'];
        $valor_unitario = $_REQUEST['txtValorX'];
        $descripcion_concepto = $_REQUEST['descripcion'];
        $valor_unitario = str_replace(',', '', $valor_unitario);
        if(empty($_REQUEST['txtIva'])){
            $valor_iva  = 0;
        } else {
            $valor_iva  = $_REQUEST['txtIva'];
            $valor_iva  = str_replace(',', '', $valor_iva);
        }        
        $valor_total    = $_REQUEST['txtValorA'];
        $valor_total    = str_replace(',', '', $valor_total);
        
        
        $sql_cons ="INSERT INTO `gf_detalle_documento_equivalente` 
            ( `documento_equivalente`, `descripcion`, `cantidad`, `valor_unitario`,`valor_iva`,`valor_total`,`unidad_origen`,`codigo`,`codigo_impuesto`,`descripcion_concepto`) 
        VALUES (:documento_equivalente, :descripcion ,:cantidad, :valor_unitario, :valor_iva,:valor_total,:unidad_origen,:codigo,:codigo_impuesto,:descripcion_concepto)";
        $sql_dato = array(
            array(":documento_equivalente",$documento),
            array(":descripcion",$descripcion),
            array(":cantidad",$cantidad),
            array(":valor_unitario",$valor_unitario),
            array(":valor_iva",$valor_iva),
            array(":valor_total",$valor_total),
            array(":unidad_origen",$unidad),
            array(":codigo",$codigo),
            array(":codigo_impuesto",$codigoImpuesto),
            array(":descripcion_concepto",$descripcion_concepto),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta = 1;
        } 
        echo $rta;
    break;
    #* Modificar  Document
    case 9:
        $rta = 0;
        $id       = $_REQUEST['id'];
        $tipo     = $_REQUEST['sltTipo'];
        $numero   = $_REQUEST['txtNumeroF'];
        $fecha    = fechaC($_REQUEST['fechaF']);
        $fechaV   = fechaC($_REQUEST['fechaV']);
        $tercero  = $_REQUEST['sltTercero'];
        $metodo_pago  = $_REQUEST['sltMetodo'];
        $forma_pago  = $_REQUEST['txtFormaP'];
        if(empty($_REQUEST['txtDescripcion'])){
            $descrip     = NULL;
        } else {
            $descrip     = $_REQUEST['txtDescripcion'];
        }
        $sql_cons ="UPDATE `gf_documento_equivalente`
            SET `tipo`=:tipo,
            `numero`=:numero,
            `fecha`=:fecha,
            `fecha_vencimiento`=:fecha_vencimiento,
            `tercero`=:tercero,
            `descripcion`=:descripcion,
            `forma_pago`=:forma_pago,
            `metodo_pago`=:metodo_pago  
            WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":tipo",$tipo),
            array(":numero",$numero),
            array(":fecha",$fecha),
            array(":fecha_vencimiento",$fechaV),
            array(":tercero",$tercero),
            array(":descripcion",$descrip),
            array(":forma_pago",$forma_pago),
            array(":metodo_pago",$metodo_pago),
            array(":id_unico",$id),
        );

        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta = 1;
        } 
        echo $rta;
    break;
    #* ELIMINAR TODOS
    case 10:
        $rta = 0;
        $id       = $_REQUEST['id'];
        $sql_cons ="DELETE FROM  `gf_detalle_documento_equivalente`
        WHERE `documento_equivalente`=:documento_equivalente ";
        $sql_dato = array(
            array(":documento_equivalente",$id)
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta =1;
        }
        echo $rta;
    break;
    #* ELIMINAR DETALLE
    case 11:
        $rta = 0;
        $id       = $_REQUEST['iddetalle'];
        $sql_cons ="DELETE FROM  `gf_detalle_documento_equivalente`
        WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":id_unico",$id)
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta =1;
        }
        echo $rta;
    break;
}
