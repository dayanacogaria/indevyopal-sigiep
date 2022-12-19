<?php
/**
 * Created by Alexander.
 * User: Alexander
 * Date: 27/05/2017
 * Time: 9:02 AM
 */

/**
 * Función para obtener el anno relacionado a la paremtriacion
 * 
 * get_date_param
 * 
 * @author Alexander Numpaque
 * @package Movimientos
 * @param int $param Id de parametrización anno
 * @return $anno Año relacionado a la parametrización año
 */
function get_date_param ($param) {
    require ('../Conexion/conexion.php');                                                   //Archivo de conexión
    $anno = "";                                                                             //Inicializamos la variable año
    $sql = "SELECT anno FROM gf_parametrizacion_anno WHERE  id_unico = $param";             //Consulta para obtener el año de la parametrización año
    $result = $mysqli->query($sql);
    $rows = mysqli_num_rows($result);
    if($rows > 0) {                                                                         //Validamos que la consulta retorne valores
        $row = mysqli_fetch_row($result);
        $anno = $row[0];                                                                    //Asiganamos el valor devuelto por la base de datos
    }
    return $anno;                                                                           //Retornamos la variable
}
/**
 * gf_max_date
 * 
 * Función para obtener la fecha maxima por el tipo de movimiento
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param int $type Id de tipo movimiento
 * @return date $date Fecha maxima de movimiento
 */
function get_max_date($type) {
    require ('../Conexion/conexion.php');                                                   //Archivo de conexión
    $date = "";                                                                             //Inicializamos la variable $date
    $sql = "SELECT fecha FROM gf_movimiento WHERE tipomovimiento = $type";                  //Consulta para obtener la fecha maxima al tipo de movimiento
    $result = $mysqli->query($sql);
    $rows = mysqli_num_rows($result);
    if($rows > 0) {                                                                         //Si movimiento mayor que 0
        $row = mysqli_fetch_row($result);                                                      
        $date = $row[0];                                                                    //Asigamos el valor obtenido por la consulta
    }
    return $date;                                                                           //Retornamos la variable
}

/**
 * get_id_details_mov
 *
 * Función para obtener los id y el valor del id de los detalles relacionados a un movimiento especifico
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param int $mov Id del movimiento
 * @return array $values Array con los id de valor y movimiento
 */
function get_id_details_mov($mov) {
    require ('../Conexion/conexion.php');                                                   //Archivo de conexión
    $values = array();                                                                      //Inicializamos el array $values
    $sql = "SELECT id_unico,valor FROM gf_detalle_movimiento WHERE movimiento = $mov";      //Consulta para obtener los id y valor relacionados al moviemiento
    $result = $mysqli->query($sql);
    $rows = mysqli_num_rows($result);                                                       //Contamos o obtenemos la cantidad de registros devueltos por la consulta
    if($rows > 0) {                                                                         //Validamos que la consulta retorne un  valor mayor que 0
        while ($row = mysqli_fetch_row($result)) {                                          //Desplegamos un array con los valores obtenidos de base de datos
            $values = array($row[0]);                                                       //Cargamos el array values junto con otro un array con el id del detalle y su valor
        }
    }
    return $values;                                                                         //Retornamos el array
}

/**
 * get_values_detail
 *
 * Función para obtener los valores del detalle a consultar
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param  int $id_unico Id del detalle de movimiento
 * @return array $values Array con los valores obtenidos del detalle
 */
function get_values_detail ($id_unico) {
    require ('../Conexion/conexion.php');                                                   //Archivo de conexión
    $values = array();
    $sql = "SELECT id_unico, planmovimiento, cantidad, valor, iva, movimiento FROM gf_detalle_movimiento WHERE id_unico = $id_unico";
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_row($result);
        $values[0] = $row[0]; #Id del Detalle
        $values[1] = $row[1]; #Id de plan inventario
        $values[2] = $row[2]; #Cantidad de elementos
        $values[3] = $row[3]; #Valor del detalle
        $values[4] = $row[4]; #Valor iva del detalle
        $values[5] = $row[5]; #Id del movimiento
    }
    return $values;
}

/**
 * get_values_mov
 *
 * Función para obtener los valores del movimiento
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param int $id_unico Id del movimiento
 * @return array $values Array con los valores obtenidos de la consulta
 */
function get_values_mov ($id_unico) {
    require ('../Conexion/conexion.php');                                                   //Archivo de conexión
    $values = array();
    $sql = "SELECT  id_unico,  numero,       descripcion, porcivaglobal, plazoentrega, observaciones,  tipomovimiento, compania, 
                    tercero,   tercero2,     dependencia, centrocosto,   rubropptal,   proyecto,       lugarentrega,   unidadentrega,
                    estado,    tipo_doc_sop, numero_doc_sop 
            FROM    gf_movimiento 
            WHERE   id_unico = $id_unico";
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_row($result);
        $values[0]  = $row[0]; #Id del movimiento
        $values[1]  = $row[1]; #Número del movimiento
        $values[2]  = $row[2]; #Descripción del movimiento
        $values[3]  = $row[3]; #Porcentaje global del movimiento
        $values[4]  = $row[4]; #Plazo de entrega
        $values[5]  = $row[5]; #Observaciones
        $values[6]  = $row[6]; #Id de tipo de movimiento
        $values[7]  = $row[7]; #Id compania
        $values[8]  = $row[8]; #Id del responsable
        $values[9]  = $row[9]; #Id del proveedor o tercero2
        $values[10] = $row[10];#Id de la dependencia
        $values[11] = $row[11];#Id de centro costo
        $values[12] = $row[12];#Id de rubro presupuestal
        $values[13] = $row[13];#Id de proyecto
        $values[14] = $row[14];#Id de lugar de entrega
        $values[15] = $row[15];#Unidad de entrega
        $values[16] = $row[16];#Id de estado de movimiento
        $values[17] = $row[17];#Id de tipo de documento de soporte
        $values[18] = $row[18];#Número de documento soporte
    }
    return $values;
}

/**
 * get_max_number_type
 *
 * Función para obtener el ultimo numero respecto al tipo enviado, si no hay iniciaiza la cuenta en el año actual con cinco ceros y un uno es decir 2017000001
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param int $type Id de tipo de movimiento
 * @return string $number 2017000001
 */
function get_max_number_type ($type) {
    require ('../Conexion/conexion.php');
    $sql = "SELECT MAX(numero) FROM gf_movimiento WHERE tipomovimiento = $type";
    $result = $mysqli->query($sql);
    $row = mysqli_fetch_row($result);
    if(!empty($row[0])) {
        $number = $row[0] + 1;
    } else {
        $number = date('Y').'000001';
    }
    return $number;
}

/**
 * get_max_mov
 *
 * Función para obtener el último id dependiendo del tipo
 * @param int $type Id del tipo de movimiento
 * @return int $id_mov Id del ultimo movimiento registrado
 */
function get_max_mov ($type) {
    require ('../Conexion/conexion.php');
    $id_mov = 0;
    //$sql = "SELECT MAX(id_unico) FROM gf_movimiento WHERE tipomovimiento = $type";
    $sql = "SELECT id_unico FROM gf_movimiento WHERE tipomovimiento = $type ORDER BY id_unico DESC LIMIT 1";
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_row($result);
        $id_mov = $row[0];
    }
    return $id_mov;
}

/**
 * get_max_detail_mov
 *
 * Función  para obtener el ultimo detalle respecto al movimiento enviado
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param int $mov Id de movimiento
 * @return int $id_d Id del ultimo detalle registrado con respecto al movimiento
 */
function get_max_detail_mov ($mov) {
    require ('../Conexion/conexion.php');
    $id_d = 0;
    $sql = "SELECT MAX(id_unico) FROM gf_detalle_movimiento WHERE movimiento = $mov";
    $result = $mysqli->query($sql);
    if($result == true){
        $row = mysqli_fetch_row($result);
        $id_d = $row[0];
    }
    return $id_d;
}

/**
 * insert_mov_pro
 *
 * Función para insertar valores a movimiento_producto
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param int $producto Id de producto a registrar
 * @param int $detalle Id del detalle al que se relaciona el producto
 * @return bool $inserted Cuando es insertdado retorna verdadero
 */
function insert_mov_pro ($producto, $detalle) {
    require ('../Conexion/conexion.php');
    $inserted = false;
    $sql = "INSERT INTO gf_movimiento_producto(producto, detallemovimiento) VALUES ($producto, $detalle)";
    $result = $mysqli->query($sql);
    if($result == true) {
        $inserted = true;
    }
    return $inserted;
}

/**
 * update_porce_mov
 *
 * Función para modificar el campo de porcentaje en el movimiento para mantener el porcentaje entre movimientos cuando se hace translado
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param int $id_unico Id de movimiento
 * @param int $porce % de iva
 * @return bool $updated Retorna verdadero cuando se actulizado
 */
function update_porce_mov ($id_unico, $porce) {
    require ('../Conexion/conexion.php');
    $updated = false;
    $sql = "UPDATE gf_movimiento SET porcivaglobal = $porce WHERE id_unico = $id_unico";
    $result = $mysqli->query($sql);
    if($result == true) {
        $updated = true;
    }
    return $updated;
}

/**
 * get_porcent_param_b
 *
 * Función obtenemos el valor del porcentaje de la tabla gs_parametros_basicos
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @return int $porcent Valor del porcentaje en gs_parametros_basicos
 */
function get_porcent_param_b () {
    require ('../Conexion/conexion.php');
    $porc = 0;
    $sql = "SELECT valor FROM gs_parametros_basicos WHERE id_unico = 2";
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_row($result);
        $porc = $row[0];
    }
    return $porc;
}

/**
 * update_values_i_c
 *
 * Función para modificar la cantidad y el iva en el detalle movimiento seleccionado
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param int $id_unico Id de detalle movimiento
 * @param int|float $cantidad Cantidad de elementos
 * @param int|float $iva Valor del iva
 * @return bool Si es modificado envia true
 */
function update_values_i_c($id_unico, $cantidad, $iva) {
    require ('../Conexion/conexion.php');
    $updated = false;
    $sql = "UPDATE gf_detalle_movimiento SET cantidad = $cantidad, iva = $iva WHERE id_unico = $id_unico";
    $result = $mysqli->query($sql);
    if($result == true) {
        $updated = true;
    }
    return $updated;
}

/**
 * get_n_products_detail
 *
 * Función para obtener la cantidad de productos registrados en movimiento producto relacionados al detalle enviado
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param int $detail Id de detalle movimiento
 * @return int $cantidad Cantidad de registros o productos que estan registrados al detalle seleccionado
 */
function get_n_products_detail ($detail) {
    require ('../Conexion/conexion.php');
    $cantidad = 0;
    $sql = "SELECT COUNT(producto) FROM gf_movimiento_producto WHERE detallemovimiento = $detail";
    $result = $mysqli->query($sql);
    if($result == true && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_row($result);
        $cantidad = $row[0];
    }
    return $cantidad;
}