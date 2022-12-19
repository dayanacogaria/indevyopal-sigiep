<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$compania = $_SESSION['compania'];
if(empty($_GET['id'])) {
    $titulo = "Listar ";
    $titulo2= ".";
    $row = $con->Listar("SELECT t.id_unico, t.nombre,t.tipo, c.id_unico, c.nombre, cn.id_unico,cn.codigo,  cn.descripcion 
    FROM gn_tipo_novedad t 
    LEFT JOIN gn_clase_novedad c ON t.clase_novedad = c.id_unico 
    LEFT JOIN gn_concepto cn ON t.concepto = cn.id_unico");
} elseif(($_GET['id'])==1) {
    $titulo = "Registrar ";
    $titulo2= ".";
} elseif(($_GET['id'])==2) {
    $titulo = "Modificar ";
    $id     = $_GET['id_r'];
    $row    = $con->Listar("SELECT t.id_unico, t.nombre,t.tipo, c.id_unico, c.nombre, cn.id_unico,cn.codigo,  cn.descripcion 
    FROM gn_tipo_novedad t 
    LEFT JOIN gn_clase_novedad c ON t.clase_novedad = c.id_unico 
    LEFT JOIN gn_concepto cn ON t.concepto = cn.id_unico
        WHERE md5(t.id_unico)='$id'");
    $titulo2= $row[0][1].' - '.$row[0][2];
} elseif(($_GET['id'])==3) {
    $id     = $_GET['id_r'];
    $titulo = "Conceptos ";
    $rowt    = $con->Listar("SELECT t.id_unico, t.nombre 
    FROM gn_tipo_novedad t 
        WHERE md5(t.id_unico)='$id'");
    $titulo2= $rowt[0][1];

    $row    = $con->Listar("SELECT ci.id_unico, ci.tipo_incapacidad, ci.dias_incapacidad, ci.porcentaje, 
        ci.dias, cd.codigo, cd.descripcion, ci.valor, cv.codigo, cv.descripcion, 
        ci.ibc, cibc.codigo, cibc.descripcion,  ci.aporte_pension_patrono, capp.codigo, capp.descripcion, 
        ci.aporte_pension_empleado, cape.codigo, cape.descripcion, 
        ci.aporte_salud_patrono, casp.codigo, casp.descripcion, 
        ci.aporte_salud_empleado, casem.codigo, casem.descripcion, 
        ci.caja_compensacion, ccc.codigo, ccc.descripcion, 
        ci.sena, cs.codigo, cs.descripcion, ci.icbf, cicbf.codigo, cicbf.descripcion, 
        ci.esap, cesap.codigo, cesap.descripcion, ci.ministerio_educacion,cmine.codigo, cmine.descripcion,  
        ci.institutos_tecnicos, cit.codigo, cit.descripcion, ci.fondo_solidaridad, cfsp.codigo, cfsp.descripcion, 
        ci.arl, carl.codigo, carl.descripcion 
        FROM gn_concepto_incapacidad ci 
        LEFT JOIN gn_concepto cd    ON cd.id_unico      = ci.dias 
        LEFT JOIN gn_concepto cv    ON cv.id_unico      = ci.valor 
        LEFT JOIN gn_concepto cibc  ON cibc.id_unico    = ci.ibc 
        LEFT JOIN gn_concepto capp  ON capp.id_unico    = ci.aporte_pension_patrono 
        LEFT JOIN gn_concepto cape  ON cape.id_unico    = ci.aporte_pension_empleado 
        LEFT JOIN gn_concepto casp  ON casp.id_unico    = ci.aporte_salud_patrono 
        LEFT JOIN gn_concepto casem ON casem.id_unico   = ci.aporte_salud_empleado 
        LEFT JOIN gn_concepto ccc   ON ccc.id_unico     = ci.caja_compensacion 
        LEFT JOIN gn_concepto cs    ON cs.id_unico      = ci.sena 
        LEFT JOIN gn_concepto cicbf ON cicbf.id_unico   = ci.icbf 
        LEFT JOIN gn_concepto cesap ON cesap.id_unico   = ci.esap 
        LEFT JOIN gn_concepto cmine ON cmine.id_unico   = ci.ministerio_educacion 
        LEFT JOIN gn_concepto cit   ON cit.id_unico     = ci.institutos_tecnicos 
        LEFT JOIN gn_concepto cfsp  ON cfsp.id_unico    = ci.fondo_solidaridad 
        LEFT JOIN gn_concepto carl  ON carl.id_unico    = ci.arl 
        WHERE md5(ci.tipo_incapacidad)='$id'");

    $rowc = $con->Listar("SELECT id_unico, codigo, descripcion FROM gn_concepto WHERE compania = $compania");
}
?>
<title>Tipo Novedad</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script src="dist/jquery.validate.js"></script>
<script src="js/md5.pack.js"></script>
<style>
    label #nombre-error, #tipo-error,#clase-error,#concepto-error, #dias-error, #valor-error{
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
</script>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px">
                <h2 id="forma-titulo3" align="center" style=" margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Tipo Novedad'?></h2>
                <?php if(empty($_GET['id'])) { ?>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Tipo</strong></td>
                                    <td><strong>Clase</strong></td>
                                    <td><strong>Concepto</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Clase</th>
                                    <th>Concepto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                    <tr>
                                        <td style="display: none;"></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="GN_TIPO_NOVEDAD.php?id=2&id_r=<?php echo md5($row[$i][0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                            <a href="GN_TIPO_NOVEDAD.php?id=3&id_r=<?php echo md5($row[$i][0]); ?>"><i title="Conceptos" class="glyphicon glyphicon-th-list" ></i></a>
                                            
                                        </td>
                                        <td><?php echo ($row[$i][1]); ?></td>
                                        <td><?php echo $row[$i][2]; ?></td>
                                        <td><?php echo utf8_encode($row[$i][4]); ?></td>
                                        <td><?php echo $row[$i][6].' - '.utf8_encode($row[$i][7]); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="GN_TIPO_NOVEDAD.php?id=1" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
                    </div>
                </div>
                <?php }  elseif(($_GET['id'])==1){ ?>
                    <a href="GN_TIPO_NOVEDAD.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: 10px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -30px;" >
                                    <label for="nombre" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -30px;">
                                    <input type="text" name="nombre" id="nombre" class="form-control " style=" width: 100%" required="required" placeholder="Nombre" title="Ingrese Nombre" >
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="tipo" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Tipo:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="tipo" id="tipo" class="form-control " style=" width: 100%" required="required" placeholder="Tipo" title="Ingrese Tipo" onkeyup="javascript:this.value = this.value.toUpperCase();">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="clase" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Clase:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <select  name="clase" id="clase" title="Seleccione clase" style="height: 30px;" class="select2_single form-control" required="required" >
                                    <option value="">Clase</option>
                                    <?php
                                    $rowc = $con->Listar("SELECT id_unico, nombre FROM gn_clase_novedad");
                                    for ($i=0; $i <count($rowc) ; $i++) { 
                                        echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1]).'</option>';
                                    }
                                    ?>
                                </select>
                                </div>
                            </div> 
                             <div class="form-group" style="margin-top: 0px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="concepto" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <select  name="concepto" id="concepto" title="Seleccione Concepto" style="height: 30px;" class="select2_single form-control" required="required" >
                                    <option value="">Concepto</option>
                                    <?php
                                    $rowc = $con->Listar("SELECT id_unico, codigo, descripcion FROM gn_concepto WHERE unidadmedida = 3 and clase= 10  and id_unico in (SELECT conceptorel FROM gn_concepto)");
                                    for ($i=0; $i <count($rowc) ; $i++) { 
                                        echo '<option value="'.$rowc[$i][0].'">'.$rowc[$i][1].' - '.utf8_encode($rowc[$i][2]).'</option>';
                                    }
                                    ?>
                                </select>
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                <?php } elseif(($_GET['id'])==2){  ?>
                    <a href="GN_TIPO_NOVEDAD.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="id" id="id" value="<?= $row[0][0]?>">
                            <div class="form-group" style="margin-top: 10px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5" text-aling="left" style="margin-top: -30px;" >
                                    <label for="nombre" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top: -30px;">
                                    <input type="text" name="nombre" id="nombre" class="form-control " style=" width: 100%" required="required" placeholder="Nombre" title="Ingrese Nombre" value="<?= $row[0][1]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="tipo" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Tipo:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <input type="text" name="tipo" id="tipo" class="form-control " style=" width: 100%" required="required" placeholder="Tipo" title="Ingrese Tipo" onkeyup="javascript:this.value = this.value.toUpperCase();" value="<?= $row[0][2]?>">
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: -20px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="clase" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Clase:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <select  name="clase" id="clase" title="Seleccione clase" style="height: 30px;" class="select2_single form-control" required="required" >
                                    <?php if(empty($row[0][3])){
                                        echo ' <option value=""> - </option>';
                                        $rowc = $con->Listar("SELECT id_unico, nombre FROM gn_clase_novedad");
                                    } else {
                                        echo ' <option value="'.$row[0][3].'">'.utf8_encode($row[0][4]).'</option>';
                                        $rowc = $con->Listar("SELECT id_unico, nombre FROM gn_clase_novedad WHERE id_unico != ".$row[0][3]);
                                    }
                                    
                                    for ($i=0; $i <count($rowc) ; $i++) { 
                                        echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1]).'</option>';
                                    }
                                    ?>
                                </select>
                                </div>
                            </div> 
                             <div class="form-group" style="margin-top: 0px;">
                                <div class="form-group form-inline  col-md-5 col-lg-5"  style="margin-top: -0px;" >
                                    <label for="concepto" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px;margin-top:0px;">
                                    <select  name="concepto" id="concepto" title="Seleccione Concepto" style="height: 30px;" class="select2_single form-control" required="required" >
                                    <?php if(empty($row[0][5])){
                                        echo ' <option value=""> - </option>';
                                        $rowc = $con->Listar("SELECT id_unico, codigo, descripcion FROM gn_concepto WHERE unidadmedida = 3 and clase= 10");
                                    } else {
                                        echo ' <option value="'.$row[0][5].'">'.$row[0][6].' - '.utf8_encode($row[0][7]).'</option>';
                                        $rowc = $con->Listar("SELECT id_unico, codigo, descripcion FROM gn_concepto WHERE unidadmedida = 3  and clase= 10  and id_unico in (SELECT conceptorel FROM gn_concepto) AND id_unico != ".$row[0][5]);
                                    }
                                    
                                    for ($i=0; $i <count($rowc) ; $i++) { 
                                        echo '<option value="'.$rowc[$i][0].'">'.$rowc[$i][1].' - '.utf8_encode($rowc[$i][2]).'</option>';
                                    }
                                    ?>
                                </select>
                                </div>
                            </div> 
                            <div class="form-group" style="margin-top: 20px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                <?php } elseif(($_GET['id'])==3){ ?>

                    <a href="GN_TIPO_NOVEDAD.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="formC" id="formC" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:guardarDt()">
                            <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <input type="hidden" name="idt" id="idt" value="<?=$rowt[0][0]?>">
                            <div class="form-group" style="margin-top: 10px;">
                                <div class="form-group form-inline  col-md-1 col-lg-1" text-aling="left" style="margin-left: 10px;" >
                                    <label for="diasI" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Dias Incapacidad:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <input type="text" name="diasI" id="diasI" class="form-control " style=" width: 100%"  placeholder="Días Incapacidad" title="Ingrese Dias Incapacidad" >
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" text-aling="left"  style="margin-left: 10px;">
                                    <label for="porcentaje" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Porcentaje:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <input type="text" name="porcentaje" id="porcentaje" class="form-control " style=" width: 100%"  placeholder="Porcentaje" title="Ingrese Porcentaje" >
                                </div>

                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="dias" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Días:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="dias" id="dias" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" required="required" >
                                        <option value="">Concepto Días</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group" style="margin-top: -15px;">
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="valor" class="col-sm-12 control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="valor" id="valor" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" required="required" >
                                        <option value="">Concepto Valor</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="ibc" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>IBC:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="ibc" id="ibc" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" >
                                        <option value="">Concepto IBC</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="app" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Aporte Pensión P:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="app" id="app" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control"  >
                                        <option value="">Aporte Pensión Patrono</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: -15px;">
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="appe" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Aporte Pensión E:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="appe" id="appe" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" >
                                        <option value="">Aporte Pensión Empleado</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="asp" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Aporte Salud P:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="asp" id="asp" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" >
                                        <option value="">Aporte Salud Patrono</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="ase" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Aporte Salud E:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="ase" id="ase" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control"  >
                                        <option value="">Aporte Salud Empleado</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: -15px;">
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="cc" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Caja Comp.:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="cc" id="cc" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" >
                                        <option value="">Caja Compensación</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="sena" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>SENA:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="sena" id="sena" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" >
                                        <option value="">SENA</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="icbf" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>ICBF:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="icbf" id="icbf" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control"  >
                                        <option value="">ICBF</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: -15px;">
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="esap" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>ESAP:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="esap" id="esap" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" >
                                        <option value="">ESAP</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="me" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Ministerio Educación:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="me" id="me" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" >
                                        <option value="">Ministerio Educación</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="it" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Institutos Técnicos:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="it" id="it" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control"  >
                                        <option value="">Institutos Técnicos</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="fsp" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>Fondo Solidaridad:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="fsp" id="fsp" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" >
                                        <option value="">Fondo Solidaridad</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1"  style="margin-left: 10px;" >
                                    <label for="arl" class="col-sm-12 control-label"><strong style="color:#03C1FB;"></strong>ARL:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left: 10px;">
                                    <select  name="arl" id="arl" title="Seleccione Concepto" style="height: 30px; width: 250px" class="select2_single form-control" >
                                        <option value="">ARL</option>
                                        <?php for ($i=0; $i <count($rowc) ; $i++) { 
                                            echo '<option value="'.$rowc[$i][0].'">'.utf8_encode($rowc[$i][1].' - '.$rowc[$i][2]).'</option>';
                                        }?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: 10px;">
                                    <label for="no" class="col-sm-5 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:30px">Guardar</button>
                                </div>
                            </div>

                            
                        </form>
                    </div>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px"></td>
                                        <td><strong>Días Incapacidad</strong></td>
                                        <td><strong>Porcentaje</strong></td>
                                        <td><strong>Días</strong></td>
                                        <td><strong>Valor</strong></td>
                                        <td><strong>IBC</strong></td>
                                        <td><strong>Aporte Pensión Patrono</strong></td>
                                        <td><strong>Aporte Pensión Empleado</strong></td>
                                        <td><strong>Aporte Salud Patrono</strong></td>
                                        <td><strong>Aporte Salud Empleado</strong></td>
                                        <td><strong>Caja Compensación</strong></td>
                                        <td><strong>Sena</strong></td>
                                        <td><strong>ICBF</strong></td>
                                        <td><strong>ESAP</strong></td>
                                        <td><strong>Ministerio Educación</strong></td>
                                        <td><strong>Institutos Técnicos</strong></td>
                                        <td><strong>Fondo Solidaridad</strong></td>
                                        <td><strong>ARL</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Días Incapacidad</th>
                                        <th>Porcentaje</th>
                                        <th>Días</th>
                                        <th>Valor</th>
                                        <th>IBC</th>
                                        <th>Aporte Pensión Patrono</th>
                                        <th>Aporte Pensión Empleado</th>
                                        <th>Aporte Salud Patrono</th>
                                        <th>Aporte Salud Empleado</th>
                                        <th>Caja Compensación</th>
                                        <th>Sena</th>
                                        <th>ICBF</th>
                                        <th>ESAP</th>
                                        <th>Ministerio Educación</th>
                                        <th>Institutos Técnicos</th>
                                        <th>Fondo Solidaridad</th>
                                        <th>ARL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < count($row); $i++) { ?>
                                        <tr>
                                            <td style="display: none;"></td>
                                            <td>
                                                <a href="#" onclick="javascript:eliminarCN(<?php echo $row[$i][0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>                                               
                                            </td>
                                            <td><?= ($row[$i][2]); ?></td>
                                            <td><?= ($row[$i][3]); ?></td>
                                            <td><?= utf8_encode($row[$i][5].' - '.$row[$i][6]); ?></td>
                                            <td><?= utf8_encode($row[$i][8].' - '.$row[$i][9]); ?></td>
                                            <td><?= utf8_encode($row[$i][11].' - '.$row[$i][12]); ?></td>                                            
                                            <td><?= utf8_encode($row[$i][14].' - '.$row[$i][15]); ?></td>
                                            <td><?= utf8_encode($row[$i][17].' - '.$row[$i][18]); ?></td>
                                            <td><?= utf8_encode($row[$i][20].' - '.$row[$i][21]); ?></td>
                                            <td><?= utf8_encode($row[$i][23].' - '.$row[$i][24]); ?></td>
                                            <td><?= utf8_encode($row[$i][26].' - '.$row[$i][27]); ?></td>
                                            <td><?= utf8_encode($row[$i][29].' - '.$row[$i][30]); ?></td>

                                            <td><?= utf8_encode($row[$i][32].' - '.$row[$i][33]); ?></td>
                                            <td><?= utf8_encode($row[$i][35].' - '.$row[$i][36]); ?></td>
                                            <td><?= utf8_encode($row[$i][38].' - '.$row[$i][39]); ?></td>
                                            <td><?= utf8_encode($row[$i][41].' - '.$row[$i][42]); ?></td>
                                            <td><?= utf8_encode($row[$i][44].' - '.$row[$i][45]); ?></td>
                                            <td><?= utf8_encode($row[$i][47].' - '.$row[$i][48]); ?></td>

                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>   
                        </div>
                    </div>
                <?php } ?>    
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
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $("#clase").select2();
        $("#concepto").select2();

        $("#dias").select2();
        $("#valor").select2();
        $("#ibc").select2();
        $("#app").select2();
        $("#appe").select2();
        $("#asp").select2();
        $("#ase").select2();
        $("#cc").select2();
        $("#sena").select2();
        $("#icbf").select2();
        $("#esap").select2();
        $("#me").select2();
        $("#it").select2();
        $("#fsp").select2();
        $("#arl").select2();

        $(".select2").select2({
            allowClear:true
        });
    </script>
    <script>
        function registrar(){
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonNomina/gn_TipoNovedadJson.php?action=1",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                {
                    console.log(response);
                    if(response==1){
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location='GN_TIPO_NOVEDAD.php';
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
    <script>
        function modificar(){
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonNomina/gn_TipoNovedadJson.php?action=2",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                {
                    console.log(response);
                    if(response==1){
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location='GN_TIPO_NOVEDAD.php';
                        })
                    } else {
                        $("#mensaje").html('No Se Ha Podido Modificar Información');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            $("#modalMensajes").modal("hide");
                        })

                    }
                }
            });
        }
    </script>
    <script>
        function eliminar(id){
            $("#modalEliminar").modal("show");
            $("#aceptarE").click(function(){
                $("#modalEliminar").modal("hide");
                var form_data = {action:3, id:id};  
                $.ajax({
                    type: 'POST',
                    url: "jsonNomina/gn_TipoNovedadJson.php",
                    data: form_data, 
                    success: function(response) {
                        console.log(response);
                        if(response==1){
                            $("#mensaje").html('Información Eliminada Correctamente');
                            $("#modalMensajes").modal("show");
                            $("#Aceptar").click(function(){
                                document.location='GN_TIPO_NOVEDAD.php';
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
    <script>
        function guardarDt(){
            var formData = new FormData($("#formC")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonNomina/gn_TipoNovedadJson.php?action=4",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                {
                    console.log(response);
                    if(response==1){
                        $("#mensaje").html('Información Guardada Correctamente');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location.reload();
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

    <script>
        function eliminarCN(id){
            $("#modalEliminar").modal("show");
            $("#aceptarE").click(function(){
                $("#modalEliminar").modal("hide");
                var form_data = {action:5, id:id};  
                $.ajax({
                    type: 'POST',
                    url: "jsonNomina/gn_TipoNovedadJson.php",
                    data: form_data, 
                    success: function(response) {
                        console.log(response);
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

