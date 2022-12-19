<?php
session_start();
//Change One
require_once './Conexion/conexion.php';?>
<html lang="en">
    <head>
        <link rel="icon" type="image/ico" href="AAA.ico" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta class="viewport" content="width=device-width, initial-scale=1.0, minimun-scalable=1.0"></meta>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" title="default" />
        <script src="js/jquery.min.js"></script>
        <script type="text/javascript" language="javascript" src="js/jquery-1.10.2.js"></script>         
        <link rel="stylesheet" href="css/select/select2.min.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link href="skins/page.css" rel="stylesheet" />
        <link href="skins/blue/accordion-menu.css" rel="stylesheet" />
        <script src="js/accordion-menu.js"></script>
        <link rel="stylesheet" type="text/css" href="css/custom.css">
        <script type="text/javascript" src="js/txtValida.js"></script>
        <script src="js/jquery.min.js"></script>
            <script src="js/jquery-ui.js" type="text/javascript"></script>
        
        <style>
            .navbar {
              margin-bottom: 0;
              border-radius: 0;
            }    
            .row.content {height: 510px}    
            .sidenav {
              padding-top: 20px;
              background-color: #f1f1f1;
              height: 100%;
            }    
            footer {
              background-color: #555;
              color: white;
              padding: 15px;
            }    
            @media screen and (max-width: 767px) {
              .sidenav {
                height: auto;
                padding: 15px;
              }
              .row.content {height:auto;}
            }
        </style>
        <div> 
            <img src="RECURSOS/TOP/Fondo---Top.png">
            <div align="right" style="margin-top:-86px">
                <img src="RECURSOS/TOP/Caja---Cliente.png">
            </div>
            <div align="left" style="margin-top:-87px">
                <img src="RECURSOS/TOP/Caja---Logo.png">
                <img style="margin-left:-200px" src="RECURSOS/TOP/Logos-Sigiep---Blanco.png">
            </div>  
        </div>
        
        <style>
            ul li{margin:10px 0;}
        </style>
        <style>
            .login {
                max-width: 330px;
                padding: 15px;
                margin: 0 auto;
            }
            #sha{
                max-width: 340px;
                -webkit-box-shadow: 0px 0px 18px 0px rgba(48, 50, 50, 0.48);
                -moz-box-shadow:    0px 0px 18px 0px rgba(48, 50, 50, 0.48);
                box-shadow:         0px 0px 18px 0px rgba(48, 50, 50, 0.48);
                border-radius: 6%;
            }
        </style>	
        <title>Inicio de sesion</title>
    </head>
    <body style="font-size: 14px">
        <div class="container well" id="sha" style="margin-top:50px;margin-bottom:70px">
            <form class="login" id="form" name="form" action="javaScript:validar()"  method="POST">
                <h3  align="center" style="margin-bottom: 15px; margin-right: 4px; margin-left: 4px;margin-top:-15px">Inicio de Sesión</h3>
                <div class="form-group" style="margin-top: -10px">
                    <label class="control-label">
                        <strong class="obligado">*</strong>N° Identificación / Nombre:
                    </label>
                    <input type="text" class="form-control" name="txtIdentificacion" id="txtIdentificacion" placeholder="N° de Identificación ó Nombre" onkeypress="return txtValida(event,'num_car')" required title="Ingrese N° de Identificación ó Nombre" autofocus autocomplete="off">
                </div>
                
                <script>
                    $("#txtIdentificacion").keyup(function () {
                        $("#txtIdentificacion").autocomplete({
                            source: "jsonSistema/consultas.php?case=7",
                            minlength: 1,
                            select: function (event, ui) {
                                var tercer = ui.item;
                                var ref = tercer.value;
                                var form_data = {
                                    case: 4,
                                    referencia: ref,
                                };
                            },
                        });
                    });
                    $("#txtIdentificacion").change(function(){
                        if($("#txtIdentificacion").val()!=""){
                            $("#divca").css("display", "block");
                            var opcion ="";
                            var tercero =$("#txtIdentificacion").val();
                            var form_data = { case: 6, tercero : tercero};
                            $.ajax({
                              type: "POST",
                              url: "jsonSistema/consultas.php",
                              data: form_data,
                              success: function(response)
                              { 
                                console.log(response);
                                opcion +=response;
                                $("#sltTercero").html(response).focus();
                                $("#sltTercero").select2({
                                    allowClear: true
                                });
                                var opcion ="";
                                var ter =$("#sltTercero").val();
                                var form_data = { case: 5, tercero : ter};
                                $.ajax({
                                  type: "POST",
                                  url: "jsonSistema/consultas.php",
                                  data: form_data,
                                  success: function(response)
                                  { 
                                    console.log(response);
                                    opcion +=response;
                                    $("#sltAnno").html(response).focus();
                                    $("#sltAnno").select2({
                                        allowClear: true
                                    });
                                    
                                  }
                                });
                              }
                            }); 
                        }
                    })
            </script>
                 <div id="divca" class="form-group" >
                <div class="form-group" style="margin-top: -10px">
                    <label class="control-label">
                        <strong class="obligado">*</strong>Compañia:
                    </label>                    
                    <select class="form-control" id="sltTercero" name="sltTercero" title="Seleccione compañia" style="height: 38px">
                        <option value="">Compañia</option>                      
                    </select>
                </div>
                <script>
                    $("#sltTercero").change(function(){
                        var opcion ="";
                        var ter =$("#sltTercero").val();
                        var form_data = { case: 5, tercero : ter};
                        $.ajax({
                          type: "POST",
                          url: "jsonSistema/consultas.php",
                          data: form_data,
                          success: function(response)
                          { 
                            console.log(response);
                            opcion +=response;
                            $("#sltAnno").html(opcion).focus();
                            $("#sltAnno").select2({
                                allowClear: true
                            });

                          }
                        }); 
                    })
                </script>
                <div class="form-group" style="margin-top: -10px">
                    <label class="control-label">
                        <strong class="obligado">*</strong>Año:
                    </label>                    
                    <select class="form-control" id="sltAnno" name="sltAnno" title="Seleccione año" style="height: 38px">
                        <option value="">Año</option>
                    </select>
                </div>
                </div>
                <div class="form-group" style="margin-top: -10px">
                    <label class="control-label">
                        <strong class="obligado">*</strong>Usuario:
                    </label>
                    <input type="text" class="form-control" name="txtUsuario" id="txtUsuario" placeholder="Usuario" onkeypress="return txtValida(event,'num_car')" required title="Ingrese usuario" autofocus>
                </div>                
                <div class="form-group" style="margin-top: -10px">
                    <label class="control-label">
                        <strong class="obligado">*</strong>Contraseña:
                    </label>
                    <input type="password" class="form-control" name="txtPass" id="txtPass" placeholder="Contraseña" required title="Ingrese contraseña">
                </div>
               
                <button class="btn btn-lg btn-primary btn-block" type="submit">Iniciar Sesión</button>				
	    </form>
	</div> 	
	<script src="js/bootstrap.js"></script>        
        <div class="modal fade" id="error" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Verifique su usuario y/o contraseña</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnAe" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            function validar(){
                if( $("#sltTercero").val()=="" || $("#sltAnno").val()=="" || $("#txtIdentificacion").val()==""){
                    $("#datos").modal('show');
                } else {
                    var formData = new FormData($("#form")[0]);  
                    var form_data = { action:1 };
                    $.ajax({
                        type: 'POST',
                        url: "login.php",
                        data:formData,
                        contentType: false,
                        processData: false,
                        success: function(response)
                        { 
                            console.log(response);
                            if(response==1){
                                $("#error").modal("show");
                                $("#btnAe").click(function(){
                                    $("#error").modal("hide");
                                })
                            } else {
                                if(response==3){
                                    document.location = 'Conexion/error2.php';
                                } else { 
                                    document.location = 'index2.php?t=1';
                                }
                            }
                        }
                    })
                }
            }
        </script>
        <div class="footer">
            <?php require_once 'footer.php'; ?>
        </div>   
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/select/select2.full.js"></script>
        <div class="modal fade" id="datos" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Datos Incompletos</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>