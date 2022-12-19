<?php   
session_start();
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
$compania = $_SESSION['compania'];
$cantidad = $_GET['cantidad'];
$con = new ConexionPDO();
$detalleMovimiento  = $_GET['movimiento'];
$valorU             = '"'.$mysqli->real_escape_string(''.$_GET["txtValor"].'').'"';
$descripcion        = '"'.$mysqli->real_escape_string(''.$_GET["txtDescripcion"].'').'"';
$panno = $_SESSION['anno'];
#* Datos Movimiento 
$dm = $con->Listar("SELECT DISTINCT m.fecha, dm.planmovimiento 
        FROM gf_detalle_movimiento dm 
        LEFT JOIN gf_movimiento m ON m.id_unico = dm.movimiento 
        WHERE dm.id_unico = $detalleMovimiento");

#* Buscar los parámetros de anno segun el valor
$dp = $con->Listar("SELECT IF($valorU <= minimacuantia,0, 
            if($valorU >= menorcuantia and $valorU <= menorcuantia_m, 12, 
            if($valorU>=mayorcuantia,(SELECT ta.valor FROM gf_plan_inventario pi 
                         LEFT JOIN gf_tipo_activo ta ON pi.tipoactivo = ta.id_unico 
                        WHERE pi.id_unico = ".$dm[0][1]."),0))) as tpo 
        FROM gf_parametrizacion_anno where id_unico = ".$panno);
if(!empty($dp[0][0])){
    $vu = $dp[0][0];
} else {
    $vu = 0;
}
$fechaa = $dm[0][0];
for($i=1;$i<=$cantidad;$i++){
    
    
    $insertProducto     = "insert into gf_producto(valor,descripcion,vida_util_remanente,fecha_adquisicion)"
            . " values ($valorU,$descripcion,$vu,'$fechaa')";
    $resulltProducto    = $mysqli->query($insertProducto);
    
    $sqlProducto        = "select MAX(id_unico) from gf_producto where valor=$valorU and descripcion=$descripcion";
    $resultProducto     = $mysqli->query($sqlProducto);
    $filaProducto       = mysqli_fetch_row($resultProducto);
    $producto           = $filaProducto[0];
    $insertMovimientoProducto = "insert into gf_movimiento_producto(producto,detallemovimiento) values ($producto,$detalleMovimiento)";
    $resultMovimientoProducto = $mysqli->query($insertMovimientoProducto);
    
    $ficha = $_GET['ficha'];
    $sqlFicha="select distinct  elm.id_unico,
                                elm.nombre,
                                fin.id_unico,
                                elm.tipodato,
                                fin.autogenerado
            from gf_ficha_inventario fin 
            left join gf_elemento_ficha elm on elm.id_unico = fin.elementoficha 
            left join gf_tipo_dato tpd on elm.tipodato = tpd.id_unico
            WHERE fin.ficha = $ficha ORDER BY elm.id_unico ASC";
    $resultFicha = $mysqli->query($sqlFicha);
    $contar = 0;
    while($campo = mysqli_fetch_row($resultFicha)){      
        $contar++;
        $fila = str_replace(' ', '', $campo[1]);
        switch ($campo[3]){
            case 1:
                if($campo[4]===1){
                    $sqlAuto="select max(prdes.valor) 
                    from gf_producto_especificacion prdes
                    left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                    left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                    LEFT JOIN gf_movimiento_producto movp ON movp.producto = prdes.producto
                    LEFT JOIN gf_detalle_movimiento detm ON detm.id_unico = movp.detallemovimiento
                    LEFT JOIN gf_movimiento mto ON mto.id_unico = detm.movimiento
                    where elm.nombre = $campo[1] AND mto.compania = $compania";
                    $resultAuto=$mysqli->query($sqlAuto);
                    $filaAuto= mysqli_num_rows($resultAuto);
                    $auto= mysqli_fetch_row($resultAuto);
                    if($filaAuto===0){
                        $valor = "NA";
                    }else{
                        $valor = $auto[0];
                    }
                }
                else{
                    $valor = '"'.$mysqli->real_escape_string(''.$_GET["$fila"].'').'"';
                }                
                break;
            case 2:
                if($campo[4]===1){
                    $sqlAuto="select max(prdes.valor) from gf_producto_especificacion prdes
                    left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                    left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                    LEFT JOIN gf_movimiento_producto movp ON movp.producto = prdes.producto
                    LEFT JOIN gf_detalle_movimiento detm ON detm.id_unico = movp.detallemovimiento
                    LEFT JOIN gf_movimiento mto ON mto.id_unico = detm.movimiento
                    where elm.nombre = $campo[1]";
                    $resultAuto=$mysqli->query($sqlAuto);
                    $filaAuto= mysqli_num_rows($resultAuto);
                    $auto= mysqli_fetch_row($resultAuto);
                    if($filaAuto===0){
                        $valor = "NA";
                    }else{
                        $valor = $auto[0];
                    }
                }else{
                    $valor = '"'.$mysqli->real_escape_string(''.$_GET["$fila"].'').'"';
                }                
                break;
            case 3:
                if($campo[4]===1){                    
                    $sqlAuto="select max(prdes.valor) from gf_producto_especificacion prdes
                    left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                    left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                    where elm.nombre = $campo[1]";
                    $resultAuto=$mysqli->query($sqlAuto);
                    $filaAuto= mysqli_num_rows($resultAuto);
                    $auto= mysqli_fetch_row($resultAuto);
                    if($filaAuto===0){
                        $auto = "NA";
                    }else{
                        $valor = $auto[0];
                    }
                }else{
                    $valor = '"'.$mysqli->real_escape_string(''.$_GET["$fila"].'').'"';
                }                
                break;
            case 4:
                if($campo[4]==1){
                    error_reporting (0);
                    $sqlAuto1="select * from gf_producto_especificacion prdes
                    LEFT JOIN gf_ficha_inventario fin on prdes.fichainventario = fin.id_unico    
                    LEFT JOIN gf_elemento_ficha elm on fin.elementoficha = elm.id_unico
                    LEFT JOIN gf_movimiento_producto movp ON movp.producto = prdes.producto
                    where elm.nombre =  '$campo[1]'";
                    $resultAuto1=$mysqli->query($sqlAuto1);
                    $filaAuto1= mysqli_num_rows($resultAuto1);                    
                    if($filaAuto1>0){
                        $autoN= mysqli_fetch_row($resultAuto1);
                        $sqlVal ="select MAX(cast(prdes.valor as unsigned)) from gf_producto_especificacion prdes   
                           LEFT JOIN gf_movimiento_producto movp ON movp.producto = prdes.producto
                           LEFT JOIN gf_detalle_movimiento detm ON detm.id_unico = movp.detallemovimiento
                           LEFT JOIN gf_movimiento mto ON mto.id_unico = detm.movimiento
                           LEFT JOIN gf_ficha_inventario fin on prdes.fichainventario = fin.id_unico    
                           LEFT JOIN gf_elemento_ficha elm on fin.elementoficha = elm.id_unico
                           WHERE elm.nombre =  '$campo[1]' AND mto.compania = $compania";                                                      
                        $result2 = $mysqli->query($sqlVal);
                        $ultim = mysqli_fetch_row($result2);
                        $valor =$ultim[0]+1;                                                
                    }else{                  
                        $valor = "1";
                    }
                }else{
                    $valor = '"'.$mysqli->real_escape_string(''.$_GET["$fila"].'').'"';
                }
                break;
            case 5:
                if($campo[4]===1){
                    $sqlAuto="select max(prdes.valor) from gf_producto_especificacion prdes
                    left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                    left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                    where elm.nombre = $campo[1]";
                    $resultAuto=$mysqli->query($sqlAuto);
                    $filaAuto= mysqli_num_rows($resultAuto);
                    $auto= mysqli_fetch_row($resultAuto);
                    if($filaAuto==0){
                        $valor = "2";
                    }else{
                        $valor = $auto[0];
                    }
                }else{
                    $valor = '"'.$mysqli->real_escape_string(''.$_GET["$fila"].'').'"';
                }
                break;
            case 6:
               if($campo[4]===1){
                 $sqlAuto="select max(prdes.valor) from gf_producto_especificacion prdes
                    left join gf_ficha_inventario fchin on prdes.fichainventario = fchin.id_unico
                    left join gf_elemento_ficha elm on fchin.elementoficha = elm.id_unico
                    where elm.nombre = $campo[0]";
                    $resultAuto=$mysqli->query($sqlAuto);
                    $filaAuto= mysqli_num_rows($resultAuto);
                    $auto= mysqli_fetch_row($resultAuto);
                    if($filaAuto==0){
                        $valor = date('Y-m-d');
                    }else{
                        $valor = $auto[0];
                    }
                }else{
                    $fechaT = ''.$mysqli->real_escape_string(''.$_GET["$fila"].'').'';
                    $valorF = explode("/",$fechaT);
                    $valor =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"'; 
                }                            
                break;
        }    
        $fichaInventario = $campo[2];
        if(!empty($valor)){
            $insertProductoE="insert into gf_producto_especificacion(fichainventario,producto,valor) values ($fichaInventario,$producto,$valor)";
            $resultProductoEspecificación = $mysqli->query($insertProductoE);
        }
    }
    
    if($i==$cantidad){
        echo json_encode($resultProductoEspecificación);
    }
}

?>