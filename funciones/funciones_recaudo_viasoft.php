<?php
/**
 * Created by Alexander.
 * User: Alexander Numpaque
 * Date: 02/06/2017
 * Time: 12:30 PM
 */

/**
 * Función para obtener el id de concepto rubro
 * get_concept_rb
 * @author Alexander Numpaque
 * @package Recaudo
 * @param int $concept Id de concepto
 * @return int $conc_r Id de concepto rubro
 */
function get_concept_rbo ($concept) {
    require ('../Conexion/conexion.php');
    $conc_r = 0;
    $sql = "SELECT id_unico FROM gf_concepto_rubro WHERE concepto = $concept";
    $result = $mysqli->query($sql);
    $rows = mysqli_num_rows($result);
    if($rows > 0) {
        $row = mysqli_fetch_row($result);
        $conc_r = $row[0];
    }
    return $conc_r;
}

/**
 * get_rubro
 * Función para obtener el rubro relacioando a concepto rubro
 * @author Alexader Numpaque
 * @package Recaudo
 * @param $concept_rb Id de concepto rubro
 * @return int $rubro Id de rubro
 */
function get_rubro ($concept_rb) {
    require ('../Conexion/conexion.php');
    @session_start();
    $_param = $_SESSION['anno'];
    $rubro  = 0;
    $sql = "SELECT    crb.rubro
            FROM      gf_concepto_rubro as crb
            LEFT JOIN gf_rubro_pptal    as rbro ON crb.rubro = rbro.id_unico
            WHERE     crb.id_unico             = $concept_rb
            AND       rbro.parametrizacionanno = $_param";
    $result = $mysqli->query($sql);
    $rows = mysqli_num_rows($result);
    if($rows > 0) {
        $row = mysqli_fetch_row($result);
        $rubro = $row[0];
    }
    return $rubro;
}

/**
 * get_rubro_fuente
 * Función para obtener el id de rubro fuente
 * @param $rubro Id de rubro
 * @return int Id de rubro fuente
 */
function get_rubro_fuente_2 ($rubro) {
    require ('../Conexion/conexion.php');
    @session_start();
    $_param = $_SESSION['anno'];
    $rb = 0;
    $sql = "SELECT    rbf.id_unico
            FROM      gf_rubro_fuente as rbf
            LEFT JOIN gf_rubro_pptal  as rbro ON rbf.rubro = rbro.id_unico
            WHERE     rbf.rubro                = $rubro
            AND       rbro.parametrizacionanno = $_param";
    $result = $mysqli->query($sql);
    $rows = mysqli_num_rows($result);
    if($rows > 0) {
        $row = mysqli_fetch_row($result);
        $rb = $row[0];
    }
    return $rb;
}

/**
 * get_concept_v
 * Función para obtener el id del concepto que se relaciona a la tabla de configuración de viasoft
 * @param int $codigo Valor del archivo plano
 * @return int Id del concepto
 */
function get_concept_v($codigo) {
    require ('../Conexion/conexion.php');
    $concepto = array();
    @session_start();
    $_param = $_SESSION['anno'];
    $sql    = "SELECT id_unico, id_concepto FROM gs_configuracion_viasoft WHERE codigo_viasoft = $codigo AND parametrizacionanno = $_param";
    $result = $mysqli->query($sql);
    $rows   = mysqli_num_rows($result);
    if($rows > 0) {
        while ($row = mysqli_fetch_row($result)) {
            $concepto[] = $row[0].",".$row[1];
        }
    }
    return $concepto;
}

/**
 * validate_number
 * Función para validar el numero donde
 * @author Alexander Numpaque
 * @param $param id de parametrización anno
 * @param $number Numero a validar
 * @return string
 */
function validate_number ($number, $param) {
    $anno = get_anno_param($param);
    return $number == ''||$number == 0?$anno.'000001':$number+1;
}

/**
 * get_anno_param
 * Función para obtener el año de la parametrización año
 * @param $param
 * @return string
 */
function get_anno_param ($param) {
    require ('../Conexion/conexion.php');
    $anno = "";
    $sql = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $param";
    $result = $mysqli->query($sql);
    $rows = mysqli_num_rows($result);
    if($rows > 0) {
        $row = mysqli_fetch_row($result);
        $anno = $row[0];
    }
    return $anno;
}

/**
 * get_acounts
 * Función para obtener id de cuenta débito y cuenta crédito
 * @param $con_rubro Id de concepto rubro
 * @return string String con Id de cuenta débito y cuenta crédito
 */
function get_acounts ($con_rubro) {
    require ('../Conexion/conexion.php');
    @session_start();
    $param  = $_SESSION['anno'];
    $values = "";
    $sql = "SELECT    ctrb.cuenta_debito, ctrb.cuenta_credito
            FROM      gf_concepto_rubro_cuenta  as ctrb
            LEFT JOIN gf_cuenta as deb ON ctrb.cuenta_debito  = deb.id_unico
            LEFT JOIN gf_cuenta as cre ON ctrb.cuenta_credito = cre.id_unico
            WHERE     ctrb.concepto_rubro     = $con_rubro
            AND       deb.parametrizacionanno = $param
            AND       cre.parametrizacionanno = $param";
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_row($result);
        $values = $row[0].','.$row[1];
    }
    return $values;
}

/**
 * get_porcent
 * Función para obtener el porcentaje relacionado al codigo
 * @param int $codigo Id de codigo
 * @param int $id_cgv Id unico de id de configuración
 * @return string $porcent porcentaje a aplicar
 */
function get_porcent ($codigo, $id_cgv) {
    require ('../Conexion/conexion.php');
    @session_start();
    $param   = $_SESSION['anno'];
    $porcent = "";
    $sql = "SELECT porcentaje FROM gs_configuracion_viasoft WHERE codigo_viasoft = $codigo AND id_unico = $id_cgv AND parametrizacionanno = $param";
    $result = $mysqli->query($sql);
    $rows = mysqli_num_rows($result);
    if($rows > 0) {
        $row = mysqli_fetch_row($result);
        $porcent = $row[0];
    }
    return $porcent;
}

/**
 * calcule_porcent
 * Función para aplicar el porcentaje a un valor
 * @param $value Valor a aplicar el porcentaje
 * @param $porcent Porcentaje obtenido y relacionado al codigo
 * @return float|int Operación con el porcentaje aplicado
 */
function calcule_porcent ($value, $porcent) {
    return ($value * $porcent)/100;
}

/**
 * get_nat_acount
 * Función para obtener la naturaleza de la cuenta
 * @param int $acount Id de la cuenta
 * @return int $nat Valor de la naturaleza
 **/
function get_nat_acount ($acount) {
    require ('../Conexion/conexion.php');
    $nat = 0;
    $sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $acount";
    $result = $mysqli->query($sql);
    $rows = mysqli_num_rows($result);
    if($rows > 0) {
        $row = mysqli_fetch_row($result);
        $nat = $row[0];
    }
    return $nat;
}

/**
 * get_acount_cr
 * Función para obtener las cuenta crédito apartir de la cuenta debito
 * @param int $debito Id de la cuenta debito
 * @return int $cta_cre Id de la cuenta credito
 */
function get_acount_cr ($debito, $concepto_rubro) {
    require ('../Conexion/conexion.php');
    @session_start();
    $param  = $_SESSION['anno'];
    $cta_cre = 0;
    $sql = "SELECT    crbt.cuenta_credito
            FROM      gf_concepto_rubro_cuenta as crbt
            LEFT JOIN gf_cuenta as deb ON crbt.cuenta_debito  = deb.id_unico
            LEFT JOIN gf_cuenta as cre ON crbt.cuenta_credito = cre.id_unico
            WHERE     crbt.cuenta_debito      = $debito
            AND       crbt.concepto_rubro     = $concepto_rubro
            AND       deb.parametrizacionanno = $param
            AND       cre.parametrizacionanno = $param";
    $result = $mysqli->query($sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_row($result);
        $cta_cre = $row[0];
    }
    return $cta_cre;
}