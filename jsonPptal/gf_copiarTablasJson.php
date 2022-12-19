<?php
require '../Conexion/ConexionPDO.php';                                                  
require '../Conexion/conexion.php';                                                     
require './funcionesPptal.php';
require '../ExcelR/Classes/PHPExcel/IOFactory.php';    
ini_set('max_execution_time', 0);
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
$html       = '';
switch ($action){
    #* De compañía a Compañia *#
    case 1:
        #*** Copiar A Todas Las Compañias ***#
        if($_POST['compania']==0){
            $rowc =$con->Listar("SELECT 
                DISTINCT t.id_unico 
                FROM  gf_tercero t 
                LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero  
                WHERE t.id_unico != $compania AND pt.perfil = 1 ");
        #*** Copiar a una compañia ***#    
        } else {
            $rowc =$con->Listar("SELECT 
                DISTINCT t.id_unico 
                FROM  gf_tercero t   
                WHERE t.id_unico = ".$_POST['compania']);
        }
        $d =0;
        switch ($_POST['tabla']) {
        #************ Copiar Tablas De Tipo Comprobante ***********#
        case 1:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                #********************** Comprobante Pptal ****************************#
                $rowpptal = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE compania = $compania");
                for ($c = 0; $c < count($rowpptal); $c++) {
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal 
                        WHERE compania = $tercero_compania AND codigo='".$rowpptal[$c][1]."'");
                    if(count($rowe)>0){ } else {
                        $codigo         = $rowpptal[$c][1];
                        $nombre         = $rowpptal[$c][2];
                        $obligaciona    = $rowpptal[$c][3];
                        $terceroi       = $rowpptal[$c][4];
                        $clasepptal     = $rowpptal[$c][6];
                        $formato        = $rowpptal[$c][7];
                        $tipooperacion  = $rowpptal[$c][8];
                        $tipodocumento  = $rowpptal[$c][9];
                        $vigencia_a     = $rowpptal[$c][10];
                        $automatico     = $rowpptal[$c][11];

                        $sql_cons ="INSERT INTO `gf_tipo_comprobante_pptal`  
                            ( `codigo`,  `nombre`, 
                            `obligacionafectacion`,`terceroigual`,
                            `compania`,`clasepptal`,
                            `formato`,`tipooperacion`,
                            `tipodocumento`,`vigencia_actual`,
                            `automatico`) 
                        VALUES (:codigo, :nombre, 
                            :obligacionafectacion, :terceroigual,
                            :compania,:clasepptal,
                            :formato,:tipooperacion,
                            :tipodocumento,:vigencia_actual,
                            :automatico)";
                        $sql_dato = array(
                            array(":codigo",$codigo),
                            array(":nombre",$nombre),
                            array(":obligacionafectacion",$obligaciona),
                            array(":terceroigual",$terceroi),
                            array(":compania",$tercero_compania),
                            array(":clasepptal",$clasepptal),
                            array(":formato",$formato),
                            array(":tipooperacion",$tipooperacion),
                            array(":tipodocumento",$tipodocumento),
                            array(":vigencia_actual",$vigencia_a),
                            array(":automatico",$automatico),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }

                #*** Actualizar Afectados ***#
                $rowpptal = $con->Listar("SELECT af.id_unico, tc.codigo 
                    FROM gf_tipo_comprobante_pptal tc 
                    LEFT JOIN gf_tipo_comprobante_pptal ta ON tc.afectado = ta.id_unico 
                    LEFT JOIN gf_tipo_comprobante_pptal af ON ta.codigo = af.codigo AND ta.compania = $compania AND af.compania = $tercero_compania  
                    WHERE tc.compania = $compania  AND tc.afectado IS NOT NULL");
                for ($c = 0; $c < count($rowpptal); $c++) {
                    $afectado = $rowpptal[$c][0];
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal 
                        WHERE compania = $tercero_compania AND codigo='".$rowpptal[$c][1]."'");
                    if(count($rowe)>0){ 
                        $id = $rowe[0][0];
                        #*** Actualizar 
                        $sql_cons ="UPDATE `gf_tipo_comprobante_pptal`  
                            SET `afectado`=:afectado 
                            WHERE `id_unico`=:id_unico";
                        $sql_dato = array(
                            array(":afectado",$afectado),
                            array(":id_unico",$id),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);                    
                    } 
                }

                #********************** Comprobante Cnt ****************************#
                $rowcnt = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE compania = $compania");
                for ($c = 0; $c < count($rowcnt); $c++) {
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_tipo_comprobante 
                        WHERE compania = $tercero_compania AND sigla='".$rowcnt[$c][1]."'");
                    if(count($rowe)>0){ } else {
                        $sigla          = $rowcnt[$c][1];
                        $nombre         = $rowcnt[$c][2];
                        $retencion      = $rowcnt[$c][3];
                        $interface      = $rowcnt[$c][4];
                        $niif           = $rowcnt[$c][5];
                        $clasecontable  = $rowcnt[$c][6];
                        $formato        = $rowcnt[$c][7];
                        $tipodocumento  = $rowcnt[$c][10];
                        $interfazp      = $rowcnt[$c][12];
                        $interfazc      = $rowcnt[$c][13];
                        $interfazr      = $rowcnt[$c][14];

                        $sql_cons ="INSERT INTO `gf_tipo_comprobante`  
                            ( `sigla`,  `nombre`, 
                            `retencion`,`interface`,
                            `niif`,`clasecontable`,
                            `formato`,`compania`,
                            `tipodocumento`,`interfaz_predial`,
                            `interfaz_comercio`,`interfaz_reteica`) 
                        VALUES (:sigla, :nombre, 
                            :retencion, :interface,
                            :niif,:clasecontable,
                            :formato,:compania,
                            :tipodocumento,:interfaz_predial,
                            :interfaz_comercio, :interfaz_reteica)";
                        $sql_dato = array(
                            array(":sigla",$sigla),
                            array(":nombre",$nombre),
                            array(":retencion",$retencion),
                            array(":interface",$interface),
                            array(":niif",$niif),
                            array(":clasecontable",$clasecontable),
                            array(":formato",$formato),
                            array(":compania",$tercero_compania),
                            array(":tipodocumento",$tipodocumento),
                            array(":interfaz_predial",$interfazp),
                            array(":interfaz_comercio",$interfazc),   
                            array(":interfaz_reteica",$interfazr),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }

                #*** Actualizar Afectados PPtal ***#
                $rowcnt = $con->Listar("SELECT af.id_unico, tc.sigla  
                    FROM gf_tipo_comprobante tc 
                    LEFT JOIN gf_tipo_comprobante_pptal ta ON tc.comprobante_pptal = ta.id_unico 
                    LEFT JOIN gf_tipo_comprobante_pptal af ON ta.codigo = af.codigo 
                    AND ta.compania = $compania AND af.compania = $tercero_compania  
                    WHERE tc.compania = $compania  AND tc.comprobante_pptal IS NOT NULL");
                for ($c = 0; $c < count($rowcnt); $c++) {
                    $afectado = $rowcnt[$c][0];
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_tipo_comprobante 
                        WHERE compania = $tercero_compania AND sigla='".$rowcnt[$c][1]."'");
                    if(count($rowe)>0){ 
                        $id = $rowe[0][0];
                        #*** Actualizar 
                        $sql_cons ="UPDATE `gf_tipo_comprobante`  
                            SET `comprobante_pptal`=:comprobante_pptal 
                            WHERE `id_unico`=:id_unico";
                        $sql_dato = array(
                            array(":comprobante_pptal",$afectado),
                            array(":id_unico",$id),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);                    
                    } 
                }
                #*** Actualizar Afectados Cnt ***#
                $rowcnt = $con->Listar("SELECT af.id_unico, tc.sigla  
                    FROM gf_tipo_comprobante tc 
                    LEFT JOIN gf_tipo_comprobante ta ON tc.tipo_comp_hom = ta.id_unico 
                    LEFT JOIN gf_tipo_comprobante af ON ta.sigla = af.sigla 
                        AND ta.compania = $compania AND af.compania = $tercero_compania 
                    WHERE tc.compania = $compania  AND tc.tipo_comp_hom IS NOT NULL");
                for ($c = 0; $c < count($rowcnt); $c++) {
                    $afectado = $rowcnt[$c][0];
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_tipo_comprobante 
                        WHERE compania = $tercero_compania AND sigla='".$rowcnt[$c][1]."'");
                    if(count($rowe)>0){ 
                        $id = $rowe[0][0];
                        #*** Actualizar 
                        $sql_cons ="UPDATE `gf_tipo_comprobante`  
                            SET `tipo_comp_hom`=:tipo_comp_hom 
                            WHERE `id_unico`=:id_unico";
                        $sql_dato = array(
                            array(":tipo_comp_hom",$afectado),
                            array(":id_unico",$id),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);                    
                    } 
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #******* Tipo Retención **********#
        case 2:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                #*** Buscar La Parametrizacion De La Compania Y Del Año Sesion
                $rowp = $con->Listar("SELECT * FROM gf_parametrizacion_anno 
                    WHERE compania = $tercero_compania AND anno = $anno");
                $pannocompania = $rowp[0][0];
                $row = $con->Listar("SELECT * FROM gf_tipo_retencion WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_tipo_retencion  
                        WHERE parametrizacionanno = $pannocompania AND nombre='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_tipo_retencion`  
                            ( `nombre`, 
                            `porcentajebase`,`limiteinferior`,
                            `porcentajeaplicar`,`valoraplicar`,
                            `factorredondeo`,`descripcion`,
                            `modificarretencion`,`factoraplicacion`,
                            `tipobase`,`modificarbase`,
                            `ley1450`,`claseretencion`,
                            `parametrizacionanno`,`compania`) 
                        VALUES (:nombre, 
                            :porcentajebase, :limiteinferior,
                            :porcentajeaplicar,:valoraplicar,
                            :factorredondeo,:descripcion,
                            :modificarretencion,:factoraplicacion,
                            :tipobase, :modificarbase, 
                            :ley1450,:claseretencion,
                            :parametrizacionanno,:compania)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":porcentajebase",$row[$t][2]),
                            array(":limiteinferior",$row[$t][3]),
                            array(":porcentajeaplicar",$row[$t][4]),
                            array(":valoraplicar",$row[$t][5]),
                            array(":factorredondeo",$row[$t][6]),
                            array(":descripcion",$row[$t][7]),
                            array(":modificarretencion",$row[$t][8]),
                            array(":factoraplicacion",$row[$t][9]),
                            array(":tipobase",$row[$t][10]),
                            array(":modificarbase",$row[$t][11]),   
                            array(":ley1450",$row[$t][12]),   
                            array(":claseretencion",$row[$t][13]),   
                            array(":parametrizacionanno",$pannocompania),   
                            array(":compania",$tercero_compania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #*** Centro Costo ***#
        case 3:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                #*** Buscar La Parametrizacion De La Compania Y Del Año Sesion
                $rowp = $con->Listar("SELECT * FROM gf_parametrizacion_anno 
                    WHERE compania = $tercero_compania AND anno = $anno");
                $pannocompania = $rowp[0][0];
                $row = $con->Listar("SELECT * FROM gf_centro_costo WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_centro_costo   
                        WHERE parametrizacionanno = $pannocompania AND nombre='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_centro_costo`  
                            ( `nombre`, 
                            `movimiento`,`sigla`,
                            `tipocentrocosto`,`predecesor`,
                            `claseservicio`,`parametrizacionanno`,
                            `compania`) 
                        VALUES (:nombre, 
                            :movimiento, :sigla,
                            :tipocentrocosto,:predecesor,
                            :claseservicio,
                            :parametrizacionanno,:compania)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":movimiento",$row[$t][2]),
                            array(":sigla",$row[$t][3]),
                            array(":tipocentrocosto",$row[$t][4]),
                            array(":predecesor",$row[$t][5]),
                            array(":claseservicio",$row[$t][6]),  
                            array(":parametrizacionanno",$pannocompania),   
                            array(":compania",$tercero_compania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #*** Mes ***#
        case 4:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                #*** Buscar La Parametrizacion De La Compania Y Del Año Sesion
                $rowp = $con->Listar("SELECT * FROM gf_parametrizacion_anno 
                    WHERE compania = $tercero_compania AND anno = $anno");
                $pannocompania = $rowp[0][0];
                $row = $con->Listar("SELECT * FROM gf_mes WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_mes   
                        WHERE parametrizacionanno = $pannocompania AND mes='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_mes`  
                            ( `mes`, 
                            `estadomes`,`parametrizacionanno`,
                            `compania`,`numero`) 
                        VALUES (:mes, 
                            :estadomes, 
                            :parametrizacionanno,:compania, 
                            :numero)";
                        $sql_dato = array(
                            array(":mes",$row[$t][1]),
                            array(":estadomes",$row[$t][2]),
                            array(":parametrizacionanno",$pannocompania),   
                            array(":compania",$tercero_compania),   
                            array(":numero",$row[$t][5]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #*** Terceros ***#
        case 5:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                $row = $con->Listar("SELECT t.*, pt.perfil FROM gf_tercero t 
                        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
                        WHERE pt.perfil != 1 AND t.compania = $compania");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_tercero 
                        WHERE compania = $tercero_compania AND numeroidentificacion='".$row[$t][7]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_tercero`  
                            ( `nombreuno`, `nombredos`,
                            `apellidouno`,`apellidodos`,
                            `razonsocial`,`nombre_comercial`,
                            `numeroidentificacion`,`digitoverficacion`,
                            `compania`,`tipoidentificacion`,
                            `sucursal`,`ciudadresidencia`,
                            `ciudadidentificacion`,`tiporegimen`,
                            `zona`,
                            `cargo`,`tipoempresa`,
                            `tipoentidad`,`codigo_dane`,
                            `ruta_logo`,`tarjeta_profesional`,
                            `tipo_compania`,`fecha_nacimiento`,
                            `email`,`migradoCCB`,
                            `firma`) 
                        VALUES (:nombreuno,:nombredos, 
                            :apellidouno,:apellidodos, 
                            :razonsocial, :nombre_comercial,
                            :numeroidentificacion,:digitoverficacion,
                            :compania,:tipoidentificacion,
                            :sucursal,:ciudadresidencia,
                            :ciudadidentificacion,:tiporegimen,
                            :zona,
                            :cargo,:tipoempresa,
                            :tipoentidad,:codigo_dane,
                            :ruta_logo,:tarjeta_profesional,
                            :tipo_compania,:fecha_nacimiento,
                            :email,:migradoCCB,
                            :firma)";
                        $sql_dato = array(
                            array(":nombreuno",$row[$t][1]),
                            array(":nombredos",$row[$t][2]),
                            array(":apellidouno",$row[$t][3]),   
                            array(":apellidodos",$row[$t][4]),   
                            array(":razonsocial",$row[$t][5]),
                            array(":nombre_comercial",$row[$t][6]),
                            array(":numeroidentificacion",$row[$t][7]),
                            array(":digitoverficacion",$row[$t][8]),
                            array(":compania",$tercero_compania),
                            array(":tipoidentificacion",$row[$t][10]),
                            array(":sucursal",$row[$t][11]),
                            array(":ciudadresidencia",$row[$t][13]),
                            array(":ciudadidentificacion",$row[$t][14]),
                            array(":tiporegimen",$row[$t][15]),
                            array(":zona",$row[$t][17]),
                            array(":cargo",$row[$t][18]),
                            array(":tipoempresa",$row[$t][19]),
                            array(":tipoentidad",$row[$t][20]),
                            array(":codigo_dane",$row[$t][21]),
                            array(":ruta_logo",$row[$t][22]),
                            array(":tarjeta_profesional",$row[$t][23]),
                            array(":tipo_compania",$row[$t][24]),
                            array(":fecha_nacimiento",$row[$t][25]),
                            array(":email",$row[$t][26]),
                            array(":migradoCCB",$row[$t][27]),
                            array(":firma",$row[$t][28]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                            $rowbt = $con->Listar("SELECT * FROM gf_tercero 
                            WHERE compania = $tercero_compania AND numeroidentificacion='".$row[$t][7]."'");
                            #** Guardar Perfil **#
                            $idt = $rowbt[0][0];
                            $sql_cons ="INSERT INTO `gf_perfil_tercero`  
                                ( `perfil`, 
                                `tercero`) 
                            VALUES (:perfil, 
                                :tercero)";
                            $sql_dato = array(
                                array(":perfil",$row[$t][29]),
                                array(":tercero",$idt),
                            );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        }
                    }
                }
                #* Actualizar Representante *#
                $row = $con->Listar("SELECT t.id_unico, tr.id_unico, t.numeroidentificacion FROM gf_tercero t 
                        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
                        LEFT JOIN gf_tercero trp ON t.representantelegal = trp.id_unico 
                        LEFT JOIN gf_tercero tr ON tr.numeroidentificacion = trp.numeroidentificacion 
                            AND trp.compania = $compania AND tr.compania = $tercero_compania 
                        WHERE pt.perfil != 1 AND t.compania = $compania AND t.representantelegal is not null");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_tercero 
                        WHERE compania = $tercero_compania AND numeroidentificacion='".$row[$t][2]."'");
                    if(count($rowe)>0){ 
                        $idm = $rowe[0][0];
                        $representante = $row[$t][1];
                        #*** Actualizar 
                        $sql_cons ="UPDATE `gf_tercero`  
                            SET `representantelegal`=:representantelegal 
                            WHERE `id_unico`=:id_unico";
                        $sql_dato = array(
                            array(":representantelegal",$representante),
                            array(":id_unico",$idm),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);    
                    } 
                }

                #* Actualizar Contacto *#
                $row = $con->Listar("SELECT t.id_unico, tr.id_unico, t.numeroidentificacion FROM gf_tercero t 
                        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
                        LEFT JOIN gf_tercero trp ON t.contacto = trp.id_unico 
                        LEFT JOIN gf_tercero tr ON tr.numeroidentificacion = trp.numeroidentificacion 
                            AND trp.compania = $compania AND tr.compania = $tercero_compania 
                        WHERE pt.perfil != 1 AND t.compania = $compania AND t.contacto is not null");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_tercero 
                        WHERE compania = $tercero_compania AND numeroidentificacion='".$row[$t][2]."'");
                    if(count($rowe)>0){ 
                        $idm = $rowe[0][0];
                        $representante = $row[$t][1];
                        #*** Actualizar 
                        $sql_cons ="UPDATE `gf_tercero`  
                            SET `contacto`=:contacto 
                            WHERE `id_unico`=:id_unico";
                        $sql_dato = array(
                            array(":contacto",$representante),
                            array(":id_unico",$idm),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);    
                    } 
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #*** Plan Contable ***#
        case 6:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                #*** Buscar La Parametrizacion De La Compania Y Del Año Sesion
                $rowp = $con->Listar("SELECT * FROM gf_parametrizacion_anno 
                    WHERE compania = $tercero_compania AND anno = $anno");
                $pannocompania = $rowp[0][0];
                $row = $con->Listar("SELECT * FROM gf_cuenta WHERE parametrizacionanno = $panno AND activa !=2");
                $nd = count($row);
                $inicio = 0;
                $fin    = 500;
                $a =0;
                while ($a==0){
                    $row = $con->Listar("SELECT * FROM gf_cuenta WHERE parametrizacionanno = $panno AND activa !=2 
                        LIMIT $inicio, $fin;");
                    for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_cuenta   
                        WHERE parametrizacionanno = $pannocompania AND codi_cuenta='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_cuenta`  
                            ( `codi_cuenta`,`nombre`, 
                            `movimiento`,`centrocosto`,
                            `auxiliartercero`,`auxiliarproyecto`,
                            `parametrizacionanno`,`activa`,
                            `dinamica`,`naturaleza`,
                            `tipocuentacgn`,`clasecuenta`,
                            `cuentapuente`,`equivalente_va`) 
                        VALUES (:codi_cuenta, :nombre,
                            :movimiento,:centrocosto,
                            :auxiliartercero,:auxiliarproyecto, 
                            :parametrizacionanno,:activa,
                            :dinamica,:naturaleza,
                            :tipocuentacgn,:clasecuenta,
                            :cuentapuente,:equivalente_va)";
                        $sql_dato = array(
                            array(":codi_cuenta",$row[$t][1]),
                            array(":nombre",$row[$t][2]),
                            array(":movimiento",$row[$t][3]),   
                            array(":centrocosto",$row[$t][4]),   
                            array(":auxiliartercero",$row[$t][5]),
                            array(":auxiliarproyecto",$row[$t][6]),
                            array(":parametrizacionanno",$pannocompania),
                            array(":activa",$row[$t][8]),
                            array(":dinamica",$row[$t][9]),
                            array(":naturaleza",$row[$t][10]),
                            array(":tipocuentacgn",$row[$t][12]),
                            array(":clasecuenta",$row[$t][13]),
                            array(":cuentapuente",$row[$t][14]),
                            array(":equivalente_va",$row[$t][15]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                    if($nd>$fin){
                        $inicio = $fin;
                        $fin    +=500;
                    } else {
                        $a =1;
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #*** Plan Presupuestal ***#
        case 7:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                #*** Buscar La Parametrizacion De La Compania Y Del Año Sesion
                $rowp = $con->Listar("SELECT * FROM gf_parametrizacion_anno 
                    WHERE compania = $tercero_compania AND anno = $anno");
                $pannocompania = $rowp[0][0];
                $row = $con->Listar("SELECT * FROM gf_rubro_pptal WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_rubro_pptal   
                        WHERE parametrizacionanno = $pannocompania AND codi_presupuesto='".$row[$t][2]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_rubro_pptal`  
                            ( `nombre`,`codi_presupuesto`, 
                            `movimiento`,`manpac`,
                            `vigencia`,`dinamica`,
                            `parametrizacionanno`,`tipoclase`,
                            `destino`,`tipovigencia`,
                            `sector`,`equivalente`) 
                        VALUES (:nombre,:codi_presupuesto, 
                            :movimiento,:manpac,
                            :vigencia,:dinamica, 
                            :parametrizacionanno,:tipoclase,
                            :destino,:tipovigencia,
                            :sector,:equivalente)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":codi_presupuesto",$row[$t][2]),
                            array(":movimiento",$row[$t][3]),   
                            array(":manpac",$row[$t][4]),   
                            array(":vigencia",$pannocompania),
                            array(":dinamica",$row[$t][6]),
                            array(":parametrizacionanno",$pannocompania),
                            array(":tipoclase",$row[$t][8]),
                            array(":destino",$row[$t][10]),
                            array(":tipovigencia",$row[$t][11]),
                            array(":sector",$row[$t][12]),
                            array(":equivalente",$row[$t][13]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #*** Plan Inventario ***#
        case 8:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                $row = $con->Listar("SELECT * FROM gf_plan_inventario 
                        WHERE compania = $compania");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_plan_inventario 
                        WHERE compania = $tercero_compania AND codi='".$row[$t][2]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_plan_inventario`  
                            ( `nombre`, `codi`,
                            `tienemovimiento`,`compania`,
                            `tipoinventario`,`unidad`,
                            `tipoactivo`,`ficha`,
                            `xCantidad`,`xFactura`,
                            `codigo_barras`,`porc_util`) 
                        VALUES (:nombre,:codi, 
                            :tienemovimiento,:compania, 
                            :tipoinventario, :unidad,
                            :tipoactivo,:ficha,
                            :xCantidad,:xFactura,
                            :codigo_barras,:porc_util)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":codi",$row[$t][2]),
                            array(":tienemovimiento",$row[$t][3]),   
                            array(":compania",$tercero_compania),   
                            array(":tipoinventario",$row[$t][5]),
                            array(":unidad",$row[$t][6]),
                            array(":tipoactivo",$row[$t][8]),
                            array(":ficha",$row[$t][9]),
                            array(":xCantidad",$row[$t][10]),
                            array(":xFactura",$row[$t][11]),
                            array(":codigo_barras",$row[$t][12]),
                            array(":porc_util",$row[$t][13])
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* Actualizar predecesor *#
                $row = $con->Listar("SELECT p.id_unico, p.codi, pr.codi 
                        FROM gf_plan_inventario p 
                        LEFT JOIN gf_plan_inventario pr ON p.predecesor = pr.id_unico 
                        WHERE p.compania = $compania 
                        AND p.predecesor IS NOT NULL");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_plan_inventario 
                        WHERE compania = $tercero_compania AND codi='".$row[$t][1]."'");
                    if(count($rowe)>0){ 
                        $predec = $con->Listar("SELECT id_unico FROM gf_plan_inventario 
                           WHERE compania = $tercero_compania AND codi='".$row[$t][2]."' ");
                        #*** Actualizar 
                        $sql_cons ="UPDATE `gf_plan_inventario`  
                            SET `predecesor`=:predecesor 
                            WHERE `id_unico`=:id_unico";
                        $sql_dato = array(
                            array(":predecesor",$predec[0][0]),
                            array(":id_unico",$rowe[0][0]),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);    
                    } 
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
         #*** Tipo Movimiento ***#
        case 9:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                $row = $con->Listar("SELECT * FROM gf_tipo_movimiento  
                        WHERE compania = $compania");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_tipo_movimiento 
                        WHERE compania = $tercero_compania AND sigla='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_tipo_movimiento`  
                            ( `sigla`, `nombre`,
                            `costea`,`clase`,
                            `tipoelemento`,`tipopersona`,
                            `tipo_documento`,`compania`) 
                        VALUES (:sigla,:nombre, 
                            :costea,:clase, 
                            :tipoelemento, :tipopersona,
                            :tipo_documento,:compania)";
                        $sql_dato = array(
                            array(":sigla",$row[$t][1]),
                            array(":nombre",$row[$t][2]),
                            array(":costea",$row[$t][3]),   
                            array(":clase",$row[$t][4]),
                            array(":tipoelemento",$row[$t][5]),
                            array(":tipopersona",$row[$t][6]),
                            array(":tipo_documento",$row[$t][7]),
                            array(":compania",$tercero_compania)
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #*** Dependencias ***#
        case 10:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                $row = $con->Listar("SELECT * FROM gf_dependencia  
                        WHERE compania = $compania");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_dependencia 
                        WHERE compania = $tercero_compania AND sigla='".$row[$t][2]."'");
                    if(count($rowe)>0){ } else {
                        #** Consultar Centro Costo ** #
                        $cc = $con->Listar("SELECT cca.* FROM gf_centro_costo cc 
                        LEFT JOIN gf_centro_costo cca ON cc.nombre = cca.nombre AND cca.compania = $tercero_compania 
                        LEFT JOIN gf_parametrizacion_anno pa ON cca.parametrizacionanno = pa.id_unico AND pa.anno = '$anno'
                        where cc.id_unico = ".$row[$t][5]);
                        $sql_cons ="INSERT INTO `gf_dependencia`  
                            ( `sigla`, `nombre`,
                            `movimiento`,`activa`,
                            `centrocosto`,`tipodependencia`,
                            `xFactura`,`compania`) 
                        VALUES (:sigla,:nombre, 
                            :movimiento,:activa, 
                            :centrocosto, :tipodependencia,
                            :xFactura,:compania)";
                        $sql_dato = array(
                            array(":sigla",$row[$t][2]),
                            array(":nombre",$row[$t][1]),
                            array(":movimiento",$row[$t][3]),   
                            array(":activa",$row[$t][4]),
                            array(":centrocosto",$cc[0][0]),
                            array(":tipodependencia",$row[$t][8]),
                            array(":xFactura",$row[$t][9]),
                            array(":compania",$tercero_compania)
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #*** Dependencias Responsables***#
        case 11:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                $row = $con->Listar("SELECT dr.*, tc.id_unico, dp.id_unico 
                    FROM gf_dependencia_responsable dr 
                LEFT JOIN gf_tercero t ON dr.responsable = t.id_unico 
                LEFT JOIN gf_dependencia d ON dr.dependencia = d.id_unico 
                LEFT JOIN gf_tercero tc ON t.numeroidentificacion = tc.numeroidentificacion AND tc.compania = $tercero_compania  
                LEFT JOIN gf_dependencia dp ON d.sigla = dp.sigla AND dp.compania = $tercero_compania  
                WHERE d.compania = $compania AND t.compania = $compania");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_dependencia_responsable  
                        WHERE dependencia = '".$rowt[$t][5]."' AND responsable='".$row[$t][4]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_dependencia_responsable`  
                            ( `dependencia`, `responsable`,
                            `movimiento`,`estado`) 
                        VALUES (:dependencia,:responsable, 
                            :movimiento,:estado)";
                        $sql_dato = array(
                            array(":dependencia",$row[$t][5]),
                            array(":responsable",$row[$t][4]),
                            array(":movimiento",$row[$t][2]),   
                            array(":estado",$row[$t][3]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #*** Fuentes ***#
        case 12:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                #*** Buscar La Parametrizacion De La Compania Y Del Año Sesion
                $rowp = $con->Listar("SELECT * FROM gf_parametrizacion_anno 
                    WHERE compania = $tercero_compania AND anno = $anno");
                $pannocompania = $rowp[0][0];
                $row = $con->Listar("SELECT * FROM gf_fuente WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_fuente   
                        WHERE parametrizacionanno = $pannocompania AND nombre='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_fuente`  
                            ( `nombre`,
                            `movimiento`,`parametrizacionanno`,
                            `compania`,`tipofuente`,
                            `recursofinanciero`,`equivalente`) 
                        VALUES (:nombre,
                            :movimiento,:parametrizacionanno,
                            :compania,:tipofuente, 
                            :recursofinanciero,:equivalente)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":movimiento",$row[$t][2]),   
                            array(":parametrizacionanno",$pannocompania),   
                            array(":compania",$row[$t][4]),
                            array(":tipofuente",$row[$t][6]),
                            array(":recursofinanciero",$row[$t][7]),
                            array(":equivalente",$row[$t][8]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 

        break;
        #*** Tipo Documento ***#
        case 13:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                $row = $con->Listar("SELECT * FROM gf_tipo_documento  
                        WHERE compania = $compania");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_tipo_documento    
                        WHERE compania = $tercero_compania AND nombre='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_tipo_documento`  
                            ( `nombre`,
                            `es_obligatorio`,`consecutivo_unico`,
                            `formato`,`clase_informe`,`compania`) 
                        VALUES (:nombre,
                            :es_obligatorio,:consecutivo_unico,
                            :formato,:clase_informe,:compania)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":es_obligatorio",$row[$t][2]),   
                            array(":consecutivo_unico",$row[$t][3]),   
                            array(":formato",$row[$t][4]),
                            array(":clase_informe",$row[$t][6]),
                            array(":compania",$tercero_compania),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        #var_dump($obj_resp);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 

        break;
        #* Cuentas Específicas 
        case 14:
           for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                #*** Buscar La Parametrizacion De La Compania Y Del Año Sesion
                $rowp = $con->Listar("SELECT * FROM gf_parametrizacion_anno 
                    WHERE compania = $tercero_compania AND anno = $anno");
                $pannocompania = $rowp[0][0];
                $row = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = ".$_REQUEST['codi_cuenta']." AND parametrizacionanno = $panno");
                $nd = count($row);
                $inicio = 0;
                $fin    = 500;
                $a =0;
                while ($a==0){
                    $row = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = ".$_REQUEST['codi_cuenta']." AND parametrizacionanno = $panno 
                        LIMIT $inicio, $fin;");
                    for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_cuenta   
                        WHERE parametrizacionanno = $pannocompania AND codi_cuenta='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_cuenta`  
                            ( `codi_cuenta`,`nombre`, 
                            `movimiento`,`centrocosto`,
                            `auxiliartercero`,`auxiliarproyecto`,
                            `parametrizacionanno`,`activa`,
                            `dinamica`,`naturaleza`,
                            `tipocuentacgn`,`clasecuenta`,
                            `cuentapuente`,`equivalente_va`) 
                        VALUES (:codi_cuenta, :nombre,
                            :movimiento,:centrocosto,
                            :auxiliartercero,:auxiliarproyecto, 
                            :parametrizacionanno,:activa,
                            :dinamica,:naturaleza,
                            :tipocuentacgn,:clasecuenta,
                            :cuentapuente,:equivalente_va)";
                        $sql_dato = array(
                            array(":codi_cuenta",$row[$t][1]),
                            array(":nombre",$row[$t][2]),
                            array(":movimiento",$row[$t][3]),   
                            array(":centrocosto",$row[$t][4]),   
                            array(":auxiliartercero",$row[$t][5]),
                            array(":auxiliarproyecto",$row[$t][6]),
                            array(":parametrizacionanno",$pannocompania),
                            array(":activa",$row[$t][8]),
                            array(":dinamica",$row[$t][9]),
                            array(":naturaleza",$row[$t][10]),
                            array(":tipocuentacgn",$row[$t][12]),
                            array(":clasecuenta",$row[$t][13]),
                            array(":cuentapuente",$row[$t][14]),
                            array(":equivalente_va",$row[$t][15]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                    if($nd>$fin){
                        $inicio = $fin;
                        $fin    +=500;
                    } else {
                        $a =1;
                    }
                }
            }
           $datos = array("d"=>$d,"rta"=>$html);
           echo json_encode($datos); 
        break;
        #*** Distribución Centro de Costos***#
        case 15:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                #*** Buscar La Parametrizacion De La Compania Y Del Año Sesion
                $rowp = $con->Listar("SELECT * FROM gf_parametrizacion_anno 
                    WHERE compania = $tercero_compania AND anno = $anno");
                $pannocompania = $rowp[0][0];
                $row = $con->Listar("SELECT DISTINCT cpa.id_unico, ccpa.id_unico, ctapa.id_unico ,
                    cf.porcentaje 
                    FROM gf_configuracion_distribucion cf
                    LEFT JOIN gf_concepto c ON cf.concepto = c.id_unico 
                    LEFT JOIN gf_concepto cpa ON c.nombre = cpa.nombre AND cpa.parametrizacionanno = $pannocompania
                    LEFT JOIN gf_centro_costo cc ON cf.centro_costo = cc.id_unico
                    LEFT JOIN gf_centro_costo ccpa ON cc.nombre = ccpa.nombre AND ccpa.parametrizacionanno = $pannocompania 
                    LEFT JOIN gf_cuenta cta ON cf.cuenta = cta.id_unico 
                    LEFT JOIN gf_cuenta ctapa ON cta.codi_cuenta = ctapa.codi_cuenta AND ctapa.parametrizacionanno = $pannocompania 
                    WHERE c.parametrizacionanno = $panno  
                    AND cpa.id_unico IS NOT NULL
                    AND ccpa.id_unico IS NOT NULL
                    AND ctapa.id_unico IS NOT NULL");
                for ($t = 0; $t < count($row); $t++) {
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_configuracion_distribucion  
                        WHERE concepto = ".$row[$t][0]." 
                        AND centro_costo = ".$row[$t][1]." 
                        AND cuenta=".$row[$t][2]);                
                    if(count($rowe)>0){ 
                    } else {
                        $sql_cons ="INSERT INTO `gf_configuracion_distribucion`  
                            ( `concepto`, 
                            `centro_costo`,`cuenta`,
                            `porcentaje`) 
                        VALUES (:concepto, 
                            :centro_costo, :cuenta,
                            :porcentaje)";
                        $sql_dato = array(
                            array(":concepto",$row[$t][0]),
                            array(":centro_costo",$row[$t][1]),
                            array(":cuenta",$row[$t][2]),
                            array(":porcentaje",$row[$t][3]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        #*** Traslados***#
        case 16:
            for ($i = 0; $i < count($rowc); $i++) {
                $tercero_compania = $rowc[$i][0];
                #*** Buscar La Parametrizacion De La Compania Y Del Año Sesion
                $rowp = $con->Listar("SELECT * FROM gf_parametrizacion_anno 
                    WHERE compania = $tercero_compania AND anno = $anno");
                $pannocompania = $rowp[0][0];
                $row = $con->Listar("SELECT DISTINCT ctpa.id_unico, ccpa.id_unico, ctdpa.id_unico , ccdpa.id_unico , ctcpa.id_unico , cccpa.id_unico 
                FROM gf_configuracion_traslado cft 
                LEFT JOIN gf_cuenta ct ON cft.cuenta_traslado = ct.id_unico 
                LEFT JOIN gf_cuenta ctpa ON ct.codi_cuenta = ctpa.codi_cuenta AND ctpa.parametrizacionanno = $pannocompania 
                LEFT JOIN gf_centro_costo cc ON cft.centro_costo = cc.id_unico 
                LEFT JOIN gf_centro_costo ccpa ON cc.nombre = ccpa.nombre AND ccpa.parametrizacionanno = $pannocompania 
                LEFT JOIN gf_cuenta ctd ON cft.cuenta_debito = ctd.id_unico 
                LEFT JOIN gf_cuenta ctdpa ON ctd.codi_cuenta = ctdpa.codi_cuenta AND ctdpa.parametrizacionanno = $pannocompania 
                LEFT JOIN gf_centro_costo ccd ON cft.centro_costo_debito = ccd.id_unico 
                LEFT JOIN gf_centro_costo ccdpa ON ccd.nombre = ccdpa.nombre AND ccdpa.parametrizacionanno = $pannocompania 
                LEFT JOIN gf_cuenta ctc ON cft.cuenta_credito = ctc.id_unico 
                LEFT JOIN gf_cuenta ctcpa ON ctc.codi_cuenta = ctcpa.codi_cuenta AND ctcpa.parametrizacionanno = $pannocompania
                LEFT JOIN gf_centro_costo ccc ON cft.centro_costo_credito = ccc.id_unico 
                LEFT JOIN gf_centro_costo cccpa ON ccc.nombre = cccpa.nombre AND cccpa.parametrizacionanno = $pannocompania 
                WHERE ct.parametrizacionanno = $panno  
                AND ctpa.id_unico IS NOT NULL 
                AND ccpa.id_unico IS NOT NULL 
                AND ctdpa.id_unico IS NOT NULL 
                AND ccdpa.id_unico IS NOT NULL 
                AND ctcpa.id_unico IS NOT NULL 
                AND cccpa.id_unico IS NOT NULL ");
                for ($t = 0; $t < count($row); $t++) {
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_configuracion_traslado  
                        WHERE cuenta_traslado = ".$row[$t][0]." 
                        AND centro_costo = ".$row[$t][1]." 
                        AND cuenta_debito=".$row[$t][2]."
                        AND centro_costo_debito= ".$row[$t][3]."
                        AND cuenta_credito= ".$row[$t][4]."
                        AND centro_costo_credito= ".$row[$t][5]);                
                    if(count($rowe)>0){ 
                    } else {
                        $sql_cons ="INSERT INTO `gf_configuracion_traslado`  
                            ( `cuenta_traslado`, `centro_costo`,
                            `cuenta_debito`,`centro_costo_debito`, 
                            `cuenta_credito`, `centro_costo_credito`) 
                        VALUES (:cuenta_traslado, :centro_costo, 
                            :cuenta_debito,:centro_costo_debito,
                            :cuenta_credito, :centro_costo_credito)";
                        $sql_dato = array(
                            array(":cuenta_traslado",$row[$t][0]),
                            array(":centro_costo",$row[$t][1]),
                            array(":cuenta_debito",$row[$t][2]),
                            array(":centro_costo_debito",$row[$t][3]),
                            array(":cuenta_credito",$row[$t][4]),
                            array(":centro_costo_credito",$row[$t][5]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
            }
            $datos = array("d"=>$d,"rta"=>$html);
            echo json_encode($datos); 
        break;
        
    }
    break;
    #************************ COPIAR TABLAS DE AÑO A AÑO ****************************#
    case 2:
        $d =0;
        switch ($_POST['tabla']){
            #*** Centro Costo ***#
            case 1:
                $anno_copiar = $_REQUEST['anno'];
                $row = $con->Listar("SELECT * FROM gf_centro_costo WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_centro_costo   
                        WHERE parametrizacionanno = $anno_copiar AND nombre='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_centro_costo`  
                            ( `nombre`, 
                            `movimiento`,`sigla`,
                            `tipocentrocosto`,`predecesor`,
                            `claseservicio`,`parametrizacionanno`,
                            `compania`) 
                        VALUES (:nombre, 
                            :movimiento, :sigla,
                            :tipocentrocosto,:predecesor,
                            :claseservicio,
                            :parametrizacionanno,:compania)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":movimiento",$row[$t][2]),
                            array(":sigla",$row[$t][3]),
                            array(":tipocentrocosto",$row[$t][4]),
                            array(":predecesor",$row[$t][5]),
                            array(":claseservicio",$row[$t][6]),  
                            array(":parametrizacionanno",$anno_copiar),   
                            array(":compania",$compania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Meses ***#
            case 2:
                $anno_copiar = $_REQUEST['anno'];
                $row = $con->Listar("SELECT * FROM gf_mes WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_mes   
                        WHERE parametrizacionanno = $anno_copiar AND mes='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_mes`  
                            ( `mes`, 
                            `estadomes`,`parametrizacionanno`,
                            `compania`,`numero`) 
                        VALUES (:mes, 
                            :estadomes, 
                            :parametrizacionanno,:compania, 
                            :numero)";
                        $sql_dato = array(
                            array(":mes",$row[$t][1]),
                            array(":estadomes",$row[$t][2]),
                            array(":parametrizacionanno",$anno_copiar),   
                            array(":compania",$compania),   
                            array(":numero",$row[$t][5]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 

            break;
            #*** Plan Contable ***#
            case 3:
                $tercero_compania = $compania;
                $pannocompania    = $_REQUEST['anno'];
                $row = $con->Listar("SELECT * FROM gf_cuenta WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_cuenta   
                        WHERE parametrizacionanno = $pannocompania AND codi_cuenta='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_cuenta`  
                            ( `codi_cuenta`,`nombre`, 
                            `movimiento`,`centrocosto`,
                            `auxiliartercero`,`auxiliarproyecto`,
                            `parametrizacionanno`,`activa`,
                            `dinamica`,`naturaleza`,
                            `tipocuentacgn`,`clasecuenta`,
                            `cuentapuente`,`equivalente_va`) 
                        VALUES (:codi_cuenta, :nombre,
                            :movimiento,:centrocosto,
                            :auxiliartercero,:auxiliarproyecto, 
                            :parametrizacionanno,:activa,
                            :dinamica,:naturaleza,
                            :tipocuentacgn,:clasecuenta,
                            :cuentapuente,:equivalente_va)";
                        $sql_dato = array(
                            array(":codi_cuenta",$row[$t][1]),
                            array(":nombre",$row[$t][2]),
                            array(":movimiento",$row[$t][3]),   
                            array(":centrocosto",$row[$t][4]),   
                            array(":auxiliartercero",$row[$t][5]),
                            array(":auxiliarproyecto",$row[$t][6]),
                            array(":parametrizacionanno",$pannocompania),
                            array(":activa",$row[$t][8]),
                            array(":dinamica",$row[$t][9]),
                            array(":naturaleza",$row[$t][10]),
                            array(":tipocuentacgn",$row[$t][12]),
                            array(":clasecuenta",$row[$t][13]),
                            array(":cuentapuente",$row[$t][14]),
                            array(":equivalente_va",$row[$t][15]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Plan Presupuestal ***#
            case 4:
                $tercero_compania = $compania;
                $pannocompania    = $_REQUEST['anno'];
                $row = $con->Listar("SELECT * FROM gf_rubro_pptal WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_rubro_pptal   
                        WHERE parametrizacionanno = $pannocompania AND codi_presupuesto='".$row[$t][2]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_rubro_pptal`  
                            ( `nombre`,`codi_presupuesto`, 
                            `movimiento`,`manpac`,
                            `vigencia`,`dinamica`,
                            `parametrizacionanno`,`tipoclase`,
                            `destino`,`tipovigencia`,
                            `sector`,`equivalente`) 
                        VALUES (:nombre,:codi_presupuesto, 
                            :movimiento,:manpac,
                            :vigencia,:dinamica, 
                            :parametrizacionanno,:tipoclase,
                            :destino,:tipovigencia,
                            :sector,:equivalente)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":codi_presupuesto",$row[$t][2]),
                            array(":movimiento",$row[$t][3]),   
                            array(":manpac",$row[$t][4]),   
                            array(":vigencia",$pannocompania),
                            array(":dinamica",$row[$t][6]),
                            array(":parametrizacionanno",$pannocompania),
                            array(":tipoclase",$row[$t][8]),
                            array(":destino",$row[$t][10]),
                            array(":tipovigencia",$row[$t][11]),
                            array(":sector",$row[$t][12]),
                            array(":equivalente",$row[$t][13]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Fuentes ***#
            case 5:
                $pannocompania    = $_REQUEST['anno'];
                $row = $con->Listar("SELECT * FROM gf_fuente WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_fuente   
                        WHERE parametrizacionanno = $pannocompania AND nombre='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_fuente`  
                            ( `nombre`,
                            `movimiento`,`parametrizacionanno`,
                            `compania`,`tipofuente`,
                            `recursofinanciero`,`equivalente`) 
                        VALUES (:nombre,
                            :movimiento,:parametrizacionanno,
                            :compania,:tipofuente, 
                            :recursofinanciero,:equivalente)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":movimiento",$row[$t][2]),   
                            array(":parametrizacionanno",$pannocompania),   
                            array(":compania",$row[$t][4]),
                            array(":tipofuente",$row[$t][6]),
                            array(":recursofinanciero",$row[$t][7]),
                            array(":equivalente",$row[$t][8]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Conceptos ***#
            case 6:
                $pannocompania    = $_REQUEST['anno'];
                $row = $con->Listar("SELECT * FROM gf_concepto WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_concepto   
                        WHERE parametrizacionanno = $pannocompania AND nombre='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_concepto`  
                            ( `nombre`,
                            `clase_concepto`,`parametrizacionanno`) 
                        VALUES (:nombre,
                            :clase_concepto,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":clase_concepto",$row[$t][2]),   
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Tipo Retenciones ***#
            case 7:
                $pannocompania    = $_REQUEST['anno'];
                $row = $con->Listar("SELECT * FROM gf_tipo_retencion WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_tipo_retencion  
                        WHERE parametrizacionanno = $pannocompania AND nombre='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_tipo_retencion`  
                            ( `nombre`, 
                            `porcentajebase`,`limiteinferior`,
                            `porcentajeaplicar`,`valoraplicar`,
                            `factorredondeo`,`descripcion`,
                            `modificarretencion`,`factoraplicacion`,
                            `tipobase`,`modificarbase`,
                            `ley1450`,`claseretencion`,
                            `parametrizacionanno`,`compania`) 
                        VALUES (:nombre, 
                            :porcentajebase, :limiteinferior,
                            :porcentajeaplicar,:valoraplicar,
                            :factorredondeo,:descripcion,
                            :modificarretencion,:factoraplicacion,
                            :tipobase, :modificarbase, 
                            :ley1450,:claseretencion,
                            :parametrizacionanno,:compania)";
                        $sql_dato = array(
                            array(":nombre",$row[$t][1]),
                            array(":porcentajebase",$row[$t][2]),
                            array(":limiteinferior",$row[$t][3]),
                            array(":porcentajeaplicar",$row[$t][4]),
                            array(":valoraplicar",$row[$t][5]),
                            array(":factorredondeo",$row[$t][6]),
                            array(":descripcion",$row[$t][7]),
                            array(":modificarretencion",$row[$t][8]),
                            array(":factoraplicacion",$row[$t][9]),
                            array(":tipobase",$row[$t][10]),
                            array(":modificarbase",$row[$t][11]),   
                            array(":ley1450",$row[$t][12]),   
                            array(":claseretencion",$row[$t][13]),   
                            array(":parametrizacionanno",$pannocompania),   
                            array(":compania",$compania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                            #*** Actualizar Cuenta ***# 
                            if($_REQUEST['igual']=='1'){
                                $rt = $con->Listar("SELECT * FROM gf_tipo_retencion  
                                WHERE parametrizacionanno = $pannocompania AND nombre='".$row[$t][1]."'");
                                $cuenta  = $row[$t][16];
                                $codc    = $con->Listar("SELECT cp.* FROM gf_cuenta c 
                                LEFT JOIN gf_cuenta cp ON c.codi_cuenta = cp.codi_cuenta 
                                and cp.parametrizacionanno = $pannocompania 
                                where c.id_unico = $cuenta");
                                if(count($codc)>0){
                                    $sql_cons ="UPDATE `gf_tipo_retencion`  
                                            SET `cuenta`=:cuenta
                                            WHERE `id_unico`=:id_unico ";
                                        $sql_dato = array(
                                            array(":cuenta",$codc[0][0]),
                                            array(":id_unico",$rt[0][0]),  
                                        );
                                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                                }                                   
                            }
                        }
                    }
                }
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Configuración Concepto Rubro Cuenta ***#
            case 8:
                $pannocompania    = $_REQUEST['anno'];
                $row = $con->Listar("SELECT
                        crc.concepto_rubro AS concepto_rubro_cuenta,
                        cr.id_unico AS concepto_rubro,
                        c.id_unico AS concepto,
                        rb.id_unico AS rubro,
                        cd.id_unico AS cd,
                        cc.id_unico AS cc,
                        cn.id_unico AS centroc,
                        crc.proyecto AS proyecto,
                        ci.id_unico AS cuenta_iva,
                        cim.id_unico AS cuenta_impoconsumo,
                        crc.tercero AS tercero,
                        cdp.id_unico AS cuenta_debito_g,
                        ccp.id_unico AS cuenta_credito_g,
                        cip.id_unico AS cuenta_iva_g,
                        cimp.id_unico AS cuenta_impo_g,
                        cnp.id_unico AS centro_g,
                        rbg.id_unico AS rubro_g,
                        cg.id_unico AS concepto_g
                    FROM
                        gf_concepto_rubro_cuenta crc
                    LEFT JOIN gf_concepto_rubro cr ON
                        cr.id_unico = crc.concepto_rubro
                    LEFT JOIN gf_concepto c ON
                        cr.concepto = c.id_unico AND c.parametrizacionanno = $panno
                    LEFT JOIN gf_rubro_pptal rb ON
                        cr.rubro = rb.id_unico AND rb.parametrizacionanno = $panno
                    LEFT JOIN gf_cuenta cd ON
                        crc.cuenta_debito = cd.id_unico AND cd.parametrizacionanno = $panno
                    LEFT JOIN gf_cuenta cc ON
                        cc.id_unico = crc.cuenta_credito AND cc.parametrizacionanno = $panno
                    LEFT JOIN gf_cuenta ci ON
                        crc.cuenta_iva = ci.id_unico AND ci.parametrizacionanno = $panno
                    LEFT JOIN gf_cuenta cim ON
                        crc.cuenta_impoconsumo = cim.id_unico AND cim.parametrizacionanno = $panno
                    LEFT JOIN gf_centro_costo cn ON
                        crc.centrocosto = cn.id_unico AND cn.parametrizacionanno = $panno
                    LEFT JOIN gf_cuenta cdp ON
                        cdp.codi_cuenta = cd.codi_cuenta AND cdp.parametrizacionanno = $pannocompania
                    LEFT JOIN gf_cuenta ccp ON
                        ccp.codi_cuenta = cc.codi_cuenta AND ccp.parametrizacionanno = $pannocompania
                    LEFT JOIN gf_cuenta cip ON
                        cip.codi_cuenta = ci.codi_cuenta AND cip.parametrizacionanno = $pannocompania
                    LEFT JOIN gf_cuenta cimp ON
                        cimp.codi_cuenta = cim.codi_cuenta AND cimp.parametrizacionanno = $pannocompania
                    LEFT JOIN gf_centro_costo cnp ON
                        cnp.nombre = cn.nombre AND cnp.parametrizacionanno = $pannocompania
                    LEFT JOIN gf_rubro_pptal rbg ON
                        rb.codi_presupuesto = rbg.codi_presupuesto AND rbg.parametrizacionanno = $pannocompania
                    LEFT JOIN gf_concepto cg ON
                        c.nombre = cg.nombre AND cg.parametrizacionanno = $pannocompania");
                for ($i = 0; $i < count($row); $i++) {
                    $id_crc             = $row[$i][0];
                    $concepto_rubro     = $row[$i][1];
                    $concepto           = $row[$i]['concepto'];
                    $rubro              = $row[$i]['rubro'];
                    
                    $cuenta_debito      = $row[$i]['cuenta_debito_g'];
                    $cuenta_credito     = $row[$i]['cuenta_credito_g'];
                    $centro_costo       = $row[$i]['centro_g'];
                    $proyecto           = $row[$i]['proyecto'];
                    $cuenta_iva         = $row[$i]['cuenta_iva_g'];
                    $cuenta_impoconsumo = $row[$i]['cuenta_impo_g'];
                    $tercero            = $row[$i]['tercero'];
                    $rubro_g            = $row[$i]['rubro_g'];
                    $concepto_g         = $row[$i]['concepto_g'];
                    $concepto_rubro_g   = "";
                    #*** Verificar Si Existe Concepto Rubro ***#
                    $rowcn = $con->Listar("SELECT * FROM gf_concepto_rubro 
                            WHERE concepto = $concepto_g AND rubro = $rubro_g");
                    if(count($rowcn)>0){
                        $concepto_rubro_g = $rowcn[0][0];
                    } else {
                        $sql_cons ="INSERT INTO `gf_concepto_rubro`  
                            ( `concepto`, 
                            `rubro`) 
                        VALUES (:concepto, 
                            :rubro)";
                        $sql_dato = array(
                            array(":concepto",$concepto_g),
                            array(":rubro",$rubro_g),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $rowcn = $con->Listar("SELECT * FROM gf_concepto_rubro 
                            WHERE concepto = $concepto_g AND rubro = $rubro_g");
                            $concepto_rubro_g = $rowcn[0][0];
                        }
                    }
                    if(!empty($concepto_rubro_g)){
                        $sql_cons ="INSERT INTO `gf_concepto_rubro_cuenta`  
                            ( `concepto_rubro`, 
                            `cuenta_debito`,`cuenta_credito`,
                            `centrocosto`,`proyecto`,
                            `cuenta_iva`,`cuenta_impoconsumo`,
                            `tercero`) 
                        VALUES (:concepto_rubro, 
                            :cuenta_debito, :cuenta_credito,
                            :centrocosto, :proyecto, 
                            :cuenta_iva,:cuenta_impoconsumo, 
                            :tercero)";
                        $sql_dato = array(
                            array(":concepto_rubro",$concepto_rubro_g),
                            array(":cuenta_debito",$cuenta_debito),
                            array(":cuenta_credito",$cuenta_credito),
                            array(":centrocosto",$centro_costo),
                            array(":proyecto",$proyecto),
                            array(":cuenta_iva",$cuenta_iva),
                            array(":cuenta_impoconsumo",$cuenta_impoconsumo),
                            array(":tercero",$tercero),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Bancos ***#
            case 9:
                $pannocompania    = $_REQUEST['anno'];
                $row = $con->Listar("SELECT * FROM gf_cuenta_bancaria WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_cuenta_bancaria  
                        WHERE parametrizacionanno = $pannocompania AND numerocuenta='".$row[$t][1]."'");
                
                    if(count($rowe)>0){ 
                        if($_REQUEST['igual']=='1'){
                            $cuenta  = $row[$t][5];
                            $codc    = $con->Listar("SELECT cp.* FROM gf_cuenta c 
                            LEFT JOIN gf_cuenta cp ON c.codi_cuenta = cp.codi_cuenta 
                            and cp.parametrizacionanno = $pannocompania 
                            where c.id_unico = $cuenta");
                            if(count($codc)>0){
                                $sql_cons ="UPDATE `gf_cuenta_bancaria`  
                                        SET `cuenta`=:cuenta
                                        WHERE `id_unico`=:id_unico ";
                                    $sql_dato = array(
                                        array(":cuenta",$codc[0][0]),
                                        array(":id_unico",$rowe[0][0]),  
                                    );
                                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                            }                                   
                        }
                        #*** Guardar Cuenta Bancaria Tercero ***#
                        $sql_cons ="INSERT INTO `gf_cuenta_bancaria_tercero`  
                            ( `cuentabancaria`, 
                            `tercero`) 
                        VALUES (:cuentabancaria, 
                            :tercero)";
                        $sql_dato = array(
                            array(":cuentabancaria",$rowe[0][0]),
                            array(":tercero",$compania),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    } else {
                        $sql_cons ="INSERT INTO `gf_cuenta_bancaria`  
                            ( `numerocuenta`, 
                            `descripcion`,`banco`,
                            `tipocuenta`,`recursofinanciero`,
                            `formato`,`parametrizacionanno`,
                            `destinacion`) 
                        VALUES (:numerocuenta, 
                            :descripcion, :banco,
                            :tipocuenta,:recursofinanciero,
                            :formato,:parametrizacionanno,
                            :destinacion)";
                        $sql_dato = array(
                            array(":numerocuenta",$row[$t][1]),
                            array(":descripcion",$row[$t][2]),
                            array(":banco",$row[$t][3]),
                            array(":tipocuenta",$row[$t][4]),
                            array(":recursofinanciero",$row[$t][6]),
                            array(":formato",$row[$t][7]),
                            array(":parametrizacionanno",$pannocompania),
                            array(":destinacion",$row[$t][9]),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                            #** Buscar Id Creada **#
                            $cb = $con->Listar("SELECT id_unico FROM gf_cuenta_bancaria 
                                WHERE numerocuenta =".$row[$t][1]." 
                                AND parametrizacionanno=$pannocompania");
                            $cuentab = $cb[0][0];
                            #*** Actualizar Cuenta ***# 
                            if($_REQUEST['igual']=='1'){
                                $cuenta  = $row[$t][5];
                                $codc    = $con->Listar("SELECT cp.* FROM gf_cuenta c 
                                LEFT JOIN gf_cuenta cp ON c.codi_cuenta = cp.codi_cuenta 
                                and cp.parametrizacionanno = $pannocompania 
                                where c.id_unico = $cuenta");
                                if(count($codc)>0){
                                    $sql_cons ="UPDATE `gf_cuenta_bancaria`  
                                            SET `cuenta`=:cuenta
                                            WHERE `id_unico`=:id_unico ";
                                        $sql_dato = array(
                                            array(":cuenta",$codc[0][0]),
                                            array(":id_unico",$cuentab),  
                                        );
                                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                                }                                   
                            }
                            #*** Guardar Cuenta Bancaria Tercero ***#
                            $sql_cons ="INSERT INTO `gf_cuenta_bancaria_tercero`  
                                ( `cuentabancaria`, 
                                `tercero`) 
                            VALUES (:cuentabancaria, 
                                :tercero)";
                            $sql_dato = array(
                                array(":cuentabancaria",$cuentab),
                                array(":tercero",$compania),
                            );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        }
                    }
                }
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Distribución ***#
            case 10:
                $pannocompania    = $_REQUEST['anno'];
                $html = '';
                
                $row = $con->Listar("SELECT DISTINCT cpa.id_unico, ccpa.id_unico, ctapa.id_unico ,
                    cf.porcentaje 
                    FROM gf_configuracion_distribucion cf
                    LEFT JOIN gf_concepto c ON cf.concepto = c.id_unico 
                    LEFT JOIN gf_concepto cpa ON c.nombre = cpa.nombre AND cpa.parametrizacionanno = $pannocompania
                    LEFT JOIN gf_centro_costo cc ON cf.centro_costo = cc.id_unico
                    LEFT JOIN gf_centro_costo ccpa ON cc.nombre = ccpa.nombre AND ccpa.parametrizacionanno = $pannocompania 
                    LEFT JOIN gf_cuenta cta ON cf.cuenta = cta.id_unico 
                    LEFT JOIN gf_cuenta ctapa ON cta.codi_cuenta = ctapa.codi_cuenta AND ctapa.parametrizacionanno = $pannocompania 
                    WHERE c.parametrizacionanno = $panno  
                    AND cpa.id_unico IS NOT NULL
                    AND ccpa.id_unico IS NOT NULL
                    AND ctapa.id_unico IS NOT NULL");
                for ($t = 0; $t < count($row); $t++) {
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_configuracion_distribucion  
                        WHERE concepto = ".$row[$t][0]." 
                        AND centro_costo = ".$row[$t][1]." 
                        AND cuenta=".$row[$t][2]);                
                    if(count($rowe)>0){ 
                    } else {
                        $sql_cons ="INSERT INTO `gf_configuracion_distribucion`  
                            ( `concepto`, 
                            `centro_costo`,`cuenta`,
                            `porcentaje`) 
                        VALUES (:concepto, 
                            :centro_costo, :cuenta,
                            :porcentaje)";
                        $sql_dato = array(
                            array(":concepto",$row[$t][0]),
                            array(":centro_costo",$row[$t][1]),
                            array(":cuenta",$row[$t][2]),
                            array(":porcentaje",$row[$t][3]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Traslado ***#
            case 11:
                $pannocompania    = $_REQUEST['anno'];
                
                $row = $con->Listar("SELECT DISTINCT ctpa.id_unico, ccpa.id_unico, ctdpa.id_unico , ccdpa.id_unico , ctcpa.id_unico , cccpa.id_unico 
                FROM gf_configuracion_traslado cft 
                LEFT JOIN gf_cuenta ct ON cft.cuenta_traslado = ct.id_unico 
                LEFT JOIN gf_cuenta ctpa ON ct.codi_cuenta = ctpa.codi_cuenta AND ctpa.parametrizacionanno = $pannocompania 
                LEFT JOIN gf_centro_costo cc ON cft.centro_costo = cc.id_unico 
                LEFT JOIN gf_centro_costo ccpa ON cc.nombre = ccpa.nombre AND ccpa.parametrizacionanno = $pannocompania 
                LEFT JOIN gf_cuenta ctd ON cft.cuenta_debito = ctd.id_unico 
                LEFT JOIN gf_cuenta ctdpa ON ctd.codi_cuenta = ctdpa.codi_cuenta AND ctdpa.parametrizacionanno = $pannocompania 
                LEFT JOIN gf_centro_costo ccd ON cft.centro_costo_debito = ccd.id_unico 
                LEFT JOIN gf_centro_costo ccdpa ON ccd.nombre = ccdpa.nombre AND ccdpa.parametrizacionanno = $pannocompania 
                LEFT JOIN gf_cuenta ctc ON cft.cuenta_credito = ctc.id_unico 
                LEFT JOIN gf_cuenta ctcpa ON ctc.codi_cuenta = ctcpa.codi_cuenta AND ctcpa.parametrizacionanno = $pannocompania
                LEFT JOIN gf_centro_costo ccc ON cft.centro_costo_credito = ccc.id_unico 
                LEFT JOIN gf_centro_costo cccpa ON ccc.nombre = cccpa.nombre AND cccpa.parametrizacionanno = $pannocompania 
                WHERE ct.parametrizacionanno = $panno  
                AND ctpa.id_unico IS NOT NULL 
                AND ccpa.id_unico IS NOT NULL 
                AND ctdpa.id_unico IS NOT NULL 
                AND ccdpa.id_unico IS NOT NULL 
                AND ctcpa.id_unico IS NOT NULL 
                AND cccpa.id_unico IS NOT NULL ");
                for ($t = 0; $t < count($row); $t++) {
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_configuracion_traslado  
                        WHERE cuenta_traslado = ".$row[$t][0]." 
                        AND centro_costo = ".$row[$t][1]." 
                        AND cuenta_debito=".$row[$t][2]."
                        AND centro_costo_debito= ".$row[$t][3]."
                        AND cuenta_credito= ".$row[$t][4]."
                        AND centro_costo_credito= ".$row[$t][5]);                
                    if(count($rowe)>0){ 
                    } else {
                        $sql_cons ="INSERT INTO `gf_configuracion_traslado`  
                            ( `cuenta_traslado`, `centro_costo`,
                            `cuenta_debito`,`centro_costo_debito`, 
                            `cuenta_credito`, `centro_costo_credito`) 
                        VALUES (:cuenta_traslado, :centro_costo, 
                            :cuenta_debito,:centro_costo_debito,
                            :cuenta_credito, :centro_costo_credito)";
                        $sql_dato = array(
                            array(":cuenta_traslado",$row[$t][0]),
                            array(":centro_costo",$row[$t][1]),
                            array(":cuenta_debito",$row[$t][2]),
                            array(":centro_costo_debito",$row[$t][3]),
                            array(":cuenta_credito",$row[$t][4]),
                            array(":centro_costo_credito",$row[$t][5]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Depreciación ***#
            case 12:
                $pannocompania    = $_REQUEST['anno'];
                
                $row = $con->Listar("SELECT DISTINCT ca.plan_inventario,  
                    IF(ca.tipo_movimiento IS NULL, 'NULL',ca.tipo_movimiento), 
                    cdpa.id_unico, ccpa.id_unico , 
                    IF(cbpa.id_unico IS NULL, 'NULL',cbpa.id_unico) 
                FROM gf_configuracion_almacen ca 
                LEFT JOIN gf_cuenta cd ON ca.cuenta_debito = cd.id_unico 
                LEFT JOIN gf_cuenta cdpa ON cd.codi_cuenta = cdpa.codi_cuenta AND cdpa.parametrizacionanno = $pannocompania 
                LEFT JOIN gf_cuenta cc ON ca.cuenta_credito = cc.id_unico 
                LEFT JOIN gf_cuenta ccpa ON cc.codi_cuenta = ccpa.codi_cuenta AND ccpa.parametrizacionanno = $pannocompania 
                LEFT JOIN gf_cuenta cb ON ca.cuenta_baja  = cb.id_unico 
                LEFT JOIN gf_cuenta cbpa ON cb.codi_cuenta = cbpa.codi_cuenta AND cbpa.parametrizacionanno = $pannocompania 
                WHERE ca.parametrizacion_anno = $panno
                AND cdpa.id_unico IS NOT NULL 
                AND ccpa.id_unico IS NOT NULL 
                AND IF(ca.cuenta_baja IS NOT NULL, cbpa.id_unico IS NOT NULL, 1 )");
                for ($t = 0; $t < count($row); $t++) {
                    #** Buscar Si Ya Existe **#
                    $rowe = $con->Listar("SELECT * FROM gf_configuracion_almacen  
                        WHERE plan_inventario = ".$row[$t][0]." 
                        AND tipo_movimiento = ".$row[$t][1]." 
                        AND cuenta_debito=".$row[$t][2]."
                        AND cuenta_credito= ".$row[$t][3]."
                        AND cuenta_baja= ".$row[$t][4]);   
                    if(count($rowe)>0){ 
                    } else {
                        if($row[$t][1]=='NULL'){
                            $tm = NULL;
                        } else {
                            $tm = $row[$t][1];
                        }
                        if($row[$t][4]=='NULL'){
                            $cb = NULL;
                        } else {
                            $cb = $row[$t][4];
                        }
                        $sql_cons ="INSERT INTO `gf_configuracion_almacen`  
                            ( `plan_inventario`, `tipo_movimiento`,
                            `cuenta_debito`,`cuenta_credito`, 
                            `cuenta_baja`,`parametrizacion_anno`) 
                        VALUES (:plan_inventario, :tipo_movimiento, 
                            :cuenta_debito,:cuenta_credito, 
                            :cuenta_baja,:parametrizacion_anno)";
                        $sql_dato = array(
                            array(":plan_inventario",$row[$t][0]),
                            array(":tipo_movimiento",$tm),
                            array(":cuenta_debito",$row[$t][2]),
                            array(":cuenta_credito",$row[$t][3]),
                            array(":cuenta_baja",$cb),
                            array(":parametrizacion_anno",$pannocompania),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Amortizaciones ***#
            case 13:
                $pannocompania    = $_REQUEST['anno'];                
                $row = $con->Listar("SELECT DISTINCT am.id_unico, cpa.id_unico, ctpa.id_unico 
                    FROM gf_comprobante_pptal cp
                    LEFT JOIN gf_tipo_comprobante_pptal tp ON cp.tipocomprobante = tp.id_unico
                    LEFT JOIN gf_tipo_comprobante tc ON tc.comprobante_pptal = tp.id_unico
                    LEFT JOIN gf_comprobante_cnt cn ON tc.id_unico = cn.tipocomprobante AND cp.numero = cn.numero
                    LEFT JOIN gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal 
                    LEFT JOIN gf_concepto_rubro cr ON dcp.conceptoRubro = cr.id_unico 
                    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                    LEFT JOIN gf_amortizacion am ON dcp.id_unico = am.detallecomprobantepptal 
                    LEFT JOIN gf_detalle_amortizacion da ON am.id_unico = da.amortizacion 
                    LEFT JOIN gf_concepto cpa ON c.nombre = cpa.nombre AND cpa.parametrizacionanno = $pannocompania 
                    LEFT JOIN gf_cuenta cta ON am.cuenta_debito = cta.id_unico 
                    LEFT JOIN gf_cuenta ctpa ON cta.codi_cuenta = ctpa.codi_cuenta and ctpa.parametrizacionanno = $pannocompania 
                    WHERE tp.clasepptal = 16 AND tp.tipooperacion = 1 AND cn.id_unico IS NOT NULL 
                    AND cp.parametrizacionanno = $panno 
                    AND c.amortizable = 1 AND am.id_unico IS NOT NULL
                    AND da.comprobante IS NULL 
                    AND cpa.id_unico IS NOT NULL 
                    and ctpa.id_unico IS NOT NULL");
                for ($t = 0; $t < count($row); $t++) {
                    $sql_cons ="UPDATE `gf_amortizacion`  
                        SET `concepto`=:concepto,  
                            `cuenta_debito`=:cuenta_debito 
                        WHERE `id_unico`=:id_unico";
                    $sql_dato = array(
                        array(":concepto",$row[$t][1]),
                        array(":cuenta_debito",$row[$t][2]),
                        array(":id_unico",$row[$t][0]),   
                    );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato); 
                    if(empty($obj_resp)){
                        $d +=1;
                    }
                }
                $rowds = $con->Listar("SELECT DISTINCT  DISTINCT c.nombre, tp.codigo, cp.numero  
                    FROM gf_comprobante_pptal cp
                    LEFT JOIN gf_tipo_comprobante_pptal tp ON cp.tipocomprobante = tp.id_unico
                    LEFT JOIN gf_tipo_comprobante tc ON tc.comprobante_pptal = tp.id_unico
                    LEFT JOIN gf_comprobante_cnt cn ON tc.id_unico = cn.tipocomprobante AND cp.numero = cn.numero
                    LEFT JOIN gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal 
                    LEFT JOIN gf_concepto_rubro cr ON dcp.conceptoRubro = cr.id_unico 
                    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                    LEFT JOIN gf_amortizacion am ON dcp.id_unico = am.detallecomprobantepptal 
                    LEFT JOIN gf_detalle_amortizacion da ON am.id_unico = da.amortizacion 
                    LEFT JOIN gf_concepto cpa ON c.nombre = cpa.nombre AND cpa.parametrizacionanno = $pannocompania 
                    LEFT JOIN gf_cuenta cta ON am.cuenta_debito = cta.id_unico 
                    LEFT JOIN gf_cuenta ctpa ON cta.codi_cuenta = ctpa.codi_cuenta and ctpa.parametrizacionanno = $pannocompania 
                    WHERE tp.clasepptal = 16 AND tp.tipooperacion = 1 AND cn.id_unico IS NOT NULL 
                    AND cp.parametrizacionanno = $panno 
                    AND c.amortizable = 1 AND am.id_unico IS NOT NULL
                    AND da.comprobante IS NULL 
                    AND (cpa.id_unico IS NULL OR ctpa.id_unico IS NULL)");
                $html = '';
                if(count($rowds)>0){
                    $html .= 'Conceptos Sin Configurar:'.'<br/>';
                    for ($s = 0; $s < count($rowds); $s++) {
                        $html .= $rowds[$s][0].': '.$rowds[$s][1].' - '.$rowds[$s][2];    
                    }
                }
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos); 
            break;
            #*** Protocolos Informes ***#
            case 14:
                $html .='';
                $pannocompania    = $_REQUEST['anno'];
                #* 1- clasifdepcgr
                $row = $con->Listar("SELECT * FROM clasifdepcgr WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM clasifdepcgr WHERE parametrizacionanno = $pannocompania) ");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM clasifdepcgr   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `clasifdepcgr`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 2- codigo_cgr
                $row = $con->Listar("SELECT * FROM codigo_cgr WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM codigo_cgr WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM codigo_cgr   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `codigo_cgr`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 3- codigo_personal
                $row = $con->Listar("SELECT * FROM codigo_personal WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM codigo_personal WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM codigo_personal   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `codigo_personal`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 4- consecutivo
                $row = $con->Listar("SELECT * FROM consecutivo WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM consecutivo WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM consecutivo   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `consecutivo`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 5- destinacion
                $row = $con->Listar("SELECT * FROM destinacion WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM destinacion WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM destinacion   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `destinacion`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 6- ejecucion_ingresos
                $row = $con->Listar("SELECT * FROM ejecucion_ingresos WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM ejecucion_ingresos WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM ejecucion_ingresos   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `ejecucion_ingresos`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 7- ejecucion_presupuestal
                $row = $con->Listar("SELECT * FROM ejecucion_presupuestal WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM ejecucion_presupuestal WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM ejecucion_presupuestal   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `ejecucion_presupuestal`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 8- entidades
                $row = $con->Listar("SELECT * FROM entidades WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM entidades WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM entidades   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `entidades`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 9- finalidad                
                $row = $con->Listar("SELECT * FROM finalidad WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM finalidad WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM finalidad   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `finalidad`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 10- fuentes_ejecucion                
                $row = $con->Listar("SELECT * FROM fuentes_ejecucion WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM fuentes_ejecucion WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM fuentes_ejecucion   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `fuentes_ejecucion`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 11- fuentes_funcionamiento                
                $row = $con->Listar("SELECT * FROM fuentes_funcionamiento WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM fuentes_funcionamiento WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM fuentes_funcionamiento   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `fuentes_funcionamiento`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 12- fuentes_servicio_deuda
                $row = $con->Listar("SELECT * FROM fuentes_servicio_deuda WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM fuentes_servicio_deuda WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM fuentes_servicio_deuda   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `fuentes_servicio_deuda`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 13- fuente_inversion_2013
                $row = $con->Listar("SELECT * FROM fuente_inversion_2013 WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM fuente_inversion_2013 WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM fuente_inversion_2013   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `fuente_inversion_2013`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 14- gastos_funcionamiento
                $row = $con->Listar("SELECT * FROM gastos_funcionamiento WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM gastos_funcionamiento WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gastos_funcionamiento   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gastos_funcionamiento`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 15- gastos_inversion                
                $row = $con->Listar("SELECT * FROM gastos_inversion WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM gastos_inversion WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gastos_inversion   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gastos_inversion`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 16- 	niif
                $row = $con->Listar("SELECT * FROM niif WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM niif WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM niif   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `niif`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 17- origen_especifico
                $row = $con->Listar("SELECT * FROM origen_especifico WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM origen_especifico WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM origen_especifico   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `origen_especifico`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 18- programacion_gastos
                $row = $con->Listar("SELECT * FROM programacion_gastos WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM programacion_gastos WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM programacion_gastos   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `programacion_gastos`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 19- programacion_ingresos
                $row = $con->Listar("SELECT * FROM programacion_ingresos WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM programacion_ingresos WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM programacion_ingresos   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `programacion_ingresos`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 20- recurso
                $row = $con->Listar("SELECT * FROM recurso WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM recurso WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM recurso   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `recurso`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 21- 	reporte_cuentas_por_pagar
                $row = $con->Listar("SELECT * FROM reporte_cuentas_por_pagar WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM reporte_cuentas_por_pagar WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM reporte_cuentas_por_pagar   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `reporte_cuentas_por_pagar`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 22- 	reporte_informacion
                $row = $con->Listar("SELECT * FROM reporte_informacion WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM reporte_informacion WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM reporte_informacion   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `reporte_informacion`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 23- 	reporte_reservas_pptal 
                $row = $con->Listar("SELECT * FROM reporte_reservas_pptal WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM reporte_reservas_pptal WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM reporte_reservas_pptal   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `reporte_reservas_pptal`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 24- reporte_tesoreria
                $row = $con->Listar("SELECT * FROM reporte_tesoreria WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM reporte_tesoreria WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM reporte_tesoreria   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `reporte_tesoreria`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 25- rgclasifderacn
                $row = $con->Listar("SELECT * FROM rgclasifderacn WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM rgclasifderacn WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM rgclasifderacn   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `rgclasifderacn`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 26- 	rgclasiforigesp
                $row = $con->Listar("SELECT * FROM rgclasiforigesp WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM rgclasiforigesp WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM rgclasiforigesp   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `rgclasiforigesp`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 27- rgclasifrec
                $row = $con->Listar("SELECT * FROM rgclasifrec WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM rgclasifrec WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM rgclasifrec   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `rgclasifrec`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 28- 	rgclasiftercgr
                $row = $con->Listar("SELECT * FROM rgclasiftercgr WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM rgclasiftercgr WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM rgclasiftercgr   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `rgclasiftercgr`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 29- 	rgsitfond
                $row = $con->Listar("SELECT * FROM rgsitfond WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM rgsitfond WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM rgsitfond   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `rgsitfond`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 30- saldos_disponibles
                $row = $con->Listar("SELECT * FROM saldos_disponibles WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM saldos_disponibles WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM saldos_disponibles   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `saldos_disponibles`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 31- servicio_deuda
                $row = $con->Listar("SELECT * FROM servicio_deuda WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM servicio_deuda WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM servicio_deuda   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `servicio_deuda`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 32- sia_gastos
                $row = $con->Listar("SELECT * FROM sia_gastos WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM sia_gastos WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM sia_gastos   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `sia_gastos`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 33-  sia_ingresos
                $row = $con->Listar("SELECT * FROM sia_ingresos WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM sia_ingresos WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM sia_ingresos   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `sia_ingresos`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 34- situacion_fondo
                $row = $con->Listar("SELECT * FROM situacion_fondo WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM situacion_fondo WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM situacion_fondo   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `situacion_fondo`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 35- tipo_acto_administrativo
                $row = $con->Listar("SELECT * FROM tipo_acto_administrativo WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM tipo_acto_administrativo WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM tipo_acto_administrativo   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `tipo_acto_administrativo`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 36- tipo_deuda
                $row = $con->Listar("SELECT * FROM tipo_deuda WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM tipo_deuda WHERE parametrizacionanno = $pannocompania)" );
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM tipo_deuda   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `tipo_deuda`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 37- 	tipo_operacion
                $row = $con->Listar("SELECT * FROM tipo_operacion WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM tipo_operacion WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM tipo_operacion   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `tipo_operacion`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 38- tipo_reporte_tesoreria
                $row = $con->Listar("SELECT * FROM tipo_reporte_tesoreria WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM tipo_reporte_tesoreria WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM tipo_reporte_tesoreria   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `tipo_reporte_tesoreria`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 39- tipo_vinculacion
                $row = $con->Listar("SELECT * FROM tipo_vinculacion WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM tipo_vinculacion WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM tipo_vinculacion   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `tipo_vinculacion`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 40- unidad_ejecutora
                $row = $con->Listar("SELECT * FROM unidad_ejecutora WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM unidad_ejecutora WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM unidad_ejecutora   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `unidad_ejecutora`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 41- clasifdepcgr
                $row = $con->Listar("SELECT * FROM vigencia WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM vigencia WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM vigencia   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `vigencia`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #*****************CUIPO #*********************
                #* 42- cuipo_cpc
                $row = $con->Listar("SELECT * FROM cuipo_cpc WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_cpc WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_cpc   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_cpc`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 43- cuipo_destinacion
                $row = $con->Listar("SELECT * FROM cuipo_destinacion WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_destinacion WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_destinacion   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_destinacion`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 44- cuipo_fecha_norma
                $row = $con->Listar("SELECT * FROM cuipo_fecha_norma WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_fecha_norma WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_fecha_norma   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_fecha_norma`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 45- cuipo_fuentes_f
                $row = $con->Listar("SELECT * FROM cuipo_fuentes_f WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_fuentes_f WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_fuentes_f   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_fuentes_f`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 46- cuipo_gastos
                $row = $con->Listar("SELECT * FROM cuipo_gastos WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_gastos WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_gastos   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_gastos`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 47- cuipo_ingresos
                $row = $con->Listar("SELECT * FROM cuipo_ingresos WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_ingresos WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_ingresos   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_ingresos`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 48- cuipo_norma
                $row = $con->Listar("SELECT * FROM cuipo_norma WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_norma WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_norma   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_norma`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 49- cuipo_politicas
                $row = $con->Listar("SELECT * FROM cuipo_politicas WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_politicas WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_politicas   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_politicas`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 50- cuipo_programat_mga1
                $row = $con->Listar("SELECT * FROM cuipo_programat_mga1 WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_programat_mga1 WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_programat_mga1   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_programat_mga1`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 51- cuipo_programat_mga2
                $row = $con->Listar("SELECT * FROM cuipo_programat_mga2 WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_programat_mga2 WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_programat_mga2   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_programat_mga2`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 52- cuipo_seccion_pref5
                $row = $con->Listar("SELECT * FROM cuipo_seccion_pref5 WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_seccion_pref5 WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_seccion_pref5   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_seccion_pref5`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 53- cuipo_seccion_presup
                $row = $con->Listar("SELECT * FROM cuipo_seccion_presup WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_seccion_presup WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_seccion_presup   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_seccion_presup`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 54- cuipo_sector
                $row = $con->Listar("SELECT * FROM cuipo_sector WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_sector WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_sector   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_sector`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 55- cuipo_situacion_fondos
                $row = $con->Listar("SELECT * FROM cuipo_situacion_fondos WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_situacion_fondos WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_situacion_fondos   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_situacion_fondos`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 56- cuipo_terceros
                $row = $con->Listar("SELECT * FROM cuipo_terceros WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_terceros WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_terceros   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_terceros`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 57- cuipo_tipo_norma
                $row = $con->Listar("SELECT * FROM cuipo_tipo_norma WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_tipo_norma WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_tipo_norma   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_tipo_norma`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* 58- cuipo_vigencia_gasto
                $row = $con->Listar("SELECT * FROM cuipo_vigencia_gasto WHERE parametrizacionanno = $panno 
                    AND id_unico NOT IN (SELECT id_unico FROM cuipo_vigencia_gasto WHERE parametrizacionanno = $pannocompania)");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM cuipo_vigencia_gasto   
                        WHERE parametrizacionanno = $pannocompania AND id_unico='".$row[$t][0]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `cuipo_vigencia_gasto`  
                            ( `id_unico`,`nombre`,`parametrizacionanno`) 
                        VALUES (:id_unico,:nombre,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":id_unico",$row[$t][0]),
                            array(":nombre",$row[$t][1]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }

                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos);
            break;
            #*** Configuración Informes ***#
            case 15:
                $pannocompania    = $_REQUEST['anno'];
                $row = $con->Listar("SELECT DISTINCT rfpa.id_unico, 
                    h.id_destino, h.origen, h.destino, 
                    th.tabla_destino 
                    FROM gn_homologaciones h 
                    LEFT JOIN gf_rubro_fuente rf ON h.id_origen = rf.id_unico 
                    LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico
                    LEFT JOIN gf_fuente f oN rf.fuente = f.id_unico 
                    LEFT JOIN gn_tabla_homologable th ON h.origen = th.id
                    LEFT JOIN gn_informe i ON th.informe = i.id 
                    LEFT JOIN gf_rubro_pptal rbpa ON rb.codi_presupuesto = rbpa.codi_presupuesto AND rbpa.parametrizacionanno = $pannocompania 
                    LEFT JOIN gf_fuente fpa ON f.nombre = fpa.nombre AND fpa.parametrizacionanno = $pannocompania 
                    LEFT JOIN gf_rubro_fuente rfpa ON rfpa.rubro = rbpa.id_unico AND rfpa.fuente = fpa.id_unico 
                    WHERE i.clase_informe != 3 AND rb.parametrizacionanno = $panno 
                    AND rfpa.id_unico IS NOT NULL 
                    ");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gn_homologaciones   
                        WHERE id_origen='".$row[$t][0]."' 
                        AND id_destino= '".$row[$t][1]."'
                        AND origen= '".$row[$t][2]."'
                        AND destino= '".$row[$t][3]."'");
                    if(count($rowe)>0){ } else {
                        $th = $row[$t][4];
                        $rowbd = $con->Listar("SELECT * FROM ".$row[$t][4]." 
                            WHERE id_unico = '".$row[$t][1]."' AND parametrizacionanno = $pannocompania");
                        if(count($rowbd)>0){
                            $sql_cons ="INSERT INTO `gn_homologaciones`  
                                ( `id_origen`,`id_destino`,
                                `origen`,`destino`) 
                            VALUES (:id_origen,:id_destino,
                                :origen,:destino)";
                            $sql_dato = array(
                                array(":id_origen",$row[$t][0]),
                                array(":id_destino",$row[$t][1]),   
                                array(":origen",$row[$t][2]),   
                                array(":destino",$row[$t][3]),
                            );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                            if(empty($obj_resp)){
                                $d +=1;
                            }
                        }
                    }
                }

                $row = $con->Listar("SELECT DISTINCT cpa.id_unico, 
                    h.id_destino, h.origen, h.destino, th.tabla_destino 
                    FROM gn_homologaciones h 
                    LEFT JOIN gf_cuenta c ON h.id_origen = c.id_unico 
                    LEFT JOIN gn_tabla_homologable th ON h.origen = th.id
                    LEFT JOIN gn_informe i ON th.informe = i.id 
                    LEFT JOIN gf_cuenta cpa ON c.codi_cuenta = cpa.codi_cuenta AND cpa.parametrizacionanno = $pannocompania 
                    WHERE i.clase_informe = 3 AND c.parametrizacionanno = $panno 
                    AND cpa.id_unico IS NOT NULL ");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gn_homologaciones   
                        WHERE id_origen='".$row[$t][0]."' 
                        AND id_destino= '".$row[$t][1]."'
                        AND origen= '".$row[$t][2]."'
                        AND destino= '".$row[$t][3]."'");
                    if(count($rowe)>0){ } else {
                        $rowbd = $con->Listar("SELECT * FROM ".$row[$t][4]." 
                            WHERE id_unico = '".$row[$t][1]."' AND parametrizacionanno = $pannocompania");
                        if(count($rowbd)>0){
                            $sql_cons ="INSERT INTO `gn_homologaciones`  
                                ( `id_origen`,`id_destino`,
                                `origen`,`destino`) 
                            VALUES (:id_origen,:id_destino,
                                :origen,:destino)";
                            $sql_dato = array(
                                array(":id_origen",$row[$t][0]),
                                array(":id_destino",$row[$t][1]),   
                                array(":origen",$row[$t][2]),   
                                array(":destino",$row[$t][3]),
                            );
                            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                            if(empty($obj_resp)){
                                $d +=1;
                            }
                        }
                    }
                }
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos);
            break;
            #*** Exógenas ***#
            case 16:
                $html .='';
                $pannocompania    = $_REQUEST['anno'];
                #* Formatos
                $row = $con->Listar("SELECT * FROM gf_formatos_exogenas 
                    WHERE parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_formatos_exogenas   
                        WHERE parametrizacionanno = $pannocompania AND formato='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_formatos_exogenas`  
                            ( `formato`,`nombre`,`cuantia`,`parametrizacionanno`) 
                        VALUES (:formato,:nombre,:cuantia,:parametrizacionanno)";
                        $sql_dato = array(
                            array(":formato",$row[$t][1]),
                            array(":nombre",$row[$t][2]),
                            array(":cuantia",$row[$t][3]),
                            array(":parametrizacionanno",$pannocompania),   
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* Conceptos
                $row = $con->Listar("SELECT fepa.id_unico, ce.codigo, ce.nombre 
                    FROM gf_concepto_exogenas ce 
                    LEFT JOIN gf_formatos_exogenas fe ON ce.formato = fe.id_unico 
                    LEFT JOIN gf_formatos_exogenas fepa ON fe.formato =fepa.formato 
                        AND fepa.parametrizacionanno = $pannocompania 
                    WHERE fe.parametrizacionanno =$panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_concepto_exogenas   
                        WHERE formato ='".$row[$t][0]."' AND codigo='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_concepto_exogenas`  
                            ( `formato`,`codigo`,`nombre`) 
                        VALUES (:formato,:codigo,:nombre)";
                        $sql_dato = array(
                            array(":formato",$row[$t][0]),
                            array(":codigo",$row[$t][1]),
                            array(":nombre",$row[$t][2]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #* Configuracion
                $row = $con->Listar("SELECT cpa.id_unico, cnpa.id_unico  
                    FROM gf_configuracion_exogenas ce 
                    LEFT JOIN gf_cuenta c ON ce.cuenta = c.id_unico 
                    LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
                    LEFT JOIN gf_formatos_exogenas fe ON cn.formato = fe.id_unico 
                    LEFT JOIN gf_cuenta cpa ON c.codi_cuenta = cpa.codi_cuenta 
                        AND cpa.parametrizacionanno = $pannocompania 
                    LEFT JOIN gf_formatos_exogenas fepa ON fe.formato = fepa.formato 
                        AND fepa.parametrizacionanno = $pannocompania  
                    LEFT JOIN gf_concepto_exogenas cnpa ON cn.codigo = cnpa.codigo 
                        AND cnpa.formato = fepa.id_unico 
                    WHERE fe.formato != '2276' AND fe.parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_configuracion_exogenas   
                        WHERE cuenta = '".$row[$t][0]."' AND concepto_exogenas='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_configuracion_exogenas`  
                            ( `cuenta`,`concepto_exogenas`) 
                        VALUES (:cuenta,:concepto_exogenas)";
                        $sql_dato = array(
                            array(":cuenta",$row[$t][0]),
                            array(":concepto_exogenas",$row[$t][1]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                #Nomina
                $row = $con->Listar("SELECT ce.cuenta, cnpa.id_unico  
                    FROM gf_configuracion_exogenas ce 
                    LEFT JOIN gf_concepto_exogenas cn ON ce.concepto_exogenas = cn.id_unico 
                    LEFT JOIN gf_formatos_exogenas fe ON cn.formato = fe.id_unico 
                    LEFT JOIN gf_formatos_exogenas fepa ON fe.formato = fepa.formato 
                        AND fepa.parametrizacionanno = $pannocompania  
                    LEFT JOIN gf_concepto_exogenas cnpa ON cn.codigo = cnpa.codigo 
                        AND cnpa.formato = fepa.id_unico 
                    WHERE fe.formato = '2276' AND fe.parametrizacionanno = $panno");
                for ($t = 0; $t < count($row); $t++) {
                    $rowe = $con->Listar("SELECT * FROM gf_configuracion_exogenas   
                        WHERE cuenta = '".$row[$t][0]."' AND concepto_exogenas='".$row[$t][1]."'");
                    if(count($rowe)>0){ } else {
                        $sql_cons ="INSERT INTO `gf_configuracion_exogenas`  
                            ( `cuenta`,`concepto_exogenas`) 
                        VALUES (:cuenta,:concepto_exogenas)";
                        $sql_dato = array(
                            array(":cuenta",$row[$t][0]),
                            array(":concepto_exogenas",$row[$t][1]),
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $d +=1;
                        }
                    }
                }
                $datos = array("d"=>$d,"rta"=>$html);
                echo json_encode($datos);
            break; 
        }
    break;
    #******************* SUBIR TERCEROS AP ******************************#
    case 3 :
        $tc =0;
        $inputFileName= $_FILES['file']['tmp_name'];                                       
        $objReader = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        for ($a = 2; $a <= $total_filas; $a++) {
            $nombreuno       = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $nombredos       = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $apellidouno     = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $apellidodos     = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $razonsocial     = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            $tipoident       = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
            $documento       = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();
            $digito          = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
            $ciudad          = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
            $correo          = $objWorksheet->getCellByColumnAndRow(9, $a)->getCalculatedValue();
            $direccion       = $objWorksheet->getCellByColumnAndRow(10, $a)->getCalculatedValue();
            $telefono        = $objWorksheet->getCellByColumnAndRow(11, $a)->getCalculatedValue();
            $perfil          = $objWorksheet->getCellByColumnAndRow(12, $a)->getCalculatedValue();
            
            
            #*** Buscar Ciudad ***#
            $rowc = $con->Listar("SELECT * FROM gf_ciudad WHERE nombre = '$ciudad'");
            if(count($rowc)>=0){
                $ciud = $rowc[0][0];
            } else {
                $ciud = NULL;
            }
            #*** Buscar Tipo Identificacion ***#
            $rowti = $con->Listar("SELECT * FROM gf_tipo_identificacion WHERE sigla ='$tipoident'");
            if(count($rowti)>=0){
                $tipoI = $rowti[0][0];
            } else {
                $tipoI = NULL;
            }
            #* Buscar Si Existe Tercero 
            $rowe = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion = $documento AND compania = $compania");
            if(count($rowe)>0){
                $rowbt  = $con->Listar("SELECT * FROM gf_tercero 
                    WHERE compania = $compania AND numeroidentificacion='".$documento."'");
                    $idt    = $rowbt[0][0];
            } else {             
                $sql_cons ="INSERT INTO `gf_tercero`  
                    ( `nombreuno`, `nombredos`,
                    `apellidouno`,`apellidodos`,
                    `razonsocial`,
                    `numeroidentificacion`,`digitoverficacion`,
                    `compania`,`tipoidentificacion`,
                    `ciudadresidencia`,
                    `ciudadidentificacion`,
                    `email`) 
                VALUES (:nombreuno,:nombredos, 
                    :apellidouno,:apellidodos, 
                    :razonsocial,
                    :numeroidentificacion,:digitoverficacion,
                    :compania,:tipoidentificacion,
                    :ciudadresidencia,
                    :ciudadidentificacion,
                    :email)";
                $sql_dato = array(
                    array(":nombreuno",$nombreuno),
                    array(":nombredos",$nombredos),
                    array(":apellidouno",$apellidouno),   
                    array(":apellidodos",$apellidodos),   
                    array(":razonsocial",$razonsocial),
                    array(":numeroidentificacion",$documento),
                    array(":digitoverficacion",$digito),
                    array(":compania",$compania),
                    array(":tipoidentificacion",$tipoI),
                    array(":ciudadresidencia",$ciud),
                    array(":ciudadidentificacion",$ciud),
                    array(":email",$correo),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);

                if(empty($obj_resp)){
                    $tc     +=1;
                    $rowbt  = $con->Listar("SELECT * FROM gf_tercero 
                    WHERE compania = $compania AND numeroidentificacion='".$documento."'");
                    $idt    = $rowbt[0][0];
                }
            }
            #*** Guardar Perfil ***#
            $sql_cons ="INSERT INTO `gf_perfil_tercero`  
                ( `perfil`, 
                `tercero`) 
            VALUES (:perfil, 
                :tercero)";
            $sql_dato = array(
                array(":perfil",$perfil),
                array(":tercero",$idt),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);

            if(!empty($direccion) && (!empty($ciud)|| $ciud!=NULL)){
                #*** Guardar Dirección ***#
                $sql_cons ="INSERT INTO `gf_direccion`  
                    ( `direccion`, `tipo_direccion`,
                    `ciudad_direccion`,`tercero`) 
                VALUES (:direccion, :tipo_direccion,
                    :ciudad_direccion, :tercero)";
                $sql_dato = array(
                    array(":direccion",$direccion),
                    array(":tipo_direccion",7),
                    array(":ciudad_direccion",$ciud),
                    array(":tercero",$idt),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            if(!empty($telefono)){
                #*** Guardar Telefono ***#
                $sql_cons ="INSERT INTO `gf_telefono`  
                    ( `tipo_telefono`, `valor`, `tercero`) 
                VALUES (:tipo_telefono, :valor,:tercero)";
                $sql_dato = array(
                    array(":tipo_telefono",1),
                    array(":valor",$telefono),
                    array(":tercero",$idt),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            }
        }
        echo $tc;
    break;
    
    #******************* SUBIR PLAN CONTABLE ******************************#
    case 4:
        $tc =0;
        $inputFileName= $_FILES['file']['tmp_name'];                                       
        $objReader = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        for ($a = 2; $a <= $total_filas; $a++) {
            $codigo_cuenta      = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $nombre_cuenta      = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $movimiento         = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $centro_costo       = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $tercero            = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            $proyecto           = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
            $naturaleza         = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();
            $cng                = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
            $clase              = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
            $equivalente_va     = $objWorksheet->getCellByColumnAndRow(9, $a)->getCalculatedValue();
            
            $sql_cons ="INSERT INTO `gf_cuenta`  
                ( `codi_cuenta`,`nombre`, 
                `movimiento`,`centrocosto`,
                `auxiliartercero`,`auxiliarproyecto`,
                `parametrizacionanno`,`activa`,
                `dinamica`,`naturaleza`,
                `tipocuentacgn`,`clasecuenta`,
                `cuentapuente`,`equivalente_va`) 
            VALUES (:codi_cuenta, :nombre,
                :movimiento,:centrocosto,
                :auxiliartercero,:auxiliarproyecto, 
                :parametrizacionanno,:activa,
                :dinamica,:naturaleza,
                :tipocuentacgn,:clasecuenta,
                :cuentapuente,:equivalente_va)";
            $sql_dato = array(
                array(":codi_cuenta",$codigo_cuenta),
                array(":nombre",$nombre_cuenta),
                array(":movimiento",$movimiento),   
                array(":centrocosto",$centro_costo),   
                array(":auxiliartercero",$tercero),
                array(":auxiliarproyecto",$proyecto),
                array(":parametrizacionanno",$panno),
                array(":activa",1),
                array(":dinamica",NULL),
                array(":naturaleza",$naturaleza),
                array(":tipocuentacgn",$cng),
                array(":clasecuenta",$clase),
                array(":cuentapuente",NULL),
                array(":equivalente_va",$equivalente_va),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                $tc +=1;
            }
        }
        echo $tc;
    break;
    #******************* SUBIR PLAN PRESUPUESTAL ******************************#
    case 5:
        $tc =0;
        $inputFileName= $_FILES['file']['tmp_name'];                                       
        $objReader = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        for ($a = 2; $a <= $total_filas; $a++) {
            $codigo     = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $nombre     = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $movimiento = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $clase      = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $sector     = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            $equivalente= $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
            
            #***** Buscar Clase Rubro****#
            $cl = $con->Listar("SELECT id_unico FROM gf_tipo_clase_pptal WHERE nombre = '$clase'");
            $tipoc = $cl[0][0];
            
            #*** Secto **#
            $s = $con->Listar("SELECT * FROM gf_sector WHERE nombre = '$sector'");
            if(count($s)>0){
                $sec = $s[0][0];
            } else {
                $sec = NULL;
            }
            
            $sql_cons ="INSERT INTO `gf_rubro_pptal`  
                ( `nombre`,`codi_presupuesto`, 
                `movimiento`,`manpac`,
                `vigencia`,`dinamica`,
                `parametrizacionanno`,`tipoclase`,
                `destino`,`tipovigencia`,
                `sector`,`equivalente`) 
            VALUES (:nombre,:codi_presupuesto, 
                :movimiento,:manpac,
                :vigencia,:dinamica, 
                :parametrizacionanno,:tipoclase,
                :destino,:tipovigencia,
                :sector,:equivalente)";
            $sql_dato = array(
                array(":nombre",$nombre),
                array(":codi_presupuesto",$codigo),
                array(":movimiento",$movimiento),   
                array(":manpac",NULL),   
                array(":vigencia",$panno),
                array(":dinamica",NULL),
                array(":parametrizacionanno",$panno),
                array(":tipoclase",$tipoc),
                array(":destino",NULL),
                array(":tipovigencia",1),
                array(":sector",$sec),
                array(":equivalente",$equivalente),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                $tc +=1;
            }
        }
        echo $tc;
    break;
    
    #************ Validar Copiar Movimientos Por Fuente SGR ***************#
    case 6:
        $fuente     = $_REQUEST['fuente'];
        $anno_c     = $_REQUEST['anno'];
        $anno_cn    = anno($anno_c);
        $html = '';
        $nr   = 0;
        $f    = 0;
        #*** Validar Que La Fuente Exista en el año a copiar ***#
        $rowvf = $con->Listar("SELECT f.* FROM gf_fuente f 
            LEFT JOIN gf_fuente fa ON fa.nombre = f.nombre 
                AND f.parametrizacionanno = $anno_c AND fa.parametrizacionanno = $panno
            WHERE fa.id_unico = $fuente");
        if(count($rowvf)>0 || !empty($rowvf[0][0])){ 
            $fuenteA = $rowvf[0][0];
            #*** Buscar Rubro Fuente de la fuente Año Actual****#
            $rowrf          = $con->Listar("SELECT rf.id_unico,  rb.codi_presupuesto, 
                rb.* 
                FROM gf_rubro_fuente rf 
                LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                WHERE rf.fuente = $fuente");
            #*** Buscar Rubro Fuente Añño Siguiente ***#
            $html = 'Rubros configurados para año '.$anno_cn.'<br/>';
            for ($i = 0; $i < count($rowrf); $i++) {
                $rbf    = $rowrf[$i][0];
                $rowrfs = $con ->Listar("SELECT rf.id_unico 
                    FROM gf_rubro_fuente rf 
                    LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                    LEFT JOIN gf_fuente f ON f.id_unico = rf.fuente 
                    LEFT JOIN gf_fuente fa ON f.nombre = fa.nombre 
                        AND fa.parametrizacionanno = $panno 
                    LEFT JOIN gf_rubro_pptal ra ON rb.codi_presupuesto = ra.codi_presupuesto  
                        AND ra.parametrizacionanno = $panno
                    LEFT JOIN 
                        gf_rubro_fuente rfa ON ra.id_unico = rfa.rubro AND rfa.fuente = fa.id_unico         
                    WHERE rfa.id_unico  =$rbf 
                       AND f.parametrizacionanno = $anno_c and rb.parametrizacionanno = $anno_c");
                if(count($rowrfs)>0 || !empty($rowrfs[0][0])){
                    $html .=$rowrf[$i][1].'<br/>';
                    $nr +=1;
                }

            }
        } else {
            $nr   = 1;
            $f    = 1;
            $html ='Fuente No Existe Para Año '.$anno_cn;
        }
        $datos = array("html"=>$html,"rta"=>$nr);
        echo json_encode($datos);
    break;
    
    #************  Copiar Movimientos Por Fuente SGR ***************#
    case 7:
        $fuente     = $_REQUEST['fuente'];
        $anno_c     = $_REQUEST['anno'];
        $anno_cn    = anno($anno_c);
        $ingresados = 0;
        #*** Validar Que La Fuente Exista en el año a copiar ***#
        $rowvf = $con->Listar("SELECT f.* FROM gf_fuente f 
            LEFT JOIN gf_fuente fa ON fa.nombre = f.nombre 
                AND f.parametrizacionanno = $anno_c AND fa.parametrizacionanno = $panno
            WHERE fa.id_unico = $fuente");
        $fuenteA = $rowvf[0][0];
        #*** Buscar Rubro Fuente de la fuente Año Actual****#
        $rowrf          = $con->Listar("SELECT rf.id_unico,  rb.codi_presupuesto, 
            rb.* 
            FROM gf_rubro_fuente rf 
            LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
            WHERE rf.fuente = $fuente");
        #*** Crear Rubros Y Rubro Fuente ***#
        for ($i = 0; $i < count($rowrf); $i++) {
            $rubro_fan = $rowrf[$i][0];
            $cod_rubro = trim($rowrf[$i][4]);
            #** Buscar si existe rubro **#
            $rowrc = $con->Listar("SELECT * FROM gf_rubro_pptal 
                WHERE codi_presupuesto ='$cod_rubro' AND parametrizacionanno=$anno_c");
            if(count($rowrc) >0 || !empty($rowrc[0][0])){
                $rubro_c = $rowrc[0][0];
            } else { 
                $sql_cons ="INSERT INTO `gf_rubro_pptal`  
                    ( `nombre`,`codi_presupuesto`, 
                    `movimiento`,`manpac`,
                    `vigencia`,`dinamica`,
                    `parametrizacionanno`,`tipoclase`,
                    `destino`,`tipovigencia`,
                    `sector`,`equivalente`) 
                VALUES (:nombre,:codi_presupuesto, 
                    :movimiento,:manpac,
                    :vigencia,:dinamica, 
                    :parametrizacionanno,:tipoclase,
                    :destino,:tipovigencia,
                    :sector,:equivalente)";
                $sql_dato = array(
                    array(":nombre",$rowrf[$i][3]),
                    array(":codi_presupuesto",$rowrf[$i][4]),
                    array(":movimiento",$rowrf[$i][5]),   
                    array(":manpac",$rowrf[$i][6]),   
                    array(":vigencia",$anno_c),
                    array(":dinamica",$rowrf[$i][8]),
                    array(":parametrizacionanno",$anno_c),
                    array(":tipoclase",$rowrf[$i][10]),
                    array(":destino",$rowrf[$i][12]),
                    array(":tipovigencia",$rowrf[$i][13]),
                    array(":sector",$rowrf[$i][14]),
                    array(":equivalente",$rowrf[$i][15]),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($obj_resp)){
                    #** Buscar Rubro Creado **#
                    $rowrc = $con->Listar("SELECT * FROM gf_rubro_pptal 
                        WHERE codi_presupuesto ='$cod_rubro' AND parametrizacionanno=$anno_c");
                    $rubro_c = $rowrc[0][0];
                }
            }
            #** Buscar Si Existe Rubro Fuente creado ***#
            $rfc = $con->Listar("SELECT * FROM gf_rubro_fuente 
                WHERE rubro= $rubro_c AND fuente=$fuenteA");    
            if(count($rfc)>0 || !empty($rfc[0][0])){
                $rubro_fuente = $rfc[0][0];
            } else { 
                #** Registrar Rubro Fuente **#
                $sql_cons ="INSERT INTO `gf_rubro_fuente`  
                    ( `rubro`,`fuente`) 
                VALUES (:rubro,:fuente)";
                $sql_dato = array(
                    array(":rubro",$rubro_c),
                    array(":fuente",$fuenteA),
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                
                $rfc = $con->Listar("SELECT * FROM gf_rubro_fuente 
                WHERE rubro= $rubro_c AND fuente=$fuenteA"); 
                $rubro_fuente = $rfc[0][0];
            }    
            
            #*** Registrar Concepto ***#
            $rowe = $con->Listar("SELECT * FROM gf_concepto   
            WHERE parametrizacionanno = $anno_c AND nombre='".$rowrf[$i][4].' - '.$rowrf[$i][3]."'");
            if(count($rowe)>0){
                $concepto = $rowe[0][0];
            } else {
                $sql_cons ="INSERT INTO `gf_concepto`  
                    ( `nombre`,
                    `clase_concepto`,`parametrizacionanno`) 
                VALUES (:nombre,
                    :clase_concepto,:parametrizacionanno)";
                $sql_dato = array(
                    array(":nombre",$rowrf[$i][4].' - '.$rowrf[$i][3]),
                    array(":clase_concepto",2),   
                    array(":parametrizacionanno",$anno_c),   
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                
                $rowe = $con->Listar("SELECT * FROM gf_concepto   
                WHERE parametrizacionanno = $anno_c AND nombre='".$rowrf[$i][4].' - '.$rowrf[$i][3]."'");
                $concepto = $rowe[0][0];
            }
            #** Buscar Si Existe Concepto Rubro **#
            $rcr = $con->Listar("SELECT * FROM gf_concepto_rubro 
                WHERE rubro=$rubro_c AND concepto =$concepto ");
            if(count($rcr)>0 || !empty($rcr[0][0])){
                $concepto_rubro = $rcr[0][0];
            } else {
                #** Crear Concepto Rubro **#
                $sql_cons ="INSERT INTO `gf_concepto_rubro`  
                    ( `concepto`,
                    `rubro`) 
                VALUES (
                    :concepto,:rubro)";
                $sql_dato = array(
                    array(":concepto",$concepto),
                    array(":rubro",$rowrc[0][0]),    
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                $rcr = $con->Listar("SELECT * FROM gf_concepto_rubro 
                WHERE rubro=$rubro_c AND concepto =$concepto ");
                $concepto_rubro = $rcr[0][0];
            }
            #********* Buscar Detalles pptales Con el rubro fuente ***#
            $rowdt = $con->Listar("SELECT DISTINCT dc.id_unico, dc.descripcion, dc.valor, dc.comprobantepptal, dc.rubrofuente, dc.conceptoRubro, dc.tercero, dc.proyecto, dc.centro_costo, dc.comprobanteafectado, dc.saldo_disponible, dc.clasenom, dc.cantidad, dc.valor_unitario , cn.* 
                FROM gf_detalle_comprobante_pptal dc 
                LEFT JOIN gf_comprobante_pptal cn ON dc.comprobantepptal = cn.id_unico 
                WHERE dc.rubrofuente =$rubro_fan ORDER BY dc.id_unico");
            if(count($rowdt)>0){
                for ($d = 0; $d < count($rowdt); $d++) {
                    #** Datos Comprobante **#
                    $numero = $rowdt[$d][15];
                    $tipo   = $rowdt[$d][22];
                    #*** Buscar Si Comprobante Existe **#
                    $ec = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                        WHERE numero = $numero AND tipocomprobante = $tipo 
                        AND parametrizacionanno =$anno_c ");
                    if($ec>0){
                        $comprobante =$ec[0][0];
                    } else {
                        #Insertar Comprobante 
                        $sql_cons ="INSERT INTO `gf_comprobante_pptal`  
                            ( `numero`,`fecha`, 
                            `fechavencimiento`, `descripcion`, 
                            `numerocontrato`, `parametrizacionanno`, 
                            `clasecontrato`, `tipocomprobante`, 
                            `tercero`, `estado`,
                            `responsable`,`compania`,
                            `usuario`,`fecha_elaboracion`,
                            `valor_abono`) 
                        VALUES (:numero,:fecha, 
                            :fechavencimiento,:descripcion,
                            :numerocontrato,:parametrizacionanno,
                            :clasecontrato,:tipocomprobante,
                            :tercero,:estado,
                            :responsable,:compania,
                            :usuario,:fecha_elaboracion,
                            :valor_abono)";
                        $sql_dato = array(
                            array(":numero",$rowdt[$d][15]),
                            array(":fecha",$rowdt[$d][16]),    
                            array(":fechavencimiento",$rowdt[$d][17]),    
                            array(":descripcion",$rowdt[$d][18]),    
                            array(":numerocontrato",$rowdt[$d][19]), 
                            array(":parametrizacionanno",$anno_c),    
                            array(":clasecontrato",$rowdt[$d][21]),    
                            array(":tipocomprobante",$rowdt[$d][22]),    
                            array(":tercero",$rowdt[$d][23]),    
                            array(":estado",$rowdt[$d][24]),    
                            array(":responsable",$rowdt[$d][25]),    
                            array(":compania",$compania),    
                            array(":usuario",$rowdt[$d][27]),    
                            array(":fecha_elaboracion",$rowdt[$d][28]),    
                            array(":valor_abono",$rowdt[$d][29]),    
                            
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        $ec = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                        WHERE numero = $numero AND tipocomprobante = $tipo 
                        AND parametrizacionanno =$anno_c ");
                        $comprobante =$ec[0][0];
                    }
                    
                    #*** Buscar Si Detalle Existe **#
                    $ec = $con->Listar("SELECT * FROM gf_detalle_comprobante_pptal  
                        WHERE comprobantepptal = $comprobante AND rubrofuente = $rubro_fuente  
                        AND valor =".$rowdt[$d][2]);
                    if(count($ec)>0){
                        
                    } else {
                        #** Si trae comprobante afectado, buscar detalle afectado **#
                        if(!empty($rowdt[$d][9])){
                            $raf = $con->Listar("SELECT dc.* 
                                FROM gf_detalle_comprobante_pptal dc 
                                LEFT JOIN gf_comprobante_pptal cn ON dc.comprobantepptal = cn.id_unico 
                                LEFT JOIN gf_comprobante_pptal cna ON cn.numero = cna.numero 
                                    AND cn.tipocomprobante = cna.tipocomprobante 
                                    AND cn.parametrizacionanno = $anno_c and cna.parametrizacionanno = $panno 
                                LEFT JOIN gf_detalle_comprobante_pptal dca ON cna.id_unico = dca.comprobantepptal 
                                    AND dc.valor = dca.valor 
                                WHERE dca.id_unico =".$rowdt[$d][9]);
                            if(count($raf)>0){
                                $afectado = $raf[0][0];
                            } else {
                                $afectado = NULL;
                            }
                        } else {
                            $afectado = NULL;
                        }
                        #** Copiar Datos Del Detalle **#
                        $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal`  
                            ( `descripcion`,`valor`,
                            `comprobantepptal`,`rubrofuente`,
                            `conceptorubro`,`tercero`,
                            `proyecto`,`centro_costo`,
                            `comprobanteafectado`,`saldo_disponible`,
                            `clasenom`,`cantidad`,
                            `valor_unitario`) 
                        VALUES (:descripcion,:valor,
                            :comprobantepptal,:rubrofuente,
                            :conceptorubro,:tercero,
                            :proyecto,:centro_costo,
                            :comprobanteafectado,:saldo_disponible,
                            :clasenom,:cantidad,
                            :valor_unitario)";
                        $sql_dato = array(
                            array(":descripcion",$rowdt[$d][1]),
                            array(":valor",$rowdt[$d][2]), 
                            array(":comprobantepptal",$comprobante),  
                            array(":rubrofuente",$rubro_fuente),    
                            array(":conceptorubro",$concepto_rubro),    
                            array(":tercero",$rowdt[$d][6]),    
                            array(":proyecto",$rowdt[$d][7]), 
                            array(":centro_costo",$rowdt[$d][8]),  
                            array(":comprobanteafectado",$afectado),    
                            array(":saldo_disponible",$rowdt[$d][10]),    
                            array(":clasenom",$rowdt[$d][11]),    
                            array(":cantidad",$rowdt[$d][12]),    
                            array(":valor_unitario",$rowdt[$d][13]),    
                        );
                        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($obj_resp)){
                            $ingresados +=1;
                        }
                    }
                    
                    
                }
            }
        } 
        
        echo $ingresados;
    break;
    
    
    #* Validar que existan todos los rubros 
    case 8:
        $arrayr         = array();
        $arraycd        = array();
        $arraycc        = array();
        $rta            = 0;
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas    = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        $html = 'Rubros no encontrados <br/>';
        $htmld = 'Cuentas débito no encontradas <br/>';
        $htmlc = 'Cuentas crédito no encontradas <br/>';
        for ($a = 2; $a <= $total_filas; $a++) {
            $codigo     = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $nombre     = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $cuentad    = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $cuentac    = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            #* Buscar código Rubro exista en la vigencia *#
            $rowr = $con->Listar("SELECT * FROM gf_rubro_pptal WHERE codi_presupuesto = '$codigo' "
                    . "AND parametrizacionanno = $panno");
            if(count($rowr)>0){} else {
                if(in_array($codigo, $arrayr)) {

                } else {
                    array_push ( $arrayr , $codigo );
                    $rta +=1;
                    $html .=$codigo.' '.$nombre.'<br/>';
                }
            }
            if(!empty($cuentad)){
                #* Buscar que las cuentas débito existan
                $rowd = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta= '$cuentad' 
                    AND parametrizacionanno = $panno");
                if(count($rowd)>0){} else {
                    if(in_array($cuentad, $arraycd)) {

                    } else {
                        array_push ( $arraycd , $cuentad );
                        $rta +=1;
                        $htmld .=$cuentad.'<br/>';
                    }
                }
            }
            if(!empty($cuentac)){
                #* Buscar que las cuentas crédito existan
                $rowc = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta= '$cuentac' 
                    AND parametrizacionanno = $panno");
                if(count($rowc)>0){} else {
                    if(in_array($cuentac, $arraycc)) {

                    } else {
                        array_push ( $arraycc , $cuentac );
                        $rta +=1;
                        $htmlc .=$cuentac.'<br/>';
                    }
                }
            }
        }
        $html .='<br/>'.$htmld.'<br/>'.$htmlc;
        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos);
    break;
    #*** Subir Apropiaciones Iniciales Por Plan de adquisición ***#
    case 9:
        $cc             = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno");
        $centroc        = $cc[0][0];
        $proyecto       = 2147483647;
        #** Tercero Varios Compañia ****#
        $tv             = $con->Listar("SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999 AND compania = $compania");
        $tercerov       = $tv[0][0];
        $arrayr         = array();
        $rta            = 0;
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas    = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        $apg            = 0;
        #Saber Cuantas fuentes vienen 
        $x = 1;
        $col = 5;
        while ($x == 1){
            $fuente[$col]     = $objWorksheet->getCellByColumnAndRow($col, 1)->getCalculatedValue();
            if(empty($fuente[$col])){
                $x = 0;
            } else {
                $col++;
            }
        }
        #**Buscar Si Existe
        $numa       = $anno;
        $numero     = $numa.'000001';
        $tipoCom    = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal 
            WHERE clasepptal = 13 and tipooperacion=1 AND compania = $compania");
        $tipoCom    = $tipoCom[0][0];
        $fecha      =$numa.'-01-01';
        $descripcion='Comprobante Apropiación Inicial '.$numa;

        $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
            WHERE numero = $numero AND tipocomprobante =$tipoCom ");

        if(count($bs)>0){
            $id_comprobante = $bs[0][0];
            $sql_cons ="DELETE FROM `gf_detalle_comprobante_pptal` 
                WHERE `comprobantepptal`=:comprobantepptal";
            $sql_dato = array(
                    array(":comprobantepptal",$id_comprobante),	
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        } else {
            #Guardar Comprobante 
            $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                    ( `numero`, `fecha`, 
                    `fechavencimiento`,`descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`) 
            VALUES (:numero, :fecha, 
                    :fechavencimiento,:descripcion,
                    :parametrizacionanno,:tipocomprobante)";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":fechavencimiento",$fecha),
                    array(":descripcion",$descripcion),
                    array(":parametrizacionanno",$panno),
                    array(":tipocomprobante",$tipoCom),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante =$tipoCom ");
            $id_comprobante = $bs[0][0];
        }
        if(!empty( $id_comprobante)) {
            for ($a = 2; $a <= $total_filas; $a++) { 
            $codigo     = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $nombre     = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $concepto   = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $debito     = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $credito    = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            
            #* Buscar id Rubro en la vigencia *#
            $rowr       = $con->Listar("SELECT 
                    rb.id_unico, 
                    tc.nombre 
                FROM gf_rubro_pptal rb 
                LEFT JOIN gf_tipo_clase_pptal tc ON rb.tipoclase = tc.id_unico 
                WHERE codi_presupuesto = '$codigo' 
                AND parametrizacionanno = $panno");
            $id_rubro = $rowr[0][0];
            #***** Buscar Clase Concepto****#
            $ccl = $con->Listar("SELECT id_unico FROM gf_clase_concepto WHERE nombre = '".$rowr[0][1]."'");
            $clasec = $ccl[0][0];
            
            #* Buscar Si Existe Concepto en la vigencia *#
            $rowc       = $con->Listar("SELECT * FROM gf_concepto WHERE nombre = '$concepto' "
                        . "AND parametrizacionanno = $panno");
            if(count($rowc)>0) { 
                $id_concepto= $rowc[0][0];
            } else {
                $sql_cons ="INSERT INTO `gf_concepto` 
                        ( `nombre`, `clase_concepto`, 
                        `parametrizacionanno`) 
                VALUES (:nombre, :clase_concepto, 
                        :parametrizacionanno)";
                $sql_dato = array(
                        array(":nombre",$concepto),
                        array(":clase_concepto",$clasec),
                        array(":parametrizacionanno",$panno),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                $rowc       = $con->Listar("SELECT * FROM gf_concepto WHERE nombre = '$concepto' "
                        . "AND parametrizacionanno = $panno");
                $id_concepto= $rowc[0][0];
            }
            
            #** Buscar Si Existe Concepto Rubro **#
            $id_cr = $con->Listar("SELECT id_unico FROM gf_concepto_rubro 
                WHERE rubro =$id_rubro AND concepto = $id_concepto");
            if(count($id_cr)>0){
                $id_cr = $id_cr[0][0];
            } else {
                $sql_cons ="INSERT INTO `gf_concepto_rubro` 
                        ( `rubro`,`concepto` ) 
                VALUES (:rubro, :concepto)";
                $sql_dato = array(
                        array(":rubro",$id_rubro),
                        array(":concepto",$id_concepto), 
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $id_cr = $con->Listar("SELECT id_unico FROM gf_concepto_rubro 
                    WHERE rubro =$id_rubro AND concepto = $id_concepto");
                $id_cr = $id_cr[0][0];
            }
             #***** Buscar Cuenta Débito ***#
            $cd = $con->Listar("SELECT id_unico FROM gf_cuenta 
                WHERE codi_cuenta = '$debito' AND parametrizacionanno = $panno");
            $ctaDebito = $cd[0][0];
            #***** Buscar Cuenta Crédito ***#
            $cc = $con->Listar("SELECT id_unico FROM gf_cuenta 
                WHERE codi_cuenta = '$credito' AND parametrizacionanno = $panno");
            $ctaCredito = $cc[0][0];
            
            #Validar Si Ya Existe Concepto Rubro Cuenta
            $crc = $con->Listar("SELECT * FROM gf_concepto_rubro_cuenta "
                    . "WHERE concepto_rubro = $id_cr AND cuenta_debito = $ctaDebito "
                    . "AND cuenta_credito = $ctaCredito");
            if(count($crc)>0){
            } else {
                if(!empty($id_cr)){
                    $sql_cons ="INSERT INTO `gf_concepto_rubro_cuenta` 
                        ( `concepto_rubro`,`cuenta_debito`, `cuenta_credito`, 
                        `centrocosto`, `proyecto` ) 
                    VALUES (:concepto_rubro, :cuenta_debito, :cuenta_credito, 
                    :centrocosto, :proyecto)";
                    $sql_dato = array(
                            array(":concepto_rubro",$id_cr), 
                            array(":cuenta_debito",$ctaDebito), 
                            array(":cuenta_credito",$ctaCredito), 
                            array(":centrocosto",$centroc), 
                            array(":proyecto",$proyecto)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                }
            }
            
            
            #*** Validar Que El valor de las fuentes no venga vacio **#
            for ($x = 5; $x <= $col; $x++) {
                $valorf = $objWorksheet->getCellByColumnAndRow($x, $a)->getCalculatedValue();
                if(!empty($valorf)){
                    # Verificar que exista fuente
                    $id_f = $con->Listar("SELECT id_unico FROM gf_fuente 
                        WHERE nombre ='".$fuente[$x]."' AND parametrizacionanno = $panno");
                    if(count($id_f)>0){
                        $id_f = $id_f[0][0];
                    } else {
                        $sql_cons ="INSERT INTO `gf_fuente` 
                                ( `nombre`,`movimiento`,
                                `parametrizacionanno`,`compania` ) 
                        VALUES (:nombre, :movimiento, 
                                :parametrizacionanno, :compania)";
                        $sql_dato = array(
                                array(":nombre",$fuente[$x]),
                                array(":movimiento",1),   
                                array(":parametrizacionanno",$panno),
                                array(":compania",$compania),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        $id_f = $con->Listar("SELECT id_unico FROM gf_fuente 
                        WHERE nombre ='".$fuente[$x]."' AND parametrizacionanno = $panno");
                        $id_f = $id_f[0][0];
                    }
                    
                    #** Verificar si existe rubro fuente ***#
                    $id_rf = $con->Listar("SELECT id_unico FROM gf_rubro_fuente 
                        WHERE rubro =$id_rubro AND fuente = $id_f");
                    if(count($id_rf)>0) { 
                        $id_rf = $id_rf[0][0];
                    } else {
                        $sql_cons ="INSERT INTO `gf_rubro_fuente` 
                                ( `rubro`,`fuente` ) 
                        VALUES (:rubro, :fuente)";
                        $sql_dato = array(
                                array(":rubro",$id_rubro),
                                array(":fuente",$id_f), 
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        $id_rf = $con->Listar("SELECT id_unico FROM gf_rubro_fuente 
                            WHERE rubro =$id_rubro AND fuente = $id_f");
                        $id_rf = $id_rf[0][0];
                    }
                    
                    #** Guardar Detalles Apropiación ***#
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                              ( `descripcion`,`valor`,
                              `comprobantepptal`,`rubrofuente`,
                              `tercero`, `proyecto`,`conceptoRubro` ) 
                    VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                     :tercero, :proyecto, :conceptoRubro)";
                    $sql_dato = array(
                        array(":descripcion",$descripcion),
                        array(":valor",$valorf),
                        array(":comprobantepptal",$id_comprobante),
                        array(":rubrofuente",$id_rf),
                        array(":tercero",$tercerov),
                        array(":proyecto",2147483647),
                        array(":conceptoRubro",$id_cr)
                     );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    if(empty($resp)){
                        $apg +=1;
                    }
                }
            }
            
        }
        }
        echo $apg;
    break;
    #* Revisar Si Ya hay comprobante de saldos iniciales con detalles
    case 10:
        $ve = $con->Listar("SELECT * FROM gf_detalle_comprobante dc
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
            WHERE tc.clasecontable = 5 AND tc.compania = $compania
            AND cn.parametrizacionanno = $panno");
        if(count($ve)>0){
            echo 1;
        } else {
            echo 0;
        }
    break;
    #* Validar Cuentas y eliminar saldos si existen
    case 11:
        $arrayc         = array();
        $rta            = 0;
        $html           = "Cuentas no encontradas <br/>";
        $sql_cons ="DELETE dc.* 
            FROM
                `gf_detalle_comprobante` dc 
            LEFT JOIN 
                `gf_comprobante_cnt` cn ON dc.comprobante = cn.id_unico 
            LEFT JOIN 
                `gf_tipo_comprobante` tc ON cn.tipocomprobante = tc.id_unico 
        WHERE tc.`clasecontable`=:clasecontable 
            AND tc.`compania`=:compania 
            AND cn.`parametrizacionanno`=:parametrizacionanno";
        $sql_dato = array(
          array(":clasecontable",5),
          array(":compania",$compania),
          array(":parametrizacionanno",$panno),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            #** Comprobar Si Cuentas Existen **#
            $inputFileName  = $_FILES['file']['tmp_name'];                                       
            $objReader      = new PHPExcel_Reader_Excel2007();					
            $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
            $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);				
            $total_filas    = $objWorksheet->getHighestRow();					
            $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
            for ($a = 2; $a <= $total_filas; $a++) {
                $sigla      = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
                if($sigla != ""){
                    $valor      = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
                    if($valor !=0){
                        $codigo     = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
                        $cod_cuenta = str_replace('.', '', $codigo);
                        #** Buscar que exista en el plan contable **#
                        $be = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = $cod_cuenta AND parametrizacionanno = $panno");
                        if(count($be)>0){}else {
                            $html .=$cod_cuenta.'<br/>';
                            $rta +=1;
                        }
                    }
                }
            }
        }
        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos);
    break;
    #* Guardar Saldos 
    case 12:
        #* Buscar Tercero Varios
        $t          = 0;
        $tv         = $con->Listar("SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999 AND compania = $compania");
        $tercerov   = $tv[0][0];
        #** Centro costo por parametrizacion **#
        $cc       = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $panno");
        $centroc  = $cc[0][0];
        #* Calcular Fecha 
        $fecha      = $anno.'-01-01';
        #* Buscar comprobante saldos iniciales 
        $si = $con->Listar("SELECT cn.id_unico 
            FROM 
                gf_comprobante_cnt cn 
            LEFT JOIN 
                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
            WHERE 
                tc.clasecontable=5 AND cn.compania=$compania AND cn.parametrizacionanno=$panno");
        if(count($si)>0){
            $id_comprobante = $si[0][0];
        } else {
            #* Buscr Tipo cnt 
            $tc         = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE clasecontable = 5 AND compania = $compania");
            $tipo       = $tc[0][0];
            
            #* Calcular Número
            $numero     = numero('gf_comprobante_cnt', $tipo, $panno);
            
            $sql_cons ="INSERT INTO `gf_comprobante_cnt`  
                ( `numero`,`fecha`,
                `descripcion`,`tipocomprobante`, 
                `parametrizacionanno`,
                `tercero`,`estado`,`compania`) 
            VALUES (:numero, :fecha,
                :descripcion,:tipocomprobante,
                :parametrizacionanno,:tercero, 
                :estado,:compania)";
            $sql_dato = array(
                array(":numero",$numero),
                array(":fecha",$fecha),
                array(":descripcion",'Comprobante Saldos Iniciales'),   
                array(":tipocomprobante",$tipo),   
                array(":parametrizacionanno",$panno),
                array(":tercero",$tercerov),
                array(":estado",1),
                array(":compania",$compania),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            $si = $con->Listar("SELECT cn.id_unico 
            FROM 
                gf_comprobante_cnt cn 
            LEFT JOIN 
                gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
            WHERE 
                tc.clasecontable=5 AND cn.compania=$compania AND cn.parametrizacionanno=$panno");
            $id_comprobante = $si[0][0];
        }
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas    = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        for ($a = 2; $a <= $total_filas; $a++) {
            $sigla      = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            if($sigla != ""){
                $codigo     = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
                $valor      = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
                if($valor !=0){
                    $cod_cuenta = str_replace('.', '', $codigo);
                    $cod_cuenta = trim($cod_cuenta);
                    if(strlen($cod_cuenta)==6){
                        #** Buscar que exista  en el plan contable **#
                        $be = $con->Listar("SELECT * FROM gf_cuenta 
                            WHERE codi_cuenta = $cod_cuenta AND parametrizacionanno = $panno");
                        if(count($be)>0){
                            $id_cuentap = $be[0][0];
                            #* Buscar Cuenta HIja
                            $ch = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta WHERE predecesor = $id_cuentap");
                            if(count($ch)>0){
                            } else {
                                $cod_ch = $cod_cuenta.'01';
                                $sql_cons ="INSERT INTO `gf_cuenta`  
                                    ( `codi_cuenta`,`nombre`, 
                                    `movimiento`,`centrocosto`,
                                    `auxiliartercero`,`auxiliarproyecto`,
                                    `parametrizacionanno`,`activa`,
                                    `dinamica`,`naturaleza`,
                                    `tipocuentacgn`,`clasecuenta`, `predecesor`) 
                                VALUES (:codi_cuenta, :nombre,
                                    :movimiento,:centrocosto,
                                    :auxiliartercero,:auxiliarproyecto, 
                                    :parametrizacionanno,:activa,
                                    :dinamica,:naturaleza,
                                    :tipocuentacgn,:clasecuenta,:predecesor)";
                                $sql_dato = array(
                                    array(":codi_cuenta",$cod_ch),
                                    array(":nombre",$be[0][2]),
                                    array(":movimiento",1),   
                                    array(":centrocosto",$be[0][4]),   
                                    array(":auxiliartercero",$be[0][5]),
                                    array(":auxiliarproyecto",$be[0][6]),
                                    array(":parametrizacionanno",$panno),
                                    array(":activa",$be[0][8]),
                                    array(":dinamica",$be[0][9]),
                                    array(":naturaleza",$be[0][10]),
                                    array(":tipocuentacgn",$be[0][12]),
                                    array(":clasecuenta",$be[0][13]),
                                    array(":predecesor",$id_cuentap),
                                );
                                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                                if(empty($obj_resp)){
                                    #* Buscar Cuenta HIja
                                    $ch = $con->Listar("SELECT id_unico, naturaleza FROM gf_cuenta WHERE predecesor = $id_cuentap");
                                }
                            }
                            $id_cuenta = $ch[0][0];
                            if(!empty($id_cuenta)){
                                #** Guardar Detalle 
                                $id_natura = $ch[0][1];
                                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                                        ( `fecha`, `comprobante`,`valor`,
                                        `cuenta`,`naturaleza`,`tercero`, 
                                        `centrocosto`) 
                                VALUES (:fecha,  :comprobante,:valor, 
                                        :cuenta,:naturaleza, :tercero, 
                                        :centrocosto)";
                                $sql_dato = array(
                                        array(":fecha",$fecha),
                                        array(":comprobante",$id_comprobante),
                                        array(":valor",($valor)),
                                        array(":cuenta",$id_cuenta),   
                                        array(":naturaleza",$id_natura),
                                        array(":tercero",$tercerov),
                                        array(":centrocosto",$centroc),
                                );
                                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                                if(empty($obj_resp)){
                                    $t +=1;
                                }
                            }
                        }
                    }
                }
            }
        }
        echo $t;
    break;
    #* Subir Plan I
    case 13:
        $save  = 0;
        $inputFileName= $_FILES['file']['tmp_name'];                                       
        $objReader = new PHPExcel_Reader_Excel2007();                   
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);            
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);               
        $total_filas = $objWorksheet->getHighestRow();                  
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        for ($a = 2; $a <= $total_filas; $a++) {
            $codigo       = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $nombre       = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $movimeinto   = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $tipoinv      = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $tipoact      = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            
            $rowc = $con->Listar("SELECT * FROM gf_plan_inventario WHERE codi ='$codigo' AND compania = $compania");
            if(count($rowc)> 0){} else {
                $sqlinsert = "INSERT INTO gf_plan_inventario (nombre, codi, tienemovimiento, compania, tipoinventario, unidad, tipoactivo) VALUES ('$nombre', '$codigo', $movimeinto, $compania, $tipoinv, 2, $tipoact)";
                $insert = $mysqli->query($sqlinsert);
                if ($insert){
                    $save++;
                }
            }
        }
        echo $save;
    break;
    #* Codigo de Barras
    case 14:
        $save  = 0;
        $inputFileName= $_FILES['file']['tmp_name'];                                       
        $objReader = new PHPExcel_Reader_Excel2007();                   
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);            
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);               
        $total_filas = $objWorksheet->getHighestRow();                  
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        for ($a = 2; $a <= $total_filas; $a++) {
            $codigo_pi    = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $codigo_ba    = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            
            $rowc = $con->Listar("SELECT * FROM gf_plan_inventario 
                WHERE codi ='$codigo_pi' AND compania = $compania");
            if(count($rowc)> 0){
                $id_unico = $rowc[0][0];
                $sql_cons ="UPDATE `gf_plan_inventario`  
                    SET `codigo_barras`=:codigo_barras 
                    WHERE `id_unico`=:id_unico";
                $sql_dato = array(
                    array(":codigo_barras",$codigo_ba),
                    array(":id_unico",$id_unico),   
                );
                $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($obj_resp)){
                    $t +=1;
                }
                $save++;
            } 
        }
        echo $save;
    break;
    
    #* Archvio Información Presupuestal
    case 15:
        $arrayrubro     = array();
        $arraytipoc     = array();
        $arrayTercero   = array();
        $rta            = 0;
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas    = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        $htmlr = 'Rubros No Configurados<br/>';
        $htmlt = 'Tipos de Comprobante No Encontrados<br/>';
        $htmltr= 'Terceros No Encontrados <br/>';
        #* Validar Rubros 
        for ($a = 2; $a <= $total_filas; $a++) {
            $item       = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $codigo_r   = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            #* Buscar Rubro 
            $rb = $con->Listar("SELECT rf.id_unico, cr.id_unico FROM gf_rubro_pptal rb 
                LEFT JOIN gf_rubro_fuente rf ON rf.rubro = rb.id_unico 
                LEFT JOIN gf_concepto_rubro cr ON cr.rubro = rb.id_unico 
                LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                WHERE rb.codi_presupuesto = '$codigo_r' AND rb.parametrizacionanno = $panno 
                AND c.nombre LIKE '$codigo_r%' AND c.parametrizacionanno = $panno 
                AND rf.id_unico IS NOT NULL AND cr.id_unico IS NOT NULL");
            if(count($rb)>0){} else {
                
                if(in_array($codigo_r, $arrayrubro)) {} else {
                    array_push ( $arrayrubro , $codigo_r );
                    $rta    +=1;
                    $htmlr  .= 'Item: '.$item.' - Rubro: '.$codigo_r.'<br/>';
                }
            }
            #*Tipo Comprobante 
            $tipoc   = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue(); 
            $rb = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal  
                WHERE codigo = '$tipoc' AND compania=$compania");
            if(count($rb)>0){} else {
                if(in_array($tipoc, $arraytipoc)) {} else {
                    array_push ( $arraytipoc , $tipoc );
                    $rta    +=1;
                    $htmlt  .=$tipoc.'<br/>';
                }
            }
            
            #*Tercero
            $nit   = $objWorksheet->getCellByColumnAndRow(13, $a)->getCalculatedValue(); 
            $rb = $con->Listar("SELECT * FROM gf_tercero  
                WHERE numeroidentificacion = '$nit' AND compania=$compania");
            if(count($rb)>0){} else {
                if(in_array($nit, $arrayTercero)) {} else {
                    array_push ( $arrayTercero , $nit );
                    $rta    +=1;
                    $htmltr .=$nit.'<br/>';
                }
            }
            
        }
        
        $html .='<br/>'.$htmlr.'<br/>'.$htmlt.'<br/>'.$htmltr;
        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos);
    break;
    
    #* Cargar Información Presupuestal 
    case 16:
        $rta            = 0;
        $html           = '';
        $inputFileName  = $_FILES['file']['tmp_name'];                                       
        $objReader      = new PHPExcel_Reader_Excel2007();					
        $objPHPExcel    = PHPExcel_IOFactory::load($inputFileName); 			
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);				
        $total_filas    = $objWorksheet->getHighestRow();					
        $total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());
        
        for ($a = 2; $a <= $total_filas; $a++) {
            $item       = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $tipo_m     = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $codigo_r   = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $numero     = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $fecha      = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            $timestamp  = PHPExcel_Shared_Date::ExcelToPHP($fecha);
            $fecha      = date("Y-m-d",$timestamp);
            $tipoc      = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();
            $tipo_con   = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
            $numero_con = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
            $nombre_pr  = $objWorksheet->getCellByColumnAndRow(9, $a)->getCalculatedValue();
            $numero_pr  = $objWorksheet->getCellByColumnAndRow(10, $a)->getCalculatedValue();
            $dependen   = $objWorksheet->getCellByColumnAndRow(11, $a)->getCalculatedValue();
            $cargo      = $objWorksheet->getCellByColumnAndRow(12, $a)->getCalculatedValue();
            $nit        = $objWorksheet->getCellByColumnAndRow(13, $a)->getCalculatedValue();
            $descripci  = $objWorksheet->getCellByColumnAndRow(15, $a)->getCalculatedValue();
            $numero_d   = $objWorksheet->getCellByColumnAndRow(16, $a)->getCalculatedValue();
            $creditos   = $objWorksheet->getCellByColumnAndRow(17, $a)->getCalculatedValue();
            $ctcreditos = $objWorksheet->getCellByColumnAndRow(18, $a)->getCalculatedValue();
            $cod_afect  = $objWorksheet->getCellByColumnAndRow(24, $a)->getCalculatedValue();
            $numero_afe = $objWorksheet->getCellByColumnAndRow(25, $a)->getCalculatedValue();
            
            #Valor =
            $valor = 0;
            if($tipo_m=='GASTOS'){
                if($creditos>0){
                    $valor = $creditos;
                }elseif($ctcreditos>0){
                    $valor = $ctcreditos*-1;
                }
            } elseif($tipo_m=='INGRESOS'){
                if($ctcreditos>0){
                    $valor = $ctcreditos;
                }elseif($creditos>0){
                    $valor = $creditos*-1;
                }
            }
            
            #* Buscar Rubro 
            $rb = $con->Listar("SELECT rf.id_unico, cr.id_unico FROM gf_rubro_pptal rb 
                LEFT JOIN gf_rubro_fuente rf ON rf.rubro = rb.id_unico 
                LEFT JOIN gf_concepto_rubro cr ON cr.rubro = rb.id_unico 
                LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                WHERE rb.codi_presupuesto = '$codigo_r' AND rb.parametrizacionanno = $panno 
                AND c.nombre LIKE '$codigo_r%' AND c.parametrizacionanno = $panno 
                AND rf.id_unico IS NOT NULL AND cr.id_unico IS NOT NULL");
            $id_rubro_fuente   = $rb[0][0];
            $id_concepto_rubro = $rb[0][1];
            #*Tipo Comprobante              
            $tc = $con->Listar("SELECT id_unico,tipooperacion FROM gf_tipo_comprobante_pptal  
                WHERE codigo = '$tipoc' AND compania=$compania");
            $id_tipoc = $tc[0][0];
            if($tc[0][1]==3 && $valor<0){
                $valor = $valor*-1; 
            }
            #*Tercero            
            $tr = $con->Listar("SELECT * FROM gf_tercero  
                WHERE numeroidentificacion = '$nit' AND compania=$compania");
            $id_tercero = $tr[0][0];            
            
            #*Proyecto         
            if(!empty($numero_pr)){
                $pr = $con->Listar("SELECT * FROM gf_proyecto   
                    WHERE nombre LIKE '$numero_pr%' AND compania=$compania");
                if(count($pr)>0){
                    $proyecto = $pr[0][0];
                } else {
                    $proyecto = 2147483647;
                }
            } else {
                $proyecto = 2147483647;
            }
            
            
            #* Buscar Comprobante 
            $rowce = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                WHERE numero = '$numero' AND tipocomprobante = $id_tipoc 
                    AND parametrizacionanno = $panno ");
            if(count($rowce)>0){
                $id_comprobante = $rowce[0][0];
            } else {
                #* Clase contrato 
                $rowcnt = $con->Listar("SELECT * FROM `gf_clase_contrato` where nombre like '$tipo_con%'");
                if(count($rowcnt)>0){
                    $clase_contrato = $rowcnt[0][0];
                }else {
                    $clase_contrato = NULL;
                }
                
                $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                        ( `numero`, `fecha`, `fechavencimiento`,`descripcion`, 
                        `numerocontrato`,`parametrizacionanno`,
                        `clasecontrato`,`tipocomprobante`,
                        `tercero`, `estado`, `responsable`, 
                        `compania`, `usuario`,`fecha_elaboracion`, `proyecto`) 
                VALUES (:numero, :fecha, :fechavencimiento,:descripcion,
                        :numerocontrato,:parametrizacionanno,
                        :clasecontrato, :tipocomprobante, 
                        :tercero, :estado, :responsable,
                        :compania,:usuario, :fecha_elaboracion,:proyecto)";
                $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":fechavencimiento",$fecha),
                    array(":descripcion",$descripci),
                    array(":numerocontrato",$numero_con),
                    array(":parametrizacionanno",$panno),
                    array(":clasecontrato",$clase_contrato),
                    array(":tipocomprobante",$id_tipoc),
                    array(":tercero",$id_tercero),
                    array(":estado",2),
                    array(":responsable",$id_tercero),
                    array(":compania",$compania),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y/m/d')),
                    array(":proyecto",$proyecto),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato); 
                $rowce = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                WHERE numero = '$numero' AND tipocomprobante = $id_tipoc 
                    AND parametrizacionanno = $panno ");
                $id_comprobante = $rowce[0][0];
            } 
            #* Buscar Detalle Afectado 
            $id_afectado = NULL;
            if(!empty($cod_afect)){
                $tcaf = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal  
                    WHERE codigo = '$cod_afect' AND compania=$compania");
                $id_tipocaf = $tcaf[0][0];
                
                $rowda = $con->Listar("SELECT * FROM gf_detalle_comprobante_pptal dc 
                LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico 
                WHERE cp.numero = '$numero_afe' AND cp.tipocomprobante = $id_tipocaf 
                    AND cp.parametrizacionanno = $panno
                AND dc.rubrofuente = $id_rubro_fuente AND dc.conceptoRubro = $id_concepto_rubro");
                if(count($rowda)>0){
                    $id_afectado = $rowda[0][0];
                }
            }
            
            #* Guardar Detalle
            $informacion = 'Item: '.$item.' - Número Doc: '.$numero_d.' - Dependencia: '.$dependen;
            $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal`  
                ( `descripcion`,`valor`,
                `comprobantepptal`,`rubrofuente`,
                `conceptorubro`,`tercero`,
                `proyecto`,`centro_costo`,
                `comprobanteafectado`,`informacion`) 
            VALUES (:descripcion,:valor,
                :comprobantepptal,:rubrofuente,
                :conceptorubro,:tercero,
                :proyecto,:centro_costo,
                :comprobanteafectado, :informacion)";
            $sql_dato = array(
                array(":descripcion",$descripci),
                array(":valor",$valor), 
                array(":comprobantepptal",$id_comprobante),  
                array(":rubrofuente",$id_rubro_fuente),    
                array(":conceptorubro",$id_concepto_rubro),    
                array(":tercero",$id_tercero),    
                array(":proyecto",$proyecto), 
                array(":centro_costo",12),  
                array(":comprobanteafectado",$id_afectado), 
                array(":informacion",$informacion),    
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                $rta++;
            } else {
                $html .='Item N°: '.$item.' No se ha podido cargar Correctamente<br/>';
            }
            
        }
        //$datos = array("msj"=>$html,"rta"=>$rta);
        echo $rta;
    break;
}