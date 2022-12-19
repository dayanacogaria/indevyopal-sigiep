<?php
require("./Conexion/ConexionPDO.php");
require './Conexion/conexion.php';
require './head.php';
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$con = new ConexionPDO();
if (!empty($_GET['id'])){
    $id = $_GET['id'];    
    $sqlcuenta = $con->Listar("
SELECT *
FROM gf_configuracion_traslado
WHERE md5(id_unico) = '$id'"
);
$idcuenta       = $sqlcuenta[0][0];
$cuentatraslado = $sqlcuenta[0][1];
$centrocosto    = $sqlcuenta[0][2];
$cuentadebito   = $sqlcuenta[0][3];
$costodebito    = $sqlcuenta[0][4];
$cuentacredito  = $sqlcuenta[0][5];
$costocredito   = $sqlcuenta[0][6];
}else {
    
}

?>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/jquery.datetimepicker.css">
<link rel="stylesheet" href="css/desing.css">
<title>Registrar Cuenta Traslado</title>
<style>
    #form>.form-group{
        margin-bottom: 5px !important;
    }
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;font-family: Arial;}
    .campos{padding: 0px;font-size: 10px}
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 class="tituloform" align="center" style="margin-top: 0px;">Configurar Cuentas Traslado </h2>                
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12 col-md-12 col-lg-12">
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="">
                            <input type="hidden" value="obligacion" name="expedir">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                            </p>                            
                            <input type="hidden" name="idcuenta" id="idcuenta" value="<?php echo $idcuenta;?>">
                            <input type="hidden" name="action" id="action">
                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;">
                                <div class="col-sm-3" align="left">                   
                                    <label for="lblcttraslado" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Cuenta Traslado:</label><br>
                                    <select name="sltcttraslado" id="sltcttraslado" class="select2_single form-control" title="Cuenta Traslado" style="width:180px; height: 38px" required>
                                        <option value="" >Cuenta Traslado</option>
                                            <?php
                                            $html = "";
                                            if (!empty($cuentatraslado)){
                                                $sqlcuen = $con->Listar("SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND id_unico = $cuentatraslado AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta");
                                                $idd = $sqlcuen[0][0];
                                                $nm1 = $sqlcuen[0][1];
                                                $nm2 = $sqlcuen[0][2];
                                                echo "<option value='$idd' selected>$nm1 - $nm2</option>";
                                                $sqper = "SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND id_unico <> $cuentatraslado AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta";
                                            $resper = $mysqli->query($sqper);
                                            while ($row = mysqli_fetch_row($resper)) {
                                                $html .= "<option value='$row[0]'>$row[1] - $row[2]</option>";
                                             }
                                            echo $html;
                                            }else{
                                                $sqper = "SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta";
                                            $resper = $mysqli->query($sqper);
                                            while ($row = mysqli_fetch_row($resper)) {
                                                $html .= "<option value='$row[0]'>$row[1] - $row[2]</option>";                                                
                                             }
                                            echo $html;
                                            }                                            
                                            ?>
                                    </select>
                                </div>
                                <div class="col-sm-3" align="left">
                                    <label for="lblcentrocosto" class="control-label" style=""><strong style="color:#03C1FB;"></strong>Centro Costo:</label><br>
                                    <select name="sltcosto" id="sltcosto" class="select2_single form-control" title="Centro Costo" style="width:180px; height: 38px" required>
                                        <option value="" >Centro Costo</option>
                                            <?php
                                            $html = "";
                                            if (!empty($centrocosto)){
                                            $sqlperi = $con->Listar("SELECT * FROM gf_centro_costo WHERE id_unico = $centrocosto AND movimiento = 1 AND parametrizacionanno = $anno AND compania = $compania" );
                                            $idd = $sqlperi[0][0];
                                            $nmperi = $sqlperi[0][1];
                                            echo "<option value='$idd' selected>$nmperi</option>";
                                                $sqper = "SELECT * FROM gf_centro_costo WHERE id_unico <> $centrocosto AND movimiento = 1 AND parametrizacionanno =  $anno
                                                AND compania = $compania";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                                }
                                                echo $html;
                                            }else {
                                                $sqper = "SELECT * FROM gf_centro_costo WHERE movimiento = 1 AND parametrizacionanno =  $anno
                                                AND compania = $compania";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                                }
                                                echo $html;
                                            }
                                                ?>
                                           
                                    </select>
                                </div>
                                <div class="col-sm-3" align="left">
                                    <label for="lblctdebito" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Cuenta Débito:</label><br>
                                    <select name="sltctdebito" id="sltctdebito" class="select2_single form-control" title="Cuenta Débito" style="width:180px; height: 38px" required>
                                        <option value="" >Cuenta Débito</option>
                                            <?php
                                            $html = "";
                                            if (!empty($cuentadebito)){
                                                $sqlcuen = $con->Listar("SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND id_unico = $cuentadebito AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta");
                                                $idd = $sqlcuen[0][0];
                                                $nm1 = $sqlcuen[0][1];
                                                $nm2 = $sqlcuen[0][2];
                                                echo "<option value='$idd' selected>$nm1 - $nm2</option>";
                                                $sqper = "SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND id_unico <> $cuentadebito AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta";
                                            $resper = $mysqli->query($sqper);
                                            while ($row = mysqli_fetch_row($resper)) {
                                                $html .= "<option value='$row[0]'>$row[1] - $row[2]</option>";
                                             }
                                            echo $html;
                                            }else{
                                                $sqper = "SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta";
                                            $resper = $mysqli->query($sqper);
                                            while ($row = mysqli_fetch_row($resper)) {
                                                $html .= "<option value='$row[0]'>$row[1] - $row[2]</option>";                                                
                                             }
                                            echo $html;
                                            }                                            
                                            ?>
                                    </select>
                                </div>
                                <div class="col-sm-3" align="left" >
                                    <label for="lblcentrodebito" class="control-label" style=""><strong style="color:#03C1FB;"></strong>Centro Costo Débito:</label><br>
                                    <select name="sltcostodebito" id="sltcostodebito" class="select2_single form-control" title="Centro Costo Débito" style="width:180px; height: 38px" required>
                                        <option value="" >Centro Costo Débito</option>
                                            <?php
                                            $html = "";
                                            if (!empty($costodebito)){
                                            $sqlperi = $con->Listar("SELECT * FROM gf_centro_costo WHERE id_unico = $costodebito AND movimiento = 1 AND parametrizacionanno =  $anno AND compania = $compania" );
                                            $idd = $sqlperi[0][0];
                                            $nmperi = $sqlperi[0][1];
                                            echo "<option value='$idd' selected>$nmperi</option>";
                                                $sqper = "SELECT * FROM gf_centro_costo WHERE id_unico <> $costodebito AND movimiento = 1 AND parametrizacionanno =  $anno
                                                AND compania = $compania";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                                }
                                                echo $html;
                                            }else {
                                                $sqper = "SELECT * FROM gf_centro_costo WHERE movimiento = 1 AND parametrizacionanno =  $anno
                                                AND compania = $compania";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                                }
                                                echo $html;
                                            }
                                                ?>
                                           
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px; margin-bottom: 0px;">
                                <div class="col-sm-3" align="left">
                                    <label for="lblctcredito" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Cuenta Crédito:</label><br>
                                    <select name="sltctcredito" id="sltctcredito" class="select2_single form-control" title="Cuenta Crédito" style="width:180px; height: 38px" required>
                                        <option value="" >Cuenta Crédito</option>
                                            <?php
                                            $html = "";
                                            if (!empty($cuentacredito)){
                                                $sqlcuen = $con->Listar("SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND id_unico = $cuentacredito AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta");
                                                $idd = $sqlcuen[0][0];
                                                $nm1 = $sqlcuen[0][1];
                                                $nm2 = $sqlcuen[0][2];
                                                echo "<option value='$idd' selected>$nm1 - $nm2</option>";
                                                $sqper = "SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND id_unico <> $cuentacredito AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta";
                                            $resper = $mysqli->query($sqper);
                                            while ($row = mysqli_fetch_row($resper)) {
                                                $html .= "<option value='$row[0]'>$row[1] - $row[2]</option>";
                                             }
                                            echo $html;
                                            }else{
                                                $sqper = "SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta";
                                            $resper = $mysqli->query($sqper);
                                            while ($row = mysqli_fetch_row($resper)) {
                                                $html .= "<option value='$row[0]'>$row[1] - $row[2]</option>";                                                
                                             }
                                            echo $html;
                                            }                                            
                                            ?>
                                    </select>
                                </div>
                                <div class="col-sm-3" align="left">
                                    <label for="lblcentrocredito" class="control-label" style=""><strong style="color:#03C1FB;"></strong>Centro Costo Crédito:</label><br>
                                    <select name="sltcostocredito" id="sltcostocredito" class="select2_single form-control" title="Centro Costo Crédito" style="width:180px; height: 38px" required>
                                        <option value="" >Centro Costo Crédito</option>
                                            <?php
                                            $html = "";
                                            if (!empty($costocredito)){
                                            $sqlperi = $con->Listar("SELECT * FROM gf_centro_costo WHERE id_unico = $costocredito AND movimiento = 1 AND parametrizacionanno =  $anno AND compania = $compania" );
                                            $idd = $sqlperi[0][0];
                                            $nmperi = $sqlperi[0][1];
                                            echo "<option value='$idd' selected>$nmperi</option>";
                                                $sqper = "SELECT * FROM gf_centro_costo WHERE id_unico <> $costocredito AND movimiento = 1 AND parametrizacionanno =  $anno
                                                AND compania = $compania";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                                }
                                                echo $html;
                                            }else {
                                                $sqper = "SELECT * FROM gf_centro_costo WHERE movimiento = 1 AND parametrizacionanno =  $anno
                                                AND compania = $compania";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                                }
                                                echo $html;
                                            }
                                                ?>
                                           
                                    </select>
                                </div>                                   
                                <div class="col-sm-6 col-md-6 col-lg-6 text-right" style="padding-top: 25px; padding-right: 78px;">                                    
                                    <button type="button" id="btnNuevo" onClick="window.location.href='GF_CONFIGURACION_TRASLADO.php';" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" >
                                        <li class="glyphicon glyphicon-plus"></li>
                                    </button>
                                    <button type="button" id="btnSave" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" >
                                        <li class="glyphicon glyphicon-floppy-disk"></li>
                                    </button>
                                    <button type="button" id="btnmodificar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Modificar" >
                                        <li class="glyphicon glyphicon-pencil"></li>
                                    </button>
                            </div>
                            </div>
                    </form>
                </div>
            </div>
            <script>
                $("#btnSave").click(function(e){       
                    e.preventDefault();
                    let form2 = $("#form");
                    $("#action").val(1);
                    if(form2.valid()){
                        $.ajax({                        
                        type: "POST",                 
                        url: "json/registrar_GF_CUENTA_TRASLADOJson.php",                     
                        data: $("#form").serialize(), 
                        success: function(data)             
                        {                          
                            let mensaje = "";
                            let id = "";
                            if(data != 0){
                              mensaje = "Información guardada correctamente.";
                              id = "?id="+data;
                            }else{
                              mensaje = "No se ha podido guardar la información";  
                              id = "#";
                            }
                            $('#pinfo').html(mensaje);
                            $('#mdlinfo').modal("show");
                            $("#btnconfirm").click(function(e){  
                                window.location.href = "GF_CONFIGURACION_TRASLADO.php"+id;
                            });
                        }
                    });
                    }                    
                });
                
                $("#btnmodificar").click(function(e){       
                    e.preventDefault();
                    let form2 = $("#form");
                    $("#action").val(2);
                    if(form2.valid()){
                        $.ajax({                        
                            type: "POST",                 
                            url: "json/registrar_GF_CUENTA_TRASLADOJson.php",                     
                            data: $("#form").serialize(), 
                            success: function(data)             
                            {                          
                                let mensaje = "";
                                let id = "";                                
                                if(data != 0){
                                  mensaje = "Información modificada correctamente.";
                                  id = "?id="+data;
                                }else{
                                  mensaje = "No se ha podido modificar la información";  
                                  id = "#";
                                }
                                $('#pinfo').html(mensaje);
                                $('#mdlinfo').modal("show");
                                $("#btnconfirm").click(function(e){  
                                    window.location.href = "GF_CONFIGURACION_TRASLADO.php"+id;
                                });
                            }                        
                    });
                    }                    
                });
                
                $("#stlamortizacion").change(function(){      
                    let id = $(this).val();
                    window.location.href = "registrar_GF_AMORTIZACION.php?id="+id;
                });
                
            </script>

            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 5px;">
                <div class="table-responsive contTabla" >
                    <table id="tabla212" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                        <thead style="position: relative;overflow: auto;width: 100%;">
                            <tr>
                                <td class="oculto">Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>C. Traslado</strong></td>
                                <td class="cabeza"><strong>Centro Costo</strong></td>
                                <td class="cabeza"><strong>C. Débito</strong></td>
                                <td class="cabeza"><strong>Costo Débito</strong></td>
                                <td class="cabeza"><strong>C. Crédito</strong></td>
                                <td class="cabeza"><strong>Costo Crédito</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%" class="cabeza"></th>
                                <th class="cabeza">C. Traslado</th>
                                <th class="cabeza">Centro Costo</th>
                                <th class="cabeza">C. Débito</th>
                                <th class="cabeza">Costo Débito</th>
                                <th class="cabeza">C. Crédito</th>
                                <th class="cabeza">Costo Crédito</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sqldetalles = "SELECT 
                                        mov.id_unico,
                                        CONCAT(cttraslado.codi_cuenta,' - ',cttraslado.nombre) AS cuentatraslado,
                                        cncosto.nombre AS centrocosto,
                                        CONCAT(ctdebito.codi_cuenta,' - ',ctdebito.nombre) AS cuentadebito,
                                        costodebito.nombre AS costodebito,
                                        CONCAT(ctcredito.codi_cuenta,' - ',ctcredito.nombre) AS cuentacredito,
                                        costocredito.nombre AS costodebito
                                        FROM gf_configuracion_traslado mov
                                        LEFT JOIN gf_cuenta cttraslado ON mov.cuenta_traslado = cttraslado.id_unico
                                        LEFT JOIN gf_centro_costo cncosto ON mov.centro_costo = cncosto.id_unico
                                        LEFT JOIN gf_cuenta ctdebito ON mov.cuenta_debito = ctdebito.id_unico
                                        LEFT JOIN gf_centro_costo costodebito ON mov.centro_costo_debito = costodebito.id_unico
                                        LEFT JOIN gf_cuenta ctcredito ON mov.cuenta_credito = ctcredito.id_unico
                                        LEFT JOIN gf_centro_costo costocredito ON mov.centro_costo_credito = costocredito.id_unico
                                        WHERE cttraslado.parametrizacionanno = $anno";
                            $resdetalles = $mysqli->query($sqldetalles);
                            while ($row = mysqli_fetch_row($resdetalles)) {
                                $idc = md5($row[0]);
                                echo "<tr>";                                
                                echo "<td class='oculto'></td>";
                                echo "<td>
                                    <a href='#' onclick='javascript:eliminar($row[0]);' title='Eliminar'>
                                            <li class='glyphicon glyphicon-trash'></li>
                                    </a>
                                    <a href= 'GF_CONFIGURACION_TRASLADO.php?id=$idc'  title='Modificar'>
                                            <li class='glyphicon glyphicon-edit'></li>
                                    </a>";
                                
                                echo "</td>";                                
                                echo "<td class='campos'>$row[1]</td>";
                                echo "<td class='campos'>$row[2]</td>";
                                echo "<td class='campos'>$row[3]</td>";
                                echo "<td class='campos'>$row[4]</td>";
                                echo "<td class='campos'>$row[5]</td>";
                                echo "<td class='campos'>$row[6]</td>";
                                echo "</tr>";
                            }                      
                        ?>   
                        </tbody>
                    </table>
                </div>
                
            </div>
            
        </div>        
    </div>
<div class="modal fade" id="mdlinfo" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p id="pinfo"></p>
            </div>
            <div id="forma-modal" class="modal-footer">                
                <button type="button" id="btnconfirm" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdlinfodel" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea está eliminar información?</p>
            </div>
            <div id="forma-modal" class="modal-footer">                
                <button type="button" id="btnconfirmdel" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" id="btncanceldel" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
            </div>
        </div>
    </div>
</div>
    <?php require_once 'footer.php'; ?>
    <script src="js/script_modal.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="js/script_date.js"></script>
    <script src="js/script_table.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script src="js/script.js"></script>    
    <script>
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });
            //melta
            let cuenta = $("#idcuenta").val();
            if (cuenta > 0){                
                $("#btnSave").prop("disabled", true);                
                $("#btneliminar").prop("disabled", false);
            }else {
                $("#btnmodificar").prop("disabled", true);
            }
        });
        
        function eliminar(id){
        $("#mdlinfodel").modal("show");        
            $("#btnconfirmdel").click(function(){
            $("#action").val(3);
            var form_data = {
                idcuenta: id,
                action:3
            };
                $.ajax({                        
                    type: "POST",                 
                    url: "json/registrar_GF_CUENTA_TRASLADOJson.php",                     
                    data: form_data, 
                    success: function(data)             
                    {                          
                        let mensaje = "";
                        let id = "";                                
                        if(data == 1 ){
                          mensaje = "Información eliminada correctamente.";
                        }else{
                          mensaje = "No se ha podido modificar la información";                            
                          id = "?id="+data;
                        }
                        $('#pinfo').html(mensaje);
                        $('#mdlinfo').modal("show");
                        $("#btnconfirm").click(function(e){  
                            window.location.href = "GF_CONFIGURACION_TRASLADO.php"+id;
                        });
                    }                        
                }); 
            }); 
        }
        
        $(document).ready(function () {
        var i = 1;
        $('#tabla212 thead th').each(function () {
            if (i != 1) {
                var title = $(this).text();
                switch (i) {
                    case 2:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 3:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 4:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 5:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 6:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 6:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 7:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 8:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 9:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 10:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 11:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 12:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 13:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 14:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 15:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 16:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 17:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 18:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 19:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                }
                i = i + 1;
            } else {
                i = i + 1;
            }
        });
        // DataTable
        var table = $('#tabla212').DataTable({
            "pageLength": 5,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No Existen Registros...",
                "info": "Página _PAGE_ de _PAGES_ ",
                "infoEmpty": "No existen datos",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ registros", "sInfoEmpty": "Mostrando 0 - 0 de 0 registros"
            },
            'columnDefs': [{
                    'targets': 0,
                    'searchable': false,
                    'orderable': false,
                    'className': 'dt-body-center'
                }]
        });
        var i = 0;
        table.columns().every(function () {
            var that = this;
            if (i != 0) {
                $('input', this.header()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that
                                .search(this.value)
                                .draw();
                    }
                });
                i = i + 1;
            } else {
                i = i + 1;
            }
        });
    });
    </script>
</body>
</html>
