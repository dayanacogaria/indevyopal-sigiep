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

    public function getMysqli(){
        return $this->mysqli;
    }

    public function setMysqli($mysqli){
        $this->mysqli = $mysqli;
    }

    public function registrar(concepto $data){
        try {
            $sql = "INSERT INTO gp_concepto(
                                    tipo_concepto,
                                    nombre,
                                    tipo_opereacion,
                                    plan_inventario,
                                    concepto_financiero,
                                    formula,
                                    factor_base
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

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar(concepto $data){
        try {
            $sql = "UPDATE gp_concepto SET
                    tipo_concepto       = $data->tipo_concepto,
                    nombre              = '$data->nombre',
                    tipo_opereacion     = $data->tipo_opereacion,
                    plan_inventario     = $data->plan_inventario,
                    concepto_financiero = $data->concepto_financiero,
                    formula             = '$data->formula',
                    factor_base         = $data->factor_base
                    WHERE id_unico      = $data->id_unico";
            $res = $mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico){
        try {
            $sql = "DELETE FROM gp_concepto WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerConceptoFinanciero($id_unico){
        try {
            $id  = 0;
            $sql = "SELECT concepto_financiero FROM gp_concepto WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if(mysqli_num_rows($res) > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerConceptoPlan(){
        try{
            $str = "SELECT    con.id_unico, con.nombre, tar.valor, tar.id_unico, tar.porcentaje_iva, tar.porcentaje_impoconsumo, tpt.nombre
                    FROM      gp_concepto        as con
                    LEFT JOIN gp_concepto_tarifa as cpt ON con.id_unico = cpt.concepto
                    LEFT JOIN gp_tarifa          as tar ON cpt.tarifa = tar.id_unico
                    LEFT JOIN gp_tipo_tarifa     as tpt ON tar.tipo_tarifa = tpt.id_unico
                    WHERE     md5(con.plan_inventario) = '$this->plan_inventario'
                    AND       tar.id_unico IS NOT NULL";
            $res = $this->mysqli->query($str);
            return $res;
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

    public function obtenerConceptosFinanciera($param){
        try {
            $str = "SELECT id_unico, nombre FROM gf_concepto WHERE  parametrizacionanno = $param";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoOrden($orden, $param){
        try {
            $str = "SELECT id_unico, UPPER(nombre) FROM gp_concepto WHERE parametrizacionanno = $param ORDER BY id_unico $orden";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage;
        }
    }

    public function obtenerDataCompania($id){
        try {
            $str = "SELECT UPPER(razonsocial), numeroidentificacion, ruta_logo FROM gf_tercero WHERE id_unico = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerConceptos($cptI, $cptF){
        try {
            $str = "SELECT id_unico, UPPER(nombre) FROM gp_concepto WHERE id_unico BETWEEN $cptI AND $cptF";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDatosConcepto($concpto){
        try {
            $str = "SELECT      DATE_FORMAT(gpg.fecha_pago, '%d/%m/%Y'), gpg.numero_pago,
                                (
                                    IF(
                                        CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                        gtr.razonsocial,
                                        CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                    )
                                ),
                                gdf.valor, gdf.cantidad, gpf.descripcion
                    FROM        gp_detalle_pago    AS gdp
                    LEFT JOIN   gp_detalle_factura AS gdf ON gdp.detalle_factura = gdf.id_unico
                    LEFT JOIN   gp_pago            AS gpg ON gdp.pago            = gpg.id_unico
                    LEFT JOIN   gp_factura         AS gpf ON gdf.factura         = gpf.id_unico
                    LEFT JOIN   gf_tercero         AS gtr ON gpf.tercero         = gtr.id_unico
                    WHERE       gdf.concepto_tarifa = $concpto";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}