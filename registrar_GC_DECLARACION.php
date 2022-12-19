    <?php
        require_once 'head.php';
        require_once './Conexion/conexion.php'; 


              
        $val = 0; 
         
        if(!empty($_REQUEST['peri2'])){
            @$periodi = $_REQUEST['peri2'];
            @$TipoDC  = $_REQUEST['TipoD'];
            @$FDEC    = $_REQUEST['FD'];
            @$Pmed    = $_REQUEST['pes'];
        }else{
            @$periodi = $_POST['peri2'];
            @$TipoDC  = $_POST['TipoD'];
            @$FDEC    = $_POST['FD'];
            @$Pmed    = $_POST['pes'];

            
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
        @$Pmed    = $_GET['pes'];

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
                        c.dir_correspondencia,
                        c.telefono,
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
        
        
        $periodog = "SELECT id_unico, vigencia FROM gc_anno_comercial WHERE md5(id_unico) = '$per'";
        $pgra = $mysqli->query($periodog);
        $pg = mysqli_fetch_row($pgra);
            
        $idP = $pg[0];
        $valorp = $pg[1];
        
        if(empty($idP)){
            $periodoG = "Periodo Gravable";
        }else{
            $periodoG = $valorp;
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

                label #sltctai-error, #sltVig-error, #sltNumI-error, #txtnum-error, #txtnum-error {
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

                  .client-form1 input[type="text"]{
                    width: 100%;
                    background: #00548f1c !important;
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
                   
                    $("#sltFechaDec").datepicker({changeMonth: true,}).val();
                });
            </script>

            <title>Declaración Industria y Comercio</title>

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
                    <!--<div class="col-sm-2 col-md-2 col-lg-2"></div>-->
                    <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                        <h3 id="forma-titulo3" align="center" style="margin-top: 0px; margin-right: 4px; margin-left: 4px;">FORMULARIO ÚNICO NACIONAL DE DECLARACIÓN Y PAGO DEL IMPUESTO DE INDUSTRIA Y COMERCIO</h3>
                        <a href="index2.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                        <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "A. INFORMACIÓN DEL CONTRIBUYENTE";?></h5>
                        <div class="client-form " style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                            <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" >
                                <p align="center" style="margin-bottom: 10px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                
                                <div class="form-group"  >
                                    
                                    <input type="hidden" name="id" value="<?php echo $rowC[0] ?>">
                                    <input type="hidden" name="PD" id="PD" value="<?php echo $periodi ?>">
                                    <input type="hidden" name="TD" id="TD" value="<?php echo $TipoDC ?>">
                                    <input type="hidden" name="Fec_D" id="Fec_D" value="<?php echo $FDEC ?>">
                                    <input type="hidden" name="PeMe" id="PeMe" value="<?php echo $Pmed ?>">
                                    <input type="hidden" name="X" id="X" value="1">
                                    <?php #echo $Pmed ?>
                                    
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                           $("#datepicker").datepicker();
                                        });
                                    </script>
                                    
                                    <?php
                                        $hoy = date('d-m-Y');
                                        $hoy = trim($hoy, '"');
                                        $fecha_div = explode("-", $hoy);
                                        $anio1 = $fecha_div[2];
                                        $mes1 = $fecha_div[1];
                                        $dia1 = $fecha_div[0];
                                        $hoy = ''.$dia1.'/'.$mes1.'/'.$anio1.'';
                                    ?>
                                    
                                    <!--<label for="sltFechaDec" class="col-sm-3 col-md-3 col-lg-3 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                                    <div class="col-sm-1 col-md-1 col-lg-1"  align="left">
                                        <input type="text" id="sltFechaDec" name="sltFechaDec" class="form-control" value="<?php #echo $hoy; ?>">
                                    </div>-->
                                    <script>
                                        <?php
                                        ##$FDEC = document.getElementById('sltFechaDec').value;
                                        ?>
                                    </script>
                                    
                                    <label for="sltctai" class="col-sm-3 col-md-3 col-lg-3 control-label" ><strong style="color:#03C1FB;">*</strong>Periodo:</label>
                                    
                                    <div class="col-sm-2 col-md-2 col-lg-1" >                                    
                                        <select name="sltctai" id="sltctai" required  class="form-control select2" title="Seleccione Periodo Gravable" style="width: 150%" >
                                               <?php                                             
                                                echo "<option value=\"$idP\">$periodoG</option>";
                                                $consulta=" SELECT annoc.id_unico,annoc.vigencia, annoc.mes  FROM gc_anno_comercial annoc ORDER BY vigencia ASC";
                                                $rr=$mysqli->query($consulta); 

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
                                                    <option value="<?php echo $fa[0]?>"><?php echo $fa[1]." - ".$ME ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <label for="sltVig" class="col-sm-2 col-md-2 col-lg-1 control-label"><strong class="obligado">*</strong>Vigencia:</label>
                                    <div class="col-sm-2 col-md-2 col-lg-1" > 
                                        <select name="sltVig" id="sltVig" required  class="form-control select2" title="Seleccione la Vigencia Fiscal" style="width: 150%" >
                                            <?php
                                                echo "<option value=\"$idV\">$ViF</option>";
                                                $consulta=" SELECT id_unico, vigencia FROM gc_vigencia_comercial  ORDER BY vigencia DESC";
                                                $rr=$mysqli->query($consulta); 
                                    
                                                while($fa=mysqli_fetch_row($rr)){ ?>
                                                    <option value="<?php echo $fa[0]?>"><?php echo $fa[1] ?></option>
                                            <?php } ?>                          

                                        </select>    
                                    </div>  

                                    <label for="reg" class="control-label col-sm-1 col-md-1 col-lg-1" >Régimen:</label>
                                    <div class="col-sm-3 col-md-3 col-lg-2">
                                        <?php   if($reg == 4){  ?>
                                                    <label class="radio-inline"><input  id="reg" type="radio" name="reg" value="1" checked title="Seleccione el Tipo de Regimen" >Común</label>
                                                    <label class="radio-inline"><input  id="reg" type="radio" name="reg" value="2"  title="Seleccione el Tipo de Regimen" > Simplificado</label>
                                        <?php   }elseif($reg == 5){ ?> 
                                                    <label class="radio-inline"><input  id="reg" type="radio" name="reg" value="1" title="Seleccione el Tipo de Regimen">Común</label>
                                                    <label class="radio-inline"><input  id="reg" type="radio" name="reg" value="2" checked  title="Seleccione el Tipo de Regimen"> Simplificado</label>
                                        <?php   }else{ ?>
                                                    <label class="radio-inline"><input  id="reg" type="radio" name="reg" value="1" title="Seleccione el Tipo de Regimen">Común</label>
                                                    <label class="radio-inline"><input  id="reg" type="radio" name="reg" value="2"  title="Seleccione el Tipo de Regimen"> Simplificado</label>
                                        <?php   } ?>            
                                    </div>

                                     
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    
                                    <label for="txtnum" class="control-label col-sm-4 col-md-4 col-lg-4"><strong class="obligado">*</strong>Número:</label>
                                    <div class="col-sm-1 col-md-1 col-lg-1">
                                        <input type="text" name="txtnum" id="txtnum" value="<?php echo $codi ?>" title="Ingrese el código de la declaración" style="width: 100%" required>
                                    </div> 
                                    
                                    <label for="PEM" class="control-label col-sm-2 col-md-2 col-lg-2" >Pesas y Medidas:</label>
                                    <div class="col-sm-3 col-md-3 col-lg-3">
                                        <?php   if($Pmed == "1"){ ?>
                                                    <label class="radio-inline"><input  id="PEM" type="radio" name="PEM" value="1" checked>SI</label>
                                                    <label class="radio-inline"><input  id="PEM" type="radio" name="PEM" value="2">NO</label>
                                        <?php   }elseif($Pmed == "2"){ ?>
                                                    <label class="radio-inline"><input  id="PEM" type="radio" name="PEM" value="1">SI</label>
                                                    <label class="radio-inline"><input  id="PEM" type="radio" name="PEM" value="2" checked>NO</label>
                                        <?php   }else{ ?>
                                                    <label class="radio-inline"><input  id="PEM" type="radio" name="PEM" value="1">SI</label>
                                                    <label class="radio-inline"><input  id="PEM" type="radio" name="PEM" value="2" checked>NO</label>
                                        <?php   } ?>
                                        
                                    </div>  
                                </div>
                                <div class="col-sm-2 col-md-2 col-lg-2"></div>
                                    <div class=" col-sm-8 col-md-8 col-lg-8 client-form contenedorForma" style="margin-top:-0.3%;"> 
                                        <h4 class="text-center titulo_h3">datos personales</h4> 
                                        <div class="form-group" style="margin-top: 1.2%; margin-left: 1%;">
                                            <input type="hidden" name="id" value="<?php echo $rowC[0] ?>">
                                            <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >1. Tipo Identificación:</label>

                                            <div class="col-sm-1 col-md-1 col-lg-1">
                                                <?php   if($ident == 1){ ?>
                                                            <input   required="" type="radio" name="ident" id="ident" value="1" checked>cc<br> 
                                                            <input    required="" type="radio" name="ident" id="ident" value="2">Nit<br>
                                                <?php   }elseif($ident==2){ ?>    
                                                            <input   required="" type="radio" name="ident" id="ident" value="1">cc<br> 
                                                            <input    required="" type="radio" name="ident" id="ident" value="2" checked>Nit<br>
                                                <?php   }else{ ?>         
                                                            <input   required="" type="radio" name="ident" id="ident" value="1">cc<br> 
                                                            <input    required="" type="radio" name="ident" id="ident" value="2">Nit<br>
                                                <?php   } ?>
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
                                            <label for="sltNumI" class="control-label col-sm-1 col-md-1 col-lg-1" ><strong class="obligado">*</strong>Contrib.:</label>
                                            <div class="col-sm-2 col-md-2 col-lg-2">
                                                <select name="sltNumI" id="sltNumI" class="form-control select2" title="Seleccione Contribuyente" required>
                                                    
                                                    <?php
                                                        echo "<option value=\"$idC\">$tercero</option>";
                                                        while($NI = mysqli_fetch_row($numI)){
                                                
                                                                echo "<option value=\"$NI[0]\">$NI[5] - $NI[3] - $NI[1]</option>";
                                                            }
                                                    ?>
                                                </select>
                                                
                                            </div>

                                            <label for="cante" class="control-label col-sm-1 col-md-1 col-lg-1">DV:</label>
                                            <div class="col-sm-1 col-md-1 col-lg-1">
                                                <input   type="text" name="dv" id="cante" class="form-control" title="DV" onkeypress="return txtValida(event,'num_car')" placeholder="DV"  maxlength="15"  readonly>
                                            </div>
                                            
                                            <label for="cact" class="control-label col-sm-1 col-md-1 col-lg-1" >Teléfono:</label>
                                            <div class="col-sm-2 col-md-2 col-lg-2">
                                                <input  type="text" name="telefono"  id="t" class="form-control" title="Telefono" placeholder="Telefono" value="<?php echo $rowC[6] ?>"  maxlength="15" readonly>
                                            </div>
                                        </div>
        
                                        <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                                            <label for="contribuyente2" class="control-label col-sm-2 col-md-2 col-lg-2" >3. Contribuyente:</label>
                                            <div  class="col-sm-9 col-md-9 col-lg-9">
                                                <input  type="text" name="Con"  id="contribuyente2" class="form-control" title="Contribuyente" placeholder="Contribuyente" value="<?php echo $rowC[1] ?>"   readonly>
                                            </div>

                                        </div>
                
                                        <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                                            <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >4. Razón Social (Nombre de Establecimiento):</label>
                                            <div class="col-sm-9 col-md-9 col-lg-9">
                                                <input  type="text" name="razón Social"  id="cact" class="form-control" title="Razón Social" onkeypress="return txtValida(event,'num_car')" placeholder="Razón Social"  maxlength="15" readonly>
                                            </div>

                                        </div>

                                        <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                                            <!--<label for="codp" class="control-label col-sm-2 col-md-2 col-lg-2">Código Postal:</label>
                                            <input type="text" name="codigoPostal" id="codp" class="form-control" maxlength="15" title="Código Postal" onkeypress="return txtValida(event,'num_car')" placeholder="Código Postal" value="<?php echo $rowC[4] ?>" >-->

                                            <label for="dn" class="control-label col-sm-2 col-md-2 col-lg-2" >5. Dirección para notificar:</label>
                                            <div class="col-sm-2 col-md-2 col-lg-2">
                                                <input type="text" name="dn" id="dn" class="form-control" value="<?php echo $rowC[5] ?>""  title="Dirección para notificar" placeholder="Dirección para notificar" readonly>
                                            </div>

                                            <label for="cact" class="control-label col-sm-2 col-md-2 col-lg-2" >6. Dirección Establecimiento:</label>
                                            <div class="col-sm-2 col-md-2 col-lg-2">
                                                <input  type="text" name="direcciónEstablecimiento"  id="cact" class="form-control" title="Dirección Establecimiento" onkeypress="return txtValida(event,'num_car')" placeholder="Dirección Establecimiento"   maxlength="15" readonly>
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

                                            <label for="txtNumE" class="control-label col-sm-2 col-md-2 col-lg-2" >7. No. Establecimientos:</label>
                                            <div class="col-sm-1 col-md-1 col-lg-1">
                                                <input  type="text" name="txtNumE"  id="txtNumE" class="form-control text-center" title="No. Establecimientos" onkeypress="return txtValida(event,'num')" placeholder="No. Establecimientos" value="<?php echo $NE ?>"  maxlength="15" readonly>
                                            </div> 
                                        </div>
                                    </div>
                                       
                                    <script src="js/md5.js"></script>
                                    <script>
                                        $("#sltNumI").change(function(){
                    
                                            //evento enter de numero identificaciong
                                            var I     = $('input[name=identificacion]:checked').val();
                                            var NI    = $("#sltNumI").val();
                                            var per   = $('#sltctai').val();
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
                                        
                                            document.location = 'registrar_GC_DECLARACION.php?I='+md5(I)+'&N='+md5(NI)+'&per='+md5(per)+'&vig='+md5(vig)+'&cod='+deco+'&peri2='+peri1+'&TipoD='+TipoD+'&FD='+FED+'&pes='+PMEDI; 
                            
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
                                    <div class="container-fluid text-center">
                                        <div class="row content">
                                            <div class="col-sm-12 col-md-12 col-lg-12 text-left client-form1">
                                                <div class="table-responsive" style="margin-top:-10px;">
                                                    <div class="table-responsive" >
                                                        <table id="tabla" class="table table-bordered"  cellspacing="0" width="100%">
                                                            <caption><strong>ESTABLECIMIENTOS</strong></caption>
                                                            <thead>
                                                                <tr>  
                                                                    <td class="cabeza"><strong>Nombre</strong></td>
                                                                    <td class="cabeza"><strong>Fecha Inicial</strong></td>
                                                                    <td class="cabeza"><strong>Estrato</strong></td>
                                                                    <td class="cabeza"><strong>Dirección</strong></td>
                                                                    <td class="cabeza"><strong>Código Catastral</strong></td>
                                                                    <td class="cabeza"><strong>Ciudad</strong></td>
                                                                    <td class="cabeza"><strong>Barrio</strong></td>
                                                                    <td class="cabeza"><strong>Localización</strong></td>
                                                                    <td class="cabeza"><strong>Tipo Entidad</strong></td>
                                                                    <td class="cabeza"><strong>Tamaño Entidad</strong></td>
                                                                </tr>
                                                            </thead>        
                                                            <tbody>
                                                                <?php 
                                                                    while($rowee=mysqli_fetch_row($resultadoee)){ ?>
                                                                        <tr>
                                                                            <td  id="" class="campos"><?php echo $rowee[1] ?></td>                
                                                                            <td  id="" class="campos"><?php echo $rowee[2] ?></td>                
                                                                            <td  id="" class="campos"><?php echo $rowee[3] ?></td>                
                                                                            <td  id="" class="campos"><?php echo $rowee[4] ?></td>                
                                                                            <td  id="" class="campos"><?php echo $rowee[5] ?></td>                
                                                                            <td  id="" class="campos"><?php echo $rowee[6] ?></td>                
                                                                            <td  id="" class="campos"><?php echo $rowee[7] ?></td>                
                                                                            <td  id="" class="campos"><?php echo $rowee[8] ?></td>
                                                                            <td  id="" class="campos"><?php echo $rowee[9] ?></td>                
                                                                            <td  id="" class="campos"><?php echo $rowee[10] ?></td>                
                                                                        </tr>
                                                                <?php 
                                                                    } 
                                                                ?>             
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                            <?php
                                                //vehiculos
                                                $sqlV = "SELECT     v.id_unico,
                                                                tv.nombre,
                                                                v.cod_inter,
                                                                v.placa,
                                                                v.porc_propiedad
                                                        FROM gc_vehiculo v
                                                        LEFT JOIN gc_contribuyente c ON c.id_unico=v.contribuyente
                                                        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                                                        LEFT JOIN gc_tipo_vehiculo tv ON tv.id_unico=v.tipo_vehiculo
                                                        WHERE md5(c.id_unico)='$n'";

                                                $resultadoV=$mysqli->query($sqlV);
                                                $sqlti="SELECT id_unico,nombre FROM gc_tipo_ingreso WHERE nombre='Ingresos Totales Año' or nombre='Ingresos Vehiculo segun%'";
                                                $rti=$mysqli->query($sqlti);  
                                                $filas=0;
                                            ?>
                                        
                                            <!--2da table Trasnportadores publicos urbanos-->
                                            <div class="col-sm-12 col-md-12 col-lg-12 text-left client-form1">
                                                <div class="table-responsive" style="margin-top:-10px;">
                                                    <div class="table-responsive" >
                                                        <table id="tablab" class="table table-bordered"  cellspacing="0" width="100%">
                                                            <caption><strong>VEHICULOS</strong></caption>
                                                            <thead>
                                                                <tr>    
                                                                    <td><strong>8. Tipo Vehículo</strong></td>
                                                                    <td><strong>9. Empresa</strong></td>
                                                                    <td><strong>10. Código Interno</strong></td>
                                                                    <td><strong>11. Placas</strong></td>
                                                                    <td><strong>12. % Propiedad</strong></td>
                                                                    <?php 
                                                                        $cn1=13;
                                                                        while($rowti=mysqli_fetch_row($rti)){ ?>
                                                                            <td><strong><?php echo $cn1.'.'.$rowti[1] ?></strong></td>
                                                                    <?php
                                                                            $cn1++;
                                                                        } ?>
                                                                    <!-- <td><strong>14. <?php echo $rowti[1] ?></strong></td>-->
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php   
                                                                        $XY = 0; 
                                                                        while($rowvv=mysqli_fetch_row($resultadoV)){ 
                                                                            $XY++;
                                                                ?>
                                                                            <tr>
                                                                                <td><?php echo $rowvv[1] ?></td>
                                                                                <td></td>
                                                                                <td><?php echo $rowvv[2] ?></td>
                                                                                <td><?php echo $rowvv[3] ?></td>
                                                                                <td><?php echo $rowvv[4] ?></td>
                                                                                <?php
                                                                                    $idInputIT="idIngresosTotales".$XY;
                                                                                   
                                                                                    $idInputIV="idIngresosVehiculo".$XY;
                                                                                    $nameVehiculos = "idVehiculo".$XY;
                                                                                    $idV = "idV".$XY;
                                                                                    $valorT = "nameValorT".$XY; 
                                                                                    $valorP = "nameValorP".$XY;
                                                                                    $i  = "idve".$XY;
                                                                                ?>
                                                                                
                                                                                <td>
                                                                                    <input type="hidden" name="<?php echo $i; ?>" value= "<?php echo $rowvv[0]; ?>">
                                                                                    <input id="<?php echo $valorT ?>" name ="<?php echo $valorT; ?> " value="" style="width: 100%" type="number" ></td> 
                                                                                <td><input id="<?php echo $valorP ?>" name ="<?php echo $valorP; ?>" value="" style="width: 100%" type="number"></td>
                                                                            </tr>
                                                                <?php   } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php 
                                                $sqlBG="SELECT cc.id_unico,cc.descripcion,tc.nombre, cc.apli_descu, cc.apli_inte, cc.anticipo FROM gc_concepto_comercial cc 
                                                    LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                                                    WHERE tc.id_unico = 1"; 

                                                $qBG=$mysqli->query($sqlBG);
                                                $inputsValue=0;  //contador inputs value

                                            ?>                                        
                                            <!--4to Base Gravable -->
                                            <?php
                                            $X = 8;
                                            ?>
                                            <div class="col-sm-12 col-md-12 col-lg-12  text-left client-form1">
                                                
                                                <div class="table-responsive" style="margin-top:-10px;">
                                                    <div class="table-responsive">
                                                        <table  class="table table-bordered"  cellspacing="0" width="100%">
                                                            <thead>
                                                            <th colspan="2" >B. BASE GRAVABLE</th>
                                                                <th>VALOR</th>
                                                                <th>AUTOLIQUIDACION</th>
                                                                <th>DIFERENCIA</th>
                                                            </thead>
                                                            <tbody>     
                                                                <?php
                                                                    $i = 0; 
                                                                    while($rowBG=mysqli_fetch_row($qBG)){ ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?php
                                                                                 echo $X++;
                                                                                
                                                                                ?>
                                                                            </td>
                                                                            <!--id_concepto Comercial-->
                                                                            <?php  
                                                                                $inputsValue++;
                                                                                $idInputCC="idInputCC".$inputsValue;
                                                                                $nameIdConceptoComercial="iidConceptoComercial".$inputsValue;
                                                                                $apdes = "apdes".$inputsValue;
                                                                                $apint = "apint".$inputsValue;
                                                                                $antici = "antici".$inputsValue;

                                                                            ?>
                                                                            <input id="<?php echo $idInputCC ?>" name="<?php echo $nameIdConceptoComercial ?>" type="hidden" value="<?php echo $rowBG[0];    ?>" >

                                                                            <input id="<?php echo $apdes ?>" name="<?php echo $apdes ?>" type="hidden" value="<?php echo $rowBG[3]; ?>" >
                                                                            <input id="<?php echo $apint ?>" name="<?php echo $apint ?>" type="hidden" value="<?php echo $rowBG[4]; ?>" >
                                                                            
                                                                            <input id="<?php echo $antici ?>" name="<?php echo $antici ?>" type="hidden" value="<?php echo $rowBG[5]; ?>" >


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
                                                                            <td >
                                                                                <input  id="<?php echo $idInputV ?>" name="<?php echo $nameValue ?>" value="" style="width: 100%;" type="text" tabindex="<?php echo $i ?>"  onkeyup="formatC('<?php echo $idInputV ?>');" onKeypress="if(event.keyCode == 13) event.returnValue = false;"  >
                                                                            </td>
                                                                            <!--<td dir="ltr" id="test1" class="tLine" nowrap onclick="addInput(this)"></td>-->
                                                                            <td>
                                                                                <input id="<?php echo $autoV ?>" name="<?php echo $nameAut ?>" value="0" style="width: 100%; background: #fff9003b !important;" type="text" readonly >
                                                                            </td>
                                                                            <td>
                                                                                <input id="<?php echo $dif ?>" name="" value="0" style="width: 100%;background: #fff9003b !important;" type="text" readonly >
                                                                            </td>
                                                                        </tr>
                                                                <?php
                                                                    }

                                                                ?>

                                                                <script>
                                                                                                                                                                          
                                                                    $("#idInputV2").change(function(){
                                                                        var x1 = ($("#idInputV1").val());
                                                                        var x2 = ($("#idInputV2").val()); 
                                                                                                
                                                                        var x3 = parseFloat(x1.replace(/\,/g, ''));
                                                                        var x4 = parseFloat(x2.replace(/\,/g, ''));
                                                                        var T = x3 - x4;
                                                                        var dif = 0;
                                                                        console.log(T);
                                                                        
                                                                        document.getElementById('idInputV3').value=T;
                                                                        document.getElementById('autoV3').value=formatV(T);
                                                                        document.getElementById('dif3').value= dif; 
                                                                    });


                                                                    $("#idInputV3").change(function(){
                                                                        var x1 = ($("#idInputV1").val());
                                                                        var x2 = ($("#idInputV2").val());
                                                                        var x3 = ($("#idInputV3").val());
                                                                        var x4 = parseFloat(x1.replace(/\,/g, ''));
                                                                        var x5 = parseFloat(x2.replace(/\,/g, ''));
                                                                        var x6 = parseFloat(x3.replace(/\,/g, ''));
                                                                        var T = x4 - x5;
                                                                        var r = x6 - T;
                                                                        if(r < 0){
                                                                            var rr = r * -1;
                                                                        }else{
                                                                            var rr = r;
                                                                        }
                                                                        var dif = 0;

                                                                        console.log("cambio: "+x6);
                                                                        console.log("res: "+T);
                                                                        console.log("val: "+rr);
                                                                        var oo = 1000000;
                                                                        var jj = 100000;
                                                                        document.getElementById('autoV3').value=formatV(T);
                                                                        document.getElementById('dif3').value= formatV(rr); 
                                                                    });

                                                                    $("#idInputV1").blur(function(){
                                                                        var x1 = ($("#idInputV1").val());
                                                                        var x2 = ($("#idInputV2").val());
                                                                        var x3 = ($("#idInputV3").val());
                                                                        var x4 = ($("#idInputV4").val());
                                                                        var x5 = ($("#idInputV5").val());
                                                                        var x6 = ($("#idInputV6").val());
                                                                        var x7 = ($("#idInputV7").val());
                                                                       
                                                                        var xx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                        var xx2 = parseFloat(x2.replace(/\,/g, ''));
                                                                         var xx3 = parseFloat(x3.replace(/\,/g, ''));
                                                                        var xx4 = parseFloat(x4.replace(/\,/g, ''));
                                                                        var xx5 = parseFloat(x5.replace(/\,/g, ''));
                                                                        var xx6 = parseFloat(x6.replace(/\,/g, ''));
                                                                        var xx7 = parseFloat(x7.replace(/\,/g, ''));
                                                                                                                                             
                                                                        
                                                                        var T = xx1 - xx2;
                                                                        var TT = -xx4 - xx5 - xx6 - xx7;
                                                                        var r = T + TT;
                                                                        if(r < 0){
                                                                            var rr = r * -1;
                                                                        }else{
                                                                            var rr = r;
                                                                        }
                                                                        var dif = 0;
                                                                        if (isNaN(T)) {
                                                                            return 0;
                                                                         } 
                                                                       
                                                                        var dif = 0;
                                                                        if (isNaN(T)) {
                                                                            return 0;
                                                                         }                                     
                                                                        document.getElementById('idInputV3').value=formatV(T);
                                                                        document.getElementById('autoV3').value=formatV(T);
                                                                        document.getElementById('dif3').value= dif; 
                                                                         document.getElementById('idInputV8').value=formatV(r);
                                                                         document.getElementById('autoV8').value=formatV(r);
                                                                        document.getElementById('dif8').value= dif; 
                                                                    });


                                                                     

                                                                    $("#idInputV7").blur(function(){
                                                                        var x1 = ($("#idInputV3").val());
                                                                        var x2 = ($("#idInputV4").val());
                                                                        var x3 = ($("#idInputV5").val());
                                                                        var x4 = ($("#idInputV6").val());
                                                                        var x5 = ($("#idInputV7").val());
                                                                        var xx1 = parseFloat(x1.replace(/\,/g, ''));
                                                                        var xx2 = parseFloat(x2.replace(/\,/g, ''));
                                                                        var xx3 = parseFloat(x3.replace(/\,/g, ''));
                                                                        var xx4 = parseFloat(x4.replace(/\,/g, ''));
                                                                        var xx5 = parseFloat(x5.replace(/\,/g, ''));
                                                                        
                                                                        
                                                                        var T = xx1 - xx2 - xx3 - xx4 - xx5;
                                                                        var r = xx3 - T;
                                                                        if(r < 0){
                                                                            var rr = r * -1;
                                                                        }else{
                                                                            var rr = r;
                                                                        }
                                                                        var dif = 0;
                                                                        if (isNaN(T)) {
                                                                            return 0;
                                                                         } 
                                                                        
                                                                        document.getElementById('idInputV8').value=formatV(T);
                                                                        document.getElementById('autoV8').value=formatV(T);
                                                                        document.getElementById('dif8').value= dif; 
                                                                    });

                                                                    $("#idInputV8").blur(function(){
                                                                        var apli1 = $("#apdes1").val();
                                                                        if(apli1 != ""){
                                                                            var ooo1  = ($("#idInputV1").val());
                                                                            var xooo1 = parseFloat(ooo1.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo1 = 0;
                                                                        }

                                                                        var apli2 = $("#apdes2").val();
                                                                        if(apli2 != ""){
                                                                            var ooo2  = ($("#idInputV2").val());
                                                                            var xooo2 = parseFloat(ooo2.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo2 = 0;
                                                                        }

                                                                        var apli3 = $("#apdes3").val();
                                                                        if(apli3 != ""){
                                                                            var ooo3  = ($("#idInputV3").val());
                                                                            var xooo3 = parseFloat(ooo3.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo3 = 0;
                                                                        }

                                                                        var apli4 = $("#apdes4").val();
                                                                        if(apli4 != ""){
                                                                            var ooo4  = ($("#idInputV4").val());
                                                                            var xooo4 = parseFloat(ooo4.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo4 = 0;
                                                                        }

                                                                        var apli5 = $("#apdes5").val();
                                                                        if(apli5 != ""){
                                                                            var ooo5  = ($("#idInputV5").val());
                                                                            var xooo5 = parseFloat(ooo5.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo5 = 0;
                                                                        }

                                                                        var apli6 = $("#apdes6").val();
                                                                        if(apli6 != ""){
                                                                            var ooo6  = ($("#idInputV6").val());
                                                                            var xooo6 = parseFloat(ooo6.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo6 = 0;
                                                                        }

                                                                        var apli7 = $("#apdes7").val();
                                                                        if(apli7 != ""){
                                                                            var ooo7  = ($("#idInputV7").val());
                                                                            var xooo7 = parseFloat(ooo7.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo7 = 0;
                                                                        }

                                                                        var apli8 = $("#apdes8").val();
                                                                        if(apli8 != ""){
                                                                            var ooo8  = ($("#idInputV8").val());
                                                                            var xooo8 = parseFloat(ooo8.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo8 = 0;
                                                                        }

                                                                        TBGD = xooo1 + xooo2 + xooo3 + xooo4 + xooo5 + xooo6 + xooo7 + xooo8;

                                                                        
                                                                    });
                                                                </script>  

                                                                <script>
                                                                    $("#idInputV8").blur(function(){
                                                                        var apli1 = $("#apint1").val();
                                                                        if(apli1 != ""){
                                                                            var ooo1  = ($("#idInputV1").val());
                                                                            var xooo1 = parseFloat(ooo1.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo1 = 0;
                                                                        }

                                                                        var apli2 = $("#apint2").val();
                                                                        if(apli2 != ""){
                                                                            var ooo2  = ($("#idInputV2").val());
                                                                            var xooo2 = parseFloat(ooo2.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo2 = 0;
                                                                        }

                                                                        var apli3 = $("#apint3").val();
                                                                        if(apli3 != ""){
                                                                            var ooo3  = ($("#idInputV3").val());
                                                                            var xooo3 = parseFloat(ooo3.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo3 = 0;
                                                                        }

                                                                        var apli4 = $("#apint4").val();
                                                                        if(apli4 != ""){
                                                                        var ooo4  = ($("#idInputV4").val());
                                                                        var xooo4 = parseFloat(ooo4.replace(/\,/g, ''));
                                                                            }else{
                                                                                var xooo4 = 0;
                                                                            }

                                                                        var apli5 = $("#apint5").val();
                                                                        if(apli5 != ""){
                                                                            var ooo5  = ($("#idInputV5").val());
                                                                            var xooo5 = parseFloat(ooo5.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo5 = 0;
                                                                        }

                                                                        var apli6 = $("#apint6").val();
                                                                        if(apli6 != ""){
                                                                            var ooo6  = ($("#idInputV6").val());
                                                                            var xooo6 = parseFloat(ooo6.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo6 = 0;
                                                                        }

                                                                        var apli7 = $("#apint7").val();
                                                                        if(apli7 != ""){
                                                                            var ooo7  = ($("#idInputV7").val());
                                                                            var xooo7 = parseFloat(ooo7.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo7 = 0;
                                                                        }

                                                                        var apli8 = $("#apint8").val();
                                                                        if(apli8 != ""){
                                                                            var ooo8  = ($("#idInputV8").val());
                                                                            var xooo8 = parseFloat(ooo8.replace(/\,/g, ''));
                                                                        }else{
                                                                            var xooo8 = 0;
                                                                        }

                                                                        TBGI = xooo1 + xooo2 + xooo3 + xooo4 + xooo5 + xooo6 + xooo7 + xooo8;
                                                                        
                                                                    });
                                                                </script>     
                                                                <script>
                                                                   
                                                                    $("#idInputV8").change(function(){

                                                                        var val3 = $("#idInputV3").val();
                                                                        var val4 = $("#idInputV4").val();
                                                                        var val5 = $("#idInputV5").val();
                                                                        var val6 = $("#idInputV6").val();
                                                                        var val7 = $("#idInputV7").val();
                                                                        var val8 = $("#idInputV8").val();
                                                                        
                                                                        var xx3 = parseFloat(val3.replace(/\,/g, ''));
                                                                        var xx4 = parseFloat(val4.replace(/\,/g, ''));
                                                                        var xx5 = parseFloat(val5.replace(/\,/g, ''));
                                                                        var xx6 = parseFloat(val6.replace(/\,/g, ''));
                                                                        var xx7 = parseFloat(val7.replace(/\,/g, ''));
                                                                        var xx8 = parseFloat(val8.replace(/\,/g, ''));
                                                                        
                                                                        var tot = xx3 - xx4 - xx5 - xx6 - xx7;
                                                                        var dif = xx8 - tot;

                                                                        if(dif < 0){
                                                                            dif = dif * -1;
                                                                        }
                                                                        //document.getElementById("autoV8").value=formatV(tot); 
                                                                        document.getElementById("dif8").value=formatV(dif); 
                                                                    });

                                                                   
                                                                </script>    
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php   
                                                /*$sqlti2="   SELECT  id_unico,
                                                                        nombre 
                                                                FROM gc_tipo_ingreso 
                                                                WHERE nombre='Ingresos Brutos por Actividad' 
                                                                or nombre='Deducciones' or nombre='Ingresos Excluidos' or nombre='Ingresos Externos' 
                                                                or nombre='Ingresos Fuera de Duitama' or nombre='Ingresos Netos Gravables' or nombre='Tarifa' or nombre='Impuesto'";
                                                $rti2=$mysqli->query($sqlti2);  */
                                                $Agravab = "SELECT vigencia FROM gc_anno_comercial WHERE id_unico = '$idP'";
                                                $AGr = $mysqli->query($Agravab);
                                                $AG = mysqli_fetch_row($AGr);

                                                $Vigenc = "SELECT id_unico FROM gc_vigencia_comercial WHERE vigencia = '$AG[0]'";
                                                $VComer = $mysqli->query($Vigenc);
                                                $VC = mysqli_fetch_row($VComer);

                                                $actividad = "  SELECT DISTINCT ac.id_unico, ac.codigo , ac.descripcion, ta.tarifa FROM gc_actividad_comercial ac 
                                                                LEFT JOIN gc_actividad_contribuyente aco ON aco.actividad = ac.id_unico 
                                                                LEFT JOIN gc_contribuyente c ON aco.contribuyente = c.id_unico 
                                                                LEFT JOIN gc_tarifa_actividad ta ON ta.act_comer = ac.id_unico
                                                                WHERE md5(c.id_unico) = '$n'  AND ta.anno_grava = '$VC[0]'
                                                                ORDER BY aco.fechainicio DESC, ac.codigo ASC";

                                                $acti = $mysqli->query($actividad);

                                                  //parámetro sobretasa bomberil**LORENA MORENO**10/01/2019
                                                                 $queryTasa = "SELECT valor FROM gs_parametros_basicos WHERE nombre = '% sobretasa bomberil'";
                                                                     $result = $mysqli->query($queryTasa);
                                                      $rowTasa = mysqli_fetch_row($result);
                                                      $porc_bom = $rowTasa[0];


                                            ?>
                                            <!--3ra table Ingresos por Actividad -->
                                            <div class="col-sm-12 col-md-12 col-lg-12 text-left client-form1">
                                               
                                                <div class="table-responsive" style="margin-top:-10px;">
                                                    <div class="table-responsive" >
                                                        <table id="tablac" class="table table-bordered"  cellspacing="0" width="100%">
                                                            <caption><strong>C. INGRESOS POR ACTIVIDAD</strong></caption>
                                                            <thead>
                                                                <tr>  
                                                                    <td><strong>Actividad</strong></td>
                                                                    <td><strong>Código</strong></td>
                                                                    <!--<td><strong>17. Ingresos Brutos por Actividad</strong></td>-->
                                                                    <!--<td><strong>18. Deducciones</strong></td>
                                                                    <td><strong>19. Ingresos Excluidos</strong></td>
                                                                    <td><strong>20. Ingresos Externos</strong></td>
                                                                    <td><strong>21. Ingresos Fuera de Duitama</strong></td>-->
                                                                    <td><strong>Ingresos Gravados</strong></td>
                                                                    <td><strong>Tarifa</strong></td>
                                                                    <td><strong>Impuesto</strong></td>
                                                                    
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                    #$cn=17;
                                                                    $mm= 0;
                                                                    $mm = $i;
                                                                    while($rowt2=mysqli_fetch_row($acti)){ 
                                                                        
                                                                      /*  $AIAct1 = explode("-",$rowt2[4]);
                                                                        $AIA = $AIAct1[0];

                                                                        $AFAct1 = explode("-",$rowt2[5]);
                                                                        $AFA = $AFAct1[0];
                                                                        if($AIA <= $AG[0]){
                                                                            
                                                                            if(($AFA >= $AG[0]) || (empty($rowt2[5]))){*/
                                                                                $mm++;
                                                                                $filas++;
                                                                                /*$idIBA="idiBA".$filas;
                                                                                $idD="idD".$filas;
                                                                                $idIE="idIE".$filas;
                                                                                $idIEXT="idIEXT".$filas;
                                                                                $idIFD="idIFD".$filas;*/
                                                                                $idING="idING".$filas;
                                                                                $idTarifa="idTarifa".$filas;
                                                                                $idIMP="idIMP".$filas;
                                                                                $idAC = "idAct".$filas;
                                                                                $tar = "tar".$filas;
                                                                ?>
                                                                                <tr>
                                                                                    <style type='text/css'> 
                                                                                        #<?php echo $idING ?> , #<?php echo $idIMP ?>, #<?php echo $idTarifa ?> {
                                                                                             text-align:right;
                                                                                             font-weight: bold;
                                                                                        } 
                                                                                    </style>
                                                                                    <td  id=""  value="<?php echo $rowt2[0] ?>"  ><?php echo $rowt2[2] ?></td>
                                                                                    <td id=""  value="<?php echo $rowt2[0] ?>"  ><?php echo $rowt2[1] ?></td>

                                                                                    <!--<td>-->
                                                                                        <input type="hidden" name="<?php echo $idAC; ?>" value= "<?php echo $rowt2[0]; ?>">
                                                                                        <input type="hidden" id="param" name="param" value= "<?php echo $rowTasa[0]; ?>">
                                                                                    <!-- <input id="<?php echo $idIBA; ?>" name="<?php echo $idIBA; ?>"  value="" style="width: 100%" type="number">
                                                                                    </td>
                                                                                    <!-- <td><input id="<?php echo $idD ?>" name="<?php echo $idD; ?>" value="" style="width: 100%" type="number"></td>
                                                                                    <td dir="ltr"><input id="<?php echo $idIE ?>" name="<?php echo $idIE; ?>" value="" style="width: 100%" type="number"></td>
                                                                                    <td><input id="<?php echo $idIEXT ?>" name="<?php echo $idIEXT; ?>"  value="" style="width: 100%" type="number"></td>-->
                                                                                    <input id="<?php echo $tar ?>" name="<?php echo $tar ?>"  value="<?php echo $rowt2[3] ?>" style="width: 100%" type="hidden">
                                                                                    
                                                                                    <td><input id="<?php echo $idING ?>" name="<?php echo $idING; ?>" tabindex="<?php echo $mm; ?>" value="" style="width: 100%;" type="text" tabindex="<?php echo $mm ?> " onkeyup="formatC('<?php echo $idING; ?>')" ></td>
                                                                                    
                                                                                    <td><input id="<?php echo $idTarifa ?>" name="<?php echo $idTarifa; ?>"  value="<?php echo $rowt2[3]."x1000"  ?>" style="width: 100%;background: #fff9003b !important;" type="text" readonly ></td>
                                                                                    <?php $mm = $mm+1; ?>
                                                                                    <td><input id="<?php echo $idIMP ?>" name="<?php echo $idIMP; ?>"  value="" style="width: 100%;" type="text" tabindex="<?php echo $mm ?>" onkeyup="formatC('<?php echo $idIMP; ?>')"></td>
                                                                                </tr>
                                                                <?php          
                                                                                $XXXX = $filas;  
                                                                            /*}
                                                                        }*/
                                                                           
                                                                    }
                                                                    
                                                                    if(!empty($XXXX)){

                                                                   
                                                                                                                                 
                                                                        for($i=1; $i<= $XXXX; $i++){
                                                                ?>
                                                                            <script>
                                                                                $("#<?php echo "idING".$i; ?>").change(function(){
                                                                                    var val = ($("#<?php echo "idING".$i; ?>").val()); 
                                                                                    var tr  = ($("#<?php echo "tar".$i; ?>").val()); 
                                                                                    
                                                                                    var xval = parseFloat(val.replace(/\,/g, ''));
                                                                                    var xtr  = parseFloat(tr.replace(/\,/g, ''));
                                                                                    
                                                                                    
                                                                                    var VImp = (xval * xtr) /1000;
                                                                                    var TVImp = Math.round(VImp / 1000) * 1000;
                                                                                      if (isNaN(TVImp)) {
                                                                                        return 0;
                                                                                         }

                                                                                    document.getElementById('<?php echo "idIMP".$i ?>').value= formatV(TVImp);
                                                                                });

                                                                               
                                                                            </script>
                                                                <?php
                                                                        }
                                                                    }
                                                                ?>
                                                                
                                                                <script>
                                                                        $("#<?php echo "idIMP".$XXXX ?>").keyup(function(){
                                                                                
                                                                                
                                                                                var TiN = 0;
                                                                                var TiM = 0;
                                                                            <?php  


                                                                                for($j = 1; $j <= $XXXX; $j++){
                                                                            ?>
                                                                                    cing = ($("#<?php echo "idING".$j ?>").val());
                                                                                    cim  = ($("#<?php echo "idIMP".$j ?>").val());
                                                                                    
                                                                                    var xcing = parseFloat(cing.replace(/\,/g, ''));
                                                                                    var xcim = parseFloat(cim.replace(/\,/g, ''));
                                                                                    
                                                                                    var TiN = TiN + xcing;
                                                                                    var TiM = TiM + xcim ;
                                                                                     if (isNaN(TiN)) {
                                                                                    return 0;
                                                                                     }
                                                                                     if (isNaN(TiM)) {
                                                                                     return 0;
                                                                                     }
                                                                                    console.log(TiN);
                                                                            <?php

                                                                                }
                                                                            ?>
                                                                                
                                                                           document.getElementById('INGM1').value=formatV(TiN);
                                                                           document.getElementById('TI1').value=formatV(TiM);
                                                                        });
                                                                </script>

                                                               
                                                            </tbody>
                                                            <tfoot>
                                                                <?php 
                                                                    $filas += 1;
                                                                    $fi = 1;
                                                                    $idING  = "idING".$filas; 
                                                                    $idTarifa="idTarifa".$filas;
                                                                    $idIMP="idIMP".$filas;
                                                                    $idAC = "idAct".$filas;
                                                                    $idCIE = "idCIE".$fi;
                                                                    $idTIICCE = "idTIICCE".$fi;
                                                                    $INGM = "INGM".$fi;
                                                                    $TI = "TI".$fi;
                                                                ?>
                                                                <tr>
                                                                    <style type='text/css'> 
                                                                        #<?php echo $INGM ?> , #<?php echo $TI ?>{
                                                                            text-align:right;
                                                                            font-weight: bold;
                                                                        } 
                                                                    </style>
                                                                    <?php $mm = $mm+1; ?>
                                                                    <td colspan="2" rowspan="" headers="">16.Total Ingresos Gravados en el Municipio o Distrito</td>
                                                                    <td colspan="1"><input id="<?php echo $INGM ?>" name="<?php echo $INGM ?>"  value="" style="width: 100%" type="text" tabindex="<?php echo $mm ?> " onkeyup="formatC('<?php echo $INGM; ?>')" ></td>
                                                                    <?php $mm = $mm+1; ?>
                                                                    <td colspan="1" rowspan="" headers="" type="number">17.Total Impuesto</td>
                                                                    <td><input id="<?php echo $TI ?>" name="<?php echo $TI ?>"  value="" style="width: 100%" type="text" tabindex="<?php echo $mm ?> " onkeyup="formatC('<?php echo $TI ?>')"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="5" rowspan="" headers=""></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="5" class="text-center" headers="">18.Liquidación del Impuesto Para la Actividad de Generación de Energía Eléctrica LEY 56 de 1981</td>
                                                                </tr>
                                                                <tr>
                                                                    <style type='text/css'> 
                                                                        #<?php echo $idCIE ?> , #<?php echo $idTIICCE ?>{
                                                                            text-align:right;
                                                                            font-weight: bold;
                                                                        } 
                                                                    </style>
                                                                    <td colspan="1" rowspan="" headers="">Capacidad Instalada en Este Municipio (en Kilovatios)</td>
                                                                    <?php $mm = $mm+1; ?>
                                                                    <td colspan="1" rowspan="" headers=""><input id="<?php echo $idCIE ?>" name="<?php echo $idIMP; ?>"  value="0" style="width: 100%" type="text" tabindex="<?php echo $mm; ?>"></td>
                                                                    <td colspan="2" rowspan="" headers="" type="text">19. Total Impuesto Industria yComercio po la Actividad de Generación de Energía Eléctrica(Multiplique la tarifa por la capacidad instalada) </td>
                                                                    <?php $mm = $mm+1; ?>
                                                                    <td colspan="1" rowspan="" headers=""><input id="<?php echo $idTIICCE ?>" name="<?php echo $idIMP; ?>"  value="0" style="width: 100%" type="text" tabindex="<?php echo $mm; ?>"></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group">
                                            <div class="col-sm-12 col-md-12 col-lg-12  text-right">
                                            <button onclick="addRow2()" type="submit" id="btnGuardarDetalle"  class="btn btn-primary shadow"><li class="glyphicon glyphicon-plus"></li></button>                              
                                            </div>
                                            </div>-->

                                            
                                            
                                            <?php 
                                                $sqlLP="SELECT cc.id_unico,cc.descripcion,tc.nombre,cc.apli_descu, cc.apli_inte, cc.codigo, cc.tipo_ope, cc.anticipo  FROM gc_concepto_comercial cc 
                                                    LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                                                    WHERE tc.id_unico = 2 ORDER BY cc.codigo ASC"; 

                                                $qLP=$mysqli->query($sqlLP);

                                                 $X = 20;
                                            ?>
                                                                                   
                                            <!--5to Liquidación privada -->
                                            <div class="col-sm-12 col-md-12 col-lg-12 text-left client-form1" style="margin-top: 0px;" >
                                                 
                                                <div class="table-responsive" >
                                                    <div class="table-responsive">
                                                        <table  class="table table-bordered"  cellspacing="0" width="100%">
                                                            <thead>
                                                                <th colspan="2" >D. LIQUIDACIÓN DEL IMPUESTO</th>
                                                                <th>VALOR</th>
                                                                <th>AUTOLIQUIDACION</th>
                                                                <th>DIFERENCIA</th>
                                                            </thead>  
                                                            <tbody>
                                                                <?php 

                                                                    $AperG = "SELECT vigencia FROM gc_vigencia_comercial WHERE id_unico = '$idV'";
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
                                                                                <?php echo $X++; ?>
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
                                                                                <?php if($idInputD == "idInputD20"){ ?>
                                                                                            
                                                                                <td style="width:  15%"><?php echo ucwords(mb_strtolower($rowLP[1])) ?>
                                                                                    <!--<input type="radio" >Extemporaneidad
                                                                                    <br/>
                                                                                    <!--<input type="radio" >Correción
                                                                                    <br/>
                                                                                    <input type="radio" >Inexatitud
                                                                                    <br/>
                                                                                    <input type="radio" >Otra-->
                                                                                </td>
                                                                                <!--value detalle declaracion-->
                                                                                <?php }else{ ?>
                                                                                        <td type="checkbox" style="width: 60%"><?php echo ucwords(mb_strtolower($rowLP[1])) ?></td>

                                                                            <?php
                                                                                    }

                                                                                $idInputV="idInputV".$inputsValue;  
                                                                                $nameValue="iValue".$inputsValue;
                                                                                $autoV = "autoV".$inputsValue;
                                                                                $dif = "dif".$inputsValue;  //
                                                                                $apdes = "apdes".$inputsValue;
                                                                                $apint = "apint".$inputsValue;
                                                                                $indi = "indi".$inputsValue;
                                                                                $nameAut = "idAutoL".$inputsValue;
                                                                                $antici = "antici".$inputsValue;
                                                                            ?>
                                                                            
                                                                            <input id="<?php echo $indi ?>" name="<?php echo $indi ?>" type="hidden" value="<?php echo $rowLP[6]; ?>" >
                                                                            <input id="<?php echo $apdes ?>" name="<?php echo $apdes ?>" type="hidden" value="<?php echo $rowLP[3]; ?>" >
                                                                            <input id="<?php echo $apint ?>" name="<?php echo $apint ?>" type="hidden" value="<?php echo $rowLP[4]; ?>" >
                                                                            <input id="<?php echo $antici ?>" name="<?php echo $antici ?>" type="hidden" value="<?php echo $rowLP[7]; ?>" >
                                                                            <input id="<?php echo "txtConcepto".$inputsValue; ?>" name="<?php echo "txtConcepto".$inputsValue; ?>" type="hidden" value="<?php echo $rowLP[0]; ?>" >
                                                                            <?php $mm = $mm+1; ?>
    																		
    									                                       <style type='text/css'> 
                                                                                #<?php echo $idInputV ?> , #<?php echo $autoV ?>, #<?php echo $dif ?> {
    										                                          text-align:right;
                                                                                    font-weight: bold;
                                                                                } 
    									                                   </style>
                                                                            
                                                                            <td><input id="<?php echo $idInputV ?>" name="<?php echo $nameValue ?>" value="" style="width: 100%" type="text" tabindex="<?php echo $mm ?>" onkeyup="formatC('<?php echo $idInputV; ?>')" onKeypress="if(event.keyCode == 13) event.returnValue = false;"></td>
                                                                            <td><input id="<?php echo $autoV ?>" name="<?php echo $nameAut ?>" value="0" style="width: 100%; background: #fff9003b !important;" type="text" readonly></td>
                                                                            <td><input id="<?php echo $dif ?>" name="<?php echo $dif ?>" value="0" style="width: 100%; text-align: right;background: #fff9003b !important;" type="text" readonly></td>
                                                                        </tr>
                                                                <?php 
                                                                    }
                                                                ?>
                                                                <script>
                                                                    $("#idInputV11").change(function(){
                                                                        
                                                                        var dif = 0;
                                                                        var PMid = $("#PEM:checked").val();
                                                                        var val1 = parseFloat($("#idInputV11").val());
                                                                        if(PMid == 1){
                                                                            var valor =  $("#ValC23").val();
                                                                        }else{
                                                                            var valor = 0;
                                                                        }

                                                                      
                                                                        console.log(PMid);
                                                                        document.getElementById("autoV11").value = val1;
                                                                        document.getElementById("dif11").value = dif;

                                                                        document.getElementById("idInputV12").value = valor;
                                                                        document.getElementById("autoV12").value = formatV(valor);
                                                                        document.getElementById("dif12").value = dif;


                                                                    });

                                                                     $("#idInputV11").blur(function(){
                                                                        
                                                                        var dif = 0;
                                                                        var PMid = $("#PEM:checked").val();
                                                                        var val1 = parseFloat($("#idInputV11").val());
                                                                        if(PMid == 1){
                                                                            var valor =  $("#ValC23").val();
                                                                        }else{
                                                                            var valor = 0;
                                                                        }

                                                                      
                                                                        console.log(PMid);
                                                                        document.getElementById("autoV11").value = val1;
                                                                        document.getElementById("dif11").value = dif;

                                                                        document.getElementById("idInputV12").value = valor;
                                                                        document.getElementById("autoV12").value = formatV(valor);
                                                                        document.getElementById("dif12").value = dif;
                                                                       
                                                                    });
                                                                </script>

                                                                <script>

                                                                    $("#idInputV12").change(function(){
                                                                        var liq = ($("#idInputV12").val());
                                                                        var Aut = ($("#autoV12").val());
                                                                        
                                                                        var xliq = parseFloat(liq.replace(/\,/g, ''));
                                                                        var xAut = parseFloat(Aut.replace(/\,/g, ''));
                                                                        var totPM;
                                                                        totPM = xliq - xAut;
                                                                        if (isNaN(totPM)) {
                                                                                 return 0;
                                                                         }  
                                                                        document.getElementById("dif12").value = totPM;

                                                                    });

                                                                    $("#idInputV12").blur(function(){
                                                                        var porcB = ($("#param").val());                              
                                                                        var val5  = ($("#idInputV9").val());
                                                                        var xval5 = parseFloat(val5.replace(/\,/g, ''));
                                                                         //sobretasa bomberil
                                                                        var Sb = (xval5 * porcB) / 100;
                                                                        if(Sb < 500){
                                                                            var vsb = Math.ceil(Sb / 1000) * 1000;
                                                                        }else{
                                                                            var vsb = Math.round(Sb / 1000) * 1000;
                                                                        }
                                                                        console.log("impuesto: "+val5);
                                                                        console.log("por: "+vsb);
                                                                        var ds = 0;
                                                                        
                                                                        document.getElementById("idInputV13").value=formatV(vsb);
                                                                        document.getElementById("autoV13").value=formatV(vsb); 
                                                                        document.getElementById("dif13").value=ds;

                                                                    });



                                                                    </script>

                                                                <script>
                                                                    $("#idInputV21").blur(function(){
                                                                        
                                                                        var valor =  $("#ValC33").val();
                                                                        var dif = 0;

                                                                         if (isNaN(valor)) {
                                                                         return 0;
                                                                         }

                                                                        
                                                                        //$("#autoV22").val(valor);
                                                                        document.getElementById("idInputV22").value = valor;
                                                                        document.getElementById("autoV22").value = formatV(valor);
                                                                        document.getElementById("dif22").value = dif;
                                                                    });
                                                                    </script>

                                                                <script>
                                                                    $("#idInputV22").change(function(){
                                                                        var liq = ($("#idInputV22").val());
                                                                        var Aut = ($("#autoV22").val());
                                                                        
                                                                        var xliq = parseFloat(liq.replace(/\,/g, ''));
                                                                        var xAut = parseFloat(Aut.replace(/\,/g, ''));
                                                                        var totPM;
                                                                        totPM = xliq - xAut;
                                                                        
                                                                        if(totPM < 0){
                                                                            totPM = totPM * -1;
                                                                        }
                                                                        
                                                                        document.getElementById("dif22").value = totPM;
                                                                    });

                                                                    $("#idInputV20").blur(function(){
                                                                        var san  = ($("#ValC32").val());
                                                                        var peri2 = ($("#sltctai").val());
                                                                        var vige = ($("#sltVig").val());
                                                                        
                                                                        var xsan  = parseFloat(san.replace(/\,/g, ''));
                                                                        var xperi = parseFloat(peri2.replace(/\,/g, ''));
                                                                        var xvige = parseFloat(vige.replace(/\,/g, ''));
                                                                       
                                                                        
                                                                        var cero = 0;
                                                                          
                                                                         
                                                                        <?php 
                                                                          $AperG = "SELECT vigencia FROM gc_anno_comercial WHERE md5(id_unico) = '$per'";
                                                                            $APGr = $mysqli->query($AperG);
                                                                            $APG = mysqli_fetch_row($APGr);

                                                                          $AVComer = "SELECT vigencia FROM gc_vigencia_comercial WHERE md5(id_unico) = '$vig' ";
                                                                            $AnVi = $mysqli->query($AVComer);
                                                                            $AVC = mysqli_fetch_row($AnVi);

                                                                            $SANS = $AVC[0] - $APG[0];
                                                                            

                                                                            if($SANS > 1){
                                                                        ?>
                                                                                document.getElementById("idInputV21").value=xsan;
                                                                                document.getElementById("autoV21").value=formatV(xsan);
                                                                                document.getElementById("dif21").value=cero;

                                                                        <?php
                                                                            }else{
                                                                                $Vencem = "SELECT fecha FROM gr_vencimiento WHERE tipo = 3 AND anno =  '$APG[0]' ";
                                                                                $VeIn = $mysqli->query($Vencem);
                                                                                $VI = mysqli_fetch_row($VeIn);
                                                                                
                                                                                
                                                                                $FDCL = explode("/",$FDEC);
                                                                                $an = $FDCL[2];
                                                                                $ms = $FDCL[1];
                                                                                $da = $FDCL[0];

                                                                                $Fdc = $an.'-'.$ms.'-'.$da;

                                                                                if($Fdc > $VI[0]){
                                                                        ?>
                                                                                  document.getElementById("idInputV21").value=san;  
                                                                                  document.getElementById("autoV21").value=formatV(san);  
                                                                                 document.getElementById("dif21").value=cero;  
                                                                        
                                                                        <?php            
                                                                                }else{
                                                                        ?>
                                                                                    document.getElementById("idInputV21").value=cero;
                                                                                    document.getElementById("autoV21").value=cero;  
                                                                                    document.getElementById("dif21").value=cero;
                                                                        <?php            
                                                                                }
                                                                            }

                                                                        ?>
                                                                        

                                                                    });

                                                                </script>

                                                                <script>  
                                                                    $("#idInputV21").change(function(){
                                                                        var sau =  ($("#idInputV21").val());
                                                                        var sas =  ($("#autoV21").val());
                                                                        
                                                                        var xsau = parseFloat(sau.replace(/\,/g, ''));
                                                                        var xsas = parseFloat(sas.replace(/\,/g, ''));

                                                                        var di = xsau - xsas;

                                                                        if(di < 0 ){
                                                                            di = di * -1;
                                                                        }

                                                                        document.getElementById("dif21").value=di;

                                                                    });

                                                                </script>
                                                                <script>
                                                                    $("#idInputV25").change(function(){
                                                                        
                                                                        var apli1 = $("#apdes9").val();
                                                                        if(apli1 != ""){
                                                                            var ooo9  = ($("#idInputV9").val());
                                                                            var xooo9 = parseFloat(ooo9.replace(/\,/g, ''));
                                                                            var aut9  = ($("#autoV9").val());
                                                                            var xaut9 = parseFloat(aut9.replace(/\,/g, '')); 
                                                                            var ind = parseFloat($("#indi9").val());
                                                                            if(ind == 3){   
                                                                                xooo9 = xooo9 * -1; 
                                                                            }
                                                                            if(xaut9 ==""){
                                                                                    xaut9 = 0;
                                                                             }
                                                                        }else{
                                                                            var xooo9 = 0;
                                                                            var xaut9 = 0; 
                                                                        }

                                                                        var apli2 = $("#apdes10").val();
                                                                        if(apli2 != ""){
                                                                            var ooo10  = ($("#idInputV10").val());
                                                                            if (isNaN(ooo10)) {
                                                                            return 0;
                                                                            }
                                                                            var xooo10 = parseFloat(ooo10.replace(/\,/g, ''));
                                                                            var aut10  = ($("#autoV10").val());
                                                                            var xaut10 = parseFloat(aut10.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi10").val());
                                                                            if(ind == 3){
                                                                                xooo10 = xooo10 * -1;
                                                                            }
                                                                            if(xaut10 ==""){
                                                                                    xaut10 = 0;
                                                                            }
                                                                        }else{
                                                                            var xooo10 = 0;
                                                                            var xaut10 = 0; 
                                                                        }

                                                                        var apli3 = $("#apdes11").val();
                                                                        if(apli3 != ""){
                                                                            var ooo11  = ($("#idInputV11").val());
                                                                            var xooo11 = parseFloat(ooo11.replace(/\,/g, ''));
                                                                            var aut11  = ($("#autoV11").val());
                                                                            var xaut11 = parseFloat(aut11.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi11").val());
                                                                            if(ind == 3){
                                                                                xooo11 = xooo11 * -1;
                                                                            }
                                                                            if(xaut11 ==""){
                                                                                    xaut11 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo11 = 0;
                                                                            var xaut11 = 0; 
                                                                        }

                                                                        var apli4 = $("#apdes12").val();
                                                                        if(apli4 != ""){
                                                                            var ooo12  = ($("#idInputV12").val());
                                                                            var xooo12 = parseFloat(ooo12.replace(/\,/g, ''));
                                                                            var aut12  = ($("#autoV12").val());
                                                                            var xaut11 = parseFloat(aut12.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi12").val());
                                                                            if(ind == 3){
                                                                                xooo12 = xooo12 * -1;
                                                                            }
                                                                             if(xaut12 ==""){
                                                                                    xaut12 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo12 = 0;
                                                                            var xaut12 = 0; 
                                                                        }

                                                                        var apli5 = $("#apdes13").val();
                                                                        if(apli5 != ""){
                                                                            var ooo13  = ($("#idInputV13").val());
                                                                             if (isNaN(ooo13)) {
                                                                            return 0;
                                                                            }
                                                                            var xooo13 = parseFloat(ooo13.replace(/\,/g, ''));
                                                                            var aut13  = ($("#autoV13").val());
                                                                            var xaut13 = parseFloat(aut13.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi13").val());
                                                                            if(ind == 3){
                                                                                xooo13 = xooo13 * -1;
                                                                            } 
                                                                            if(aut13 ==""){
                                                                                 xaut13 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo13 = 0;
                                                                            var xaut13 = 0; 
                                                                        }

                                                                        var apli6 = $("#apdes14").val();
                                                                        if(apli6 != ""){
                                                                            var ooo14  = ($("#idInputV14").val());

                                                                            var xooo14 = parseFloat(ooo14.replace(/\,/g, ''));
                                                                            var aut14  = ($("#autoV14").val());

                                                                            var xaut14 = parseFloat(aut14.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi14").val());
                                                                            if(ind == 3){
                                                                                xooo14 = xooo14 * -1;
                                                                            } 
                                                                             if(xaut14 ==""){
                                                                                    xaut14 = 0;
                                                                               }
                                                                        }else{
                                                                            var xooo14 = 0;
                                                                            var xaut14 = 0; 
                                                                        }

                                                                       

                                                                        var apli8 = $("#apdes15").val();
                                                                        if(apli8 != ""){
                                                                            var ooo15  = ($("#idInputV15").val());
                                                                            var xooo15 = parseFloat(ooo15.replace(/\,/g, ''));
                                                                            var aut15  = ($("#autoV15").val());
                                                                            var xaut15 = parseFloat(aut15.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi15").val());
                                                                            if(ind == 3){
                                                                                xooo15 = xooo15 * -1;
                                                                            } 
                                                                            if(xaut15 ==""){
                                                                                    xaut15 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo15 = 0;
                                                                            var xaut15 = 0; 
                                                                        }

                                                                        var apli9 = $("#apdes16").val();
                                                                        if(apli9 != ""){
                                                                            var ooo16  = ($("#idInputV16").val());
                                                                            var xooo16 = parseFloat(ooo16.replace(/\,/g, ''));
                                                                            var aut16  = ($("#autoV16").val());
                                                                            var xaut16 = parseFloat(aut16.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi16").val());
                                                                            if(ind == 3){
                                                                                xooo16 = xooo16 * -1;
                                                                            } 
                                                                            if(xaut16 ==""){
                                                                                    xaut16 = 0;
                                                                            }
                                                                        }else{
                                                                            var xooo16 = 0;
                                                                            var xaut16 = 0; 
                                                                        }

                                                                        var apli10 = $("#apdes17").val();
                                                                        if(apli10 != ""){
                                                                            var ooo17  = ($("#idInputV17").val());
                                                                            var xooo17 = parseFloat(ooo17.replace(/\,/g, ''));
                                                                            var aut17  = ($("#autoV17").val());
                                                                            var xaut17 = parseFloat(aut17.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi17").val());
                                                                            if(ind == 3){
                                                                                xooo17 = xooo17 * -1;
                                                                               
                                                                            } 
                                                                             if(xaut17 ==""){
                                                                                    xaut17 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo17 = 0;
                                                                            var xaut17 = 0; 
                                                                        }

                                                                        var apli12 = $("#apdes18").val();
                                                                        if(apli12 != ""){
                                                                            var ooo18  = ($("#idInputV18").val());
                                                                            var xooo18 = parseFloat(ooo18.replace(/\,/g, ''));
                                                                            var aut18  = ($("#autoV18").val());
                                                                            var xaut18 = parseFloat(aut18.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi18").val());
                                                                            if(ind == 3){
                                                                                xooo18 = xooo18 * -1;
                                                                            } 
                                                                             if(xaut18 ==""){
                                                                                    xaut18 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo18 = 0;
                                                                            var xaut18 = 0; 
                                                                        }

                                                                        var apli13 = $("#apdes19").val();
                                                                        if(apli13 != ""){
                                                                            var ooo19  = ($("#idInputV19").val());
                                                                            var xooo19 = parseFloat(ooo19.replace(/\,/g, ''));
                                                                            var aut19  = ($("#autoV19").val());
                                                                            var xaut19 = parseFloat(aut19.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi19").val());
                                                                            if(ind == 3){
                                                                                xooo19 = xooo19 * -1;
                                                                            } 
                                                                            if(xaut19 ==""){
                                                                                    xaut19 = 0;
                                                                                }

                                                                        }else{
                                                                            var xooo19 = 0;
                                                                            var xaut19 = 0; 
                                                                        }

                                                                        var apli14 = $("#apdes20").val();
                                                                        if(apli14 != ""){
                                                                            var ooo20  = ($("#idInputV20").val());
                                                                             if (isNaN(ooo20)) {
                                                                            return 0;
                                                                            }
                                                                            var xooo20 = parseFloat(ooo20.replace(/\,/g, ''));
                                                                            var aut20  = ($("#autoV20").val());
                                                                            var xaut20 = parseFloat(aut20.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi20").val());
                                                                            if(ind == 3){
                                                                                xooo20 = xooo20 * -1;
                                                                            } 
                                                                             if(xaut20 ==""){
                                                                                 xaut20 = 0;
                                                                              }
                                                                        }else{
                                                                            var xooo20 = 0;
                                                                            var xaut20 = 0; 
                                                                        }

                                                                        var apli15 = $("#apdes21").val();
                                                                        if(apli15 != ""){
                                                                            var ooo21  = ($("#idInputV21").val());
                                                                            var xooo21 = parseFloat(ooo21.replace(/\,/g, ''));
                                                                            var aut21  = ($("#autoV21").val());
                                                                            var xaut21 = parseFloat(aut21.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi21").val());
                                                                            if(ind == 3){
                                                                                xooo21 = xooo21 * -1;
                                                                            } 
                                                                            if(xaut21 ==""){
                                                                                    xaut21 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo21 = 0;
                                                                            var xaut21 = 0; 
                                                                        }

                                                                        var apli16 = $("#apdes22").val();
                                                                        if(apli16 != ""){
                                                                            var ooo22  = ($("#idInputV22").val());
                                                                            var xooo22 = parseFloat(ooo22.replace(/\,/g, ''));
                                                                            var aut22  = ($("#autoV22").val());
                                                                            var xaut22 = parseFloat(aut22.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi22").val());
                                                                            if(ind == 3){
                                                                                xooo22 = xooo22 * -1;
                                                                            } 
                                                                             if(xaut22 ==""){
                                                                                    xaut22 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo22 = 0;
                                                                            var xaut22 = 0; 
                                                                        }

                                                                        var apli17 = $("#apdes23").val();
                                                                        if(apli17 != ""){
                                                                            var ooo23  = ($("#idInputV23").val());
                                                                            var xooo23 = parseFloat(ooo23.replace(/\,/g, ''));
                                                                            var aut23  = ($("#autoV23").val());
                                                                            var xaut23 = parseFloat(aut23.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi23").val());
                                                                            if(ind == 3){
                                                                                xooo23 = xooo23 * -1;
                                                                            } 
                                                                            if(xaut23 ==""){
                                                                                    xaut23 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo23 = 0;
                                                                            var xaut23 = 0; 
                                                                        }

                                                                        var apli18 = $("#apdes24").val();
                                                                        if(apli18 != ""){
                                                                            var ooo24  = ($("#idInputV24").val());
                                                                            var xooo24 = parseFloat(ooo24.replace(/\,/g, ''));
                                                                            var aut24  = ($("#autoV24").val());
                                                                            var xaut16 = parseFloat(aut24.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi24").val());
                                                                            if(ind == 3){
                                                                                xooo24 = xooo24 * -1;
                                                                            }
                                                                            if(xaut24 ==""){
                                                                                    xaut24 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo24 = 0;
                                                                            var xaut24 = 0; 
                                                                        }

                                                                        var apli19 = $("#apdes25").val();
                                                                        if(apli17 != ""){
                                                                            var ooo25  = ($("#idInputV25").val());
                                                                            var xooo25 = parseFloat(ooo25.replace(/\,/g, ''));
                                                                            var aut25  = ($("#autoV25").val());
                                                                            var xaut25 = parseFloat(aut25.replace(/\,/g, ''));
                                                                            var ind = parseFloat($("#indi25").val());
                                                                            if(ind == 3){
                                                                                xooo25 = xooo25 * -1;
                                                                            } 
                                                                             if(xaut25 ==""){
                                                                                    xaut25 = 0;
                                                                                }
                                                                        }else{
                                                                            var xooo25 = 0;
                                                                            var xaut25 = 0; 
                                                                        }

                                                                        TLID = xooo9 + xooo10 + xooo11 + xooo12 + xooo13 + xooo14 + xooo15 + xooo16 + xooo17 + xooo18 + xooo19 + xooo20 + xooo21 + xooo22 + xooo23 + xooo24 + xooo25;

                                                                        TLIDA = xaut9 + xaut10 + xaut11 + xaut12 + xaut13 + xaut14 + xaut15 + xaut16 + xaut17 + xaut18 + xaut19 + xaut20 + xaut21 + xaut22 + xaut23 + xaut24 + xaut25; 

                                                                     

                                                                     var TTTD = TBGD + TLID;                
                                                                     var TTTDA = TLIDA;


                                                                    
                                                                    document.getElementById("BaseDesc").value=TTTD;
                                                                    document.getElementById("BaseDescAut").value=TTTDA;
                                                                        
                                                                    });
                                                                </script> 

                                                                <script>
                                                                    $("#idInputV25").blur(function(){
                                                                        
                                                                        var apli1 = $("#apint9").val();
                                                                        if(apli1 != ""){
                                                                            var ooo9  = ($("#idInputV9").val());
                                                                            var xooo9 = parseFloat(ooo9.replace(/\,/g, ''));
                                                                            var aut9  = $("#autoV9").val();
                                                                            var ind   = parseFloat($("#indi9").val());
                                                                            if(ind == 3){
                                                                                xooo9 = xooo9 * -1;
                                                                                
                                                                                if(aut9 ==""){
                                                                                    xaut9 = 0;
                                                                                }else{
                                                                                    var aut9  = ($("#autoV9").val());
                                                                                    var xaut9 = parseFloat(aut9.replace(/\,/g, ''));
                                                                                    xaut9 = xaut9 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut9  = ($("#autoV9").val());
                                                                                var xaut9 = parseFloat(aut9.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo9 = 0;
                                                                            var xaut9 = 0; 
                                                                        }

                                                                        var apli2 = $("#apint10").val();
                                                                        if(apli2 != ""){
                                                                            var ooo10  = ($("#idInputV10").val());
                                                                            var xooo10 = parseFloat(ooo10.replace(/\,/g, ''));
                                                                            var aut10  = $("#autoV10").val();
                                                                            var ind    = parseFloat($("#indi10").val());
                                                                            if(ind == 3){
                                                                                xooo10 = xooo10 * -1;
                                                                                
                                                                                if(aut10 ==""){
                                                                                    xaut10 = 0;
                                                                                }else{
                                                                                    var aut10  = ($("#autoV10").val());
                                                                                    var xaut10 = parseFloat(aut10.replace(/\,/g, ''));
                                                                                    xaut10  = xaut10 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut10  = ($("#autoV10").val());
                                                                                var xaut10 = parseFloat(aut10.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo10 = 0;
                                                                            var xaut10 = 0; 
                                                                        }

                                                                        var apli3 = $("#apint11").val();
                                                                        if(apli3 != ""){
                                                                            var ooo11  = ($("#idInputV11").val());
                                                                            var xooo11 = parseFloat(ooo11.replace(/\,/g, ''));
                                                                            var aut11  = $("#autoV11").val();
                                                                            var ind    = parseFloat($("#indi11").val());
                                                                            if(ind == 3){
                                                                                xooo11 = xooo11 * -1;
                                                                                
                                                                                if(aut11 ==""){
                                                                                    xaut11 = 0;
                                                                                }else{
                                                                                    var aut11  = ($("#autoV11").val());
                                                                                    var xaut11 = parseFloat(aut11.replace(/\,/g, ''));
                                                                                    xaut11  = xaut11 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut11  = ($("#autoV11").val());
                                                                                var xaut11 = parseFloat(aut11.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo11 = 0;
                                                                            var xaut11 = 0; 
                                                                        }

                                                                        var apli4 = $("#apint12").val();
                                                                        if(apli4 != ""){
                                                                            var ooo12  = ($("#idInputV12").val());
                                                                            var xooo12 = parseFloat(ooo12.replace(/\,/g, ''));
                                                                            var aut12  = $("#autoV12").val();
                                                                            var ind    = parseFloat($("#indi12").val());
                                                                            if(ind == 3){
                                                                                xooo12 = xooo12 * -1;
                                                                                
                                                                                if(aut12 ==""){
                                                                                    xaut12 = 0;
                                                                                }else{
                                                                                    var aut12  = ($("#autoV12").val());
                                                                                    var xaut12 = parseFloat(aut12.replace(/\,/g, ''));
                                                                                    xaut12  = xaut12 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut12  = ($("#autoV12").val());
                                                                                var xaut12 = parseFloat(aut12.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo12 = 0;
                                                                            var xaut12 = 0; 
                                                                        }

                                                                        var apli5 = $("#apint13").val();
                                                                        if(apli5 != ""){
                                                                            var ooo13  = ($("#idInputV13").val());
                                                                            var xooo13 = parseFloat(ooo13.replace(/\,/g, ''));
                                                                            var aut13  = $("#autoV13").val();
                                                                            var ind    = parseFloat($("#indi13").val());
                                                                            if(ind == 3){
                                                                                xooo13 = xooo13 * -1;
                                                                               
                                                                                if(aut13 ==""){
                                                                                    xaut13 = 0;
                                                                                }else{
                                                                                    var aut13  = ($("#autoV13").val());
                                                                                    var xaut13 = parseFloat(aut13.replace(/\,/g, ''));
                                                                                    xaut13  = xaut13 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut13  = ($("#autoV13").val());
                                                                                var xaut13 = parseFloat(aut13.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo13 = 0;
                                                                            var xaut13 = 0; 
                                                                        }

                                                                        var apli6 = $("#apint14").val();
                                                                        if(apli6 != ""){
                                                                            var ooo14  = ($("#idInputV14").val());
                                                                            var xooo14 = parseFloat(ooo14.replace(/\,/g, ''));
                                                                            var aut14  = $("#autoV14").val();
                                                                            var ind = parseFloat($("#indi14").val());
                                                                            if(ind == 3){
                                                                                xooo14 = xooo14 * -1;
                                                                                
                                                                                if(aut14 ==""){
                                                                                    xaut14 = 0;
                                                                                }else{
                                                                                    var aut14  = ($("#autoV14").val());
                                                                                    var xaut14 = parseFloat(aut14.replace(/\,/g, ''));
                                                                                    xaut14  = xaut14 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut14  = ($("#autoV14").val());
                                                                                var xaut14 = parseFloat(aut14.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo14 = 0;
                                                                            var xaut14 = 0; 
                                                                        }
                                                                                                                                              

                                                                        var apli8 = $("#apint15").val();
                                                                        if(apli8 != ""){
                                                                            var ooo15  = ($("#idInputV15").val());
                                                                            var xooo15 = parseFloat(ooo15.replace(/\,/g, ''));
                                                                            var aut15  = $("#autoV15").val();
                                                                            var ind    = parseFloat($("#indi15").val());
                                                                            if(ind == 3){
                                                                                xooo15 = xooo15 * -1;
                                                                                
                                                                                if(aut15 ==""){
                                                                                    xaut15 = 0;
                                                                                }else{
                                                                                    var aut15  = ($("#autoV15").val());
                                                                                    var xaut15 = parseFloat(aut15.replace(/\,/g, ''));
                                                                                    xaut15  = xaut15 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut15  = ($("#autoV15").val());
                                                                                var xaut15 = parseFloat(aut15.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo15 = 0;
                                                                            var xaut15 = 0; 
                                                                        }

                                                                        var apli9 = $("#apint16").val();
                                                                        if(apli9 != ""){
                                                                            var ooo16  = ($("#idInputV16").val());
                                                                            var xooo16 = parseFloat(ooo16.replace(/\,/g, ''));
                                                                            var aut16  = $("#autoV16").val();
                                                                            var ind    = parseFloat($("#indi16").val());
                                                                            if(ind == 3){
                                                                                xooo16 = xooo16 * -1;
                                                                                
                                                                                if(aut16 == "" || aut16 == " "){
                                                                                    xaut16 = 0;
                                                                                }else{
                                                                                    var aut16  = ($("#autoV16").val());
                                                                                    var xaut16 = parseFloat(aut16.replace(/\,/g, ''));
                                                                                    xaut16  = xaut16 * -1;
                                                                                }
                                                                                
                                                                            }else{
                                                                                var aut16  = ($("#autoV16").val());
                                                                                var xaut16 = parseFloat(aut16.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo16 = 0;
                                                                            var xaut16 = 0; 
                                                                        }

                                                                        var apli10 = $("#apint17").val();
                                                                        if(apli10 != ""){
                                                                            var ooo17  = ($("#idInputV17").val());
                                                                            var xooo17 = parseFloat(ooo17.replace(/\,/g, ''));
                                                                            var aut17  = $("#autoV17").val();
                                                                            var ind    = parseFloat($("#indi17").val());
                                                                            if(ind == 3){
                                                                                xooo17 = xooo17 * -1;
                                                                                
                                                                                if(aut17 ==""){
                                                                                    xaut17 = 0;
                                                                                }else{
                                                                                    var aut17  = ($("#autoV17").val());
                                                                                    var xooo17 = parseFloat(ooo17.replace(/\,/g, ''));
                                                                                    xaut17  = xaut17 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut17  = ($("#autoV17").val());
                                                                                var xaut17 = parseFloat(aut17.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo17 = 0;
                                                                            var xaut17 = 0; 
                                                                        }

                                                                        var apli12 = $("#apint18").val();
                                                                        if(apli12 != ""){
                                                                            var ooo18  = ($("#idInputV18").val());
                                                                            var xooo18 = parseFloat(ooo18.replace(/\,/g, ''));
                                                                            var aut18  = $("#autoV18").val();
                                                                            var ind    = parseFloat($("#indi18").val());
                                                                            if(ind == 3){
                                                                                xooo18 = xooo18 * -1;
                                                                                
                                                                                if(aut18 ==""){
                                                                                    xaut18 = 0;
                                                                                }else{
                                                                                    var aut18  = ($("#autoV18").val());
                                                                                    var xaut18 = parseFloat(aut18.replace(/\,/g, ''));
                                                                                    xaut18  = xaut18 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut18  = ($("#autoV18").val());
                                                                                var xaut18 = parseFloat(aut18.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo18 = 0;
                                                                            var xaut18 = 0; 
                                                                        }

                                                                        var apli13 = $("#apint19").val();
                                                                        if(apli13 != ""){
                                                                            var ooo19  = ($("#idInputV19").val());
                                                                            var xooo19 = parseFloat(ooo19.replace(/\,/g, ''));
                                                                            var aut19  = $("#autoV19").val();
                                                                            var ind    = parseFloat($("#indi19").val());
                                                                            if(ind == 3){
                                                                                xooo19 = xooo19 * -1;
                                                                                
                                                                                if(aut19 ==""){
                                                                                    xaut19 = 0;
                                                                                }else{
                                                                                    var aut19  = ($("#autoV19").val());
                                                                                    var xaut19 = parseFloat(aut19.replace(/\,/g, ''));
                                                                                    xaut19  = xaut19 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut19  = ($("#autoV19").val());
                                                                                var xaut19 = parseFloat(aut19.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo19 = 0;
                                                                            var xaut19 = 0; 
                                                                        }

                                                                        var apli14 = $("#apint20").val();
                                                                        if(apli14 != ""){
                                                                            var ooo20  = ($("#idInputV20").val());
                                                                            var xooo20 = parseFloat(ooo20.replace(/\,/g, ''));
                                                                            var aut20  = $("#autoV20").val();
                                                                            var ind    = parseFloat($("#indi20").val());
                                                                            if(ind == 3){
                                                                                xooo20 = xooo20 * -1;
                                                                                
                                                                                if(aut20 ==""){
                                                                                    xaut20 = 0;
                                                                                }else{
                                                                                    var aut20  = ($("#autoV20").val());
                                                                                    var xaut20 = parseFloat(aut20.replace(/\,/g, ''));
                                                                                    xaut20  = xaut20 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut20  = ($("#autoV20").val());
                                                                                var xaut20 = parseFloat(aut20.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo20 = 0;
                                                                            var xaut20 = 0; 
                                                                        }

                                                                        var apli15 = $("#apint21").val();
                                                                        if(apli15 != ""){
                                                                            var ooo21  = ($("#idInputV21").val());
                                                                            var xooo21 = parseFloat(ooo21.replace(/\,/g, ''));
                                                                            var aut21  = $("#autoV21").val();
                                                                            var ind    = parseFloat($("#indi21").val());
                                                                            if(ind == 3){
                                                                                xooo21 = xooo21 * -1;
                                                                                
                                                                                if(aut21 ==""){
                                                                                    xaut21 = 0;
                                                                                }else{
                                                                                    var aut21  = ($("#autoV21").val());
                                                                                    var xaut21 = parseFloat(aut21.replace(/\,/g, ''));
                                                                                    xaut21  = xaut21 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut21  = ($("#autoV21").val());
                                                                                var xaut21 = parseFloat(aut21.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo21 = 0;
                                                                            var xaut21= 0; 
                                                                        }

                                                                        var apli16 = $("#apint22").val();
                                                                        if(apli16 != ""){
                                                                            var ooo22  = ($("#idInputV22").val());
                                                                            var xooo21 = parseFloat(ooo21.replace(/\,/g, ''));
                                                                            var aut21  = $("#autoV21").val();
                                                                            var ind    = parseFloat($("#indi22").val());
                                                                            if(ind == 3){
                                                                                xooo22 = xooo22 * -1;
                                                                                
                                                                                if(aut22 ==""){
                                                                                    xaut22 = 0;
                                                                                }else{
                                                                                    var aut22  = ($("#autoV22").val());
                                                                                    var xaut22 = parseFloat(aut22.replace(/\,/g, ''));
                                                                                    xaut22 = xaut22 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut22  = ($("#autoV22").val());
                                                                                var xaut22 = parseFloat(aut22.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo22 = 0;
                                                                            var xaut22 = 0; 
                                                                        
                                                                        }

                                                                        var apli17 = $("#apint23").val();
                                                                        if(apli17 != ""){
                                                                            var ooo23  = ($("#idInputV23").val());
                                                                            var xooo23 = parseFloat(ooo23.replace(/\,/g, ''));
                                                                            var aut23  = $("#autoV23").val();
                                                                            var ind    = parseFloat($("#indi23").val());
                                                                            if(ind == 3){
                                                                                xooo23 = xooo23 * -1;
                                                                                
                                                                                if(aut23 ==""){
                                                                                    xaut23 = 0;
                                                                                }else{
                                                                                    var aut23  = ($("#autoV23").val());
                                                                                    var xaut23 = parseFloat(aut23.replace(/\,/g, ''));
                                                                                    xaut23  = xaut23 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut23  = ($("#autoV23").val());
                                                                                var xaut23 = parseFloat(aut23.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo23 = 0;
                                                                            var xaut23 = 0; 
                                                                        }

                                                                        var apli18 = $("#apint24").val();
                                                                        if(apli18 != ""){
                                                                            var ooo24  = ($("#idInputV24").val());
                                                                            var xooo24 = parseFloat(ooo24.replace(/\,/g, ''));
                                                                            var aut24  = $("#autoV24").val();
                                                                            var ind    = parseFloat($("#indi24").val());
                                                                            if(ind == 3){
                                                                                xooo24 = xooo24 * -1;
                                                                                
                                                                                if(aut24 ==""){
                                                                                    xaut24 = 0;
                                                                                }else{
                                                                                    var aut24  = ($("#autoV24").val());
                                                                                    var xaut24 = parseFloat(aut24.replace(/\,/g, ''));
                                                                                    xaut24  = xaut24 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut24  = ($("#autoV24").val());
                                                                                var xaut24 = parseFloat(aut24.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo24 = 0;
                                                                            var xaut24 = 0; 
                                                                        }

                                                                        var apli19 = $("#apint25").val();
                                                                        if(apli19 != ""){
                                                                            var ooo25  = ($("#idInputV25").val());
                                                                            var xooo25 = parseFloat(ooo25.replace(/\,/g, ''));
                                                                            var aut25  = $("#autoV25").val();
                                                                            var ind    = parseFloat($("#indi25").val());
                                                                            if(ind == 3){
                                                                                xooo25 = xooo25 * -1;
                                                                                
                                                                                if(aut25 ==""){
                                                                                    xaut25 = 0;
                                                                                }else{
                                                                                    var aut25  = ($("#autoV25").val());
                                                                                    var xaut25 = parseFloat(aut25.replace(/\,/g, ''));
                                                                                    xaut25  = xaut25 * -1;
                                                                                }
                                                                            }else{
                                                                                var aut25  = ($("#autoV25").val());
                                                                                var xaut25 = parseFloat(aut25.replace(/\,/g, ''));
                                                                            }
                                                                        }else{
                                                                            var xooo25 = 0;
                                                                            var xaut25 = 0; 
                                                                        }

                                                                        TLII = xooo9 + xooo10 + xooo11 + xooo12 + xooo13 + xooo14 + xooo15 + xooo16 + xooo17 + xooo18 + xooo19 + xooo20 + xooo21 + xooo22 + xooo23 + xooo24 + xooo25;

                                                                        TLIIA = xaut9 + xaut10 + xaut11 + xaut12 + xaut13 + xaut14 + xaut15 + xaut16 + xaut17 + xaut18 + xaut19 + xaut20 + xaut21 + xaut22 + xaut23 + xaut24 + xaut25; 
                                                                        
                                                                        console.log("base Int Auto: "+TLIIA);
                                                                        console.log("aut9 I: "+xaut9);
                                                                        console.log("aut10 I: "+xaut10);
                                                                        console.log("aut11 I: "+xaut11);
                                                                        console.log("aut12 I: "+xaut12);
                                                                        console.log("aut13 I: "+xaut13);
                                                                        console.log("aut14 I: "+xaut14);
                                                                        console.log("aut15 I: "+xaut15);
                                                                        console.log("aut16 I: "+xaut16);
                                                                        console.log("aut17 I: "+xaut17);
                                                                        console.log("aut18 I: "+xaut18);
                                                                        console.log("aut19 I: "+xaut19);
                                                                        console.log("aut20 I: "+xaut20);
                                                                        console.log("aut21 I: "+xaut21);
                                                                        console.log("aut22 I: "+xaut22);
                                                                        console.log("aut23 I: "+xaut23);
                                                                        console.log("aut24 I: "+xaut24);
                                                                        console.log("aut25 I: "+xaut25);

                                                                        console.log("base Int Des: "+TLII);
                                                                        console.log("ooo9: "+xooo9);
                                                                        console.log("ooo10: "+xooo10);
                                                                        console.log("ooo11: "+xooo11);
                                                                        console.log("ooo12: "+xooo12);
                                                                        console.log("ooo13: "+xooo13);
                                                                        console.log("ooo14: "+xooo14);
                                                                        console.log("ooo15: "+xooo15);
                                                                        console.log("ooo16: "+xooo16);
                                                                        console.log("ooo17: "+xooo17);
                                                                        console.log("ooo18: "+xooo18);
                                                                        console.log("ooo19: "+xooo19);
                                                                        console.log("ooo20: "+xooo20);
                                                                        console.log("ooo21: "+xooo21);
                                                                        console.log("ooo22: "+xooo22);
                                                                        console.log("ooo23: "+xooo23);
                                                                        console.log("ooo24: "+xooo24);
                                                                        console.log("ooo25: "+xooo25);

                                                                   
                                                                    var TTTI = TBGI + TLII; 
                                                                   
                                                                    document.getElementById("BaseInteres").value=TTTI;                      
                                                                    document.getElementById("BaseIntcAut").value=TLIIA;
                                                                    });

                                                                    
                                                                </script> 
                                                                
                                                                <script>
                                                                    $("#idTIICCE1").blur(function(){
                                                                        var ccc = ($("#TI1").val());
                                                                        var porcB = ($("#param").val());
                                                                        var xccc = parseFloat(ccc.replace(/\,/g, ''));
                                                                        var d = 0;
                                                                        document.getElementById("idInputV9").value=formatV(xccc);
                                                                        document.getElementById("autoV9").value=formatV(xccc);
                                                                        document.getElementById("dif9").value=d;

                                                                        var val5  = ($("#idInputV9").val());
                                                                        var xval5 = parseFloat(val5.replace(/\,/g, ''));
                                                                        var por = (xval5 * 15)/100;
                                                                        if(por < 500){
                                                                            var x = Math.ceil(por / 1000) * 1000;
                                                                        }else{
                                                                            var x = Math.round(por / 1000) * 1000;
                                                                        }
                                                                        
                                                                        var dd = 0;

                                                                        document.getElementById("idInputV10").value=formatV(x);
                                                                        document.getElementById("autoV10").value=formatV(x); 
                                                                        document.getElementById("dif10").value=dd;

                                                                        //sobretasa bomberil
                                                                        var Sb = (xval5 * porcB) / 100;
                                                                        if(Sb < 500){
                                                                            var vsb = Math.ceil(Sb / 1000) * 1000;
                                                                        }else{
                                                                            var vsb = Math.round(Sb / 1000) * 1000;
                                                                        }
                                                                       	console.log("impuesto: "+val5);
                                                                       	console.log("por: "+vsb);
                                                                        var ds = 0;
                                                                        
                                                                        document.getElementById("idInputV13").value=formatV(vsb);
                                                                        document.getElementById("autoV13").value=formatV(vsb); 
                                                                        document.getElementById("dif13").value=ds;

                                                                    });


                                                                     $("#TI1").blur(function(){
                                                                        var ccc = ($("#TI1").val());
                                                                        var porcB = ($("#param").val());
                                                                        var xccc = parseFloat(ccc.replace(/\,/g, ''));
                                                                        var d = 0;
                                                                        document.getElementById("idInputV9").value=formatV(xccc);
                                                                        document.getElementById("autoV9").value=formatV(xccc);
                                                                        document.getElementById("dif9").value=d;

                                                                        var val5  = ($("#idInputV9").val());
                                                                        var xval5 = parseFloat(val5.replace(/\,/g, ''));
                                                                        var por = (xval5 * 15)/100;
                                                                        if(por < 500){
                                                                            var x = Math.ceil(por / 1000) * 1000;
                                                                        }else{
                                                                            var x = Math.round(por / 1000) * 1000;
                                                                        }
                                                                        
                                                                        var dd = 0;

                                                                        document.getElementById("idInputV10").value=formatV(x);
                                                                        document.getElementById("autoV10").value=formatV(x); 
                                                                        document.getElementById("dif10").value=dd;

                                                                        //sobretasa bomberil
                                                                        var Sb = (xval5 * porcB) / 100;
                                                                        if(Sb < 500){
                                                                            var vsb = Math.ceil(Sb / 1000) * 1000;
                                                                        }else{
                                                                            var vsb = Math.round(Sb / 1000) * 1000;
                                                                        }
                                                                        console.log("impuesto: "+val5);
                                                                        console.log("por: "+vsb);
                                                                        var ds = 0;
                                                                        
                                                                        document.getElementById("idInputV13").value=formatV(vsb);
                                                                        document.getElementById("autoV13").value=formatV(vsb); 
                                                                        document.getElementById("dif13").value=ds;

                                                                    });

                                                                    $("#idInputV13").change(function(){
                                                                        var val1 = ($("#idInputV13").val());
                                                                        var val2 = ($("#autoV13").val());
                                                                        
                                                                        var xval1 = parseFloat(val1.replace(/\,/g, ''));
                                                                        var xval2 = parseFloat(val2.replace(/\,/g, ''));
                                                                        
                                                                        var dif = xval1 - xval2;
                                                                        if(dif < 0){
                                                                            dif = dif * -1;
                                                                        }
                                                                        
                                                                        document.getElementById("dif13").value=dif;
                                                                    });

                                                                    $("#idInputV9").change(function(){
                                                                        var val1 = ($("#idTIICCE1").val());
                                                                        var val2 = ($("#TI1").val());
                                                                        var val3 = ($("#idInputV9").val());
                                                                        var val4 = ($("#autoV9").val());
                                                                        var val5 = ($("#autoV10").val());
                                                                        
                                                                        var xval1 = parseFloat(val1.replace(/\,/g, ''));
                                                                        var xval2 = parseFloat(val2.replace(/\,/g, ''));
                                                                        var xval3 = parseFloat(val3.replace(/\,/g, ''));
                                                                        var xval4 = parseFloat(val4.replace(/\,/g, ''));
                                                                        var xval5 = parseFloat(val5.replace(/\,/g, ''));
                                                                        
                                                                        //var tot = val1 + val2;
                                                                        var dif = xval3 - xval4;
                                                                        var por = (xval3 * 15)/100; 
                                                                        if(por < 500){
                                                                            var P = Math.ceil(por / 1000) * 1000;
                                                                        }else{
                                                                            var P = Math.round(por / 1000) * 1000;
                                                                        }
                                                                        console.log("DIF "+dif);
                                                                        if(dif < 0){
                                                                            dif = dif * -1;
                                                                        }
                                                                        
                                                                        dif1 = P - xval5;
                                                                        if(dif1 < 0){
                                                                            dif1 = dif1 * -1;
                                                                        }
                                                                        //document.getElementById("autoV9").value=tot; 
                                                                        document.getElementById("dif9").value=dif; 
                                                                        document.getElementById("idInputV10").value=formatV(P);
                                                                        document.getElementById("dif10").value=dif1;


                                                                        
                                                                         
                                                                    });
                                                                    
                                                                    $("#idInputV10").change(function(){
                                                                       
                                                                        var val3 = ($("#autoV10").val());
                                                                        var val4 = ($("#idInputV10").val());
                                                                        
                                                                        var xval3 = parseFloat(val3.replace(/\,/g, ''));
                                                                        var xval4 = parseFloat(val4.replace(/\,/g, ''));
                                                                        
                                                                        var por = (xval3 * 15)/100;
                                                                        var x = Math.round(por / 1000) * 1000; 
                                                                        var dif = xval4 - xval3;  

                                                                        //document.write(dif);
                                                                        if(dif < 0){
                                                                            dif = dif * -1;
                                                                        }
                                                                        //document.getElementById("autoV10").value=x; 
                                                                        document.getElementById("dif10").value=dif; 
                                                                        
                                                                    });

                                                                    $("#idInputV14").blur(function(){
                                                                        var val1 = ($("#idInputV9").val());
                                                                        var val2 = ($("#idInputV10").val());
                                                                        var val3 = ($("#idInputV11").val());
                                                                        var val4 = ($("#idInputV12").val());
                                                                        var val5 = ($("#idInputV13").val());
                                                                        var val6 = ($("#idInputV14").val());
                                                                        
                                                                        var xval1 = parseFloat(val1.replace(/\,/g, ''));
                                                                        var xval2 = parseFloat(val2.replace(/\,/g, ''));
                                                                        var xval3 = parseFloat(val3.replace(/\,/g, ''));
                                                                        var xval4 = parseFloat(val4.replace(/\,/g, ''));
                                                                        var xval5 = parseFloat(val5.replace(/\,/g, ''));
                                                                        var xval6 = parseFloat(val6.replace(/\,/g, ''));
                                                                        
                                                                        var dif2 = 0;
                                                                         if(isNaN(xval6)){
                                                                                return 0;
                                                                            }
                                                                        
                                                                        document.getElementById("autoV14").value=formatV(xval6); 
                                                                        document.getElementById("dif14").value=dif2; 
                                                                        
                                                                        var val7  = ($("#autoV9").val());
                                                                        var val8  = ($("#autoV10").val());
                                                                        var val9  = ($("#autoV11").val());
                                                                        var val10 = ($("#autoV12").val());
                                                                        var val11 = ($("#autoV13").val());
                                                                        var val12 = ($("#autoV14").val());
                                                                        
                                                                        var xval7  = parseFloat(val7.replace(/\,/g, ''));
                                                                        var xval8  = parseFloat(val8.replace(/\,/g, ''));
                                                                        var xval9  = parseFloat(val9.replace(/\,/g, ''));
                                                                        var xval10 = parseFloat(val10.replace(/\,/g, ''));
                                                                        var xval11 = parseFloat(val11.replace(/\,/g, ''));
                                                                        var xval12 = parseFloat(val12.replace(/\,/g, ''));
                                                                        
                                                                        var totD = xval1 + xval2 + xval3 + xval4 + xval5 + xval6;
                                                                        var totS = xval7 + xval8 + xval9 + xval10 + xval11 + xval12;
                                                                        var dif = totD - totS;
                                                                         if (isNaN(totD)) {
                                                                         return 0;
                                                                         }
                                                                         if (isNaN(totS)) {
                                                                         return 0;
                                                                         }
                                                                         if (isNaN(dif)) {
                                                                         return 0;
                                                                         }
                                                                        
                                                                        console.log("sistema: "+totS);
                                                                        console.log("20: "+xval7);
                                                                        console.log("21: "+xval8);
                                                                        console.log("22: "+xval9);
                                                                        console.log("23: "+xval10);
                                                                        console.log("24: "+xval11);
                                                                        console.log("25: "+xval12);

                                                                        if(dif < 0){
                                                                            dif = dif * -1;
                                                                        }
                                                                        

                                                                        document.getElementById("idInputV15").value=formatV(totD); 
                                                                        document.getElementById("autoV15").value=formatV(totS); 
                                                                        document.getElementById("dif15").value=dif; 
                                                                    });

                                                                    $("#idInputV15").change(function(){
                                                                        //var val1 = ($("#idInputV9").val());
                                                                        //var val2 = ($("#idInputV10").val());
                                                                        //var val3 = ($("#idInputV11").val());
                                                                        //var val4 = ($("#idInputV12").val());
                                                                        //var val5 = ($("#idInputV13").val());
                                                                        //var val6 = ($("#idInputV14").val());
                                                                        var val7 = ($("#idInputV15").val());
                                                                        var val8 = ($("#autoV15").val());
                                                                        
                                                                        //var xval1 = parseFloat(val1.replace(/\,/g, ''));
                                                                        //var xval2 = parseFloat(val2.replace(/\,/g, ''));
                                                                        //var xval3 = parseFloat(val3.replace(/\,/g, ''));
                                                                        //var xval4 = parseFloat(val4.replace(/\,/g, ''));
                                                                        //var xval5 = parseFloat(val5.replace(/\,/g, ''));
                                                                        //var xval6 = parseFloat(val6.replace(/\,/g, ''));
                                                                        var xval7 = parseFloat(val7.replace(/\,/g, ''));
                                                                        var xval8 = parseFloat(val8.replace(/\,/g, ''));
                                                                        
                                                                        //var tot = xval1 + xval2 + xval3 + xval4 + xval5 + xval6;
                                                                        var dif = xval8 - xval7;

                                                                        if(dif < 0){
                                                                            dif = dif * -1;
                                                                        }
                                                                        //document.getElementById("autoV15").value=formatV(tot); 
                                                                        document.getElementById("dif15").value=dif; 
                                                                    });

                                                                    $("#idInputV19").blur(function(){
                                                                        var imp  = ($("#idInputV9").val());
                                                                        var anti = parseFloat($("#antici20").val());

                                                                        var ximp = parseFloat(imp.replace(/\,/g, ''));
                                                                        var cero = 0;
                                                                        

                                                                        console.log("anticipo: "+anti);
                                                                        if(anti == 1){
                                                                            var T  = (ximp * 25) / 100;
                                                                            if(T < 500){
                                                                                T = Math.ceil(T / 1000) * 1000;
                                                                            }else{
                                                                                T = Math.round(T / 1000) * 1000;
                                                                            }
                                                                        }else{
                                                                            T = 0;
                                                                        }
                                                                        
                                                                        document.getElementById("idInputV20").value=T;
                                                                        document.getElementById("autoV20").value=formatV(T);
                                                                     document.getElementById("dif20").value=0;
                                                                    });

                                                                    $("#idInputV20").change(function(){
                                                                        var imp  = ($("#idInputV20").val());
                                                                        var imp1 = ($("#autoV20").val());
                                                                        
                                                                        var ximp  = parseFloat(imp.replace(/\,/g, ''));
                                                                        var ximp1 = parseFloat(imp1.replace(/\,/g, ''));
                                                                        
                                                                        var T  = ximp - ximp1;

                                                                        if(T < 500){
                                                                            TT = Math.ceil(T / 1000) * 1000;
                                                                        }else{
                                                                            TT = Math.round(T / 1000) + 1000;
                                                                        }

                                                                        if(TT < 0){
                                                                            TT = TT * -1;
                                                                        }

                                                                        console.log("tt: "+TT);
                                                                        //document.getElementById("auto20").value=T;
                                                                        document.getElementById("dif20").value=TT;
                                                                    });

                                                                    $("#idInputV23").blur(function(){
                                                                        var val1 = ($("#idInputV15").val());
                                                                        var val2 = ($("#idInputV16").val());
                                                                        var val3 = ($("#idInputV17").val());
                                                                        var val4 = ($("#idInputV18").val());
                                                                        var val5 = ($("#idInputV19").val());
                                                                        var val6 = ($("#idInputV20").val());
                                                                        var val7 = ($("#idInputV21").val());
                                                                        var val8 = ($("#idInputV22").val());
                                                                        var val9 = ($("#idInputV23").val());
                                                                        
                                                                        var val10 = $("#autoV15").val();
                                                                        var val11 = $("#autoV16").val();
                                                                        var val12 = $("#autoV17").val();
                                                                        var val13 = $("#autoV18").val();
                                                                        var val14 = $("#autoV19").val();
                                                                        var val15 = $("#autoV20").val();
                                                                        var val16 = $("#autoV21").val();
                                                                        var val17 = $("#autoV22").val();
                                                                        var val18 = $("#autoV23").val();
                                                                        
                                                                        var xval1  = parseFloat(val1.replace(/\,/g, ''));
                                                                        var xval2  = parseFloat(val2.replace(/\,/g, ''));
                                                                        var xval3  = parseFloat(val3.replace(/\,/g, ''));
                                                                        var xval4  = parseFloat(val4.replace(/\,/g, ''));
                                                                        var xval5  = parseFloat(val5.replace(/\,/g, ''));
                                                                        var xval6  = parseFloat(val6.replace(/\,/g, ''));
                                                                        var xval7  = parseFloat(val7.replace(/\,/g, ''));
                                                                        var xval8  = parseFloat(val8.replace(/\,/g, ''));
                                                                        var xval9  = parseFloat(val9.replace(/\,/g, ''));
                                                                        
                                                                        if(val10 == ""){
                                                                            xval10 = 0;
                                                                        }else{
                                                                            var val10 = ($("#autoV15").val());
                                                                            var xval10 = parseFloat(val10.replace(/\,/g, ''));
                                                                        }
                                                                        
                                                                        if(val11 == ""){
                                                                            xval11 = 0;
                                                                        }else{
                                                                            var val11 = ($("#autoV16").val());
                                                                            var xval11 = parseFloat(val11.replace(/\,/g, ''));
                                                                        }

                                                                        if(val12 == ""){
                                                                            xval12 = 0;
                                                                        }else{
                                                                            var val12 = ($("#autoV17").val());
                                                                            var xval12 = parseFloat(val12.replace(/\,/g, ''));
                                                                        }

                                                                        if(val13 == ""){
                                                                            xval13 = 0;
                                                                        }else{
                                                                            var val13 = ($("#autoV18").val());
                                                                            var xval13 = parseFloat(val13.replace(/\,/g, ''));
                                                                        }

                                                                        if(val14 == ""){
                                                                            xval14 = 0;
                                                                        }else{
                                                                            var val14 = ($("#autoV19").val());
                                                                            var xval14 = parseFloat(val14.replace(/\,/g, ''));
                                                                        }

                                                                        if(val15 == ""){
                                                                            xval15 = 0;
                                                                        }else{
                                                                            var val15 = ($("#autoV20").val());
                                                                            var xval15 = parseFloat(val15.replace(/\,/g, ''));
                                                                        }

                                                                        if(val16 == ""){
                                                                            xval16 = 0;
                                                                        }else{
                                                                            var val16 = ($("#autoV21").val());
                                                                            var xval16 = parseFloat(val16.replace(/\,/g, ''));
                                                                        }

                                                                        if(val17 == ""){
                                                                            xval17 = 0;
                                                                        }else{
                                                                            var val17 = ($("#autoV22").val());
                                                                            var xval17 = parseFloat(val17.replace(/\,/g, ''));
                                                                        }

                                                                        if(val18 == ""){
                                                                            xval18 = 0;
                                                                        }else{
                                                                            var val18 = ($("#autoV23").val());
                                                                            var xval18 = parseFloat(val18.replace(/\,/g, ''));
                                                                        }

                                                                        var totD = xval1  - xval2  - xval3  - xval4  - xval5  + xval6  + xval7  + xval8  - xval9;
                                                                        var totS = xval10 - xval11 - xval12 - xval13 - xval14 + xval15 + xval16 + xval17 - xval18;
                                                                        
                                                                        console.log("v: "+totS);
                                                                        console.log("27: "+val10);
                                                                        console.log("28: "+val11);
                                                                        console.log("29: "+val12);
                                                                        console.log("30: "+val13);
                                                                        console.log("31: "+val14);
                                                                        console.log("32: "+val15);
                                                                        console.log("33: "+val16);
                                                                        console.log("34: "+val17);
                                                                        console.log("35: "+val18);
                                                                        var dif = totD - totS;
                                                                         


                                                                        if(dif < 0){
                                                                            dif = dif * -1;
                                                                        }

                                                                        
                                                                        if(totS >= 0 ){
                                                                            document.getElementById("idInputV24").value=formatV(totD);
                                                                            document.getElementById("autoV24").value=formatV(totS); 
                                                                            document.getElementById("autoV25").value=0;
                                                                            document.getElementById("dif24").value= dif;
                                                                        }else{
                                                                            document.getElementById("idInputV25").value=formatV(totD);
                                                                            document.getElementById("autoV25").value=formatV(totS);
                                                                            document.getElementById("autoV24").value=""; 
                                                                            document.getElementById("dif25").value= dif;    
                                                                        }
                                                                        
                                                                        
                                                                    });

                                                                    var val9 = parseInt($("#idInputV22").val());


                                                                    if(val9 >= 0){
                                                                        $("#idInputV22").change(function(){
                                                                            var val1 = parseInt($("#idInputV22").val());
                                                                            var val2 = parseInt($("#autoV22").val());


                                                                            if(val1 < 0){
                                                                                val1 = val1 * -1;
                                                                            }

                                                                            if(val2 < 0){
                                                                                val2 = val2 * -1;
                                                                            }
                                                                            
                                                                            var dif = val1 - val2;
                                                                            //document.write(val1,val2);
                                                                              if (isNaN(dif)) {
                                                                                 return 0;
                                                                                 }
                                                                            if(dif < 0){
                                                                                dif = dif * -1;
                                                                            }

                                                                            document.getElementById("dif22").value=dif;
                                                                        });
                                                                    }else{
                                                                        $("#idInputV23").change(function(){
                                                                            var val1 = ($("#idInputV23").val());
                                                                            //var val2 = ($("#autoV23").val());
                                                                            
                                                                            var xval1  = parseFloat(val1.replace(/\,/g, ''));
                                                                            //var xval2  = parseFloat(val2.replace(/\,/g, ''));
                                                                            
                                                                            var dif = 0;
                                                                            //document.write(val1,val2);
                                                                            if(dif < 0){
                                                                                dif = dif * -1;
                                                                            }
                                                                            document.getElementById("autoV23").value=formatV(xval1);
                                                                            document.getElementById("dif23").value=dif;
                                                                        });
                                                                    }
                                                                     
                                                                    $("#idInputV24").change(function(){
                                                                            var val1 = ($("#idInputV24").val());
                                                                            var val2 = ($("#autoV24").val());
                                                                            
                                                                            var xval1  = parseFloat(val1.replace(/\,/g, ''));
                                                                            var xval2  = parseFloat(val2.replace(/\,/g, ''));
                                                                            
                                                                            var dif = xval1 - xval2 ;
                                                                            //document.write(val1,val2);
                                                                            if(dif < 0){
                                                                                dif = dif * -1;
                                                                            }
                                                                            //document.getElementById("autoV24").value=formatV(xval1);
                                                                            document.getElementById("dif24").value=dif;
                                                                        });
                                                                    

                                                                    $("#idInputV25").blur(function(){
                                                                        var SC = ($("#idInputV24").val());
                                                                        var SF = ($("#idInputV25").val());
                                                                        
                                                                        var xSC  = parseFloat(SC.replace(/\,/g, ''));
                                                                        var xSF  = parseFloat(SF.replace(/\,/g, ''));
                                                                        
                                                                        var au1   = ($("#autoV24").val());
                                                                        var xau1  = parseFloat(au1.replace(/\,/g, ''));
                                                                        var au2 = $("#autoV25").val();
                                                                        if(au2 == ""){
                                                                            xau2 = 0;
                                                                        }else{
                                                                            var au2   = ($("#autoV25").val());
                                                                            var xau2  = parseFloat(au2.replace(/\,/g, ''));
                                                                        }
                                                                        var dr = xSC - xSF;
                                                                        
                                                                        var d = xau1 - xau2;
                                                                        var di = dr - d;
                                                                        if(di < 0){
                                                                            di = di * -1;
                                                                        }
                                                                        if(dr < 0 && d < 0 ){
                                                                            document.getElementById("idInputV26").value= 0;
                                                                            document.getElementById("autoV26").value= 0;
                                                                            document.getElementById("dif26").value= 0;
                                                                        }else{
                                                                            document.getElementById("idInputV26").value=formatV(SC);
                                                                            document.getElementById("autoV26").value= formatV(xau1);
                                                                            document.getElementById("dif26").value= 0;
                                                                        }

                                                                        
                                                                    });

                                                                </script>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                             
                                            <?php 
                                                $sqlFC="SELECT cc.id_unico,cc.descripcion,tc.nombre FROM gc_concepto_comercial cc 
                                                    LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                                                    WHERE tc.id_unico= 8"; 

                                                $qFC=$mysqli->query($sqlFC);
                                            ?>

                                            <!--6to F. Correcion -->
                                            <!--<div class="col-sm-12 col-md-12 col-lg-12 text-left">
                                                <div class="table-responsive" style="margin-top:-10px;">
                                                    <div class="table-responsive">
                                                        <table  class="table table-bordered"  cellspacing="0" width="100%">
                                                            <thead>
                                                                <th colspan="2">F. CORRECIÓN(Si no es correcto pase al Numeral 44)</th>
                                                                <th>VALOR</th>
                                                                <th>AUTOLIQUIDACION</th>
                                                                <th>DIFERENCIA</th>
                                                            </thead>
                                                            <tbody>
                                                                <?php /*
                                                                    while($rowFC=mysqli_fetch_row($qFC)){ ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?php echo $X++; ?>
                                                                            </td>
                                                                            <!--id_concepto Comercial-->
                                                                            <?php  
                                                                                $inputsValue++;
                                                                                $idInputCC="idInputCC".$inputsValue;
                                                                                $nameIdConceptoComercial="iidConceptoComercial".$inputsValue 
                                                                            ?>
                                                                            <input id="<?php echo $idInputCC ?>" name="<?php echo $nameIdConceptoComercial ?>" type="hidden" value="<?php echo $rowFC[0] ?>" >

                                                                            <!--descripcion concepto comercial-->
                                                                            <?php 
                                                                                $idInputD="idInputD".$inputsValue;
                                                                                $nameDescripcion="iDescripcion".$inputsValue 
                                                                            ?>
                                                                            <input id="<?php echo $idInputD ?>" name="<?php echo $nameDescripcion ?>" type="hidden" value="<?php echo $rowFC[1] ?>" >
                                                                            <td style="width: 60%"><?php echo ucwords(mb_strtolower($rowFC[1])) ?></td>
                                                                            <!--value detalle declaracion-->
                                                                            <?php
                                                                                $idInputV="idInputV".$inputsValue;
                                                                                $nameValue="iValue".$inputsValue;  //
                                                                            ?>

                                                                            <td><input id="<?php echo $idInputV ?>" name="<?php echo $nameValue ?>" value="" style="width: 100%" type="number"></td>
                                                                            <td><input id="" name="" value="" style="width: 100%" type="number" readonly></td>
                                                                            <td><input id="" name="" value="" style="width: 100%" type="number" readonly></td>
                                                                        </tr>
                                                                <?php 
                                                                    } */
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>-->
                                            <?php 
                                                $sqlF=  "SELECT cc.id_unico,cc.descripcion,tc.nombre FROM gc_concepto_comercial cc 
                                                        LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                                                        WHERE tc.nombre='F. CORRECCION'"; 

                                                $qF=$mysqli->query($sqlF);
                                            ?>
                                                
                                              
                                            <!--7tmo G.pago -->
                                            <?php 
                                                $sqlGP="SELECT cc.id_unico,cc.descripcion,tc.nombre FROM gc_concepto_comercial cc 
                                                        LEFT JOIN gc_tipo_comercio tc ON tc.id_unico=cc.tipo
                                                        WHERE tc.id_unico = 3"; 

                                                $qGP=$mysqli->query($sqlGP);
                                            ?>
                                            <div class="col-sm-12 col-md-12 col-lg-12 text-left client-form1" >
                                                
                                                <div class="table-responsive" style="margin-top:-10px;">
                                                    <table  class="table table-bordered"  cellspacing="0" width="100%">
                                                        <thead>
                                                            <th colspan="2" >G. PAGO</th>
                                                            <th>VALOR</th>
                                                            <th>AUTOLIQUIDACION</th>
                                                            <th>DIFERENCIA</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                               if($periodoG = "Periodo Gravable"){
                                                                 $periodoG = 0;
                                                               }

                                                               if($ViF = "Vigencia Comercial"){
                                                                $ViF = 0;
                                                               }

                                                                $val = $ViF - $periodoG;
                                                                $desIC = 0;
                                                                $intIC = 0;

                                                                $conDesc = "SELECT id_unico, codigo FROM  gc_concpeto_comercial WHERE apli_descu is not NULL";
                                                                $Cdesc = $mysqli->query($conDesc);


                                                                $conInte = "SELECT id_unico, codigo FROM gc_comcepto_comercial WHERE apli_inte is not NULL";
                                                                $Cinte = $mysqli->query($conInte);


                                                                if($val <= 1){
                                                                    $desIC;
                                                                }else{
                                                                    $intIC;
                                                                }

                                                                while($rowGP=mysqli_fetch_row($qGP)){ ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo $X++; ?>
                                                                        </td>
                                                                        <!--id_concepto Comercial-->
                                                                        <?php  
                                                                            $inputsValue++;
                                                                            $idInputCC="idInputCC".$inputsValue;
                                                                            $nameIdConceptoComercial="iidConceptoComercial".$inputsValue 
                                                                        ?>
                                                                        <input id="<?php echo $idInputCC ?>" name="<?php echo $nameIdConceptoComercial ?>" type="hidden" value="<?php echo $rowGP[0] ?>" >

                                                                        <!--descripcion concepto comercial-->
                                                                        <?php 
                                                                            $idInputD="idInputD".$inputsValue;
                                                                            $nameDescripcion="iDescripcion".$inputsValue 
                                                                        ?>
                                                                        <input id="<?php echo $idInputD ?>" name="<?php echo $nameDescripcion ?>" type="hidden" value="<?php echo $rowGP[1] ?>" >
                                                                        <td style="width: 60%"><?php echo ucwords(mb_strtolower($rowGP[1])) ?></td>

                                                                        <!--value detalle declaracion-->
                                                                        <?php
                                                                            $idInputV="idInputV".$inputsValue;
                                                                            $nameValue="iValue".$inputsValue;
                                                                            $autoV = "autoV".$inputsValue;
                                                                            $dif = "dif".$inputsValue;  //
                                                                            $nameAut = "idAutoL".$inputsValue;
                                                                        ?>
                                                                         <?php $mm = $mm+1; ?>

                                                                        <style type='text/css'> 
    									#<?php echo $idInputV ?> , #<?php echo $autoV ?>, #<?php echo $dif ?> {
                                                                                text-align:right;
                                                                                font-weight: bold;
    									} 
                                                                        </style>


                                                                        <td><input id="<?php echo $idInputV ?>" name="<?php echo $nameValue ?>" value="" style="width: 100%" type="text" tabindex="<?php echo $mm ?>" onkeyup="formatC('<?php echo $idInputV; ?>')"></td>
                                                                        <td><input id="<?php echo $autoV ?>" name="<?php echo $nameAut ?>" value="0" style="width: 100%; background: #fff9003b !important;" type="text" readonly></td>                                                                                         
                                                                        <td><input id="<?php echo $dif ?>" name="<?php echo $dif ?>" value="0" style="width: 100%;background: #fff9003b !important;" type="text" readonly></td>
                                                                    </tr>
                                                            <?php   
                                                                } 
                                                            ?>
                                                            <input type="hidden" id="BaseInteres" name="BaseInteres" value="" >
                                                            <input type="hidden" id="BaseDesc" name="BaseDesc" value="" >
                                                            <input type="hidden" id="BaseDescAut" name="BaseDescAut" value="" >
                                                            <input type="hidden" id="BaseIntcAut" name="BaseIntcAut" value="" >
                                                            <script>

                                                                //G. Pago valores 
                                                               /* $("#idInputV25").blur(function(){
                                                                    var TTTD = TBGD + TLID;
                                                                    var TTTI = TBGI + TLII;
                                                                    var TTTDA = TLIDA;
                                                                    
                                                                    
                                                                    document.getElementById("BaseDesc").value=TTTD;
                                                                    document.getElementById("BaseInteres").value=TTTI;
                                                                    document.getElementById("BaseDescAut").value=TTTDA;
                                                                    document.getElementById("BaseIntcAut").value=TLIIA;
                                                                });*/

                                                                $("#idInputV26").change(function(){
                                                                    var tp1 = parseFloat($("#idInputV26").val());
                                                                    var tp2 = parseFloat($("#autoV26").val());
                                                                   
                                                                    var dtp = tp1 - tp2;

                                                                    if(dtp < 0){
                                                                        dtp = dtp * -1;
                                                                    } 

                                                                    document.getElementById("dif26").value=formatV(dtp);

                                                                });
                                                            </script>
                                                            <script>
                                                                $("#idInputV27").change(function(){
                                                                    var val1 = ($("#idInputV27").val());
                                                                    var val2 = ($("#autoV27").val());
                                                                    var val3 = ($("#idInputV24").val());
                                                                    var val4 = ($("#idInputV25").val());
                                                                    var val5 = ($("#idInputV26").val());
                                                                    
                                                                    var xval1 = parseFloat(val1.replace(/\,/g, ''));
                                                                    var xval2 = parseFloat(val2.replace(/\,/g, ''));
                                                                    var xval3 = parseFloat(val3.replace(/\,/g, ''));
                                                                    var xval4 = parseFloat(val4.replace(/\,/g, ''));
                                                                    var xval5 = parseFloat(val5.replace(/\,/g, ''));
                                                                                                                                            
                                                                    var dif = xval1 - xval2;
                                                                     if (isNaN(dif)) {
                                                                            return 0;
                                                                     } 

                                                                    
                                                                    //var dif = val1 - tot;
                                                                    //document.write(dif);
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }

                                                                    //document.getElementById("autoV27").value=tot;
                                                                    document.getElementById("dif27").value=formatV(dif);
                                                                    document.getElementById("autoV27").value=xval1;
                                                                });
                                                                $("#idInputV27").change(function(){
                                                                    var val1 = ($("#idInputV27").val());
                                                                    var val2 = ($("#autoV27").val());
                                                                    var xval1 = parseFloat(val1.replace(/\,/g, ''));
                                                                    var xval2 = parseFloat(val2.replace(/\,/g, ''));                  
                                                                    var dif = xval1 - xval2;  
                                                                    if(dif < 0){
                                                                        dif = dif * -1;
                                                                    }
                                                                    document.getElementById("dif27").value=formatV(dif);
                                                                    document.getElementById("autoV27").value=xval1;
                                                                });


                                                                $("#INGM1").blur(function(){
                                                                    var valI15 = ($("#idInputV8").val());
                                                                    var valI16 = ($("#INGM1").val());
                                                                    
                                                                    var xvalI15 = parseFloat(valI15.replace(/\,/g, ''));
                                                                    var xvalI16 = parseFloat(valI16.replace(/\,/g, ''));
                                                                    
                                                                    var dif = xvalI15 - xvalI16;
                                                                    
                                                                    if(dif != 0){
                                                                        $("#difIngre").modal('show');
                                                                    }

                                                                    
                                                                });

                                                                $("#INGM1").change(function(){
                                                                    var valI15 = ($("#idInputV8").val());
                                                                    var valI16 = ($("#INGM1").val());
                                                                    
                                                                    var xvalI15 = parseFloat(valI15.replace(/\,/g, ''));
                                                                    var xvalI16 = parseFloat(valI16.replace(/\,/g, ''));
                                                                    
                                                                    var dif = xvalI15 - xvalI16;

                                                                    if(dif != 0){
                                                                        $("#difIngre").modal('show');
                                                                    }
                                                                    
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
                                    <div class="col-sm-11 col-md-11 col-lg-11  text-right">
                                         <?php $mm = $mm+1; ?>
                                        <button  type="submit" id="btnGuardarDetalle" title="Guardar Declaración" onclick="GuardarDecla()" class="btn btn-primary shadow" tabindex="<?php echo $mm ?>"><li class="glyphicon glyphicon-floppy-disk"></li></button>  
                                        <button  type="submit" id="btnGuardarDetalle" title="Imprimir Declaración" onclick="ImprimirDecla()" class="btn btn-primary shadow" tabindex="<?php echo $mm ?>"><li class="glyphicon glyphicon-print"></li></button>  
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
                            $('form').attr('action', 'jsonComercio/registrarDeclaracionJson.php');
                             $('#form').attr('target','');
                            
                        }

                        function ImprimirDecla(){
                            $('form').attr('action', 'informesComercio/generar_INF_DECLARACION.php');
                            $('#form').attr('target','_BLANK');

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

    <div class="col-sm-8 col-md-8 col-lg-8 client-form1" style="margin-top:-11.3%;margin-right: 10px;margin-left: 1%;padding: 5px 5px 5px 5px;">   
                    
                          

                        </div>


                          <!--listado establecimiento contribuyente-->
                         <div class="col-sm-8 client-form1" style="margin-top:-0.5%">
                              


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
                            <p>Existe una diferencia entre el concepto 15 (Total Ingresos Gravables) y el concepto 16 (Total Ingresos Gravados en el Municipio o Distrito).</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" onClick="foco('idING1');">Aceptar</button>
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
            
            <script src="js/bootstrap.min.js"></script>
            
          
            

            <script>
                function foco(idElemento){
                     document.getElementById(idElemento).focus();
                 };
               
                $("#idInputV28").blur(function(){
                    var VP = ($("#idInputV26").val());
                    var D  = ($("#idInputV27").val());
                    var I  = ($("#idInputV28").val());
                    
                    var xVP = parseFloat(VP.replace(/\,/g, ''));
                    var xD  = parseFloat(D.replace(/\,/g, ''));
                    var xI  = parseFloat(I.replace(/\,/g, ''));

                    var VP1 = ($("#autoV26").val());
                    var D1  = ($("#autoV27").val());
                    var I1  = ($("#autoV28").val());
                    
                    var xVP1 = parseFloat(VP1.replace(/\,/g, ''));
                    var xD1  = parseFloat(D1.replace(/\,/g, ''));
                    var xI1  = parseFloat(I1.replace(/\,/g, ''));
                    if(isNaN(xVP1)){
                        xVP1 = 0;
                    }
                    if(isNaN(xD1)){
                        xD1 = 0;
                    }
                    if(isNaN(xI1)){
                        xI1 = 0;
                    }

                    var NP = (xVP - xD) + xI;

                    var NP1 = (xVP1 - xD1) + xI1;
                    var d = NP - NP1
                      
                    if(d < 0){
                        d = d * -1;
                    }
                    document.getElementById("idInputV29").value=formatV(NP);
                    document.getElementById("autoV29").value=formatV(NP1);
                    document.getElementById("dif29").value=formatV(d);
                
                });

                $("#idInputV29").change(function(){
                    var VP  = ($("#idInputV29").val());
                    var VP1 = ($("#autoV29").val());
                    
                    var xVP = parseFloat(VP.replace(/\,/g, ''));
                    var xVP1 = parseFloat(VP1.replace(/\,/g, ''));

                    var DVP = xVP - xVP1;
                    if(DVP < 0){
                        DVP = DVP * -1;
                    }
                     document.getElementById("dif29").value=formatV(DVP);
                });
                $("#idInputV28").change(function(){
                    var VP2 = ($("#idInputV28").val());
                    var VP3 = ($("#autoV28").val());

                    var xVP2 = parseFloat(VP2.replace(/\,/g, ''));
                    var xVP3 = parseFloat(VP3.replace(/\,/g, ''));

                    var DI = xVP2 - xVP3;
                    if(DI <0 ){
                        DI = DI * -1;
                    }
                     
                    document.getElementById("dif28").value=formatV(DI);
                
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
                    var VDescAu     = $("#BaseDescAut").val();
                    var VIntcAu     = $("#BaseIntcAut").val();

                    var cero = 0;

                    
                     console.log("Base Interes : "+valor);
                     console.log("Base Descuento : "+VDesc);
                     console.log("Base Desc Auto : "+VDescAu);
                     console.log("Base Intes Auto : "+VIntcAu);
                    //var valor       = 99000; 
                    console.log("fecha decla: "+fecD)
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
                                    document.getElementById("idInputV28").value=cero;
                                    document.getElementById("autoV28").value=cero; 
                                    document.getElementById("dif28").value=cero;
                                }else if((result > 0)&&(result < 500)){
                                    result = Math.ceil(result / 1000) * 1000;
                                    document.getElementById("idInputV27").value=cero;
                                    document.getElementById("autoV27").value=cero; 
                                    document.getElementById("dif27").value=cero;

                                    document.getElementById("idInputV28").value=formatV(result);
                                    document.getElementById("autoV28").value=formatV(result); 
                                    document.getElementById("dif28").value=cero; 
                                }else{
                                    result = Math.round(result / 1000) * 1000;
                                    document.getElementById("idInputV27").value=cero;
                                    document.getElementById("autoV27").value=cero; 
                                    document.getElementById("dif27").value=cero;

                                    document.getElementById("idInputV28").value=formatV(result);
                                    document.getElementById("autoV28").value=formatV(result); 
                                    document.getElementById("dif28").value=cero;
                                }
                            }
                              

                        });

                        $.ajax({
                            url: 'funciones/Int_Desc_Industria.php',
                            type: 'POST',                    
                            data: {
                                'bandera':'int',
                                'fechaD': fecD,
                                'valor': VIntcAu ,
                                'fechaVen': FechVI
                           
                            },

                            success: function(data){
                                console.log("valor interes:"+data);
                                result = data.trim();
                                if(result == 0){
                                    //document.getElementById("idInputV28").value=0;
                                    document.getElementById("autoV28").value=cero; 
                                    document.getElementById("dif28").value=cero;
                                }else if((result > 0)&&(result < 500)){
                                    result = Math.ceil(result / 1000) * 1000;
                                    //document.getElementById("idInputV27").value=0;
                                    document.getElementById("autoV27").value=cero; 
                                    document.getElementById("dif27").value=cero;

                                    //document.getElementById("idInputV28").value=result;
                                    document.getElementById("autoV28").value=formatV(result); 
                                    document.getElementById("dif28").value=cero; 
                                }else{
                                    result = Math.round(result / 1000) * 1000;
                                    //document.getElementById("idInputV27").value=0;
                                    document.getElementById("autoV27").value=cero; 
                                    document.getElementById("dif27").value=cero;

                                    //document.getElementById("idInputV28").value=result;
                                    document.getElementById("autoV28").value=formatV(result); 
                                    document.getElementById("dif28").value=cero;
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
                                    console.log("descuento R: "+result);
                                    document.getElementById("idInputV27").value=cero;
                                    //document.getElementById("autoV27").value=0; 
                                    document.getElementById("dif27").value=cero;
                                }else if((result > 0)&&(result < 500)){
                                    result = Math.ceil(result / 1000) * 1000;
                                    document.getElementById("idInputV27").value=formatV(result);
                                    //document.getElementById("autoV27").value=result; 
                                    document.getElementById("dif27").value=cero;

                                    document.getElementById("idInputV28").value=cero;
                                    document.getElementById("autoV28").value=cero; 
                                    document.getElementById("dif28").value=cero; 
                                }else if(result > 500){
                                    result = Math.round(result / 1000) * 1000;
                                    document.getElementById("idInputV27").value=formatV(result);
                                    //document.getElementById("autoV27").value=result; 
                                    document.getElementById("dif27").value=cero;

                                    document.getElementById("idInputV28").value=cero;
                                    document.getElementById("autoV28").value=cero; 
                                    document.getElementById("dif28").value=cero;
                                }else{
                                    console.log("descuento R: "+result);
                                    document.getElementById("idInputV27").value=cero;
                                    document.getElementById("autoV27").value=0; 
                                    document.getElementById("dif27").value=cero;

                                    document.getElementById("idInputV28").value=cero;
                                    document.getElementById("autoV28").value=cero; 
                                    document.getElementById("dif28").value=cero;
                                }
                            }
                        });
                        
                        $.ajax({
                            url: 'funciones/Int_Desc_Industria.php',
                            type: 'POST',                    
                            data: {
                                'bandera':'des',
                                'fechaD': fecD,
                                'valor': VDescAu,
                                'tipo': tipo,
                                'p': AnG
                            },

                            success: function(data){
                                console.log("valor descuento:"+data);
                                result = data.trim();
                                
                                if(result == 0){
                                    console.log("descuento R1: "+result);
                                    //document.getElementById("idInputV27").value=0;
                                    document.getElementById("autoV27").value=cero; 
                                    document.getElementById("dif27").value=cero;
                                }else if((result > 0)&&(result < 500)){
                                    console.log("descuento R2: "+result);
                                    result = Math.ceil(result / 1000) * 1000;
                                    //document.getElementById("idInputV27").value=result;
                                    document.getElementById("autoV27").value=formatV(result); 
                                    document.getElementById("dif27").value=cero;

                                    document.getElementById("idInputV28").value=cero;
                                    document.getElementById("autoV28").value=cero; 
                                    document.getElementById("dif28").value=cero; 
                                }else if(result > 500){
                                    console.log("descuento R3: "+result);
                                    result = Math.round(result / 1000) * 1000;
                                    //document.getElementById("idInputV27").value=result;
                                    document.getElementById("autoV27").value=formatV(result); 
                                    document.getElementById("dif27").value=cero;

                                    document.getElementById("idInputV28").value=cero;
                                    document.getElementById("autoV28").value=cero; 
                                    document.getElementById("dif28").value=cero;
                                }else{
                                    console.log("descuento R4: "+result);
                                    //document.getElementById("idInputV27").value=0;
                                    document.getElementById("autoV27").value=cero; 
                                    document.getElementById("dif27").value=cero;

                                    document.getElementById("idInputV28").value=cero;
                                    document.getElementById("autoV28").value=cero; 
                                    document.getElementById("dif28").value=cero;
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

            <script type="text/javascript" src="js/select2.js"></script>
            <script type="text/javascript"> 
                $("#sltctai").select2();
                $("#sltVig").select2();
                $("#sltNumI").select2();
            </script>
        </body>
    </html>

    |