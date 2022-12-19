<?php
require './Conexion/conexion.php';
require_once './head.php';
$param     = $_SESSION['anno'];
$conceptos = $this->conceptos->obtenerListado($param);
?>
    <title>Conceptos Facturación</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/desing.css">
    <style>
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px;}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px;}
        .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;font-family: Arial;}
        .campos{padding: 0px;font-size: 10px;}
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" class="titulo" style="margin: 0px 4px 20px;">Conceptos Facturación</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="access.php?controller=EspacioConcepto&action=Guardar">
                        <p align="center" style="margin-bottom: 15px; margin-top: 15px; margin-left: 30px; font-size: 80%;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <input type="hidden" name="txtEspacio" value="<?php echo $_GET['Espacio'] ?>">
                        <div class="form-group">
                            <label for="sltConceptosFactura" class="control-label col-sm-3 col-md-3 col-lg-3"><span class="obligado">*</span>Conceptos Facturación:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <select name="sltConceptosFactura" id="sltConceptosFactura" class="select form-control" required>
                                    <option value="">Conceptos Facturación</option>
                                    <?php
                                    $html = "";
                                    while($row = mysqli_fetch_row($conceptos)){
                                        $html .= "<option value='$row[0]'>".ucwords(mb_strtolower($row[1]))."</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <button class="btn btn-primary borde-sombra"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 5px;">
                <table class="table table-striped table-condensed display detalle" id="tablaX" cellpadding="0" width="100%">
                    <thead>
                        <tr>
                            <td class="oculto"></td>
                            <td class="cabeza" style="width: 5%;"></td>
                            <td class="cabeza" style="font-weight: 700;">Conceptos</td>
                        </tr>
                        <tr>
                            <th class="oculto"></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(count($data) < 0){
                        $html  = "";
                        $html .= "<tr>";
                        $html .= "</tr>";
                        echo $html;
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select").select2();

        $(document).ready(function () {
            var i = 1;
            $('#tablaX thead th').each(function (){
                if (i != 1){
                    var title = $(this).text();
                    switch (i){
                        case 3:
                            $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                            break;
                    }
                    i = i + 1;
                } else {
                    i = i + 1;
                }
            });

            var table = $('#tablaX').DataTable({
                "autoFill": true,
                "scrollX": true,
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