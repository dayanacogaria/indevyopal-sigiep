<?php 
#16/06/2017 --- Nestor B --- se agrego la función del datepicker y la función fecha inicial ademas se modificó la forma como se llama los calendarios
#11/07/2017 --- Nestor B --- se agregó los dias de nomina del periodo
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
    
$sql = "SELECT    d.anno, d.valor, d.fecha_inicio, d.fecha_final, d.tipo, t.nombre,d.id_unico
                FROM gc_int_desc d 
                LEFT JOIN gr_tipo_di t ON d.tipo =  t.id_unico
                WHERE md5(d.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    

    $pid   = $row[0];
    $pci   = $row[1];
    $pfeci = $row[2];
    $pfecf = $row[3];
    $pacu  = $row[4];
    $pest  = $row[5];
   

/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style >
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
</style>
<script src="js/jquery-ui.js"></script>
<script>

        $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
               
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();
        
        
});
</script>
<title>Modificar Int Desc</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Interés Descuento</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarIntDescJson.php">
                            <input type="hidden" name="id" value="<?php echo $row[6] ?>">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <!----------Campo para llenar código Interno-->
                            
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="Anno" class="col-sm-5 control-label"><strong class="obligado">*</strong>Año:</label>
                                <input type="text" name="Anno" value="<?php echo $pid ?>" id="Anno" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'num')" placeholder="Nombre" required>
                            </div>                                    
                            <!----------Fin Campo código Interno-->
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="txtvalor" class="col-sm-5 control-label"><strong class="obligado">*</strong>Año:</label>
                                <input type="text" name="txtvalor" value="<?php echo $pci ?>" id="txtvalor" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'dec')" placeholder="Nombre" required>
                            </div
                            <!----------Script para invocar Date Picker-->
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("#datepicker").datepicker();
                                });
                            </script>
                            <!------------------------- Campo para seleccionar Fecha Inicio-->
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="sltfechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicio:</label>
                                <?php                                         
                                    $pfeci = $row[2];
                                    if(!empty($row[2])||$row[2]!=''){
                                        $pfeci = trim($pfeci, '"');
                                        $fecha_div = explode("-", $pfeci);
                                        $anioi = $fecha_div[0];
                                        $mesi  = $fecha_div[1];
                                        $diai  = $fecha_div[2];
                                        $pfeci   = $diai.'/'.$mesi.'/'.$anioi;
                                    }else{
                                        $pfeci='';
                                    }
                                ?>
                                <input name="sltFechaI" id="sltFechaI" title="Ingrese Fecha Inicial" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1"   value="<?php echo $pfeci;?>">  
               
                            </div>
                            <!----------Fin Captura de Fecha Inicio-->
                            <!------------------------- Campo para seleccionar Fecha Fin-->
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="sltFechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Fin:</label>
                                <?php                                         
                                    $pfecf = $row[3];
                                    if(!empty($row[3])||$row[3]!=''){
                                        $pfecf = trim($pfecf, '"');
                                        $fecha_div = explode("-", $pfecf);
                                        $anioi = $fecha_div[0];
                                        $mesi  = $fecha_div[1];
                                        $diai  = $fecha_div[2];
                                        $pfecf   = $diai.'/'.$mesi.'/'.$anioi;
                                    }else{
                                        $pfecf='';
                                    }
                                ?>     
                                <input name="sltFechaF" id="sltFechaF" title="Ingrese Fecha Final" type="text" style="width: 140px;height: 30px" class="form-control col-sm-1" value="<?php echo $pfecf;?>" >  
               
                            </div>
                            <!----------Fin Captura de Fecha Fin-->
                            <!--- Consulta para llenar Estado Empleado-->
                            <?php 
                                if(empty($pest))
                                    $es   = "SELECT id_unico, nombre FROM gr_tipo_di";
                                else
                                    $es   = "SELECT id_unico, nombre FROM gr_tipo_di WHERE id_unico != $pacu";
                        
                                $esta = $mysqli->query($es);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5"><strong class="obligado"></strong>Tipo:</label>
                                <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo" style="height: 30px" required>
                                    <option value="<?php echo $pacu?>"><?php echo $pest ?></option>
                                    <?php 
                                        while ($filaES = mysqli_fetch_row($esta)) { ?>
                                            <option value="<?php echo $filaES[0];?>"><?php echo $filaES[1]; ?></option>
                                    <?php
                                        }
                                    ?>
                                    <option value="">-</option>
                                </select>   
                            </div>
                                              
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            </div>


                        </form>
                    </div>
            </div>                  
        </div>
    </div>
    <?php require_once './footer.php'; ?>

    
</body>
</html>
