<?php
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno']; 

$rowr = $con->Listar("SELECT u.rol FROM gs_usuario u WHERE u.tercero =".$_SESSION['usuario_tercero']);
$rol = $rowr[0][0];
?>
<html>
    <head>
        <title>Usuarios</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <script src="js/md5.pack.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <style>
        label #tercero-error, #sector-error, #codigo-error, #direccion-error, 
        #ciudad-error, #uso-error, #estrato-error, #estado-error, 
        #numero_m-error, #fechaI-error, #estado_m-error, #acueducto-error, 
        #alcantarillado-error,#aseo-error,#codigoI-error,#codigoR-error{ 
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
            },
          });

          $(".cancel").click(function() {
            validator.resetForm();
          });
        });
        </script>

        <style>
         .form-control {font-size: 10px;}
         .borde-sombra{
            box-shadow: 0px 2px 5px 1px grey;
        }
        .cabeza{
            text-align: center;background: #e9e9e9;
        }
        .boton-especial{
            color: #fff;
            background-color: #337ab7;
            border-color: #2e6da4;
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .letra-boton{
            color: #fff;
        }
        
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
                $("#txtFecham").datepicker({changeMonth: true,}).val();                                
                $("#fechaI").datepicker({changeMonth: true,}).val();


        });
        </script>
    </head>
    <body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Usuarios</h2>
                <?php if(empty($_GET['id'])){ ?>
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()" >  
                    <div class="col-sm-6 col-md-6 col-lg-6 borde-sombra" style="margin-top:5px">
                        <div class="col-sm-12 col-md-12 col-lg-12">    
                            <div class="table-responsive">
                                <table id="tbl" class="table table-bordered clearfix" width="100%">
                                    <tr>
                                        <thead>
                                            <th class="cabeza" colspan="4">DATOS GENERALES SUSCRIPTOR</th>
                                        </thead>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12"  style="margin-top:-5px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="sector" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="sector" id="sector" class=" form-control select2" title="Seleccione Sector" style="height: auto;" required>
                                        <option value="">Sector</option>
                                        <?php $rows = $con->Listar("SELECT * FROM gp_sector ORDER BY codigo ASC");
                                        for ($s = 0; $s < count($rows); $s++) {
                                            echo '<option value="'.$rows[$s][0].'">'.$rows[$s][2].' - '.$rows[$s][1].'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-15px">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="codigo" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Sistema</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <input name="codigo" id="codigo" placeholder="Código Sistema" class="col-sm-4 form-control" title="Código" required="required" title="Ingrese Código Sistema" style="width: 106%" readonly="true"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-0px">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="codigoI" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Interno</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <input name="codigoI" id="codigoI" placeholder="Código Interno" class="col-sm-4 form-control" title="Código Interno" required="required" title="Ingrese Código Interno" style="width: 106%" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-0px">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="codigoR" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Ruta</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <input name="codigoR" id="codigoR" placeholder="Código Ruta" class="col-sm-4 form-control" title="Código Ruta" required="required" title="Ingrese Código Ruta" style="width: 106%" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-0px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="tercero" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tercero:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="tercero" id="tercero" class=" form-control select2" title="Seleccione Tercero" style="height: auto;" required>
                                        <option value="">Tercero</option>
                                        <?php $rowt = $con->Listar("SELECT DISTINCT t.id_unico,  
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
                                                FROM gf_tercero t ");
                                        for ($t = 0; $t < count($rowt); $t++) {
                                            echo '<option value="'.$rowt[$t][0].'">'. ucwords(mb_strtolower($rowt[$t][1])).' - '.$rowt[$t][2].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-15px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="direccion" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Dirección:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <input name="direccion" id="direccion" placeholder="Dirección" class="col-sm-4 form-control" title="Ingrese Dirección" required style="width: 100%"  />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-15px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="ciudad" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Ciudad:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="ciudad" id="ciudad" class=" form-control select2" title="Seleccione Ciudad" style="height: auto;" required>
                                        <option value="">Ciudad</option>
                                        <?php 
                                        $rowc = $con->Listar("SELECT DISTINCT c.id_unico, c.nombre, d.nombre 
                                            FROM gf_ciudad c 
                                            LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
                                            ORDER BY c.nombre");
                                        for ($c = 0; $c < count($rowc); $c++) {
                                            echo '<option value="'.$rowc[$c][0].'">'.ucwords(mb_strtolower($rowc[$c][1].' - '.$rowc[$c][2])).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-15px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="barrio" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Barrio:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="barrio" id="barrio" class=" form-control select2" title="Seleccione Barrio" style="height: auto;">
                                        <option value="">Barrio</option>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-1px">
                            &nbsp;<br/>
                            &nbsp;<br/>
                            &nbsp;<br/>
                            <div class="form-group form-inline col-md-3 col-lg-3"></div>
                            <div class="form-group form-inline col-md-3 col-lg-3">
                                <button type="submit" class="btn sombra btn-primary" style="margin-top: 20px" title="Guardar"><i class="glyphicon glyphicon-floppy-disk"></i></button>
                            </div>
                            <div class="form-group form-inline col-md-3 col-lg-3">
                                <button type="button" id="btntercero" class="btn sombra btn-primary" style="margin-top: 20px" title="Guardar Tercero"><i class="glyphicon glyphicon-user"></i></button>
                            </div>
                            <div class="form-group form-inline col-md-3 col-lg-3">
                                <button type="button" id="informe" class="btn sombra btn-primary" style="margin-top: 20px" title="Informe Usuarios"><i class="glyphicon glyphicon-print"></i></button>
                            </div>
                            <div class="form-group form-inline col-md-3 col-lg-3">
                                <button type="button" id="buscar" class="btn sombra btn-primary" style="margin-top: 20px" title="Buscar"><i class="glyphicon glyphicon-search"></i></button>
                            </div>
                            &nbsp;<br/>
                            &nbsp;<br/>    
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-md-6 col-lg-6 borde-sombra" style="margin-top:5px">
                        <div class="col-sm-12 col-md-12 col-lg-12 margin-superior">
                            <div class="table-responsive">
                                <table id="tbl" class="table table-bordered clearfix" width="100%">
                                    <tr>
                                        <thead>
                                            <th class="cabeza">CATEGORÍA DEL SUSCRIPTOR</th>
                                        </thead>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-15px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="uso" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Uso:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="uso" id="uso" class=" form-control select2" title="Seleccione Uso" style="height: auto;" required>
                                        <option value="">Uso</option>
                                        <?php $rowu = $con->Listar("SELECT * FROM gp_uso");
                                        for ($u = 0; $u < count($rowu); $u++) {
                                            echo '<option value="'.$rowu[$u][0].'">'.ucwords(mb_strtolower($rowu[$u][1])).'</option>';    
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="estrato" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Estrato:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="estrato" id="estrato" class=" form-control select2" title="Seleccione Estrato" style="height: auto;" required>
                                        <option value="">Estrato</option>
                                        <?php $rowe = $con->Listar("SELECT * FROM gp_estrato WHERE tipo_estrato = 2");
                                        for ($e = 0; $e < count($rowe); $e++) {
                                            echo '<option value="'.$rowe[$e][0].'">'.$rowe[$e][1].' - '.ucwords(mb_strtolower($rowe[$e][2])).'</option>';    
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="estado" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Estado:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="estado" id="estado" class=" form-control select2" title="Seleccione Estado" style="height: auto;" required>
                                        <option value="">Estado</option>
                                        <?php $rowe = $con->Listar("SELECT * FROM gr_estado_predio");
                                        for ($e = 0; $e < count($rowe); $e++) {
                                            echo '<option value="'.$rowe[$e][0].'">'.$rowe[$e][1].' - '.ucwords(mb_strtolower($rowe[$e][2])).'</option>';    
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        &nbsp;<br/>
                        <div class="col-sm-12 col-md-12 col-lg-12 margin-superior">
                            <div class="table-responsive">
                                <table id="tbl" class="table table-bordered clearfix" width="100%">
                                    <tr>
                                        <thead>
                                            <th class="cabeza">DATOS MEDIDOR</th>
                                        </thead>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-10px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="numero_m" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Número Medidor:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <input name="numero_m" id="numero_m" placeholder="Número Medidor" class="col-sm-4 form-control" title="Seleccione Número Medidor" required style="width: 100%"  />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="fechaI" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fecha Instalación:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <input name="fechaI" id="fechaI" placeholder="Fecha Instalación" class="col-sm-4 form-control" title="Seleccione Fecha Instalación" required style="width: 100%"  />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="estado_m" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Estado Medidor:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="estado_m" id="estado_m" class=" form-control select2" title="Seleccione Estado Medidor" style="height: auto;" required>
                                        <option value="">Estado Medidor</option>
                                        <?php $rowem = $con->Listar("SELECT * FROM gp_estado_medidor ");
                                        for ($m = 0; $m < count($rowem); $m++) {
                                            echo '<option value="'.$rowem[$m][0].'">'.ucwords(mb_strtolower($rowem[$m][1])).'</option>';    
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">    
                            <div class="table-responsive">
                                <table id="tbl" class="table table-bordered clearfix" width="100%">
                                    <tr>
                                        <thead>
                                            <th class="cabeza" colspan="4">SERVICIOS A COBRAR</th>
                                        </thead>
                                    </tr>
                                </table>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-10px">
                         
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <label for="acueducto" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tiene Acueducto:</label>
                                </div>
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <select name="acueducto" id="acueducto" class=" form-control select2" title="Seleccione Tiene Acueducto" style="height: auto;" required>
                                        <option value="1">Si</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                                <?php if($rol==1 || $rol==2){ ?>
                                    <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: 10px">
                                        <select name="interesAc" id="interesAc" class=" form-control select2" title="Aplica Interés Acueducto" style="height: auto;" required>
                                            <option value="1">Si</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <label for="alcantarillado" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tiene Alcantarillado:</label>
                                </div>
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <select name="alcantarillado" id="alcantarillado" class=" form-control select2" title="Seleccione Tiene Alcantarillado" style="height: auto;" required>
                                        <option value="1">Si</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                                <?php if($rol==1 || $rol==2){ ?>
                                    <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: 10px">
                                        <select name="interesAl" id="interesAl" class=" form-control select2" title="Aplica Interés Alcantarillado" style="height: auto;" required>
                                            <option value="1">Si</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <label for="aseo" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tiene Aseo:</label>
                                </div>
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <select name="aseo" id="aseo" class=" form-control select2" title="Seleccione Tiene Aseo" style="height: auto;" required>
                                        <option value="1">Si</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                                <?php if($rol==1 || $rol==2){ ?>
                                    <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: 10px">
                                        <select name="interesAs" id="interesAs" class=" form-control select2" title="Aplica Interés Aseo" style="height: auto;" required>
                                            <option value="1">Si</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    
                </form>
                <?php } else { 
                    $rowuv = $con->Listar("SELECT DISTINCT uvms.id_unico, 
                        uvs.id_unico, 
                        uv.id_unico, 
                        s.id_unico, s.nombre, s.codigo, 
                        p.codigo_catastral, 
                        t.id_unico, IF(CONCAT_WS(' ',
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
                        c.id_unico, c.nombre, d.nombre, p.direccion, 
                        b.id_unico, b.nombre, 
                        u.id_unico, u.nombre, 
                        e.id_unico, e.codigo, e.nombre, 
                        ep.id_unico, ep.codigo, ep.nombre, 
                        m.id_unico, m.referencia, DATE_FORMAT(m.fecha_instalacion,'%d/%m/%Y'), 
                        em.id_unico, em.nombre, p.id_unico, 
                        uv.codigo_interno, uv.codigo_ruta 
                        FROM gp_unidad_vivienda_medidor_servicio uvms 
                        LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
                        LEFT JOIN gp_estado_medidor em ON m.estado_medidor = em.id_unico 
                        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico  
                        LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                        LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
                        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
                        LEFT JOIN gf_ciudad c oN p.ciudad = c.id_unico 
                        LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
                        LEFT JOIN gp_uso u ON uv.uso = u.id_unico 
                        LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
                        LEFT JOIN gr_estado_predio ep ON p.estado = ep.id_unico 
                        LEFT JOIN gp_barrio b ON p.barrio = b.id_unico 
                        WHERE uvms.id_unico =".$_GET['id']);
                    ?>
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:modificar()" >  
                    <input type="hidden" id="id_uvms" name="id_uvms" value="<?php echo $rowuv[0][0];?>">
                    <input type="hidden" id="id_uvs" name="id_uvs" value="<?php echo $rowuv[0][1];?>">
                    <input type="hidden" id="id_uv" name="id_uv" value="<?php echo $rowuv[0][2];?>">
                    <input type="hidden" id="id_predio" name="id_predio" value="<?php echo $rowuv[0][29];?>">
                    <div class="col-sm-6 col-md-6 col-lg-6 borde-sombra" style="margin-top:5px">
                        <div class="col-sm-12 col-md-12 col-lg-12">    
                            <div class="table-responsive">
                                <table id="tbl" class="table table-bordered clearfix" width="100%">
                                    <tr>
                                        <thead>
                                            <th class="cabeza" colspan="4">DATOS GENERALES SUSCRIPTOR</th>
                                        </thead>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12"  style="margin-top:-5px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="sector" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="sector" id="sector" class=" form-control select2" title="Seleccione Sector" style="height: auto;" required>
                                        <?php 
                                        if(empty($rowuv[0][3])){
                                            echo '<option value=""> - </option>';
                                            $ids = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][3].'">'.$rowuv[0][5].' - '.$rowuv[0][4].'</option>';
                                            $ids = $rowuv[0][3];
                                        }
                                        $rows = $con->Listar("SELECT * FROM gp_sector WHERE id_unico != $ids ORDER BY codigo ASC");
                                        for ($s = 0; $s < count($rows); $s++) {
                                            echo '<option value="'.$rows[$s][0].'">'.$rows[$s][2].' - '.$rows[$s][1].'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-15px">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="codigo" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Sistema</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <input name="codigo" id="codigo" placeholder="Código Sistema" class="col-sm-4 form-control" title="Código" required="required" title="Ingrese Código Sistema" style="width: 106%" readonly="true" value="<?php echo $rowuv[0][6];?>"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-0px">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="codigoI" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Interno</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <input name="codigoI" id="codigoI" placeholder="Código Interno" class="col-sm-4 form-control" title="Código Interno" required="required" title="Ingrese Código Interno" style="width: 106%" value="<?php echo $rowuv[0][30];?>"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-0px">
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <label for="codigoR" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Ruta</label>
                            </div>
                            <div class="form-group form-inline  col-md-6 col-lg-6">
                                <input name="codigoR" id="codigoR" placeholder="Código Ruta" class="col-sm-4 form-control" title="Código Ruta" required="required" title="Ingrese Código Ruta" style="width: 106%" value="<?php echo $rowuv[0][31];?>" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-0px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="tercero" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tercero:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="tercero" id="tercero" class=" form-control select2" title="Seleccione Tercero" style="height: auto;" required>
                                        <?php 
                                        if(empty($rowuv[0][7])){
                                            echo '<option value=""> - </option>';
                                            $idt = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][7].'">'.ucwords(mb_strtolower($rowuv[0][8])).' - '.$rowuv[0][9].'</option>';
                                            $idt = $rowuv[0][7];
                                        }
                                        $rowt = $con->Listar("SELECT DISTINCT t.id_unico,  
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
                                                FROM gf_tercero t WHERE t.id_unico != $idt ");
                                        for ($t = 0; $t < count($rowt); $t++) {
                                            echo '<option value="'.$rowt[$t][0].'">'. ucwords(mb_strtolower($rowt[$t][1])).' - '.$rowt[$t][2].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-15px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="direccion" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Dirección:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <input name="direccion" id="direccion" value="<?php echo $rowuv[0][13];?>" placeholder="Dirección" class="col-sm-4 form-control" title="Ingrese Dirección" required style="width: 100%"  />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-15px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="ciudad" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Ciudad:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="ciudad" id="ciudad" class=" form-control select2" title="Seleccione Ciudad" style="height: auto;" required>
                                        <?php 
                                        if(empty($rowuv[0][10])){
                                            echo '<option value=""> - </option>';
                                            $idc = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][10].'">'.ucwords(mb_strtolower($rowuv[0][11].' - '.$rowuv[0][12])).'</option>';
                                            $idc = $rowuv[0][10];
                                        }
                                        $rowc = $con->Listar("SELECT DISTINCT c.id_unico, c.nombre, d.nombre 
                                            FROM gf_ciudad c 
                                            LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
                                            WHERE c.id_unico != $idc 
                                            ORDER BY c.nombre");
                                        for ($c = 0; $c < count($rowc); $c++) {
                                            echo '<option value="'.$rowc[$c][0].'">'.ucwords(mb_strtolower($rowc[$c][1].' - '.$rowc[$c][2])).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-15px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="barrio" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Barrio:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="barrio" id="barrio" class=" form-control select2" title="Seleccione Barrio" style="height: auto;">
                                        <?php 
                                        if(empty($rowuv[0][14])){
                                            echo '<option value=""> - </option>';
                                            $idb = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][14].'">'.ucwords(mb_strtolower($rowuv[0][15])).'</option>';
                                            echo '<option value=""> - </option>';
                                            $idb = $rowuv[0][10];
                                        }
                                        if($idc != 0){
                                            $rowb = $con->Listar("SELECT * FROM gp_barrio WHERE ciudad = $idc AND id_unico != $idb");
                                            for ($b = 0; $b < count($rowb); $b++) {
                                                echo '<option value="'.$rowb[$b][0].'">'.ucwords(mb_strtolower($rowb[$b][1])).'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12 " style="margin-top:-4px">
                            &nbsp;<br/>
                            &nbsp;<br/>
                            &nbsp;<br/>
                            <div class="form-group form-inline col-md-2 col-lg-2" style="margin-left:-60px">
                                <button type="submit" class="btn sombra btn-primary" style="margin-top: 20px" title="Modificar"><i class="glyphicon glyphicon-pencil"></i></button>
                            </div>
                            <div class="form-group form-inline col-md-2 col-lg-2" style="margin-left:8px">
                                <button type="button" id="facturas" class="btn sombra btn-primary" style="margin-top: 20px" title="Ver Facturas"><i class="glyphicon glyphicon-eye-open"></i></button>
                            </div>
                            <div class="form-group form-inline col-md-2 col-lg-2" style="margin-left:8px">
                                <button type="button" id="otrosC" class="btn sombra btn-primary" style="margin-top: 20px" title="Otros Conceptos"><i class="glyphicon glyphicon-asterisk"></i></button>
                            </div>
                            <div class="form-group form-inline col-md-2 col-lg-2" style="margin-left:8px">
                                <button type="button" id="btnCambiarM" class="btn sombra btn-primary" style="margin-top: 20px" title="Cambiar Medidor"><i class="glyphicon glyphicon-refresh"></i></button>
                            </div>
                            <div class="form-group form-inline col-md-2 col-lg-2" style="margin-left:8px">
                                <button type="button" id="btntercero" class="btn sombra btn-primary" style="margin-top: 20px" title="Guardar Tercero"><i class="glyphicon glyphicon-user"></i></button>
                            </div>
                            <div class="form-group form-inline col-md-2 col-lg-2 " style="margin-left:8px">
                                <button type="button" id="buscar" class="btn sombra btn-primary" style="margin-top: 20px" title="Buscar"><i class="glyphicon glyphicon-search"></i></button>
                            </div>
                            <div class="form-group form-inline col-md-2 col-lg-2" style="margin-left:8px">
                                <button type="button" id="nuevo" class="btn sombra btn-primary" style="margin-top: 20px" title="Nuevo"><i class="glyphicon glyphicon-plus"></i></button>
                            </div>

                            &nbsp;<br/>
                            &nbsp;<br/>    
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-6 col-lg-6 borde-sombra" style="margin-top:5px">
                        <div class="col-sm-12 col-md-12 col-lg-12 margin-superior">
                            <div class="table-responsive">
                                <table id="tbl" class="table table-bordered clearfix" width="100%">
                                    <tr>
                                        <thead>
                                            <th class="cabeza">CATEGORÍA DEL SUSCRIPTOR</th>
                                        </thead>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-15px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="uso" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Uso:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="uso" id="uso" class=" form-control select2" title="Seleccione Uso" style="height: auto;" required>
                                        <?php 
                                        if(empty($rowuv[0][16])){
                                            echo '<option value=""> - </option>';
                                            $idu = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][16].'">'.ucwords(mb_strtolower($rowuv[0][17])).'</option>';
                                            $idu = $rowuv[0][16];
                                        }
                                        $rowu = $con->Listar("SELECT * FROM gp_uso WHERE id_unico != $idu");
                                        for ($u = 0; $u < count($rowu); $u++) {
                                            echo '<option value="'.$rowu[$u][0].'">'.ucwords(mb_strtolower($rowu[$u][1])).'</option>';    
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="estrato" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Estrato:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="estrato" id="estrato" class=" form-control select2" title="Seleccione Estrato" style="height: auto;" required>
                                        <?php 
                                        if(empty($rowuv[0][18])){
                                            echo '<option value=""> - </option>';
                                            $ide = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][18].'">'.$rowuv[0][19].' - '.ucwords(mb_strtolower($rowuv[0][20])).'</option>';
                                            $ide = $rowuv[0][18];
                                        }
                                        $rowe = $con->Listar("SELECT * FROM gp_estrato 
                                            WHERE tipo_estrato = 2 AND id_unico != $ide");
                                        for ($e = 0; $e < count($rowe); $e++) {
                                            echo '<option value="'.$rowe[$e][0].'">'.$rowe[$e][1].' - '.ucwords(mb_strtolower($rowe[$e][2])).'</option>';    
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="estado" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Estado:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="estado" id="estado" class=" form-control select2" title="Seleccione Estado" style="height: auto;" required>
                                        <?php 
                                        if(empty($rowuv[0][21])){
                                            echo '<option value=""> - </option>';
                                            $ides = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][21].'">'.$rowuv[0][22].' - '.ucwords(mb_strtolower($rowuv[0][23])).'</option>';
                                            $ides = $rowuv[0][21];
                                        }
                                        $rowe = $con->Listar("SELECT * FROM gr_estado_predio WHERE  id_unico != $ides");
                                        for ($e = 0; $e < count($rowe); $e++) {
                                            echo '<option value="'.$rowe[$e][0].'">'.$rowe[$e][1].' - '.ucwords(mb_strtolower($rowe[$e][2])).'</option>';    
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        &nbsp;<br/>
                        <div class="col-sm-12 col-md-12 col-lg-12 margin-superior">
                            <div class="table-responsive">
                                <table id="tbl" class="table table-bordered clearfix" width="100%">
                                    <tr>
                                        <thead>
                                            <th class="cabeza">DATOS MEDIDOR</th>
                                        </thead>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-10px">
                            <input type="hidden" name="id_medidor" id="id_medidor" value="<?php echo $rowuv[0][24];?>">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="numero_m" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Número Medidor:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <input name="numero_m" id="numero_m" value="<?php echo $rowuv[0][25];?>" placeholder="Número Medidor" class="col-sm-4 form-control" title="Seleccione Número Medidor" required style="width: 100%"  />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="fechaI" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Fecha Instalación:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <input name="fechaI" id="fechaI" value="<?php echo $rowuv[0][26];?>" placeholder="Fecha Instalación" class="col-sm-4 form-control" title="Seleccione Fecha Instalación" required style="width: 100%"  />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <label for="estado_m" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Estado Medidor:</label>
                                </div>
                                <div class="form-group form-inline  col-md-6 col-lg-6">
                                    <select name="estado_m" id="estado_m" class=" form-control select2" title="Seleccione Estado Medidor" style="height: auto;" required>
                                        <?php 
                                        if(empty($rowuv[0][27])){
                                            echo '<option value=""> - </option>';
                                            $idem = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][27].'">'.ucwords(mb_strtolower($rowuv[0][28])).'</option>';
                                            $idem = $rowuv[0][27];
                                        }
                                        $rowem = $con->Listar("SELECT * FROM gp_estado_medidor WHERE id_unico != $idem");
                                        for ($m = 0; $m < count($rowem); $m++) {
                                            echo '<option value="'.$rowem[$m][0].'">'.ucwords(mb_strtolower($rowem[$m][1])).'</option>';    
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">    
                            <div class="table-responsive">
                                <table id="tbl" class="table table-bordered clearfix" width="100%">
                                    <tr>
                                        <thead>
                                            <th class="cabeza" colspan="4">SERVICIOS A COBRAR</th>
                                        </thead>
                                    </tr>
                                </table>
                            </div>
                        </div> 
                        <?php #** Buscar Servicios Unidad vivienda **#
                        $rowsr1 = $con->Listar("SELECT id_unico, aplica_interes FROM gp_unidad_vivienda_servicio 
                            WHERE tipo_servicio= 1 AND estado_servicio = 1 AND unidad_vivienda = ".$rowuv[0][2]);
                        $rowsr2 = $con->Listar("SELECT id_unico, aplica_interes FROM gp_unidad_vivienda_servicio 
                            WHERE tipo_servicio= 2 AND estado_servicio = 1  AND unidad_vivienda = ".$rowuv[0][2]);
                        $rowsr3 = $con->Listar("SELECT id_unico, aplica_interes FROM gp_unidad_vivienda_servicio 
                            WHERE tipo_servicio= 3 AND estado_servicio = 1 AND unidad_vivienda = ".$rowuv[0][2]);
                        
                        ?>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-10px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <label for="acueducto" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tiene Acueducto:</label>
                                </div>
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <select name="acueducto" id="acueducto" class=" form-control select2" title="Seleccione Tiene Acueducto" style="height: auto;" required>
                                        <?php if(count($rowsr1)>0) {
                                            echo '<option value="1">Si</option>';
                                            echo '<option value="2">No</option>';
                                        } else {
                                            echo '<option value="2">No</option>';
                                            echo '<option value="1">Si</option>';
                                        }?>
                                    </select>
                                </div>
                                <?php if(count($rowsr1)>0 AND ($rol==1 || $rol==2)){ ?>
                                    <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: 10px">
                                        <select name="interesAc" id="interesAc" class=" form-control select2" title="Aplica Interés Acueducto" style="height: auto;" required>
                                            <?php if($rowsr1[0][1]==1) {
                                                echo '<option value="1">Si</option>';
                                                echo '<option value="2">No</option>';
                                            } else {
                                                echo '<option value="2">No</option>';
                                                echo '<option value="1">Si</option>';
                                            }?>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <label for="alcantarillado" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tiene Alcantarillado:</label>
                                </div>
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <select name="alcantarillado" id="alcantarillado" class=" form-control select2" title="Seleccione Tiene Alcantarillado" style="height: auto;" required>
                                        <?php if(count($rowsr2)>0) {
                                            echo '<option value="1">Si</option>';
                                            echo '<option value="2">No</option>';
                                        } else {
                                            echo '<option value="2">No</option>';
                                            echo '<option value="1">Si</option>';
                                        }?>
                                    </select>
                                </div>
                                <?php if(count($rowsr2)>0 AND ($rol==1 || $rol==2)){ ?>
                                    <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: 10px">
                                        <select name="interesAl" id="interesAl" class=" form-control select2" title="Aplica Interés Alcantarillado" style="height: auto;" required>
                                            <?php if($rowsr2[0][1]==1) {
                                                echo '<option value="1">Si</option>';
                                                echo '<option value="2">No</option>';
                                            } else {
                                                echo '<option value="2">No</option>';
                                                echo '<option value="1">Si</option>';
                                            }?>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:-25px">
                            <div class="form-group">
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <label for="aseo" class="col-sm-10 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tiene Aseo:</label>
                                </div>
                                <div class="form-group form-inline  col-md-5 col-lg-5">
                                    <select name="aseo" id="aseo" class=" form-control select2" title="Seleccione Tiene Aseo" style="height: auto;" required>
                                        <?php if(count($rowsr3)>0) {
                                            echo '<option value="1">Si</option>';
                                            echo '<option value="2">No</option>';
                                        } else {
                                            echo '<option value="2">No</option>';
                                            echo '<option value="1">Si</option>';
                                        }?>
                                    </select>
                                </div>
                                <?php if(count($rowsr3)>0 AND ($rol==1 || $rol==2)){ ?>
                                    <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left: 10px">
                                        <select name="interesAs" id="interesAs" class=" form-control select2" title="Aplica Interés Aseo" style="height: auto;" required>
                                            <?php if($rowsr3[0][1]==1) {
                                                echo '<option value="1">Si</option>';
                                                echo '<option value="2">No</option>';
                                            } else {
                                                echo '<option value="2">No</option>';
                                                echo '<option value="1">Si</option>';
                                            }?>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    
                </form>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php require './footer.php'; ?>
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <div class="modal fade" id="modalMensaje" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnMsj" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlBuscar" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Buscar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label style="display:inline-block; width:140px"><strong class="obligado">*</strong>Sector:</label>
                    <select id="sectorbuscar" style="display:inline-block; width:250px;"  name="sectorbuscar" id="sectorbuscar" class="form-control select2" title="Sector" required="required">
                        <option value="" >Sector</option>
                        <?php 
                        $rowsb = $con->Listar("SELECT * FROM gp_sector ORDER BY codigo ASC");
                        for ($s = 0; $s < count($rowsb); $s++) {
                            echo '<option value="'.$rowsb[$s][0].'">'.$rowsb[$s][2].' - '.$rowsb[$s][1].'</option>';
                        }?>
                    </select>  
                    <br/>
                    <br/>
                    <label style="display:inline-block; width:140px"><strong class="obligado">*</strong>Usuario:</label>
                    <select style="display:inline-block; width:250px;"  name="usuariobuscar" id="usuariobuscar" class="form-control select2" title="Sector" style="width:250px;">
                        <option value="">Usuario</option>
                    </select> 
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnBuscarT" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Buscar</button>
                    <button type="button" id="btnCBuscarT" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlTercero" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="forma-modal">
                    <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Tercero</h4>
                </div>
                <form action="javaScript:guardarTercero()" method="post" class="form-horizontal" id="formTercero" enctype="multipart/form-data" style="font-size: 10px !important;">
                    <div class="modal-body">
                        <div class="row">
                            <p align="center" style="margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <input type="hidden" name="txtUrl" value="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                            <div class="form-group">
                                <label for="sltTipoIdent" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo Identificación:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <select name="sltTipoIdent" id="sltTipoIdent" class="form-control select" title="Seleccione tipo identificación" required tabindex="7">
                                        <option value="">Tipo Identificación</option>
                                        <?php
                                        $html = "";
                                        $rowti = $con->Listar("SELECT id_unico, LOWER(nombre),UPPER(sigla) FROM gf_tipo_identificacion");
                                        for ($ti = 0;$ti < count($rowti);$ti++) {
                                            echo '<option value="'.$rowti[$ti][0].'">'.$rowti[$ti][2].' - '.ucwords($rowti[$ti][1]).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <label for="txtNumeroI" class="col-sm-3 col-md-3 col-lg-3 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <input type="text" name="txtNumeroI" id="txtNumeroI" onblur="return existente()" class="form-control" maxlength="100" title="Ingrese número identificación" onkeypress="return txtValida(event,'num')" placeholder="Número Identificación" required style="width: 100%; font-size: 10px !important;" tabindex="8" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="txtPrimerNombre" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Primer Nombre:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <input type="text" name="txtPrimerNombre" id="txtPrimerNombre" class="form-control" maxlength="100" title="Ingrese el primer nombre" onkeypress="return txtValida(event,'car')" placeholder="Primer Nombre" required="" style="width: 100%;font-size: 10px !important;" tabindex="3" autocomplete="off">
                                </div>
                                <label for="txtSegundoNombre" class="control-label col-sm-3 col-md-3 col-lg-3 text-right">Segundo Nombre:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <input type="text" name="txtSegundoNombre" id="txtSegundoNombre" class="form-control" maxlength="100" title="Ingrese el segundo nombre" onkeypress="return txtValida(event,'car')" placeholder="Segundo Nombre" style="width: 100%; font-size: 10px !important;" tabindex="4" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="txtPrimerApellido" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Primer Apellido:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <input type="text" name="txtPrimerApellido" id="txtPrimerApellido" class="form-control" maxlength="100" title="Ingrese el primer apellido" onkeypress="return txtValida(event,'car')" placeholder="Primer Apellido" required style="width: 100%;font-size: 10px !important;" tabindex="5" autocomplete="off">
                                </div>
                                <label for="txtSegundoApellido" class="control-label col-sm-3 col-md-3 col-lg-3 text-right">Segundo Apellido:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <input type="text" name="txtSegundoApellido" id="txtSegundoApellido" class="form-control" maxlength="100" title="Ingrese el segundo apellido" onkeypress="return txtValida(event,'car')" placeholder="Segundo Apellido" style="width: 100%; font-size: 10px !important;" tabindex="6" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="forma-modal">
                        <div class="row">
                            <div class="form-group">
                                <label for="no" class="col-sm-11 col-md-11 col-lg-11 control-label"></label>
                                <div class="col-sm-1 col-md-1 col-lg-1 text-right" style="margin-left:-20px">
                                    <button type="submit" class="btn btn-default" id="btnModalGuardarT"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlMedidor" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="forma-modal">
                    <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Cambiar Medidor</h4>
                </div>
                <form action="javaScript:cambiarMedidor()" method="post" class="form-horizontal" id="formMedidor" enctype="multipart/form-data" style="font-size: 10px !important;">
                    <div class="modal-body">
                        <div class="row">
                            <p align="center" style="margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group">
                                <label for="txtNumeroM" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong style="color:#03C1FB;">*</strong>Número Medidor:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <input type="text" name="txtNumeroM" id="txtNumeroM" class="form-control" maxlength="100" title="Ingrese número Medidor" onkeypress="return txtValida(event,'num_car')" placeholder="Número Medidor" required style="width: 100%; font-size: 10px !important;" tabindex="8" autocomplete="off">
                                </div>
                                <label for="sltEstadom" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Estado Medidor:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <select name="sltEstadom" id="sltEstadom" class="form-control select" title="Seleccione Estado Medidor" required tabindex="7">
                                        <option value="">Estado Medidor</option>
                                        <?php
                                        $html = "";
                                        $rowti = $con->Listar("SELECT id_unico, LOWER(nombre) FROM gp_estado_medidor");
                                        for ($ti = 0;$ti < count($rowti);$ti++) {
                                            echo '<option value="'.$rowti[$ti][0].'">'.ucwords($rowti[$ti][1]).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                            </div>
                            <div class="form-group">
                                <label for="txtFecham" class="control-label col-sm-2 col-md-2 col-lg-2 text-right"><span class="obligado">*</span>Fecha Instalación</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <input type="text" name="txtFecham" id="txtFecham" class="form-control" maxlength="100" title="Ingrese Fecha Instalación"  placeholder="Fecha Instalación" required="" style="width: 100%;font-size: 10px !important;" tabindex="3" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="forma-modal">
                        <div class="row">
                            <div class="form-group">
                                <label for="no" class="col-sm-11 col-md-11 col-lg-11 control-label"></label>
                                <div class="col-sm-1 col-md-1 col-lg-1 text-right" style="margin-left:-20px">
                                    <button type="submit" class="btn btn-default" id="btnModalGuardarC"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalusuariosl" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content" style="width: 500px;">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informe Usuarios</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <div class="form-group"  align="center">
                        <select style="font-size:15px;height: 40px;" name="exportarIu" id="exportarIu" class="form-control" title="Exportar A" required>
                            <option >Exportar A:</option>
                            <option value="1">PDF</option>
                            <option value="2">Excel</option>
                        </select>
                    </div>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" class="btn" onclick="exportarInforme()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript"> 
        $("#sector").select2();
        $("#tercero").select2();
        $("#ciudad").select2();
        $("#barrio").select2();
        $("#medidor").select2();
        $("#uso").select2();
        $("#estrato").select2();
        $("#estado_m").select2();
        $("#acueducto").select2();
        $("#alcantarillado").select2();
        $("#aseo").select2();
        $("#estado").select2();
        $("#sectorbuscar").select2();
        $("#usuariobuscar").select2();
    </script>
    <script>
        $("#sector").change(function(){
            var sector = $("#sector").val();
            var form_data = { action:1, sector:sector  };
            $.ajax({
              type: "POST",
              url: "jsonServicios/gp_usuariosJson.php",
              data: form_data,
              success: function(response)
              { 
                  console.log(response);
                  var numero = response.trim();
                  $("#codigo").html(numero);
                  $("#codigo").val(numero);
              }   
            }); 

        })
        $("#ciudad").change(function(){
            var option ='<option value="">Barrio</option>';
            var form_data = { action:2, ciudad: $("#ciudad").val() };
            $.ajax({
                type: "POST",
                url: "jsonServicios/gp_usuariosJson.php",
                data: form_data,
                success: function(response)
                { 
                    option += response;
                    $("#barrio").html(option);
                }   
            }); 
        })
        $("#codigo").change(function(){
            var codigo = $("#codigo").val();
            var form_data = { action:3, codigo:codigo  };
            $.ajax({
              type: "POST",
              url: "jsonServicios/gp_usuariosJson.php",
              data: form_data,
              success: function(response)
              { 
                  if(response==1){
                      $("#mensaje").html('Código ya existe');
                      $("#modalMensaje").modal("show");
                      $("#btnMsj").click(function(){
                            var sector = $("#sector").val();
                            var form_data = { action:1, sector:sector  };
                            $.ajax({
                              type: "POST",
                              url: "jsonServicios/gp_usuariosJson.php",
                              data: form_data,
                              success: function(response)
                              { 
                                  console.log(response);
                                  var numero = response.trim();
                                  $("#codigo").html(numero);
                                  $("#codigo").val(numero);
                              }   
                            }); 
                      })
                  }
              }   
            }); 
        })
    </script>
    <script>
        function guardar(){
            var formData = new FormData($("#form")[0]);  
            jsShowWindowLoad('Guardando Información...');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_usuariosJson.php?action=4",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'G');
                    if(response !=0){
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                            document.location='GP_USUARIOS.php?id='+response;
                        })
                    } else {
                        $("#mensaje").html('No se ha podido guardar información');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                        })
                    }
                }
            })
        }
    </script>
    <script>
        $("#buscar").click(function(){
            $("#mdlBuscar").modal('show');
        }); 
        $("#sectorbuscar").change(function(){
            var sector = $("#sectorbuscar").val();
            var option = '<option="">Usuario</option>';
            var form_data = { action:5, sector:sector  };
            $.ajax({
              type: "POST",
              url: "jsonServicios/gp_usuariosJson.php",
              data: form_data,
              success: function(response)
              {  
                  console.log(response);
                  option += response;
                  $("#usuariobuscar").html(option);
              }   
            }); 

        })
    </script>
    <script>
        $("#btntercero").click(function(){
            $("#mdlTercero").modal("show");
        });
        $("#btnBuscarT").click(function(){
            var usuario = $("#usuariobuscar").val();
            if(usuario =="" || usuario =='Usuario'  ){
                $("#mdlBuscar").modal('hide');
            } else {
                document.location ='GP_USUARIOS.php?id='+usuario;
            }
        });
        $("#btnCBuscarT").click(function(){
            $("#mdlBuscar").modal('hide');
        }); 
        $("#nuevo").click(function(){
            document.location='GP_USUARIOS.php'
        })
    </script>
    <script>
        function modificar(){
            var formData = new FormData($("#form")[0]);  
            jsShowWindowLoad('Modificando Información...');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_usuariosJson.php?action=6",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'G');
                    if(response !=0){
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                            document.location='GP_USUARIOS.php?id='+response;
                        })
                    } else {
                        $("#mensaje").html('No se ha podido modificar información');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                        })
                    }
                }
            })
        }
    </script>
    <script>
        function existente(){
            var tipoD = $("#sltTipoIdent").val();    
            var numI  = $("#txtNumeroI").val();
            var result = '';
            if(tipoD == null || tipoD == '' || tipoD == "Tipo Identificación" || numI == null || numI == ""){
                $("#mdlTercero").modal('hide');
                $("#txtNumeroI").val('');
                $("#mensaje").html('Seleccione tipo de documento');
                $("#modalMensaje").modal('show');
                
            }else{
                $.ajax({
                data: {"numI": numI,perfil:3, action:2},
                type: "POST",
                url: "jsonPptal/gf_tercerosJson.php",
                success: function (data) {
                    var resultado = JSON.parse(data);
                    var rta = resultado["rta"];
                    var id  = resultado["id"];
                    console.log((rta == 1));
                    if (rta == 1) {
                        $("#mdlTercero").modal('hide');
                        $("#txtNumeroI").val('');
                        $("#mensaje").html('Este número de Identificación  ya existe');
                        $("#modalMensaje").modal('show');                        
                    } else {
                    }                   
                }
            });
            }
        }
        function guardarTercero(){
            var formData = new FormData($("#formTercero")[0]);  
            jsShowWindowLoad('Guardando Información...');
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_tercerosJson.php?action=5",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'G');
                    $("#mdlTercero").modal('hide');                    
                    if(response !=0){
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                            document.location.reload();
                        })
                    } else {
                        $("#mensaje").html('No se ha podido guardar información');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                        })
                    }
                }
            })
        }
    </script>
    <script>
        $("#facturas").click(function(){
            document.location ='GP_DATOS_USUARIOS.php?t=1&id='+$("#id_uvms").val();
        })
        $("#otrosC").click(function(){
            document.location ='GP_DATOS_USUARIOS.php?t=2&id='+$("#id_uvms").val();
        })
    </script>
    <script>
        $("#informe").click(function(){
            $("#modalusuariosl").modal("show");
        });
        function exportarInforme(){
            var t = $("#exportarIu").val();
            if(t!=""){
                window.open('informes_servicios/INF_USUARIOS.php?t='+t);
            }            
        }
    </script>
    <script>
        $("#btnCambiarM").click(function(){
           $("#mdlMedidor").modal("show");
        })
        function cambiarMedidor(){
            var id = $("#id_uvms").val(); 
            $("#mdlMedidor").modal("hide");
            jsShowWindowLoad('Cambiando...');
            var formData = new FormData($("#formMedidor")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_usuariosJson.php?action=14&id_u="+id,
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response+'G');
                    if(response !=0){
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                            document.location ='GP_USUARIOS.php?id='+response;
                        })
                    } else {
                        $("#mensaje").html('No se ha podido guardar información');
                        $("#modalMensaje").modal('show');
                        $("#btnMsj").click(function(){
                            $("#modalMensaje").modal('hide');
                        })
                    }
                }
            })
        }
    </script>
</body>
</html>
