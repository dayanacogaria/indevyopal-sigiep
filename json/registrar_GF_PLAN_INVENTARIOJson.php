<?php
/**
 * registrar_GF_PLAN_INVENTARIO.php
 *
 * Archivo para registro,eliminado y modificado
 *
 * @author Alexander Numpaque
 * @package Plan Inventario
 * @version $Id: registrar_GF_PLAN_INVENTARIO.php 001 2017-05-26 Alexander Numpaque$
 * */

/**
 * gf_plan_inventario
 *
 * Clase para guardado, modificado y eliminado
 *
 * @author Alexander Numpaque
 * @package Plan Inventario
 * @version $Id: registrar_GF_PLAN_INVENTARIO.php 001 2017-05-26 Alexander Numpaque$
 * @version $Id: registrar_GF_PLAN_INVENTARIO.php 002 2018-04-16 Alexander Numpaque$
 * */
class gf_plan_inventario{

    public $id_unico;
    public $codigo;
    public $nombre;
    public $tieneMovimiento;
    public $tipoInventario;
    public $unidad;
    public $tipoActivo;
    public $compania;
    public $predecesor;
    public $ficha;
    public $xCantidad;
    public $xFactura;
    public $tipo_concepto;
    public $tipo_operacion;
    public $plan_inventario;
    public $concepto_financiero;
    public $formula;
    public $factor_base;
    public $parametrizacionanno;

    public function getIdUnico(){
        return $this->id_unico;
    }

    public function setIdUnico($id_unico){
        $this->id_unico = $id_unico;
    }

    public function getCodigo(){
        return $this->codigo;
    }

    public function setCodigo($codigo){
        $this->codigo = $codigo;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function getTieneMovimiento(){
        return $this->tieneMovimiento;
    }

    public function setTieneMovimiento($tieneMovimiento){
        $this->tieneMovimiento = $tieneMovimiento;
    }

    public function getTipoInventario(){
        return $this->tipoInventario;
    }

    public function setTipoInventario($tipoInventario){
        $this->tipoInventario = $tipoInventario;
    }

    public function getUnidad(){
        return $this->unidad;
    }

    public function setUnidad($unidad){
        $this->unidad = $unidad;
    }

    public function getTipoActivo(){
        return $this->tipoActivo;
    }

    public function setTipoActivo($tipoActivo){
        $this->tipoActivo = $tipoActivo;
    }

    public function getCompania(){
        return $this->compania;
    }

    public function setCompania($compania){
        $this->compania = $compania;
    }

    public function getPredecesor(){
        return $this->predecesor;
    }

    public function setPredecesor($predecesor){
        $this->predecesor = $predecesor;
    }

    public function getFicha(){
        return $this->ficha;
    }

    public function setFicha($ficha){
        $this->ficha = $ficha;
    }

    public function getXCantidad(){
        return $this->xCantidad;
    }

    public function setXCantidad($xCantidad){
        $this->xCantidad = $xCantidad;
    }

    public function getXFactura(){
        return $this->xFactura;
    }

    public function setXFactura($xFactura){
        $this->xFactura = $xFactura;
    }

    public function getTipoConcepto(){
        return $this->tipo_concepto;
    }

    public function setTipoConcepto($tipo_concepto){
        $this->tipo_concepto = $tipo_concepto;
    }

    public function getTipoOperacion(){
        return $this->tipo_operacion;
    }

    public function setTipoOperacion($tipo_operacion){
        $this->tipo_operacion = $tipo_operacion;
    }

    public function getPlanInventario(){
        return $this->plan_inventario;
    }

    public function setPlanInventario($plan_inventario){
        $this->plan_inventario = $plan_inventario;
    }

    public function getConceptoFinanciero(){
        return $this->concepto_financiero;
    }

    public function setConceptoFinanciero($concepto_financiero){
        $this->concepto_financiero = $concepto_financiero;
    }

    public function getFormula(){
        return $this->formula;
    }

    public function setFormula($formula){
        $this->formula = $formula;
    }

    public function getFactorBase(){
        return $this->factor_base;
    }

    public function setFactorBase($factor_base){
        $this->factor_base = $factor_base;
    }

    public function getParametrizacionanno(){
        return $this->parametrizacionanno;
    }

    public function setParametrizacionanno($parametrizacionanno){
        $this->parametrizacionanno = $parametrizacionanno;
    }

    /**
     * save_data
     *
     * Función para registrar valores en la base de datos
     *
     * @author Alexander Numpaque
     * @package Plan Inventario
     * @param String $codigo Codigo de plan inventario
     * @param String $nombre Nombre de plan inventario
     * @param tinyint $movimiento Tiene o no tiene moviento
     * @param int $tipoInv Id de tipo de inventario
     * @param int $undFact Id de unodad factor
     * @param int $tipoAct Id de tipo activo
     * @param int $compania Id de compañia
     * @param int $predecesor Id de predecesor
     * @param int $ficha Id de Ficha
     * @param int $xCantidad Indicador de elemento de objeto de hotel
     * @param int $xConcepto Indicador de elemento facturable
     * @return bool $inserted Retorna verdadero cuando se inserta en la base de datos
     */
    public static function save_data($codigo, $nombre, $movimiento, $tipoInv, $undFact, $tipoAct,$compania, $predecesor, $ficha, $xCantidad, $xConcepto, $codigo_barras) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "INSERT INTO gf_plan_inventario (codi, nombre, tienemovimiento, tipoinventario, 
            unidad, tipoactivo, compania, predecesor, ficha, xCantidad, xFactura, codigo_barras) 
            VALUES($codigo, $nombre, $movimiento, $tipoInv, $undFact, $tipoAct, $compania, 
            $predecesor, $ficha, $xCantidad, $xConcepto,$codigo_barras)";
        $result = $mysqli->query($sql);
        if($result == true) {
            $inserted = true;
        }
        return $inserted;
    }

    /**
     * modify_data
     *
     * Función para modificar valores en la base de datos
     *
     * @author Alexander Numpaque
     * @package Plan Inventario
     * @param String $codigo Codigo de plan inventario
     * @param String $nombre Nombre de plan inventario
     * @param tinyint $movimiento Tiene o no tiene moviento
     * @param int $tipoInv Id de tipo de inventario
     * @param int $undFact Id de unidad factor
     * @param int $tipoAct Id de tipo activo
     * @param int $predecesor Id de predecesor
     * @param int $ficha Id de Ficha
     * @param int $id_unico Id del registro a modificar
     * @param int $xCantidad Indicador de elemento hotel
     * @param int $xConcepto Indicador de concepto factura
     * @return bool $edited Retorna verdadero cuando se modifica el registro en la base de datos
     */
    public static  function modify_data($codigo, $nombre, $movimiento, $tipoInv, $undFact, $tipoAct, $predecesor, $ficha, $id_unico, $xCantidad, $xConcepto, $codigo_barras) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE  gf_plan_inventario
                SET     codi            = $codigo,
                        nombre          = $nombre,
                        tienemovimiento = $movimiento,
                        tipoinventario  = $tipoInv,
                        unidad          = $undFact,
                        tipoactivo  = $tipoAct,
                        predecesor  = $predecesor,
                        ficha       = $ficha,
                        xCantidad   = $xCantidad,
                        xFactura   = $xConcepto,
                        codigo_barras   = $codigo_barras 
                WHERE   id_unico    = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }

    /**
     * delete_data
     *
     * Función para eliminar un registro de la base de datos
     *
     * @author Alexander Numpaque
     * @package Plan Inventario
     * @param int $id_unico Id del registro a eliminar
     * @return bool $deleted retonara verdadero cuando se eliminar el registro en la base de datos
     */
    public static function delete_data($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gf_plan_inventario WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
    }

    /**
     * save_plan_p
     *
     * Función para guardar en la tabla plan inventario asociado
     *
     * @author Alexander Numpaque
     * @package Plan Inventario
     * @param int|String $codigo Codigo del registro
     * @param int $padre Id de plan inventario padre
     * @return bool $inserted cuando el valor es insertado retornara verdadero
     */
    public static function save_plan_p($codigo, $padre) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $sql = "SELECT MAX(id_unico) FROM gf_plan_inventario WHERE codi = $codigo";
        $result = $mysqli->query($sql);
        $planHijo = $result->fetch_row();
        if(!empty($padre)) {
            $planPadre = '"'.$mysqli->real_escape_string(''.$padre.'').'"';
            $insertPlanPadre = "insert into gf_plan_inventario_asociado(plan_padre,plan_hijo) values ($planPadre,$planHijo[0])";
            $resultPlanPadre = $mysqli->query($insertPlanPadre);
            if($resultPlanPadre == true) {
                $inserted = true;
            }
        }
        return $inserted;
    }

    /**
     * modify_plan_p
     *
     * Función para modificar los valores en la tabla plan inventario
     *
     * @author Alexander Numpaque
     * @package Plan Inventario
     * @param int $planAso Id plan asociado como hijo
     * @param int $padre Id de plan inventario padre
     * @param int $id_unico Id de plan inventario asociado
     * @return bool $edited cuando el valor es modificado retornara verdadero
     */
    public static function modify_plan_p($planAso, $padre, $id_unico) {
        require ('../Conexion/conexion.php');
        $edited = false;
        if(!empty($planAso)) {
            if(!empty($padre)) {
                $sql="UPDATE gf_plan_inventario_asociado SET plan_padre='$padre' WHERE id_unico = '$planAso'";
                $result = $mysqli->query($sql);
                if($result == true) {
                    $edited = true;
                }
            } else {
                $sql="DELETE FROM gf_plan_inventario_asociado WHERE id_unico = '$planAso'";
                $result = $mysqli->query($sql);
                if($result == true) {
                    $edited = true;
                }
            }
        } else {
            $sql="INSERT INTO gf_plan_inventario_asociado(plan_padre,plan_hijo) VALUES($padre, $id_unico)";
            $result = $mysqli->query($sql);
            if($result == true) {
                $edited = true;
            }
        }
        return $edited;
    }

    public static function guardarConceptoFactura(gf_plan_inventario $data){
        try {
            require ('../Conexion/conexion.php');
             $str = "INSERT INTO gp_concepto(tipo_concepto, nombre, tipo_operacion, plan_inventario,  factor_base, compania)
                    VALUES(
                      $data->tipo_concepto,
                      $data->nombre,
                      $data->tipo_operacion,
                      $data->plan_inventario,
                      $data->factor_base,
                      $data->compania
                    )
                    ";
            $res = $mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function  obtenerIDRegistro($codigo){
        try {
            require ('../Conexion/conexion.php');
            $str = "SELECT id_unico FROM gf_plan_inventario WHERE  codi = $codigo";
            $res = $mysqli->query($str);
            if(!empty(mysqli_num_rows($res))){
                $row = mysqli_fetch_row($res);
                return $row[0];
            }else{
                return 0;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function existeConcepto($id){
        try {
            require ('../Conexion/conexion.php');
            $xxx = 0;
            $str = "SELECT id_unico FROM gp_concepto WHERE plan_inventario = $id";
            $res = $mysqli->query($str);
            if(mysqli_num_rows($res) > 0){
                $row = mysqli_fetch_row($res);
                $xxx = $row[0];
            }else{
                $xxx = 0;
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function eliminarConceptos($plan){
        try {
            require ('../Conexion/conexion.php');
            $str = "DELETE FROM gp_concepto WHERE plan_inventario = $plan";
            return $mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}