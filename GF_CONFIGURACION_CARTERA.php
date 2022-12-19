<?php
header("Content-Type: text/html;charset=utf-8");
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('Conexion/conexionsql.php');
require_once('jsonPptal/funcionesPptal.php');
require_once('head_listar.php');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$nanno      = anno($anno);
if(empty($_GET['id'])) {
    $titulo = "Listar ";
    $titulo2= ".";
    $sql = "SELECT Identificador, Nombre_Tipo_Credito, Rss FROM TIPO_CREDITO  ORDER BY Identificador ASC";
    $stmt = sqlsrv_query( $conn, $sql );  
} elseif(($_GET['id'])==3) {
    $titulo = " ";
    $id     = $_GET['id_t'];
    $sql2    = "SELECT Identificador, Nombre_Tipo_Credito, Rss FROM TIPO_CREDITO WHERE Identificador = '$id'";
    $stmt2 = sqlsrv_query( $conn, $sql2 );  
    $row2= sqlsrv_fetch_array( $stmt2, SQLSRV_FETCH_ASSOC) ;
    $titulo2= $row2['Identificador'].' - '.$row2['Nombre_Tipo_Credito'];
}

?>
<title>Configuración Cartera - Financiera</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script src="dist/jquery.validate.js"></script>
<script src="js/md5.pack.js"></script>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<link href="css/select/select2.min.css" rel="stylesheet">    
<style>
    label #nombre-error, #tipo-error, #numero-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
}
body{
    font-size: 12px;
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
    }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
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
 });
</script>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px">
                <h2 id="forma-titulo3" align="center" style=" margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Configuración Cartera - Financiera'?></h2>
                <?php if(empty($_GET['id'])) { ?>
                
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Identificador Tipo Credito</strong></td>
                                    <td><strong>Nombre Tipo Credito</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Identificador Tipo Credito</th>
                                    <th>Nombre Tipo Credito</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) { ?>
                                    <tr>
                                        <td style="display: none;"></td>
                                        <td>
                                            <a href="GF_CONFIGURACION_CARTERA.php?id=3&id_t=<?=$row['Identificador'] ?>"><i title="Configuración" class="glyphicon glyphicon-cog" ></i></a>
                                        </td>
                                        <td><?= $row['Identificador'] ?></td>
                                        <td><?= $row['Nombre_Tipo_Credito'] ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php }  elseif(($_GET['id'])==3){ ?>
                    <a href="GF_CONFIGURACION_CARTERA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tableO" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px"></td>
                                        <td><strong>Concepto Cartera</strong></td>
                                        <td><strong>Concepto Financiero</strong></td>
                                        <td><strong>Concepto Desembolsos</strong></td>
                                        <td><strong>Concepto Descontable</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Concepto Cartera</th>
                                        <th>Concepto Financiero</th>
                                        <th>Concepto Desembolsos</th>
                                        <th>Concepto Descontable</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $sqlc = "SELECT Identificador, Nombre_Clase_Concepto FROM CLASE_CONCEPTO";
                                    $stmt = sqlsrv_query( $conn, $sqlc );  
                                    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
                                        $id_c =$row['Identificador']; 
                                        $sqls   = "SELECT Id_Tipo_Credito,Id_Clase_Concepto,ano,gf_Concepto,gf_concepto_ds, descontable
                                            FROM CONFIGURACION_PAGOS 
                                            WHERE Id_Tipo_Credito ='$id' AND Id_Clase_Concepto = '$id_c' AND ano = '$nanno'";
                                        $stmts  = sqlsrv_query( $conn, $sqls ); 
                                        $rows   = sqlsrv_fetch_array( $stmts, SQLSRV_FETCH_ASSOC); 
                                        if(count($rows) > 0){
                                            $cf = $con->Listar("SELECT nombre FROM gf_concepto WHERE id_unico = ".$rows['gf_Concepto']);
                                            $cd = $con->Listar("SELECT nombre FROM gf_concepto WHERE id_unico = ".$rows['gf_concepto_ds']);
                                            if($rows['descontable']==true){ $ds = 'Sí'; } else { $ds = 'No'; }
                                            echo '<tr>
                                                <td style="display: none;">Identificador</td>
                                                <td><a  onclick="javascript:eliminar('."'".$id."'".','."'".$id_c."'".','."'".$nanno."'".')"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a></td>
                                                <td>'.$row['Identificador'].' - '.$row['Nombre_Clase_Concepto'].'</td>
                                                <td>'.$cf[0][0].'</td>
                                                <td>'.$cd[0][0].'</td>
                                                <td>'.$ds.'</td>
                                            </tr>';
                                        } else {
                                            echo '<tr><form name="form'.$id_c.'" id="form'.$id_c.'" method="POST" action="javascript:guardar('."'".$id_c."'".')">
                                            <td style="display: none;"><input type ="hidden" name="tipo_credito" id="tipo_credito" value ="'.$id.'">'
                                                    . '<input type ="hidden" name="concepto" id="concepto" value ="'.$id_c.'">'
                                                    . '<input type ="hidden" name="anno" id="anno" value ="'.$nanno.'">'.'</td>
                                            <td><button type="submit"><i title="Guardar" class="glyphicon glyphicon-floppy-disk" ></i></button></td>
                                            <td>'.$row['Identificador'].' - '.$row['Nombre_Clase_Concepto'].'</td>';
                                            echo '<td>';
                                            echo '<select name="concepto'.$id_c.'" id="concepto'.$id_c.'" class="select2_single form-control" >';
                                            echo '<option value ="">Concepto Financiero</option>';
                                            $cfn = $con->Listar("SELECT id_unico,  nombre FROM gf_concepto WHERE parametrizacionanno = $anno AND clase_concepto IN(1,3)");
                                            for ($cf = 0; $cf < count($cfn); $cf++) {
                                                echo '<option value ="'.$cfn[$cf][0].'">'.ucwords($cfn[$cf][1]).'</option>';
                                            }
                                            echo '</select>
                                            </td>
                                            <td>
                                                <select name="conceptod'.$id_c.'" id="conceptod'.$id_c.'" class="select2_single form-control" >
                                                <option value ="">Concepto Desembolso</option> ';
                                                $cfd = $con->Listar("SELECT id_unico,  nombre FROM gf_concepto WHERE parametrizacionanno = $anno AND clase_concepto IN(2)");
                                                for ($cd = 0; $cd < count($cfd); $cd++) {
                                                    echo '<option value ="'.$cfd[$cd][0].'">'.ucwords($cfd[$cd][1]).'</option>';
                                                }
                                            echo '</td>
                                            <td>
                                                <input type="radio" name="descontable'.$id_c.'" value="1">Si 
                                                <input type="radio" name="descontable'.$id_c.'" value="2" checked>No 
                                            </td>
                                            </form>
                                            </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                                
                        </div>
                    </div>
                <?php }?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea Eliminar El Registro Seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="aceptarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="cancelarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
   
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script>
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });
        });
        
    </script>
    <script>
        function guardar(id){
            var nam = 'form'+id;
            var formData = new FormData($("#"+nam)[0]);  
            
           $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_carteraJson.php?action=5",
            data:formData,
            contentType: false,
            processData: false,
            success: function(response)
            { 
                console.log(response);
                if(response==0){
                    $("#mensaje").html('Información Guardada Correctamente');  
                    $("#modalMensajes").modal('show'); 
                    $("#Aceptar").click(function(){
                        document.location.reload();
                    })
                    
                } else {
                    $("#mensaje").html('No Se Ha Podido Guardar La Información');  
                    $("#modalMensajes").modal('show'); 
                }
                
            }
           })
        }  
    </script>
    <script>
        function eliminar(tipo, concepto, anno){
            $("#modalEliminar").modal("show");
            $("#aceptarE").click(function(){
                $("#modalEliminar").modal("hide");
                var form_data = {action:4, tipo:tipo, concepto:concepto, anno:anno};  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_carteraJson.php",
                    data: form_data, 
                    success: function(response) {
                        console.log('Eliminar: '+response);
                        if(response==1){
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location.reload();
                            })
                        } else {
                            $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                 $("#modalMensajes").modal("hide");
                            })
                        }
                    }
                });
            })
            $("#cancelarE").click(function(){
                $("#modalEliminar").modal("hide");
            })
        }
    </script>
</body>
</html>

