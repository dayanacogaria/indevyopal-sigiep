<?php ######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
#14/10/2017 | Erica G. | Ver Configuración
#04/05/2017 | Erica G. | Diseño, tíldes, búsquedas
######################################################################################################

#Llamamos a clase coenxión
require_once ('Conexion/conexion.php'); 
#Llamaos al head
require_once ('head.php');
#Capturamos la variable $id
$id = $_GET['id'];
#variable Session
$_SESSION['url']="modificar_GF_RUBRO_PPTAL.php?id=$id";
#consulta
$sql = "
    SELECT RP.id_unico,
       RP.nombre,
       RP.codi_presupuesto,
       RP.movimiento,
       RP.manpac,
       RP.vigencia,
       RP.dinamica,
       CPT.id_unico,
       CPT.nombre,
       (SELECT H.nombre FROM gf_rubro_pptal H WHERE RP.predecesor = H.id_unico),
       (SELECT H.codi_presupuesto FROM gf_rubro_pptal H WHERE RP.predecesor = H.id_unico ),
       DT.id_unico,
       DT.nombre,
       TV.id_unico,
       TV.nombre,
       SC.id_unico,
       SC.nombre, 
       RP.equivalente,
       RP.predecesor, 
       pa.anno 
   FROM gf_rubro_pptal RP
   LEFT JOIN gf_tipo_clase_pptal CPT ON RP.tipoclase = CPT.id_unico
   LEFT JOIN gf_destino DT ON RP.destino = DT.id_unico
   LEFT JOIN gf_tipo_vigencia TV ON RP.tipovigencia = TV.id_unico
   LEFT JOIN gf_sector SC ON RP.sector = SC.id_unico
   LEFT JOIN gf_parametrizacion_anno pa ON  RP.vigencia = pa.id_unico
   WHERE md5(RP.id_unico) = '$id'";
#Definimos la variable $ppto con los valores devueltos por la consulta
$ppto = $mysqli->query($sql);
#Definimos la variable $rubro con los valores devueltos de $ppto como un
#array númerico
$rubro = mysqli_fetch_row($ppto);
$param = $_SESSION['anno'];
?>
 <link href="css/select/select2.min.css" rel="stylesheet">
        <title>Modificar Rubro Presupuestal</title>
    </head>
    <body>
        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once ('menu.php'); ?>                
                <div class="col-sm-7 text-left" style="margin-top: -22px;margin-left: -10px">
                    <h2 class="tituloform" align="center">Modificar Rubro Presupuestal</h2>
                    <a href="listar_GF_RUBRO_PPTAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Rubro Presupuestal: <?php echo $rubro[2].' - '. ucwords(mb_strtolower($rubro[1])) ?></h5>
                    <div class="contenedorForma client-form" style="margin-top: -5px">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarRubroPptalJson.php">
                            <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>					
                            <input type="hidden" name="id" id="id" value="<?php echo $rubro[0]; ?>"/>                                                        
                            <div class="form-group" style="margin-top:-20px">
                                <label for="txtCodigoP" class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Código Presupuesto:
                                </label>
                                <input required="required" type="text" name="txtCodigoP" id="txtCodigoP" class="form-control" title="Ingrese código presupuestal" onkeypress="return txtValida(event,'num_car')"  placeholder="Código Presupuestal" style="height: 30px" value="<?php echo $rubro[2]; ?>"/>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <label for="txtNombre" class="control-label col-sm-5" >
                                    <strong class="obligado">*</strong>Nombre:                                   
                                </label>
                                <input required="required" type="text" name="txtNombre" id="txtNombre" class="form-control" title="Ingrese nombre" onkeypress="return txtValida(event,'num_car')" maxlength="100" placeholder="Nombre" required style="height: 30px" value="<?php echo (($rubro[1])); ?>"/>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <label for="" class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tipo Clase:
                                </label>
                                <?php 
                                #Consulta para cargar tipo Clase
                                $con = "SELECT id_unico,nombre FROM gf_tipo_clase_pptal WHERE id_unico != $rubro[7] ORDER BY nombre ASC";
                                #Ejecutamos la consulta cargandola en la conexión
                                $tipoC = $mysqli->query($con);
                                #Defimos la variable fila como array o vector númerico                                
                                ?>
                                <select required="required" name="sltTipoClase" class="form-control select2_single" title="Seleccione tipo clase" style="height: 30px">
                                    <option value="<?php echo $rubro[7]; ?>"><?php echo ucwords(($rubro[8])); ?></option>                                    
                                    <?php 
                                    while ($fila = mysqli_fetch_row($tipoC)) { ?>
                                        <option value="<?php echo $fila[0]; ?>"><?php echo ucwords(($fila[1])); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php 
                            $sql = "SELECT id_unico, nombre FROM gf_destino WHERE id_unico != '$rubro[11]' ORDER BY nombre ASC ";
                            $destino = $mysqli->query($sql);
                            ?>
                            <div class="form-group" style="margin-top: -10px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Destino:
                                </label>
                                <select required="required" name="sltDestino" class="form-control select2_single" title="Seleccione destino" style="height: 30px">
                                    <?php if(empty($rubro[11])) { 
                                        echo '<option value="">-</option>';
                                    } else { 
                                        echo '<option value="'.$rubro[11] .'">'.ucwords((mb_strtolower($rubro[12]))).'</option>';
                                    } 
                                    while ($fila1 = mysqli_fetch_row($destino)) { ?>
                                        <option value="<?php echo $fila1[0];?>"><?php echo ucwords(mb_strtolower($fila1[1])); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-horizontal" style="margin-top:-10px">                                    
                                <label class="control-label col-sm-5" for="optMov">
                                    <strong class="obligado">*</strong>Movimiento:
                                </label>
                                <?php 
                                #Validamos si el valor recibido por es si(1) o no(2)
                                switch ($rubro[3]){
                                    case 1: ?>
                                        <input type="radio" name="optMov" id="optMov"  title="Indicar si hay movimiento" value="1" checked/>SI
                                        <input type="radio" name="optMov" id="optMov"  title="Indicar no hay movimiento" value="2" />NO   
                                <?php
                                        break;
                                    default: ?>
                                       <input type="radio" name="optMov" id="optMov"  title="Indicar si hay movimiento" value="1" />SI
                                       <input type="radio" name="optMov" id="optMov"  title="Indicar no hay movimiento" value="2" checked/>NO   
                                <?php
                                        break;
                                } ?>                                
                                <label for="optManP" class="control-label col-sm-offset-1">
                                    Maneja PAC:
                                </label> 
                                <?php 
                                switch ($rubro[4]){
                                    case 1: ?>
                                        <input type="radio" name="optManP" id="optManP" title="Indicar si maneja PAC" value="1" checked/>SI
                                        <input type="radio" name="optManP" id="optManP" title="Indicar no maneja PAC" value="2" />NO                                     
                                <?php
                                        break;
                                    default: ?>
                                        <input type="radio" name="optManP" id="optManP" title="Indicar si maneja PAC" value="1" />SI
                                        <input type="radio" name="optManP" id="optManP" title="Indicar no maneja PAC" value="2" checked/>NO                                    
                                <?php
                                        break;
                                }
                                ?>                                
                            </div>
                            <!-- Actualización 09/02/2017: Verificación de envío de valor seleccionado-->
                            <div class="form-group" style="margin-top:-10px;">
                                <label for="sltVigencia" class="col-sm-5 control-label">
                                    <strong class="obligado">*</strong>Vigencia:
                                </label>
                                <select required="required" name="sltVigencia" class="form-control select2_single" title="Seleccione vigencia" style="height: 30px">
                                    <?php                                     
                                    $sql10="select id_unico, anno from gf_parametrizacion_anno where id_unico != '$rubro[5]'";
                                    $result10=$mysqli->query($sql10);
                                    if(!empty($rubro[19])) { 
                                    ?>
                                    
                                    <option value="<?php echo $rubro[5]?>"><?php echo  $rubro[19]; ?></option>
                                    <?php 
                                    }
                                    while ($v = mysqli_fetch_row($result10)) { ?>
                                    <option value="<?php echo $v[0]?>"><?php echo $v[1];?></option>             
                                    <?php }
                                    ?>
                                </select>
                            </div>
                            <!-- Fin Actualización 09/02/2017-->
                            <div class="form-group" style="margin-top:-15px">
                                <label for="txtDinamica" class="col-sm-5 control-label">
                                    <strong class="obligado"></strong>Dinamica:
                                </label>
                                <textarea  type="text" name="txtDinamica" id="txtDinamica" title="Ingrese dinamica" class="form-control" onkeypress="return txtValida(event,'num_car')" maxlength="5000" placeholder="Dinamica" style="height: 51px;resize: both;" ><?php echo ucwords(($rubro[6])); ?></textarea>
                            </div>
                            
                            <?php 
                            #cargamos la consulta
                            $sql = "SELECT DISTINCTROW
                                            PADRE.predecesor,                        
                                            PADRE.codi_presupuesto,
                                            PADRE.nombre
                                    FROM
                                            gf_rubro_pptal PADRE
                                    LEFT JOIN   
                                            gf_rubro_pptal HIJO
                                    ON
                                        PADRE.id_unico = HIJO.id_unico
                                    WHERE 
                                    (PADRE.codi_presupuesto != $rubro[10] ) AND PADRE.parametrizacionanno = $param";
                            $rb = $mysqli->query($sql);
                            ?>
                            <div class="form-group" style="margin-top: -20px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Predecesor:
                                </label>
                                <select name="sltPredecesor" class="form-control select2_single" title="Seleccione predecesor" style="height: 30px">
                                    <?php 
                                    if(!empty($rubro[18])){
                                        $sql11="select id_unico, CONCAT(codi_presupuesto,' - ',nombre) from gf_rubro_pptal where id_unico=$rubro[18]";
                                        $result11=$mysqli->query($sql11);
                                        $pr=mysqli_fetch_row($result11);
                                        echo "<option value='$pr[0]'>$pr[1]</option>";
                                        $sql111="select id_unico, CONCAT(codi_presupuesto,' - ',nombre) from gf_rubro_pptal where id_unico!=$rubro[18] and id_unico!=$rubro[0] AND parametrizacionanno = $param";
                                        $result111=$mysqli->query($sql111);
                                        while ($valores=mysqli_fetch_row($result111)) {
                                            echo "<option value='$valores[0]'>$valores[1]</option>";
                                        }
                                    }else{
                                        echo '<option value="">-</option>';
                                        $sql11="select id_unico, CONCAT(codi_presupuesto,' - ',nombre) from gf_rubro_pptal WHERE AND parametrizacionanno = $param";
                                        $result11=$mysqli->query($sql11);
                                        while ($pr=mysqli_fetch_row($result11)) {
                                            echo "<option value='$pr[0]'>$pr[1]</option>";
                                        }
                                    }
                                    ?>                                    
                                </select>
                            </div>                            
                            <?php 
                            $sql = "SELECT id_unico,nombre FROM gf_tipo_vigencia WHERE id_unico != $rubro[13] ORDER BY nombre ASC"; 
                            $tipoV = $mysqli->query($sql);      
                            ?>
                            <div class="form-group" style="margin-top:-10px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tipo Vigencia:
                                </label>
                                <select  required="required" name="stlTipoVigencia" class="form-control select2_single" title="Seleccione tipo vigencia" style="height: 30px">
                                    <option value="<?php echo $rubro[13]; ?>"><?php echo  ucwords((mb_strtolower($rubro[14]))) ?></option>
                                    <?php while ($fila2 = mysqli_fetch_array($tipoV)) { ?>
                                    <option value="<?php echo $fila2[0]; ?>"><?php echo ucwords(($fila2[1])) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php 
                            if (!empty($rubro[15])) {
                            $sql = "SELECT id_unico, nombre FROM gf_sector WHERE id_unico != $rubro[15] ORDER BY nombre ASC";
                            } else {
                                $sql = "SELECT id_unico, nombre FROM gf_sector ORDER BY nombre ASC";
                            }
                            $sect = $mysqli->query($sql);
                            ?>
                            <div class="form-group" style="margin-top: -10px" style="height: 30px">
                                <label class="control-label col-sm-5">
                                    Sector:
                                </label>
                                <select class="form-control select2_single" name="stlSector" id="stlSector" name="Seleccione secor">
                                    <?php if (!empty($rubro[15])) { ?>
                                    <option value="<?php echo $rubro[15]; ?>"><?php echo ucwords((mb_strtolower($rubro[16]))) ?></option>
                                    <?php } else { echo '<option value="">-</option>';}?>
                                    <?php while ($fila3 = mysqli_fetch_row($sect)) { ?>
                                    <option value="<?php echo $fila3[0]; ?>"><?php echo ucwords(($fila3[1])); ?></option>
                                    <?php   } ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -10px" style="height: 30px">
                                <label class="control-label col-sm-5">
                                    Equivalente:
                                </label>
                                <?php if($rubro[17]=='null' || $rubro[17]==NULL) { ?>
                                <input class="form-control" placeholder="Equivalente" type="text" name="equivalente" id="equivalente" title="Ingrese el código equivalente" onkeypress="return txtValida(event, 'num')" >
                                <?php }  else { ?>
                                <input class="form-control" placeholder="Equivalente" type="text" name="equivalente" id="equivalente" title="Ingrese el código equivalente" onkeypress="return txtValida(event, 'num')" value="<?php echo $rubro[17]?>">
                                <?php } ?>
                                
                            </div>
                            <div align="center">
                                <button type="submit" class="btn btn-primary sombra" style="margin-top: -18px; margin-bottom: 10px; margin-left: -50px;" >Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div>
                <div class="col-sm-7 col-sm-3" style="margin-left: 10px">
                        <table class="tablaC table-condensed" style="margin-left: -30px;margin-top: -22px">
                            <thead>
                                <tr>
                                    <tr>
                                        <th>
                                            <h2 class="titulo" align="center">Consultas</h2>
                                        </th>
                                        <th>
                                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                        </th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="btnConsultas" style="margin-bottom: 1px;">
                                            <a href="Movimiento_Rubro_Pttal.php?id=<?php echo md5($rubro[0]);?>">
                                                MOVIMIENTO PRESUPUESTAL
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_TIPO_CLASE_PPTAL.php">TIPO CLASE<br/>PRESUPUESTAL</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btnConsultas" style="margin-bottom: 1px;">
                                            <a href="Resumen_Mov_Rubro_Pttal.php?id=<?php echo md5($rubro[0]);?>">
                                                RESUMEN DE MOVIMIENTO
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <!-- onclick="return ventanaSecundaria('registrar_GF_DESTINO.php')" -->
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_DESTINO.php">DESTINO</a>                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btnConsultas" style="margin-bottom: 1px;">
                                            <a href="#">
                                                GRAFICOS DE<br/> SALDOS
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_SECTOR.php">SECTORES</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btnConsultas" style="margin-bottom: 1px;">
                                            <a href="#">
                                               MOVIMIENTO DE PAC ENTRE MESES
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btnConsultas" style="margin-bottom: 1px;">
                                            <a href="#" onclick="buscarCR();">
                                               CONFIGURACIÓN
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
        <?php require_once('footer.php'); ?>
    </body>
</html>

  <script>
      function buscarCR(){
        var form_data = { action: 3, id:+$("#id").val() };
        $.ajax({
          type: "POST",
          url: "jsonPptal/gf_rubro_pptalJson.php",
          data: form_data,
          success: function(response){
                resultado = JSON.parse(response);
                var data = resultado["respuesta"];
                var id   = resultado["id"];
                if(data==1){
                    window.open('Modificar_GF_CONCEPTO_RUBRO.php?id='+id);
                } else {
                    if(data==2){
                        $("#mdlConRub").modal("show");
                    } else {
                        $("#lblmsj1").html("Rubro No Está Configurado");
                        $("#mdlMensajes1").modal("show");
                    }
                }
                
          }
      });

      }
  </script>
  <div class="modal fade" id="mdlConRub" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label class="form_label"><strong style="color:#03C1FB;">*</strong>Concepto Rubro: </label>
                    <?php $id = $_GET['id'];
                     $bCR = "SELECT cr.id_unico, 
                            CONCAT(r.codi_presupuesto, ' - ', LOWER(r.nombre)), 
                            LOWER(c.nombre) 
                            FROM gf_concepto_rubro cr 
                            LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                            LEFT JOIN gf_rubro_pptal r ON r.id_unico = cr.rubro 
                            WHERE md5(cr.rubro) = '$id'";
                     $bCR = $mysqli->query($bCR);
                     echo '<select id="concepRubro" name="concepRubro" class="form-control select2_single" style="width:250px">';
                     echo '<option value="">Concepto Rubro</option>';
                     while ($row = mysqli_fetch_row($bCR)) {
                         echo '<option value="'.md5($row[0]).'">'. ucwords($row[1].' - '.$row[2]).'</option>';
                     }
                    echo '</select>'; 
                    ?>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="submit" id="btnConRubA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Aceptar</button>
                    <button type="submit" id="btnConRubC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Cancelar</button>
                </div>
                
            </div>
        </div>
    </div>
  <script>
      $("#btnConRubA").click(function(){
          var conr = $("#concepRubro").val();
          if(conr==""){
              $("#lblmsj1").html("Escoja Concepto Rubro");
              $("#mdlMensajes1").modal("show");
          } else {
             window.open('Modificar_GF_CONCEPTO_RUBRO.php?id='+conr); 
          }
      })
  </script>
  <div class="modal fade" id="mdlMensajes1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label style="font-weight: normal" id="lblmsj1" name="lblmsj" ></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="submit" id="btnMsjAceptar1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" autofocus="">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
  <script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<!-- select2 -->
 

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>