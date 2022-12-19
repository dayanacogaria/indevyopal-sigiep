<?php
   require_once '../Conexion/conexion.php';
    session_start();
    $pa = ''.$_SESSION['anno'].'';
    //insert declaracion
    $perD       = $_POST['PD']; 
    $TiDE       = $_POST['TD'];
    $c          = $_POST['sltNumI'];
    $p          = $_POST['sltctai'];
    $vf         = $_POST['sltVig'];
    $fecha_dec  = $_POST['Fec_D'];
    #$fecha_dec  = $_POST['sltFechaDec'];
    $pesasM     = $_POST['PeMe'];
    $numD       = $_POST['txtnum'];
    
    #$numero  = ''.$mysqli->real_escape_string(''.$_POST['txtnum'].'').'';
    error_reporting(0);
    #$TiDE = 1;
    $fecha1 = trim($fecha_dec, '');
    $fecha_div = explode("/", $fecha1);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $hoy = ''.$anio1.'-'.$mes1.'-'.$dia1.'';
    
    #CONSULA SI EXISTE UNA DECLARACION DEL CONTRIBUYENTE EN EL PERIODO
    $declaracion = "SELECT * FROM gc_declaracion WHERE contribuyente = '$c' AND periodo = '$p'";
    $decla = $mysqli->query($declaracion);
    $nde = mysqli_num_rows($decla);
    
    if($nde < 1 ){
        
        $X = 0;
        $sql = "INSERT INTO gc_declaracion (cod_dec, contribuyente, periodo, vigencia,  tipo_per, tipo_dec, fecha, correccion, tipo_correc, observaciones,clase,parametrizacionanno)
             VALUES ($numD, $c, $p, $vf, $perD, $TiDE, '$hoy', NULL, NULL, NULL,1,$pa)";

        $resultado = $mysqli->query($sql);

        if($pesasM == "1"){
            $cont = "UPDATE gc_contribuyente SET pesas_medidas = 1 WHERE id_unico = '$c'";
            $resultado = $mysqli->query($cont);
        }
            $sqlD="SELECT MAX(id_unico) AS id FROM gc_declaracion WHERE contribuyente = '$c' AND periodo = '$p'";
            $resultadoD = $mysqli->query($sqlD);
            $rowD=mysqli_fetch_row($resultadoD);
            $idDeclaracion=$rowD[0];

            $X = $_POST;

            

            for($a = 1; $a < count($X); $a++){
                $nconcepto = 'iidConceptoComercial'.$a;
                $nvalue    = 'iValue'.$a;
                $vvalue    = $_POST["$nvalue"];
                $vconcepto = $_POST["$nconcepto"];
                if(!empty($vconcepto) && !empty($vvalue)){
                    $vvalue = str_replace(',', '', $vvalue);
                    $insertDetallesDeclaracion="INSERT INTO gc_detalle_declaracion (declaracion, concepto, valor, tipo_det) VALUES ($idDeclaracion, $vconcepto, $vvalue, 1)";
                    $resultado= $mysqli->query($insertDetallesDeclaracion);

                }

                $nVal = "idAutoL".$a;
                $vVal = $_POST["$nVal"];

                if(!empty($vconcepto) && !empty($vVal)){
                    $vVal = str_replace(',', '', $vVal);
                    $insertDetallesDeclaracion="INSERT INTO gc_detalle_declaracion (declaracion, concepto, valor, tipo_det) VALUES ($idDeclaracion, $vconcepto, $vVal, 2)";
                    $resultado= $mysqli->query($insertDetallesDeclaracion);
                }

                
                $idVeh = 'idve'.$a;
                $idv = $_POST["$idVeh"];
                $nvehiculo = 'idVehiculo'.$a;
                $nvalor = 'nameValorT'.$a."_";
                $vvehiculo = $_POST["$nvehiculo"];
                $vvalor = $_POST["$nvalor"];
                if( !empty($vvalor)){
                    $vvalor = str_replace(',', '', $vvalor);
                    $ingreso="INSERT INTO gc_declaracion_ingreso (declaracion, act_comer, tipo_ingreso, valor, inmueble) VALUES ($idDeclaracion, NULL,3, $vvalor, $idv)";
                    $resultado= $mysqli->query($ingreso);
                }
                
                $nvaP = "nameValorP".$a;
                $vvaP = $_POST["$nvaP"];
                
              
                if( !empty($vvaP)){
                    $vvaP = str_replace(',', '', $vvaP);
                    $ingreso="INSERT INTO gc_declaracion_ingreso (declaracion, act_comer, tipo_ingreso, valor, inmueble) VALUES ($idDeclaracion, NULL,4, $vvaP, $idv)";
                    $resultado= $mysqli->query($ingreso);
                }
                $idACO = 'idAct'.$a;
                $Nidact = $_POST["$idACO"];
                $nIBA = "idiBA".$a;
                $vIBA = $_POST["$nIBA"];
                
                if( !empty($vIBA)){
                    $vIBA = str_replace(',', '', $vIBA);
                    $ingreso="INSERT INTO gc_declaracion_ingreso (declaracion, act_comer, tipo_ingreso, valor, inmueble) VALUES ($idDeclaracion, $Nidact,5, $vIBA, NULL)";
                    $resultado= $mysqli->query($ingreso);
                }
                
                $nIdD = "idD".$a;
                $vIdD = $_POST["$nIdD"];
                if( !empty($vIdD)){
                    $vIdD = str_replace(',', '', $vIdD);
                    $ingreso="INSERT INTO gc_declaracion_ingreso (declaracion, act_comer, tipo_ingreso, valor, inmueble) VALUES ($idDeclaracion, $Nidact,6, $vIdD, NULL)";
                    $resultado= $mysqli->query($ingreso);
                }
                
                $nIdTE = "idIE".$a;
                $vIdTE = $_POST["$nIdTE"];
                if( !empty($vIdTE)){
                    $vIdTE = str_replace(',', '', $vIdTE);
                    $ingreso="INSERT INTO gc_declaracion_ingreso (declaracion, act_comer, tipo_ingreso, valor, inmueble) VALUES ($idDeclaracion, $Nidact,7, $vIdTE, NULL)";
                    $resultado= $mysqli->query($ingreso);
                }
                
                $nIdTEXT = "idIEXT".$a;
                $vIdTEXT = $_POST["$nIdTEXT"];
                if( !empty($vIdTEXT)){
                    $vIdTEXT = str_replace(',', '', $vIdTEXT);
                    $ingreso="INSERT INTO gc_declaracion_ingreso (declaracion, act_comer, tipo_ingreso, valor, inmueble) VALUES ($idDeclaracion, $Nidact,8, $vIdTEXT, NULL)";
                    $resultado= $mysqli->query($ingreso);
                }
                
                $nIdIFD = "idIFD".$a;
                $vIdIFD = $_POST["$nIdIFD"];
                if( !empty($vIdIFD)){
                    $vIdIFD = str_replace(',', '', $vIdIFD);
                    $ingreso="INSERT INTO gc_declaracion_ingreso (declaracion, act_comer, tipo_ingreso, valor, inmueble) VALUES ($idDeclaracion, $Nidact,9, $vIdIFD, NULL)";
                    $resultado= $mysqli->query($ingreso);
                }
                
                $nIdING = "idING".$a;
                $vIdING = $_POST["$nIdING"];
                if( !empty($vIdING)){
                    $vIdING = str_replace(',', '', $vIdING);
                    $ingreso="INSERT INTO gc_declaracion_ingreso (declaracion, act_comer, tipo_ingreso, valor, inmueble) VALUES ($idDeclaracion, $Nidact,8, $vIdING, NULL)";
                    $resultado= $mysqli->query($ingreso);
                }
                
                $nIdTarifa = "idTarifa".$a;
                $vIdTarifa = $_POST["$nIdTarifa"];
                
                if( !empty($vIdTarifa)){
                    $vIdTarifa = str_replace(',', '', $vIdTarifa);
                    $ingreso="INSERT INTO gc_declaracion_ingreso (declaracion, act_comer, tipo_ingreso, valor, inmueble) VALUES ($idDeclaracion, $Nidact,9, $vIdTarifa, NULL)";
                    $resultado= $mysqli->query($ingreso);
                }
                
                $nIdIMP = "idIMP".$a;
                $vIdIMP = $_POST["$nIdIMP"];
                if( !empty($vIdIMP)){
                    $vIdIMP = str_replace(',', '', $vIdIMP);
                    $ingreso="INSERT INTO gc_declaracion_ingreso (declaracion, act_comer, tipo_ingreso, valor, inmueble) VALUES ($idDeclaracion, $Nidact,10, $vIdIMP, NULL)";
                    $resultado= $mysqli->query($ingreso);
                }
            }

            $D = "dif27";
            $dif = $_POST["$D"];

            if($dif != 0){
                $dif = str_replace(',', '', $dif);
                $insertDetallesDeclaracion="INSERT INTO gc_detalle_declaracion (declaracion, concepto, valor, tipo_det) VALUES ($idDeclaracion, 28, $dif, 2)";
                $resultado= $mysqli->query($insertDetallesDeclaracion);

            }

            $ConDecS = "SELECT SUM(dd.valor) FROM gc_detalle_declaracion dd LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico WHERE dd.declaracion = '$idDeclaracion' AND cc.tipo_ope = 2 AND dd.tipo_det = 1 ";
            $ValCons = $mysqli->query($ConDecS);
            $ValCS = mysqli_fetch_row($ValCons);

            
            $ConDecR12 = "SELECT SUM(dd.valor) FROM gc_detalle_declaracion dd LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico WHERE dd.declaracion = '$idDeclaracion' AND cc.tipo_ope = 3 AND dd.tipo_det = 1 ";
            $ValConr = $mysqli->query($ConDecR12);
            $ValCR = mysqli_fetch_row($ValConr);
            
           

            $DIFERENCIA = $ValCS[0] - $ValCR[0];

            $ConNP = "SELECT dd.valor FROM gc_detalle_declaracion dd LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico WHERE dd.declaracion = '$idDeclaracion' AND cc.tipo_ope = 4 AND dd.tipo_det = 1 ";
            $ValNP = $mysqli->query($ConNP);
            $VNP = mysqli_fetch_row($ValNP);
            
            $ToDif = $VNP[0] - $DIFERENCIA;

            $concepto = "SELECT id_unico FROM  gc_concepto_comercial WHERE tipo = 4 AND tipo_ope = 2";
            $cocep = $mysqli->query($concepto);
            $IdCon = mysqli_fetch_row($cocep);
            
            $sql64 = "INSERT INTO gc_detalle_declaracion(declaracion,concepto,valor,tipo_det)VALUES($idDeclaracion,$IdCon[0],$ToDif,1)";
            $resultado = $mysqli->query($sql64);
        #}
    
        $num = 1;
        //insert detalles declaracion

        

        /*for ($i = 0; $i < count($miArray); $i++) {
        
            $idConceptoComercial=$miArray[$i][0];
            $valor=$miArray[$i][1];

            $insertDetallesDeclaracion="INSERT INTO gc_detalle_declaracion (declaracion, concepto, valor, tipo_det) VALUES ($idDeclaracion, $idConceptoComercial, $valor, 3);";
            $resultado= $mysqli->query($insertDetallesDeclaracion);
        }
     */
    
    }else{
        $X = 1;
    }
    
    
    #echo $resultado;

?>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
        <script src="../js/md5.pack.js"></script>
        <script src="../js/jquery.min.js"></script>
        <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
        <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
        <link rel="stylesheet" href="../css/select2.css">
        <link rel="stylesheet" href="../css/select2-bootstrap.min.css"/>
        
   </head>
    <body>
    </body>
</html>
<!--Modal para informar al usuario que se ha registrado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información guardada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
</div>
<!--Modal para informar al usuario que no se ha podido registrar -->
<div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <?php   if($X == 0){ ?>
                            <p>No se ha podido guardar la información.</p>
                <?php   }else{ ?>
                            <p>No se ha podido guardar la información. Debido a que ya existe una declaración para el contribuyente con el mismo periodo:<?php echo $p ?></p>
                <?php   } ?>            
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="LiqReca" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 450px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Recaudo</h4>
            </div>
            <div class="modal-body" >
                <div class="row text-left">
                    <form class="form-horizontal" id="formu" name="formu" method="post" action="registrarRecaudoJson.php">                                                        
                        <div class="form-group" >
                            <input type="hidden" name="txtTper" value="<?php echo $perD ?>">
                            <input type="hidden" name="txtTdec1" value="<?php echo $TiDE ?>">
                            <input type="hidden" name="txtfecD" value="<?php echo $fecha1 ?>">
                            <input type="hidden" name="X" value="1">
                            <input type="hidden" name="txtnumeral" value="1">
                            
                            <label for="banco" class="control-label col-sm-4 col-md-4 col-lg-4 text-right"><strong class="obligado">*</strong>Banco:</label>
                            <?php 
                                $per = "SELECT cb.id_unico, CONCAT(cb.numerocuenta,' - ',t.razonsocial)
                                    FROM gf_cuenta_bancaria cb
                                    lEFT JOIN  gf_cuenta_bancaria_tercero cbt ON cbt.cuentabancaria = cb.id_unico
                                    lEFT JOIN  gf_tercero t ON cb.banco = t.id_unico
                                    WHERE cbt.tercero = 1 AND cb.parametrizacionanno = '$pa'";
                                $periodo = $mysqli->query($per);
                            ?> 
                            <div class="col-sm-6 col-md-6 col-lg-6">    
                                <select  id="banco" name="banco" class="form-control select2" title="Seleccione el Banco" required>
                                    <option value="" >Banco</option>
                                    <?php 
                                        while($rowE = mysqli_fetch_row($periodo)){
                                            echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                        }
                                    ?>                     
                                </select>
                            </div>    
                        </div>
                        <div class="form-group">
                            <!--<script type="text/javascript">
                                    $(document).ready(function() {
                                    $("#datepicker").datepicker();
                            });
                            </script>-->
                            <label for="sltFecha" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Fecha:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <input name="sltFecha" id="sltFecha" title="Ingrese Fecha " type="date"  class="form-control "   placeholder="Ingrese la fecha">
                            </div>    
                        </div>
                        <div class="form-group"  ">
                            <label for="txtconsg" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" >Consignación o Pago:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <input name="txtconsg" id="txtconsg" title="Ingrese Número Consignación " type="text" class="form-control " placeholder="Ingrese Número Consignación o Pago ">
                            </div>    
                        </div>
                        <div class="form-group"  ">
                            <?php 
                                $Declaracion = "SELECT id_unico FROM gc_declaracion WHERE cod_dec = '$numD' AND clase = 1 ";
                                $Declara = $mysqli->query($Declaracion);
                                $Dec = mysqli_fetch_row($Declara);
                            ?>
                            <input type="hidden" name="txtidDec" value="<?php echo $Dec[0] ?>">
                            <label for="txtnum" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Declaración:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <input name="txtnum" id="txtnum" title="Ingrese el Número Documento" type="text" class="form-control " value="<?php echo $numD ?>"  placeholder="Ingrese el Número Documento" readonly>
                            </div>    
                        </div>
                        <div class="form-group"  align="left">
                            <label for="sltTipoR" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Tipo Recaudo:</label>
                            <?php 
                                $tiporec = "SELECT id_unico, nombre FROM gc_tipo_recaudo WHERE id_unico = 1 ";
                                $treca = $mysqli->query($tiporec);
                            ?> 
                            <div class="col-sm-6 col-md-6 col-lg-6">    
                                <select  id="sltTipoR" name="sltTipoR" class="form-control select2" title="Seleccione el Tipo de Recaudo" >
                                    <!--<option >Tipo Recaudo</option>-->
                                    <?php 
                                        while($rowTR = mysqli_fetch_row($treca)){
                                            echo "<option value=".$rowTR[0].">".$rowTR[1]."</option>";
                                        }
                                    ?>                     
                                </select>
                            </div>    
                        </div>
                    </form>
                </div>    
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="guardar" class="btn"  style="color: #000; margin-top: 2px"  title="Guardar Recaudo"><li class="glyphicon glyphicon-floppy-disk" ></button>    
                <button type="button" id ="soloDec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
            </div>  
        </div>
        
    </div>
        
    <script>
        $('#guardar').click(function(e){
            var cbanco  = $("#banco").val();
            var fecha  = $("#sltFecha").val();
            var pago   = $("#txtconsg").val();
            var tipo   = $("#sltTipoR").val();
            if(cbanco.length > 0 && fecha.length > 0 ){
                $('#formu').submit();
            }else{
                if(cbanco == ""){
                    //$("#banco").parents(".col-sm-6").addClass('has-error');
                    $("#s2id_banco").addClass('has-error');
                    //$("label[for='banco']").addClass('has-error');
                }
                if(fecha == ""){
                    $("#sltFecha").parents(".col-sm-6").addClass('has-error');
                }
                if(pago == ""){
                    $("#txtconsg").parents(".col-sm-6").addClass('has-error');
                }
                if(tipo == ""){
                    $("#s2id_sltTipoR").parents(".col-sm-6").addClass('has-error');
                }
            }
        });
    
        
    </script>
    <script>
        $('#soloDec').click(function(e){
            $("#LiqReca").modal('hide');
            window.location = "../registrar_GC_DECLARACION.php?peri2=<?php echo $perD ?>&TipoD=<?php echo $TiDE ?>&FD=<?php echo $fecha1 ?> ";
        });
    </script>
</div>
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/select2.js"></script>
        <script type="text/javascript"> 
            
            $("#banco").select2();

       
        </script>
  
<?php   if($resultado==true || $resultado==1){ ?>
            <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function(){
                    $("#myModal1").modal('hide'); 
                    $("#LiqReca").modal('show');
                    
                    //window.location = "../registrar_GC_DECLARACION.php?I=<?php echo md5($p)?>&N=<?php echo md5($c)?>&num=<?php echo md5($num)?>&cod=<?php echo $numD ?>&id_dec=<?php echo $idDeclaracion ?>&peri2=<?php echo $perD ?>&TipoD=<?php echo $TiDE ?>&FD=<?php echo $fecha_dec ?> ";
                    //window.history.go(-1);
                });
            </script>
<?php   }else{ ?>
            <script type="text/javascript">
                $("#myModal2").modal('show');
                $("#ver2").click(function(){
                    $("#myModal2").modal('hide');      
                   window.location = "../registrar_GC_DECLARACION.php?peri2=<?php echo $perD ?>&TipoD=<?php echo $TiDE ?>&FD=<?php echo $fecha_dec ?> ";
                });
            </script>
<?php   } ?>
