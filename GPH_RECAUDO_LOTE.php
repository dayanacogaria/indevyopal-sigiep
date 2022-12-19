<?php
#############################################################################
#       ******************     Modificaciones       ******************      #
#############################################################################
#14/09/2018 |Erica G. | ARCHIVO CREADO
#############################################################################
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];
if(!empty($_REQUEST['f'])){
    $fecha = fechaC($_REQUEST['f']);
    $rowd = $con->Listar("SELECT f.id_unico, 
    f.numero_factura, 
    DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), 
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
    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)),
    tf.prefijo, tf.nombre, 
    GROUP_CONCAT(df.id_unico), SUM(df.valor_total_ajustado) , 
    f.fecha_vencimiento, 
    IF(f.fecha_vencimiento>='$fecha', 1, 2),
    DATEDIFF( '$fecha',f.fecha_vencimiento), 
    f.id_espacio_habitable , 
    eh.codigo, eh.descripcion , 
    (SELECT COUNT(fs.id_unico) FROM gp_factura fs WHERE fs.id_espacio_habitable = f.id_espacio_habitable AND fs.fecha_factura>f.fecha_factura) 
    FROM gp_factura f 
    LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura 
    LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
    LEFT JOIN gf_tercero t ON f.tercero = t.id_unico    
    LEFT JOIN gh_espacios_habitables eh ON f.id_espacio_habitable = eh.id_unico 
    WHERE f.fecha_factura <='$fecha' 
    GROUP BY f.id_unico ORDER BY eh.id_unico, t.id_unico, f.fecha_factura" );
} else {
    $rowd =0;
}
?>
<html>
    <head>
        <title>Recaudo Facturación</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script>
        $(document).ready(function() {
            var i= 0;
            $('#tableO thead th').each( function () {
                if(i => 0) {
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
                } else {
                    i = i+1;
                }
            });
            
            var i2= 0;
            $('#tableO2 thead th').each( function () {
                if(i2 => 0) {
                    var title = $(this).text();
                    switch (i2){
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
                            
                            
                            
                    }
                    i2 = i2+1;
                } else {
                    i2 = i2+1;
                }
            });
            
            
            
            // DataTable
            var table = $('#tableO').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 220,
                "scrollX": true,
                scrollCollapse: true,
                paging: false,
                fixedColumns:   {
                    leftColumns: 1
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });
            
            // DataTable
            var table2 = $('#tableO2').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 120,
                "scrollX": true,
                scrollCollapse: true,
                paging: false,
                fixedColumns:   {
                    leftColumns: 1
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
                if(i!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            
            var i2 = 0;
            table2.columns().every( function () {
                var that = this;
                if(i2!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i2 = i2+1;
                } else {
                    i2 = i2+1;
                }
            });
        });
        </script>
        <style>
            label #tercero-error, #banco-error, #tipoRecaudo-error, #paquete-error, #fecha-error, #recaudo-error, #cupones-error,#valor-error { 
             display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;
        }
        body{
            font-size: 12px;
        }
        </style>
        <style>
    /*Modificación al diseño del la tabla Datatable*/
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}
    /*Modificación al body*/
    body{
        font-family: Arial;
        font-size: 10px;
    }

    /*Campos dinamicos label incrustados en la tabla*/
    .valorLabel{
        font-size: 10px;
        white-space:nowrap
    }
    .valorLabel:hover{
        cursor: pointer;
        color:#1155CC;
    }
    /*td de la tabla*/
    .campos{
        padding: 0px;
        font-size: 10px
    }
    .campoD{
        font-size: 12px;
        height: 19px;
        padding: 2px;
    }
    /*Estilos de cabeza y campos de la tabla
    */
    .cabeza{
        white-space:nowrap;
        padding: 20px;
    }
    .campos{
        padding:-20px;
    }

    .client-form input[type="text"]{
        width: 100%;
    }

    .client-form textarea{
        width: 100%;
        height: 34px;
    }

    .sombreado{
        box-shadow: 1px 1px 1px 1px gray;
        color:#fff;
        border-color:#1075C1;
    }

    .privada, .herencia{
        display: none;
    }
    
 
</style>
        <script>
        $().ready(function() {
          var validator = $("#form").validate({
                ignore: "",
            errorPlacement: function(error, element) {

              $( element )
                .closest( "form" )
                  .find( "label[for='" + element.attr( "id" ) + "']" )
                    .append( error );
            },
          });

          $(".cancel").click(function() {
            validator.resetForm();
          });
        });
        </script>

        <style>
         .form-control {font-size: 12px;}
        </style>
        <script>

                $(function(){
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
                $("#fecha").datepicker({changeMonth: true,}).val();


        });
        </script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Recaudo Facturación</h2>
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardard()" >  
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline " style="margin-top: -5px;margin-left: -10px">
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left: 0px;">
                                    <label for="fecha" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fecha:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  0px;">
                                    <input name="fecha" id="fecha" value="<?php if(!empty($_REQUEST['f'])){echo $_REQUEST['f'];}?>" onchange="cambiarfecha()" class="col-sm-4 form-control" title="Seleccione Fecha" required style="width: 95%"/>
                                </div>
                                <script>
                                     function cambiarfecha(){
                                         document.location='GPH_RECAUDO_LOTE.php?f='+$("#fecha").val();
                                     }
                                </script>
                                <div class="form-group form-inline  col-md-1 col-lg-1">
                                    <label for="banco" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Banco:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  0px;">
                                    <select name="banco" id="banco" class=" form-control select2" title="Seleccione Banco" style="height: auto;" required>
                                        <option value="">Banco</option>
                                        <?php 
                                        $rowB = $con->Listar("SELECT DISTINCT 
                                                ctb.id_unico,
                                                CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
                                                c.id_unico 
                                            FROM gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
                                            LEFT JOIN gf_cuenta c ON ctb.cuenta = c.id_unico 
                                            WHERE ctbt.tercero = $compania 
                                            AND ctb.parametrizacionanno = $anno 
                                            AND c.id_unico IS NOT NULL ORDER BY ctb.numerocuenta");
                                        if(count($rowB)>0){
                                            $j=0;
                                            while ($j < count($rowB)) {
                                                echo '<option value="'.$rowB[$j][0].'">'.$rowB[$j][1].'</option>';
                                                $j++;
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:0px;">
                                    <label for="tipoRecaudo" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Recaudo:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: 0px;">
                                    <select name="tipoRecaudo" id="tipoRecaudo" class="form-control select2" title="Seleccione Tipo Recaudo" style="height: auto; " required>
                                        <option value="">Tipo Recaudo</option>
                                        <?php 
                                        $tr = $con->Listar("SELECT * FROM gp_tipo_pago ORDER BY nombre ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                            echo '<option value="'.$tr[$i][0].'">'.$tr[$i][1].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-top:10px; float: right">
                                    <button style="margin-left:0px;" type="submit"  class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i></button>
                                </div>    
                               
                            </div>
                            <input type="hidden" name="facturas" id="facturas" value="0">
                            <input type="hidden" name="valor_seleccionado" id="valor_seleccionado" value="0">
                            <input type="hidden" value="<?php echo count($rowd)?>" id="num" name="num">
                    </div> 
                    <br/>
                    <?php if(!empty($_REQUEST['f'])){ ?>
                    <div class="form-group" style="margin-top: -15px">
                        <div  style="text-align:right">
                            <input style="margin-top:10px; margin-bottom: 10px;" type="checkbox" onclick="if (this.checked) marcar(true); else marcar(false);" /><strong style=" font-size: 12px;">&nbsp;&nbsp;Marcar/Desmarcar Todos</strong>
                        </div>
                        <script type="text/javascript">
                                function marcar(status) 
                                {
                                    var tabla1 = document.getElementById("tableO");
                                    var eleNodelist1 = tabla1.getElementsByTagName("input");
                                    var facturas = $("#facturas").val();
                                    var valor = parseFloat($("#valor_seleccionado").val());
                                    for (i = 0; i < eleNodelist1.length; i++) {
                                        var fact = 'factura'+i;
                                        var fact_sel  = $("#"+fact).val(); 
                                        var val = 'saldo'+i;
                                        if(typeof(fact_sel) !== "undefined"){
                                            if(status==true){       
                                                facturas +=','+fact_sel;
                                                valor +=parseFloat($("#"+val).val());
                                            } else {
                                                facturas = facturas.replace(','+fact_sel, "");
                                                valor -=parseFloat($("#"+val).val());
                                            }
                                        }
                                        $("#facturas").val(facturas);
                                        $("#valor_seleccionado").val(valor);
                                        vh = formatV(valor) ;
                                        $("#valorseleccionado").html('Valor Seleccionado: '+vh);
                                        if (eleNodelist1[i].type == 'checkbox'){
                                          
                                            if (status == null) {
                                                eleNodelist1[i].checked = !eleNodelist1[i].checked;
                                            }else {
                                                eleNodelist1[i].checked = status;
                                            }
                                        }
                                            
                                    }
                                }
                            </script>
                        <div class="table-responsive" style="margin-left: 0px; margin-right: 0px;margin-top:0px;">
                            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                                <table id="tableO" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="max-height: 200px">
                                    <thead>
                                        <tr>
                                            <td class="oculto">Identificador</td>
                                            <td width="7%"></td>
                                            <td class="cabeza"><strong>Tercero</strong></td>
                                            <td class="cabeza"><strong>Tipo Factura</strong></td>
                                            <td class="cabeza"><strong>Número</strong></td>
                                            <td class="cabeza"><strong>Espacio Habitable</strong></td>
                                            <td class="cabeza"><strong>Fecha</strong></td>
                                            <td class="cabeza"><strong>Valor</strong></td>
                                            <td class="cabeza"><strong>Recargo</strong></td>
                                            <td class="cabeza"><strong>Saldo</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="oculto">Identificador</th>
                                            <th width="7%"></th>
                                            <th>Tercero</th>
                                            <th>Tipo Factura</th>
                                            <th>Número</th>
                                            <th>Espacio Habitable</th>
                                            <th>Fecha</th>
                                            <th>Valor</th>
                                            <th>Recargo</th>
                                            <th>Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php                                             
                                            for ($i = 0; $i < count($rowd); $i++) {    
                                                #** Buscar Pagos **#
                                                $pgs = $con->Listar("SELECT SUM(valor) FROM gp_detalle_pago
                                                    WHERE detalle_factura IN (".$rowd[$i][7].")");
                                                $valr = $pgs[0][0];
                                                $saldo = $rowd[$i][8]-$valr;
                                                if($saldo>0){
                                                    echo '<tr><td style="display: none;"></td>';
                                                    echo '<td class="campos text-center">';
                                                    echo '<input name="seleccion'.$i.'" id="seleccion'.$i.'" type="checkbox" onchange="cambiarV('.$i.')">';
                                                    echo '<input name="factura'.$i.'" id="factura'.$i.'" type="hidden" value="'.$rowd[$i][0].'">';
                                                    echo '<input name="saldo'.$i.'" id="saldo'.$i.'" type="hidden" value="'.$saldo.'">';
                                                    echo '</td>';
                                                    echo '<td class="campos text-left">'.ucwords(mb_strtolower($rowd[$i][3])).' - '.$rowd[$i][4].'</td>';  
                                                    echo '<td class="campos text-left">'.mb_strtoupper($rowd[$i][5]).' - '.ucwords(mb_strtolower($rowd[$i][6])).'</td>';                  
                                                    echo '<td class="campos text-left">'.$rowd[$i][1].'</td>';                   
                                                    echo '<td class="campos text-left">'.$rowd[$i][14].'</td>';                   
                                                    echo '<td class="campos text-left">'.$rowd[$i][2].'</td>';  
                                                    echo '<td class="campos text-right">'.number_format($rowd[$i][8],2,'.',',').'</td>';
                                                    echo '<td class="campos text-right">';
                                                    $recargo =0;
                                                    if($rowd[$i][10]==2){
                                                        #** Busca  Tarifa Concepto Recargo del espacio habitable **#
                                                        $dias = $rowd[$i][11];
                                                        $esph = $rowd[$i][12];
                                                        if($rowd[$i][15]==0){
                                                            $vr = $con->Listar("SELECT eht.valor 
                                                            FROM gph_espacio_habitable_concepto ehc 
                                                            LEFT JOIN gph_espacio_habitable_tarifa eht ON ehc.id_unico = eht.id_espacio_habitable_concepto 
                                                            LEFT JOIN gp_concepto c ON ehc.id_concepto = c.id_unico 
                                                            WHERE ehc.id_espacio_habitable =$esph AND c.tipo_concepto = 2  AND c.nombre ='Recargo mes anterior' 
                                                            AND eht.ano = $anno");
                                                            IF(count($vr)>0){
                                                                $recargo = $vr[0][0]*$dias;
                                                                if($recargo>20000){
                                                                    $recargo =20000;
                                                                }
                                                            }
                                                        } else {
                                                            $recargo =0;
                                                        }
                                                    }
                                                    $saldo = $saldo+$recargo;
                                                    echo '<input name="valor_recar'.$i.'" id="valor_recar'.$i.'" value = "'.number_format($recargo,0,'.',',').'" '
                                                            . 'class="col-sm-2 form-control" title="Seleccione Valor" required '
                                                            . 'title="Ingrese el valor" style=" width: 95%"  '
                                                            . 'onkeyup="valorr('.$i.')" onchange="valor_c('.$i.')" />';
                                                    echo '<input type="hidden" name="recargo'.$i.'" id="recargo'.$i.'" value="'.$recargo.'">';
                                                    echo '<input type="hidden" name="valor_rgc'.$i.'" id="valor_rgc'.$i.'" value="20000">';
                                                    echo '</td>';
                                                    
                                                    echo '<td class="campos text-right">';
                                                    echo '<input name="valor_r'.$i.'" id="valor_r'.$i.'" value = "'.number_format($saldo,0,'.',',').'" '
                                                            . 'class="col-sm-2 form-control" title="Seleccione Valor" required '
                                                            . 'title="Ingrese el valor" style=" width: 95%"  '
                                                            . 'onkeyup="valor('.$i.')" onchange="valor_c('.$i.')"/>';
                                                    echo '<input type="hidden" name="valor_c'.$i.'" id="valor_c'.$i.'" value="'.$saldo.'">';
                                                    echo '<input type="hidden" name="valor_rr'.$i.'" id="valor_rr'.$i.'" value="'.$saldo.'">';
                                                    echo '</td>';
                                                    echo '</tr>';
                                                }
                                            }                                       
                                        
                                        ?>
                                    </tbody>
                                </table>                                
                            </div>
                        </div> 
                    </div>
                    <?php } ?>
                    <div class="col-md-2 col-lg-2">
                    <label id="valorseleccionado" name="valorseleccionado"></label>
                    </div>
                    </form>
                    <script>
                        function cambiarV(i){
                            var fac     = 'factura'+i;
                            var id      = $("#"+fac).val();
                            var ncheck = 'seleccion'+i;
                            var facturas = $("#facturas").val();
                            var valor = parseFloat($("#valor_seleccionado").val());
                            var val = 'saldo'+i;
                            if($("#"+ncheck).prop('checked')){
                                facturas +=','+id;
                                valor +=parseFloat($("#"+val).val());
                            } else {
                                facturas = facturas.replace(','+id, "");
                                valor -=parseFloat($("#"+val).val());
                            }
                            $("#facturas").val(facturas);
                            $("#valor_seleccionado").val(valor);
                            vh = formatV(valor) ;
                            $("#valorseleccionado").html('Valor Seleccionado: '+vh);
                        }
                        function valor(i){

                            var n = 'valor_r'+i;
                            var numero = $("#"+n).val();
                            numero = numero.replace(/\,/g,'');
                            numero = numero.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
                            numero = numero.split('').reverse().join('').replace(/^[\,]/,'');
                            $("#"+n).val(numero);
                            var c = 'valor_c'+i;
                            var valor_c = $("#"+c).val();
                            var valor = $("#"+n).val();

                            valor = parseFloat(valor.replace(/\,/g, ''));
                            if (valor_c < valor){
                                $("#"+n).val(0);
                            } 


                        }
                        function valor_c(i){
                            var n = 'valor_r'+i;
                            var valor = $("#"+n).val();
                            valor = parseFloat(valor.replace(/\,/g, ''));
                            var a ='valor_rr'+i;
                            var valor_a = $("#"+a).val();
                            var valor_s = parseFloat($("#valor_seleccionado").val());
                            valor_s -=valor_a;
                            valor_s +=valor;
                            var valor_a = $("#"+a).val(valor);
                            $("#valor_seleccionado").val(valor_s);
                            vh = formatV(valor_s) ;
                            $("#valorseleccionado").html('Valor Seleccionado: '+vh);
                        }
                        function valorr(i){

                            var n = 'valor_recar'+i;
                            var numero = $("#"+n).val();
                            numero = numero.replace(/\,/g,'');
                            numero = numero.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
                            numero = numero.split('').reverse().join('').replace(/^[\,]/,'');
                            $("#"+n).val(numero);
                            var c = 'valor_rgc'+i;
                            var valor_c = $("#"+c).val();
                            var valor = $("#"+n).val();
                            var nr = 'recargo'+i;
                            valor = parseFloat(valor.replace(/\,/g, ''));
                            $("#"+nr).val(valor);
                            if (valor_c < valor){
                                $("#"+n).val(0);
                                $("#"+nr).val(0);
                            } 

                        }
                        function valor_cr(i){
                            var n = 'valor_recar'+i;
                            var valor = $("#"+n).val();
                            valor = parseFloat(valor.replace(/\,/g, ''));
                            var a ='recargo'+i;
                            var valor_a = $("#"+a).val();
                            var valor_s = parseFloat($("#valor_seleccionado").val());
                            valor_s -=valor_a;
                            valor_s +=valor;
                            var valor_a = $("#"+a).val(valor);
                            $("#valor_seleccionado").val(valor_s);
                            vh = formatV(valor_s) ;
                            $("#valorseleccionado").html('Valor Seleccionado: '+vh);
                        }
                    </script>
                    <script>
                    function guardard(){
                        var facturas_s = $("#facturas").val();
                        var formData = new FormData($("#form")[0]);  
                            jsShowWindowLoad('Guardando Datos...');
                            var form_data = { action:1 };
                            $.ajax({
                            type: 'POST',
                            url: "jsonPh/gph_facturacionJson.php?action=2&f="+facturas_s,
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            { 
                                jsRemoveWindowLoad();
                                console.log(response);
                                var rta = response
                                if(rta !=0){

                                    $("#mensaje").html(rta+' Recaudos Registrados Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        window.location='GPH_RECAUDO_LOTE.php';
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalMensajes").modal("hide");
                                    })
                                }

                            }
                        });
                    }
                </script>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMensajes2" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje2" name="mensaje2"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModal1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                        <button type="button" id="btnModal2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function cerrar(){
                $("#mdlModificarReteciones1").modal('hide');
                $(".modal-backdrop fade in").css('display','none');
                $(".modal-backdrop").css('display','none');
            }
        </script>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
            $("#tercero").select2();
            $("#banco").select2();
            $("#tipoRecaudo").select2();
            
            
        </script>
    </body>
</html>
