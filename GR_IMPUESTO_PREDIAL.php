<?php 

require_once('Conexion/conexion.php');
require_once('head_listar.php');
?>
<!--Titulo de la página-->
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #codC-error, #iduvms-error, #valor-error  {
    display: block;
    color: #155180;
    font-weight: normal;
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

<script type="text/javascript">
        $(document).ready(function() {
           var i= 0;
          $('#tablaR thead th').each( function () {
              if(i >= 0){ 
              var title = $(this).text();
              switch (i){
                case 0:
                    $(this).html( '' );
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
                case 10:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 11:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 12:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 13:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;
                case 14:
                    $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                break;

              }
              i = i+1;
            }else{
              i = i+1;
            }
          } );

          // DataTable
         var table = $('#tablaR').DataTable({
            'clickToSelect': true,
               'select': true,
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
        <style>
    .valorLabel{
    font-size: 10px;
    }
    .valorLabel:hover{
        cursor: pointer;
        color:#1155CC;
    }
    /*td de la tabla*/
    .campos{
        padding: 0px;
        font-size: 10px;
        height: 10px;
    }
</style>
<title>Impuesto Predial</title>
</head>
<body>

 
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-8 text-left">
    <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: 4px;">Datos Básicos Impuesto Predial</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="">

          <p align="center" style="margin-bottom: 25px; margin-top: 15px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
          <!--Ingresa la información-->
            <div class="form-group form-inline" style="margin-top: -15px;">
              <input type="hidden" id="codC" name="codC" required title="Código Catastral" />
              <label for="codC" class="col-sm-2 control-label"><strong style="color:#03C1FB;">*</strong>Código Catastral:</label>
              <input type="text" name="codCa" id="codCa" class="form-control"  title="Ingrese Código Catastral"  placeholder="Código Catastral" required style="display: inline; width: 250px" onchange="valorCambio();">
              <a id="btnBuscar" class="btn" title="Buscar Código Catastral" style="display: inline" onclick="buscar();"><li class="glyphicon glyphicon-search"></li></a>
               <div class="form-group form-inline" style="margin-top: -15px;">
              
              <label for="concepto" class="col-sm-3 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
              <select name="concepto" id="concepto" class="select2_single form-control" style="display: inline; width: 250px" >
                  <option value>Concepto</option>                
              </select>
              
                
            </div> 
            </div>
            <script>
                $("#codCa").keyup(function(){
                        $("#codCa").autocomplete({
                         source:"consultasBasicas/autoCompletadoPredial.php",
                            minlength:1,
                            select: function(event, ui) {
                                var codigo = ui.item;
                                var cod = codigo.value;
                                var form_data={
                                    case:1,
                                    codigo:cod,
                                };
                                $.ajax({
                                    type: 'POST',
                                    url: "consultasBasicas/busquedasPredial.php",
                                    data:form_data,
                                    success: function (data) { 
                                        var resultado = JSON.parse(data);
                                        if (resultado == 'null' || resultado== null || resultado =='' || resultado ==""){
                                            document.getElementById('codC').value='';
                                        }else {
                                            
                                            document.getElementById('codC').value=resultado;
                                            var id = document.getElementById('codC').value;
                                        }
                                    }
                                });
                            },
                        });
                });
            </script>
            <script>
                function valorCambio(){
                    
                    var ref = document.getElementById('uvms').value;
                    var form_data={
                        case:11,
                        referencia:ref,
                    };
                    $.ajax({
                        type: 'POST',
                        url: "consultasBasicas/busquedas.php",
                        data:form_data,
                        success: function (data) { 
                            var resultado = JSON.parse(data);
                            if (resultado == 'null' || resultado== null || resultado =='' || resultado ==""){
                                document.getElementById('iduvms').value='';
                                document.getElementById('periodo1').disabled= true;
                                
                            }else {
                                document.getElementById('iduvms').value=resultado;
                                document.getElementById('periodo1').disabled= false;
                                
                                var id = document.getElementById('iduvms').value;
                                var form_data={
                                    case:7,
                                    id:id
                                };
                                $.ajax({
                                    type: 'POST',
                                    url: "consultasBasicas/busquedas.php",
                                    data:form_data,
                                    success: function (data) { 
                                        $("#periodo1").html(data).fadeIn();
                                        $("#periodo1").css('display','none');
                                    }
                                });
                            }
                        }
                    });
                }
            </script>
            
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert" >
          </form>
        </div>      
    </div>
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
    
 <!--#######################MODAL BUSQUEDA############################-->   
<div class="modal fade" id="myModalBuscar" role="dialog" align="center" >
    <div class="modal-dialog" style="height:600px;width:90%">
        <div class="modal-content client-form1">
            <div id="forma-modal" class="modal-header">       
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Buscar Predio</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrar" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
               <div class="modal-body" style="margin-top: 8px">                                
                <div class="row">
                   <div class="col-sm-12" style="margin-top: 10px;">
                        <div class="table-responsive " >
                            <table id="tablaR" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                                <thead>
                                    <tr>    
                                        <td class="cabeza" ></td>
                                        <td class="cabeza"><strong>Código Catastral</strong></td>
                                        <td class="cabeza"><strong>Nombre</strong></td>
                                        <td class="cabeza"><strong>Matrícula Inmobiliaria</strong></td>
                                        <td class="cabeza"><strong>Dirección</strong></td>
                                        <td class="cabeza"><strong>Código SIG</strong></td>
                                        <td class="cabeza"><strong>Ciudad</strong></td>
                                        <td class="cabeza"><strong>Barrio</strong></td>
                                        <td class="cabeza"><strong>Tipo Predio</strong></td>
                                        <td class="cabeza"><strong>Estrato</strong></td>
                                        <td class="cabeza"><strong>Tercero</strong></td>
                                    </tr>
                                    <tr>  
                                        <th class="cabeza"></th>
                                        <th class="cabeza">Código Catastral</th>
                                        <th class="cabeza">Nombre</th>
                                        <th class="cabeza">Matrícula Inmobiliaria</th>
                                        <th class="cabeza">Dirección</th>
                                        <th class="cabeza">Código SIG</th>
                                        <th class="cabeza">Ciudad</th>
                                        <th class="cabeza">Barrio</th>
                                        <th class="cabeza">Tipo Predio</th>
                                        <th class="cabeza">Estrato</th>
                                        <th class="cabeza">Tercero</th>
                                        
                                        
                                        
                                        
                                        
                                    </tr>
                                </thead>                                 
                                <tbody id="cuerpo" class="text-center"> 
                                    <?php $resultado = "SELECT
                                                    p.id_unico,
                                                    p.codigo_catastral,
                                                    p.nombre,
                                                    p.matricula_inmobiliaria,
                                                    p.direccion,
                                                    p.codigo_sig,
                                                    p.ciudad,
                                                    c.nombre,
                                                    d.nombre,
                                                    p.barrio,
                                                    b.nombre, 
                                                    p.tipo_predio, 
                                                    tp.nombre, p.estrato, es.nombre,
                                                    MAX(terp.porcentaje) as porc, 
                                                    (SELECT MAX(tercep.tercero) 
                                                     FROM gp_tercero_predio tercep WHERE tercep.predio = p.id_unico 
                                                     AND tercep.porcentaje =  (MAX(terp.porcentaje)))
                                                  FROM
                                                    gp_predio1 p
                                                  LEFT JOIN
                                                    gf_ciudad c ON p.ciudad = c.id_unico
                                                  LEFT JOIN
                                                    gf_departamento d ON c.departamento = d.id_unico
                                                  LEFT JOIN
                                                    gp_barrio b ON p.barrio = b.id_unico
                                                    LEFT JOIN gp_tipo_predio tp ON p.tipo_predio = tp.id_unico LEFT JOIN gp_estrato es ON p.estrato = es.id_unico 
                                                    LEFT JOIN gp_tercero_predio terp ON terp.predio =p.id_unico 
                                                    WHERE terp.propietario=1";
                                    $resultado = $mysqli->query($resultado);?>
                                    <?php while($row = mysqli_fetch_row($resultado)) { ?>
                                        <tr>
                                            
                                            <td class="campos"><a onclick="referencia(<?php echo $row[0].','."'".$row[1]."'"?> )" class="btn"><i class="glyphicon glyphicon-download-alt"></i></a></td>
                                            <td class="campos"><?php echo mb_strtoupper(($row[1]));?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower(($row[2])));?></td>
                                            <td class="campos"><?php echo (mb_strtoupper(($row[3])));?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower(($row[4])));?></td>
                                            <td class="campos"><?php echo (mb_strtoupper(($row[5])));?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower(($row[7].' - '.$row[8])));?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower(($row[10])));?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower(($row[12])));?></td>
                                            <td class="campos"><?php echo ucwords(mb_strtolower(($row[14])));?></td>
                                            <td class="campos">
                                                <?php if(!empty($row[16])){
                                                    $ter = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                                            (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', "
                                                            . "id_unico, numeroidentificacion FROM gf_tercero ter "
                                                            . "WHERE id_unico ='$row[16]'";
                                                    $ter=$mysqli->query($ter);
                                                    if(mysqli_num_rows($ter)>0){
                                                        $ter= mysqli_fetch_row($ter);
                                                        echo ucwords(mb_strtolower($ter[0].' - '.$ter[2]));
                                                    }
                                                }?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
               </div>
            
            <div id="forma-modal" class="modal-footer"></div>
        </div>
        
        </div>
    </div>
    
<?php require_once 'footer.php';?>

<script>
    function buscar(){
        
        $("#myModalBuscar").modal('show');
    }
    $("#myModalBuscar").on('shown.bs.modal',function(){
                var dataTable = $("#tablaR").DataTable();
                dataTable.columns.adjust().responsive.recalc();
            });
</script>
<script>
    function referencia (id, referencia){
       
        document.getElementById('codCa').value= referencia;
        document.getElementById('codC').value= id;
        
        
        $("#myModalBuscar").modal('hide');
    }
</script>

</body>
</html>

