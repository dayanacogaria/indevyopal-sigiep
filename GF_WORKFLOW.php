<?php
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];
if(empty($_GET['id'])){ 
    $rowc       = $con->Listar("SELECT 
        c.id_unico, LOWER(c.nombre) , v.dias 
        FROM gf_vencimiento v 
        LEFT JOIN gf_clase_pptal c ON v.clase = c.id_unico 
        WHERE v.compania = $compania ");
} else {
    $rowcb      = $con->Listar("SELECT c.id_unico, LOWER(c.nombre) , v.dias 
        FROM gf_vencimiento v 
        LEFT JOIN gf_clase_pptal c ON v.clase = c.id_unico 
        WHERE MD5(c.id_unico)='".$_GET['id']."' AND v.compania = $compania");
    $rowc       = $con->Listar("SELECT 
        c.id_unico, LOWER(c.nombre) , v.dias 
        FROM gf_vencimiento v 
        LEFT JOIN gf_clase_pptal c ON v.clase = c.id_unico 
        WHERE v.compania = $compania AND MD5(c.id_unico)!='".$_GET['id']."'");
}

?>
<html>
    <head>
        <title>WorkFlow</title>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <script src="js/md5.pack.js"></script>
        <style>

            label #clase-error,#dias-error {
                display: block;
                color: #bd081c;
                font-weight: bold;
                font-style: italic;
            }
            .proximo{
                background-color: rgba(212,134,37,0.94) !important;
                color: #fff !important;
            }

            .vencido{
                background-color: rgba(196,40,32,0.94) !important;
                color: #fff;
            }

            .igual{
                background-color: rgba(44,117,192,0.94) !important;
                color: #fff !important;
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
                rules: {
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });
        </script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px">WorkFlow</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="clase" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Clase:</label>
                                <select name="clase" id="clase" class="form-control col-sm-1 col-md-1 col-lg-1 select2_single" title="Seleccione Clase" style="width: 250px; text-align: left" required="required">
                                    <?php 
                                    if(empty($_GET['id'])){ 
                                        echo '<option value="">Clase</option>';
                                    } else {
                                        echo '<option value="'.$rowcb[0][0].'">'.ucwords($rowcb[0][1]).' - '.$rowcb[0][2].' Días</option>';
                                    }
                                    for ($z = 0; $z < count($rowc); $z++) {
                                            echo '<option value="'.$rowc[$z][0].'">'.ucwords($rowc[$z][1]).' - '.$rowc[$z][2].' Días</option>';
                                        }
                                    ?>
                                </select>
                                <button onclick="javascript:location.href='GF_WORKFLOW.php'" id="btnNuevo" class="btn btn-primary glyphicon glyphicon-plus"  title="Nuevo" style="margin-left: 10px;"></button>
                            </div>
                        </form>
                    </div>
                    <br/>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px" align="center"></td>
                                        <td><strong>Tipo Comprobante</strong></td>
                                        <td><strong>Número</strong></td>
                                        <td><strong>Fecha</strong></td>
                                        <td><strong>Tercero</strong></td>
                                        <td><strong>Descripción</strong></td>
                                        <td><strong>Fecha Vencimiento</strong></td>
                                        <td><strong>Días Vencimiento</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Tipo Comprobante</th>
                                        <th>Número</th>
                                        <th>Fecha</th>
                                        <th>Tercero</th>
                                        <th>Descripción</th>
                                        <th>Fecha Vencimiento</th>
                                        <th>Días Vencimiento</th>
                                    </tr>
                                </thead>
                                <?php 
                                if(!empty($_GET['id'])){
                                    $clase = $_GET['id'];
                                    #* Buscar Días 
                                    $ds = $con->Listar("SELECT dias FROM gf_vencimiento 
                                        WHERE md5(clase)='$clase' AND compania = $compania");
                                    $dias = $ds[0][0];
                                    $pds  = (80*$dias)/100;
                                    $pd   = $dias - $pds;
                                    #Buscar Comprobantes Por Clase
                                    $row = $con->Listar("SELECT UPPER(tc.codigo), LOWER(tc.nombre), 
                                        cp.numero, DATE_FORMAT(cp.fecha, '%d/%m/%Y'), 
                                        cp.descripcion,IF(CONCAT_WS(' ',
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
                                        DATE_FORMAT(DATE_ADD(cp.fecha,INTERVAL ".$dias." DAY),  '%d/%m/%Y') as fechaV, 
                                        DATEDIFF(NOW(),DATE_ADD(cp.fecha,INTERVAL ".$dias." DAY)), 
                                        (SELECT COUNT(dcp.id_unico) FROM gf_detalle_comprobante_pptal dcp WHERE dcp.comprobantepptal = cp.id_unico 
                                        AND dcp.id_unico IN (SELECT dcpa.comprobanteafectado FROM gf_detalle_comprobante_pptal dcpa))
                                        FROM gf_comprobante_pptal cp
                                        LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                                        LEFT JOIN gf_tercero t ON cp.tercero = t.id_unico 
                                        WHERE md5(tc.clasepptal) = '$clase' 
                                            AND tc.tipooperacion = 1 
                                            AND cp.parametrizacionanno = $anno 
                                        ORDER BY cp.fecha, cp.numero");
                                    
                                    for ($i = 0; $i < count($row); $i++) {
                                        if($row[$i][9]>0){
                                        } else {
                                            if($row[$i][8]>0){
                                                echo "<tr class='vencido'>";
                                            } elseif(($row[$i][8]*-1) >= $pd) {
                                                echo "<tr class='igual'>";
                                            } else {
                                                echo "<tr class='proximo'>";
                                            }
                                            echo '<td style="display: none;">'.$row[$i][0].'</td>';
                                            echo '<td>'; 
                                            echo '</td>';
                                            echo '<td>'.$row[$i][0].' - '.ucwords($row[$i][1]).'</td>'; 
                                            echo '<td>'.$row[$i][2].'</td>'; 
                                            echo '<td>'.$row[$i][3].'</td>'; 
                                            echo '<td>'.ucwords(mb_strtolower($row[$i][5])).' - '.$row[$i][6].'</td>'; 
                                            echo '<td>'.$row[$i][4].'</td>'; 
                                            echo '<td>'.$row[$i][7].'</td>'; 
                                            if($row[$i][8]>0){
                                                echo '<td>'.$row[$i][8].'</td>'; 
                                            } else {
                                                echo '<td></td>'; 
                                            }
                                            echo '</tr>';
                                        }
                                    }
                                }
                                ?>
                            </table>       
                        </div>            
                    </div>     
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
        <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensajeEliminar" name="mensajeEliminar"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
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
            $("#clase").change(function(){
                let clase = $("#clase").val();
                if(clase !=''){
                    document.location = 'GF_WORKFLOW.php?id='+md5(clase);
                }
            })
        </script>
    </body>
</html>

