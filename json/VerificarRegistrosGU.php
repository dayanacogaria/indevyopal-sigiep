<?php 
#
require_once('../Conexion/conexion.php');
session_start(); 
$action= $_REQUEST['action'];

switch ($action){
    case(1):
        #**********Recepción Variables ****************#
        $org       = $_REQUEST['sltOrg'];
        $f_res       = $_REQUEST['fechaInicial'];
        $fecha_Res = DateTime::createFromFormat('d/m/Y', $f_res);
        $fecha_Res= $fecha_Res->format('Y/m/d');        
        $pl    = $_REQUEST['sltPlaca'];
        $comp    = $_REQUEST['sltComp'];
        //consulta de verificaicon
        $obj_resp=verificar_consulta($org, $fecha_Res, $pl, $comp);
        
    break;
    case(2):
        
    break;

}

function verificar_consulta($orgn, $fechar, $p,$cm){
    global $rps;
    $sql_inform="Select
            com.prefijo_resol,
            com.numero_resol,
            (DAY(com.fecha_resol)) as dia_resol,
            (MONTHNAME(com.fecha_resol)) as mes_resol,
            (YEAR(com.fecha_resol)) as ano_resol,
            com.comparendo,
            com.fecha_comparendo,
            concat(com.nombres,' ',com.apellidos) as conductor,
            com.cedula,
            com.infraccion as cod_infraccion,
            tc.nombre as nom_infraccion,
            tc.sigla_sancion,
            tc.sancion,
            tc.valor_sancion,
            org.nombre as nom_org
            from gu_comparendo com
            left join gu_tipo_comparendo tc on tc.codigo=com.infraccion
            left join gf_sucursal org on org.id_unico=com.sucursal 
            where com.sucursal='$orgn' and com.fecha_resol='$fechar' 
            and com.placa='$p' and com.comparendo='$cm'";
    
    $inform_resol = $mysqli->query($sql_inform);

    $res = mysqli_fetch_row($inform_resol);
    if(empty($res[0])){
        $rps=0;
    }else{
        $rps=1;
    }
        
    return $rps;
}
