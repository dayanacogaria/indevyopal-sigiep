<?php
/**
 * Modelo de baja de productos
 */
require_once ('./Conexion/db.php');
class baja{
    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtnerDep(){
        try {
            $sql = "SELECT dep.id_unico, UPPER(dep.sigla), dep.nombre FROM gf_dependencia dep WHERE dep.tipodependencia = 1";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDepId($id_unico){
        try {
            $sql = "SELECT dep.id_unico, UPPER(dep.sigla), dep.nombre FROM gf_dependencia dep WHERE md5(dep.id_unico) = '$id_unico'";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDepDif($id_unico){
        try {
            $sql = "SELECT dep.id_unico, UPPER(dep.sigla), dep.nombre FROM gf_dependencia dep WHERE md5(dep.id_unico) != '$id_unico' AND tipodependencia = 1";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerProductos(){
        try {
            $sql = "SELECT DISTINCT mpr.producto
                    FROM            gf_movimiento_producto mpr
                    LEFT JOIN       gf_producto pro ON mpr.producto = pro.id_unico
                    WHERE           pro.baja IS NULL OR pro.baja = 0";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDetallesR($producto){
        try {
            $sql = "SELECT MAX(detallemovimiento) FROM gf_movimiento_producto WHERE producto = $producto";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerProductosDetalleDependencia($detalle, $producto, $dep){
        try {
            $sql =" SELECT    dtm.id_unico  'ID',       pes.valor   'SERIE',  dtm.valor     'VALOR',    pro.descripcion 'DESCRIPCION',
                              tpm.sigla     'TIPO_MOV', mov.numero  'N_MOV',  pro.id_unico  'PRODUCTO', fch.descripcion 'NFICHA',
                              plan.nombre nom_pro, plan.codi as plnc
                    FROM      gf_detalle_movimiento dtm
                    LEFT JOIN gf_movimiento_producto mop      ON dtm.id_unico         = mop.detallemovimiento
                    LEFT JOIN gf_movimiento mov               ON mov.id_unico         = dtm.movimiento
                    LEFT JOIN gf_producto pro                 ON pro.id_unico         = mop.producto
                    LEFT JOIN gf_producto_especificacion pes  ON pro.id_unico         = pes.producto
                    LEFT JOIN gf_ficha_inventario fin         ON pes.fichainventario  = fin.id_unico
                    LEFT JOIN gf_elemento_ficha elm           ON fin.elementoficha    = elm.id_unico
                    LEFT JOIN gf_tipo_movimiento tpm          ON mov.tipomovimiento   = tpm.id_unico
                    LEFT JOIN gf_ficha fch                    ON fin.ficha            = fch.id_unico
                    LEFT JOIN gf_plan_inventario plan         ON dtm.planmovimiento   = plan.id_unico
                    WHERE     dtm.id_unico          = $detalle
                    AND       pro.id_unico          = $producto
                    AND       fin.elementoficha     = 6
                    AND       md5(mov.dependencia)  = '$dep'";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function tiposBaja($compania){
        try {
            $sql = "SELECT tpm.id_unico, UPPER(tpm.sigla), tpm.nombre FROM gf_tipo_movimiento tpm WHERE tpm.clase = 7 AND tpm.compania = $compania";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerResponsableDepdencia($dependencia){
        try {
            $xxx = 0;
            $sql = "SELECT responsable FROM gf_dependencia_responsable WHERE dependencia = $dependencia";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificarBaja($producto, $baja){
        try {
            $sql = "UPDATE gf_producto SET baja = $baja WHERE id_unico = $producto";
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

    public function obtenerResponsablesDepdencia($dependencia){
        try {
            $xxx = 0;
            $sql = "SELECT ter.id_unico as id_unico,
            IF(concat_ws(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '', ter.razonsocial, concat_ws(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) as nombre
            FROM gf_dependencia_responsable  dep
            LEFT JOIN gf_tercero ter ON dep.responsable = ter.id_unico
            WHERE md5(dep.dependencia) = '$dependencia'";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_ASSOC);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function generarConsecutivo($tipo, $param, $compania){
        try {
            $xxx = 0;
            $sql = "SELECT MAX(numero) FROM gf_movimiento WHERE tipomovimiento = $tipo AND parametrizacionanno = $param AND compania = $compania ORDER BY id_unico DESC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            if(empty($row[0])){
                $xxx = date('Y').'000001';
            }else{
                $xxx = $row[0] + 1;
            }

            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterMovsB($compania, $param){
        try {
            $sql = "SELECT      mov.id_unico,CONCAT(tpm.sigla,' ',mov.numero,' ',DATE_FORMAT(mov.fecha,'%d/%m/%Y')),
                                IF( CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,(ter.razonsocial),
                                    CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE'
                    FROM        gf_movimiento mov
                    LEFT JOIN   gf_tipo_movimiento tpm  ON mov.tipomovimiento = tpm.id_unico
                    LEFT JOIN   gf_tercero ter          ON ter.id_unico = mov.tercero2
                    WHERE       tpm.clase IN (7)
                    AND         mov.compania = $compania
                    AND         mov.parametrizacionanno = $param
                    ORDER BY    mov.tipomovimiento, mov.numero DESC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function valorAcumMov($mov){
        try {
            $xxx = 0;
            $sql = "SELECT (dtm.valor) FROM gf_detalle_movimiento dtm WHERE dtm.movimiento = $mov";
            $res = $this->mysqli->query($sql);
            while ($row_r = $res->fetch_row()) {
                $xxx += $row_r[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerBaja($id_unico){
        try {
            $sql = "SELECT mov.id_unico,
                           date_format(mov.fecha, '%d/%m/%Y') as fecha,
                           mov.numero as  numero,
                           mov.dependencia,
                           tpm.sigla,
                           tpm.nombre as tpnom,
                           dep.sigla as cdep,
                           dep.nombre as depnom,
                           IF(
                              concat_ws(' ', ter.nombreuno, ter.nombredos, apellidouno, apellidodos) = ' ',
                              ter.razonsocial,
                              concat_ws(' ', ter.nombreuno, ter.nombredos, apellidouno, apellidodos)
                           ) as ternom,
                           IF(
                              ter.digitoverficacion = ' ',
                              ter.numeroidentificacion,
                              concat_ws(' - ', ter.numeroidentificacion, ter.digitoverficacion)
                           ) as doc
                    FROM gf_movimiento as mov
                    LEFT JOIN gf_tipo_movimiento tpm ON mov.tipomovimiento = tpm.id_unico
                    LEFT JOIN gf_dependencia     dep ON mov.dependencia    = dep.id_unico
                    LEFT JOIN gf_tercero         ter ON mov.tercero        = ter.id_unico
                    WHERE md5(mov.id_unico) = '$id_unico'";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_assoc();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDetallesBaja($mov){
        try {
            $sql = "SELECT    dtm.id_unico, dtm.valor, concat_ws(' ',pln.codi, pln.nombre) as producto, pro.descripcion, pEs.valor, pro.id_unico
                    FROM      gf_detalle_movimiento as dtm
                    LEFT JOIN gf_plan_inventario pln         ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento_producto mpr     ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_producto            pro     ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_producto_especificacion pEs ON pEs.producto      = pro.id_unico
                    WHERE     md5(dtm.movimiento) = '$mov' AND pEs.fichainventario = 6";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminarRelMovP($detalle, $producto){
        try {
            $sql = "DELETE FROM gf_movimiento_producto WHERE detallemovimiento = $detalle AND producto = $producto";
            $res = $this->mysqli->query($sql);
            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }
            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico){
        try {
            $sql = "DELETE FROM gf_detalle_movimiento WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }
            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function cambiarEstado($producto){
        try {
            $sql = "UPDATE gf_producto SET baja = 0 WHERE id_unico = $producto";
            $res = $this->mysqli->query($sql);
            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }
            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterResDep($id_unico){
        try {
            $sql = "SELECT IF(
                                concat_ws(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                ter.razonsocial,
                                concat_ws(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                            ) as nombre,
                            IF(
                              ter.digitoverficacion = ' ',
                              ter.numeroidentificacion,
                              concat_ws(' - ', ter.numeroidentificacion, ter.digitoverficacion)
                           ) as doc
                    FROM gf_tercero ter
                    WHERE ter.id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}