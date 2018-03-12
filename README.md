# API_SEPOMEX_PHP
Un API escrito en PHP para obtener información de determinado código postal
de SEPOMEX. El API cuenta con un script para poder actualizarlo.

Para hacer funcionar la aplicación, se puede clonar el GIT en la raíz del servidor local.

$ git clone https://github.com/BrayanIribe/API_SEPOMEX_PHP

Después, hay que hacer el siguiente procedimiento:

1 - Ejecutar el archivo CP.SQL en MySQL (es decir, importarlo y crear la respectiva base de datos).

2 - Ejecutar el archivo UPDATE.PHP (para almacenar toda la información de CP).

Si queremos obtener información de determinado CP podemos acceder
al siguiente URL, esto es si tenemos el .HTACCESS bien configurado:

http://localhost:82/40000

Si no utilizaremos el .HTACCESS, podemos hacerlo de la siguiente manera:

http://localhost:82/index.php?cp=40000

El resultado (correcto) sería un JSON de la siguiente forma:

{

  "colonias":["IGUALA DE LA INDEPENDENCIA CENTRO"],
  
  "municipio":"IGUALA DE LA INDEPENDENCIA",
  
  "ciudad":"IGUALA DE LA INDEPENDENCIA",
  
  "estado":"GUERRERO",
  
  "cp":"40000"
  
}

Si se desea actualizar la base de datos, podemos ejecutar el fichero
update.php. Si se devuelve el valor "1" quiere decir que fue un éxito.
Cabe mencionar, que es necesario contar con conexión a Internet para
realizar el proceso.
