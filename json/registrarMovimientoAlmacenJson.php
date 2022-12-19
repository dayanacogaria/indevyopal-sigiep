<?php

/**
* movimiento
*
* Clase para registrar, modificar y eliminar los valores en la tabla gf_movimiento
*
* @author Alexander Numpaque
* @package Movimiento
* @version 0001 30/05/2017
*/
class movimiento {

    /**
     * save_data
     *
     * Función para guardar los valores en la base de datos
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $centrocosto Id de centro costo
     * @param int $lugarE Id de lugar de entrega
     * @param int $unidadPE Id de unidad plazo de entrega
     * @param int $plazoE Plazo de entrega
     * @param int $proyecto Id de Proyecto
     * @param int $centrocosto Id de centro costo
     * @param int $rubroP Id de rubro fuente
     * @param int $tercero Id de tercero
     * @param int $dependencia Id de dependencia
     * @param int $responsable Id de responsable
     * @param int $fecha Id de fecha
     * @param int $paramA Id de parametrización año
     * @param int $numeroC Número de movimiento
     * @param int $tipoM Tipo movimiento
     * @param int $iva Iva
     * @param int $compania Id de la compañia
     * @param int $tipo_doc_sop Id de tipo documento soporte
     * @param String $num_doc_sop Número de documento de soporte
     * @return bool $inserted cuando es insertado retornara verdadero
     */
    public static function save_data($numero, $fecha, $descripcion, $plazoE, $observaciones, $tipoM, $param, $responsable, $tercero, $dependencia, $centrocosto, $rrptal, $proyecto, $lugarE, $unidadE, $estado, $iva, $compania, $tipo_doc_sop, $num_doc_sop) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gf_movimiento(numero, fecha, descripcion, plazoentrega, observaciones, tipomovimiento, parametrizacionanno, tercero, tercero2, dependencia, centrocosto, rubropptal, proyecto, lugarentrega, unidadentrega, estado, porcivaglobal, compania, tipo_doc_sop, numero_doc_sop)
              VALUES ($numero, $fecha, $descripcion, $plazoE, $observaciones, $tipoM, $param, $responsable, $tercero, $dependencia, $centrocosto, $rrptal, $proyecto, $lugarE, $unidadE, $estado, $iva, $compania, $tipo_doc_sop, $num_doc_sop)";
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
    }

    /**
     * get_last_id
     *
     * Función para obtener el ultimo id registrado
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $tipomovimiento Tipo de movimiento
     * @param int $numero Número de movimiento
     * @return int $id_unico Id del ultimo registro en la tabla gf_movimiento
     */
    public static function get_last_id($tipomovimiento, $numero) {
        require ('../Conexion/conexion.php');
        $id_unico = 0;
        $sqlUL = "SELECT MAX(id_unico) FROM gf_movimiento WHERE numero=$numero AND tipomovimiento=$tipomovimiento";
        $resultUL = $mysqli->query($sqlUL);
        $rows = mysqli_num_rows($resultUL);
        if($rows > 0){
            $fila = mysqli_fetch_row($resultUL);
            $id_unico = $fila[0];
        }
        return $id_unico;
    }


    /**
     * save_detail_mov
     *
     * Función para registrar los detalles en  el movimiento
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $planI Id de parametrización año
     * @param int $cantidad cantidad de objetos
     * @param int|float $valor Valor del objeto
     * @param int|float $valorIva valor del iva
     * @return bool $inserted Si el valor es ingresado retornara verdadero
     */
    public static function save_detail_mov($planI, $cantidad, $valor, $valorIva , $movimiento, $afectado = 'NULL') {
        require ('../Conexion/conexion.php');
        $inserted = "";
        date_default_timezone_set('America/Bogota');
        $hora     = date('H:i:s');
        $sql = "INSERT INTO gf_detalle_movimiento(planmovimiento, cantidad, valor, iva, movimiento, detalleasociado, hora) VALUES($planI, $cantidad, $valor, $valorIva, $movimiento, $afectado, '$hora');";
        $result = $mysqli->query($sql);
        if ($result == true) {
            $inserted = true;
        }
        return $inserted;
    }

    /**
     * save_detail_mov_ajuste
     *
     * Función para registrar los detalles en  el movimiento
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $planI Id de parametrización año
     * @param int $cantidad cantidad de objetos
     * @param int|float $valor Valor del objeto
     * @param int|float $valorIva valor del iva
     * @param int|float $ajuste valor de ajuste
     * @return bool $inserted Si el valor es ingresado retornara verdadero
     */
    public static function save_detail_mov_ajuste($planI, $cantidad, $valor, $valorIva ,$movimiento, $afectado = 'NULL', $ajuste) {
        require ('../Conexion/conexion.php');
        $inserted = "";
        $sql = "INSERT INTO gf_detalle_movimiento(planmovimiento, cantidad, valor, iva, movimiento, detalleasociado, ajuste) VALUES($planI, $cantidad, $valor, $valorIva, $movimiento, $afectado, $ajuste);";
        $result = $mysqli->query($sql);
        if ($result == true) {
            $inserted = true;
        }
        return $inserted;
    }

    /**
     * gf_detail_mov
     *
     * Función para obtener la cantidad de n detalle del movimiento asociado
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $id_unico Id de movimiento asociado
     * @return array $details Array con los id de los detalles del movimiento asociado
     */
    public static function get_detail_mov ($id_unico) {
        require ('../Conexion/conexion.php');
        $details = array();
        if(!empty($id_unico)){
            $sql = "SELECT id_unico FROM gf_detalle_movimiento WHERE movimiento = $id_unico";
            $result = $mysqli->query($sql);
            $rows = mysqli_num_rows($result);
            if($rows > 0){
                while ($row = mysqli_fetch_row($result)) {
                    $details[] = $row[0];
                }
            }
        }
        return $details;
    }

    /**
     * gf_detail_mov2
     *
     * Función para obtener la cantidad de n detalle del movimiento asociado
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $id_unico Id de movimiento asociado
     * @return array $details Array con los id de los detalles del movimiento asociado
     */
    public static function get_detail_mov2 ($id_unico) {
        require ('../Conexion/conexion.php');
        $details = array();
        if(!empty($id_unico)){
            $sql = "SELECT    dtm.id_unico
                    FROM      gf_detalle_movimiento as dtm
                    LEFT JOIN gf_plan_inventario    as pln ON dtm.planmovimiento = pln.id_unico
                    WHERE     dtm.movimiento      = $id_unico
                    AND       pln.tipoinventario != 5";
            $result = $mysqli->query($sql);
            $rows = mysqli_num_rows($result);
            if($rows > 0){
                while ($row = mysqli_fetch_row($result)) {
                    $details[] = $row[0];
                }
            }
        }
        return $details;
    }

    /**
     * get_values_detail
     *
     * Función para obtener los valores del detalle enviado
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $id_unico Id de detalle
     * @return Array $values Array con los valores del detalle
     */
    public static function get_values_detail ($id_unico) {
        require ('../Conexion/conexion.php');
        $values = array();
        $sql = "SELECT planmovimiento, cantidad, valor, iva  FROM gf_detalle_movimiento WHERE  id_unico = $id_unico";
        $result = $mysqli->query($sql);
        $rows = mysqli_num_rows($result);
        if($rows > 0) {
            $row = mysqli_fetch_row($result);
            for($a = 0; $a < count($row); $a ++) {
                $values[] = $row[$a];
            }
        }
        return $values;
    }

    /**
     * modify_data
     *
     * Modificar valores de la base de datos
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $id_unico Id del registro a modificar
     * @param date $fecha Fecha del movimiento
     * @param String $observaciones Observaciones de movimiento
     * @param String $descripcion Descripción de movimiento
     * @return bool $edited Si el registro es modificado retornara verdadero
     */
    public static function modify_data($id_unico, $fecha, $observaciones, $descripcion, $iva) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE gf_movimiento SET fecha = $fecha, observaciones = $observaciones, descripcion = $descripcion, porcivaglobal = $iva WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }

    /**
     * modify_detail
     *
     * Función para modificar los valores en el detalle
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int|float $valor Valor en el detalle
     * @param float $cantidad Cantidad a ingresar en el detalle
     * @param float $valorIva Iva del detalle
     * @param int $id_unico Id del detalle
     * @return bool $edited retorna verdadero cuando el valor es modificado
     */
    public static function modify_detail ($valor, $cantidad, $valorIva, $id_unico) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE gf_detalle_movimiento SET valor = $valor, cantidad = $cantidad, iva = $valorIva WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }

    /**
     * delete_detail
     *
     * Función para eliminar el detalle del movimiento seleccionado
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $id_unico Id del detalle de movimiento
     * @return bool $deleted Si el registro eliminado se retornara verdadero
     */
    public static function delete_detail ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gf_detalle_movimiento WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
    }

    /**
     * delete_data
     *
     * Función para eliminar el registro de base de datos
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $id_unico Id del movimiento
     * @return bool $deleted Retornara verdadero si el registro es eliminado
     */
    public static function delete_data ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gf_movimiento WHERE  id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
    }

    /**
     * get_values_aso_for_exit
     *
     * Función para obtener los valores que faltan para registrar un movimiento de salida
     *
     * @author Alexander Numpaque
     * @package Movimiento
     * @param int $id_aso Id de movimiento asociado
     * @return string Cadena de texto con los valores faltantes para ubicar en el movimiento de salida
     */
    public static function get_values_aso_for_exit ($id_aso) {
        require ('../Conexion/conexion.php');
        $values = "";
        $sql = "SELECT plazoentrega, rubropptal, lugarentrega, unidadentrega, porcivaglobal, tipo_doc_sop, numero_doc_sop, tercero FROM gf_movimiento WHERE id_unico = $id_aso";
        $result = $mysqli->query($sql);
        $row = mysqli_fetch_row($result);
        $rows= mysqli_num_rows($result);
        if($rows > 0) {
            $values = $row[0].",".$row[1].",".$row[2].",".$row[3].",".$row[4].",".$row[5].",".$row[6].",".$row[7];
        }
        return $values;
    }

    public function obtnerDataAsociado($aso){
        require ('../Conexion/conexion.php');
        $sql = "SELECT cantidad FROM gf_detalle_movimiento WHERE detalleasociado = $aso";
        $res = $mysqli->query($sql);
        $row = $res->fetch_all(MYSQLI_NUM);
        return $row;
    }

    public function obtenerCiudadCompania($compania){
        require ('../Conexion/conexion.php');
        $xxx = "NULL";
        $str = "SELECT ciudadidentificacion FROM gf_tercero WHERE id_unico = $compania";
        $res = $mysqli->query($str);
        if($res->num_rows > 0){
            $row = $res->fetch_row();
            $xxx = $row[0];
        }
        return $xxx;
    }
}
