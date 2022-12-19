<?php
##########################################################################################
# *********************************** Modificaciones *********************************** # 
##########################################################################################
#05/02/2018 | Erica G. | Equivalente Vigencia Anterior
##########################################################################################
require_once ('./Conexion/conexion.php');
require_once ('./head_listar.php');
require_once './Conexion/ConexionPDO.php';
require_once './jsonPptal/funcionesPptal.php';
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
$nanno2 = $nanno-1;
$cann2 = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = $nanno2");
if(count($cann2)>0){
    $anno2 = $cann2[0][0];
} else {
    $anno2 = 0;
}

$id = $_GET['id'];

$query = "SELECT
            CT.id_unico,
            CT.codi_cuenta,
            CT.nombre, 
            NT.id_unico,
            NT.nombre,
            CC.id_unico,
            CC.nombre,
            CT.movimiento,
            CT.centrocosto,
            CT.auxiliartercero,
            CT.auxiliarproyecto,
            CT.activa,
            CT.dinamica,
            TPC.id_unico,
            TPC.nombre,
            CT.predecesor,
            (SELECT CT.nombre FROM gf_cuenta H WHERE CT.predecesor = H.id_unico),
            (SELECT CT.codi_cuenta FROM gf_cuenta H WHERE CT.predecesor = H.id_unico),            
            CTR.id_unico,
            CTR.nombre,
            CGN.id_unico,
            CGN.nombre,
            CT.equivalente_va, 
            CT.tercero_reciproca, 
            IF(CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos) 
                IS NULL OR CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos) = '',
                (t.razonsocial),
                CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos)) AS NOMBRE,
            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                t.numeroidentificacion, 
            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
        FROM gf_cuenta CT  
        LEFT JOIN gf_naturaleza NT ON CT.naturaleza = NT.id_unico
        LEFT JOIN gf_tipo_cuenta_cgn TPC ON CT.tipocuentacgn = TPC.id_unico
        LEFT JOIN gf_clase_cuenta CC ON CT.clasecuenta = CC.id_unico
        LEFT JOIN gf_centro_costo CTR ON CT.centrocosto = CTR.id_unico
        LEFT JOIN gf_tipo_cuenta_cgn CGN ON CT.tipocuentacgn = CGN.id_unico
        LEFT JOIN gf_tercero t ON CT.tercero_reciproca = t.id_unico 
        WHERE md5(CT.id_unico) = '$id'";
$cuenta = $mysqli->query($query);
$campo = mysqli_fetch_row($cuenta);

?>
    <link href="css/select/select2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        
        <script type="text/javascript">
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
            //var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $("#txtFechaInicial").datepicker({changeMonth: true}).val();        
            $("#txtFechaFinal").datepicker({changeMonth: true}).val(); 
        });
        </script> 
        
        <script type="text/javascript">
        $(document).ready(function() {
           var i= 0;
          $('#tablaMovimientos thead th').each( function () {
              if(i >= 0){ 
              var title = $(this).text();
              switch (i){
                case 0:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 1:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 2:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 3:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 4:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 5:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 6:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 7:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 8:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 9:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;                

              }
              i = i+1;
            }else{
              i = i+1;
            }
          } );

          // DataTable
         var table = $('#tablaMovimientos').DataTable({
            "autoFill": true,
            "scrollX": true,
            "pageLength": 5,
              "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No Existen Registros...",
                "info": "Página _PAGE_ de _PAGES_ ",
                "infoEmpty": "No existen datos",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
              },
              'columnDefs': [{
               'targets': 0,
               'searchable':false,
               'orderable':false,
               'className': 'dt-body-center'

            }]
         });

        var i = 0;
        table.columns().every( function () {
            var that = this;
            if(i!=0){
                $( 'input', this.header() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
                i = i+1;
                }else{
                    i = i+1;
                }
            } );
        } );
        </script>
        <style>
            .cabeza{
                white-space:nowrap;
                padding: 20px;
            }
            .campos{
                padding:-20px;
            }
            table.dataTable thead tr th,table.dataTable thead td{padding:1px 18px;font-size:10px}
            table.dataTable tbody td,table.dataTable tbody td{padding:1px;white-space: nowrap}
            .dataTables_wrapper .ui-toolbar{padding:2px}
            
            body{
                font-size: 10px
            }
        </style>
        
        <title>Modificar Cuenta</title>
    </head> 
    <body>
        <div class="container-fluid text-left" >
            <div class="row content">
                <?php require_once ('menu.php'); ?>                
                <div class="col-sm-7 text-left" style="margin-top: -22px;margin-left: -20px" >
                    <h2 class="tituloform" align="center">Modificar Cuenta</h2>
                    <div class="contenedorForma client-form" style="margin-top: -5px">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarCuentaPJson.php">
                            <input type="hidden" name="id" value="<?php echo $campo[0]; ?>"/>
                            <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>					
                            <div class="form-group" style="margin-top: -20px">
                                <label for="txtCodigoC" class="control-label col-sm-5" >
                                    <strong class="obligado">*</strong>Código Cuenta:                                   
                                </label>
                                <input value="<?php echo ucwords((mb_strtolower($campo[1]))); ?>" type="text" name="txtCodigoC" id="txtCodigoC" class="form-control" title="Ingrese código cuenta" onkeypress="return txtValida(event,'num')" maxlength="50" placeholder="Código Cuenta" required style="height: 30px" autofocus="" onkeyup="javascript:consultar();"/>
                            </div>
                            <script>
                                $("#txtCodigoC").change(function(){
                                    var codigo = document.getElementById('txtCodigoC').value;
                                    
                                    var form_data = {
                                                
                                                id:codigo
                                            };
                                        
                                            $.ajax({
                                                type:"POST",
                                                url:"consultasDatosCuenta/consultarExistente.php",
                                                data:form_data,
                                                success: function (data) {
                                                    var result = JSON.parse(data);
                                                    if(result==1){
                                                        $("#myModal1").modal('show');
                                                        document.getElementById('txtCodigoC').value="";
                                                    }
                                                }
                                            });
                                })
                            </script>
                            <div class="form-group" style="margin-top: -20px">
                                <label class="col-sm-5 control-label" for="txtNombre">
                                    <strong class="obligado">*</strong>Nombre:
                                </label>
                                <input type="text" value="<?php echo ucwords((mb_strtolower($campo[2]))); ?>"  name="txtNombre" id="txtNombre" class="form-control" title="Ingrese nombre" onkeypress="return txtValida(event,'num_car')" maxlength="100" placeholder="Nombre" required style="height: 30px" autofocus=""/>
                            </div>                            
                            <div class="form-group" style="margin-top: -20px">
                                <?php 
                                $sql = "SELECT DISTINCTROW id_unico,nombre FROM gf_naturaleza WHERE id_unico != $campo[3] ORDER BY nombre ASC";
                                $nat = $mysqli->query($sql);                                
                                ?>
                                <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Naturaleza:
                                </label>
                                <select name="sltNaturaleza" id="sltNaturaleza" class="form-control" title="Seleccione naturaleza de la cuenta" style="height: 30px" required="">
                                    <option value="<?php echo $campo[3]; ?>"><?php echo ucwords((mb_strtolower($campo[4]))); ?></option>
                                    <?php 
                                    while ($fila = mysqli_fetch_row($nat)) {
                                        echo '<option value="'.$fila[0].'">'.ucwords((mb_strtolower($fila[1]))).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -20px">
                                <?php 
                                $sql = "SELECT DISTINCTROW id_unico,nombre FROM gf_clase_cuenta WHERE id_unico != $campo[5] ORDER BY nombre ASC";
                                $rs = $mysqli->query($sql);                                
                                ?>
                                <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Clase Cuenta:
                                </label>
                                <select name="sltClaseC" id="sltClaseC" class="form-control" title="Seleccione clase cuenta" style="height: 30px" required="">
                                    <option value="<?php echo $campo[5]; ?>"><?php echo ucwords((mb_strtolower($campo[6]))); ?></option>
                                    <?php 
                                    while ($fila = mysqli_fetch_row($rs)) {
                                        echo '<option value="'.$fila[0].'">'.ucwords((mb_strtolower($fila[1]))).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top:-28px">                                    
                                <label class="control-label col-sm-5" for="optMov">
                                     <strong class="obligado">*</strong>Movimiento:
                                </label>                                
                                <?php 
                                if ($campo[7]==1) {
                                     ?>
                                        <input type="radio" name="optMov" id="optMov1"  title="Indicar si la cuenta recibe movimiento directamente, es decir son cuentas auxiliares" value="1" />SI
                                        <input type="radio" name="optMov" id="optMov2"  title="Indicar si la cuenta no recibe movimiento directamente, es decir no son cuentas auxiliares" value="2" />NO                                
                                        <script type="text/javascript" >
                                            $(document).ready(function(){
                                                $("#optMov1").prop('checked',true);
                                                
                                                if($("#optMov1").is(':checked')){
                                                    $("#optCentro1").attr('disabled',true);                                                
                                                    $("#optAuxT1").attr('disabled',true);
                                                    $("#optAuxP1").attr('disabled',true);
                                                } 
                                            });                                           
                                        </script>
                                    <?php
                                } else { ?>
                                        <input type="radio" name="optMov" id="optMov1"  title="Indicar si la cuenta recibe movimiento directamente, es decir son cuentas auxiliares" value="1" />SI
                                        <input type="radio" name="optMov" id="optMov2"  title="Indicar si la cuenta no recibe movimiento directamente, es decir no son cuentas auxiliares" value="2" />NO                                
                                        <script type="text/javascript" >
                                            $(document).ready(function(){
                                                $("#optMov2").prop('checked',true);
                                                if($("#optMov2").is(':checked')){
                                                    $("#optCentro1").attr('disabled',false);                                                
                                                    $("#optAuxT1").attr('disabled',false);
                                                    $("#optAuxP1").attr('disabled',false);
                                                } 
                                            });                                           
                                        </script>
                                    <?php
                                }
                                ?>                                
                            </div><br/>
                            <div class="form-group" style="margin-top:-28px">
                                <label for="optManP" class="control-label col-sm-5">
                                     <strong class="obligado">*</strong>Centro Costo:
                                </label>
                                <?php 
                                if($campo[8]==1) {
                                     ?>
                                        <input type="radio" name="optCentro" id="optCentro1" title="Indicar si la cuenta maneja centro de costo" value="1" />SI
                                        <input type="radio" name="optCentro" id="optCentro2" title="Indicar si la cuenta no maneja centro de costo" value="2"/>NO                                    
                                        <script type="text/javascript" >
                                            $(document).ready(function(){
                                                $("#optCentro1").attr('checked',true);                                                                                                
                                                if($("#optCentro1").is(':checked')){
                                                    $("#optMov1").prop('disabled',true);                                                     
                                                } 
                                            });                                           
                                        </script>
                                    <?php
                                } else { ?>
                                        <input type="radio" name="optCentro" id="optCentro1" title="Indicar si la cuenta maneja centro de costo" value="1"/>SI
                                        <input type="radio" name="optCentro" id="optCentro2" title="Indicar si la cuenta no maneja centro de costo" value="2" />NO                                    
                                        <script type="text/javascript" >
                                            $(document).ready(function(){
                                                $("#optCentro2").prop('checked',true);
                                                /*if($("#optCentro2").is(':checked')){
                                                    $("#optMov1").attr('disabled',false);                                                                                                    
                                                } */
                                            });                                           
                                        </script>
                                    <?php
                                }
                                ?>                                
                            </div><br/>
                            <div class="form-group" style="margin-top:-28px">                                    
                                <label class="control-label col-sm-5" for="optMov">
                                     <strong class="obligado">*</strong>Auxiliar Tercero:
                                </label>
                                <?php 
                                if ($campo[9]==1) {
                                    ?>  
                                        <input type="radio" name="optAuxT" id="optAuxT1"  title="Indicar si la cuenta maneja auxiliar de tercero" value="1"  />SI
                                        <input type="radio" name="optAuxT" id="optAuxT2"  title="Indicar si la cuenta no maneja auxiliar de tercero" value="2" />NO                                
                                        <script type="text/javascript" >
                                            $(document).ready(function(){
                                                $("#optAuxT1").prop('checked',true);
                                                if($("#optAuxT1").is(':checked')){
                                                    $("#optMov1").attr('disabled',true);                                                                                                    
                                                } 
                                            });                                           
                                        </script>
                                    <?php
                                } else { ?>
                                        <input type="radio" name="optAuxT" id="optAuxT1"  title="Indicar si la cuenta maneja auxiliar de tercero" value="1" />SI
                                        <input type="radio" name="optAuxT" id="optAuxT2"  title="Indicar si la cuenta no maneja auxiliar de tercero" value="2" />NO                                
                                        <script type="text/javascript" >
                                            $(document).ready(function(){
                                                $("#optAuxT2").prop('checked',true);                                                
                                                /*if($("#optAuxT2").is(':checked')){
                                                    $("#optMov1").attr('disabled',false);                                                                                                    
                                                } */
                                            });                                           
                                        </script>
                                    <?php
                                     
                                }
                                ?>                                
                            </div><br/>
                            <div class="form-group" style="margin-top: -28px">
                                <label for="optManP" class="control-label col-sm-5">
                                     <strong class="obligado">*</strong>Auxiliar Proyecto:
                                </label>
                                <?php 
                                if ($campo[10]==1) {
                                    ?>
                                        <input type="radio" name="optAuxP" id="optAuxP1" title="Indicar si maneja proyecto auxiliar" value="1" />SI
                                        <input type="radio" name="optAuxP" id="optAuxP2" title="Indicar si maneja proyecto auxiliar" value="2" />NO                                    
                                        <script type="text/javascript" >
                                            $(document).ready(function(){
                                                $("#optAuxP1").prop('checked',true);
                                                if($("#optAuxP1").is(':checked')){
                                                    $("#optMov1").attr('disabled',true);                                                                                                    
                                                } 
                                            });                                           
                                        </script>
                                    <?php
                                } else {  ?>
                                        <input type="radio" name="optAuxP" id="optAuxP1" title="Indicar si maneja proyecto auxiliar" value="1" />SI
                                        <input type="radio" name="optAuxP" id="optAuxP2" title="Indicar si maneja proyecto auxiliar" value="2" />NO                                    
                                        <script type="text/javascript" >
                                            $(document).ready(function(){
                                                $("#optAuxP2").prop('checked',true);
                                                /*if($("#optAuxP2").is(':checked')){
                                                    $("#optMov1").attr('disabled',false);                                                                                                    
                                                } */
                                            });                                           
                                        </script>
                                    <?php
                                }
                                ?>                                
                            </div><br/>
                            <div class="form-group" style="margin-top: -28px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Activa:                                    
                                </label>
                                <?php 
                                switch ($campo[11]) {
                                    case 1: ?>
                                        <input type="radio" name="optAct" id="optAct1" title="Indicar si la cuenta esta activa" value="1" checked=""/>SI
                                        <input type="radio" name="optAct" id="optAct2" title="Indicar si la cuenta esta no activa" value="2" />NO                                    
                                    <?php
                                        break;
                                    case 2: ?>
                                        <input type="radio" name="optAct" id="optAct1" title="Indicar si la cuenta esta activa" value="1"/>SI
                                        <input type="radio" name="optAct" id="optAct2" title="Indicar si la cuenta esta no activa" value="2" checked=""/>NO                                    
                                    <?php
                                        break;
                                }
                                ?>                                
                            </div><br/>
                            <div class="form-group" style="margin-top:-28px">
                                <label for="txtDinamica" class="col-sm-5 control-label">
                                    Dinamica:
                                </label>
                                <textarea type="text" name="txtDinamica" id="txtDinamica" title="Ingrese dinamica" class="form-control" onkeypress="return txtValida(event,'num_car')" maxlength="5000" placeholder="Dinamica" style="height: 45px;resize: both;"><?php echo trim(ucwords((($campo[12])))) ?></textarea>
                            </div>
                            <div class="form-group" style="margin-top: -20px">
                                
                                <label class="col-sm-5 control-label">
                                    Tipo Cuenta Cgn:
                                </label>
                                <select name="sltTipoCuentaCgn" id="sltTipoCuentaCgn" class="form-control" title="Seleccione tipo clase cuenta cgn" style="height: 30px" >                                                                        
                                    <?php 
                                    if(empty($campo[13])){
                                        echo '<option value="">Tipo Cuenta Cgn</option>';
                                        $sqlTipoC = "SELECT DISTINCTROW id_unico,nombre FROM gf_tipo_cuenta_cgn ORDER BY id_unico ASC";
                                        $resultTipoCGN = $mysqli->query($sqlTipoC);
                                        while($filatipoCGN=  mysqli_fetch_row($resultTipoCGN)){
                                            echo '<option value="'.$filatipoCGN[0].'">'.  ucwords(mb_strtolower($filatipoCGN[1])).'</option>';
                                        }
                                    }else{
                                        echo '<option value="'.$campo[13].'">'.ucwords((mb_strtolower($campo[14]))).'</option>';
                                        $query = "SELECT DISTINCTROW id_unico,nombre FROM gf_tipo_cuenta_cgn WHERE id_unico != $campo[13] ORDER BY id_unico ASC";
                                        $result = $mysqli->query($query); 
                                        while ($fila = mysqli_fetch_row($result)) {
                                            echo '<option value="'.$fila[0].'">'.ucwords((mb_strtolower($fila[1]))).'</option>';
                                        }
                                        
                                        echo '<option value=""></option>';
                                    }
                                    
                                    ?>                                    
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -20px">
                                <?php 
                                
                                ?>
                                <label class="col-sm-5 control-label">
                                    Predecesor:
                                </label>
                                <select name="sltPredecesor" class="select2_single form-control" id="sltPredecesor" title="Seleccione predecesor">                                    
                                    <?php 
                                    if(empty($campo[15])){?>
                                        <option value="">Predecesor</option>
                                        <?php 
                                        $sqlPadre = "SELECT id_unico,nombre,codi_cuenta FROM gf_cuenta WHERE (id_unico) != $campo[0] AND parametrizacionanno = $anno ORDER BY codi_cuenta ASC";
                                        $resultPadre = $mysqli->query($sqlPadre);
                                        while($rowPadre = mysqli_fetch_row($resultPadre)){?>
                                            <option value="<?php echo $rowPadre[0]?>"><?php echo ucwords(mb_strtolower($rowPadre[2].' - '.$rowPadre[1]))?></option>
                                        <?php }                                        
                                    }else{
                                        $a = "SELECT id_unico,nombre,codi_cuenta FROM gf_cuenta WHERE (id_unico) = $campo[15]";
                                        $m = $mysqli->query($a);
                                        $t = mysqli_fetch_row($m);?>
                                        <option value="<?php echo $t[0] ?>"><?php echo ucwords(mb_strtolower($t[2].' - '.$t[1])) ?></option>
                                        <?php $query = "SELECT DISTINCTROW
                                                        PADRE.id_unico,                        
                                                        PADRE.codi_cuenta,
                                                        PADRE.nombre
                                                FROM
                                                                    gf_cuenta PADRE                                    
                                                WHERE PADRE.id_unico != $campo[15] AND PADRE.id_unico!=$campo[0] AND PADRE.parametrizacionanno = $anno "
                                                . "ORDER BY PADRE.codi_cuenta ASC";
                                                $rs = $mysqli->query($query);

                                        while($fila1 = mysqli_fetch_row($rs)){ ?>
                                            <option value="<?php echo $fila1[0];  ?>"><?php echo ucwords((mb_strtolower($fila1[1].' - '.$fila1[2]))); ?></option>
                                        <?php }
                                    }                                    
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group" style="margin-top: -10px">
                                <label class="col-sm-5 control-label">Equivalente Vigencia Anterior:</label>
                                <select name="sltEquivalente" id="sltEquivalente" class="select2_single form-control" title="Seleccione Código Equivalente Vigencia Anterior" style="height: 30px" >
                                    <?php if(empty($campo[22])) { 
                                        echo '<option value=""> - </option>';
                                        $query = "SELECT DISTINCTROW id_unico,codi_cuenta, nombre "
                                        . "FROM gf_cuenta WHERE parametrizacionanno = $anno2 ORDER BY codi_cuenta ASC";
                                        $result = $mysqli->query($query); 
                                    } else {
                                        $query1 = "SELECT DISTINCTROW id_unico,codi_cuenta, nombre 
                                        FROM gf_cuenta WHERE parametrizacionanno = $anno2 AND codi_cuenta = '".$campo[22]."'ORDER BY codi_cuenta ASC";
                                        $result1 = $mysqli->query($query1); 
                                        $fila1 = mysqli_fetch_row($result1);
                                        echo '<option value="'.$fila1[1].'">'.$fila1[1].' - '.ucwords((mb_strtolower($fila1[2]))).'</option>';
                                        $query = "SELECT DISTINCTROW id_unico,codi_cuenta, nombre "
                                        . "FROM gf_cuenta WHERE parametrizacionanno = $anno2 AND codi_cuenta !='".$campo[22]."'ORDER BY codi_cuenta ASC";
                                        $result = $mysqli->query($query); 
                                    }
                                    while ($fila = mysqli_fetch_row($result)) {
                                        echo '<option value="'.$fila[1].'">'.$fila[1].' - '.ucwords((mb_strtolower($fila[2]))).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -10px">
                                <label class="col-sm-5 control-label">Tercero Recíproca:</label>
                                <select name="terceroR" id="terceroR" class="select2_single form-control" title="Seleccione Tercero" style="height: 30px" >
                                    <?php 
                                    $compania = $_SESSION['compania'];
                                    if(empty($campo[23])) { 
                                        echo '<option value=""> - </option>';
                                        $id = 0;
                                    } else {
                                        $id = $campo[23];
                                        echo '<option value="'.$campo[23].'">'.ucwords((mb_strtolower($campo[24]))).' '.$campo[25].'</option>';
                                        echo '<option value=""> - </option>';
                                    }
                                    $query = "SELECT DISTINCTROW t.id_unico,IF(CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos) 
                                            IS NULL OR CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos) = '',
                                            (t.razonsocial),
                                            CONCAT_WS(' ',
                                            t.nombreuno,
                                            t.nombredos,
                                            t.apellidouno,
                                            t.apellidodos)) AS NOMBRE,
                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                            t.numeroidentificacion, 
                                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))  
                                 FROM gf_tercero t  WHERE id_unico != $id AND compania = $compania";
                                $result = $mysqli->query($query); 
                                    while ($fila = mysqli_fetch_row($result)) {
                                        echo '<option value="'.$fila[0].'">'.ucwords((mb_strtolower($fila[1]))).' - '.$fila[2].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top:5px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -12px; margin-bottom: -10px; margin-left: 0px;">Guardar</button>
                            </div>                            
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div>
                <script src="js/select/select2.full.js"></script>
                <script>
                    $(document).ready(function() {
                      $(".select2_single").select2({
                        allowClear: true
                      });
                    });
                </script>
                <div class="col-sm-7 col-sm-3">
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
                                    <div  class="btnConsultas" style="margin-bottom: 1px;" data-toggle="modal">
                                        <a href="#" id="linkEntreM" data-toggle="modal">
                                            MOVIMIENTOS EFECTUADOS               
                                            <script type="text/javascript">
                                                $("#linkEntreM").click(function(){                                                                                                                                                            
                                                    var form_data = {
                                                        cuenta:"1"
                                                    };
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: "consulta_MOVIMIENTOS_EFECTUADOS.php",
                                                        data: form_data,
                                                        success: function (data) {
                                                            $("#modalEntreMeses").modal('show');
                                                            $("#cuenta").val(<?php echo $campo[1]; ?>);
                                                            $("#cuenta").attr('title','<?php echo ucwords(mb_strtolower($campo[2])); ?>');
                                                        }
                                                    });
                                                });
                                            </script>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <a href="Registrar_GF_CLASE_CUENTA.php" class="btn btn-primary btnInfo">CLASE CUENTA</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="btnConsultas" style="margin-bottom: 1px;">
                                        <a href="#">
                                            MOVIMIENTO ENTRE MESES                                            
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GF_SECTOR.php">SECTOR</a>
                                </td>
                            </tr>                            
                            <tr>
                                <td>
                                    <div class="btnConsultas" style="margin-bottom: 1px;">
                                        <a href="#">
                                            GRAFICO DE<br/>SALDOS
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
        <div class="modal fade" id="myModal2" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Ingrese un código presupuestal.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Este código presupuestal ya existe.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once('footer.php'); ?>                       
        <script type="text/javascript" >
             function consultar(){
                var cod = document.form.txtCodigoC.value;
                if (cod == null || cod == '') {
                    $("#noC").modal('show');
                }else{
                    $.ajax({
                        data:{"codigo":cod},
                        type:"POST",
                        url:"consultasDatosCuenta/consultaCuenta.php",
                        success: function (data) { 
                            console.log(data);
                            $('.texto select').html(data).change();
                        }
                    });
                }
            }
            
            $("#optMov1").change(function(){                
                $("#optCentro1").attr('disabled',true);
                $("#optMov1").attr('disabled',false);
                $("#optAuxT1").attr('disabled',true);
                $("#optAuxP1").attr('disabled',true);
            });
            $("#optMov2").change(function(){                
                $("#optCentro1").attr('disabled',false);
                $("#optMov1").attr('disabled',false);
                $("#optAuxT1").attr('disabled',false);
                $("#optAuxP1").attr('disabled',false);
            });
            
            $("#optCentro1").change(function(){
                $("#optMov1").attr('disabled',true);
            });
            $("#optCentro2").change(function(){
                $("#optMov1").attr('disabled',false);
            });
            $("#optAuxT1").change(function(){
                $("#optMov1").attr('disabled',true);
            });
            $("#optAuxP1").change(function(){
                $("#optMov1").attr('disabled',true);
            });
            $("#optAuxP2").change(function(){
                $("#optMov1").attr('disabled',false);
            });
        </script>
        <?php require_once './consulta_MOVIMIENTOS_EFECTUADOS.php'; ?>     
        <script type="text/javascript">
            $("#modalEntreMeses").on('shown.bs.modal',function(){
                var dataTable = $("#tablaMovimientos").DataTable();
                dataTable.columns.adjust().responsive.recalc();
            });
        </script>
        
    </body>
</html>

