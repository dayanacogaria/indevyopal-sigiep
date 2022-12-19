
<?php
#01/03/2017 --- Nestor B --- se agregó la librerría de busqueda rápida en los selects del formulario
#01/03/2017 --- Nestor B --- se modificó el formulario para que cuando unos de los campos llegue vacío este no se deforme
#08/03/2017 --- Nestor B --- se modificó el menú de información adicional, se ocultaron varios botonos que son novedades y no perternecen a hoja de vida
#03/08/2017 --- Nestor B --- se agrego el campo de salario integral
#28/08/2017 --- Nestor B --- se agregó el archivo básico de tipo de riesgo

require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
$_SESSION['url'] = 'modificar_GN_EMPLEADO.php';
  $sql = "SELECT    e.id_unico,
                    e.tercero,
                    ter.id_unico,
                    CONCAT_WS(' ',
                     ter.nombreuno,
                     ter.nombredos,
                     ter.apellidouno,
                     ter.apellidodos),
                    e.estado,
                    ee.id_unico,
                    ee.nombre,
                    e.cesantias,
                    rc.id_unico,
                    rc.nombre,
                    e.mediopago,
                    mp.id_unico,
                    mp.nombre,
                    e.unidadejecutora,
                    ue.id_unico,
                    ue.nombre,
                    e.grupogestion,
                    gg.id_unico,
                    gg.nombre,
                    e.codigointerno,
                    e.salInt,
                    e.tipo_riesgo,
                    cr.nombre, ROUND(cr.valor,2),
                    ter.id_unico,
                    e.equivalente_NE,
                    ne.nombre
                FROM gn_empleado e
                LEFT JOIN   gf_tercero ter          ON e.tercero = ter.id_unico
                LEFT JOIN   gn_estado_empleado ee   ON e.estado = ee.id_unico
                LEFT JOIN   gn_regimen_cesantias rc ON e.cesantias = rc.id_unico
                LEFT JOIN   gn_medio_pago mp        ON e.mediopago = mp.id_unico
                LEFT JOIN   gn_unidad_ejecutora ue  ON e.unidadejecutora = ue.id_unico
                LEFT JOIN   gn_grupo_gestion gg     ON e.grupogestion = gg.id_unico
                LEFT JOIN   gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
                LEFT JOIN   gn_tipo_contrato_nomina_e ne ON e.equivalente_NE = ne.id_unico
                where md5(e.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);
    $emid   = $row[0];
    $emter  = $row[1];
    $terid  = $row[2];
    $ternom = $row[3];
    $emest  = $row[4];
    $estid  = $row[5];
    $estnom = $row[6];
    $emcrc  = $row[7];
    $rcid   = $row[8];
    $rcnom  = $row[9];
    $emmp   = $row[10];
    $mpid   = $row[11];
    $mpnom  = $row[12];
    $emue   = $row[13];
    $ueid   = $row[14];
    $uenom  = $row[15];
    $emgg   = $row[16];
    $ggid   = $row[17];
    $ggnom  = $row[18];
    $emci   = $row[19];
    $si    = $row[20];
    $riesgo    = $row[21];
    $nomr    = $row[22].' - '.$row[23];
    $contrato    = $row[25];
    $nomCon    = $row[26];
   # $ret    = $row[23];
    $_SESSION['id'] = $row[0];
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './head.php';
?>
<title>Modificar Empleado</title>
<link href="css/select/select2.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php';  ?>
                <div class="col-sm-7 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-bottom: 20px; margin-right: 4px; margin-left: -10px;">Modificar Empleado</h2>
                    <a href="<?php echo 'listar_GN_EMPLEADO.php';?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(("Datos del Empleado"));?></h5>
                    <div style="margin-top:-5px; border: 4px solid #020324; border-radius: 10px; margin-left: -10px; margin-right: 4px;" class="client-form">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarEmpleadoJson.php">
                              <input type="hidden" name="id" value="<?php echo $emid ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
<!------------------------- Consulta para llenar campo Tercero-->
                        <?php
                        $ter = "SELECT          pt.perfil,
                                                pt.tercero,
                                                t.id_unico,
                                                CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)
                            FROM gf_perfil_tercero pt
                            LEFT JOIN gf_tercero t ON pt.tercero = t.id_unico
                            WHERE pt.perfil = 2 AND id_unico != $emter";
                        $tercero = $mysqli->query($ter);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tercero:
                            </label>
                            <select name="sltTercero" class="select2_single form-control" id="sltTercero" title="Seleccione tercero" style="height: 30px" required>
                            <option value="<?php echo $emter?>"><?php echo $ternom?></option>
                                <?php
                                while ($filaT = mysqli_fetch_row($tercero)) { ?>
                                <option value="<?php echo $filaT[2];?>"><?php echo $filaT[3]; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
<!----------Fin Consulta Para llenar Tercero-->
<!----------Campo para llenar código Interno-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="CodigoI" class="col-sm-5 control-label"><strong class="obligado"></strong>Código Interno:</label>
                     <input type="text" name="txtCodigoI" value="<?php echo $emci ?>" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre">
                </div>
<!----------Fin Campo código Interno-->
<!------------------------- Consulta para llenar Estado Empleado-->
                        <?php

                        if(empty($emest))
                            $es   = "SELECT id_unico, nombre FROM gn_estado_empleado";
                        else
                            $es   = "SELECT id_unico, nombre FROM gn_estado_empleado WHERE id_unico != $emest";

                        $esta = $mysqli->query($es);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estado:
                            </label>
                            <select name="sltEstado" class="select2_single form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                            <?php
                            if(!empty($estid)){
                            ?>

                            <option value="<?php echo $estid?>"><?php echo $estnom ?></option>
                            <?php
                            }else{
                            ?>
                            <option value="">-</option>
                                <?php
                            }
                                while ($filaES = mysqli_fetch_row($esta)) { ?>
                                <option value="<?php echo $filaES[0];?>"><?php echo $filaES[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""></option>
                            </select>
                        </div>
<!----------Fin Consulta Para llenar Estado Empleado-->
<!------------------------- Consulta para llenar Regimen Cesantías-->
                        <?php

                        if(empty($emcrc))
                            $rc   = "SELECT id_unico, nombre FROM gn_regimen_cesantias";
                        else
                            $rc   = "SELECT id_unico, nombre FROM gn_regimen_cesantias WHERE id_unico != $emcrc";

                        $regc = $mysqli->query($rc);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Cesantías:
                            </label>
                            <select name="sltCesantias" class="select2_single form-control" id="sltCesantias" title="Seleccione Cesantías" style="height: 30px">
                            <?php
                            if(!empty($rcid)){
                            ?>
                            <option value="<?php echo $rcid?>"><?php echo $rcnom?></option>
                            <?php
                            }else{
                            ?>
                            <option value="">-</option>

                                <?php
                            }
                                while ($filaRC = mysqli_fetch_row($regc)) { ?>
                                <option value="<?php echo $filaRC[0];?>"><?php echo $filaRC[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""></option>
                            </select>
                        </div>
<!----------Fin Consulta Para llenar Regimen Cesantías-->
<!------------------------- Consulta para llenar Medio Pago-->
                        <?php
                        if(empty($emmp))
                            $mp   = "SELECT id_unico, nombre FROM gn_medio_pago";
                        else
                            $mp   = "SELECT id_unico, nombre FROM gn_medio_pago WHERE id_unico != $emmp";

                        $mpag = $mysqli->query($mp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Medio Pago:
                            </label>
                            <select name="sltMedioP" class="select2_single form-control" id="sltMedioP" title="Seleccione Medio Pago" style="height: 30px">
                            <?php
                            if(!empty($mpid)){
                            ?>
                            <option value="<?php echo $mpid?>"><?php echo $mpnom?></option>
                            <?php
                            }else{
                            ?>
                                <option value="">-</option>

                                <?php
                            }
                                while ($filaMP = mysqli_fetch_row($mpag)) { ?>
                                <option value="<?php echo $filaMP[0];?>"><?php echo $filaMP[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""></option>
                            </select>
                        </div>
<!----------Fin Consulta Para llenar Medio Pago-->
<!------------------------- Consulta para llenar Unidad Ejecutora-->
                        <?php
                        if(empty($emue))
                            $ue  = "SELECT id_unico, nombre FROM gn_unidad_ejecutora";
                        else
                            $ue  = "SELECT id_unico, nombre FROM gn_unidad_ejecutora WHERE id_unico != $emue";
                        $ueje = $mysqli->query($ue);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Unidad Ejecutora:
                            </label>
                            <select name="sltUnidadE" class="select2_single form-control" id="sltUnidadE" title="Seleccione Unidad Ejecutora" style="height: 30px">
                            <?php
                            if(!empty($ueid)){

                             ?>
                            <option value="<?php echo $ueid?>"><?php echo $uenom?></option>
                            <?php
                        }
                            else{
                              ?>
                              <option value="">-</option>
                              <?php
                          }
                                while ($filaUE = mysqli_fetch_row($ueje)) { ?>
                                <option value="<?php echo $filaUE[0];?>"><?php echo $filaUE[1]; ?></option>
                                <?php

                                }
                                ?>
                                <option value="">-</option>
                            </select>
                        </div>
<!----------Fin Consulta Para llenar Unidad Ejecutora-->
<!------------------------- Consulta para llenar Grupo Gestión-->
                        <?php

                        if(empty($emgg))
                            $gg   = "SELECT id_unico, nombre FROM gn_grupo_gestion";
                        else
                            $gg   = "SELECT id_unico, nombre FROM gn_grupo_gestion WHERE id_unico != $emgg";

                        $gges = $mysqli->query($gg);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Grupo Gestión:
                            </label>
                            <select name="sltGrupoG" class="select2_single form-control" id="sltGrupoG" title="Seleccione Grupo Gestión" style="height: 30px">
                            <?php
                            if(!empty($ggid)){
                            ?>
                            <option value="<?php echo $ggid?>"><?php echo $ggnom?></option>
                            <?php
                            }else{
                            ?>
                             <option value="">-</option>
                                <?php
                            }
                                while ($filaGG = mysqli_fetch_row($gges)) { ?>
                                <option value="<?php echo $filaGG[0];?>"><?php echo $filaGG[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""></option>
                            </select>
                        </div>
<!------------------------- Fin Consulta para llenar Grupo Gestión-->                        
<!------------------------- Consulta para llenar tipo riesgo-->
                        <?php

                        if(empty($emgg))
                            $cr   = "SELECT id_unico, nombre, ROUND(valor,2)  FROM gn_categoria_riesgos";
                        else
                            $cr   = "SELECT id_unico, nombre, ROUND(valor,2) FROM gn_categoria_riesgos WHERE id_unico != $riesgo";

                        $cres = $mysqli->query($cr);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Tipo de Riesgo:
                            </label>
                            <select name="sltRiesgo" class="select2_single form-control" id="sltRiesgo" title="Seleccione el Tipo de Riesgo" style="height: 30px">
                            <?php
                            if(!empty($riesgo)){
                            ?>
                            <option value="<?php echo $riesgo?>"><?php echo $nomr.'%'?></option>
                            <?php
                            }else{
                            ?>
                             <option value="">-</option>
                                <?php
                            }
                                while ($filaCR = mysqli_fetch_row($cres)) { ?>
                                <option value="<?php echo $filaCR[0];?>"><?php echo $filaCR[1].' - '.$filaCR[2].'%'; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""></option>
                            </select>
                        </div>
<!-------------------------Fin  Consulta para llenar tipo riesgo-->
<!------------------------- Consulta para llenar tipo contrato nomina electronica -->
                        <?php


                        $tipoC   = "SELECT id_unico, nombre FROM gn_tipo_contrato_nomina_E";

                        $tipCon = $mysqli->query($tipoC);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Tipo Contrato Nomina:
                            </label>
                            <select name="sltContrato" class="select2_single form-control" id="sltContrato" title="Seleccione el Tipo de Contrato" style="height: 30px">
                            <?php
                            if(!empty($contrato)){
                            ?>
                            <option value="<?php echo $contrato?>"><?php echo $nomCon?></option>
                            <?php
                            }else{
                            ?>
                             <option value="">-</option>
                                <?php
                            }
                                while ($filaCo = mysqli_fetch_row($tipCon)) { ?>
                                <option value="<?php echo $filaCo[0];?>"><?php echo $filaCo[1];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
<!------------------------- Fin Consulta para llenar tipo contrato nomina electronica -->

                        <div class="form-group" style="margin-top: -5px">
                                <label for="salaIn" class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Salario Integral:
                                </label>
                                <?php if($si == 1){ ?>
                                        <input type="radio" name="salaIn" id="salaIn" value="1" checked>SI
                                        <input type="radio" name="salaIn" id="salaIn" value="2" >NO
                                <?php }else { ?>
                                        <input type="radio" name="salaIn" id="salaIn" value="1">SI
                                        <input type="radio" name="salaIn" id="salaIn" value="2" checked>NO
                                <?php } ?>
                        </div>

                        <!--<div class="form-group" style="margin-top: -5px">
                                <label for="Retro" class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Regimen Retroactivo:
                                </label>
                                <?php # if($ret == 1){ ?>
                                        <input type="radio" name="Retro" id="Retro" value="1" checked>SI
                                        <input type="radio" name="Retro" id="Retro" value="2" >NO
                                <?php #}else { ?>
                                        <input type="radio" name="Retro" id="Retro" value="1">SI
                                        <input type="radio" name="Retro" id="Retro" value="2" checked>NO
                                <?php #} ?>
                        </div>   -->
                            <div class="form-group" style="margin-top: 10px;">
                              <label for="no" class="col-sm-5 control-label"></label>
                              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                              </div>
                              
                          </form>
                      </div>
                  </div>
<!-- Información Adicional-->
                  <div class="col-sm-7 col-sm-3" style="margin-top:-22px">
                <table class="tablaC table-condensed" style="margin-left: -30px;margin-top:-22">
                    <thead>
                        <th>
                            <h2 class="titulo" align="center" style="font-size:17px;">Información adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <!--<td>
                                <a href="registrar_GN_ACCIDENTE.php?idE=<?php echo md5($row[0]) ?>" style="margin-top:0px"><button class="btn btn-primary btnInfo" style="margin-top:0px">ACCIDENTE</button></a>
                            </td> -->
                            <td>
                               <a href="registrar_GN_AFILIACION.php?idE=<?php echo md5($emid)?>"><button class="btn btn-primary btnInfo" style="margin-top:0px">AFILIACIÓN</button></a><br/>
                            </td>
                        </tr>
                       <!-- <tr>
                            <td>
                               <a href="registrar_GN_CREDITO.php?idE=<?php echo md5($row[0])?>"><button class="btn btnInfo btn-primary btnInfo" style="margin-top:5px">CRÉDITO</button></a><br/>
                            </td>
                            <td>
                                <a href="registrar_GN_EMBARGO.php?idE=<?php echo md5($row[0])?>"><button class="btn btn-primary btnInfo" style="margin-top:5px">EMBARGO</button></a><br/>
                            </td>
                        </tr>-->
                        <tr>
                            <td>
                                <a href="registrar_GN_EMPLEADO_TIPO.php?idE=<?php echo md5($row[0])?>"><button class="btn btn-primary btnInfo" style="margin-top:5px">EMPLEADO TIPO</button></a><br/>
                            </td>
                          <!--<td>
                            <a href="registrar_GN_ENCARGO.php?idE=<?php echo md5($row[0])?>" class="btn btnInfo btn-primary" style="margin-top:5px">ENCARGO</a>
                          </td>-->
                        </tr>
                        <tr>
                          <td>
                              <a href="registrar_GN_ESTUDIO.php?idE=<?php echo md5($row[0])?>"><button class="btn btnInfo btn-primary" style="margin-top:5px">ESTUDIO</button></a><br/>
                          </td>
                        <tr>
                            <td>
                              <a href="registrar_GN_FAMILIAR.php?idE=<?php echo md5($row[0])?>"><button class="btn btnInfo btn-primary" style="margin-top:5px">FAMILIAR</button></a><br/>
                          </td>
                        </tr>
                        </tr>
                        <tr>
                         <!-- <td>
                              <a href="registrar_GN_HORAS_EXTRAS.php?idE=<?php echo md5($row[0])?>"><button class="btn btnInfo btn-primary" style="margin-top:5px">HORAS EXTRAS</button></a><br/>
                          </td>  -->
                          <td>
                            <a href="registrar_GN_IDIOMA_EMPLEADO.php?idE=<?php echo md5($row[0])?>" class="btn btnInfo btn-primary" style="margin-top:5px">IDIOMA EMPLEADO</a>
                          </td>
                        </tr>
                        <tr>
                         <!-- <td>
                            <a href="registrar_GN_INCAPACIDAD.php?idE=<?php echo md5($row[0])?>" class="btn btnInfo btn-primary" style="margin-top:5px">INCAPACIDAD</a>
                          </td>  -->
                          <td>
                            <a href="registrar_GN_LABORAL.php?idE=<?php echo md5($row[0])?>" class="btn btnInfo btn-primary" style="margin-top:5px">LABORAL</a>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <a href="registrar_GN_TIPO_RIESGO.php" class="btn btnInfo btn-primary" style="margin-top:5px">TIPO RIESGO</a>
                          </td>
                        </tr>
                        <tr>

                        <!--<td>
                            <a href="registrar_GN_NOVEDAD.php?idE=<?php echo md5($row[0])?>" class="btn btnInfo btn-primary" style="margin-top:5px">NOVEDAD</a>
                          </td> -->
                          <td>
                            <a href="registrar_GN_PENSIONADO.php?idE=<?php echo md5($row[0])?>" class="btn btnInfo btn-primary" style="margin-top:5px">PENSIONADO</a>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <a href="registrar_GN_TERCERO_CATEGORIA.php?idE=<?php echo md5($emid)?>" class="btn btnInfo btn-primary" style="margin-top:5px">TERCERO CATEGORÍA</a>
                          </td>
                       <!--   <td>
                              <a href="registrar_GN_VACACIONES.php?idE=<?php echo md5($row[0])?>"><button class="btn btnInfo btn-primary" style="margin-top:5px">VACACIONES</button></a><br/>
                          </td> -->

                        </tr>
                        <tr>
                            <td>
                            <a href="registrar_GN_VINCULACION_RETIRO.php?idE=<?php echo md5($emid)?>" class="btn btnInfo btn-primary" style="margin-top:5px">VINCULACION RETIRO</a>
                          </td>
                        </tr>
                        <tr>
                            <td>
                            <a href="registrar_GN_HOJADEVIDA.php?idE=<?php echo md5($row[0])?>" class="btn btnInfo btn-primary" style="margin-top:5px">HOJA DE VIDA</a>
                          </td>
                        </tr>
                    </tbody>
                </table>
            </div>
<!-- Fin Información Adicional-->
              </div>
        </div>
        <?php require_once './footer.php'; ?>
         <script src="js/select/select2.full.js"></script>
        <script>
         $(document).ready(function() {
         $(".select2_single").select2({

        allowClear: true
      });


    });
    </script>

    <script>
    </script>
    </body>
</html>
