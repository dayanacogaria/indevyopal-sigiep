<?php
   require_once '../Conexion/conexion.php';
   require_once '../Conexion/ConexionPDO.php';
   require_once '../jsonPptal/funcionesPptal.php';
    session_start();
    $pa = ''.$_SESSION['anno'].'';
    //insert declaracion
    $banco        = $_POST['banco'];
    $decla        = $_POST['txtidDec'];
    $fecha        = '"'.$_POST['sltFecha'].'"';
    $Tperi        = $_POST['txtTper'];
    $Tdecl        = $_POST['txtTdec1'];
    $fedcl        = $_POST['txtfecD'];
    $x            = $_POST['X'];
    $tipo            = $_POST['txtTipo'];
    $consig       = ''.$mysqli->real_escape_string(''.$_POST['txtconsg'].'').'';
    #$tipo    = ''.$mysqli->real_escape_string(''.$_POST['sltTipoR'].'').'';

   
    error_reporting(0);

    if(empty($consig)){
        $consig = "NULL";
    }else{
        $consig       = '"'.$mysqli->real_escape_string(''.$_POST['txtconsg'].'').'"';
    }

    $fecha_div = explode("/", $fecha);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $hoy = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';

     $DDC1 = "SELECT SUM(dd.valor) FROM gc_detalle_declaracion dd LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico left join gc_declaracion d ON dd.declaracion = d.id_unico
            WHERE dd.declaracion = '$decla' AND cc.tipo_ope = 1 AND  cc.tipo = 7 AND dd.tipo_det = 1 AND  d.clase = 2";
  
    $detDE1 = $mysqli->query($DDC1);
    $xwe = mysqli_fetch_row($detDE1);

    $vige = "SELECT anno FROM  gf_parametrizacion_anno WHERE id_unico = '$pa'";
    $vig = $mysqli->query($vige);
    $V = mysqli_fetch_row($vig);

    $CDecla = "SELECT MAX(consecutivo) FROM  gc_recaudo_comercial WHERE consecutivo LIKE '$V[0]%' AND parametrizacionanno = '$pa' ";
    $CodD = $mysqli->query($CDecla);
    $ncd = mysqli_num_rows($CodD);

    if($ncd > 0){

        $CD = mysqli_fetch_row($CodD);
        $code = $CD[0] + 1;
    }else{

        $code = $V[0]."00001";
    }

     $sql = "INSERT INTO gc_recaudo_comercial (consecutivo, declaracion, fecha, num_pag,  cuenta_ban, valor,observaciones, tipo_rec, rec_afect, acuerdo_pago, parametrizacionanno, clase)
             VALUES ($code, $decla, $fecha, $consig, $banco, $xwe[0],NULL,NULL, NULL,NULL,$pa, $tipo)";
    $resultado = $mysqli->query($sql);

    if($resultado==true){
        //Consulta de la ultima declarcion
        $sqlR="SELECT MAX(id_unico) AS id FROM gc_recaudo_comercial WHERE  declaracion = '$decla'";
        $resultadoR = $mysqli->query($sqlR);
        $rowR=mysqli_fetch_row($resultadoR);
        $idRecaudo=$rowR[0];

        $DDC = "SELECT dd.valor, dd.id_unico FROM gc_detalle_declaracion dd 
                LEFT JOIN gc_concepto_comercial cc ON dd.concepto = cc.id_unico 
                WHERE dd.declaracion = '$decla' AND dd.tipo_det = '1' AND  cc.tipo_ope is not null";
        
        $detDE = $mysqli->query($DDC);

        while ($rowDC = mysqli_fetch_row($detDE)) {
            $sql1 = "INSERT INTO gc_detalle_recaudo(recaudo,det_dec,valor)VALUES($idRecaudo,$rowDC[1],$rowDC[0])";
            $resultado = $mysqli->query($sql1);
        }


    }
    #echo $resultado;
    $num1 = "";
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
   </head>
    <body>
        <input type="hidden" id="idreca" name="idreca" value="<?php echo $idRecaudo  ?>">
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
                            <p>No se ha podido guardar la información. Debido a que ya existe una declaración para el contribuyente con el mismo periodo</p>
                <?php   } ?>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<script>

    function guardar(id){

        
        var val1 = 17;
        var val2 = 18;
                        
        //jsShowWindowLoad('Verificando..');
        var form_data = { action: val1, id:id };
        $.ajax({
            type: "POST",
            url: "../jsonPptal/gf_interfaz_ComercioJson.php",
            data: form_data,
            success: function(response){
                //jsRemoveWindowLoad();
                console.log(response);
                var resultado = JSON.parse(response);
                var rta = resultado["rta"];
                var mensaje = resultado["msj"];
                if(rta==1){
                    //jsShowWindowLoad('Guardando..');
                    var form_data = { action: val2, id:id };
                    $.ajax({
                        type: "POST",
                        url: "../jsonPptal/gf_interfaz_ComercioJson.php",
                        data: form_data,
                        success: function(response){
                            console.log(response);
                            //jsRemoveWindowLoad();
                            if(response==1){
                                $("#mensaje").html('Comprobante Guardado Correctamente');
                                $("#mdlMensajes").modal("show");
                                $("#btnAceptar").click(function(){
                                    //document.location.reload();
                                    <?php  if(!empty($x)){ ?>
                                                window.location = "../registrar_GC_DECLARACION.php?peri2=<?php echo $Tperi ?>&TipoD=<?php echo $Tdecl  ?>&FD=<?php echo $fedcl ?>";
                                    <?php   }else{ ?>
                                                window.history.go(-1);
                                    <?php   } ?>
                                });
                            }else{
                                $("#mensaje").html('No Se Ha Podido Registrar Comprobante');
                                $("#mdlMensajes").modal("show");
                                $("#btnAceptar").click(function(){
                                    //document.location.reload();
                                    window.location = "../registrar_GC_DECLARACION.php?peri2=<?php echo $Tperi ?>&TipoD=<?php echo $Tdecl  ?>&FD=<?php echo $fedcl ?>";
                                });
                            }
                        }
                    })
                }else{
                    $("#mensaje").html(mensaje);
                    $("#mdlMensajes").modal("show");
                    $("#btnAceptar").click(function(){
                        $("#mdlMensajes").modal("hide");
                        <?php   if(!empty($x)){ ?>
                                    window.location = "../registrar_GC_DECLARACION.php?peri2=<?php echo $Tperi ?>&TipoD=<?php echo $Tdecl  ?>&FD=<?php echo $fedcl ?>";
                        <?php   }else{ ?>
                                    window.history.go(-1);
                        <?php   } ?>
                    });
                }
            }
        })
    }
</script>
<script>

    function guardar(id){
        var val1 = 17;
        var val2 = 18;
                        //jsShowWindowLoad('Verificando..');
                        var form_data = { action: val1, id:id };
                        $.ajax({
                            type: "POST",
                            url: "../jsonPptal/gf_interfaz_ComercioJson.php",
                            data: form_data,
                            success: function(response)
                            {
                                //jsRemoveWindowLoad();
                                console.log(response);
                                var resultado = JSON.parse(response);
                                var rta = resultado["rta"];
                                var mensaje = resultado["msj"];
                                if(rta==1){
                                    //jsShowWindowLoad('Guardando..');
                                    var form_data = { action: val2, id:id };
                                    $.ajax({
                                        type: "POST",
                                        url: "../jsonPptal/gf_interfaz_ComercioJson.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            console.log(response);
                                            //jsRemoveWindowLoad();
                                            if(response==1){
                                                $("#mensaje").html('Comprobante Guardado Correctamente');
                                                $("#mdlMensajes").modal("show");
                                                $("#btnAceptar").click(function(){
                                                    //document.location.reload();
                                                    <?php   if(!empty($x)){ ?>
                                                                window.location = "../registrar_GC_ICA.php";
                                                    <?php   }else{ ?>
                                                                window.history.go(-1);
                                                    <?php   } ?>
                                                });
                                            } else {
                                                $("#mensaje").html('No Se Ha Podido Registrar Comprobante');
                                                $("#mdlMensajes").modal("show");
                                                $("#btnAceptar").click(function(){
                                                    //document.location.reload();
                                                    window.location = "../registrar_GC_ICA.php";
                                                });
                                            }
                                        }
                                    })
                                } else {
                                    $("#mensaje").html(mensaje);
                                    $("#mdlMensajes").modal("show");
                                    $("#btnAceptar").click(function(){
                                        $("#mdlMensajes").modal("hide");
                                        <?php   if(!empty($x)){ ?>
                                                    window.location = "../registrar_GC_ICA.php";
                                        <?php   }else{ ?>
                                                    window.history.go(-1);
                                        <?php   } ?>
                                    });
                                }
                            }
                        })



                    }
</script>
<?php   if($resultado==true || $resultado==1){ ?>
            <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function(){
                    $("#myModal1").modal('hide');
                    //window.location = "../registrar_GC_DECLARACION.php?peri2=<?php echo $Tperi ?>&TipoD=<?php echo $Tdecl  ?>&FD=<?php echo $fedcl ?>";
                    //window.history.go(-1);
                    var id = $("#idreca").val();
                    console.log("id: "+id);
                    guardar(id);

                });
            </script>
<?php   }else{ ?>
            <script type="text/javascript">
                $("#myModal2").modal('show');
                $("#ver2").click(function(){
                    $("#myModal2").modal('hide');
                   //window.location = "../registrar_GC_DECLARACION.php?I=<?php echo md5($p)?>&N=<?php echo md5($c) ?>";
                   
                   window.location = "../registrar_GC_ICA.php";
                });
            </script>
<?php   } ?>
