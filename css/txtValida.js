/*#########MODIFICACIONES#############
 * 13/03/2017 |  FUNCION VALIDARDES, PARA DESCRIPCIONES
 ####################################*/
function txtValida(elEvento, permitidos, id, n) //Evento event, tipo de caracteres permitidos, id del input a validar (en caso de ser 'dec'), núemro de decimales permitidos.
  {

    
    //Variables para validación
    var numeros = "0123456789"; 
    var caracteres = " abcdefghijklmnopqrstuvwxyzñABCDEFGHIJKLMNOPQRSTUVWXYZÑáéíóú�?É�?ÓÚ";//Caracteres perm,itidos 
    var caracteres_sinespacio = "abcdefghijklmnopqrstuvwxyzñABCDEFGHIJKLMNOPQRSTUVWXYZÑáéíóú�?É�?ÓÚ";//Caracteres sin espacio
    var caracteres_sin_tilde = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; //Nuevo Ferney
    var numeros_caracteres = numeros + caracteres; 
    var conP= '0123456789.-';
    var teclas_especiales_numeros = [8, 20]; //Ascii retroseso y espacio
    var teclas_especiales_letras = [8, 20, 239, 127, 9, 165];
    var tecla_especial_dir =[8, 20, 239, 127, 9, 165, 35, 45, 40, 41, 46];//Nuevo Erica Dirección (#-().)
    var punto_decimal = [46]; //Nuevo Ferney.
    var puntos_comas = [44, 45, 46, 47, 43]; 
    var sin_nada = []; 
    var num = 0;
    var punto = 0; //Nuevo Ferney
    var permt = 0; //Nuevo Ferney
    var raya_baja = [95]; //Nuevo Ferney
      
      //Switch en el que se valida que tipo de datos es permitido
      switch(permitidos)
      {
        //Si el caso es numerico que permita el ingreso
        case 'num':
          permitidos = numeros;
          num = 1;
          break;
        //Si el caso es decimal
        case 'dec': //Nuevo Ferney
          permitidos = numeros;
          num = 2;
          break;  
        //Si el caso es caracteres
        case 'car':
          permitidos = caracteres;
          break;
        //Si el caso es numeros y caracyeres
        case 'num_car':
          permitidos = numeros_caracteres;
          break;
        case 'sin_espcio': //Nuevo Ferney
          permitidos = numeros_caracteres;
          num = 3;
          break; 
        case 'todas': //Nuevo Ferney
          permitidos = numeros_caracteres;
          break; 
        case 'direccion': //Nuevo Erica
          permitidos = numeros_caracteres;
           num = 4;
          break; 
        case 'car_sin': //Nuevo Erica, Caracteres sin espacio
          permitidos = caracteres_sinespacio;
          num = 5;
          break; 
         case 'decimales': //Numeros con .
          permitidos = conP;
          break; 
         case 'car_raya_baja': //Numeros con .
          permitidos = raya_baja + caracteres_sin_tilde;
          num = 6;
          break;
      }
      
      //Toma el evento de la ventana
      var evento = elEvento || window.event;
      //Toma el caracter en el evento 
      var codigoCaracter = evento.charCode || evento.keyCode;
      //Convierte a String el caracter enviado
      var caracter = String.fromCharCode(codigoCaracter);
      
      var tecla_especial = false;

      //Validaciones obligatorias para todos los input.
      tecla = (document.all) ? elEvento.keyCode : elEvento.which;
      if (tecla == 8 || tecla == 0) // Permite retoseso (8) y teclas especiales.
        return true;//tecla_especial = true;//
      else if (tecla == 39) // Impide la comilla
        return false;//tecla_especial = false; //
      else if (tecla == 32 && num == 3)
        return false;
     

      //Validación para ingreso de valores con letras.
      if(num == 0)
      {
        for(var i in teclas_especiales_letras)
        {
          if(codigoCaracter == teclas_especiales_letras[i])
          {
            tecla_especial = true;
            break;
          }
        }
      }
      //Validación para ingreso de valores númericos
      else if(num == 1)
      {
        for(var i in teclas_especiales_numeros)
        {
          if(codigoCaracter == teclas_especiales_numeros[i])
          {
            tecla_especial = true;
            break;
          }
        }
      }
      //Validación para el ingreso de decimales en la que se puedde dar 
      //la contidad de decimales despues del punto por ejemplo 16,2
      else if(num==2) //Nuevo Ferney
      {
        var valor = document.getElementById(id).value;
        var ultimo = document.getElementById(id).value.length;
        punto = valor.indexOf('.'); //poscición del punto.

        if(punto == -1)
        {
          for(var i in punto_decimal)
          {
            if(codigoCaracter == punto_decimal[i])
            {
              tecla_especial = true;
              break;
            }
          }
        }
        else
        {
          if((ultimo - punto) > n)
            permt = 1;
        }

      }  
      else if(num == 3)
      {
        for(var j in puntos_comas)
        {
          if(codigoCaracter == puntos_comas[j])
          {
            tecla_especial = true;
            break;
          }
        }  
      }
      else if(num == 4)
      {
        for(var i in tecla_especial_dir )
        {
          if(codigoCaracter == tecla_especial_dir[i])
          {
            tecla_especial = true;
            break;
          }
        }  
      }
      else if(num == 5)
      {
        for(var x in sin_nada)
        {
          if(codigoCaracter == sin_nada[x])
          {
            tecla_especial = true;
            break;
          }
        }  
      }
       else if(num == 6)
      {
        for(var i in raya_baja )
        {
          if(codigoCaracter == raya_baja[i])
          {
            tecla_especial = true;
            break;
          }
        }  
      }

      //Validación para ingreso de caracteres
      if(permt == 0)
        return permitidos.indexOf(caracter) != -1 || tecla_especial;
      else
        return false; 


  }

  function format(id)
{
  var numero = document.getElementById(id).value;
  numero = numero.replace(/\./g,'');
  numero = numero.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
  numero = numero.split('').reverse().join('').replace(/^[\.]/,'');
  document.getElementById(id).value = numero;
}


function formatC(id) 
{
  var numero = document.getElementById(id).value;
  numero = numero.replace(/\,/g,'');
  numero = numero.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
  numero = numero.split('').reverse().join('').replace(/^[\,]/,'');
  document.getElementById(id).value = numero;
}
/*PERMITE INGRESO DE TODOS LOS CARACTERES MENOS (' ")*/
var validarDes = function (event){
    event = event || window.event;
    var charCode = event.keyCode || event.which;
    if(charCode ==39 || charCode ==34){
        return false;
    } else {
        return true;
    }
}


function formatV(value) 
{
  var numero = value;
  //numero = numero.replace(/\,/g,'');
  numero = numero.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
  numero = numero.split('').reverse().join('').replace(/^[\,]/,'');
  return numero;
}