<?php
@session_start();
@ini_set('max_execution_time', 0);
require ('../Conexion/ConexionPDO.php');
require ('../Conexion/conexion.php');
require ('../ExcelR/Classes/PHPExcel/IOFactory.php');
require ('./funcionesPptal.php');
$con = new ConexionPDO();
$param      = $_SESSION['anno'];
$nanno      = anno($param);
if(empty($_REQUEST['compania'])){
    $compania   = $_SESSION['compania'];
} else {
    $compania   = $_REQUEST['compania'];
}
$bscp      = $con->Listar("SELECT MAX(cast(pe.valor AS unsigned)) FROM gf_producto_especificacion pe 
LEFT JOIN gf_producto p ON p.id_unico = pe.producto 
LEFT JOIN gf_movimiento_producto mp ON mp.producto = p.id_unico 
LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
WHERE m.compania = $compania AND pe.fichainventario = 6");
$placa           = $bscp[0][0]+1;
$parametrizacion = buscarParametrizacion();
$htmlI           = "";
$fecha           = $nanno.'-01-01';
#Centro Costo
$id_centro_costo = buscarCC('Varios');
#Crear dependencia Bodega
$bdba             = buscardependencia('Bodega Almacén');
if($bdba==0){
    creardependencia('999','Bodega Almacén', 1, 0);
    $bdba = buscardependencia('Bodega Almacén');
}
#Crear dependencia Bajas
$bdbj = buscardependencia('Bodega de Bajas');
$dtguardados = 0;
if($bdbj==0){
    creardependencia('991','Bodega de Bajas', 2, 0);
}
if (!empty($_FILES['file']['tmp_name'])) {
    $file           = $_FILES['file']['tmp_name'];
    $objReader      = new PHPExcel_Reader_Excel2007();
    $objPHPExcel    = PHPExcel_IOFactory::load($file);
    #Escoger Hoja Movimiento
    if ($objPHPExcel->setActiveSheetIndexByName('INVENTARIO GENERAL')) {
        $objWorksheet   = $objPHPExcel->setActiveSheetIndexByName('INVENTARIO GENERAL');
        $total_filas    = $objWorksheet->getHighestRow();
        $ti             = 2;
        $tf             = 10;
        $total_filas    = $total_filas-1;
        while ($total_filas > 0) {
            for ($a = $ti; $a < $tf; $a++) {
                #Leer Datos
                $n_registro = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();#
                $fecha_r    = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();#
                $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha_r);
                $fecha_r    = date("Y-m-d",$timestamp);
                $codigo     = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();#
                $tipo_a     = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();#
                $nombre_a   = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();#
                $clasf      = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();#
                $descripc   = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();#
                $marca      = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                $modelo     = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
                $serie      = $objWorksheet->getCellByColumnAndRow(9, $a)->getCalculatedValue();
                $color      = $objWorksheet->getCellByColumnAndRow(10, $a)->getCalculatedValue();
                $estado     = $objWorksheet->getCellByColumnAndRow(11, $a)->getCalculatedValue();
                $proceden   = $objWorksheet->getCellByColumnAndRow(12, $a)->getCalculatedValue();
                $doc_sop    = $objWorksheet->getCellByColumnAndRow(13, $a)->getCalculatedValue();
                $n_doc      = $objWorksheet->getCellByColumnAndRow(14, $a)->getCalculatedValue();
                $sede       = $objWorksheet->getCellByColumnAndRow(18, $a)->getCalculatedValue();#
                $dependen   = $objWorksheet->getCellByColumnAndRow(19, $a)->getCalculatedValue();#
                $respons    = $objWorksheet->getCellByColumnAndRow(20, $a)->getCalculatedValue();#
                $cantidad   = $objWorksheet->getCellByColumnAndRow(21, $a)->getCalculatedValue();#
                $valor_t    = $objWorksheet->getCellByColumnAndRow(32, $a)->getCalculatedValue();#
                $vida_u     = $objWorksheet->getCellByColumnAndRow(34, $a)->getCalculatedValue();#
                $observac   = $objWorksheet->getCellByColumnAndRow(40, $a)->getCalculatedValue();#
                if($total_filas<=0){
                } else {
                    #************* CREAR DEPENDENCIAS ******************#
                    #SEDE
                    if(empty($sede) && empty($dependen)){
                        $sede = 'SIN DEPENDENCIA EN DATA';
                    }
                    if(empty($sede)){
                        $id_sede = 0;
                    } else {
                        $bdb = buscardependencia($sede);
                        if($bdb==0){
                            creardependencia('', $sede, 5, 0);
                        }
                        $id_sede = buscardependencia($sede);
                    }
                    #DEPENDENCIA
                    if(empty($dependen)){
                        $id_dependencia = $id_sede;
                    } else {
                        $bdb = buscardependencia($dependen);
                        if($bdb==0){
                            if(empty($id_sede)){
                                creardependencia('', $dependen, 5, 0);
                            } else {
                                creardependencia('', $dependen, 5, $id_sede);
                            }
                        }
                        $id_dependencia = buscardependencia($dependen);
                    } 
                    #************* CREAR RESPONSABLES ******************#
                    if($id_dependencia==0){
                        $htmlI .= "Registro ".$n_registro.' No fue cargado, No es posible encontrar Dependencia';
                    } else {
                        if(empty($respons)){
                            $respons = 'Varios';
                        }
                        $id_responsable = buscarresponsable($respons);
                        if($id_responsable==0){
                            #* Crear Responsables 
                            crearResponsable($respons);
                        }
                        $id_responsable = buscarresponsable($respons);
                        if($id_responsable==0){
                            $htmlI .= "Registro ".$n_registro.' No fue cargado, No es posible encontrar Tercero';
                        } else {
                            #Crear Dependencia Responsable 
                            $dr = creardependenciaR($id_dependencia, $id_responsable);
                            $dr = creardependenciaR($bdba, $id_responsable);
                            $tme = buscarTipoM('ENT');
                            $tms = buscarTipoM('SAL');
                            if($tme==0){
                                $htmlI .= "Registro ".$n_registro.' No fue cargado, No es posible encontrar Tipo Comprobante Entrada';
                            } elseif($tms==0) {
                                $htmlI .= "Registro ".$n_registro.' No fue cargado, No es posible encontrar Tipo Comprobante Salida';
                            } else {
                                #************ CRER ELEMENTOS *************#
                                if($clasf=='PPYE'){
                                    $tipo_el = '2';
                                } else {
                                    $tipo_el = '1';
                                }
                                if(empty($codigo)){
                                    $codigob  = $tipo_el.'9999';
                                } else {
                                    $codigob  = $tipo_el.$codigo;
                                }
                                $nombre_e = ucwords(mb_strtolower($tipo_a.' - '.$nombre_a));
                                if(empty($nombre_e)){
                                    $nombre_e = ucwords(mb_strtolower($descripc));
                                }
                                if(empty($tipo_a.$nombre_a)){
                                    $nombre_e = 'VARIOS';
                                }
                                #** BUSCAR ELEMENTO 
                                $elemento = buscarElemento($codigob, $nombre_e, $tipo_el,$nombre_a);
                                if($elemento == 0){    
                                    guardarElemento($codigob, $nombre_e, $tipo_el,$nombre_a);
                                }
                                $elemento = buscarElemento($codigob, $nombre_e, $tipo_el,$nombre_a);
                                if(empty($elemento)){
                                    $htmlI .= "Registro ".$n_registro.' No fue cargado, No es posible encontrar Elemento';
                                } else {
                                    #************ CRER MOVIMIENTOS *************#
                                    #ENTRADAS
                                    $entrada = buscarMovimiento($tme, $bdba, $id_responsable,$fecha_r);
                                    if($entrada ==0){
                                        $ms = crearMovimiento($tme, $bdba, $id_responsable,$fecha_r);
                                    } 
                                    $entrada = buscarMovimiento($tme, $bdba, $id_responsable,$fecha_r);

                                    #SALIDAS 
                                    $salida = buscarMovimiento($tms, $id_dependencia, $id_responsable,$fecha_r);
                                    if($salida ==0){
                                        $ms = crearMovimiento($tms, $id_dependencia, $id_responsable,$fecha_r);
                                    } 
                                    $salida = buscarMovimiento($tms, $id_dependencia, $id_responsable,$fecha_r);

                                    if(empty($entrada) || empty($salida)){
                                        $htmlI .= "Registro ".$n_registro.' No fue cargado, No es posible encontrar Entrada O Salida';
                                    } else {
                                        #************ CREAR DETALLES *************#
                                        #ENTRADAS
                                        #Insertar Detalle
                                        $cantidadO = $cantidad;
                                        if(empty($cantidadO)){
                                            if($valor_t < 0){
                                                $cantidad = 0;
                                            } else {
                                                $cantidad = 1;
                                            }
                                        } else {
                                            if($valor_t < 0){
                                                $cantidad = 0;
                                            }elseif($cantidad ==0) {
                                                if($valor_t < 0){
                                                    $cantidad = 0;
                                                } else {
                                                    $cantidad = 1;
                                                }
                                            } else {
                                                $cantidad = (int)$cantidadO;
                                            }
                                        }
                                        if($valor_t < 0){
                                            $valor_u = 0;
                                        } else {
                                            if($cantidad ==0){
                                                if($valor_t < 0){
                                                    $valor_u  = 0;
                                                } else {
                                                    $valor_u  = $valor_t;
                                                }
                                            } else {
                                                $valor_u  = ROUND(($valor_t/$cantidad),3);
                                            }
                                        }
                                        $detalle = guardarDetalle($entrada,$elemento,$cantidadO,$cantidad, $valor_t, $valor_u,$observac,$n_registro,NULL);
                                        
                                        if($clasf=='PPYE'){
                                            if(empty($vida_u)){
                                                $vida_u = 0;
                                            }
                                            #Insertar Producto
                                            for ($f = 0; $f < $cantidad; $f++) {
                                                $sql_cons ="INSERT INTO `gf_producto` 
                                                        ( `descripcion`, `valor`,`vida_util_remanente`,`fecha_adquisicion`,`fecha_anterior`) 
                                                VALUES (:descripcion, :valor, :vida_util_remanente, :fecha_adquisicion , :fecha_anterior)";
                                                $sql_dato = array(
                                                    array(":descripcion",$descripc),
                                                    array(":valor",$valor_u),
                                                    array(":vida_util_remanente",$vida_u),
                                                    array(":fecha_adquisicion",'2019-01-01'),
                                                    array(":fecha_anterior",$fecha_r),
                                                );
                                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                                                //Buscar Producto creado 
                                                $bvp = $con->Listar("SELECT MAX(id_unico) FROM gf_producto WHERE valor =".$valor_u);
                                                $producto = $bvp[0][0];
                                                $sql_cons ="INSERT INTO `gf_movimiento_producto` 
                                                        ( `detallemovimiento`, `producto`) 
                                                VALUES (:detallemovimiento, :producto )";
                                                $sql_dato = array(
                                                    array(":detallemovimiento",$detalle),
                                                    array(":producto",$bvp[0][0]),
                                                );
                                                $resp = $con->InAcEl($sql_cons,$sql_dato);

                                                crearEspecificaciones($producto,$marca,$serie,$color,$codigo,$modelo,$estado,$proceden,$doc_sop,$n_doc);
                                            }
                                        }
                                        #SALIDAS
                                        $detalles = guardarDetalle($salida,$elemento,$cantidadO,$cantidad, $valor_t, $valor_u,$observac,$n_registro,$detalle);
                                        if($clasf=='PPYE'){
                                            #* Buscar Productos detalles entrada 
                                            $rowpe = $con->Listar("SELECT producto FROM gf_movimiento_producto WHERE detallemovimiento = $detalle");
                                            for ($p = 0; $p < count($rowpe); $p++) {
                                                $sql_cons ="INSERT INTO `gf_movimiento_producto` 
                                                        ( `detallemovimiento`, `producto`) 
                                                VALUES (:detallemovimiento, :producto )";
                                                $sql_dato = array(
                                                    array(":detallemovimiento",$detalles),
                                                    array(":producto",$rowpe[$p][0]),
                                                );
                                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                                            }
                                        }

                                        #Actualizar Dependencia Entrada
                                        $upd = $sql_cons ="UPDATE `gf_movimiento` 
                                                SET `dependencia`=:dependencia 
                                                WHERE `id_unico`=:id_unico ";
                                        $sql_dato = array(
                                                array(":dependencia",$bdba),
                                                array(":id_unico",$entrada),
                                        );
                                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                                        $dtguardados +=1;
                                    }
                                }
                            }
                        }
                    } 
                }
                $total_filas -= 1;    
            } 
            $ti  = $tf;
            $tf += 9;
        }
    }
}
$datos = array("msj"=>$htmlI,"rta"=>$dtguardados, "pr"=>$placa); 
echo json_encode($datos);

function buscardependencia($nombre){
    global $con;
    global $compania;
    
    $nombre = str_replace(' ', '', $nombre);
    $buscar = $con->Listar("SELECT * FROM gf_dependencia
        WHERE REPLACE(nombre, ' ', '')='$nombre' AND compania = $compania ");
    if(!empty($buscar[0][0])){
        $id = $buscar[0][0];
    } else {
        $id = 0;
    }
    return $id;
}
function creardependencia($sigla, $nombre, $clase, $predecesor){
    global $con;
    global $compania;
    global $id_centro_costo;
    if(empty($sigla)){
        if($predecesor==0){
            #* Buscar Una dependencia Con código mayor
            $bdp = $con->Listar("SELECT MAX(cast(sigla as unsigned)),LENGTH(MAX(cast(sigla as unsigned)))
                FROM gf_dependencia 
                WHERE compania = $compania AND sigla NOT IN('999', '991')");
            if(empty($bdp[0][0])){
                $sigla ='001';
            } else {
                $n = $bdp[0][0]+1;
                if($bdp[0][1]==1){
                    $sigla ='00'.$n;
                }elseif($bdp[0][1]==2){
                    $sigla ='0'.$n;
                }else {
                    $sigla =$n;
                }   
            }
        } else {
            #* Buscar Una dependencia Con código mayor
            $bdp = $con->Listar("SELECT MAX(cast(sigla as unsigned)),
                LENGTH(MAX(cast(sigla as unsigned))), 
                (SELECT da.sigla FROM gf_dependencia da WHERE da.id_unico = '$predecesor') 
                FROM gf_dependencia d
                WHERE d.compania = $compania AND d.sigla NOT IN('999', '991') 
                AND d.predecesor = '$predecesor'");
            if(empty($bdp[0][0])){
                $sigla =$bdp[0][2].'001';
            } else {
                $n = $bdp[0][0]+1;
                if($bdp[0][1]==1){
                    $sigla =$bdp[0][2].'00'.$n;
                }elseif($bdp[0][1]==2){
                    $sigla =$bdp[0][2].'0'.$n;
                }else {
                    $sigla =$bdp[0][2].$n;
                }   
            }
        }
    }
    if($predecesor==0){
        $id_predecesor = NULL;
    } else {
        $id_predecesor = $predecesor;
    }
    $sql_cons ="INSERT INTO `gf_dependencia` 
            ( `nombre`,`sigla`,`movimiento`,`activa`, `compania`,
            `predecesor`, `centrocosto`,`tipodependencia`) 
    VALUES (:nombre,:sigla,:movimiento, :activa, :compania, 
        :predecesor, :centrocosto, :tipodependencia)";
    $sql_dato = array(
        array(":nombre",$nombre),
        array(":sigla",$sigla),
        array(":movimiento",1),
        array(":activa",1),
        array(":compania",$compania),
        array(":predecesor",$id_predecesor),
        array(":centrocosto",$id_centro_costo),
        array(":tipodependencia",$clase),
        
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);    
    return true;
}
function buscarCC($nombre){
    global $con;
    global $compania;
    global $nanno;
    
    $buscar = $con->Listar("SELECT cc.id_unico  
        FROM gf_centro_costo cc 
        LEFT JOIN gf_parametrizacion_anno pa ON cc.parametrizacionanno = pa.id_unico AND pa.anno = '$nanno'
        WHERE cc.nombre = '$nombre' AND pa.compania = $compania");
    if(!empty($buscar[0][0])){
        $id = $buscar[0][0];
    } else {
        $id = 0;
    }
    return $id;
}
function buscarresponsable($nombre){
    global $con;
    global $compania;
    
    $nombre = str_replace(' ', '', $nombre);
    
    $buscar = $con->Listar("SELECT * FROM gf_tercero
        WHERE CONCAT_WS('',REPLACE(nombreuno, ' ', ''), 
            REPLACE(nombredos, ' ', ''), REPLACE(apellidouno, ' ', ''), 
            REPLACE(apellidodos, ' ', ''),REPLACE(razonsocial, ' ', ''))='$nombre'
            AND compania = $compania ");
    if(!empty($buscar[0][0])){
        $id = $buscar[0][0];
    } else {
        $id = 0;
    }
    return $id;
}
function crearResponsable($nombre){
    global $con;
    global $compania;
    global $id_centro_costo;
    
    $razonsocial = $nombre;
    $bdp = $con->Listar("SELECT MAX(cast(numeroidentificacion as unsigned)),
        LENGTH(MAX(cast(numeroidentificacion as unsigned))) 
        FROM gf_tercero 
        WHERE compania = $compania AND numeroidentificacion LIKE '0%'");
    if(empty($bdp[0][0])){
        $nidentificacion ='001';
    } else {
        $n = $bdp[0][0]+1;
        if($bdp[0][1]==1){
            $nidentificacion ='00'.$n;
        }elseif($bdp[0][1]==2){
            $nidentificacion ='0'.$n;
        }else {
            $nidentificacion =$n;
        }   
    }
    $sql_cons ="INSERT INTO `gf_tercero` 
            ( `razonsocial`,`numeroidentificacion`,
            `tipoidentificacion`,`compania`) 
    VALUES (:razonsocial,:numeroidentificacion,
        :tipoidentificacion, :compania)";
    $sql_dato = array(
        array(":razonsocial",$razonsocial),
        array(":numeroidentificacion",$nidentificacion),
        array(":tipoidentificacion",1),
        array(":compania",$compania),
        
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);    
    return true;
}
function creardependenciaR($dependencia, $responsable){
    global $con;
    global $compania;
    $be = $con->Listar("SELECT * FROM gf_dependencia_responsable 
        WHERE dependencia = $dependencia AND responsable = $responsable");
    if(empty($be[0][0])){
        $sql_cons ="INSERT INTO `gf_dependencia_responsable` 
                ( `dependencia`,`responsable`,
                `movimiento`,`estado`) 
        VALUES (:dependencia,:responsable,
            :movimiento, :estado)";
        $sql_dato = array(
            array(":dependencia",$dependencia),
            array(":responsable",$responsable),
            array(":movimiento",1),
            array(":estado",1),

        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);  
    }
    return true;
}
function buscarTipoM($sigla){
    global $con;
    global $compania;
    $be = $con->Listar("SELECT * FROM gf_tipo_movimiento 
        WHERE sigla = '$sigla' AND compania = $compania");
    if(empty($be[0][0])){
        return 0;
    } else {
        return $be[0][0];
    }
}
function buscarMovimiento($tipo, $dependencia, $responsable,$fechar){
    global $con;
    global $compania;
    global $nanno;
    global $parametrizacion;
    $be = $con->Listar("SELECT * FROM gf_movimiento  
        WHERE tipomovimiento = $tipo 
        AND dependencia = $dependencia 
        AND tercero = $responsable 
        AND compania = $compania 
        and fecha = '$fechar' 
        AND parametrizacionanno = $parametrizacion");
    if(empty($be[0][0])){
        return 0;
    } else {
        return $be[0][0];
    }
}
function crearMovimiento($tipo, $dependencia, $responsable,$fecha_r){
    global $con;
    global $compania;
    global $parametrizacion;
    global $fecha;
    global $id_centro_costo;
    
    $bdp = $con->Listar("SELECT MAX(cast(numero as unsigned)) 
        FROM gf_movimiento  
        WHERE compania = $compania AND  tipomovimiento = $tipo 
            AND parametrizacionanno = $parametrizacion");
    if(empty($bdp[0][0])){
        $numero ='2019000001';
    } else {
        $numero =$bdp[0][0]+1;
    }
    $sql_cons ="INSERT INTO `gf_movimiento` 
            ( `numero`,`fecha`,`descripcion`,
            `porcivaglobal`, `tipomovimiento`, `parametrizacionanno`, 
            `compania`, `tercero`, `tercero2`,
            `dependencia`, `centrocosto`, `proyecto`, 
            `estado` ) 
    VALUES (:numero,:fecha,:descripcion,
        :porcivaglobal, :tipomovimiento, :parametrizacionanno,
        :compania, :tercero, :tercero2, 
        :dependencia, :centrocosto,:proyecto, 
        :estado)";
    $sql_dato = array(
        array(":numero",$numero),
        array(":fecha",$fecha_r),
        array(":descripcion",'Movimiento Inventario R'),
        array(":porcivaglobal",0),
        array(":tipomovimiento",$tipo),
        array(":parametrizacionanno",$parametrizacion),
        array(":compania",$compania),
        array(":tercero",$responsable),
        array(":tercero2",$responsable),
        array(":dependencia",$dependencia),
        array(":centrocosto",$id_centro_costo),
        array(":proyecto",2147483647),
        array(":estado",2),
        
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);    
    return true;
}
function buscarParametrizacion(){
    global $con;
    global $compania;
    global $nanno;
    $be = $con->Listar("SELECT * FROM gf_parametrizacion_anno   
        WHERE anno = '$nanno' 
        AND compania = $compania");
    return $be[0][0];
}

function guardarElemento($codigoP, $nombre, $ti,$nombre_a){
    global $con;
    global $compania;
    global $nanno;
    #Buscar Número 
    $cd = $con->Listar("SELECT MAX(codi) FROM gf_plan_inventario 
    WHERE codi LIKE '$codigoP%'  AND compania = $compania 
        AND codi !='$codigoP' AND tipoinventario = $ti" );
    if(empty($cd[0][0])){
        $codigon = $codigoP.'0001';
    }else {
        $codigon = $cd[0][0]+1;
    }
    $rowelem    = $con->Listar("SELECT * 
    FROM gf_plan_inventario
    WHERE codi = '$codigoP' AND compania = $compania");
    $sql_cons ="INSERT INTO `gf_plan_inventario`  
            ( `nombre`,`codi`, `tienemovimiento`,
            `compania`,`tipoinventario`,`unidad`,
            `predecesor`,`tipoactivo`,`nombre_activo`,`ficha`) 
    VALUES (:nombre,:codi, :tienemovimiento, 
            :compania, :tipoinventario, :unidad, 
            :predecesor, :tipoactivo,:nombre_activo, :ficha)";
    $sql_dato = array(
            array(":nombre",$nombre),
            array(":codi",$codigon), 
            array(":tienemovimiento",2), 
            array(":compania",$compania), 
            array(":tipoinventario",$ti), 
            array(":unidad",2), 
            array(":predecesor",$rowelem[0][0]), 
            array(":tipoactivo",1),
            array(":nombre_activo",$nombre_a),
            array(":ficha",2),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato); 
    $tcon = $con->Listar("SELECT * FROM gf_plan_inventario 
        WHERE nombre LIKE '$nombre' AND codi =$codigon AND compania =$compania  ");
    $id_elemento = $tcon[0][0];
    
    return $id_elemento;
}

function buscarElemento($codigo, $nombre, $tipo_el){
    global $con;
    global $compania;
    
    $nombre = str_replace(' ', '', $nombre);
    $nombre = str_replace(' - ', '', $nombre);
    $cd = $con->Listar("SELECT * FROM gf_plan_inventario 
    WHERE codi LIKE '$codigo%'  
        AND compania = $compania 
        AND REPLACE(nombre, ' ', '') = '$nombre' 
        AND tipoinventario = $tipo_el");
    if(empty($cd[0][0])){
        $codigon = 0;
    }else {
        $codigon = $cd[0][0];
    }
    return $codigon;
}
function guardarDetalle($movimiento,$elemento,$cantidadO,$cantidad, $valor_t, $valor_u,$observac,$n_registro, $detallea){
    global $con;
    global $compania;
    global $nanno;
    
    $sql_cons ="INSERT INTO `gf_detalle_movimiento`  
            ( `cantidad`,`valor`, `iva`,
            `movimiento`,`detalleasociado`,`planmovimiento`,
            `xvalor_t`,`n_registro`,`observaciones`,`cantidad_origen`) 
    VALUES (:cantidad,:valor, :iva, 
            :movimiento, :detalleasociado, :planmovimiento, 
            :xvalor_t, :n_registro,:observaciones, :cantidad_origen)";
    $sql_dato = array(
            array(":cantidad",$cantidad),
            array(":valor",$valor_u), 
            array(":iva",0), 
            array(":movimiento",$movimiento), 
            array(":detalleasociado",$detallea), 
            array(":planmovimiento",$elemento), 
            array(":xvalor_t",$valor_t), 
            array(":n_registro",$n_registro),
            array(":observaciones",$observac),
            array(":cantidad_origen",$cantidadO),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato); 
    $id_detalle = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_movimiento WHERE movimiento = $movimiento");
    return $id_detalle[0][0];
}
function crearEspecificaciones($producto,$marca,$serie,$color,$codigo,$modelo,$estado,$proceden,$doc_sop,$n_doc){
    global $con;
    global $placa;
    
    #***Marca
    $sql_cons ="INSERT INTO `gf_producto_especificacion` 
            ( `valor`, `producto`, 
            `fichainventario`) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$marca),
        array(":producto",$producto),
        array(":fichainventario",2),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***serie
    $sql_cons ="INSERT INTO `gf_producto_especificacion` 
            ( `valor`, `producto`, 
            `fichainventario`) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$serie),
        array(":producto",$producto),
        array(":fichainventario",5),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Color
    $sql_cons ="INSERT INTO `gf_producto_especificacion` 
            ( `valor`, `producto`, 
            `fichainventario`) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$color),
        array(":producto",$producto),
        array(":fichainventario",9),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Codigo
    $sql_cons ="INSERT INTO `gf_producto_especificacion` 
            ( `valor`, `producto`, 
            `fichainventario`) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$codigo),
        array(":producto",$producto),
        array(":fichainventario",10),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Modelo
    $sql_cons ="INSERT INTO `gf_producto_especificacion` 
            ( `valor`, `producto`, 
            `fichainventario`) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$modelo),
        array(":producto",$producto),
        array(":fichainventario",13),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Estado
    $sql_cons ="INSERT INTO `gf_producto_especificacion` 
            ( `valor`, `producto`, 
            `fichainventario`) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$estado),
        array(":producto",$producto),
        array(":fichainventario",14),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Procedencia
    $sql_cons ="INSERT INTO `gf_producto_especificacion` 
            ( `valor`, `producto`, 
            `fichainventario`) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$proceden),
        array(":producto",$producto),
        array(":fichainventario",15),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Doc_Soporte
    $sql_cons ="INSERT INTO `gf_producto_especificacion` 
            ( `valor`, `producto`, 
            `fichainventario`) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$doc_sop),
        array(":producto",$producto),
        array(":fichainventario",16),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Numero_Doc
    $sql_cons ="INSERT INTO `gf_producto_especificacion` 
            ( `valor`, `producto`, 
            `fichainventario`) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$n_doc),
        array(":producto",$producto),
        array(":fichainventario",17),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Placa
    $sql_cons ="INSERT INTO `gf_producto_especificacion` 
            ( `valor`, `producto`, 
            `fichainventario`) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$placa),
        array(":producto",$producto),
        array(":fichainventario",6),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    $placa++; 
}