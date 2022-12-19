<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#04/04/2019 | Creado
########################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('Conexion/ConexionPDO.php');
require 'jsonPptal/funcionesPptal.php';
require_once('head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
$n = 'Informe Saldos';
$action ='';
if($_REQUEST['t']==1){
   $n ='Informe Saldo En Costos';
   $action ='informes_consolidado/INF_ACUMULADO_CUENTAS.php?t=1';
}elseif($_REQUEST['t']==2){
   $n ='Cuentas Por Cobrar Con Saldo'; 
   $action ='informes_consolidado/INF_ACUMULADO_CUENTAS.php?t=2';
}elseif($_REQUEST['t']==3){
   $n ='Informe Recíprocas'; 
   $action ='informes_consolidado/INF_ACUMULADO_CUENTAS.php?t=3';
}elseif($_REQUEST['t']==4){
   $n ='Informe Cuentas Inactivas Con Saldo'; 
   $action ='informes_consolidado/INF_ACUMULADO_CUENTAS.php?t=4';
}
?>
<html>
    <head>
        <title><?php echo $n;?></title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="js/md5.pack.js"></script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px"><?php echo $n;?></h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="<?php echo $action;?>" target=”_blank” >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <?php if($_REQUEST['t']!=4 and $_REQUEST['t']!=1){ ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Inicial:</label>
                                <select name="mesI" id="mesI" class="select2_single form-control" title="Seleccione mes Inicial" style="height: auto " required>
                                    <?php 
                                    echo '<option value="">Mes Inicial</option>';
                                    $vg = $con->Listar("SELECT numero, mes  
                                        FROM gf_mes 
                                        WHERE parametrizacionanno = $anno ORDER BY cast(numero as unsigned)");
                                    for ($i = 0; $i < count($vg); $i++) {
                                       echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].'</option>'; 
                                    }                                    
                                    ?>
                                </select>
                                
                            </div>
                            <?PHP } ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Final:</label>
                                <select name="mesF" id="mesF" class="select2_single form-control" title="Seleccione mes final" style="height: auto " required>
                                    <?php 
                                    echo '<option value="">Mes Final</option>';
                                    $vg = $con->Listar("SELECT numero, mes  
                                        FROM gf_mes 
                                        WHERE parametrizacionanno = $anno ORDER BY cast(numero as unsigned) DESC");
                                    for ($i = 0; $i < count($vg); $i++) {
                                       echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].'</option>'; 
                                    }                                    
                                    ?>
                                </select>
                                
                            </div>
                            <?php if($_REQUEST['t']==3){ ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="ni" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Número Identificación:</label>
                                <input type="number" maxlength="9" name="ni" id="ni" class="form-control" required="required" placeholder="Número Identificación" title="Ingrese Número Identificación Sin Dígito de Verificación">
                            </div>    
                            <?php } ?>
                            <div align="center">
                                <button type="submit" class="btn btn-primary sombra" style="margin-top: 0px; margin-bottom: 10px; margin-left: -100px;" >Generar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    allowClear: true,
                });
            });
        </script>
    </body>
</html>
</html>

