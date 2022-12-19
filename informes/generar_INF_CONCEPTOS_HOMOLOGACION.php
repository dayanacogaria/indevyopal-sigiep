<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Conceptos_Homologacion.xls");
require_once("../Conexion/conexion.php");
require_once("../Conexion/ConexionPDO.php");
session_start();
ini_set('max_execution_time', 0);
$anno = $_SESSION['anno'];
$con = new ConexionPDO();
#***********************Datos Compañia***********************#
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 
            ter.id_unico,
            ter.razonsocial,
            UPPER(ti.nombre),
            IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
            dir.direccion,
            tel.valor,
            ter.ruta_logo 
        FROM            
            gf_tercero ter
        LEFT JOIN 	
            gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
        LEFT JOIN       
            gf_direccion dir ON dir.tercero = ter.id_unico
        LEFT JOIN 	
            gf_telefono  tel ON tel.tercero = ter.id_unico
        WHERE 
            ter.id_unico = $compania");

$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];

$compania    = $_SESSION['compania'];
$usuario     = $_SESSION['usuario'];

#* Consulta *¨#
$sqlemp = " SELECT DISTINCT  
        cnf.id_unico, CONCAT(cn.codigo,' - ',LOWER(cn.descripcion)),
        LOWER(c.nombre),
        CONCAT( rp.codi_presupuesto,' ',LOWER(rp.nombre), ' - ', LOWER(f.nombre)),
        LOWER(gg.nombre),
        IF(CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) 
            IS NULL OR CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) = '',
            (tr.razonsocial),
            CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)) AS NOMBRE, 
            IF(tr.digitoverficacion IS NULL OR tr.digitoverficacion='',
                tr.numeroidentificacion, 
            CONCAT(tr.numeroidentificacion, ' - ', tr.digitoverficacion)), 
            tr.digitoverficacion, 
            cd.codi_cuenta, LOWER(cd.nombre), 
            cc.codi_cuenta, LOWER(cc.nombre) 
        FROM gn_concepto_nomina_financiero cnf
        LEFT JOIN gn_concepto cn ON cnf.concepto_nomina = cn.id_unico
        LEFT JOIN gf_concepto_rubro cf ON cnf.concepto_financiero = cf.id_unico 
        LEFT JOIN gf_concepto_rubro_cuenta crc ON crc.concepto_rubro = cf.id_unico 
        LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico 
        LEFT JOIN gf_cuenta cc ON cc.id_unico = crc.cuenta_credito 
        LEFT JOIN gf_concepto c ON cf.concepto = c.id_unico
        LEFT JOIN gf_rubro_fuente rf ON rf.id_unico = cnf.rubro_fuente 
        LEFT JOIN gf_rubro_pptal rp ON cf.rubro = rp.id_unico
        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico
        LEFT JOIN gn_grupo_gestion gg ON cnf.grupo_gestion = gg.id_unico
        LEFT JOIN gf_tercero tr ON cnf.tercero = tr.id_unico 
        WHERE cnf.parametrizacionanno = $anno 
        ORDER BY cnf.grupo_gestion";
$emp = $mysqli->query($sqlemp);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Informe Homologaciones Financiera Nómina</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="7" align="center"><strong>
                <br/>&nbsp;
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;
                <br/>HOMOLOGACIÓN FINANCIERA NÓMINA         
                <br/>&nbsp;                 
                </strong>
            </th>
                <tr></tr>
                <tr>
                    <th>Grupo de Gestión</th>
                    <th>Concepto Nómina</th>
                    <th>Concepto Financiero</th>
                    <th>Rubro Presupuestal</th>
                    <th>Cuenta Débito</th>
                    <th>Cuenta Crédito</th>
                    <th>Tercero</th>
                </tr
            <tbody>
            <?php 
            while ($Cemp = mysqli_fetch_row($emp)) {
                echo '<tr>';
                echo '<td>'.ucwords($Cemp[4]).'</td>';
                echo '<td>'.ucwords($Cemp[1]).'</td>';
                echo '<td>'.ucwords($Cemp[2]).'</td>';
                echo '<td>'.ucwords($Cemp[3]).'</td>';
                echo '<td>'.$Cemp[8].' - '.ucwords($Cemp[9]).'</td>';
                echo '<td>'.$Cemp[10].' - '.ucwords($Cemp[11]).'</td>';
                echo '<td>'.ucwords(mb_strtolower($Cemp[5])).' - '.$Cemp[6].'</td>';
                echo '</tr>';
            } ?>       
            </tbody>  
        </table>
    </body>
</html>