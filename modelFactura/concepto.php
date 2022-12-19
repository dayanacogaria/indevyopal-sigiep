<?php
require_once ('./Conexion/db.php');
class concepto{
    public $id_unico;
    public $tipo_concepto;
    public $nombre;
    public $tipo_opereacion;
    public $plan_inventario;
    public $concepto_financiero;
    public $formula;
    public $factor_base;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function getIdUnico(){
        return $this->id_unico;
    }

    public function setIdUnico($id_unico){
        $this->id_unico = $id_unico;
    }

    public function getTipoConcepto(){
        return $this->tipo_concepto;
    }

    public function setTipoConcepto($tipo_concepto){
        $this->tipo_concepto = $tipo_concepto;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function getTipoOpereacion(){
        return $this->tipo_opereacion;
    }

    public function setTipoOpereacion($tipo_opereacion){
        $this->tipo_opereacion = $tipo_opereacion;
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

    public function registrar(concepto $data){
        try {
            $sql = "INSERT INTO gp_concepto( 
                                    tipo_concepto, nombre, tipo_operacion, plan_inventario, concepto_financiero,
                                    formula, factor_base
                                ) VALUES(
                                    $data->tipo_concepto,
                                    '$data->nombre',
                                    $data->tipo_opereacion,
                                    $data->plan_inventario,
                                    $data->concepto_financiero,
                                    '$data->formula',
                                    $data->factor_base
                                )";
            $res = $this->mysqli->query($sql);
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar(concepto $data){
        try {
            $sql = "UPDATE gp_concepto SET
                    tipo_concepto       = $data->tipo_concepto,
                    nombre              = '$data->nombre',
                    tipo_operacion      = $data->tipo_opereacion,
                    plan_inventario     = $data->plan_inventario,
                    concepto_financiero = $data->concepto_financiero,
                    formula             = '$data->formula',
                    factor_base         = $data->factor_base
                    WHERE id_unico      = $data->id_unico";
            $res = $this->mysqli->query($sql);
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico){
        try {
            $sql = "DELETE FROM gp_concepto WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerConceptoFinanciero($id_unico, $panno){
        try {
            $id  = 0;
            $sql = "SELECT concepto_rubro FROM gp_configuracion_concepto 
            WHERE concepto = $id_unico AND parametrizacionanno = $panno";
            $res = $this->mysqli->query($sql);
            if(mysqli_num_rows($res) > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerConceptoPlan(){
        try{
            $str = "SELECT    con.id_unico, con.nombre, tar.valor, tar.id_unico, tar.porcentaje_iva, tar.porcentaje_impoconsumo, tpt.nombre, unf.id_unico,
                              unf.nombre, elu.valor_conversion, cpt.id_unico, cpt.porcentajeI
                    FROM      gp_concepto        AS con
                    LEFT JOIN gp_concepto_tarifa AS cpt ON con.id_unico        = cpt.concepto
                    LEFT JOIN gp_tarifa          AS tar ON cpt.tarifa          = tar.id_unico
                    LEFT JOIN gp_tipo_tarifa     AS tpt ON tar.tipo_tarifa     = tpt.id_unico
                    LEFT JOIN gf_elemento_unidad AS elu ON cpt.elemento_unidad = elu.id_unico
                    LEFT JOIN gf_unidad_factor   AS unf ON elu.unidad_empaque  = unf.id_unico
                    WHERE     MD5(con.plan_inventario) = '$this->plan_inventario'
                    AND       tar.id_unico IS NOT NULL";
            $res = $this->mysqli->query($str);
            return $res;
        }catch (Exception $e){
            die($e->getMessage());
        }
    }

    public function obtnerConceptoPl(){
        try{
            $str = "SELECT    con.plan_inventario
                    FROM      gp_concepto        as con
                    LEFT JOIN gp_concepto_tarifa as cpt ON con.id_unico = cpt.concepto
                    LEFT JOIN gp_tarifa          as tar ON cpt.tarifa = tar.id_unico
                    WHERE     con.id_unico = '$this->id_unico'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        }catch (Exception $e){
            die($e->getMessage());
        }
    }

    public function obtnerConceptoPlanI($id){
        try{
            $xxx = 0;
            $str = "SELECT    con.plan_inventario
                    FROM      gp_concepto  as con
                    WHERE     con.id_unico = $id";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        }catch (Exception $e){
            die($e->getMessage());
        }
    }

    public function obtenerConceptosPlanId(){
        try {
            $str = "SELECT    DISTINCT con.id_unico, con.nombre
                    FROM      gp_concepto        as con
                    LEFT JOIN gp_concepto_tarifa as cpt ON con.id_unico = cpt.concepto
                    LEFT JOIN gp_tarifa          as tar ON cpt.tarifa = tar.id_unico
                    WHERE     md5(con.plan_inventario) = '$this->plan_inventario'";
            $res = $this->mysqli->query($str);
            return $res;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}