<?php
require_once ('./Conexion/db.php');

class inventario{

    private $mysqli;

    public $id_unico;

    public function getIdUnico(){
        return $this->id_unico;
    }

    public function setIdUnico($id_unico){
        $this->id_unico = $id_unico;
    }

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtnerPredecesor(){
        try {
            $sql = "SELECT gfpi.id_unico, CONCAT(gfpi.codi,' ', gfpi.nombre) plan FROM gf_plan_inventario gfpi
                    LEFT JOIN gf_plan_inventario pi ON gfpi.predecesor = pi.id_unico WHERE gfpi.tienemovimiento = 1
                    ORDER BY gfpi.codi ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoInventario(){
        try {
            $sql = "SELECT id_unico, nombre FROM gf_tipo_inventario ORDER BY nombre ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUnidadFactor(){
        try {
            $sql = "SELECT id_unico, nombre FROM gf_unidad_factor ORDER BY nombre ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoActivo(){
        try {
            $sql = "SELECT id_unico, nombre FROM gf_tipo_activo ORDER BY nombre ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterFicha(){
        try {
            $sql = "SELECT id_unico, descripcion FROM gf_ficha ORDER BY descripcion ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerPadre(){
        try {
            $sql = "SELECT id_unico,CONCAT(codi,' ',nombre) FROM gf_plan_inventario ORDER BY id_unico ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterPadreId($id_unico){
        try {
            $sql = "SELECT id_unico, CONCAT(codi,' ',nombre), codi, predecesor FROM gf_plan_inventario WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCantidadHijos($padre){
        try {
            $xxx = 0;
            $sql = "SELECT count(id_unico) FROM gf_plan_inventario WHERE predecesor = $padre";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUltimoCodigoHijo($padre){
        try {
            $sql = "SELECT (id_unico) FROM gf_plan_inventario WHERE predecesor = $padre order by codi desc limit 1";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row[0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoInventarioId($id_unico){
        try {
            $sql = "SELECT id_unico, nombre FROM gf_tipo_inventario WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUnidadFactorId($id_unico, $plan){
        try {
            $sql = "SELECT    gct.id_unico, gtr.valor, gtr.porcentaje_iva, gtr.porcentaje_impoconsumo, guf.id_unico, guf.nombre
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gf_elemento_unidad AS gun ON gct.elemento_unidad = gun.id_unico
                    LEFT JOIN gf_unidad_factor   AS guf ON gun.unidad_empaque  = guf.id_unico
                    LEFT JOIN gp_concepto        AS gcn ON gct.concepto        = gcn.id_unico
                    LEFT JOIN gp_tarifa          AS gtr ON gct.tarifa          = gtr.id_unico
                    WHERE     guf.id_unico        = $id_unico
                    AND       gcn.plan_inventario = $plan";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoActivoId($id_unico){
        try {
            $sql = "SELECT id_unico, nombre FROM gf_tipo_activo WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoInventarioDiferentes($id_unico){
        try {
            $sql = "SELECT id_unico, nombre FROM gf_tipo_inventario WHERE id_unico != $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUnidadFactorDiferentes($id_unico){
        try {
            $sql = "SELECT id_unico, nombre FROM gf_unidad_factor WHERE id_unico != $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoActivoDiferentes($id_unico){
        try {
            $sql = "SELECT id_unico, nombre FROM gf_tipo_activo WHERE id_unico != $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDatosPlan($id_unico){
        try {
            $sql = "SELECT id_unico, nombre, codi, tienemovimiento, compania, tipoinventario, unidad, predecesor, tipoactivo, ficha
                    FROM   gf_plan_inventario
                    WHERE  id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtner_hijos($codigo){
        try {
            $sql = "SELECT id_unico FROM gf_plan_inventario WHERE codi LIKE '$codigo%' ";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function actualizar_tipo_inv($id_unico, $tipo){
        try {
            $sql = "UPDATE gf_plan_inventario SET tipoactivo = $tipo WHERE id_unico = $id_unico";
            return $this->mysqli->query($sql);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerCodigo($id){
        $xxx = 0;
        $str = "SELECT codi FROM gf_plan_inventario WHERE id_unico = $id";
        $res = $this->mysqli->query($str);
        if($res->num_rows > 0){
            $row = $res->fetch_row();
            $xxx = $row[0];
        }
        return $xxx;
    }

    public function buscarElementos($codBarras){
        try {
            $str = "SELECT    DISTINCT gpl.id_unico, gpl.codi, UPPER(gct.nombre), gpl.unidad
                    FROM      gp_concepto AS gct
                    LEFT JOIN gf_plan_inventario AS gpl ON gct.plan_inventario = gpl.id_unico
                    WHERE    (
                              gpl.codigo_barras LIKE '%$codBarras%'
                    OR        gpl.nombre        LIKE '%$codBarras%'
                    OR        gpl.codi          LIKE '%$codBarras%'
                    )
                    AND      xFactura = 1 LIMIT 20";
            $res = $this->mysqli->query($str);
            mysqli_close($this->mysqli);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerIdConcepto($id_unico){
        try {
            $xxx = 0;
            $str = "SELECT id_unico FROM gp_concepto WHERE plan_inventario = $id_unico";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerIdElemento($codi){
        try {
            $xxx = 0;
            $str = "SELECT id_unico FROM gf_plan_inventario WHERE codigo_barras = '$codi'";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorProducto($plan, $unidad){
        try {
            $xxx = 0;
            $str = "SELECT    gtr.valor
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gp_concepto        AS gcn ON gct.concepto        = gcn.id_unico
                    LEFT JOIN gf_elemento_unidad AS geu ON gct.elemento_unidad = geu.id_unico
                    LEFT JOIN gp_tarifa          AS gtr ON gct.tarifa          = gtr.id_unico
                    WHERE     gcn.plan_inventario = $plan
                    AND       geu.unidad_empaque  = $unidad";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerConceptoFactura($plan){
        try {
            $xxx = 0;
            $str = "SELECT id_unico FROM gp_concepto WHERE md5(plan_inventario)= '$plan'";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtener(){
        try {
            $str = "SELECT nombre, id_unico FROM gf_plan_inventario WHERE md5(id_unico) = '$this->id_unico'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerUnidadF(){
        try {
            $str = "SELECT id_unico, nombre FROM gf_unidad_factor ORDER BY nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUnidadId($id){
        try {
            $xxx = "";
            $str = "SELECT nombre FROM gf_unidad_factor WHERE id_unico = $id";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetalleAnteriorEntrada($plan, $unidad){
        try {
            $str = "SELECT    gdm.id_unico, gpp.precio_act, gdm.unidad_origen
                    FROM      gf_precio_producto AS gpp
                    LEFT JOIN gf_detalle_movimiento AS gdm ON gpp.detalle_mov = gdm.id_unico
                    WHERE     gdm.planmovimiento = $plan
                    AND       gpp.unidad         = $unidad
                    ORDER BY  gpp.id_unico DESC
                    LIMIT     1, 1";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerElementosDiferenteId($id){
        try {
            $str = "SELECT id_unico, CONCAT_WS(' ', codi, UPPER(nombre)) FROM gf_plan_inventario WHERE id_unico != $id ORDER BY codi ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoElementos(){
        try {
            $str = "SELECT id_unico, CONCAT_WS(' ', codi, UPPER(nombre)) FROM gf_plan_inventario ORDER BY codi ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataAsociados($padre, $tarifa){
        try {
            $str = "SELECT    gpa.id_unico, CONCAT_WS(' ',gpl.codi, UPPER(gpl.nombre)), gpa.cantidad
                    FROM      gf_plan_inventario_asociado AS gpa
                    LEFT JOIN gf_plan_inventario          AS gpl ON gpa.plan_hijo = gpl.id_unico
                    WHERE     gpa.plan_padre = $padre
                    AND       gpa.tarifa     = $tarifa";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function guardarRelacionData($padre, $hijo, $tarifa, $cantidad){
        try {
            $str = "INSERT INTO gf_plan_inventario_asociado(plan_padre, plan_hijo, tarifa, cantidad) VALUES($padre, $hijo, $tarifa, $cantidad)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarRelacion($id){
        try {
            $str = "DELETE FROM gf_plan_inventario_asociado WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}