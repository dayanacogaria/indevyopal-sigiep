<?php

function cierre($id){
    @session_start();
    $anno = $_SESSION['anno'];
    $fc = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = $id";
    $fc = $GLOBALS['mysqli']->query($fc);
    $fc = mysqli_fetch_row($fc);
    $fc = $fc[0];
    ##DIVIDIR FECHA
    $fecha_div = explode("-", $fc);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];

    ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
    $ci="SELECT
    cp.id_unico
    FROM 
    gs_cierre_periodo cp
    LEFT JOIN
    gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
    LEFT JOIN
    gf_mes m ON cp.mes = m.id_unico
    WHERE
    pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2 
    AND cp.anno = $anno";
    $ci =$GLOBALS['mysqli']->query($ci);
    if(mysqli_num_rows($ci)>0){ 
        $resultado=1;
    } else {
        $resultado=0;
    }
    return $resultado;
}

function cierrecnt($id){
    @session_start();
    $anno = $_SESSION['anno'];
    $fc = "SELECT fecha FROM gf_comprobante_cnt WHERE id_unico = $id";
    $fc = $GLOBALS['mysqli']->query($fc);
    $fc = mysqli_fetch_row($fc);
    $fc = $fc[0];
    ##DIVIDIR FECHA
    $fecha_div = explode("-", $fc);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];

    ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
    $ci="SELECT
    cp.id_unico
    FROM 
    gs_cierre_periodo cp
    LEFT JOIN
    gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
    LEFT JOIN
    gf_mes m ON cp.mes = m.id_unico
    WHERE
    pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2 
    AND cp.anno = $anno";
    $ci =$GLOBALS['mysqli']->query($ci);
    if(mysqli_num_rows($ci)>0){ 
        $resultado=1;
    } else {
        $resultado=0;
    }
    return $resultado;
}
function cierrepartida($id){
    @session_start();
    $anno = $_SESSION['anno'];
    $fc = "SELECT m.id_unico, m.parametrizacionanno FROM gf_partida_conciliatoria pc "
            . " LEFT JOIN gf_mes m ON m.id_unico = pc.mes WHERE pc.id_unico = $id";
    $fc = $GLOBALS['mysqli']->query($fc);
    $fc = mysqli_fetch_row($fc);
    $anio = $fc[1];
    $mes = $fc[0];
    
    ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
    $ci="SELECT
    cp.id_unico
    FROM 
    gs_cierre_periodo cp 
    WHERE
    cp.anno = '$anio' AND cp.mes = '$mes' AND cp.estado =2 
    AND cp.anno = $anno";
    $ci =$GLOBALS['mysqli']->query($ci);
    if(mysqli_num_rows($ci)>0){ 
        $resultado=1;
    } else {
        $resultado=0;
    }
    return $resultado;
}
function cierrepartidames($id){
    @session_start();
    $anno = $_SESSION['anno'];
    $fc = "SELECT m.id_unico, m.parametrizacionanno FROM gf_mes m  WHERE m.id_unico = $id";
    $fc = $GLOBALS['mysqli']->query($fc);
    $fc = mysqli_fetch_row($fc);
    $anio = $fc[1];
    $mes = $fc[0];
    
    ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
    $ci="SELECT
    cp.id_unico
    FROM 
    gs_cierre_periodo cp 
    WHERE
    cp.anno = '$anio' AND cp.mes = '$mes' AND cp.estado =2 
    AND cp.anno = $anno";
    $ci =$GLOBALS['mysqli']->query($ci);
    if(mysqli_num_rows($ci)>0){ 
        $resultado=1;
    } else {
        $resultado=0;
    }
    return $resultado;
}

function conciliadocnt($id){
    $fc = "SELECT id_unico 
        FROM gf_detalle_comprobante 
        WHERE conciliado = 1 AND comprobante = $id";
    $fc  = $GLOBALS['mysqli']->query($fc);
    $rta = 0;
    if(mysqli_num_rows($fc)>0){ 
        $rta = 1;
    }
    return $rta;
    
}