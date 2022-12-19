<?php
require_once ('../Conexion/conexion.php');
require_once ('../Conexion/ConexionPDO.php');
ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];
$html       = '';
switch ($_REQUEST['action']){
    #* Por compañía
    case 1:
        #* Buscar los movimientos de tipo entrada
        $row = $con->Listar("SELECT DISTINCT m.id_unico 
            FROM gf_movimiento m 
            LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
            WHERE m.compania = $compania AND tm.clase = 2");
        $cp = 1;
        for ($i = 0; $i < count($row); $i++) {
            $id_movimiento = $row[$i][0];
            #* Buscar detalles donde plan inventario sea devolutivo
            $rowd = $con->Listar("SELECT DISTINCT dm.id_unico, dm.cantidad, dm.valor  
                FROM gf_detalle_movimiento dm 
                LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
                WHERE dm.movimiento = $id_movimiento and pi.tipoinventario = 2");
            for ($d = 0; $d < count($rowd); $d++) {
                $id_detalle = $rowd[$d][0];
                $cantidad   = $rowd[$d][1];
                #* Buscar Productos 
                $rowdp = $con->Listar("SELECT DISTINCT mp.producto 
                    FROM gf_movimiento_producto mp 
                    WHERE mp.detallemovimiento = $id_detalle");
                if(count($rowdp)!=$cantidad){
                    #Crear Productos faltantes
                    $dif = $cantidad - count($rowdp);
                    if($dif>0){
                        for ($f = 0; $f < $dif; $f++) {
                            $sql_cons ="INSERT INTO `gf_producto` 
                                    ( `descripcion`, `valor`) 
                            VALUES (:descripcion, :valor )";
                            $sql_dato = array(
                                array(":descripcion",'NA'),
                                array(":valor",$rowd[$d][2]),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            //Buscar Producto creado 
                            $bvp = $con->Listar("SELECT MAX(id_unico) FROM gf_producto WHERE valor =".$rowd[$d][2]);

                            $sql_cons ="INSERT INTO `gf_movimiento_producto` 
                                    ( `detallemovimiento`, `producto`) 
                            VALUES (:detallemovimiento, :producto )";
                            $sql_dato = array(
                                array(":detallemovimiento",$id_detalle),
                                array(":producto",$bvp[0][0]),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        }
                    }
                } 
                #* Buscar Productos 
                $rowdp = $con->Listar("SELECT DISTINCT mp.producto 
                    FROM gf_movimiento_producto mp 
                    WHERE mp.detallemovimiento = $id_detalle");
                // Asignar ficha
                for ($p = 0; $p < count($rowdp); $p++) {
                    $pd = $rowdp[$p][0];
                    //Buscar placa 
                    $pl = $con->Listar("SELECT id_unico, valor 
                    FROM gf_producto_especificacion 
                    WHERE producto = $pd and fichainventario = 6");
                    if(empty($pl[0][0])){
                        $sql_cons ="INSERT INTO `gf_producto_especificacion` 
                                ( `valor`, `producto`, 
                                `fichainventario`) 
                        VALUES (:valor, :producto, 
                                :fichainventario)";
                        $sql_dato = array(
                            array(":valor",$cp),
                            array(":producto",$pd),
                            array(":fichainventario",6),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        $cp++;
                    } else {
                        $sql_cons ="UPDATE `gf_producto_especificacion` 
                                SET `valor`=:valor 
                                WHERE `id_unico`=:id_unico ";
                        $sql_dato = array(
                                array(":valor",$cp),
                                array(":id_unico",$pl[0][0]),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        $cp++;
                    }
                }        
            }
        }
        if($cp > 1){
            $html .= $cp.' Placas Generadas Correctamente';
        } else {
            $html .= ' No se encontraron movimientos';
        }
    break;
    #* Todas las compañias
    case 2:
        $rowc = $con->Listar("SELECT DISTINCT  t.id_unico, t.razonsocial, t.numeroidentificacion 
        FROM gf_tercero t 
        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
        WHERE pt.perfil = 1 AND t.id_unico != $compania");
        for ($c = 0; $c < count($rowc); $c++) {
            $compania = $rowc[$c][0];
            #* Buscar los movimientos de tipo entrada
            $row = $con->Listar("SELECT DISTINCT m.id_unico 
                FROM gf_movimiento m 
                LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
                WHERE m.compania = $compania AND tm.clase = 2");
            $cp = 1;
            for ($i = 0; $i < count($row); $i++) {
                $id_movimiento = $row[$i][0];
                #* Buscar detalles donde plan inventario sea devolutivo
                $rowd = $con->Listar("SELECT DISTINCT dm.id_unico, dm.cantidad, dm.valor  
                    FROM gf_detalle_movimiento dm 
                    LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
                    WHERE dm.movimiento = $id_movimiento and pi.tipoinventario = 2");
                for ($d = 0; $d < count($rowd); $d++) {
                    $id_detalle = $rowd[$d][0];
                    $cantidad   = $rowd[$d][1];
                    #* Buscar Productos 
                    $rowdp = $con->Listar("SELECT DISTINCT mp.producto 
                        FROM gf_movimiento_producto mp 
                        WHERE mp.detallemovimiento = $id_detalle");
                    if(count($rowdp)!=$cantidad){
                        #Crear Productos faltantes
                        $dif = $cantidad - count($rowdp);
                        if($dif>0){
                            for ($f = 0; $f < $dif; $f++) {
                                $sql_cons ="INSERT INTO `gf_producto` 
                                        ( `descripcion`, `valor`) 
                                VALUES (:descripcion, :valor )";
                                $sql_dato = array(
                                    array(":descripcion",'NA'),
                                    array(":valor",$rowd[$d][2]),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                                //Buscar Producto creado 
                                $bvp = $con->Listar("SELECT MAX(id_unico) FROM gf_producto WHERE valor =".$rowd[$d][2]);

                                $sql_cons ="INSERT INTO `gf_movimiento_producto` 
                                        ( `detallemovimiento`, `producto`) 
                                VALUES (:detallemovimiento, :producto )";
                                $sql_dato = array(
                                    array(":detallemovimiento",$id_detalle),
                                    array(":producto",$bvp[0][0]),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }
                        }
                    } 
                    #* Buscar Productos 
                    $rowdp = $con->Listar("SELECT DISTINCT mp.producto 
                        FROM gf_movimiento_producto mp 
                        WHERE mp.detallemovimiento = $id_detalle");
                    // Asignar ficha
                    for ($p = 0; $p < count($rowdp); $p++) {
                        $pd = $rowdp[$p][0];
                        //Buscar placa 
                        $pl = $con->Listar("SELECT id_unico, valor 
                        FROM gf_producto_especificacion 
                        WHERE producto = $pd and fichainventario = 6");
                        if(empty($pl[0][0])){
                            $sql_cons ="INSERT INTO `gf_producto_especificacion` 
                                    ( `valor`, `producto`, 
                                    `fichainventario`) 
                            VALUES (:valor, :producto, 
                                    :fichainventario)";
                            $sql_dato = array(
                                array(":valor",$cp),
                                array(":producto",$pd),
                                array(":fichainventario",6),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $cp++;
                        } else {
                            $sql_cons ="UPDATE `gf_producto_especificacion` 
                                    SET `valor`=:valor 
                                    WHERE `id_unico`=:id_unico ";
                            $sql_dato = array(
                                    array(":valor",$cp),
                                    array(":id_unico",$pl[0][0]),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $cp++;
                        }
                    }        
                }
            }
            if($cp > 1){
                $html .= '<strong>'.$rowc[$c][1].' - '.$rowc[$c][2].':</strong>     '.$cp.' Placas Generadas Correctamente'.'<br/>';
            }
        }
    break;
}

echo $html;
