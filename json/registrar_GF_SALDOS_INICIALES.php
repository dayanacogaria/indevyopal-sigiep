<?php
#Llamamos a la clase de conexión
require_once ('./Conexion/conexion.php');
require_once ('./head_listar.php');
?>
<style>
    .combo{
        border-radius: 2px;  
        width: 105%;
    }
    
    .combo:hover{
        cursor: pointer;
    }
</style>
        <title>Saldos Iniciales</title>
    </head>
    <body>
        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-8 text-center" style="margin-top: -22px">
                    <h2 class="tituloform" align="center" >Saldos Iniciales</h2>
                    <div class="client-form" style="margin-left:4px;margin-right:4px">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetalleComprobante.php" style="margin-top:-15px">
                            <div class="col-sm-2" style="margin-left:2px;">
                                <div class="form-group" style="margin-top: 5px; margin-right: -25px;"  align="left">
                                    <?php 
                                    $sqlC = "SELECT id_unico,
                                                codi_cuenta,
                                                nombre 
                                        FROM    gf_cuenta
                                        WHERE   movimiento = 1
                                        OR      centrocosto = 1
                                        OR      auxiliartercero = 1
                                        OR      auxiliarproyecto = 1";
                                    $res = $mysqli->query($sqlC);
                                    ?>
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Cuenta
                                    </label>
                                    <select name="sltcuenta" id="sltcuenta" autofocus="" class="form-control" style="width:100px;height:30px" title="Seleccione cuenta" required="">
                                        <option>AAA</option>
                                        <?php 
                                        while ($fila = mysqli_fetch_row($res)){ ?>
                                            <option value="<?php echo $fila[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($fila[1].' - '.$fila[2]))) ?></option>    
                                        <?php                                         
                                        }
                                        ?>
                                    </select>
                                </div>                               
                            </div>    
                            <div class="col-sm-2" style="margin-left:-5px">
                                <div class="form-group" style="margin-top: 5px;"  align="left">
                                    <?php 
                                    $sqlT = "SELECT DISTINCT T.id_unico,
                                                    D.nombre,
                                                    T.numeroidentificacion,                                                     
                                                    CONCAT(T.nombreuno)                                                     
                                            FROM gf_tercero T 
                                            LEFT JOIN gf_tipo_identificacion D 
                                            ON T.tipoidentificacion = D.id_unico
                                            WHERE T.id_unico = 2";
                                    $res = $mysqli->query($sqlT);
                                    $filaT = mysqli_fetch_row($res);
                                    $sql = "SELECT DISTINCT T.id_unico,
                                                    D.nombre,
                                                    T.numeroidentificacion,                                                     
                                                    CONCAT(T.nombreuno,' ',T.nombredos,' ',T.apellidouno,' ',T.apellidodos),
                                                    T.razonsocial
                                            FROM gf_tercero T 
                                            LEFT JOIN gf_tipo_identificacion D 
                                            ON T.tipoidentificacion = D.id_unico
                                            WHERE T.id_unico != 2";
                                    $rs = $mysqli->query($sql);
                                    ?>
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Tercero
                                    </label>
                                    <select name="slttercero" id="slttercero" class="form-control" style="width:100px;height:30px" title="Seleccione tercero" required="">
                                        <option value="<?php echo $filaT[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($filaT[3].'('.$filaT[1].' - '.$filaT[2].')'))); ?></option>
                                        <?php 
                                        while ($fila1=mysqli_fetch_row($rs)) { ?>
                                            <option value="<?php echo $fila1[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($fila1[3].'('.$fila1[1].' - '.$fila1[2].')')));?></option>
                                            <?php
                                            if ($fila1[3] == NULL || empty($fila[3])) { ?>
                                                <option value="<?php echo $fila1[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($fila1[4].'('.$fila1[1].' - '.$fila1[2].')')));
                                            }   
                                            ?>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2" style="margin-left:-5px">
                                <div class="form-group" style="margin-top: 5px;"  align="left">
                                    <?php 
                                    $sqlCC = "SELECT id_unico,nombre FROM gf_centro_costo WHERE id_unico = 12 ORDER BY nombre ASC";
                                    $a = $mysqli->query($sqlCC);
                                    $filaC = mysqli_fetch_row($a);
                                    $sqlCT = "SELECT id_unico,nombre FROM gf_centro_costo WHERE id_unico != 12 ORDER BY nombre ASC";
                                    $r = $mysqli->query($sqlCT);
                                    ?>
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Centro Costo
                                    </label>
                                    <select name="sltcentroc" id="sltcentroc" class="form-control" style="width:100px;height:30px" title="Seleccione centro costo" required="">
                                        <option value="<?php echo $filaC[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($filaC[1]))); ?></option>
                                        <?php 
                                        while($fila2=  mysqli_fetch_row($r)){ ?>
                                         <option value="<?php echo $fila2[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($fila2[1]))); ?></option>   
                                        <?php                                          
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2" style="margin-left:-5px">
                                <div class="form-group" style="margin-top: 5px;"  align="left">
                                    <?php 
                                    $sqlP = "SELECT id_unico,nombre FROM gf_proyecto WHERE nombre = 'VARIOS'" ;
                                    $d = $mysqli->query($sqlP);                                    
                                    $filaP = mysqli_fetch_row($d);
                                    $sqlPY = "SELECT id_unico,nombre FROM gf_proyecto WHERE nombre != 'VARIOS'" ;
                                    $X = $mysqli->query($sqlPY);
                                    ?>
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Proyecto
                                    </label>
                                    <select name="sltproyecto" id="sltproyecto" class="form-control" style="width:100px;height:30px" title="Seleccione proyecto" required="">
                                        <option value="<?php echo $filaP[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($filaP[1]))) ?></option>
                                        <?php 
                                        while($fila3 = mysqli_fetch_row($X)){ ?>
                                            <option value="<?php echo $fila3[0]; ?>"><?php echo ucwords(utf8_encode(strtolower($fila3[1]))) ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2"   style="margin-left:-5px">
                                <div class="form-group" style="margin-top: 5px;" align="left">
                                    <label class="control-label">
                                        Valor Débito
                                    </label>
                                    <input type="text" name="txtValorD" style="height:30px;width:90px"/>
                                </div>
                            </div>
                            <div class="col-sm-2" style="margin-left:-15px" >
                                <div class="form-group" style="margin-top: 5px;" align="left">
                                    <label class="control-label">
                                        Valor Crédito
                                    </label>
                                    <input type="text" name="txtValorC" style="height:30px;width:90px"/>
                                </div>
                            </div>
                            <div class="col-sm-1" align="left" style="margin-top:-50px;margin-left:530px">
                                <button type="submit" class="btn btn-primary sombra">Guardar</button>
                                <input type="hidden" name="MM_insert" >
                            </div>
                                                                                   
                        </form>                        
                    </div>
                </div>
                <div class="col-sm-8 text-center" style="margin-top: -20px;margin-bottom:-30px">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <?php 
                            $sql = " SELECT
  DT.id_unico,
  CT.id_unico,
  CT.nombre,
  CT.codi_cuenta,
  DT.naturaleza,
  N.id_unico,
  N.nombre,
  T.id_unico,
  T.nombreuno,
  T.nombredos,
  T.apellidouno,
  T.apellidodos,
  T.numeroidentificacion,
  TI.id_unico,
  TI.nombre,
  CC.id_unico,
  CC.nombre,
  PR.id_unico,
  PR.nombre,
  DT.valor
FROM
  gf_detalle_comprobante DT
LEFT JOIN
  gf_cuenta CT ON DT.cuenta = CT.id_unico
LEFT JOIN
  gf_naturaleza N ON N.id_unico = DT.naturaleza
LEFT JOIN
  gf_tercero T ON DT.tercero = T.id_unico
LEFT JOIN
  gf_tipo_identificacion TI ON T.tipoidentificacion = TI.id_unico
LEFT JOIN
  gf_centro_costo CC ON DT.centrocosto = CC.id_unico
LEFT JOIN
  gf_proyecto PR ON DT.proyecto = PR.id_unico";
                            $rs = $mysqli->query($sql);
                            ?>
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="oculto"></td>
                                        <td width="7%" class="cabeza" align="center"></td>
                                        <td class="cabeza"><strong>Cuenta</strong></td>
                                        <td class="cabeza"><strong>Tercero</strong></td>
                                        <td class="cabeza"><strong>Centro Costo</strong></td>
                                        <td class="cabeza"><strong>Proyecto</strong></td>
                                        <td class="cabeza"><strong>Valor Debito</strong></td>
                                        <td class="cabeza"><strong>Valor Credito</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="oculto"></th>
                                        <th width="7%"></th>
                                        <th>Cuenta</th>
                                        <th>Tercero</th>
                                        <th>Centro Costo</th>
                                        <th>Proyecto</th>
                                        <th>Valor Debito</th>
                                        <th>Valor Credito</th>
                                    </tr>
                                </thead>
                                <tbody>  
                                    <?php 
                                    while ($row = mysqli_fetch_row($rs)) { ?>
                                    <tr>
                                        <td class="campos oculto">
                                            <?php echo $row[0]; ?>
                                        </td>
                                        <td class="campos">
                                            <a onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                                                <li class="glyphicon glyphicon-trash"></li>
                                            </a>
                                            <a title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>)">
                                                <li class="glyphicon glyphicon-edit"></li>
                                            </a>
                                            <a title="Guardar" id="guardar" style="display: none" onclick="javascript:guardarCambios(<?php echo $row[0]; ?>)">
                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                            </a>
                                        </td>
                                        <!-- Código de cuenta y nombre de la cuenta -->
                                        <td class="campos">
                                            <?php echo '<label style="font-weight:normal" id="cuenta'.$row[0].'">'.utf8_encode(ucwords(strtolower($row[3].' - '.$row[2]))).'</label>'; ?>
                                            <select style="display: none;margin-left:-15px" id="sltC<?php echo $row[0]; ?>">
                                                <option value="<?php echo $row[1];?>"><?php echo $row[3].'-'.$row[2]; ?></option>
                                                    <?php 
                                                    $sqlCTN = "SELECT id_unico,codi_cuenta,nombre FROM gf_cuenta WHERE id_unico != $row[1]";
                                                    $result = $mysqli->query($sqlCTN);
                                                    while ($s = mysqli_fetch_row($result)){
                                                        echo '<option value="'.$s[0].'">'.$s[1].' - '.$s[2].'</option>';
                                                    }
                                                    ?>
                                            </select>
                                        </td>
                                        <!-- Datos de tercero -->
                                        <td class="campos">
                                            <?php echo '<label style="font-weight:normal" id="tercero'.$row[0].'">'.utf8_encode(ucwords(strtolower($row[8].' '.$row[9].' '.$row[10].' '.$row[11].'('.$row[14].' - '.$row[12].')'))).'</label>'; ?>
                                            <select id="sltTercero<?php echo $row[0]; ?>" style="display: none;margin-left:-12px;width:230px">
                                                <option value="<?php echo $row[7] ?>"><?php echo utf8_encode(ucwords(strtolower($row[8].' '.$row[9].' '.$row[10].' '.$row[11].'('.$row[14].' - '.$row[12].')'))) ?></option>
                                                <?php 
                                                $sqlTR = "SELECT
  T.id_unico,
  CONCAT(
    T.nombreuno,
    ' ',
    T.nombredos,
    ' ',
    T.apellidouno,
    ' ', T.apellidodos
  ),
  TI.id_unico,
  TI.nombre,
  T.numeroidentificacion
FROM
  gf_tercero T
LEFT JOIN
  gf_tipo_identificacion TI ON T.tipoidentificacion = TI.id_unico
WHERE
  T.id_unico != 2";
                                                $resulta = $mysqli->query($sqlTR);
                                                while($e = mysqli_fetch_row($resulta)){
                                                    echo '<option value="'.$e[0].'">'.utf8_encode($e[1].'('.$e[3].' - '.$e[4]).')'.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="campos">
                                            <?php echo '<label style="font-weight:normal" id="centroC'.$row[0].'">'.utf8_encode(ucwords(strtolower($row[16]))).'</label>'; ?>
                                            <select id="sltcentroC<?php echo $row[0]; ?>" style="display: none;margin-left:-15px;width:75px">
                                                <option value="<?php echo $row[15]; ?>"><?php echo $row[16]; ?></option>
                                                <?php
                                                $sqlCCT = "SELECT id_unico,nombre FROM gf_centro_costo WHERE id_unico != '$row[15]'";
                                                $g = $mysqli->query($sqlCCT);
                                                while($f = mysqli_fetch_row($g)){
                                                    echo '<option value="'.$f[0].'">'.$f[1].'</option>';
                                                }
                                                ?> 
                                            </select>
                                        </td>
                                        <td class="campos">
                                            <?php echo '<label style="font-weight:normal" id="proyecto'.$row[0].'">'.utf8_encode(ucwords(strtolower($row[18]))).'</label>'; ?>
                                            <select style="display: none;margin-left:-12px;width:80px" id="sltProyecto<?php echo $row[0]; ?>">
                                                <option value="<?php echo $row[17]; ?>"><?php echo $row[18]; ?></option>
                                                <?php 
                                                $sqlCP = "SELECT id_unico,nombre FROM gf_proyecto WHERE id_unico != $row[17]";
                                                $result = $mysqli->query($sqlCP);
                                                while ($y = mysqli_fetch_row($result)){
                                                    echo '<option value="'.$y[0].'">'.$y[1].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <!-- Campo de valor debito y credito. Validación para imprimir valor -->
                                        <td class="campos">
                                            <?php if($row[4]==1){
                                                  echo '<label style="font-weight:normal" id="debitoP'.$row[0].'">'.$row[19].'</label>';
                                                  echo '<input style="display:none;margin-left:-12px;width:70px" type="text" name="txtDebito'.$row[0].'" id="txtDebito'.$row[0].'" value="'.$row[19].'" />';
                                            }  else {
                                                echo '<label style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                                echo '<input type="text" style="didisplay:none;margin-left:-12px;width:70px" name="txtCredito'.$row[0].'"  id="txtCredito'.$row[0].'"/>';
                                            } ?>
                                        </td>
                                        <td class="campos">
                                            <?php if ($row[4] == 2) {
                                                echo '<label style="font-weight:normal" id="debitoP'.$row[0].'">'.$row[19].'</label>';
                                                echo '<input type="text" style="display:none;margin-left:-12px;width:70px" name="txtDebito'.$row[0].'" id="name="txtDebito'.$row[0].'"" value="value" />';
                                            }  else {
                                                echo '<label style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                                echo '<input type="text" style="display:none;margin-left:-12px;width:70px" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" />';
                                            } ?>
                                        </td>
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>                            
                    <?php 
                    #Suma de debito
                    $sqlD = "SELECT      SUM(DT.valor) 
                             FROM        gf_detalle_comprobante DT
                             LEFT JOIN   gf_cuenta CT ON DT.cuenta = CT.id_unico
                             LEFT JOIN   gf_naturaleza N ON CT.naturaleza = N.id_unico
                             WHERE 	 DT.naturaleza = 1";
                    $sumaD = $mysqli->query($sqlD);
                    $valorD = mysqli_fetch_row($sumaD);
                    #Suma credito
                    $sqlD = "SELECT      SUM(DT.valor) 
                             FROM        gf_detalle_comprobante DT
                             LEFT JOIN   gf_cuenta CT ON DT.cuenta = CT.id_unico
                             LEFT JOIN   gf_naturaleza N ON CT.naturaleza = N.id_unico
                             WHERE 	 DT.naturaleza = 2";
                    $sumaC = $mysqli->query($sqlD);
                    $valorC = mysqli_fetch_row($sumaC);
                    #Diferencia
                    $diferencia = $valorD[0] - $valorC[0];
                    ?>
                    <div class="col-sm-offset-7  col-sm-7">
                        <div class="col-sm-3">
                            <div class="form-group" style="margin-top:10px" align="left">                                    
                                <label class="control-label">
                                    <strong>Totales</strong>
                                </label>                                
                            </div>
                        </div>                        
                        <div class="col-sm-3" style="margin-top:10px;" align="left">
                            <?php 
                            if (($valorD[0]) === NULL) { ?>
                                 <label class="control-label" title="Suma débito">0</label>                   
                            <?php
                            }else { ?>
                                 <label class="control-label" title="Suma débito"><?php echo $valorD[0] ?></label>
                            <?php }
                            ?>
                        </div>                        
                        <div class="col-sm-3" style="margin-top:10px;" align="left">
                            <?php 
                            if ($valorC[0] === NULL) { ?>
                                <label class="control-label" title="Suma crédito">0</label>
                            <?php
                            }else{ ?>
                                <label class="control-label" title="Suma crédito"><?php echo $valorC[0]; ?></label>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-offset-7 col-sm-7" style="margin-top:-5px">
                        <div class="col-sm-3">
                            <div class="form-group" style="margin-top:-10px" align="left">                                    
                                <label class="control-label">
                                    <strong>Diferencia</strong>
                                </label>                                
                            </div>
                        </div>
                        <div class="col-sm-3 col-sm-offset-3" style="margin-top:-10px;" align="left">
                            <?php 
                            if ($diferencia === 0) { ?>
                                  <label class="control-label" title="Diferencia">0</label>                          
                            <?php }else{ ?>
                                  <label class="control-label" title="Diferencia"><?php echo $diferencia; ?></label>
                            <?php    
                            }
                            ?>
                        </div>
                    </div>
                </div>                
                <div class="col-sm-8 col-sm-2" style="margin-top:-139px"  >
                        <table class="tablaC table-condensed text-center" align="center">
                            <thead>
                                <tr>
                                    <tr>                                        
                                        <th>
                                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                        </th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_CUENTA_P.php">CUENTA</a>
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <!-- onclick="return ventanaSecundaria('registrar_GF_DESTINO.php')" -->
                                        <a class="btn btn-primary btnInfo" href="#">PERSONA</a>                                        
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="#">CENTRO COSTO</a>
                                    </td>
                                </tr>                               
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="#">PROYECTO</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>                
            </div>
        </div>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado de Detalle Comprobante?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información eliminada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal2" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="infoM" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información modificada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noModifico" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido modificar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="js/menu.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        
        <script type="text/javascript">           
            function eliminar(id){
                var result = '';
                $("#myModal").modal('show');
                $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarDetalleComprobanteJson.php?id="+id,
                    success: function (data) {
                    result = JSON.parse(data);
                    if(result==true)
                      $("#myModal1").modal('show');
                    else
                      $("#myModal2").modal('show');
                    }
                });
            });
        }
        </script>
        <script type="text/javascript">
            function modificar(id){
                var sltcuenta = 'sltC'+id;
                var lblCuenta = 'cuenta'+id;
                var sltTercero = 'sltTercero'+id;
                var lblTercero = 'tercero'+id;
                var sltCentroC = 'sltcentroC'+id;
                var lblCentroC = 'centroC'+id;
                var sltProyecto = 'sltProyecto'+id;
                var lblProyecto = 'proyecto'+id;
                var txtDebito = 'txtDebito'+id;
                var lblDebito = 'debitoP'+id;
                var txtCredito = 'txtCredito'+id;
                var lblCredito = 'creditoP'+id;
                
                $("#"+sltcuenta).css('display','block');                               
                $("#"+lblCuenta).css('display','none');
                $("#"+sltTercero).css('display','block');
                $("#"+lblTercero).css('display','none');
                $("#"+sltCentroC).css('display','block');
                $("#"+lblCentroC).css('display','none');
                $("#"+sltProyecto).css('display','block');
                $("#"+lblProyecto).css('display','none');
                $("#"+txtDebito).css('display','block');
                $("#"+lblDebito).css('display','none');
                $("#"+txtCredito).css('display','block');
                $("#"+lblCredito).css('display','none');                
                $("#guardar").css('display','block');
                
            }
        </script>
        <script type="text/javascript">
            function guardarCambios(id){
                var sltcuenta = 'sltC'+id;
                var sltTercero = 'sltTercero'+id;
                var sltCentroC = 'sltcentroC'+id;
                var sltProyecto = 'sltProyecto'+id;
                var txtDebito = 'txtDebito'+id;
                var txtCredito = 'txtCredito'+id;
                
                var form_data = {
                    is_ajax:1,
                    id:+id,
                    cuenta:$("#"+sltcuenta).val(),
                    tercero:$("#"+sltTercero).val(),
                    centroC:$("#"+sltCentroC).val(),
                    proyecto:$("#"+sltProyecto).val(),
                    debito:$("#"+txtDebito).val(),
                    credito:$("#"+txtCredito).val()
                };
                var result='';
                $.ajax({
                    type: 'POST',
                    url: "json/modificarDetalleComprobante.php",
                    data:form_data,
                    success: function (data) {
                        result = JSON.parse(data);
                        if (result==true) {
                            $("#infoM").modal('show');
                        }else{
                            $("#noModifico").modal('show');
                        }
                    }
                });
            }
        </script>
        <script type="text/javascript">
            $('#btnModifico').click(function(){
                document.location = 'registrar_GF_SALDOS_INICIALES.php';
            });
        </script>
        <script type="text/javascript">
            $('#btnNoModifico').click(function(){
                document.location = 'registrar_GF_SALDOS_INICIALES.php';
            });
        </script>
        <script type="text/javascript">
            $('#ver1').click(function(){
                document.location = 'registrar_GF_SALDOS_INICIALES.php';
            });
        </script>
        <script type="text/javascript">    
            $('#ver2').click(function(){  
                document.location = 'registrar_GF_SALDOS_INICIALES.php';
            });
        </script>
        <?php require_once './footer.php'; ?>
    </body>
</html>


