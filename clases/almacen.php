<?php
require_once './Conexion/db.php';
class almacen{

    public $id_unico;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtenerAsociado($clase){
        try {
            $str = "SELECT DISTINCT id_unico, nombre, UPPER(sigla)
                    FROM            gf_tipo_movimiento
                    WHERE            clase = $clase";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTiposAsociado($id, $clase){
        try {
            $str = "SELECT  DISTINCT id_unico, nombre, UPPER(sigla)
                    FROM             gf_tipo_movimiento
                    WHERE            id_unico != $id
                    AND              clase     = $clase";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerInfoAso($idaso){
        try {
            $str = "SELECT DISTINCT mv.id_unico, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), tmv.nombre, tmv.sigla
                      FROM            gf_movimiento mv
                      LEFT JOIN       gf_detalle_movimiento dtm       ON  dtm.movimiento      = mv.id_unico
                      LEFT JOIN       gf_tipo_movimiento tmv          ON  mv.tipomovimiento   = tmv.id_unico
                      WHERE           mv.id_unico = $idaso";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataXDetalle($id){
        try {
            $str = "SELECT id_unico, (valor + iva) * cantidad FROM gf_detalle_movimiento WHERE movimiento = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoMovimiento($clase, $compania){
        try {
            $str = "SELECT DISTINCT tm.id_unico, UPPER(CONCAT_WS(' ',tm.nombre, UPPER(tm.sigla)))
                    FROM      gf_tipo_movimiento tm
                    LEFT JOIN gf_clase cl ON tm.clase = cl.id_unico
                    WHERE     tm.clase    = $clase
                    AND       tm.compania = $compania";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTiposDiferentes($tipo, $compania, $clase){
        try {
            $str = "SELECT DISTINCT tm.id_unico, UPPER(CONCAT_WS(' ',tm.nombre, UPPER(tm.sigla)))
                    FROM      gf_tipo_movimiento tm
                    LEFT JOIN gf_clase cl ON tm.clase = cl.id_unico
                    WHERE     tm.clase = $clase
                    AND       id_unico != $tipo
                    AND       compania = $compania";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCentroCosto($compania, $param){
        try {
            $str = "SELECT DISTINCT id_unico, UPPER(nombre)
                    FROM  gf_centro_costo
                    WHERE compania            = $compania
                    AND   parametrizacionanno = $param
                    ORDER BY nombre DESC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCentroCostoDiff($compania, $param, $id){
        try {
            $str = "SELECT DISTINCT id_unico, UPPER(nombre)
                    FROM  gf_centro_costo
                    WHERE compania            = $compania
                    AND   parametrizacionanno = $param
                    AND   id_unico           != $id
                    ORDER BY nombre DESC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerProyectos(){
        try {
            $str = "SELECT DISTINCT id_unico, UPPER(nombre) FROM gf_proyecto ORDER BY nombre DESC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerProyectosDiff($id){
        try {
            $str = "SELECT DISTINCT id_unico, UPPER(nombre) FROM gf_proyecto WHERE id_unico != $id ORDER BY nombre DESC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDependencia($compania, $tipo){
        try {
            $str = "SELECT DISTINCT id_unico, UPPER(CONCAT_WS(' ',nombre, sigla))
                    FROM   gf_dependencia
                    WHERE  compania        = $compania
                    AND    tipodependencia = $tipo";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDependenciaDiff($compania, $tipo, $id){
        try {
            $str = "SELECT DISTINCT id_unico, UPPER(CONCAT_WS(' ',nombre, sigla))
                    FROM   gf_dependencia
                    WHERE  compania        = $compania
                    AND    tipodependencia = $tipo
                    AND    id_unico       != $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerResponsablesDiff($id, $compania, $dependencia){
        try {
            $str = "SELECT DISTINCT  ter.id_unico,
                                 CONCAT_WS(' ',
                                     UPPER(
                                        IF(
                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                            ter.razonsocial,
                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                        )
                                     ),
                                     CONCAT_WS(' ', ti.nombre, ter.numeroidentificacion)
                                 )
                FROM             gf_dependencia_responsable AS dpr
                LEFT JOIN        gf_tercero                 AS ter ON dpr.responsable = ter.id_unico
                LEFT JOIN        gf_tipo_identificacion     AS ti  ON ti.id_unico     = ter.tipoidentificacion
                LEFT JOIN        gf_dependencia_responsable AS dtr ON dtr.responsable = ter.id_unico
                WHERE            ter.id_unico   != $id
                AND              ter.compania    = $compania
                AND              dtr.dependencia = $dependencia";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTerceros($compania){
        try {
            try {
                $str = "SELECT    ter.id_unico,
                                  UPPER(
                                    CONCAT_WS(' ',
                                      IF(
                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                        ter.razonsocial,
                                        CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)
                                      ),
                                      CONCAT_WS(' ',ti.nombre,ter.numeroidentificacion)
                                    )
                                  )
                        FROM      gf_tercero             AS ter
                        LEFT JOIN gf_tipo_identificacion AS ti  ON ti.id_unico  = ter.tipoidentificacion
                        LEFT JOIN gf_perfil_tercero      AS prt ON ter.id_unico = prt.tercero
                        WHERE     (prt.perfil BETWEEN 5 AND 6)
                        AND       ter.compania = $compania";
                $res = $this->mysqli->query($str);
                return $res->fetch_all(MYSQLI_NUM);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoDocSoporte(){
        try {
            $str = "SELECT id_unico, nombre FROM gf_tipo_documento_soporte_a ORDER BY nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoDocSoporteDiff($id){
        try {
            $str = "SELECT id_unico, nombre FROM gf_tipo_documento_soporte_a WHERE id_unico != $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovsBusqueda($compania, $param){
        try {
            $str = "SELECT      mov.id_unico, CONCAT_WS(' ',tpm.sigla, mov.numero, DATE_FORMAT(mov.fecha,'%d/%m/%Y')),
                                IF( CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                    ter.razonsocial,
                                    CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)
                                )
                    FROM        gf_movimiento mov
                    LEFT JOIN   gf_tipo_movimiento tpm  ON mov.tipomovimiento = tpm.id_unico
                    LEFT JOIN   gf_tercero ter          ON ter.id_unico = mov.tercero2
                    WHERE       tpm.clase IN (2)
                    AND         mov.compania = $compania
                    AND         mov.parametrizacionanno = $param";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataMov($id){
        try {
            $str = "SELECT    mv.id_unico, mv.tipomovimiento, UPPER(CONCAT_WS(' ', tpm.nombre, tpm.sigla)), mv.numero,
                              DATE_FORMAT(mv.fecha,'%d/%m/%Y'), mv.centrocosto, UPPER(cnc.nombre), mv.proyecto,
                              UPPER(pry.nombre), mv.dependencia, UPPER(CONCAT_WS(' ', dpc.nombre, dpc.sigla)),
                              mv.tercero,
                              (
                                IF(
                                  CONCAT_WS(' ' , trc.nombreuno, trc.nombredos, trc.apellidouno, trc.apellidodos) = '',
                                  trc.razonsocial,
                                  CONCAT_WS(' ' , trc.nombreuno, trc.nombredos, trc.apellidouno, trc.apellidodos)
                                )
                              ),
                              mv.descripcion,
                              mv.tercero2,
                              (
                                IF(
                                  CONCAT_WS(' ' , ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                  ter.razonsocial,
                                  CONCAT_WS(' ' , ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                )
                              ),
                              mv.porcivaglobal, mv.tipo_doc_sop, UPPER(tpd.nombre), mv.numero_doc_sop, mv.descuento
                    FROM      gf_movimiento               AS mv
                    LEFT JOIN gf_tipo_movimiento          AS tpm ON mv.tipomovimiento = tpm.id_unico
                    LEFT JOIN gf_centro_costo             AS cnc ON mv.centrocosto    = cnc.id_unico
                    LEFT JOIN gf_proyecto                 AS pry ON mv.proyecto       = pry.id_unico
                    LEFT JOIN gf_dependencia              AS dpc ON mv.dependencia    = dpc.id_unico
                    LEFT JOIN gf_tercero                  AS trc ON mv.tercero        = trc.id_unico
                    LEFT JOIN gf_tercero                  AS ter ON mv.tercero2       = ter.id_unico
                    LEFT JOIN gf_tipo_documento_soporte_a AS tpd ON mv.tipo_doc_sop   = tpd.id_unico
                    WHERE     md5(mv.id_unico) = '$id'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerParametroBasico($id){
        try {
            $xxx = 0;
            $str = "SELECT valor FROM gs_parametros_basicos WHERE id_unico = $id";
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

    public function obtenerPlanInventario($compania){
        try {
            $str = "SELECT   id_unico, UPPER(CONCAT_WS(' ',codi, nombre))
                    FROM     gf_plan_inventario
                    WHERE    tienemovimiento = 2
                    AND      compania        = $compania
                    AND      codi           != ' '
                    ORDER BY codi ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * GuardarMov
     *
     * Proceso para guardar movimiento de almacen
     *
     * @param int $tipo
     * @param string $numero
     * @param date   $fecha
     * @param int    $centrocosto
     * @param int    $proyecto
     * @param int    $dep
     * @param int    $responsable
     * @param int    $tercero
     * @param double $iva
     * @param int    $tipo_doc
     * @param string $num_doc
     * @param double $descuento
     * @return bool|mysqli_result|string
     */
    public function GuardarMov($tipo, $numero, $fecha, $centrocosto, $proyecto, $dep, $responsable, $tercero,
                               $iva, $tipo_doc, $num_doc, $descuento, $des, $compania, $param){
        try {
            echo $str = "INSERT INTO gf_movimiento(tipomovimiento, numero, fecha, centrocosto, proyecto, dependencia, tercero,
                                              tercero2, porcivaglobal, tipo_doc_sop, numero_doc_sop, descuento, descripcion,
                                              compania, parametrizacionanno, estado)
                          VALUES($tipo, $numero, '$fecha', $centrocosto, $proyecto, $dep, $responsable, $tercero, $iva,
                          $tipo_doc, '$num_doc', $descuento, '$des', $compania, $param, 2)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getData($id_unico){
        try {
            $str = "SELECT    dtm.id_unico
                    FROM      gf_detalle_movimiento as dtm
                    LEFT JOIN gf_plan_inventario    as pln ON dtm.planmovimiento = pln.id_unico
                    WHERE     dtm.movimiento      = $id_unico
                    AND       pln.tipoinventario != 5";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function get_values_detail($id_unico){
        try {
            $values = array();
            $str    = "SELECT id_unico, planmovimiento, cantidad, valor, iva, movimiento FROM gf_detalle_movimiento WHERE id_unico = $id_unico";
            $res    = $this->mysqli->query($str);
            if($res->num_rows > 0) {
                $row       = $res->fetch_row();
                $values[0] = $row[0]; #Id del Detalle
                $values[1] = $row[1]; #Id de plan inventario
                $values[2] = $row[2]; #Cantidad de elementos
                $values[3] = $row[3]; #Valor del detalle
                $values[4] = $row[4]; #Valor iva del detalle
                $values[5] = $row[5]; #Id del movimiento
            }
            return $values;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtnerDataAsociado($aso){
        try {
            $str = "SELECT cantidad FROM gf_detalle_movimiento WHERE detalleasociado = $aso";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function save_detail_mov($planI, $cantidad, $valor, $valorIva , $movimiento, $afectado = 'NULL') {
        try {
            date_default_timezone_set('America/Bogota');
            $hora = date('H:i:s');
            $str  = "INSERT INTO gf_detalle_movimiento(planmovimiento, cantidad, valor, iva, movimiento, detalleasociado, hora)
                                          VALUES($planI, $cantidad, $valor, $valorIva, $movimiento, $afectado, '$hora');";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUnidadesElemento($elemento){
        try {
            $str = "SELECT    gct.id_unico, gpl.codi, UPPER(gpl.nombre), UPPER(gun.nombre), gct.porcentajeI
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gp_concepto        AS gcn ON gct.concepto        = gcn.id_unico
                    LEFT JOIN gf_elemento_unidad AS gel ON gct.elemento_unidad = gel.id_unico
                    LEFT JOIN gf_unidad_factor   AS gun ON gel.unidad_empaque  = gun.id_unico
                    LEFT JOIN gf_plan_inventario AS gpl ON gcn.plan_inventario = gpl.id_unico
                    WHERE     gcn.plan_inventario IN ($elemento)";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ActualizarPorcentajeIncremento($id, $x){
        try {
            $str = "UPDATE gp_concepto_tarifa SET porcentajeI = $x WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTarifasConceptoElemento($id){
        try {
            $str = "SELECT    gtr.id_unico, gtr.valor, gct.porcentajeI, gel.valor_conversion, gct.id_unico
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gp_concepto        AS gcn ON gct.concepto        = gcn.id_unico
                    LEFT JOIN gp_tarifa          AS gtr ON gct.tarifa          = gtr.id_unico
                    LEFT JOIN gf_elemento_unidad AS gel ON gct.elemento_unidad = gel.id_unico
                    WHERE     gcn.plan_inventario = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerRelacionHijosUnidad($padre, $unidad){
        try {
            $str = "SELECT    gpa.plan_hijo, gpa.cantidad
                    FROM      gf_plan_inventario_asociado AS gpa
                    LEFT JOIN gp_concepto_tarifa          AS gct ON gpa.tarifa          = gct.id_unico
                    LEFT JOIN gf_elemento_unidad          AS gel ON gct.elemento_unidad = gel.id_unico
                    WHERE     gpa.plan_padre     = $padre
                    AND       gel.unidad_empaque = $unidad";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
