<?php
##########################################################################################
#                           MODIFICACIONES
#29/06/2017 |ERICA G. | CAMBIAR EL LISTAR A OTRA VENTANA                           
##########################################################################################
#Llamamos a la clase de conexión
require_once ('./Conexion/conexion.php');
require_once ('./head_listar.php');
$parmanno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
?>
<style>
    .combo{
        border-radius: 2px;  
       
    }
    
    .combo:hover{
        cursor: pointer;
    }
    
    body{
        font-size: 10px
    }
</style>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<!-- select2 -->
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<title>Saldos Iniciales</title>
</head>
    <body>
        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-8 text-center" style="margin-top: -22px">
                    <h2 class="tituloform" align="center" >Saldos Iniciales</h2>
                    <div class="contenedorForma client-form" style="margin-top: -5px">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetalleComprobante.php" >
                            <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>				    
                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;"> <!-- Primera fila -->
                                <div class="col-sm-4" align="left" style="padding-left: 0px;">
                                    <?php 
                                    $sqlC = "SELECT id_unico,
                                                codi_cuenta,
                                                nombre 
                                        FROM    gf_cuenta
                                        WHERE   (movimiento = 1
                                        OR      centrocosto = 1
                                        OR      auxiliartercero = 1
                                        OR      auxiliarproyecto = 1 ) and parametrizacionanno = $parmanno 
                                        ORDER BY codi_cuenta ASC";
                                    $res = $mysqli->query($sqlC);
                                    ?>
                                    <label class="control-label col-sm-1" style="width: 80px">
                                        <strong class="obligado">*</strong>Cuenta:
                                    </label>
                                    <select name="sltcuenta" id="sltcuenta" autofocus="" class="form-control col-sm-1" style="width:160px;height:30px" title="Seleccione cuenta" required="">
                                        <option value="">Cuenta</option>
                                        <?php 
                                        while ($fila = mysqli_fetch_row($res)){ ?>
                                        <option value="<?php echo $fila[0]; ?>" ><?php echo ucwords( (mb_strtolower($fila[1].' - '.$fila[2]))) ?></option>    
                                        <?php                                         
                                        }
                                        ?>
                                        <script type="text/javascript">
                                            $(document).ready(function(){                                                
                                                var padre = 0;
                                                $("#slttercero").prop('disabled',true);
                                            $("#sltcuenta").change(function(){
                                                if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                                    padre = 0;         
                                                    $("#slttercero").prop('disabled',true);
                                                }else{
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax:1,
                                                    data:+padre
                                                };                                        
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultasDetalleComprobante/consultarTercero.php",
                                                    data:form_data,                                                    
                                                    success: function (data) {
                                                        var tercero = document.getElementById('slttercero');
                                                        if (data==1) {
                                                            tercero.disabled=false; 
                                                        }else if(data==2){
                                                            $("#slttercero").prop('disabled',true);
                                                        }                                                       
                                                    }
                                                });
                                            });
                                        });
                                        </script>
                                        <script type="text/javascript">
                                            $(document).ready(function(){                                                
                                                var padre = 0;
                                                $("#sltcentroc").prop('disabled',true);
                                            $("#sltcuenta").change(function(){
                                                if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                                    padre = 0;         
                                                    $("#sltcentroc").prop('disabled',true);
                                                }else{
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax:1,
                                                    data:+padre
                                                };                                        
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultasDetalleComprobante/consultarCentroC.php",
                                                    data:form_data,                                                    
                                                    success: function (data) {
                                                        var centro = document.getElementById('sltcentroc');
                                                        if (data==1) {
                                                            centro.disabled=false; 
                                                        }else if(data==2){
                                                            $("#sltcentroc").prop('disabled',true);
                                                        }                                                       
                                                    }
                                                });
                                            });
                                        });
                                        </script>
                                        <script type="text/javascript">
                                            $(document).ready(function(){                                                
                                                var padre = 0;
                                                $("#sltproyecto").prop('disabled',true);
                                            $("#sltcuenta").change(function(){
                                                if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                                    padre = 0;         
                                                    $("#sltproyecto").prop('disabled',true);
                                                }else{
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax:1,
                                                    data:+padre
                                                };                                        
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultasDetalleComprobante/consultaProyecto.php",
                                                    data:form_data,                                                    
                                                    success: function (data) {
                                                        var centro = document.getElementById('sltproyecto');
                                                        if (data==1) {
                                                            centro.disabled=false; 
                                                        }else if(data==2){
                                                            $("#sltproyecto").prop('disabled',true);
                                                        }                                                       
                                                    }
                                                });
                                            });
                                        });
                                        </script>
                                    </select>
                                </div>   
                                <div class="col-sm-4" align="left" style="padding-left: 0px;">
                                    <?php                                     
                                    $sql = "SELECT DISTINCT T.id_unico,
                                                    D.nombre,
                                                    T.numeroidentificacion,                                                     
                                                    CONCAT(T.nombreuno,' ',T.nombredos,' ',T.apellidouno,' ',T.apellidodos),
                                                    T.razonsocial
                                            FROM gf_tercero T 
                                            LEFT JOIN gf_tipo_identificacion D 
                                            ON T.tipoidentificacion = D.id_unico 
                                             
                                            ";
                                    $rs = $mysqli->query($sql);
                                    ?>
                                    <label class="control-label col-sm-1" style="width: 80px">
                                        <strong class="obligado">*</strong>Tercero:
                                    </label>
                                    <select name="slttercero" id="slttercero" class="form-control col-sm-1" style="width:160px;height:30px" title="Seleccione tercero" required="">
                                        <option value="2">Tercero</option>
                                        <?php 
                                        $sql191= "SELECT  IF(CONCAT_WS(' ',
                                                    tr.nombreuno,
                                                    tr.nombredos,
                                                    tr.apellidouno,
                                                    tr.apellidodos) 
                                                    IS NULL OR CONCAT_WS(' ',
                                                    tr.nombreuno,
                                                    tr.nombredos,
                                                    tr.apellidouno,
                                                    tr.apellidodos) = '',
                                                    (tr.razonsocial),
                                                    CONCAT_WS(' ',
                                                    tr.nombreuno,
                                                    tr.nombredos,
                                                    tr.apellidouno,
                                                    tr.apellidodos)) AS NOMBRE,
                                                    tr.id_unico, 
                                            CONCAT(tr.numeroidentificacion) AS 'TipoD' 
                                            FROM gf_tercero tr
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = tr.tipoidentificacion 
                                            WHERE tr.compania = $compania ORDER BY NOMBRE ASC ";
                                            $rs191 = $mysqli->query($sql191);
                                            while($row191 = mysqli_fetch_row($rs191)){
                                                echo '<option value="'.$row191[1].'">'.ucwords(mb_strtolower($row191[0].' - '.$row191[2])).'</option>';
                                            }                                     
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-4" align="left" style="padding-left: 0px;">
                                    <?php 
                                    $sqlCC = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico = 12 ORDER BY nombre ASC";
                                    $a = $mysqli->query($sqlCC);
                                    $filaC = mysqli_fetch_row($a);
                                    $sqlCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $filaC[0] ORDER BY nombre ASC";
                                    $r = $mysqli->query($sqlCT);
                                    ?>
                                    <label class="control-label col-sm-1" style="width: 80px;">
                                        <strong class="obligado">*</strong>Centro Costo:
                                    </label>
                                    <select name="sltcentroc" id="sltcentroc" class="form-control col-sm-1" style="width:160px;height:30px" title="Seleccione centro costo" required="">
                                        <option value="12">Centro Costo</option>
                                        <option value="<?php echo $filaC[0]; ?>"><?php echo ucwords( (mb_strtolower($filaC[1]))); ?></option>
                                        <?php 
                                        while($fila2=  mysqli_fetch_row($r)){ ?>
                                         <option value="<?php echo $fila2[0]; ?>"><?php echo ucwords( (mb_strtolower($fila2[1]))); ?></option>   
                                        <?php                                          
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;"> <!-- Primera fila -->
                                <div class="col-sm-4" align="left" style="padding-left: 0px;">
                                    <?php 
                                    $sqlP = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE nombre = 'VARIOS'" ;
                                    $d = $mysqli->query($sqlP);                                    
                                    $filaP = mysqli_fetch_row($d);
                                    $sqlPY = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $filaP[0]" ;
                                    $X = $mysqli->query($sqlPY);
                                    ?>
                                    <label class="control-label col-sm-1" style="width: 80px">
                                        <strong class="obligado">*</strong>Proyecto:
                                    </label>
                                    <select name="sltproyecto" id="sltproyecto" class="form-control col-sm-1" style="width:160px;height:30px" title="Seleccione proyecto" required="">
                                        <option value="<?php echo $filaP[0]; ?>">Proyecto</option>
                                        <option value="<?php echo $filaP[0]; ?>"><?php echo ucwords( (mb_strtolower($filaP[1]))) ?></option>
                                        <?php 
                                        while($fila3 = mysqli_fetch_row($X)){ ?>
                                            <option value="<?php echo $fila3[0]; ?>"><?php echo ucwords( (mb_strtolower($fila3[1]))) ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        $("#txtValorD").keyup(function(){
                                            $("#txtValorC").prop('disabled',true);
                                        });
                                        $("#txtValorC").keyup(function(){
                                            $("#txtValorD").prop('disabled',true);
                                        });
                                    });
                                    function justNumbers(e){   
                                        var keynum = window.event ? window.event.keyCode : e.which;
                                        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
                                        return true;
                                        return /\d/.test(String.fromCharCode(keynum));
                                    }
                                </script>
                                <div class="col-sm-4" align="left" style="padding-left: 0px;">
                                    <label class="control-label col-sm-1" style="width: 80px">
                                        Valor Débito:
                                    </label>
                                    <input type="text" name="txtValorD" onkeypress="return justNumbers(event);" id="txtValorD" maxlength="50" style="height:30px;width:160px"/>                                    
                                </div>
                                <div class="col-sm-4" align="left" style="padding-left: 0px;">
                                    <label class="control-label col-sm-1" style="width: 80px">
                                        Valor Crédito:
                                    </label>
                                    <input type="text"  name="txtValorC" onkeypress="return justNumbers(event);" id="txtValorC" maxlength="50" style="height:30px;width:160px"/>
                                </div>
                            
                            </div>
                            
                            <div class="form-group form-inline  "align="center" style="display: inline-block; margin-left: 500px">   
                                <input type="hidden" name="MM_insert" style="height: 10px">
                                <div class="col-sm-1 " style="margin-top:10px;margin-left: 10px">    
                                <button  type="submit" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                                </div>
                                <div class="col-sm-1" style="margin-top:10px;margin-left: 30px">    
                                <a class="btn sombra btn-primary" title="Imprimir" id="btnImprimir" onclick="informe();"><li class="fa fa-file-pdf-o" ></li></a>
                                </div>
                                <div class="col-sm-1" style="margin-top:10px;margin-left: 30px" >    
                                <a class="btn sombra btn-primary" title="Imprimir" id="btnImprimirExcel" onclick="informeExcel();"><li class="fa fa-file-excel-o" ></li></a>
                                </div>
                            </div>
                        </form>    
                    </div>
                    
                </div> 
                <!---Funciones Botones Informes -->
                <script>
                    function informe(){
                        window.open('informes/inf_saldos_iniciales.php?tipo=pdf');
                    } 
                    function informeExcel(){
                        window.open('informes/inf_saldos_iniciales.php?tipo=excel');
                    }
                </script>
                <div class="col-sm-8 col-sm-1" style="margin-top:-25px"  >
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
                                        <a onclick="javascript:abrirMT()" class="btn btn-primary btnInfo">PERSONA</a>                                       
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_CENTRO_COSTO.php">CENTRO COSTO</a>
                                    </td>
                                </tr>                               
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_PROYECTO.php">PROYECTO</a>
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="LISTAR_GF_SALDOS_INICIALES_CONTABILIDAD.php">LISTAR SALDOS <BR/>INICIALES</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>                
            </div>
        </div>
        
        <script type="text/javascript" src="js/select2.js"></script>
        <script>
            //Cuenta
            $("#sltcuenta").select2({
                placeholder : 'Cuenta',
                allowClear : true
            });
            //Tercero
            $("#slttercero").select2({
                placeholder : 'Cuenta',
                allowClear : true
            });
            //Centro costo
            $("#sltcentroc").select2({
                placeholder : 'Cuenta',
                allowClear : true
            });
            //Proyecto
            $("#sltproyecto").select2({
                placeholder : 'Cuenta',
                allowClear : true
            });
        </script>
        <?php require_once './footer.php'; ?>
    </body>
</html>


