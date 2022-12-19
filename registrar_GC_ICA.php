<?php
    require_once 'head.php';
    require_once './Conexion/conexion.php';    
    if(!empty($_REQUEST['peri2'])){
        @$periodi = $_REQUEST['peri2'];
        @$TipoDC  = $_REQUEST['TipoD'];
        @$FDEC    = $_REQUEST['FD'];
        #@$Pmed    = $_REQUEST['pes'];
    }else{
        @$periodi = $_POST['peri2'];
        @$TipoDC  = $_POST['TipoD'];
        @$FDEC    = $_POST['FD'];
        #@$Pmed    = $_POST['pes'];

        
    }

    @$i       = $_GET['I'];
    @$n       = $_GET['N'];
    @$per     = $_GET['per'];
    @$vig     = $_GET['vig'];
    @$reg     = $_GET['reg'];
    @$Xor     = $_GET['num'];
    @$cod_dec = $_GET['cod'];
    @$id_dec  = $_GET['id_dec'];
    @$dec     = $_GET['dec'];
    #@$Pmed    = $_POST['pes'];
    @$FechaDR = $_GET['fecDR'];

    $sql="SELECT    c.id_unico,
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
                    t.apellidodos)) AS nt, 
                    c.tercero,
                    t.numeroidentificacion,
                    ti.nombre,
                    d.direccion,
                    te.valor,
                    t.tipoidentificacion,
                    t.tiporegimen
        FROM gc_contribuyente c 
        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico=t.tipoidentificacion
        LEFT JOIN gf_direccion d ON d.tercero = t.id_unico
        LEFT JOIN gf_telefono te ON te.tercero = t.id_unico
        WHERE md5(c.id_unico)='$n' ";

   
    //contribuyente
    $resultado=$mysqli->query($sql);
    $rowC=mysqli_fetch_row($resultado);

    $D_Rela = "SELECT cod_dec FROM gc_declaracion WHERE contribuyente = ";
    $DDec = $mysqli->query($D_Rela);
    
    $idC = $rowC[0];
    $datosCont= $rowC[3].' - '.$rowC[1];
    $a = "none";
    if(empty($idC)){
        $tercero = "Número Identificación";    
    }else{
        $tercero = $datosCont;
        $a="inline-block";
    }
    
    $periodog = "SELECT id_unico, vigencia, mes FROM gc_anno_comercial WHERE md5(id_unico) = '$per'";
    $pgra = $mysqli->query($periodog);
    $pg = mysqli_fetch_row($pgra);
    
    $idP = $pg[0];
    $valorp = $pg[1];
    
    if(empty($idP)){
        $perG = "Periodo Gravable";
    }else{
        if($pg[2] == 0){
            $ME = "ANUAL";
        }elseif($pg[2] == 1){
            $ME = "ENERO";
        }elseif($pg[2] == 2){
            $ME = "FEBRERO";
        }elseif($pg[2] == 3){
            $ME = "MARZO";
        }elseif($pg[2] == 4){
            $ME = "ABRIL";
        }elseif($pg[2] == 5){
            $ME = "MAYO";
        }elseif($pg[2] == 6){
            $ME = "JUNIO";
        }elseif($pg[2] == 7){
            $ME = "JULIO";
        }elseif($pg[2] == 8){
            $ME = "AGOSTO";
        }elseif($pg[2] == 9){
            $ME = "SEPTIEMBRE";
        }elseif($pg[2] == 10){
            $ME = "OCTUBRE";
        }elseif($pg[2] == 11){
            $ME = "NOVIEMBRE";
        }elseif($pg[2] == 12){
            $ME = "DICEMBRE";
        }
        $perG = $valorp;
    }
    
    $vigenciaF = "SELECT id_unico, vigencia FROM gc_vigencia_comercial WHERE md5(id_unico) = '$vig'";
    $vfis = $mysqli->query($vigenciaF);
    $vf = mysqli_fetch_row($vfis);
    
    $idV = $vf[0];
    $valorV = $vf[1];
    
    if(empty($idV)){
        $ViF = "Vigencia Comercial";
    }else{
        $ViF = $valorV;
    }
    
    $identif = $rowC[7];
    if(empty($identif)){
        $ident = "";
    }else{
        $ident = $identif;
    }

    $regi = $rowC[8];
    if(empty($regi)){

        $reg = "";
    }else{
        $reg = $regi;
    }

    if(!empty($cod_dec)){
        $codi = $cod_dec;
    }else{
        $codi = "";
    }
    
    if(empty($FechaDR)){
        $hoy = date("d/m/Y");
        
    }else{
        $hoy = $FechaDR;
        
    }
    
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta class="viewport" content="width=device-width, initial-scale=1.0, minimun-scalable=1.0"></meta>
        <link rel="icon" href="img/AAA.ico" />
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" title="default" />
        <script src="js/jquery.min.js"></script>
        <script src="js/jquery-ui.js" type="text/javascript"></script>
        <script type="text/javascript" language="javascript" src="js/jquery-1.10.2.js"></script>
        <link rel="stylesheet" href="css/normalize.css"/>
        <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
        <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="js/dataTables.jqueryui.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
        <link rel="stylesheet" href="css/custom.css"/>
        <style>

            label #sltctai-error, #sltVig-error, #sltNumI-error, #reg-error,#txtnum-error, #txtnum-error {
                display: block;
                color: #155180;
                font-weight: normal;
                font-style: italic;
                font-size: 10px
            }
            /*Estilos tabla*/
            table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:13px}
            table.dataTable tbody td,table.dataTable tbody td{padding:1px}
            .dataTables_wrapper .ui-toolbar{padding:2px}
            /*Campos dinamicos*/
            .campoD:focus {
                border-color: #66afe9;
                outline: 0;            
                box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
            }
            .campoD:hover{
                cursor: pointer;
            }
            /*Campos dinamicos label*/
            .valorLabel{
                font-size: 13px;
            }
            .valorLabel:hover{
                cursor: pointer;
                color:#1155CC;
            }
            /*td de la tabla*/
            .campos{
                padding: 0px;
                font-size: 13px
            }
            /*cuerpo*/
            body{
                font-size: 13px;
                font-family: Arial;
            }

            .client-form input[type="text"]{
                width: 100%;
            }
            /*.client-form select{
                width: 100%;
            }

            .client-form input[type="file"]{
                width: 100%;
            }*/

            .titulo_h3{
                text-transform: uppercase;
                font-weight: 700;
            }
            
            .client-form input[type="text"]{
                width: 100%;
            }
            .client-form select{
                width: 100%;
            }

            .client-form input[type="file"]{
                width: 100%;
            }
            
            #nuestroSelector{
	           white-space:nowrap;
            }

            input[type="text"]{
            width: 100%;
            }
            #forma-Barra{
                color: #fff; 
                background: #00548F; 
                height: 50px; 
                width : 85%;
                padding: 6px; 
                border-radius:5px
            }
        </style>  

        <script src="js/md5.js"></script>
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
               
                $("#sltFechaDR").datepicker({changeMonth: true,}).val();
            });
        </script>

        <title>Reteica</title>

        <script src="dist/jquery.validate.js"></script>
        <!-- Librerias de carga para el datapicker -->
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!-- select2 -->
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    
    </head>
    
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
    <script src="js/md5.js"></script>

    
    <script type="text/javascript">
        var cr1=2;
        var ci=0;
        function addRow(){
            var table = document.getElementById("tablab");
            var row = table.insertRow(cr1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            var cell8 = row.insertCell(7);

            cell2.setAttribute("onclick","addInput(this,ci)");
            cell3.setAttribute("onclick","addInput(this,ci)");
            cell4.setAttribute("onclick","addInput(this,ci)");
            cell5.setAttribute("onclick","addInput(this,ci)");
            cell6.setAttribute("onclick","addInput(this,ci)");
            cell7.setAttribute("onclick","addInput(this,ci)");
            cell8.setAttribute("onclick","addInput(this,ci)");
            cell2.setAttribute("class","tLine");
            cell3.setAttribute("class","tLine");
            cell4.setAttribute("class","tLine");
            cell5.setAttribute("class","tLine");
            cell6.setAttribute("class","tLine");
            cell7.setAttribute("class","tLine");
            cell8.setAttribute("class","tLine");

            cr1++;
            // document.getElementById("p1").innerHTML = "New text!";
        }

        function closeInput(elm) {
            var td = elm.parentNode;
            var value = elm.value;
            td.removeChild(elm);
            td.innerHTML = value;
        }

        /*function addInput(elm,i) {
            if (elm.getElementsByTagName('input').length > 0) return;
                var value = elm.innerHTML;
                elm.innerHTML = '';
                var input = document.createElement('input');
                input.setAttribute('type', 'text');
                input.setAttribute('value', value);
                input.setAttribute('onBlur', 'closeInput(this)');
                input.setAttribute('name', i);
                elm.appendChild(input);
                input.focus();
                //alert(value);
        }*/

    </script>

    <body>   

        <div class="container-fluid text-center">
            <div class="row content">
                <div class="col-sm-2 col-md-2 col-lg-2 text-left"></div>
                <!--<div class="col-sm-2 col-md-2 col-lg-2"></div>-->
                <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                    <h3 id="forma-titulo3" align="center" style="width:85%; margin-top: 0px; margin-right: 4px; margin-left: 4px;">Declaración de Retención en la fuente de impuesto de Industria y Comercio, Avisos y Tableros</h3>
                     <a href="index2.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:80%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo "Datos del Contribuyente";?></h5>
                    <div class="client-form " style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" >
                            <p align="center" style="margin-bottom: 10px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            
                            <div class="form-inline">
                                
                                <input type="hidden" name="id" value="<?php echo $rowC[0] ?>">
                                <input type="hidden" name="PD" id="PD" value="<?php echo $periodi ?>">
                                <input type="hidden" name="TD" id="TD" value="<?php echo $TipoDC ?>">
                                <input type="hidden" name="Fec_D" id="Fec_D" value="<?php echo $FDEC ?>">
                                
                                <?php
                                    $periodoG = "SELECT ac.id_unico,ac.vigencia, ac.mes  FROM gc_anno_comercial ac ORDER BY ac.vigencia ASC";
                                    $rr = $mysqli->query($periodoG);
                                ?>
                                <label for="sltPer" class="col-sm-1 col-md-1 col-lg-1 control-label" ><strong style="color:#03C1FB;">*</strong>Periodo:</label>    
                                <div class="col-sm-2 col-md-2 col-lg-2" >
                                    <select name="sltPer" id="sltPer" class="form-control select2" title="Seleccione Periodo Gravable" style="height: 35px;" required>
                                        
                                        <?php
                                            echo "<option value=\"$idP\">$perG - $ME</option>";
                                            while($fa=mysqli_fetch_row($rr)){ 
                                                if($fa[2] == 0){
                                                    $ME = "ANUAL";
                                                }elseif($fa[2] == 1){
                                                    $ME = "ENERO";
                                                }elseif($fa[2] == 2){
                                                    $ME = "FEBRERO";
                                                }elseif($fa[2] == 3){
                                                    $ME = "MARZO";
                                                }elseif($fa[2] == 4){
                                                    $ME = "ABRIL";
                                                }elseif($fa[2] == 5){
                                                    $ME = "MAYO";
                                                }elseif($fa[2] == 6){
                                                    $ME = "JUNIO";
                                                }elseif($fa[2] == 7){
                                                    $ME = "JULIO";
                                                }elseif($fa[2] == 8){
                                                    $ME = "AGOSTO";
                                                }elseif($fa[2] == 9){
                                                    $ME = "SEPTIEMBRE";
                                                }elseif($fa[2] == 10){
                                                    $ME = "OCTUBRE";
                                                }elseif($fa[2] == 11){
                                                    $ME = "NOVIEMBRE";
                                                }elseif($fa[2] == 12){
                                                    $ME = "DICEMBRE";
                                                }
                                        ?>
                                                <option value="<?php echo $fa[0]; ?>"><?php echo $fa[1].' - '.$ME; ?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>

                                <label for="sltVig" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Vigencia:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2" > 
                                    <select name="sltVig" id="sltVig" required  class="form-control select2" title="Seleccione la Vigencia Fiscal" style="height: 35px;" required>
                                        
                                        <?php
                                            echo "<option value=\"$idV\">$ViF</option>";
                                            $vigenciaC="SELECT id_unico, vigencia FROM gc_vigencia_comercial  ORDER BY vigencia DESC";
                                            $res1=$mysqli->query($vigenciaC); 

                                            while($VC=mysqli_fetch_row($res1)){ ?>
                                                <option value="<?php echo $VC[0]?>"><?php echo $VC[1] ?></option>
                                        <?php 
                                            } 
                                        ?>                          
                                    </select>    
                                </div> 

                                <?php
                                    if(empty($idC)){
                                        $cont = "SELECT     c.id_unico,
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
                                                                    t.apellidodos)) AS nt, 
                                                                    c.tercero,
                                                                    t.numeroidentificacion,
                                                                    ti.nombre,
                                                                    c.codigo_mat
                                                        FROM gc_contribuyente c 
                                                        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                                                        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico=t.tipoidentificacion
                                                        WHERE c.estado = 1 ORDER BY c.codigo_mat ASC";
                                    }else{
                                        $cont = "SELECT     c.id_unico,
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
                                                                    t.apellidodos)) AS nt, 
                                                                    c.tercero,
                                                                    t.numeroidentificacion,
                                                                    ti.nombre,
                                                                    c.codigo_mat
                                                        FROM gc_contribuyente c 
                                                        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                                                        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico=t.tipoidentificacion
                                                        WHERE c.id_unico != '$idC' AND c.estado = 1 ORDER BY c.codigo_mat ASC";
                                    }
                                   

                                    $numI = $mysqli->query($cont);
                                ?>
                                <label for="sltNumI" class="control-label col-sm-1 col-md-1 col-lg-1" ><strong class="obligado">*</strong>Contribuyente:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <select name="sltNumI" id="sltNumI" class="form-control select2" title="Seleccione Contribuyente" required>
                                        
                                        <?php
                                            echo "<option value=\"$idC\">$tercero</option>";
                                            while($NI = mysqli_fetch_row($numI)){
                                                echo "<option value=\"$NI[0]\">$NI[5] - $NI[3] - $NI[1]</option>";
                                            }
                                        ?>
                                    </select>
                                </div>

                                 
                            </div>
                            
                            <div class="col-sm-10 col-md-10 col-lg-10 form-inline" style="margin-top: 10px;" >
                                <script type="text/javascript">
                                        $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        });
                                    </script>
                                    <label for="sltFechaDR"  type="date" class="col-sm-1 col-md-1 col-lg-1 control-label">
                                        <strong class="obligado"></strong>Fecha:
                                    </label>
                                    <div class="col-sm-2 col-md-2 col-lg-2" style="margin-left: 10px;">
                                        <input name="sltFechaDR" id="sltFechaDR" title="Ingrese Fecha " type="text" style="width: 190px;"  class="form-control col-sm-3 col-md-3 col-lg-3"   value="<?php echo $hoy ?>">
                                    </div>
                                                                        
                                    <div class="col-sm-4 col-md-4 col-lg-4" style="margin-left: 30px;">
                                        <label for="txtNumD"  type="date" class="col-sm-4 col-md-4 col-lg-4 control-label"><strong class="obligado"></strong>Código:</label>
                                        <input name="txtNumD" id="txtNumD" title="Ingrese Número de Declaración " type="text" style="width: auto;"  class="form-control">
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4" > 
                                        <a  type="button" id="btnGuardarTerCont" title="Resgistrar Contribuyente" onclick="RegistrarTerCont()" class="btn btn-primary shadow" tabindex="<?php echo $i ?>"><li class="glyphicon glyphicon-user"></li></a> 
                                    
                                    </div>
                                    
                            </div>
                            <?php
                                $Agravab = "SELECT vigencia FROM gc_anno_comercial WHERE id_unico = '$idP'";
                                $AGr = $mysqli->query($Agravab);
                                $AG = mysqli_fetch_row($AGr);

                                $Vigenc = "SELECT id_unico FROM gc_vigencia_comercial WHERE vigencia = '$AG[0]'";
                                $VComer = $mysqli->query($Vigenc);
                                $VC = mysqli_fetch_row($VComer);
                                
                                $actividad = "  SELECT ac.id_unico, ac.codigo , ac.descripcion, ta.tarifa, aco.fechainicio,aco.fechacierre FROM gc_actividad_comercial ac 
                                                            LEFT JOIN gc_actividad_contribuyente aco ON aco.actividad = ac.id_unico 
                                                            LEFT JOIN gc_contribuyente c ON aco.contribuyente = c.id_unico 
                                                            LEFT JOIN gc_tarifa_actividad ta ON ta.act_comer = ac.id_unico
                                                            WHERE md5(c.id_unico) = '$n'  AND ta.anno_grava = '$VC[0]'";

                                $acti = $mysqli->query($actividad);
                            ?>
                            <div class=" col-sm-10 col-md-10 col-lg-10 client-form contenedorForma" style="margin-top: 3%;"> 
                                    <h4 class="text-center titulo_h3">datos generales</h4> 
                                    <div class="form-group" style="margin-top: 1.2%;">
                                        <input type="hidden" name="id" value="<?php echo $rowC[0] ?>">
                                        
                                        <div class="form-group" style="margin-top: 5px; margin-left: 0.1px;">

                                            <label for="txtrazon" class="control-label col-sm-3 col-md-3 col-lg-3" >1. Nombres o Razón Social:</label>
                                            <div  class="col-sm-7 col-md-7 col-lg-7">   
                                                <input  type="text" name="txtrazon"  id="txtrazon" class="form-control" title="Contribuyente" placeholder="Contribuyente" value="<?php echo $rowC[1] ?>"   readonly>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="txtNumI" class="control-label col-sm-3 col-md-3 col-lg-3">2. Identifiación:</label>
                                            <div class="col-sm-3 col-md-3 col-lg-3">
                                                <input   type="text" name="txtNumI" id="txtNumI" class="form-control" title="Número de identificación" onkeypress="return txtValida(event,'num_car')" placeholder="Identificación" value="<?php echo $rowC[3] ?>" maxlength="15"  readonly>
                                            </div>

                                            <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >3. Telefono:</label>
                                            <div class="col-sm-2 col-md-2 col-lg-2">
                                                <input  type="text" name="telefono"  id="t" class="form-control" title="Telefono" placeholder="Telefono" value="<?php echo $rowC[6] ?>"  maxlength="15" readonly>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="txtDir" class="control-label col-sm-3 col-md-3 col-lg-3">4. Dirección:</label>
                                            <div class="col-sm-7 col-md-7 col-lg-7">
                                                <input   type="text" name="txtDir" id="txtDir" class="form-control" title="Número de identificación" onkeypress="return txtValida(event,'num_car')" placeholder="Identificación"  maxlength="15"  readonly>
                                            </div>
                                        </div>
                                        
                                        <label for="txtDir" class="control-label col-sm-3 col-md-3 col-lg-3">5. Actividad Principal:</label>
                                        <div class="col-sm-3 col-md-3 col-lg-3">
                                            <input   type="text" name="txtDir" id="txtDir" class="form-control" title="Número de identificación" onkeypress="return txtValida(event,'num_car')" placeholder="Identificación"  maxlength="15"  readonly>
                                        </div>
                                        
                                        <label for="txtDir" class="control-label col-sm-2 col-md-2 col-lg-2">6. N° Establecimientos:</label>
                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                            <input   type="text" name="txtDir" id="txtDir" class="form-control" title="Número de identificación" onkeypress="return txtValida(event,'num_car')" placeholder="Identificación"  maxlength="15"  readonly>
                                        </div>
                                        
                                        
                                         <?php
                                            $NE="";
                                            if(!empty($n)){
                                                $EST = "SELECT COUNT(id_unico) FROM gc_establecimiento WHERE md5(contribuyente) = '$n'";
                                                $ES = $mysqli->query($EST);
                                                $E = mysqli_fetch_row($ES);
                                                $NE = $E[0];
                                            }
                                        ?>

                                    </div>
    
                                   
                                </div>
                                   
                                <script src="js/md5.js"></script>
                                <script>
                                    $("#sltNumI").change(function(){
                
                                        //evento enter de numero identificaciong
                                        var fechaR     = $("#sltFechaDR").val();
                                        var NI    = $("#sltNumI").val();
                                        var per   = $('#sltPer').val();
                                        var vig   = $('#sltVig').val();
                                        var deco  = $('#txtnum').val();
                                        var peri1  = $('#PD').val();
                                        var TipoD = $('#TD').val();
                                        var FED = $('#Fec_D').val();
                                        var PMEDI = $("#PEM:checked").val();
                                        console.log(PMEDI);
                                        /*var reg = "";
                                        
                                        $('input[name="reg"]:checked').each(function() {
                                                reg = $(this).val();
                    
                                        });*/
                                    
                                        document.location = 'registrar_GC_ICA.php?fecDR='+fechaR+'&N='+md5(NI)+'&per='+md5(per)+'&vig='+md5(vig)+'&cod='+deco+'&peri2='+peri1+'&TipoD='+TipoD; 
                        
                                    });
            
                                </script>

                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <?php  
                                    //establecimientos
                                    $sqlee="SELECT  e.id_unico,
                                                e.nombre,
                                                DATE_FORMAT(e.fechainicioAct,'%d-%m-%Y') AS fechaFacConvertida,
                                                est.nombre,
                                                e.direccion,
                                                e.cod_catastral,
                                                ciu.nombre,
                                                b.nombre,
                                                l.nombre,
                                                te.nombre,
                                                tame.nombre
                                        FROM gc_establecimiento e
                                        LEFT JOIN gc_contribuyente c ON c.id_unico=e.contribuyente
                                        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                                        LEFT JOIN gp_estrato est ON est.id_unico=e.estrato
                                        LEFT JOIN gf_ciudad ciu ON ciu.id_unico=e.ciudad
                                        LEFT JOIN gp_barrio b ON b.id_unico=e.barrio
                                        LEFT JOIN gc_localizacion l ON l.id_unico=e.localizacion
                                        LEFT JOIN gf_tipo_entidad te ON te.id_unico=e.tipo_entidad
                                        LEFT JOIN gc_tamanno_entidad tame ON tame.id_unico=e.tamanno_entidad
                                        WHERE md5(c.id_unico)='$n'";
                                
                                    $resultadoee=$mysqli->query($sqlee);
                                ?>
                                <br>
                                
                                        
                                    
                                        <!--2da table Trasnportadores publicos urbanos-->
                                        <?php 
                                            $sqlBG="SELECT cc.id_unico,cc.descripcion,tc.nombre, cc.apli_descu, cc.apli_inte, cc.codigo, cc.tipo_ope FROM gc_concepto_comercial cc 
                                                LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                                                WHERE tc.id_unico = 6"; 

                                            $qBG=$mysqli->query($sqlBG);
                                            $inputsValue=0;  //contador inputs value

                                        ?>                                        
                                        <!--4to Base Gravable -->
                                        <?php
                                        #$X = 7;
                                        ?>
                                        <div class="col-sm-10 col-md-10 col-lg-10  text-left">
                                            <div class="table-responsive" style="margin-top:-10px;">
                                                <div class="table-responsive">
                                                    <table  class="table table-bordered"  cellspacing="0" width="100%">
                                                        
                                                        <thead>
                                                            <tr>
                                                                <th colspan="5" style="text-align: center">APROXIME LOS VALORES AL MULTIPLO DE MIL MAS CERCANO Y NO ESCRIBA CENTAVOS</th>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <th></th>

                                                                <th colspan="1">C.  RETENCION A TITULO DEL IMPUESTO DE INDUSTRIA Y COMERCIO Y COMPLEMENTARIOS</th>
                                                                <th>VALOR</th>
                                                                <th>AUTOLIQUIDACION</th>
                                                                <th>DIFERENCIA</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>     
                                                            <?php
                                                                $Agravab = "SELECT vigencia, mes FROM gc_anno_comercial WHERE id_unico = '$idP'";
                                                                $AGr = $mysqli->query($Agravab);
                                                                $AG = mysqli_fetch_row($AGr);
                                                                
                                                                $AperG = "SELECT vigencia FROM gc_vigencia_comercial WHERE id_unico = '$idV'";
                                                                $APGr = $mysqli->query($AperG);
                                                                $APG = mysqli_fetch_row($APGr);
                                                                
                                                                $vence = "SELECT fecha FROM  gr_vencimiento WHERE anno = '$AG[0]' AND mes = '$AG[1]' AND tipo = 5";
                                                                $VenceDR = $mysqli->query($vence);
                                                                $NVDR = mysqli_num_rows($VenceDR);
                                                                if($NVDR > 0){
                                                                    $VDR = mysqli_fetch_row($VenceDR);
                                                                }
                                                                
                                                                
                                                                
                                                                
                                                                #$i = 0; 
                                                                while($rowBG=mysqli_fetch_row($qBG)){ 
                                                                    
                                                                    $Vconp = "SELECT valor, concepto FROM gc_valor_concepto WHERE concepto = '$rowBG[0]' AND anno_grava = '$idV'";
                                                                    $VConce = $mysqli->query($Vconp);
                                                                    $VCon = mysqli_fetch_row($VConce);
                                                            ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php
                                                                                echo $rowBG[5];
                                                                            ?>
                                                                        </td>
                                                                        
                                                                        <!--id_concepto Comercial-->
                                                                        <?php  
                                                                            $inputsValue++;
                                                                            $idInputCC="idInputCC".$inputsValue;
                                                                            $nameIdConceptoComercial="iidConceptoComercial".$inputsValue;
                                                                            $apdes = "apdes".$inputsValue;
                                                                            $apint = "apint".$inputsValue;
                                                                            $ValC = "ValC".$inputsValue;
                                                                            $indi = "indi".$inputsValue;

                                                                        ?>
                                                                        <input id="<?php echo $indi ?>" name="<?php echo $indi ?>" type="hidden" value="<?php echo $rowLP[6]; ?>" >
                                                                        <input id="<?php echo $idInputCC ?>" name="<?php echo $nameIdConceptoComercial ?>" type="hidden" value="<?php echo $rowBG[0]; ?>" >
                                                                        <input id="<?php echo $ValC?>" name="<?php echo $ValC ?>" type="hidden" value="<?php echo $VCon[0] ?>" >
                                                                        <input id="<?php echo $apdes ?>" name="<?php echo $apdes ?>" type="hidden" value="<?php echo $rowBG[3]; ?>" >
                                                                        <input id="<?php echo $apint ?>" name="<?php echo $apint ?>" type="hidden" value="<?php echo $rowBG[4]; ?>" >


                                                                        <input id="<?php echo "txtConcepto".$inputsValue; ?>" name="<?php echo "txtConcepto".$inputsValue; ?>" type="hidden" value="<?php echo $rowBG[0]; ?>" >
                                                                        <!--descripcion concepto comercial-->
                                                                        <?php 
                                                                            $idInputD="idInputD".$inputsValue;
                                                                            $nameDescripcion="iDescripcion".$inputsValue ?>
                                                                            <input id="<?php echo $idInputD ?>" name="<?php echo $nameDescripcion ?>" type="hidden" value="<?php echo $rowBG[1]; ?>" >
                                                                            <td style="width: 60%"><?php echo ucwords(mb_strtolower($rowBG[1])) ?></td>
                                                                            <!--value detalle declaracion-->
                                                                        <?php
                                                                            $idInputV="idInputV".$inputsValue;
                                                                            $nameValue="iValue".$inputsValue; 
                                                                            $autoV = "autoV".$inputsValue;
                                                                            $dif = "dif".$inputsValue; //
                                                                            $i = $inputsValue;
                                                                            $nameAut = "idAutoL".$inputsValue;
                                                                            
                                                                        ?>
                                                                        <style type='text/css'> 
                                                                            #<?php echo $idInputV ?> , #<?php echo $autoV ?>, #<?php echo $dif ?> {
                                                                                 text-align:right;
                                                                                 font-weight: bold;
                                                                            } 
                                                                        </style>
                                                                        <td>
                                                                            <input id="<?php echo $idInputV ?>" name="<?php echo $nameValue ?>" value="" style="width: 100%" type="text" tabindex="<?php echo $i ?>" onkeyup="formatC('<?php echo $idInputV ?>');" >
                                                                        </td>
                                                                        <!--<td dir="ltr" id="test1" class="tLine" nowrap onclick="addInput(this)"></td>-->
                                                                        <td><input id="<?php echo $autoV ?>" name="<?php echo $nameAut ?>" value="" style="width: 100%" type="text" readonly></td>
                                                                        <td><input id="<?php echo $dif ?>" name="" value="" style="width: 100%" type="text" readonly></td>
                                                                    </tr>
                                                            <?php
                                                                }

                                                            ?>
                                                            <input id="txtFechaVenPer" name="txtFechaVenPer" type="hidden" value="<?php echo $VDR[0]; ?>" >        
                                                           

                                                                 
                                                              
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <?php 
                                            $sqlLP="SELECT cc.id_unico,cc.descripcion,tc.nombre,cc.apli_descu, cc.apli_inte, cc.codigo, cc.tipo_ope  FROM gc_concepto_comercial cc 
                                                LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                                                WHERE tc.id_unico = 7 ORDER BY cc.codigo ASC"; 

                                            $qLP=$mysqli->query($sqlLP);

                                            $X = 14;
                                            
                                            #$mm = 0;
                                        ?>
                                                                               
                                        <!--5to Liquidación privada -->
                                        <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: 0px;" >
                                            <div class="table-responsive" >
                                                <div class="table-responsive">
                                                    <table  class="table table-bordered"  cellspacing="0" width="100%">
                                                        <thead>
                                                            <th colspan="2">D. VALOR PAGADO</th>
                                                            <th>VALOR</th>
                                                            <th>AUTOLIQUIDACION</th>
                                                            <th>DIFERENCIA</th>
                                                        </thead>  
                                                        <tbody>
                                                            <?php 

                                                                $AperG = "SELECT vigencia FROM gc_anno_comercial WHERE id_unico = '$idP'";
                                                                $APGr = $mysqli->query($AperG);
                                                                $APG = mysqli_fetch_row($APGr);

                                                                $VGCom = "SELECT id_unico FROM gc_vigencia_comercial WHERE vigencia = '$APG[0]'";
                                                                $AAAA = $mysqli->query($VGCom);
                                                                $V = mysqli_fetch_row($AAAA);

                                                                while($rowLP=mysqli_fetch_row($qLP)){ 
                                                                    
                                                                    $Vconp = "SELECT valor, concepto FROM gc_valor_concepto WHERE concepto = '$rowLP[0]' AND anno_grava = '$V[0]'";
                                                                    $VConce = $mysqli->query($Vconp);
                                                                    $VCon = mysqli_fetch_row($VConce);

                                                            ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo $rowLP[5]; ?>
                                                                        </td>
                                                                        <!--id_concepto Comercial-->
                                                                        <?php  
                                                                            $inputsValue++;
                                                                            $idInputCC="idInputCC".$inputsValue;
                                                                            $nameIdConceptoComercial="iidConceptoComercial".$inputsValue ;
                                                                            $ValC = "ValC";
                                                                        ?>

                                                                        <input id="<?php echo $idInputCC ?>" name="<?php echo $nameIdConceptoComercial ?>" type="hidden" value="<?php echo $rowLP[0] ?>" >

                                                                        <input id="<?php echo $ValC.$rowLP[5] ?>" name="<?php echo $ValC.$rowLP[5] ?>" type="hidden" value="<?php echo $VCon[0] ?>" >
                                                                        <!--descripcion concepto comercial-->
                                                                        <?php 
                                                                            $idInputD="idInputD".$inputsValue;
                                                                            $nameDescripcion="iDescripcion".$inputsValue ?>
                                                                            <input id="<?php echo $idInputD ?>" name="<?php echo $nameDescripcion ?>" type="hidden" value="<?php echo $rowLP[1] ?>" >
                                                                            <?php   if($idInputD == "idInputD20"){ ?>
                                                                                        
                                                                                        <td style="width:  15%"><?php echo ucwords(mb_strtolower($rowLP[1])) ?></td>
                                                                                        
                                                                            <?php   }else{ ?>
                                                                                        <td type="checkbox" style="width: 60%"><?php echo ucwords(mb_strtolower($rowLP[1])) ?></td>

                                                                            <?php   }

                                                                            $idInputV   = "idInputV".$inputsValue;  
                                                                            $nameValue  = "iValue".$inputsValue;
                                                                            $autoV      = "autoV".$inputsValue;
                                                                            $dif        = "dif".$inputsValue;  //
                                                                            $apdes      = "apdes".$inputsValue;
                                                                            $apint      = "apint".$inputsValue;
                                                                            $indi       = "indi".$inputsValue;
                                                                            $nameAut    = "idAutoL".$inputsValue;
                                                                            $i          = $inputsValue;
                                                                            
                                                                        ?>
                                                                        <style type='text/css'> 
                                                                            #<?php echo $idInputV ?> , #<?php echo $autoV ?>, #<?php echo $dif ?> {
                                                                                 text-align:right;
                                                                                 font-weight: bold;
                                                                            } 
                                                                        </style>
                                                                        <input id="<?php echo $indi ?>" name="<?php echo $indi ?>" type="hidden" value="<?php echo $rowLP[6]; ?>" >
                                                                        <input id="<?php echo $apdes ?>" name="<?php echo $apdes ?>" type="hidden" value="<?php echo $rowLP[3]; ?>" >
                                                                        <input id="<?php echo $apint ?>" name="<?php echo $apint ?>" type="hidden" value="<?php echo $rowLP[4]; ?>" >
                                                                        <input id="<?php echo "txtConcepto".$inputsValue; ?>" name="<?php echo "txtConcepto".$inputsValue; ?>" type="hidden" value="<?php echo $rowLP[0]; ?>"  >
                                                                        <td><input id="<?php echo $idInputV ?>" name="<?php echo $nameValue ?>" value="" style="width: 100%" type="text" tabindex="<?php echo $i ?>"  onkeyup="formatC('<?php echo $idInputV ?>');"></td>
                                                                        <td><input id="<?php echo $autoV ?>" name="<?php echo $nameAut ?>" value="" style="width: 100%" type="text" readonly></td>
                                                                        <td><input id="<?php echo $dif ?>" name="" value="" style="width: 100%" type="text" readonly></td>
                                                                    </tr>
                                                            <?php 
                                                                }
                                                            ?>
                                                            <input id="difT1" name="difT1" type="hidden" value="" >
                                                            <input id="difT2" name="difT2" type="hidden" value="" >
                                                            <script>
                                                                $("#idInputV1").blur(function(){
                                                                    var x1 = ($("#idInputV1").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    
                                                                    document.getElementById('autoV1').value=formatV(nx1);
                                                                    
                                                                });
                                                                
                                                                $("#idInputV2").blur(function(){
                                                                    var x1 = ($("#idInputV2").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    
                                                                    document.getElementById('autoV2').value=formatV(nx1);
                                                                    
                                                                });
                                                                
                                                                $("#idInputV3").blur(function(){
                                                                    var x1 = ($("#idInputV3").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    
                                                                    document.getElementById('autoV3').value=formatV(nx1);
                                                                    
                                                                });
                                                                
                                                                $("#idInputV4").blur(function(){
                                                                    var x1 = ($("#idInputV4").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    
                                                                    document.getElementById('autoV4').value=formatV(nx1);
                                                                    
                                                                });
                                                                $("#idInputV4").blur(function(){
                                                                    var x1 = ($("#idInputV1").val());
                                                                    var x2 = ($("#idInputV2").val());
                                                                    var x3 = ($("#idInputV3").val());
                                                                    var x4 = ($("#idInputV4").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    var nx2 = parseFloat(x2.replace(/\,/g, ''));
                                                                    var nx3 = parseFloat(x3.replace(/\,/g, ''));
                                                                    var nx4 = parseFloat(x4.replace(/\,/g, ''));
                                                                    
                                                                    var T = nx1 + nx2 + nx3 + nx4;
                                                                    var dif = 0;
                                                                    console.log(T);
                                                                    document.getElementById('idInputV5').value=formatV(T);
                                                                    document.getElementById('autoV5').value=formatV(T);
                                                                    document.getElementById('dif5').value= dif; 
                                                                });
                                                                
                                                                $("#idInputV5").change(function(){
                                                                    var x1 = ($("#idInputV5").val());
                                                                    var x2 = ($("#autoV5").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    var nx2 = parseFloat(x2.replace(/\,/g, ''));
                                                                    
                                                                    var dif = nx1 - nx2;
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                    console.log("diferencia retencion: "+dif);
                                                                    document.getElementById('dif5').value= formatV(dif); 
                                                                });
                                                                
                                                                $("#idInputV5").blur(function(){
                                                                    
                                                                    var x1 = ($("#txtFechaVenPer").val());
                                                                    var x2 = ($("#sltFechaDR").val());
                                                                    
                                                                    var x3 =  x2.split('/').reverse().join('-');
                                                                    if(x1 < x3){
                                                                       var san = ($("#ValC6").val());
                                                                    }else{
                                                                        var san = 0;
                                                                    }
                                                                    
                                                                    var dif = 0;
                                                                    console.log("fecha vence: "+x1);
                                                                    console.log("fecha dec: "+x3);
                                                                    document.getElementById('idInputV6').value=formatV(san);
                                                                    document.getElementById('autoV6').value=formatV(san);
                                                                    document.getElementById('dif6').value= dif; 
                                                                });

                                                                $("#idInputV6").change(function(){
                                                                    var x1 = ($("#idInputV6").val());
                                                                    var x2 = ($("#autoV6").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    var nx2 = parseFloat(x2.replace(/\,/g, ''));
                                                                    
                                                                    var dif = nx1 - nx2;
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                    console.log("diferencia retencion: "+dif);
                                                                    document.getElementById('dif6').value= formatV(dif); 
                                                                });
                                                                
                                                                $("#idInputV6").blur(function(){
                                                                    var x1 = ($("#idInputV6").val());
                                                                    var x2 = ($("#idInputV5").val());
                                                                    var x3 = ($("#autoV6").val());
                                                                    var x4 = ($("#autoV5").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    var nx2 = parseFloat(x2.replace(/\,/g, ''));
                                                                    var nx3 = parseFloat(x3.replace(/\,/g, ''));
                                                                    var nx4 = parseFloat(x4.replace(/\,/g, ''));
                                                                    
                                                                    var T = nx1 + nx2;
                                                                    var T1 = nx3 + nx4;
                                                                    var dif = T - T1;
                                                                    
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                    document.getElementById('idInputV7').value=formatV(T);
                                                                    document.getElementById('autoV7').value=formatV(T1);
                                                                    document.getElementById('dif7').value= formatV(dif); 
                                                                });

                                                                $("#idInputV7").change(function(){
                                                                    var x1 = ($("#idInputV7").val());
                                                                    var x2 = ($("#autoV7").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    var nx2 = parseFloat(x2.replace(/\,/g, ''));

                                                                    var dif = nx1 - nx2 
                                                                    
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                    
                                                                    document.getElementById('dif7').value= formatV(dif); 
                                                                });
                                                                
                                                                $("#idInputV7").blur(function(){
                                                                    var x1 = ($("#idInputV5").val());
                                                                    var x2 = ($("#idInputV6").val());
                                                                    
                                                                    var x3 = ($("#autoV5").val());
                                                                    var x4 = ($("#autoV6").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    var nx2 = parseFloat(x2.replace(/\,/g, ''));
                                                                    
                                                                    var nx3 = parseFloat(x3.replace(/\,/g, ''));
                                                                    var nx4 = parseFloat(x4.replace(/\,/g, ''));

                                                                    var dif = nx1 - nx3;
                                                                    var dif1 = nx2 - nx4;
                                                                    
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                    if(dif1 < 0){
                                                                        dif1 = dif1 * -1;
                                                                    }
                                                                    document.getElementById('idInputV8').value=formatV(nx1);
                                                                    document.getElementById('autoV8').value=formatV(nx3);
                                                                    document.getElementById('dif8').value= formatV(dif);
                                                                    
                                                                    document.getElementById('idInputV9').value=formatV(nx2);
                                                                    document.getElementById('autoV9').value=formatV(nx4);
                                                                    document.getElementById('dif9').value= formatV(dif1);
                                                                });
                                                                
                                                                
                                                                $("#idInputV8").change(function(){
                                                                    var x1 = ($("#idInputV8").val());
                                                                    var x2 = ($("#autoV8").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    var nx2 = parseFloat(x2.replace(/\,/g, ''));

                                                                    var dif = nx1 - nx2 
                                                                    
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                    console.log("diferencia renglon 14: "+dif);
                                                                    document.getElementById('dif8').value= formatV(dif); 
                                                                });

                                                                    
                                                              
                                                            </script>  

                                                            <script>
                                                                $("#idInputV9").blur(function(){
                                                                    
                                                                    var apli1 = $("#apint1").val();
                                                                    if(apli1 != ""){
                                                                        var ooo1  = ($("#idInputV1").val());
                                                                        var xooo1 = parseFloat(ooo1.replace(/\,/g, ''));
                                                                        var aut1  = $("#autoV1").val();
                                                                        var ind   = parseFloat($("#indi1").val());
                                                                        if(ind == 3){
                                                                            xooo1 = xooo1 * -1;
                                                                            
                                                                            if(aut1 =="" || aut1 ==" "){
                                                                                xaut1 = 0;
                                                                            }else{
                                                                                var aut1  = ($("#autoV1").val());
                                                                                var xaut1 = parseFloat(aut1.replace(/\,/g, ''));
                                                                                xaut1 = xaut1 * -1;
                                                                            }
                                                                        }else{
                                                                            
                                                                            if(aut1 =="" || aut1 ==" "){
                                                                                xaut1 = 0;
                                                                            }else{
                                                                               var xaut1 = parseFloat(aut1.replace(/\,/g, '')); 
                                                                            }
                                                                           
                                                                        }
                                                                    }else{
                                                                        var xooo1 = 0;
                                                                        var xaut1 = 0; 
                                                                    }

                                                                    var apli2 = $("#apint2").val();
                                                                    if(apli2 != ""){
                                                                        var ooo2  = ($("#idInputV2").val());
                                                                        var xooo2 = parseFloat(ooo2.replace(/\,/g, ''));
                                                                        var aut2  = $("#autoV2").val();
                                                                        var ind    = parseFloat($("#indi2").val());
                                                                        if(ind == 3){
                                                                            xooo2 = xooo2 * -1;
                                                                            
                                                                            if(aut2 =="" || aut2 ==" "){
                                                                                xaut2 = 0;
                                                                            }else{
                                                                                var aut2  = ($("#autoV2").val());
                                                                                var xaut2 = parseFloat(aut2.replace(/\,/g, ''));
                                                                                xaut2  = xaut2 * -1;
                                                                            }
                                                                        }else{
                                                                            
                                                                            if(aut2 =="" || aut2 ==" "){
                                                                                xaut2 = 0;
                                                                            }else{
                                                                               var xaut2 = parseFloat(aut2.replace(/\,/g, '')); 
                                                                            }
                                                                        }
                                                                    }else{
                                                                        var xooo2 = 0;
                                                                        var xaut2 = 0; 
                                                                    }

                                                                    var apli3 = $("#apint3").val();
                                                                    if(apli3 != ""){
                                                                        var ooo3  = ($("#idInputV3").val());
                                                                        var xooo3 = parseFloat(ooo3.replace(/\,/g, ''));
                                                                        var aut3  = $("#autoV3").val();
                                                                        var ind    = parseFloat($("#indi3").val());
                                                                        if(ind == 3){
                                                                            xooo3 = xooo3 * -1;
                                                                            
                                                                            if(aut3 =="" || aut3 ==" "){
                                                                                xaut3 = 0;
                                                                            }else{
                                                                                var aut3  = ($("#autoV11").val());
                                                                                var xaut3 = parseFloat(aut3.replace(/\,/g, ''));
                                                                                xaut3  = xaut3 * -1;
                                                                            }
                                                                        }else{
                                                                            
                                                                            if(aut3 =="" || aut3 ==" "){
                                                                                xaut3 = 0;
                                                                            }else{
                                                                               var xaut3 = parseFloat(aut3.replace(/\,/g, '')); 
                                                                            }
                                                                        }
                                                                    }else{
                                                                        var xooo3 = 0;
                                                                        var xaut3 = 0; 
                                                                    }

                                                                    var apli4 = $("#apint4").val();
                                                                    if(apli4 != ""){
                                                                        var ooo4  = ($("#idInputV4").val());
                                                                        var xooo4 = parseFloat(ooo4.replace(/\,/g, ''));
                                                                        var aut4  = $("#autoV4").val();
                                                                        var ind    = parseFloat($("#indi4").val());
                                                                        if(ind == 3){
                                                                            xooo4 = xooo4 * -1;
                                                                            
                                                                            if(aut4 =="" || aut4 ==" "){
                                                                                xaut4 = 0;
                                                                            }else{
                                                                                var aut4  = ($("#autoV4").val());
                                                                                var xaut4 = parseFloat(aut4.replace(/\,/g, ''));
                                                                                xaut4  = xaut4 * -1;
                                                                            }
                                                                        }else{
                                                                            
                                                                            if(aut4 =="" || aut4 ==" "){
                                                                                xaut4 = 0;
                                                                            }else{
                                                                               var xaut4 = parseFloat(aut4.replace(/\,/g, '')); 
                                                                            }
                                                                        }
                                                                    }else{
                                                                        var xooo4 = 0;
                                                                        var xaut4 = 0; 
                                                                    }

                                                                    var apli5 = $("#apint5").val();
                                                                    if(apli5 != ""){
                                                                        var ooo5  = ($("#idInputV5").val());
                                                                        var xooo5 = parseFloat(ooo5.replace(/\,/g, ''));
                                                                        var aut5  = $("#autoV5").val();
                                                                        var ind    = parseFloat($("#indi5").val());
                                                                        if(ind == 3){
                                                                            xooo5 = xooo5 * -1;
                                                                           
                                                                            if(aut5 =="" || aut5 ==" "){
                                                                                xaut5 = 0;
                                                                            }else{
                                                                                var aut5  = ($("#autoV5").val());
                                                                                var xaut5 = parseFloat(aut5.replace(/\,/g, ''));
                                                                                xaut5  = xaut5 * -1;
                                                                            }
                                                                        }else{
                                                                            
                                                                            if(aut5 =="" || aut5 ==" "){
                                                                                xaut5 = 0;
                                                                            }else{
                                                                               var xaut5 = parseFloat(aut5.replace(/\,/g, '')); 
                                                                            }
                                                                        }
                                                                    }else{
                                                                        var xooo5 = 0;
                                                                        var xaut5 = 0; 
                                                                    }

                                                                    var apli6 = $("#apint6").val();
                                                                    if(apli6 != ""){
                                                                        var ooo6  = ($("#idInputV6").val());
                                                                        var xooo6 = parseFloat(ooo6.replace(/\,/g, ''));
                                                                        var aut6  = $("#autoV6").val();
                                                                        var ind = parseFloat($("#indi6").val());
                                                                        if(ind == 3){
                                                                            xooo6 = xooo6 * -1;
                                                                            
                                                                            if(aut6 =="" || aut6 ==" "){
                                                                                xaut6 = 0;
                                                                            }else{
                                                                                var aut6  = ($("#autoV6").val());
                                                                                var xaut6 = parseFloat(aut6.replace(/\,/g, ''));
                                                                                xaut6  = xaut6 * -1;
                                                                            }
                                                                        }else{
                                                                            if(aut6 =="" || aut6 ==" "){
                                                                                xaut6 = 0;
                                                                            }else{
                                                                               var xaut6 = parseFloat(aut6.replace(/\,/g, '')); 
                                                                            }
                                                                        }
                                                                    }else{
                                                                        var xooo6 = 0;
                                                                        var xaut6 = 0; 
                                                                    }

                                                                    var apli7 = $("#apint7").val();
                                                                    if(apli7 != ""){
                                                                        var ooo7  = ($("#idInputV7").val());
                                                                        var xooo7 = parseFloat(ooo7.replace(/\,/g, ''));
                                                                        var aut7  = $("#autoV7").val();
                                                                        var ind    = parseFloat($("#indi7").val());
                                                                        if(ind == 3){
                                                                            xooo7 = xooo7 * -1;
                                                                            
                                                                            if(aut7 =="" || aut7 ==" "){
                                                                                xaut7 = 0;
                                                                            }else{
                                                                                var aut7  = ($("#autoV7").val());
                                                                                var xaut7 = parseFloat(aut7.replace(/\,/g, ''));
                                                                                xaut7  = xaut7 * -1;
                                                                            }
                                                                        }else{
                                                                            if(aut7 =="" || aut7 ==" "){
                                                                                xaut7 = 0;
                                                                            }else{
                                                                               var xaut7 = parseFloat(aut7.replace(/\,/g, '')); 
                                                                            }
                                                                        }
                                                                    }else{
                                                                        var xooo7 = 0;
                                                                        var xaut7 = 0; 
                                                                    }

                                                                    var apli8 = $("#apint8").val();
                                                                    if(apli8 != ""){
                                                                        var ooo8  = ($("#idInputV8").val());
                                                                        var xooo8 = parseFloat(ooo8.replace(/\,/g, ''));
                                                                        var aut8  = $("#autoV8").val();
                                                                        var ind    = parseFloat($("#indi8").val());
                                                                        if(ind == 3){
                                                                            xooo8 = xooo8 * -1;
                                                                            
                                                                            if(aut8 == "" || aut8 == " "){
                                                                                xaut8 = 0;
                                                                            }else{
                                                                                var aut8  = ($("#autoV8").val());
                                                                                var xaut8 = parseFloat(aut8.replace(/\,/g, ''));
                                                                                xaut8  = xaut8 * -1;
                                                                            }
                                                                            
                                                                        }else{
                                                                            if(aut8 =="" || aut8 ==" "){
                                                                                xaut8 = 0;
                                                                            }else{
                                                                               var xaut8 = parseFloat(aut8.replace(/\,/g, '')); 
                                                                            }
                                                                        }
                                                                    }else{
                                                                        var xooo8 = 0;
                                                                        var xaut8 = 0; 
                                                                    }
                                                                    
                                                                    var apli9 = $("#apint9").val();
                                                                    if(apli9 != ""){
                                                                        var ooo9  = ($("#idInputV9").val());
                                                                        var xooo9 = parseFloat(ooo9.replace(/\,/g, ''));
                                                                        var aut9  = $("#autoV9").val();
                                                                        var ind    = parseFloat($("#indi9").val());
                                                                        if(ind == 3){
                                                                            xooo9 = xooo9 * -1;
                                                                            
                                                                            if(aut9 == "" || aut9 == " "){
                                                                                xaut9 = 0;
                                                                            }else{
                                                                                var aut9  = ($("#autoV9").val());
                                                                                var xaut9 = parseFloat(aut9.replace(/\,/g, ''));
                                                                                xaut9  = xaut9 * -1;
                                                                            }
                                                                            
                                                                        }else{
                                                                            if(aut9 =="" || aut9 ==" "){
                                                                                xaut9 = 0;
                                                                            }else{
                                                                               var xaut9 = parseFloat(aut9.replace(/\,/g, '')); 
                                                                            }
                                                                        }
                                                                    }else{
                                                                        var xooo9 = 0;
                                                                        var xaut9 = 0; 
                                                                    }

                                                                    
                                                                    var TLII = xooo1 + xooo2 + xooo3 + xooo4 + xooo5 + xooo6 + xooo7 + xooo8 + xooo9;

                                                                    var TLIIA = xaut1 + xaut2 + xaut3 + xaut4 + xaut5 + xaut6 + xaut7 + xaut8 + xaut9; 
                                                                    
                                                                    var x1 = ($("#txtFechaVenPer").val());
                                                                    var x2 = ($("#sltFechaDR").val());
                                                                    
                                                                    var x3 =  x2.split('/').reverse().join('-');
                                                                    if(x1 < x3){
                                                                        $.ajax({
                                                                            url: 'funciones/Int_Desc_Industria.php',
                                                                            type: 'POST',                    
                                                                            data: {
                                                                                'bandera':'int',
                                                                                'fechaD': x2,
                                                                                'valor': TLII,
                                                                                'fechaVen': x1



                                                                            },

                                                                            success: function(data){
                                                                                console.log("valor interes:"+data);
                                                                                result = data.trim();
                                                                                
                                                                                if(result == 0){
                                                                                    document.getElementById("idInputV10").value=0;
                                                                                    //document.getElementById("autoV10").value=0; 
                                                                                    document.getElementById("difT1").value=0;
                                                                                }else if((result > 0)&&(result < 500)){
                                                                                    result = Math.ceil(result / 1000) * 1000;
                                                                                    document.getElementById("idInputV10").value=formatV(result);
                                                                                    //document.getElementById("autoV10").value=formatV(result); 
                                                                                    document.getElementById("difT1").value=result; 
                                                                                }else{
                                                                                    result = Math.round(result / 1000) * 1000;
                                                                                    document.getElementById("idInputV10").value=formatV(result);
                                                                                    //document.getElementById("autoV10").value=formatV(result); 
                                                                                    document.getElementById("difT1").value=result;
                                                                                }
                                                                            }
                          
                                                                        });

                                                                        $.ajax({
                                                                            url: 'funciones/Int_Desc_Industria.php',
                                                                            type: 'POST',                    
                                                                            data: {
                                                                                'bandera':'int',
                                                                                'fechaD': x2,
                                                                                'valor': TLIIA ,
                                                                                'fechaVen': x1

                                                                            },

                                                                            success: function(data){
                                                                                console.log("valor interes:"+data);
                                                                                result1 = data.trim();
                                                                                
                                                                                if(result1 == 0){
                                                                                    //document.getElementById("idInputV28").value=0;
                                                                                    document.getElementById("autoV10").value=0; 
                                                                                    document.getElementById("difT2").value=0;
                                                                                }else if((result1 > 0)&&(result1 < 500)){
                                                                                    result1 = Math.ceil(result1 / 1000) * 1000;
                                                                                    //document.getElementById("idInputV28").value=result;
                                                                                    document.getElementById("autoV10").value=formatV(result1); 
                                                                                    document.getElementById("difT2").value=result1; 
                                                                                }else{
                                                                                    result1 = Math.round(result1 / 1000) * 1000;
                                                                                    //document.getElementById("idInputV28").value=result;
                                                                                    document.getElementById("autoV10").value=formatV(result1); 
                                                                                    document.getElementById("difT2").value=result1;
                                                                                }
                                                                            }

                                                                        });
                                                                        
                                                                        
                                                                        
                                                                    }else{
                                                                        document.getElementById("idInputV10").value=0;
                                                                        document.getElementById("autoV10").value=0; 
                                                                        document.getElementById("dif10").value=0;
                                                                    }
                                                                    
                                                                    console.log("base Int Auto: "+TLIIA);
                                                                    console.log("aut1 I: "+xaut1);
                                                                    console.log("aut2 I: "+xaut2);
                                                                    console.log("aut3 I: "+xaut3);
                                                                    console.log("aut4 I: "+xaut4);
                                                                    console.log("aut5 I: "+xaut5);
                                                                    console.log("aut6 I: "+xaut6);
                                                                    console.log("aut7 I: "+xaut7);
                                                                    console.log("aut8 I: "+xaut8);
                                                                    console.log("aut9 I: "+xaut9);
                                                                    
                                                                    console.log("base Int Des: "+TLII);
                                                                    console.log("ooo1: "+xooo1);
                                                                    console.log("ooo2: "+xooo2);
                                                                    console.log("ooo3: "+xooo3);
                                                                    console.log("ooo4: "+xooo4);
                                                                    console.log("ooo5: "+xooo5);
                                                                    console.log("ooo6: "+xooo6);
                                                                    console.log("ooo7: "+xooo7);
                                                                    console.log("ooo8: "+xooo8);
                                                                    console.log("ooo9: "+xooo9);
                                                                    
                                                                });
                                                                
                                                                $("#idInputV10").blur(function(){
                                                                    var dif1 = ($("#idInputV10").val());
                                                                    var dif2 = ($("#autoV10").val());
                                                                    
                                                                    var ndif1 = parseFloat(dif1.replace(/\,/g, ''));
                                                                    var ndif2 = parseFloat(dif2.replace(/\,/g, ''));
                                                                    var dif = ndif1 - ndif2;
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                    console.log("Diferencia interes1: "+dif1);
                                                                    console.log("Diferencia interes2: "+dif2);
                                                                    console.log("Diferencia interes: "+dif);
                                                                    document.getElementById("dif10").value=formatV(dif);
                                                                });
                                                                
                                                                $("#idInputV10").change(function(){
                                                                    var dif1 = ($("#idInputV10").val());
                                                                    var dif2 = ($("#autoV10").val());
                                                                    
                                                                    var ndif1 = parseFloat(dif1.replace(/\,/g, ''));
                                                                    var ndif2 = parseFloat(dif2.replace(/\,/g, ''));
                                                                    var dif = ndif1 - ndif2;
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                    console.log("Diferencia interes1: "+dif1);
                                                                    console.log("Diferencia interes2: "+dif2);
                                                                    console.log("Diferencia interes: "+dif);
                                                                    document.getElementById("dif10").value=formatV(dif);
                                                                });
                                                                
                                                                
                                                                $("#idInputV10").blur(function(){
                                                                    var x1 = ($("#idInputV8").val());
                                                                    var x2 = ($("#idInputV9").val());
                                                                    var x3 = ($("#idInputV10").val());

                                                                    var x4 = ($("#autoV8").val());
                                                                    var x5 = ($("#autoV9").val());
                                                                    var x6 = ($("#autoV10").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    var nx2 = parseFloat(x2.replace(/\,/g, ''));
                                                                    var nx3 = parseFloat(x3.replace(/\,/g, ''));
                                                                    var nx4 = parseFloat(x4.replace(/\,/g, ''));
                                                                    var nx5 = parseFloat(x5.replace(/\,/g, ''));
                                                                    var nx6 = parseFloat(x6.replace(/\,/g, ''));

                                                                    var T1 = nx1 + nx2 + nx3;
                                                                    var T2 = nx4 + nx5 + nx6;
                                                                    
                                                                    var dif = T1 - T2;
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                   
                                                                    document.getElementById("idInputV11").value=formatV(T1);
                                                                    document.getElementById('autoV11').value= formatV(T2);
                                                                    document.getElementById('dif11').value= formatV(dif); 
                                                                });
                                                               
                                                                $("#idInputV11").change(function(){
                                                                    var x1 = ($("#idInputV11").val());
                                                                    var x2 = ($("#autoV11").val());
                                                                    
                                                                    var nx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                    var nx2 = parseFloat(x2.replace(/\,/g, ''));
                                                                    
                                                                    var dif = nx1 - nx2;
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                   
                                                                    
                                                                    document.getElementById('dif11').value= formatV(dif); 
                                                                });
                                                            </script>
                                                            
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-sm-10 col-md-10 col-lg-10  text-right">
                                     
                                    <button  type="submit" id="btnGuardarDetalle" title="Guardar Declaración" onclick="GuardarDecla()" class="btn btn-primary shadow" tabindex="<?php echo $i ?>"><li class="glyphicon glyphicon-floppy-disk"></li></button>  
                                    <!--<button  type="submit" id="btnGuardarDetalle" title="Imprimir Declaración" onclick="ImprimirDecla()" class="btn btn-primary shadow" tabindex="<?php #echo $i ?>"><li class="glyphicon glyphicon-print"></li></button>  
                                    <!--<button type="button" id="liquidar" onclick="abrirRec()" title ="Liquidar Recaudo"  class="btn btn-primary shadow" disabled="true"><li  class="glyphicon glyphicon-usd" ></li></button>       -->                     
                                </div>
                            </div>
                        </form>        
                    </div>
                </div>
                <?php   if(!empty($Xor)){ ?>
                            <script>
                                $("#liquidar").prop("disabled", false);
                            </script>    
                <?php   }else{ ?>           
                            <script>
                                $("#liquidar").prop("disabled", true);
                            </script>
                <?php   } ?>
                <script>
                    function GuardarDecla(){
                        $('form').attr('action', 'jsonComercio/registrarDeclaracionIcaJson.php');
                        $('#form').attr('target','');
                    }

                    function ImprimirDecla(){
                        $('form').attr('action', 'informesComercio/generar_INF_DECLARACION.php');
                        $('#form').attr('target','_BLANK');

                    }

                    function RegistrarTerCont(){
                    	$("#terCont").modal('show');
                    }
                </script> 
                <div class="modal fade" id="terCont" role="dialog" align="center" >
				    <div class="modal-dialog">
				        <div class="modal-content" style="width: 500px;">
				            <div id="forma-modal" class="modal-header">
				                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Perfil Tercero</h4>
				            </div>
				            <div class="modal-body" style="margin-top: 8px">
				                <div class="form-group"  align="center">
				                    <select style="font-size:15px;height: 40px;" name="terceroC" id="terceroC" class="form-control" title="Tipo Identificación" required>
				                        <option >Perfil Tercero</option>
				                        <option value="1">Cliente Juridica</option>
				                        <option value="2">Cliente Natural</option>
				                                                        
				                    </select>
				                </div>                           
				            </div>
				            <div id="forma-modal" class="modal-footer">
				                <button type="button" id="men" class="btn" onclick="return enviar()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>	
				                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
				            </div>	
				        </div>
				    </div>
				</div>
				<script>
				    function enviar(){                
				        var form = document.getElementById('terceroC').value;
				        if(form == 1){
				            window.location='registrar_GC_CONTRIBUYENTE_JURIDICA.php';
				        }else{
				            window.location='registrar_GC_CONTRIBUYENTE_NATURAL.php';
				        }
				        ///window.location="terceros.php?tercero="+form;
				    }
				</script>       
                <div class="modal fade" id="myModal" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>¿Desea eliminar el registro seleccionado de Establecimiento?</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal fade" id="myModal1" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>Información eliminada correctamente.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal fade" id="myModal2" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
  
                <div class="modal fade" id="NoModal" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>No hay contribuyentes, por favor verifique la identificación y el número</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Script que dan estilo al formulario-->

                <script src="js/bootstrap.min.js"></script>
                <script src="js/txtValida.js"></script>

                <!--Scrip que envia los datos para la eliminación-->
                <script type="text/javascript">
                    function eliminar(id){
                        var result = '';
                        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            $("#mymodal").modal('hide');
                            $.ajax({
                                type:"GET",
                                url:"json/eliminarTarifaPJson.php?id="+id,
                                success: function (data) {
                                    result = JSON.parse(data);
                                    if(result==true)  
                                        $("#myModal1").modal('show');
                                    else
                                        $("#myModal2").modal('show');
                                }
                            });
                        });
                    }
                </script>

                <script type="text/javascript">
                    function modal(){
                        $("#myModal").modal('show');
                    }
                </script>
                <!--Actualiza la página-->
                <script type="text/javascript">
    
                    $('#ver1').click(function(){
                        document.location = 'listar_GR_TARIFA.php';
                    });
    
                </script>

                <script type="text/javascript">    
                    $('#ver2').click(function(){
                        document.location = 'listar_GR_TARIFA.php';
                    });    
                </script>
            </div>    
        </div>
    </body>    
</html>
   

<?php if (isset($_GET['e'])){ ?>
 
                     <!--2do formulario Establecimiento -->

<div class="col-sm-8 col-md-8 col-lg-8 client-form" style="margin-top:-11.3%;margin-right: 10px;margin-left: 1%;padding: 5px 5px 5px 5px;">   
                
                      

                    </div>


                      <!--listado establecimiento contribuyente-->
                     <div class="col-sm-8" style="margin-top:-0.5%">
                          


           <!--modales de eliminacion-->
                <div class="modal fade" id="myModal" role="dialog" align="center" >
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                      <p>¿Desea eliminar el registro seleccionado de Establecimiento?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                      <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                      <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                  </div>
                </div>
                </div>
                <div class="modal fade" id="myModal1" role="dialog" align="center" >
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                      <p>Información eliminada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                      <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                  </div>
                </div>
                </div>
                <div class="modal fade" id="myModal2" role="dialog" align="center" >
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                      <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                      <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                  </div>
                </div>
                </div>
                
                <?php require 'GC_ESTABLECIMIENTO_MODAL.php' ; ?>
                <!--Script que envia los datos para la eliminación-->
                <script type="text/javascript">
                      function eliminare(id)
                      {
                         var result = '';
                         $("#myModal").modal('show');
                         $("#ver").click(function(){
                              $("#mymodal").modal('hide');
                              $.ajax({
                                  type:"GET",
                                  url:"jsonComercio/eliminarEstablecimientosJson.php?id="+id,
                                  success: function (data) {
                                  result = JSON.parse(data);
                                  if(result==true)
                                      $("#myModal1").modal('show');
                                 else
                                      $("#myModal2").modal('show');
                                  }
                              });
                          });
                      }

                       function open_modal_e(id){  
                              var form_data={                            
                                id:id 
                              };
                              $.ajax({
                                  type:"POST",
                                  url: "GC_ESTABLECIMIENTO_MODAL.php#mdlModificar",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlModificar").html(data);
                                    $(".modale").modal('show');
                                 }
                             })  
                        }
                  </script>

                  <script type="text/javascript">
                      function modal()
                      {
                         $("#myModal").modal('show');
                      }
                  </script>
                    <!--Actualiza la página-->
                  <script type="text/javascript">
                    
                      $('#ver1').click(function(){
                        document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                      });
                    
                  </script>

                  <script type="text/javascript">    
                      $('#ver2').click(function(){
                        document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                      });    
                  </script>

                          <script type="text/javascript" >
                            function abrirdetalleMov(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }
                            function abrirdetalleMov1(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }                                                                                          
                          </script>

                          <style>
                              .valores:hover{
                                  cursor: pointer;
                                  color:#1155CC;
                              }
                          </style>
                          <div class="container">

                          </div>
                    
                          <script>
                            $("#btnGuardarM").click(function(){
                                if($("#sltBanco").val()==""){
                                    $("#mdlBanco").modal('show');
                                }else{
                                    var form_data = {
                                        banco:$("#sltBanco").val(),
                                        fecha:$("#fecha").val(),
                                        descripcion:$("#txtDescripcion").val(),
                                        valor:<?php echo $w; ?>,
                                        valorEjecucion:'0',
                                        comprobante:$("#id").val(),
                                        tercero:$("#slttercero").val(),
                                        proyecto:$("#sltproyecto").val(),
                                        centro:$("#sltcentroc").val()
                                    };
                                    var result = '';
                                    
                                    $.ajax({
                                        type: 'POST',
                                        url: "consultarComprobanteIngreso/GuardarBanco.php",
                                        data:form_data,
                                        success: function (data) {
                                            result = JSON.parse(data);
                                            console.log(data);
                                            if(result==true){
                                                $("#guardado").modal('show');
                                            }else{
                                                $("#noguardado").modal('show');
                                            }
                                        }
                                    });
                                }                                                                                                
                            });                        
                          </script>
                      </div>                



              <?php } ?>
  



  </div>
</div>
<div class="text-left"><?php require_once 'footer.php'; ?> </div>

        
        <!-- Modales de guardado -->
        <div class="modal fade" id="guardado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información guardada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnG" class="btn" onclick="reloda()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noguardado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido guardar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnG2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Moidales de eliminado -->
        <!--<div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado de Detalle Ingreso?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información eliminada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal2" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>-->
         
        <!-- Modales de modificado -->
        <div class="modal fade" id="difIngre" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Existe una diferencia entre el concepto 15(Total Ingresos Gravables) y el concepto 16(Total Ingresos Gravados en el Municipio o Distrito).</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noModifico" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido modificar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="LiqReca" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content" style="width: 450px;">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Recaudo</h4>
                    </div>
                    <div class="modal-body" >
                        <div class="row text-left">
                            <form class="form-horizontal" id="formu" name="formu" method="post" action="jsonComercio/registrarRecaudoJson.php">                                                        
                                <div class="form-group" >
                                    <input type="hidden" name="txtTper"  value="<?php echo $periodi ?>">
                                    <input type="hidden" name="txtTdec1" value="<?php echo $TipoDC ?>">
                                    <input type="hidden" name="txtfecD"  value="<?php echo $FDEC ?>">
                                    <input type="hidden" name="txtidDec" value="<?php echo $id_dec ?>">
                                    <input type="hidden" name="txtnumeral" value="2">
                                    <label for="banco" class="control-label col-sm-4 col-md-4 col-lg-4 text-right"><strong class="obligado">*</strong>Banco:</label>
                                        <?php 
                                            $per = "SELECT cb.id_unico, CONCAT(cb.numerocuenta,' - ',t.razonsocial)
                                                    FROM gf_cuenta_bancaria cb
                                                    lEFT JOIN  gf_cuenta_bancaria_tercero cbt ON cbt.cuentabancaria = cb.id_unico
                                                    lEFT JOIN  gf_tercero t ON cb.banco = t.id_unico
                                                    WHERE cbt.tercero = 1";
                                            $periodo = $mysqli->query($per);
                                        ?> 
                                    <div class="col-sm-6 col-md-6 col-lg-6">    
                                        <select  id="banco" name="banco" class="form-control select2" title="Seleccione el Banco" required>
                                            <option value="" >Banco</option>
                                            <?php 
                                                while($rowE = mysqli_fetch_row($periodo)){
                                                    echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                                }
                                            ?>                     
                                        </select>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <script type="text/javascript">
                                                $(document).ready(function() {
                                                    $("#datepicker").datepicker();
                                                });
                                            </script>
                                    <label for="sltFecha" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Fecha:</label>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <input name="sltFecha" id="sltFecha" title="Ingrese Fecha " type="text"  class="form-control "   placeholder="Ingrese la fecha">
                                    </div>    
                                </div>
                                <div class="form-group"  ">
                                
                                    <label for="txtconsg" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" >Consignación o Pago:</label>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <input name="txtconsg" id="txtconsg" title="Ingrese Número Consignación " type="text" class="form-control " placeholder="Ingrese Número Consignación o Pago ">
                                    </div>    
                                </div>
                                <div class="form-group"  ">
                                
                                    <label for="txtnum" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Declaración:</label>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <input name="txtnum" id="txtnum" title="Ingrese el Número Documento" type="text" class="form-control " value="<?php echo $codi ?>"  placeholder="Ingrese el Número Documento" readonly>
                                    </div>    
                                </div>
                                <div class="form-group"  align="left">
                                    <label for="sltTipoR" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Tipo Recaudo:</label>
                                        <?php 
                                            $tiporec = "SELECT id_unico, nombre FROM gc_tipo_recaudo WHERE id_unico = 1 ";
                                            $treca = $mysqli->query($tiporec);
                                        ?> 
                                    <div class="col-sm-6 col-md-6 col-lg-6">    
                                        <select  id="sltTipoR" name="sltTipoR" class="form-control select2" title="Seleccione el Tipo de Recaudo" >
                                            <!--<option >Tipo Recaudo</option>-->
                                            <?php 
                                                while($rowTR = mysqli_fetch_row($treca)){
                                                    echo "<option value=".$rowTR[0].">".$rowTR[1]."</option>";
                                                }
                                            ?>                     
                                        </select>
                                    </div>    
                                </div>
                            </form>
                        </div>    
                           
                        
                        
                                                 
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="guardar" class="btn"  style="color: #000; margin-top: 2px"  title="Guardar Recaudo"><li class="glyphicon glyphicon-floppy-disk" ></button>    
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
                    </div>  
                </div>
            </div>

            <script>
                $('#guardar').click(function(e){
                    var cbanco  = $("#banco").val();
                    var fecha  = $("#sltFecha").val();
                    var pago   = $("#txtconsg").val();
                    var tipo   = $("#sltTipoR").val();
                    if(cbanco.length > 0 && fecha.length > 0 ){
                        $('#formu').submit();
                    }else{
                        if(cbanco == ""){
                            //$("#banco").parents(".col-sm-6").addClass('has-error');
                            $("#s2id_banco").addClass('has-error');
                            //$("label[for='banco']").addClass('has-error');
                        }

                        if(fecha == ""){
                            $("#sltFecha").parents(".col-sm-6").addClass('has-error');
                        }

                        if(pago == ""){
                            $("#txtconsg").parents(".col-sm-6").addClass('has-error');
                        }

                        if(tipo == ""){
                            $("#s2id_sltTipoR").parents(".col-sm-6").addClass('has-error');
                        }
                    }
                });
            </script>
        </div>
        <script>
        function abrirRec(){
            $("#LiqReca").modal('show');
                
        }
</script>
        
        <!-- Librerias -->
        <script src="js/bootstrap.min.js"></script>
        
        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
            $("#sltctai").select2();
            $("#sltVig").select2();
            $("#sltNumI").select2();
            $("#sltPer").select2();

       
        </script>

        <script>
            $("#idInputV28").blur(function(){
                var VP = parseFloat($("#idInputV26").val());
                var D = parseFloat($("#idInputV27").val());
                var I = parseFloat($("#idInputV28").val());

                var VP1 = parseFloat($("#autoV26").val());
                var D1 = parseFloat($("#autoV27").val());
                var I1 = parseFloat($("#autoV28").val());

                var NP = (VP - D) + I;

                var NP1 = (VP1 - D1) + I1;
                var d = NP - NP1
                document.getElementById("idInputV29").value=NP;
                document.getElementById("autoV29").value=NP1;
                document.getElementById("dif29").value=d;
            
            });
        </script>
        <?php
            $Agravab = "SELECT vigencia FROM gc_anno_comercial WHERE id_unico = '$idP'";
            $AGr = $mysqli->query($Agravab);
            $AG = mysqli_fetch_row($AGr);

            $Vencem = "SELECT fecha FROM gr_vencimiento WHERE tipo = 3 AND anno = '$AG[0]' ";
            $VeIn = $mysqli->query($Vencem);
            $VI = mysqli_fetch_row($VeIn);

            

            $Fdc = $VI[0];
        ?>
        <input type="hidden" id="FeVI" name="FeVI" value="<?php echo $Fdc ?>">
        <input type="hidden" id="txtAgra" name="txtAgra" value="<?php echo $AG[0] ?>">
        <script>          
            $("#idInputV26").blur(function(){
                 
                var fecD        = $("#Fec_D").val(); 
                var FechVI      = $("#FeVI").val();
                var valor       = $("#BaseInteres").val();
                var VDesc       = $("#BaseDesc").val();
                var tipo        = 4;
                var SA          = parseFloat($("#idInputV21").val());
                var AnG         = $("#txtAgra").val();
                                                              
                //var valor       = 99000; 
                console.log("sancion: "+SA)
                if(SA > 0){
                    $.ajax({
                        url: 'funciones/Int_Desc_Industria.php',
                        type: 'POST',                    
                        data: {
                            'bandera':'int',
                            'fechaD': fecD,
                            'valor': valor,
                            'fechaVen': FechVI
                            
                            

                        },

                        success: function(data){
                            console.log("valor interes:"+data);
                            result = data.trim();
                            if(result == 0){
                                document.getElementById("idInputV28").value=0;
                                document.getElementById("autoV28").value=0; 
                                document.getElementById("dif28").value=0;
                            }else if((result > 0)&&(result < 500)){
                                result = Math.ceil(result / 1000) * 1000;
                                document.getElementById("idInputV27").value=0;
                                document.getElementById("autoV27").value=0; 
                                document.getElementById("dif27").value=0;

                                document.getElementById("idInputV28").value=result;
                                document.getElementById("autoV28").value=result; 
                                document.getElementById("dif28").value=0; 
                            }else{
                                result = Math.round(result / 1000) * 1000;
                                document.getElementById("idInputV27").value=0;
                                document.getElementById("autoV27").value=0; 
                                document.getElementById("dif27").value=0;

                                document.getElementById("idInputV28").value=result;
                                document.getElementById("autoV28").value=result; 
                                document.getElementById("dif28").value=0;
                            }
                        }
                          
                    });

                }else{
                    console.log("hola");
                    $.ajax({
                        url: 'funciones/Int_Desc_Industria.php',
                        type: 'POST',                    
                        data: {
                            'bandera':'des',
                            'fechaD': fecD,
                            'valor': VDesc,
                            'tipo': tipo,
                            'p': AnG
                            

                        },

                        success: function(data){
                            console.log("valor descuento:"+data);
                            result = data.trim();
                            if(result == 0){
                                document.getElementById("idInputV27").value=0;
                                document.getElementById("autoV27").value=0; 
                                document.getElementById("dif27").value=0;
                            }else if((result > 0)&&(result < 500)){
                                result = Math.ceil(result / 1000) * 1000;
                                document.getElementById("idInputV27").value=result;
                                document.getElementById("autoV27").value=result; 
                                document.getElementById("dif27").value=0;

                                document.getElementById("idInputV28").value=0;
                                document.getElementById("autoV28").value=0; 
                                document.getElementById("dif28").value=0; 
                            }else{
                                result = Math.round(result / 1000) * 1000;
                                document.getElementById("idInputV27").value=result;
                                document.getElementById("autoV27").value=result; 
                                document.getElementById("dif27").value=0;

                                document.getElementById("idInputV28").value=0;
                                document.getElementById("autoV28").value=0; 
                                document.getElementById("dif28").value=0;
                            }
                            
                            
                        }

                        
                    });

                        console.log("fechaD: "+fecD);
                        console.log("valor des: "+VDesc);
                        console.log("tipo: "+tipo);
                }
            });


            

        </script>
        <?php #echo $_SESSION['vinteres']; ?>

     
    </body>
</html>

|