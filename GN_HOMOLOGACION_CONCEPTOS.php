<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
require_once('./jsonPptal/funcionesPptal.php');
$compania   =$_SESSION['compania'];
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$grupo      ='Grupo de Gestión: ';
if(!empty($_GET['gg'])){
    $row = $con->Listar("SELECT DISTINCT  cnf.id_unico, 
            CONCAT_WS(' - ',cn.codigo,LOWER(cn.descripcion)), 
            LOWER(c.nombre),
            CONCAT( rp.codi_presupuesto,' ',LOWER(rp.nombre), ' - ', LOWER(f.nombre)), 
            gg.nombre, 
            IF(CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) 
            IS NULL OR CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) = '',
            (tr.razonsocial),
            CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)) AS NOMBRE, 
            tr.numeroidentificacion, tr.digitoverficacion, 
            cnf.tipo 
        FROM gn_concepto_nomina_financiero cnf 
        LEFT JOIN gn_concepto cn ON cnf.concepto_nomina = cn.id_unico 
        LEFT JOIN gf_concepto_rubro cf ON cnf.concepto_financiero = cf.id_unico 
        LEFT JOIN gf_concepto c ON cf.concepto = c.id_unico 
        LEFT JOIN gf_rubro_fuente rf ON rf.id_unico = cnf.rubro_fuente  
        LEFT JOIN gf_rubro_pptal rp ON cf.rubro = rp.id_unico 
        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
        LEFT JOIN gn_grupo_gestion gg ON cnf.grupo_gestion = gg.id_unico 
        LEFT JOIN gf_tercero tr ON cnf.tercero = tr.id_unico 
        WHERE cnf.parametrizacionanno = $anno AND cnf.grupo_gestion=".$_GET['gg']);
    $grupo .= $row[0][4];
}
?>
<head>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script> 
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="js/md5.pack.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <title>Homologación de Conceptos</title>
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
    label#conceptoN-error, #conceptoF-error,#rubroF-error, #tercero-error{
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;
    }
    
</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top: -15px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 0px; margin-right: 4px; margin-left: 4px;">Homologación de Conceptos</h2>
                <a href="LISTAR_GN_CONCEPTOS_HOMOLOGACION.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $grupo;?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
                    <?php  if(!empty($_GET['gg']) && empty($_GET['id'])) { ?>
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                         <div class="form-group form-inline " style="margin-top: -5px;margin-left: 0px">
                            <input type="hidden" name="grupoG" id="grupoG" value="<?php echo $_GET['gg']?>">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="conceptoN" class="col-sm-12 control-label"><strong class="obligado">*</strong>Concepto Nómina:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  0px;">
                                <select name="conceptoN" id="conceptoN" class="form-control select2" title="Seleccione Concepto Nómina" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Concepto Nómina</option>';
                                        $tr = $con->Listar("SELECT id_unico, codigo, 
                                            LOWER(descripcion) FROM gn_concepto 
                                            WHERE compania = $compania  
                                            AND (clase =1 OR clase =2 OR clase = 7 OR clase = 5)  
                                            AND unidadmedida = 1 ORDER BY codigo ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][1].' - '.ucwords($tr[$i][2]).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="conceptoF" class="col-sm-12 control-label"><strong class="obligado">*</strong>Concepto Financiero:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  20px;">
                                <select name="conceptoF" id="conceptoF" class="form-control select2" title="Seleccione Concepto Financiero" style="height: auto " required>
                                    <option value="">Concepto Financiero</option>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="rubroF" class="col-sm-12 control-label"><strong class="obligado"></strong>Rubro Fuente:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  20px;">
                                <select name="rubroF" id="rubroF" class="form-control select2" title="Seleccione Rubro Fuente" style="height: auto ">
                                    <option value="">Rubro Fuente</option>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="tercero" class="col-sm-12 control-label"><strong class="obligado"></strong>Tercero:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  0px;">
                                <select name="tercero" id="tercero" class="form-control select2" title="Seleccione Tercero" style="height: auto " >
                                    <?php 
                                        echo '<option value="">Tercero</option>';
                                        $tr = $con->Listar("SELECT distinct tr.id_unico, IF(CONCAT_WS(' ',
                                                tr.nombreuno,
                                                tr.nombredos,
                                                tr.apellidouno,
                                                tr.apellidodos) 
                                                IS NULL OR CONCAT_WS(' ',
                                                tr.nombreuno,
                                                tr.nombredos,
                                                tr.apellidouno,
                                                tr.apellidodos) = '',
                                                (tr.razonsocial),
                                                CONCAT_WS(' ',
                                                tr.nombreuno,
                                                tr.nombredos,
                                                tr.apellidouno,
                                                tr.apellidodos)) AS NOMBRE, 
                                                IF(tr.digitoverficacion IS NULL OR tr.digitoverficacion='',
                                                     tr.numeroidentificacion, 
                                                CONCAT(tr.numeroidentificacion, ' - ', tr.digitoverficacion)) 

                                               FROM gf_tercero tr 
                                               LEFT JOIN gf_perfil_tercero  pt ON tr.id_unico = pt.tercero 
                                              WHERE pt.perfil in (1,2,11,12)
                                               ORDER BY NOMBRE ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1].' - '.$tr[$i][2])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-top:  -20px;margin-left:-20px">
                                <button onclick="javaScript:grupog()" style="margin-left:20px;margin-top: 0px" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                <button type="submit" style="margin-left:20px; margin-top: 0px" type="button"  class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <div class="form-group form-inline " id="divTipo" style="display:none;margin-top: -20px;margin-left: 0px">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-top: -20px;">
                                <label for="tipo" class="col-sm-12 control-label"><strong class="obligado"></strong>Tipo:</label>
                             </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-top: -20px;margin-left: 10px">
                                <input type="radio" id="tipo" name="tipo" value="1">Público
                                <input type="radio" id="tipo" name="tipo" value="2">Privado
                                <a onclick="borrarRadio()"><i title="Borrar" class="glyphicon glyphicon-remove"></i></a>
                            </div>
                        </div>
                    </form>
                    <br/>
                    
                    <?php } else { 
                            $rowc = $con->Listar("SELECT cnf.id_unico, 
                                cn.id_unico, CONCAT(cn.codigo,' - ',LOWER(cn.descripcion)), 
                                cf.id_unico,
                                CONCAT(LOWER(c.nombre),' - ', rcon.codi_presupuesto, ' ', LOWER(rcon.nombre)),
                                gg.id_unico,  LOWER(gg.nombre), 
                                tr.id_unico, IF(CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos) = '',
                                (tr.razonsocial),
                                CONCAT_WS(' ',
                                tr.nombreuno,
                                tr.nombredos,
                                tr.apellidouno,
                                tr.apellidodos)) AS NOMBRE, 
                                IF(tr.digitoverficacion IS NULL OR tr.digitoverficacion='',
                                    tr.numeroidentificacion, 
                               CONCAT(tr.numeroidentificacion, ' - ', tr.digitoverficacion)) , tr.digitoverficacion, cn.clase, 
                                rf.id_unico, 
                                CONCAT(rp.codi_presupuesto, ' ', LOWER(rp.nombre), ' - ', f.id_unico ,' ', LOWER(f.nombre)) , 
                                rf.rubro , rcon.id_unico, cnf.tipo 
                            FROM gn_concepto_nomina_financiero cnf 
                            LEFT JOIN gn_concepto cn ON cnf.concepto_nomina = cn.id_unico 
                            LEFT JOIN gf_concepto_rubro cf ON cnf.concepto_financiero = cf.id_unico 
                            LEFT JOIN gf_concepto c ON cf.concepto = c.id_unico 
                            LEFT JOIN gf_rubro_pptal rcon ON rcon.id_unico = cf.rubro 
                            LEFT JOIN gf_rubro_fuente rf ON cnf.rubro_fuente = rf.id_unico 
                            LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico 
                            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                            LEFT JOIN gn_grupo_gestion gg ON cnf.grupo_gestion = gg.id_unico 
                            LEFT JOIN gf_tercero tr ON cnf.tercero = tr.id_unico 
                            WHERE cnf.id_unico ='".$_GET['id']."'");
                            ?>
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:modificar()">
                            <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                             <div class="form-group form-inline " style="margin-top: -5px;margin-left: 0px">
                                <input type="hidden" name="grupoG" id="grupoG" value="<?php echo $_GET['gg']?>">
                                <input type="hidden" name="id" id="id" value="<?php echo $rowc[0][0]?>">
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                    <label for="conceptoN" class="col-sm-12 control-label"><strong class="obligado">*</strong>Concepto Nómina:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  0px;">
                                    <select name="conceptoN" id="conceptoN" class="form-control select2" title="Seleccione Concepto Nómina" style="height: auto " required>
                                        <?php 
                                            if(!empty($rowc[0][1])){
                                                $idcn = $rowc[0][1];
                                                echo '<option value="'.$rowc[0][1].'">'.ucwords($rowc[0][2]).'</option>';
                                            } else {
                                                $idcn = 0;
                                                echo '<option value="">Concepto Nómina</option>';
                                            }
                                            $tr = $con->Listar("SELECT id_unico, codigo, 
                                                LOWER(descripcion) FROM gn_concepto 
                                                WHERE id_unico != $idcn AND compania = $compania  
                                                AND (clase =1 OR clase =2 OR clase = 7 OR clase = 5)  
                                                AND unidadmedida = 1 ORDER BY codigo ASC");
                                            for ($i = 0; $i < count($tr); $i++) {
                                               echo '<option value="'.$tr[$i][0].'">'.$tr[$i][1].' - '.ucwords($tr[$i][2]).'</option>'; 
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                    <label for="conceptoF" class="col-sm-12 control-label"><strong class="obligado">*</strong>Concepto Financiero:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  20px;">
                                    <select name="conceptoF" id="conceptoF" class="form-control select2" title="Seleccione Concepto Financiero" style="height: auto " required>
                                        <?php $idcf = $rowc[0][3];
                                        $nomcf = $rowc[0][4];
                                        if(!empty($rowc[0][3])){
                                            echo '<option value="'.$idcf.'">'.ucwords($nomcf).'</option>';
                                        } else {
                                            echo '<option value="">Concepto Financiero</option>';
                                        }
                                        switch ($rowc[0][11]){
                                            case (1):
                                                $conf = $con->Listar("SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                                                ."LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                                                ."LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                                                ." WHERE c.clase_concepto =2 AND c.parametrizacionanno = $anno AND cr.id_unico != $idcf  ");
                                            break;
                                            case (7):
                                                $conf = $con->Listar("SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                                                ."LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                                                ."LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                                                ." WHERE c.clase_concepto =2 AND c.parametrizacionanno = $anno AND cr.id_unico != $idcf  ");
                                            break;
                                            case (2):
                                                $conf = $con->Listar("SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                                                ."LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                                                ."LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                                                ." WHERE c.clase_concepto =3 AND c.parametrizacionanno = $anno AND cr.id_unico != $idcf  ");
                                            break;
                                            case (5):
                                                $conf = $con->Listar("SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                                                ."LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                                                ."LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                                                ." WHERE c.clase_concepto =3 AND c.parametrizacionanno = $anno AND cr.id_unico != $idcf  ");
                                                break;
                                            default :
                                                $conf = $con->Listar("SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                                                ."LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                                                ."LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                                                ." WHERE c.clase_concepto =0 AND c.parametrizacionanno = $anno AND cr.id_unico != $idcf  ");
                                            break;
                                        }
                                        for ($cf = 0; $cf < count($conf); $cf++) {
                                            echo '<option value="'.$conf[$cf][0].'">'.ucwords($conf[$cf][1]).'</option>';
                                        }
                                        ?>
                                        
                                        
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                    <label for="rubroF" class="col-sm-12 control-label"><strong class="obligado"></strong>Rubro Fuente:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  20px;">
                                    <select name="rubroF" id="rubroF" class="form-control select2" title="Seleccione Rubro Fuente" style="height: auto ">
                                        <?php if(!empty($rowc[0][12])){
                                            $idrf = $rowc[0][12];
                                            echo '<option value="'.$rowc[0][12].'">'.ucwords($rowc[0][13]).'</option>';
                                        } else {
                                            $idrf = 0;
                                            echo '<option value=""> - </option>';
                                        } 
                                        $rubro = $rowc[0][15];
                                        $rowrf = $con->Listar("SELECT rf.id_unico, CONCAT(rp.codi_presupuesto, ' ', LOWER(rp.nombre),' - ', f.id_unico, ' ',LOWER(f.nombre)) 
                                        FROM gf_rubro_fuente rf LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico 
                                        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                                        WHERE rf.rubro = $rubro AND rf.id_unico != $idrf");
                                        for ($r = 0; $r < count($rowrf); $r++) {
                                            echo '<option value="'.$rowrf[$r][0].'">'.ucwords($rowrf[$r][1]).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                    <label for="tercero" class="col-sm-12 control-label"><strong class="obligado"></strong>Tercero:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  0px;">
                                    <select name="tercero" id="tercero" class="form-control select2" title="Seleccione Tercero" style="height: auto " >
                                        <?php 
                                        if(empty($rowc[0][7])){
                                            echo '<option value="">Tercero</option>'; 
                                            $idt = 0;
                                        } else {
                                            echo '<option value="'.$rowc[0][7].'">'.ucwords(mb_strtolower($rowc[0][8].' - '.$rowc[0][9])).'</option>'; 
                                            echo '<option value=""> - </option>'; 
                                            $idt = $rowc[0][7];
                                        }
                                        $tr = $con->Listar("SELECT tr.id_unico, IF(CONCAT_WS(' ',
                                                tr.nombreuno,
                                                tr.nombredos,
                                                tr.apellidouno,
                                                tr.apellidodos) 
                                                IS NULL OR CONCAT_WS(' ',
                                                tr.nombreuno,
                                                tr.nombredos,
                                                tr.apellidouno,
                                                tr.apellidodos) = '',
                                                (tr.razonsocial),
                                                CONCAT_WS(' ',
                                                tr.nombreuno,
                                                tr.nombredos,
                                                tr.apellidouno,
                                                tr.apellidodos)) AS NOMBRE, 
                                                IF(tr.digitoverficacion IS NULL OR tr.digitoverficacion='',
                                                     tr.numeroidentificacion, 
                                                CONCAT(tr.numeroidentificacion, ' - ', tr.digitoverficacion)) 

                                               FROM gf_tercero tr WHERE id_unico != $idt ORDER BY NOMBRE ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1].' - '.$tr[$i][2])).'</option>'; 
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-top:  -20px;margin-left:-20px">
                                    <button onclick="javaScript:grupog()" style="margin-left:20px;margin-top: 0px" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                    <button type="submit" style="margin-left:20px; margin-top: 0px" type="button"  class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <div class="form-group form-inline " id="divTipo" style="display:none;margin-top: -20px;margin-left: 0px">
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-top: -20px;">
                                    <label for="tipo" class="col-sm-12 control-label"><strong class="obligado"></strong>Tipo:</label>
                                 </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-top: -20px;margin-left: 10px">
                                    <?php switch ($rowc[0][16]){
                                        case (1):
                                            echo '<input type="radio" id="tipo" name="tipo" value="1" checked="checked">Público';
                                            echo '<input type="radio" id="tipo" name="tipo" value="2">Privado';
                                        break;
                                        case (2):
                                            echo '<input type="radio" id="tipo" name="tipo" value="1">Público';
                                            echo '<input type="radio" id="tipo" name="tipo" value="2" checked="checked">Privado';
                                        break;
                                        default:
                                            echo '<input type="radio" id="tipo" name="tipo" value="1">Público';
                                            echo '<input type="radio" id="tipo" name="tipo" value="2">Privado';
                                        break;
                                    }?>                                    
                                    <a onclick="borrarRadio()"><i title="Borrar" class="glyphicon glyphicon-remove"></i></a>
                                </div>
                            </div>
                        </form>
                        <script>
                            $(document).ready(function (){
                                var concepto  = $("#conceptoN").val();
                                var form_data = {action: 11, concepto:concepto};
                                $.ajax({
                                    type:"POST",
                                    url: "jsonPptal/gn_nomina_financieraJson.php",
                                    data: form_data,
                                     success: function(response){
                                        console.log('Conceptom'+response);
                                        if(response ==2 || response ==5){
                                            $("#rubroF").attr("required", false);
                                        } else {
                                            $("#rubroF").attr("required", true);
                                        }
                                        if(response ==7){
                                            $("#divTipo").css('display', 'block');
                                        } else {
                                            $("#divTipo").css('display', 'none');
                                        }
                                    }
                                });
                            });
                       </script> 
                        <?php } ?>
                </div>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Concepto Nómina</strong></td>
                                    <td><strong>Concepto Financiero</strong></td>
                                    <td><strong>Rubro Presupuestal</strong></td>
                                    <td><strong>Tercero</strong></td>
                                    <td><strong>Tipo</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Concepto Nómina</th>
                                    <th>Concepto Financiero</th>
                                    <th>Rubro Presupuestal</th>
                                    <th>Tercero</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>

                                <tr>
                                    <td style="display: none;"><?php echo $row[$i][0]?></td>    
                                    <td><a  href="#" onclick="javascript:eliminar(<?php echo $row[$i][0]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a onclick="javaScript:modificaram(<?php echo $row[$i][0]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo ucwords(mb_strtolower($row[$i][1])); ?></td>  
                                    <td><?php echo ucwords(mb_strtolower($row[$i][2])); ?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[$i][3])); ?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[$i][5].' - '.$row[$i][6])); ?></td>  
                                    <td><?php if($row[$i][8]==1){echo 'Pública';}elseif($row[$i][8]==2){echo 'Privada';} else { echo '';} ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    
    
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
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlgg" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Grupo de Gestión</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label class="form_label"><strong style="color:#03C1FB;">*</strong>Grupo de Gestión: </label>
                <select name="grupog" id="grupog" class="form-control input-sm" title="Grupo de Gestión" style="width:250px;">
                    <option value="" >Grupo de Gestión</option>
                        <?php
                        $row1 = "SELECT id_unico, LOWER(nombre) FROM gn_grupo_gestion  ORDER BY nombre ASC";
                        $row1 = $mysqli->query($row1);

                        while ($row = mysqli_fetch_row($row1)) {
                            echo "<option value='$row[0]'>". ucwords($row[1])."</option>";
                        } 
                        ?>
                </select> 
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="guardarG" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" id="cancelarG" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
            </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              var form_data = {action:2,id:id};
              $.ajax({
                  type:"POST",
                  url:"jsonPptal/gn_nomina_financieraJson.php",
                  data: form_data,
                  success: function (data) {
                    console.log(data);
                    result = JSON.parse(data);
                    if (result == true) {
                        $("#mensaje").html("Información Eliminada Correctamente");
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location.reload();
                        })
                    } else { 
                        $("#mensaje").html("No se ha podido eliminar la información");
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location.reload();
                        })
                    }
                }
            });
        });
    }
    function modificaram(id){
        document.location = 'GN_HOMOLOGACION_CONCEPTOS.php?gg='+$("#grupoG").val()+'&id='+id;
    }
    function grupog(){
        $("#mdlgg").modal("show");
    }
    $("#guardarG").click(function(){
        var gg = $("#grupog").val();
        if(gg!=''){
            window.location ='GN_HOMOLOGACION_CONCEPTOS.php?gg='+gg;
        } else {
            $("#mdlgg").modal("hide");
        }
    })
    $("#cancelarG").click(function(){
        $("#mdlgg").modal("hide");
    })
    function borrarRadio(){
        document.getElementsByName("tipo")[0].checked = false;
        document.getElementsByName("tipo")[1].checked = false;
      }
</script>

<script>
    $("#conceptoN").change(function(){
        document.getElementsByName("tipo")[0].checked = false;
        document.getElementsByName("tipo")[1].checked = false;
        $("#conceptoF").val("");
        $("#rubroF").val("");
        var concepto = $("#conceptoN").val();
        if(concepto ==""){

        } else {
            //Verificar si es concepto devengo no es necesario Rubro Fuente
            var form_data= {action: 11, concepto:concepto};
            $.ajax({
                type:"POST",
                url: "jsonPptal/gn_nomina_financieraJson.php",
                data: form_data,
                success: function(response){
                    console.log(response);
                    if(response ==2 || response ==5){
                        $("#rubroF").attr("required", false);
                    } else  {
                        $("#rubroF").attr("required", true);
                    }
                    if(response ==7){
                        $("#divTipo").css('display', 'block');
                    } else {
                        $("#divTipo").css('display', 'none');
                    }
                }
            });
            var form_data= {action: 6, concepto:concepto};
            var opcion = '<option value="" >Concepto Financiero</option>';
            $.ajax({
                type:"POST",
                url: "jsonPptal/gn_nomina_financieraJson.php",
                data: form_data,
                success: function(response){
                    opcion +=response;
                    $("#conceptoF").html(opcion).focus();
                }
            })
        }
   })
</script> 
<script>
    $("#conceptoF").change(function(){
        $("#rubroF").val("");
        var concepto = $("#conceptoF").val();
        if(concepto ==""){

        } else {
            var form_data= {action: 8, concepto:concepto};
            var opcion = '<option value="" >Rubro Fuente</option>';
            $.ajax({
                type:"POST",
                url: "jsonPptal/gn_nomina_financieraJson.php",
                data: form_data,
                success: function(response){
                    console.log(response );
                    opcion +=response;
                    $("#rubroF").html(opcion).focus();
                }
           })
        }
    })
</script> 
<script>
    function guardar(){
        jsShowWindowLoad('Guardando');
        var formData = new FormData($("#form")[0]);  
        $.ajax({
            type: 'POST',
            url: "jsonPptal/gn_nomina_financieraJson.php?action=1",
            data:formData,
            contentType: false,
            processData: false,         
            success: function(response)
            { 
                jsRemoveWindowLoad();
                console.log(response);
                if(response ==true){
                  $("#mensaje").html("Información Guardada Correctamente");
                  $("#modalMensajes").modal("show");
                  $("#Aceptar").click(function(){
                      document.location.reload();
                  })
                } else {
                  $("#mensaje").html("No Se Ha Podido Guardar La Información");
                  $("#modalMensajes").modal("show");
                  $("#Aceptar").click(function(){
                      $("#mdlMensajes").modal("hide");
                  })
                }
            }//Fin succes.
        }); 
    }
</script>
<script>
    function modificar(){
        jsShowWindowLoad('Modificando...');
        var formData = new FormData($("#form")[0]);  
        $.ajax({
            type: 'POST',
            url: "jsonPptal/gn_nomina_financieraJson.php?action=3",
            data:formData,
            contentType: false,
            processData: false,   
            success: function(response) { 
                jsRemoveWindowLoad();
                console.log(response);
                if(response ==true){
                  $("#mensaje").html("Información Modificada Correctamente");
                  $("#modalMensajes").modal("show");
                  $("#Aceptar").click(function(){
                      document.location ='GN_HOMOLOGACION_CONCEPTOS.php?gg='+$("#grupoG").val();
                  })
                } else {
                  $("#mensaje").html("No Se Ha Podido Modificar La Información");
                  $("#modalMensajes").modal("show");
                  $("#Aceptar").click(function(){
                      $("#mdlMensajes").modal("hide");
                  })
                }
            }//Fin succes.
        }); 
    }
</script>
<script type="text/javascript"> 
        $("#conceptoN").select2();
        $("#tercero").select2();
        $("#conceptoF").select2();
        $("#rubroF").select2();
        $("#grupog").select2();
    </script>
</body>
</html>





